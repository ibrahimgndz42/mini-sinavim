<?php
include "session_check.php"; 
include "connectDB.php";

// Form gönderildiyse
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $set_title = $_POST["set_title"];
    $set_desc  = $_POST["set_desc"];
    $category  = $_POST["category"]; // Kategori al
    $user_id   = $_SESSION["user_id"];

    // --- EN AZ 2 KART ZORUNLULUĞU ---
    $validCardCount = 0;
    foreach ($_POST["term"] as $key => $term_text) {
        $defination_text = $_POST["defination"][$key];

        if (trim($term_text) == "" && trim($defination_text) == "") continue;

        $validCardCount++;
    }

    if ($validCardCount < 2) {
        echo "<p style='color:red;'>En az 2 kart eklemelisiniz!</p>";
        echo "<a href='create_set.php'>Geri dön</a>";
        exit;
    }
    // -----------------------------------


    // 1) Set kaydı
    $sql = "INSERT INTO sets (user_id, title, description, category)
            VALUES ('$user_id', '$set_title', '$set_desc', '$category')";
    $conn->query($sql);

    $set_id = $conn->insert_id; // yeni oluşan set_id

    // 2) Kartları kaydet
    foreach ($_POST["term"] as $key => $term_text) {
        $defination_text = $_POST["defination"][$key];

        if (trim($term_text) == "" && trim($defination_text) == "") continue;

        $sql = "INSERT INTO cards (set_id, front_text, back_text)
                VALUES ('$set_id', '$term_text', '$defination_text')";
        $conn->query($sql);
    }

    echo "<p>Set başarıyla oluşturuldu!</p>";
    echo "<a href='index.php'>Ana sayfaya dön</a>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Set Oluştur</title>

    <style>
        .card-box {
            border: 1px solid #aaa;
            padding: 10px;
            margin-bottom: 10px;
        }
    </style>

    <script>
    function updateDeleteButtons() {
        let cards = document.querySelectorAll(".card-box");
        let deleteButtons = document.querySelectorAll(".delete-btn");

        if (cards.length <= 2) {
            // 2 kart veya daha az → silme yasak
            deleteButtons.forEach(btn => btn.style.display = "none");
        } else {
            // 3 veya daha fazla kart → silme serbest
            deleteButtons.forEach(btn => btn.style.display = "inline-block");
        }
    }

    // Yeni kart ekle
    function addCardBox() {
        let container = document.getElementById("cardsContainer");

        let box = document.createElement("div");
        box.className = "card-box";

        box.innerHTML = `
            <label>Ön Yüz:</label><br>
            <input type="text" name="term[]" required><br><br>

            <label>Arka Yüz:</label><br>
            <input type="text" name="defination[]" required><br><br>

            <button type="button" class="delete-btn" onclick="deleteCard(this)">
                Kartı Sil
            </button>
        `;

        container.appendChild(box);
        updateDeleteButtons();
    }

    // Kart silme
    function deleteCard(btn) {
        btn.parentElement.remove();
        updateDeleteButtons();
    }

    // İlk yüklemede kontrol çalışsın
    window.onload = updateDeleteButtons;
    </script>


</head>
<body>

<h2>Yeni Set Oluştur</h2>



<form method="POST">

    <label>Set Başlığı:</label><br>
    <input type="text" name="set_title" required><br><br>

    <label>Açıklama (isteğe bağlı):</label><br>
    <textarea name="set_desc"></textarea><br><br>

    <label>Kategori:</label><br>
    <select name="category">
        <option value="Genel">Genel</option>
        <option value="Matematik">Matematik</option>
        <option value="Fen Bilimleri">Fen Bilimleri</option>
        <option value="Yabancı Dil">Yabancı Dil</option>
        <option value="Tarih">Tarih</option>
        <option value="Edebiyat">Edebiyat</option>
        <option value="Yazılım">Yazılım</option>
        <option value="Diğer">Diğer</option>
    </select><br><br>

    <h3>Kartlar</h3>

    <div id="cardsContainer">
        <!-- Kart 1 -->
        <div class="card-box">
            <label>Ön Yüz:</label><br>
            <input type="text" name="term[]" required><br><br>

            <label>Arka Yüz:</label><br>
            <input type="text" name="defination[]" required><br><br>

            <button type="button" class="delete-btn" onclick="deleteCard(this)" style="display:none">
                Kartı Sil
            </button>
        </div>

        <!-- Kart 2 -->
        <div class="card-box">
            <label>Ön Yüz:</label><br>
            <input type="text" name="term[]" required><br><br>

            <label>Arka Yüz:</label><br>
            <input type="text" name="defination[]" required><br><br>

            <button type="button" class="delete-btn" onclick="deleteCard(this)" style="display:none">
                Kartı Sil
            </button>
        </div>
    </div>


    <button type="button" onclick="addCardBox()">+ Kart Ekle</button>
    <br><br>

    <button type="submit">Kaydet</button>
</form>

</body>
</html>
