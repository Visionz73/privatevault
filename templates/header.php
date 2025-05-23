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
    <link rel="stylesheet" href="assets/css/improvements.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            padding-top: 76px;
        }
        
        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            background-color: #fff !important;
            z-index: 1030;
        }
        
        .navbar-brand {
            font-weight: 600;
            font-size: 1.25rem;
        }
        
        .navbar .nav-link {
            font-weight: 500;
            position: relative;
            padding: 0.5rem 1rem !important;
            transition: color 0.2s ease;
        }
        
        .navbar .nav-link:hover {
            color: #3a7bd5;
        }
        
        .navbar .nav-link.active {
            color: #3a7bd5 !important;
            font-weight: 600;
        }
        
        .navbar .nav-link.active::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 1rem;
            right: 1rem;
            height: 3px;
            background-color: #3a7bd5;
            border-radius: 3px 3px 0 0;
        }
        
        .navbar-toggler {
            border: none !important;
            padding: 0.5rem;
        }
        
        .navbar-toggler:focus {
            box-shadow: none !important;
        }
        
        .dropdown-menu {
            border-radius: 0.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border: none;
            margin-top: 0.5rem;
        }
        
        .dropdown-item:active {
            background-color: #3a7bd5;
        }
        
        @media (max-width: 991px) {
            .navbar-collapse {
                background-color: #fff;
                padding: 1rem;
                border-radius: 0.5rem;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                position: absolute;
                top: 100%;
                right: 1rem;
                left: 1rem;
                z-index: 1040;
            }
            
            .navbar-nav .nav-item {
                margin-bottom: 0.5rem;
            }
        }

        /* Slide-up animation for alerts */
        @keyframes slideUp {
            0% {
                opacity: 0;
                transform: translateY(10px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert {
            animation: slideUp 0.5s ease-out forwards;
        }
    </style>
</head>
<body>

<!-- Unified Fixed Navbar -->
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
                        <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'havetopay') !== false ? 'active' : ''; ?>" href="havetopay.php">
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




































    <?php endif; ?>    </div>        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>        <?php echo htmlspecialchars($successMessage); ?>        <i class="fas fa-check-circle me-2"></i>    <div class="alert alert-success alert-dismissible fade show slide-up" role="alert">    <?php if (!empty($successMessage)): ?>        <?php endif; ?>    </div>        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>        <?php echo htmlspecialchars($errorMessage); ?>        <i class="fas fa-exclamation-circle me-2"></i>    <div class="alert alert-danger alert-dismissible fade show slide-up" role="alert">    <?php if (!empty($errorMessage)): ?><div class="container mt-4"><!-- Page content begins with enhanced messaging --></nav>    </div>        </div>            <?php endif; ?>                </ul>                    </li>                        </a>                            <i class="fas fa-user-plus me-1"></i> Register                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active' : ''; ?>" href="register.php">                    <li class="nav-item">                    </li>                        </a>                            <i class="fas fa-sign-in-alt me-1"></i> Login                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>" href="login.php">                    <li class="nav-item">                <ul class="navbar-nav ms-auto">            <?php else: ?>                </ul></nav>

<!-- Page content begins -->
<div class="container mt-4">
