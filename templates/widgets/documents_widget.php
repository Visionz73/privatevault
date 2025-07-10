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
          <div class="widget-list-item p-3 bg-white/5 border border-white/10 rounded-lg transition-all duration-300 hover:bg-white/10 hover:border-white/20 hover:transform hover:translateX-1 cursor-pointer" onclick="window.location.href='/file-explorer.php'">
            <div class="flex items-center space-x-3">
              <div class="icon-gradient-green p-2 rounded-lg">
                <i class="fas fa-file text-white text-sm"></i>
              </div>
              <div class="flex-1 min-w-0">
                <h4 class="text-white font-medium text-sm truncate"><?= htmlspecialchars($doc['title'] ?? $doc['filename']) ?></h4>
                <p class="text-white/60 text-xs"><?= date('d.m.Y', strtotime($doc['upload_date'])) ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="text-center py-6">
          <i class="fas fa-folder-open text-white/30 text-2xl mb-2"></i>
          <p class="text-white/60 text-sm">Keine Dokumente hochgeladen</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</article>
