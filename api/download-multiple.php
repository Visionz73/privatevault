<?php
session_start();
require_once '../config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Nicht authentifiziert']);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Nur POST-Anfragen erlaubt']);
    exit;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['file_ids']) || !is_array($input['file_ids'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Fehlende oder ungültige file_ids']);
    exit;
}

$file_ids = array_map('intval', $input['file_ids']);
$user_id = $_SESSION['user_id'];

if (empty($file_ids)) {
    echo json_encode(['success' => false, 'message' => 'Keine Dateien ausgewählt']);
    exit;
}

try {
    // Get files that belong to user
    $placeholders = str_repeat('?,', count($file_ids) - 1) . '?';
    $params = array_merge($file_ids, [$user_id]);
    
    $stmt = $pdo->prepare("SELECT id, filename, filepath FROM documents WHERE id IN ($placeholders) AND user_id = ?");
    $stmt->execute($params);
    $files = $stmt->fetchAll();
    
    if (empty($files)) {
        echo json_encode(['success' => false, 'message' => 'Keine gültigen Dateien gefunden']);
        exit;
    }
    
    // Create a temporary ZIP file
    $zip_filename = 'download_' . date('Y-m-d_H-i-s') . '_' . uniqid() . '.zip';
    $zip_path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $zip_filename;
    
    $zip = new ZipArchive();
    if ($zip->open($zip_path, ZipArchive::CREATE) !== TRUE) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Erstellen der ZIP-Datei']);
        exit;
    }
    
    $files_added = 0;
    foreach ($files as $file) {
        $file_path = '../uploads/' . $file['filepath'];
        if (file_exists($file_path)) {
            $zip->addFile($file_path, $file['filename']);
            $files_added++;
        }
    }
    
    if ($files_added === 0) {
        $zip->close();
        unlink($zip_path);
        echo json_encode(['success' => false, 'message' => 'Keine der ausgewählten Dateien konnte gefunden werden']);
        exit;
    }
    
    $zip->close();
    
    // Return download info
    echo json_encode([
        'success' => true,
        'download_url' => '/api/download-zip.php?file=' . urlencode($zip_filename),
        'filename' => 'Dateien_' . date('Y-m-d_H-i-s') . '.zip',
        'files_count' => $files_added
    ]);
    
} catch (Exception $e) {
    error_log("Error creating bulk download: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Fehler beim Erstellen des Downloads']);
}
?>
