<?php
session_start();

session_unset();

session_destroy();

header("Location: صفحة تسجيل الدخول.html");
exit();
?>
