/**
 * Enhanced Notes Manager - Advanced Client-Side Management
 * Provides comprehensive note management with modern features
 */
class EnhancedNotesManager {
    constructor() {
        this.notes = [];
        this.categories = [];
        this.templates = [];
        this.selectedNotes = new Set();
        this.currentView = 'grid';
        this.currentFilter = {
            search: '',
            category: null,
            type: 'all',
            archived: false,
            tags: []
        };
        this.sortBy = 'updated';
        this.sortOrder = 'desc';
        this.isLoading = false;
        this.autoSaveTimeout = null;
        this.searchTimeout = null;
        
        // Event listeners
        this.eventListeners = new Map();
        
        this.init();
    }
    
    async init() {
        this.setupEventListeners();
        this.setupKeyboardShortcuts();
        await this.loadInitialData();
        this.setupAutoSave();
        this.setupRealTimeFeatures();
    }
    
    /**
     * Event Management
     */
    on(event, callback) {
        if (!this.eventListeners.has(event)) {
            this.eventListeners.set(event, []);
        }
        this.eventListeners.get(event).push(callback);
    }
    
    emit(event, data) {
        if (this.eventListeners.has(event)) {
            this.eventListeners.get(event).forEach(callback => callback(data));
        }
    }
    
    /**
     * Data Loading and Management
     */
    async loadInitialData() {
        this.setLoading(true);
        try {
            await Promise.all([
                this.loadNotes(),
                this.loadCategories(),
                this.loadTemplates()
            ]);
            this.emit('dataLoaded', { notes: this.notes, categories: this.categories });
        } catch (error) {
            this.handleError('Failed to load initial data', error);
        } finally {
            this.setLoading(false);
        }
    }
    
    async loadNotes(options = {}) {
        const params = new URLSearchParams({
            archived: this.currentFilter.archived ? 'true' : 'false',
            limit: options.limit || 50,
            offset: options.offset || 0,
            sort: this.sortBy,
            order: this.sortOrder,
            include_links: 'true',
            include_stats: options.includeStats || 'false',
            ...this.currentFilter.search && { search: this.currentFilter.search },
            ...this.currentFilter.category && { category: this.currentFilter.category },
            ...this.currentFilter.type !== 'all' && { type: this.currentFilter.type },
            ...this.currentFilter.tags.length && { tags: this.currentFilter.tags.join(',') }
        });
        
        try {
            const response = await fetch(`/api/notes.php?${params}`);
            const data = await this.handleResponse(response);
            
            if (options.append) {
                this.notes = [...this.notes, ...data.notes];
            } else {
                this.notes = data.notes || [];
            }
            
            this.emit('notesLoaded', { 
                notes: this.notes, 
                pagination: data.pagination,
                stats: data.stats 
            });
            
            return data;
        } catch (error) {
            this.handleError('Failed to load notes', error);
            return { notes: [], pagination: null };
        }
    }
    
    async loadCategories() {
        try {
            const response = await fetch('/api/notes.php?action=categories');
            const data = await this.handleResponse(response);
            this.categories = data.categories || [];
            return this.categories;
        } catch (error) {
            this.handleError('Failed to load categories', error);
            return [];
        }
    }
    
    async loadTemplates() {
        try {
            const response = await fetch('/api/notes.php?action=templates');
            const data = await this.handleResponse(response);
            this.templates = data.templates || [];
            return this.templates;
        } catch (error) {
            this.handleError('Failed to load templates', error);
            return [];
        }
    }
    
