<?php
// HaveToPay template - Modern redesign
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HaveToPay | PrivateVault</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4F46E5;
            --primary-light: #EEF2FF;
            --primary-dark: #3730A3;
            --success-color: #10B981;
            --success-light: #ECFDF5;
            --danger-color: #EF4444;
            --danger-light: #FEF2F2;
            --warning-color: #F59E0B;
            --text-dark: #1F2937;
            --text-muted: #6B7280;
            --surface-card: #FFFFFF;
            --surface-bg: #F9FAFB;
            --border-radius: 16px;
            --transition: all 0.3s ease;
        }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--surface-bg);
            color: var(--text-dark);
        }
        
        .container {
            max-width: 1280px;
            padding: 0 1rem;
        }
        
        /* Modern Cards */
        .modern-card {
            background-color: var(--surface-card);
            border-radius: var(--border-radius);
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.08);
            border: none;
            transition: var(--transition);
            height: 100%;
            overflow: hidden;
        }
        
        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            padding: 1.5rem;
            border: none;
            background: none;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Gradient Headers */
        .gradient-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }
        
        .gradient-success {
            background: linear-gradient(135deg, var(--success-color), #059669);
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }
        
        .gradient-danger {
            background: linear-gradient(135deg, var(--danger-color), #B91C1C);
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }
        
        /* Modern Buttons */
        .btn-modern-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            color: white;
            font-weight: 600;
            box-shadow: 0px 4px 12px rgba(79, 70, 229, 0.3);
            transition: var(--transition);
        }
        
        .btn-modern-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0px 6px 16px rgba(79, 70, 229, 0.4);
            color: white;
        }
        
        .btn-modern-outline {
            background: white;
            border: 2px solid #E5E7EB;
            border-radius: 12px;
            padding: 11px 24px;
            color: var(--text-dark);
            font-weight: 600;
            transition: var(--transition);
        }
        
        .btn-modern-outline:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        /* Modern Badges */
        .modern-badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .badge-success {
            background-color: var(--success-light);
            color: var(--success-color);
        }
        
        .badge-danger {
            background-color: var(--danger-light);
            color: var(--danger-color);
        }
        
        /* Modern List Items */
        .modern-list-item {
            border-radius: 12px;
            margin-bottom: 12px;
            transition: var(--transition);
            border: 1px solid #F3F4F6;
            background-color: white;
        }
        
        .modern-list-item:hover {
            transform: translateX(3px);
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.05);
            border-color: #E5E7EB;
        }
        
        /* Page Header */
        .page-header {
            margin-bottom: 32px;
        }
        
        .page-header h1 {
            font-weight: 700;
            font-size: 2rem;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }
        
        /* Table Styling */
        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .modern-table th {
            font-weight: 600;
            padding: 16px 20px;
            text-align: left;
            color: var(--text-muted);
            border-bottom: 1px solid #F3F4F6;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .modern-table td {
            padding: 16px 20px;
            border-bottom: 1px solid #F3F4F6;
            vertical-align: middle;
        }
        
        .modern-table tr:last-child td {
            border-bottom: none;
        }
        
        .modern-table tbody tr {
            transition: var(--transition);
        }
        
        .modern-table tbody tr:hover {
            background-color: var(--primary-light);
        }
        
        /* Balance Cards */
        .balance-value {
            font-size: 1.75rem;
            font-weight: 700;
            margin-top: 0.5rem;
            margin-bottom: 0;
        }
        
        .balance-label {
            color: var(--text-muted);
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .amount-owed {
            color: var(--success-color);
        }
        
        .amount-owing {
            color: var(--danger-color);
        }
        
        .amount-neutral {
            color: var(--text-dark);
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .main-content {
            animation: fadeIn 0.6s ease-out;
        }
        
        .balance-card {
            animation: fadeIn 0.6s ease-out;
            animation-delay: 0.2s;
            animation-fill-mode: both;
        }
        
        .people-card {
            animation: fadeIn 0.6s ease-out;
            animation-delay: 0.4s;
            animation-fill-mode: both;
        }
        
        .expenses-card {
            animation: fadeIn 0.6s ease-out;
            animation-delay: 0.6s;
            animation-fill-mode: both;
        }
        
        /* Alerts */
        .modern-alert {
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            border: none;
            display: flex;
            align-items: center;
        }
        
        .modern-alert.success {
            background-color: var(--success-light);
            color: var(--success-color);
        }
        
        .modern-alert.warning {
            background-color: var(--danger-light);
            color: var(--danger-color);
        }
        
        .modern-alert i {
            margin-right: 0.75rem;
            font-size: 1.25rem;
        }
        
        /* Content area spacing - Updated to fix overlap issues */
        .content-container {
            padding-top: 2rem;
            padding-bottom: 3rem;
        }
        
        /* Media queries */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 1rem;
            }
            
            .action-buttons {
                width: 100%;
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .btn-modern-primary, .btn-modern-outline {
                width: 100%;
            }
            
            /* Mobile padding for content with navbar */
            .content-container {
                padding-top: 4.5rem !important;
            }
        }

        /* Adjustment for navbar integration */
        @media (min-width: 769px) {
            body {
                margin-left: 0;
            }
            
            .content-container {
                margin-left: 16rem;
                width: calc(100% - 16rem);
                padding-bottom: 3rem;
            }
        }
    </style>
</head>
<body class="haveToPay-layout">
    <?php require_once __DIR__.'/navbar.php'; ?>
    
    <div class="content-container">
        <div class="container main-content">
            <!-- Success/Error Messages -->
            <?php if (!empty($successMessage)): ?>
            <div class="modern-alert success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($successMessage); ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($errorMessage)): ?>
            <div class="modern-alert warning">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
            <?php endif; ?>

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center page-header">
                <h1><i class="fas fa-wallet me-3 text-primary"></i>HaveToPay</h1>
                <div class="action-buttons">
                    <a href="havetopay_add.php" class="btn-modern-primary me-2">
                        <i class="fas fa-plus me-2"></i>Add Expense
                    </a>
                    <a href="index.php" class="btn-modern-outline">
                        <i class="fas fa-home me-2"></i>Dashboard
                    </a>
                </div>
            </div>

            <!-- Balance Summary Card -->
            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="modern-card balance-card">
                        <div class="gradient-primary card-header">
                            <h5 class="m-0 fw-bold">Your Balance Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4 align-items-center">
                                <div class="col-md-4 text-center">
                                    <div class="balance-label">You are owed</div>
                                    <h3 class="balance-value amount-owed"><?php echo number_format($totalOwed, 2); ?> €</h3>
                                </div>
                                
                                <div class="col-md-4 text-center">
                                    <div class="balance-label">Your net balance</div>
                                    <h3 class="balance-value <?php echo $netBalance >= 0 ? 'amount-owed' : 'amount-owing'; ?>">
                                        <?php echo number_format($netBalance, 2); ?> €
                                    </h3>
                                    <?php if($netBalance >= 0): ?>
                                        <div class="mt-2 badge-success modern-badge">
                                            <i class="fas fa-arrow-up me-1"></i> Positive Balance
                                        </div>
                                    <?php else: ?>
                                        <div class="mt-2 badge-danger modern-badge">
                                            <i class="fas fa-arrow-down me-1"></i> Negative Balance
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-4 text-center">
                                    <div class="balance-label">You owe</div>
                                    <h3 class="balance-value amount-owing"><?php echo number_format($totalOwing, 2); ?> €</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Cards -->
            <div class="row mb-4">
                <!-- People who owe me -->
                <div class="col-lg-6 mb-4">
                    <div class="modern-card people-card h-100">
                        <div class="gradient-success card-header d-flex justify-content-between align-items-center">
                            <h5 class="m-0 fw-bold">People Who Owe You</h5>
                            <span class="modern-badge bg-white text-success">
                                <?php echo count($balances['others_owe']); ?> people
                            </span>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($balances['others_owe'])): ?>
                                <div class="text-center p-5">
                                    <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">No one owes you money at the moment.</p>
                                </div>
                            <?php else: ?>
                                <ul class="list-unstyled mb-0">
                                    <?php foreach ($balances['others_owe'] as $balance): ?>
                                    <li class="modern-list-item p-3 d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px;">
                                                <?php echo strtoupper(substr($balance['username'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?php echo htmlspecialchars($balance['display_name']); ?></h6>
                                                <small class="text-muted">@<?php echo htmlspecialchars($balance['username']); ?></small>
                                            </div>
                                        </div>
                                        <span class="modern-badge badge-success">
                                            <?php echo number_format($balance['amount_owed'], 2); ?> €
                                        </span>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- People I owe -->
                <div class="col-lg-6 mb-4">
                    <div class="modern-card people-card h-100">
                        <div class="gradient-danger card-header d-flex justify-content-between align-items-center">
                            <h5 class="m-0 fw-bold">People You Owe</h5>
                            <span class="modern-badge bg-white text-danger">
                                <?php echo count($balances['user_owes']); ?> people
                            </span>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($balances['user_owes'])): ?>
                                <div class="text-center p-5">
                                    <i class="fas fa-smile fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">You don't owe anyone money at the moment.</p>
                                </div>
                            <?php else: ?>
                                <ul class="list-unstyled mb-0">
                                    <?php foreach ($balances['user_owes'] as $balance): ?>
                                    <li class="modern-list-item p-3 d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3 bg-danger text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px;">
                                                <?php echo strtoupper(substr($balance['username'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?php echo htmlspecialchars($balance['display_name']); ?></h6>
                                                <small class="text-muted">@<?php echo htmlspecialchars($balance['username']); ?></small>
                                            </div>
                                        </div>
                                        <span class="modern-badge badge-danger">
                                            <?php echo number_format($balance['amount_owed'], 2); ?> €
                                        </span>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Expenses -->
            <div class="row">
                <div class="col-12">
                    <div class="modern-card expenses-card">
                        <div class="gradient-primary card-header d-flex justify-content-between align-items-center">
                            <h5 class="m-0 fw-bold">Recent Expenses</h5>
                            <span class="modern-badge bg-white text-primary">
                                <?php echo count($recentExpenses); ?> expenses
                            </span>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($recentExpenses)): ?>
                                <div class="text-center p-5">
                                    <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No expenses recorded yet.</p>
                                    <a href="havetopay_add.php" class="btn btn-sm btn-modern-primary mt-2">
                                        <i class="fas fa-plus me-2"></i>Add Your First Expense
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="modern-table">
                                        <thead>
                                            <tr>
                                                <th>Expense</th>
                                                <th>Amount</th>
                                                <th>Paid By</th>
                                                <th>Date</th>
                                                <th>Participants</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentExpenses as $expense): ?>
                                            <tr>
                                                <td>
                                                    <a href="havetopay_detail.php?id=<?php echo $expense['id']; ?>" class="text-decoration-none text-primary fw-medium">
                                                        <?php echo htmlspecialchars($expense['title']); ?>
                                                    </a>
                                                    <?php if(!empty($expense['description'])): ?>
                                                        <div class="text-muted small"><?php echo htmlspecialchars(mb_strimwidth($expense['description'], 0, 50, "...")); ?></div>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="fw-semibold"><?php echo number_format($expense['amount'], 2); ?> €</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-primary text-white d-flex align-items-center justify-content-center rounded-circle me-2" style="width: 28px; height: 28px; font-size: 0.8rem;">
                                                            <?php echo strtoupper(substr($expense['payer_name'], 0, 1)); ?>
                                                        </div>
                                                        <span><?php echo htmlspecialchars($expense['payer_display_name']); ?></span>
                                                    </div>
                                                </td>
                                                <td><?php echo date('d M Y', strtotime($expense['expense_date'])); ?></td>
                                                <td>
                                                    <span class="modern-badge" style="background-color: #EFF6FF; color: #3B82F6;">
                                                        <i class="fas fa-users me-1"></i> 
                                                        <?php echo $expense['participant_count']; ?> people
                                                    </span>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
