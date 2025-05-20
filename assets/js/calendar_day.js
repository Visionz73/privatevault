document.addEventListener('DOMContentLoaded', function() {
    // Handle click events on calendar events
    const eventElements = document.querySelectorAll('.event');
    const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
    
    eventElements.forEach(element => {
        element.addEventListener('click', function() {
            const eventId = this.getAttribute('data-event-id');
            showEventDetails(eventId);
        });
    });
    
    // Handle delete event
    document.getElementById('deleteEventBtn').addEventListener('click', function() {
        const eventId = this.getAttribute('data-event-id');
        if (confirm('Are you sure you want to delete this event?')) {
            deleteEvent(eventId);
        }
    });
    
    function showEventDetails(eventId) {
        // Set the event ID to the delete button
        document.getElementById('deleteEventBtn').setAttribute('data-event-id', eventId);
        
        // Set the edit link
        document.getElementById('editEventBtn').href = `edit_event.php?id=${eventId}`;
        
        // Fetch event details via AJAX
        fetch(`get_event.php?id=${eventId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const event = data.event;
                    let html = `
                        <h4>${event.title}</h4>
                        <p><strong>Date:</strong> ${event.date}</p>
                        <p><strong>Time:</strong> ${event.start_time} - ${event.end_time}</p>
                    `;
                    
                    if (event.location) {
                        html += `<p><strong>Location:</strong> ${event.location}</p>`;
                    }
                    
                    if (event.description) {
                        html += `<p><strong>Description:</strong> ${event.description}</p>`;
                    }
                    
                    document.getElementById('eventModalBody').innerHTML = html;
                    eventModal.show();
                } else {
                    alert('Error loading event details');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading event details');
            });
    }
    
    function deleteEvent(eventId) {
        fetch('delete_event.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${eventId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                eventModal.hide();
                location.reload();
            } else {
                alert('Error deleting event: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting event');
        });
    }
});
