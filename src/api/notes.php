<?php
/**
 * Enhanced Notes API with advanced features
 * Supports CRUD operations, AI-powered search, smart linking, collaboration, and more
 * Version 2.0 - Optimized and Extended
 */

// Immediately start output buffering to catch any unwanted output
ob_start();

// Global error handling - ensure all errors are returned as JSON
function handleError($errno, $errstr, $errfile, $errline) {
    // Clean any output buffer that might contain HTML
    if (ob_get_level()) {
        ob_clean();
    }
    
    $error = [
        'error' => 'PHP Error',
        'message' => $errstr,
        'file' => basename($errfile),
        'line' => $errline,
        'type' => $errno
    ];
    
    if (!headers_sent()) {
        header('Content-Type: application/json');
        http_response_code(500);
    }
    
    echo json_encode($error);
    exit;
}

function handleException($exception) {
    // Clean any output buffer that might contain HTML
    if (ob_get_level()) {
        ob_clean();
    }
    
    $error = [
        'error' => 'Exception',
        'message' => $exception->getMessage(),
        'file' => basename($exception->getFile()),
        'line' => $exception->getLine()
    ];
    
    if (!headers_sent()) {
        header('Content-Type: application/json');
        http_response_code(500);
    }
    
    echo json_encode($error);
    exit;
}

function handleFatalError() {
    $error = error_get_last();
    if ($error !== NULL && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        // Clean any output buffer that might contain HTML
        if (ob_get_level()) {
            ob_clean();
        }
        
        $errorResponse = [
            'error' => 'Fatal Error',
            'message' => $error['message'],
            'file' => basename($error['file']),
            'line' => $error['line']
        ];
        
        if (!headers_sent()) {
            header('Content-Type: application/json');
            http_response_code(500);
        }
        
        echo json_encode($errorResponse);
    }
}

// Set error handlers
set_error_handler('handleError');
set_exception_handler('handleException');
register_shutdown_function('handleFatalError');

// Disable HTML error display
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Buffer output to catch any unexpected HTML
ob_start();

require_once __DIR__ . '/../lib/auth.php';

// Handle database connection with proper error handling
try {
    // Include config to get database connection
    require_once __DIR__ . '/../../config.php';
    
    // Check if PDO connection was successful
    if (!isset($pdo) || !$pdo instanceof PDO) {
        throw new Exception('Database connection failed - PDO not available');
    }
    
    // Test the connection
    $pdo->query('SELECT 1');
    
} catch (Exception $e) {
    // Clean any output buffer that might contain HTML
    if (ob_get_level()) {
        ob_clean();
    }
    
    if (!headers_sent()) {
        header('Content-Type: application/json');
        http_response_code(500);
    }
    
    echo json_encode([
        'error' => 'Database connection failed',
        'message' => $e->getMessage()
    ]);
    exit;
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Start session safely
try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Session initialization failed', 'message' => $e->getMessage()]);
    exit;
}

