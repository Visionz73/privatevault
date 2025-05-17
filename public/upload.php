<?php
// public/upload.php
require __DIR__ . '/../config.php';
require __DIR__ . '/../src/controllers/upload.php';

session_start();
require_once __DIR__ . '/../src/lib/auth.php';
requireLogin();

// Define the destination directory for uploaded files. Ensure this directory exists or create it.
$uploadDir = __DIR__ . '/../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Fehler beim Hochladen der Datei.';
    } else {
        // Allowed file MIME types; adjust as needed.
        $allowedTypes = [
          'application/pdf',
          'application/msword',
          'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
          'image/jpeg',
          'image/png'
        ];
        if (!in_array($file['type'], $allowedTypes)) {
            $error = 'Dateityp nicht erlaubt.';
        } else {
            $filename = time() . '_' . basename($file['name']);
            $targetFile = $uploadDir . $filename;
            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                $success = 'Datei erfolgreich hochgeladen.';
            } else {
                $error = 'Fehler beim Speichern der Datei.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Upload | Private Vault</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
  <div class="bg-white rounded shadow-md p-8 w-full max-w-md">
    <h1 class="text-2xl font-bold mb-4">Datei hochladen</h1>
    <?php if ($error): ?>
      <div class="bg-red-100 text-red-700 p-2 rounded mb-4"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="bg-green-100 text-green-700 p-2 rounded mb-4"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <form action="upload.php" method="post" enctype="multipart/form-data">
      <div class="mb-4">
        <label for="file" class="block text-gray-700 mb-2">Datei auswählen:</label>
        <input type="file" id="file" name="file" class="w-full px-3 py-2 border rounded">
      </div>
      <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600">Hochladen</button>
    </form>
  </div>
</body>
</html>
