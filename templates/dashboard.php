<!-- templates/dashboard.php -->
<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Dashboard | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: 'Inter', sans-serif; }
    /* Responsive adjustments for iPhone */
    @media (max-width: 414px) {
      main { margin-left: 0 !important; padding: 1rem; }
      nav#sidebar { width: 100%; position: relative; }
    }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#eef7ff] via-[#f7fbff] to-[#f9fdf2] flex flex-col">

  <?php require_once __DIR__.'/navbar.php'; ?>

  <!-- Use responsive margin: on small screens, remove left margin so content fills the screen -->
  <main class="ml-64 md:ml-0 flex-1 p-8 space-y-10">

    <!-- Greeting --------------------------------------------------------->
    <?php
    if (class_exists('IntlDateFormatter')) {
        $formatter = new IntlDateFormatter(
            'de_DE',
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE
        );
        $formattedDate = $formatter->format(new DateTime());
    } else {
        $formattedDate = date('l, d. F'); // Fallback using date()
    }
    ?>
    <h1 class="text-3xl font-bold text-gray-900 leading-tight">
      <?= $formattedDate ?><br>
      Guten <?= date('H')<12?'Morgen':(date('H')<18?'Tag':'Abend') ?>,
      <?= htmlspecialchars($user['first_name']??$user['username']) ?>
    </h1>

    <!-- Grid ------------------------------------------------------------->
    <div class="grid gap-8 auto-rows-min" style="grid-template-columns:repeat(auto-fill,minmax(340px,1fr));">

      <!-- Inbox Widget --------------------------------------------------->
      <article class="bg-white rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.06)] p-6 flex flex-col">
        <a href="inbox.php" class="group inline-flex items-center mb-4">
          <h2 class="text-lg font-semibold mr-1">Inbox</h2>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </a>
        <?php 
          // Debug-Ausgabe: Gesamter Aufgaben-Array aus dem Controller
          echo "<!-- Debug dashboard.php: count(\$tasks) = " . count($tasks) . " -->";
          
          // Falls du in Dashboard zusätzlich filtern möchtest, prüfe auch hier:
          $filteredTasks = $tasks; // (oder anderer Filter)
          echo "<!-- Debug dashboard.php (filtered): count(\$filteredTasks) = " . count($filteredTasks) . " -->";
        ?>
        <p class="text-sm text-gray-500 mb-4"><?= $openTaskCount ?> abschließende Elemente</p>

        <ul class="flex-1 overflow-y-auto text-sm divide-y divide-gray-100">
          <?php foreach($filteredTasks as $idx=>$t): ?>
            <li class="px-2 py-2 <?= $idx %2 ? 'bg-gray-50' : 'bg-white' ?> flex justify-between items-center">
              <span class="truncate pr-2">
                <?= htmlspecialchars($t['title']) ?>
              </span>
              <?php if(isset($t['due_date']) && $t['due_date']): $over = strtotime($t['due_date']) < time(); ?>
                <span class="<?= $over ? 'bg-red-100 text-red-600' : 'text-gray-400' ?> px-2 py-0.5 rounded-full text-xs whitespace-nowrap">
                  <?= $over ? 'Überfällig' : date('d.m.', strtotime($t['due_date'])) ?>
                </span>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
          <?php if(empty($filteredTasks)): ?>
            <li class="px-2 py-2 text-gray-500">Keine offenen Aufgaben.</li>
          <?php endif; ?>
        </ul>
      </article>

      <!-- Dokumente Widget ----------------------------------------------->
      <article class="bg-white rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.06)] p-6 flex flex-col">
        <a href="profile.php?tab=documents" class="group inline-flex items-center mb-4">
          <h2 class="text-lg font-semibold mr-1">Dokumente</h2>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <p class="text-sm text-gray-500 mb-4"><?= $docCount ?> Dateien</p>

        <ul class="flex-1 overflow-y-auto text-sm divide-y divide-gray-100">
          <?php foreach($docs as $idx=>$d): ?>
            <li class="px-2 py-2 <?= $idx %2 ? 'bg-gray-50' : 'bg-white' ?> truncate">
              <?= htmlspecialchars($d['title']) ?>
            </li>
          <?php endforeach; ?>
          <?php if(empty($docs)): ?>
            <li class="px-2 py-2 text-gray-500">Keine neuen Dokumente.</li>
          <?php endif; ?>
        </ul>
      </article>

      <!-- Meine Termine Widget -->
      <article class="bg-white/60 backdrop-blur-sm rounded-3xl shadow-md p-6 flex flex-col">
        <div class="flex items-center justify-between mb-4">
          <a href="calendar.php" class="flex items-center">
            <h2 class="text-lg font-semibold">Meine Termine</h2>
            <span class="ml-2 text-gray-500">&gt;</span>
          </a>
          <!-- Updated plus button: transparent, rounder, no blue -->
          <button id="showInlineEventForm" class="bg-white/30 text-gray-700 px-4 py-2 rounded-full border border-gray-200">
            +
          </button>
        </div>
        <!-- Inline Event Form (initially hidden) -->
        <div id="inlineEventFormContainer" class="mb-4 hidden">
          <form id="inlineEventForm" class="space-y-2">
            <input type="text" name="title" placeholder="Event Titel" class="w-full border border-gray-300 rounded p-2" required>
            <input type="date" name="date" class="w-full border border-gray-300 rounded p-2" required>
            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600">
              Termin erstellen
            </button>
          </form>
        </div>
        <p class="text-sm text-gray-500 mb-4"><?= count($events) ?> Termine</p>
        <ul id="dashboardEventList" class="flex-1 overflow-y-auto text-sm divide-y divide-gray-200">
          <?php if(!empty($events)): ?>
            <?php foreach($events as $evt): ?>
              <li class="px-2 py-2 flex justify-between items-center">
                <a href="calendar.php" class="truncate pr-2 flex-1"><?= htmlspecialchars($evt['title']) ?></a>
                <span class="text-gray-400 text-xs"><?= date('d.m.Y', strtotime($evt['event_date'])) ?></span>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li class="px-2 py-2 text-gray-500">Keine Termine gefunden.</li>
          <?php endif; ?>
        </ul>
      </article>

      <!-- Placeholder Cards --------------------------------------------->
      <?php foreach(['Recruiting','Abwesenheit','Org-Chart','Events'] as $name): ?>
        <article class="bg-white rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.06)] p-6 flex items-center justify-center text-gray-400 text-sm">
          <?= $name ?>-Widget
        </article>
      <?php endforeach; ?>
    </div><!-- /grid -->
  </main>
