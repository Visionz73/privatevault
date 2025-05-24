<?php
// This template is included by src/controllers/change_password.php
// Assumes header/footer are included by the controller.
// $pageTitle is expected to be set by the controller (src/controllers/change_password.php).
?>
<main class="container mt-5 pt-4"> <?php // Added pt-4 for spacing due to fixed navbar, and main tag ?>
    <div class="row">
        <div class="col-md-8 offset-md-2"> <?php // Centering content ?>
            
            <?php // Using a card for consistent styling with settings.php ?>
            <section class="card">
                <div class="card-header">
                    <?php // $pageTitle is set in src/controllers/change_password.php ?>
                    <h2><?php echo htmlspecialchars($pageTitle ?? 'Change Password'); ?></h2>
                </div>
                <div class="card-body">
                    <p class="lead">This feature is currently under development.</p> <?php // Using lead class for emphasis ?>
                    <p>The form to change your password will be available here soon.</p>
                    
                    <div class="mt-4 text-center"> <?php // Spacing and centering for the button ?>
                        <a href="settings.php" class="btn btn-outline-secondary">Back to Settings</a> <?php // Using outline button style ?>
                    </div>
                </div>
            </section>

        </div>
    </div>
</main>
