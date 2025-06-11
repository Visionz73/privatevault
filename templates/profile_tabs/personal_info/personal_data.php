<!-- templates/profile_tabs/personal_info/personal_data.php -->
<?php /* Erfolgs‑ und Fehlermeldungen kommen vom Controller, z. B. $success / $errors */ ?>
<div class="glassmorphism-container p-6 space-y-8">
  <h2 class="text-xl font-semibold text-primary">Persönliche Daten</h2>

  <div class="space-y-8">
    <!-- Success/Error Messages -->
    <?php if (!empty($success)): ?>
      <div class="glass-alert-success p-4">
        <div class="flex items-center gap-3">
          <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          <span><?= htmlspecialchars($success) ?></span>
        </div>
      </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
      <div class="glass-alert-error p-4">
        <div class="flex items-start gap-3">
          <svg class="w-5 h-5 text-red-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
          </svg>
          <div>
            <?php foreach ($errors as $error): ?>
              <p class="text-sm"><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- Personal Data Form -->
    <div class="glass-card p-8">
      <div class="mb-6">
        <h3 class="text-xl font-semibold text-primary mb-2">Grunddaten</h3>
        <p class="text-muted text-sm">Ihre persönlichen Informationen</p>
      </div>
      
      <form method="post" action="" class="space-y-6">
        <input type="hidden" name="form_marker" value="personal_data_update">
        
        <!-- Name Fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block mb-3">Vorname *</label>
            <input 
              type="text" 
              name="first_name" 
              value="<?= htmlspecialchars($user['first_name'] ?? '') ?>"
              class="glass-input w-full px-4 py-3"
              required
            >
          </div>
          <div>
            <label class="block mb-3">Nachname *</label>
            <input 
              type="text" 
              name="last_name" 
              value="<?= htmlspecialchars($user['last_name'] ?? '') ?>"
              class="glass-input w-full px-4 py-3"
              required
            >
          </div>
        </div>
        
        <!-- Additional Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block mb-3">Geburtsdatum</label>
            <input 
              type="date" 
              name="birthdate" 
              value="<?= htmlspecialchars($user['birthdate'] ?? '') ?>"
              class="glass-input w-full px-4 py-3"
            >
          </div>
          <div>
            <label class="block mb-3">Nationalität</label>
            <input 
              type="text" 
              name="nationality" 
              value="<?= htmlspecialchars($user['nationality'] ?? '') ?>"
              class="glass-input w-full px-4 py-3"
              placeholder="z.B. Deutsch"
            >
          </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block mb-3">Beruf</label>
            <input 
              type="text" 
              name="job_title" 
              value="<?= htmlspecialchars($user['job_title'] ?? '') ?>"
              class="glass-input w-full px-4 py-3"
              placeholder="Ihr Beruf"
            >
          </div>
          <div>
            <label class="block mb-3">Standort</label>
            <input 
              type="text" 
              name="location" 
              value="<?= htmlspecialchars($user['location'] ?? '') ?>"
              class="glass-input w-full px-4 py-3"
              placeholder="Stadt, Land"
            >
          </div>
        </div>
        
        <div class="pt-6">
          <button type="submit" class="glass-btn-primary px-8 py-3 font-medium">
            Änderungen speichern
          </button>
        </div>
      </form>
    </div>
    
    <!-- Address Section -->
    <div class="glass-card p-8">
      <div class="mb-6">
        <h3 class="text-xl font-semibold text-primary mb-2">Adresse</h3>
        <p class="text-muted text-sm">Ihre Kontaktdaten</p>
      </div>
      
      <form method="post" action="" class="space-y-6">
        <input type="hidden" name="form_marker" value="personal_data_update">
        
        <div>
          <label class="block mb-3">Straße & Hausnummer</label>
          <input 
            type="text" 
            name="street" 
            value="<?= htmlspecialchars($user['street'] ?? '') ?>"
            class="glass-input w-full px-4 py-3"
            placeholder="Musterstraße 123"
          >
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <label class="block mb-3">PLZ</label>
            <input 
              type="text" 
              name="zip" 
              value="<?= htmlspecialchars($user['zip'] ?? '') ?>"
              class="glass-input w-full px-4 py-3"
              placeholder="12345"
            >
          </div>
          <div class="md:col-span-2">
            <label class="block mb-3">Stadt</label>
            <input 
              type="text" 
              name="city" 
              value="<?= htmlspecialchars($user['city'] ?? '') ?>"
              class="glass-input w-full px-4 py-3"
              placeholder="Musterstadt"
            >
          </div>
        </div>
        
        <div>
          <label class="block mb-3">Land</label>
          <input 
            type="text" 
            name="country" 
            value="<?= htmlspecialchars($user['country'] ?? '') ?>"
            class="glass-input w-full px-4 py-3"
            placeholder="Deutschland"
          >
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block mb-3">Telefon</label>
            <input 
              type="tel" 
              name="phone" 
              value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
              class="glass-input w-full px-4 py-3"
              placeholder="+49 123 456789"
            >
          </div>
          <div>
            <label class="block mb-3">Private E-Mail</label>
            <input 
              type="email" 
              name="private_email" 
              value="<?= htmlspecialchars($user['private_email'] ?? '') ?>"
              class="glass-input w-full px-4 py-3"
              placeholder="ihre@email.de"
            >
          </div>
        </div>
        
        <div class="pt-6">
          <button type="submit" class="glass-btn-primary px-8 py-3 font-medium">
            Adresse speichern
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
