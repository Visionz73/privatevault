<?php
// public/upload.php
require __DIR__ . '/../config.php';
require __DIR__ . '/../src/controllers/upload.php';

session_start();
require_once __DIR__ . '/../src/lib/auth.php';
requireLogin();

// Use your existing upload processing: name, category, and file
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve existing form fields
    $name     = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    // ... retrieve any additional fields as needed ...

    // Basic validation (adjust as needed)
    if (!$name || !$category) {
        $error = 'Name und Kategorie sind erforderlich.';
    } elseif (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Bitte wählen Sie eine Datei aus.';
    } else {
        // Set up upload directory (ensure it exists & is writeable)
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                die("Fehler: Upload-Verzeichnis konnte nicht erstellt werden.");
            }
        }
        // Use a unique file name for collision avoidance
        $filename   = time() . '_' . basename($_FILES['file']['name]);
        $targetFile = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
            // Optionally, save upload info to your database here (name, category, file path, etc.)
            $success = 'Datei erfolgreich hochgeladen.';
        } else {
            $error = 'Fehler beim Speichern der Datei.';
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
    <?php if ($error): ?>
      <div class="bg-red-100 text-red-700 p-2 rounded mb-4"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="bg-green-100 text-green-700 p-2 rounded mb-4"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <form action="upload.php" method="post" enctype="multipart/form-data">
      <!-- Existing fields for filtering on profile -->
      <div class="mb-4">
        <label for="name" class="block text-gray-700 mb-2">Name:</label>
        <input type="text" id="name" name="name" class="w-full px-3 py-2 border rounded" required>
      </div>
      <div class="mb-4">
        <label for="category" class="block text-gray-700 mb-2">Kategorie:</label>
        <input type="text" id="category" name="category" class="w-full px-3 py-2 border rounded" required>
      </div>
      <!-- Existing file input -->
      <div class="mb-4">
        <label for="file" class="block text-gray-700 mb-2">Datei auswählen:</label>
        <input type="file" id="file" name="file" class="w-full px-3 py-2 border rounded" required>
      </div>
      <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600">
        Hochladen
      </button>
    </form>
  </div>
</body>
</html>
