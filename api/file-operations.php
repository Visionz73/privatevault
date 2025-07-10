<?php
// api/file-operations.php
// Comprehensive file operations API

declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/lib/auth.php';

session_start();
requireLogin();

header('Content-Type: application/json');

$userId = (int)$_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($method) {
        case 'POST':
            handlePost($action, $userId, $pdo);
            break;
        case 'PUT':
            handlePut($action, $userId, $pdo);
            break;
        case 'DELETE':
            handleDelete($action, $userId, $pdo);
            break;
        case 'GET':
            handleGet($action, $userId, $pdo);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    error_log("File operations error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}

function handlePost($action, $userId, $pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    switch ($action) {
        case 'move':
            moveFile($input, $userId, $pdo);
            break;
        case 'copy':
            copyFile($input, $userId, $pdo);
            break;
        case 'create-folder':
            createFolder($input, $userId, $pdo);
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Unknown action']);
            break;
    }
}

function handlePut($action, $userId, $pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    switch ($action) {
        case 'rename':
            renameFile($input, $userId, $pdo);
            break;
        case 'update-metadata':
            updateMetadata($input, $userId, $pdo);
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Unknown action']);
            break;
    }
}

function handleDelete($action, $userId, $pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    switch ($action) {
        case 'delete':
            deleteFile($input, $userId, $pdo);
            break;
        case 'bulk-delete':
            bulkDelete($input, $userId, $pdo);
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Unknown action']);
            break;
    }
}

function handleGet($action, $userId, $pdo) {
    switch ($action) {
        case 'stats':
            getFileStats($userId, $pdo);
            break;
        case 'search':
            searchFiles($_GET, $userId, $pdo);
            break;
        case 'recent':
            getRecentFiles($_GET, $userId, $pdo);
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Unknown action']);
            break;
    }
}

function moveFile($input, $userId, $pdo) {
    $fileId = $input['file_id'] ?? null;
    $targetCategoryId = $input['target_category_id'] ?? null;
    
    if (!$fileId) {
        http_response_code(400);
        echo json_encode(['error' => 'File ID required']);
        return;
    }
    
    $stmt = $pdo->prepare("
        UPDATE documents 
        SET category_id = :cat_id, updated_at = NOW() 
        WHERE id = :id AND user_id = :uid
    ");
    
    $result = $stmt->execute([
        ':cat_id' => $targetCategoryId,
        ':id' => $fileId,
        ':uid' => $userId
    ]);
    
    if ($result && $stmt->rowCount() > 0) {
        // Log activity
        logActivity($userId, 'file_move', [
            'file_id' => $fileId,
            'target_category_id' => $targetCategoryId
        ], $pdo);
        
        echo json_encode(['success' => true, 'message' => 'Datei verschoben']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Datei nicht gefunden']);
    }
}

function renameFile($input, $userId, $pdo) {
    $fileId = $input['file_id'] ?? null;
    $newName = trim($input['new_name'] ?? '');
    
    if (!$fileId || empty($newName)) {
        http_response_code(400);
        echo json_encode(['error' => 'File ID and new name required']);
        return;
    }
    
    $stmt = $pdo->prepare("
        UPDATE documents 
        SET title = :title, updated_at = NOW() 
        WHERE id = :id AND user_id = :uid
    ");
    
    $result = $stmt->execute([
        ':title' => $newName,
        ':id' => $fileId,
        ':uid' => $userId
    ]);
    
    if ($result && $stmt->rowCount() > 0) {
        logActivity($userId, 'file_rename', [
            'file_id' => $fileId,
            'new_name' => $newName
        ], $pdo);
        
        echo json_encode(['success' => true, 'message' => 'Datei umbenannt']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Datei nicht gefunden']);
    }
}

function deleteFile($input, $userId, $pdo) {
    $fileId = $input['file_id'] ?? null;
    
    if (!$fileId) {
        http_response_code(400);
        echo json_encode(['error' => 'File ID required']);
        return;
    }
    
    // Get file info first
    $stmt = $pdo->prepare("SELECT * FROM documents WHERE id = :id AND user_id = :uid");
    $stmt->execute([':id' => $fileId, ':uid' => $userId]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$file) {
        http_response_code(404);
        echo json_encode(['error' => 'Datei nicht gefunden']);
        return;
    }
    
    // Delete from database
    $stmt = $pdo->prepare("DELETE FROM documents WHERE id = :id AND user_id = :uid");
    $result = $stmt->execute([':id' => $fileId, ':uid' => $userId]);
    
    if ($result && $stmt->rowCount() > 0) {
        // Try to delete physical file
        $filePath = __DIR__ . '/../uploads/' . $file['filename'];
        if (is_file($filePath)) {
            unlink($filePath);
        }
        
        logActivity($userId, 'file_delete', [
            'file_id' => $fileId,
            'filename' => $file['filename']
        ], $pdo);
        
        echo json_encode(['success' => true, 'message' => 'Datei gelöscht']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Fehler beim Löschen']);
    }
}

function getFileStats($userId, $pdo) {
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_files,
            SUM(file_size) as total_size,
            AVG(file_size) as avg_size,
            MIN(upload_date) as oldest_file,
            MAX(upload_date) as newest_file
        FROM documents 
        WHERE user_id = :uid
    ");
    
    $stmt->execute([':uid' => $userId]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get file type breakdown
    $stmt = $pdo->prepare("
        SELECT 
            LOWER(SUBSTRING_INDEX(filename, '.', -1)) as extension,
            COUNT(*) as count
        FROM documents 
        WHERE user_id = :uid
        GROUP BY extension
        ORDER BY count DESC
        LIMIT 10
    ");
    
    $stmt->execute([':uid' => $userId]);
    $typeBreakdown = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'type_breakdown' => $typeBreakdown
    ]);
}

function searchFiles($params, $userId, $pdo) {
    $query = $params['q'] ?? '';
    $limit = min((int)($params['limit'] ?? 20), 100);
    $offset = (int)($params['offset'] ?? 0);
    
    if (empty($query)) {
        echo json_encode(['success' => true, 'files' => []]);
        return;
    }
    
    $stmt = $pdo->prepare("
        SELECT d.*, dc.name as category_name
        FROM documents d
        LEFT JOIN document_categories dc ON d.category_id = dc.id
        WHERE d.user_id = :uid 
        AND (d.title LIKE :query OR d.filename LIKE :query OR d.original_name LIKE :query)
        ORDER BY d.upload_date DESC
        LIMIT :limit OFFSET :offset
    ");
    
    $stmt->execute([
        ':uid' => $userId,
        ':query' => '%' . $query . '%',
        ':limit' => $limit,
        ':offset' => $offset
    ]);
    
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'files' => $files,
        'query' => $query,
        'count' => count($files)
    ]);
}

function logActivity($userId, $action, $details, $pdo) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO activity_log (user_id, action, details, created_at) 
            VALUES (:uid, :action, :details, NOW())
        ");
        
        $stmt->execute([
            ':uid' => $userId,
            ':action' => $action,
            ':details' => json_encode($details)
        ]);
    } catch (PDOException $e) {
        error_log("Activity logging error: " . $e->getMessage());
    }
}
?>
