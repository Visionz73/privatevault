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
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?php echo htmlspecialchars($successMessage); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?php echo htmlspecialchars($errorMessage); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">
                    <i class="fas fa-wallet me-3" style="color: var(--primary-color);"></i>
                    HaveToPay
                </h1>
                <p class="text-muted mb-0">Manage and track shared expenses</p>
            </div>
            <div class="d-flex gap-2">
                <a href="havetopay_add.php" class="btn btn-modern btn-modern-primary">
                    <i class="fas fa-plus me-2"></i>Add Expense
                </a>
            </div>
        </div>

        <!-- Balance Summary Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="modern-card">
                    <div class="card-header-modern">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Your Balance Summary</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="text-muted mb-2">You are owed</div>
                                <h3 class="text-success mb-0 fw-bold"><?php echo number_format($totalOwed, 2); ?> €</h3>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted mb-2">Your net balance</div>
                                <h3 class="<?php echo $netBalance >= 0 ? 'text-success' : 'text-danger'; ?> mb-2 fw-bold">
                                    <?php echo number_format($netBalance, 2); ?> €
                                </h3>
                                <span class="badge <?php echo $netBalance >= 0 ? 'bg-success' : 'bg-danger'; ?> rounded-pill">
                                    <?php echo $netBalance >= 0 ? 'Positive' : 'Negative'; ?> Balance
                                </span>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted mb-2">You owe</div>
                                <h3 class="text-danger mb-0 fw-bold"><?php echo number_format($totalOwing, 2); ?> €</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Balance Cards -->
        <div class="row mb-4">
            <!-- People who owe me -->
            <div class="col-lg-6 mb-4">
                <div class="modern-card h-100">
                    <div class="card-header bg-success text-white p-3">
                        <h6 class="mb-0 d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-arrow-down me-2"></i>People Who Owe You</span>
                            <span class="badge bg-light text-success"><?php echo count($balances['others_owe']); ?></span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($balances['others_owe'])): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No one owes you money at the moment.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($balances['others_owe'] as $balance): ?>
                            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 45px; height: 45px; font-weight: 600;">
                                            <?php echo strtoupper(substr($balance['username'], 0, 1)); ?>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($balance['display_name']); ?></h6>
                                        <small class="text-muted">@<?php echo htmlspecialchars($balance['username']); ?></small>
                                    </div>
                                </div>
                                <span class="badge bg-success-subtle text-success fs-6 px-3 py-2">
                                    <?php echo number_format($balance['amount_owed'], 2); ?> €
                                </span>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- People I owe -->
            <div class="col-lg-6 mb-4">
                <div class="modern-card h-100">
                    <div class="card-header bg-danger text-white p-3">
                        <h6 class="mb-0 d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-arrow-up me-2"></i>People You Owe</span>
                            <span class="badge bg-light text-danger"><?php echo count($balances['user_owes']); ?></span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($balances['user_owes'])): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-smile fa-3x text-muted mb-3"></i>
                                <p class="text-muted">You don't owe anyone money at the moment.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($balances['user_owes'] as $balance): ?>
                            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 45px; height: 45px; font-weight: 600;">
                                            <?php echo strtoupper(substr($balance['username'], 0, 1)); ?>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($balance['display_name']); ?></h6>
                                        <small class="text-muted">@<?php echo htmlspecialchars($balance['username']); ?></small>
                                    </div>
                                </div>
                                <span class="badge bg-danger-subtle text-danger fs-6 px-3 py-2">
                                    <?php echo number_format($balance['amount_owed'], 2); ?> €
                                </span>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Expenses -->
        <div class="row">
            <div class="col-12">
                <div class="modern-card">
                    <div class="card-header-modern">
                        <h6 class="mb-0 d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-receipt me-2"></i>Recent Expenses</span>
                            <span class="badge bg-light" style="color: var(--primary-color);"><?php echo count($recentExpenses); ?> expenses</span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($recentExpenses)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No expenses recorded yet.</p>
                                <a href="havetopay_add.php" class="btn btn-modern btn-modern-primary">
                                    <i class="fas fa-plus me-2"></i>Add Your First Expense
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0 fw-semibold">Expense</th>
                                            <th class="border-0 fw-semibold">Amount</th>
                                            <th class="border-0 fw-semibold">Paid By</th>
                                            <th class="border-0 fw-semibold">Date</th>
                                            <th class="border-0 fw-semibold">Participants</th>
                                            <th class="border-0 fw-semibold">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentExpenses as $expense): ?>
                                        <tr>
                                            <td class="py-3">
                                                <strong><?php echo htmlspecialchars($expense['title']); ?></strong>
                                                <?php if(!empty($expense['description'])): ?>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars(mb_strimwidth($expense['description'], 0, 50, "...")); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3 fw-bold"><?php echo number_format($expense['amount'], 2); ?> €</td>
                                            <td class="py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                         style="width: 32px; height: 32px; font-size: 0.8rem; font-weight: 600;">
                                                        <?php echo strtoupper(substr($expense['payer_name'], 0, 1)); ?>
                                                    </div>
                                                    <span><?php echo htmlspecialchars($expense['payer_display_name']); ?></span>
                                                </div>
                                            </td>
                                            <td class="py-3"><?php echo date('d M Y', strtotime($expense['expense_date'])); ?></td>
                                            <td class="py-3">
                                                <span class="badge bg-info-subtle text-info px-3 py-2">
                                                    <i class="fas fa-users me-1"></i> 
                                                    <?php echo $expense['participant_count']; ?> people
                                                </span>
                                            </td>
                                            <td class="py-3">
                                                <a href="havetopay_detail.php?id=<?php echo $expense['id']; ?>" 
                                                   class="btn btn-sm btn-outline-primary rounded-pill">
                                                    <i class="fas fa-eye me-1"></i>Details
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

        // Close sidebar on mobile when clicking outside
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.mobile-toggle');
            
            if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });
    </script>
</body>
</html>
