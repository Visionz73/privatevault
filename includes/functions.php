<?php
// ...existing code...

/**
 * Get all events for a specific day
 * 
 * @param string $date Date in Y-m-d format
 * @return array Array of events
 */
function getEventsForDay($date) {
    global $conn;
    
    $events = [];
    
    // Format date for SQL query
    $date_formatted = date('Y-m-d', strtotime($date));
    
    $sql = "SELECT * FROM events WHERE DATE(start_time) = ? ORDER BY start_time";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $date_formatted);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
    
    return $events;
}

// ...existing code...