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
      height: fit-content;
    }
    
    /* Content area styling */
    .settings-content {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1.5rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      min-height: calc(100vh - 12rem);
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
    
    /* Select elements */
    .form-select {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      border-radius: 0.75rem;
      transition: all 0.3s ease;
    }
    
    .form-select:focus {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.4);
      outline: none;
      box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
    }
    
    .form-select option {
      background: #2d1b69;
      color: white;
    }
    
    /* Navigation items */
    .nav-item {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 0.75rem;
      transition: all 0.3s ease;
      color: rgba(255, 255, 255, 0.9);
      display: flex;
      align-items: center;
      padding: 1rem;
      text-decoration: none;
      margin-bottom: 0.5rem;
    }
    
    .nav-item:hover {
      background: rgba(255, 255, 255, 0.1);
      border-color: rgba(255, 255, 255, 0.2);
      transform: translateX(3px);
      color: white;
      text-decoration: none;
    }
    
    .nav-item.active {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.6) 0%, rgba(79, 70, 229, 0.6) 100%);
      border-color: rgba(147, 51, 234, 0.4);
      color: white;
      font-weight: 600;
    }
    
    .nav-item-icon {
      width: 1.5rem;
      height: 1.5rem;
      margin-right: 1rem;
      flex-shrink: 0;
      color: rgba(255, 255, 255, 0.7);
    }
    
    .nav-item-content {
      flex: 1;
    }
    
    .nav-item-title {
      font-weight: 600;
      font-size: 0.95rem;
      margin-bottom: 0.25rem;
      color: white;
    }
    
    .nav-item-description {
      font-size: 0.8rem;
      opacity: 0.7;
      line-height: 1.3;
      color: rgba(255, 255, 255, 0.6);
    }
    
    /* Text colors for dark theme */
    .text-primary {
      color: #c084fc !important;
    }
    
    .text-muted {
      color: rgba(255, 255, 255, 0.6) !important;
    }
    
    .text-secondary {
      color: rgba(255, 255, 255, 0.8) !important;
    }
    
    /* Labels and form text */
    label {
      color: white !important;
      font-weight: 500;
      margin-bottom: 0.5rem;
    }
    
    .form-text {
      color: rgba(255, 255, 255, 0.6) !important;
    }
    
    /* Breadcrumb styling */
    .breadcrumb-item a {
      color: rgba(255, 255, 255, 0.8);
      text-decoration: none;
    }
    
    .breadcrumb-item a:hover {
      color: white;
    }
    
    .breadcrumb-item.active {
      color: #c084fc;
    }
    
    /* Profile avatar */
    .profile-avatar {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 700;
      border: 3px solid rgba(255,255,255,0.3);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }
    
    /* Role badge */
    .role-badge {
      background: rgba(147, 51, 234, 0.2);
      color: #c4b5fd;
      border: 1px solid rgba(147, 51, 234, 0.3);
      padding: 0.25rem 0.75rem;
      border-radius: 9999px;
      font-size: 0.875rem;
    }
    
    /* Buttons */
    .btn-primary {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.8) 0%, rgba(79, 70, 229, 0.8) 100%);
      border: 1px solid rgba(147, 51, 234, 0.3);
      color: white;
      border-radius: 0.75rem;
      transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.9) 0%, rgba(79, 70, 229, 0.9) 100%);
      transform: translateY(-1px);
      box-shadow: 0 4px 15px rgba(147, 51, 234, 0.3);
      color: white;
    }
    
    .btn-secondary {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      border-radius: 0.75rem;
      transition: all 0.3s ease;
    }
    
    .btn-secondary:hover {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.3);
      color: white;
    }
    
    /* Subtab navigation */
    .subtab-nav {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 0.75rem;
      margin-bottom: 1.5rem;
    }
    
    .subtab-link {
      color: rgba(255, 255, 255, 0.7);
      padding: 0.75rem 1rem;
      border-radius: 0.5rem;
      text-decoration: none;
      transition: all 0.3s ease;
      margin: 0.125rem;
    }
    
    .subtab-link:hover {
      background: rgba(255, 255, 255, 0.1);
      color: white;
      text-decoration: none;
    }
    
    .subtab-link.active {
      background: rgba(147, 51, 234, 0.6);
      color: white;
      font-weight: 600;
    }
    
    /* Cards and content areas */
    .content-card {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 1rem;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
    }
    
    .content-card h3 {
      color: white;
      margin-bottom: 1rem;
    }
    
    /* Table styling for dark theme */
    .table {
      color: white;
    }
    
    .table th {
      color: rgba(255, 255, 255, 0.9);
      border-color: rgba(255, 255, 255, 0.1);
    }
    
    .table td {
      color: rgba(255, 255, 255, 0.8);
      border-color: rgba(255, 255, 255, 0.1);
    }
    
    .table-hover tbody tr:hover {
      background: rgba(255, 255, 255, 0.05);
    }
    
    /* Success/Error messages */
    .alert {
      border-radius: 0.75rem;
      border: none;
    }
    
    .alert-success {
      background: rgba(34, 197, 94, 0.2);
      color: #86efac;
      border: 1px solid rgba(34, 197, 94, 0.3);
    }
    
    .alert-danger {
      background: rgba(239, 68, 68, 0.2);
      color: #fca5a5;
      border: 1px solid rgba(239, 68, 68, 0.3);
    }
    
    /* Full-width layout adjustments */
    .profile-layout {
      min-height: calc(100vh - 4rem);
    }
    
    @media (min-width: 769px) {
      .profile-layout {
        min-height: 100vh;
        margin-left: 16rem;
      }
    }
    
    /* Mobile adjustments */
    @media (max-width: 768px) {
      main { margin-top: 4rem; }
      .profile-header { margin: 1rem; }
      
      .grid-cols-1.xl\\:grid-cols-5 {
        grid-template-columns: 1fr;
      }
      
      .xl\\:col-span-1,
      .xl\\:col-span-4 {
        grid-column: span 1;
      }
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

<!-- Main Content - Full Width -->
<div class="profile-layout flex-1 p-4 md:p-6 mt-14 md:mt-0">
  <!-- Header with user info and breadcrumb -->
  <header class="profile-header mb-6">
    <div class="max-w-full mx-auto px-6 py-6">
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
            <h1 class="text-3xl font-bold text-primary"><?= htmlspecialchars($user['username']) ?></h1>
            <span class="role-badge">
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

  <!-- Settings Layout - Full Width Grid -->
  <div class="grid grid-cols-1 xl:grid-cols-5 gap-6 max-w-full mx-auto">
    <!-- Settings Sidebar -->
    <aside class="xl:col-span-1">
      <div class="settings-sidebar p-6 sticky top-6">
        <h3 class="font-semibold text-primary mb-4">Einstellungen</h3>
        
        <!-- Search/Filter -->
        <input id="settingsFilter" type="text" 
               placeholder="Suchen..." 
               class="form-input w-full px-4 py-2 text-sm mb-4">
        
        <!-- Categories -->
        <nav id="settingsList" class="space-y-1">
          <?php
          $categories = [
            'personal_info' => [
              'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="nav-item-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>',
              'title' => 'Persönliche Daten',
              'description' => 'Name, E-Mail, Telefon'
            ],
            'finance' => [
              'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="nav-item-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V4m0 12v4"/></svg>',
              'title' => 'Finanzen',
              'description' => 'Ein- & Ausgaben verwalten'
            ],
            'documents' => [
              'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="nav-item-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
              'title' => 'Dokumente',
              'description' => 'Verträge, Rechnungen, etc.'
            ],
            'security' => [
              'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="nav-item-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>',
              'title' => 'Sicherheit',
              'description' => 'Passwort & Privatsphäre'
            ]
          ];
          
          foreach ($categories as $key => $category):
            $isActive = $activeTab === $key;
            $href = "?tab=" . $key;
          ?>
            <a href="<?= $href ?>" class="nav-item <?= $isActive ? 'active' : '' ?>" data-category="<?= $key ?>">
              <?= $category['icon'] ?>
              <div class="nav-item-content">
                <div class="nav-item-title"><?= $category['title'] ?></div>
                <div class="nav-item-description"><?= $category['description'] ?></div>
              </div>
            </a>
          <?php endforeach; ?>
        </nav>
      </div>
    </aside>
    
    <!-- Content Area - Full Width -->
    <main class="xl:col-span-4">
      <div class="settings-content p-6">
        <?php
        // Tab content rendering
        switch ($activeTab) {
          case 'personal_info':
            // Subtab navigation for personal_info
            $personalSubtabs = [
              'personal_data' => 'Persönliche Daten',
              'public_profile' => 'Öffentliches Profil',
              'hr_information' => 'HR Informationen'
            ];
            $currentPersonalSubtab = $_GET['subtab'] ?? 'personal_data';
            
            echo '<div class="mb-6">';
            echo '<div class="subtab-nav p-1">';
            echo '<div class="flex flex-wrap gap-1">';
            foreach ($personalSubtabs as $key => $label) {
              $isActive = $currentPersonalSubtab === $key;
              $href = "?tab=personal_info&subtab=" . $key;
              echo '<a href="' . $href . '" class="subtab-item px-4 py-2 ' . ($isActive ? 'active' : '') . '">' . htmlspecialchars($label) . '</a>';
            }
            echo '</div>';
            echo '</div>';
            echo '</div>';
            
            // Include the appropriate subtab template
            $subtabFile = __DIR__ . "/profile_tabs/personal_info/{$currentPersonalSubtab}.php";
            if (file_exists($subtabFile)) {
              include $subtabFile;
            } else {
              echo '<div class="text-center py-12 text-muted">Subtab-Inhalt nicht gefunden.</div>';
            }
            break;
            
          case 'finance':
            $financeSubtabs = [
              'finance_overview' => 'Übersicht',
              'income' => 'Einnahmen',
              'expenses' => 'Ausgaben'
            ];
            $currentFinanceSubtab = $_GET['subtab'] ?? 'finance_overview';
            
            echo '<div class="mb-6">';
            echo '<div class="subtab-nav p-1">';
            echo '<div class="flex flex-wrap gap-1">';
            foreach ($financeSubtabs as $key => $label) {
              $isActive = $currentFinanceSubtab === $key;
              $href = "?tab=finance&subtab=" . $key;
              echo '<a href="' . $href . '" class="subtab-item px-4 py-2 ' . ($isActive ? 'active' : '') . '">' . htmlspecialchars($label) . '</a>';
            }
            echo '</div>';
            echo '</div>';
            echo '</div>';
            
            // Include the appropriate finance subtab template
            $subtabFile = __DIR__ . "/profile_tabs/finance/{$currentFinanceSubtab}.php";
            if (file_exists($subtabFile)) {
              include $subtabFile;
            } else {
              // Fallback to finance overview
              include __DIR__ . '/profile_tabs/finance/finance_overview.php';
            }
            break;
            
          case 'documents':
            $docSubtabs = [
              'documents' => 'Alle Dokumente',
              'contracts' => 'Verträge',
              'invoices' => 'Rechnungen',
              'insurance' => 'Versicherungen',
              'other_docs' => 'Sonstige'
            ];
            $currentDocSubtab = $_GET['subtab'] ?? 'documents';
            
            echo '<div class="mb-6">';
            echo '<div class="subtab-nav p-1">';
            echo '<div class="flex flex-wrap gap-1">';
            foreach ($docSubtabs as $key => $label) {
              $isActive = $currentDocSubtab === $key;
              $href = "?tab=documents&subtab=" . $key;
              echo '<a href="' . $href . '" class="subtab-item px-4 py-2 ' . ($isActive ? 'active' : '') . '">' . htmlspecialchars($label) . '</a>';
            }
            echo '</div>';
            echo '</div>';
            echo '</div>';
            
            // Include the appropriate documents subtab template
            $subtabFile = __DIR__ . "/profile_tabs/documents/{$currentDocSubtab}.php";
            if (file_exists($subtabFile)) {
              include $subtabFile;
            } else {
              include __DIR__ . '/profile_tabs/documents/documents.php';
            }
            break;
            
          case 'security':
            echo '<div class="glassmorphism-container p-6">';
            echo '<h2 class="text-xl font-semibold text-primary mb-4">Sicherheitseinstellungen</h2>';
            echo '<div class="text-center py-12 text-muted">Sicherheitseinstellungen werden demnächst verfügbar sein.</div>';
            echo '</div>';
            break;
            
          default:
            echo '<div class="text-center py-12 text-muted">Tab-Inhalt nicht gefunden.</div>';
        }
        ?>
      </div>
    </main>
  </div>
</div>

<script>
// Enhanced search functionality
document.getElementById('settingsFilter').addEventListener('input', function(e) {
  const searchTerm = e.target.value.toLowerCase();
  const navItems = document.querySelectorAll('#settingsList .nav-item');
  
  navItems.forEach(item => {
    const title = item.querySelector('.nav-item-title').textContent.toLowerCase();
    const description = item.querySelector('.nav-item-description').textContent.toLowerCase();
    const matches = title.includes(searchTerm) || description.includes(searchTerm);
    
    item.style.display = matches ? 'flex' : 'none';
  });
});
</script>
</body>
</html>
