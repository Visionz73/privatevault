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
  /* Modern dark gradient navbar styling */
  @media (min-width: 769px) {
    nav#sidebar {
      position: fixed;
      left: 0;
      top: 0;
      bottom: 0;
      width: 16rem; /* w-64 */
      background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
      border-right: 1px solid rgba(255,255,255,0.1);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      z-index: 50;
    }
    .mobile-menu { display: none; }
    .sidebar-content { display: block; }
  }

  /* Mobile dark gradient styling */
  @media (max-width: 768px) {
    nav#sidebar {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 4rem;
      background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
      border-bottom: 1px solid rgba(255,255,255,0.1);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
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
      background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
      backdrop-filter: saturate(180%) blur(20px);
      z-index: 49;
      padding: 1rem;
      overflow-y: auto;
    }
    .sidebar-content.active {
      display: block;
    }
  }

  /* Logo styling for dark background */
  .logo-container {
    display: flex;
    align-items: center;
    color: white;
    font-weight: 700;
    font-size: 1.25rem;
    text-decoration: none;
  }

  /* Rounded container for navigation items */
  .nav-container {
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 1rem;
    padding: 1rem;
    margin: 1rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
  }

  /* Modern navigation links */
  .nav-link-modern {
    color: rgba(255, 255, 255, 0.9) !important;
    padding: 0.75rem 1rem !important;
    border-radius: 0.5rem !important;
    transition: all 0.3s ease !important;
    margin-bottom: 0.25rem;
    text-decoration: none;
    display: flex;
    align-items: center;
  }
  .nav-link-modern:hover {
    background: rgba(255, 255, 255, 0.15) !important;
    color: white !important;
    transform: translateX(3px);
  }
  .nav-link-modern.active {
    background: rgba(255, 255, 255, 0.2) !important;
    color: white !important;
    font-weight: 600;
  }

  .nav-link-modern svg {
    margin-right: 0.75rem;
    width: 1.25rem;
    height: 1.25rem;
    color: rgba(255, 255, 255, 0.7);
  }

  /* User Banner Styles for dark theme */
  .user-banner {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding: 1rem;
    margin: 0 1rem 1rem;
  }
  .user-banner button {
    width: 100%;
    display: flex;
    align-items: center;
    padding: 1rem;
    border-radius: 1rem;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    cursor: pointer;
  }
  .user-banner button:hover {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.08) 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    border-color: rgba(255, 255, 255, 0.3);
  }
  .user-banner .user-info {
    flex: 1;
    text-align: left;
    margin-left: 0.75rem;
    margin-right: 0.75rem;
  }
  .user-banner .user-info .user-name {
    color: white;
    font-weight: 600;
    font-size: 0.95rem;
    margin-bottom: 0.125rem;
  }
  .user-banner .user-info .user-role {
    color: rgba(255, 255, 255, 0.75);
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .user-banner svg {
    color: rgba(255, 255, 255, 0.6);
    transition: all 0.3s ease;
  }
  .user-banner button:hover svg {
    color: rgba(255, 255, 255, 0.9);
    transform: rotate(180deg);
  }

  /* Profile avatar styling for dark theme */
  .profile-avatar {
    width: 2.5rem;
    height: 2.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1rem;
    border: 2px solid rgba(255,255,255,0.3);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
  }
  .user-banner button:hover .profile-avatar {
    transform: scale(1.05);
    border-color: rgba(255,255,255,0.5);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
  }

  /* Mobile header styling */
  .mobile-header-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 0 1rem;
  }

  .mobile-toggle-btn {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    border-radius: 0.5rem;
    padding: 0.5rem;
    color: white;
    transition: all 0.3s ease;
  }
  .mobile-toggle-btn:hover {
    background: rgba(255, 255, 255, 0.2);
  }

  /* Profile Modal dark theme adjustments */
  .profile-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(5px);
    z-index: 9999 !important;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
  }
  .profile-modal.active {
    opacity: 1;
    visibility: visible;
  }
  .profile-modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.9);
    background: linear-gradient(135deg, #2d1b69 0%, #11101d 100%);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 1rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    max-width: 400px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
    transition: all 0.3s ease;
    z-index: 10000;
  }
  .profile-modal.active .profile-modal-content {
    transform: translate(-50%, -50%) scale(1);
  }
  .profile-modal-header {
    padding: 1.5rem 1.5rem 1rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .profile-modal-body {
    padding: 1.5rem;
  }
  .close-modal {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #6b7280;
    cursor: pointer;
    transition: color 0.2s ease;
  }
  .close-modal:hover {
    color: #374151;
  }
  .modal-menu-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    margin-bottom: 0.5rem;
    border-radius: 0.5rem;
    text-decoration: none;
    color: #374151;
    transition: all 0.2s ease;
    border: 1px solid transparent;
  }
  .modal-menu-item:hover {
    background: #f3f4f6;
    color: #667eea;
    border-color: #e5e7eb;
  }
  .modal-menu-item svg {
    margin-right: 0.75rem;
    width: 1.25rem;
    height: 1.25rem;
  }

  /* User Banner Styles */
  .user-banner {
    @apply border-t border-gray-200 p-4;
  }
  .user-banner button {
    @apply w-full flex items-center p-3 rounded-lg bg-gradient-to-r from-blue-50 to-purple-50 hover:from-blue-100 hover:to-purple-100 transition-all duration-200;
  }
  .user-banner .user-info {
    @apply flex-1 text-left;
  }
  .user-banner .user-info .user-name {
    @apply font-medium text-gray-900;
  }
  .user-banner .user-info .user-role {
    @apply text-xs text-gray-500 capitalize;
  }
  .user-banner svg {
    @apply h-4 w-4 text-gray-400;
  }

  /* Sidebar styles */
  #sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 16rem;
    height: 100vh;
    background: white;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    z-index: 40;
    transition: transform 0.3s ease;
  }
  
  @media (max-width: 768px) {
    #sidebar {
      transform: translateX(-100%);
    }
    
    #sidebar.active {
      transform: translateX(0);
    }
  }
  
  /* Profile modal styles */
  .profile-modal {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: none;
    z-index: 50;
  }
  
  .profile-modal.active {
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .profile-modal-content {
    background: white;
    border-radius: 0.75rem;
    width: 100%;
    max-width: 20rem;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    margin: 1rem;
  }
  
  .profile-modal-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .profile-modal-body {
    padding: 1rem 1.5rem;
  }
  
  .profile-avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(74, 144, 226, 0.1);
    color: #4A90E2;
    font-weight: 600;
    border-radius: 9999px;
  }
  
  .modal-menu-item {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    border-radius: 0.5rem;
    color: #4b5563;
    transition: all 0.2s;
  }
  
  .modal-menu-item:hover {
    background-color: #f3f4f6;
  }
  
  .modal-menu-item svg {
    width: 1.25rem;
    height: 1.25rem;
    margin-right: 0.75rem;
  }
  
  .close-modal {
    font-size: 1.5rem;
    line-height: 1;
    color: #9ca3af;
    cursor: pointer;
  }
  
  .close-modal:hover {
    color: #6b7280;
  }
