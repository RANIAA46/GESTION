<?php
$conn = new mysqli("localhost", "root", "", "gestionabsence");

if ($conn->connect_error) {
    die("Échec de connexion : " . $conn->connect_error);
}

if (isset($_GET['Id_Mdl'])) {
    $Id_Mdl = $_GET['Id_Mdl'];
    $sql = "DELETE FROM module WHERE Id_Mdl = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $Id_Mdl);

    if ($stmt->execute()) {
        header("Location: gestion_modules.php");
    } else {
        echo "Erreur lors de la suppression : " . $conn->error;
    }
} else {
    echo "Module non spécifié !";
}

$conn->close();
?>
