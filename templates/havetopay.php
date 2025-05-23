<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HaveToPay | PrivateVault</title>
    <link href="https://fonts.googleapis.com/css2?family=SF+Pro+Display:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --sf-blue: #007AFF;
            --sf-green: #34C759;
            --sf-red: #FF3B30;
            --sf-orange: #FF9500;
            --sf-gray: #8E8E93;
            --sf-gray-light: #F2F2F7;
            --sf-gray-medium: #E5E5EA;
            --sf-gray-dark: #3A3A3C;
            --sf-background: #FFFFFF;
            --sf-secondary-background: #F2F2F7;
            --sf-text-primary: #000000;
            --sf-text-secondary: #8E8E93;
            --sf-divider: #E5E5EA;
        }

        body {
            font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--sf-secondary-background);
            color: var(--sf-text-primary);
            margin: 0;
            padding: 0;
            line-height: 1.4;
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

        .main-content {
            padding: 24px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Apple-style cards */
        .card {
            background: var(--sf-background);
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: none;
            overflow: hidden;
            margin-bottom: 24px;
        }

        .card-header {
            background: var(--sf-background);
            border-bottom: 1px solid var(--sf-divider);
            padding: 20px 24px;
            font-weight: 600;
            font-size: 20px;
            color: var(--sf-text-primary);
        }

        .card-body {
            padding: 24px;
        }

        /* Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            padding: 0 8px;
        }

        .page-title {
            font-size: 34px;
            font-weight: 700;
            color: var(--sf-text-primary);
            margin: 0;
        }

        .header-actions {
            display: flex;
            gap: 12px;
        }

        /* Apple-style buttons */
        .btn {
            border-radius: 12px;
            padding: 12px 20px;
            font-weight: 500;
            font-size: 16px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: var(--sf-blue);
            color: white;
        }

        .btn-secondary {
            background: var(--sf-gray-light);
            color: var(--sf-text-primary);
        }

        .btn:hover {
            opacity: 0.8;
        }

        /* Balance summary */
        .balance-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1px;
            background: var(--sf-divider);
            border-radius: 16px;
            overflow: hidden;
        }

        .balance-item {
            background: var(--sf-background);
            padding: 24px;
            text-align: center;
        }

        .balance-label {
            font-size: 14px;
            color: var(--sf-text-secondary);
            margin-bottom: 8px;
            font-weight: 500;
        }

        .balance-amount {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .balance-amount.positive {
            color: var(--sf-green);
        }

        .balance-amount.negative {
            color: var(--sf-red);
        }

        .balance-amount.neutral {
            color: var(--sf-text-primary);
        }

        .balance-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .balance-badge.positive {
            background: rgba(52, 199, 89, 0.1);
            color: var(--sf-green);
        }

        .balance-badge.negative {
            background: rgba(255, 59, 48, 0.1);
            color: var(--sf-red);
        }

        /* Lists */
        .list-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .list-item {
            background: var(--sf-background);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--sf-divider);
        }

        .list-item:last-child {
            border-bottom: none;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 20px;
            background: var(--sf-blue);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
        }

        .avatar.owe {
            background: var(--sf-red);
        }

        .user-details h6 {
            margin: 0;
            font-weight: 600;
            font-size: 16px;
            color: var(--sf-text-primary);
        }

        .user-details small {
            color: var(--sf-text-secondary);
            font-size: 14px;
        }

        .amount-badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
        }

        .amount-badge.positive {
            background: rgba(52, 199, 89, 0.1);
            color: var(--sf-green);
        }

        .amount-badge.negative {
            background: rgba(255, 59, 48, 0.1);
            color: var(--sf-red);
        }

        /* Empty states */
        .empty-state {
            text-align: center;
            padding: 48px 24px;
        }

        .empty-state i {
            font-size: 48px;
            color: var(--sf-gray);
            margin-bottom: 16px;
        }

        .empty-state p {
            color: var(--sf-text-secondary);
            font-size: 16px;
            margin: 0;
        }

        /* Table */
        .table-container {
            overflow-x: auto;
            border-radius: 12px;
            background: var(--sf-background);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background: var(--sf-gray-light);
            padding: 16px 20px;
            text-align: left;
            font-weight: 600;
            color: var(--sf-text-primary);
            font-size: 14px;
            border-bottom: 1px solid var(--sf-divider);
        }

        .table td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--sf-divider);
            vertical-align: middle;
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .table tr:hover {
            background: rgba(0, 122, 255, 0.05);
        }

        .expense-title {
            font-weight: 600;
            color: var(--sf-text-primary);
            margin-bottom: 4px;
        }

        .expense-description {
            color: var(--sf-text-secondary);
            font-size: 14px;
        }

        .tag {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            background: var(--sf-gray-light);
            color: var(--sf-text-secondary);
        }

        .tag.participants {
            background: rgba(0, 122, 255, 0.1);
            color: var(--sf-blue);
        }

        /* Action buttons */
        .action-link {
            color: var(--sf-blue);
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
        }

        .action-link:hover {
            text-decoration: underline;
        }

        .action-link.danger {
            color: var(--sf-red);
        }

        /* Alerts */
        .alert {
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            border: none;
        }

        .alert.success {
            background: rgba(52, 199, 89, 0.1);
            color: var(--sf-green);
        }

        .alert.error {
            background: rgba(255, 59, 48, 0.1);
            color: var(--sf-red);
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .main-content {
                padding: 16px;
            }

            .page-header {
                flex-direction: column;
                align-items: stretch;
                gap: 16px;
            }

            .header-actions {
                flex-direction: column;
            }

            .list-container {
                grid-template-columns: 1fr;
            }

            .balance-grid {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <!-- Alerts -->
        <?php if (!empty($successMessage)): ?>
        <div class="alert success">
            <i class="fas fa-check-circle"></i>
            <?php echo htmlspecialchars($successMessage); ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
        <div class="alert error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars($errorMessage); ?>
        </div>
        <?php endif; ?>

        <!-- Header -->
        <div class="page-header">
            <h1 class="page-title">HaveToPay</h1>
            <div class="header-actions">
                <a href="havetopay_add.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Add Expense
                </a>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-home"></i>
                    Dashboard
                </a>
            </div>
        </div>

        <!-- Balance Summary -->
        <div class="card">
            <div class="card-header">Balance Overview</div>
            <div class="card-body">
                <div class="balance-grid">
                    <div class="balance-item">
                        <div class="balance-label">You are owed</div>
                        <div class="balance-amount positive">€<?php echo number_format($totalOwed, 2); ?></div>
                    </div>
                    <div class="balance-item">
                        <div class="balance-label">Net Balance</div>
                        <div class="balance-amount <?php echo $netBalance >= 0 ? 'positive' : 'negative'; ?>">
                            €<?php echo number_format($netBalance, 2); ?>
                        </div>
                        <div class="balance-badge <?php echo $netBalance >= 0 ? 'positive' : 'negative'; ?>">
                            <?php echo $netBalance >= 0 ? 'Positive' : 'Negative'; ?>
                        </div>
                    </div>
                    <div class="balance-item">
                        <div class="balance-label">You owe</div>
                        <div class="balance-amount negative">€<?php echo number_format($totalOwing, 2); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Balance Lists -->
        <div class="list-container">
            <!-- People who owe you -->
            <div class="card">
                <div class="card-header">
                    People Who Owe You
                    <span class="tag"><?php echo count($balances['others_owe']); ?> people</span>
                </div>
                <div class="card-body">
                    <?php if (empty($balances['others_owe'])): ?>
                        <div class="empty-state">
                            <i class="fas fa-check-circle"></i>
                            <p>No one owes you money</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($balances['others_owe'] as $balance): ?>
                        <div class="list-item">
                            <div class="user-info">
                                <div class="avatar">
                                    <?php echo strtoupper(substr($balance['username'], 0, 1)); ?>
                                </div>
                                <div class="user-details">
                                    <h6><?php echo htmlspecialchars($balance['display_name']); ?></h6>
                                    <small>@<?php echo htmlspecialchars($balance['username']); ?></small>
                                </div>
                            </div>
                            <div class="amount-badge positive">
                                €<?php echo number_format($balance['amount_owed'], 2); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- People you owe -->
            <div class="card">
                <div class="card-header">
                    People You Owe
                    <span class="tag"><?php echo count($balances['user_owes']); ?> people</span>
                </div>
                <div class="card-body">
                    <?php if (empty($balances['user_owes'])): ?>
                        <div class="empty-state">
                            <i class="fas fa-smile"></i>
                            <p>You don't owe anyone</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($balances['user_owes'] as $balance): ?>
                        <div class="list-item">
                            <div class="user-info">
                                <div class="avatar owe">
                                    <?php echo strtoupper(substr($balance['username'], 0, 1)); ?>
                                </div>
                                <div class="user-details">
                                    <h6><?php echo htmlspecialchars($balance['display_name']); ?></h6>
                                    <small>@<?php echo htmlspecialchars($balance['username']); ?></small>
                                </div>
                            </div>
                            <div class="amount-badge negative">
                                €<?php echo number_format($balance['amount_owed'], 2); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Expenses -->
        <div class="card">
            <div class="card-header">
                Recent Expenses
                <span class="tag"><?php echo count($recentExpenses); ?> expenses</span>
            </div>
            <div class="card-body">
                <?php if (empty($recentExpenses)): ?>
                    <div class="empty-state">
                        <i class="fas fa-receipt"></i>
                        <p>No expenses recorded yet</p>
                        <a href="havetopay_add.php" class="btn btn-primary" style="margin-top: 16px;">
                            <i class="fas fa-plus"></i>
                            Add Your First Expense
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Expense</th>
                                    <th>Amount</th>
                                    <th>Paid By</th>
                                    <th>Date</th>
                                    <th>Participants</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentExpenses as $expense): ?>
                                <tr>
                                    <td>
                                        <div class="expense-title"><?php echo htmlspecialchars($expense['title']); ?></div>
                                        <?php if(!empty($expense['description'])): ?>
                                            <div class="expense-description"><?php echo htmlspecialchars(mb_strimwidth($expense['description'], 0, 50, "...")); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong>€<?php echo number_format($expense['amount'], 2); ?></strong>
                                    </td>
                                    <td>
                                        <div class="user-info">
                                            <div class="avatar" style="width: 24px; height: 24px; font-size: 12px;">
                                                <?php echo strtoupper(substr($expense['payer_name'], 0, 1)); ?>
                                            </div>
                                            <span><?php echo htmlspecialchars($expense['payer_display_name']); ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo date('M j', strtotime($expense['expense_date'])); ?></td>
                                    <td>
                                        <span class="tag participants">
                                            <?php echo $expense['participant_count']; ?> people
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 12px;">
                                            <a href="havetopay_detail.php?id=<?php echo $expense['id']; ?>" class="action-link">
                                                Details
                                            </a>
                                            <?php if ($expense['payer_id'] == $userId || ($_SESSION['is_admin'] ?? false)): ?>
                                                <button type="button" 
                                                        class="action-link danger"
                                                        style="background: none; border: none; cursor: pointer;"
                                                        onclick="confirmDelete(<?php echo $expense['id']; ?>, '<?php echo htmlspecialchars($expense['title'], ENT_QUOTES); ?>')">
                                                    Delete
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
    
    <!-- Delete Modal -->
    <div id="deleteModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 16px; padding: 24px; max-width: 400px; margin: 20px;">
            <h3 style="margin: 0 0 16px 0; font-size: 20px; font-weight: 600;">Delete Expense</h3>
            <p style="margin: 0 0 24px 0; color: var(--sf-text-secondary);">
                Are you sure you want to delete "<span id="expenseTitle"></span>"? This action cannot be undone.
            </p>
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="closeDeleteModal()" class="btn btn-secondary">Cancel</button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete_expense">
                    <input type="hidden" name="expense_id" id="deleteExpenseId" value="">
                    <button type="submit" style="background: var(--sf-red); color: white;" class="btn">Delete</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function confirmDelete(expenseId, expenseTitle) {
            document.getElementById('deleteExpenseId').value = expenseId;
            document.getElementById('expenseTitle').textContent = expenseTitle;
            const modal = document.getElementById('deleteModal');
            modal.style.display = 'flex';
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }
        
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });
    </script>
</body>
</html>
