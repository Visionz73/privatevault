<?php
// Simple tool to view HaveToPay tables
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/lib/auth.php';

// Ensure only admins can access this page
requireLogin();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: index.php');
    exit;
}

// Get table data
$tables = ['expenses', 'expense_participants'];
$tableData = [];
$tableColumns = [];

foreach ($tables as $table) {
    try {
        // Get column names
        $columnQuery = $pdo->query("SHOW COLUMNS FROM $table");
        $tableColumns[$table] = $columnQuery->fetchAll(PDO::FETCH_COLUMN);
        
        // Get data
        $dataQuery = $pdo->query("SELECT * FROM $table LIMIT 100");
        $tableData[$table] = $dataQuery->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $tableData[$table] = null;
        $tableColumns[$table] = null;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HaveToPay Tables Viewer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>HaveToPay Tables Viewer</h1>
        <p><a href="index.php" class="btn btn-primary btn-sm">Back to Dashboard</a></p>
        
        <?php foreach ($tables as $table): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h3><?php echo htmlspecialchars($table); ?> Table</h3>
                </div>
                <div class="card-body">
                    <?php if ($tableData[$table] === null): ?>
                        <div class="alert alert-warning">Table does not exist or cannot be accessed.</div>
                    <?php elseif (empty($tableData[$table])): ?>
                        <div class="alert alert-info">No data in this table yet.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <?php foreach ($tableColumns[$table] as $column): ?>
                                            <th><?php echo htmlspecialchars($column); ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tableData[$table] as $row): ?>
                                        <tr>
                                            <?php foreach ($row as $value): ?>
                                                <td><?php echo htmlspecialchars($value); ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
