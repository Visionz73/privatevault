<?php
// public/taskboard.php — Entry point for the Kanban board
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../config.php';           // DB + global settings
require_once __DIR__ . '/../src/controllers/taskboard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Board | PrivateVault</title>
    <!-- Modern CSS Framework -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/taskboard.css">
</head>
<body>
    <div class="container-fluid">
        <header class="taskboard-header">
            <h1><i class="fas fa-clipboard-list me-2"></i>Task Board</h1>
            <div class="actions">
                <button class="btn btn-primary" id="newTaskBtn">
                    <i class="fas fa-plus me-1"></i> New Task
                </button>
                <div class="dropdown ms-2">
                    <button class="btn btn-light dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                        <li><a class="dropdown-item" href="#">All Tasks</a></li>
                        <li><a class="dropdown-item" href="#">My Tasks</a></li>
                        <li><a class="dropdown-item" href="#">High Priority</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="taskboard" id="taskboard">
            <div class="board-column" id="todo">
                <div class="column-header">
                    <h3>To Do</h3>
                    <span class="task-count"><?php echo count($todoTasks ?? []); ?></span>
                </div>
                <div class="task-list" data-status="todo">
                    <?php if(!empty($todoTasks)): foreach($todoTasks as $task): ?>
                    <div class="task-card" draggable="true" data-task-id="<?php echo $task['id']; ?>">
                        <div class="task-priority <?php echo $task['priority']; ?>"></div>
                        <h4 class="task-title"><?php echo htmlspecialchars($task['title']); ?></h4>
                        <p class="task-description"><?php echo htmlspecialchars($task['description']); ?></p>
                        <div class="task-meta">
                            <span class="due-date"><i class="far fa-calendar-alt"></i> <?php echo $task['due_date']; ?></span>
                            <?php if(!empty($task['assignee'])): ?>
                            <span class="assignee"><i class="far fa-user"></i> <?php echo htmlspecialchars($task['assignee']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <div class="board-column" id="inprogress">
                <div class="column-header">
                    <h3>In Progress</h3>
                    <span class="task-count"><?php echo count($inProgressTasks ?? []); ?></span>
                </div>
                <div class="task-list" data-status="inprogress">
                    <?php if(!empty($inProgressTasks)): foreach($inProgressTasks as $task): ?>
                    <div class="task-card" draggable="true" data-task-id="<?php echo $task['id']; ?>">
                        <div class="task-priority <?php echo $task['priority']; ?>"></div>
                        <h4 class="task-title"><?php echo htmlspecialchars($task['title']); ?></h4>
                        <p class="task-description"><?php echo htmlspecialchars($task['description']); ?></p>
                        <div class="task-meta">
                            <span class="due-date"><i class="far fa-calendar-alt"></i> <?php echo $task['due_date']; ?></span>
                            <?php if(!empty($task['assignee'])): ?>
                            <span class="assignee"><i class="far fa-user"></i> <?php echo htmlspecialchars($task['assignee']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <div class="board-column" id="completed">
                <div class="column-header">
                    <h3>Completed</h3>
                    <span class="task-count"><?php echo count($completedTasks ?? []); ?></span>
                </div>
                <div class="task-list" data-status="completed">
                    <?php if(!empty($completedTasks)): foreach($completedTasks as $task): ?>
                    <div class="task-card" draggable="true" data-task-id="<?php echo $task['id']; ?>">
                        <div class="task-priority <?php echo $task['priority']; ?>"></div>
                        <h4 class="task-title"><?php echo htmlspecialchars($task['title']); ?></h4>
                        <p class="task-description"><?php echo htmlspecialchars($task['description']); ?></p>
                        <div class="task-meta">
                            <span class="due-date"><i class="far fa-calendar-alt"></i> <?php echo $task['due_date']; ?></span>
                            <?php if(!empty($task['assignee'])): ?>
                            <span class="assignee"><i class="far fa-user"></i> <?php echo htmlspecialchars($task['assignee']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>

        <!-- Task Modal -->
        <div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="taskModalLabel">New Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="taskForm">
                            <input type="hidden" id="taskId" name="taskId">
                            <div class="mb-3">
                                <label for="taskTitle" class="form-label">Title</label>
                                <input type="text" class="form-control" id="taskTitle" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="taskDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="taskDescription" name="description" rows="3"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="taskDueDate" class="form-label">Due Date</label>
                                    <input type="date" class="form-control" id="taskDueDate" name="dueDate">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="taskPriority" class="form-label">Priority</label>
                                    <select class="form-select" id="taskPriority" name="priority">
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="taskAssignee" class="form-label">Assignee</label>
                                <input type="text" class="form-control" id="taskAssignee" name="assignee">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="saveTaskBtn">Save Task</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="assets/js/taskboard.js"></script>
</body>
</html>
