<!-- templates/dashboard.php -->
<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard | Private Vault</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Enhanced Zettelkasten Styles -->
  <link rel="stylesheet" href="/css/zettelkasten.css">
  <!-- D3.js for graph visualization -->
  <script src="https://d3js.org/d3.v7.min.js"></script>
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
    }    /* Enhanced Node View Styles */
    .node-view-container {
      position: relative;
      width: 100%;
      height: 500px;
      overflow: hidden;
      border-radius: 1rem;
      background: linear-gradient(135deg, rgba(0, 0, 0, 0.3) 0%, rgba(30, 30, 60, 0.2) 100%);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .node-canvas {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
    }

    .node-canvas svg {
      width: 100%;
      height: 100%;
    }

    /* D3.js Graph Styles */
    .graph-node {
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .graph-node:hover {
      filter: brightness(1.2);
    }

    .graph-node circle {
      transition: all 0.3s ease;
    }

    .graph-node text {
      font-family: 'Inter', sans-serif;
      font-weight: 600;
      text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
      pointer-events: none;
    }

    /* Graph Link Styles */
    .links line {
      transition: all 0.3s ease;
    }

    .links line:hover {
      stroke-width: 4 !important;
      stroke-opacity: 1 !important;
    }

    /* Tooltip Styles */
    .graph-tooltip {
      position: absolute;
      background: rgba(0, 0, 0, 0.9);
      color: white;
      padding: 8px 12px;
      border-radius: 6px;
      font-size: 12px;
      pointer-events: none;
      z-index: 1000;
      opacity: 0;
      transition: opacity 0.3s ease;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .graph-tooltip.visible {
      opacity: 1;
    }

    /* Node Info Panel Styles */
    #nodeInfoPanel {
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
      transition: all 0.3s ease;
      transform: translateX(100%);
    }

    #nodeInfoPanel.visible {
      transform: translateX(0);
    }

    /* Legacy Node Styles (fallback) */
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
        </div>        <!-- View Toggle -->
        <div class="view-toggle-buttons mt-4">
          <button class="view-toggle-btn active" data-view="grid" onclick="zettelkasten.switchView('grid')">
            <i class="fas fa-th mr-1"></i>Grid
          </button>
          <button class="view-toggle-btn" data-view="node" onclick="zettelkasten.switchView('node')">
            <i class="fas fa-project-diagram mr-1"></i>Knoten
          </button>
          <button class="view-toggle-btn" data-view="list" onclick="zettelkasten.switchView('list')">
            <i class="fas fa-list mr-1"></i>Liste
          </button>
        </div>
      </div>
      
      <div class="notes-app-body">
        <!-- Grid View -->
        <div class="notes-grid" id="notesGrid" style="display: block;">
          <!-- Notes will be loaded here -->
        </div>

        <!-- Enhanced Node View with D3.js -->
        <div class="node-view-container" id="nodeView" style="display: none;">
          <div class="node-canvas" id="nodeCanvas">
            <!-- SVG graph will be rendered here by D3.js -->
          </div>
          
          <!-- Graph Controls -->
          <div class="absolute top-4 left-4 bg-black/50 backdrop-blur-sm rounded-lg p-3">
            <div class="flex flex-col gap-2 text-white/80 text-sm">
              <button onclick="zettelkasten.centerGraph()" class="text-left hover:text-white">
                <i class="fas fa-compress-arrows-alt mr-2"></i>Zentrieren
              </button>
              <button onclick="zettelkasten.togglePhysics()" class="text-left hover:text-white">
                <i class="fas fa-play mr-2"></i>Physik an/aus
              </button>
              <button onclick="zettelkasten.resetLayout()" class="text-left hover:text-white">
                <i class="fas fa-undo mr-2"></i>Layout zurücksetzen
              </button>
            </div>
          </div>
          
          <!-- Legend -->
          <div class="absolute bottom-4 left-4 bg-black/50 backdrop-blur-sm rounded-lg p-3">
            <div class="text-white/80 text-xs space-y-1">
              <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                <span>Normale Verknüpfung</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                <span>Angeheftete Notiz</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="w-4 h-0.5 bg-gray-400" style="border-style: dashed;"></div>
                <span>Rückverweis</span>
              </div>
            </div>
          </div>
          
          <!-- Node Info Panel -->
          <div id="nodeInfoPanel" class="absolute top-4 right-4 bg-black/70 backdrop-blur-sm rounded-lg p-4 max-w-xs hidden">
            <h4 class="text-white font-semibold mb-2" id="nodeInfoTitle">Notiz-Details</h4>
            <div class="text-white/80 text-sm space-y-2" id="nodeInfoContent">
              <!-- Dynamic content -->
            </div>
            <div class="flex gap-2 mt-3">
              <button onclick="zettelkasten.editSelectedNote()" class="px-3 py-1 bg-blue-600 text-white rounded text-xs">
                Bearbeiten
              </button>
              <button onclick="zettelkasten.showNoteLinks()" class="px-3 py-1 bg-purple-600 text-white rounded text-xs">
                Verknüpfungen
              </button>
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

  <!-- Enhanced Note Editor Modal -->
  <div id="noteEditorModal" class="note-editor-modal">
    <div class="note-editor-content">
      <div class="note-editor-header">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold text-white">Notiz bearbeiten</h3>
          <div class="flex items-center gap-2">
            <button onclick="showLinkedNotes()" class="note-action-btn" title="Verknüpfungen anzeigen">
              <i class="fas fa-link"></i>
            </button>
            <button onclick="showShareDialog()" class="note-action-btn" title="Teilen">
              <i class="fas fa-share-alt"></i>
            </button>
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
  
  <!-- Share Note Dialog -->
  <div id="shareNoteModal" class="note-editor-modal" style="display: none;">
    <div class="note-editor-content max-w-md">
      <div class="note-editor-header">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold text-white">Notiz teilen</h3>
          <button onclick="closeShareDialog()" class="note-action-btn">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      
      <div class="note-editor-body">
        <div class="space-y-4">
          <div>
            <label class="block text-white/80 text-sm mb-2">Mit Benutzer teilen:</label>
            <select id="shareUserSelect" class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white">
              <option value="">Benutzer auswählen...</option>
            </select>
          </div>
          
          <div>
            <label class="block text-white/80 text-sm mb-2">Berechtigungsebene:</label>
            <select id="sharePermissionSelect" class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white">
              <option value="read">Nur lesen</option>
              <option value="edit">Bearbeiten</option>
              <option value="comment">Kommentieren</option>
            </select>
          </div>
          
          <div class="flex gap-3">
            <button onclick="shareCurrentNote()" class="notes-btn-primary">
              Teilen
            </button>
            <button onclick="closeShareDialog()" class="notes-btn-secondary">
              Abbrechen
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Link Notes Dialog -->
  <div id="linkNotesModal" class="note-editor-modal" style="display: none;">
    <div class="note-editor-content max-w-2xl">
      <div class="note-editor-header">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold text-white">Notizen verknüpfen</h3>
          <button onclick="closeLinkDialog()" class="note-action-btn">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      
      <div class="note-editor-body">
        <div class="space-y-4">
          <!-- Current Links -->
          <div>
            <h4 class="text-white font-medium mb-3">Bestehende Verknüpfungen:</h4>
            <div id="existingLinks" class="space-y-2 max-h-40 overflow-y-auto">
              <!-- Dynamic content -->
            </div>
          </div>
          
          <!-- Create New Link -->
          <div class="border-t border-white/20 pt-4">
            <h4 class="text-white font-medium mb-3">Neue Verknüpfung erstellen:</h4>
            <div class="space-y-3">
              <div>
                <input 
                  type="text" 
                  id="linkSearchInput" 
                  placeholder="Notiz suchen..." 
                  class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50"
                >
              </div>
              
              <div id="linkSearchResults" class="space-y-2 max-h-40 overflow-y-auto">
                <!-- Search results will appear here -->
              </div>
              
              <div class="flex gap-3">
                <button onclick="createSelectedLink()" class="notes-btn-primary" disabled id="createLinkBtn">
                  Verknüpfung erstellen
                </button>
                <button onclick="closeLinkDialog()" class="notes-btn-secondary">
                  Schließen
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Wiki Link Suggestion Tooltip -->
  <div id="wikiLinkTooltip" class="graph-tooltip">
    <div class="text-xs">
      <strong>Wiki-Link Tipp:</strong><br>
      Verwenden Sie [[Notiz-Titel]] um automatische Verknüpfungen zu erstellen
    </div>
  </div>
  
  <script>
    // Gradient management
    const gradients = {
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
    }    // Notes App Variables
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
        }
      } catch (error) {
        console.error('Error loading notes:', error);
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
            </div>
          </div>
        `).join('');
      }
    }

    function updateNodeView() {
      const nodeCanvas = document.getElementById('nodeCanvas');
      if (!nodeCanvas) return;
      
      nodeCanvas.innerHTML = '';
      
      if (notesApp.notes.length === 0) {
        nodeCanvas.innerHTML = '<div class="absolute inset-0 flex items-center justify-center text-white/60"><i class="fas fa-sticky-note text-4xl mb-4"></i><p>Keine Notizen vorhanden</p></div>';
        return;
      }
      
      // Create nodes with random positions (in real app, save positions)
      notesApp.notes.forEach((note, index) => {
        const node = document.createElement('div');
        node.className = `note-node ${note.is_pinned ? 'pinned' : ''}`;
        node.style.backgroundColor = note.color;
        node.style.left = `${(index % 5) * 150 + 20}px`;
        node.style.top = `${Math.floor(index / 5) * 100 + 20}px`;
        node.setAttribute('data-note-id', note.id);
        
        node.innerHTML = `
          <div class="node-title">${escapeHtml(note.title)}</div>
          <div class="node-preview">${note.content ? escapeHtml(note.content.substring(0, 50)) + '...' : ''}</div>
        `;
        
        // Add click event
        node.addEventListener('click', () => editNote(note.id));
        
        // Add drag functionality
        makeNodeDraggable(node);
        
        nodeCanvas.appendChild(node);
      });
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
          </div>
        `).join('');
      }
    }

    function makeNodeDraggable(node) {
      let isDragging = false;
      let startX, startY, initialX, initialY;
      
      node.addEventListener('mousedown', (e) => {
        if (e.detail === 1) { // Single click
          setTimeout(() => {
            if (!isDragging) {
              // This was a click, not a drag
              return;
            }
          }, 200);
        }
        
        isDragging = false;
        startX = e.clientX;
        startY = e.clientY;
        initialX = parseInt(node.style.left) || 0;
        initialY = parseInt(node.style.top) || 0;
        
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

    // Enhanced Zettelkasten Functions
    
    function showLinkedNotes() {
      if (!notesApp.currentNote) return;
      
      const modal = document.getElementById('linkNotesModal');
      if (modal) {
        modal.style.display = 'flex';
        loadExistingLinks();
        setupLinkSearch();
      }
    }
    
    function showShareDialog() {
      if (!notesApp.currentNote) return;
      
      const modal = document.getElementById('shareNoteModal');
      if (modal) {
        modal.style.display = 'flex';
        loadAvailableUsers();
      }
    }
    
    function closeShareDialog() {
      const modal = document.getElementById('shareNoteModal');
      if (modal) {
        modal.style.display = 'none';
      }
    }
    
    function closeLinkDialog() {
      const modal = document.getElementById('linkNotesModal');
      if (modal) {
        modal.style.display = 'none';
      }
    }
    
    async function loadAvailableUsers() {
      try {
        const response = await fetch('/api/users.php?action=list');
        const data = await response.json();
        
        if (data.success) {
          const select = document.getElementById('shareUserSelect');
          select.innerHTML = '<option value="">Benutzer auswählen...</option>';
          
          data.users.forEach(user => {
            if (user.id !== notesApp.currentNote.user_id) { // Don't include the note owner
              const option = document.createElement('option');
              option.value = user.id;
              option.textContent = user.username;
              select.appendChild(option);
            }
          });
        }
      } catch (error) {
        console.error('Error loading users:', error);
      }
    }
    
    async function loadExistingLinks() {
      if (!notesApp.currentNote) return;
      
      try {
        const response = await fetch(`/api/notes.php?action=links&note_id=${notesApp.currentNote.id}`);
        const data = await response.json();
        
        if (data.success) {
          const container = document.getElementById('existingLinks');
          
          if (data.links.length === 0) {
            container.innerHTML = '<p class="text-white/60 text-sm">Keine Verknüpfungen vorhanden</p>';
            return;
          }
          
          container.innerHTML = data.links.map(link => {
            const isSource = link.source_note_id === notesApp.currentNote.id;
            const targetTitle = isSource ? link.target_title : link.source_title;
            const targetColor = isSource ? link.target_color : link.source_color;
            const linkTypeIcon = link.link_type === 'bidirectional' ? 'fa-exchange-alt' : 
                                 link.link_type === 'backlink' ? 'fa-arrow-left' : 'fa-arrow-right';
            
            return `
              <div class="flex items-center justify-between p-2 bg-white/5 rounded">
                <div class="flex items-center gap-2">
                  <div class="w-3 h-3 rounded-full" style="background: ${targetColor}"></div>
                  <span class="text-white text-sm">${escapeHtml(targetTitle)}</span>
                  <i class="fas ${linkTypeIcon} text-white/60 text-xs"></i>
                </div>
                <button onclick="deleteLink(${link.id})" class="text-red-400 hover:text-red-300 text-xs">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            `;
          }).join('');
        }
      } catch (error) {
        console.error('Error loading links:', error);
      }
    }
    
    function setupLinkSearch() {
      const searchInput = document.getElementById('linkSearchInput');
      const resultsContainer = document.getElementById('linkSearchResults');
      let selectedNoteId = null;
      
      searchInput.addEventListener('input', async (e) => {
        const query = e.target.value.trim();
        
        if (query.length < 2) {
          resultsContainer.innerHTML = '';
          return;
        }
        
        try {
          const response = await fetch(`/api/notes.php?search=${encodeURIComponent(query)}&limit=10`);
          const data = await response.json();
          
          if (data.success) {
            const filteredNotes = data.notes.filter(note => 
              note.id !== notesApp.currentNote.id && // Don't show current note
              !notesApp.currentNote.linked_notes?.some(link => 
                link.target_note_id === note.id || link.source_note_id === note.id
              ) // Don't show already linked notes
            );
            
            resultsContainer.innerHTML = filteredNotes.map(note => `
              <div class="p-2 bg-white/5 rounded cursor-pointer hover:bg-white/10 transition-colors" 
                   onclick="selectNoteForLink(${note.id}, '${escapeHtml(note.title)}')">
                <div class="flex items-center gap-2">
                  <div class="w-3 h-3 rounded-full" style="background: ${note.color}"></div>
                  <span class="text-white text-sm">${escapeHtml(note.title)}</span>
                </div>
                ${note.content ? `<p class="text-white/60 text-xs mt-1 line-clamp-1">${escapeHtml(note.content.substring(0, 100))}...</p>` : ''}
              </div>
            `).join('');
          }
        } catch (error) {
          console.error('Error searching notes:', error);
        }
      });
    }
    
    function selectNoteForLink(noteId, title) {
      window.selectedLinkNoteId = noteId;
      document.getElementById('createLinkBtn').disabled = false;
      
      // Highlight selected note
      document.querySelectorAll('#linkSearchResults > div').forEach(div => {
        div.classList.remove('bg-blue-600/30');
      });
      event.target.closest('div').classList.add('bg-blue-600/30');
    }
    
    async function createSelectedLink() {
      if (!window.selectedLinkNoteId || !notesApp.currentNote) return;
      
      try {
        const response = await fetch('/api/notes.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            action: 'link',
            source_note_id: notesApp.currentNote.id,
            target_note_id: window.selectedLinkNoteId,
            link_type: 'reference'
          })
        });
        
        const data = await response.json();
        
        if (data.success) {
          showNotification('Verknüpfung erstellt', 'success');
          loadExistingLinks();
          
          // Clear selection
          document.getElementById('linkSearchInput').value = '';
          document.getElementById('linkSearchResults').innerHTML = '';
          document.getElementById('createLinkBtn').disabled = true;
          window.selectedLinkNoteId = null;
          
          // Reload notes to update link counts
          await loadNotes();
        }
      } catch (error) {
        console.error('Error creating link:', error);
        showNotification('Fehler beim Erstellen der Verknüpfung', 'error');
      }
    }
    
    async function deleteLink(linkId) {
      if (!confirm('Verknüpfung wirklich löschen?')) return;
      
      try {
        const response = await fetch(`/api/notes.php?link_id=${linkId}`, {
          method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.success) {
          showNotification('Verknüpfung gelöscht', 'success');
          loadExistingLinks();
          await loadNotes();
        }
      } catch (error) {
        console.error('Error deleting link:', error);
      }
    }
    
    async function shareCurrentNote() {
      const userId = document.getElementById('shareUserSelect').value;
      const permission = document.getElementById('sharePermissionSelect').value;
      
      if (!userId || !notesApp.currentNote) {
        showNotification('Bitte wählen Sie einen Benutzer aus', 'error');
        return;
      }
      
      try {
        const response = await fetch('/api/notes.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            action: 'share',
            note_id: notesApp.currentNote.id,
            share_with_user_id: parseInt(userId),
            permission_level: permission
          })
        });
        
        const data = await response.json();
        
        if (data.success) {
          showNotification('Notiz erfolgreich geteilt', 'success');
          closeShareDialog();
        } else {
          showNotification(data.error || 'Fehler beim Teilen', 'error');
        }
      } catch (error) {
        console.error('Error sharing note:', error);
        showNotification('Fehler beim Teilen der Notiz', 'error');
      }
    }
    
    // Enhanced content processing for wiki-links
    function processNoteContent(textarea) {
      const content = textarea.value;
      const tooltip = document.getElementById('wikiLinkTooltip');
      
      // Show tooltip if user types [[ 
      if (content.includes('[[') && !content.includes(']]')) {
        showWikiLinkTooltip(textarea);
      } else {
        hideWikiLinkTooltip();
      }
      
      // Auto-complete wiki links (optional enhancement)
      const matches = content.match(/\[\[([^\]]*)/g);
      if (matches) {
        // Could implement auto-complete dropdown here
      }
    }
    
    function showWikiLinkTooltip(element) {
      const tooltip = document.getElementById('wikiLinkTooltip');
      const rect = element.getBoundingClientRect();
      
      tooltip.style.left = rect.left + 'px';
      tooltip.style.top = (rect.bottom + 10) + 'px';
      tooltip.classList.add('visible');
    }
    
    function hideWikiLinkTooltip() {
      const tooltip = document.getElementById('wikiLinkTooltip');
      tooltip.classList.remove('visible');
    }
    
    // Enhanced note editor setup
    function setupEnhancedNoteEditor() {
      const contentTextarea = document.getElementById('noteContent');
      if (contentTextarea) {
        contentTextarea.addEventListener('input', () => {
          processNoteContent(contentTextarea);
        });
      }
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
      // Initialize Zettelkasten manager if D3.js is available
      if (typeof d3 !== 'undefined') {
        // Load our enhanced notes manager
        const script = document.createElement('script');
        script.src = '/js/zettelkasten-manager.js';
        script.onload = () => {
          console.log('Zettelkasten manager loaded');
        };
        document.head.appendChild(script);
      } else {
        console.warn('D3.js not available - falling back to basic notes functionality');
      }
      
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
      
      // Setup enhanced note editor
      setupEnhancedNoteEditor();
      
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
      
      // Setup enhanced note editor
      setupEnhancedNoteEditor();
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
      }    });
  </script>
  
  <!-- Load Enhanced Zettelkasten Manager -->
  <script src="/js/enhanced-zettelkasten.js"></script>
  
  <script>
    // Initialize enhanced Zettelkasten when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
      // Overwrite the basic zettelkasten with enhanced version if available
      if (typeof EnhancedZettelkasten !== 'undefined') {
        window.zettelkasten = new EnhancedZettelkasten();
        console.log('Enhanced Zettelkasten initialized');
      }
    });
  </script>
</body>
</html>
