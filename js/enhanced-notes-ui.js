/**
 * Enhanced Notes UI - Advanced User Interface
 * Provides modern, intelligent note management interface
 */
class EnhancedNotesUI {
    constructor() {
        this.api = new EnhancedNotesManager();
        this.currentView = 'grid';
        this.selectedNotes = new Set();
        this.isSelectionMode = false;
        this.searchTimeout = null;
        this.autoSaveTimeout = null;
        this.quickCaptureModal = null;
        
        this.init();
    }
    
    async init() {
        this.setupEventListeners();
        this.setupKeyboardShortcuts();
        this.setupQuickCapture();
        this.setupDragAndDrop();
        this.setupRealTimeFeatures();
        this.initializeInterface();
        
        // Load initial data
        await this.loadNotes();
        this.setupAnalytics();
    }
    
    /**
     * Setup Event Listeners
     */
    setupEventListeners() {
        // Search functionality
        const searchInput = document.getElementById('notesSearch');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    this.performSmartSearch(e.target.value);
                }, 300);
            });
        }
        
        // View toggle buttons
        document.querySelectorAll('.view-toggle-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const view = e.target.dataset.view || e.target.closest('button').dataset.view;
                this.switchView(view);
            });
        });
        
        // Bulk operations
        document.getElementById('bulkOperationBtn')?.addEventListener('click', () => {
            this.toggleSelectionMode();
        });
        
        // Quick capture button
        document.getElementById('quickCaptureBtn')?.addEventListener('click', () => {
            this.openQuickCapture();
        });
        
        // AI suggestions button
        document.getElementById('aiSuggestionsBtn')?.addEventListener('click', () => {
            this.showAISuggestions();
        });
        
        // Analytics button
        document.getElementById('analyticsBtn')?.addEventListener('click', () => {
            this.showAnalytics();
        });
    }
    
    /**
     * Setup Keyboard Shortcuts
     */
    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + N: New note
            if ((e.ctrlKey || e.metaKey) && e.key === 'n' && !e.target.closest('.note-editor')) {
                e.preventDefault();
                this.createNewNote();
            }
            
            // Ctrl/Cmd + K: Quick capture
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                this.openQuickCapture();
            }
            
            // Ctrl/Cmd + F: Focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                document.getElementById('notesSearch')?.focus();
            }
            
            // Ctrl/Cmd + A: Select all (in selection mode)
            if ((e.ctrlKey || e.metaKey) && e.key === 'a' && this.isSelectionMode) {
                e.preventDefault();
                this.selectAllNotes();
            }
            
            // Escape: Exit selection mode
            if (e.key === 'Escape' && this.isSelectionMode) {
                this.toggleSelectionMode();
            }
            
            // Ctrl/Cmd + G: Graph view
            if ((e.ctrlKey || e.metaKey) && e.key === 'g') {
                e.preventDefault();
                this.showGraphView();
            }
        });
    }
    
    /**
     * Quick Capture Setup
     */
    setupQuickCapture() {
        this.quickCaptureModal = this.createQuickCaptureModal();
        document.body.appendChild(this.quickCaptureModal);
    }
    
    createQuickCaptureModal() {
        const modal = document.createElement('div');
        modal.id = 'quickCaptureModal';
        modal.className = 'quick-capture-modal hidden';
        modal.innerHTML = `
            <div class="quick-capture-overlay" onclick="this.closest('.quick-capture-modal').classList.add('hidden')"></div>
            <div class="quick-capture-content">
                <div class="quick-capture-header">
                    <h3><i class="fas fa-bolt"></i> Schnell-Notiz</h3>
                    <button class="quick-capture-close" onclick="this.closest('.quick-capture-modal').classList.add('hidden')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="quick-capture-body">
                    <textarea 
                        id="quickCaptureContent" 
                        placeholder="Gedanken, Ideen, ToDos... einfach losschreiben!"
                        rows="10"
                    ></textarea>
                    <div class="quick-capture-options">
                        <label class="checkbox-label">
                            <input type="checkbox" id="autoProcessCapture" checked>
                            <span>Automatisch organisieren (Tags, Kategorien)</span>
                        </label>
                        <select id="captureSource">
                            <option value="manual">Manuell</option>
                            <option value="web">Webseite</option>
                            <option value="meeting">Meeting</option>
                            <option value="idea">Idee</option>
                        </select>
                    </div>
                </div>
                <div class="quick-capture-footer">
                    <button class="btn-secondary" onclick="this.closest('.quick-capture-modal').classList.add('hidden')">
                        Abbrechen
                    </button>
                    <button class="btn-primary" onclick="enhancedNotesUI.executeQuickCapture()">
                        <i class="fas fa-save"></i> Speichern
                    </button>
                </div>
            </div>
        `;
        return modal;
    }
    
    /**
     * Smart Search Implementation
     */
    async performSmartSearch(query) {
        if (query.length < 2) {
            this.clearSearchResults();
            await this.loadNotes();
            return;
        }
        
        try {
            this.showSearchLoading();
            
            // Use semantic search for better results
            const response = await fetch(`/src/api/notes.php?action=smart_search&q=${encodeURIComponent(query)}&mode=hybrid&limit=20`);
            const data = await response.json();
            
            if (data.success) {
                this.displaySearchResults(data.results, query);
                this.highlightSearchTerms(query);
            }
        } catch (error) {
            console.error('Smart search error:', error);
            this.showNotification('Suchfehler', 'error');
        }
    }
    
    displaySearchResults(results, query) {
        const container = document.getElementById('notesContainer');
        if (!container) return;
        
        if (results.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-search text-4xl text-white/20 mb-4"></i>
                    <h3>Keine Ergebnisse für "${query}"</h3>
                    <p>Versuchen Sie andere Suchbegriffe oder erstellen Sie eine neue Notiz.</p>
                    <button class="btn-primary mt-4" onclick="enhancedNotesUI.createNoteFromSearch('${query}')">
                        <i class="fas fa-plus"></i> Notiz erstellen
                    </button>
                </div>
            `;
            return;
        }
        
        container.innerHTML = results.map(note => this.renderNoteCard(note, true)).join('');
        this.updateNoteCount(results.length);
    }
    
    /**
     * AI Suggestions Panel
     */
    async showAISuggestions(noteId = null) {
        if (!noteId && this.selectedNotes.size === 1) {
            noteId = Array.from(this.selectedNotes)[0];
        }
        
        if (!noteId) {
            this.showNotification('Bitte wählen Sie eine Notiz aus', 'warning');
            return;
        }
        
        try {
            const response = await fetch(`/src/api/notes.php?action=ai_suggestions&note_id=${noteId}`);
            const data = await response.json();
            
            if (data.success) {
                this.displaySuggestionsPanel(data.suggestions, noteId);
            }
        } catch (error) {
            console.error('AI suggestions error:', error);
            this.showNotification('KI-Vorschläge konnten nicht geladen werden', 'error');
        }
    }
    
    displaySuggestionsPanel(suggestions, noteId) {
        const panel = document.createElement('div');
        panel.className = 'suggestions-panel';
        panel.innerHTML = `
            <div class="suggestions-header">
                <h3><i class="fas fa-magic"></i> KI-Vorschläge</h3>
                <button class="close-btn" onclick="this.closest('.suggestions-panel').remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="suggestions-content">
                ${suggestions.map(suggestion => this.renderSuggestion(suggestion, noteId)).join('')}
            </div>
        `;
        
        document.body.appendChild(panel);
        panel.classList.add('show');
    }
    
    renderSuggestion(suggestion, noteId) {
        const confidenceColor = suggestion.confidence > 0.7 ? 'green' : 
                               suggestion.confidence > 0.5 ? 'yellow' : 'red';
        
        return `
            <div class="suggestion-item" data-type="${suggestion.type}">
                <div class="suggestion-header">
                    <span class="suggestion-type">${this.getSuggestionIcon(suggestion.type)} ${this.getSuggestionTitle(suggestion.type)}</span>
                    <span class="confidence-badge confidence-${confidenceColor}">
                        ${Math.round(suggestion.confidence * 100)}%
                    </span>
                </div>
                <div class="suggestion-content">
                    ${this.renderSuggestionContent(suggestion)}
                </div>
                <div class="suggestion-actions">
                    <button class="btn-sm btn-secondary" onclick="enhancedNotesUI.applySuggestion('${noteId}', '${suggestion.type}', ${JSON.stringify(suggestion.data).replace(/'/g, '\\\'')})">
                        Anwenden
                    </button>
                    <button class="btn-sm btn-ghost" onclick="this.closest('.suggestion-item').remove()">
                        Ablehnen
                    </button>
                </div>
                <div class="suggestion-reason">${suggestion.reason}</div>
            </div>
        `;
    }
    
    /**
     * Graph View Implementation
     */
    async showGraphView() {
        try {
            const response = await fetch('/src/api/notes.php?action=graph_data&include_orphans=false&min_connections=1');
            const data = await response.json();
            
            if (data.success) {
                this.displayGraphVisualization(data.graph);
            }
        } catch (error) {
            console.error('Graph view error:', error);
            this.showNotification('Graph-Ansicht konnte nicht geladen werden', 'error');
        }
    }
    
    displayGraphVisualization(graphData) {
        const modal = document.createElement('div');
        modal.className = 'graph-modal';
        modal.innerHTML = `
            <div class="graph-overlay" onclick="this.closest('.graph-modal').remove()"></div>
            <div class="graph-container">
                <div class="graph-header">
                    <h3><i class="fas fa-project-diagram"></i> Notizen-Netzwerk</h3>
                    <div class="graph-controls">
                        <button class="btn-sm" onclick="enhancedNotesUI.exportGraph()">
                            <i class="fas fa-download"></i> Export
                        </button>
                        <button class="btn-sm" onclick="this.closest('.graph-modal').remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div id="graphVisualization" class="graph-visualization"></div>
                <div class="graph-stats">
                    <div class="stat-item">
                        <span class="stat-label">Notizen:</span>
                        <span class="stat-value">${graphData.nodes.length}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Verbindungen:</span>
                        <span class="stat-value">${graphData.edges.length}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Dichte:</span>
                        <span class="stat-value">${Math.round(graphData.metrics.density * 100)}%</span>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        this.renderGraph(graphData);
    }
    
    /**
     * Analytics Dashboard
     */
    async showAnalytics() {
        try {
            const response = await fetch('/src/api/notes.php?action=analytics&timeframe=30d');
            const data = await response.json();
            
            if (data.success) {
                this.displayAnalyticsDashboard(data.analytics);
            }
        } catch (error) {
            console.error('Analytics error:', error);
            this.showNotification('Analytics konnten nicht geladen werden', 'error');
        }
    }
    
    displayAnalyticsDashboard(analytics) {
        const modal = document.createElement('div');
        modal.className = 'analytics-modal';
        modal.innerHTML = `
            <div class="analytics-overlay" onclick="this.closest('.analytics-modal').remove()"></div>
            <div class="analytics-container">
                <div class="analytics-header">
                    <h3><i class="fas fa-chart-bar"></i> Notizen Analytics</h3>
                    <button class="close-btn" onclick="this.closest('.analytics-modal').remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="analytics-content">
                    ${this.renderAnalyticsCharts(analytics)}
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        this.initializeCharts(analytics);
    }
    
    /**
     * Quick Capture Execution
     */
    async executeQuickCapture() {
        const content = document.getElementById('quickCaptureContent').value.trim();
        const autoProcess = document.getElementById('autoProcessCapture').checked;
        const source = document.getElementById('captureSource').value;
        
        if (!content) {
            this.showNotification('Bitte geben Sie Inhalt ein', 'warning');
            return;
        }
        
        try {
            const response = await fetch('/src/api/notes.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'quick_capture',
                    content: content,
                    source: source,
                    auto_process: autoProcess
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification('Schnell-Notiz erstellt', 'success');
                this.quickCaptureModal.classList.add('hidden');
                document.getElementById('quickCaptureContent').value = '';
                await this.loadNotes();
            } else {
                throw new Error(data.error || 'Failed to create quick note');
            }
        } catch (error) {
            console.error('Quick capture error:', error);
            this.showNotification('Fehler beim Erstellen der Notiz', 'error');
        }
    }
    
    /**
     * Utility Methods
     */
    openQuickCapture() {
        this.quickCaptureModal.classList.remove('hidden');
        document.getElementById('quickCaptureContent').focus();
    }
    
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${this.getNotificationIcon(type)}"></i>
                <span>${message}</span>
            </div>
            <button class="notification-close" onclick="this.closest('.notification').remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
    
    getNotificationIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-triangle',
            warning: 'exclamation-circle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }
    
    getSuggestionIcon(type) {
        const icons = {
            tags: '🏷️',
            category: '📁',
            links: '🔗',
            content_improvement: '✨',
            optimization: '⚡'
        };
        return icons[type] || '💡';
    }
    
    getSuggestionTitle(type) {
        const titles = {
            tags: 'Tag-Vorschläge',
            category: 'Kategorie-Vorschlag',
            links: 'Verknüpfungsvorschläge',
            content_improvement: 'Inhaltsverbesserungen',
            optimization: 'Optimierungsvorschläge'
        };
        return titles[type] || 'Vorschlag';
    }
    
    async loadNotes() {
        try {
            await this.api.loadNotes();
            this.renderNotes();
        } catch (error) {
            console.error('Load notes error:', error);
            this.showNotification('Fehler beim Laden der Notizen', 'error');
        }
    }
    
    renderNotes() {
        const container = document.getElementById('notesContainer');
        if (!container) return;
        
        const notes = this.api.notes;
        
        if (notes.length === 0) {
            container.innerHTML = this.renderEmptyState();
            return;
        }
        
        switch (this.currentView) {
            case 'grid':
                container.innerHTML = notes.map(note => this.renderNoteCard(note)).join('');
                break;
            case 'list':
                container.innerHTML = this.renderListView(notes);
                break;
            case 'timeline':
                container.innerHTML = this.renderTimelineView(notes);
                break;
        }
        
        this.updateNoteCount(notes.length);
    }
    
    renderNoteCard(note, isSearchResult = false) {
        const tags = Array.isArray(note.tags) ? note.tags : [];
        const isSelected = this.selectedNotes.has(note.id);
        
        return `
            <div class="note-card ${isSelected ? 'selected' : ''} ${isSearchResult ? 'search-result' : ''}" 
                 data-note-id="${note.id}"
                 style="border-left-color: ${note.color}">
                ${this.isSelectionMode ? `
                    <div class="note-selection">
                        <input type="checkbox" ${isSelected ? 'checked' : ''} 
                               onchange="enhancedNotesUI.toggleNoteSelection('${note.id}')">
                    </div>
                ` : ''}
                <div class="note-header">
                    <h3 class="note-title" onclick="enhancedNotesUI.openNote('${note.id}')">${note.title}</h3>
                    <div class="note-actions">
                        ${note.is_pinned ? '<i class="fas fa-thumbtack text-yellow-400"></i>' : ''}
                        ${note.is_favorite ? '<i class="fas fa-heart text-red-400"></i>' : ''}
                        <button class="note-action-btn" onclick="enhancedNotesUI.showNoteMenu('${note.id}', event)">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                    </div>
                </div>
                <div class="note-content">
                    ${note.content ? note.content.substring(0, 150) + (note.content.length > 150 ? '...' : '') : ''}
                </div>
                <div class="note-footer">
                    <div class="note-tags">
                        ${tags.slice(0, 3).map(tag => `<span class="tag">#${tag}</span>`).join('')}
                        ${tags.length > 3 ? `<span class="tag-more">+${tags.length - 3}</span>` : ''}
                    </div>
                    <div class="note-meta">
                        <span class="note-date">${this.formatDate(note.updated_at)}</span>
                        ${note.links_count > 0 ? `<span class="note-links"><i class="fas fa-link"></i> ${note.links_count}</span>` : ''}
                    </div>
                </div>
            </div>
        `;
    }
    
    renderEmptyState() {
        return `
            <div class="empty-state">
                <i class="fas fa-sticky-note text-6xl text-white/20 mb-6"></i>
                <h3 class="text-xl font-semibold mb-2">Noch keine Notizen</h3>
                <p class="text-white/70 mb-6">Erstellen Sie Ihre erste Notiz oder verwenden Sie die Schnell-Erfassung</p>
                <div class="empty-state-actions">
                    <button class="btn-primary" onclick="enhancedNotesUI.createNewNote()">
                        <i class="fas fa-plus"></i> Neue Notiz
                    </button>
                    <button class="btn-secondary" onclick="enhancedNotesUI.openQuickCapture()">
                        <i class="fas fa-bolt"></i> Schnell-Erfassung
                    </button>
                </div>
            </div>
        `;
    }
    
    formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffTime = Math.abs(now - date);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays === 1) return 'Heute';
        if (diffDays === 2) return 'Gestern';
        if (diffDays <= 7) return `vor ${diffDays} Tagen`;
        
        return date.toLocaleDateString('de-DE');
    }
    
    initializeInterface() {
        // Add enhanced toolbar
        this.addEnhancedToolbar();
        
        // Add floating action button
        this.addFloatingActionButton();
        
        // Initialize tooltips
        this.initializeTooltips();
    }
    
    addEnhancedToolbar() {
        const toolbar = document.querySelector('.notes-toolbar');
        if (!toolbar) return;
        
        const enhancedControls = document.createElement('div');
        enhancedControls.className = 'enhanced-controls';
        enhancedControls.innerHTML = `
            <button id="quickCaptureBtn" class="btn-secondary" title="Schnell-Erfassung (Ctrl+K)">
                <i class="fas fa-bolt"></i>
            </button>
            <button id="aiSuggestionsBtn" class="btn-secondary" title="KI-Vorschläge">
                <i class="fas fa-magic"></i>
            </button>
            <button id="analyticsBtn" class="btn-secondary" title="Analytics">
                <i class="fas fa-chart-bar"></i>
            </button>
            <button id="bulkOperationBtn" class="btn-secondary" title="Stapelverarbeitung">
                <i class="fas fa-tasks"></i>
            </button>
        `;
        
        toolbar.appendChild(enhancedControls);
    }
    
    addFloatingActionButton() {
        const fab = document.createElement('div');
        fab.className = 'floating-action-button';
        fab.innerHTML = `
            <button class="fab-main" onclick="enhancedNotesUI.openQuickCapture()">
                <i class="fas fa-plus"></i>
            </button>
        `;
        
        document.body.appendChild(fab);
    }
    
    initializeTooltips() {
        // Simple tooltip implementation
        document.querySelectorAll('[title]').forEach(element => {
            element.addEventListener('mouseenter', (e) => {
                const tooltip = document.createElement('div');
                tooltip.className = 'tooltip';
                tooltip.textContent = e.target.getAttribute('title');
                document.body.appendChild(tooltip);
                
                const rect = e.target.getBoundingClientRect();
                tooltip.style.left = rect.left + rect.width / 2 - tooltip.offsetWidth / 2 + 'px';
                tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
                
                e.target._tooltip = tooltip;
            });
            
            element.addEventListener('mouseleave', (e) => {
                if (e.target._tooltip) {
                    e.target._tooltip.remove();
                    delete e.target._tooltip;
                }
            });
        });
    }
}

// CSS Styles for Enhanced UI
const enhancedStyles = `
<style>
.quick-capture-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.quick-capture-modal.hidden {
    display: none;
}

.quick-capture-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(5px);
}

.quick-capture-content {
    position: relative;
    background: linear-gradient(135deg, rgba(30, 41, 59, 0.95), rgba(51, 65, 85, 0.95));
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 1rem;
    width: 90%;
    max-width: 600px;
    max-height: 80vh;
    overflow: hidden;
    backdrop-filter: blur(20px);
}

.quick-capture-header {
    display: flex;
    justify-content: between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.quick-capture-header h3 {
    color: white;
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
}

.quick-capture-close {
    background: none;
    border: none;
    color: rgba(255, 255, 255, 0.7);
    font-size: 1.25rem;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 0.5rem;
}

.quick-capture-close:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
}

.quick-capture-body {
    padding: 1.5rem;
}

#quickCaptureContent {
    width: 100%;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 0.75rem;
    padding: 1rem;
    color: white;
    font-size: 1rem;
    line-height: 1.5;
    resize: vertical;
    min-height: 200px;
}

