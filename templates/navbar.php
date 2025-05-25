<?php
// Adjusted Navbar with proper paths for all files
require_once __DIR__ . '/../src/lib/auth.php';
require_once __DIR__ . '/../src/lib/utils.php';
$user = getUser();

// Only groups.php is in the admin directory
$isAdminPage = strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false;

// Determine if we're on the havetopay page to add specific styling
$isHaveToPayPage = basename($_SERVER['PHP_SELF']) === 'havetopay.php' || 
                   basename($_SERVER['PHP_SELF']) === 'havetopay_add.php' ||
                   basename($_SERVER['PHP_SELF']) === 'havetopay_detail.php';
?>

<style>
  /* Modern gradient navbar styling */
  @media (min-width: 769px) {
    nav#sidebar {
      position: fixed;
      left: 0;
      top: 0;
      bottom: 0;
      width: 16rem; /* w-64 */
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      backdrop-filter: blur(15px);
      border-right: 1px solid rgba(255,255,255,0.2);
      box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
      z-index: 50;
    }
    .mobile-menu { display: none; }
    .sidebar-content { display: block; }
  }

  /* Mobile modern gradient styling */
  @media (max-width: 768px) {
    nav#sidebar {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 4rem; /* slightly taller for modern look */
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-bottom: 1px solid rgba(255,255,255,0.2);
      box-shadow: 0 4px 15px rgba(31, 38, 135, 0.37);
      z-index: 50;
    }
    .mobile-menu { 
      display: flex; 
      align-items: center;
      justify-content: space-between;
      width: 100%;
      height: 100%;
      padding: 0 1rem;
    }
    .sidebar-content { 
      display: none;
      position: fixed;
      top: 4rem;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      z-index: 49;
      padding: 1rem;
      overflow-y: auto;
    }
    .sidebar-content.active {
      display: block;
    }
    .sidebar-content ul li {
      margin-bottom: 0.75rem;
    }
    .sidebar-content ul li a {
      display: block;
      padding: 0.75rem 1rem;
      border-radius: 0.75rem;
      transition: all 0.3s ease;
      color: rgba(255,255,255,0.9);
      backdrop-filter: blur(10px);
    }
    .sidebar-content ul li a:hover {
      background-color: rgba(255,255,255,0.2);
      color: white;
      transform: translateX(5px);
    }
  }

  /* Modern menu button styling */
  .menu-btn {
    background: rgba(255,255,255,0.2);
    border: none;
    border-radius: 0.5rem;
    padding: 0.5rem;
    color: white;
    transition: all 0.3s ease;
  }
  .menu-btn:hover {
    background: rgba(255,255,255,0.3);
    transform: scale(1.05);
  }

  /* Modern logo styling */
  .logo-container {
    display: flex;
    align-items: center;
    color: white;
    font-weight: 700;
    font-size: 1.25rem;
    text-decoration: none;
  }

  /* Profile avatar styling */
  .profile-avatar {
    width: 2.5rem;
    height: 2.5rem;
    background: linear-gradient(135deg, #ff6b6b, #feca57);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1rem;
  }

  /* Smooth transitions for all interactive elements */
  nav#sidebar a,
  nav#sidebar button {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }

  /* Modern glassmorphism effect */
  nav#sidebar {
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
  }

  /* Enhanced focus states for accessibility */
  nav#sidebar a:focus,
  nav#sidebar button:focus {
    outline: 2px solid rgba(255, 255, 255, 0.5);
    outline-offset: 2px;
  }
</style>

