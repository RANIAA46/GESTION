<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $id_ens = $_POST['id_ens'];
  $nom = $_POST['nom'];
  $prenom = $_POST['prenom'];
  $date_naiss = $_POST['date_naiss'];
  $lieu_naiss = $_POST['lieu_naiss'];
  $sexe = $_POST['sexe'];
  $email = $_POST['email'];

  $conn = new mysqli("localhost", "root", "", "gestionabsence");
  if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
  }

  // التأكد أن رقم الأستاذ غير مكرر
  $check = $conn->prepare("SELECT Id_Ens FROM enseignant WHERE Id_Ens = ?");
  $check->bind_param("i", $id_ens);
  $check->execute();
  $check->store_result();

  if ($check->num_rows > 0) {
    echo "<script>alert('⚠️ رقم الأستاذ موجود مسبقًا!'); window.history.back();</script>";
  } else {
    $stmt = $conn->prepare("INSERT INTO enseignant (Id_Ens, Nom_Ens, Prenom_Ens, Date_Naiss_ENS, Lieu_Naiss_ENS, Sexe, Email_ens) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $id_ens, $nom, $prenom, $date_naiss, $lieu_naiss, $sexe, $email);

    if ($stmt->execute()) {
      echo "<script>alert('✅ تم إضافة الأستاذ بنجاح'); window.location.href='gestion_enseignants.php';</script>";
    } else {
      echo "❌ خطأ: " . $conn->error;
    }

    $stmt->close();
  }

  $check->close();
  $conn->close();
} else {
  echo "طريقة الطلب غير صحيحة.";
}
?>
