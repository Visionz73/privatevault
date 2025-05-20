<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>HaveToPay | Ausgaben teilen</title>
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
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">HaveToPay</h1>
          <p class="text-gray-600">Verwalte gemeinsame Ausgaben und teile Kosten mit Freunden</p>
        </div>
        
        <a href="havetopay_add.php" class="px-4 py-2 bg-[#4A90E2] text-white rounded-lg text-sm font-medium hover:bg-[#357ABD] transition flex items-center justify-center w-full md:w-auto">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Neue Ausgabe hinzufügen
        </a>
      </div>
      
      <!-- Success Message -->
      <?php if (!empty($successMessage)): ?>
      <div class="mb-4 p-4 bg-green-100 rounded-lg text-green-700" role="alert">
        <?= htmlspecialchars($successMessage) ?>
      </div>
      <?php endif; ?>
      
      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Net Balance -->
        <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-sm p-6">
          <h3 class="text-sm font-medium text-gray-500 mb-2">Gesamtbilanz</h3>
          <p class="text-2xl font-bold <?= $netBalance >= 0 ? 'text-green-600' : 'text-red-600' ?>">
            <?= number_format($netBalance, 2, ',', '.') ?> €
          </p>
          <p class="text-sm text-gray-600 mt-1">
            <?= $netBalance >= 0 ? 'Du bekommst noch Geld' : 'Du schuldest noch Geld' ?>
          </p>
        </div>
        
        <!-- People Owe You -->
        <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-sm p-6">
          <h3 class="text-sm font-medium text-gray-500 mb-2">Du bekommst noch</h3>
          <p class="text-2xl font-bold text-green-600">
            <?= number_format($totalOwed, 2, ',', '.') ?> €
          </p>
          <p class="text-sm text-gray-600 mt-1">
            Von <?= count($balances['others_owe']) ?> Personen
          </p>
        </div>
        
        <!-- You Owe People -->
        <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-sm p-6">
          <h3 class="text-sm font-medium text-gray-500 mb-2">Du schuldest noch</h3>
          <p class="text-2xl font-bold text-red-600">
            <?= number_format($totalOwing, 2, ',', '.') ?> €
          </p>
          <p class="text-sm text-gray-600 mt-1">
            An <?= count($balances['user_owes']) ?> Personen
          </p>
        </div>
      </div>
      
      <!-- Balances Section -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- People who owe you -->
        <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-sm overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Diese Personen schulden dir Geld</h2>
          </div>
          
          <div class="p-6">
            <?php if (empty($balances['others_owe'])): ?>
              <p class="text-gray-500 text-center py-4">Momentan schuldet dir niemand Geld</p>
            <?php else: ?>
              <ul class="divide-y divide-gray-100">
                <?php foreach ($balances['others_owe'] as $balance): ?>
                  <li class="py-3 flex justify-between items-center">
                    <div class="flex items-center">
                      <div class="h-10 w-10 rounded-full bg-[#4A90E2]/10 flex items-center justify-center text-[#4A90E2] font-semibold">
                        <?= strtoupper(substr($balance['username'], 0, 2)) ?>
                      </div>
                      <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">
                          <?= htmlspecialchars($balance['first_name'] . ' ' . $balance['last_name']) ?>
                        </p>
                        <p class="text-xs text-gray-500"><?= htmlspecialchars($balance['username']) ?></p>
                      </div>
                    </div>
                    <span class="text-green-600 font-semibold">
                      +<?= number_format($balance['amount_owed'], 2, ',', '.') ?> €
                    </span>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
        </div>
        
        <!-- People you owe money to -->
        <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-sm overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Diesen Personen schuldest du Geld</h2>
          </div>
          
          <div class="p-6">
            <?php if (empty($balances['user_owes'])): ?>
              <p class="text-gray-500 text-center py-4">Du schuldest niemandem Geld</p>
            <?php else: ?>
              <ul class="divide-y divide-gray-100">
                <?php foreach ($balances['user_owes'] as $balance): ?>
                  <li class="py-3 flex justify-between items-center">
                    <div class="flex items-center">
                      <div class="h-10 w-10 rounded-full bg-[#4A90E2]/10 flex items-center justify-center text-[#4A90E2] font-semibold">
                        <?= strtoupper(substr($balance['username'], 0, 2)) ?>
                      </div>
                      <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">
                          <?= htmlspecialchars($balance['first_name'] . ' ' . $balance['last_name']) ?>
                        </p>
                        <p class="text-xs text-gray-500"><?= htmlspecialchars($balance['username']) ?></p>
                      </div>
                    </div>
                    <span class="text-red-600 font-semibold">
                      -<?= number_format($balance['amount_owed'], 2, ',', '.') ?> €
                    </span>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
        </div>
      </div>
      
      <!-- Recent Expenses -->
      <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
          <h2 class="text-lg font-semibold text-gray-900">Letzte Ausgaben</h2>
        </div>
        
        <div class="p-6">
          <?php if (empty($recentExpenses)): ?>
            <p class="text-gray-500 text-center py-4">Noch keine Ausgaben vorhanden</p>
          <?php else: ?>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead>
                  <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <th class="px-4 py-3">Datum</th>
                    <th class="px-4 py-3">Titel</th>
                    <th class="px-4 py-3">Zahler</th>
                    <th class="px-4 py-3">Teilnehmer</th>
                    <th class="px-4 py-3">Betrag</th>
                    <th class="px-4 py-3"></th>
                  </tr>
                </thead>
                <tbody class="bg-white/40 divide-y divide-gray-100">
                  <?php foreach ($recentExpenses as $expense): ?>
                    <tr>
                      <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                        <?= date('d.m.Y', strtotime($expense['expense_date'])) ?>
                      </td>
                      <td class="px-4 py-3 text-sm">
                        <?= htmlspecialchars($expense['title']) ?>
                      </td>
                      <td class="px-4 py-3 whitespace-nowrap text-sm">
                        <?= htmlspecialchars($expense['payer_name']) ?>
                        <?php if ($expense['payer_id'] == $userId): ?>
                          <span class="text-xs text-gray-500">(Du)</span>
                        <?php endif; ?>
                      </td>
                      <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                        <?= $expense['participant_count'] ?> Personen
                      </td>
                      <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                        <?= number_format($expense['amount'], 2, ',', '.') ?> €
                      </td>
                      <td class="px-4 py-3 whitespace-nowrap text-sm text-right">
                        <a href="havetopay_detail.php?id=<?= $expense['id'] ?>" class="text-[#4A90E2] hover:underline">
                          Details
                        </a>
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
