<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($expense['title']) ?> | HaveToPay</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            --success-gradient: linear-gradient(135deg, #059669 0%, #047857 100%);
            --danger-gradient: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            --glass-bg: rgba(255, 255, 255, 0.9);
            --glass-border: rgba(0, 0, 0, 0.1);
        }
        
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #cbd5e1 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            color: #1f2937;
            padding: 2rem;
        }
        
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }
        
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        .gradient-primary {
            background: var(--primary-gradient);
            color: white;
        }
        
        .btn-modern {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 13px;
            color: #374151;
            transition: all 0.3s ease;
        }
        
        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
            border: none;
        }
        
        .user-avatar {
            background: linear-gradient(135deg, #4f46e5, #3730a3);
            border: 2px solid rgba(255, 255, 255, 0.9);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            color: white;
        }
        
        .status-badge {
            border: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .table-glass {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 16px;
        }
        
        .table-row {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .table-row:hover {
            background: rgba(0, 0, 0, 0.02);
        }
        
        .info-card {
            background: rgba(255, 255, 255, 0.6);
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 12px;
            padding: 16px;
        }
        
        .text-primary {
            color: #1f2937;
        }
        
        .text-secondary {
            color: #6b7280;
        }
        
        .text-muted {
            color: #9ca3af;
        }
        
        /* Mobile adjustments */
        @media (max-width: 768px) {
            body {
                padding: 1rem;
                padding-top: 5rem !important;
            }
            
            .main-container {
                max-width: 100%;
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
<body>
    <div class="main-container">
        <div class="main-content">
            <!-- Back Link -->
            <div class="mb-6">
                <a href="havetopay.php" class="btn-modern flex items-center w-fit">
                    <i class="fas fa-arrow-left mr-2"></i>Back to HaveToPay
                </a>
            </div>
            
            <!-- Success/Error Messages -->
            <?php if (!empty($success)): ?>
            <div class="glass-card mb-6 p-4 text-primary">
                <div class="flex items-center text-sm">
                    <i class="fas fa-check-circle mr-3 text-green-600"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
            <div class="glass-card mb-6 p-4 text-primary">
                <div class="flex items-center text-sm">
                    <i class="fas fa-exclamation-circle mr-3 text-red-600"></i>
                    <div>
                        <?php foreach ($errors as $error): ?>
                            <div><?php echo htmlspecialchars($error); ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Expense Details -->
            <div class="glass-card mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2">
                            <div class="flex justify-between items-start mb-6">
                                <h1 class="text-2xl font-bold text-primary"><?php echo htmlspecialchars($expense['title']); ?></h1>
                                <?php if ($expense['payer_id'] == $userId || ($_SESSION['is_admin'] ?? false)): ?>
                                    <button type="button" 
                                            onclick="confirmDeleteExpense()"
                                            class="btn-modern btn-danger">
                                        <i class="fas fa-trash mr-2"></i>Delete
                                    </button>
                                <?php endif; ?>
                            </div>
                            
                            <div class="flex items-center mb-6">
                                <div class="user-avatar w-12 h-12 rounded-full flex items-center justify-center font-semibold mr-4 text-sm">
                                    <?php echo strtoupper(substr($expense['payer_name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <div class="font-semibold text-primary text-sm">
                                        <?php echo htmlspecialchars($expense['payer_full_name'] ?: $expense['payer_name']); ?>
                                        <?php if ($expense['payer_id'] == $userId): ?> (You)<?php endif; ?>
                                    </div>
                                    <div class="text-xs text-secondary">@<?php echo htmlspecialchars($expense['payer_name']); ?></div>
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap gap-4 text-xs text-secondary mb-6">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    <?php echo date('d M Y', strtotime($expense['expense_date'])); ?>
                                </div>
                                <?php if(!empty($expense['expense_category'])): ?>
                                <div class="flex items-center">
                                    <i class="fas fa-tag mr-2"></i>
                                    <?php echo htmlspecialchars($expense['expense_category']); ?>
                                </div>
                                <?php endif; ?>
                                <div class="flex items-center">
                                    <i class="fas fa-users mr-2"></i>
                                    <?php echo count($participants); ?> participants
                                </div>
                            </div>
                            
                            <?php if (!empty($expense['description'])): ?>
                            <div class="info-card">
                                <h6 class="font-semibold text-primary mb-2 text-sm">Description</h6>
                                <p class="text-secondary text-sm leading-relaxed"><?php echo nl2br(htmlspecialchars($expense['description'])); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="text-center">
                            <div class="text-3xl font-bold text-indigo-600 mb-2"><?php echo number_format($expense['amount'], 2); ?> €</div>
                            <div class="text-secondary text-sm">Total amount</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Participants List -->
            <div class="glass-card">
                <div class="gradient-primary text-white p-5 rounded-t-3xl">
                    <h3 class="text-lg font-bold flex items-center">
                        <i class="fas fa-users mr-3"></i>Participants
                    </h3>
                </div>
                <div class="p-6">
                    <?php if (empty($participants)): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-users text-6xl text-gray-300 mb-6"></i>
                            <p class="text-secondary text-sm">No participants found</p>
                        </div>
                    <?php else: ?>
                        <div class="table-glass rounded-2xl overflow-hidden">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="text-left py-4 px-6 font-semibold text-primary text-sm">Participant</th>
                                        <th class="text-left py-4 px-6 font-semibold text-primary text-sm">Share</th>
                                        <th class="text-left py-4 px-6 font-semibold text-primary text-sm">Status</th>
                                        <th class="text-left py-4 px-6 font-semibold text-primary text-sm">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($participants as $participant): ?>
                                    <tr class="table-row">
                                        <td class="py-4 px-6">
                                            <div class="flex items-center">
                                                <div class="user-avatar w-8 h-8 rounded-full flex items-center justify-center font-semibold mr-3 text-xs">
                                                    <?php echo strtoupper(substr($participant['username'], 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-primary text-sm">
                                                        <?php echo htmlspecialchars($participant['full_name'] ?: $participant['username']); ?>
                                                        <?php if ($participant['user_id'] == $userId): ?> (You)<?php endif; ?>
                                                    </div>
                                                    <div class="text-xs text-secondary">@<?php echo htmlspecialchars($participant['username']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 font-bold text-indigo-600 text-sm">
                                            <?php echo number_format($participant['share_amount'], 2); ?> €
                                        </td>
                                        <td class="py-4 px-6">
                                            <?php if ($participant['is_settled']): ?>
                                                <span class="status-badge bg-green-100 text-green-700 rounded-full font-medium px-3 py-1 text-xs">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Settled
                                                </span>
                                            <?php else: ?>
                                                <span class="status-badge bg-yellow-100 text-yellow-700 rounded-full font-medium px-3 py-1 text-xs">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    Pending
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="flex gap-3">
                                                <?php if (!$participant['is_settled'] && ($expense['payer_id'] == $userId || $participant['user_id'] == $userId)): ?>
                                                    <form method="post" class="inline">
                                                        <input type="hidden" name="action" value="settle">
                                                        <input type="hidden" name="participant_id" value="<?php echo $participant['id']; ?>">
                                                        <button type="submit" class="text-green-600 hover:text-green-800 font-medium text-xs transition-colors">
                                                            <i class="fas fa-check-circle mr-1"></i>
                                                            Settle
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                
                                                <?php if ($expense['payer_id'] == $userId || $participant['user_id'] == $userId): ?>
                                                    <button type="button" 
                                                            onclick="confirmRemoveParticipant(<?php echo $participant['id']; ?>, '<?php echo htmlspecialchars($participant['full_name'] ?: $participant['username'], ENT_QUOTES); ?>')"
                                                            class="text-red-600 hover:text-red-800 font-medium text-xs transition-colors">
                                                        <i class="fas fa-user-minus mr-1"></i>
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
    </div>
    
    <!-- Delete Expense Confirmation Modal -->
    <div id="deleteExpenseModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="glass-card max-w-md mx-4 p-6">
            <div class="flex items-center mb-6">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl mr-4"></i>
                <h3 class="text-lg font-bold text-primary">Delete Expense</h3>
            </div>
            <p class="text-secondary mb-6 text-sm leading-relaxed">
                Are you sure you want to delete this entire expense? This will remove the expense and all associated participant records. This action cannot be undone.
            </p>
            <div class="flex justify-end gap-4">
                <button type="button" onclick="closeDeleteExpenseModal()" class="btn-modern">
                    Cancel
                </button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete_expense">
                    <button type="submit" class="btn-modern btn-danger">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Remove Participant Confirmation Modal -->
    <div id="removeParticipantModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="glass-card max-w-md mx-4 p-6">
            <div class="flex items-center mb-6">
                <i class="fas fa-user-minus text-orange-500 text-2xl mr-4"></i>
                <h3 class="text-lg font-bold text-primary">Remove Participant</h3>
            </div>
            <p class="text-secondary mb-6 text-sm leading-relaxed">
                Are you sure you want to remove "<span id="participantName" class="font-medium text-primary"></span>" from this expense? 
                The remaining participants' shares will be recalculated automatically.
            </p>
            <div class="flex justify-end gap-4">
                <button type="button" onclick="closeRemoveParticipantModal()" class="btn-modern">
                    Cancel
                </button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="remove_participant">
                    <input type="hidden" name="participant_id" id="removeParticipantId" value="">
                    <button type="submit" class="btn-modern bg-orange-500 hover:bg-orange-600 text-white border-orange-500">
                        <i class="fas fa-user-minus mr-2"></i>Remove
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function confirmDeleteExpense() {
            document.getElementById('deleteExpenseModal').classList.remove('hidden');
            document.getElementById('deleteExpenseModal').classList.add('flex');
        }
        
        function closeDeleteExpenseModal() {
            document.getElementById('deleteExpenseModal').classList.add('hidden');
            document.getElementById('deleteExpenseModal').classList.remove('flex');
        }
        
        function confirmRemoveParticipant(participantId, participantName) {
            document.getElementById('removeParticipantId').value = participantId;
            document.getElementById('participantName').textContent = participantName;
            document.getElementById('removeParticipantModal').classList.remove('hidden');
            document.getElementById('removeParticipantModal').classList.add('flex');
        }
        
        function closeRemoveParticipantModal() {
            document.getElementById('removeParticipantModal').classList.add('hidden');
            document.getElementById('removeParticipantModal').classList.remove('flex');
        }
        
        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.id === 'deleteExpenseModal') {
                closeDeleteExpenseModal();
            }
            if (e.target.id === 'removeParticipantModal') {
                closeRemoveParticipantModal();
            }
        });
    </script>
</body>
</html>
