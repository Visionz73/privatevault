<div class="space-y-8">
  <div>
    <h2 class="text-xl font-semibold text-gray-900 mb-2">Sicherheitseinstellungen</h2>
    <p class="text-gray-600">Verwalten Sie Ihr Passwort und Sicherheitsoptionen.</p>
  </div>

  <!-- Change Password -->
  <div class="bg-white border border-gray-200 rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Passwort ändern</h3>
    <form method="POST" action="/src/controllers/profile_security.php" class="space-y-4">
      <input type="hidden" name="action" value="change_password">
      
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Aktuelles Passwort</label>
        <input type="password" name="current_password" required
               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Neues Passwort</label>
        <input type="password" name="new_password" required
               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Passwort bestätigen</label>
        <input type="password" name="confirm_password" required
               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
      </div>

      <button type="submit" 
              class="px-6 py-2 bg-[#4A90E2] text-white rounded-lg hover:bg-[#357abd] transition-colors">
        Passwort ändern
      </button>
    </form>
  </div>

  <!-- Two-Factor Authentication -->
  <div class="bg-white border border-gray-200 rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Zwei-Faktor-Authentifizierung</h3>
    <p class="text-gray-600 mb-4">Erhöhen Sie die Sicherheit Ihres Accounts mit 2FA.</p>
    
    <div class="flex items-center justify-between">
      <span class="text-sm font-medium text-gray-700">2FA Status:</span>
      <span class="px-3 py-1 rounded-full text-sm bg-red-100 text-red-800">Deaktiviert</span>
    </div>
    
    <button class="mt-4 px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
      2FA aktivieren
    </button>
  </div>

  <!-- Active Sessions -->
  <div class="bg-white border border-gray-200 rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Aktive Sessions</h3>
    <div class="space-y-3">
      <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
        <div>
          <p class="font-medium text-gray-900">Aktuelle Session</p>
          <p class="text-sm text-gray-600">Chrome auf Windows • <?= date('d.m.Y H:i') ?></p>
        </div>
        <span class="px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">Aktiv</span>
      </div>
    </div>
    
    <button class="mt-4 px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
      Alle anderen Sessions beenden
    </button>
  </div>
</div>
