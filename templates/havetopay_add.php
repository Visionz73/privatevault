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
            background: linear-gradient(135deg, #2d1b69 0%, #11101d 50%, #1a0909 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="min-h-screen flex">
    <?php include_once __DIR__ . '/navbar.php'; ?>

    <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Back Link -->
        <div class="mb-6">
            <a href="havetopay.php" class="inline-flex items-center text-blue-400 hover:text-blue-300 font-medium group transition-colors">
                <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>Back to HaveToPay
            </a>
        </div>
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white/90 mb-2">Add New Expense</h1>
            <p class="text-white/60">Split expenses with your friends and track who owes what</p>
        </div>
        
        <!-- Error and Success Messages -->
        <?php if (!empty($errors)): ?>
            <div class="bg-red-500/20 border border-red-400/30 backdrop-blur-sm text-red-300 p-4 rounded-xl mb-6">
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
            <div class="bg-green-500/20 border border-green-400/30 backdrop-blur-sm text-green-300 p-4 rounded-xl mb-6 flex items-center">
                <i class="fas fa-check-circle text-xl mr-3"></i>
                <p><?php echo htmlspecialchars($success); ?></p>
            </div>
        <?php endif; ?>
        
        <!-- Add Expense Form -->
        <div class="bg-gradient-to-br from-purple-900/20 via-gray-900/30 to-red-900/20 backdrop-blur-xl rounded-3xl border border-white/10 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600/30 via-indigo-700/40 to-purple-800/30 backdrop-blur-sm px-8 py-6 border-b border-white/10">
                <h2 class="text-xl font-bold flex items-center text-white/90">
                    <i class="fas fa-plus-circle mr-3"></i>Expense Details
                </h2>
            </div>
            <div class="p-8">
                <form action="" method="POST" class="space-y-6">
                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Title -->
                        <div class="space-y-2">
                            <label for="title" class="block text-sm font-medium text-white/80">Title *</label>
                            <input type="text" id="title" name="title" required
                                   class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400/50 transition-all backdrop-blur-sm text-white/90 placeholder-white/40"
                                   placeholder="What is this expense for?"
                                   value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                        </div>
                        
                        <!-- Amount -->
                        <div class="space-y-2">
                            <label for="amount" class="block text-sm font-medium text-white/80">Amount *</label>
                            <div class="relative">
                                <span class="absolute left-4 top-3 text-white/50">€</span>
                                <input type="number" id="amount" name="amount" step="0.01" min="0.01" required
                                       class="w-full pl-8 pr-4 py-3 bg-white/5 border border-white/20 rounded-xl focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400/50 transition-all backdrop-blur-sm text-white/90 placeholder-white/40"
                                       placeholder="0.00"
                                       value="<?php echo htmlspecialchars($_POST['amount'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <!-- Date -->
                        <div class="space-y-2">
                            <label for="expense_date" class="block text-sm font-medium text-white/80">Date</label>
                            <input type="date" id="expense_date" name="expense_date"
                                   class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400/50 transition-all backdrop-blur-sm text-white/90"
                                   value="<?php echo htmlspecialchars($_POST['expense_date'] ?? date('Y-m-d')); ?>">
                        </div>
                        
                        <!-- Category -->
                        <div class="space-y-2">
                            <label for="category" class="block text-sm font-medium text-white/80">Category</label>
                            <select id="category" name="category"
                                    class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400/50 transition-all backdrop-blur-sm text-white/90">
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Group -->
                        <div class="space-y-2">
                            <label for="group_id" class="block text-sm font-medium text-white/80">Group (Optional)</label>
                            <select id="group_id" name="group_id"
                                    class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400/50 transition-all backdrop-blur-sm text-white/90">
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
                        <div class="space-y-2">
                            <label for="participants" class="block text-sm font-medium text-white/80">Split With *</label>
                            <select id="participants" name="participants[]" multiple required
                                    class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400/50 transition-all backdrop-blur-sm text-white/90"
                                    style="min-height: 120px;">
                                <?php foreach ($allUsers as $user): ?>
                                <option value="<?php echo $user['id']; ?>"
                                        <?php echo in_array($user['id'], $_POST['participants'] ?? []) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($user['display_name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="text-sm text-white/50 mt-1">Hold Ctrl/Cmd to select multiple participants</p>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="space-y-2">
                        <label for="description" class="block text-sm font-medium text-white/80">Description</label>
                        <textarea id="description" name="description" rows="4"
                                  class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400/50 transition-all backdrop-blur-sm text-white/90 placeholder-white/40"
                                  placeholder="Add any details about this expense..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex justify-center pt-4">
                        <button type="submit"
                                class="bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/20 hover:border-white/30 text-white/90 hover:text-white px-8 py-3 rounded-xl font-semibold transition-all shadow-lg">
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
