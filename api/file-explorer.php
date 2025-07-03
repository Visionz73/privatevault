<?php
// API endpoint für AJAX-basierten File-Explorer
require_once __DIR__ . '/../src/lib/db.php';
require_once __DIR__ . '/../src/lib/auth.php';

requireLogin();
header('Content-Type: application/json; charset=utf-8');

session_start();
$userId = $_SESSION['user_id'];

// Eingabe sauber lesen und validieren
$currentCategory = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_STRING) ?: 'all';
$searchQuery     = filter_input(INPUT_GET, 'search',   FILTER_SANITIZE_STRING) ?: '';
$sortBy          = filter_input(INPUT_GET, 'sort',     FILTER_SANITIZE_STRING) ?: 'upload_date';
$sortOrder       = strtoupper(filter_input(INPUT_GET, 'order', FILTER_SANITIZE_STRING) ?: 'DESC');

// Soft-delete per AJAX
if (isset($_GET['delete']) && ctype_digit($_GET['delete'])) {
    $stmtDel = $pdo->prepare("
        UPDATE documents
        SET is_deleted = 1
        WHERE id = :id AND user_id = :uid
    ");
    $stmtDel->execute([
        ':id'  => $_GET['delete'],
        ':uid' => $userId,
    ]);
}

// Whitelists für Sortierung
$allowedSorts  = ['upload_date','filename','file_size','title'];
$allowedOrders = ['ASC','DESC'];
if (!in_array($sortBy,  $allowedSorts,  true)) $sortBy  = 'upload_date';
if (!in_array($sortOrder, $allowedOrders, true)) $sortOrder = 'DESC';

// Basis-Query
$sql = "
    SELECT 
        d.id,
        d.filename,
        d.title,
        d.file_size,
        d.upload_date,
        -- Dateityp bestimmen
        CASE
            WHEN LOWER(d.filename) REGEXP '\\.(jpg|jpeg|png|gif|bmp|webp|svg)$' THEN 'images'
            WHEN LOWER(d.filename) REGEXP '\\.(mp3|wav|flac|aac|ogg|m4a)$'    THEN 'music'
            WHEN LOWER(d.filename) REGEXP '\\.(mp4|avi|mkv|mov|wmv|flv|webm)$' THEN 'videos'
            WHEN LOWER(d.filename) REGEXP '\\.(zip|rar|7z|tar|gz|bz2)$'       THEN 'archives'
            WHEN LOWER(d.filename) REGEXP '\\.(pdf|doc|docx|txt|rtf|odt)$'    THEN 'documents'
            ELSE 'other'
        END AS file_type,
        -- Formatierte Größe
        CASE 
            WHEN d.file_size < 1024 THEN CONCAT(d.file_size, ' B')
            WHEN d.file_size < 1048576 THEN CONCAT(ROUND(d.file_size/1024,1), ' KB')
            WHEN d.file_size < 1073741824 THEN CONCAT(ROUND(d.file_size/1048576,1), ' MB')
            ELSE CONCAT(ROUND(d.file_size/1073741824,2), ' GB')
        END AS formatted_size
    FROM documents d
    WHERE d.user_id = :uid
      AND d.is_deleted = 0
";

// Parameter-Array
$params = [':uid' => $userId];

// Kategorie-Filter (nur, wenn nicht 'all')
if ($currentCategory !== 'all') {
    $sql .= " AND (
        CASE
            WHEN LOWER(d.filename) REGEXP '\\.(jpg|jpeg|png|gif|bmp|webp|svg)$' THEN 'images'
            WHEN LOWER(d.filename) REGEXP '\\.(mp3|wav|flac|aac|ogg|m4a)$'    THEN 'music'
            WHEN LOWER(d.filename) REGEXP '\\.(mp4|avi|mkv|mov|wmv|flv|webm)$' THEN 'videos'
            WHEN LOWER(d.filename) REGEXP '\\.(zip|rar|7z|tar|gz|bz2)$'       THEN 'archives'
            WHEN LOWER(d.filename) REGEXP '\\.(pdf|doc|docx|txt|rtf|odt)$'    THEN 'documents'
            ELSE 'other'
        END
    ) = :category";
    $params[':category'] = $currentCategory;
}

// Such-Filter
if ($searchQuery !== '') {
    $sql .= " AND (d.title LIKE :term OR d.filename LIKE :term)";
    $params[':term'] = '%' . $searchQuery . '%';
}

// Sortierung anhängen
$sql .= " ORDER BY d.{$sortBy} {$sortOrder}";

// Statement ausführen
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Kategorie-Statistiken bauen
$stats = ['all' => count($files)];
foreach ($files as $f) {
    $ft = $f['file_type'];
    $stats[$ft] = ($stats[$ft] ?? 0) + 1;
}

// Antwort als JSON
echo json_encode([
    'status' => 'success',
    'files'  => $files,
    'stats'  => $stats,
], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
