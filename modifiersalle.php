<?php
if (isset($_GET['nom_salle'])) {
  $original_nom_salle = $_GET['nom_salle'];

  // الاتصال بقاعدة البيانات
  $conn = new mysqli("localhost", "root", "", "gestionabsence");
  if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
  }

  // جلب بيانات القاعة
  $stmt = $conn->prepare("SELECT * FROM salle WHERE nom_salle = ?");
  $stmt->bind_param("s", $original_nom_salle);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
  } else {
    echo "❌ القاعة غير موجودة.";
    exit;
  }

  // عند إرسال النموذج
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_nom_salle = $_POST["nom_salle"];
    $capacite = $_POST["capacite"];

    // تحديث البيانات
    $update = $conn->prepare("UPDATE salle SET nom_salle=?, capacite=? WHERE nom_salle=?");
    $update->bind_param("sis", $new_nom_salle, $capacite, $original_nom_salle);

    if ($update->execute()) {
      echo "<script>alert('✅ تم تعديل القاعة بنجاح'); window.location.href='tablesalles.php';</script>";
    } else {
      echo "❌ خطأ أثناء التعديل: " . $conn->error;
    }

    $update->close();
  }

  $stmt->close();
  $conn->close();
} else {
  echo "❌ لم يتم تحديد اسم القاعة.";
  exit;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تعديل القاعة</title>
  <style>
    body {
      font-family: 'Tajawal', sans-serif;
      background: #f1f8e9;
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
      width: 400px;
    }
    label { font-weight: bold; display: block; margin-top: 10px; }
    input {
      width: 100%; padding: 10px;
      margin-top: 5px; border-radius: 6px;
      border: 1px solid #ccc;
    }
    button {
      margin-top: 20px;
      width: 100%;
      padding: 12px;
      background-color: #388e3c;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    button:hover {
      background-color: #2e7d32;
    }
  </style>
</head>
<body>

  <div class="form-box">
    <h2>✏️ تعديل القاعة</h2>
    <form method="POST">
      <label>اسم القاعة:</label>
      <input type="text" name="nom_salle" value="<?= htmlspecialchars($row['nom_salle']) ?>" required>

      <label>السعة:</label>
      <input type="number" name="capacite" value="<?= $row['capacite'] ?>" required>

      <button type="submit">💾 حفظ التعديلات</button>
    </form>
  </div>

</body>
</html>
