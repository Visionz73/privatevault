<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HaveToPay | PrivateVault</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen flex">
    <?php include_once __DIR__ . '/navbar.php'; ?>

    <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Success/Error Messages -->
        <?php if (!empty($successMessage)): ?>
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-8 flex items-center shadow-sm">
            <i class="fas fa-check-circle text-xl mr-3"></i>
            <p><?php echo htmlspecialchars($successMessage); ?></p>
        </div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-8 flex items-center shadow-sm">
            <i class="fas fa-exclamation-circle text-xl mr-3"></i>
            <p><?php echo htmlspecialchars($errorMessage); ?></p>
        </div>
        <?php endif; ?>

        <!-- Header with Gradient Background -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl shadow-lg mb-10 overflow-hidden">
            <div class="px-8 py-10 text-white">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-6">
                    <h1 class="text-3xl md:text-4xl font-bold flex items-center">
                        <i class="fas fa-wallet mr-4 opacity-80"></i>HaveToPay
                    </h1>
                    <div class="flex flex-wrap gap-3">
                        <a href="havetopay_add.php" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-5 py-3 rounded-xl shadow-sm flex items-center justify-center backdrop-blur-sm transition-colors">
                            <i class="fas fa-plus mr-2"></i>Add Expense
                        </a>
                        <a href="index.php" class="bg-white bg-opacity-10 hover:bg-opacity-20 text-white px-5 py-3 rounded-xl shadow-sm flex items-center justify-center backdrop-blur-sm transition-colors">
                            <i class="fas fa-home mr-2"></i>Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Balance Summary Card -->
        <div class="bg-white rounded-2xl shadow-md mb-10 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-xl font-semibold text-gray-800">Your Balance Summary</h2>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                    <div class="bg-green-50 rounded-xl p-6">
                        <div class="text-gray-600 text-sm font-medium mb-2">You are owed</div>
                        <div class="text-3xl font-bold text-green-600"><?php echo number_format($totalOwed, 2); ?> €</div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl p-6">
                        <div class="text-gray-600 text-sm font-medium mb-2">Your net balance</div>
                        <div class="text-3xl font-bold <?php echo $netBalance >= 0 ? 'text-green-600' : 'text-red-600'; ?>">
                            <?php echo number_format($netBalance, 2); ?> €
                        </div>
                        <div class="mt-3">
                            <span class="px-3 py-1 text-xs font-medium rounded-full <?php echo $netBalance >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                                <i class="fas fa-arrow-<?php echo $netBalance >= 0 ? 'up' : 'down'; ?> mr-1"></i>
                                <?php echo $netBalance >= 0 ? 'Positive' : 'Negative'; ?> Balance
                            </span>
                        </div>
                    </div>
                    
                    <div class="bg-red-50 rounded-xl p-6">
                        <div class="text-gray-600 text-sm font-medium mb-2">You owe</div>
                        <div class="text-3xl font-bold text-red-600"><?php echo number_format($totalOwing, 2); ?> €</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-10">
            <!-- People who owe me -->
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4 text-white">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold">People Who Owe You</h3>
                        <span class="bg-white/20 text-white px-3 py-1 rounded-full text-xs font-semibold">
                            <?php echo count($balances['others_owe']); ?> people
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <?php if (empty($balances['others_owe'])): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-check-circle text-6xl text-gray-200 mb-4"></i>
                            <p class="text-gray-500">No one owes you money at the moment.</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($balances['others_owe'] as $balance): ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-full flex items-center justify-center font-semibold mr-4">
                                        <?php echo strtoupper(substr($balance['username'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-800"><?php echo htmlspecialchars($balance['display_name']); ?></div>
                                        <div class="text-sm text-gray-500">@<?php echo htmlspecialchars($balance['username']); ?></div>
                                    </div>
                                </div>
                                <span class="bg-green-100 text-green-700 px-4 py-2 rounded-lg text-sm font-semibold">
                                    <?php echo number_format($balance['amount_owed'], 2); ?> €
                                </span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- People I owe -->
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-4 text-white">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold">People You Owe</h3>
                        <span class="bg-white/20 text-white px-3 py-1 rounded-full text-xs font-semibold">
                            <?php echo count($balances['user_owes']); ?> people
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <?php if (empty($balances['user_owes'])): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-smile text-6xl text-gray-200 mb-4"></i>
                            <p class="text-gray-500">You don't owe anyone money at the moment.</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($balances['user_owes'] as $balance): ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 text-white rounded-full flex items-center justify-center font-semibold mr-4">
                                        <?php echo strtoupper(substr($balance['username'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-800"><?php echo htmlspecialchars($balance['display_name']); ?></div>
                                        <div class="text-sm text-gray-500">@<?php echo htmlspecialchars($balance['username']); ?></div>
                                    </div>
                                </div>
                                <span class="bg-red-100 text-red-700 px-4 py-2 rounded-lg text-sm font-semibold">
                                    <?php echo number_format($balance['amount_owed'], 2); ?> €
                                </span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Expenses -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4 text-white">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Recent Expenses</h3>
                    <span class="bg-white/20 text-white px-3 py-1 rounded-full text-xs font-semibold">
                        <?php echo count($recentExpenses); ?> expenses
                    </span>
                </div>
            </div>
            <div class="p-6">
                <?php if (empty($recentExpenses)): ?>
                    <div class="text-center py-16">
                        <i class="fas fa-receipt text-6xl text-gray-200 mb-5"></i>
                        <p class="text-gray-500 mb-6 text-lg">No expenses recorded yet.</p>
                        <a href="havetopay_add.php" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold py-3 px-6 rounded-xl shadow-md inline-flex items-center hover:from-indigo-700 hover:to-purple-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Add Your First Expense
                        </a>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="text-left py-4 px-4 font-medium text-gray-600 text-sm">Expense</th>
                                    <th class="text-left py-4 px-4 font-medium text-gray-600 text-sm">Amount</th>
                                    <th class="text-left py-4 px-4 font-medium text-gray-600 text-sm">Paid By</th>
                                    <th class="text-left py-4 px-4 font-medium text-gray-600 text-sm">Date</th>
                                    <th class="text-left py-4 px-4 font-medium text-gray-600 text-sm">Participants</th>
                                    <th class="text-left py-4 px-4 font-medium text-gray-600 text-sm">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentExpenses as $expense): ?>
                                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                                    <td class="py-5 px-4">
                                        <div class="font-medium text-gray-800"><?php echo htmlspecialchars($expense['title']); ?></div>
                                        <?php if(!empty($expense['description'])): ?>
                                            <div class="text-xs text-gray-500 mt-1"><?php echo htmlspecialchars(mb_strimwidth($expense['description'], 0, 50, "...")); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-5 px-4 font-semibold text-gray-800"><?php echo number_format($expense['amount'], 2); ?> €</td>
                                    <td class="py-5 px-4">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-full flex items-center justify-center text-xs font-medium mr-2">
                                                <?php echo strtoupper(substr($expense['payer_name'], 0, 1)); ?>
                                            </div>
                                            <span class="text-gray-700"><?php echo htmlspecialchars($expense['payer_display_name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="py-5 px-4 text-gray-600"><?php echo date('d M Y', strtotime($expense['expense_date'])); ?></td>
                                    <td class="py-5 px-4">
                                        <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-semibold">
                                            <i class="fas fa-users mr-1"></i> 
                                            <?php echo $expense['participant_count']; ?> people
                                        </span>
                                    </td>
                                    <td class="py-5 px-4">
                                        <div class="flex gap-4">
                                            <a href="havetopay_detail.php?id=<?php echo $expense['id']; ?>" 
                                               class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                                                <i class="fas fa-eye mr-1"></i>Details
                                            </a>
                                            <?php if ($expense['payer_id'] == $userId || ($_SESSION['is_admin'] ?? false)): ?>
                                                <button type="button" 
                                                        class="text-red-600 hover:text-red-800 font-medium text-sm"
                                                        onclick="confirmDelete(<?php echo $expense['id']; ?>, '<?php echo htmlspecialchars($expense['title'], ENT_QUOTES); ?>')">
                                                    <i class="fas fa-trash mr-1"></i>Delete
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl p-8 max-w-md mx-4 shadow-xl">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-red-100 text-red-600 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800">Confirm Delete</h3>
            </div>
            <p class="text-gray-600 mb-8">Are you sure you want to delete "<span id="expenseTitle" class="font-medium"></span>"? This action cannot be undone.</p>
            <div class="flex justify-end gap-4">
                <button type="button" onclick="closeDeleteModal()" class="px-5 py-3 text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 font-medium transition-colors">
                    Cancel
                </button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete_expense">
                    <input type="hidden" name="expense_id" id="deleteExpenseId" value="">
                    <button type="submit" class="px-5 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 font-medium transition-colors">
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
    </div>
    </main>
</body>
</html>
