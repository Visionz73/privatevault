<?php
// test_secure_urls.php - Test script for secure URL generation
require_once __DIR__ . '/config.php';

echo "<h1>Secure URL Generation Test</h1>";

// Test the getSecureUrl function
$testPaths = [
    'uploads/test.pdf',
    'uploads/image.jpg',
    'uploads/document.docx'
];

foreach ($testPaths as $path) {
    $secureUrl = getSecureUrl($path);
    $fileUrl = getFileUrl(basename($path));
    
    echo "<p><strong>Path:</strong> {$path}</p>";
    echo "<p><strong>Secure URL:</strong> {$secureUrl}</p>";
    echo "<p><strong>File URL:</strong> {$fileUrl}</p>";
    echo "<hr>";
}

echo "<h2>Current Server Variables:</h2>";
echo "<pre>";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'not set') . "\n";
echo "SERVER_NAME: " . ($_SERVER['SERVER_NAME'] ?? 'not set') . "\n";
echo "REQUEST_SCHEME: " . ($_SERVER['REQUEST_SCHEME'] ?? 'not set') . "\n";
echo "</pre>";
?>
