<?php
// Debug script for notes API
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Notes API Debug</h1>";

// Test 1: Check if files exist
echo "<h2>1. File Existence</h2>";
$files_to_check = [
    'src/lib/auth.php',
    'src/lib/db.php', 
    'config.php',
    'src/api/notes.php',
    'api/notes.php'
];

foreach ($files_to_check as $file) {
    $exists = file_exists($file);
    $color = $exists ? 'green' : 'red';
    echo "<div style='color: $color'>$file: " . ($exists ? "EXISTS" : "MISSING") . "</div>";
}

// Test 2: Include config and check DB
echo "<h2>2. Database Connection</h2>";
try {
    require_once 'config.php';
    echo "<div style='color: green'>Config loaded successfully</div>";
    echo "<div>DSN: $dsn</div>";
    echo "<div>User: $dbUser</div>";
    
    if (isset($pdo)) {
        echo "<div style='color: green'>PDO object exists</div>";
        
        // Test connection
        $stmt = $pdo->query("SELECT 1");
        echo "<div style='color: green'>Database connection working</div>";
    } else {
        echo "<div style='color: red'>PDO object not found</div>";
    }
} catch (Exception $e) {
    echo "<div style='color: red'>Database error: " . $e->getMessage() . "</div>";
}

// Test 3: Check session
echo "<h2>3. Session & Auth</h2>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<div>Session status: " . session_status() . "</div>";
echo "<div>Session ID: " . session_id() . "</div>";
echo "<div>User ID in session: " . ($_SESSION['user_id'] ?? 'NOT SET') . "</div>";

// Test 4: Include auth and test
try {
    require_once 'src/lib/auth.php';
    echo "<div style='color: green'>Auth.php loaded successfully</div>";
    
    $logged_in = isLoggedIn();
    echo "<div>Is logged in: " . ($logged_in ? 'YES' : 'NO') . "</div>";
    
    if ($logged_in) {
        $user = getUser();
        echo "<div>User data: " . print_r($user, true) . "</div>";
    }
} catch (Exception $e) {
    echo "<div style='color: red'>Auth error: " . $e->getMessage() . "</div>";
}

// Test 5: Direct API call simulation
echo "<h2>4. Direct API Test</h2>";
try {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_GET['archived'] = 'false';
    $_GET['limit'] = '20';
    
    echo "<div>Simulating GET request to notes API...</div>";
    
    // Capture output
    ob_start();
    include 'src/api/notes.php';
    $output = ob_get_clean();
    
    echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px 0;'>";
    echo "<strong>API Output:</strong><br>";
    echo htmlspecialchars($output);
    echo "</div>";
} catch (Exception $e) {
    echo "<div style='color: red'>API error: " . $e->getMessage() . "</div>";
    echo "<div>Stack trace: " . $e->getTraceAsString() . "</div>";
}
?>
