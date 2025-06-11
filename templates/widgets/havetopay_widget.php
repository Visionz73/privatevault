<?php
// Ensure variables are set, providing defaults if not (though they should be set by the controller)
$widgetTotalOwed = $widgetTotalOwed ?? 0.00;
$widgetTotalOwing = $widgetTotalOwing ?? 0.00;
$widgetNetBalance = $widgetNetBalance ?? 0.00;
?>
<!-- HaveToPay Widget for Dashboard -->
<article class="widget-card p-6 flex flex-col">
  <!-- Header Section -->
  <div class="flex items-center justify-between mb-4">
    <a href="/havetopay.php" class="inline-flex items-center widget-header">
      <h2 class="mr-1">HaveToPay</h2>
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
      </svg>
    </a>
    <button onclick="window.location.href='/havetopay_add.php'" class="widget-button">
      +
    </button>
  </div>
  
  <!-- Balance Summary Section -->
  <div class="grid grid-cols-1 gap-3 mb-4">
    <div class="glass-item p-3 text-center">
      <div class="text-xs text-white/50 mb-1">Netto-Saldo</div>
      <div class="text-lg font-bold <?= $widgetNetBalance >= 0 ? 'text-green-400' : 'text-red-400' ?>">
        €<?= number_format($widgetNetBalance, 2) ?>
      </div>
    </div>
    
    <div class="grid grid-cols-2 gap-2">
      <div class="glass-item p-2 text-center">
        <div class="text-xs text-white/50 mb-1">Du bekommst</div>
        <div class="text-sm font-semibold text-green-400">€<?= number_format($widgetTotalOwed, 2) ?></div>
      </div>
      <div class="glass-item p-2 text-center">
        <div class="text-xs text-white/50 mb-1">Du schuldest</div>
        <div class="text-sm font-semibold text-red-400">€<?= number_format($widgetTotalOwing, 2) ?></div>
      </div>
    </div>
  </div>
  
  <!-- Quick Status Section -->
  <div class="flex-1 flex items-center justify-center">
    <?php if ($widgetNetBalance > 0): ?>
      <div class="text-center">
        <div class="w-12 h-12 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-2">
          <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 12v2"/>
          </svg>
        </div>
        <p class="text-sm text-green-400 font-medium">Guthaben</p>
      </div>
    <?php elseif ($widgetNetBalance < 0): ?>
      <div class="text-center">
        <div class="w-12 h-12 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-2">
          <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <p class="text-sm text-red-400 font-medium">Schulden</p>
      </div>
    <?php else: ?>
      <div class="text-center">
        <div class="w-12 h-12 bg-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-2">
          <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <p class="text-sm text-blue-400 font-medium">Ausgeglichen</p>
      </div>
    <?php endif; ?>
  </div>
</article>
