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
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .liquid-glass:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(255, 255, 255, 0.25);
      transform: translateY(-2px);
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
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    }

    .file-card:hover::before {
      left: 100%;
    }

    .file-card.selected {
      background: rgba(147, 51, 234, 0.2);
      border-color: rgba(147, 51, 234, 0.5);
      transform: scale(1.02);
    }

    /* Navigation Sidebar */
    .sidebar-glass {
      background: rgba(255, 255, 255, 0.06);
      backdrop-filter: blur(25px) saturate(200%);
      border-right: 1px solid rgba(255, 255, 255, 0.1);
    }

    .nav-item {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 1rem;
      transition: all 0.3s ease;
      cursor: pointer;
      text-decoration: none;
    }

    .nav-item:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(255, 255, 255, 0.2);
      transform: translateX(4px);
      text-decoration: none;
    }

    .nav-item.active {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.3), rgba(79, 70, 229, 0.3));
      border-color: rgba(147, 51, 234, 0.5);
    }

    /* Buttons */
    .liquid-glass-btn-primary {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.8), rgba(79, 70, 229, 0.8));
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      border-radius: 0.75rem;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .liquid-glass-btn-primary:hover {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.9), rgba(79, 70, 229, 0.9));
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(147, 51, 234, 0.4);
    }

    /* Search and Input */
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

    /* Context Menu */
    .context-menu {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 0.75rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      z-index: 1000;
    }

    .context-menu-item {
      transition: all 0.2s ease;
      background: transparent;
      border: none;
      color: rgba(255, 255, 255, 0.8);
      cursor: pointer;
    }

    .context-menu-item:hover {
      background: rgba(255, 255, 255, 0.1);
      color: white;
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
      .file-explorer-container {
        margin-top: 4rem;
        margin-left: 0;
      }
    }

    @media (min-width: 769px) {
      .file-explorer-container {
        margin-left: 16rem;
      }
    }
  </style>
