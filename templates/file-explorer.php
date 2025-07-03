<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Datei-Explorer | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/public/assets/css/file-explorer.css" />
</head>
<body class="bg-gradient-to-br from-gray-800 via-indigo-800 to-gray-900 font-inter text-white h-full">
  <?php require_once __DIR__ . '/../templates/navbar.php'; ?>
  <div class="file-explorer-layout flex h-full">
    <!-- Sidebar -->
    <aside class="sidebar-glass hidden lg:flex flex-col p-6 gap-6">
      <h2 class="liquid-glass-header p-4 text-xl font-semibold">Datei-Explorer</h2>
      <input id="searchInput" type="text" placeholder="Suche..." class="liquid-glass p-2 rounded-lg focus:outline-none bg-white/10 text-white placeholder-white/60" />
      <nav id="categoryList" class="flex-1 overflow-auto space-y-4"></nav>
      <select id="sortSelect" class="liquid-glass p-2 rounded-lg text-white">
        <option value="upload_date_DESC">Neueste</option>
        <option value="upload_date_ASC">Älteste</option>
        <option value="filename_ASC">Name A-Z</option>
        <option value="filename_DESC">Name Z-A</option>
        <option value="file_size_DESC">Grösste</option>
        <option value="file_size_ASC">Kleinste</option>
      </select>
    </aside>
    <!-- Main Content -->
    <main class="flex-1 p-6 overflow-auto">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Meine Dateien</h1>
        <div class="flex gap-2">
          <button id="gridViewBtn" class="view-toggle-btn active liquid-glass p-2 rounded-lg"><i class="fas fa-th"></i></button>
          <button id="listViewBtn" class="view-toggle-btn liquid-glass p-2 rounded-lg"><i class="fas fa-list"></i></button>
        </div>
      </div>
      <div id="fileGrid" class="file-grid"></div>
      <div id="fileList" class="hidden space-y-4"></div>
    </main>
  </div>
  <script src="/public/assets/js/file-explorer.js"></script>
</body>
</html>
