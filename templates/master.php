<?php
// Master template file that combines header, navbar, content and footer
// Usage: include this file and set appropriate variables before including

// Default variables
$pageTitle = $pageTitle ?? 'Private Vault';
$bodyClasses = $bodyClasses ?? '';
$contentClasses = $contentClasses ?? 'p-4 md:p-8';
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Apple UI Styling -->
    <link rel="stylesheet" href="/assets/css/apple-ui.css">
    
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom page styles -->
    <?php if (isset($customStyles)): ?>
        <style>
            <?= $customStyles ?>
        </style>
    <?php endif; ?>
</head>
<body class="min-h-screen <?= $bodyClasses ?>">

<?php include_once __DIR__ . '/navbar.php'; ?>

<main class="content-container ml-0 mt-14 md:ml-64 md:mt-0 <?= $contentClasses ?>">
    <?php if (isset($content)): ?>
        <?= $content ?>
    <?php else: ?>
        <!-- Default content goes here or a content placeholder -->
        <div class="bg-red-100 p-4 rounded-lg">
            <p class="text-red-700">No content provided. Please set the $content variable before including this template.</p>
        </div>
    <?php endif; ?>
</main>

<!-- Default Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle light/dark mode toggles if present
    const lightModeBtn = document.getElementById('lightModeBtn');
    const darkModeBtn = document.getElementById('darkModeBtn');
    
    if (lightModeBtn && darkModeBtn) {
        lightModeBtn.addEventListener('click', () => {
            document.body.classList.remove('dark-mode');
            localStorage.setItem('colorTheme', 'light');
        });
        
        darkModeBtn.addEventListener('click', () => {
            document.body.classList.add('dark-mode');
            localStorage.setItem('colorTheme', 'dark');
        });
        
        // Check for saved theme preference
        const savedTheme = localStorage.getItem('colorTheme');
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-mode');
        }
    }
});
</script>

<?php if (isset($customScripts)): ?>
    <script>
        <?= $customScripts ?>
    </script>
<?php endif; ?>

</body>
</html>
