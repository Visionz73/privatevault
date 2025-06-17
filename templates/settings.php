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
        body { 
            font-family: 'Inter', sans-serif;
            background: var(--current-theme-bg, linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%));
            min-height: 100vh;
            transition: background 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }
        @media (max-width: 768px) {
            main.content-area { 
                margin-top: 4rem;
            }
        }
        
        .glassmorphism-container {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        
        .form-input {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 0.75rem;
        }
        
        .form-input:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.4);
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.8) 0%, rgba(79, 70, 229, 0.8) 100%);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 0.75rem;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.9) 0%, rgba(79, 70, 229, 0.9) 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(147, 51, 234, 0.3);
        }
    </style>
    
    <script>
        // Listen for theme changes
        window.addEventListener('themeChanged', (e) => {
            document.documentElement.style.setProperty('--current-theme-bg', e.detail.background);
        });
        
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
                volcanic: 'linear-gradient(135deg, #2c1810 0%, #8b0000 50%, #ff4500 100%)'
            };
            
            if (themes[savedTheme]) {
                document.body.style.background = themes[savedTheme];
            }
        });
    </script>
</head>
<body class="min-h-screen flex">

    <?php require_once __DIR__ . '/navbar.php'; ?>

    <!-- Main content area, adjusted for sidebar -->
    <main class="content-area ml-0 mt-16 md:ml-64 md:mt-0 flex-1 p-6 md:p-8 space-y-6">
        <div class="max-w-3xl mx-auto">
            <header class="mb-8">
                <h1 class="text-3xl font-bold text-white">
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
            <section class="glassmorphism-container overflow-hidden mb-8">
                <div class="bg-white/10 p-5 border-b border-white/20">
                    <h2 class="text-xl font-semibold text-white">Account Information</h2>
                </div>
                <div class="p-6">
                    <form action="settings.php" method="POST" class="space-y-6">
                        <input type="hidden" name="csrf_token_settings" value="<?php echo htmlspecialchars($csrf_token_settings ?? ''); ?>">
                        
                        <div>
                            <label for="displayName" class="block text-sm font-medium text-gray-700 mb-1">Display Name</label>
                            <input type="text" name="displayName" id="displayName" 
                                   value="<?php echo htmlspecialchars($currentDisplayName ?? ''); ?>" 
                                   placeholder="Enter your display name" required
                                   class="form-input mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" name="email" id="email" 
                                   value="<?php echo htmlspecialchars($currentEmail ?? ''); ?>" 
                                   placeholder="Enter your email address" required
                                   class="form-input mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        
                        <div class="flex justify-end pt-2">
                            <button type="submit" name="saveAccountChanges" 
                                    class="btn-primary px-5 py-2">
                                Save Account Changes
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <?php // Security Section ?>
            <section class="glassmorphism-container overflow-hidden mb-8">
                <div class="bg-white/10 p-5 border-b border-white/20">
                    <h2 class="text-xl font-semibold text-white">Security</h2>
                </div>
                <div class="p-6">
                    <p class="text-white/80 mb-4">To change your password, please proceed to the password change page.</p>
                    <div class="flex justify-start">
                        <a href="change_password.php" 
                           class="inline-flex justify-center py-2 px-5 border border-white/20 text-white bg-white/10 hover:bg-white/15 rounded-lg transition-all duration-200">
                           Change Password
                        </a>
                    </div>
                </div>
            </section>
            
            <div class="text-center mt-10">
                 <a href="profile.php" class="text-white/80 hover:text-white text-sm font-medium">
                    &larr; Back to Profile
                 </a>
            </div>
        </div>
    </main>

</body>
</html>
