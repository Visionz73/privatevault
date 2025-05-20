<?php
// src/api/task_update.php
require_once __DIR__ . '/../lib/auth.php';
requireLogin();
require_once __DIR__ . '/../lib/db.php';

header('Content-Type: text/plain');

// Get parameters
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$status = $_POST['status'] ?? '';

// Validate inputs
if (!$id || !in_array($status, ['todo', 'doing', 'done'], true)) {
    http_response_code(400);
    echo "Ungültige Parameter. ID: $id, Status: $status";
    exit;
}

try {
    // Update task status
    $stmt = $pdo->prepare('
        UPDATE tasks 
        SET status = ? 
        WHERE id = ?
    ');
    $result = $stmt->execute([$status, $id]);
    
    if ($result) {
        echo "Status erfolgreich aktualisiert";
    } else {
        http_response_code(500);
        echo "Datenbankaktualisierung fehlgeschlagen";
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo "Fehler: " . $e->getMessage();
}
