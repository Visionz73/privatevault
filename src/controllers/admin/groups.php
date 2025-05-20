<?php
// Admin groups controller
require_once __DIR__ . '/../../lib/auth.php';
require_once __DIR__ . '/../../lib/db.php';

// Create necessary tables
require_once __DIR__ . '/../../../database/group_tags_table.php';

// Authentication check
requireLogin();
requireRole(['admin']);

$success = '';
$errors = [];

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        // Existing group actions
        case 'create_group':
            $name = trim($_POST['group_name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $selectedUsers = $_POST['members'] ?? [];
            
            if (empty($name)) {
                $errors[] = 'Gruppenname ist erforderlich.';
            } else {
                $pdo->beginTransaction();
                try {
                    // Create the group
                    $stmt = $pdo->prepare('INSERT INTO user_groups (name, description, created_by) VALUES (?, ?, ?)');
                    $stmt->execute([$name, $description, $_SESSION['user_id']]);
                    $groupId = $pdo->lastInsertId();
                    
                    // Add selected members
                    if (!empty($selectedUsers)) {
                        $insertMembers = $pdo->prepare('INSERT INTO user_group_members (group_id, user_id, added_by) VALUES (?, ?, ?)');
                        foreach ($selectedUsers as $userId) {
                            $insertMembers->execute([$groupId, $userId, $_SESSION['user_id']]);
                        }
                    }
                    
                    $pdo->commit();
                    $success = 'Gruppe erfolgreich erstellt.';
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $errors[] = 'Fehler beim Erstellen der Gruppe: ' . $e->getMessage();
                }
            }
            break;
            
        case 'update_group':
            $groupId = (int)($_POST['group_id'] ?? 0);
            $name = trim($_POST['group_name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $selectedUsers = $_POST['members'] ?? [];
            
            if (empty($name) || $groupId === 0) {
                $errors[] = 'Ungültige Gruppeninformationen.';
            } else {
                $pdo->beginTransaction();
                try {
                    // Update group details
                    $stmt = $pdo->prepare('UPDATE user_groups SET name = ?, description = ? WHERE id = ?');
                    $stmt->execute([$name, $description, $groupId]);
                    
                    // Remove all existing members
                    $stmt = $pdo->prepare('DELETE FROM user_group_members WHERE group_id = ?');
                    $stmt->execute([$groupId]);
                    
                    // Add selected members
                    if (!empty($selectedUsers)) {
                        $insertMembers = $pdo->prepare('INSERT INTO user_group_members (group_id, user_id, added_by) VALUES (?, ?, ?)');
                        foreach ($selectedUsers as $userId) {
                            $insertMembers->execute([$groupId, $userId, $_SESSION['user_id']]);
                        }
                    }
                    
                    $pdo->commit();
                    $success = 'Gruppe erfolgreich aktualisiert.';
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $errors[] = 'Fehler beim Aktualisieren der Gruppe: ' . $e->getMessage();
                }
            }
            break;
            
        case 'delete_group':
            $groupId = (int)($_POST['group_id'] ?? 0);
            
            if ($groupId === 0) {
                $errors[] = 'Ungültige Gruppe.';
            } else {
                $stmt = $pdo->prepare('DELETE FROM user_groups WHERE id = ?');
                $stmt->execute([$groupId]);
                $success = 'Gruppe erfolgreich gelöscht.';
            }
            break;
        
        // New tag actions
        case 'create_tag':
            $tagName = trim($_POST['tag_name'] ?? '');
            $tagColor = $_POST['tag_color'] ?? '#4A90E2';
            
            if (empty($tagName)) {
                $errors[] = 'Tag-Name ist erforderlich.';
            } elseif (strlen($tagName) > 50) {
                $errors[] = 'Tag-Name darf maximal 50 Zeichen lang sein.';
            }
            
            if (empty($errors)) {
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO group_tags (name, color, created_by)
                        VALUES (?, ?, ?)
                    ");
                    $stmt->execute([$tagName, $tagColor, $_SESSION['user_id']]);
                    $success = 'Tag erfolgreich erstellt.';
                } catch (PDOException $e) {
                    $errors[] = 'Datenbankfehler: ' . $e->getMessage();
                }
            }
            break;
            
        case 'update_tag':
            $tagId = $_POST['tag_id'] ?? 0;
            $tagName = trim($_POST['tag_name'] ?? '');
            $tagColor = $_POST['tag_color'] ?? '#4A90E2';
            
            if (empty($tagId) || !is_numeric($tagId)) {
                $errors[] = 'Ungültige Tag-ID.';
            }
            if (empty($tagName)) {
                $errors[] = 'Tag-Name ist erforderlich.';
            }
            
            if (empty($errors)) {
                try {
                    $stmt = $pdo->prepare("
                        UPDATE group_tags
                        SET name = ?, color = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$tagName, $tagColor, $tagId]);
                    $success = 'Tag erfolgreich aktualisiert.';
                } catch (PDOException $e) {
                    $errors[] = 'Datenbankfehler: ' . $e->getMessage();
                }
            }
            break;
            
        case 'delete_tag':
            $tagId = $_POST['tag_id'] ?? 0;
            
            if (empty($tagId) || !is_numeric($tagId)) {
                $errors[] = 'Ungültige Tag-ID.';
            }
            
            if (empty($errors)) {
                try {
                    $stmt = $pdo->prepare("DELETE FROM group_tags WHERE id = ?");
                    $stmt->execute([$tagId]);
                    $success = 'Tag erfolgreich gelöscht.';
                } catch (PDOException $e) {
                    $errors[] = 'Datenbankfehler: ' . $e->getMessage();
                }
            }
            break;
            
        case 'assign_tags':
            $groupId = $_POST['group_id'] ?? 0;
            $tagIds = $_POST['tag_ids'] ?? [];
            
            if (empty($groupId) || !is_numeric($groupId)) {
                $errors[] = 'Ungültige Gruppen-ID.';
            }
            
            if (empty($errors)) {
                try {
                    // Begin transaction
                    $pdo->beginTransaction();
                    
                    // Remove existing tag assignments for this group
                    $stmt = $pdo->prepare("DELETE FROM group_tag_assignments WHERE group_id = ?");
                    $stmt->execute([$groupId]);
                    
                    // Add new tag assignments
                    if (!empty($tagIds)) {
                        $stmt = $pdo->prepare("
                            INSERT INTO group_tag_assignments (group_id, tag_id, assigned_by)
                            VALUES (?, ?, ?)
                        ");
                        
                        foreach ($tagIds as $tagId) {
                            $stmt->execute([$groupId, $tagId, $_SESSION['user_id']]);
                        }
                    }
                    
                    $pdo->commit();
                    $success = 'Tags erfolgreich zugewiesen.';
                } catch (PDOException $e) {
                    $pdo->rollBack();
                    $errors[] = 'Datenbankfehler: ' . $e->getMessage();
                }
            }
            break;
    }
}

// Fetch all groups with members count
try {
    $stmt = $pdo->query("
        SELECT g.id, g.name, g.description, COUNT(DISTINCT m.user_id) as member_count
        FROM user_groups g
        LEFT JOIN user_group_members m ON g.id = m.group_id
        GROUP BY g.id
        ORDER BY g.name
    ");
    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch members for each group
    foreach ($groups as &$group) {
        $stmt = $pdo->prepare("
            SELECT u.id, u.username
            FROM user_group_members m
            JOIN users u ON m.user_id = u.id
            WHERE m.group_id = ?
            ORDER BY u.username
        ");
        $stmt->execute([$group['id']]);
        $group['members'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Fetch tags assigned to this group
        $stmt = $pdo->prepare("
            SELECT t.id, t.name, t.color
            FROM group_tag_assignments a
            JOIN group_tags t ON a.tag_id = t.id
            WHERE a.group_id = ?
            ORDER BY t.name
        ");
        $stmt->execute([$group['id']]);
        $group['tags'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    unset($group); // Break the reference
} catch (PDOException $e) {
    $errors[] = 'Fehler beim Laden der Gruppen: ' . $e->getMessage();
    $groups = [];
}

// Fetch all users for select dropdowns
try {
    $stmt = $pdo->query("SELECT id, username, email FROM users ORDER BY username");
    $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = 'Fehler beim Laden der Benutzer: ' . $e->getMessage();
    $allUsers = [];
}

// Fetch all tags
try {
    $stmt = $pdo->query("SELECT id, name, color FROM group_tags ORDER BY name");
    $allTags = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = 'Fehler beim Laden der Tags: ' . $e->getMessage();
    $allTags = [];
}

// Include the template
require_once __DIR__ . '/../../../templates/admin/groups.php';
