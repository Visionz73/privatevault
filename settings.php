<?php
// settings.php
require_once __DIR__ . '/src/lib/auth.php';
requireLogin();
$user = getUser();
$pageTitle = 'Einstellungen';

// Simulate fetching settings or use actual logic
$settings = ['theme' => 'dark', 'language' => 'de'];
?>
<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | Private Vault</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#eef7ff] via-[#f7fbff] to-[#f9fdf2] flex">

<?php require_once __DIR__ . '/templates/navbar.php'; ?>

<div class="ml-0 md:ml-64 flex-1 p-4 md:p-8 mt-14 md:mt-0">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Einstellungen</h1>

        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <form>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="theme">
                        Theme
                    </label>
                    <select id="theme" name="theme" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="light" <?php echo ($settings['theme'] ?? '') === 'light' ? 'selected' : ''; ?>>Light</option>
                        <option value="dark" <?php echo ($settings['theme'] ?? '') === 'dark' ? 'selected' : ''; ?>>Dark</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="language">
                        Sprache
                    </label>
                    <select id="language" name="language" class="shadow border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="en" <?php echo ($settings['language'] ?? '') === 'en' ? 'selected' : ''; ?>>English</option>
                        <option value="de" <?php echo ($settings['language'] ?? '') === 'de' ? 'selected' : ''; ?>>Deutsch</option>
                    </select>
                </div>

                <div class="flex items-center justify-between">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="button">
                        Speichern
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
