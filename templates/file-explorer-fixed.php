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
      transition: background 0.8s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow-x: hidden;
    }

    /* Animated gradient background similar to dashboard */
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
    }    /* Enhanced Liquid Glass Effects - Improved */
    .liquid-glass {
      background: rgba(255, 255, 255, 0.10);
      backdrop-filter: blur(25px) saturate(200%);
      border: 1px solid rgba(255, 255, 255, 0.18);
      border-radius: 2rem;
      box-shadow: 
        0 15px 35px rgba(0, 0, 0, 0.35),
        inset 0 1px 0 rgba(255, 255, 255, 0.15);
      position: relative;
      overflow: hidden;
      transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .liquid-glass:hover {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.25);
      transform: translateY(-3px);
      box-shadow: 
        0 20px 45px rgba(0, 0, 0, 0.45),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }

    .liquid-glass-header {
      background: linear-gradient(135deg, 
        rgba(255, 255, 255, 0.15) 0%, 
        rgba(255, 255, 255, 0.10) 100%);
      backdrop-filter: blur(30px) saturate(250%);
      border-bottom: 1px solid rgba(255, 255, 255, 0.15);
      padding: 2.5rem;
      border-radius: 2rem 2rem 0 0;
      position: relative;
    }

    .liquid-glass-header::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 80%;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    }    .file-card {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(20px) saturate(180%);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1.8rem;
      transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      padding: 2rem;
      margin: 0.75rem;
      box-shadow: 
        0 8px 25px rgba(0, 0, 0, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }

    .file-card:hover {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.3);
      transform: translateY(-6px) scale(1.03);
      box-shadow: 
        0 25px 50px rgba(0, 0, 0, 0.4),
        0 0 0 1px rgba(255, 255, 255, 0.15),
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
      transition: left 0.8s ease;
    }

    .file-card:hover::before {
      left: 100%;
    }    /* Folder cards with enhanced liquid glass */
    .folder-card {
      background: linear-gradient(135deg, 
        rgba(147, 51, 234, 0.18) 0%, 
        rgba(79, 70, 229, 0.12) 100%);
      backdrop-filter: blur(20px) saturate(180%);
      border: 1px solid rgba(147, 51, 234, 0.35);
      border-radius: 1.8rem;
      transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      padding: 2rem;
      margin: 0.75rem;
      box-shadow: 
        0 8px 25px rgba(147, 51, 234, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }

    .folder-card:hover {
      background: linear-gradient(135deg, 
        rgba(147, 51, 234, 0.28) 0%, 
        rgba(79, 70, 229, 0.22) 100%);
      border-color: rgba(147, 51, 234, 0.6);
      transform: translateY(-6px) scale(1.03);
      box-shadow: 
        0 25px 50px rgba(147, 51, 234, 0.3),
        0 0 0 1px rgba(147, 51, 234, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }    .sidebar-glass {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(30px) saturate(220%);
      border-right: 1px solid rgba(255, 255, 255, 0.15);
      box-shadow: 
        0 8px 25px rgba(0, 0, 0, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }

    .nav-item {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1rem;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      cursor: pointer;
      text-decoration: none;
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
      transition: left 0.6s ease;
    }

    .nav-item:hover::before {
      left: 100%;
    }

    .nav-item:hover {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.25);
      transform: translateX(4px);
      text-decoration: none;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .nav-item.active {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.35), rgba(79, 70, 229, 0.35));
      border-color: rgba(147, 51, 234, 0.6);
      transform: translateX(6px);
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
    }    .stats-card {
      background: linear-gradient(135deg, 
        rgba(255, 255, 255, 0.12) 0%, 
        rgba(255, 255, 255, 0.08) 100%);
      backdrop-filter: blur(25px) saturate(200%);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 1.5rem;
      box-shadow: 
        0 8px 25px rgba(0, 0, 0, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.15);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .stats-card:hover {
      transform: translateY(-2px);
      box-shadow: 
        0 12px 30px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }

    .file-icon-container {
      width: 3rem;
      height: 3rem;
      border-radius: 1rem;
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(15px);
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 
        0 4px 15px rgba(0, 0, 0, 0.15),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }

    .file-card:hover .file-icon-container {
      transform: scale(1.15) rotateY(5deg);
      background: rgba(255, 255, 255, 0.25);
      box-shadow: 
        0 8px 25px rgba(0, 0, 0, 0.25),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
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
    }    /* Layout adjustments for navbar - improved margins and positioning */
    .file-explorer-container {
      margin-left: 0;
      margin-top: 4rem; /* Space for mobile navbar */
      height: calc(100vh - 4rem);
      padding: 3rem 2rem; /* Increased outer padding for better spacing */
    }

    @media (min-width: 768px) {
      .file-explorer-container {
        margin-left: 16rem; /* 256px = w-64 navbar width */
        margin-top: 0;
        height: 100vh;
        padding: 3rem 2.5rem; /* Increased padding for desktop */
      }
    }

    /* Main content area with enhanced liquid glass effect */
    .main-content-area {
      background: rgba(255, 255, 255, 0.06);
      backdrop-filter: blur(40px) saturate(200%);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 2.5rem;
      padding: 3rem;
      height: 100%;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      box-shadow: 
        0 25px 50px rgba(0, 0, 0, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.15);
      position: relative;
    }

    .main-content-area::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
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
    }  </style>
</head>
<body class="min-h-screen">
  <?php require_once __DIR__.'/../templates/navbar.php'; ?>

  <div class="file-explorer-container">
    <div class="main-content-area">
      <!-- Header Section -->
      <div class="liquid-glass-header mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
          <div>
            <h1 class="text-4xl font-bold text-white mb-2 bg-gradient-to-r from-purple-400 to-blue-400 bg-clip-text text-transparent">
              Datei-Explorer
            </h1>
            <p class="text-white/70 text-lg">Verwalten Sie alle Ihre Dateien an einem Ort</p>
          </div>
          
          <div class="flex flex-col sm:flex-row gap-4">
            <div class="stats-card p-4 min-w-[120px]">
              <div class="text-center">
                <div class="text-2xl font-bold text-white"><?= $totalFiles ?></div>
                <div class="text-sm text-white/60">Dateien</div>
              </div>
            </div>
            <div class="stats-card p-4 min-w-[120px]">
              <div class="text-center">
                <div class="text-2xl font-bold text-white"><?= formatFileSize($totalSize) ?></div>
                <div class="text-sm text-white/60">Speicher</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Control Bar -->
      <div class="liquid-glass p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
          <!-- Search and Upload -->
          <div class="flex flex-col sm:flex-row gap-4 flex-1">
            <a href="/upload.php" class="gradient-button px-6 py-3 font-medium inline-flex items-center gap-2 group">
              <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
              </svg>
              Datei hochladen
            </a>
            
            <div class="search-bar flex-1 relative">
              <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
              <input type="text" placeholder="Dateien durchsuchen..." 
                     value="<?= htmlspecialchars($searchQuery) ?>"
                     class="w-full pl-10 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 transition-all">
            </div>
          </div>

          <!-- View Toggle -->
          <div class="view-toggle flex bg-white/5 border border-white/10 rounded-lg p-1">
            <button class="view-toggle-btn px-3 py-2 text-white/70 <?= $currentView === 'grid' ? 'active' : '' ?>">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
              </svg>
            </button>
            <button class="view-toggle-btn px-3 py-2 text-white/70 <?= $currentView === 'list' ? 'active' : '' ?>">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
              </svg>
            </button>
          </div>
        </div>
      </div>      <!-- Files Grid -->
      <div class="flex-1 overflow-y-auto custom-scrollbar">
        <div class="p-4">
          <?php if (empty($files)): ?>
            <!-- Empty State -->
            <div class="liquid-glass p-16 text-center">
              <div class="w-32 h-32 bg-gradient-to-br from-purple-500/20 to-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-8">
                <i class="fas fa-folder-open text-5xl text-white/40"></i>
              </div>
              <h3 class="text-2xl font-semibold text-white mb-4">
                <?= $searchQuery ? 'Keine Suchergebnisse gefunden' : 'Keine Dateien vorhanden' ?>
              </h3>
              <p class="text-white/60 mb-8 text-lg">
                <?= $searchQuery ? 'Versuchen Sie es mit anderen Suchbegriffen' : 'Laden Sie Ihre erste Datei hoch, um loszulegen' ?>
              </p>
              <a href="/upload.php" class="gradient-button inline-block px-8 py-4 rounded-xl text-white font-medium text-lg">
                <i class="fas fa-plus mr-3"></i>
                Erste Datei hochladen
              </a>
            </div>        <?php else: ?>
          <!-- Grid View -->
          <div id="gridView" class="<?= $currentView === 'grid' ? 'block' : 'hidden' ?>">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6">
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
                <div class="file-card file-type-<?= $fileType ?> group">
                  <div class="flex flex-col h-full">                    <!-- File Icon -->
                    <div class="file-icon-container mx-auto mb-4">
                      <svg class="w-6 h-6 <?= $fileInfo['color'] ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    </div>                    <!-- Actions -->
                    <div class="mt-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300">
                      <a href="/uploads/<?= urlencode($file['filename']) ?>" 
                         download 
                         class="flex-1 text-center py-2 px-3 bg-white/10 hover:bg-white/20 rounded-lg text-white text-xs transition-colors">
                        <i class="fas fa-download mr-1"></i>
                        Download
                      </a>
                      <button onclick="deleteFile(<?= $file['id'] ?>)" 
                              class="py-2 px-3 bg-red-500/20 hover:bg-red-500/30 rounded-lg text-red-300 text-xs transition-colors">
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
