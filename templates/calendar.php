<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Kalender | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet"/>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: 'Inter', sans-serif; }
    @media (max-width: 768px) {
      main { margin-top: 3.5rem; }
    }
    .calendar-day { min-height: 100px; }
    .calendar-day.today { background-color: rgba(74, 144, 226, 0.05); }
    .calendar-day.other-month { opacity: 0.5; }
    .event-dot { width: 10px; height: 10px; border-radius: 50%; }
    .event-item { 
      cursor: pointer;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#eef7ff] via-[#f7fbff] to-[#f9fdf2] flex flex-col">
  <?php require_once __DIR__.'/navbar.php'; ?>
  
  <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
    <header class="mb-6">
      <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <h1 class="text-3xl font-bold text-gray-900">Kalender</h1>
        
        <!-- Calendar Controls -->
        <div class="flex flex-wrap gap-3">
          <!-- View Switcher -->
          <div class="inline-flex bg-white rounded-lg shadow-sm">
            <a href="?view=month&year=<?= $year ?>&month=<?= $month ?>&day=<?= $day ?>" 
               class="px-4 py-2 rounded-l-lg <?= $view === 'month' ? 'bg-[#4A90E2] text-white' : 'text-gray-700' ?>">
              Monat
            </a>
            <a href="?view=week&year=<?= $year ?>&month=<?= $month ?>&day=<?= $day ?>" 
               class="px-4 py-2 <?= $view === 'week' ? 'bg-[#4A90E2] text-white' : 'text-gray-700' ?>">
              Woche
            </a>
            <a href="?view=day&year=<?= $year ?>&month=<?= $month ?>&day=<?= $day ?>" 
               class="px-4 py-2 rounded-r-lg <?= $view === 'day' ? 'bg-[#4A90E2] text-white' : 'text-gray-700' ?>">
              Tag
            </a>
          </div>
          
          <!-- Date Navigation -->
          <div class="inline-flex bg-white rounded-lg shadow-sm">
            <?php if ($view === 'month'): ?>
              <?php 
                $prevMonth = $month - 1;
                $prevYear = $year;
                if ($prevMonth < 1) {
                  $prevMonth = 12;
                  $prevYear--;
                }
                
                $nextMonth = $month + 1;
                $nextYear = $year;
                if ($nextMonth > 12) {
                  $nextMonth = 1;
                  $nextYear++;
                }
              ?>
              <a href="?view=month&year=<?= $prevYear ?>&month=<?= $prevMonth ?>" class="px-3 py-2 rounded-l-lg hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
              </a>
              <span class="px-4 py-2 text-gray-700">
                <?= date('F Y', strtotime("$year-$month-01")) ?>
              </span>
              <a href="?view=month&year=<?= $nextYear ?>&month=<?= $nextMonth ?>" class="px-3 py-2 rounded-r-lg hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </a>
            <?php elseif ($view === 'week'): ?>
              <?php
                $prevWeek = clone $currentDate;
                $prevWeek->modify('-7 days');
                
                $nextWeek = clone $currentDate;
                $nextWeek->modify('+7 days');
              ?>
              <a href="?view=week&year=<?= $prevWeek->format('Y') ?>&month=<?= $prevWeek->format('m') ?>&day=<?= $prevWeek->format('d') ?>" 
                 class="px-3 py-2 rounded-l-lg hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
              </a>
              <span class="px-4 py-2 text-gray-700">
                KW <?= $dateInfo['currentWeek'] ?>: 
                <?= date('d.m.', strtotime($dateInfo['startDate'])) ?> - 
                <?= date('d.m.Y', strtotime($dateInfo['endDate'])) ?>
              </span>
              <a href="?view=week&year=<?= $nextWeek->format('Y') ?>&month=<?= $nextWeek->format('m') ?>&day=<?= $nextWeek->format('d') ?>" 
                 class="px-3 py-2 rounded-r-lg hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </a>
            <?php else: ?>
              <?php
                $prevDay = clone $currentDate;
                $prevDay->modify('-1 day');
                
                $nextDay = clone $currentDate;
                $nextDay->modify('+1 day');
              ?>
              <a href="?view=day&year=<?= $prevDay->format('Y') ?>&month=<?= $prevDay->format('m') ?>&day=<?= $prevDay->format('d') ?>" 
                 class="px-3 py-2 rounded-l-lg hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
              </a>
              <span class="px-4 py-2 text-gray-700">
                <?= date('d. F Y', strtotime($dateInfo['currentDate'])) ?>
              </span>
              <a href="?view=day&year=<?= $nextDay->format('Y') ?>&month=<?= $nextDay->format('m') ?>&day=<?= $nextDay->format('d') ?>" 
                 class="px-3 py-2 rounded-r-lg hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </a>
            <?php endif; ?>
          </div>
          
          <!-- New Event Button -->
          <button id="newEventBtn" class="px-4 py-2 bg-[#4A90E2] text-white rounded-lg shadow hover:bg-[#4A90E2]/90 transition-colors">
            + Neuer Termin
          </button>
        </div>
      </div>
      
      <?php if (!empty($success)): ?>
        <div class="mt-4 bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
          <?= htmlspecialchars($success) ?>
        </div>
      <?php endif; ?>
      
      <?php if (!empty($errors)): ?>
        <div class="mt-4 bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
          <ul class="list-disc list-inside">
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
    </header>
    
    <!-- Calendar View -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-8">
      <?php if ($view === 'month'): ?>
        <!-- Month View -->
        <div class="grid grid-cols-7 text-center font-semibold bg-gray-50 border-b">
          <div class="px-2 py-3">Mo</div>
          <div class="px-2 py-3">Di</div>
          <div class="px-2 py-3">Mi</div>
          <div class="px-2 py-3">Do</div>
          <div class="px-2 py-3">Fr</div>
          <div class="px-2 py-3">Sa</div>
          <div class="px-2 py-3">So</div>
        </div>
        
        <div class="grid grid-cols-7 border-l">
          <?php
            $currentDay = new DateTime($dateInfo['startDate']);
            $today = new DateTime();
            $today->setTime(0, 0, 0);
            
            while ($currentDay->format('Y-m-d') <= $dateInfo['endDate']) {
              $isToday = $currentDay->format('Y-m-d') === $today->format('Y-m-d');
              $isOtherMonth = $currentDay->format('m') != $dateInfo['currentMonth'];
              $dateStr = $currentDay->format('Y-m-d');
              $dayEvents = $eventsByDate[$dateStr] ?? [];
          ?>
            <div class="calendar-day border-b border-r p-1 <?= $isToday ? 'today' : '' ?> <?= $isOtherMonth ? 'other-month' : '' ?>">
              <div class="flex justify-between items-center mb-1">
                <span class="text-sm font-medium <?= $isToday ? 'text-[#4A90E2]' : '' ?>">
                  <?= $currentDay->format('j') ?>
                </span>
                <button class="add-event-btn text-xs text-gray-500 hover:text-[#4A90E2]" 
                        data-date="<?= $dateStr ?>">+</button>
              </div>
              
              <!-- Events for this day -->
              <div class="space-y-1">
                <?php foreach ($dayEvents as $event): ?>
                  <div class="event-item text-xs p-1 rounded"
                       style="background-color: <?= htmlspecialchars($event['color']) ?>25; border-left: 3px solid <?= htmlspecialchars($event['color']) ?>;"
                       data-event-id="<?= $event['id'] ?>"
                       onclick="showEventDetails(<?= htmlspecialchars(json_encode($event)) ?>)">
                    <?php if (!$event['all_day'] && $event['start_time']): ?>
                      <span class="font-medium"><?= date('H:i', strtotime($event['start_time'])) ?></span>
                    <?php endif; ?>
                    <?= htmlspecialchars($event['title']) ?>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php
              $currentDay->modify('+1 day');
            }
          ?>
        </div>
        
      <?php elseif ($view === 'week'): ?>
        <!-- Week View -->
        <div class="grid grid-cols-8">
          <!-- Time column -->
          <div class="border-r">
            <div class="h-12 border-b"></div>
            <?php for ($hour = 0; $hour < 24; $hour++): ?>
              <div class="h-12 border-b text-xs text-right pr-2 pt-0">
                <?= sprintf('%02d:00', $hour) ?>
              </div>
            <?php endfor; ?>
          </div>
          
          <?php
            $weekDay = new DateTime($dateInfo['startDate']);
            $today = new DateTime();
            $today->setTime(0, 0, 0);
            
            for ($i = 0; $i < 7; $i++):
              $isToday = $weekDay->format('Y-m-d') === $today->format('Y-m-d');
              $dateStr = $weekDay->format('Y-m-d');
              $dayEvents = $eventsByDate[$dateStr] ?? [];
          ?>
            <div class="border-r">
              <!-- Day header -->
              <div class="h-12 border-b p-1 text-center <?= $isToday ? 'bg-blue-50' : 'bg-gray-50' ?>">
                <div class="text-xs font-semibold"><?= $weekDay->format('D') ?></div>
                <div class="text-sm <?= $isToday ? 'text-[#4A90E2] font-bold' : '' ?>"><?= $weekDay->format('j.n.') ?></div>
              </div>
              
              <!-- Hours grid - simplified to just show all-day events -->
              <div class="relative">
                <?php for ($hour = 0; $hour < 24; $hour++): ?>
                  <div class="h-12 border-b"></div>
                <?php endfor; ?>
                
                <!-- Events - just show markers in this simplified view -->
                <?php foreach ($dayEvents as $event): ?>
                  <?php if ($event['all_day']): ?>
                    <div class="absolute top-0 left-0 right-0 px-1 py-0.5 bg-[<?= $event['color'] ?>] text-white text-xs truncate">
                      <?= htmlspecialchars($event['title']) ?>
                    </div>
                  <?php elseif ($event['start_time']): ?>
                    <?php 
                      $hour = (int)substr($event['start_time'], 0, 2);
                      $minute = (int)substr($event['start_time'], 3, 2);
                      $top = ($hour * 12) + ($minute / 5);
                      $height = 12; // Default 1-hour height
                      
                      if ($event['end_time']) {
                        $endHour = (int)substr($event['end_time'], 0, 2);
                        $endMinute = (int)substr($event['end_time'], 3, 2);
                        $endTop = ($endHour * 12) + ($endMinute / 5);
                        $height = $endTop - $top;
                      }
                    ?>
                    <div class="absolute px-1 py-0.5 text-xs truncate cursor-pointer"
                         style="top: <?= $top ?>px; height: <?= $height ?>px; left: 0; right: 0; background-color: <?= $event['color'] ?>75;"
                         onclick="showEventDetails(<?= htmlspecialchars(json_encode($event)) ?>)">
                      <?= date('H:i', strtotime($event['start_time'])) ?> <?= htmlspecialchars($event['title']) ?>
                    </div>
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>
            </div>
          <?php
              $weekDay->modify('+1 day');
            endfor;
          ?>
        </div>
        
      <?php else: ?>
        <!-- Day View -->
        <div class="grid grid-cols-1 md:grid-cols-2">
          <div class="p-4 border-r">
            <h3 class="text-lg font-semibold mb-4"><?= date('l, d. F Y', strtotime($dateInfo['currentDate'])) ?></h3>
            
            <?php 
              $dateStr = $dateInfo['currentDate'];
              $dayEvents = $eventsByDate[$dateStr] ?? []; 
              usort($dayEvents, function($a, $b) {
                if ($a['all_day'] && !$b['all_day']) return -1;
                if (!$a['all_day'] && $b['all_day']) return 1;
                if ($a['all_day'] && $b['all_day']) return 0;
                return strcmp($a['start_time'] ?? '', $b['start_time'] ?? '');
              });
            ?>
            
            <?php if (empty($dayEvents)): ?>
              <p class="text-gray-500">Keine Termine für diesen Tag.</p>
            <?php else: ?>
              <div class="space-y-3">
                <?php foreach ($dayEvents as $event): ?>
                  <div class="p-3 rounded-lg border-l-4" 
                       style="border-color: <?= htmlspecialchars($event['color']) ?>; background-color: <?= htmlspecialchars($event['color']) ?>10;"
                       onclick="showEventDetails(<?= htmlspecialchars(json_encode($event)) ?>)">
                    <div class="font-semibold"><?= htmlspecialchars($event['title']) ?></div>
                    
                    <?php if ($event['all_day']): ?>
                      <div class="text-sm text-gray-500">Ganztägig</div>
                    <?php elseif ($event['start_time']): ?>
                      <div class="text-sm text-gray-500">
                        <?= date('H:i', strtotime($event['start_time'])) ?>
                        <?php if ($event['end_time']): ?>
                          - <?= date('H:i', strtotime($event['end_time'])) ?>
                        <?php endif; ?>
                      </div>
                    <?php endif; ?>
                    
                    <?php if ($event['location']): ?>
                      <div class="text-sm text-gray-500"><?= htmlspecialchars($event['location']) ?></div>
                    <?php endif; ?>
                    
                    <?php if ($event['assigned_to']): ?>
                      <div class="mt-2 text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded-full inline-block">
                        Zugewiesen an: <?= htmlspecialchars($event['assignee_name']) ?>
                      </div>
                    <?php endif; ?>
                    
                    <?php if ($event['assigned_group_id']): ?>
                      <div class="mt-2 text-xs px-2 py-1 bg-purple-100 text-purple-800 rounded-full inline-block">
                        Gruppe: <?= htmlspecialchars($event['group_name']) ?>
                      </div>
                    <?php endif; ?>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
          
          <div class="p-4">
            <div id="dayViewDetail" class="hidden">
              <h3 id="detailTitle" class="text-xl font-semibold mb-2"></h3>
              <p id="detailTime" class="text-sm text-gray-500 mb-4"></p>
              
              <div id="detailDescription" class="mb-4 text-gray-700"></div>
              
              <div id="detailAssignment" class="text-sm"></div>
              
              <div class="mt-4 flex space-x-2">
                <button id="editEventBtn" class="px-3 py-1 bg-blue-100 text-blue-700 rounded">Bearbeiten</button>
                <button id="deleteEventBtn" class="px-3 py-1 bg-red-100 text-red-700 rounded">Löschen</button>
              </div>
            </div>
            
            <div id="dayViewPlaceholder" class="text-center text-gray-500 py-12">
              Wählen Sie einen Termin aus, um Details anzuzeigen.
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </main>
  
  <!-- New Event Modal -->
  <div id="eventModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 mx-4 max-h-[90vh] overflow-y-auto">
      <div class="flex justify-between items-start mb-4">
        <h2 id="modalTitle" class="text-xl font-semibold">Neuer Termin</h2>
        <button id="closeModal" class="text-gray-400 hover:text-gray-600">&times;</button>
      </div>
      
      <form id="eventForm" method="post" action="/calendar.php" class="space-y-4">
        <input type="hidden" name="action" value="create_event">
        <input type="hidden" name="view" value="<?= $view ?>">
        <input type="hidden" name="year" value="<?= $year ?>">
        <input type="hidden" name="month" value="<?= $month ?>">
        <input type="hidden" name="day" value="<?= $day ?>">
        
        <div>
          <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titel *</label>
          <input type="text" id="title" name="title" required 
                 class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
        </div>
        
        <div>
          <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Beschreibung</label>
          <textarea id="description" name="description" rows="3" 
                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]"></textarea>
        </div>
        
        <div>
          <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Ort</label>
          <input type="text" id="location" name="location" 
                 class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
        </div>
        
        <div>
          <label for="event_date" class="block text-sm font-medium text-gray-700 mb-1">Datum *</label>
          <input type="date" id="event_date" name="event_date" required 
                 class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
        </div>
        
        <div class="flex items-center mb-4">
          <input type="checkbox" id="all_day" name="all_day" checked 
                 class="h-4 w-4 text-[#4A90E2] focus:ring-[#4A90E2]">
          <label for="all_day" class="ml-2 text-sm text-gray-700">Ganztägiger Termin</label>
        </div>
        
        <div id="timeSelectionGroup" class="grid grid-cols-2 gap-4 hidden">
          <div>
            <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Startzeit</label>
            <input type="time" id="start_time" name="start_time" 
                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
          </div>
          
          <div>
            <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">Endzeit</label>
            <input type="time" id="end_time" name="end_time" 
                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Zuweisung</label>
          <div class="grid grid-cols-3 gap-4 mb-4">
            <div>
              <label class="inline-flex items-center">
                <input type="radio" name="assignment_type" value="none" checked 
                       onclick="toggleAssignmentType('none')"
                       class="h-4 w-4 text-[#4A90E2] border-gray-300 focus:ring-[#4A90E2]">
                <span class="ml-2 text-sm">Keine</span>
              </label>
            </div>
            <div>
              <label class="inline-flex items-center">
                <input type="radio" name="assignment_type" value="user"
                       onclick="toggleAssignmentType('user')"
                       class="h-4 w-4 text-[#4A90E2] border-gray-300 focus:ring-[#4A90E2]">
                <span class="ml-2 text-sm">Benutzer</span>
              </label>
            </div>
            <div>
              <label class="inline-flex items-center">
                <input type="radio" name="assignment_type" value="group"
                       onclick="toggleAssignmentType('group')"
                       class="h-4 w-4 text-[#4A90E2] border-gray-300 focus:ring-[#4A90E2]">
                <span class="ml-2 text-sm">Gruppe</span>
              </label>
            </div>
          </div>
          
          <!-- User Assignment -->
          <div id="user_assignment" class="hidden">
            <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">Benutzer auswählen</label>
            <select id="assigned_to" name="assigned_to" 
                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
              <option value="" disabled selected>Bitte auswählen...</option>
              <?php foreach ($allUsers as $user): ?>
                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <!-- Group Assignment -->
          <div id="group_assignment" class="hidden">
            <label for="assigned_group_id" class="block text-sm font-medium text-gray-700 mb-1">Gruppe auswählen</label>
            <select id="assigned_group_id" name="assigned_group_id"
                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
              <option value="" disabled selected>Bitte auswählen...</option>
              <?php foreach ($userGroups as $group): ?>
                <option value="<?= $group['id'] ?>"><?= htmlspecialchars($group['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        
        <div>
          <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Farbe</label>
          <div class="flex space-x-2">
            <input type="color" id="color" name="color" value="#4A90E2"
                   class="h-8 w-8 rounded cursor-pointer">
            <input type="text" id="colorText" value="#4A90E2" disabled
                   class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-50">
          </div>
        </div>
        
        <div class="pt-4 flex justify-end space-x-3">
          <button type="button" id="cancelBtn" 
                  class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            Abbrechen
          </button>
          <button type="submit" 
                  class="px-4 py-2 bg-[#4A90E2] text-white rounded-lg hover:bg-[#4A90E2]/90">
            Speichern
          </button>
        </div>
      </form>
    </div>
  </div>
  
  <script>
    // Event modal handling
    const modal = document.getElementById('eventModal');
    const openModalBtn = document.getElementById('newEventBtn');
    const closeModalBtn = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const allDayCheckbox = document.getElementById('all_day');
    const timeSelectionGroup = document.getElementById('timeSelectionGroup');
    const eventForm = document.getElementById('eventForm');
    const eventDateInput = document.getElementById('event_date');
    const colorInput = document.getElementById('color');
    const colorTextInput = document.getElementById('colorText');
    
    // Set today's date as default
    eventDateInput.value = '<?= date('Y-m-d') ?>';
    
    // Update color text when color is changed
    colorInput.addEventListener('input', (e) => {
      colorTextInput.value = e.target.value;
    });
    
    // Toggle time selection based on all-day checkbox
    allDayCheckbox.addEventListener('change', () => {
      timeSelectionGroup.classList.toggle('hidden', allDayCheckbox.checked);
      
      // Clear time values when all-day is checked to avoid sending empty strings
      if (allDayCheckbox.checked) {
        document.getElementById('start_time').value = '';
        document.getElementById('end_time').value = '';
      }
    });
    
    // Open modal with default values
    openModalBtn.addEventListener('click', () => {
      document.getElementById('modalTitle').textContent = 'Neuer Termin';
      eventForm.reset();
      eventDateInput.value = '<?= date('Y-m-d') ?>';
      colorInput.value = '#4A90E2';
      colorTextInput.value = '#4A90E2';
      toggleAssignmentType('none');
      
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    });
    
    // Open modal for specific date (from day cells)
    document.querySelectorAll('.add-event-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const date = btn.dataset.date;
        document.getElementById('modalTitle').textContent = 'Neuer Termin';
        eventForm.reset();
        eventDateInput.value = date;
        colorInput.value = '#4A90E2';
        colorTextInput.value = '#4A90E2';
        toggleAssignmentType('none');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
      });
    });
    
    // Close modal
    [closeModalBtn, cancelBtn].forEach(btn => {
      btn.addEventListener('click', () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      });
    });
    
    // Assignment type toggle
    function toggleAssignmentType(type) {
      document.getElementById('user_assignment').classList.toggle('hidden', type !== 'user');
      document.getElementById('group_assignment').classList.toggle('hidden', type !== 'group');
      
      // Update required attributes
      const userSelect = document.getElementById('assigned_to');
      const groupSelect = document.getElementById('assigned_group_id');
      
      userSelect.required = (type === 'user');
      groupSelect.required = (type === 'group');
    }
    
    // Show event details in day view
    function showEventDetails(event) {
      const detailEl = document.getElementById('dayViewDetail');
      const placeholderEl = document.getElementById('dayViewPlaceholder');
      
      document.getElementById('detailTitle').textContent = event.title;
      
      let timeText = '';
      if (event.all_day == 1) {
        timeText = 'Ganztägig';
      } else if (event.start_time) {
        timeText = `${event.start_time.substring(0, 5)}`;
        if (event.end_time) {
          timeText += ` - ${event.end_time.substring(0, 5)}`;
        }
      }
      
      if (event.location) {
        timeText += ` | ${event.location}`;
      }
      
      document.getElementById('detailTime').textContent = timeText;
      
      document.getElementById('detailDescription').textContent = event.description || 'Keine Beschreibung vorhanden.';
      
      let assignmentText = '';
      if (event.assigned_to) {
        assignmentText = `<div class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full inline-block">Zugewiesen an: ${event.assignee_name}</div>`;
      } else if (event.assigned_group_id) {
        assignmentText = `<div class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full inline-block">Gruppe: ${event.group_name}</div>`;
      } else {
        assignmentText = '<div class="text-gray-500">Keine Zuweisung</div>';
      }
      document.getElementById('detailAssignment').innerHTML = assignmentText;
      
      // Setup edit/delete buttons
      document.getElementById('editEventBtn').onclick = () => {
        // Redirect to edit page
        window.location.href = `calendar_edit.php?id=${event.id}`;
      };
      
      document.getElementById('deleteEventBtn').onclick = () => {
        if (confirm('Sind Sie sicher, dass Sie diesen Termin löschen möchten?')) {
          window.location.href = `calendar_delete.php?id=${event.id}&view=<?= $view ?>&year=<?= $year ?>&month=<?= $month ?>&day=<?= $day ?>`;
        }
      };
      
      detailEl.classList.remove('hidden');
      placeholderEl.classList.add('hidden');
    }
    
    // Close modal when clicking outside
    modal.addEventListener('click', e => {
      if (e.target === modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }
    });
  </script>
</body>
</html>
