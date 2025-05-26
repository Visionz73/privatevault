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
<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="/public/assets/logo.png" alt="PrivateVault Logo" height="32" class="me-2" />
            <span class="fw-bold">PrivateVault</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                data-bs-target="#navbarContent" aria-controls="navbarContent" 
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo isCurrentPage('index.php') ? 'active' : ''; ?>" href="index.php">
                        <i class="fas fa-home me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isCurrentPage('taskboard.php') ? 'active' : ''; ?>" href="taskboard.php">
                        <i class="fas fa-tasks me-1"></i> Taskboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isCurrentPage('havetopay.php') ? 'active' : ''; ?>" href="havetopay.php">
                        <i class="fas fa-money-bill-wave me-1"></i> HaveToPay
                    </a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i>
                        <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-id-card me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo isCurrentPage('login.php') ? 'active' : ''; ?>" href="login.php">
                        <i class="fas fa-sign-in-alt me-1"></i> Login
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
