<?php
// Modern documents section with enhanced liquid glass styling
$docSubtabs = [
  'documents' => 'Alle Dokumente',
  'contracts' => 'Verträge', 
  'invoices' => 'Rechnungen',
  'insurance' => 'Versicherungen',
  'other_docs' => 'Sonstige'
];
$currentDocSubtab = $_GET['subtab'] ?? 'documents';
$currentView = $_GET['view'] ?? 'grid'; // Default to grid view
?>

<div class="space-y-6">
  <!-- Enhanced Section Header with Liquid Glass Effect -->
  <div class="glass-card p-8 mb-8">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h2 class="text-3xl font-bold text-white mb-2 bg-gradient-to-r from-purple-400 to-blue-400 bg-clip-text text-transparent">
          Dokument-Verwaltung
        </h2>
        <p class="text-white/70">Verwalten Sie Ihre wichtigen Dokumente mit modernster Technologie</p>
      </div>
      <div class="text-right">
        <div class="text-sm text-white/60 mb-1">Gesamt</div>
        <div class="text-2xl font-bold text-white"><?= count($docs ?? []) ?></div>
      </div>
    </div>
    
    <!-- Action Bar -->
    <div class="flex flex-wrap gap-4 items-center justify-between">
      <div class="flex items-center gap-4">
        <a href="/upload.php" class="liquid-glass-btn-primary px-6 py-3 font-medium inline-flex items-center gap-2 group">
          <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
          </svg>
          Dokument hochladen
        </a>
        
        <!-- View Toggle Buttons -->
        <div class="flex bg-white/5 backdrop-blur-md border border-white/10 rounded-xl p-1">
          <button onclick="switchDocumentView('grid')" 
                  class="view-toggle-btn px-4 py-2 rounded-lg transition-all duration-300 <?= $currentView === 'grid' ? 'bg-white/20 text-white shadow-lg' : 'text-white/60 hover:text-white hover:bg-white/10' ?>">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
          </button>
          <button onclick="switchDocumentView('list')" 
                  class="view-toggle-btn px-4 py-2 rounded-lg transition-all duration-300 <?= $currentView === 'list' ? 'bg-white/20 text-white shadow-lg' : 'text-white/60 hover:text-white hover:bg-white/10' ?>">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
            </svg>
          </button>
        </div>
      </div>
      
      <!-- Enhanced Search -->
      <div class="flex items-center gap-4">
        <div class="relative">
          <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input 
            type="text" 
            placeholder="Dokumente durchsuchen..." 
            class="liquid-glass-input pl-10 pr-4 py-3 text-sm w-80"
            value="<?= htmlspecialchars($_GET['title_filter'] ?? '') ?>"
            id="documentSearch"
          >
        </div>
      </div>
    </div>
  </div>
  
  <!-- Enhanced Subtab Navigation -->
  <div class="glass-card p-3 mb-8">
    <div class="flex flex-wrap gap-2">
      <?php foreach ($docSubtabs as $key => $label): ?>
        <?php 
        $isActive = $currentDocSubtab === $key;
        $href = "?tab=documents&subtab=" . $key . "&view=" . $currentView;
        ?>
        <a href="<?= $href ?>" 
           class="liquid-glass-tab px-6 py-3 rounded-xl transition-all duration-300 text-sm font-medium relative overflow-hidden group <?= $isActive ? 'active' : '' ?>">
          <span class="relative z-10"><?= htmlspecialchars($label) ?></span>
          <?php if ($isActive): ?>
            <div class="absolute inset-0 bg-gradient-to-r from-purple-500/30 to-blue-500/30 rounded-xl"></div>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
    
  <!-- Document Content with Advanced Views -->
  <?php if ($currentDocSubtab === 'documents'): ?>
    
    <!-- Grid View -->
    <div id="gridView" class="document-view <?= $currentView === 'grid' ? 'active' : 'hidden' ?>">
      <?php if (empty($docs)): ?>
        <div class="glass-card p-12 text-center">
          <div class="flex flex-col items-center gap-6">
            <div class="w-24 h-24 bg-gradient-to-br from-purple-500/20 to-blue-500/20 rounded-2xl flex items-center justify-center">
              <svg class="w-12 h-12 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-white mb-2">Keine Dokumente gefunden</h3>
              <p class="text-white/60">Laden Sie Ihr erstes Dokument hoch, um loszulegen</p>
            </div>
            <a href="/upload.php" class="liquid-glass-btn-primary px-6 py-3">
              Erstes Dokument hochladen
            </a>
          </div>
        </div>
      <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <?php foreach($docs as $doc): ?>
            <div class="document-card group">
              <div class="document-card-inner">
                <!-- Card Header -->
                <div class="p-6 pb-4">
                  <div class="flex items-start justify-between mb-4">
                    <div class="document-icon">
                      <?php 
                      $extension = strtolower(pathinfo($doc['filename'] ?? '', PATHINFO_EXTENSION));
                      $iconColor = 'text-blue-400';
                      $iconPath = 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
                      
                      switch($extension) {
                        case 'pdf':
                          $iconColor = 'text-red-400';
                          break;
                        case 'doc':
                        case 'docx':
                          $iconColor = 'text-blue-400';
                          break;
                        case 'jpg':
                        case 'jpeg':
                        case 'png':
                          $iconColor = 'text-green-400';
                          $iconPath = 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z';
                          break;
                      }
                      ?>
                      <svg class="w-8 h-8 <?= $iconColor ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $iconPath ?>"/>
                      </svg>
                    </div>
                    <div class="document-menu opacity-0 group-hover:opacity-100 transition-opacity">
                      <button class="text-white/60 hover:text-white p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                        </svg>
                      </button>
                    </div>
                  </div>
                  
                  <h3 class="document-title"><?= htmlspecialchars($doc['title'] ?? $doc['filename'] ?? 'Unbekannt') ?></h3>
                  <p class="document-subtitle"><?= htmlspecialchars($doc['category_name'] ?? 'Keine Kategorie') ?></p>
                </div>
                
                <!-- Card Content -->
                <div class="px-6 pb-4 flex-1">
                  <div class="space-y-3">
                    <div class="flex items-center text-sm text-white/60">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                      </svg>
                      <?= date('d.m.Y', strtotime($doc['upload_date'] ?? 'now')) ?>
                    </div>
                    
                    <?php if (isset($doc['file_size'])): ?>
                    <div class="flex items-center text-sm text-white/60">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                      </svg>
                      <?= number_format($doc['file_size'] / 1024, 1) ?> KB
                    </div>
                    <?php endif; ?>
                  </div>
                </div>
                
                <!-- Card Actions -->
                <div class="p-6 pt-0">
                  <div class="flex gap-2">
                    <a href="/uploads/<?= urlencode($doc['filename'] ?? '') ?>" 
                       download 
                       class="flex-1 liquid-glass-btn-secondary text-center py-2 text-sm">
                      Download
                    </a>
                    <button onclick="deleteDocument(<?= $doc['id'] ?>)" 
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
      <?php endif; ?>
    </div>
    
    <!-- List View -->
    <div id="listView" class="document-view <?= $currentView === 'list' ? 'active' : 'hidden' ?>">
      <div class="glass-card overflow-hidden">
        <div class="liquid-glass-table">
          <table class="w-full">
            <thead>
              <tr class="border-b border-white/10">
                <th class="text-left py-4 px-6 font-medium text-white/80">Dokument</th>
                <th class="text-left py-4 px-6 font-medium text-white/80">Typ</th>
                <th class="text-left py-4 px-6 font-medium text-white/80">Kategorie</th>
                <th class="text-left py-4 px-6 font-medium text-white/80">Datum</th>
                <th class="text-left py-4 px-6 font-medium text-white/80">Größe</th>
                <th class="text-right py-4 px-6 font-medium text-white/80">Aktionen</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($docs)): ?>
                <tr>
                  <td colspan="6" class="py-12 text-center">
                    <div class="flex flex-col items-center gap-4">
                      <div class="w-16 h-16 bg-gradient-to-br from-purple-500/20 to-blue-500/20 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                      </div>
                      <div>
                        <h3 class="text-lg font-semibold text-white mb-1">Keine Dokumente gefunden</h3>
                        <p class="text-white/60 text-sm">Laden Sie Ihr erstes Dokument hoch</p>
                      </div>
                    </div>
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach($docs as $doc): ?>
                  <tr class="document-row group border-b border-white/5 hover:bg-white/5 transition-all duration-300">
                    <td class="py-4 px-6">
                      <div class="flex items-center gap-4">
                        <div class="document-icon-small">
                          <?php 
                          $extension = strtolower(pathinfo($doc['filename'] ?? '', PATHINFO_EXTENSION));
                          $iconColor = 'text-blue-400';
                          switch($extension) {
                            case 'pdf': $iconColor = 'text-red-400'; break;
                            case 'doc':
                            case 'docx': $iconColor = 'text-blue-400'; break;
                            case 'jpg':
                            case 'jpeg':
                            case 'png': $iconColor = 'text-green-400'; break;
                          }
                          ?>
                          <svg class="w-6 h-6 <?= $iconColor ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                          </svg>
                        </div>
                        <div>
                          <div class="font-medium text-white"><?= htmlspecialchars($doc['title'] ?? $doc['filename'] ?? 'Unbekannt') ?></div>
                          <div class="text-sm text-white/60"><?= htmlspecialchars($doc['filename'] ?? '') ?></div>
                        </div>
                      </div>
                    </td>
                    <td class="py-4 px-6 text-white/70 text-sm"><?= strtoupper($extension ?? 'N/A') ?></td>
                    <td class="py-4 px-6 text-white/70 text-sm"><?= htmlspecialchars($doc['category_name'] ?? 'Keine Kategorie') ?></td>
                    <td class="py-4 px-6 text-white/70 text-sm"><?= date('d.m.Y', strtotime($doc['upload_date'] ?? 'now')) ?></td>
                    <td class="py-4 px-6 text-white/70 text-sm">
                      <?= isset($doc['file_size']) ? number_format($doc['file_size'] / 1024, 1) . ' KB' : 'N/A' ?>
                    </td>
                    <td class="py-4 px-6 text-right">
                      <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="/uploads/<?= urlencode($doc['filename'] ?? '') ?>" 
                           download 
                           class="liquid-glass-btn-secondary px-3 py-1 text-sm">
                          Download
                        </a>
                        <button onclick="deleteDocument(<?= $doc['id'] ?>)" 
                                class="liquid-glass-btn-danger px-3 py-1 text-sm">
                          Löschen
                        </button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>  <?php else: ?>
    <?php
    $subtabFile = __DIR__ . "/documents/{$currentDocSubtab}.php";
    if (file_exists($subtabFile)) {
      include $subtabFile;
    } else {
      echo '<div class="glass-card p-8 text-center">
              <h3 class="text-lg text-white mb-2">Bereich nicht verfügbar</h3>
              <p class="text-white/60">Dieser Bereich ist noch nicht implementiert.</p>
            </div>';
    }
    ?>
  <?php endif; ?>
