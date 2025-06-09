<?php
$conn = new mysqli("localhost", "root", "", "gestionabsence");
mysqli_set_charset($conn, "utf8");

if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

$role = $_POST["role"];
$email = $_POST["email"];
$old_pass = $_POST["old_pass"];
$new_pass = $_POST["new_pass"];
$confirm_pass = $_POST["confirm_pass"];

if ($new_pass !== $confirm_pass) {
    die("كلمة المرور الجديدة وتأكيدها غير متطابقين.");
}

// تحديد الجدول واسم الحقول حسب الدور
switch ($role) {
    case "etudiant":
        $table = "etudiant";
        $email_field = "Email_Etud";
        $pass_field = "Date_De_Naiss";
        break;
    case "enseignant":
        $table = "enseignant";
        $email_field = "Email_Ens";
        $pass_field = "Date_Naiss_ENS";
        break;
    case "departement":
        $table = "departement";
        $email_field = "Email_Dep";
        $pass_field = "Date_Naiss_Dep";
        break;
    default:
        die("نوع المستخدم غير صالح.");
}

// جلب كلمة المرور الحالية
$sql = "SELECT $pass_field FROM $table WHERE $email_field = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($stored_pass);
$stmt->fetch();
$stmt->close();

if (!$stored_pass) {
    die("البريد الإلكتروني غير موجود.");
}

if ($old_pass !== $stored_pass) {
    die("كلمة المرور الحالية غير صحيحة.");
}

// تحديث كلمة المرور الجديدة
$update_sql = "UPDATE $table SET $pass_field = ? WHERE $email_field = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("ss", $new_pass, $email);

if ($update_stmt->execute()) {
    echo "تم تغيير كلمة المرور بنجاح.";
    header("Location: صفحة تسجيل الدخول.html");
    exit();
} else {
    echo "حدث خطأ أثناء التحديث: " . $conn->error;
}

$conn->close();
?>
