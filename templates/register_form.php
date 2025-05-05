<!-- templates/register_form.php -->
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registrierung für Mitarbeitende | Private Vault</title>

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
    class="w-full max-w-md bg-white/50 backdrop-blur-lg
           border border-white/40 rounded-3xl shadow-lg p-8 relative overflow-hidden"
  >
    <!-- Hintergrund-Gradienten -->
    <div
      class="absolute -top-20 -left-20 w-56 h-56
             bg-gradient-to-tr from-indigo-600/30 to-transparent rounded-full"
    ></div>
    <div
      class="absolute -bottom-20 -right-16 w-72 h-72
             bg-gradient-to-bl from-indigo-800/20 to-transparent rounded-full"
    ></div>

    <!-- Icon -->
    <div class="flex justify-center mb-6 relative z-10">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-16 w-16 text-indigo-600"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        stroke-width="2"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M3 15a4 4 0 004 4h10a4 4 0 000-8 5 5 0 10-9.9 1"
        />
      </svg>
    </div>

    <!-- Überschrift -->
    <h1
      class="text-center text-3xl font-bold text-indigo-900 mb-8 relative z-10"
    >
      Neues Konto erstellen
    </h1>

    <!-- Fehlermeldungen -->
    <?php if (!empty($errors)): ?>
      <div
        class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700
               rounded-md relative z-10"
      >
        <ul class="list-disc list-inside space-y-1">
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <!-- Formular -->
    <form action="register.php" method="post" class="space-y-6 relative z-10">
      <!-- E-Mail -->
      <div>
        <label
          for="email"
          class="block text-sm font-semibold text-indigo-900 mb-2"
          >E-Mail-Adresse</label
        >
        <input
          id="email"
          name="email"
          type="email"
          required
          placeholder="name@icloud.com"
          value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
          class="w-full px-5 py-3 rounded-xl bg-white text-gray-900
                 placeholder-gray-500 border border-gray-200
                 focus:outline-none focus:ring-2 focus:ring-indigo-500
                 focus:border-transparent transition"
        />
      </div>

      <!-- Benutzername -->
      <div>
        <label
          for="username"
          class="block text-sm font-semibold text-indigo-900 mb-2"
          >Benutzername</label
        >
        <input
          id="username"
          name="username"
          type="text"
          required
          placeholder="dein Name"
          value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
          class="w-full px-5 py-3 rounded-xl bg-white text-gray-900
                 placeholder-gray-500 border border-gray-200
                 focus:outline-none focus:ring-2 focus:ring-indigo-500
                 focus:border-transparent transition"
        />
      </div>

      <!-- Passwort -->
      <div>
        <label
          for="password"
          class="block text-sm font-semibold text-indigo-900 mb-2"
          >Passwort</label
        >
        <input
          id="password"
          name="password"
          type="password"
          required
          placeholder="••••••••"
          class="w-full px-5 py-3 rounded-xl bg-white text-gray-900
                 placeholder-gray-500 border border-gray-200
                 focus:outline-none focus:ring-2 focus:ring-indigo-500
                 focus:border-transparent transition"
        />
      </div>

      <!-- Passwort bestätigen -->
      <div>
        <label
          for="confirm_password"
          class="block text-sm font-semibold text-indigo-900 mb-2"
          >Passwort bestätigen</label
        >
        <input
          id="confirm_password"
          name="confirm_password"
          type="password"
          required
          placeholder="••••••••"
          class="w-full px-5 py-3 rounded-xl bg-white text-gray-900
                 placeholder-gray-500 border border-gray-200
                 focus:outline-none focus:ring-2 focus:ring-indigo-500
                 focus:border-transparent transition"
        />
      </div>

      <!-- Submit -->
      <button
        type="submit"
        class="w-full py-3 rounded-xl bg-indigo-600 text-white font-semibold
               uppercase tracking-wide shadow-md hover:bg-indigo-700
               hover:shadow-lg transition"
      >
        Account erstellen
      </button>
    </form>

    <!-- Link zum Login -->
    <p class="text-center text-sm text-indigo-800 mt-6 relative z-10">
      Bereits registriert?
      <a href="login.php" class="underline hover:text-indigo-900">Anmelden</a>
    </p>
  </div>
</body>
</html>
