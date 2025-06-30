<?php
// Simple API test
session_start();

// Enable error handling for JSON
function handleApiError($errno, $errstr, $errfile, $errline) {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    echo json_encode(['error' => 'PHP Error', 'message' => $errstr]);
    exit;
}

set_error_handler('handleApiError');

header('Content-Type: application/json');

try {
    // Test database connection
    require_once 'config.php';
    
    // Test authentication
    require_once 'src/lib/auth.php';
    
    if (!isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['error' => 'Not logged in']);
        exit;
    }
    
    $user = getUser();
    if (!$user) {
        http_response_code(401);
        echo json_encode(['error' => 'User not found']);
        exit;
    }
    
    // Simple test query
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM notes WHERE user_id = ? AND is_deleted = 0");
    $stmt->execute([$user['id']]);
    $result = $stmt->fetch();
    
    echo json_encode([
        'success' => true,
        'user_id' => $user['id'],
        'notes_count' => (int)$result['count'],
        'message' => 'API working correctly'
    ]);
    
} catch (Exception $e) {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
