<?php
session_start();

// تحقق من وجود الأستاذ في الجلسة
if (!isset($_SESSION['id_ens'])) {
    echo "<script>alert('يجب تسجيل الدخول أولاً'); window.location.href='login.html';</script>";
    exit();
}

$id_ens = $_SESSION['id_ens'];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <title>لوحة الأستاذ</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    body {
      font-family: 'Tajawal', sans-serif;
      background-color: #e0f2f1;
      padding: 20px;
      text-align: center;
    }
    .card {
      background: white;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      border-radius: 12px;
      padding: 30px;
      margin: 20px auto;
      width: 300px;
      cursor: pointer;
      transition: background 0.3s ease;
      font-size: 18px;
      color: #00796b;
    }
    .card:hover {
      background: #b2dfdb;
      color: #004d40;
    }
  </style>
</head>
<body>

<h2>مرحباً بك في لوحة الأستاذ</h2>

<div class="card" onclick="window.location.href='emploiprof.php?id_ens=<?php echo $id_ens; ?>'">
  <i class="fas fa-calendar-week"></i>
  جدول التوقيت الخاص بي
</div>

</body>
</html>
