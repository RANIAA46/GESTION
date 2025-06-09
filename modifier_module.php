<?php
$conn = new mysqli("localhost", "root", "", "gestionabsence");
if ($conn->connect_error) die("Erreur de connexion");

$Id_Mdl = '';
$Disigne = '';
$Coeff = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ✅ المعالجة: تعديل الوحدة
    $Id_Mdl = $_POST['Id_Mdl'];
    $Disigne = $_POST['Disigne'];
    $Coeff = $_POST['Coeff'];

    $sql = "UPDATE module SET Disigne = ?, Coeff = ? WHERE Id_Mdl = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sis", $Disigne, $Coeff, $Id_Mdl);
    if ($stmt->execute()) {
        header("Location: gestion_modules.php");
        exit;
    } else {
        echo "<p style='color:red;'>Erreur lors de la mise à jour.</p>";
    }
} else if (isset($_GET['Id_Mdl'])) {
    // ✅ عرض بيانات الوحدة في النموذج
    $Id_Mdl = $_GET['Id_Mdl'];
    $stmt = $conn->prepare("SELECT * FROM module WHERE Id_Mdl = ?");
    $stmt->bind_param("s", $Id_Mdl);
    $stmt->execute();
    $result = $stmt->get_result();
    $module = $result->fetch_assoc();

    if ($module) {
        $Disigne = $module['Disigne'];
        $Coeff = $module['Coeff'];
    } else {
        echo "<p style='color:red;'>Module introuvable.</p>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Modifier Module</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f6f8;
      padding: 30px;
    }
    form {
      width: 400px;
      margin: auto;
      background: #fff;
      padding: 20px;
      box-shadow: 0 0 10px #ccc;
      border-radius: 10px;
    }
    input, label, button {
      display: block;
      width: 100%;
      margin-bottom: 15px;
    }
    input {
      padding: 10px;
    }
    button {
      padding: 10px;
      background-color: #27ae60;
      color: white;
      border: none;
      cursor: pointer;
      font-weight: bold;
    }
    button:hover {
      background-color: #219150;
    }
    h2 {
      text-align: center;
      color: #2c3e50;
    }
  </style>
</head>
<body>

<h2>تعديل الوحدة</h2>
<form method="POST">
  <input type="hidden" name="Id_Mdl" value="<?= htmlspecialchars($Id_Mdl) ?>">

  <label>اسم الوحدة:</label>
  <input type="text" name="Disigne" value="<?= htmlspecialchars($Disigne) ?>" required>

  <label>المعامل:</label>
  <input type="number" name="Coeff" value="<?= htmlspecialchars($Coeff) ?>" required>

  <button type="submit">حفظ التعديلات</button>
</form>

</body>
</html>
