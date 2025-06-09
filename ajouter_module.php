<?php
$conn = new mysqli("localhost", "root", "", "gestionabsence");

if ($conn->connect_error) {
    die("Échec de connexion : " . $conn->connect_error);
}

$Id_Mdl = $_POST['Id_Mdl'];
$Disigne = $_POST['Disigne'];
$Coeff = $_POST['Coeff'];

$sql = "INSERT INTO module (Id_Mdl, Disigne, Coeff) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $Id_Mdl, $Disigne, $Coeff);

if ($stmt->execute()) {
    header("Location: gestion_modules.php"); // الصفحة الرئيسية
} else {
    echo "Erreur : " . $conn->error;
}

$conn->close();
?>
