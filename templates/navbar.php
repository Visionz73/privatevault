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

<!-- Mobile Top Navigation Bar (visible on small screens) -->
<nav class="md:hidden fixed top-0 left-0 right-0 bg-white shadow-md z-50">
    <div class="flex items-center justify-between px-4 py-3">
        <div class="flex items-center">
            <button id="mobile-menu-button" class="mr-2 text-gray-600 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <a href="/index.php" class="text-xl font-bold text-blue-600">Private Vault</a>
        </div>
        <div class="relative group">
            <button class="flex items-center text-sm font-medium text-gray-700 focus:outline-none">
                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold mr-2">
                    <?= substr($username, 0, 1); ?>
                </div>
                <span class="hidden sm:inline-block"><?= htmlspecialchars($username); ?></span>
            </button>
            <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden group-hover:block">
                <a href="/profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                <a href="/settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                <div class="border-t border-gray-100"></div>
                <a href="/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
            </div>
        </div>
    </div>
</nav>

<!-- Sidebar Navigation (visible on medium and larger screens) -->
<aside id="sidebar" class="fixed top-0 left-0 bottom-0 w-64 bg-white shadow-md overflow-y-auto transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out z-40">
    <div class="p-5 flex items-center justify-center border-b border-gray-200">
        <a href="/index.php" class="text-xl font-bold text-blue-600">Private Vault</a>
    </div>
    
    <nav class="p-4 space-y-2">
        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 pl-2">Main</div>
        
        <a href="/index.php" class="flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors <?= isActive('index.php') ? 'bg-blue-50 text-blue-600' : '' ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span>Dashboard</span>
        </a>
        
        <a href="/taskboard.php" class="flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors <?= isActive('taskboard.php') ? 'bg-blue-50 text-blue-600' : '' ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            <span>Tasks</span>
        </a>
        
        <a href="/inbox.php" class="flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors <?= isActive('inbox.php') ? 'bg-blue-50 text-blue-600' : '' ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <span>Inbox</span>
        </a>
        
        <a href="/calendar.php" class="flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors <?= isActive('calendar.php') ? 'bg-blue-50 text-blue-600' : '' ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span>Calendar</span>
        </a>
        
        <a href="/havetopay.php" class="flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors <?= isActive('havetopay.php') ? 'bg-blue-50 text-blue-600' : '' ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>HaveToPay</span>
        </a>
        
        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mt-6 mb-2 pl-2">Account</div>
        
        <a href="/profile.php" class="flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors <?= isActive('profile.php') ? 'bg-blue-50 text-blue-600' : '' ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span>Profile</span>
        </a>
        
        <a href="/settings.php" class="flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors <?= isActive('settings.php') ? 'bg-blue-50 text-blue-600' : '' ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span>Settings</span>
        </a>
        
        <?php if ($isAdmin): ?>
        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mt-6 mb-2 pl-2">Admin</div>
        
        <a href="/admin.php" class="flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors <?= isActive('admin.php') ? 'bg-blue-50 text-blue-600' : '' ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            <span>Admin Dashboard</span>
        </a>
        <?php endif; ?>
        
        <div class="border-t border-gray-200 my-4"></div>
        
        <a href="/logout.php" class="flex items-center px-4 py-2.5 text-red-600 rounded-lg hover:bg-red-50 transition-colors">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            <span>Logout</span>
        </a>
    </nav>
</aside>

<!-- Mobile menu backdrop -->
<div id="sidebar-backdrop" class="fixed inset-0 bg-black opacity-0 pointer-events-none transition-opacity duration-300 ease-in-out z-30"></div>

<!-- JavaScript for mobile menu toggle -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const backdrop = document.getElementById('sidebar-backdrop');
        
        function openMobileMenu() {
            sidebar.classList.remove('-translate-x-full');
            backdrop.classList.remove('opacity-0', 'pointer-events-none');
            backdrop.classList.add('opacity-50');
        }
        
        function closeMobileMenu() {
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.remove('opacity-50');
            backdrop.classList.add('opacity-0', 'pointer-events-none');
        }
        
        mobileMenuButton.addEventListener('click', function() {
            if (sidebar.classList.contains('-translate-x-full')) {
                openMobileMenu();
            } else {
                closeMobileMenu();
            }
        });
        
        backdrop.addEventListener('click', closeMobileMenu);
        
        // Close mobile menu when window is resized to desktop size
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) { // md breakpoint
                closeMobileMenu();
            }
        });
    });
</script>
