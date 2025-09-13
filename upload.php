<?php
// --- KONFIGURACJA ---
$USERNAME = "admin";       // login do panelu
$PASSWORD = "tajnehaslo";  // zmień na własne, mocne hasło
$uploadDir = __DIR__ . "/uploads/";

// --- PROSTA AUTORYZACJA ---
if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])
    || $_SERVER['PHP_AUTH_USER'] !== $USERNAME
    || $_SERVER['PHP_AUTH_PW'] !== $PASSWORD) {
    
    header('WWW-Authenticate: Basic realm="Panel Upload"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Brak dostępu';
    exit;
}

// --- OBSŁUGA UPLOADU ---
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $target = $uploadDir . basename($_FILES['file']['name']);
    if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
        $msg = "Plik przesłany!";
    } else {
        $msg = "Błąd podczas przesyłania pliku.";
    }
}

// --- OBSŁUGA USUWANIA ---
if (isset($_GET['delete'])) {
    $file = basename($_GET['delete']);
    $target = $uploadDir . $file;
    if (is_file($target)) {
        unlink($target);
        $msg = "Plik $file usunięty.";
    }
}

// --- POBRANIE LISTY PLIKÓW ---
$files = glob($uploadDir . "*.{jpg,jpeg,png,gif,mp4,webm}", GLOB_BRACE);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Panel zarządzania</title>
<style>
body { font-family:sans-serif; margin:20px; }
.msg { color:green; margin-bottom:10px; }
.file-list { margin-top:20px; }
.file-item { margin:5px 0; }
.thumb { max-width:150px; max-height:100px; display:inline-block; margin-right:10px; vertical-align:middle; }
</style>
</head>
<body>
<h1>Panel zarządzania</h1>

<?php if (!empty($msg)) echo "<div class='msg'>$msg</div>"; ?>

<h2>Dodaj plik</h2>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="file" required>
    <button type="submit">Wyślij</button>
</form>

<h2>Aktualne pliki</h2>
<div class="file-list">
<?php foreach($files as $file): 
    $name = basename($file);
    $ext = pathinfo($file, PATHINFO_EXTENSION);
?>
    <div class="file-item">
        <?php if (in_array(strtolower($ext), ['mp4','webm'])): ?>
            🎬 <?php echo $name; ?>
        <?php else: ?>
            <img src="uploads/<?php echo $name; ?>" class="thumb"> <?php echo $name; ?>
        <?php endif; ?>
        [<a href="?delete=<?php echo urlencode($name); ?>" onclick="return confirm('Usunąć plik <?php echo $name; ?>?');">Usuń</a>]
    </div>
<?php endforeach; ?>
</div>

</body>
</html>
