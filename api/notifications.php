<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in output, but log them

// Set CORS headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    require_once __DIR__ . '/../src/lib/db.php';
    require_once __DIR__ . '/../src/lib/auth.php';
    
    // Check if user is logged in
    if (!isset($_SESSION)) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Not logged in']);
        exit;
    }
    
    $userId = $_SESSION['user_id'];
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['action']) && $_GET['action'] === 'unread_count') {
                // Get unread notification count
                try {
                    $stmt = $pdo->prepare("
                        SELECT COUNT(*) as count 
                        FROM notifications 
                        WHERE user_id = ? AND read_at IS NULL
                    ");
                    $stmt->execute([$userId]);
                    $result = $stmt->fetch();
                    
                    echo json_encode(['success' => true, 'count' => (int)$result['count']]);
                } catch (Exception $e) {
                    // If notifications table doesn't exist, return 0
                    echo json_encode(['success' => true, 'count' => 0]);
                }
            } elseif (isset($_GET['since'])) {
                // Get new notifications since timestamp
                $since = $_GET['since'] ?: '1970-01-01 00:00:00';
                try {
                    $stmt = $pdo->prepare("
                        SELECT id, title, message, type, data, created_at 
                        FROM notifications 
                        WHERE user_id = ? AND created_at > ? AND read_at IS NULL
                        ORDER BY created_at DESC
                    ");
                    $stmt->execute([$userId, $since]);
                    $notifications = $stmt->fetchAll();
                    
                    echo json_encode(['success' => true, 'notifications' => $notifications]);
                } catch (Exception $e) {
                    echo json_encode(['success' => true, 'notifications' => []]);
                }
            } else {
                // Get user's notification settings
                try {
                    $stmt = $pdo->prepare("SELECT notification_settings FROM users WHERE id = ?");
                    $stmt->execute([$userId]);
                    $user = $stmt->fetch();
                    
                    $settings = $user && $user['notification_settings'] ? json_decode($user['notification_settings'], true) : [];
                } catch (Exception $e) {
                    $settings = [];
                }
                
                // Default settings if none exist
                $defaultSettings = [
                    'desktop_enabled' => true,
                    'sound_enabled' => true,
                    'email_enabled' => true,
                    'push_enabled' => true,
                    'task_reminders' => true,
                    'calendar_events' => true,
                    'note_reminders' => false,
                    'system_alerts' => true,
                    'finance_updates' => true,
                    'document_uploads' => true,
                    'security_warnings' => true,
                    'quiet_start' => '22:00',
                    'quiet_end' => '07:00',
                    'frequency' => 'immediate'
                ];
                
                $settings = array_merge($defaultSettings, $settings);
                
                echo json_encode(['success' => true, 'settings' => $settings]);
            }
            break;
            
        case 'POST':
            // Update notification settings
            if (!$input) {
                echo json_encode(['success' => false, 'error' => 'Invalid input data']);
                break;
            }
            
            // Validate and sanitize settings
            $allowedSettings = [
                'desktop_enabled', 'sound_enabled', 'email_enabled', 'push_enabled',
                'task_reminders', 'calendar_events', 'note_reminders', 'system_alerts',
                'finance_updates', 'document_uploads', 'security_warnings',
                'quiet_start', 'quiet_end', 'frequency'
            ];
            
            $settings = [];
            foreach ($allowedSettings as $setting) {
                if (isset($input[$setting])) {
                    $settings[$setting] = $input[$setting];
                }
            }
            
            try {
                // Update user settings
                $stmt = $pdo->prepare("UPDATE users SET notification_settings = ? WHERE id = ?");
                $stmt->execute([json_encode($settings), $userId]);
                
                echo json_encode(['success' => true, 'message' => 'Einstellungen gespeichert']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => 'Failed to save settings: ' . $e->getMessage()]);
            }
            break;
            
        case 'PUT':
            // Send a test notification
            $title = $input['title'] ?? 'Test-Benachrichtigung';
            $message = $input['message'] ?? 'Dies ist eine Test-Benachrichtigung von PrivateVault.';
            $type = $input['type'] ?? 'info';
            
            try {
                // Store notification in database for history
                $stmt = $pdo->prepare("
                    INSERT INTO notifications (user_id, title, message, type, created_at) 
                    VALUES (?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$userId, $title, $message, $type]);
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Test-Benachrichtigung gesendet',
                    'notification' => [
                        'title' => $title,
                        'message' => $message,
                        'type' => $type,
                        'timestamp' => date('Y-m-d H:i:s')
                    ]
                ]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => 'Failed to send notification: ' . $e->getMessage()]);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
?>
