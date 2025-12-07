<?php
session_start();
include "connectDB.php";
include "menu.php";

if (!isset($_GET['id'])) {
    echo "<center><h1>Geçersiz Set ID</h1><a href='sets.php'>Geri Dön</a></center>";
    exit;
}

$set_id = intval($_GET['id']);

// 1. Set bilgilerini çek
$sql_set = "SELECT sets.*, users.username, users.user_id as owner_id 
            FROM sets 
            JOIN users ON sets.user_id = users.user_id 
            WHERE sets.set_id = $set_id";

$result_set = $conn->query($sql_set);

if ($result_set->num_rows == 0) {
    echo "<center><h1>Set bulunamadı</h1><a href='sets.php'>Geri Dön</a></center>";
    exit;
}

$set = $result_set->fetch_assoc();

// (Favori kontrolü kaldırıldı) but user id check stays
$current_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;


// 2. Kartları çek
$sql_cards = "SELECT front_text as term, back_text as defination FROM cards WHERE set_id = $set_id";
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


    <script>
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
