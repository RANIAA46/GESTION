<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $conn = new mysqli("localhost", "root", "", "gestionabsence");

  if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
  }

  $nom_salle = $_POST["nom_salle"];
  $capacite = $_POST["capacite"];

  $stmt = $conn->prepare("INSERT INTO salle (nom_salle, capacite) VALUES (?, ?)");
  $stmt->bind_param("si", $nom_salle, $capacite);

  if ($stmt->execute()) {
    echo "<script>alert('✅ تم إضافة القاعة بنجاح'); window.location.href='tablesalles.php';</script>";
  } else {
    echo "❌ فشل الإضافة: " . $conn->error;
  }

  $stmt->close();
  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>إضافة قاعة</title>
  <style>
    body {
      font-family: 'Tajawal', sans-serif;
      background: #e0f2f1;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .form-box {
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    label { font-weight: bold; display: block; margin-top: 10px; }
    input {
      width: 100%; padding: 10px; margin-top: 5px;
      border: 1px solid #ccc; border-radius: 6px;
    }
    button {
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #00796b;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
  </style>
</head>
<body>

  <div class="form-box">
    <h2>➕ إضافة قاعة</h2>
    <form method="POST">
      <label>اسم القاعة:</label>
      <input type="text" name="nom_salle" required>

      <label>السعة:</label>
      <input type="number" name="capacite" required>

      <button type="submit">💾 حفظ</button>
    </form>
  </div>

</body>
</html>
