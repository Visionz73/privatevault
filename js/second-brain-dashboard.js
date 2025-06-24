/**
 * Second Brain Dashboard Integration
 * Integrates the enhanced notes system with graph view and statistics
 */

class SecondBrainDashboard {
    constructor() {
        this.currentView = 'grid';
        this.brain = null;
        this.linkCreator = null;
        this.statisticsData = null;
        
        this.initializeDashboard();
        this.loadStatistics();
    }

    initializeDashboard() {
        this.createGraphContainer();
        this.createStatisticsPanel();
        this.createQuickActions();
        this.setupViewSwitching();
        this.initializeGraph();
    }

    createGraphContainer() {
        const graphHTML = `
            <div id="graph-container" class="hidden">
                <div class="glass-card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-white">Knowledge Graph</h3>
                        <div class="flex items-center space-x-2">
                            <button id="graph-layout-btn" class="btn-secondary px-3 py-1 text-sm">
                                <i class="fas fa-project-diagram mr-1"></i>Re-layout
                            </button>
                            <button id="graph-center-btn" class="btn-secondary px-3 py-1 text-sm">
                                <i class="fas fa-crosshairs mr-1"></i>Center
                            </button>
                            <button id="graph-fullscreen-btn" class="btn-secondary px-3 py-1 text-sm">
                                <i class="fas fa-expand mr-1"></i>Fullscreen
                            </button>
                        </div>
                    </div>
                    <div id="knowledge-graph" class="node-view-container w-full h-96 bg-gray-900 rounded-lg"></div>
                    
                    <!-- Graph Controls -->
                    <div class="mt-4 flex flex-wrap gap-4">
                        <div class="flex items-center space-x-2">
                            <label class="text-sm text-gray-300">Node Size:</label>
                            <input type="range" id="node-size-slider" min="8" max="24" value="12" class="w-20">
                        </div>
                        <div class="flex items-center space-x-2">
                            <label class="text-sm text-gray-300">Link Strength:</label>
                            <input type="range" id="link-strength-slider" min="0.1" max="3" step="0.1" value="1" class="w-20">
                        </div>
                        <button id="toggle-labels-btn" class="btn-secondary px-3 py-1 text-sm">
                            <i class="fas fa-tags mr-1"></i>Toggle Labels
                        </button>
                    </div>
                </div>
                
                <!-- Selected Node Panel -->
                <div id="selected-node-panel" class="glass-card p-4 mt-4 hidden">
                    <h4 class="text-lg font-semibold text-white mb-3">Node Details</h4>
                    <div id="node-details-content">
                        <!-- Content will be populated when a node is selected -->
                    </div>
                </div>
            </div>
        `;
        
        const notesContainer = document.querySelector('.notes-container') || document.querySelector('main');
        notesContainer.insertAdjacentHTML('beforeend', graphHTML);
    }

    createStatisticsPanel() {
        const statsHTML = `
            <div id="statistics-panel" class="glass-card p-6 mb-6">
                <h3 class="text-xl font-semibold text-white mb-4">Second Brain Statistics</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="stat-card bg-blue-600/20 border border-blue-400/30 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-300 text-sm">Total Notes</p>
                                <p id="stat-total-notes" class="text-2xl font-bold text-white">0</p>
                            </div>
                            <i class="fas fa-sticky-note text-blue-400 text-xl"></i>
                        </div>
                    </div>
                    
                    <div class="stat-card bg-green-600/20 border border-green-400/30 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-300 text-sm">This Week</p>
                                <p id="stat-notes-week" class="text-2xl font-bold text-white">0</p>
                            </div>
                            <i class="fas fa-calendar-week text-green-400 text-xl"></i>
                        </div>
                    </div>
                    
                    <div class="stat-card bg-purple-600/20 border border-purple-400/30 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-300 text-sm">Total Links</p>
                                <p id="stat-total-links" class="text-2xl font-bold text-white">0</p>
                            </div>
                            <i class="fas fa-link text-purple-400 text-xl"></i>
                        </div>
                    </div>
                    
                    <div class="stat-card bg-yellow-600/20 border border-yellow-400/30 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-yellow-300 text-sm">Top Tags</p>
                                <p id="stat-top-tags" class="text-xl font-bold text-white">0</p>
                            </div>
                            <i class="fas fa-tags text-yellow-400 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Chart Container -->
                <div class="mt-6">
                    <h4 class="text-lg font-semibold text-white mb-3">Activity Over Time</h4>
                    <div id="activity-chart" class="bg-gray-800 rounded-lg p-4 h-48">
                        <canvas id="activity-canvas" width="100%" height="100%"></canvas>
                    </div>
                </div>
                
                <!-- Top Tags Cloud -->
                <div class="mt-6">
                    <h4 class="text-lg font-semibold text-white mb-3">Most Used Tags</h4>
                    <div id="top-tags-cloud" class="flex flex-wrap gap-2">
                        <!-- Tags will be populated here -->
                    </div>
                </div>
            </div>
        `;
        
        const notesContainer = document.querySelector('.notes-container') || document.querySelector('main');
        notesContainer.insertAdjacentHTML('afterbegin', statsHTML);
    }

