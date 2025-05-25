<?php
// src/controllers/docs.php
// This controller is included by public/docs.php
// public/docs.php handles session_start(), config.php, and auth.php (requireLogin(), getUser())

// $user variable is needed by templates/navbar.php
// auth.php (which defines getUser()) is included by public/docs.php
$user = getUser(); 

if (!$user) {
    // This case should be rare due to requireLogin() in public/docs.php
    // Redirect to login if user data somehow isn't available.
    header('Location: login.php'); 
    exit;
}

$pageTitle = "My Documents";
$documents = [];
$page_error = null; // Initialize page_error to null

// Fetch documents for the logged-in user
// Assumes $pdo is available globally from config.php (included via public/docs.php -> config.php)
// Assumes $_SESSION['user_id'] is set by requireLogin() (called in public/docs.php)
try {
    // Assuming 'documents' table columns: id, user_id, file_name, file_path, category, created_at
    // Ensure 'is_deleted' column is considered if soft deletes are implemented for documents.
    // For now, assuming no soft delete or it's handled elsewhere (e.g. only non-deleted items are in 'documents' table).
    // If a documents table has an 'is_deleted' column, the query should be:
    // "SELECT id, file_name, file_path, category, created_at FROM documents WHERE user_id = ? AND (is_deleted = 0 OR is_deleted IS NULL) ORDER BY created_at DESC"
    $stmt = $pdo->prepare("SELECT id, file_name, file_path, category, created_at FROM documents WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching documents for user_id {$_SESSION['user_id']}: " . $e->getMessage());
    $page_error = "Could not retrieve documents at this time. Please try again later.";
}

// Load the main template
// Variables available to templates/docs.php:
// $pageTitle, $user (for navbar.php), $documents, $page_error
require_once __DIR__ . '/../../templates/docs.php'; 
?>
