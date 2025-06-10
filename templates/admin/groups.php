<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Gruppen | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: 'Inter', sans-serif; }
    @media (max-width: 768px) {
      main { margin-top: 4rem; }
    }
    .tag {
      display: inline-flex;
      align-items: center;
      padding: 2px 8px;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 500;
      line-height: 1.25rem;
      margin-right: 0.25rem;
      margin-bottom: 0.25rem;
      white-space: nowrap;
    }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#eef7ff] via-[#f7fbff] to-[#f9fdf2] flex">
  <?php require_once __DIR__.'/../../templates/navbar.php'; ?>

  <main class="ml-0 md:ml-64 flex-1 p-4 md:p-8 mt-14 md:mt-0">
    <div class="max-w-6xl mx-auto">
      <div class="flex flex-wrap justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Benutzergruppen</h1>
        <div class="flex space-x-2 mt-2 md:mt-0">
          <button id="createGroupBtn" class="px-4 py-2 bg-[#4A90E2] text-white rounded-lg hover:bg-[#4A90E2]/90">
            Neue Gruppe
          </button>
          <button id="createTagBtn" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
            Neuer Tag
          </button>
        </div>
      </div>

      <?php if (!empty($success)): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-xl">
          <?= htmlspecialchars($success) ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-xl">
          <ul class="list-disc pl-5">
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
      
      <!-- Tag management section -->
      <div class="bg-white/60 backdrop-blur-sm rounded-2xl shadow-sm p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Verfügbare Tags</h2>
        
        <div class="flex flex-wrap gap-2 mb-4">
          <?php if (empty($allTags)): ?>
            <p class="text-gray-500">Keine Tags gefunden.</p>
          <?php else: ?>
            <?php foreach ($allTags as $tag): ?>
              <div class="tag" style="background-color: <?= htmlspecialchars($tag['color']) ?>33; color: <?= htmlspecialchars($tag['color']) ?>; border: 1px solid <?= htmlspecialchars($tag['color']) ?>">
                <?= htmlspecialchars($tag['name']) ?>
                <button class="edit-tag-btn ml-2" data-tag-id="<?= $tag['id'] ?>" data-tag-name="<?= htmlspecialchars($tag['name']) ?>" data-tag-color="<?= htmlspecialchars($tag['color']) ?>">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                  </svg>
                </button>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <!-- Groups section -->
      <div class="bg-white/60 backdrop-blur-sm rounded-2xl shadow-sm p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Gruppen</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <?php if (empty($groups)): ?>
            <p class="md:col-span-3 text-gray-500">Keine Gruppen gefunden.</p>
          <?php else: ?>
            <?php foreach ($groups as $group): ?>
              <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="flex justify-between items-start">
                  <h3 class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($group['name']) ?></h3>
                  <div class="flex space-x-1">
                    <button class="edit-group-btn p-1 text-blue-500 hover:text-blue-700" 
                            data-group-id="<?= $group['id'] ?>"
                            data-name="<?= htmlspecialchars($group['name']) ?>"
                            data-description="<?= htmlspecialchars($group['description']) ?>">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                      </svg>
                    </button>
                    <button class="assign-tags-btn p-1 text-purple-500 hover:text-purple-700"
                            data-group-id="<?= $group['id'] ?>"
                            data-group-name="<?= htmlspecialchars($group['name']) ?>">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                      </svg>
                    </button>
                    <button class="delete-group-btn p-1 text-red-500 hover:text-red-700"
                            data-group-id="<?= $group['id'] ?>">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                      </svg>
                    </button>
                  </div>
                </div>
                
                <p class="text-sm text-gray-600 mt-2"><?= htmlspecialchars($group['description']) ?></p>
                
                <!-- Display group tags -->
                <?php if (!empty($group['tags'])): ?>
                  <div class="mt-3 flex flex-wrap">
                    <?php foreach ($group['tags'] as $tag): ?>
                      <span class="tag" style="background-color: <?= htmlspecialchars($tag['color']) ?>33; color: <?= htmlspecialchars($tag['color']) ?>; border: 1px solid <?= htmlspecialchars($tag['color']) ?>">
                        <?= htmlspecialchars($tag['name']) ?>
                      </span>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
                
                <div class="mt-4">
                  <h4 class="text-sm font-medium text-gray-700">Mitglieder (<?= $group['member_count'] ?>):</h4>
                  <div class="flex flex-wrap gap-2 mt-2">
                    <?php if (empty($group['members'])): ?>
                      <span class="text-gray-500 text-xs">Keine Mitglieder gefunden.</span>
                    <?php else: ?>
                      <?php foreach ($group['members'] as $member): ?>
                        <span class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-700 rounded-full text-xs">
                          <?= htmlspecialchars($member['username']) ?>
                        </span>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </main>

  <!-- Create Group Modal -->
  <div id="groupModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[9999]">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 mx-4">
      <div class="flex justify-between items-start mb-4">
        <h2 id="modalTitle" class="text-xl font-semibold">Neue Gruppe erstellen</h2>
        <button id="closeModal" class="text-gray-400 hover:text-gray-600">&times;</button>
      </div>
      
      <form id="groupForm" method="post" class="space-y-4">
        <input type="hidden" name="action" value="create_group">
        <input type="hidden" id="groupId" name="group_id" value="">
        
        <div>
          <label for="groupName" class="block text-sm font-medium text-gray-700 mb-1">Gruppenname</label>
          <input type="text" id="groupName" name="group_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
          <label for="groupDescription" class="block text-sm font-medium text-gray-700 mb-1">Beschreibung</label>
          <textarea id="groupDescription" name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
        </div>
        
        <div>
          <label for="groupMembers" class="block text-sm font-medium text-gray-700 mb-1">Mitglieder</label>
          <select id="groupMembers" name="members[]" multiple class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <?php foreach ($allUsers as $user): ?>
              <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars($user['email']) ?>)</option>
            <?php endforeach; ?>
          </select>
          <p class="text-xs text-gray-500 mt-1">Mehrere Mitglieder mit Strg+Klick auswählen</p>
        </div>
        
        <div class="flex justify-end space-x-3">
          <button type="button" id="cancelBtn" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            Abbrechen
          </button>
          <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
            Speichern
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Create Tag Modal -->
  <div id="tagModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[9999]">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 mx-4">
      <div class="flex justify-between items-start mb-4">
        <h2 id="tagModalTitle" class="text-xl font-semibold">Neuen Tag erstellen</h2>
        <button id="closeTagModal" class="text-gray-400 hover:text-gray-600">&times;</button>
      </div>
      
      <form id="tagForm" method="post" class="space-y-4">
        <input type="hidden" name="action" value="create_tag">
        <input type="hidden" id="tagId" name="tag_id" value="">
        
        <div>
          <label for="tagName" class="block text-sm font-medium text-gray-700 mb-1">Tag-Name</label>
          <input type="text" id="tagName" name="tag_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
          <label for="tagColor" class="block text-sm font-medium text-gray-700 mb-1">Farbe</label>
          <div class="flex space-x-2">
            <input type="color" id="tagColor" name="tag_color" value="#4A90E2" class="h-10 w-10 rounded cursor-pointer">
            <input type="text" id="tagColorText" value="#4A90E2" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
          </div>
        </div>
        
        <div class="flex justify-end space-x-3">
          <button type="button" id="cancelTagBtn" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            Abbrechen
          </button>
          <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
            Speichern
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Assign Tags Modal -->
  <div id="assignTagsModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[9999]">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 mx-4">
      <div class="flex justify-between items-start mb-4">
        <h2 id="assignTagsTitle" class="text-xl font-semibold">Tags zuweisen</h2>
        <button id="closeAssignTagsModal" class="text-gray-400 hover:text-gray-600">&times;</button>
      </div>
      
      <form id="assignTagsForm" method="post" class="space-y-4">
        <input type="hidden" name="action" value="assign_tags">
        <input type="hidden" id="assignGroupId" name="group_id" value="">
        
        <p class="text-gray-600 text-sm mb-2">Wählen Sie die Tags aus, die der Gruppe zugewiesen werden sollen.</p>
        
        <div class="max-h-60 overflow-y-auto p-2 border border-gray-200 rounded-lg">
          <?php if (empty($allTags)): ?>
            <p class="text-gray-500 text-sm">Keine Tags verfügbar. Bitte erstellen Sie zuerst Tags.</p>
          <?php else: ?>
            <?php foreach ($allTags as $tag): ?>
              <div class="flex items-center mb-2">
                <input type="checkbox" id="tag-<?= $tag['id'] ?>" name="tag_ids[]" value="<?= $tag['id'] ?>" class="h-4 w-4 text-blue-600 rounded">
                <label for="tag-<?= $tag['id'] ?>" class="ml-2 block">
                  <span class="tag" style="background-color: <?= htmlspecialchars($tag['color']) ?>33; color: <?= htmlspecialchars($tag['color']) ?>; border: 1px solid <?= htmlspecialchars($tag['color']) ?>">
                    <?= htmlspecialchars($tag['name']) ?>
                  </span>
                </label>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
        
        <div class="flex justify-end space-x-3">
          <button type="button" id="cancelAssignTagsBtn" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            Abbrechen
          </button>
          <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
            Tags zuweisen
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Group Modal -->
  <div id="deleteModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[9999]">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 mx-4">
      <div class="flex justify-between items-start mb-4">
        <h2 class="text-xl font-semibold">Gruppe löschen</h2>
        <button id="closeDeleteModal" class="text-gray-400 hover:text-gray-600">&times;</button>
      </div>
      
      <p class="mb-4">Bist du sicher, dass du diese Gruppe löschen möchtest? Diese Aktion kann nicht rückgängig gemacht werden.</p>
      
      <form method="post">
        <input type="hidden" name="action" value="delete_group">
        <input type="hidden" id="deleteGroupId" name="group_id" value="">
        
        <div class="flex justify-end space-x-3">
          <button type="button" id="cancelDeleteBtn" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
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
    // Create Group Modal
    const modal = document.getElementById('groupModal');
    const deleteModal = document.getElementById('deleteModal');
    const createBtn = document.getElementById('createGroupBtn');
    const closeBtn = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const closeDeleteBtn = document.getElementById('closeDeleteModal');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
    
    // Open new group modal
    createBtn.addEventListener('click', () => {
      document.getElementById('modalTitle').textContent = 'Neue Gruppe erstellen';
      document.getElementById('groupForm').reset();
      document.getElementById('groupForm').action.value = 'create_group';
      document.getElementById('groupId').value = '';
      
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    });
    
    // Close modal
    [closeBtn, cancelBtn].forEach(btn => {
      btn.addEventListener('click', () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      });
    });
    
    // Close delete modal
    [closeDeleteBtn, cancelDeleteBtn].forEach(btn => {
      btn.addEventListener('click', () => {
        deleteModal.classList.add('hidden');
        deleteModal.classList.remove('flex');
      });
    });
    
    // Edit Group
    document.querySelectorAll('.edit-group-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const groupId = btn.dataset.groupId;
        const name = btn.dataset.name;
        const description = btn.dataset.description;
        
        document.getElementById('modalTitle').textContent = 'Gruppe bearbeiten';
        document.getElementById('groupForm').action.value = 'update_group';
        document.getElementById('groupId').value = groupId;
        document.getElementById('groupName').value = name;
        document.getElementById('groupDescription').value = description;
        
        // TODO: Load group members for editing
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
      });
    });
    
    // Delete Group
    document.querySelectorAll('.delete-group-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.getElementById('deleteGroupId').value = btn.dataset.groupId;
        deleteModal.classList.remove('hidden');
        deleteModal.classList.add('flex');
      });
    });
    
    // Tag Modal
    const tagModal = document.getElementById('tagModal');
    const createTagBtn = document.getElementById('createTagBtn');
    const closeTagBtn = document.getElementById('closeTagModal');
    const cancelTagBtn = document.getElementById('cancelTagBtn');
    const tagForm = document.getElementById('tagForm');
    const tagColor = document.getElementById('tagColor');
    const tagColorText = document.getElementById('tagColorText');
    
    // Open new tag modal
    createTagBtn.addEventListener('click', () => {
      document.getElementById('tagModalTitle').textContent = 'Neuen Tag erstellen';
      tagForm.reset();
      tagForm.action.value = 'create_tag';
      document.getElementById('tagId').value = '';
      tagColor.value = '#4A90E2';
      tagColorText.value = '#4A90E2';
      
      tagModal.classList.remove('hidden');
      tagModal.classList.add('flex');
    });
    
    // Close tag modal
    [closeTagBtn, cancelTagBtn].forEach(btn => {
      btn.addEventListener('click', () => {
        tagModal.classList.add('hidden');
        tagModal.classList.remove('flex');
      });
    });
    
    // Update color input text when color picker changes
    tagColor.addEventListener('input', (e) => {
      tagColorText.value = e.target.value;
    });
    
    // Update color picker when text changes
    tagColorText.addEventListener('input', (e) => {
      // Only update if valid hex color
      if (/^#[0-9A-F]{6}$/i.test(e.target.value)) {
        tagColor.value = e.target.value;
      }
    });
    
    // Edit Tag
    document.querySelectorAll('.edit-tag-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation(); // Prevent event bubbling
        
        const tagId = btn.dataset.tagId;
        const tagName = btn.dataset.tagName;
        const tagColorValue = btn.dataset.tagColor;
        
        document.getElementById('tagModalTitle').textContent = 'Tag bearbeiten';
        tagForm.action.value = 'update_tag';
        document.getElementById('tagId').value = tagId;
        document.getElementById('tagName').value = tagName;
        tagColor.value = tagColorValue;
        tagColorText.value = tagColorValue;
        
        tagModal.classList.remove('hidden');
        tagModal.classList.add('flex');
      });
    });
    
    // Assign Tags Modal
    const assignTagsModal = document.getElementById('assignTagsModal');
    const closeAssignTagsBtn = document.getElementById('closeAssignTagsModal');
    const cancelAssignTagsBtn = document.getElementById('cancelAssignTagsBtn');
    
    // Open assign tags modal
    document.querySelectorAll('.assign-tags-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const groupId = btn.dataset.groupId;
        const groupName = btn.dataset.groupName;
        
        document.getElementById('assignTagsTitle').textContent = `Tags für "${groupName}" zuweisen`;
        document.getElementById('assignGroupId').value = groupId;
        
        // Reset all checkboxes
        document.querySelectorAll('#assignTagsForm input[type="checkbox"]').forEach(cb => {
          cb.checked = false;
        });
        
        // Get current tags for this group and check the corresponding boxes
        fetch(`/api/get_group_tags.php?group_id=${groupId}`)
          .then(response => response.json())
          .then(tags => {
            tags.forEach(tagId => {
              const checkbox = document.getElementById(`tag-${tagId}`);
              if (checkbox) checkbox.checked = true;
            });
          })
          .catch(error => console.error('Error fetching group tags:', error));
        
        assignTagsModal.classList.remove('hidden');
        assignTagsModal.classList.add('flex');
      });
    });
    
    // Close assign tags modal
    [closeAssignTagsBtn, cancelAssignTagsBtn].forEach(btn => {
      btn.addEventListener('click', () => {
        assignTagsModal.classList.add('hidden');
        assignTagsModal.classList.remove('flex');
      });
    });
    
    // Close modals on background click
    [modal, deleteModal, tagModal, assignTagsModal].forEach(m => {
      m.addEventListener('click', e => {
        if (e.target === m) {
          m.classList.add('hidden');
          m.classList.remove('flex');
        }
      });
    });
  </script>
</body>
</html>
