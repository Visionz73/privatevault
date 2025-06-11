<?php
// Ensure variables are set, providing defaults if not (though they should be set by the controller)
$widgetTotalOwed = $widgetTotalOwed ?? 0.00;
$widgetTotalOwing = $widgetTotalOwing ?? 0.00;
$widgetNetBalance = $widgetNetBalance ?? 0.00;
?>
<article class="widget-card p-6 flex flex-col">
    <!-- Widget Header with Gradient Background -->
    <div class="flex justify-between items-center mb-4">
        <a href="havetopay.php" class="group inline-flex items-center widget-header">
            <h2 class="mr-1">HaveToPay Overview</h2>
            <svg class="w-4 h-4 fill-current opacity-70 group-hover:opacity-100 group-hover:translate-x-1 transition-all" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
        </a>
        <a href="havetopay_add.php" title="Add Expense" class="widget-button text-sm flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Add Expense
        </a>
    </div>

    <p class="widget-description mb-4">Financial overview and balances</p>

    <div class="space-y-4 flex-1">
        <!-- Balance Information with Glass Effect -->
        <div class="grid grid-cols-1 gap-3">
            <div class="widget-list-item flex justify-between items-center">
                <span class="text-white font-medium">You are owed:</span>
                <span class="text-green-400 font-semibold">
                    €<?php echo htmlspecialchars(number_format($widgetTotalOwed, 2, ',', '.')); ?>
                </span>
            </div>
            
            <div class="widget-list-item flex justify-between items-center">
                <span class="text-white font-medium">You owe:</span>
                <span class="text-red-400 font-semibold">
                    €<?php echo htmlspecialchars(number_format($widgetTotalOwing, 2, ',', '.')); ?>
                </span>
            </div>
            
            <div class="h-px bg-white/20 my-2"></div>
            
            <div class="widget-list-item flex justify-between items-center">
                <span class="text-white font-semibold">Net Balance:</span>
                <span class="<?php echo ($widgetNetBalance >= 0) ? 'text-green-400' : 'text-red-400'; ?> font-bold text-lg">
                    €<?php echo htmlspecialchars(number_format($widgetNetBalance, 2, ',', '.')); ?>
                </span>
            </div>
        </div>
        
        <!-- Visual Balance Indicator with Glass Effect -->
        <div class="w-full h-3 bg-white/10 rounded-full overflow-hidden backdrop-blur-sm border border-white/20">
            <?php 
            $percentOwed = 0;
            $percentOwing = 0;
            $maxAmount = max(abs($widgetTotalOwed), abs($widgetTotalOwing));
            
            if ($maxAmount > 0) {
                $percentOwed = ($widgetTotalOwed / $maxAmount) * 50;
                $percentOwing = ($widgetTotalOwing / $maxAmount) * 50;
            }
            ?>
            <div class="flex h-full">
                <div class="bg-gradient-to-r from-green-400 to-green-500 h-full transition-all duration-500" style="width: <?php echo $percentOwed; ?>%;"></div>
                <div class="bg-white/5 h-full" style="width: <?php echo 100 - $percentOwed - $percentOwing; ?>%;"></div>
                <div class="bg-gradient-to-r from-red-400 to-red-500 h-full transition-all duration-500" style="width: <?php echo $percentOwing; ?>%;"></div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-2 gap-2 mt-4">
            <div class="widget-list-item text-center p-3">
                <div class="text-xs text-white/60 mb-1">Status</div>
                <div class="text-sm font-medium <?php echo ($widgetNetBalance >= 0) ? 'text-green-400' : 'text-red-400'; ?>">
                    <?php echo ($widgetNetBalance >= 0) ? 'Positive' : 'Negative'; ?>
                </div>
            </div>
            <div class="widget-list-item text-center p-3">
                <div class="text-xs text-white/60 mb-1">Balance</div>
                <div class="text-sm font-medium text-white">
                    €<?php echo number_format(abs($widgetNetBalance), 0); ?>
                </div>
            </div>
        </div>
    </div>
</article>
