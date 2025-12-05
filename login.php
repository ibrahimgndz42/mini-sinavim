<?php
include 'connectDB.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // SQL Injection'a karşı Prepared Statement kullanmak daha güvenlidir (İleride buna geçmeni öneririm)
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

    $error = "Kullanıcı adı veya şifre hatalı!";
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <!--<link rel="stylesheet" type="text/css"  href="style.css"> -->
    <style>
        /* Sayfa Genel Yapısı */
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #8EC5FC, #E0C3FC);
            height: 100vh; 
            display: flex;
            justify-content: center;
            align-items: center;
        }
        /* Beyaz Kutu (Kart) */
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px; /* Köşeleri yuvarlat */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Hafif gölge */
            width: 350px;
            text-align: center;
        }

        /* Başlık */
        .container h2 {
            margin-bottom: 20px;
            color: #333;
        }

        /* Form Elemanları */
        form {
            display: flex;
            flex-direction: column;
            align-items: flex-start; /* Etiketleri sola hizala */
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
            font-size: 14px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box; /* Padding ekleyince kutu taşmasın */
            font-size: 14px;
        }

        input:focus {
            border-color: #007bff; /* Tıklanıldığında mavi çerçeve */
            outline: none;
        }

        /* Buton */
        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff; /* Mavi renk */
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #0056b3; /* Üzerine gelince koyu mavi */
        }

        /* Hata Mesajı */
        .error-msg {
            color: red;
            margin-bottom: 10px;
            font-size: 14px;
        }
    </style> 
</head>
<body>

<div class="container">
    <form action="login.php" method="POST">
        <h2>Giriş Yap</h2>

        <?php if(isset($error)): ?>
            <p class="error-msg"><?php echo $error; ?></p>
        <?php endif; ?>

        <label for="username">Kullanıcı Adı:</label>
        <input type="text" id="username" name="username" placeholder="Kullanıcı adınızı girin" required>
        
        <label for="password">Şifre:</label>
        <input type="password" id="password" name="password" placeholder="Şifrenizi girin" required>
        
        <button type="submit">Giriş Yap</button>
    </form>
</div>

</body>
</html>