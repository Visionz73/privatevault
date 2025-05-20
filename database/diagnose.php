<?php
// Database Diagnostic Tool
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config.php';

echo "<h1>Database Diagnostic Results</h1>";

// Expected tables and their required columns
$expectedStructure = [
    'users' => [
        'id', 'username', 'password_hash', 'email', 'role', 'created_at', 'updated_at'
    ],
    'tasks' => [
        'id', 'title', 'description', 'created_by', 'assigned_to', 'due_date', 'status', 'is_done'
    ],
    'documents' => [
        'id', 'user_id', 'title', 'filename', 'is_deleted'
    ],
    'events' => [
        'id', 'title', 'created_by', 'event_date'
    ],
    'sub_tasks' => [
        'id', 'task_id', 'title', 'status'
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

// Check admin user
echo "<h2>Admin User Check</h2>";
try {
    $stmt = $pdo->query("SELECT * FROM users WHERE role = 'admin' LIMIT 1");
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "<p style='color:green'>Admin user exists: {$admin['username']}</p>";
    } else {
        echo "<p style='color:red'>No admin user found!</p>";
        echo "<p>You should create an admin user with the SQL below:</p>";
        echo "<pre>INSERT INTO users (username, password_hash, email, role) VALUES (
    'admin', 
    '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 
    'admin@example.com', 
    'admin'
);</pre>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>Error checking admin user: " . $e->getMessage() . "</p>";
}

// Generate fix SQL statements
echo "<h2>SQL Fixes</h2>";
echo "<p>Run these SQL statements to fix missing tables and columns:</p>";
echo "<pre>";

// Tasks table SQL
echo "-- Task table\n";
echo "CREATE TABLE IF NOT EXISTS tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  created_by INT NOT NULL,
  assigned_to INT,
  assigned_group_id INT,
  user_id INT NOT NULL,
  due_date DATE,
  status ENUM('todo', 'doing', 'done') DEFAULT 'todo',
  is_done TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id),
  FOREIGN KEY (assigned_to) REFERENCES users(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);\n\n";

// Subtasks table SQL
echo "-- Subtasks table\n";
echo "CREATE TABLE IF NOT EXISTS sub_tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  task_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  status ENUM('open','closed') DEFAULT 'open',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE
);\n\n";

// Events table SQL
echo "-- Events table\n";
echo "CREATE TABLE IF NOT EXISTS events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  created_by INT NOT NULL,
  event_date DATE NOT NULL,
  assigned_to INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id),
  FOREIGN KEY (assigned_to) REFERENCES users(id)
);\n\n";

// Add columns if missing
echo "-- Add missing columns if needed\n";
echo "ALTER TABLE tasks ADD COLUMN IF NOT EXISTS status ENUM('todo', 'doing', 'done') DEFAULT 'todo';\n";
echo "ALTER TABLE tasks ADD COLUMN IF NOT EXISTS is_done TINYINT(1) DEFAULT 0;\n";

echo "</pre>";
?>

<h2>Admin Page Fix Steps</h2>
<ol>
  <li>Run the SQL statements above to fix missing tables/columns</li>
  <li>Check for PHP errors in the admin controller: <code>c:\xampp\htdocs\privatevault\src\controllers\admin.php</code></li>
  <li>Verify the admin.php template exists: <code>c:\xampp\htdocs\privatevault\templates\admin.php</code></li>
  <li>Make sure your account has 'admin' role in the database</li>
</ol>
