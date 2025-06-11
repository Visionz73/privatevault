<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense | HaveToPay</title>
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
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }
        .glass-container:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
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
            border-radius: 0.75rem;
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
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
        }
        
        /* Glass select styling */
        .glass-select {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 0.75rem;
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
    </style>
</head>
<body class="min-h-screen flex">
    <?php include_once __DIR__ . '/navbar.php'; ?>

    <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Back Link -->
        <div class="mb-8">
            <a href="havetopay.php" class="inline-flex items-center text-blue-400 hover:text-blue-300 font-medium group transition-colors">
                <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>Back to HaveToPay
            </a>
        </div>
        
        <!-- Header -->
        <div class="glass-container mb-10 overflow-hidden">
            <div class="glass-header px-8 py-6 text-white">
                <h1 class="text-3xl font-bold flex items-center">
                    <i class="fas fa-plus-circle mr-4 opacity-80"></i>Add New Expense
                </h1>
                <p class="mt-2 text-white/70">Split expenses with your friends and track who owes what</p>
            </div>
        </div>
        
        <!-- Error and Success Messages -->
        <?php if (!empty($errors)): ?>
            <div class="glass-container glass-error p-6 rounded-xl mb-8">
                <div class="flex">
                    <i class="fas fa-exclamation-circle text-xl mr-3 mt-0.5"></i>
                    <div>
                        <strong>Please fix the following errors:</strong>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="glass-container glass-success p-6 rounded-xl mb-8 flex items-center">
                <i class="fas fa-check-circle text-xl mr-3"></i>
                <p><?php echo htmlspecialchars($success); ?></p>
            </div>
        <?php endif; ?>
        
        <!-- Add Expense Form -->
        <div class="glass-container overflow-hidden">
            <div class="glass-header px-8 py-6 text-white">
                <h2 class="text-xl font-bold flex items-center">
                    <i class="fas fa-receipt mr-3"></i>Expense Details
                </h2>
            </div>
            <div class="p-8">
                <form action="" method="POST" class="space-y-8">
                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Title -->
                        <div class="space-y-3">
                            <label for="title" class="block text-sm font-medium text-white">Title *</label>
                            <input type="text" id="title" name="title" required
                                   class="glass-input w-full px-4 py-3 transition-all"
                                   placeholder="What is this expense for?"
                                   value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                        </div>
                        
                        <!-- Amount -->
                        <div class="space-y-3">
                            <label for="amount" class="block text-sm font-medium text-white">Amount *</label>
                            <div class="relative">
                                <span class="absolute left-4 top-3 text-white/60">€</span>
                                <input type="number" id="amount" name="amount" step="0.01" min="0.01" required
                                       class="glass-input w-full pl-8 pr-4 py-3 transition-all"
                                       placeholder="0.00"
                                       value="<?php echo htmlspecialchars($_POST['amount'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <!-- Date -->
                        <div class="space-y-3">
                            <label for="expense_date" class="block text-sm font-medium text-white">Date</label>
                            <input type="date" id="expense_date" name="expense_date"
                                   class="glass-input w-full px-4 py-3 transition-all"
                                   value="<?php echo htmlspecialchars($_POST['expense_date'] ?? date('Y-m-d')); ?>">
                        </div>
                        
                        <!-- Category -->
                        <div class="space-y-3">
                            <label for="category" class="block text-sm font-medium text-white">Category</label>
                            <select id="category" name="category" class="glass-select w-full px-4 py-3 transition-all">
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Group -->
                        <div class="space-y-3">
                            <label for="group_id" class="block text-sm font-medium text-white">Group (Optional)</label>
                            <select id="group_id" name="group_id" class="glass-select w-full px-4 py-3 transition-all">
                                <option value="">-- No Group --</option>
                                <?php foreach ($allGroups as $group): ?>
                                <option value="<?php echo $group['id']; ?>"
                                        <?php echo ($_POST['group_id'] ?? '') == $group['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($group['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Participants -->
                        <div class="space-y-3">
                            <label for="participants" class="block text-sm font-medium text-white">Split With *</label>
                            <select id="participants" name="participants[]" multiple required
                                    class="glass-select w-full px-4 py-3 transition-all"
                                    style="min-height: 120px;">
                                <?php foreach ($allUsers as $user): ?>
                                <option value="<?php echo $user['id']; ?>"
                                        <?php echo in_array($user['id'], $_POST['participants'] ?? []) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($user['display_name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="text-sm text-white/60 mt-2">Hold Ctrl/Cmd to select multiple participants</p>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="space-y-3">
                        <label for="description" class="block text-sm font-medium text-white">Description</label>
                        <textarea id="description" name="description" rows="4"
                                  class="glass-input w-full px-4 py-3 transition-all resize-none"
                                  placeholder="Add any details about this expense..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex justify-center pt-6">
                        <button type="submit"
                                class="glass-btn-primary px-8 py-4 rounded-xl font-semibold shadow-lg">
                            <i class="fas fa-plus-circle mr-2"></i>Add Expense
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </main>
</body>
</html>
