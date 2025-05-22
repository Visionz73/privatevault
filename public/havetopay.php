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
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
    </head>
    <body>
        <div class=\"container mt-5\">
            <div class=\"card\">
                <div class=\"card-header bg-danger text-white\">
                    <h4>Error Loading HaveToPay Module</h4>
                </div>
                <div class=\"card-body\">
                    <p>We encountered an error while loading the HaveToPay module.</p>
                    <p>Please try again later or contact the administrator.</p>
                    <a href=\"index.php\" class=\"btn btn-primary\">Return to Dashboard</a>
                </div>
            </div>
        </div>
    </body>
    </html>";
}
?>
