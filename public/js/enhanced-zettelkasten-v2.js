/**
 * Enhanced Zettelkasten Manager
 * Advanced note management with rich features
 */
class EnhancedZettelkasten {
    constructor() {
        this.notes = [];
        this.links = [];
        this.categories = [];
        this.templates = [];
        this.selectedNotes = new Set();
        this.currentView = 'grid';
        this.searchQuery = '';
        this.activeFilters = {
            category: null,
            tags: [],
            dateRange: null,
            favorite: false
        };
        this.tooltip = null;
        this.simulation = null;
        this.editor = null;
        this.statistics = null;
        
        this.init();
    }
    
    async init() {
        await this.loadData();
        this.setupEventListeners();
        this.setupEditor();
        this.createTooltip();
        this.initializeKeyboardShortcuts();
    }
    
    async loadData() {
        try {
            // Load notes with enhanced API
            const notesResponse = await fetch('/src/api/enhanced_notes.php?include_links=true&limit=200');
            const notesData = await notesResponse.json();
            
            if (notesData.success) {
                this.notes = notesData.notes || [];
                this.links = notesData.links || [];
            }
            
            // Load categories
            const categoriesResponse = await fetch('/src/api/enhanced_notes.php?action=categories');
            const categoriesData = await categoriesResponse.json();
            if (categoriesData.success) {
                this.categories = categoriesData.categories || [];
            }
            
            // Load templates
            const templatesResponse = await fetch('/src/api/enhanced_notes.php?action=templates');
            const templatesData = await templatesResponse.json();
            if (templatesData.success) {
                this.templates = templatesData.templates || [];
            }
            
            // Load statistics
            const statsResponse = await fetch('/src/api/enhanced_notes.php?action=stats');
            const statsData = await statsResponse.json();
            if (statsData.success) {
                this.statistics = statsData.statistics;
                this.updateStatisticsDisplay();
            }
            
            this.updateNotesDisplay();
            this.updateCategoriesFilter();
            this.updateTemplatesDropdown();
            
        } catch (error) {
            console.error('Error loading data:', error);
            this.showNotification('Fehler beim Laden der Daten', 'error');
        }
    }
    
