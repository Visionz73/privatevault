<?php
// src/controllers/create_task.php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';

// Falls noch nicht geschehen, Session starten
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fallback: Wenn kein eingeloggter Nutzer existiert, setze einen Standardnutzer (nur zu Testzwecken!)
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = ['id' => 1]; // Standardnutzer-ID
}


// Initialisierung von Variablen
$allUsers = [];
$success = '';
$errors = [];

// Benutzer laden
try {
    $stmt = $pdo->query("SELECT id, username FROM users ORDER BY username");
    $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = 'Fehler beim Laden der Benutzer';
}

// Formularverarbeitung
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO tasks (title, description, assigned_to, due_date, status, user_id, created_by) VALUES (?, ?, ?, ?, 'open', ?, ?)"
        );
        $stmt->execute([
            $_POST['title'],
            $_POST['description'],
            $_POST['assigned_to'],
            $_POST['due_date'],
            $_SESSION['user']['id'] ?? 1,  // use session user id or default 1
            $_SESSION['user']['id'] ?? 1   // created_by same as user_id
        ]);
        
        header('Location: /dashboard.php');
        exit;
    } catch (PDOException $e) {
        $errors[] = 'Fehler beim Erstellen der Aufgabe: ' . $e->getMessage();
    }
}

// Template laden
require_once __DIR__ . '/../../templates/create_task.php';
?>
