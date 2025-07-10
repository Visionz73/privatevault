<?php
// public/file-explorer.php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/lib/auth.php';

session_start();
requireLogin();

$userId = (int)$_SESSION['user_id'];

// --- Input validieren und säubern ---
$deleteId    = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
$currentPath = filter_input(INPUT_GET, 'path',   FILTER_SANITIZE_STRING) ?: '';
$currentView = filter_input(INPUT_GET, 'view',   FILTER_SANITIZE_STRING) ?: 'grid';
$searchQuery = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING) ?: '';
$filterType  = filter_input(INPUT_GET, 'type',   FILTER_SANITIZE_STRING) ?: '';

// --- Datei löschen (hard delete) ---
if ($deleteId !== false && $deleteId !== null) {
    $stmt = $pdo->prepare("
        DELETE FROM documents 
         WHERE id = :id
           AND user_id = :uid
    ");
    $stmt->execute([
        ':id'  => $deleteId,
        ':uid' => $userId,
    ]);

    // Redirect zurück (Pfad erhalten)
    $qs = $currentPath !== '' 
        ? '?path=' . urlencode($currentPath) 
        : '';
    header('Location: /file-explorer.php' . $qs);
    exit;
}

// --- Dateityp-Kategorien ---
$fileTypes = [
    'documents' => ['pdf','doc','docx','txt','rtf'],
    'images'    => ['jpg','jpeg','png','gif','bmp','svg','webp'],
    'videos'    => ['mp4','avi','mov','wmv','flv','webm','mkv'],
    'audio'     => ['mp3','wav','flac','aac','ogg','wma'],
    'archives'  => ['zip','rar','7z','tar','gz'],
    'code'      => ['js','php','css','html','py','java','cpp','c'],
];

// --- WHERE-Klauseln zusammensetzen ---
$where = ['d.user_id = :uid', 'd.is_deleted = 0'];
$params = [':uid' => $userId];

if ($searchQuery !== '') {
    $where[] = '(d.title    LIKE :term
               OR d.filename LIKE :term
               OR d.original_name LIKE :term)';
    $params[':term'] = '%' . $searchQuery . '%';
}

if ($filterType !== '' && isset($fileTypes[$filterType])) {
    $exts = $fileTypes[$filterType];
    // Platzhalter für IN-Liste
    $ph = implode(',', array_map(fn($i) => ":ext{$i}", array_keys($exts)));
    $where[] = "LOWER(SUBSTRING_INDEX(d.filename, '.', -1)) IN ($ph)";
    foreach ($exts as $i => $ext) {
        $params[":ext{$i}"] = strtolower($ext);
    }
}

$whereSql = implode(' AND ', $where);

// --- Dateien aus DB laden ---
$sql = "
    SELECT 
        d.*,
        dc.name AS category_name,
        CHAR_LENGTH(d.filename) - CHAR_LENGTH(REPLACE(d.filename, '.', '')) AS dot_count
    FROM documents d
    LEFT JOIN document_categories dc
      ON d.category_id = dc.id
    WHERE $whereSql
    ORDER BY d.upload_date DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Kategorien für Dropdown ---
$stmt = $pdo->prepare("SELECT * FROM document_categories WHERE user_id = :uid ORDER BY name");
$stmt->execute([':uid' => $userId]);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Statistiken berechnen ---
$totalFiles = count($files);
$totalSize  = 0;
$typeCounts = [];

foreach ($files as $f) {
    $ext = strtolower(pathinfo($f['filename'], PATHINFO_EXTENSION));
    $typeCounts[$ext] = ($typeCounts[$ext] ?? 0) + 1;

    $path = __DIR__ . '/../uploads/' . $f['filename'];
    if (is_file($path)) {
        $totalSize += filesize($path);
    }
}

// --- Hilfsfunktionen ---
function formatFileSize(int $bytes): string
{
    return match (true) {
        $bytes >= 1<<30 => number_format($bytes / (1<<30), 2) . ' GB',
        $bytes >= 1<<20 => number_format($bytes / (1<<20), 2) . ' MB',
        $bytes >= 1<<10 => number_format($bytes / (1<<10), 2) . ' KB',
        default         => $bytes . ' B',
    };
}

function getFileIcon(string $filename): array
{
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    $iconMap = [
        'pdf'  => ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'text-red-400'],
        'jpg'  => ['icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'text-green-400'],
        'jpeg' => ['icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'text-green-400'],
        'png'  => ['icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'text-green-400'],
        'mp3'  => ['icon' => 'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3', 'color' => 'text-purple-400'],
        'mp4'  => ['icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 'color' => 'text-pink-400'],
        'zip'  => ['icon' => 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7h2V8h8v5h6z', 'color' => 'text-yellow-400'],
    ];

    return $iconMap[$ext] ?? ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'text-gray-400'];
}

// --- Template laden ---
require_once __DIR__ . '/../templates/file-explorer-fixed.php';
    'searchQuery', 'filterType'
);

require_once __DIR__ . '/../templates/file-explorer-fixed.php';
