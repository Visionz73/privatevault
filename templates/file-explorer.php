<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Datei-Explorer | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body { 
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
      min-height: 100vh;
      position: relative;
      overflow-x: hidden;
    }

    /* Enhanced animated background */
    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: 
        radial-gradient(circle at 20% 50%, rgba(147, 51, 234, 0.08) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(79, 70, 229, 0.08) 0%, transparent 50%),
        radial-gradient(circle at 40% 80%, rgba(236, 72, 153, 0.08) 0%, transparent 50%);
      animation: gradientShift 20s ease-in-out infinite;
      z-index: -1;
    }

    @keyframes gradientShift {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.8; }
    }

    /* Layout adjustments with consistent spacing */
    .file-explorer-container {
      padding-top: 1rem;
      padding-left: 1rem;
      padding-right: 1rem;
      padding-bottom: 1rem;
    }
    
    @media (min-width: 769px) {
      .file-explorer-container {
        margin-left: 16rem;
        padding-top: 1.5rem;
        padding-left: 1.5rem;
        padding-right: 1.5rem;
        padding-bottom: 1.5rem;
      }
    }

    /* Standardized Liquid Glass Effects */
    .liquid-glass {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(20px) saturate(180%);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1.5rem;
      box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.25),
        inset 0 1px 0 rgba(255, 255, 255, 0.15);
      position: relative;
      overflow: hidden;
    }

    .liquid-glass::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
      z-index: 1;
    }

    .liquid-glass-header {
      background: linear-gradient(135deg, 
        rgba(255, 255, 255, 0.15) 0%, 
        rgba(255, 255, 255, 0.10) 100%);
      backdrop-filter: blur(25px) saturate(200%);
      border-bottom: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1.5rem 1.5rem 0 0;
      padding: 1.5rem;
      margin: 0;
    }

    /* Enhanced Sidebar with consistent spacing */
    .sidebar-glass {
      background: rgba(255, 255, 255, 0.06);
      backdrop-filter: blur(25px) saturate(200%);
      border-right: 1px solid rgba(255, 255, 255, 0.12);
      box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
      width: 20rem;
      padding: 1.5rem;
      gap: 1.5rem;
    }

    /* File Cards with consistent spacing */
    .file-card {
      background: rgba(255, 255, 255, 0.07);
      backdrop-filter: blur(15px) saturate(180%);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1.25rem;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      cursor: pointer;
      transform: translateZ(0);
      padding: 1.5rem;
      margin: 0.5rem;
    }

    .file-card:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(255, 255, 255, 0.25);
      transform: translateY(-4px) scale(1.02);
      box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.3),
        0 0 0 1px rgba(255, 255, 255, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }

    .file-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
      transition: left 0.6s ease;
      z-index: 1;
    }

    .file-card:hover::before {
      left: 100%;
    }

    /* Navigation Items with consistent spacing */
    .nav-item {
      background: rgba(255, 255, 255, 0.04);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 1rem;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      cursor: pointer;
      margin: 0.25rem 0;
      padding: 0.75rem 1rem;
    }
    
    .nav-item:hover {
      background: rgba(255, 255, 255, 0.08);
      border-color: rgba(255, 255, 255, 0.18);
      transform: translateX(4px);
    }

    .nav-item.active {
      background: linear-gradient(135deg, rgba(147, 51, 234, 0.25), rgba(79, 70, 229, 0.25));
      border-color: rgba(147, 51, 234, 0.4);
      transform: translateX(6px);
      box-shadow: 0 4px 20px rgba(147, 51, 234, 0.2);
    }

    /* Action Buttons with consistent styling */
    .action-btn {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 0.75rem;
      color: rgba(255, 255, 255, 0.8);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      padding: 0.5rem 1rem;
      margin: 0.25rem;
    }

    .action-btn:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(255, 255, 255, 0.25);
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }

    .action-btn.success {
      background: rgba(34, 197, 94, 0.15);
      border-color: rgba(34, 197, 94, 0.3);
      color: #86efac;
    }

    .action-btn.success:hover {
      background: rgba(34, 197, 94, 0.25);
      border-color: rgba(34, 197, 94, 0.4);
      color: white;
    }

    .action-btn.danger {
      background: rgba(239, 68, 68, 0.15);
      border-color: rgba(239, 68, 68, 0.3);
      color: #fca5a5;
    }

    .action-btn.danger:hover {
      background: rgba(239, 68, 68, 0.25);
      border-color: rgba(239, 68, 68, 0.4);
      color: white;
    }

    /* Search Bar with consistent spacing */
    .search-bar {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1rem;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      margin: 0 1rem;
    }

    .search-bar:focus-within {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(147, 51, 234, 0.5);
      box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.2);
      transform: translateY(-1px);
    }

    /* Stats Cards with consistent spacing */
    .stats-card {
      background: linear-gradient(135deg, 
        rgba(255, 255, 255, 0.12) 0%, 
        rgba(255, 255, 255, 0.06) 100%);
      backdrop-filter: blur(25px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1rem;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      padding: 1rem;
      margin: 0.5rem 0;
    }

    .stats-card:hover {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.25);
      transform: translateY(-2px);
    }

    /* Breadcrumb with consistent spacing */
    .breadcrumb {
      background: rgba(255, 255, 255, 0.06);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 1rem;
      padding: 0.875rem 1.25rem;
      margin: 0 0.5rem;
      position: relative;
      overflow: hidden;
    }

    /* Grid Layout with consistent spacing */
    .file-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 1.5rem;
      padding: 1.5rem;
    }

    /* List View with consistent spacing */
    .file-list {
      padding: 1.5rem;
    }

    .file-list .liquid-glass {
      margin: 0;
    }

    /* Mobile Responsive with proper spacing */
    @media (max-width: 768px) {
      .file-explorer-container {
        padding: 1rem;
      }
      
      .sidebar-glass {
        width: 100%;
        padding: 1rem;
      }
      
      .liquid-glass-header {
        padding: 1rem;
      }
      
      .file-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
        padding: 1rem;
      }
      
      .file-card {
        padding: 1rem;
        margin: 0.25rem;
      }
      
      .nav-item {
        padding: 0.625rem 0.875rem;
      }
      
      .stats-card {
        padding: 0.75rem;
      }
    }
  </style>
