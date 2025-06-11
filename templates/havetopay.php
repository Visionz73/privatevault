<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schuldenverwaltung | PrivateVault</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }
        
        /* Scale up main content by 10% */
        main {
            transform: scale(1.1);
            transform-origin: top left;
            width: 90.9%; /* Compensate for scaling (100/1.1) */
        }
        
        @media (max-width: 768px) {
            main {
                transform: scale(1.1);
                width: 90.9%;
            }
        }
        
        .glass-container {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 0.8rem;
            box-shadow: 0 6px 26px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }
        .glass-container:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.2);
        }
        
        .glass-header {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.1) 100%);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .glass-item {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.4rem;
            transition: all 0.3s ease;
        }
        .glass-item:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }
        
        .glass-btn {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            transition: all 0.3s ease;
        }
        .glass-btn:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            color: white;
        }
        
        .glass-modal {
            background: linear-gradient(135deg, #2d1b69 0%, #11101d 100%);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.8rem;
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.3);
        }

        .glass-select {
            background: #374151 !important;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 0.6rem;
            color: white !important;
            backdrop-filter: blur(10px);
        }
        .glass-select option {
            background: #374151 !important;
            color: white !important;
        }
        .glass-select:focus {
            background: #4b5563 !important;
            border-color: rgba(255, 255, 255, 0.4);
        }
    </style>
