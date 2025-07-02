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
    }

    /* Layout adjustments for navbar - improved margins */
    .file-explorer-container {
      margin-left: 0;
      margin-top: 4rem; /* Space for mobile navbar */
      min-height: calc(100vh - 4rem);
      padding: 2rem 1rem; /* Increased padding for better spacing */
    }

    @media (min-width: 768px) {
      .file-explorer-container {
        margin-left: 16rem; /* 256px = w-64 navbar width */
        margin-top: 0;
        min-height: 100vh;
        padding: 3rem 2rem; /* Increased padding for desktop */
      }
    }

    /* Enhanced Liquid Glass Effects - Dashboard Style */
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
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .liquid-glass:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(255, 255, 255, 0.2);
      transform: translateY(-2px);
      box-shadow: 
        0 12px 40px rgba(0, 0, 0, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.15);
    }

    .liquid-glass-header {
      background: linear-gradient(135deg, 
        rgba(255, 255, 255, 0.12) 0%, 
        rgba(255, 255, 255, 0.08) 100%);
      backdrop-filter: blur(25px) saturate(200%);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      padding: 2rem;
      border-radius: 1.5rem 1.5rem 0 0;
    }

    /* File Cards */
    .file-card {
      background: rgba(255, 255, 255, 0.06);
      backdrop-filter: blur(15px) saturate(180%);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 1.2rem;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      padding: 1.5rem;
      cursor: pointer;
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

    /* Folder cards */
    .folder-card {
      background: linear-gradient(135deg, 
        rgba(147, 51, 234, 0.15) 0%, 
        rgba(79, 70, 229, 0.1) 100%);
      backdrop-filter: blur(15px) saturate(180%);
      border: 1px solid rgba(147, 51, 234, 0.3);
      border-radius: 1.2rem;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      padding: 1.5rem;
      cursor: pointer;
    }

    .folder-card:hover {
      background: linear-gradient(135deg, 
        rgba(147, 51, 234, 0.25) 0%, 
        rgba(79, 70, 229, 0.2) 100%);
      border-color: rgba(147, 51, 234, 0.5);
      transform: translateY(-4px) scale(1.02);
      box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.4),
        0 0 0 1px rgba(147, 51, 234, 0.2);
    }

    .search-bar {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 0.75rem;
    }

    .search-bar:focus-within {
      background: rgba(255, 255, 255, 0.08);
      border-color: rgba(147, 51, 234, 0.5);
      box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.1);
    }

    .gradient-button {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.8), rgba(79, 70, 229, 0.8));
      backdrop-filter: blur(10px);
      border: 1px solid rgba(147, 51, 234, 0.5);
      border-radius: 0.75rem;
      color: white;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      text-decoration: none;
    }

    .gradient-button:hover {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.9), rgba(79, 70, 229, 0.9));
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(147, 51, 234, 0.3);
      color: white;
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
      width: 3rem;
      height: 3rem;
      border-radius: 0.75rem;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
      margin: 0 auto 1rem auto;
    }

    .file-card:hover .file-icon-container {
      transform: scale(1.1);
      background: rgba(255, 255, 255, 0.15);
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

    /* File type specific colors */
    .file-type-image .file-icon-container {
      background: linear-gradient(135deg, rgba(34, 197, 94, 0.3), rgba(22, 163, 74, 0.2));
    }

    .file-type-video .file-icon-container {
      background: linear-gradient(135deg, rgba(236, 72, 153, 0.3), rgba(219, 39, 119, 0.2));
    }

    .file-type-audio .file-icon-container {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.3), rgba(126, 34, 206, 0.2));
    }

    .file-type-document .file-icon-container {
      background: linear-gradient(135deg, rgba(59, 130, 246, 0.3), rgba(37, 99, 235, 0.2));
    }

    .file-type-archive .file-icon-container {
      background: linear-gradient(135deg, rgba(245, 158, 11, 0.3), rgba(217, 119, 6, 0.2));
    }
  </style>
