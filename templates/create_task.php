<?php
// templates/create_task.php
?>
<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Task anlegen | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet"/>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>body{font-family:'Inter',sans-serif}</style>
</head>
<body class="min-h-screen bg-bg flex">
  <?php require_once __DIR__ . '/navbar.php'; ?>

  <main class="ml-64 flex-1 p-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Neue Aufgabe erstellen</h1>

    <?php if (!empty($success)): ?>
      <div class="p-4 bg-green-100 border border-green-300 text-green-800 rounded shadow mb-6">
        <?= htmlspecialchars($success) ?>
      </div>
    <?php elseif (!empty($errors)): ?>
      <div class="p-4 bg-red-100 border border-red-300 text-red-800 rounded shadow mb-6">
        <ul class="list-disc list-inside">
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" class="bg-card-bg p-6 rounded-xl shadow-card-lg space-y-6 max-w-xl">
      <div>
        <label class="block text-sm font-medium text-text-secondary mb-1">Titel *</label>
        <input name="title" required
               class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary"
               value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
      </div>

      <div>
        <label class="block text-sm font-medium text-text-secondary mb-1">Beschreibung</label>
        <textarea name="description" rows="3"
                  class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
      </div>

      <div>
        <label class="block text-sm font-medium text-text-secondary mb-1">Empfänger *</label>
        <select name="assigned_to" required
                class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary">
          <option value="" disabled selected>Bitte auswählen…</option>
          <?php foreach ($allUsers as $u): ?>
            <option value="<?= $u['id'] ?>" <?= (($_POST['assigned_to'] ?? '')==$u['id'])?'selected':'' ?> >
              <?= htmlspecialchars($u['username']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium text-text-secondary mb-1">Fällig am</label>
        <input type="date" name="due_date"
               class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary"
               value="<?= htmlspecialchars($_POST['due_date'] ?? '') ?>">
      </div>

      <button type="submit"
              class="px-6 py-2 bg-primary text-white rounded-lg shadow hover:bg-primary-dark transition">
        Aufgabe erstellen
      </button>
    </form>
  </main>
</body>
</html>
