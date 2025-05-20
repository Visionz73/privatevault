<?php
// Adjusted Navbar with dynamic path handling
require_once __DIR__ . '/../src/lib/auth.php';
$user = getUser();

// Detect if we're in the admin directory to adjust paths
$isAdminPage = strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false;
$basePath = $isAdminPage ? '../' : '';
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
      background: rgba(255,255,255,0.8);
      backdrop-filter: blur(10px);
      border-right: 1px solid rgba(255,255,255,0.6);
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      z-index: 50;
    }
    .mobile-menu { display: none; }
    .sidebar-content { display: block; }
  }

  /* Mobile topbar styling */
  @media (max-width: 768px) {
    nav#sidebar {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 3.5rem; /* fixed top bar height */
      background: rgba(255,255,255,0.95);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(255,255,255,0.6);
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
      background: rgba(255,255,255,0.95);
      backdrop-filter: blur(10px);
      z-index: 49;
      padding: 1rem;
    }
    .sidebar-content.active {
      display: block;
    }
  }
</style>
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
    <a href="<?= $basePath ?>dashboard.php" class="flex items-center">
      <img src="/assets/logo.png" alt="Logo" class="h-12 w-auto" />
      
    </a>
  </div>
  <!-- Desktop: sidebar content -->
  <div class="sidebar-content flex flex-col h-full">
    <div class="flex-1 overflow-y-auto mt-2">
      <button id="toggleBtn" class="p-4 focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" 
             viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
      <!-- Centered logo -->
      <div class="flex justify-center mb-6">
        <a href="<?= $basePath ?>dashboard.php" class="flex items-center">
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
        ];
        foreach ($links as $l): ?>
          <li>
            <a href="<?= $basePath . $l['href'] ?>" class="flex items-center w-full text-gray-700 hover:text-primary transition px-4 py-2 rounded-lg">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
                   viewBox="0 0 24 24" stroke="currentColor">
                <?= $l['icon'] ?>
              </svg>
              <span class="ml-3"><?= $l['label'] ?></span>
            </a>
          </li>
        <?php endforeach; ?>
        <?php if ($user && $user['role'] === 'admin'): ?>
          <li>
            <a href="<?= $isAdminPage ? 'index.php' : 'admin/index.php' ?>" class="flex items-center w-full text-gray-700 hover:text-primary transition px-4 py-2 rounded-lg">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
                   viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
              </svg>
              <span class="ml-3">Admin Dashboard</span>
            </a>
          </li>
          <li>
            <a href="<?= $isAdminPage ? 'groups.php' : 'admin/groups.php' ?>" class="flex items-center w-full text-gray-700 hover:text-primary transition px-4 py-2 rounded-lg">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
                   viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <span class="ml-3">Gruppen</span>
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
    <div class="px-4 py-2 w-full mt-auto">
      <a href="<?= $basePath ?>logout.php" class="flex items-center w-full text-gray-700 hover:text-primary transition px-4 py-2 rounded-lg">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7" />
        </svg>
        <span class="ml-3">Abmelden</span>
      </a>
    </div>
  </div>
</nav>

<script>
  // Mobile toggle: toggle sidebar content on mobile
  const mobileToggleBtn = document.getElementById('mobileToggleBtn');
  const sidebarContent = document.querySelector('nav .sidebar-content');
  
  if (mobileToggleBtn) {
    mobileToggleBtn.addEventListener('click', () => {
      sidebarContent.classList.toggle('active');
    });
  }
  // Desktop toggle for collapse (optional)
  const btn = document.getElementById('toggleBtn');
  if(btn) {
    btn.addEventListener('click', () => {
      const sidebar = document.getElementById('sidebar');
      sidebar.classList.toggle('collapsed');
      sidebar.classList.toggle('w-64');
      sidebar.classList.toggle('w-16');
    });
  }
</script>
