<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Notizen | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
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
    
    .glass-card {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    
    .note-card {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: all 0.3s ease;
    }
    
    .note-card:hover {
      background: rgba(255, 255, 255, 0.15);
      transform: translateY(-2px);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
    }
    
    .category-tag {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .search-bar {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
    }
    
    .btn-primary {
      background: linear-gradient(135deg, rgba(34, 197, 94, 0.8) 0%, rgba(34, 197, 94, 0.6) 100%);
      border: 1px solid rgba(34, 197, 94, 0.3);
    }
    
    .note-type-badge {
      font-size: 0.75rem;
      padding: 0.25rem 0.5rem;
      border-radius: 0.375rem;
      font-weight: 500;
    }
    
    .type-daily { background: rgba(59, 130, 246, 0.2); color: #93c5fd; }
    .type-knowledge { background: rgba(251, 191, 36, 0.2); color: #fcd34d; }
    .type-documentation { background: rgba(139, 92, 246, 0.2); color: #c4b5fd; }
    .type-note { background: rgba(156, 163, 175, 0.2); color: #d1d5db; }
  </style>
</head>

<body class="min-h-screen">
  <?php require_once __DIR__ . '/navbar.php'; ?>

  <main class="ml-0 mt-16 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
    <div class="max-w-7xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-3xl font-bold text-white">Notizen & Zettelkasten</h1>
          <p class="text-white/60 mt-1">Verwalte dein Wissen und verknüpfe Ideen</p>
        </div>
        
        <div class="flex flex-wrap gap-3">
          <button onclick="createDailyNote()" class="btn-primary px-4 py-2 text-white rounded-lg hover:opacity-90 transition-opacity">
            <i class="fas fa-calendar-day mr-2"></i>Daily Note
          </button>
          <button onclick="openNoteModal()" class="btn-primary px-4 py-2 text-white rounded-lg hover:opacity-90 transition-opacity">
            <i class="fas fa-plus mr-2"></i>Neue Notiz
          </button>
          <button onclick="showGraphView()" class="bg-purple-600/20 border border-purple-400/30 px-4 py-2 text-purple-300 rounded-lg hover:bg-purple-600/30 transition-colors">
            <i class="fas fa-project-diagram mr-2"></i>Graph View
          </button>
        </div>
      </div>

      <!-- Filters & Search -->
      <div class="glass-card p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Search -->
          <div class="md:col-span-2">
            <div class="relative">
              <input type="text" 
                     placeholder="Notizen durchsuchen..." 
                     value="<?= htmlspecialchars($search) ?>"
                     onkeyup="filterNotes(this.value)"
                     class="w-full px-4 py-2 pl-10 search-bar text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/50">
              <i class="fas fa-search absolute left-3 top-3 text-white/50"></i>
            </div>
          </div>
          
          <!-- Category Filter -->
          <div>
            <select onchange="filterByCategory(this.value)" class="w-full px-4 py-2 search-bar text-white rounded-lg">
              <option value="">Alle Kategorien</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $category == $cat['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($cat['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <!-- Type Filter -->
          <div>
            <select onchange="filterByType(this.value)" class="w-full px-4 py-2 search-bar text-white rounded-lg">
              <option value="">Alle Typen</option>
              <option value="note" <?= $type === 'note' ? 'selected' : '' ?>>Notizen</option>
              <option value="daily" <?= $type === 'daily' ? 'selected' : '' ?>>Daily Notes</option>
              <option value="knowledge" <?= $type === 'knowledge' ? 'selected' : '' ?>>Wissen</option>
              <option value="documentation" <?= $type === 'documentation' ? 'selected' : '' ?>>Dokumentation</option>
            </select>
          </div>
        </div>
        
        <!-- View Toggle -->
        <div class="mt-4 flex justify-between items-center">
          <div class="flex gap-2">
            <button onclick="setView('grid')" class="px-3 py-1 rounded text-sm <?= $view === 'grid' ? 'bg-white/20 text-white' : 'text-white/60' ?>">
              <i class="fas fa-th mr-1"></i>Grid
            </button>
            <button onclick="setView('list')" class="px-3 py-1 rounded text-sm <?= $view === 'list' ? 'bg-white/20 text-white' : 'text-white/60' ?>">
              <i class="fas fa-list mr-1"></i>Liste
            </button>
            <button onclick="setView('timeline')" class="px-3 py-1 rounded text-sm <?= $view === 'timeline' ? 'bg-white/20 text-white' : 'text-white/60' ?>">
              <i class="fas fa-clock mr-1"></i>Timeline
            </button>
          </div>
          
          <div class="text-white/60 text-sm">
            <?= count($notes) ?> Notizen gefunden
          </div>
        </div>
      </div>

      <!-- Notes Grid/List -->
      <?php if ($view === 'grid'): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <?php foreach ($notes as $note): ?>
            <div class="note-card p-5 rounded-lg cursor-pointer" onclick="openNote(<?= $note['id'] ?>)">
              <div class="flex justify-between items-start mb-3">
                <span class="note-type-badge type-<?= $note['type'] ?>">
                  <?= ucfirst($note['type']) ?>
                </span>
                <div class="flex gap-1">
                  <?php if ($note['is_favorite']): ?>
                    <i class="fas fa-star text-yellow-400 text-sm"></i>
                  <?php endif; ?>
                  <?php if ($note['link_count'] > 0): ?>
                    <span class="text-xs bg-blue-500/20 text-blue-300 px-2 py-1 rounded">
                      <?= $note['link_count'] ?> <i class="fas fa-link"></i>
                    </span>
                  <?php endif; ?>
                </div>
              </div>
              
              <h3 class="text-white font-semibold mb-2 line-clamp-2">
                <?= htmlspecialchars($note['title']) ?>
              </h3>
              
              <?php if ($note['content']): ?>
                <p class="text-white/70 text-sm mb-3 line-clamp-3">
                  <?= htmlspecialchars(substr(strip_tags($note['content']), 0, 150)) ?>...
                </p>
              <?php endif; ?>
              
              <div class="flex justify-between items-center text-xs text-white/50">
                <span><?= date('d.m.Y', strtotime($note['updated_at'])) ?></span>
                <?php if ($note['category_name']): ?>
                  <span class="category-tag px-2 py-1 rounded" style="background-color: <?= $note['category_color'] ?>20;">
                    <?= htmlspecialchars($note['category_name']) ?>
                  </span>
                <?php endif; ?>
              </div>
              
              <?php if ($note['tags']): ?>
                <div class="mt-2 flex flex-wrap gap-1">
                  <?php foreach (explode(',', $note['tags']) as $tag): ?>
                    <span class="text-xs bg-white/10 text-white/70 px-2 py-1 rounded">
                      #<?= trim($tag) ?>
                    </span>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <!-- List View -->
        <div class="glass-card overflow-hidden">
          <div class="divide-y divide-white/10">
            <?php foreach ($notes as $note): ?>
              <div class="p-4 hover:bg-white/5 transition-colors cursor-pointer" onclick="openNote(<?= $note['id'] ?>)">
                <div class="flex justify-between items-start">
                  <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                      <h3 class="text-white font-semibold"><?= htmlspecialchars($note['title']) ?></h3>
                      <span class="note-type-badge type-<?= $note['type'] ?>"><?= ucfirst($note['type']) ?></span>
                      <?php if ($note['category_name']): ?>
                        <span class="category-tag px-2 py-1 rounded text-xs">
                          <?= htmlspecialchars($note['category_name']) ?>
                        </span>
                      <?php endif; ?>
                    </div>
                    
                    <?php if ($note['content']): ?>
                      <p class="text-white/70 text-sm mb-2">
                        <?= htmlspecialchars(substr(strip_tags($note['content']), 0, 200)) ?>...
                      </p>
                    <?php endif; ?>
                    
                    <div class="flex items-center gap-4 text-xs text-white/50">
                      <span><i class="fas fa-calendar mr-1"></i><?= date('d.m.Y H:i', strtotime($note['updated_at'])) ?></span>
                      <?php if ($note['link_count'] > 0): ?>
                        <span><i class="fas fa-link mr-1"></i><?= $note['link_count'] ?> Verknüpfungen</span>
                      <?php endif; ?>
                    </div>
                  </div>
                  
                  <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick="event.stopPropagation(); editNote(<?= $note['id'] ?>)" class="text-white/60 hover:text-white">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="event.stopPropagation(); toggleFavorite(<?= $note['id'] ?>)" class="text-white/60 hover:text-yellow-400">
                      <i class="fas fa-star <?= $note['is_favorite'] ? 'text-yellow-400' : '' ?>"></i>
                    </button>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
      
      <?php if (empty($notes)): ?>
        <div class="glass-card p-12 text-center">
          <i class="fas fa-sticky-note text-white/30 text-6xl mb-4"></i>
          <h3 class="text-xl text-white/70 mb-2">Keine Notizen gefunden</h3>
          <p class="text-white/50 mb-6">Erstelle deine erste Notiz oder passe deine Filter an.</p>
          <button onclick="openNoteModal()" class="btn-primary px-6 py-3 text-white rounded-lg">
            Erste Notiz erstellen
          </button>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <script>
    function openNote(id) {
      window.location.href = `/note_detail.php?id=${id}`;
    }
    
    function editNote(id) {
      window.location.href = `/note_edit.php?id=${id}`;
    }
    
    function createDailyNote() {
      const today = new Date().toISOString().split('T')[0];
      window.location.href = `/note_create.php?type=daily&date=${today}`;
    }
    
    function openNoteModal() {
      window.location.href = '/note_create.php';
    }
    
    function showGraphView() {
      window.location.href = '/notes_graph.php';
    }
    
    function filterNotes(search) {
      const url = new URL(window.location);
      if (search) {
        url.searchParams.set('search', search);
      } else {
        url.searchParams.delete('search');
      }
      window.location.href = url.toString();
    }
    
    function filterByCategory(categoryId) {
      const url = new URL(window.location);
      if (categoryId) {
        url.searchParams.set('category', categoryId);
      } else {
        url.searchParams.delete('category');
      }
      window.location.href = url.toString();
    }
    
    function filterByType(type) {
      const url = new URL(window.location);
      if (type) {
        url.searchParams.set('type', type);
      } else {
        url.searchParams.delete('type');
      }
      window.location.href = url.toString();
    }
    
    function setView(view) {
      const url = new URL(window.location);
      url.searchParams.set('view', view);
      window.location.href = url.toString();
    }
    
    function toggleFavorite(id) {
      fetch(`/api/note_favorite.php`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${id}`
      }).then(() => {
        location.reload();
      });
    }
  </script>
</body>
</html>
