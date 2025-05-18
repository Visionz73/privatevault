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
    body { font-family: 'Inter', sans-serif; }
  </style>
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#eef7ff] via-[#f7fbff] to-[#f9fdf2] p-4">
  <div class="w-full max-w-md bg-white/60 backdrop-blur-sm rounded-2xl shadow-sm p-8">
    <!-- Logo -->
    <div class="flex justify-center mb-8">
      <a href="dashboard.php" class="flex items-center">
        <img src="/assets/logo.png" alt="Logo" class="h-26 w-auto" />
        
      </a>
    </div>

    

    <?php if (!empty($error)): ?>
      <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-600 rounded-xl text-sm">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form action="login.php" method="post" class="space-y-6">
      <div>
        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Benutzername</label>
        <input type="text" id="username" name="username" required 
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
               class="w-full px-4 py-2 bg-white/80 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]" />
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Passwort</label>
        <input type="password" id="password" name="password" required 
               class="w-full px-4 py-2 bg-white/80 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]" />
      </div>

      <button type="submit" 
              class="w-full bg-[#4A90E2] text-white py-2 px-4 rounded-lg hover:bg-[#4A90E2]/90 transition-colors">
        Einloggen
      </button>
    </form>

    <p class="mt-8 text-sm text-center text-gray-600">
      Noch keinen Account?
      <a href="register.php" class="text-[#4A90E2] hover:underline">Registrieren</a>
    </p>
  </div>
</body>
</html>
