<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Security Settings'); ?> | Private Vault</title> <?php // Added | Private Vault for consistency ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" /> <?php // Added Inter font to match other Tailwind pages ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { 
            font-family: 'Inter', sans-serif; /* Added Inter font to body */
            /* Ensure body takes full height for gradient if content is short */
            min-height: 100vh;
            display: flex; /* For flex-col layout */
            flex-direction: column; /* For flex-col layout */
        }
        /* Adjust main content margin for mobile when top navbar is present */
        @media (max-width: 768px) { /* md breakpoint in Tailwind is 768px */
            main.content-area { 
                margin-top: 4rem; /* Approx h-16, Tailwind h-14 is 3.5rem. Ensure this matches navbar.php mobile height. */
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-100 via-gray-100 to-stone-100 antialiased"> <?php // Added gradient and antialiased like other pages ?>
    <?php require_once __DIR__ . '/navbar.php'; // The Tailwind sidebar ?>

    <main class="content-area ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-6 md:p-8"> <?php // Added flex-1 for full height, adjusted padding ?>
        <div class="max-w-2xl mx-auto"> 
            <header class="mb-6"> <?php // Added a header block for the title ?>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($pageTitle ?? 'Security Settings'); ?></h1>
            </header>

            <div class="bg-white shadow-xl rounded-lg p-6 md:p-8"> <?php // Enhanced card styling ?>

                <!-- Change Password Section -->
                <section class="mb-8 pb-6 border-b border-gray-200"> <?php // Added section tag and bottom border ?>
                    <h2 class="text-xl font-semibold text-gray-700 mb-3">Change Password</h2>
                    <p class="text-gray-600 mb-4">Secure your account by regularly changing your password.</p>
                    <a href="change_password.php" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Change Password
                    </a>
                </section>

                <!-- Two-Factor Authentication (2FA) Section -->
                <section class="mb-8"> <?php // Added section tag ?>
                    <h2 class="text-xl font-semibold text-gray-700 mb-3">Two-Factor Authentication (2FA)</h2>
                    <p class="text-gray-600 mb-2">Add an extra layer of security to your account. With 2FA, you'll need your password and a code from an authenticator app to log in.</p>
                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-md">
                        <p class="text-sm text-blue-700 italic"><i class="fas fa-info-circle mr-2"></i>This feature is planned for future implementation.</p> <?php // Styled the planned feature text ?>
                    </div>
                </section>
                
                <div class="mt-8 pt-6 border-t border-gray-200"> <?php // Added top border for separation ?>
                    <a href="settings.php" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                        <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Back to General Settings
                    </a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
