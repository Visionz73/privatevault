<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Datei-Explorer | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body { 
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
      min-height: 100vh;
      position: relative;
      overflow-x: hidden;
    }

    /* Enhanced animated background */
    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: 
        radial-gradient(circle at 20% 50%, rgba(147, 51, 234, 0.08) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(79, 70, 229, 0.08) 0%, transparent 50%),
        radial-gradient(circle at 40% 80%, rgba(236, 72, 153, 0.08) 0%, transparent 50%);
      animation: gradientShift 20s ease-in-out infinite;
      z-index: -1;
    }

    @keyframes gradientShift {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.8; }
    }

    /* Layout adjustments with consistent spacing */
    .file-explorer-container {
      padding-top: 1rem;
      padding-left: 1rem;
      padding-right: 1rem;
      padding-bottom: 1rem;
    }
    
    @media (min-width: 769px) {
      .file-explorer-container {
        margin-left: 16rem;
        padding-top: 1.5rem;
        padding-left: 1.5rem;
        padding-right: 1.5rem;
        padding-bottom: 1.5rem;
      }
    }

    /* Standardized Liquid Glass Effects */
    .liquid-glass {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(20px) saturate(180%);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1.5rem;
      box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.25),
        inset 0 1px 0 rgba(255, 255, 255, 0.15);
      position: relative;
      overflow: hidden;
    }

    .liquid-glass::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
      z-index: 1;
    }

    .liquid-glass-header {
      background: linear-gradient(135deg, 
        rgba(255, 255, 255, 0.15) 0%, 
        rgba(255, 255, 255, 0.10) 100%);
      backdrop-filter: blur(25px) saturate(200%);
      border-bottom: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1.5rem 1.5rem 0 0;
      padding: 1.5rem;
      margin: 0;
    }

    /* Enhanced Sidebar with consistent spacing */
    .sidebar-glass {
      background: rgba(255, 255, 255, 0.06);
      backdrop-filter: blur(25px) saturate(200%);
      border-right: 1px solid rgba(255, 255, 255, 0.12);
      box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
      width: 20rem;
      padding: 1.5rem;
      gap: 1.5rem;
    }

    /* File Cards with consistent spacing */
    .file-card {
      background: rgba(255, 255, 255, 0.07);
      backdrop-filter: blur(15px) saturate(180%);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1.25rem;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      cursor: pointer;
      transform: translateZ(0);
      padding: 1.5rem;
      margin: 0.5rem;
    }

    .file-card:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(255, 255, 255, 0.25);
      transform: translateY(-4px) scale(1.02);
      box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.3),
        0 0 0 1px rgba(255, 255, 255, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }

    .file-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
      transition: left 0.6s ease;
      z-index: 1;
    }

    .file-card:hover::before {
      left: 100%;
    }

    /* Navigation Items with consistent spacing */
    .nav-item {
      background: rgba(255, 255, 255, 0.04);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 1rem;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      cursor: pointer;
      margin: 0.25rem 0;
      padding: 0.75rem 1rem;
    }
    
    .nav-item:hover {
      background: rgba(255, 255, 255, 0.08);
      border-color: rgba(255, 255, 255, 0.18);
      transform: translateX(4px);
    }

    .nav-item.active {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.25), rgba(79, 70, 229, 0.25));
      border-color: rgba(147, 51, 234, 0.4);
      transform: translateX(6px);
      box-shadow: 0 4px 20px rgba(147, 51, 234, 0.2);
    }

    /* Action Buttons with consistent styling */
    .action-btn {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 0.75rem;
      color: rgba(255, 255, 255, 0.8);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      padding: 0.5rem 1rem;
      margin: 0.25rem;
    }

    .action-btn:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(255, 255, 255, 0.25);
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }

    .action-btn.success {
      background: rgba(34, 197, 94, 0.15);
      border-color: rgba(34, 197, 94, 0.3);
      color: #86efac;
    }

    .action-btn.success:hover {
      background: rgba(34, 197, 94, 0.25);
      border-color: rgba(34, 197, 94, 0.4);
      color: white;
    }

    .action-btn.danger {
      background: rgba(239, 68, 68, 0.15);
      border-color: rgba(239, 68, 68, 0.3);
      color: #fca5a5;
    }

    .action-btn.danger:hover {
      background: rgba(239, 68, 68, 0.25);
      border-color: rgba(239, 68, 68, 0.4);
      color: white;
    }

    /* Search Bar with consistent spacing */
    .search-bar {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1rem;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      margin: 0 1rem;
    }

    .search-bar:focus-within {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(147, 51, 234, 0.5);
      box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.2);
      transform: translateY(-1px);
    }

    /* Stats Cards with consistent spacing */
    .stats-card {
      background: linear-gradient(135deg, 
        rgba(255, 255, 255, 0.12) 0%, 
        rgba(255, 255, 255, 0.06) 100%);
      backdrop-filter: blur(25px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1rem;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      padding: 1rem;
      margin: 0.5rem 0;
    }

    .stats-card:hover {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.25);
      transform: translateY(-2px);
    }

    /* Breadcrumb with consistent spacing */
    .breadcrumb {
      background: rgba(255, 255, 255, 0.06);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 1rem;
      padding: 0.875rem 1.25rem;
      margin: 0 0.5rem;
      position: relative;
      overflow: hidden;
    }

    /* Grid Layout with consistent spacing */
    .file-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 1.5rem;
      padding: 1.5rem;
    }

    /* List View with consistent spacing */
    .file-list {
      padding: 1.5rem;
    }

    .file-list .liquid-glass {
      margin: 0;
    }

    /* Mobile Responsive with proper spacing */
    @media (max-width: 768px) {
      .file-explorer-container {
        padding: 1rem;
      }
      
      .sidebar-glass {
        width: 100%;
        padding: 1rem;
      }
      
      .liquid-glass-header {
        padding: 1rem;
      }
      
      .file-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
        padding: 1rem;
      }
      
      .file-card {
        padding: 1rem;
        margin: 0.25rem;
      }
      
      .nav-item {
        padding: 0.625rem 0.875rem;
      }
      
      .stats-card {
        padding: 0.75rem;
      }
    }
  </style>
