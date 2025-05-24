<?php
// This template is usually included by a controller.
// $currentDisplayName and $currentEmail are expected to be set by the controller (src/controllers/settings.php).
// Session messages are also handled by the controller's redirect.
// session_start() is expected to be called by the entry point script (e.g., public/settings.php)
?>
<main class="container mt-5 pt-4"> <?php // Added pt-4 for spacing due to fixed navbar ?>
    <div class="row">
        <div class="col-md-8 offset-md-2"> <?php // Center content ?>
            
            <h1 class="mb-4 text-center">User Settings</h1> <?php // Centered main title ?>

            <?php // Display session feedback messages ?>
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success_message']); // Clear message after displaying ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php 
                        // Using nl2br to convert newline characters to <br> tags for multi-line error messages
                        echo nl2br(htmlspecialchars($_SESSION['error_message'])); 
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error_message']); // Clear message after displaying ?>
            <?php endif; ?>

            <?php // Account Information Form ?>
            <section class="card mb-4">
                <div class="card-header">
                    <h2>Account Information</h2>
                </div>
                <div class="card-body">
                    <form action="settings.php" method="POST"> <?php // Action explicitly points to settings.php ?>
                        <div class="mb-3">
                            <label for="displayName" class="form-label">Display Name</label>
                            <input type="text" class="form-control" name="displayName" id="displayName" 
                                   value="<?php echo htmlspecialchars($currentDisplayName ?? ''); ?>" 
                                   placeholder="Enter your display name" required> <?php // Added 'required' attribute ?>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" id="email" 
                                   value="<?php echo htmlspecialchars($currentEmail ?? ''); ?>" 
                                   placeholder="Enter your email address" required> <?php // Added 'required' attribute ?>
                        </div>
                        <button type="submit" name="saveAccountChanges" class="btn btn-primary">Save Account Changes</button>
                    </form>
                </div>
            </section>

            <?php // Security Section ?>
            <section class="card mb-5"> <?php // Added mb-5 for more spacing before "Back to profile" link ?>
                <div class="card-header">
                    <h2>Security</h2>
                </div>
                <div class="card-body">
                    <p>To change your password, please proceed to the password change page.</p>
                    <a href="change_password.php" class="btn btn-secondary">Change Password</a>
                </div>
            </section>
            
            <div class="text-center"> <?php // Centered the link, removed extra p tag and div's mt-4. Relies on card's mb-5 now. ?>
                 <a href="profile.php">Back to Profile</a>
            </div>

        </div>
    </div>
</main>
