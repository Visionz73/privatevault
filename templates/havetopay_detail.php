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
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --danger-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            --glass-bg: rgba(255, 255, 255, 0.25);
            --glass-border: rgba(255, 255, 255, 0.18);
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        
        .glass-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(31, 38, 135, 0.5);
        }
        
        .gradient-primary {
            background: var(--primary-gradient);
            position: relative;
            overflow: hidden;
        }
        
        .gradient-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 100%);
            pointer-events: none;
        }
        
        .btn-modern {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
        }
        
        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            background: rgba(255, 255, 255, 0.3);
        }
        
        .user-avatar {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .status-badge {
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 12px;
            padding: 6px 12px;
        }
        
        .table-glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border-radius: 16px;
        }
        
        .table-row {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .table-row:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .container-centered {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        /* Mobile adjustments */
        @media (max-width: 768px) {
            body {
                padding-top: 4rem !important;
                font-size: 13px;
            }
            .container-centered {
                padding: 0 1rem;
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
    <div class="main-content py-8">
        <div class="container-centered">
            <!-- Back Link -->
            <div class="mb-6">
                <a href="havetopay.php" class="btn-modern text-white hover:text-white flex items-center w-fit">
                    <i class="fas fa-arrow-left mr-2"></i>Back to HaveToPay
                </a>
            </div>
            
            <!-- Success/Error Messages -->
            <?php if (!empty($success)): ?>
            <div class="glass-card mb-6 p-4 text-white">
                <div class="flex items-center text-sm">
                    <i class="fas fa-check-circle mr-3 text-green-300"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
            <div class="glass-card mb-6 p-4 text-white">
                <div class="flex items-center text-sm">
                    <i class="fas fa-exclamation-circle mr-3 text-red-300"></i>
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
                                <h1 class="text-2xl font-bold text-white"><?php echo htmlspecialchars($expense['title']); ?></h1>
                                <?php if ($expense['payer_id'] == $userId || ($_SESSION['is_admin'] ?? false)): ?>
                                    <button type="button" 
                                            onclick="confirmDeleteExpense()"
                                            class="btn-modern text-red-300 hover:text-red-200">
                                        <i class="fas fa-trash mr-2"></i>Delete
                                    </button>
                                <?php endif; ?>
                            </div>
                            
                            <div class="flex items-center mb-6">
                                <div class="user-avatar w-12 h-12 rounded-full flex items-center justify-center font-semibold mr-4 text-white text-sm">
                                    <?php echo strtoupper(substr($expense['payer_name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <div class="font-semibold text-white text-sm">
                                        <?php echo htmlspecialchars($expense['payer_full_name'] ?: $expense['payer_name']); ?>
                                        <?php if ($expense['payer_id'] == $userId): ?> (You)<?php endif; ?>
                                    </div>
                                    <div class="text-xs text-white/60">@<?php echo htmlspecialchars($expense['payer_name']); ?></div>
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap gap-4 text-xs text-white/70 mb-6">
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
                            <div class="bg-white/10 backdrop-filter backdrop-blur-lg border border-white/20 rounded-lg p-4">
                                <h6 class="font-semibold text-white mb-2 text-sm">Description</h6>
                                <p class="text-white/80 text-sm leading-relaxed"><?php echo nl2br(htmlspecialchars($expense['description'])); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="text-center">
                            <div class="text-3xl font-bold text-yellow-300 mb-2"><?php echo number_format($expense['amount'], 2); ?> €</div>
                            <div class="text-white/60 text-sm">Total amount</div>
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
                            <i class="fas fa-users text-6xl text-white/30 mb-6"></i>
                            <p class="text-white/70 text-sm">No participants found</p>
                        </div>
                    <?php else: ?>
                        <div class="table-glass rounded-2xl overflow-hidden">
                            <table class="w-full">
                                <thead class="bg-white/10">
                                    <tr>
                                        <th class="text-left py-4 px-6 font-semibold text-white text-sm">Participant</th>
                                        <th class="text-left py-4 px-6 font-semibold text-white text-sm">Share</th>
                                        <th class="text-left py-4 px-6 font-semibold text-white text-sm">Status</th>
                                        <th class="text-left py-4 px-6 font-semibold text-white text-sm">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($participants as $participant): ?>
                                    <tr class="table-row">
                                        <td class="py-4 px-6">
                                            <div class="flex items-center">
                                                <div class="user-avatar w-8 h-8 rounded-full flex items-center justify-center font-semibold mr-3 text-white text-xs">
                                                    <?php echo strtoupper(substr($participant['username'], 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-white text-sm">
                                                        <?php echo htmlspecialchars($participant['full_name'] ?: $participant['username']); ?>
                                                        <?php if ($participant['user_id'] == $userId): ?> (You)<?php endif; ?>
                                                    </div>
                                                    <div class="text-xs text-white/60">@<?php echo htmlspecialchars($participant['username']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 font-bold text-yellow-300 text-sm">
                                            <?php echo number_format($participant['share_amount'], 2); ?> €
                                        </td>
                                        <td class="py-4 px-6">
                                            <?php if ($participant['is_settled']): ?>
                                                <span class="status-badge bg-green-300/20 text-green-300 rounded-full font-medium">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Settled
                                                </span>
                                            <?php else: ?>
                                                <span class="status-badge bg-yellow-300/20 text-yellow-300 rounded-full font-medium">
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
                                                        <button type="submit" class="text-green-300 hover:text-green-200 font-medium text-xs transition-colors">
                                                            <i class="fas fa-check-circle mr-1"></i>
                                                            Settle
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                
                                                <?php if ($expense['payer_id'] == $userId || $participant['user_id'] == $userId): ?>
                                                    <button type="button" 
                                                            onclick="confirmRemoveParticipant(<?php echo $participant['id']; ?>, '<?php echo htmlspecialchars($participant['full_name'] ?: $participant['username'], ENT_QUOTES); ?>')"
                                                            class="text-red-300 hover:text-red-200 font-medium text-xs transition-colors">
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
                <i class="fas fa-exclamation-triangle text-red-300 text-2xl mr-4"></i>
                <h3 class="text-lg font-bold text-white">Delete Expense</h3>
            </div>
            <p class="text-white/80 mb-6 text-sm leading-relaxed">
                Are you sure you want to delete this entire expense? This will remove the expense and all associated participant records. This action cannot be undone.
            </p>
            <div class="flex justify-end gap-4">
                <button type="button" onclick="closeDeleteExpenseModal()" class="btn-modern text-white">
                    Cancel
                </button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete_expense">
                    <button type="submit" class="btn-modern bg-red-500/80 hover:bg-red-500 text-white border-red-400/50">
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
                <i class="fas fa-user-minus text-orange-300 text-2xl mr-4"></i>
                <h3 class="text-lg font-bold text-white">Remove Participant</h3>
            </div>
            <p class="text-white/80 mb-6 text-sm leading-relaxed">
                Are you sure you want to remove "<span id="participantName" class="font-medium text-yellow-300"></span>" from this expense? 
                The remaining participants' shares will be recalculated automatically.
            </p>
            <div class="flex justify-end gap-4">
                <button type="button" onclick="closeRemoveParticipantModal()" class="btn-modern text-white">
                    Cancel
                </button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="remove_participant">
                    <input type="hidden" name="participant_id" id="removeParticipantId" value="">
                    <button type="submit" class="btn-modern bg-orange-500/80 hover:bg-orange-500 text-white border-orange-400/50">
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
