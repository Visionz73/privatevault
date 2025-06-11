<?php
// public/task_detail_modal.php

require_once __DIR__ . '/../src/lib/auth.php';
requireLogin();

require_once __DIR__ . '/../src/lib/db.php';

// ID prüfen
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    http_response_code(400);
    echo '<p>Ungültige Anfrage</p>';
    exit;
}

// Task & Creator laden - add proper permission check
$stmt = $pdo->prepare('
  SELECT t.*, u.username AS creator
    FROM tasks t
    JOIN users u ON u.id = t.created_by
   WHERE t.id = ? AND (t.assigned_to = ? OR t.created_by = ? OR t.assigned_group_id IN (
       SELECT group_id FROM user_group_members WHERE user_id = ?
   ))
');
$stmt->execute([$id, $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$task) {
    http_response_code(404);
    echo '<p>Aufgabe nicht gefunden</p>';
    exit;
}

// alle User für das Dropdown
$users = $pdo->query('SELECT id, username FROM users ORDER BY username')->fetchAll(PDO::FETCH_ASSOC);

?>
<div>
  <h2 class="text-xl font-bold mb-4"><?= htmlspecialchars($task['title']) ?></h2>
  <p class="mb-4"><?= nl2br(htmlspecialchars($task['description'])) ?></p>
  <p class="text-sm text-text-secondary mb-6">
    Erstellt von: <strong><?= htmlspecialchars($task['creator']) ?></strong><br>
    Erstellt am: <?= date('d.m.Y H:i', strtotime($task['created_at'])) ?>
    <?php if (!empty($task['due_date'])): ?>
      <br>Fällig am: <?= date('d.m.Y', strtotime($task['due_date'])) ?>
    <?php endif; ?>
  </p>

  <form id="assignForm">
    <label class="block text-sm font-medium text-text mb-2">Zugewiesen an:</label>
    <select name="assigned_to" class="w-full px-4 py-2 border rounded-lg mb-4">
      <?php foreach ($users as $u): ?>
        <option value="<?= $u['id'] ?>" <?= $u['id'] == $task['assigned_to'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($u['username']) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <input type="hidden" name="id" value="<?= $task['id'] ?>">
    <div class="flex justify-end space-x-2">
      <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-lg border">Abbrechen</button>
      <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Speichern</button>
    </div>
  </form>

  <script>
    function closeModal() {
      document.getElementById('detailModal').classList.add('hidden');
    }
    document.getElementById('assignForm').addEventListener('submit', e => {
      e.preventDefault();
      const fd = new FormData(e.target);
      fetch('src/api/task_assign_update.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
          if (res.success) location.reload();
          else alert(res.error || 'Fehler');
        });
    });
  </script>
