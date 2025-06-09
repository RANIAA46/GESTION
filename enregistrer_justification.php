<?php
session_start();

$conn = new mysqli("localhost", "root", "", "gestionabsence");
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

if (!isset($_SESSION['matricule'])) {
    echo "الرجاء تسجيل الدخول أولاً.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_abs = intval($_POST['id_abs']);
    $cause = $conn->real_escape_string($_POST['cause']);
    $date_justif = date('Y-m-d H:i:s');

    $filePath = null;

    if (isset($_FILES['justificatif']) && $_FILES['justificatif']['error'] === UPLOAD_ERR_OK) {
        $uploads_dir = 'uploads';
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0777, true);
        }
        $filename = basename($_FILES['justificatif']['name']);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $newFilename = uniqid() . '.' . $extension;
        $targetPath = $uploads_dir . '/' . $newFilename;

        if (move_uploaded_file($_FILES['justificatif']['tmp_name'], $targetPath)) {
            $filePath = $conn->real_escape_string($targetPath);
        } else {
            echo "فشل في رفع الملف.";
            exit();
        }
    }

    $sql = "UPDATE absence 
            SET status_Abs = 'en_attente', 
                Cause_abs = '$cause',
                date_justification = '$date_justif'";

    if ($filePath !== null) {
        $sql .= ", fichier_justificatif = '$filePath'";
    }

    $sql .= " WHERE Id_Abs = $id_abs";

    if ($conn->query($sql) === TRUE) {
        echo "تم إرسال التبرير بنجاح.";
        echo "<br><a href='justifier_absence.php'>العودة إلى قائمة الغيابات</a>";
    } else {
        echo "حدث خطأ: " . $conn->error;
    }
} else {
    echo "الرجاء تقديم النموذج أولاً.";
}
$conn->close();
?>
