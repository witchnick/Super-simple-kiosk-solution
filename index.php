<?php
// Katalog z mediami
$uploadsDir = __DIR__ . "/uploads/";

// Pobranie wszystkich obrazów i filmów
$files = glob($uploadsDir . "*.{jpg,jpeg,png,gif,mp4,webm}", GLOB_BRACE);

// Tablica z URL-ami dla przeglądarki
$media = [];
$serverIP = $_SERVER['SERVER_ADDR']; // adres IP serwera

foreach ($files as $file) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $media[] = [
        'url'  => "http://$serverIP/uploads/" . basename($file),
        'type' => in_array($ext, ['mp4','webm']) ? 'video' : 'image'
    ];
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Slideshow Kiosk</title>
<style>
body { margin:0; background:black; display:flex; justify-content:center; align-items:center; height:100vh; }
img, video { max-width:100%; max-height:100%; }
video { display:none; }
</style>
</head>
<body>
<img id="slideshow-img" style="display:none;">
<video id="slideshow-video" autoplay muted playsinline></video>

<script>
const media = <?php echo json_encode($media); ?>;
let index = 0;

const img = document.getElementById('slideshow-img');
const video = document.getElementById('slideshow-video');

function nextSlide() {
    index = (index + 1) % media.length;
    showSlide();
}

function showSlide() {
    const item = media[index];
    if (!item) return;

    if (item.type === 'image') {
        video.style.display = 'none';
        video.pause();
        img.src = item.url;
        img.style.display = 'block';
        setTimeout(nextSlide, 5000); // czas wyświetlania obrazów w ms
    } else {
        img.style.display = 'none';
        video.src = item.url;
        video.style.display = 'block';
        video.play();
        video.onended = nextSlide; // po zakończeniu filmu
    }
}

// start
showSlide();
</script>
</body>
</html>
