<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($expense['title']) ?> | HaveToPay</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen flex">
    <?php include_once __DIR__ . '/navbar.php'; ?>

    <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Back Link -->
        <div class="mb-8">
            <a href="havetopay.php" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium group">
                <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>Back to HaveToPay
            </a>
        </div>
        
        <!-- Success/Error Messages -->
        <?php if (!empty($success)): ?>
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-8 flex items-center shadow-sm">
            <i class="fas fa-check-circle text-xl mr-3"></i>
            <p><?php echo htmlspecialchars($success); ?></p>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-8">
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
        <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-10">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-6 text-white">
                <h1 class="text-2xl md:text-3xl font-bold"><?php echo htmlspecialchars($expense['title']); ?></h1>
            </div>
            
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <div class="md:col-span-2 space-y-8">
                        <!-- Payer Information -->
                        <div class="flex items-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-full flex items-center justify-center text-xl font-semibold mr-5">
                                <?php echo strtoupper(substr($expense['payer_name'], 0, 1)); ?>
                            </div>
                            <div>
                                <div class="text-gray-400 text-sm mb-1">Paid by</div>
                                <div class="font-semibold text-lg text-gray-800">
                                    <?php echo htmlspecialchars($expense['payer_full_name'] ?: $expense['payer_name']); ?>
                                    <?php if ($expense['payer_id'] == $userId): ?> (You)<?php endif; ?>
                                </div>
                                <div class="text-sm text-gray-500">@<?php echo htmlspecialchars($expense['payer_name']); ?></div>
                            </div>
                        </div>
                        
                        <!-- Expense Metadata -->
                        <div class="flex flex-wrap gap-6 text-sm text-gray-600">
                            <div class="bg-gray-100 px-4 py-3 rounded-xl flex items-center">
                                <i class="fas fa-calendar-alt text-indigo-500 mr-3"></i>
                                <div>
                                    <div class="text-xs text-gray-500 mb-0.5">Date</div>
                                    <div class="font-medium"><?php echo date('d M Y', strtotime($expense['expense_date'])); ?></div>
                                </div>
                            </div>
                            
                            <?php if(!empty($expense['expense_category'])): ?>
                            <div class="bg-gray-100 px-4 py-3 rounded-xl flex items-center">
                                <i class="fas fa-tag text-indigo-500 mr-3"></i>
                                <div>
                                    <div class="text-xs text-gray-500 mb-0.5">Category</div>
                                    <div class="font-medium"><?php echo htmlspecialchars($expense['expense_category']); ?></div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="bg-gray-100 px-4 py-3 rounded-xl flex items-center">
                                <i class="fas fa-users text-indigo-500 mr-3"></i>
                                <div>
                                    <div class="text-xs text-gray-500 mb-0.5">Participants</div>
                                    <div class="font-medium"><?php echo count($participants); ?> people</div>
                                </div>
                            </div>
                            
                            <?php if ($expense['payer_id'] == $userId || ($_SESSION['is_admin'] ?? false)): ?>
                                <div class="ml-auto">
                                    <button type="button" 
                                            onclick="confirmDeleteExpense()"
                                            class="bg-red-50 hover:bg-red-100 text-red-600 font-medium px-4 py-3 rounded-xl transition-colors flex items-center">
                                        <i class="fas fa-trash-alt mr-2"></i>Delete Expense
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($expense['description'])): ?>
                        <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-6">
                            <h6 class="font-semibold text-indigo-900 mb-3">Description</h6>
                            <p class="text-indigo-800 whitespace-pre-line"><?php echo htmlspecialchars($expense['description']); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Amount Card -->
                    <div>
                        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-6 text-center">
                            <div class="text-gray-500 mb-2">Total amount</div>
                            <div class="text-4xl font-bold text-indigo-600 mb-4">€<?php echo number_format($expense['amount'], 2); ?></div>
                            
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
                            
                            <div class="text-xs text-gray-500 mb-2">Settlement progress</div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2">
                                <div class="bg-green-600 h-2.5 rounded-full" style="width: <?php echo $percentage; ?>%"></div>
                            </div>
                            <div class="text-sm font-medium">
                                <?php echo $settledCount; ?> of <?php echo $totalParticipants; ?> settled
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Participants List -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-6 text-white">
                <h3 class="text-xl font-bold flex items-center">
                    <i class="fas fa-users mr-3"></i>Participants
                </h3>
            </div>
            <div class="p-8">
                <?php if (empty($participants)): ?>
                    <div class="text-center py-16">
                        <i class="fas fa-users text-6xl text-gray-200 mb-4"></i>
                        <p class="text-gray-500">No participants found</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php foreach ($participants as $participant): ?>
                            <div class="bg-gray-50 rounded-xl p-5 <?php echo $participant['is_settled'] ? 'border-2 border-green-200' : ''; ?>">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-gradient-to-br <?php echo $participant['is_settled'] ? 'from-green-500 to-green-600' : 'from-indigo-500 to-purple-600'; ?> text-white rounded-full flex items-center justify-center font-semibold mr-4">
                                            <?php echo strtoupper(substr($participant['username'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-800">
                                                <?php echo htmlspecialchars($participant['full_name'] ?: $participant['username']); ?>
                                                <?php if ($participant['user_id'] == $userId): ?> (You)<?php endif; ?>
                                            </div>
                                            <div class="text-sm text-gray-500">@<?php echo htmlspecialchars($participant['username']); ?></div>
                                        </div>
                                    </div>
                                    
                                    <span class="font-semibold text-lg text-gray-800">
                                        <?php echo number_format($participant['share_amount'], 2); ?> €
                                    </span>
                                </div>
                                
                                <div class="flex justify-between items-center">
                                    <?php if ($participant['is_settled']): ?>
                                        <span class="bg-green-100 text-green-700 px-3 py-1.5 rounded-lg text-sm font-medium inline-flex items-center">
                                            <i class="fas fa-check-circle mr-1.5"></i>
                                            Settled on <?php echo date('d M Y', strtotime($participant['settled_date'])); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="bg-yellow-100 text-yellow-700 px-3 py-1.5 rounded-lg text-sm font-medium inline-flex items-center">
                                            <i class="fas fa-clock mr-1.5"></i>
                                            Pending
                                        </span>
                                    <?php endif; ?>
                                    
                                    <div class="flex gap-2">
                                        <?php if (!$participant['is_settled'] && ($expense['payer_id'] == $userId || $participant['user_id'] == $userId)): ?>
                                            <form method="post" class="inline">
                                                <input type="hidden" name="action" value="settle">
                                                <input type="hidden" name="participant_id" value="<?php echo $participant['id']; ?>">
                                                <button type="submit" class="text-green-600 hover:text-green-800 font-medium text-sm bg-green-50 hover:bg-green-100 px-3 py-1.5 rounded-lg transition-colors">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Mark as Settled
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <?php if ($expense['payer_id'] == $userId || $participant['user_id'] == $userId): ?>
                                            <button type="button" 
                                                    onclick="confirmRemoveParticipant(<?php echo $participant['id']; ?>, '<?php echo htmlspecialchars($participant['full_name'] ?: $participant['username'], ENT_QUOTES); ?>')"
                                                    class="text-red-600 hover:text-red-800 font-medium text-sm bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-colors">
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
    <div id="deleteExpenseModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl p-8 max-w-md mx-4 shadow-xl">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-red-100 text-red-600 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800">Delete Expense</h3>
            </div>
            <p class="text-gray-600 mb-8">
                Are you sure you want to delete this entire expense? This will remove the expense and all associated participant records. This action cannot be undone.
            </p>
            <div class="flex justify-end gap-4">
                <button type="button" onclick="closeDeleteExpenseModal()" class="px-5 py-3 text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 font-medium transition-colors">
                    Cancel
                </button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete_expense">
                    <button type="submit" class="px-5 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 font-medium transition-colors">
                        <i class="fas fa-trash mr-2"></i>Delete Expense
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Remove Participant Confirmation Modal -->
    <div id="removeParticipantModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl p-8 max-w-md mx-4 shadow-xl">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-user-minus text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800">Remove Participant</h3>
            </div>
            <p class="text-gray-600 mb-8">
                Are you sure you want to remove "<span id="participantName" class="font-medium"></span>" from this expense? 
                The remaining participants' shares will be recalculated automatically.
            </p>
            <div class="flex justify-end gap-4">
                <button type="button" onclick="closeRemoveParticipantModal()" class="px-5 py-3 text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 font-medium transition-colors">
                    Cancel
                </button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="remove_participant">
                    <input type="hidden" name="participant_id" id="removeParticipantId" value="">
                    <button type="submit" class="px-5 py-3 bg-orange-600 text-white rounded-xl hover:bg-orange-700 font-medium transition-colors">
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
