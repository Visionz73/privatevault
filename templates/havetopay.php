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
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            --success-gradient: linear-gradient(135deg, #059669 0%, #047857 100%);
            --danger-gradient: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            --glass-bg: rgba(255, 255, 255, 0.9);
            --glass-border: rgba(0, 0, 0, 0.1);
        }
        
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #cbd5e1 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            padding: 2rem;
            color: #1f2937;
        }
        
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }
        
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        .gradient-primary {
            background: var(--primary-gradient);
            color: white;
        }
        
        .gradient-success {
            background: var(--success-gradient);
            color: white;
        }
        
        .gradient-danger {
            background: var(--danger-gradient);
            color: white;
        }
        
        .btn-modern {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 13px;
            color: #374151;
        }
        
        .btn-primary {
            background: var(--primary-gradient);
            color: white;
            border: none;
        }
        
        .btn-secondary {
            background: rgba(156, 163, 175, 0.2);
            color: #374151;
            border: 1px solid rgba(156, 163, 175, 0.3);
        }
        
        .balance-card {
            animation: slideInUp 0.8s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        
        .metric-card {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 16px;
            padding: 20px;
        }
        
        .user-avatar {
            background: linear-gradient(135deg, #4f46e5, #3730a3);
            border: 2px solid rgba(255, 255, 255, 0.9);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            color: white;
        }
        
        .status-badge {
            border: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .table-glass {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 16px;
        }
        
        .table-row {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Text colors for better contrast */
        .text-primary {
            color: #1f2937;
        }
        
        .text-secondary {
            color: #6b7280;
        }
        
        .text-muted {
            color: #9ca3af;
        }
        
        /* Mobile adjustments */
        @media (max-width: 768px) {
            body {
                padding: 1rem;
                padding-top: 5rem !important;
            }
            
            .main-container {
                max-width: 100%;
            }
        }
        
        /* Desktop adjustments */
        @media (min-width: 769px) {
            .main-content {
                margin-left: 16rem;
                width: calc(100% - 16rem);
            }
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="main-content">
            <!-- Success/Error Messages -->
            <?php if (!empty($successMessage)): ?>
            <div class="glass-card mb-4 p-4 text-primary">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3 text-green-600"></i>
                    <?php echo htmlspecialchars($successMessage); ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($errorMessage)): ?>
            <div class="glass-card mb-4 p-4 text-primary">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-3 text-red-600"></i>
                    <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-primary flex items-center">
                    <i class="fas fa-wallet mr-3 text-indigo-600"></i>HaveToPay
                </h1>
                <div class="flex gap-3">
                    <a href="havetopay_add.php" class="btn-modern btn-primary flex items-center">
                        <i class="fas fa-plus mr-2"></i>Add Expense
                    </a>
                    <a href="index.php" class="btn-modern btn-secondary flex items-center">
                        <i class="fas fa-home mr-2"></i>Dashboard
                    </a>
                </div>
            </div>

            <!-- Balance Summary Card -->
            <div class="glass-card balance-card mb-6">
                <div class="gradient-primary text-white p-6 rounded-t-3xl">
                    <h2 class="text-xl font-bold flex items-center">
                        <i class="fas fa-chart-line mr-3"></i>Balance Overview
                    </h2>
                    <p class="opacity-90 mt-1 text-sm">Track your financial balance with friends</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="metric-card text-center">
                            <div class="text-secondary text-xs font-medium mb-2">You are owed</div>
                            <div class="text-3xl font-bold text-green-600 mb-2"><?php echo number_format($totalOwed, 2); ?> €</div>
                            <div class="w-10 h-0.5 bg-green-600 rounded-full mx-auto"></div>
                        </div>
                        
                        <div class="metric-card text-center">
                            <div class="text-secondary text-xs font-medium mb-2">Net Balance</div>
                            <div class="text-3xl font-bold <?php echo $netBalance >= 0 ? 'text-green-600' : 'text-red-600'; ?> mb-2">
                                <?php echo number_format($netBalance, 2); ?> €
                            </div>
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium status-badge <?php echo $netBalance >= 0 ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50'; ?>">
                                <i class="fas fa-arrow-<?php echo $netBalance >= 0 ? 'up' : 'down'; ?> mr-1"></i>
                                <?php echo $netBalance >= 0 ? 'Positive' : 'Negative'; ?>
                            </div>
                        </div>
                        
                        <div class="metric-card text-center">
                            <div class="text-secondary text-xs font-medium mb-2">You owe</div>
                            <div class="text-3xl font-bold text-red-600 mb-2"><?php echo number_format($totalOwing, 2); ?> €</div>
                            <div class="w-10 h-0.5 bg-red-600 rounded-full mx-auto"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- People who owe me -->
                <div class="glass-card">
                    <div class="gradient-success text-white p-5 rounded-t-3xl flex justify-between items-center">
                        <h3 class="font-bold text-lg flex items-center">
                            <i class="fas fa-hand-holding-usd mr-2"></i>Money Incoming
                        </h3>
                        <span class="bg-white/20 text-white px-3 py-1 rounded-full text-xs font-medium">
                            <?php echo count($balances['others_owe']); ?> people
                        </span>
                    </div>
                    <div class="p-5">
                        <?php if (empty($balances['others_owe'])): ?>
                            <div class="text-center py-8">
                                <i class="fas fa-check-circle text-5xl text-gray-300 mb-4"></i>
                                <p class="text-secondary">All settled up!</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-3">
                                <?php foreach ($balances['others_owe'] as $balance): ?>
                                <div class="flex items-center justify-between p-3 bg-white/50 rounded-xl">
                                    <div class="flex items-center">
                                        <div class="user-avatar w-10 h-10 rounded-full flex items-center justify-center font-semibold mr-3 text-sm">
                                            <?php echo strtoupper(substr($balance['username'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="font-medium text-primary text-sm"><?php echo htmlspecialchars($balance['display_name']); ?></div>
                                            <div class="text-xs text-secondary">@<?php echo htmlspecialchars($balance['username']); ?></div>
                                        </div>
                                    </div>
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full font-bold text-sm">
                                        +<?php echo number_format($balance['amount_owed'], 2); ?> €
                                    </span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- People I owe -->
                <div class="glass-card">
                    <div class="gradient-danger text-white p-5 rounded-t-3xl flex justify-between items-center">
                        <h3 class="font-bold text-lg flex items-center">
                            <i class="fas fa-credit-card mr-2"></i>Money Outgoing
                        </h3>
                        <span class="bg-white/20 text-white px-3 py-1 rounded-full text-xs font-medium">
                            <?php echo count($balances['user_owes']); ?> people
                        </span>
                    </div>
                    <div class="p-5">
                        <?php if (empty($balances['user_owes'])): ?>
                            <div class="text-center py-8">
                                <i class="fas fa-smile text-5xl text-gray-300 mb-4"></i>
                                <p class="text-secondary">You're debt-free!</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-3">
                                <?php foreach ($balances['user_owes'] as $balance): ?>
                                <div class="flex items-center justify-between p-3 bg-white/50 rounded-xl">
                                    <div class="flex items-center">
                                        <div class="user-avatar w-10 h-10 rounded-full flex items-center justify-center font-semibold mr-3 text-sm">
                                            <?php echo strtoupper(substr($balance['username'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="font-medium text-primary text-sm"><?php echo htmlspecialchars($balance['display_name']); ?></div>
                                            <div class="text-xs text-secondary">@<?php echo htmlspecialchars($balance['username']); ?></div>
                                        </div>
                                    </div>
                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full font-bold text-sm">
                                        -<?php echo number_format($balance['amount_owed'], 2); ?> €
                                    </span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Expenses -->
            <div class="glass-card">
                <div class="gradient-primary text-white p-5 rounded-t-3xl flex justify-between items-center">
                    <h3 class="font-bold text-lg flex items-center">
                        <i class="fas fa-history mr-2"></i>Recent Activity
                    </h3>
                    <span class="bg-white/20 text-white px-3 py-1 rounded-full text-xs font-medium">
                        <?php echo count($recentExpenses); ?> expenses
                    </span>
                </div>
                <div class="p-5">
                    <?php if (empty($recentExpenses)): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-receipt text-6xl text-gray-300 mb-6"></i>
                            <p class="text-secondary mb-4 text-lg">No expenses yet</p>
                            <a href="havetopay_add.php" class="btn-modern btn-primary">
                                <i class="fas fa-plus mr-2"></i>Add Your First Expense
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-glass rounded-xl overflow-hidden">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="text-left py-3 px-4 font-semibold text-primary text-sm">Expense</th>
                                        <th class="text-left py-3 px-4 font-semibold text-primary text-sm">Amount</th>
                                        <th class="text-left py-3 px-4 font-semibold text-primary text-sm">Paid By</th>
                                        <th class="text-left py-3 px-4 font-semibold text-primary text-sm">Date</th>
                                        <th class="text-left py-3 px-4 font-semibold text-primary text-sm">Participants</th>
                                        <th class="text-left py-3 px-4 font-semibold text-primary text-sm">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentExpenses as $expense): ?>
                                    <tr class="table-row">
                                        <td class="py-3 px-4">
                                            <div class="font-medium text-primary text-sm"><?php echo htmlspecialchars($expense['title']); ?></div>
                                            <?php if(!empty($expense['description'])): ?>
                                                <div class="text-xs text-secondary"><?php echo htmlspecialchars(mb_strimwidth($expense['description'], 0, 50, "...")); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3 px-4 font-bold text-indigo-600 text-sm"><?php echo number_format($expense['amount'], 2); ?> €</td>
                                        <td class="py-3 px-4">
                                            <div class="flex items-center">
                                                <div class="user-avatar w-7 h-7 rounded-full flex items-center justify-center text-xs font-medium mr-2">
                                                    <?php echo strtoupper(substr($expense['payer_name'], 0, 1)); ?>
                                                </div>
                                                <span class="text-xs text-primary"><?php echo htmlspecialchars($expense['payer_display_name']); ?></span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-xs text-secondary"><?php echo date('d M Y', strtotime($expense['expense_date'])); ?></td>
                                        <td class="py-3 px-4">
                                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-medium">
                                                <i class="fas fa-users mr-1"></i> 
                                                <?php echo $expense['participant_count']; ?> people
                                            </span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="flex gap-2">
                                                <a href="havetopay_detail.php?id=<?php echo $expense['id']; ?>" 
                                                   class="text-indigo-600 hover:text-indigo-800 font-medium text-xs">
                                                    <i class="fas fa-eye mr-1"></i>Details
                                                </a>
                                                <?php if ($expense['payer_id'] == $userId || ($_SESSION['is_admin'] ?? false)): ?>
                                                    <button type="button" 
                                                            class="text-red-600 hover:text-red-800 font-medium text-xs border-none bg-transparent cursor-pointer"
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
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="glass-card max-w-md mx-4 p-6">
            <div class="flex items-center mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl mr-3"></i>
                <h3 class="text-lg font-bold text-primary">Confirm Delete</h3>
            </div>
            <p class="text-secondary mb-6 text-sm">Are you sure you want to delete "<span id="expenseTitle" class="font-medium text-primary"></span>"? This action cannot be undone.</p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()" class="btn-modern btn-secondary">
                    Cancel
                </button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete_expense">
                    <input type="hidden" name="expense_id" id="deleteExpenseId" value="">
                    <button type="submit" class="btn-modern bg-red-600 hover:bg-red-700 text-white border-red-600">
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
