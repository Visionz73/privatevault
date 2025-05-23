<?php
// Adjusted Navbar with proper paths for all files
require_once __DIR__ . '/../src/lib/auth.php';
$user = getUser();

// Only groups.php is in the admin directory
$isAdminPage = strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false;

// Determine if we're on the havetopay page to add specific styling
$isHaveToPayPage = basename($_SERVER['PHP_SELF']) === 'havetopay.php' || 
                   basename($_SERVER['PHP_SELF']) === 'havetopay_add.php' ||
                   basename($_SERVER['PHP_SELF']) === 'havetopay_detail.php';
?>
<style>
  /* Desktop sidebar styling */
  @media (min-width: 769px) {
    nav#sidebar {
      position: fixed;
      left: 0;
      top: 0;
      bottom: 0;
      width: 16rem; /* w-64 */
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
      z-index: 50;
    }
    .mobile-menu { display: none; }
    .sidebar-content { display: block; }
  }

  /* Mobile topbar styling - Fixed to be fully opaque */
  @media (max-width: 768px) {
    nav#sidebar {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 3.5rem; /* fixed top bar height */
      background: #ffffff; /* Fully opaque white background */
      border-bottom: 1px solid #e5e7eb;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      z-index: 50;
    }
    .mobile-menu { 
      display: flex; 
      align-items: center;
      width: 100%;
      height: 100%;
    }
    .sidebar-content { 
      display: none;
      position: fixed;
      top: 3.5rem;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      z-index: 49;
      padding: 1rem;
      overflow-y: auto; /* Allow scrolling if many menu items */
    }
    .sidebar-content.active {
      display: block;
    }
    /* Improved mobile menu item styling */
    .sidebar-content ul li {
      margin-bottom: 0.75rem;
    }
    .sidebar-content ul li a {
      display: block;
      padding: 0.75rem 1rem;
      border-radius: 0.75rem;
      transition: all 0.2s ease;
      color: rgba(255, 255, 255, 0.8);
    }
    .sidebar-content ul li a:hover {
      background-color: rgba(255, 255, 255, 0.1);
      color: white;
    }
  }
  
  /* Fix for HaveToPay pages - ensure content appears correctly */
  .haveToPay-layout {
    padding-top: 0 !important;
  }
  
  @media (min-width: 769px) {
    body.haveToPay-layout main, 
    body.haveToPay-layout .content-container {
      margin-left: 16rem !important;
      width: calc(100% - 16rem) !important;
    }
  }

  /* Modern Logout Button */
  .logout-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 1.5rem 1rem;
    padding: 0.75rem 1.5rem;
    background-color: rgba(255, 255, 255, 0.15);
    color: white;
    border-radius: 0.75rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.2);
  }

  .logout-btn:hover {
    background-color: rgba(255, 255, 255, 0.25);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    color: white;
    text-decoration: none;
  }

  .logout-btn i {
    margin-right: 0.75rem;
    font-size: 1.1rem;
  }

  /* Nav links styling */
  .nav-link-modern {
    display: flex;
    align-items: center;
    color: rgba(255, 255, 255, 0.8);
    transition: all 0.2s ease;
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
  }

  .nav-link-modern:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: white;
    transform: translateX(4px);
  }

  .nav-link-modern.active {
    background-color: rgba(255, 255, 255, 0.15);
    color: white;
    font-weight: 500;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }

  .nav-link-modern i, .nav-link-modern svg {
    margin-right: 0.75rem;
  }
</style>

<!-- Add the haveToPay-layout class to body if on HaveToPay page -->
<script>
  if (<?php echo $isHaveToPayPage ? 'true' : 'false'; ?>) {
    document.body.classList.add('haveToPay-layout');
  }
</script>

<nav id="sidebar">
  <!-- Mobile top bar -->
  <div class="mobile-menu">
    <button id="mobileToggleBtn" class="p-2 focus:outline-none">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" 
           viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
              d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>
    <a href="/dashboard.php" class="flex items-center">
      <img src="/assets/logo.png" alt="Logo" class="h-12 w-auto" />
      
    </a>
  </div>
  <!-- Desktop: sidebar content -->
  <div class="sidebar-content flex flex-col h-full">
    <div class="flex-1 overflow-y-auto mt-2">
      <button id="toggleBtn" class="p-4 focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" 
             viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
      <!-- Centered logo -->
      <div class="flex justify-center mb-6">
        <a href="/dashboard.php" class="flex items-center">
          <img src="/assets/logo.png" alt="Logo" class="h-24 w-auto" />
        </a>
      </div>
      <ul class="flex flex-col space-y-4 px-2">
        <?php
        $links = [
          ['href'=>'dashboard.php',   'icon'=>'<path d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m-4 0h8" />', 'label'=>'Dashboard'],
          ['href'=>'upload.php',      'icon'=>'<path d="M12 4v16m8-8H4" />', 'label'=>'Upload'],
          ['href'=>'profile.php',     'icon'=>'<path d="M5.121 17.804A9 9 0 0112 15a9 9 0 016.879 2.804M12 12a4 4 0 100-8 4 4 0 000 8z" />', 'label'=>'Profil'],
          ['href'=>'inbox.php',       'icon'=>'<path d="M9 12h6m2 0a8 8 0 11-16 0 8 8 0 0116 0z" />', 'label'=>'MyTask'],
          ['href'=>'create_task.php', 'icon'=>'<path d="M4 4l16 16M4 20L20 4" />', 'label'=>'Create Task'],
          ['href'=>'taskboard.php',   'icon'=>'<path d="M4 6h16M4 12h16M4 18h16" />', 'label'=>'Kanban'],
          ['href'=>'havetopay.php',   'icon'=>'<path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />', 'label'=>'HaveToPay'],
        ];
        foreach ($links as $l): 
          $isActive = basename($_SERVER['PHP_SELF']) === $l['href'];
        ?>
          <li>
            <a href="/<?= $l['href'] ?>" class="nav-link-modern <?= $isActive ? 'active' : '' ?>">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
                   viewBox="0 0 24 24" stroke="currentColor">
                <?= $l['icon'] ?>
              </svg>
              <span><?= $l['label'] ?></span>
            </a>
          </li>
        <?php endforeach; ?>
        <?php if ($user && isset($user['role']) && $user['role'] === 'admin'): ?>
          <li>
            <a href="/admin.php" class="nav-link-modern">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
                   viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
              </svg>
              <span>Admin Dashboard</span>
            </a>
          </li>
          <li>
            <a href="/admin/groups.php" class="nav-link-modern">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
                   viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <span>Manage Groups</span>
            </a>
          </li>
        <?php endif; ?>
      </ul>

      <!-- Added Logout Button -->
      <?php if (isset($_SESSION['user_id'])): ?>
      <a href="/logout.php" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- Mobile navigation toggle script -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const mobileToggleBtn = document.getElementById('mobileToggleBtn');
    const sidebar = document.querySelector('.sidebar-content');
    
    if (mobileToggleBtn && sidebar) {
      mobileToggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('active');
      });
    }
  });
</script>
