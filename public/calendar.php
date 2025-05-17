<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Kalender | Private Vault</title>
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.5/main.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.5/main.global.min.js"></script>
  <!-- Optional: TailwindCSS -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-4">
  <h1 class="text-3xl font-bold mb-4">Kalender</h1>
  <!-- Kalender Container -->
  <div id="calendar"></div>
  
  <!-- Event-Planungsformular -->
  <div class="mt-8">
    <h2 class="text-xl font-semibold mb-2">Neues Event hinzufügen</h2>
    <form id="eventForm" class="space-y-2">
      <input type="text" name="title" placeholder="Event Titel" class="border p-2 rounded w-full" required>
      <input type="date" name="date" class="border p-2 rounded w-full" required>
      <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Hinzufügen</button>
    </form>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var calendarEl = document.getElementById('calendar');
      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        selectable: true,
        editable: true,
        events: [] // Hier sollten ggf. Events aus der DB geladen werden
      });
      calendar.render();

      // Einfaches Client-seitiges Hinzufügen eines Events (ohne Persistenz)
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
