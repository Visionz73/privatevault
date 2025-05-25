<?php
// settings.php
require_once __DIR__ . '/src/lib/auth.php';
requireLogin();
$user = getUser();
$pageTitle = 'Einstellungen';

// Simulate fetching settings or use actual logic
$settings = ['theme' => 'dark', 'language' => 'de'];

// require_once __DIR__ . '/templates/header.php'; // This was "falsch"
require_once __DIR__ . '/templates/navbar.php'; // Using your "normale navbar"
?>

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

<?php
require_once __DIR__ . '/templates/footer.php'; // Assuming you want the main footer
?>
