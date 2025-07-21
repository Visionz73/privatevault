<?php
/**
 * Notification Manager - Helper class for sending notifications
 */

class NotificationManager {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Send a notification to a user
     */
    public function sendNotification($userId, $title, $message, $type = 'info', $data = null, $scheduledFor = null) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO notifications (user_id, title, message, type, data, scheduled_for, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $dataJson = $data ? json_encode($data) : null;
            
            $result = $stmt->execute([
                $userId, 
                $title, 
                $message, 
                $type, 
                $dataJson, 
                $scheduledFor
            ]);
            
            if ($result) {
                $notificationId = $this->pdo->lastInsertId();
                
                // If it's an immediate notification, also check if we should send email
                if (!$scheduledFor) {
                    $this->checkEmailNotification($userId, $title, $message, $type);
                }
                
                return $notificationId;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Failed to send notification: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send notification to multiple users
     */
    public function sendBulkNotification($userIds, $title, $message, $type = 'info', $data = null) {
        $sent = 0;
        foreach ($userIds as $userId) {
            if ($this->sendNotification($userId, $title, $message, $type, $data)) {
                $sent++;
            }
        }
        return $sent;
    }
    
    /**
     * Check if user should receive email notification
     */
    private function checkEmailNotification($userId, $title, $message, $type) {
        try {
            $stmt = $this->pdo->prepare("SELECT email, notification_settings FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if (!$user) return false;
            
            $settings = $user['notification_settings'] ? json_decode($user['notification_settings'], true) : [];
            
            // Check if email notifications are enabled
            $emailEnabled = $settings['email_enabled'] ?? true;
            
            // Check specific type settings
            $typeEnabled = false;
            switch ($type) {
                case 'task_reminder':
                    $typeEnabled = $settings['task_reminders'] ?? true;
                    break;
                case 'calendar_event':
                    $typeEnabled = $settings['calendar_events'] ?? true;
                    break;
                case 'finance_update':
                    $typeEnabled = $settings['finance_updates'] ?? true;
                    break;
                case 'security_warning':
                    $typeEnabled = $settings['security_warnings'] ?? true;
                    break;
                default:
                    $typeEnabled = $settings['system_alerts'] ?? true;
            }
            
            if ($emailEnabled && $typeEnabled) {
                $this->sendEmailNotification($user['email'], $title, $message, $type);
            }
            
        } catch (Exception $e) {
            error_log("Failed to check email notification: " . $e->getMessage());
        }
    }
    
    /**
     * Send email notification
     */
    private function sendEmailNotification($email, $title, $message, $type) {
        // Simple email implementation
        // In production, you might want to use a proper email service
        
        $subject = "PrivateVault: " . $title;
        $headers = [
            'From' => 'noreply@privatevault.local',
            'Reply-To' => 'noreply@privatevault.local',
            'Content-Type' => 'text/html; charset=UTF-8',
            'X-Mailer' => 'PHP/' . phpversion()
        ];
        
        $htmlMessage = $this->generateEmailTemplate($title, $message, $type);
        
        // In development, log instead of sending
        if (defined('DEVELOPMENT') && DEVELOPMENT) {
            error_log("Email notification: To: $email, Subject: $subject, Message: $message");
        } else {
            mail($email, $subject, $htmlMessage, implode("\r\n", array_map(
                function($k, $v) { return "$k: $v"; },
                array_keys($headers),
                $headers
            )));
        }
    }
    
    /**
     * Generate HTML email template
     */
    private function generateEmailTemplate($title, $message, $type) {
        $color = '#3B82F6';
        switch ($type) {
            case 'success':
                $color = '#10B981';
                break;
            case 'warning':
                $color = '#F59E0B';
                break;
            case 'error':
            case 'security_warning':
                $color = '#EF4444';
                break;
        }
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>$title</title>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <div style='background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>
                <h1 style='color: white; margin: 0; font-size: 24px;'>PrivateVault</h1>
            </div>
            
            <div style='background: #f8f9fa; padding: 20px; border-radius: 10px; border-left: 4px solid $color;'>
                <h2 style='color: $color; margin-top: 0;'>$title</h2>
                <p style='margin-bottom: 0; font-size: 16px;'>$message</p>
            </div>
            
            <div style='margin-top: 20px; padding: 15px; background: #e9ecef; border-radius: 5px; font-size: 14px; color: #6c757d;'>
                <p style='margin: 0;'>Diese E-Mail wurde automatisch von PrivateVault gesendet. Bitte antworten Sie nicht auf diese E-Mail.</p>
                <p style='margin: 5px 0 0 0;'>Zeit: " . date('d.m.Y H:i:s') . "</p>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Mark notifications as read
     */
    public function markAsRead($notificationIds, $userId) {
        try {
            $placeholders = str_repeat('?,', count($notificationIds) - 1) . '?';
            $stmt = $this->pdo->prepare("
                UPDATE notifications 
                SET read_at = NOW() 
                WHERE id IN ($placeholders) AND user_id = ?
            ");
            
            $params = array_merge($notificationIds, [$userId]);
            return $stmt->execute($params);
        } catch (Exception $e) {
            error_log("Failed to mark notifications as read: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get unread notification count
     */
    public function getUnreadCount($userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count 
                FROM notifications 
                WHERE user_id = ? AND read_at IS NULL
            ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch();
            return $result['count'];
        } catch (Exception $e) {
            error_log("Failed to get unread count: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Send task reminder
     */
    public function sendTaskReminder($userId, $taskTitle, $taskId, $dueDate) {
        $data = [
            'action' => 'open_task',
            'taskId' => $taskId
        ];
        
        $message = "Ihre Aufgabe '$taskTitle' ist am " . date('d.m.Y H:i', strtotime($dueDate)) . " fällig.";
        
        return $this->sendNotification(
            $userId,
            'Aufgaben-Erinnerung',
            $message,
            'task_reminder',
            $data
        );
    }
    
    /**
     * Send calendar event reminder
     */
    public function sendCalendarReminder($userId, $eventTitle, $eventId, $eventDate) {
        $data = [
            'action' => 'open_calendar',
            'eventId' => $eventId
        ];
        
        $message = "Ihr Termin '$eventTitle' beginnt am " . date('d.m.Y H:i', strtotime($eventDate)) . ".";
        
        return $this->sendNotification(
            $userId,
            'Termin-Erinnerung',
            $message,
            'calendar_event',
            $data
        );
    }
    
    /**
     * Send finance update
     */
    public function sendFinanceUpdate($userId, $amount, $description) {
        $data = [
            'action' => 'open_finance'
        ];
        
        $message = "Neue Finanztransaktion: $description ($amount €)";
        
        return $this->sendNotification(
            $userId,
            'Finanz-Update',
            $message,
            'finance_update',
            $data
        );
    }
}
?>
