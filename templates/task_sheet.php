<?php
session_start();
require_once __DIR__ . '/../src/lib/auth.php';
requireLogin();
require_once __DIR__ . '/../src/lib/db.php';

$taskId = $_GET['id'] ?? null;
if (!$taskId) {
    echo "Aufgaben-ID fehlt.";
    exit;
}

// Fetch main task details (adjust table/column names as needed)
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
$stmt->execute([$taskId, $_SESSION['user_id']]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$task) {
    echo "Aufgabe nicht gefunden.";
    exit;
}

// Fetch sub‑tasks
$stmt = $pdo->prepare("SELECT * FROM sub_tasks WHERE task_id = ? ORDER BY created_at ASC");
$stmt->execute([$taskId]);
$subTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate progress (as percentage)
$totalSub = count($subTasks);
$completed = 0;
foreach ($subTasks as $sub) {
    if ($sub['is_completed']) {
        $completed++;
    }
}
$progressPercent = $totalSub > 0 ? round(($completed / $totalSub) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Aufgabenblatt | Private Vault</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/assets/css/apple-ui.css">
</head>
<body class="bg-gray-50 min-h-screen p-4">
  <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-md p-6">
    <!-- Task Header -->
    <div class="mb-6">
      <h1 class="text-3xl font-bold text-gray-900"><?= htmlspecialchars($task['title']) ?></h1>
      <p class="mt-2 text-gray-700"><?= nl2br(htmlspecialchars($task['description'])) ?></p>
    </div>

    <!-- Progress Bar -->
    <div class="mb-6">
      <div class="flex justify-between mb-1">
        <span class="text-sm font-medium text-blue-700">Fortschritt</span>
        <span class="text-sm font-medium text-blue-700"><?= $progressPercent ?>%</span>
      </div>
      <div class="w-full bg-gray-200 rounded-full h-4">
        <div class="bg-blue-600 h-4 rounded-full" style="width: <?= $progressPercent ?>%"></div>
      </div>
    </div>

    <!-- Sub-Task List -->
    <div class="mb-6">
      <h2 class="text-xl font-semibold text-gray-800 mb-4">Unteraufgaben</h2>
      <?php if (empty($subTasks)): ?>
        <p class="text-gray-600">Keine Unteraufgaben vorhanden.</p>
      <?php else: ?>
        <ul class="space-y-2">
          <?php foreach ($subTasks as $sub): ?>
            <li class="flex items-center">
              <input type="checkbox" class="mr-2" <?= $sub['is_completed'] ? 'checked' : '' ?> disabled>
              <span class="<?= $sub['is_completed'] ? 'line-through text-gray-500' : 'text-gray-800' ?>">
                <?= htmlspecialchars($sub['title']) ?>
              </span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>

    <!-- New Sub-Task Form -->
    <div>
      <h2 class="text-xl font-semibold text-gray-800 mb-4">Neue Unteraufgabe hinzufügen</h2>
      <form action="/add_subtask.php" method="post" class="flex space-x-4">
        <input type="hidden" name="task_id" value="<?= $taskId ?>">
        <input type="text" name="subtask_title" required placeholder="Titel der Unteraufgabe"
               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
          Hinzufügen
        </button>
      </form>
    </div>
  </div>
</body>
</html>
