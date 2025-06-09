<?php
session_start();

$conn = new mysqli("localhost", "root", "", "gestionabsence");
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}
$conn->set_charset("utf8");

// حساب عدد الغيابات غير المبررة لكل طالب
$absence_counts = [];
$sql_count = "
    SELECT Id_Univ, COUNT(*) AS total_abs
    FROM absence
    WHERE (fichier_justificatif IS NULL OR commentaire = 'justification refuser')
    GROUP BY Id_Univ
";
$res_count = $conn->query($sql_count);
while ($row = $res_count->fetch_assoc()) {
    $absence_counts[$row['Id_Univ']] = $row['total_abs'];
}

// جلب بيانات الغيابات
$sql = "
    SELECT 
        a.Id_Abs,
        a.Date,
        a.status_Abs,
        a.type_Abs,
        a.Id_Seance,
        a.Id_Univ,
        a.Cause_abs,
        a.fichier_justificatif,
        a.commentaire,
        e.matricule,
        e.Nom_Etud,
        e.Prenom_Etud
    FROM absence a
    JOIN etud_univ eu ON a.Id_Univ = eu.Id_Univ
    JOIN etudiant e ON eu.matricule = e.matricule
    ORDER BY a.Date DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8" />
    <title>تقرير الغياب</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            direction: rtl;
            padding: 20px;
            background-color: #e0f7f4;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 25px;
            background-color: #fff;
            box-shadow: 0 0 10px #ccc;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 12px 10px;
            text-align: center;
            font-size: 15px;
        }
        th {
            background-color: #00c9bd;
            color: white;
        }
        .back-link {
            margin-top: 30px;
            display: inline-block;
            text-decoration: none;
            background-color: #00a3a3;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 16px;
        }
        h2 {
            text-align: center;
            color: #055c60;
            font-size: 42px;
        }
        select {
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #999;
            font-size: 14px;
            cursor: pointer;
            background-color: #f9f9f9;
        }
    </style>
    <script>
        function autoSubmit(select) {
            select.form.submit();
        }
    </script>
</head>
<body>

<h2>تقرير الغياب</h2>

<?php
if ($result && $result->num_rows > 0) {
    echo "<table>";
    echo "<tr>
            <th>رقم الغياب</th>
            <th>الاسم</th>
            <th>اللقب</th>
            <th>رقم الطالب</th>
            <th>تاريخ الغياب</th>
            <th>رقم الحصة</th>
            <th>سبب الغياب</th>
            <th>نوع الغياب</th>
            <th>الحالة</th>
            <th>مبرر الغياب</th>
            <th>القرار</th>
            <th>عدد الغيابات</th>
            <th>نوع التنبيه</th>
          </tr>";

    while($row = $result->fetch_assoc()) {
        $id_univ = $row['Id_Univ'];
        $nb_abs = isset($absence_counts[$id_univ]) ? $absence_counts[$id_univ] : 0;

        // حساب نوع التنبيه
        if ($nb_abs >= 6) $avert = "إقصاء من الوحدة";
        elseif ($nb_abs >= 5) $avert = "تنبيه ثاني";
        elseif ($nb_abs >= 3) $avert = "تنبيه أول";
        else $avert = "—";

        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Id_Abs']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Nom_Etud']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Prenom_Etud']) . "</td>";
        echo "<td>" . htmlspecialchars($row['matricule']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Id_Seance']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Cause_abs']) . "</td>";
        echo "<td>" . htmlspecialchars($row['type_Abs']) . "</td>";
        echo "<td>" . htmlspecialchars($row['status_Abs']) . "</td>";

        // مبرر الغياب
        if (!empty($row['fichier_justificatif'])) {
            echo "<td><a href='" . htmlspecialchars($row['fichier_justificatif']) . "' target='_blank'>عرض</a></td>";
        } else {
            echo "<td>—</td>";
        }

        // تعليق رئيس القسم
        echo "<td>";
        echo "<form method='POST' action='save_decision.php'>";
        echo "<input type='hidden' name='id_abs' value='" . $row['Id_Abs'] . "' />";
        echo "<select name='commentaire' onchange='autoSubmit(this)'>
                <option value=''>—</option>
                <option value='justification accepter'" . ($row['commentaire'] == 'justification accepter' ? ' selected' : '') . ">justification accepter</option>
                <option value='justification refuser'" . ($row['commentaire'] == 'justification refuser' ? ' selected' : '') . ">justification refuser</option>
              </select>";
        echo "</form>";
        echo "</td>";

        // عدد الغيابات والتنبيه
        echo "<td>" . $nb_abs . "</td>";
        echo "<td>" . $avert . "</td>";

        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p class='no-data'>لا توجد بيانات غياب لعرضها.</p>";
}
?>

<a class="back-link" href="chefDepartement.html">العودة إلى لوحة التحكم</a>

</body>
</html>

<?php
$conn->close();
?>
