<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense | HaveToPay</title>
    <link href="https://fonts.googleapis.com/css2?family=SF+Pro+Display:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
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
        
        .modern-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        /* Form styling */
        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--sf-text-primary);
            font-size: 16px;
        }

        .form-control {
            width: 100%;
            padding: 16px;
            border: 1px solid var(--sf-divider);
            border-radius: 12px;
            background: var(--sf-background);
            font-size: 16px;
            color: var(--sf-text-primary);
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--sf-blue);
            box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.1);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
        }

        .form-text {
            font-size: 14px;
            color: var(--sf-text-secondary);
            margin-top: 6px;
        }

        select.form-control {
            cursor: pointer;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        .submit-container {
            text-align: center;
            margin-top: 32px;
        }

        .btn-large {
            padding: 16px 32px;
            font-size: 18px;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="main-content p-6">
        <!-- Back Link -->
        <div class="mb-6">
            <a href="havetopay.php" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium">
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
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                <div class="flex">
                    <i class="fas fa-exclamation-circle mr-3 mt-1"></i>
                    <div>
                        <strong>Please fix the following errors:</strong>
                        <ul class="list-disc list-inside mt-2">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                <i class="fas fa-check-circle mr-3"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <!-- Add Expense Form -->
        <div class="modern-card">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 text-white p-6 rounded-t-2xl">
                <h2 class="text-xl font-bold flex items-center">
                    <i class="fas fa-plus mr-3"></i>Expense Details
                </h2>
            </div>
            <div class="p-6">
                <form action="" method="POST" class="space-y-6">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" id="title" name="title" class="form-control" 
                                   placeholder="What is this expense for?" required
                                   value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="amount" class="form-label">Amount</label>
                            <div style="position: relative;">
                                <span style="position: absolute; left: 16px; top: 16px; color: var(--sf-text-secondary);">€</span>
                                <input type="number" id="amount" name="amount" class="form-control" 
                                       style="padding-left: 40px;" step="0.01" min="0.01" placeholder="0.00" required
                                       value="<?php echo htmlspecialchars($_POST['amount'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="expense_date" class="form-label">Date</label>
                            <input type="date" id="expense_date" name="expense_date" class="form-control"
                                   value="<?php echo htmlspecialchars($_POST['expense_date'] ?? date('Y-m-d')); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="category" class="form-label">Category</label>
                            <select id="category" name="category" class="form-control">
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['name']); ?>"
                                        <?php echo ($_POST['category'] ?? '') == $category['name'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="group_id" class="form-label">Group (Optional)</label>
                            <select id="group_id" name="group_id" class="form-control">
                                <option value="">-- No Group --</option>
                                <?php foreach ($allGroups as $group): ?>
                                <option value="<?php echo $group['id']; ?>"
                                        <?php echo ($_POST['group_id'] ?? '') == $group['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($group['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="participants" class="form-label">Split With</label>
                            <select id="participants" name="participants[]" multiple class="form-control" 
                                    style="min-height: 120px;" required>
                                <?php foreach ($allUsers as $user): ?>
                                <option value="<?php echo $user['id']; ?>"
                                        <?php echo in_array($user['id'], $_POST['participants'] ?? []) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($user['display_name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Hold Ctrl/Cmd to select multiple participants</div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control"
                                  placeholder="Add any details about this expense..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="submit-container">
                        <button type="submit" class="btn btn-primary btn-large">
                            <i class="fas fa-plus-circle"></i>
                            Add Expense
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
