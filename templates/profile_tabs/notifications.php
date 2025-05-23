<div class="space-y-8">
  <div>
    <h2 class="text-xl font-semibold text-gray-900 mb-2">Benachrichtigungen</h2>
    <p class="text-gray-600">Verwalten Sie Ihre E-Mail- und Push-Benachrichtigungen.</p>
  </div>

  <form method="POST" action="/src/controllers/profile_notifications.php" class="space-y-6">
    <!-- Email Notifications -->
    <div class="bg-white border border-gray-200 rounded-lg p-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">E-Mail Benachrichtigungen</h3>
      <div class="space-y-4">
        <label class="flex items-center">
          <input type="checkbox" name="email_finance" value="1" 
                 class="rounded border-gray-300 text-[#4A90E2] focus:ring-[#4A90E2]">
          <span class="ml-3 text-sm text-gray-700">Finanz-Updates (neue Einträge, Budgetüberschreitungen)</span>
        </label>
        
        <label class="flex items-center">
          <input type="checkbox" name="email_documents" value="1"
                 class="rounded border-gray-300 text-[#4A90E2] focus:ring-[#4A90E2]">
          <span class="ml-3 text-sm text-gray-700">Dokument-Updates (neue Uploads, Ablaufdaten)</span>
        </label>
        
        <label class="flex items-center">
          <input type="checkbox" name="email_security" value="1"
                 class="rounded border-gray-300 text-[#4A90E2] focus:ring-[#4A90E2]">
          <span class="ml-3 text-sm text-gray-700">Sicherheitswarnungen (Login-Versuche, Passwort-Änderungen)</span>
        </label>
        
        <label class="flex items-center">
          <input type="checkbox" name="email_newsletter" value="1"
                 class="rounded border-gray-300 text-[#4A90E2] focus:ring-[#4A90E2]">
          <span class="ml-3 text-sm text-gray-700">Newsletter und Updates</span>
        </label>
      </div>
    </div>

    <!-- Push Notifications -->
    <div class="bg-white border border-gray-200 rounded-lg p-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Push-Benachrichtigungen</h3>
      <div class="space-y-4">
        <label class="flex items-center">
          <input type="checkbox" name="push_finance" value="1"
                 class="rounded border-gray-300 text-[#4A90E2] focus:ring-[#4A90E2]">
          <span class="ml-3 text-sm text-gray-700">Wichtige Finanz-Alerts</span>
        </label>
        
        <label class="flex items-center">
          <input type="checkbox" name="push_reminders" value="1"
                 class="rounded border-gray-300 text-[#4A90E2] focus:ring-[#4A90E2]">
          <span class="ml-3 text-sm text-gray-700">Erinnerungen (Dokument-Ablauf, Termine)</span>
        </label>
        
        <label class="flex items-center">
          <input type="checkbox" name="push_security" value="1"
                 class="rounded border-gray-300 text-[#4A90E2] focus:ring-[#4A90E2]">
          <span class="ml-3 text-sm text-gray-700">Sofortige Sicherheitswarnungen</span>
        </label>
      </div>
    </div>

    <!-- Notification Frequency -->
    <div class="bg-white border border-gray-200 rounded-lg p-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Benachrichtigungsfrequenz</h3>
      <div class="space-y-3">
        <label class="flex items-center">
          <input type="radio" name="frequency" value="immediate" checked
                 class="border-gray-300 text-[#4A90E2] focus:ring-[#4A90E2]">
          <span class="ml-3 text-sm text-gray-700">Sofort</span>
        </label>
        
        <label class="flex items-center">
          <input type="radio" name="frequency" value="daily"
                 class="border-gray-300 text-[#4A90E2] focus:ring-[#4A90E2]">
          <span class="ml-3 text-sm text-gray-700">Tägliche Zusammenfassung</span>
        </label>
        
        <label class="flex items-center">
          <input type="radio" name="frequency" value="weekly"
                 class="border-gray-300 text-[#4A90E2] focus:ring-[#4A90E2]">
          <span class="ml-3 text-sm text-gray-700">Wöchentliche Zusammenfassung</span>
        </label>
      </div>
    </div>

    <div class="flex justify-end">
      <button type="submit" 
              class="px-6 py-2 bg-[#4A90E2] text-white rounded-lg hover:bg-[#357abd] transition-colors">
        Einstellungen speichern
      </button>
    </div>
  </form>
</div>
