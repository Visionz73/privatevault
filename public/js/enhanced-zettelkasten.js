/**
 * Enhanced Zettelkasten Manager
 * Provides comprehensive note management with graph visualization
 */
class EnhancedZettelkasten {
    constructor() {
        this.notes = [];
        this.links = [];
        this.selectedNotes = new Set();
        this.currentView = 'grid';
        this.searchQuery = '';
        this.tooltip = null;
        this.simulation = null;
        
        this.init();
    }
    
    async init() {
        await this.loadNotes();
        this.setupEventListeners();
        this.createTooltip();
    }
    
    async loadNotes() {
        try {
            const response = await fetch('/api/notes.php?limit=200&include_links=true');
            const data = await response.json();
            
            if (data.success) {
                this.notes = data.notes || [];
                this.links = data.links || [];
                this.updateNotesDisplay();
            }
        } catch (error) {
            console.error('Error loading notes:', error);
            this.showNotification('Fehler beim Laden der Notizen', 'error');
        }
    }
    
    setupEventListeners() {
        // Search functionality
        const searchInput = document.getElementById('notesSearch');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.searchQuery = e.target.value.toLowerCase();
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
        
        // Graph controls
        this.setupGraphControls();
    }
    
    setupGraphControls() {
        const centerBtn = document.querySelector('[onclick="zettelkasten.centerGraph()"]');
        if (centerBtn) {
            centerBtn.onclick = () => this.centerGraph();
        }
        
        const resetBtn = document.querySelector('[onclick="zettelkasten.resetGraph()"]');
        if (resetBtn) {
            resetBtn.onclick = () => this.resetGraph();
        }
        
        const reheatBtn = document.querySelector('[onclick="zettelkasten.reheatSimulation()"]');
        if (reheatBtn) {
            reheatBtn.onclick = () => this.reheatSimulation();
        }
    }
    