</head>
<body class="h-full overflow-hidden">
  <?php require_once __DIR__.'/../templates/navbar.php'; ?>

  <div class="file-explorer-container">
    <div class="flex h-full">
      <!-- Sidebar -->
      <div id="sidebar" class="sidebar-glass flex-shrink-0 overflow-y-auto custom-scrollbar p-4">
        <input id="searchInput" type="text" placeholder="Suche..." class="search-bar w-full mb-4 p-2 text-white bg-transparent focus:outline-none" />
        <div id="categoryList" class="space-y-2"></div>
        <select id="sortSelect" class="action-btn w-full mt-4 bg-transparent">
          <option value="upload_date_DESC">Neueste</option>
          <option value="upload_date_ASC">Älteste</option>
          <option value="filename_ASC">Name A-Z</option>
          <option value="filename_DESC">Name Z-A</option>
          <option value="file_size_DESC">Grösste</option>
          <option value="file_size_ASC">Kleinste</option>
        </select>
      </div>
      <!-- Main Content -->
      <div class="flex-1 overflow-y-auto p-4">
        <div class="flex justify-between items-center mb-4">
          <div>
            <button id="gridViewBtn" class="action-btn active"><i class="fas fa-th"></i></button>
            <button id="listViewBtn" class="action-btn ml-2"><i class="fas fa-list"></i></button>
          </div>
          <div class="text-white/80 text-sm">Dateien insgesamt: <span id="totalCount">0</span></div>
        </div>
        <div id="fileGrid" class="file-grid"></div>
        <div id="fileList" class="file-list hidden"></div>
      </div>
    </div>
  </div>
  <script src="/public/assets/js/file-explorer.js"></script>
</body>
</html>
