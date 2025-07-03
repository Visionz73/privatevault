<?php
// API endpoint for AJAX-based File Explorer
require_once __DIR__ . '/../src/lib/db.php';
require_once __DIR__ . '/../src/lib/auth.php';
requireLogin();
header('Content-Type: application/json');

$userId = $_SESSION['user_id'];
$currentCategory = $_GET['category'] ?? 'all';
$searchQuery = $_GET['search'] ?? '';
$sortBy = $_GET['sort'] ?? 'upload_date';
$sortOrder = $_GET['order'] ?? 'DESC';

$
// Handle delete requests via AJAX
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $stmtDel = $pdo->prepare("UPDATE documents SET is_deleted = 1 WHERE id = ? AND user_id = ?");
    $stmtDel->execute([$_GET['delete'], $userId]);
}
// Build SQL as in controller
$params = [$userId];
$sql = "SELECT d.id, d.filename, d.title, d.file_size, d.upload_date,
           CASE
             WHEN LOWER(d.filename) REGEXP '\\.(jpg|jpeg|png|gif|bmp|webp|svg)$' THEN 'images'
             WHEN LOWER(d.filename) REGEXP '\\.(mp3|wav|flac|aac|ogg|m4a)$' THEN 'music'
             WHEN LOWER(d.filename) REGEXP '\\.(mp4|avi|mkv|mov|wmv|flv|webm)$' THEN 'videos'
             WHEN LOWER(d.filename) REGEXP '\\.(zip|rar|7z|tar|gz|bz2)$' THEN 'archives'
             WHEN LOWER(d.filename) REGEXP '\\.(pdf|doc|docx|txt|rtf|odt)$' THEN 'documents'
             ELSE 'other'
           END as file_type,
           CASE 
               WHEN d.file_size < 1024 THEN CONCAT(d.file_size, ' B')
               WHEN d.file_size < 1048576 THEN CONCAT(ROUND(d.file_size/1024, 1), ' KB')
               WHEN d.file_size < 1073741824 THEN CONCAT(ROUND(d.file_size/1048576, 1), ' MB')
               ELSE CONCAT(ROUND(d.file_size/1073741824, 2), ' GB')
           END as formatted_size
    FROM documents d
    WHERE d.user_id = ? AND d.is_deleted = 0";

if ($currentCategory !== 'all') {
    $sql .= " AND (file_type = '" . $currentCategory . "')";
}
if (!empty($searchQuery)) {
    $sql .= " AND (d.title LIKE ? OR d.filename LIKE ?)";
    $term = "%" . $searchQuery . "%";
    $params[] = $term;
    $params[] = $term;
}
$allowedSorts = ['upload_date','filename','file_size','title'];
$allowedOrders = ['ASC','DESC'];
if (in_array($sortBy,$allowedSorts) && in_array($sortOrder,$allowedOrders)) {
    $sql .= " ORDER BY d.$sortBy $sortOrder";
} else {
    $sql .= " ORDER BY d.upload_date DESC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Compute category statistics
$stats = ['all' => count($files)];
foreach ($files as $f) {
    $ft = $f['file_type'];
    if (!isset($stats[$ft])) {
        $stats[$ft] = 0;
    }
    $stats[$ft]++;
}
// Return JSON response with files and stats
echo json_encode(['status' => 'success', 'files' => $files, 'stats' => $stats]);
