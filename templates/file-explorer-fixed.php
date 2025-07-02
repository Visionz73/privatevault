<!DOCTYPE html>
<html lang="de" data-theme="light" class="h-full">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Datei-Explorer | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="/public/assets/css/file-explorer.css">
  <script src="/public/assets/libs/pdfjs/pdf.min.js"></script>
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
<body class="flex h-full">
  <!-- Dark/Light-Toggle -->
  <button class="theme-toggle" onclick="toggleTheme()">🌓</button>

  <!-- Sidebar Navigation -->
  <aside class="sidebar">
    <nav>
      <a href="?section=downloads" class="<?= ($_GET['section']??'')==='downloads'?'active':'' ?>">Downloads</a>
      <a href="?section=documents" class="<?= ($_GET['section']??'')==='documents'?'active':'' ?>">Dokumente</a>
      <a href="?section=images" class="<?= ($_GET['section']??'')==='images'?'active':'' ?>">Bilder</a>
      <!-- ... weitere Einträge ... -->
    </nav>
    <!-- Live-Suche -->
    <div class="mt-4">
      <input type="text" id="liveSearch" placeholder="Suchen…" oninput="filterFiles()">
    </div>
  </aside>

  <!-- Main Content -->
  <main class="main flex-1 p-6 overflow-auto">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
      <a href="/">Home</a> › <span><?= ucfirst($filterType?:'Alle') ?></span>
    </div>

    <!-- View Toggle -->
    <div class="view-toggle mb-4">
      <button onclick="switchView('grid')" class="<?= $currentView==='grid'?'active':'' ?>">Grid</button>
      <button onclick="switchView('list')" class="<?= $currentView==='list'?'active':'' ?>">Liste</button>
    </div>

    <!-- Files -->
    <?php if($currentView==='grid'): ?>
      <div id="gridView" class="file-grid">
        <?php foreach($files as $f): ?>
          <div class="file-card" data-filename="<?= htmlspecialchars($f['filename']) ?>" onclick="previewFile(this)">
            <div><?= htmlspecialchars($f['filename']) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <table id="listView" class="file-list">
        <tr><th>Name</th><th>Typ</th><th>Aktionen</th></tr>
        <?php foreach($files as $f): ?>
          <tr>
            <td><?= htmlspecialchars($f['filename']) ?></td>
            <td><?= pathinfo($f['filename'],PATHINFO_EXTENSION) ?></td>
            <td>
              <button onclick="previewFileElement(this)">Vorschau</button>
              <button onclick="deleteFile(<?= $f['id'] ?>)">Löschen</button>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>

  </main>

  <!-- Preview-Modal -->
  <div id="previewBackdrop" class="modal-backdrop" onclick="closePreview()">
    <div class="modal" onclick="event.stopPropagation()">
      <button class="modal-close" onclick="closePreview()">×</button>
      <div id="previewContent"></div>
    </div>
  </div>

  <script src="/public/assets/js/file-explorer-macos.js"></script>
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

    // Dark/Light theme toggle
    function toggleTheme() {
      const htmlElement = document.documentElement;
      if (htmlElement.getAttribute('data-theme') === 'light') {
        htmlElement.setAttribute('data-theme', 'dark');
      } else {
        htmlElement.setAttribute('data-theme', 'light');
      }
    }

    // Live search filter
    function filterFiles() {
      const query = document.getElementById('liveSearch').value.toLowerCase();
      const files = document.querySelectorAll('.file-card, .file-list tr');
      
      files.forEach(file => {
        const fileName = file.querySelector('.file-name')?.textContent.toLowerCase() || '';
        if (fileName.includes(query)) {
          file.style.display = '';
        } else {
          file.style.display = 'none';
        }
      });
    }

    // File preview
    function previewFile(element) {
      const fileName = element.getAttribute('data-filename');
      const previewContent = document.getElementById('previewContent');
      
      // Clear previous content
      previewContent.innerHTML = '';
      
      // Determine file type and create appropriate viewer
      const ext = fileName.split('.').pop().toLowerCase();
      if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
        // Image preview
        const img = document.createElement('img');
        img.src = '/uploads/' + fileName;
        img.classList.add('w-full', 'h-auto');
        previewContent.appendChild(img);
      } else if (['mp4', 'webm', 'ogg'].includes(ext)) {
        // Video preview
        const video = document.createElement('video');
        video.src = '/uploads/' + fileName;
        video.controls = true;
        video.classList.add('w-full', 'h-auto');
        previewContent.appendChild(video);
      } else if (['mp3', 'wav', 'ogg'].includes(ext)) {
        // Audio preview
        const audio = document.createElement('audio');
        audio.src = '/uploads/' + fileName;
        audio.controls = true;
        previewContent.appendChild(audio);
      } else if (ext === 'pdf') {
        // PDF preview using PDF.js
        const pdfViewer = document.createElement('div');
        pdfViewer.classList.add('pdf-viewer');
        const pdfUrl = '/uploads/' + fileName;
        
        // Asynchronously download PDF.js and render the PDF
        const loadingTask = pdfjsLib.getDocument(pdfUrl);
        loadingTask.promise.then(pdf => {
          // Fetch the first page of the PDF
          pdf.getPage(1).then(page => {
            const scale = 1.5;
            const viewport = page.getViewport({ scale: scale });
            
            // Prepare canvas using PDF page dimensions
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;
            pdfViewer.appendChild(canvas);
            
            // Render PDF page into canvas context
            const renderContext = {
              canvasContext: context,
              viewport: viewport
            };
            page.render(renderContext);
          });
        }, reason => {
          console.error('Error loading PDF: ' + reason);
        });
        
        previewContent.appendChild(pdfViewer);
      } else {
        // Unsupported file type
        previewContent.innerHTML = '<p class="text-red-500">Vorschau nicht verfügbar für diesen Dateityp.</p>';
      }
      
      // Show the modal
      document.getElementById('previewBackdrop').classList.remove('hidden');
    }

    function closePreview() {
      document.getElementById('previewBackdrop').classList.add('hidden');
      const previewContent = document.getElementById('previewContent');
      
      // Clear content
      previewContent.innerHTML = '';
    }
  </script>
</body>
</html>
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
