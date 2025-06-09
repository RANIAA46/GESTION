<?php
if (!isset($_GET['id_ens'])) {
  echo "لم يتم تحديد الأستاذ.";
  exit;
}

$id_ens = $_GET['id_ens'];
$conn = new mysqli("localhost", "root", "", "gestionabsence");
if ($conn->connect_error) {
  die("فشل الاتصال: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nom = $_POST['nom'];
  $prenom = $_POST['prenom'];
  $date_naiss = $_POST['date_naiss'];
  $lieu_naiss = $_POST['lieu_naiss'];
  $sexe = $_POST['sexe'];
  $email = $_POST['email'];

  $stmt = $conn->prepare("UPDATE enseignant SET Nom_Ens=?, Prenom_Ens=?, Date_Naiss_ENS=?, Lieu_Naiss_Ens=?, Sexe=?, Email_ens=? WHERE Id_Ens=?");
  $stmt->bind_param("ssssssi", $nom, $prenom, $date_naiss, $lieu_naiss, $sexe, $email, $id_ens);

  if ($stmt->execute()) {
    echo "<script>alert('تم تعديل بيانات الأستاذ بنجاح'); window.location.href='gestion_enseignants.php';</script>";
  } else {
    echo "خطأ في التعديل: " . $conn->error;
  }
  $stmt->close();
  $conn->close();
  exit;
}

$sql = "SELECT * FROM enseignant WHERE Id_Ens = $id_ens";
$result = $conn->query($sql);
if ($result->num_rows != 1) {
  echo "الأستاذ غير موجود.";
  exit;
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <title>تعديل بيانات الأستاذ</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Tajawal', sans-serif;
      background: #f0f4f3;
      padding: 40px;
      max-width: 500px;
      margin: auto;
      direction: rtl;
      text-align: right;
    }
    label {
      display: block;
      margin-bottom: 8px;
      font-weight: bold;
    }
    input, select {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 16px;
    }
    button {
      background-color: #00796b;
      color: white;
      padding: 12px;
      width: 100%;
      border: none;
      border-radius: 10px;
      font-size: 18px;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    button:hover {
      background-color: #004d40;
    }
  </style>
</head>
<body>

  <h2>تعديل بيانات الأستاذ</h2>

  <form method="POST">
    <label>الاسم:</label>
    <input type="text" name="nom" value="<?= htmlspecialchars($row['Nom_Ens']) ?>" required />

    <label>اللقب:</label>
    <input type="text" name="prenom" value="<?= htmlspecialchars($row['Prenom_Ens']) ?>" required />

    <label>تاريخ الميلاد:</label>
    <input type="date" name="date_naiss" value="<?= $row['Date_Naiss_ENS'] ?>" required />

    <label>مكان الميلاد:</label>
    <input type="text" name="lieu_naiss" value="<?= htmlspecialchars($row['Lieu_Naiss_ENS']) ?>" required />

    <label>الجنس:</label>
    <select name="sexe" required>
      <option value="">-- اختر --</option>
      <option value="homme" <?= $row['Sexe'] == 'homme' ? 'selected' : '' ?>>ذكر</option>
      <option value="femme" <?= $row['Sexe'] == 'femme' ? 'selected' : '' ?>>أنثى</option>
    </select>

    <label>البريد الإلكتروني:</label>
    <input type="email" name="email" value="<?= htmlspecialchars($row['Email_ens']) ?>" required />

    <button type="submit">حفظ التعديلات</button>
  </form>

</body>
</html>
