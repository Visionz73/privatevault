document.addEventListener('DOMContentLoaded', function() {
    // Get all task cards and task lists
    const taskCards = document.querySelectorAll('.task-card');
    const taskLists = document.querySelectorAll('.task-list');
    let draggedTask = null;

    // Initialize the modal
    const taskModal = new bootstrap.Modal(document.getElementById('taskModal'));
    const newTaskBtn = document.getElementById('newTaskBtn');
    const saveTaskBtn = document.getElementById('saveTaskBtn');
    
    // Add event listeners to task cards for drag functionality
    taskCards.forEach(card => {
        // Start dragging
        card.addEventListener('dragstart', function(e) {
            draggedTask = card;
            setTimeout(() => {
                card.classList.add('dragging');
            }, 0);
            
            // Store the task ID and original list
            e.dataTransfer.setData('text/plain', card.dataset.taskId);
            e.dataTransfer.effectAllowed = 'move';
        });
        
        // End dragging
        card.addEventListener('dragend', function() {
            draggedTask.classList.remove('dragging');
            draggedTask = null;
            
            // Remove highlight from all drop zones
            taskLists.forEach(list => {
                list.classList.remove('drag-over');
            });
        });
        
        // Open task card for editing when clicked
        card.addEventListener('click', function() {
            const taskId = card.dataset.taskId;
            // Fetch task data via AJAX and populate the form
            fetch(`/api/tasks/${taskId}`)
                .then(response => response.json())
                .then(task => {
                    document.getElementById('taskId').value = task.id;
                    document.getElementById('taskTitle').value = task.title;
                    document.getElementById('taskDescription').value = task.description;
                    document.getElementById('taskDueDate').value = task.due_date;
                    document.getElementById('taskPriority').value = task.priority;
                    document.getElementById('taskAssignee').value = task.assignee;
                    
                    document.getElementById('taskModalLabel').textContent = 'Edit Task';
                    taskModal.show();
                })
                .catch(error => console.error('Error fetching task details:', error));
        });
    });
    
    // Add event listeners to task lists for drop targets
    taskLists.forEach(list => {
        // Prevent default to allow drop
        list.addEventListener('dragover', function(e) {
            e.preventDefault();
            list.classList.add('drag-over');
        });
        
        // Remove highlight when leaving
        list.addEventListener('dragleave', function() {
            list.classList.remove('drag-over');
        });
        
        // Handle the drop
        list.addEventListener('drop', function(e) {
            e.preventDefault();
            list.classList.remove('drag-over');
            
            // Get the dragged task ID
            const taskId = e.dataTransfer.getData('text/plain');
            if (!taskId) return;
            
            // Get the target status
            const newStatus = list.dataset.status;
            
            // Update the task status via AJAX
            fetch('/api/tasks/update-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    taskId: taskId,
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    // If successful, move the task card to the new list
                    const taskCard = document.querySelector(`.task-card[data-task-id="${taskId}"]`);
                    list.appendChild(taskCard);
                    
                    // Update task counts
                    updateTaskCounts();
                    
                    // Add a small animation to show success
                    taskCard.style.animation = 'pulse 0.5s';
                    setTimeout(() => {
                        taskCard.style.animation = '';
                    }, 500);
                } else {
                    console.error('Error updating task status:', result.message);
                    // Optionally show an error message to the user
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
    
    // New task button functionality
    newTaskBtn.addEventListener('click', function() {
        // Reset the form
        document.getElementById('taskForm').reset();
        document.getElementById('taskId').value = '';
        document.getElementById('taskModalLabel').textContent = 'New Task';
        taskModal.show();
    });
    
    // Save task button functionality
    saveTaskBtn.addEventListener('click', function() {
        const form = document.getElementById('taskForm');
        const taskId = document.getElementById('taskId').value;
        
        // Form validation
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Collect form data
        const formData = {
            id: taskId || null,
            title: document.getElementById('taskTitle').value,
            description: document.getElementById('taskDescription').value,
            due_date: document.getElementById('taskDueDate').value,
            priority: document.getElementById('taskPriority').value,
            assignee: document.getElementById('taskAssignee').value,
            status: taskId ? null : 'todo' // Default status for new tasks
        };
        
        // Determine if this is an update or create operation
        const url = taskId ? '/api/tasks/update' : '/api/tasks/create';
        
        // Send data via AJAX
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                taskModal.hide();
                // Reload the page to show updated tasks
                // In a more sophisticated implementation, you could just update the DOM
                location.reload();
            } else {
                console.error('Error saving task:', result.message);
                // Show an error message
            }
        })
        .catch(error => console.error('Error:', error));
    });
    
    // Function to update task counts in column headers
    function updateTaskCounts() {
        const columns = ['todo', 'inprogress', 'completed'];
        
        columns.forEach(columnId => {
            const column = document.getElementById(columnId);
            const taskList = column.querySelector('.task-list');
            const taskCount = taskList.querySelectorAll('.task-card').length;
            column.querySelector('.task-count').textContent = taskCount;
        });
    }
    
    // Add keyframe animation for the pulse effect
    const style = document.createElement('style');
    style.textContent = `
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    `;
    document.head.appendChild(style);
});
