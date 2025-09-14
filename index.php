<?php
$uploadsDir = __DIR__ . "/uploads/";
$files = glob($uploadsDir . "*.{jpg,jpeg,png,gif,mp4,webm}", GLOB_BRACE);

$media = [];
$baseURL = "http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . "/";

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
<meta charset="UTF-8">
<title>Kiosk Slideshow</title>
<style>
body { margin:0; background:black; display:flex; justify-content:center; align-items:center; height:100vh; color:white; font-family:sans-serif; }
img, video { max-width:100%; max-height:100%; }
video { display:none; }
</style>
</head>
<body>
<?php if (empty($media)): ?>
    <div id="empty">
        <h2>Brak plików do wyświetlenia</h2>
        <p>Oczekiwanie na nowe treści...</p>
    </div>
    <script>
        // Automatyczne odświeżanie co 10 sekund, gdy nie ma plików
        setTimeout(() => location.reload(), 10000);
    </script>
<?php else: ?>
    <img id="slideshow-img" style="display:none;">
    <video id="slideshow-video" autoplay muted playsinline></video>

    <script>
    const media = <?php echo json_encode($media); ?>;
    let index = 0;

    const img = document.getElementById('slideshow-img');
    const video = document.getElementById('slideshow-video');

    function nextSlide() {
        index++;
        if (index >= media.length) {
            // pełny cykl -> odświeżenie strony, żeby sprawdzić nowe pliki
            location.reload();
        } else {
            showSlide();
        }
    }

    function showSlide() {
        const item = media[index];
        if (!item) return;

        if (item.type === 'image') {
            video.style.display = 'none';
            video.pause();
            img.src = item.url;
            img.style.display = 'block';
            setTimeout(nextSlide, 5000); // obraz 5s
        } else {
            img.style.display = 'none';
            video.src = item.url;
            video.style.display = 'block';
            video.play();
            video.onended = nextSlide;
        }
    }

    // start pokazu
    showSlide();
    </script>
<?php endif; ?>
</body>
</html>
