<div class="p-6 flex flex-col h-full">
  <a href="profile.php?tab=documents" class="group inline-flex items-center mb-4 widget-header">
    <h2 class="mr-1">Dokumente</h2>
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
  </a>
  <p class="widget-description mb-4"><?= $widgetData['documents_count'] ?? 0 ?> Dateien</p>

  <div class="widget-scroll-container flex-1">
    <div class="widget-scroll-content space-y-2">
      <?php if(!empty($widgetData['documents'])): ?>
        <?php foreach($widgetData['documents'] as $d): ?>
          <div class="widget-list-item">
            <span class="truncate block task-title"><?= htmlspecialchars($d['title'] ?? '') ?></span>
            <div class="text-xs text-white/50 mt-1">
              <?= date('d.m.Y', strtotime($d['upload_date'])) ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="widget-list-item text-center task-meta py-4">Keine Dokumente vorhanden.</div>
      <?php endif; ?>
    </div>
  </div>
</div>
