<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE HTML>
<html>
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Welcome Page</title>
        <link rel="stylesheet" type="text/css"  href="style.css">
        <script src="script.js"></script>
</head>
<body>
    <h1>MINI SINAVIM</h1>


</body>


</html>