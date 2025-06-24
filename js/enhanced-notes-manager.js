/**
 * Enhanced Notes Manager for Second Brain functionality
 * Handles note creation, editing, tagging, and search
 */

class EnhancedNotesManager {
    constructor() {
        this.notes = new Map();
        this.tags = new Set();
        this.selectedNote = null;
        this.isEditMode = false;
        this.searchQuery = '';
        this.activeFilters = new Set();
        
        this.initializeUI();
        this.setupEventListeners();
        this.loadNotes();
    }

    initializeUI() {
        this.setupNotesGrid();
        this.setupNoteEditor();
        this.setupSearchAndFilters();
        this.setupTagManager();
        this.setupKeyboardShortcuts();
    }

    setupNotesGrid() {
        // Create notes grid container if it doesn't exist
        if (!document.getElementById('notes-grid')) {
            const notesContainer = document.querySelector('.notes-container') || document.body;
            const gridHTML = `
                <div id="notes-grid" class="notes-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <!-- Notes will be populated here -->
                </div>
            `;
            notesContainer.insertAdjacentHTML('beforeend', gridHTML);
        }
        this.notesGrid = document.getElementById('notes-grid');
    }

    setupNoteEditor() {
        // Enhanced note editor modal
        const editorHTML = `
            <div id="note-editor-modal" class="note-editor-modal fixed inset-0 bg-black bg-opacity-50 hidden z-50">
                <div class="note-editor-content bg-gray-900 rounded-lg shadow-2xl max-w-4xl mx-auto mt-8 max-h-[90vh] overflow-hidden">
                    <!-- Editor Header -->
                    <div class="note-editor-header flex items-center justify-between p-4 border-b border-gray-700">
                        <div class="flex items-center space-x-3">
                            <button id="note-pin-btn" class="note-action-btn" title="Pin note">
                                <i class="fas fa-thumbtack"></i>
                            </button>
                            <button id="note-color-picker" class="note-action-btn" title="Change color">
                                <i class="fas fa-palette"></i>
                            </button>
                            <button id="note-link-btn" class="note-action-btn" title="Create link">
                                <i class="fas fa-link"></i>
                            </button>
                            <button id="note-reminder-btn" class="note-action-btn" title="Set reminder">
                                <i class="fas fa-bell"></i>
                            </button>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span id="note-word-count" class="text-sm text-gray-400">0 words</span>
                            <button id="note-save-btn" class="notes-btn-primary">
                                <i class="fas fa-save mr-2"></i>Save
                            </button>
                            <button id="note-close-btn" class="notes-btn-secondary">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Editor Body -->
                    <div class="note-editor-body p-4 overflow-y-auto" style="max-height: calc(90vh - 160px);">
                        <!-- Title -->
                        <input type="text" 
                               id="note-title-input" 
                               placeholder="Note title..." 
                               class="w-full text-2xl font-bold bg-transparent border-none outline-none text-white mb-4 placeholder-gray-400">
                        
                        <!-- Content -->
                        <textarea id="note-content-input" 
                                  placeholder="Start writing your note... Use [[Note Title]] to link to other notes."
                                  class="w-full h-64 bg-transparent border-none outline-none text-white resize-none placeholder-gray-400"
                                  style="min-height: 300px;"></textarea>
                        
                        <!-- Tags -->
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Tags</label>
                            <div id="note-tags-container" class="flex flex-wrap gap-2 mb-2">
                                <!-- Tags will be added here -->
                            </div>
                            <input type="text" 
                                   id="note-tags-input" 
                                   placeholder="Add tags (separated by commas)..."
                                   class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400">
                        </div>

                        <!-- Backlinks -->
                        <div id="note-backlinks-section" class="mt-6 hidden">
                            <h4 class="text-lg font-semibold text-white mb-3">Linked Notes</h4>
                            <div id="note-backlinks-list" class="space-y-2">
                                <!-- Backlinks will be populated here -->
                            </div>
                        </div>
                    </div>

                    <!-- Color Picker -->
                    <div id="color-picker-dropdown" class="hidden absolute top-16 left-4 bg-gray-800 rounded-lg p-3 shadow-xl border border-gray-600">
                        <div class="grid grid-cols-6 gap-2">
                            <button class="note-color-btn w-8 h-8 rounded-full" data-color="#fbbf24" style="background: #fbbf24;"></button>
                            <button class="note-color-btn w-8 h-8 rounded-full" data-color="#f59e0b" style="background: #f59e0b;"></button>
                            <button class="note-color-btn w-8 h-8 rounded-full" data-color="#ef4444" style="background: #ef4444;"></button>
                            <button class="note-color-btn w-8 h-8 rounded-full" data-color="#22c55e" style="background: #22c55e;"></button>
                            <button class="note-color-btn w-8 h-8 rounded-full" data-color="#3b82f6" style="background: #3b82f6;"></button>
                            <button class="note-color-btn w-8 h-8 rounded-full" data-color="#8b5cf6" style="background: #8b5cf6;"></button>
                            <button class="note-color-btn w-8 h-8 rounded-full" data-color="#ec4899" style="background: #ec4899;"></button>
                            <button class="note-color-btn w-8 h-8 rounded-full" data-color="#06b6d4" style="background: #06b6d4;"></button>
                            <button class="note-color-btn w-8 h-8 rounded-full" data-color="#84cc16" style="background: #84cc16;"></button>
                            <button class="note-color-btn w-8 h-8 rounded-full" data-color="#f97316" style="background: #f97316;"></button>
                            <button class="note-color-btn w-8 h-8 rounded-full" data-color="#6b7280" style="background: #6b7280;"></button>
                            <button class="note-color-btn w-8 h-8 rounded-full" data-color="#1f2937" style="background: #1f2937;"></button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', editorHTML);
        this.noteEditor = document.getElementById('note-editor-modal');
    }

    setupSearchAndFilters() {
        // Enhanced search and filter UI
        const searchHTML = `
            <div id="notes-search-filters" class="notes-search-filters mb-6">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Search -->
                    <div class="flex-1 relative">
                        <input type="text" 
                               id="notes-search-input" 
                               placeholder="Search notes, tags, content..." 
                               class="w-full px-4 py-3 pl-12 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <i class="fas fa-search absolute left-4 top-4 text-gray-400"></i>
                        <button id="clear-search-btn" class="absolute right-3 top-3 text-gray-400 hover:text-white hidden">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <!-- View Toggle -->
                    <div class="view-toggle-buttons flex bg-gray-800 rounded-lg p-1">
                        <button id="grid-view-btn" class="view-toggle-btn active px-3 py-2 rounded">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button id="list-view-btn" class="view-toggle-btn px-3 py-2 rounded">
                            <i class="fas fa-list"></i>
                        </button>
                        <button id="graph-view-btn" class="view-toggle-btn px-3 py-2 rounded">
                            <i class="fas fa-project-diagram"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Filters -->
                <div id="notes-filters" class="flex flex-wrap gap-2 mt-4">
                    <button id="show-all-notes" class="filter-btn active">All Notes</button>
                    <button id="show-pinned-notes" class="filter-btn">Pinned</button>
                    <button id="show-recent-notes" class="filter-btn">Recent</button>
                    <button id="show-unlinked-notes" class="filter-btn">Unlinked</button>
                </div>
                
                <!-- Tag Cloud -->
                <div id="tag-cloud" class="mt-4 hidden">
                    <h4 class="text-sm font-medium text-gray-300 mb-2">Filter by Tags:</h4>
                    <div id="tag-cloud-container" class="flex flex-wrap gap-2">
                        <!-- Tags will be populated here -->
                    </div>
                </div>
            </div>
        `;
        
        const container = document.querySelector('.notes-container') || this.notesGrid.parentNode;
        container.insertAdjacentHTML('afterbegin', searchHTML);
    }

    setupTagManager() {
        this.tagInput = document.getElementById('note-tags-input');
        this.tagsContainer = document.getElementById('note-tags-container');
        
        if (this.tagInput) {
            this.tagInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ',') {
                    e.preventDefault();
                    this.addTag(this.tagInput.value.trim());
                    this.tagInput.value = '';
                }
            });
        }
    }

    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + N: New note
            if ((e.ctrlKey || e.metaKey) && e.key === 'n' && !e.target.matches('input, textarea')) {
                e.preventDefault();
                this.openNoteEditor();
            }
            
            // Ctrl/Cmd + K: Search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                document.getElementById('notes-search-input')?.focus();
            }
            
            // Escape: Close editor
            if (e.key === 'Escape' && this.isEditMode) {
                this.closeNoteEditor();
            }
            
            // Ctrl/Cmd + S: Save note
            if ((e.ctrlKey || e.metaKey) && e.key === 's' && this.isEditMode) {
                e.preventDefault();
                this.saveNote();
            }
            
            // Ctrl/Cmd + L: Create link
            if ((e.ctrlKey || e.metaKey) && e.key === 'l' && this.isEditMode) {
                e.preventDefault();
                this.startLinkCreation();
            }
        });
    }

    setupEventListeners() {
        // Note editor events
        document.getElementById('note-close-btn')?.addEventListener('click', () => this.closeNoteEditor());
        document.getElementById('note-save-btn')?.addEventListener('click', () => this.saveNote());
        document.getElementById('note-pin-btn')?.addEventListener('click', () => this.togglePin());
        document.getElementById('note-color-picker')?.addEventListener('click', () => this.toggleColorPicker());
        document.getElementById('note-link-btn')?.addEventListener('click', () => this.startLinkCreation());
        
        // Color picker
        document.querySelectorAll('.note-color-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.setNoteColor(e.target.dataset.color);
                this.hideColorPicker();
            });
        });
        
        // Search and filters
        document.getElementById('notes-search-input')?.addEventListener('input', (e) => {
            this.searchNotes(e.target.value);
        });
        
        // View toggles
        document.getElementById('grid-view-btn')?.addEventListener('click', () => this.setView('grid'));
        document.getElementById('list-view-btn')?.addEventListener('click', () => this.setView('list'));
        document.getElementById('graph-view-btn')?.addEventListener('click', () => this.setView('graph'));
        
        // Filter buttons
        document.getElementById('show-all-notes')?.addEventListener('click', () => this.filterNotes('all'));
        document.getElementById('show-pinned-notes')?.addEventListener('click', () => this.filterNotes('pinned'));
        document.getElementById('show-recent-notes')?.addEventListener('click', () => this.filterNotes('recent'));
        document.getElementById('show-unlinked-notes')?.addEventListener('click', () => this.filterNotes('unlinked'));
        
        // Content input for word count and auto-linking
        document.getElementById('note-content-input')?.addEventListener('input', (e) => {
            this.updateWordCount();
            this.detectNoteLinks(e.target.value);
        });
    }

    async loadNotes() {
        try {
            const response = await fetch('/src/api/notes.php?archived=false&limit=100');
            if (!response.ok) throw new Error('Failed to load notes');
            
            const data = await response.json();
            this.processNotes(data.notes || []);
            this.renderNotes();
            this.updateTagCloud();
            
        } catch (error) {
            console.error('Error loading notes:', error);
            this.showError('Failed to load notes');
        }
    }

    processNotes(notesArray) {
        this.notes.clear();
        this.tags.clear();
        
        notesArray.forEach(note => {
            this.notes.set(note.id, note);
            note.tags.forEach(tag => this.tags.add(tag));
        });
    }

    renderNotes() {
        if (!this.notesGrid) return;
        
        const filteredNotes = this.getFilteredNotes();
        
        this.notesGrid.innerHTML = filteredNotes.map(note => this.createNoteCard(note)).join('');
        
        // Add event listeners to note cards
        this.notesGrid.querySelectorAll('.note-card').forEach(card => {
            const noteId = parseInt(card.dataset.noteId);
            card.addEventListener('click', () => this.openNote(noteId));
            
            // Add context menu
            card.addEventListener('contextmenu', (e) => {
                e.preventDefault();
                this.showNoteContextMenu(e, noteId);
            });
        });
    }

    createNoteCard(note) {
        const preview = this.getContentPreview(note.content);
        const tagsList = note.tags.map(tag => `<span class="note-tag">${tag}</span>`).join('');
        
        return `
            <div class="note-card" data-note-id="${note.id}" style="background: linear-gradient(135deg, ${note.color}20, ${note.color}10);">
                <div class="note-card-header">
                    <h3 class="note-title">${this.escapeHtml(note.title)}</h3>
                    <div class="note-actions">
                        ${note.is_pinned ? '<i class="fas fa-thumbtack text-yellow-400"></i>' : ''}
                        <button class="note-action-btn" onclick="event.stopPropagation(); notesManager.deleteNote(${note.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="note-content">
                    <p class="line-clamp-3">${preview}</p>
                </div>
                <div class="note-footer">
                    <div class="note-tags">${tagsList}</div>
                    <div class="note-meta">
                        <span class="text-xs text-gray-400">${this.formatDate(note.updated_at)}</span>
                    </div>
                </div>
            </div>
        `;
    }

    getFilteredNotes() {
        let filtered = Array.from(this.notes.values());
        
        // Apply search filter
        if (this.searchQuery) {
            const query = this.searchQuery.toLowerCase();
            filtered = filtered.filter(note => 
                note.title.toLowerCase().includes(query) ||
                note.content.toLowerCase().includes(query) ||
                note.tags.some(tag => tag.toLowerCase().includes(query))
            );
        }
        
        // Apply active filters
        this.activeFilters.forEach(filter => {
            switch (filter) {
                case 'pinned':
                    filtered = filtered.filter(note => note.is_pinned);
                    break;
                case 'recent':
                    const weekAgo = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000);
                    filtered = filtered.filter(note => new Date(note.created_at) > weekAgo);
                    break;
                case 'unlinked':
                    // This would require link data - implement after loading link information
                    break;
            }
        });
        
        return filtered.sort((a, b) => {
            if (a.is_pinned && !b.is_pinned) return -1;
            if (!a.is_pinned && b.is_pinned) return 1;
            return new Date(b.updated_at) - new Date(a.updated_at);
        });
    }

    openNoteEditor(noteId = null) {
        this.isEditMode = true;
        this.selectedNote = noteId ? this.notes.get(noteId) : null;
        
        // Populate editor
        if (this.selectedNote) {
            document.getElementById('note-title-input').value = this.selectedNote.title;
            document.getElementById('note-content-input').value = this.selectedNote.content;
            this.renderNoteTags(this.selectedNote.tags);
            this.loadNoteBacklinks(this.selectedNote.id);
        } else {
            document.getElementById('note-title-input').value = '';
            document.getElementById('note-content-input').value = '';
            this.renderNoteTags([]);
        }
        
        this.noteEditor.classList.remove('hidden');
        this.noteEditor.classList.add('active');
        document.getElementById('note-title-input').focus();
        this.updateWordCount();
    }

    openNote(noteId) {
        this.openNoteEditor(noteId);
    }

    closeNoteEditor() {
        this.isEditMode = false;
        this.selectedNote = null;
        this.noteEditor.classList.remove('active');
        this.noteEditor.classList.add('hidden');
        this.hideColorPicker();
    }

    async saveNote() {
        const title = document.getElementById('note-title-input').value.trim();
        const content = document.getElementById('note-content-input').value.trim();
        const tags = this.getCurrentTags();
        
        if (!title) {
            this.showError('Title is required');
            return;
        }
        
        try {
            const method = this.selectedNote ? 'PUT' : 'POST';
            const body = {
                title,
                content,
                tags,
                color: this.getCurrentColor()
            };
            
            if (this.selectedNote) {
                body.id = this.selectedNote.id;
            }
            
            const response = await fetch('/src/api/notes.php', {
                method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(body)
            });
            
            if (!response.ok) throw new Error('Failed to save note');
            
            const result = await response.json();
            this.showSuccess(this.selectedNote ? 'Note updated' : 'Note created');
            this.closeNoteEditor();
            this.loadNotes(); // Refresh notes
            
        } catch (error) {
            console.error('Error saving note:', error);
            this.showError('Failed to save note');
        }
    }

    async deleteNote(noteId) {
        if (!confirm('Are you sure you want to delete this note?')) return;
        
        try {
            const response = await fetch(`/src/api/notes.php?id=${noteId}`, {
                method: 'DELETE'
            });
            
            if (!response.ok) throw new Error('Failed to delete note');
            
            this.notes.delete(noteId);
            this.renderNotes();
            this.showSuccess('Note deleted');
            
        } catch (error) {
            console.error('Error deleting note:', error);
            this.showError('Failed to delete note');
        }
    }

    // Search and filter methods
    searchNotes(query) {
        this.searchQuery = query;
        this.renderNotes();
        
        const clearBtn = document.getElementById('clear-search-btn');
        if (clearBtn) {
            clearBtn.classList.toggle('hidden', !query);
        }
    }

    filterNotes(filterType) {
        // Update active filter buttons
        document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
        
        if (filterType === 'all') {
            this.activeFilters.clear();
            document.getElementById('show-all-notes').classList.add('active');
        } else {
            this.activeFilters.clear();
            this.activeFilters.add(filterType);
            document.getElementById(`show-${filterType}-notes`).classList.add('active');
        }
        
        this.renderNotes();
    }

    // Tag management
    addTag(tagName) {
        if (!tagName || this.getCurrentTags().includes(tagName)) return;
        
        const tagElement = document.createElement('span');
        tagElement.className = 'note-tag bg-blue-600 text-white px-2 py-1 rounded text-sm flex items-center gap-1';
        tagElement.innerHTML = `
            ${this.escapeHtml(tagName)}
            <button onclick="this.parentElement.remove()" class="text-blue-200 hover:text-white">
                <i class="fas fa-times text-xs"></i>
            </button>
        `;
        
        this.tagsContainer.appendChild(tagElement);
        this.tags.add(tagName);
    }

    renderNoteTags(tags) {
        this.tagsContainer.innerHTML = '';
        tags.forEach(tag => this.addTag(tag));
    }

    getCurrentTags() {
        return Array.from(this.tagsContainer.querySelectorAll('.note-tag'))
            .map(tag => tag.textContent.trim());
    }

    getCurrentColor() {
        return document.querySelector('.note-color-btn.active')?.dataset.color || '#fbbf24';
    }

    // UI helper methods
    updateWordCount() {
        const content = document.getElementById('note-content-input')?.value || '';
        const wordCount = content.trim() ? content.trim().split(/\s+/).length : 0;
        const counter = document.getElementById('note-word-count');
        if (counter) {
            counter.textContent = `${wordCount} word${wordCount !== 1 ? 's' : ''}`;
        }
    }

    detectNoteLinks(content) {
        // Detect [[Note Title]] patterns for auto-linking
        const linkPattern = /\[\[([^\]]+)\]\]/g;
        const matches = [...content.matchAll(linkPattern)];
        
        // Could implement auto-suggestion or validation here
        matches.forEach(match => {
            const linkedTitle = match[1];
            // Check if note with this title exists
            const existingNote = Array.from(this.notes.values())
                .find(note => note.title.toLowerCase() === linkedTitle.toLowerCase());
            
            if (!existingNote) {
                // Could show suggestion to create new note
            }
        });
    }

    setView(viewType) {
        document.querySelectorAll('.view-toggle-btn').forEach(btn => btn.classList.remove('active'));
        document.getElementById(`${viewType}-view-btn`).classList.add('active');
        
        // Implement view changes
        switch (viewType) {
            case 'grid':
                this.notesGrid.className = 'notes-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4';
                break;
            case 'list':
                this.notesGrid.className = 'notes-grid space-y-2';
                break;
            case 'graph':
                // Show graph view - integrate with SecondBrain class
                this.showGraphView();
                break;
        }
    }

    showGraphView() {
        // Toggle to graph view
        if (window.secondBrain) {
            document.getElementById('notes-grid').style.display = 'none';
            document.getElementById('graph-container').style.display = 'block';
        }
    }

    // Utility methods
    getContentPreview(content, maxLength = 150) {
        if (!content) return 'No content';
        return content.length > maxLength ? content.substring(0, maxLength) + '...' : content;
    }

    formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    showError(message) {
        this.showNotification(message, 'error');
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-600' : 'bg-blue-600';
        notification.className = `fixed top-4 right-4 ${bgColor} text-white px-4 py-2 rounded-lg shadow-lg z-50 transform transition-transform duration-300`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.notesManager = new EnhancedNotesManager();
});
