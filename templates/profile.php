<!-- templates/profile.php (sidebar categories per application) -->
<?php
require_once __DIR__ . '/../src/lib/auth.php';
$user = getUser();
$activeTab = $_GET['tab'] ?? 'personal_info';
?>
<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Profil | Private Vault</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: 'Inter', sans-serif; }
    @media (max-width: 768px) {
      main { margin-top: 4rem; }
    }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#eef7ff] via-[#f7fbff] to-[#f9fdf2] flex flex-col">
  <?php require_once 'navbar.php'; ?>
  
  <main class="ml-0 mt-16 md:ml-64 md:mt-8 flex-1 p-6 md:p-8">
    <!-- Tab Navigation -->
    <div class="max-w-7xl mx-auto mb-8">
      <nav class="flex space-x-4 overflow-x-auto pb-4">
        <?php $tabs=['personal_info'=>'Personal info','finance'=>'Ein & Ausgaben','documents'=>'Dokumente'];
          foreach($tabs as $k=>$lbl):?>
            <a href="?tab=<?=$k?>" 
               class="px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap
                      <?=$k===$activeTab 
                          ? 'bg-white shadow text-gray-900' 
                          : 'text-gray-500 hover:text-gray-900' ?>">
              <?=ucfirst(str_replace('_', ' ', $k))?>
            </a>
        <?php endforeach; ?>
      </nav>
    </div>

    <!-- Content Area with more spacing -->
    <div class="max-w-7xl mx-auto">
      <div class="bg-white/60 backdrop-blur-sm rounded-2xl shadow-sm p-6 md:p-8 space-y-8">
        <?php
          // Determine sidebar categories 
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
        
          $sub=$_GET['subtab']??array_key_first($sidebarCats);
          $filePath=__DIR__."/profile_tabs/{$activeTab}/{$sub}.php";
          if(!file_exists($filePath)){
            echo '<p class="text-gray-500">Kein Inhalt verfügbar.</p>';
          }else{
            include $filePath;
          }
        ?>
      </div>
    </div>
  </main>
</body>
</html>