</head>
<body class="min-h-screen">
  <?php require_once __DIR__.'/navbar.php'; ?>

  <div class="file-explorer-container">
    <!-- Header Section -->
    <div class="liquid-glass mb-8">
      <div class="liquid-glass-header">
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
      <div class="p-6">
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
                     class="w-full pl-10 pr-4 py-3 bg-transparent border-0 text-white placeholder-white/50 focus:outline-none">
            </div>
          </div>

          <!-- View Toggle -->
          <div class="view-toggle flex p-1">
            <a href="?<?= http_build_query(array_merge($_GET, ['view' => 'grid'])) ?>" 
               class="view-toggle-btn px-3 py-2 text-white/70 <?= $currentView === 'grid' ? 'active' : '' ?>">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
              </svg>
            </a>
            <a href="?<?= http_build_query(array_merge($_GET, ['view' => 'list'])) ?>" 
               class="view-toggle-btn px-3 py-2 text-white/70 <?= $currentView === 'list' ? 'active' : '' ?>">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
              </svg>
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Folders Section -->
    <?php if (!empty($folders)): ?>
    <div class="mb-8">
      <h2 class="text-xl font-semibold text-white mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
          <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
        </svg>
        Ordner
      </h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php foreach ($folders as $folder): ?>
          <div class="folder-card">
            <div class="file-icon-container">
              <svg class="w-6 h-6 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
              </svg>
            </div>
            <h3 class="text-white font-medium text-center mb-1"><?= htmlspecialchars($folder['name']) ?></h3>
            <p class="text-white/60 text-sm text-center"><?= $folder['file_count'] ?> Dateien</p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Files Section -->
    <div class="mb-8">
      <h2 class="text-xl font-semibold text-white mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Dateien
        <?php if ($searchQuery): ?>
          <span class="text-white/60 text-sm">- Suchergebnisse für "<?= htmlspecialchars($searchQuery) ?>"</span>
        <?php endif; ?>
      </h2>
      
      <?php if (!empty($files)): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <?php foreach ($files as $file): 
            $fileIcon = getFileIcon($file['filename']);
            $ext = strtolower(pathinfo($file['filename'], PATHINFO_EXTENSION));
            $fileType = 'document';
            
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'])) $fileType = 'image';
            elseif (in_array($ext, ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv'])) $fileType = 'video';
            elseif (in_array($ext, ['mp3', 'wav', 'flac', 'aac', 'ogg', 'wma'])) $fileType = 'audio';
            elseif (in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz'])) $fileType = 'archive';
          ?>
            <div class="file-card file-type-<?= $fileType ?>" onclick="downloadFile(<?= $file['id'] ?>)">
              <div class="file-icon-container">
                <svg class="w-6 h-6 <?= $fileIcon['color'] ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $fileIcon['icon'] ?>"/>
                </svg>
              </div>
              <h3 class="text-white font-medium text-center mb-1 text-sm leading-tight"><?= htmlspecialchars($file['title'] ?: $file['original_name']) ?></h3>
              <p class="text-white/60 text-xs text-center mb-2"><?= strtoupper($ext) ?></p>
              <p class="text-white/40 text-xs text-center"><?= date('d.m.Y', strtotime($file['upload_date'])) ?></p>
              
              <!-- File Actions -->
              <div class="mt-3 flex justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <button onclick="event.stopPropagation(); downloadFile(<?= $file['id'] ?>)" 
                        class="p-1 bg-white/10 rounded text-white/70 hover:bg-white/20 hover:text-white transition-colors">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                </button>
                <button onclick="event.stopPropagation(); deleteFile(<?= $file['id'] ?>)" 
                        class="p-1 bg-red-500/20 rounded text-red-400 hover:bg-red-500/30 hover:text-red-300 transition-colors">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="liquid-glass p-12 text-center">
          <svg class="w-16 h-16 text-white/30 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
          </svg>
          <h3 class="text-xl font-semibold text-white mb-2">Keine Dateien gefunden</h3>
          <p class="text-white/60 mb-6">
            <?php if ($searchQuery): ?>
              Keine Dateien gefunden, die Ihrer Suche entsprechen.
            <?php else: ?>
              Laden Sie Ihre erste Datei hoch, um loszulegen.
            <?php endif; ?>
          </p>
          <a href="/upload.php" class="gradient-button px-6 py-3 font-medium inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            Datei hochladen
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <script>
    function downloadFile(fileId) {
      window.location.href = `/download.php?id=${fileId}`;
    }

    function deleteFile(fileId) {
      if (confirm('Sind Sie sicher, dass Sie diese Datei löschen möchten?')) {
        window.location.href = `?delete=${fileId}&${window.location.search.substring(1)}`;
      }
    }

    // Search functionality
    document.querySelector('input[type="text"]').addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        const params = new URLSearchParams(window.location.search);
        params.set('search', this.value);
        window.location.href = '?' + params.toString();
      }
    });

    // Auto-submit search after delay
    let searchTimeout;
    document.querySelector('input[type="text"]').addEventListener('input', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        const params = new URLSearchParams(window.location.search);
        if (this.value) {
          params.set('search', this.value);
        } else {
          params.delete('search');
        }
        window.location.href = '?' + params.toString();
      }, 500);
    });
  </script>
</body>
</html>
