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
    body { 
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
      min-height: 100vh;
    }
    
    /* Glassmorphism containers */
    .glassmorphism-container {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    
    /* Stats cards - Only hover on these */
    .stats-card {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 1rem;
      transition: all 0.3s ease;
    }
    
    .stats-card:hover {
      background: rgba(255, 255, 255, 0.15);
      transform: translateY(-2px);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
    }
    
    /* Form inputs */
    .form-input {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      border-radius: 0.5rem;
    }
    
    .form-input:focus {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.4);
      outline: none;
      box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
    }
    
    /* Buttons */
    .btn-primary {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.8) 0%, rgba(79, 70, 229, 0.8) 100%);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.9) 0%, rgba(79, 70, 229, 0.9) 100%);
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(147, 51, 234, 0.3);
    }
    
    .btn-secondary {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      transition: all 0.3s ease;
    }
    
    .btn-secondary:hover {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.3);
      transform: translateY(-1px);
    }
    
    .btn-danger {
      background: linear-gradient(135deg, rgba(239, 68, 68, 0.8) 0%, rgba(220, 38, 38, 0.8) 100%);
      border: 1px solid rgba(239, 68, 68, 0.3);
      color: white;
      transition: all 0.3s ease;
    }
    
    .btn-danger:hover {
      background: linear-gradient(135deg, rgba(239, 68, 68, 0.9) 0%, rgba(220, 38, 38, 0.9) 100%);
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(239, 68, 68, 0.3);
    }
    
    /* Table styling */
    .table-container {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .table-header {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    }
    
    .table-row {
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      transition: all 0.2s ease;
    }
    
    .table-row:hover {
      background: rgba(255, 255, 255, 0.05);
    }
    
    /* Alert styling */
    .alert-success {
      background: rgba(34, 197, 94, 0.2);
      border: 1px solid rgba(34, 197, 94, 0.4);
      color: #86efac;
      backdrop-filter: blur(10px);
    }
    
    .alert-error {
      background: rgba(239, 68, 68, 0.2);
      border: 1px solid rgba(239, 68, 68, 0.4);
      color: #fca5a5;
      backdrop-filter: blur(10px);
    }
    
    /* Mobile adjustments */
    @media (max-width: 768px) {
      main { margin-top: 4rem; }
    }
    
    @media (min-width: 769px) {
      main { margin-left: 16rem; }
    }
  </style>
</head>

<body class="min-h-screen flex flex-col">
  <!-- Navbar -->
  <?php require_once __DIR__ . '/navbar.php'; ?>

  <main class="ml-0 mt-16 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
    <div class="max-w-7xl mx-auto space-y-6">
      <!-- Page Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h1 class="text-3xl font-bold text-white">Admin Dashboard</h1>
        <button id="createUserBtn" class="px-6 py-3 btn-primary rounded-lg text-sm font-medium">
          + Neuen Benutzer anlegen
        </button>
      </div>

      <!-- Notifications -->
      <?php if ($success): ?>
        <div class="alert-success px-6 py-4 rounded-lg">
          <?= htmlspecialchars($success) ?>
        </div>
      <?php endif; ?>
      
      <?php if ($errors): ?>
        <div class="alert-error px-6 py-4 rounded-lg">
          <ul class="list-disc list-inside">
            <?php foreach ($errors as $e): ?>
              <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <!-- Stats Overview -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="stats-card p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-500/20 text-blue-300">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
            <div class="ml-5">
              <p class="text-gray-300 text-sm">Benutzer</p>
              <h3 class="text-2xl font-bold text-white"><?= number_format($stats['users']) ?></h3>
            </div>
          </div>
        </div>
        
        <div class="stats-card p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-500/20 text-green-300">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
            </div>
            <div class="ml-5">
              <p class="text-gray-300 text-sm">Aufgaben</p>
              <h3 class="text-2xl font-bold text-white"><?= number_format($stats['tasks']) ?></h3>
            </div>
          </div>
        </div>
        
        <div class="stats-card p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-500/20 text-purple-300">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
              </svg>
            </div>
            <div class="ml-5">
              <p class="text-gray-300 text-sm">Dokumente</p>
              <h3 class="text-2xl font-bold text-white"><?= number_format($stats['documents']) ?></h3>
            </div>
          </div>
        </div>
      </div>

      <!-- User Management -->
      <div class="glassmorphism-container overflow-hidden">
        <div class="px-6 py-4 table-header">
          <h2 class="text-xl font-semibold text-white">Benutzerverwaltung</h2>
        </div>
        
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="table-header">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Benutzer</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">E-Mail</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Rolle</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Statistik</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Aktionen</th>
              </tr>
            </thead>
            <tbody class="table-container">
              <?php foreach ($users as $u): ?>
                <tr class="table-row">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-white"><?= $u['id'] ?></td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center text-white font-semibold">
                        <?= strtoupper(substr($u['username'], 0, 2)) ?>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-white"><?= htmlspecialchars($u['username']) ?></div>
                        <div class="text-sm text-gray-300">Seit <?= date('d.m.Y', strtotime($u['created_at'])) ?></div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300"><?= htmlspecialchars($u['email']) ?></td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <form method="post" class="flex items-center space-x-2">
                      <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                      <input type="hidden" name="action" value="update_role">
                      <select name="role" class="text-sm form-input">
                        <?php foreach (['admin','member','guest'] as $role): ?>
                          <option value="<?= $role ?>" <?= $u['role'] === $role ? 'selected' : '' ?>><?= ucfirst($role) ?></option>
                        <?php endforeach; ?>
                      </select>
                      <button type="submit" class="text-xs px-3 py-1 btn-secondary rounded">
                        Ändern
                      </button>
                    </form>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-xs text-gray-300 space-y-1">
                      <div>Aufgaben: <?= $u['task_count'] ?></div>
                      <div>Dokumente: <?= $u['doc_count'] ?></div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <?php if ((int)$u['id'] !== (int)$_SESSION['user_id']): ?>
                      <button onclick="confirmDelete(<?= $u['id'] ?>, '<?= htmlspecialchars($u['username']) ?>')" 
                              class="text-xs px-3 py-1 btn-danger rounded">
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
  <div id="deleteModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center z-[9999]">
    <div class="glassmorphism-container p-6 m-4 max-w-lg w-full">
      <h3 class="text-lg font-medium text-white mb-4">Benutzer löschen</h3>
      <p class="text-sm text-gray-300 mb-6">Möchten Sie den Benutzer <strong id="deleteUserName" class="text-white"></strong> wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden. Alle Aufgaben und Dokumente dieses Benutzers werden ebenfalls gelöscht.</p>
      
      <form method="post" id="deleteForm">
        <input type="hidden" name="user_id" id="deleteUserId">
        <input type="hidden" name="action" value="delete_user">
        
        <div class="flex justify-end gap-4">
          <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-600/50 text-gray-200 rounded-lg hover:bg-gray-600/70 transition">
            Abbrechen
          </button>
          <button type="submit" class="px-4 py-2 btn-danger rounded-lg">
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
