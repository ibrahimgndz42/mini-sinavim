<?php
include "session_check.php";
include "connectDB.php";
include "menu.php";

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username']; // Login.php'de session'a username atıyor muyuz? Kontrol etmem lazım. 
// Login.php'ye bakıp username session'da mı diye kontrol etmeliyim ama varsayalım şimdilik. 
// Eğer yoksa DB'den çekeriz.

// Kullanıcı bilgisini çek (Garanti olsun)
$sql_user = "SELECT username, email, created_at FROM users WHERE user_id = $user_id";
$res_user = $conn->query($sql_user);
$user_info = $res_user->fetch_assoc();

// 1. Kendi Setlerim
$sql_my_sets = "SELECT * FROM sets WHERE user_id = $user_id ORDER BY created_at DESC";
$res_my_sets = $conn->query($sql_my_sets);

// 2. Favorilerim
$sql_favs = "SELECT sets.*, users.username as creator_name 
             FROM favorites 
             JOIN sets ON favorites.set_id = sets.set_id 
             JOIN users ON sets.user_id = users.user_id 
             WHERE favorites.user_id = $user_id 
             ORDER BY favorites.created_at DESC";
$res_favs = $conn->query($sql_favs);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Profilim - Mini Sınavım</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .profile-header {
            text-align: center;
            padding: 40px;
            background-color: rgba(255,255,255,0.8);
            margin-bottom: 20px;
        }
        .section-title {
            text-align: center;
            margin-top: 40px;
            font-size: 24px;
            color: #333;
        }
    </style>
</head>
<body>

    <div class="profile-header">
        <h1>Merhaba, <?php echo htmlspecialchars($user_info['username']); ?>!</h1>
        <p>E-posta: <?php echo htmlspecialchars($user_info['email']); ?></p>
        <p>Üyelik Tarihi: <?php echo date("d.m.Y", strtotime($user_info['created_at'])); ?></p>
    </div>

    <!-- KENDİ SETLERİM -->
    <h2 class="section-title">Oluşturduğum Setler</h2>
    <div class="sets-container">
        <?php if ($res_my_sets->num_rows > 0): ?>
            <?php while($row = $res_my_sets->fetch_assoc()): ?>
                <a href="view_set.php?id=<?php echo $row['set_id']; ?>" class="set-card">
                    <div style="background: #eef; padding: 2px 8px; border-radius: 4px; font-size: 12px; align-self: flex-start; margin-bottom: 5px;">
                        <?php echo htmlspecialchars($row['category']); ?>
                    </div>
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <div class="desc">
                        <?php echo htmlspecialchars(substr($row['description'], 0, 80)); ?>...
                    </div>
                    <div class="meta">
                        (Senin Setin)
                    </div>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align: center;">Henüz set oluşturmadın.</p>
        <?php endif; ?>
    </div>

    <!-- FAVORİLERİM -->
    <h2 class="section-title">Favori Setlerim</h2>
    <div class="sets-container">
        <?php if ($res_favs->num_rows > 0): ?>
            <?php while($row = $res_favs->fetch_assoc()): ?>
                <a href="view_set.php?id=<?php echo $row['set_id']; ?>" class="set-card" style="border: 2px solid #FFD700;">
                    <div style="background: #fff8e1; padding: 2px 8px; border-radius: 4px; font-size: 12px; align-self: flex-start; margin-bottom: 5px;">
                        <?php echo htmlspecialchars($row['category']); ?>
                    </div>
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <div class="desc">
                        <?php echo htmlspecialchars(substr($row['description'], 0, 80)); ?>...
                    </div>
                    <div class="meta">
                        Oluşturan: <?php echo htmlspecialchars($row['creator_name']); ?>
                    </div>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align: center;">Henüz favorilere eklediğin bir set yok.</p>
        <?php endif; ?>
    </div>

</body>
</html>
