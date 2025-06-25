<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced Notizen | Private Vault</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Rich Text Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    
    <!-- D3.js for graph visualization -->
    <script src="https://d3js.org/d3.v7.min.js"></script>
    
    <style>
        body { 
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
            min-height: 100vh;
        }
        
        .glass-morphism {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        
        .note-card.enhanced {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            padding: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .note-card.enhanced:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-4px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        }
        
        .note-card.enhanced::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--note-color, #fbbf24);
            border-radius: 1rem 1rem 0 0;
        }
        
        .note-card-header {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        
        .note-meta-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        
        .note-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: white;
            cursor: pointer;
            line-height: 1.4;
            transition: color 0.2s ease;
        }
        
        .note-title:hover {
            color: #60a5fa;
        }
        
        .note-content {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.875rem;
            line-height: 1.6;
            margin-bottom: 1rem;
            cursor: pointer;
        }
        
        .note-footer {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 0.75rem;
        }
        
        .note-meta-bottom {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .category-badge, .word-count-badge, .reading-time-badge, .links-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-weight: 500;
        }
        
        .category-badge {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .word-count-badge {
            background: rgba(34, 197, 94, 0.2);
            color: #86efac;
        }
        
        .reading-time-badge {
            background: rgba(59, 130, 246, 0.2);
            color: #93c5fd;
        }
        
        .links-badge {
            background: rgba(168, 85, 247, 0.2);
            color: #c4b5fd;
        }
        
        .note-date {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .note-tags {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .tag {
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.8);
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .tag:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
        
        .tag-more {
            background: rgba(156, 163, 175, 0.2);
            color: #d1d5db;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
        }
        
        .note-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        
        .note-card.enhanced:hover .note-actions {
            opacity: 1;
        }
        
        .action-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: rgba(255, 255, 255, 0.8);
            width: 2rem;
            height: 2rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .action-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
        
        .favorite-icon.active {
            color: #f59e0b;
        }
        
        .pin-icon.active {
            color: #10b981;
        }
        
        .dropdown {
            position: relative;
        }
        
        .dropdown-content {
            position: absolute;
            top: 100%;
            right: 0;
            background: rgba(31, 41, 55, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 0.5rem;
            padding: 0.5rem 0;
            min-width: 12rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
            display: none;
            z-index: 1000;
        }
        
        .dropdown.open .dropdown-content {
            display: block;
        }
        
        .dropdown-content a {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        
        .dropdown-content a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .dropdown-content a.delete-action {
            color: #f87171;
        }
        
        .dropdown-content a.delete-action:hover {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
        }
        
        .advanced-search-panel {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-top: 1rem;
            display: none;
        }
        
        .advanced-search-panel.active {
            display: block;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .filter-group label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .filter-input {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            color: white;
            font-size: 0.875rem;
        }
        
        .filter-input:focus {
            outline: none;
            border-color: #60a5fa;
            box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.1);
        }
        
        .filter-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        
        .stats-card {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 0.75rem;
            padding: 1rem;
            text-align: center;
        }
        
        .stats-number {
            font-size: 1.875rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.25rem;
        }
        
        .stats-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.875rem;
        }
        
        .search-highlight {
            background: rgba(251, 191, 36, 0.3);
            padding: 0.125rem 0.25rem;
            border-radius: 0.25rem;
        }
        
        .auto-save-indicator {
            position: fixed;
            top: 1rem;
            right: 1rem;
            background: rgba(34, 197, 94, 0.9);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 2000;
        }
        
        .keyboard-shortcut {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 0.25rem;
            padding: 0.125rem 0.375rem;
            font-size: 0.75rem;
            font-family: monospace;
        }
        
        /* Enhanced modal styles */
        .modal-enhanced {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .modal-enhanced.active {
            opacity: 1;
            visibility: visible;
        }
        
        .modal-content-enhanced {
            background: rgba(31, 41, 55, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            width: 90vw;
            max-width: 800px;
            max-height: 90vh;
            overflow: hidden;
            transform: scale(0.95);
            transition: transform 0.3s ease;
        }
        
        .modal-enhanced.active .modal-content-enhanced {
            transform: scale(1);
        }
        
        /* Responsive improvements */
        @media (max-width: 768px) {
            .note-card.enhanced {
                padding: 1rem;
            }
            
            .note-meta-bottom {
                flex-direction: column;
                gap: 0.25rem;
            }
            
            .note-tags {
                margin-top: 0.5rem;
            }
            
            .advanced-search-panel {
                padding: 1rem;
            }
        }
    </style>
</head>

<body class="min-h-screen">
    <?php require_once __DIR__ . '/navbar.php'; ?>

    <main class="ml-0 mt-16 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
        <div class="max-w-7xl mx-auto space-y-6">
            
            <!-- Header with Statistics -->
            <div class="glass-morphism p-6">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">Enhanced Zettelkasten</h1>
                        <p class="text-white/70">Erweiterte Notizen-Verwaltung mit intelligenten Features</p>
                    </div>
                    
                    <!-- Quick Statistics -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="stats-card">
                            <div class="stats-number" id="totalNotesCount">0</div>
                            <div class="stats-label">Notizen</div>
                        </div>
                        <div class="stats-card">
                            <div class="stats-number" id="totalWordsCount">0</div>
                            <div class="stats-label">Wörter</div>
                        </div>
                        <div class="stats-card">
                            <div class="stats-number" id="totalConnectionsCount">0</div>
                            <div class="stats-label">Verknüpfungen</div>
                        </div>
                        <div class="stats-card">
                            <div class="stats-number" id="categoriesUsedCount">0</div>
                            <div class="stats-label">Kategorien</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Search and Controls -->
            <div class="glass-morphism p-6">
                <div class="flex flex-col lg:flex-row gap-4">
                    <!-- Main Search -->
                    <div class="flex-1 relative">
                        <input type="text" 
                               id="notesSearch" 
                               placeholder="Notizen durchsuchen... (Strg+K)"
                               class="w-full pl-12 pr-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:bg-white/15 focus:border-white/30 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                        <i class="fas fa-search absolute left-4 top-4 text-white/50"></i>
                        <div class="absolute right-4 top-3">
                            <span class="keyboard-shortcut">Strg+K</span>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex gap-3">
                        <button id="advancedSearchToggle" class="action-btn px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                            <i class="fas fa-sliders-h mr-2"></i>Erweitert
                        </button>
                        <button onclick="zettelkasten.createNewNote()" class="action-btn px-4 py-3 bg-green-600/20 border border-green-400/30 rounded-lg text-green-300 hover:bg-green-600/30 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Neue Notiz <span class="keyboard-shortcut ml-2">Strg+N</span>
                        </button>
                    </div>
                </div>
                
                <!-- Advanced Search Panel -->
                <div id="advancedSearchPanel" class="advanced-search-panel">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="filter-group">
                            <label for="categoryFilter">Kategorie</label>
                            <select id="categoryFilter" class="filter-input">
                                <option value="">Alle Kategorien</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="tagFilter">Tags (komma-getrennt)</label>
                            <input type="text" id="tagFilter" class="filter-input" placeholder="tag1, tag2">
                        </div>
                        
                        <div class="filter-group">
                            <label for="dateFromFilter">Von Datum</label>
                            <input type="date" id="dateFromFilter" class="filter-input">
                        </div>
                        
                        <div class="filter-group">
                            <label for="dateToFilter">Bis Datum</label>
                            <input type="date" id="dateToFilter" class="filter-input">
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between mt-4">
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 text-white/80">
                                <input type="checkbox" id="favoriteFilter" class="rounded">
                                Nur Favoriten
                            </label>
                        </div>
                        
                        <button id="clearFilters" class="px-4 py-2 bg-gray-600/20 border border-gray-400/30 rounded-lg text-gray-300 hover:bg-gray-600/30 transition-colors">
                            <i class="fas fa-times mr-2"></i>Filter zurücksetzen
                        </button>
                    </div>
                </div>
            </div>

            <!-- View Controls -->
            <div class="glass-morphism p-4">
                <div class="flex items-center justify-between">
                    <div class="flex gap-2">
                        <button class="view-toggle-btn active px-4 py-2 bg-white/20 text-white rounded-lg transition-colors" data-view="grid">
                            <i class="fas fa-th mr-2"></i>Grid
                        </button>
                        <button class="view-toggle-btn px-4 py-2 bg-white/10 text-white/70 rounded-lg hover:bg-white/20 transition-colors" data-view="list">
                            <i class="fas fa-list mr-2"></i>Liste
                        </button>
                        <button class="view-toggle-btn px-4 py-2 bg-white/10 text-white/70 rounded-lg hover:bg-white/20 transition-colors" data-view="node">
                            <i class="fas fa-project-diagram mr-2"></i>Graph
                        </button>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <div class="text-white/60 text-sm">
                            <span id="notesCount">0</span> Notizen gefunden
                        </div>
                        
                        <select id="sortOptions" class="bg-white/10 border border-white/20 rounded-lg px-3 py-2 text-white text-sm">
                            <option value="updated-desc">Zuletzt aktualisiert</option>
                            <option value="created-desc">Zuletzt erstellt</option>
                            <option value="title-asc">Titel A-Z</option>
                            <option value="title-desc">Titel Z-A</option>
                            <option value="words-desc">Wörter (absteigend)</option>
                            <option value="words-asc">Wörter (aufsteigend)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Notes Display Area -->
            <div class="notes-container">
                <!-- Grid View -->
                <div id="notesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <!-- Notes will be loaded here -->
                </div>

                <!-- List View -->
                <div id="listView" style="display: none;">
                    <div class="glass-morphism divide-y divide-white/10">
                        <!-- List items will be loaded here -->
                    </div>
                </div>

                <!-- Graph View -->
                <div id="nodeView" style="display: none;">
                    <div class="glass-morphism h-96 relative">
                        <div id="nodeCanvas" class="w-full h-full">
                            <!-- Graph visualization will be rendered here -->
                        </div>
                        <div class="absolute top-4 right-4 flex gap-2">
                            <button onclick="zettelkasten.centerGraph()" class="action-btn bg-white/10 border border-white/20 rounded p-2 text-white hover:bg-white/20">
                                <i class="fas fa-crosshairs"></i>
                            </button>
                            <button onclick="zettelkasten.resetGraph()" class="action-btn bg-white/10 border border-white/20 rounded p-2 text-white hover:bg-white/20">
                                <i class="fas fa-redo"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Enhanced Note Editor Modal -->
    <div id="noteEditorModal" class="modal-enhanced">
        <div class="modal-content-enhanced">
            <div class="flex flex-col h-full">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-white/20">
                    <h2 class="text-xl font-semibold text-white">Notiz bearbeiten</h2>
                    <div class="flex items-center gap-3">
                        <div id="autoSaveIndicator" class="text-green-400 text-sm opacity-0">
                            <i class="fas fa-check-circle mr-1"></i>Automatisch gespeichert
                        </div>
                        <button onclick="zettelkasten.closeNoteEditor()" class="action-btn text-white/60 hover:text-white">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Modal Body -->
                <div class="flex-1 p-6 overflow-y-auto">
                    <form id="noteForm" class="space-y-6">
                        <input type="hidden" id="noteId" name="id">
                        
                        <!-- Title and Template -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <div class="lg:col-span-2">
                                <label for="noteTitle" class="block text-sm font-medium text-white/80 mb-2">Titel</label>
                                <input type="text" 
                                       id="noteTitle" 
                                       name="title" 
                                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:bg-white/15 focus:border-white/30 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                                       placeholder="Notiz Titel..." 
                                       required>
                            </div>
                            
                            <div>
                                <label for="templateSelect" class="block text-sm font-medium text-white/80 mb-2">Template</label>
                                <select id="templateSelect" name="template_id" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:bg-white/15 focus:border-white/30 focus:outline-none">
                                    <option value="">Kein Template</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Category and Color -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label for="noteCategory" class="block text-sm font-medium text-white/80 mb-2">Kategorie</label>
                                <select id="noteCategory" name="category_id" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:bg-white/15 focus:border-white/30 focus:outline-none">
                                    <option value="">Keine Kategorie</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="noteColor" class="block text-sm font-medium text-white/80 mb-2">Farbe</label>
                                <div class="flex items-center gap-3">
                                    <input type="color" 
                                           id="noteColor" 
                                           name="color" 
                                           value="#fbbf24" 
                                           class="w-12 h-12 rounded-lg border border-white/20 bg-white/10">
                                    <div class="flex gap-2">
                                        <button type="button" onclick="zettelkasten.setNoteColor('#fbbf24')" class="w-8 h-8 rounded-full border-2 border-white/20" style="background: #fbbf24;"></button>
                                        <button type="button" onclick="zettelkasten.setNoteColor('#3b82f6')" class="w-8 h-8 rounded-full border-2 border-white/20" style="background: #3b82f6;"></button>
                                        <button type="button" onclick="zettelkasten.setNoteColor('#10b981')" class="w-8 h-8 rounded-full border-2 border-white/20" style="background: #10b981;"></button>
                                        <button type="button" onclick="zettelkasten.setNoteColor('#f59e0b')" class="w-8 h-8 rounded-full border-2 border-white/20" style="background: #f59e0b;"></button>
                                        <button type="button" onclick="zettelkasten.setNoteColor('#8b5cf6')" class="w-8 h-8 rounded-full border-2 border-white/20" style="background: #8b5cf6;"></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tags -->
                        <div>
                            <label for="noteTags" class="block text-sm font-medium text-white/80 mb-2">Tags (komma-getrennt)</label>
                            <input type="text" 
                                   id="noteTags" 
                                   name="tags" 
                                   class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:bg-white/15 focus:border-white/30 focus:outline-none"
                                   placeholder="tag1, tag2, tag3">
                        </div>
                        
                        <!-- Content Editor -->
                        <div>
                            <label for="noteContent" class="block text-sm font-medium text-white/80 mb-2">Inhalt</label>
                            <div id="noteContentEditor" style="height: 300px; background: rgba(255,255,255,0.05); border-radius: 0.5rem;"></div>
                            <textarea id="noteContent" name="content" class="hidden"></textarea>
                        </div>
                        
                        <!-- Options -->
                        <div class="flex items-center gap-6">
                            <label class="flex items-center gap-2 text-white/80">
                                <input type="checkbox" id="notePinned" name="is_pinned" class="rounded">
                                Anheften
                            </label>
                            <label class="flex items-center gap-2 text-white/80">
                                <input type="checkbox" id="noteFavorite" name="is_favorite" class="rounded">
                                Favorit
                            </label>
                        </div>
                    </form>
                </div>
                
                <!-- Modal Footer -->
                <div class="flex items-center justify-between p-6 border-t border-white/20">
                    <div class="text-sm text-white/60">
                        Speichern: <span class="keyboard-shortcut">Strg+S</span>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" onclick="zettelkasten.closeNoteEditor()" class="px-4 py-2 bg-gray-600/20 border border-gray-400/30 rounded-lg text-gray-300 hover:bg-gray-600/30 transition-colors">
                            Abbrechen
                        </button>
                        <button type="button" onclick="zettelkasten.saveCurrentNote()" class="px-6 py-2 bg-blue-600/20 border border-blue-400/30 rounded-lg text-blue-300 hover:bg-blue-600/30 transition-colors">
                            <i class="fas fa-save mr-2"></i>Speichern
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto-save Indicator -->
    <div id="autoSaveIndicator" class="auto-save-indicator">
        <i class="fas fa-check-circle mr-2"></i>Automatisch gespeichert
    </div>

    <!-- Scripts -->
    <script src="/public/js/enhanced-zettelkasten-v2.js"></script>
    
    <script>
        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Enhanced Zettelkasten initializing...');
        });
        
        // Global functions for backward compatibility
        function editNote(noteId) {
            if (window.zettelkasten) {
                window.zettelkasten.editNote(noteId);
            }
        }
        
        function deleteNote(noteId) {
            if (window.zettelkasten) {
                window.zettelkasten.deleteNote(noteId);
            }
        }
        
        function togglePin(noteId) {
            if (window.zettelkasten) {
                window.zettelkasten.toggleNotePin(noteId);
            }
        }
        
        function toggleFavorite(noteId) {
            if (window.zettelkasten) {
                window.zettelkasten.toggleNoteFavorite(noteId);
            }
        }
    </script>
</body>
</html>