    switchView(view) {
        this.currentView = view;
        
        // Update button states
        document.querySelectorAll('.view-toggle-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        const activeBtn = document.querySelector(`[data-view="${view}"]`);
        if (activeBtn) {
            activeBtn.classList.add('active');
        }
          // Show/hide views
        const notesGrid = document.getElementById('notesGrid');
        const nodeView = document.getElementById('nodeView');
        const listView = document.getElementById('listView');
        
        if (notesGrid) notesGrid.style.display = view === 'grid' ? 'block' : 'none';
        if (nodeView) nodeView.style.display = view === 'node' ? 'block' : 'none';
        if (listView) listView.style.display = view === 'list' ? 'block' : 'none';
        
        this.updateNotesDisplay();
    }
    
    filterNotes() {
        const filteredNotes = this.notes.filter(note => {
            if (!this.searchQuery) return true;
            
            return note.title.toLowerCase().includes(this.searchQuery) ||
                   (note.content && note.content.toLowerCase().includes(this.searchQuery)) ||
                   (note.tags && note.tags.some(tag => tag.toLowerCase().includes(this.searchQuery)));
        });
        
        this.updateNotesDisplay(filteredNotes);
    }
    
    updateNotesDisplay(notes = this.notes) {
        switch (this.currentView) {
            case 'grid':
                this.updateGridView(notes);
                break;
            case 'node':
                this.updateNodeView(notes);
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
            container.innerHTML = this.getEmptyStateHTML('grid');
            return;
        }
        
        container.innerHTML = notes.map(note => this.createNoteCardHTML(note)).join('');
    }
    
    updateListView(notes) {
        const container = document.querySelector('#listView .space-y-2');
        if (!container) return;
        
        if (notes.length === 0) {
            container.innerHTML = this.getEmptyStateHTML('list');
            return;
        }
        
        container.innerHTML = notes.map(note => this.createNoteListItemHTML(note)).join('');
    }
    
    updateNodeView(notes) {
        const container = document.getElementById('nodeCanvas');
        if (!container) return;
        
        // Clear existing content
        container.innerHTML = '';
        
        if (notes.length === 0) {
            container.innerHTML = this.getEmptyStateHTML('node');
            return;
        }
        
        // Check if D3.js is available
        if (typeof d3 === 'undefined') {
            this.renderSimpleNodeView(container, notes);
        } else {
            this.renderD3NodeView(container, notes);
        }
    }
    
    renderSimpleNodeView(container, notes) {
        container.className = 'node-canvas-simple relative w-full h-full overflow-hidden';
        
        // Create nodes
        notes.forEach((note, index) => {
            const node = document.createElement('div');
            node.className = `note-node absolute cursor-pointer transition-all duration-300 ${note.is_pinned ? 'pinned' : ''}`;
            node.setAttribute('data-note-id', note.id);
            
            // Calculate position
            const cols = Math.floor(container.clientWidth / 140) || 6;
            const x = (index % cols) * 140 + 20;
            const y = Math.floor(index / cols) * 120 + 20;
            
            node.style.cssText = `
                left: ${x}px;
                top: ${y}px;
                width: 120px;
                height: 90px;
                background: linear-gradient(135deg, ${note.color}, ${this.darkenColor(note.color, 20)});
                border-radius: 12px;
                padding: 10px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.4);
                border: 2px solid ${note.is_pinned ? '#fbbf24' : 'transparent'};
                transform: scale(1);
                z-index: 1;
            `;
            
            node.innerHTML = `
                <div class="node-content text-white">
                    <div class="node-title font-bold text-xs leading-tight mb-2 overflow-hidden" 
                         style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                        ${this.escapeHtml(note.title)}
                    </div>
                    <div class="node-meta flex items-center justify-between text-xs opacity-80">
                        <div class="flex items-center gap-1">
                            ${note.links_count > 0 ? `<i class="fas fa-link"></i> ${note.links_count}` : ''}
                        </div>
                        <div class="flex items-center gap-1">
                            ${note.is_pinned ? '<i class="fas fa-thumbtack text-yellow-300"></i>' : ''}
                            ${note.is_shared ? '<i class="fas fa-share-alt text-blue-300"></i>' : ''}
                        </div>
                    </div>
                </div>
            `;
            
            // Add hover effects
            node.addEventListener('mouseenter', (e) => {
                node.style.transform = 'scale(1.1)';
                node.style.zIndex = '10';
                node.style.borderColor = 'rgba(255,255,255,0.8)';
                this.showNodeTooltip(e, note);
            });
            
            node.addEventListener('mouseleave', (e) => {
                node.style.transform = 'scale(1)';
                node.style.zIndex = '1';
                node.style.borderColor = note.is_pinned ? '#fbbf24' : 'transparent';
                this.hideNodeTooltip();
            });
            
            // Add click handler
            node.addEventListener('click', () => {
                this.editNote(note.id);
            });
            
            // Make draggable
            this.makeNodeDraggable(node);
            
            container.appendChild(node);
        });
        
        // Draw connections
        this.drawSimpleConnections(container, notes);
    }
    
    renderD3NodeView(container, notes) {
        const width = container.clientWidth;
        const height = container.clientHeight;
        
        // Create SVG
        const svg = d3.select(container)
            .append('svg')
            .attr('width', width)
            .attr('height', height)
            .style('background', 'transparent');
        
        // Prepare links data
        const linksData = this.links.filter(link => 
            notes.some(n => n.id === link.source_note_id) && 
            notes.some(n => n.id === link.target_note_id)
        ).map(link => ({
            source: link.source_note_id,
            target: link.target_note_id,
            type: link.link_type || 'reference'
        }));
        
        // Create zoom behavior
        const zoom = d3.zoom()
            .scaleExtent([0.1, 4])
            .on('zoom', (event) => {
                g.attr('transform', event.transform);
            });
        
        svg.call(zoom);
        
        // Main group for all elements
        const g = svg.append('g');
        
        // Add arrow markers for different link types
        const defs = svg.append('defs');
        
        const arrowColors = {
            'reference': '#60A5FA',
            'backlink': '#34D399',
            'bidirectional': '#F472B6'
        };
        
        Object.entries(arrowColors).forEach(([type, color]) => {
            defs.append('marker')
                .attr('id', `arrow-${type}`)
                .attr('viewBox', '0 -5 10 10')
                .attr('refX', 25)
                .attr('refY', 0)
                .attr('orient', 'auto')
                .attr('markerWidth', 6)
                .attr('markerHeight', 6)
                .append('path')
                .attr('d', 'M0,-5L10,0L0,5')
                .attr('fill', color);
        });
        
        // Create force simulation
        this.simulation = d3.forceSimulation(notes)
            .force('link', d3.forceLink(linksData).id(d => d.id).distance(200))
            .force('charge', d3.forceManyBody().strength(-500))
            .force('center', d3.forceCenter(width / 2, height / 2))
            .force('collision', d3.forceCollide().radius(d => this.getNodeRadius(d) + 10));
        
        // Create links
        const link = g.append('g')
            .attr('class', 'links')
            .selectAll('line')
            .data(linksData)
            .join('line')
            .attr('stroke', d => arrowColors[d.type] || '#666')
            .attr('stroke-opacity', 0.8)
            .attr('stroke-width', d => d.type === 'bidirectional' ? 3 : 2)
            .attr('stroke-dasharray', d => d.type === 'backlink' ? '5,5' : null)
            .attr('marker-end', d => `url(#arrow-${d.type})`);
        
        // Create node groups
        const nodeGroup = g.append('g')
            .attr('class', 'nodes')
            .selectAll('g')
            .data(notes)
            .join('g')
            .attr('class', 'node-group')
            .style('cursor', 'pointer')
            .call(d3.drag()
                .on('start', (event, d) => this.dragStarted(event, d))
                .on('drag', (event, d) => this.dragged(event, d))
                .on('end', (event, d) => this.dragEnded(event, d))
            );
        
        // Add node circles with gradients
        notes.forEach(note => {
            const gradient = defs.append('radialGradient')
                .attr('id', `gradient-${note.id}`)
                .attr('cx', '30%')
                .attr('cy', '30%');
            
            gradient.append('stop')
                .attr('offset', '0%')
                .attr('stop-color', note.color);
                
            gradient.append('stop')
                .attr('offset', '100%')
                .attr('stop-color', d3.color(note.color).darker(1));
        });
        
        // Add circles
        nodeGroup.append('circle')
            .attr('r', d => this.getNodeRadius(d))
            .attr('fill', d => `url(#gradient-${d.id})`)
            .attr('stroke', d => d.is_pinned ? '#fbbf24' : '#fff')
            .attr('stroke-width', d => d.is_pinned ? 4 : 2)
            .style('filter', 'drop-shadow(0 4px 8px rgba(0,0,0,0.4))');
        
        // Add link count badges
        nodeGroup.filter(d => d.links_count > 0)
            .append('circle')
            .attr('r', 10)
            .attr('cx', d => this.getNodeRadius(d) - 10)
            .attr('cy', d => -this.getNodeRadius(d) + 10)
            .attr('fill', '#3B82F6')
            .attr('stroke', '#fff')
            .attr('stroke-width', 2);
            
        nodeGroup.filter(d => d.links_count > 0)
            .append('text')
            .attr('x', d => this.getNodeRadius(d) - 10)
            .attr('y', d => -this.getNodeRadius(d) + 14)
            .attr('text-anchor', 'middle')
            .attr('font-size', '10px')
            .attr('font-weight', 'bold')
            .attr('fill', 'white')
            .text(d => d.links_count);
        
        // Add pin indicators
        nodeGroup.filter(d => d.is_pinned)
            .append('text')
            .attr('x', 0)
            .attr('y', d => -this.getNodeRadius(d) - 8)
            .attr('text-anchor', 'middle')
            .attr('font-family', 'FontAwesome')
            .attr('font-size', '14px')
            .attr('fill', '#fbbf24')
            .text('\uf08d');
        
        // Add labels
        nodeGroup.append('text')
            .attr('dy', 4)
            .attr('text-anchor', 'middle')
            .attr('font-size', '12px')
            .attr('font-weight', 'bold')
            .attr('fill', 'white')
            .attr('text-shadow', '0 1px 3px rgba(0,0,0,0.8)')
            .text(d => d.title.length > 15 ? d.title.substring(0, 15) + '...' : d.title);
        
        // Add interactions
        nodeGroup
            .on('mouseover', (event, d) => {
                this.showNodeTooltip(event, d);
                this.highlightConnectedNodes(nodeGroup, link, d);
            })
            .on('mouseout', (event, d) => {
                this.hideNodeTooltip();
                this.unhighlightNodes(nodeGroup, link);
            })
            .on('click', (event, d) => {
                event.stopPropagation();
                this.editNote(d.id);
            });
        
        // Update simulation
        this.simulation.on('tick', () => {
            link
                .attr('x1', d => d.source.x)
                .attr('y1', d => d.source.y)
                .attr('x2', d => d.target.x)
                .attr('y2', d => d.target.y);
                
            nodeGroup
                .attr('transform', d => `translate(${d.x},${d.y})`);
        });
        
        // Store references for graph controls
        this.svg = svg;
        this.nodeGroup = nodeGroup;
        this.linkGroup = link;
        this.zoom = zoom;
    }
    
    getNodeRadius(note) {
        const baseRadius = 25;
        const linkBonus = Math.min(15, (note.links_count || 0) * 2);
        return baseRadius + linkBonus;
    }
    
    highlightConnectedNodes(nodeGroup, links, selectedNode) {
        // Fade all nodes and links
        nodeGroup.select('circle').style('opacity', 0.3);
        links.style('opacity', 0.1);
        
        // Highlight selected node
        nodeGroup.filter(d => d.id === selectedNode.id)
            .select('circle')
            .style('opacity', 1)
            .style('stroke-width', 6);
        
        // Find connected nodes
        const connectedIds = new Set();
        this.links.forEach(link => {
            if (link.source_note_id === selectedNode.id) {
                connectedIds.add(link.target_note_id);
            }
            if (link.target_note_id === selectedNode.id) {
                connectedIds.add(link.source_note_id);
            }
        });
        
        // Highlight connected nodes
        nodeGroup.filter(d => connectedIds.has(d.id))
            .select('circle')
            .style('opacity', 0.8);
        
        // Highlight relevant links
        links.filter(d => 
            d.source.id === selectedNode.id || d.target.id === selectedNode.id
        ).style('opacity', 1);
    }
    
    unhighlightNodes(nodeGroup, links) {
        nodeGroup.select('circle')
            .style('opacity', 1)
            .style('stroke-width', d => d.is_pinned ? 4 : 2);
        links.style('opacity', 0.8);
    }
    
    // D3 drag handlers
    dragStarted(event, d) {
        if (!event.active && this.simulation) {
            this.simulation.alphaTarget(0.3).restart();
        }
        d.fx = d.x;
        d.fy = d.y;
    }
    
    dragged(event, d) {
        d.fx = event.x;
        d.fy = event.y;
    }
    
    dragEnded(event, d) {
        if (!event.active && this.simulation) {
            this.simulation.alphaTarget(0);
        }
        d.fx = null;
        d.fy = null;
        
        // Save position
        this.saveNodePosition(d.id, d.x, d.y);
    }
    
    // Graph controls
    centerGraph() {
        if (this.zoom && this.svg) {
            const width = this.svg.attr('width');
            const height = this.svg.attr('height');
            this.svg.transition().duration(750).call(
                this.zoom.transform,
                d3.zoomIdentity.translate(width / 2, height / 2).scale(1)
            );
        }
    }
    
    resetGraph() {
        if (this.simulation) {
            this.simulation.alpha(1).restart();
        }
    }
    
    reheatSimulation() {
        if (this.simulation) {
            this.simulation.alphaTarget(0.3).restart();
            setTimeout(() => {
                this.simulation.alphaTarget(0);
            }, 1000);
        }
    }
    
    // Utility methods
    createTooltip() {
        if (!this.tooltip) {
            this.tooltip = document.createElement('div');
            this.tooltip.className = 'node-tooltip absolute bg-black/90 text-white p-3 rounded-lg text-sm pointer-events-none z-50 opacity-0 transition-opacity duration-200';
            this.tooltip.style.maxWidth = '300px';
            document.body.appendChild(this.tooltip);
        }
    }
    
    showNodeTooltip(event, note) {
        if (!this.tooltip) return;
        
        const content = note.content ? note.content.substring(0, 150) + '...' : 'Keine Beschreibung';
        const linkText = note.links_count > 0 ? `${note.links_count} Verknüpfung${note.links_count > 1 ? 'en' : ''}` : 'Keine Verknüpfungen';
        
        this.tooltip.innerHTML = `
            <div class="font-bold mb-2">${this.escapeHtml(note.title)}</div>
            <div class="text-gray-300 mb-2">${this.escapeHtml(content)}</div>
            <div class="flex items-center gap-4 text-xs">
                <span class="flex items-center gap-1">
                    <i class="fas fa-link"></i> ${linkText}
                </span>
                <span class="flex items-center gap-1">
                    <i class="fas fa-calendar"></i> ${this.formatDate(note.updated_at)}
                </span>
                ${note.is_pinned ? '<span class="flex items-center gap-1 text-yellow-300"><i class="fas fa-thumbtack"></i> Angeheftet</span>' : ''}
            </div>
        `;
        
        const rect = event.target.getBoundingClientRect();
        this.tooltip.style.left = (rect.right + 10) + 'px';
        this.tooltip.style.top = rect.top + 'px';
        this.tooltip.style.opacity = '1';
    }
    
    hideNodeTooltip() {
        if (this.tooltip) {
            this.tooltip.style.opacity = '0';
        }
    }
    
    makeNodeDraggable(node) {
        let isDragging = false;
        let startX, startY, initialX, initialY;
        
        node.addEventListener('mousedown', (e) => {
            isDragging = true;
            startX = e.clientX;
            startY = e.clientY;
            initialX = parseInt(node.style.left);
            initialY = parseInt(node.style.top);
            node.style.cursor = 'grabbing';
            e.preventDefault();
        });
        
        document.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            
            const deltaX = e.clientX - startX;
            const deltaY = e.clientY - startY;
            
            node.style.left = (initialX + deltaX) + 'px';
            node.style.top = (initialY + deltaY) + 'px';
        });
        
