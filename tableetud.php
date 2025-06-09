<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <title>قائمة الطلبة - HADIR</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal&display=swap" rel="stylesheet"/>

  <style>
    body {
      font-family: 'Tajawal', sans-serif;
      margin: 0;
      padding: 20px;
      background: linear-gradient(to right, #76c7c0, #ffffff);
    }

    .container {
      max-width: 1200px;
      margin: auto;
      background-color: #fff;
      border-radius: 15px;
      padding: 30px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      color: #00796b;
      margin-bottom: 30px;
      font-size: 28px;
    }

    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      margin-bottom: 20px;
      gap: 10px;
    }

    .search-bar input {
      padding: 12px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 6px;
      width: 250px;
    }

    .search-bar button {
      padding: 12px 18px;
      background-color: #00796b;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
    }

    .search-bar button:hover {
      background-color: #004d40;
    }

    .add-btn {
      padding: 14px 28px;
      background-color: #2e7d32;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-size: 18px;
      transition: background 0.3s ease;
    }

    .add-btn:hover {
      background-color: #1b5e20;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      text-align: center;
      font-size: 18px;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 15px 12px;
    }

    th {
      background-color: #00796b;
      color: white;
      font-size: 18px;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    tr:hover {
      background-color: #f1f1f1;
    }

    .actions a {
      margin: 0 6px;
      color: #00796b;
      font-size: 20px;
      text-decoration: none;
    }

    .actions a.delete {
      color: #e53935;
    }

    .back-btn {
      display: block;
      width: fit-content;
      margin: 25px auto 0;
      padding: 12px 24px;
      background-color: #00796b;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-size: 16px;
    }

    .back-btn:hover {
      background-color: #004d40;
    }
  </style>
</head>
<body>

  <div class="container">
    <h2><i class="fas fa-list"></i> قائمة الطلبة</h2>

    <div class="top-bar">
      <a href="ajout.html" class="add-btn"><i class="fas fa-plus"></i> إضافة طالب</a>

      <div class="search-bar">
        <input type="text" id="search-input" placeholder="ابحث عن الرقم الجامعي...">
        <button onclick="searchStudent()">بحث</button>
      </div>
    </div>

    <table id="students-table">
      <thead>
        <tr>
          <th>الرقم</th>
          <th>اللقب</th>
          <th>الاسم</th>
          <th>البريد الإلكتروني</th>
          <th>تاريخ البكالوريا</th>
          <th>الخيارات</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $conn = new mysqli("localhost", "root", "", "gestionabsence");
          mysqli_set_charset($conn, "utf8");

          if ($conn->connect_error) {
              die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
          }

          $sql = "SELECT * FROM etudiant";
          $result = $conn->query($sql);

          if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  echo "<tr>";
                  echo "<td>" . $row["matricule"] . "</td>";
                  echo "<td>" . $row["Nom_Etud"] . "</td>";
                  echo "<td>" . $row["Prenom_Etud"] . "</td>";
                  echo "<td>" . $row["Email_Etud"] . "</td>";
                  echo "<td>" . $row["Date_Bac"] . "</td>";
                  echo "<td class='actions'>
                          <a href='modifier.php?matricule=" . $row["matricule"] . "' title='تعديل'><i class='fas fa-edit'></i></a>
                          <a href='supprimer.php?matricule=" . $row["matricule"] . "' class='delete' title='حذف' onclick=\"return confirm('هل أنت متأكد أنك تريد حذف هذا الطالب؟');\"><i class='fas fa-trash-alt'></i></a>
                        </td>";
                  echo "</tr>";
              }
          } else {
              echo "<tr><td colspan='6'>لا يوجد طلبة مسجلين.</td></tr>";
          }

          $conn->close();
        ?>
      </tbody>
    </table>

    <a href="chefDepartement.html" class="back-btn">الرجوع</a>
  </div>

  <script>
    function searchStudent() {
      let input = document.getElementById("search-input").value.toLowerCase();
      let rows = document.querySelectorAll("#students-table tbody tr");

      rows.forEach(row => {
        let matricule = row.cells[0].textContent.toLowerCase();
        row.style.display = matricule.includes(input) ? "" : "none";
      });
    }
  </script>

</body>
</html>
