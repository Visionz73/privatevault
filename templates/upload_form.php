<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dokument hochladen | Private Vault</title>
  <!-- Inter Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet"/>
  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: 'Inter', sans-serif; }
    @media (max-width: 768px) {
      main { margin-top: 3.5rem; }
      .container { margin: 1rem; }
    }
  </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-[#eef7ff] via-[#f7fbff] to-[#f9fdf2] flex flex-col">

  <?php require_once __DIR__.'/../templates/navbar.php'; ?>

  <main class="flex-1 flex items-center justify-center p-4">
    <div class="relative w-full max-w-md bg-white/80 backdrop-blur-sm border border-white/60 rounded-3xl shadow-2xl overflow-hidden">
      <!-- Deko -->
      <div class="absolute -top-24 -left-24 w-72 h-72 bg-gradient-to-tr from-[#4A90E2]/40 to-transparent rounded-full blur-2xl"></div>
      <div class="absolute -bottom-24 -right-24 w-80 h-80 bg-gradient-to-bl from-[#357ABD]/40 to-transparent rounded-full blur-2xl"></div>

      <div class="relative z-10 p-8">
        <h1 class="text-center text-3xl font-extrabold text-gray-900 mb-6">Neues Dokument</h1>

        <?php if (!empty($uploadError)): ?>
          <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg shadow-inner">
            <?= htmlspecialchars($uploadError) ?>
          </div>
        <?php elseif (!empty($uploadSuccess)): ?>
          <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg shadow-inner">
            <?= htmlspecialchars($uploadSuccess) ?>
          </div>
        <?php endif; ?>

        <form action="upload.php" method="post" enctype="multipart/form-data" class="space-y-5">
          <!-- Titel -->
          <div>
            <label for="title" class="block text-sm font-semibold text-indigo-900 mb-2">Titel</label>
            <input
              type="text"
              id="title"
              name="title"
              required
              value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
              class="w-full px-5 py-3 rounded-2xl bg-gray-100 border border-gray-300 focus:ring-2 focus:ring-[#4A90E2] focus:border-transparent transition-shadow shadow-inner"
            />
          </div>

          <!-- Kategorie -->
          <div>
            <label for="category_id" class="block text-sm font-semibold text-indigo-900 mb-2">Kategorie</label>
            <select
              id="category_id"
              name="category_id"
              required
              class="w-full px-5 py-3 rounded-2xl bg-gray-100 border border-gray-300 focus:ring-2 focus:ring-[#4A90E2] focus:border-transparent transition-shadow shadow-inner"
            >
              <option value="">– bitte auswählen –</option>
              <?php foreach ($cats as $c): ?>
                <option value="<?= $c['id'] ?>"
                  <?= (($_POST['category_id'] ?? '') == $c['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($c['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Datei -->
          <div>
            <label for="docfile" class="block text-sm font-semibold text-indigo-900 mb-2">Datei hochladen</label>
            <label for="docfile"
                   class="flex items-center justify-center cursor-pointer px-5 py-3 border-2 border-dashed border-gray-300 rounded-2xl bg-white hover:bg-gray-50 transition">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 mr-2" fill="none"
                   viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round"
                   stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 12v8m0-8l-3 3m3-3l3 3M4 12h16" /></svg>
              <span class="text-sm text-gray-600">Datei auswählen …</span>
              <input type="file" id="docfile" name="docfile" required class="hidden"/>
            </label>
          </div>

          <!-- Knopf -->
          <button
            type="submit"
            class="w-full py-3 font-semibold text-white uppercase rounded-2xl bg-gradient-to-r from-[#4A90E2] to-[#357ABD] hover:from-[#357ABD] hover:to-[#4A90E2] transition-transform transform hover:scale-105 shadow-lg"
          >
            <span class="inline-flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                   viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round"
                   stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h10a4 4 0 004-4M16 8l-4-4m0 0L8 8m4-4v12" /></svg>
              Hochladen
            </span>
          </button>
        </form>

        <p class="text-center text-sm text-gray-700 mt-5">
          <a href="dashboard.php" class="underline hover:text-indigo-600">Zurück zum Dashboard</a>
        </p>
      </div>
    </div>
  </main>
</body>
</html>
