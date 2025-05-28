<?php
// Ensure variables are set, providing defaults if not (though they should be set by the controller)
$widgetTotalOwed = $widgetTotalOwed ?? 0.00;
$widgetTotalOwing = $widgetTotalOwing ?? 0.00;
$widgetNetBalance = $widgetNetBalance ?? 0.00;
?>
<article class="bg-white rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.06)] p-6 flex flex-col">
    <div class="flex justify-between items-start mb-4">
        <a href="havetopay.php" class="group inline-flex items-center">
            <h2 class="text-lg font-semibold mr-1">HaveToPay Overview</h2>
            <svg class="w-4 h-4 fill-current text-gray-400 group-hover:text-gray-600 transition-colors" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
        </a>
        <a href="havetopay_add.php" title="Add Expense" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-3 py-1.5 rounded-lg shadow-sm flex items-center text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Add Expense
        </a>
    </div>

    <div class="space-y-2 text-sm mb-4">
        <p>
            <span class="font-medium">You are owed:</span>
            <span class="text-green-600 font-semibold">
                €<?php echo htmlspecialchars(number_format($widgetTotalOwed, 2, ',', '.')); ?>
            </span>
        </p>
        <p>
            <span class="font-medium">You owe:</span>
            <span class="text-red-600 font-semibold">
                €<?php echo htmlspecialchars(number_format($widgetTotalOwing, 2, ',', '.')); ?>
            </span>
        </p>
        <hr class="my-1">
        <p class="text-base">
            <span class="font-semibold">Net Balance:</span>
            <span class="<?php echo ($widgetNetBalance >= 0) ? 'text-green-600' : 'text-red-600'; ?> font-bold">
                €<?php echo htmlspecialchars(number_format($widgetNetBalance, 2, ',', '.')); ?>
            </span>
        </p>
    </div>

</article>
