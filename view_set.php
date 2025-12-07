<?php
include "session_check.php";
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
            // Sayfayı yenile ki tekrar gönderilmesin
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
    // Kendi yorumu mu kontrol et
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
    
    // Kendi yorumu mu kontrol et
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
</head>
<body>

    <div style="text-align: center; margin-top: 30px;">
        <h1 style="margin-bottom: 10px;"><?php echo htmlspecialchars($set['title']); ?></h1>
        <p style="color: #555;"><?php echo nl2br(htmlspecialchars($set['description'])); ?></p>
        <small>Kategori: <?php echo htmlspecialchars($set['category']); ?> | Oluşturan: <b><?php echo htmlspecialchars($set['username']); ?></b></small>
        
        <div style="margin-top: 15px;">
            <?php if ($current_user_id > 0): ?>
                
                <!-- Klasöre Ekle Butonu -->
                <a href="select_folder.php?set_id=<?php echo $set_id; ?>" style="text-decoration:none; margin-right: 10px; background: #ffc107; color: #333; padding: 5px 10px; border-radius: 5px;">
                    📁 Klasöre Ekle
                </a>

                <!-- Düzenle / Sil Sadece Sahibi İçin -->
                <?php if ($set['owner_id'] == $current_user_id): ?>
                    | <a href="edit_set.php?id=<?php echo $set_id; ?>" style="margin: 0 5px;">✏️ Düzenle</a>
                    | <a href="delete_set.php?id=<?php echo $set_id; ?>" onclick="return confirm('Bu seti silmek istediğine emin misin?');" style="color: red; margin: 0 5px;">🗑️ Sil</a>
                <?php endif; ?>

            <?php endif; ?>

            <!-- Quiz Butonu Herkese Açık -->
             | <a href="quiz.php?id=<?php echo $set_id; ?>" style="text-decoration:none; margin-left: 10px; background: #333; color: white; padding: 5px 10px; border-radius: 5px;">🧠 Test Çöz</a>
        </div>
    </div>

    <!-- Flashcard Alanı -->
    <?php if (count($cards) > 0): ?>
        <div class="flashcard-container" onclick="flipCard()">
            <div class="flashcard" id="flashcard">
                <div class="flashcard-face flashcard-front" id="cardFront">
                    <!-- JS ile dolacak -->
                </div>
                <div class="flashcard-face flashcard-back" id="cardBack">
                    <!-- JS ile dolacak -->
                </div>
            </div>
        </div>

        <div class="controls">
            <button onclick="prevCard()">&#8592; Önceki</button>
            <span id="cardCounter" style="margin: 0 15px; font-weight: bold;">1 / <?php echo count($cards); ?></span>
            <button onclick="nextCard()">Sonraki &#8594;</button>
        </div>

    <?php else: ?>
        <p style="text-align:center; margin-top:50px;">Bu sette henüz kart yok.</p>
    <?php endif; ?>

    <!-- Terim Listesi -->
    
    <div style="max-width: 600px; margin: 40px auto; padding: 20px; background: #f9f9f9; border-radius: 8px;">
        <h3>Yorumlar</h3>

        <!-- Yorum Ekleme Formu -->
        <?php if ($current_user_id > 0): ?>
            <form method="POST" style="margin-bottom: 20px;">
                <textarea name="comment" placeholder="Bu set hakkında bir şeyler yaz..." required style="width: 100%; height: 80px; padding: 10px; border: 1px solid #ccc; border-radius: 4px;"></textarea>
                <div style="text-align: right; margin-top: 5px;">
                    <button type="submit" name="submit_comment" style="padding: 8px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Yorum Yap</button>
                </div>
            </form>
        <?php else: ?>
            <p><i>Yorum yapmak için <a href="login.php">giriş yapmalısın</a>.</i></p>
        <?php endif; ?>

        <!-- Yorumları Listele -->
        <div class="comments-list">
            <?php 
                $sql_comments = "SELECT comments.*, users.username FROM comments 
                                 JOIN users ON comments.user_id = users.user_id 
                                 WHERE set_id = $set_id ORDER BY created_at DESC";
                $res_comments = $conn->query($sql_comments);
            ?>
            <?php if ($res_comments->num_rows > 0): ?>
                <?php while($com = $res_comments->fetch_assoc()): ?>
                    <div style="border-bottom: 1px solid #eee; padding: 10px 0;">
                        <div style="display: flex; justify-content: space-between;">
                            <div>
                                <strong><?php echo htmlspecialchars($com['username']); ?></strong> 
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
                        // Eğer düzenleme modundaysa formu göster
                        if (isset($_GET['edit_comment']) && $_GET['edit_comment'] == $com['comment_id'] && $current_user_id == $com['user_id']): 
                        ?>
                            <form method="POST" style="margin-top: 5px;">
                                <input type="hidden" name="edit_comment_id" value="<?php echo $com['comment_id']; ?>">
                                <textarea name="edit_comment_text" style="width: 100%; height: 60px; padding: 5px;"><?php echo htmlspecialchars($com['comment_text']); ?></textarea>
                                <div style="margin-top: 5px;">
                                    <button type="submit" name="update_comment_submit" style="padding: 4px 10px; background: #28a745; color: white; border: none; border-radius: 3px; cursor: pointer;">Kaydet</button>
                                    <a href="view_set.php?id=<?php echo $set_id; ?>" style="color: #666; margin-left: 10px; font-size: 13px;">İptal</a>
                                </div>
                            </form>
                        <?php else: ?>
                            <p style="margin: 5px 0 0; color: #333;"><?php echo nl2br(htmlspecialchars($com['comment_text'])); ?></p>
                        <?php endif; ?>
                        
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color: #777;">Henüz hiç yorum yapılmamış. İlk yorumu sen yap!</p>
            <?php endif; ?>
        </div>
    </div>    <script>
        // PHP dizisini JS'ye aktar
        const cards = <?php echo json_encode($cards); ?>;
        
        let currentIndex = 0;
        const flashcard = document.getElementById("flashcard");
        const front = document.getElementById("cardFront");
        const back = document.getElementById("cardBack");
        const counter = document.getElementById("cardCounter");

        function updateCard() {
            if (cards.length === 0) return;
            
            // Animasyonu sıfırla (eğer dönmüşse düzelt)
            flashcard.classList.remove("flipped");
            
            // İçeriği güncelle (hafif gecikme ile görsel geçiş daha iyi olur ama basit tutuyoruz)
            setTimeout(() => {
                front.textContent = cards[currentIndex].term;
                back.textContent = cards[currentIndex].defination;
                counter.textContent = (currentIndex + 1) + " / " + cards.length;
            }, 150);
        }

        function flipCard() {
            flashcard.classList.toggle("flipped");
        }

        function nextCard() {
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