    createQuickActions() {
        const quickActionsHTML = `
            <div id="quick-actions-panel" class="glass-card p-4 mb-6">
                <h3 class="text-lg font-semibold text-white mb-3">Quick Actions</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <button id="quick-daily-note" class="quick-action-btn bg-blue-600/20 border border-blue-400/30 hover:bg-blue-600/30 p-3 rounded-lg text-blue-300 text-center transition-colors">
                        <i class="fas fa-calendar-day text-xl mb-2"></i>
                        <p class="text-sm">Daily Note</p>
                    </button>
                    
                    <button id="quick-random-note" class="quick-action-btn bg-green-600/20 border border-green-400/30 hover:bg-green-600/30 p-3 rounded-lg text-green-300 text-center transition-colors">
                        <i class="fas fa-random text-xl mb-2"></i>
                        <p class="text-sm">Random Note</p>
                    </button>
                    
                    <button id="quick-unlinked-notes" class="quick-action-btn bg-purple-600/20 border border-purple-400/30 hover:bg-purple-600/30 p-3 rounded-lg text-purple-300 text-center transition-colors">
                        <i class="fas fa-unlink text-xl mb-2"></i>
                        <p class="text-sm">Unlinked Notes</p>
                    </button>
                    
                    <button id="quick-export-backup" class="quick-action-btn bg-yellow-600/20 border border-yellow-400/30 hover:bg-yellow-600/30 p-3 rounded-lg text-yellow-300 text-center transition-colors">
                        <i class="fas fa-download text-xl mb-2"></i>
                        <p class="text-sm">Export Backup</p>
                    </button>
                </div>
            </div>
        `;
        
        const notesContainer = document.querySelector('.notes-container') || document.querySelector('main');
        notesContainer.insertAdjacentHTML('afterbegin', quickActionsHTML);
    }

    setupViewSwitching() {
        // Override the view switching in enhanced-notes-manager
        const originalSetView = window.notesManager?.setView;
        if (window.notesManager) {
            window.notesManager.setView = (viewType) => {
                this.switchView(viewType);
                if (originalSetView) {
                    originalSetView.call(window.notesManager, viewType);
                }
            };
        }

        // Add event listeners
        document.getElementById('graph-layout-btn')?.addEventListener('click', () => this.relayoutGraph());
        document.getElementById('graph-center-btn')?.addEventListener('click', () => this.centerGraph());
        document.getElementById('graph-fullscreen-btn')?.addEventListener('click', () => this.toggleFullscreen());
        document.getElementById('node-size-slider')?.addEventListener('input', (e) => this.updateNodeSize(e.target.value));
        document.getElementById('link-strength-slider')?.addEventListener('input', (e) => this.updateLinkStrength(e.target.value));
        document.getElementById('toggle-labels-btn')?.addEventListener('click', () => this.toggleLabels());

        // Quick actions
        document.getElementById('quick-daily-note')?.addEventListener('click', () => this.createDailyNote());
        document.getElementById('quick-random-note')?.addEventListener('click', () => this.openRandomNote());
        document.getElementById('quick-unlinked-notes')?.addEventListener('click', () => this.showUnlinkedNotes());
        document.getElementById('quick-export-backup')?.addEventListener('click', () => this.exportBackup());
    }

    initializeGraph() {
        this.brain = new SecondBrain('knowledge-graph');
        this.linkCreator = new NoteLinkCreator(this.brain);
        
        // Set up event handlers
        this.brain.onNodeClick = (node) => this.handleNodeClick(node);
        this.brain.onNodeHover = (node) => this.handleNodeHover(node);
    }

