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
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Einstellungen</h1>
            
            <!-- Session Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-300" role="alert">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg border border-red-300" role="alert">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Security Section -->
            <section class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h2 class="text-lg font-medium text-gray-900">Sicherheit</h2>
                </div>
                <div class="p-6 space-y-4">
                    <a href="profile.php?tab=security" 
                       class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div>
                            <h3 class="font-medium text-gray-900">Passwort ändern</h3>
                            <p class="text-sm text-gray-600">Aktualisieren Sie Ihr Passwort regelmäßig für optimale Sicherheit</p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </section>
            
            <!-- Personal Information Section -->
            <section class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h2 class="text-lg font-medium text-gray-900">Persönliche Informationen</h2>
                </div>
                <div class="p-6 space-y-4">
                    <a href="profile.php?tab=personal_info&subtab=personal_data" 
                       class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div>
                            <h3 class="font-medium text-gray-900">Persönliche Daten</h3>
                            <p class="text-sm text-gray-600">Bearbeiten Sie Ihre persönlichen Informationen</p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    
                    <a href="profile.php?tab=personal_info&subtab=public_profile" 
                       class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div>
                            <h3 class="font-medium text-gray-900">Öffentliches Profil</h3>
                            <p class="text-sm text-gray-600">Verwalten Sie Ihre öffentlich sichtbaren Informationen</p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </section>
            
            <!-- Notifications Section -->
            <section class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h2 class="text-lg font-medium text-gray-900">Benachrichtigungen</h2>
                </div>
                <div class="p-6">
                    <a href="profile.php?tab=notifications" 
                       class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div>
                            <h3 class="font-medium text-gray-900">Benachrichtigungseinstellungen</h3>
                            <p class="text-sm text-gray-600">Passen Sie an, welche Benachrichtigungen Sie erhalten</p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </section>
            
            <div class="text-center mt-10">
                 <a href="profile.php" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                    &larr; Zurück zum Profil
                 </a>
            </div>
        </div>
    </main>

</body>
</html>
