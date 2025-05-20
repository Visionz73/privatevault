<?php
// Database Diagnostic Tool - Public Access
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config.php';

echo "<h1>Database Diagnostic Results</h1>";

// Expected tables and their required columns
$expectedStructure = [
    'users' => [
        'id', 'username', 'password_hash', 'email', 'role', 'created_at'
    ],
    'tasks' => [
        'id', 'title', 'description', 'created_by', 'assigned_to', 'status', 'is_done'
    ],
    'documents' => [
        'id', 'user_id', 'title', 'filename', 'is_deleted'
    ],
    'events' => [
        'id', 'title', 'created_by', 'event_date'
    ]
];

// Check database tables
echo "<h2>Table Check</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Table</th><th>Status</th><th>Missing Columns</th></tr>";

try {
    // Get all tables in the database
    $stmt = $pdo->query("SHOW TABLES");
    $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($expectedStructure as $table => $columns) {
        if (in_array($table, $existingTables)) {
            // Table exists, check columns
            $stmt = $pdo->query("DESCRIBE {$table}");
            $existingColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $missingColumns = array_diff($columns, $existingColumns);
            
            if (empty($missingColumns)) {
                echo "<tr><td>{$table}</td><td style='color:green'>OK</td><td>None</td></tr>";
            } else {
                echo "<tr><td>{$table}</td><td style='color:orange'>Missing Columns</td><td>" . 
                     implode(", ", $missingColumns) . "</td></tr>";
            }
        } else {
            echo "<tr><td>{$table}</td><td style='color:red'>Missing</td><td>All columns</td></tr>";
        }
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='3' style='color:red'>Error: " . $e->getMessage() . "</td></tr>";
}
echo "</table>";

// Generate fix SQL statements
echo "<h2>SQL Fixes</h2>";
echo "<pre>";

// Tasks table SQL
echo "CREATE TABLE IF NOT EXISTS tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  created_by INT NOT NULL,
  assigned_to INT NOT NULL,
  due_date DATE,
  status ENUM('todo', 'doing', 'done') DEFAULT 'todo',
  is_done TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);\n\n";

// Events table SQL
echo "CREATE TABLE IF NOT EXISTS events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  created_by INT NOT NULL,
  event_date DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);\n\n";

echo "</pre>";
?>

<p>Access this SQL from phpMyAdmin and run it to fix missing tables/columns</p>
<p><a href="/admin.php">Try Admin Page Again</a></p>