#quickCaptureContent:focus {
    outline: none;
    border-color: rgba(59, 130, 246, 0.5);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.quick-capture-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
    gap: 1rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.875rem;
    cursor: pointer;
}

.quick-capture-footer {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    padding: 1rem 1.5rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(0, 0, 0, 0.1);
}

.suggestions-panel {
    position: fixed;
    top: 50%;
    right: 2rem;
    transform: translateY(-50%);
    width: 400px;
    max-height: 80vh;
    background: linear-gradient(135deg, rgba(30, 41, 59, 0.95), rgba(51, 65, 85, 0.95));
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 1rem;
    backdrop-filter: blur(20px);
    z-index: 1000;
    opacity: 0;
    transform: translateY(-50%) translateX(100%);
    transition: all 0.3s ease;
}

.suggestions-panel.show {
    opacity: 1;
    transform: translateY(-50%) translateX(0);
}

.suggestion-item {
    padding: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.suggestion-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.confidence-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
}

.confidence-green { background: rgba(34, 197, 94, 0.2); color: rgb(34, 197, 94); }
.confidence-yellow { background: rgba(234, 179, 8, 0.2); color: rgb(234, 179, 8); }
.confidence-red { background: rgba(239, 68, 68, 0.2); color: rgb(239, 68, 68); }

