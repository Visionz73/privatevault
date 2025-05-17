<?php
// Adjusted Navbar for Mobile and Desktop
require_once __DIR__ . '/../src/lib/auth.php';
$user = getUser();
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
    /* Use relative positioning to center the logo */
    .mobile-menu {
      position: relative;
      width: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    /* Show toggler at the left */
    .mobile-menu button#mobileToggleBtn {
      position: absolute;
      left: 0;
      margin-left: 0.5rem;
    }
    .sidebar-content { display: none; }
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
    <a href="dashboard.php" class="flex items-center">
      <img src="/assets/logo.png" alt="Logo" class="h-8 w-auto" />
      
    </a>
  </div>
  <!-- Desktop: sidebar content -->
  <div class="sidebar-content">
    <button id="toggleBtn" class="p-4 focus:outline-none">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" 
           viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
              d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>
    <!-- Centered logo -->
    <div class="flex justify-center mb-6">
      <a href="dashboard.php" class="flex items-center">
        <img src="/assets/logo.png" alt="Logo" class="h-16 w-auto" />
        
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
          <a href="<?= $l['href'] ?>" class="flex items-center w-full text-gray-700 hover:text-primary transition px-4 py-2 rounded-lg">
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
          <a href="admin.php" class="flex items-center w-full text-gray-700 hover:text-primary transition px-4 py-2 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
                 viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
            </svg>
            <span class="ml-3">Admin</span>
          </a>
        </li>
      <?php endif; ?>
    </ul>
    <div class="px-4 py-2 w-full">
      <a href="logout.php" class="flex items-center w-full text-gray-700 hover:text-primary transition px-4 py-2 rounded-lg">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
             viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M17 16l4-4m0 0l-4-4m4 4H7" />
        </svg>
        <span class="ml-3">Abmelden</span>
      </a>
    </div>
  </div>
</nav>

<script>
  // Mobile toggle: toggle sidebar content on mobile
  const mobileToggleBtn = document.getElementById('mobileToggleBtn');
  if(mobileToggleBtn) {
    mobileToggleBtn.addEventListener('click', () => {
      const sidebarContent = document.querySelector('nav .sidebar-content');
      sidebarContent.classList.toggle('hidden');
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
