<?php
$conn = new mysqli("localhost", "root", "", "gestionabsence");
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

$etudiants = [];
$distribution = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nb_sections = intval($_POST["nb_sections"]);
    $nb_groupes_per_section = intval($_POST["nb_groupes"]);
    $total_groupes = $nb_sections * $nb_groupes_per_section;

    // جلب الطلبة مرتبين أبجدياً
    $result = $conn->query("SELECT * FROM etudiant ORDER BY Nom_Etud ASC, Prenom_Etud ASC");

    while ($row = $result->fetch_assoc()) {
        $etudiants[] = $row;
    }

    $total_etudiants = count($etudiants);

    // حساب عدد الطلبة في كل فوج
    $base_group_size = floor($total_etudiants / $total_groupes);
    $remaining = $total_etudiants % $total_groupes;

    $group_sizes = [];
    for ($i = 0; $i < $total_groupes; $i++) {
        $group_sizes[$i] = $base_group_size + ($i < $remaining ? 1 : 0);
    }

    // توزيع الطلبة
    $index = 0;
    for ($groupe_number = 1; $groupe_number <= $total_groupes; $groupe_number++) {
        $section_number = floor(($groupe_number - 1) / $nb_groupes_per_section) + 1;
        $size = $group_sizes[$groupe_number - 1];

        for ($j = 0; $j < $size && $index < $total_etudiants; $j++, $index++) {
            $etudiants[$index]["Groupe"] = $groupe_number;
            $etudiants[$index]["Section"] = $section_number;
            $distribution[] = $etudiants[$index];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>توزيع الطلبة</title>
    <style>
        body { font-family: 'Tajawal', sans-serif; background: #f0fdfc; padding: 20px; }
        h2 { color: #00796b; }
        form { margin-bottom: 20px; }
        label { font-weight: bold; margin-left: 10px; }
        input[type=number] { padding: 6px; width: 80px; margin: 5px; }
        button { padding: 8px 16px; background-color: #00796b; color: white; border: none; border-radius: 5px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background: #b2dfdb; }
    </style>
</head>
<body>

    <h2>📊 توزيع الطلبة حسب الأقسام والأفواج</h2>

    <form method="POST">
        <label>عدد الأقسام:</label>
        <input type="number" name="nb_sections" required>

        <label>عدد الأفواج في كل قسم:</label>
        <input type="number" name="nb_groupes" required>

        <button type="submit">🔄 توزيع</button>
    </form>

    <?php if (!empty($distribution)): ?>
        <table>
            <tr>
                <th>الرقم الجامعي</th>
                <th>اللقب</th>
                <th>الاسم</th>
                <th>القسم</th>
                <th>الفوج</th>
            </tr>
            <?php foreach ($distribution as $etud): ?>
                <tr>
                    <td><?= htmlspecialchars($etud["matricule"]) ?></td>
                    <td><?= htmlspecialchars($etud["Nom_Etud"]) ?></td>
                    <td><?= htmlspecialchars($etud["Prenom_Etud"]) ?></td>
                    <td><?= $etud["Section"] ?></td>
                    <td><?= $etud["Groupe"] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

</body>
</html>

