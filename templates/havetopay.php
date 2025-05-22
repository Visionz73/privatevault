<?php
// HaveToPay template - Modern UI
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HaveToPay | PrivateVault</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4A90E2;
            --success: #2ecc71;
            --danger: #e74c3c;
            --warning: #f39c12;
            --light: #f5f7fa;
            --dark: #2c3e50;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
        }
        
        .wrapper {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            border-bottom: none;
            padding: 1.5rem 1.5rem 0;
            background-color: transparent;
            font-weight: 600;
        }
        
        .balance-card {
            background: linear-gradient(120deg, var(--primary), #6EC5FF);
            color: white;
            overflow: hidden;
            position: relative;
        }
        
        .balance-card::after {
            content: "";
            position: absolute;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -100px;
            right: -50px;
        }
        
        .balance-card::before {
            content: "";
            position: absolute;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            bottom: -40px;
            left: -60px;
        }
        
        .balance-value {
            font-size: 2.5rem;
            font-weight: 700;
        }
        
        .balance-label {
            opacity: 0.8;
            font-size: 0.9rem;
        }
        
        .btn-floating {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 100;
        }
        
        .user-badge {
            display: flex;
            align-items: center;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50px;
            padding: 0.5rem 1rem;
            margin-bottom: 1rem;
        }
        
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: 600;
        }
        
        .expense-card {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .expense-amount {
            font-weight: 600;
            font-size: 1.2rem;
        }
        
        .expense-date {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--dark);
            position: relative;
            display: inline-block;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 40px;
            height: 3px;
            background-color: var(--primary);
        }

        .owe-me-list .list-group-item,
        .i-owe-list .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
        }

        .table-expenses thead th {
            font-weight: 500;
            border-top: none;
            border-bottom: 2px solid rgba(0,0,0,0.05);
        }
        
        .error-container {
            background-color: white;
            max-width: 600px;
            margin: 100px auto;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <?php if (!$tablesExist): ?>
    <div class="error-container p-5 text-center">
        <div class="mb-4 text-danger">
            <i class="fas fa-exclamation-triangle fa-3x"></i>
        </div>
        <h2>HaveToPay Setup Required</h2>
        <p><?php echo $errorMessage ?? 'Database tables need to be created'; ?></p>
        <div class="mt-4">
            <a href="havetopay_setup.php" class="btn btn-primary">Run Setup</a>
            <a href="index.php" class="btn btn-outline-secondary ms-2">Return to Dashboard</a>
        </div>
    </div>
    <?php else: ?>
    
    <div class="wrapper py-5">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="fs-2 fw-bold mb-0">HaveToPay</h1>
                <p class="text-muted mb-0">Track shared expenses between friends and groups</p>
            </div>
            <div>
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Dashboard
                </a>
            </div>
        </div>
        
        <!-- Alerts -->
        <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($successMessage); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($errorMessage); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <!-- Balance Summary -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card balance-card h-100">
                    <div class="card-body p-4">
                        <div class="user-badge">
                            <div class="avatar">
                                <?php 
                                    $initials = substr($currentUser['first_name'] ?? $currentUser['username'], 0, 1) . 
                                               substr($currentUser['last_name'] ?? '', 0, 1);
                                    echo strtoupper($initials);
                                ?>
                            </div>
                            <span><?php echo htmlspecialchars($currentUser['first_name'] ?? $currentUser['username']); ?></span>
                        </div>
                        
                        <h4 class="mb-4">Your Balance</h4>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="balance-label">You are owed</div>
                                <div class="balance-value"><?php echo number_format($totalOwed, 2); ?>€</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="balance-label">You owe</div>
                                <div class="balance-value"><?php echo number_format($totalOwing, 2); ?>€</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="balance-label">Net Balance</div>
                                <div class="balance-value <?php echo $netBalance >= 0 ? 'text-white' : 'text-warning'; ?>">
                                    <?php echo number_format($netBalance, 2); ?>€
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <h3 class="section-title">Quick Add Expense</h3>
                        <form action="havetopay_add.php" method="post">
                            <div class="mb-3">
                                <label for="title" class="form-label">Description</label>
                                <input type="text" class="form-control" id="title" name="title" placeholder="What was it for?" required>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="amount" class="form-label">Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">€</span>
                                        <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0.01" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="expense_date" class="form-label">Date</label>
                                    <input type="date" class="form-control" id="expense_date" name="expense_date" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Add Expense</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Settlement Section -->
        <div class="row mb-4">
            <!-- People who owe me -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <h3 class="section-title">People who owe you</h3>
                    </div>
                    <div class="card-body">
                        <?php if (empty($balances['others_owe'])): ?>
                            <div class="text-center p-4 text-muted">
                                <i class="fas fa-check-circle fa-2x mb-3"></i>
                                <p>No one owes you money at the moment!</p>
                            </div>
                        <?php else: ?>
                            <ul class="list-group owe-me-list">
                                <?php foreach ($balances['others_owe'] as $balance): ?>
                                <li class="list-group-item">
                                    <div>
                                        <strong>
                                            <?php echo htmlspecialchars($balance['first_name'] . ' ' . $balance['last_name'] ?: $balance['username']); ?>
                                        </strong>
                                        <div class="small text-muted">@<?php echo htmlspecialchars($balance['username']); ?></div>
                                    </div>
                                    <div class="text-success fw-bold">
                                        <?php echo number_format($balance['amount_owed'], 2); ?>€
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- People I owe -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <h3 class="section-title">People you owe</h3>
                    </div>
                    <div class="card-body">
                        <?php if (empty($balances['user_owes'])): ?>
                            <div class="text-center p-4 text-muted">
                                <i class="fas fa-check-circle fa-2x mb-3"></i>
                                <p>You don't owe anyone money at the moment!</p>
                            </div>
                        <?php else: ?>
                            <ul class="list-group i-owe-list">
                                <?php foreach ($balances['user_owes'] as $balance): ?>
                                <li class="list-group-item">
                                    <div>
                                        <strong>
                                            <?php echo htmlspecialchars($balance['first_name'] . ' ' . $balance['last_name'] ?: $balance['username']); ?>
                                        </strong>
                                        <div class="small text-muted">@<?php echo htmlspecialchars($balance['username']); ?></div>
                                    </div>
                                    <div class="text-danger fw-bold">
                                        <?php echo number_format($balance['amount_owed'], 2); ?>€
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h3 class="section-title">Recent Expenses</h3>
            </div>
            <div class="card-body">
                <?php if (empty($recentExpenses)): ?>
                    <div class="text-center p-5 text-muted">
                        <i class="fas fa-receipt fa-3x mb-3"></i>
                        <p>No expenses recorded yet. Add one to get started!</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-expenses">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Paid By</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Participants</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentExpenses as $expense): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($expense['description']); ?></td>
                                    <td><?php echo htmlspecialchars($expense['payer_name']); ?></td>
                                    <td><?php echo date('d.m.Y', strtotime($expense['expense_date'])); ?></td>
                                    <td class="fw-bold"><?php echo number_format($expense['amount'], 2); ?>€</td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo $expense['participant_count']; ?></span>
                                    </td>
                                    <td>
                                        <a href="havetopay_detail.php?id=<?php echo $expense['id']; ?>" class="btn btn-sm btn-outline-primary">
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

    <!-- Floating Action Button -->
    <a href="havetopay_add.php" class="btn btn-primary btn-floating">
        <i class="fas fa-plus"></i>
    </a>
    <?php endif; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
