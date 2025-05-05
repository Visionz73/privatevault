<!-- templates/admin.php -->
<!DOCTYPE html>
<html lang="de" class="h-full">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User-Gruppen verwalten | Private Vault</title>

  <!-- Inter Font -->
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap"
    rel="stylesheet"
  />

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    body { font-family: 'Inter', sans-serif; }
  </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-indigo-50 via-blue-50 to-purple-50 p-6 flex flex-col">

  <!-- Navbar -->
  <?php require_once __DIR__ . '/navbar.php'; ?>

  <main class="flex-grow max-w-5xl mx-auto space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">User-Gruppen verwalten</h1>

    <?php if ($success): ?>
      <div class="p-4 bg-green-100 border border-green-300 text-green-800 rounded shadow">
        <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>
    <?php if ($errors): ?>
      <div class="p-4 bg-red-100 border border-red-300 text-red-800 rounded shadow">
        <ul class="list-disc list-inside">
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">ID</th>
            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Benutzername</th>
            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">E-Mail</th>
            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Rolle</th>
            <th class="px-6 py-3"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php foreach ($users as $u): ?>
          <tr>
            <td class="px-6 py-4 text-sm text-gray-900"><?= $u['id'] ?></td>
            <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($u['username']) ?></td>
            <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($u['email']) ?></td>
            <td class="px-6 py-4">
              <form method="post" class="flex items-center space-x-2">
                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                <select name="role"
                        class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                  <?php foreach (['admin','member','guest'] as $role): ?>
                    <option value="<?= $role ?>" <?= $u['role'] === $role ? 'selected' : '' ?>>
                      <?= ucfirst($role) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <button type="submit"
                        class="px-3 py-2 bg-primary text-white rounded-lg shadow hover:bg-primary-dark transition">
                  Speichern
                </button>
              </form>
            </td>
            <td class="px-6 py-4 text-right text-sm text-gray-500">
              <?= date('d.m.Y', strtotime($u['created_at'])) ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>

  <!-- Footer -->


</body>
</html>
