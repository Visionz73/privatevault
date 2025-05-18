<!-- templates/profile.php (sidebar categories per application) -->
<?php
require_once __DIR__ . '/../src/lib/auth.php';
$user = getUser();
$activeTab = $_GET['tab'] ?? 'personal_info';
?>
<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Profil | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: 'Inter', sans-serif; }
    @media (max-width: 768px) {
      main { margin-top: 4rem; }
    }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#eef7ff] via-[#f7fbff] to-[#f9fdf2] flex">
<?php require_once __DIR__.'/navbar.php'; ?>

<!-- Main Content -->
<div class="ml-0 md:ml-64 flex-1 p-4 md:p-8 mt-14 md:mt-0">
  <!-- Header with user info -->
  <header class="bg-white/60 backdrop-blur-sm shadow-sm rounded-2xl mb-8">
    <div class="max-w-5xl mx-auto flex items-center gap-6 px-8 py-8">
      <div class="h-24 w-24 rounded-full bg-[#4A90E2]/10 flex items-center justify-center text-3xl font-bold text-[#4A90E2]">
        <?= $initials ?>
      </div>
      <div class="flex-1">
        <div class="flex flex-wrap items-center gap-3">
          <h1 class="text-3xl font-bold text-gray-900 break-all"><?= htmlspecialchars($user['username']) ?></h1>
          <span class="px-3 py-1 rounded-full bg-[#4A90E2]/10 text-[#4A90E2] text-sm font-medium"><?= ucfirst($user['role']) ?></span>
        </div>
        <p class="mt-3 text-gray-600 flex flex-wrap gap-x-3 gap-y-2 text-sm">
          <span><?= htmlspecialchars($user['job_title'] ?? '—') ?></span><span class="hidden sm:inline">•</span>
          <span><?= htmlspecialchars($user['department'] ?? '—') ?></span><span class="hidden sm:inline">•</span>
          <span><?= htmlspecialchars($user['location'] ?? '—') ?></span>
        </p>
        <p class="mt-2 text-xs text-gray-500">Seit <?= date('d.m.Y',strtotime($user['created_at'])) ?> | Meldet an <span class="underline">Head&nbsp;of&nbsp;Finance</span></p>
      </div>
    </div>
  </header>

  <!-- Tab Navigation -->
  <nav class="bg-white/60 backdrop-blur-sm shadow-sm rounded-2xl mb-8 overflow-hidden">
    <div class="px-4 md:px-6">
      <ul class="flex space-x-8 text-gray-500 overflow-x-auto">
        <?php $tabs=['personal_info'=>'Personal info','finance'=>'Ein & Ausgaben','documents'=>'Dokumente'];
          foreach($tabs as $k=>$lbl):
            $cls = $k === $activeTab ? 'border-[#4A90E2] text-gray-900 font-medium' : 'hover:text-gray-900'; ?>
            <li><a href="?tab=<?=$k?>" class="py-4 block border-b-2 whitespace-nowrap <?=$cls?>"><?=$lbl?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </nav>

  <!-- Determine sidebar categories -->
  <?php
    switch($activeTab){
      case 'finance':
        $sidebarCats=[
          'finance_overview'=>'Übersicht',
          'income'=>'Einnahmen',
          'expenses'=>'Ausgaben',
          'budgets'=>'Budgets',
          'balance'=>'Kontostand'
        ];
        break;
      case 'documents':
        $sidebarCats=[
          'contracts'=>'Verträge',
          'invoices'=>'Rechnungen',
          'ids'=>'Ausweise/ID',
          'insurance'=>'Versicherungen',
          'other_docs'=>'Sonstige Dokumente'
        ];
        break;
      default: // personal_info
        $sidebarCats=[
          'public_profile'=>'Public profile',
          'hr_information'=>'HR information',
          'personal_data'=>'Personal data'
        ];
    }
  ?>

  <!-- Main Grid Layout -->
  <div class="grid grid-cols-1 md:grid-cols-4 gap-6 md:gap-8">
    <!-- Sidebar Categories -->
    <aside class="bg-white/60 backdrop-blur-sm shadow-sm rounded-2xl p-4 md:p-6 space-y-4 h-fit">
      <input id="sidebarFilter" type="text" 
             placeholder="Filter Kategorien…" 
             class="w-full px-4 py-2 rounded-lg bg-white/80 border border-gray-200 focus:ring-2 focus:ring-[#4A90E2]/50 focus:border-[#4A90E2]">
      <ul id="sidebarList" class="space-y-2 text-gray-600 max-h-[60vh] overflow-y-auto text-sm">
        <?php foreach($sidebarCats as $catKey=>$catLabel):?>
          <li class="filter-item hover:text-gray-900 cursor-pointer">
            <a href="?tab=<?=$activeTab?>&subtab=<?=$catKey?>" 
               class="block px-3 py-2 rounded-lg hover:bg-white/80">
              <?=$catLabel?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </aside>

    <!-- Content Area -->
    <section class="md:col-span-3 bg-white/60 backdrop-blur-sm shadow-sm rounded-2xl p-4 md:p-6">
      <?php
        $sub = $_GET['subtab'] ?? array_key_first($sidebarCats);
        $filePath = __DIR__."/profile_tabs/{$activeTab}/{$sub}.php";
        if(!file_exists($filePath)){
          echo '<p class="text-gray-500">Kein Inhalt verfügbar.</p>';
        } else {
          include $filePath;
        }
      ?>
    </section>
  </div>
</div>

<script>
document.getElementById('sidebarFilter').addEventListener('input',e=>{
  const f=e.target.value.toLowerCase();
  document.querySelectorAll('#sidebarList .filter-item').forEach(i=>{
    i.style.display=i.textContent.toLowerCase().includes(f)?'':'none';
  });
});
</script>
</body>
</html>
