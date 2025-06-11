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
      min-height: 320px;
      position: relative;
      overflow: hidden;
    }
    .widget-card:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(255, 255, 255, 0.2);
      transform: translateY(-2px);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
    }

    /* Draggable widget styling */
    .widget-slot {
      min-height: 320px;
      border: 2px dashed rgba(255, 255, 255, 0.1);
      border-radius: 1.5rem;
      transition: all 0.3s ease;
    }
    
    .widget-slot.drag-over {
      border-color: rgba(147, 51, 234, 0.5);
      background: rgba(147, 51, 234, 0.1);
      transform: scale(1.02);
    }
    
    .widget-dragging {
      opacity: 0.5;
      transform: rotate(5deg);
    }

    /* Enhanced scrollable widget containers */
    .widget-scroll-container {
      position: relative;
      overflow: hidden;
      max-height: 220px;
    }
    
    .widget-scroll-content {
      overflow-y: auto;
      scrollbar-width: none;
      -ms-overflow-style: none;
      max-height: 220px;
      padding-right: 4px;
    }
    
    .widget-scroll-content::-webkit-scrollbar {
      display: none;
    }
    
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

    /* Widget controls */
    .widget-controls {
      position: absolute;
      top: 0.75rem;
      right: 0.75rem;
      opacity: 0;
      transition: all 0.3s ease;
      z-index: 10;
    }
    
    .widget-card:hover .widget-controls {
      opacity: 1;
    }
    
    .widget-control-btn {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      border-radius: 0.375rem;
      padding: 0.25rem;
      transition: all 0.3s ease;
      cursor: pointer;
    }
    
    .widget-control-btn:hover {
      background: rgba(255, 255, 255, 0.2);
      transform: scale(1.1);
    }

    /* Placeholder styling */
    .placeholder-widget {
      background: rgba(255, 255, 255, 0.03);
      backdrop-filter: blur(10px);
      border: 2px dashed rgba(255, 255, 255, 0.1);
      border-radius: 1.5rem;
      color: rgba(255, 255, 255, 0.4);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 3rem;
      font-size: 0.875rem;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
      min-height: 320px;
    }
    
    .placeholder-widget:hover {
      border-color: rgba(255, 255, 255, 0.2);
      background: rgba(255, 255, 255, 0.05);
      color: rgba(255, 255, 255, 0.6);
    }

    /* Widget customization panel */
    .customization-panel {
      position: fixed;
      top: 0;
      right: -400px;
      width: 400px;
      height: 100vh;
      background: linear-gradient(135deg, #2d1b69 0%, #11101d 100%);
      backdrop-filter: blur(20px);
      border-left: 1px solid rgba(255, 255, 255, 0.15);
      box-shadow: -8px 0 32px rgba(0, 0, 0, 0.3);
      transition: right 0.3s ease;
      z-index: 1000;
      overflow-y: auto;
    }
    
    .customization-panel.open {
      right: 0;
    }
    
    .customization-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(5px);
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
      z-index: 999;
    }
    
    .customization-overlay.open {
      opacity: 1;
      visibility: visible;
    }

    /* Available widgets list */
    .available-widget {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 0.75rem;
      padding: 1rem;
      margin-bottom: 0.75rem;
      cursor: grab;
      transition: all 0.3s ease;
    }
    
    .available-widget:hover {
      background: rgba(255, 255, 255, 0.12);
      transform: translateY(-2px);
    }
    
    .available-widget:active {
      cursor: grabbing;
    }

    /* 4-column grid for desktop */
    .dashboard-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1.5rem;
      auto-rows: min-content;
    }
    
    @media (max-width: 1200px) {
      .dashboard-grid {
        grid-template-columns: repeat(3, 1fr);
      }
    }
    
    @media (max-width: 768px) {
      .dashboard-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
      }
    }
    
    @media (max-width: 480px) {
      .dashboard-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body class="min-h-screen flex flex-col">

  <?php require_once __DIR__.'/navbar.php'; ?>

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
    <div class="flex justify-between items-center">
      <h1 class="text-3xl font-bold greeting-text leading-tight">
        <?= $formattedDate ?><br>
        Guten <?= date('H')<12?'Morgen':(date('H')<18?'Tag':'Abend') ?>,
        <?= htmlspecialchars($user['first_name']??$user['username']) ?>
      </h1>
      
      <button id="customizeBtn" class="px-6 py-3 btn-primary rounded-lg text-sm font-medium">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        Anpassen
      </button>
    </div>

    <!-- Dashboard Grid -------------------------------------------------->
    <div class="dashboard-grid" id="dashboardGrid">
      <?php for ($i = 1; $i <= 16; $i++): ?>
        <?php $slot = $widgetSlots[$i]; ?>
        <div class="widget-slot" 
             data-slot="<?= $i ?>" 
             ondrop="dropWidget(event)" 
             ondragover="allowDrop(event)"
             ondragenter="dragEnter(event)"
             ondragleave="dragLeave(event)">
          
          <?php if ($slot['widget_type'] === 'placeholder'): ?>
            <!-- Placeholder Widget -->
            <div class="placeholder-widget" onclick="openCustomizationPanel()">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4v16m8-8H4" />
              </svg>
              <p>Widget hinzufügen</p>
              <p class="text-xs mt-2 opacity-75">Klicken zum Anpassen</p>
            </div>
          <?php else: ?>
            <!-- Render Widget -->
            <div class="widget-card" draggable="true" ondragstart="dragStart(event)" data-widget-type="<?= $slot['widget_type'] ?>">
              <!-- Widget Controls -->
              <div class="widget-controls flex gap-2">
                <button class="widget-control-btn" onclick="removeWidget(<?= $i ?>)" title="Widget entfernen">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
              
              <!-- Widget Content -->
              <?php
              switch ($slot['widget_type']) {
                case 'inbox':
                  include __DIR__ . '/widgets/inbox_widget.php';
                  break;
                case 'documents':
                  include __DIR__ . '/widgets/documents_widget.php';
                  break;
                case 'calendar':
                  include __DIR__ . '/widgets/calendar_widget.php';
                  break;
                case 'havetopay':
                  include __DIR__ . '/widgets/havetopay_widget.php';
                  break;
              }
              ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endfor; ?>
    </div>
  </main>

  <!-- Customization Panel --------------------------------------------->
  <div class="customization-overlay" id="customizationOverlay" onclick="closeCustomizationPanel()"></div>
  <div class="customization-panel" id="customizationPanel">
    <div class="p-6">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-white">Dashboard anpassen</h2>
        <button class="widget-control-btn" onclick="closeCustomizationPanel()">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <div class="space-y-6">
        <div>
          <h3 class="text-lg font-semibold text-white mb-4">Verfügbare Widgets</h3>
          <div class="space-y-3">
            <?php foreach ($availableWidgets as $type => $info): ?>
              <?php if ($type !== 'placeholder'): ?>
                <div class="available-widget" 
                     draggable="true" 
                     ondragstart="dragStartFromPanel(event, '<?= $type ?>')"
                     data-widget-type="<?= $type ?>">
                  <div class="flex items-center">
                    <div class="p-2 rounded-lg bg-purple-500/20 text-purple-300 mr-3">
                      <?php
                      $icons = [
                        'inbox' => '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>',
                        'folder' => '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>',
                        'calendar' => '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
                        'money' => '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V4m0 12v4"/></svg>'
                      ];
                      echo $icons[$info['icon']] ?? $icons['inbox'];
                      ?>
                    </div>
                    <div>
                      <div class="text-white font-medium"><?= htmlspecialchars($info['name']) ?></div>
                      <div class="text-xs text-gray-400">Zum Dashboard ziehen</div>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </div>
        
        <div class="border-t border-white/10 pt-6">
          <button onclick="resetDashboard()" class="w-full px-4 py-3 bg-red-500/20 text-red-300 border border-red-500/30 rounded-lg hover:bg-red-500/30 transition">
            Dashboard zurücksetzen
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Dashboard customization
    let draggedWidget = null;
    let draggedFromPanel = false;
    
    function openCustomizationPanel() {
      document.getElementById('customizationPanel').classList.add('open');
      document.getElementById('customizationOverlay').classList.add('open');
    }
    
    function closeCustomizationPanel() {
      document.getElementById('customizationPanel').classList.remove('open');
      document.getElementById('customizationOverlay').classList.remove('open');
    }
    
    document.getElementById('customizeBtn').addEventListener('click', openCustomizationPanel);
    
    // Drag and Drop functionality
    function dragStart(event) {
      draggedWidget = event.target.dataset.widgetType;
      draggedFromPanel = false;
      event.target.classList.add('widget-dragging');
    }
    
    function dragStartFromPanel(event, widgetType) {
      draggedWidget = widgetType;
      draggedFromPanel = true;
      event.dataTransfer.setData('text/plain', widgetType);
    }
    
    function allowDrop(event) {
      event.preventDefault();
    }
    
    function dragEnter(event) {
      event.preventDefault();
      if (draggedWidget) {
        event.currentTarget.classList.add('drag-over');
      }
    }
    
    function dragLeave(event) {
      event.currentTarget.classList.remove('drag-over');
    }
    
    function dropWidget(event) {
      event.preventDefault();
      event.currentTarget.classList.remove('drag-over');
      
      if (draggedWidget) {
        const slotPosition = event.currentTarget.dataset.slot;
        updateWidgetPosition(slotPosition, draggedWidget);
      }
      
      // Clean up dragging styles
      document.querySelectorAll('.widget-dragging').forEach(el => {
        el.classList.remove('widget-dragging');
      });
      
      draggedWidget = null;
      draggedFromPanel = false;
    }
    
    function updateWidgetPosition(slotPosition, widgetType) {
      fetch('/src/controllers/widget_management.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
          action: 'update_widget_position',
          slot_position: slotPosition,
          widget_type: widgetType
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          location.reload();
        } else {
          alert('Fehler beim Aktualisieren des Widgets: ' + (data.error || 'Unbekannter Fehler'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Fehler beim Aktualisieren des Widgets');
      });
    }
    
    function removeWidget(slotPosition) {
      if (confirm('Widget entfernen?')) {
        fetch('/src/controllers/widget_management.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: new URLSearchParams({
            action: 'remove_widget',
            slot_position: slotPosition
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            location.reload();
          } else {
            alert('Fehler beim Entfernen des Widgets: ' + (data.error || 'Unbekannter Fehler'));
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Fehler beim Entfernen des Widgets');
        });
      }
    }
    
    function resetDashboard() {
      if (confirm('Dashboard auf Standardlayout zurücksetzen? Alle Anpassungen gehen verloren.')) {
        fetch('/src/controllers/widget_management.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: new URLSearchParams({
            action: 'reset_dashboard'
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            location.reload();
          } else {
            alert('Fehler beim Zurücksetzen: ' + (data.error || 'Unbekannter Fehler'));
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Fehler beim Zurücksetzen');
        });
      }
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
          
          checkScroll();
          window.addEventListener('resize', checkScroll);
          
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
    
    document.addEventListener('DOMContentLoaded', () => {
      initScrollIndicators();
    });
  </script>
</body>
</html>
