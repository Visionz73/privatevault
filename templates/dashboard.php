<!-- templates/dashboard.php -->
<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Dashboard | Private Vault</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>body{font-family:'Inter',sans-serif}</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#eef7ff] via-[#f7fbff] to-[#f9fdf2] flex">

  <?php require_once __DIR__.'/navbar.php'; ?>

  <main class="ml-64 flex-1 p-8 space-y-10">

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

      <!-- Kalender Widget ----------------------------------------------->
      <article class="bg-white rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.06)] p-6">
        <?php require_once __DIR__.'/calendar_widget.php'; ?>
      </article>

      <!-- Placeholder Cards --------------------------------------------->
      <?php foreach(['Recruiting','Abwesenheit','Org-Chart','Events'] as $name): ?>
        <article class="bg-white rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.06)] p-6 flex items-center justify-center text-gray-400 text-sm">
          <?= $name ?>-Widget
        </article>
      <?php endforeach; ?>
      <!-- Neuer Kalender-Widget -->
      <article class="bg-white rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.06)] p-6 cursor-pointer col-span-2" onclick="window.location.href='calendar.php'">
        <div class="flex flex-col items-center h-full">
          <h2 class="text-xl font-semibold mb-4">Kalender</h2>
          <!-- Entferne fixe Höhe, damit der Kalender den verfügbaren Platz füllt -->
          <div id="miniCalendar" class="w-full h-full"></div>
        </div>
      </article>

    </div><!-- /grid -->
  </main>
</body>
</html>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.5/index.global.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('miniCalendar');
    if(calendarEl) {
      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: false,
        height: '100%',
        selectable: false,
        events: [] // Hier können optional Vorschau-Events geladen werden
      });
      calendar.render();
    }
  });
</script>
