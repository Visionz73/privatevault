<!-- templates/profile_tabs/hr_information.php -->
<div class="bg-card-bg rounded-xl shadow-card-lg p-6 space-y-4">
  <h2 class="text-xl font-semibold text-text">HR Information</h2>
  <p class="text-text-secondary italic">Hier können Sie HR-spezifische Daten pflegen (z.B. Eintrittsdatum, Vertragsdetails etc.).</p>
  <!-- Beispiel-Formular -->
  <form method="post" action="?tab=personal_info&subtab=hr_information" class="space-y-6">
    <div>
      <label class="block text-sm font-medium text-text-secondary mb-1">Eintrittsdatum</label>
      <input name="hire_date" type="date"
             value="<?= htmlspecialchars($user['hire_date'] ?? '') ?>"
             class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary"/>
    </div>
    <div>
      <label class="block text-sm font-medium text-text-secondary mb-1">Vertragsart</label>
      <select name="contract_type"
              class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary">
        <option value="unbefristet" <?= ($user['contract_type'] ?? '')==='unbefristet'?'selected':'' ?>>Unbefristet</option>
        <option value="befristet"   <?= ($user['contract_type'] ?? '')==='befristet'   ?'selected':'' ?>>Befristet</option>
      </select>
    </div>
    <button type="submit"
            class="px-6 py-2 bg-primary text-white rounded-lg shadow hover:bg-primary-dark transition">
      Speichern
    </button>
  </form>
</div>
