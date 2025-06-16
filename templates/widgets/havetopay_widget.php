<?php
// Ensure variables are set, providing defaults if not (though they should be set by the controller)
$widgetTotalOwed = $widgetTotalOwed ?? 0.00;
$widgetTotalOwing = $widgetTotalOwing ?? 0.00;
$widgetNetBalance = $widgetNetBalance ?? 0.00;
?>
<!-- HaveToPay Widget -->
<article class="widget-card p-6 flex flex-col" style="background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 1.5rem; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
  <div class="flex justify-between items-center mb-4">
    <a href="havetopay.php" class="group inline-flex items-center widget-header">
      <h2 class="mr-1 text-white/90">Finanzen</h2>
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
  <div class="grid grid-cols-2 gap-3 mb-4">
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
          <div class="widget-list-item p-3 rounded-lg cursor-pointer transition-all duration-300" 
               style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1);"
               onclick="window.location.href='havetopay_detail.php?id=<?= $expense['id'] ?>'">
            <div class="flex justify-between items-start">
              <div class="flex-1 min-w-0">
                <div class="task-title text-sm truncate text-white/90">
                  <?= htmlspecialchars($expense['title']) ?>
                </div>
                <div class="task-description text-xs truncate text-white/60">
                  €<?= number_format($expense['amount'], 2) ?> von 
                  <?= htmlspecialchars($expense['payer_name']) ?>
                </div>
              </div>
              <div class="flex-shrink-0 text-right">
                <div class="text-xs font-medium text-blue-400">
                  <?= date('d.m.', strtotime($expense['expense_date'])) ?>
                </div>
                <span class="status-badge bg-yellow-500/20 text-yellow-300 border border-yellow-400/30 px-1 py-0.5 rounded-full text-xs backdrop-blur-sm">
                  <?= ucfirst($expense['settlement_status']) ?>
                </span>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="widget-list-item text-center task-meta py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V4m0 12v4"/>
          </svg>
          <p class="text-white/60">Keine ausstehenden Ausgaben.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</article>
