<?php
// Fetch current user data for the navbar
require_once __DIR__ . '/../src/lib/auth.php';
$user = getUser();

// Define the navigation items with icons
$navItems = [
    'index.php' => ['title' => 'Dashboard', 'icon' => '<svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>'],
    'inbox.php' => ['title' => 'Inbox', 'icon' => '<svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>'],
    'calendar.php' => ['title' => 'Kalender', 'icon' => '<svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>'],
    'taskboard.php' => ['title' => 'Taskboard', 'icon' => '<svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>'],
    'havetopay.php' => ['title' => 'HaveToPay', 'icon' => '<svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'],
    'admin.php' => ['title' => 'Admin', 'icon' => '<svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>'],
    'groups.php' => ['title' => 'Gruppen', 'icon' => '<svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>'],
];

// Current page for highlighting active menu item
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Desktop sidebar navigation (visible on md screens and up) -->
<aside class="fixed left-0 top-0 h-screen w-64 bg-gray-100/90 backdrop-blur-sm shadow-lg z-30 hidden md:block overflow-y-auto">
    <!-- App Logo -->
    <div class="p-5">
        <div class="flex items-center">
            <div class="bg-purple-600 rounded-full w-10 h-10 flex items-center justify-center mr-3">
                <span class="text-white text-xl font-semibold">e</span>
            </div>
            <span class="text-gray-700 text-2xl font-semibold">OMNI</span>
        </div>
    </div>

    <!-- User Profile -->
    <a href="profile.php" class="flex items-center px-5 py-3 hover:bg-gray-200/80 transition-colors">
        <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mr-4">
            <?= isset($user['username']) ? strtoupper(substr($user['username'], 0, 1)) : 'U' ?>
        </div>
        <div>
            <div class="text-lg font-medium"><?= htmlspecialchars($user['username'] ?? 'User') ?></div>
            <div class="text-sm text-gray-600">
                <?= isset($user['role']) && $user['role'] === 'admin' ? 'Admin' : 'User' ?>
            </div>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
    </a>

    <!-- Navigation Menu -->
    <nav class="mt-3">
        <?php foreach ($navItems as $url => $item): ?>
            <a href="<?= $url ?>" class="flex items-center px-5 py-3 <?= $currentPage === $url ? 'bg-gray-200/80 text-black' : 'text-gray-700 hover:bg-gray-200/60' ?> transition-colors">
                <div class="w-6 h-6 mr-4 text-gray-500">
                    <?= $item['icon'] ?>
                </div>
                <?= htmlspecialchars($item['title']) ?>
            </a>
        <?php endforeach; ?>
    </nav>
    
    <!-- Theme Toggle at the bottom -->
    <div class="absolute bottom-0 left-0 right-0 flex p-5 items-center justify-between text-gray-600">
        <button id="theme-toggle-light" class="p-2 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </button>
        <button id="theme-toggle-dark" class="p-2 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>
    </div>
</aside>

<!-- Mobile navigation (visible on screens below md) -->
<div class="fixed top-0 left-0 right-0 bg-white shadow-md z-30 md:hidden">
    <div class="flex items-center justify-between p-4">
        <div class="flex items-center">
            <div class="bg-purple-600 rounded-full w-8 h-8 flex items-center justify-center mr-2">
                <span class="text-white text-lg font-semibold">e</span>
            </div>
            <span class="text-gray-700 text-xl font-semibold">OMNI</span>
        </div>
        <button id="mobile-menu-button" class="p-2 rounded-md text-gray-700 hover:bg-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
            </svg>
        </button>
    </div>
    
    <!-- Mobile menu dropdown (hidden by default) -->
    <div id="mobile-menu" class="hidden">
        <div class="px-4 py-2 border-t border-gray-200">
            <a href="profile.php" class="flex items-center py-2">
                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                    <?= isset($user['username']) ? strtoupper(substr($user['username'], 0, 1)) : 'U' ?>
                </div>
                <div>
                    <div class="font-medium"><?= htmlspecialchars($user['username'] ?? 'User') ?></div>
                    <div class="text-xs text-gray-600">
                        <?= isset($user['role']) && $user['role'] === 'admin' ? 'Admin' : 'User' ?>
                    </div>
                </div>
            </a>
            
            <?php foreach ($navItems as $url => $item): ?>
                <a href="<?= $url ?>" class="flex items-center py-3 <?= $currentPage === $url ? 'text-blue-600' : 'text-gray-700' ?>">
                    <div class="w-5 h-5 mr-3 <?= $currentPage === $url ? 'text-blue-600' : 'text-gray-500' ?>">
                        <?= $item['icon'] ?>
                    </div>
                    <?= htmlspecialchars($item['title']) ?>
                </a>
            <?php endforeach; ?>
            
            <div class="flex justify-between mt-3 pt-3 border-t border-gray-100">
                <button id="mobile-theme-light" class="p-2 text-gray-600 rounded">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>
                <button id="mobile-theme-dark" class="p-2 text-gray-600 rounded">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for mobile menu toggle -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        }
        
        // Close menu when clicking anywhere else
        document.addEventListener('click', function(event) {
            if (!mobileMenu.contains(event.target) && !mobileMenuButton.contains(event.target) && !mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
            }
        });
    });
</script>
