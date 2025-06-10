<?php
// Adjusted Navbar with proper paths for all files
require_once __DIR__ . '/../src/lib/auth.php';
require_once __DIR__ . '/../src/lib/utils.php';
$user = getUser();

// Define the navigation items
$navItems = [
    'index.php' => ['title' => 'Dashboard', 'icon' => 'fa-home'],
    'taskboard.php' => ['title' => 'Tasks', 'icon' => 'fa-tasks'],
    'inbox.php' => ['title' => 'Inbox', 'icon' => 'fa-inbox'],
    'calendar.php' => ['title' => 'Calendar', 'icon' => 'fa-calendar'],
    'havetopay.php' => ['title' => 'HaveToPay', 'icon' => 'fa-money-bill-wave'],
];

// Add admin-only nav items
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
    $navItems['admin.php'] = ['title' => 'Admin', 'icon' => 'fa-shield-alt'];
}

// Helper function to check if current page matches a specific page
function isActivePage($pageName) {
    $currentPage = basename($_SERVER['PHP_SELF']);
    return $currentPage == $pageName;
}
?>

<!-- Mobile Top Navbar -->
<nav class="md:hidden fixed top-0 left-0 right-0 bg-white shadow-md z-50 h-14">
    <div class="flex justify-between items-center px-4 h-full">
        <a href="index.php" class="flex items-center space-x-2">
            <i class="fas fa-vault text-blue-600"></i>
            <span class="font-bold text-gray-800">PrivateVault</span>
        </a>
        
        <div class="flex items-center space-x-4">
            <button id="mobileMenuBtn" class="text-gray-600 focus:outline-none">
                <i class="fas fa-bars text-lg"></i>
            </button>
        </div>
    </div>
    
    <!-- Mobile Menu (Hidden by default) -->
    <div id="mobileMenu" class="hidden bg-white shadow-md absolute left-0 right-0 top-14">
        <div class="flex flex-col py-2">
            <?php foreach ($navItems as $url => $item): ?>
                <a href="<?= $url ?>" class="flex items-center space-x-2 px-4 py-3 hover:bg-gray-100 <?= isActivePage($url) ? 'text-blue-600 bg-blue-50' : 'text-gray-700' ?>">
                    <i class="fas <?= $item['icon'] ?> w-5 text-center"></i>
                    <span><?= $item['title'] ?></span>
                </a>
            <?php endforeach; ?>
            
            <?php if (isset($user)): ?>
                <div class="border-t border-gray-200 mt-2 pt-2">
                    <a href="profile.php" class="flex items-center space-x-2 px-4 py-3 hover:bg-gray-100 <?= isActivePage('profile.php') ? 'text-blue-600 bg-blue-50' : 'text-gray-700' ?>">
                        <i class="fas fa-user w-5 text-center"></i>
                        <span>Profile</span>
                    </a>
                    <a href="settings.php" class="flex items-center space-x-2 px-4 py-3 hover:bg-gray-100 <?= isActivePage('settings.php') ? 'text-blue-600 bg-blue-50' : 'text-gray-700' ?>">
                        <i class="fas fa-cog w-5 text-center"></i>
                        <span>Settings</span>
                    </a>
                    <a href="logout.php" class="flex items-center space-x-2 px-4 py-3 hover:bg-gray-100 text-red-600">
                        <i class="fas fa-sign-out-alt w-5 text-center"></i>
                        <span>Logout</span>
                    </a>
                </div>
            <?php else: ?>
                <a href="login.php" class="flex items-center space-x-2 px-4 py-3 hover:bg-gray-100 <?= isActivePage('login.php') ? 'text-blue-600 bg-blue-50' : 'text-gray-700' ?>">
                    <i class="fas fa-sign-in-alt w-5 text-center"></i>
                    <span>Login</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Desktop Sidebar Navigation -->
<aside class="hidden md:flex md:flex-col fixed left-0 top-0 bottom-0 w-64 bg-white shadow-md z-40">
    <!-- Logo -->
    <div class="p-4 border-b border-gray-200">
        <a href="index.php" class="flex items-center space-x-2">
            <i class="fas fa-vault text-blue-600 text-xl"></i>
            <span class="font-bold text-gray-800 text-lg">PrivateVault</span>
        </a>
    </div>
    
    <!-- User Profile Summary -->
    <?php if (isset($user)): ?>
    <div class="p-4 border-b border-gray-200">
        <div class="flex items-center space-x-3">
            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                <?= strtoupper(substr($user['username'] ?? 'U', 0, 1)) ?>
            </div>
            <div>
                <div class="font-medium text-gray-800"><?= htmlspecialchars($user['username'] ?? 'User') ?></div>
                <div class="text-xs text-gray-500"><?= htmlspecialchars($user['email'] ?? '') ?></div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Navigation Links -->
    <nav class="flex-1 overflow-y-auto py-4">
        <ul>
            <?php foreach ($navItems as $url => $item): ?>
                <li>
                    <a href="<?= $url ?>" class="flex items-center space-x-3 px-6 py-3 hover:bg-gray-100 <?= isActivePage($url) ? 'text-blue-600 bg-blue-50 border-r-4 border-blue-600' : 'text-gray-700' ?>">
                        <i class="fas <?= $item['icon'] ?> w-5 text-center"></i>
                        <span><?= $item['title'] ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    
    <!-- Bottom Actions -->
    <?php if (isset($user)): ?>
    <div class="p-4 border-t border-gray-200">
        <div class="grid grid-cols-3 gap-2">
            <a href="profile.php" class="flex flex-col items-center p-2 text-gray-700 hover:bg-gray-100 rounded">
                <i class="fas fa-user text-gray-600 mb-1"></i>
                <span class="text-xs">Profile</span>
            </a>
            <a href="settings.php" class="flex flex-col items-center p-2 text-gray-700 hover:bg-gray-100 rounded">
                <i class="fas fa-cog text-gray-600 mb-1"></i>
                <span class="text-xs">Settings</span>
            </a>
            <a href="logout.php" class="flex flex-col items-center p-2 text-red-600 hover:bg-red-50 rounded">
                <i class="fas fa-sign-out-alt mb-1"></i>
                <span class="text-xs">Logout</span>
            </a>
        </div>
    </div>
    <?php else: ?>
    <div class="p-4 border-t border-gray-200">
        <a href="login.php" class="flex items-center justify-center space-x-2 p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-sign-in-alt"></i>
            <span>Login</span>
        </a>
    </div>
    <?php endif; ?>
</aside>

<script>
    // Mobile menu toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
        const menuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        
        if (menuBtn && mobileMenu) {
            menuBtn.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
            
            // Close menu when clicking outside
            document.addEventListener('click', function(event) {
                const isClickInside = menuBtn.contains(event.target) || mobileMenu.contains(event.target);
                
                if (!isClickInside && !mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                }
            });
        }
    });
</script>