</style>

<!-- Add the haveToPay-layout class to body if on HaveToPay page -->
<script>
  if (<?php echo $isHaveToPayPage ? 'true' : 'false'; ?>) {
    document.body.classList.add('haveToPay-layout');
  }
</script>

<nav id="sidebar">
  <div class="sidebar-content flex flex-col h-full">
    <!-- Logo/Header -->
    <div class="p-4">
      <a href="/" class="logo-container">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white mr-2" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
        </svg>
        <span class="text-xl font-semibold">Private Vault</span>
      </a>
    </div>

    <!-- Navigation Links Container -->
    <div class="flex-1">
      <div class="nav-container">
        <ul class="space-y-1">
          <li>
            <a href="/dashboard.php" class="nav-link-modern">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
              <span>Dashboard</span>
            </a>
          </li>
          <li>
            <a href="/inbox.php" class="nav-link-modern">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
              </svg>
              <span>Inbox</span>
            </a>
          </li>
          <li>
            <a href="/calendar.php" class="nav-link-modern">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span>Kalender</span>
            </a>
          </li>
          <li>
            <a href="/taskboard.php" class="nav-link-modern">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
              <span>Taskboard</span>
            </a>
          </li>
          <li>
            <a href="/havetopay.php" class="nav-link-modern">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V4m0 12v4" />
              </svg>
              <span>HaveToPay</span>
            </a>
          </li>
          <?php if ($user && ($user['role'] ?? '') === 'admin'): ?>
          <li>
            <a href="/admin.php" class="nav-link-modern">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3zm0 2c-2.21 0-4 1.79-4 4v1h8v-1c0-2.21-1.79-4-4-4z" />
              </svg>
              <span>Admin</span>
            </a>
          </li>
          <li>
            <a href="/admin/groups.php" class="nav-link-modern">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-3-3h-2M9 20H4v-2a3 3 0 013-3h2m7-4a4 4 0 10-8 0 4 4 0 008 0z" />
              </svg>
              <span>Gruppen</span>
            </a>
          </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>

    <!-- User Banner at Bottom -->
    <div class="user-banner">
      <button onclick="openProfileModal()">
        <div class="profile-avatar mr-3">
          <?= isset($user) ? strtoupper(substr($user['username'], 0, 2)) : 'GU' ?>
        </div>
        <div class="flex-1 text-left">
          <div class="user-name truncate"><?= isset($user) ? htmlspecialchars($user['username']) : 'Gast' ?></div>
          <div class="user-role"><?= isset($user) ? ucfirst($user['role'] ?? 'user') : 'Nicht angemeldet' ?></div>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </button>
    </div>
  </div>
