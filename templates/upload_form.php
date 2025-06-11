<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dokument hochladen | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { 
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
      min-height: 100vh;
    }
    @media (max-width: 768px) {
      main { margin-top: 4rem; }
    }
    
    .glassmorphism-container {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1.5rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    
    .file-drop-area {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 3rem;
      border: 2px dashed rgba(255, 255, 255, 0.3);
      border-radius: 1rem;
      transition: all 0.3s ease;
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
    }
    .file-drop-area.is-active {
      border-color: rgba(147, 51, 234, 0.6);
      background: rgba(147, 51, 234, 0.1);
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
      font-size: 1rem;
      line-height: 1.5;
      color: rgba(255, 255, 255, 0.8);
      text-align: center;
    }
    
    .form-input {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      border-radius: 0.75rem;
      transition: all 0.3s ease;
    }
    .form-input:focus {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.4);
      outline: none;
      box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
    }
    .form-input::placeholder {
      color: rgba(255, 255, 255, 0.5);
    }
    
    .btn-primary {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.8) 0%, rgba(79, 70, 229, 0.8) 100%);
      color: white;
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 0.75rem;
      transition: all 0.3s ease;
    }
    .btn-primary:hover {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.9) 0%, rgba(79, 70, 229, 0.9) 100%);
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(147, 51, 234, 0.3);
    }
    
    .success-message {
      background: rgba(34, 197, 94, 0.2);
      border: 1px solid rgba(34, 197, 94, 0.3);
      color: #86efac;
      border-radius: 0.75rem;
    }
    
    .error-message {
      background: rgba(239, 68, 68, 0.2);
      border: 1px solid rgba(239, 68, 68, 0.3);
      color: #fca5a5;
      border-radius: 0.75rem;
    }
    
    .header-text {
      color: white;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }
    
    .text-secondary {
      color: rgba(255, 255, 255, 0.8);
    }
  </style>
</head>
<body class="min-h-screen flex">
  <?php require_once __DIR__.'/navbar.php'; ?>
  
  <main class="ml-0 mt-16 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
    <div class="max-w-2xl mx-auto">
      <header class="mb-8">
        <h1 class="text-3xl font-bold header-text">Dokument hochladen</h1>
        <p class="mt-2 text-secondary">Laden Sie ein neues Dokument in Ihre private Ablage hoch.</p>
      </header>
      
      <!-- Upload Form Container -->
      <div class="glassmorphism-container p-8">
        <form method="post" enctype="multipart/form-data" class="space-y-6">
          <!-- File Drop Area -->
          <div class="file-drop-area">
            <input type="file" name="file" class="file-input" required>
            <div class="file-msg text-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-4 text-white/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
              </svg>
              <p class="mb-2">Datei hier ablegen oder klicken zum Auswählen</p>
              <p class="text-xs text-white/60">Unterstützte Formate: PDF, DOC, DOCX, JPG, PNG (Max. 10MB)</p>
            </div>
          </div>
          
          <!-- Title Input -->
          <div>
            <label for="title" class="block text-sm font-medium text-white mb-2">Titel</label>
            <input type="text" name="title" id="title" required 
                   class="form-input w-full px-4 py-3" 
                   placeholder="Dokumententitel eingeben...">
          </div>
          
          <!-- Description Textarea -->
          <div>
            <label for="description" class="block text-sm font-medium text-white mb-2">Beschreibung</label>
            <textarea name="description" id="description" rows="4" 
                      class="form-input w-full px-4 py-3" 
                      placeholder="Optionale Beschreibung..."></textarea>
          </div>
          
          <!-- Submit Button -->
          <div class="flex justify-end">
            <button type="submit" class="btn-primary px-6 py-3">
              Dokument hochladen
            </button>
          </div>
        </form>
      </div>
      
      <?php if (!empty($uploadSuccess)): ?>
        <div class="mb-6 success-message px-4 py-3 mt-6">
          Dokument erfolgreich hochgeladen!
        </div>
      <?php endif; ?>
    </div>
  </main>

  <script>
    // File drop functionality
    const dropArea = document.querySelector('.file-drop-area');
    const fileInput = document.getElementById('docfile');
    const fileInfo = document.getElementById('file-info');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
      dropArea.addEventListener(eventName, preventDefaults, false);
    });

    ['dragenter', 'dragover'].forEach(eventName => {
      dropArea.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
      dropArea.addEventListener(eventName, unhighlight, false);
    });

    dropArea.addEventListener('drop', handleDrop, false);

    function preventDefaults(e) {
      e.preventDefault();
      e.stopPropagation();
    }

    function highlight(e) {
      dropArea.classList.add('is-active');
    }

    function unhighlight(e) {
      dropArea.classList.remove('is-active');
    }

    function handleDrop(e) {
      const dt = e.dataTransfer;
      const files = dt.files;
      fileInput.files = files;
      updateFileInfo(files[0]);
    }

    fileInput.addEventListener('change', function(e) {
      if (e.target.files.length > 0) {
        updateFileInfo(e.target.files[0]);
      }
    });

    function updateFileInfo(file) {
      if (file) {
        fileInfo.textContent = `Ausgewählt: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
        fileInfo.classList.remove('hidden');
      }
    }
  </script>
</body>
</html>
