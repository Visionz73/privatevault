<?php
// api/download.php
// Enhanced file download with security and logging

declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/lib/auth.php';

session_start();
requireLogin();

$userId = (int)$_SESSION['user_id'];
$fileId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$fileId) {
    http_response_code(400);
    exit('Invalid file ID');
}

try {
    // Get file information with security check
    $stmt = $pdo->prepare("
        SELECT d.*, dc.name as category_name 
        FROM documents d 
        LEFT JOIN document_categories dc ON d.category_id = dc.id 
        WHERE d.id = :id AND d.user_id = :uid
    ");
    $stmt->execute([':id' => $fileId, ':uid' => $userId]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$file) {
        http_response_code(404);
        exit('File not found');
    }
    
    $filePath = __DIR__ . '/../uploads/' . $file['filename'];
    
    if (!is_file($filePath)) {
        http_response_code(404);
        exit('File not found on disk');
    }
    
    // Log download activity
    try {
        $stmt = $pdo->prepare("
            INSERT INTO activity_log (user_id, action, details, created_at) 
            VALUES (:uid, 'file_download', :details, NOW())
        ");
        $stmt->execute([
            ':uid' => $userId,
            ':details' => json_encode([
                'file_id' => $fileId,
                'filename' => $file['filename'],
                'original_name' => $file['original_name'],
                'category' => $file['category_name']
            ])
        ]);
    } catch (PDOException $e) {
        // Log error but don't stop download
        error_log("Download logging error: " . $e->getMessage());
    }
    
    // Set appropriate headers
    $displayName = $file['original_name'] ?: $file['filename'];
    $fileSize = filesize($filePath);
    $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
    
    header('Content-Type: ' . $mimeType);
    header('Content-Disposition: attachment; filename="' . $displayName . '"');
    header('Content-Length: ' . $fileSize);
    header('Cache-Control: private, max-age=0, no-cache');
    header('Pragma: public');
    
    // Output file
    if ($fileSize > 8192) {
        // Large file - stream it
        $handle = fopen($filePath, 'rb');
        while (!feof($handle)) {
            echo fread($handle, 8192);
            ob_flush();
            flush();
        }
        fclose($handle);
    } else {
        // Small file - output directly
        readfile($filePath);
    }
    
} catch (PDOException $e) {
    error_log("Download error: " . $e->getMessage());
    http_response_code(500);
    exit('Database error');
} catch (Exception $e) {
    error_log("Download error: " . $e->getMessage());
    http_response_code(500);
    exit('Server error');
}
?>
