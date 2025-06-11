<?php
// Modern finance section
$financeSubtabs = [
  'finance_overview' => 'Übersicht',
  'income' => 'Einnahmen',
  'expenses' => 'Ausgaben'
];
$currentFinanceSubtab = $_GET['subtab'] ?? 'finance_overview';
?>

<div class="space-y-6">
  <!-- Section Header -->
  <div class="mb-8">
    <h2 class="text-2xl font-bold text-primary mb-2">Finanz-Management</h2>
    <p class="text-secondary">Verwalten Sie Ihre Einnahmen und Ausgaben</p>
  </div>
  
  <!-- Quick Stats -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="glass-card p-6 text-center">
      <div class="text-green-400 text-2xl font-bold mb-2">
        €<?= number_format($totalIncome ?? 0, 2, ',', '.') ?>
      </div>
      <div class="text-sm text-muted">Gesamte Einnahmen</div>
    </div>
    <div class="glass-card p-6 text-center">
      <div class="text-red-400 text-2xl font-bold mb-2">
        €<?= number_format($totalExpense ?? 0, 2, ',', '.') ?>
      </div>
      <div class="text-sm text-muted">Gesamte Ausgaben</div>
    </div>
    <div class="glass-card p-6 text-center">
      <div class="text-primary text-2xl font-bold mb-2">
        €<?= number_format(($totalIncome ?? 0) - ($totalExpense ?? 0), 2, ',', '.') ?>
      </div>
      <div class="text-sm text-muted">Saldo</div>
    </div>
  </div>
  
  <!-- Subtab Navigation -->
  <div class="glass-card p-2 mb-8">
    <div class="flex flex-wrap gap-2">
      <?php foreach ($financeSubtabs as $key => $label): ?>
        <?php 
        $isActive = $currentFinanceSubtab === $key;
        $href = "?tab=finance&subtab=" . $key;
        ?>
        <a href="<?= $href ?>" 
           class="px-4 py-2 rounded-lg transition-all duration-300 text-sm font-medium <?= $isActive ? 'bg-gradient-to-r from-purple-500/40 to-blue-500/40 text-white border border-purple-500/30' : 'text-secondary hover:text-white hover:bg-white/10' ?>">
          <?= htmlspecialchars($label) ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
  
  <!-- Subtab Content -->
  <?php
  $subtabFile = __DIR__ . "/finance/{$currentFinanceSubtab}.php";
  if (file_exists($subtabFile)) {
    include $subtabFile;
  } else {
    echo '<div class="glass-card p-8 text-center">
            <h3 class="text-lg text-primary mb-2">Bereich nicht verfügbar</h3>
            <p class="text-muted">Dieser Bereich ist noch nicht implementiert.</p>
          </div>';
  }
  ?>
</div>
