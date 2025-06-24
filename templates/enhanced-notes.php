<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Second Brain - Notizen | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <style>
    /* Enhanced styling for Second Brain features */
    body { 
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
      min-height: 100vh;
    }
    
    @media (max-width: 768px) {
      main { margin-top: 4rem; }
    }
    
    .glass-card {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    
    /* Enhanced Note Cards */
    .note-card {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: all 0.3s ease;
      cursor: pointer;
      border-radius: 0.75rem;
      overflow: hidden;
      position: relative;
    }
    
    .note-card:hover {
      background: rgba(255, 255, 255, 0.15);
      transform: translateY(-4px);
      box-shadow: 0 16px 48px rgba(0, 0, 0, 0.4);
      border-color: rgba(255, 255, 255, 0.3);
    }
    
    .note-card.pinned {
      border-color: #f59e0b;
      box-shadow: 0 4px 16px rgba(245, 158, 11, 0.3);
    }
    
    .note-card-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      padding: 1rem 1rem 0.5rem;
    }
    
    .note-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: white;
      margin: 0;
      line-height: 1.4;
      flex: 1;
      margin-right: 0.5rem;
    }
    
    .note-actions {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      opacity: 0;
      transition: opacity 0.2s ease;
    }
    
    .note-card:hover .note-actions {
      opacity: 1;
    }
    
    .note-action-btn {
      background: rgba(255, 255, 255, 0.1);
      border: none;
      border-radius: 0.25rem;
      padding: 0.25rem;
      color: rgba(255, 255, 255, 0.7);
      cursor: pointer;
      transition: all 0.2s ease;
    }
    
    .note-action-btn:hover {
      background: rgba(255, 255, 255, 0.2);
      color: white;
    }
    
    .note-content {
      padding: 0 1rem;
      color: rgba(255, 255, 255, 0.8);
      font-size: 0.9rem;
      line-height: 1.5;
    }
    
    .note-footer {
      padding: 0.5rem 1rem 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .note-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 0.25rem;
    }
    
    .note-tag {
      background: rgba(59, 130, 246, 0.3);
      color: #93c5fd;
      font-size: 0.75rem;
      padding: 0.125rem 0.5rem;
      border-radius: 9999px;
      border: 1px solid rgba(59, 130, 246, 0.5);
    }
    
    /* Graph View Styles */
    .node-view-container {
      position: relative;
      width: 100%;
      height: 500px;
      overflow: hidden;
      border-radius: 1rem;
      background: rgba(0, 0, 0, 0.3);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .node-canvas {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      cursor: grab;
    }
    
    .node-canvas:active {
      cursor: grabbing;
    }
    
    /* Enhanced Search and Filter Styles */
    .search-bar {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      color: white;
      transition: all 0.3s ease;
    }
    
    .search-bar:focus {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(59, 130, 246, 0.5);
      box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }
    
    .filter-btn {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: rgba(255, 255, 255, 0.8);
      padding: 0.5rem 1rem;
      border-radius: 0.5rem;
      transition: all 0.2s ease;
      cursor: pointer;
    }
    
    .filter-btn:hover {
      background: rgba(255, 255, 255, 0.2);
      color: white;
    }
    
    .filter-btn.active {
      background: rgba(59, 130, 246, 0.3);
      border-color: rgba(59, 130, 246, 0.5);
      color: #93c5fd;
    }
    
    .view-toggle-buttons {
      display: flex;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 0.5rem;
      padding: 0.25rem;
    }
    
    .view-toggle-btn {
      background: transparent;
      border: none;
      color: rgba(255, 255, 255, 0.7);
      padding: 0.5rem 0.75rem;
      border-radius: 0.25rem;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    
    .view-toggle-btn:hover {
      background: rgba(255, 255, 255, 0.1);
      color: white;
    }
    
    .view-toggle-btn.active {
      background: rgba(59, 130, 246, 0.3);
      color: #93c5fd;
    }
    
    /* Note Editor Styles */
    .note-editor-modal {
      display: flex;
      align-items: flex-start;
      justify-content: center;
      padding: 2rem;
      z-index: 50;
    }
    
    .note-editor-modal.active {
      display: flex;
    }
    
    .note-editor-content {
      width: 100%;
      max-width: 4xl;
      margin-top: 2rem;
      background: #1f2937;
      border-radius: 0.75rem;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .note-editor-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 1rem;
      border-bottom: 1px solid #374151;
    }
    
    .note-editor-body {
      padding: 1.5rem;
      max-height: calc(90vh - 8rem);
      overflow-y: auto;
    }
    
    .notes-btn-primary {
      background: linear-gradient(135deg, #3b82f6, #1d4ed8);
      color: white;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 0.5rem;
      cursor: pointer;
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .notes-btn-primary:hover {
      background: linear-gradient(135deg, #2563eb, #1e40af);
      transform: translateY(-1px);
    }
    
    .notes-btn-secondary {
      background: rgba(255, 255, 255, 0.1);
      color: rgba(255, 255, 255, 0.9);
      border: 1px solid rgba(255, 255, 255, 0.2);
      padding: 0.5rem 1rem;
      border-radius: 0.5rem;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    
    .notes-btn-secondary:hover {
      background: rgba(255, 255, 255, 0.2);
      color: white;
    }
    
    /* Statistics Cards */
    .stat-card {
      transition: all 0.3s ease;
    }
    
    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    }
    
    /* Quick Actions */
    .quick-action-btn {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .quick-action-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    }
    
    /* Tag Cloud */
    .tag-cloud-item {
      cursor: pointer;
      transition: all 0.2s ease;
    }
    
    .tag-cloud-item:hover {
      transform: scale(1.05);
    }
    
    /* Utility Classes */
    .line-clamp-2 {
      overflow: hidden;
      display: -webkit-box;
      -webkit-box-orient: vertical;
      -webkit-line-clamp: 2;
    }
    
    .line-clamp-3 {
      overflow: hidden;
      display: -webkit-box;
      -webkit-box-orient: vertical;
      -webkit-line-clamp: 3;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
      .note-editor-content {
        margin: 0.5rem;
        max-height: calc(100vh - 1rem);
      }
      
      .node-view-container {
        height: 300px;
      }
    }
    
    /* Animation for page transitions */
    .fade-in {
      animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    /* Loading states */
    .loading {
      position: relative;
      pointer-events: none;
      opacity: 0.6;
    }
    
    .loading::after {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 20px;
      height: 20px;
      margin: -10px 0 0 -10px;
      border: 2px solid #ffffff33;
      border-top: 2px solid #ffffff;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
</head>

<?php require_once __DIR__ . '/navbar.php'; ?>

<body class="min-h-screen">
  <main class="ml-0 mt-16 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
    <div class="max-w-7xl mx-auto space-y-6 notes-container fade-in">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-3xl font-bold text-white">Second Brain - Notizen & Zettelkasten</h1>
          <p class="text-white/60 mt-1">Verwalte dein Wissen, verknüpfe Ideen und entdecke Verbindungen</p>
        </div>
        
        <div class="flex flex-wrap gap-3">
          <button id="create-daily-note-btn" class="btn-primary px-4 py-2 text-white rounded-lg hover:opacity-90 transition-opacity">
            <i class="fas fa-calendar-day mr-2"></i>Daily Note
          </button>
          <button id="create-note-btn" class="btn-primary px-4 py-2 text-white rounded-lg hover:opacity-90 transition-opacity">
            <i class="fas fa-plus mr-2"></i>Neue Notiz
          </button>
          <button id="show-graph-btn" class="bg-purple-600/20 border border-purple-400/30 px-4 py-2 text-purple-300 rounded-lg hover:bg-purple-600/30 transition-colors">
            <i class="fas fa-project-diagram mr-2"></i>Graph View
          </button>
          <button id="export-backup-btn" class="bg-gray-600/20 border border-gray-400/30 px-4 py-2 text-gray-300 rounded-lg hover:bg-gray-600/30 transition-colors">
            <i class="fas fa-download mr-2"></i>Export
          </button>
        </div>
      </div>

      <!-- Statistics, Quick Actions, Search and Filters will be inserted here by JavaScript -->
      
      <!-- Loading Indicator -->
      <div id="loading-indicator" class="text-center py-8 hidden">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-white"></div>
        <p class="text-white/60 mt-2">Loading your Second Brain...</p>
      </div>
      
      <!-- Notes Grid will be inserted here by JavaScript -->
      
      <!-- Graph Container will be inserted here by JavaScript -->
    </div>
  </main>

  <!-- Include JavaScript modules -->
  <script src="/js/second-brain.js"></script>
  <script src="/js/enhanced-notes-manager.js"></script>
  <script src="/js/second-brain-dashboard.js"></script>
  
  <script>
    // Initialize global variables for cross-component communication
    let brain = null;
    let linkCreator = null;
    let notesManager = null;
    let secondBrainDashboard = null;
    
    // Initialize the application
    document.addEventListener('DOMContentLoaded', () => {
      // Show loading indicator
      document.getElementById('loading-indicator').classList.remove('hidden');
      
      // Initialize components with proper order
      setTimeout(() => {
        try {
          // Initialize in the correct order
          if (window.EnhancedNotesManager) {
            notesManager = new EnhancedNotesManager();
          }
          
          if (window.SecondBrainDashboard) {
            secondBrainDashboard = new SecondBrainDashboard();
          }
          
          // Set up header button event listeners
          setupHeaderButtons();
          
          // Hide loading indicator
          document.getElementById('loading-indicator').classList.add('hidden');
          
        } catch (error) {
          console.error('Error initializing Second Brain:', error);
          document.getElementById('loading-indicator').innerHTML = 
            '<p class="text-red-400">Error loading Second Brain. Please refresh the page.</p>';
        }
      }, 100);
    });
    
    function setupHeaderButtons() {
      // Header button event listeners
      document.getElementById('create-daily-note-btn')?.addEventListener('click', () => {
        secondBrainDashboard?.createDailyNote();
      });
      
      document.getElementById('create-note-btn')?.addEventListener('click', () => {
        notesManager?.openNoteEditor();
      });
      
      document.getElementById('show-graph-btn')?.addEventListener('click', () => {
        notesManager?.setView('graph');
      });
      
      document.getElementById('export-backup-btn')?.addEventListener('click', () => {
        secondBrainDashboard?.exportBackup();
      });
    }
    
    // Legacy function compatibility for existing code
    function createDailyNote() {
      secondBrainDashboard?.createDailyNote();
    }
    
    function openNoteModal() {
      notesManager?.openNoteEditor();
    }
    
    function showGraphView() {
      notesManager?.setView('graph');
    }
    
    function filterNotes(query) {
      notesManager?.searchNotes(query);
    }
    
    function filterByCategory(category) {
      console.log('Filtering by category:', category);
      // Implementation for category filtering if needed
    }
    
    // Enhanced keyboard shortcuts
    document.addEventListener('keydown', (e) => {
      // Prevent shortcuts when typing in inputs
      if (e.target.matches('input, textarea, select')) return;
      
      // Ctrl/Cmd + N: New note
      if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
        e.preventDefault();
        notesManager?.openNoteEditor();
      }
      
      // Ctrl/Cmd + K: Search
      if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        document.getElementById('notes-search-input')?.focus();
      }
      
      // Ctrl/Cmd + Shift + G: Toggle graph view
      if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'G') {
        e.preventDefault();
        notesManager?.setView('graph');
      }
      
      // Ctrl/Cmd + Shift + D: Create daily note
      if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'D') {
        e.preventDefault();
        secondBrainDashboard?.createDailyNote();
      }
      
      // Ctrl/Cmd + Shift + R: Random note
      if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'R') {
        e.preventDefault();
        secondBrainDashboard?.openRandomNote();
      }
      
      // Ctrl/Cmd + Shift + E: Export backup
      if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'E') {
        e.preventDefault();
        secondBrainDashboard?.exportBackup();
      }
    });
    
    // Add helpful hints for users
    console.log(`
🧠 Second Brain Keyboard Shortcuts:
• Ctrl/Cmd + N: New note
• Ctrl/Cmd + K: Search
• Ctrl/Cmd + Shift + G: Graph view
• Ctrl/Cmd + Shift + D: Daily note
• Ctrl/Cmd + Shift + R: Random note
• Ctrl/Cmd + Shift + E: Export backup
• Ctrl/Cmd + L: Create link (in editor)
• Ctrl/Cmd + S: Save note (in editor)
• Escape: Close editor
    `);
  </script>
</body>
</html>
