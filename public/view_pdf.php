<?php
session_start();
require_once __DIR__ . '/../src/lib/auth.php';
requireLogin();

$doc_id = $_GET['id'] ?? null;
if (!$doc_id) {
    header('Location: /profile.php?tab=documents');
    exit;
}

// Fetch document info
$stmt = $pdo->prepare("SELECT * FROM documents WHERE id = ? AND user_id = ? AND is_deleted = 0");
$stmt->execute([$doc_id, $_SESSION['user_id']]);
$doc = $stmt->fetch();

if (!$doc) {
    header('Location: /profile.php?tab=documents');
    exit;
}

// Set headers for inline PDF display
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $doc['filename'] . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Output the PDF file
readfile(__DIR__ . '/../uploads/' . $doc['filename']);
exit;
?>
<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($doc['title']) ?> | PDF Viewer</title>
    <link rel="stylesheet" href="/privatevault/css/main.css">
</head>
<body class="h-full flex flex-col bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow-sm p-4 flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-900"><?= htmlspecialchars($doc['title']) ?></h1>
        <a href="javascript:history.back()" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 text-gray-600">
            Zurück
        </a>
    </header>
    
    <!-- PDF Viewer -->
    <main class="flex-1 p-4">
        <div class="w-full h-full rounded-lg overflow-hidden shadow-lg">
            <embed src="/uploads/<?= urlencode($doc['filename']) ?>" 
                   type="application/pdf" 
                   width="100%" 
                   height="100%"
                   class="w-full h-full">
        </div>
    </main>
</body>
</html>
