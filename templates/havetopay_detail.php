<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($expense['title']) ?> | Schuldenverwaltung</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }
        
        /* Scale up main content by 10% */
        main {
            transform: scale(1.1);
            transform-origin: top left;
            width: 90.9%; /* Compensate for scaling (100/1.1) */
        }
        
        @media (max-width: 768px) {
            main {
                transform: scale(1.1);
                width: 90.9%;
            }
        }
        
        /* Glass effect containers */
        .glass-container {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }
        .glass-container:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
        }
        
        .glass-header {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.1) 100%);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .glass-item {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.75rem;
            transition: all 0.3s ease;
        }
        .glass-item:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }
        
        .glass-btn {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            transition: all 0.3s ease;
        }
        .glass-btn:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            color: white;
        }
        
        .glass-modal {
            background: linear-gradient(135deg, #2d1b69 0%, #11101d 100%);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body class="min-h-screen flex">
    <?php include_once __DIR__ . '/navbar.php'; ?>

    <main class="ml-0 mt-16 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Back Link -->
        <div class="mb-8">
            <a href="havetopay.php" class="inline-flex items-center text-blue-400 hover:text-blue-300 font-medium group transition-colors">
                <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>Zurück zur Schuldenverwaltung
            </a>
        </div>
        
        <!-- Success/Error Messages -->
        <?php if (!empty($success)): ?>
        <div class="glass-container p-4 mb-8 flex items-center text-green-300">
            <i class="fas fa-check-circle text-xl mr-3"></i>
            <p><?php echo htmlspecialchars($success); ?></p>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
        <div class="glass-container p-4 mb-8 text-red-300">
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
        <div class="glass-container mb-10 overflow-hidden">
            <div class="glass-header px-8 py-6 text-white">
                <h1 class="text-2xl md:text-3xl font-bold"><?php echo htmlspecialchars($expense['title']); ?></h1>
            </div>
            
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <div class="md:col-span-2 space-y-8">
                        <!-- Payer Information -->
                        <div class="flex items-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-purple-500 text-white rounded-full flex items-center justify-center text-xl font-semibold mr-5">
                                <?php echo strtoupper(substr($expense['payer_name'], 0, 1)); ?>
                            </div>
                            <div>
                                <div class="text-white/60 text-sm mb-1">Bezahlt von</div>
                                <div class="font-semibold text-lg text-white">
                                    <?php echo htmlspecialchars($expense['payer_full_name'] ?: $expense['payer_name']); ?>
                                    <?php if ($expense['payer_id'] == $userId): ?> (Du)<?php endif; ?>
                                </div>
                                <div class="text-sm text-white/60">@<?php echo htmlspecialchars($expense['payer_name']); ?></div>
                            </div>
                        </div>
                        
                        <!-- Expense Metadata -->
                        <div class="flex flex-wrap gap-6 text-sm text-white/70">
                            <div class="glass-item px-4 py-3 flex items-center">
                                <i class="fas fa-calendar-alt text-blue-400 mr-3"></i>
                                <div>
                                    <div class="text-xs text-white/50 mb-0.5">Datum</div>
                                    <div class="font-medium text-white"><?php echo date('d.m.Y', strtotime($expense['expense_date'])); ?></div>
                                </div>
                            </div>
                            
                            <?php if(!empty($expense['expense_category'])): ?>
                            <div class="glass-item px-4 py-3 flex items-center">
                                <i class="fas fa-tag text-blue-400 mr-3"></i>
                                <div>
                                    <div class="text-xs text-white/50 mb-0.5">Kategorie</div>
                                    <div class="font-medium text-white"><?php echo htmlspecialchars($expense['expense_category']); ?></div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="glass-item px-4 py-3 flex items-center">
                                <i class="fas fa-users text-blue-400 mr-3"></i>
                                <div>
                                    <div class="text-xs text-white/50 mb-0.5">Teilnehmer</div>
                                    <div class="font-medium text-white"><?php echo count($participants); ?> Personen</div>
                                </div>
                            </div>
                            
                            <?php if ($expense['payer_id'] == $userId || ($_SESSION['is_admin'] ?? false)): ?>
                                <div class="ml-auto">
                                    <button type="button" 
                                            onclick="confirmDeleteExpense()"
                                            class="bg-red-500/20 border border-red-500/30 text-red-300 font-medium px-3 py-3 rounded-xl transition-colors flex items-center hover:bg-red-500/30"
                                            title="Ausgabe löschen">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($expense['description'])): ?>
                        <div class="glass-item p-6 border-blue-500/30">
                            <h6 class="font-semibold text-white mb-3">Beschreibung</h6>
                            <p class="text-white/80 whitespace-pre-line"><?php echo htmlspecialchars($expense['description']); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Amount Card -->
                    <div>
                        <div class="glass-item p-6 text-center">
                            <div class="text-white/60 mb-2">Gesamtbetrag</div>
                            <div class="text-4xl font-bold text-blue-400 mb-4">€<?php echo number_format($expense['amount'], 2); ?></div>
                            
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
                            
                            <div class="text-xs text-white/50 mb-2">Begleichungsfortschritt</div>
                            <div class="w-full bg-white/10 rounded-full h-2.5 mb-2">
                                <div class="bg-green-500 h-2.5 rounded-full transition-all duration-500" style="width: <?php echo $percentage; ?>%"></div>
                            </div>
                            <div class="text-sm font-medium text-white">
                                <?php echo $settledCount; ?> von <?php echo $totalParticipants; ?> beglichen
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Participants List -->
        <div class="glass-container overflow-hidden">
            <div class="glass-header px-8 py-6 text-white">
                <h3 class="text-xl font-bold flex items-center">
                    <i class="fas fa-users mr-3"></i>Teilnehmer
                </h3>
            </div>
            <div class="p-8">
                <?php if (empty($participants)): ?>
                    <div class="text-center py-16">
                        <i class="fas fa-users text-6xl text-white/20 mb-4"></i>
                        <p class="text-white/60">Keine Teilnehmer gefunden</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php foreach ($participants as $participant): ?>
                            <div class="glass-item p-5 <?php echo $participant['is_settled'] ? 'border-green-500/30' : ''; ?>">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-gradient-to-br <?php echo $participant['is_settled'] ? 'from-green-400 to-green-500' : 'from-blue-400 to-purple-500'; ?> text-white rounded-full flex items-center justify-center font-semibold mr-4">
                                            <?php echo strtoupper(substr($participant['username'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="font-medium text-white">
                                                <?php echo htmlspecialchars($participant['full_name'] ?: $participant['username']); ?>
                                                <?php if ($participant['user_id'] == $userId): ?> (Du)<?php endif; ?>
                                            </div>
                                            <div class="text-sm text-white/60">@<?php echo htmlspecialchars($participant['username']); ?></div>
                                        </div>
                                    </div>
                                    
                                    <span class="font-semibold text-lg text-white">
                                        <?php echo number_format($participant['share_amount'], 2); ?> €
                                    </span>
                                </div>
                                
                                <div class="flex justify-between items-center">
                                    <?php if ($participant['is_settled']): ?>
                                        <span class="bg-green-500/20 text-green-300 px-3 py-1.5 rounded-lg text-sm font-medium inline-flex items-center border border-green-500/30">
                                            <i class="fas fa-check-circle mr-1.5"></i>
                                            Beglichen am <?php echo date('d.m.Y', strtotime($participant['settled_date'])); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="bg-yellow-500/20 text-yellow-300 px-3 py-1.5 rounded-lg text-sm font-medium inline-flex items-center border border-yellow-500/30">
                                            <i class="fas fa-clock mr-1.5"></i>
                                            Ausstehend
                                        </span>
                                    <?php endif; ?>
                                    
                                    <div class="flex gap-2">
                                        <?php if (!$participant['is_settled'] && ($expense['payer_id'] == $userId || $participant['user_id'] == $userId)): ?>
                                            <form method="post" class="inline">
                                                <input type="hidden" name="action" value="settle">
                                                <input type="hidden" name="participant_id" value="<?php echo $participant['id']; ?>">
                                                <button type="submit" class="text-green-400 hover:text-green-300 font-medium text-sm bg-green-500/20 hover:bg-green-500/30 px-3 py-1.5 rounded-lg transition-colors border border-green-500/30"
                                                        title="Als beglichen markieren">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <?php if ($expense['payer_id'] == $userId || $participant['user_id'] == $userId): ?>
                                            <button type="button" 
                                                    onclick="confirmRemoveParticipant(<?php echo $participant['id']; ?>, '<?php echo htmlspecialchars($participant['full_name'] ?: $participant['username'], ENT_QUOTES); ?>')"
                                                    class="text-red-400 hover:text-red-300 font-medium text-sm bg-red-500/20 hover:bg-red-500/30 px-3 py-1.5 rounded-lg transition-colors border border-red-500/30"
                                                    title="Entfernen">
                                                <i class="fas fa-user-minus"></i>
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
        <div class="glass-modal p-8 max-w-md mx-4">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-red-500/20 text-red-400 rounded-full flex items-center justify-center mr-4 border border-red-500/30">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-white">Ausgabe löschen</h3>
            </div>
            <p class="text-white/70 mb-8">
                Bist du sicher, dass du diese gesamte Ausgabe löschen möchtest? Dies entfernt die Ausgabe und alle zugehörigen Teilnehmerdatensätze. Diese Aktion kann nicht rückgängig gemacht werden.
            </p>
            <div class="flex justify-end gap-4">
                <button type="button" onclick="closeDeleteExpenseModal()" class="glass-btn px-5 py-3 rounded-xl font-medium transition-colors">
                    Abbrechen
                </button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete_expense">
                    <button type="submit" class="bg-red-500/20 border border-red-500/30 text-red-300 px-5 py-3 rounded-xl font-medium hover:bg-red-500/30 transition-colors">
                        <i class="fas fa-trash mr-2"></i>Ausgabe löschen
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Remove Participant Confirmation Modal -->
    <div id="removeParticipantModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 backdrop-blur-sm">
        <div class="glass-modal p-8 max-w-md mx-4">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-orange-500/20 text-orange-400 rounded-full flex items-center justify-center mr-4 border border-orange-500/30">
                    <i class="fas fa-user-minus text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-white">Teilnehmer entfernen</h3>
            </div>
            <p class="text-white/70 mb-8">
                Bist du sicher, dass du "<span id="participantName" class="font-medium text-white"></span>" von dieser Ausgabe entfernen möchtest? 
                Die Anteile der verbleibenden Teilnehmer werden automatisch neu berechnet.
            </p>
            <div class="flex justify-end gap-4">
                <button type="button" onclick="closeRemoveParticipantModal()" class="glass-btn px-5 py-3 rounded-xl font-medium transition-colors">
                    Abbrechen
                </button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="remove_participant">
                    <input type="hidden" name="participant_id" id="removeParticipantId" value="">
                    <button type="submit" class="bg-orange-500/20 border border-orange-500/30 text-orange-300 px-5 py-3 rounded-xl font-medium hover:bg-orange-500/30 transition-colors">
                        <i class="fas fa-user-minus mr-2"></i>Entfernen
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
