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

// 2. Klasörlerim
$sql_folders = "SELECT * FROM folders WHERE user_id = $user_id ORDER BY created_at DESC";
$res_folders = $conn->query($sql_folders);
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

            
    .sets-container {
        /* Set kartlarını yan yana (grid) veya esnek (flex) bir düzende gösterir. */
        display: flex;
        flex-wrap: wrap;
        /* Kartlar sığmazsa alt satıra geçsin */
        gap: 20px;
        /* Kartlar arasındaki boşluk */
        justify-content: center;
        /* Kartları ortalamak için */
        padding: 20px;
        max-width: 1200px;
        /* Maksimum genişlik */
        margin: 0 auto;
        /* Ortalamak için */
    }

    .set-card {
        /* Bir kartın temel görünümü */
        display: flex;
        flex-direction: column;
        width: 300px;
        /* Kart genişliği */
        background-color: #ffffff;
        /* Beyaz arka plan */
        border: 1px solid #e0e0e0;
        /* Hafif kenarlık */
        border-radius: 8px;
        /* Yuvarlak köşeler */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        /* Hafif gölge */
        padding: 15px;
        text-decoration: none;
        /* Link alt çizgisini kaldır */
        color: #333;
        /* Metin rengi */
        transition: transform 0.2s, box-shadow 0.2s;
        min-height: 150px;
        /* Kartın minimum yüksekliği */
    }

    .set-card:hover {
        transform: translateY(-5px);
        /* Üzerine gelindiğinde hafifçe yukarı kaydır */
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        /* Gölgeyi belirginleştir */
    }

    .set-card h3 {
        margin-top: 5px;
        margin-bottom: 10px;
        font-size: 1.2em;
        color: #007bff;
        /* Başlık rengi */
    }

    .set-card .desc {
        font-size: 0.9em;
        color: #666;
        flex-grow: 1;
        /* Açıklama alanının esnemesini sağlar */
    }

    .set-card .meta {
        margin-top: 10px;
        font-size: 0.8em;
        color: #999;
        text-align: right;
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
                    <?php 
                    $category_text = isset($row['category']) && $row['category'] !== null && $row['category'] !== '' ? htmlspecialchars($row['category']) : 'Kategori Yok';
                    ?>
                    <div style="background: #eef; padding: 2px 8px; border-radius: 4px; font-size: 12px; align-self: flex-start; margin-bottom: 5px;">
                        <?php echo $category_text; ?>
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

    <!-- KLASÖRLERİM -->
    <h2 class="section-title">Klasörlerim</h2>
    <div class="folder-list" style="max-width: 800px; margin: 0 auto;">
        <?php if ($res_folders->num_rows > 0): ?>
            <?php while($row = $res_folders->fetch_assoc()): ?>
                <?php 
                    $f_id = $row['folder_id'];
                    $sql_count = "SELECT COUNT(*) as cnt FROM folder_sets WHERE folder_id = $f_id";
                    $res_count = $conn->query($sql_count);
                    $count = $res_count->fetch_assoc()['cnt'];
                ?>
                <div class="folder-card" style="background: #fff; border: 1px solid #ddd; padding: 15px; margin-bottom: 10px; border-radius: 5px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 style="margin: 0;">📁 <a href="view_folder.php?id=<?php echo $row['folder_id']; ?>" style="text-decoration: none; color: #333;"><?php echo htmlspecialchars($row['name']); ?></a></h3>
                        <small><?php echo $count; ?> set</small>
                    </div>
                    <div>
                        <a href="view_folder.php?id=<?php echo $row['folder_id']; ?>" style="text-decoration: none; background: #007bff; color: white; padding: 5px 10px; border-radius: 3px;">Aç</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align: center;">Henüz klasörün yok.</p>
        <?php endif; ?>
    </div>
    
    <div style="text-align: center; margin-top: 20px;">
        <a href="folders.php" class="btn" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">+ Yeni Klasör Oluştur</a>
    </div>

</body>
</html>
