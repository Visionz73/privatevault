<?php
// src/controllers/create_task.php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../../config.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

requireLogin();
requireRole(['admin']);          // nur Admins dürfen Tasks erstellen

// Initialisierung von Variablen
$allUsers = [];
$success = '';
$errors = [];

// Alle Nutzer für das Dropdown
try {
    $stmt = $pdo->query("SELECT id, username FROM users ORDER BY username");
    $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = 'Fehler beim Laden der Benutzer';
}

// Formularverarbeitung
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("INSERT INTO tasks (title, description, assigned_to, due_date, status, user_id) VALUES (?, ?, ?, ?, 'open', ?)");
        $stmt->execute([
            $_POST['title'],
            $_POST['description'],
            $_POST['assigned_to'],
            $_POST['due_date'],
            $_SESSION['user']['id']  // Füge user_id hinzu
        ]);
        $success = 'Aufgabe wurde erfolgreich erstellt.';

        // Weiterleitung nach erfolgreicher Erstellung
        header('Location: /dashboard.php');
        exit;
    } catch (PDOException $e) {
        $errors[] = 'Fehler beim Erstellen der Aufgabe: ' . $e->getMessage();
    }
}

// Template laden
require_once __DIR__ . '/../../templates/create_task.php';
?>
