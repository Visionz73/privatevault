<?php
// public/upload.php
require __DIR__ . '/../config.php';
require __DIR__ . '/../src/controllers/upload.php';

session_start();
require_once __DIR__ . '/../src/lib/auth.php';
requireLogin();

// Load categories from database
$stmt = $pdo->query('SELECT id, name FROM document_categories ORDER BY name');
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Use your existing upload processing: name, category, and file
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title     = trim($_POST['title'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);

    // Basic validation (adjust as needed)
    if (!$title || !$categoryId) {
        $error = 'Titel und Kategorie sind erforderlich.';
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
            // Insert into database with category_id
            $stmt = $pdo->prepare("
                INSERT INTO documents (user_id, title, filename, category_id, upload_date) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            if ($stmt->execute([$_SESSION['user_id'], $title, $filename, $categoryId])) {
                $success = 'Dokument erfolgreich hochgeladen.';
            }
        } else {
            $error = 'Fehler beim Speichern der Datei.';
        }
    }
}
// Define available categories
$categories = ['Verträge', 'Versicherungen', 'Rechnungen', 'Sonstige'];
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Upload | Private Vault</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: 'Inter', sans-serif; }
    /* Mobile adjustments */
    @media (max-width: 768px) {
      main { margin-top: 3.5rem; }
    }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#eef7ff] via-[#f7fbff] to-[#f9fdf2] flex flex-col">
  <?php require_once __DIR__.'/../templates/navbar.php'; ?>
  
  <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
    <div class="bg-white rounded shadow-md p-8 w-full max-w-md">
      <?php if ($error): ?>
        <div class="bg-red-100 text-red-700 p-2 rounded mb-4"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="bg-green-100 text-green-700 p-2 rounded mb-4"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
      <form action="upload.php" method="post" enctype="multipart/form-data">
        <div class="mb-4">
          <label for="title" class="block text-gray-700 mb-2">Titel:</label>
          <input type="text" id="title" name="title" class="w-full px-3 py-2 border rounded" required>
        </div>
        <div class="mb-4">
          <label for="category_id" class="block text-gray-700 mb-2">Kategorie:</label>
          <select id="category_id" name="category_id" class="w-full px-3 py-2 border rounded" required>
            <option value="">Bitte wählen...</option>
            <?php foreach($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
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
  </main>
</body>
</html>
