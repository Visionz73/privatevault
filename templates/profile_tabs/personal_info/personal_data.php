<!-- templates/profile_tabs/personal_info/personal_data.php -->
<?php /* Erfolgs‑ und Fehlermeldungen kommen vom Controller, z. B. $success / $errors */ ?>
<div class="glassmorphism-container p-6 space-y-8">
  <h2 class="text-xl font-semibold text-primary">Persönliche Daten</h2>

  <?php if (!empty($success)): ?>
    <div class="p-4 bg-green-500/20 border border-green-500/30 text-green-300 rounded-lg backdrop-blur">
      <?= htmlspecialchars($success) ?>
    </div>
  <?php endif; ?>
  <?php if (!empty($errors)): ?>
    <div class="p-4 bg-red-500/20 border border-red-500/30 text-red-300 rounded-lg backdrop-blur">
      <ul class="list-disc list-inside space-y-1 text-sm">
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" action="" class="space-y-8">
    <input type="hidden" name="form_marker" value="personal_data_update">

    <!-- Grund­daten ----------------------------------------------------- -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label class="block text-sm font-medium text-secondary mb-1">Vorname</label>
        <input name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>"
               class="form-input w-full px-4 py-2">
      </div>
      <div>
        <label class="block text-sm font-medium text-secondary mb-1">Nachname</label>
        <input name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>"
               class="form-input w-full px-4 py-2">
      </div>

      <div>
        <label class="block text-sm font-medium text-secondary mb-1">Geburtsdatum</label>
        <input type="date" name="birthdate" value="<?= htmlspecialchars($user['birthdate'] ?? '') ?>"
               class="form-input w-full px-4 py-2">
      </div>
      <div>
        <label class="block text-sm font-medium text-secondary mb-1">Nationalität</label>
        <input name="nationality" value="<?= htmlspecialchars($user['nationality'] ?? '') ?>"
               class="form-input w-full px-4 py-2">
      </div>
       <div>
        <label class="block text-sm font-medium text-secondary mb-1">Job Title</label>
        <input name="job_title" value="<?= htmlspecialchars($user['job_title'] ?? '') ?>"
               class="form-input w-full px-4 py-2">
      </div>
      <div>
        <label class="block text-sm font-medium text-secondary mb-1">Location</label>
        <input name="location" value="<?= htmlspecialchars($user['location'] ?? '') ?>"
               class="form-input w-full px-4 py-2">
      </div>
    </div>

    <!-- Adresse -------------------------------------------------------- -->
    <fieldset class="space-y-4">
      <legend class="text-sm font-medium text-secondary">Adresse</legend>
      <input name="street" placeholder="Straße + Hausnr." value="<?= htmlspecialchars($user['street'] ?? '') ?>"
             class="form-input w-full px-4 py-2">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input name="zip" placeholder="PLZ" value="<?= htmlspecialchars($user['zip'] ?? '') ?>"
               class="form-input px-4 py-2">
        <input name="city" placeholder="Ort" value="<?= htmlspecialchars($user['city'] ?? '') ?>"
               class="form-input col-span-2 px-4 py-2">
      </div>
      <input name="country" placeholder="Land" value="<?= htmlspecialchars($user['country'] ?? '') ?>"
             class="form-input w-full px-4 py-2">
    </fieldset>

    <!-- Kontakt -------------------------------------------------------- -->
    <fieldset class="space-y-4">
      <legend class="text-sm font-medium text-secondary">Kontakt</legend>
      <input type="tel" name="phone" placeholder="Telefon" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
             class="form-input w-full px-4 py-2">
      <input type="email" name="private_email" placeholder="Private E‑Mail" value="<?= htmlspecialchars($user['private_email'] ?? '') ?>"
             class="form-input w-full px-4 py-2">
    </fieldset>

    <button type="submit" class="btn-primary px-6 py-2">
      Speichern
    </button>
  </form>
</div>
