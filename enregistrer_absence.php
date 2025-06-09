<?php
$conn = new mysqli("localhost", "root", "", "gestionabsence");
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

$id_seance = intval($_POST['id_seance']);
$date = $_POST['date_absence'];

if (isset($_POST['absents'])) {
    $absents = $_POST['absents'];
} else {
    $absents = [];
}

foreach ($absents as $matricule) {
    $matricule = intval($matricule);
    $res = $conn->query("SELECT Id_Univ FROM etud_univ WHERE matricule = $matricule LIMIT 1");
    if ($res && $res->num_rows > 0) {
        $id_univ = $res->fetch_assoc()['Id_Univ'];

        $conn->query("INSERT INTO absence (Date, status_Abs, type_Abs, Id_Seance, Id_Univ, Cause_abs)
                      VALUES ('$date', 'non_justifiee', 'abs_seance_pedagogique', $id_seance, $id_univ, '')");
    }
}

echo "<script>alert('تم تسجيل الغيابات بنجاح'); window.location.href='emploi.php';</script>";
?>  


