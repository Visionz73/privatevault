<div class="space-y-8">
  <div>
    <h2 class="text-xl font-semibold text-gray-900 mb-2">Benachrichtigungseinstellungen</h2>
    <p class="text-gray-600">Verwalten Sie Ihre E-Mail- und Push-Benachrichtigungen.</p>
  </div>

  <form method="POST" action="/src/controllers/profile_notifications.php" class="space-y-8">
    <!-- E-Mail Benachrichtigungen -->
    <div class="bg-white border border-gray-200 rounded-lg p-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">E-Mail Benachrichtigungen</h3>
      <div class="space-y-4">
        <label class="flex items-center">
          <input type="checkbox" name="email_finance" class="rounded border-gray-300 text-[#4A90E2] focus:ring-[#4A90E2]">
          <span class="ml-3 text-gray-700">Finanz-Updates</span>
        </label>
        <label class="flex items-center">
          <input type="checkbox" name="email_documents" class="rounded border-gray-300 text-[#4A90E2] focus:ring-[#4A90E2]">
          <span class="ml-3 text-gray-700">Dokument-Uploads</span>
        </label>
        <label class="flex items-center">
          <input type="checkbox" name="email_security" class="rounded border-gray-300 text-[#4A90E2] focus:ring-[#4A90E2]">
          <span class="ml-3 text-gray-700">Sicherheitswarnungen</span>
        </label>
      </div>
    </div>

    <!-- Push Benachrichtigungen -->
    <div class="bg-white border border-gray-200 rounded-lg p-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Push-Benachrichtigungen</h3>
      <div class="space-y-4">
        <label class="flex items-center">
          <input type="checkbox" name="push_reminders" class="rounded border-gray-300 text-[#4A90E2] focus:ring-[#4A90E2]">
          <span class="ml-3 text-gray-700">Erinnerungen</span>
        </label>
        <label class="flex items-center">
          <input type="checkbox" name="push_security" class="rounded border-gray-300 text-[#4A90E2] focus:ring-[#4A90E2]">
          <span class="ml-3 text-gray-700">Sicherheitswarnungen</span>
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
