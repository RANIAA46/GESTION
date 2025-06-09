<?php
// emploi_prof.php
$pdo = new PDO("mysql:host=localhost;dbname=gestionabsence", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

session_start();

if (!isset($_GET['id_ens'])) {
    echo "❌ المعرف غير موجود.";
    exit;
}
$id_ens = $_GET['id_ens'];

// جلب كل الفترات الزمنية
$reps = $pdo->query("SELECT * FROM repartition ORDER BY Heure_Deb")->fetchAll(PDO::FETCH_ASSOC);

// الأيام
$jours = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi'];

// إنشاء مصفوفة فارغة للتوقيت
$timetable = [];
foreach ($jours as $jour) {
    foreach ($reps as $rep) {
        $timetable[$jour][$rep['Num_Rep']] = "";
    }
}

// جلب الحصص الخاصة بالأستاذ
$sql = "
    SELECT e.*, m.Disigne, g.Id_Grp, s.nom_salle, r.Heure_Deb, r.Heure_Fin
    FROM emploi e
    JOIN module m ON e.Id_Mdl = m.Id_Mdl
    JOIN groupe g ON e.Id_Grp = g.Id_Grp
    JOIN salle s ON e.nom_salle = s.nom_salle
    JOIN repartition r ON e.Num_Rep = r.Num_Rep
    WHERE e.Id_Ens = ?
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_ens]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ملء الجدول بالمعلومات
foreach ($data as $row) {
    $jour = $row['Jours'];
    $num_rep = $row['Num_Rep'];

    $content = htmlspecialchars($row['Disigne']) . "<br>" .
        "groupe: <a href='presence.php?id_seance=" . urlencode($row['Id_Emp']) . "'>" . htmlspecialchars($row['Id_Grp']) . "</a><br>" .
        "Salle: " . htmlspecialchars($row['nom_salle']);

    $timetable[$jour][$num_rep] = $content;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Emploi du temps</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #00796b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            vertical-align: middle;
        }
        th {
            background-color: #00796b;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        a {
            color: #00796b;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h2> جدول توقيت الاستاذ 📚</h2>

<table>
    <thead>
        <tr>
            <th>Jour / Heure</th>
            <?php foreach ($reps as $rep): ?>
                <th><?= htmlspecialchars($rep['Heure_Deb']) ?> <br>à<br> <?= htmlspecialchars($rep['Heure_Fin']) ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($jours as $jour): ?>
            <tr>
                <th><?= htmlspecialchars($jour) ?></th>
                <?php foreach ($reps as $rep): ?>
                    <td><?= $timetable[$jour][$rep['Num_Rep']] ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
