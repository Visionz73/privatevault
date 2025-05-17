<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Kalender | Private Vault</title>
  <!-- TailwindCSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- FullCalendar CSS -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.5/main.min.css" rel="stylesheet" />
  <style>
    body { font-family: 'Inter', sans-serif; }
    /* FullCalendar Anpassungen */
    .fc { background-color: white; border-radius: 0.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .fc-toolbar-title { font-size: 1.25rem; font-weight: 600; }
    .fc-button { background-color: transparent; border: 1px solid #e5e7eb; color: #374151; }
    .fc-button:hover { background-color: #f3f4f6; }
  </style>
</head>
<body class="p-8 bg-gradient-to-br from-[#eef7ff] via-[#f7fbff] to-[#f9fdf2]">
  <header class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Kalender</h1>
  </header>
  
  <!-- Kalender Container -->
  <div id="calendar" class="mb-8"></div>
  
  <!-- Liste der erstellten Termine -->
  <section class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow mb-8">
    <h2 class="text-2xl font-semibold mb-4">Meine Termine</h2>
    <ul id="eventList" class="divide-y divide-gray-200">
      <li id="noEvents" class="py-2 text-gray-500 text-center">Keine Ereignisse gefunden.</li>
    </ul>
  </section>
  
  <!-- Event-Planungsformular -->
  <section class="max-w-md mx-auto bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-semibold mb-4">Neues Event hinzufügen</h2>
    <form id="eventForm" class="space-y-4">
      <input type="text" name="title" placeholder="Event Titel" class="w-full border border-gray-300 rounded p-2" required>
      <input type="date" name="date" class="w-full border border-gray-300 rounded p-2" required>
      <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600">Hinzufügen</button>
    </form>
  </section>
  
  <!-- FullCalendar JS -->
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.5/main.global.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Array zur Speicherung der Termine
      let myEvents = [];
      
      // Kalender initialisieren
      var calendarEl = document.getElementById('calendar');
      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        selectable: true,
        editable: true,
        events: myEvents
      });
      calendar.render();
      
      // Funktion zum Aktualisieren der Event-Liste unter dem Kalender
      function updateEventList() {
        const eventList = document.getElementById('eventList');
        eventList.innerHTML = '';
        if (myEvents.length === 0) {
          eventList.innerHTML = '<li class="py-2 text-gray-500 text-center">Keine Ereignisse gefunden.</li>';
          return;
        }
        myEvents.forEach(function(event, index) {
          const li = document.createElement('li');
          li.className = "py-2 flex justify-between items-center";
          li.innerHTML = `<span class="font-medium">${event.title}</span>
                          <span class="text-sm text-gray-500">${new Date(event.start).toLocaleDateString('de-DE')}</span>`;
          eventList.appendChild(li);
        });
      }
      
      // Event-Formular-Handler
      document.getElementById('eventForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const title = this.title.value.trim();
        const date = this.date.value;
        if(title && date){
          const newEvent = {
            title: title,
            start: date,
            allDay: true
          };
          // Event zum Kalender hinzufügen
          calendar.addEvent(newEvent);
          // Event in unser Array speichern und Liste aktualisieren
          myEvents.push(newEvent);
          updateEventList();
          this.reset();
        }
      });
    });
  </script>
</body>
</html>
