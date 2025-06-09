<?php
$conn = new mysqli("localhost", "root", "", "gestionabsence");
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $role = $_POST["role"];
    $new_password = trim($_POST["new_password"]);

    // التحقق من أن الحقول ليست فارغة
    if (empty($email) || empty($role) || empty($new_password)) {
        echo "<script>alert('يرجى ملء جميع الحقول.'); window.history.back();</script>";
        exit;
    }

    // تحديد اسم الجدول وحقول البريد وكلمة المرور حسب نوع المستخدم
    $table = "";
    $email_field = "";
    $password_field = "";

    if ($role === "etudiant") {
        $table = "etudiant";
        $email_field = "Email_Etud";
        $password_field = "Date_De_Naiss";
    } elseif ($role === "enseignant") {
        $table = "enseignant";
        $email_field = "Email_ens";
        $password_field = "Date_Naiss_Ens";
    } elseif ($role === "chef") {
        $table = "departement";
        $email_field = "Email_Dep";
        $password_field = "Date_Naiss_Dep";
    } else {
        die("نوع المستخدم غير صالح.");
    }

    // التأكد من وجود طلب استرجاع
    $stmt = $conn->prepare("SELECT * FROM $table WHERE $email_field = ? AND `reset password requested` = 1");
    if (!$stmt) {
        die("فشل تحضير الاستعلام: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // تحديث كلمة المرور (تاريخ الميلاد) وإلغاء طلب الاسترجاع
        $update = $conn->prepare("UPDATE $table SET $password_field = ?, `reset password requested` = 0 WHERE $email_field = ?");
        if (!$update) {
            die("فشل تحضير استعلام التحديث: " . $conn->error);
        }

        $update->bind_param("ss", $new_password, $email);
        if ($update->execute()) {
            echo "<script>alert('تم تحديث كلمة المرور بنجاح.'); window.location.href='صفحة تسجيل الدخول.html';</script>";
        } else {
            echo "<script>alert('حدث خطأ أثناء التحديث.');</script>";
        }
        $update->close();
    } else {
        echo "<script>alert('لم يتم العثور على طلب استرجاع صالح لهذا البريد.'); window.history.back();</script>";
    }

    $stmt->close();
}

$conn->close();
?>
س
