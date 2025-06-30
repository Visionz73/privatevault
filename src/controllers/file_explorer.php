<?php
// src/controllers/file_explorer.php
// Vollständiger Datei-Explorer Controller mit erweiterten Funktionen
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';
requireLogin();

$user = getUser();
$userId = $_SESSION['user_id'];

// Kategorien für den File Explorer erweitern
$fileCategories = [
    'all' => 'Alle Dateien',
    'documents' => 'Dokumente', 
    'images' => 'Bilder',
    'music' => 'Musik',
    'videos' => 'Videos',
    'archives' => 'Archive',
    'other' => 'Sonstige'
];

// Aktuelle Kategorie und Ansicht
$currentCategory = $_GET['category'] ?? 'all';
$currentView = $_GET['view'] ?? 'grid';
$searchQuery = $_GET['search'] ?? '';
$sortBy = $_GET['sort'] ?? 'upload_date';
$sortOrder = $_GET['order'] ?? 'DESC';

// Löschfunktion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $stmt = $pdo->prepare("UPDATE documents SET is_deleted = 1 WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['delete'], $userId]);
    
    // Redirect without delete parameter
    $redirect_params = $_GET;
    unset($redirect_params['delete']);
    $redirect_url = '/file-explorer.php?' . http_build_query($redirect_params);
    header("Location: $redirect_url");
    exit;
}

// Erweiterte Dateiabfrage basierend auf MIME-Types und Erweiterungen  
$params = [$userId];
$sql = "
    SELECT d.*, dc.name as category_name,
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
    LEFT JOIN document_categories dc ON d.category_id = dc.id
    WHERE d.user_id = ? AND d.is_deleted = 0
";

// Kategorie-Filter
if ($currentCategory !== 'all') {
    if ($currentCategory === 'documents') {
        $sql .= " AND (LOWER(d.filename) REGEXP '\\.(pdf|doc|docx|txt|rtf|odt)$' OR dc.name IN ('Dokumente', 'Verträge', 'Rechnungen', 'Versicherungen'))";
    } elseif ($currentCategory === 'images') {
        $sql .= " AND LOWER(d.filename) REGEXP '\\.(jpg|jpeg|png|gif|bmp|webp|svg)$'";
    } elseif ($currentCategory === 'music') {
        $sql .= " AND LOWER(d.filename) REGEXP '\\.(mp3|wav|flac|aac|ogg|m4a)$'";
    } elseif ($currentCategory === 'videos') {
        $sql .= " AND LOWER(d.filename) REGEXP '\\.(mp4|avi|mkv|mov|wmv|flv|webm)$'";
    } elseif ($currentCategory === 'archives') {
        $sql .= " AND LOWER(d.filename) REGEXP '\\.(zip|rar|7z|tar|gz|bz2)$'";
    } else {
        $sql .= " AND NOT (LOWER(d.filename) REGEXP '\\.(jpg|jpeg|png|gif|bmp|webp|svg|mp3|wav|flac|aac|ogg|m4a|mp4|avi|mkv|mov|wmv|flv|webm|zip|rar|7z|tar|gz|bz2|pdf|doc|docx|txt|rtf|odt)$')";
    }
}

// Suchfunktion
if (!empty($searchQuery)) {
    $sql .= " AND (d.title LIKE ? OR d.filename LIKE ?)";
    $searchTerm = '%' . $searchQuery . '%';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

// Sortierung
$allowedSorts = ['upload_date', 'filename', 'file_size', 'title'];
$allowedOrders = ['ASC', 'DESC'];

if (in_array($sortBy, $allowedSorts) && in_array($sortOrder, $allowedOrders)) {
    $sql .= " ORDER BY d.$sortBy $sortOrder";
} else {
    $sql .= " ORDER BY d.upload_date DESC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Statistiken berechnen
$stats = [];
foreach ($fileCategories as $key => $label) {
    if ($key === 'all') {
        $stats[$key] = count($files);
        continue;
    }
    
    $count = 0;
    foreach ($files as $file) {
        if ($key === 'documents' && 
            (in_array(strtolower(pathinfo($file['filename'], PATHINFO_EXTENSION)), ['pdf', 'doc', 'docx', 'txt', 'rtf', 'odt']) ||
             in_array($file['category_name'], ['Dokumente', 'Verträge', 'Rechnungen', 'Versicherungen']))) {
            $count++;
        } elseif ($file['file_type'] === $key) {
            $count++;
        }
    }
    $stats[$key] = $count;
}

// Gesamtspeicher berechnen
$totalSize = 0;
foreach ($files as $file) {
    $totalSize += $file['file_size'] ?? 0;
}

if ($totalSize < 1024) {
    $formattedTotalSize = $totalSize . ' B';
} elseif ($totalSize < 1048576) {
    $formattedTotalSize = round($totalSize/1024, 1) . ' KB';
} elseif ($totalSize < 1073741824) {
    $formattedTotalSize = round($totalSize/1048576, 1) . ' MB';
} else {
    $formattedTotalSize = round($totalSize/1073741824, 2) . ' GB';
}

// Template laden
require_once __DIR__ . '/../../templates/file_explorer.php';
?>
