<!DOCTYPE html>
<html lang="de" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced Notes - Intelligente Notizenverwaltung</title>
    
    <!-- Preload critical resources -->
    <link rel="preload" href="/css/enhanced-notes.css" as="style">
    <link rel="preload" href="/js/enhanced-notes-ui.js" as="script">
    
    <!-- Stylesheets -->
    <link href="/css/enhanced-notes.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Meta tags for SEO and sharing -->
    <meta name="description" content="Intelligente Notizenverwaltung mit KI-Features, Smart Search und fortschrittlicher Vernetzung">
    <meta name="keywords" content="notes, knowledge management, AI, smart search, productivity">
    
    <!-- Favicon -->
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    
    <!-- Progressive Web App -->
    <meta name="theme-color" content="#1e3a8a">
    <link rel="manifest" href="/manifest.json">
</head>
<body class="notes-body">
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-content">
            <div class="loading-spinner-large"></div>
            <h3>Notizen werden geladen...</h3>
            <p>Bereite intelligente Features vor</p>
        </div>
    </div>

    <!-- Main Application -->
    <div class="notes-app" id="notesApp">
        <!-- Header Section -->
        <header class="notes-header">
            <div class="header-content">
                <div class="title-section">
                    <h1 class="notes-title">
                        <i class="fas fa-brain"></i>
                        Enhanced Notes
                    </h1>
                    <p class="notes-subtitle">Intelligente Notizenverwaltung mit KI-Power</p>
                </div>
                
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="enhancedNotesUI.showAnalytics()" title="Analytics Dashboard">
                        <i class="fas fa-chart-bar"></i>
                        <span class="btn-text">Analytics</span>
                    </button>
                    
                    <button class="btn btn-secondary" onclick="enhancedNotesUI.showGraphView()" title="Notizen-Netzwerk (Ctrl+G)">
                        <i class="fas fa-project-diagram"></i>
                        <span class="btn-text">Graph</span>
                    </button>
                    
                    <button class="btn btn-primary" onclick="enhancedNotesUI.openQuickCapture()" title="Schnell-Erfassung (Ctrl+K)">
                        <i class="fas fa-bolt"></i>
                        <span class="btn-text">Quick Capture</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Toolbar Section -->
        <div class="notes-toolbar">
            <!-- Search -->
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input 
                    type="text" 
                    id="notesSearch" 
                    class="search-input" 
                    placeholder="Intelligente Suche... (Ctrl+F)"
                    autocomplete="off"
                    spellcheck="false"
                >
                <div class="search-suggestions" id="searchSuggestions"></div>
            </div>

            <!-- Filters -->
            <div class="filter-controls">
                <div class="filter-dropdown">
                    <select id="categoryFilter" class="filter-select">
                        <option value="">Alle Kategorien</option>
                        <option value="personal">Persönlich</option>
                        <option value="work">Arbeit</option>
                        <option value="projects">Projekte</option>
                        <option value="knowledge">Wissen</option>
                    </select>
                </div>

                <div class="filter-dropdown">
                    <select id="typeFilter" class="filter-select">
                        <option value="">Alle Typen</option>
                        <option value="note">Notizen</option>
                        <option value="daily">Daily Notes</option>
                        <option value="task">Aufgaben</option>
                        <option value="meeting">Meeting</option>
                        <option value="documentation">Dokumentation</option>
                    </select>
                </div>

                <div class="filter-dropdown">
                    <select id="sortFilter" class="filter-select">
                        <option value="updated_desc">Zuletzt bearbeitet</option>
                        <option value="created_desc">Neu erstellt</option>
                        <option value="title_asc">Titel A-Z</option>
                        <option value="title_desc">Titel Z-A</option>
                        <option value="priority_desc">Priorität</option>
                    </select>
                </div>
            </div>

            <!-- View Controls -->
            <div class="view-controls">
                <div class="view-toggle">
                    <button class="view-toggle-btn active" data-view="grid" title="Grid-Ansicht">
                        <i class="fas fa-th"></i>
                    </button>
                    <button class="view-toggle-btn" data-view="list" title="Listen-Ansicht">
                        <i class="fas fa-list"></i>
                    </button>
                    <button class="view-toggle-btn" data-view="timeline" title="Timeline-Ansicht">
                        <i class="fas fa-clock"></i>
                    </button>
                </div>

                <button class="btn btn-secondary" id="bulkOperationBtn" title="Stapelverarbeitung">
                    <i class="fas fa-tasks"></i>
                </button>

                <button class="btn btn-secondary" onclick="enhancedNotesUI.showAISuggestions()" title="KI-Vorschläge">
                    <i class="fas fa-magic"></i>
                </button>
            </div>
        </div>

        <!-- Status Bar -->
        <div class="status-bar">
            <div class="status-info">
                <span id="noteCount" class="status-item">
                    <i class="fas fa-sticky-note"></i>
                    <span id="noteCountValue">0</span> Notizen
                </span>
                <span id="selectionCount" class="status-item hidden">
                    <i class="fas fa-check-square"></i>
                    <span id="selectionCountValue">0</span> ausgewählt
                </span>
            </div>
            
            <div class="status-actions">
                <button class="btn btn-sm btn-ghost" onclick="enhancedNotesUI.toggleArchiveView()" id="archiveToggle">
                    <i class="fas fa-archive"></i>
                    <span>Archiv</span>
                </button>
                
                <button class="btn btn-sm btn-ghost" onclick="enhancedNotesUI.refreshNotes()" title="Aktualisieren">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>

        <!-- Bulk Operations Panel -->
        <div id="bulkOperationsPanel" class="bulk-operations-panel hidden">
            <div class="bulk-header">
                <h3>Stapelverarbeitung</h3>
                <button class="btn btn-sm btn-ghost" onclick="enhancedNotesUI.toggleSelectionMode()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="bulk-actions">
                <button class="btn btn-sm btn-secondary" onclick="enhancedNotesUI.bulkArchive()">
                    <i class="fas fa-archive"></i> Archivieren
                </button>
                <button class="btn btn-sm btn-secondary" onclick="enhancedNotesUI.bulkPin()">
                    <i class="fas fa-thumbtack"></i> Anheften
                </button>
                <button class="btn btn-sm btn-secondary" onclick="enhancedNotesUI.bulkTag()">
                    <i class="fas fa-tags"></i> Tags hinzufügen
                </button>
                <button class="btn btn-sm btn-secondary" onclick="enhancedNotesUI.bulkCategory()">
                    <i class="fas fa-folder"></i> Kategorisieren
                </button>
                <button class="btn btn-sm btn-danger" onclick="enhancedNotesUI.bulkDelete()">
                    <i class="fas fa-trash"></i> Löschen
                </button>
            </div>
        </div>

        <!-- Main Content Area -->
        <main class="notes-main">
            <div id="notesContainer" class="notes-container">
                <!-- Notes will be loaded here -->
            </div>
            
            <!-- Pagination -->
            <div id="paginationContainer" class="pagination-container hidden">
                <button class="btn btn-secondary" id="loadMoreBtn" onclick="enhancedNotesUI.loadMoreNotes()">
                    <i class="fas fa-plus"></i>
                    Mehr laden
                </button>
            </div>
        </main>
    </div>

    <!-- AI Insights Panel -->
    <div id="aiInsightsPanel" class="ai-insights-panel hidden">
        <div class="panel-header">
            <h3><i class="fas fa-robot"></i> KI-Einblicke</h3>
            <button class="panel-close" onclick="this.closest('.ai-insights-panel').classList.add('hidden')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="panel-content">
            <div class="insight-section">
                <h4>Heute empfohlen</h4>
                <div id="dailyRecommendations"></div>
            </div>
            <div class="insight-section">
                <h4>Verknüpfungsvorschläge</h4>
                <div id="linkSuggestions"></div>
            </div>
            <div class="insight-section">
                <h4>Unorganisierte Notizen</h4>
                <div id="unorganizedNotes"></div>
            </div>
        </div>
    </div>

    <!-- Keyboard Shortcuts Help -->
    <div id="shortcutsModal" class="shortcuts-modal hidden">
        <div class="shortcuts-overlay" onclick="this.closest('.shortcuts-modal').classList.add('hidden')"></div>
        <div class="shortcuts-content">
            <div class="shortcuts-header">
                <h3><i class="fas fa-keyboard"></i> Tastaturkürzel</h3>
                <button class="shortcuts-close" onclick="this.closest('.shortcuts-modal').classList.add('hidden')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="shortcuts-grid">
                <div class="shortcut-item">
                    <kbd>Ctrl + N</kbd>
                    <span>Neue Notiz</span>
                </div>
                <div class="shortcut-item">
                    <kbd>Ctrl + K</kbd>
                    <span>Schnell-Erfassung</span>
                </div>
                <div class="shortcut-item">
                    <kbd>Ctrl + F</kbd>
                    <span>Suche fokussieren</span>
                </div>
                <div class="shortcut-item">
                    <kbd>Ctrl + G</kbd>
                    <span>Graph-Ansicht</span>
                </div>
                <div class="shortcut-item">
                    <kbd>Ctrl + A</kbd>
                    <span>Alle auswählen</span>
                </div>
                <div class="shortcut-item">
                    <kbd>Esc</kbd>
                    <span>Auswahl beenden</span>
                </div>
                <div class="shortcut-item">
                    <kbd>?</kbd>
                    <span>Hilfe anzeigen</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Context Menus -->
    <div id="noteContextMenu" class="context-menu hidden">
        <div class="context-item" onclick="enhancedNotesUI.openNote()">
            <i class="fas fa-eye"></i> Öffnen
        </div>
        <div class="context-item" onclick="enhancedNotesUI.editNote()">
            <i class="fas fa-edit"></i> Bearbeiten
        </div>
        <div class="context-divider"></div>
        <div class="context-item" onclick="enhancedNotesUI.duplicateNote()">
            <i class="fas fa-copy"></i> Duplizieren
        </div>
        <div class="context-item" onclick="enhancedNotesUI.shareNote()">
            <i class="fas fa-share"></i> Teilen
        </div>
        <div class="context-divider"></div>
        <div class="context-item" onclick="enhancedNotesUI.pinNote()">
            <i class="fas fa-thumbtack"></i> Anheften
        </div>
        <div class="context-item" onclick="enhancedNotesUI.favoriteNote()">
            <i class="fas fa-heart"></i> Favorit
        </div>
        <div class="context-divider"></div>
        <div class="context-item danger" onclick="enhancedNotesUI.deleteNote()">
            <i class="fas fa-trash"></i> Löschen
        </div>
    </div>

    <!-- Toast Notifications Container -->
    <div id="toastContainer" class="toast-container"></div>

    <!-- Scripts -->
    <script src="/js/enhanced-notes-manager.js"></script>
    <script src="/js/enhanced-notes-ui.js"></script>
    
    <!-- Chart.js for Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- D3.js for Graph Visualization -->
    <script src="https://d3js.org/d3.v7.min.js"></script>

    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => console.log('SW registered'))
                    .catch(err => console.log('SW registration failed'));
            });
        }
    </script>

    <!-- Help System -->
    <script>
        document.addEventListener('keydown', (e) => {
            if (e.key === '?' && !e.target.closest('input, textarea')) {
                document.getElementById('shortcutsModal').classList.remove('hidden');
            }
        });
    </script>

    <!-- Additional Styles -->
    <style>
        /* Additional component styles */
        .status-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--spacing-md) var(--spacing-lg);
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            margin-bottom: var(--spacing-lg);
            font-size: 0.875rem;
        }

        .status-info {
            display: flex;
            gap: var(--spacing-lg);
            align-items: center;
        }

        .status-item {
            display: flex;
            align-items: center;
            gap: var(--spacing-xs);
            color: var(--text-secondary);
        }

        .status-actions {
            display: flex;
            gap: var(--spacing-sm);
            align-items: center;
        }

        .bulk-operations-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--spacing-lg);
            margin-bottom: var(--spacing-lg);
            transition: all var(--transition-normal);
        }

        .bulk-operations-panel.hidden {
            display: none;
        }

        .bulk-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-md);
        }

        .bulk-header h3 {
            margin: 0;
            color: var(--text-primary);
            font-size: 1.125rem;
        }

        .bulk-actions {
            display: flex;
            flex-wrap: wrap;
            gap: var(--spacing-sm);
        }

        .ai-insights-panel {
            position: fixed;
            top: 50%;
            right: 2rem;
            transform: translateY(-50%);
            width: 350px;
            max-height: 70vh;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            z-index: 100;
            overflow: hidden;
            transition: all var(--transition-normal);
        }

        .ai-insights-panel.hidden {
            display: none;
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--spacing-lg);
            border-bottom: 1px solid var(--glass-border);
            background: rgba(59, 130, 246, 0.1);
        }

        .panel-header h3 {
            margin: 0;
            color: var(--text-primary);
            font-size: 1.125rem;
        }

        .panel-close {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: var(--spacing-xs);
            border-radius: var(--radius-sm);
            transition: all var(--transition-normal);
        }

        .panel-close:hover {
            background: var(--bg-hover);
            color: var(--text-primary);
        }

        .panel-content {
            padding: var(--spacing-lg);
            max-height: 60vh;
            overflow-y: auto;
        }

        .insight-section {
            margin-bottom: var(--spacing-lg);
        }

        .insight-section h4 {
            margin: 0 0 var(--spacing-md) 0;
            color: var(--text-primary);
            font-size: 1rem;
            font-weight: 600;
        }

        .shortcuts-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .shortcuts-modal.hidden {
            display: none;
        }

        .shortcuts-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .shortcuts-content {
            position: relative;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            width: 90%;
            max-width: 600px;
            overflow: hidden;
        }

        .shortcuts-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--spacing-lg);
            border-bottom: 1px solid var(--glass-border);
        }

        .shortcuts-header h3 {
            margin: 0;
            color: var(--text-primary);
            font-size: 1.25rem;
        }

        .shortcuts-close {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: var(--spacing-xs);
            border-radius: var(--radius-sm);
            transition: all var(--transition-normal);
        }

        .shortcuts-close:hover {
            background: var(--bg-hover);
            color: var(--text-primary);
        }

        .shortcuts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--spacing-md);
            padding: var(--spacing-lg);
        }

        .shortcut-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--spacing-md);
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--radius-lg);
        }

        .shortcut-item kbd {
            background: var(--text-primary);
            color: var(--bg-secondary);
            padding: 0.25rem 0.5rem;
            border-radius: var(--radius-sm);
            font-family: monospace;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .context-menu {
            position: fixed;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--spacing-xs);
            z-index: 1000;
            min-width: 180px;
            box-shadow: var(--glass-shadow);
        }

        .context-menu.hidden {
            display: none;
        }

        .context-item {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            padding: var(--spacing-md);
            color: var(--text-primary);
            cursor: pointer;
            border-radius: var(--radius-md);
            transition: all var(--transition-fast);
            font-size: 0.875rem;
        }

        .context-item:hover {
            background: var(--bg-hover);
        }

        .context-item.danger {
            color: var(--error);
        }

        .context-item.danger:hover {
            background: rgba(239, 68, 68, 0.1);
        }

        .context-divider {
            height: 1px;
            background: var(--glass-border);
            margin: var(--spacing-xs) 0;
        }

        .toast-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 2000;
            display: flex;
            flex-direction: column;
            gap: var(--spacing-sm);
        }

        .filter-controls {
            display: flex;
            gap: var(--spacing-md);
            align-items: center;
        }

        .view-controls {
            display: flex;
            gap: var(--spacing-md);
            align-items: center;
        }

        .pagination-container {
            text-align: center;
            padding: var(--spacing-xl);
        }

        .pagination-container.hidden {
            display: none;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .notes-header {
                padding: var(--spacing-md);
            }
            
            .header-content {
                flex-direction: column;
                gap: var(--spacing-md);
                align-items: stretch;
            }
            
            .header-actions {
                justify-content: center;
            }
            
            .notes-toolbar {
                flex-direction: column;
                gap: var(--spacing-md);
                align-items: stretch;
            }
            
            .filter-controls {
                flex-wrap: wrap;
            }
            
            .view-controls {
                justify-content: center;
            }
            
            .ai-insights-panel {
                right: 1rem;
                left: 1rem;
                width: auto;
                transform: translateY(-50%);
            }
            
            .shortcuts-content {
                margin: 1rem;
                width: auto;
            }
            
            .btn-text {
                display: none;
            }
        }
    </style>
</body>
</html>
