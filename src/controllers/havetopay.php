<?php
// Controller for the main HaveToPay dashboard view
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/db.php';

// Ensure user is logged in
requireLogin();
$userId = $_SESSION['user_id'];

// Initialize variables
$errorMessage = '';
$successMessage = '';
$pageTitle = 'HaveToPay Dashboard';
$balances = ['others_owe' => [], 'user_owes' => []];
$totalOwed = 0;
$totalOwing = 0;
$netBalance = 0;
$recentExpenses = [];
$currentUser = [];
$allUsers = [];

try {
    // First, check users table structure
    $columnsResult = $pdo->query("DESCRIBE users");
    $userColumns = [];
    while ($column = $columnsResult->fetch(PDO::FETCH_ASSOC)) {
        $userColumns[] = $column['Field'];
    }
    
    // Determine if name fields exist
    $hasFirstName = in_array('first_name', $userColumns);
    $hasLastName = in_array('last_name', $userColumns);
    
    // Make sure tables exist
    $tableCreationOutput = '';
    ob_start();
    require_once __DIR__ . '/../../database/havetopay_tables.php';
    $tableCreationOutput = ob_get_clean();

    // Process any success or error messages from table creation
    if (!empty($tableCreationOutput)) {
        if (strpos(strtolower($tableCreationOutput), 'error') !== false) {
            $errorMessage = "Error during table setup: " . htmlspecialchars($tableCreationOutput);
        } elseif (strpos($tableCreationOutput, 'successfully') !== false) {
            $successMessage = htmlspecialchars($tableCreationOutput);
        }
    }
    
    // Get current user data
    if ($hasFirstName && $hasLastName) {
        $stmt = $pdo->prepare("SELECT id, username, first_name, last_name FROM users WHERE id = ?");
    } else {
        $stmt = $pdo->prepare("SELECT id, username FROM users WHERE id = ?");
    }
    $stmt->execute([$userId]);
    $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$currentUser) {
        throw new Exception("User data could not be retrieved.");
    }
    
    // Prepare user display name
    $currentUser['display_name'] = $hasFirstName && $hasLastName && 
        !empty($currentUser['first_name']) && !empty($currentUser['last_name']) ? 
        $currentUser['first_name'] . ' ' . $currentUser['last_name'] : 
        $currentUser['username'];
    
    // Get all users for expense participant selection
    if ($hasFirstName && $hasLastName) {
        $stmt = $pdo->prepare("SELECT id, username, first_name, last_name FROM users WHERE id != ? ORDER BY username");
    } else {
        $stmt = $pdo->prepare("SELECT id, username FROM users WHERE id != ? ORDER BY username");
    }
    $stmt->execute([$userId]);
    $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add display names to all users
    foreach ($allUsers as &$user) {
        $user['display_name'] = $hasFirstName && $hasLastName && 
            !empty($user['first_name']) && !empty($user['last_name']) ? 
            $user['first_name'] . ' ' . $user['last_name'] : 
            $user['username'];
    }
    
    // Get balances: what others owe current user
    $nameFields = $hasFirstName && $hasLastName ? 
        "u.first_name, u.last_name" : 
        "u.username as name";
        
    $stmt = $pdo->prepare("
        SELECT u.id, u.username, $nameFields,
               SUM(ep.share_amount) as amount_owed
        FROM expenses e
        JOIN expense_participants ep ON e.id = ep.expense_id
        JOIN users u ON ep.user_id = u.id
        WHERE e.payer_id = ? AND ep.user_id != ? AND ep.is_settled = 0
        GROUP BY u.id
    ");
    $stmt->execute([$userId, $userId]);
    $othersOwe = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add display names to balances
    foreach ($othersOwe as $balance) {
        $balance['display_name'] = $hasFirstName && $hasLastName && 
            !empty($balance['first_name']) && !empty($balance['last_name']) ? 
            $balance['first_name'] . ' ' . $balance['last_name'] : 
            $balance['username'];
        $balances['others_owe'][] = $balance;
    }
    
    // Get balances: what current user owes others
    $stmt = $pdo->prepare("
        SELECT u.id, u.username, $nameFields,
               SUM(ep.share_amount) as amount_owed
        FROM expenses e
        JOIN expense_participants ep ON e.id = ep.expense_id
        JOIN users u ON e.payer_id = u.id
        WHERE ep.user_id = ? AND e.payer_id != ? AND ep.is_settled = 0
        GROUP BY u.id
    ");
    $stmt->execute([$userId, $userId]);
    $userOwes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add display names to balances
    foreach ($userOwes as $balance) {
        $balance['display_name'] = $hasFirstName && $hasLastName && 
            !empty($balance['first_name']) && !empty($balance['last_name']) ? 
            $balance['first_name'] . ' ' . $balance['last_name'] : 
            $balance['username'];
        $balances['user_owes'][] = $balance;
    }
    
    // Get recent expenses involving the current user
    $userNameFields = $hasFirstName && $hasLastName ? 
        "u.first_name as payer_first_name, u.last_name as payer_last_name," : 
        "";
        
    $stmt = $pdo->prepare("
        SELECT e.*,
               u.username as payer_name,
               $userNameFields
               (SELECT COUNT(*) FROM expense_participants WHERE expense_id = e.id) as participant_count
        FROM expenses e
        JOIN users u ON e.payer_id = u.id
        WHERE e.payer_id = ? 
        OR e.id IN (SELECT expense_id FROM expense_participants WHERE user_id = ?)
        ORDER BY e.created_at DESC
        LIMIT 10
    ");
    $stmt->execute([$userId, $userId]);
    $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add display names to expenses
    foreach ($expenses as $expense) {
        $expense['payer_display_name'] = $hasFirstName && $hasLastName && 
            !empty($expense['payer_first_name']) && !empty($expense['payer_last_name']) ? 
            $expense['payer_first_name'] . ' ' . $expense['payer_last_name'] : 
            $expense['payer_name'];
        $recentExpenses[] = $expense;
    }
    
    // Calculate total balances
    foreach ($balances['others_owe'] as $balance) {
        $totalOwed += $balance['amount_owed'];
    }
    
    foreach ($balances['user_owes'] as $balance) {
        $totalOwing += $balance['amount_owed'];
    }
    
    $netBalance = $totalOwed - $totalOwing;
    
} catch (Exception $e) {
    $errorMessage = "An error occurred: " . $e->getMessage();
    error_log("HaveToPay error: " . $e->getMessage());
}

// Check for success message in query string (from add/edit operations)
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'added':
            $successMessage = 'Expense added successfully';
            break;
        case 'settled':
            $successMessage = 'Payment marked as settled';
            break;
    }
}

