<?php
session_start();
$conn = new mysqli("localhost", "root", "", "gestionabsence");
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

if (isset($_POST['id_abs'], $_POST['cause']) && isset($_FILES['justif_file'])) {
    $id_abs = intval($_POST['id_abs']);
    $cause = $conn->real_escape_string($_POST['cause']);

    // معالجة الملف
    $upload_dir = 'uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true); // إنشاء المجلد إذا غير موجود
    }

    $file_name = basename($_FILES['justif_file']['name']);
    $target_file = $upload_dir . time() . "_" . $file_name;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed = ['pdf', 'jpg', 'jpeg', 'png'];

    if (in_array($file_type, $allowed)) {
        if (move_uploaded_file($_FILES['justif_file']['tmp_name'], $target_file)) {
            $cause .= " (ملف: $target_file)";
            $sql = "UPDATE absence 
                    SET status_Abs = 'justifiée', Cause_abs = '$cause'
                    WHERE Id_Abs = $id_abs";

            if ($conn->query($sql) === TRUE) {
                echo "<script>alert('تم إرسال المبرر والملف بنجاح'); window.location.href='justifier_absence.php';</script>";
            } else {
                echo "<script>alert('فشل في حفظ المبرر'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('فشل في تحميل الملف'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('نوع الملف غير مدعوم'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('يرجى ملء جميع الحقول'); window.history.back();</script>";
}

$conn->close();

