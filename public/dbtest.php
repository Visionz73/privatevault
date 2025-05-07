<?php
require_once __DIR__ . '/../config.php';

$stmt = $pdo->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "<h1>Tables in privatevault_db:</h1><ul>";
foreach ($tables as $table) {
    echo "<li>$table</li>";
}
echo "</ul>";

