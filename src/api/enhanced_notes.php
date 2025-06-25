<?php
/**
 * Enhanced Notes API
 * Provides comprehensive note management with advanced features
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Check authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user = getUser();
if (!$user) {
    http_response_code(401);
    echo json_encode(['error' => 'User not found']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? '';

try {
    // Ensure database tables exist
    ensureTablesExist($pdo);
    
    switch ($method) {
        case 'GET':
            handleGetRequest($pdo, $user['id'], $action);
            break;
            
        case 'POST':
            handlePostRequest($pdo, $user['id'], $input, $action);
            break;
            
        case 'PUT':
            handlePutRequest($pdo, $user['id'], $input, $action);
            break;
            
        case 'DELETE':
            handleDeleteRequest($pdo, $user['id'], $_GET['id'] ?? null, $action);
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

function ensureTablesExist($pdo) {
    // Execute the enhanced database structure
    $sqlFile = __DIR__ . '/../../database/enhanced_notes_structure.sql';
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        $statements = explode(';', $sql);
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    error_log("SQL Error: " . $e->getMessage() . " in statement: " . $statement);
                }
            }
        }
    }
}

function handleGetRequest($pdo, $userId, $action) {
    switch ($action) {
        case 'search':
            handleSearch($pdo, $userId);
            break;
        case 'categories':
            handleGetCategories($pdo, $userId);
            break;
        case 'templates':
            handleGetTemplates($pdo, $userId);
            break;
        case 'stats':
            handleGetStatistics($pdo, $userId);
            break;
        case 'links':
            handleGetNoteLinks($pdo, $userId, $_GET['note_id'] ?? null);
            break;
        case 'graph':
            handleGetGraphData($pdo, $userId);
            break;
        default:
            handleGetNotes($pdo, $userId);
    }
}

function handlePostRequest($pdo, $userId, $input, $action) {
    switch ($action) {
        case 'link':
            handleCreateLink($pdo, $userId, $input);
            break;
        case 'category':
            handleCreateCategory($pdo, $userId, $input);
            break;
        case 'template':
            handleCreateTemplate($pdo, $userId, $input);
            break;
        case 'daily':
            handleCreateDailyNote($pdo, $userId, $input);
            break;
        case 'duplicate':
            handleDuplicateNote($pdo, $userId, $input);
            break;
        default:
            handleCreateNote($pdo, $userId, $input);
    }
}

function handlePutRequest($pdo, $userId, $input, $action) {
    switch ($action) {
        case 'position':
            handleUpdatePosition($pdo, $userId, $input);
            break;
        case 'pin':
            handleTogglePin($pdo, $userId, $input);
            break;
        case 'favorite':
            handleToggleFavorite($pdo, $userId, $input);
            break;
        case 'archive':
            handleToggleArchive($pdo, $userId, $input);
            break;
        default:
            handleUpdateNote($pdo, $userId, $input);
    }
}

function handleDeleteRequest($pdo, $userId, $noteId, $action) {
    switch ($action) {
        case 'link':
            handleDeleteLink($pdo, $userId, $_GET['link_id'] ?? null);
            break;
        case 'category':
            handleDeleteCategory($pdo, $userId, $_GET['category_id'] ?? null);
            break;
        case 'permanent':
            handlePermanentDelete($pdo, $userId, $noteId);
            break;
        default:
            handleDeleteNote($pdo, $userId, $noteId);
    }
}

function handleGetNotes($pdo, $userId) {
    try {
        $archived = $_GET['archived'] ?? 'false';
        $category = $_GET['category'] ?? null;
        $search = $_GET['search'] ?? '';
        $tags = $_GET['tags'] ?? '';
        $limit = min(intval($_GET['limit'] ?? 50), 200);
        $offset = intval($_GET['offset'] ?? 0);
        $sortBy = $_GET['sort'] ?? 'updated';
        $sortDir = $_GET['dir'] ?? 'desc';
        $includeLinks = $_GET['include_links'] ?? 'false';
        
        $whereConditions = ['n.user_id = ?'];
        $params = [$userId];
        
        // Apply filters
        if ($archived === 'true') {
            $whereConditions[] = 'n.is_archived = 1';
        } else {
            $whereConditions[] = 'n.is_archived = 0';
        }
        
        if ($category) {
            $whereConditions[] = 'n.category_id = ?';
            $params[] = $category;
        }
        
        if ($search) {
            $whereConditions[] = '(MATCH(n.title, n.content) AGAINST(? IN NATURAL LANGUAGE MODE) OR n.title LIKE ? OR n.content LIKE ?)';
            $searchTerm = "%$search%";
            $params[] = $search;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if ($tags) {
            $whereConditions[] = 'EXISTS (SELECT 1 FROM note_tags nt WHERE nt.note_id = n.id AND nt.tag_name LIKE ?)';
            $params[] = "%$tags%";
        }
        
        // Build sort clause
        $allowedSorts = ['updated', 'created', 'title', 'word_count'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'updated';
        $sortDir = strtolower($sortDir) === 'asc' ? 'ASC' : 'DESC';
        
        $sortField = $sortBy === 'updated' ? 'n.updated_at' : 
                    ($sortBy === 'created' ? 'n.created_at' : 
                    ($sortBy === 'title' ? 'n.title' : 'n.word_count'));
        
        $sql = "SELECT n.*, 
                       c.name as category_name, 
                       c.color as category_color,
                       c.icon as category_icon,
                       GROUP_CONCAT(DISTINCT nt.tag_name) as tags,
                       (SELECT COUNT(*) FROM note_links nl WHERE nl.source_note_id = n.id OR nl.target_note_id = n.id) as links_count
                FROM notes n 
                LEFT JOIN note_categories c ON n.category_id = c.id 
                LEFT JOIN note_tags nt ON n.id = nt.note_id 
                WHERE " . implode(' AND ', $whereConditions) . "
                GROUP BY n.id, c.name, c.color, c.icon
                ORDER BY n.is_pinned DESC, $sortField $sortDir 
                LIMIT $limit OFFSET $offset";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format notes
        foreach ($notes as &$note) {
            $note['tags'] = $note['tags'] ? explode(',', $note['tags']) : [];
            $note['is_pinned'] = (bool)$note['is_pinned'];
            $note['is_archived'] = (bool)$note['is_archived'];
            $note['is_favorite'] = (bool)$note['is_favorite'];
            $note['is_published'] = (bool)$note['is_published'];
            $note['links_count'] = (int)$note['links_count'];
            $note['word_count'] = (int)$note['word_count'];
            $note['reading_time_minutes'] = (int)$note['reading_time_minutes'];
            
            // Calculate reading time if not set
            if ($note['reading_time_minutes'] <= 0 && $note['content']) {
                $wordCount = str_word_count(strip_tags($note['content']));
                $note['reading_time_minutes'] = max(1, round($wordCount / 200)); // 200 words per minute
            }
        }
        
        // Get links if requested
        $links = [];
        if ($includeLinks === 'true') {
            $links = getNoteLinks($pdo, $userId);
        }
        
        // Get total count
        $countSql = "SELECT COUNT(DISTINCT n.id) as total 
                     FROM notes n 
                     LEFT JOIN note_tags nt ON n.id = nt.note_id 
                     WHERE " . implode(' AND ', $whereConditions);
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $total = $countStmt->fetchColumn();
        
        echo json_encode([
            'success' => true,
            'notes' => $notes,
            'links' => $links,
            'pagination' => [
                'total' => (int)$total,
                'limit' => $limit,
                'offset' => $offset,
                'has_more' => ($offset + $limit) < $total
            ]
        ]);
        
    } catch (Exception $e) {
        error_log("Error in handleGetNotes: " . $e->getMessage());
        throw $e;
    }
}

function handleCreateNote($pdo, $userId, $input) {
    try {
        $title = trim($input['title'] ?? '');
        $content = trim($input['content'] ?? '');
        $contentType = $input['content_type'] ?? 'text';
        $color = $input['color'] ?? '#fbbf24';
        $categoryId = $input['category_id'] ?? null;
        $templateId = $input['template_id'] ?? null;
        $parentId = $input['parent_id'] ?? null;
        $tags = $input['tags'] ?? [];
        
        if (empty($title)) {
            http_response_code(400);
            echo json_encode(['error' => 'Title is required']);
            return;
        }
        
        // Apply template if specified
        if ($templateId) {
            $templateStmt = $pdo->prepare("SELECT template_content FROM note_templates WHERE id = ? AND (user_id = ? OR is_system = 1)");
            $templateStmt->execute([$templateId, $userId]);
            $template = $templateStmt->fetch();
            if ($template) {
                $content = str_replace([
                    '{{date}}',
                    '{{title}}',
                    '{{time}}'
                ], [
                    date('Y-m-d'),
                    $title,
                    date('H:i')
                ], $template['template_content']);
            }
        }
        
        // Calculate word count and reading time
        $wordCount = str_word_count(strip_tags($content));
        $readingTime = max(1, round($wordCount / 200));
        
        $pdo->beginTransaction();
        
        // Create note
        $sql = "INSERT INTO notes (user_id, title, content, content_type, color, category_id, parent_id, word_count, reading_time_minutes) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId, $title, $content, $contentType, $color, $categoryId, $parentId, $wordCount, $readingTime]);
        $noteId = $pdo->lastInsertId();
        
        // Add tags
        if (!empty($tags)) {
            $tagStmt = $pdo->prepare("INSERT IGNORE INTO note_tags (note_id, tag_name) VALUES (?, ?)");
            foreach ($tags as $tag) {
                $tag = trim($tag);
                if (!empty($tag)) {
                    $tagStmt->execute([$noteId, $tag]);
                }
            }
        }
        
        // Auto-detect and create links from content
        createAutoLinks($pdo, $noteId, $content, $userId);
        
        // Update template usage count
        if ($templateId) {
            $pdo->prepare("UPDATE note_templates SET usage_count = usage_count + 1 WHERE id = ?")->execute([$templateId]);
        }
        
        // Update user statistics
        updateUserStatistics($pdo, $userId);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'id' => $noteId,
            'message' => 'Notiz erfolgreich erstellt',
            'word_count' => $wordCount,
            'reading_time' => $readingTime
        ]);
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

function handleUpdateNote($pdo, $userId, $input) {
    try {
        $noteId = $input['id'] ?? null;
        $title = trim($input['title'] ?? '');
        $content = trim($input['content'] ?? '');
        $color = $input['color'] ?? '#fbbf24';
        $categoryId = $input['category_id'] ?? null;
        $isPinned = $input['is_pinned'] ?? false;
        $isFavorite = $input['is_favorite'] ?? false;
        $tags = $input['tags'] ?? [];
        $createVersion = $input['create_version'] ?? false;
        
        if (empty($noteId) || empty($title)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID and title are required']);
            return;
        }
        
        $pdo->beginTransaction();
        
        // Check ownership
        $checkStmt = $pdo->prepare("SELECT title, content FROM notes WHERE id = ? AND user_id = ?");
        $checkStmt->execute([$noteId, $userId]);
        $existingNote = $checkStmt->fetch();
        
        if (!$existingNote) {
            http_response_code(404);
            echo json_encode(['error' => 'Note not found']);
            return;
        }
        
        // Create version if content changed significantly
        if ($createVersion || (strlen($content) > 0 && $existingNote['content'] !== $content)) {
            createNoteVersion($pdo, $noteId, $existingNote['title'], $existingNote['content']);
        }
        
        // Calculate word count and reading time
        $wordCount = str_word_count(strip_tags($content));
        $readingTime = max(1, round($wordCount / 200));
        
        // Update note
        $sql = "UPDATE notes SET 
                    title = ?, 
                    content = ?, 
                    color = ?, 
                    category_id = ?, 
                    is_pinned = ?, 
                    is_favorite = ?,
                    word_count = ?,
                    reading_time_minutes = ?,
                    updated_at = NOW() 
                WHERE id = ? AND user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $content, $color, $categoryId, $isPinned ? 1 : 0, $isFavorite ? 1 : 0, $wordCount, $readingTime, $noteId, $userId]);
        
        // Update tags
        $pdo->prepare("DELETE FROM note_tags WHERE note_id = ?")->execute([$noteId]);
        if (!empty($tags)) {
            $tagStmt = $pdo->prepare("INSERT IGNORE INTO note_tags (note_id, tag_name) VALUES (?, ?)");
            foreach ($tags as $tag) {
                $tag = trim($tag);
                if (!empty($tag)) {
                    $tagStmt->execute([$noteId, $tag]);
                }
            }
        }
        
        // Update auto-links
        updateAutoLinks($pdo, $noteId, $content, $userId);
        
        // Update user statistics
        updateUserStatistics($pdo, $userId);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Notiz erfolgreich aktualisiert',
            'word_count' => $wordCount,
            'reading_time' => $readingTime
        ]);
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

function handleSearch($pdo, $userId) {
    $query = $_GET['q'] ?? '';
    $limit = min(intval($_GET['limit'] ?? 20), 50);
    
    if (empty($query)) {
        echo json_encode(['success' => true, 'results' => []]);
        return;
    }
    
    // Save search history
    $pdo->prepare("INSERT INTO note_search_history (user_id, search_query, search_filters) VALUES (?, ?, ?)")
        ->execute([$userId, $query, json_encode($_GET)]);
    
    // Perform search with ranking
    $sql = "SELECT n.id, n.title, n.content, n.color, n.updated_at,
                   c.name as category_name,
                   GROUP_CONCAT(DISTINCT nt.tag_name) as tags,
                   MATCH(n.title, n.content) AGAINST(? IN NATURAL LANGUAGE MODE) as relevance_score
            FROM notes n
            LEFT JOIN note_categories c ON n.category_id = c.id
            LEFT JOIN note_tags nt ON n.id = nt.note_id
            WHERE n.user_id = ? AND n.is_archived = 0 
            AND (MATCH(n.title, n.content) AGAINST(? IN NATURAL LANGUAGE MODE) 
                OR n.title LIKE ? OR n.content LIKE ?)
            GROUP BY n.id
            ORDER BY relevance_score DESC, n.updated_at DESC
            LIMIT ?";
    
    $searchTerm = "%$query%";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$query, $userId, $query, $searchTerm, $searchTerm, $limit]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format results
    foreach ($results as &$result) {
        $result['tags'] = $result['tags'] ? explode(',', $result['tags']) : [];
        $result['relevance_score'] = (float)$result['relevance_score'];
        
        // Highlight search terms in title and content
        $result['title_highlighted'] = highlightSearchTerm($result['title'], $query);
        $result['content_snippet'] = getContentSnippet($result['content'], $query);
    }
    
    echo json_encode(['success' => true, 'results' => $results, 'query' => $query]);
}

function handleGetCategories($pdo, $userId) {
    $sql = "SELECT * FROM note_categories 
            WHERE user_id = ? OR is_system = 1 
            ORDER BY is_system DESC, sort_order, name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'categories' => $categories]);
}

function handleGetTemplates($pdo, $userId) {
    $sql = "SELECT * FROM note_templates 
            WHERE user_id = ? OR is_system = 1 
            ORDER BY is_system DESC, usage_count DESC, name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'templates' => $templates]);
}

function handleGetStatistics($pdo, $userId) {
    // Get or create user statistics
    $stmt = $pdo->prepare("SELECT * FROM note_statistics WHERE user_id = ?");
    $stmt->execute([$userId]);
    $stats = $stmt->fetch();
    
    if (!$stats) {
        updateUserStatistics($pdo, $userId);
        $stmt->execute([$userId]);
        $stats = $stmt->fetch();
    }
    
    // Get additional live statistics
    $liveStats = [];
    
    // Notes by category
    $stmt = $pdo->prepare("
        SELECT c.name, c.color, COUNT(n.id) as count 
        FROM note_categories c 
        LEFT JOIN notes n ON c.id = n.category_id AND n.user_id = ? AND n.is_archived = 0
        WHERE c.user_id = ? OR c.is_system = 1
        GROUP BY c.id, c.name, c.color
        ORDER BY count DESC
    ");
    $stmt->execute([$userId, $userId]);
    $liveStats['notes_by_category'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Recent activity
    $stmt = $pdo->prepare("
        SELECT DATE(updated_at) as date, COUNT(*) as notes_updated
        FROM notes 
        WHERE user_id = ? AND updated_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY DATE(updated_at)
        ORDER BY date DESC
        LIMIT 30
    ");
    $stmt->execute([$userId]);
    $liveStats['recent_activity'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'statistics' => $stats, 'live_stats' => $liveStats]);
}

function handleGetGraphData($pdo, $userId) {
    // Get notes for graph
    $stmt = $pdo->prepare("
        SELECT id, title, color, category_id, is_pinned, 
               word_count, position_x, position_y
        FROM notes 
        WHERE user_id = ? AND is_archived = 0
    ");
    $stmt->execute([$userId]);
    $nodes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get links
    $links = getNoteLinks($pdo, $userId);
    
    echo json_encode(['success' => true, 'nodes' => $nodes, 'links' => $links]);
}

// Helper functions
function createAutoLinks($pdo, $noteId, $content, $userId) {
    // Find [[Note Title]] patterns
    preg_match_all('/\[\[([^\]]+)\]\]/', $content, $matches);
    
    foreach ($matches[1] as $linkTitle) {
        $stmt = $pdo->prepare("
            SELECT id FROM notes 
            WHERE title = ? AND user_id = ? AND is_archived = 0 AND id != ?
        ");
        $stmt->execute([$linkTitle, $userId, $noteId]);
        $targetNote = $stmt->fetch();
        
        if ($targetNote) {
            // Create bidirectional link
            $linkStmt = $pdo->prepare("
                INSERT IGNORE INTO note_links (source_note_id, target_note_id, link_type) 
                VALUES (?, ?, 'reference')
            ");
            $linkStmt->execute([$noteId, $targetNote['id']]);
        }
    }
}

function updateAutoLinks($pdo, $noteId, $content, $userId) {
    // Remove existing auto-generated links
    $pdo->prepare("DELETE FROM note_links WHERE source_note_id = ? AND link_type = 'reference'")
        ->execute([$noteId]);
    
    // Recreate links
    createAutoLinks($pdo, $noteId, $content, $userId);
}

function createNoteVersion($pdo, $noteId, $title, $content) {
    // Get next version number
    $stmt = $pdo->prepare("SELECT COALESCE(MAX(version_number), 0) + 1 as next_version FROM note_versions WHERE note_id = ?");
    $stmt->execute([$noteId]);
    $nextVersion = $stmt->fetchColumn();
    
    // Create version
    $pdo->prepare("INSERT INTO note_versions (note_id, title, content, version_number) VALUES (?, ?, ?, ?)")
        ->execute([$noteId, $title, $content, $nextVersion]);
}

function updateUserStatistics($pdo, $userId) {
    // Calculate statistics
    $totalNotes = $pdo->prepare("SELECT COUNT(*) FROM notes WHERE user_id = ? AND is_archived = 0");
    $totalNotes->execute([$userId]);
    $notesCount = $totalNotes->fetchColumn();
    
    $totalWords = $pdo->prepare("SELECT SUM(word_count) FROM notes WHERE user_id = ? AND is_archived = 0");
    $totalWords->execute([$userId]);
    $wordsCount = $totalWords->fetchColumn() ?: 0;
    
    $totalConnections = $pdo->prepare("SELECT COUNT(*) FROM note_links nl JOIN notes n ON (nl.source_note_id = n.id OR nl.target_note_id = n.id) WHERE n.user_id = ?");
    $totalConnections->execute([$userId]);
    $connectionsCount = $totalConnections->fetchColumn();
    
    $categoriesUsed = $pdo->prepare("SELECT COUNT(DISTINCT category_id) FROM notes WHERE user_id = ? AND category_id IS NOT NULL AND is_archived = 0");
    $categoriesUsed->execute([$userId]);
    $categoriesCount = $categoriesUsed->fetchColumn();
    
    // Most used tags
    $tagsStmt = $pdo->prepare("
        SELECT nt.tag_name, COUNT(*) as usage_count 
        FROM note_tags nt 
        JOIN notes n ON nt.note_id = n.id 
        WHERE n.user_id = ? AND n.is_archived = 0 
        GROUP BY nt.tag_name 
        ORDER BY usage_count DESC 
        LIMIT 10
    ");
    $tagsStmt->execute([$userId]);
    $mostUsedTags = $tagsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Update or insert statistics
    $pdo->prepare("
        INSERT INTO note_statistics (user_id, total_notes, total_words, total_connections, categories_used, most_used_tags)
        VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
        total_notes = VALUES(total_notes),
        total_words = VALUES(total_words),
        total_connections = VALUES(total_connections),
        categories_used = VALUES(categories_used),
        most_used_tags = VALUES(most_used_tags),
        last_updated = NOW()
    ")->execute([$userId, $notesCount, $wordsCount, $connectionsCount, $categoriesCount, json_encode($mostUsedTags)]);
}

function getNoteLinks($pdo, $userId) {
    $stmt = $pdo->prepare("
        SELECT nl.*, 
               n1.title as source_title, 
               n2.title as target_title
        FROM note_links nl
        JOIN notes n1 ON nl.source_note_id = n1.id
        JOIN notes n2 ON nl.target_note_id = n2.id
        WHERE n1.user_id = ? AND n2.user_id = ?
    ");
    $stmt->execute([$userId, $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function highlightSearchTerm($text, $term) {
    return preg_replace('/(' . preg_quote($term, '/') . ')/i', '<mark>$1</mark>', $text);
}

function getContentSnippet($content, $searchTerm, $snippetLength = 200) {
    $content = strip_tags($content);
    $pos = stripos($content, $searchTerm);
    
    if ($pos !== false) {
        $start = max(0, $pos - 100);
        $snippet = substr($content, $start, $snippetLength);
        if ($start > 0) $snippet = '...' . $snippet;
        if (strlen($content) > $start + $snippetLength) $snippet .= '...';
        return highlightSearchTerm($snippet, $searchTerm);
    }
    
    return substr($content, 0, $snippetLength) . (strlen($content) > $snippetLength ? '...' : '');
}

// Additional handlers for missing functions
function handleDeleteNote($pdo, $userId, $noteId) {
    if (empty($noteId)) {
        http_response_code(400);
        echo json_encode(['error' => 'Note ID is required']);
        return;
    }
    
    // Soft delete by archiving
    $stmt = $pdo->prepare("UPDATE notes SET is_archived = 1 WHERE id = ? AND user_id = ?");
    $stmt->execute([$noteId, $userId]);
    
    if ($stmt->rowCount() > 0) {
        updateUserStatistics($pdo, $userId);
        echo json_encode(['success' => true, 'message' => 'Notiz archiviert']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Note not found']);
    }
}

function handleTogglePin($pdo, $userId, $input) {
    $noteId = $input['note_id'] ?? null;
    if (!$noteId) {
        http_response_code(400);
        echo json_encode(['error' => 'Note ID required']);
        return;
    }
    
    $stmt = $pdo->prepare("UPDATE notes SET is_pinned = NOT is_pinned WHERE id = ? AND user_id = ?");
    $stmt->execute([$noteId, $userId]);
    
    echo json_encode(['success' => true, 'message' => 'Pin status updated']);
}

function handleToggleFavorite($pdo, $userId, $input) {
    $noteId = $input['note_id'] ?? null;
    if (!$noteId) {
        http_response_code(400);
        echo json_encode(['error' => 'Note ID required']);
        return;
    }
    
    $stmt = $pdo->prepare("UPDATE notes SET is_favorite = NOT is_favorite WHERE id = ? AND user_id = ?");
    $stmt->execute([$noteId, $userId]);
    
    echo json_encode(['success' => true, 'message' => 'Favorite status updated']);
}

function handleUpdatePosition($pdo, $userId, $input) {
    $noteId = $input['note_id'] ?? null;
    $x = $input['x'] ?? null;
    $y = $input['y'] ?? null;
    
    if (!$noteId || $x === null || $y === null) {
        http_response_code(400);
        echo json_encode(['error' => 'Note ID and coordinates required']);
        return;
    }
    
    $stmt = $pdo->prepare("UPDATE notes SET position_x = ?, position_y = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$x, $y, $noteId, $userId]);
    
    echo json_encode(['success' => true, 'message' => 'Position updated']);
}
?>
