<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';
requireLogin();

$userId = $_SESSION['user_id'];
$sub    = $_POST['subtab'] ?? '';

/* -------- Public profile -------------------------------------------- */
if ($sub === 'public_profile') {
    $bio   = trim($_POST['bio'] ?? '');
    $links = $_POST['links'] ?? [];

    // nur nicht-leere Links speichern
    $links = array_filter($links, fn($v) => $v !== '');

    $stmt = $pdo->prepare(
      'UPDATE users SET bio = ?, links = ? WHERE id = ?'
    );
    $stmt->execute([$bio, json_encode($links), $userId]);
}

/* -------- HR Information -------------------------------------------- */
if ($sub === 'hr_information') {
    $data = [
      'job_title'     => $_POST['job_title']     ?? null,
      'department'    => $_POST['department']    ?? null,
      'employee_id'   => $_POST['employee_id']   ?? null,
      'start_date'    => $_POST['start_date']    ?: null,
      'manager'       => $_POST['manager']       ?? null,
      'work_location' => $_POST['work_location'] ?? null,
    ];

    $set = [];
    foreach ($data as $col=>$val) $set[] = "`$col` = :$col";
    $sql = 'UPDATE users SET '.implode(', ',$set).' WHERE id = :id';

    $stmt = $pdo->prepare($sql);
    $data['id'] = $userId;
    $stmt->execute($data);
}

/* -------- Personal data --------------------------------------------- */
if ($sub === 'personal_data') {
    $data = [
      'first_name'   => $_POST['first_name']   ?? null,
      'last_name'    => $_POST['last_name']    ?? null,
      'dob'          => $_POST['dob']          ?: null,
      'nationality'  => $_POST['nationality']  ?? null,
      'street'       => $_POST['street']       ?? null,
      'zip'          => $_POST['zip']          ?? null,
      'city'         => $_POST['city']         ?? null,
      'country'      => $_POST['country']      ?? null,
      'phone'        => $_POST['phone']        ?? null,
      'private_email'=> $_POST['private_email']?? null,
    ];

    $set = [];
    foreach ($data as $col=>$val) $set[] = "`$col` = :$col";
    $sql = 'UPDATE users SET '.implode(', ',$set).' WHERE id = :id';

    $stmt = $pdo->prepare($sql);
    $data['id'] = $userId;
    $stmt->execute($data);
}

/* -------------------------------------------------------------------- */
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
