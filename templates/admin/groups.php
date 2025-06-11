<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Gruppen | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { 
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
      min-height: 100vh;
    }
    @media (max-width: 768px) {
      main { margin-top: 4rem; }
    }
    
    /* Liquid glass styling */
    .glass-card {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1.5rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      color: white;
      transition: all 0.3s ease;
    }
    .glass-card:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(255, 255, 255, 0.2);
      transform: translateY(-2px);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
    }
    
    .glass-button {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      border-radius: 0.75rem;
      transition: all 0.3s ease;
    }
    .glass-button:hover {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.3);
      transform: translateY(-1px);
    }
    
    .glass-input {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      border-radius: 0.5rem;
    }
    .glass-input:focus {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.3);
      outline: none;
      box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
    }
    .glass-input::placeholder {
      color: rgba(255, 255, 255, 0.5);
    }
    
    .glass-modal {
      background: linear-gradient(135deg, #2d1b69 0%, #11101d 100%);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1rem;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    }
    
    .tag {
      display: inline-flex;
      align-items: center;
      padding: 4px 12px;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 500;
      line-height: 1.25rem;
      margin-right: 0.25rem;
      margin-bottom: 0.25rem;
      white-space: nowrap;
      backdrop-filter: blur(10px);
    }
    
    .member-badge {
      background: rgba(147, 51, 234, 0.2);
      border: 1px solid rgba(147, 51, 234, 0.3);
      color: #c4b5fd;
      padding: 4px 8px;
      border-radius: 9999px;
      font-size: 0.75rem;
      backdrop-filter: blur(10px);
    }
    
    .action-button {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 0.5rem;
      padding: 4px;
      transition: all 0.3s ease;
    }
    .action-button:hover {
      background: rgba(255, 255, 255, 0.15);
      transform: scale(1.05);
    }
    
    .header-text {
      color: white;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }
    
    .success-message {
      background: rgba(34, 197, 94, 0.2);
      border: 1px solid rgba(34, 197, 94, 0.3);
      color: #86efac;
      border-radius: 0.75rem;
      backdrop-filter: blur(10px);
    }
    
    .error-message {
      background: rgba(239, 68, 68, 0.2);
      border: 1px solid rgba(239, 68, 68, 0.3);
      color: #fca5a5;
      border-radius: 0.75rem;
      backdrop-filter: blur(10px);
    }
  </style>
</head>
<body class="min-h-screen flex">
  <?php require_once __DIR__.'/../../templates/navbar.php'; ?>

  <main class="ml-0 md:ml-64 flex-1 p-4 md:p-8 mt-16 md:mt-0">
    <div class="max-w-6xl mx-auto">
      <div class="flex flex-wrap justify-between items-center mb-6">
        <h1 class="text-3xl font-bold header-text">Benutzergruppen</h1>
        <div class="flex space-x-3 mt-2 md:mt-0">
          <button id="createGroupBtn" class="glass-button px-6 py-3 font-medium">
            <i class="fas fa-plus mr-2"></i>Neue Gruppe
          </button>
          <button id="createTagBtn" class="glass-button px-6 py-3 font-medium">
            <i class="fas fa-tag mr-2"></i>Neuer Tag
          </button>
        </div>
      </div>

      <?php if (!empty($success)): ?>
        <div class="mb-6 success-message p-4">
          <i class="fas fa-check-circle mr-2"></i>
          <?= htmlspecialchars($success) ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <div class="mb-6 error-message p-4">
          <i class="fas fa-exclamation-circle mr-2"></i>
          <ul class="list-disc pl-5">
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
      
      <!-- Tag management section -->
      <div class="glass-card p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4 text-white/90">
          <i class="fas fa-tags mr-2"></i>Verfügbare Tags
        </h2>
        
        <div class="flex flex-wrap gap-2 mb-4">
          <?php if (empty($allTags)): ?>
            <p class="text-white/50">Keine Tags gefunden.</p>
          <?php else: ?>
            <?php foreach ($allTags as $tag): ?>
              <div class="tag" style="background-color: <?= htmlspecialchars($tag['color']) ?>33; color: <?= htmlspecialchars($tag['color']) ?>; border: 1px solid <?= htmlspecialchars($tag['color']) ?>">
                <?= htmlspecialchars($tag['name']) ?>
                <button class="edit-tag-btn ml-2 hover:scale-110 transition-transform" data-tag-id="<?= $tag['id'] ?>" data-tag-name="<?= htmlspecialchars($tag['name']) ?>" data-tag-color="<?= htmlspecialchars($tag['color']) ?>">
                  <i class="fas fa-edit text-xs"></i>
                </button>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <!-- Groups section -->
      <div class="glass-card p-6 mb-8">
        <h2 class="text-xl font-semibold mb-6 text-white/90">
          <i class="fas fa-users mr-2"></i>Gruppen
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php if (empty($groups)): ?>
            <p class="md:col-span-3 text-white/50 text-center py-8">Keine Gruppen gefunden.</p>
          <?php else: ?>
            <?php foreach ($groups as $group): ?>
              <div class="glass-card p-5 border border-white/10">
                <div class="flex justify-between items-start mb-4">
                  <h3 class="text-lg font-semibold text-white/90"><?= htmlspecialchars($group['name']) ?></h3>
                  <div class="flex space-x-1">
                    <button class="edit-group-btn action-button text-blue-400 hover:text-blue-300" 
                            data-group-id="<?= $group['id'] ?>"
                            data-name="<?= htmlspecialchars($group['name']) ?>"
                            data-description="<?= htmlspecialchars($group['description']) ?>">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="assign-tags-btn action-button text-purple-400 hover:text-purple-300"
                            data-group-id="<?= $group['id'] ?>"
                            data-group-name="<?= htmlspecialchars($group['name']) ?>">
                      <i class="fas fa-tag"></i>
                    </button>
                    <button class="delete-group-btn action-button text-red-400 hover:text-red-300"
                            data-group-id="<?= $group['id'] ?>">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </div>
                
                <p class="text-sm text-white/60 mb-4"><?= htmlspecialchars($group['description']) ?></p>
                
                <!-- Display group tags -->
                <?php if (!empty($group['tags'])): ?>
                  <div class="mb-4 flex flex-wrap gap-1">
                    <?php foreach ($group['tags'] as $tag): ?>
                      <span class="tag" style="background-color: <?= htmlspecialchars($tag['color']) ?>33; color: <?= htmlspecialchars($tag['color']) ?>; border: 1px solid <?= htmlspecialchars($tag['color']) ?>">
                        <?= htmlspecialchars($tag['name']) ?>
                      </span>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
                
                <div>
                  <h4 class="text-sm font-medium text-white/70 mb-2">
                    <i class="fas fa-user mr-1"></i>Mitglieder (<?= $group['member_count'] ?>):
                  </h4>
                  <div class="flex flex-wrap gap-2">
                    <?php if (empty($group['members'])): ?>
                      <span class="text-white/40 text-xs">Keine Mitglieder gefunden.</span>
                    <?php else: ?>
                      <?php foreach ($group['members'] as $member): ?>
                        <span class="member-badge">
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
  <div id="groupModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-[9999]">
    <div class="glass-modal rounded-xl shadow-xl w-full max-w-md p-6 mx-4 max-h-[90vh] overflow-y-auto">
      <div class="flex justify-between items-start mb-4">
        <h2 id="modalTitle" class="text-xl font-semibold text-white">Neue Gruppe erstellen</h2>
        <button id="closeModal" class="text-white/60 hover:text-white text-2xl">&times;</button>
      </div>
      
      <form id="groupForm" method="post" class="space-y-4">
        <input type="hidden" name="action" value="create_group">
        <input type="hidden" id="groupId" name="group_id" value="">
        
        <div>
          <label for="groupName" class="block text-sm font-medium text-white/80 mb-1">Gruppenname</label>
          <input type="text" id="groupName" name="group_name" required class="w-full px-4 py-3 glass-input">
        </div>
        
        <div>
          <label for="groupDescription" class="block text-sm font-medium text-white/80 mb-1">Beschreibung</label>
          <textarea id="groupDescription" name="description" rows="3" class="w-full px-4 py-3 glass-input"></textarea>
        </div>
        
        <div>
          <label for="groupMembers" class="block text-sm font-medium text-white/80 mb-1">Mitglieder</label>
          <select id="groupMembers" name="members[]" multiple class="w-full px-4 py-3 glass-input" style="min-height: 120px;">
            <?php foreach ($allUsers as $user): ?>
              <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars($user['email']) ?>)</option>
            <?php endforeach; ?>
          </select>
          <p class="text-xs text-white/50 mt-1">Mehrere Mitglieder mit Strg+Klick auswählen</p>
        </div>
        
        <div class="flex justify-end space-x-3 pt-4">
          <button type="button" id="cancelBtn" class="glass-button px-4 py-2">
            Abbrechen
          </button>
          <button type="submit" class="glass-button px-4 py-2 bg-blue-600/30 border-blue-400/50">
            Speichern
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Create Tag Modal -->
  <div id="tagModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-[9999]">
    <div class="glass-modal rounded-xl shadow-xl w-full max-w-md p-6 mx-4">
      <div class="flex justify-between items-start mb-4">
        <h2 id="tagModalTitle" class="text-xl font-semibold text-white">Neuen Tag erstellen</h2>
        <button id="closeTagModal" class="text-white/60 hover:text-white text-2xl">&times;</button>
      </div>
      
      <form id="tagForm" method="post" class="space-y-4">
        <input type="hidden" name="action" value="create_tag">
        <input type="hidden" id="tagId" name="tag_id" value="">
        
        <div>
          <label for="tagName" class="block text-sm font-medium text-white/80 mb-1">Tag-Name</label>
          <input type="text" id="tagName" name="tag_name" required class="w-full px-4 py-3 glass-input">
        </div>
        
        <div>
          <label for="tagColor" class="block text-sm font-medium text-white/80 mb-1">Farbe</label>
          <div class="flex space-x-2">
            <input type="color" id="tagColor" name="tag_color" value="#4A90E2" class="h-12 w-12 rounded-lg cursor-pointer border border-white/20">
            <input type="text" id="tagColorText" value="#4A90E2" class="flex-1 px-4 py-3 glass-input">
          </div>
        </div>
        
        <div class="flex justify-end space-x-3 pt-4">
          <button type="button" id="cancelTagBtn" class="glass-button px-4 py-2">
            Abbrechen
          </button>
          <button type="submit" class="glass-button px-4 py-2 bg-green-600/30 border-green-400/50">
            Speichern
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Assign Tags Modal -->
  <div id="assignTagsModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-[9999]">
    <div class="glass-modal rounded-xl shadow-xl w-full max-w-md p-6 mx-4">
      <div class="flex justify-between items-start mb-4">
        <h2 id="assignTagsTitle" class="text-xl font-semibold text-white">Tags zuweisen</h2>
        <button id="closeAssignTagsModal" class="text-white/60 hover:text-white text-2xl">&times;</button>
      </div>
      
      <form id="assignTagsForm" method="post" class="space-y-4">
        <input type="hidden" name="action" value="assign_tags">
        <input type="hidden" id="assignGroupId" name="group_id" value="">
        
        <p class="text-white/60 text-sm mb-2">Wählen Sie die Tags aus, die der Gruppe zugewiesen werden sollen.</p>
        
        <div class="max-h-60 overflow-y-auto p-3 glass-card border border-white/20 rounded-lg">
          <?php if (empty($allTags)): ?>
            <p class="text-white/50 text-sm">Keine Tags verfügbar. Bitte erstellen Sie zuerst Tags.</p>
          <?php else: ?>
            <?php foreach ($allTags as $tag): ?>
              <div class="flex items-center mb-3">
                <input type="checkbox" id="tag-<?= $tag['id'] ?>" name="tag_ids[]" value="<?= $tag['id'] ?>" class="h-4 w-4 text-blue-600 rounded bg-white/10 border-white/20">
                <label for="tag-<?= $tag['id'] ?>" class="ml-3 block">
                  <span class="tag" style="background-color: <?= htmlspecialchars($tag['color']) ?>33; color: <?= htmlspecialchars($tag['color']) ?>; border: 1px solid <?= htmlspecialchars($tag['color']) ?>">
                    <?= htmlspecialchars($tag['name']) ?>
                  </span>
                </label>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
        
        <div class="flex justify-end space-x-3 pt-4">
          <button type="button" id="cancelAssignTagsBtn" class="glass-button px-4 py-2">
            Abbrechen
          </button>
          <button type="submit" class="glass-button px-4 py-2 bg-purple-600/30 border-purple-400/50">
            Tags zuweisen
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Group Modal -->
  <div id="deleteModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-[9999]">
    <div class="glass-modal rounded-xl shadow-xl w-full max-w-md p-6 mx-4">
      <div class="flex justify-between items-start mb-4">
        <h2 class="text-xl font-semibold text-white">Gruppe löschen</h2>
        <button id="closeDeleteModal" class="text-white/60 hover:text-white text-2xl">&times;</button>
      </div>
      
      <p class="mb-6 text-white/80">Bist du sicher, dass du diese Gruppe löschen möchtest? Diese Aktion kann nicht rückgängig gemacht werden.</p>
      
      <form method="post">
        <input type="hidden" name="action" value="delete_group">
        <input type="hidden" id="deleteGroupId" name="group_id" value="">
        
        <div class="flex justify-end space-x-3">
          <button type="button" id="cancelDeleteBtn" class="glass-button px-4 py-2">
            Abbrechen
          </button>
          <button type="submit" class="glass-button px-4 py-2 bg-red-600/30 border-red-400/50">
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
