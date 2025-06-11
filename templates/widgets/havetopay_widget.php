<?php
// Ensure variables are set, providing defaults if not (though they should be set by the controller)
$widgetTotalOwed = $widgetTotalOwed ?? 0.00;
$widgetTotalOwing = $widgetTotalOwing ?? 0.00;
$widgetNetBalance = $widgetNetBalance ?? 0.00;
?>
<?php if (isset($widgetData['havetopay'])): ?>
  <?php $havetopayData = $widgetData['havetopay']; ?>
  <!-- HaveToPay Widget -->
  <article class="widget-card p-6 flex flex-col">
    <div class="flex justify-between items-center mb-4">
      <a href="havetopay.php" class="group inline-flex items-center widget-header">
        <h2 class="mr-1">HaveToPay</h2>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
      </a>
    </div>
    
    <!-- Balance Overview -->
    <div class="widget-description mb-4 space-y-2">
      <div class="flex justify-between items-center">
        <span class="text-sm">Saldo:</span>
        <span class="font-semibold <?= $havetopayData['net_balance'] >= 0 ? 'text-green-400' : 'text-red-400' ?>">
          €<?= number_format(abs($havetopayData['net_balance']), 2, ',', '.') ?>
          <?= $havetopayData['net_balance'] >= 0 ? ' Guthaben' : ' Schulden' ?>
        </span>
      </div>
      <div class="grid grid-cols-2 gap-2 text-xs">
        <div class="text-green-400">
          Bekommen: €<?= number_format($havetopayData['total_owed'], 2, ',', '.') ?>
        </div>
        <div class="text-red-400">
          Zahlen: €<?= number_format($havetopayData['total_owing'], 2, ',', '.') ?>
        </div>
      </div>
    </div>

    <div class="widget-scroll-container flex-1">
      <div class="widget-scroll-content space-y-2">
        <!-- Recent Expenses -->
        <?php if (!empty($havetopayData['recent_expenses'])): ?>
          <?php foreach($havetopayData['recent_expenses'] as $expense): ?>
            <div class="widget-list-item" onclick="window.location.href='havetopay_detail.php?id=<?= $expense['id'] ?>'">
              <div class="flex justify-between items-center mb-1">
                <span class="task-title text-sm truncate"><?= htmlspecialchars($expense['title']) ?></span>
                <span class="text-xs text-green-400">€<?= number_format($expense['amount'], 2, ',', '.') ?></span>
              </div>
              <div class="text-xs task-meta flex justify-between">
                <span>Von: <?= htmlspecialchars($expense['payer_name']) ?></span>
                <span><?= date('d.m.Y', strtotime($expense['expense_date'])) ?></span>
              </div>
              <?php if ($expense['settlement_status'] === 'partially_settled'): ?>
                <div class="text-xs mt-1">
                  <span class="px-1 py-0.5 bg-yellow-100 text-yellow-800 rounded-full">
                    <?= $expense['settled_count'] ?>/<?= $expense['participant_count'] ?> bezahlt
                  </span>
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="widget-list-item text-center task-meta py-4">Keine offenen Ausgaben.</div>
        <?php endif; ?>
      </div>
    </div>
  </article>
<?php endif; ?>
