<?php
// API Weiterleitung für notes.php
// Wichtig: Session muss vor include gestartet werden

// Start output buffering immediately
ob_start();

// Error handling für JSON-Response
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Global error handler for this file
function apiRouterErrorHandler($errno, $errstr, $errfile, $errline) {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'error' => 'API Router Error',
        'message' => $errstr,
        'file' => basename($errfile),
        'line' => $errline
    ]);
    exit;
}

set_error_handler('apiRouterErrorHandler');

// Content-Type früh setzen
header('Content-Type: application/json');

try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Include the actual API file
    require_once __DIR__ . '/../src/api/notes.php';
} catch (Exception $e) {
    if (ob_get_level()) ob_clean();
    http_response_code(500);
    echo json_encode([
        'error' => 'API initialization failed',
        'message' => $e->getMessage(),
        'file' => basename($e->getFile()),
        'line' => $e->getLine()
    ]);
}
?>
