<?php
// src/controllers/profile.php

require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';

requireLogin();

// 1) Tabs und aktiven Tab bestimmen
$tabs      = ['personal_info','finance','documents'];
$activeTab = $_GET['tab'] ?? 'personal_info';
if (!in_array($activeTab, $tabs, true)) {
    $activeTab = 'personal_info';
}

// 2) Subtab nur relevant für personal_info
$subTab = $_GET['subtab'] ?? '';

// 3) POST-Handling für Personal Info
$success = '';
$errors  = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && $activeTab === 'personal_info'
    && $subTab === ''
) {
    $first = trim($_POST['first_name'] ?? '');
    $last  = trim($_POST['last_name']  ?? '');
    $birth = $_POST['birthdate']      ?? null;
    $job   = trim($_POST['job_title']  ?? '');
    $loc   = trim($_POST['location']   ?? '');

    if ($first === '' || $last === '') {
        $errors[] = 'Vor- und Nachname sind Pflicht.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            UPDATE users SET
              first_name = ?, last_name = ?, birthdate = ?,
              job_title = ?, location = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$first, $last, $birth, $job, $loc, $_SESSION['user_id']]);
        $success = 'Personal Info wurde gespeichert.';
    }
}

// 4) POST-Handling für Public Profile
$publicSuccess = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && $activeTab === 'personal_info'
    && $subTab === 'public_profile'
) {
    $linkedin = trim($_POST['linkedin'] ?? '');
    $twitter  = trim($_POST['twitter']  ?? '');
    $xing     = trim($_POST['xing']     ?? '');

    $stmt = $pdo->prepare("
      UPDATE users SET linkedin = ?, twitter = ?, xing = ?, updated_at = NOW()
      WHERE id = ?
    ");
    $stmt->execute([$linkedin, $twitter, $xing, $_SESSION['user_id']]);
    $publicSuccess = 'Public Profile wurde gespeichert.';
}

// 5) POST-Handling für Finance-Tab
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $activeTab === 'finance') {
    $description = trim($_POST['description'] ?? '');
    $type        = $_POST['type']            ?? '';
    $amount      = $_POST['amount']          ?? '';
    $entryDate   = $_POST['entry_date']      ?? '';

    if ($description === '' || !in_array($type, ['income','expense'], true) || !is_numeric($amount)) {
        $errors[] = 'Bitte alle Felder korrekt ausfüllen.';
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO finance_entries
              (user_id, type, amount, entry_date, note, currency)
            VALUES (?,?,?,?,?,'EUR')
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            $type,
            $amount,
            $entryDate,
            $description
        ]);
        $publicSuccess = 'Eintrag erfolgreich hinzugefügt.';
    }
}

// 6) Finance-Einträge laden & löschen
$financeEntries = [];
if ($activeTab === 'finance') {
    if (!empty($_GET['delete_finance']) && is_numeric($_GET['delete_finance'])) {
        $stmt = $pdo->prepare('DELETE FROM finance_entries WHERE id = ? AND user_id = ?');
        $stmt->execute([$_GET['delete_finance'], $_SESSION['user_id']]);
    }
    $stmt = $pdo->prepare("
        SELECT * FROM finance_entries
        WHERE user_id = ?
        ORDER BY entry_date DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $financeEntries = $stmt->fetchAll();
}

// 6a) Totale berechnen
$totalIncome  = 0.0;
$totalExpense = 0.0;
foreach ($financeEntries as $f) {
    if ($f['type'] === 'income') {
        $totalIncome  += (float)$f['amount'];
    } else {
        $totalExpense += (float)$f['amount'];
    }
}
$balance = $totalIncome - $totalExpense;


// 7) Dokumente laden & löschen
$docs = [];
if ($activeTab === 'documents') {
    if (!empty($_GET['delete']) && is_numeric($_GET['delete'])) {
        $stmt = $pdo->prepare('UPDATE documents SET is_deleted = 1 WHERE id = ? AND user_id = ?');
        $stmt->execute([$_GET['delete'], $_SESSION['user_id']]);
    }
    $stmt = $pdo->prepare("
        SELECT * FROM documents
        WHERE user_id = ? AND is_deleted = 0
        ORDER BY upload_date DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $docs = $stmt->fetchAll();
}

// 8) Userdaten für sämtliche Tabs
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// 9) Template rendern
require_once __DIR__ . '/../../templates/profile.php';
