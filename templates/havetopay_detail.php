<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($expense['title']) ?> | HaveToPay</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: 'Inter', sans-serif; }
    @media (max-width: 768px) {
      main { margin-top: 3.5rem; }
    }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#eef7ff] via-[#f7fbff] to-[#f9fdf2] flex flex-col">
  <?php require_once __DIR__.'/navbar.php'; ?>
  
  <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
    <div class="max-w-3xl mx-auto">
      <!-- Back link -->
      <div class="mb-6">
        <a href="havetopay.php" class="text-[#4A90E2] flex items-center hover:underline">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Zurück zur Übersicht
        </a>
      </div>
      
      <?php if (!empty($success)): ?>
        <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
          <?= htmlspecialchars($success) ?>
        </div>
      <?php endif; ?>
      
      <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
          <ul class="list-disc list-inside">
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
      
      <!-- Expense Header -->
      <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($expense['title']) ?></h1>
        
        <div class="flex flex-col md:flex-row md:items-center gap-4 md:justify-between">
          <div class="space-y-2">
            <div class="flex items-center text-gray-600 text-sm">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <?= date('d.m.Y', strtotime($expense['expense_date'])) ?>
            </div>
            
            <div class="flex items-center text-gray-600 text-sm">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              Bezahlt von: 
              <span class="font-medium">
                <?= htmlspecialchars($expense['payer_full_name'] ?: $expense['payer_name']) ?>
                <?php if ($expense['payer_id'] == $userId): ?> (Du)<?php endif; ?>
              </span>
            </div>
          </div>
          
          <div class="flex items-center text-xl md:text-2xl font-bold">
            <?= number_format($expense['amount'], 2, ',', '.') ?> €
          </div>
        </div>
        
        <?php if (!empty($expense['description'])): ?>
          <div class="mt-4 pt-4 border-t border-gray-100">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Beschreibung:</h3>
            <p class="text-gray-600"><?= nl2br(htmlspecialchars($expense['description'])) ?></p>
          </div>
        <?php endif; ?>
      </div>
      
      <!-- Participants List -->
      <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
          <h2 class="text-lg font-semibold text-gray-900">Teilnehmer</h2>
        </div>
        
        <div class="p-6">
          <?php if (empty($participants)): ?>
            <p class="text-gray-500 text-center py-4">Keine Teilnehmer gefunden</p>
          <?php else: ?>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead>
                  <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <th class="px-4 py-3">Teilnehmer</th>
                    <th class="px-4 py-3">Anteil</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3"></th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                  <?php foreach ($participants as $participant): ?>
                    <tr class="<?= $participant['is_settled'] ? 'bg-green-50' : '' ?>">
                      <td class="px-4 py-3">
                        <div class="flex items-center">
                          <div class="h-8 w-8 rounded-full bg-[#4A90E2]/10 flex items-center justify-center text-[#4A90E2] text-xs font-semibold mr-2">
                            <?= strtoupper(substr($participant['username'], 0, 2)) ?>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-900">
                              <?= htmlspecialchars($participant['full_name'] ?: $participant['username']) ?>
                              <?php if ($participant['user_id'] == $userId): ?> (Du)<?php endif; ?>
                            </p>
                            <p class="text-xs text-gray-500"><?= htmlspecialchars($participant['username']) ?></p>
                          </div>
                        </div>
                      </td>
                      <td class="px-4 py-3 text-sm font-medium">
                        <?= number_format($participant['share_amount'], 2, ',', '.') ?> €
                      </td>
                      <td class="px-4 py-3 whitespace-nowrap text-sm">
                        <?php if ($participant['is_settled']): ?>
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Beglichen am <?= date('d.m.Y', strtotime($participant['settled_at'])) ?>
                          </span>
                        <?php else: ?>
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Ausstehend
                          </span>
                        <?php endif; ?>
                      </td>
                      <td class="px-4 py-3 whitespace-nowrap text-sm text-right">
                        <?php if (!$participant['is_settled'] && ($expense['payer_id'] == $userId || $participant['user_id'] == $userId)): ?>
                          <form method="post" class="inline">
                            <input type="hidden" name="action" value="settle">
                            <input type="hidden" name="participant_id" value="<?= $participant['id'] ?>">
                            <button type="submit" class="text-[#4A90E2] hover:underline">
                              Als beglichen markieren
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
  </main>
</body>
</html>
