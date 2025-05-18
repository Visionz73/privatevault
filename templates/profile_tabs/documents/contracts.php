<?php
// templates/profile_tabs/documents/contracts.php
require_once __DIR__ . '/../../../src/lib/db.php';
require_once __DIR__ . '/../../../src/lib/auth.php';
requireLogin();

$userId = $_SESSION['user_id'];
$view   = $_GET['view'] ?? 'grid';

// Verträge laden
$stmt = $pdo->prepare(
  'SELECT d.id, d.title, d.filename, d.end_date
     FROM documents d
     JOIN document_categories c ON c.id = d.category_id
    WHERE d.user_id = ? AND c.name = "Verträge" AND d.is_deleted = 0
 ORDER BY d.upload_date DESC'
);
$stmt->execute([$userId]);
$docs = $stmt->fetchAll(PDO::FETCH_ASSOC);

function isImg($f) {
  return preg_match('/\.(png|jpe?g|gif|webp)$/i', $f);
}
?>

<div class="bg-white rounded-xl shadow p-6 space-y-6">
  <div class="flex justify-between items-center">
    <h3 class="text-xl font-semibold text-gray-900">Meine Verträge</h3>
    <div class="inline-flex text-sm border rounded-lg overflow-hidden">
      <a href="?tab=documents&subtab=contracts&view=list"
         class="px-3 py-1 <?= $view==='list' ? 'bg-[#4A90E2] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' ?>">
        Liste
      </a>
      <a href="?tab=documents&subtab=contracts&view=grid"
         class="px-3 py-1 <?= $view==='grid' ? 'bg-[#4A90E2] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' ?>">
        Kachel
      </a>
    </div>
  </div>

  <?php if (empty($docs)): ?>

    <p class="text-gray-500">Keine Verträge gefunden.</p>

  <?php elseif ($view === 'list'): ?>

    <ul class="divide-y divide-gray-200 text-sm">
      <?php foreach ($docs as $d): ?>
        <li class="py-3 flex justify-between items-center">
          <a href="<?= isImg($d['filename']) ? 'download.php?id=' . $d['id'] : 'view_pdf.php?id=' . $d['id'] ?>"
             class="truncate text-[#4A90E2] hover:underline" 
             <?= isImg($d['filename']) ? 'target="_blank"' : '' ?>>
            <?= htmlspecialchars($d['title']) ?>
          </a>
          <?php if ($d['end_date']): ?>
            <span class="text-xs text-gray-400">
              <?= date('d.m.Y', strtotime($d['end_date'])) ?>
            </span>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>

  <?php else: ?>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
      <?php foreach ($docs as $d): ?>
        <a href="download.php?id=<?= $d['id'] ?>" target="_blank"
           class="group relative block w-full aspect-square rounded-lg overflow-hidden shadow">

          <?php if (isImg($d['filename'])): ?>
            <div style="background-image:url('../uploads/<?= urlencode($d['filename']) ?>')"
                 class="absolute inset-0 bg-cover bg-center"></div>
          <?php else: ?>
            <div class="absolute inset-0 bg-gray-300 flex items-center justify-center
                        text-gray-600 text-4xl font-bold">PDF</div>
          <?php endif; ?>

          <div class="absolute inset-0 bg-white/30 backdrop-blur-sm transition
                      group-hover:bg-white/10"></div>
          <div class="absolute bottom-0 left-0 right-0 bg-white/70 backdrop-blur p-2
                      text-[13px] text-gray-800 truncate">
            <?= htmlspecialchars($d['title']) ?>
          </div>
        </a>
      <?php endforeach; ?>
    </div>

  <?php endif; ?>
</div>
