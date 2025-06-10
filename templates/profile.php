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
    body { font-family: 'Inter', sans-serif; }
    @media (max-width: 768px) {
      main { margin-top: 4rem; /* Adjusted for dark gradient navbar */ }
    }
    @media (min-width: 769px) {
      .main-profile-content { 
        margin-left: 17rem !important;
        margin-top: 1rem;
        margin-right: 1rem;
        margin-bottom: 1rem;
      }
    }
    .settings-sidebar {
      background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
      border-right: 1px solid #e2e8f0;
    }
    .settings-content {
      background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#eef7ff] via-[#f7fbff] to-[#f9fdf2] flex">
<?php 
$navbarPath = __DIR__ . '/navbar.php';
if (file_exists($navbarPath)) {
    require_once $navbarPath;
} else {
    echo '<!-- Dark gradient navbar not found at: ' . $navbarPath . ' -->';
}
?>

<!-- Main Content -->
<div class="main-profile-content ml-0 md:ml-64 flex-1 p-4 md:p-8 mt-14 md:mt-0">
  <!-- Header with user info and breadcrumb -->
  <header class="bg-white/60 backdrop-blur-sm shadow-sm rounded-2xl mb-8">
    <div class="max-w-6xl mx-auto px-8 py-6">
      <!-- Breadcrumb -->
      <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-4">
        <a href="/privatevault/dashboard.php" class="hover:text-[#4A90E2]">Dashboard</a>
        <span>&rsaquo;</span>
        <span class="text-gray-900 font-medium">Profil Einstellungen</span>
      </nav>
      
      <!-- User Header -->
      <div class="flex items-center gap-6">
        <div class="h-20 w-20 rounded-full bg-gradient-to-br from-[#4A90E2] to-[#667eea] flex items-center justify-center text-2xl font-bold text-white shadow-lg">
          <?= $initials ?>
        </div>
        <div class="flex-1">
          <div class="flex flex-wrap items-center gap-3">
            <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($user['username']) ?></h1>
            <span class="px-3 py-1 rounded-full bg-[#4A90E2]/10 text-[#4A90E2] text-sm font-medium"><?= ucfirst($user['role'] ?? 'user') ?></span>
          </div>
          <p class="mt-2 text-gray-600 text-sm">
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
      <div class="settings-sidebar rounded-2xl shadow-sm p-6 sticky top-8">
        <h3 class="font-semibold text-gray-900 mb-4">Einstellungen</h3>
        
        <!-- Search/Filter -->
        <input id="settingsFilter" type="text" 
               placeholder="Suchen..." 
               class="w-full px-4 py-2 rounded-lg bg-white border border-gray-200 focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2] text-sm mb-4">
        
        <!-- Categories -->
        <nav id="settingsList" class="space-y-1">
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
               class="filter-item group flex items-start p-3 rounded-lg transition-all duration-200 <?= $isActive ? 'bg-[#4A90E2] text-white shadow-md' : 'hover:bg-white hover:shadow-sm text-gray-700' ?>">
              <svg class="w-5 h-5 mt-0.5 mr-3 flex-shrink-0 <?= $isActive ? 'text-white' : 'text-gray-400 group-hover:text-[#4A90E2]' ?>" 
                   fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <?= $cat['icon'] ?>
              </svg>
              <div class="flex-1 min-w-0">
                <div class="font-medium text-sm"><?= $cat['label'] ?></div>
                <div class="text-xs <?= $isActive ? 'text-white/80' : 'text-gray-500' ?> mt-0.5">
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
      <div class="settings-content rounded-2xl shadow-sm p-8">
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
                echo '<div class="text-center py-12"><p class="text-gray-500">Security settings will be available soon.</p></div>';
            }
            break;
          case 'notifications':
            $notificationsTabPath = __DIR__ . '/profile_tabs/notifications.php';
            if (file_exists($notificationsTabPath)) {
                include $notificationsTabPath;
            } else {
                echo '<div class="text-center py-12"><p class="text-gray-500">Notification settings will be available soon.</p></div>';
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
          <nav class="flex space-x-1 mb-8 bg-gray-100 rounded-lg p-1">
            <?php foreach($sidebarCats as $catKey => $catLabel): 
              $isActive = $sub === $catKey;
            ?>
              <a href="?tab=<?= $activeTab ?>&subtab=<?= $catKey ?>" 
                 class="px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 <?= $isActive ? 'bg-white text-[#4A90E2] shadow-sm' : 'text-gray-600 hover:text-gray-900' ?>">
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
              echo '<p class="text-gray-500">Kein Inhalt verfügbar für diese Kategorie.</p>';
              echo '<p class="text-xs text-gray-400 mt-2">Looking for: ' . htmlspecialchars($filePath) . '</p>';
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
