<?php
// Ensure variables are set, providing defaults if not (though they should be set by the controller)
$widgetTotalOwed = $widgetTotalOwed ?? 0.00;
$widgetTotalOwing = $widgetTotalOwing ?? 0.00;
$widgetNetBalance = $widgetNetBalance ?? 0.00;
?>
<!-- HaveToPay Widget -->
<article class="widget-card p-6 flex flex-col">
  <div class="flex justify-between items-center mb-4">
    <a href="havetopay.php" class="group inline-flex items-center widget-header">
      <h2 class="mr-1">HaveToPay</h2>
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
      </svg>
    </a>
    
    <!-- Net Balance Display -->
    <div class="text-right">
      <div class="text-xs text-white/60 mb-1">Netto Saldo</div>
      <div class="text-lg font-bold <?= $widgetNetBalance >= 0 ? 'text-green-400' : 'text-red-400' ?>">
        <?= $widgetNetBalance >= 0 ? '+' : '' ?><?= number_format($widgetNetBalance, 2) ?> €
      </div>
    </div>
  </div>
  
  <!-- Balance Summary Cards -->
  <div class="grid grid-cols-2 gap-3 mb-4">
    <div class="bg-green-500/10 border border-green-400/20 rounded-xl p-3 text-center">
      <div class="text-xs text-green-300 mb-1">Du bekommst</div>
      <div class="text-sm font-bold text-green-400">+<?= number_format($widgetTotalOwed, 2) ?> €</div>
    </div>
    <div class="bg-red-500/10 border border-red-400/20 rounded-xl p-3 text-center">
      <div class="text-xs text-red-300 mb-1">Du schuldest</div>
      <div class="text-sm font-bold text-red-400">-<?= number_format($widgetTotalOwing, 2) ?> €</div>
    </div>
  </div>

  <!-- Scrollable Balance Summary -->
  <div class="widget-scroll-container flex-1 mb-3">
    <div class="widget-scroll-content space-y-2">
      <!-- People who owe you -->
      <?php if (!empty($balances['others_owe'])): ?>
        <div class="text-xs text-green-300 font-medium mb-2 flex items-center">
          <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
          Schulden dir (<?= count($balances['others_owe']) ?>)
        </div>
        <?php foreach(array_slice($balances['others_owe'], 0, 4) as $balance): ?>
          <div class="widget-list-item flex justify-between items-center py-2">
            <div class="flex items-center min-w-0">
              <div class="w-6 h-6 bg-green-500/20 text-green-300 rounded-full flex items-center justify-center text-xs font-semibold mr-2 flex-shrink-0">
                <?= strtoupper(substr($balance['username'], 0, 1)) ?>
              </div>
              <span class="text-white/90 text-sm truncate">
                <?= htmlspecialchars($balance['display_name'] ?? $balance['username']) ?>
              </span>
            </div>
            <span class="text-green-400 text-xs font-semibold">
              +<?= number_format($balance['amount_owed'], 2) ?> €
            </span>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <!-- People you owe -->
      <?php if (!empty($balances['user_owes'])): ?>
        <div class="text-xs text-red-300 font-medium mb-2 mt-3 flex items-center">
          <div class="w-2 h-2 bg-red-400 rounded-full mr-2"></div>
          Du schuldest (<?= count($balances['user_owes']) ?>)
        </div>
        <?php foreach(array_slice($balances['user_owes'], 0, 4) as $balance): ?>
          <div class="widget-list-item flex justify-between items-center py-2">
            <div class="flex items-center min-w-0">
              <div class="w-6 h-6 bg-red-500/20 text-red-300 rounded-full flex items-center justify-center text-xs font-semibold mr-2 flex-shrink-0">
                <?= strtoupper(substr($balance['username'], 0, 1)) ?>
              </div>
              <span class="text-white/90 text-sm truncate">
                <?= htmlspecialchars($balance['display_name'] ?? $balance['username']) ?>
              </span>
            </div>
            <span class="text-red-400 text-xs font-semibold">
              -<?= number_format($balance['amount_owed'], 2) ?> €
            </span>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
      
      <?php if (empty($balances['others_owe']) && empty($balances['user_owes'])): ?>
        <div class="widget-list-item text-center task-meta py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 8v4"/>
          </svg>
          Alle Schulden beglichen!
        </div>
      <?php endif; ?>
    </div>
  </div>
</article>
