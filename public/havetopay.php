<?php
// Entry point for HaveToPay module
try {
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../src/controllers/havetopay.php';
} catch (Exception $e) {
    // Log error
    error_log("Error in HaveToPay module: " . $e->getMessage());
    
    // Show a user-friendly error page
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Error in HaveToPay</title>
        <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css\" rel=\"stylesheet\">
