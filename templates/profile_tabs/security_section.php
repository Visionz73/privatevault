<div class="space-y-8">
  <!-- Section Header -->
  <div class="mb-8">
    <h2 class="text-2xl font-bold text-primary mb-2">Sicherheitseinstellungen</h2>
    <p class="text-secondary">Verwalten Sie Ihr Passwort und Sicherheitsoptionen</p>
  </div>

  <!-- Messages -->
  <?php if (isset($_SESSION['success'])): ?>
    <div class="glass-alert-success p-4">
      <div class="flex items-center gap-3">
        <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <span><?= htmlspecialchars($_SESSION['success']) ?></span>
      </div>
    </div>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['error'])): ?>
    <div class="glass-alert-error p-4">
      <div class="flex items-center gap-3">
        <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <span><?= htmlspecialchars($_SESSION['error']) ?></span>
      </div>
    </div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <!-- Password Change -->
  <div class="glass-card p-8">
    <div class="mb-6">
      <h3 class="text-xl font-semibold text-primary mb-2">Passwort ändern</h3>
      <p class="text-muted text-sm">Aktualisieren Sie Ihr Passwort für mehr Sicherheit</p>
    </div>
    
    <form method="POST" action="/src/controllers/profile_security.php" class="space-y-6">
      <input type="hidden" name="action" value="change_password">
      
      <div>
        <label class="block mb-3">Aktuelles Passwort *</label>
        <input 
          type="password" 
          name="current_password" 
          required 
          class="glass-input w-full px-4 py-3"
          placeholder="Ihr aktuelles Passwort"
        >
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block mb-3">Neues Passwort *</label>
          <input 
            type="password" 
            name="new_password" 
            minlength="8" 
            required 
            class="glass-input w-full px-4 py-3"
            placeholder="Mindestens 8 Zeichen"
          >
        </div>
        <div>
          <label class="block mb-3">Passwort bestätigen *</label>
          <input 
            type="password" 
            name="confirm_password" 
            minlength="8" 
            required 
            class="glass-input w-full px-4 py-3"
            placeholder="Passwort wiederholen"
          >
        </div>
      </div>
      
      <div class="pt-6">
        <button type="submit" class="glass-btn-primary px-8 py-3 font-medium">
          Passwort ändern
        </button>
      </div>
    </form>
  </div>

  <!-- Security Status -->
  <div class="glass-card p-8">
    <div class="mb-6">
      <h3 class="text-xl font-semibold text-primary mb-2">Sicherheitsstatus</h3>
      <p class="text-muted text-sm">Übersicht über Ihre Sicherheitseinstellungen</p>
    </div>
    
    <div class="space-y-4">
      <!-- Password Strength -->
      <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/10">
        <div class="flex items-center gap-3">
          <div class="p-2 bg-green-500/20 rounded-lg">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
          </div>
          <div>
            <h4 class="font-medium text-white">Passwort</h4>
            <p class="text-sm text-muted">Zuletzt geändert: <?= date('d.m.Y', strtotime($user['updated_at'] ?? 'now')) ?></p>
          </div>
        </div>
        <span class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-sm border border-green-500/30">
          Sicher
        </span>
      </div>

      <!-- Two-Factor Authentication -->
      <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/10">
        <div class="flex items-center gap-3">
          <div class="p-2 bg-gray-500/20 rounded-lg">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
          </div>
          <div>
            <h4 class="font-medium text-white">Zwei-Faktor-Authentifizierung</h4>
            <p class="text-sm text-muted">Zusätzliche Sicherheitsebene für Ihr Konto</p>
          </div>
        </div>
        <span class="px-3 py-1 bg-gray-500/20 text-gray-400 rounded-full text-sm border border-gray-500/30">
          Nicht aktiviert
        </span>
      </div>

      <!-- Session Management -->
      <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/10">
        <div class="flex items-center gap-3">
          <div class="p-2 bg-blue-500/20 rounded-lg">
            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
          </div>
          <div>
            <h4 class="font-medium text-white">Aktive Sitzungen</h4>
            <p class="text-sm text-muted">Aktuell angemeldet: <?= date('d.m.Y H:i') ?></p>
          </div>
        </div>
        <span class="px-3 py-1 bg-blue-500/20 text-blue-400 rounded-full text-sm border border-blue-500/30">
          1 Sitzung
        </span>
      </div>
    </div>
  </div>

  <!-- Account Security -->
  <div class="glass-card p-8">
    <div class="mb-6">
      <h3 class="text-xl font-semibold text-primary mb-2">Konto-Sicherheit</h3>
      <p class="text-muted text-sm">Erweiterte Sicherheitsoptionen</p>
    </div>
    
    <div class="space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h4 class="font-medium text-white">Login-Benachrichtigungen</h4>
          <p class="text-sm text-muted">E-Mail bei neuen Anmeldungen erhalten</p>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
          <input type="checkbox" class="sr-only peer" checked>
          <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
        </label>
      </div>
      
      <div class="flex items-center justify-between">
        <div>
          <h4 class="font-medium text-white">Automatische Abmeldung</h4>
          <p class="text-sm text-muted">Nach 30 Minuten Inaktivität</p>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
          <input type="checkbox" class="sr-only peer" checked>
          <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
        </label>
      </div>
    </div>
  </div>
</div>
