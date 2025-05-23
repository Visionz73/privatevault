<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense | HaveToPay</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            --danger-gradient: linear-gradient(135deg, #f56565 0%, #c53030 100%);
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            --hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        .glass-card {
            background-color: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .glass-card:hover {
            box-shadow: var(--hover-shadow);
        }
        
        .gradient-primary {
            background: var(--primary-gradient);
        }
        
        .apple-input {
            background-color: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 12px;
            padding: 12px 16px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.04);
        }
        
        .apple-input:focus {
            background-color: rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08), 0 0 0 3px rgba(102, 126, 234, 0.15);
            border-color: #667eea;
            outline: none;
        }
        
        .apple-input::placeholder {
            color: #a0aec0;
        }
        
        .apple-select {
            background-color: rgba(255, 255, 255, 0.6);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            backdrop-filter: blur(10px);
            appearance: none;
        }
        
        .apple-btn {
            border-radius: 16px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .apple-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        
        .apple-btn-primary {
            background: var(--primary-gradient);
            color: white;
        }
        
        /* Mobile adjustments */
        @media (max-width: 768px) {
            body {
                padding-top: 4rem !important;
            }
        }
        
        /* Desktop adjustments */
        @media (min-width: 769px) {
            .main-content {
                margin-left: 16rem;
                width: calc(100% - 16rem);
            }
        }
        
        .animated-bg {
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }
        
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen">
    <?php require_once __DIR__ . '/../templates/navbar.php'; ?>
    
    <div class="main-content p-6 animated-bg">
        <!-- Back Link -->
        <div class="mb-6">
            <a href="havetopay.php" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium transition-all hover:translate-x-[-4px]">
                <i class="fas fa-arrow-left mr-2"></i>Back to HaveToPay
            </a>
        </div>
        
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Add New Expense</h1>
            <p class="text-gray-600 mt-2">Split expenses with your friends and track who owes what</p>
        </div>
        
        <!-- Error and Success Messages -->
        <?php if (!empty($errors)): ?>
            <div class="glass-card border-l-4 border-red-500 p-4 mb-6">
                <div class="flex">
                    <i class="fas fa-exclamation-circle mr-3 mt-1 text-red-500 text-xl"></i>
                    <div>
                        <strong class="text-red-700">Please fix the following errors:</strong>
                        <ul class="list-disc list-inside mt-2 text-red-700">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="glass-card border-l-4 border-green-500 p-4 mb-6 flex items-center">
                <i class="fas fa-check-circle mr-3 text-green-500 text-xl"></i>
                <p class="text-green-700"><?php echo htmlspecialchars($success); ?></p>
            </div>
        <?php endif; ?>
        
        <!-- Add Expense Form -->
        <div class="glass-card">
            <div class="gradient-primary text-white p-6 rounded-t-3xl">
                <h2 class="text-xl font-bold flex items-center">
                    <i class="fas fa-plus mr-3"></i>Expense Details
                </h2>
            </div>
            <div class="p-6">
                <form action="" method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                            <input type="text" id="title" name="title" required
                                   class="apple-input w-full"
                                   placeholder="What is this expense for?"
                                   value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                        </div>
                        
                        <!-- Amount -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount *</label>
                            <div class="relative">
                                <span class="absolute left-4 top-3 text-gray-500">€</span>
                                <input type="number" id="amount" name="amount" step="0.01" min="0.01" required
                                       class="apple-input w-full pl-8"
                                       placeholder="0.00"
                                       value="<?php echo htmlspecialchars($_POST['amount'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <!-- Date -->
                        <div>
                            <label for="expense_date" class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                            <input type="date" id="expense_date" name="expense_date"
                                   class="apple-input w-full"
                                   value="<?php echo htmlspecialchars($_POST['expense_date'] ?? date('Y-m-d')); ?>">
                        </div>
                        
                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select id="category" name="category"
                                    class="apple-input apple-select w-full">
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['name']); ?>"
                                        <?php echo ($_POST['category'] ?? '') == $category['name'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Group -->
                        <div>
                            <label for="group_id" class="block text-sm font-medium text-gray-700 mb-2">Group (Optional)</label>
                            <select id="group_id" name="group_id"
                                    class="apple-input apple-select w-full">
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
                        <div>
                            <label for="participants" class="block text-sm font-medium text-gray-700 mb-2">Split With *</label>
                            <select id="participants" name="participants[]" multiple required
                                    class="apple-input w-full"
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
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="description" name="description" rows="3"
                                  class="apple-input w-full"
                                  placeholder="Add any details about this expense..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex justify-center">
                        <button type="submit" class="apple-btn apple-btn-primary w-full md:w-auto">
                            <i class="fas fa-plus-circle mr-2"></i>Add Expense
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
