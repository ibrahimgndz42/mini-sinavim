<?php
include 'connectDB.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO users (username, email, password) 
                VALUES ('$username', '$email', '$password')";
        $conn->query($sql);

        $success = "Kayıt başarılı! Giriş sayfasına yönlendiriliyorsunuz...";

    } catch (mysqli_sql_exception $e) {

        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {

            if (strpos($e->getMessage(), "'$email'") !== false) {
                $error = "Bu e‑posta adresi zaten kullanılıyor!";
            }

            if (strpos($e->getMessage(), "'$username'") !== false) {
                $error = "Bu kullanıcı adı zaten kullanılıyor!";
            }

        } else {
            $error = "Bir hata oluştu: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol</title>

    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #8EC5FC, #E0C3FC);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-card {
            backdrop-filter: blur(15px);
            background: rgba(255, 255, 255, 0.25);
            border-radius: 16px;
            padding: 35px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .login-header h2 {
            margin: 0;
            font-size: 28px;
            color: #fff;
        }

        .login-header p {
            margin-top: 6px;
            color: #f0f0f0;
            font-size: 14px;
        }

        .input-wrapper {
            position: relative;
            margin-bottom: 25px;
        }

        .input-wrapper input {
            width: 100%;
            padding: 14px 12px;
            border: 1px solid rgba(255,255,255,0.4);
            background: rgba(255,255,255,0.15);
            border-radius: 8px;
            font-size: 15px;
            color: #fff;
            outline: none;
        }

        .input-wrapper label {
            position: absolute;
            top: 50%;
            left: 12px;
            transform: translateY(-50%);
            color: #eaeaea;
            pointer-events: none;
            transition: 0.2s ease;
        }

        .input-wrapper input:focus + label,
        .input-wrapper input:not(:placeholder-shown) + label {
            top: -6px;
            font-size: 12px;
            color: #fff;
        }

        .focus-border {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 2px;
            width: 0;
            background: #fff;
            transition: 0.3s;
        }

        .input-wrapper input:focus ~ .focus-border {
            width: 100%;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: #ffffff;
            color: #333;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.3s;
        }

        .login-btn:hover {
            background: #eaeaea;
        }

        .error-msg {
            background: rgba(255, 0, 0, 0.25);
            border: 1px solid rgba(255, 0, 0, 0.4);
            padding: 10px;
            border-radius: 8px;
            color: #ff4d4d;
            text-align: center;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .success-msg {
            background: rgba(0, 255, 150, 0.25);
            border: 1px solid rgba(0, 255, 150, 0.4);
            padding: 10px;
            border-radius: 8px;
            color: #004228ff;
            text-align: center;
            margin-bottom: 15px;
            font-weight: 600;
        }
    </style>
</head>

<body>

<div class="login-container">
    <div class="login-card">

        <?php if(!isset($success)): ?>
        <div class="login-header">
            <h2>Hesap Oluştur</h2>
            <p>Yeni bir hesap oluşturun</p>
        </div>
        <?php endif; ?>

        <?php if(isset($success)): ?>
            <p class="success-msg"><?= $success ?></p>

            <script>
                setTimeout(() => {
                    window.location.href = "login.php";
                }, 3000);
            </script>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <p class="error-msg"><?= $error ?></p>
        <?php endif; ?>

        <?php if(!isset($success)): ?>
        <form action="register.php" method="POST">

            <div class="input-wrapper">
                <input type="email" id="email" name="email" required placeholder=" ">
                <label for="email">E-posta</label>
                <span class="focus-border"></span>
            </div>

            <div class="input-wrapper">
                <input type="text" id="username" name="username" required placeholder=" ">
                <label for="username">Kullanıcı Adı</label>
                <span class="focus-border"></span>
            </div>

            <div class="input-wrapper">
                <input type="password" id="password" name="password" required placeholder=" ">
                <label for="password">Şifre</label>
                <span class="focus-border"></span>
            </div>

            <button type="submit" class="login-btn">Kaydol</button>
        </form>
        <?php endif; ?>

    </div>
</div>


</body>
</html>