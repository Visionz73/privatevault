<?php
// public/api/notes.php - API für das Notes-System
// Wichtig: Session muss vor include gestartet werden

// Error handling für JSON-Response
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Content-Type früh setzen
header('Content-Type: application/json');

try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    require_once __DIR__ . '/../../src/api/notes.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'API initialization failed',
        'message' => $e->getMessage()
    ]);
}
?>
