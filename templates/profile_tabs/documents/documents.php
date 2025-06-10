<!-- templates/profile_tabs/documents.php -->
<div class="bg-card-bg rounded-xl shadow-card-lg p-6 space-y-4">
  <h2 class="text-xl font-semibold text-text">Dokumente</h2>

  <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 mb-4">
    <a href="upload.php"
       class="inline-block px-6 py-2 bg-primary text-white rounded-lg shadow hover:bg-primary-dark transition">
      Neues Dokument
    </a>

    <div class="flex flex-col sm:flex-row gap-4">
      <!-- Title Filter -->
      <div class="flex items-center space-x-2">
        <label for="title_filter" class="text-sm font-medium text-text-secondary">Nach Titel:</label>
        <input type="text" 
               id="title_filter" 
               placeholder="Titel durchsuchen..." 
               value="<?= htmlspecialchars($_GET['title_filter'] ?? '') ?>"
               class="rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary-light focus:ring-opacity-50 text-sm py-2 px-3 min-w-[200px]">
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
          
          <label for="category_filter" class="text-sm font-medium text-text-secondary">Kategorie:</label>
          <select name="category_filter" id="category_filter" 
                  class="rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary-light focus:ring-opacity-50 text-sm py-2 px-3"
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
    <table class="min-w-full divide-y divide-border">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 text-left text-sm font-medium text-text-secondary">Titel</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-text-secondary">Typ</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-text-secondary">Kategorie</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-text-secondary">Status</th>
          <th class="px-4 py-2"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-border">
        <?php if (empty($docs)): ?>
          <tr><td colspan="5" class="p-4 text-center text-text-secondary">Keine Dokumente gefunden.</td></tr>
        <?php else: foreach($docs as $d): ?>
          <tr>
            <td class="px-4 py-2 text-text"><?= htmlspecialchars($d['title']) ?></td>
            <td class="px-4 py-2 text-text"><?= htmlspecialchars($d['doc_type']) ?></td>
            <td class="px-4 py-2 text-text"><?= htmlspecialchars($d['category_name'] ?? 'N/A') ?></td>
            <td class="px-4 py-2 text-text"><?= $d['is_deleted'] ? 'Gelöscht' : 'Aktiv' ?></td>
            <td class="px-4 py-2 text-right space-x-2">
              <a href="/uploads/<?= urlencode($d['filename']) ?>" download class="text-primary hover:underline">Download</a>
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
              <a href="?<?= $deleteQuery ?>" class="text-red-600 hover:underline">Löschen</a>
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
