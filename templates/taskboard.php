<?php
// templates/taskboard.php
require_once __DIR__ . '/../src/lib/auth.php';
requireLogin();
require_once __DIR__ . '/../src/lib/db.php';

// Status-Spalten und Labels
$statuses = [
  'todo'  => 'To&nbsp;Do',
  'doing' => 'In&nbsp;Bearbeitung',
  'done'  => 'Erledigt'
];

// Alle Tasks des Users laden
$stmt = $pdo->prepare('
  SELECT * 
    FROM tasks 
   WHERE created_by = ? 
ORDER BY created_at DESC
');
$stmt->execute([$_SESSION['user_id']]);
$all = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Nach Status gruppieren
$tasksByStatus = array_fill_keys(array_keys($statuses), []);
foreach ($all as $t) {
  $tasksByStatus[$t['status']][] = $t;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>TaskBoard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <style>body{font-family:Inter,sans-serif}</style>
</head>
<body class="min-h-screen flex bg-[#f5f7fa]">
  <?php require_once __DIR__ . '/navbar.php'; ?>
  <main class="ml-64 flex-1 p-8 overflow-x-auto">
    <h1 class="text-2xl font-bold mb-8">TaskBoard</h1>

    <div class="flex gap-6">
      <?php foreach ($statuses as $key => $label): ?>
        <section
          class="w-72 flex-shrink-0 bg-gray-100 rounded-xl p-4 space-y-4 overflow-y-auto border border-gray-200"
          data-status="<?= $key ?>"
          ondragover="event.preventDefault()" ondrop="dropTask(event,this)"
        >
          <div class="flex items-center justify-between text-sm font-semibold uppercase tracking-wider mb-2">
            <span><?= $label ?></span>
            <button onclick="openNewTask('<?= $key ?>')" class="text-primary text-lg leading-none">+</button>
          </div>

          <?php foreach ($tasksByStatus[$key] as $t): ?>
            <article
               class="bg-white rounded-lg shadow p-3 hover:shadow-md transition cursor-grab"
               draggable="true" ondragstart="dragTask(event)"
               data-id="<?= $t['id'] ?>" onclick="openTask(<?= $t['id'] ?>)"
            >
              <h3 class="font-semibold text-gray-800"><?= htmlspecialchars($t['title']) ?></h3>
              <p class="text-sm text-gray-500 mt-1"><?= nl2br(htmlspecialchars(substr($t['description'],0,80))) ?></p>
            </article>
          <?php endforeach; ?>
        </section>
      <?php endforeach; ?>
    </div>
  </main>

  <!-- Modal -->
  <div id="modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div id="modalContent" class="bg-white rounded-xl shadow-xl w-full max-w-md p-6"></div>
  </div>

  <script>
    let dragged = null;
    function dragTask(e) { dragged = e.target; }
    function dropTask(e, col) {
      if (!dragged) return;
      col.appendChild(dragged);
      updateStatus(dragged.dataset.id, col.dataset.status);
    }
    function updateStatus(id, status) {
      const fd = new FormData();
      fd.append('id', id);
      fd.append('status', status);
      fetch('/src/api/task_update.php', { method:'POST', body:fd });
    }
    function openTask(id) {
      fetch('/templates/task_modal.php?id='+id)
        .then(r=>r.text())
        .then(html => {
          document.getElementById('modalContent').innerHTML = html;
          document.getElementById('modal').classList.remove('hidden');
        });
    }
    function openNewTask(defaultStatus='todo') {
      fetch('/templates/task_modal.php?status='+defaultStatus)
        .then(r=>r.text())
        .then(html => {
          document.getElementById('modalContent').innerHTML = html;
          document.getElementById('modal').classList.remove('hidden');
        });
    }
    document.getElementById('modal').addEventListener('click', e => {
      if (e.target.id==='modal') e.target.classList.add('hidden');
    });
  </script>
</body>
</html>
