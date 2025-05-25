<?php
/**
 * Modern Bootstrap Navbar component for PrivateVault
 */

// Function to check if the current page matches a specific page
function isCurrentPage($pageName) {
    $currentPage = basename($_SERVER['PHP_SELF']);
    return $currentPage == $pageName;
}
?>

<!-- Modern Fixed Navbar -->
<nav class="navbar navbar-expand-lg fixed-top" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); backdrop-filter: blur(20px);">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center text-white fw-bold" href="index.php">
            <i class="fas fa-vault me-2 fs-4"></i>
            <span class="fs-5">PrivateVault</span>
        </a>
        
        <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" 
                data-bs-target="#navbarContent" aria-controls="navbarContent" 
                aria-expanded="false" aria-label="Toggle navigation"
                style="box-shadow: none;">
            <i class="fas fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item mx-2">
                    <a class="nav-link text-white fw-medium px-3 py-2 rounded-pill transition-all 
                       <?php echo isCurrentPage('index.php') ? 'bg-white bg-opacity-20' : ''; ?>" 
                       href="index.php"
                       style="transition: all 0.3s ease;"
                       onmouseover="this.style.backgroundColor='rgba(255,255,255,0.2)'"
                       onmouseout="this.style.backgroundColor='<?php echo isCurrentPage('index.php') ? 'rgba(255,255,255,0.2)' : 'transparent'; ?>'">
                        <i class="fas fa-home me-2"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link text-white fw-medium px-3 py-2 rounded-pill
                       <?php echo isCurrentPage('taskboard.php') ? 'bg-white bg-opacity-20' : ''; ?>" 
                       href="taskboard.php"
                       style="transition: all 0.3s ease;"
                       onmouseover="this.style.backgroundColor='rgba(255,255,255,0.2)'"
                       onmouseout="this.style.backgroundColor='<?php echo isCurrentPage('taskboard.php') ? 'rgba(255,255,255,0.2)' : 'transparent'; ?>'">
                        <i class="fas fa-tasks me-2"></i>Taskboard
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link text-white fw-medium px-3 py-2 rounded-pill
                       <?php echo isCurrentPage('havetopay.php') ? 'bg-white bg-opacity-20' : ''; ?>" 
                       href="havetopay.php"
                       style="transition: all 0.3s ease;"
                       onmouseover="this.style.backgroundColor='rgba(255,255,255,0.2)'"
                       onmouseout="this.style.backgroundColor='<?php echo isCurrentPage('havetopay.php') ? 'rgba(255,255,255,0.2)' : 'transparent'; ?>'">
                        <i class="fas fa-money-bill-wave me-2"></i>HaveToPay
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link text-white fw-medium px-3 py-2 rounded-pill
                       <?php echo isCurrentPage('calendar.php') ? 'bg-white bg-opacity-20' : ''; ?>" 
                       href="calendar.php"
                       style="transition: all 0.3s ease;"
                       onmouseover="this.style.backgroundColor='rgba(255,255,255,0.2)'"
                       onmouseout="this.style.backgroundColor='<?php echo isCurrentPage('calendar.php') ? 'rgba(255,255,255,0.2)' : 'transparent'; ?>'">
                        <i class="fas fa-calendar-alt me-2"></i>Kalender
                    </a>
                </li>
            </ul>
            
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white fw-medium d-flex align-items-center px-3 py-2 rounded-pill" 
                       href="#" id="userDropdown" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false"
                       style="transition: all 0.3s ease; background-color: rgba(255,255,255,0.1);">
                        <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center me-2"
                             style="width: 32px; height: 32px; font-size: 14px; font-weight: 700;">
                            <?php echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?>
                        </div>
                        <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2" 
                        style="background: rgba(255,255,255,0.95); backdrop-filter: blur(20px);">
                        <li>
                            <a class="dropdown-item rounded-2 mx-2 my-1 py-2" href="profile.php">
                                <i class="fas fa-id-card me-2 text-primary"></i>Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-2 mx-2 my-1 py-2" href="settings.php">
                                <i class="fas fa-cog me-2 text-secondary"></i>Settings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider mx-2"></li>
                        <li>
                            <a class="dropdown-item rounded-2 mx-2 my-1 py-2 text-danger" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link text-white fw-medium px-4 py-2 rounded-pill border border-white border-opacity-30
                       <?php echo isCurrentPage('login.php') ? 'bg-white text-dark' : ''; ?>" 
                       href="login.php"
                       style="transition: all 0.3s ease;"
                       onmouseover="this.style.backgroundColor='rgba(255,255,255,0.9)'; this.style.color='#333'"
                       onmouseout="this.style.backgroundColor='transparent'; this.style.color='white'">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<style>
.dropdown-item:hover {
    background-color: rgba(102, 126, 234, 0.1) !important;
    transform: translateX(5px);
    transition: all 0.3s ease;
}

body {
    padding-top: 80px; /* Account for fixed navbar */
}

@media (max-width: 991px) {
    .navbar-collapse {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(20px);
        border-radius: 15px;
        margin-top: 1rem;
        padding: 1rem;
    }
}
</style>
