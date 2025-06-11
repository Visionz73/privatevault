<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// templates/profile.php (sidebar categories per application)
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
  <title>Profil Einstellungen | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { 
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
      min-height: 100vh;
    }
    
    /* Main glassmorphism containers */
    .glassmorphism-container {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1.5rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    
    /* Header styling */
    .profile-header {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1.5rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    
    /* Sidebar styling */
    .settings-sidebar {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1.5rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    
    /* Content area styling */
    .settings-content {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1.5rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    
    /* Form inputs */
    .form-input {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      border-radius: 0.75rem;
      transition: all 0.3s ease;
    }
    
    .form-input:focus {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.4);
      outline: none;
      box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
    }
    
    .form-input::placeholder {
      color: rgba(255, 255, 255, 0.5);
    }
    
    /* Navigation items */
    .nav-item {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 0.75rem;
      transition: all 0.3s ease;
      color: rgba(255, 255, 255, 0.9);
    }
    
    .nav-item:hover {
      background: rgba(255, 255, 255, 0.1);
      border-color: rgba(255, 255, 255, 0.2);
      transform: translateX(3px);
      color: white;
    }
    
    .nav-item.active {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.6) 0%, rgba(79, 70, 229, 0.6) 100%);
      border-color: rgba(147, 51, 234, 0.4);
      color: white;
      font-weight: 600;
    }
    
    /* Subtab navigation */
    .subtab-nav {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 1rem;
      padding: 0.25rem;
    }
    
    .subtab-item {
      color: rgba(255, 255, 255, 0.7);
      border-radius: 0.5rem;
      transition: all 0.3s ease;
    }
    
    .subtab-item:hover {
      color: rgba(255, 255, 255, 0.9);
    }
    
    .subtab-item.active {
      background: rgba(255, 255, 255, 0.15);
      color: white;
      font-weight: 500;
    }
    
    /* Buttons */
    .btn-primary {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.8) 0%, rgba(79, 70, 229, 0.8) 100%);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      border-radius: 0.75rem;
      transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.9) 0%, rgba(79, 70, 229, 0.9) 100%);
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(147, 51, 234, 0.3);
    }
    
    /* Avatar styling */
    .profile-avatar {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
      border: 3px solid rgba(255, 255, 255, 0.3);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }
    
    /* Badge styling */
    .role-badge {
      background: rgba(147, 51, 234, 0.2);
      border: 1px solid rgba(147, 51, 234, 0.4);
      color: #c4b5fd;
    }
    
    /* Text colors */
    .text-primary {
      color: white;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }
    
    .text-secondary {
      color: rgba(255, 255, 255, 0.8);
    }
    
    .text-muted {
      color: rgba(255, 255, 255, 0.6);
    }
    
    /* Mobile adjustments */
    @media (max-width: 768px) {
      main { margin-top: 4rem; }
      .profile-header { margin: 1rem; }
    }
    
    @media (min-width: 769px) {
      main { margin-left: 16rem; }
    }
  </style>
</head>
<body class="min-h-screen flex">
<?php 
$navbarPath = __DIR__ . '/navbar.php';
if (file_exists($navbarPath)) {
    require_once $navbarPath;
} else {
    echo '<!-- Navbar not found at: ' . $navbarPath . ' -->';
}
?>

