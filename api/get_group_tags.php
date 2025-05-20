<?php
// API endpoint for getting tags assigned to a group
header('Content-Type: application/json');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/lib/auth.php';

// Ensure user is authenticated and has admin permissions
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Nicht authentifiziert']);
    exit;
}

// Check if user is an admin
$user = getUser();
if ($user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Keine Berechtigung']);
    exit;
}

// Get group ID from query parameter
$groupId = isset($_GET['group_id']) ? (int)$_GET['group_id'] : 0;

if (empty($groupId)) {
    http_response_code(400);
    echo json_encode(['error' => 'Gruppen-ID erforderlich']);
    exit;
}

try {
    // Fetch tag IDs assigned to the group
    $stmt = $pdo->prepare("
        SELECT tag_id 
        FROM group_tag_assignments 
        WHERE group_id = ?
    ");
    $stmt->execute([$groupId]);
    $tagIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo json_encode($tagIds);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>
