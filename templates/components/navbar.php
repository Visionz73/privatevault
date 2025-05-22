<?php
/**
 * Navbar component for PrivateVault
 * Include this file in your templates to display the navigation bar
 */

// Function to check if the current page matches a specific page
function isCurrentPage($pageName) {
    $currentPage = basename($_SERVER['PHP_SELF']);
    return $currentPage == $pageName;
}

// Define the navigation items
$navItems = [
    'index.php' => ['title' => 'Dashboard', 'icon' => 'fas fa-home'],
    'taskboard.php' => ['title' => 'Tasks', 'icon' => 'fas fa-tasks'],
    'havetopay.php' => ['title' => 'HaveToPay', 'icon' => 'fas fa-money-bill-wave'],
];

// Optional: Add admin-only nav items
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
    $navItems['admin.php'] = ['title' => 'Admin', 'icon' => 'fas fa-shield-alt'];
}
?>

<!-- Fixed Navbar -->
<nav class="navbar navbar-expand-lg fixed-top navbar-light bg-white">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-vault me-2"></i>PrivateVault
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" 
                aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <?php if (isset($_SESSION['user_id'])): ?>
                <ul class="navbar-nav ms-auto">
                    <?php foreach ($navItems as $url => $item): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo isCurrentPage($url) ? 'active' : ''; ?>" href="<?php echo $url; ?>">
                            <i class="<?php echo $item['icon']; ?> me-1"></i> <?php echo $item['title']; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-id-card me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            <?php else: ?>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo isCurrentPage('login.php') ? 'active' : ''; ?>" href="login.php">
                            <i class="fas fa-sign-in-alt me-1"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo isCurrentPage('register.php') ? 'active' : ''; ?>" href="register.php">
                            <i class="fas fa-user-plus me-1"></i> Register
                        </a>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>
