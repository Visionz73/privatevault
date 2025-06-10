<?php
// Ensure variables are set, providing defaults if not (though they should be set by the controller)
$widgetTotalOwed = $widgetTotalOwed ?? 0.00;
$widgetTotalOwing = $widgetTotalOwing ?? 0.00;
$widgetNetBalance = $widgetNetBalance ?? 0.00;
?>
<article class="bg-white rounded-2xl shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
    <!-- Widget Header with Gradient Background -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4 text-white">
        <div class="flex justify-between items-center">
            <a href="havetopay.php" class="group inline-flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V4m0 12v4" />
                </svg>
                <span class="text-xl font-semibold">HaveToPay</span>
            </a>
            <a href="havetopay_add.php" title="Add Expense" class="bg-white/20 hover:bg-white/30 text-white font-medium px-3 py-1.5 rounded-lg flex items-center text-sm backdrop-blur-sm transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Add Expense
            </a>
        </div>
    </div>

    <div class="p-6 space-y-4">
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
            
            <div class="h-px bg-gray-200 my-1"></div>
            
            <div class="flex justify-between items-center">
                <span class="text-gray-700 font-semibold">Net Balance:</span>
                <span class="<?php echo ($widgetNetBalance >= 0) ? 'text-green-600' : 'text-red-600'; ?> font-bold text-lg">
                    €<?php echo htmlspecialchars(number_format($widgetNetBalance, 2, ',', '.')); ?>
                </span>
            </div>
        </div>
        
        <!-- Visual Balance Indicator -->
        <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
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
                <div class="bg-green-500 h-full" style="width: <?php echo $percentOwed; ?>%;"></div>
                <div class="bg-gray-200 h-full" style="width: <?php echo 100 - $percentOwed - $percentOwing; ?>%;"></div>
                <div class="bg-red-500 h-full" style="width: <?php echo $percentOwing; ?>%;"></div>
            </div>
        </div>
    </div>
</article>
