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
  /* Modern frosted glass sidebar styling with dark gradient */
  @media (min-width: 769px) {
    nav#sidebar {
      position: fixed;
      left: 0;
      top: 0;
      bottom: 0;
      width: 16rem; /* w-64 */
      background: linear-gradient(135deg, #2d1b69 0%, #8b1538 50%, #1a0d0d 100%);
      backdrop-filter: saturate(150%) blur(20px);
      border-right: 1px solid rgba(255,255,255,0.1);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      z-index: 50;
      padding: 1.5rem;
      display: flex;
      flex-direction: column;
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
      height: 4rem;
      background: linear-gradient(135deg, #2d1b69 0%, #8b1538 50%, #1a0d0d 100%);
      backdrop-filter: saturate(150%) blur(20px);
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
      background: linear-gradient(135deg, #2d1b69 0%, #8b1538 50%, #1a0d0d 100%);
      backdrop-filter: saturate(150%) blur(20px);
      z-index: 49;
      padding: 1rem;
      overflow-y: auto;
    }
    .sidebar-content.active {
      display: block;
    }
  }

  /* Logo styling - updated for dark theme */
  .logo-container {
    display: flex;
    align-items: center;
    margin-bottom: 2rem;
  }
  
  .logo-icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 9999px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-right: 0.75rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
  }
  
  .logo-text {
    font-size: 1.25rem;
    font-weight: 600;
    color: white;
  }

  /* Navigation links - rounded transparent blur sections */
  .nav-links {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    flex: 1;
  }
  
  .nav-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    border-radius: 12px;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 500;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    margin-bottom: 0.25rem;
  }
  
  .nav-link:hover {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(20px);
    border-color: rgba(255, 255, 255, 0.2);
    color: white;
    transform: translateX(4px);
  }
  
  .nav-link.active {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(25px);
    border-color: rgba(255, 255, 255, 0.3);
    color: white;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(255, 255, 255, 0.1);
  }
  
  .nav-link svg {
    width: 1.25rem;
    height: 1.25rem;
    color: rgba(255, 255, 255, 0.8);
  }

  .nav-link.active svg {
    color: white;
  }

  /* User banner in sidebar - updated for dark theme */
  .user-banner {
    margin-top: auto;
    padding-top: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
  }
  
  .user-button {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    width: 100%;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  
  .user-button:hover {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(20px);
    border-color: rgba(255, 255, 255, 0.2);
  }
  
  .user-avatar {
    width: 2rem;
    height: 2rem;
    border-radius: 9999px;
    background: rgba(255, 255, 255, 0.15);
    color: white;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.75rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
  }
  
  .user-info {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    flex: 1;
    overflow: hidden;
  }
  
  .user-name {
    font-weight: 500;
    color: white;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    width: 100%;
  }
  
  .user-role {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.7);
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
    border: none;
  }
  .modal-menu-item:hover {
    background: #f3f4f6;
    color: #667eea;
  }
  .modal-menu-item svg {
    margin-right: 0.75rem;
    width: 1.25rem;
    height: 1.25rem;
  }

  /* Mobile view specific styles - updated colors */
  @media (max-width: 768px) {
    .mobile-menu-btn {
      padding: 0.5rem;
      border-radius: 8px;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .mobile-menu-btn svg {
      width: 1.5rem;
      height: 1.5rem;
      color: white;
    }
    
    .mobile-logo {
      display: flex;
      align-items: center;
    }
    
    .mobile-logo .logo-icon {
      width: 2rem;
      height: 2rem;
      margin-right: 0.5rem;
    }
    
    .mobile-logo .logo-text {
      font-size: 1.125rem;
      color: white;
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
  <div class="sidebar-content flex flex-col h-full">
    <!-- Logo/Header -->
    <div class="logo-container">
      <div class="logo-icon">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
        </svg>
      </div>
      <span class="logo-text">Private Vault</span>
    </div>

    <!-- Navigation Links -->
    <div class="nav-links">
      <a href="/dashboard.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
        </svg>
        <span>Dashboard</span>
      </a>
      
      <a href="/inbox.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'inbox.php' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
        </svg>
        <span>Inbox</span>
      </a>
      
      <a href="/calendar.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'calendar.php' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <span>Kalender</span>
      </a>
      
      <a href="/taskboard.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'taskboard.php' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        <span>Taskboard</span>
      </a>
      
      <a href="/havetopay.php" class="nav-link <?= $isHaveToPayPage ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V4m0 12v4" />
        </svg>
        <span>HaveToPay</span>
      </a>
      
      <?php if ($user && ($user['role'] ?? '') === 'admin'): ?>
      <a href="/admin.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'admin.php' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016zM12 9v2m0 4h.01" />
        </svg>
        <span>Admin</span>
      </a>
      
      <a href="/admin/groups.php" class="nav-link <?= $isAdminPage ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-3-3h-2M9 20H4v-2a3 3 0 013-3h2m7-4a4 4 0 10-8 0 4 4 0 008 0z" />
        </svg>
        <span>Gruppen</span>
      </a>
      <?php endif; ?>
    </div>

    <!-- User Banner at Bottom -->
    <div class="user-banner">
      <button onclick="openProfileModal()" class="user-button">
        <div class="user-avatar">
          <?= isset($user) ? strtoupper(substr($user['username'], 0, 2)) : 'GU' ?>
        </div>
        <div class="user-info">
          <div class="user-name"><?= isset($user) ? htmlspecialchars($user['username']) : 'Gast' ?></div>
          <div class="user-role"><?= isset($user) ? ucfirst($user['role'] ?? 'user') : 'Nicht angemeldet' ?></div>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </button>
    </div>
  </div>
</nav>

<!-- Mobile header -->
<div class="md:hidden fixed top-0 left-0 right-0 mobile-menu z-30">
  <button id="mobileToggleBtn" class="mobile-menu-btn">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
  </button>
  
  <div class="mobile-logo">
    <div class="logo-icon">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
      </svg>
    </div>
    <span class="logo-text">Private Vault</span>
  </div>
  
  <button onclick="openProfileModal()" class="mobile-menu-btn">
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
        <!-- Profile Section Group -->
        <div class="border border-gray-200 rounded-lg p-1">
          <a href="/profile.php" class="modal-menu-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            Profil bearbeiten
          </a>
          <a href="/settings.php" class="modal-menu-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            Einstellungen
          </a>
          <a href="/profile.php?tab=notifications" class="modal-menu-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-5 5v-5zM9 17H4l5 5v-5zM9 7v10m6-10v10"></path>
            </svg>
            Benachrichtigungen
          </a>
          <a href="/profile.php?tab=security" class="modal-menu-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            Sicherheit
          </a>
        </div>
        
        <!-- Logout Section Group -->
        <div class="border border-gray-200 rounded-lg p-1">
          <a href="/logout.php" class="modal-menu-item text-red-600 hover:text-red-700 hover:bg-red-50">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            Abmelden
          </a>
        </div>
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
