<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// templates/profile.php - Modern redesign with liquid glass effect
require_once __DIR__ . '/../src/lib/auth.php';
require_once __DIR__ . '/../src/lib/utils.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: /login.php');
    exit;
}

$user = getUser();
if (!$user) {
    die('Error: Could not load user data');
}

$activeTab = $_GET['tab'] ?? 'personal_info';
$initials = getUserInitials($user);
?>
<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Profil | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { 
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
      min-height: 100vh;
    }
    
    /* Enhanced Glassmorphism Effects */
    .glass-card {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 1.5rem;
      box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }
    
    .glass-card:hover {
      background: rgba(255, 255, 255, 0.1);
      border-color: rgba(255, 255, 255, 0.18);
      transform: translateY(-2px);
      box-shadow: 
        0 12px 40px rgba(0, 0, 0, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.15);
    }
    
    .glass-nav {
      background: rgba(255, 255, 255, 0.06);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 1.25rem;
      box-shadow: 0 4px 24px rgba(0, 0, 0, 0.2);
    }
    
    .glass-content {
      background: rgba(255, 255, 255, 0.04);
      backdrop-filter: blur(25px);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 2rem;
      box-shadow: 
        0 16px 48px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.05);
    }
    
    /* Navigation Items - Only these have hover animations */
    .nav-item {
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 1rem;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }
    
    .nav-item::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
      transition: left 0.5s ease;
    }
    
    .nav-item:hover::before {
      left: 100%;
    }
    
    .nav-item:hover {
      background: rgba(255, 255, 255, 0.08);
      border-color: rgba(255, 255, 255, 0.15);
      transform: translateX(8px) scale(1.02);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    }
    
    .nav-item.active {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.4) 0%, rgba(79, 70, 229, 0.4) 100%);
      border-color: rgba(147, 51, 234, 0.5);
      box-shadow: 
        0 8px 32px rgba(147, 51, 234, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }
    
    /* Form Elements */
    .glass-input {
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.15);
      color: white;
      border-radius: 0.875rem;
      backdrop-filter: blur(10px);
      transition: all 0.3s ease;
      box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .glass-input:focus {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(147, 51, 234, 0.5);
      outline: none;
      box-shadow: 
        0 0 0 3px rgba(147, 51, 234, 0.2),
        inset 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .glass-input::placeholder {
      color: rgba(255, 255, 255, 0.4);
    }
    
    .glass-select {
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.15);
      color: white;
      border-radius: 0.875rem;
      backdrop-filter: blur(10px);
    }
    
    .glass-select option {
      background: #2d1b69;
      color: white;
    }
    
    /* Buttons */
    .glass-btn-primary {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.7) 0%, rgba(79, 70, 229, 0.7) 100%);
      border: 1px solid rgba(147, 51, 234, 0.4);
      color: white;
      border-radius: 0.875rem;
      backdrop-filter: blur(10px);
      transition: all 0.3s ease;
      font-weight: 500;
      box-shadow: 0 4px 16px rgba(147, 51, 234, 0.3);
    }
    
    .glass-btn-primary:hover {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.8) 0%, rgba(79, 70, 229, 0.8) 100%);
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(147, 51, 234, 0.4);
    }
    
    .glass-btn-secondary {
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      border-radius: 0.875rem;
      backdrop-filter: blur(10px);
      transition: all 0.3s ease;
    }
    
    .glass-btn-secondary:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(255, 255, 255, 0.3);
      transform: translateY(-1px);
    }
    
    /* Profile Header */
    .profile-header {
      background: rgba(255, 255, 255, 0.06);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 2rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    
    .profile-avatar {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
      border-radius: 50%;
      border: 3px solid rgba(255,255,255,0.2);
      box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.3),
        inset 0 2px 4px rgba(255, 255, 255, 0.1);
      transition: all 0.3s ease;
    }
    
    .profile-avatar:hover {
      transform: scale(1.05);
      box-shadow: 
        0 12px 40px rgba(0, 0, 0, 0.4),
        inset 0 2px 4px rgba(255, 255, 255, 0.2);
    }
    
    /* Text Colors */
    .text-primary {
      background: linear-gradient(135deg, #a855f7, #3b82f6);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .text-secondary {
      color: rgba(255, 255, 255, 0.8);
    }
    
    .text-muted {
      color: rgba(255, 255, 255, 0.6);
    }
    
    /* Labels */
    label {
      color: rgba(255, 255, 255, 0.9) !important;
      font-weight: 500;
      font-size: 0.875rem;
    }
    
    /* Tables */
    .glass-table {
      background: rgba(255, 255, 255, 0.02);
      border-radius: 1rem;
      overflow: hidden;
    }
    
    .glass-table th {
      background: rgba(255, 255, 255, 0.1);
      color: rgba(255, 255, 255, 0.9);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .glass-table td {
      color: rgba(255, 255, 255, 0.8);
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .glass-table tbody tr:hover {
      background: rgba(255, 255, 255, 0.05);
    }
    
    /* Alerts */
    .glass-alert-success {
      background: rgba(34, 197, 94, 0.15);
      border: 1px solid rgba(34, 197, 94, 0.3);
      color: #86efac;
      border-radius: 1rem;
      backdrop-filter: blur(10px);
    }
    
    .glass-alert-error {
      background: rgba(239, 68, 68, 0.15);
      border: 1px solid rgba(239, 68, 68, 0.3);
      color: #fca5a5;
      border-radius: 1rem;
      backdrop-filter: blur(10px);
    }
    
    /* Responsive Layout */
    .profile-layout {
      min-height: calc(100vh - 4rem);
    }
    
    @media (min-width: 769px) {
      .profile-layout {
        min-height: 100vh;
        margin-left: 16rem;
      }
    }
    
    @media (max-width: 768px) {
      main { margin-top: 4rem; }
      .grid-cols-1.lg\\:grid-cols-4 {
        grid-template-columns: 1fr;
      }
      
      /* Mobile profile optimizations */
      .profile-header {
        margin-bottom: 1.5rem;
      }
      
      .profile-header .p-8 {
        padding: 1.5rem;
      }
      
      .profile-avatar {
        width: 4rem;
        height: 4rem;
        font-size: 1.5rem;
      }
      
      .glass-nav {
        position: static;
        margin-bottom: 1.5rem;
      }
      
      .glass-content {
        border-radius: 1.5rem;
      }
      
      /* Mobile navigation improvements */
      .nav-item {
        padding: 0.75rem;
        margin-bottom: 0.5rem;
      }
      
      .nav-item h4 {
        font-size: 0.9rem;
      }
      
      .nav-item p {
        font-size: 0.75rem;
      }
      
      /* Mobile search box */
      #categorySearch {
        padding: 0.75rem;
        font-size: 0.9rem;
      }
      
      /* Mobile breadcrumb */
      .breadcrumb-mobile {
        font-size: 0.8rem;
        margin-bottom: 1rem;
      }
      
      /* Mobile user info section */
      .mobile-user-section {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
      }
      
      .mobile-user-section .profile-avatar {
        margin: 0 auto;
      }
      
      /* Mobile grid adjustments */
      .mobile-grid-fix {
        grid-template-columns: 1fr;
        gap: 1.5rem;
      }
      
      /* Mobile sticky navigation */
      .mobile-nav-sticky {
        position: sticky;
        top: 4.5rem;
        z-index: 10;
      }
    }
    
    /* Custom Scrollbar */
    ::-webkit-scrollbar {
      width: 8px;
    }
    
    ::-webkit-scrollbar-track {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.2);
      border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
      background: rgba(255, 255, 255, 0.3);
    }
  </style>
</head>
<body class="min-h-screen">
<?php 
$navbarPath = __DIR__ . '/navbar.php';
if (file_exists($navbarPath)) {
    require_once $navbarPath;
}
?>

<!-- Main Content -->
<div class="profile-layout flex-1 p-2 md:p-4 lg:p-8 mt-16 md:mt-0">
  <!-- Profile Header -->
  <header class="profile-header mb-6 md:mb-8">
    <div class="p-4 md:p-8">
      <!-- Breadcrumb -->
      <nav class="flex items-center space-x-2 text-xs md:text-sm mb-4 md:mb-6 breadcrumb-mobile">
        <a href="/dashboard.php" class="text-secondary hover:text-white transition-colors">Dashboard</a>
        <span class="text-muted">/</span>
        <span class="text-primary font-medium">Profil</span>
      </nav>
      
      <!-- User Info -->
      <div class="flex items-center gap-4 md:gap-8 mobile-user-section">
        <div class="profile-avatar h-16 w-16 md:h-24 md:w-24 flex items-center justify-center text-2xl md:text-3xl font-bold text-white">
          <?= $initials ?>
        </div>
        <div class="flex-1 min-w-0">
          <h1 class="text-2xl md:text-4xl font-bold text-primary mb-2 truncate"><?= htmlspecialchars($user['username']) ?></h1>
          <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 mb-2 md:mb-3">
            <span class="px-2 md:px-3 py-1 bg-gradient-to-r from-purple-500/20 to-blue-500/20 border border-purple-500/30 rounded-full text-xs md:text-sm text-purple-200">
              <?= ucfirst($user['role'] ?? 'user') ?>
            </span>
            <span class="text-muted text-xs md:text-sm">
              Mitglied seit <?= date('M Y', strtotime($user['created_at'] ?? 'now')) ?>
            </span>
          </div>
          <p class="text-secondary text-sm md:text-base">
            Verwalten Sie Ihre persönlichen Informationen und Einstellungen
          </p>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Grid Layout -->
  <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 md:gap-8 mobile-grid-fix">
    <!-- Sidebar Navigation -->
    <aside class="lg:col-span-1">
      <div class="glass-nav p-4 md:p-6 mobile-nav-sticky">
        <h3 class="text-base md:text-lg font-semibold text-primary mb-4 md:mb-6">Kategorien</h3>
        
        <!-- Quick Search -->
        <div class="mb-4 md:mb-6">
          <input 
            type="text" 
            placeholder="Suchen..." 
            class="glass-input w-full px-3 md:px-4 py-2 md:py-3 text-sm"
            id="categorySearch"
          >
        </div>
        
        <!-- Navigation Items -->
        <nav class="space-y-2 md:space-y-3" id="categoryNav">
          <?php
          $categories = [
            'personal_info' => [
              'icon' => '<svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>',
              'title' => 'Persönliche Daten',
              'description' => 'Name, Kontakt & Adresse'
            ],
            'finance' => [
              'icon' => '<svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V4m0 12v4"/></svg>',
              'title' => 'Finanzen',
              'description' => 'Einnahmen & Ausgaben'
            ],
            'security' => [
              'icon' => '<svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>',
              'title' => 'Sicherheit',
              'description' => 'Passwort & Privatsphäre'
            ]
          ];
          
          foreach ($categories as $key => $category):
            $isActive = $activeTab === $key;
            $href = "?tab=" . $key;
          ?>
            <a href="<?= $href ?>" 
               class="nav-item <?= $isActive ? 'active' : '' ?> block p-3 md:p-4 text-decoration-none"
               data-category="<?= strtolower($category['title']) ?>">
              <div class="flex items-start gap-2 md:gap-3">
                <div class="text-purple-300 mt-0.5 flex-shrink-0">
                  <?= $category['icon'] ?>
                </div>
                <div class="flex-1 min-w-0">
                  <h4 class="font-medium text-white text-sm mb-1"><?= $category['title'] ?></h4>
                  <p class="text-xs text-muted leading-relaxed"><?= $category['description'] ?></p>
                </div>
              </div>
            </a>
          <?php endforeach; ?>
        </nav>
      </div>
    </aside>
    
    <!-- Content Area -->
    <main class="lg:col-span-3">
      <div class="glass-content p-4 md:p-8">
        <?php
        // Include the appropriate tab content
        switch ($activeTab) {
          case 'personal_info':
            include __DIR__ . '/profile_tabs/personal_info_section.php';
            break;
            
          case 'finance':
            include __DIR__ . '/profile_tabs/finance_section.php';
            break;
            
          case 'documents':
            include __DIR__ . '/profile_tabs/documents_overview.php';
            break;
            
          case 'security':
            include __DIR__ . '/profile_tabs/security_section.php';
            break;
            
          default:
            echo '<div class="glass-card p-6 md:p-8 text-center"><h2 class="text-lg md:text-xl text-primary mb-4">Tab nicht gefunden</h2><p class="text-muted">Der angeforderte Bereich existiert nicht.</p></div>';
        }
        ?>
      </div>
    </main>
  </div>
</div>

<script>
// Search functionality
document.getElementById('categorySearch').addEventListener('input', function(e) {
  const searchTerm = e.target.value.toLowerCase();
  const navItems = document.querySelectorAll('#categoryNav .nav-item');
  
  navItems.forEach(item => {
    const category = item.dataset.category || '';
    const title = item.querySelector('h4').textContent.toLowerCase();
    const description = item.querySelector('p').textContent.toLowerCase();
    
    const matches = category.includes(searchTerm) || 
                   title.includes(searchTerm) || 
                   description.includes(searchTerm);
    
    item.style.display = matches ? 'block' : 'none';
  });
});

// Smooth transitions
document.addEventListener('DOMContentLoaded', function() {
  const cards = document.querySelectorAll('.glass-card, .nav-item');
  cards.forEach((card, index) => {
    card.style.animationDelay = `${index * 100}ms`;
    card.classList.add('animate-fade-in');
  });
  
  // Mobile optimization: Close navigation when clicking on content area
  const mainContent = document.querySelector('main');
  if (mainContent && window.innerWidth <= 768) {
    mainContent.addEventListener('click', function() {
      const sidebar = document.querySelector('.sidebar-content');
      const overlay = document.getElementById('mobileOverlay');
      if (sidebar && sidebar.classList.contains('active')) {
        sidebar.classList.remove('active');
        if (overlay) overlay.classList.remove('active');
        document.body.style.overflow = '';
      }
    });
  }
});
</script>

<style>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fadeIn 0.6s ease-out forwards;
}
</style>
</body>
</html>
