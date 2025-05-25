<!-- templates/profile_tabs/personal_info/personal_info.php -->
<?php
// This template is for the main 'Personal Info' form when no specific subtab is chosen.
// It's handled by src/controllers/profile.php when $activeTab === 'personal_info' && $subTab === ''
// Variables available: $user, $csrf_token_personal_info, $success, $errors
?>
<div class="bg-white rounded-xl shadow-lg p-6 md:p-8 space-y-6">
  <h2 class="text-2xl font-semibold text-gray-800 border-b pb-4 mb-6">Personal Info</h2>

  <?php if (!empty($success)): // Display success message set by controller for this specific form ?>
    <div class="bg-green-50 border-l-4 border-green-400 text-green-700 p-4 mb-6 rounded-md shadow-sm" role="alert">
        <div class="flex">
            <div class="py-1"><svg class="fill-current h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM6.7 9.29L9 11.6l4.3-4.3 1.4 1.42L9 14.4l-3.7-3.7 1.4-1.42z"/></svg></div>
            <div>
                <p class="font-bold">Success</p>
                <p class="text-sm"><?= htmlspecialchars($success) ?></p>
            </div>
        </div>
    </div>
  <?php endif; ?>
  <?php if (!empty($errors)): // Display errors set by controller for this specific form ?>
    <div class="bg-red-50 border-l-4 border-red-400 text-red-700 p-4 mb-6 rounded-md shadow-sm" role="alert">
        <div class="flex">
            <div class="py-1"><svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 5l1.41 1.41L7.83 9l2.58 2.59L9 13l-4-4 4-4z"/></svg></div>
            <div>
                <p class="font-bold">Error</p>
                <ul class="list-disc pl-5 text-sm">
                    <?php foreach ($errors as $e): ?>
                      <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
  <?php endif; ?>

  <form method="post" action="profile.php?tab=personal_info&subtab=" class="space-y-6">
    <input type="hidden" name="action" value="update_personal_info">
    <input type="hidden" name="csrf_token_personal_info" value="<?php echo htmlspecialchars($csrf_token_personal_info ?? ''); ?>">
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label for="pi_first_name" class="block text-sm font-medium text-gray-700 mb-1">Vorname</label>
        <input type="text" name="first_name" id="pi_first_name"
               value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required
               class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
      </div>
      <div>
        <label for="pi_last_name" class="block text-sm font-medium text-gray-700 mb-1">Nachname</label>
        <input type="text" name="last_name" id="pi_last_name"
               value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required
               class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label for="pi_birthdate" class="block text-sm font-medium text-gray-700 mb-1">Geburtsdatum</label>
        <input type="date" name="birthdate" id="pi_birthdate" 
               value="<?= htmlspecialchars($user['birthdate'] ?? '') ?>"
               class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
      </div>
      <div>
        <label for="pi_location" class="block text-sm font-medium text-gray-700 mb-1">Standort</label>
        <input type="text" name="location" id="pi_location"
               value="<?= htmlspecialchars($user['location'] ?? '') ?>"
               class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label for="pi_job_title" class="block text-sm font-medium text-gray-700 mb-1">Job-Titel</label>
        <input type="text" name="job_title" id="pi_job_title"
               value="<?= htmlspecialchars($user['job_title'] ?? '') ?>"
               class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
      </div>
    </div>

    <div class="flex justify-end pt-2">
        <button type="submit"
                class="inline-flex justify-center py-2 px-5 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
          Speichern
        </button>
    </div>
  </form>
</div>
