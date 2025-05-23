<!-- templates/profile_tabs/finance.php -->
<div class="space-y-8">
  <div>
    <h2 class="text-xl font-semibold text-gray-900 mb-2">Finanz-Übersicht</h2>
    <p class="text-gray-600">Überblick über Ihre Einnahmen und Ausgaben.</p>
  </div>

  <!-- Summary Cards -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
      <h3 class="text-sm font-medium text-green-800 mb-2">Gesamte Einnahmen</h3>
      <p class="text-2xl font-bold text-green-900">€<?= number_format($totalIncome, 2, ',', '.') ?></p>
    </div>
    
    <div class="bg-red-50 border border-red-200 rounded-lg p-6">
      <h3 class="text-sm font-medium text-red-800 mb-2">Gesamte Ausgaben</h3>
      <p class="text-2xl font-bold text-red-900">€<?= number_format($totalExpense, 2, ',', '.') ?></p>
    </div>
    
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
      <h3 class="text-sm font-medium text-blue-800 mb-2">Saldo</h3>
      <p class="text-2xl font-bold <?= $balance >= 0 ? 'text-green-900' : 'text-red-900' ?>">
        €<?= number_format($balance, 2, ',', '.') ?>
      </p>
    </div>
  </div>

  <!-- Recent Entries -->
  <div class="bg-white border border-gray-200 rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Letzte Einträge</h3>
    <?php if (empty($financeEntries)): ?>
      <p class="text-gray-500 text-center py-8">Noch keine Finanzeinträge vorhanden.</p>
    <?php else: ?>
      <div class="space-y-3">
        <?php foreach (array_slice($financeEntries, 0, 5) as $entry): ?>
          <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex-1">
              <p class="font-medium text-gray-900"><?= htmlspecialchars($entry['note']) ?></p>
              <p class="text-sm text-gray-600"><?= date('d.m.Y', strtotime($entry['entry_date'])) ?></p>
            </div>
            <span class="font-bold <?= $entry['type'] === 'income' ? 'text-green-600' : 'text-red-600' ?>">
              <?= $entry['type'] === 'income' ? '+' : '-' ?>€<?= number_format($entry['amount'], 2, ',', '.') ?>
            </span>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Formular und Fehler -->
  <?php if (!empty($publicSuccess)): ?>
    <div class="p-4 bg-green-100 border border-green-300 text-green-800 rounded shadow">
      <?= htmlspecialchars($publicSuccess) ?>
    </div>
  <?php endif; ?>
  <?php if (!empty($errors)): ?>
    <div class="p-4 bg-red-100 border border-red-300 text-red-800 rounded shadow">
      <ul class="list-disc list-inside">
        <?php foreach($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <!-- Eingabe-Formular -->
  <form method="post" action="?tab=finance" class="bg-card-bg p-6 rounded-xl shadow-card-lg space-y-6">
    <div>
      <label class="block text-sm font-medium text-text-secondary mb-1">Bezeichnung</label>
      <input name="description" type="text"
             class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary"
             value="<?= htmlspecialchars($_POST['description'] ?? '') ?>"/>
    </div>
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-text-secondary mb-1">Typ</label>
        <select name="type"
                class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary">
          <option value="income"  <?= ($_POST['type'] ?? '')==='income'  ?'selected':'' ?>>Einnahme</option>
          <option value="expense" <?= ($_POST['type'] ?? '')==='expense'?'selected':'' ?>>Ausgabe</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-text-secondary mb-1">Betrag (€)</label>
        <input name="amount" type="number" step="0.01"
               class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary"
               value="<?= htmlspecialchars($_POST['amount'] ?? '') ?>"/>
      </div>
    </div>
    <div>
      <label class="block text-sm font-medium text-text-secondary mb-1">Datum</label>
      <input name="entry_date" type="date"
             class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary"
             value="<?= htmlspecialchars($_POST['entry_date'] ?? '') ?>"/>
    </div>
    <button type="submit"
            class="w-full py-3 bg-primary text-white rounded-xl font-semibold shadow hover:bg-primary-dark transition">
      Hinzufügen
    </button>
  </form>

  <!-- Liste der Einträge -->
  <div class="overflow-x-auto bg-card-bg rounded-xl shadow-card-lg">
    <table class="min-w-full divide-y divide-border">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 text-left text-sm font-medium text-text-secondary">Bezeichnung</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-text-secondary">Datum</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-text-secondary">Typ</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-text-secondary">Betrag</th>
          <th class="px-4 py-2"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-border">
        <?php if (empty($financeEntries)): ?>
          <tr><td colspan="5" class="p-4 text-center text-text-secondary">Keine Einträge</td></tr>
        <?php else: foreach($financeEntries as $f): ?>
          <tr>
            <td class="px-4 py-2 text-text"><?= htmlspecialchars($f['note']) ?></td>
            <td class="px-4 py-2 text-text"><?= date('d.m.Y', strtotime($f['entry_date'])) ?></td>
            <td class="px-4 py-2 text-text"><?= ucfirst($f['type']) ?></td>
            <td class="px-4 py-2 text-text"><?= number_format($f['amount'],2,',','.') ?> €</td>
            <td class="px-4 py-2 text-right">
              <a href="?tab=finance&delete_finance=<?= $f['id'] ?>"
                 class="text-red-600 hover:underline">Löschen</a>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
