<?php
// Enhanced Zettelkasten Notes API
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
    switch ($method) {
        case 'GET':
            // Check for special endpoints
            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'links':
                        handleGetNoteLinks($pdo, $user['id'], $_GET['note_id'] ?? null);
                        break;
                    case 'graph':
                        handleGetGraphData($pdo, $user['id']);
                        break;
                    case 'search_for_linking':
                        handleSearchForLinking($pdo, $user['id'], $_GET['query'] ?? '');
                        break;
                    case 'shared_notes':
                        handleGetSharedNotes($pdo, $user['id']);
                        break;
                    default:
                        handleGetNotes($pdo, $user['id']);
                }
            } else if (isset($_GET['id'])) {
                handleGetNote($pdo, $user['id'], $_GET['id']);
            } else {
                handleGetNotes($pdo, $user['id']);
            }
            break;
            
        case 'POST':
            if (isset($input['action'])) {
                switch ($input['action']) {
                    case 'link':
                        handleCreateLink($pdo, $user['id'], $input);
                        break;
                    case 'share':
                        handleShareNote($pdo, $user['id'], $input);
                        break;
                    case 'process_wiki_links':
                        handleProcessWikiLinks($pdo, $user['id'], $input);
                        break;
                    default:
                        handleCreateNote($pdo, $user['id'], $input);
                }
            } else {
                handleCreateNote($pdo, $user['id'], $input);
            }
            break;
            
        case 'PUT':
            if (isset($input['action'])) {
                switch ($input['action']) {
                    case 'update_position':
                        handleUpdateNodePosition($pdo, $user['id'], $input);
                        break;
                    default:
                        handleUpdateNote($pdo, $user['id'], $input);
                }
            } else {
                handleUpdateNote($pdo, $user['id'], $input);
            }
            break;
            
        case 'DELETE':
            if (isset($_GET['link_id'])) {
                handleDeleteLink($pdo, $user['id'], $_GET['link_id']);
            } else {
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
        $limit = min(intval($_GET['limit'] ?? 200), 200);
        $searchQuery = $_GET['search'] ?? '';
        $includeLinks = $_GET['include_links'] ?? 'false';
        
        // Base query with shared notes included
        $sql = "SELECT DISTINCT n.id, n.user_id, n.title, n.content, n.color, n.is_pinned, n.is_archived, 
                   n.created_at, n.updated_at, n.links_count, n.position_x, n.position_y,
                   n.visibility, n.is_shared,
                   GROUP_CONCAT(DISTINCT nt.tag_name) as tags,
                   CASE WHEN n.user_id = ? THEN 'owner' 
                        WHEN ns.permission_level IS NOT NULL THEN ns.permission_level 
                        ELSE 'none' END as permission_level
            FROM notes n 
            LEFT JOIN note_tags nt ON n.id = nt.note_id 
            LEFT JOIN note_shares ns ON n.id = ns.note_id AND ns.shared_with = ? AND ns.is_active = 1
            WHERE (n.user_id = ? OR ns.shared_with = ?) 
            AND n.is_archived = ?";
        
        $params = [$userId, $userId, $userId, $userId, $archived === 'true' ? 1 : 0];
        
        // Add search functionality
        if (!empty($searchQuery)) {
            $sql .= " AND (n.title LIKE ? OR n.content LIKE ?)";
            $searchParam = "%$searchQuery%";
            $params[] = $searchParam;
            $params[] = $searchParam;
        }
        
        $sql .= " GROUP BY n.id, n.user_id, n.title, n.content, n.color, n.is_pinned, n.is_archived, 
                     n.created_at, n.updated_at, n.links_count, n.position_x, n.position_y,
                     n.visibility, n.is_shared, ns.permission_level
                  ORDER BY n.is_pinned DESC, n.updated_at DESC 
                  LIMIT " . $limit;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
          // Format and enhance each note
        foreach ($notes as &$note) {
            $note['tags'] = $note['tags'] ? explode(',', $note['tags']) : [];
            $note['is_pinned'] = (bool)$note['is_pinned'];
            $note['is_archived'] = (bool)$note['is_archived'];
            $note['is_shared'] = (bool)$note['is_shared'];
            $note['id'] = (int)$note['id'];
            $note['user_id'] = (int)$note['user_id'];
            $note['links_count'] = (int)($note['links_count'] ?? 0);
            $note['position_x'] = $note['position_x'] ? (float)$note['position_x'] : null;
            $note['position_y'] = $note['position_y'] ? (float)$note['position_y'] : null;
            
            // Get linked notes if requested
            if ($includeLinks === 'true') {
                $linksSql = "SELECT target_note_id, link_type FROM note_links WHERE source_note_id = ?
                            UNION
                            SELECT source_note_id as target_note_id, 'backlink' as link_type FROM note_links WHERE target_note_id = ?";
                $linksStmt = $pdo->prepare($linksSql);
                $linksStmt->execute([$note['id'], $note['id']]);
                $note['linked_notes'] = $linksStmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        
        $response = [
            'success' => true, 
            'notes' => $notes,
            'count' => count($notes)
        ];
        
        // Add links if requested
        if ($includeLinks === 'true') {
            $linksSql = "SELECT nl.id, nl.source_note_id, nl.target_note_id, nl.link_type, 
                               nl.created_at, nl.created_by
                        FROM note_links nl
                        JOIN notes n1 ON nl.source_note_id = n1.id
                        JOIN notes n2 ON nl.target_note_id = n2.id
                        LEFT JOIN note_shares ns1 ON n1.id = ns1.note_id AND ns1.shared_with = ? AND ns1.is_active = 1
                        LEFT JOIN note_shares ns2 ON n2.id = ns2.note_id AND ns2.shared_with = ? AND ns2.is_active = 1
                        WHERE ((n1.user_id = ? OR ns1.shared_with = ?) AND n1.is_archived = 0)
                        AND ((n2.user_id = ? OR ns2.shared_with = ?) AND n2.is_archived = 0)";
            
            $linksStmt = $pdo->prepare($linksSql);
            $linksStmt->execute([$userId, $userId, $userId, $userId, $userId, $userId]);
            $response['links'] = $linksStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        echo json_encode($response);
        
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
        $visibility = $input['visibility'] ?? 'private';
        
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
            is_shared BOOLEAN DEFAULT FALSE,
            visibility ENUM('private', 'shared', 'public') DEFAULT 'private',
            links_count INT DEFAULT 0,
            position_x FLOAT DEFAULT NULL,
            position_y FLOAT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_user_archived (user_id, is_archived),
            INDEX idx_visibility (visibility)
        )");
        
        $pdo->beginTransaction();
        
        $sql = "INSERT INTO notes (user_id, title, content, color, visibility) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId, $title, $content, $color, $visibility]);
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
        
        // Process wiki-style links [[Note Title]]
        processWikiLinks($pdo, $noteId, $content, $userId);
        
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

// Enhanced Zettelkasten Functions

function handleGetNoteLinks($pdo, $userId, $noteId) {
    if (!$noteId) {
        http_response_code(400);
        echo json_encode(['error' => 'Note ID required']);
        return;
    }
    
    try {
        // Get all links for this note
        $sql = "SELECT nl.*, 
                   n1.title as source_title, n1.color as source_color,
                   n2.title as target_title, n2.color as target_color
                FROM note_links nl
                JOIN notes n1 ON nl.source_note_id = n1.id
                JOIN notes n2 ON nl.target_note_id = n2.id
                WHERE (nl.source_note_id = ? OR nl.target_note_id = ?)
                AND (n1.user_id = ? OR EXISTS(SELECT 1 FROM note_shares WHERE note_id = n1.id AND shared_with = ?))
                AND (n2.user_id = ? OR EXISTS(SELECT 1 FROM note_shares WHERE note_id = n2.id AND shared_with = ?))";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$noteId, $noteId, $userId, $userId, $userId, $userId]);
        $links = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'links' => $links]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error loading links: ' . $e->getMessage()]);
    }
}

function handleGetGraphData($pdo, $userId) {
    try {
        // Get all accessible notes with positions
        $notesSql = "SELECT DISTINCT n.id, n.title, n.color, n.is_pinned, n.links_count, 
                           n.position_x, n.position_y, n.created_at, n.updated_at,
                           CASE WHEN n.user_id = ? THEN 'owner' 
                                WHEN ns.permission_level IS NOT NULL THEN ns.permission_level 
                                ELSE 'none' END as permission_level
                    FROM notes n 
                    LEFT JOIN note_shares ns ON n.id = ns.note_id AND ns.shared_with = ? AND ns.is_active = 1
                    WHERE (n.user_id = ? OR ns.shared_with = ?) 
                    AND n.is_archived = 0
                    ORDER BY n.updated_at DESC";
        
        $stmt = $pdo->prepare($notesSql);
        $stmt->execute([$userId, $userId, $userId, $userId]);
        $nodes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get all links between accessible notes
        $linksSql = "SELECT nl.id, nl.source_note_id, nl.target_note_id, nl.link_type, 
                           nl.created_at, nl.created_by
                    FROM note_links nl
                    JOIN notes n1 ON nl.source_note_id = n1.id
                    JOIN notes n2 ON nl.target_note_id = n2.id
                    LEFT JOIN note_shares ns1 ON n1.id = ns1.note_id AND ns1.shared_with = ? AND ns1.is_active = 1
                    LEFT JOIN note_shares ns2 ON n2.id = ns2.note_id AND ns2.shared_with = ? AND ns2.is_active = 1
                    WHERE ((n1.user_id = ? OR ns1.shared_with = ?) AND n1.is_archived = 0)
                    AND ((n2.user_id = ? OR ns2.shared_with = ?) AND n2.is_archived = 0)";
        
        $stmt = $pdo->prepare($linksSql);
        $stmt->execute([$userId, $userId, $userId, $userId, $userId, $userId]);
        $links = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'nodes' => $nodes,
            'links' => $links,
            'count' => [
                'nodes' => count($nodes),
                'links' => count($links)
            ]
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error loading graph data: ' . $e->getMessage()]);
    }
}

function handleSearchForLinking($pdo, $userId, $query) {
    try {
        if (empty($query)) {
            echo json_encode(['success' => true, 'notes' => []]);
            return;
        }
        
        $searchSql = "SELECT DISTINCT n.id, n.title, n.content, n.color, n.links_count
                     FROM notes n 
                     LEFT JOIN note_shares ns ON n.id = ns.note_id AND ns.shared_with = ? AND ns.is_active = 1
                     WHERE (n.user_id = ? OR ns.shared_with = ?) 
                     AND n.is_archived = 0
                     AND (n.title LIKE ? OR n.content LIKE ?)
                     ORDER BY n.title
                     LIMIT 20";
        
        $searchParam = "%$query%";
        $stmt = $pdo->prepare($searchSql);
        $stmt->execute([$userId, $userId, $userId, $searchParam, $searchParam]);
        $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'notes' => $notes]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error searching notes: ' . $e->getMessage()]);
    }
}

function handleCreateLink($pdo, $userId, $input) {
    try {
        $sourceId = $input['source_note_id'] ?? null;
        $targetId = $input['target_note_id'] ?? null;
        $linkType = $input['link_type'] ?? 'reference';
        
        if (!$sourceId || !$targetId) {
            http_response_code(400);
            echo json_encode(['error' => 'Source and target note IDs required']);
            return;
        }
        
        if ($sourceId == $targetId) {
            http_response_code(400);
            echo json_encode(['error' => 'Cannot link note to itself']);
            return;
        }
        
        // Verify user has access to both notes
        $accessSql = "SELECT COUNT(*) as count FROM notes n 
                     LEFT JOIN note_shares ns ON n.id = ns.note_id AND ns.shared_with = ? AND ns.is_active = 1
                     WHERE n.id IN (?, ?) AND (n.user_id = ? OR ns.shared_with = ?)";
        $stmt = $pdo->prepare($accessSql);
        $stmt->execute([$userId, $sourceId, $targetId, $userId, $userId]);
        
        if ($stmt->fetch()['count'] < 2) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied to one or both notes']);
            return;
        }
        
        // Check if link already exists
        $existsSql = "SELECT id FROM note_links WHERE source_note_id = ? AND target_note_id = ?";
        $stmt = $pdo->prepare($existsSql);
        $stmt->execute([$sourceId, $targetId]);
        
        if ($stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['error' => 'Link already exists']);
            return;
        }
        
        // Create the link
        $insertSql = "INSERT INTO note_links (source_note_id, target_note_id, link_type, created_by) 
                     VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($insertSql);
        $stmt->execute([$sourceId, $targetId, $linkType, $userId]);
        
        $linkId = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true, 
            'link_id' => $linkId,
            'message' => 'Link created successfully'
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error creating link: ' . $e->getMessage()]);
    }
}

