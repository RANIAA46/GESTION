<?php
if (isset($_GET['matricule'])) {
  // الاتصال بقاعدة البيانات
  $conn = new mysqli("localhost", "root", "", "gestionabsence");

  if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
  }

  // الحصول على الرقم الجامعي
  $matricule = $_GET['matricule'];

  // تنفيذ استعلام الحذف
  $sql = "DELETE FROM etudiant WHERE matricule = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $matricule);

  if ($stmt->execute()) {
    echo "<script>alert('تم حذف الطالب بنجاح!'); window.location.href='tableetud.php';</script>";
  } else {
    echo "<script>alert('فشل الحذف!'); window.location.href='tableetud.php';</script>";
  }

  $stmt->close();
  $conn->close();
}
?>
