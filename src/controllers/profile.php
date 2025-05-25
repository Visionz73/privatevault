<?php
// src/controllers/profile.php

require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';

requireLogin();

// 1) Tabs und aktiven Tab bestimmen
$tabs      = ['personal_info','finance','documents', 'security', 'notifications']; // Added security & notifications
$activeTab = $_GET['tab'] ?? 'personal_info';
// No strict validation against $tabs here, as some tabs might directly include content.
// The controller primarily manages data for personal_info, finance, documents, and now security POST.

// 2) Subtab
$subTab = $_GET['subtab'] ?? '';

// Initialize messages and CSRF token variables
$success = ''; 
$errors  = []; 

// CSRF Token Generation
$csrf_token_personal_info = '';
if ($activeTab === 'personal_info' && $subTab === '') {
    if (empty($_SESSION['csrf_token_personal_info'])) {
        $_SESSION['csrf_token_personal_info'] = bin2hex(random_bytes(32));
    }
    $csrf_token_personal_info = $_SESSION['csrf_token_personal_info'];
}

$csrf_token_public_profile = ''; 
if ($activeTab === 'personal_info' && $subTab === 'public_profile') {
    if (empty($_SESSION['csrf_token_public_profile'])) {
        $_SESSION['csrf_token_public_profile'] = bin2hex(random_bytes(32));
    }
    $csrf_token_public_profile = $_SESSION['csrf_token_public_profile'];
}

$csrf_token_hr_info = ''; 
if ($activeTab === 'personal_info' && $subTab === 'hr_information') {
    if (empty($_SESSION['csrf_token_hr_info'])) {
        $_SESSION['csrf_token_hr_info'] = bin2hex(random_bytes(32));
    }
    $csrf_token_hr_info = $_SESSION['csrf_token_hr_info'];
}

$csrf_token_personal_data = ''; 
if ($activeTab === 'personal_info' && $subTab === 'personal_data') {
    if (empty($_SESSION['csrf_token_personal_data'])) {
        $_SESSION['csrf_token_personal_data'] = bin2hex(random_bytes(32));
    }
    $csrf_token_personal_data = $_SESSION['csrf_token_personal_data'];
}

$csrf_token_change_password_profile = '';
if ($activeTab === 'security') { // Generate when security tab is active
    if (empty($_SESSION['csrf_token_change_password_profile'])) {
        $_SESSION['csrf_token_change_password_profile'] = bin2hex(random_bytes(32));
    }
    $csrf_token_change_password_profile = $_SESSION['csrf_token_change_password_profile'];
}


// POST-Handling für Personal Info (main tab, no specific subtab for this form)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $activeTab === 'personal_info' && $subTab === '' && isset($_POST['action']) && $_POST['action'] === 'update_personal_info') {
    // ... (logic from turn 152, collapsed for brevity) ...
    if (!isset($_POST['csrf_token_personal_info']) || !hash_equals($_SESSION['csrf_token_personal_info'] ?? '', $_POST['csrf_token_personal_info'])) {
        $errors[] = "Invalid security token for personal info. Please try again.";
        unset($_SESSION['csrf_token_personal_info']);
    } else {
        unset($_SESSION['csrf_token_personal_info']); 
        $first = trim($_POST['first_name'] ?? ''); $last  = trim($_POST['last_name']  ?? ''); $birth = $_POST['birthdate'] ?? null; $job = trim($_POST['job_title']  ?? ''); $loc = trim($_POST['location']   ?? '');
        if ($first === '' || $last === '') { $errors[] = 'Vor- und Nachname sind Pflicht.'; }
        if (empty($errors)) {
            try { $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, birthdate = ?, job_title = ?, location = ?, updated_at = NOW() WHERE id = ?"); $stmt->execute([$first, $last, $birth, $job, $loc, $_SESSION['user_id']]); $success = 'Personal Info wurde gespeichert.'; }
            catch (PDOException $e) { error_log("Error updating personal info: " . $e->getMessage()); $errors[] = "Ein Datenbankfehler ist aufgetreten."; }
        }
    }
    if (!empty($errors) && empty($_SESSION['csrf_token_personal_info'])) { $_SESSION['csrf_token_personal_info'] = bin2hex(random_bytes(32)); }
    $csrf_token_personal_info = $_SESSION['csrf_token_personal_info'] ?? ''; 
}

