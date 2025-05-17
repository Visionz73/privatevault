<?php
session_start();
require_once __DIR__ . '/../src/lib/db.php';
require_once __DIR__ . '/../src/lib/auth.php';
requireLogin();
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT id, title, event_date FROM events WHERE user_id = ? ORDER BY event_date ASC");
$stmt->execute([$userId]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Kalender | Private Vault</title>
  <!-- TailwindCSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: 'Inter', sans-serif; }
  </style>
</head>
<body class="p-8 bg-gradient-to-br from-[#eef7ff] via-[#f7fbff] to-[#f9fdf2]">
  <header class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Kalender</h1>
  </header>
  
  <!-- Terminliste Widget -->
  <section class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow mb-8">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-2xl font-semibold">Meine Termine</h2>
      <button id="showFormBtn" class="bg-blue-500 text-white px-3 py-1 rounded">Termin hinzufügen</button>
    </div>
    <p class="text-sm text-gray-500 mb-4"><?= count($events) ?> Termine</p>
    <ul id="eventList" class="divide-y divide-gray-200">
      <?php if(!empty($events)): ?>
        <?php foreach($events as $evt): ?>
          <li class="py-2 flex justify-between items-center">
            <span class="font-medium"><?= htmlspecialchars($evt['title']) ?></span>
            <span class="text-sm text-gray-500"><?= date('d.m.Y', strtotime($evt['event_date'])) ?></span>
          </li>
        <?php endforeach; ?>
      <?php else: ?>
        <li class="py-2 text-gray-500 text-center">Keine Termine gefunden.</li>
      <?php endif; ?>
    </ul>
  </section>
  
  <!-- Event-Planungsformular (initial versteckt) -->
  <section id="eventFormContainer" class="max-w-md mx-auto bg-white p-6 rounded-lg shadow mb-8 hidden">
    <h2 class="text-xl font-semibold mb-4">Neues Event hinzufügen</h2>
    <form id="eventForm" class="space-y-4">
      <input type="text" name="title" placeholder="Event Titel" class="w-full border border-gray-300 rounded p-2" required>
      <input type="date" name="date" class="w-full border border-gray-300 rounded p-2" required>
      <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600">Hinzufügen</button>
    </form>
  </section>
  
  <script>
    // Toggle Formularanzeige
    document.getElementById('showFormBtn').addEventListener('click', function() {
      const formContainer = document.getElementById('eventFormContainer');
      formContainer.classList.toggle('hidden');
    });
    
    // Updates der Terminliste
    function updateEventList(newEvent) {
      const eventList = document.getElementById('eventList');
      // Entferne "Keine Termine gefunden." falls vorhanden
      const noEventsEl = document.getElementById('noEvents');
      if(noEventsEl) noEventsEl.remove();
      
      // Füge neues Event ans Ende der Liste hinzu
      const li = document.createElement('li');
      li.className = "py-2 flex justify-between items-center";
      li.innerHTML = `<span class="font-medium">${newEvent.title}</span>
                      <span class="text-sm text-gray-500">${new Date(newEvent.date).toLocaleDateString('de-DE')}</span>`;
      eventList.appendChild(li);
    }
    
    // Event-Formular-Handler per AJAX
    document.getElementById('eventForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const title = this.title.value.trim();
      const date = this.date.value;
      if(title && date){
        fetch('/src/controllers/create_event.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({ title: title, date: date })
        })
        .then(response => response.json())
        .then(data => {
          if(data.success){
            updateEventList(data.event);
            this.reset();
            // Formular ausblenden
            document.getElementById('eventFormContainer').classList.add('hidden');
          } else {
            alert('Fehler: ' + (data.error || 'Unbekannter Fehler'));
          }
        })
        .catch(() => alert('Fehler beim Erstellen des Termins.'));
      }
    });
  </script>
</body>
</html>
