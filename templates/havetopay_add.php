<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense | HaveToPay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        :root {
            --primary-color: #4F46E5;
            --primary-light: #EEF2FF;
            --primary-dark: #3730A3;
            --success-color: #10B981;
            --success-light: #ECFDF5;
            --danger-color: #EF4444;
            --danger-light: #FEF2F2;
            --warning-color: #F59E0B;
            --text-dark: #1F2937;
            --text-muted: #6B7280;
            --surface-card: #FFFFFF;
            --surface-bg: #F9FAFB;
            --border-radius: 16px;
            --input-radius: 12px;
            --transition: all 0.3s ease;
        }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--surface-bg);
            color: var(--text-dark);
        }
        
        .content-container {
            padding: 2rem 1rem 3rem;
        }
        
        /* Modern card styling */
        .modern-card {
            background: #FFFFFF;
            border-radius: var(--border-radius);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: none;
            overflow: hidden;
        }
        
        .modern-card .card-header {
            padding: 1.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border: none;
        }
        
        .modern-card .card-body {
            padding: 2rem;
        }
        
        /* Form controls */
        .form-label {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            padding: 0.75rem 1rem;
            font-size: 1rem;
            border-radius: var(--input-radius);
            border: 2px solid #E5E7EB;
            transition: var(--transition);
            background-color: #F9FAFB;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }
        
        .input-group-text {
            border-radius: var(--input-radius) 0 0 var(--input-radius);
            border: 2px solid #E5E7EB;
            border-right: none;
            background-color: #F3F4F6;
            color: var(--text-dark);
            padding: 0.75rem 1rem;
            font-weight: 600;
        }
        
        .input-group .form-control {
            border-radius: 0 var(--input-radius) var(--input-radius) 0;
        }
        
        /* Modern buttons */
        .btn-modern-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 12px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
            transition: var(--transition);
        }
        
        .btn-modern-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.4);
            color: white;
        }
        
        .btn-modern-outline {
            background: white;
            border: 2px solid #E5E7EB;
            border-radius: 12px;
            padding: 0.75rem 2rem;
            color: var(--text-dark);
            font-weight: 600;
            transition: var(--transition);
        }
        
        .btn-modern-outline:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        /* Back link */
        .back-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            transition: var(--transition);
        }
        
        .back-link:hover {
            color: var(--primary-dark);
            transform: translateX(-3px);
        }
        
        .back-link i {
            margin-right: 8px;
            transition: var(--transition);
        }
        
        .back-link:hover i {
            transform: translateX(-3px);
        }
        
        /* Modern alerts */
        .modern-alert {
            border-radius: var(--input-radius);
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            border: none;
            display: flex;
            align-items: flex-start;
        }
        
        .modern-alert.success {
            background-color: var(--success-light);
            color: var(--success-color);
        }
        
        .modern-alert.warning {
            background-color: var(--danger-light);
            color: var(--danger-color);
        }
        
        .modern-alert i {
            margin-right: 0.75rem;
            font-size: 1.25rem;
            margin-top: 0.2rem;
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .main-content {
            animation: fadeIn 0.6s ease-out;
        }
        
        /* Select2 customization */
        .select2-container--default .select2-selection--multiple {
            padding: 0.5rem;
            border: 2px solid #E5E7EB;
            border-radius: var(--input-radius);
            min-height: calc(3.5rem + 2px);
            background-color: #F9FAFB;
        }
        
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }
        
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: var(--primary-light);
            border: none;
            border-radius: 50px;
            padding: 5px 10px;
            color: var(--primary-color);
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .select2-container--default .select2-selection__choice__remove {
            color: var(--primary-color);
            margin-right: 5px;
        }
        
        .select2-dropdown {
            border: 2px solid var(--primary-color);
            border-radius: var(--input-radius);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }
        
        /* Form section styling */
        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #F3F4F6;
        }
        
        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .form-section-title {
            font-weight: 700;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
            color: var(--text-dark);
            display: flex;
            align-items: center;
        }
        
        .form-section-title i {
            margin-right: 10px;
            color: var(--primary-color);
            font-size: 1.1rem;
        }
        
        /* Adjustments for navbar integration */
        @media (min-width: 769px) {
            .content-container {
                margin-left: 16rem;
                width: calc(100% - 16rem);
                padding: 2rem 1.5rem 3rem;
            }
        }
        
        @media (max-width: 768px) {
            .modern-card .card-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <?php if (file_exists(__DIR__.'/navbar.php')) require_once __DIR__.'/navbar.php'; ?>
    
    <div class="content-container">
        <div class="container main-content">
            <!-- Back Link -->
            <div class="mb-4">
                <a href="havetopay.php" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    Back to HaveToPay
                </a>
            </div>
            
            <!-- Header -->
            <div class="mb-4">
                <h1 class="fw-bold mb-1">Add New Expense</h1>
                <p class="text-muted">Split expenses with your friends and track who owes what</p>
            </div>
            
            <!-- Error and Success Messages -->
            <?php if (!empty($errors)): ?>
                <div class="modern-alert warning">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 ps-3 mt-1">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="modern-alert success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <!-- Add Expense Form -->
            <div class="modern-card mb-4">
                <div class="card-header">
                    <h2 class="card-title mb-0 fw-bold">Expense Details</h2>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <h3 class="form-section-title">
                                <i class="fas fa-info-circle"></i>Basic Information
                            </h3>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="title" class="form-label">Title*</label>
                                    <input type="text" class="form-control" id="title" name="title" placeholder="What is this expense for?" required>
                                    <div class="form-text">Give your expense a clear name</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="amount" class="form-label">Amount*</label>
                                    <div class="input-group">
                                        <span class="input-group-text">€</span>
                                        <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0.01" placeholder="0.00" required>
                                    </div>
                                    <div class="form-text">Enter the total amount paid</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Details Section -->
                        <div class="form-section">
                            <h3 class="form-section-title">
                                <i class="fas fa-clipboard-list"></i>Details
                            </h3>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="expense_date" class="form-label">Date</label>
                                    <input type="date" class="form-control" id="expense_date" name="expense_date" value="<?php echo date('Y-m-d'); ?>">
                                    <div class="form-text">When did you make this payment?</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="category" class="form-label">Category</label>
                                    <select class="form-select" id="category" name="category">
                                        <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo htmlspecialchars($category['name']); ?>">
                                            <i class="fas <?php echo htmlspecialchars($category['icon']); ?>"></i> 
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Select a category for this expense</div>
                                </div>
                                <div class="col-12">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Add any details about this expense..."></textarea>
                                    <div class="form-text">Optional: Add more details about this expense</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Split Section -->
                        <div class="form-section">
                            <h3 class="form-section-title">
                                <i class="fas fa-users"></i>Split With
                            </h3>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="group_id" class="form-label">Group (Optional)</label>
                                    <select class="form-select" id="group_id" name="group_id">
                                        <option value="">-- No Group --</option>
                                        <?php foreach ($allGroups as $group): ?>
                                        <option value="<?php echo $group['id']; ?>">
                                            <?php echo htmlspecialchars($group['name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Optionally assign to a group</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="participants" class="form-label">Split With*</label>
                                    <select class="form-select participants-select" id="participants" name="participants[]" multiple required>
                                        <?php foreach ($allUsers as $user): ?>
                                        <option value="<?php echo $user['id']; ?>">
                                            <?php echo htmlspecialchars($user['display_name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Select who to split this expense with</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="submit" class="btn-modern-primary">
                                <i class="fas fa-plus-circle me-2"></i>Add Expense
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 for better multi-select experience
            $('.participants-select').select2({
                placeholder: "Select participants",
                width: '100%'
            });
            
            // Add nice icons to the category dropdown
            $("#category").each(function() {
                const select = $(this);
                select.find("option").each(function() {
                    const option = $(this);
                    const icon = option.find("i").attr("class");
                    if (icon) {
                        option.text(option.text().replace(icon, ""));
                    }
                });
            });
        });
    </script>
</body>
</html>
