<!-- templates/profile_tabs/personal_info/public_profile.php -->
<div class="space-y-8">
  <div>
    <h2 class="text-xl font-semibold text-gray-900 mb-2">Public Profile</h2>
    <p class="text-gray-600">Diese Informationen sind für andere Benutzer sichtbar.</p>
  </div>

  <?php if ($publicSuccess): ?>
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
      <p class="text-green-800"><?= htmlspecialchars($publicSuccess) ?></p>
    </div>
  <?php endif; ?>

  <form method="POST" action="/src/controllers/profile_save.php" class="space-y-6">
    <input type="hidden" name="subtab" value="public_profile">
    
    <!-- Bio -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
      <textarea name="bio" rows="4" 
                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]"
                placeholder="Erzählen Sie etwas über sich..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
    </div>

    <!-- Social Links -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Social Links</label>
      <?php 
      $links = json_decode($user['links'] ?? '[]', true) ?: [];
      $linkTypes = ['LinkedIn', 'Twitter/X', 'Xing', 'GitHub', 'Website'];
      ?>
      <div class="space-y-3">
        <?php foreach ($linkTypes as $type): ?>
          <div class="flex items-center space-x-3">
            <label class="w-20 text-sm text-gray-600"><?= $type ?>:</label>
            <input type="url" name="links[<?= strtolower($type) ?>]" 
                   value="<?= htmlspecialchars($links[strtolower($type)] ?? '') ?>"
                   class="flex-1 px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]"
                   placeholder="https://...">
          </div>
        <?php endforeach; ?>
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
