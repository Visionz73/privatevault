<?php
// src/controllers/create_task.php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

requireLogin();
requireRole(['admin']);          // nur Admins dürfen Tasks erstellen

echo "Debug: Controller geladen.<br>";

// Initialisierung von Variablen
$allUsers = [];
$success = '';
$errors = [];

// Alle Nutzer für das Dropdown
$allUsers = $pdo->query(
  'SELECT id, username FROM users ORDER BY username'
)->fetchAll();

// Formularverarbeitung
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "Debug: POST-Daten empfangen.<br>";
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $assigned_to = $_POST['assigned_to'] ?? '';
    $due_date    = $_POST['due_date']    ?? null;

    if ($title === '' || $assigned_to === '') {
        $errors[] = 'Titel und Empfänger sind Pflichtfelder.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare(
              'INSERT INTO tasks (title, description, assigned_to, created_by, due_date)
               VALUES (?,?,?,?,?)'
            );
            $stmt->execute([
              $title,
              $description,
              $assigned_to,
              $_SESSION["user_id"],
              $due_date
            ]);
            $success = 'Aufgabe wurde erstellt.';
            echo "Debug: Aufgabe erfolgreich erstellt.<br>";
            $_POST = [];               // Formular leeren
        } catch (PDOException $e) {
            $errors[] = 'Datenbankfehler: ' . $e->getMessage();
            echo "Debug: Datenbankfehler - " . $e->getMessage() . "<br>";
        }
    }
}

// Template laden
require_once __DIR__ . '/../../templates/create_task.php';
?>
