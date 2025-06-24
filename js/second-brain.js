/**
 * Second Brain - Core JavaScript Module
 * Manages the knowledge graph, note linking, and visual interactions
 */

class SecondBrain {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.notes = new Map();
        this.links = new Map();
        this.clusters = new Map();
        this.searchIndex = new Map();
        this.filters = {
            tags: [],
            search: '',
            timeRange: 'all'
        };
        
        // Visual settings
        this.nodeSize = 12;
        this.linkWidth = 2;
        this.highlightColor = '#60a5fa';
        this.selectedNodeColor = '#f59e0b';
        
        // Event handlers
        this.onNodeClick = null;
        this.onNodeHover = null;
        this.onLinkCreate = null;
        
        this.initializeCanvas();
        this.setupEventListeners();
        this.loadData();
    }

    initializeCanvas() {
        this.canvas = document.createElement('canvas');
        this.ctx = this.canvas.getContext('2d');
        this.canvas.className = 'w-full h-full cursor-pointer';
        this.container.appendChild(this.canvas);
        
        this.resizeCanvas();
        window.addEventListener('resize', () => this.resizeCanvas());
    }

    resizeCanvas() {
        const rect = this.container.getBoundingClientRect();
        this.canvas.width = rect.width * window.devicePixelRatio;
        this.canvas.height = rect.height * window.devicePixelRatio;
        this.canvas.style.width = rect.width + 'px';
        this.canvas.style.height = rect.height + 'px';
        this.ctx.scale(window.devicePixelRatio, window.devicePixelRatio);
        this.redraw();
    }

    setupEventListeners() {
        this.canvas.addEventListener('click', (e) => this.handleCanvasClick(e));
        this.canvas.addEventListener('mousemove', (e) => this.handleCanvasHover(e));
        this.canvas.addEventListener('mousedown', (e) => this.handleMouseDown(e));
        this.canvas.addEventListener('mouseup', (e) => this.handleMouseUp(e));
    }

    async loadData() {
        try {
            const response = await fetch('/src/api/notes.php?action=graph', {
                method: 'GET',
                headers: { 'Content-Type': 'application/json' }
            });
            
            if (!response.ok) throw new Error('Failed to load graph data');
            
            const data = await response.json();
            this.processGraphData(data);
            this.layoutNodes();
            this.redraw();
            
        } catch (error) {
            console.error('Error loading graph data:', error);
            this.showError('Failed to load notes graph');
        }
    }

    processGraphData(data) {
        // Clear existing data
        this.notes.clear();
        this.links.clear();
        
        // Process nodes
        data.nodes.forEach(node => {
            this.notes.set(node.id, {
                ...node,
                x: node.node_position_x || Math.random() * this.canvas.width,
                y: node.node_position_y || Math.random() * this.canvas.height,
                vx: 0,
                vy: 0,
                fx: null,
                fy: null,
                size: this.calculateNodeSize(node),
                visible: true
            });
        });
        
        // Process links
        data.links.forEach(link => {
            const linkId = `${link.source_note_id}-${link.target_note_id}`;
            this.links.set(linkId, {
                source: this.notes.get(link.source_note_id),
                target: this.notes.get(link.target_note_id),
                type: link.link_type,
                strength: 1
            });
        });
        
        this.updateSearchIndex();
    }

    calculateNodeSize(node) {
        const baseSize = this.nodeSize;
        const linkCount = node.incoming_links + node.outgoing_links;
        const sizeMultiplier = 1 + (linkCount * 0.1);
        return Math.min(baseSize * sizeMultiplier, baseSize * 3);
    }

    layoutNodes() {
        if (this.notes.size === 0) return;
        
        // Use force-directed layout for nodes without positions
        const unpositionedNodes = Array.from(this.notes.values())
            .filter(node => node.node_position_x === null);
        
        if (unpositionedNodes.length > 0) {
            this.applyForceLayout(unpositionedNodes);
        }
    }

    applyForceLayout(nodes) {
        const centerX = this.canvas.width / 2;
        const centerY = this.canvas.height / 2;
        const iterations = 100;
        
        for (let i = 0; i < iterations; i++) {
            // Apply forces
            nodes.forEach(node => {
                // Center force
                const dx = centerX - node.x;
                const dy = centerY - node.y;
                node.vx += dx * 0.01;
                node.vy += dy * 0.01;
                
                // Repulsion force
                nodes.forEach(other => {
                    if (node === other) return;
                    const dx = node.x - other.x;
                    const dy = node.y - other.y;
                    const distance = Math.sqrt(dx * dx + dy * dy) || 1;
                    const force = 500 / (distance * distance);
                    node.vx += (dx / distance) * force;
                    node.vy += (dy / distance) * force;
                });
                
                // Link attraction force
                this.links.forEach(link => {
                    if (link.source === node) {
                        const dx = link.target.x - node.x;
                        const dy = link.target.y - node.y;
                        const distance = Math.sqrt(dx * dx + dy * dy) || 1;
                        const force = distance * 0.02;
                        node.vx += (dx / distance) * force;
                        node.vy += (dy / distance) * force;
                    }
                });
            });
            
            // Update positions
            nodes.forEach(node => {
                node.x += node.vx;
                node.y += node.vy;
                node.vx *= 0.8; // Damping
                node.vy *= 0.8;
                
                // Keep within bounds
                node.x = Math.max(node.size, Math.min(this.canvas.width - node.size, node.x));
                node.y = Math.max(node.size, Math.min(this.canvas.height - node.size, node.y));
            });
        }
    }

    redraw() {
        if (!this.ctx) return;
        
        // Clear canvas
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        
        // Draw links first
        this.drawLinks();
        
        // Draw nodes
        this.drawNodes();
        
        // Draw labels for hovered/selected nodes
        this.drawLabels();
    }

    drawLinks() {
        this.ctx.strokeStyle = 'rgba(255, 255, 255, 0.2)';
        this.ctx.lineWidth = this.linkWidth;
        
        this.links.forEach(link => {
            if (!link.source.visible || !link.target.visible) return;
            
            this.ctx.beginPath();
            this.ctx.moveTo(link.source.x, link.source.y);
            this.ctx.lineTo(link.target.x, link.target.y);
            this.ctx.stroke();
        });
    }

    drawNodes() {
        this.notes.forEach(node => {
            if (!node.visible) return;
            
            // Node circle
            this.ctx.fillStyle = node.color || '#fbbf24';
            this.ctx.strokeStyle = node.is_pinned ? this.selectedNodeColor : 'rgba(255, 255, 255, 0.3)';
            this.ctx.lineWidth = node.is_pinned ? 3 : 1;
            
            this.ctx.beginPath();
            this.ctx.arc(node.x, node.y, node.size, 0, 2 * Math.PI);
            this.ctx.fill();
            this.ctx.stroke();
            
            // Pin indicator
            if (node.is_pinned) {
                this.ctx.fillStyle = this.selectedNodeColor;
                this.ctx.beginPath();
                this.ctx.arc(node.x - node.size * 0.6, node.y - node.size * 0.6, 3, 0, 2 * Math.PI);
                this.ctx.fill();
            }
        });
    }

    drawLabels() {
        this.ctx.font = '12px -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif';
        this.ctx.textAlign = 'center';
        this.ctx.textBaseline = 'middle';
        
        this.notes.forEach(node => {
            if (!node.visible || !node.showLabel) return;
            
            // Background
            const text = node.title;
            const textWidth = this.ctx.measureText(text).width;
            const padding = 8;
            
            this.ctx.fillStyle = 'rgba(0, 0, 0, 0.8)';
            this.ctx.fillRect(
                node.x - textWidth/2 - padding,
                node.y + node.size + 10,
                textWidth + padding * 2,
                20
            );
            
            // Text
            this.ctx.fillStyle = 'white';
            this.ctx.fillText(text, node.x, node.y + node.size + 20);
        });
    }

    // Event handlers
    handleCanvasClick(e) {
        const rect = this.canvas.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        const clickedNode = this.getNodeAt(x, y);
        if (clickedNode) {
            this.selectNode(clickedNode);
            if (this.onNodeClick) {
                this.onNodeClick(clickedNode);
            }
        } else {
            this.deselectAll();
        }
    }

    handleCanvasHover(e) {
        const rect = this.canvas.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        const hoveredNode = this.getNodeAt(x, y);
        
        // Clear previous hover states
        this.notes.forEach(node => {
            node.showLabel = false;
        });
        
        if (hoveredNode) {
            hoveredNode.showLabel = true;
            this.canvas.style.cursor = 'pointer';
            
            if (this.onNodeHover) {
                this.onNodeHover(hoveredNode);
            }
        } else {
            this.canvas.style.cursor = 'default';
        }
        
        this.redraw();
    }

    getNodeAt(x, y) {
        for (const node of this.notes.values()) {
            if (!node.visible) continue;
            
            const dx = x - node.x;
            const dy = y - node.y;
            const distance = Math.sqrt(dx * dx + dy * dy);
            
            if (distance <= node.size) {
                return node;
            }
        }
        return null;
    }

    selectNode(node) {
        this.notes.forEach(n => n.selected = false);
        node.selected = true;
        node.showLabel = true;
        this.highlightConnectedNodes(node);
        this.redraw();
    }

    deselectAll() {
        this.notes.forEach(node => {
            node.selected = false;
            node.showLabel = false;
            node.highlighted = false;
        });
        this.redraw();
    }

    highlightConnectedNodes(centerNode) {
        const connectedIds = new Set();
        
        this.links.forEach(link => {
            if (link.source.id === centerNode.id) {
                connectedIds.add(link.target.id);
            }
            if (link.target.id === centerNode.id) {
                connectedIds.add(link.source.id);
            }
        });
        
        this.notes.forEach(node => {
            node.highlighted = connectedIds.has(node.id);
        });
    }

    // Filter and search methods
    applyFilters() {
        this.notes.forEach(node => {
            node.visible = this.shouldShowNode(node);
        });
        this.redraw();
    }

    shouldShowNode(node) {
        // Tag filter
        if (this.filters.tags.length > 0) {
            const hasMatchingTag = this.filters.tags.some(filterTag => 
                node.tags.some(nodeTag => nodeTag.toLowerCase().includes(filterTag.toLowerCase()))
            );
            if (!hasMatchingTag) return false;
        }
        
        // Search filter
        if (this.filters.search) {
            const searchLower = this.filters.search.toLowerCase();
            const titleMatch = node.title.toLowerCase().includes(searchLower);
            const tagMatch = node.tags.some(tag => tag.toLowerCase().includes(searchLower));
            if (!titleMatch && !tagMatch) return false;
        }
        
        return true;
    }

    searchNodes(query) {
        this.filters.search = query;
        this.applyFilters();
        
        // Highlight matching nodes
        this.notes.forEach(node => {
            if (node.visible && query) {
                const titleMatch = node.title.toLowerCase().includes(query.toLowerCase());
                node.highlighted = titleMatch;
            }
        });
        
        this.redraw();
    }

    filterByTags(tags) {
        this.filters.tags = Array.isArray(tags) ? tags : [tags];
        this.applyFilters();
    }

    // Utility methods
    updateSearchIndex() {
        this.searchIndex.clear();
        this.notes.forEach(note => {
            const words = (note.title + ' ' + note.tags.join(' ')).toLowerCase().split(/\s+/);
            words.forEach(word => {
                if (!this.searchIndex.has(word)) {
                    this.searchIndex.set(word, []);
                }
                this.searchIndex.get(word).push(note.id);
            });
        });
    }

    async saveNodePosition(nodeId, x, y) {
        try {
            await fetch('/src/api/notes.php?action=position', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    note_id: nodeId,
                    x: x,
                    y: y
                })
            });
        } catch (error) {
            console.error('Error saving node position:', error);
        }
    }

    showError(message) {
        // Create error notification
        const errorDiv = document.createElement('div');
        errorDiv.className = 'fixed top-4 right-4 bg-red-600 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        errorDiv.textContent = message;
        document.body.appendChild(errorDiv);
        
        setTimeout(() => {
            document.body.removeChild(errorDiv);
        }, 5000);
    }
}

