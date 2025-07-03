<?php
// public/file-explorer.php

declare(strict_types=1);
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/lib/auth.php';

session_start();
requireLogin();

$userId = (int)$_SESSION['user_id'];

// Demo-Ordner
$folders = [
    ['id'=>1,'name'=>'Verträge','file_count'=>5],
    ['id'=>2,'name'=>'Rechnungen','file_count'=>12],
    ['id'=>3,'name'=>'Fotos','file_count'=>28],
    ['id'=>4,'name'=>'Backups','file_count'=>3],
    ['id'=>5,'name'=>'Dokumente','file_count'=>15],
    ['id'=>6,'name'=>'Musik','file_count'=>7],
];

// Eingaben säubern
$deleteId    = filter_input(INPUT_GET,'delete',FILTER_VALIDATE_INT);
$currentPath = filter_input(INPUT_GET,'path',FILTER_SANITIZE_STRING) ?: '';
$searchQuery = filter_input(INPUT_GET,'search',FILTER_SANITIZE_STRING) ?: '';\$filterType  = filter_input(INPUT_GET,'type',FILTER_SANITIZE_STRING) ?: '';

// Datei löschen
if (\$deleteId !== false && \$deleteId !== null) {
    \$stmt = \$pdo->prepare(
        "DELETE FROM documents WHERE id = :id AND user_id = :uid"
    );
    \$stmt->execute([':id'=>\$deleteId, ':uid'=>\$userId]);
    \$qs = \$currentPath!=='"'? '?path=' . urlencode(\$currentPath) : '';
    header('Location: /file-explorer.php'.\$qs);
    exit;
}

// Kategorien
$fileTypes = [
    'documents'=>['pdf','doc','docx','txt','rtf'],
    'images'=>['jpg','jpeg','png','gif','bmp','svg','webp'],
    'videos'=>['mp4','avi','mov','wmv','flv','webm','mkv'],
    'audio'=>['mp3','wav','flac','aac','ogg','wma'],
    'archives'=>['zip','rar','7z','tar','gz'],
    'code'=>['js','php','css','html','py','java','cpp','c'],
];

// WHERE-Klauseln
$where = ['d.user_id = :uid','d.is_deleted = 0'];
$params=[':uid'=>\$userId];

if (\$searchQuery!=='') {
    \$where[]='(d.title LIKE :term OR d.filename LIKE :term OR d.original_name LIKE :term)';
    \$params[':term']='%'.\$searchQuery.'%';
}
if (\$filterType!=='' && isset(\$fileTypes[\$filterType])) {
    \$exts=\$fileTypes[\$filterType];
    \$ph=implode(',',array_map(fn(\$i)=>":ext\$i",array_keys(\$exts)));
    \$where[]="LOWER(SUBSTRING_INDEX(d.filename,'.',-1)) IN (\$ph)";
    foreach(\$exts as \$i=>\$ext){\$params[":ext\$i"]=strtolower(\$ext);}   
}

$whereSql=implode(' AND ',\$where);

// Dateien abrufen
$sql = <<<SQL
SELECT d.*, dc.name AS category_name
FROM documents d
LEFT JOIN document_categories dc ON d.category_id = dc.id
WHERE \$whereSql
ORDER BY d.upload_date DESC
SQL;

\$stmt = \$pdo->prepare(\$sql);
\$stmt->execute(\$params);
\$files = \$stmt->fetchAll(PDO::FETCH_ASSOC);

