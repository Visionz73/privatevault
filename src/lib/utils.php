<?php
// Shared utility functions

/**
 * Generate user initials from username
 */
function getUserInitials($user) {
    // Handle both user array and user ID
    if (is_array($user)) {
        if (!isset($user['username'])) return 'U';
        $username = $user['username'];
    } else {
        // Assume it's a user ID
        global $pdo;
        if (empty($user)) return 'U';
        
        $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
        $stmt->execute([$user]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $username = $result ? $result['username'] : '';
    }
    
    if (empty($username)) return 'U';
    
    $names = explode(' ', trim($username));
    if (count($names) >= 2) {
        return strtoupper(substr($names[0], 0, 1) . substr($names[1], 0, 1));
    }
    return strtoupper(substr($username, 0, 2));
}

/**
 * Get username by user ID
 */
function getUserName($userId) {
    global $pdo;
    if (empty($userId)) return '';
    
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['username'] : '';
}

/**
 * Get group name by group ID
 */
function getGroupName($groupId) {
    global $pdo;
    if (empty($groupId)) return '';
    
    $stmt = $pdo->prepare("SELECT name FROM user_groups WHERE id = ?");
    $stmt->execute([$groupId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['name'] : '';
}

/**
 * Get group initials by group ID
 */
function getGroupInitials($groupId) {
    $name = getGroupName($groupId);
    if (empty($name)) return '';
    
    $parts = explode(' ', $name);
    $initials = '';
    
    foreach ($parts as $part) {
        if (!empty($part)) {
            $initials .= strtoupper(substr($part, 0, 1));
        }
    }
    
    return $initials;
}
