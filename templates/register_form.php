<!-- templates/register_form.php -->
<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registrieren | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
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
    <div class="text-center mb-6">
      <div class="flex justify-center mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
        </svg>
      </div>
      <h1 class="text-2xl font-bold text-white">Konto erstellen</h1>
      <p class="text-white opacity-80 mt-2">Erstellen Sie Ihr persönliches Konto bei Private Vault</p>
    </div>
    
    <?php if (!empty($errors)): ?>
      <div class="mb-6 p-4 error-message rounded-xl text-sm">
        <?php foreach ($errors as $error): ?>
          <div><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    
    <form action="register.php" method="post" class="space-y-6">
      <input type="hidden" name="csrf_token_register" value="<?php echo htmlspecialchars($csrf_token_register ?? ''); ?>">
      
      <div>
        <label for="username" class="block text-sm font-medium text-white mb-2">Benutzername</label>
        <input type="text" id="username" name="username" required 
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
               placeholder="Wählen Sie einen Benutzernamen"
               class="w-full px-4 py-3 form-input rounded-lg" />
      </div>
      
      <div>
        <label for="email" class="block text-sm font-medium text-white mb-2">E-Mail-Adresse</label>
        <input type="email" id="email" name="email" required 
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
               placeholder="Geben Sie Ihre E-Mail ein"
               class="w-full px-4 py-3 form-input rounded-lg" />
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-white mb-2">Passwort</label>
        <input type="password" id="password" name="password" required 
               placeholder="Mindestens 8 Zeichen mit Groß-, Kleinbuchstaben, Zahl und Sonderzeichen"
               class="w-full px-4 py-3 form-input rounded-lg" />
      </div>

      <div>
        <label for="confirm_password" class="block text-sm font-medium text-white mb-2">Passwort bestätigen</label>
        <input type="password" id="confirm_password" name="confirm_password" required 
               placeholder="Passwort wiederholen"
               class="w-full px-4 py-3 form-input rounded-lg" />
      </div>

      <button type="submit" class="w-full primary-button py-3 px-4 rounded-lg">
        Registrieren
      </button>
    </form>

    <p class="mt-8 text-sm text-center text-white opacity-80">
      Bereits registriert? <a href="login.php" class="text-white font-medium hover:underline opacity-100">Anmelden</a>
    </p>
  </div>
</body>
</html>
