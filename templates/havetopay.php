<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HaveToPay | PrivateVault</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="main-content p-6">
        <!-- Success/Error Messages -->
        <?php if (!empty($successMessage)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
            <i class="fas fa-check-circle mr-3"></i>
            <?php echo htmlspecialchars($successMessage); ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
            <i class="fas fa-exclamation-circle mr-3"></i>
            <?php echo htmlspecialchars($errorMessage); ?>
        </div>
        <?php endif; ?>

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-wallet mr-3 text-indigo-600"></i>HaveToPay
            </h1>
            <div class="flex gap-3">
                <a href="havetopay_add.php" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-sm flex items-center">
                    <i class="fas fa-plus mr-2"></i>Add Expense
                </a>
                <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg shadow-sm flex items-center">
                    <i class="fas fa-home mr-2"></i>Dashboard
                </a>
            </div>
        </div>

        <!-- Balance Summary Card -->
        <div class="bg-white shadow-md rounded-lg mb-8">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-700">Your Balance Summary</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                    <div>
                        <div class="text-gray-600 text-sm font-medium">You are owed</div>
                        <div class="text-2xl font-bold text-green-600 mt-1"><?php echo number_format($totalOwed, 2); ?> €</div>
                    </div>
                    
                    <div>
                        <div class="text-gray-600 text-sm font-medium">Your net balance</div>
                        <div class="text-2xl font-bold <?php echo $netBalance >= 0 ? 'text-green-600' : 'text-red-600'; ?> mt-1">
                            <?php echo number_format($netBalance, 2); ?> €
                        </div>
                        <div class="mt-2">
                            <span class="px-3 py-1 text-xs font-medium rounded-full <?php echo $netBalance >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                                <i class="fas fa-arrow-<?php echo $netBalance >= 0 ? 'up' : 'down'; ?> mr-1"></i>
                                <?php echo $netBalance >= 0 ? 'Positive' : 'Negative'; ?> Balance
                            </span>
                        </div>
                    </div>
                    
                    <div>
                        <div class="text-gray-600 text-sm font-medium">You owe</div>
                        <div class="text-2xl font-bold text-red-600 mt-1"><?php echo number_format($totalOwing, 2); ?> €</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- People who owe me -->
            <div class="bg-white shadow-md rounded-lg">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-700">People Who Owe You</h3>
                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">
                        <?php echo count($balances['others_owe']); ?> people
                    </span>
                </div>
                <div class="p-4">
                    <?php if (empty($balances['others_owe'])): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-check-circle text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">No one owes you money at the moment.</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($balances['others_owe'] as $balance): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-slate-200 text-slate-600 rounded-full flex items-center justify-center font-semibold mr-3">
                                        <?php echo strtoupper(substr($balance['username'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div class="font-medium"><?php echo htmlspecialchars($balance['display_name']); ?></div>
                                        <div class="text-sm text-gray-500">@<?php echo htmlspecialchars($balance['username']); ?></div>
                                    </div>
                                </div>
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">
                                    <?php echo number_format($balance['amount_owed'], 2); ?> €
                                </span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- People I owe -->
            <div class="bg-white shadow-md rounded-lg">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-700">People You Owe</h3>
                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">
                        <?php echo count($balances['user_owes']); ?> people
                    </span>
                </div>
                <div class="p-4">
                    <?php if (empty($balances['user_owes'])): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-smile text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">You don't owe anyone money at the moment.</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($balances['user_owes'] as $balance): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-slate-200 text-slate-600 rounded-full flex items-center justify-center font-semibold mr-3">
                                        <?php echo strtoupper(substr($balance['username'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div class="font-medium"><?php echo htmlspecialchars($balance['display_name']); ?></div>
                                        <div class="text-sm text-gray-500">@<?php echo htmlspecialchars($balance['username']); ?></div>
                                    </div>
                                </div>
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">
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
        <div class="bg-white shadow-md rounded-lg">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-700">Recent Expenses</h3>
                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">
                    <?php echo count($recentExpenses); ?> expenses
                </span>
            </div>
            <div class="p-4">
                <?php if (empty($recentExpenses)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-receipt text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 mb-4">No expenses recorded yet.</p>
                        <a href="havetopay_add.php" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-sm inline-flex items-center">
                            <i class="fas fa-plus mr-2"></i>Add Your First Expense
                        </a>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="border-b border-gray-200">
                                <tr>
                                    <th class="text-left py-3 px-4 font-medium text-gray-600 text-sm">Expense</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-600 text-sm">Amount</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-600 text-sm">Paid By</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-600 text-sm">Date</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-600 text-sm">Participants</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-600 text-sm">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($recentExpenses as $expense): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-4 px-4 text-sm">
                                        <div class="font-medium text-gray-800"><?php echo htmlspecialchars($expense['title']); ?></div>
                                        <?php if(!empty($expense['description'])): ?>
                                            <div class="text-xs text-gray-500"><?php echo htmlspecialchars(mb_strimwidth($expense['description'], 0, 50, "...")); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4 px-4 text-sm font-semibold text-gray-700"><?php echo number_format($expense['amount'], 2); ?> €</td>
                                    <td class="py-4 px-4 text-sm">
                                        <div class="flex items-center">
                                            <div class="w-7 h-7 bg-slate-200 text-slate-600 rounded-full flex items-center justify-center text-xs font-medium mr-2">
                                                <?php echo strtoupper(substr($expense['payer_name'], 0, 1)); ?>
                                            </div>
                                            <span class="text-gray-700"><?php echo htmlspecialchars($expense['payer_display_name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-sm text-gray-600"><?php echo date('d M Y', strtotime($expense['expense_date'])); ?></td>
                                    <td class="py-4 px-4 text-sm">
                                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-semibold">
                                            <i class="fas fa-users mr-1"></i> 
                                            <?php echo $expense['participant_count']; ?> people
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-sm">
                                        <div class="flex gap-3">
                                            <a href="havetopay_detail.php?id=<?php echo $expense['id']; ?>" 
                                               class="text-blue-500 hover:text-blue-700 font-medium">
                                                <i class="fas fa-eye mr-1"></i>Details
                                            </a>
                                            <?php if ($expense['payer_id'] == $userId || ($_SESSION['is_admin'] ?? false)): ?>
                                                <button type="button" 
                                                        class="text-red-500 hover:text-red-700 font-medium"
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
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md mx-4 shadow-xl">
            <div class="flex items-center mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl mr-3"></i>
                <h3 class="text-lg font-semibold text-gray-800">Confirm Delete</h3>
            </div>
            <p class="text-gray-600 mb-6">Are you sure you want to delete "<span id="expenseTitle" class="font-medium"></span>"? This action cannot be undone.</p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                    Cancel
                </button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete_expense">
                    <input type="hidden" name="expense_id" id="deleteExpenseId" value="">
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
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
</body>
</html>
