<?php
// Modern personal info section with subtabs
$personalSubtabs = [
  'personal_data' => 'Persönliche Daten',
  'public_profile' => 'Öffentliches Profil',
  'hr_information' => 'HR Informationen'
];
$currentPersonalSubtab = $_GET['subtab'] ?? 'personal_data';
?>

<div class="space-y-6">
  <!-- Section Header -->
  <div class="mb-8">
    <h2 class="text-2xl font-bold text-primary mb-2">Persönliche Informationen</h2>
    <p class="text-secondary">Verwalten Sie Ihre persönlichen Daten und Profilinformationen</p>
  </div>
  
  <!-- Subtab Navigation -->
  <div class="glass-card p-2 mb-8">
    <div class="flex flex-wrap gap-2">
      <?php foreach ($personalSubtabs as $key => $label): ?>
        <?php 
        $isActive = $currentPersonalSubtab === $key;
        $href = "?tab=personal_info&subtab=" . $key;
        ?>
        <a href="<?= $href ?>" 
           class="px-4 py-2 rounded-lg transition-all duration-300 text-sm font-medium <?= $isActive ? 'bg-gradient-to-r from-purple-500/40 to-blue-500/40 text-white border border-purple-500/30' : 'text-secondary hover:text-white hover:bg-white/10' ?>">
          <?= htmlspecialchars($label) ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
  
  <!-- Subtab Content -->
  <?php
  $subtabFile = __DIR__ . "/personal_info/{$currentPersonalSubtab}.php";
  if (file_exists($subtabFile)) {
    include $subtabFile;
  } else {
    echo '<div class="glass-card p-8 text-center">
            <h3 class="text-lg text-primary mb-2">Bereich nicht verfügbar</h3>
            <p class="text-muted">Dieser Bereich ist noch nicht implementiert.</p>
          </div>';
  }
  ?>
</div>
