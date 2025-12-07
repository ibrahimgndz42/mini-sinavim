<?php
session_start(); //session var mı yok mu kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
