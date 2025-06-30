<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die('Nicht authentifiziert');
}

if (!isset($_GET['file'])) {
    http_response_code(400);
    die('Datei-Parameter fehlt');
}

$zip_filename = basename($_GET['file']);
$zip_path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $zip_filename;

// Validate filename format for security
if (!preg_match('/^download_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}_[a-f0-9]+\.zip$/', $zip_filename)) {
    http_response_code(400);
    die('Ungültiger Dateiname');
}

if (!file_exists($zip_path)) {
    http_response_code(404);
    die('Datei nicht gefunden');
}

// Set headers for download
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zip_filename . '"');
header('Content-Length: ' . filesize($zip_path));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

// Stream the file
readfile($zip_path);

// Clean up - delete the temporary file
unlink($zip_path);
exit;
?>