// Template rendering with unified header/footer
require_once __DIR__ . '/../../templates/header.php';
?>

<!-- HaveToPay Content (inline to fix accessibility) -->
<div class="container-fluid mt-4">
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

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
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
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Your Balance Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="text-muted">You are owed</div>
                            <h3 class="text-success mb-0"><?php echo number_format($totalOwed, 2); ?> €</h3>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted">Your net balance</div>
                            <h3 class="<?php echo $netBalance >= 0 ? 'text-success' : 'text-danger'; ?> mb-0">
                                <?php echo number_format($netBalance, 2); ?> €
                            </h3>
                            <small class="badge <?php echo $netBalance >= 0 ? 'bg-success' : 'bg-danger'; ?>">
                                <?php echo $netBalance >= 0 ? 'Positive' : 'Negative'; ?> Balance
                            </small>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted">You owe</div>
                            <h3 class="text-danger mb-0"><?php echo number_format($totalOwing, 2); ?> €</h3>
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
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-arrow-down me-2"></i>People Who Owe You</h5>
                    <span class="badge bg-light text-success"><?php echo count($balances['others_owe']); ?> people</span>
                </div>
                <div class="card-body">
                    <?php if (empty($balances['others_owe'])): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No one owes you money at the moment.</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($balances['others_owe'] as $balance): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <?php echo strtoupper(substr($balance['username'], 0, 1)); ?>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($balance['display_name']); ?></h6>
                                        <small class="text-muted">@<?php echo htmlspecialchars($balance['username']); ?></small>
                                    </div>
                                </div>
                                <span class="badge bg-success-subtle text-success fs-6">
                                    <?php echo number_format($balance['amount_owed'], 2); ?> €
                                </span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- People I owe -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-arrow-up me-2"></i>People You Owe</h5>
                    <span class="badge bg-light text-danger"><?php echo count($balances['user_owes']); ?> people</span>
                </div>
                <div class="card-body">
                    <?php if (empty($balances['user_owes'])): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-smile fa-3x text-muted mb-3"></i>
                            <p class="text-muted">You don't owe anyone money at the moment.</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($balances['user_owes'] as $balance): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <?php echo strtoupper(substr($balance['username'], 0, 1)); ?>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($balance['display_name']); ?></h6>
                                        <small class="text-muted">@<?php echo htmlspecialchars($balance['username']); ?></small>
                                    </div>
                                </div>
                                <span class="badge bg-danger-subtle text-danger fs-6">
                                    <?php echo number_format($balance['amount_owed'], 2); ?> €
                                </span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Expenses -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Recent Expenses</h5>
                    <span class="badge bg-light text-primary"><?php echo count($recentExpenses); ?> expenses</span>
                </div>
                <div class="card-body">
                    <?php if (empty($recentExpenses)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No expenses recorded yet.</p>
                            <a href="havetopay_add.php" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add Your First Expense
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
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
                                            <strong><?php echo htmlspecialchars($expense['title']); ?></strong>
                                            <?php if(!empty($expense['description'])): ?>
                                                <br><small class="text-muted"><?php echo htmlspecialchars(mb_strimwidth($expense['description'], 0, 50, "...")); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="fw-bold"><?php echo number_format($expense['amount'], 2); ?> €</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 28px; height: 28px; font-size: 0.8rem;">
                                                    <?php echo strtoupper(substr($expense['payer_name'], 0, 1)); ?>
                                                </div>
                                                <span><?php echo htmlspecialchars($expense['payer_display_name']); ?></span>
                                            </div>
                                        </td>
                                        <td><?php echo date('d M Y', strtotime($expense['expense_date'])); ?></td>
                                        <td>
                                            <span class="badge bg-info text-dark">
                                                <i class="fas fa-users me-1"></i> 
                                                <?php echo $expense['participant_count']; ?> people
                                            </span>
                                        </td>
                                        <td>
                                            <a href="havetopay_detail.php?id=<?php echo $expense['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary">
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

<?php
require_once __DIR__ . '/../../templates/footer.php';
?>
