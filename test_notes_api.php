<?php
// Test der Notes API
require_once __DIR__ . '/src/lib/auth.php';
require_once __DIR__ . '/src/lib/db.php';

// Für den Test simulieren wir einen eingeloggten User
session_start();

if (!isLoggedIn()) {
    echo "Bitte loggen Sie sich zuerst ein.\n";
    exit;
}

$user = getUser();
echo "Test der Notes API für User: " . $user['username'] . "\n";

try {
    // Test: Notes abrufen
    $sql = "SELECT COUNT(*) as count FROM notes WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user['id']]);
    $result = $stmt->fetch();
    
    echo "Anzahl vorhandener Notizen: " . $result['count'] . "\n";
    
    // Test: Neue Notiz erstellen
    $sql = "INSERT INTO notes (user_id, title, content, color) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user['id'], 'Test Notiz', 'Das ist eine Test-Notiz vom ' . date('Y-m-d H:i:s'), '#fbbf24']);
    
    echo "Test-Notiz erstellt mit ID: " . $pdo->lastInsertId() . "\n";
    
    // Test: Notes wieder abrufen
    $sql = "SELECT * FROM notes WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user['id']]);
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Vorhandene Notizen:\n";
    foreach ($notes as $note) {
        echo "- ID: {$note['id']}, Titel: {$note['title']}, Erstellt: {$note['created_at']}\n";
    }
    
    echo "\nAPI Test erfolgreich!\n";
    
} catch (Exception $e) {
    echo "Fehler: " . $e->getMessage() . "\n";
}
?>
