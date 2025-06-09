<?php
if (isset($_GET['nom_salle'])) {
  $conn = new mysqli("localhost", "root", "", "gestionabsence");

  if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
  }

  $nom_salle = $_GET['nom_salle'];

  $stmt = $conn->prepare("DELETE FROM salle WHERE nom_salle = ?");
  $stmt->bind_param("s", $nom_salle);

  if ($stmt->execute()) {
    echo "<script>alert('✅ تم حذف القاعة بنجاح'); window.location.href='tablesalles.php';</script>";
  } else {
    echo "<script>alert('❌ فشل الحذف'); window.location.href='tablesalles.php';</script>";
  }

  $stmt->close();
  $conn->close();
}
?>
