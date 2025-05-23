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

// Generate user initials
function getUserInitials($user) {
    if (!$user || !isset($user['username'])) return 'U';
    $names = explode(' ', trim($user['username']));
    if (count($names) >= 2) {
        return strtoupper(substr($names[0], 0, 1) . substr($names[1], 0, 1));
    }
    return strtoupper(substr($user['username'], 0, 2));
}
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
    font-weight: 600;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid rgba(255,255,255,0.3);
    position: relative;
  }
  .profile-avatar:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
  }

  /* Profile dropdown */
  .profile-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    margin-top: 0.5rem;
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    min-width: 200px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    z-index: 100;
  }
  .profile-dropdown.active {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
  }
  .profile-dropdown a {
    display: block;
    padding: 0.75rem 1rem;
    color: #374151;
    text-decoration: none;
    transition: all 0.2s ease;
    border-radius: 0.5rem;
    margin: 0.25rem;
  }
  .profile-dropdown a:hover {
    background: #f3f4f6;
    color: #667eea;
  }
  .profile-dropdown a:first-child {
    margin-top: 0.5rem;
  }
  .profile-dropdown a:last-child {
    margin-bottom: 0.5rem;
    color: #dc2626;
  }

  /* Modern navigation links */
  .nav-link-modern {
    color: rgba(255,255,255,0.9) !important;
    padding: 0.75rem 1rem !important;
    border-radius: 0.75rem !important;
    transition: all 0.3s ease !important;
    backdrop-filter: blur(10px);
    margin-bottom: 0.25rem;
  }
  .nav-link-modern:hover {
    background: rgba(255,255,255,0.2) !important;
    color: white !important;
    transform: translateX(5px);
  }
  .nav-link-modern.active {
    background: rgba(255,255,255,0.25) !important;
    color: white !important;
    font-weight: 600;
  }

  /* Fix for HaveToPay pages */
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
    <button id="mobileToggleBtn" class="menu-btn">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" 
           viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
              d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>
    <a href="/dashboard.php" class="logo-container">
      <img src="/assets/logo.png" alt="Logo" class="h-8 w-auto mr-2" />
      PrivateVault
    </a>
    <!-- Mobile Profile Avatar -->
    <?php if ($user): ?>
    <div class="relative">
      <div class="profile-avatar" onclick="toggleProfileDropdown()">
        <?= getUserInitials($user) ?>
      </div>
      <div id="profileDropdown" class="profile-dropdown">
        <a href="/profile.php">
          <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
          </svg>
          Profil
        </a>
        <a href="/settings.php">
          <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
          Einstellungen
        </a>
        <a href="/logout.php">
          <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
          </svg>
          Abmelden
        </a>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <!-- Desktop: sidebar content -->
  <div class="sidebar-content flex flex-col h-full">
    <div class="flex-1 overflow-y-auto mt-2">
      <button id="toggleBtn" class="menu-btn ml-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" 
             viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
      
      <!-- Centered logo -->
      <div class="flex justify-center mb-6 mt-4">
        <a href="/dashboard.php" class="logo-container flex-col">
          <img src="/assets/logo.png" alt="Logo" class="h-16 w-auto mb-2" />
          <span class="text-sm font-semibold">PrivateVault</span>
        </a>
      </div>

      <!-- Desktop Profile Section -->
      <?php if ($user): ?>
      <div class="flex flex-col items-center mb-6 px-4">
        <div class="profile-avatar mb-3" onclick="toggleProfileDropdown()">
          <?= getUserInitials($user) ?>
        </div>
        <div class="text-center relative">
          <p class="text-white font-medium"><?= htmlspecialchars($user['username']) ?></p>
          <div id="profileDropdownDesktop" class="profile-dropdown">
            <a href="/profile.php">
              <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
              Profil
            </a>
            <a href="/settings.php">
              <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
              Einstellungen
            </a>
            <a href="/logout.php">
              <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
              </svg>
              Abmelden
            </a>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <ul class="flex flex-col space-y-2 px-2">
        <?php
        $links = [
          ['href'=>'dashboard.php',   'icon'=>'<path d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m-4 0h8" />', 'label'=>'Dashboard'],
          ['href'=>'upload.php',      'icon'=>'<path d="M12 4v16m8-8H4" />', 'label'=>'Upload'],
          ['href'=>'inbox.php',       'icon'=>'<path d="M9 12h6m2 0a8 8 0 11-16 0 8 8 0 0116 0z" />', 'label'=>'MyTask'],
          ['href'=>'create_task.php', 'icon'=>'<path d="M4 4l16 16M4 20L20 4" />', 'label'=>'Create Task'],
          ['href'=>'taskboard.php',   'icon'=>'<path d="M4 6h16M4 12h16M4 18h16" />', 'label'=>'Kanban'],
          ['href'=>'havetopay.php',   'icon'=>'<path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />', 'label'=>'HaveToPay'],
        ];
        foreach ($links as $l): 
          $isActive = basename($_SERVER['PHP_SELF']) === $l['href'];
        ?>
          <li>
            <a href="/<?= $l['href'] ?>" class="nav-link-modern flex items-center w-full <?= $isActive ? 'active' : '' ?>">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
                   viewBox="0 0 24 24" stroke="currentColor">
                <?= $l['icon'] ?>
              </svg>
              <span class="ml-3"><?= $l['label'] ?></span>
            </a>
          </li>
        <?php endforeach; ?>
        <?php if ($user && isset($user['role']) && $user['role'] === 'admin'): ?>
          <li>
            <a href="/admin.php" class="nav-link-modern flex items-center w-full">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
                   viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
              </svg>
              <span class="ml-3">Admin Dashboard</span>
            </a>
          </li>
          <li>
            <a href="/admin/groups.php" class="nav-link-modern flex items-center w-full">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
                   viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <span class="ml-3">Manage Groups</span>
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Enhanced navigation scripts -->
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

  function toggleProfileDropdown() {
    const dropdown = document.getElementById('profileDropdown') || document.getElementById('profileDropdownDesktop');
    if (dropdown) {
      dropdown.classList.toggle('active');
    }
  }

  // Close dropdown when clicking outside
  document.addEventListener('click', function(event) {
    const profileAvatar = document.querySelector('.profile-avatar');
    const dropdown = document.querySelector('.profile-dropdown.active');
    
    if (dropdown && !profileAvatar.contains(event.target) && !dropdown.contains(event.target)) {
      dropdown.classList.remove('active');
    }
  });
</script>