    setupEventListeners() {
        // Enhanced search with debouncing
        const searchInput = document.getElementById('notesSearch');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.searchQuery = e.target.value.toLowerCase();
                    this.performSearch();
                }, 300);
            });
        }
        
        // Advanced search toggle
        const advancedSearchBtn = document.getElementById('advancedSearchToggle');
        if (advancedSearchBtn) {
            advancedSearchBtn.addEventListener('click', () => this.toggleAdvancedSearch());
        }
        
        // View toggle buttons
        document.querySelectorAll('.view-toggle-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const view = e.target.dataset.view || e.target.closest('button').dataset.view;
                this.switchView(view);
            });
        });
        
        // Filter controls
        this.setupFilterControls();
        
        // Graph controls
        this.setupGraphControls();
        
        // Category management
        this.setupCategoryManagement();
        
        // Template management
        this.setupTemplateManagement();
        
        // Bulk operations
        this.setupBulkOperations();
    }
    
    setupFilterControls() {
        // Category filter
        const categoryFilter = document.getElementById('categoryFilter');
        if (categoryFilter) {
            categoryFilter.addEventListener('change', (e) => {
                this.activeFilters.category = e.target.value || null;
                this.applyFilters();
            });
        }
        
        // Tag filter
        const tagFilter = document.getElementById('tagFilter');
        if (tagFilter) {
            tagFilter.addEventListener('input', (e) => {
                this.activeFilters.tags = e.target.value.split(',').map(tag => tag.trim()).filter(tag => tag);
                this.applyFilters();
            });
        }
        
        // Date range filter
        const dateFromFilter = document.getElementById('dateFromFilter');
        const dateToFilter = document.getElementById('dateToFilter');
        if (dateFromFilter && dateToFilter) {
            [dateFromFilter, dateToFilter].forEach(input => {
                input.addEventListener('change', () => {
                    this.activeFilters.dateRange = {
                        from: dateFromFilter.value,
                        to: dateToFilter.value
                    };
                    this.applyFilters();
                });
            });
        }
        
        // Favorite filter
        const favoriteFilter = document.getElementById('favoriteFilter');
        if (favoriteFilter) {
            favoriteFilter.addEventListener('change', (e) => {
                this.activeFilters.favorite = e.target.checked;
                this.applyFilters();
            });
        }
        
        // Clear filters
        const clearFiltersBtn = document.getElementById('clearFilters');
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', () => this.clearFilters());
        }
    }
    
    setupEditor() {
        // Initialize rich text editor if available
        const editorContainer = document.getElementById('noteContentEditor');
        if (editorContainer && typeof Quill !== 'undefined') {
            this.editor = new Quill(editorContainer, {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'color': [] }, { 'background': [] }],
                        ['link', 'blockquote', 'code-block'],
                        ['clean']
                    ]
                },
                placeholder: 'Notiz Inhalt...'
            });
            
            // Auto-save functionality
            this.editor.on('text-change', () => {
                if (this.currentNote && this.autoSaveTimeout) {
                    clearTimeout(this.autoSaveTimeout);
                }
                this.autoSaveTimeout = setTimeout(() => {
                    this.autoSaveNote();
                }, 2000);
            });
        }
    }
    
    initializeKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + K for quick search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                this.focusSearch();
            }
            
            // Ctrl/Cmd + N for new note
            if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                e.preventDefault();
                this.createNewNote();
            }
            
            // Ctrl/Cmd + S for save
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                this.saveCurrentNote();
            }
            
            // Escape to close modals
            if (e.key === 'Escape') {
                this.closeAllModals();
            }
            
            // Arrow keys for navigation in graph view
            if (this.currentView === 'node' && ['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
                this.handleGraphNavigation(e);
            }
        });
    }
    
    async performSearch() {
        if (!this.searchQuery.trim()) {
            this.updateNotesDisplay();
            return;
        }
        
        try {
            const response = await fetch(`/src/api/enhanced_notes.php?action=search&q=${encodeURIComponent(this.searchQuery)}`);
            const data = await response.json();
            
            if (data.success) {
                this.displaySearchResults(data.results);
                this.updateSearchStats(data.results.length, this.searchQuery);
            }
        } catch (error) {
            console.error('Search error:', error);
            this.showNotification('Suchfehler', 'error');
        }
    }
    
    displaySearchResults(results) {
        const container = this.getActiveViewContainer();
        if (!container) return;
        
        if (results.length === 0) {
            container.innerHTML = this.getEmptySearchHTML();
            return;
        }
        
        switch (this.currentView) {
            case 'grid':
                container.innerHTML = results.map(note => this.createSearchResultCardHTML(note)).join('');
                break;
            case 'list':
                container.innerHTML = results.map(note => this.createSearchResultListHTML(note)).join('');
                break;
            case 'node':
                this.updateNodeView(results);
                break;
        }
    }
    
    applyFilters() {
        let filteredNotes = [...this.notes];
        
        // Category filter
        if (this.activeFilters.category) {
            filteredNotes = filteredNotes.filter(note => 
                note.category_id == this.activeFilters.category
            );
        }
        
        // Tags filter
        if (this.activeFilters.tags.length > 0) {
            filteredNotes = filteredNotes.filter(note => 
                this.activeFilters.tags.some(filterTag => 
                    note.tags.some(noteTag => 
                        noteTag.toLowerCase().includes(filterTag.toLowerCase())
                    )
                )
            );
        }
        
        // Date range filter
        if (this.activeFilters.dateRange && this.activeFilters.dateRange.from) {
            filteredNotes = filteredNotes.filter(note => {
                const noteDate = new Date(note.updated_at);
                const fromDate = new Date(this.activeFilters.dateRange.from);
                const toDate = this.activeFilters.dateRange.to ? new Date(this.activeFilters.dateRange.to) : new Date();
                
                return noteDate >= fromDate && noteDate <= toDate;
            });
        }
        
        // Favorite filter
        if (this.activeFilters.favorite) {
            filteredNotes = filteredNotes.filter(note => note.is_favorite);
        }
        
        this.updateNotesDisplay(filteredNotes);
        this.updateFilterStats(filteredNotes.length);
    }
    
    clearFilters() {
        this.activeFilters = {
            category: null,
            tags: [],
            dateRange: null,
            favorite: false
        };
        
        // Reset filter controls
        const categoryFilter = document.getElementById('categoryFilter');
        if (categoryFilter) categoryFilter.value = '';
        
        const tagFilter = document.getElementById('tagFilter');
        if (tagFilter) tagFilter.value = '';
        
        const dateFromFilter = document.getElementById('dateFromFilter');
        const dateToFilter = document.getElementById('dateToFilter');
        if (dateFromFilter) dateFromFilter.value = '';
        if (dateToFilter) dateToFilter.value = '';
        
        const favoriteFilter = document.getElementById('favoriteFilter');
        if (favoriteFilter) favoriteFilter.checked = false;
        
        this.updateNotesDisplay();
    }
    
    async createNewNote(template = null) {
        const modal = document.getElementById('noteEditorModal');
        if (!modal) return;
        
        this.currentNote = null;
        
        // Reset form
        const form = document.getElementById('noteForm');
        if (form) form.reset();
        
        // Apply template if provided
        if (template) {
            await this.applyTemplate(template);
        }
        
        // Focus title input
        const titleInput = document.getElementById('noteTitle');
        if (titleInput) {
            setTimeout(() => titleInput.focus(), 100);
        }
        
        modal.classList.add('active');
    }
    
    async applyTemplate(templateId) {
        const template = this.templates.find(t => t.id == templateId);
        if (!template) return;
        
        const titleInput = document.getElementById('noteTitle');
        const contentInput = document.getElementById('noteContent');
        
        // Apply template content with placeholder replacement
        let content = template.template_content;
        content = content.replace(/\{\{date\}\}/g, new Date().toISOString().split('T')[0]);
        content = content.replace(/\{\{time\}\}/g, new Date().toLocaleTimeString());
        content = content.replace(/\{\{title\}\}/g, titleInput?.value || '');
        
        if (this.editor) {
            this.editor.setText(content);
        } else if (contentInput) {
            contentInput.value = content;
        }
        
        // Update template usage
        try {
            await fetch('/src/api/enhanced_notes.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'template_usage',
                    template_id: templateId
                })
            });
        } catch (error) {
            console.error('Error updating template usage:', error);
        }
    }
    
    async saveCurrentNote() {
        const form = document.getElementById('noteForm');
        if (!form) return;
        
        const formData = new FormData(form);
        const noteData = {
            title: formData.get('title'),
            content: this.editor ? this.editor.root.innerHTML : formData.get('content'),
            color: formData.get('color') || '#fbbf24',
            category_id: formData.get('category_id') || null,
            tags: formData.get('tags')?.split(',').map(tag => tag.trim()).filter(tag => tag) || [],
            is_pinned: formData.has('is_pinned'),
            is_favorite: formData.has('is_favorite'),
            template_id: formData.get('template_id') || null
        };
        
        if (!noteData.title.trim()) {
            this.showNotification('Titel ist erforderlich', 'error');
            return;
        }
        
        try {
            const url = '/src/api/enhanced_notes.php';
            const method = this.currentNote ? 'PUT' : 'POST';
            
            if (this.currentNote) {
                noteData.id = this.currentNote.id;
            }
            
            const response = await fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(noteData)
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification(data.message || 'Notiz gespeichert', 'success');
                await this.loadData(); // Reload to get updated data
                this.closeNoteEditor();
            } else {
                this.showNotification(data.error || 'Fehler beim Speichern', 'error');
            }
        } catch (error) {
            console.error('Error saving note:', error);
            this.showNotification('Fehler beim Speichern der Notiz', 'error');
        }
    }
    
    async autoSaveNote() {
        if (!this.currentNote) return;
        
        const form = document.getElementById('noteForm');
        if (!form) return;
        
        const formData = new FormData(form);
        const noteData = {
            id: this.currentNote.id,
            title: formData.get('title'),
            content: this.editor ? this.editor.root.innerHTML : formData.get('content'),
            auto_save: true
        };
        
        try {
            await fetch('/src/api/enhanced_notes.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(noteData)
            });
            
            // Show auto-save indicator
            this.showAutoSaveIndicator();
        } catch (error) {
            console.error('Auto-save error:', error);
        }
    }
    
    showAutoSaveIndicator() {
        const indicator = document.getElementById('autoSaveIndicator');
        if (indicator) {
            indicator.style.opacity = '1';
            setTimeout(() => {
                indicator.style.opacity = '0';
            }, 2000);
        }
    }
    
    async duplicateNote(noteId) {
        const note = this.notes.find(n => n.id == noteId);
        if (!note) return;
        
        try {
            const response = await fetch('/src/api/enhanced_notes.php?action=duplicate', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    note_id: noteId,
                    title: `${note.title} (Kopie)`
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification('Notiz dupliziert', 'success');
                await this.loadData();
            } else {
                this.showNotification(data.error || 'Fehler beim Duplizieren', 'error');
            }
        } catch (error) {
            console.error('Error duplicating note:', error);
            this.showNotification('Fehler beim Duplizieren der Notiz', 'error');
        }
    }
    
    async toggleNotePin(noteId) {
        try {
            const response = await fetch('/src/api/enhanced_notes.php?action=pin', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ note_id: noteId })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Update local data
                const note = this.notes.find(n => n.id == noteId);
                if (note) {
                    note.is_pinned = !note.is_pinned;
                    this.updateNotesDisplay();
                }
                this.showNotification('Pin-Status aktualisiert', 'success');
            }
        } catch (error) {
            console.error('Error toggling pin:', error);
            this.showNotification('Fehler beim Aktualisieren des Pin-Status', 'error');
        }
    }
    
    async toggleNoteFavorite(noteId) {
        try {
            const response = await fetch('/src/api/enhanced_notes.php?action=favorite', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ note_id: noteId })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Update local data
                const note = this.notes.find(n => n.id == noteId);
                if (note) {
                    note.is_favorite = !note.is_favorite;
                    this.updateNotesDisplay();
                }
                this.showNotification('Favorit-Status aktualisiert', 'success');
            }
        } catch (error) {
            console.error('Error toggling favorite:', error);
            this.showNotification('Fehler beim Aktualisieren des Favorit-Status', 'error');
        }
    }
    
    // Enhanced note card with more features
    createEnhancedNoteCardHTML(note) {
        const categoryBadge = note.category_name ? 
            `<span class="category-badge" style="background-color: ${note.category_color}20; color: ${note.category_color}">
                <i class="fas fa-${note.category_icon || 'folder'}"></i> ${note.category_name}
            </span>` : '';
        
        const wordCountBadge = note.word_count > 0 ? 
            `<span class="word-count-badge">
                <i class="fas fa-file-word"></i> ${note.word_count} Wörter
            </span>` : '';
        
        const readingTimeBadge = note.reading_time_minutes > 0 ? 
            `<span class="reading-time-badge">
                <i class="fas fa-clock"></i> ${note.reading_time_minutes} Min
            </span>` : '';
        
        return `
            <div class="note-card enhanced" data-note-id="${note.id}" style="border-left: 4px solid ${note.color}">
                <div class="note-card-header">
                    <div class="note-meta-top">
                        ${categoryBadge}
                        <div class="note-actions">
                            ${note.is_favorite ? '<i class="fas fa-heart favorite-icon active" title="Favorit"></i>' : ''}
                            ${note.is_pinned ? '<i class="fas fa-thumbtack pin-icon active" title="Angeheftet"></i>' : ''}
                            <div class="dropdown">
                                <button class="action-btn" onclick="this.parentElement.classList.toggle('open')">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-content">
                                    <a href="#" onclick="zettelkasten.editNote(${note.id})">
                                        <i class="fas fa-edit"></i> Bearbeiten
                                    </a>
                                    <a href="#" onclick="zettelkasten.duplicateNote(${note.id})">
                                        <i class="fas fa-copy"></i> Duplizieren
                                    </a>
                                    <a href="#" onclick="zettelkasten.toggleNotePin(${note.id})">
                                        <i class="fas fa-thumbtack"></i> ${note.is_pinned ? 'Lösen' : 'Anheften'}
                                    </a>
                                    <a href="#" onclick="zettelkasten.toggleNoteFavorite(${note.id})">
                                        <i class="fas fa-heart"></i> ${note.is_favorite ? 'Aus Favoriten' : 'Zu Favoriten'}
                                    </a>
                                    <a href="#" onclick="zettelkasten.showNoteLinks(${note.id})">
                                        <i class="fas fa-link"></i> Verknüpfungen
                                    </a>
                                    <a href="#" onclick="zettelkasten.deleteNote(${note.id})" class="delete-action">
                                        <i class="fas fa-trash"></i> Löschen
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h3 class="note-title" onclick="zettelkasten.editNote(${note.id})">
                        ${this.escapeHtml(note.title)}
                    </h3>
                </div>
                
                ${note.content ? `
                    <div class="note-content" onclick="zettelkasten.editNote(${note.id})">
                        ${this.truncateContent(note.content, 150)}
                    </div>
                ` : ''}
                
                <div class="note-footer">
                    <div class="note-meta-bottom">
                        ${wordCountBadge}
                        ${readingTimeBadge}
                        ${note.links_count > 0 ? `<span class="links-badge"><i class="fas fa-link"></i> ${note.links_count}</span>` : ''}
                    </div>
                    
                    <div class="note-date">
                        <i class="fas fa-calendar"></i> ${this.formatDate(note.updated_at)}
                    </div>
                    
                    ${note.tags && note.tags.length > 0 ? `
                        <div class="note-tags">
                            ${note.tags.slice(0, 3).map(tag => 
                                `<span class="tag" onclick="zettelkasten.filterByTag('${tag}')">#${this.escapeHtml(tag)}</span>`
                            ).join('')}
                            ${note.tags.length > 3 ? `<span class="tag-more">+${note.tags.length - 3}</span>` : ''}
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }
    
    truncateContent(content, maxLength) {
        const plainText = this.stripHtml(content);
        if (plainText.length <= maxLength) return this.escapeHtml(plainText);
        return this.escapeHtml(plainText.substring(0, maxLength)) + '...';
    }
    
    stripHtml(html) {
        const tmp = document.createElement('div');
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText || '';
    }
    
    filterByTag(tag) {
        const tagFilter = document.getElementById('tagFilter');
        if (tagFilter) {
            tagFilter.value = tag;
            this.activeFilters.tags = [tag];
            this.applyFilters();
        }
    }
    
    updateStatisticsDisplay() {
        if (!this.statistics) return;
        
        const statsElements = {
            totalNotes: document.getElementById('totalNotesCount'),
            totalWords: document.getElementById('totalWordsCount'),
            totalConnections: document.getElementById('totalConnectionsCount'),
            categoriesUsed: document.getElementById('categoriesUsedCount')
        };
        
        if (statsElements.totalNotes) {
            statsElements.totalNotes.textContent = this.statistics.total_notes || 0;
        }
        if (statsElements.totalWords) {
            statsElements.totalWords.textContent = this.formatNumber(this.statistics.total_words || 0);
        }
        if (statsElements.totalConnections) {
            statsElements.totalConnections.textContent = this.statistics.total_connections || 0;
        }
        if (statsElements.categoriesUsed) {
            statsElements.categoriesUsed.textContent = this.statistics.categories_used || 0;
        }
    }
    
    formatNumber(num) {
        if (num >= 1000000) {
            return (num / 1000000).toFixed(1) + 'M';
        } else if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K';
        }
        return num.toString();
    }
    
    focusSearch() {
        const searchInput = document.getElementById('notesSearch');
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
        }
    }
    
    closeAllModals() {
        document.querySelectorAll('.modal, .modal-overlay').forEach(modal => {
            modal.classList.remove('active', 'open');
        });
    }
    
    closeNoteEditor() {
        const modal = document.getElementById('noteEditorModal');
        if (modal) {
            modal.classList.remove('active');
        }
        this.currentNote = null;
        if (this.autoSaveTimeout) {
            clearTimeout(this.autoSaveTimeout);
        }
    }
    
    getActiveViewContainer() {
        switch (this.currentView) {
            case 'grid': return document.getElementById('notesGrid');
            case 'list': return document.querySelector('#listView .space-y-2');
            case 'node': return document.getElementById('nodeCanvas');
            default: return null;
        }
    }
    
    // Additional helper methods would continue here...
    // This is a comprehensive starting structure for the enhanced system
}

// Initialize the enhanced zettelkasten when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    if (typeof window.zettelkasten === 'undefined') {
        window.zettelkasten = new EnhancedZettelkasten();
    }
});

// Export for module use if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = EnhancedZettelkasten;
}