</nav>

<!-- Mobile header -->
<div class="md:hidden fixed top-0 left-0 right-0 h-16 z-30" style="background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);">
  <div class="mobile-header-content h-full">
    <button id="mobileToggleBtn" class="mobile-toggle-btn">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>
    <a href="/" class="logo-container">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
      </svg>
      <span class="font-semibold">Private Vault</span>
    </a>
    <button onclick="openProfileModal()" class="mobile-toggle-btn">
      <div class="h-6 w-6 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center text-white text-xs font-semibold">
        <?= isset($user) ? strtoupper(substr($user['username'], 0, 2)) : 'GU' ?>
      </div>
    </button>
  </div>
</div>

<!-- Profile Modal -->
<?php if ($user): ?>
<div id="profileModal" class="profile-modal">
  <div class="profile-modal-content">
    <div class="profile-modal-header">
      <div class="flex items-center">
        <div class="profile-avatar mr-3" style="width: 3rem; height: 3rem;">
          <?= getUserInitials($user) ?>
        </div>
        <div>
          <h3 class="font-semibold text-gray-900"><?= htmlspecialchars($user['username']) ?></h3>
          <p class="text-sm text-gray-500"><?= ucfirst($user['role']) ?></p>
        </div>
      </div>
      <button class="close-modal" onclick="closeProfileModal()">&times;</button>
    </div>
    <div class="profile-modal-body">
      <nav class="space-y-1">
        <a href="/profile.php" class="modal-menu-item">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
          </svg>
          Profil bearbeiten
        </a>
        <a href="/settings.php" class="modal-menu-item">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37 2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
          Einstellungen
        </a>
        <a href="/profile.php?tab=notifications" class="modal-menu-item">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 17H4l5 5v-5zM9 7v10m6-10v10"></path>
          </svg>
          Benachrichtigungen
        </a>
        <a href="/profile.php?tab=security" class="modal-menu-item">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
          </svg>
          Sicherheit
        </a>
        <hr class="my-3">
        <a href="/logout.php" class="modal-menu-item text-red-600 hover:text-red-700 hover:bg-red-50">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
          </svg>
          Abmelden
        </a>
      </nav>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Enhanced navigation scripts -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const mobileToggleBtn = document.getElementById('mobileToggleBtn');
    const sidebar = document.querySelector('.sidebar-content');
    
    if (mobileToggleBtn && sidebar) {
      mobileToggleBtn.addEventListener('click', function() {
        document.querySelector('nav#sidebar').classList.toggle('active');
      });
    }
  });

  function openProfileModal() {
    const modal = document.getElementById('profileModal');
    if (modal) {
      modal.classList.add('active');
    }
  }

  function closeProfileModal() {
    const modal = document.getElementById('profileModal');
    if (modal) {
      modal.classList.remove('active');
    }
  }

  // Close modal when clicking outside
  document.addEventListener('click', function(e) {
    const modal = document.getElementById('profileModal');
    if (modal && modal.classList.contains('active') && !modal.contains(e.target) && !e.target.closest('button[onclick="openProfileModal()"]')) {
      closeProfileModal();
    }
  });
</script>
