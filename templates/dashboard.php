<!-- templates/dashboard.php -->
<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body { 
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
      min-height: 100vh;
    }
    @media (max-width: 768px) {
      main { margin-top: 4rem; }
    }

    /* Dashboard Shorts - Konsistentes dunkles Glassmorphism */
    .dashboard-short {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1.5rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }
    .dashboard-short:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(255, 255, 255, 0.2);
      transform: translateY(-2px);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
    }

    /* Simple Header Styling */
    .short-header {
      background: rgba(255, 255, 255, 0.1);
      border-bottom: 1px solid rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .short-header:hover {
      background: rgba(255, 255, 255, 0.15);
    }

    /* Finance Header - Special styling for balance display */
    .finance-header {
      background: rgba(255, 255, 255, 0.1);
      border-bottom: 1px solid rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
    }

    /* Stats Numbers - Weißer Text für bessere Lesbarkeit */
    .stats-number {
      color: white;
      font-weight: 800;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    /* Quick Action Buttons */
    .quick-action-btn {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      border-radius: 0.75rem;
      transition: all 0.3s ease;
      font-size: 0.875rem;
      font-weight: 500;
    }
    .quick-action-btn:hover {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.3);
      transform: translateY(-1px);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    }

    /* List Items in Shorts */
    .short-list-item {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 0.75rem;
      transition: all 0.3s ease;
      cursor: pointer;
    }
    .short-list-item:hover {
      background: rgba(255, 255, 255, 0.1);
      border-color: rgba(255, 255, 255, 0.2);
      transform: translateX(4px);
    }

    /* Greeting Text */
    .greeting-text {
      color: white;
      text-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    /* Badge Styles */
    .status-badge {
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      padding: 4px 10px;
      backdrop-filter: blur(10px);
    }
    .badge-pending { 
      background: rgba(251, 191, 36, 0.2); 
      color: #fbbf24; 
      border: 1px solid rgba(251, 191, 36, 0.3); 
    }
    .badge-completed { 
      background: rgba(34, 197, 94, 0.2); 
      color: #86efac; 
      border: 1px solid rgba(34, 197, 94, 0.3); 
    }
    .badge-overdue { 
      background: rgba(239, 68, 68, 0.2); 
      color: #fca5a5; 
      border: 1px solid rgba(239, 68, 68, 0.3); 
    }

    /* Progress Bars */
    .progress-bar {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 8px;
      overflow: hidden;
    }
    .progress-fill {
      background: linear-gradient(90deg, #3b82f6, #1d4ed8);
      height: 8px;
      border-radius: 8px;
      transition: width 0.5s ease;
    }

    /* Text Colors */
    .text-primary { color: white !important; }
    .text-secondary { color: rgba(255, 255, 255, 0.8) !important; }
    .text-muted { color: rgba(255, 255, 255, 0.6) !important; }

    /* Member Badge für Gruppen */
    .member-badge {
      background: rgba(147, 51, 234, 0.2);
      color: #c4b5fd;
      border: 1px solid rgba(147, 51, 234, 0.3);
      padding: 2px 8px;
      border-radius: 9999px;
      font-size: 0.75rem;
      backdrop-filter: blur(10px);
    }

    /* Scrollbar styling */
    .short-scroll {
      max-height: 280px;
      overflow-y: auto;
      scrollbar-width: thin;
      scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
    }
    .short-scroll::-webkit-scrollbar {
      width: 6px;
    }
    .short-scroll::-webkit-scrollbar-track {
      background: transparent;
    }
    .short-scroll::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.2);
      border-radius: 3px;
    }
  </style>
</head>
<body class="min-h-screen">
  <?php require_once __DIR__.'/navbar.php'; ?>

  <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-6 space-y-8">
    <!-- Dynamic Greeting -->
    <div class="text-center mb-12">
      <h1 class="text-4xl md:text-6xl font-bold greeting-text mb-4">
        <?php
        $hour = date('H');
        $greeting = $hour < 12 ? 'Guten Morgen' : ($hour < 18 ? 'Guten Tag' : 'Guten Abend');
        echo $greeting;
        ?>,
        <?= htmlspecialchars($user['first_name'] ?? $user['username']) ?>
      </h1>
      <p class="text-xl text-white/70">
        <?= date('l, d. F Y') ?> • Willkommen in deinem Dashboard
      </p>
    </div>

    <!-- Dashboard Shorts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-8">
      
      <!-- Tasks Short -->
      <div class="dashboard-short col-span-1 md:col-span-2 xl:col-span-1">
        <div class="short-header p-6" onclick="window.location.href='inbox.php'">
          <div class="flex items-center justify-between">
            <h3 class="text-white font-semibold text-xl">Inbox</h3>
            <div class="text-right">
              <div class="stats-number text-3xl"><?= $openTaskCount ?></div>
              <div class="text-white/60 text-sm">offen</div>
            </div>
          </div>
        </div>
        
        <div class="p-6">
          <div class="short-scroll space-y-3">
            <?php if (!empty($tasks)): ?>
              <?php foreach(array_slice($tasks, 0, 5) as $task): ?>
                <div class="short-list-item p-4" onclick="window.location.href='task_detail.php?id=<?= $task['id'] ?>'">
                  <div class="flex justify-between items-start mb-2">
                    <h4 class="text-white font-medium text-sm truncate flex-1"><?= htmlspecialchars($task['title']) ?></h4>
                    <?php if(isset($task['due_date']) && $task['due_date']): ?>
                      <span class="status-badge <?= strtotime($task['due_date']) < time() ? 'badge-overdue' : 'badge-pending' ?> ml-2">
                        <?= date('d.m.', strtotime($task['due_date'])) ?>
                      </span>
                    <?php endif; ?>
                  </div>
                  <?php if(!empty($task['description'])): ?>
                    <p class="text-white/60 text-xs truncate"><?= htmlspecialchars($task['description']) ?></p>
                  <?php endif; ?>
                  <div class="flex justify-between text-xs text-white/50 mt-2">
                    <span>Von: <?= htmlspecialchars($task['creator_name'] ?? 'Unbekannt') ?></span>
                    <span><?= $task['assigned_group_id'] ? 'Gruppe' : 'Persönlich' ?></span>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="text-center py-8">
                <p class="text-white/60">Keine offenen Aufgaben</p>
              </div>
            <?php endif; ?>
          </div>
          
          <div class="mt-6 grid grid-cols-2 gap-3">
            <button onclick="window.location.href='inbox.php'" class="quick-action-btn px-4 py-2">
              Inbox
            </button>
            <button onclick="window.location.href='create_task.php'" class="quick-action-btn px-4 py-2">
              Neue Aufgabe
            </button>
          </div>
        </div>
      </div>

      <!-- Calendar Short -->
      <div class="dashboard-short">
        <div class="short-header p-6" onclick="window.location.href='calendar.php'">
          <div class="flex items-center justify-between">
            <h3 class="text-white font-semibold text-xl">Kalender</h3>
            <div class="text-right">
              <div class="stats-number text-3xl"><?= count($upcomingEvents ?? []) ?></div>
              <div class="text-white/60 text-sm">heute</div>
            </div>
          </div>
        </div>
        
        <div class="p-6">
          <div class="short-scroll space-y-3">
            <?php if (!empty($upcomingEvents)): ?>
              <?php foreach(array_slice($upcomingEvents, 0, 4) as $event): ?>
                <div class="short-list-item p-4" onclick="window.location.href='calendar.php'">
                  <div class="flex justify-between items-start mb-2">
                    <h4 class="text-white font-medium text-sm truncate flex-1"><?= htmlspecialchars($event['title']) ?></h4>
                    <span class="text-blue-400 text-xs ml-2"><?= date('H:i', strtotime($event['event_date'])) ?></span>
                  </div>
                  <?php if(!empty($event['description'])): ?>
                    <p class="text-white/60 text-xs truncate"><?= htmlspecialchars($event['description']) ?></p>
                  <?php endif; ?>
                  <div class="text-xs text-white/50 mt-2">
                    <span><?= date('d.m.Y', strtotime($event['event_date'])) ?></span>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="text-center py-6">
                <p class="text-white/60 text-sm">Keine Termine geplant</p>
              </div>
            <?php endif; ?>
          </div>
          
          <div class="mt-6">
            <button onclick="window.location.href='calendar.php'" class="quick-action-btn w-full px-4 py-2">
              Neuer Termin
            </button>
          </div>
        </div>
      </div>

      <!-- Documents Short -->
      <div class="dashboard-short">
        <div class="short-header p-6" onclick="window.location.href='profile.php?tab=documents'">
          <div class="flex items-center justify-between">
            <h3 class="text-white font-semibold text-xl">Dokumente</h3>
            <div class="text-right">
              <div class="stats-number text-3xl"><?= $docCount ?></div>
              <div class="text-white/60 text-sm">gesamt</div>
            </div>
          </div>
        </div>
        
        <div class="p-6">
          <div class="short-scroll space-y-3">
            <?php if (!empty($recentDocuments)): ?>
              <?php foreach(array_slice($recentDocuments, 0, 4) as $doc): ?>
                <div class="short-list-item p-4" onclick="window.location.href='profile.php?tab=documents'">
                  <div class="flex items-center space-x-3">
                    <div class="icon-gradient-green p-2 rounded-lg">
                      <i class="fas fa-file text-white text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                      <h4 class="text-white font-medium text-sm truncate"><?= htmlspecialchars($doc['filename']) ?></h4>
                      <p class="text-white/60 text-xs"><?= date('d.m.Y', strtotime($doc['upload_date'])) ?></p>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="text-center py-6">
                <p class="text-white/60 text-sm">Keine Dokumente hochgeladen</p>
              </div>
            <?php endif; ?>
          </div>
          
          <div class="mt-6">
            <button onclick="window.location.href='profile.php?tab=documents'" class="quick-action-btn w-full px-4 py-2">
              Hochladen
            </button>
          </div>
        </div>
      </div>

      <!-- HaveToPay Short - Keep existing balance layout -->
      <div class="dashboard-short">
        <div class="finance-header p-6">
          <div class="flex items-center justify-between">
            <a href="havetopay.php" class="text-white font-semibold text-xl hover:text-white/80 transition-colors">
              Finanzen
            </a>
            <div class="text-right">
              <div class="stats-number text-3xl <?= $widgetNetBalance >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                <?= number_format($widgetNetBalance, 0) ?>€
              </div>
              <div class="text-white/60 text-sm">Bilanz</div>
            </div>
          </div>
        </div>
        
        <div class="p-6">
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="text-center p-3 bg-green-500/10 border border-green-400/20 rounded-xl backdrop-filter blur-10">
              <div class="text-green-400 font-bold text-lg">+<?= number_format($widgetTotalOwed, 0) ?>€</div>
              <div class="text-white/60 text-xs">Du bekommst</div>
            </div>
            <div class="text-center p-3 bg-red-500/10 border border-red-400/20 rounded-xl backdrop-filter blur-10">
              <div class="text-red-400 font-bold text-lg">-<?= number_format($widgetTotalOwing, 0) ?>€</div>
              <div class="text-white/60 text-xs">Du schuldest</div>
            </div>
          </div>
          
          <div class="short-scroll space-y-2">
            <?php if (!empty($recentExpenses)): ?>
              <?php foreach(array_slice($recentExpenses, 0, 3) as $expense): ?>
                <div class="short-list-item p-3" onclick="window.location.href='havetopay_detail.php?id=<?= $expense['id'] ?>'">
                  <div class="flex justify-between items-center">
                    <div class="flex-1 min-w-0">
                      <h4 class="text-white font-medium text-sm truncate"><?= htmlspecialchars($expense['title']) ?></h4>
                      <p class="text-white/60 text-xs">€<?= number_format($expense['amount'], 2) ?></p>
                    </div>
                    <span class="status-badge badge-pending"><?= date('d.m.', strtotime($expense['expense_date'])) ?></span>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="text-center py-4">
                <i class="fas fa-coins text-white/30 text-2xl mb-2"></i>
                <p class="text-white/60 text-sm">Keine Ausgaben</p>
              </div>
            <?php endif; ?>
          </div>
          
          <div class="mt-6">
            <button onclick="window.location.href='havetopay.php'" class="quick-action-btn w-full px-4 py-2">
              Ausgabe hinzufügen
            </button>
          </div>
        </div>
      </div>

      <!-- System Stats Short -->
      <div class="dashboard-short">
        <div class="short-header p-6" onclick="window.location.href='profile.php'">
          <h3 class="text-white font-semibold text-xl">Statistiken</h3>
        </div>
        
        <div class="p-6 space-y-4">
          <div class="flex justify-between items-center">
            <span class="text-white/80 text-sm">Aufgaben erledigt</span>
            <span class="text-white font-semibold"><?= $completedTasksCount ?? 0 ?></span>
          </div>
          <div class="progress-bar">
            <div class="progress-fill" style="width: <?= min(100, ($completedTasksCount ?? 0) * 10) ?>%"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-white/80 text-sm">Dokumente</span>
            <span class="text-white font-semibold"><?= $docCount ?></span>
          </div>
          <div class="progress-bar">
            <div class="progress-fill bg-gradient-to-r from-green-500 to-green-400" style="width: <?= min(100, $docCount * 5) ?>%"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-white/80 text-sm">Termine</span>
            <span class="text-white font-semibold"><?= count($upcomingEvents ?? []) ?></span>
          </div>
          <div class="progress-bar">
            <div class="progress-fill bg-gradient-to-r from-purple-500 to-purple-400" style="width: <?= min(100, count($upcomingEvents ?? []) * 20) ?>%"></div>
          </div>
          
          <div class="mt-6">
            <button onclick="window.location.href='profile.php'" class="quick-action-btn w-full px-4 py-2">
              <i class="fas fa-user mr-2"></i>Profil
            </button>
          </div>
        </div>
      </div>

      <!-- Quick Actions Short -->
      <div class="dashboard-short col-span-1 md:col-span-2 xl:col-span-1">
        <div class="short-header p-6">
          <h3 class="text-white font-semibold text-xl">Schnellaktionen</h3>
        </div>
        
        <div class="p-6">
          <div class="grid grid-cols-2 gap-4">
            <button onclick="window.location.href='create_task.php'" class="quick-action-btn p-4 text-center">
              <i class="fas fa-plus text-2xl mb-2"></i>
              <div class="text-sm">Neue Aufgabe</div>
            </button>
            <button onclick="window.location.href='calendar.php'" class="quick-action-btn p-4 text-center">
              <i class="fas fa-calendar-plus text-2xl mb-2"></i>
              <div class="text-sm">Termin</div>
            </button>
            <button onclick="window.location.href='havetopay_add.php'" class="quick-action-btn p-4 text-center">
              <i class="fas fa-receipt text-2xl mb-2"></i>
              <div class="text-sm">Ausgabe</div>
            </button>
            <button onclick="window.location.href='profile.php?tab=documents'" class="quick-action-btn p-4 text-center">
              <i class="fas fa-upload text-2xl mb-2"></i>
              <div class="text-sm">Upload</div>
            </button>
            <button onclick="window.location.href='taskboard.php'" class="quick-action-btn p-4 text-center">
              <i class="fas fa-tasks text-2xl mb-2"></i>
              <div class="text-sm">Kanban</div>
            </button>
            <button onclick="window.location.href='admin/groups.php'" class="quick-action-btn p-4 text-center">
              <i class="fas fa-users text-2xl mb-2"></i>
              <div class="text-sm">Gruppen</div>
            </button>
          </div>
        </div>
      </div>

    </div>

    <!-- Recent Activity -->
    <div class="dashboard-short mt-8">
      <div class="short-header p-6">
        <h3 class="text-white font-semibold text-xl">Letzte Aktivität</h3>
      </div>
      
      <div class="p-6">
        <div class="short-scroll space-y-3 max-h-48">
          <div class="short-list-item p-4">
            <div class="flex items-center space-x-3">
              <div class="icon-gradient-green p-2 rounded-full w-8 h-8 flex items-center justify-center">
                <i class="fas fa-check text-white text-xs"></i>
              </div>
              <div>
                <p class="text-white text-sm">Dashboard wurde geladen</p>
                <p class="text-white/60 text-xs">vor wenigen Sekunden</p>
              </div>
            </div>
          </div>
          <!-- Add more activities dynamically here -->
        </div>
      </div>
    </div>

  </main>
  
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Simple fade-in animation only
      const cards = document.querySelectorAll('.dashboard-short');
      cards.forEach((card, index) => {
        card.style.opacity = '0';
        setTimeout(() => {
          card.style.transition = 'opacity 0.4s ease';
          card.style.opacity = '1';
        }, index * 50);
      });
    });
  </script>
</body>
</html>