<!-- Navigation Sidebar -->
<nav id="sidebar" class="sidebar">
  <!-- Mobile Header -->
  <div class="mobile-menu">
    <a href="index.php" class="logo-container">
      <i class="fas fa-vault me-2"></i>
      PrivateVault
    </a>
    <button class="menu-btn" onclick="toggleSidebar()">
      <i class="fas fa-bars"></i>
    </button>
  </div>

  <!-- Sidebar Content -->
  <div class="sidebar-content">
    <!-- Logo Section -->
    <div class="p-4 border-b border-white/20">
      <a href="index.php" class="logo-container">
        <i class="fas fa-vault me-2 text-2xl"></i>
        <span class="text-xl">PrivateVault</span>
      </a>
    </div>

    <!-- Navigation Menu -->
    <div class="p-4">
      <ul class="space-y-2">
        <li>
          <a href="index.php" class="flex items-center p-3 rounded-xl text-white/90 hover:bg-white/20 hover:text-white transition-all duration-300 group">
            <i class="fas fa-home text-lg me-3 group-hover:scale-110 transition-transform"></i>
            <span class="font-medium">Dashboard</span>
          </a>
        </li>
        <li>
          <a href="taskboard.php" class="flex items-center p-3 rounded-xl text-white/90 hover:bg-white/20 hover:text-white transition-all duration-300 group">
            <i class="fas fa-tasks text-lg me-3 group-hover:scale-110 transition-transform"></i>
            <span class="font-medium">Taskboard</span>
          </a>
        </li>
        <li>
          <a href="havetopay.php" class="flex items-center p-3 rounded-xl text-white/90 hover:bg-white/20 hover:text-white transition-all duration-300 group">
            <i class="fas fa-money-bill-wave text-lg me-3 group-hover:scale-110 transition-transform"></i>
            <span class="font-medium">HaveToPay</span>
          </a>
        </li>
        <li>
          <a href="calendar.php" class="flex items-center p-3 rounded-xl text-white/90 hover:bg-white/20 hover:text-white transition-all duration-300 group">
            <i class="fas fa-calendar-alt text-lg me-3 group-hover:scale-110 transition-transform"></i>
            <span class="font-medium">Kalender</span>
          </a>
        </li>
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
        <li>
          <a href="admin/groups.php" class="flex items-center p-3 rounded-xl text-white/90 hover:bg-white/20 hover:text-white transition-all duration-300 group">
            <i class="fas fa-shield-alt text-lg me-3 group-hover:scale-110 transition-transform"></i>
            <span class="font-medium">Admin</span>
          </a>
        </li>
        <?php endif; ?>
      </ul>
    </div>

    <!-- User Profile Section -->
    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="mt-auto p-4 border-t border-white/20">
      <div class="flex items-center p-3 rounded-xl bg-white/10 backdrop-blur-sm">
        <div class="profile-avatar me-3">
          <?php echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?>
        </div>
        <div class="flex-1">
          <div class="text-white font-medium text-sm">
            <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>
          </div>
          <div class="text-white/70 text-xs">Online</div>
        </div>
        <div class="relative">
          <button class="text-white/70 hover:text-white p-2" onclick="toggleUserMenu()" id="userMenuBtn">
            <i class="fas fa-ellipsis-v"></i>
          </button>
          <div id="userMenu" class="hidden absolute right-0 bottom-full mb-2 bg-white rounded-lg shadow-xl py-2 w-48 z-50">
            <a href="profile.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition-colors">
              <i class="fas fa-user me-2"></i>Profile
            </a>
            <a href="settings.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition-colors">
              <i class="fas fa-cog me-2"></i>Settings
            </a>
            <hr class="my-1">
            <a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-red-50 transition-colors">
              <i class="fas fa-sign-out-alt me-2"></i>Logout
            </a>
          </div>
        </div>
      </div>
    </div>
    <?php else: ?>
    <div class="mt-auto p-4 border-t border-white/20">
      <a href="login.php" class="flex items-center justify-center p-3 rounded-xl bg-white/20 text-white font-medium hover:bg-white/30 transition-all duration-300">
        <i class="fas fa-sign-in-alt me-2"></i>
        Login
      </a>
    </div>
    <?php endif; ?>
  </div>
</nav>

<script>
function toggleSidebar() {
  const sidebarContent = document.querySelector('.sidebar-content');
  sidebarContent.classList.toggle('active');
}

function toggleUserMenu() {
  const userMenu = document.getElementById('userMenu');
  userMenu.classList.toggle('hidden');
}

// Close user menu when clicking outside
document.addEventListener('click', function(e) {
  const userMenu = document.getElementById('userMenu');
  const userMenuBtn = document.getElementById('userMenuBtn');
  
  if (!userMenuBtn.contains(e.target) && !userMenu.contains(e.target)) {
    userMenu.classList.add('hidden');
  }
});

// Close mobile menu when clicking outside
document.addEventListener('click', function(e) {
  const sidebar = document.getElementById('sidebar');
  const sidebarContent = document.querySelector('.sidebar-content');
  
  if (window.innerWidth <= 768 && !sidebar.contains(e.target)) {
    sidebarContent.classList.remove('active');
  }
});
</script>
