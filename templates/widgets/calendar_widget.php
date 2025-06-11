<?php
// Get events for the current user
$stmt = $pdo->prepare("
    SELECT e.*, u.username AS creator
      FROM events e
      JOIN users u ON u.id = e.created_by
     WHERE e.assigned_to = ? OR e.created_by = ?
     ORDER BY e.event_date ASC
     LIMIT 5
");
$stmt->execute([$user['id'], $user['id']]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total events
$stmtCount = $pdo->prepare("
    SELECT COUNT(*) as count
      FROM events e
     WHERE e.assigned_to = ? OR e.created_by = ?
");
$stmtCount->execute([$user['id'], $user['id']]);
$eventCount = $stmtCount->fetch(PDO::FETCH_ASSOC)['count'];
?>

<article class="widget-card p-6 flex flex-col">
  <div class="flex items-center justify-between mb-4">
    <a href="calendar.php" class="group inline-flex items-center widget-header">
      <h2 class="mr-1">Meine Termine</h2>
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
      </svg>
    </a>
    <button id="showInlineEventForm" class="widget-button">
      +
    </button>
  </div>
  <p class="widget-description mb-4"><?= $eventCount ?> Termine</p>
  
  <!-- Inline event creation form (initially hidden) -->
  <div id="inlineEventFormContainer" class="mb-4 hidden">
    <form id="inlineEventForm" class="space-y-2 widget-form">
      <input type="text" name="title" placeholder="Event Titel" required>
      <input type="date" name="date" required>
      <button type="submit" class="w-full widget-button">
        Termin erstellen
      </button>
    </form>
  </div>
  
  <div class="widget-scroll-container flex-1">
    <div id="dashboardEventList" class="widget-scroll-content space-y-2">
      <?php if(!empty($events)): ?>
        <?php foreach($events as $evt): ?>
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
</article>
