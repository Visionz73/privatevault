<!-- templates/dashboard.php -->
<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Dashboard | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { 
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
      min-height: 100vh;
    }
    /* On mobile, add a top margin to main to push content below the fixed mobile navbar */
    @media (max-width: 768px) {
      main { margin-top: 3.5rem; }
    }

    /* Dark theme widget styling */
    .widget-card {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1.5rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      color: white;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }
    .widget-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
      transition: left 0.5s;
    }
    .widget-card:hover::before {
      left: 100%;
    }
    .widget-card:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(255, 255, 255, 0.25);
      transform: translateY(-4px);
      box-shadow: 0 16px 48px rgba(0, 0, 0, 0.4);
    }

    /* Enhanced scrollable widget containers */
    .widget-scroll-container {
      position: relative;
      overflow: hidden;
      max-height: 280px; /* Approximately 4 items */
    }
    
    .widget-scroll-content {
      overflow-y: auto;
      scrollbar-width: none; /* Firefox */
      -ms-overflow-style: none; /* Internet Explorer/Edge */
      max-height: 280px;
      padding-right: 4px; /* Space for hover scroll indicator */
    }
    
    .widget-scroll-content::-webkit-scrollbar {
      display: none; /* Chrome, Safari, Opera */
    }
    
    /* Hover scroll indicator */
    .widget-scroll-container:hover .widget-scroll-content {
      scrollbar-width: thin;
      scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
    }
    
    .widget-scroll-container:hover .widget-scroll-content::-webkit-scrollbar {
      display: block;
      width: 4px;
    }
    
    .widget-scroll-container:hover .widget-scroll-content::-webkit-scrollbar-track {
      background: transparent;
    }
    
    .widget-scroll-container:hover .widget-scroll-content::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.3);
      border-radius: 2px;
    }
    
    .widget-scroll-container:hover .widget-scroll-content::-webkit-scrollbar-thumb:hover {
      background: rgba(255, 255, 255, 0.5);
    }
    
    /* Gradient fade effect at bottom when scrollable */
    .widget-scroll-container::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 20px;
      background: linear-gradient(transparent, rgba(255, 255, 255, 0.08));
      pointer-events: none;
      opacity: 0;
      transition: opacity 0.3s ease;
    }
    
    .widget-scroll-container.has-scroll::after {
      opacity: 1;
    }

    /* Widget headers */
    .widget-header {
      color: white;
      font-weight: 600;
      font-size: 1.125rem;
    }
    .widget-header a {
      color: white !important;
      text-decoration: none;
      transition: all 0.3s ease;
    }
    .widget-header a:hover {
      color: rgba(255, 255, 255, 0.8) !important;
    }
    .widget-header svg {
      color: rgba(255, 255, 255, 0.7);
    }

    /* Widget content */
    .widget-description {
      color: rgba(255, 255, 255, 0.7);
      font-size: 0.875rem;
    }

    /* List items in widgets */
    .widget-list-item {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 0.75rem;
      padding: 0.75rem;
      margin-bottom: 0.5rem;
      transition: all 0.3s ease;
      cursor: pointer;
    }
    .widget-list-item:hover {
      background: rgba(255, 255, 255, 0.1);
      border-color: rgba(255, 255, 255, 0.2);
      transform: translateX(3px);
    }
    .widget-list-item:nth-child(even) {
      background: rgba(255, 255, 255, 0.03);
    }
    .widget-list-item:nth-child(even):hover {
      background: rgba(255, 255, 255, 0.08);
    }

    /* Task list specific styling */
    .task-title {
      color: white;
      font-weight: 500;
    }
    .task-description {
      color: rgba(255, 255, 255, 0.6);
      font-size: 0.875rem;
    }
    .task-meta {
      color: rgba(255, 255, 255, 0.5);
      font-size: 0.75rem;
    }
    .task-meta .font-medium {
      color: rgba(255, 255, 255, 0.7);
    }

    /* Status badges */
    .status-overdue {
      background: rgba(239, 68, 68, 0.2);
      color: #fca5a5;
      border: 1px solid rgba(239, 68, 68, 0.3);
    }
    .status-due {
      background: rgba(156, 163, 175, 0.2);
      color: rgba(255, 255, 255, 0.6);
      border: 1px solid rgba(156, 163, 175, 0.3);
    }
    .group-badge {
      background: rgba(147, 51, 234, 0.2);
      color: #c4b5fd;
      border: 1px solid rgba(147, 51, 234, 0.3);
    }

    /* Buttons and controls */
    .widget-button {
      background: rgba(255, 255, 255, 0.1);
      color: white;
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 0.5rem;
      padding: 0.5rem 1rem;
      transition: all 0.3s ease;
      cursor: pointer;
    }
    .widget-button:hover {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.3);
    }

    /* Dropdown menus */
    .dropdown-menu {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 0.75rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    .dropdown-item {
      color: rgba(255, 255, 255, 0.9);
      padding: 0.75rem 1rem;
      transition: all 0.3s ease;
    }
    .dropdown-item:hover {
      background: rgba(255, 255, 255, 0.1);
      color: white;
    }
    .dropdown-item.active {
      background: rgba(255, 255, 255, 0.15);
      color: white;
    }

    /* Forms in widgets */
    .widget-form input, .widget-form select, .widget-form textarea {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 0.5rem;
      color: white;
      padding: 0.75rem;
    }
    .widget-form input::placeholder {
      color: rgba(255, 255, 255, 0.5);
    }
    .widget-form input:focus, .widget-form select:focus, .widget-form textarea:focus {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.3);
      outline: none;
    }

    /* Modal dark theme */
    .modal-content {
      background: linear-gradient(135deg, #2d1b69 0%, #11101d 100%);
      color: white;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .modal-header {
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Greeting text */
    .greeting-text {
      color: white;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }

    /* Placeholder widgets */
    .placeholder-widget {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 1.5rem;
      color: rgba(255, 255, 255, 0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 3rem;
      font-size: 0.875rem;
    }

    /* Loading states */
    .widget-loading {
      opacity: 0.6;
      pointer-events: none;
    }
    
    .widget-loading::after {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 20px;
      height: 20px;
      border: 2px solid rgba(255,255,255,0.3);
      border-top: 2px solid white;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      transform: translate(-50%, -50%);
    }
    
    @keyframes spin {
      0% { transform: translate(-50%, -50%) rotate(0deg); }
      100% { transform: translate(-50%, -50%) rotate(360deg); }
    }
    
    /* Success animations */
    @keyframes slideOut {
      0% { transform: translateX(0); opacity: 1; }
      100% { transform: translateX(-100%); opacity: 0; }
    }
    
    .slide-out {
      animation: slideOut 0.3s ease forwards;
    }
  </style>
</head>
<body class="min-h-screen flex flex-col">

  <?php
  // sicherstellen, dass Session, User und $pdo zur Verfügung stehen
  require_once __DIR__.'/../src/lib/auth.php';
  requireLogin();
  require_once __DIR__.'/../src/lib/db.php';

  // Fetch data for Inbox Widget (Tasks)
  $filterType = $_GET['filter'] ?? 'mine';
  $filterGroupId = $_GET['group_id'] ?? null;

  // Get user's groups for filter dropdown
  try {
    $stmt = $pdo->prepare("
      SELECT g.id, g.name 
      FROM groups g 
      JOIN group_memberships gm ON g.id = gm.group_id 
      WHERE gm.user_id = ?
    ");
    $stmt->execute([$user['id']]);
    $userGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (Exception $e) {
    $userGroups = [];
    $filterType = 'mine'; // Force to 'mine' if groups don't exist
  }

  // Fetch tasks based on filter
  try {
    if ($filterType === 'mine') {
      $stmt = $pdo->prepare("
        SELECT t.*, 
               creator.username as creator_name,
               assignee.username as assignee_name,
               g.name as group_name
        FROM tasks t
        LEFT JOIN users creator ON t.created_by = creator.id
        LEFT JOIN users assignee ON t.assigned_to = assignee.id
        LEFT JOIN groups g ON t.assigned_group_id = g.id
        WHERE (t.assigned_to = ? OR t.created_by = ?) 
          AND t.status != 'completed'
        ORDER BY 
          CASE WHEN t.due_date IS NOT NULL AND t.due_date < NOW() THEN 0 ELSE 1 END,
          t.due_date ASC, t.created_at DESC
        LIMIT 10
      ");
      $stmt->execute([$user['id'], $user['id']]);
    } else {
      $stmt = $pdo->prepare("
        SELECT t.*, 
               creator.username as creator_name,
               assignee.username as assignee_name,
               g.name as group_name
        FROM tasks t
        LEFT JOIN users creator ON t.created_by = creator.id
        LEFT JOIN users assignee ON t.assigned_to = assignee.id
        LEFT JOIN groups g ON t.assigned_group_id = g.id
        WHERE t.assigned_group_id = ? AND t.status != 'completed'
        ORDER BY 
          CASE WHEN t.due_date IS NOT NULL AND t.due_date < NOW() THEN 0 ELSE 1 END,
          t.due_date ASC, t.created_at DESC
        LIMIT 10
      ");
      $stmt->execute([$filterGroupId]);
    }
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (Exception $e) {
    $tasks = [];
  }

  // Count open tasks
  try {
    if ($filterType === 'mine') {
      $stmt = $pdo->prepare("
        SELECT COUNT(*) as count FROM tasks 
        WHERE (assigned_to = ? OR created_by = ?) AND status != 'completed'
      ");
      $stmt->execute([$user['id'], $user['id']]);
    } else {
      $stmt = $pdo->prepare("
        SELECT COUNT(*) as count FROM tasks 
        WHERE assigned_group_id = ? AND status != 'completed'
      ");
      $stmt->execute([$filterGroupId]);
    }
    $openTaskCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
  } catch (Exception $e) {
    $openTaskCount = 0;
  }

  // Fetch data for HaveToPay Widget
  try {
    // Calculate total amounts owed to user
    $stmt = $pdo->prepare("
      SELECT COALESCE(SUM(amount), 0) as total
      FROM debts 
      WHERE creditor_id = ? AND status = 'active'
    ");
    $stmt->execute([$user['id']]);
    $widgetTotalOwed = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0.00;

    // Calculate total amounts user owes
    $stmt = $pdo->prepare("
      SELECT COALESCE(SUM(amount), 0) as total
      FROM debts 
      WHERE debtor_id = ? AND status = 'active'
    ");
    $stmt->execute([$user['id']]);
    $widgetTotalOwing = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0.00;

    // Calculate net balance
    $widgetNetBalance = $widgetTotalOwed - $widgetTotalOwing;

    // Get detailed balances for the widget
    $balances = ['others_owe' => [], 'user_owes' => []];

    // People who owe the user
    $stmt = $pdo->prepare("
      SELECT u.username, u.first_name, u.last_name,
             COALESCE(SUM(d.amount), 0) as amount_owed,
             CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as display_name
      FROM debts d
      JOIN users u ON d.debtor_id = u.id
      WHERE d.creditor_id = ? AND d.status = 'active'
      GROUP BY u.id, u.username, u.first_name, u.last_name
      HAVING amount_owed > 0
      ORDER BY amount_owed DESC
    ");
    $stmt->execute([$user['id']]);
    $balances['others_owe'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // People the user owes
    $stmt = $pdo->prepare("
      SELECT u.username, u.first_name, u.last_name,
             COALESCE(SUM(d.amount), 0) as amount_owed,
             CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as display_name
      FROM debts d
      JOIN users u ON d.creditor_id = u.id
      WHERE d.debtor_id = ? AND d.status = 'active'
      GROUP BY u.id, u.username, u.first_name, u.last_name
      HAVING amount_owed > 0
      ORDER BY amount_owed DESC
    ");
    $stmt->execute([$user['id']]);
    $balances['user_owes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

  } catch (Exception $e) {
    // Fallback values if HaveToPay tables don't exist
    $widgetTotalOwed = 0.00;
    $widgetTotalOwing = 0.00;
    $widgetNetBalance = 0.00;
    $balances = ['others_owe' => [], 'user_owes' => []];
  }

  // Fetch upcoming events for Calendar Widget
  try {
    $stmt = $pdo->prepare("
      SELECT title, date, time 
      FROM events 
      WHERE user_id = ? AND date >= CURDATE() 
      ORDER BY date ASC, time ASC 
      LIMIT 5
    ");
    $stmt->execute([$user['id']]);
    $upcomingEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (Exception $e) {
    $upcomingEvents = [];
  }

  require_once __DIR__.'/navbar.php'; ?>

  <!-- Use responsive margin: on small screens, remove left margin so content fills the screen -->
  <!-- Adjust main margin: on mobile use top margin to push content below the fixed top navbar; on desktop use left margin -->
  <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-8 space-y-10">

    <!-- Enhanced Greeting -->
    <?php
    if (class_exists('IntlDateFormatter')) {
        $formatter = new IntlDateFormatter(
            'de_DE',
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE
        );
        $formattedDate = $formatter->format(new DateTime());
    } else {
        $formattedDate = date('l, d. F');
    }
    
    // Get productivity stats
    $todayTasks = 0;
    $completedToday = 0;
    try {
      $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM tasks WHERE (assigned_to = ? OR created_by = ?) AND DATE(created_at) = CURDATE()");
      $stmt->execute([$user['id'], $user['id']]);
      $todayTasks = $stmt->fetch()['total'] ?? 0;
      
      $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM tasks WHERE (assigned_to = ? OR created_by = ?) AND status = 'completed' AND DATE(updated_at) = CURDATE()");
      $stmt->execute([$user['id'], $user['id']]);
      $completedToday = $stmt->fetch()['total'] ?? 0;
    } catch (Exception $e) {}
    ?>
    
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold greeting-text leading-tight">
          <?= $formattedDate ?><br>
          Guten <?= date('H')<12?'Morgen':(date('H')<18?'Tag':'Abend') ?>,
          <?= htmlspecialchars($user['first_name']??$user['username']) ?>
        </h1>
      </div>
      
      <!-- Daily Stats -->
      <div class="flex gap-3">
        <div class="bg-green-500/10 border border-green-400/20 rounded-xl p-3 text-center min-w-[80px]">
          <div class="text-xs text-green-300">Heute erledigt</div>
          <div class="text-lg font-bold text-green-400"><?= $completedToday ?></div>
        </div>
        <div class="bg-blue-500/10 border border-blue-400/20 rounded-xl p-3 text-center min-w-[80px]">
          <div class="text-xs text-blue-300">Neue Aufgaben</div>
          <div class="text-lg font-bold text-blue-400"><?= $todayTasks ?></div>
        </div>
      </div>
    </div>

    <!-- Grid ------------------------------------------------------------->
    <div class="grid gap-6 auto-rows-min" style="grid-template-columns:repeat(auto-fill,minmax(320px,1fr));">

      <!-- Enhanced Inbox Widget -->
      <article class="widget-card p-6 flex flex-col">
        <div class="flex justify-between items-center mb-4">
          <a href="inbox.php" class="group inline-flex items-center widget-header">
            <h2 class="mr-1">Inbox</h2>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </a>
          
          <!-- Group Filter Dropdown -->
          <div class="relative">
            <button id="groupFilterBtn" class="widget-button text-sm flex items-center">
              <?php if ($filterType === 'mine'): ?>
                Meine Aufgaben
              <?php else: ?>
                <?php 
                $groupName = "Gruppe";
                foreach ($userGroups as $g) {
                  if ($g['id'] == $filterGroupId) {
                    $groupName = $g['name'];
                    break;
                  }
                }
                ?>
                Gruppe: <?= htmlspecialchars($groupName) ?>
              <?php endif; ?>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
            <div id="groupFilterMenu" class="absolute right-0 mt-2 w-56 dropdown-menu hidden z-20">
              <a href="?filter=mine" class="block dropdown-item <?= $filterType==='mine' ? 'active' : '' ?>">
                Meine Aufgaben
              </a>
              <?php if (!empty($userGroups)): ?>
                <div class="border-t border-white/10 my-1"></div>
                <?php foreach($userGroups as $g): ?>
                  <a href="?filter=group&group_id=<?= $g['id'] ?>" 
                     class="block dropdown-item <?= ($filterType==='group' && $filterGroupId==$g['id']) ? 'active' : '' ?>">
                    <?= htmlspecialchars($g['name']) ?>
                  </a>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
        
        <p class="widget-description mb-4"><?= $openTaskCount ?> abschließende Elemente</p>

        <div class="widget-scroll-container flex-1">
          <div class="widget-scroll-content space-y-2">
            <?php if (!empty($tasks)): ?>
              <?php foreach($tasks as $idx => $t): ?>
                <div class="widget-list-item flex flex-col gap-2"
                     onclick="window.location.href='task_detail.php?id=<?= $t['id'] ?>'">
                  <!-- Title and Due Date with Budget -->
                  <div class="flex justify-between items-center">
                    <span class="task-title truncate"><?= htmlspecialchars($t['title']) ?></span>
                    <div class="flex items-center gap-1 flex-shrink-0">
                      <?php if (!empty($t['estimated_budget'])): ?>
                        <span class="bg-green-100 text-green-800 px-1 py-0.5 rounded-full text-xs">
                          €<?= number_format($t['estimated_budget'], 0) ?>
                        </span>
                      <?php endif; ?>
                      <?php if (!empty($t['estimated_hours'])): ?>
                        <span class="bg-blue-100 text-blue-800 px-1 py-0.5 rounded-full text-xs">
                          <?= $t['estimated_hours'] ?>h
                        </span>
                      <?php endif; ?>
                      <?php if(isset($t['due_date']) && $t['due_date']): $over = strtotime($t['due_date']) < time(); ?>
                        <span class="<?= $over ? 'status-overdue' : 'status-due' ?> px-1 py-0.5 rounded-full text-xs whitespace-nowrap">
                          <?= $over ? 'Überfällig' : date('d.m.', strtotime($t['due_date'])) ?>
                        </span>
                      <?php endif; ?>
                    </div>
                  </div>
                  
                  <!-- Description (short) -->
                  <?php if(!empty($t['description'])): ?>
                    <p class="task-description line-clamp-1 text-xs"><?= htmlspecialchars(mb_strimwidth($t['description'], 0, 60, "...")) ?></p>
                  <?php endif; ?>
                  
                  <!-- Creator and Assignee Info -->
                  <div class="flex gap-2 task-meta text-xs">
                    <span class="truncate">
                      <span class="font-medium">Von:</span> 
                      <?= htmlspecialchars($t['creator_name'] ?? 'Unbekannt') ?>
                    </span>
                    <span class="truncate">
                      <span class="font-medium">Für:</span> 
                      <?php if ($t['assigned_group_id']): ?>
                        <span class="group-badge px-1 py-0.5 rounded-full">
                          <?= htmlspecialchars($t['group_name'] ?? 'Unbekannt') ?>
                        </span>
                      <?php else: ?>
                        <?= htmlspecialchars($t['assignee_name'] ?? 'Nicht zugewiesen') ?>
                      <?php endif; ?>
                    </span>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="widget-list-item text-center task-meta py-4">Keine offenen Aufgaben.</div>
            <?php endif; ?>
          </div>
        </div>
      </article>

      <!-- Tasks Widget -->
      <?php include __DIR__.'/widgets/tasks_widget.php'; ?>

      <!-- Calendar Widget -->
      <?php include __DIR__.'/widgets/calendar_widget.php'; ?>

      <!-- HaveToPay Widget -->
      <?php include __DIR__.'/widgets/havetopay_widget.php'; ?>

      <!-- Notes Widget -->
      <?php include __DIR__.'/widgets/notes_widget.php'; ?>

      <!-- Documents Widget -->
      <?php include __DIR__.'/widgets/documents_widget.php'; ?>

      <!-- Backup Widget -->
      <article class="widget-card p-6 flex flex-col">
        <div class="flex justify-between items-center mb-4">
          <a href="backup.php" class="group inline-flex items-center widget-header">
            <h2 class="mr-1">Backup</h2>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </a>
          
          <span class="status-overdue px-2 py-1 rounded-full text-xs">
            Überfällig
          </span>
        </div>
        
        <p class="widget-description mb-4">Von: Test Für: ghost1</p>

        <div class="widget-scroll-container flex-1">
          <div class="widget-scroll-content space-y-2">
            <div class="widget-list-item text-center task-meta py-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
              </svg>
              Backup-System bereit.
              <button onclick="window.location.href='backup.php'" 
                      class="block mx-auto mt-2 text-blue-400 hover:text-blue-300 text-xs">
                Backup erstellen
              </button>
            </div>
          </div>
        </div>
      </article>

      <!-- Groups Widget -->
      <article class="widget-card p-6 flex flex-col">
        <div class="flex justify-between items-center mb-4">
          <a href="groups.php" class="group inline-flex items-center widget-header">
            <h2 class="mr-1">Gruppen</h2>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </a>
          
          <button onclick="window.location.href='groups.php?action=create'" class="widget-button text-sm flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Gruppe
          </button>
        </div>
        
        <p class="widget-description mb-4"><?= count($userGroups) ?> Gruppen</p>

        <div class="widget-scroll-container flex-1">
          <div class="widget-scroll-content space-y-2">
            <?php if (!empty($userGroups)): ?>
              <?php foreach ($userGroups as $group): ?>
                <div class="widget-list-item" onclick="window.location.href='groups.php?id=<?= $group['id'] ?>'">
                  <div class="flex justify-between items-center">
                    <div class="flex items-center min-w-0">
                      <div class="w-6 h-6 bg-purple-500/20 text-purple-300 rounded-full flex items-center justify-center text-xs font-semibold mr-2 flex-shrink-0">
                        <?= strtoupper(substr($group['name'], 0, 1)) ?>
                      </div>
                      <span class="text-white/90 text-sm truncate">
                        <?= htmlspecialchars($group['name']) ?>
                      </span>
                    </div>
                    <span class="group-badge px-1 py-0.5 rounded-full text-xs">
                      Aktiv
                    </span>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="widget-list-item text-center task-meta py-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Keine Gruppen gefunden.
                <button onclick="window.location.href='groups.php?action=create'" 
                        class="block mx-auto mt-2 text-blue-400 hover:text-blue-300 text-xs">
                  Erste Gruppe erstellen
                </button>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </article>

      <!-- Cheat Widget -->
      <article class="widget-card p-6 flex flex-col">
        <div class="flex justify-between items-center mb-4">
          <a href="cheat.php" class="group inline-flex items-center widget-header">
            <h2 class="mr-1">Cheat</h2>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </a>
          
          <span class="status-overdue px-2 py-1 rounded-full text-xs">
            Überfällig
          </span>
        </div>
        
        <p class="widget-description mb-4">Schnelle Referenzen und Befehle</p>

        <div class="widget-scroll-container flex-1">
          <div class="widget-scroll-content space-y-2">
            <div class="widget-list-item text-center task-meta py-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              Cheat-Sheets verfügbar.
              <button onclick="window.location.href='cheat.php'" 
                      class="block mx-auto mt-2 text-blue-400 hover:text-blue-300 text-xs">
                Cheat-Sheets anzeigen
              </button>
            </div>
          </div>
        </div>
      </article>

    </div><!-- /grid -->
  </main>
  
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Group filter dropdown
      const groupFilterBtn = document.getElementById('groupFilterBtn');
      const groupFilterMenu = document.getElementById('groupFilterMenu');
      
      if (groupFilterBtn && groupFilterMenu) {
        groupFilterBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          groupFilterMenu.classList.toggle('hidden');
        });
        
        document.addEventListener('click', () => {
          groupFilterMenu.classList.add('hidden');
        });
      }

      // Add loading states for widget interactions
      window.showWidgetLoading = function(element) {
        element.classList.add('widget-loading');
      };
      
      window.hideWidgetLoading = function(element) {
        element.classList.remove('widget-loading');
      };
      
      // Enhanced scroll indicators
      function initScrollIndicators() {
        const scrollContainers = document.querySelectorAll('.widget-scroll-container');
        
        scrollContainers.forEach(container => {
          const content = container.querySelector('.widget-scroll-content');
          if (content) {
            function checkScroll() {
              if (content.scrollHeight > content.clientHeight) {
                container.classList.add('has-scroll');
              } else {
                container.classList.remove('has-scroll');
              }
            }
            
            checkScroll();
            window.addEventListener('resize', checkScroll);
            
            // Smooth scrolling
            content.addEventListener('wheel', (e) => {
              if (content.scrollHeight > content.clientHeight) {
                e.preventDefault();
                content.scrollBy({
                  top: e.deltaY * 0.8,
                  behavior: 'smooth'
                });
              }
            });
          }
        });
      }
      
      initScrollIndicators();
      
      // Auto-refresh widgets every 5 minutes
      setInterval(() => {
        // Only refresh if page is visible
        if (!document.hidden) {
          location.reload();
        }
      }, 300000);
      
      // Show notification for completed tasks
      window.showTaskCompleted = function(taskTitle) {
        // Create a simple toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg z-50 opacity-0 transition-opacity';
        toast.textContent = `✓ ${taskTitle} abgeschlossen`;
        document.body.appendChild(toast);
        
        setTimeout(() => toast.style.opacity = '1', 100);
        setTimeout(() => {
          toast.style.opacity = '0';
          setTimeout(() => document.body.removeChild(toast), 300);
        }, 3000);
      };
    });
  </script>
</body>
</html>
