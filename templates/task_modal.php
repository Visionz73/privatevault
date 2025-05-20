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
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
    <div>
      <label for="recurrence_type" class="block text-sm font-medium text-gray-700">Wiederholung</label>
      <select name="recurrence_type" id="recurrence_type" class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-blue-500">
        <option value="none" <?= ($task['recurrence_type'] ?? 'none') === 'none' ? 'selected' : '' ?>>Keine</option>
        <option value="daily" <?= ($task['recurrence_type'] ?? '') === 'daily' ? 'selected' : '' ?>>Täglich</option>
        <option value="weekly" <?= ($task['recurrence_type'] ?? '') === 'weekly' ? 'selected' : '' ?>>Wöchentlich</option>
        <option value="monthly" <?= ($task['recurrence_type'] ?? '') === 'monthly' ? 'selected' : '' ?>>Monatlich</option>
        <option value="yearly" <?= ($task['recurrence_type'] ?? '') === 'yearly' ? 'selected' : '' ?>>Jährlich</option>
      </select>
    </div>
    <div>
      <label for="recurrence_interval" class="block text-sm font-medium text-gray-700">Intervall</label>
      <input type="number" name="recurrence_interval" id="recurrence_interval" value="<?= htmlspecialchars($task['recurrence_interval'] ?? '') ?>" class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-blue-500" min="1">
    </div>
    <div>
      <label for="recurrence_end_date" class="block text-sm font-medium text-gray-700">Endet am</label>
      <input type="date" name="recurrence_end_date" id="recurrence_end_date" value="<?= htmlspecialchars($task['recurrence_end_date'] ?? '') ?>" class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-blue-500">
    </div>
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
  // Ensure recurrence fields are included if the type is not 'none'
  const recurrenceType = fd.get('recurrence_type');
  if (recurrenceType === 'none') {
    fd.delete('recurrence_interval');
    fd.delete('recurrence_end_date');
  }
  fetch('/src/api/task_save.php', { method:'POST', body:fd })
    .then(r=>r.json())
    .then(res => {
      if (res.success) location.reload();
      else alert(res.error||'Fehler');
    });
});
</script>
