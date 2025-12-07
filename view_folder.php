<?php
session_start();
include "connectDB.php";
include "menu.php";
include "session_check.php";

if (!isset($_GET['id'])) {
    header("Location: folders.php");
    exit;
}

$folder_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Klasör bilgisini çek (Sadece kendi klasörünü görebilir)
$sql_folder = "SELECT * FROM folders WHERE folder_id = $folder_id AND user_id = $user_id";
$res_folder = $conn->query($sql_folder);

if ($res_folder->num_rows == 0) {
    echo "<center><h1>Klasör bulunamadı veya erişim reddedildi.</h1><a href='folders.php'>Geri Dön</a></center>";
    exit;
}

$folder = $res_folder->fetch_assoc();

// Klasörü Silme işlemi
if (isset($_GET['delete']) && $_GET['delete'] == 'true') {
    $del_sql = "DELETE FROM folders WHERE folder_id = $folder_id";
    if ($conn->query($del_sql)) {
        echo "<script>alert('Klasör silindi!'); window.location.href='folders.php';</script>";
        exit;
    }
}

// Seti Klasörden Çıkarma İşlemi
if (isset($_GET['remove_set'])) {
    $remove_set_id = intval($_GET['remove_set']);
    $sql_remove = "DELETE FROM folder_sets WHERE folder_id = $folder_id AND set_id = $remove_set_id";
    $conn->query($sql_remove);
    header("Location: view_folder.php?id=$folder_id");
    exit;
}

// Klasördeki setleri çek
$sql_sets = "SELECT sets.*, users.username, folder_sets.added_at 
             FROM folder_sets 
             JOIN sets ON folder_sets.set_id = sets.set_id 
             JOIN users ON sets.user_id = users.user_id 
             WHERE folder_sets.folder_id = $folder_id 
             ORDER BY folder_sets.added_at DESC";
$res_sets = $conn->query($sql_sets);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($folder['name']); ?> - Klasör Detayı</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" style="max-width: 900px; margin: 40px auto; padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 20px;">
            <div>
                <h1>📁 <?php echo htmlspecialchars($folder['name']); ?></h1>
                <a href="folders.php">← Klasörlere Dön</a>
            </div>
            <a href="view_folder.php?id=<?php echo $folder_id; ?>&delete=true" onclick="return confirm('Bu klasörü ve içindeki bağlantıları silmek istediğine emin misin? (Setlerin kendisi silinmez)');" style="background: red; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none;">Klasörü Sil</a>
        </div>

        <div class="sets-container">
            <?php if ($res_sets->num_rows > 0): ?>
                <?php while($row = $res_sets->fetch_assoc()): ?>
                    <div class="set-card" style="position: relative;">
                        <a href="view_set.php?id=<?php echo $row['set_id']; ?>" style="text-decoration: none; color: inherit; display: block;">
                            <div style="background: #eef; padding: 2px 8px; border-radius: 4px; font-size: 12px; align-self: flex-start; margin-bottom: 5px;">
                                <?php echo htmlspecialchars($row['category']); ?>
                            </div>
                            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                            <div class="desc">
                                <?php echo htmlspecialchars(substr($row['description'], 0, 80)); ?>...
                            </div>
                            <div class="meta">
                                Oluşturan: <?php echo htmlspecialchars($row['username']); ?>
                            </div>
                        </a>
                        <a href="view_folder.php?id=<?php echo $folder_id; ?>&remove_set=<?php echo $row['set_id']; ?>" onclick="return confirm('Bu seti klasörden çıkarmak istiyor musun?');" style="position: absolute; top: 10px; right: 10px; background: #fff; border: 1px solid #ccc; padding: 2px 6px; border-radius: 4px; text-decoration: none; font-size: 12px;">❌ Çıkar</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Bu klasörde henüz hiç set yok.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
