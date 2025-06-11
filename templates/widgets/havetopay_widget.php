<?php
// Ensure variables are set, providing defaults if not (though they should be set by the controller)
$widgetTotalOwed = $widgetTotalOwed ?? 0.00;
$widgetTotalOwing = $widgetTotalOwing ?? 0.00;
$widgetNetBalance = $widgetNetBalance ?? 0.00;
?>
<article class="bg-gradient-to-br from-purple-900/20 via-gray-900/30 to-red-900/20 backdrop-blur-xl rounded-3xl border border-white/10 hover:border-white/20 transition-all duration-300 overflow-hidden shadow-2xl">
    <!-- Widget Header -->
    <div class="bg-gradient-to-r from-purple-800/30 via-gray-800/40 to-red-800/30 backdrop-blur-sm px-6 py-4 border-b border-white/10">
        <div class="flex justify-between items-center">
            <a href="havetopay.php" class="group inline-flex items-center">
                <h2 class="text-lg font-semibold mr-2 text-white/90 group-hover:text-white transition-colors">HaveToPay</h2>
                <svg class="w-4 h-4 fill-current text-white/60 group-hover:text-white/90 group-hover:translate-x-1 transition-all" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </a>
            <a href="havetopay_add.php" title="Add Expense" class="bg-white/10 hover:bg-white/20 backdrop-blur-sm text-white/90 hover:text-white font-medium px-3 py-1.5 rounded-xl flex items-center text-sm border border-white/10 hover:border-white/20 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Add
            </a>
        </div>
    </div>

    <div class="p-6 space-y-4">
        <!-- Balance Information -->
        <div class="grid grid-cols-1 gap-3">
            <div class="flex justify-between items-center">
                <span class="text-white/70 font-medium">You are owed:</span>
                <span class="text-green-400 font-semibold">
                    €<?php echo htmlspecialchars(number_format($widgetTotalOwed, 2, ',', '.')); ?>
                </span>
            </div>
            
            <div class="flex justify-between items-center">
                <span class="text-white/70 font-medium">You owe:</span>
                <span class="text-red-400 font-semibold">
                    €<?php echo htmlspecialchars(number_format($widgetTotalOwing, 2, ',', '.')); ?>
                </span>
            </div>
            
            <div class="h-px bg-white/10 my-2"></div>
            
            <div class="flex justify-between items-center">
                <span class="text-white/90 font-semibold">Net Balance:</span>
                <span class="<?php echo ($widgetNetBalance >= 0) ? 'text-green-400' : 'text-red-400'; ?> font-bold text-lg">
                    €<?php echo htmlspecialchars(number_format($widgetNetBalance, 2, ',', '.')); ?>
                </span>
            </div>
        </div>
        
        <!-- Visual Balance Indicator -->
        <div class="w-full h-2 bg-white/10 rounded-full overflow-hidden backdrop-blur-sm">
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
                <div class="bg-green-400/80 h-full transition-all duration-300" style="width: <?php echo $percentOwed; ?>%;"></div>
                <div class="bg-white/5 h-full" style="width: <?php echo 100 - $percentOwed - $percentOwing; ?>%;"></div>
                <div class="bg-red-400/80 h-full transition-all duration-300" style="width: <?php echo $percentOwing; ?>%;"></div>
            </div>
        </div>
    </div>
</article>
