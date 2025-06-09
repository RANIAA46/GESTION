<?php 
header('Content-Type: text/html; charset=utf-8');
session_start();

$conn = new mysqli("localhost", "root", "", "gestionabsence");
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

if (!isset($_SESSION['matricule'])) {
    echo "الرجاء تسجيل الدخول أولاً.";
    exit();
}

$matricule = $_SESSION['matricule'];

$sql = "
SELECT 
    a.Id_Abs,
    m.Disigne AS module, 
    a.Date AS date_absence, 
    a.status_Abs, 
    a.type_Abs AS type, 
    r.Heure_Deb, 
    r.Heure_Fin, 
    s.nom_salle, 
    a.Cause_abs 
FROM etud_univ eu 
JOIN absence a ON a.Id_Univ = eu.Id_Univ 
JOIN seance sc ON sc.Id_Seance = a.Id_Seance 
JOIN module m ON m.Id_Mdl = sc.Id_Mdl 
JOIN repartition r ON r.Num_Rep = sc.Num_Rep 
JOIN salle s ON s.nom_salle = sc.nom_salle 
WHERE eu.matricule = '$matricule' AND a.status_Abs = 'non_justifiee' 
ORDER BY a.Date DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>قائمة الغيابات غير المبررة</title>
    <style>
        body {
            font-family: Tahoma, sans-serif;
            background-color: #f2f2f2;
            padding: 20px;
            direction: rtl;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 95%;
            margin: auto;
            background: white;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #004080;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        a.btn {
            display: inline-block;
            padding: 5px 10px;
            background-color: #28a745;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }
        a.btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<h2>قائمة الغيابات غير المبررة الخاصة بك</h2>

<?php
if ($result && $result->num_rows > 0) {
    echo "<table>
            <tr>
                <th>المادة</th>
                <th>تاريخ الغياب</th>
                <th>الفترة</th>
                <th>القاعة</th>
                <th>نوع الغياب</th>
                <th>تبرير</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['module']}</td>
                <td>{$row['date_absence']}</td>
                <td>{$row['Heure_Deb']} - {$row['Heure_Fin']}</td>
                <td>{$row['nom_salle']}</td>
                <td>{$row['type']}</td>
                <td><a class='btn' href='form_justification.php?id={$row['Id_Abs']}'>تبرير</a></td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p style='text-align:center;'>لا توجد غيابات غير مبررة.</p>";
}
$conn->close();
?>

</body>
</html>

