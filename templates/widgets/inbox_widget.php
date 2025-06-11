<div class="p-6 flex flex-col h-full">
  <div class="flex justify-between items-center mb-4">
    <a href="inbox.php" class="group inline-flex items-center widget-header">
      <h2 class="mr-1">Inbox</h2>
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
      </svg>
    </a>
  </div>
  
  <p class="widget-description mb-4"><?= $widgetData['inbox_count'] ?? 0 ?> abschließende Elemente</p>

  <div class="widget-scroll-container flex-1">
    <div class="widget-scroll-content space-y-2">
      <?php if (!empty($widgetData['inbox_tasks'])): ?>
        <?php foreach($widgetData['inbox_tasks'] as $t): ?>
          <div class="widget-list-item flex flex-col gap-2"
               onclick="window.location.href='task_detail.php?id=<?= $t['id'] ?>'">
            <div class="flex justify-between items-center">
              <span class="task-title truncate"><?= htmlspecialchars($t['title']) ?></span>
              <?php if(isset($t['due_date']) && $t['due_date']): $over = strtotime($t['due_date']) < time(); ?>
                <span class="<?= $over ? 'status-overdue' : 'status-due' ?> px-1 py-0.5 rounded-full text-xs whitespace-nowrap">
                  <?= $over ? 'Überfällig' : date('d.m.', strtotime($t['due_date'])) ?>
                </span>
              <?php endif; ?>
            </div>
            
            <?php if(!empty($t['description'])): ?>
              <p class="task-description line-clamp-1 text-xs"><?= htmlspecialchars(mb_strimwidth($t['description'], 0, 60, "...")) ?></p>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="widget-list-item text-center task-meta py-4">Keine offenen Aufgaben.</div>
      <?php endif; ?>
    </div>
  </div>
</div>
