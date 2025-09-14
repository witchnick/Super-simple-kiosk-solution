<!-- 
Panel zarządzania reklamami i ogłoszeniami 
https://github.com/witchnick/Super-simple-kiosk-solution

14.09.2025
-->
<?php
require_once __DIR__ . '/config.php';
session_start();

$uploadDir = __DIR__ . '/uploads/';
$msg = "";

// Ścieżka do logo
$logoPath = __DIR__ . '/logo.jpg';
$logoUrl  = file_exists($logoPath) ? 'logo.jpg' : null;

// Wylogowanie
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: upload.php");
    exit;
}

// Logowanie
if (!isset($_SESSION['authenticated'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
        if ($_POST['password'] === $UPLOAD_PASSWORD) {
            $_SESSION['authenticated'] = true;
            header("Location: upload.php");
            exit;
        } else {
            $msg = "❌ Nieprawidłowe hasło!";
        }
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo htmlspecialchars($PANEL_TITLE); ?></title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100 flex items-center justify-center h-screen">
        <div class="bg-white shadow-lg rounded-xl p-8 w-96">
            <?php if ($logoUrl): ?>
                <div class="flex justify-center mb-6">
                    <img src="<?php echo $logoUrl; ?>" alt="Logo" class="w-full object-contain">
                </div>
            <?php endif; ?>

            <h1 class="text-3xl font-bold mb-6 text-center">
                <?php echo htmlspecialchars($PANEL_TITLE); ?>
            </h1>

            <?php if (!empty($msg)) echo "<p class='text-red-600 mb-3'>$msg</p>"; ?>
            <form method="post" class="space-y-4">
                <div>
                    <label class="block mb-1 font-medium">Hasło:</label>
                    <input type="password" name="password" required class="w-full border rounded p-2 focus:outline-none focus:ring focus:border-blue-300">
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">Zaloguj</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// ======================
// Użytkownik zalogowany
// ======================

// Upload pliku
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $filename = basename($_FILES['file']['name']);
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if ($_FILES['file']['size'] > $MAX_FILE_SIZE) {
        $msg = "❌ Plik jest za duży! Maksymalnie 30 MB.";
    } elseif (!in_array($ext, $ALLOWED_EXT)) {
        $msg = "❌ Niedozwolony typ pliku. Można wrzucać tylko: " . implode(", ", $ALLOWED_EXT);
    } else {
        $target = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
            $msg = "✅ Plik został przesłany.";
        } else {
            $msg = "❌ Błąd podczas przesyłania pliku.";
        }
    }
}

// Usuwanie pliku
if (isset($_GET['delete'])) {
    $file = basename($_GET['delete']);
    $path = $uploadDir . $file;
    if (is_file($path)) {
        unlink($path);
        $_SESSION['msg'] = "✅ Plik usunięty.";
    }
    header("Location: upload.php");
    exit;
}

// Wiadomość z sesji
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

// Lista plików
$files = array_diff(scandir($uploadDir), ['.', '..']);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($PANEL_TITLE); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto py-10">
        <div class="bg-white shadow-lg rounded-xl p-8">
            <?php if ($logoUrl): ?>
                <div class="flex justify-center mb-6">
                    <img src="<?php echo $logoUrl; ?>" alt="Logo" class="w-full object-contain">
                </div>
            <?php endif; ?>

            <h1 class="text-3xl font-bold mb-6 text-center">
                <?php echo htmlspecialchars($PANEL_TITLE); ?>
            </h1>

            <?php if (!empty($msg)) echo "<p class='mb-4 text-blue-700 font-medium'>$msg</p>"; ?>

            <!-- Formularz uploadu -->
            <form method="post" enctype="multipart/form-data" class="flex items-center space-x-4 mb-8">
                <input type="file" name="file" required class="flex-1 border rounded p-2">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Wyślij</button>
            </form>

            <!-- Lista plików -->
            <h2 class="text-xl font-semibold mb-4">Pliki w katalogu:</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <?php foreach ($files as $file): 
                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    $url = "uploads/" . urlencode($file);
                ?>
                <div class="bg-gray-50 rounded-lg shadow p-3 flex flex-col items-center">
                    <?php if (in_array($ext, ['jpg','jpeg','png','gif','webp'])): ?>
                        <img src="<?php echo $url; ?>" class="max-h-32 object-contain mb-2">
                    <?php elseif (in_array($ext, ['mp4','webm'])): ?>
                        <video src="<?php echo $url; ?>" class="max-h-32 object-contain mb-2" preload="metadata" muted></video>
                    <?php else: ?>
                        <div class="w-32 h-20 bg-gray-300 flex items-center justify-center text-gray-700">?</div>
                    <?php endif; ?>

                    <div class="text-center text-sm break-words">
                        <a href="<?php echo $url; ?>" target="_blank" class="text-blue-600 hover:underline"><?php echo htmlspecialchars($file); ?></a>
                    </div>
                    <a href="?delete=<?php echo urlencode($file); ?>" 
                       onclick="return confirm('Usunąć plik?');"
                       class="mt-2 inline-block bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-sm">
                       Usuń
                    </a>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-6">
                <a href="?logout=1" class="text-gray-600 hover:underline">Wyloguj</a>
            </div>
        </div>
    </div>
</body>
</html>
