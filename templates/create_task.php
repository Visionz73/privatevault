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
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { 
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
            min-height: 100vh;
        }
        @media (max-width: 768px) {
            main { margin-top: 4rem; }
        }
        
        /* Glass card styling */
        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            color: white;
        }
        
        /* Form inputs */
        .glass-input {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 0.75rem;
            color: white;
            transition: all 0.3s ease;
        }
        .glass-input:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
        }
        .glass-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        
        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.8) 0%, rgba(79, 70, 229, 0.8) 100%);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 0.75rem;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.9) 0%, rgba(79, 70, 229, 0.9) 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(147, 51, 234, 0.3);
        }
        
        /* Success/Error messages */
        .success-message {
            background: rgba(34, 197, 94, 0.2);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #86efac;
            border-radius: 0.75rem;
            backdrop-filter: blur(10px);
        }
        .error-message {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            border-radius: 0.75rem;
            backdrop-filter: blur(10px);
        }
        
        /* Header text */
        .header-text {
            color: white;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }
        
        /* Labels */
        .form-label {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
        }
    </style>
</head>
<body class="min-h-screen flex">
    <?php require_once __DIR__ . '/navbar.php'; ?>
    
    <main class="ml-0 mt-16 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-3xl font-bold header-text mb-6">Neue Aufgabe erstellen</h1>
            
            <?php if (!empty($success)): ?>
                <div class="success-message p-4 mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="error-message p-4 mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 15.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <ul class="list-disc list-inside">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="post" action="/create_task.php" class="glass-card p-6 md:p-8 space-y-6">
                <div>
                    <label for="title" class="block text-sm form-label mb-2">Titel *</label>
                    <input type="text" id="title" name="title" required 
                           value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" 
                           class="w-full px-4 py-3 glass-input"
                           placeholder="Aufgabentitel eingeben...">
                </div>
                
                <div>
                    <label for="description" class="block text-sm form-label mb-2">Beschreibung</label>
                    <textarea id="description" name="description" rows="4" 
                              class="w-full px-4 py-3 glass-input"
                              placeholder="Beschreibung der Aufgabe..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                </div>
                
                <div>
                    <label class="block text-sm form-label mb-2">Zuweisung an</label>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="inline-flex items-center">
                                <input type="radio" name="assignment_type" value="user" checked 
                                        onclick="toggleAssignmentType('user')"
                                        class="h-4 w-4 text-purple-600 bg-white/10 border-white/20 focus:ring-purple-500">
                                <span class="ml-2 text-white/90">Einzelner Benutzer</span>
                            </label>
                        </div>
                        <div>
                            <label class="inline-flex items-center">
                                <input type="radio" name="assignment_type" value="group"
                                        onclick="toggleAssignmentType('group')"
                                        class="h-4 w-4 text-purple-600 bg-white/10 border-white/20 focus:ring-purple-500">
                                <span class="ml-2 text-white/90">Benutzergruppe</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- User Assignment -->
                    <div id="user_assignment">
                        <label for="assigned_to" class="block text-sm form-label mb-2">Benutzer auswählen *</label>
                        <select id="assigned_to" name="assigned_to" required 
                                class="w-full px-4 py-3 glass-input">
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
                        <label for="assigned_group" class="block text-sm form-label mb-2">Gruppe auswählen *</label>
                        <select id="assigned_group" name="assigned_group"
                                class="w-full px-4 py-3 glass-input">
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
                    <label for="due_date" class="block text-sm form-label mb-2">Fällig am</label>
                    <input type="date" id="due_date" name="due_date" 
                           value="<?= htmlspecialchars($_POST['due_date'] ?? '') ?>" 
                           class="w-full px-4 py-3 glass-input">
                </div>
                
                <div class="pt-4">
                    <button type="submit" class="w-full md:w-auto px-8 py-3 btn-primary font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Aufgabe erstellen
                    </button>
                </div>
            </form>
        </div>
    </main>
    
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
</body>
</html>
