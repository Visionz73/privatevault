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
      background: var(--current-theme-bg, linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%));
      min-height: 100vh;
      transition: background 0.8s cubic-bezier(0.4, 0, 0.2, 1);
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
  
  <script>
    // Listen for theme changes
    window.addEventListener('themeChanged', (e) => {
      document.documentElement.style.setProperty('--current-theme-bg', e.detail.background);
    });
    
    // Apply saved theme on load
    document.addEventListener('DOMContentLoaded', () => {
      const savedTheme = localStorage.getItem('privatevault_theme') || 'cosmic';
      const themes = {
        cosmic: 'linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%)',
        ocean: 'linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3730a3 100%)',
        sunset: 'linear-gradient(135deg, #f59e0b 0%, #dc2626 50%, #7c2d12 100%)',
        forest: 'linear-gradient(135deg, #064e3b 0%, #047857 50%, #065f46 100%)',
        purple: 'linear-gradient(135deg, #581c87 0%, #7c3aed 50%, #3730a3 100%)',
        rose: 'linear-gradient(135deg, #9f1239 0%, #e11d48 50%, #881337 100%)',
        cyber: 'linear-gradient(135deg, #065f46 0%, #0891b2 50%, #1e40af 100%)',
        ember: 'linear-gradient(135deg, #7c2d12 0%, #ea580c 50%, #92400e 100%)',
        midnight: 'linear-gradient(135deg, #111827 0%, #1f2937 50%, #374151 100%)',
        aurora: 'linear-gradient(135deg, #065f46 0%, #059669 25%, #0891b2 50%, #3b82f6 75%, #8b5cf6 100%)',
        neon: 'linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%)',
        volcanic: 'linear-gradient(135deg, #2c1810 0%, #8b0000 50%, #ff4500 100%)',
        matrix: 'linear-gradient(135deg, #0d1117 0%, #161b22 50%, #21262d 100%)',
        synthwave: 'linear-gradient(135deg, #2d1b69 0%, #8b5a97 50%, #ff006e 100%)',
        deepspace: 'linear-gradient(135deg, #0c0c0c 0%, #1a0033 50%, #4a148c 100%)',
        crimson: 'linear-gradient(135deg, #1a0000 0%, #660000 50%, #cc0000 100%)',
        arctic: 'linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%)'
      };
      
      if (themes[savedTheme]) {
        document.body.style.background = themes[savedTheme];
      }
    });
  </script>
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
          <!-- File upload area -->
          <div class="file-drop-area">
            <input type="file" class="file-input" name="document" accept=".pdf,.doc,.docx,.txt,.jpg,.png">
            <div class="file-msg">
              <i class="fas fa-cloud-upload-alt text-4xl mb-4 text-white/60"></i>
              <p class="text-lg font-medium">Datei hier ablegen oder klicken zum Auswählen</p>
              <p class="text-sm">Unterstützte Formate: PDF, DOC, DOCX, TXT, JPG, PNG</p>
            </div>
          </div>
          
          <!-- Document details -->
          <div class="space-y-4">
            <div>
              <label class="block text-white font-medium mb-2">Dokumententitel</label>
              <input type="text" name="title" class="form-input w-full px-4 py-3" placeholder="Titel eingeben...">
            </div>
            
            <div>
              <label class="block text-white font-medium mb-2">Beschreibung (optional)</label>
              <textarea name="description" rows="3" class="form-input w-full px-4 py-3" placeholder="Beschreibung eingeben..."></textarea>
            </div>
            
            <div>
              <label class="block text-white font-medium mb-2">Kategorie</label>
              <select name="category" class="form-input w-full px-4 py-3">
                <option value="">Kategorie auswählen</option>
                <option value="personal">Persönlich</option>
                <option value="financial">Finanziell</option>
                <option value="medical">Medizinisch</option>
                <option value="legal">Rechtlich</option>
                <option value="other">Sonstiges</option>
              </select>
            </div>
          </div>
          
          <!-- Submit button -->
          <div class="pt-4">
            <button type="submit" class="btn-primary w-full px-6 py-3 font-medium">
              <i class="fas fa-upload mr-2"></i>
              Dokument hochladen
            </button>
          </div>
        </form>
      </div>
    </div>
  </main>
</body>
</html>
