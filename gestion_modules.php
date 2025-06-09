<!DOCTYPE html>
<head>
    <html lang="ar" dir="rtl">

  <meta charset="UTF-8">
  <title>Gestion des Modules</title>
  <!-- Font Awesome لأيقونات التعديل والحذف -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #e9f5ee;
      padding: 20px;
    }

    h2 {
      text-align: center;
      color: #2e7d32; /* أخضر داكن */
    }

    table {
      width: 80%;
      margin: 0 auto;
      border-collapse: collapse;
      background-color: #ffffff;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    th, td {
      padding: 12px;
      text-align: center;
      border: 1px solid #c8e6c9;
    }

    th {
      background-color: #2e7d32; /* أخضر داكن */
      color: white;
    }

    tr:hover {
      background-color: #f1f8f4;
    }

    a.icon {
      text-decoration: none;
      margin: 0 5px;
      font-size: 18px;
      color: #388e3c; /* أخضر متوسط */
    }

    a.icon:hover {
      color: #1b5e20; /* أخضر أغمق */
    }

    .delete {
      color: #c62828; /* أحمر خفيف */
    }

    .delete:hover {
      color: #8e0000;
    }

    .add-button {
      display: block;
      width: fit-content;
      margin: 20px auto;
      padding: 10px 20px;
      background-color: #43a047; /* أخضر زاهي */
      color: white;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .add-button:hover {
      background-color: #2e7d32;
    }
  </style>
</head>
<body>
  <h2>قائمة الوحدات</h2>

  <table>
    <tr>
      <th>معرف الوحدة</th>
      <th>اسم الوحدة</th>
      <th>المعامل</th>
      <th>العمليات</th>
    </tr>

    <?php
    $conn = new mysqli("localhost", "root", "", "gestionabsence");
    if ($conn->connect_error) {
        die("Échec de connexion: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM module";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
      echo "<tr>
              <td>{$row['Id_Mdl']}</td>
              <td>{$row['Disigne']}</td>
              <td>{$row['Coeff']}</td>
              <td>
                <a class='icon' href='modifier_module.php?Id_Mdl={$row['Id_Mdl']}' title='تعديل'><i class='fas fa-edit'></i></a>
                <a class='icon delete' href='supprimer_module.php?Id_Mdl={$row['Id_Mdl']}' title='حذف' onclick=\"return confirm('هل أنت متأكد أنك تريد حذف هذه الوحدة؟');\"><i class='fas fa-trash-alt'></i></a>
              </td>
            </tr>";
    }

    $conn->close();
    ?>
  </table>

  <a class="add-button" href="ajouter_module.html">+ إضافة وحدة</a>
</body>
</html>
