<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Datei-Explorer | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
      min-height: 100vh;
      transition: background 0.8s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow-x: hidden;
    }

    /* Dashboard-consistent background animation */
    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: 
        radial-gradient(circle at 20% 50%, rgba(147, 51, 234, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(79, 70, 229, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 40% 80%, rgba(236, 72, 153, 0.1) 0%, transparent 50%);
      animation: gradientShift 15s ease-in-out infinite;
      z-index: -1;
    }

    @keyframes gradientShift {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.7; }
    }

    /* Layout adjustments for navbar */
    .file-explorer-container {
      padding-top: 4rem;
    }
    
    @media (min-width: 769px) {
      .file-explorer-container {
        margin-left: 16rem;
        padding-top: 2rem;
      }
    }

    /* Dashboard-consistent glass effects */
    .glass-card {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 1.5rem;
      box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .glass-card:hover {
      background: rgba(255, 255, 255, 0.1);
      border-color: rgba(255, 255, 255, 0.18);
      transform: translateY(-2px);
      box-shadow: 
        0 12px 40px rgba(0, 0, 0, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.15);
    }

    .sidebar-glass {
      background: rgba(255, 255, 255, 0.06);
      backdrop-filter: blur(15px);
      border-right: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 0;
      box-shadow: 0 4px 24px rgba(0, 0, 0, 0.2);
    }

    /* File Cards */
    .file-card {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 1.5rem;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      cursor: pointer;
      animation: fadeInUp 0.5s ease-out;
    }

    .file-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
      transition: left 0.6s ease;
    }

    .file-card:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(255, 255, 255, 0.25);
      transform: translateY(-4px) scale(1.02);
      box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.4),
        0 0 0 1px rgba(255, 255, 255, 0.1);
    }

    .file-card:hover::before {
      left: 100%;
    }

    /* Navigation Items */
    .nav-item {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 1rem;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      cursor: pointer;
      text-decoration: none;
      color: rgba(255, 255, 255, 0.8);
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
      transform: translateX(4px);
      text-decoration: none;
      color: white;
    }

    .nav-item.active {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.3), rgba(79, 70, 229, 0.3));
      border-color: rgba(147, 51, 234, 0.5);
      transform: translateX(8px);
      color: white;
    }

    /* Dashboard-consistent buttons */
    .liquid-glass-btn-primary {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.8), rgba(79, 70, 229, 0.8));
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      border-radius: 0.75rem;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      text-decoration: none;
    }

    .liquid-glass-btn-primary:hover {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.9), rgba(79, 70, 229, 0.9));
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(147, 51, 234, 0.4);
      text-decoration: none;
      color: white;
    }

    .liquid-glass-btn-secondary {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      border-radius: 0.75rem;
      transition: all 0.3s ease;
      text-decoration: none;
    }

    .liquid-glass-btn-secondary:hover {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.3);
      transform: translateY(-1px);
      text-decoration: none;
      color: white;
    }

    .liquid-glass-btn-danger {
      background: rgba(239, 68, 68, 0.2);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(239, 68, 68, 0.3);
      color: #fca5a5;
      border-radius: 0.75rem;
      transition: all 0.3s ease;
    }

    .liquid-glass-btn-danger:hover {
      background: rgba(239, 68, 68, 0.3);
      border-color: rgba(239, 68, 68, 0.5);
      transform: translateY(-1px);
    }

    /* Stats Cards */
    .stats-card {
      background: linear-gradient(135deg, 
        rgba(255, 255, 255, 0.1) 0%, 
        rgba(255, 255, 255, 0.05) 100%);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1rem;
      transition: all 0.3s ease;
    }

    .stats-card:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(255, 255, 255, 0.25);
      transform: translateY(-2px);
    }

    /* Search Bar */
    .search-bar {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1rem;
      transition: all 0.3s ease;
    }

    .search-bar:focus-within {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(147, 51, 234, 0.5);
      box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.2);
    }

    /* View Toggle */
    .view-toggle {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 0.75rem;
    }

    .view-toggle-btn {
      transition: all 0.3s ease;
      border-radius: 0.5rem;
      background: transparent;
      border: none;
      color: rgba(255, 255, 255, 0.6);
      cursor: pointer;
    }

    .view-toggle-btn.active {
      background: rgba(147, 51, 234, 0.3);
      color: #a855f7;
    }

    .view-toggle-btn:hover {
      background: rgba(255, 255, 255, 0.1);
      color: white;
    }

    /* Table */
    .liquid-glass-table {
      background: rgba(255, 255, 255, 0.04);
      backdrop-filter: blur(25px);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 1.5rem;
      overflow: hidden;
    }

    .liquid-glass-table table {
      border-collapse: separate;
      border-spacing: 0;
      width: 100%;
    }

    .liquid-glass-table thead tr {
      background: rgba(255, 255, 255, 0.05);
    }

    .liquid-glass-table tbody tr:hover {
      background: rgba(255, 255, 255, 0.08);
    }

    .liquid-glass-table th,
    .liquid-glass-table td {
      padding: 1rem 1.5rem;
      text-align: left;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    /* File Icons */
    .file-icon-container {
      width: 3rem;
      height: 3rem;
      border-radius: 0.75rem;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
    }

    .file-card:hover .file-icon-container {
      transform: scale(1.1);
      background: rgba(255, 255, 255, 0.15);
    }

    /* Animations */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .file-card:nth-child(2n) {
      animation-delay: 0.1s;
    }

    .file-card:nth-child(3n) {
      animation-delay: 0.2s;
    }

    /* Custom scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
      width: 8px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.2);
      border-radius: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
      background: rgba(255, 255, 255, 0.3);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .file-explorer-container {
        padding-top: 5rem;
        margin-left: 0;
      }
      
      .glass-card, .file-card { 
        border-radius: 1rem; 
      }
      
      .nav-item { 
        border-radius: 0.75rem; 
      }
    }
  </style>
</head>

<body class="h-full">
  <?php require_once __DIR__ . '/navbar.php'; ?>

  <div class="file-explorer-container">
    <div class="flex h-full">
      <!-- Sidebar -->
      <div class="sidebar-glass w-80 flex-shrink-0 p-6 overflow-y-auto custom-scrollbar">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-2xl font-bold text-white mb-2 bg-gradient-to-r from-purple-400 to-blue-400 bg-clip-text text-transparent">
            Datei-Explorer
          </h1>
          <p class="text-white/60 text-sm">Verwalten Sie alle Ihre Dateien</p>
        </div>

        <!-- Quick Stats -->
        <div class="mb-8 space-y-4">
          <div class="stats-card p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-white/60 text-sm">Dateien gesamt</p>
                <p class="text-white text-xl font-bold"><?= $totalFiles ?></p>
              </div>
              <div class="w-10 h-10 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-file text-blue-400"></i>
              </div>
            </div>
          </div>
          
          <div class="stats-card p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-white/60 text-sm">Speicher belegt</p>
                <p class="text-white text-lg font-bold"><?= formatFileSize($totalSize) ?></p>
              </div>
              <div class="w-10 h-10 bg-gradient-to-br from-green-500/20 to-emerald-500/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-hdd text-green-400"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- File Type Navigation -->
        <div class="space-y-3">
          <h3 class="text-white font-medium text-sm uppercase tracking-wide mb-4">Dateitypen</h3
          >
          
          <a href="?<?= http_build_query(array_merge($_GET, ['type' => ''])) ?>" 
             class="nav-item p-3 flex items-center gap-3 <?= $filterType === '' ? 'active' : '' ?>">
            <i class="fas fa-th w-4"></i>
            <span>Alle Dateien</span>
            <span class="ml-auto text-xs bg-white/10 px-2 py-1 rounded-full"><?= $totalFiles ?></span>
          </a>

          <a href="?<?= http_build_query(array_merge($_GET, ['type' => 'documents'])) ?>" 
             class="nav-item p-3 flex items-center gap-3 <?= $filterType === 'documents' ? 'active' : '' ?>">
            <i class="fas fa-file-alt text-blue-400 w-4"></i>
            <span>Dokumente</span>
            <span class="ml-auto text-xs bg-white/10 px-2 py-1 rounded-full">
              <?= array_sum(array_intersect_key($typeCounts, array_flip($fileTypes['documents']))) ?>
            </span>
          </a>

          <a href="?<?= http_build_query(array_merge($_GET, ['type' => 'images'])) ?>" 
             class="nav-item p-3 flex items-center gap-3 <?= $filterType === 'images' ? 'active' : '' ?>">
            <i class="fas fa-image text-green-400 w-4"></i>
            <span>Bilder</span>
            <span class="ml-auto text-xs bg-white/10 px-2 py-1 rounded-full">
              <?= array_sum(array_intersect_key($typeCounts, array_flip($fileTypes['images']))) ?>
            </span>
          </a>

          <a href="?<?= http_build_query(array_merge($_GET, ['type' => 'videos'])) ?>" 
             class="nav-item p-3 flex items-center gap-3 <?= $filterType === 'videos' ? 'active' : '' ?>">
            <i class="fas fa-video text-pink-400 w-4"></i>
            <span>Videos</span>
            <span class="ml-auto text-xs bg-white/10 px-2 py-1 rounded-full">
              <?= array_sum(array_intersect_key($typeCounts, array_flip($fileTypes['videos']))) ?>
            </span>
          </a>

          <a href="?<?= http_build_query(array_merge($_GET, ['type' => 'audio'])) ?>" 
             class="nav-item p-3 flex items-center gap-3 <?= $filterType === 'audio' ? 'active' : '' ?>">
            <i class="fas fa-music text-purple-400 w-4"></i>
            <span>Audio</span>
            <span class="ml-auto text-xs bg-white/10 px-2 py-1 rounded-full">
              <?= array_sum(array_intersect_key($typeCounts, array_flip($fileTypes['audio']))) ?>
            </span>
          </a>

          <a href="?<?= http_build_query(array_merge($_GET, ['type' => 'archives'])) ?>" 
             class="nav-item p-3 flex items-center gap-3 <?= $filterType === 'archives' ? 'active' : '' ?>">
            <i class="fas fa-archive text-yellow-400 w-4"></i>
            <span>Archive</span>
            <span class="ml-auto text-xs bg-white/10 px-2 py-1 rounded-full">
              <?= array_sum(array_intersect_key($typeCounts, array_flip($fileTypes['archives']))) ?>
            </span>
          </a>

          <a href="?<?= http_build_query(array_merge($_GET, ['type' => 'code'])) ?>" 
             class="nav-item p-3 flex items-center gap-3 <?= $filterType === 'code' ? 'active' : '' ?>">
            <i class="fas fa-code text-cyan-400 w-4"></i>
            <span>Code</span>
            <span class="ml-auto text-xs bg-white/10 px-2 py-1 rounded-full">
              <?= array_sum(array_intersect_key($typeCounts, array_flip($fileTypes['code']))) ?>
            </span>
          </a>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 space-y-3">
          <h3 class="text-white font-medium text-sm uppercase tracking-wide mb-4">Aktionen</h3>
          
          <a href="/upload.php" class="liquid-glass-btn-primary block w-full p-3 rounded-lg text-white text-center font-medium">
            <i class="fas fa-plus mr-2"></i>
            Datei hochladen
          </a>
          
          <button onclick="window.print()" class="nav-item p-3 w-full text-left text-white/80 hover:text-white">
            <i class="fas fa-print mr-3"></i>
            Liste drucken
          </button>
        </div>
      </div>

      <!-- Main Content -->
      <div class="flex-1 flex flex-col min-w-0">
        <!-- Header Bar -->
        <div class="glass-card m-6 mb-4">
          <div class="p-6">
            <div class="flex items-center justify-between">
              <!-- Search & Filter -->
              <div class="flex items-center gap-4 flex-1">
                <div class="search-bar flex-1 max-w-md">
                  <form method="GET" class="flex items-center">
                    <?php foreach ($_GET as $key => $value): ?>
                      <?php if ($key !== 'search'): ?>
                        <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
                      <?php endif; ?>
                    <?php endforeach; ?>
                    <div class="relative w-full">
                      <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-white/50"></i>
                      <input 
                        type="text" 
                        name="search" 
                        value="<?= htmlspecialchars($searchQuery) ?>"
                        placeholder="Dateien durchsuchen..." 
                        class="w-full pl-10 pr-4 py-2 bg-transparent text-white placeholder-white/50 focus:outline-none"
                      >
                    </div>
                  </form>
                </div>
              </div>

              <!-- View Toggle -->
              <div class="view-toggle p-1 flex gap-1">
                <button onclick="switchView('grid')" 
                        class="view-toggle-btn px-3 py-2 text-white/60 hover:text-white <?= $currentView === 'grid' ? 'active' : '' ?>">
                  <i class="fas fa-th"></i>
                </button>
                <button onclick="switchView('list')" 
                        class="view-toggle-btn px-3 py-2 text-white/60 hover:text-white <?= $currentView === 'list' ? 'active' : '' ?>">
                  <i class="fas fa-list"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- File Content -->
        <div class="flex-1 p-6 pt-0 overflow-y-auto custom-scrollbar">
          <?php if (empty($files)): ?>
            <!-- Empty State -->
            <div class="glass-card p-12 text-center">
              <div class="w-24 h-24 bg-gradient-to-br from-purple-500/20 to-blue-500/20 rounded-3xl flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-folder-open text-4xl text-white/40"></i>
              </div>
              <h3 class="text-xl font-semibold text-white mb-2">
                <?= $searchQuery ? 'Keine Suchergebnisse gefunden' : 'Keine Dateien vorhanden' ?>
              </h3>
              <p class="text-white/60 mb-6">
                <?= $searchQuery ? 'Versuchen Sie es mit anderen Suchbegriffen' : 'Laden Sie Ihre erste Datei hoch, um loszulegen' ?>
              </p>
              <a href="/upload.php" class="liquid-glass-btn-primary inline-block px-6 py-3 rounded-lg text-white font-medium">
                <i class="fas fa-plus mr-2"></i>
                Erste Datei hochladen
              </a>
            </div>
          <?php else: ?>
            <!-- Grid View -->
            <div id="gridView" class="<?= $currentView === 'grid' ? 'block' : 'hidden' ?>">
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6">
                <?php foreach ($files as $file): ?>
                  <?php 
                  $fileInfo = getFileIcon($file['filename']);
                  $ext = strtolower(pathinfo($file['filename'], PATHINFO_EXTENSION));
                  ?>
                  <div class="file-card p-6 group" data-file-id="<?= $file['id'] ?>">
                    <div class="flex flex-col h-full">
                      <!-- File Icon -->
                      <div class="mb-4 flex-shrink-0">
                        <div class="file-icon-container mx-auto">
                          <svg class="w-6 h-6 <?= $fileInfo['color'] ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $fileInfo['icon'] ?>"/>
                          </svg>
                        </div>
                      </div>

                      <!-- File Info -->
                      <div class="flex-1 text-center">
                        <h3 class="text-white font-medium text-sm mb-1 truncate" title="<?= htmlspecialchars($file['display_name']) ?>">
                          <?= htmlspecialchars($file['display_name']) ?>
                        </h3>
                        <p class="text-white/60 text-xs mb-2"><?= htmlspecialchars($file['category_name'] ?? 'Keine Kategorie') ?></p>
                        <p class="text-white/40 text-xs"><?= date('d.m.Y', strtotime($file['upload_date'])) ?></p>
                      </div>

                      <!-- Actions -->
                      <div class="mt-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="/download.php?id=<?= $file['id'] ?>" 
                           class="flex-1 text-center py-2 px-3 liquid-glass-btn-secondary text-xs">
                          <i class="fas fa-download mr-1"></i>
                          Download
                        </a>
                        <button onclick="deleteFile(<?= $file['id'] ?>)" 
                                class="py-2 px-3 liquid-glass-btn-danger text-xs">
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>

            <!-- List View -->
            <div id="listView" class="<?= $currentView === 'list' ? 'block' : 'hidden' ?>">
              <div class="liquid-glass-table overflow-hidden">
                <table class="w-full">
                  <thead>
                    <tr class="border-b border-white/10">
                      <th class="text-left py-4 px-6 font-medium text-white/80">Name</th>
                      <th class="text-left py-4 px-6 font-medium text-white/80">Typ</th>
                      <th class="text-left py-4 px-6 font-medium text-white/80">Kategorie</th>
                      <th class="text-left py-4 px-6 font-medium text-white/80">Datum</th>
                      <th class="text-right py-4 px-6 font-medium text-white/80">Aktionen</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($files as $file): ?>
                      <?php 
                      $fileInfo = getFileIcon($file['filename']);
                      $ext = strtolower(pathinfo($file['filename'], PATHINFO_EXTENSION));
                      ?>
                      <tr class="border-b border-white/5 hover:bg-white/5 transition-colors group">
                        <td class="py-4 px-6">
                          <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                              <svg class="w-4 h-4 <?= $fileInfo['color'] ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $fileInfo['icon'] ?>"/>
                              </svg>
                            </div>
                            <div>
                              <p class="text-white font-medium text-sm"><?= htmlspecialchars($file['display_name']) ?></p>
                              <p class="text-white/60 text-xs"><?= htmlspecialchars($file['filename']) ?></p>
                            </div>
                          </div>
                        </td>
                        <td class="py-4 px-6">
                          <span class="text-white/70 text-sm uppercase"><?= $ext ?></span>
                        </td>
                        <td class="py-4 px-6 text-white/70 text-sm"><?= htmlspecialchars($file['category_name'] ?? 'Keine Kategorie') ?></td>
                        <td class="py-4 px-6 text-white/70 text-sm"><?= date('d.m.Y', strtotime($file['upload_date'])) ?></td>
                        <td class="py-4 px-6 text-right">
                          <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="/download.php?id=<?= $file['id'] ?>" 
                               class="py-1 px-3 liquid-glass-btn-secondary text-xs">
                              Download
                            </a>
                            <button onclick="deleteFile(<?= $file['id'] ?>)" 
                                    class="py-1 px-3 liquid-glass-btn-danger text-xs">
                              Löschen
                            </button>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <script>
    // View switching
    function switchView(view) {
      const gridView = document.getElementById('gridView');
      const listView = document.getElementById('listView');
      const buttons = document.querySelectorAll('.view-toggle-btn');
      
      // Update URL
      const url = new URL(window.location);
      url.searchParams.set('view', view);
      window.history.replaceState({}, '', url);
      
      // Toggle views
      if (view === 'grid') {
        gridView.classList.remove('hidden');
        listView.classList.add('hidden');
      } else {
        gridView.classList.add('hidden');
        listView.classList.remove('hidden');
      }
      
      // Update buttons
      buttons.forEach(btn => btn.classList.remove('active'));
      event.currentTarget.classList.add('active');
    }

    // Delete file
    function deleteFile(fileId) {
      if (confirm('Sind Sie sicher, dass Sie diese Datei löschen möchten?')) {
        const url = new URL(window.location);
        url.searchParams.set('delete', fileId);
        window.location.href = url.toString();
      }
    }

    // Initialize animations
    document.addEventListener('DOMContentLoaded', function() {
      // Stagger animation for file cards
      const cards = document.querySelectorAll('.file-card');
      cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
          card.style.transition = 'all 0.4s ease';
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, index * 50);
      });

      // Search auto-submit
      const searchInput = document.querySelector('input[name="search"]');
      if (searchInput) {
        let timeout;
        searchInput.addEventListener('input', function() {
          clearTimeout(timeout);
          timeout = setTimeout(() => {
            this.form.submit();
          }, 500);
        });
      }
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
      if (e.ctrlKey && e.key === 'k') {
        e.preventDefault();
        document.querySelector('input[name="search"]').focus();
      }
    });
  </script>
</body>
</html>
                    <div class="flex flex-col h-full">
                      <!-- Enhanced File Icon -->
                      <div class="file-icon-container mx-auto mb-4">
                        <svg class="w-6 h-6 <?= $fileInfo['color'] ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $fileInfo['icon'] ?>"/>
                        </svg>
                      </div>

                      <!-- Enhanced File Info -->
                      <div class="flex-1 text-center">
                        <h3 class="text-white font-medium text-sm mb-1 truncate" title="<?= htmlspecialchars($file['title'] ?? $file['original_name'] ?? $file['filename']) ?>">
                          <?= htmlspecialchars($file['title'] ?? $file['original_name'] ?? $file['filename']) ?>
                        </h3>
                        <p class="text-white/60 text-xs mb-2"><?= htmlspecialchars($file['category_name'] ?? 'Keine Kategorie') ?></p>
                        <p class="text-white/40 text-xs"><?= date('d.m.Y', strtotime($file['upload_date'])) ?></p>
                      </div>

                      <!-- Enhanced Actions -->
                      <div class="mt-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button onclick="downloadFile('<?= urlencode($file['filename']) ?>', this)" 
                                class="download-btn action-btn success flex-1 text-center py-2 px-3 text-xs transition-all">
                          <i class="fas fa-download mr-1"></i>
                          <span class="download-text">Download</span>
                        </button>
                        <button onclick="deleteFile(<?= $file['id'] ?>)" 
                                class="action-btn danger py-2 px-3 text-xs transition-all">
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>

            <!-- Enhanced List View -->
            <div id="listView" class="<?= $currentView === 'list' ? 'block' : 'hidden' ?>">
              <div class="liquid-glass overflow-hidden">
                <table class="w-full">
                  <thead>
                    <tr class="border-b border-white/10">
                      <th class="text-left py-4 px-6 font-medium text-white/80">Name</th>
                      <th class="text-left py-4 px-6 font-medium text-white/80">Typ</th>
                      <th class="text-left py-4 px-6 font-medium text-white/80">Kategorie</th>
                      <th class="text-left py-4 px-6 font-medium text-white/80">Datum</th>
                      <th class="text-left py-4 px-6 font-medium text-white/80">Größe</th>
                      <th class="text-right py-4 px-6 font-medium text-white/80">Aktionen</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($files as $file): ?>
                      <?php 
                      $fileInfo = getFileIcon($file['filename']);
                      $ext = strtolower(pathinfo($file['filename'], PATHINFO_EXTENSION));
                      $filePath = __DIR__ . '/../uploads/' . $file['filename'];
                      $fileSize = file_exists($filePath) ? filesize($filePath) : 0;
                      ?>
                      <tr class="border-b border-white/5 hover:bg-white/5 transition-all duration-300 group">
                        <td class="py-4 px-6">
                          <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                              <svg class="w-4 h-4 <?= $fileInfo['color'] ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $fileInfo['icon'] ?>"/>
                              </svg>
                            </div>
                            <div>
                              <p class="text-white font-medium text-sm"><?= htmlspecialchars($file['title'] ?? $file['original_name'] ?? $file['filename']) ?></p>
                              <p class="text-white/60 text-xs"><?= htmlspecialchars($file['filename']) ?></p>
                            </div>
                          </div>
                        </td>
                        <td class="py-4 px-6">
                          <span class="text-white/70 text-sm uppercase"><?= $ext ?></span>
                        </td>
                        <td class="py-4 px-6 text-white/70 text-sm"><?= htmlspecialchars($file['category_name'] ?? 'Keine Kategorie') ?></td>
                        <td class="py-4 px-6 text-white/70 text-sm"><?= date('d.m.Y', strtotime($file['upload_date'])) ?></td>
                        <td class="py-4 px-6 text-white/70 text-sm"><?= formatFileSize($fileSize) ?></td>
                        <td class="py-4 px-6 text-right">
                          <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="downloadFile('<?= urlencode($file['filename']) ?>', this)" 
                                    class="download-btn action-btn success py-1 px-3 text-xs transition-all">
                              <span class="download-text">Download</span>
                            </button>
                            <button onclick="deleteFile(<?= $file['id'] ?>)" 
                                    class="action-btn danger py-1 px-3 text-xs transition-all">
                              Löschen
                            </button>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Enhanced Download functionality
    function downloadFile(filename, button) {
      const downloadText = button.querySelector('.download-text');
      const originalText = downloadText.textContent;
      
      button.classList.add('downloading');
      downloadText.textContent = 'Laden...';
      
      const link = document.createElement('a');
      link.href = `/uploads/${filename}`;
      link.download = filename;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      
      setTimeout(() => {
        button.classList.remove('downloading');
        downloadText.textContent = originalText;
      }, 1500);
    }

    // Enhanced View Switching
    function switchView(view) {
      const gridView = document.getElementById('gridView');
      const listView = document.getElementById('listView');
      const buttons = document.querySelectorAll('.view-toggle-btn');
      
      const url = new URL(window.location);
      url.searchParams.set('view', view);
      window.history.replaceState({}, '', url);
      
      if (view === 'grid') {
        gridView.classList.remove('hidden');
        listView.classList.add('hidden');
      } else {
        gridView.classList.add('hidden');
        listView.classList.remove('hidden');
      }
      
      buttons.forEach(btn => btn.classList.remove('active'));
      event.currentTarget.classList.add('active');
    }

    function deleteFile(fileId) {
      if (confirm('Sind Sie sicher, dass Sie diese Datei löschen möchten?')) {
        const url = new URL(window.location);
        url.searchParams.set('delete', fileId);
        window.location.href = url.toString();
      }
    }

    // Enhanced initialization
    document.addEventListener('DOMContentLoaded', function() {
      // Stagger animation for file cards
      const cards = document.querySelectorAll('.file-card');
      cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
          card.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, index * 100);
      });

      // Enhanced search functionality
      const searchInput = document.querySelector('input[name="search"]');
      if (searchInput) {
        let timeout;
        searchInput.addEventListener('input', function() {
          clearTimeout(timeout);
          timeout = setTimeout(() => {
            this.form.submit();
          }, 800);
        });
      }
    });

    // Enhanced keyboard shortcuts
    document.addEventListener('keydown', function(e) {
      if (e.ctrlKey && e.key === 'k') {
        e.preventDefault();
        document.querySelector('input[name="search"]').focus();
      }
      if (e.key === 'Escape') {
        document.querySelector('input[name="search"]').blur();
      }
      if (e.key === 'v' && !e.ctrlKey && !e.altKey) {
        const currentView = document.getElementById('gridView').classList.contains('hidden') ? 'list' : 'grid';
        const newView = currentView === 'grid' ? 'list' : 'grid';
        const targetButton = document.querySelector(`[onclick="switchView('${newView}')"]`);
        if (targetButton) {
          targetButton.click();
        }
      }
    });
  </script>
</body>
</html>
        listView.classList.remove('hidden');
      }
      
      // Update buttons
      buttons.forEach(btn => btn.classList.remove('active'));
      event.currentTarget.classList.add('active');
    }

    function deleteFile(fileId) {
      if (confirm('Sind Sie sicher, dass Sie diese Datei löschen möchten?')) {
        const url = new URL(window.location);
        url.searchParams.set('delete', fileId);
        window.location.href = url.toString();
      }
    }

    function initializeAnimations() {
      // Stagger animation for file cards
      const cards = document.querySelectorAll('.file-card, .folder-card');
      cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
          card.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, index * 50);
      });

      // Search auto-submit
      const searchInput = document.querySelector('input[name="search"]');
      if (searchInput) {
        let timeout;
        searchInput.addEventListener('input', function() {
          clearTimeout(timeout);
          timeout = setTimeout(() => {
            this.form.submit();
          }, 500);
        });
      }
    }

    function setupKeyboardShortcuts() {
      document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'k') {
          e.preventDefault();
          document.querySelector('input[name="search"]').focus();
        }
        if (e.key === 'Escape') {
          document.querySelector('input[name="search"]').blur();
        }
        // Grid/List view toggle with 'v' key
        if (e.key === 'v' && !e.ctrlKey && !e.altKey) {
          const currentView = document.getElementById('gridView').classList.contains('hidden') ? 'list' : 'grid';
          const newView = currentView === 'grid' ? 'list' : 'grid';
          const targetButton = document.querySelector(`[onclick="switchView('${newView}')"]`);
          if (targetButton) {
            targetButton.click();
          }
        }
      });
    }
  </script>
</body>
</html>
