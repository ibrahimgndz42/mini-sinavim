<?php
include "connectDB.php";
include "menu.php";

if (!isset($_GET['id'])) {
    echo "<center><h1>Geçersiz Set ID</h1><a href='sets.php'>Geri Dön</a></center>";
    exit;
}

$set_id = intval($_GET['id']);

// 1. Set bilgilerini çek
$sql_set = "SELECT sets.*, users.username, users.user_id as owner_id, categories.name AS category
            FROM sets 
            JOIN users ON sets.user_id = users.user_id
            LEFT JOIN categories ON sets.category_id = categories.category_id
            WHERE sets.set_id = $set_id";

$result_set = $conn->query($sql_set);

if ($result_set->num_rows == 0) {
    echo "<center><h1>Set bulunamadı</h1><a href='sets.php'>Geri Dön</a></center>";
    exit;
}

$set = $result_set->fetch_assoc();

// Yorum Gönderme İşlemi
$current_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_comment']) && $current_user_id > 0) {
    $comment_text = trim($_POST['comment']);
    if (!empty($comment_text)) {
        $stmt_com = $conn->prepare("INSERT INTO comments (set_id, user_id, comment_text) VALUES (?, ?, ?)");
        $stmt_com->bind_param("iis", $set_id, $current_user_id, $comment_text);
        if ($stmt_com->execute()) {
            header("Location: view_set.php?id=$set_id");
            exit;
        } else {
            echo "<script>alert('Yorum gönderilirken hata oluştu.');</script>";
        }
    }
}

// Yorum Silme İşlemi
if (isset($_GET['delete_comment']) && $current_user_id > 0) {
    $del_id = intval($_GET['delete_comment']);
    $check_owner = $conn->query("SELECT * FROM comments WHERE comment_id = $del_id AND user_id = $current_user_id");
    if ($check_owner->num_rows > 0) {
        $conn->query("DELETE FROM comments WHERE comment_id = $del_id");
        header("Location: view_set.php?id=$set_id");
        exit;
    }
}

// Yorum Güncelleme İşlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_comment_submit']) && $current_user_id > 0) {
    $upd_id = intval($_POST['edit_comment_id']);
    $upd_text = trim($_POST['edit_comment_text']);
    
    $check_owner = $conn->query("SELECT * FROM comments WHERE comment_id = $upd_id AND user_id = $current_user_id");
    if ($check_owner->num_rows > 0 && !empty($upd_text)) {
        $stmt_upd = $conn->prepare("UPDATE comments SET comment_text = ? WHERE comment_id = ?");
        $stmt_upd->bind_param("si", $upd_text, $upd_id);
        $stmt_upd->execute();
        header("Location: view_set.php?id=$set_id");
        exit;
    }
}

