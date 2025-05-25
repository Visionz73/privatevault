<!-- templates/profile_tabs/personal_info/personal_data.php -->
<?php 
// Session messages are displayed by the main templates/profile.php after redirect.
// $user and $csrf_token_personal_data are passed from src/controllers/profile.php
?>
<div class="bg-white rounded-xl shadow-lg p-6 md:p-8 space-y-6">
  <h2 class="text-2xl font-semibold text-gray-800 border-b pb-4 mb-6">Persönliche Daten</h2>

  <?php // Session messages (success/error) are displayed by the main templates/profile.php ?>

  <form method="post" action="profile.php?tab=personal_info&subtab=personal_data" class="space-y-8">
    <input type="hidden" name="action" value="update_personal_data">
    <input type="hidden" name="csrf_token_personal_data" value="<?php echo htmlspecialchars($csrf_token_personal_data ?? ''); ?>">

    <!-- Grund­daten -->
    <fieldset class="space-y-4">
        <legend class="text-lg font-medium text-gray-900 mb-2">Grunddaten</legend>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
          <div>
            <label for="pd_first_name" class="block text-sm font-medium text-gray-700 mb-1">Vorname</label>
            <input type="text" name="first_name" id="pd_first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required
                   class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
          </div>
          <div>
            <label for="pd_last_name" class="block text-sm font-medium text-gray-700 mb-1">Nachname</label>
            <input type="text" name="last_name" id="pd_last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required
                   class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
          </div>
          <div>
            <label for="pd_dob" class="block text-sm font-medium text-gray-700 mb-1">Geburtsdatum</label>
            <input type="date" name="dob" id="pd_dob" value="<?= htmlspecialchars($user['dob'] ?? '') ?>"
                   class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
          </div>
          <div>
            <label for="pd_nationality" class="block text-sm font-medium text-gray-700 mb-1">Nationalität</label>
            <input type="text" name="nationality" id="pd_nationality" value="<?= htmlspecialchars($user['nationality'] ?? '') ?>"
                   class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
          </div>
        </div>
    </fieldset>

    <!-- Adresse -->
    <fieldset class="space-y-4 pt-4 border-t border-gray-200">
      <legend class="text-lg font-medium text-gray-900 mb-2">Adresse</legend>
      <div>
          <label for="pd_street" class="block text-sm font-medium text-gray-700 mb-1">Straße + Hausnr.</label>
          <input type="text" name="street" id="pd_street" placeholder="Straße + Hausnr." value="<?= htmlspecialchars($user['street'] ?? '') ?>"
                 class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4">
        <div>
            <label for="pd_zip" class="block text-sm font-medium text-gray-700 mb-1">PLZ</label>
            <input type="text" name="zip" id="pd_zip" placeholder="PLZ" value="<?= htmlspecialchars($user['zip'] ?? '') ?>"
                   class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
        <div class="md:col-span-2">
            <label for="pd_city" class="block text-sm font-medium text-gray-700 mb-1">Ort</label>
            <input type="text" name="city" id="pd_city" placeholder="Ort" value="<?= htmlspecialchars($user['city'] ?? '') ?>"
                   class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
      </div>
      <div>
          <label for="pd_country" class="block text-sm font-medium text-gray-700 mb-1">Land</label>
          <input type="text" name="country" id="pd_country" placeholder="Land" value="<?= htmlspecialchars($user['country'] ?? '') ?>"
                 class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
      </div>
    </fieldset>

    <!-- Kontakt -->
    <fieldset class="space-y-4 pt-4 border-t border-gray-200">
      <legend class="text-lg font-medium text-gray-900 mb-2">Kontakt</legend>
      <div>
          <label for="pd_phone" class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
          <input type="tel" name="phone" id="pd_phone" placeholder="Telefon" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                 class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
      </div>
      <div>
          <label for="pd_private_email" class="block text-sm font-medium text-gray-700 mb-1">Private E‑Mail</label>
          <input type="email" name="private_email" id="pd_private_email" placeholder="Private E‑Mail" value="<?= htmlspecialchars($user['private_email'] ?? '') ?>"
                 class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
      </div>
    </fieldset>

    <div class="flex justify-end pt-4">
        <button type="submit" class="inline-flex justify-center py-2 px-5 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
          Speichern
        </button>
    </div>
  </form>
</div>
