<?php
$conn = new mysqli("localhost", "root", "", "gestionabsence");
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $role = $_POST["role"];

    $table = "";
    $email_field = "";

    if ($role === "etudiant") {
        $table = "etudiant";
        $email_field = "Email_Etud";
    } elseif ($role === "enseignant") {
        $table = "enseignant";
        $email_field = "Email_ens";
    } elseif ($role === "chef") {
        $table = "departement";
        $email_field = "Email_Dep";
    } else {
        die("نوع المستخدم غير صالح.");
    }

    // التأكد من وجود المستخدم
    $stmt = $conn->prepare("SELECT * FROM $table WHERE $email_field = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // طلب استرجاع كلمة المرور
        $update = $conn->prepare("UPDATE $table SET `reset password requested` = 1 WHERE $email_field = ?");
        $update->bind_param("s", $email);
        $update->execute();
        $update->close();

        echo "<script>alert('تم إرسال رابط تغيير كلمة المرور.'); window.location.href=' new.html';</script>";
    } else {
        echo "<script>alert('البريد الإلكتروني غير موجود.'); window.history.back();</script>";
    }
    

    $stmt->close();

}

$conn->close();
?>