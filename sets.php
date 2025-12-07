<?php
include "connectDB.php";
include "menu.php";

// Setleri ve oluşturan kullanıcı adlarını çekiyoruz
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

$sql = "SELECT 
            sets.set_id, 
            sets.title, 
            sets.description, 
            categories.name AS category,
            sets.created_at, 
            users.username,
            (SELECT COUNT(*) FROM cards WHERE cards.set_id = sets.set_id) AS card_count
        FROM sets
        JOIN users ON sets.user_id = users.user_id
        JOIN categories ON sets.category_id = categories.category_id";



if ($category_filter) {
    $safe_filter = $conn->real_escape_string($category_filter);
    $sql .= " WHERE categories.name = '$safe_filter'";
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
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #8EC5FC, #E0C3FC);
            height: 100vh;
        }
        .category-menu {
            margin-top: 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
        }

        .category-menu a {
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 20px;
            text-decoration: none;
            color: #333;
            font-size: 14px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            transition: background .2s, transform .2s;
        }

        .category-menu a:hover {
            background: white;
            transform: translateY(-2px);
        }

        /* Aktif kategori */
        .category-active {
            background: #6A5ACD !important;
            color: white !important;
            font-weight: bold;
        }

        .sets-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .set-card {
            background: rgba(255,255,255,0.85);
            border-radius: 16px;
            padding: 20px;
            width: 300px;
            backdrop-filter: blur(6px);
            border: 1px solid rgba(255,255,255,0.4);
            text-decoration: none;
            color: #222;
            display: flex;
            flex-direction: column;
            gap: 12px;
            transition: transform .2s, box-shadow .2s;
        }

        .set-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .set-title {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
        }


        .set-card h3 {
            font-size: 22px;
            font-weight: 700;
            color: #2e2e2e;
            margin: 0 0 10px 0;
        }

        .term-badge {
            align-self: flex-start;
            background: #6A5ACD;
            padding: 5px 12px;
            color: white;
            font-size: 13px;
            border-radius: 12px;
            font-weight: 600;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #555;
        }

        .creator {
            font-weight: 600;
        }

        .date {
            opacity: .8;
        }

        .set-card .meta {
            font-size: 13px;
            color: #555;
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        .hero h1 {
            font-size: 44px;
            font-weight: 800;
            color: white;
            text-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }

        .hero p {
            color: white;
            opacity: 0.9;
            margin-top: 5px;
            font-size: 18px;
        }
        .hero {
            padding: 30px;
            backdrop-filter: blur(8px);
            text-align: center; 
        }


    </style>
</head>
<body>

    <div class="hero" style="margin-top: 50px; margin-bottom: 30px;">
        <h1 class="title" style="font-size: 40px;">Çalışma Setleri</h1>
        <p class="subtitle" style="opacity: 1; animation: none;">Tüm kullanıcıların oluşturduğu setleri keşfet</p>
        
        <?php
        // Kategorileri çek
        $catQuery = $conn->query("SELECT name FROM categories ORDER BY category_id ASC");

        ?>
        <div class="category-menu">
            <a href="sets.php" class="<?php echo $category_filter == '' ? 'category-active' : ''; ?>">Tümü</a>

            <?php while($cat = $catQuery->fetch_assoc()): ?>
                <a href="sets.php?category=<?php echo urlencode($cat['name']); ?>"
                class="<?php echo ($category_filter == $cat['name']) ? 'category-active' : ''; ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </a>
            <?php endwhile; ?>
        </div>
    </div>


    <div class="sets-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <a href="view_set.php?id=<?php echo $row['set_id']; ?>" class="set-card">

                    <h3 class="set-title"><?php echo htmlspecialchars($row['title']); ?></h3>

                    <div class="term-badge">
                        <?php echo $row['card_count']; ?> terim
                    </div>

                    <div class="meta-row">
                        <span class="creator">👤 <?php echo htmlspecialchars($row['username']); ?></span>
                        <span class="date"><?php echo date("d.m.Y", strtotime($row['created_at'])); ?></span>
                    </div>

                </a>


            <?php endwhile; ?>
        <?php else: ?>
            <p>Henüz hiç set oluşturulmamış. İlk seti sen oluştur!</p>
        <?php endif; ?>
    </div>

</body>
</html>
