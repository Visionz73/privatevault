<div class="space-y-8">
  <div>
    <h2 class="text-xl font-semibold text-primary mb-2">Sicherheitseinstellungen</h2>
    <p class="text-muted">Verwalten Sie Ihr Passwort und Sicherheitsoptionen.</p>
  </div>

  <!-- Session Messages -->
  <?php if (isset($_SESSION['success'])): ?>
    <div class="p-4 mb-4 text-sm text-green-300 bg-green-500/20 border border-green-500/30 rounded-lg backdrop-blur" role="alert">
      <?= htmlspecialchars($_SESSION['success']) ?>
    </div>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>
  <?php if (isset($_SESSION['error'])): ?>
    <div class="p-4 mb-4 text-sm text-red-300 bg-red-500/20 border border-red-500/30 rounded-lg backdrop-blur" role="alert">
      <?= htmlspecialchars($_SESSION['error']) ?>
    </div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <!-- Change Password -->
  <div class="glassmorphism-container p-6">
    <h3 class="text-lg font-medium text-primary mb-4">Passwort ändern</h3>
    <form method="POST" action="/src/controllers/profile_security.php" class="space-y-4">
      <input type="hidden" name="action" value="change_password">
      
      <div>
        <label class="block text-sm font-medium text-secondary mb-2">Aktuelles Passwort</label>
        <input type="password" name="current_password" required class="form-input w-full px-4 py-3">
      </div>

      <div class="row">
        <div class="col-md-6">
          <label for="new_password" class="form-label">Neues Passwort *</label>
          <input type="password" class="form-control form-input" id="new_password" name="new_password" minlength="8" required>
          <small class="form-text">Mindestens 8 Zeichen</small>
        </div>
        <div class="col-md-6">
          <label for="confirm_password" class="form-label">Passwort bestätigen *</label>
          <input type="password" class="form-control form-input" id="confirm_password" name="confirm_password" minlength="8" required>
        </div>
      </div>
      
      <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-primary">
          Passwort ändern
        </button>
      </div>
    </form>
  </div>

  <!-- Two-Factor Authentication -->
  <div class="glassmorphism-container p-6">
    <h3 class="text-lg font-medium text-primary mb-4">Zwei-Faktor-Authentifizierung</h3>
    <p class="text-muted mb-4">Erhöhen Sie die Sicherheit Ihres Accounts mit 2FA.</p>
    
    <div class="flex items-center justify-between">
      <span class="text-sm font-medium text-secondary">2FA Status:</span>
      <span class="px-3 py-1 rounded-full text-sm bg-gray-500/20 text-gray-300 border border-gray-500/30">Nicht implementiert</span>
    </div>
    
    <button class="mt-4 px-6 py-2 bg-gray-600/50 text-gray-300 rounded-lg cursor-not-allowed border border-gray-600/30" disabled>
      2FA konfigurieren (Nicht verfügbar)
    </button>
  </div>

  <!-- Active Sessions -->
  <div class="glassmorphism-container p-6">
    <h3 class="text-lg font-medium text-primary mb-4">Aktive Sessions (Beispiel)</h3>
    <p class="text-muted mb-4 text-sm">Diese Funktion ist derzeit nicht vollständig implementiert.</p>
    <div class="space-y-3">
      <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg border border-white/10">
        <div>
          <p class="font-medium text-secondary">Beispiel Session</p>
          <p class="text-sm text-muted">Beispiel Browser auf Beispiel OS • <?= date('d.m.Y H:i') ?></p>
        </div>
        <span class="px-3 py-1 rounded-full text-sm bg-gray-500/20 text-gray-300 border border-gray-500/30">Platzhalter</span>
      </div>
    </div>
    
    <button class="mt-4 px-6 py-2 bg-gray-600/50 text-gray-300 rounded-lg cursor-not-allowed border border-gray-600/30" disabled>
      Andere Sessions beenden (Nicht verfügbar)
    </button>
  </div>
</div>