// POST-Handling for Public Profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_public_profile') {
    // ... (logic from turn 152, collapsed for brevity) ...
    if (!isset($_POST['csrf_token_public_profile']) || !hash_equals($_SESSION['csrf_token_public_profile'] ?? '', $_POST['csrf_token_public_profile'])) { $_SESSION['error_message'] = "Invalid security token for public profile. Please try again."; unset($_SESSION['csrf_token_public_profile']); }
    else {
        unset($_SESSION['csrf_token_public_profile']); $bio = trim($_POST['bio'] ?? ''); $links_input = $_POST['links'] ?? []; $current_form_errors = []; 
        if (strlen($bio) > 1000) { $current_form_errors[] = "Bio cannot exceed 1000 characters."; }
        $valid_links = []; foreach ($links_input as $key => $url) { $trimmed_url = trim($url); if (!empty($trimmed_url)) { if (!filter_var($trimmed_url, FILTER_VALIDATE_URL)) { $current_form_errors[] = "Invalid URL provided for " . htmlspecialchars(ucfirst(str_replace('_', ' ', $key))) . "."; } else { $valid_links[$key] = $trimmed_url; } } else { $valid_links[$key] = ''; } }
        if (empty($current_form_errors)) { try { $stmt = $pdo->prepare('UPDATE users SET bio = ?, links = ? WHERE id = ?'); if ($stmt->execute([$bio, json_encode($valid_links), $_SESSION['user_id']])) { $_SESSION['success_message'] = "Public profile updated successfully."; } else { $_SESSION['error_message'] = "Failed to update public profile."; } } catch (PDOException $e) { error_log("Error updating public profile: " . $e->getMessage()); $_SESSION['error_message'] = "A database error occurred while updating public profile."; } }
        else { $_SESSION['error_message'] = implode("<br>", $current_form_errors); }
    }
    header('Location: profile.php?tab=personal_info&subtab=public_profile'); exit;
}

// POST-Handling for HR Information
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_hr_info') {
    // ... (logic from turn 152, collapsed for brevity) ...
    if (!isset($_POST['csrf_token_hr_info']) || !hash_equals($_SESSION['csrf_token_hr_info'] ?? '', $_POST['csrf_token_hr_info'])) { $_SESSION['error_message'] = "Invalid security token for HR information. Please try again."; unset($_SESSION['csrf_token_hr_info']); }
    else {
        unset($_SESSION['csrf_token_hr_info']); $hr_data_to_update = []; $hr_fields = ['job_title', 'department', 'employee_id', 'start_date', 'manager', 'work_location']; $current_form_errors = [];
        foreach ($hr_fields as $field) { if (isset($_POST[$field])) { $value = trim($_POST[$field]); if ($field === 'start_date' && !empty($value) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) { $current_form_errors[] = "Invalid Start Date format. Please use YYYY-MM-DD."; } $hr_data_to_update[$field] = !empty($value) ? $value : null; } }
        if (empty($current_form_errors)) { if (!empty($hr_data_to_update)) { $set_clauses = []; foreach (array_keys($hr_data_to_update) as $col) { $set_clauses[] = "`" . str_replace('`', '``', $col) . "` = :$col"; } $sql = 'UPDATE users SET ' . implode(', ', $set_clauses) . ', updated_at = NOW() WHERE id = :id'; $hr_data_to_update['id'] = $_SESSION['user_id']; try { $stmt = $pdo->prepare($sql); if ($stmt->execute($hr_data_to_update)) { $_SESSION['success_message'] = "HR information updated successfully."; } else { $_SESSION['error_message'] = "Failed to update HR information."; } } catch (PDOException $e) { error_log("Error updating HR information: " . $e->getMessage()); $_SESSION['error_message'] = "A database error occurred while updating HR information."; } } else { $_SESSION['success_message'] = "HR information processed (no changes detected or submitted)."; } }
        else { $_SESSION['error_message'] = implode("<br>", $current_form_errors); }
    }
    header('Location: profile.php?tab=personal_info&subtab=hr_information'); exit;
}

