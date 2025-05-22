<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense | HaveToPay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border: none;
        }
        .card-header {
            border-radius: 15px 15px 0 0 !important;
            border: none;
        }
        .btn-primary {
            background: linear-gradient(45deg, #3a7bd5, #00d2ff);
            border: none;
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 500;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #3a7bd5, #3a7bd5);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Add New Expense</h1>
            <a href="havetopay.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
        
        <!-- Error and Success Messages -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <!-- Add Expense Form -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Expense Details</h5>
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label">Title*</label>
                            <input type="text" class="form-control" id="title" name="title" required
                                   value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                                   placeholder="e.g. Pizza for Movie Night">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="amount" class="form-label">Amount*</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0.01" required
                                       value="<?= htmlspecialchars($_POST['amount'] ?? '') ?>"
                                       placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="expense_date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="expense_date" name="expense_date" required
                                   value="<?= htmlspecialchars($_POST['expense_date'] ?? date('Y-m-d')) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['name']); ?>">
                                    <i class="fas <?php echo htmlspecialchars($category['icon']); ?>"></i> 
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                                  placeholder="Optional details about the expense"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="group_id" class="form-label">Group (Optional)</label>
                            <select class="form-select" id="group_id" name="group_id">
                                <option value="">-- No Group --</option>
                                <?php foreach ($allGroups as $group): ?>
                                <option value="<?php echo $group['id']; ?>">
                                    <?php echo htmlspecialchars($group['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="participants" class="form-label">Split With*</label>
                            <select class="form-select" id="participants" name="participants[]" multiple required>
                                <?php foreach ($allUsers as $user): ?>
                                <option value="<?php echo $user['id']; ?>">
                                    <?php echo htmlspecialchars($user['username']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Hold Ctrl/Cmd to select multiple users</div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-2"></i>Add Expense
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
