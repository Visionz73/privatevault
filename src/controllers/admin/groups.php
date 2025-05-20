<?php
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/auth.php';

requireLogin();
requireRole(['admin']);

$success = '';
$errors = [];

// Get all users for member selection
$stmt = $pdo->query('SELECT id, username, email FROM users ORDER BY username');
$allUsers = $stmt->fetchAll();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
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
    }
}

// Load all groups with members
$groups = [];
$stmt = $pdo->query('SELECT * FROM user_groups ORDER BY name');
$groupsData = $stmt->fetchAll();

foreach ($groupsData as $group) {
    $stmt = $pdo->prepare('
        SELECT u.id, u.username, u.email 
        FROM users u
        JOIN user_group_members gm ON u.id = gm.user_id
        WHERE gm.group_id = ?
        ORDER BY u.username
    ');
    $stmt->execute([$group['id']]);
    $members = $stmt->fetchAll();
    
    $groups[] = [
        'id' => $group['id'],
        'name' => $group['name'],
        'description' => $group['description'],
        'created_at' => $group['created_at'],
        'members' => $members,
        'member_count' => count($members)
    ];
}

// Include the template
require_once __DIR__ . '/../../../templates/admin/groups.php';
```
