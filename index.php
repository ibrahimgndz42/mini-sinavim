

<!DOCTYPE HTML>
<html>
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mini Sınavım</title>
        <!--<link rel="stylesheet" type="text/css"  href="style.css"> -->
        <style>
            body {
                margin: 0;
                font-family: 'Inter', sans-serif;
                background: linear-gradient(135deg, #8EC5FC, #E0C3FC);
                height: 100vh;
            }
            .hero {
                text-align: center;
                margin-top: 120px;
            }

            .title {
                font-size: 60px;
                font-weight: 700;
            }

            .subtitle {
                font-size: 22px;
                color: #555;
                margin-top: 10px;
                opacity: 0;
                animation: fade 1s ease forwards;
            }

            @keyframes fade {
                from { opacity: 0; }
                to { opacity: 1; }
            }
        </style>
 
</head>
<body>
    <?php include "menu.php"; ?>
    <center>

        <div class="hero">
            <h1 class="title">Mini Sınavım</h1>
            <p id="changingText" class="subtitle"></p>
        </div>

    </center>
    
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
            textElement.style.opacity = 0;

            setTimeout(() => {
                textElement.textContent = texts[index];
                textElement.style.opacity = 1;
                index = (index + 1) % texts.length;
            }, 300);
        }

        changeText();
        setInterval(changeText, 10000);
    </script>

</body>


</html>