.graph-modal, .analytics-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.graph-container, .analytics-container {
    position: relative;
    background: linear-gradient(135deg, rgba(30, 41, 59, 0.95), rgba(51, 65, 85, 0.95));
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 1rem;
    width: 90%;
    max-width: 1200px;
    height: 80vh;
    backdrop-filter: blur(20px);
    display: flex;
    flex-direction: column;
}

.graph-visualization {
    flex: 1;
    padding: 1rem;
}

.floating-action-button {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    z-index: 100;
}

.fab-main {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(59, 130, 246, 0.4);
    transition: all 0.3s ease;
}

.fab-main:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 25px rgba(59, 130, 246, 0.6);
}

.enhanced-controls {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.note-card {
    transition: all 0.3s ease;
    position: relative;
}

.note-card.selected {
    background: rgba(59, 130, 246, 0.1);
    border-color: rgb(59, 130, 246);
}

.note-selection {
    position: absolute;
    top: 0.5rem;
    left: 0.5rem;
    z-index: 10;
}

.notification {
    position: fixed;
    top: 1rem;
    right: 1rem;
    z-index: 2000;
    background: rgba(30, 41, 59, 0.95);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 0.75rem;
    padding: 1rem;
    color: white;
    display: flex;
    align-items: center;
    gap: 1rem;
    min-width: 300px;
    backdrop-filter: blur(20px);
    animation: slideIn 0.3s ease;
}

.notification-success { border-left: 4px solid rgb(34, 197, 94); }
.notification-error { border-left: 4px solid rgb(239, 68, 68); }
.notification-warning { border-left: 4px solid rgb(234, 179, 8); }
.notification-info { border-left: 4px solid rgb(59, 130, 246); }

@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

.tooltip {
    position: absolute;
    background: rgba(0, 0, 0, 0.9);
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    white-space: nowrap;
    z-index: 2000;
    pointer-events: none;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: rgba(255, 255, 255, 0.8);
}

.empty-state-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}
</style>
`;

// Add styles to document
document.head.insertAdjacentHTML('beforeend', enhancedStyles);

// Initialize Enhanced Notes UI
let enhancedNotesUI;
document.addEventListener('DOMContentLoaded', () => {
    enhancedNotesUI = new EnhancedNotesUI();
});
