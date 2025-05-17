<?php
ob_start(); // Output Buffering aktivieren

// Fehlerberichterstattung aktivieren
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!file_exists(__DIR__ . '/../config.php')) {
    die("Error: config.php not found at " . __DIR__ . '/../config.php');
}
if (!file_exists(__DIR__ . '/../src/controllers/create_task.php')) {
    die("Error: Controller not found at " . __DIR__ . '/../src/controllers/create_task.php');
}

// Konfigurations- und Controller-Dateien laden
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/controllers/create_task.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ...existing creation logic...
    if($creationSuccessful){ // Ensure that $creationSuccessful is set in your controller
        header('Location: inbox.php');
        exit;
    }
}

// Remove any inclusion of templates that display the taskboard if not needed
// For example, if there's a require or include for a dashboard or taskboard template, remove or comment it out:
// require_once __DIR__.'/dashboard_template.php';

// ...existing code for displaying the create task form...

ob_end_flush();
?>
