<?php include "menu.php"; ?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Sınavım - Hoşgeldiniz</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* FIX: Sayfalar arası geçişte menü zıplamasını önlemek için */
        html {
            overflow-y: scroll; /* Kaydırma çubuğu alanını her zaman rezerve et */
        }

        /* FIX: Tüm elemanların boyut hesaplamasını standartlaştırır */
        * {
            box-sizing: border-box;
        }

        /* Genel Sayfa Yapısı */


        /* Menünün genişliğini garantiye alalım */
        body > nav, .menu-container {
            width: 100%;
        }

        /* İçeriği Ortalamak için Kapsayıcı */
        .hero-container {
            flex: 1; /* Kalan boşluğu doldur */
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            width: 100%;
        }

        /* Buzlu Cam Kart Tasarımı */
        .glass-card {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(15px); /* Buzlu cam efekti */
            -webkit-backdrop-filter: blur(15px); /* Safari desteği */
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 50px 40px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            animation: floatIn 1s ease-out;
        }

        /* Başlık Stili */
        .title {
            font-size: 3rem;
            font-weight: 700;
            color: #fff;
            margin: 0 0 10px 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            letter-spacing: -1px;
        }

        /* Değişen Alt Başlık Stili */
        .subtitle {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 40px;
            min-height: 30px; /* Metin değişirken zıplamayı önler */
            font-weight: 400;
            transition: opacity 0.5s ease-in-out; /* Yumuşak geçiş efekti */
        }

        /* Buton Grubu */
        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 30px;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }

        /* Beyaz Buton */
        .btn-white {
            background-color: #fff;
            color: #7b68ee; /* Tema rengine uygun morumsu */
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .btn-white:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            background-color: #f8f9fa;
        }

        /* Şeffaf/Outline Buton */
        .btn-outline {
            background: rgba(255,255,255,0.1);
            border: 2px solid rgba(255,255,255,0.6);
            color: #fff;
        }

        .btn-outline:hover {
            background: rgba(255,255,255,0.3);
            border-color: #fff;
            transform: translateY(-3px);
        }

        /* Animasyonlar */
        @keyframes floatIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Mobilde Responsive Ayarlar */
        @media (max-width: 480px) {
            .title { font-size: 2.2rem; }
            .subtitle { font-size: 1rem; }
            .glass-card { padding: 30px 20px; }
        }
    </style>
</head>
<body>

    <div class="hero-container">
        <div class="glass-card">
            <h1 class="title">Mini Sınavım</h1>
            <p id="changingText" class="subtitle">Yükleniyor...</p>

            <div class="btn-group">
                <a href="sets.php" class="btn btn-white">Setleri Keşfet</a>
                <a href="create_set.php" class="btn btn-outline">Set Oluştur</a>
            </div>
        </div>
    </div>

    <script>
        const texts = [
            "Kendi çalışma setlerini oluştur.",
            "Öğrenmeni hızlandır.",
            "Bilgini test et.",
            "Başarıya hazırlan."  
        ];

        let index = 0;
        const textElement = document.getElementById("changingText");

        function changeText() {
            // Önce yazıyı görünmez yap (fade out)
            textElement.style.opacity = 0;

            setTimeout(() => {
                // Yazıyı değiştir
                textElement.textContent = texts[index];
                // Tekrar görünür yap (fade in)
                textElement.style.opacity = 1;
                // Sıradaki index'e geç
                index = (index + 1) % texts.length;
            }, 500); 
        }

        // İlk açılışta hemen çalıştır
        changeText();
        // Her 4 saniyede bir değiştir
        setInterval(changeText, 4000);
    </script>

</body>
</html>