// Note Link Creator - handles bidirectional linking
class NoteLinkCreator {
    constructor(brain) {
        this.brain = brain;
        this.isLinkMode = false;
        this.sourceNode = null;
        this.setupUI();
    }

    setupUI() {
        // Add link creation buttons to note editor
        this.addLinkButton = document.createElement('button');
        this.addLinkButton.innerHTML = '<i class="fas fa-link mr-2"></i>Create Link';
        this.addLinkButton.className = 'btn-secondary px-3 py-1 text-sm';
        this.addLinkButton.onclick = () => this.toggleLinkMode();
    }

    toggleLinkMode() {
        this.isLinkMode = !this.isLinkMode;
        this.addLinkButton.textContent = this.isLinkMode ? 'Cancel Link' : 'Create Link';
        this.addLinkButton.className = this.isLinkMode ? 
            'btn-warning px-3 py-1 text-sm' : 
            'btn-secondary px-3 py-1 text-sm';
        
        if (this.isLinkMode) {
            this.brain.canvas.style.cursor = 'crosshair';
            this.brain.onNodeClick = (node) => this.handleNodeClickForLink(node);
        } else {
            this.brain.canvas.style.cursor = 'default';
            this.brain.onNodeClick = null;
            this.sourceNode = null;
        }
    }

