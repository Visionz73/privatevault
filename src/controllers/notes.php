<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';

requireLogin();

// Create tables if they don't exist
require_once __DIR__ . '/../../database/notes_tables.php';

$userId = $_SESSION['user_id'];
$action = $_GET['action'] ?? 'index';
$view = $_GET['view'] ?? 'grid';
$category = $_GET['category'] ?? null;
$type = $_GET['type'] ?? null;
$search = $_GET['search'] ?? '';

$success = '';
$errors = [];

// Handle actions
switch ($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $noteType = $_POST['type'] ?? 'note';
            $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
            $tags = trim($_POST['tags'] ?? '');
            $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
            
            if (empty($title)) {
                $errors[] = 'Titel ist erforderlich';
            }
            
            if (empty($errors)) {
                try {
                    $pdo->beginTransaction();
                    
                    // Create note
                    $stmt = $pdo->prepare("
                        INSERT INTO notes (title, content, type, category_id, user_id, parent_id, tags)
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([$title, $content, $noteType, $categoryId, $userId, $parentId, $tags]);
                    $noteId = $pdo->lastInsertId();
                    
                    // Handle daily notes
                    if ($noteType === 'daily') {
                        $date = $_POST['date'] ?? date('Y-m-d');
                        $mood = $_POST['mood'] ?? null;
                        $weather = $_POST['weather'] ?? null;
                        
                        $stmt = $pdo->prepare("
                            INSERT INTO daily_notes (note_id, date, user_id, mood, weather)
                            VALUES (?, ?, ?, ?, ?)
                        ");
                        $stmt->execute([$noteId, $date, $userId, $mood, $weather]);
                    }
                    
                    // Auto-detect and create links
                    createAutoLinks($pdo, $noteId, $content, $userId);
                    
                    $pdo->commit();
                    $success = 'Notiz erfolgreich erstellt';
                    header("Location: /notes.php?id=$noteId");
                    exit;
                    
                } catch (PDOException $e) {
                    $pdo->rollBack();
                    $errors[] = 'Datenbankfehler: ' . $e->getMessage();
                }
            }
        }
        break;
        
    case 'edit':
        $noteId = (int)($_GET['id'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
            $tags = trim($_POST['tags'] ?? '');
            
            if (empty($title)) {
                $errors[] = 'Titel ist erforderlich';
            }
            
            if (empty($errors)) {
                try {
                    $stmt = $pdo->prepare("
                        UPDATE notes 
                        SET title = ?, content = ?, category_id = ?, tags = ?, updated_at = NOW()
                        WHERE id = ? AND user_id = ?
                    ");
                    $stmt->execute([$title, $content, $categoryId, $tags, $noteId, $userId]);
                    
                    // Update links
                    updateAutoLinks($pdo, $noteId, $content, $userId);
                    
                    $success = 'Notiz erfolgreich aktualisiert';
                    header("Location: /notes.php?id=$noteId");
                    exit;
                    
                } catch (PDOException $e) {
                    $errors[] = 'Datenbankfehler: ' . $e->getMessage();
                }
            }
        }
        break;
        
    case 'delete':
        $noteId = (int)($_GET['id'] ?? 0);
        if ($noteId > 0) {
            try {
                $stmt = $pdo->prepare("
                    UPDATE notes SET is_deleted = 1 
                    WHERE id = ? AND user_id = ?
                ");
                $stmt->execute([$noteId, $userId]);
                $success = 'Notiz gelöscht';
                header('Location: /notes.php');
                exit;
            } catch (PDOException $e) {
                $errors[] = 'Fehler beim Löschen: ' . $e->getMessage();
            }
        }
        break;
}

// Load categories
$stmt = $pdo->prepare("
    SELECT * FROM note_categories 
    WHERE user_id = ? 
    ORDER BY sort_order, name
");
$stmt->execute([$userId]);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Load notes based on filters
$whereConditions = ['n.user_id = ?', 'n.is_deleted = 0'];
$params = [$userId];

if ($category) {
    $whereConditions[] = 'n.category_id = ?';
    $params[] = $category;
}

if ($type) {
    $whereConditions[] = 'n.type = ?';
    $params[] = $type;
}

if ($search) {
    $whereConditions[] = '(n.title LIKE ? OR n.content LIKE ? OR n.tags LIKE ?)';
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$sql = "
    SELECT n.*, c.name as category_name, c.color as category_color,
           (SELECT COUNT(*) FROM note_links WHERE source_note_id = n.id OR target_note_id = n.id) as link_count
    FROM notes n
    LEFT JOIN note_categories c ON n.category_id = c.id
    WHERE " . implode(' AND ', $whereConditions) . "
    ORDER BY n.updated_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Load templates
$stmt = $pdo->prepare("
    SELECT * FROM note_templates 
    WHERE user_id = ? OR is_system = 1
    ORDER BY is_system DESC, name
");
$stmt->execute([$userId]);
$templates = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Functions for auto-linking
function createAutoLinks($pdo, $noteId, $content, $userId) {
    // Find [[Note Title]] patterns
    preg_match_all('/\[\[([^\]]+)\]\]/', $content, $matches);
    
    foreach ($matches[1] as $linkTitle) {
        $stmt = $pdo->prepare("
            SELECT id FROM notes 
            WHERE title = ? AND user_id = ? AND is_deleted = 0
        ");
        $stmt->execute([$linkTitle, $userId]);
        $targetNote = $stmt->fetch();
        
        if ($targetNote) {
            $stmt = $pdo->prepare("
                INSERT IGNORE INTO note_links (source_note_id, target_note_id, link_type)
                VALUES (?, ?, 'reference')
            ");
            $stmt->execute([$noteId, $targetNote['id']]);
        }
    }
}

function updateAutoLinks($pdo, $noteId, $content, $userId) {
    // Remove existing auto-generated links
    $stmt = $pdo->prepare("
        DELETE FROM note_links 
        WHERE source_note_id = ? AND link_type = 'reference'
    ");
    $stmt->execute([$noteId]);
    
    // Recreate links
    createAutoLinks($pdo, $noteId, $content, $userId);
}

require_once __DIR__ . '/../../templates/notes.php';
?>