</head>
<body class="min-h-screen">
  <?php require_once __DIR__.'/navbar.php'; ?>

  <div class="file-explorer-container">
    <div class="flex h-screen">
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
          <div class="liquid-glass p-4">
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
          
          <div class="liquid-glass p-4">
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
          <h3 class="text-white font-medium text-sm uppercase tracking-wide mb-4">Dateitypen</h3>
          
          <a href="?<?= http_build_query(array_merge($_GET, ['type' => ''])) ?>" 
             class="nav-item p-3 flex items-center gap-3 text-white/80 hover:text-white <?= $filterType === '' ? 'active' : '' ?>">
            <i class="fas fa-th w-4"></i>
            <span>Alle Dateien</span>
            <span class="ml-auto text-xs bg-white/10 px-2 py-1 rounded-full"><?= $totalFiles ?></span>
          </a>

          <?php foreach($fileTypes as $type => $extensions): ?>
            <?php $count = array_sum(array_intersect_key($typeCounts, array_flip($extensions))); ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['type' => $type])) ?>" 
               class="nav-item p-3 flex items-center gap-3 text-white/80 hover:text-white <?= $filterType === $type ? 'active' : '' ?>">
              <i class="fas fa-<?= getTypeIcon($type) ?> w-4 <?= getTypeColor($type) ?>"></i>
              <span><?= ucfirst($type) ?></span>
              <span class="ml-auto text-xs bg-white/10 px-2 py-1 rounded-full"><?= $count ?></span>
            </a>
          <?php endforeach; ?>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 space-y-3">
          <h3 class="text-white font-medium text-sm uppercase tracking-wide mb-4">Aktionen</h3>
          
          <a href="/upload.php" class="liquid-glass-btn-primary block w-full p-3 rounded-lg text-white text-center font-medium">
            <i class="fas fa-plus mr-2"></i>
            Datei hochladen
          </a>
          
          <button onclick="downloadSelected()" class="nav-item p-3 w-full text-left text-white/80 hover:text-white" disabled id="downloadBtn">
            <i class="fas fa-download mr-3"></i>
            Ausgewählte herunterladen
          </button>

          <button onclick="deleteSelected()" class="nav-item p-3 w-full text-left text-white/80 hover:text-white" disabled id="deleteBtn">
            <i class="fas fa-trash mr-3"></i>
            Ausgewählte löschen
          </button>
        </div>
      </div>

      <!-- Main Content -->
      <div class="flex-1 flex flex-col min-w-0">
        <!-- Header Bar -->
        <div class="liquid-glass m-6 mb-4">
          <div class="p-6">
            <div class="flex items-center justify-between">
              <!-- Search -->
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

              <!-- View Toggle & Bulk Actions -->
              <div class="flex items-center gap-4">
                <div class="view-toggle p-1 flex gap-1">
                  <button onclick="switchView('grid')" 
                          class="view-toggle-btn px-3 py-2 <?= $currentView === 'grid' ? 'active' : '' ?>">
                    <i class="fas fa-th"></i>
                  </button>
                  <button onclick="switchView('list')" 
                          class="view-toggle-btn px-3 py-2 <?= $currentView === 'list' ? 'active' : '' ?>">
                    <i class="fas fa-list"></i>
                  </button>
                </div>

                <button onclick="selectAll()" class="liquid-glass-btn-primary px-4 py-2 text-sm">
                  <i class="fas fa-check-square mr-2"></i>
                  Alle auswählen
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- File Content -->
        <div class="flex-1 p-6 pt-0 overflow-y-auto custom-scrollbar">
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
                  <div class="file-card p-6 group" 
                       data-file-id="<?= $file['id'] ?>"
                       data-filename="<?= htmlspecialchars($file['filename']) ?>"
                       oncontextmenu="showContextMenu(event, <?= $file['id'] ?>)">
                    <div class="flex flex-col h-full">
                      <!-- Selection Checkbox -->
                      <div class="flex justify-between items-start mb-4">
                        <input type="checkbox" class="file-select rounded border-white/20 bg-white/10 text-purple-500 focus:ring-purple-500" 
                               data-file-id="<?= $file['id'] ?>" onchange="updateBulkActions()">
                        
                        <!-- File Icon -->
                        <div class="file-icon-container mx-auto w-16 h-16 bg-white/10 rounded-xl flex items-center justify-center">
                          <svg class="w-8 h-8 <?= $fileInfo['color'] ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $fileInfo['icon'] ?>"/>
                          </svg>
                        </div>

                        <!-- Context Menu Button -->
                        <button class="opacity-0 group-hover:opacity-100 transition-opacity p-1 hover:bg-white/10 rounded" 
                                onclick="showContextMenu(event, <?= $file['id'] ?>)">
                          <i class="fas fa-ellipsis-v text-white/60"></i>
                        </button>
                      </div>

                      <!-- File Info -->
                      <div class="flex-1 text-center">
                        <h3 class="text-white font-medium text-sm mb-1 truncate" title="<?= htmlspecialchars($file['display_name']) ?>">
                          <?= htmlspecialchars($file['display_name']) ?>
                        </h3>
                        <p class="text-white/60 text-xs mb-2"><?= htmlspecialchars($file['category_name'] ?? 'Keine Kategorie') ?></p>
                        <p class="text-white/40 text-xs"><?= date('d.m.Y', $file['upload_timestamp']) ?></p>
                      </div>

                      <!-- Actions -->
                      <div class="mt-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="/api/download.php?id=<?= $file['id'] ?>" 
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
                      <th class="text-left py-4 px-6 font-medium text-white/80">
                        <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()" 
                               class="rounded border-white/20 bg-white/10 text-purple-500 focus:ring-purple-500">
                      </th>
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
                      $fileSize = file_exists($filePath) ? filesize($filePath) : ($file['file_size'] ?? 0);
                      ?>
                      <tr class="border-b border-white/5 hover:bg-white/5 transition-colors group" 
                          data-file-id="<?= $file['id'] ?>"
                          oncontextmenu="showContextMenu(event, <?= $file['id'] ?>)">
                        <td class="py-4 px-6">
                          <input type="checkbox" class="file-select rounded border-white/20 bg-white/10 text-purple-500 focus:ring-purple-500" 
                                 data-file-id="<?= $file['id'] ?>" onchange="updateBulkActions()">
                        </td>
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
                        <td class="py-4 px-6 text-white/70 text-sm"><?= date('d.m.Y', $file['upload_timestamp']) ?></td>
                        <td class="py-4 px-6 text-white/70 text-sm"><?= formatFileSize($fileSize) ?></td>
                        <td class="py-4 px-6 text-right">
                          <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="/api/download.php?id=<?= $file['id'] ?>" 
                               class="py-1 px-3 bg-white/10 hover:bg-white/20 rounded-lg text-white text-xs transition-colors">
                              Download
                            </a>
                            <button onclick="deleteFile(<?= $file['id'] ?>)" 
                                    class="py-1 px-3 bg-red-500/20 hover:bg-red-500/30 rounded-lg text-red-300 text-xs transition-colors">
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

  <!-- Context Menu -->
  <div id="contextMenu" class="context-menu fixed hidden py-2 min-w-48">
    <button onclick="downloadFile(contextMenuFileId)" class="context-menu-item w-full text-left px-4 py-2 text-sm">
      <i class="fas fa-download mr-3"></i>Download
    </button>
    <button onclick="renameFile(contextMenuFileId)" class="context-menu-item w-full text-left px-4 py-2 text-sm">
      <i class="fas fa-edit mr-3"></i>Umbenennen
    </button>
    <button onclick="copyFile(contextMenuFileId)" class="context-menu-item w-full text-left px-4 py-2 text-sm">
      <i class="fas fa-copy mr-3"></i>Kopieren
    </button>
    <hr class="border-white/10 my-1">
    <button onclick="deleteFile(contextMenuFileId)" class="context-menu-item w-full text-left px-4 py-2 text-red-400 hover:text-red-300 text-sm">
      <i class="fas fa-trash mr-3"></i>Löschen
    </button>
  </div>

  <script>
    let contextMenuFileId = null;
    let selectedFiles = new Set();

    // View Switching
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

    // File Selection
    function updateBulkActions() {
      const checkboxes = document.querySelectorAll('.file-select:checked');
      const downloadBtn = document.getElementById('downloadBtn');
      const deleteBtn = document.getElementById('deleteBtn');
      
      selectedFiles = new Set(Array.from(checkboxes).map(cb => cb.dataset.fileId));
      
      const hasSelection = selectedFiles.size > 0;
      downloadBtn.disabled = !hasSelection;
      deleteBtn.disabled = !hasSelection;
      
      downloadBtn.classList.toggle('nav-item', !hasSelection);
      downloadBtn.classList.toggle('liquid-glass-btn-primary', hasSelection);
      deleteBtn.classList.toggle('nav-item', !hasSelection);
      deleteBtn.classList.toggle('liquid-glass-btn-primary', hasSelection);
    }

    function selectAll() {
      const checkboxes = document.querySelectorAll('.file-select');
      const allSelected = Array.from(checkboxes).every(cb => cb.checked);
      
      checkboxes.forEach(cb => cb.checked = !allSelected);
      updateBulkActions();
    }

    function toggleSelectAll() {
      const mainCheckbox = document.getElementById('selectAllCheckbox');
      const fileCheckboxes = document.querySelectorAll('.file-select');
      
      fileCheckboxes.forEach(cb => cb.checked = mainCheckbox.checked);
      updateBulkActions();
    }

    // Context Menu
    function showContextMenu(e, fileId) {
      e.preventDefault();
      e.stopPropagation();
      
      contextMenuFileId = fileId;
      const contextMenu = document.getElementById('contextMenu');
      
      contextMenu.style.left = e.pageX + 'px';
      contextMenu.style.top = e.pageY + 'px';
      contextMenu.classList.remove('hidden');
      
      document.addEventListener('click', hideContextMenu);
    }

    function hideContextMenu() {
      document.getElementById('contextMenu').classList.add('hidden');
      document.removeEventListener('click', hideContextMenu);
    }

    // File Operations
    function downloadFile(fileId) {
      window.location.href = `/api/download.php?id=${fileId}`;
      hideContextMenu();
    }

    function deleteFile(fileId) {
      if (confirm('Sind Sie sicher, dass Sie diese Datei löschen möchten?')) {
        fetch('/api/file-operations.php?action=delete', {
          method: 'DELETE',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ file_id: fileId })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
          } else {
            showNotification(data.error, 'error');
          }
        });
      }
      hideContextMenu();
    }

    function renameFile(fileId) {
      const newName = prompt('Neuer Name:');
      if (newName && newName.trim()) {
        fetch('/api/file-operations.php?action=rename', {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ file_id: fileId, new_name: newName.trim() })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
          } else {
            showNotification(data.error, 'error');
          }
        });
      }
      hideContextMenu();
    }

    function copyFile(fileId) {
      const newName = prompt('Name für die Kopie:');
      if (newName !== null) {
        fetch('/api/file-operations.php?action=copy', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ file_id: fileId, new_name: newName.trim() })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
          } else {
            showNotification(data.error, 'error');
          }
        });
      }
      hideContextMenu();
    }

    function downloadSelected() {
      if (selectedFiles.size === 0) return;
      
      if (selectedFiles.size === 1) {
        const fileId = Array.from(selectedFiles)[0];
        downloadFile(fileId);
      } else {
        showNotification('Bulk download wird implementiert...', 'info');
      }
    }

    function deleteSelected() {
      if (selectedFiles.size === 0) return;
      
      if (confirm(`${selectedFiles.size} Dateien löschen?`)) {
        fetch('/api/file-operations.php?action=bulk-delete', {
          method: 'DELETE',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ file_ids: Array.from(selectedFiles) })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
          } else {
            showNotification(data.error, 'error');
          }
        });
      }
    }

    // Notification system
    function showNotification(message, type = 'info') {
      const notification = document.createElement('div');
      const colors = {
        success: 'bg-green-500/20 border-green-500/50',
        error: 'bg-red-500/20 border-red-500/50',
        warning: 'bg-yellow-500/20 border-yellow-500/50',
        info: 'bg-blue-500/20 border-blue-500/50'
      };
      
      notification.className = `fixed top-4 right-4 p-4 rounded-xl backdrop-blur-lg border ${colors[type]} text-white z-50 max-w-sm`;
      notification.textContent = message;
      
      document.body.appendChild(notification);
      
      setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
      }, 3000);
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
      // Update bulk actions on page load
      updateBulkActions();
      
      // Hide context menu on outside click
      document.addEventListener('click', function(e) {
        if (!e.target.closest('#contextMenu')) {
          hideContextMenu();
        }
      });
    });
  </script>

  <?php
  // Helper functions for template
  function getTypeIcon($type) {
    $icons = [
      'documents' => 'file-alt',
      'images' => 'image',
      'videos' => 'video',
      'audio' => 'music',
      'archives' => 'archive',
      'code' => 'code'
    ];
    return $icons[$type] ?? 'file';
  }

  function getTypeColor($type) {
    $colors = [
      'documents' => 'text-blue-400',
      'images' => 'text-green-400',
      'videos' => 'text-pink-400',
      'audio' => 'text-purple-400',
      'archives' => 'text-yellow-400',
      'code' => 'text-cyan-400'
    ];
    return $colors[$type] ?? 'text-gray-400';
  }
  ?>
