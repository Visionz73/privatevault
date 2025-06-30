<?php
// public/file-explorer.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/lib/auth.php';
requireLogin();

$userId = $_SESSION['user_id'];

// Handle file deletion
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM documents WHERE id = ? AND user_id = ?");
    $stmt->execute([$deleteId, $userId]);
    header('Location: /file-explorer.php' . ($_GET['path'] ? '?path=' . urlencode($_GET['path']) : ''));
    exit;
}

// Get current path and view settings
$currentPath = $_GET['path'] ?? '';
$currentView = $_GET['view'] ?? 'grid';
$searchQuery = $_GET['search'] ?? '';
$filterType = $_GET['type'] ?? '';

// Define file type categories
$fileTypes = [
    'documents' => ['pdf', 'doc', 'docx', 'txt', 'rtf'],
    'images' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'],
    'videos' => ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv'],
    'audio' => ['mp3', 'wav', 'flac', 'aac', 'ogg', 'wma'],
    'archives' => ['zip', 'rar', '7z', 'tar', 'gz'],
    'code' => ['js', 'php', 'css', 'html', 'py', 'java', 'cpp', 'c'],
];

// Build SQL query based on filters
$whereClauses = ["user_id = ?", "is_deleted = 0"];
$params = [$userId];

if ($searchQuery) {
    $whereClauses[] = "(title LIKE ? OR filename LIKE ? OR original_name LIKE ?)";
    $searchTerm = '%' . $searchQuery . '%';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if ($filterType && isset($fileTypes[$filterType])) {
    $extensions = $fileTypes[$filterType];
    $placeholders = implode(',', array_fill(0, count($extensions), '?'));
    $whereClauses[] = "LOWER(SUBSTRING_INDEX(filename, '.', -1)) IN ($placeholders)";
    $params = array_merge($params, $extensions);
}

$whereClause = implode(' AND ', $whereClauses);

// Get files
$stmt = $pdo->prepare("
    SELECT d.*, dc.name as category_name, 
           CHAR_LENGTH(d.filename) - CHAR_LENGTH(REPLACE(d.filename, '.', '')) as file_size_calc
    FROM documents d 
    LEFT JOIN document_categories dc ON d.category_id = dc.id 
    WHERE $whereClause
    ORDER BY d.upload_date DESC
");
$stmt->execute($params);
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate file statistics
$totalFiles = count($files);
$totalSize = 0;
$typeCounts = [];

foreach ($files as $file) {
    $ext = strtolower(pathinfo($file['filename'], PATHINFO_EXTENSION));
    $typeCounts[$ext] = ($typeCounts[$ext] ?? 0) + 1;
    
    // Try to get file size from filesystem
    $filePath = __DIR__ . '/../uploads/' . $file['filename'];
    if (file_exists($filePath)) {
        $totalSize += filesize($filePath);
    }
}

// Helper function to format file size
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
    if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
    return $bytes . ' B';
}

// Helper function to get file icon and color
function getFileIcon($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    $iconMap = [
        // Documents
        'pdf' => ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'text-red-400'],
        'doc' => ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'text-blue-400'],
        'docx' => ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'text-blue-400'],
        
        // Images
        'jpg' => ['icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'text-green-400'],
        'jpeg' => ['icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'text-green-400'],
        'png' => ['icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'text-green-400'],
        
        // Audio
        'mp3' => ['icon' => 'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3', 'color' => 'text-purple-400'],
        'wav' => ['icon' => 'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3', 'color' => 'text-purple-400'],
        
        // Video
        'mp4' => ['icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 'color' => 'text-pink-400'],
        'avi' => ['icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 'color' => 'text-pink-400'],
        
        // Archives
        'zip' => ['icon' => 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4', 'color' => 'text-yellow-400'],
        'rar' => ['icon' => 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4', 'color' => 'text-yellow-400'],
    ];
    
    return $iconMap[$ext] ?? ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'text-gray-400'];
}

require_once __DIR__ . '/../templates/file-explorer-fixed.php';
