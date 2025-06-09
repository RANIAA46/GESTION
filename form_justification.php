<?php
session_start();
if (!isset($_SESSION['matricule'])) {
    echo "الرجاء تسجيل الدخول أولاً.";
    exit();
}

if (!isset($_GET['id'])) {
    echo "معرف الغياب غير موجود.";
    exit();
}

$id_abs = intval($_GET['id']);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تبرير الغياب</title>
    <style>
        body {
            font-family: Tahoma, sans-serif;
            background-color: #f7f7f7;
            padding: 30px;
            direction: rtl;
        }
        form {
            background-color: white;
            width: 500px;
            margin: auto;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        textarea, input[type="file"] {
            width: 100%;
            margin-bottom: 20px;
        }
        button {
            background-color: #004080;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0059b3;
        }
    </style>
</head>
<body>

<h2 style="text-align:center;">تبرير الغياب</h2>

<form action="enregistrer_justification.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id_abs" value="<?= $id_abs ?>">
    
    <label for="cause">سبب الغياب:</label>
    <textarea name="cause" rows="4" required></textarea>

    <label for="justificatif">إرفاق المبرر (صورة أو PDF):</label>
    <input type="file" name="justificatif" accept="image/*,.pdf" required>

    <button type="submit">إرسال</button>
</form>

</body>
</html>
