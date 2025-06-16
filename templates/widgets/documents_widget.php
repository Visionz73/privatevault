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
      <h2 class="mr-1 text-white/90">Dokumente</h2>
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
      </svg>
    </a>
    
    <div class="text-right">
      <div class="text-xs text-white/60 mb-1">Gesamt</div>
      <div class="text-lg font-bold text-white/90"><?= $docCount ?></div>
    </div>
  </div>
  
  <div class="widget-scroll-container flex-1">
    <div class="widget-scroll-content space-y-2">
      <?php if (!empty($recentDocuments)): ?>
        <?php foreach ($recentDocuments as $doc): ?>
          <div class="widget-list-item" onclick="window.location.href='profile.php?tab=documents'">
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-gradient-to-br from-blue-500/20 to-purple-500/20 border border-blue-400/30 rounded-lg flex items-center justify-center backdrop-blur-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="task-title text-sm truncate text-white/90"><?= htmlspecialchars($doc['filename']) ?></div>
                <div class="task-description text-xs text-white/60"><?= date('d.m.Y', strtotime($doc['upload_date'])) ?></div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="widget-list-item text-center task-meta py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
          </svg>
          <p class="text-white/60">Keine Dokumente hochgeladen.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</article>
