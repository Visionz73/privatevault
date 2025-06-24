<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user = getUser();
$method = $_SERVER['REQUEST_METHOD'];

// Get input data
$input = null;
if ($method === 'POST' || $method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON']);
        exit;
    }
}

try {
    $pdo = getDBConnection();
    
    // Create tables if they don't exist
    createNotesTablesIfNotExist($pdo);
    
    switch ($method) {
        case 'GET':
            handleGetNotes($pdo, $user['id']);
            break;
            
        case 'POST':
            handleCreateNote($pdo, $user['id'], $input);
            break;
            
        case 'PUT':
            handleUpdateNote($pdo, $user['id'], $input);
            break;
            
        case 'DELETE':
            handleDeleteNote($pdo, $user['id'], $_GET['id'] ?? null);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    error_log("Notes API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}

function createNotesTablesIfNotExist($pdo) {
    $createNotesTable = "
        CREATE TABLE IF NOT EXISTS notes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            content TEXT,
            color VARCHAR(7) DEFAULT '#fbbf24',
            is_pinned BOOLEAN DEFAULT FALSE,
            is_archived BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_user_id (user_id),
            INDEX idx_created_at (created_at),
            INDEX idx_pinned (is_pinned),
            INDEX idx_archived (is_archived)
        )";
    
    $createTagsTable = "
        CREATE TABLE IF NOT EXISTS note_tags (
            id INT AUTO_INCREMENT PRIMARY KEY,
            note_id INT NOT NULL,
            tag_name VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_note_id (note_id),
            INDEX idx_tag_name (tag_name),
            UNIQUE KEY unique_note_tag (note_id, tag_name)
        )";
    
    $pdo->exec($createNotesTable);
    $pdo->exec($createTagsTable);
}

function handleGetNotes($pdo, $userId) {
    $archived = $_GET['archived'] ?? 'false';
    $limit = min(intval($_GET['limit'] ?? 50), 100);
    
    $sql = "SELECT n.*, GROUP_CONCAT(nt.tag_name) as tags 
            FROM notes n 
            LEFT JOIN note_tags nt ON n.id = nt.note_id 
            WHERE n.user_id = ? AND n.is_archived = ?
            GROUP BY n.id 
            ORDER BY n.is_pinned DESC, n.updated_at DESC 
            LIMIT ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $archived === 'true' ? 1 : 0, $limit]);
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format tags as array
    foreach ($notes as &$note) {
        $note['tags'] = $note['tags'] ? explode(',', $note['tags']) : [];
        $note['is_pinned'] = (bool)$note['is_pinned'];
        $note['is_archived'] = (bool)$note['is_archived'];
    }
    
    echo json_encode(['notes' => $notes, 'count' => count($notes)]);
}

function handleCreateNote($pdo, $userId, $input) {
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'No input data']);
        return;
    }
    
    $title = trim($input['title'] ?? '');
    $content = trim($input['content'] ?? '');
    $color = $input['color'] ?? '#fbbf24';
    $tags = $input['tags'] ?? [];
    
    if (empty($title)) {
        http_response_code(400);
        echo json_encode(['error' => 'Title is required']);
        return;
    }
    
    // Validate color format
    if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
        $color = '#fbbf24';
    }
    
    $pdo->beginTransaction();
    
    try {
        $sql = "INSERT INTO notes (user_id, title, content, color) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId, $title, $content, $color]);
        $noteId = $pdo->lastInsertId();
        
        // Add tags if provided
        if (!empty($tags) && is_array($tags)) {
            $tagSql = "INSERT INTO note_tags (note_id, tag_name) VALUES (?, ?)";
            $tagStmt = $pdo->prepare($tagSql);
            foreach ($tags as $tag) {
                $tag = trim($tag);
                if (!empty($tag)) {
                    try {
                        $tagStmt->execute([$noteId, $tag]);
                    } catch (PDOException $e) {
                        // Ignore duplicate tag errors
                        if ($e->getCode() !== '23000') {
                            throw $e;
                        }
                    }
                }
            }
        }
        
        $pdo->commit();
        echo json_encode(['success' => true, 'id' => $noteId, 'message' => 'Note created successfully']);
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error creating note: " . $e->getMessage());
        throw $e;
    }
}

function handleUpdateNote($pdo, $userId, $input) {
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'No input data']);
        return;
    }
    
    $noteId = $input['id'] ?? null;
    $title = trim($input['title'] ?? '');
    $content = trim($input['content'] ?? '');
    $color = $input['color'] ?? '#fbbf24';
    $isPinned = $input['is_pinned'] ?? false;
    $tags = $input['tags'] ?? [];
    
    if (empty($noteId) || empty($title)) {
        http_response_code(400);
        echo json_encode(['error' => 'ID and title are required']);
        return;
    }
    
    // Validate color format
    if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
        $color = '#fbbf24';
    }
    
    $pdo->beginTransaction();
    
    try {
        // Check ownership
        $checkSql = "SELECT id FROM notes WHERE id = ? AND user_id = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$noteId, $userId]);
        if (!$checkStmt->fetch()) {
            http_response_code(403);
            echo json_encode(['error' => 'Note not found or access denied']);
            return;
        }
        
        // Update note
        $sql = "UPDATE notes SET title = ?, content = ?, color = ?, is_pinned = ?, updated_at = NOW() WHERE id = ? AND user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $content, $color, $isPinned ? 1 : 0, $noteId, $userId]);
        
        // Update tags
        $pdo->prepare("DELETE FROM note_tags WHERE note_id = ?")->execute([$noteId]);
        if (!empty($tags) && is_array($tags)) {
            $tagSql = "INSERT INTO note_tags (note_id, tag_name) VALUES (?, ?)";
            $tagStmt = $pdo->prepare($tagSql);
            foreach ($tags as $tag) {
                $tag = trim($tag);
                if (!empty($tag)) {
                    try {
                        $tagStmt->execute([$noteId, $tag]);
                    } catch (PDOException $e) {
                        // Ignore duplicate tag errors
                        if ($e->getCode() !== '23000') {
                            throw $e;
                        }
                    }
                }
            }
        }
        
        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Note updated successfully']);
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error updating note: " . $e->getMessage());
        throw $e;
    }
}

function handleDeleteNote($pdo, $userId, $noteId) {
    if (empty($noteId)) {
        http_response_code(400);
        echo json_encode(['error' => 'Note ID is required']);
        return;
    }
    
    $sql = "DELETE FROM notes WHERE id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$noteId, $userId]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Note deleted successfully']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Note not found']);
    }
}
?>
