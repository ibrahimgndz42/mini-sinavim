<?php
session_start();
include "connectDB.php";
include "menu.php";
include "session_check.php";

$user_id = $_SESSION['user_id'];

// Yeni klasör oluşturma işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_folder'])) {
    $folder_name = trim($_POST['folder_name']);
    if (!empty($folder_name)) {
        $stmt = $conn->prepare("INSERT INTO folders (user_id, name) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $folder_name);
        if ($stmt->execute()) {
            echo "<script>alert('Klasör başarıyla oluşturuldu!'); window.location.href='folders.php';</script>";
        } else {
            echo "<script>alert('Hata oluştu!');</script>";
        }
    }
}

// Klasörleri çek
$sql_folders = "SELECT * FROM folders WHERE user_id = $user_id ORDER BY created_at DESC";
$result = $conn->query($sql_folders);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Klasörlerim - Mini Sınavım</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" style="max-width: 800px; margin: 40px auto; padding: 20px;">
        <h1 style="text-align: center;">Klasörlerim</h1>
        
        <!-- Klasör Oluşturma Formu -->
        <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
            <h3>Yeni Klasör Oluştur</h3>
            <form method="POST">
                <input type="text" name="folder_name" placeholder="Klasör Adı (Örn: Matematik)" required style="width: 70%; padding: 10px;">
                <button type="submit" name="create_folder" style="padding: 10px 20px; background: #28a745; color: white; border: none; cursor: pointer;">+ Oluştur</button>
            </form>
        </div>

        <!-- Klasör Listesi -->
        <div class="folder-list">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <?php 
                        // Klasördeki set sayısını bul
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
                <p>Henüz hiç klasörün yok.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
