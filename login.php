<?php
include 'connectDB.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE username='$username'");
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];

            header("Location: index.php");
            exit;
        }
    }

    echo "Kullanıcı adı veya şifre hatalı!";
}
?>

<div class="container">
    <form action="login.php" method="POST">
        <h2>Giriş Yap</h2>
        <label for="username">Kullanıcı Adı:</label>
        <input type="text" name="username" placeholder="Kullanıcı adı" required>
        <br><br>
        <label for="username">Şifre:</label>
        <input type="password" name="password" placeholder="Şifre" required>
        <br><br>
        <button type="submit">Giriş Yap</button>
    </form>
</div>