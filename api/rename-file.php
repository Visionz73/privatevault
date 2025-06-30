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

if (!$input || !isset($input['file_id']) || !isset($input['new_name'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Fehlende Parameter']);
    exit;
}

$file_id = intval($input['file_id']);
$new_name = trim($input['new_name']);
$user_id = $_SESSION['user_id'];

// Validate new name
if (empty($new_name) || strlen($new_name) > 255) {
    echo json_encode(['success' => false, 'message' => 'Ungültiger Dateiname']);
    exit;
}

// Sanitize filename
$new_name = preg_replace('/[^a-zA-Z0-9._\-\s]/', '', $new_name);

try {
    // Check if file exists and belongs to user
    $stmt = $pdo->prepare("SELECT * FROM documents WHERE id = ? AND user_id = ?");
    $stmt->execute([$file_id, $user_id]);
    $file = $stmt->fetch();
    
    if (!$file) {
        echo json_encode(['success' => false, 'message' => 'Datei nicht gefunden oder keine Berechtigung']);
        exit;
    }
    
    // Check if new filename already exists in same category
    $stmt = $pdo->prepare("SELECT id FROM documents WHERE filename = ? AND category = ? AND user_id = ? AND id != ?");
    $stmt->execute([$new_name, $file['category'], $user_id, $file_id]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Eine Datei mit diesem Namen existiert bereits']);
        exit;
    }
    
    // Update filename
    $stmt = $pdo->prepare("UPDATE documents SET filename = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$new_name, $file_id, $user_id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true, 
            'message' => 'Datei erfolgreich umbenannt',
            'new_name' => $new_name
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Umbenennen der Datei']);
    }
    
} catch (Exception $e) {
    error_log("Error renaming file: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler aufgetreten']);
}
?>
