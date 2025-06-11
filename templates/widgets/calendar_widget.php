<div class="p-6 flex flex-col h-full">
  <div class="flex items-center justify-between mb-4">
    <a href="calendar.php" class="inline-flex items-center widget-header">
      Meine Termine
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
      </svg>
    </a>
  </div>
  <p class="widget-description mb-4"><?= count($widgetData['events'] ?? []) ?> Termine</p>
  
  <div class="widget-scroll-container flex-1">
    <div class="widget-scroll-content space-y-2">
      <?php if(!empty($widgetData['events'])): ?>
        <?php foreach($widgetData['events'] as $evt): ?>
          <div class="widget-list-item flex justify-between items-center">
            <a href="calendar.php" class="truncate pr-2 flex-1 task-title"><?= htmlspecialchars($evt['title']) ?></a>
            <span class="task-meta text-xs"><?= date('d.m.Y', strtotime($evt['event_date'])) ?></span>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="widget-list-item text-center task-meta py-4">Keine Termine gefunden.</div>
      <?php endif; ?>
    </div>
  </div>
</div>
