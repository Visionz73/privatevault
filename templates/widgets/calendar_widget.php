<?php
require_once __DIR__.'/../../src/lib/auth.php';
requireLogin();
require_once __DIR__.'/../../src/lib/db.php';

// Fetch upcoming events for the current user
$stmt = $pdo->prepare("
    SELECT e.*, u.username AS creator_name
    FROM events e
    LEFT JOIN users u ON u.id = e.created_by
    WHERE (e.assigned_to = ? OR e.created_by = ?)
    AND e.event_date >= CURDATE()
    ORDER BY e.event_date ASC, e.start_time ASC
    LIMIT 5
");
$stmt->execute([$user['id'], $user['id']]);
$upcomingEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get today's events
$stmt = $pdo->prepare("
    SELECT COUNT(*) as total 
    FROM events 
    WHERE (assigned_to = ? OR created_by = ?) 
    AND DATE(event_date) = CURDATE()
");
$stmt->execute([$user['id'], $user['id']]);
$todayEvents = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// Get this week's events
$stmt = $pdo->prepare("
    SELECT COUNT(*) as total 
    FROM events 
    WHERE (assigned_to = ? OR created_by = ?) 
    AND WEEK(event_date) = WEEK(CURDATE())
    AND YEAR(event_date) = YEAR(CURDATE())
");
$stmt->execute([$user['id'], $user['id']]);
$weekEvents = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
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
      Termin
    </button>
  </div>
  
  <!-- Event Statistics -->
  <div class="grid grid-cols-2 gap-2 mb-4">
    <div class="bg-blue-500/10 border border-blue-400/20 rounded-lg p-2 text-center">
      <div class="text-xs text-blue-300">Heute</div>
      <div class="text-sm font-bold text-blue-400"><?= $todayEvents ?></div>
    </div>
    <div class="bg-purple-500/10 border border-purple-400/20 rounded-lg p-2 text-center">
      <div class="text-xs text-purple-300">Diese Woche</div>
      <div class="text-sm font-bold text-purple-400"><?= $weekEvents ?></div>
    </div>
  </div>

  <!-- Inline Event Creation Form -->
  <div id="inlineEventFormContainer" class="hidden mb-4">
    <form id="inlineEventForm" class="widget-form space-y-3">
      <input type="text" name="title" placeholder="Titel des Termins" required class="w-full text-sm">
      <div class="grid grid-cols-2 gap-2">
        <input type="date" name="event_date" required class="text-sm">
        <input type="time" name="start_time" class="text-sm">
      </div>
      <textarea name="description" placeholder="Beschreibung (optional)" rows="2" class="w-full text-sm"></textarea>
      <div class="flex gap-2">
        <button type="submit" class="widget-button text-xs flex-1">Erstellen</button>
        <button type="button" onclick="closeEventForm()" class="widget-button text-xs px-3">✕</button>
      </div>
    </form>
  </div>

  <div class="widget-scroll-container flex-1">
    <div id="dashboardEventList" class="widget-scroll-content space-y-2">
      <?php if (!empty($upcomingEvents)): ?>
        <?php foreach ($upcomingEvents as $event): ?>
          <div class="widget-list-item group" onclick="window.location.href='calendar.php?event=<?= $event['id'] ?>'">
            <div class="flex justify-between items-start">
              <div class="flex-1 min-w-0">
                <div class="task-title text-sm truncate">
                  <?= htmlspecialchars($event['title']) ?>
                </div>
                
                <?php if (!empty($event['description'])): ?>
                  <div class="task-description text-xs truncate mt-1">
                    <?= htmlspecialchars(mb_strimwidth($event['description'], 0, 40, "...")) ?>
                  </div>
                <?php endif; ?>
                
                <div class="task-meta text-xs mt-1">
                  <?php 
                  $eventDate = new DateTime($event['event_date']);
                  $today = new DateTime();
                  $tomorrow = new DateTime('+1 day');
                  
                  if ($eventDate->format('Y-m-d') === $today->format('Y-m-d')) {
                    echo 'Heute';
                  } elseif ($eventDate->format('Y-m-d') === $tomorrow->format('Y-m-d')) {
                    echo 'Morgen';
                  } else {
                    echo $eventDate->format('d.m.Y');
                  }
                  ?>
                </div>
              </div>
              
              <div class="flex-shrink-0 text-right">
                <div class="text-xs font-medium text-blue-400">
                  <?= date('d.m.', strtotime($event['event_date'])) ?>
                </div>
                
                <?php if (!empty($event['start_time'])): ?>
                  <div class="task-meta text-xs">
                    <?= date('H:i', strtotime($event['start_time'])) ?>
                  </div>
                <?php endif; ?>
                
                <!-- Quick delete button -->
                <button onclick="deleteEvent(event, <?= $event['id'] ?>)" 
                        class="opacity-0 group-hover:opacity-100 p-1 hover:bg-red-500/20 rounded transition-all mt-1">
                  <svg class="w-3 h-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
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
          <button onclick="openEventForm()" 
                  class="block mx-auto mt-2 text-blue-400 hover:text-blue-300 text-xs">
            Ersten Termin erstellen
          </button>
        </div>
      <?php endif; ?>
    </div>
  </div>
</article>

<script>
function openEventForm() {
  document.getElementById('inlineEventFormContainer').classList.remove('hidden');
  // Set today as default date
  document.querySelector('input[name="event_date"]').value = new Date().toISOString().split('T')[0];
}

function closeEventForm() {
  document.getElementById('inlineEventFormContainer').classList.add('hidden');
  document.getElementById('inlineEventForm').reset();
}

// Event form submission
document.getElementById('inlineEventForm')?.addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  
  fetch('create_event.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      location.reload(); // Refresh to show new event
    } else {
      alert('Fehler beim Erstellen des Termins: ' + (data.error || 'Unbekannter Fehler'));
    }
  })
  .catch(error => {
    alert('Fehler beim Erstellen des Termins');
  });
});

// Event deletion
function deleteEvent(event, eventId) {
  event.stopPropagation();
  
  if (!confirm('Termin wirklich löschen?')) return;
  
  fetch('delete_event.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `id=${eventId}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const eventElement = event.target.closest('.widget-list-item');
      eventElement.style.transition = 'all 0.3s ease';
      eventElement.style.opacity = '0';
      eventElement.style.transform = 'translateX(-100%)';
      setTimeout(() => eventElement.remove(), 300);
    } else {
      alert('Fehler beim Löschen des Termins');
    }
  })
  .catch(error => {
    alert('Fehler beim Löschen des Termins');
  });
}

// Setup event listeners
document.getElementById('showInlineEventForm')?.addEventListener('click', openEventForm);
</script>