</head>
<body class="h-full overflow-hidden">
  <?php require_once __DIR__.'/../templates/navbar.php'; ?>

  <div class="file-explorer-container">
    <div class="flex h-full">
      <!-- Enhanced Sidebar with consistent spacing -->
      <div class="sidebar-glass flex-shrink-0 overflow-y-auto custom-scrollbar">
        <!-- Enhanced Header with proper spacing -->
        <div class="mb-6">
          <h1 class="text-2xl font-bold text-white mb-2 bg-gradient-to-r from-purple-400 via-pink-400 to-blue-400 bg-clip-text text-transparent">
            Datei-Explorer
          </h1>
          <p class="text-white/60 text-sm">Verwalten Sie alle Ihre Dateien elegant</p>
        </div>

        <!-- Enhanced Quick Stats with consistent spacing -->
        <div class="mb-6 space-y-3">
          <div class="stats-card">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-white/60 text-sm">Dateien gesamt</p>
                <p class="text-white text-xl font-bold"><?= $totalFiles ?></p>
              </div>
              <div class="w-10 h-10 bg-gradient-to-br from-blue-500/30 to-purple-500/30 rounded-xl flex items-center justify-center">
                <i class="fas fa-file text-blue-400"></i>
              </div>
            </div>
          </div>
          
          <div class="stats-card">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-white/60 text-sm">Speicher belegt</p>
                <p class="text-white text-lg font-bold"><?= formatFileSize($totalSize) ?></p>
              </div>
              <div class="w-10 h-10 bg-gradient-to-br from-green-500/30 to-emerald-500/30 rounded-xl flex items-center justify-center">
                <i class="fas fa-hdd text-green-400"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Enhanced File Type Navigation with consistent spacing -->
        <div class="space-y-2">
          <h3 class="text-white font-medium text-sm uppercase tracking-wide mb-4">Dateitypen</h3
          >
          
          <a href="?<?= http_build_query(array_merge($_GET, ['type' => ''])) ?>" 
             class="nav-item flex items-center gap-3 text-white/80 hover:text-white text-sm <?= $filterType === '' ? 'active' : '' ?>">
            <i class="fas fa-th w-4 text-base"></i>
            <span>Alle Dateien</span>
            <span class="ml-auto text-xs bg-white/10 px-2 py-1 rounded-full"><?= $totalFiles ?></span>
          </a>

          <a href="?<?= http_build_query(array_merge($_GET, ['type' => 'documents'])) ?>" 
             class="nav-item flex items-center gap-3 text-white/80 hover:text-white text-sm <?= $filterType === 'documents' ? 'active' : '' ?>">
            <i class="fas fa-file-alt text-blue-400 w-4 text-base"></i>
            <span>Dokumente</span>
            <span class="ml-auto text-xs bg-white/10 px-2 py-1 rounded-full">
              <?= array_sum(array_intersect_key($typeCounts, array_flip($fileTypes['documents']))) ?>
            </span>
          </a>

          <a href="?<?= http_build_query(array_merge($_GET, ['type' => 'images'])) ?>" 
             class="nav-item flex items-center gap-3 text-white/80 hover:text-white text-sm <?= $filterType === 'images' ? 'active' : '' ?>">
            <i class="fas fa-image text-green-400 w-4 text-base"></i>
            <span>Bilder</span>
            <span class="ml-auto text-xs bg-white/10 px-2 py-1 rounded-full">
              <?= array_sum(array_intersect_key($typeCounts, array_flip($fileTypes['images']))) ?>
            </span>
          </a>

          <a href="?<?= http_build_query(array_merge($_GET, ['type' => 'videos'])) ?>" 
             class="nav-item flex items-center gap-3 text-white/80 hover:text-white text-sm <?= $filterType === 'videos' ? 'active' : '' ?>">
            <i class="fas fa-video text-pink-400 w-4 text-base"></i>
            <span>Videos</span>
            <span class="ml-auto text-xs bg-white/10 px-2 py-1 rounded-full">
              <?= array_sum(array_intersect_key($typeCounts, array_flip($fileTypes['videos']))) ?>
            </span>
          </a>

          <a href="?<?= http_build_query(array_merge($_GET, ['type' => 'audio'])) ?>" 
             class="nav-item flex items-center gap-3 text-white/80 hover:text-white text-sm <?= $filterType === 'audio' ? 'active' : '' ?>">
            <i class="fas fa-music text-purple-400 w-4 text-base"></i>
            <span>Audio</span>
            <span class="ml-auto text-xs bg-white/10 px-2 py-1 rounded-full">
              <?= array_sum(array_intersect_key($typeCounts, array_flip($fileTypes['audio']))) ?>
            </span>
          </a>

          <a href="?<?= http_build_query(array_merge($_GET, ['type' => 'archives'])) ?>" 
             class="nav-item flex items-center gap-3 text-white/80 hover:text-white text-sm <?= $filterType === 'archives' ? 'active' : '' ?>">
            <i class="fas fa-archive text-yellow-400 w-4 text-base"></i>
            <span>Archive</span>
            <span class="ml-auto text-xs bg-white/10 px-2 py-1 rounded-full">
              <?= array_sum(array_intersect_key($typeCounts, array_flip($fileTypes['archives']))) ?>
            </span>
          </a>

          <a href="?<?= http_build_query(array_merge($_GET, ['type' => 'code'])) ?>" 
             class="nav-item flex items-center gap-3 text-white/80 hover:text-white text-sm <?= $filterType === 'code' ? 'active' : '' ?>">
            <i class="fas fa-code text-cyan-400 w-4 text-base"></i>
            <span>Code</span>
            <span class="ml-auto text-xs bg-white/10 px-2 py-1 rounded-full">
              <?= array_sum(array_intersect_key($typeCounts, array_flip($fileTypes['code']))) ?>
            </span>
          </a>
        </div>

        <!-- Enhanced Quick Actions with consistent spacing -->
        <div class="mt-6 space-y-3">
          <h3 class="text-white font-medium text-sm uppercase tracking-wide mb-4">Aktionen</h3>
          
          <a href="/upload.php" class="gradient-button block w-full text-center font-medium action-btn">
            <i class="fas fa-plus mr-2"></i>
            Datei hochladen
          </a>
          
          <button onclick="window.print()" class="nav-item w-full text-left text-white/80 hover:text-white">
            <i class="fas fa-print mr-3"></i>
            Liste drucken
          </button>
        </div>
      </div>

      <!-- Enhanced Main Content with consistent spacing -->
      <div class="main-content flex-1 flex flex-col min-w-0">
        <!-- Enhanced Header Bar with proper spacing -->
        <div class="liquid-glass-header">
          <div class="flex items-center justify-between">
            <!-- Enhanced Breadcrumb & Search with consistent spacing -->
            <div class="flex items-center gap-4 flex-1">
              <div class="breadcrumb">
                <div class="flex items-center gap-2 text-white/80">
                  <i class="fas fa-home"></i>
                  <span>Datei-Explorer</span>
                  <?php if ($filterType): ?>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span class="capitalize"><?= ucfirst($filterType) ?></span>
                  <?php endif; ?>
                </div>
              </div>

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

            <!-- Enhanced View Toggle with consistent spacing -->
            <div class="view-toggle flex gap-1 p-1">
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

        <!-- Enhanced File Content with consistent spacing -->
        <div class="flex-1 overflow-y-auto custom-scrollbar">
          <?php if (empty($files)): ?>
            <!-- Enhanced Empty State with proper spacing -->
            <div class="liquid-glass m-6 p-12 text-center">
              <div class="w-24 h-24 bg-gradient-to-br from-purple-500/30 to-blue-500/30 rounded-3xl flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-folder-open text-4xl text-white/40"></i>
              </div>
              <h3 class="text-xl font-semibold text-white mb-2">
                <?= $searchQuery ? 'Keine Suchergebnisse gefunden' : 'Keine Dateien vorhanden' ?>
              </h3>
              <p class="text-white/60 mb-6">
                <?= $searchQuery ? 'Versuchen Sie es mit anderen Suchbegriffen' : 'Laden Sie Ihre erste Datei hoch, um loszulegen' ?>
              </p>
              <a href="/upload.php" class="gradient-button inline-block px-6 py-3 rounded-xl text-white font-medium">
                <i class="fas fa-plus mr-2"></i>
                Erste Datei hochladen
              </a>
            </div>
          <?php else: ?>
            <!-- Enhanced Grid View with consistent spacing -->
            <div id="gridView" class="<?= $currentView === 'grid' ? 'block' : 'hidden' ?>">
              <div class="file-grid">
                <?php foreach ($files as $file): ?>
                  <?php 
                  $fileInfo = getFileIcon($file['filename']);
                  $ext = strtolower(pathinfo($file['filename'], PATHINFO_EXTENSION));
                  $fileType = 'document';
                  foreach ($fileTypes as $type => $extensions) {
                    if (in_array($ext, $extensions)) {
                      $fileType = $type;
                      break;
                    }
                  }
                  ?>
                  <div class="file-card file-type-<?= $fileType ?> p-6 group" 
                       data-file-id="<?= $file['id'] ?>"
                       data-filename="<?= htmlspecialchars($file['filename']) ?>">
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

            <!-- Enhanced List View with consistent spacing -->
            <div id="listView" class="<?= $currentView === 'list' ? 'block' : 'hidden' ?>">
              <div class="file-list">
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
                    }
                  }
                  ?>
                  <div class="file-card file-type-<?= $fileType ?> p-6 group" 
                       data-file-id="<?= $file['id'] ?>"
                       data-filename="<?= htmlspecialchars($file['filename']) ?>">
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