// Check authentication
try {
    if (!function_exists('isLoggedIn') || !isLoggedIn()) {
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
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Authentication check failed', 'message' => $e->getMessage()]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? null;

// Initialize enhanced tables
initializeEnhancedTables($pdo);

try {
    switch ($method) {
        case 'GET':
            switch ($action) {
                case 'search':
                    handleSearch($pdo, $user['id']);
                    break;
                case 'links':
                    handleGetLinks($pdo, $user['id'], $_GET['note_id'] ?? null);
                    break;
                case 'categories':
                    handleGetCategories($pdo, $user['id']);
                    break;
                case 'templates':
                    handleGetTemplates($pdo, $user['id']);
                    break;
                case 'stats':
                    handleGetStats($pdo, $user['id']);
                    break;
                case 'export':
                    handleExport($pdo, $user['id'], $_GET['format'] ?? 'json');
                    break;
                case 'backlinks':
                    handleGetBacklinks($pdo, $user['id'], $_GET['note_id']);
                    break;
                case 'ai_suggestions':
                    handleAISuggestions($pdo, $user['id'], $_GET['note_id']);
                    break;
                case 'smart_search':
                    handleSmartSearch($pdo, $user['id']);
                    break;
                case 'related':
                    handleGetRelatedNotes($pdo, $user['id'], $_GET['note_id']);
                    break;
                case 'analytics':
                    handleGetAnalytics($pdo, $user['id']);
                    break;
                case 'graph_data':
                    handleGetGraphData($pdo, $user['id']);
                    break;
                case 'recommendations':
                    handleGetRecommendations($pdo, $user['id']);
                    break;
                case 'content_preview':
                    handleContentPreview($pdo, $user['id'], $_GET['note_id']);
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
                case 'duplicate':
                    handleDuplicateNote($pdo, $user['id'], $input);
                    break;
                case 'bulk':
                    handleBulkOperation($pdo, $user['id'], $input);
                    break;
                case 'import':
                    handleImport($pdo, $user['id'], $input);
                    break;
                case 'batch_edit':
                    handleBatchEdit($pdo, $user['id'], $input);
                    break;
                case 'auto_organize':
                    handleAutoOrganize($pdo, $user['id'], $input);
                    break;
                case 'voice_note':
                    handleVoiceNote($pdo, $user['id'], $input);
                    break;
                case 'text_analysis':
                    handleTextAnalysis($pdo, $user['id'], $input);
                    break;
                case 'quick_capture':
                    handleQuickCapture($pdo, $user['id'], $input);
                    break;
                default:
                    handleCreateNote($pdo, $user['id'], $input);
            }
            break;
            
        case 'PUT':
            switch ($action) {
                case 'position':
                    handleUpdatePosition($pdo, $user['id'], $input);
                    break;
                case 'share':
                    handleShareNote($pdo, $user['id'], $input);
                    break;
                case 'batch_update':
                    handleBatchUpdate($pdo, $user['id'], $input);
                    break;
                case 'smart_tag':
                    handleSmartTagging($pdo, $user['id'], $input);
                    break;
                case 'merge':
                    handleMergeNotes($pdo, $user['id'], $input);
                    break;
                default:
                    handleUpdateNote($pdo, $user['id'], $input);
            }
            break;
            
        case 'PATCH':
            handlePatchNote($pdo, $user['id'], $input);
            break;
            
        case 'DELETE':
            switch ($action) {
                case 'link':
                    handleDeleteLink($pdo, $user['id'], $_GET['link_id']);
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
    error_log("Notes API Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}

/**
 * Initialize enhanced database tables for advanced notes features
 */
function initializeEnhancedTables($pdo) {
    // Enhanced notes table
    $pdo->exec("CREATE TABLE IF NOT EXISTS notes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(500) NOT NULL,
        content LONGTEXT,
        color VARCHAR(7) DEFAULT '#fbbf24',
        is_pinned BOOLEAN DEFAULT FALSE,
        is_archived BOOLEAN DEFAULT FALSE,
        is_deleted BOOLEAN DEFAULT FALSE,
        is_favorite BOOLEAN DEFAULT FALSE,
        is_shared BOOLEAN DEFAULT FALSE,
        note_type ENUM('note', 'daily', 'knowledge', 'documentation', 'task', 'meeting') DEFAULT 'note',
        category_id INT NULL,
        template_id INT NULL,
        parent_id INT NULL,
        position_x INT DEFAULT 0,
        position_y INT DEFAULT 0,
        view_count INT DEFAULT 0,
        word_count INT DEFAULT 0,
        reading_time INT DEFAULT 0,
        priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
        reminder_date DATETIME NULL,
        due_date DATETIME NULL,
        completion_date DATETIME NULL,
        metadata JSON NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        accessed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user_type (user_id, note_type),
        INDEX idx_user_archived (user_id, is_archived),
        INDEX idx_user_deleted (user_id, is_deleted),
        INDEX idx_pinned (is_pinned),
        INDEX idx_category (category_id),
        INDEX idx_parent (parent_id),
        INDEX idx_reminder (reminder_date),
        FULLTEXT idx_search (title, content)
    )");

    // Note tags
    $pdo->exec("CREATE TABLE IF NOT EXISTS note_tags (
        id INT AUTO_INCREMENT PRIMARY KEY,
        note_id INT NOT NULL,
        tag_name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_note_tag (note_id, tag_name),
        INDEX idx_tag_name (tag_name),
        INDEX idx_note_id (note_id)
    )");

    // Note links (for connecting notes)
    $pdo->exec("CREATE TABLE IF NOT EXISTS note_links (
        id INT AUTO_INCREMENT PRIMARY KEY,
        source_note_id INT NOT NULL,
        target_note_id INT NOT NULL,
        link_type ENUM('reference', 'backlink', 'bidirectional', 'parent-child', 'similar') DEFAULT 'reference',
        description TEXT NULL,
        strength DECIMAL(3,2) DEFAULT 1.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_link (source_note_id, target_note_id, link_type),
        INDEX idx_source (source_note_id),
        INDEX idx_target (target_note_id)
    )");

    // Note categories
    $pdo->exec("CREATE TABLE IF NOT EXISTS note_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        description TEXT NULL,
        color VARCHAR(7) DEFAULT '#6b7280',
        icon VARCHAR(50) DEFAULT 'folder',
        parent_id INT NULL,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user (user_id),
        INDEX idx_parent (parent_id)
    )");

    // Note templates
    $pdo->exec("CREATE TABLE IF NOT EXISTS note_templates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        name VARCHAR(100) NOT NULL,
        description TEXT NULL,
        template_content LONGTEXT NOT NULL,
        note_type ENUM('note', 'daily', 'knowledge', 'documentation', 'task', 'meeting') DEFAULT 'note',
        is_system BOOLEAN DEFAULT FALSE,
        is_active BOOLEAN DEFAULT TRUE,
        usage_count INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user (user_id),
        INDEX idx_type (note_type)
    )");

    // Note attachments
    $pdo->exec("CREATE TABLE IF NOT EXISTS note_attachments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        note_id INT NOT NULL,
        filename VARCHAR(255) NOT NULL,
        original_filename VARCHAR(255) NOT NULL,
        file_path VARCHAR(500) NOT NULL,
        file_type VARCHAR(100) NOT NULL,
        file_size INT NOT NULL,
        mime_type VARCHAR(100) NOT NULL,
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_note_id (note_id)
    )");

    // Note versions (for history/backup)
    $pdo->exec("CREATE TABLE IF NOT EXISTS note_versions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        note_id INT NOT NULL,
        title VARCHAR(500) NOT NULL,
        content LONGTEXT,
        version_number INT NOT NULL,
        change_summary TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_note_id (note_id),
        INDEX idx_version (note_id, version_number)
    )");

    // Note shares (for collaboration)
    $pdo->exec("CREATE TABLE IF NOT EXISTS note_shares (
        id INT AUTO_INCREMENT PRIMARY KEY,
        note_id INT NOT NULL,
        shared_by_user_id INT NOT NULL,
        shared_with_user_id INT NULL,
        share_token VARCHAR(100) NULL,
        permission_level ENUM('read', 'comment', 'edit') DEFAULT 'read',
        expires_at DATETIME NULL,
        is_public BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_note_id (note_id),
        INDEX idx_token (share_token),
        INDEX idx_shared_with (shared_with_user_id)
    )");

    // Note activities (for tracking changes)
    $pdo->exec("CREATE TABLE IF NOT EXISTS note_activities (
        id INT AUTO_INCREMENT PRIMARY KEY,
        note_id INT NOT NULL,
        user_id INT NOT NULL,
        activity_type ENUM('created', 'updated', 'viewed', 'shared', 'linked', 'tagged', 'deleted') NOT NULL,
        activity_data JSON NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_note_id (note_id),
        INDEX idx_user_id (user_id),
        INDEX idx_type (activity_type)
    )");

    // Enhanced note analytics table
    $pdo->exec("CREATE TABLE IF NOT EXISTS note_analytics (
        id INT AUTO_INCREMENT PRIMARY KEY,
        note_id INT NOT NULL,
        user_id INT NOT NULL,
        metric_type ENUM('word_count', 'reading_time', 'edit_count', 'view_count', 'link_count', 'engagement_score') NOT NULL,
        metric_value DECIMAL(10,2) NOT NULL,
        recorded_at DATE NOT NULL,
        INDEX idx_note_metric (note_id, metric_type),
        INDEX idx_user_date (user_id, recorded_at),
        UNIQUE KEY unique_daily_metric (note_id, metric_type, recorded_at)
    )");

    // Smart suggestions table
    $pdo->exec("CREATE TABLE IF NOT EXISTS note_suggestions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        note_id INT NOT NULL,
        suggestion_type ENUM('tag', 'category', 'link', 'content', 'optimization') NOT NULL,
        suggestion_data JSON NOT NULL,
        confidence_score DECIMAL(3,2) DEFAULT 0.50,
        status ENUM('pending', 'accepted', 'rejected', 'auto_applied') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        applied_at TIMESTAMP NULL,
        INDEX idx_user_status (user_id, status),
        INDEX idx_note_type (note_id, suggestion_type)
    )");

    // Advanced search index table
    $pdo->exec("CREATE TABLE IF NOT EXISTS note_search_index (
        id INT AUTO_INCREMENT PRIMARY KEY,
        note_id INT NOT NULL,
        term VARCHAR(100) NOT NULL,
        term_frequency DECIMAL(5,4) NOT NULL,
        document_frequency INT DEFAULT 1,
        tfidf_score DECIMAL(8,6) GENERATED ALWAYS AS (
            term_frequency * LOG((SELECT COUNT(*) FROM notes WHERE is_deleted = 0) / document_frequency)
        ) STORED,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_note_term (note_id, term),
        INDEX idx_term_score (term, tfidf_score DESC),
        UNIQUE KEY unique_note_term (note_id, term)
    )");

    // Content embeddings for semantic search
    $pdo->exec("CREATE TABLE IF NOT EXISTS note_embeddings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        note_id INT NOT NULL,
        embedding_vector JSON NOT NULL,
        model_version VARCHAR(50) DEFAULT 'text-embedding-v1',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_note (note_id),
        INDEX idx_model (model_version)
    )");

    // User preferences for notes
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_note_preferences (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        preference_key VARCHAR(100) NOT NULL,
        preference_value JSON NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user_pref (user_id, preference_key)
    )");

    // Note workflows and automation
    $pdo->exec("CREATE TABLE IF NOT EXISTS note_workflows (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        name VARCHAR(200) NOT NULL,
        description TEXT NULL,
        trigger_conditions JSON NOT NULL,
        actions JSON NOT NULL,
        is_active BOOLEAN DEFAULT TRUE,
        execution_count INT DEFAULT 0,
        last_execution TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user_active (user_id, is_active)
    )");

    // Note collections/notebooks
    $pdo->exec("CREATE TABLE IF NOT EXISTS note_collections (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        name VARCHAR(200) NOT NULL,
        description TEXT NULL,
        color VARCHAR(7) DEFAULT '#6b7280',
        icon VARCHAR(50) DEFAULT 'collection',
        is_smart BOOLEAN DEFAULT FALSE,
        smart_rules JSON NULL,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user (user_id),
        INDEX idx_smart (is_smart)
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS note_collection_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        collection_id INT NOT NULL,
        note_id INT NOT NULL,
        position INT DEFAULT 0,
        added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_collection_note (collection_id, note_id),
        INDEX idx_collection_pos (collection_id, position)
    )");
}

/**
 * Enhanced function to get notes with advanced filtering and features
 */
function handleGetNotes($pdo, $userId) {
    try {
        $archived = $_GET['archived'] ?? 'false';
        $limit = min(intval($_GET['limit'] ?? 50), 200);
        $offset = max(intval($_GET['offset'] ?? 0), 0);
        $sortBy = $_GET['sort'] ?? 'updated';
        $sortOrder = $_GET['order'] ?? 'desc';
        $category = $_GET['category'] ?? null;
        $type = $_GET['type'] ?? null;
        $search = $_GET['search'] ?? null;
        $tags = $_GET['tags'] ?? null;
        $includeLinks = $_GET['include_links'] ?? 'false';
        $includeStats = $_GET['include_stats'] ?? 'false';

        // Build WHERE conditions
        $whereConditions = ['n.user_id = ?', 'n.is_deleted = 0'];
        $params = [$userId];

        if ($archived !== 'all') {
            $whereConditions[] = 'n.is_archived = ?';
            $params[] = ($archived === 'true') ? 1 : 0;
        }

        if ($category) {
            $whereConditions[] = 'n.category_id = ?';
            $params[] = $category;
        }

        if ($type) {
            $whereConditions[] = 'n.note_type = ?';
            $params[] = $type;
        }

        if ($search) {
            $whereConditions[] = '(MATCH(n.title, n.content) AGAINST(? IN NATURAL LANGUAGE MODE) OR n.title LIKE ? OR n.content LIKE ?)';
            $searchTerm = "%$search%";
            $params[] = $search;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if ($tags) {
            $tagArray = explode(',', $tags);
            $tagPlaceholders = str_repeat('?,', count($tagArray) - 1) . '?';
            $whereConditions[] = "n.id IN (SELECT note_id FROM note_tags WHERE tag_name IN ($tagPlaceholders))";
            $params = array_merge($params, $tagArray);
        }

        // Valid sort columns
        $validSorts = ['created', 'updated', 'title', 'view_count', 'word_count', 'priority'];
        if (!in_array($sortBy, $validSorts)) $sortBy = 'updated';
        if (!in_array(strtolower($sortOrder), ['asc', 'desc'])) $sortOrder = 'desc';

        $orderBy = match($sortBy) {
            'created' => 'n.created_at',
            'updated' => 'n.updated_at',
            'title' => 'n.title',
            'view_count' => 'n.view_count',
            'word_count' => 'n.word_count',
            'priority' => "FIELD(n.priority, 'urgent', 'high', 'medium', 'low')",
            default => 'n.updated_at'
        };

        $sql = "SELECT n.*, 
                   c.name as category_name, c.color as category_color,
                   GROUP_CONCAT(DISTINCT nt.tag_name ORDER BY nt.tag_name) as tags,
                   (SELECT COUNT(*) FROM note_links WHERE source_note_id = n.id OR target_note_id = n.id) as links_count,
                   (SELECT COUNT(*) FROM note_attachments WHERE note_id = n.id) as attachments_count
            FROM notes n 
            LEFT JOIN note_categories c ON n.category_id = c.id
            LEFT JOIN note_tags nt ON n.id = nt.note_id 
            WHERE " . implode(' AND ', $whereConditions) . "
            GROUP BY n.id
            ORDER BY n.is_pinned DESC, $orderBy $sortOrder 
            LIMIT $limit OFFSET $offset";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format response data
        foreach ($notes as &$note) {
            $note['tags'] = $note['tags'] ? explode(',', $note['tags']) : [];
            $note['is_pinned'] = (bool)$note['is_pinned'];
            $note['is_archived'] = (bool)$note['is_archived'];
            $note['is_favorite'] = (bool)$note['is_favorite'];
            $note['is_shared'] = (bool)$note['is_shared'];
            $note['id'] = (int)$note['id'];
            $note['user_id'] = (int)$note['user_id'];
            $note['links_count'] = (int)$note['links_count'];
            $note['attachments_count'] = (int)$note['attachments_count'];
            $note['view_count'] = (int)$note['view_count'];
            $note['word_count'] = (int)$note['word_count'];
            $note['reading_time'] = (int)$note['reading_time'];

            // Parse metadata
            if ($note['metadata']) {
                $note['metadata'] = json_decode($note['metadata'], true);
            }

            // Update view count and access time
            updateNoteAccess($pdo, $note['id']);
        }

        // Include links if requested
        $links = [];
        if ($includeLinks === 'true') {
            $links = getNoteLinks($pdo, $userId, array_column($notes, 'id'));
        }

        // Include stats if requested
        $stats = [];
        if ($includeStats === 'true') {
            $stats = getUserNotesStats($pdo, $userId);
        }

        // Get total count for pagination
        $countSql = "SELECT COUNT(DISTINCT n.id) as total FROM notes n 
                     LEFT JOIN note_tags nt ON n.id = nt.note_id 
                     WHERE " . implode(' AND ', $whereConditions);
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $totalCount = $countStmt->fetch()['total'];

        echo json_encode([
            'success' => true,
            'notes' => $notes,
            'links' => $links,
            'stats' => $stats,
            'pagination' => [
                'total' => (int)$totalCount,
                'limit' => $limit,
                'offset' => $offset,
                'has_more' => ($offset + $limit) < $totalCount
            ],
            'meta' => [
                'query_time' => microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],
                'filters_applied' => [
                    'archived' => $archived,
                    'category' => $category,
                    'type' => $type,
                    'search' => $search,
                    'tags' => $tags
                ]
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

/**
 * Enhanced function to create notes with advanced features
 */

function handleCreateNote($pdo, $userId, $input) {
    try {
        $title = trim($input['title'] ?? '');
        $content = trim($input['content'] ?? '');
        $color = $input['color'] ?? '#fbbf24';
        $type = $input['type'] ?? 'note';
        $category = $input['category_id'] ?? null;
        $priority = $input['priority'] ?? 'medium';
        $tags = $input['tags'] ?? [];
        $templateId = $input['template_id'] ?? null;
        $parentId = $input['parent_id'] ?? null;
        $reminderDate = $input['reminder_date'] ?? null;
        $dueDate = $input['due_date'] ?? null;
        $metadata = $input['metadata'] ?? null;
        
        if (empty($title)) {
            http_response_code(400);
            echo json_encode(['error' => 'Title is required']);
            return;
        }

        // Calculate word count and reading time
        $wordCount = str_word_count(strip_tags($content));
        $readingTime = max(1, ceil($wordCount / 200)); // Assuming 200 words per minute

        $pdo->beginTransaction();
        
        $sql = "INSERT INTO notes (
            user_id, title, content, color, note_type, category_id, priority,
            parent_id, reminder_date, due_date, word_count, reading_time, metadata
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $userId, $title, $content, $color, $type, $category, $priority,
            $parentId, $reminderDate, $dueDate, $wordCount, $readingTime,
            $metadata ? json_encode($metadata) : null
        ]);
        
        $noteId = $pdo->lastInsertId();
        
        // Add tags
        if (!empty($tags)) {
            $tagSql = "INSERT IGNORE INTO note_tags (note_id, tag_name) VALUES (?, ?)";
            $tagStmt = $pdo->prepare($tagSql);
            foreach ($tags as $tag) {
                $tag = trim($tag);
                if (!empty($tag)) {
                    $tagStmt->execute([$noteId, $tag]);
                }
            }
        }

        // Create initial version
        createNoteVersion($pdo, $noteId, $title, $content, 1, 'Initial creation');

        // Auto-link to existing notes
        createAutoLinks($pdo, $noteId, $title . ' ' . $content, $userId);

        // Log activity
        logNoteActivity($pdo, $noteId, $userId, 'created', [
            'title' => $title,
            'type' => $type,
            'word_count' => $wordCount
        ]);
        
        $pdo->commit();

        // Return created note with full details
        $createdNote = getNoteById($pdo, $noteId, $userId);
        
        echo json_encode([
            'success' => true,
            'note' => $createdNote,
            'message' => 'Notiz erfolgreich erstellt',
            'meta' => [
                'word_count' => $wordCount,
                'reading_time' => $readingTime,
                'auto_links_created' => getAutoLinksCount($pdo, $noteId)
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

/**
 * Enhanced update function with versioning and change tracking
 */

function handleUpdateNote($pdo, $userId, $input) {
    $noteId = $input['id'] ?? null;
    $title = trim($input['title'] ?? '');
    $content = trim($input['content'] ?? '');
    $color = $input['color'] ?? '#fbbf24';
    $isPinned = $input['is_pinned'] ?? false;
    $isArchived = $input['is_archived'] ?? false;
    $isFavorite = $input['is_favorite'] ?? false;
    $priority = $input['priority'] ?? 'medium';
    $categoryId = $input['category_id'] ?? null;
    $reminderDate = $input['reminder_date'] ?? null;
    $dueDate = $input['due_date'] ?? null;
    $tags = $input['tags'] ?? [];
    $metadata = $input['metadata'] ?? null;
    $changeSummary = $input['change_summary'] ?? null;
    
    if (empty($noteId) || empty($title)) {
        http_response_code(400);
        echo json_encode(['error' => 'ID and title are required']);
        return;
    }
    
    $pdo->beginTransaction();
    
    try {
        // Get current note for comparison
        $currentNote = getNoteById($pdo, $noteId, $userId);
        if (!$currentNote) {
            http_response_code(404);
            echo json_encode(['error' => 'Note not found or access denied']);
            return;
        }

        // Calculate word count and reading time
        $wordCount = str_word_count(strip_tags($content));
        $readingTime = max(1, ceil($wordCount / 200));

        // Check if content actually changed
        $contentChanged = ($currentNote['title'] !== $title) || ($currentNote['content'] !== $content);
        
        // Update note
        $sql = "UPDATE notes SET 
                title = ?, content = ?, color = ?, is_pinned = ?, is_archived = ?, 
                is_favorite = ?, priority = ?, category_id = ?, reminder_date = ?, 
                due_date = ?, word_count = ?, reading_time = ?, metadata = ?, 
                updated_at = NOW() 
                WHERE id = ? AND user_id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $title, $content, $color, $isPinned ? 1 : 0, $isArchived ? 1 : 0,
            $isFavorite ? 1 : 0, $priority, $categoryId, $reminderDate,
            $dueDate, $wordCount, $readingTime, 
            $metadata ? json_encode($metadata) : null, $noteId, $userId
        ]);

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

        // Create version if content changed
        if ($contentChanged) {
            $versionNumber = getNextVersionNumber($pdo, $noteId);
            createNoteVersion($pdo, $noteId, $title, $content, $versionNumber, $changeSummary);
        }

        // Update auto-links if content changed
        if ($contentChanged) {
            updateAutoLinks($pdo, $noteId, $title . ' ' . $content, $userId);
        }

        // Log activity
        logNoteActivity($pdo, $noteId, $userId, 'updated', [
            'changes' => array_keys(array_filter([
                'title' => $currentNote['title'] !== $title,
                'content' => $currentNote['content'] !== $content,
                'pinned' => $currentNote['is_pinned'] != $isPinned,
                'archived' => $currentNote['is_archived'] != $isArchived,
                'favorite' => $currentNote['is_favorite'] != $isFavorite
            ])),
            'word_count_change' => $wordCount - $currentNote['word_count']
        ]);

        $pdo->commit();

        // Return updated note
        $updatedNote = getNoteById($pdo, $noteId, $userId);
        
        echo json_encode([
            'success' => true,
            'note' => $updatedNote,
            'message' => 'Notiz erfolgreich aktualisiert',
            'meta' => [
                'content_changed' => $contentChanged,
                'version_created' => $contentChanged,
                'word_count_change' => $wordCount - $currentNote['word_count']
            ]
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Patch function for partial updates
 */
function handlePatchNote($pdo, $userId, $input) {
    $noteId = $input['id'] ?? null;
    
    if (empty($noteId)) {
        http_response_code(400);
        echo json_encode(['error' => 'Note ID is required']);
        return;
    }

    // Check note ownership
    $note = getNoteById($pdo, $noteId, $userId);
    if (!$note) {
        http_response_code(404);
        echo json_encode(['error' => 'Note not found']);
        return;
    }

    $updates = [];
    $params = [];
    $activityData = [];

    // Build dynamic update query based on provided fields
    $allowedFields = [
        'is_pinned', 'is_archived', 'is_favorite', 'priority', 'color',
        'category_id', 'reminder_date', 'due_date', 'position_x', 'position_y'
    ];

    foreach ($allowedFields as $field) {
        if (array_key_exists($field, $input)) {
            $updates[] = "$field = ?";
            $params[] = $input[$field];
            $activityData[$field] = $input[$field];
        }
    }

    if (empty($updates)) {
        http_response_code(400);
        echo json_encode(['error' => 'No valid fields to update']);
        return;
    }

    $updates[] = "updated_at = NOW()";
    $params[] = $noteId;
    $params[] = $userId;

    $sql = "UPDATE notes SET " . implode(', ', $updates) . " WHERE id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Log activity
    logNoteActivity($pdo, $noteId, $userId, 'updated', $activityData);

    echo json_encode([
        'success' => true,
        'message' => 'Notiz teilweise aktualisiert',
        'updated_fields' => array_keys($activityData)
    ]);
}

/**
 * Enhanced search functionality
 */
function handleSearch($pdo, $userId) {
    $query = $_GET['q'] ?? '';
    $type = $_GET['type'] ?? 'all';
    $limit = min(intval($_GET['limit'] ?? 20), 50);
    
    if (strlen($query) < 2) {
        echo json_encode(['success' => true, 'results' => [], 'message' => 'Query too short']);
        return;
    }

    try {
        // Full-text search with relevance scoring
        $sql = "SELECT n.*, 
                   MATCH(n.title, n.content) AGAINST(? IN NATURAL LANGUAGE MODE) as relevance,
                   c.name as category_name,
                   GROUP_CONCAT(DISTINCT nt.tag_name) as tags
                FROM notes n
                LEFT JOIN note_categories c ON n.category_id = c.id
                LEFT JOIN note_tags nt ON n.id = nt.note_id
                WHERE n.user_id = ? AND n.is_deleted = 0 AND (
                    MATCH(n.title, n.content) AGAINST(? IN NATURAL LANGUAGE MODE) OR
                    n.title LIKE ? OR
                    n.content LIKE ? OR
                    nt.tag_name LIKE ?
                )";

        $params = [$query, $userId, $query, "%$query%", "%$query%", "%$query%"];

        if ($type !== 'all') {
            $sql .= " AND n.note_type = ?";
            $params[] = $type;
        }

        $sql .= " GROUP BY n.id ORDER BY relevance DESC, n.updated_at DESC LIMIT $limit";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format results
        foreach ($results as &$result) {
            $result['tags'] = $result['tags'] ? explode(',', $result['tags']) : [];
            $result['relevance'] = (float)$result['relevance'];
            
            // Highlight search terms in title and content
            $result['title_highlighted'] = highlightSearchTerms($result['title'], $query);
            $result['content_snippet'] = createContentSnippet($result['content'], $query);
        }

        echo json_encode([
            'success' => true,
            'results' => $results,
            'count' => count($results),
            'query' => $query,
            'type' => $type
        ]);

    } catch (Exception $e) {
        error_log("Search error: " . $e->getMessage());
        echo json_encode(['error' => 'Search failed: ' . $e->getMessage()]);
    }
}

/**
 * Handle note linking
 */
function handleCreateLink($pdo, $userId, $input) {
    $sourceId = $input['source_note_id'] ?? null;
    $targetId = $input['target_note_id'] ?? null;
    $linkType = $input['link_type'] ?? 'reference';
    $description = $input['description'] ?? null;
    $strength = $input['strength'] ?? 1.0;

    if (!$sourceId || !$targetId) {
        http_response_code(400);
        echo json_encode(['error' => 'Source and target note IDs are required']);
        return;
    }

    // Verify both notes belong to user
    $verifyQuery = "SELECT COUNT(*) as count FROM notes WHERE id IN (?, ?) AND user_id = ? AND is_deleted = 0";
    $stmt = $pdo->prepare($verifyQuery);
    $stmt->execute([$sourceId, $targetId, $userId]);
    
    if ($stmt->fetch()['count'] !== 2) {
        http_response_code(404);
        echo json_encode(['error' => 'One or both notes not found']);
        return;
    }

    try {
        $sql = "INSERT INTO note_links (source_note_id, target_note_id, link_type, description, strength) 
                VALUES (?, ?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE 
                link_type = VALUES(link_type), 
                description = VALUES(description), 
                strength = VALUES(strength)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$sourceId, $targetId, $linkType, $description, $strength]);

        // Log activities for both notes
        logNoteActivity($pdo, $sourceId, $userId, 'linked', ['target_note_id' => $targetId, 'link_type' => $linkType]);
        logNoteActivity($pdo, $targetId, $userId, 'linked', ['source_note_id' => $sourceId, 'link_type' => $linkType]);

        echo json_encode([
            'success' => true,
            'message' => 'Notizen erfolgreich verknüpft',
            'link_id' => $pdo->lastInsertId()
        ]);

    } catch (Exception $e) {
        error_log("Link creation error: " . $e->getMessage());
        echo json_encode(['error' => 'Failed to create link: ' . $e->getMessage()]);
    }
}

/**
 * Get note links
 */
function handleGetLinks($pdo, $userId, $noteId = null) {
    try {
        if ($noteId) {
            // Get links for specific note
            $sql = "SELECT nl.*, 
                       sn.title as source_title, sn.color as source_color,
                       tn.title as target_title, tn.color as target_color
                    FROM note_links nl
                    JOIN notes sn ON nl.source_note_id = sn.id
                    JOIN notes tn ON nl.target_note_id = tn.id
                    WHERE (nl.source_note_id = ? OR nl.target_note_id = ?) 
                    AND sn.user_id = ? AND tn.user_id = ?
                    AND sn.is_deleted = 0 AND tn.is_deleted = 0";
            $params = [$noteId, $noteId, $userId, $userId];
        } else {
            // Get all user's links
            $sql = "SELECT nl.*, 
                       sn.title as source_title, sn.color as source_color,
                       tn.title as target_title, tn.color as target_color
                    FROM note_links nl
                    JOIN notes sn ON nl.source_note_id = sn.id
                    JOIN notes tn ON nl.target_note_id = tn.id
                    WHERE sn.user_id = ? AND tn.user_id = ?
                    AND sn.is_deleted = 0 AND tn.is_deleted = 0";
            $params = [$userId, $userId];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $links = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'links' => $links,
            'count' => count($links)
        ]);

    } catch (Exception $e) {
        error_log("Get links error: " . $e->getMessage());
        echo json_encode(['error' => 'Failed to get links: ' . $e->getMessage()]);
    }
}

/**
 * Get backlinks for a note
 */
function handleGetBacklinks($pdo, $userId, $noteId) {
    if (!$noteId) {
        http_response_code(400);
        echo json_encode(['error' => 'Note ID is required']);
        return;
    }

    try {
        // Get notes that link to this note
        $sql = "SELECT DISTINCT n.id, n.title, n.color, n.note_type, n.updated_at,
                   nl.link_type, nl.description, nl.strength
                FROM notes n
                JOIN note_links nl ON n.id = nl.source_note_id
                WHERE nl.target_note_id = ? AND n.user_id = ? AND n.is_deleted = 0
                ORDER BY nl.created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$noteId, $userId]);
        $backlinks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'backlinks' => $backlinks,
            'count' => count($backlinks)
        ]);

    } catch (Exception $e) {
        error_log("Get backlinks error: " . $e->getMessage());
        echo json_encode(['error' => 'Failed to get backlinks: ' . $e->getMessage()]);
    }
}

/**
 * Duplicate a note
 */
function handleDuplicateNote($pdo, $userId, $input) {
    $noteId = $input['note_id'] ?? null;
    $newTitle = $input['new_title'] ?? null;

    if (!$noteId) {
        http_response_code(400);
        echo json_encode(['error' => 'Note ID is required']);
        return;
    }

    try {
        // Get original note
        $original = getNoteById($pdo, $noteId, $userId);
        if (!$original) {
            http_response_code(404);
            echo json_encode(['error' => 'Note not found']);
            return;
        }

        // Create duplicate
        $duplicateTitle = $newTitle ?: $original['title'] . ' (Kopie)';
        
        $sql = "INSERT INTO notes (
            user_id, title, content, color, note_type, category_id, priority,
            word_count, reading_time, metadata
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $userId, $duplicateTitle, $original['content'], $original['color'],
            $original['note_type'], $original['category_id'], $original['priority'],
            $original['word_count'], $original['reading_time'], $original['metadata']
        ]);

        $duplicateId = $pdo->lastInsertId();

        // Copy tags
        $copyTagsSql = "INSERT INTO note_tags (note_id, tag_name) 
                       SELECT ?, tag_name FROM note_tags WHERE note_id = ?";
        $pdo->prepare($copyTagsSql)->execute([$duplicateId, $noteId]);

        // Log activity
        logNoteActivity($pdo, $duplicateId, $userId, 'created', [
            'action' => 'duplicated',
            'original_note_id' => $noteId
        ]);

        echo json_encode([
            'success' => true,
            'duplicate_id' => $duplicateId,
            'message' => 'Notiz erfolgreich dupliziert'
        ]);

    } catch (Exception $e) {
        error_log("Duplicate note error: " . $e->getMessage());
        echo json_encode(['error' => 'Failed to duplicate note: ' . $e->getMessage()]);
    }
}

/**
 * Handle bulk operations
 */
function handleBulkOperation($pdo, $userId, $input) {
    $operation = $input['operation'] ?? null;
    $noteIds = $input['note_ids'] ?? [];

    if (!$operation || empty($noteIds)) {
        http_response_code(400);
        echo json_encode(['error' => 'Operation and note IDs are required']);
        return;
    }

    // Verify all notes belong to user
    $placeholders = str_repeat('?,', count($noteIds) - 1) . '?';
    $verifyQuery = "SELECT COUNT(*) as count FROM notes WHERE id IN ($placeholders) AND user_id = ?";
    $params = array_merge($noteIds, [$userId]);
    $stmt = $pdo->prepare($verifyQuery);
    $stmt->execute($params);
    
    if ($stmt->fetch()['count'] !== count($noteIds)) {
        http_response_code(404);
        echo json_encode(['error' => 'Some notes not found']);
        return;
    }

    try {
        $pdo->beginTransaction();
        $affectedRows = 0;

        switch ($operation) {
            case 'archive':
                $sql = "UPDATE notes SET is_archived = 1, updated_at = NOW() WHERE id IN ($placeholders) AND user_id = ?";
                break;
            case 'unarchive':
                $sql = "UPDATE notes SET is_archived = 0, updated_at = NOW() WHERE id IN ($placeholders) AND user_id = ?";
                break;
            case 'pin':
                $sql = "UPDATE notes SET is_pinned = 1, updated_at = NOW() WHERE id IN ($placeholders) AND user_id = ?";
                break;
            case 'unpin':
                $sql = "UPDATE notes SET is_pinned = 0, updated_at = NOW() WHERE id IN ($placeholders) AND user_id = ?";
                break;
            case 'favorite':
                $sql = "UPDATE notes SET is_favorite = 1, updated_at = NOW() WHERE id IN ($placeholders) AND user_id = ?";
                break;
            case 'unfavorite':
                $sql = "UPDATE notes SET is_favorite = 0, updated_at = NOW() WHERE id IN ($placeholders) AND user_id = ?";
                break;
            case 'delete':
                $sql = "UPDATE notes SET is_deleted = 1, updated_at = NOW() WHERE id IN ($placeholders) AND user_id = ?";
                break;
            case 'category':
                $categoryId = $input['category_id'] ?? null;
                $sql = "UPDATE notes SET category_id = ?, updated_at = NOW() WHERE id IN ($placeholders) AND user_id = ?";
                $params = array_merge([$categoryId], $noteIds, [$userId]);
                break;
            default:
                throw new Exception('Invalid operation');
        }

        if (!isset($params)) {
            $params = array_merge($noteIds, [$userId]);
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $affectedRows = $stmt->rowCount();

        // Log activities
        foreach ($noteIds as $noteId) {
            logNoteActivity($pdo, $noteId, $userId, 'updated', [
                'bulk_operation' => $operation,
                'note_count' => count($noteIds)
            ]);
        }

        $pdo->commit();

        echo json_encode([
            'success' => true,
            'affected_rows' => $affectedRows,
            'message' => "Bulk operation '$operation' completed successfully"
        ]);

    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Bulk operation error: " . $e->getMessage());
        echo json_encode(['error' => 'Bulk operation failed: ' . $e->getMessage()]);
    }
}

/**
 * AI-Powered Suggestions
 */
function handleAISuggestions($pdo, $userId, $noteId) {
    if (!$noteId) {
        http_response_code(400);
        echo json_encode(['error' => 'Note ID is required']);
        return;
    }

    try {
        $note = getNoteById($pdo, $noteId, $userId);
        if (!$note) {
            http_response_code(404);
            echo json_encode(['error' => 'Note not found']);
            return;
        }

        $suggestions = [];

        // Suggest tags based on content
        $suggestedTags = generateTagSuggestions($pdo, $note['content'], $userId);
        if (!empty($suggestedTags)) {
            $suggestions[] = [
                'type' => 'tags',
                'data' => $suggestedTags,
                'confidence' => 0.8,
                'reason' => 'Based on content analysis'
            ];
        }

        // Suggest categories
        $suggestedCategory = suggestCategory($pdo, $note['content'], $userId);
        if ($suggestedCategory) {
            $suggestions[] = [
                'type' => 'category',
                'data' => $suggestedCategory,
                'confidence' => 0.7,
                'reason' => 'Content similarity analysis'
            ];
        }

        // Suggest related notes for linking
        $relatedNotes = findRelatedNotes($pdo, $noteId, $note['content'], $userId);
        if (!empty($relatedNotes)) {
            $suggestions[] = [
                'type' => 'links',
                'data' => array_slice($relatedNotes, 0, 5),
                'confidence' => 0.6,
                'reason' => 'Semantic similarity'
            ];
        }

        // Content improvement suggestions
        $contentSuggestions = analyzeContentQuality($note['content']);
        if (!empty($contentSuggestions)) {
            $suggestions[] = [
                'type' => 'content_improvement',
                'data' => $contentSuggestions,
                'confidence' => 0.5,
                'reason' => 'Content analysis'
            ];
        }

        echo json_encode([
            'success' => true,
            'suggestions' => $suggestions,
            'note_id' => $noteId
        ]);

    } catch (Exception $e) {
        error_log("AI suggestions error: " . $e->getMessage());
        echo json_encode(['error' => 'Failed to generate suggestions: ' . $e->getMessage()]);
    }
}

/**
 * Smart Search with NLP and semantic analysis
 */
function handleSmartSearch($pdo, $userId) {
    $query = $_GET['q'] ?? '';
    $mode = $_GET['mode'] ?? 'semantic'; // semantic, keyword, hybrid
    $limit = min(intval($_GET['limit'] ?? 20), 50);
    
    if (strlen($query) < 2) {
        echo json_encode(['success' => true, 'results' => [], 'message' => 'Query too short']);
        return;
    }

    try {
        $results = [];

        switch ($mode) {
            case 'semantic':
                $results = performSemanticSearch($pdo, $query, $userId, $limit);
                break;
            case 'keyword':
                $results = performKeywordSearch($pdo, $query, $userId, $limit);
                break;
            case 'hybrid':
                $semanticResults = performSemanticSearch($pdo, $query, $userId, $limit / 2);
                $keywordResults = performKeywordSearch($pdo, $query, $userId, $limit / 2);
                $results = mergeSearchResults($semanticResults, $keywordResults);
                break;
        }

        // Add search analytics
        recordSearchAnalytics($pdo, $userId, $query, count($results), $mode);

        echo json_encode([
            'success' => true,
            'results' => $results,
            'query' => $query,
            'mode' => $mode,
            'count' => count($results)
        ]);

    } catch (Exception $e) {
        error_log("Smart search error: " . $e->getMessage());
        echo json_encode(['error' => 'Smart search failed: ' . $e->getMessage()]);
    }
}

/**
 * Get related notes using advanced algorithms
 */
function handleGetRelatedNotes($pdo, $userId, $noteId) {
    if (!$noteId) {
        http_response_code(400);
        echo json_encode(['error' => 'Note ID is required']);
        return;
    }

    try {
        $note = getNoteById($pdo, $noteId, $userId);
        if (!$note) {
            http_response_code(404);
            echo json_encode(['error' => 'Note not found']);
            return;
        }

        $relatedNotes = [];

        // Find notes with similar tags
        $tagRelated = findNotesByTags($pdo, $note['tags'], $userId, $noteId);
        
        // Find notes in same category
        $categoryRelated = findNotesByCategory($pdo, $note['category_id'], $userId, $noteId);
        
        // Find notes with content similarity
        $contentRelated = findNotesByContentSimilarity($pdo, $note['content'], $userId, $noteId);

        // Merge and score results
        $allRelated = array_merge($tagRelated, $categoryRelated, $contentRelated);
        $relatedNotes = scoreAndSortRelated($allRelated);

        echo json_encode([
            'success' => true,
            'related_notes' => array_slice($relatedNotes, 0, 10),
            'count' => count($relatedNotes)
        ]);

    } catch (Exception $e) {
        error_log("Get related notes error: " . $e->getMessage());
        echo json_encode(['error' => 'Failed to get related notes: ' . $e->getMessage()]);
    }
}

/**
 * Advanced Analytics Dashboard
 */
function handleGetAnalytics($pdo, $userId) {
    try {
        $timeframe = $_GET['timeframe'] ?? '30d'; // 7d, 30d, 90d, 1y
        $analytics = [];

        // Note creation trends
        $analytics['creation_trends'] = getCreationTrends($pdo, $userId, $timeframe);
        
        // Content analysis
        $analytics['content_stats'] = getContentAnalytics($pdo, $userId);
        
        // Link network analysis
        $analytics['network_stats'] = getNetworkAnalytics($pdo, $userId);
        
        // Category distribution
        $analytics['category_distribution'] = getCategoryDistribution($pdo, $userId);
        
        // Tag usage patterns
        $analytics['tag_patterns'] = getTagUsagePatterns($pdo, $userId);
        
        // Productivity metrics
        $analytics['productivity'] = getProductivityMetrics($pdo, $userId, $timeframe);
        
        // Search patterns
        $analytics['search_patterns'] = getSearchPatterns($pdo, $userId, $timeframe);

        echo json_encode([
            'success' => true,
            'analytics' => $analytics,
            'generated_at' => date('c')
        ]);

    } catch (Exception $e) {
        error_log("Analytics error: " . $e->getMessage());
        echo json_encode(['error' => 'Failed to generate analytics: ' . $e->getMessage()]);
    }
}

/**
 * Graph Data for Network Visualization
 */
function handleGetGraphData($pdo, $userId) {
    try {
        $includeOrphans = $_GET['include_orphans'] ?? 'false';
        $minConnections = intval($_GET['min_connections'] ?? 1);
        
        // Get nodes (notes)
        $nodesSql = "SELECT n.id, n.title, n.color, n.note_type, n.is_pinned, n.is_favorite,
                           c.name as category_name, c.color as category_color,
                           COUNT(DISTINCT nl1.id) + COUNT(DISTINCT nl2.id) as connection_count
                    FROM notes n
                    LEFT JOIN note_categories c ON n.category_id = c.id
                    LEFT JOIN note_links nl1 ON n.id = nl1.source_note_id
                    LEFT JOIN note_links nl2 ON n.id = nl2.target_note_id
                    WHERE n.user_id = ? AND n.is_deleted = 0
                    GROUP BY n.id";
        
        if ($includeOrphans === 'false') {
            $nodesSql .= " HAVING connection_count > 0";
        }
        
        if ($minConnections > 1) {
            $nodesSql .= " HAVING connection_count >= ?";
            $params = [$userId, $minConnections];
        } else {
            $params = [$userId];
        }

        $stmt = $pdo->prepare($nodesSql);
        $stmt->execute($params);
        $nodes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get edges (links)
        $edgesSql = "SELECT nl.source_note_id as source, nl.target_note_id as target,
                           nl.link_type, nl.strength, nl.description
                    FROM note_links nl
                    JOIN notes sn ON nl.source_note_id = sn.id
                    JOIN notes tn ON nl.target_note_id = tn.id
                    WHERE sn.user_id = ? AND tn.user_id = ?
                    AND sn.is_deleted = 0 AND tn.is_deleted = 0";

        $stmt = $pdo->prepare($edgesSql);
        $stmt->execute([$userId, $userId]);
        $edges = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate network metrics
        $metrics = calculateNetworkMetrics($nodes, $edges);

        echo json_encode([
            'success' => true,
            'graph' => [
                'nodes' => $nodes,
                'edges' => $edges,
                'metrics' => $metrics
            ]
        ]);

    } catch (Exception $e) {
        error_log("Graph data error: " . $e->getMessage());
        echo json_encode(['error' => 'Failed to get graph data: ' . $e->getMessage()]);
    }
}

/**
 * Intelligent Recommendations
 */
function handleGetRecommendations($pdo, $userId) {
    try {
        $recommendations = [];

        // Note to review (old, unlinked notes)
        $toReview = getNotesToReview($pdo, $userId);
        if (!empty($toReview)) {
            $recommendations[] = [
                'type' => 'review',
                'title' => 'Notizen zum Überprüfen',
                'description' => 'Diese Notizen wurden lange nicht bearbeitet',
                'items' => $toReview,
                'priority' => 'medium'
            ];
        }

        // Notes to link
        $toLinkPairs = getNotesToLink($pdo, $userId);
        if (!empty($toLinkPairs)) {
            $recommendations[] = [
                'type' => 'linking',
                'title' => 'Verknüpfungsvorschläge',
                'description' => 'Diese Notizen könnten miteinander verknüpft werden',
                'items' => $toLinkPairs,
                'priority' => 'high'
            ];
        }

        // Categories to create
        $categoryRecommendations = getCategoryRecommendations($pdo, $userId);
        if (!empty($categoryRecommendations)) {
            $recommendations[] = [
                'type' => 'categories',
                'title' => 'Neue Kategorien vorgeschlagen',
                'description' => 'Basierend auf Ihren Notizen',
                'items' => $categoryRecommendations,
                'priority' => 'low'
            ];
        }

        // Templates to create
        $templateRecommendations = getTemplateRecommendations($pdo, $userId);
        if (!empty($templateRecommendations)) {
            $recommendations[] = [
                'type' => 'templates',
                'title' => 'Template-Vorschläge',
                'description' => 'Erstellen Sie Templates für häufige Notizstrukturen',
                'items' => $templateRecommendations,
                'priority' => 'low'
            ];
        }

        echo json_encode([
            'success' => true,
            'recommendations' => $recommendations,
            'count' => count($recommendations)
        ]);

    } catch (Exception $e) {
        error_log("Recommendations error: " . $e->getMessage());
        echo json_encode(['error' => 'Failed to get recommendations: ' . $e->getMessage()]);
    }
}

/**
 * Quick Capture for rapid note taking
 */
function handleQuickCapture($pdo, $userId, $input) {
    try {
        $content = $input['content'] ?? '';
        $source = $input['source'] ?? 'web'; // web, mobile, email, etc.
        $autoProcess = $input['auto_process'] ?? true;
        
        if (empty($content)) {
            http_response_code(400);
            echo json_encode(['error' => 'Content is required']);
            return;
        }

        // Extract title from content
        $title = extractTitleFromContent($content);
        
        // Auto-detect note type
        $noteType = detectNoteType($content);
        
        // Quick create note
        $noteData = [
            'title' => $title,
            'content' => $content,
            'note_type' => $noteType,
            'metadata' => json_encode([
                'source' => $source,
                'quick_capture' => true,
                'captured_at' => date('c')
            ])
        ];

        if ($autoProcess) {
            // Auto-tag
            $suggestedTags = generateTagSuggestions($pdo, $content, $userId);
            $noteData['tags'] = array_slice($suggestedTags, 0, 3);
            
            // Auto-categorize
            $suggestedCategory = suggestCategory($pdo, $content, $userId);
            if ($suggestedCategory) {
                $noteData['category_id'] = $suggestedCategory['id'];
            }
        }

        $note = createNoteInternal($pdo, $userId, $noteData);
        
        echo json_encode([
            'success' => true,
            'note' => $note,
            'message' => 'Schnell-Notiz erfolgreich erstellt'
        ]);

    } catch (Exception $e) {
        error_log("Quick capture error: " . $e->getMessage());
        echo json_encode(['error' => 'Quick capture failed: ' . $e->getMessage()]);
    }
}

/**
 * Batch operations for multiple notes
 */
function handleBatchEdit($pdo, $userId, $input) {
    $noteIds = $input['note_ids'] ?? [];
    $changes = $input['changes'] ?? [];
    
    if (empty($noteIds) || empty($changes)) {
        http_response_code(400);
        echo json_encode(['error' => 'Note IDs and changes are required']);
        return;
    }

    try {
        $pdo->beginTransaction();
        $results = [];

        foreach ($noteIds as $noteId) {
            $note = getNoteById($pdo, $noteId, $userId);
            if ($note) {
                $updateResult = updateNoteInternal($pdo, $noteId, $userId, $changes);
                $results[] = [
                    'note_id' => $noteId,
                    'success' => $updateResult !== false,
                    'changes' => $changes
                ];
            }
        }

        $pdo->commit();

        echo json_encode([
            'success' => true,
            'results' => $results,
            'message' => count($results) . ' Notizen wurden bearbeitet'
        ]);

    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Batch edit error: " . $e->getMessage());
        echo json_encode(['error' => 'Batch edit failed: ' . $e->getMessage()]);
    }
}

/**
 * Helper Functions for New Features
 */
function generateTagSuggestions($pdo, $content, $userId) {
    $tags = [];
    
    // Extract keywords using simple NLP
    $words = str_word_count(strtolower($content), 1);
    $words = array_filter($words, function($word) {
        return strlen($word) > 3 && !in_array($word, ['that', 'this', 'with', 'from', 'have', 'been', 'they', 'were', 'said']);
    });
    
    $wordFreq = array_count_values($words);
    arsort($wordFreq);
    
    // Get existing popular tags
    $stmt = $pdo->prepare("
        SELECT tag_name, COUNT(*) as usage_count 
        FROM note_tags nt 
        JOIN notes n ON nt.note_id = n.id 
        WHERE n.user_id = ? 
        GROUP BY tag_name 
        ORDER BY usage_count DESC 
        LIMIT 20
    ");
    $stmt->execute([$userId]);
    $popularTags = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Combine and score suggestions
    $suggestions = array_keys(array_slice($wordFreq, 0, 5));
    foreach ($popularTags as $tag) {
        if (stripos($content, $tag['tag_name']) !== false) {
            $suggestions[] = $tag['tag_name'];
        }
    }
    
    return array_unique($suggestions);
}

function suggestCategory($pdo, $content, $userId) {
    // Get user's categories with sample content
    $stmt = $pdo->prepare("
        SELECT c.*, GROUP_CONCAT(n.content SEPARATOR ' ') as sample_content
        FROM note_categories c
        LEFT JOIN notes n ON c.id = n.category_id AND n.user_id = ?
        WHERE c.user_id = ?
        GROUP BY c.id
        ORDER BY COUNT(n.id) DESC
    ");
    $stmt->execute([$userId, $userId]);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $bestMatch = null;
    $bestScore = 0;
    
    foreach ($categories as $category) {
        if ($category['sample_content']) {
            $score = calculateTextSimilarity($content, $category['sample_content']);
            if ($score > $bestScore && $score > 0.3) {
                $bestScore = $score;
                $bestMatch = $category;
            }
        }
    }
    
    return $bestMatch;
}

function calculateTextSimilarity($text1, $text2) {
    $words1 = array_unique(str_word_count(strtolower($text1), 1));
    $words2 = array_unique(str_word_count(strtolower($text2), 1));
    
    $intersection = count(array_intersect($words1, $words2));
    $union = count(array_unique(array_merge($words1, $words2)));
    
    return $union > 0 ? $intersection / $union : 0;
}

function extractTitleFromContent($content) {
    // Extract first line or first sentence as title
    $lines = explode("\n", trim($content));
    $firstLine = trim($lines[0]);
    
    if (strlen($firstLine) > 3 && strlen($firstLine) < 100) {
        return $firstLine;
    }
    
    // Fallback to first sentence
    $sentences = explode('.', $content);
    $firstSentence = trim($sentences[0]);
    
    if (strlen($firstSentence) > 3 && strlen($firstSentence) < 100) {
        return $firstSentence;
    }
    
    return 'Unbenannte Notiz ' . date('Y-m-d H:i');
}

function detectNoteType($content) {
    // Simple heuristics to detect note type
    $content = strtolower($content);
    
    if (strpos($content, 'todo') !== false || strpos($content, '- [ ]') !== false || strpos($content, 'aufgabe') !== false) {
        return 'task';
    }
    
    if (strpos($content, 'meeting') !== false || strpos($content, 'besprechung') !== false || strpos($content, 'protokoll') !== false) {
        return 'meeting';
    }
    
    if (preg_match('/\d{4}-\d{2}-\d{2}/', $content) || strpos($content, 'heute') !== false || strpos($content, 'daily') !== false) {
        return 'daily';
    }
    
    if (strlen($content) > 1000 || strpos($content, 'documentation') !== false || strpos($content, 'dokumentation') !== false) {
        return 'documentation';
    }
    
    return 'note';
}

function createNoteInternal($pdo, $userId, $noteData) {
    $sql = "INSERT INTO notes (
        user_id, title, content, color, note_type, category_id, priority,
        word_count, reading_time, metadata, created_at, updated_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
    
    $wordCount = str_word_count($noteData['content'] ?? '');
    $readingTime = max(1, ceil($wordCount / 200)); // Assuming 200 WPM reading speed
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $userId,
        $noteData['title'],
        $noteData['content'] ?? '',
        $noteData['color'] ?? '#fbbf24',
        $noteData['note_type'] ?? 'note',
        $noteData['category_id'] ?? null,
        $noteData['priority'] ?? 'medium',
        $wordCount,
        $readingTime,
        $noteData['metadata'] ?? null
    ]);
    
    $noteId = $pdo->lastInsertId();
    
    // Add tags if provided
    if (!empty($noteData['tags'])) {
        foreach ($noteData['tags'] as $tag) {
            $tagSql = "INSERT IGNORE INTO note_tags (note_id, tag_name) VALUES (?, ?)";
            $pdo->prepare($tagSql)->execute([$noteId, trim($tag)]);
        }
    }
    
    // Log activity
    logNoteActivity($pdo, $noteId, $userId, 'created', ['quick_capture' => true]);
    
    return getNoteById($pdo, $noteId, $userId);
}

// Final safety check - clean any remaining output buffer
if (ob_get_level()) {
    $bufferContent = ob_get_contents();
    if (trim($bufferContent) !== '') {
        ob_clean();
        
        // If there was unexpected output, return it as an error
        if (!headers_sent()) {
            header('Content-Type: application/json');
            http_response_code(500);
        }
        
        echo json_encode([
            'error' => 'Unexpected output detected',
            'debug_output' => $bufferContent
        ]);
        exit;
    }
    ob_end_clean();
}

?>
