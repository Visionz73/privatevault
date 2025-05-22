<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($expense['title']) ?> | HaveToPay</title>
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
            min-height: 100vh;
        }
        
        .content-container {
            padding: 2rem 1rem 3rem;
        }
        
        /* Modern Cards */
        .modern-card {
            background-color: var(--surface-card);
            border-radius: var(--border-radius);
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.08);
            border: none;
            overflow: hidden;
            transition: var(--transition);
        }
        
        /* Buttons */
        .btn-modern-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border: none;
            border-radius: 12px;
            padding: 10px 20px;
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
        
        .btn-link-modern {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            transition: var(--transition);
        }
        
        .btn-link-modern:hover {
            color: var(--primary-dark);
            transform: translateX(-3px);
        }
        
        .btn-link-modern i {
            margin-right: 8px;
            transition: var(--transition);
        }
        
        .btn-link-modern:hover i {
            transform: translateX(-3px);
        }
        
        /* Status Badges */
        .status-badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
        }
        
        .status-badge i {
            margin-right: 6px;
        }
        
        .status-badge.settled {
            background-color: var(--success-light);
            color: var(--success-color);
        }
        
        .status-badge.pending {
            background-color: var(--warning-color);
            color: white;
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        
        .fade-in-delay-1 {
            animation: fadeIn 0.6s ease-out;
            animation-delay: 0.2s;
            animation-fill-mode: both;
        }
        
        .fade-in-delay-2 {
            animation: fadeIn 0.6s ease-out;
            animation-delay: 0.4s;
            animation-fill-mode: both;
        }
        
        /* Alert Messages */
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
        
        /* Detail section styling */
        .expense-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }
        
        .expense-amount {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-color);
        }
        
        .expense-meta {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .expense-meta i {
            width: 20px;
            text-align: center;
            margin-right: 8px;
        }
        
        .expense-description {
            background-color: var(--primary-light);
            border-radius: 12px;
            padding: 1rem;
            margin-top: 1.5rem;
        }
        
        .expense-user {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 12px;
            font-size: 1rem;
        }
        
        .user-details {
            display: flex;
            flex-direction: column;
        }
        
        .user-name {
            font-weight: 600;
            color: var(--text-dark);
        }
        
        .user-username {
            font-size: 0.8rem;
            color: var(--text-muted);
        }
        
        /* Table styling */
        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .modern-table th {
            text-align: left;
            padding: 1rem;
            color: var(--text-muted);
            font-weight: 600;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            font-size: 0.85rem;
        }
        
        .modern-table td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .modern-table tr:last-child td {
            border-bottom: none;
        }
        
        .modern-table tbody tr:hover {
            background-color: rgba(0,0,0,0.01);
        }
        
        /* Settled row styling */
        .row-settled {
            background-color: var(--success-light) !important;
        }
        
        /* Settle button */
        .btn-settle {
            color: var(--primary-color);
            background-color: transparent;
            border: none;
            font-weight: 600;
            padding: 0;
            transition: var(--transition);
            cursor: pointer;
        }
        
        .btn-settle:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        /* Adjustments for navbar integration */
        @media (min-width: 769px) {
            .content-container {
                margin-left: 16rem;
                width: calc(100% - 16rem);
                padding: 2rem 1.5rem 3rem;
            }
        }
        
        /* Mobile specific fix */
        @media (max-width: 768px) {
            .content-container {
                padding-top: 4.5rem !important;
            }
        }
    </style>
</head>
<body class="haveToPay-layout">
    <?php require_once __DIR__.'/navbar.php'; ?>
    
    <div class="content-container">
        <div class="container">
            <!-- Back Link -->
            <div class="mb-4 fade-in">
                <a href="havetopay.php" class="btn-link-modern">
                    <i class="fas fa-arrow-left"></i>
                    Back to HaveToPay
                </a>
            </div>
            
            <!-- Success/Error Messages -->
            <?php if (!empty($success)): ?>
            <div class="modern-alert success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
            <div class="modern-alert warning">
                <i class="fas fa-exclamation-circle"></i>
                <?php foreach ($errors as $error): ?>
                    <div><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!-- Expense Details -->
            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="modern-card fade-in">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-8">
                                    <h1 class="expense-title"><?php echo htmlspecialchars($expense['title']); ?></h1>
                                    
                                    <div class="expense-user">
                                        <div class="user-avatar">
                                            <?php echo strtoupper(substr($expense['payer_name'], 0, 1)); ?>
                                        </div>
                                        <div class="user-details">
                                            <span class="user-name">
                                                <?php echo htmlspecialchars($expense['payer_full_name'] ?: $expense['payer_name']); ?>
                                                <?php if ($expense['payer_id'] == $userId): ?> (You)<?php endif; ?>
                                            </span>
                                            <span class="user-username">@<?php echo htmlspecialchars($expense['payer_name']); ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex flex-wrap gap-4 mt-3">
                                        <div class="expense-meta">
                                            <i class="fas fa-calendar-alt"></i>
                                            <?php echo date('d M Y', strtotime($expense['expense_date'])); ?>
                                        </div>
                                        
                                        <?php if(!empty($expense['expense_category'])): ?>
                                        <div class="expense-meta">
                                            <i class="fas fa-tag"></i>
                                            <?php echo htmlspecialchars($expense['expense_category']); ?>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="expense-meta">
                                            <i class="fas fa-users"></i>
                                            <?php echo count($participants); ?> participants
                                        </div>
                                    </div>
                                    
                                    <?php if (!empty($expense['description'])): ?>
                                    <div class="expense-description">
                                        <h6 class="fw-bold mb-2">Description</h6>
                                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($expense['description'])); ?></p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-4 text-md-end text-center mt-4 mt-md-0">
                                    <div class="expense-amount mb-2">€<?php echo number_format($expense['amount'], 2, ',', '.'); ?></div>
                                    <span class="expense-meta">Total amount</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Participants List -->
            <div class="row">
                <div class="col-12">
                    <div class="modern-card fade-in-delay-1">
                        <div class="card-header bg-primary text-white p-4">
                            <h3 class="mb-0 fw-bold">Participants</h3>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($participants)): ?>
                                <div class="text-center p-5">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No participants found</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="modern-table">
                                        <thead>
                                            <tr>
                                                <th>Participant</th>
                                                <th>Share Amount</th>
                                                <th>Status</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($participants as $participant): ?>
                                            <tr class="<?php echo $participant['is_settled'] ? 'row-settled' : ''; ?>">
                                                <td>
                                                    <div class="expense-user mb-0">
                                                        <div class="user-avatar" style="width: 36px; height: 36px; font-size: 0.9rem; background-color: <?php echo $participant['is_settled'] ? '#10B981' : '#4F46E5'; ?>">
                                                            <?php echo strtoupper(substr($participant['username'], 0, 1)); ?>
                                                        </div>
                                                        <div class="user-details">
                                                            <span class="user-name">
                                                                <?php echo htmlspecialchars($participant['full_name'] ?: $participant['username']); ?>
                                                                <?php if ($participant['user_id'] == $userId): ?> (You)<?php endif; ?>
                                                            </span>
                                                            <span class="user-username">@<?php echo htmlspecialchars($participant['username']); ?></span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="fw-semibold">
                                                    <?php echo number_format($participant['share_amount'], 2, ',', '.'); ?> €
                                                </td>
                                                <td>
                                                    <?php if ($participant['is_settled']): ?>
                                                        <span class="status-badge settled">
                                                            <i class="fas fa-check-circle"></i>
                                                            Settled on <?php echo date('d M Y', strtotime($participant['settled_date'])); ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="status-badge pending">
                                                            <i class="fas fa-clock"></i>
                                                            Pending
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-end">
                                                    <?php if (!$participant['is_settled'] && ($expense['payer_id'] == $userId || $participant['user_id'] == $userId)): ?>
                                                        <form method="post" class="d-inline">
                                                            <input type="hidden" name="action" value="settle">
                                                            <input type="hidden" name="participant_id" value="<?php echo $participant['id']; ?>">
                                                            <button type="submit" class="btn-settle">
                                                                <i class="fas fa-check-circle me-1"></i>
                                                                Mark as Settled
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
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
