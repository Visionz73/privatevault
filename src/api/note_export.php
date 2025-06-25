<?php
/**
 * Note Export/Import Functionality
 */
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user = getUser();
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'export':
        handleExport($pdo, $user['id']);
        break;
    case 'import':
        handleImport($pdo, $user['id']);
        break;
    case 'export_markdown':
        handleMarkdownExport($pdo, $user['id']);
        break;
    case 'backup':
        handleFullBackup($pdo, $user['id']);
        break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
}

function handleExport($pdo, $userId) {
    $format = $_GET['format'] ?? 'json';
    $noteIds = isset($_GET['notes']) ? explode(',', $_GET['notes']) : null;
    
    try {
        // Get notes to export
        $sql = "SELECT n.*, c.name as category_name, GROUP_CONCAT(nt.tag_name) as tags
                FROM notes n 
                LEFT JOIN note_categories c ON n.category_id = c.id
                LEFT JOIN note_tags nt ON n.id = nt.note_id
                WHERE n.user_id = ? AND n.is_archived = 0";
        
        $params = [$userId];
        
        if ($noteIds) {
            $placeholders = str_repeat('?,', count($noteIds) - 1) . '?';
            $sql .= " AND n.id IN ($placeholders)";
            $params = array_merge($params, $noteIds);
        }
        
        $sql .= " GROUP BY n.id ORDER BY n.updated_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format notes
        foreach ($notes as &$note) {
            $note['tags'] = $note['tags'] ? explode(',', $note['tags']) : [];
        }
        
        // Get links if exporting all notes
        $links = [];
        if (!$noteIds) {
            $linkStmt = $pdo->prepare("
                SELECT nl.*, n1.title as source_title, n2.title as target_title
                FROM note_links nl
                JOIN notes n1 ON nl.source_note_id = n1.id
                JOIN notes n2 ON nl.target_note_id = n2.id
                WHERE n1.user_id = ? AND n2.user_id = ?
            ");
            $linkStmt->execute([$userId, $userId]);
            $links = $linkStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        $exportData = [
            'exported_at' => date('Y-m-d H:i:s'),
            'version' => '2.0',
            'notes' => $notes,
            'links' => $links,
            'total_notes' => count($notes),
            'total_links' => count($links)
        ];
        
        switch ($format) {
            case 'json':
                header('Content-Type: application/json');
                header('Content-Disposition: attachment; filename="notes_export_' . date('Y-m-d_H-i-s') . '.json"');
                echo json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                break;
                
            case 'csv':
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="notes_export_' . date('Y-m-d_H-i-s') . '.csv"');
                
                $output = fopen('php://output', 'w');
                
                // CSV Headers
                fputcsv($output, ['ID', 'Title', 'Content', 'Category', 'Tags', 'Created', 'Updated', 'Pinned', 'Favorite']);
                
                foreach ($notes as $note) {
                    fputcsv($output, [
                        $note['id'],
                        $note['title'],
                        strip_tags($note['content']),
                        $note['category_name'] ?? '',
                        implode(', ', $note['tags']),
                        $note['created_at'],
                        $note['updated_at'],
                        $note['is_pinned'] ? 'Yes' : 'No',
                        $note['is_favorite'] ? 'Yes' : 'No'
                    ]);
                }
                
                fclose($output);
                break;
                
            default:
                http_response_code(400);
                echo json_encode(['error' => 'Unsupported format']);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Export failed: ' . $e->getMessage()]);
    }
}

function handleMarkdownExport($pdo, $userId) {
    try {
        $noteIds = isset($_GET['notes']) ? explode(',', $_GET['notes']) : null;
        
        $sql = "SELECT n.*, c.name as category_name, GROUP_CONCAT(nt.tag_name) as tags
                FROM notes n 
                LEFT JOIN note_categories c ON n.category_id = c.id
                LEFT JOIN note_tags nt ON n.id = nt.note_id
                WHERE n.user_id = ? AND n.is_archived = 0";
        
        $params = [$userId];
        
        if ($noteIds) {
            $placeholders = str_repeat('?,', count($noteIds) - 1) . '?';
            $sql .= " AND n.id IN ($placeholders)";
            $params = array_merge($params, $noteIds);
        }
        
        $sql .= " GROUP BY n.id ORDER BY n.updated_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-Type: text/markdown');
        header('Content-Disposition: attachment; filename="notes_export_' . date('Y-m-d_H-i-s') . '.md"');
        
        echo "# Notes Export\n\n";
        echo "Generated on: " . date('Y-m-d H:i:s') . "\n";
        echo "Total notes: " . count($notes) . "\n\n";
        echo "---\n\n";
        
        foreach ($notes as $note) {
            echo "# " . $note['title'] . "\n\n";
            
            // Metadata
            echo "**Created:** " . date('Y-m-d H:i:s', strtotime($note['created_at'])) . "\n";
            echo "**Updated:** " . date('Y-m-d H:i:s', strtotime($note['updated_at'])) . "\n";
            
            if ($note['category_name']) {
                echo "**Category:** " . $note['category_name'] . "\n";
            }
            
            if ($note['tags']) {
                $tags = explode(',', $note['tags']);
                echo "**Tags:** " . implode(', ', array_map(function($tag) { return '#' . trim($tag); }, $tags)) . "\n";
            }
            
            echo "\n";
            
            // Content
            if ($note['content']) {
                // Convert HTML to Markdown if needed
                $content = strip_tags($note['content']);
                echo $content . "\n";
            }
            
            echo "\n---\n\n";
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Markdown export failed: ' . $e->getMessage()]);
    }
}

function handleFullBackup($pdo, $userId) {
    try {
        // Get all user data
        $backup = [
            'created_at' => date('Y-m-d H:i:s'),
            'version' => '2.0',
            'user_id' => $userId
        ];
        
        // Notes
        $stmt = $pdo->prepare("
            SELECT n.*, GROUP_CONCAT(DISTINCT nt.tag_name) as tags
            FROM notes n 
            LEFT JOIN note_tags nt ON n.id = nt.note_id
            WHERE n.user_id = ?
            GROUP BY n.id
            ORDER BY n.created_at
        ");
        $stmt->execute([$userId]);
        $backup['notes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Categories
        $stmt = $pdo->prepare("SELECT * FROM note_categories WHERE user_id = ? ORDER BY sort_order");
        $stmt->execute([$userId]);
        $backup['categories'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Templates
        $stmt = $pdo->prepare("SELECT * FROM note_templates WHERE user_id = ? ORDER BY name");
        $stmt->execute([$userId]);
        $backup['templates'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Links
        $stmt = $pdo->prepare("
            SELECT nl.*
            FROM note_links nl
            JOIN notes n1 ON nl.source_note_id = n1.id
            WHERE n1.user_id = ?
        ");
        $stmt->execute([$userId]);
        $backup['links'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Statistics
        $stmt = $pdo->prepare("SELECT * FROM note_statistics WHERE user_id = ?");
        $stmt->execute([$userId]);
        $backup['statistics'] = $stmt->fetch(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="notes_full_backup_' . date('Y-m-d_H-i-s') . '.json"');
        echo json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Backup failed: ' . $e->getMessage()]);
    }
}

function handleImport($pdo, $userId) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        return;
    }
    
    if (!isset($_FILES['import_file'])) {
        http_response_code(400);
        echo json_encode(['error' => 'No file uploaded']);
        return;
    }
    
    $file = $_FILES['import_file'];
    $format = $_POST['format'] ?? 'json';
    
    try {
        $content = file_get_contents($file['tmp_name']);
        
        switch ($format) {
            case 'json':
                $data = json_decode($content, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception('Invalid JSON format');
                }
                
                $result = importFromJson($pdo, $userId, $data);
                break;
                
            case 'markdown':
                $result = importFromMarkdown($pdo, $userId, $content);
                break;
                
            default:
                throw new Exception('Unsupported import format');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Import successful',
            'imported' => $result
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Import failed: ' . $e->getMessage()]);
    }
}

function importFromJson($pdo, $userId, $data) {
    $imported = ['notes' => 0, 'categories' => 0, 'templates' => 0, 'links' => 0];
    
    $pdo->beginTransaction();
    
    try {
        // Import categories first
        if (isset($data['categories'])) {
            foreach ($data['categories'] as $category) {
                if ($category['is_system']) continue; // Skip system categories
                
                $stmt = $pdo->prepare("
                    INSERT INTO note_categories (user_id, name, description, color, icon, sort_order)
                    VALUES (?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                    description = VALUES(description),
                    color = VALUES(color),
                    icon = VALUES(icon)
                ");
                $stmt->execute([
                    $userId,
                    $category['name'],
                    $category['description'] ?? '',
                    $category['color'] ?? '#6b7280',
                    $category['icon'] ?? 'folder',
                    $category['sort_order'] ?? 0
                ]);
                $imported['categories']++;
            }
        }
        
        // Import templates
        if (isset($data['templates'])) {
            foreach ($data['templates'] as $template) {
                if ($template['is_system']) continue; // Skip system templates
                
                $stmt = $pdo->prepare("
                    INSERT INTO note_templates (user_id, name, description, template_content, template_type)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $userId,
                    $template['name'],
                    $template['description'] ?? '',
                    $template['template_content'],
                    $template['template_type'] ?? 'note'
                ]);
                $imported['templates']++;
            }
        }
        
        // Import notes
        if (isset($data['notes'])) {
            $noteIdMapping = [];
            
            foreach ($data['notes'] as $note) {
                // Find category ID if exists
                $categoryId = null;
                if (!empty($note['category_name'])) {
                    $catStmt = $pdo->prepare("SELECT id FROM note_categories WHERE user_id = ? AND name = ?");
                    $catStmt->execute([$userId, $note['category_name']]);
                    $category = $catStmt->fetch();
                    if ($category) {
                        $categoryId = $category['id'];
                    }
                }
                
                // Insert note
                $stmt = $pdo->prepare("
                    INSERT INTO notes (user_id, title, content, content_type, color, category_id, is_pinned, is_favorite, word_count, reading_time_minutes, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $wordCount = str_word_count(strip_tags($note['content'] ?? ''));
                $readingTime = max(1, round($wordCount / 200));
                
                $stmt->execute([
                    $userId,
                    $note['title'],
                    $note['content'] ?? '',
                    $note['content_type'] ?? 'text',
                    $note['color'] ?? '#fbbf24',
                    $categoryId,
                    $note['is_pinned'] ?? false,
                    $note['is_favorite'] ?? false,
                    $wordCount,
                    $readingTime,
                    $note['created_at'] ?? date('Y-m-d H:i:s')
                ]);
                
                $newNoteId = $pdo->lastInsertId();
                $noteIdMapping[$note['id']] = $newNoteId;
                
                // Import tags
                if (!empty($note['tags'])) {
                    $tagStmt = $pdo->prepare("INSERT IGNORE INTO note_tags (note_id, tag_name) VALUES (?, ?)");
                    foreach ($note['tags'] as $tag) {
                        $tagStmt->execute([$newNoteId, trim($tag)]);
                    }
                }
                
                $imported['notes']++;
            }
            
            // Import links with mapped IDs
            if (isset($data['links']) && !empty($noteIdMapping)) {
                foreach ($data['links'] as $link) {
                    $sourceId = $noteIdMapping[$link['source_note_id']] ?? null;
                    $targetId = $noteIdMapping[$link['target_note_id']] ?? null;
                    
                    if ($sourceId && $targetId) {
                        $stmt = $pdo->prepare("
                            INSERT IGNORE INTO note_links (source_note_id, target_note_id, link_type, link_text)
                            VALUES (?, ?, ?, ?)
                        ");
                        $stmt->execute([
                            $sourceId,
                            $targetId,
                            $link['link_type'] ?? 'reference',
                            $link['link_text'] ?? null
                        ]);
                        $imported['links']++;
                    }
                }
            }
        }
        
        $pdo->commit();
        
        // Update statistics
        updateUserStatistics($pdo, $userId);
        
        return $imported;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function importFromMarkdown($pdo, $userId, $content) {
    $imported = ['notes' => 0];
    
    // Simple markdown parser for note import
    $sections = preg_split('/^# /m', $content);
    array_shift($sections); // Remove first empty section
    
    $pdo->beginTransaction();
    
    try {
        foreach ($sections as $section) {
            $lines = explode("\n", trim($section));
            if (empty($lines[0])) continue;
            
            $title = trim($lines[0]);
            $content = '';
            $tags = [];
            $categoryName = null;
            
            $contentStarted = false;
            
            for ($i = 1; $i < count($lines); $i++) {
                $line = trim($lines[$i]);
                
                if ($line === '---') {
                    break; // End of note
                }
                
                if (!$contentStarted && preg_match('/^\*\*(.*?):\*\*\s*(.*)$/', $line, $matches)) {
                    $key = strtolower($matches[1]);
                    $value = $matches[2];
                    
                    if ($key === 'tags') {
                        $tags = array_map('trim', explode(',', str_replace('#', '', $value)));
                    } elseif ($key === 'category') {
                        $categoryName = $value;
                    }
                } else {
                    if (!empty($line) || $contentStarted) {
                        $contentStarted = true;
                        $content .= $line . "\n";
                    }
                }
            }
            
            $content = trim($content);
            
            // Find or create category
            $categoryId = null;
            if ($categoryName) {
                $catStmt = $pdo->prepare("SELECT id FROM note_categories WHERE user_id = ? AND name = ?");
                $catStmt->execute([$userId, $categoryName]);
                $category = $catStmt->fetch();
                
                if (!$category) {
                    $catInsert = $pdo->prepare("INSERT INTO note_categories (user_id, name, color) VALUES (?, ?, ?)");
                    $catInsert->execute([$userId, $categoryName, '#6b7280']);
                    $categoryId = $pdo->lastInsertId();
                } else {
                    $categoryId = $category['id'];
                }
            }
            
            // Insert note
            $wordCount = str_word_count(strip_tags($content));
            $readingTime = max(1, round($wordCount / 200));
            
            $stmt = $pdo->prepare("
                INSERT INTO notes (user_id, title, content, category_id, word_count, reading_time_minutes)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$userId, $title, $content, $categoryId, $wordCount, $readingTime]);
            
            $noteId = $pdo->lastInsertId();
            
            // Add tags
            if (!empty($tags)) {
                $tagStmt = $pdo->prepare("INSERT IGNORE INTO note_tags (note_id, tag_name) VALUES (?, ?)");
                foreach ($tags as $tag) {
                    if (!empty($tag)) {
                        $tagStmt->execute([$noteId, $tag]);
                    }
                }
            }
            
            $imported['notes']++;
        }
        
        $pdo->commit();
        
        return $imported;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

// Helper function (should be in a shared file)
function updateUserStatistics($pdo, $userId) {
    // This function should match the one in enhanced_notes.php
    // For now, we'll just update the note count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notes WHERE user_id = ? AND is_archived = 0");
    $stmt->execute([$userId]);
    $notesCount = $stmt->fetchColumn();
    
    $pdo->prepare("
        INSERT INTO note_statistics (user_id, total_notes, last_updated)
        VALUES (?, ?, NOW())
        ON DUPLICATE KEY UPDATE
        total_notes = VALUES(total_notes),
        last_updated = NOW()
    ")->execute([$userId, $notesCount]);
}
?>