</head>
<body class="min-h-screen flex">
    <?php include_once __DIR__ . '/navbar.php'; ?>

    <main class="ml-0 mt-16 md:ml-64 md:mt-0 flex-1 p-3 md:p-5">
        <div class="max-w-5xl mx-auto">
            <!-- Compact Header -->
            <div class="flex justify-between items-center mb-5">
                <h1 class="text-xl font-bold text-white">Schuldenverwaltung</h1>
                <a href="havetopay_add.php" class="glass-btn px-2 py-1.5 rounded-lg text-xs flex items-center" title="Ausgabe hinzufügen">
                    <i class="fas fa-plus"></i>
                </a>
            </div>

            <!-- Success/Error Messages -->
            <?php if (!empty($successMessage)): ?>
            <div class="glass-container p-2.5 mb-3 flex items-center text-green-300">
                <i class="fas fa-check-circle mr-1.5 text-sm"></i>
                <p class="text-xs"><?php echo htmlspecialchars($successMessage); ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($errorMessage)): ?>
            <div class="glass-container p-2.5 mb-3 flex items-center text-red-300">
                <i class="fas fa-exclamation-circle mr-1.5 text-sm"></i>
                <p class="text-xs"><?php echo htmlspecialchars($errorMessage); ?></p>
            </div>
            <?php endif; ?>

            <!-- Balance Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-5">
                <div class="glass-container p-3 text-center">
                    <div class="text-white/60 text-xs mb-0.5">Du bekommst</div>
                    <div class="text-lg font-bold text-green-400"><?php echo number_format($totalOwed, 2); ?> €</div>
                </div>
                
                <div class="glass-container p-3 text-center">
                    <div class="text-white/60 text-xs mb-0.5">Netto-Saldo</div>
                    <div class="text-lg font-bold <?php echo $netBalance >= 0 ? 'text-green-400' : 'text-red-400'; ?>">
                        <?php echo number_format($netBalance, 2); ?> €
                    </div>
                </div>
                
                <div class="glass-container p-3 text-center">
                    <div class="text-white/60 text-xs mb-0.5">Du schuldest</div>
                    <div class="text-lg font-bold text-red-400"><?php echo number_format($totalOwing, 2); ?> €</div>
                </div>
            </div>

            <!-- Balances Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
                <!-- People who owe you -->
                <div class="glass-container">
                    <div class="glass-header px-3 py-2.5 text-white">
                        <h3 class="text-xs font-semibold">Personen die dir Geld schulden (<?php echo count($balances['others_owe']); ?>)</h3>
                    </div>
                    <div class="p-3">
                        <?php if (empty($balances['others_owe'])): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle text-2xl text-white/20 mb-1.5"></i>
                                <p class="text-white/60 text-xs">Niemand schuldet dir Geld</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-1.5">
                                <?php foreach ($balances['others_owe'] as $balance): ?>
                                <div class="glass-item p-2.5 flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-7 h-7 bg-gradient-to-br from-green-400 to-green-500 text-white rounded-full flex items-center justify-center text-xs font-semibold mr-2">
                                            <?php echo strtoupper(substr($balance['username'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="text-white text-xs font-medium"><?php echo htmlspecialchars($balance['display_name']); ?></div>
                                            <div class="text-white/60 text-xs">@<?php echo htmlspecialchars($balance['username']); ?></div>
                                        </div>
                                    </div>
                                    <span class="text-green-300 text-xs font-semibold">
                                        <?php echo number_format($balance['amount_owed'], 2); ?> €
                                    </span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- People you owe -->
                <div class="glass-container">
                    <div class="glass-header px-3 py-2.5 text-white">
                        <h3 class="text-xs font-semibold">Personen denen du Geld schuldest (<?php echo count($balances['user_owes']); ?>)</h3>
                    </div>
                    <div class="p-3">
                        <?php if (empty($balances['user_owes'])): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-smile text-2xl text-white/20 mb-1.5"></i>
                                <p class="text-white/60 text-xs">Du schuldest niemandem Geld</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-1.5">
                                <?php foreach ($balances['user_owes'] as $balance): ?>
                                <div class="glass-item p-2.5 flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-7 h-7 bg-gradient-to-br from-red-400 to-red-500 text-white rounded-full flex items-center justify-center text-xs font-semibold mr-2">
                                            <?php echo strtoupper(substr($balance['username'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="text-white text-xs font-medium"><?php echo htmlspecialchars($balance['display_name']); ?></div>
                                            <div class="text-white/60 text-xs">@<?php echo htmlspecialchars($balance['username']); ?></div>
                                        </div>
                                    </div>
                                    <span class="text-red-300 text-xs font-semibold">
                                        <?php echo number_format($balance['amount_owed'], 2); ?> €
                                    </span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Expenses with Filter -->
            <div class="glass-container">
                <div class="glass-header px-3 py-2.5 text-white">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xs font-semibold">Aktuelle Ausgaben (<?php echo count($filteredExpenses); ?>)</h3>
                        <div class="flex items-center gap-1.5">
                            <form method="GET" class="flex items-center gap-1.5">
                                <label for="filter_user" class="text-xs text-white/70">Filter:</label>
                                <select name="filter_user" id="filter_user" onchange="this.form.submit()" 
                                        class="bg-gray-800 border border-white/20 rounded px-1.5 py-0.5 text-xs text-white focus:bg-gray-700 focus:border-white/40">
                                    <option value="own" <?php echo ($expenseFilter === 'own') ? 'selected' : ''; ?>>
                                        Meine Ausgaben
                                    </option>
                                    <option value="all" <?php echo ($expenseFilter === 'all') ? 'selected' : ''; ?>>
                                        Alle Ausgaben
                                    </option>
                                    <option value="participating" <?php echo ($expenseFilter === 'participating') ? 'selected' : ''; ?>>
                                        Beteiligte Ausgaben
                                    </option>
                                    <?php foreach ($allUsers as $user): ?>
                                        <option value="<?php echo $user['id']; ?>" 
                                                <?php echo ($expenseFilter === (string)$user['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($user['display_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="p-3">
                    <?php if (empty($filteredExpenses)): ?>
                        <div class="text-center py-6">
                            <i class="fas fa-receipt text-3xl text-white/20 mb-2"></i>
                            <p class="text-white/60 mb-3 text-xs">Keine Ausgaben gefunden</p>
                            <a href="havetopay_add.php" class="glass-btn py-1.5 px-3 rounded-lg text-xs">
                                <i class="fas fa-plus mr-1.5"></i>Erste Ausgabe hinzufügen
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="space-y-1.5">
                            <?php foreach ($filteredExpenses as $expense): ?>
                            <div class="glass-item p-2.5">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start mb-1.5">
                                            <div>
                                                <div class="text-white font-medium text-xs"><?php echo htmlspecialchars($expense['title']); ?></div>
                                                <div class="text-white/60 text-xs">
                                                    Von <?php echo htmlspecialchars($expense['payer_display_name']); ?> • 
                                                    <?php echo date('d.m.Y', strtotime($expense['expense_date'])); ?> • 
                                                    <?php echo $expense['participant_count']; ?> Personen
                                                </div>
                                            </div>
                                            <div class="text-white font-semibold text-xs"><?php echo number_format($expense['amount'], 2); ?> €</div>
                                        </div>
                                        <div class="flex gap-1.5">
                                            <a href="havetopay_detail.php?id=<?php echo $expense['id']; ?>" 
                                               class="text-blue-400 hover:text-blue-300 text-xs">
                                                <i class="fas fa-eye mr-0.5"></i>Details
                                            </a>
                                            <?php if ($expense['payer_id'] == $userId || ($_SESSION['is_admin'] ?? false)): ?>
                                                <button type="button" 
                                                        class="text-red-400 hover:text-red-300 text-xs"
                                                        onclick="confirmDelete(<?php echo $expense['id']; ?>, '<?php echo htmlspecialchars($expense['title'], ENT_QUOTES); ?>')">
                                                    <i class="fas fa-trash mr-0.5"></i>Löschen
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 backdrop-blur-sm">
        <div class="glass-modal p-5 max-w-md mx-3">
            <div class="flex items-center mb-3">
                <div class="w-8 h-8 bg-red-500/20 text-red-400 rounded-full flex items-center justify-center mr-2">
                    <i class="fas fa-exclamation-triangle text-sm"></i>
                </div>
                <h3 class="text-base font-semibold text-white">Löschen bestätigen</h3>
            </div>
            <p class="text-white/70 mb-4 text-sm">Bist du sicher, dass du "<span id="expenseTitle" class="font-medium text-white"></span>" löschen möchtest?</p>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeDeleteModal()" class="glass-btn px-3 py-1.5 rounded-lg text-xs">
                    Abbrechen
                </button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete_expense">
                    <input type="hidden" name="expense_id" id="deleteExpenseId" value="">
                    <button type="submit" class="bg-red-500/20 border border-red-500/30 text-red-300 px-3 py-1.5 rounded-lg text-xs hover:bg-red-500/30">
                        <i class="fas fa-trash mr-1.5"></i>Löschen
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function confirmDelete(expenseId, expenseTitle) {
            document.getElementById('deleteExpenseId').value = expenseId;
            document.getElementById('expenseTitle').textContent = expenseTitle;
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('deleteModal').classList.add('flex');
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.getElementById('deleteModal').classList.remove('flex');
        }
        
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
    </main>
</body>
</html>