</body>
</html>
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
                      <th class="text-left py-4 px-6 font-medium text-white/80">
                        <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()" 
                               class="rounded border-white/20 bg-white/10 text-purple-500 focus:ring-purple-500">
                      </th>
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
                      ?>                      <tr class="border-b border-white/5 hover:bg-white/5 transition-colors group" 
                          draggable="true" 
                          data-file-id="<?= $file['id'] ?>"
                          data-filename="<?= htmlspecialchars($file['filename']) ?>"
                          data-category-id="<?= $file['category_id'] ?? 0 ?>"
                          oncontextmenu="showContextMenu(event, <?= $file['id'] ?>)">
                        <td class="py-4 px-6">
                          <input type="checkbox" class="file-select rounded border-white/20 bg-white/10 text-purple-500 focus:ring-purple-500" 
                                 data-file-id="<?= $file['id'] ?>" onchange="updateBulkActions()">
                        </td>
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
  <!-- Context Menu -->
  <div id="contextMenu" class="context-menu fixed hidden py-2 min-w-48">
    <button onclick="downloadFile(contextMenuFileId)" class="context-menu-item w-full text-left px-4 py-2 text-white/80 hover:text-white text-sm">
      <i class="fas fa-download mr-3"></i>Download
    </button>
    <button onclick="renameFile(contextMenuFileId)" class="context-menu-item w-full text-left px-4 py-2 text-white/80 hover:text-white text-sm">
      <i class="fas fa-edit mr-3"></i>Umbenennen
    </button>
    <button onclick="showMoveModal(contextMenuFileId)" class="context-menu-item w-full text-left px-4 py-2 text-white/80 hover:text-white text-sm">
      <i class="fas fa-folder mr-3"></i>Verschieben
    </button>
    <hr class="border-white/10 my-1">
    <button onclick="deleteFile(contextMenuFileId)" class="context-menu-item w-full text-left px-4 py-2 text-red-400 hover:text-red-300 text-sm">
      <i class="fas fa-trash mr-3"></i>Löschen
    </button>
  </div>

  <!-- Move File Modal -->
  <div id="moveModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="glass-card p-6 max-w-md w-full mx-4">
      <h3 class="text-xl font-semibold text-white mb-4">Datei verschieben</h3>
      
      <div class="mb-4">
        <label class="block text-white/80 text-sm mb-2">Zielordner auswählen:</label>
        <select id="targetFolderSelect" class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-white focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20">
          <option value="">Hauptordner</option>
          <?php foreach ($categories as $category): ?>
            <option value="<?= htmlspecialchars($category['name']) ?>"><?= htmlspecialchars($category['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <div class="mb-4">
        <label class="block text-white/80 text-sm mb-2">Oder neuen Ordner erstellen:</label>
        <input type="text" id="newFolderInput" placeholder="Neuer Ordnername" 
               class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-white placeholder-white/50 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20">
      </div>
      
      <div class="flex gap-3 justify-end">
        <button onclick="closeMoveModal()" class="px-4 py-2 liquid-glass-btn-secondary">
          Abbrechen
        </button>
        <button onclick="confirmMove()" class="px-4 py-2 liquid-glass-btn-primary">
          Verschieben
        </button>
      </div>
    </div>
  </div>

  <script>
    let draggedElement = null;
    let contextMenuFileId = null;
    let selectedFiles = new Set();

    // View Switching
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

    // File Selection
    function updateBulkActions() {
      const checkboxes = document.querySelectorAll('.file-select:checked');
      const downloadBtn = document.getElementById('downloadBtn');
      
      selectedFiles = new Set(Array.from(checkboxes).map(cb => cb.dataset.fileId));
      
      downloadBtn.disabled = selectedFiles.size === 0;
      downloadBtn.classList.toggle('liquid-glass-btn-primary', selectedFiles.size > 0);
      downloadBtn.classList.toggle('liquid-glass-btn-secondary', selectedFiles.size === 0);
    }

    function selectAll() {
      const checkboxes = document.querySelectorAll('.file-select');
      const allSelected = Array.from(checkboxes).every(cb => cb.checked);
      
      checkboxes.forEach(cb => cb.checked = !allSelected);
      updateBulkActions();
    }

    function toggleSelectAll() {
      const mainCheckbox = document.getElementById('selectAllCheckbox');
      const fileCheckboxes = document.querySelectorAll('.file-select');
      
      fileCheckboxes.forEach(cb => cb.checked = mainCheckbox.checked);
      updateBulkActions();
    }

    // Drag and Drop
    document.addEventListener('DOMContentLoaded', function() {
      const fileCards = document.querySelectorAll('.file-card, tr[data-file-id]');
      const dropZones = document.querySelectorAll('.drop-zone');

      // Add drag event listeners to files
      fileCards.forEach(card => {
        card.addEventListener('dragstart', handleDragStart);
        card.addEventListener('dragend', handleDragEnd);
      });

      // Add drop event listeners to folders
      dropZones.forEach(zone => {
        zone.addEventListener('dragover', handleDragOver);
        zone.addEventListener('dragenter', handleDragEnter);
        zone.addEventListener('dragleave', handleDragLeave);
        zone.addEventListener('drop', handleDrop);
      });
    });

    function handleDragStart(e) {
      draggedElement = this;
      this.classList.add('dragging');
      e.dataTransfer.effectAllowed = 'move';
      e.dataTransfer.setData('text/html', this.outerHTML);
    }

    function handleDragEnd() {
      this.classList.remove('dragging');
      draggedElement = null;
      
      // Remove all drop indicators
      document.querySelectorAll('.drop-zone').forEach(zone => {
        zone.classList.remove('drag-over');
      });
    }

    function handleDragOver(e) {
      if (e.preventDefault) {
        e.preventDefault();
      }
      e.dataTransfer.dropEffect = 'move';
      return false;
    }

    function handleDragEnter() {
      this.classList.add('drag-over');
    }

    function handleDragLeave() {
      this.classList.remove('drag-over');
    }

    function handleDrop(e) {
      if (e.stopPropagation) {
        e.stopPropagation();
      }

      this.classList.remove('drag-over');

      if (draggedElement) {
        const fileId = draggedElement.dataset.fileId;
        const newCategoryId = this.dataset.category;
        
        if (fileId && newCategoryId) {
          moveFileToCategory(fileId, newCategoryId);
        }
      }

      return false;
    }    // File Operations - Enhanced Move Function
    function moveFileToCategory(fileId, targetFolder) {
      showNotification('Verschiebe Datei...', 'info');
      
      fetch('/api/move-file.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          file_id: fileId,
          target_folder: targetFolder
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showNotification('Datei erfolgreich verschoben', 'success');
          setTimeout(() => window.location.reload(), 1000);
        } else {
          showNotification('Fehler beim Verschieben: ' + data.message, 'error');
        }
      })
      .catch(error => {
        showNotification('Fehler beim Verschieben der Datei', 'error');
        console.error('Error:', error);
      });
    }

    function deleteFile(fileId) {
      if (confirm('Sind Sie sicher, dass Sie diese Datei löschen möchten?')) {
        const url = new URL(window.location);
        url.searchParams.set('delete', fileId);
        window.location.href = url.toString();
      }
    }    function downloadSelected() {
      if (selectedFiles.size === 0) {
        showNotification('Keine Dateien ausgewählt', 'warning');
        return;
      }
      
      const fileIds = Array.from(selectedFiles);
      
      if (fileIds.length === 1) {
        window.location.href = `/download.php?id=${fileIds[0]}`;
      } else {
        // Multiple files - use bulk download API
        showNotification('Erstelle Download-Archiv...', 'info');
        
        fetch('/api/download-multiple.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ file_ids: fileIds })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showNotification(`${data.files_count} Dateien bereit zum Download`, 'success');
            // Trigger download
            window.location.href = data.download_url;
            selectedFiles.clear();
            updateFileDisplay();
          } else {
            showNotification('Fehler beim Erstellen des Downloads: ' + data.message, 'error');
          }
        })
        .catch(error => {
          showNotification('Fehler beim Erstellen des Downloads', 'error');
          console.error('Error:', error);
        });
      }
    }

    // Context Menu
    function showContextMenu(e, fileId) {
      e.preventDefault();
      e.stopPropagation();
      
      contextMenuFileId = fileId;
      const contextMenu = document.getElementById('contextMenu');
      
      contextMenu.style.left = e.pageX + 'px';
      contextMenu.style.top = e.pageY + 'px';
      contextMenu.classList.remove('hidden');
      
      // Hide on outside click
      document.addEventListener('click', hideContextMenu);
    }

    function hideContextMenu() {
      document.getElementById('contextMenu').classList.add('hidden');
      document.removeEventListener('click', hideContextMenu);
    }

    function downloadFile(fileId) {
      window.location.href = `/download.php?id=${fileId}`;
      hideContextMenu();
    }    function renameFile(fileId) {
      const fileElement = document.querySelector(`[data-file-id="${fileId}"]`);
      const currentName = fileElement ? fileElement.dataset.filename : '';
      
      const newName = prompt('Neuer Dateiname:', currentName);
      if (newName && newName !== currentName) {
        showNotification('Benenne Datei um...', 'info');
        
        fetch('/api/rename-file.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            file_id: fileId,
            new_name: newName
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showNotification('Datei erfolgreich umbenannt', 'success');
            // Update the display
            if (fileElement) {
              const nameElement = fileElement.querySelector('.file-name');
              if (nameElement) {
                nameElement.textContent = data.new_name;
              }
              fileElement.dataset.filename = data.new_name;
            }
          } else {
            showNotification('Fehler beim Umbenennen: ' + data.message, 'error');
          }
        })
        .catch(error => {
          showNotification('Fehler beim Umbenennen der Datei', 'error');
          console.error('Error:', error);
        });
      }
      hideContextMenu();
    }    function moveToFolder(fileId) {
      showMoveModal(fileId);
      hideContextMenu();
    }

    function showMoveModal(fileId) {
      contextMenuFileId = fileId;
      document.getElementById('moveModal').classList.remove('hidden');
      document.getElementById('targetFolderSelect').value = '';
      document.getElementById('newFolderInput').value = '';
    }

    function closeMoveModal() {
      document.getElementById('moveModal').classList.add('hidden');
      contextMenuFileId = null;
    }

    function confirmMove() {
      const targetFolder = document.getElementById('targetFolderSelect').value;
      const newFolder = document.getElementById('newFolderInput').value.trim();
      
      const finalTarget = newFolder || targetFolder;
      
      if (contextMenuFileId) {
        moveFileToCategory(contextMenuFileId, finalTarget);
        closeMoveModal();
      }
    }
      hideContextMenu();
    }    // Enhanced Notifications with Liquid Glass Design
    function showNotification(message, type = 'info') {
      const notification = document.createElement('div');
      const colors = {
        success: 'border-green-500/50 bg-green-500/20',
        error: 'border-red-500/50 bg-red-500/20',
        warning: 'border-yellow-500/50 bg-yellow-500/20',
        info: 'border-blue-500/50 bg-blue-500/20'
      };
      
      const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
      };
      
      notification.className = `fixed top-4 right-4 p-4 rounded-xl backdrop-blur-lg border ${colors[type]} text-white z-50 max-w-sm shadow-2xl`;
      notification.style.animation = 'slideInRight 0.3s ease-out';
      
      notification.innerHTML = `
        <div class="flex items-center gap-3">
          <i class="${icons[type]} text-lg"></i>
          <span class="flex-1">${message}</span>
          <button onclick="this.parentElement.parentElement.remove()" class="text-white/60 hover:text-white ml-2">
            <i class="fas fa-times"></i>
          </button>
        </div>
      `;
      
      document.body.appendChild(notification);
      
      setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-in';
        setTimeout(() => notification.remove(), 300);
      }, 4000);
    }

    // Add CSS for notification animations
    const notificationStyles = `
      @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
      }
      @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
      }
    `;
    const styleSheet = document.createElement('style');
    styleSheet.textContent = notificationStyles;
    document.head.appendChild(styleSheet);

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
      // Stagger animation for file cards
      const cards = document.querySelectorAll('.file-card, tr[data-file-id]');
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

      // Update category counts
      updateCategoryCounts();
    });

    function updateCategoryCounts() {
      // Count files per category and update badges
      const files = <?= json_encode($files) ?>;
      const counts = { '1': 0, '2': 0, '3': 0, '4': 0 };
      
      files.forEach(file => {
        if (file.category_id && counts.hasOwnProperty(file.category_id)) {
          counts[file.category_id]++;
        }
      });
      
      document.getElementById('contracts-count').textContent = counts['1'];
      document.getElementById('invoices-count').textContent = counts['2'];
      document.getElementById('insurance-count').textContent = counts['3'];
      document.getElementById('other-count').textContent = counts['4'];
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
      if (e.ctrlKey && e.key === 'k') {
        e.preventDefault();
        document.querySelector('input[name="search"]').focus();
      }
      if (e.key === 'Escape') {
        document.querySelector('input[name="search"]').blur();
        hideContextMenu();
      }
      if (e.ctrlKey && e.key === 'a') {
        e.preventDefault();
        selectAll();
      }
    });
  </script>
</body>
</html>
