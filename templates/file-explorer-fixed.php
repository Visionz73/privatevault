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
    }

    /* Enhanced Liquid Glass Effects */
    .liquid-glass {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(20px) saturate(180%);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1.5rem;
      box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
      position: relative;
      overflow: hidden;
    }

    .liquid-glass-header {
      background: linear-gradient(135deg, 
        rgba(255, 255, 255, 0.12) 0%, 
        rgba(255, 255, 255, 0.08) 100%);
      backdrop-filter: blur(25px) saturate(200%);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .file-card {
      background: rgba(255, 255, 255, 0.06);
      backdrop-filter: blur(15px) saturate(180%);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 1.2rem;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }

    .file-card:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(255, 255, 255, 0.25);
      transform: translateY(-4px) scale(1.02);
      box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.4),
        0 0 0 1px rgba(255, 255, 255, 0.1);
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

    .file-card:hover::before {
      left: 100%;
    }

    .sidebar-glass {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(25px) saturate(200%);
      border-right: 1px solid rgba(255, 255, 255, 0.1);
    }

    .nav-item {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 0.75rem;
      transition: all 0.3s ease;
      cursor: pointer;
      text-decoration: none;
    }

    .nav-item:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(255, 255, 255, 0.2);
      transform: translateX(2px);
      text-decoration: none;
    }

    .nav-item.active {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.3), rgba(79, 70, 229, 0.3));
      border-color: rgba(147, 51, 234, 0.5);
    }

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

    .gradient-button {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.8), rgba(79, 70, 229, 0.8));
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      text-decoration: none;
    }

    .gradient-button:hover {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.9), rgba(79, 70, 229, 0.9));
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(147, 51, 234, 0.4);
      text-decoration: none;
    }

    .gradient-button::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s ease;
    }

    .gradient-button:hover::before {
      left: 100%;
    }

    .view-toggle {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 0.75rem;
    }

    .view-toggle-btn {
      transition: all 0.3s ease;
      border-radius: 0.5rem;
    }

    .view-toggle-btn.active {
      background: rgba(147, 51, 234, 0.3);
      color: #a855f7;
    }

    .view-toggle-btn:hover {
      background: rgba(255, 255, 255, 0.1);
    }

    .stats-card {
      background: linear-gradient(135deg, 
        rgba(255, 255, 255, 0.1) 0%, 
        rgba(255, 255, 255, 0.05) 100%);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1rem;
    }

    .file-icon-container {
      width: 2.5rem;
      height: 2.5rem;
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

    .breadcrumb {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 0.75rem;
      padding: 0.75rem 1rem;
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

    /* File type animations */
    .file-type-image .file-icon-container {
      background: linear-gradient(135deg, rgba(34, 197, 94, 0.2), rgba(22, 163, 74, 0.1));
    }

    .file-type-video .file-icon-container {
      background: linear-gradient(135deg, rgba(236, 72, 153, 0.2), rgba(219, 39, 119, 0.1));
    }

    .file-type-audio .file-icon-container {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.2), rgba(126, 34, 206, 0.1));
    }

    .file-type-document .file-icon-container {
      background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(37, 99, 235, 0.1));
    }

    .file-type-archive .file-icon-container {
      background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(217, 119, 6, 0.1));
    }

    /* Navbar integration */
    .file-explorer-container {
      margin-left: 0;
      margin-top: 4rem; /* Space for mobile navbar */
      height: calc(100vh - 4rem);
    }

    @media (min-width: 768px) {
      .file-explorer-container {
        margin-left: 16rem; /* 256px = w-64 navbar width */
        margin-top: 0;
        height: 100vh;
      }
    }

    /* Loading animation */
    @keyframes shimmer {
      0% { background-position: -200% 0; }
      100% { background-position: 200% 0; }
    }

    .loading-shimmer {
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
      background-size: 200% 100%;
      animation: shimmer 2s infinite;
    }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900">
  <?php require_once __DIR__.'/../templates/navbar.php'; ?>

  <div class="file-explorer-container flex h-full">
    <!-- Sidebar -->
    <div class="sidebar-glass w-72 flex-shrink-0 p-4 overflow-y-auto custom-scrollbar">
      <!-- Header -->
      <div class="mb-6">
        <h1 class="text-xl font-bold text-white mb-2 bg-gradient-to-r from-purple-400 to-blue-400 bg-clip-text text-transparent">
          Datei-Explorer
        </h1>
        <p class="text-white/60 text-xs">Verwalten Sie alle Ihre Dateien</p>
      </div>

      <!-- Quick Stats -->
      <div class="mb-6 space-y-3">
        <div class="stats-card p-3">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-white/60 text-xs">Dateien gesamt</p>
              <p class="text-white text-lg font-bold"><?= $totalFiles ?></p>
            </div>
            <div class="w-8 h-8 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-lg flex items-center justify-center">
              <i class="fas fa-file text-blue-400 text-sm"></i>
            </div>
          </div>
        </div>
        
        <div class="stats-card p-3">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-white/60 text-xs">Speicher belegt</p>
              <p class="text-white text-sm font-bold"><?= formatFileSize($totalSize) ?></p>
            </div>
            <div class="w-8 h-8 bg-gradient-to-br from-green-500/20 to-emerald-500/20 rounded-lg flex items-center justify-center">
              <i class="fas fa-hdd text-green-400 text-sm"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- File Type Navigation -->
      <div class="space-y-2">
        <h3 class="text-white font-medium text-xs uppercase tracking-wide mb-3">Dateitypen</h3>
        
        <a href="?<?= http_build_query(array_merge($_GET, ['type' => ''])) ?>" 
           class="nav-item p-2 flex items-center gap-2 text-white/80 hover:text-white text-sm <?= $filterType === '' ? 'active' : '' ?>">
          <i class="fas fa-th w-4 text-xs"></i>
          <span>Alle Dateien</span>
          <span class="ml-auto text-xs bg-white/10 px-1.5 py-0.5 rounded-full"><?= $totalFiles ?></span>
        </a>

        <a href="?<?= http_build_query(array_merge($_GET, ['type' => 'documents'])) ?>" 
           class="nav-item p-2 flex items-center gap-2 text-white/80 hover:text-white text-sm <?= $filterType === 'documents' ? 'active' : '' ?>">
          <i class="fas fa-file-alt text-blue-400 w-4 text-xs"></i>
          <span>Dokumente</span>
          <span class="ml-auto text-xs bg-white/10 px-1.5 py-0.5 rounded-full">
            <?= array_sum(array_intersect_key($typeCounts, array_flip($fileTypes['documents']))) ?>
          </span>
        </a>

        <a href="?<?= http_build_query(array_merge($_GET, ['type' => 'images'])) ?>" 
           class="nav-item p-2 flex items-center gap-2 text-white/80 hover:text-white text-sm <?= $filterType === 'images' ? 'active' : '' ?>">
          <i class="fas fa-image text-green-400 w-4 text-xs"></i>
          <span>Bilder</span>
          <span class="ml-auto text-xs bg-white/10 px-1.5 py-0.5 rounded-full">
            <?= array_sum(array_intersect_key($typeCounts, array_flip($fileTypes['images']))) ?>
          </span>
        </a>

        <a href="?<?= http_build_query(array_merge($_GET, ['type' => 'videos'])) ?>" 
           class="nav-item p-2 flex items-center gap-2 text-white/80 hover:text-white text-sm <?= $filterType === 'videos' ? 'active' : '' ?>">
          <i class="fas fa-video text-pink-400 w-4 text-xs"></i>
          <span>Videos</span>
          <span class="ml-auto text-xs bg-white/10 px-1.5 py-0.5 rounded-full">
            <?= array_sum(array_intersect_key($typeCounts, array_flip($fileTypes['videos']))) ?>
          </span>
        </a>

        <a href="?<?= http_build_query(array_merge($_GET, ['type' => 'audio'])) ?>" 
           class="nav-item p-2 flex items-center gap-2 text-white/80 hover:text-white text-sm <?= $filterType === 'audio' ? 'active' : '' ?>">
          <i class="fas fa-music text-purple-400 w-4 text-xs"></i>
          <span>Audio</span>
          <span class="ml-auto text-xs bg-white/10 px-1.5 py-0.5 rounded-full">
            <?= array_sum(array_intersect_key($typeCounts, array_flip($fileTypes['audio']))) ?>
          </span>
        </a>

        <a href="?<?= http_build_query(array_merge($_GET, ['type' => 'archives'])) ?>" 
           class="nav-item p-2 flex items-center gap-2 text-white/80 hover:text-white text-sm <?= $filterType === 'archives' ? 'active' : '' ?>">
          <i class="fas fa-archive text-yellow-400 w-4 text-xs"></i>
          <span>Archive</span>
          <span class="ml-auto text-xs bg-white/10 px-1.5 py-0.5 rounded-full">
            <?= array_sum(array_intersect_key($typeCounts, array_flip($fileTypes['archives']))) ?>
          </span>
        </a>

        <a href="?<?= http_build_query(array_merge($_GET, ['type' => 'code'])) ?>" 
           class="nav-item p-2 flex items-center gap-2 text-white/80 hover:text-white text-sm <?= $filterType === 'code' ? 'active' : '' ?>">
          <i class="fas fa-code text-cyan-400 w-4 text-xs"></i>
          <span>Code</span>
          <span class="ml-auto text-xs bg-white/10 px-1.5 py-0.5 rounded-full">
            <?= array_sum(array_intersect_key($typeCounts, array_flip($fileTypes['code']))) ?>
          </span>
        </a>
      </div>

      <!-- Quick Actions -->
      <div class="mt-6 space-y-2">
        <h3 class="text-white font-medium text-xs uppercase tracking-wide mb-3">Aktionen</h3>
        
        <a href="/upload.php" class="gradient-button block w-full p-2 rounded-lg text-white text-center font-medium text-sm">
          <i class="fas fa-plus mr-2"></i>
          Datei hochladen
        </a>
        
        <button onclick="window.print()" class="nav-item p-2 w-full text-left text-white/80 hover:text-white text-sm">
          <i class="fas fa-print mr-2"></i>
          Liste drucken
        </button>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0">
      <!-- Header Bar -->
      <div class="liquid-glass-header p-4">
        <div class="flex items-center justify-between">
          <!-- Breadcrumb & Search -->
          <div class="flex items-center gap-4 flex-1">
            <div class="breadcrumb">
              <div class="flex items-center gap-2 text-white/80 text-sm">
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

      <!-- File Content -->
      <div class="flex-1 p-4 overflow-y-auto custom-scrollbar">
        <?php if (empty($files)): ?>
          <!-- Empty State -->
          <div class="liquid-glass p-12 text-center">
            <div class="w-24 h-24 bg-gradient-to-br from-purple-500/20 to-blue-500/20 rounded-3xl flex items-center justify-center mx-auto mb-6">
              <i class="fas fa-folder-open text-4xl text-white/40"></i>
            </div>
            <h3 class="text-xl font-semibold text-white mb-2">
              <?= $searchQuery ? 'Keine Suchergebnisse gefunden' : 'Keine Dateien vorhanden' ?>
            </h3>
            <p class="text-white/60 mb-6">
              <?= $searchQuery ? 'Versuchen Sie es mit anderen Suchbegriffen' : 'Laden Sie Ihre erste Datei hoch, um loszulegen' ?>
            </p>
            <a href="/upload.php" class="gradient-button inline-block px-6 py-3 rounded-lg text-white font-medium">
              <i class="fas fa-plus mr-2"></i>
              Erste Datei hochladen
            </a>
          </div>
        <?php else: ?>
          <!-- Grid View -->
          <div id="gridView" class="<?= $currentView === 'grid' ? 'block' : 'hidden' ?>">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
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
                <div class="file-card file-type-<?= $fileType ?> p-4 group">
                  <div class="flex flex-col h-full">
                    <!-- File Icon -->
                    <div class="file-icon-container mx-auto mb-3">
                      <svg class="w-5 h-5 <?= $fileInfo['color'] ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $fileInfo['icon'] ?>"/>
                      </svg>
                    </div>

                    <!-- File Info -->
                    <div class="flex-1 text-center">
                      <h3 class="text-white font-medium text-sm mb-1 truncate" title="<?= htmlspecialchars($file['title'] ?? $file['original_name'] ?? $file['filename']) ?>">
                        <?= htmlspecialchars($file['title'] ?? $file['original_name'] ?? $file['filename']) ?>
                      </h3>
                      <p class="text-white/60 text-xs mb-1"><?= htmlspecialchars($file['category_name'] ?? 'Keine Kategorie') ?></p>
                      <p class="text-white/40 text-xs"><?= date('d.m.Y', strtotime($file['upload_date'])) ?></p>
                    </div>

                    <!-- Actions -->
                    <div class="mt-3 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                      <a href="/uploads/<?= urlencode($file['filename']) ?>" 
                         download 
                         class="flex-1 text-center py-1.5 px-2 bg-white/10 hover:bg-white/20 rounded-lg text-white text-xs transition-colors">
                        <i class="fas fa-download mr-1"></i>
                        Download
                      </a>
                      <button onclick="deleteFile(<?= $file['id'] ?>)" 
                              class="py-1.5 px-2 bg-red-500/20 hover:bg-red-500/30 rounded-lg text-red-300 text-xs transition-colors">
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
            <div class="liquid-glass overflow-hidden">
              <table class="w-full">
                <thead>
                  <tr class="border-b border-white/10">
                    <th class="text-left py-3 px-4 font-medium text-white/80 text-sm">Name</th>
                    <th class="text-left py-3 px-4 font-medium text-white/80 text-sm">Typ</th>
                    <th class="text-left py-3 px-4 font-medium text-white/80 text-sm">Kategorie</th>
                    <th class="text-left py-3 px-4 font-medium text-white/80 text-sm">Datum</th>
                    <th class="text-left py-3 px-4 font-medium text-white/80 text-sm">Größe</th>
                    <th class="text-right py-3 px-4 font-medium text-white/80 text-sm">Aktionen</th>
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
                    <tr class="border-b border-white/5 hover:bg-white/5 transition-colors group">
                      <td class="py-3 px-4">
                        <div class="flex items-center gap-3">
                          <div class="w-6 h-6 bg-white/10 rounded-lg flex items-center justify-center">
                            <svg class="w-3 h-3 <?= $fileInfo['color'] ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $fileInfo['icon'] ?>"/>
                            </svg>
                          </div>
                          <div>
                            <p class="text-white font-medium text-sm"><?= htmlspecialchars($file['title'] ?? $file['original_name'] ?? $file['filename']) ?></p>
                            <p class="text-white/60 text-xs"><?= htmlspecialchars($file['filename']) ?></p>
                          </div>
                        </div>
                      </td>
                      <td class="py-3 px-4">
                        <span class="text-white/70 text-sm uppercase"><?= $ext ?></span>
                      </td>
                      <td class="py-3 px-4 text-white/70 text-sm"><?= htmlspecialchars($file['category_name'] ?? 'Keine Kategorie') ?></td>
                      <td class="py-3 px-4 text-white/70 text-sm"><?= date('d.m.Y', strtotime($file['upload_date'])) ?></td>
                      <td class="py-3 px-4 text-white/70 text-sm"><?= formatFileSize($fileSize) ?></td>
                      <td class="py-3 px-4 text-right">
                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                          <a href="/uploads/<?= urlencode($file['filename']) ?>" 
                             download 
                             class="py-1 px-2 bg-white/10 hover:bg-white/20 rounded-lg text-white text-xs transition-colors">
                            Download
                          </a>
                          <button onclick="deleteFile(<?= $file['id'] ?>)" 
                                  class="py-1 px-2 bg-red-500/20 hover:bg-red-500/30 rounded-lg text-red-300 text-xs transition-colors">
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

  <script>
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
      if (e.key === 'Escape') {
        document.querySelector('input[name="search"]').blur();
      }
    });
  </script>
</body>
</html>
