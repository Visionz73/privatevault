<?php
// Get documents for the current user
$stmt = $pdo->prepare("
    SELECT id, title, upload_date, file_size
    FROM documents 
    WHERE user_id = ? 
    ORDER BY upload_date DESC 
    LIMIT 5
");
$stmt->execute([$user['id']]);
$docs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total documents
$stmtCount = $pdo->prepare("
    SELECT COUNT(*) as count
    FROM documents 
    WHERE user_id = ?
");
$stmtCount->execute([$user['id']]);
$docCount = $stmtCount->fetch(PDO::FETCH_ASSOC)['count'];
?>

<article class="widget-card p-6 flex flex-col">
  <div class="flex justify-between items-center mb-4">
    <a href="profile.php?tab=documents" class="group inline-flex items-center widget-header">
      <h2 class="mr-1">Dokumente</h2>
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
      </svg>
    </a>
    
    <button onclick="openDocumentUpload()" class="widget-button">
      +
    </button>
  </div>
  <p class="widget-description mb-4"><?= $docCount ?> Dateien</p>

  <div class="widget-scroll-container flex-1">
    <div class="widget-scroll-content space-y-2">
      <?php if(!empty($docs)): ?>
        <?php foreach($docs as $d): ?>
          <div class="widget-list-item" onclick="window.location.href='profile.php?tab=documents&doc=<?= $d['id'] ?>'">
            <div class="flex justify-between items-center mb-1">
              <span class="task-title truncate"><?= htmlspecialchars($d['title'] ?? 'Unbenanntes Dokument') ?></span>
              <?php if (!empty($d['file_size'])): ?>
                <span class="task-meta text-xs">
                  <?= formatFileSize($d['file_size']) ?>
                </span>
              <?php endif; ?>
            </div>
            <div class="task-meta text-xs">
              <?= date('d.m.Y', strtotime($d['upload_date'])) ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="widget-list-item text-center task-meta py-4">Keine Dokumente vorhanden.</div>
      <?php endif; ?>
    </div>
  </div>
</article>

<?php
// Helper function for file size formatting
if (!function_exists('formatFileSize')) {
    function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
?>
