<?php
require_once __DIR__.'/../../src/lib/auth.php';
requireLogin();
require_once __DIR__.'/../../src/lib/db.php';

// Fetch recent documents for the current user
$stmt = $pdo->prepare("
    SELECT id, filename, file_size, upload_date, category
    FROM documents 
    WHERE user_id = ? 
    ORDER BY upload_date DESC 
    LIMIT 5
");
$stmt->execute([$user['id']]);
$recentDocuments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total document count
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM documents WHERE user_id = ?");
$stmt->execute([$user['id']]);
$docCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
?>

<article class="widget-card p-6 flex flex-col">
  <div class="flex justify-between items-center mb-4">
    <a href="profile.php?tab=documents" class="group inline-flex items-center widget-header">
      <h2 class="mr-1">Dokumente</h2>
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
      </svg>
    </a>
    
    <button onclick="openDocumentUpload()" class="widget-button text-sm flex items-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      Upload
    </button>
  </div>
  
  <p class="widget-description mb-4"><?= $docCount ?> Dokumente gespeichert</p>

  <div class="widget-scroll-container flex-1">
    <div class="widget-scroll-content space-y-2">
      <?php if (!empty($recentDocuments)): ?>
        <?php foreach ($recentDocuments as $doc): ?>
          <div class="widget-list-item" onclick="window.open('download.php?id=<?= $doc['id'] ?>', '_blank')">
            <div class="flex justify-between items-start">
              <div class="flex-1 min-w-0">
                <div class="task-title text-sm truncate">
                  <?= htmlspecialchars($doc['filename']) ?>
                </div>
                <?php if (!empty($doc['category'])): ?>
                  <div class="task-description text-xs truncate">
                    Kategorie: <?= htmlspecialchars($doc['category']) ?>
                  </div>
                <?php endif; ?>
                <div class="task-meta text-xs">
                  <span><?= formatFileSize($doc['file_size']) ?></span>
                </div>
              </div>
              <div class="flex-shrink-0 text-right">
                <div class="text-xs font-medium text-blue-400">
                  <?= date('d.m.', strtotime($doc['upload_date'])) ?>
                </div>
                <span class="status-badge bg-blue-100 text-blue-800 px-1 py-0.5 rounded-full text-xs">
                  <?= strtoupper(pathinfo($doc['filename'], PATHINFO_EXTENSION)) ?>
                </span>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="widget-list-item text-center task-meta py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          Keine Dokumente gefunden.
          <button onclick="openDocumentUpload()" 
                  class="block mx-auto mt-2 text-blue-400 hover:text-blue-300 text-xs">
            Erstes Dokument hochladen
          </button>
        </div>
      <?php endif; ?>
    </div>
  </div>
</article>

<?php
// Helper function for file size formatting
if (!function_exists('formatFileSize')) {
    function formatFileSize($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
?>
