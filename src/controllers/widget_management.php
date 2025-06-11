<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/db.php';

requireLogin();
$userId = $_SESSION['user_id'];

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'update_widget_position':
            $slotPosition = (int)($_POST['slot_position'] ?? 0);
            $widgetType = $_POST['widget_type'] ?? '';
            
            if ($slotPosition < 1 || $slotPosition > 16 || empty($widgetType)) {
                throw new Exception('Invalid parameters');
            }
            
            // Check if widget type is valid
            $validWidgets = ['inbox', 'documents', 'calendar', 'havetopay', 'placeholder'];
            if (!in_array($widgetType, $validWidgets)) {
                throw new Exception('Invalid widget type');
            }
            
            // Update or insert widget position
            $stmt = $pdo->prepare("
                INSERT INTO user_dashboard_widgets (user_id, slot_position, widget_type) 
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                widget_type = VALUES(widget_type),
                updated_at = CURRENT_TIMESTAMP
            ");
            $stmt->execute([$userId, $slotPosition, $widgetType]);
            
            echo json_encode(['success' => true]);
            break;
            
        case 'remove_widget':
            $slotPosition = (int)($_POST['slot_position'] ?? 0);
            
            if ($slotPosition < 1 || $slotPosition > 16) {
                throw new Exception('Invalid slot position');
            }
            
            $stmt = $pdo->prepare("
                UPDATE user_dashboard_widgets 
                SET widget_type = 'placeholder', updated_at = CURRENT_TIMESTAMP
                WHERE user_id = ? AND slot_position = ?
            ");
            $stmt->execute([$userId, $slotPosition]);
            
            echo json_encode(['success' => true]);
            break;
            
        case 'get_widget_config':
            $stmt = $pdo->prepare("
                SELECT slot_position, widget_type, widget_config 
                FROM user_dashboard_widgets 
                WHERE user_id = ? AND is_active = 1
                ORDER BY slot_position
            ");
            $stmt->execute([$userId]);
            $config = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Fill empty slots with placeholders
            $fullConfig = [];
            for ($i = 1; $i <= 16; $i++) {
                $found = false;
                foreach ($config as $widget) {
                    if ($widget['slot_position'] == $i) {
                        $fullConfig[$i] = $widget;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $fullConfig[$i] = [
                        'slot_position' => $i,
                        'widget_type' => 'placeholder',
                        'widget_config' => null
                    ];
                }
            }
            
            echo json_encode(['success' => true, 'config' => array_values($fullConfig)]);
            break;
            
        case 'reset_dashboard':
            // Reset to default layout
            $stmt = $pdo->prepare("DELETE FROM user_dashboard_widgets WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            $defaultWidgets = [
                1 => 'inbox',
                2 => 'documents',
                3 => 'calendar',
                4 => 'havetopay'
            ];
            
            foreach ($defaultWidgets as $position => $type) {
                $stmt = $pdo->prepare("
                    INSERT INTO user_dashboard_widgets (user_id, slot_position, widget_type) 
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$userId, $position, $type]);
            }
            
            echo json_encode(['success' => true]);
            break;
            
        default:
            throw new Exception('Unknown action');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
