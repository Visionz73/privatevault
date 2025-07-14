<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Datei-Explorer | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body class="min-h-screen bg-gray-100">
<?php require_once __DIR__ . '/navbar.php'; ?>

<main class="ml-0 mt-16 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
  <div class="max-w-7xl mx-auto">
    <div class="mb-4">
      <h1 class="text-2xl font-bold">Datei-Explorer</h1>
    </div>
    <!-- Breadcrumb -->
    <nav class="text-sm mb-4">
      <ol class="list-reset flex text-blue-600">
        <li><a href="data-explorer.php" class="hover:underline">Root</a></li>
        <?php foreach ($crumbs as $crumb): ?>
          <li><span class="mx-2">/</span></li>
          <li><a href="data-explorer.php?path=<?= urlencode($crumb['path']) ?>" class="hover:underline"><?= htmlspecialchars($crumb['name']) ?></a></li>
        <?php endforeach; ?>
      </ol>
    </nav>
    <!-- Forms -->
    <div class="flex gap-4 mb-6">
      <form method="POST" action="data-explorer.php?path=<?= urlencode($path) ?>" class="flex gap-2" enctype="multipart/form-data">
        <input type="text" name="new_folder" placeholder="Neuer Ordner" class="px-3 py-2 border rounded"/>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Erstellen</button>
      </form>
      <form method="POST" action="data-explorer.php?path=<?= urlencode($path) ?>" enctype="multipart/form-data">
        <input type="file" name="upload_file" class="px-3 py-2 border rounded"/>
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Hochladen</button>
      </form>
    </div>
    <!-- Directory Listing -->
    <div class="bg-white shadow rounded p-4">
      <?php if (empty($directories) && empty($files)): ?>
        <div class="text-gray-500">Dieser Ordner ist leer.</div>
      <?php else: ?>
        <table class="w-full">
          <thead>
            <tr class="border-b">
              <th class="text-left py-2">Name</th>
              <th class="text-left py-2">Typ</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($directories as $dir): ?>
            <?php $dirPath = ($path !== '' ? $path . '/' : '') . $dir; ?>
            <tr class="hover:bg-gray-100">
              <td class="py-2">
                <i class="fas fa-folder text-yellow-500 mr-2"></i>
                <a href="data-explorer.php?path=<?= urlencode($dirPath) ?>"><?= htmlspecialchars($dir) ?></a>
              </td>
              <td class="py-2">Ordner</td>
            </tr>
            <?php endforeach; ?>
            <?php foreach ($files as $file): ?>
            <?php $fileUrl = '/uploads/' . ($path !== '' ? $path . '/' : '') . $file; ?>
            <tr class="hover:bg-gray-100">
              <td class="py-2">
                <?php $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION)); ?>
                <?php if (in_array($ext, ['jpg','jpeg','png','gif','bmp','webp','svg'])): ?>
                  <i class="fas fa-image text-blue-500 mr-2"></i>
                <?php elseif (in_array($ext,['mp4','avi','mov','webm'])): ?>
                  <i class="fas fa-file-video text-red-500 mr-2"></i>
                <?php elseif ($ext === 'pdf'): ?>
                  <i class="fas fa-file-pdf text-red-700 mr-2"></i>
                <?php else: ?>
                  <i class="fas fa-file text-gray-500 mr-2"></i>
                <?php endif; ?>
                <a href="<?= htmlspecialchars($fileUrl) ?>" target="_blank"><?= htmlspecialchars($file) ?></a>
              </td>
              <td class="py-2"><?= htmlspecialchars(strtoupper($ext)) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</main>
<?php require_once __DIR__ . '/footer.php'; ?>
</body>
</html>
