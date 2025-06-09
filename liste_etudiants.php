<?php
session_start();
if (!isset($_SESSION['id_ens'])) {
    header("Location: enseignant.html");
    exit();
}

if (!isset($_GET['id_seance'])) {
    echo "رقم الحصة غير متوفر.";
    exit();
}

$conn = new mysqli("localhost", "root", "", "gestionabsence");
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

$id_seance = intval($_GET['id_seance']);

$seance = $conn->query("SELECT s.*, m.Disigne, r.Heure_Deb, r.Heure_Fin, ens.Nom_Ens, ens.Prenom_Ens
                        FROM seance s
                        JOIN module m ON s.Id_Mdl = m.Id_Mdl
                        JOIN repartition r ON s.Num_Rep = r.Num_Rep
                        JOIN enseignant ens ON s.Id_Ens = ens.Id_Ens
                        WHERE s.Id_Seance = $id_seance")->fetch_assoc();

$id_grp = $seance['Id_Grp'];
$id_mdl = $seance['Id_Mdl'];

$query = "SELECT e.matricule, e.Nom_Etud, e.Prenom_Etud 
          FROM etudiant e
          JOIN etud_univ eu ON e.matricule = eu.matricule
          WHERE eu.Id_Grp = $id_grp AND eu.Id_Mdl = '$id_mdl'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تسجيل الغيابات</title>
  <style>
    body {
      font-family: 'Tajawal', sans-serif;
      background-color: #f1f8f7;
      padding: 30px;
      text-align: center;
    }
    table {
      margin: auto;
      border-collapse: collapse;
      width: 80%;
      background-color: #fff;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 12px;
      border: 1px solid #ccc;
    }
    th {
      background-color: #00796b;
      color: white;
    }
    input[type="date"] {
      padding: 6px;
      font-size: 14px;
    }
    button {
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #00796b;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
    }
    button:hover {
      background-color: #004d40;
    }
    .info-box {
      margin-bottom: 20px;
      font-size: 18px;
      line-height: 1.8;
    }
  </style>
</head>
<body>

<h2>تسجيل الغياب - الحصة رقم <?= $id_seance ?></h2>

<div class="info-box">
  📘 <strong>المادة:</strong> <?= $seance['Disigne'] ?> <br>
  👨‍🏫 <strong>الأستاذ:</strong> <?= $seance['Nom_Ens'] . ' ' . $seance['Prenom_Ens'] ?> <br>
  🕒 <strong>الوقت:</strong> <?= $seance['Heure_Deb'] ?> - <?= $seance['Heure_Fin'] ?> <br>
  👥 <strong>رقم الفوج:</strong> <?= $seance['Id_Grp'] ?>
</div>

<form action="enregistrer_absence.php" method="POST">
  <input type="hidden" name="id_seance" value="<?= $id_seance ?>">
  <label>📅 اختر تاريخ الحصة:</label>
  <input type="date" name="date_absence" required><br><br>

  <table>
    <tr>
      <th>تحديد</th>
      <th>الاسم</th>
      <th>اللقب</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
      <td><input type="checkbox" name="absents[]" value="<?= $row['matricule'] ?>"></td>
      <td><?= $row['Nom_Etud'] ?></td>
      <td><?= $row['Prenom_Etud'] ?></td>
    </tr>
    <?php endwhile; ?>
  </table>

  <button type="submit">تسجيل الغيابات</button>
</form>

</body>
</html>
