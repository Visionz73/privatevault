<div class="space-y-8">
  <!-- Page Header -->
  <div class="border-b border-gray-200 pb-6">
    <h2 class="text-2xl font-bold text-gray-900">Sicherheitseinstellungen</h2>
    <p class="mt-2 text-gray-600">Verwalten Sie Ihr Passwort und Ihre Sicherheitsoptionen</p>
  </div>

  <!-- Password Section -->
  <div class="bg-white rounded-lg border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Passwort ändern</h3>
    <form class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Aktuelles Passwort</label>
        <input type="password" name="current_password" 
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Neues Passwort</label>
        <input type="password" name="new_password" 
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Passwort bestätigen</label>
        <input type="password" name="confirm_password" 
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
      </div>
      <button type="submit" 
              class="px-6 py-2 bg-[#4A90E2] text-white rounded-lg hover:bg-[#357abd] transition-colors">
        Passwort aktualisieren
      </button>
    </form>
  </div>

  <!-- Two-Factor Authentication -->
  <div class="bg-white rounded-lg border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-semibold text-gray-900">Zwei-Faktor-Authentifizierung</h3>
      <span class="px-3 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Deaktiviert</span>
    </div>
    <p class="text-gray-600 mb-4">Erhöhen Sie die Sicherheit Ihres Accounts durch 2FA.</p>
    <button class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
      2FA aktivieren
    </button>
  </div>

  <!-- Login Sessions -->
  <div class="bg-white rounded-lg border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Aktive Sitzungen</h3>
    <div class="space-y-4">
      <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
        <div>
          <p class="font-medium text-gray-900">Chrome auf Windows</p>
          <p class="text-sm text-gray-500">IP: 192.168.1.100 • Aktuelle Sitzung</p>
        </div>
        <span class="px-3 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Aktiv</span>
      </div>
    </div>
  </div>
</div>
