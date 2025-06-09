<?php
$conn = new mysqli("localhost", "root", "", "gestionabsence");
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

$message = "";
$diagnostic = [];
$etudiants = [];
$id_grp = null;
$id_seance = null;
$date = date("Y-m-d");
$type = 'TD';

// الحصول على id_seance من GET أو POST
if (isset($_GET["id_seance"])) {
    $id_seance = intval($_GET["id_seance"]);
} elseif (isset($_POST["id_seance"])) {
    $id_seance = intval($_POST["id_seance"]);
}

// الحصول على التاريخ من POST أو استخدام تاريخ اليوم
if (isset($_POST["date"])) {
    $date = $_POST["date"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["save_presence"])) {
    $id_grp = isset($_POST["id_grp"]) ? intval($_POST["id_grp"]) : null;


    // جلب بيانات الحصة من emploi باستخدام Id_Emp = id_seance
    $stmt_info = $conn->prepare("SELECT Id_Grp, nom_salle, Id_Mdl, Id_Ens, Num_Rep FROM emploi WHERE Id_Emp = ?");
    $stmt_info->bind_param("i", $id_seance);
    $stmt_info->execute();
    $info = $stmt_info->get_result()->fetch_assoc();

    if ($info) {
        $id_grp = $info["Id_Grp"];

        // التحقق من وجود الحصة بالفعل في جدول seance حسب التاريخ والفوج ونوع الحصة وNum_Rep
        $stmt_check = $conn->prepare("SELECT Id_Seance FROM seance WHERE Date = ? AND Id_Grp = ? AND Type = ? AND Num_Rep = ?");
        $stmt_check->bind_param("sisi", $date, $id_grp, $type, $info["Num_Rep"]);
        $stmt_check->execute();
        $res_check = $stmt_check->get_result();

        if ($res_check->num_rows > 0) {
            $row = $res_check->fetch_assoc();
            $id_seance = $row["Id_Seance"];
            $diagnostic[] = "🟢 الحصة موجودة مسبقاً (Id_Seance = $id_seance).";
        } else {
            // إنشاء الحصة في جدول seance
            $stmt_insert = $conn->prepare("
                INSERT INTO seance (Date, Type, nom_salle, Statut, Id_Grp, Id_Mdl, Num_Rep, Id_Ens)
                VALUES (?, ?, ?, 'Effectuee', ?, ?, ?, ?)
            ");
            $stmt_insert->bind_param(
                "sssissi",
                $date,
                $type,
                $info["nom_salle"],
                $id_grp,
                $info["Id_Mdl"],
                $info["Num_Rep"],
                $info["Id_Ens"]
            );
            
            if ($stmt_insert->execute()) {
                $id_seance = $conn->insert_id;
                $diagnostic[] = "✅ تم إنشاء الحصة تلقائيًا (Id_Seance = $id_seance).";
            } else {
                $message = "❌ خطأ في إدخال الحصة: " . $stmt_insert->error;
            }
        }

        // إذا تم التأكد من Id_Seance
        if ($id_seance && empty($message)) {
            // حذف الغيابات السابقة
            $stmt_del = $conn->prepare("DELETE FROM absence WHERE Id_Seance = ? AND Date = ?");
            $stmt_del->bind_param("is", $id_seance, $date);
            $stmt_del->execute();

            // جلب الطلبة في الفوج
            $stmt_etud = $conn->prepare("
                SELECT e.matricule, e.Nom_Etud, e.Prenom_Etud, eu.Id_Univ
                FROM etudiant e 
                JOIN etud_univ eu ON e.matricule = eu.matricule 
                WHERE eu.Id_Grp = ?
                ORDER BY e.Nom_Etud, e.Prenom_Etud
            ");
            $stmt_etud->bind_param("i", $id_grp);
            $stmt_etud->execute();
            $etudiants = $stmt_etud->get_result()->fetch_all(MYSQLI_ASSOC);

            // تسجيل الغياب
            foreach ($etudiants as $etud) {
                $matricule = $etud["matricule"];
                $id_univ = $etud["Id_Univ"];

                if (isset($_POST["presence"][$matricule]) && $_POST["presence"][$matricule] == "1") {
                    $presence = 'non_justifiee';
                    $type_abs = 'abs_seance_pedagogique';
                    $cause_abs = '';

                    $stmt_abs = $conn->prepare("INSERT INTO absence (Date, status_Abs, type_Abs, Id_Seance, Id_Univ, Cause_abs) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt_abs->bind_param("sssiss", $date, $presence, $type_abs, $id_seance, $id_univ, $cause_abs);
                    if ($stmt_abs->execute()) {
                        $diagnostic[] = "✅ تم تسجيل غياب الطالب ($id_univ)";
                    } else {
                        $diagnostic[] = "❌ خطأ في تسجيل الطالب $id_univ: " . $stmt_abs->error;
                    }
                } else {
                    $diagnostic[] = "🟢 الطالب ($id_univ) حاضر.";
                }
            }

            $message = "✅ تم حفظ الحضور بنجاح.";
        }
    } else {
        $message = "❌ لم يتم العثور على بيانات الحصة في جدول emploi.";
    }
}

// تحميل الطلبة عند الدخول أول مرة
if ($_SERVER["REQUEST_METHOD"] != "POST" && $id_seance) {
    $stmt_grp = $conn->prepare("SELECT Id_Grp FROM emploi WHERE Id_Emp = ?");
    $stmt_grp->bind_param("i", $id_seance);
    $stmt_grp->execute();
    $res_grp = $stmt_grp->get_result()->fetch_assoc();
    if ($res_grp) {
        $id_grp = $res_grp["Id_Grp"];
        $stmt_etud = $conn->prepare("
            SELECT e.matricule, e.Nom_Etud, e.Prenom_Etud, eu.Id_Univ
            FROM etudiant e 
            JOIN etud_univ eu ON e.matricule = eu.matricule 
            WHERE eu.Id_Grp = ?
            ORDER BY e.Nom_Etud, e.Prenom_Etud
        ");
        $stmt_etud->bind_param("i", $id_grp);
        $stmt_etud->execute();
        $etudiants = $stmt_etud->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <title>تسجيل الحضور</title>
    <style>
        body { font-family: 'Tajawal', sans-serif; background: #f4fdfd; padding: 20px; }
        h2 { color: #00695c; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background: #b2dfdb; }
        button { padding: 8px 16px; background-color: #00695c; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .message { color: green; font-weight: bold; }
        label { font-weight: bold; }
        input[type="date"], input[type="number"] { padding: 6px; margin: 4px 0 10px 0; width: 150px; }
        .diagnostic { margin-top: 20px; padding: 10px; background: #e0f7fa; border-radius: 5px; font-family: monospace; white-space: pre-line; }
    </style>
</head>
<body>

<h2>📋 تسجيل الحضور للحصة رقم <?php echo htmlspecialchars($id_seance); ?></h2>

<?php if (!empty($message)): ?>
    <p class="message"><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<form method="POST">
    <label for="id_seance">رقم الحصة (id_seance):</label><br>
    <input type="number" id="id_seance" name="id_seance" value="<?php echo htmlspecialchars($id_seance); ?>" required><br>

    <label for="date">تاريخ الحضور:</label><br>
    <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>" required><br>

    <?php if (!empty($etudiants)): ?>
        <input type="hidden" name="id_grp" value="<?php echo $id_grp; ?>">
        <table>
            <tr>
                <th>الرقم الجامعي</th>
                <th>اللقب</th>
                <th>الاسم</th>
                <th>غائب؟</th>
            </tr>
            <?php foreach ($etudiants as $etud): ?>
                <?php
                    $checked = "";
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $checked = (isset($_POST["presence"][$etud["matricule"]]) && $_POST["presence"][$etud["matricule"]] == "1") ? "checked" : "";
                    }
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($etud["matricule"]); ?></td>
                    <td><?php echo htmlspecialchars($etud["Nom_Etud"]); ?></td>
                    <td><?php echo htmlspecialchars($etud["Prenom_Etud"]); ?></td>
                    <td>
                        <input type="checkbox" name="presence[<?php echo $etud["matricule"]; ?>]" value="1" <?php echo $checked; ?>>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <button type="submit" name="save_presence">حفظ الغياب</button>
    <?php else: ?>
        <p>يرجى إدخال رقم الحصة أعلاه وتحميل الطلبة.</p>
    <?php endif; ?>
</form>

<?php if (!empty($diagnostic)): ?>
    <div class="diagnostic">
        <?php echo implode("\n", $diagnostic); ?>
    </div>
<?php endif; ?>

</body>
</html>