// POST-Handling for Personal Data (Detailed)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_personal_data') {
    // ... (logic from turn 152, collapsed for brevity) ...
    if (!isset($_POST['csrf_token_personal_data']) || !hash_equals($_SESSION['csrf_token_personal_data'] ?? '', $_POST['csrf_token_personal_data'])) { $_SESSION['error_message'] = "Invalid security token for personal data. Please try again."; unset($_SESSION['csrf_token_personal_data']); }
    else {
        unset($_SESSION['csrf_token_personal_data']); $personal_data_to_update = []; $personal_data_fields = ['first_name', 'last_name', 'dob', 'nationality', 'street', 'zip', 'city', 'country', 'phone', 'private_email']; $current_form_errors = [];
        foreach ($personal_data_fields as $field) { if (isset($_POST[$field])) { $value = trim($_POST[$field]); if (($field === 'first_name' || $field === 'last_name') && empty($value)) { $current_form_errors[] = ucfirst(str_replace('_', ' ', $field)) . " cannot be empty."; } if ($field === 'dob' && !empty($value) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) { $current_form_errors[] = "Invalid Date of Birth format. Please use YYYY-MM-DD."; } if ($field === 'private_email' && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) { $current_form_errors[] = "Invalid Private Email format."; } $personal_data_to_update[$field] = !empty($value) ? $value : null; } }
        if (empty($current_form_errors)) { if (!empty($personal_data_to_update)) { $set_clauses = []; foreach (array_keys($personal_data_to_update) as $col) { $set_clauses[] = "`" . str_replace('`', '``', $col) . "` = :$col"; } $sql = 'UPDATE users SET ' . implode(', ', $set_clauses) . ', updated_at = NOW() WHERE id = :id'; $personal_data_to_update['id'] = $_SESSION['user_id']; try { $stmt = $pdo->prepare($sql); if ($stmt->execute($personal_data_to_update)) { $_SESSION['success_message'] = "Personal data updated successfully."; }  else { $_SESSION['error_message'] = "Failed to update personal data."; } } catch (PDOException $e) { error_log("Error updating personal data: " . $e->getMessage()); $_SESSION['error_message'] = "A database error occurred while updating personal data."; } } else { $_SESSION['success_message'] = "Personal data processed (no changes detected or submitted)."; } }
        else { $_SESSION['error_message'] = implode("<br>", $current_form_errors); }
    }
    header('Location: profile.php?tab=personal_info&subtab=personal_data'); exit;
}

