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

  /* Profile Modal Styling */
  .profile-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(5px);
    z-index: 1000;
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
    background: white;
    border-radius: 1rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    max-width: 400px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
    transition: all 0.3s ease;
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
    <div class="p-4 border-b border-gray-200">
      <a href="/" class="flex items-center space-x-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#4A90E2]" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
        </svg>
        <span class="text-xl font-semibold text-gray-900">Private Vault</span>
      </a>
    </div>

    <!-- Navigation Links -->
    <div class="py-4 flex-1 overflow-y-auto">
      <ul class="space-y-1 px-3">
        <li>
          <a href="/dashboard.php" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span>Dashboard</span>
          </a>
        </li>
        <li>
          <a href="/inbox.php" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <span>Inbox</span>
          </a>
        </li>
        <li>
          <a href="/calendar.php" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>Kalender</span>
          </a>
        </li>
        <!-- More menu items can be added here -->
      </ul>
    </div>

    <!-- User Banner at Bottom -->
    <div class="user-banner border-t border-gray-200 p-4">
      <button onclick="openProfileModal()" class="w-full flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100">
        <div class="h-8 w-8 rounded-full bg-[#4A90E2]/10 flex items-center justify-center text-[#4A90E2] font-semibold">
          <?= isset($user) ? strtoupper(substr($user['username'], 0, 2)) : 'GU' ?>
        </div>
        <div class="flex-1 truncate">
          <div class="font-medium text-gray-900 truncate"><?= isset($user) ? htmlspecialchars($user['username']) : 'Gast' ?></div>
          <div class="text-xs text-gray-500"><?= isset($user) ? ucfirst($user['role'] ?? 'user') : 'Nicht angemeldet' ?></div>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </button>
    </div>
  </div>
</nav>

<!-- Mobile header -->
<div class="md:hidden fixed top-0 left-0 right-0 bg-white shadow-sm z-30 px-4 py-2 flex items-center justify-between">
  <button id="mobileToggleBtn" class="p-2 rounded-lg hover:bg-gray-100">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
  </button>
  <a href="/" class="flex items-center space-x-2">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#4A90E2]" viewBox="0 0 20 20" fill="currentColor">
      <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
    </svg>
    <span class="font-semibold text-gray-900">Private Vault</span>
  </a>
  <button onclick="openProfileModal()" class="p-2 rounded-lg hover:bg-gray-100">
    <div class="h-6 w-6 rounded-full bg-[#4A90E2]/10 flex items-center justify-center text-[#4A90E2] text-xs font-semibold">
      <?= isset($user) ? strtoupper(substr($user['username'], 0, 2)) : 'GU' ?>
    </div>
  </button>
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
