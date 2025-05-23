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
            --success-gradient: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            --danger-gradient: linear-gradient(135deg, #f56565 0%, #c53030 100%);
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            --hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        .glass-card {
            background-color: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .glass-card:hover {
            box-shadow: var(--hover-shadow);
        }
        
        .gradient-primary {
            background: var(--primary-gradient);
        }
        
        .apple-btn {
            border-radius: 16px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
            display: inline-flex;
            align-items: center;
        }
        
        .apple-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        
        /* Avatar styling */
        .avatar {
            background: var(--primary-gradient);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-transform: uppercase;
            font-weight: 600;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }
        
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
        
        /* Modern Table */
        .modern-table th {
            color: #4a5568;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.75rem;
            padding: 1rem;
        }
        
        .modern-table tbody tr {
            transition: all 0.2s ease;
        }
        
        .modern-table tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }
        
        .animated-bg {
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }
        
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen">
    <?php require_once __DIR__ . '/../templates/navbar.php'; ?>
    
    <div class="main-content p-6 animated-bg">
        <!-- Back Link -->
        <div class="mb-6">
            <a href="havetopay.php" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium transition-all hover:translate-x-[-4px]">
                <i class="fas fa-arrow-left mr-2"></i>Back to HaveToPay
            </a>
        </div>
        
        <!-- Success/Error Messages -->
        <?php if (!empty($success)): ?>
        <div class="glass-card border-l-4 border-green-500 p-4 mb-6 flex items-center">
            <i class="fas fa-check-circle mr-3 text-green-500 text-xl"></i>
            <p class="text-green-700"><?php echo htmlspecialchars($success); ?></p>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
        <div class="glass-card border-l-4 border-red-500 p-4 mb-6">
            <i class="fas fa-exclamation-circle mr-3 text-red-500 text-xl"></i>
            <?php foreach ($errors as $error): ?>
                <p class="text-red-700"><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <!-- Expense Details -->
        <div class="glass-card mb-6">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2">
                        <div class="flex justify-between items-start mb-4">
                            <h1 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($expense['title']); ?></h1>
                            <?php if ($expense['payer_id'] == $userId || ($_SESSION['is_admin'] ?? false)): ?>
                                <button type="button" 
                                        onclick="confirmDeleteExpense()"
                                        class="apple-btn bg-red-500 text-white hover:bg-red-600 transition-colors">
                                    <i class="fas fa-trash mr-2"></i>Delete Expense
                                </button>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex items-center mb-4">
                            <div class="avatar w-12 h-12 mr-4">
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
                        <div class="bg-indigo-50/60 backdrop-blur-sm rounded-2xl border border-indigo-100 p-4">
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
        <div class="glass-card">
            <div class="gradient-primary text-white p-4 rounded-t-3xl">
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
                        <table class="w-full modern-table">
                            <thead class="border-b border-gray-100">
                                <tr>
                                    <th class="text-left">Participant</th>
                                    <th class="text-left">Share Amount</th>
                                    <th class="text-left">Status</th>
                                    <th class="text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php foreach ($participants as $participant): ?>
                                <tr class="<?php echo $participant['is_settled'] ? 'bg-green-50/50' : ''; ?> hover:bg-gray-50/50 backdrop-blur-sm">
                                    <td class="py-4 px-3">
                                        <div class="flex items-center">
                                            <div class="avatar w-10 h-10 mr-3" 
                                                 style="background: <?php echo $participant['is_settled'] ? 'var(--success-gradient)' : 'var(--primary-gradient)'; ?>;">
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
                                    <td class="py-4 px-3 font-semibold">
                                        <?php echo number_format($participant['share_amount'], 2); ?> €
                                    </td>
                                    <td class="py-4 px-3">
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
                                    <td class="py-4 px-3">
                                        <div class="flex gap-2">
                                            <?php if (!$participant['is_settled'] && ($expense['payer_id'] == $userId || $participant['user_id'] == $userId)): ?>
                                                <form method="post" class="inline">
                                                    <input type="hidden" name="action" value="settle">
                                                    <input type="hidden" name="participant_id" value="<?php echo $participant['id']; ?>">
                                                    <button type="submit" class="text-green-600 hover:text-green-800 font-medium text-sm hover:underline">
                                                        <i class="fas fa-check-circle mr-1"></i>
                                                        Mark as Settled
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <?php if ($expense['payer_id'] == $userId || $participant['user_id'] == $userId): ?>
                                                <button type="button" 
                                                        onclick="confirmRemoveParticipant(<?php echo $participant['id']; ?>, '<?php echo htmlspecialchars($participant['full_name'] ?: $participant['username'], ENT_QUOTES); ?>')"
                                                        class="text-red-600 hover:text-red-800 font-medium text-sm hover:underline">
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
    <div id="deleteExpenseModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white/80 backdrop-blur-md rounded-3xl p-6 max-w-md mx-4 shadow-xl border border-white/20">
            <div class="flex items-center mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl mr-3"></i>
                <h3 class="text-lg font-semibold">Delete Expense</h3>
            </div>
            <p class="text-gray-600 mb-6">
                Are you sure you want to delete this entire expense? This will remove the expense and all associated participant records. This action cannot be undone.
            </p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeDeleteExpenseModal()" class="px-4 py-2 text-gray-600 bg-gray-200 rounded-xl hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete_expense">
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors">
                        <i class="fas fa-trash mr-2"></i>Delete Expense
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Remove Participant Confirmation Modal -->
    <div id="removeParticipantModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white/80 backdrop-blur-md rounded-3xl p-6 max-w-md mx-4 shadow-xl border border-white/20">
            <div class="flex items-center mb-4">
                <i class="fas fa-user-minus text-orange-500 text-2xl mr-3"></i>
                <h3 class="text-lg font-semibold">Remove Participant</h3>
            </div>
            <p class="text-gray-600 mb-6">
                Are you sure you want to remove "<span id="participantName" class="font-medium"></span>" from this expense? 
                The remaining participants' shares will be recalculated automatically.
            </p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeRemoveParticipantModal()" class="px-4 py-2 text-gray-600 bg-gray-200 rounded-xl hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="remove_participant">
                    <input type="hidden" name="participant_id" id="removeParticipantId" value="">
                    <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-xl hover:bg-orange-700 transition-colors">
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
