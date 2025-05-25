<?php
// notifications.php
require_once __DIR__ . '/src/lib/auth.php';
requireLogin();
$user = getUser();
$pageTitle = 'Benachrichtigungen';

// Simulate fetching notification settings or use actual logic
$notifications = ['email_new_task' => true, 'email_updates' => false];

require_once __DIR__ . '/templates/header.php';
?>

<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Benachrichtigungen</h1>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <form>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    E-Mail Benachrichtigungen
                </label>
                <div class="mt-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="email_new_task" class="form-checkbox h-5 w-5 text-blue-600" <?php echo ($notifications['email_new_task'] ?? false) ? 'checked' : ''; ?>>
                        <span class="ml-2 text-gray-700">Bei neuer Aufgabe</span>
                    </label>
                </div>
                <div class="mt-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="email_updates" class="form-checkbox h-5 w-5 text-blue-600" <?php echo ($notifications['email_updates'] ?? false) ? 'checked' : ''; ?>>
                        <span class="ml-2 text-gray-700">Produkt-Updates</span>
                    </label>
                </div>
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
require_once __DIR__ . '/templates/footer.php';
?>
