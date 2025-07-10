<?php
// Simple documents overview for profile
$userId = $_SESSION['user_id'];

// Get recent documents
$stmt = $pdo->prepare("
    SELECT d.*, dc.name as category_name
    FROM documents d 
    LEFT JOIN document_categories dc ON d.category_id = dc.id 
    WHERE d.user_id = ? AND d.is_deleted = 0
    ORDER BY d.upload_date DESC 
    LIMIT 10
");
$stmt->execute([$userId]);
$recentDocs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM documents WHERE user_id = ? AND is_deleted = 0");
$stmt->execute([$userId]);
$totalDocs = $stmt->fetchColumn();
?>

<div class="space-y-6">
  <!-- Header -->
  <div class="glass-card p-8">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-3xl font-bold text-white mb-2 bg-gradient-to-r from-purple-400 to-blue-400 bg-clip-text text-transparent">
          Dokumente Übersicht
        </h2>
        <p class="text-white/70">Schnellzugriff auf Ihre wichtigsten Dateien</p>
      </div>
      <div class="text-right">
        <div class="text-sm text-white/60 mb-1">Gesamt</div>
        <div class="text-2xl font-bold text-white"><?= $totalDocs ?></div>
      </div>
    </div>
    
    <div class="mt-6 flex gap-4">
      <a href="/file-explorer.php" class="liquid-glass-btn-primary px-6 py-3 font-medium inline-flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v0l7-3 7 3"/>
        </svg>
        Datei-Explorer öffnen
      </a>
      
      <a href="/upload.php" class="liquid-glass-btn-secondary px-6 py-3 font-medium inline-flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
        </svg>
        Datei hochladen
      </a>
    </div>
  </div>

  <!-- Recent Documents -->
  <?php if (empty($recentDocs)): ?>
    <div class="glass-card p-12 text-center">
      <div class="w-24 h-24 bg-gradient-to-br from-purple-500/20 to-blue-500/20 rounded-2xl flex items-center justify-center mx-auto mb-6">
        <svg class="w-12 h-12 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
      </div>
      <h3 class="text-xl font-semibold text-white mb-2">Keine Dokumente vorhanden</h3>
      <p class="text-white/60 mb-6">Laden Sie Ihr erstes Dokument hoch oder verwenden Sie den Datei-Explorer</p>
      <div class="flex gap-4 justify-center">
        <a href="/upload.php" class="liquid-glass-btn-primary px-6 py-3">
          Erste Datei hochladen
        </a>
        <a href="/file-explorer.php" class="liquid-glass-btn-secondary px-6 py-3">
          Datei-Explorer
        </a>
      </div>
    </div>
  <?php else: ?>
    <div class="glass-card p-6">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-semibold text-white">Zuletzt hochgeladene Dateien</h3>
        <a href="/file-explorer.php" class="text-purple-400 hover:text-purple-300 text-sm font-medium">
          Alle anzeigen →
        </a>
      </div>
      
      <div class="space-y-3">
        <?php foreach ($recentDocs as $doc): ?>
          <?php
          $ext = strtolower(pathinfo($doc['filename'], PATHINFO_EXTENSION));
          $iconColor = 'text-blue-400';
          $iconPath = 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
          
          switch($ext) {
            case 'pdf':
              $iconColor = 'text-red-400';
              break;
            case 'jpg':
            case 'jpeg':
            case 'png':
              $iconColor = 'text-green-400';
              $iconPath = 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z';
              break;
            case 'mp3':
            case 'wav':
              $iconColor = 'text-purple-400';
              $iconPath = 'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3';
              break;
          }
          ?>
          <div class="p-4 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 hover:border-white/20 transition-all group">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-white/10 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 <?= $iconColor ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $iconPath ?>"/>
                </svg>
              </div>
              
              <div class="flex-1 min-w-0">
                <h4 class="text-white font-medium truncate"><?= htmlspecialchars($doc['title'] ?? $doc['original_name'] ?? $doc['filename']) ?></h4>
                <div class="flex items-center gap-4 text-sm text-white/60 mt-1">
                  <span><?= htmlspecialchars($doc['category_name'] ?? 'Keine Kategorie') ?></span>
                  <span>•</span>
                  <span><?= date('d.m.Y', strtotime($doc['upload_date'])) ?></span>
                  <span>•</span>
                  <span class="uppercase"><?= $ext ?></span>
                </div>
              </div>
              
              <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <a href="/uploads/<?= urlencode($doc['filename']) ?>" 
                   download 
                   class="p-2 bg-white/10 hover:bg-white/20 rounded-lg text-white transition-colors"
                   title="Download">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      
      <?php if (count($recentDocs) >= 10): ?>
        <div class="mt-6 text-center">
          <a href="/file-explorer.php" class="liquid-glass-btn-secondary px-6 py-3 inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v0l7-3 7 3"/>
            </svg>
            Alle Dateien im Explorer anzeigen
          </a>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>
