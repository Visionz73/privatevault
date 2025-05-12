<?php
// config.php
// DB-Konfiguration
$dsn    = 'mysql:host=localhost;dbname=privatevault;charset=utf8mb4';
$dbUser = 'root';           // neu
$dbPass = '';   // neu 

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

