<?php
include "session_check.php"; 
include "connectDB.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $set_title = $_POST["set_title"];
    $set_desc  = $_POST["set_desc"];
    $category  = $_POST["category"]; // Kategori al
    $user_id   = $_SESSION["user_id"];

    // Minimum 2 kart kontrolü
    $validCardCount = 0;
    foreach ($_POST["term"] as $key => $term) {
        $def = $_POST["defination"][$key];
        if (trim($term) !== "" && trim($def) !== "") {
            $validCardCount++;
        }
    }

    if ($validCardCount < 2) {
        $error = "En az 2 kart eklemelisiniz!";
    } else {
        $sql = "INSERT INTO sets (user_id, title, description, category_id)
                VALUES ('$user_id', '$set_title', '$set_desc', '$set_category')";
        $conn->query($sql);

        $set_id = $conn->insert_id;

        foreach ($_POST["term"] as $key => $term) {
            $def = $_POST["defination"][$key];
            if (trim($term) !== "" && trim($def) !== "") {
                $conn->query("INSERT INTO cards (set_id, term, defination)
                              VALUES ('$set_id', '$term', '$def')");
            }
        }
        $success = "Set başarıyla oluşturuldu! Yönlendiriliyorsunuz...";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Set Oluştur</title>

<style>
    body {
        margin: 0;
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #8EC5FC, #E0C3FC);
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding-top: 40px;
    }
    * {
        box-sizing: border-box;
    }

    .create-container {
        width: 100%;
        max-width: 650px;
        padding: 20px;
    }

    .glass-card { //en dış kart
        backdrop-filter: blur(100px);
        background: rgba(255, 255, 255, 0.10);
        border-radius: 16px;
        padding: 35px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.15);
        border: 1px solid rgba(255,255,255,0.3);
        animation: fadeIn 0.6s ease;
    }

    // 1) Set kaydı
    $sql = "INSERT INTO sets (user_id, title, description, category)
            VALUES ('$user_id', '$set_title', '$set_desc', '$category')";
    $conn->query($sql);
    @keyframes fadeIn {
        from {opacity:0; transform:translateY(20px);}
        to   {opacity:1; transform:translateY(0);}
    }

    h2 {
        text-align: center;
        color: #fff;
        margin-bottom: 20px;
        font-size: 30px;
    }

    textarea.auto-expand {
        overflow: hidden;
        min-height: 40px; /* Başlangıç yüksekliği */
        resize: none;     /* Kullanıcı boyutunu değiştiremesin */
        background: rgba(255,255,255,0.1);
        border-radius: 8px;
        border: 1px solid rgba(255,255,255,0.3);
        color: #fff;
        padding: 14px;
        width: 100%;
        font-size: 15px;
        outline: none;
    }


        $sql = "INSERT INTO cards (set_id, front_text, back_text)
                VALUES ('$set_id', '$term_text', '$defination_text')";
        $conn->query($sql);
    .input-wrapper {
        position: relative;
        margin-bottom: 25px;
    }

    .input-wrapper input,
    .input-wrapper textarea,
    .input-wrapper select {
        width: 100%;
        padding: 14px 14px;
        border: 1px solid rgba(255,255,255,0.3);
        background: rgba(255,255,255,0.10);
        border-radius: 8px;
        font-size: 15px;
        color: #fff;
        outline: none;
        resize: none;
        overflow: hidden;
    }

    .input-wrapper label {
        position: absolute;
        top: 50%;
        left: 12px;
        transform: translateY(-50%);
        color: rgba(255,255,255,0.9);   
        pointer-events: none;
        transition: .2s ease;
    }

    .input-wrapper input:focus + label,
    .input-wrapper input:not(:placeholder-shown) + label,
    .input-wrapper textarea:focus + label,
    .input-wrapper textarea:not(:placeholder-shown) + label,
    .input-wrapper select:focus + label,
    .input-wrapper select:not([value=""]) + label {
        top: -6px;
        font-size: 12px;
        color: #fff;
    }

    .focus-border {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 2px;
        width: 0;
        background: #fff;
        transition: .3s;
    }

    .input-wrapper input:focus ~ .focus-border,
    .input-wrapper textarea:focus ~ .focus-border,
    .input-wrapper select:focus ~ .focus-border {
        width: 100%;
    }

    /* Kart numarası etiketi */
    .card-number {
        position: absolute;
        top: 10px;
        left: 10px;
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
        font-weight: bold;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 14px;
        pointer-events: none;
    }

    .card-box {
        position: relative;
        backdrop-filter: blur(25px);
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.35);
        padding: 60px 22px 22px 22px;
        border-radius: 14px;
        margin-bottom: 18px;
    }

    .custom-select {
        position: relative;
        z-index: 1000;
        cursor: pointer;
        border-radius: 12px;
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.3);
        color: #fff;
        padding: 10px 12px;
        user-select: none;
    }

    .custom-select .selected::after {
        content: "▾";
        float: right;
    }

    .custom-select ul.options {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;

        background: rgba(255,255,255,0.5);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.4);
        color: #474747ff; 
        border-radius: 12px;
        list-style: none;
        padding: 0;
        margin: 5px 0 0 0;
        display: none;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
    }

    .custom-select ul.options li {
        padding: 10px 12px;
        transition: background 0.2s;
    }

    .custom-select ul.options li:hover {
        background: rgba(255,255,255,0.25);
    }


    .delete-btn {
        background: #ff4d4d;
        border: none;
        padding: 10px;
        border-radius: 8px;
        color: white;
        cursor: pointer;
        margin-top: 12px;
    }

    .add-btn, .create-btn, .cancel-btn {
        width: 100%;
        padding: 12px;
        margin-top: 10px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-size: 16px;
    }

    .add-btn { background: #fff; color:#333; }
    .create-btn { background:#fff; font-weight:bold; }
    .cancel-btn { background:#ff4040; color:#fff; }

    .success-msg, .error-msg {
        padding: 12px;
        border-radius: 10px;
        text-align: center;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .success-msg {
        background: rgba(0,255,150,0.25);
        border: 1px solid rgba(0,255,150,0.4);
        color:#003d18;
    }

    .error-msg {
        background: rgba(255,0,0,0.25);
        border: 1px solid rgba(255,0,0,0.4);
        color:#ff1a1a;
    }
</style>
</head>

<body>

<div class="create-container">
<div class="glass-card">

<?php if(!isset($success)): ?>
    <h2>Yeni Set Oluştur</h2>
<?php endif; ?>

<?php if(isset($success)): ?>
    <p class="success-msg"><?= $success ?></p>
    <script>
        setTimeout(()=>{ window.location.href="index.php"; }, 3000);
    </script>
<?php elseif(isset($error)): ?>
    <p class="error-msg"><?= $error ?></p>
<?php endif; ?>

<?php if(!isset($success)): ?>
<form method="POST">

    <div class="input-wrapper">

        <textarea class="auto-expand" rows="1" name="set_title" required  placeholder=" "></textarea>
        <label>Set Başlığı</label>
        <span class="focus-border"></span>
    </div>

    <div class="input-wrapper">
        <textarea class="auto-expand" rows="1" name="set_desc" rows="1" placeholder=" "></textarea>
        <label>Açıklama (İsteğe bağlı)</label>
        <span class="focus-border"></span>
    </div>

    <div class="input-wrapper">
        <div class="custom-select" id="categorySelect">
            <div class="selected">Kategori Seçiniz</div>
            <ul class="options">
                <li data-value="1">Genel</li>
                <li data-value="2">Matematik</li>
                <li data-value="3">Fen Bilimleri</li>
                <li data-value="4">Yabancı Dil</li>
                <li data-value="5">Tarih</li>
                <li data-value="6">Edebiyat</li>
                <li data-value="7">Yazılım</li>
                <li data-value="8">Diğer</li>
            </ul>
        </div>
        <input type="hidden" name="set_category" id="hiddenCategory">
    </div>


    <h3 style="color:white; text-align:center;">Kartlar</h3>

    <div id="cardsContainer">

        <!-- 1. Kart -->
        <div class="card-box">
            <div class="input-wrapper">
                <input type="text" name="term[]" placeholder=" " required>
                <label>Ön Yüz</label>
                <span class="focus-border"></span>
            </div>

            <div class="input-wrapper">
                <input type="text" name="defination[]" placeholder=" " required>
                <label>Arka Yüz</label>
                <span class="focus-border"></span>
            </div>

            <button type="button" class="delete-btn" style="display:none">Kartı Sil</button>
        </div>

        <!-- 2. Kart -->
        <div class="card-box">
            <div class="input-wrapper">
                <input type="text" name="term[]" placeholder=" " required>
                <label>Ön Yüz</label>
                <span class="focus-border"></span>
            </div>

            <div class="input-wrapper">
                <input type="text" name="defination[]" placeholder=" " required>
                <label>Arka Yüz</label>
                <span class="focus-border"></span>
            </div>

            <button type="button" class="delete-btn" style="display:none">Kartı Sil</button>
        </div>

    </div>

    <button type="button" class="add-btn" onclick="addCard()">+ Kart Ekle</button>
    <button type="submit" class="create-btn">Seti Oluştur</button>
    <button type="button" class="cancel-btn" onclick="window.location.href='index.php'">İptal</button>

</form>
<?php endif; ?>
</div></div>

<script>

const categorySelect = document.getElementById("categorySelect");
const selected = categorySelect.querySelector(".selected");
const optionsContainer = categorySelect.querySelector(".options");
const hiddenInput = document.getElementById("hiddenCategory");

selected.addEventListener("click", () => {
    optionsContainer.style.display = optionsContainer.style.display === "block" ? "none" : "block";
});

optionsContainer.querySelectorAll("li").forEach(option => {
    option.addEventListener("click", () => {
        selected.textContent = option.textContent;
        hiddenInput.value = option.dataset.value;
        optionsContainer.style.display = "none";
    });
});

// Sayfa dışında tıklayınca dropdown kapanması
document.addEventListener("click", (e) => {
    if (!categorySelect.contains(e.target)) {
        optionsContainer.style.display = "none";
    }
});


// Kart numaralandırma fonksiyonu
function updateCardNumbers() {
    const cards = document.querySelectorAll("#cardsContainer .card-box");
    cards.forEach((card, index) => {
        let numberLabel = card.querySelector(".card-number");
        if (!numberLabel) {
            numberLabel = document.createElement("div");
            numberLabel.className = "card-number";
            card.appendChild(numberLabel);
        }
        numberLabel.textContent = (index + 1) + ". Kart";
    });
}

function autoExpandTextarea(textarea) {
    textarea.style.height = 'auto'; // Önce yükseklik sıfırlanır
    textarea.style.height = textarea.scrollHeight + 'px'; // İçeriğe göre yükseklik
}

// Sayfadaki tüm auto-expand textarea’lara event ekleyelim
document.querySelectorAll('textarea.auto-expand').forEach(textarea => {
    // Başlangıçta yükseklik ayarla
    autoExpandTextarea(textarea);

    // Yazarken yükseklik değişsin
    textarea.addEventListener('input', () => autoExpandTextarea(textarea));
});


// Kart ekleme fonksiyonu
function addCard() {
    const container = document.getElementById("cardsContainer");

    const box = document.createElement("div");
    box.className = "card-box";

    box.innerHTML = `
        <div class="input-wrapper">
            <input type="text" name="term[]" placeholder=" " required>
            <label>Ön Yüz</label>
            <span class="focus-border"></span>
        </div>

        <div class="input-wrapper">
            <input type="text" name="defination[]" placeholder=" " required>
            <label>Arka Yüz</label>
            <span class="focus-border"></span>
        </div>

        <button type="button" class="delete-btn">Kartı Sil</button>
    `;

    container.appendChild(box);

    // Yeni kart silme butonu listener
    const delBtn = box.querySelector(".delete-btn");
    delBtn.addEventListener("click", function() {
        box.remove();
        checkDeletes();
        updateCardNumbers();
    });

    checkDeletes();
    updateCardNumbers();
}

// Mevcut tüm kartlara silme listener ekle
function attachDeleteListeners() {
    const boxes = document.querySelectorAll("#cardsContainer .card-box");
    boxes.forEach(box => {
        const btn = box.querySelector(".delete-btn");
        if (!btn.dataset.listener) { // aynı listener birden eklenmesin
            btn.addEventListener("click", function() {
                box.remove();
                checkDeletes();
                updateCardNumbers();
            });
            btn.dataset.listener = true;
        }
    });
}

// Silme butonlarını ve görünürlüğü kontrol
function checkDeletes() {
    const boxes = document.querySelectorAll(".card-box");
    const delBtns = document.querySelectorAll(".delete-btn");

    delBtns.forEach(btn => btn.style.display = boxes.length > 2 ? "block" : "none");
}

// Sayfa yüklendiğinde listener ekle ve numaraları güncelle
attachDeleteListeners();
updateCardNumbers();
checkDeletes();
</script>

</body>
</html>
