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
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            --glass-bg: rgba(255, 255, 255, 0.9);
            --glass-border: rgba(0, 0, 0, 0.1);
        }
        
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #cbd5e1 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            color: #1f2937;
            padding: 2rem;
        }
        
        .main-container {
            max-width: 900px;
            margin: 0 auto;
            width: 100%;
        }
        
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        .gradient-primary {
            background: var(--primary-gradient);
            color: white;
        }
        
        .btn-modern {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 13px;
            color: #374151;
            transition: all 0.3s ease;
        }
        
        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }
        
        .btn-primary {
            background: var(--primary-gradient);
            color: white;
            border: none;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
            border: none;
        }
        
        .form-input {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 12px 16px;
            color: #1f2937;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-input::placeholder {
            color: #9ca3af;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
            background: white;
        }
        
        .checkbox-container {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .checkbox-container:hover {
            background: rgba(255, 255, 255, 0.95);
            border-color: #4f46e5;
        }
        
        .checkbox-container input[type="checkbox"]:checked + .checkbox-content {
            background: rgba(79, 70, 229, 0.1);
        }
        
        .text-primary {
            color: #1f2937;
        }
        
        .text-secondary {
            color: #6b7280;
        }
        
        /* Mobile adjustments */
        @media (max-width: 768px) {
            body {
                padding: 1rem;
                padding-top: 5rem !important;
            }
            
            .main-container {
                max-width: 100%;
            }
        }
        
        /* Desktop adjustments */
        @media (min-width: 769px) {
            .main-content {
                margin-left: 16rem;
                width: calc(100% - 16rem);
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="main-content">
            <!-- Back Link -->
            <div class="mb-6">
                <a href="havetopay.php" class="btn-modern flex items-center w-fit">
                    <i class="fas fa-arrow-left mr-2"></i>Back to HaveToPay
                </a>
            </div>
            
            <!-- Header -->
            <div class="mb-6 text-center">
                <h1 class="text-2xl font-bold text-primary mb-2">Add New Expense</h1>
                <p class="text-secondary text-sm">Split expenses with your friends and track who owes what</p>
            </div>
            
            <!-- Error and Success Messages -->
            <?php if (!empty($errors)): ?>
                <div class="glass-card mb-6 p-5">
                    <div class="flex text-primary">
                        <i class="fas fa-exclamation-circle mr-3 mt-1 text-red-600"></i>
                        <div>
                            <strong class="text-sm">Please fix the following errors:</strong>
                            <ul class="list-disc list-inside mt-2 text-sm space-y-1">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="glass-card mb-6 p-5">
                    <div class="flex items-center text-primary text-sm">
                        <i class="fas fa-check-circle mr-3 text-green-600"></i>
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Add Expense Form -->
            <div class="glass-card">
                <div class="gradient-primary text-white p-5 rounded-t-3xl">
                    <h2 class="text-lg font-bold flex items-center">
                        <i class="fas fa-plus mr-3"></i>Expense Details
                    </h2>
                </div>
                <div class="p-6">
                    <form action="" method="POST" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Title -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-primary mb-3">Title *</label>
                                <input type="text" id="title" name="title" required
                                       class="form-input w-full"
                                       placeholder="What is this expense for?"
                                       value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                            </div>
                            
                            <!-- Amount -->
                            <div>
                                <label for="amount" class="block text-sm font-medium text-primary mb-3">Amount *</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-3 text-secondary text-sm">€</span>
                                    <input type="number" id="amount" name="amount" step="0.01" min="0.01" required
                                           class="form-input w-full pl-8"
                                           placeholder="0.00"
                                           value="<?php echo htmlspecialchars($_POST['amount'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <!-- Date -->
                            <div>
                                <label for="expense_date" class="block text-sm font-medium text-primary mb-3">Date</label>
                                <input type="date" id="expense_date" name="expense_date"
                                       class="form-input w-full"
                                       value="<?php echo htmlspecialchars($_POST['expense_date'] ?? date('Y-m-d')); ?>">
                            </div>
                            
                            <!-- Category -->
                            <div>
                                <label for="category" class="block text-sm font-medium text-primary mb-3">Category</label>
                                <select id="category" name="category" class="form-input w-full">
                                    <option value="Other">Other</option>
                                    <option value="Food">Food & Drinks</option>
                                    <option value="Transportation">Transportation</option>
                                    <option value="Entertainment">Entertainment</option>
                                    <option value="Shopping">Shopping</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-primary mb-3">Description</label>
                            <textarea id="description" name="description" rows="3"
                                      class="form-input w-full"
                                      placeholder="Add any additional details about this expense..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                        </div>
                        
                        <!-- Participants -->
                        <div>
                            <label class="block text-sm font-medium text-primary mb-3">Select Participants *</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-48 overflow-y-auto p-4 bg-gray-50/50 rounded-lg border border-gray-200">
                                <?php foreach ($allUsers as $user): ?>
                                    <label class="checkbox-container cursor-pointer">
                                        <div class="flex items-center p-3 rounded-lg checkbox-content">
                                            <input type="checkbox" name="participants[]" value="<?php echo $user['id']; ?>" 
                                                   class="mr-3 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <div class="text-sm">
                                                <div class="font-medium text-primary"><?php echo htmlspecialchars($user['display_name']); ?></div>
                                                <div class="text-secondary text-xs">@<?php echo htmlspecialchars($user['username']); ?></div>
                                            </div>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="pt-4 text-center">
                            <button type="submit" class="btn-modern btn-success px-8 py-3">
                                <i class="fas fa-plus mr-2"></i>Create Expense
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
