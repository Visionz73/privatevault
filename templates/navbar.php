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
<nav class="md:hidden fixed top-0 left-0 right-0 bg-gradient-to-r from-purple-700 to-indigo-700 shadow-md z-40">
    <div class="flex items-center justify-between px-4 py-3">
        <a href="/dashboard.php" class="flex items-center">
            <img src="/assets/logo.png" alt="Logo" class="h-8 w-auto" />
        </a>
        
        <button id="mobile-menu-button" class="text-white focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>
</nav>

<!-- Sidebar Navigation (visible on medium and larger screens) -->
<aside id="sidebar" class="fixed top-0 left-0 bottom-0 w-64 bg-gradient-to-br from-purple-700 via-purple-600 to-indigo-700 shadow-lg overflow-y-auto transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out z-40">
    <div class="flex items-center justify-center py-5">
        <a href="/dashboard.php" class="flex items-center">
            <img src="/assets/logo.png" alt="Logo" class="h-10 w-auto" />
        </a>
    </div>
    
    <nav class="px-4 space-y-2">
        <a href="/dashboard.php" class="flex items-center px-4 py-2.5 text-white rounded-xl hover:bg-white/10 transition-colors <?= isActive('dashboard.php') ? 'bg-white/20 font-medium' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span>Dashboard</span>
        </a>
        
        <a href="/taskboard.php" class="flex items-center px-4 py-2.5 text-white rounded-xl hover:bg-white/10 transition-colors <?= isActive('taskboard.php') ? 'bg-white/20 font-medium' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            <span>Groups</span>
        </a>
        
        <a href="/havetopay.php" class="flex items-center px-4 py-2.5 text-white rounded-xl hover:bg-white/10 transition-colors <?= isActive('havetopay.php') ? 'bg-white/20 font-medium' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Purchases</span>
        </a>
        
        <a href="/settings.php" class="flex items-center px-4 py-2.5 text-white rounded-xl hover:bg-white/10 transition-colors <?= isActive('settings.php') ? 'bg-white/20 font-medium' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span>Settings</span>
        </a>
        
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
        <a href="/admin.php" class="flex items-center px-4 py-2.5 text-white rounded-xl hover:bg-white/10 transition-colors <?= isActive('admin.php') ? 'bg-white/20 font-medium' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <span>Admin</span>
        </a>
        <?php endif; ?>
    </nav>
    
    <div class="px-4 mt-6">
        <div class="border-t border-white/10 pt-4">
            <a href="/logout.php" class="flex items-center px-4 py-2.5 text-white rounded-xl hover:bg-white/10 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>Logout</span>
            </a>
        </div>
    </div>
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
