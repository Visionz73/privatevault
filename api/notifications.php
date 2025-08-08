<?php
// notifications.php - Enhanced Notifications API
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

function respond($data, int $status = 200) {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    require_once __DIR__ . '/../src/lib/db.php';
    require_once __DIR__ . '/../src/lib/auth.php';
    require_once __DIR__ . '/../src/lib/NotificationManager.php';
    if (!isset($_SESSION)) session_start();
    if (empty($_SESSION['user_id'])) respond(['success' => false, 'error' => 'Not logged in'], 401);
    $userId = (int)$_SESSION['user_id'];
    $notificationManager = new NotificationManager($pdo);
} catch (Throwable $e) {
    respond(['success' => false, 'error' => 'Bootstrap error: '.$e->getMessage()], 500);
}

$method = $_SERVER['REQUEST_METHOD'];
$inputRaw = file_get_contents('php://input');
$input = $inputRaw !== '' ? json_decode($inputRaw, true) : [];
if ($inputRaw !== '' && $input === null && json_last_error() !== JSON_ERROR_NONE) {
    respond(['success' => false, 'error' => 'Invalid JSON body'], 400);
}

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
$allowedSettings = array_keys($defaultSettings);

try {
    switch ($method) {
        case 'GET': {
            $action = $_GET['action'] ?? null;
            // Backwards compatibility: ?since=... without action
            if (!$action && isset($_GET['since'])) $action = 'new';
            if ($action === 'unread_count') {
                $count = $notificationManager->getUnreadCount($userId);
                respond(['success' => true, 'count' => (int)$count]);
            } elseif ($action === 'new') {
                $since = $_GET['since'] ?? '1970-01-01 00:00:00';
                try {
                    $stmt = $pdo->prepare("SELECT id, title, message, type, data, created_at FROM notifications WHERE user_id = ? AND created_at > ? AND read_at IS NULL ORDER BY created_at DESC");
                    $stmt->execute([$userId, $since]);
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
                } catch (Throwable $e) { $rows = []; }
                respond(['success' => true, 'notifications' => $rows]);
            } elseif ($action === 'list') {
                $limit = max(1, min(100, (int)($_GET['limit'] ?? 20)));
                $offset = max(0, (int)($_GET['offset'] ?? 0));
                $unreadOnly = isset($_GET['unread']) && ($_GET['unread'] === '1' || $_GET['unread'] === 'true');
                $type = isset($_GET['type']) ? trim($_GET['type']) : null;
                $sql = "SELECT id, title, message, type, data, created_at, read_at FROM notifications WHERE user_id = ?";
                $params = [$userId];
                if ($unreadOnly) { $sql .= " AND read_at IS NULL"; }
                if ($type) { $sql .= " AND type = ?"; $params[] = $type; }
                $sqlCount = "SELECT COUNT(*) as total FROM (".$sql.") c";
                $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
                $paramsFull = array_merge($params, [$limit, $offset]);
                try {
                    $stmt = $pdo->prepare($sqlCount);
                    $stmt->execute($params);
                    $total = (int)$stmt->fetchColumn();
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($paramsFull);
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
                } catch (Throwable $e) { $rows = []; $total = 0; }
                respond(['success' => true, 'total' => $total, 'limit' => $limit, 'offset' => $offset, 'notifications' => $rows]);
            } else { // settings
                try {
                    $stmt = $pdo->prepare("SELECT notification_settings FROM users WHERE id = ?");
                    $stmt->execute([$userId]);
                    $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
                    $settings = $userRow && $userRow['notification_settings'] ? json_decode($userRow['notification_settings'], true) : [];
                    if (!is_array($settings)) $settings = [];
                } catch (Throwable $e) { $settings = []; }
                $settings = array_merge($defaultSettings, $settings);
                respond(['success' => true, 'settings' => $settings]);
            }
            break;
        }
        case 'POST': {
            $action = $input['action'] ?? ($_GET['action'] ?? null);
            if ($action === 'mark_read') {
                $ids = $input['ids'] ?? [];
                if (!is_array($ids) || empty($ids)) respond(['success' => false, 'error' => 'ids required'], 400);
                $idsClean = array_values(array_filter(array_map('intval', $ids), fn($v)=>$v>0));
                if (!$idsClean) respond(['success' => false, 'error' => 'No valid ids'], 400);
                $ok = $notificationManager->markAsRead($idsClean, $userId);
                respond(['success' => (bool)$ok, 'marked' => count($idsClean)]);
            } elseif ($action === 'mark_all_read') {
                try {
                    $stmt = $pdo->prepare("UPDATE notifications SET read_at = NOW() WHERE user_id = ? AND read_at IS NULL");
                    $stmt->execute([$userId]);
                    respond(['success' => true, 'marked' => $stmt->rowCount()]);
                } catch (Throwable $e) {
                    respond(['success' => false, 'error' => 'Failed to mark all read']);
                }
            } else { // save settings (default)
                if (!$input) respond(['success' => false, 'error' => 'No settings provided'], 400);
                $settingsIn = [];
                foreach ($allowedSettings as $key) {
                    if (array_key_exists($key, $input)) {
                        $val = $input[$key];
                        if (in_array($key, ['desktop_enabled','sound_enabled','email_enabled','push_enabled','task_reminders','calendar_events','note_reminders','system_alerts','finance_updates','document_uploads','security_warnings'], true)) {
                            $settingsIn[$key] = (bool)$val; 
                        } elseif (in_array($key, ['quiet_start','quiet_end','frequency'], true)) {
                            $settingsIn[$key] = substr((string)$val,0,10);
                        }
                    }
                }
                $settingsFinal = array_merge($defaultSettings, $settingsIn);
                try {
                    $stmt = $pdo->prepare("UPDATE users SET notification_settings = ? WHERE id = ?");
                    $stmt->execute([json_encode($settingsFinal, JSON_UNESCAPED_UNICODE), $userId]);
                    respond(['success' => true, 'message' => 'Settings saved', 'settings' => $settingsFinal]);
                } catch (Throwable $e) {
                    respond(['success' => false, 'error' => 'Save failed']);
                }
            }
            break;
        }
        case 'PUT': {
            $title = trim($input['title'] ?? 'Test-Benachrichtigung');
            $message = trim($input['message'] ?? 'Dies ist eine Test-Benachrichtigung von PrivateVault.');
            $type = preg_replace('/[^a-z0-9_\-]/i','', $input['type'] ?? 'info');
            $data = isset($input['data']) && is_array($input['data']) ? $input['data'] : null;
            $id = $notificationManager->sendNotification($userId, $title, $message, $type, $data);
            if ($id === false) respond(['success' => false, 'error' => 'Insert failed'], 500);
            respond(['success' => true, 'notification' => [
                'id' => (int)$id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ]]);
            break;
        }
        default:
            respond(['success' => false, 'error' => 'Method not allowed'], 405);
    }
} catch (Throwable $e) {
    respond(['success' => false, 'error' => 'Server error: '.$e->getMessage()], 500);
}
?>
