<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/lib/db.php';

echo "<h1>Admin Role Check</h1>";

if (!isset($_SESSION['user_id'])) {
    echo "<p style='color:red'>Not logged in.</p>";
    exit;
}

$stmt = $pdo->prepare("SELECT id, username, role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<p>User: " . htmlspecialchars($user['username']) . "</p>";
echo "<p>Role: " . htmlspecialchars($user['role']) . "</p>";

if ($user['role'] !== 'admin') {
    echo "<p style='color:red'>You don't have admin role.</p>";
    echo "<p>Run this SQL to grant admin:</p>";
    echo "<pre>UPDATE users SET role = 'admin' WHERE id = " . (int)$_SESSION['user_id'] . ";</pre>";
} else {
    echo "<p style='color:green'>You have admin role.</p>";
}
