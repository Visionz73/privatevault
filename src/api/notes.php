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

// $pdo is already available from db.php which includes config.php
if (!isset($pdo) || !$pdo) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

try {
    // Ensure tables exist (silently)
    // Note: We need to ensure tables exist but without outputting text
    // The enhanced_notes_tables.php outputs text, so we'll create tables manually here
    
    // Create basic notes table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS notes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        content TEXT,
        color VARCHAR(7) DEFAULT '#fbbf24',
        is_pinned BOOLEAN DEFAULT FALSE,
        is_archived BOOLEAN DEFAULT FALSE,
        node_position_x FLOAT DEFAULT NULL,
        node_position_y FLOAT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id),
        INDEX idx_created_at (created_at),
        INDEX idx_pinned (is_pinned),
        INDEX idx_archived (is_archived)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Create note_tags table if it doesn't exist  
    $pdo->exec("CREATE TABLE IF NOT EXISTS note_tags (
        id INT AUTO_INCREMENT PRIMARY KEY,
        note_id INT NOT NULL,
        tag_name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_note_id (note_id),
        INDEX idx_tag_name (tag_name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Create note_links table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS note_links (
        id INT AUTO_INCREMENT PRIMARY KEY,
        source_note_id INT NOT NULL,
        target_note_id INT NOT NULL,
        link_type ENUM('reference', 'mention', 'relates_to', 'follows_from') DEFAULT 'reference',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        created_by INT NOT NULL,
        INDEX idx_source_note (source_note_id),
        INDEX idx_target_note (target_note_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    $action = $_GET['action'] ?? '';
    
    switch ($method) {
        case 'GET':
            switch ($action) {
                case 'graph':
                    handleGetGraphData($pdo, $user['id']);
                    break;
                case 'links':
                    handleGetNoteLinks($pdo, $user['id'], $_GET['id'] ?? null);
                    break;
                case 'search':
                    handleSearchNotes($pdo, $user['id'], $_GET['q'] ?? '');
                    break;
                case 'clusters':
                    handleGetClusters($pdo, $user['id']);
                    break;
                case 'stats':
                    handleGetStats($pdo, $user['id']);
                    break;
                default:
                    handleGetNotes($pdo, $user['id']);
            }
            break;
            
        case 'POST':
            switch ($action) {
                case 'link':
                    handleCreateLink($pdo, $user['id'], $input);
                    break;
                case 'cluster':
                    handleCreateCluster($pdo, $user['id'], $input);
                    break;
                case 'reminder':
                    handleCreateReminder($pdo, $user['id'], $input);
                    break;
                default:
                    handleCreateNote($pdo, $user['id'], $input);
            }
            break;
            
        case 'PUT':
            switch ($action) {
                case 'position':
                    handleUpdateNodePosition($pdo, $user['id'], $input);
                    break;
                default:
                    handleUpdateNote($pdo, $user['id'], $input);
            }
            break;
            
        case 'DELETE':
            switch ($action) {
                case 'link':
                    handleDeleteLink($pdo, $user['id'], $_GET['id'] ?? null);
                    break;
                default:
                    handleDeleteNote($pdo, $user['id'], $_GET['id'] ?? null);
            }
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
        
        // Get notes with error handling
        // Note: Cannot parameterize LIMIT in MySQL prepared statements, embed it directly
        $sql = "SELECT n.id, n.user_id, n.title, n.content, n.color, n.is_pinned, n.is_archived, n.created_at, n.updated_at,
                   GROUP_CONCAT(nt.tag_name) as tags 
            FROM notes n 
            LEFT JOIN note_tags nt ON n.id = nt.note_id 
            WHERE n.user_id = ? AND n.is_archived = ?
            GROUP BY n.id, n.user_id, n.title, n.content, n.color, n.is_pinned, n.is_archived, n.created_at, n.updated_at
            ORDER BY n.is_pinned DESC, n.updated_at DESC 
            LIMIT " . $limit;
        
        $stmt = $pdo->prepare($sql);
        // Execute with only user_id and archived flag
        $stmt->execute([$userId, $archived === 'true' ? 1 : 0]);
        $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format tags as array and ensure all fields are present
        foreach ($notes as &$note) {
            $note['tags'] = $note['tags'] ? explode(',', $note['tags']) : [];
            $note['is_pinned'] = (bool)$note['is_pinned'];
            $note['is_archived'] = (bool)$note['is_archived'];
            $note['id'] = (int)$note['id'];
            $note['user_id'] = (int)$note['user_id'];
        }
        
        echo json_encode([
            'success' => true, 
            'notes' => $notes,
            'count' => count($notes),
            'debug' => [
                'userId' => $userId,
                'archived' => $archived,
                'limit' => $limit
            ]
        ]);
        
    } catch (Exception $e) {
        error_log("Error in handleGetNotes: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'error' => 'Error loading notes: ' . $e->getMessage(),
            'debug' => [
                'userId' => $userId ?? 'unknown',
                'file' => __FILE__,
                'line' => __LINE__
            ]
        ]);
    }
}

function handleCreateNote($pdo, $userId, $input) {
    try {
        $title = trim($input['title'] ?? '');
        $content = trim($input['content'] ?? '');
        $color = $input['color'] ?? '#fbbf24';
        $tags = $input['tags'] ?? [];
        
        if (empty($title)) {
            http_response_code(400);
            echo json_encode(['error' => 'Title is required']);
            return;
        }
        
        // Ensure tables exist
        $pdo->exec("CREATE TABLE IF NOT EXISTS notes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            content TEXT,
            color VARCHAR(7) DEFAULT '#fbbf24',
            is_pinned BOOLEAN DEFAULT FALSE,
            is_archived BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_user_archived (user_id, is_archived)
        )");
        
        $pdo->exec("CREATE TABLE IF NOT EXISTS note_tags (
            id INT AUTO_INCREMENT PRIMARY KEY,
            note_id INT NOT NULL,
            tag_name VARCHAR(100) NOT NULL,
            INDEX idx_note_id (note_id)
        )");
        
        $pdo->beginTransaction();
        
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
        echo json_encode([
            'success' => true, 
            'id' => $noteId, 
            'message' => 'Notiz erstellt',
            'debug' => [
                'title' => $title,
                'userId' => $userId,
                'noteId' => $noteId
            ]
        ]);
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Error creating note: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Error creating note: ' . $e->getMessage()]);
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

// New Second Brain API Functions

function handleGetGraphData($pdo, $userId) {
    try {
        // Get all notes with their positions and connections
        $sql = "SELECT n.id, n.title, n.color, n.is_pinned, n.node_position_x, n.node_position_y,
                   n.created_at, n.updated_at,
                   GROUP_CONCAT(DISTINCT nt.tag_name) as tags,
                   COUNT(DISTINCT CASE WHEN nl1.source_note_id = n.id THEN nl1.target_note_id END) as outgoing_links,
                   COUNT(DISTINCT CASE WHEN nl1.target_note_id = n.id THEN nl1.source_note_id END) as incoming_links
            FROM notes n 
            LEFT JOIN note_tags nt ON n.id = nt.note_id 
            LEFT JOIN note_links nl1 ON (n.id = nl1.source_note_id OR n.id = nl1.target_note_id)
            WHERE n.user_id = ? AND n.is_archived = 0
            GROUP BY n.id
            ORDER BY n.updated_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        $nodes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get all links between notes
        $linkSql = "SELECT nl.source_note_id, nl.target_note_id, nl.link_type,
                       n1.title as source_title, n2.title as target_title
                FROM note_links nl
                JOIN notes n1 ON nl.source_note_id = n1.id
                JOIN notes n2 ON nl.target_note_id = n2.id
                WHERE n1.user_id = ? AND n2.user_id = ?";
        
        $linkStmt = $pdo->prepare($linkSql);
        $linkStmt->execute([$userId, $userId]);
        $links = $linkStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format nodes
        foreach ($nodes as &$node) {
            $node['tags'] = $node['tags'] ? explode(',', $node['tags']) : [];
            $node['id'] = (int)$node['id'];
            $node['is_pinned'] = (bool)$node['is_pinned'];
            $node['outgoing_links'] = (int)$node['outgoing_links'];
            $node['incoming_links'] = (int)$node['incoming_links'];
            $node['node_position_x'] = $node['node_position_x'] ? (float)$node['node_position_x'] : null;
            $node['node_position_y'] = $node['node_position_y'] ? (float)$node['node_position_y'] : null;
        }
        
        echo json_encode([
            'success' => true,
            'nodes' => $nodes,
            'links' => $links,
            'stats' => [
                'total_nodes' => count($nodes),
                'total_links' => count($links)
            ]
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error loading graph data: ' . $e->getMessage()]);
    }
}

function handleGetNoteLinks($pdo, $userId, $noteId) {
    if (!$noteId) {
        http_response_code(400);
        echo json_encode(['error' => 'Note ID required']);
        return;
    }
    
    try {
        // Get outgoing links
        $outgoingSql = "SELECT nl.target_note_id as note_id, n.title, nl.link_type, nl.created_at
                       FROM note_links nl
                       JOIN notes n ON nl.target_note_id = n.id
                       WHERE nl.source_note_id = ? AND n.user_id = ?";
        
        $stmt = $pdo->prepare($outgoingSql);
        $stmt->execute([$noteId, $userId]);
        $outgoing = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get incoming links (backlinks)
        $incomingSql = "SELECT nl.source_note_id as note_id, n.title, nl.link_type, nl.created_at
                       FROM note_links nl
                       JOIN notes n ON nl.source_note_id = n.id
                       WHERE nl.target_note_id = ? AND n.user_id = ?";
        
        $stmt = $pdo->prepare($incomingSql);
        $stmt->execute([$noteId, $userId]);
        $incoming = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'outgoing_links' => $outgoing,
            'incoming_links' => $incoming
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error loading note links: ' . $e->getMessage()]);
    }
}

function handleSearchNotes($pdo, $userId, $query) {
    if (empty($query)) {
        echo json_encode(['success' => true, 'notes' => []]);
        return;
    }
    
    try {
        $sql = "SELECT n.id, n.title, n.content, n.color, n.created_at,
                   GROUP_CONCAT(DISTINCT nt.tag_name) as tags,
                   MATCH(n.title, n.content) AGAINST(? IN NATURAL LANGUAGE MODE) as relevance
            FROM notes n
            LEFT JOIN note_tags nt ON n.id = nt.note_id
            WHERE n.user_id = ? AND n.is_archived = 0
            AND (n.title LIKE ? OR n.content LIKE ? OR nt.tag_name LIKE ?)
            GROUP BY n.id
            ORDER BY relevance DESC, n.updated_at DESC
            LIMIT 20";
        
        $searchTerm = "%{$query}%";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$query, $userId, $searchTerm, $searchTerm, $searchTerm]);
        $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($notes as &$note) {
            $note['tags'] = $note['tags'] ? explode(',', $note['tags']) : [];
            $note['id'] = (int)$note['id'];
            $note['relevance'] = (float)($note['relevance'] ?? 0);
        }
        
        echo json_encode([
            'success' => true,
            'notes' => $notes,
            'query' => $query
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error searching notes: ' . $e->getMessage()]);
    }
}

function handleCreateLink($pdo, $userId, $input) {
    $sourceId = $input['source_note_id'] ?? null;
    $targetId = $input['target_note_id'] ?? null;
    $linkType = $input['link_type'] ?? 'reference';
    
    if (!$sourceId || !$targetId) {
        http_response_code(400);
        echo json_encode(['error' => 'Source and target note IDs required']);
        return;
    }
    
    try {
        // Verify both notes belong to the user
        $checkSql = "SELECT COUNT(*) as count FROM notes WHERE id IN (?, ?) AND user_id = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$sourceId, $targetId, $userId]);
        $result = $checkStmt->fetch();
        
        if ($result['count'] != 2) {
            http_response_code(403);
            echo json_encode(['error' => 'Notes not found or access denied']);
            return;
        }
        
        // Create the link
        $sql = "INSERT INTO note_links (source_note_id, target_note_id, link_type, created_by) 
                VALUES (?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE link_type = VALUES(link_type)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$sourceId, $targetId, $linkType, $userId]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Link created successfully'
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error creating link: ' . $e->getMessage()]);
    }
}

function handleUpdateNodePosition($pdo, $userId, $input) {
    $noteId = $input['note_id'] ?? null;
    $x = $input['x'] ?? null;
    $y = $input['y'] ?? null;
    
    if (!$noteId || $x === null || $y === null) {
        http_response_code(400);
        echo json_encode(['error' => 'Note ID and coordinates required']);
        return;
    }
    
    try {
        $sql = "UPDATE notes SET node_position_x = ?, node_position_y = ? 
                WHERE id = ? AND user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$x, $y, $noteId, $userId]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Note not found']);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error updating position: ' . $e->getMessage()]);
    }
}

function handleGetStats($pdo, $userId) {
    try {
        // Notes statistics
        $noteStats = $pdo->prepare("
            SELECT 
                COUNT(*) as total_notes,
                COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as notes_today,
                COUNT(CASE WHEN YEARWEEK(created_at) = YEARWEEK(NOW()) THEN 1 END) as notes_this_week,
                COUNT(CASE WHEN MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW()) THEN 1 END) as notes_this_month
            FROM notes 
            WHERE user_id = ? AND is_archived = 0
        ");
        $noteStats->execute([$userId]);
        $notes = $noteStats->fetch(PDO::FETCH_ASSOC);
        
        // Tags statistics
        $tagStats = $pdo->prepare("
            SELECT nt.tag_name, COUNT(*) as usage_count
            FROM note_tags nt
            JOIN notes n ON nt.note_id = n.id
            WHERE n.user_id = ? AND n.is_archived = 0
            GROUP BY nt.tag_name
            ORDER BY usage_count DESC
            LIMIT 10
        ");
        $tagStats->execute([$userId]);
        $topTags = $tagStats->fetchAll(PDO::FETCH_ASSOC);
        
        // Links statistics
        $linkStats = $pdo->prepare("
            SELECT COUNT(*) as total_links
            FROM note_links nl
            JOIN notes n1 ON nl.source_note_id = n1.id
            JOIN notes n2 ON nl.target_note_id = n2.id
            WHERE n1.user_id = ? AND n2.user_id = ?
        ");
        $linkStats->execute([$userId, $userId]);
        $links = $linkStats->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'stats' => [
                'notes' => $notes,
                'links' => $links,
                'top_tags' => $topTags
            ]
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error loading statistics: ' . $e->getMessage()]);
    }
}

function handleDeleteLink($pdo, $userId, $linkId) {
    if (!$linkId) {
        http_response_code(400);
        echo json_encode(['error' => 'Link ID required']);
        return;
    }
    
    try {
        $sql = "DELETE nl FROM note_links nl
                JOIN notes n1 ON nl.source_note_id = n1.id
                JOIN notes n2 ON nl.target_note_id = n2.id
                WHERE nl.id = ? AND n1.user_id = ? AND n2.user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$linkId, $userId, $userId]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Link deleted']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Link not found']);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error deleting link: ' . $e->getMessage()]);
    }
}
?>
