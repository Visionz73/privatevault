<?php
// templates/settings.php
// Variables expected from src/controllers/settings.php:
// $pageTitle, $csrf_token_settings, $currentDisplayName, $currentEmail, $user (for navbar)
// Session messages like $_SESSION['success_message'] or $_SESSION['error_message'] are handled by the controller's redirect
// and displayed here.
// Session is started by public/settings.php -> config.php
?>
<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?php echo htmlspecialchars($pageTitle ?? 'User Settings'); ?> | Private Vault</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Adjust main content margin for mobile when top navbar is present */
        @media (max-width: 768px) { /* md breakpoint in Tailwind is 768px */
            main.content-area { 
                margin-top: 4rem; /* Approx h-16, adjust if navbar.php mobile height changes from h-14 (3.5rem) */
            }
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-100 via-gray-100 to-stone-100 flex flex-col antialiased">

    <?php require_once __DIR__ . '/navbar.php'; // The Tailwind sidebar navbar ?>

    <!-- Main content area, adjusted for sidebar -->
    <main class="content-area ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-6 md:p-8 space-y-6">
        <div class="max-w-3xl mx-auto">
            <header class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">
                    <?php echo htmlspecialchars($pageTitle ?? 'User Settings'); ?>
                </h1>
            </header>

            <?php // Display session feedback messages ?>
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="bg-green-50 border-l-4 border-green-400 text-green-700 p-4 mb-6 rounded-md shadow-sm" role="alert">
                    <div class="flex">
                        <div class="py-1"><svg class="fill-current h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM6.7 9.29L9 11.6l4.3-4.3 1.4 1.42L9 14.4l-3.7-3.7 1.4-1.42z"/></svg></div>
                        <div>
                            <p class="font-bold">Success</p>
                            <p class="text-sm"><?php echo htmlspecialchars($_SESSION['success_message']); ?></p>
                        </div>
                    </div>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="bg-red-50 border-l-4 border-red-400 text-red-700 p-4 mb-6 rounded-md shadow-sm" role="alert">
                     <div class="flex">
                        <div class="py-1"><svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 5l1.41 1.41L7.83 9l2.58 2.59L9 13l-4-4 4-4z"/></svg></div>
                        <div>
                            <p class="font-bold">Error</p>
                            <p class="text-sm"><?php echo nl2br(htmlspecialchars($_SESSION['error_message'])); ?></p>
                        </div>
                    </div>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <?php // Account Information Form ?>
            <section class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
                <div class="bg-gray-50 p-5 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-700">Account Information</h2>
                </div>
                <div class="p-6">
                    <form action="settings.php" method="POST" class="space-y-6">
                        <input type="hidden" name="csrf_token_settings" value="<?php echo htmlspecialchars($csrf_token_settings ?? ''); ?>">
                        
                        <div>
                            <label for="displayName" class="block text-sm font-medium text-gray-700 mb-1">Display Name</label>
                            <input type="text" name="displayName" id="displayName" 
                                   value="<?php echo htmlspecialchars($currentDisplayName ?? ''); ?>" 
                                   placeholder="Enter your display name" required
                                   class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" name="email" id="email" 
                                   value="<?php echo htmlspecialchars($currentEmail ?? ''); ?>" 
                                   placeholder="Enter your email address" required
                                   class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        
                        <div class="flex justify-end pt-2">
                            <button type="submit" name="saveAccountChanges" 
                                    class="inline-flex justify-center py-2 px-5 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Save Account Changes
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <?php // Security Section ?>
            <section class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
                <div class="bg-gray-50 p-5 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-700">Security</h2>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 mb-4">To change your password, please proceed to the password change page.</p>
                    <div class="flex justify-start">
                        <a href="change_password.php" 
                           class="inline-flex justify-center py-2 px-5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                           Change Password
                        </a>
                    </div>
                </div>
            </section>
            
            <div class="text-center mt-10">
                 <a href="profile.php" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                    &larr; Back to Profile
                 </a>
            </div>
        </div>
    </main>

</body>
</html>
