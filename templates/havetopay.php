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
        <div class="alert alert-success py-2 px-3 d-flex align-items-center mb-3 shadow-sm" style="border-radius: 8px;">
            <i class="fas fa-check-circle me-2"></i>
            <small><?php echo htmlspecialchars($successMessage); ?></small>
            <button type="button" class="btn-close btn-sm ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger py-2 px-3 d-flex align-items-center mb-3 shadow-sm" style="border-radius: 8px;">
            <i class="fas fa-exclamation-circle me-2"></i>
            <small><?php echo htmlspecialchars($errorMessage); ?></small>
            <button type="button" class="btn-close btn-sm ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <!-- Header Row -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="fas fa-wallet text-primary me-2"></i>
                HaveToPay
            </h5>
            <div>
                <a href="havetopay_add.php" class="btn btn-sm btn-primary rounded-pill shadow-sm">
                    <i class="fas fa-plus me-1"></i>Add
                </a>
            </div>
        </div>

        <!-- Balance Summary Card -->
        <div class="card shadow-sm mb-4 overflow-hidden" style="border-radius: 12px;">
            <div class="card-body p-0">
                <div class="row g-0">
                    <!-- Balance Visualization -->
                    <div class="col-12 col-md-8">
                        <div class="p-3">
                            <div class="d-flex justify-content-center align-items-center mb-2">
                                <span class="badge bg-light text-secondary px-3 py-1 rounded-pill">Net Balance</span>
                            </div>
                            
                            <!-- Balance Value -->
                            <h3 class="text-center mb-3 <?php echo $netBalance >= 0 ? 'text-success' : 'text-danger'; ?>">
                                <?php echo number_format($netBalance, 2); ?> €
                            </h3>
                            
                            <!-- Balance Bar -->
                            <div class="progress mb-2" style="height: 8px; border-radius: 4px; background-color: #f5f5f5;">
                                <?php
                                $totalAmount = abs($totalOwed) + abs($totalOwing);
                                $owedPercentage = $totalAmount > 0 ? (abs($totalOwed) / $totalAmount) * 100 : 0;
                                ?>
                                <div class="progress-bar bg-success" role="progressbar" 
                                    style="width: <?php echo $owedPercentage; ?>%" 
                                    aria-valuenow="<?php echo $owedPercentage; ?>" 
                                    aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar bg-danger" role="progressbar" 
                                    style="width: <?php echo 100 - $owedPercentage; ?>%" 
                                    aria-valuenow="<?php echo 100 - $owedPercentage; ?>" 
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            
                            <!-- Balance Legend -->
                            <div class="d-flex justify-content-between text-muted" style="font-size: 0.75rem;">
                                <div><i class="fas fa-arrow-down text-success me-1"></i> Owed to you</div>
                                <div><i class="fas fa-arrow-up text-danger me-1"></i> You owe</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Balance Details -->
                    <div class="col-12 col-md-4 bg-light">
                        <div class="p-3">
                            <div class="row g-0 text-center">
                                <div class="col-6 border-end">
                                    <div class="text-muted mb-1" style="font-size: 0.7rem;">INCOMING</div>
                                    <div class="text-success fw-semibold"><?php echo number_format($totalOwed, 2); ?> €</div>
                                    <div class="text-muted" style="font-size: 0.75rem;"><?php echo count($balances['others_owe']); ?> people</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted mb-1" style="font-size: 0.7rem;">OUTGOING</div>
                                    <div class="text-danger fw-semibold"><?php echo number_format($totalOwing, 2); ?> €</div>
                                    <div class="text-muted" style="font-size: 0.75rem;"><?php echo count($balances['user_owes']); ?> people</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Row -->
        <div class="row g-3 mb-4">
            <!-- People Who Owe You -->
            <div class="col-12 col-md-6">
                <div class="card shadow-sm h-100" style="border-radius: 12px; border-top: 3px solid #10b981;">
                    <div class="card-header bg-transparent border-0 py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="fas fa-arrow-down text-success me-2"></i>
                                People Who Owe You
                            </h6>
                            <span class="badge bg-success rounded-pill">
                                <?php echo count($balances['others_owe']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0" style="max-height: 250px; overflow-y: auto;">
                        <?php if (empty($balances['others_owe'])): ?>
                            <div class="text-center py-3">
                                <i class="fas fa-check-circle text-muted mb-2" style="font-size: 1.5rem;"></i>
                                <p class="text-muted small">No one owes you money at the moment.</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($balances['others_owe'] as $balance): ?>
                                <div class="list-group-item border-0 py-2 px-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center me-2"
                                                 style="width: 28px; height: 28px; background-color: #10b981; font-size: 0.7rem;">
                                                <?php echo strtoupper(substr($balance['username'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="fw-medium" style="font-size: 0.85rem;"><?php echo htmlspecialchars($balance['display_name']); ?></div>
                                                <div class="text-muted" style="font-size: 0.7rem;">@<?php echo htmlspecialchars($balance['username']); ?></div>
                                            </div>
                                        </div>
                                        <span class="text-success fw-medium"><?php echo number_format($balance['amount_owed'], 2); ?> €</span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- People You Owe -->
            <div class="col-12 col-md-6">
                <div class="card shadow-sm h-100" style="border-radius: 12px; border-top: 3px solid #ef4444;">
                    <div class="card-header bg-transparent border-0 py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="fas fa-arrow-up text-danger me-2"></i>
                                People You Owe
                            </h6>
                            <span class="badge bg-danger rounded-pill">
                                <?php echo count($balances['user_owes']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0" style="max-height: 250px; overflow-y: auto;">
                        <?php if (empty($balances['user_owes'])): ?>
                            <div class="text-center py-3">
                                <i class="fas fa-smile text-muted mb-2" style="font-size: 1.5rem;"></i>
                                <p class="text-muted small">You don't owe anyone money at the moment.</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($balances['user_owes'] as $balance): ?>
                                <div class="list-group-item border-0 py-2 px-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center me-2"
                                                 style="width: 28px; height: 28px; background-color: #ef4444; font-size: 0.7rem;">
                                                <?php echo strtoupper(substr($balance['username'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="fw-medium" style="font-size: 0.85rem;"><?php echo htmlspecialchars($balance['display_name']); ?></div>
                                                <div class="text-muted" style="font-size: 0.7rem;">@<?php echo htmlspecialchars($balance['username']); ?></div>
                                            </div>
                                        </div>
                                        <span class="text-danger fw-medium"><?php echo number_format($balance['amount_owed'], 2); ?> €</span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Expenses -->
        <div class="card shadow-sm mb-3" style="border-radius: 12px;">
            <div class="card-header bg-transparent border-0 py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-receipt text-primary me-2"></i>
                        Recent Expenses
                    </h6>
                    <span class="badge bg-primary rounded-pill">
                        <?php echo count($recentExpenses); ?>
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentExpenses)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-receipt text-muted mb-2" style="font-size: 1.5rem;"></i>
                        <p class="text-muted">No expenses recorded yet.</p>
                        <a href="havetopay_add.php" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus me-1"></i>Add First Expense
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm m-0" style="font-size: 0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Expense</th>
                                    <th>Amount</th>
                                    <th>Paid By</th>
                                    <th>Date</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentExpenses as $expense): ?>
                                <tr>
                                    <td class="ps-3">
                                        <?php echo htmlspecialchars($expense['title']); ?>
                                    </td>
                                    <td class="fw-medium"><?php echo number_format($expense['amount'], 2); ?> €</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-1" 
                                                 style="width: 20px; height: 20px; font-size: 0.65rem;">
                                                <?php echo strtoupper(substr($expense['payer_name'], 0, 1)); ?>
                                            </div>
                                            <span class="text-truncate" style="max-width: 100px;">
                                                <?php echo htmlspecialchars($expense['payer_display_name']); ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td><?php echo date('d.m.y', strtotime($expense['expense_date'])); ?></td>
                                    <td class="text-end pe-3">
                                        <a href="havetopay_detail.php?id=<?php echo $expense['id']; ?>" 
                                           class="btn btn-sm btn-outline-secondary px-1 py-0">
                                            <i class="fas fa-eye"></i>
                                        </a>
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
