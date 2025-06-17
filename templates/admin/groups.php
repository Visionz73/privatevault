<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Gruppen | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body { 
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
      min-height: 100vh;
    }
    
    @media (max-width: 768px) {
      main { margin-top: 4rem; }
    }
    
    /* Enhanced liquid glass styling */
    .glass-card {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1.5rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      color: white;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }
    
    .glass-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.05), transparent);
      transition: left 0.6s ease;
    }
    
    .glass-card:hover::before {
      left: 100%;
    }
    
    .glass-card:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(255, 255, 255, 0.25);
      transform: translateY(-2px);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
    }
    
    .glass-button {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.8) 0%, rgba(79, 70, 229, 0.8) 100%);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      border-radius: 0.75rem;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }
    
    .glass-button::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
      transition: left 0.5s ease;
    }
    
    .glass-button:hover::before {
      left: 100%;
    }
    
    .glass-button:hover {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.9) 0%, rgba(79, 70, 229, 0.9) 100%);
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(147, 51, 234, 0.3);
    }
    
    .glass-input {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      border-radius: 0.75rem;
      transition: all 0.3s ease;
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
      background: rgba(0, 0, 0, 0.7);
      backdrop-filter: blur(10px);
    }
    
    .modal-content {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(30px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1.5rem;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    }
    
    .floating-tag {
      display: inline-flex;
      align-items: center;
      padding: 6px 12px;
      border-radius: 1rem;
      font-size: 0.75rem;
      font-weight: 500;
      margin: 2px;
      backdrop-filter: blur(10px);
      transition: all 0.3s ease;
      cursor: pointer;
      position: relative;
      overflow: hidden;
    }
    
    .floating-tag:hover {
      transform: scale(1.05) translateY(-1px);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }
    
    .member-bubble {
      background: rgba(147, 51, 234, 0.2);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(147, 51, 234, 0.3);
      color: #c4b5fd;
      padding: 4px 8px;
      border-radius: 1rem;
      font-size: 0.75rem;
      font-weight: 500;
      margin: 2px;
      transition: all 0.3s ease;
      display: inline-block;
    }
    
    .member-bubble:hover {
      background: rgba(147, 51, 234, 0.3);
      transform: scale(1.05);
    }
    
    .action-btn {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 0.5rem;
      padding: 6px;
      transition: all 0.3s ease;
      color: white;
      cursor: pointer;
    }
    
    .action-btn:hover {
      background: rgba(255, 255, 255, 0.15);
      transform: scale(1.05);
    }
    
    .stats-card {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1rem;
      transition: all 0.3s ease;
    }
    
    .stats-card:hover {
      background: rgba(255, 255, 255, 0.12);
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }
    
    .header-gradient {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(30px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1.5rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    
    .success-alert {
      background: rgba(34, 197, 94, 0.2);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(34, 197, 94, 0.3);
      color: #86efac;
      border-radius: 1rem;
    }
    
    .error-alert {
      background: rgba(239, 68, 68, 0.2);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(239, 68, 68, 0.3);
      color: #fca5a5;
      border-radius: 1rem;
    }
    
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .fade-in-up {
      animation: fadeInUp 0.5s ease forwards;
    }
    
    .group-card {
      animation: fadeInUp 0.5s ease forwards;
      opacity: 0;
    }
    
    .group-card:nth-child(1) { animation-delay: 0.1s; }
    .group-card:nth-child(2) { animation-delay: 0.2s; }
    .group-card:nth-child(3) { animation-delay: 0.3s; }
  </style>
</head>
<body class="min-h-screen flex">
  <?php require_once __DIR__.'/../../templates/navbar.php'; ?>

  <main class="ml-0 md:ml-64 flex-1 p-4 md:p-8 mt-16 md:mt-0">
    <div class="max-w-7xl mx-auto">
      
      <!-- Header Section -->
      <div class="header-gradient">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
          <div>
            <h1 class="text-4xl font-bold text-white mb-2">
              <i class="fas fa-users-gear mr-3 text-blue-300"></i>
              Benutzergruppen
            </h1>
            <p class="text-white/70 text-lg">Verwalten Sie Ihre Teams und organisieren Sie Benutzer</p>
          </div>
          
          <div class="flex flex-wrap gap-3">
            <button id="createGroupBtn" class="glass-button px-6 py-3 font-medium group">
              <i class="fas fa-plus mr-2 group-hover:rotate-90 transition-transform duration-300"></i>
              Neue Gruppe
            </button>
            <button id="createTagBtn" class="glass-button px-6 py-3 font-medium group">
              <i class="fas fa-tag mr-2 group-hover:rotate-12 transition-transform duration-300"></i>
              Neuer Tag
            </button>
          </div>
        </div>
        
        <!-- Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
          <div class="stats-card">
            <div class="text-3xl font-bold text-blue-300"><?= count($groups) ?></div>
            <div class="text-white/70 text-sm mt-1">Gruppen</div>
          </div>
          <div class="stats-card">
            <div class="text-3xl font-bold text-purple-300"><?= count($allTags) ?></div>
            <div class="text-white/70 text-sm mt-1">Tags</div>
          </div>
          <div class="stats-card">
            <div class="text-3xl font-bold text-green-300"><?= count($allUsers) ?></div>
            <div class="text-white/70 text-sm mt-1">Benutzer</div>
          </div>
        </div>
      </div>

      <!-- Alerts -->
      <?php if (!empty($success)): ?>
        <div class="mb-6 success-alert p-4">
          <i class="fas fa-check-circle mr-2"></i>
          <?= htmlspecialchars($success) ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <div class="mb-6 error-alert p-4">
          <i class="fas fa-exclamation-triangle mr-2"></i>
          <ul class="list-disc pl-5">
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
      
      <!-- Tag Management Section -->
      <div class="glass-card p-8 mb-8 pulse-glow">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-bold text-white flex items-center">
            <i class="fas fa-tags mr-3 text-yellow-400"></i>
            Tag-Bibliothek
          </h2>
          <span class="text-white/50 text-sm"><?= count($allTags) ?> verfügbare Tags</span>
        </div>
        
        <div class="min-h-[120px] flex items-center justify-center">
          <?php if (empty($allTags)): ?>
            <div class="text-center py-8">
              <i class="fas fa-tag text-6xl text-white/20 mb-4"></i>
              <p class="text-white/50 text-lg">Noch keine Tags erstellt</p>
              <p class="text-white/30 text-sm">Erstellen Sie Ihren ersten Tag!</p>
            </div>
          <?php else: ?>
            <div class="flex flex-wrap gap-3 justify-center">
              <?php foreach ($allTags as $tag): ?>
                <div class="floating-tag" style="background: <?= htmlspecialchars($tag['color']) ?>40; border: 2px solid <?= htmlspecialchars($tag['color']) ?>80; color: white;">
                  <span class="font-medium"><?= htmlspecialchars($tag['name']) ?></span>
                  <button class="edit-tag-btn ml-3 p-1 rounded-full hover:bg-white/20 transition-all duration-200" 
                          data-tag-id="<?= $tag['id'] ?>" 
                          data-tag-name="<?= htmlspecialchars($tag['name']) ?>" 
                          data-tag-color="<?= htmlspecialchars($tag['color']) ?>">
                    <i class="fas fa-pencil text-xs"></i>
                  </button>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Groups Section -->
      <div class="glass-card p-8">
        <div class="flex items-center justify-between mb-8">
          <h2 class="text-2xl font-bold text-white flex items-center">
            <i class="fas fa-layer-group mr-3 text-blue-400"></i>
            Gruppen-Übersicht
          </h2>
          <span class="text-white/50 text-sm"><?= count($groups) ?> aktive Gruppen</span>
        </div>
        
        <?php if (empty($groups)): ?>
          <div class="text-center py-16">
            <i class="fas fa-users text-8xl text-white/20 mb-6"></i>
            <h3 class="text-2xl font-semibold text-white/70 mb-4">Keine Gruppen gefunden</h3>
            <p class="text-white/50 mb-8">Erstellen Sie Ihre erste Gruppe um loszulegen!</p>
            <button onclick="document.getElementById('createGroupBtn').click()" class="glass-button px-8 py-4 text-lg font-medium">
              <i class="fas fa-plus mr-2"></i>
              Erste Gruppe erstellen
            </button>
          </div>
        <?php else: ?>
          <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
            <?php foreach ($groups as $group): ?>
              <div class="group-card glass-card p-6 hover:scale-105 transition-all duration-300">
                <!-- Header with actions -->
                <div class="flex justify-between items-start mb-6">
                  <div class="flex-1">
                    <h3 class="text-xl font-bold text-white mb-2 flex items-center">
                      <i class="fas fa-users-rectangle mr-2 text-blue-300"></i>
                      <?= htmlspecialchars($group['name']) ?>
                    </h3>
                    <p class="text-white/70 text-sm leading-relaxed"><?= htmlspecialchars($group['description']) ?></p>
                  </div>
                  
                  <div class="flex space-x-2 ml-4">
                    <button class="edit-group-btn action-btn edit" 
                            data-group-id="<?= $group['id'] ?>"
                            data-name="<?= htmlspecialchars($group['name']) ?>"
                            data-description="<?= htmlspecialchars($group['description']) ?>"
                            title="Gruppe bearbeiten">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="assign-tags-btn action-btn tag"
                            data-group-id="<?= $group['id'] ?>"
                            data-group-name="<?= htmlspecialchars($group['name']) ?>"
                            title="Tags zuweisen">
                      <i class="fas fa-tag"></i>
                    </button>
                    <button class="delete-group-btn action-btn delete"
                            data-group-id="<?= $group['id'] ?>"
                            title="Gruppe löschen">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </div>
                
                <!-- Tags Section -->
                <?php if (!empty($group['tags'])): ?>
                  <div class="mb-6">
                    <div class="flex items-center mb-3">
                      <i class="fas fa-tags text-yellow-400 mr-2"></i>
                      <span class="text-white/80 text-sm font-medium">Tags</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                      <?php foreach ($group['tags'] as $tag): ?>
                        <span class="floating-tag text-xs" style="background: <?= htmlspecialchars($tag['color']) ?>30; border: 1px solid <?= htmlspecialchars($tag['color']) ?>; color: <?= htmlspecialchars($tag['color']) ?>;">
                          <?= htmlspecialchars($tag['name']) ?>
                        </span>
                      <?php endforeach; ?>
                    </div>
                  </div>
                <?php endif; ?>
                
                <!-- Members Section -->
                <div>
                  <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                      <i class="fas fa-user-friends text-purple-400 mr-2"></i>
                      <span class="text-white/80 text-sm font-medium">Mitglieder</span>
                    </div>
                    <span class="bg-white/20 px-3 py-1 rounded-full text-xs font-bold">
                      <?= $group['member_count'] ?>
                    </span>
                  </div>
                  
                  <div class="min-h-[60px] flex items-center">
                    <?php if (empty($group['members'])): ?>
                      <div class="text-center w-full py-4">
                        <i class="fas fa-user-slash text-white/30 text-2xl mb-2"></i>
                        <p class="text-white/40 text-xs">Keine Mitglieder</p>
                      </div>
                    <?php else: ?>
                      <div class="flex flex-wrap gap-2">
                        <?php foreach ($group['members'] as $member): ?>
                          <span class="member-bubble">
                            <i class="fas fa-user mr-1"></i>
                            <?= htmlspecialchars($member['username']) ?>
                          </span>
                        <?php endforeach; ?>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </main>

  <!-- Create Group Modal -->
  <div id="groupModal" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center z-[9999]">
    <div class="modal-content w-full max-w-lg p-8 mx-4 max-h-[90vh] overflow-y-auto">
      <div class="flex justify-between items-center mb-6">
        <h2 id="modalTitle" class="text-2xl font-bold text-white flex items-center">
          <i class="fas fa-users-gear mr-3"></i>
          Neue Gruppe erstellen
        </h2>
        <button id="closeModal" class="action-btn hover:rotate-90 transition-transform duration-300">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <form id="groupForm" method="post" class="space-y-6">
        <input type="hidden" name="action" value="create_group">
        <input type="hidden" id="groupId" name="group_id" value="">
        
        <div>
          <label for="groupName" class="block text-sm font-semibold text-white/90 mb-2">
            <i class="fas fa-signature mr-2"></i>Gruppenname
          </label>
          <input type="text" id="groupName" name="group_name" required class="w-full px-4 py-3 glass-input" placeholder="Team Alpha, Marketing, Entwicklung...">
        </div>
        
        <div>
          <label for="groupDescription" class="block text-sm font-semibold text-white/90 mb-2">
            <i class="fas fa-align-left mr-2"></i>Beschreibung
          </label>
          <textarea id="groupDescription" name="description" rows="3" class="w-full px-4 py-3 glass-input" placeholder="Was macht diese Gruppe besonders?"></textarea>
        </div>
        
        <div>
          <label for="groupMembers" class="block text-sm font-semibold text-white/90 mb-2">
            <i class="fas fa-user-friends mr-2"></i>Mitglieder auswählen
          </label>
          <select id="groupMembers" name="members[]" multiple class="w-full px-4 py-3 glass-input" style="min-height: 120px;">
            <?php foreach ($allUsers as $user): ?>
              <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars($user['email']) ?>)</option>
            <?php endforeach; ?>
          </select>
          <p class="text-xs text-white/60 mt-2">
            <i class="fas fa-info-circle mr-1"></i>
            Mehrere Mitglieder mit Strg+Klick auswählen
          </p>
        </div>
        
        <div class="flex justify-end space-x-4 pt-6 border-t border-white/20">
          <button type="button" id="cancelBtn" class="glass-button px-6 py-3">
            <i class="fas fa-times mr-2"></i>Abbrechen
          </button>
          <button type="submit" class="glass-button px-6 py-3" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.3), rgba(37, 99, 235, 0.3)); border-color: rgba(59, 130, 246, 0.5);">
            <i class="fas fa-save mr-2"></i>Speichern
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Create Tag Modal -->
  <div id="tagModal" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center z-[9999]">
    <div class="modal-content w-full max-w-md p-8 mx-4">
      <div class="flex justify-between items-center mb-6">
        <h2 id="tagModalTitle" class="text-2xl font-bold text-white flex items-center">
          <i class="fas fa-tag mr-3"></i>
          Neuen Tag erstellen
        </h2>
        <button id="closeTagModal" class="action-btn hover:rotate-90 transition-transform duration-300">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <form id="tagForm" method="post" class="space-y-6">
        <input type="hidden" name="action" value="create_tag">
        <input type="hidden" id="tagId" name="tag_id" value="">
        
        <div>
          <label for="tagName" class="block text-sm font-semibold text-white/90 mb-2">
            <i class="fas fa-signature mr-2"></i>Tag-Name
          </label>
          <input type="text" id="tagName" name="tag_name" required class="w-full px-4 py-3 glass-input" placeholder="VIP, Premium, Beta...">
        </div>
        
        <div>
          <label for="tagColor" class="block text-sm font-semibold text-white/90 mb-2">
            <i class="fas fa-palette mr-2"></i>Farbe wählen
          </label>
          <div class="flex space-x-4">
            <input type="color" id="tagColor" name="tag_color" value="#4A90E2" class="h-14 w-14 rounded-xl cursor-pointer border-2 border-white/20">
            <input type="text" id="tagColorText" value="#4A90E2" class="flex-1 px-4 py-3 glass-input font-mono">
          </div>
          <div class="flex space-x-2 mt-3">
            <button type="button" class="color-preset w-8 h-8 rounded-lg cursor-pointer border border-white/20" style="background: #ef4444" data-color="#ef4444"></button>
            <button type="button" class="color-preset w-8 h-8 rounded-lg cursor-pointer border border-white/20" style="background: #f97316" data-color="#f97316"></button>
            <button type="button" class="color-preset w-8 h-8 rounded-lg cursor-pointer border border-white/20" style="background: #eab308" data-color="#eab308"></button>
            <button type="button" class="color-preset w-8 h-8 rounded-lg cursor-pointer border border-white/20" style="background: #22c55e" data-color="#22c55e"></button>
            <button type="button" class="color-preset w-8 h-8 rounded-lg cursor-pointer border border-white/20" style="background: #3b82f6" data-color="#3b82f6"></button>
            <button type="button" class="color-preset w-8 h-8 rounded-lg cursor-pointer border border-white/20" style="background: #8b5cf6" data-color="#8b5cf6"></button>
            <button type="button" class="color-preset w-8 h-8 rounded-lg cursor-pointer border border-white/20" style="background: #ec4899" data-color="#ec4899"></button>
          </div>
        </div>
        
        <div class="flex justify-end space-x-4 pt-6 border-t border-white/20">
          <button type="button" id="cancelTagBtn" class="glass-button px-6 py-3">
            <i class="fas fa-times mr-2"></i>Abbrechen
          </button>
          <button type="submit" class="glass-button px-6 py-3" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.3), rgba(22, 163, 74, 0.3)); border-color: rgba(34, 197, 94, 0.5);">
            <i class="fas fa-save mr-2"></i>Speichern
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Assign Tags Modal -->
  <div id="assignTagsModal" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center z-[9999]">
    <div class="modal-content w-full max-w-lg p-8 mx-4">
      <div class="flex justify-between items-center mb-6">
        <h2 id="assignTagsTitle" class="text-2xl font-bold text-white flex items-center">
          <i class="fas fa-tags mr-3"></i>
          Tags zuweisen
        </h2>
        <button id="closeAssignTagsModal" class="action-btn hover:rotate-90 transition-transform duration-300">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <form id="assignTagsForm" method="post" class="space-y-6">
        <input type="hidden" name="action" value="assign_tags">
        <input type="hidden" id="assignGroupId" name="group_id" value="">
        
        <p class="text-white/70 text-sm mb-4">Wählen Sie die Tags aus, die der Gruppe zugewiesen werden sollen.</p>
        
        <div class="max-h-80 overflow-y-auto p-4 glass-card border border-white/20 rounded-2xl">
          <?php if (empty($allTags)): ?>
            <div class="text-center py-8">
              <i class="fas fa-tag text-4xl text-white/30 mb-4"></i>
              <p class="text-white/50">Keine Tags verfügbar</p>
              <p class="text-white/30 text-sm">Erstellen Sie zuerst Tags</p>
            </div>
          <?php else: ?>
            <?php foreach ($allTags as $tag): ?>
              <div class="flex items-center mb-4 p-3 rounded-xl hover:bg-white/5 transition-all duration-200">
                <input type="checkbox" id="tag-<?= $tag['id'] ?>" name="tag_ids[]" value="<?= $tag['id'] ?>" class="h-5 w-5 text-blue-600 rounded bg-white/10 border-white/20 mr-4">
                <label for="tag-<?= $tag['id'] ?>" class="cursor-pointer flex-1">
                  <span class="floating-tag" style="background: <?= htmlspecialchars($tag['color']) ?>40; border: 2px solid <?= htmlspecialchars($tag['color']) ?>; color: white;">
                    <?= htmlspecialchars($tag['name']) ?>
                  </span>
                </label>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
        
        <div class="flex justify-end space-x-4 pt-6 border-t border-white/20">
          <button type="button" id="cancelAssignTagsBtn" class="glass-button px-6 py-3">
            <i class="fas fa-times mr-2"></i>Abbrechen
          </button>
          <button type="submit" class="glass-button px-6 py-3" style="background: linear-gradient(135deg, rgba(147, 51, 234, 0.3), rgba(126, 34, 206, 0.3)); border-color: rgba(147, 51, 234, 0.5);">
            <i class="fas fa-check mr-2"></i>Tags zuweisen
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Group Modal -->
  <div id="deleteModal" class="fixed inset-0 bg-black/60 backdrop-blur-md hidden items-center justify-center z-[9999]">
    <div class="modal-content w-full max-w-md p-8 mx-4">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-white flex items-center">
          <i class="fas fa-exclamation-triangle mr-3 text-red-400"></i>
          Gruppe löschen
        </h2>
        <button id="closeDeleteModal" class="action-btn hover:rotate-90 transition-transform duration-300">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <p class="mb-8 text-white/80 text-center">
        Sind Sie sicher, dass Sie diese Gruppe löschen möchten?<br>
        <span class="text-red-400 font-semibold">Diese Aktion kann nicht rückgängig gemacht werden.</span>
      </p>
      
      <form method="post">
        <input type="hidden" name="action" value="delete_group">
        <input type="hidden" id="deleteGroupId" name="group_id" value="">
        
        <div class="flex justify-end space-x-4">
          <button type="button" id="cancelDeleteBtn" class="glass-button px-6 py-3">
            <i class="fas fa-times mr-2"></i>Abbrechen
          </button>
          <button type="submit" class="glass-button px-6 py-3" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.3), rgba(220, 38, 38, 0.3)); border-color: rgba(239, 68, 68, 0.5);">
            <i class="fas fa-trash mr-2"></i>Löschen
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Enhanced modal management with animations
    const modals = {
      group: document.getElementById('groupModal'),
      tag: document.getElementById('tagModal'),
      assignTags: document.getElementById('assignTagsModal'),
      delete: document.getElementById('deleteModal')
    };
    
    // Utility functions
    function openModal(modalName) {
      const modal = modals[modalName];
      modal.classList.remove('hidden');
      modal.classList.add('flex');
      modal.style.animation = 'fadeIn 0.3s ease';
    }
    
    function closeModal(modalName) {
      const modal = modals[modalName];
      modal.style.animation = 'fadeOut 0.3s ease';
      setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }, 300);
    }
    
    // Group Modal Events
    document.getElementById('createGroupBtn').addEventListener('click', () => {
      document.getElementById('modalTitle').innerHTML = '<i class="fas fa-users-gear mr-3"></i>Neue Gruppe erstellen';
      document.getElementById('groupForm').reset();
      document.getElementById('groupForm').action.value = 'create_group';
      document.getElementById('groupId').value = '';
      openModal('group');
    });
    
    document.querySelectorAll('.edit-group-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const groupId = btn.dataset.groupId;
        const name = btn.dataset.name;
        const description = btn.dataset.description;
        
        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit mr-3"></i>Gruppe bearbeiten';
        document.getElementById('groupForm').action.value = 'update_group';
        document.getElementById('groupId').value = groupId;
        document.getElementById('groupName').value = name;
        document.getElementById('groupDescription').value = description;
        openModal('group');
      });
    });
    
    // Tag Modal Events
    document.getElementById('createTagBtn').addEventListener('click', () => {
      document.getElementById('tagModalTitle').innerHTML = '<i class="fas fa-tag mr-3"></i>Neuen Tag erstellen';
      document.getElementById('tagForm').reset();
      document.getElementById('tagForm').action.value = 'create_tag';
      document.getElementById('tagId').value = '';
      document.getElementById('tagColor').value = '#4A90E2';
      document.getElementById('tagColorText').value = '#4A90E2';
      openModal('tag');
    });
    
    document.querySelectorAll('.edit-tag-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        
        const tagId = btn.dataset.tagId;
        const tagName = btn.dataset.tagName;
        const tagColor = btn.dataset.tagColor;
        
        document.getElementById('tagModalTitle').innerHTML = '<i class="fas fa-edit mr-3"></i>Tag bearbeiten';
        document.getElementById('tagForm').action.value = 'update_tag';
        document.getElementById('tagId').value = tagId;
        document.getElementById('tagName').value = tagName;
        document.getElementById('tagColor').value = tagColor;
        document.getElementById('tagColorText').value = tagColor;
        openModal('tag');
      });
    });
    
    // Color preset functionality
    document.querySelectorAll('.color-preset').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        const color = btn.dataset.color;
        document.getElementById('tagColor').value = color;
        document.getElementById('tagColorText').value = color;
      });
    });
    
    // Color input synchronization
    document.getElementById('tagColor').addEventListener('input', (e) => {
      document.getElementById('tagColorText').value = e.target.value;
    });
    
    document.getElementById('tagColorText').addEventListener('input', (e) => {
      if (/^#[0-9A-F]{6}$/i.test(e.target.value)) {
        document.getElementById('tagColor').value = e.target.value;
      }
    });
    
    // Assign Tags Modal
    document.querySelectorAll('.assign-tags-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const groupId = btn.dataset.groupId;
        const groupName = btn.dataset.groupName;
        
        document.getElementById('assignTagsTitle').innerHTML = `<i class="fas fa-tags mr-3"></i>Tags für "${groupName}" zuweisen`;
        document.getElementById('assignGroupId').value = groupId;
        
        // Reset checkboxes
        document.querySelectorAll('#assignTagsForm input[type="checkbox"]').forEach(cb => {
          cb.checked = false;
        });
        
        openModal('assignTags');
      });
    });
    
    // Delete Modal
    document.querySelectorAll('.delete-group-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.getElementById('deleteGroupId').value = btn.dataset.groupId;
        openModal('delete');
      });
    });
    
    // Close modal events
    const closeButtons = [
      ['closeModal', 'group'],
      ['cancelBtn', 'group'],
      ['closeTagModal', 'tag'],
      ['cancelTagBtn', 'tag'],
      ['closeAssignTagsModal', 'assignTags'],
      ['cancelAssignTagsBtn', 'assignTags'],
      ['closeDeleteModal', 'delete'],
      ['cancelDeleteBtn', 'delete']
    ];
    
    closeButtons.forEach(([buttonId, modalName]) => {
      document.getElementById(buttonId)?.addEventListener('click', () => {
        closeModal(modalName);
      });
    });
    
    // Close on background click
    Object.values(modals).forEach(modal => {
      modal.addEventListener('click', (e) => {
        if (e.target === modal) {
          const modalName = Object.keys(modals).find(key => modals[key] === modal);
          closeModal(modalName);
        }
      });
    });
    
    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
      @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
      }
      @keyframes fadeOut {
        from { opacity: 1; transform: scale(1); }
        to { opacity: 0; transform: scale(0.9); }
      }
    `;
    document.head.appendChild(style);
    
    // Auto-hide alerts
    setTimeout(() => {
      document.querySelectorAll('.success-alert, .error-alert').forEach(alert => {
        alert.style.animation = 'slideOutUp 0.5s ease forwards';
        setTimeout(() => alert.remove(), 500);
      });
    }, 5000);
  </script>
</body>
</html>
