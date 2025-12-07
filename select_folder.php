<?php
session_start();
include "connectDB.php";
include "menu.php";
include "session_check.php";

if (!isset($_GET['set_id'])) {
    header("Location: sets.php");
    exit;
}

$set_id = intval($_GET['set_id']);
$user_id = $_SESSION['user_id'];

// Set bilgisini çek (Kullanıcıya neyi eklediğini göstermek için)
$sql_set = "SELECT title FROM sets WHERE set_id = $set_id";
$res_set = $conn->query($sql_set);
if ($res_set->num_rows == 0) {
    echo "Set bulunamadı.";
    exit;
}
$set = $res_set->fetch_assoc();

// Ekleme İşlemi
if (isset($_GET['add_to_folder'])) {
    $folder_id = intval($_GET['add_to_folder']);
    
    // Klasörün kullanıcıya ait olduğunu doğrula
    $check_folder = $conn->query("SELECT * FROM folders WHERE folder_id = $folder_id AND user_id = $user_id");
    if ($check_folder->num_rows > 0) {
        $sql_insert = "INSERT IGNORE INTO folder_sets (folder_id, set_id) VALUES ($folder_id, $set_id)";
        if ($conn->query($sql_insert)) {
            echo "<script>alert('Set klasöre eklendi!'); window.location.href='view_set.php?id=$set_id';</script>";
            exit;
        } else {
            echo "Hata: " . $conn->error;
        }
    } else {
        echo "Yetkisiz işlem.";
    }
}

// Kullanıcının klasörlerini çek
$sql_folders = "SELECT * FROM folders WHERE user_id = $user_id ORDER BY created_at DESC";
$res_folders = $conn->query($sql_folders);

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Klasöre Ekle</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" style="max-width: 600px; margin: 40px auto; text-align: center;">
        <h2>"<?php echo htmlspecialchars($set['title']); ?>" setini hangi klasöre eklemek istersin?</h2>
        
        <div class="folder-list" style="margin-top: 20px;">
            <?php if ($res_folders->num_rows > 0): ?>
                <?php while($row = $res_folders->fetch_assoc()): ?>
                    <a href="select_folder.php?set_id=<?php echo $set_id; ?>&add_to_folder=<?php echo $row['folder_id']; ?>" 
                       style="display: block; background: #fff; border: 1px solid #ddd; padding: 15px; margin-bottom: 10px; border-radius: 5px; text-decoration: none; color: #333; font-size: 18px;">
                        📁 <?php echo htmlspecialchars($row['name']); ?>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Henüz hiç klasörün yok. Önce bir klasör oluşturmalısın.</p>
                <a href="folders.php" class="btn" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">+ Yeni Klasör Oluştur</a>
            <?php endif; ?>
        </div>
        
        <br>
        <a href="view_set.php?id=<?php echo $set_id; ?>">İptal</a>
    </div>
</body>
</html>
