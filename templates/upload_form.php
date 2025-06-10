<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dokument hochladen | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/assets/css/apple-ui.css">
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
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <div class="p-6">
        <form method="post" enctype="multipart/form-data" class="space-y-5">
          <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titel *</label>
            <input type="text" id="title" name="title" required
                   placeholder="Geben Sie einen Titel für das Dokument ein"
                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2] transition-colors">
          </div>
          
          <div>
            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Kategorie *</label>
            <select id="category_id" name="category_id" required
                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2] transition-colors">
              <option value="">Bitte wählen Sie eine Kategorie</option>
              <?php foreach ($cats as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Dokument *</label>
            <div class="file-drop-area rounded-lg bg-gray-50">
              <span class="file-msg text-gray-500">Datei hierher ziehen oder klicken zum Auswählen</span>
              <input type="file" id="docfile" name="docfile" required accept=".pdf,.png,.jpeg,.jpg,.docx" class="file-input">
            </div>
            <p class="mt-2 text-xs text-gray-500">Erlaubte Dateitypen: PDF, PNG, JPEG, JPG, DOCX</p>
          </div>
          
          <div class="pt-4">
            <button type="submit" class="w-full md:w-auto px-6 py-3 bg-[#4A90E2] text-white font-medium rounded-lg shadow hover:bg-[#4A90E2]/90 transition-colors">
              <span class="flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                Dokument hochladen
              </span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </main>
  
  <script>
    // File drop area functionality
    const fileDropArea = document.querySelector('.file-drop-area');
    const fileInput = document.querySelector('.file-input');
    const fileMsg = document.querySelector('.file-msg');
    
    // Highlight drop area when dragging file over it
    ['dragover', 'dragenter'].forEach(eventName => {
      fileDropArea.addEventListener(eventName, function(e) {
        e.preventDefault();
        this.classList.add('is-active');
      });
    });
    
    ['dragleave', 'dragend'].forEach(eventName => {
      fileDropArea.addEventListener(eventName, function() {
        this.classList.remove('is-active');
      });
    });
    
    // Handle file selection and display filename
    fileInput.addEventListener('change', function() {
      if (this.files && this.files.length) {
        fileMsg.textContent = this.files[0].name;
      }
    });
    
    // Handle file drop
    fileDropArea.addEventListener('drop', function(e) {
      e.preventDefault();
      this.classList.remove('is-active');
      
      if (e.dataTransfer.files && e.dataTransfer.files.length) {
        fileInput.files = e.dataTransfer.files;
        fileMsg.textContent = e.dataTransfer.files[0].name;
      }
    });
  </script>
</body>
</html>
