<!-- templates/login_form.php -->
<!DOCTYPE html>
<html lang="de" class="h-full">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login | Private Vault</title>

  <!-- Moderne Schrift: Inter -->
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap"
    rel="stylesheet"
  />

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    body {
      font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont,
                   'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    }
  </style>
</head>

<body
  class="min-h-screen flex items-center justify-center
         bg-gradient-to-br from-indigo-50 via-blue-50 to-purple-50 p-6"
>
  <div
    class="w-full max-w-md bg-white/80 backdrop-blur-sm
           border border-white/60 rounded-3xl
           shadow-2xl p-8 relative overflow-hidden"
  >
    <!-- Hintergrund-Gradienten -->
    <div
      class="absolute -top-20 -left-20 w-64 h-64
             bg-gradient-to-tr from-primary/40 to-transparent rounded-full filter blur-2xl"
    ></div>
    <div
      class="absolute -bottom-20 -right-16 w-80 h-80
             bg-gradient-to-bl from-primary-dark/30 to-transparent rounded-full filter blur-2xl"
    ></div>

    <!-- Icon -->
    <div class="flex justify-center mb-6 relative z-10">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-16 w-16 text-primary"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        stroke-width="2"
      >
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
    </div>

    <!-- Überschrift -->
    <h1
      class="text-center text-3xl font-bold text-gray-900 mb-8 relative z-10"
    >
      Anmelden
    </h1>

    <!-- Fehlermeldung -->
    <?php if (!empty($error)): ?>
      <div
        class="mb-6 p-4 bg-red-100 border border-red-300 text-red-800
               rounded-md relative z-10 shadow"
      >
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <!-- Login-Formular -->
    <form action="login.php" method="post" class="space-y-6 relative z-10">
      <!-- Benutzername -->
      <div>
        <label
          for="username"
          class="block text-sm font-semibold text-gray-900 mb-2"
          >Benutzername</label
        >
        <input
          id="username"
          name="username"
          type="text"
          required
          value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
          placeholder="dein Name"
          class="w-full px-5 py-3 rounded-xl bg-white text-gray-900
                 placeholder-gray-500 border border-gray-300
                 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition shadow-inner"
        />
      </div>

      <!-- Passwort -->
      <div>
        <label
          for="password"
          class="block text-sm font-semibold text-gray-900 mb-2"
          >Passwort</label
        >
        <input
          id="password"
          name="password"
          type="password"
          required
          placeholder="••••••••"
          class="w-full px-5 py-3 rounded-xl bg-white text-gray-900
                 placeholder-gray-500 border border-gray-300
                 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition shadow-inner"
        />
      </div>

      <!-- Submit -->
      <button
        type="submit"
        class="w-full py-3 rounded-xl bg-primary text-gray-900 font-semibold
               uppercase tracking-wide shadow-2xl hover:bg-primary-dark
               hover:shadow-inner transition"
      >
        Einloggen
      </button>
    </form>

    <!-- Link zur Registrierung -->
    <p class="text-center text-sm text-gray-800 mt-6 relative z-10">
      Noch keinen Account?
      <a href="register.php" class="underline text-primary-dark hover:text-primary"
        >Registrieren</a
      >
    </p>
  </div>
</body>
</html>
