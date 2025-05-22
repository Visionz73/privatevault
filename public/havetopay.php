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
        <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css\" rel=\"stylesheet\">
        <link href=\"https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap\" rel=\"stylesheet\">
        <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
        <style>
            body {
                font-family: 'Poppins', sans-serif;
                background-color: #f8f9fa;
                color: #343a40;
            }
            .error-container {
                max-width: 600px;
                margin: 80px auto;
            }
            .error-card {
                border: none;
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                overflow: hidden;
            }
            .error-header {
                background: linear-gradient(45deg, #ff5b5b, #ff9a9e);
                padding: 25px;
                position: relative;
            }
            .error-icon {
                font-size: 3rem;
                margin-bottom: 10px;
                color: rgba(255,255,255,0.9);
            }
            .error-body {
                padding: 30px;
            }
            .btn-return {
                background: linear-gradient(45deg, #3a7bd5, #00d2ff);
                border: none;
                border-radius: 50px;
                padding: 10px 25px;
                font-weight: 500;
                box-shadow: 0 4px 15px rgba(0,210,255,0.4);
                transition: transform 0.3s, box-shadow 0.3s;
            }
            .btn-return:hover {
                transform: translateY(-3px);
                box-shadow: 0 7px 20px rgba(0,210,255,0.5);
            }
            .error-details {
                background-color: #f8f9fa;
                border-radius: 10px;
                padding: 15px;
                margin-top: 20px;
                font-size: 0.9rem;
            }
            .error-wave {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 15px;
                background: url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 1440 320\"><path fill=\"%23fff\" fill-opacity=\"1\" d=\"M0,64L48,80C96,96,192,128,288,128C384,128,480,96,576,80C672,64,768,64,864,74.7C960,85,1056,107,1152,112C1248,117,1344,107,1392,101.3L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z\"></path></svg>');
                background-size: cover;
            }
        </style>
    </head>
    <body>
        <div class=\"container error-container\">
            <div class=\"error-card card\">
                <div class=\"error-header text-white text-center\">
                    <i class=\"fas fa-exclamation-triangle error-icon\"></i>
                    <h2 class=\"fw-bold mb-0\">Oops! Something went wrong</h2>
                    <p class=\"mb-0\">We couldn't load the HaveToPay module</p>
                    <div class=\"error-wave\"></div>
                </div>
                <div class=\"error-body text-center\">
                    <p>Our team has been notified of this issue and we're working to fix it.</p>
                    <p>Please try again later or contact support if the problem persists.</p>
                    <a href=\"index.php\" class=\"btn btn-primary btn-return\">
                        <i class=\"fas fa-home me-2\"></i>Return to Dashboard
                    </a>
                    <div class=\"error-details mt-4\">
                        <p class=\"text-muted mb-0\"><small>Error ID: " . substr(md5($e->getMessage()), 0, 8) . "</small></p>
                        <p class=\"text-muted mb-0\"><small>Time: " . date('Y-m-d H:i:s') . "</small></p>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>";
}
?>
