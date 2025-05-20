<?php
// Calculate first and last day of week
$dayOfWeek = date('w', $timestamp);
$firstDayOfWeek = date('Y-m-d', strtotime("-$dayOfWeek days", $timestamp));
$lastDayOfWeek = date('Y-m-d', strtotime("+6 days", strtotime($firstDayOfWeek)));

// Get all days of the week
$days = [];
$currentDay = $firstDayOfWeek;
while (strtotime($currentDay) <= strtotime($lastDayOfWeek)) {
    $days[] = $currentDay;
    $currentDay = date('Y-m-d', strtotime('+1 day', strtotime($currentDay)));
}

// Group events by day
$eventsByDay = [];
foreach ($events as $event) {
    $startDate = date('Y-m-d', strtotime($event['start_datetime']));
    $endDate = date('Y-m-d', strtotime($event['end_datetime']));
    
    // For multi-day events, add to all days
    $currentDate = $startDate;
    while (strtotime($currentDate) <= strtotime($endDate)) {
        // Only add if it's in the current week
        if (strtotime($currentDate) >= strtotime($firstDayOfWeek) && 
            strtotime($currentDate) <= strtotime($lastDayOfWeek)) {
            
            if (!isset($eventsByDay[$currentDate])) {
                $eventsByDay[$currentDate] = [];
            }
            $eventsByDay[$currentDate][] = $event;
        }
        
        // Move to next day
        $currentDate = date('Y-m-d', strtotime('+1 day', strtotime($currentDate)));
    }
}

// Days of the week
$daysOfWeek = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'];
?>

<div class="overflow-x-auto">
    <div class="grid grid-cols-7 min-w-[900px]">
        <!-- Header row with day names -->
        <?php foreach ($days as $index => $day): ?>
            <?php 
            $isToday = $day === date('Y-m-d');
            $headerClass = $isToday ? 'bg-blue-50' : 'bg-gray-50';
            ?>
            <div class="border-r border-b last:border-r-0 p-3 <?= $headerClass ?>">
                <div class="text-center">
                    <p class="text-sm font-medium"><?= $daysOfWeek[$index] ?></p>
                    <p class="text-lg font-bold <?= $isToday ? 'text-blue-600' : '' ?>">
                        <?= date('d.m.', strtotime($day)) ?>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
        
        <!-- All-day events -->
        <div class="col-span-7 border-b p-2 bg-gray-50">
            <p class="text-sm font-medium mb-2">Ganztägige Termine</p>
            <div class="grid grid-cols-7 gap-2">
                <?php foreach ($days as $day): ?>
                    <div class="min-h-[30px]">
                        <?php if (isset($eventsByDay[$day])): ?>
                            <?php foreach ($eventsByDay[$day] as $event): ?>
                                <?php if ($event['all_day'] == 1): ?>
                                    <div class="calendar-event mb-1" 
                                         style="background-color: <?= htmlspecialchars($event['color'] ?? '#4A90E2') ?>15; color: <?= htmlspecialchars($event['color'] ?? '#4A90E2') ?>;"
                                         onclick="showEventDetails('<?= htmlspecialchars(json_encode($event), ENT_QUOTES) ?>')">
                                        <span class="font-medium"><?= htmlspecialchars($event['title']) ?></span>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <button class="text-xs text-gray-400 hover:text-blue-500 w-full text-center add-event-btn" 
                                onclick="addEventOnDate('<?= $day ?>', true)">+</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Time slots for each day -->
        <?php for ($hour = 8; $hour < 20; $hour++): ?>
            <?php 
            $timeLabel = sprintf("%02d:00", $hour);
            ?>
            <!-- Time label -->
            <div class="col-span-7 border-b bg-gray-50 pl-2 text-sm text-gray-500">
                <?= $timeLabel ?>
            </div>
            
            <!-- Event slots for each day at this hour -->
            <?php foreach ($days as $day): ?>
                <div class="border-r border-b last:border-r-0 p-1 min-h-[80px]" data-date="<?= $day ?>" data-hour="<?= $hour ?>">
                    <?php if (isset($eventsByDay[$day])): ?>
                        <?php foreach ($eventsByDay[$day] as $event): ?>
                            <?php 
                            if ($event['all_day'] == 1) continue;
                            
                            $eventHour = (int)date('H', strtotime($event['start_datetime']));
                            $eventEndHour = (int)date('H', strtotime($event['end_datetime']));
                            
                            // Only show events that start or overlap with this hour
                            if ($eventHour <= $hour && $eventEndHour > $hour) {
                                $startMinute = $eventHour === $hour ? date('i', strtotime($event['start_datetime'])) : 0;
                                $isStart = $eventHour === $hour;
                            ?>
                                <div class="calendar-event" 
                                     style="background-color: <?= htmlspecialchars($event['color'] ?? '#4A90E2') ?>15; color: <?= htmlspecialchars($event['color'] ?? '#4A90E2') ?>;"
                                     onclick="showEventDetails('<?= htmlspecialchars(json_encode($event), ENT_QUOTES) ?>')">
                                    <?php if ($isStart): ?>
                                        <span class="text-xs"><?= date('H:i', strtotime($event['start_datetime'])) ?></span>
                                    <?php endif; ?>
                                    <span class="font-medium ml-1"><?= htmlspecialchars($event['title']) ?></span>
                                </div>
                            <?php 
                            }
                            ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if ($hour > 7 && $hour < 19): ?>
                        <button class="text-xs text-gray-400 hover:text-blue-500 w-full text-center add-event-btn"
                                onclick="addEventOnDate('<?= $day ?>', false, '<?= sprintf('%02d:00', $hour) ?>')">+</button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endfor; ?>
    </div>
</div>

<script>
function addEventOnDate(date, allDay = false, time = null) {
    const modal = document.getElementById('eventModal');
    document.getElementById('startDate').value = date;
    document.getElementById('endDate').value = date;
    
    document.getElementById('allDayEvent').checked = allDay;
    if (allDay) {
        document.getElementById('startTimeContainer').classList.add('hidden');
        document.getElementById('endTimeContainer').classList.add('hidden');
    } else {
        document.getElementById('startTimeContainer').classList.remove('hidden');
        document.getElementById('endTimeContainer').classList.remove('hidden');
        
        if (time) {
            document.getElementById('startTime').value = time;
            
            // Set end time to one hour later
            const [hours, minutes] = time.split(':');
            const endTimeHours = parseInt(hours) + 1;
            document.getElementById('endTime').value = `${endTimeHours.toString().padStart(2, '0')}:${minutes}`;
        }
    }
    
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
