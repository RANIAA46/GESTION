<?php
// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "", "gestionabsence");

if ($conn->connect_error) {
  die("فشل الاتصال: " . $conn->connect_error);
}

// جلب بيانات القاعات
$result = $conn->query("SELECT * FROM salle");
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>قائمة القاعات</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Tajawal', sans-serif;
      background-color: #f0fdfc;
      padding: 30px;
    }

    h2 {
      text-align: center;
      color: #00695c;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 25px;
      background-color: white;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    th, td {
      padding: 12px 15px;
      border: 1px solid #ddd;
      text-align: center;
    }

    th {
      background-color: #00796b;
      color: white;
    }

    tr:hover {
      background-color: #f1f1f1;
    }

    a.btn {
      padding: 6px 12px;
      border-radius: 6px;
      text-decoration: none;
      color: white;
      margin: 2px;
      font-size: 14px;
    }

    .edit {
      background-color: #0288d1;
    }

    .delete {
      background-color: #d32f2f;
    }

    .add-btn {
      display: inline-block;
      margin-bottom: 15px;
      background-color: #388e3c;
      padding: 10px 20px;
      color: white;
      text-decoration: none;
      border-radius: 8px;
    }

    .add-btn:hover {
      background-color: #2e7d32;
    }
  </style>
</head>
<body>

  <h2>📋 قائمة القاعات</h2>

  <a href="ajoutsalle.php" class="add-btn">➕ إضافة قاعة جديدة</a>

  <table>
    <thead>
      <tr>
        <th>اسم القاعة</th>
        <th>السعة</th>
        <th>العمليات</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['nom_salle']) ?></td>
          <td><?= htmlspecialchars($row['capacite']) ?></td>
          <td>
            <a class="btn edit" href="modifiersalle.php?nom_salle=<?= urlencode($row['nom_salle']) ?>">✏️ تعديل</a>
            <a class="btn delete" href="supprimersalle.php?nom_salle=<?= urlencode($row['nom_salle']) ?>" onclick="return confirm('هل أنت متأكد من حذف القاعة؟')">🗑️ حذف</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

</body>
</html>

<?php $conn->close(); ?>
