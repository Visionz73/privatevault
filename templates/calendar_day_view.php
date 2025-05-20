<div class="calendar-day-view">
    <div class="day-header">
        <h3><?= date('l, F j, Y', $timestamp) ?></h3>
    </div>
    
    <div class="day-container">
        <?php 
        // Create time slots for each hour of the day
        for ($hour = 0; $hour < 24; $hour++): 
            $hourFormatted = sprintf('%02d:00', $hour);
            $currentHour = date('Y-m-d H:00:00', strtotime("$date $hour:00:00"));
            $nextHour = date('Y-m-d H:00:00', strtotime("$date $hour:00:00 +1 hour"));
        ?>
            <div class="time-slot">
                <div class="time-label"><?= $hourFormatted ?></div>
                <div class="time-events">
                    <?php 
                    // Filter events for this hour
                    $hourEvents = array_filter($events, function($event) use ($currentHour, $nextHour) {
                        $eventStart = strtotime($event['start_datetime']);
                        $eventEnd = strtotime($event['end_datetime']);
                        $slotStart = strtotime($currentHour);
                        $slotEnd = strtotime($nextHour);
                        
                        // Event starts in this hour or continues through this hour
                        return ($eventStart >= $slotStart && $eventStart < $slotEnd) || 
                               ($eventStart < $slotStart && $eventEnd > $slotStart);
                    });
                    
                    foreach ($hourEvents as $event): 
                        $eventStart = strtotime($event['start_datetime']);
                        $eventStartTime = date('g:i A', $eventStart);
                        $eventEnd = strtotime($event['end_datetime']);
                        $eventEndTime = date('g:i A', $eventEnd);
                        $duration = ($eventEnd - $eventStart) / 60; // minutes
                        
                        // Calculate position within the hour
                        $startMinute = (int)date('i', $eventStart);
                        $topPosition = ($startMinute / 60) * 100;
                        
                        // Calculate height based on duration (cap at this hour)
                        $heightPercentage = min(100 - $topPosition, ($duration / 60) * 100);
                        
                        // Generate a color based on event type or category
                        $bgColor = isset($event['color']) ? $event['color'] : '#' . substr(md5($event['id']), 0, 6);
                    ?>
                        <div class="event" 
                             data-event-id="<?= htmlspecialchars($event['id']) ?>"
                             style="top: <?= $topPosition ?>%; 
                                    height: <?= $heightPercentage ?>%; 
                                    background-color: <?= $bgColor ?>;">
                            <div class="event-time"><?= $eventStartTime ?> - <?= $eventEndTime ?></div>
                            <div class="event-title"><?= htmlspecialchars($event['title']) ?></div>
                            <?php if (!empty($event['location'])): ?>
                                <div class="event-location"><?= htmlspecialchars($event['location']) ?></div>
                            <?php endif; ?>
                            
                            <?php if ($event['assigned_to'] || $event['assigned_group_id']): ?>
                            <div class="event-assignment">
                                <?php if ($event['assigned_to']): ?>
                                    <span class="badge bg-info"><?= htmlspecialchars($event['assignee_name']) ?></span>
                                <?php endif; ?>
                                <?php if ($event['assigned_group_id']): ?>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($event['group_name']) ?></span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endfor; ?>
    </div>
</div>

<style>
.calendar-day-view {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.day-header {
    padding: 10px;
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    margin-bottom: 10px;
}

.day-container {
    display: flex;
    flex-direction: column;
    height: calc(100% - 60px);
    overflow-y: auto;
}

.time-slot {
    display: flex;
    height: 60px;
    border-bottom: 1px solid #eee;
    position: relative;
}

.time-label {
    flex: 0 0 60px;
    padding: 5px;
    text-align: right;
    color: #666;
    font-size: 0.8rem;
    border-right: 1px solid #eee;
}

.time-events {
    flex: 1;
    position: relative;
}

.event {
    position: absolute;
    left: 2px;
    right: 2px;
    padding: 2px 5px;
    border-radius: 3px;
    color: white;
    overflow: hidden;
    font-size: 0.8rem;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(0,0,0,0.2);
    z-index: 10;
}

.event-title {
    font-weight: bold;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.event-time {
    opacity: 0.9;
    font-size: 0.75rem;
}

.event-location {
    font-size: 0.7rem;
    opacity: 0.9;
}

.event-assignment {
    margin-top: 2px;
}

.event-assignment .badge {
    font-size: 0.65rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add click event handler for events
    const eventElements = document.querySelectorAll('.event');
    eventElements.forEach(function(element) {
        element.addEventListener('click', function() {
            const eventId = this.getAttribute('data-event-id');
            window.location.href = 'event.php?id=' + eventId;
        });
    });
});
</script>
