<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';
requireLogin();
$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Feldeingaben
    $title       = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $event_date  = $_POST['event_date'] ?? '';
    $assigned_to = isset($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null;
    
    // Einfaches Validieren
    if ($title && $event_date) {
        $stmt = $pdo->prepare("
            INSERT INTO events (title, description, event_date, created_by, assigned_to)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$title, $description, $event_date, $userId, $assigned_to]);
        header('Location: /dashboard.php');
        exit;
    } else {
        $error = "Titel und Datum sind erforderlich.";
    }
}

// Bei GET: Formular anzeigen
require_once __DIR__ . '/../../templates/create_event.php';
