<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'PrivateVault'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            padding-top: 76px; /* Adjusted for fixed navbar */
        }
        
        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        
        .navbar-brand {
            font-weight: 600;
            font-size: 1.25rem;
        }
        
        .navbar .nav-link {
            font-weight: 500;
            position: relative;
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        .navbar .nav-link:hover {
            color: #3a7bd5;
        }
        
        .navbar .nav-link.active {
            color: #3a7bd5;
        }
        
        /* Underline effect for active link */
        .navbar .nav-link.active::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0.5rem;
            right: 0.5rem;
            height: 3px;
            background-color: #3a7bd5;
            border-radius: 3px 3px 0 0;
        }
        
        .navbar-toggler {
            border: none;
            padding: 0.5rem;
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
        }
        
        .dropdown-menu {
            border-radius: 0.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border: none;
        }
        
        .dropdown-item:active {
            background-color: #3a7bd5;
        }
        
        /* Fix dropdown positioning */
        .dropdown-menu {
            margin-top: 0.5rem;
        }
        
        /* Remove unnecessary margins from navbar */
        .navbar-collapse {
            flex-grow: 0;
        }
        
        /* Fix issue with navbar collapse transition */
        @media (max-width: 991px) {
            .navbar-collapse.collapsing,
            .navbar-collapse.show {
                background-color: #fff;
                padding: 1rem;
                border-radius: 0.5rem;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                position: absolute;
                top: 100%;
                right: 0;
                width: auto;
                min-width: 200px;
                z-index: 1000;
            }
        }
    </style>
</head>
<body>

<!-- Fixed Navbar -->
<nav class="navbar navbar-expand-lg fixed-top navbar-light">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-vault me-2"></i>PrivateVault
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" 
                aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <?php if (isset($_SESSION['user_id'])): ?>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                            <i class="fas fa-home me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'taskboard.php' ? 'active' : ''; ?>" href="taskboard.php">
                            <i class="fas fa-tasks me-1"></i> Tasks
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'havetopay.php' ? 'active' : ''; ?>" href="havetopay.php">
                            <i class="fas fa-money-bill-wave me-1"></i> HaveToPay
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-id-card me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            <?php else: ?>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active' : ''; ?>" href="register.php">Register</a>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Page content begins -->
<div class="container mt-4">
