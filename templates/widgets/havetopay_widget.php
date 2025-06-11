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
      <div class="text-xs text-white/60 mb-1">Saldo</div>
      <div class="text-sm font-bold <?= $widgetNetBalance >= 0 ? 'text-green-400' : 'text-red-400' ?>">
        <?= $widgetNetBalance >= 0 ? '+' : '' ?><?= number_format($widgetNetBalance, 2) ?> €
      </div>
    </div>
  </div>
  
  <p class="widget-description mb-4">
    <?= number_format($widgetTotalOwed, 2) ?> € erhalten • <?= number_format($widgetTotalOwing, 2) ?> € schulden
  </p>

  <!-- Scrollable Balance Summary -->
  <div class="widget-scroll-container flex-1 mb-3">
    <div class="widget-scroll-content space-y-2">
      <!-- People who owe you -->
      <?php if (!empty($balances['others_owe'])): ?>
        <?php foreach(array_slice($balances['others_owe'], 0, 6) as $balance): ?>
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
        <?php foreach(array_slice($balances['user_owes'], 0, 6) as $balance): ?>
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

      <!-- Recent Expenses -->
      <?php if (!empty($recentExpenses)): ?>
        <div class="border-t border-white/10 pt-2 mt-2">
          <div class="text-xs text-white/60 mb-2">Letzte Ausgaben</div>
          <?php foreach(array_slice($recentExpenses, 0, 4) as $expense): ?>
            <div class="widget-list-item py-2" onclick="window.location.href='havetopay_detail.php?id=<?= $expense['id'] ?>'">
              <div class="flex justify-between items-center">
                <span class="text-white/90 text-sm truncate">
                  <?= htmlspecialchars($expense['title']) ?>
                </span>
                <span class="text-white/70 text-xs">
                  <?= number_format($expense['amount'], 2) ?> €
                </span>
              </div>
              <div class="text-xs text-white/50 mt-1">
                <?= date('d.m.Y', strtotime($expense['expense_date'])) ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Empty state -->
      <?php if (empty($balances['others_owe']) && empty($balances['user_owes']) && empty($recentExpenses)): ?>
        <div class="widget-list-item text-center task-meta py-4">
          <div class="text-white/30 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
            </svg>
          </div>
          <div class="text-white/50 text-sm">Keine Ausgaben vorhanden</div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</article>
