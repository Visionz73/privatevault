<?php
// templates/file_explorer.php
// Vollständiger Datei-Explorer mit modernem Liquid Glass Design
?>
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
    }
    
    @media (max-width: 768px) {
      main { margin-top: 4rem; }
    }
    
    /* Enhanced Liquid Glass Effects */
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

    /* File Cards */
    .file-card {
      background: rgba(255, 255, 255, 0.06);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 1.5rem;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      overflow: hidden;
      position: relative;
      cursor: pointer;
      display: flex;
      flex-direction: column;
      aspect-ratio: 1 / 1;
    }

    .file-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #9333ea, #4f46e5, #06b6d4);
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .file-card:hover::before {
      opacity: 1;
    }

    .file-card:hover {
      background: rgba(255, 255, 255, 0.08);
      border-color: rgba(255, 255, 255, 0.2);
      transform: translateY(-4px) scale(1.02);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
    }

    /* File Icons */
    .file-icon {
      width: 60px;
      height: 60px;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border-radius: 1rem;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
    }

    .file-card:hover .file-icon {
      background: rgba(255, 255, 255, 0.15);
      transform: scale(1.1);
    }

    /* Category Navigation */
    .category-nav {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 1.25rem;
      overflow: hidden;
    }

    .category-item {
      background: transparent;
      border: none;
      color: rgba(255, 255, 255, 0.7);
      padding: 1rem 1.5rem;
      transition: all 0.3s ease;
      width: 100%;
      text-align: left;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      position: relative;
    }

    .category-item:hover {
      background: rgba(255, 255, 255, 0.1);
      color: white;
    }

    .category-item.active {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.2), rgba(79, 70, 229, 0.2));
      color: white;
      border-left: 4px solid #9333ea;
    }

    /* Buttons */
    .liquid-glass-btn {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.3), rgba(79, 70, 229, 0.3));
      backdrop-filter: blur(20px);
      border: 1px solid rgba(147, 51, 234, 0.4);
      color: white;
      border-radius: 1rem;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      font-weight: 500;
    }

    .liquid-glass-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s ease;
    }

    .liquid-glass-btn:hover::before {
      left: 100%;
    }

    .liquid-glass-btn:hover {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.4), rgba(79, 70, 229, 0.4));
      border-color: rgba(147, 51, 234, 0.6);
      transform: translateY(-2px);
      box-shadow: 0 8px 32px rgba(147, 51, 234, 0.3);
    }

    .liquid-glass-btn-secondary {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      border-radius: 0.75rem;
      transition: all 0.3s ease;
    }

    .liquid-glass-btn-secondary:hover {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.3);
      transform: translateY(-1px);
    }

    .liquid-glass-btn-danger {
      background: rgba(239, 68, 68, 0.2);
      backdrop-filter: blur(15px);
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

    /* Search and Input */
    .liquid-glass-input {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      color: white;
      border-radius: 1rem;
      transition: all 0.3s ease;
      box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .liquid-glass-input:focus {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(147, 51, 234, 0.5);
      outline: none;
      box-shadow: 
        0 0 0 3px rgba(147, 51, 234, 0.2),
        inset 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .liquid-glass-input::placeholder {
      color: rgba(255, 255, 255, 0.4);
    }

    /* View Toggle */
    .view-toggle-btn {
      background: transparent;
      border: none;
      color: rgba(255, 255, 255, 0.6);
      padding: 0.75rem;
      border-radius: 0.75rem;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .view-toggle-btn:hover {
      background: rgba(255, 255, 255, 0.1);
      color: white;
    }

    .view-toggle-btn.active {
      background: rgba(147, 51, 234, 0.3);
      color: white;
      box-shadow: 0 0 20px rgba(147, 51, 234, 0.3);
    }

    /* List View */
    .file-list-item {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 1rem;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .file-list-item:hover {
      background: rgba(255, 255, 255, 0.08);
      border-color: rgba(255, 255, 255, 0.2);
      transform: translateX(4px);
    }

    /* Image Preview */
    .image-preview {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border-radius: 1rem;
      overflow: hidden;
      aspect-ratio: 1;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .image-preview img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 1rem;
    }

    /* Statistics Cards */
    .stat-card {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 1rem;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, #9333ea, #4f46e5);
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .stat-card:hover::before {
      opacity: 1;
    }

    .stat-card:hover {
      background: rgba(255, 255, 255, 0.08);
      transform: translateY(-2px);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .file-card {
        min-height: 200px;
      }
      
      .category-nav {
        order: 2;
      }
      
      .main-content {
        order: 1;
      }
    }

    /* Animations */
    .file-card {
      animation: fadeInUp 0.5s ease-out;
    }

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

    /* File Type Colors */
    .file-type-documents { color: #3b82f6; }
    .file-type-images { color: #10b981; }
    .file-type-music { color: #f59e0b; }
    .file-type-videos { color: #ef4444; }
    .file-type-archives { color: #8b5cf6; }
    .file-type-other { color: #6b7280; }
  </style>
</head>

<body class="min-h-screen">
  <?php require_once __DIR__ . '/navbar.php'; ?>

  <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-6 space-y-8" style="padding-top: 6rem;">
    <!-- Header Section -->
    <div class="glass-card p-8">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div>
          <h1 class="text-4xl font-bold text-white mb-2 bg-gradient-to-r from-purple-400 to-blue-400 bg-clip-text text-transparent">
            Datei-Explorer
          </h1>
          <p class="text-white/70 text-lg">Verwalten Sie alle Ihre Dateien an einem Ort</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-4">
          <div class="stat-card p-4 min-w-[120px]">
            <div class="text-center">
              <div class="text-2xl font-bold text-white"><?= $stats['all'] ?></div>
              <div class="text-sm text-white/60">Dateien</div>
            </div>
          </div>
          <div class="stat-card p-4 min-w-[120px]">
            <div class="text-center">
              <div class="text-2xl font-bold text-white"><?= $formattedTotalSize ?></div>
              <div class="text-sm text-white/60">Speicher</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Control Bar -->
    <div class="glass-card p-6">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <!-- Search and Upload -->
        <div class="flex flex-col sm:flex-row gap-4 flex-1">
          <a href="/upload.php" class="liquid-glass-btn px-6 py-3 font-medium inline-flex items-center gap-2 group">
            <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            Datei hochladen
          </a>

          <div class="relative flex-1 max-w-md">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input 
              type="text" 
              placeholder="Dateien durchsuchen..." 
              class="liquid-glass-input pl-12 pr-4 py-3 text-sm w-full"
              value="<?= htmlspecialchars($searchQuery) ?>"
              id="searchInput"
              onkeypress="if(event.key==='Enter') performSearch()"
            >
          </div>
        </div>

        <!-- View Controls -->
        <div class="flex items-center gap-4">
          <!-- Sort Options -->
          <select class="liquid-glass-input px-3 py-2 text-sm" onchange="changeSort(this.value)">
            <option value="upload_date" <?= $sortBy === 'upload_date' ? 'selected' : '' ?>>Nach Datum</option>
            <option value="filename" <?= $sortBy === 'filename' ? 'selected' : '' ?>>Nach Name</option>
            <option value="file_size" <?= $sortBy === 'file_size' ? 'selected' : '' ?>>Nach Größe</option>
            <option value="title" <?= $sortBy === 'title' ? 'selected' : '' ?>>Nach Titel</option>
          </select>

          <!-- View Toggle -->
          <div class="flex bg-white/5 backdrop-blur-md border border-white/10 rounded-xl p-1">
            <button onclick="switchView('grid')" 
                    class="view-toggle-btn <?= $currentView === 'grid' ? 'active' : '' ?>">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
              </svg>
            </button>
            <button onclick="switchView('list')" 
                    class="view-toggle-btn <?= $currentView === 'list' ? 'active' : '' ?>">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
      <!-- Category Sidebar -->
      <div class="lg:col-span-1 category-nav">
        <?php foreach ($fileCategories as $key => $label): ?>
          <button onclick="changeCategory('<?= $key ?>')" 
                  class="category-item <?= $currentCategory === $key ? 'active' : '' ?>">
            <span class="text-lg">
              <?php
              $icons = [
                'all' => '📁',
                'documents' => '📄',
                'images' => '🖼️',
                'music' => '🎵',
                'videos' => '🎬',
                'archives' => '📦',
                'other' => '📋'
              ];
              echo $icons[$key] ?? '📋';
              ?>
            </span>
            <div class="flex-1">
              <div class="font-medium"><?= $label ?></div>
              <div class="text-xs text-white/50"><?= $stats[$key] ?> Dateien</div>
            </div>
          </button>
        <?php endforeach; ?>
      </div>

      <!-- File Content -->
      <div class="lg:col-span-4 main-content">
        <?php if (empty($files)): ?>
          <!-- Empty State -->
          <div class="glass-card p-12 text-center">
            <div class="flex flex-col items-center gap-6">
              <div class="w-24 h-24 bg-gradient-to-br from-purple-500/20 to-blue-500/20 rounded-2xl flex items-center justify-center">
                <svg class="w-12 h-12 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
              </div>
              <div>
                <h3 class="text-2xl font-semibold text-white mb-2">Keine Dateien gefunden</h3>
                <p class="text-white/60 mb-6">
                  <?= !empty($searchQuery) ? 'Keine Dateien entsprechen Ihrer Suche.' : 'Laden Sie Ihre erste Datei hoch.' ?>
                </p>
                <a href="/upload.php" class="liquid-glass-btn px-8 py-3 inline-flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                  </svg>
                  Erste Datei hochladen
                </a>
              </div>
            </div>
          </div>
        <?php else: ?>
          
          <!-- Grid View -->
          <div id="gridView" class="<?= $currentView === 'grid' ? 'block' : 'hidden' ?>">
            <div class="grid gap-6" style="grid-template-columns:repeat(auto-fill,minmax(220px,1fr))">
              <?php foreach ($files as $file): ?>
                <div class="file-card group" data-file-type="<?= $file['file_type'] ?>">
                  <div class="p-6 h-full flex flex-col">
                    <!-- File Preview/Icon -->
                    <div class="mb-4 flex-shrink-0">
                      <?php if ($file['file_type'] === 'images'): ?>
                        <div class="image-preview">
                          <img src="/uploads/<?= urlencode($file['filename']) ?>" 
                               alt="<?= htmlspecialchars($file['title'] ?? $file['filename']) ?>"
                               onerror="this.parentElement.innerHTML='<div class=\"file-icon file-type-images\"><svg class=\"w-8 h-8\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\"/></svg></div>'"
                               loading="lazy">
                        </div>
                      <?php else: ?>
                        <div class="file-icon file-type-<?= $file['file_type'] ?>">
                          <?php
                          $iconSvg = match($file['file_type']) {
                            'documents' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                            'music' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>',
                            'videos' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>',
                            'archives' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
                            default => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'
                          };
                          ?>
                          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <?= $iconSvg ?>
                          </svg>
                        </div>
                      <?php endif; ?>
                    </div>

                    <!-- File Info -->
                    <div class="flex-1 flex flex-col">
                      <h3 class="font-semibold text-white mb-1 line-clamp-2">
                        <?= htmlspecialchars($file['title'] ?: $file['filename']) ?>
                      </h3>
                      <p class="text-sm text-white/60 mb-3">
                        <?= htmlspecialchars($file['filename']) ?>
                      </p>
                      
                      <div class="flex-1"></div>
                      
                      <!-- File Meta -->
                      <div class="space-y-2 text-xs text-white/50 mb-4">
                        <div class="flex items-center gap-2">
                          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                          </svg>
                          <?= date('d.m.Y', strtotime($file['upload_date'])) ?>
                        </div>
                        <div class="flex items-center gap-2">
                          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                          </svg>
                          <?= $file['formatted_size'] ?>
                        </div>
                      </div>

                      <!-- Actions -->
                      <div class="flex gap-2">
                        <a href="/download.php?id=<?= $file['id'] ?>"
                           class="flex-1 liquid-glass-btn-secondary text-center py-2 text-sm">
                          Download
                        </a>
                        <button onclick="deleteFile(<?= $file['id'] ?>)" 
                                class="liquid-glass-btn-danger px-3 py-2 text-sm">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- List View -->
          <div id="listView" class="<?= $currentView === 'list' ? 'block' : 'hidden' ?>">
            <div class="glass-card overflow-hidden">
              <div class="p-6">
                <div class="space-y-3">
                  <?php foreach ($files as $file): ?>
                    <div class="file-list-item p-4 group">
                      <div class="flex items-center gap-4">
                        <!-- File Icon -->
                        <div class="file-icon-small file-type-<?= $file['file_type'] ?> w-12 h-12 bg-white/10 rounded-lg flex items-center justify-center flex-shrink-0">
                          <?php if ($file['file_type'] === 'images'): ?>
                            <img src="/uploads/<?= urlencode($file['filename']) ?>" 
                                 alt="<?= htmlspecialchars($file['title'] ?? $file['filename']) ?>"
                                 class="w-full h-full object-cover rounded-lg"
                                 onerror="this.parentElement.innerHTML='<svg class=\"w-6 h-6 file-type-images\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\"/></svg>'"
                                 loading="lazy">
                          <?php else: ?>
                            <?php
                            $iconSvg = match($file['file_type']) {
                              'documents' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                              'music' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>',
                              'videos' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>',
                              'archives' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
                              default => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'
                            };
                            ?>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <?= $iconSvg ?>
                            </svg>
                          <?php endif; ?>
                        </div>

                        <!-- File Info -->
                        <div class="flex-1 min-w-0">
                          <h3 class="font-medium text-white truncate">
                            <?= htmlspecialchars($file['title'] ?: $file['filename']) ?>
                          </h3>
                          <div class="flex items-center gap-4 text-sm text-white/60">
                            <span><?= htmlspecialchars($file['filename']) ?></span>
                            <span><?= $file['formatted_size'] ?></span>
                            <span><?= date('d.m.Y', strtotime($file['upload_date'])) ?></span>
                          </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                          <a href="/download.php?id=<?= $file['id'] ?>"
                             class="liquid-glass-btn-secondary px-3 py-2 text-sm">
                            Download
                          </a>
                          <button onclick="deleteFile(<?= $file['id'] ?>)" 
                                  class="liquid-glass-btn-danger px-3 py-2 text-sm">
                            Löschen
                          </button>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>

        <?php endif; ?>
      </div>
    </div>
  </main>

  <script>
    // View switching
    function switchView(view) {
      const gridView = document.getElementById('gridView');
      const listView = document.getElementById('listView');
      const toggleBtns = document.querySelectorAll('.view-toggle-btn');
      
      // Update URL
      const url = new URL(window.location);
      url.searchParams.set('view', view);
      window.history.pushState({}, '', url);
      
      // Update button states
      toggleBtns.forEach(btn => {
        btn.classList.remove('active');
      });
      
      if (view === 'grid') {
        gridView.classList.remove('hidden');
        gridView.classList.add('block');
        listView.classList.add('hidden');
        listView.classList.remove('block');
        document.querySelector('button[onclick="switchView(\'grid\')"]').classList.add('active');
      } else {
        listView.classList.remove('hidden');
        listView.classList.add('block');
        gridView.classList.add('hidden');
        gridView.classList.remove('block');
        document.querySelector('button[onclick="switchView(\'list\')"]').classList.add('active');
      }
    }

    // Category switching
    function changeCategory(category) {
      const url = new URL(window.location);
      url.searchParams.set('category', category);
      url.searchParams.delete('search'); // Clear search when switching category
      window.location.href = url.toString();
    }

    // Search functionality
    function performSearch() {
      const searchValue = document.getElementById('searchInput').value;
      const url = new URL(window.location);
      if (searchValue.trim()) {
        url.searchParams.set('search', searchValue);
      } else {
        url.searchParams.delete('search');
      }
      window.location.href = url.toString();
    }

    // Sort functionality
    function changeSort(sortBy) {
      const url = new URL(window.location);
      url.searchParams.set('sort', sortBy);
      window.location.href = url.toString();
    }

    // Delete file
    function deleteFile(fileId) {
      if (confirm('Sind Sie sicher, dass Sie diese Datei löschen möchten?')) {
        const url = new URL(window.location);
        url.searchParams.set('delete', fileId);
        window.location.href = url.toString();
      }
    }

    // Real-time search
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function(e) {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        performSearch();
      }, 1000); // Wait 1 second after user stops typing
    });

    // Initialize animations
    document.addEventListener('DOMContentLoaded', function() {
      const cards = document.querySelectorAll('.file-card');
      cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
      });
    });
  </script>
</body>
</html>
