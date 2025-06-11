<?php
// Lade alle User für die Zuweisung
require_once __DIR__ . '/../src/lib/db.php';
$users = $pdo->query("SELECT id, username FROM users ORDER BY username")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8"/>
  <title>Ereignis erstellen | Private Vault</title>
  <link href="https://cdn.tailwindcss.com" rel="stylesheet">
</head>
<body class="p-8">
  <h1 class="text-2xl mb-4">Neues Ereignis erstellen</h1>
  <?php if(isset($error)): ?>
    <p class="text-red-600"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>
  <form method="post" action="create_event.php" class="space-y-4">
    <div>
      <label class="block">Titel:</label>
      <input type="text" name="title" class="border p-2 w-full" required>
    </div>
    <div>
      <label class="block">Beschreibung:</label>
      <textarea name="description" class="border p-2 w-full"></textarea>
    </div>
    <div>
      <label class="block">Datum:</label>
      <input type="date" name="event_date" class="border p-2" required>
    </div>
    <div>
      <label class="block">Zugewiesen an:</label>
      <select name="assigned_to" class="border p-2">
        <option value="">-- Nicht zugewiesen --</option>
        <?php foreach($users as $user): ?>
          <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2">Ereignis erstellen</button>
  </form>
</body>
</html>
