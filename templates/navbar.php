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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'PrivateVault' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navbar with purple gradient -->
    <header class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-md">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <!-- Logo and Brand -->
                <a href="index.php" class="flex items-center space-x-3">
                    <img src="logo.png" alt="PrivateVault Logo" class="h-10 w-auto">
                    <span class="font-semibold text-xl">PrivateVault</span>
                </a>
                
                <!-- Navigation Links - Desktop -->
                <nav class="hidden md:flex space-x-8 items-center">
                    <a href="index.php" class="font-medium hover:text-purple-200 transition-colors <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'border-b-2 border-white' : '' ?>">
                        Dashboard
                    </a>
                    <a href="havetopay.php" class="font-medium hover:text-purple-200 transition-colors <?= basename($_SERVER['PHP_SELF']) === 'havetopay.php' ? 'border-b-2 border-white' : '' ?>">
                        HaveToPay
                    </a>
                    <a href="profile.php" class="font-medium hover:text-purple-200 transition-colors <?= basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'border-b-2 border-white' : '' ?>">
                        Profile
                    </a>
                    <a href="logout.php" class="ml-4 bg-white/20 hover:bg-white/30 rounded-lg px-4 py-2 font-medium transition-colors">
                        Log Out
                    </a>
                </nav>
                
                <!-- Mobile Menu Button -->
                <button type="button" class="md:hidden text-white focus:outline-none" id="mobileMenuButton">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
            
            <!-- Mobile Navigation Menu -->
            <div class="md:hidden hidden" id="mobileMenu">
                <div class="px-2 pt-2 pb-4 space-y-1">
                    <a href="index.php" class="block px-3 py-2 rounded-md font-medium hover:bg-white/10 transition-colors">
                        Dashboard
                    </a>
                    <a href="havetopay.php" class="block px-3 py-2 rounded-md font-medium hover:bg-white/10 transition-colors">
                        HaveToPay
                    </a>
                    <a href="profile.php" class="block px-3 py-2 rounded-md font-medium hover:bg-white/10 transition-colors">
                        Profile
                    </a>
                    <a href="logout.php" class="block px-3 py-2 rounded-md font-medium hover:bg-white/10 transition-colors">
                        Log Out
                    </a>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Page Content Container -->
    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page content will be injected here -->

<script>
    // Mobile menu toggle
    document.getElementById('mobileMenuButton').addEventListener('click', function() {
        const menu = document.getElementById('mobileMenu');
        menu.classList.toggle('hidden');
    });
</script>
