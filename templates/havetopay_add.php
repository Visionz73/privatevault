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
            --glass-bg: rgba(255, 255, 255, 0.25);
            --glass-border: rgba(255, 255, 255, 0.18);
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        
        .gradient-primary {
            background: var(--primary-gradient);
            position: relative;
            overflow: hidden;
        }
        
        .gradient-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 100%);
            pointer-events: none;
        }
        
        .btn-modern {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
        }
        
        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            background: rgba(255, 255, 255, 0.3);
        }
        
        .form-input {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 12px 16px;
            color: white;
            font-size: 14px;
        }
        
        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        
        .form-input:focus {
            outline: none;
            border-color: rgba(255, 255, 255, 0.4);
            background: rgba(255, 255, 255, 0.2);
        }
        
        .container-centered {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        /* Mobile adjustments */
        @media (max-width: 768px) {
            body {
                padding-top: 4rem !important;
                font-size: 13px;
            }
            .container-centered {
                padding: 0 1rem;
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
    <div class="main-content py-8">
        <div class="container-centered">
            <!-- Back Link -->
            <div class="mb-6">
                <a href="havetopay.php" class="btn-modern text-white hover:text-white flex items-center w-fit">
                    <i class="fas fa-arrow-left mr-2"></i>Back to HaveToPay
                </a>
            </div>
            
            <!-- Header -->
            <div class="mb-8 text-center">
                <h1 class="text-2xl font-bold text-white mb-2">Add New Expense</h1>
                <p class="text-white/70 text-sm">Split expenses with your friends and track who owes what</p>
            </div>
            
            <!-- Error and Success Messages -->
            <?php if (!empty($errors)): ?>
                <div class="glass-card mb-6 p-5">
                    <div class="flex text-white">
                        <i class="fas fa-exclamation-circle mr-3 mt-1 text-red-300"></i>
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
                    <div class="flex items-center text-white text-sm">
                        <i class="fas fa-check-circle mr-3 text-green-300"></i>
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
                                <label for="title" class="block text-sm font-medium text-white/80 mb-3">Title *</label>
                                <input type="text" id="title" name="title" required
                                       class="form-input w-full"
                                       placeholder="What is this expense for?"
                                       value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                            </div>
                            
                            <!-- Amount -->
                            <div>
                                <label for="amount" class="block text-sm font-medium text-white/80 mb-3">Amount *</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-3 text-white/60 text-sm">€</span>
                                    <input type="number" id="amount" name="amount" step="0.01" min="0.01" required
                                           class="form-input w-full pl-8"
                                           placeholder="0.00"
                                           value="<?php echo htmlspecialchars($_POST['amount'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <!-- Date -->
                            <div>
                                <label for="expense_date" class="block text-sm font-medium text-white/80 mb-3">Date</label>
                                <input type="date" id="expense_date" name="expense_date"
                                       class="form-input w-full"
                                       value="<?php echo htmlspecialchars($_POST['expense_date'] ?? date('Y-m-d')); ?>">
                            </div>
                            
                            <!-- Category -->
                            <div>
                                <label for="category" class="block text-sm font-medium text-white/80 mb-3">Category</label>
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
                            <label for="description" class="block text-sm font-medium text-white/80 mb-3">Description</label>
                            <textarea id="description" name="description" rows="3"
                                      class="form-input w-full"
                                      placeholder="Add any additional details about this expense..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                        </div>
                        
                        <!-- Participants -->
                        <div>
                            <label class="block text-sm font-medium text-white/80 mb-3">Select Participants *</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-48 overflow-y-auto p-4 bg-white/10 rounded-lg backdrop-blur-lg">
                                <?php foreach ($allUsers as $user): ?>
                                    <label class="flex items-center p-3 rounded-lg bg-white/10 hover:bg-white/20 transition-colors cursor-pointer">
                                        <input type="checkbox" name="participants[]" value="<?php echo $user['id']; ?>" 
                                               class="mr-3 rounded border-white/30 text-blue-500 focus:ring-blue-500 focus:ring-offset-0">
                                        <div class="text-sm">
                                            <div class="font-medium text-white"><?php echo htmlspecialchars($user['display_name']); ?></div>
                                            <div class="text-white/60 text-xs">@<?php echo htmlspecialchars($user['username']); ?></div>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="pt-4 text-center">
                            <button type="submit" class="btn-modern bg-green-500/80 hover:bg-green-500 text-white border-green-400/50 px-8 py-3">
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
