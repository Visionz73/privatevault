class TaskManager {
    constructor() {
        this.tasks = [];
        this.init();
    }

    init() {
        console.log('TaskManager initialized');
        this.loadTasks();
        this.bindEvents();
    }

    bindEvents() {
        // Add task form
        const addForm = document.getElementById('addTaskForm');
        if (addForm) {
            addForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.addTask();
            });
        }

        // Filter buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.filterTasks(e.target.dataset.filter);
            });
        });
    }

    async loadTasks() {
        console.log('Loading tasks...');
        try {
            const response = await fetch('/privatevault/api/tasks.php', {
                method: 'GET',
                credentials: 'same-origin'
            });
            
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Tasks loaded:', data);
            
            if (data.success) {
                this.tasks = data.tasks || [];
                this.renderTasks();
                this.updateStats();
            } else {
                throw new Error(data.error || 'Failed to load tasks');
            }
        } catch (error) {
            console.error('Error loading tasks:', error);
            this.showError('Fehler beim Laden der Aufgaben: ' + error.message);
        }
    }

    async addTask() {
        const form = document.getElementById('addTaskForm');
        const formData = new FormData(form);
        
        const taskData = {
            title: formData.get('title'),
            description: formData.get('description'),
            priority: formData.get('priority'),
            due_date: formData.get('due_date')
        };

        console.log('Adding task:', taskData);

        try {
            const response = await fetch('/privatevault/api/tasks.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify(taskData)
            });

            const data = await response.json();
            console.log('Add task response:', data);

            if (data.success) {
                form.reset();
                this.loadTasks(); // Reload tasks
                this.showSuccess('Aufgabe erfolgreich hinzugefügt');
            } else {
                throw new Error(data.error || 'Failed to add task');
            }
        } catch (error) {
            console.error('Error adding task:', error);
            this.showError('Fehler beim Hinzufügen der Aufgabe: ' + error.message);
        }
    }

    async updateTask(taskId, updates) {
        console.log('Updating task:', taskId, updates);
        
        try {
            const response = await fetch('/privatevault/api/tasks.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify({ id: taskId, ...updates })
            });

            const data = await response.json();
            
            if (data.success) {
                this.loadTasks(); // Reload tasks
            } else {
                throw new Error(data.error || 'Failed to update task');
            }
        } catch (error) {
            console.error('Error updating task:', error);
            this.showError('Fehler beim Aktualisieren der Aufgabe: ' + error.message);
        }
    }

    async deleteTask(taskId) {
        if (!confirm('Aufgabe wirklich löschen?')) return;

        try {
            const response = await fetch('/privatevault/api/tasks.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify({ id: taskId })
            });

            const data = await response.json();
            
            if (data.success) {
                this.loadTasks(); // Reload tasks
                this.showSuccess('Aufgabe gelöscht');
            } else {
                throw new Error(data.error || 'Failed to delete task');
            }
        } catch (error) {
            console.error('Error deleting task:', error);
            this.showError('Fehler beim Löschen der Aufgabe: ' + error.message);
        }
    }

    renderTasks() {
        const container = document.getElementById('tasksContainer');
        if (!container) {
            console.error('Tasks container not found');
            return;
        }

        console.log('Rendering', this.tasks.length, 'tasks');

        if (this.tasks.length === 0) {
            container.innerHTML = '<div class="text-center text-muted py-4">Keine Aufgaben vorhanden</div>';
            return;
        }

        container.innerHTML = this.tasks.map(task => this.createTaskHTML(task)).join('');
        
        // Bind events to new elements
        this.bindTaskEvents();
    }

    createTaskHTML(task) {
        const priorityClass = {
            'low': 'border-success',
            'medium': 'border-warning', 
            'high': 'border-danger'
        };

        const priorityText = {
            'low': 'Niedrig',
            'medium': 'Mittel',
            'high': 'Hoch'
        };

        return `
            <div class="task-item card mb-3 ${priorityClass[task.priority] || ''}" data-task-id="${task.id}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="card-title ${task.completed == 1 ? 'text-decoration-line-through text-muted' : ''}">${task.title}</h6>
                            ${task.description ? `<p class="card-text text-muted">${task.description}</p>` : ''}
                            <small class="text-muted">Priorität: ${priorityText[task.priority] || task.priority}</small>
                            ${task.due_date ? `<br><small class="text-muted">Fällig: ${new Date(task.due_date).toLocaleDateString('de-DE')}</small>` : ''}
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-sm ${task.completed == 1 ? 'btn-success' : 'btn-outline-success'} toggle-complete" 
                                    data-task-id="${task.id}">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-task" data-task-id="${task.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    bindTaskEvents() {
        // Toggle complete
        document.querySelectorAll('.toggle-complete').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const taskId = e.target.closest('[data-task-id]').dataset.taskId;
                const task = this.tasks.find(t => t.id == taskId);
                if (task) {
                    this.updateTask(taskId, { completed: task.completed == 1 ? 0 : 1 });
                }
            });
        });

        // Delete task
        document.querySelectorAll('.delete-task').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const taskId = e.target.closest('[data-task-id]').dataset.taskId;
                this.deleteTask(taskId);
            });
        });
    }

    filterTasks(filter) {
        // Update active filter button
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`[data-filter="${filter}"]`).classList.add('active');

        // Filter and render tasks
        let filteredTasks = this.tasks;
        
        switch(filter) {
            case 'pending':
                filteredTasks = this.tasks.filter(task => task.completed == 0);
                break;
            case 'completed':
                filteredTasks = this.tasks.filter(task => task.completed == 1);
                break;
            case 'high-priority':
                filteredTasks = this.tasks.filter(task => task.priority === 'high');
                break;
        }

        // Temporarily replace tasks for rendering
        const originalTasks = this.tasks;
        this.tasks = filteredTasks;
        this.renderTasks();
        this.tasks = originalTasks;
    }

    updateStats() {
        const total = this.tasks.length;
        const completed = this.tasks.filter(task => task.completed == 1).length;
        const pending = total - completed;

        // Update stats if elements exist
        const totalEl = document.getElementById('totalTasks');
        const completedEl = document.getElementById('completedTasks');
        const pendingEl = document.getElementById('pendingTasks');

        if (totalEl) totalEl.textContent = total;
        if (completedEl) completedEl.textContent = completed;
        if (pendingEl) pendingEl.textContent = pending;
    }

    showSuccess(message) {
        this.showMessage(message, 'success');
    }

    showError(message) {
        this.showMessage(message, 'danger');
    }

    showMessage(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        const container = document.querySelector('.container-fluid') || document.body;
        container.insertBefore(alertDiv, container.firstChild);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded, initializing TaskManager...');
    window.taskManager = new TaskManager();
});
