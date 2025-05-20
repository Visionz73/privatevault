<?php
// Calculate first day of the month view
$firstDay = date('Y-m-01', $timestamp);
$firstDayOfWeek = date('w', strtotime($firstDay));
$firstDayOfView = date('Y-m-d', strtotime("-$firstDayOfWeek days", strtotime($firstDay)));

// Calculate last day of the month
$lastDay = date('Y-m-t', $timestamp);
$lastDayOfWeek = date('w', strtotime($lastDay));
$daysToAdd = 6 - $lastDayOfWeek;
$lastDayOfView = date('Y-m-d', strtotime("+$daysToAdd days", strtotime($lastDay)));

// Current month
$currentMonth = date('m', $timestamp);

// Group events by day
$eventsByDay = [];
foreach ($events as $event) {
    $startDate = date('Y-m-d', strtotime($event['start_datetime']));
    $endDate = date('Y-m-d', strtotime($event['end_datetime']));
    
    // For multi-day events, add to all days
    $currentDate = $startDate;
    while (strtotime($currentDate) <= strtotime($endDate)) {
        if (!isset($eventsByDay[$currentDate])) {
            $eventsByDay[$currentDate] = [];
        }
        $eventsByDay[$currentDate][] = $event;
        
        // Move to next day
        $currentDate = date('Y-m-d', strtotime('+1 day', strtotime($currentDate)));
    }
}

// Days of the week
$daysOfWeek = ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'];
?>

<div class="overflow-hidden">
    <!-- Header with days of the week -->
    <div class="grid grid-cols-7 border-b">
        <?php foreach ($daysOfWeek as $day): ?>
            <div class="p-2 text-center text-sm font-medium text-gray-800 border-r last:border-r-0">
                <?= $day ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Calendar grid -->
    <div class="grid grid-cols-7">
        <?php
        $currentDate = $firstDayOfView;
        while (strtotime($currentDate) <= strtotime($lastDayOfView)) {
            $isCurrentMonth = date('m', strtotime($currentDate)) === $currentMonth;
            $isToday = $currentDate === date('Y-m-d');
            $hasEvents = isset($eventsByDay[$currentDate]) && count($eventsByDay[$currentDate]) > 0;
            
            $cellClasses = "border-r border-b last:border-r-0 calendar-day p-1 relative";
            if (!$isCurrentMonth) {
                $cellClasses .= " other-month bg-gray-50";
            }
            if ($isToday) {
                $cellClasses .= " bg-blue-50";
            }
        ?>
            <div class="<?= $cellClasses ?>" data-date="<?= $currentDate ?>">
                <!-- Date display -->
                <div class="flex justify-between items-center mb-1">
                    <span class="text-sm <?= $isToday ? 'font-bold text-blue-600' : '' ?>">
                        <?= date('j', strtotime($currentDate)) ?>
                    </span>
                    
                    <?php if ($isCurrentMonth): ?>
                        <button class="text-xs text-gray-400 hover:text-blue-500 add-event-btn" 
                                onclick="addEventOnDate('<?= $currentDate ?>')">+</button>
                    <?php endif; ?>
                </div>
                
                <!-- Events for this day -->
                <?php if ($hasEvents): ?>
                    <div class="space-y-1 max-h-24 overflow-y-auto">
                        <?php foreach ($eventsByDay[$currentDate] as $event): ?>
                            <div class="calendar-event" 
                                 style="background-color: <?= htmlspecialchars($event['color'] ?? '#4A90E2') ?>15; color: <?= htmlspecialchars($event['color'] ?? '#4A90E2') ?>;"
                                 onclick="showEventDetails('<?= htmlspecialchars(json_encode($event), ENT_QUOTES) ?>')">
                                <?php if ($event['all_day']): ?>
                                    <span class="font-medium"><?= htmlspecialchars($event['title']) ?></span>
                                <?php else: ?>
                                    <span class="text-xs"><?= date('H:i', strtotime($event['start_datetime'])) ?></span>
                                    <span class="font-medium ml-1"><?= htmlspecialchars($event['title']) ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php
            $currentDate = date('Y-m-d', strtotime('+1 day', strtotime($currentDate)));
        }
        ?>
    </div>
</div>

<script>
function addEventOnDate(date) {
    const modal = document.getElementById('eventModal');
    document.getElementById('startDate').value = date;
    document.getElementById('endDate').value = date;
    document.getElementById('eventId').value = '';
    document.getElementById('eventTitle').value = '';
    document.getElementById('eventDescription').value = '';
    document.getElementById('eventLocation').value = '';
    document.getElementById('deleteEventBtn').classList.add('hidden');
    document.getElementById('modalTitle').textContent = 'Neuer Termin';
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
</script>