    switchView(viewType) {
        this.currentView = viewType;
        
        const notesGrid = document.getElementById('notes-grid');
        const graphContainer = document.getElementById('graph-container');
        
        switch (viewType) {
            case 'grid':
            case 'list':
                notesGrid.style.display = 'block';
                graphContainer.style.display = 'none';
                break;
            case 'graph':
                notesGrid.style.display = 'none';
                graphContainer.style.display = 'block';
                this.brain?.resizeCanvas();
                break;
        }
    }

    async loadStatistics() {
        try {
            const response = await fetch('/src/api/notes.php?action=stats');
            if (!response.ok) throw new Error('Failed to load statistics');
            
            const data = await response.json();
            this.statisticsData = data.stats;
            this.updateStatisticsDisplay();
            this.createActivityChart();
            
        } catch (error) {
            console.error('Error loading statistics:', error);
        }
    }

    updateStatisticsDisplay() {
        if (!this.statisticsData) return;
        
        const { notes, links, top_tags } = this.statisticsData;
        
        // Update stat cards
        document.getElementById('stat-total-notes').textContent = notes.total_notes || 0;
        document.getElementById('stat-notes-week').textContent = notes.notes_this_week || 0;
        document.getElementById('stat-total-links').textContent = links.total_links || 0;
        document.getElementById('stat-top-tags').textContent = top_tags.length || 0;
        
        // Update tags cloud
        this.updateTopTagsCloud(top_tags);
    }

    updateTopTagsCloud(topTags) {
        const container = document.getElementById('top-tags-cloud');
        if (!container || !topTags) return;
        
        container.innerHTML = topTags.map(tag => {
            const size = Math.min(16 + (tag.usage_count * 2), 24);
            return `
                <button class="tag-cloud-item px-3 py-1 rounded-full bg-blue-600/20 border border-blue-400/30 text-blue-300 hover:bg-blue-600/30 transition-colors"
                        style="font-size: ${size}px"
                        onclick="notesManager.filterByTag('${tag.tag_name}')">
                    ${tag.tag_name} (${tag.usage_count})
                </button>
            `;
        }).join('');
    }

    createActivityChart() {
        const canvas = document.getElementById('activity-canvas');
        if (!canvas || !this.statisticsData) return;
        
        const ctx = canvas.getContext('2d');
        const rect = canvas.parentElement.getBoundingClientRect();
        canvas.width = rect.width;
        canvas.height = rect.height;
        
        // Simple activity chart (would be better with Chart.js)
        this.drawSimpleActivityChart(ctx, canvas.width, canvas.height);
    }

    drawSimpleActivityChart(ctx, width, height) {
        // Generate sample data for the last 7 days
        const days = 7;
        const data = Array.from({length: days}, () => Math.floor(Math.random() * 10));
        const maxValue = Math.max(...data, 1);
        
        const barWidth = width / days;
        const maxBarHeight = height - 40;
        
        ctx.fillStyle = '#374151';
        ctx.fillRect(0, 0, width, height);
        
        // Draw bars
        data.forEach((value, index) => {
            const barHeight = (value / maxValue) * maxBarHeight;
            const x = index * barWidth + 10;
            const y = height - barHeight - 20;
            
            const gradient = ctx.createLinearGradient(0, y, 0, y + barHeight);
            gradient.addColorStop(0, '#3b82f6');
            gradient.addColorStop(1, '#1e40af');
            
            ctx.fillStyle = gradient;
            ctx.fillRect(x, y, barWidth - 20, barHeight);
            
            // Draw value labels
            ctx.fillStyle = '#d1d5db';
            ctx.font = '12px sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText(value.toString(), x + (barWidth - 20) / 2, y - 5);
        });
    }

    // Graph interaction methods
    handleNodeClick(node) {
        this.showNodeDetails(node);
        this.highlightConnectedNotes(node);
    }

    handleNodeHover(node) {
        // Show tooltip or preview
        this.showNodeTooltip(node);
    }

