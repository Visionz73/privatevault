<?php
// Include the HaveToPay widget controller to get data
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
    
    <!-- Balance indicator -->
    <div class="text-right">
      <?php if ($widgetNetBalance > 0): ?>
        <span class="widget-button text-sm bg-green-600/30 border-green-400/50">
          +€<?= number_format($widgetNetBalance, 2) ?>
        </span>
      <?php elseif ($widgetNetBalance < 0): ?>
        <span class="widget-button text-sm bg-red-600/30 border-red-400/50">
          -€<?= number_format(abs($widgetNetBalance), 2) ?>
        </span>
      <?php else: ?>
        <span class="widget-button text-sm">€0.00</span>
      <?php endif; ?>
    </div>
  </div>
  
  <p class="widget-description mb-4">
    <?php if ($widgetNetBalance > 0): ?>
      Sie bekommen €<?= number_format($widgetNetBalance, 2) ?>
    <?php elseif ($widgetNetBalance < 0): ?>
      Sie schulden €<?= number_format(abs($widgetNetBalance), 2) ?>
    <?php else: ?>
      Alle Schulden beglichen
    <?php endif; ?>
  </p>

  <div class="widget-scroll-container flex-1">
    <div class="widget-scroll-content space-y-2">
      <?php if (!empty($recentExpenses)): ?>
        <?php foreach($recentExpenses as $expense): ?>
          <div class="widget-list-item" onclick="window.location.href='havetopay_detail.php?id=<?= $expense['id'] ?>'">
            <div class="flex justify-between items-center mb-1">
              <span class="task-title truncate"><?= htmlspecialchars($expense['title']) ?></span>
              <span class="task-meta text-xs">€<?= number_format($expense['amount'], 2) ?></span>
            </div>
            
            <div class="flex justify-between items-center text-xs task-meta">
              <span>
                <?php 
                $payerName = !empty($expense['payer_first_name']) && !empty($expense['payer_last_name']) ? 
                    $expense['payer_first_name'] . ' ' . $expense['payer_last_name'] : 
                    $expense['payer_name'];
                ?>
                Bezahlt von: <?= htmlspecialchars($payerName) ?>
              </span>
              <span>
                <?php if ($expense['settlement_status'] === 'partially_settled'): ?>
                  <span class="status-due px-1 py-0.5 rounded-full">Teilweise</span>
                <?php else: ?>
                  <span class="status-overdue px-1 py-0.5 rounded-full">Offen</span>
                <?php endif; ?>
              </span>
            </div>
            
            <div class="text-xs task-meta mt-1">
              <?= date('d.m.Y', strtotime($expense['expense_date'])) ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="widget-list-item text-center task-meta py-4">Keine Ausgaben gefunden.</div>
      <?php endif; ?>
    </div>
  </div>
</article>
