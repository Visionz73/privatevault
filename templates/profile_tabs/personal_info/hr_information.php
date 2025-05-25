<!-- templates/profile_tabs/personal_info/hr_information.php -->
<?php
// This template is for the 'HR Information' form.
// It's handled by src/controllers/profile.php when $activeTab === 'personal_info' 
// and $subTab === 'hr_information' and $_POST['action'] === 'update_hr_info'.
// Variables available: $user, $csrf_token_hr_info
// Session messages are displayed by the main templates/profile.php
?>
<div class="bg-white rounded-xl shadow-lg p-6 md:p-8 space-y-6">
  <div class="space-y-8">
    <div>
      <h2 class="text-2xl font-semibold text-gray-800 border-b pb-4 mb-6">HR Information</h2>
      <p class="text-gray-600">Berufliche und arbeitsrelevante Informationen.</p>
    </div>

    <?php // Session messages (success/error) are displayed by the main templates/profile.php ?>

    <form method="POST" action="profile.php?tab=personal_info&subtab=hr_information" class="space-y-6">
      <input type="hidden" name="action" value="update_hr_info">
      <input type="hidden" name="csrf_token_hr_info" value="<?php echo htmlspecialchars($csrf_token_hr_info ?? ''); // From controller ?>">
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
        <!-- Job Title -->
        <div>
          <label for="hr_job_title" class="block text-sm font-medium text-gray-700 mb-1">Berufsbezeichnung</label>
          <input type="text" name="job_title" id="hr_job_title"
                 value="<?= htmlspecialchars($user['job_title'] ?? '') ?>"
                 class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>

        <!-- Department -->
        <div>
          <label for="hr_department" class="block text-sm font-medium text-gray-700 mb-1">Abteilung</label>
          <input type="text" name="department" id="hr_department"
                 value="<?= htmlspecialchars($user['department'] ?? '') ?>"
                 class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>

        <!-- Employee ID -->
        <div>
          <label for="hr_employee_id" class="block text-sm font-medium text-gray-700 mb-1">Mitarbeiter-ID</label>
          <input type="text" name="employee_id" id="hr_employee_id"
                 value="<?= htmlspecialchars($user['employee_id'] ?? '') ?>"
                 class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>

        <!-- Start Date -->
        <div>
          <label for="hr_start_date" class="block text-sm font-medium text-gray-700 mb-1">Eintrittsdatum</label>
          <input type="date" name="start_date" id="hr_start_date"
                 value="<?= htmlspecialchars($user['start_date'] ?? '') ?>"
                 class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>

        <!-- Manager -->
        <div>
          <label for="hr_manager" class="block text-sm font-medium text-gray-700 mb-1">Vorgesetzter</label>
          <input type="text" name="manager" id="hr_manager"
                 value="<?= htmlspecialchars($user['manager'] ?? '') ?>"
                 class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>

        <!-- Work Location -->
        <div>
          <label for="hr_work_location" class="block text-sm font-medium text-gray-700 mb-1">Arbeitsort</label>
          <input type="text" name="work_location" id="hr_work_location"
                 value="<?= htmlspecialchars($user['work_location'] ?? '') ?>"
                 class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
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
</div>
