<?php
// HaveToPay template
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HaveToPay | PrivateVault</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border: none;
        }
        .card-header {
            border-radius: 15px 15px 0 0 !important;
            border: none;
        }
        .btn {
            border-radius: 50px;
            padding: 8px 20px;
        }
        .badge {
            padding: 6px 12px;
            border-radius: 50px;
        }
        .navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
    <?php if (file_exists(__DIR__.'/navbar.php')) require_once __DIR__.'/navbar.php'; ?>
    
    <div class="container mt-4 mb-5">
        <!-- Success/Error Messages -->
        <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($successMessage); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($errorMessage); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold">HaveToPay</h1>
            <div>
                <a href="havetopay_add.php" class="btn btn-primary me-2">
                    <i class="fas fa-plus me-2"></i>Add Expense
                </a>
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Add Expense Button -->
        <div class="text-end mb-4">
            <a href="havetopay_add.php" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Add New Expense
            </a>
        </div>

        <!-- Main Dashboard -->
        <div class="row">
            <!-- Balance Summary -->
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Balance Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>You are owed:</span>
                            <span class="text-success fw-bold"><?php echo number_format($totalOwed, 2); ?> €</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>You owe:</span>
                            <span class="text-danger fw-bold"><?php echo number_format($totalOwing, 2); ?> €</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Net Balance:</span>
                            <span class="fw-bold <?php echo $netBalance >= 0 ? 'text-success' : 'text-danger'; ?>">
                                <?php echo number_format($netBalance, 2); ?> €
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Add Expense -->
            <div class="col-md-8 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Add New Expense</h5>
                    </div>
                    <div class="card-body">
                        <form action="havetopay_add.php" method="post">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <input type="text" class="form-control" id="description" name="description" placeholder="What was it for?" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="amount" class="form-label">Total Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">€</span>
                                        <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0.01" placeholder="0.00" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="participants" class="form-label">Split With</label>
                                    <select class="form-select" id="participants" name="participants[]" multiple required>
                                        <?php foreach($allUsers as $user): ?>
                                        <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['display_name'] ?? $user['username']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="expenseDate" class="form-label">Date</label>
                                    <input type="date" class="form-control" id="expenseDate" name="expense_date" value="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-2"></i>Add Expense
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- People who owe me -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">People Who Owe Me</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($balances['others_owe'])): ?>
                            <p class="text-muted">No one owes you money at the moment.</p>
                        <?php else: ?>
                            <ul class="list-group">
                                <?php foreach ($balances['others_owe'] as $balance): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?php echo htmlspecialchars($balance['display_name'] ?? $balance['username']); ?></strong>
                                        <small class="d-block text-muted">@<?php echo htmlspecialchars($balance['username']); ?></small>
                                    </div>
                                    <span class="badge bg-success rounded-pill">€<?php echo number_format($balance['amount_owed'], 2); ?></span>
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
                    <div class="card-header bg-danger text-white">
                        <h5 class="card-title mb-0">People I Owe</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($balances['user_owes'])): ?>
                            <p class="text-muted">You don't owe anyone money at the moment.</p>
                        <?php else: ?>
                            <ul class="list-group">
                                <?php foreach ($balances['user_owes'] as $balance): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?php echo htmlspecialchars($balance['display_name'] ?? $balance['username']); ?></strong>
                                        <small class="d-block text-muted">@<?php echo htmlspecialchars($balance['username']); ?></small>
                                    </div>
                                    <span class="badge bg-danger rounded-pill">€<?php echo number_format($balance['amount_owed'], 2); ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Expenses -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Recent Expenses</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recentExpenses)): ?>
                    <p class="text-muted">No expenses recorded yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th> <!-- Changed from Description -->
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
                                        <a href="havetopay_detail.php?id=<?php echo $expense['id']; ?>">
                                            <?php echo htmlspecialchars($expense['title']); ?>
                                        </a>
                                        <?php if(!empty($expense['description'])): ?>
                                            <small class="d-block text-muted"><?php echo htmlspecialchars(mb_strimwidth($expense['description'], 0, 50, "...")); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>€<?php echo number_format($expense['amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($expense['payer_display_name'] ?? $expense['payer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($expense['expense_date']); ?></td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo $expense['participant_count']; ?> people</span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Status and Tools -->
        <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top">
            <div>
                <p class="text-muted mb-0"><small>Connected as: <?php echo htmlspecialchars($currentUser['display_name'] ?? $currentUser['username']); ?></small></p>
            </div>
            <div>
                <a href="havetopay_setup.php" class="btn btn-sm btn-outline-secondary">Setup Tool</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