    /**
     * Note CRUD Operations
     */
    async createNote(noteData) {
        this.setLoading(true);
        try {
            const response = await fetch('/api/notes.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    title: noteData.title,
                    content: noteData.content || '',
                    color: noteData.color || '#fbbf24',
                    type: noteData.type || 'note',
                    category_id: noteData.categoryId,
                    priority: noteData.priority || 'medium',
                    tags: noteData.tags || [],
                    reminder_date: noteData.reminderDate,
                    due_date: noteData.dueDate,
                    metadata: noteData.metadata
                })
            });
            
            const data = await this.handleResponse(response);
            
            if (data.success) {
                this.notes.unshift(data.note);
                this.emit('noteCreated', data.note);
                this.showNotification('Notiz erfolgreich erstellt', 'success');
                return data.note;
            }
        } catch (error) {
            this.handleError('Failed to create note', error);
            throw error;
        } finally {
            this.setLoading(false);
        }
    }
    
    async updateNote(noteId, updates) {
        try {
            const response = await fetch('/api/notes.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id: noteId,
                    ...updates
                })
            });
            
            const data = await this.handleResponse(response);
            
            if (data.success) {
                const index = this.notes.findIndex(n => n.id == noteId);
                if (index !== -1) {
                    this.notes[index] = data.note;
                }
                this.emit('noteUpdated', data.note);
                this.showNotification('Notiz gespeichert', 'success');
                return data.note;
            }
        } catch (error) {
            this.handleError('Failed to update note', error);
            throw error;
        }
    }
    
    async patchNote(noteId, patches) {
        try {
            const response = await fetch('/api/notes.php', {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id: noteId,
                    ...patches
                })
            });
            
            const data = await this.handleResponse(response);
            
            if (data.success) {
                const note = this.notes.find(n => n.id == noteId);
                if (note) {
                    Object.assign(note, patches);
                }
                this.emit('notePatched', { noteId, patches });
                return true;
            }
        } catch (error) {
            this.handleError('Failed to patch note', error);
            return false;
        }
    }
    
    async deleteNote(noteId) {
        try {
            const response = await fetch(`/api/notes.php?id=${noteId}`, {
                method: 'DELETE'
            });
            
            const data = await this.handleResponse(response);
            
            if (data.success) {
                this.notes = this.notes.filter(n => n.id != noteId);
                this.selectedNotes.delete(noteId);
                this.emit('noteDeleted', noteId);
                this.showNotification('Notiz in Papierkorb verschoben', 'info');
                return true;
            }
        } catch (error) {
            this.handleError('Failed to delete note', error);
            return false;
        }
    }
    
    async duplicateNote(noteId, newTitle) {
        try {
            const response = await fetch('/api/notes.php?action=duplicate', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    note_id: noteId,
                    new_title: newTitle
                })
            });
            
            const data = await this.handleResponse(response);
            
            if (data.success) {
                await this.loadNotes(); // Reload to get the new note
                this.emit('noteDuplicated', data.duplicate_id);
                this.showNotification('Notiz erfolgreich dupliziert', 'success');
                return data.duplicate_id;
            }
        } catch (error) {
            this.handleError('Failed to duplicate note', error);
            return null;
        }
    }
    
    /**
     * Search and Filtering
     */
    async performSearch(query, options = {}) {
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }
        
        return new Promise((resolve) => {
            this.searchTimeout = setTimeout(async () => {
                try {
                    const params = new URLSearchParams({
                        q: query,
                        type: options.type || 'all',
                        limit: options.limit || 20
                    });
                    
                    const response = await fetch(`/api/notes.php?action=search&${params}`);
                    const data = await this.handleResponse(response);
                    
                    this.emit('searchCompleted', data);
                    resolve(data.results || []);
                } catch (error) {
                    this.handleError('Search failed', error);
                    resolve([]);
                }
            }, 300); // Debounce search
        });
    }
    
    setFilter(filterKey, value) {
        this.currentFilter[filterKey] = value;
        this.emit('filterChanged', { key: filterKey, value });
        this.loadNotes();
    }
    
    setSorting(sortBy, sortOrder = 'desc') {
        this.sortBy = sortBy;
        this.sortOrder = sortOrder;
        this.emit('sortingChanged', { sortBy, sortOrder });
        this.loadNotes();
    }
    
    /**
     * Bulk Operations
     */
    async performBulkOperation(operation, noteIds, options = {}) {
        if (!noteIds || noteIds.length === 0) {
            this.showNotification('Keine Notizen ausgewählt', 'warning');
            return false;
        }
        
        try {
            const response = await fetch('/api/notes.php?action=bulk', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    operation,
                    note_ids: noteIds,
                    ...options
                })
            });
            
            const data = await this.handleResponse(response);
            
            if (data.success) {
                // Update local notes
                noteIds.forEach(noteId => {
                    const note = this.notes.find(n => n.id == noteId);
                    if (note) {
                        switch (operation) {
                            case 'archive':
                                note.is_archived = true;
                                break;
                            case 'unarchive':
                                note.is_archived = false;
                                break;
                            case 'pin':
                                note.is_pinned = true;
                                break;
                            case 'unpin':
                                note.is_pinned = false;
                                break;
                            case 'favorite':
                                note.is_favorite = true;
                                break;
                            case 'unfavorite':
                                note.is_favorite = false;
                                break;
                            case 'delete':
                                this.notes = this.notes.filter(n => n.id != noteId);
                                break;
                            case 'category':
                                note.category_id = options.category_id;
                                break;
                        }
                    }
                });
                
                this.selectedNotes.clear();
                this.emit('bulkOperationCompleted', { operation, noteIds, count: data.affected_rows });
                this.showNotification(`${data.affected_rows} Notizen ${operation}`, 'success');
                return true;
            }
        } catch (error) {
            this.handleError(`Bulk ${operation} failed`, error);
            return false;
        }
    }
    
    /**
     * Note Linking
     */
    async createNoteLink(sourceId, targetId, linkType = 'reference', description = '') {
        try {
            const response = await fetch('/api/notes.php?action=link', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    source_note_id: sourceId,
                    target_note_id: targetId,
                    link_type: linkType,
                    description: description
                })
            });
            
            const data = await this.handleResponse(response);
            
            if (data.success) {
                this.emit('linkCreated', { sourceId, targetId, linkType });
                this.showNotification('Notizen erfolgreich verknüpft', 'success');
                return data.link_id;
            }
        } catch (error) {
            this.handleError('Failed to create link', error);
            return null;
        }
    }
    
    async loadNoteLinks(noteId) {
        try {
            const response = await fetch(`/api/notes.php?action=links&note_id=${noteId}`);
            const data = await this.handleResponse(response);
            return data.links || [];
        } catch (error) {
            this.handleError('Failed to load links', error);
            return [];
        }
    }
    
    async loadBacklinks(noteId) {
        try {
            const response = await fetch(`/api/notes.php?action=backlinks&note_id=${noteId}`);
            const data = await this.handleResponse(response);
            return data.backlinks || [];
        } catch (error) {
            this.handleError('Failed to load backlinks', error);
            return [];
        }
    }
    
    /**
     * Selection Management
     */
    toggleNoteSelection(noteId) {
        if (this.selectedNotes.has(noteId)) {
            this.selectedNotes.delete(noteId);
        } else {
            this.selectedNotes.add(noteId);
        }
        this.emit('selectionChanged', Array.from(this.selectedNotes));
    }
    
    selectAllVisibleNotes() {
        this.notes.forEach(note => this.selectedNotes.add(note.id));
        this.emit('selectionChanged', Array.from(this.selectedNotes));
    }
    
    clearSelection() {
        this.selectedNotes.clear();
        this.emit('selectionChanged', []);
    }
    
    /**
     * Auto-save and Real-time Features
     */
    setupAutoSave() {
        this.on('noteContentChanged', (data) => {
            if (this.autoSaveTimeout) {
                clearTimeout(this.autoSaveTimeout);
            }
            
            this.autoSaveTimeout = setTimeout(() => {
                this.updateNote(data.noteId, {
                    title: data.title,
                    content: data.content
                });
            }, 2000); // Auto-save after 2 seconds of inactivity
        });
    }
    
    setupRealTimeFeatures() {
        // Periodically sync with server for collaborative features
        setInterval(() => {
            this.syncWithServer();
        }, 30000); // Sync every 30 seconds
    }
    
    async syncWithServer() {
        try {
            const lastSync = localStorage.getItem('lastNotesSync');
            const params = new URLSearchParams({
                since: lastSync || new Date(Date.now() - 24 * 60 * 60 * 1000).toISOString()
            });
            
            const response = await fetch(`/api/notes.php?action=sync&${params}`);
            const data = await this.handleResponse(response);
            
            if (data.changes && data.changes.length > 0) {
                this.handleServerChanges(data.changes);
                localStorage.setItem('lastNotesSync', new Date().toISOString());
            }
        } catch (error) {
            console.log('Sync failed (normal if offline):', error.message);
        }
    }
    
    handleServerChanges(changes) {
        changes.forEach(change => {
            switch (change.type) {
                case 'note_updated':
                    const index = this.notes.findIndex(n => n.id === change.note_id);
                    if (index !== -1) {
                        this.notes[index] = { ...this.notes[index], ...change.data };
                        this.emit('noteUpdatedFromServer', change.data);
                    }
                    break;
                case 'note_deleted':
                    this.notes = this.notes.filter(n => n.id !== change.note_id);
                    this.emit('noteDeletedFromServer', change.note_id);
                    break;
            }
        });
    }
    
    /**
     * Keyboard Shortcuts
     */
    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + N: New note
            if ((e.ctrlKey || e.metaKey) && e.key === 'n' && !e.shiftKey) {
                e.preventDefault();
                this.emit('shortcutNewNote');
            }
            
            // Ctrl/Cmd + Shift + N: New note from template
            if ((e.ctrlKey || e.metaKey) && e.key === 'N' && e.shiftKey) {
                e.preventDefault();
                this.emit('shortcutNewNoteFromTemplate');
            }
            
            // Ctrl/Cmd + F: Focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                this.emit('shortcutFocusSearch');
            }
            
            // Ctrl/Cmd + A: Select all notes
            if ((e.ctrlKey || e.metaKey) && e.key === 'a' && this.isNotesViewActive()) {
                e.preventDefault();
                this.selectAllVisibleNotes();
            }
            
            // Delete: Delete selected notes
            if (e.key === 'Delete' && this.selectedNotes.size > 0) {
                e.preventDefault();
                this.emit('shortcutDeleteSelected');
            }
            
            // Escape: Clear selection
            if (e.key === 'Escape') {
                this.clearSelection();
            }
        });
    }
    
    /**
     * Import/Export Features
     */
    async exportNotes(format = 'json', options = {}) {
        try {
            const params = new URLSearchParams({
                format,
                ...options.noteIds && { note_ids: options.noteIds.join(',') },
                ...options.includeArchived && { include_archived: 'true' }
            });
            
            const response = await fetch(`/api/notes.php?action=export&${params}`);
            
            if (format === 'json') {
                const data = await response.json();
                this.downloadFile(JSON.stringify(data, null, 2), 'notes.json', 'application/json');
            } else {
                const blob = await response.blob();
                this.downloadFile(blob, `notes.${format}`, response.headers.get('content-type'));
            }
            
            this.showNotification('Export erfolgreich', 'success');
        } catch (error) {
            this.handleError('Export failed', error);
        }
    }
    
    async importNotes(file) {
        try {
            const formData = new FormData();
            formData.append('file', file);
            
            const response = await fetch('/api/notes.php?action=import', {
                method: 'POST',
                body: formData
            });
            
            const data = await this.handleResponse(response);
            
            if (data.success) {
                await this.loadNotes(); // Reload notes
                this.showNotification(`${data.imported_count} Notizen importiert`, 'success');
                return data.imported_count;
            }
        } catch (error) {
            this.handleError('Import failed', error);
            return 0;
        }
    }
    
    /**
     * Utility Methods
     */
    async handleResponse(response) {
        if (!response.ok) {
            const error = await response.json().catch(() => ({ error: 'Network error' }));
            throw new Error(error.error || `HTTP ${response.status}`);
        }
        return await response.json();
    }
    
    setLoading(loading) {
        this.isLoading = loading;
        this.emit('loadingStateChanged', loading);
    }
    
    handleError(message, error) {
        console.error(message, error);
        this.emit('error', { message, error });
        this.showNotification(message, 'error');
    }
    
    showNotification(message, type = 'info') {
        this.emit('notification', { message, type });
    }
    
    downloadFile(content, filename, mimeType) {
        const blob = content instanceof Blob ? content : new Blob([content], { type: mimeType });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }
    
    isNotesViewActive() {
        return document.querySelector('.notes-container:focus, .notes-grid:focus') !== null;
    }
    
    getNote(noteId) {
        return this.notes.find(note => note.id == noteId);
    }
    
    getNotesByCategory(categoryId) {
        return this.notes.filter(note => note.category_id == categoryId);
    }
    
    getNotesByTag(tag) {
        return this.notes.filter(note => note.tags && note.tags.includes(tag));
    }
    
    getAllTags() {
        const tags = new Set();
        this.notes.forEach(note => {
            if (note.tags) {
                note.tags.forEach(tag => tags.add(tag));
            }
        });
        return Array.from(tags).sort();
    }
    
    /**
     * Statistics and Analytics
     */
    getNotesStats() {
        const stats = {
            total: this.notes.length,
            pinned: this.notes.filter(n => n.is_pinned).length,
            archived: this.notes.filter(n => n.is_archived).length,
            favorites: this.notes.filter(n => n.is_favorite).length,
            byType: {},
            byCategory: {},
            totalWords: 0,
            averageWords: 0
        };
        
        this.notes.forEach(note => {
            // Count by type
            stats.byType[note.note_type] = (stats.byType[note.note_type] || 0) + 1;
            
            // Count by category
            const category = note.category_name || 'Uncategorized';
            stats.byCategory[category] = (stats.byCategory[category] || 0) + 1;
            
            // Word count
            stats.totalWords += note.word_count || 0;
        });
        
        stats.averageWords = stats.total > 0 ? Math.round(stats.totalWords / stats.total) : 0;
        
        return stats;
    }
}

// Global instance
window.notesManager = new EnhancedNotesManager();

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = EnhancedNotesManager;
}