        document.addEventListener('mouseup', () => {
            if (isDragging) {
                isDragging = false;
                node.style.cursor = 'pointer';
                
                // Save position
                const noteId = node.getAttribute('data-note-id');
                const x = parseInt(node.style.left);
                const y = parseInt(node.style.top);
                this.saveNodePosition(noteId, x, y);
            }
        });
    }
    
    drawSimpleConnections(container, notes) {
        // Create SVG overlay for connections
        const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.style.position = 'absolute';
        svg.style.top = '0';
        svg.style.left = '0';
        svg.style.width = '100%';
        svg.style.height = '100%';
        svg.style.pointerEvents = 'none';
        svg.style.zIndex = '0';
        
        this.links.forEach(link => {
            const sourceNode = container.querySelector(`[data-note-id="${link.source_note_id}"]`);
            const targetNode = container.querySelector(`[data-note-id="${link.target_note_id}"]`);
            
            if (sourceNode && targetNode) {
                const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                
                const sourceRect = sourceNode.getBoundingClientRect();
                const targetRect = targetNode.getBoundingClientRect();
                const containerRect = container.getBoundingClientRect();
                
                const x1 = sourceRect.left - containerRect.left + sourceRect.width / 2;
                const y1 = sourceRect.top - containerRect.top + sourceRect.height / 2;
                const x2 = targetRect.left - containerRect.left + targetRect.width / 2;
                const y2 = targetRect.top - containerRect.top + targetRect.height / 2;
                
                line.setAttribute('x1', x1);
                line.setAttribute('y1', y1);
                line.setAttribute('x2', x2);
                line.setAttribute('y2', y2);
                line.setAttribute('stroke', link.link_type === 'bidirectional' ? '#F472B6' : '#60A5FA');
                line.setAttribute('stroke-width', '2');
                line.setAttribute('stroke-opacity', '0.6');
                
                if (link.link_type === 'backlink') {
                    line.setAttribute('stroke-dasharray', '5,5');
                }
                
                svg.appendChild(line);
            }
        });
        
        container.appendChild(svg);
    }
    
    async saveNodePosition(noteId, x, y) {
        try {
            await fetch('/api/notes.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'update_position',
                    note_id: noteId,
                    position_x: x,
                    position_y: y
                })
            });
        } catch (error) {
            console.error('Error saving node position:', error);
        }
    }
    
    // Note management methods
    async editNote(noteId) {
        const note = this.notes.find(n => n.id == noteId);
        if (!note) return;
        
        // Use existing note editor function if available
        if (typeof openNoteEditor === 'function') {
            openNoteEditor(noteId);
        } else if (typeof editNote === 'function') {
            editNote(noteId);
        } else {
            window.location.href = `/note_detail.php?id=${noteId}`;
        }
    }
    
    showNoteLinks(noteId) {
        if (typeof showLinkedNotes === 'function') {
            showLinkedNotes();
        }
    }
    
    // Helper methods
    createNoteCardHTML(note) {
        return `
            <div class="note-card group relative bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-4 hover:bg-white/10 transition-all duration-300" 
                 style="border-left: 4px solid ${note.color}" 
                 data-note-id="${note.id}">
                <div class="flex items-start justify-between mb-3">
                    <h3 class="note-title text-white font-semibold text-lg leading-tight cursor-pointer" 
                        onclick="zettelkasten.editNote(${note.id})">
                        ${this.escapeHtml(note.title)}
                    </h3>
                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        ${note.is_pinned ? '<i class="fas fa-thumbtack text-yellow-400"></i>' : ''}
                        ${note.links_count > 0 ? `<span class="text-xs bg-blue-500/20 text-blue-300 px-2 py-1 rounded">${note.links_count} <i class="fas fa-link"></i></span>` : ''}
                        <button onclick="zettelkasten.editNote(${note.id})" class="text-white/60 hover:text-white p-1">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </div>
                ${note.content ? `<div class="note-content text-white/70 text-sm mb-3 line-clamp-3">${this.escapeHtml(note.content)}</div>` : ''}
                <div class="flex items-center justify-between text-xs text-white/50">
                    <span><i class="fas fa-calendar mr-1"></i>${this.formatDate(note.updated_at)}</span>
                    ${note.tags && note.tags.length > 0 ? `<div class="flex gap-1">${note.tags.slice(0, 2).map(tag => `<span class="bg-white/10 px-2 py-1 rounded">#${this.escapeHtml(tag)}</span>`).join('')}</div>` : ''}
                </div>
            </div>
        `;
    }
    
    createNoteListItemHTML(note) {
        return `
            <div class="note-list-item group flex items-center gap-4 p-4 bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg hover:bg-white/10 transition-all duration-300 cursor-pointer"
                 data-note-id="${note.id}"
                 onclick="zettelkasten.editNote(${note.id})">
                <div class="w-4 h-4 rounded-full flex-shrink-0" style="background: ${note.color}"></div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="text-white font-medium truncate">${this.escapeHtml(note.title)}</h3>
                        <div class="flex items-center gap-2 text-xs text-white/50">
                            ${note.is_pinned ? '<i class="fas fa-thumbtack text-yellow-400"></i>' : ''}
                            ${note.links_count > 0 ? `<span class="flex items-center gap-1"><i class="fas fa-link"></i> ${note.links_count}</span>` : ''}
                        </div>
                    </div>
                    ${note.content ? `<p class="text-white/60 text-sm truncate">${this.escapeHtml(note.content)}</p>` : ''}
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-xs text-white/40">${this.formatDate(note.updated_at)}</span>
                        ${note.tags && note.tags.length > 0 ? `<div class="flex gap-1">${note.tags.slice(0, 3).map(tag => `<span class="text-xs bg-white/10 px-1 py-0.5 rounded">#${this.escapeHtml(tag)}</span>`).join('')}</div>` : ''}
                    </div>
                </div>
            </div>
        `;
    }
    
    getEmptyStateHTML(view) {
        const icons = {
            grid: 'fa-th',
            node: 'fa-project-diagram',
            list: 'fa-list'
        };
        
        const messages = {
            grid: 'Keine Notizen im Grid gefunden',
            node: 'Keine Notizen für Knotenansicht vorhanden',
            list: 'Keine Notizen in der Liste gefunden'
        };
        
        return `
            <div class="col-span-full text-center py-12 text-white/60">
                <i class="fas ${icons[view]} text-6xl mb-4 opacity-50"></i>
                <h3 class="text-xl mb-2">${messages[view]}</h3>
                <p class="text-sm opacity-70">Erstellen Sie Notizen und verknüpfen Sie diese miteinander</p>
                <button onclick="openNoteEditor()" class="mt-4 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>Erste Notiz erstellen
                </button>
            </div>
        `;
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('de-DE', {
            day: '2-digit',
            month: '2-digit', 
            year: 'numeric'
        });
    }
    
    darkenColor(color, percent) {
        const num = parseInt(color.replace("#", ""), 16);
        const amt = Math.round(2.55 * percent);
        const R = (num >> 16) - amt;
        const G = (num >> 8 & 0x00FF) - amt;
        const B = (num & 0x0000FF) - amt;
        return "#" + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
            (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
            (B < 255 ? B < 1 ? 0 : B : 255)).toString(16).slice(1);
    }
    
    showNotification(message, type = 'info') {
        if (typeof showNotification === 'function') {
            showNotification(message, type);
        } else {
            console.log(`${type.toUpperCase()}: ${message}`);
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    if (typeof window.zettelkasten === 'undefined') {
        window.zettelkasten = new EnhancedZettelkasten();
    }
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = EnhancedZettelkasten;
}
