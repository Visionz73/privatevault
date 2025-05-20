<?php
// config.php
// DB-Konfiguration
$dsn    = 'mysql:host=127.0.0.1;dbname=privatevault_db;charset=utf8mb4';
$dbUser = 'pv_user';           // neu
$dbPass = '12345678';   // neu 

$options = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    die('DB‐Verbindung fehlgeschlagen: ' . $e->getMessage());
}

// Session nur starten, wenn noch keine aktiv ist:
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enable DEBUG mode for development
define('DEBUG', true);

// Make sure logs directory exists
if (!file_exists(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}

