<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($expense['title']) ?> | HaveToPay</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #2d1b69 0%, #11101d 50%, #1a0909 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="min-h-screen flex">
    <?php include_once __DIR__ . '/navbar.php'; ?>

    <main class="ml-0 mt-16 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Back Link -->
        <div class="mb-6">
            <a href="havetopay.php" class="inline-flex items-center text-blue-400 hover:text-blue-300 font-medium group transition-colors">
                <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>Back to HaveToPay
            </a>
        </div>
        
        <!-- Success/Error Messages -->
        <?php if (!empty($success)): ?>
        <div class="bg-green-500/20 border border-green-400/30 backdrop-blur-sm text-green-300 p-4 rounded-xl mb-6 flex items-center">
            <i class="fas fa-check-circle text-xl mr-3"></i>
            <p><?php echo htmlspecialchars($success); ?></p>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
        <div class="bg-red-500/20 border border-red-400/30 backdrop-blur-sm text-red-300 p-4 rounded-xl mb-6">
            <div class="flex">
                <i class="fas fa-exclamation-circle text-xl mr-3 mt-0.5"></i>
                <div>
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Expense Details Card -->
        <div class="bg-gradient-to-br from-purple-900/20 via-gray-900/30 to-red-900/20 backdrop-blur-xl rounded-3xl border border-white/10 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-purple-600/30 via-indigo-700/40 to-purple-800/30 backdrop-blur-sm px-8 py-6 border-b border-white/10">
                <h1 class="text-2xl md:text-3xl font-bold text-white/90"><?php echo htmlspecialchars($expense['title']); ?></h1>
            </div>
            
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="md:col-span-2 space-y-6">
                        <!-- Payer Information -->
                        <div class="flex items-center p-4 bg-white/5 border border-white/10 rounded-2xl backdrop-blur-sm">
                            <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-indigo-600 text-white rounded-2xl flex items-center justify-center text-lg font-semibold mr-4">
                                <?php echo strtoupper(substr($expense['payer_name'], 0, 1)); ?>
                            </div>
                            <div>
                                <div class="text-white/50 text-sm mb-1">Paid by</div>
                                <div class="font-semibold text-lg text-white/90">
                                    <?php echo htmlspecialchars($expense['payer_full_name'] ?: $expense['payer_name']); ?>
                                    <?php if ($expense['payer_id'] == $userId): ?> (You)<?php endif; ?>
                                </div>
                                <div class="text-sm text-white/60">@<?php echo htmlspecialchars($expense['payer_name']); ?></div>
                            </div>
                        </div>
                        
                        <!-- Expense Metadata -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="bg-white/5 border border-white/10 px-4 py-3 rounded-xl backdrop-blur-sm">
                                <div class="text-xs text-white/50 mb-1">Date</div>
                                <div class="font-medium text-white/90"><?php echo date('d M Y', strtotime($expense['expense_date'])); ?></div>
                            </div>
                            
                            <?php if(!empty($expense['expense_category'])): ?>
                            <div class="bg-white/5 border border-white/10 px-4 py-3 rounded-xl backdrop-blur-sm">
                                <div class="text-xs text-white/50 mb-1">Category</div>
                                <div class="font-medium text-white/90"><?php echo htmlspecialchars($expense['expense_category']); ?></div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="bg-white/5 border border-white/10 px-4 py-3 rounded-xl backdrop-blur-sm">
                                <div class="text-xs text-white/50 mb-1">Participants</div>
                                <div class="font-medium text-white/90"><?php echo count($participants); ?> people</div>
                            </div>
                            
                            <?php if ($expense['payer_id'] == $userId || ($_SESSION['is_admin'] ?? false)): ?>
                                <div class="bg-white/5 border border-white/10 px-4 py-3 rounded-xl backdrop-blur-sm">
                                    <button type="button" 
                                            onclick="confirmDeleteExpense()"
                                            class="text-red-400 hover:text-red-300 font-medium text-sm transition-colors">
                                        <i class="fas fa-trash-alt mr-1"></i>Delete
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($expense['description'])): ?>
                        <div class="bg-blue-500/10 border border-blue-400/20 rounded-2xl p-6 backdrop-blur-sm">
                            <h6 class="font-semibold text-blue-300 mb-3">Description</h6>
                            <p class="text-blue-200 whitespace-pre-line"><?php echo htmlspecialchars($expense['description']); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Amount Card -->
                    <div>
                        <div class="bg-white/5 border border-white/10 rounded-2xl p-6 text-center backdrop-blur-sm">
                            <div class="text-white/50 mb-2">Total amount</div>
                            <div class="text-4xl font-bold text-white/90 mb-4">€<?php echo number_format($expense['amount'], 2); ?></div>
                            
                            <?php 
                            // Calculate how many people have settled
                            $settledCount = 0;
                            foreach ($participants as $p) {
                                if ($p['is_settled']) $settledCount++;
                            }
                            
                            // Calculate percentage
                            $totalParticipants = count($participants);
                            $percentage = $totalParticipants > 0 ? ($settledCount / $totalParticipants) * 100 : 0;
                            ?>
                            
                            <div class="text-xs text-white/50 mb-2">Settlement progress</div>
                            <div class="w-full bg-white/10 rounded-full h-2.5 mb-2">
                                <div class="bg-green-400 h-2.5 rounded-full transition-all duration-300" style="width: <?php echo $percentage; ?>%"></div>
                            </div>
                            <div class="text-sm font-medium text-white/80">
                                <?php echo $settledCount; ?> of <?php echo $totalParticipants; ?> settled
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Participants List -->
        <div class="bg-gradient-to-br from-purple-900/20 via-gray-900/30 to-red-900/20 backdrop-blur-xl rounded-3xl border border-white/10 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600/30 via-indigo-700/40 to-purple-800/30 backdrop-blur-sm px-8 py-6 border-b border-white/10">
                <h3 class="text-xl font-bold flex items-center text-white/90">
                    <i class="fas fa-users mr-3"></i>Participants
                </h3>
            </div>
            <div class="p-8">
                <?php if (empty($participants)): ?>
                    <div class="text-center py-16">
                        <i class="fas fa-users text-6xl text-white/20 mb-4"></i>
                        <p class="text-white/50">No participants found</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach ($participants as $participant): ?>
                            <div class="bg-white/5 border border-white/10 rounded-2xl p-5 backdrop-blur-sm <?php echo $participant['is_settled'] ? 'border-green-400/30' : ''; ?>">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-gradient-to-br <?php echo $participant['is_settled'] ? 'from-green-500 to-green-600' : 'from-purple-500 to-indigo-600'; ?> text-white rounded-xl flex items-center justify-center font-semibold mr-4">
                                            <?php echo strtoupper(substr($participant['username'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="font-medium text-white/90">
                                                <?php echo htmlspecialchars($participant['full_name'] ?: $participant['username']); ?>
                                                <?php if ($participant['user_id'] == $userId): ?> (You)<?php endif; ?>
                                            </div>
                                            <div class="text-sm text-white/50">@<?php echo htmlspecialchars($participant['username']); ?></div>
                                        </div>
                                    </div>
                                    
                                    <span class="font-semibold text-lg text-white/90">
                                        <?php echo number_format($participant['share_amount'], 2); ?> €
                                    </span>
                                </div>
                                
                                <div class="flex justify-between items-center">
                                    <?php if ($participant['is_settled']): ?>
                                        <span class="bg-green-500/20 border border-green-400/30 text-green-300 px-3 py-1.5 rounded-lg text-sm font-medium inline-flex items-center backdrop-blur-sm">
                                            <i class="fas fa-check-circle mr-1.5"></i>
                                            Settled on <?php echo date('d M Y', strtotime($participant['settled_date'])); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="bg-yellow-500/20 border border-yellow-400/30 text-yellow-300 px-3 py-1.5 rounded-lg text-sm font-medium inline-flex items-center backdrop-blur-sm">
                                            <i class="fas fa-clock mr-1.5"></i>
                                            Pending
                                        </span>
                                    <?php endif; ?>
                                    
                                    <div class="flex gap-2">
                                        <?php if (!$participant['is_settled'] && ($expense['payer_id'] == $userId || $participant['user_id'] == $userId)): ?>
                                            <form method="post" class="inline">
                                                <input type="hidden" name="action" value="settle">
                                                <input type="hidden" name="participant_id" value="<?php echo $participant['id']; ?>">
                                                <button type="submit" class="text-green-400 hover:text-green-300 font-medium text-sm bg-green-500/10 border border-green-400/20 hover:bg-green-500/20 px-3 py-1.5 rounded-lg transition-all backdrop-blur-sm">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Settle
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <?php if ($expense['payer_id'] == $userId || $participant['user_id'] == $userId): ?>
                                            <button type="button" 
                                                    onclick="confirmRemoveParticipant(<?php echo $participant['id']; ?>, '<?php echo htmlspecialchars($participant['full_name'] ?: $participant['username'], ENT_QUOTES); ?>')"
                                                    class="text-red-400 hover:text-red-300 font-medium text-sm bg-red-500/10 border border-red-400/20 hover:bg-red-500/20 px-3 py-1.5 rounded-lg transition-all backdrop-blur-sm">
                                                <i class="fas fa-user-minus mr-1"></i>
                                                Remove
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Delete Expense Confirmation Modal -->
    <div id="deleteExpenseModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-gradient-to-br from-purple-900/40 via-gray-900/50 to-red-900/40 backdrop-blur-xl border border-white/20 rounded-3xl p-8 max-w-md mx-4">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-red-500/20 border border-red-400/30 text-red-400 rounded-2xl flex items-center justify-center mr-4">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-white/90">Delete Expense</h3>
            </div>
            <p class="text-white/70 mb-8">
                Are you sure you want to delete this entire expense? This will remove the expense and all associated participant records. This action cannot be undone.
            </p>
            <div class="flex justify-end gap-4">
                <button type="button" onclick="closeDeleteExpenseModal()" class="px-5 py-3 text-white/70 bg-white/10 border border-white/20 rounded-xl hover:bg-white/20 font-medium transition-all backdrop-blur-sm">
                    Cancel
                </button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete_expense">
                    <button type="submit" class="px-5 py-3 bg-red-500/30 border border-red-400/40 text-red-300 rounded-xl hover:bg-red-500/40 font-medium transition-all backdrop-blur-sm">
                        <i class="fas fa-trash mr-2"></i>Delete Expense
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Remove Participant Confirmation Modal -->
    <div id="removeParticipantModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-gradient-to-br from-purple-900/40 via-gray-900/50 to-red-900/40 backdrop-blur-xl border border-white/20 rounded-3xl p-8 max-w-md mx-4">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-orange-500/20 border border-orange-400/30 text-orange-400 rounded-2xl flex items-center justify-center mr-4">
                    <i class="fas fa-user-minus text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-white/90">Remove Participant</h3>
            </div>
            <p class="text-white/70 mb-8">
                Are you sure you want to remove "<span id="participantName" class="font-medium text-white/90"></span>" from this expense? 
                The remaining participants' shares will be recalculated automatically.
            </p>
            <div class="flex justify-end gap-4">
                <button type="button" onclick="closeRemoveParticipantModal()" class="px-5 py-3 text-white/70 bg-white/10 border border-white/20 rounded-xl hover:bg-white/20 font-medium transition-all backdrop-blur-sm">
                    Cancel
                </button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="remove_participant">
                    <input type="hidden" name="participant_id" id="removeParticipantId" value="">
                    <button type="submit" class="px-5 py-3 bg-orange-500/30 border border-orange-400/40 text-orange-300 rounded-xl hover:bg-orange-500/40 font-medium transition-all backdrop-blur-sm">
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
    </div>
    </main>
</body>
</html>
