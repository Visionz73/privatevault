<!-- templates/admin.php -->
<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: 'Inter', sans-serif; }
    @media (max-width: 768px) {
      main { margin-top: 3.5rem; }
    }
  </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-[#eef7ff] via-[#f7fbff] to-[#f9fdf2] flex flex-col">
  <!-- Navbar -->
  <?php require_once __DIR__ . '/navbar.php'; ?>

  <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
    <div class="max-w-7xl mx-auto space-y-6">
      <!-- Page Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
        <button id="createUserBtn" class="px-4 py-2 bg-green-500 text-white rounded-lg text-sm font-medium hover:bg-green-600 transition">
          + Neuen Benutzer anlegen
        </button>
      </div>

      <!-- Notifications -->
      <?php if ($success): ?>
        <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
          <?= htmlspecialchars($success) ?>
        </div>
      <?php endif; ?>
      
      <?php if ($errors): ?>
        <div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
          <ul class="list-disc list-inside">
            <?php foreach ($errors as $e): ?>
              <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <!-- Stats Overview -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-sm p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
            <div class="ml-5">
              <p class="text-gray-500 text-sm">Benutzer</p>
              <h3 class="text-xl font-bold text-gray-900"><?= number_format($stats['users']) ?></h3>
            </div>
          </div>
        </div>
        
        <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-sm p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
            </div>
            <div class="ml-5">
              <p class="text-gray-500 text-sm">Aufgaben</p>
              <h3 class="text-xl font-bold text-gray-900"><?= number_format($stats['tasks']) ?></h3>
            </div>
          </div>
        </div>
        
        <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-sm p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
              </svg>
            </div>
            <div class="ml-5">
              <p class="text-gray-500 text-sm">Dokumente</p>
              <h3 class="text-xl font-bold text-gray-900"><?= number_format($stats['documents']) ?></h3>
            </div>
          </div>
        </div>
      </div>

      <!-- User Management -->
      <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
          <h2 class="text-lg font-semibold text-gray-900">Benutzerverwaltung</h2>
        </div>
        
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Benutzer</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">E-Mail</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rolle</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statistik</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktionen</th>
              </tr>
            </thead>
            <tbody class="bg-white/40 divide-y divide-gray-200">
              <?php foreach ($users as $u): ?>
                <tr>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $u['id'] ?></td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10 rounded-full bg-[#4A90E2]/10 flex items-center justify-center text-[#4A90E2] font-semibold">
                        <?= strtoupper(substr($u['username'], 0, 2)) ?>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($u['username']) ?></div>
                        <div class="text-sm text-gray-500">Seit <?= date('d.m.Y', strtotime($u['created_at'])) ?></div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($u['email']) ?></td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <form method="post" class="flex items-center space-x-2">
                      <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                      <input type="hidden" name="action" value="update_role">
                      <select name="role" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <?php foreach (['admin','member','guest'] as $role): ?>
                          <option value="<?= $role ?>" <?= $u['role'] === $role ? 'selected' : '' ?>><?= ucfirst($role) ?></option>
                        <?php endforeach; ?>
                      </select>
                      <button type="submit" class="text-xs px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                        Ändern
                      </button>
                    </form>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-xs text-gray-600 space-y-1">
                      <div>Aufgaben: <?= $u['task_count'] ?></div>
                      <div>Dokumente: <?= $u['doc_count'] ?></div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <?php if ((int)$u['id'] !== (int)$_SESSION['user_id']): ?>
                      <button onclick="confirmDelete(<?= $u['id'] ?>, '<?= htmlspecialchars($u['username']) ?>')" 
                              class="text-xs px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                        Löschen
                      </button>
                    <?php else: ?>
                      <span class="text-xs text-gray-400">Aktueller User</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

  <!-- Delete Confirmation Modal -->
  <div id="deleteModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[9999]">
    <div class="bg-white rounded-xl p-6 m-4 max-w-lg w-full">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Benutzer löschen</h3>
      <p class="text-sm text-gray-500 mb-6">Möchten Sie den Benutzer <strong id="deleteUserName"></strong> wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden. Alle Aufgaben und Dokumente dieses Benutzers werden ebenfalls gelöscht.</p>
      
      <form method="post" id="deleteForm">
        <input type="hidden" name="user_id" id="deleteUserId">
        <input type="hidden" name="action" value="delete_user">
        
        <div class="flex justify-end gap-4">
          <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg">
            Abbrechen
          </button>
          <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
            Endgültig löschen
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Delete user confirmation
    function confirmDelete(userId, username) {
      document.getElementById('deleteUserId').value = userId;
      document.getElementById('deleteUserName').textContent = username;
      document.getElementById('deleteModal').classList.remove('hidden');
      document.getElementById('deleteModal').classList.add('flex');
    }
    
    function closeDeleteModal() {
      document.getElementById('deleteModal').classList.add('hidden');
      document.getElementById('deleteModal').classList.remove('flex');
    }
    
    // Close modal on background click
    document.getElementById('deleteModal').addEventListener('click', (e) => {
      if (e.target === document.getElementById('deleteModal')) closeDeleteModal();
    });
  </script>
</body>
</html>
