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
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            --danger-gradient: linear-gradient(135deg, #f56565 0%, #c53030 100%);
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            --hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        .glass-card {
            background-color: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }
        
        .gradient-primary {
            background: var(--primary-gradient);
        }
        
        .gradient-success {
            background: var(--success-gradient);
        }
        
        .gradient-danger {
            background: var(--danger-gradient);
        }
        
        .apple-btn {
            border-radius: 16px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
            display: flex;
            align-items: center;
        }
        
        .apple-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        
        .apple-btn-primary {
            background: var(--primary-gradient);
            color: white;
        }
        
        .apple-btn-secondary {
            background: rgba(255, 255, 255, 0.8);
            color: #1a202c;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        
        .balance-card {
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animated-bg {
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }
        
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
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
        
        /* Table styling */
        .modern-table th {
            color: #4a5568;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.75rem;
            padding: 1rem;
        }
        
        .modern-table tbody tr {
            transition: all 0.2s ease;
        }
        
        .modern-table tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }
        
        /* Avatar styling */
        .avatar {
            background: var(--primary-gradient);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-transform: uppercase;
            font-weight: 600;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen">
    <?php require_once __DIR__ . '/../templates/navbar.php'; ?>
    
    <div class="main-content p-6 animated-bg">
        <!-- Success/Error Messages -->
        <?php if (!empty($successMessage)): ?>
        <div class="glass-card border-l-4 border-green-500 p-4 mb-6 flex items-center">
            <i class="fas fa-check-circle mr-3 text-green-500 text-xl"></i>
            <p class="text-green-700"><?php echo htmlspecialchars($successMessage); ?></p>
        </div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
        <div class="glass-card border-l-4 border-red-500 p-4 mb-6 flex items-center">
            <i class="fas fa-exclamation-circle mr-3 text-red-500 text-xl"></i>
            <p class="text-red-700"><?php echo htmlspecialchars($errorMessage); ?></p>
        </div>
        <?php endif; ?>

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-wallet mr-3 text-indigo-600"></i>HaveToPay
            </h1>
            <div class="flex gap-3">
                <a href="havetopay_add.php" class="apple-btn apple-btn-primary">
                    <i class="fas fa-plus mr-2"></i>Add Expense
                </a>
                <a href="index.php" class="apple-btn apple-btn-secondary">
                    <i class="fas fa-home mr-2"></i>Dashboard
                </a>
            </div>
        </div>

        <!-- Balance Summary Card -->
        <div class="glass-card balance-card mb-8">
            <div class="gradient-primary text-white p-6 rounded-t-3xl">
                <h2 class="text-xl font-bold">Your Balance Summary</h2>
            </div>
            <div class="p-6 backdrop-blur-sm">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                    <div>
                        <div class="text-gray-600 text-sm font-medium">You are owed</div>
                        <div class="text-3xl font-bold text-green-600 mt-2"><?php echo number_format($totalOwed, 2); ?> €</div>
                    </div>
                    
                    <div>
                        <div class="text-gray-600 text-sm font-medium">Your net balance</div>
                        <div class="text-3xl font-bold <?php echo $netBalance >= 0 ? 'text-green-600' : 'text-red-600'; ?> mt-2">
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
                        <div class="text-3xl font-bold text-red-600 mt-2"><?php echo number_format($totalOwing, 2); ?> €</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- People who owe me -->
            <div class="glass-card">
                <div class="gradient-success text-white p-4 rounded-t-3xl flex justify-between items-center">
                    <h3 class="font-bold">People Who Owe You</h3>
                    <span class="bg-white text-green-600 px-3 py-1 rounded-full text-sm font-medium shadow">
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
                            <div class="flex items-center justify-between p-3 bg-white/50 backdrop-blur-sm rounded-2xl hover:bg-white/70 transition-colors">
                                <div class="flex items-center">
                                    <div class="avatar w-10 h-10 mr-3">
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
            <div class="glass-card">
                <div class="gradient-danger text-white p-4 rounded-t-3xl flex justify-between items-center">
                    <h3 class="font-bold">People You Owe</h3>
                    <span class="bg-white text-red-600 px-3 py-1 rounded-full text-sm font-medium shadow">
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
                            <div class="flex items-center justify-between p-3 bg-white/50 backdrop-blur-sm rounded-2xl hover:bg-white/70 transition-colors">
                                <div class="flex items-center">
                                    <div class="avatar w-10 h-10 mr-3" style="background: linear-gradient(135deg, #f56565 0%, #c53030 100%);">
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
        <div class="glass-card">
            <div class="gradient-primary text-white p-4 rounded-t-3xl flex justify-between items-center">
                <h3 class="font-bold">Recent Expenses</h3>
                <span class="bg-white text-indigo-600 px-3 py-1 rounded-full text-sm font-medium shadow">
                    <?php echo count($recentExpenses); ?> expenses
                </span>
            </div>
            <div class="p-4">
                <?php if (empty($recentExpenses)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-receipt text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 mb-4">No expenses recorded yet.</p>
                        <a href="havetopay_add.php" class="apple-btn apple-btn-primary inline-block">
                            <i class="fas fa-plus mr-2"></i>Add Your First Expense
                        </a>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full modern-table">
                            <thead class="border-b border-gray-100">
                                <tr>
                                    <th class="text-left">Expense</th>
                                    <th class="text-left">Amount</th>
                                    <th class="text-left">Paid By</th>
                                    <th class="text-left">Date</th>
                                    <th class="text-left">Participants</th>
                                    <th class="text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php foreach ($recentExpenses as $expense): ?>
                                <tr class="hover:bg-gray-50/50 backdrop-blur-sm">
                                    <td class="py-4 px-3">
                                        <div class="font-medium"><?php echo htmlspecialchars($expense['title']); ?></div>
                                        <?php if(!empty($expense['description'])): ?>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars(mb_strimwidth($expense['description'], 0, 50, "...")); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4 px-3 font-semibold"><?php echo number_format($expense['amount'], 2); ?> €</td>
                                    <td class="py-4 px-3">
                                        <div class="flex items-center">
                                            <div class="avatar w-7 h-7 mr-2">
                                                <?php echo strtoupper(substr($expense['payer_name'], 0, 1)); ?>
                                            </div>
                                            <span class="text-sm"><?php echo htmlspecialchars($expense['payer_display_name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-3 text-sm text-gray-600"><?php echo date('d M Y', strtotime($expense['expense_date'])); ?></td>
                                    <td class="py-4 px-3">
                                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-medium">
                                            <i class="fas fa-users mr-1"></i> 
                                            <?php echo $expense['participant_count']; ?> people
                                        </span>
                                    </td>
                                    <td class="py-4 px-3">
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
    <div id="deleteModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white/80 backdrop-blur-md rounded-3xl p-6 max-w-md mx-4 shadow-xl border border-white/20">
            <div class="flex items-center mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl mr-3"></i>
                <h3 class="text-lg font-semibold">Confirm Delete</h3>
            </div>
            <p class="text-gray-600 mb-6">Are you sure you want to delete "<span id="expenseTitle" class="font-medium"></span>"? This action cannot be undone.</p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-gray-600 bg-gray-200 rounded-xl hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete_expense">
                    <input type="hidden" name="expense_id" id="deleteExpenseId" value="">
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors">
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
