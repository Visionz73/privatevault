<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../models/Task.php';

requireLogin();                  // alle Rollen dürfen lesen
requireRole(['admin','member']);  // Gäste ausgeschlossen

// Get current user ID
$userId = $_SESSION['user_id'];

// Filter mode: 'all' or 'user'
$filterMode = $_GET['filter'] ?? 'all';

/* ------------------------------------------------------------------
   Aufgaben nach Status gruppiert mit Filter-Option
-------------------------------------------------------------------*/
$columns = ['todo'=>[], 'doing'=>[], 'done'=>[]];

// Basis Query
$query = "
    SELECT t.id, t.title, t.description, t.created_by, t.assigned_to, t.due_date, t.status,
           uc.username AS creator_name, ua.username AS assignee_name
    FROM tasks t
    LEFT JOIN users uc ON t.created_by = uc.id
    LEFT JOIN users ua ON t.assigned_to = ua.id
";

// Filter anwenden - NUR zugewiesene Aufgaben anzeigen
if ($filterMode === 'user') {
    $query .= " WHERE t.assigned_to = ?";  // Nur zugewiesene Aufgaben 
    $stmt = $pdo->prepare($query);
    $stmt->execute([$userId]);
} else {
    $stmt = $pdo->query($query);
}

// Gruppieren nach Status
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    // Fallback wenn Status nicht existiert
    $status = in_array($row['status'], array_keys($columns)) ? $row['status'] : 'todo';
    $columns[$status][] = $row;
}

/* ------------------------------------------------------------------
   Alle Users fürs Dropdown
-------------------------------------------------------------------*/
$allUsers = $pdo->query(
  'SELECT id, username FROM users ORDER BY username'
)->fetchAll();

/* ------------------------------------------------------------------
   AJAX Request Handling für Drag & Drop
-------------------------------------------------------------------*/
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    
    // Handle API endpoints
    $endpoint = $_SERVER['REQUEST_URI'];
    
    if ($endpoint === '/api/tasks/update-status' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // Process the drag and drop status update
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['taskId']) && isset($data['status'])) {
            $taskId = $data['taskId'];
            $newStatus = $data['status'];
            
            try {
                $taskModel = new Task($pdo);
                $success = $taskModel->updateStatus($taskId, $newStatus);
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => $success,
                    'message' => $success ? 'Task status updated' : 'Failed to update task status'
                ]);
            } catch (Exception $e) {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Server error: ' . $e->getMessage()
                ]);
            }
        } else {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Missing required parameters'
            ]);
        }
        exit;
    } elseif (preg_match('/^\/api\/tasks\/(\d+)$/', $endpoint, $matches) && $_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get a specific task
        $taskId = $matches[1];
        
        try {
            $taskModel = new Task($pdo);
            $task = $taskModel->getTask($taskId);
            
            if ($task) {
                header('Content-Type: application/json');
                echo json_encode($task);
            } else {
                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Task not found'
                ]);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
        exit;
    } elseif ($endpoint === '/api/tasks/create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // Create a new task
        $data = json_decode(file_get_contents('php://input'), true);
        
        try {
            $taskModel = new Task($pdo);
            $taskId = $taskModel->createTask($data);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'taskId' => $taskId,
                'message' => 'Task created successfully'
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
        exit;
    } elseif ($endpoint === '/api/tasks/update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // Update an existing task
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['id'])) {
            try {
                $taskModel = new Task($pdo);
                $success = $taskModel->updateTask($data);
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => $success,
                    'message' => $success ? 'Task updated successfully' : 'Failed to update task'
                ]);
            } catch (Exception $e) {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Server error: ' . $e->getMessage()
                ]);
            }
        } else {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Missing required parameters'
            ]);
        }
        exit;
    }
}

// For the normal page load, get all tasks
try {
    $taskModel = new Task($pdo);
    $todoTasks = $taskModel->getTasksByStatus('todo');
    $inProgressTasks = $taskModel->getTasksByStatus('inprogress');
    $completedTasks = $taskModel->getTasksByStatus('completed');
} catch (Exception $e) {
    // Handle database errors
    echo "Error loading tasks: " . $e->getMessage();
}
?>
