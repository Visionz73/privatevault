<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Task Table Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .error { color: red; }
        .success { color: green; }
        pre { background: #f5f5f5; padding: 10px; overflow: auto; }
    </style>
</head>
<body>
    <h1>Tasks Table Structure</h1>
    <?php
    try {
        // Check table structure
        $stmt = $pdo->query("DESCRIBE tasks");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h2>Columns in tasks table:</h2>";
        echo "<table><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            foreach ($col as $key => $value) {
                echo "<td>" . htmlspecialchars($value ?? "NULL") . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        
        // Count tasks
        $stmt = $pdo->query("SELECT COUNT(*) FROM tasks");
        $taskCount = $stmt->fetchColumn();
        echo "<p>Total tasks in database: <strong>{$taskCount}</strong></p>";
        
        // Sample tasks
        if ($taskCount > 0) {
            $stmt = $pdo->query("SELECT * FROM tasks LIMIT 5");
            $sampleTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<h2>Sample tasks:</h2>";
            echo "<table><tr>";
            foreach (array_keys($sampleTasks[0]) as $key) {
                echo "<th>" . htmlspecialchars($key) . "</th>";
            }
            echo "</tr>";
            
            foreach ($sampleTasks as $task) {
                echo "<tr>";
                foreach ($task as $value) {
                    echo "<td>" . htmlspecialchars($value ?? "NULL") . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } catch (PDOException $e) {
        echo '<div class="error">';
        echo '<p>Error checking tasks table: ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '</div>';
    }
    ?>
    
    <h2>SQL Fix for Tasks Table</h2>
    <pre>
ALTER TABLE tasks 
  MODIFY COLUMN assigned_to INT NULL,
  DROP COLUMN IF EXISTS user_id;
    </pre>
    
    <p>
        <a href="/admin.php">Go to Admin Page</a> | 
        <a href="/diagnose.php">Run Full Diagnostics</a>
    </p>
</body>
</html>