// POST-Handling for Change Password (Moved from profile_security.php)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password_profile') {
    if (!isset($_POST['csrf_token_change_password_profile']) || !hash_equals($_SESSION['csrf_token_change_password_profile'] ?? '', $_POST['csrf_token_change_password_profile'])) {
        $_SESSION['error_message'] = "Invalid security token. Please try again.";
        unset($_SESSION['csrf_token_change_password_profile']);
    } else {
        unset($_SESSION['csrf_token_change_password_profile']); 

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmNewPassword = $_POST['confirm_new_password'] ?? '';
        
        $current_form_errors = [];

        if (empty($currentPassword) || empty($newPassword) || empty($confirmNewPassword)) {
            $current_form_errors[] = "All password fields are required.";
        }
        if ($newPassword !== $confirmNewPassword) {
            $current_form_errors[] = "New passwords do not match.";
        }
        if (strlen($newPassword) < 8) { $current_form_errors[] = "New password must be at least 8 characters long."; }
        if (!preg_match('/[A-Z]/', $newPassword)) { $current_form_errors[] = "New password must contain at least one uppercase letter."; }
        if (!preg_match('/[a-z]/', $newPassword)) { $current_form_errors[] = "New password must contain at least one lowercase letter."; }
        if (!preg_match('/[0-9]/', $newPassword)) { $current_form_errors[] = "New password must contain at least one digit."; }
        if (!preg_match('/[^A-Za-z0-9\s]/', $newPassword)) { $current_form_errors[] = "New password must contain at least one special character."; }

        if (empty($current_form_errors)) {
            try {
                $stmt_fetch = $pdo->prepare('SELECT password_hash FROM users WHERE id = ?');
                $stmt_fetch->execute([$_SESSION['user_id']]);
                $user_data = $stmt_fetch->fetch(PDO::FETCH_ASSOC);

                if ($user_data && password_verify($currentPassword, $user_data['password_hash'])) {
                    $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt_update = $pdo->prepare('UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?');
                    if ($stmt_update->execute([$hashedNewPassword, $_SESSION['user_id']])) {
                        $_SESSION['success_message'] = "Password updated successfully.";
                    } else { $_SESSION['error_message'] = "Failed to update password."; }
                } else { $_SESSION['error_message'] = "Incorrect current password."; }
            } catch (PDOException $e) {
                error_log("Error changing password: " . $e->getMessage());
                $_SESSION['error_message'] = "A database error occurred while changing password. Please try again.";
            }
        } else {
            $_SESSION['error_message'] = implode("<br>", $current_form_errors);
        }
    }
    // Regenerate CSRF token for the security tab if errors occurred
    if (!empty($_SESSION['error_message']) && empty($_SESSION['csrf_token_change_password_profile'])) {
        $_SESSION['csrf_token_change_password_profile'] = bin2hex(random_bytes(32));
    }
    header('Location: profile.php?tab=security');
    exit;
}


// POST-Handling für Finance-Tab (Add CSRF if this form is to be refactored here)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $activeTab === 'finance' && !isset($_POST['action'])) { 
    // ... (finance POST logic, collapsed for brevity) ...
    $description = trim($_POST['description'] ?? ''); $type = $_POST['type'] ?? ''; $amount  = $_POST['amount'] ?? ''; $entryDate = $_POST['entry_date'] ?? '';
    if ($description === '' || !in_array($type, ['income','expense'], true) || !is_numeric($amount) || empty($entryDate)) { $errors[] = 'Bitte alle Felder korrekt ausfüllen für den Finanz-Eintrag.'; }
    else {
        try { $stmt = $pdo->prepare("INSERT INTO finance_entries (user_id, type, amount, entry_date, note, currency) VALUES (?,?,?,?,?,'EUR')"); $stmt->execute([$_SESSION['user_id'], $type, $amount, $entryDate, $description]); $_SESSION['success_message'] = 'Finanz-Eintrag erfolgreich hinzugefügt.'; header('Location: profile.php?tab=finance'); exit; }
        catch (PDOException $e) { error_log("Error adding finance entry: " . $e->getMessage()); $errors[] = "Ein Datenbankfehler ist aufgetreten beim Hinzufügen des Finanz-Eintrags."; }
    }
}

// Data fetching for GET requests and for re-displaying form with errors
$financeEntries = []; $totalIncome  = 0.0; $totalExpense = 0.0; $balance = 0.0;
if ($activeTab === 'finance') { /* ... finance data fetching and delete ... */ } 
$docs = []; $category_documents = []; $current_category_name = '';
if ($activeTab === 'documents') { /* ... document data fetching and delete ... */ } 

try {
    $stmtUser = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmtUser->execute([$_SESSION['user_id']]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC); 
    if (!$user) { header("Location: login.php"); exit; }
} catch (PDOException $e) { error_log("Error fetching user data for profile: " . $e->getMessage()); $errors[] = "Fehler beim Laden der Benutzerdaten."; $user = []; }

require_once __DIR__ . '/../../templates/profile.php';

?>
