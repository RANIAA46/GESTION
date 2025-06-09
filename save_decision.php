<?php
session_start();

$conn = new mysqli("localhost", "root", "", "gestionabsence");
$conn->set_charset("utf8");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_abs = isset($_POST['id_abs']) ? intval($_POST['id_abs']) : 0;
    $commentaire = isset($_POST['commentaire']) ? $_POST['commentaire'] : '';

    if ($id_abs > 0 && in_array($commentaire, ['justification accepter', 'justification refuser'])) {
        // تعديل هنا لتتناسب مع قيم قاعدة البيانات
        $new_status = ($commentaire === 'justification accepter') ? 'justifiee' : 'non_justifiee';

        $stmt = $conn->prepare("UPDATE absence SET commentaire = ?, status_Abs = ? WHERE Id_Abs = ?");
        $stmt->bind_param("ssi", $commentaire, $new_status, $id_abs);
        $stmt->execute();

        // يمكن إعادة التوجيه مباشرة دون طباعة رسائل
        $stmt->close();
        $conn->close();
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    } else {
        $conn->close();
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }
}
?>
