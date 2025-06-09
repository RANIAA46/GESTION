<?php
if (isset($_GET['id_ens'])) {
  $id_ens = $_GET['id_ens'];
  $conn = new mysqli("localhost", "root", "", "gestionabsence");

  if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
  }

  $stmt = $conn->prepare("DELETE FROM enseignant WHERE Id_Ens = ?");
  $stmt->bind_param("i", $id_ens);

  if ($stmt->execute()) {
    echo "<script>alert('تم حذف الأستاذ بنجاح'); window.location.href='gestion_enseignants.php';</script>";
  } else {
    echo "خطأ أثناء الحذف: " . $conn->error;
  }

  $stmt->close();
  $conn->close();
} else {
  echo "لم يتم تحديد الأستاذ للحذف.";
}
?>
