<!-- templates/profile_tabs/documents.php -->
<div class="bg-card-bg rounded-xl shadow-card-lg p-6 space-y-4">
  <h2 class="text-xl font-semibold text-text">Dokumente</h2>

  <a href="upload.php"
     class="inline-block px-6 py-2 bg-primary text-white rounded-lg shadow hover:bg-primary-dark transition">
    Neues Dokument
  </a>

  <div class="mt-6 overflow-x-auto">
    <table class="min-w-full divide-y divide-border">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 text-left text-sm font-medium text-text-secondary">Titel</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-text-secondary">Typ</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-text-secondary">Status</th>
          <th class="px-4 py-2"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-border">
        <?php if (empty($docs)): ?>
          <tr><td colspan="4" class="p-4 text-center text-text-secondary">Keine Dokumente</td></tr>
        <?php else: foreach($docs as $d): ?>
          <tr>
            <td class="px-4 py-2 text-text"><?= htmlspecialchars($d['title']) ?></td>
            <td class="px-4 py-2 text-text"><?= htmlspecialchars($d['doc_type']) ?></td>
            <td class="px-4 py-2 text-text"><?= $d['is_deleted'] ? 'Gelöscht' : 'Aktiv' ?></td>
            <td class="px-4 py-2 text-right space-x-2">
              <a href="/uploads/<?= urlencode($d['filename']) ?>" download class="text-primary hover:underline">Download</a>
              <a href="?tab=documents&delete=<?= $d['id'] ?>" class="text-red-600 hover:underline">Löschen</a>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
