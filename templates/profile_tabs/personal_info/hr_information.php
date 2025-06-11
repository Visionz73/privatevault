<!-- templates/profile_tabs/hr_information.php -->
<div class="bg-card-bg rounded-xl shadow-card-lg p-6 space-y-4">
  <div class="space-y-8">
    <div>
      <h2 class="text-xl font-semibold text-gray-900 mb-2">HR Informationen</h2>
      <p class="text-gray-600">Berufliche und arbeitsrelevante Informationen.</p>
    </div>

    <form method="POST" action="/src/controllers/profile_save.php" class="space-y-6">
      <input type="hidden" name="subtab" value="hr_information">
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Job Title -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Berufsbezeichnung</label>
          <input type="text" name="job_title" 
                 value="<?= htmlspecialchars($user['job_title'] ?? '') ?>"
                 class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
        </div>

        <!-- Department -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Abteilung</label>
          <input type="text" name="department" 
                 value="<?= htmlspecialchars($user['department'] ?? '') ?>"
                 class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
        </div>

        <!-- Employee ID -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Mitarbeiter-ID</label>
          <input type="text" name="employee_id" 
                 value="<?= htmlspecialchars($user['employee_id'] ?? '') ?>"
                 class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
        </div>

        <!-- Start Date -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Eintrittsdatum</label>
          <input type="date" name="start_date" 
                 value="<?= htmlspecialchars($user['start_date'] ?? '') ?>"
                 class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
        </div>

        <!-- Manager -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Vorgesetzter</label>
          <input type="text" name="manager" 
                 value="<?= htmlspecialchars($user['manager'] ?? '') ?>"
                 class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
        </div>

        <!-- Location -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Arbeitsort</label>
          <input type="text" name="work_location" 
                 value="<?= htmlspecialchars($user['work_location'] ?? '') ?>"
                 class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
        </div>
      </div>

      <div class="flex justify-end">
        <button type="submit" 
                class="px-6 py-2 bg-[#4A90E2] text-white rounded-lg hover:bg-[#357abd] transition-colors">
          Speichern
        </button>
      </div>
    </form>
  </div>
</div>
