<div class="space-y-8">
  <div>
    <h2 class="text-2xl font-semibold text-gray-800 border-b pb-4 mb-6">Sicherheitseinstellungen</h2>
    <p class="text-gray-600">Verwalten Sie Ihr Passwort und Sicherheitsoptionen.</p>
  </div>

  <?php // Session messages (success/error) are displayed by the main templates/profile.php ?>

  <!-- Change Password -->
  <div class="bg-white shadow-lg rounded-lg p-6 md:p-8">
    <h3 class="text-xl font-semibold text-gray-700 mb-4">Passwort ändern</h3>
    <form method="POST" action="profile.php?tab=security" class="space-y-4">
      <input type="hidden" name="action" value="change_password_profile">
      <input type="hidden" name="csrf_token_change_password_profile" value="<?php echo htmlspecialchars($csrf_token_change_password_profile ?? ''); // From controller ?>">
      
      <div>
        <label for="sec_current_password" class="block text-sm font-medium text-gray-700 mb-1">Aktuelles Passwort</label>
        <input type="password" name="current_password" id="sec_current_password" required
               class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
      </div>

      <div>
        <label for="sec_new_password" class="block text-sm font-medium text-gray-700 mb-1">Neues Passwort</label>
        <input type="password" name="new_password" id="sec_new_password" required
               class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
      </div>

      <div>
        <label for="sec_confirm_new_password" class="block text-sm font-medium text-gray-700 mb-1">Neues Passwort bestätigen</label>
        <input type="password" name="confirm_new_password" id="sec_confirm_new_password" required
               class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
      </div>

      <div class="flex justify-end pt-2">
          <button type="submit" 
                  class="inline-flex justify-center py-2 px-5 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Passwort ändern
          </button>
      </div>
    </form>
  </div>

  <!-- Two-Factor Authentication -->
  <div class="bg-white shadow-lg rounded-lg p-6 md:p-8 mt-8">
    <h3 class="text-xl font-semibold text-gray-700 mb-4">Zwei-Faktor-Authentifizierung</h3>
    <p class="text-gray-600 mb-4">Erhöhen Sie die Sicherheit Ihres Accounts mit 2FA.</p>
    
    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
      <span class="text-sm font-medium text-gray-700">2FA Status:</span>
      <span class="px-3 py-1 rounded-full text-sm bg-red-100 text-red-700 font-medium">Deaktiviert</span>
    </div>
    
    <button class="mt-4 inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
      <i class="fas fa-shield-alt mr-2"></i>2FA aktivieren
    </button>
  </div>

  <!-- Active Sessions (Placeholder) -->
  <div class="bg-white shadow-lg rounded-lg p-6 md:p-8 mt-8">
    <h3 class="text-xl font-semibold text-gray-700 mb-4">Aktive Sessions</h3>
    <div class="space-y-3">
      <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
        <div>
          <p class="font-medium text-gray-900">Aktuelle Session</p>
          <p class="text-sm text-gray-600">Chrome auf Windows • <?= date('d.m.Y H:i') ?></p>
        </div>
        <span class="px-3 py-1 rounded-full text-sm bg-green-100 text-green-700 font-medium">Aktiv</span>
      </div>
      <!-- Add more session listings here if applicable -->
    </div>
    
    <button class="mt-4 inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
      <i class="fas fa-sign-out-alt mr-2"></i>Alle anderen Sessions beenden
    </button>
  </div>
</div>
