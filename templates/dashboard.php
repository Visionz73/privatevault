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
    }
    .widget-card:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(255, 255, 255, 0.2);
      transform: translateY(-2px);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
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
  </style>
</head>
<body class="min-h-screen flex flex-col">

  <?php require_once __DIR__.'/navbar.php'; ?>

  <!-- Use responsive margin: on small screens, remove left margin so content fills the screen -->
  <!-- Adjust main margin: on mobile use top margin to push content below the fixed top navbar; on desktop use left margin -->
  <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-8 space-y-10">

    <!-- Greeting --------------------------------------------------------->
    <?php
    if (class_exists('IntlDateFormatter')) {
        $formatter = new IntlDateFormatter(
            'de_DE',
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE
        );
        $formattedDate = $formatter->format(new DateTime());
    } else {
        $formattedDate = date('l, d. F'); // Fallback using date()
    }
    ?>
    <h1 class="text-3xl font-bold greeting-text leading-tight">
      <?= $formattedDate ?><br>
      Guten <?= date('H')<12?'Morgen':(date('H')<18?'Tag':'Abend') ?>,
      <?= htmlspecialchars($user['first_name']??$user['username']) ?>
    </h1>    <!-- Grid ------------------------------------------------------------->
    <div class="grid gap-8 auto-rows-min" style="grid-template-columns:repeat(auto-fill,minmax(340px,1fr));">

      <!-- Enhanced Inbox Widget - Now same size as other widgets -->
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

      <!-- Dokumente Widget -->
      <article class="widget-card p-6 flex flex-col">
        <a href="profile.php?tab=documents" class="group inline-flex items-center mb-4 widget-header">
          <h2 class="mr-1">Dokumente</h2>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </a>
        <p class="widget-description mb-4"><?= $docCount ?> Dateien</p>

        <div class="widget-scroll-container flex-1">
          <div class="widget-scroll-content space-y-2">
            <?php if(!empty($docs)): ?>
              <?php foreach($docs as $idx=>$d): ?>
                <div class="widget-list-item">
                  <span class="truncate block task-title"><?= htmlspecialchars($d['title'] ?? '') ?></span>
                  <div class="text-xs text-white/50 mt-1">
                    <?= date('d.m.Y', strtotime($d['upload_date'])) ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="widget-list-item text-center task-meta py-4">Keine Dokumente vorhanden.</div>
            <?php endif; ?>
          </div>
        </div>
      </article>

      <!-- Meine Termine Widget -->
      <article class="widget-card p-6 flex flex-col">
        <div class="flex items-center justify-between mb-4">
          <a href="calendar.php" class="inline-flex items-center widget-header">
            Meine Termine
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </a>
          <button id="showInlineEventForm" class="widget-button">
            +
          </button>
        </div>
        <p class="widget-description mb-4"><?= count($events) ?> Termine</p>
        <!-- Inline event creation form (initially hidden) -->
        <div id="inlineEventFormContainer" class="mb-4 hidden">
          <form id="inlineEventForm" class="space-y-2 widget-form">
            <input type="text" name="title" placeholder="Event Titel" required>
            <input type="date" name="date" required>
            <button type="submit" class="w-full widget-button">
              Termin erstellen
            </button>
          </form>
        </div>
        
        <div class="widget-scroll-container flex-1">
          <div id="dashboardEventList" class="widget-scroll-content space-y-2">
            <?php if(!empty($events)): ?>
              <?php foreach($events as $evt): ?>
                <div class="widget-list-item flex justify-between items-center">
                  <a href="calendar.php" class="truncate pr-2 flex-1 task-title"><?= htmlspecialchars($evt['title']) ?></a>
                  <span class="task-meta text-xs"><?= date('d.m.Y', strtotime($evt['event_date'])) ?></span>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="widget-list-item text-center task-meta py-4">Keine Termine gefunden.</div>
            <?php endif; ?>
          </div>
        </div>
      </article>      <!-- HaveToPay Widget - Now placed next to calendar widget -->
      <?php include __DIR__.'/widgets/havetopay_widget.php'; ?>

      <!-- Placeholder Cards - Display fewer placeholders to maintain balance -->
      <?php foreach(['Recruiting','Org-Chart'] as $name): ?>
        <article class="placeholder-widget">
          <?= $name ?>-Widget
        </article>
      <?php endforeach; ?>
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

      // Initialize scroll indicators for widgets
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
            
            // Check initially
            checkScroll();
            
            // Check on resize
            window.addEventListener('resize', checkScroll);
            
            // Smooth scrolling on wheel
            content.addEventListener('wheel', (e) => {
              e.preventDefault();
              content.scrollBy({
                top: e.deltaY * 0.5,
                behavior: 'smooth'
              });
            });
          }
        });
      }
      
      // Initialize scroll indicators
      initScrollIndicators();
    });
    
    // Toggle inline event creation form
    document.getElementById('showInlineEventForm').addEventListener('click', function() {
      document.getElementById('inlineEventFormContainer').classList.toggle('hidden');
    });

    // Handle inline event form submission via AJAX
    document.getElementById('inlineEventForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const title = this.title.value.trim();
      const date = this.date.value;
      if(title && date){
        fetch('/create_event.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({ title: title, date: date })
        })
        .then(response => response.json())
        .then(data => {
          if(data.success){
            // Create new list element for the event
            const newEvent = data.event;
            const li = document.createElement('div');
            li.className = "widget-list-item flex justify-between items-center";
            li.innerHTML = `<a href="calendar.php" class="truncate pr-2 flex-1 task-title">${newEvent.title}</a>
                             <span class="task-meta text-xs">${new Date(newEvent.date).toLocaleDateString('de-DE')}</span>`;
            const eventList = document.getElementById('dashboardEventList');
            
            // If "Keine Termine gefunden." is present, remove it.
            if(eventList.childElementCount === 1 && eventList.firstElementChild.textContent.includes('Keine Termine')) {
              eventList.innerHTML = '';
            }
            eventList.prepend(li); // Add to top
            this.reset();
            document.getElementById('inlineEventFormContainer').classList.add('hidden');
          } else {
            alert('Fehler: ' + (data.error || 'Unbekannter Fehler'));
          }
        })
        .catch(() => alert('Fehler beim Erstellen des Termins.'));
      }
    });
  </script>
</body>
</html>
