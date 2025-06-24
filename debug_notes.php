<?php
// Debug script for notes API
session_start();

// Test if user is logged in
echo "<h2>Debug Notes API</h2>\n";
echo "<h3>Session Check:</h3>\n";
echo "<pre>\n";
echo "Session ID: " . session_id() . "\n";
echo "User ID in session: " . ($_SESSION['user_id'] ?? 'NOT SET') . "\n";
echo "Session data: " . print_r($_SESSION, true) . "\n";
echo "</pre>\n";

if (empty($_SESSION['user_id'])) {
    echo "<p style='color: red;'>ERROR: No user logged in. Please log in first.</p>\n";
    echo "<a href='/login.php'>Go to Login</a>\n";
    exit;
}

// Test database connection
require_once __DIR__ . '/config.php';
echo "<h3>Database Check:</h3>\n";
echo "<pre>\n";
try {
    $result = $pdo->query("SELECT 1");
    echo "Database connection: OK\n";
    
    // Check if notes table exists
    $result = $pdo->query("SHOW TABLES LIKE 'notes'");
    if ($result->rowCount() > 0) {
        echo "Notes table: EXISTS\n";
        
        // Count notes for current user
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM notes WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $count = $stmt->fetch()['count'];
        echo "Notes for user {$_SESSION['user_id']}: {$count}\n";
        
        // Show recent notes
        $stmt = $pdo->prepare("SELECT id, title, created_at FROM notes WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
        $stmt->execute([$_SESSION['user_id']]);
        $notes = $stmt->fetchAll();
        echo "Recent notes:\n";
        foreach ($notes as $note) {
            echo "  ID: {$note['id']}, Title: '{$note['title']}', Created: {$note['created_at']}\n";
        }
    } else {
        echo "Notes table: DOES NOT EXIST\n";
    }
    
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
echo "</pre>\n";

// Test API directly
echo "<h3>API Test:</h3>\n";
echo "<pre>\n";

// Simulate GET request
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['archived'] = 'false';
$_GET['limit'] = '20';

ob_start();
try {
    include __DIR__ . '/src/api/notes.php';
    $output = ob_get_contents();
} catch (Exception $e) {
    $output = "ERROR: " . $e->getMessage();
}
ob_end_clean();

echo "API Response:\n";
echo $output . "\n";
echo "</pre>\n";

echo "<h3>Test Complete</h3>\n";
echo "<a href='/dashboard.php'>Back to Dashboard</a>\n";
?>