</div>

<!-- Enhanced Styles for Liquid Glass Documents -->
<style>
/* Enhanced Liquid Glass Effects */
.liquid-glass-btn-primary {
  background: linear-gradient(135deg, rgba(147, 51, 234, 0.3), rgba(79, 70, 229, 0.3));
  backdrop-filter: blur(20px);
  border: 1px solid rgba(147, 51, 234, 0.4);
  color: white;
  border-radius: 1rem;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.liquid-glass-btn-primary::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s ease;
}

.liquid-glass-btn-primary:hover::before {
  left: 100%;
}

.liquid-glass-btn-primary:hover {
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

.liquid-glass-tab {
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: rgba(255, 255, 255, 0.7);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.liquid-glass-tab:hover {
  background: rgba(255, 255, 255, 0.1);
  border-color: rgba(255, 255, 255, 0.2);
  color: white;
  transform: translateY(-1px);
}

.liquid-glass-tab.active {
  background: linear-gradient(135deg, rgba(147, 51, 234, 0.2), rgba(79, 70, 229, 0.2));
  border-color: rgba(147, 51, 234, 0.4);
  color: white;
  box-shadow: 0 4px 20px rgba(147, 51, 234, 0.2);
}

/* Document Cards */
.document-card {
  background: rgba(255, 255, 255, 0.06);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 1.5rem;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  overflow: hidden;
  position: relative;
}

.document-card::before {
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

.document-card:hover::before {
  opacity: 1;
}

.document-card:hover {
  background: rgba(255, 255, 255, 0.08);
  border-color: rgba(255, 255, 255, 0.2);
  transform: translateY(-4px) scale(1.02);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
}

.document-card-inner {
  display: flex;
  flex-direction: column;
  height: 100%;
  min-height: 240px;
}

.document-icon {
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

.document-card:hover .document-icon {
  background: rgba(255, 255, 255, 0.15);
  transform: scale(1.1);
}

.document-title {
  font-size: 1rem;
  font-weight: 600;
  color: white;
  margin-bottom: 0.25rem;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.document-subtitle {
  font-size: 0.875rem;
  color: rgba(255, 255, 255, 0.6);
  margin-bottom: 1rem;
}

/* List View Styles */
.liquid-glass-table {
  background: transparent;
}

.document-row:hover {
  background: rgba(255, 255, 255, 0.05) !important;
}

.document-icon-small {
  width: 40px;
  height: 40px;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border-radius: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

/* View Toggle Animation */
.document-view {
  transition: all 0.3s ease;
}

.document-view.hidden {
  opacity: 0;
  transform: translateY(10px);
  pointer-events: none;
}

.document-view.active {
  opacity: 1;
  transform: translateY(0);
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .document-card-inner {
    min-height: 200px;
  }
  
  .liquid-glass-input {
    width: 100% !important;
  }
  
  .view-toggle-btn {
    padding: 0.5rem;
  }
}

/* Loading and animations */
.document-card {
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

.document-card:nth-child(even) {
  animation-delay: 0.1s;
}

.document-card:nth-child(3n) {
  animation-delay: 0.2s;
}
</style>

<script>
// Enhanced Document Management JavaScript
function switchDocumentView(view) {
  const gridView = document.getElementById('gridView');
  const listView = document.getElementById('listView');
  const toggleBtns = document.querySelectorAll('.view-toggle-btn');
  
  // Update URL without page reload
  const url = new URL(window.location);
  url.searchParams.set('view', view);
  window.history.pushState({}, '', url);
  
  // Update button states
  toggleBtns.forEach(btn => {
    btn.classList.remove('bg-white/20', 'text-white', 'shadow-lg');
    btn.classList.add('text-white/60');
  });
  
  const activeBtn = event?.target || document.querySelector(`[onclick="switchDocumentView('${view}')"]`);
  if (activeBtn) {
    activeBtn.classList.remove('text-white/60');
    activeBtn.classList.add('bg-white/20', 'text-white', 'shadow-lg');
  }
  
  // Switch views with animation
  if (view === 'grid') {
    listView.classList.remove('active');
    listView.classList.add('hidden');
    setTimeout(() => {
      gridView.classList.remove('hidden');
      gridView.classList.add('active');
    }, 150);
  } else {
    gridView.classList.remove('active');
    gridView.classList.add('hidden');
    setTimeout(() => {
      listView.classList.remove('hidden');
      listView.classList.add('active');
    }, 150);
  }
}

// Enhanced search functionality with real-time filtering
let searchTimeout;
document.getElementById('documentSearch').addEventListener('input', function(e) {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    const searchTerm = e.target.value.toLowerCase();
    
    // Filter grid view cards
    const gridCards = document.querySelectorAll('#gridView .document-card');
    gridCards.forEach(card => {
      const title = card.querySelector('.document-title')?.textContent.toLowerCase() || '';
      const subtitle = card.querySelector('.document-subtitle')?.textContent.toLowerCase() || '';
      const matches = title.includes(searchTerm) || subtitle.includes(searchTerm);
      
      if (matches) {
        card.style.display = 'block';
        card.style.animation = 'fadeInUp 0.3s ease-out';
      } else {
        card.style.display = 'none';
      }
    });
    
    // Filter list view rows
    const listRows = document.querySelectorAll('#listView .document-row');
    listRows.forEach(row => {
      const title = row.cells[0]?.textContent.toLowerCase() || '';
      const type = row.cells[1]?.textContent.toLowerCase() || '';
      const category = row.cells[2]?.textContent.toLowerCase() || '';
      
      const matches = title.includes(searchTerm) || 
                     type.includes(searchTerm) || 
                     category.includes(searchTerm);
      
      row.style.display = matches ? '' : 'none';
    });
  }, 300);
});

// Delete document with enhanced confirmation
function deleteDocument(documentId) {
  // Create custom modal instead of basic confirm
  const modal = document.createElement('div');
  modal.className = 'fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50';
  modal.innerHTML = `
    <div class="glass-card p-8 max-w-md mx-4">
      <h3 class="text-xl font-semibold text-white mb-4">Dokument löschen</h3>
      <p class="text-white/70 mb-6">Sind Sie sicher, dass Sie dieses Dokument permanent löschen möchten? Diese Aktion kann nicht rückgängig gemacht werden.</p>
      <div class="flex gap-4">
        <button onclick="this.closest('.fixed').remove()" class="liquid-glass-btn-secondary px-6 py-2 flex-1">
          Abbrechen
        </button>
        <button onclick="confirmDelete(${documentId})" class="liquid-glass-btn-danger px-6 py-2 flex-1">
          Löschen
        </button>
      </div>
    </div>
  `;
  
  document.body.appendChild(modal);
  
  // Close on backdrop click
  modal.addEventListener('click', (e) => {
    if (e.target === modal) {
      modal.remove();
    }
  });
}

function confirmDelete(documentId) {
  // Remove modal
  document.querySelector('.fixed.inset-0').remove();
  
  // Get current subtab and view to maintain state after deletion
  const urlParams = new URLSearchParams(window.location.search);
  const currentSubtab = urlParams.get('subtab') || 'documents';
  const currentView = urlParams.get('view') || 'grid';
  
  // Redirect to delete URL with current state
  window.location.href = `?tab=documents&subtab=${currentSubtab}&view=${currentView}&delete=${documentId}`;
}

// Initialize view based on URL parameter
document.addEventListener('DOMContentLoaded', function() {
  const urlParams = new URLSearchParams(window.location.search);
  const view = urlParams.get('view') || 'grid';
  switchDocumentView(view);
  
  // Add stagger animation to cards
  const cards = document.querySelectorAll('.document-card');
  cards.forEach((card, index) => {
    card.style.animationDelay = `${index * 0.1}s`;
  });
});

// Make functions globally available for all document views
window.switchDocumentView = switchDocumentView;
window.deleteDocument = deleteDocument;
window.confirmDelete = confirmDelete;
</script>
