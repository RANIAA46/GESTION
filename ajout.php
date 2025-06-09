<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // الاتصال بقاعدة البيانات
  $conn = new mysqli("localhost", "root", "", "gestionabsence"); // ✅ غيّر الاسم إذا لزم

  if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
  }

  // استقبال القيم
  $matricule = $_POST["matricule"];
  $nom = $_POST["nom"];
  $prenom = $_POST["prenom"];
  $date_naiss = $_POST["date_naiss"];
  $lieu_naiss = $_POST["lieu_naiss"];
  $sexe = $_POST["sexe"];
  $date_bac = $_POST["date_bac"];
  $email = $_POST["email"];
  $id_dep = $_POST["id_dep"];

  // إدخال البيانات
  $sql = "INSERT INTO etudiant (matricule, Nom_Etud, Prenom_Etud, Date_De_Naiss, Lieu_De_Naiss, Sexe, Date_Bac, Email_Etud, Id_Dep)
          VALUES ('$matricule', '$nom', '$prenom', '$date_naiss', '$lieu_naiss', '$sexe', '$date_bac', '$email', '$id_dep')";

  if ($conn->query($sql) === TRUE) {
    echo "<script>alert('✅ تم إضافة الطالب بنجاح'); window.location.href='tableetud.php';</script>";
  } else {
    echo "❌ خطأ أثناء الإضافة: " . $conn->error;
  }

  $conn->close();
}
?>