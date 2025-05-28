<?php
// Get current user data if available
$currentUser = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? 'Guest';
$isAdmin = $_SESSION['is_admin'] ?? false;

// Function to check if the current page matches a specific page
function isActive($pageName) {
    $currentPage = basename($_SERVER['PHP_SELF']);
    return $currentPage === $pageName ? true : false;
}
?>

<!-- Mobile Navigation Bar -->
<nav class="md:hidden fixed top-0 left-0 right-0 bg-gradient-to-r from-purple-700 to-indigo-700 shadow-md z-40">
    <div class="flex items-center justify-between px-4 py-3">
        <a href="/dashboard.php" class="flex items-center">
            <img src="/assets/logo.png" alt="Logo" class="h-16 w-auto" />
        </a>
        <button id="mobile-menu-button" class="text-white focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>
    </div>
    
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden px-2 pb-3 space-y-1">
        <a href="/dashboard.php" class="block px-3 py-2 rounded-md text-white hover:bg-purple-600">Dashboard</a>
        <a href="/taskboard.php" class="block px-3 py-2 rounded-md text-white hover:bg-purple-600">Taskboard</a>
        <a href="/inbox.php" class="block px-3 py-2 rounded-md text-white hover:bg-purple-600">Inbox</a>
        <a href="/calendar.php" class="block px-3 py-2 rounded-md text-white hover:bg-purple-600">Calendar</a>
        
        <?php if ($isAdmin): ?>
        <!-- Admin Section - Mobile -->
        <div class="pt-2 mt-2 border-t border-purple-500">
            <div class="px-3 py-1 text-sm font-medium text-purple-300">Admin Area</div>
            <a href="/admin.php" class="block px-3 py-2 rounded-md text-white hover:bg-purple-600">Admin Panel</a>
            <a href="/admin/users.php" class="block px-3 py-2 rounded-md text-white hover:bg-purple-600">User Management</a>
            <a href="/admin/groups.php" class="block px-3 py-2 rounded-md text-white hover:bg-purple-600">Group Management</a>
            <a href="/admin/settings.php" class="block px-3 py-2 rounded-md text-white hover:bg-purple-600">System Settings</a>
        </div>
        <?php endif; ?>
    </div>
</nav>

<!-- Desktop Sidebar Navigation -->
<nav class="hidden md:block fixed top-0 left-0 bottom-0 w-64 bg-gradient-to-b from-purple-700 to-indigo-700 shadow-xl z-40">
    <div class="flex items-center justify-center h-20 border-b border-purple-600">
        <a href="/dashboard.php" class="flex items-center">
            <img src="/assets/logo.png" alt="Logo" class="h-16 w-auto" />
        </a>
    </div>
    
    <div class="px-4 py-4">
        <ul class="space-y-2">
            <li>
                <a href="/dashboard.php" class="flex items-center px-4 py-3 text-white rounded-xl hover:bg-purple-600 transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="/taskboard.php" class="flex items-center px-4 py-3 text-white rounded-xl hover:bg-purple-600 transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span>Taskboard</span>
                </a>
            </li>
            <li>
                <a href="/inbox.php" class="flex items-center px-4 py-3 text-white rounded-xl hover:bg-purple-600 transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <span>Inbox</span>
                </a>
            </li>
            <li>
                <a href="/calendar.php" class="flex items-center px-4 py-3 text-white rounded-xl hover:bg-purple-600 transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Calendar</span>
                </a>
            </li>
            
            <?php if ($isAdmin): ?>
            <!-- Admin Section - Desktop -->
            <li class="pt-6">
                <div class="px-4 py-2 text-sm font-medium text-purple-300 uppercase tracking-wider">Admin Area</div>
                <ul class="mt-2 space-y-1">
                    <li>
                        <a href="/admin.php" class="flex items-center px-4 py-3 text-white rounded-xl hover:bg-purple-600 transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>Admin Panel</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/users.php" class="flex items-center px-4 py-3 text-white rounded-xl hover:bg-purple-600 transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span>User Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/groups.php" class="flex items-center px-4 py-3 text-white rounded-xl hover:bg-purple-600 transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span>Group Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/settings.php" class="flex items-center px-4 py-3 text-white rounded-xl hover:bg-purple-600 transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>System Settings</span>
                        </a>
                    </li>
                </ul>
            </li>
            <?php endif; ?>
        </ul>
    </div>
    
    <!-- User Profile Section -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-purple-600">
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-500 flex items-center justify-center">
                <span class="text-white font-semibold"><?= isset($user['username']) ? substr($user['username'], 0, 2) : 'U' ?></span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">
                    <?= isset($user['username']) ? htmlspecialchars($user['username']) : 'User' ?>
                </p>
                <p class="text-xs text-purple-300 truncate">
                    <?= isset($user['email']) ? htmlspecialchars($user['email']) : 'user@example.com' ?>
                </p>
            </div>
            <a href="/logout.php" class="text-white hover:text-purple-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
            </a>
        </div>
    </div>
</nav>

<script>
    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
</script>
