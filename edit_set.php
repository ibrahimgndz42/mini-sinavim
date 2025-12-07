<?php
include "session_check.php";
include "connectDB.php";

if (!isset($_GET['id'])) {
    echo "ID gerekli.";
    exit;
}

$set_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// 1. Yetki Kontrolü ve Veri Çekme
$sql = "SELECT * FROM sets WHERE set_id = $set_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "Set bulunamadı.";
    exit;
}

$set = $result->fetch_assoc();

if ($set['user_id'] != $user_id) {
    echo "Bu seti düzenleme yetkiniz yok.";
    exit;
}

// Kartları çek
$sql_cards = "SELECT * FROM cards WHERE set_id = $set_id";
$result_cards = $conn->query($sql_cards);
$cards = [];
while($row = $result_cards->fetch_assoc()) {
    $cards[] = $row;
}


// 2. Form Gönderildiğinde Güncelleme
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $set_title = $_POST["set_title"];
    $set_desc  = $_POST["set_desc"];
    $category  = $_POST["category"];

    // --- EN AZ 2 KART ZORUNLULUĞU ---
    $validCardCount = 0;
    foreach ($_POST["term"] as $key => $term_text) {
        $defination_text = $_POST["defination"][$key];
        if (trim($term_text) == "" && trim($defination_text) == "") continue;
        $validCardCount++;
    }

    if ($validCardCount < 2) {
        $error = "En az 2 kart eklemelisiniz!";
    } else {
        // A) Set bilgilerini güncelle
        $sql_update = "UPDATE sets SET title='$set_title', description='$set_desc', category='$category' WHERE set_id=$set_id";
        $conn->query($sql_update);

        // B) Kartları güncelle (Eskileri sil, yenileri ekle - Basit Yöntem)
        $conn->query("DELETE FROM cards WHERE set_id=$set_id");

        foreach ($_POST["term"] as $key => $term_text) {
            $defination_text = $_POST["defination"][$key];
            if (trim($term_text) == "" && trim($defination_text) == "") continue;

            $sql_insert = "INSERT INTO cards (set_id, front_text, back_text) VALUES ('$set_id', '$term_text', '$defination_text')";
            $conn->query($sql_insert);
        }

        echo "<script>alert('Set güncellendi!'); window.location.href='view_set.php?id=$set_id';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Seti Düzenle</title>
    <style>
        .card-box { border: 1px solid #aaa; padding: 10px; margin-bottom: 10px; }
        body { font-family: Arial, sans-serif; padding: 20px; }
    </style>
    <script>
    function updateDeleteButtons() {
        let cards = document.querySelectorAll(".card-box");
        let deleteButtons = document.querySelectorAll(".delete-btn");
        if (cards.length <= 2) {
            deleteButtons.forEach(btn => btn.style.display = "none");
        } else {
            deleteButtons.forEach(btn => btn.style.display = "inline-block");
        }
    }
    function addCardBox() {
        let container = document.getElementById("cardsContainer");
        let box = document.createElement("div");
        box.className = "card-box";
        box.innerHTML = `
            <label>Ön Yüz:</label><br>
            <input type="text" name="term[]" required><br><br>
            <label>Arka Yüz:</label><br>
            <input type="text" name="defination[]" required><br><br>
            <button type="button" class="delete-btn" onclick="deleteCard(this)">Kartı Sil</button>
        `;
        container.appendChild(box);
        updateDeleteButtons();
    }
    function deleteCard(btn) {
        btn.parentElement.remove();
        updateDeleteButtons();
    }
    window.onload = updateDeleteButtons;
    </script>
</head>
<body>

<h2>Seti Düzenle</h2>

<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="POST">
    <label>Set Başlığı:</label><br>
    <input type="text" name="set_title" value="<?php echo htmlspecialchars($set['title']); ?>" required><br><br>

    <label>Açıklama:</label><br>
    <textarea name="set_desc"><?php echo htmlspecialchars($set['description']); ?></textarea><br><br>

    <label>Kategori:</label><br>
    <select name="category">
        <?php 
        $cats = ["Genel", "Matematik", "Fen Bilimleri", "Yabancı Dil", "Tarih", "Edebiyat", "Yazılım", "Diğer"];
        foreach($cats as $cat) {
            $selected = ($set['category'] == $cat) ? 'selected' : '';
            echo "<option value='$cat' $selected>$cat</option>";
        }
        ?>
    </select><br><br>

    <h3>Kartlar</h3>
    <div id="cardsContainer">
        <?php foreach($cards as $card): ?>
        <div class="card-box">
            <label>Ön Yüz:</label><br>
            <input type="text" name="term[]" value="<?php echo htmlspecialchars($card['front_text']); ?>" required><br><br>

            <label>Arka Yüz:</label><br>
            <input type="text" name="defination[]" value="<?php echo htmlspecialchars($card['back_text']); ?>" required><br><br>

            <button type="button" class="delete-btn" onclick="deleteCard(this)">Kartı Sil</button>
        </div>
        <?php endforeach; ?>
    </div>

    <button type="button" onclick="addCardBox()">+ Kart Ekle</button>
    <br><br>
    <button type="submit">Güncelle</button>
    <a href="view_set.php?id=<?php echo $set_id; ?>">İptal</a>
</form>

</body>
</html>
