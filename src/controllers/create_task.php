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
    // Debug: log received form fields
    error_log("DEBUG: create_task POST received");
    error_log("DEBUG: Title: " . ($_POST['title'] ?? 'unset'));
    error_log("DEBUG: Description: " . ($_POST['description'] ?? 'unset'));
    error_log("DEBUG: Assigned_to: " . ($_POST['assigned_to'] ?? 'unset'));
    error_log("DEBUG: Due_date: " . ($_POST['due_date'] ?? 'unset'));

    try {
        $stmt = $pdo->prepare("INSERT INTO tasks (title, description, assigned_to, due_date, status, user_id, created_by) VALUES (?, ?, ?, ?, 'open', ?, ?)");
        $result = $stmt->execute([
            $_POST['title'] ?? '',
            $_POST['description'] ?? '',
            $_POST['assigned_to'] ?? '',
            $_POST['due_date'] ?? '',
            $_SESSION['user']['id'] ?? 1,  // fallback user id
            $_SESSION['user']['id'] ?? 1   // fallback as created_by
        ]);
        error_log("DEBUG: Insert executed. Row count: " . $stmt->rowCount());
        if ($result && $stmt->rowCount() > 0) {
            $success = 'Aufgabe wurde erfolgreich erstellt.';
            header('Location: /dashboard.php');
            exit;
        } else {
            error_log("DEBUG: No rows inserted.");
            $errors[] = 'Aufgabe wurde nicht erstellt. Überprüfen Sie die Eingabedaten.';
        }
    } catch (PDOException $e) {
        error_log("DEBUG: PDOException: " . $e->getMessage());
        $errors[] = 'Fehler beim Erstellen der Aufgabe: ' . $e->getMessage();
    }
}

// Template laden
require_once __DIR__ . '/../../templates/create_task.php';
?>