// 2. Kartları çek
$sql_cards = "SELECT term , defination FROM cards WHERE set_id = $set_id";
$result_cards = $conn->query($sql_cards);
$cards = [];
while($row = $result_cards->fetch_assoc()) {
    $cards[] = $row;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($set['title']); ?> - Mini Sınavım</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Genel Konteyner ve Glassmorphism */
        .view-wrapper {
            width: 90%;
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            backdrop-filter: blur(15px);
            background: rgba(255, 255, 255, 0.4);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.5);
            animation: fadeIn 0.6s ease;
            box-sizing: border-box; /* Padding'in taşmasını önler */
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .view-wrapper h1 {
            margin-bottom: 10px;
            text-align: center;
            /* TAŞMAYI ENGELLEYEN KODLAR */
            word-wrap: break-word;      /* Eski tarayıcılar için */
            overflow-wrap: break-word;  /* Modern standart */
            word-break: break-word;     /* Uzun kelimeleri satır sonunda kırar */
        }
        
        .view-wrapper p {
            color: #555;
            text-align: center;
            margin-bottom: 20px;
            /* TAŞMAYI ENGELLEYEN KODLAR */
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        /* --- FLASHCARD ALANI (ORTALAMA VE 3D) --- */
        
        /* Kartın içinde durduğu görünmez kutu */
        .flashcard-container {
            display: flex;
            justify-content: center; /* Yatayda ortalar */
            align-items: center;     /* Dikeyde ortalar */
            perspective: 1000px;     /* 3D derinlik efekti */
            margin: 40px 0;          /* Alttan ve üstten boşluk */
        }

        /* Kartın kendisi */
        .flashcard {
            width: 100%;
            max-width: 600px;        /* Kartın maksimum genişliği */
            height: 350px;           /* Kartın yüksekliği */
            position: relative;
            transform-style: preserve-3d; /* Çocuk elementler 3D düzlemde kalsın */
            transition: transform 0.6s cubic-bezier(0.4, 0.2, 0.2, 1); /* Yumuşak dönüş */
            cursor: pointer;
        }

        .flashcard.flipped {
            transform: rotateY(180deg);
        }

        /* Ön ve Arka Yüz Ortak Özellikler */
        .flashcard-face {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden; /* Kart döndüğünde arkasını gizle */
            -webkit-backface-visibility: hidden; /* Safari desteği */
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 24px;
            font-weight: 600;
            padding: 30px;
            box-sizing: border-box;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            overflow-y: auto; /* Dikeyde taşarsa scroll çıkar */
            
            /* Kart içindeki uzun kelimeleri de kırmak için */
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        /* Ön Yüz */
        .flashcard-front {
            background-color: #ffffff;
            color: #333;
            z-index: 2;
        }

        /* Arka Yüz */
        .flashcard-back {
            background-color: #2c3e50; /* Koyu lacivert/gri ton */
            color: #fff;
            transform: rotateY(180deg); /* Başlangıçta arkası dönük olsun */
        }

        /* --- KONTROLLER --- */
        .controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px; /* Butonlar arası boşluk */
            margin-bottom: 40px;
        }

        .controls button {
            padding: 12px 24px;
            font-size: 16px;
            cursor: pointer;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 8px;
            transition: background 0.2s;
        }

        .controls button:hover {
            background-color: #555;
        }

        #cardCounter {
            font-size: 18px;
            font-weight: bold;
            font-family: monospace;
            background: rgba(255,255,255,0.6);
            padding: 5px 10px;
            border-radius: 5px;
        }

        /* --- YORUM ALANI --- */
        .comments-area {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        
        .comment-box {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
            /* Yorumlarda da taşmayı önleyelim */
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
    </style>
</head>
<body>

<div class="view-wrapper">

    <div>
        <h1><?php echo htmlspecialchars($set['title']); ?></h1>
        <p><?php echo nl2br(htmlspecialchars($set['description'])); ?></p>
        
        <div style="text-align: center; margin-bottom: 20px;">
            <small>Kategori: <?php echo htmlspecialchars($set['category']); ?> | Oluşturan: <b><?php echo htmlspecialchars($set['username']); ?></b></small>
            
            <div style="margin-top: 15px;">
                <?php if ($current_user_id > 0): ?>
                    <a href="select_folder.php?set_id=<?php echo $set_id; ?>" style="text-decoration:none; margin-right: 10px; background: #ffc107; color: #333; padding: 5px 10px; border-radius: 5px;">
                        📁 Klasöre Ekle
                    </a>

                    <?php if ($set['owner_id'] == $current_user_id): ?>
                        | <a href="edit_set.php?id=<?php echo $set_id; ?>" style="margin: 0 5px;">✏️ Düzenle</a>
                        | <a href="delete_set.php?id=<?php echo $set_id; ?>" onclick="return confirm('Bu seti silmek istediğine emin misin?');" style="color: red; margin: 0 5px;">🗑️ Sil</a>
                    <?php endif; ?>
                <?php endif; ?>
                | <a href="quiz.php?id=<?php echo $set_id; ?>" style="text-decoration:none; margin-left: 10px; background: #333; color: white; padding: 5px 10px; border-radius: 5px;">🧠 Test Çöz</a>
            </div>
        </div>
    </div>

    <?php if (count($cards) > 0): ?>
        <div class="flashcard-container" onclick="flipCard()">
            <div class="flashcard" id="flashcard">
                <div class="flashcard-face flashcard-front" id="cardFront">
                    </div>
                <div class="flashcard-face flashcard-back" id="cardBack">
                    </div>
            </div>
        </div>

        <div class="controls">
            <button onclick="prevCard()">&#8592; Önceki</button>
            <span id="cardCounter">1 / <?php echo count($cards); ?></span>
            <button onclick="nextCard()">Sonraki &#8594;</button>
        </div>
    <?php else: ?>
        <p style="text-align:center; margin-top:50px;">Bu sette henüz kart yok.</p>
    <?php endif; ?>


    <div class="comments-area">
        <h3>Yorumlar</h3>

        <?php if ($current_user_id > 0): ?>
            <form method="POST" style="margin-bottom: 20px;">
                <textarea name="comment" placeholder="Bu set hakkında bir şeyler yaz..." required style="width: 100%; height: 80px; padding: 10px; border: 1px solid #ddd; border-radius: 8px; resize: vertical;"></textarea>
                <div style="text-align: right; margin-top: 5px;">
                    <button type="submit" name="submit_comment" style="padding: 8px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Yorum Yap</button>
                </div>
            </form>
        <?php else: ?>
            <p><i>Yorum yapmak için <a href="login.php">giriş yapmalısın</a>.</i></p>
        <?php endif; ?>

        <div class="comments-list">
            <?php 
                $sql_comments = "SELECT comments.*, users.username FROM comments 
                                JOIN users ON comments.user_id = users.user_id 
                                WHERE set_id = $set_id ORDER BY created_at DESC";
                $res_comments = $conn->query($sql_comments);
            ?>
            <?php if ($res_comments->num_rows > 0): ?>
                <?php while($com = $res_comments->fetch_assoc()): ?>
                    <div class="comment-box">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                            <div>
                                <strong style="color: #333;"><?php echo htmlspecialchars($com['username']); ?></strong> 
                                <span style="color: #999; font-size: 12px; margin-left: 10px;"><?php echo date("d.m.Y H:i", strtotime($com['created_at'])); ?></span>
                            </div>
                            <?php if ($current_user_id == $com['user_id']): ?>
                                <div style="font-size: 12px;">
                                    <a href="view_set.php?id=<?php echo $set_id; ?>&edit_comment=<?php echo $com['comment_id']; ?>" style="text-decoration: none; color: blue; margin-right: 5px;">✏️ Düzenle</a>
                                    <a href="view_set.php?id=<?php echo $set_id; ?>&delete_comment=<?php echo $com['comment_id']; ?>" onclick="return confirm('Silmek istediğine emin misin?');" style="text-decoration: none; color: red;">🗑️ Sil</a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php 
                        // Yorum Düzenleme Formu
                        if (isset($_GET['edit_comment']) && $_GET['edit_comment'] == $com['comment_id'] && $current_user_id == $com['user_id']): 
                        ?>
                            <form method="POST">
                                <input type="hidden" name="edit_comment_id" value="<?php echo $com['comment_id']; ?>">
                                <textarea name="edit_comment_text" style="width: 100%; height: 60px; padding: 5px;"><?php echo htmlspecialchars($com['comment_text']); ?></textarea>
                                <div style="margin-top: 5px;">
                                    <button type="submit" name="update_comment_submit" style="padding: 4px 10px; background: #28a745; color: white; border: none; border-radius: 3px; cursor: pointer;">Kaydet</button>
                                    <a href="view_set.php?id=<?php echo $set_id; ?>" style="color: #666; margin-left: 10px; font-size: 13px;">İptal</a>
                                </div>
                            </form>
                        <?php else: ?>
                            <p style="margin: 0; color: #555; line-height: 1.5;"><?php echo nl2br(htmlspecialchars($com['comment_text'])); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color: #777; text-align: center;">Henüz hiç yorum yapılmamış. İlk yorumu sen yap!</p>
            <?php endif; ?>
        </div>
    </div> 
</div> 

<script>
    const cards = <?php echo json_encode($cards); ?>;
    
    let currentIndex = 0;
    const flashcard = document.getElementById("flashcard");
    const front = document.getElementById("cardFront");
    const back = document.getElementById("cardBack");
    const counter = document.getElementById("cardCounter");

    function updateCard() {
        if (cards.length === 0) return;
        
        // Animasyonu sıfırla (eğer arka yüz çevriliyse öne döndür)
        flashcard.classList.remove("flipped");
        
        // İçeriği güncelle (yarım saniye bekle ki kart dönerken içerik değişmesin, dönüş bitince değişsin)
        setTimeout(() => {
            front.textContent = cards[currentIndex].term;
            back.textContent = cards[currentIndex].defination;
            counter.textContent = (currentIndex + 1) + " / " + cards.length;
        }, 150); // 150ms gecikme anlık değişimden daha doğal durur
    }

    function flipCard() {
        flashcard.classList.toggle("flipped");
    }

    function nextCard() {
        // Olay yayılımını durdurmaya gerek yok çünkü butonlar kartın dışında
        if (currentIndex < cards.length - 1) {
            currentIndex++;
            updateCard();
        }
    }

    function prevCard() {
        if (currentIndex > 0) {
            currentIndex--;
            updateCard();
        }
    }

    // Başlangıç
    updateCard();
</script>

</body>
</html>