<div class="space-y-8">
  <!-- Page Header -->
  <div class="border-b border-gray-200 pb-6">
    <h2 class="text-2xl font-bold text-gray-900">Benachrichtigungseinstellungen</h2>
    <p class="mt-2 text-gray-600">Wählen Sie aus, wie Sie benachrichtigt werden möchten</p>
  </div>

  <!-- Email Notifications -->
  <div class="bg-white rounded-lg border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">E-Mail Benachrichtigungen</h3>
    <div class="space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <p class="font-medium text-gray-900">Neue Aufgaben</p>
          <p class="text-sm text-gray-500">Benachrichtigung bei neuen zugewiesenen Aufgaben</p>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
          <input type="checkbox" checked class="sr-only peer">
          <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#4A90E2]/25 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#4A90E2]"></div>
        </label>
      </div>
      
      <div class="flex items-center justify-between">
        <div>
          <p class="font-medium text-gray-900">Fällige Rechnungen</p>
          <p class="text-sm text-gray-500">Erinnerung an bevorstehende Zahlungen</p>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
          <input type="checkbox" checked class="sr-only peer">
          <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#4A90E2]/25 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#4A90E2]"></div>
        </label>
      </div>

      <div class="flex items-center justify-between">
        <div>
          <p class="font-medium text-gray-900">Wöchentlicher Report</p>
          <p class="text-sm text-gray-500">Zusammenfassung Ihrer Aktivitäten</p>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
          <input type="checkbox" class="sr-only peer">
          <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#4A90E2]/25 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#4A90E2]"></div>
        </label>
      </div>
    </div>
  </div>

  <!-- Push Notifications -->
  <div class="bg-white rounded-lg border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Browser Benachrichtigungen</h3>
    <div class="space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <p class="font-medium text-gray-900">Desktop Benachrichtigungen</p>
          <p class="text-sm text-gray-500">Push-Benachrichtigungen im Browser</p>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
          <input type="checkbox" class="sr-only peer">
          <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#4A90E2]/25 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#4A90E2]"></div>
        </label>
      </div>
    </div>
  </div>

  <!-- Save Button -->
  <div class="flex justify-end">
    <button type="submit" 
            class="px-8 py-3 bg-[#4A90E2] text-white rounded-lg hover:bg-[#357abd] transition-colors font-medium">
      Einstellungen speichern
    </button>
  </div>
</div>
