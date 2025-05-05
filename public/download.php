<?php
// public/download.php – sicheres Herunterladen

require_once __DIR__ . '/../src/lib/db.php';
require_once __DIR__ . '/../src/lib/auth.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    exit('Ungültige Dokumenten-ID.');
}

$userId = $_SESSION['user_id'] ?? 0;
$role = $_SESSION['role'] ?? 'user';

// Dokument abrufen (nur eigene Dokumente oder Admins)
$stmt = $pdo->prepare('
  SELECT filename, original_name
    FROM documents
   WHERE id = :id
     AND (user_id = :uid OR :role = "admin")
');
$stmt->execute([
    ':id'   => $id,
    ':uid'  => $userId,
    ':role' => $role
]);

$doc = $stmt->fetch();
if (!$doc) {
    http_response_code(403);
    exit('Kein Zugriff oder Dokument nicht gefunden.');
}

// Pfad zur Datei
$path = __DIR__ . '/../uploads/' . $doc['filename'];
if (!is_file($path)) {
    http_response_code(404);
    exit('Datei fehlt auf dem Server.');
}

// Download starten
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($doc['original_name']) . '"');
header('Content-Length: ' . filesize($path));
readfile($path);
exit;
