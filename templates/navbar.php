<?php
// templates/navbar.php (updated with Kanban link)
require_once __DIR__ . '/../src/lib/auth.php';
$user = getUser();
?>
<style>
  nav.collapsed .label { display: none; }
  nav { transition: width 0.3s; }
  /* Additional mobile styling */
  @media (max-width: 767px) {
    nav { width: 100% !important; position: relative; }
  }
</style>
<nav id="sidebar" class="bg-white/80 backdrop-blur-sm border-r border-white/60 shadow-lg fixed left-0 top-0 bottom-0 w-64 hidden md:flex flex-col">
  <!-- Mobile Toggle (shown only on mobile) -->
  <div class="md:hidden flex justify-between p-4 bg-white/80 border-b border-gray-200">
    <span class="text-lg font-bold">Menu</span>
    <button id="mobileToggleBtn" class="p-2">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>
  </div>
  <!-- Existing sidebar content -->
  <button id="toggleBtn" class="p-4 focus:outline-none">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
  </button>
  <div class="flex-1 overflow-y-auto mt-2">
    <!-- Logo -->
    <a href="dashboard.php" class="flex items-center w-full px-4 py-2 mb-6">
      <img src="/assets/logo.png" alt="Logo" class="h-16 w-auto" />
      <span class="label ml-3 text-xl font-bold text-gray-900">O-Archive</span>
    </a>
    <!-- Navigation Links -->
    <ul class="flex flex-col space-y-4 px-2">
      <?php
      $links = [
        ['href'=>'dashboard.php',   'icon'=>'<path d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m-4 0h8" />', 'label'=>'Dashboard'],
        ['href'=>'upload.php',      'icon'=>'<path d="M12 4v16m8-8H4" />', 'label'=>'Upload'],
        ['href'=>'profile.php',     'icon'=>'<path d="M5.121 17.804A9 9 0 0112 15a9 9 0 016.879 2.804M12 12a4 4 0 100-8 4 4 0 000 8z" />', 'label'=>'Profil'],
        ['href'=>'inbox.php',       'icon'=>'<path d="M9 12h6m2 0a8 8 0 11-16 0 8 8 0 0116 0z" />', 'label'=>'MyTask'],
        ['href'=>'create_task.php', 'icon'=>'<path d="M4 4l16 16M4 20L20 4" />', 'label'=>'Create Task'],
        ['href'=>'taskboard.php',   'icon'=>'<path d="M4 6h16M4 12h16M4 18h16" />', 'label'=>'Kanban'],
        // Add additional links as needed
      ];
      foreach ($links as $l): ?>
      <li>
        <a href="<?= $l['href'] ?>" class="flex items-center w-full text-gray-700 hover:text-primary transition px-4 py-2 rounded-lg">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <?= $l['icon'] ?>
          </svg>
          <span class="label ml-3"><?= $l['label'] ?></span>
        </a>
      </li>
      <?php endforeach; ?>
      <?php if ($user && $user['role'] === 'admin'): ?>
      <li>
        <a href="admin.php" class="flex items-center w-full text-gray-700 hover:text-primary transition px-4 py-2 rounded-lg">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
          </svg>
          <span class="label ml-3">Admin</span>
        </a>
      </li>
      <?php endif; ?>
    </ul>
  </div>
  <!-- Logout -->
  <div class="px-4 py-2 w-full">
    <a href="logout.php" class="flex items-center w-full text-gray-700 hover:text-primary transition px-4 py-2 rounded-lg">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7" />
      </svg>
      <span class="label ml-3">Abmelden</span>
    </a>
  </div>
</nav>

<script>
  // Mobile toggle for sidebar
  const mobileToggleBtn = document.getElementById('mobileToggleBtn');
  if(mobileToggleBtn){
    mobileToggleBtn.addEventListener('click', () => {
      const sidebar = document.getElementById('sidebar');
      sidebar.classList.toggle('hidden');
    });
  }
  // Existing toggle for desktop sidebar
  const btn = document.getElementById('toggleBtn');
  const sidebar = document.getElementById('sidebar');
  btn.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
    sidebar.classList.toggle('w-64');
    sidebar.classList.toggle('w-16');
  });
</script>
