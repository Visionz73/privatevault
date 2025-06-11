<?php
// Fetch upcoming events for the current user
$stmt = $pdo->prepare("
    SELECT e.*, u.username AS creator_name
    FROM events e
    LEFT JOIN users u ON u.id = e.created_by
    WHERE (e.assigned_to = ? OR e.created_by = ?)
    AND e.event_date >= CURDATE()
    ORDER BY e.event_date ASC
    LIMIT 5
");
$stmt->execute([$user['id'], $user['id']]);
$upcomingEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total event count for current month
$stmt = $pdo->prepare("
    SELECT COUNT(*) as total 
    FROM events 
    WHERE (assigned_to = ? OR created_by = ?) 
    AND MONTH(event_date) = MONTH(CURDATE()) 
    AND YEAR(event_date) = YEAR(CURDATE())
");
$stmt->execute([$user['id'], $user['id']]);
$monthlyEvents = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
?>

<article class="widget-card p-6 flex flex-col">
  <div class="flex justify-between items-center mb-4">
    <a href="calendar.php" class="group inline-flex items-center widget-header">
      <h2 class="mr-1">Kalender</h2>
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
      </svg>
    </a>
    
    <button id="showInlineEventForm" class="widget-button text-sm flex items-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      Ereignis
    </button>
  </div>
  
  <p class="widget-description mb-4"><?= $monthlyEvents ?> Termine in diesem Monat</p>

  <!-- Inline Event Creation Form -->
  <div id="inlineEventFormContainer" class="hidden mb-4 p-3 bg-white/5 rounded-lg">
    <form id="inlineEventForm" class="widget-form space-y-2">
      <input type="text" name="title" placeholder="Ereignis-Titel..." required class="w-full px-3 py-2 text-sm">
      <input type="date" name="date" required class="w-full px-3 py-2 text-sm">
      <div class="flex gap-2">
        <button type="submit" class="widget-button px-3 py-1 text-xs bg-green-600/30 border-green-400/50">
          Erstellen
        </button>
        <button type="button" onclick="document.getElementById('inlineEventFormContainer').classList.add('hidden')" 
                class="widget-button px-3 py-1 text-xs">
          Abbrechen
        </button>
      </div>
    </form>
  </div>

  <div class="widget-scroll-container flex-1">
    <div id="dashboardEventList" class="widget-scroll-content space-y-2">
      <?php if (!empty($upcomingEvents)): ?>
        <?php foreach ($upcomingEvents as $event): ?>
          <div class="widget-list-item" onclick="window.location.href='calendar.php'">
            <div class="flex justify-between items-start">
              <div class="flex-1 min-w-0">
                <div class="task-title text-sm truncate">
                  <?= htmlspecialchars($event['title']) ?>
                </div>
                <?php if (!empty($event['description'])): ?>
                  <div class="task-description text-xs truncate">
                    <?= htmlspecialchars($event['description']) ?>
                  </div>
                <?php endif; ?>
                <div class="task-meta text-xs">
                  <span class="font-medium">Erstellt von:</span> 
                  <?= htmlspecialchars($event['creator_name'] ?? 'Unbekannt') ?>
                </div>
              </div>
              <div class="flex-shrink-0 text-right">
                <div class="text-xs font-medium text-blue-400">
                  <?= date('d.m.', strtotime($event['event_date'])) ?>
                </div>
                <?php if (date('Y-m-d', strtotime($event['event_date'])) === date('Y-m-d')): ?>
                  <span class="status-badge bg-green-100 text-green-800 px-1 py-0.5 rounded-full text-xs">
                    Heute
                  </span>
                <?php elseif (strtotime($event['event_date']) < strtotime('+7 days')): ?>
                  <span class="status-badge bg-orange-100 text-orange-800 px-1 py-0.5 rounded-full text-xs">
                    Diese Woche
                  </span>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="widget-list-item text-center task-meta py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          Keine Termine gefunden.
          <button id="createFirstEvent" onclick="document.getElementById('inlineEventFormContainer').classList.remove('hidden')" 
                  class="block mx-auto mt-2 text-blue-400 hover:text-blue-300 text-xs">
            Ersten Termin erstellen
          </button>
        </div>
      <?php endif; ?>
    </div>
  </div>
</article>
