<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Debug-Funktion
function debugLog($message) {
    error_log(date('Y-m-d H:i:s') . " - Tasks API: " . $message);
}

try {
    // Prüfe ob User eingeloggt ist
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in');
    }

    // Database connection
    $host = 'localhost';
    $dbname = 'privatevault';
    $username = 'root';
    $password = '';

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $method = $_SERVER['REQUEST_METHOD'];
    $user_id = $_SESSION['user_id'];

    debugLog("Method: $method, User ID: $user_id");

    switch ($method) {
        case 'GET':
            // Alle Tasks für den User laden
            $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$user_id]);
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            debugLog("Found " . count($tasks) . " tasks for user $user_id");
            
            echo json_encode([
                'success' => true,
                'tasks' => $tasks,
                'count' => count($tasks)
            ]);
            break;

        case 'POST':
            // Neue Task erstellen
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['title']) || empty(trim($input['title']))) {
                throw new Exception('Title is required');
            }

            $title = trim($input['title']);
            $description = isset($input['description']) ? trim($input['description']) : '';
            $priority = isset($input['priority']) ? $input['priority'] : 'medium';
            $due_date = isset($input['due_date']) && !empty($input['due_date']) ? $input['due_date'] : null;

            $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description, priority, due_date, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$user_id, $title, $description, $priority, $due_date]);
            
            $task_id = $pdo->lastInsertId();
            debugLog("Created new task with ID: $task_id");

            echo json_encode([
                'success' => true,
                'message' => 'Task created successfully',
                'task_id' => $task_id
            ]);
            break;

        case 'PUT':
            // Task updaten
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['id'])) {
                throw new Exception('Task ID is required');
            }

            $task_id = $input['id'];
            $updates = [];
            $values = [];

            if (isset($input['title'])) {
                $updates[] = "title = ?";
                $values[] = trim($input['title']);
            }
            if (isset($input['description'])) {
                $updates[] = "description = ?";
                $values[] = trim($input['description']);
            }
            if (isset($input['completed'])) {
                $updates[] = "completed = ?";
                $values[] = $input['completed'] ? 1 : 0;
            }
            if (isset($input['priority'])) {
                $updates[] = "priority = ?";
                $values[] = $input['priority'];
            }
            if (isset($input['due_date'])) {
                $updates[] = "due_date = ?";
                $values[] = !empty($input['due_date']) ? $input['due_date'] : null;
            }

            if (empty($updates)) {
                throw new Exception('No fields to update');
            }

            // Add updated_at timestamp
            $updates[] = "updated_at = NOW()";
            
            $values[] = $user_id;
            $values[] = $task_id;

            $sql = "UPDATE tasks SET " . implode(', ', $updates) . " WHERE user_id = ? AND id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);

            if ($stmt->rowCount() === 0) {
                throw new Exception('Task not found or not updated');
            }

            debugLog("Updated task ID: $task_id");

            echo json_encode([
                'success' => true,
                'message' => 'Task updated successfully'
            ]);
            break;

        case 'DELETE':
            // Task löschen
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['id'])) {
                throw new Exception('Task ID is required');
            }

            $task_id = $input['id'];
            $stmt = $pdo->prepare("DELETE FROM tasks WHERE user_id = ? AND id = ?");
            $stmt->execute([$user_id, $task_id]);

            if ($stmt->rowCount() === 0) {
                throw new Exception('Task not found');
            }

            debugLog("Deleted task ID: $task_id");

            echo json_encode([
                'success' => true,
                'message' => 'Task deleted successfully'
            ]);
            break;

        default:
            throw new Exception('Method not allowed');
    }

} catch (Exception $e) {
    debugLog("Error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
