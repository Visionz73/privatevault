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
                background: var(--current-theme-bg, linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%));
                color: #343a40;
                transition: background 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .error-container {
                max-width: 600px;
                margin: 80px auto;
            }
            .error-card {
                background: rgba(255, 255, 255, 0.08);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.15);
                border-radius: 1.5rem;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
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
                color: white;
            }
            .btn-return {
                background: linear-gradient(45deg, #3a7bd5, #00d2ff);
                border: none;
                border-radius: 50px;
                padding: 10px 25px;
                font-weight: 500;
                box-shadow: 0 4px 15px rgba(0,210,255,0.4);
                transition: transform 0.3s, box-shadow 0.3s;
                color: white;
            }
            .btn-return:hover {
                transform: translateY(-3px);
                box-shadow: 0 7px 20px rgba(0,210,255,0.5);
                color: white;
            }
            .error-details {
                background: rgba(255, 255, 255, 0.1);
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
        
        <script>
            // Apply saved theme on load
            document.addEventListener('DOMContentLoaded', () => {
                const savedTheme = localStorage.getItem('privatevault_theme') || 'cosmic';
                const themes = {
                    cosmic: 'linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%)',
                    ocean: 'linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3730a3 100%)',
                    sunset: 'linear-gradient(135deg, #f59e0b 0%, #dc2626 50%, #7c2d12 100%)',
                    forest: 'linear-gradient(135deg, #064e3b 0%, #047857 50%, #065f46 100%)',
                    purple: 'linear-gradient(135deg, #581c87 0%, #7c3aed 50%, #3730a3 100%)',
                    rose: 'linear-gradient(135deg, #9f1239 0%, #e11d48 50%, #881337 100%)',
                    cyber: 'linear-gradient(135deg, #065f46 0%, #0891b2 50%, #1e40af 100%)',
                    ember: 'linear-gradient(135deg, #7c2d12 0%, #ea580c 50%, #92400e 100%)',
                    midnight: 'linear-gradient(135deg, #111827 0%, #1f2937 50%, #374151 100%)',
                    aurora: 'linear-gradient(135deg, #065f46 0%, #059669 25%, #0891b2 50%, #3b82f6 75%, #8b5cf6 100%)',
                    neon: 'linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%)',
                    volcanic: 'linear-gradient(135deg, #2c1810 0%, #8b0000 50%, #ff4500 100%)',
                    matrix: 'linear-gradient(135deg, #0d1117 0%, #161b22 50%, #21262d 100%)',
                    synthwave: 'linear-gradient(135deg, #2d1b69 0%, #8b5a97 50%, #ff006e 100%)',
                    deepspace: 'linear-gradient(135deg, #0c0c0c 0%, #1a0033 50%, #4a148c 100%)',
                    crimson: 'linear-gradient(135deg, #1a0000 0%, #660000 50%, #cc0000 100%)',
                    arctic: 'linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%)'
                };
                
                if (themes[savedTheme]) {
                    document.body.style.background = themes[savedTheme];
                }
            });
        </script>
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
