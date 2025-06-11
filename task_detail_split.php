<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo '<div class="alert alert-danger">Nicht berechtigt</div>';
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo '<div class="alert alert-danger">Ungültige Task-ID</div>';
    exit;
}

try {
    $host = 'localhost';
    $dbname = 'privatevault';
    $username = 'root';
    $password = '';

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$task) {
        http_response_code(404);
        echo '<div class="alert alert-danger">Task nicht gefunden</div>';
        exit;
    }

    $priorityText = [
        'low' => 'Niedrig',
        'medium' => 'Mittel',
        'high' => 'Hoch'
    ];

    $priorityClass = [
        'low' => 'success',
        'medium' => 'warning',
        'high' => 'danger'
    ];
?>

<div class="task-detail-content">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h4 class="mb-0"><?php echo htmlspecialchars($task['title']); ?></h4>
                <span class="badge bg-<?php echo $priorityClass[$task['priority']] ?? 'secondary'; ?>">
                    <?php echo $priorityText[$task['priority']] ?? htmlspecialchars($task['priority']); ?>
                </span>
            </div>
            
            <?php if (!empty($task['description'])): ?>
            <div class="mb-3">
                <label class="form-label fw-bold">Beschreibung:</label>
                <p class="text-muted"><?php echo nl2br(htmlspecialchars($task['description'])); ?></p>
            </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Status:</label>
                    <p>
                        <?php if ($task['completed']): ?>
                            <span class="badge bg-success">Erledigt</span>
                        <?php else: ?>
                            <span class="badge bg-warning">Ausstehend</span>
                        <?php endif; ?>
                    </p>
                </div>
                
                <?php if ($task['due_date']): ?>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Fälligkeitsdatum:</label>
                    <p class="text-muted"><?php echo date('d.m.Y', strtotime($task['due_date'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Erstellt:</label>
                    <p class="text-muted"><?php echo date('d.m.Y H:i', strtotime($task['created_at'])); ?></p>
                </div>
                
                <?php if ($task['updated_at']): ?>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Zuletzt aktualisiert:</label>
                    <p class="text-muted"><?php echo date('d.m.Y H:i', strtotime($task['updated_at'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <hr>
    
    <div class="d-flex justify-content-between">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
        <div>
            <button type="button" class="btn btn-<?php echo $task['completed'] ? 'warning' : 'success'; ?> me-2" 
                    onclick="toggleTaskStatus(<?php echo $task['id']; ?>, <?php echo $task['completed'] ? '0' : '1'; ?>)">
                <?php echo $task['completed'] ? 'Als ausstehend markieren' : 'Als erledigt markieren'; ?>
            </button>
            <button type="button" class="btn btn-danger" onclick="deleteTaskFromModal(<?php echo $task['id']; ?>)">
                Löschen
            </button>
        </div>
    </div>
</div>

<?php
} catch (Exception $e) {
    error_log("Task detail error: " . $e->getMessage());
    http_response_code(500);
    echo '<div class="alert alert-danger">Fehler beim Laden der Task-Details. Details: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>
