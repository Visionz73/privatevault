<?php
// Debug-Version der Notes API
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "DEBUG: API wird aufgerufen\n";

try {
    require_once __DIR__ . '/../src/lib/auth.php';
    echo "DEBUG: Auth-Lib geladen\n";
    
    require_once __DIR__ . '/../src/lib/db.php';
    echo "DEBUG: DB-Lib geladen\n";
    
} catch (Exception $e) {
    echo "DEBUG: Fehler beim Laden der Libs: " . $e->getMessage() . "\n";
    exit;
}

header('Content-Type: application/json');

// Session check
session_start();
echo "DEBUG: Session gestartet\n";

if (!function_exists('isLoggedIn')) {
    echo json_encode(['error' => 'Auth function not available']);
    exit;
}

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized - not logged in']);
    exit;
}

echo "DEBUG: User ist eingeloggt\n";

$user = getUser();
echo "DEBUG: User ID: " . $user['id'] . "\n";

$method = $_SERVER['REQUEST_METHOD'];
echo "DEBUG: HTTP Method: " . $method . "\n";

// Einfacher Test: Notes zählen
try {
    global $pdo;
    
    $sql = "SELECT COUNT(*) as count FROM notes WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user['id']]);
    $result = $stmt->fetch();
    
    echo json_encode([
        'debug' => true,
        'method' => $method,
        'user_id' => $user['id'],
        'notes_count' => $result['count'],
        'message' => 'API erreichbar und funktionsfähig'
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
