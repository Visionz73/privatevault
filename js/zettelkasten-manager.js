// Enhanced Notes Manager with Zettelkasten features
class ZettelkastenManager {
    constructor() {
        this.notes = [];
        this.links = [];
        this.graphNodes = [];
        this.selectedNotes = new Set();
        this.currentView = 'grid';
        this.searchQuery = '';
        this.isGraphInitialized = false;
        
        this.init();
    }
    
    async init() {
        await this.loadNotes();
        this.setupEventListeners();
        this.initializeGraph();
    }
    
    async loadNotes() {
        try {
            const response = await fetch('/api/notes.php?limit=100');
            const data = await response.json();
            
            if (data.success) {
                this.notes = data.notes;
                this.updateNotesDisplay();
                
                if (this.currentView === 'node') {
                    await this.loadGraphData();
                }
            }
        } catch (error) {
            console.error('Error loading notes:', error);
        }
    }
    
    async loadGraphData() {
        try {
            const response = await fetch('/api/notes.php?action=graph');
            const data = await response.json();
            
            if (data.success) {
                this.graphNodes = data.nodes;
                this.links = data.links;
                this.updateGraphView();
            }
        } catch (error) {
            console.error('Error loading graph data:', error);
        }
    }
    
    setupEventListeners() {
        // Search functionality
        const searchInput = document.getElementById('notesSearch');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.searchQuery = e.target.value;
                this.filterNotes();
            });
        }
        
        // View toggle buttons
        document.querySelectorAll('.view-toggle-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const view = e.target.dataset.view || e.target.closest('button').dataset.view;
                this.switchView(view);
            });
        });
    }
    
    switchView(view) {
        this.currentView = view;
        
        // Update button states
        document.querySelectorAll('.view-toggle-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`[data-view="${view}"]`).classList.add('active');
        
        // Show/hide views
        document.getElementById('notesGrid').style.display = view === 'grid' ? 'block' : 'none';
        document.getElementById('nodeView').style.display = view === 'node' ? 'block' : 'none';
        document.getElementById('listView').style.display = view === 'list' ? 'block' : 'none';
        
        if (view === 'node' && !this.isGraphInitialized) {
            this.initializeGraph();
        }
        
        this.updateNotesDisplay();
    }
    
    filterNotes() {
        const filteredNotes = this.notes.filter(note => {
            const matchesSearch = !this.searchQuery || 
                note.title.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                (note.content && note.content.toLowerCase().includes(this.searchQuery.toLowerCase()));
            
            return matchesSearch;
        });
        
        this.updateNotesDisplay(filteredNotes);
    }
    
    updateNotesDisplay(notes = this.notes) {
        switch (this.currentView) {
            case 'grid':
                this.updateGridView(notes);
                break;
            case 'node':
                this.updateGraphView(notes);
                break;
            case 'list':
                this.updateListView(notes);
                break;
        }
    }
    
    updateGridView(notes) {
        const container = document.getElementById('notesGrid');
        if (!container) return;
        
        if (notes.length === 0) {
            container.innerHTML = `
                <div class="col-span-full text-center py-12 text-white/60">
                    <i class="fas fa-sticky-note text-4xl mb-4"></i>
                    <p>Keine Notizen gefunden</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = notes.map(note => `
            <div class="note-card" style="border-left: 4px solid ${note.color}" data-note-id="${note.id}">
                <div class="note-card-header">
                    <h3 class="note-title">${this.escapeHtml(note.title)}</h3>
                    <div class="note-actions">
                        ${note.is_pinned ? '<i class="fas fa-thumbtack text-yellow-400"></i>' : ''}
                        ${note.links_count > 0 ? `<span class="text-xs bg-blue-500/20 text-blue-300 px-2 py-1 rounded">${note.links_count} <i class="fas fa-link"></i></span>` : ''}
                        <button onclick="zettelkasten.editNote(${note.id})" class="note-action-btn">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </div>
                ${note.content ? `<div class="note-content">${this.escapeHtml(note.content)}</div>` : ''}
                <div class="note-footer">
                    <span class="note-date">${this.formatDate(note.updated_at)}</span>
                    ${note.tags && note.tags.length > 0 ? `<div class="note-tags">${note.tags.map(tag => `<span class="note-tag">${this.escapeHtml(tag)}</span>`).join('')}</div>` : ''}
                </div>
            </div>
        `).join('');
    }
    
    updateListView(notes) {
        const container = document.querySelector('#listView .space-y-2');
        if (!container) return;
        
        if (notes.length === 0) {
            container.innerHTML = `
                <div class="text-center py-12 text-white/60">
                    <i class="fas fa-sticky-note text-4xl mb-4"></i>
                    <p>Keine Notizen vorhanden</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = notes.map(note => `
            <div class="short-list-item p-4" onclick="zettelkasten.editNote(${note.id})" data-note-id="${note.id}">
                <div class="flex items-start gap-3">
                    <div class="w-4 h-4 rounded-full flex-shrink-0 mt-1" style="background: ${note.color}"></div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="text-white font-medium text-sm">${this.escapeHtml(note.title)}</h4>
                            <div class="flex items-center gap-2">
                                ${note.is_pinned ? '<i class="fas fa-thumbtack text-yellow-400 text-xs"></i>' : ''}
                                ${note.links_count > 0 ? `<span class="text-xs text-blue-300">${note.links_count} <i class="fas fa-link"></i></span>` : ''}
                            </div>
                        </div>
                        ${note.content ? `<p class="text-white/60 text-xs line-clamp-2">${this.escapeHtml(note.content)}</p>` : ''}
                        <div class="flex items-center gap-2 mt-2">
                            <span class="text-white/50 text-xs">${this.formatDate(note.updated_at)}</span>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }
    
    initializeGraph() {
        const container = document.getElementById('nodeCanvas');
        if (!container || this.isGraphInitialized) return;
        
        // Clear existing content
        container.innerHTML = '';
        
        // Create SVG for D3.js
        const width = container.clientWidth;
        const height = container.clientHeight;
        
        const svg = d3.select(container)
            .append('svg')
            .attr('width', width)
            .attr('height', height)
            .style('background', 'transparent');
        
        // Create zoom behavior
        const zoom = d3.zoom()
            .scaleExtent([0.1, 3])
            .on('zoom', (event) => {
                graphGroup.attr('transform', event.transform);
            });
        
        svg.call(zoom);
        
        // Create main group for all graph elements
        const graphGroup = svg.append('g');
        
        // Create groups for links and nodes
        this.linkGroup = graphGroup.append('g').attr('class', 'links');
        this.nodeGroup = graphGroup.append('g').attr('class', 'nodes');
        
        // Store references
        this.svg = svg;
        this.graphGroup = graphGroup;
        this.width = width;
        this.height = height;
        
        this.isGraphInitialized = true;
        
        // Load and render graph data
        this.loadGraphData();
    }
    
    updateGraphView(notes = this.notes) {
        if (!this.isGraphInitialized) {
            this.initializeGraph();
            return;
        }
        
        // Prepare data
        const nodeData = notes.map((note, index) => ({
            id: note.id,
            title: note.title,
            color: note.color,
            isPinned: note.is_pinned,
            linksCount: note.links_count || 0,
            x: note.position_x || (index % 5) * 150 + 100,
            y: note.position_y || Math.floor(index / 5) * 100 + 100
        }));
        
        const linkData = this.links.filter(link => 
            nodeData.some(n => n.id === link.source_note_id) && 
            nodeData.some(n => n.id === link.target_note_id)
        ).map(link => ({
            source: link.source_note_id,
            target: link.target_note_id,
            type: link.link_type
        }));
        
        this.renderGraph(nodeData, linkData);
    }
    
    renderGraph(nodes, links) {
        // Create force simulation
        const simulation = d3.forceSimulation(nodes)
            .force('link', d3.forceLink(links).id(d => d.id).distance(100))
            .force('charge', d3.forceManyBody().strength(-300))
            .force('center', d3.forceCenter(this.width / 2, this.height / 2))
            .force('collision', d3.forceCollide().radius(50));
        
        // Create links
        const link = this.linkGroup.selectAll('line')
            .data(links)
            .join('line')
            .attr('stroke', '#666')
            .attr('stroke-opacity', 0.6)
            .attr('stroke-width', d => d.type === 'bidirectional' ? 3 : 2)
            .attr('stroke-dasharray', d => d.type === 'backlink' ? '5,5' : '');
        
        // Create nodes
        const node = this.nodeGroup.selectAll('g')
            .data(nodes)
            .join('g')
            .attr('class', 'graph-node')
            .call(this.createDragBehavior(simulation));
        
        // Add circles to nodes
        node.append('circle')
            .attr('r', d => Math.max(20, Math.min(40, 20 + d.linksCount * 3)))
            .attr('fill', d => d.color)
            .attr('stroke', d => d.isPinned ? '#fbbf24' : '#fff')
            .attr('stroke-width', d => d.isPinned ? 3 : 2)
            .attr('opacity', 0.8);
        
        // Add labels
        node.append('text')
            .text(d => d.title.length > 15 ? d.title.substring(0, 12) + '...' : d.title)
            .attr('text-anchor', 'middle')
            .attr('dy', 5)
            .attr('fill', 'white')
            .attr('font-size', '12px')
            .attr('font-weight', 'bold')
            .style('pointer-events', 'none');
        
        // Add tooltips
        node.append('title')
            .text(d => `${d.title}\nVerbindungen: ${d.linksCount}`);
        
        // Add click handlers
        node.on('click', (event, d) => {
            if (!event.defaultPrevented) {
                this.editNote(d.id);
            }
        });
        
        // Add hover effects
        node.on('mouseenter', function(event, d) {
            d3.select(this).select('circle')
                .transition()
                .duration(200)
                .attr('opacity', 1)
                .attr('stroke-width', 4);
        })
        .on('mouseleave', function(event, d) {
            d3.select(this).select('circle')
                .transition()
                .duration(200)
                .attr('opacity', 0.8)
                .attr('stroke-width', d.isPinned ? 3 : 2);
        });
        
        // Update positions on simulation tick
        simulation.on('tick', () => {
            link
                .attr('x1', d => d.source.x)
                .attr('y1', d => d.source.y)
                .attr('x2', d => d.target.x)
                .attr('y2', d => d.target.y);
            
            node.attr('transform', d => `translate(${d.x},${d.y})`);
        });
    }
    
    createDragBehavior(simulation) {
        return d3.drag()
            .on('start', (event, d) => {
                if (!event.active) simulation.alphaTarget(0.3).restart();
                d.fx = d.x;
                d.fy = d.y;
            })
            .on('drag', (event, d) => {
                d.fx = event.x;
                d.fy = event.y;
            })
            .on('end', (event, d) => {
                if (!event.active) simulation.alphaTarget(0);
                // Save position
                this.saveNodePosition(d.id, d.fx, d.fy);
                d.fx = null;
                d.fy = null;
            });
    }
    
    async saveNodePosition(noteId, x, y) {
        try {
            await fetch('/api/notes.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'save_position',
                    note_id: noteId,
                    x: x,
                    y: y
                })
            });
        } catch (error) {
            console.error('Error saving node position:', error);
        }
    }
    
    async createNoteLink(sourceId, targetId) {
        try {
            const response = await fetch('/api/notes.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'link',
                    source_note_id: sourceId,
                    target_note_id: targetId,
                    link_type: 'reference'
                })
            });
            
            const data = await response.json();
            if (data.success) {
                await this.loadGraphData();
                this.showNotification('Verknüpfung erstellt', 'success');
            }
        } catch (error) {
            console.error('Error creating link:', error);
        }
    }
    
    async shareNote(noteId, userId, permission = 'read') {
        try {
            const response = await fetch('/api/notes.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'share',
                    note_id: noteId,
                    share_with_user_id: userId,
                    permission_level: permission
                })
            });
            
            const data = await response.json();
            if (data.success) {
                this.showNotification('Notiz geteilt', 'success');
            }
        } catch (error) {
            console.error('Error sharing note:', error);
        }
    }
    
    editNote(noteId) {
        // This will be connected to the existing note editor
        if (window.editNote) {
            window.editNote(noteId);
        }
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
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
    
    showNotification(message, type = 'info') {
        // Use existing notification system if available
        if (window.showNotification) {
            window.showNotification(message, type);
        } else {
            console.log(`${type.toUpperCase()}: ${message}`);
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    if (typeof d3 !== 'undefined') {
        window.zettelkasten = new ZettelkastenManager();
    } else {
        console.warn('D3.js not loaded - graph view will not be available');
    }
});
