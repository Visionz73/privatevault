<!-- templates/profile_tabs/personal_info/public_profile.php -->
<div class="space-y-8">
  <div>
    <h2 class="text-xl font-semibold text-gray-900 mb-2">Public Profile</h2>
    <p class="text-gray-600">Diese Informationen sind für andere Benutzer sichtbar.</p>
  </div>

  <?php
    // Session messages (success/error) are expected to be displayed by the main 
    // templates/profile.php (which includes this file) after a redirect.
    // The local $publicSuccess variable is no longer set by the controller for this form.
  ?>

  <form method="POST" action="profile.php?tab=personal_info&subtab=public_profile" class="space-y-6">
    <input type="hidden" name="action" value="update_public_profile">
    <input type="hidden" name="csrf_token_public_profile" value="<?php echo htmlspecialchars($csrf_token_public_profile ?? ''); // From controller ?>">
    
    <!-- Bio -->
    <div>
      <label for="bio_input" class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
      <textarea name="bio" id="bio_input" rows="4" 
                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]"
                placeholder="Erzählen Sie etwas über sich..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
    </div>

    <!-- Social Links -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Social Links</label>
      <?php 
      // $user is passed from src/controllers/profile.php
      $user_links = isset($user['links']) && is_string($user['links']) ? json_decode($user['links'], true) : ($user['links'] ?? []);
      if (!is_array($user_links)) { 
          $user_links = [];
      }
      
      // Define the link types and their labels for the form
      // These keys should match what the controller expects in $_POST['links']
      $linkTypes = [
          'linkedin'    => 'LinkedIn',
          'twitter_x'   => 'Twitter/X',
          'xing'        => 'Xing',
          'github'      => 'GitHub',
          'website'     => 'Website'
          // Add other link types here if needed, and ensure controller handles them
      ];
      ?>
      <div class="space-y-3">
        <?php foreach ($linkTypes as $key => $label): ?>
          <div class="flex items-center space-x-3">
            <label for="link_field_<?= $key ?>" class="w-24 text-sm text-gray-600 shrink-0"><?= htmlspecialchars($label) ?>:</label>
            <input type="url" id="link_field_<?= $key ?>" name="links[<?= $key ?>]" 
                   value="<?= htmlspecialchars($user_links[$key] ?? '') ?>"
                   class="flex-1 px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]"
                   placeholder="https://...">
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="flex justify-end pt-2">
      <button type="submit" 
              class="px-6 py-2 bg-[#4A90E2] text-white rounded-lg hover:bg-[#357abd] focus:ring-2 focus:ring-offset-2 focus:ring-[#4A90E2] transition-colors">
        Speichern
      </button>
    </div>
  </form>
</div>