function handleShareNote($pdo, $userId, $input) {
    try {
        $noteId = $input['note_id'] ?? null;
        $shareWithUserId = $input['share_with_user_id'] ?? null;
        $permissionLevel = $input['permission_level'] ?? 'read';
        
        if (!$noteId || !$shareWithUserId) {
            http_response_code(400);
            echo json_encode(['error' => 'Note ID and target user ID required']);
            return;
        }
        
        // Verify user owns the note
        $ownsSql = "SELECT id FROM notes WHERE id = ? AND user_id = ?";
        $stmt = $pdo->prepare($ownsSql);
        $stmt->execute([$noteId, $userId]);
        
        if (!$stmt->fetch()) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied - you can only share your own notes']);
            return;
        }
        
        // Verify target user exists
        $userExistsSql = "SELECT id FROM users WHERE id = ?";
        $stmt = $pdo->prepare($userExistsSql);
        $stmt->execute([$shareWithUserId]);
        
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'Target user not found']);
            return;
        }
        
        // Create or update share
        $shareSql = "INSERT INTO note_shares (note_id, shared_by, shared_with, permission_level) 
                    VALUES (?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                    permission_level = VALUES(permission_level),
                    shared_at = CURRENT_TIMESTAMP,
                    is_active = 1";
        $stmt = $pdo->prepare($shareSql);
        $stmt->execute([$noteId, $userId, $shareWithUserId, $permissionLevel]);
        
        // Update note is_shared flag
        $updateNoteSql = "UPDATE notes SET is_shared = 1 WHERE id = ?";
        $stmt = $pdo->prepare($updateNoteSql);
        $stmt->execute([$noteId]);
        
        echo json_encode(['success' => true, 'message' => 'Note shared successfully']);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error sharing note: ' . $e->getMessage()]);
    }
}

