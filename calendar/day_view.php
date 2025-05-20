<?php
require_once '../includes/header.php';
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Get the date from URL parameters or use today
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$timestamp = strtotime($date);
$formatted_date = date('l, F j, Y', $timestamp);

// Get events for this day
$events = getEventsForDay($date);

// Navigation links
$prev_day = date('Y-m-d', strtotime('-1 day', $timestamp));
$next_day = date('Y-m-d', strtotime('+1 day', $timestamp));
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2>Calendar - Day View</h2>
            <div class="d-flex justify-content-between align-items-center">
                <a href="day_view.php?date=<?php echo $prev_day; ?>" class="btn btn-outline-primary">&laquo; Previous Day</a>
                <h3><?php echo $formatted_date; ?></h3>
                <a href="day_view.php?date=<?php echo $next_day; ?>" class="btn btn-outline-primary">Next Day &raquo;</a>
            </div>
            <div class="mt-2 text-center">
                <a href="month_view.php?date=<?php echo $date; ?>" class="btn btn-sm btn-secondary">Month View</a>
                <a href="week_view.php?date=<?php echo $date; ?>" class="btn btn-sm btn-secondary">Week View</a>
                <a href="add_event.php?date=<?php echo $date; ?>" class="btn btn-sm btn-success">Add Event</a>
            </div>
        </div>
    </div>
    
    <div class="day-calendar">
        <?php 
        // Display time slots for each hour
        for ($hour = 0; $hour < 24; $hour++) {
            $time_slot = sprintf("%02d:00", $hour);
            $hour_events = array_filter($events, function($event) use ($hour) {
                $event_hour = (int)date('G', strtotime($event['start_time']));
                return $event_hour == $hour;
            });
        ?>
        <div class="time-slot">
            <div class="time-label"><?php echo $time_slot; ?></div>
            <div class="events-container">
                <?php foreach ($hour_events as $event) : ?>
                <div class="event" 
                     data-event-id="<?php echo $event['id']; ?>"
                     style="background-color: <?php echo $event['color']; ?>">
                    <div class="event-title"><?php echo htmlspecialchars($event['title']); ?></div>
                    <div class="event-time">
                        <?php 
                        echo date('g:i A', strtotime($event['start_time'])) . ' - ' . 
                             date('g:i A', strtotime($event['end_time'])); 
                        ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="eventModalBody">
                Loading...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="editEventBtn" class="btn btn-primary">Edit</a>
                <button type="button" id="deleteEventBtn" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="../assets/css/calendar_day.css">
<script src="../assets/js/calendar_day.js"></script>

<?php require_once '../includes/footer.php'; ?>
