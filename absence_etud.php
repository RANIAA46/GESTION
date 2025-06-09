<?php
session_start();

if (!isset($_SESSION['matricule'])) {
    die("يرجى تسجيل الدخول أولاً.");
}

$matricule = $_SESSION['matricule'];

$conn = new mysqli("localhost", "root", "", "gestionabsence");
$conn->set_charset("utf8");

$sql = "
    SELECT 
        m.Disigne AS Nom_Mat, 
        a.Date,
        r.Heure_Deb,
        r.Heure_Fin,
        s.nom_salle,
        a.type_Abs,
        a.status_Abs,
        a.Cause_abs
    FROM absence a
    JOIN etud_univ eu ON a.Id_Univ = eu.Id_Univ
    JOIN seance s ON a.Id_Seance = s.Id_Seance
    JOIN module m ON s.Id_Mdl = m.Id_Mdl
    JOIN repartition r ON s.Num_Rep = r.Num_Rep
    WHERE eu.matricule = ?
    ORDER BY a.Date DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $matricule);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>قائمة الغيابات</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f2f2f2;
            padding: 20px;
            direction: rtl;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        table {
            border-collapse: collapse;
            margin: auto;
            width: 90%;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #004080;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #eef;
        }
    </style>
</head>
<body>

<h2>قائمة الغيابات الخاصة بك</h2>

<table>
    <tr>
        <th>المادة</th>
        <th>تاريخ الغياب</th>
        <th>الفترة الزمنية</th>
        <th>القاعة</th>
        <th>نوع الغياب</th>
        <th>الحالة</th>
        <th>السبب</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?= htmlspecialchars($row['Nom_Mat']) ?></td>
        <td><?= htmlspecialchars($row['Date']) ?></td>
        <td><?= htmlspecialchars($row['Heure_Deb']) ?> - <?= htmlspecialchars($row['Heure_Fin']) ?></td>
        <td><?= htmlspecialchars($row['nom_salle']) ?></td>
        <td><?= htmlspecialchars($row['type_Abs']) ?></td>
        <td><?= htmlspecialchars($row['status_Abs']) ?></td>
        <td><?= htmlspecialchars($row['Cause_abs']) ?></td>
    </tr>
    <?php } ?>

</table>

</body>
</html>

<?php
$conn->close();
?>
