<?php require_once __DIR__ . '/../navbar.php'; ?>

<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Gruppenverwaltung | Private Vault</title>
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

  <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
    <div class="max-w-7xl mx-auto space-y-6">

      <!-- Page Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h1 class="text-2xl font-bold text-gray-900">Gruppenverwaltung</h1>
        <button id="createGroupBtn" class="px-4 py-2 bg-green-500 text-white rounded-lg text-sm font-medium hover:bg-green-600 transition">
          + Neue Gruppe anlegen
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

      <!-- Groups List -->
      <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
          <h2 class="text-lg font-semibold text-gray-900">Benutzergruppen</h2>
        </div>
        
        <?php if (empty($groups)): ?>
          <div class="p-6 text-gray-500 text-center">
            Keine Gruppen gefunden. Erstellen Sie Ihre erste Gruppe mit dem Button oben.
          </div>
        <?php else: ?>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Beschreibung</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mitglieder</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Erstellt am</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktionen</th>
                </tr>
              </thead>
              <tbody class="bg-white/40 divide-y divide-gray-200">
                <?php foreach ($groups as $group): ?>
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      <?= htmlspecialchars($group['name']) ?>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                      <?= htmlspecialchars($group['description'] ?? '') ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <div class="flex -space-x-2 overflow-hidden">
                        <?php foreach (array_slice($group['members'], 0, 5) as $idx => $member): ?>
                          <div class="h-8 w-8 rounded-full bg-blue-100 ring-2 ring-white flex items-center justify-center text-xs font-medium text-blue-600">
                            <?= htmlspecialchars(substr($member['username'], 0, 2)) ?>
                          </div>
                        <?php endforeach; ?>
                        
                        <?php if (count($group['members']) > 5): ?>
                          <div class="h-8 w-8 rounded-full bg-gray-100 ring-2 ring-white flex items-center justify-center text-xs font-medium text-gray-600">
                            +<?= count($group['members']) - 5 ?>
                          </div>
                        <?php endif; ?>
                      </div>
                      <div class="mt-1 text-xs text-gray-500">
                        <?= $group['member_count'] ?> Mitglieder
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <?= date('d.m.Y H:i', strtotime($group['created_at'])) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <button onclick="openEditGroupModal(<?= $group['id'] ?>, '<?= htmlspecialchars($group['name']) ?>', '<?= htmlspecialchars($group['description'] ?? '') ?>', <?= htmlspecialchars(json_encode(array_column($group['members'], 'id'))) ?>)" 
                              class="text-blue-600 hover:text-blue-900 mr-3">
                        Bearbeiten
                      </button>
                      <button onclick="confirmDeleteGroup(<?= $group['id'] ?>, '<?= htmlspecialchars($group['name']) ?>')" 
                              class="text-red-600 hover:text-red-900">
                        Löschen
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </main>

  <!-- Create Group Modal -->
  <div id="createGroupModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 m-4 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
      <div class="flex justify-between items-start mb-4">
        <h3 class="text-lg font-semibold">Neue Gruppe erstellen</h3>
        <button onclick="closeCreateGroupModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
      </div>
      <form method="post" class="space-y-4">
        <input type="hidden" name="action" value="create_group">
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Gruppenname</label>
          <input type="text" name="group_name" required 
                 class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Beschreibung</label>
          <textarea name="description" rows="2" 
                   class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Mitglieder</label>
          <div class="border rounded-lg p-3 max-h-48 overflow-y-auto">
            <div class="mb-2">
              <input type="text" id="memberSearch" placeholder="Nach Benutzern suchen..." 
                     class="w-full px-3 py-2 border rounded-lg">
            </div>
            <?php foreach ($allUsers as $user): ?>
              <div class="user-item py-1">
                <label class="flex items-center space-x-2">
                  <input type="checkbox" name="members[]" value="<?= $user['id'] ?>" 
                         class="h-4 w-4 rounded border-gray-300 text-blue-600">
                  <span class="text-sm"><?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars($user['email']) ?>)</span>
                </label>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        
        <div class="flex justify-end space-x-3 pt-3">
          <button type="button" onclick="closeCreateGroupModal()" 
                 class="px-4 py-2 border rounded-lg hover:bg-gray-50">
            Abbrechen
          </button>
          <button type="submit" 
                 class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
            Gruppe erstellen
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Group Modal -->
  <div id="editGroupModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 m-4 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
      <div class="flex justify-between items-start mb-4">
        <h3 class="text-lg font-semibold">Gruppe bearbeiten</h3>
        <button onclick="closeEditGroupModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
      </div>
      <form method="post" class="space-y-4">
        <input type="hidden" name="action" value="update_group">
        <input type="hidden" name="group_id" id="editGroupId">
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Gruppenname</label>
          <input type="text" name="group_name" id="editGroupName" required 
                 class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Beschreibung</label>
          <textarea name="description" id="editGroupDescription" rows="2" 
                   class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Mitglieder</label>
          <div class="border rounded-lg p-3 max-h-48 overflow-y-auto">
            <div class="mb-2">
              <input type="text" id="editMemberSearch" placeholder="Nach Benutzern suchen..." 
                     class="w-full px-3 py-2 border rounded-lg">
            </div>
            <?php foreach ($allUsers as $user): ?>
              <div class="edit-user-item py-1">
                <label class="flex items-center space-x-2">
                  <input type="checkbox" name="members[]" value="<?= $user['id'] ?>" 
                         class="edit-user-checkbox h-4 w-4 rounded border-gray-300 text-blue-600">
                  <span class="text-sm"><?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars($user['email']) ?>)</span>
                </label>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        
        <div class="flex justify-end space-x-3 pt-3">
          <button type="button" onclick="closeEditGroupModal()" 
                 class="px-4 py-2 border rounded-lg hover:bg-gray-50">
            Abbrechen
          </button>
          <button type="submit" 
                 class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
            Änderungen speichern
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div id="deleteGroupModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 m-4 max-w-md w-full">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Gruppe löschen</h3>
      <p class="text-sm text-gray-500 mb-6">Möchten Sie die Gruppe <strong id="deleteGroupName"></strong> wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.</p>
      
      <form method="post">
        <input type="hidden" name="action" value="delete_group">
        <input type="hidden" name="group_id" id="deleteGroupId">
        
        <div class="flex justify-end gap-4">
          <button type="button" onclick="closeDeleteGroupModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg">
            Abbrechen
          </button>
          <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
            Löschen
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // User search filter logic
    document.getElementById('memberSearch').addEventListener('input', function(e) {
      const searchTerm = e.target.value.toLowerCase();
      document.querySelectorAll('.user-item').forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(searchTerm) ? '' : 'none';
      });
    });
    
    document.getElementById('editMemberSearch').addEventListener('input', function(e) {
      const searchTerm = e.target.value.toLowerCase();
      document.querySelectorAll('.edit-user-item').forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(searchTerm) ? '' : 'none';
      });
    });
    
    // Create Group Modal
    document.getElementById('createGroupBtn').addEventListener('click', function() {
      document.getElementById('createGroupModal').classList.remove('hidden');
      document.getElementById('createGroupModal').classList.add('flex');
    });
    
    function closeCreateGroupModal() {
      document.getElementById('createGroupModal').classList.add('hidden');
      document.getElementById('createGroupModal').classList.remove('flex');
    }
    
    // Edit Group Modal
    function openEditGroupModal(groupId, groupName, groupDescription, selectedMembers) {
      document.getElementById('editGroupId').value = groupId;
      document.getElementById('editGroupName').value = groupName;
      document.getElementById('editGroupDescription').value = groupDescription || '';
      
      // Reset all checkboxes
      document.querySelectorAll('.edit-user-checkbox').forEach(checkbox => {
        checkbox.checked = false;
      });
      
      // Check selected members
      selectedMembers.forEach(memberId => {
        const checkbox = document.querySelector(`.edit-user-checkbox[value="${memberId}"]`);
        if (checkbox) checkbox.checked = true;
      });
      
      document.getElementById('editGroupModal').classList.remove('hidden');
      document.getElementById('editGroupModal').classList.add('flex');
    }
    
    function closeEditGroupModal() {
      document.getElementById('editGroupModal').classList.add('hidden');
      document.getElementById('editGroupModal').classList.remove('flex');
    }
    
    // Delete Group Modal
    function confirmDeleteGroup(groupId, groupName) {
      document.getElementById('deleteGroupId').value = groupId;
      document.getElementById('deleteGroupName').textContent = groupName;
      document.getElementById('deleteGroupModal').classList.remove('hidden');
      document.getElementById('deleteGroupModal').classList.add('flex');
    }
    
    function closeDeleteGroupModal() {
      document.getElementById('deleteGroupModal').classList.add('hidden');
      document.getElementById('deleteGroupModal').classList.remove('flex');
    }
    
    // Close modals on background click
    document.querySelectorAll('#createGroupModal, #editGroupModal, #deleteGroupModal').forEach(modal => {
      modal.addEventListener('click', (e) => {
        if (e.target === modal) {
          modal.classList.add('hidden');
          modal.classList.remove('flex');
        }
      });
    });
  </script>
</body>
</html>
