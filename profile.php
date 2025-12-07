<?php
include "session_check.php";
include "connectDB.php";

$user_id = $_SESSION['user_id'];

// Kullanıcı bilgisini çek
$sql_user = "SELECT username, email, created_at FROM users WHERE user_id = $user_id";
$res_user = $conn->query($sql_user);
$user_info = $res_user->fetch_assoc();

// 1. Kendi Setlerim
$sql_my_sets = "SELECT 
                    sets.*, 
                    categories.name AS category
                FROM sets 
                LEFT JOIN categories ON sets.category_id = categories.category_id
                WHERE user_id = $user_id 
                ORDER BY created_at DESC";
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
        /* SIFIRLAMA VE TEMEL AYARLAR */
        * {
            box-sizing: border-box;
        }

        /* MENÜYÜ KAPLAYAN ALAN (Tam Genişlik) */
        .menu-wrapper {
            width: 100%;
            margin-bottom: 30px;
        }

        /* İÇERİĞİ ORTALAYAN KONTEYNER */
        .container {
            width: 100%;
            max-width: 1100px; /* İçerik çok yayılmasın */
            margin: 0 auto;    /* Ortala */
            padding: 0 20px 40px 20px; /* İç boşluklar burada */
        }

        /* CAM GÖRÜNÜMLÜ KART (Ortak Stil) */
        .glass-panel {
            background: rgba(255, 255, 255, 0.35); /* Hafif şeffaf beyaz */
            backdrop-filter: blur(12px); /* Buzlu cam efekti */
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        /* PROFİL KARTI (En Üst) */
        .profile-header {
            text-align: center;
            padding: 40px;
            margin-bottom: 40px;
            color: #fff;
        }

        .profile-header h1 {
            margin: 0 0 10px 0;
            font-size: 32px;
            text-shadow: 0 2px 5px rgba(0,0,0,0.15);
        }

        .profile-header p {
            margin: 5px 0;
            font-size: 16px;
            color: rgba(255, 255, 255, 0.9);
        }

        /* BÖLÜM BAŞLIKLARI */
        .section-title {
            font-size: 24px;
            color: #fff;
            margin-bottom: 20px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between; /* Başlık ve linki iki yana at */
        }

        .section-title a {
            font-size: 14px;
            color: #fff;
            text-decoration: underline;
            font-weight: normal;
        }

        /* IZGARA (GRID) YAPISI */
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); /* Responsive */
            gap: 20px;
            margin-bottom: 50px;
        }

        /* KART STİLLERİ (Set ve Klasörler için) */
        .item-card {
            background: rgba(255, 255, 255, 0.7); /* Biraz daha opak */
            border-radius: 12px;
            padding: 20px;
            text-decoration: none;
            color: #333;
            transition: transform 0.2s, box-shadow 0.2s, background 0.2s;
            border: 1px solid rgba(255, 255, 255, 0.6);
            display: flex;
            flex-direction: column;
            min-height: 150px;
        }

        .item-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }

        /* Kategori Etiketi */
        .category-badge {
            align-self: flex-start;
            background: #8EC5FC;
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0 0 8px 0;
        }

        .card-desc {
            font-size: 13px;
            color: #666;
            line-height: 1.4;
            flex-grow: 1; /* Alanı doldur */
            margin-bottom: 10px;
        }

        .card-footer {
            border-top: 1px solid rgba(0,0,0,0.05);
            padding-top: 10px;
            font-size: 12px;
            color: #888;
            text-align: right;
        }

        /* Klasör İkonu */
        .folder-icon {
            font-size: 40px;
            margin-bottom: 10px;
            display: block;
            text-align: center;
        }

        .folder-card-content {
            text-align: center;
        }

        /* Boş Durum Mesajı */
        .empty-msg {
            grid-column: 1 / -1;
            text-align: center;
            padding: 30px;
            background: rgba(255,255,255,0.2);
            border-radius: 12px;
            color: white;
            font-style: italic;
        }

        /* Yeni Oluştur Butonu */
        .create-btn {
            display: inline-block;
            background: #fff;
            color: #7b68ee;
            padding: 12px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: 0.3s;
        }
        .create-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        }

    </style>
</head>
<body>

    <div class="menu-wrapper">
        <?php include "menu.php"; ?>
    </div>

    <div class="container">
        
        <div class="glass-panel profile-header">
            <h1>Merhaba, <?php echo htmlspecialchars($user_info['username']); ?>!</h1>
            <p>📧 <?php echo htmlspecialchars($user_info['email']); ?></p>
            <p>📅 Üyelik Tarihi: <?php echo date("d.m.Y", strtotime($user_info['created_at'])); ?></p>
        </div>

        <div class="section-title">
            <span>📝 Oluşturduğum Setler</span>
            </div>

        <div class="grid-container">
            <?php if ($res_my_sets->num_rows > 0): ?>
                <?php while($row = $res_my_sets->fetch_assoc()): ?>
                    
                    <a href="view_set.php?id=<?php echo $row['set_id']; ?>" class="item-card">
                        <?php 
                            $cat = !empty($row['category']) ? htmlspecialchars($row['category']) : 'Genel';
                        ?>
                        <span class="category-badge"><?php echo $cat; ?></span>
                        
                        <h3 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                        
                        <div class="card-desc">
                            <?php 
                                $desc = $row['description'];
                                echo htmlspecialchars(mb_strlen($desc) > 50 ? mb_substr($desc, 0, 50) . "..." : $desc); 
                            ?>
                        </div>
                        
                        <div class="card-footer">
                            Senin Setin
                        </div>
                    </a>

                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-msg">Henüz hiç set oluşturmadın.</div>
            <?php endif; ?>
        </div>

        <div class="section-title">
            <span>📂 Klasörlerim</span>
            <a href="folders.php">Yönet & Düzenle</a>
        </div>

        <div class="grid-container">
            <?php if ($res_folders->num_rows > 0): ?>
                <?php while($row = $res_folders->fetch_assoc()): ?>
                    <?php 
                        $f_id = $row['folder_id'];
                        $sql_count = "SELECT COUNT(*) as cnt FROM folder_sets WHERE folder_id = $f_id";
                        $res_count = $conn->query($sql_count);
                        $count = $res_count->fetch_assoc()['cnt'];
                    ?>
                    
                    <a href="view_folder.php?id=<?php echo $row['folder_id']; ?>" class="item-card" style="justify-content: center;">
                        <div class="folder-card-content">
                            <span class="folder-icon">📁</span>
                            <h3 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h3>
                            <span style="font-size:12px; color:#666;"><?php echo $count; ?> set</span>
                        </div>
                    </a>

                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-msg">Henüz klasörün yok.</div>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin-top: 10px;">
            <a href="folders.php" class="create-btn">+ Yeni Klasör Oluştur</a>
        </div>

    </div>

</body>
</html>