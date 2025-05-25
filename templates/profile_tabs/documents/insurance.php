<?php
// templates/profile_tabs/documents/insurance.php
// Assumes $category_documents (filtered for 'Versicherungen') and $current_category_name ('Versicherungen')
// are passed from src/controllers/profile.php.
// Assumes $user is available from src/controllers/profile.php.

$view = $_GET['view'] ?? 'grid'; // View preference can still be managed by GET param

// Helper function for display (can be moved to a general helper file if used elsewhere)
if (!function_exists('isImg')) { // Define if not already defined by another included tab
    function isImg($filename) {
      return preg_match('/\.(png|jpe?g|gif|webp)$/i', $filename);
    }
}
?>

<div class="bg-white rounded-xl shadow p-6 space-y-6">
  <div class="flex justify-between items-center">
    <h3 class="text-xl font-semibold text-gray-900">
        Meine <?php echo htmlspecialchars($current_category_name ?? 'Versicherungen'); ?>
    </h3>
    <div class="inline-flex text-sm border rounded-lg overflow-hidden">
      <a href="?tab=documents&subtab=insurance&view=list"
         class="px-3 py-1 <?= $view==='list' ? 'bg-[#4A90E2] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' ?>">
        Liste
      </a>
      <a href="?tab=documents&subtab=insurance&view=grid"
         class="px-3 py-1 <?= $view==='grid' ? 'bg-[#4A90E2] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' ?>">
        Kachel
      </a>
    </div>
  </div>

  <?php if (empty($category_documents)): ?>
    <p class="text-gray-500">Keine <?php echo strtolower(htmlspecialchars($current_category_name ?? 'Versicherungen')); ?> gefunden.</p>
  <?php elseif ($view === 'list'): ?>
    <ul class="divide-y divide-gray-200 text-sm">
      <?php foreach ($category_documents as $d): ?>
        <li class="py-3 flex justify-between items-center">
          <?php
            // Use file_path if available and seems like a direct link, otherwise use download.php or view_pdf.php
            $isPdf = strtolower(pathinfo($d['filename'], PATHINFO_EXTENSION)) === 'pdf';
            $link = '';
            if ($isPdf && file_exists(__DIR__ . '/../../../../public/view_pdf.php')) { // Check if view_pdf.php exists
                $link = "view_pdf.php?id=" . $d['id'];
            } elseif (!empty($d['file_path'])) {
                // Heuristic: if file_path starts with 'http' or '/', assume it's a direct link
                // Otherwise, assume it's relative to a base uploads path. For now, use download.php as a safe fallback.
                $link = (strpos($d['file_path'], 'http') === 0 || strpos($d['file_path'], '/') === 0) 
                        ? htmlspecialchars($d['file_path']) 
                        : "download.php?id=" . $d['id']; // Fallback for non-direct paths
            } else {
                $link = "download.php?id=" . $d['id'];
            }
          ?>
          <a href="<?= $link ?>" 
             class="truncate text-[#4A90E2] hover:underline" 
             target="_blank"> 
            <?= htmlspecialchars($d['title']) ?>
          </a>
          <?php if (!empty($d['end_date'])): ?>
            <span class="text-xs text-gray-400">
              Gültig bis: <?= date('d.m.Y', strtotime($d['end_date'])) ?>
            </span>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: // Grid view ?>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
      <?php foreach ($category_documents as $d): 
            $link = !empty($d['file_path']) ? htmlspecialchars($d['file_path']) : "download.php?id=" . $d['id'];
            $previewUrl = null;
            if (isImg($d['filename'])) {
                 // Assuming file_path is web-accessible path to the image or filename is in a common web-accessible uploads dir
                 // The original template used '../uploads/' relative to public.
                 $previewUrl = '../uploads/' . urlencode($d['filename']); 
            }
      ?>
        <a href="<?= $link ?>" target="_blank"
           class="group relative block w-full aspect-square rounded-lg overflow-hidden shadow">
          <?php if ($previewUrl): ?>
            <div style="background-image:url('<?= $previewUrl ?>')"
                 class="absolute inset-0 bg-cover bg-center"></div>
          <?php else: ?>
            <div class="absolute inset-0 bg-gray-300 flex items-center justify-center
                        text-gray-600 text-4xl font-bold">
              <?= strtoupper(pathinfo($d['filename'], PATHINFO_EXTENSION)) ?>
            </div>
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