function handleUpdateNodePosition($pdo, $userId, $input) {
    try {
        $noteId = $input['note_id'] ?? null;
        $positionX = $input['position_x'] ?? null;
        $positionY = $input['position_y'] ?? null;
        
        if (!$noteId || $positionX === null || $positionY === null) {
            http_response_code(400);
            echo json_encode(['error' => 'Note ID and position coordinates required']);
            return;
        }
        
        // Update position for notes the user owns or has edit access to
        $updateSql = "UPDATE notes n
                     LEFT JOIN note_shares ns ON n.id = ns.note_id AND ns.shared_with = ? AND ns.is_active = 1
                     SET n.position_x = ?, n.position_y = ?
                     WHERE n.id = ? AND (n.user_id = ? OR (ns.shared_with = ? AND ns.permission_level IN ('edit', 'comment')))";

        $stmt = $pdo->prepare($updateSql);
        $stmt->execute([$userId, $positionX, $positionY, $noteId, $userId, $userId]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Position updated']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Note not found or access denied']);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error updating position: ' . $e->getMessage()]);
    }
}

function handleProcessWikiLinks($pdo, $userId, $input) {
    try {
        $noteId = $input['note_id'] ?? null;
        $content = $input['content'] ?? '';
        
        if (!$noteId) {
            http_response_code(400);
            echo json_encode(['error' => 'Note ID required']);
            return;
        }
        
        // Verify user owns the note
        $ownsSql = "SELECT id FROM notes WHERE id = ? AND user_id = ?";
        $stmt = $pdo->prepare($ownsSql);
        $stmt->execute([$noteId, $userId]);
        
        if (!$stmt->fetch()) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }
        
        processWikiLinks($pdo, $noteId, $content, $userId);
        
        echo json_encode(['success' => true, 'message' => 'Wiki links processed']);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error processing wiki links: ' . $e->getMessage()]);
    }
}

function handleGetSharedNotes($pdo, $userId) {
    try {
        $sql = "SELECT DISTINCT n.id, n.title, n.content, n.color, n.created_at, n.updated_at,
                       ns.permission_level, ns.shared_at,
                       u.username as shared_by_username
                FROM notes n
                JOIN note_shares ns ON n.id = ns.note_id
                JOIN users u ON ns.shared_by = u.id
                WHERE ns.shared_with = ? AND ns.is_active = 1 AND n.is_archived = 0
                ORDER BY ns.shared_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'shared_notes' => $notes]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error loading shared notes: ' . $e->getMessage()]);
    }
}
?>
