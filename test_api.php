<?php
// Test script for notifications API
session_start();

// Simulate a logged-in user (replace with actual user ID)
$_SESSION['user_id'] = 1; // Change this to an existing user ID

echo "Testing Notifications API...\n\n";

// Test GET request
echo "Testing GET request...\n";
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET = [];

ob_start();
include 'api/notifications.php';
$response = ob_get_clean();

echo "Response: " . $response . "\n\n";

// Test unread count
echo "Testing unread count...\n";
$_GET['action'] = 'unread_count';

ob_start();
include 'api/notifications.php';
$response = ob_get_clean();

echo "Response: " . $response . "\n\n";

echo "API test completed.\n";
?>
