<?php
// Simplified entry point for inbox
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/controllers/inbox.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if elements exist before adding event listeners
    const taskForm = document.getElementById('taskForm');
    if (taskForm) {
        taskForm.addEventListener('submit', function(e) {
            e.preventDefault();
            addTask();
        });
    }

    const searchInput = document.getElementById('searchTasks');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterTasks();
        });
    }

    const priorityFilter = document.getElementById('priorityFilter');
    if (priorityFilter) {
        priorityFilter.addEventListener('change', function() {
            filterTasks();
        });
    }

    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            filterTasks();
        });
    }

    // Load tasks on page load
    loadTasks();
});

window.openDetailModal = function(taskId) {
    const modalElement = document.getElementById('taskDetailModal');
    const modalBodyElement = document.getElementById('taskDetailModalBody');
    
    if (!modalElement || !modalBodyElement) {
        console.error('Modal elements not found');
        return;
    }

    // Show loading state
    modalBodyElement.innerHTML = '<div class="text-center p-4"><div class="spinner-border" role="status"><span class="visually-hidden">Laden...</span></div></div>';
    
    // Show modal
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
    
    // Load task details
    openTaskDetail(taskId);
};

function openTaskDetail(taskId) {
    const modalBody = document.getElementById('taskDetailModalBody');
    
    if (!modalBody) {
        console.error('Modal body element not found');
        return;
    }

    fetch(`task_detail_split.php?id=${taskId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(html => {
            modalBody.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading task details:', error);
            modalBody.innerHTML = '<div class="alert alert-danger">Fehler beim Laden der Task-Details</div>';
        });
}

function toggleTaskStatus(taskId, completed) {
    const taskData = {
        id: taskId,
        completed: completed
    };

    fetch('api/tasks.php', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        credentials: 'same-origin',
        body: JSON.stringify(taskData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal and reload tasks
            const modal = bootstrap.Modal.getInstance(document.getElementById('taskDetailModal'));
            if (modal) {
                modal.hide();
            }
            loadTasks();
        } else {
            alert('Fehler beim Aktualisieren der Aufgabe');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Fehler beim Aktualisieren der Aufgabe');
    });
}

function deleteTaskFromModal(taskId) {
    if (!confirm('Aufgabe wirklich löschen?')) {
        return;
    }

    fetch('api/tasks.php', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
        },
        credentials: 'same-origin',
        body: JSON.stringify({ id: taskId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal and reload tasks
            const modal = bootstrap.Modal.getInstance(document.getElementById('taskDetailModal'));
            if (modal) {
                modal.hide();
            }
            loadTasks();
        } else {
            alert('Fehler beim Löschen der Aufgabe');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Fehler beim Löschen der Aufgabe');
    });
}
</script>

<!-- Task Detail Modal -->
<div class="modal fade" id="taskDetailModal" tabindex="-1" aria-labelledby="taskDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskDetailModalLabel">Task Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="taskDetailModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>
