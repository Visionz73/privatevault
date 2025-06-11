<!-- templates/profile_tabs/personal_info/public_profile.php -->
<div class="content-card">
  <h3 class="text-xl font-semibold text-primary mb-6">Öffentliches Profil</h3>
  
  <?php if (!empty($publicSuccess)): ?>
    <div class="alert alert-success mb-4"><?= htmlspecialchars($publicSuccess) ?></div>
  <?php endif; ?>

  <form method="post" action="/src/controllers/profile_save.php" class="space-y-6">
    <input type="hidden" name="subtab" value="public_profile">
    
    <!-- Bio -->
    <div>
      <label for="bio" class="block text-sm font-medium mb-2">Biografie</label>
      <textarea id="bio" name="bio" rows="4" 
                class="form-input w-full px-4 py-3"
                placeholder="Erzählen Sie etwas über sich..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
    </div>
    
    <!-- Links -->
    <div>
      <label class="block text-sm font-medium mb-2">Social Links</label>
      <?php
      $links = json_decode($user['links'] ?? '[]', true) ?: [];
      for ($i = 0; $i < 3; $i++):
        $link = $links[$i] ?? '';
      ?>
      <div class="mb-3">
        <input type="url" name="links[]" 
               value="<?= htmlspecialchars($link) ?>"
               placeholder="https://example.com"
               class="form-input w-full px-4 py-3">
      </div>
      <?php endfor; ?>
    </div>
    
    <div class="flex justify-end pt-4">
      <button type="submit" 
              class="btn-primary px-6 py-3">
        Speichern
      </button>
    </div>
  </form>
</div>