    handleNodeClickForLink(node) {
        if (!this.sourceNode) {
            this.sourceNode = node;
            node.selected = true;
            this.brain.redraw();
            this.showLinkPreview();
        } else if (this.sourceNode.id !== node.id) {
            this.createLink(this.sourceNode, node);
            this.toggleLinkMode();
        }
    }

    async createLink(sourceNode, targetNode, linkType = 'reference') {
        try {
            const response = await fetch('/src/api/notes.php?action=link', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    source_note_id: sourceNode.id,
                    target_note_id: targetNode.id,
                    link_type: linkType
                })
            });

            if (!response.ok) throw new Error('Failed to create link');

            // Add link to brain
            const linkId = `${sourceNode.id}-${targetNode.id}`;
            this.brain.links.set(linkId, {
                source: sourceNode,
                target: targetNode,
                type: linkType,
                strength: 1
            });

            this.brain.redraw();
            this.showSuccess('Link created successfully');
            
        } catch (error) {
            console.error('Error creating link:', error);
            this.brain.showError('Failed to create link');
        }
    }

    showLinkPreview() {
        // Visual feedback for link creation
        const preview = document.createElement('div');
        preview.className = 'fixed bottom-4 left-4 bg-blue-600 text-white px-4 py-2 rounded-lg';
        preview.textContent = `Linking from: ${this.sourceNode.title}. Click another note to complete.`;
        document.body.appendChild(preview);
        
        setTimeout(() => {
            if (document.body.contains(preview)) {
                document.body.removeChild(preview);
            }
        }, 5000);
    }

    showSuccess(message) {
        const successDiv = document.createElement('div');
        successDiv.className = 'fixed top-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        successDiv.textContent = message;
        document.body.appendChild(successDiv);
        
        setTimeout(() => {
            document.body.removeChild(successDiv);
        }, 3000);
    }
}

// Export for use in other modules
window.SecondBrain = SecondBrain;
window.NoteLinkCreator = NoteLinkCreator;
