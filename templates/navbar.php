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
  /* Basic navbar styling without gradients */
  @media (min-width: 769px) {
    nav#sidebar {
      position: fixed;
      left: 0;
      top: 0;
      bottom: 0;
      width: 16rem; /* w-64 */
      background: #4F46E5;
      border-right: 1px solid #3730A3;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      z-index: 50;
      display: flex;
      flex-direction: column;
    }
    .mobile-menu { display: none; }
    .sidebar-content { 
      display: flex;
      flex-direction: column;
      flex-grow: 1;
      overflow-y: auto;
    }
  }

  /* Mobile styling */
  @media (max-width: 768px) {
    nav#sidebar {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 3.5rem;
      background: #4F46E5;
      border-bottom: 1px solid #3730A3;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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
      top: 3.5rem;
      left: 0;
      right: 0;
      bottom: 0;
      background: #4F46E5;
      z-index: 49;
      padding: 1rem;
      overflow-y: auto;
    }
    .sidebar-content.active {
      display: flex;
      flex-direction: column;
    }
    .sidebar-content ul li {
      margin-bottom: 0.75rem;
    }
    .sidebar-content ul li a {
      display: flex;
      align-items: center;
      padding: 0.75rem 1rem;
      border-radius: 0.5rem;
      transition: all 0.2s ease;
      color: white;
    }
    .sidebar-content ul li a:hover {
      background-color: rgba(255,255,255,0.1);
    }
  }

  /* Menu button styling */
  .menu-btn {
    background: rgba(255,255,255,0.1);
    border: none;
    border-radius: 0.25rem;
    padding: 0.5rem;
    color: white;
    transition: all 0.2s ease;
  }
  .menu-btn:hover {
    background: rgba(255,255,255,0.2);
  }

  /* Logo area styling */
  .logo-container {
    text-align: center;
    padding: 1.5rem 1rem;
    margin-bottom: 1rem;
  }
  
  .logo-container .logo-text {
    color: white;
    font-size: 1.5rem;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 0.5rem;
  }
  
  .logo-container .app-name {
    color: white;
    font-size: 1rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
  }
  
  /* User profile banner - ensure it stays at bottom */
  .user-profile-banner {
    margin-top: auto;
    margin-bottom: 1rem;
    margin-left: 1rem;
    margin-right: 1rem;
    border-radius: 0.5rem;
    background: #3730A3;
    padding: 0.75rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    transition: all 0.2s ease;
    min-height: auto;
  }
  
  .user-profile-banner:hover {
    background: #4338CA;
  }
  
  .user-avatar {
    width: 3rem;
    height: 3rem;
    background: #4F46E5;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
    border: 2px solid rgba(255,255,255,0.3);
  }
  
  .user-display-name {
    color: white;
    font-size: 1rem;
    font-weight: 600;
    text-align: center;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
    line-height: 1.2;
  }
  
  .user-username {
    color: rgba(255,255,255,0.8);
    font-size: 0.75rem;
    text-align: center;
    margin-top: 0.25rem;
  }

  /* Navigation menu items styling */
  .nav-menu {
    padding: 0 0.5rem;
    list-style-type: none;
    flex-grow: 1;
  }
  
  .nav-item {
    margin-bottom: 0.5rem;
  }
  
  .nav-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    color: white;
    transition: all 0.2s ease;
    font-weight: 500;
    text-decoration: none;
  }
  
  .nav-link:hover, .nav-link.active {
    background: rgba(255,255,255,0.1);
  }
  
  .nav-icon {
    width: 1.5rem;
    height: 1.5rem;
    margin-right: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
  }
</style>

