<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HaveToPay | PrivateVault</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #2d1b69 0%, #11101d 50%, #1a0909 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="min-h-screen flex">
    <?php include_once __DIR__ . '/navbar.php'; ?>

    <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-2 md:p-4">
        <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-6 py-2">
            <!-- Success/Error Messages -->
            <?php if (!empty($successMessage)): ?>
            <div class="bg-green-500/20 border border-green-400/30 backdrop-blur-sm text-green-300 p-3 rounded-xl mb-4 flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <p class="text-sm"><?php echo htmlspecialchars($successMessage); ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($errorMessage)): ?>
            <div class="bg-red-500/20 border border-red-400/30 backdrop-blur-sm text-red-300 p-3 rounded-xl mb-4 flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <p class="text-sm"><?php echo htmlspecialchars($errorMessage); ?></p>
            </div>
            <?php endif; ?>

            <!-- Balance Summary -->
            <div class="bg-gradient-to-br from-purple-900/20 via-gray-900/30 to-red-900/20 backdrop-blur-xl rounded-2xl border border-white/10 mb-4 overflow-hidden">
                <div class="p-4 md:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                        <div class="bg-green-500/10 border border-green-400/20 backdrop-blur-sm rounded-xl p-4">
                            <div class="text-white/60 text-xs font-medium mb-1">You are owed</div>
                            <div class="text-2xl font-bold text-green-400"><?php echo number_format($totalOwed, 2); ?> €</div>
                        </div>
                        
                        <div class="bg-white/5 border border-white/10 backdrop-blur-sm rounded-xl p-4">
                            <div class="text-white/60 text-xs font-medium mb-1">Net balance</div>
                            <div class="text-2xl font-bold <?php echo $netBalance >= 0 ? 'text-green-400' : 'text-red-400'; ?>">
                                <?php echo number_format($netBalance, 2); ?> €
                            </div>
                            <div class="mt-2">
                                <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo $netBalance >= 0 ? 'bg-green-500/20 border border-green-400/30 text-green-300' : 'bg-red-500/20 border border-red-400/30 text-red-300'; ?>">
                                    <i class="fas fa-arrow-<?php echo $netBalance >= 0 ? 'up' : 'down'; ?> mr-1"></i>
                                    <?php echo $netBalance >= 0 ? 'Positive' : 'Negative'; ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="bg-red-500/10 border border-red-400/20 backdrop-blur-sm rounded-xl p-4">
                            <div class="text-white/60 text-xs font-medium mb-1">You owe</div>
                            <div class="text-2xl font-bold text-red-400"><?php echo number_format($totalOwing, 2); ?> €</div>
                        </div>
                    </div>
                    
                    <!-- Quick Action Buttons -->
                    <div class="flex justify-center gap-3 mt-4">
                        <a href="havetopay_add.php" class="bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/10 hover:border-white/20 text-white/90 hover:text-white px-4 py-2 rounded-xl transition-all duration-200 flex items-center text-sm">
                            <i class="fas fa-plus mr-2"></i>Add Expense
                        </a>
                        <a href="index.php" class="bg-white/5 hover:bg-white/10 backdrop-blur-sm border border-white/10 hover:border-white/20 text-white/70 hover:text-white/90 px-4 py-2 rounded-xl transition-all duration-200 flex items-center text-sm">
                            <i class="fas fa-home mr-2"></i>Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                <!-- People who owe me -->
                <div class="bg-gradient-to-br from-purple-900/20 via-gray-900/30 to-red-900/20 backdrop-blur-xl rounded-2xl border border-white/10 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-600/30 via-green-700/40 to-green-800/30 backdrop-blur-sm px-4 py-3 border-b border-white/10">
                        <div class="flex justify-between items-center">
                            <h3 class="text-sm font-semibold text-white/90">People Who Owe You</h3>
                            <span class="bg-white/10 border border-white/20 text-white/80 px-2 py-1 rounded-full text-xs font-semibold backdrop-blur-sm">
                                <?php echo count($balances['others_owe']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="p-4 max-h-64 overflow-y-auto">
                        <?php if (empty($balances['others_owe'])): ?>
                            <div class="text-center py-8">
                                <i class="fas fa-check-circle text-4xl text-white/20 mb-2"></i>
                                <p class="text-white/50 text-sm">No one owes you money.</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-2">
                                <?php foreach ($balances['others_owe'] as $balance): ?>
                                <div class="flex items-center justify-between p-3 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 transition-all backdrop-blur-sm">
                                    <div class="flex items-center min-w-0">
                                        <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-full flex items-center justify-center font-semibold mr-3 text-xs flex-shrink-0">
                                            <?php echo strtoupper(substr($balance['username'], 0, 1)); ?>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="font-medium text-white/90 text-sm truncate"><?php echo htmlspecialchars($balance['display_name']); ?></div>
                                            <div class="text-xs text-white/50">@<?php echo htmlspecialchars($balance['username']); ?></div>
                                        </div>
                                    </div>
                                    <span class="bg-green-500/20 border border-green-400/30 text-green-300 px-2 py-1 rounded-lg text-xs font-semibold backdrop-blur-sm flex-shrink-0">
                                        <?php echo number_format($balance['amount_owed'], 2); ?> €
                                    </span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- People I owe -->
                <div class="bg-gradient-to-br from-purple-900/20 via-gray-900/30 to-red-900/20 backdrop-blur-xl rounded-2xl border border-white/10 overflow-hidden">
                    <div class="bg-gradient-to-r from-red-600/30 via-red-700/40 to-red-800/30 backdrop-blur-sm px-4 py-3 border-b border-white/10">
                        <div class="flex justify-between items-center">
                            <h3 class="text-sm font-semibold text-white/90">People You Owe</h3>
                            <span class="bg-white/10 border border-white/20 text-white/80 px-2 py-1 rounded-full text-xs font-semibold backdrop-blur-sm">
                                <?php echo count($balances['user_owes']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="p-4 max-h-64 overflow-y-auto">
                        <?php if (empty($balances['user_owes'])): ?>
                            <div class="text-center py-8">
                                <i class="fas fa-smile text-4xl text-white/20 mb-2"></i>
                                <p class="text-white/50 text-sm">You don't owe anyone money.</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-2">
                                <?php foreach ($balances['user_owes'] as $balance): ?>
                                <div class="flex items-center justify-between p-3 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 transition-all backdrop-blur-sm">
                                    <div class="flex items-center min-w-0">
                                        <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-red-600 text-white rounded-full flex items-center justify-center font-semibold mr-3 text-xs flex-shrink-0">
                                            <?php echo strtoupper(substr($balance['username'], 0, 1)); ?>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="font-medium text-white/90 text-sm truncate"><?php echo htmlspecialchars($balance['display_name']); ?></div>
                                            <div class="text-xs text-white/50">@<?php echo htmlspecialchars($balance['username']); ?></div>
                                        </div>
                                    </div>
                                    <span class="bg-red-500/20 border border-red-400/30 text-red-300 px-2 py-1 rounded-lg text-xs font-semibold backdrop-blur-sm flex-shrink-0">
                                        <?php echo number_format($balance['amount_owed'], 2); ?> €
                                    </span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="bg-white/5 backdrop-blur-xl rounded-xl border border-white/10 mb-4 overflow-hidden">
                <div class="p-3">
                    <form method="GET" class="flex flex-wrap items-center gap-3">
                        <!-- Status Filter -->
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-white/80 whitespace-nowrap">Status:</label>
                            <select name="status" class="px-3 py-1.5 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm focus:ring-1 focus:ring-purple-500/50 focus:border-purple-500/50 focus:outline-none">
                                <option value="">Alle</option>
                                <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Ausstehend</option>
                                <option value="settled" <?= ($_GET['status'] ?? '') === 'settled' ? 'selected' : '' ?>>Beglichen</option>
                                <option value="partially_settled" <?= ($_GET['status'] ?? '') === 'partially_settled' ? 'selected' : '' ?>>Teilweise</option>
                            </select>
                        </div>

                        <!-- User Filter -->
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-white/80 whitespace-nowrap">User:</label>
                            <select name="user" class="px-3 py-1.5 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm focus:ring-1 focus:ring-purple-500/50 focus:border-purple-500/50 focus:outline-none">
                                <option value="">Alle</option>
                                <option value="me" <?= ($_GET['user'] ?? '') === 'me' ? 'selected' : '' ?>>Meine Ausgaben</option>
                                <option value="involved" <?= ($_GET['user'] ?? '') === 'involved' ? 'selected' : '' ?>>Beteiligt</option>
                                <?php foreach ($allUsers as $user): ?>
                                    <option value="<?= $user['id'] ?>" <?= ($_GET['user'] ?? '') == $user['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($user['display_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Group Filter -->
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-white/80 whitespace-nowrap">Gruppe:</label>
                            <select name="group" class="px-3 py-1.5 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm focus:ring-1 focus:ring-purple-500/50 focus:border-purple-500/50 focus:outline-none">
                                <option value="">Alle</option>
                                <option value="no_group" <?= ($_GET['group'] ?? '') === 'no_group' ? 'selected' : '' ?>>Keine Gruppe</option>
                                <?php foreach ($allGroups as $group): ?>
                                    <option value="<?= $group['id'] ?>" <?= ($_GET['group'] ?? '') == $group['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($group['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2 ml-auto">
                            <button type="submit" class="bg-white/10 hover:bg-white/20 backdrop-blur-sm text-white/80 hover:text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-all border border-white/20 hover:border-white/30">
                                <i class="fas fa-search mr-1"></i>Filter
                            </button>
                            <a href="havetopay.php" class="bg-white/5 hover:bg-white/10 backdrop-blur-sm text-white/60 hover:text-white/80 px-3 py-1.5 rounded-lg text-sm font-medium transition-all border border-white/10 hover:border-white/20">
                                <i class="fas fa-times mr-1"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Filtered Expenses -->
            <div class="bg-gradient-to-br from-purple-900/20 via-gray-900/30 to-red-900/20 backdrop-blur-xl rounded-2xl border border-white/10 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600/30 via-indigo-700/40 to-purple-800/30 backdrop-blur-sm px-4 py-3 border-b border-white/10">
                    <div class="flex justify-between items-center">
                        <h3 class="text-sm font-semibold text-white/90">
                            <?php if (!empty($_GET['status']) || !empty($_GET['user']) || !empty($_GET['group'])): ?>
                                Gefilterte Ausgaben
                            <?php else: ?>
                                Aktuelle Ausgaben
                            <?php endif; ?>
                        </h3>
                        <span class="bg-white/10 border border-white/20 text-white/80 px-2 py-1 rounded-full text-xs font-semibold backdrop-blur-sm">
                            <?php echo count($filteredExpenses); ?>
                        </span>
                    </div>
                </div>
                <div class="p-4 max-h-80 overflow-y-auto">
                    <?php if (empty($filteredExpenses)): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-receipt text-4xl text-white/20 mb-3"></i>
                            <p class="text-white/50 mb-4 text-sm">Keine Ausgaben für die gewählten Filter gefunden.</p>
                            <a href="havetopay_add.php" class="bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/10 hover:border-white/20 text-white/90 hover:text-white font-semibold py-2 px-4 rounded-xl inline-flex items-center transition-all text-sm">
                                <i class="fas fa-plus mr-2"></i>Erste Ausgabe hinzufügen
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="space-y-2">
                            <?php foreach ($filteredExpenses as $expense): ?>
                            <div class="flex flex-col md:flex-row md:items-center justify-between p-3 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 transition-all backdrop-blur-sm">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <div class="font-medium text-white/90 text-sm truncate"><?php echo htmlspecialchars($expense['title']); ?></div>
                                                
                                                <!-- Settlement Status Badge -->
                                                <?php if ($expense['settlement_status'] === 'fully_settled'): ?>
                                                    <span class="bg-green-500/20 border border-green-400/30 text-green-300 px-2 py-1 rounded-full text-xs font-medium">
                                                        <i class="fas fa-check-circle mr-1"></i>Settled
                                                    </span>
                                                <?php elseif ($expense['settlement_status'] === 'partially_settled'): ?>
                                                    <span class="bg-yellow-500/20 border border-yellow-400/30 text-yellow-300 px-2 py-1 rounded-full text-xs font-medium">
                                                        <i class="fas fa-clock mr-1"></i>Partial
                                                    </span>
                                                <?php else: ?>
                                                    <span class="bg-red-500/20 border border-red-400/30 text-red-300 px-2 py-1 rounded-full text-xs font-medium">
                                                        <i class="fas fa-exclamation-circle mr-1"></i>Pending
                                                    </span>
                                                <?php endif; ?>

                                                <!-- Group Badge -->
                                                <?php if (!empty($expense['group_name'])): ?>
                                                    <span class="bg-purple-500/20 border border-purple-400/30 text-purple-300 px-2 py-1 rounded-full text-xs font-medium">
                                                        <i class="fas fa-users mr-1"></i><?= htmlspecialchars($expense['group_name']) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <?php if(!empty($expense['description'])): ?>
                                                <div class="text-xs text-white/50 mt-1 truncate"><?php echo htmlspecialchars(mb_strimwidth($expense['description'], 0, 40, "...")); ?></div>
                                            <?php endif; ?>
                                            
                                            <div class="flex items-center gap-3 mt-1 text-xs text-white/60">
                                                <span><?php echo number_format($expense['amount'], 2); ?> €</span>
                                                <span><?php echo date('d M', strtotime($expense['expense_date'])); ?></span>
                                                <span><i class="fas fa-users mr-1"></i><?php echo $expense['participant_count']; ?></span>
                                                <span><i class="fas fa-user mr-1"></i><?php echo htmlspecialchars($expense['payer_display_name']); ?></span>
                                                
                                                <!-- Settlement Progress -->
                                                <span class="text-xs">
                                                    <?= $expense['settled_count'] ?>/<?= $expense['participant_count'] ?> paid
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex gap-2 ml-3">
                                            <a href="havetopay_detail.php?id=<?php echo $expense['id']; ?>" 
                                               class="text-blue-400 hover:text-blue-300 text-sm transition-colors">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($expense['payer_id'] == $userId || ($_SESSION['is_admin'] ?? false)): ?>
                                                <button type="button" 
                                                        class="text-red-400 hover:text-red-300 text-sm transition-colors"
                                                        onclick="confirmDelete(<?php echo $expense['id']; ?>, '<?php echo htmlspecialchars($expense['title'], ENT_QUOTES); ?>')">
                                                    <i class="fas fa-trash"></i>
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
            <div class="bg-gradient-to-br from-purple-900/40 via-gray-900/50 to-red-900/40 backdrop-blur-xl border border-white/20 rounded-2xl p-6 max-w-md mx-4">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-red-500/20 border border-red-400/30 text-red-400 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white/90">Confirm Delete</h3>
                </div>
                <p class="text-white/70 mb-6 text-sm">Are you sure you want to delete "<span id="expenseTitle" class="font-medium text-white/90"></span>"? This action cannot be undone.</p>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-white/70 bg-white/10 border border-white/20 rounded-xl hover:bg-white/20 font-medium transition-all backdrop-blur-sm text-sm">
                        Cancel
                    </button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete_expense">
                        <input type="hidden" name="expense_id" id="deleteExpenseId" value="">
                        <button type="submit" class="px-4 py-2 bg-red-500/30 border border-red-400/40 text-red-300 rounded-xl hover:bg-red-500/40 font-medium transition-all backdrop-blur-sm text-sm">
                            <i class="fas fa-trash mr-2"></i>Delete
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
            
            // Close modal when clicking outside
            document.getElementById('deleteModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeDeleteModal();
                }
            });
        </script>
    </main>
</body>
</html>
