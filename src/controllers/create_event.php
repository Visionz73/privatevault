<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';

requireLogin();
$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

$title = trim($_POST['title'] ?? '');
$date  = trim($_POST['date'] ?? '');

if (!$title || !$date) {
    http_response_code(400);
    echo json_encode(['error' => 'Title and date are required']);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO events (user_id, title, event_date) VALUES (?, ?, ?)");
$stmt->execute([$userId, $title, $date]);
$eventId = $pdo->lastInsertId();

echo json_encode([
    'success' => true,
    'event'   => [
        'id'    => $eventId,
        'title' => $title,
        'date'  => $date
    ]
]);
