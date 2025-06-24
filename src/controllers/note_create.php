<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';

requireLogin();

// Ensure tables exist
require_once __DIR__ . '/../../database/notes_tables.php';

$userId = $_SESSION['user_id'];
$type = $_GET['type'] ?? 'note';
$date = $_GET['date'] ?? date('Y-m-d');
$templateId = $_GET['template'] ?? null;

$success = '';
$errors = [];

// Load categories and templates
$stmt = $pdo->prepare("SELECT * FROM note_categories WHERE user_id = ? ORDER BY sort_order, name");
$stmt->execute([$userId]);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM note_templates WHERE user_id = ? OR is_system = 1 ORDER BY is_system DESC, name");
$stmt->execute([$userId]);
$templates = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Load template content if specified
$templateContent = '';
if ($templateId) {
    $stmt = $pdo->prepare("SELECT template_content FROM note_templates WHERE id = ? AND (user_id = ? OR is_system = 1)");
    $stmt->execute([$templateId, $userId]);
    $template = $stmt->fetch();
    if ($template) {
        $templateContent = $template['template_content'];
        // Replace placeholders
        $templateContent = str_replace('{{date}}', date('Y-m-d'), $templateContent);
        $templateContent = str_replace('{{title}}', '', $templateContent);
    }
}

// Handle daily note creation
if ($type === 'daily') {
    // Check if daily note already exists
    $stmt = $pdo->prepare("SELECT n.id FROM notes n JOIN daily_notes d ON n.id = d.note_id WHERE d.date = ? AND d.user_id = ?");
    $stmt->execute([$date, $userId]);
    $existingNote = $stmt->fetch();
    
    if ($existingNote) {
        header("Location: /note_detail.php?id=" . $existingNote['id']);
        exit;
    }
    
    // Use daily template if no specific template selected
    if (!$templateId) {
        $stmt = $pdo->prepare("SELECT template_content FROM note_templates WHERE type = 'daily' AND (user_id = ? OR is_system = 1) ORDER BY is_system DESC LIMIT 1");
        $stmt->execute([$userId]);
        $template = $stmt->fetch();
        if ($template) {
            $templateContent = str_replace('{{date}}', $date, $template['template_content']);
        }
    }
}

// Handle form submission
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
            header("Location: /note_detail.php?id=$noteId");
            exit;
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = 'Datenbankfehler: ' . $e->getMessage();
        }
    }
}

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

require_once __DIR__ . '/../../templates/note_create.php';
?>
