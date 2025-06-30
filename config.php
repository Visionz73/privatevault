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
    // The $options array already correctly sets ERRMODE_EXCEPTION and FETCH_ASSOC.
    // No need for $pdo->setAttribute() calls here for those specific options.

} catch (PDOException $e) {
    // Log the detailed error for server administrators
    // Make sure error_log is configured correctly on the server (e.g., to a file or syslog).
    error_log("Database Connection Error: " . $e->getMessage());

    // Display a user-friendly error message and die gracefully
    // This prevents exposing sensitive error details like $e->getMessage() to the end-user.
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Error</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            color: #333;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }
        .error-container { 
            background-color: #fff;
            border: 1px solid #ddd; 
            border-radius: 8px;
            padding: 30px 40px; 
            display: inline-block; 
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h1 {
            color: #d9534f; /* A common error color */
            font-size: 24px;
            margin-top: 0;
        }
        p {
            font-size: 16px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>Technical Difficulties</h1>
        <p>We are currently experiencing technical difficulties and our team has been notified. Please try again later.</p>
    </div>
</body>
</html>
HTML;
    exit; // Stop further script execution
}

// Session nur starten, wenn noch keine aktiv ist:
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Secure URL generation function
function getSecureUrl($path) {
    $protocol = 'https';
    $host = $_SERVER['HTTP_HOST'] ?? 'omni.local';
    
    // Remove leading slash if present
    $path = ltrim($path, '/');
    
    return $protocol . '://' . $host . '/' . $path;
}

// Alternative: Force HTTPS for file URLs
function getFileUrl($filename) {
    return getSecureUrl('uploads/' . urlencode($filename));
}
?>
