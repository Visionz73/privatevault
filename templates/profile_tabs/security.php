<div class="space-y-8">
  <div>
    <h2 class="text-xl font-semibold text-gray-900 mb-2">Sicherheitseinstellungen</h2>
    <p class="text-gray-600">Verwalten Sie Ihr Passwort und Sicherheitsoptionen.</p>
  </div>

  <!-- Session Messages -->
  <?php if (isset($_SESSION['success'])): ?>
    <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-300" role="alert">
      <?= htmlspecialchars($_SESSION['success']) ?>
    </div>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>
  <?php if (isset($_SESSION['error'])): ?>
    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg border border-red-300" role="alert">
      <?= htmlspecialchars($_SESSION['error']) ?>
    </div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <!-- Change Password -->
  <div class="bg-white border border-gray-200 rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Passwort ändern</h3>
    <form method="POST" action="/privatevault/src/controllers/profile_security.php" class="space-y-4">
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
      <span class="px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-800">Nicht implementiert</span>
    </div>
    
    <button class="mt-4 px-6 py-2 bg-gray-400 text-white rounded-lg cursor-not-allowed" disabled>
      2FA konfigurieren (Nicht verfügbar)
    </button>
  </div>

  <!-- Active Sessions -->
  <div class="bg-white border border-gray-200 rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Aktive Sessions (Beispiel)</h3>
    <p class="text-gray-600 mb-4 text-sm">Diese Funktion ist derzeit nicht vollständig implementiert.</p>
    <div class="space-y-3">
      <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
        <div>
          <p class="font-medium text-gray-900">Beispiel Session</p>
          <p class="text-sm text-gray-600">Beispiel Browser auf Beispiel OS • <?= date('d.m.Y H:i') ?></p>
        </div>
        <span class="px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-800">Platzhalter</span>
      </div>
    </div>
    
    <button class="mt-4 px-6 py-2 bg-gray-400 text-white rounded-lg cursor-not-allowed" disabled>
      Andere Sessions beenden (Nicht verfügbar)
    </button>
  </div>
</div>
