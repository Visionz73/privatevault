<!-- templates/login_form.php -->
<!DOCTYPE html>
<html lang="de" class="h-full">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/privatevault/css/main.css">
  <style>
    body { 
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
      min-height: 100vh;
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
