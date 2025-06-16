<?php
// Ensure variables are set, providing defaults if not (though they should be set by the controller)
$widgetTotalOwed = $widgetTotalOwed ?? 0.00;
$widgetTotalOwing = $widgetTotalOwing ?? 0.00;
$widgetNetBalance = $widgetNetBalance ?? 0.00;
?>
<!-- HaveToPay Widget -->
<article class="widget-card p-6 flex flex-col" style="background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 1.5rem; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
  <div class="flex justify-between items-center mb-6">
    <a href="havetopay.php" class="group inline-flex items-center">
      <h2 class="mr-1 text-white/90 text-xl font-semibold">Finanzen</h2>
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
  <div class="grid grid-cols-2 gap-3 mb-6">
    <div class="bg-green-500/10 border border-green-400/20 rounded-xl p-3 text-center backdrop-blur-sm">
      <div class="text-xs text-green-300 mb-1">Du bekommst</div>
      <div class="text-sm font-bold text-green-400">+<?= number_format($widgetTotalOwed, 2) ?> €</div>
    </div>
    <div class="bg-red-500/10 border border-red-400/20 rounded-xl p-3 text-center backdrop-blur-sm">
      <div class="text-xs text-red-300 mb-1">Du schuldest</div>
      <div class="text-sm font-bold text-red-400">-<?= number_format($widgetTotalOwing, 2) ?> €</div>
    </div>
  </div>
  
  <!-- Recent Expenses -->
  <div class="widget-scroll-container flex-1">
    <div class="widget-scroll-content space-y-2" style="max-height: 200px; overflow-y: auto;">
      <?php if (!empty($recentExpenses)): ?>
        <?php foreach ($recentExpenses as $expense): ?>
          <div class="widget-list-item p-3 bg-white/5 border border-white/10 rounded-lg transition-all duration-300 hover:bg-white/10 hover:border-white/20 hover:transform hover:translateX-1 cursor-pointer" onclick="window.location.href='havetopay_detail.php?id=<?= $expense['id'] ?>'">
            <div class="flex justify-between items-center">
              <div class="flex-1 min-w-0">
                <div class="text-sm font-medium text-white/90 truncate"><?= htmlspecialchars($expense['title']) ?></div>
                <div class="text-xs text-white/60">€<?= number_format($expense['amount'], 2) ?></div>
              </div>
              <div class="text-xs text-white/50"><?= date('d.m.', strtotime($expense['expense_date'])) ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="text-center py-6">
          <div class="text-white/40 text-sm">Keine Ausgaben</div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</article>
