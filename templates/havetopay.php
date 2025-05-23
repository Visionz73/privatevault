<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HaveToPay | PrivateVault</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4F46E5;
            --success-color: #10B981;
            --danger-color: #EF4444;
        }
        
        .modern-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        
        .modern-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .gradient-primary {
            background: linear-gradient(135deg, var(--primary-color), #3730A3);
        }
        
        .gradient-success {
            background: linear-gradient(135deg, var(--success-color), #059669);
        }
        
        .gradient-danger {
            background: linear-gradient(135deg, var(--danger-color), #B91C1C);
        }
        
        .btn-modern {
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-modern:hover {
            transform: translateY(-2px);
        }
        
        .balance-card {
            animation: fadeIn 0.6s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Mobile adjustments */
        @media (max-width: 768px) {
            body {
                padding-top: 4rem !important;
            }
        }
        
        /* Desktop adjustments */
        @media (min-width: 769px) {
            .main-content {
                margin-left: 16rem;
                width: calc(100% - 16rem);
            }
        }
    </style>
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
        <div class="d-flex justify-content-between align-items-center mb-4 fade-in">
            <h1 class="h2"><i class="fas fa-wallet me-3 text-primary"></i>HaveToPay</h1>
            <div class="d-flex gap-2">
                <a href="havetopay_add.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Expense
                </a>
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="fas fa-home me-2"></i>Dashboard
                </a>
            </div>
        </div>

        <!-- Balance Summary Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card slide-up">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Your Balance Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center g-4">
                            <div class="col-md-4">
                                <div class="text-muted mb-2">You are owed</div>
                                <h3 class="text-success mb-0"><?php echo number_format($totalOwed, 2); ?> €</h3>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted mb-2">Your net balance</div>
                                <h3 class="<?php echo $netBalance >= 0 ? 'text-success' : 'text-danger'; ?> mb-2">
                                    <?php echo number_format($netBalance, 2); ?> €
                                </h3>
                                <span class="badge <?php echo $netBalance >= 0 ? 'bg-success' : 'bg-danger'; ?>">
                                    <i class="fas fa-arrow-<?php echo $netBalance >= 0 ? 'up' : 'down'; ?> me-1"></i>
                                    <?php echo $netBalance >= 0 ? 'Positive' : 'Negative'; ?> Balance
                                </span>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted mb-2">You owe</div>
                                <h3 class="text-danger mb-0"><?php echo number_format($totalOwing, 2); ?> €</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- People who owe me -->
            <div class="modern-card">
                <div class="gradient-success text-white p-4 rounded-t-2xl flex justify-between items-center">
                    <h3 class="font-bold">People Who Owe You</h3>
                    <span class="bg-white text-green-600 px-3 py-1 rounded-full text-sm font-medium">
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
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-indigo-600 text-white rounded-full flex items-center justify-center font-semibold mr-3">
                                        <?php echo strtoupper(substr($balance['username'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div class="font-medium"><?php echo htmlspecialchars($balance['display_name']); ?></div>
                                        <div class="text-sm text-gray-500">@<?php echo htmlspecialchars($balance['username']); ?></div>
                                    </div>
                                </div>
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full font-medium">
                                    <?php echo number_format($balance['amount_owed'], 2); ?> €
                                </span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- People I owe -->
            <div class="modern-card">
                <div class="gradient-danger text-white p-4 rounded-t-2xl flex justify-between items-center">
                    <h3 class="font-bold">People You Owe</h3>
                    <span class="bg-white text-red-600 px-3 py-1 rounded-full text-sm font-medium">
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
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-red-600 text-white rounded-full flex items-center justify-center font-semibold mr-3">
                                        <?php echo strtoupper(substr($balance['username'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div class="font-medium"><?php echo htmlspecialchars($balance['display_name']); ?></div>
                                        <div class="text-sm text-gray-500">@<?php echo htmlspecialchars($balance['username']); ?></div>
                                    </div>
                                </div>
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full font-medium">
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
        <div class="modern-card">
            <div class="gradient-primary text-white p-4 rounded-t-2xl flex justify-between items-center">
                <h3 class="font-bold">Recent Expenses</h3>
                <span class="bg-white text-indigo-600 px-3 py-1 rounded-full text-sm font-medium">
                    <?php echo count($recentExpenses); ?> expenses
                </span>
            </div>
            <div class="p-4">
                <?php if (empty($recentExpenses)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-receipt text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 mb-4">No expenses recorded yet.</p>
                        <a href="havetopay_add.php" class="btn-modern bg-indigo-600 text-white hover:bg-indigo-700">
                            <i class="fas fa-plus mr-2"></i>Add Your First Expense
                        </a>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="border-b border-gray-200">
                                <tr>
                                    <th class="text-left py-3 font-medium text-gray-600">Expense</th>
                                    <th class="text-left py-3 font-medium text-gray-600">Amount</th>
                                    <th class="text-left py-3 font-medium text-gray-600">Paid By</th>
                                    <th class="text-left py-3 font-medium text-gray-600">Date</th>
                                    <th class="text-left py-3 font-medium text-gray-600">Participants</th>
                                    <th class="text-left py-3 font-medium text-gray-600">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($recentExpenses as $expense): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-4">
                                        <div class="font-medium"><?php echo htmlspecialchars($expense['title']); ?></div>
                                        <?php if(!empty($expense['description'])): ?>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars(mb_strimwidth($expense['description'], 0, 50, "...")); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4 font-semibold"><?php echo number_format($expense['amount'], 2); ?> €</td>
                                    <td class="py-4">
                                        <div class="flex items-center">
                                            <div class="w-7 h-7 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-medium mr-2">
                                                <?php echo strtoupper(substr($expense['payer_name'], 0, 1)); ?>
                                            </div>
                                            <span class="text-sm"><?php echo htmlspecialchars($expense['payer_display_name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="py-4 text-sm text-gray-600"><?php echo date('d M Y', strtotime($expense['expense_date'])); ?></td>
                                    <td class="py-4">
                                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-medium">
                                            <i class="fas fa-users mr-1"></i> 
                                            <?php echo $expense['participant_count']; ?> people
                                        </span>
                                    </td>
                                    <td class="py-4">
                                        <div class="flex gap-2">
                                            <a href="havetopay_detail.php?id=<?php echo $expense['id']; ?>" 
                                               class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                                                <i class="fas fa-eye mr-1"></i>Details
                                            </a>
                                            <?php if ($expense['payer_id'] == $userId || ($_SESSION['is_admin'] ?? false)): ?>
                                                <button type="button" 
                                                        class="text-red-600 hover:text-red-800 font-medium text-sm border-none bg-transparent cursor-pointer"
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
        <div class="bg-white rounded-lg p-6 max-w-md mx-4">
            <div class="flex items-center mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl mr-3"></i>
                <h3 class="text-lg font-semibold">Confirm Delete</h3>
            </div>
            <p class="text-gray-600 mb-6">Are you sure you want to delete "<span id="expenseTitle" class="font-medium"></span>"? This action cannot be undone.</p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-300">
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
