<?php
// Basic diagnostics page
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Privatevault Diagnostic Page</h1>";

// Step 1: Check PHP Version
echo "<h2>PHP Version</h2>";
echo "PHP Version: " . phpversion();

// Step 2: Check directory permissions
echo "<h2>Directory Permissions</h2>";
$dirs = [
    '.',
    './uploads',
    './src',
    './templates',
    './public'
];

foreach ($dirs as $dir) {
    if (file_exists($dir)) {
        echo "<p>$dir exists. ";
        echo "Readable: " . (is_readable($dir) ? 'Yes' : 'No') . ". ";
        echo "Writable: " . (is_writable($dir) ? 'Yes' : 'No') . "</p>";
    } else {
        echo "<p>$dir does not exist.</p>";
    }
}

// Step 3: Check database connection
echo "<h2>Database Connection</h2>";
try {
    require_once __DIR__ . '/config.php';
    echo "<p>Config file loaded successfully.</p>";
    
    echo "<p>Testing database connection: ";
    $stmt = $pdo->query("SELECT 1");
    echo "Success!</p>";

    // Check some important tables
    $tables = ['users', 'tasks', 'documents'];
    echo "<p>Checking tables:</p><ul>";
    foreach ($tables as $table) {
        try {
            $result = $pdo->query("SELECT COUNT(*) FROM $table");
            $count = $result->fetchColumn();
            echo "<li>$table: $count records</li>";
        } catch (PDOException $e) {
            echo "<li>$table: Error - " . $e->getMessage() . "</li>";
        }
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

// Step 4: Check session handling
echo "<h2>Session Handling</h2>";
if (session_status() === PHP_SESSION_NONE) {
    echo "<p>Session not started.</p>";
    session_start();
    echo "<p>Session started now.</p>";
} else {
    echo "<p>Session already active.</p>";
}

echo "<p>Session ID: " . session_id() . "</p>";

// Step 5: Show $_SERVER information
echo "<h2>Server Information</h2>";
echo "<p>REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p>DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
?>

<h2>Next Steps</h2>
<ol>
    <li>Check the PHP error log located at: <?php echo ini_get('error_log'); ?></li>
    <li>Test navigation: <a href="index.php">Home</a> | <a href="public/inbox.php">Inbox</a></li>
    <li>Verify file paths in config.php</li>
</ol>
