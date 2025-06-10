<!-- templates/login_form.php -->
<!DOCTYPE html>
<html lang="de" class="h-full">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/assets/css/apple-ui.css">
  <style>
    body {
      background: linear-gradient(135deg, #eef7ff 0%, #f9fdf2 100%) fixed;
    }
    
    .login-card {
      animation: fadeIn 0.6s ease-out;
    }
    
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
  <div class="w-full max-w-md glass-card p-8 login-card">
    <!-- Logo -->
    <div class="flex justify-center mb-8">
      <a href="/index.php" class="flex items-center">
        <div class="h-16 w-16 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg">
          OMNI
        </div>
      </a>
    </div>
    
    <!-- Login Form -->
    <h1 class="text-2xl font-bold text-center mb-6 text-gray-900">Login to Your Account</h1>
    
    <?php if (isset($error_message)): ?>
      <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md">
        <p><?= htmlspecialchars($error_message) ?></p>
      </div>
    <?php endif; ?>
    
    <form action="/login.php" method="post" class="space-y-4">
      <div>
        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
        <input type="text" id="username" name="username" required class="glass-input w-full" 
               value="<?= htmlspecialchars($username ?? '') ?>">
      </div>
      
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input type="password" id="password" name="password" required class="glass-input w-full">
      </div>
      
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <input type="checkbox" id="remember" name="remember" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
          <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
        </div>
        <a href="/forgot_password.php" class="text-sm text-indigo-600 hover:text-indigo-500">Forgot password?</a>
      </div>
      
      <button type="submit" class="w-full glass-button-primary py-2 px-4 rounded-lg font-medium">
        Sign In
      </button>
    </form>
    
    <div class="mt-6 text-center">
      <p class="text-sm text-gray-600">Don't have an account? 
        <a href="/register.php" class="font-medium text-indigo-600 hover:text-indigo-500">Register</a>
      </p>
    </div>
  </div>
</body>
</html>
