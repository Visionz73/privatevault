<?php
// Vorausgesetzt, $user ist im Dashboard bereits verfügbar und $pdo aus DB-Verbindung
$stmt = $pdo->prepare("
    SELECT e.*, u.username AS creator
      FROM events e
      JOIN users u ON u.id = e.created_by
     WHERE e.assigned_to = ? OR e.created_by = ?
     ORDER BY e.event_date ASC
");
$stmt->execute([$user['id'], $user['id']]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="bg-white rounded-2xl shadow p-6">
  <h2 class="text-lg font-bold mb-4">Kalender</h2>
  <?php if(count($events) > 0): ?>
    <ul class="space-y-2">
      <?php foreach($events as $event): ?>
        <li class="border-b pb-2">
          <p class="font-semibold"><?= htmlspecialchars($event['title']) ?></p>
          <p class="text-sm text-gray-500"><?= date('d.m.Y', strtotime($event['event_date'])) ?></p>
          <?php if($event['description']): ?>
            <p class="text-sm"><?= htmlspecialchars($event['description']) ?></p>
          <?php endif; ?>
          <p class="text-xs text-gray-400">Erstellt von: <?= htmlspecialchars($event['creator']) ?></p>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p class="text-gray-500">Keine Ereignisse gefunden.</p>
  <?php endif; ?>
</div>
