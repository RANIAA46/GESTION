<?php
// تأكد أن هناك رقم جامعي في الرابط
if (isset($_GET['matricule'])) {
  $matricule = $_GET['matricule'];

  // الاتصال بقاعدة البيانات
  $conn = new mysqli("localhost", "root", "", "gestionabsence");

  if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
  }

  // جلب بيانات الطالب
  $stmt = $conn->prepare("SELECT * FROM etudiant WHERE matricule = ?");
  $stmt->bind_param("s", $matricule);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
  } else {
    echo "الطالب غير موجود.";
    exit;
  }

  $stmt->close();

  // عند إرسال النموذج
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];
    $date_naiss = $_POST["date_naiss"];
    $lieu_naiss = $_POST["lieu_naiss"];
    $sexe = $_POST["sexe"];
    $date_bac = $_POST["date_bac"];
    $email = $_POST["email"];
    $id_dep = $_POST["id_dep"];

    // تحديث البيانات
    $update = $conn->prepare("UPDATE etudiant SET Nom_Etud=?, Prenom_Etud=?, Date_De_Naiss=?, Lieu_De_Naiss=?, Sexe=?, Date_Bac=?, Email_Etud=?, Id_Dep=? WHERE matricule=?");
    $update->bind_param("ssssssssi", $nom, $prenom, $date_naiss, $lieu_naiss, $sexe, $date_bac, $email, $id_dep, $matricule);

    if ($update->execute()) {
      echo "<script>alert('✅ تم التعديل بنجاح'); window.location.href='tableetud.php';</script>";
    } else {
      echo "❌ خطأ في التعديل: " . $conn->error;
    }

    $update->close();
  }

  $conn->close();
} else {
  echo "❌ لا يوجد معرف طالب محدد.";
  exit;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تعديل بيانات الطالب</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Tajawal', sans-serif;
      background: #f0fdfc;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 30px;
    }

    .form-container {
      background-color: #fff;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 600px;
    }

    h2 {
      text-align: center;
      color: #00796b;
    }

    label {
      font-weight: bold;
      display: block;
      margin-top: 15px;
      margin-bottom: 5px;
    }

    input, select {
      width: 100%;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    button {
      margin-top: 20px;
      width: 100%;
      padding: 12px;
      background-color: #00796b;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
    }

    button:hover {
      background-color: #004d40;
    }
  </style>
</head>
<body>

  <div class="form-container">
    <h2>تعديل بيانات الطالب</h2>
    <form method="POST">
      <label>الاسم:</label>
      <input type="text" name="nom" value="<?= $row['Nom_Etud'] ?>" required>

      <label>اللقب:</label>
      <input type="text" name="prenom" value="<?= $row['Prenom_Etud'] ?>" required>

      <label>تاريخ الميلاد:</label>
      <input type="date" name="date_naiss" value="<?= $row['Date_De_Naiss'] ?>" required>

      <label>مكان الميلاد:</label>
      <input type="text" name="lieu_naiss" value="<?= $row['Lieu_De_Naiss'] ?>" required>

      <label>الجنس:</label>
<select name="sexe" required>
  <option value="homme" <?= $row['Sexe'] == 'homme' ? 'selected' : '' ?>>homme</option>
  <option value="femme" <?= $row['Sexe'] == 'femme' ? 'selected' : '' ?>>femme</option>
</select>

      <label>تاريخ البكالوريا:</label>
      <input type="date" name="date_bac" value="<?= $row['Date_Bac'] ?>" required>

      <label>البريد الإلكتروني:</label>
      <input type="email" name="email" value="<?= $row['Email_Etud'] ?>" required>

      <label>رقم القسم:</label>
      <input type="number" name="id_dep" value="<?= $row['Id_Dep'] ?>" required>

      <button type="submit">💾 حفظ التغييرات</button>
    </form>
  </div>

</body>
</html>
