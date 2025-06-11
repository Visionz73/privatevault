<?php
// templates/create_task.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($allUsers)) {
    $allUsers = []; // Beispiel: Leere Liste, falls nicht definiert
}
if (!isset($allGroups)) {
    $allGroups = []; // Beispiel: Leere Liste, falls nicht definiert
}
if (!isset($success)) {
    $success = ''; // Standardwert
}
if (!isset($errors)) {
    $errors = []; // Standardwert
}
?>
<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Aufgabe erstellen | Private Vault</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="/privatevault/css/main.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media (max-width: 768px) {
            main { margin-top: 3.5rem; }
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#eef7ff] via-[#f7fbff] to-[#f9fdf2] flex flex-col">
    <?php require_once __DIR__ . '/navbar.php'; ?>
    
    <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Neue Aufgabe erstellen</h1>
            
            <?php if (!empty($errors)): ?>
                <div class="bg-red-50 border border-red-100 text-red-600 rounded-xl p-4 mb-6">
                    <ul class="list-disc list-inside">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="post" action="/create_task.php" class="bg-white/60 backdrop-blur-sm rounded-xl shadow-sm p-6 md:p-8 space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titel *</label>
                    <input type="text" id="title" name="title" required 
                           value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" 
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Beschreibung</label>
                    <textarea id="description" name="description" rows="4" 
                              class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                </div>
                
                <div>
                    <label for="assignment_type" class="block text-sm font-medium text-gray-700 mb-1">Zuweisung an</label>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="inline-flex items-center">
                                <input type="radio" name="assignment_type" value="user" checked 
                                        onclick="toggleAssignmentType('user')"
                                        class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                <span class="ml-2">Einzelner Benutzer</span>
                            </label>
                        </div>
                        <div>
                            <label class="inline-flex items-center">
                                <input type="radio" name="assignment_type" value="group"
                                        onclick="toggleAssignmentType('group')"
                                        class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                <span class="ml-2">Benutzergruppe</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- User Assignment -->
                    <div id="user_assignment">
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">Benutzer auswählen *</label>
                        <select id="assigned_to" name="assigned_to" required 
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
                            <option value="" disabled <?= empty($_POST['assigned_to']) ? 'selected' : '' ?>>Bitte auswählen...</option>
                            <?php foreach ($allUsers as $user): ?>
                                <option value="<?= $user['id'] ?>" <?= isset($_POST['assigned_to']) && $_POST['assigned_to'] == $user['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($user['username']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Group Assignment -->
                    <div id="group_assignment" style="display: none;">
                        <label for="assigned_group" class="block text-sm font-medium text-gray-700 mb-1">Gruppe auswählen *</label>
                        <select id="assigned_group" name="assigned_group"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
                            <option value="" disabled selected>Bitte auswählen...</option>
                            <?php foreach ($allGroups as $group): ?>
                                <option value="<?= $group['id'] ?>" <?= isset($_POST['assigned_group']) && $_POST['assigned_group'] == $group['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($group['name']) ?> (<?= $group['member_count'] ?> Mitglieder)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Fällig am</label>
                    <input type="date" id="due_date" name="due_date" 
                           value="<?= htmlspecialchars($_POST['due_date'] ?? '') ?>" 
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
                </div>
                
                <div class="pt-4">
                    <button type="submit" class="w-full md:w-auto px-6 py-2 bg-[#4A90E2] text-white rounded-lg hover:bg-[#4A90E2]/90 transition-colors">
                        Aufgabe erstellen
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
<script>
    function toggleAssignmentType(type) {
        if (type === 'user') {
            document.getElementById('user_assignment').style.display = 'block';
            document.getElementById('group_assignment').style.display = 'none';
            document.getElementById('assigned_to').setAttribute('required', 'required');
            document.getElementById('assigned_group').removeAttribute('required');
        } else {
            document.getElementById('user_assignment').style.display = 'none';
            document.getElementById('group_assignment').style.display = 'block';
            document.getElementById('assigned_to').removeAttribute('required');
            document.getElementById('assigned_group').setAttribute('required', 'required');
        }
    }

    // Check if assignment type is already set (in case of form resubmission)
    document.addEventListener('DOMContentLoaded', function() {
        const assignmentType = document.querySelector('input[name="assignment_type"]:checked').value;
        if (assignmentType) {
            toggleAssignmentType(assignmentType);
        }
    });
</script>
</html>