// Hilfsfunktionen
function formatFileSize(int \$b): string {
    return match(true) {
        \$b>=1<<30 => number_format(\$b/(1<<30),2).' GB',
        \$b>=1<<20 => number_format(\$b/(1<<20),2).' MB',
        \$b>=1<<10 => number_format(\$b/(1<<10),2).' KB',
        default => \$b.' B',
    };
}
function getFileIcon(string \$name): array {
    \$ext=strtolower(pathinfo(\$name,PATHINFO_EXTENSION));
    static \$map=[
        'pdf'=>['d'=>'M9 12h6m-6 4h6m2 5H7a2...','col'=>'text-red-400'],
        'jpg'=>['d'=>'M4 16l4.586-4.586a2...','col'=>'text-green-400'],
        'mp3'=>['d'=>'M9 19V6l12-3v13M9...','col'=>'text-purple-400'],
        'mp4'=>['d'=>'M15 10l4.553-2.276A1...','col'=>'text-pink-400'],
        'zip'=>['d'=>'M20 13V6a2 2 0 ...','col'=>'text-yellow-400'],
    ];
    return \$map[\$ext]??['d'=>'M9 12h6m-6...','col'=>'text-gray-400'];
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>File Explorer</title>
    <style>
        /* Schwarz-Pinker Gradient */
        body { margin:0; min-height:100vh;
          background: linear-gradient(135deg,#0f0004 0%,#ff007f 100%);
          font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif; }
        .glass-container { max-width:1200px; margin:2rem auto; padding:1.5rem;
          background:rgba(255,255,255,0.1); backdrop-filter:blur(30px);
          border:1px solid rgba(255,255,255,0.2); border-radius:24px;
          box-shadow:0 8px 32px rgba(0,0,0,0.37);
        }
        .controls { display:flex; flex-wrap:wrap; gap:0.5rem; margin-bottom:1rem; }
        .input, .button, select { background:rgba(255,255,255,0.2); border:none;
          color:#fff; padding:0.75rem 1rem; border-radius:12px;
          backdrop-filter:blur(10px);
        }
        .input::placeholder { color:rgba(255,255,255,0.7); }
        .button:hover, select:hover { background:rgba(255,255,255,0.3); cursor:pointer; }
        .files-grid { display:flex; flex-wrap:wrap; margin:-0.75rem; }
        .file-card { background:rgba(255,255,255,0.15);
          backdrop-filter:blur(10px); border-radius:16px;
          padding:1rem; margin:0.75rem; flex:0 1 calc(25% - 1.5rem);
          display:flex; flex-direction:column; align-items:center;
          text-align:center; transition:transform 0.2s,box-shadow 0.2s;
        }
        .file-card:hover { transform:scale(1.05); box-shadow:0 12px 24px rgba(0,0,0,0.5); }
        .file-card svg { width:48px; height:48px; fill:white; opacity:0.8; margin-bottom:0.5rem; }
        .filename { color:#fff; font-weight:500; font-size:0.9rem;
          white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
        }
        ::-webkit-scrollbar { width:8px; }
        ::-webkit-scrollbar-track { background:rgba(255,255,255,0.1); border-radius:4px; }
        ::-webkit-scrollbar-thumb { background:rgba(255,255,255,0.3); border-radius:4px; }
    </style>
</head>
<body>
  <div class="glass-container">
    <form method="get" class="controls">
      <input type="text" name="search" class="input" placeholder="Suchen..." value="<?=htmlspecialchars(\$searchQuery)?>">
      <select name="type" class="input">
        <option value="">Alle Typen</option>
        <?php foreach(array_keys(\$fileTypes) as \$type): ?>
          <option value="<?=$type?>" <?=\$filterType===\$type?'selected':''?>><?=$type?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="button">Filter anwenden</button>
    </form>
    <div class="files-grid">
      <?php foreach(\$folders as \$folder): ?>
        <div class="file-card">
          <!-- Ordner-Icon -->
          <svg viewBox="0 0 24 24"><path fill="white" d="M3 7a2 2 0 012-2h5l2 2h7a2 2 0 012 2v2H3z"/></svg>
          <div class="filename"><?=htmlspecialchars(\$folder['name'])?> (<?=\$folder['file_count']?>)</div>
        </div>
      <?php endforeach; ?>
      <?php foreach(\$files as \$file): 
        \$icon=getFileIcon(\$file['filename']); ?>
        <div class="file-card">
          <svg viewBox="0 0 24 24"><path fill="white" d="<?=htmlspecialchars(\$icon['d'])?>"/></svg>
          <div class="filename"><?=htmlspecialchars(\$file['filename'])?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>
