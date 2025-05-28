<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense | HaveToPay</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <?php include_once __DIR__ . '/navbar.php'; ?>
    
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Back Link -->
        <div class="mb-8">
            <a href="havetopay.php" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium group">
                <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>Back to HaveToPay
            </a>
        </div>
        
        <!-- Header -->
        <div class="mb-10">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Add New Expense</h1>
            <p class="text-gray-600">Split expenses with your friends and track who owes what</p>
        </div>
        
        <!-- Error and Success Messages -->
        <?php if (!empty($errors)): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-8">
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
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-8 flex items-center">
                <i class="fas fa-check-circle text-xl mr-3"></i>
                <p><?php echo htmlspecialchars($success); ?></p>
            </div>
        <?php endif; ?>
        
        <!-- Add Expense Form -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-6 text-white">
                <h2 class="text-xl font-bold flex items-center">
                    <i class="fas fa-plus-circle mr-3"></i>Expense Details
                </h2>
            </div>
            <div class="p-8">
                <form action="" method="POST" class="space-y-8">
                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Title -->
                        <div class="space-y-2">
                            <label for="title" class="block text-sm font-medium text-gray-700">Title *</label>
                            <input type="text" id="title" name="title" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                   placeholder="What is this expense for?"
                                   value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                        </div>
                        
                        <!-- Amount -->
                        <div class="space-y-2">
                            <label for="amount" class="block text-sm font-medium text-gray-700">Amount *</label>
                            <div class="relative">
                                <span class="absolute left-4 top-3 text-gray-500">€</span>
                                <input type="number" id="amount" name="amount" step="0.01" min="0.01" required
                                       class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                       placeholder="0.00"
                                       value="<?php echo htmlspecialchars($_POST['amount'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <!-- Date -->
                        <div class="space-y-2">
                            <label for="expense_date" class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" id="expense_date" name="expense_date"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                   value="<?php echo htmlspecialchars($_POST['expense_date'] ?? date('Y-m-d')); ?>">
                        </div>
                        
                        <!-- Category -->
                        <div class="space-y-2">
                            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                            <select id="category" name="category"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
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
                        <div class="space-y-2">
                            <label for="group_id" class="block text-sm font-medium text-gray-700">Group (Optional)</label>
                            <select id="group_id" name="group_id"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
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
                            <label for="participants" class="block text-sm font-medium text-gray-700">Split With *</label>
                            <select id="participants" name="participants[]" multiple required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                    style="min-height: 120px;">
                                <?php foreach ($allUsers as $user): ?>
                                <option value="<?php echo $user['id']; ?>"
                                        <?php echo in_array($user['id'], $_POST['participants'] ?? []) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($user['display_name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="text-sm text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple participants</p>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="space-y-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea id="description" name="description" rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                  placeholder="Add any details about this expense..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex justify-center">
                        <button type="submit"
                                class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-8 py-3 rounded-xl font-semibold hover:from-indigo-700 hover:to-purple-700 transition-colors shadow-md">
                            <i class="fas fa-plus-circle mr-2"></i>Add Expense
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
