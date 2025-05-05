<?php
// src/lib/auth.php

// Session-Status prüfen (falls nicht schon geschehen)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function getUser(): ?array {
    global $pdo;
    if (!isLoggedIn()) {
        return null;
    }
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

/**
 * Stellt sicher, dass der eingeloggte User eine der erlaubten Rollen hat.
 * Beispiel: requireRole(['admin','member']);
 */
function requireRole(array $roles): void {
    $user = getUser();
    if (!$user || !in_array($user['role'], $roles, true)) {
        http_response_code(403);
        echo 'Zugriff verweigert';
        exit;
    }
}
