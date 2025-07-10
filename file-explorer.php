<?php
// Enhanced File Explorer with comprehensive features
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/lib/auth.php';

session_start();
requireLogin();

$user = getUser();
$userId = (int)$_SESSION['user_id'];

// Enhanced input validation
$deleteId = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
$currentView = filter_input(INPUT_GET, 'view', FILTER_SANITIZE_STRING) ?: 'grid';
$searchQuery = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING) ?: '';
$filterType = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING) ?: '';
$sortBy = filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_STRING) ?: 'upload_date';

// File deletion with proper error handling
if ($deleteId !== false && $deleteId !== null) {
    try {
        $stmt = $pdo->prepare("DELETE FROM documents WHERE id = :id AND user_id = :uid");
        $stmt->execute([':id' => $deleteId, ':uid' => $userId]);
        
        if ($stmt->rowCount() > 0) {
            $_SESSION['success_message'] = 'Datei erfolgreich gelöscht';
        } else {
            $_SESSION['error_message'] = 'Datei konnte nicht gelöscht werden';
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = 'Fehler beim Löschen der Datei';
        error_log("File deletion error: " . $e->getMessage());
    }
    
    header('Location: /file-explorer.php');
    exit;
}

// Enhanced file type categorization
$fileTypes = [
    'documents' => ['pdf', 'doc', 'docx', 'txt', 'rtf', 'odt', 'xls', 'xlsx', 'ppt', 'pptx'],
    'images' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp', 'ico', 'tiff'],
    'videos' => ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv', 'm4v', '3gp'],
    'audio' => ['mp3', 'wav', 'flac', 'aac', 'ogg', 'wma', 'm4a', 'opus'],
    'archives' => ['zip', 'rar', '7z', 'tar', 'gz', 'bz2', 'xz'],
    'code' => ['php', 'js', 'html', 'css', 'py', 'java', 'cpp', 'c', 'json', 'xml', 'sql'],
];

// Build dynamic SQL query
$where = ['d.user_id = :uid'];
$params = [':uid' => $userId];

// Add search functionality
if (!empty($searchQuery)) {
    $where[] = '(d.title LIKE :search OR d.filename LIKE :search OR d.original_name LIKE :search)';
    $params[':search'] = '%' . $searchQuery . '%';
}

// Add file type filtering
if (!empty($filterType) && isset($fileTypes[$filterType])) {
    $extensions = $fileTypes[$filterType];
    $extPlaceholders = [];
    foreach ($extensions as $i => $ext) {
        $placeholder = ":ext{$i}";
        $extPlaceholders[] = $placeholder;
        $params[$placeholder] = strtolower($ext);
    }
    $where[] = "LOWER(SUBSTRING_INDEX(d.filename, '.', -1)) IN (" . implode(',', $extPlaceholders) . ")";
}

$whereSql = implode(' AND ', $where);

// Enhanced file query with better metadata
$sql = "
    SELECT 
        d.*,
        dc.name AS category_name,
        UNIX_TIMESTAMP(d.upload_date) AS upload_timestamp,
        CASE 
            WHEN d.title IS NOT NULL AND d.title != '' THEN d.title
            WHEN d.original_name IS NOT NULL AND d.original_name != '' THEN d.original_name
            ELSE d.filename
        END AS display_name
    FROM documents d
    LEFT JOIN document_categories dc ON d.category_id = dc.id
    WHERE {$whereSql}
    ORDER BY d.{$sortBy} DESC
    LIMIT 1000
";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("File query error: " . $e->getMessage());
    $files = [];
}

// Calculate comprehensive statistics
$totalFiles = count($files);
$totalSize = 0;
$typeCounts = [];

foreach ($files as $file) {
    $ext = strtolower(pathinfo($file['filename'], PATHINFO_EXTENSION));
    $typeCounts[$ext] = ($typeCounts[$ext] ?? 0) + 1;
    
    $filePath = __DIR__ . '/uploads/' . $file['filename'];
    if (is_file($filePath)) {
        $totalSize += filesize($filePath);
    }
}

// Get categories for dropdown
try {
    $stmt = $pdo->prepare("SELECT * FROM document_categories ORDER BY name");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
}

// Utility functions
function formatFileSize(int $bytes): string {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, 2) . ' ' . $units[$pow];
}

function getFileIcon(string $filename): array {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    $iconMap = [
        'pdf' => ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'text-red-400'],
        'doc' => ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'text-blue-400'],
        'docx' => ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'text-blue-400'],
        'txt' => ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'text-gray-400'],
        'jpg' => ['icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'text-green-400'],
        'jpeg' => ['icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'text-green-400'],
        'png' => ['icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'text-green-400'],
        'gif' => ['icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'text-green-400'],
        'mp3' => ['icon' => 'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z', 'color' => 'text-purple-400'],
        'wav' => ['icon' => 'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z', 'color' => 'text-purple-400'],
        'mp4' => ['icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 'color' => 'text-pink-400'],
        'avi' => ['icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 'color' => 'text-pink-400'],
        'zip' => ['icon' => 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4', 'color' => 'text-yellow-400'],
        'rar' => ['icon' => 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4', 'color' => 'text-yellow-400'],
        'php' => ['icon' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4', 'color' => 'text-cyan-400'],
        'js' => ['icon' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4', 'color' => 'text-cyan-400'],
        'html' => ['icon' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4', 'color' => 'text-cyan-400'],
        'css' => ['icon' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4', 'color' => 'text-cyan-400'],
    ];
    
    return $iconMap[$ext] ?? ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'text-gray-400'];
}

// Include only the enhanced template
require_once __DIR__ . '/templates/file-explorer-enhanced.php';
?>
}

// Include the enhanced template
require_once __DIR__ . '/templates/file-explorer-enhanced.php';
?>
