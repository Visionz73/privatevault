// src/controllers/create_task.php
<?php
// Ab jetzt immer absolut per DOCUMENT_ROOT einbinden
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/auth.php';

// Session starten
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // Debug-Fallback
}

$allUsers = [];
$errors   = [];

// Nutzer für select laden
try {
    $stmt     = $pdo->query("SELECT id, username FROM users ORDER BY username");
    $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = 'Error loading users: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fallback: wenn kein Empfänger ausgewählt, auf mich selbst setzen
    $assignedTo = $_POST['assigned_to'] ?? $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("
            INSERT INTO tasks
              (title, description, assigned_to, due_date, status, user_id, created_by)
            VALUES
              (?, ?, ?, ?, 'open', ?, ?)
        ");
        $stmt->execute([
            $_POST['title']       ?? '',
            $_POST['description'] ?? '',
            $assignedTo,
            $_POST['due_date']    ?? null,
            $_SESSION['user_id'],
            $_SESSION['user_id']
        ]);

        if ($stmt->rowCount() > 0) {
            // Redirect zur öffentlichen Inbox
            header('Location: /inbox.php');
            exit;
        } else {
            $errors[] = 'Task wurde nicht angelegt. Bitte Eingaben prüfen.';
        }
    } catch (PDOException $e) {
        $errors[] = 'Fehler beim Anlegen: ' . $e->getMessage();
    }
}

// Template laden
require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/create_task.php';
