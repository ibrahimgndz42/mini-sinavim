<?php
include "session_check.php";  // session yoksa giremez
include "connectDB.php";

// Form gönderildiyse
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $set_title = $_POST["set_title"];
    $set_desc  = $_POST["set_desc"];
    $set_category  = $_POST["set_category"]; 
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
    $sql = "INSERT INTO sets (user_id, title, description, category_id)
            VALUES ('$user_id', '$set_title', '$set_desc', '$set_category')";
    $conn->query($sql);

    $set_id = $conn->insert_id; // yeni oluşan set_id

    // 2) Kartları kaydet
    foreach ($_POST["term"] as $key => $term_text) {
        $defination_text = $_POST["defination"][$key];

        if (trim($term_text) == "" && trim($defination_text) == "") continue;

        $sql = "INSERT INTO cards (set_id, term, defination)
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
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #8EC5FC, #E0C3FC);
            height: 100vh;
        }
        .page-title {
            font-size: 48px;
            font-weight: 700;
            text-align: center;
            color: #1f1f1f;
            margin-bottom: 30px;
            letter-spacing: -0.5px;
            padding-bottom: 10px;
        }
        /* Kart kutusu */
        .card-box {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 12px;
            background-color: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        /* Form genel */
        form {
            max-width: 800px;
            margin: 40px auto;
            background-color: #fefefe;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        /* Başlıklar */
        h2, h3 {
            text-align: center;
            font-weight: 600;
            color: #333;
        }

        /* Etiketler */
        label {
            font-weight: 500;
            display: block;
            margin-bottom: 6px;
            color: #444;
        }

        /* Input ve textarea */
        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 10px 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
            margin-bottom: 16px;
            resize: none; 
            overflow: hidden;
        }

        /* Butonlar */
        button[type="create"],
        button[type="button"] {
            background-color: #4F46E5;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button[type="cancel"] {
            background-color: #d40707ff;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="create"]:hover,
        button[type="button"]:hover {
            background-color: #4338CA;
        }
        button[type="cancel"]:hover {
            background-color: #9e0505ff;
        }


        /* Sil butonu */
        .delete-btn {
            background-color: #EF4444;
            margin-top: 10px;
        }

        .delete-btn:hover {
            background-color: #DC2626;
        }
        .card-header {
            font-weight: 600;
            margin-bottom: 10px;
            color: #4F46E5;
            font-size: 18px;
        }
    </style>

    <
    >
    function autoGrow(textarea) {
        textarea.style.height = "auto";
        textarea.style.height = textarea.scrollHeight + "px";
    }

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
    <div class="container">
        <h2 class="page-title">Yeni Set Oluştur</h2>

        <form method="POST" class="form-box" id="setForm">
            <label>Set Başlığı:</label>
            <input type="text" name="set_title" placeholer="Başlık" required>

            <label>Açıklama (isteğe bağlı):</label>
            <textarea name="set_desc" oninput="autoGrow(this)"
                rows="2" placeholder="Bir açıklama girin..."></textarea>

            <label>Kategori:</label>
            <select name="set_category">
                <option value="1">Genel</option>
                <option value="2">Matematik</option>
                <option value="3">Fen Bilimleri</option>
                <option value="4">Yabancı Dil</option>
                <option value="5">Tarih</option>
                <option value="6">Edebiyat</option>
                <option value="7">Yazılım</option>
                <option value="8">Diğer</option>
            </select>

            <h3>Kartlar</h3>
            <div id="cardsContainer">
                <!-- Kart 1 -->
                <div class="card-box">
                    <div class="card-header">Kart 1</div>
                    <label>Ön Yüz:</label>
                    <input type="text" name="term[]" required>

                    <label>Arka Yüz:</label>
                    <input type="text" name="defination[]" required>

                    <button type="button" class="delete-btn" onclick="deleteCard(this)" style="display:none">
                        Kartı Sil
                    </button>
                </div>

                <!-- Kart 2 -->
                <div class="card-box">
                    <div class="card-header">Kart 2</div>
                    <label>Ön Yüz:</label>
                    <input type="text" name="term[]" required>

                    <label>Arka Yüz:</label>
                    <input type="text" name="defination[]" required>

                    <button type="button" class="delete-btn" onclick="deleteCard(this)" style="display:none">
                        Kartı Sil
                    </button>
                </div>
            </div>

            <button type="button" onclick="addCardBox()">Kart Ekle</button>

            <!-- Alttaki Oluştur Butonu -->
            <div style="text-align:right; margin-top: 30px;">
                <button type="cancel">Vazgeç</button>
                <button type="create">Oluştur</button>
            </div>
        </form>
    </div>

    <script>
        function updateDeleteButtons() {
            let cards = document.querySelectorAll(".card-box");
            let deleteButtons = document.querySelectorAll(".delete-btn");
            let headers = document.querySelectorAll(".card-header");

            // Kart numaralarını güncelle
            headers.forEach((header, index) => {
                header.textContent = "Kart " + (index + 1);
            });

            // Silme butonlarını kontrol et
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
                <div class="card-header"></div>
                <label>Ön Yüz:</label>
                <input type="text" name="term[]" required>

                <label>Arka Yüz:</label>
                <input type="text" name="defination[]" required>

                <button type="button" class="delete-btn" onclick="deleteCard(this)">
                    Kartı Sil
                </button>
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


</body>
</html>
