<!-- templates/profile_tabs/personal_info.php -->
<div class="bg-card-bg rounded-xl shadow-card-lg p-6 space-y-4">
  <h2 class="text-xl font-semibold text-text">Personal Info</h2>

  <?php if ($success): ?>
    <div class="p-4 bg-green-100 border border-green-300 text-green-800 rounded shadow">
      <?= htmlspecialchars($success) ?>
    </div>
  <?php endif; ?>
  <?php if ($errors): ?>
    <div class="p-4 bg-red-100 border border-red-300 text-red-800 rounded shadow">
      <ul class="list-disc list-inside">
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label class="block text-sm font-medium text-text-secondary mb-1">Vorname</label>
        <input name="first_name"
               value="<?= htmlspecialchars($user['first_name'] ?? '') ?>"
               class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary"
        />
      </div>
      <div>
        <label class="block text-sm font-medium text-text-secondary mb-1">Nachname</label>
        <input name="last_name"
               value="<?= htmlspecialchars($user['last_name'] ?? '') ?>"
               class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary"
        />
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label class="block text-sm font-medium text-text-secondary mb-1">Geburtsdatum</label>
        <input name="birthdate" type="date"
               value="<?= htmlspecialchars($user['birthdate'] ?? '') ?>"
               class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary"
        />
      </div>
      <div>
        <label class="block text-sm font-medium text-text-secondary mb-1">Standort</label>
        <input name="location"
               value="<?= htmlspecialchars($user['location'] ?? '') ?>"
               class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary"
        />
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label class="block text-sm font-medium text-text-secondary mb-1">Job-Titel</label>
        <input name="job_title"
               value="<?= htmlspecialchars($user['job_title'] ?? '') ?>"
               class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary"
        />
      </div>
    </div>

    <button type="submit"
            class="px-6 py-2 bg-primary text-white rounded-lg shadow hover:bg-primary-dark transition">
      Speichern
    </button>
  </form>
</div>
