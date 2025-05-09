<?php
// templates/task_modal.php
require_once __DIR__ . '/../src/lib/auth.php';
requireLogin();
require_once __DIR__ . '/../src/lib/db.php';

$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;
$status = $_GET['status'] ?? 'todo';
$task   = ['title'=>'','description'=>'','status'=>$status];

if ($id) {
  $stmt = $pdo->prepare('
    SELECT * 
      FROM tasks 
     WHERE id=? AND created_by=?'
  );
  $stmt->execute([$id, $_SESSION['user_id']]);
  $task = $stmt->fetch(PDO::FETCH_ASSOC) ?: $task;
}

$statuses = ['todo'=>'To Do','doing'=>'In Bearbeitung','done'=>'Erledigt'];
?>
<form id="taskForm">
  <input type="hidden" name="id" value="<?= $id ?? '' ?>">
  <div class="mb-4">
    <label class="block text-sm font-medium text-gray-700">Titel</label>
    <input name="title" required
           value="<?= htmlspecialchars($task['title']) ?>"
           class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-blue-500"/>
  </div>
  <div class="mb-4">
    <label class="block text-sm font-medium text-gray-700">Beschreibung</label>
    <textarea name="description" rows="4"
              class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($task['description']) ?></textarea>
  </div>
  <div class="mb-4">
    <label class="block text-sm font-medium text-gray-700">Status</label>
    <select name="status" class="w-full px-4 py-2 border rounded">
      <?php foreach ($statuses as $k=>$l): ?>
        <option value="<?= $k ?>" <?= $k===($task['status']??'')?'selected':'' ?>>
          <?= $l ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="flex justify-end">
    <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
      <?= $id ? 'Speichern' : 'Anlegen' ?>
    </button>
  </div>
</form>

<script>
document.getElementById('taskForm').addEventListener('submit', e => {
  e.preventDefault();
  const fd = new FormData(e.target);
  fetch('/src/api/task_save.php', { method:'POST', body:fd })
    .then(r=>r.json())
    .then(res => {
      if (res.success) location.reload();
      else alert(res.error||'Fehler');
    });
});
</script>
