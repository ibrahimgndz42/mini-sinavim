<?php
include 'connectDB.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

    $error = "Kullanıcı adı veya şifre hatalı!";
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>

    <style>
        @keyframes onAutoFillStart {}

        input:-webkit-autofill {
            animation-name: onAutoFillStart;
        }
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #8EC5FC, #E0C3FC);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Container */
        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        /* Glass Card */
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

        /* Floating Input */
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

        /* Sadece .filled ve focus için label yukarı çıksın */
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

        /* Focus Border Animation */
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

        /* Password Toggle */
        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
        }

        .password-toggle .eye-icon {
            width: 20px;
            height: 20px;
            background: url('https://cdn-icons-png.flaticon.com/512/709/709612.png') no-repeat center;
            background-size: contain;
            filter: invert(1);
        }

        /* Error Message */
        .error-msg {
            color: #ff4d4d;
            margin-bottom: 10px;
            text-align: center;
            font-size: 14px;
        }

        /* Login Button */
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
    </style>
</head>

<body>

<div class="login-container">
    <div class="login-card">

        <div class="login-header">
            <h2>Tekrar Hoş Geldin</h2>
            <p>Hesabına giriş yap</p>
        </div>

        <?php if(isset($error)): ?>
            <p class="error-msg"><?= $error ?></p>
        <?php endif; ?>

       <form action="login.php" method="POST" class="login-form">

            <div class="input-wrapper">
                <input type="text" id="username" name="username" required autocomplete="username" placeholder=" ">
                <label for="username">Kullanıcı Adı</label>
                <span class="focus-border"></span>
            </div>

            <div class="input-wrapper password-wrapper">
                <input type="password" id="password" name="password" required autocomplete="current-password" placeholder=" ">
                <label for="password">Şifre</label>
                <button type="button" class="password-toggle" onclick="togglePassword()">
                    <span class="eye-icon"></span>
                </button>
                <span class="focus-border"></span>
            </div>

            <button type="submit" class="login-btn">Giriş Yap</button>
        </form>

    </div>
</div>

<script>
function togglePassword() {
    const pass = document.getElementById("password");
    pass.type = pass.type === "password" ? "text" : "password";
}

// Auto-fill yakalama + elle yazma kontrolü
function checkFilled(input) {
    if (input.value.trim() !== "") {
        input.classList.add("filled");
    } else {
        input.classList.remove("filled");
    }
}

window.addEventListener("DOMContentLoaded", () => {
    const inputs = document.querySelectorAll(".input-wrapper input");

    inputs.forEach(input => {
        checkFilled(input);

        input.addEventListener("input", () => {
            checkFilled(input);
        });
    });
});

// Auto-fill animasyonunu yakala (Chrome için)
document.addEventListener("animationstart", (e) => {
    if (e.animationName === "onAutoFillStart") {
        e.target.classList.add("filled");
    }
});
</script>

</body>
</html>