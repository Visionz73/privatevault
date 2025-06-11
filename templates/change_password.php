<?php
// templates/change_password.php
// Variables expected from src/controllers/change_password.php:
// $pageTitle, $user (for navbar.php)
// Session is started by public/change_password.php -> config.php
?>
<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?php echo htmlspecialchars($pageTitle ?? 'Change Password'); ?> | Private Vault</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Adjust main content margin for mobile when top navbar is present */
        @media (max-width: 768px) { /* md breakpoint in Tailwind is 768px */
            main.content-area { 
                margin-top: 4rem; /* Approx h-16, Tailwind h-14 is 3.5rem. Ensure this matches navbar.php mobile height. */
            }
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-100 via-gray-100 to-stone-100 flex flex-col antialiased">

    <?php require_once __DIR__ . '/navbar.php'; // The Tailwind sidebar navbar ?>

    <!-- Main content area, adjusted for sidebar -->
    <main class="content-area ml-0 mt-16 md:ml-64 md:mt-0 flex-1 p-6 md:p-8">
        <div class="max-w-xl mx-auto"> <?php // Adjusted max-width for a typical content page ?>
            <header class="mb-6"> <?php // Reduced bottom margin slightly ?>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                    <?php echo htmlspecialchars($pageTitle ?? 'Change Password'); ?>
                </h1>
            </header>

            <div class="bg-white shadow-xl rounded-lg p-6 md:p-8">
                <p class="text-gray-700 mb-4 text-lg">This feature is currently under development.</p>
                <p class="text-gray-600 mb-6">The form to change your password will be available here soon.</p>
                
                <div class="mt-8 flex justify-start">
                    <a href="settings.php" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Back to Settings
                    </a>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
