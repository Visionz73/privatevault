<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($expense['title']) ?> | HaveToPay</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2">
                        <div class="flex justify-between items-start mb-4">
                            <h1 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($expense['title']); ?></h1>
                            <?php if ($expense['payer_id'] == $userId || ($_SESSION['is_admin'] ?? false)): ?>
                                <button type="button" 
                                        onclick="confirmDeleteExpense()"
                                        class="text-red-600 hover:text-red-800 font-medium bg-red-50 hover:bg-red-100 px-3 py-2 rounded-lg transition-colors">
                                    <i class="fas fa-trash mr-2"></i>Delete Expense
                                </button>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-indigo-600 text-white rounded-full flex items-center justify-center font-semibold mr-4">
                                <?php echo strtoupper(substr($expense['payer_name'], 0, 1)); ?>
                            </div>
                            <div>
                                <div class="font-semibold">
                                    <?php echo htmlspecialchars($expense['payer_full_name'] ?: $expense['payer_name']); ?>
                                    <?php if ($expense['payer_id'] == $userId): ?> (You)<?php endif; ?>
                                </div>
                                <div class="text-sm text-gray-500">@<?php echo htmlspecialchars($expense['payer_name']); ?></div>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-4">
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
                        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                            <h6 class="font-semibold text-indigo-900 mb-2">Description</h6>
                            <p class="text-indigo-800"><?php echo nl2br(htmlspecialchars($expense['description'])); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-4xl font-bold text-indigo-600">€<?php echo number_format($expense['amount'], 2); ?></div>
                        <div class="text-gray-500">Total amount</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Participants List -->
        <div class="modern-card">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 text-white p-4 rounded-t-2xl">
                <h3 class="text-xl font-bold flex items-center">
                    <i class="fas fa-users mr-3"></i>Participants
                </h3>
            </div>
            <div class="p-6">
                <?php if (empty($participants)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">No participants found</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="border-b border-gray-200">
                                <tr>
                                    <th class="text-left py-3 font-medium text-gray-600">Participant</th>
                                    <th class="text-left py-3 font-medium text-gray-600">Share Amount</th>
                                    <th class="text-left py-3 font-medium text-gray-600">Status</th>
                                    <th class="text-left py-3 font-medium text-gray-600">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($participants as $participant): ?>
                                <tr class="<?php echo $participant['is_settled'] ? 'bg-green-50' : ''; ?>">
                                    <td class="py-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-<?php echo $participant['is_settled'] ? 'green' : 'indigo'; ?>-600 text-white rounded-full flex items-center justify-center font-semibold mr-3">
                                                <?php echo strtoupper(substr($participant['username'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="font-medium">
                                                    <?php echo htmlspecialchars($participant['full_name'] ?: $participant['username']); ?>
                                                    <?php if ($participant['user_id'] == $userId): ?> (You)<?php endif; ?>
                                                </div>
                                                <div class="text-sm text-gray-500">@<?php echo htmlspecialchars($participant['username']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 font-semibold">
                                        <?php echo number_format($participant['share_amount'], 2); ?> €
                                    </td>
                                    <td class="py-4">
                                        <?php if ($participant['is_settled']): ?>
                                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Settled on <?php echo date('d M Y', strtotime($participant['settled_date'])); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm font-medium">
                                                <i class="fas fa-clock mr-1"></i>
                                                Pending
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4">
                                        <div class="flex gap-2">
                                            <?php if (!$participant['is_settled'] && ($expense['payer_id'] == $userId || $participant['user_id'] == $userId)): ?>
                                                <form method="post" class="inline">
                                                    <input type="hidden" name="action" value="settle">
                                                    <input type="hidden" name="participant_id" value="<?php echo $participant['id']; ?>">
                                                    <button type="submit" class="text-green-600 hover:text-green-800 font-medium text-sm">
                                                        <i class="fas fa-check-circle mr-1"></i>
                                                        Mark as Settled
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <?php if ($expense['payer_id'] == $userId || $participant['user_id'] == $userId): ?>
                                                <button type="button" 
                                                        onclick="confirmRemoveParticipant(<?php echo $participant['id']; ?>, '<?php echo htmlspecialchars($participant['full_name'] ?: $participant['username'], ENT_QUOTES); ?>')"
                                                        class="text-red-600 hover:text-red-800 font-medium text-sm">
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
    
    <!-- Delete Expense Confirmation Modal -->
    <div id="deleteExpenseModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md mx-4">
            <div class="flex items-center mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl mr-3"></i>
                <h3 class="text-lg font-semibold">Delete Expense</h3>
            </div>
            <p class="text-gray-600 mb-6">
                Are you sure you want to delete this entire expense? This will remove the expense and all associated participant records. This action cannot be undone.
            </p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeDeleteExpenseModal()" class="px-4 py-2 text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-300">
                    Cancel
                </button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete_expense">
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        <i class="fas fa-trash mr-2"></i>Delete Expense
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Remove Participant Confirmation Modal -->
    <div id="removeParticipantModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md mx-4">
            <div class="flex items-center mb-4">
                <i class="fas fa-user-minus text-orange-500 text-2xl mr-3"></i>
                <h3 class="text-lg font-semibold">Remove Participant</h3>
            </div>
            <p class="text-gray-600 mb-6">
                Are you sure you want to remove "<span id="participantName" class="font-medium"></span>" from this expense? 
                The remaining participants' shares will be recalculated automatically.
            </p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeRemoveParticipantModal()" class="px-4 py-2 text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-300">
                    Cancel
                </button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="remove_participant">
                    <input type="hidden" name="participant_id" id="removeParticipantId" value="">
                    <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
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
