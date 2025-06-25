<?php
// Simple API test file to check if notes API is working
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set response header
header('Content-Type: application/json');

// Create a simple test response
$response = [
    'success' => true,
    'message' => 'API test successful',
    'notes' => [
        [
            'id' => 1,
            'title' => 'Test Note 1',
            'content' => 'This is a test note for the Second Brain graph view',
            'color' => '#fbbf24',
            'tags' => ['test', 'graph'],
            'created_at' => date('Y-m-d H:i:s'),
            'x' => 100,
            'y' => 100
        ],
        [
            'id' => 2,
            'title' => 'Test Note 2',
            'content' => 'Another test note connected to the first one',
            'color' => '#ef4444',
            'tags' => ['test', 'connection'],
            'created_at' => date('Y-m-d H:i:s'),
            'x' => 300,
            'y' => 200
        ],
        [
            'id' => 3,
            'title' => 'Second Brain',
            'content' => 'A note about building your second brain with interconnected thoughts',
            'color' => '#8b5cf6',
            'tags' => ['secondbrain', 'knowledge'],
            'created_at' => date('Y-m-d H:i:s'),
            'x' => 200,
            'y' => 350
        ]
    ]
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>
