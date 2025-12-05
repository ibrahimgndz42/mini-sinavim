<?php
session_start(); 
?>

<style>
    /* Navigasyon barını esnek kutu (flexbox) yapıyoruz */
    .navbar {
        position: sticky;
        top: 0;
        z-index: 1000;
        display: flex;
        justify-content: space-between; /* İçindeki iki grubu (sol ve sağ) iki uca yaslar */
        align-items: center; /* Dikey olarak ortalar */
        padding: 10px 20px; /* Biraz boşluk verelim */
        background-color: #333; /* Örnek arka plan rengi */
    }

    /* Linklerin görünümü (Opsiyonel süsleme) */
    .navbar a {
        color: white;
        text-decoration: none;
        margin: 0 10px; /* Linkler birbirine yapışmasın */
    }
    .navbar a:hover {
        text-decoration: underline;
    }
</style>

<nav class="navbar">
    <div class="nav-links">
        <a href="index.php">Ana Sayfa</a>
        <a href="sets.php">Tüm Setler</a>
    </div>

    <div class="nav-auth">
        <?php if (isset($_SESSION["user_id"])): ?>
            <a href="create_set.php">Set Oluştur</a>
            <a href="logout.php">Çıkış Yap</a>
        <?php else: ?>
            <a href="login.php">Giriş Yap</a>
            <a href="register.php">Kayıt Ol</a>
        <?php endif; ?>
    </div>
</nav>