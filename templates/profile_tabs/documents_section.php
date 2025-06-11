<?php
// Modern documents section
$docSubtabs = [
  'documents' => 'Alle Dokumente',
  'contracts' => 'Verträge',
  'invoices' => 'Rechnungen',
  'insurance' => 'Versicherungen',
  'other_docs' => 'Sonstige'
];
$currentDocSubtab = $_GET['subtab'] ?? 'documents';
?>

<div class="space-y-6">
  <!-- Section Header -->
  <div class="mb-8">
    <h2 class="text-2xl font-bold text-primary mb-2">Dokument-Verwaltung</h2>
    <p class="text-secondary">Verwalten Sie Ihre wichtigen Dokumente und Dateien</p>
  </div>
  
  <!-- Quick Actions -->
  <div class="glass-card p-6 mb-8">
    <div class="flex flex-wrap gap-4 items-center justify-between">
      <div class="flex items-center gap-4">
        <a href="/upload.php" class="glass-btn-primary px-6 py-3 font-medium inline-flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
          Dokument hochladen
        </a>
        <span class="text-muted text-sm">
          <?= count($docs ?? []) ?> Dokumente gespeichert
        </span>
      </div>
      
      <!-- Search -->
      <div class="flex items-center gap-4">
        <input 
          type="text" 
          placeholder="Dokumente durchsuchen..." 
          class="glass-input px-4 py-2 text-sm w-64"
          value="<?= htmlspecialchars($_GET['title_filter'] ?? '') ?>"
          id="documentSearch"
        >
      </div>
    </div>
  </div>
  
  <!-- Subtab Navigation -->
  <div class="glass-card p-2 mb-8">
    <div class="flex flex-wrap gap-2">
      <?php foreach ($docSubtabs as $key => $label): ?>
        <?php 
        $isActive = $currentDocSubtab === $key;
        $href = "?tab=documents&subtab=" . $key;
        ?>
        <a href="<?= $href ?>" 
           class="px-4 py-2 rounded-lg transition-all duration-300 text-sm font-medium <?= $isActive ? 'bg-gradient-to-r from-purple-500/40 to-blue-500/40 text-white border border-purple-500/30' : 'text-secondary hover:text-white hover:bg-white/10' ?>">
          <?= htmlspecialchars($label) ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
  
  <!-- Document Content -->
  <?php if ($currentDocSubtab === 'documents'): ?>
    <!-- Documents Table -->
    <div class="glass-card overflow-hidden">
      <div class="glass-table">
        <table class="w-full">
          <thead>
            <tr class="glass-table">
              <th class="glass-table px-6 py-4 text-left">Titel</th>
              <th class="glass-table px-6 py-4 text-left">Typ</th>
              <th class="glass-table px-6 py-4 text-left">Kategorie</th>
              <th class="glass-table px-6 py-4 text-left">Datum</th>
              <th class="glass-table px-6 py-4 text-right">Aktionen</th>
            </tr>
          </thead>
          <tbody class="glass-table">
            <?php if (empty($docs)): ?>
              <tr>
                <td colspan="5" class="glass-table px-6 py-12 text-center text-muted">
                  <div class="flex flex-col items-center gap-4">
                    <svg class="w-12 h-12 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p>Keine Dokumente gefunden</p>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach($docs as $doc): ?>
                <tr class="glass-table">
                  <td class="glass-table px-6 py-4">
                    <div class="flex items-center gap-3">
                      <div class="p-2 bg-purple-500/20 rounded-lg">
                        <svg class="w-4 h-4 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                      </div>
                      <span class="font-medium"><?= htmlspecialchars($doc['title'] ?? '') ?></span>
                    </div>
                  </td>
                  <td class="glass-table px-6 py-4 text-secondary"><?= htmlspecialchars($doc['doc_type'] ?? '') ?></td>
                  <td class="glass-table px-6 py-4 text-secondary"><?= htmlspecialchars($doc['category_name'] ?? 'N/A') ?></td>
                  <td class="glass-table px-6 py-4 text-secondary"><?= date('d.m.Y', strtotime($doc['upload_date'] ?? 'now')) ?></td>
                  <td class="glass-table px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                      <a href="/uploads/<?= urlencode($doc['filename'] ?? '') ?>" 
                         download 
                         class="glass-btn-secondary px-3 py-1 text-sm">
                        Download
                      </a>
                      <a href="?tab=documents&delete=<?= $doc['id'] ?>" 
                         class="text-red-400 hover:text-red-300 px-3 py-1 text-sm"
                         onclick="return confirm('Dokument wirklich löschen?')">
                        Löschen
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php else: ?>
    <?php
    $subtabFile = __DIR__ . "/documents/{$currentDocSubtab}.php";
    if (file_exists($subtabFile)) {
      include $subtabFile;
    } else {
      echo '<div class="glass-card p-8 text-center">
              <h3 class="text-lg text-primary mb-2">Bereich nicht verfügbar</h3>
              <p class="text-muted">Dieser Bereich ist noch nicht implementiert.</p>
            </div>';
    }
    ?>
  <?php endif; ?>
</div>

<script>
// Document search functionality
document.getElementById('documentSearch').addEventListener('input', function(e) {
  const searchTerm = e.target.value.toLowerCase();
  const rows = document.querySelectorAll('tbody tr');
  
  rows.forEach(row => {
    if (row.cells.length > 1) {
      const title = row.cells[0].textContent.toLowerCase();
      const type = row.cells[1].textContent.toLowerCase();
      const category = row.cells[2].textContent.toLowerCase();
      
      const matches = title.includes(searchTerm) || 
                     type.includes(searchTerm) || 
                     category.includes(searchTerm);
      
      row.style.display = matches ? '' : 'none';
    }
  });
});
</script>