</body>
</html>

<script>
// Toggle inline event creation form
document.getElementById('showInlineEventForm').addEventListener('click', function() {
  document.getElementById('inlineEventFormContainer').classList.toggle('hidden');
});

// Handle inline event form submission via AJAX
document.getElementById('inlineEventForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const title = this.title.value.trim();
  const date = this.date.value;
  if(title && date){
    fetch('/create_event.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ title: title, date: date })
    })
    .then(response => response.json())
    .then(data => {
      if(data.success){
        // Create new list element for the event
        const newEvent = data.event;
        const li = document.createElement('li');
        li.className = "px-2 py-2 flex justify-between items-center";
        li.innerHTML = `<a href="calendar.php" class="truncate pr-2 flex-1">${newEvent.title}</a>
                         <span class="text-gray-400 text-xs">${new Date(newEvent.date).toLocaleDateString('de-DE')}</span>`;
        const eventList = document.getElementById('dashboardEventList');
        
        // If "Keine Termine gefunden." is present, remove it.
        if(eventList.childElementCount === 1 && eventList.firstElementChild.textContent.includes('Keine Termine')) {
          eventList.innerHTML = '';
        }
        eventList.appendChild(li);
        // Update count (force a reload or recalc count)
        // For simplicity, not auto-updating count here.
        this.reset();
        document.getElementById('inlineEventFormContainer').classList.add('hidden');
      } else {
        alert('Fehler: ' + (data.error || 'Unbekannter Fehler'));
      }
    })
    .catch(() => alert('Fehler beim Erstellen des Termins.'));
  }
});
</script>
