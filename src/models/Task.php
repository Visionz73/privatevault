<?php
class Task {
    private $db;
    
    public function __construct($pdo) {
        $this->db = $pdo;
    }
    
    /**
     * Get all tasks by status
     * 
     * @param string $status The status to filter by (todo, inprogress, completed)
     * @return array Array of tasks
     */
    public function getTasksByStatus($status) {
        $query = "SELECT * FROM tasks WHERE status = :status ORDER BY priority DESC, due_date ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['status' => $status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get a specific task by ID
     * 
     * @param int $taskId The task ID
     * @return array|false Task data or false if not found
     */
    public function getTask($taskId) {
        $query = "SELECT * FROM tasks WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $taskId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Update a task's status
     * 
     * @param int $taskId The task ID
     * @param string $status The new status
     * @return bool Success or failure
     */
    public function updateStatus($taskId, $status) {
        $query = "UPDATE tasks SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'id' => $taskId,
            'status' => $status
        ]);
    }
    
    /**
     * Create a new task
     * 
     * @param array $data The task data
     * @return int|false The new task ID or false on failure
     */
    public function createTask($data) {
        $query = "INSERT INTO tasks (title, description, due_date, priority, assignee, status) 
                  VALUES (:title, :description, :due_date, :priority, :assignee, :status)";
        $stmt = $this->db->prepare($query);
        $success = $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'],
            'due_date' => $data['due_date'],
            'priority' => $data['priority'],
            'assignee' => $data['assignee'],
            'status' => $data['status']
        ]);
        
        return $success ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Update an existing task
     * 
     * @param array $data The task data including ID
     * @return bool Success or failure
     */
    public function updateTask($data) {
        $query = "UPDATE tasks 
                  SET title = :title, 
                      description = :description, 
                      due_date = :due_date, 
                      priority = :priority, 
                      assignee = :assignee 
                  WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'id' => $data['id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'due_date' => $data['due_date'],
            'priority' => $data['priority'],
            'assignee' => $data['assignee']
        ]);
    }
}
?>
