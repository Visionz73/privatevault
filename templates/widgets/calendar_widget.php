<?php
require_once __DIR__.'/../../src/lib/auth.php';
requireLogin();
require_once __DIR__.'/../../src/lib/db.php';

// Fetch upcoming events starting today
$today = new DateTimeImmutable('today');
$stmt = $pdo->prepare(
    "SELECT e.*, u.username AS creator_name
     FROM events e
     LEFT JOIN users u ON u.id = e.created_by
     WHERE (e.assigned_to = ? OR e.created_by = ?)
       AND e.event_date >= ?
     ORDER BY e.event_date ASC, IFNULL(e.start_time, '') ASC
     LIMIT 10"
);
$stmt->execute([
    $user['id'],
    $user['id'],
    $today->format('Y-m-d')
]);
$upcomingEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group events by date
$eventsByDate = [];
foreach ($upcomingEvents as $event) {
    $date = $event['event_date'];
    if (!isset($eventsByDate[$date])) {
        $eventsByDate[$date] = [];
    }
    $eventsByDate[$date][] = $event;
}
$todayEvents = $eventsByDate[$today->format('Y-m-d')] ?? [];

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
  <div id="inlineEventFormContainer" class="hidden mb-4 p-3 bg-white/5 border border-white/10 rounded-lg backdrop-blur-sm">
    <form id="inlineEventForm" class="widget-form space-y-2">
      <div class="flex gap-2">
        <button type="submit" class="widget-button px-3 py-1 text-xs bg-green-600/30 border border-green-400/50 text-green-300 hover:bg-green-600/40 hover:border-green-400/60 rounded-lg backdrop-blur-sm transition-all duration-200">
        </button>
        <button type="button" onclick="document.getElementById('inlineEventFormContainer').classList.add('hidden')" 
                class="widget-button px-3 py-1 text-xs bg-white/10 border border-white/20 text-white/70 hover:bg-white/15 hover:border-white/30 rounded-lg backdrop-blur-sm transition-all duration-200">
          Abbrechen
        </button>
      </div>
    </form>
  </div>

  <div class="flex justify-between items-center mb-6">
    <h2 class="text-white/90 text-xl font-semibold">
      <?= date('l, d.m.') ?>
    </h2>
    <div class="text-right">
      <div class="text-xs text-white/60 mb-1">heute</div>
      <div class="text-lg font-bold text-white/90"><?= count($todayEvents) ?></div>
    </div>
  </div>

  <div class="widget-scroll-container flex-1">
    <div id="dashboardEventList" class="widget-scroll-content space-y-2">
      <?php if (!empty($upcomingEvents)): ?>
        <?php $currentDate = ''; ?>
        <?php foreach ($upcomingEvents as $event): ?>
          <?php if ($currentDate !== $event['event_date']): ?>
            <?php $currentDate = $event['event_date']; ?>
            <div class="text-white/60 text-xs mt-3">
              <?= date('D d.m', strtotime($currentDate)) ?>
            </div>
          <?php endif; ?>
          <div class="widget-list-item p-3 bg-white/5 border border-white/10 rounded-lg transition-all duration-300 hover:bg-white/10 hover:border-white/20 hover:transform hover:translateX-1 cursor-pointer" onclick="window.location.href='calendar.php'">
            <div class="flex justify-between items-center">
              <span class="truncate flex-1 text-white text-sm">
                <?= htmlspecialchars($event['title']) ?>
              </span>
              <?php if (!empty($event['start_time'])): ?>
                <span class="text-blue-400 text-xs ml-2">
                  <?= substr($event['start_time'],0,5) ?>
                </span>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="text-center py-6">
          <div class="text-white/40 text-sm">Keine Termine</div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</article>

<script>
// Toggle inline event creation form
document.getElementById('showInlineEventForm').addEventListener('click', function() {
  document.getElementById('inlineEventFormContainer').classList.toggle('hidden');
});

// Handle inline event form submission
document.getElementById('inlineEventForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const title = this.title.value.trim();
  const date = this.date.value;
  
  if(title && date){
    fetch('/src/api/create_event.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ title: title, date: date })
    })
    .then(response => response.json())
    .then(data => {
      if(data.success){
        // Reload page to show new event
        window.location.reload();
      } else {
        alert('Fehler: ' + (data.error || 'Unbekannter Fehler'));
      }
    })
    .catch(() => alert('Fehler beim Erstellen des Termins.'));
  }
});
</script>