<!-- Main Navigation Sidebar -->
<nav id="sidebar">
  <!-- Mobile Menu -->
  <div class="mobile-menu">
    <div class="flex items-center">
      <button class="menu-btn mr-2" id="mobileMenuToggle">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
      <span class="text-white font-semibold text-lg">PrivateVault</span>
    </div>
    
    <?php if ($user): ?>
      <div class="flex items-center">
        <div class="h-8 w-8 rounded-full bg-indigo-700 flex items-center justify-center text-white font-medium">
          <?php echo strtoupper(substr($user['username'] ?? 'U', 0, 1)); ?>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <!-- Sidebar Content -->
  <div class="sidebar-content">
    <!-- Logo Area -->
    <div class="logo-container">
      <div class="logo-text">OMNI</div>
      <div class="app-name">PrivateVault</div>
    </div>

    <!-- Main Navigation -->
    <ul class="nav-menu">
      <li class="nav-item">
        <a href="/index.php" class="nav-link <?php echo basename($_SERVER['SCRIPT_NAME']) == 'index.php' ? 'active' : ''; ?>">
          <div class="nav-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
          </div>
          <span>Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="/upload.php" class="nav-link <?php echo basename($_SERVER['SCRIPT_NAME']) == 'upload.php' ? 'active' : ''; ?>">
          <div class="nav-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
          </div>
          <span>Upload</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="/taskboard.php" class="nav-link <?php echo basename($_SERVER['SCRIPT_NAME']) == 'taskboard.php' ? 'active' : ''; ?>">
          <div class="nav-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
          </div>
          <span>MyTask</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="/create_task.php" class="nav-link <?php echo basename($_SERVER['SCRIPT_NAME']) == 'create_task.php' ? 'active' : ''; ?>">
          <div class="nav-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <span>Create Task</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="/inbox.php" class="nav-link <?php echo basename($_SERVER['SCRIPT_NAME']) == 'inbox.php' ? 'active' : ''; ?>">
          <div class="nav-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
          </div>
          <span>Kanban</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="/havetopay.php" class="nav-link <?php echo $isHaveToPayPage ? 'active' : ''; ?>">
          <div class="nav-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <span>HaveToPay</span>
        </a>
      </li>
      
      <?php if (isset($user['is_admin']) && $user['is_admin']): ?>
      <li class="nav-item">
        <a href="/admin.php" class="nav-link <?php echo basename($_SERVER['SCRIPT_NAME']) == 'admin.php' ? 'active' : ''; ?>">
          <div class="nav-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
            </svg>
          </div>
          <span>Admin Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="/admin/groups.php" class="nav-link <?php echo $isAdminPage && basename($_SERVER['SCRIPT_NAME']) == 'groups.php' ? 'active' : ''; ?>">
          <div class="nav-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </div>
          <span>Manage Groups</span>
        </a>
      </li>
      <?php endif; ?>
      
      <!-- Logout Link in Navigation -->
      <li class="nav-item">
        <a href="/logout.php" class="nav-link">
          <div class="nav-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
          </div>
          <span>Logout</span>
        </a>
      </li>
    </ul>

    <!-- User Profile Banner (Bottom) -->
    <?php if ($user): ?>
    <div class="user-profile-banner">
      <div class="user-avatar">
        <?php echo strtoupper(substr($user['username'] ?? 'U', 0, 2)); ?>
      </div>
      <div class="user-display-name">
        <?php echo htmlspecialchars($user['first_name'] ? $user['first_name'] . ' ' . $user['last_name'] : $user['username']); ?>
      </div>
      <div class="user-username">@<?php echo htmlspecialchars($user['username']); ?></div>
    </div>
    <?php endif; ?>
  </div>
</nav>

<script>
  // Mobile menu toggle
  document.getElementById('mobileMenuToggle')?.addEventListener('click', function() {
    const sidebarContent = document.querySelector('.sidebar-content');
    sidebarContent.classList.toggle('active');
  });
  
  // Close mobile menu when clicking outside
  document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    
    if (window.innerWidth <= 768 && 
        !sidebar.contains(event.target) || 
        (event.target !== mobileMenuToggle && !mobileMenuToggle.contains(event.target))) {
      const sidebarContent = document.querySelector('.sidebar-content');
      if (sidebarContent.classList.contains('active')) {
        sidebarContent.classList.remove('active');
      }
    }
  });
</script>
