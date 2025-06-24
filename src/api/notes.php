<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/db.php';

header('Content-Type: application/json');

// Check if session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized - not logged in']);
    exit;
}

$user = getUser();
if (!$user) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized - user not found']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// Verify database connection
require_once __DIR__ . '/../../config.php';

if (!isset($pdo) || !$pdo) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

try {
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
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}

function handleGetNotes($pdo, $userId) {
    try {
        $archived = $_GET['archived'] ?? 'false';
        $limit = min(intval($_GET['limit'] ?? 50), 100);
        
        // Check if tables exist and create them if needed
        $result = $pdo->query("SHOW TABLES LIKE 'notes'");
        $notesTableExists = $result->rowCount() > 0;
        
        if (!$notesTableExists) {
            $pdo->exec("CREATE TABLE IF NOT EXISTS notes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                title VARCHAR(255) NOT NULL,
                content TEXT,
                color VARCHAR(7) DEFAULT '#fbbf24',
                is_pinned BOOLEAN DEFAULT FALSE,
                is_archived BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )");
        }
        
        $result = $pdo->query("SHOW TABLES LIKE 'note_tags'");
        $tagsTableExists = $result->rowCount() > 0;
        
        if (!$tagsTableExists) {
            $pdo->exec("CREATE TABLE IF NOT EXISTS note_tags (
                id INT AUTO_INCREMENT PRIMARY KEY,
                note_id INT NOT NULL,
                tag_name VARCHAR(100) NOT NULL,
                FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE
            )");
        }        
        // Try to get notes, if tables don't exist yet, return empty array
        try {
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
        } catch (Exception $e) {
            // If tables don't exist or there's an error, return empty array
            $notes = [];
        }
        
        // Format tags as array
        foreach ($notes as &$note) {
            $note['tags'] = $note['tags'] ? explode(',', $note['tags']) : [];
        }
        
        echo json_encode(['success' => true, 'notes' => $notes]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error loading notes: ' . $e->getMessage()]);
    }
}

function handleCreateNote($pdo, $userId, $input) {
    $title = trim($input['title'] ?? '');
    $content = trim($input['content'] ?? '');
    $color = $input['color'] ?? '#fbbf24';
    $tags = $input['tags'] ?? [];
    
    if (empty($title)) {
        http_response_code(400);
        echo json_encode(['error' => 'Title is required']);
        return;
    }
    
    $pdo->beginTransaction();
    
    try {
        $sql = "INSERT INTO notes (user_id, title, content, color) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId, $title, $content, $color]);
        $noteId = $pdo->lastInsertId();
        
        // Add tags
        if (!empty($tags)) {
            $tagSql = "INSERT INTO note_tags (note_id, tag_name) VALUES (?, ?)";
            $tagStmt = $pdo->prepare($tagSql);
            foreach ($tags as $tag) {
                $tag = trim($tag);
                if (!empty($tag)) {
                    $tagStmt->execute([$noteId, $tag]);
                }
            }
        }
        
        $pdo->commit();
        echo json_encode(['success' => true, 'id' => $noteId, 'message' => 'Notiz erstellt']);
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function handleUpdateNote($pdo, $userId, $input) {
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
        $sql = "UPDATE notes SET title = ?, content = ?, color = ?, is_pinned = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $content, $color, $isPinned ? 1 : 0, $noteId]);
        
        // Update tags
        $pdo->prepare("DELETE FROM note_tags WHERE note_id = ?")->execute([$noteId]);
        if (!empty($tags)) {
            $tagSql = "INSERT INTO note_tags (note_id, tag_name) VALUES (?, ?)";
            $tagStmt = $pdo->prepare($tagSql);
            foreach ($tags as $tag) {
                $tag = trim($tag);
                if (!empty($tag)) {
                    $tagStmt->execute([$noteId, $tag]);
                }
            }
        }
          $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Notiz gespeichert']);
    } catch (Exception $e) {
        $pdo->rollBack();
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
        echo json_encode(['success' => true, 'message' => 'Notiz gelöscht']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Note not found']);
    }
}
?>
