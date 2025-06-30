<!-- templates/profile_tabs/documents.php -->
<div class="glassmorphism-container p-6 space-y-4">
  <h2 class="text-xl font-semibold text-primary">Dokumente</h2>

  <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 mb-4">
    <a href="upload.php"
       class="btn-primary inline-block px-6 py-2 text-center">
      Neues Dokument
    </a>

    <div class="flex flex-col sm:flex-row gap-4">
      <!-- Title Filter -->
      <div class="flex items-center space-x-2">
        <label for="title_filter" class="text-sm font-medium text-secondary">Nach Titel:</label>
        <input type="text" 
               id="title_filter" 
               placeholder="Titel durchsuchen..." 
               value="<?= htmlspecialchars($_GET['title_filter'] ?? '') ?>"
               class="form-input text-sm py-2 px-3 min-w-[200px]">
      </div>

      <!-- Category Filter -->
      <?php if (isset($documentCategories) && !empty($documentCategories)): ?>
      <form method="GET" id="categoryFilterForm" class="flex items-center space-x-2">
          <input type="hidden" name="tab" value="documents">
          <?php
          // Preserve subtab and title filter
          $currentSubtabForFilter = $_GET['subtab'] ?? 'documents'; 
          ?>
          <input type="hidden" name="subtab" value="<?= htmlspecialchars($currentSubtabForFilter) ?>">
          <?php if (!empty($_GET['title_filter'])): ?>
          <input type="hidden" name="title_filter" value="<?= htmlspecialchars($_GET['title_filter']) ?>">
          <?php endif; ?>
          
          <label for="category_filter" class="text-sm font-medium text-secondary">Kategorie:</label>
          <select name="category_filter" id="category_filter" 
                  class="form-input text-sm py-2 px-3"
                  onchange="document.getElementById('categoryFilterForm').submit();">
              <option value="">Alle Kategorien</option>
              <?php foreach ($documentCategories as $category): ?>
                  <option value="<?= $category['id'] ?>" <?= (isset($_GET['category_filter']) && $_GET['category_filter'] == $category['id']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($category['name']) ?>
                  </option>
              <?php endforeach; ?>
          </select>
      </form>
      <?php endif; ?>
    </div>
  </div>

  <div class="mt-6 overflow-x-auto">
    <table class="min-w-full">
      <thead class="bg-white/10 backdrop-blur">
        <tr>
          <th class="px-4 py-3 text-left text-sm font-medium text-secondary">Titel</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-secondary">Typ</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-secondary">Kategorie</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-secondary">Status</th>
          <th class="px-4 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-white/10">
        <?php if (empty($docs)): ?>
          <tr><td colspan="5" class="p-4 text-center text-muted">Keine Dokumente gefunden.</td></tr>
        <?php else: foreach($docs as $d): ?>
          <tr class="hover:bg-white/5 transition-colors">
            <td class="px-4 py-3 text-secondary"><?= htmlspecialchars($d['title'] ?? '') ?></td>
            <td class="px-4 py-3 text-secondary"><?= htmlspecialchars($d['doc_type'] ?? '') ?></td>
            <td class="px-4 py-3 text-secondary"><?= htmlspecialchars($d['category_name'] ?? 'N/A') ?></td>
            <td class="px-4 py-3 text-secondary"><?= ($d['is_deleted'] ?? false) ? 'Gelöscht' : 'Aktiv' ?></td>
            <td class="px-4 py-3 text-right space-x-2">
              <a href="/uploads/<?= urlencode($d['filename'] ?? '') ?>" download class="text-blue-300 hover:text-blue-200 hover:underline">Download</a>
              <?php
              // Preserve category filter and subtab when deleting
              $deleteParams = ['tab' => 'documents', 'delete' => $d['id']];
              // Use the same subtab variable as used for the filter form
              $currentSubtabForDelete = $_GET['subtab'] ?? 'documents';
              $deleteParams['subtab'] = $currentSubtabForDelete;
              
              if (isset($_GET['category_filter']) && $_GET['category_filter'] !== '') {
                $deleteParams['category_filter'] = $_GET['category_filter'];
              }
              $deleteQuery = http_build_query($deleteParams);
              ?>
              <a href="?<?= $deleteQuery ?>" class="text-red-300 hover:text-red-200 hover:underline">Löschen</a>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
// Title filter functionality with debounce
let titleFilterTimeout;
document.getElementById('title_filter').addEventListener('input', function(e) {
    clearTimeout(titleFilterTimeout);
    titleFilterTimeout = setTimeout(() => {
        const currentUrl = new URL(window.location.href);
        if (e.target.value.trim()) {
            currentUrl.searchParams.set('title_filter', e.target.value.trim());
        } else {
            currentUrl.searchParams.delete('title_filter');
        }
        window.location.href = currentUrl.toString();
    }, 500); // 500ms debounce
});
</script>
