
<?php
session_start(); //session var mı yok mu kontrolü. session isteyen sayfalara ekle
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
