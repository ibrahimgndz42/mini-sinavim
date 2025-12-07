<?php include "menu.php"; ?>


<!DOCTYPE HTML>
<html>
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Welcome Page</title>
        <link rel="stylesheet" type="text/css"  href="style.css">
 
</head>
<body>
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