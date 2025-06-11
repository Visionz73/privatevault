<?php
// public/download.php – sicheres Herunterladen

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/lib/db.php';
require_once __DIR__ . '/../src/lib/auth.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    exit('Ungültige Dokumenten-ID.');
}

$userId = $_SESSION['user_id'] ?? 0;

try {
    // Dokument abrufen (nur eigene Dokumente)
    $stmt = $pdo->prepare('
      SELECT filename, original_name, title
        FROM documents
       WHERE id = ? AND user_id = ? AND is_deleted = 0
    ');
    $stmt->execute([$id, $userId]);

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

    // MIME-Type bestimmen
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $path);
    finfo_close($finfo);

    // Download-Headers setzen
    header('Content-Type: ' . $mimeType);
    header('Content-Disposition: attachment; filename="' . addslashes($doc['original_name']) . '"');
    header('Content-Length: ' . filesize($path));
    header('Cache-Control: private, no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Datei ausgeben
    readfile($path);
    exit;

} catch (Exception $e) {
    error_log('Download error: ' . $e->getMessage());
    http_response_code(500);
    exit('Fehler beim Herunterladen der Datei.');
}
?>
