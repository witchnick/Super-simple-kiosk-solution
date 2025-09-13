<?php
$uploadsDir = __DIR__ . "/uploads/";
$baseURL = "http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . "/";
$files = glob($uploadsDir . "*.{jpg,jpeg,png,gif,mp4,webm}", GLOB_BRACE);

$media = [];
foreach ($files as $file) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $media[] = [
        'url'  => $baseURL . "uploads/" . basename($file),
        'type' => in_array($ext, ['mp4','webm']) ? 'video' : 'image'
    ];
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Kiosk</title>
<style>
    body { margin:0; background:black; display:flex; justify-content:center; align-items:center; height:100vh; }
    img, video { max-width:100%; max-height:100%; }
    video { display:none; }
</style>
</head>
<body>
    <img id="slideshow-img" style="display:none;">
    <video id="slideshow-video" autoplay muted></video>

<script>
const media = <?php echo json_encode($media); ?>;
let index = 0;

const img = document.getElementById("slideshow-img");
const video = document.getElementById("slideshow-video");

function showNext() {
    const item = media[index];
    if (!item) return;

    if (item.type === "image") {
        video.style.display = "none";
        video.pause();
        img.src = item.url;
        img.style.display = "block";
        setTimeout(nextSlide, 10000); // 10 sekund
    } else {
        img.style.display = "none";
        video.src = item.url;
        video.style.display = "block";
        video.play();
        video.onended = nextSlide; // po zakończeniu filmu
    }
}

function nextSlide() {
    index++;
    if (index >= media.length) {
        // pełny cykl -> odświeżenie strony
        location.reload();
    } else {
        showNext();
    }
}

showNext();
</script>
</body>
</html>
