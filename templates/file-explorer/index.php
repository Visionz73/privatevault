<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Datei-Explorer</title>
  <link rel="stylesheet" href="/assets/css/file-explorer.css">
</head>
<body>
  <?php include 'partials/sidebar.php'; ?>
  <main class="fe-main">
    <header class="fe-header">
      <form method="GET">
        <input name="search" placeholder="Suche..." value="<?=htmlspecialchars($search)?>">
        <button type="submit">🔍</button>
      </form>
      <div class="fe-toggle">
        <button data-view="grid" class="<?= $view==='grid'?'active':'' ?>">Grid</button>
        <button data-view="list" class="<?= $view==='list'?'active':'' ?>">Liste</button>
      </div>
    </header>
    <section id="fe-container">
      <?php 
        if ($view==='grid') include 'partials/grid-view.php';
        else               include 'partials/list-view.php';
      ?>
    </section>
  </main>
  <script src="/assets/js/file-explorer.js"></script>
</body>
</html>
