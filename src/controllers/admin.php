<?php
// Error logging
ini_set('display_errors', 1);
error_reporting(E_ALL);
error_log("Admin controller loaded", 0);

// src/controllers/admin.php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';

try {
    requireLogin();
    requireRole(['admin']);

    $success = '';
    $errors = [];
    $action = $_POST['action'] ?? '';

    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        
        // Validate user exists (except for new user creation)
        if ($user_id > 0 && $action !== 'create_user') {
            $check = $pdo->prepare('SELECT COUNT(*) FROM users WHERE id = ?');
            $check->execute([$user_id]);
            if ($check->fetchColumn() == 0) {
                $errors[] = 'Benutzer nicht gefunden.';
            }
        }
        
        // Skip current user for certain actions
        if ($user_id === (int)$_SESSION['user_id'] && in_array($action, ['delete_user'])) {
            $errors[] = 'Sie können Ihren eigenen Account nicht löschen.';
        }
        
        if (empty($errors)) {
            switch ($action) {
                case 'update_role':
                    $role = $_POST['role'] ?? '';
                    if (in_array($role, ['admin', 'member', 'guest'], true)) {
                        $stmt = $pdo->prepare('UPDATE users SET role = ?, updated_at = NOW() WHERE id = ?');
                        $stmt->execute([$role, $user_id]);
                        $success = 'Rolle erfolgreich aktualisiert.';
                    } else {
                        $errors[] = 'Ungültige Rolle.';
                    }
                    break;
                    
                case 'delete_user':
                    // Begin transaction for atomicity
                    $pdo->beginTransaction();
                    try {
                        // Delete user's events first
                        $stmt = $pdo->prepare('DELETE FROM events WHERE created_by = ?');
                        $stmt->execute([$user_id]);
                        
                        // Update events where user is assigned to remove assignment
                        $stmt = $pdo->prepare('UPDATE events SET assigned_to = NULL WHERE assigned_to = ?');
                        $stmt->execute([$user_id]);
                        
                        // Delete user's tasks
                        $stmt = $pdo->prepare('DELETE FROM tasks WHERE created_by = ? OR assigned_to = ?');
                        $stmt->execute([$user_id, $user_id]);
                        
                        // Delete user's documents
                        $stmt = $pdo->prepare('UPDATE documents SET is_deleted = 1 WHERE user_id = ?');
                        $stmt->execute([$user_id]);
                        
                        // Delete user from group memberships
                        $stmt = $pdo->prepare('DELETE FROM user_group_members WHERE user_id = ?');
                        $stmt->execute([$user_id]);
                        
                        // Delete user's finance entries if they exist
                        try {
                            $stmt = $pdo->prepare('DELETE FROM finance_entries WHERE user_id = ?');
                            $stmt->execute([$user_id]);
                        } catch (PDOException $e) {
                            // Table might not exist, ignore
                        }
                        
                        // Delete user's expense participations if they exist
                        try {
                            $stmt = $pdo->prepare('DELETE FROM expense_participants WHERE user_id = ?');
                            $stmt->execute([$user_id]);
                        } catch (PDOException $e) {
                            // Table might not exist, ignore
                        }
                        
                        // Update expenses where user is the payer to remove payer assignment
                        try {
                            $stmt = $pdo->prepare('DELETE FROM expenses WHERE payer_id = ?');
                            $stmt->execute([$user_id]);
                        } catch (PDOException $e) {
                            // Table might not exist, ignore
                        }
                        
                        // Delete the user
                        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
                        $stmt->execute([$user_id]);
                        
                        $pdo->commit();
                        $success = 'Benutzer und zugehörige Daten wurden erfolgreich gelöscht.';
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        $errors[] = 'Fehler beim Löschen des Benutzers: ' . $e->getMessage();
                    }
                    break;
                    
                // You could add more actions here
            }
        }
    }

    // Load statistics for dashboard
    $stats = [
        'users' => $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
        'tasks' => $pdo->query('SELECT COUNT(*) FROM tasks')->fetchColumn(),
        'documents' => $pdo->query('SELECT COUNT(*) FROM documents WHERE is_deleted = 0')->fetchColumn()
    ];

    // Get task distribution by status
    $tasksByStatus = $pdo->query('
        SELECT status, COUNT(*) as count 
        FROM tasks 
        GROUP BY status
    ')->fetchAll(PDO::FETCH_KEY_PAIR);

    // Load all users for management
    $stmt = $pdo->query('
        SELECT u.id, u.username, u.email, u.role, u.created_at,
               (SELECT COUNT(*) FROM tasks WHERE created_by = u.id OR assigned_to = u.id) AS task_count,
               (SELECT COUNT(*) FROM documents WHERE user_id = u.id AND is_deleted = 0) AS doc_count
        FROM users u
        ORDER BY u.id
    ');
    $users = $stmt->fetchAll();

    // Template rendern
    require_once __DIR__ . '/../../templates/admin.php';
} catch (Exception $e) {
    error_log("Admin error: " . $e->getMessage());
    echo "<div style='color:red; padding:20px; background:#ffeeee; border:1px solid red; margin:20px;'>";
    echo "Admin error: " . $e->getMessage();
    echo "</div>";
}
