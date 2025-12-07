<?php
include "connectDB.php";
include "menu.php";

// Setleri ve oluşturan kullanıcı adlarını çekiyoruz
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

$sql = "SELECT sets.set_id, sets.title, sets.description, sets.category, sets.created_at, users.username 
        FROM sets 
        JOIN users ON sets.user_id = users.user_id";

if ($category_filter) {
    $sql .= " WHERE sets.category = '$category_filter'";
}

$sql .= " ORDER BY sets.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Tüm Setler - Mini Sınavım</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="hero" style="margin-top: 50px; margin-bottom: 30px;">
        <h1 class="title" style="font-size: 40px;">Çalışma Setleri</h1>
        <p class="subtitle" style="opacity: 1; animation: none;">Tüm kullanıcıların oluşturduğu setleri keşfet</p>
        
        <div style="margin-top: 20px;">
            <a href="sets.php" style="margin: 0 10px; color: #333;">Tümü</a>
            <a href="sets.php?category=Matematik" style="margin: 0 10px; color: #333;">Matematik</a>
            <a href="sets.php?category=Fen Bilimleri" style="margin: 0 10px; color: #333;">Fen</a>
            <a href="sets.php?category=Yabancı Dil" style="margin: 0 10px; color: #333;">Dil</a>
            <a href="sets.php?category=Yazılım" style="margin: 0 10px; color: #333;">Yazılım</a>
        </div>
    </div>

    <div class="sets-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <a href="view_set.php?id=<?php echo $row['set_id']; ?>" class="set-card">
                    <div style="background: #eee; padding: 2px 8px; border-radius: 4px; font-size: 12px; align-self: flex-start; margin-bottom: 5px;">
                        <?php echo htmlspecialchars($row['category']); ?>
                    </div>
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    
                    <div class="desc">
                        <?php 
                        // Açıklama uzunsa kısalt
                        $desc = htmlspecialchars($row['description']);
                        if (strlen($desc) > 100) {
                            $desc = substr($desc, 0, 100) . "...";
                        }
                        echo $desc ? $desc : "<i>Açıklama yok</i>";
                        ?>
                    </div>

                    <div class="meta">
                        Oluşturan: <b><?php echo htmlspecialchars($row['username']); ?></b>
                        <br>
                        <?php echo date("d.m.Y", strtotime($row['created_at'])); ?>
                    </div>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Henüz hiç set oluşturulmamış. İlk seti sen oluştur!</p>
        <?php endif; ?>
    </div>

</body>
</html>
