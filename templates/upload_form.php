<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dokument hochladen | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: 'Inter', sans-serif; }
    @media (max-width: 768px) {
      main { margin-top: 3.5rem; }
    }
    .file-drop-area {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
      border: 2px dashed #e5e7eb;
      border-radius: 0.5rem;
      transition: all 0.2s ease;
    }
    .file-drop-area.is-active {
      border-color: #4A90E2;
      background-color: rgba(74, 144, 226, 0.05);
    }
    .file-input {
      position: absolute;
      left: 0;
      top: 0;
      height: 100%;
      width: 100%;
      cursor: pointer;
      opacity: 0;
    }
    .file-msg {
      font-size: 0.9rem;
      line-height: 1.4;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#eef7ff] via-[#f7fbff] to-[#f9fdf2] flex flex-col">
  <?php require_once __DIR__.'/navbar.php'; ?>
  
  <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
    <header class="mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Dokument hochladen</h1>
      <p class="mt-2 text-gray-600">Laden Sie ein neues Dokument in Ihre private Ablage hoch.</p>
    </header>
    
    <?php if (!empty($uploadSuccess)): ?>
      <div class="mb-6 bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
        <div class="flex items-center">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
          <?= htmlspecialchars($uploadSuccess) ?>
        </div>
      </div>
    <?php endif; ?>
    
    <?php if (!empty($uploadError)): ?>
      <div class="mb-6 bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
        <div class="flex items-center">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
          </svg>
          <?= htmlspecialchars($uploadError) ?>
        </div>
      </div>
    <?php endif; ?>
    
    <!-- Upload Form -->
    <form action="upload.php" method="post" enctype="multipart/form-data" class="bg-white/60 backdrop-blur-sm rounded-xl shadow-sm p-6">
      <div class="mb-6">
        <label for="document_title" class="block text-sm font-medium text-gray-700 mb-2">Dokumenttitel</label>
        <input type="text" id="document_title" name="document_title" required
               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
      </div>
      
      <div class="mb-6">
        <label for="document_category" class="block text-sm font-medium text-gray-700 mb-2">Kategorie</label>
        <select id="document_category" name="document_category"
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
          <option value="">-- Bitte wählen --</option>
          <?php foreach ($categories ?? [] as $category): ?>
            <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Dokument</label>
        <div class="file-drop-area">
          <span class="file-msg">Datei hierher ziehen oder klicken zum Auswählen</span>
          <input type="file" name="document_file" class="file-input" required>
        </div>
      </div>
      
      <div class="flex justify-end">
        <button type="submit" class="px-6 py-2 bg-[#4A90E2] text-white rounded-lg hover:bg-[#4A90E2]/90 transition-colors">
          Hochladen
        </button>
      </div>
    </form>
  </main>

  <script>
    // File upload interactive functionality
    const fileDropArea = document.querySelector('.file-drop-area');
    const fileInput = fileDropArea.querySelector('.file-input');
    const fileMsg = fileDropArea.querySelector('.file-msg');
    
    fileInput.addEventListener('change', function() {
      if (this.files && this.files[0]) {
        fileMsg.textContent = this.files[0].name;
      }
    });
    
    ['dragover', 'dragenter'].forEach(event => {
      fileDropArea.addEventListener(event, function(e) {
        e.preventDefault();
        this.classList.add('is-active');
      });
    });
    
    ['dragleave', 'dragend', 'drop'].forEach(event => {
      fileDropArea.addEventListener(event, function(e) {
        e.preventDefault();
        this.classList.remove('is-active');
      });
    });
    
    fileDropArea.addEventListener('drop', function(e) {
      fileInput.files = e.dataTransfer.files;
      if (fileInput.files && fileInput.files[0]) {
        fileMsg.textContent = fileInput.files[0].name;
      }
    });
  </script>
</body>
</html>
