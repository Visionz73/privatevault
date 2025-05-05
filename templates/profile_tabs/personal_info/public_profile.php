<!-- templates/profile_tabs/personal_info/public_profile.php -->
<form method="post" class="space-y-6">

  <!-- Überschrift ----------------------------------------------------- -->
  <h3 class="text-xl font-semibold text-gray-900">Public profile</h3>

  <!-- Kurzbiografie --------------------------------------------------- -->
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Kurz-Bio</label>
    <textarea name="bio" rows="4"
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-[#4A90E2]"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
    <p class="text-xs text-gray-500 mt-1">Max. 300 Zeichen; Markdown erlaubt.</p>
  </div>

  <!-- Web-/Social Links (Grid) ---------------------------------------- -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <?php
      $links = [
        'website'   => 'Website / Portfolio',
        'linkedin'  => 'LinkedIn-Profil',
        'github'    => 'GitHub-Profil',
        'twitter'   => 'Twitter-Handle'
      ];
      foreach ($links as $key=>$label):
    ?>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1"><?= $label ?></label>
        <input name="links[<?= $key ?>]" type="url"
               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-[#4A90E2]"
               value="<?= htmlspecialchars($user['links'][$key] ?? '') ?>">
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Speichern-Button ------------------------------------------------- -->
  <button type="submit"
          class="px-6 py-2 bg-[#4A90E2] text-white rounded-lg shadow hover:bg-[#357ABD] transition">
    Speichern
  </button>
</form>
