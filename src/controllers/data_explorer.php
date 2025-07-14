<?php
// src/controllers/data_explorer.php
require_once __DIR__ . '/../lib/auth.php';
requireLogin();

$rootDir = realpath(__DIR__ . '/../../uploads');
$path = $_GET['path'] ?? '';
$targetDir = realpath($rootDir . '/' . $path);

if ($targetDir === false || strpos($targetDir, $rootDir) !== 0) {
    $targetDir = $rootDir;
    $path = '';
}

// Handle folder creation and file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // New folder
    if (isset($_POST['new_folder']) && trim($_POST['new_folder']) !== '') {
        $folderName = preg_replace('/[^A-Za-z0-9_\- ]/', '', $_POST['new_folder']);
        $newDir = $targetDir . '/' . $folderName;
        if (!is_dir($newDir)) {
            mkdir($newDir, 0755);
        }
    }
    // File upload
    if (isset($_FILES['upload_file']) && $_FILES['upload_file']['error'] === UPLOAD_ERR_OK) {
        $uploadName = basename($_FILES['upload_file']['name']);
        move_uploaded_file($_FILES['upload_file']['tmp_name'], $targetDir . '/' . $uploadName);
    }
    header('Location: data-explorer.php?path=' . urlencode($path));
    exit;
}

// Scan directory
$items = scandir($targetDir);
$directories = [];
$files = [];
foreach ($items as $item) {
    if ($item === '.' || $item === '..') continue;
    $full = $targetDir . '/' . $item;
    if (is_dir($full)) {
        $directories[] = $item;
    } elseif (is_file($full)) {
        $files[] = $item;
    }
}

// Breadcrumbs
$crumbs = [];
if ($path !== '') {
    $parts = explode('/', $path);
    $acc = '';
    foreach ($parts as $part) {
        $acc = $acc === '' ? $part : $acc . '/' . $part;
        $crumbs[] = ['name' => $part, 'path' => $acc];
    }
}

require_once __DIR__ . '/../../templates/data_explorer.php';
