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
        
        .glass-container {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
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
            border-radius: 0.5rem;
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
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body class="min-h-screen flex">
    <?php include_once __DIR__ . '/navbar.php'; ?>

    <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-4 md:p-6">
        <div class="max-w-6xl mx-auto">
            <!-- Compact Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-white">Schuldenverwaltung</h1>
                <a href="havetopay_add.php" class="glass-btn px-4 py-2 rounded-lg text-sm flex items-center">
                    <i class="fas fa-plus mr-2"></i>Ausgabe hinzufügen
                </a>
            </div>

            <!-- Success/Error Messages -->
            <?php if (!empty($successMessage)): ?>
            <div class="glass-container p-3 mb-4 flex items-center text-green-300">
                <i class="fas fa-check-circle mr-2"></i>
                <p class="text-sm"><?php echo htmlspecialchars($successMessage); ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($errorMessage)): ?>
            <div class="glass-container p-3 mb-4 flex items-center text-red-300">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <p class="text-sm"><?php echo htmlspecialchars($errorMessage); ?></p>
            </div>
            <?php endif; ?>

            <!-- Balance Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="glass-container p-4 text-center">
                    <div class="text-white/60 text-xs mb-1">Du bekommst</div>
                    <div class="text-xl font-bold text-green-400"><?php echo number_format($totalOwed, 2); ?> €</div>
                </div>
                
                <div class="glass-container p-4 text-center">
                    <div class="text-white/60 text-xs mb-1">Netto-Saldo</div>
                    <div class="text-xl font-bold <?php echo $netBalance >= 0 ? 'text-green-400' : 'text-red-400'; ?>">
                        <?php echo number_format($netBalance, 2); ?> €
                    </div>
                </div>
                
                <div class="glass-container p-4 text-center">
                    <div class="text-white/60 text-xs mb-1">Du schuldest</div>
                    <div class="text-xl font-bold text-red-400"><?php echo number_format($totalOwing, 2); ?> €</div>
                </div>
            </div>

            <!-- Balances Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- People who owe you -->
                <div class="glass-container">
                    <div class="glass-header px-4 py-3 text-white">
                        <h3 class="text-sm font-semibold">Personen die dir Geld schulden (<?php echo count($balances['others_owe']); ?>)</h3>
                    </div>
                    <div class="p-4">
                        <?php if (empty($balances['others_owe'])): ?>
                            <div class="text-center py-6">
                                <i class="fas fa-check-circle text-3xl text-white/20 mb-2"></i>
                                <p class="text-white/60 text-sm">Niemand schuldet dir Geld</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-2">
                                <?php foreach ($balances['others_owe'] as $balance): ?>
                                <div class="glass-item p-3 flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gradient-to-br from-green-400 to-green-500 text-white rounded-full flex items-center justify-center text-xs font-semibold mr-3">
                                            <?php echo strtoupper(substr($balance['username'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="text-white text-sm font-medium"><?php echo htmlspecialchars($balance['display_name']); ?></div>
                                            <div class="text-white/60 text-xs">@<?php echo htmlspecialchars($balance['username']); ?></div>
                                        </div>
                                    </div>
                                    <span class="text-green-300 text-sm font-semibold">
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
                    <div class="glass-header px-4 py-3 text-white">
                        <h3 class="text-sm font-semibold">Personen denen du Geld schuldest (<?php echo count($balances['user_owes']); ?>)</h3>
                    </div>
                    <div class="p-4">
                        <?php if (empty($balances['user_owes'])): ?>
                            <div class="text-center py-6">
                                <i class="fas fa-smile text-3xl text-white/20 mb-2"></i>
                                <p class="text-white/60 text-sm">Du schuldest niemandem Geld</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-2">
                                <?php foreach ($balances['user_owes'] as $balance): ?>
                                <div class="glass-item p-3 flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gradient-to-br from-red-400 to-red-500 text-white rounded-full flex items-center justify-center text-xs font-semibold mr-3">
                                            <?php echo strtoupper(substr($balance['username'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="text-white text-sm font-medium"><?php echo htmlspecialchars($balance['display_name']); ?></div>
                                            <div class="text-white/60 text-xs">@<?php echo htmlspecialchars($balance['username']); ?></div>
                                        </div>
                                    </div>
                                    <span class="text-red-300 text-sm font-semibold">
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
                <div class="glass-header px-4 py-3 text-white">
                    <div class="flex justify-between items-center">
                        <h3 class="text-sm font-semibold">Aktuelle Ausgaben (<?php echo count($filteredExpenses); ?>)</h3>
                        <div class="flex items-center gap-2">
                            <form method="GET" class="flex items-center gap-2">
                                <label for="filter_user" class="text-xs text-white/70">Filter:</label>
                                <select name="filter_user" id="filter_user" onchange="this.form.submit()" 
                                        class="bg-white/10 border border-white/20 rounded px-2 py-1 text-xs text-white">
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
                <div class="p-4">
                    <?php if (empty($filteredExpenses)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-receipt text-4xl text-white/20 mb-3"></i>
                            <p class="text-white/60 mb-4">Keine Ausgaben gefunden</p>
                            <a href="havetopay_add.php" class="glass-btn py-2 px-4 rounded-lg text-sm">
                                <i class="fas fa-plus mr-2"></i>Erste Ausgabe hinzufügen
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="space-y-2">
                            <?php foreach ($filteredExpenses as $expense): ?>
                            <div class="glass-item p-3">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <div class="text-white font-medium text-sm"><?php echo htmlspecialchars($expense['title']); ?></div>
                                                <div class="text-white/60 text-xs">
                                                    Von <?php echo htmlspecialchars($expense['payer_display_name']); ?> • 
                                                    <?php echo date('d.m.Y', strtotime($expense['expense_date'])); ?> • 
                                                    <?php echo $expense['participant_count']; ?> Personen
                                                </div>
                                            </div>
                                            <div class="text-white font-semibold text-sm"><?php echo number_format($expense['amount'], 2); ?> €</div>
                                        </div>
                                        <div class="flex gap-2">
                                            <a href="havetopay_detail.php?id=<?php echo $expense['id']; ?>" 
                                               class="text-blue-400 hover:text-blue-300 text-xs">
                                                <i class="fas fa-eye mr-1"></i>Details
                                            </a>
                                            <?php if ($expense['payer_id'] == $userId || ($_SESSION['is_admin'] ?? false)): ?>
                                                <button type="button" 
                                                        class="text-red-400 hover:text-red-300 text-xs"
                                                        onclick="confirmDelete(<?php echo $expense['id']; ?>, '<?php echo htmlspecialchars($expense['title'], ENT_QUOTES); ?>')">
                                                    <i class="fas fa-trash mr-1"></i>Löschen
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
        <div class="glass-modal p-6 max-w-md mx-4">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-red-500/20 text-red-400 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="text-lg font-semibold text-white">Löschen bestätigen</h3>
            </div>
            <p class="text-white/70 mb-6">Bist du sicher, dass du "<span id="expenseTitle" class="font-medium text-white"></span>" löschen möchtest?</p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()" class="glass-btn px-4 py-2 rounded-lg text-sm">
                    Abbrechen
                </button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete_expense">
                    <input type="hidden" name="expense_id" id="deleteExpenseId" value="">
                    <button type="submit" class="bg-red-500/20 border border-red-500/30 text-red-300 px-4 py-2 rounded-lg text-sm hover:bg-red-500/30">
                        <i class="fas fa-trash mr-2"></i>Löschen
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
