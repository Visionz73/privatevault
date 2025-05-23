<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($expense['title']) ?> | HaveToPay</title>
    <link href="https://fonts.googleapis.com/css2?family=SF+Pro+Display:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
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
        
        .modern-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .expense-header {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 24px;
            align-items: start;
        }

        .expense-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin: 16px 0;
            font-size: 14px;
            color: var(--sf-text-secondary);
        }

        .expense-meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .expense-amount {
            text-align: center;
            padding: 24px;
            background: var(--sf-gray-light);
            border-radius: 12px;
        }

        .expense-amount-value {
            font-size: 36px;
            font-weight: 700;
            color: var(--sf-blue);
            margin: 0;
        }

        .expense-amount-label {
            color: var(--sf-text-secondary);
            font-size: 14px;
            margin-top: 4px;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.settled {
            background: rgba(52, 199, 89, 0.1);
            color: var(--sf-green);
        }

        .status-badge.pending {
            background: rgba(255, 149, 0, 0.1);
            color: var(--sf-orange);
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="main-content p-6">
        <!-- Back Link -->
        <div class="mb-6">
            <a href="havetopay.php" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to HaveToPay
            </a>
        </div>
        
        <!-- Success/Error Messages -->
        <?php if (!empty($success)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
            <i class="fas fa-check-circle mr-3"></i>
            <?php echo htmlspecialchars($success); ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-exclamation-circle mr-3"></i>
            <?php foreach ($errors as $error): ?>
                <div><?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <!-- Expense Details -->
        <div class="modern-card mb-6">
            <div class="p-6">
                <div class="expense-header">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($expense['title']); ?></h1>
                        
                        <div class="user-info" style="margin-bottom: 16px;">
                            <div class="avatar" style="width: 48px; height: 48px; font-size: 18px;">
                                <?php echo strtoupper(substr($expense['payer_name'], 0, 1)); ?>
                            </div>
                            <div class="user-details">
                                <h6 style="font-size: 16px;">
                                    <?php echo htmlspecialchars($expense['payer_full_name'] ?: $expense['payer_name']); ?>
                                    <?php if ($expense['payer_id'] == $userId): ?> (You)<?php endif; ?>
                                </h6>
                                <small>@<?php echo htmlspecialchars($expense['payer_name']); ?></small>
                            </div>
                        </div>
                        
                        <div class="expense-meta">
                            <div class="expense-meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                <?php echo date('M j, Y', strtotime($expense['expense_date'])); ?>
                            </div>
                            <?php if(!empty($expense['expense_category'])): ?>
                            <div class="expense-meta-item">
                                <i class="fas fa-tag"></i>
                                <?php echo htmlspecialchars($expense['expense_category']); ?>
                            </div>
                            <?php endif; ?>
                            <div class="expense-meta-item">
                                <i class="fas fa-users"></i>
                                <?php echo count($participants); ?> participants
                            </div>
                        </div>
                        
                        <?php if (!empty($expense['description'])): ?>
                        <div style="background: var(--sf-gray-light); padding: 16px; border-radius: 12px; margin-top: 16px;">
                            <h6 style="margin: 0 0 8px 0; font-weight: 600;">Description</h6>
                            <p style="margin: 0; color: var(--sf-text-secondary);">
                                <?php echo nl2br(htmlspecialchars($expense['description'])); ?>
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="expense-amount">
                        <div class="expense-amount-value">€<?php echo number_format($expense['amount'], 2); ?></div>
                        <div class="expense-amount-label">Total Amount</div>
                        <?php if ($expense['payer_id'] == $userId || ($_SESSION['is_admin'] ?? false)): ?>
                            <button type="button" 
                                    onclick="confirmDeleteExpense()"
                                    class="btn"
                                    style="background: var(--sf-red); color: white; margin-top: 16px; font-size: 14px; padding: 8px 16px;">
                                <i class="fas fa-trash"></i>
                                Delete
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Participants -->
        <div class="card">
            <div class="card-header">
                Participants
                <span class="tag"><?php echo count($participants); ?> people</span>
            </div>
            <div class="card-body">
                <?php if (empty($participants)): ?>
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <p>No participants found</p>
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Participant</th>
                                    <th>Share</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($participants as $participant): ?>
                                <tr style="<?php echo $participant['is_settled'] ? 'background: rgba(52, 199, 89, 0.05);' : ''; ?>">
                                    <td>
                                        <div class="user-info">
                                            <div class="avatar <?php echo $participant['is_settled'] ? '' : 'owe'; ?>" style="width: 32px; height: 32px; font-size: 14px;">
                                                <?php echo strtoupper(substr($participant['username'], 0, 1)); ?>
                                            </div>
                                            <div class="user-details">
                                                <h6 style="font-size: 14px;">
                                                    <?php echo htmlspecialchars($participant['full_name'] ?: $participant['username']); ?>
                                                    <?php if ($participant['user_id'] == $userId): ?> (You)<?php endif; ?>
                                                </h6>
                                                <small>@<?php echo htmlspecialchars($participant['username']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>€<?php echo number_format($participant['share_amount'], 2); ?></strong>
                                    </td>
                                    <td>
                                        <?php if ($participant['is_settled']): ?>
                                            <span class="status-badge settled">
                                                Settled
                                            </span>
                                            <div style="font-size: 12px; color: var(--sf-text-secondary); margin-top: 4px;">
                                                <?php echo date('M j', strtotime($participant['settled_date'])); ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="status-badge pending">
                                                Pending
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 12px;">
                                            <?php if (!$participant['is_settled'] && ($expense['payer_id'] == $userId || $participant['user_id'] == $userId)): ?>
                                                <form method="post" style="display: inline;">
                                                    <input type="hidden" name="action" value="settle">
                                                    <input type="hidden" name="participant_id" value="<?php echo $participant['id']; ?>">
                                                    <button type="submit" class="action-link" style="background: none; border: none; cursor: pointer;">
                                                        Mark Settled
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <?php if ($expense['payer_id'] == $userId || $participant['user_id'] == $userId): ?>
                                                <button type="button" 
                                                        onclick="confirmRemoveParticipant(<?php echo $participant['id']; ?>, '<?php echo htmlspecialchars($participant['full_name'] ?: $participant['username'], ENT_QUOTES); ?>')"
                                                        class="action-link danger"
                                                        style="background: none; border: none; cursor: pointer;">
                                                    Remove
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
    
    <!-- Delete Expense Confirmation Modal -->
    <div id="deleteExpenseModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 16px; padding: 24px; max-width: 400px; margin: 20px;">
            <h3 style="margin: 0 0 16px 0; font-size: 20px; font-weight: 600;">Delete Expense</h3>
            <p style="margin: 0 0 24px 0; color: var(--sf-text-secondary);">
                Are you sure you want to delete this expense? This action cannot be undone.
            </p>
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="closeDeleteExpenseModal()" class="btn btn-secondary">Cancel</button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete_expense">
                    <button type="submit" style="background: var(--sf-red); color: white;" class="btn">Delete</button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Remove Participant Confirmation Modal -->
    <div id="removeParticipantModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 16px; padding: 24px; max-width: 400px; margin: 20px;">
            <h3 style="margin: 0 0 16px 0; font-size: 20px; font-weight: 600;">Remove Participant</h3>
            <p style="margin: 0 0 24px 0; color: var(--sf-text-secondary);">
                Remove "<span id="participantName"></span>" from this expense?
            </p>
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="closeRemoveParticipantModal()" class="btn btn-secondary">Cancel</button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="remove_participant">
                    <input type="hidden" name="participant_id" id="removeParticipantId" value="">
                    <button type="submit" style="background: var(--sf-orange); color: white;" class="btn">Remove</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function confirmDeleteExpense() {
            document.getElementById('deleteExpenseModal').style.display = 'flex';
        }
        
        function closeDeleteExpenseModal() {
            document.getElementById('deleteExpenseModal').style.display = 'none';
        }
        
        function confirmRemoveParticipant(participantId, participantName) {
            document.getElementById('removeParticipantId').value = participantId;
            document.getElementById('participantName').textContent = participantName;
            document.getElementById('removeParticipantModal').style.display = 'flex';
        }
        
        function closeRemoveParticipantModal() {
            document.getElementById('removeParticipantModal').style.display = 'none';
        }
        
        // Close modals on outside click
        document.addEventListener('click', function(e) {
            if (e.target.id === 'deleteExpenseModal') closeDeleteExpenseModal();
            if (e.target.id === 'removeParticipantModal') closeRemoveParticipantModal();
        });
    </script>
</body>
</html>
