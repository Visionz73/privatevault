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
  <!-- Custom Styles für 1:1 Format -->
  <style>
    body { font-family: 'Inter', sans-serif; }
    /* Passe FullCalendar an Tailwind an */
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
        events: [] // Hier können zukünftig Events dynamisch geladen werden
      });
      calendar.render();

      // Einfaches Client-seitiges Event hinzufügen (ohne Persistenz)
      document.getElementById('eventForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const title = this.title.value;
        const date = this.date.value;
        if(title && date){
          calendar.addEvent({
            title: title,
            start: date,
            allDay: true
          });
          this.reset();
        }
      });
    });
  </script>
</body>
</html>
