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
  <div class="flex justify-between items-center mb-6">
    <a href="profile.php?tab=documents" class="group inline-flex items-center">
      <h2 class="mr-1 text-white/90 text-xl font-semibold">Dokumente</h2>
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
          <div class="widget-list-item p-3 bg-white/5 border border-white/10 rounded-lg transition-all duration-300 hover:bg-white/10 hover:border-white/20 hover:transform hover:translateX-1 cursor-pointer" onclick="window.location.href='profile.php?tab=documents'">
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-gradient-to-br from-blue-500/20 to-purple-500/20 border border-blue-400/30 rounded-lg flex items-center justify-center backdrop-blur-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="task-title text-sm truncate text-white/90"><?= htmlspecialchars($doc['filename']) ?></div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="text-center py-6">
          <div class="text-white/40 text-sm">Keine Dokumente</div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</article>
