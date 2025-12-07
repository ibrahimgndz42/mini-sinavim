<?php
include 'connectDB.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, email, password) 
            VALUES ('$username', '$email', '$password')";

    if ($conn->query($sql)) {
        echo "Kayıt başarılı! <a href='login.php'>Giriş yap</a>";
    } else {
        echo "Hata: " . $conn->error;
    }
}
?>


<div class="container">
    <form action="register.php" method="POST">
        <h2>Kayıt Ol</h2>
        <label for="username">E-posta:</label>
        <input type="email" name="email" id="email" placeholder="Email" required>
        <br><br>
        <label for="username">Kullanıcı Adı:</label>
        <input type="text" name="username" id="username" placeholder="Kullanıcı adı" required>
        <br><br>
        <label for="username">Şifre Yeni:</label>
        <input type="password" name="password" id="password" placeholder="Şifre" required>
        <br><br>
        <button type="submit">Kaydol</button>
    </form>
</div>