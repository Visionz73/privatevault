<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ausgabe hinzufügen | Schuldenverwaltung</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }
        
        /* Glass effect containers */
        .glass-container {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 1.2rem;
            box-shadow: 0 6px 26px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }
        .glass-container:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        }
        
        /* Glass header with gradient */
        .glass-header {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.1) 100%);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Glass form inputs */
        .glass-input {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 0.6rem;
            color: white;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        .glass-input:focus {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(99, 102, 241, 0.5);
            outline: none;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }
        .glass-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        
        /* Glass buttons */
        .glass-btn {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            transition: all 0.3s ease;
        }
        .glass-btn:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            color: white;
        }
        
        /* Primary button with gradient */
        .glass-btn-primary {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.8) 0%, rgba(139, 92, 246, 0.8) 100%);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: white;
            transition: all 0.3s ease;
        }
        .glass-btn-primary:hover {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.9) 0%, rgba(139, 92, 246, 0.9) 100%);
            border-color: rgba(99, 102, 241, 0.5);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.3);
        }
        
        /* Glass select styling */
        .glass-select {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 0.6rem;
            color: white;
            backdrop-filter: blur(10px);
        }
        .glass-select option {
            background: #1a0909;
            color: white;
        }
        
        /* Error message styling */
        .glass-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }
        
        /* Success message styling */
        .glass-success {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #86efac;
        }
        
        /* Checkbox styling */
        input[type="checkbox"] {
            accent-color: #3b82f6;
        }
        
        input[type="checkbox"]:checked {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }
    </style>
</head>
<body class="min-h-screen flex">
    <?php include_once __DIR__ . '/navbar.php'; ?>

    <main class="ml-0 mt-16 md:ml-64 md:mt-0 flex-1 p-3 md:p-5">
    <div class="max-w-4xl mx-auto px-3 sm:px-5 lg:px-6 py-6">
        <!-- Back Link -->
        <div class="mb-5">
            <a href="havetopay.php" class="inline-flex items-center text-blue-400 hover:text-blue-300 font-medium group transition-colors text-sm">
                <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>Zurück zur Schuldenverwaltung
            </a>
        </div>
        
        <!-- Header -->
        <div class="mb-5">
            <h1 class="text-xl font-bold text-white flex items-center mb-2">
                <i class="fas fa-plus-circle mr-2 text-blue-400"></i>Neue Ausgabe hinzufügen
            </h1>
        </div>
        
        <!-- Error and Success Messages -->
        <?php if (!empty($errors)): ?>
            <div class="glass-container glass-error p-4 rounded-xl mb-6">
                <div class="flex">
                    <i class="fas fa-exclamation-circle text-lg mr-2 mt-0.5"></i>
                    <div>
                        <strong class="text-sm">Bitte behebe die folgenden Fehler:</strong>
                        <ul class="list-disc list-inside mt-1 space-y-0.5 text-xs">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="glass-container glass-success p-4 rounded-xl mb-6 flex items-center">
                <i class="fas fa-check-circle text-lg mr-2"></i>
                <p class="text-sm"><?php echo htmlspecialchars($success); ?></p>
            </div>
        <?php endif; ?>
        
        <!-- Add Expense Form -->
        <div class="glass-container overflow-hidden">
            <div class="glass-header px-5 py-4 text-white">
                <h2 class="text-lg font-bold flex items-center">
                    <i class="fas fa-receipt mr-2"></i>Ausgabendetails
                </h2>
            </div>
            <div class="p-5">
                <form action="" method="POST" class="space-y-5">
                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Title -->
                        <div class="space-y-2">
                            <label for="title" class="block text-xs font-medium text-white">Titel *</label>
                            <input type="text" id="title" name="title" required
                                   class="glass-input w-full px-3 py-2 transition-all text-sm"
                                   placeholder="Wofür ist diese Ausgabe?"
                                   value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                        </div>
                        
                        <!-- Amount -->
                        <div class="space-y-2">
                            <label for="amount" class="block text-xs font-medium text-white">Betrag *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-white/60 text-sm">€</span>
                                <input type="number" id="amount" name="amount" step="0.01" min="0.01" required
                                       class="glass-input w-full pl-6 pr-3 py-2 transition-all text-sm"
                                       placeholder="0,00"
                                       value="<?php echo htmlspecialchars($_POST['amount'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <!-- Date -->
                        <div class="space-y-2">
                            <label for="expense_date" class="block text-xs font-medium text-white">Datum</label>
                            <input type="date" id="expense_date" name="expense_date"
                                   class="glass-input w-full px-3 py-2 transition-all text-sm"
                                   value="<?php echo htmlspecialchars($_POST['expense_date'] ?? date('Y-m-d')); ?>">
                        </div>
                        
                        <!-- Category -->
                        <div class="space-y-2">
                            <label for="category" class="block text-xs font-medium text-white">Kategorie</label>
                            <select id="category" name="category" class="glass-select w-full px-3 py-2 transition-all text-sm">
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['name']); ?>"
                                        <?php echo ($_POST['category'] ?? '') == $category['name'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Group & Participants -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Group -->
                        <div class="space-y-2">
                            <label for="group_id" class="block text-xs font-medium text-white">Gruppe (Optional)</label>
                            <select id="group_id" name="group_id" class="glass-select w-full px-3 py-2 transition-all text-sm">
                                <option value="">-- Keine Gruppe --</option>
                                <?php foreach ($allGroups as $group): ?>
                                <option value="<?php echo $group['id']; ?>"
                                        <?php echo ($_POST['group_id'] ?? '') == $group['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($group['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Participants -->
                        <div class="space-y-2">
                            <label class="block text-xs font-medium text-white">Teilen mit *</label>
                            <div class="glass-input p-3 max-h-40 overflow-y-auto">
                                <div class="space-y-1">
                                    <?php foreach ($allUsers as $user): ?>
                                    <label class="flex items-center space-x-2 cursor-pointer hover:bg-white/5 p-1.5 rounded text-sm">
                                        <input type="checkbox" 
                                               name="participants[]" 
                                               value="<?php echo $user['id']; ?>"
                                               <?php echo in_array($user['id'], $_POST['participants'] ?? []) ? 'checked' : ''; ?>
                                               class="w-3 h-3 text-blue-500 bg-white/10 border-white/30 rounded focus:ring-blue-500 focus:ring-2">
                                        <div class="w-6 h-6 bg-gradient-to-br from-blue-400 to-purple-500 text-white rounded-full flex items-center justify-center text-xs font-semibold">
                                            <?php echo strtoupper(substr($user['display_name'], 0, 1)); ?>
                                        </div>
                                        <span class="text-white text-xs font-medium"><?php echo htmlspecialchars($user['display_name']); ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <p class="text-xs text-white/60">Wähle die Teilnehmer für diese Ausgabe</p>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="space-y-2">
                        <label for="description" class="block text-xs font-medium text-white">Beschreibung</label>
                        <textarea id="description" name="description" rows="3"
                                  class="glass-input w-full px-3 py-2 transition-all resize-none text-sm"
                                  placeholder="Füge Details zu dieser Ausgabe hinzu..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex justify-center pt-4">
                        <button type="submit"
                                class="glass-btn-primary px-6 py-3 rounded-xl font-semibold shadow-lg text-sm">
                            <i class="fas fa-plus-circle mr-2"></i>Ausgabe hinzufügen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </main>
</body>
</html>
