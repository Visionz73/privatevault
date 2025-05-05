<!-- templates/profile_tabs/personal_info/personal_data.php -->
<?php /* Erfolgs‑ und Fehlermeldungen kommen vom Controller, z. B. $success / $errors */ ?>
<div class="bg-white rounded-xl shadow p-6 space-y-8">
  <h2 class="text-xl font-semibold text-gray-900">Persönliche Daten</h2>

  <?php if (!empty($success)): ?>
    <div class="p-4 bg-green-100 border border-green-300 text-green-800 rounded shadow">
      <?= htmlspecialchars($success) ?>
    </div>
  <?php endif; ?>
  <?php if (!empty($errors)): ?>
    <div class="p-4 bg-red-100 border border-red-300 text-red-800 rounded shadow">
      <ul class="list-disc list-inside space-y-1 text-sm">
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" action="/privatevault/src/controllers/profile_save.php" class="space-y-8">
    <input type="hidden" name="subtab" value="personal_data">

    <!-- Grund­daten ----------------------------------------------------- -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Vorname</label>
        <input name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>"
               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-[#4A90E2]">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nachname</label>
        <input name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>"
               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-[#4A90E2]">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Geburtsdatum</label>
        <input type="date" name="dob" value="<?= htmlspecialchars($user['dob'] ?? '') ?>"
               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-[#4A90E2]">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nationalität</label>
        <input name="nationality" value="<?= htmlspecialchars($user['nationality'] ?? '') ?>"
               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-[#4A90E2]">
      </div>
    </div>

    <!-- Adresse -------------------------------------------------------- -->
    <fieldset class="space-y-4">
      <legend class="text-sm font-medium text-gray-700">Adresse</legend>
      <input name="street" placeholder="Straße + Hausnr." value="<?= htmlspecialchars($user['street'] ?? '') ?>"
             class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-[#4A90E2]">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input name="zip" placeholder="PLZ" value="<?= htmlspecialchars($user['zip'] ?? '') ?>"
               class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-[#4A90E2]">
        <input name="city" placeholder="Ort" value="<?= htmlspecialchars($user['city'] ?? '') ?>"
               class="col-span-2 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-[#4A90E2]">
      </div>
      <input name="country" placeholder="Land" value="<?= htmlspecialchars($user['country'] ?? '') ?>"
             class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-[#4A90E2]">
    </fieldset>

    <!-- Kontakt -------------------------------------------------------- -->
    <fieldset class="space-y-4">
      <legend class="text-sm font-medium text-gray-700">Kontakt</legend>
      <input type="tel" name="phone" placeholder="Telefon" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
             class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-[#4A90E2]">
      <input type="email" name="private_email" placeholder="Private E‑Mail" value="<?= htmlspecialchars($user['private_email'] ?? '') ?>"
             class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-[#4A90E2]">
    </fieldset>

    <button type="submit" class="px-6 py-2 bg-[#4A90E2] text-white rounded-lg shadow hover:bg-[#357ABD] transition">
      Speichern
    </button>
  </form>
</div>