    showNodeDetails(node) {
        const panel = document.getElementById('selected-node-panel');
        const content = document.getElementById('node-details-content');
        
        if (!panel || !content) return;
        
        content.innerHTML = `
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h5 class="text-lg font-semibold text-white">${this.escapeHtml(node.title)}</h5>
                    <button onclick="notesManager.openNote(${node.id})" class="btn-primary px-3 py-1 text-sm">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </button>
                </div>
                
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-400">Links:</span>
                        <span class="text-white">${node.incoming_links + node.outgoing_links}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Tags:</span>
                        <span class="text-white">${node.tags.length}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Updated:</span>
                        <span class="text-white">${this.formatDate(node.updated_at)}</span>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-1">
                    ${node.tags.map(tag => `<span class="note-tag">${tag}</span>`).join('')}
                </div>
                
                <div class="pt-2 border-t border-gray-700">
                    <button onclick="brain.highlightConnectedNodes(brain.notes.get(${node.id}))" class="btn-secondary px-3 py-1 text-sm mr-2">
                        <i class="fas fa-project-diagram mr-1"></i>Show Connections
                    </button>
                    <button onclick="linkCreator.sourceNode = brain.notes.get(${node.id}); linkCreator.toggleLinkMode()" class="btn-secondary px-3 py-1 text-sm">
                        <i class="fas fa-link mr-1"></i>Create Link
                    </button>
                </div>
            </div>
        `;
        
        panel.classList.remove('hidden');
    }

    showNodeTooltip(node) {
        // Implementation for hover tooltip
    }

    highlightConnectedNotes(node) {
        if (this.brain) {
            this.brain.highlightConnectedNodes(node);
        }
    }

    // Graph control methods
    relayoutGraph() {
        if (this.brain) {
            this.brain.layoutNodes();
            this.brain.redraw();
        }
    }

    centerGraph() {
        if (this.brain) {
            // Center the graph view
            this.brain.layoutNodes();
            this.brain.redraw();
        }
    }

    toggleFullscreen() {
        const graphContainer = document.getElementById('graph-container');
        if (graphContainer) {
            graphContainer.classList.toggle('fixed');
            graphContainer.classList.toggle('inset-0');
            graphContainer.classList.toggle('z-50');
            graphContainer.classList.toggle('bg-gray-900');
        }
    }

    updateNodeSize(size) {
        if (this.brain) {
            this.brain.nodeSize = parseInt(size);
            this.brain.redraw();
        }
    }

    updateLinkStrength(strength) {
        if (this.brain) {
            this.brain.linkWidth = parseFloat(strength);
            this.brain.redraw();
        }
    }

    toggleLabels() {
        if (this.brain) {
            this.brain.notes.forEach(node => {
                node.showLabel = !node.showLabel;
            });
            this.brain.redraw();
        }
    }

    // Quick action methods
    async createDailyNote() {
        const today = new Date().toISOString().split('T')[0];
        const title = `Daily Note - ${today}`;
        
        try {
            const response = await fetch('/src/api/notes.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    title,
                    content: `# Daily Note - ${today}\n\n## Goals for today\n\n## Notes\n\n## Reflection\n`,
                    tags: ['daily', 'journal'],
                    color: '#22c55e'
                })
            });
            
            if (response.ok) {
                const result = await response.json();
                window.notesManager?.openNote(result.id);
                this.showSuccess('Daily note created');
            }
        } catch (error) {
            this.showError('Failed to create daily note');
        }
    }

    openRandomNote() {
        const noteIds = Array.from(window.notesManager?.notes.keys() || []);
        if (noteIds.length > 0) {
            const randomId = noteIds[Math.floor(Math.random() * noteIds.length)];
            window.notesManager?.openNote(randomId);
        }
    }

    showUnlinkedNotes() {
        // Filter to show notes with no links
        window.notesManager?.filterNotes('unlinked');
    }

    async exportBackup() {
        try {
            const response = await fetch('/src/api/notes.php?archived=false&limit=1000');
            if (!response.ok) throw new Error('Failed to export notes');
            
            const data = await response.json();
            const backup = {
                exported_at: new Date().toISOString(),
                notes: data.notes,
                version: '1.0'
            };
            
            const blob = new Blob([JSON.stringify(backup, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            
            const a = document.createElement('a');
            a.href = url;
            a.download = `second-brain-backup-${new Date().toISOString().split('T')[0]}.json`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
            
            this.showSuccess('Backup exported successfully');
            
        } catch (error) {
            this.showError('Failed to export backup');
        }
    }

    // Utility methods
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
        notification.className = `fixed top-4 right-4 ${bgColor} text-white px-4 py-2 rounded-lg shadow-lg z-50`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 3000);
    }
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.secondBrainDashboard = new SecondBrainDashboard();
});
