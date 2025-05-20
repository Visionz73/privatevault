<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Neue Ausgabe | HaveToPay</title>
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
    <div class="max-w-2xl mx-auto">
      <!-- Back link -->
      <div class="mb-6">
        <a href="havetopay.php" class="text-[#4A90E2] flex items-center hover:underline">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Zurück zur Übersicht
        </a>
      </div>
      
      <h1 class="text-2xl font-bold text-gray-900 mb-6">Neue Ausgabe hinzufügen</h1>
      
      <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
          <ul class="list-disc list-inside">
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
      
      <form method="post" class="bg-white rounded-xl shadow-sm p-6 space-y-6">
        <!-- Basic Info Section -->
        <div class="space-y-4">
          <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titel *</label>
            <input type="text" id="title" name="title" required 
                   value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                   placeholder="z.B. Pizza für Film-Abend"
                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
          </div>
          
          <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Beschreibung</label>
            <textarea id="description" name="description" rows="3" 
                      placeholder="Optionale Details zur Ausgabe"
                      class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Gesamtbetrag *</label>
              <div class="relative">
                <input type="number" id="amount" name="amount" step="0.01" required min="0.01"
                       value="<?= htmlspecialchars($_POST['amount'] ?? '') ?>"
                       placeholder="0.00"
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-500">
                  €
                </div>
              </div>
            </div>
            
            <div>
              <label for="expense_date" class="block text-sm font-medium text-gray-700 mb-1">Datum *</label>
              <input type="date" id="expense_date" name="expense_date" required 
                     value="<?= htmlspecialchars($_POST['expense_date'] ?? date('Y-m-d')) ?>"
                     class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
            </div>
          </div>
        </div>
        
        <!-- Participants Section -->
        <div class="pt-4 border-t border-gray-100">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Teilnehmer</h3>
          <p class="text-sm text-gray-600 mb-4">
            Wähle die Personen aus, die an dieser Ausgabe beteiligt sind. Du hast die Ausgabe bezahlt und teilst sie mit den ausgewählten Personen.
          </p>
          
          <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2">
              <?php foreach ($allUsers as $user): ?>
                <label class="flex items-center space-x-3 py-2 px-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                  <input type="checkbox" name="participants[]" value="<?= $user['id'] ?>"
                         class="h-4 w-4 text-[#4A90E2] border-gray-300 focus:ring-[#4A90E2]"
                         <?= isset($_POST['participants']) && in_array($user['id'], $_POST['participants']) ? 'checked' : '' ?>>
                  <div class="flex items-center">
                    <div class="h-8 w-8 rounded-full bg-[#4A90E2]/10 flex items-center justify-center text-[#4A90E2] text-xs font-semibold mr-2">
                      <?= strtoupper(substr($user['username'], 0, 2)) ?>
                    </div>
                    <div>
                      <p class="text-sm font-medium"><?= htmlspecialchars($user['username']) ?></p>
                      <?php if (!empty($user['full_name'])): ?>
                        <p class="text-xs text-gray-500"><?= htmlspecialchars($user['full_name']) ?></p>
                      <?php endif; ?>
                    </div>
                  </div>
                </label>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
        
        <!-- Split Method Section -->
        <div class="pt-4 border-t border-gray-100">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Kostenaufteilung</h3>
          
          <div class="space-y-4">
            <div class="flex space-x-6">
              <label class="flex items-center">
                <input type="radio" name="split_method" value="equal" 
                       <?= ($_POST['split_method'] ?? 'equal') === 'equal' ? 'checked' : '' ?>
                       class="h-4 w-4 text-[#4A90E2] border-gray-300 focus:ring-[#4A90E2]"
                       onchange="toggleSplitMethod('equal')">
                <span class="ml-2 text-sm">Gleichmäßig teilen</span>
              </label>
              <label class="flex items-center">
                <input type="radio" name="split_method" value="custom"
                       <?= ($_POST['split_method'] ?? '') === 'custom' ? 'checked' : '' ?>
                       class="h-4 w-4 text-[#4A90E2] border-gray-300 focus:ring-[#4A90E2]"
                       onchange="toggleSplitMethod('custom')">
                <span class="ml-2 text-sm">Individuelle Beträge</span>
              </label>
            </div>
            
            <!-- Custom amount inputs (initially hidden) -->
            <div id="custom_amounts_container" class="space-y-3 <?= ($_POST['split_method'] ?? '') === 'custom' ? '' : 'hidden' ?>">
              <p class="text-sm text-gray-600">
                Gib für jeden Teilnehmer den individuellen Betrag ein
              </p>
              
              <div id="custom_amount_fields" class="space-y-2">
                <!-- Will be populated by JavaScript -->
              </div>
              
              <div class="flex justify-between text-sm">
                <span>Gesamt:</span>
                <span id="custom_total" class="font-medium">0.00 €</span>
              </div>
              <div id="amount_mismatch" class="text-red-600 text-sm hidden">
                Die Summe der individuellen Beträge entspricht nicht dem Gesamtbetrag
              </div>
            </div>
          </div>
        </div>
        
        <!-- Submit Button -->
        <div class="pt-4 flex justify-end">
          <button type="submit" class="px-6 py-2 bg-[#4A90E2] text-white rounded-lg hover:bg-[#357ABD] transition">
            Ausgabe hinzufügen
          </button>
        </div>
      </form>
    </div>
  </main>
  
  <script>
    // Split method toggle
    function toggleSplitMethod(method) {
      document.getElementById('custom_amounts_container').classList.toggle('hidden', method !== 'custom');
      if (method === 'custom') {
        updateCustomAmountFields();
      }
    }
    
    // Update custom amount fields based on selected participants
    function updateCustomAmountFields() {
      const container = document.getElementById('custom_amount_fields');
      container.innerHTML = ''; // Clear existing fields
      
      const totalAmount = parseFloat(document.getElementById('amount').value) || 0;
      const participants = Array.from(document.querySelectorAll('input[name="participants[]"]:checked'));
      
      if (participants.length > 0) {
        const equalShare = totalAmount / participants.length;
        
        participants.forEach(participant => {
          const userId = participant.value;
          const label = participant.closest('label').querySelector('p').textContent.trim();
          
          const field = document.createElement('div');
          field.className = 'flex items-center';
          field.innerHTML = `
            <label class="w-36 text-sm">${label}:</label>
            <div class="relative flex-1">
              <input type="number" name="custom_amounts[${userId}]" step="0.01" min="0" 
                     value="${equalShare.toFixed(2)}" 
                     class="w-full px-4 py-2 border border-gray-200 rounded-lg"
                     onchange="updateCustomTotal()">
              <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-500">
                €
              </div>
            </div>
          `;
          container.appendChild(field);
        });
        
        updateCustomTotal();
      } else {
        const message = document.createElement('p');
        message.className = 'text-sm text-gray-500';
        message.textContent = 'Keine Teilnehmer ausgewählt';
        container.appendChild(message);
      }
    }
    
    // Update custom total and validate against the total amount
    function updateCustomTotal() {
      const customInputs = document.querySelectorAll('input[name^="custom_amounts"]');
      let customTotal = 0;
      
      customInputs.forEach(input => {
        customTotal += parseFloat(input.value) || 0;
      });
      
      const totalAmount = parseFloat(document.getElementById('amount').value) || 0;
      document.getElementById('custom_total').textContent = customTotal.toFixed(2) + ' €';
      
      // Check if amounts match
      const mismatchDiv = document.getElementById('amount_mismatch');
      if (Math.abs(customTotal - totalAmount) > 0.01) {
        mismatchDiv.classList.remove('hidden');
      } else {
        mismatchDiv.classList.add('hidden');
      }
    }
    
    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
      // Update custom fields when participant selection changes
      document.querySelectorAll('input[name="participants[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', () => {
          if (document.querySelector('input[name="split_method"]:checked').value === 'custom') {
            updateCustomAmountFields();
          }
        });
      });
      
      // Update custom fields when total amount changes
      document.getElementById('amount').addEventListener('input', () => {
        if (document.querySelector('input[name="split_method"]:checked').value === 'custom') {
          updateCustomAmountFields();
        }
      });
      
      // Initialize fields if needed
      if (document.querySelector('input[name="split_method"]:checked').value === 'custom') {
        updateCustomAmountFields();
      }
    });
  </script>
</body>
</html>
