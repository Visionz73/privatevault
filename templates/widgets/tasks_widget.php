<?php
require_once __DIR__.'/../../src/lib/auth.php';
requireLogin();
require_once __DIR__.'/../../src/lib/db.php';

// Fetch recent tasks for the current user
$stmt = $pdo->prepare("
    SELECT t.*, 
           creator.username as creator_name,
           assignee.username as assignee_name,
           g.name as group_name
    FROM tasks t
    LEFT JOIN users creator ON t.created_by = creator.id
    LEFT JOIN users assignee ON t.assigned_to = assignee.id
    LEFT JOIN user_groups g ON t.assigned_group_id = g.id
    WHERE (t.assigned_to = ? OR t.created_by = ?) 
      AND t.is_done != 1
    ORDER BY 
      CASE WHEN t.due_date IS NOT NULL AND t.due_date < NOW() THEN 0 ELSE 1 END,
      t.due_date ASC, t.created_at DESC
    LIMIT 5
");
$stmt->execute([$user['id'], $user['id']]);
$recentTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total open task count
$stmt = $pdo->prepare("
    SELECT COUNT(*) as total 
    FROM tasks 
    WHERE (assigned_to = ? OR created_by = ?) AND is_done != 1
");
$stmt->execute([$user['id'], $user['id']]);
$openTaskCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
?>

<article class="widget-card p-6 flex flex-col">
  <div class="flex justify-between items-center mb-4">
    <a href="taskboard.php" class="group inline-flex items-center widget-header">
      <h2 class="mr-1">Aufgaben</h2>
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
      </svg>
    </a>
    
    <button onclick="window.location.href='create_task.php'" class="widget-button text-sm flex items-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      Neue Aufgabe
    </button>
  </div>
  
  <p class="widget-description mb-4"><?= $openTaskCount ?> offene Aufgaben</p>

  <div class="widget-scroll-container flex-1">
    <div class="widget-scroll-content space-y-2">
      <?php if (!empty($recentTasks)): ?>
        <?php foreach ($recentTasks as $task): ?>
          <div class="widget-list-item" onclick="window.location.href='task_detail.php?id=<?= $task['id'] ?>'">
            <div class="flex justify-between items-start">
              <div class="flex-1 min-w-0">
                <div class="task-title text-sm truncate">
                  <?= htmlspecialchars($task['title']) ?>
                </div>
                <?php if (!empty($task['description'])): ?>
                  <div class="task-description text-xs truncate">
                    <?= htmlspecialchars(mb_strimwidth($task['description'], 0, 50, "...")) ?>
                  </div>
                <?php endif; ?>
                <div class="task-meta text-xs">
                  <span class="font-medium">Von:</span> <?= htmlspecialchars($task['creator_name'] ?? 'Unbekannt') ?>
                </div>
              </div>
              <div class="flex-shrink-0 text-right">
                <?php if (!empty($task['due_date'])): ?>
                  <?php $isOverdue = strtotime($task['due_date']) < time(); ?>
                  <div class="text-xs font-medium <?= $isOverdue ? 'text-red-400' : 'text-blue-400' ?>">
                    <?= date('d.m.', strtotime($task['due_date'])) ?>
                  </div>
                  <?php if ($isOverdue): ?>
                    <span class="status-overdue px-1 py-0.5 rounded-full text-xs">
                      Überfällig
                    </span>
                  <?php endif; ?>
                <?php endif; ?>
                <?php if ($task['assigned_group_id']): ?>
                  <span class="group-badge px-1 py-0.5 rounded-full text-xs">
                    Gruppe
                  </span>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="widget-list-item text-center task-meta py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          Keine offenen Aufgaben.
          <button onclick="window.location.href='create_task.php'" 
                  class="block mx-auto mt-2 text-blue-400 hover:text-blue-300 text-xs">
            Erste Aufgabe erstellen
          </button>
        </div>
      <?php endif; ?>
    </div>
  </div>
</article>
