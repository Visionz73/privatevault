<?php
// Include the widget controller to get data
require_once __DIR__ . '/../../src/controllers/widgets/havetopay_widget.php';
?>

<article class="widget-card p-6 flex flex-col">
  <div class="flex justify-between items-center mb-4">
    <a href="havetopay.php" class="group inline-flex items-center widget-header">
      <h2 class="mr-1">HaveToPay</h2>
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
      </svg>
    </a>
    
    <!-- Net Balance Indicator -->
    <div class="text-right">
      <div class="text-xs widget-description">Netto-Saldo</div>
      <div class="font-bold <?= $widgetNetBalance >= 0 ? 'text-green-400' : 'text-red-400' ?>">
        €<?= number_format(abs($widgetNetBalance), 2) ?>
        <?= $widgetNetBalance >= 0 ? '↑' : '↓' ?>
      </div>
    </div>
  </div>
  
  <p class="widget-description mb-4">
    Schulden: €<?= number_format($widgetTotalOwing, 2) ?> | 
    Guthaben: €<?= number_format($widgetTotalOwed, 2) ?>
  </p>

  <div class="widget-scroll-container flex-1">
    <div class="widget-scroll-content space-y-2">
      <?php if (!empty($balances['user_owes']) || !empty($balances['others_owe'])): ?>
        
        <!-- What user owes others -->
        <?php if (!empty($balances['user_owes'])): ?>
          <div class="mb-3">
            <h4 class="text-xs font-medium text-red-400 mb-2">Du schuldest:</h4>
            <?php foreach (array_slice($balances['user_owes'], 0, 3) as $debt): ?>
              <div class="widget-list-item flex justify-between items-center">
                <span class="task-title text-sm truncate">
                  <?= htmlspecialchars($debt['display_name']) ?>
                </span>
                <span class="text-red-400 font-medium text-sm">
                  €<?= number_format($debt['amount_owed'], 2) ?>
                </span>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        
        <!-- What others owe user -->
        <?php if (!empty($balances['others_owe'])): ?>
          <div class="mb-3">
            <h4 class="text-xs font-medium text-green-400 mb-2">Dir schulden:</h4>
            <?php foreach (array_slice($balances['others_owe'], 0, 3) as $credit): ?>
              <div class="widget-list-item flex justify-between items-center">
                <span class="task-title text-sm truncate">
                  <?= htmlspecialchars($credit['display_name']) ?>
                </span>
                <span class="text-green-400 font-medium text-sm">
                  €<?= number_format($credit['amount_owed'], 2) ?>
                </span>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        
        <!-- Recent expenses -->
        <?php if (!empty($recentExpenses)): ?>
          <div class="border-t border-white/10 pt-3 mt-3">
            <h4 class="text-xs font-medium text-white/70 mb-2">Letzte Ausgaben:</h4>
            <?php foreach (array_slice($recentExpenses, 0, 2) as $expense): ?>
              <div class="widget-list-item">
                <div class="flex justify-between items-start">
                  <div class="flex-1 min-w-0">
                    <span class="task-title text-sm truncate block">
                      <?= htmlspecialchars($expense['title']) ?>
                    </span>
                    <div class="task-meta text-xs flex gap-2">
                      <span>€<?= number_format($expense['amount'], 2) ?></span>
                      <span><?= date('d.m.', strtotime($expense['expense_date'])) ?></span>
                    </div>
                  </div>
                  <span class="status-badge px-1 py-0.5 rounded-full text-xs whitespace-nowrap ml-2 
                    <?= $expense['settlement_status'] === 'fully_settled' ? 'bg-green-100 text-green-800' : 
                        ($expense['settlement_status'] === 'partially_settled' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
                    <?= $expense['settlement_status'] === 'fully_settled' ? 'Bezahlt' : 
                        ($expense['settlement_status'] === 'partially_settled' ? 'Teilweise' : 'Offen') ?>
                  </span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        
      <?php else: ?>
        <div class="widget-list-item text-center task-meta py-4">
          Keine offenen Schulden oder Guthaben.
        </div>
      <?php endif; ?>
    </div>
  </div>
</article>
