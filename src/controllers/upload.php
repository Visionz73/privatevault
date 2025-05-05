<?php
// src/controllers/upload.php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';
requireLogin();

/* ------------------------------------------------------------------ */
/*   Kategorien für das Dropdown laden                                */
$cats = $pdo->query(
  'SELECT id, name 
     FROM document_categories 
  ORDER BY name'
)->fetchAll(PDO::FETCH_ASSOC);

/* ------------------------------------------------------------------ */
$uploadError   = '';
$uploadSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title      = trim($_POST['title'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);

    /* --- Datei vorhanden? ------------------------------------------ */
    if (empty($_FILES['docfile']) || $_FILES['docfile']['error'] !== UPLOAD_ERR_OK) {
        $uploadError = 'Fehler beim Datei-Upload.';
    } else {
        /* --- Dateiendung prüfen ------------------------------------ */
        $ext      = strtolower(pathinfo($_FILES['docfile']['name'], PATHINFO_EXTENSION));
        $allowed  = ['pdf','png','jpeg','jpg','docx'];

        if (!in_array($ext, $allowed, true)) {
            $uploadError = 'Dateityp nicht erlaubt.';
        } elseif ($categoryId === 0) {
            $uploadError = 'Bitte eine Kategorie auswählen.';
        } else {
            /* --- Upload-Verzeichnis erstellen, falls nötig ------------ */
            $uploadDir = __DIR__ . '/../../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            /* --- Neuen Dateinamen generieren ------------------------- */
            $newName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

            /* --- Datei verschieben ----------------------------------- */
            if (move_uploaded_file($_FILES['docfile']['tmp_name'], $uploadDir . $newName)) {
                /* --- In DB schreiben ---------------------------------- */
                $stmt = $pdo->prepare(
                  'INSERT INTO documents
                     (user_id, title, filename, original_name, category_id, upload_date, is_deleted)
                   VALUES (?, ?, ?, ?, ?, NOW(), 0)'
                );
                $stmt->execute([
                  $_SESSION['user_id'],
                  $title,
                  $newName,
                  $_FILES['docfile']['name'],
                  $categoryId
                ]);

                $uploadSuccess = 'Upload erfolgreich!';
            } else {
                $uploadError = 'Die Datei konnte nicht gespeichert werden.';
            }
        }
    }
}

/* ------------------------------------------------------------------ */
/*   Form-Template einbinden                                         */
require_once __DIR__ . '/../../templates/upload_form.php';
