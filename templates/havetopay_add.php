<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ausgabe hinzufügen | HaveToPay</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #2d1b69 0%, #11101d 50%, #1a0909 100%);
            min-height: 100vh;
        }
        
        .user-checkbox, .group-checkbox {
            display: none;
        }
        
        .user-label, .group-label {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .user-checkbox:checked + .user-label,
        .group-checkbox:checked + .group-label {
            background: rgba(59, 130, 246, 0.2);
            border-color: rgba(59, 130, 246, 0.5);
        }
        
        .search-box {
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .user-item, .group-item {
            transition: all 0.3s ease;
        }
        
        .user-item.hidden, .group-item.hidden {
            display: none;
        }
        
        .selection-mode {
            border: 2px solid rgba(59, 130, 246, 0.5);
            background: rgba(59, 130, 246, 0.1);
        }
    </style>
</head>
<body class="min-h-screen flex">
    <?php include_once __DIR__ . '/navbar.php'; ?>

    <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Back Link -->
            <div class="mb-6">
                <a href="havetopay.php" class="inline-flex items-center text-blue-400 hover:text-blue-300 font-medium group transition-colors">
                    <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>Zurück zu den Ausgaben
                </a>
            </div>
            
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-white/90 mb-2">Neue Ausgabe erstellen</h1>
                <p class="text-white/60">Teile Kosten mit Freunden und behalte den Überblick über offene Beträge</p>
            </div>
            
            <!-- Error and Success Messages -->
            <?php if (!empty($errors)): ?>
                <div class="bg-red-500/20 border border-red-400/30 backdrop-blur-sm text-red-300 p-4 rounded-xl mb-6">
                    <div class="flex">
                        <i class="fas fa-exclamation-circle text-xl mr-3 mt-0.5"></i>
                        <div>
                            <strong>Bitte beheben Sie folgende Fehler:</strong>
                            <ul class="list-disc list-inside mt-2 space-y-1">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="bg-green-500/20 border border-green-400/30 backdrop-blur-sm text-green-300 p-4 rounded-xl mb-6 flex items-center">
                    <i class="fas fa-check-circle text-xl mr-3"></i>
                    <p><?php echo htmlspecialchars($success); ?></p>
                </div>
            <?php endif; ?>
            
            <!-- Add Expense Form -->
            <form action="" method="POST" class="space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column: Basic Info -->
                    <div class="lg:col-span-2">
                        <div class="bg-gradient-to-br from-white/10 via-white/5 to-white/8 backdrop-blur-xl rounded-2xl border border-white/10 overflow-hidden">
                            <div class="bg-white/10 backdrop-blur-sm px-6 py-4 border-b border-white/10">
                                <h2 class="text-lg font-bold flex items-center text-white/90">
                                    <i class="fas fa-info-circle mr-3"></i>Ausgaben-Details
                                </h2>
                            </div>
                            <div class="p-6 space-y-6">
                                <!-- Title & Amount -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label for="title" class="block text-sm font-medium text-white/80">Beschreibung *</label>
                                        <input type="text" id="title" name="title" required
                                               class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400/50 transition-all backdrop-blur-sm text-white/90 placeholder-white/40"
                                               placeholder="z.B. Restaurantbesuch, Einkauf, Kinokarten..."
                                               value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="space-y-2">
                                        <label for="amount" class="block text-sm font-medium text-white/80">Gesamtbetrag *</label>
                                        <div class="relative">
                                            <span class="absolute left-4 top-3 text-white/50">€</span>
                                            <input type="number" id="amount" name="amount" step="0.01" min="0.01" required
                                                   class="w-full pl-8 pr-4 py-3 bg-white/5 border border-white/20 rounded-xl focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400/50 transition-all backdrop-blur-sm text-white/90 placeholder-white/40"
                                                   placeholder="0,00"
                                                   value="<?php echo htmlspecialchars($_POST['amount'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Date & Category -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label for="expense_date" class="block text-sm font-medium text-white/80">Datum</label>
                                        <input type="date" id="expense_date" name="expense_date"
                                               class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400/50 transition-all backdrop-blur-sm text-white/90"
                                               value="<?php echo htmlspecialchars($_POST['expense_date'] ?? date('Y-m-d')); ?>">
                                    </div>
                                    
                                    <div class="space-y-2">
                                        <label for="category" class="block text-sm font-medium text-white/80">Kategorie</label>
                                        <select id="category" name="category"
                                                class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400/50 transition-all backdrop-blur-sm text-white/90">
                                            <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo htmlspecialchars($category['name']); ?>"
                                                    <?php echo ($_POST['category'] ?? '') == $category['name'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Description -->
                                <div class="space-y-2">
                                    <label for="description" class="block text-sm font-medium text-white/80">Zusätzliche Notizen</label>
                                    <textarea id="description" name="description" rows="3"
                                              class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400/50 transition-all backdrop-blur-sm text-white/90 placeholder-white/40"
                                              placeholder="Weitere Details zu dieser Ausgabe (optional)..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column: Participants -->
                    <div class="space-y-6">
                        <!-- Selection Mode Toggle -->
                        <div class="bg-gradient-to-br from-purple-900/20 via-gray-900/30 to-red-900/20 backdrop-blur-xl rounded-2xl border border-white/10 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-600/30 via-blue-700/40 to-blue-800/30 backdrop-blur-sm px-4 py-3 border-b border-white/10">
                                <h3 class="text-sm font-bold text-white/90 flex items-center">
                                    <i class="fas fa-users mr-2"></i>Teilnehmer auswählen
                                </h3>
                            </div>
                            <div class="p-4">
                                <div class="flex gap-2 mb-4">
                                    <button type="button" id="toggleUsers" class="flex-1 px-3 py-2 bg-blue-500/20 border border-blue-400/30 text-blue-300 rounded-lg text-sm font-medium hover:bg-blue-500/30 transition-all">
                                        <i class="fas fa-user mr-1"></i>Einzelpersonen
                                    </button>
                                    <button type="button" id="toggleGroups" class="flex-1 px-3 py-2 bg-white/5 border border-white/20 text-white/60 rounded-lg text-sm font-medium hover:bg-white/10 transition-all">
                                        <i class="fas fa-users mr-1"></i>Gruppen
                                    </button>
                                </div>
                                
                                <!-- Search Box -->
                                <div class="search-box bg-white/5 border border-white/20 rounded-lg p-3 mb-4">
                                    <div class="relative">
                                        <i class="fas fa-search absolute left-3 top-3 text-white/40"></i>
                                        <input type="text" id="searchInput" placeholder="Suchen..." 
                                               class="w-full pl-10 pr-4 py-2 bg-transparent border-none text-white/90 placeholder-white/40 focus:outline-none text-sm">
                                    </div>
                                </div>
                                
                                <!-- Quick Actions -->
                                <div class="flex gap-2 mb-4">
                                    <button type="button" id="selectAll" class="flex-1 px-3 py-2 bg-green-500/20 border border-green-400/30 text-green-300 rounded-lg text-sm font-medium hover:bg-green-500/30 transition-all">
                                        <i class="fas fa-check-square mr-1"></i>Alle
                                    </button>
                                    <button type="button" id="clearAll" class="flex-1 px-3 py-2 bg-red-500/20 border border-red-400/30 text-red-300 rounded-lg text-sm font-medium hover:bg-red-500/30 transition-all">
                                        <i class="fas fa-times mr-1"></i>Keine
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Users Selection -->
                        <div id="usersSection" class="bg-gradient-to-br from-purple-900/20 via-gray-900/30 to-red-900/20 backdrop-blur-xl rounded-2xl border border-white/10 overflow-hidden">
                            <div class="bg-gradient-to-r from-purple-600/30 via-indigo-700/40 to-purple-800/30 backdrop-blur-sm px-4 py-3 border-b border-white/10">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-sm font-bold text-white/90 flex items-center">
                                        <i class="fas fa-user-friends mr-2"></i>Verfügbare Personen
                                    </h3>
                                    <span id="userSelectedCount" class="bg-white/10 border border-white/20 text-white/80 px-2 py-1 rounded-full text-xs font-semibold backdrop-blur-sm">
                                        0 ausgewählt
                                    </span>
                                </div>
                            </div>
                            <div class="p-4 max-h-80 overflow-y-auto">
                                <?php if (empty($allUsersIncludingMe)): ?>
                                    <div class="text-center py-6">
                                        <i class="fas fa-users text-2xl text-white/20 mb-2"></i>
                                        <p class="text-white/50 text-sm">Keine anderen Benutzer gefunden</p>
                                    </div>
                                <?php else: ?>
                                    <div class="space-y-2" id="userList">
                                        <?php foreach ($allUsersIncludingMe as $user): ?>
                                        <div class="user-item" data-name="<?= strtolower($user['display_name']) ?>" data-username="<?= strtolower($user['username']) ?>">
                                            <input type="checkbox" 
                                                   id="user_<?php echo $user['id']; ?>" 
                                                   name="participants[]" 
                                                   value="<?php echo $user['id']; ?>"
                                                   data-user-id="<?php echo $user['id']; ?>"
                                                   class="user-checkbox"
                                                   <?php echo $user['id'] == $userId ? 'checked disabled' : ''; ?>
                                                   <?php echo in_array($user['id'], $_POST['participants'] ?? []) ? 'checked' : ''; ?>>
                                            
                                            <label for="user_<?php echo $user['id']; ?>" 
                                                   class="user-label block p-3 bg-white/5 border border-white/10 rounded-lg hover:bg-white/10 transition-all backdrop-blur-sm <?php echo $user['id'] == $userId ? 'opacity-60' : ''; ?>">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 text-white rounded-lg flex items-center justify-center font-semibold mr-3 text-xs flex-shrink-0">
                                                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="font-medium text-white/90 text-sm truncate">
                                                            <?php echo htmlspecialchars($user['display_name']); ?>
                                                            <?php if ($user['id'] == $userId): ?>
                                                                <span class="text-blue-400">(Du)</span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="text-xs text-white/50 truncate">
                                                            @<?php echo htmlspecialchars($user['username']); ?>
                                                        </div>
                                                    </div>
                                                    <div class="ml-2">
                                                        <i class="fas fa-check text-blue-400 opacity-0 transition-opacity check-icon"></i>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Groups Selection -->
                        <div id="groupsSection" class="bg-gradient-to-br from-purple-900/20 via-gray-900/30 to-red-900/20 backdrop-blur-xl rounded-2xl border border-white/10 overflow-hidden hidden">
                            <div class="bg-gradient-to-r from-green-600/30 via-green-700/40 to-green-800/30 backdrop-blur-sm px-4 py-3 border-b border-white/10">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-sm font-bold text-white/90 flex items-center">
                                        <i class="fas fa-users mr-2"></i>Verfügbare Gruppen
                                    </h3>
                                    <span id="groupSelectedCount" class="bg-white/10 border border-white/20 text-white/80 px-2 py-1 rounded-full text-xs font-semibold backdrop-blur-sm">
                                        0 ausgewählt
                                    </span>
                                </div>
                            </div>
                            <div class="p-4 max-h-80 overflow-y-auto">
                                <?php if (empty($allGroups)): ?>
                                    <div class="text-center py-6">
                                        <i class="fas fa-users text-2xl text-white/20 mb-2"></i>
                                        <p class="text-white/50 text-sm mb-3">Keine Gruppen gefunden</p>
                                        <a href="groups.php" class="text-blue-400 hover:text-blue-300 text-sm">
                                            <i class="fas fa-plus mr-1"></i>Erste Gruppe erstellen
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="space-y-2" id="groupList">
                                        <?php foreach ($allGroups as $group): ?>
                                        <div class="group-item" data-name="<?= strtolower($group['name']) ?>">
                                            <input type="checkbox" 
                                                   id="group_<?php echo $group['id']; ?>" 
                                                   data-group-id="<?php echo $group['id']; ?>"
                                                   data-members="<?php echo htmlspecialchars(json_encode($group['members'] ?? [])); ?>"
                                                   class="group-checkbox">
                                            
                                            <label for="group_<?php echo $group['id']; ?>" 
                                                   class="group-label block p-3 bg-white/5 border border-white/10 rounded-lg hover:bg-white/10 transition-all backdrop-blur-sm">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-teal-600 text-white rounded-lg flex items-center justify-center font-semibold mr-3 text-xs flex-shrink-0">
                                                        <i class="fas fa-users"></i>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="font-medium text-white/90 text-sm truncate">
                                                            <?php echo htmlspecialchars($group['name']); ?>
                                                        </div>
                                                        <div class="text-xs text-white/50 truncate">
                                                            <?php echo isset($group['member_count']) ? $group['member_count'] : count($group['members']); ?> Mitglieder
                                                        </div>
                                                    </div>
                                                    <div class="ml-2">
                                                        <i class="fas fa-check text-green-400 opacity-0 transition-opacity check-icon"></i>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Selected Summary & Submit -->
                        <div class="bg-gradient-to-br from-purple-900/20 via-gray-900/30 to-red-900/20 backdrop-blur-xl rounded-2xl border border-white/10 p-4">
                            <div class="mb-4 p-3 bg-blue-500/10 border border-blue-400/20 rounded-lg">
                                <div class="text-sm text-blue-300 font-medium mb-2">
                                    <i class="fas fa-calculator mr-1"></i>Aufgeteilte Kosten
                                </div>
                                <div class="text-xs text-blue-200/70" id="costBreakdown">
                                    Wähle Teilnehmer aus, um die Kostenaufteilung zu sehen
                                </div>
                            </div>
                            
                            <button type="submit"
                                    id="submitButton"
                                    class="w-full bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/20 hover:border-white/30 text-white/90 hover:text-white px-6 py-3 rounded-xl font-semibold transition-all shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-plus-circle mr-2"></i>Ausgabe erstellen
                            </button>
                            
                            <div class="mt-3 text-xs text-white/50 text-center">
                                Die Kosten werden gleichmäßig zwischen allen Teilnehmern aufgeteilt
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const userCheckboxes = document.querySelectorAll('.user-checkbox');
                const groupCheckboxes = document.querySelectorAll('.group-checkbox');
                const userSelectedCount = document.getElementById('userSelectedCount');
                const groupSelectedCount = document.getElementById('groupSelectedCount');
                const submitButton = document.getElementById('submitButton');
                const searchInput = document.getElementById('searchInput');
                const toggleUsers = document.getElementById('toggleUsers');
                const toggleGroups = document.getElementById('toggleGroups');
                const usersSection = document.getElementById('usersSection');
                const groupsSection = document.getElementById('groupsSection');
                const selectAll = document.getElementById('selectAll');
                const clearAll = document.getElementById('clearAll');
                const costBreakdown = document.getElementById('costBreakdown');
                const amountInput = document.getElementById('amount');
                
                let currentMode = 'users';
                
                // Toggle between users and groups
                toggleUsers.addEventListener('click', function() {
                    currentMode = 'users';
                    usersSection.classList.remove('hidden');
                    groupsSection.classList.add('hidden');
                    toggleUsers.classList.add('bg-blue-500/20', 'border-blue-400/30', 'text-blue-300');
                    toggleUsers.classList.remove('bg-white/5', 'border-white/20', 'text-white/60');
                    toggleGroups.classList.remove('bg-green-500/20', 'border-green-400/30', 'text-green-300');
                    toggleGroups.classList.add('bg-white/5', 'border-white/20', 'text-white/60');
                    updateSearch();
                });
                
                toggleGroups.addEventListener('click', function() {
                    currentMode = 'groups';
                    usersSection.classList.add('hidden');
                    groupsSection.classList.remove('hidden');
                    toggleGroups.classList.add('bg-green-500/20', 'border-green-400/30', 'text-green-300');
                    toggleGroups.classList.remove('bg-white/5', 'border-white/20', 'text-white/60');
                    toggleUsers.classList.remove('bg-blue-500/20', 'border-blue-400/30', 'text-blue-300');
                    toggleUsers.classList.add('bg-white/5', 'border-white/20', 'text-white/60');
                    updateSearch();
                });
                
                // Search functionality
                searchInput.addEventListener('input', updateSearch);
                
                function updateSearch() {
                    const query = searchInput.value.toLowerCase();
                    const items = currentMode === 'users' ? 
                        document.querySelectorAll('.user-item') : 
                        document.querySelectorAll('.group-item');
                    
                    items.forEach(item => {
                        const name = item.dataset.name || '';
                        const username = item.dataset.username || '';
                        const matches = name.includes(query) || username.includes(query);
                        item.classList.toggle('hidden', !matches);
                    });
                }
                
                // Select/Clear all functionality
                selectAll.addEventListener('click', function() {
                    if (currentMode === 'users') {
                        userCheckboxes.forEach(checkbox => {
                            if (!checkbox.disabled && !checkbox.closest('.user-item').classList.contains('hidden')) {
                                checkbox.checked = true;
                            }
                        });
                    } else {
                        groupCheckboxes.forEach(checkbox => {
                            if (!checkbox.closest('.group-item').classList.contains('hidden')) {
                                checkbox.checked = true;
                                selectGroupMembers(checkbox);
                            }
                        });
                    }
                    updateCounts();
                    updateCostBreakdown();
                });
                
                clearAll.addEventListener('click', function() {
                    if (currentMode === 'users') {
                        userCheckboxes.forEach(checkbox => {
                            if (!checkbox.disabled) {
                                checkbox.checked = false;
                            }
                        });
                    } else {
                        groupCheckboxes.forEach(checkbox => {
                            checkbox.checked = false;
                        });
                        // Clear user selections from groups
                        userCheckboxes.forEach(checkbox => {
                            if (!checkbox.disabled) {
                                checkbox.checked = false;
                            }
                        });
                    }
                    updateCounts();
                    updateCostBreakdown();
                });
                
                // Group selection functionality
                groupCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        if (this.checked) {
                            selectGroupMembers(this);
                        }
                        updateCounts();
                        updateCostBreakdown();
                    });
                });
                
                function selectGroupMembers(groupCheckbox) {
                    try {
                        const memberIds = JSON.parse(groupCheckbox.dataset.members || '[]');
                        memberIds.forEach(memberId => {
                            const userCheckbox = document.querySelector(`input[data-user-id="${memberId}"]`);
                            if (userCheckbox && !userCheckbox.disabled) {
                                userCheckbox.checked = true;
                            }
                        });
                    } catch (e) {
                        console.error('Error parsing group members:', e);
                    }
                }
                
                // User selection functionality
                userCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', updateCounts);
                    checkbox.addEventListener('change', updateCostBreakdown);
                });
                
                function updateCounts() {
                    const checkedUsers = document.querySelectorAll('.user-checkbox:checked').length;
                    const checkedGroups = document.querySelectorAll('.group-checkbox:checked').length;
                    
                    userSelectedCount.textContent = `${checkedUsers} ausgewählt`;
                    groupSelectedCount.textContent = `${checkedGroups} ausgewählt`;
                    
                    // Enable/disable submit button
                    submitButton.disabled = checkedUsers === 0;
                    
                    // Update visual states
                    userCheckboxes.forEach(checkbox => {
                        const label = checkbox.nextElementSibling;
                        const checkIcon = label.querySelector('.check-icon');
                        
                        if (checkbox.checked) {
                            checkIcon.style.opacity = '1';
                            label.classList.add('selection-mode');
                        } else {
                            checkIcon.style.opacity = '0';
                            label.classList.remove('selection-mode');
                        }
                    });
                    
                    groupCheckboxes.forEach(checkbox => {
                        const label = checkbox.nextElementSibling;
                        const checkIcon = label.querySelector('.check-icon');
                        
                        if (checkbox.checked) {
                            checkIcon.style.opacity = '1';
                            label.classList.add('selection-mode');
                        } else {
                            checkIcon.style.opacity = '0';
                            label.classList.remove('selection-mode');
                        }
                    });
                }
                
                function updateCostBreakdown() {
                    const checkedUsers = document.querySelectorAll('.user-checkbox:checked').length;
                    const amount = parseFloat(amountInput.value) || 0;
                    
                    if (checkedUsers === 0) {
                        costBreakdown.textContent = 'Wähle Teilnehmer aus, um die Kostenaufteilung zu sehen';
                    } else if (amount === 0) {
                        costBreakdown.textContent = `${checkedUsers} Teilnehmer ausgewählt - Gib einen Betrag ein`;
                    } else {
                        const perPerson = (amount / checkedUsers).toFixed(2);
                        costBreakdown.textContent = `${checkedUsers} Teilnehmer × ${perPerson}€ = ${amount.toFixed(2)}€ gesamt`;
                    }
                }
                
                // Update cost breakdown when amount changes
                amountInput.addEventListener('input', updateCostBreakdown);
                
                // Initial setup
                updateCounts();
                updateCostBreakdown();
                
                // Form validation
                document.querySelector('form').addEventListener('submit', function(e) {
                    const checkedUsers = document.querySelectorAll('.user-checkbox:checked').length;
                    if (checkedUsers === 0) {
                        e.preventDefault();
                        alert('Bitte wählen Sie mindestens einen Teilnehmer aus.');
                    }
                });
            });
        </script>
    </main>
</body>
</html>
