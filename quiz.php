<?php
include "session_check.php";
include "connectDB.php";
include "menu.php";

if (!isset($_GET['id'])) {
    echo "ID gerekli.";
    exit;
}

$set_id = intval($_GET['id']);

// Set bilgisi
$sql_set = "SELECT title FROM sets WHERE set_id = $set_id";
$res_set = $conn->query($sql_set);
if ($res_set->num_rows == 0) {
    echo "Set bulunamadı.";
    exit;
}
$set = $res_set->fetch_assoc();

// Kartları çek
$sql_cards = "SELECT front_text, back_text FROM cards WHERE set_id = $set_id";
$res_cards = $conn->query($sql_cards);

$cards = [];
while($row = $res_cards->fetch_assoc()) {
    $cards[] = $row;
}

if (count($cards) < 4) {
    echo "<center><h3>Test modu için en az 4 kart gereklidir.</h3><a href='view_set.php?id=$set_id'>Geri Dön</a></center>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Test: <?php echo htmlspecialchars($set['title']); ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .quiz-container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .question {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .options {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .option-btn {
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            background: #f9f9f9;
            cursor: pointer;
            font-size: 18px;
            transition: 0.2s;
        }
        .option-btn:hover {
            background: #eef;
            border-color: #aaf;
        }
        .correct {
            background-color: #c8e6c9 !important; /* Yeşil */
            border-color: #4caf50 !important;
        }
        .wrong {
            background-color: #ffcdd2 !important; /* Kırmızı */
            border-color: #f44336 !important;
        }
        #resultArea {
            display: none;
        }
    </style>
</head>
<body>

<div class="quiz-container" id="quizBox">
    <div style="margin-bottom: 20px; color: #666;">
        Soru <span id="qIndex">1</span> / <span id="qTotal"><?php echo count($cards); ?></span>
    </div>

    <div class="question" id="questionText">Soru Yükleniyor...</div>

    <div class="options" id="optionsBox">
        <!-- Şıklar buraya gelecek -->
    </div>
</div>

<div class="quiz-container" id="resultArea">
    <h2>Test Tamamlandı!</h2>
    <p style="font-size: 20px;">Doğru Sayısı: <b id="scoreVal">0</b> / <?php echo count($cards); ?></p>
    <br>
    <button onclick="location.reload()" style="padding: 10px 20px; cursor: pointer;">Tekrar Çöz</button>
    <a href="view_set.php?id=<?php echo $set_id; ?>" style="margin-left: 20px; text-decoration: none;">Sete Dön</a>
</div>

<script>
    const cards = <?php echo json_encode($cards); ?>;
    let currentQuestion = 0;
    let score = 0;
    
    // Kartları karıştır (Sor sormak için)
    // Fisher-Yates Shuffle
    function shuffle(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
        return array;
    }

    // Soruları hazırla: Her kart bir soru olacak ama sırasını karıştıralım
    let questions = [...cards];
    shuffle(questions);

    const questionText = document.getElementById("questionText");
    const optionsBox = document.getElementById("optionsBox");
    const qIndexSpan = document.getElementById("qIndex");
    const quizBox = document.getElementById("quizBox");
    const resultArea = document.getElementById("resultArea");
    const scoreVal = document.getElementById("scoreVal");

    function loadQuestion() {
        if (currentQuestion >= questions.length) {
            showResult();
            return;
        }

        const q = questions[currentQuestion];
        qIndexSpan.textContent = currentQuestion + 1;
        questionText.textContent = q.front_text;
        
        // Şıkları hazırla
        // Doğru cevap
        let options = [q.back_text];
        
        // Yanlış cevapları havuzdan (diğer kartların tanımlarından) çek
        // Mevcut kart dışındaki tüm tanımları bir listeye al
        let allDefinitions = cards.map(c => c.back_text).filter(d => d !== q.back_text);
        
        // Karıştır ve ilk 3'ünü al
        shuffle(allDefinitions);
        options.push(...allDefinitions.slice(0, 3));
        
        // Şıkları karıştır ki doğru cevap hep en başta olmasın
        shuffle(options);

        optionsBox.innerHTML = "";
        options.forEach(opt => {
            const btn = document.createElement("button");
            btn.className = "option-btn";
            btn.textContent = opt;
            btn.onclick = () => checkAnswer(btn, opt, q.back_text);
            optionsBox.appendChild(btn);
        });
    }

    function checkAnswer(btn, selected, correct) {
        // Tüm butonları devre dışı bırak
        const buttons = document.querySelectorAll(".option-btn");
        buttons.forEach(b => b.disabled = true);

        if (selected === correct) {
            btn.classList.add("correct");
            score++;
        } else {
            btn.classList.add("wrong");
            // Doğruyu göster
            buttons.forEach(b => {
                if (b.textContent === correct) b.classList.add("correct");
            });
        }

        setTimeout(() => {
            currentQuestion++;
            loadQuestion();
        }, 1500);
    }

    function showResult() {
        quizBox.style.display = "none";
        resultArea.style.display = "block";
        scoreVal.textContent = score;
    }

    // Başlat
    loadQuestion();
</script>

</body>
</html>
