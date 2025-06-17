<!-- templates/login_form.php -->
<!DOCTYPE html>
<html lang="de" class="h-full">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { 
      font-family: 'Inter', sans-serif;
      background: var(--current-theme-bg, linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%));
      min-height: 100vh;
      transition: background 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .glassmorphism-container {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    
    .form-input {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      transition: all 0.3s ease;
    }
    
    .form-input:focus {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.4);
      outline: none;
      box-shadow: 0 0 20px rgba(255, 255, 255, 0.1);
    }
    
    .form-input::placeholder {
      color: rgba(255, 255, 255, 0.6);
    }
    
    .primary-button {
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.1) 100%);
      border: 1px solid rgba(255, 255, 255, 0.3);
      color: white;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .primary-button:hover {
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.3) 0%, rgba(255, 255, 255, 0.15) 100%);
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }
    
    .error-message {
      background: rgba(220, 38, 38, 0.2);
      border: 1px solid rgba(220, 38, 38, 0.4);
      color: #fca5a5;
      backdrop-filter: blur(10px);
    }
  </style>
  
  <script>
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
        volcanic: 'linear-gradient(135deg, #2c1810 0%, #8b0000 50%, #ff4500 100%)'
      };
      
      if (themes[savedTheme]) {
        document.body.style.background = themes[savedTheme];
      }
    });
  </script>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
  <div class="w-full max-w-md glassmorphism-container rounded-2xl p-8">
    <!-- Logo -->
    <div class="flex justify-center mb-8">
      <a href="/index.php" class="flex flex-col items-center">
        <img src="/assets/logo.png" alt="Private Vault" class="h-16 w-auto mb-3" style="filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.3));">
        <span class="text-2xl font-bold text-white">Private Vault</span>
      </a>
    </div>

    <?php if (!empty($error)): ?>
      <div class="mb-6 p-4 error-message rounded-xl text-sm">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form action="login.php" method="post" class="space-y-6">
      <input type="hidden" name="csrf_token_login" value="<?php echo htmlspecialchars($csrf_token_login ?? ''); ?>">
      
      <div>
        <label for="username" class="block text-sm font-medium text-white mb-2">Benutzername</label>
        <input type="text" id="username" name="username" required 
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
               placeholder="Geben Sie Ihren Benutzernamen ein"
               class="w-full px-4 py-3 form-input rounded-lg" />
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-white mb-2">Passwort</label>
        <input type="password" id="password" name="password" required 
               placeholder="Geben Sie Ihr Passwort ein"
               class="w-full px-4 py-3 form-input rounded-lg" />
      </div>

      <button type="submit" class="w-full primary-button py-3 px-4 rounded-lg">
        Einloggen
      </button>
    </form>

    <p class="mt-8 text-sm text-center text-white opacity-80">
      Noch keinen Account?
      <a href="register.php" class="text-white font-medium hover:underline opacity-100">Registrieren</a>
    </p>
  </div>
</body>
</html>
