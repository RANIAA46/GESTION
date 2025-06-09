<?php
$conn = new mysqli("localhost", "root", "", "gestionabsence");
if ($conn->connect_error) {
  die("فشل الاتصال: " . $conn->connect_error);
}

$sql = "SELECT * FROM enseignant ORDER BY Id_Ens ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <title>قائمة الأساتذة</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Tajawal', sans-serif;
      background: #e8f5f2;
      padding: 30px;
      text-align: center;
    }
    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
      max-width: 1000px;
      margin: 0 auto 20px;
    }
    .search-bar input {
      padding: 10px;
      font-size: 16px;
      width: 250px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
    .search-bar button {
      padding: 10px 16px;
      background-color: #00796b;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    .search-bar button:hover {
      background-color: #004d40;
    }
    table {
      margin: 0 auto;
      border-collapse: collapse;
      width: 90%;
      max-width: 1000px;
      background: white;
      box-shadow: 0 3px 8px rgba(0,0,0,0.15);
    }
    th, td {
      border: 1px solid #ddd;
      padding: 12px 15px;
      text-align: center;
    }
    th {
      background-color: #00796b;
      color: white;
    }
    tr:hover {
      background-color: #f1f9f8;
    }
    a {
      text-decoration: none;
      color: #00796b;
      font-weight: bold;
    }
    a:hover {
      color: #004d40;
    }
    .btn-add {
      display: inline-block;
      margin: 20px auto 0;
      padding: 10px 20px;
      background-color: #00796b;
      color: white;
      border-radius: 10px;
      text-decoration: none;
      font-size: 16px;
      transition: background-color 0.3s;
    }
    .btn-add:hover {
      background-color: #004d40;
    }
  </style>
</head>
<body>

  <h2>قائمة الأساتذة</h2>

  <div class="top-bar">
    <a href="ajoutens.html" class="btn-add">➕ إضافة أستاذ جديد</a>

    <div class="search-bar">
      <input type="text" id="search-input" placeholder="ابحث برقم أو اسم أو لقب الأستاذ...">
      <button onclick="searchTeacher()">بحث</button>
    </div>
  </div>

  <table id="teachers-table">
    <thead>
      <tr>
        <th>رقم الأستاذ</th>
        <th>الاسم</th>
        <th>اللقب</th>
        <th>تاريخ ومكان الميلاد</th>
        <th>الجنس</th>
        <th>البريد الإلكتروني</th>
        <th>تعديل</th>
        <th>حذف</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['Id_Ens'] ?></td>
            <td><?= htmlspecialchars($row['Nom_Ens']) ?></td>
            <td><?= htmlspecialchars($row['Prenom_Ens']) ?></td>
            <td><?= $row['Date_Naiss_ENS'] . ' - ' . htmlspecialchars($row['Lieu_Naiss_ENS']) ?></td>
            <td><?= htmlspecialchars($row['Sexe']) ?></td>
            <td><?= htmlspecialchars($row['Email_ens']) ?></td>
            <td><a href="modifierens.php?id_ens=<?= $row['Id_Ens'] ?>">✏️ تعديل</a></td>
            <td><a href="supprimerens.php?id_ens=<?= $row['Id_Ens'] ?>" onclick="return confirm('هل أنت متأكد من حذف هذا الأستاذ؟');">🗑️ حذف</a></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="8">لا يوجد أساتذة في النظام</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <script>
    function searchTeacher() {
      let input = document.getElementById("search-input").value.toLowerCase();
      let rows = document.querySelectorAll("#teachers-table tbody tr");

      rows.forEach(row => {
        let id = row.cells[0].textContent.toLowerCase();
        let nom = row.cells[1].textContent.toLowerCase();
        let prenom = row.cells[2].textContent.toLowerCase();

        if (id.includes(input) || nom.includes(input) || prenom.includes(input)) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      });
    }
  </script>

</body>
</html>

<?php
$conn->close();
?>
