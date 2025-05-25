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
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --danger-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            --purple-gradient: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            --blue-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --glass-bg: rgba(255, 255, 255, 0.25);
            --glass-border: rgba(255, 255, 255, 0.18);
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            padding: 2rem;
        }
        
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }
        
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        
        .glass-card:hover {
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 15px 40px rgba(31, 38, 135, 0.5);
        }
        
        .gradient-primary {
            background: var(--primary-gradient);
            position: relative;
            overflow: hidden;
        }
        
        .gradient-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 100%);
            pointer-events: none;
        }
        
        .gradient-success {
            background: var(--success-gradient);
            position: relative;
            overflow: hidden;
        }
        
        .gradient-success::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 100%);
            pointer-events: none;
        }
        
        .gradient-danger {
            background: var(--danger-gradient);
            position: relative;
            overflow: hidden;
        }
        
        .gradient-danger::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 100%);
            pointer-events: none;
        }
        
        .btn-modern {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
        }
        
        .btn-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }
        
        .btn-modern:hover::before {
            left: 100%;
        }
        
        .btn-modern:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            background: rgba(255, 255, 255, 0.3);
        }
        
        .btn-primary {
            background: var(--primary-gradient);
            color: white;
            border: none;
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.15);
            color: white;
        }
        
        .balance-card {
            animation: slideInUp 0.8s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        
        .metric-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            padding: 20px;
            transition: all 0.3s ease;
        }
        
        .metric-card:hover {
            transform: translateY(-4px);
            background: rgba(255, 255, 255, 0.25);
        }
        
        .user-avatar {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .status-badge {
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .table-glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border-radius: 16px;
        }
        
        .table-row {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .table-row:hover {
            background: rgba(255, 255, 255, 0.1);
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
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .floating {
            animation: float 6s ease-in-out infinite;
        }
        
        /* Improved text readability */
        .text-readable {
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }
        
        .text-readable-light {
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
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
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="main-content">
            <!-- Success/Error Messages -->
            <?php if (!empty($successMessage)): ?>
            <div class="glass-card mb-4 p-4 text-white text-readable">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3 text-green-300"></i>
                    <?php echo htmlspecialchars($successMessage); ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($errorMessage)): ?>
            <div class="glass-card mb-4 p-4 text-white text-readable">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-3 text-red-300"></i>
                    <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-white flex items-center floating text-readable">
                    <i class="fas fa-wallet mr-3 text-yellow-300"></i>HaveToPay
                </h1>
                <div class="flex gap-3">
                    <a href="havetopay_add.php" class="btn-modern btn-primary flex items-center text-readable-light">
                        <i class="fas fa-plus mr-2"></i>Add Expense
                    </a>
                    <a href="index.php" class="btn-modern btn-secondary flex items-center text-readable-light">
                        <i class="fas fa-home mr-2"></i>Dashboard
                    </a>
                </div>
            </div>

            <!-- Balance Summary Card -->
            <div class="glass-card balance-card mb-6">
                <div class="gradient-primary text-white p-6 rounded-t-3xl">
                    <h2 class="text-xl font-bold flex items-center text-readable">
                        <i class="fas fa-chart-line mr-3"></i>Balance Overview
                    </h2>
                    <p class="opacity-90 mt-1 text-sm text-readable-light">Track your financial balance with friends</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="metric-card text-center">
                            <div class="text-white/80 text-xs font-medium mb-2 text-readable-light">You are owed</div>
                            <div class="text-3xl font-bold text-green-300 mb-2 text-readable"><?php echo number_format($totalOwed, 2); ?> €</div>
                            <div class="w-10 h-0.5 bg-green-300 rounded-full mx-auto"></div>
                        </div>
                        
                        <div class="metric-card text-center">
                            <div class="text-white/80 text-xs font-medium mb-2 text-readable-light">Net Balance</div>
                            <div class="text-3xl font-bold <?php echo $netBalance >= 0 ? 'text-green-300' : 'text-red-300'; ?> mb-2 text-readable">
                                <?php echo number_format($netBalance, 2); ?> €
                            </div>
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium status-badge <?php echo $netBalance >= 0 ? 'text-green-300' : 'text-red-300'; ?> text-readable-light">
                                <i class="fas fa-arrow-<?php echo $netBalance >= 0 ? 'up' : 'down'; ?> mr-1"></i>
                                <?php echo $netBalance >= 0 ? 'Positive' : 'Negative'; ?>
                            </div>
                        </div>
                        
                        <div class="metric-card text-center">
                            <div class="text-white/80 text-xs font-medium mb-2 text-readable-light">You owe</div>
                            <div class="text-3xl font-bold text-red-300 mb-2 text-readable"><?php echo number_format($totalOwing, 2); ?> €</div>
                            <div class="w-10 h-0.5 bg-red-300 rounded-full mx-auto"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- People who owe me -->
                <div class="glass-card">
                    <div class="gradient-success text-white p-5 rounded-t-3xl flex justify-between items-center">
                        <h3 class="font-bold text-lg flex items-center text-readable">
                            <i class="fas fa-hand-holding-usd mr-2"></i>Money Incoming
                        </h3>
                        <span class="bg-white/20 backdrop-filter backdrop-blur-lg text-white px-3 py-1 rounded-full text-xs font-medium text-readable-light">
                            <?php echo count($balances['others_owe']); ?> people
                        </span>
                    </div>
                    <div class="p-5">
                        <?php if (empty($balances['others_owe'])): ?>
                            <div class="text-center py-8">
                                <i class="fas fa-check-circle text-5xl text-white/30 mb-4"></i>
                                <p class="text-white/80 text-readable-light">All settled up!</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-3">
                                <?php foreach ($balances['others_owe'] as $balance): ?>
                                <div class="flex items-center justify-between p-3 bg-white/10 rounded-xl hover:bg-white/20 transition-all duration-300">
                                    <div class="flex items-center">
                                        <div class="user-avatar w-10 h-10 rounded-full flex items-center justify-center font-semibold mr-3 text-white text-sm">
                                            <?php echo strtoupper(substr($balance['username'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="font-medium text-white text-sm text-readable"><?php echo htmlspecialchars($balance['display_name']); ?></div>
                                            <div class="text-xs text-white/60 text-readable-light">@<?php echo htmlspecialchars($balance['username']); ?></div>
                                        </div>
                                    </div>
                                    <span class="bg-green-300/20 text-green-300 px-3 py-1 rounded-full font-bold text-sm text-readable">
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
                        <h3 class="font-bold text-lg flex items-center text-readable">
                            <i class="fas fa-credit-card mr-2"></i>Money Outgoing
                        </h3>
                        <span class="bg-white/20 backdrop-filter backdrop-blur-lg text-white px-3 py-1 rounded-full text-xs font-medium text-readable-light">
                            <?php echo count($balances['user_owes']); ?> people
                        </span>
                    </div>
                    <div class="p-5">
                        <?php if (empty($balances['user_owes'])): ?>
                            <div class="text-center py-8">
                                <i class="fas fa-smile text-5xl text-white/30 mb-4"></i>
                                <p class="text-white/80 text-readable-light">You're debt-free!</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-3">
                                <?php foreach ($balances['user_owes'] as $balance): ?>
                                <div class="flex items-center justify-between p-3 bg-white/10 rounded-xl hover:bg-white/20 transition-all duration-300">
                                    <div class="flex items-center">
                                        <div class="user-avatar w-10 h-10 rounded-full flex items-center justify-center font-semibold mr-3 text-white text-sm">
                                            <?php echo strtoupper(substr($balance['username'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="font-medium text-white text-sm text-readable"><?php echo htmlspecialchars($balance['display_name']); ?></div>
                                            <div class="text-xs text-white/60 text-readable-light">@<?php echo htmlspecialchars($balance['username']); ?></div>
                                        </div>
                                    </div>
                                    <span class="bg-red-300/20 text-red-300 px-3 py-1 rounded-full font-bold text-sm text-readable">
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
                    <h3 class="font-bold text-lg flex items-center text-readable">
                        <i class="fas fa-history mr-2"></i>Recent Activity
                    </h3>
                    <span class="bg-white/20 backdrop-filter backdrop-blur-lg text-white px-3 py-1 rounded-full text-xs font-medium text-readable-light">
                        <?php echo count($recentExpenses); ?> expenses
                    </span>
                </div>
                <div class="p-5">
                    <?php if (empty($recentExpenses)): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-receipt text-6xl text-white/20 mb-6"></i>
                            <p class="text-white/80 mb-4 text-lg text-readable">No expenses yet</p>
                            <a href="havetopay_add.php" class="btn-modern btn-primary text-readable-light">
                                <i class="fas fa-plus mr-2"></i>Add Your First Expense
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-glass rounded-xl overflow-hidden">
                            <table class="w-full">
                                <thead class="bg-white/10">
                                    <tr>
                                        <th class="text-left py-3 px-4 font-semibold text-white text-sm text-readable">Expense</th>
                                        <th class="text-left py-3 px-4 font-semibold text-white text-sm text-readable">Amount</th>
                                        <th class="text-left py-3 px-4 font-semibold text-white text-sm text-readable">Paid By</th>
                                        <th class="text-left py-3 px-4 font-semibold text-white text-sm text-readable">Date</th>
                                        <th class="text-left py-3 px-4 font-semibold text-white text-sm text-readable">Participants</th>
                                        <th class="text-left py-3 px-4 font-semibold text-white text-sm text-readable">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentExpenses as $expense): ?>
                                    <tr class="table-row">
                                        <td class="py-3 px-4">
                                            <div class="font-medium text-white text-sm text-readable"><?php echo htmlspecialchars($expense['title']); ?></div>
                                            <?php if(!empty($expense['description'])): ?>
                                                <div class="text-xs text-white/60 text-readable-light"><?php echo htmlspecialchars(mb_strimwidth($expense['description'], 0, 50, "...")); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3 px-4 font-bold text-yellow-300 text-sm text-readable"><?php echo number_format($expense['amount'], 2); ?> €</td>
                                        <td class="py-3 px-4">
                                            <div class="flex items-center">
                                                <div class="user-avatar w-7 h-7 rounded-full flex items-center justify-center text-xs font-medium mr-2 text-white">
                                                    <?php echo strtoupper(substr($expense['payer_name'], 0, 1)); ?>
                                                </div>
                                                <span class="text-xs text-white text-readable"><?php echo htmlspecialchars($expense['payer_display_name']); ?></span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-xs text-white/70 text-readable-light"><?php echo date('d M Y', strtotime($expense['expense_date'])); ?></td>
                                        <td class="py-3 px-4">
                                            <span class="bg-blue-300/20 text-blue-300 px-2 py-1 rounded-full text-xs font-medium text-readable-light">
                                                <i class="fas fa-users mr-1"></i> 
                                                <?php echo $expense['participant_count']; ?> people
                                            </span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="flex gap-2">
                                                <a href="havetopay_detail.php?id=<?php echo $expense['id']; ?>" 
                                                   class="text-blue-300 hover:text-blue-200 font-medium text-xs transition-colors text-readable-light">
                                                    <i class="fas fa-eye mr-1"></i>Details
                                                </a>
                                                <?php if ($expense['payer_id'] == $userId || ($_SESSION['is_admin'] ?? false)): ?>
                                                    <button type="button" 
                                                            class="text-red-300 hover:text-red-200 font-medium text-xs border-none bg-transparent cursor-pointer transition-colors text-readable-light"
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
                <i class="fas fa-exclamation-triangle text-red-300 text-2xl mr-3"></i>
                <h3 class="text-lg font-bold text-white text-readable">Confirm Delete</h3>
            </div>
            <p class="text-white/80 mb-6 text-sm text-readable-light">Are you sure you want to delete "<span id="expenseTitle" class="font-medium text-yellow-300"></span>"? This action cannot be undone.</p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()" class="btn-modern btn-secondary text-readable-light">
                    Cancel
                </button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete_expense">
                    <input type="hidden" name="expense_id" id="deleteExpenseId" value="">
                    <button type="submit" class="btn-modern bg-red-500/80 hover:bg-red-500 text-white border-red-400/50 text-readable-light">
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
