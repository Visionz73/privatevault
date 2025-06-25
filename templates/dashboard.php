<!-- templates/dashboard.php -->
<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body { 
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
      min-height: 100vh;
      transition: background 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }
    @media (max-width: 768px) {
      main { margin-top: 4rem; }
    }

    /* macOS-Style Control Bar */
    .control-bar {
      position: fixed;
      top: 1rem;
      right: 1rem;
      z-index: 60;
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1rem;
      padding: 0.5rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    @media (max-width: 768px) {
      .control-bar {
        top: 5rem;
        right: 0.5rem;
        left: auto;
        transform: none;
        width: fit-content;
      }
    }

    .control-icon {
      width: 2rem;
      height: 2rem;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 0.5rem;
      display: flex;
      align-items: center;
      justify-content: center;
      color: rgba(255, 255, 255, 0.8);
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }

    .control-icon::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
      transition: left 0.5s ease;
    }

    .control-icon:hover::before {
      left: 100%;
    }

    .control-icon:hover {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.25);
      color: white;
      transform: translateY(-1px);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .control-icon.active {
      background: rgba(59, 130, 246, 0.3);
      border-color: rgba(59, 130, 246, 0.5);
      color: #93c5fd;
    }

    /* Gradient Picker Modal */
    .gradient-picker-modal {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.7);
      backdrop-filter: blur(5px);
      z-index: 9999;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 1rem;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .gradient-picker-modal.active {
      display: flex;
      opacity: 1;
    }

    .gradient-picker-content {
      background: rgba(30, 30, 30, 0.95);
      backdrop-filter: blur(30px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 1.5rem;
      box-shadow: 0 25px 80px rgba(0, 0, 0, 0.5);
      max-width: 500px;
      width: 100%;
      max-height: 80vh;
      overflow-y: auto;
      color: white;
      transform: scale(0.9) translateY(20px);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .gradient-picker-modal.active .gradient-picker-content {
      transform: scale(1) translateY(0);
    }

    .gradient-picker-header {
      padding: 1.5rem 1.5rem 1rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .gradient-picker-close {
      background: none;
      border: none;
      color: rgba(255, 255, 255, 0.6);
      font-size: 1.5rem;
      cursor: pointer;
      transition: color 0.3s ease;
      padding: 0;
      width: 2rem;
      height: 2rem;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 0.5rem;
    }

    .gradient-picker-close:hover {
      color: white;
      background: rgba(255, 255, 255, 0.1);
    }

    .gradient-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
      gap: 1rem;
      padding: 1.5rem;
    }

    .gradient-option {
      aspect-ratio: 16/9;
      border-radius: 1rem;
      cursor: pointer;
      position: relative;
      border: 2px solid transparent;
      transition: all 0.3s ease;
      overflow: hidden;
    }

    .gradient-option::before {
      content: '';
      position: absolute;
      inset: 0;
      background: inherit;
      border-radius: inherit;
    }

    .gradient-option::after {
      content: '✓';
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      color: white;
      font-size: 1.5rem;
      font-weight: bold;
      opacity: 0;
      transition: opacity 0.3s ease;
      text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
    }

    .gradient-option:hover {
      transform: scale(1.05);
      border-color: rgba(255, 255, 255, 0.3);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }

    .gradient-option.selected {
      border-color: rgba(59, 130, 246, 0.8);
      box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
    }

    .gradient-option.selected::after {
      opacity: 1;
    }

    .gradient-label {
      text-align: center;
      margin-top: 0.5rem;
      font-size: 0.75rem;
      color: rgba(255, 255, 255, 0.7);
      font-weight: 500;
    }

    /* Predefined Gradients */
    .gradient-cosmic { background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%); }
    .gradient-ocean { background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3730a3 100%); }
    .gradient-sunset { background: linear-gradient(135deg, #f59e0b 0%, #dc2626 50%, #7c2d12 100%); }
    .gradient-forest { background: linear-gradient(135deg, #064e3b 0%, #047857 50%, #065f46 100%); }
    .gradient-purple { background: linear-gradient(135deg, #581c87 0%, #7c3aed 50%, #3730a3 100%); }
    .gradient-rose { background: linear-gradient(135deg, #9f1239 0%, #e11d48 50%, #881337 100%); }
    .gradient-cyber { background: linear-gradient(135deg, #065f46 0%, #0891b2 50%, #1e40af 100%); }
    .gradient-ember { background: linear-gradient(135deg, #7c2d12 0%, #ea580c 50%, #92400e 100%); }

    /* Dashboard Shorts - Konsistentes dunkles Glassmorphism */
    .dashboard-short {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1.5rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      position: relative;
      overflow: hidden;
    }

    /* Simple Header Styling - No more separate background */
    .short-header {
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .short-header:hover h3 {
      color: rgba(255, 255, 255, 1);
    }

    /* Finance Header - Special styling for balance display */
    .finance-header {
      backdrop-filter: blur(10px);
    }

    /* Stats Numbers - Weißer Text für bessere Lesbarkeit */
    .stats-number {
      color: white;
      font-weight: 800;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    /* Quick Action Buttons */
    .quick-action-btn {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      border-radius: 0.75rem;
      transition: all 0.3s ease;
      font-size: 0.875rem;
      font-weight: 500;
    }
    .quick-action-btn:hover {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.3);
      transform: translateY(-1px);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    }

    /* List Items in Shorts - Only these have hover animations */
    .short-list-item {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 0.75rem;
      transition: all 0.3s ease;
      cursor: pointer;
    }
    .short-list-item:hover {
      background: rgba(255, 255, 255, 0.1);
      border-color: rgba(255, 255, 255, 0.2);
      transform: translateX(4px);
    }

    /* Greeting Text */
    .greeting-text {
      color: white;
      text-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    /* Badge Styles */
    .status-badge {
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      padding: 4px 10px;
      backdrop-filter: blur(10px);
    }
    .badge-pending { 
      background: rgba(251, 191, 36, 0.2); 
      color: #fbbf24; 
      border: 1px solid rgba(251, 191, 36, 0.3); 
    }
    .badge-completed { 
      background: rgba(34, 197, 94, 0.2); 
      color: #86efac; 
      border: 1px solid rgba(34, 197, 94, 0.3); 
    }
    .badge-overdue { 
      background: rgba(239, 68, 68, 0.2); 
      color: #fca5a5; 
      border: 1px solid rgba(239, 68, 68, 0.3); 
    }

    /* Progress Bars */
    .progress-bar {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 8px;
      overflow: hidden;
    }
    .progress-fill {
      background: linear-gradient(90deg, #3b82f6, #1d4ed8);
      height: 8px;
      border-radius: 8px;
      transition: width 0.5s ease;
    }

    /* Text Colors */
    .text-primary { color: white !important; }
    .text-secondary { color: rgba(255, 255, 255, 0.8) !important; }
    .text-muted { color: rgba(255, 255, 255, 0.6) !important; }

    /* Member Badge für Gruppen */
    .member-badge {
      background: rgba(147, 51, 234, 0.2);
      color: #c4b5fd;
      border: 1px solid rgba(147, 51, 234, 0.3);
      padding: 2px 8px;
      border-radius: 9999px;
      font-size: 0.75rem;
      backdrop-filter: blur(10px);
    }

    /* Scrollbar styling */
    .short-scroll {
      max-height: 280px;
      overflow-y: auto;
      scrollbar-width: thin;
      scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
    }
    .short-scroll::-webkit-scrollbar {
      width: 6px;
    }
    .short-scroll::-webkit-scrollbar-track {
      background: transparent;
    }
    .short-scroll::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.2);
      border-radius: 3px;
    }

    /* Notes App Styles */
    .notes-app-modal {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.8);
      backdrop-filter: blur(10px);
      z-index: 9999;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 1rem;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .notes-app-modal.active {
      display: flex;
      opacity: 1;
    }

    .notes-app-content {
      background: rgba(30, 30, 30, 0.95);
      backdrop-filter: blur(30px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 1.5rem;
      box-shadow: 0 25px 80px rgba(0, 0, 0, 0.5);
      width: 100%;
      max-width: 1200px;
      max-height: 90vh;
      overflow: hidden;
      color: white;
      transform: scale(0.9) translateY(20px);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .notes-app-modal.active .notes-app-content {
      transform: scale(1) translateY(0);
    }

    .notes-app-header {
      padding: 1.5rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .notes-app-body {
      padding: 1.5rem;
      max-height: 60vh;
      overflow-y: auto;
    }

    .notes-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 1rem;
    }

    .note-card {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1rem;
      padding: 1rem;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .note-card:hover {
      background: rgba(255, 255, 255, 0.12);
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }

    .note-card-header {
      display: flex;
      justify-content: space-between;
      align-items: start;
      margin-bottom: 0.75rem;
    }

    .note-title {
      font-size: 1rem;
      font-weight: 600;
      color: white;
      margin: 0;
      line-height: 1.3;
    }

    .note-actions {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .note-card:hover .note-actions {
      opacity: 1;
    }

    .note-content {
      color: rgba(255, 255, 255, 0.8);
      font-size: 0.875rem;
      line-height: 1.5;
      margin-bottom: 0.75rem;
      max-height: 120px;
      overflow: hidden;
      display: -webkit-box;
      -webkit-line-clamp: 5;
      -webkit-box-orient: vertical;
    }

    .note-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 0.75rem;
      color: rgba(255, 255, 255, 0.5);
    }

    .note-tags {
      display: flex;
      gap: 0.25rem;
      flex-wrap: wrap;
    }

    .note-tag {
      background: rgba(255, 255, 255, 0.1);
      padding: 0.125rem 0.5rem;
      border-radius: 0.75rem;
      font-size: 0.625rem;
    }

    /* Note Editor Modal */
    .note-editor-modal {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.8);
      backdrop-filter: blur(10px);
      z-index: 10000;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 1rem;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .note-editor-modal.active {
      display: flex;
      opacity: 1;
    }

    .note-editor-content {
      background: rgba(30, 30, 30, 0.95);
      backdrop-filter: blur(30px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 1.5rem;
      box-shadow: 0 25px 80px rgba(0, 0, 0, 0.5);
      width: 100%;
      max-width: 600px;
      max-height: 90vh;
      overflow: hidden;
      color: white;
      transform: scale(0.9) translateY(20px);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .note-editor-modal.active .note-editor-content {
      transform: scale(1) translateY(0);
    }

    .note-editor-header {
      padding: 1.5rem 1.5rem 1rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .note-editor-body {
      padding: 1.5rem;
      max-height: 70vh;
      overflow-y: auto;
    }

    .note-action-btn {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: rgba(255, 255, 255, 0.7);
      border-radius: 0.5rem;
      padding: 0.5rem;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .note-action-btn:hover {
      background: rgba(255, 255, 255, 0.15);
      color: white;
    }

    .note-action-btn.active {
      background: rgba(251, 191, 36, 0.3);
      color: #fbbf24;
      border-color: rgba(251, 191, 36, 0.5);
    }

    .note-color-btn {
      width: 2rem;
      height: 2rem;
      border: 2px solid transparent;
      border-radius: 50%;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .note-color-btn:hover {
      transform: scale(1.1);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    }

    .note-color-btn.active {
      border-color: white;
      box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.3);
    }

    .notes-btn-primary {
      background: linear-gradient(135deg, rgba(59, 130, 246, 0.8) 0%, rgba(37, 99, 235, 0.8) 100%);
      color: white;
      border: 1px solid rgba(59, 130, 246, 0.3);
      border-radius: 0.5rem;
      padding: 0.5rem 1rem;
      font-weight: 500;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .notes-btn-primary:hover {
      background: linear-gradient(135deg, rgba(59, 130, 246, 0.9) 0%, rgba(37, 99, 235, 0.9) 100%);
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(59, 130, 246, 0.3);
    }

    .notes-btn-secondary {
      background: rgba(255, 255, 255, 0.1);
      color: rgba(255, 255, 255, 0.8);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 0.5rem;
      padding: 0.5rem 1rem;
      font-weight: 500;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .notes-btn-secondary:hover {
      background: rgba(255, 255, 255, 0.15);
      color: white;
    }

    /* Line clamp utility */
    .line-clamp-2 {
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
      .notes-grid {
        grid-template-columns: 1fr;
      }
      
      .notes-app-content {
        margin: 0.5rem;
        max-width: none;
      }
      
      .note-editor-content {
        margin: 0.5rem;
        max-width: none;
      }
    }

    /* Node View Styles */
    .node-view-container {
      position: relative;
      width: 100%;
      height: 400px;
      overflow: hidden;
      border-radius: 1rem;
      background: rgba(0, 0, 0, 0.2);
    }

    .node-canvas {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
    }

    .note-node {
      position: absolute;
      width: 120px;
      height: 80px;
      border-radius: 8px;
      padding: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 0.75rem;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
      border: 2px solid transparent;
    }

    .note-node:hover {
      transform: scale(1.05);
      border-color: rgba(255, 255, 255, 0.3);
      z-index: 10;
    }

    .note-node.pinned {
      border-color: #fbbf24;
      box-shadow: 0 0 0 2px rgba(251, 191, 36, 0.3);
    }

    .node-title {
      font-weight: 600;
      color: white;
      line-height: 1.2;
      overflow: hidden;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
    }

    .node-preview {
      color: rgba(255, 255, 255, 0.7);
      font-size: 0.625rem;
      overflow: hidden;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
    }

    .view-toggle-buttons {
      display: flex;
      gap: 0.5rem;
      margin-bottom: 1rem;
    }

    .view-toggle-btn {
      padding: 0.375rem 0.75rem;
      border-radius: 0.5rem;
      border: 1px solid rgba(255, 255, 255, 0.2);
      background: rgba(255, 255, 255, 0.1);
      color: rgba(255, 255, 255, 0.7);
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 0.75rem;
    }

    .view-toggle-btn.active {
      background: rgba(59, 130, 246, 0.3);
      border-color: rgba(59, 130, 246, 0.5);
      color: #93c5fd;
    }

    .view-toggle-btn:hover {
      background: rgba(255, 255, 255, 0.15);
      color: white;
    }

    /* Enhanced Graph Controls */
    .graph-control-btn {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      padding: 0.5rem;
      border-radius: 0.5rem;
      cursor: pointer;
      transition: all 0.2s ease;
      backdrop-filter: blur(10px);
    }

    .graph-control-btn:hover {
      background: rgba(255, 255, 255, 0.2);
      border-color: rgba(255, 255, 255, 0.4);
      transform: scale(1.05);
    }

    .graph-control-btn.active {
      background: rgba(59, 130, 246, 0.5);
      border-color: rgba(59, 130, 246, 0.8);
    }

    /* Enhanced Node Types */
    .note-node.daily-node {
      border-left: 4px solid #22c55e;
    }

    .note-node.idea-node {
      border-left: 4px solid #8b5cf6;
    }

    .note-node.documentation-node {
      border-left: 4px solid #3b82f6;
    }

    .note-node.knowledge-node {
      border-left: 4px solid #f59e0b;
    }

    .note-node.selected {
      border-color: #60a5fa !important;
      box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.3);
      z-index: 20;
    }

    .note-node.connected {
      border-color: #34d399 !important;
      box-shadow: 0 0 0 2px rgba(52, 211, 153, 0.2);
    }

    /* Filter Tags */
    .filter-tag-btn {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      padding: 0.25rem 0.5rem;
      border-radius: 0.375rem;
      font-size: 0.75rem;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .filter-tag-btn:hover {
      background: rgba(255, 255, 255, 0.2);
    }

    .filter-tag-btn.active {
      background: rgba(59, 130, 246, 0.5);
      border-color: rgba(59, 130, 246, 0.8);
    }

    /* Node Links/Connections */
    .node-link {
      stroke: rgba(255, 255, 255, 0.3);
      stroke-width: 2;
      fill: none;
      pointer-events: none;
      transition: all 0.3s ease;
    }

    .node-link.highlighted {
      stroke: #60a5fa;
      stroke-width: 3;
      stroke-dasharray: 5,5;
      animation: dash 1s linear infinite;
    }

    @keyframes dash {
      to {
        stroke-dashoffset: -10;
      }
    }

    /* Node Tooltip Enhanced */
    .note-node-tooltip {
      position: absolute;
      bottom: 100%;
      left: 50%;
      transform: translateX(-50%);
      background: rgba(0, 0, 0, 0.9);
      color: white;
      padding: 0.5rem;
      border-radius: 0.5rem;
      font-size: 0.75rem;
      white-space: nowrap;
      pointer-events: none;
      opacity: 0;
      transition: opacity 0.2s ease;
      z-index: 1000;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      max-width: 200px;
      white-space: normal;
      text-align: center;
    }

    .note-node:hover .note-node-tooltip {
      opacity: 1;
    }

    /* Node Count Info */
    .node-info {
      position: absolute;
      bottom: 4px;
      right: 4px;
      display: flex;
      gap: 2px;
    }

    .node-info-badge {
      background: rgba(0, 0, 0, 0.5);
      color: white;
      padding: 1px 4px;
      border-radius: 8px;
      font-size: 8px;
      font-weight: bold;
    }

    .link-count { background: rgba(34, 197, 94, 0.7); }
    .tag-count { background: rgba(59, 130, 246, 0.7); }

    /* Graph Zoom Controls */
    .graph-zoom-controls {
      position: absolute;
      bottom: 20px;
      left: 20px;
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    /* Node Connection Lines */
    .node-connections {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: 1;
    }

    /* Node Canvas Enhanced */
    .node-canvas {
      position: relative;
      width: 100%;
      height: 100%;
      min-height: 500px;
      overflow: hidden;
      cursor: grab;
    }

    .node-canvas:active {
      cursor: grabbing;
    }
  </style>
</head>
<body class="min-h-screen">
  <?php require_once __DIR__.'/navbar.php'; ?>

  <!-- macOS-Style Control Bar -->
  <div class="control-bar">
    <div class="control-icon" onclick="openGradientPicker()" title="Hintergrund-Gradient ändern">
      <i class="fas fa-palette text-sm"></i>
    </div>
    <div class="control-icon" onclick="toggleTheme()" title="Theme wechseln">
      <i class="fas fa-moon text-sm"></i>
    </div>
    <div class="control-icon" onclick="toggleCompactMode()" title="Kompakter Modus">
      <i class="fas fa-compress text-sm"></i>
    </div>
    <div class="control-icon" onclick="openNotificationSettings()" title="Benachrichtigungen">
      <i class="fas fa-bell text-sm"></i>
    </div>
    <div class="control-icon" onclick="openLayoutSettings()" title="Layout anpassen">
      <i class="fas fa-th text-sm"></i>
    </div>
    <div class="control-icon" onclick="openSystemSettings()" title="System-Einstellungen">
      <i class="fas fa-cog text-sm"></i>
    </div>
  </div>

  <!-- Gradient Picker Modal -->
  <div id="gradientPickerModal" class="gradient-picker-modal">
    <div class="gradient-picker-content">
      <div class="gradient-picker-header">
        <h3 class="text-lg font-semibold">Hintergrund-Gradient wählen</h3>
        <button class="gradient-picker-close" onclick="closeGradientPicker()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <div class="gradient-grid">
        <div class="gradient-option-container">
          <div class="gradient-option gradient-cosmic" data-gradient="cosmic" onclick="selectGradient('cosmic')"></div>
          <div class="gradient-label">Cosmic (Standard)</div>
        </div>
        
        <div class="gradient-option-container">
          <div class="gradient-option gradient-ocean" data-gradient="ocean" onclick="selectGradient('ocean')"></div>
          <div class="gradient-label">Ocean Blue</div>
        </div>
        
        <div class="gradient-option-container">
          <div class="gradient-option gradient-sunset" data-gradient="sunset" onclick="selectGradient('sunset')"></div>
          <div class="gradient-label">Sunset Fire</div>
        </div>
        
        <div class="gradient-option-container">
          <div class="gradient-option gradient-forest" data-gradient="forest" onclick="selectGradient('forest')"></div>
          <div class="gradient-label">Forest Green</div>
        </div>
        
        <div class="gradient-option-container">
          <div class="gradient-option gradient-purple" data-gradient="purple" onclick="selectGradient('purple')"></div>
          <div class="gradient-label">Royal Purple</div>
        </div>
        
        <div class="gradient-option-container">
          <div class="gradient-option gradient-rose" data-gradient="rose" onclick="selectGradient('rose')"></div>
          <div class="gradient-label">Rose Garden</div>
        </div>
        
        <div class="gradient-option-container">
          <div class="gradient-option gradient-cyber" data-gradient="cyber" onclick="selectGradient('cyber')"></div>
          <div class="gradient-label">Cyber Teal</div>
        </div>
        
        <div class="gradient-option-container">
          <div class="gradient-option gradient-ember" data-gradient="ember" onclick="selectGradient('ember')"></div>
          <div class="gradient-label">Ember Glow</div>
        </div>
      </div>
    </div>
  </div>

  <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-6 space-y-8" style="padding-top: 6rem;">
    <!-- Dynamic Greeting -->
    <div class="text-center mb-12">
      <h1 class="text-4xl md:text-6xl font-bold greeting-text mb-4">
        <?php
        $hour = date('H');
        $greeting = $hour < 12 ? 'Guten Morgen' : ($hour < 18 ? 'Guten Tag' : 'Guten Abend');
        echo $greeting;
        ?>,
        <?= htmlspecialchars($user['first_name'] ?? $user['username']) ?>
      </h1>
      <p class="text-xl text-white/70">
        <?= date('l, d. F Y') ?> • Willkommen in deinem Dashboard
      </p>
    </div>

    <!-- Dashboard Shorts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-8">
      
      <!-- Tasks Short -->
      <div class="dashboard-short col-span-1 md:col-span-2 xl:col-span-1">
        <div class="short-header p-6" onclick="window.location.href='inbox.php'">
          <div class="flex items-center justify-between">
            <h3 class="text-white font-semibold text-xl">Inbox</h3>
            <div class="text-right">
              <div class="stats-number text-3xl"><?= $openTaskCount ?></div>
              <div class="text-white/60 text-sm">offen</div>
            </div>
          </div>
        </div>
        
        <div class="p-6">
          <div class="short-scroll space-y-3">
            <?php if (!empty($tasks)): ?>
              <?php foreach(array_slice($tasks, 0, 5) as $task): ?>
                <div class="short-list-item p-4" onclick="window.location.href='task_detail.php?id=<?= $task['id'] ?>'">
                  <div class="flex justify-between items-start mb-2">
                    <h4 class="text-white font-medium text-sm truncate flex-1"><?= htmlspecialchars($task['title']) ?></h4>
                    <?php if(isset($task['due_date']) && $task['due_date']): ?>
                      <span class="status-badge <?= strtotime($task['due_date']) < time() ? 'badge-overdue' : 'badge-pending' ?> ml-2">
                        <?= date('d.m.', strtotime($task['due_date'])) ?>
                      </span>
                    <?php endif; ?>
                  </div>
                  <?php if(!empty($task['description'])): ?>
                    <p class="text-white/60 text-xs truncate"><?= htmlspecialchars($task['description']) ?></p>
                  <?php endif; ?>
                  <div class="flex justify-between text-xs text-white/50 mt-2">
                    <span>Von: <?= htmlspecialchars($task['creator_name'] ?? 'Unbekannt') ?></span>
                    <span><?= $task['assigned_group_id'] ? 'Gruppe' : 'Persönlich' ?></span>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="text-center py-8">
                <p class="text-white/60">Keine offenen Aufgaben</p>
              </div>
            <?php endif; ?>
          </div>
          
          <div class="mt-6 grid grid-cols-2 gap-3">
            <button onclick="window.location.href='inbox.php'" class="quick-action-btn px-4 py-2">
              Inbox
            </button>
            <button onclick="window.location.href='create_task.php'" class="quick-action-btn px-4 py-2">
              Neue Aufgabe
            </button>
          </div>
        </div>
      </div>

      <!-- Calendar Short -->
      <div class="dashboard-short">
        <div class="short-header p-6" onclick="window.location.href='calendar.php'">
          <div class="flex items-center justify-between">
            <h3 class="text-white font-semibold text-xl">Kalender</h3>
            <div class="text-right">
              <div class="stats-number text-3xl"><?= count($upcomingEvents ?? []) ?></div>
              <div class="text-white/60 text-sm">heute</div>
            </div>
          </div>
        </div>
        
        <div class="p-6">
          <div class="short-scroll space-y-3">
            <?php if (!empty($upcomingEvents)): ?>
              <?php foreach(array_slice($upcomingEvents, 0, 4) as $event): ?>
                <div class="short-list-item p-4" onclick="window.location.href='calendar.php'">
                  <div class="flex justify-between items-start mb-2">
                    <h4 class="text-white font-medium text-sm truncate flex-1"><?= htmlspecialchars($event['title']) ?></h4>
                    <span class="text-blue-400 text-xs ml-2"><?= date('H:i', strtotime($event['event_date'])) ?></span>
                  </div>
                  <?php if(!empty($event['description'])): ?>
                    <p class="text-white/60 text-xs truncate"><?= htmlspecialchars($event['description']) ?></p>
                  <?php endif; ?>
                  <div class="text-xs text-white/50 mt-2">
                    <span><?= date('d.m.Y', strtotime($event['event_date'])) ?></span>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="text-center py-6">
                <p class="text-white/60 text-sm">Keine Termine geplant</p>
              </div>
            <?php endif; ?>
          </div>
          
          <div class="mt-6">
            <button onclick="window.location.href='calendar.php'" class="quick-action-btn w-full px-4 py-2">
              Neuer Termin
            </button>
          </div>
        </div>
      </div>

      <!-- Documents Short -->
      <div class="dashboard-short">
        <div class="short-header p-6" onclick="window.location.href='profile.php?tab=documents'">
          <div class="flex items-center justify-between">
            <h3 class="text-white font-semibold text-xl">Dokumente</h3>
            <div class="text-right">
              <div class="stats-number text-3xl"><?= $docCount ?></div>
              <div class="text-white/60 text-sm">gesamt</div>
            </div>
          </div>
        </div>
        
        <div class="p-6">
          <div class="short-scroll space-y-3">
            <?php if (!empty($recentDocuments)): ?>
              <?php foreach(array_slice($recentDocuments, 0, 4) as $doc): ?>
                <div class="short-list-item p-4" onclick="window.location.href='profile.php?tab=documents'">
                  <div class="flex items-center space-x-3">
                    <div class="icon-gradient-green p-2 rounded-lg">
                      <i class="fas fa-file text-white text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                      <h4 class="text-white font-medium text-sm truncate"><?= htmlspecialchars($doc['filename']) ?></h4>
                      <p class="text-white/60 text-xs"><?= date('d.m.Y', strtotime($doc['upload_date'])) ?></p>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="text-center py-6">
                <p class="text-white/60 text-sm">Keine Dokumente hochgeladen</p>
              </div>
            <?php endif; ?>
          </div>
          
          <div class="mt-6">
            <button onclick="window.location.href='profile.php?tab=documents'" class="quick-action-btn w-full px-4 py-2">
              Hochladen
            </button>
          </div>
        </div>
      </div>

      <!-- Notes Widget - Add this new widget after Documents Short -->
      <div class="dashboard-short col-span-1 md:col-span-2 xl:col-span-1">
        <div class="short-header p-6" onclick="toggleNotesApp()">
          <div class="flex items-center justify-between">
            <h3 class="text-white font-semibold text-xl">Quick Notes</h3>
            <div class="text-right">
              <div class="stats-number text-3xl" id="notesCount">0</div>
              <div class="text-white/60 text-sm">notizen</div>
            </div>
          </div>
        </div>
        
        <div class="p-6">
          <!-- Quick Add Form -->
          <div class="mb-4" id="quickNoteForm">
            <div class="flex gap-2">
              <input 
                type="text" 
                id="quickNoteTitle" 
                placeholder="Schnelle Notiz..." 
                class="flex-1 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white text-sm placeholder-white/50 focus:bg-white/15 focus:border-white/30 focus:outline-none"
              >
              <button 
                onclick="addQuickNote()" 
                class="quick-action-btn px-3 py-2 text-sm"
                title="Notiz hinzufügen"
              >
                <i class="fas fa-plus"></i>
              </button>
            </div>
          </div>
          
          <!-- Notes List -->
          <div class="short-scroll space-y-2" id="notesList">
            <div class="text-center py-6 text-white/60" id="notesEmptyState">
              <i class="fas fa-sticky-note text-2xl mb-2"></i>
              <p class="text-sm">Keine Notizen vorhanden</p>
            </div>
          </div>
          
          <div class="mt-6 grid grid-cols-2 gap-3">
            <button onclick="toggleNotesApp()" class="quick-action-btn px-4 py-2">
              <i class="fas fa-expand-alt mr-1"></i>
              Alle Notizen
            </button>
            <button onclick="openNoteEditor()" class="quick-action-btn px-4 py-2">
              <i class="fas fa-edit mr-1"></i>
              Neue Notiz
            </button>
          </div>
        </div>
      </div>

      <!-- HaveToPay Short - Keep existing balance layout -->
      <div class="dashboard-short">
        <div class="finance-header p-6">
          <div class="flex items-center justify-between">
            <a href="havetopay.php" class="text-white font-semibold text-xl hover:text-white/80 transition-colors">
              Finanzen
            </a>
            <div class="text-right">
              <div class="stats-number text-3xl <?= $widgetNetBalance >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                <?= number_format($widgetNetBalance, 0) ?>€
              </div>
              <div class="text-white/60 text-sm">Bilanz</div>
            </div>
          </div>
        </div>
        
        <div class="p-6">
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="text-center p-3 bg-green-500/10 border border-green-400/20 rounded-xl backdrop-filter blur-10">
              <div class="text-green-400 font-bold text-lg">+<?= number_format($widgetTotalOwed, 0) ?>€</div>
              <div class="text-white/60 text-xs">Du bekommst</div>
            </div>
            <div class="text-center p-3 bg-red-500/10 border border-red-400/20 rounded-xl backdrop-filter blur-10">
              <div class="text-red-400 font-bold text-lg">-<?= number_format($widgetTotalOwing, 0) ?>€</div>
              <div class="text-white/60 text-xs">Du schuldest</div>
            </div>
          </div>
          
          <div class="short-scroll space-y-2">
            <?php if (!empty($recentExpenses)): ?>
              <?php foreach(array_slice($recentExpenses, 0, 3) as $expense): ?>
                <div class="short-list-item p-3" onclick="window.location.href='havetopay_detail.php?id=<?= $expense['id'] ?>'">
                  <div class="flex justify-between items-center">
                    <div class="flex-1 min-w-0">
                      <h4 class="text-white font-medium text-sm truncate"><?= htmlspecialchars($expense['title']) ?></h4>
                      <p class="text-white/60 text-xs">€<?= number_format($expense['amount'], 2) ?></p>
                    </div>
                    <span class="status-badge badge-pending"><?= date('d.m.', strtotime($expense['expense_date'])) ?></span>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="text-center py-4">
                <i class="fas fa-coins text-white/30 text-2xl mb-2"></i>
                <p class="text-white/60 text-sm">Keine Ausgaben</p>
              </div>
            <?php endif; ?>
          </div>
          
          <div class="mt-6">
            <button onclick="window.location.href='havetopay.php'" class="quick-action-btn w-full px-4 py-2">
              Ausgabe hinzufügen
            </button>
          </div>
        </div>
      </div>

      <!-- System Stats Short -->
      <div class="dashboard-short">
        <div class="short-header p-6" onclick="window.location.href='profile.php'">
          <h3 class="text-white font-semibold text-xl">Statistiken</h3>
        </div>
        
        <div class="p-6 space-y-4">
          <div class="flex justify-between items-center">
            <span class="text-white/80 text-sm">Aufgaben erledigt</span>
            <span class="text-white font-semibold"><?= $completedTasksCount ?? 0 ?></span>
          </div>
          <div class="progress-bar">
            <div class="progress-fill" style="width: <?= min(100, ($completedTasksCount ?? 0) * 10) ?>%"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-white/80 text-sm">Dokumente</span>
            <span class="text-white font-semibold"><?= $docCount ?></span>
          </div>
          <div class="progress-bar">
            <div class="progress-fill bg-gradient-to-r from-green-500 to-green-400" style="width: <?= min(100, $docCount * 5) ?>%"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-white/80 text-sm">Termine</span>
            <span class="text-white font-semibold"><?= count($upcomingEvents ?? []) ?></span>
          </div>
          <div class="progress-bar">
            <div class="progress-fill bg-gradient-to-r from-purple-500 to-purple-400" style="width: <?= min(100, count($upcomingEvents ?? []) * 20) ?>%"></div>
          </div>
          
          <div class="mt-6">
            <button onclick="window.location.href='profile.php'" class="quick-action-btn w-full px-4 py-2">
              <i class="fas fa-user mr-2"></i>Profil
            </button>
          </div>
        </div>
      </div>

      <!-- Quick Actions Short -->
      <div class="dashboard-short col-span-1 md:col-span-2 xl:col-span-1">
        <div class="short-header p-6">
          <h3 class="text-white font-semibold text-xl">Schnellaktionen</h3>
        </div>
        
        <div class="p-6">
          <div class="grid grid-cols-2 gap-4">
            <button onclick="window.location.href='create_task.php'" class="quick-action-btn p-4 text-center">
              <i class="fas fa-plus text-2xl mb-2"></i>
              <div class="text-sm">Neue Aufgabe</div>
            </button>
            <button onclick="window.location.href='calendar.php'" class="quick-action-btn p-4 text-center">
              <i class="fas fa-calendar-plus text-2xl mb-2"></i>
              <div class="text-sm">Termin</div>
            </button>
            <button onclick="window.location.href='havetopay_add.php'" class="quick-action-btn p-4 text-center">
              <i class="fas fa-receipt text-2xl mb-2"></i>
              <div class="text-sm">Ausgabe</div>
            </button>
            <button onclick="window.location.href='profile.php?tab=documents'" class="quick-action-btn p-4 text-center">
              <i class="fas fa-upload text-2xl mb-2"></i>
              <div class="text-sm">Upload</div>
            </button>
            <button onclick="window.location.href='admin/groups.php'" class="quick-action-btn p-4 text-center">
              <i class="fas fa-users text-2xl mb-2"></i>
              <div class="text-sm">Gruppen</div>
            </button>
          </div>
        </div>
      </div>

    </div>

    <!-- Recent Activity -->
    <div class="dashboard-short mt-8">
      <div class="short-header p-6">
        <h3 class="text-white font-semibold text-xl">Letzte Aktivität</h3>
      </div>
      
      <div class="p-6">
        <div class="short-scroll space-y-3 max-h-48">
          <div class="short-list-item p-4">
            <div class="flex items-center space-x-3">
              <div class="icon-gradient-green p-2 rounded-full w-8 h-8 flex items-center justify-center">
                <i class="fas fa-check text-white text-xs"></i>
              </div>
              <div>
                <p class="text-white text-sm">Dashboard wurde geladen</p>
                <p class="text-white/60 text-xs">vor wenigen Sekunden</p>
              </div>
            </div>
          </div>
          <!-- Add more activities dynamically here -->
        </div>
      </div>
    </div>

  </main>
  
  <!-- Notes App Modal -->
  <div id="notesAppModal" class="notes-app-modal">
    <div class="notes-app-content">
      <div class="notes-app-header">
        <div class="flex items-center justify-between">
          <h2 class="text-xl font-bold text-white">Quick Notes</h2>
          <div class="flex items-center gap-3">
            <button onclick="openNoteEditor()" class="notes-btn-primary">
              <i class="fas fa-plus mr-2"></i>Neue Notiz
            </button>
            <button onclick="toggleNotesApp()" class="notes-btn-secondary">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
        
        <!-- Search and Filter -->
        <div class="flex gap-3 mt-4">
          <div class="flex-1 relative">
            <i class="fas fa-search absolute left-3 top-3 text-white/40"></i>
            <input 
              type="text" 
              id="notesSearch" 
              placeholder="Notizen durchsuchen..." 
              class="w-full pl-10 pr-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:bg-white/15 focus:border-white/30 focus:outline-none"
            >
          </div>
          <button onclick="toggleArchived()" class="notes-btn-secondary" id="archiveToggle">
            <i class="fas fa-archive mr-1"></i>Archiv
          </button>
        </div>

        <!-- View Toggle -->
        <div class="view-toggle-buttons mt-4">
          <button class="view-toggle-btn active" id="gridViewBtn" onclick="switchNotesView('grid')">
            <i class="fas fa-th mr-1"></i>Grid
          </button>
          <button class="view-toggle-btn" id="nodeViewBtn" onclick="switchNotesView('node')">
            <i class="fas fa-project-diagram mr-1"></i>Knoten
          </button>
          <button class="view-toggle-btn" id="listViewBtn" onclick="switchNotesView('list')">
            <i class="fas fa-list mr-1"></i>Liste
          </button>
        </div>
      </div>
      
      <div class="notes-app-body">
        <!-- Grid View -->
        <div class="notes-grid" id="notesGrid" style="display: block;">
          <!-- Notes will be loaded here -->
        </div>        <!-- Node View - Enhanced Second Brain Graph -->
        <div class="node-view-container" id="nodeView" style="display: none;">
          <!-- Graph Controls -->
          <div class="absolute top-4 left-4 z-10">
            <div class="bg-black/20 backdrop-blur-sm rounded-lg p-3 space-y-2">
              <div class="flex items-center space-x-2">
                <button id="graph-layout-btn" class="graph-control-btn" title="Re-layout nodes">
                  <i class="fas fa-sitemap"></i>
                </button>
                <button id="graph-center-btn" class="graph-control-btn" title="Center graph">
                  <i class="fas fa-crosshairs"></i>
                </button>
                <button id="graph-zoom-fit" class="graph-control-btn" title="Fit to screen">
                  <i class="fas fa-expand-arrows-alt"></i>
                </button>
              </div>
              
              <!-- Node Size Control -->
              <div class="flex items-center space-x-2 text-white/70 text-xs">
                <span>Size:</span>
                <input type="range" id="node-size-slider" min="0.5" max="2" step="0.1" value="1" 
                       class="w-16 h-1 bg-white/20 rounded-lg">
              </div>
              
              <!-- Link Strength Control -->
              <div class="flex items-center space-x-2 text-white/70 text-xs">
                <span>Links:</span>
                <input type="range" id="link-strength-slider" min="0.2" max="2" step="0.1" value="1" 
                       class="w-16 h-1 bg-white/20 rounded-lg">
              </div>
              
              <!-- Toggle Labels -->
              <button id="toggle-labels-btn" class="graph-control-btn w-full text-xs" title="Toggle labels">
                <i class="fas fa-tag mr-1"></i>Labels
              </button>
            </div>
          </div>
          
          <!-- Graph Canvas -->
          <div class="node-canvas" id="nodeCanvas">
            <!-- Node visualization will be rendered here -->
          </div>
          
          <!-- Graph Info Panel -->
          <div class="absolute top-4 right-4 z-10">
            <div class="bg-black/20 backdrop-blur-sm rounded-lg p-3 text-white/70 text-sm">
              <div class="flex items-center space-x-4">
                <div class="text-center">
                  <div class="text-lg font-bold text-white" id="graph-nodes-count">0</div>
                  <div class="text-xs">Notizen</div>
                </div>
                <div class="text-center">
                  <div class="text-lg font-bold text-white" id="graph-links-count">0</div>
                  <div class="text-xs">Links</div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Selected Node Panel -->
          <div id="selected-node-panel" class="absolute bottom-4 left-4 z-10 hidden">
            <div class="bg-black/40 backdrop-blur-sm rounded-lg p-4 max-w-xs">
              <div id="node-details-content">
                <!-- Node details will be shown here -->
              </div>
            </div>
          </div>
          
          <!-- Filter Panel -->
          <div class="absolute bottom-4 right-4 z-10">
            <div class="bg-black/20 backdrop-blur-sm rounded-lg p-3">
              <div class="text-white/70 text-xs mb-2">Filter:</div>
              <div class="flex flex-wrap gap-1">
                <button class="filter-tag-btn active" data-filter="all">All</button>
                <button class="filter-tag-btn" data-filter="daily">Daily</button>
                <button class="filter-tag-btn" data-filter="ideas">Ideas</button>
                <button class="filter-tag-btn" data-filter="docs">Docs</button>
                <button class="filter-tag-btn" data-filter="knowledge">Knowledge</button>
              </div>
            </div>
          </div>
        </div>

        <!-- List View -->
        <div id="listView" style="display: none;">
          <div class="space-y-2" id="notesList">
            <!-- List view will be populated here -->
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Note Editor Modal -->
  <div id="noteEditorModal" class="note-editor-modal">
    <div class="note-editor-content">
      <div class="note-editor-header">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold text-white">Notiz bearbeiten</h3>
          <div class="flex items-center gap-2">
            <button onclick="toggleNotePin()" class="note-action-btn" id="pinBtn" title="Anheften">
              <i class="fas fa-thumbtack"></i>
            </button>
            <button onclick="deleteCurrentNote()" class="note-action-btn text-red-400" id="deleteBtn" title="Löschen">
              <i class="fas fa-trash"></i>
            </button>
            <button onclick="closeNoteEditor()" class="note-action-btn">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
      </div>
      
      <div class="note-editor-body">
        <form id="noteForm">
          <input type="hidden" id="noteId" name="id">
          
          <div class="mb-4">
            <input 
              type="text" 
              id="noteTitle" 
              name="title" 
              placeholder="Titel der Notiz..." 
              class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white text-lg font-medium placeholder-white/50 focus:bg-white/15 focus:border-white/30 focus:outline-none"
              required
            >
          </div>
          
          <div class="mb-4">
            <textarea 
              id="noteContent" 
              name="content" 
              placeholder="Inhalt der Notiz..." 
              rows="10"
              class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:bg-white/15 focus:border-white/30 focus:outline-none resize-none"
            ></textarea>
          </div>
          
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <label class="text-white/80 text-sm">Farbe:</label>
              <div class="flex gap-2">
                <button type="button" class="note-color-btn" data-color="#fbbf24" style="background: #fbbf24"></button>
                <button type="button" class="note-color-btn" data-color="#3b82f6" style="background: #3b82f6"></button>
                <button type="button" class="note-color-btn" data-color="#10b981" style="background: #10b981"></button>
                <button type="button" class="note-color-btn" data-color="#f59e0b" style="background: #f59e0b"></button>
                <button type="button" class="note-color-btn" data-color="#8b5cf6" style="background: #8b5cf6"></button>
                <button type="button" class="note-color-btn" data-color="#ec4899" style="background: #ec4899"></button>
              </div>
            </div>
            
            <div class="flex gap-3">
              <button type="button" onclick="closeNoteEditor()" class="notes-btn-secondary">
                Abbrechen
              </button>
              <button type="submit" class="notes-btn-primary">
                Speichern
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <script>
    // Gradient management    const gradients = {
      cosmic: 'linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%)',
      ocean: 'linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3730a3 100%)',
      sunset: 'linear-gradient(135deg, #f59e0b 0%, #dc2626 50%, #7c2d12 100%)',
      forest: 'linear-gradient(135deg, #064e3b 0%, #047857 50%, #065f46 100%)',
      purple: 'linear-gradient(135deg, #581c87 0%, #7c3aed 50%, #3730a3 100%)',
      rose: 'linear-gradient(135deg, #9f1239 0%, #e11d48 50%, #881337 100%)',
      cyber: 'linear-gradient(135deg, #065f46 0%, #0891b2 50%, #1e40af 100%)',
      ember: 'linear-gradient(135deg, #7c2d12 0%, #ea580c 50%, #92400e 100%)'
    };

    let currentGradient = localStorage.getItem('dashboardGradient') || 'cosmic';

    function openGradientPicker() {
      const modal = document.getElementById('gradientPickerModal');
      modal.classList.add('active');
      
      // Update selected state
      document.querySelectorAll('.gradient-option').forEach(option => {
        option.classList.remove('selected');
      });
      const currentOption = document.querySelector(`[data-gradient="${currentGradient}"]`);
      if (currentOption) {
        currentOption.classList.add('selected');
      }
    }

    function closeGradientPicker() {
      const modal = document.getElementById('gradientPickerModal');
      modal.classList.remove('active');
    }

    function selectGradient(gradientName) {
      currentGradient = gradientName;
      localStorage.setItem('dashboardGradient', gradientName);
      
      // Apply gradient to body
      document.body.style.background = gradients[gradientName];
      
      // Update selected state
      document.querySelectorAll('.gradient-option').forEach(option => {
        option.classList.remove('selected');
      });
      const selectedOption = document.querySelector(`[data-gradient="${gradientName}"]`);
      if (selectedOption) {
        selectedOption.classList.add('selected');
      }      
      // Close modal after short delay
      setTimeout(() => {
        closeGradientPicker();
      }, 600);
    }
    
    // Notes App Variables
    let notesApp = {
      isOpen: false,
      showArchived: false,
      currentNote: null,
      notes: [],
      selectedColor: '#fbbf24',
      currentView: 'grid' // grid, node, list
    };

    // Notes App Functions
    async function loadNotes() {
      try {
        console.log('Loading notes...');
        const response = await fetch(`/api/notes.php?archived=${notesApp.showArchived}&limit=20`);
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Notes loaded:', data);
        
        if (data.notes) {
          notesApp.notes = data.notes;
          updateNotesDisplay();
          updateNotesCount();
        } else if (data.error) {
          throw new Error(data.error);
        }      } catch (error) {
        console.error('Error loading notes:', error);
        
        // Fallback to sample data for testing the Second Brain graph view
        console.log('Using sample data for Second Brain testing...');
        notesApp.notes = [
          {
            id: 1,
            title: 'Second Brain Concept',
            content: 'Building a second brain helps capture and organize knowledge for creative thinking.',
            color: '#8b5cf6',
            tags: ['knowledge', 'learning'],
            created_at: new Date().toISOString(),
            x: 150,
            y: 100
          },
          {
            id: 2,
            title: 'Knowledge Management',
            content: 'Systematic approach to capturing, organizing, and retrieving information.',
            color: '#fbbf24',
            tags: ['productivity', 'system'],
            created_at: new Date().toISOString(),
            x: 400,
            y: 180
          },
          {
            id: 3,
            title: 'Creative Connections',
            content: 'Linking ideas across domains creates unexpected insights and innovations.',
            color: '#ef4444',
            tags: ['creativity', 'innovation'],
            created_at: new Date().toISOString(),
            x: 250,
            y: 320
          },
          {
            id: 4,
            title: 'Visual Thinking',
            content: 'Graph-based visualization helps understand relationships between concepts.',
            color: '#10b981',
            tags: ['visualization', 'graph'],
            created_at: new Date().toISOString(),
            x: 500,
            y: 280
          }
        ];
        updateNotesDisplay();
        updateNotesCount();
        
        showNotification('Fehler beim Laden der Notizen: ' + error.message, 'error');
      }
    }

    function updateNotesDisplay() {
      const notesList = document.getElementById('notesList');
      const notesGrid = document.getElementById('notesGrid');
      
      // Update widget list (always show recent notes)
      if (notesApp.notes.length === 0) {
        if (notesList) {
          notesList.innerHTML = '<div class="text-center py-6 text-white/60"><i class="fas fa-sticky-note text-2xl mb-2"></i><p class="text-sm">Keine Notizen vorhanden</p></div>';
        }
      } else {
        const widgetNotes = notesApp.notes.slice(0, 4);
        const widgetContainer = document.querySelector('#notesList');
        if (widgetContainer && widgetContainer.closest('.dashboard-short')) {
          // This is the widget list
          widgetContainer.innerHTML = widgetNotes.map(note => `
            <div class="short-list-item p-3" onclick="editNote(${note.id}); return false;" data-note-id="${note.id}">
              <div class="flex items-start gap-3">
                <div class="w-3 h-3 rounded-full flex-shrink-0 mt-1" style="background: ${note.color}"></div>
                <div class="flex-1 min-w-0">
                  <h4 class="text-white font-medium text-sm truncate">${escapeHtml(note.title)}</h4>
                  ${note.content ? `<p class="text-white/60 text-xs mt-1 line-clamp-2">${escapeHtml(note.content)}</p>` : ''}
                  <div class="flex items-center gap-2 mt-2">
                    ${note.is_pinned ? '<i class="fas fa-thumbtack text-yellow-400 text-xs"></i>' : ''}
                    <span class="text-white/50 text-xs">${formatDate(note.updated_at)}</span>
                  </div>
                </div>
              </div>
            </div>
          `).join('');
        }
      }
      
      // Update modal views based on current view
      updateModalView();
    }

    function updateModalView() {
      switch (notesApp.currentView) {
        case 'grid':
          updateGridView();
          break;
        case 'node':
          updateNodeView();
          break;
        case 'list':
          updateListView();
          break;
      }
    }

    function updateGridView() {
      const notesGrid = document.getElementById('notesGrid');
      if (!notesGrid) return;
      
      if (notesApp.notes.length === 0) {
        notesGrid.innerHTML = '<div class="col-span-full text-center py-12 text-white/60"><i class="fas fa-sticky-note text-4xl mb-4"></i><p>Keine Notizen vorhanden</p></div>';
      } else {
        notesGrid.innerHTML = notesApp.notes.map(note => `
          <div class="note-card" style="border-left: 4px solid ${note.color}" data-note-id="${note.id}">
            <div class="note-card-header">
              <h3 class="note-title">${escapeHtml(note.title)}</h3>
              <div class="note-actions">
                ${note.is_pinned ? '<i class="fas fa-thumbtack text-yellow-400"></i>' : ''}
                <button onclick="editNote(${note.id})" class="note-action-btn">
                  <i class="fas fa-edit"></i>
                </button>
              </div>
            </div>
            ${note.content ? `<div class="note-content">${escapeHtml(note.content).replace(/\n/g, '<br>')}</div>` : ''}
            <div class="note-footer">
              <span class="note-date">${formatDate(note.updated_at)}</span>
              ${note.tags && note.tags.length > 0 ? `<div class="note-tags">${note.tags.map(tag => `<span class="note-tag">${escapeHtml(tag)}</span>`).join('')}</div>` : ''}
            </div>          </div>
        `).join('');
      }
    
    // Enhanced Second Brain Node View
    function updateNodeView() {
      const nodeCanvas = document.getElementById('nodeCanvas');
      if (!nodeCanvas) return;
      
      nodeCanvas.innerHTML = '';
      
      if (notesApp.notes.length === 0) {
        nodeCanvas.innerHTML = `
          <div class="absolute inset-0 flex items-center justify-center text-white/60">
            <div class="text-center">
              <i class="fas fa-brain text-6xl mb-4 text-purple-400"></i>
              <h3 class="text-xl mb-2">Dein Second Brain wartet</h3>
              <p class="mb-4">Erstelle Notizen und verknüpfe sie miteinander</p>
              <button onclick="openNoteEditor()" class="notes-btn-primary">
                <i class="fas fa-plus mr-2"></i>Erste Notiz erstellen
              </button>
            </div>
          </div>
        `;
        return;
      }
      
      // Update graph stats
      updateGraphStats();
      
      // Create SVG for connections
      const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
      svg.className = 'node-connections';
      svg.style.position = 'absolute';
      svg.style.top = '0';
      svg.style.left = '0';
      svg.style.width = '100%';
      svg.style.height = '100%';
      svg.style.pointerEvents = 'none';
      svg.style.zIndex = '1';
      nodeCanvas.appendChild(svg);
      
      // Create nodes with enhanced positioning
      const canvasWidth = nodeCanvas.clientWidth || 800;
      const canvasHeight = nodeCanvas.clientHeight || 500;
      const margin = 80;
      
      // Generate node layout based on connections and importance
      const nodeLayout = generateSmartLayout(notesApp.notes, canvasWidth, canvasHeight, margin);
      
      notesApp.notes.forEach((note, index) => {
        const node = createEnhancedNoteNode(note, index, nodeLayout);
        nodeCanvas.appendChild(node);
      });
      
      // Draw connections between linked notes
      drawNoteConnections(svg, notesApp.notes, nodeLayout);
      
      // Setup node interactions
      setupNodeInteractions();
      
      // Setup graph controls
      setupGraphControls();
    }
    
    function generateSmartLayout(notes, width, height, margin) {
      const layout = new Map();
      
      // For now, use circle layout but we'll enhance this
      const centerX = width / 2;
      const centerY = height / 2;
      const radius = Math.min(width, height) * 0.35;
      
      notes.forEach((note, index) => {
        let x, y;
        
        // Use saved position if available
        if (note.node_position_x && note.node_position_y) {
          x = Math.max(margin, Math.min(width - margin, note.node_position_x));
          y = Math.max(margin, Math.min(height - margin, note.node_position_y));
        } else {
          // Smart circle layout with some randomness
          const angle = (index / notes.length) * 2 * Math.PI;
          const radiusVariation = radius * (0.8 + Math.random() * 0.4);
          
          x = centerX + Math.cos(angle) * radiusVariation;
          y = centerY + Math.sin(angle) * radiusVariation;
          
          // Add some clustering based on tags
          if (note.tags && note.tags.length > 0) {
            const tagHash = note.tags[0].split('').reduce((a, b) => {
              a = ((a << 5) - a) + b.charCodeAt(0);
              return a & a;
            }, 0);
            const clusterAngle = (tagHash % 8) * (Math.PI / 4);
            const clusterOffset = 30;
            x += Math.cos(clusterAngle) * clusterOffset;
            y += Math.sin(clusterAngle) * clusterOffset;
          }
          
          // Keep within bounds
          x = Math.max(margin, Math.min(width - margin, x));
          y = Math.max(margin, Math.min(height - margin, y));
        }
        
        layout.set(note.id, { x, y });
      });
      
      return layout;
    }
    
    function createEnhancedNoteNode(note, index, layout) {
      const node = document.createElement('div');
      const position = layout.get(note.id);
      
      // Determine node type and styling
      let nodeClass = 'note-node';
      let nodeType = 'default';
      let nodeIcon = 'fas fa-sticky-note';
      
      if (note.tags && note.tags.length > 0) {
        const tagStr = note.tags.join(' ').toLowerCase();
        if (tagStr.includes('daily') || tagStr.includes('dailythoughts')) {
          nodeType = 'daily';
          nodeClass += ' daily-node';
          nodeIcon = 'fas fa-calendar-day';
        } else if (tagStr.includes('idea') || tagStr.includes('ideen')) {
          nodeType = 'idea';
          nodeClass += ' idea-node';
          nodeIcon = 'fas fa-lightbulb';
        } else if (tagStr.includes('doc') || tagStr.includes('dokumentation')) {
          nodeType = 'documentation';
          nodeClass += ' documentation-node';
          nodeIcon = 'fas fa-file-alt';
        } else if (tagStr.includes('wissen') || tagStr.includes('knowledge')) {
          nodeType = 'knowledge';
          nodeClass += ' knowledge-node';
          nodeIcon = 'fas fa-brain';
        }
      }
      
      if (note.is_pinned) {
        nodeClass += ' pinned';
      }
      
      // Calculate connections count (simulate for now)
      const connectionsCount = Math.floor(Math.random() * 5);
      const tagsCount = note.tags ? note.tags.length : 0;
      
      node.className = nodeClass;
      node.style.left = `${position.x}px`;
      node.style.top = `${position.y}px`;
      node.style.backgroundColor = note.color;
      node.setAttribute('data-note-id', note.id);
      node.setAttribute('data-note-type', nodeType);
      
      node.innerHTML = `
        <div class="node-header">
          <i class="${nodeIcon}" style="font-size: 0.8rem; opacity: 0.8;"></i>
          ${note.is_pinned ? '<i class="fas fa-thumbtack" style="font-size: 0.6rem; color: #fbbf24; margin-left: auto;"></i>' : ''}
        </div>
        <div class="node-title">${escapeHtml(note.title)}</div>
        <div class="node-info">
          ${connectionsCount > 0 ? `<span class="node-info-badge link-count">${connectionsCount}</span>` : ''}
          ${tagsCount > 0 ? `<span class="node-info-badge tag-count">${tagsCount}</span>` : ''}
        </div>
        <div class="note-node-tooltip">
          <strong>${escapeHtml(note.title)}</strong><br>
          <small>${note.content ? escapeHtml(note.content.substring(0, 100)) + '...' : 'Keine Inhalte'}</small><br>
          <small style="color: #9ca3af;">
            ${note.tags && note.tags.length > 0 ? note.tags.map(tag => `#${tag}`).join(' ') : 'Keine Tags'}
          </small>
        </div>
      `;
      
      return node;
    }
    
    function drawNoteConnections(svg, notes, layout) {
      // For now, create some random connections to demonstrate
      // In a real implementation, you'd load actual note links from the database
      const connections = generateSampleConnections(notes);
      
      connections.forEach(connection => {
        const sourcePos = layout.get(connection.source);
        const targetPos = layout.get(connection.target);
        
        if (sourcePos && targetPos) {
          const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
          line.className = 'node-link';
          line.setAttribute('x1', sourcePos.x + 60); // Center of node
          line.setAttribute('y1', sourcePos.y + 40);
          line.setAttribute('x2', targetPos.x + 60);
          line.setAttribute('y2', targetPos.y + 40);
          line.setAttribute('data-source', connection.source);
          line.setAttribute('data-target', connection.target);
          
          svg.appendChild(line);
        }
      });
    }
    
    function generateSampleConnections(notes) {
      const connections = [];
      
      // Create some sample connections based on similar tags or titles
      for (let i = 0; i < notes.length; i++) {
        for (let j = i + 1; j < notes.length; j++) {
          const note1 = notes[i];
          const note2 = notes[j];
          
          // Connect notes with similar tags
          const commonTags = note1.tags && note2.tags ? 
            note1.tags.filter(tag => note2.tags.includes(tag)) : [];
          
          if (commonTags.length > 0 || Math.random() < 0.3) {
            connections.push({
              source: note1.id,
              target: note2.id,
              type: commonTags.length > 0 ? 'tag_similarity' : 'semantic'
            });
          }
        }
      }
      
      return connections.slice(0, Math.min(connections.length, notes.length * 2));
    }
    
    function setupNodeInteractions() {
      const nodes = document.querySelectorAll('.note-node');
      let selectedNode = null;
      let isDragging = false;
      let dragStart = { x: 0, y: 0 };
      
      nodes.forEach(node => {
        // Click to select/edit
        node.addEventListener('click', (e) => {
          if (!isDragging) {
            const noteId = parseInt(node.getAttribute('data-note-id'));
            
            // Deselect previous
            if (selectedNode) {
              selectedNode.classList.remove('selected');
            }
            
            // Select current
            node.classList.add('selected');
            selectedNode = node;
            
            // Show node details
            showNodeDetails(noteId);
            
            // Highlight connected nodes
            highlightConnectedNodes(noteId);
          }
        });
        
        // Double-click to edit
        node.addEventListener('dblclick', (e) => {
          const noteId = parseInt(node.getAttribute('data-note-id'));
          editNote(noteId);
        });
        
        // Drag functionality
        node.addEventListener('mousedown', (e) => {
          isDragging = false;
          dragStart = { x: e.clientX, y: e.clientY };
          
          const onMouseMove = (e) => {
            const deltaX = e.clientX - dragStart.x;
            const deltaY = e.clientY - dragStart.y;
            
            if (Math.abs(deltaX) > 5 || Math.abs(deltaY) > 5) {
              isDragging = true;
              
              const rect = node.getBoundingClientRect();
              const canvas = document.getElementById('nodeCanvas');
              const canvasRect = canvas.getBoundingClientRect();
              
              const newX = rect.left - canvasRect.left + deltaX;
              const newY = rect.top - canvasRect.top + deltaY;
              
              node.style.left = `${Math.max(0, Math.min(canvasRect.width - 120, newX))}px`;
              node.style.top = `${Math.max(0, Math.min(canvasRect.height - 80, newY))}px`;
              
              // Update connections
              updateConnectionsForNode(node);
            }
          };
          
          const onMouseUp = () => {
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
            
            if (isDragging) {
              // Save new position
              const noteId = parseInt(node.getAttribute('data-note-id'));
              const x = parseInt(node.style.left);
              const y = parseInt(node.style.top);
              saveNodePosition(noteId, x, y);
            }
            
            setTimeout(() => { isDragging = false; }, 100);
          };
          
          document.addEventListener('mousemove', onMouseMove);
          document.addEventListener('mouseup', onMouseUp);
        });
      });
    }
    
    function updateConnectionsForNode(node) {
      const nodeId = node.getAttribute('data-note-id');
      const svg = document.querySelector('.node-connections');
      const connections = svg.querySelectorAll(`[data-source="${nodeId}"], [data-target="${nodeId}"]`);
      
      const nodeRect = node.getBoundingClientRect();
      const canvasRect = document.getElementById('nodeCanvas').getBoundingClientRect();
      const centerX = nodeRect.left - canvasRect.left + 60;
      const centerY = nodeRect.top - canvasRect.top + 40;
      
      connections.forEach(line => {
        if (line.getAttribute('data-source') === nodeId) {
          line.setAttribute('x1', centerX);
          line.setAttribute('y1', centerY);
        } else {
          line.setAttribute('x2', centerX);
          line.setAttribute('y2', centerY);
        }
      });
    }
    
    function showNodeDetails(noteId) {
      const note = notesApp.notes.find(n => n.id === noteId);
      if (!note) return;
      
      const panel = document.getElementById('selected-node-panel');
      const content = document.getElementById('node-details-content');
      
      content.innerHTML = `
        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <h5 class="text-lg font-semibold text-white">${escapeHtml(note.title)}</h5>
            <button onclick="editNote(${note.id})" class="notes-btn-primary px-3 py-1 text-sm">
              <i class="fas fa-edit mr-1"></i>Edit
            </button>
          </div>
          
          <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
              <span class="text-gray-400">Created:</span>
              <span class="text-white">${formatDate(note.created_at)}</span>
            </div>
            <div>
              <span class="text-gray-400">Updated:</span>
              <span class="text-white">${formatDate(note.updated_at)}</span>
            </div>
          </div>
          
          ${note.tags && note.tags.length > 0 ? `
            <div>
              <span class="text-gray-400 text-sm">Tags:</span>
              <div class="flex flex-wrap gap-1 mt-1">
                ${note.tags.map(tag => `<span class="filter-tag-btn text-xs">#${tag}</span>`).join('')}
              </div>
            </div>
          ` : ''}
          
          ${note.content ? `
            <div>
              <span class="text-gray-400 text-sm">Preview:</span>
              <p class="text-white text-sm mt-1 line-clamp-3">${escapeHtml(note.content.substring(0, 150))}...</p>
            </div>
          ` : ''}
        </div>
      `;
      
      panel.classList.remove('hidden');
    }
    
    function highlightConnectedNodes(nodeId) {
      // Clear previous highlights
      document.querySelectorAll('.note-node.connected').forEach(node => {
        node.classList.remove('connected');
      });
      
      document.querySelectorAll('.node-link.highlighted').forEach(link => {
        link.classList.remove('highlighted');
      });
      
      // Highlight connections
      const connections = document.querySelectorAll(`[data-source="${nodeId}"], [data-target="${nodeId}"]`);
      connections.forEach(link => {
        link.classList.add('highlighted');
        
        const sourceId = link.getAttribute('data-source');
        const targetId = link.getAttribute('data-target');
        const connectedId = sourceId === nodeId.toString() ? targetId : sourceId;
        
        const connectedNode = document.querySelector(`[data-note-id="${connectedId}"]`);
        if (connectedNode) {
          connectedNode.classList.add('connected');
        }
      });
    }
    
    function setupGraphControls() {
      // Layout button
      document.getElementById('graph-layout-btn')?.addEventListener('click', () => {
        relayoutGraph();
      });
      
      // Center button
      document.getElementById('graph-center-btn')?.addEventListener('click', () => {
        centerGraph();
      });
      
      // Zoom fit button
      document.getElementById('graph-zoom-fit')?.addEventListener('click', () => {
        fitGraphToScreen();
      });
      
      // Node size slider
      document.getElementById('node-size-slider')?.addEventListener('input', (e) => {
        updateNodeSize(parseFloat(e.target.value));
      });
      
      // Link strength slider
      document.getElementById('link-strength-slider')?.addEventListener('input', (e) => {
        updateLinkStrength(parseFloat(e.target.value));
      });
      
      // Toggle labels
      document.getElementById('toggle-labels-btn')?.addEventListener('click', () => {
        toggleNodeLabels();
      });
      
      // Filter buttons
      document.querySelectorAll('.filter-tag-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          const filter = btn.getAttribute('data-filter');
          filterGraphByType(filter);
          
          // Update active state
          document.querySelectorAll('.filter-tag-btn').forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
        });
      });
    }
    
    function updateGraphStats() {
      const nodeCount = notesApp.notes.length;
      const linkCount = document.querySelectorAll('.node-link').length;
      
      document.getElementById('graph-nodes-count').textContent = nodeCount;
      document.getElementById('graph-links-count').textContent = linkCount;
    }
    
    function relayoutGraph() {
      // Re-generate layout and update positions
      const canvas = document.getElementById('nodeCanvas');
      const width = canvas.clientWidth;
      const height = canvas.clientHeight;
      const newLayout = generateSmartLayout(notesApp.notes, width, height, 80);
      
      const nodes = document.querySelectorAll('.note-node');
      nodes.forEach(node => {
        const noteId = parseInt(node.getAttribute('data-note-id'));
        const position = newLayout.get(noteId);
        
        // Animate to new position
        node.style.transition = 'all 0.5s ease';
        node.style.left = `${position.x}px`;
        node.style.top = `${position.y}px`;
        
        // Update connections
        setTimeout(() => {
          updateConnectionsForNode(node);
          node.style.transition = '';
        }, 500);
      });
    }
    
    function centerGraph() {
      const canvas = document.getElementById('nodeCanvas');
      const nodes = document.querySelectorAll('.note-node');
      
      if (nodes.length === 0) return;
      
      // Calculate center of all nodes
      let totalX = 0, totalY = 0;
      nodes.forEach(node => {
        totalX += parseInt(node.style.left);
        totalY += parseInt(node.style.top);
      });
      
      const avgX = totalX / nodes.length;
      const avgY = totalY / nodes.length;
      
      const canvasWidth = canvas.clientWidth;
      const canvasHeight = canvas.clientHeight;
      const offsetX = (canvasWidth / 2) - avgX - 60; // 60 is half node width
      const offsetY = (canvasHeight / 2) - avgY - 40; // 40 is half node height
      
      // Move all nodes
      nodes.forEach(node => {
        const currentX = parseInt(node.style.left);
        const currentY = parseInt(node.style.top);
        
        node.style.transition = 'all 0.5s ease';
        node.style.left = `${Math.max(0, Math.min(canvasWidth - 120, currentX + offsetX))}px`;
        node.style.top = `${Math.max(0, Math.min(canvasHeight - 80, currentY + offsetY))}px`;
        
        setTimeout(() => {
          updateConnectionsForNode(node);
          node.style.transition = '';
        }, 500);
      });
    }
    
    function fitGraphToScreen() {
      const canvas = document.getElementById('nodeCanvas');
      const nodes = document.querySelectorAll('.note-node');
      
      if (nodes.length === 0) return;
      
      // Find bounds of all nodes
      let minX = Infinity, maxX = -Infinity;
      let minY = Infinity, maxY = -Infinity;
      
      nodes.forEach(node => {
        const x = parseInt(node.style.left);
        const y = parseInt(node.style.top);
        minX = Math.min(minX, x);
        maxX = Math.max(maxX, x + 120);
        minY = Math.min(minY, y);
        maxY = Math.max(maxY, y + 80);
      });
      
      const graphWidth = maxX - minX;
      const graphHeight = maxY - minY;
      const canvasWidth = canvas.clientWidth;
      const canvasHeight = canvas.clientHeight;
      
      // Calculate scale to fit
      const scaleX = (canvasWidth - 160) / graphWidth;
      const scaleY = (canvasHeight - 160) / graphHeight;
      const scale = Math.min(scaleX, scaleY, 2); // Don't scale too much
      
      // Calculate offset to center
      const scaledWidth = graphWidth * scale;
      const scaledHeight = graphHeight * scale;
      const offsetX = (canvasWidth - scaledWidth) / 2 - (minX * scale);
      const offsetY = (canvasHeight - scaledHeight) / 2 - (minY * scale);
      
      // Apply transformation
      nodes.forEach(node => {
        const currentX = parseInt(node.style.left);
        const currentY = parseInt(node.style.top);
        
        const newX = (currentX * scale) + offsetX;
        const newY = (currentY * scale) + offsetY;
        
        node.style.transition = 'all 0.5s ease';
        node.style.left = `${newX}px`;
        node.style.top = `${newY}px`;
        node.style.transform = `scale(${scale})`;
        
        setTimeout(() => {
          updateConnectionsForNode(node);
          node.style.transition = '';
        }, 500);
      });
    }
    
    function updateNodeSize(scale) {
      const nodes = document.querySelectorAll('.note-node');
      nodes.forEach(node => {
        node.style.transform = `scale(${scale})`;
      });
    }
    
    function updateLinkStrength(strength) {
      const links = document.querySelectorAll('.node-link');
      links.forEach(link => {
        link.style.strokeWidth = strength * 2;
        link.style.opacity = strength * 0.5;
      });
    }
    
    function toggleNodeLabels() {
      const nodes = document.querySelectorAll('.note-node .node-title');
      const button = document.getElementById('toggle-labels-btn');
      const isHidden = nodes[0]?.style.display === 'none';
      
      nodes.forEach(title => {
        title.style.display = isHidden ? 'block' : 'none';
      });
      
      button.classList.toggle('active', !isHidden);
    }
    
    function filterGraphByType(type) {
      const nodes = document.querySelectorAll('.note-node');
      
      nodes.forEach(node => {
        const nodeType = node.getAttribute('data-note-type');
        const shouldShow = type === 'all' || nodeType === type;
        
        node.style.display = shouldShow ? 'block' : 'none';
        node.style.opacity = shouldShow ? '1' : '0.3';
      });
      
      // Update connections visibility
      const links = document.querySelectorAll('.node-link');
      links.forEach(link => {
        const sourceNode = document.querySelector(`[data-note-id="${link.getAttribute('data-source')}"]`);
        const targetNode = document.querySelector(`[data-note-id="${link.getAttribute('data-target')}"]`);
        
        const shouldShow = sourceNode?.style.display !== 'none' && targetNode?.style.display !== 'none';
        link.style.display = shouldShow ? 'block' : 'none';
      });
    }
    
    async function saveNodePosition(noteId, x, y) {
      try {
        const response = await fetch('/api/notes.php?action=position', {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            note_id: noteId,
            x: x,
            y: y
          })
        });
        
        if (!response.ok) {
          console.warn('Failed to save node position');
        }      } catch (error) {
        console.warn('Error saving node position:', error);
      }
    }

    function updateListView() {
      const listContainer = document.querySelector('#listView .space-y-2');
      if (!listContainer) return;
      
      if (notesApp.notes.length === 0) {
        listContainer.innerHTML = '<div class="text-center py-12 text-white/60"><i class="fas fa-sticky-note text-4xl mb-4"></i><p>Keine Notizen vorhanden</p></div>';
      } else {
        listContainer.innerHTML = notesApp.notes.map(note => `
          <div class="short-list-item p-4" onclick="editNote(${note.id}); return false;" data-note-id="${note.id}">
            <div class="flex items-start gap-3">
              <div class="w-4 h-4 rounded-full flex-shrink-0 mt-1" style="background: ${note.color}"></div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1">
                  <h4 class="text-white font-medium text-sm">${escapeHtml(note.title)}</h4>
                  <div class="flex items-center gap-2">
                    ${note.is_pinned ? '<i class="fas fa-thumbtack text-yellow-400 text-xs"></i>' : ''}
                    <span class="text-white/50 text-xs">${formatDate(note.updated_at)}</span>
                  </div>
                </div>
                ${note.content ? `<p class="text-white/60 text-xs line-clamp-2">${escapeHtml(note.content)}</p>` : ''}
                ${note.tags && note.tags.length > 0 ? `<div class="flex gap-1 mt-2">${note.tags.map(tag => `<span class="note-tag">${escapeHtml(tag)}</span>`).join('')}</div>` : ''}
              </div>
            </div>
          </div>        `).join('');
      }
    }
    
    function makeNodeDraggable(node, noteId) {
      let isDragging = false;
      let startX, startY, initialX, initialY;
      
      node.addEventListener('mousedown', (e) => {
        if (e.button !== 0) return; // Only left mouse button
        
        isDragging = false;
        startX = e.clientX;
        startY = e.clientY;
        initialX = parseInt(node.style.left) || 0;
        initialY = parseInt(node.style.top) || 0;
        
        const onMouseMove = (e) => {
          e.preventDefault();
          isDragging = true;
          
          const deltaX = e.clientX - startX;
          const deltaY = e.clientY - startY;
          
          const nodeCanvas = document.getElementById('nodeCanvas');
          const newX = Math.max(0, Math.min(initialX + deltaX, nodeCanvas.offsetWidth - node.offsetWidth));
          const newY = Math.max(0, Math.min(initialY + deltaY, nodeCanvas.offsetHeight - node.offsetHeight));
          
          node.style.left = newX + 'px';
          node.style.top = newY + 'px';
        };
        
        const onMouseUp = () => {
          document.removeEventListener('mousemove', onMouseMove);
          document.removeEventListener('mouseup', onMouseUp);
          
          // Reset dragging flag after a short delay
          setTimeout(() => {
            isDragging = false;
          }, 100);
        };
        
        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
        
        e.preventDefault();
      });
    }

    function drawNodeConnections(svg) {
      // Simulate some connections based on shared tags
      const nodes = document.querySelectorAll('.note-node');
      const connections = [];
      
      // Find notes with shared tags (simplified algorithm)
      notesApp.notes.forEach((note1, i) => {
        notesApp.notes.forEach((note2, j) => {
          if (i >= j) return; // Avoid duplicates and self-connections
          
          if (note1.tags && note2.tags) {
            const sharedTags = note1.tags.filter(tag => note2.tags.includes(tag));
            if (sharedTags.length > 0) {
              connections.push({
                source: note1.id,
                target: note2.id,
                strength: sharedTags.length
              });
            }
          }
        });
      });
      
      // Draw the connections
      connections.forEach(connection => {
        const sourceNode = document.querySelector(`[data-note-id="${connection.source}"]`);
        const targetNode = document.querySelector(`[data-note-id="${connection.target}"]`);
        
        if (sourceNode && targetNode) {
          const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
          
          const sourceRect = sourceNode.getBoundingClientRect();
          const targetRect = targetNode.getBoundingClientRect();
          const canvasRect = svg.getBoundingClientRect();
          
          const x1 = sourceRect.left - canvasRect.left + sourceRect.width / 2;
          const y1 = sourceRect.top - canvasRect.top + sourceRect.height / 2;
          const x2 = targetRect.left - canvasRect.left + targetRect.width / 2;
          const y2 = targetRect.top - canvasRect.top + targetRect.height / 2;
          
          line.setAttribute('x1', x1);
          line.setAttribute('y1', y1);
          line.setAttribute('x2', x2);
          line.setAttribute('y2', y2);
          line.setAttribute('class', 'node-link');
          line.setAttribute('data-source', connection.source);
          line.setAttribute('data-target', connection.target);
          
          // Thicker line for stronger connections
          line.style.strokeWidth = Math.min(connection.strength + 1, 3);
          
          svg.appendChild(line);
        }
      });
    }

    function updateNodeConnections() {
      const svg = document.querySelector('.node-links');
      if (!svg) return;
      
      // Update all line positions
      const lines = svg.querySelectorAll('line');
      lines.forEach(line => {
        const sourceId = line.getAttribute('data-source');
        const targetId = line.getAttribute('data-target');
        
        const sourceNode = document.querySelector(`[data-note-id="${sourceId}"]`);
        const targetNode = document.querySelector(`[data-note-id="${targetId}"]`);
        
        if (sourceNode && targetNode) {
          const sourceRect = sourceNode.getBoundingClientRect();
          const targetRect = targetNode.getBoundingClientRect();
          const canvasRect = svg.getBoundingClientRect();
          
          const x1 = sourceRect.left - canvasRect.left + sourceRect.width / 2;
          const y1 = sourceRect.top - canvasRect.top + sourceRect.height / 2;
          const x2 = targetRect.left - canvasRect.left + targetRect.width / 2;
          const y2 = targetRect.top - canvasRect.top + targetRect.height / 2;
          
          line.setAttribute('x1', x1);
          line.setAttribute('y1', y1);
          line.setAttribute('x2', x2);
          line.setAttribute('y2', y2);
        }
      });
    }

    function highlightConnectedNodes(noteId) {
      // Highlight all nodes connected to the hovered note
      const lines = document.querySelectorAll('.node-link');
      const connectedNodes = new Set();
      
      lines.forEach(line => {
        const sourceId = line.getAttribute('data-source');
        const targetId = line.getAttribute('data-target');
        
        if (sourceId === noteId.toString()) {
          connectedNodes.add(targetId);
          line.style.stroke = 'rgba(255, 255, 255, 0.8)';
          line.style.strokeWidth = '3';
        } else if (targetId === noteId.toString()) {
          connectedNodes.add(sourceId);
          line.style.stroke = 'rgba(255, 255, 255, 0.8)';
          line.style.strokeWidth = '3';
        }
      });
      
      // Add connected class to nodes
      connectedNodes.forEach(nodeId => {
        const node = document.querySelector(`[data-note-id="${nodeId}"]`);
        if (node) {
          node.classList.add('connected-node');
        }
      });
    }

    function clearNodeHighlights() {
      // Reset all line styles
      const lines = document.querySelectorAll('.node-link');
      lines.forEach(line => {
        line.style.stroke = 'rgba(255, 255, 255, 0.2)';
        line.style.strokeWidth = line.getAttribute('data-strength') || '1';
      });
      
      // Remove connected class from all nodes
      const nodes = document.querySelectorAll('.note-node');
      nodes.forEach(node => {
        node.classList.remove('connected-node');
      });
    }

    function addGraphControls(container) {
      const controls = document.createElement('div');
      controls.className = 'graph-controls';
      controls.innerHTML = `
        <button class="graph-control-btn" onclick="centerGraphView()" title="Zentrieren">
          <i class="fas fa-crosshairs"></i>
        </button>
        <button class="graph-control-btn" onclick="autoLayoutNodes()" title="Auto-Layout">
          <i class="fas fa-magic"></i>
        </button>
        <button class="graph-control-btn" onclick="zoomGraphView('in')" title="Hineinzoomen">
          <i class="fas fa-search-plus"></i>
        </button>
        <button class="graph-control-btn" onclick="zoomGraphView('out')" title="Herauszoomen">
          <i class="fas fa-search-minus"></i>
        </button>
        <button class="graph-control-btn" onclick="toggleGraphFullscreen()" title="Vollbild">
          <i class="fas fa-expand"></i>
        </button>
      `;
      container.appendChild(controls);
    }

    function centerGraphView() {
      // Center all nodes in the canvas
      const canvas = document.getElementById('nodeCanvas');
      const nodes = canvas.querySelectorAll('.note-node');
      
      if (nodes.length === 0) return;
      
      const canvasWidth = canvas.clientWidth;
      const canvasHeight = canvas.clientHeight;
      const centerX = canvasWidth / 2;
      const centerY = canvasHeight / 2;
      
      // Calculate current center of all nodes
      let totalX = 0, totalY = 0;
      nodes.forEach(node => {
        totalX += parseInt(node.style.left);
        totalY += parseInt(node.style.top);
      });
      
      const currentCenterX = totalX / nodes.length;
      const currentCenterY = totalY / nodes.length;
      
      // Move all nodes
      const offsetX = centerX - currentCenterX;
      const offsetY = centerY - currentCenterY;
      
      nodes.forEach(node => {
        const newX = parseInt(node.style.left) + offsetX;
        const newY = parseInt(node.style.top) + offsetY;
        
        node.style.left = `${Math.max(20, Math.min(canvasWidth - 20, newX))}px`;
        node.style.top = `${Math.max(20, Math.min(canvasHeight - 20, newY))}px`;
      });
      
      updateNodeConnections();
    }

    function autoLayoutNodes() {
      // Simple force-directed layout simulation
      const canvas = document.getElementById('nodeCanvas');
      const nodes = canvas.querySelectorAll('.note-node');
      
      if (nodes.length === 0) return;
      
      const canvasWidth = canvas.clientWidth;
      const canvasHeight = canvas.clientHeight;
      const centerX = canvasWidth / 2;
      const centerY = canvasHeight / 2;
      const radius = Math.min(canvasWidth, canvasHeight) * 0.35;
      
      // Arrange nodes in a circle
      nodes.forEach((node, index) => {
        const angle = (index / nodes.length) * 2 * Math.PI;
        const x = centerX + Math.cos(angle) * radius;
        const y = centerY + Math.sin(angle) * radius;
        
        node.style.transition = 'all 0.8s ease';
        node.style.left = `${x}px`;
        node.style.top = `${y}px`;
      });
      
      setTimeout(() => {
        updateNodeConnections();
        // Remove transition after animation
        nodes.forEach(node => {
          node.style.transition = '';
        });
      }, 800);
    }

    function zoomGraphView(direction) {
      const canvas = document.getElementById('nodeCanvas');
      const currentScale = parseFloat(canvas.style.transform?.match(/scale\(([^)]+)\)/)?.[1] || '1');
      const newScale = direction === 'in' ? currentScale * 1.2 : currentScale / 1.2;
      const clampedScale = Math.max(0.5, Math.min(2, newScale));
      
      canvas.style.transform = `scale(${clampedScale})`;
      canvas.style.transformOrigin = 'center center';
    }

    function toggleGraphFullscreen() {
      const modal = document.getElementById('notesAppModal');
      modal.classList.toggle('fullscreen-mode');
    }

    async function saveNodePosition(noteId, x, y) {
      try {
        const response = await fetch('/api/notes.php?action=position', {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            note_id: noteId,
            x: x,
            y: y
          })
        });
        
        if (!response.ok) {
          console.warn('Failed to save node position');
        }
      } catch (error) {
        console.warn('Error saving node position:', error);
      }
    }
        
        const onMouseMove = (e) => {
          isDragging = true;
          const deltaX = e.clientX - startX;
          const deltaY = e.clientY - startY;
          
          const nodeCanvas = document.getElementById('nodeCanvas');
          const newX = Math.max(0, Math.min(initialX + deltaX, nodeCanvas.offsetWidth - node.offsetWidth));
          const newY = Math.max(0, Math.min(initialY + deltaY, nodeCanvas.offsetHeight - node.offsetHeight));
          
          node.style.left = newX + 'px';
          node.style.top = newY + 'px';
        };
        
        const onMouseUp = () => {
          document.removeEventListener('mousemove', onMouseMove);
          document.removeEventListener('mouseup', onMouseUp);
          
          // Reset dragging flag after a short delay
          setTimeout(() => {
            isDragging = false;
          }, 100);
        };
        
        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
        
        e.preventDefault();
      });
    }

    function switchNotesView(viewType) {
      notesApp.currentView = viewType;
      
      // Update button states
      document.querySelectorAll('.view-toggle-btn').forEach(btn => {
        btn.classList.remove('active');
      });
      document.getElementById(viewType + 'ViewBtn').classList.add('active');
      
      // Show/hide views
      document.getElementById('notesGrid').style.display = viewType === 'grid' ? 'block' : 'none';
      document.getElementById('nodeView').style.display = viewType === 'node' ? 'block' : 'none';
      document.getElementById('listView').style.display = viewType === 'list' ? 'block' : 'none';
      
      // Update the current view
      updateModalView();
    }

    function updateNotesCount() {
      const count = notesApp.notes.length;
      const notesCountElement = document.getElementById('notesCount');
      if (notesCountElement) {
        notesCountElement.textContent = count;
      }
    }

    function toggleNotesApp() {
      console.log('toggleNotesApp called');
      const modal = document.getElementById('notesAppModal');
      if (!modal) {
        console.error('notesAppModal not found');
        return;
      }
      
      notesApp.isOpen = !notesApp.isOpen;
      console.log('notesApp.isOpen:', notesApp.isOpen);
      
      if (notesApp.isOpen) {
        modal.classList.add('active');
        loadNotes();
      } else {
        modal.classList.remove('active');
      }
    }

    function closeNoteEditor() {
      const modal = document.getElementById('noteEditorModal');
      if (modal) {
        modal.classList.remove('active');
      }
      notesApp.currentNote = null;
    }

    function editNote(noteId) {
      console.log('editNote called with ID:', noteId);
      openNoteEditor(noteId);
    }

    async function addQuickNote() {
      console.log('addQuickNote called');
      const titleInput = document.getElementById('quickNoteTitle');
      if (!titleInput) {
        console.error('quickNoteTitle input not found');
        return;
      }
      
      const title = titleInput.value.trim();
      console.log('Quick note title:', title);
      
      if (!title) return;
      
      try {
        console.log('Creating quick note:', { title });
        
        const response = await fetch('/api/notes.php', {
          method: 'POST',
          headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({ 
            title: title,
            content: '', 
            color: '#fbbf24' 
          })
        });
        
        console.log('Response status:', response.status);
        const data = await response.json();
        console.log('Response data:', data);
        
        if (data.success) {
          titleInput.value = '';
          await loadNotes();
          showNotification(data.message || 'Notiz erstellt', 'success');
        } else {
          showNotification(data.error || 'Fehler beim Erstellen der Notiz', 'error');
        }
      } catch (error) {
        console.error('Error creating note:', error);
        showNotification('Fehler beim Erstellen der Notiz: ' + error.message, 'error');
      }
    }

    function openNoteEditor(noteId = null) {
      console.log('openNoteEditor called with:', noteId);
      const modal = document.getElementById('noteEditorModal');
      const form = document.getElementById('noteForm');
      if (!modal || !form) {
        console.error('Modal or form not found:', { modal, form });
        return;
      }
      
      if (noteId) {
        const note = notesApp.notes.find(n => n.id == noteId);
        if (note) {
          console.log('Found note:', note);
          notesApp.currentNote = note;
          document.getElementById('noteId').value = note.id;
          document.getElementById('noteTitle').value = note.title;
          document.getElementById('noteContent').value = note.content || '';
          notesApp.selectedColor = note.color;
          updateColorSelection();
          updatePinButton(note.is_pinned);
        } else {
          console.error('Note not found:', noteId);
        }
      } else {
        form.reset();
        notesApp.currentNote = null;
        notesApp.selectedColor = '#fbbf24';
        updateColorSelection();
        updatePinButton(false);
      }
      
      modal.classList.add('active');
      
      // Focus title input after a short delay
      setTimeout(() => {
        const titleInput = document.getElementById('noteTitle');
        if (titleInput) titleInput.focus();
      }, 100);
    }

    function toggleArchived() {
      notesApp.showArchived = !notesApp.showArchived;
      const toggleBtn = document.getElementById('archiveToggle');
      if (toggleBtn) {
        toggleBtn.classList.toggle('active', notesApp.showArchived);
      }
      loadNotes();
    }

    function searchNotes(query) {
      const filteredNotes = notesApp.notes.filter(note => {
        return note.title.toLowerCase().includes(query.toLowerCase()) ||
               (note.content && note.content.toLowerCase().includes(query.toLowerCase()));
      });
      
      const notesGrid = document.getElementById('notesGrid');
      if (notesGrid) {
        notesGrid.innerHTML = filteredNotes.length > 0 ?
          filteredNotes.map(note => `
            <div class="note-card" style="border-left: 4px solid ${note.color}" data-note-id="${note.id}">
              <div class="note-card-header">
                <h3 class="note-title">${escapeHtml(note.title)}</h3>
                <div class="note-actions">
                  ${note.is_pinned ? '<i class="fas fa-thumbtack text-yellow-400"></i>' : ''}
                  <button onclick="editNote(${note.id})" class="note-action-btn">
                    <i class="fas fa-edit"></i>
                  </button>
                </div>
              </div>
              ${note.content ? `<div class="note-content">${escapeHtml(note.content).replace(/\n/g, '<br>')}</div>` : ''}
              <div class="note-footer">
                <span class="note-date">${formatDate(note.updated_at)}</span>
                ${note.tags && note.tags.length > 0 ? `<div class="note-tags">${note.tags.map(tag => `<span class="note-tag">${escapeHtml(tag)}</span>`).join('')}</div>` : ''}
              </div>
            </div>
          `).join('') :
          '<div class="col-span-full text-center py-12 text-white/60"><i class="fas fa-sticky-note text-4xl mb-4"></i><p>Keine Notizen gefunden</p></div>';
      }
    }

    async function saveNote() {
      const formData = new FormData(document.getElementById('noteForm'));
      const noteData = {
        title: formData.get('title'),
        content: formData.get('content'),
        color: notesApp.selectedColor,
        is_pinned: notesApp.currentNote?.is_pinned || false
      };
      
      if (notesApp.currentNote) {
        noteData.id = notesApp.currentNote.id;
      }
      
      try {
        console.log('Saving note:', noteData);
        
        const response = await fetch('/api/notes.php', {
          method: notesApp.currentNote ? 'PUT' : 'POST',
          headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json' 
          },
          body: JSON.stringify(noteData)
        });
        
        console.log('Save response status:', response.status);
        const data = await response.json();
        console.log('Save response data:', data);
        
        if (data.success) {
          closeNoteEditor();
          await loadNotes();
          showNotification(data.message || 'Notiz gespeichert', 'success');
        } else {
          showNotification(data.error || 'Fehler beim Speichern der Notiz', 'error');
        }
      } catch (error) {
        console.error('Error saving note:', error);
        showNotification('Fehler beim Speichern der Notiz: ' + error.message, 'error');
      }
    }

    async function toggleNotePin() {
      if (!notesApp.currentNote) return;
      
      const newPinnedState = !notesApp.currentNote.is_pinned;
      
      try {
        const response = await fetch('/api/notes.php', {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            id: notesApp.currentNote.id,
            title: notesApp.currentNote.title,
            content: notesApp.currentNote.content,
            color: notesApp.currentNote.color,
            is_pinned: newPinnedState
          })
        });
        
        const data = await response.json();
        
        if (data.success) {
          notesApp.currentNote.is_pinned = newPinnedState;
          updatePinButton(newPinnedState);
          await loadNotes();
          showNotification(newPinnedState ? 'Notiz angeheftet' : 'Notiz gelöst', 'success');
        }
      } catch (error) {
        console.error('Error toggling pin:', error);
        showNotification('Fehler beim Anheften', 'error');
      }
    }

    async function deleteCurrentNote() {
      if (!notesApp.currentNote) return;
      
      if (!confirm('Möchten Sie diese Notiz wirklich löschen?')) return;
      
      try {
        const response = await fetch(`/api/notes.php?id=${notesApp.currentNote.id}`, {
          method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.success) {
          closeNoteEditor();
          await loadNotes();
          showNotification('Notiz gelöscht', 'success');
        } else {
          showNotification(data.error || 'Fehler beim Löschen', 'error');
        }
      } catch (error) {
        console.error('Error deleting note:', error);
        showNotification('Fehler beim Löschen', 'error');
      }
    }

    // Helper functions
    function escapeHtml(text) {
      const div = document.createElement('div');
      div.textContent = text || '';
      return div.innerHTML;
    }

    function formatDate(dateString) {
      const date = new Date(dateString);
      const now = new Date();
      const diffTime = Math.abs(now - date);
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      
      if (diffDays === 1) return 'Heute';
      if (diffDays === 2) return 'Gestern';
      if (diffDays <= 7) return `vor ${diffDays} Tagen`;
      
      return date.toLocaleDateString('de-DE');
    }

    function updateColorSelection() {
      document.querySelectorAll('.note-color-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.color === notesApp.selectedColor);
      });
    }

    function updatePinButton(isPinned) {
      const pinBtn = document.getElementById('pinBtn');
      if (pinBtn) {
        pinBtn.classList.toggle('active', isPinned);
      }
    }

    // Simple notification function
    function showNotification(message, type = 'info') {
      const notification = document.createElement('div');
      notification.className = `fixed top-20 right-4 bg-${type}-500 text-white p-4 rounded-lg shadow-lg transition-all transform`;
      notification.style.pointerEvents = 'none';
      notification.style.opacity = '0';
      notification.innerHTML = message;
      
      document.body.appendChild(notification);
      
      setTimeout(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateY(0)';
      }, 100);
      
      setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-10px)';
        setTimeout(() => {
          if (notification.parentNode) {
            document.body.removeChild(notification);
          }
        }, 300);
      }, 3000);
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
      // Apply saved gradient
      if (gradients[currentGradient]) {
        document.body.style.background = gradients[currentGradient];
      }
      
      // Simple fade-in animation
      const cards = document.querySelectorAll('.dashboard-short');
      cards.forEach((card, index) => {
        card.style.opacity = '0';
        setTimeout(() => {
          card.style.transition = 'opacity 0.4s ease';
          card.style.opacity = '1';
        }, index * 50);
      });
      
      // Initialize notes app
      console.log('Initializing notes app...');
      loadNotes();
      
      // Add event delegation for note clicks
      document.addEventListener('click', function(e) {
        const noteItem = e.target.closest('[data-note-id]');
        if (noteItem) {
          const noteId = noteItem.getAttribute('data-note-id');
          console.log('Note clicked via delegation:', noteId);
          editNote(parseInt(noteId));
          e.preventDefault();
          e.stopPropagation();
        }
      });
      
      // Setup color buttons
      document.querySelectorAll('.note-color-btn').forEach(btn => {
        btn.addEventListener('click', function() {
          notesApp.selectedColor = this.dataset.color;
          updateColorSelection();
        });
      });
      
      // Setup note form
      const noteForm = document.getElementById('noteForm');
      if (noteForm) {
        noteForm.addEventListener('submit', async function(e) {
          e.preventDefault();
          await saveNote();
        });
      }
      
      // Setup search
      const notesSearch = document.getElementById('notesSearch');
      if (notesSearch) {
        notesSearch.addEventListener('input', function() {
          searchNotes(this.value);
        });
      }
      
      // Setup quick note enter key
      const quickNoteTitle = document.getElementById('quickNoteTitle');
      if (quickNoteTitle) {
        quickNoteTitle.addEventListener('keypress', function(e) {
          if (e.key === 'Enter') {
            addQuickNote();
          }
        });
      }
    });

    // Close modal on background click
    document.addEventListener('click', function(e) {
      const gradientModal = document.getElementById('gradientPickerModal');
      const notesModal = document.getElementById('notesAppModal');
      const editorModal = document.getElementById('noteEditorModal');
      
      if (gradientModal && e.target === gradientModal) {
        closeGradientPicker();
      }
      if (notesModal && e.target === notesModal) {
        toggleNotesApp();
      }
      if (editorModal && e.target === editorModal) {
        closeNoteEditor();
      }
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        const gradientModal = document.getElementById('gradientPickerModal');
        const notesModal = document.getElementById('notesAppModal');
        const editorModal = document.getElementById('noteEditorModal');
        
        if (gradientModal && gradientModal.classList.contains('active')) {
          closeGradientPicker();
        }
        if (notesModal && notesModal.classList.contains('active')) {
          toggleNotesApp();
        }
        if (editorModal && editorModal.classList.contains('active')) {
          closeNoteEditor();
        }
      }
    });
  </script>
</body>
</html>
