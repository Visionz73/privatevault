<?php
// Debug script for notes API
session_start();

echo "<h2>Notes API Debug - Updated</h2>";

// Check if user is logged in
echo "<h3>Session Check:</h3>";
echo "Session ID: " . session_id() . "<br>";
echo "User ID in session: " . ($_SESSION['user_id'] ?? 'NOT SET') . "<br>";

// Check database connection
echo "<h3>Database Check:</h3>";
try {
    require_once __DIR__ . '/config.php';
    echo "Database connection: SUCCESS<br>";
    echo "PDO object exists: " . (isset($pdo) ? 'YES' : 'NO') . "<br>";
    
    // Check if tables exist
    $stmt = $pdo->query("SHOW TABLES LIKE 'notes'");
    $notesTableExists = $stmt->fetch() ? 'YES' : 'NO';
    echo "Notes table exists: $notesTableExists<br>";
    
    $stmt = $pdo->query("SHOW TABLES LIKE 'note_tags'");
    $noteTagsTableExists = $stmt->fetch() ? 'YES' : 'NO';
    echo "Note_tags table exists: $noteTagsTableExists<br>";
    
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        echo "User found in database: " . ($user ? 'YES' : 'NO') . "<br>";
        if ($user) {
            echo "Username: " . $user['username'] . "<br>";
        }
    }
    
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "<br>";
}

// Test API directly
echo "<h3>API Test:</h3>";
if (isset($_SESSION['user_id'])) {
    echo "<a href='/api/notes.php' target='_blank'>Test API directly</a><br>";
    echo "<a href='/src/api/notes.php' target='_blank'>Test API source directly</a><br>";
} else {
    echo "Cannot test API - user not logged in<br>";
    echo "<a href='/login.php'>Go to login</a><br>";
}

// Show current working directory and file paths
echo "<h3>File System Check:</h3>";
echo "Current directory: " . __DIR__ . "<br>";
echo "API file exists (/api/notes.php): " . (file_exists(__DIR__ . '/api/notes.php') ? 'YES' : 'NO') . "<br>";
echo "API source exists (/src/api/notes.php): " . (file_exists(__DIR__ . '/src/api/notes.php') ? 'YES' : 'NO') . "<br>";
echo "Auth lib exists: " . (file_exists(__DIR__ . '/src/lib/auth.php') ? 'YES' : 'NO') . "<br>";
echo "DB lib exists: " . (file_exists(__DIR__ . '/src/lib/db.php') ? 'YES' : 'NO') . "<br>";

// Create tables if they don't exist
if (isset($pdo) && isset($_SESSION['user_id'])) {
    echo "<h3>Creating tables if needed:</h3>";
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS notes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            content TEXT,
            color VARCHAR(7) DEFAULT '#fbbf24',
            is_pinned BOOLEAN DEFAULT FALSE,
            is_archived BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        
        $pdo->exec("CREATE TABLE IF NOT EXISTS note_tags (
            id INT AUTO_INCREMENT PRIMARY KEY,
            note_id INT NOT NULL,
            tag_name VARCHAR(100) NOT NULL,
            INDEX idx_note_id (note_id)
        )");
        
        echo "Tables created successfully<br>";
    } catch (Exception $e) {
        echo "Error creating tables: " . $e->getMessage() . "<br>";
    }
}
?>
