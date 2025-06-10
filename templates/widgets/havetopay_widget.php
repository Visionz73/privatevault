<?php
// Ensure variables are set, providing defaults if not (though they should be set by the controller)
$widgetTotalOwed = $widgetTotalOwed ?? 0.00;
$widgetTotalOwing = $widgetTotalOwing ?? 0.00;
$widgetNetBalance = $widgetNetBalance ?? 0.00;
?>
<article class="glass-card overflow-hidden transition-all duration-300">
    <!-- Widget Header with Gradient Background -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4 text-white">
        <div class="flex justify-between items-center">
            <a href="havetopay.php" class="group inline-flex items-center">
                <h2 class="text-lg font-semibold mr-1">HaveToPay Overview</h2>
                <svg class="w-4 h-4 fill-current opacity-70 group-hover:opacity-100 group-hover:translate-x-1 transition-all" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </a>
            <a href="havetopay_add.php" title="Add Expense" class="glass-button flex items-center text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Add Expense
            </a>
        </div>
    </div>

    <div class="p-6 space-y-4 bg-white/60 backdrop-filter backdrop-blur-md">
        <!-- Balance Information with Improved Spacing and Visualization -->
        <div class="grid grid-cols-1 gap-3">
            <div class="flex justify-between items-center">
                <span class="text-gray-600 font-medium">You are owed:</span>
                <span class="text-green-600 font-semibold">
                    €<?php echo htmlspecialchars(number_format($widgetTotalOwed, 2, ',', '.')); ?>
                </span>
            </div>
            
            <div class="flex justify-between items-center">
                <span class="text-gray-600 font-medium">You owe:</span>
                <span class="text-red-600 font-semibold">
                    €<?php echo htmlspecialchars(number_format($widgetTotalOwing, 2, ',', '.')); ?>
                </span>
            </div>
            
            <div class="h-px bg-gray-200/50 my-1"></div>
            
            <div class="flex justify-between items-center">
                <span class="text-gray-700 font-semibold">Net Balance:</span>
                <span class="<?php echo ($widgetNetBalance >= 0) ? 'text-green-600' : 'text-red-600'; ?> font-bold text-lg">
                    €<?php echo htmlspecialchars(number_format($widgetNetBalance, 2, ',', '.')); ?>
                </span>
            </div>
        </div>
        
        <!-- Visual Balance Indicator - Updated for Apple UI -->
        <div class="w-full h-3 bg-gray-100/50 rounded-full overflow-hidden backdrop-blur-sm">
            <?php 
            $percentOwed = 0;
            $percentOwing = 0;
            $maxAmount = max(abs($widgetTotalOwed), abs($widgetTotalOwing), 1); // Avoid division by zero
            
            if ($maxAmount > 0) {
                $percentOwed = ($widgetTotalOwed / $maxAmount) * 50;
                $percentOwing = ($widgetTotalOwing / $maxAmount) * 50;
            }
            ?>
            <div class="flex h-full">
                <div class="bg-gradient-to-r from-green-400 to-green-500 h-full" style="width: <?php echo $percentOwed; ?>%;"></div>
                <div class="bg-gray-200/30 h-full" style="width: <?php echo 100 - $percentOwed - $percentOwing; ?>%;"></div>
                <div class="bg-gradient-to-r from-red-500 to-red-400 h-full" style="width: <?php echo $percentOwing; ?>%;"></div>
            </div>
        </div>
    </div>
</article>
