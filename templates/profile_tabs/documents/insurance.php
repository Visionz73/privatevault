<?php
// templates/profile_tabs/documents/insurance.php
// Versicherungen mit modernem liquid glass design
if (!isset($docs) || !isset($_SESSION['user_id'])) {
    // Falls nicht von der Hauptseite geladen - eigene Datenbank-Abfrage
    require_once __DIR__ . '/../../../src/lib/db.php';
    require_once __DIR__ . '/../../../src/lib/auth.php';
    requireLogin();
    
    $userId = $_SESSION['user_id'];
    $stmt = $pdo->prepare("
        SELECT d.*, dc.name as category_name 
        FROM documents d
        LEFT JOIN document_categories dc ON d.category_id = dc.id
        WHERE d.user_id = ? AND (dc.name = 'Versicherungen' OR dc.name LIKE '%versicherung%') AND d.is_deleted = 0
        ORDER BY d.upload_date DESC
    ");
    $stmt->execute([$userId]);
    $docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$currentView = $_GET['view'] ?? 'grid';
$insuranceDocs = array_filter($docs, function($doc) {
    return stripos($doc['category_name'] ?? '', 'versicherung') !== false || 
           ($doc['category_name'] ?? '') === 'Versicherungen';
});
?>

<div class="space-y-6">
  <!-- Header mit Liquid Glass Design -->
  <div class="glass-card p-8">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h2 class="text-3xl font-bold text-white mb-2 bg-gradient-to-r from-orange-400 to-red-400 bg-clip-text text-transparent">
          Meine Versicherungen
        </h2>
        <p class="text-white/70">Verwalten Sie Ihre Versicherungspolizzen</p>
      </div>
      <div class="text-right">
        <div class="text-sm text-white/60 mb-1">Versicherungen</div>
        <div class="text-2xl font-bold text-white"><?= count($insuranceDocs) ?></div>
      </div>
    </div>
    
    <!-- View Toggle -->
    <div class="flex bg-white/5 backdrop-blur-md border border-white/10 rounded-xl p-1 w-fit">
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

  <!-- Grid View -->
  <div id="gridView" class="document-view <?= $currentView === 'grid' ? 'active' : 'hidden' ?>">
    <?php if (empty($insuranceDocs)): ?>
      <div class="glass-card p-12 text-center">
        <div class="flex flex-col items-center gap-6">
          <div class="w-24 h-24 bg-gradient-to-br from-orange-500/20 to-red-500/20 rounded-2xl flex items-center justify-center">
            <svg class="w-12 h-12 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-xl font-semibold text-white mb-2">Keine Versicherungen gefunden</h3>
            <p class="text-white/60">Laden Sie Ihre ersten Versicherungspolizzen hoch</p>
          </div>
        </div>
      </div>
    <?php else: ?>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php foreach($insuranceDocs as $doc): ?>
          <div class="document-card group">
            <div class="document-card-inner">
              <!-- Card Header -->
              <div class="p-6 pb-4">
                <div class="flex items-start justify-between mb-4">
                  <div class="document-icon">
                    <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                  </div>
                </div>
                
                <h3 class="document-title"><?= htmlspecialchars($doc['title'] ?? $doc['filename'] ?? 'Unbekannt') ?></h3>
                <p class="document-subtitle">Versicherung</p>
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
                  
                  <?php if (isset($doc['end_date']) && $doc['end_date']): ?>
                  <div class="flex items-center text-sm text-orange-400">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Läuft ab: <?= date('d.m.Y', strtotime($doc['end_date'])) ?>
                  </div>
                  <?php endif; ?>
                </div>
              </div>
              
              <!-- Card Actions -->
              <div class="p-6 pt-0">
                <div class="flex gap-2">                  <a href="<?= getFileUrl($doc['filename'] ?? '') ?>" 
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
              <th class="text-left py-4 px-6 font-medium text-white/80">Versicherung</th>
              <th class="text-left py-4 px-6 font-medium text-white/80">Hochgeladen</th>
              <th class="text-left py-4 px-6 font-medium text-white/80">Läuft ab</th>
              <th class="text-right py-4 px-6 font-medium text-white/80">Aktionen</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($insuranceDocs)): ?>
              <tr>
                <td colspan="4" class="py-12 text-center">
                  <div class="flex flex-col items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-500/20 to-red-500/20 rounded-2xl flex items-center justify-center">
                      <svg class="w-8 h-8 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                      </svg>
                    </div>
                    <div>
                      <h3 class="text-lg font-semibold text-white mb-1">Keine Versicherungen gefunden</h3>
                      <p class="text-white/60 text-sm">Laden Sie Ihre ersten Versicherungspolizzen hoch</p>
                    </div>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach($insuranceDocs as $doc): ?>
                <tr class="document-row group border-b border-white/5 hover:bg-white/5 transition-all duration-300">
                  <td class="py-4 px-6">
                    <div class="flex items-center gap-4">
                      <div class="document-icon-small">
                        <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                      </div>
                      <div>
                        <div class="font-medium text-white"><?= htmlspecialchars($doc['title'] ?? $doc['filename'] ?? 'Unbekannt') ?></div>
                        <div class="text-sm text-white/60"><?= htmlspecialchars($doc['filename'] ?? '') ?></div>
                      </div>
                    </div>
                  </td>
                  <td class="py-4 px-6 text-white/70 text-sm"><?= date('d.m.Y', strtotime($doc['upload_date'] ?? 'now')) ?></td>
                  <td class="py-4 px-6 text-white/70 text-sm">
                    <?= isset($doc['end_date']) && $doc['end_date'] ? date('d.m.Y', strtotime($doc['end_date'])) : 'Unbefristet' ?>
                  </td>
                  <td class="py-4 px-6 text-right">
                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">                      <a href="<?= getFileUrl($doc['filename'] ?? '') ?>" 
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
  </div>
</div>
        <li class="py-3 flex justify-between items-center">
          <a href="download.php?id=<?= $d['id'] ?>"
             class="truncate text-[#4A90E2] hover:underline" target="_blank">
            <?= htmlspecialchars($d['title']) ?>
          </a>
          <?php if ($d['end_date']): ?>
            <span class="text-xs text-gray-400">
              <?= date('d.m.Y', strtotime($d['end_date'])) ?>
            </span>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>

  <?php else: ?>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
      <?php foreach ($docs as $d): ?>
        <a href="download.php?id=<?= $d['id'] ?>" target="_blank"
           class="group relative block w-full aspect-square rounded-lg overflow-hidden shadow">

          <?php if (isImg($d['filename'])): ?>            <div style="background-image:url('<?= getFileUrl($d['filename']) ?>')"
                 class="absolute inset-0 bg-cover bg-center"></div>
          <?php else: ?>
            <div class="absolute inset-0 bg-gray-300 flex items-center justify-center
                        text-gray-600 text-4xl font-bold">PDF</div>
          <?php endif; ?>

          <div class="absolute inset-0 bg-white/30 backdrop-blur-sm transition
                      group-hover:bg-white/10"></div>
          <div class="absolute bottom-0 left-0 right-0 bg-white/70 backdrop-blur p-2
                      text-[13px] text-gray-800 truncate">
            <?= htmlspecialchars($d['title']) ?>
          </div>
        </a>
      <?php endforeach; ?>
    </div>

  <?php endif; ?>
</div>