<!-- Main Content -->
<div class="ml-0 md:ml-64 flex-1 p-4 md:p-8 mt-14 md:mt-0">
  <!-- Header with user info and breadcrumb -->
  <header class="profile-header mb-8">
    <div class="max-w-6xl mx-auto px-8 py-6">
      <!-- Breadcrumb -->
      <nav class="flex items-center space-x-2 text-sm text-secondary mb-4">
        <a href="/dashboard.php" class="hover:text-white transition-colors">Dashboard</a>
        <span>&rsaquo;</span>
        <span class="text-primary font-medium">Profil Einstellungen</span>
      </nav>
      
      <!-- User Header -->
      <div class="flex items-center gap-6">
        <div class="profile-avatar h-20 w-20 rounded-full flex items-center justify-center text-2xl font-bold text-white">
          <?= $initials ?>
        </div>
        <div class="flex-1">
          <div class="flex flex-wrap items-center gap-3">
            <h1 class="text-2xl font-bold text-primary"><?= htmlspecialchars($user['username']) ?></h1>
            <span class="role-badge px-3 py-1 rounded-full text-sm font-medium">
              <?= ucfirst($user['role'] ?? 'user') ?>
            </span>
          </div>
          <p class="mt-2 text-muted text-sm">
            Verwalten Sie Ihre Profil-Informationen und Einstellungen
          </p>
        </div>
      </div>
    </div>
  </header>

  <!-- Settings Layout -->
  <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 max-w-6xl mx-auto">
    <!-- Settings Sidebar -->
    <aside class="lg:col-span-1">
      <div class="settings-sidebar p-6 sticky top-8">
        <h3 class="font-semibold text-primary mb-4">Einstellungen</h3>
        
        <!-- Search/Filter -->
        <input id="settingsFilter" type="text" 
               placeholder="Suchen..." 
               class="form-input w-full px-4 py-2 text-sm mb-4">
        
        <!-- Categories -->
        <nav id="settingsList" class="space-y-2">
          <?php
          $categories = [
            'personal_info' => [
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>',
              'label' => 'Persönliche Daten',
              'description' => 'Name, E-Mail, Telefon'
            ],
            'finance' => [
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
              'label' => 'Finanzen',
              'description' => 'Ein- & Ausgaben verwalten'
            ],
            'documents' => [
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>',
              'label' => 'Dokumente',
              'description' => 'Verträge, Rechnungen, etc.'
            ],
            'security' => [
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>',
              'label' => 'Sicherheit',
              'description' => 'Passwort & 2FA'
            ],
            'notifications' => [
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 17H4l5 5v-5zM9 7v10m6-10v10"></path>',
              'label' => 'Benachrichtigungen',
              'description' => 'E-Mail & Push-Benachrichtigungen'
            ]
          ];

          foreach ($categories as $catKey => $cat):
            $isActive = $activeTab === $catKey;
          ?>
            <a href="?tab=<?= $catKey ?>" 
               class="filter-item nav-item <?= $isActive ? 'active' : '' ?> group flex items-start p-3 transition-all duration-200">
              <svg class="w-5 h-5 mt-0.5 mr-3 flex-shrink-0" 
                   fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <?= $cat['icon'] ?>
              </svg>
              <div class="flex-1 min-w-0">
                <div class="font-medium text-sm"><?= $cat['label'] ?></div>
                <div class="text-xs text-muted mt-0.5">
                  <?= $cat['description'] ?>
                </div>
              </div>
            </a>
          <?php endforeach; ?>
        </nav>
      </div>
    </aside>

    <!-- Content Area -->
    <section class="lg:col-span-3">
      <div class="settings-content p-8">
        <?php
        // Determine content based on active tab
        switch($activeTab) {
          case 'finance':
            $sidebarCats = [
              'finance_overview' => 'Übersicht',
              'income' => 'Einnahmen',
              'expenses' => 'Ausgaben',
              'budgets' => 'Budgets',
              'balance' => 'Kontostand'
            ];
            break;
          case 'documents':
            $sidebarCats = [
              'documents' => 'Alle Dokumente',
              'contracts' => 'Verträge',
              'invoices' => 'Rechnungen',
              'ids' => 'Ausweise/ID',
              'insurance' => 'Versicherungen',
              'other_docs' => 'Sonstige Dokumente'
            ];
            break;
          case 'security':
            $securityTabPath = __DIR__ . '/profile_tabs/security.php';
            if (file_exists($securityTabPath)) {
                include $securityTabPath;
            } else {
                echo '<div class="text-center py-12"><p class="text-muted">Security settings will be available soon.</p></div>';
            }
            break;
          case 'notifications':
            $notificationsTabPath = __DIR__ . '/profile_tabs/notifications.php';
            if (file_exists($notificationsTabPath)) {
                include $notificationsTabPath;
            } else {
                echo '<div class="text-center py-12"><p class="text-muted">Notification settings will be available soon.</p></div>';
            }
            break;
          default: // personal_info
            $sidebarCats = [
              'public_profile' => 'Public profile',
              'hr_information' => 'HR information',
              'personal_data' => 'Personal data'
            ];
        }

        // For tabs that have subtabs, show subtab navigation and content
        if (in_array($activeTab, ['personal_info', 'finance', 'documents'])):
          $sub = $_GET['subtab'] ?? array_key_first($sidebarCats);
        ?>
          <!-- Subtab Navigation -->
          <nav class="subtab-nav flex space-x-1 mb-8">
            <?php foreach($sidebarCats as $catKey => $catLabel): 
              $isActive = $sub === $catKey;
            ?>
              <a href="?tab=<?= $activeTab ?>&subtab=<?= $catKey ?>" 
                 class="subtab-item <?= $isActive ? 'active' : '' ?> px-4 py-2 text-sm font-medium transition-all duration-200">
                <?= $catLabel ?>
              </a>
            <?php endforeach; ?>
          </nav>

          <!-- Subtab Content -->
          <?php
          $filePath = __DIR__ . "/profile_tabs/{$activeTab}/{$sub}.php";
          if (file_exists($filePath)) {
              include $filePath;
          } else {
              echo '<div class="text-center py-12">';
              echo '<p class="text-muted">Kein Inhalt verfügbar für diese Kategorie.</p>';
              echo '<p class="text-xs text-muted mt-2">Looking for: ' . htmlspecialchars($filePath) . '</p>';
              echo '</div>';
          }
          ?>
        <?php endif; ?>
      </div>
    </section>
  </div>
</div>

<script>
// Settings filter functionality
document.getElementById('settingsFilter').addEventListener('input', e => {
  const filter = e.target.value.toLowerCase();
  document.querySelectorAll('#settingsList .filter-item').forEach(item => {
    const text = item.textContent.toLowerCase();
    item.style.display = text.includes(filter) ? '' : 'none';
  });
});
</script>
</body>
</html>
