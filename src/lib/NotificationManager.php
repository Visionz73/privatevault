<?php
/**
 * Notification Manager - Helper class for sending notifications
 */

class NotificationManager {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /** Send a notification to a user */
    public function sendNotification($userId, $title, $message, $type = 'info', $data = null, $scheduledFor = null) {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO notifications (user_id, title, message, type, data, scheduled_for, created_at) VALUES (?,?,?,?,?,?,NOW())"
            );
            $dataJson = $data ? json_encode($data) : null;
            $ok = $stmt->execute([$userId,$title,$message,$type,$dataJson,$scheduledFor]);
            if(!$ok) return false;
            $id = $this->pdo->lastInsertId();
            if(!$scheduledFor) { $this->checkEmailNotification($userId,$title,$message,$type); }
            return $id;
        } catch (\Throwable $e) {
            error_log('sendNotification error: '.$e->getMessage());
            return false;
        }
    }

    /** Send notification to multiple users */
    public function sendBulkNotification($userIds, $title, $message, $type = 'info', $data = null) {
        $sent = 0;
        foreach ($userIds as $uid) {
            if ($this->sendNotification($uid, $title, $message, $type, $data)) $sent++;
        }
        return $sent;
    }

    /** Check if user should receive email notification */
    private function checkEmailNotification($userId, $title, $message, $type) {
        try {
            $stmt = $this->pdo->prepare('SELECT email, notification_settings FROM users WHERE id=?');
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$user) return false;
            $settings = $user['notification_settings'] ? json_decode($user['notification_settings'], true) : [];
            $emailEnabled = $settings['email_enabled'] ?? true;
            $map = [
                'task_reminder' => $settings['task_reminders'] ?? true,
                'calendar_event' => $settings['calendar_events'] ?? true,
                'finance_update' => $settings['finance_updates'] ?? true,
                'security_warning' => $settings['security_warnings'] ?? true,
            ];
            $typeEnabled = $map[$type] ?? ($settings['system_alerts'] ?? true);
            if($emailEnabled && $typeEnabled) {
                $this->sendEmailNotification($user['email'],$title,$message,$type);
            }
            return true;
        } catch (\Throwable $e) {
            error_log('checkEmailNotification error: '.$e->getMessage());
            return false;
        }
    }

    /** Send email notification */
    private function sendEmailNotification($email, $title, $message, $type) {
        $subject = 'PrivateVault: '.$title;
        $headers = [
            'From' => 'noreply@privatevault.local',
            'Reply-To' => 'noreply@privatevault.local',
            'Content-Type' => 'text/html; charset=UTF-8',
            'X-Mailer' => 'PHP/'.phpversion()
        ];
        $html = $this->generateEmailTemplate($title,$message,$type);
        if(defined('DEVELOPMENT') && DEVELOPMENT) {
            error_log("Mail dev log: $email | $subject | $message");
            return true;
        }
        @mail($email,$subject,$html,implode("\r\n", array_map(fn($k,$v)=>"$k: $v", array_keys($headers), $headers)));
        return true;
    }

    /** Generate HTML email template */
    private function generateEmailTemplate($title, $message, $type) {
        $color = '#3B82F6';
        switch ($type) {
            case 'success': $color='#10B981'; break;
            case 'warning': $color='#F59E0B'; break;
            case 'error':
            case 'security_warning': $color='#EF4444'; break;
        }
        return "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>$title</title></head><body style='font-family:Arial,sans-serif;background:#f5f5f5;padding:20px;'>".
               "<div style='max-width:600px;margin:0 auto;background:#fff;border-radius:8px;padding:20px;border-left:6px solid $color'>".
               "<h2 style='margin-top:0;color:$color'>$title</h2><p style='font-size:15px;line-height:1.5;color:#333'>".nl2br(htmlspecialchars($message))."</p>".
               "<p style='font-size:12px;color:#666;margin-top:30px'>Automatische Nachricht • ".date('d.m.Y H:i:s')."</p></div></body></html>";
    }

    /** Mark notifications as read */
    public function markAsRead($notificationIds, $userId) {
        if(!$notificationIds) return false;
        $placeholders = implode(',', array_fill(0,count($notificationIds),'?'));
        try {
            $stmt = $this->pdo->prepare("UPDATE notifications SET read_at=NOW() WHERE id IN ($placeholders) AND user_id=?");
            return $stmt->execute(array_merge($notificationIds,[$userId]));
        } catch (\Throwable $e) {
            error_log('markAsRead error: '.$e->getMessage());
            return false;
        }
    }

    /** Get unread notification count */
    public function getUnreadCount($userId) {
        try {
            $stmt=$this->pdo->prepare('SELECT COUNT(*) c FROM notifications WHERE user_id=? AND read_at IS NULL');
            $stmt->execute([$userId]);
            return (int)$stmt->fetchColumn();
        } catch (\Throwable $e) {
            error_log('getUnreadCount error: '.$e->getMessage());
            return 0;
        }
    }

    /** Send task reminder */
    public function sendTaskReminder($userId, $taskTitle, $taskId, $dueDate) {
        $data=['action'=>'open_task','taskId'=>$taskId];
        $msg="Ihre Aufgabe '$taskTitle' ist am ".date('d.m.Y H:i', strtotime($dueDate))." fällig.";
        return $this->sendNotification($userId,'Aufgaben-Erinnerung',$msg,'task_reminder',$data);
    }

    /** Send calendar event reminder */
    public function sendCalendarReminder($userId, $eventTitle, $eventId, $eventDate) {
        $data=['action'=>'open_calendar','eventId'=>$eventId];
        $msg="Ihr Termin '$eventTitle' beginnt am ".date('d.m.Y H:i', strtotime($eventDate)).".";
        return $this->sendNotification($userId,'Termin-Erinnerung',$msg,'calendar_event',$data);
    }

    /** Send finance update */
    public function sendFinanceUpdate($userId, $amount, $description) {
        $data=['action'=>'open_finance'];
        $msg="Neue Finanztransaktion: $description ($amount €)";
        return $this->sendNotification($userId,'Finanz-Update',$msg,'finance_update',$data);
    }

    /** Fetch notifications with pagination and optional filters */
    public function listNotifications(int $userId, int $limit = 20, int $offset = 0, bool $unreadOnly = false, ?string $type = null): array {
        $limit = max(1, min(100, $limit));
        $offset = max(0, $offset);
        $sql = "SELECT id,title,message,type,data,created_at,read_at FROM notifications WHERE user_id=?";
        $params=[$userId];
        if ($unreadOnly) { $sql .= " AND read_at IS NULL"; }
        if ($type) { $sql .= " AND type = ?"; $params[] = $type; }
        $sqlCount = "SELECT COUNT(*) FROM (".$sql.") c";
        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        try {
            $stmt=$this->pdo->prepare($sqlCount); $stmt->execute($params); $total=(int)$stmt->fetchColumn();
            $stmt=$this->pdo->prepare($sql); $stmt->execute(array_merge($params,[$limit,$offset]));
            $rows=$stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            return ['total'=>$total,'limit'=>$limit,'offset'=>$offset,'notifications'=>$rows];
        } catch (\Throwable $e) {
            error_log('listNotifications error: '.$e->getMessage());
            return ['total'=>0,'limit'=>$limit,'offset'=>$offset,'notifications'=>[]];
        }
    }
}
?>
