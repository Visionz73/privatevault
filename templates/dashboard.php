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

    /* Notification Badge */
    .notification-badge {
      position: absolute;
      top: -8px;
      right: -8px;
      background: #ef4444;
      color: white;
      border-radius: 50%;
      width: 18px;
      height: 18px;
      font-size: 10px;
      font-weight: bold;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 2px solid rgba(255, 255, 255, 0.2);
      animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
      0% {
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
      }
      70% {
        box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
      }
      100% {
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
      }
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
    }    /* Dashboard Widget Layout - Buttons bündig am unteren Rand */
    .dashboard-short {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1.5rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      position: relative;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      min-height: 350px; /* Mindesthöhe für einheitliches Erscheinungsbild */
    }

    .widget-content {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .widget-buttons {
      margin-top: auto; /* Buttons automatisch nach unten drücken */
      padding: 1.5rem;
      padding-top: 0;
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

    /* Switch/Toggle Styles for Settings */
    .switch {
      position: relative;
      display: inline-block;
      width: 60px;
      height: 34px;
    }

    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(255, 255, 255, 0.2);
      transition: 0.4s;
      border-radius: 34px;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 26px;
      width: 26px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      transition: 0.4s;
      border-radius: 50%;
    }

    input:checked + .slider {
      background-color: #3b82f6;
    }

    input:checked + .slider:before {
      transform: translateX(26px);
    }

    /* Layout Option Styles */
    .layout-option {
      transition: all 0.3s ease;
    }

    .layout-option:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }

    .layout-option.selected {
      border-color: #3b82f6 !important;
      box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
    }

    /* Performance Overlay */
    #performanceOverlay {
      font-family: 'Courier New', monospace;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Settings Form Styles */
    .settings-form select,
    .settings-form input[type="text"],
    .settings-form input[type="email"] {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      border-radius: 0.5rem;
      padding: 0.75rem;
      transition: all 0.3s ease;
    }

    .settings-form select:focus,
    .settings-form input[type="text"]:focus,
    .settings-form input[type="email"]:focus {
      outline: none;
      border-color: #3b82f6;
      box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
    }

    .settings-form select option {
      background: #1f2937;
      color: white;
    }

    /* Dashboard Grid Animation */
    .dashboard-grid {
      transition: all 0.5s ease;
    }

    .dashboard-grid.layout-transition {
      transform: scale(0.95);
      opacity: 0.7;
    }

    /* Widget Hover Effects Enhanced */
    .dashboard-short:hover {
      transform: translateY(-2px) scale(1.02);
    }

    .dashboard-short.compact-mode {
      transform: scale(0.85);
      margin: 0.5rem;
    }

    .dashboard-short.large-mode {
      transform: scale(1.1);
    }

    /* Theme Transition */
    body.theme-transition {
      transition: background 0.5s ease, color 0.5s ease;
    }

    /* Light Theme Overrides */
    body.light-theme {
      background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 30%, #cbd5e1 100%);
      color: #1f2937;
    }

    body.light-theme .dashboard-short,
    body.light-theme .glassmorphism-container {
      background: rgba(255, 255, 255, 0.25);
      border-color: rgba(255, 255, 255, 0.3);
      color: #1f2937;
    }

    body.light-theme .text-primary {
      color: #1f2937 !important;
    }

    body.light-theme .text-secondary {
      color: #4b5563 !important;
    }

    body.light-theme .text-muted {
      color: #6b7280 !important;
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

    /* Widget Sizing Classes */
    .widget-small {
      min-height: 200px;
      padding: 1rem;
    }
    
    .widget-small h3 {
      font-size: 1rem;
      margin-bottom: 0.5rem;
    }
    
    .widget-small .stats-number {
      font-size: 1.5rem;
    }
    
    .widget-medium {
      min-height: 250px;
      padding: 1.5rem;
    }
    
    .widget-medium h3 {
      font-size: 1.125rem;
      margin-bottom: 0.75rem;
    }
    
    .widget-medium .stats-number {
      font-size: 2rem;
    }
    
    .widget-large {
      min-height: 300px;
      padding: 2rem;
    }
    
    .widget-large h3 {
      font-size: 1.25rem;
      margin-bottom: 1rem;
    }
    
    .widget-large .stats-number {
      font-size: 2.5rem;
    }
    
    /* Compact Mode */
    .compact-mode {
      min-height: 150px !important;
      padding: 0.75rem !important;
    }
    
    .compact-mode h3 {
      font-size: 0.875rem !important;
      margin-bottom: 0.5rem !important;
    }
    
    .compact-mode .stats-number {
      font-size: 1.25rem !important;
    }
    
    .compact-mode .widget-content {
      padding: 0.5rem !important;
    }
    
    /* Theme Support */
    .theme-light .dashboard-short {
      background: rgba(255, 255, 255, 0.95);
      border-color: rgba(0, 0, 0, 0.1);
      color: #1f2937;
    }
    
    .theme-light .dashboard-short:hover {
      background: rgba(255, 255, 255, 1);
      border-color: rgba(0, 0, 0, 0.2);
    }
    
    .theme-light .stats-number {
      color: #3b82f6;
    }
    
    .theme-dark .dashboard-short {
      background: rgba(255, 255, 255, 0.1);
      border-color: rgba(255, 255, 255, 0.2);
      color: #f1f5f9;
    }
    
    .theme-dark .dashboard-short:hover {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.3);
    }
    
    .theme-dark .stats-number {
      color: #60a5fa;
    }
    
    /* Responsive Dashboard Grid */
    .dashboard-grid {
      display: grid;
      gap: 2rem;
      transition: all 0.3s ease;
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
      .dashboard-grid {
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
      }
      
      .dashboard-short {
        min-height: 200px;
        padding: 1rem;
      }
      
      .dashboard-short h3 {
        font-size: 1rem;
      }
      
      .stats-number {
        font-size: 1.5rem;
      }
    }
    
    /* Tablet Responsive */
    @media (min-width: 769px) and (max-width: 1023px) {
      .dashboard-grid {
        gap: 1.5rem;
      }
      
      .dashboard-short {
        min-height: 225px;
        padding: 1.25rem;
      }
      
      .dashboard-short h3 {
        font-size: 1.125rem;
      }
      
      .stats-number {
        font-size: 1.75rem;
      }
    }
    
    /* Desktop Responsive */
    @media (min-width: 1024px) {
      .dashboard-grid {
        gap: 2rem;
      }
      
      .dashboard-short {
        min-height: 250px;
        padding: 1.5rem;
      }
      
      .dashboard-short h3 {
        font-size: 1.25rem;
      }
      
      .stats-number {
        font-size: 2rem;
      }
    }
    
    /* Performance Optimizations */
    .dashboard-short {
      will-change: transform;
      backface-visibility: hidden;
    }
    
    .dashboard-short:hover {
      transform: translateY(-2px);
    }
    
    /* Animation Classes */
    .animate-fade-in {
      animation: fadeIn 0.3s ease-in-out;
    }
    
    .animate-slide-up {
      animation: slideUp 0.3s ease-in-out;
    }
    
    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }
    
    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    /* Modal Styles */
    .modal-backdrop {
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
    }
    
    .modal-content {
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      animation: modalSlideIn 0.3s ease-out;
    }
    
    @keyframes modalSlideIn {
      from {
        opacity: 0;
        transform: translateY(-20px) scale(0.95);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }
    
    /* In-App Notification Styles */
    .in-app-notification {
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      animation: slideInRight 0.3s ease-out;
    }
    
    .in-app-notification.translate-x-full {
      transform: translateX(100%);
    }
    
    @keyframes slideInRight {
      from {
        transform: translateX(100%);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }
    
    /* Notification Permission Banner */
    .notification-permission-banner {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 60;
      background: linear-gradient(135deg, #3B82F6, #1D4ED8);
      color: white;
      padding: 1rem;
      text-align: center;
      transform: translateY(-100%);
      transition: transform 0.3s ease;
    }
    
    .notification-permission-banner.show {
      transform: translateY(0);
    }
    
    /* Notification Badge */
    .notification-badge {
      position: absolute;
      top: -2px;
      right: -2px;
      background: #EF4444;
      color: white;
      border-radius: 50%;
      width: 12px;
      height: 12px;
      font-size: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 2px solid rgba(255, 255, 255, 0.2);
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
      <span id="notificationBadge" class="notification-badge" style="display: none;">0</span>
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

  <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-6 space-y-8" style="padding-top: 6rem;">    <!-- Dynamic Greeting -->
    <div class="text-left mb-12">
      <h1 class="text-4xl md:text-6xl font-bold greeting-text mb-4">
        <?php
        $hour = date('H');
        $greeting = $hour < 12 ? 'Guten Morgen' : ($hour < 18 ? 'Guten Tag' : 'Guten Abend');
        echo $greeting;
        ?> <?= htmlspecialchars($user['first_name'] ?? $user['username']) ?>
      </h1>
      <p class="text-xl text-white/70">
        <?= date('l, d. F Y') ?>
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
        
        <div class="widget-content">
          <div class="p-6 pb-0 flex-1">
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
          </div>
          
          <div class="widget-buttons">
            <div class="grid grid-cols-2 gap-3">
              <button onclick="window.location.href='inbox.php'" class="quick-action-btn px-4 py-2">
                Inbox
              </button>
              <button onclick="window.location.href='create_task.php'" class="quick-action-btn px-4 py-2">
                Neue Aufgabe
              </button>
            </div>
          </div>
        </div>
      </div>      <!-- Calendar Short -->
      <div class="dashboard-short">
        <div class="short-header p-6" onclick="window.location.href='calendar.php?view=week'">
          <div class="flex items-center justify-between">
            <h3 class="text-white font-semibold text-xl">Kalender</h3>
            <div class="text-right">
              <div class="stats-number text-3xl"><?= count($todayEvents ?? []) ?></div>
              <div class="text-white/60 text-sm">heute</div>
            </div>
          </div>
        </div>

        <div class="widget-content">
          <div class="p-6 pb-0 flex-1">
            <div class="short-scroll space-y-2">
              <?php
                $weekStart = new DateTimeImmutable('monday this week');
                for ($i = 0; $i < 7; $i++):
                  $day = $weekStart->modify("+{$i} days");
                  $isToday = $day->format('Y-m-d') === date('Y-m-d');
                  $dayEvents = $eventsByDate[$day->format('Y-m-d')] ?? [];
              ?>
                <div class="short-list-item p-3 <?= $isToday ? 'bg-purple-600/30' : '' ?>" onclick="window.location.href='calendar.php?view=day&year=<?= $day->format('Y') ?>&month=<?= $day->format('m') ?>&day=<?= $day->format('d') ?>'">
                  <div class="flex justify-between items-center">
                    <span class="text-white text-sm font-medium">
                      <?= $day->format('D d.m') ?>
                    </span>
                    <span class="text-white/60 text-xs"><?= count($dayEvents) ?></span>
                  </div>
                  <?php foreach(array_slice($dayEvents, 0, 2) as $event): ?>
                    <div class="flex justify-between text-xs text-white/80 mt-1">
                      <span class="truncate flex-1">
                        <?= htmlspecialchars($event['title']) ?>
                      </span>
                      <?php if (!empty($event['start_time'])): ?>
                        <span class="text-blue-400 ml-2">
                          <?= substr($event['start_time'], 0, 5) ?>
                        </span>
                      <?php endif; ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endfor; ?>
            </div>
          </div>

          <div class="widget-buttons">
            <button onclick="window.location.href='calendar.php'" class="quick-action-btn w-full px-4 py-2">
              Neuer Termin
            </button>
          </div>
        </div>
      </div>      <!-- Documents Short -->
      <div class="dashboard-short">
        <div class="short-header p-6" onclick="window.location.href='/data-explorer.php'">
          <div class="flex items-center justify-between">
            <h3 class="text-white font-semibold text-xl">Dokumente</h3>
            <div class="text-right">
              <div class="stats-number text-3xl"><?= $docCount ?></div>
              <div class="text-white/60 text-sm">gesamt</div>
            </div>
          </div>
        </div>
        
        <div class="widget-content">
          <div class="p-6 pb-0 flex-1">
            <div class="short-scroll space-y-3">              <?php if (!empty($recentDocuments)): ?>
                  <?php foreach(array_slice($recentDocuments, 0, 4) as $doc): ?>
                  <div class="short-list-item p-4" onclick="window.location.href='/data-explorer.php'">
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
          </div>
            <div class="widget-buttons">
        <button onclick="window.location.href='/data-explorer.php'" class="quick-action-btn w-full px-4 py-2">
              Öffnen
            </button>
          </div>
        </div>
      </div>      <!-- Notes Widget - Add this new widget after Documents Short -->
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
        
        <div class="widget-content">
          <div class="p-6 pb-0 flex-1">
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
          </div>
          
          <div class="widget-buttons">
            <div class="grid grid-cols-2 gap-3">
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
      </div>      <!-- HaveToPay Short - Keep existing balance layout -->
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
        
        <div class="widget-content">
          <div class="p-6 pb-0 flex-1">
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
          </div>
          
          <div class="widget-buttons">
            <button onclick="window.location.href='havetopay.php'" class="quick-action-btn w-full px-4 py-2">
              Ausgabe hinzufügen
            </button>
          </div>
        </div>
      </div>      <!-- System Stats Short -->
      <div class="dashboard-short">
        <div class="short-header p-6" onclick="window.location.href='profile.php'">
          <h3 class="text-white font-semibold text-xl">Statistiken</h3>
        </div>
        
        <div class="widget-content">
          <div class="p-6 pb-0 flex-1 space-y-4">
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
              <span class="text-white font-semibold"><?= $totalWeekEvents ?></span>
            </div>
            <div class="progress-bar">
              <div class="progress-fill bg-gradient-to-r from-purple-500 to-purple-400" style="width: <?= min(100, $totalWeekEvents * 20) ?>%"></div>
            </div>
          </div>
          
          <div class="widget-buttons">
            <button onclick="window.location.href='profile.php'" class="quick-action-btn w-full px-4 py-2">
              <i class="fas fa-user mr-2"></i>Profil
            </button>
          </div>
        </div>
      </div>    </div>    <!-- Recent Activity -->
    <div class="dashboard-short mt-8">
      <div class="short-header p-6">
        <h3 class="text-white font-semibold text-xl">Letzte Aktivität</h3>
      </div>
      
      <div class="widget-content">
        <div class="p-6 pb-0 flex-1">
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
        </div>

        <!-- Node View -->
        <div class="node-view-container" id="nodeView" style="display: none;">
          <div class="node-canvas" id="nodeCanvas">
            <!-- Node visualization will be rendered here -->
          </div>
          <div class="absolute top-4 right-4 text-white/60 text-sm">
            <i class="fas fa-info-circle mr-1"></i>
            Ziehen Sie Notizen um sie zu verschieben
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
    }

    // Theme Toggle Function
    function toggleTheme() {
      const body = document.body;
      const currentTheme = localStorage.getItem('theme') || 'dark';
      const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
      
      localStorage.setItem('theme', newTheme);
      
      if (newTheme === 'light') {
        body.style.background = 'linear-gradient(135deg, #f8fafc 0%, #e2e8f0 30%, #cbd5e1 100%)';
        body.style.color = '#1f2937';
        
        // Update glassmorphism containers for light theme
        const glassContainers = document.querySelectorAll('.dashboard-short, .glassmorphism-container');
        glassContainers.forEach(container => {
          container.style.background = 'rgba(255, 255, 255, 0.25)';
          container.style.borderColor = 'rgba(255, 255, 255, 0.3)';
        });
        
        // Update control bar icon
        const themeIcon = document.querySelector('.control-icon [class*="moon"]');
        if (themeIcon) {
          themeIcon.className = 'fas fa-sun text-sm';
        }
      } else {
        body.style.background = gradients[currentGradient];
        body.style.color = '#ffffff';
        
        // Restore dark theme glassmorphism
        const glassContainers = document.querySelectorAll('.dashboard-short, .glassmorphism-container');
        glassContainers.forEach(container => {
          container.style.background = 'rgba(255, 255, 255, 0.08)';
          container.style.borderColor = 'rgba(255, 255, 255, 0.15)';
        });
        
        // Update control bar icon
        const themeIcon = document.querySelector('.control-icon [class*="sun"]');
        if (themeIcon) {
          themeIcon.className = 'fas fa-moon text-sm';
        }
      }
      
      showNotification(`Theme zu ${newTheme === 'light' ? 'Hell' : 'Dunkel'} gewechselt`, 'success');
    }

    // Compact Mode Toggle Function
    function toggleCompactMode() {
      const isCompact = localStorage.getItem('compactMode') === 'true';
      const newCompact = !isCompact;
      
      localStorage.setItem('compactMode', newCompact);
      
      const widgets = document.querySelectorAll('.dashboard-short');
      const controlIcon = document.querySelector('.control-icon [class*="compress"]');
      
      if (newCompact) {
        widgets.forEach(widget => {
          widget.style.transform = 'scale(0.85)';
          widget.style.margin = '0.5rem';
        });
        
        if (controlIcon) {
          controlIcon.className = 'fas fa-expand text-sm';
        }
        
        showNotification('Kompakter Modus aktiviert', 'success');
      } else {
        widgets.forEach(widget => {
          widget.style.transform = 'scale(1)';
          widget.style.margin = '1rem';
        });
        
        if (controlIcon) {
          controlIcon.className = 'fas fa-compress text-sm';
        }
        
        showNotification('Kompakter Modus deaktiviert', 'success');
      }
    }

    // Notification Settings Function
    function openNotificationSettings() {
      const modal = createModal('Benachrichtigungseinstellungen', `
        <div class="space-y-6">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="font-medium text-white">Desktop-Benachrichtigungen</h4>
              <p class="text-sm text-white/60">Erhalten Sie Benachrichtigungen auf Ihrem Desktop</p>
            </div>
            <label class="switch">
              <input type="checkbox" id="desktopNotifications" ${Notification.permission === 'granted' ? 'checked' : ''}>
              <span class="slider"></span>
            </label>
          </div>
          
          <div class="flex items-center justify-between">
            <div>
              <h4 class="font-medium text-white">Aufgaben-Erinnerungen</h4>
              <p class="text-sm text-white/60">Benachrichtigungen für fällige Aufgaben</p>
            </div>
            <label class="switch">
              <input type="checkbox" id="taskReminders" ${localStorage.getItem('taskReminders') !== 'false' ? 'checked' : ''}>
              <span class="slider"></span>
            </label>
          </div>
          
          <div class="flex items-center justify-between">
            <div>
              <h4 class="font-medium text-white">Ereignis-Benachrichtigungen</h4>
              <p class="text-sm text-white/60">Benachrichtigungen für anstehende Ereignisse</p>
            </div>
            <label class="switch">
              <input type="checkbox" id="eventNotifications" ${localStorage.getItem('eventNotifications') !== 'false' ? 'checked' : ''}>
              <span class="slider"></span>
            </label>
          </div>
          
          <div class="flex items-center justify-between">
            <div>
              <h4 class="font-medium text-white">Sound-Benachrichtigungen</h4>
              <p class="text-sm text-white/60">Akustische Signale für Benachrichtigungen</p>
            </div>
            <label class="switch">
              <input type="checkbox" id="soundNotifications" ${localStorage.getItem('soundNotifications') !== 'false' ? 'checked' : ''}>
              <span class="slider"></span>
            </label>
          </div>
        </div>
      `);
      
      // Add event listeners for notification settings
      const desktopNotifications = modal.querySelector('#desktopNotifications');
      const taskReminders = modal.querySelector('#taskReminders');
      const eventNotifications = modal.querySelector('#eventNotifications');
      const soundNotifications = modal.querySelector('#soundNotifications');
      
      desktopNotifications.addEventListener('change', function() {
        if (this.checked) {
          Notification.requestPermission().then(permission => {
            if (permission !== 'granted') {
              this.checked = false;
              showNotification('Benachrichtigungen wurden nicht erlaubt', 'error');
            }
          });
        }
      });
      
      taskReminders.addEventListener('change', function() {
        localStorage.setItem('taskReminders', this.checked);
      });
      
      eventNotifications.addEventListener('change', function() {
        localStorage.setItem('eventNotifications', this.checked);
      });
      
      soundNotifications.addEventListener('change', function() {
        localStorage.setItem('soundNotifications', this.checked);
      });
    }

    // Layout Settings Function
    function openLayoutSettings() {
      const modal = createModal('Layout-Einstellungen', `
        <div class="space-y-6">
          <div>
            <h4 class="font-medium text-white mb-3">Widget-Anordnung</h4>
            <div class="grid grid-cols-2 gap-4">
              <button class="layout-option p-4 rounded-lg border-2 border-white/20 hover:border-white/40 transition-colors" onclick="applyLayout('default')">
                <div class="grid grid-cols-2 gap-1 mb-2">
                  <div class="bg-white/30 h-4 rounded"></div>
                  <div class="bg-white/30 h-4 rounded"></div>
                  <div class="bg-white/30 h-4 rounded"></div>
                  <div class="bg-white/30 h-4 rounded"></div>
                </div>
                <span class="text-sm text-white">Standard</span>
              </button>
              
              <button class="layout-option p-4 rounded-lg border-2 border-white/20 hover:border-white/40 transition-colors" onclick="applyLayout('compact')">
                <div class="grid grid-cols-3 gap-1 mb-2">
                  <div class="bg-white/30 h-3 rounded"></div>
                  <div class="bg-white/30 h-3 rounded"></div>
                  <div class="bg-white/30 h-3 rounded"></div>
                  <div class="bg-white/30 h-3 rounded"></div>
                  <div class="bg-white/30 h-3 rounded"></div>
                  <div class="bg-white/30 h-3 rounded"></div>
                </div>
                <span class="text-sm text-white">Kompakt</span>
              </button>
              
              <button class="layout-option p-4 rounded-lg border-2 border-white/20 hover:border-white/40 transition-colors" onclick="applyLayout('wide')">
                <div class="grid grid-cols-1 gap-1 mb-2">
                  <div class="bg-white/30 h-4 rounded"></div>
                  <div class="bg-white/30 h-4 rounded"></div>
                  <div class="bg-white/30 h-4 rounded"></div>
                </div>
                <span class="text-sm text-white">Breit</span>
              </button>
              
              <button class="layout-option p-4 rounded-lg border-2 border-white/20 hover:border-white/40 transition-colors" onclick="applyLayout('sidebar')">
                <div class="grid grid-cols-3 gap-1 mb-2">
                  <div class="bg-white/30 h-4 rounded"></div>
                  <div class="bg-white/30 h-4 rounded col-span-2"></div>
                  <div class="bg-white/30 h-4 rounded"></div>
                  <div class="bg-white/30 h-4 rounded col-span-2"></div>
                </div>
                <span class="text-sm text-white">Seitenleiste</span>
              </button>
            </div>
          </div>
          
          <div>
            <h4 class="font-medium text-white mb-3">Widget-Größe</h4>
            <div class="flex gap-2">
              <button class="px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20 transition-colors text-white" onclick="adjustWidgetSize('small')">Klein</button>
              <button class="px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20 transition-colors text-white" onclick="adjustWidgetSize('medium')">Mittel</button>
              <button class="px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20 transition-colors text-white" onclick="adjustWidgetSize('large')">Groß</button>
            </div>
          </div>
          
          <div>
            <h4 class="font-medium text-white mb-3">Dashboard-Optionen</h4>
            <div class="space-y-3">
              <label class="flex items-center gap-3">
                <input type="checkbox" id="showGreeting" ${localStorage.getItem('showGreeting') !== 'false' ? 'checked' : ''} class="rounded">
                <span class="text-white">Begrüßung anzeigen</span>
              </label>
              <label class="flex items-center gap-3">
                <input type="checkbox" id="showStats" ${localStorage.getItem('showStats') !== 'false' ? 'checked' : ''} class="rounded">
                <span class="text-white">Statistiken anzeigen</span>
              </label>
              <label class="flex items-center gap-3">
                <input type="checkbox" id="animateWidgets" ${localStorage.getItem('animateWidgets') !== 'false' ? 'checked' : ''} class="rounded">
                <span class="text-white">Widget-Animationen</span>
              </label>
            </div>
          </div>
        </div>
      `);
      
      // Add event listeners for layout settings
      const showGreeting = modal.querySelector('#showGreeting');
      const showStats = modal.querySelector('#showStats');
      const animateWidgets = modal.querySelector('#animateWidgets');
      
      showGreeting.addEventListener('change', function() {
        localStorage.setItem('showGreeting', this.checked);
        const greetingElement = document.querySelector('.greeting-text');
        if (greetingElement) {
          greetingElement.style.display = this.checked ? 'block' : 'none';
        }
      });
      
      showStats.addEventListener('change', function() {
        localStorage.setItem('showStats', this.checked);
        const statsElements = document.querySelectorAll('.stats-number');
        statsElements.forEach(el => {
          el.style.display = this.checked ? 'block' : 'none';
        });
      });
      
      animateWidgets.addEventListener('change', function() {
        localStorage.setItem('animateWidgets', this.checked);
        const widgets = document.querySelectorAll('.dashboard-short');
        widgets.forEach(widget => {
          widget.style.transition = this.checked ? 'all 0.3s ease' : 'none';
        });
      });
    }

    // System Settings Function
    function openSystemSettings() {
      const modal = createModal('System-Einstellungen', `
        <div class="space-y-6">
          <div>
            <h4 class="font-medium text-white mb-3">Sprache</h4>
            <select id="languageSelect" class="w-full p-3 rounded-lg bg-white/10 border border-white/20 text-white">
              <option value="de">Deutsch</option>
              <option value="en">English</option>
              <option value="es">Español</option>
              <option value="fr">Français</option>
            </select>
          </div>
          
          <div>
            <h4 class="font-medium text-white mb-3">Zeitzone</h4>
            <select id="timezoneSelect" class="w-full p-3 rounded-lg bg-white/10 border border-white/20 text-white">
              <option value="Europe/Berlin">Europa/Berlin</option>
              <option value="Europe/London">Europa/London</option>
              <option value="America/New_York">Amerika/New York</option>
              <option value="America/Los_Angeles">Amerika/Los Angeles</option>
              <option value="Asia/Tokyo">Asien/Tokyo</option>
            </select>
          </div>
          
          <div>
            <h4 class="font-medium text-white mb-3">Automatische Updates</h4>
            <label class="flex items-center gap-3">
              <input type="checkbox" id="autoUpdates" ${localStorage.getItem('autoUpdates') !== 'false' ? 'checked' : ''} class="rounded">
              <span class="text-white">Automatische Updates aktivieren</span>
            </label>
          </div>
          
          <div>
            <h4 class="font-medium text-white mb-3">Entwickleroptionen</h4>
            <div class="space-y-3">
              <label class="flex items-center gap-3">
                <input type="checkbox" id="debugMode" ${localStorage.getItem('debugMode') === 'true' ? 'checked' : ''} class="rounded">
                <span class="text-white">Debug-Modus</span>
              </label>
              <label class="flex items-center gap-3">
                <input type="checkbox" id="showPerformance" ${localStorage.getItem('showPerformance') === 'true' ? 'checked' : ''} class="rounded">
                <span class="text-white">Performance-Metriken anzeigen</span>
              </label>
            </div>
          </div>
          
          <div class="pt-4 border-t border-white/20">
            <h4 class="font-medium text-white mb-3">Daten & Privatsphäre</h4>
            <div class="space-y-3">
              <button class="w-full p-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition-colors" onclick="exportData()">
                <i class="fas fa-download mr-2"></i>
                Daten exportieren
              </button>
              <button class="w-full p-3 rounded-lg bg-yellow-600 hover:bg-yellow-700 text-white transition-colors" onclick="clearCache()">
                <i class="fas fa-trash mr-2"></i>
                Cache leeren
              </button>
              <button class="w-full p-3 rounded-lg bg-red-600 hover:bg-red-700 text-white transition-colors" onclick="resetSettings()">
                <i class="fas fa-undo mr-2"></i>
                Einstellungen zurücksetzen
              </button>
            </div>
          </div>
        </div>
      `);
      
      // Add event listeners for system settings
      const languageSelect = modal.querySelector('#languageSelect');
      const timezoneSelect = modal.querySelector('#timezoneSelect');
      const autoUpdates = modal.querySelector('#autoUpdates');
      const debugMode = modal.querySelector('#debugMode');
      const showPerformance = modal.querySelector('#showPerformance');
      
      // Set current values
      languageSelect.value = localStorage.getItem('language') || 'de';
      timezoneSelect.value = localStorage.getItem('timezone') || 'Europe/Berlin';
      
      languageSelect.addEventListener('change', function() {
        localStorage.setItem('language', this.value);
        showNotification('Sprache geändert. Seite wird neu geladen...', 'info');
        setTimeout(() => location.reload(), 2000);
      });
      
      timezoneSelect.addEventListener('change', function() {
        localStorage.setItem('timezone', this.value);
        showNotification('Zeitzone geändert', 'success');
      });
      
      autoUpdates.addEventListener('change', function() {
        localStorage.setItem('autoUpdates', this.checked);
      });
      
      debugMode.addEventListener('change', function() {
        localStorage.setItem('debugMode', this.checked);
        console.log('Debug-Modus:', this.checked ? 'aktiviert' : 'deaktiviert');
      });
      
      showPerformance.addEventListener('change', function() {
        localStorage.setItem('showPerformance', this.checked);
        togglePerformanceMetrics(this.checked);
      });
    }    // Helper Functions for Control Bar
    function createModal(title, content) {
      const modal = document.createElement('div');
      modal.className = 'fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4';
      modal.innerHTML = `
        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
          <div class="p-6 border-b border-white/20">
            <div class="flex items-center justify-between">
              <h3 class="text-xl font-semibold text-white">${title}</h3>
              <button class="text-white/60 hover:text-white transition-colors" onclick="this.closest('.fixed').remove()">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <div class="p-6">
            ${content}
          </div>
        </div>
      `;
      
      document.body.appendChild(modal);
      
      // Close on background click
      modal.addEventListener('click', function(e) {
        if (e.target === modal) {
          modal.remove();
        }
      });
      
      return modal;
    }

    function showNotification(message, type = 'info') {
      const notification = document.createElement('div');
      notification.className = `fixed top-20 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transform translate-x-full transition-transform duration-300 ${
        type === 'success' ? 'bg-green-600 text-white' :
        type === 'error' ? 'bg-red-600 text-white' :
        type === 'warning' ? 'bg-yellow-600 text-white' :
        'bg-blue-600 text-white'
      }`;
      
      notification.innerHTML = `
        <div class="flex items-center gap-3">
          <i class="fas ${
            type === 'success' ? 'fa-check-circle' :
            type === 'error' ? 'fa-exclamation-circle' :
            type === 'warning' ? 'fa-exclamation-triangle' :
            'fa-info-circle'
          }"></i>
          <span>${message}</span>
        </div>
      `;
      
      document.body.appendChild(notification);
      
      // Animate in
      setTimeout(() => {
        notification.style.transform = 'translateX(0)';
      }, 100);
      
      // Auto remove after 5 seconds
      setTimeout(() => {
        notification.style.transform = 'translateX(full)';
        setTimeout(() => {
          notification.remove();
        }, 300);
      }, 5000);
    }

    function applyLayout(layoutType) {
      const dashboard = document.querySelector('.dashboard-grid');
      if (!dashboard) return;
      
      localStorage.setItem('dashboardLayout', layoutType);
      
      switch (layoutType) {
        case 'compact':
          dashboard.className = 'dashboard-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4';
          break;
        case 'wide':
          dashboard.className = 'dashboard-grid grid grid-cols-1 lg:grid-cols-2 gap-6';
          break;
        case 'sidebar':
          dashboard.className = 'dashboard-grid grid grid-cols-1 lg:grid-cols-3 gap-6';
          break;
        default:
          dashboard.className = 'dashboard-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6';
      }
      
      showNotification(`Layout zu "${layoutType}" geändert`, 'success');
    }

    function adjustWidgetSize(size) {
      const widgets = document.querySelectorAll('.dashboard-short');
      localStorage.setItem('widgetSize', size);
      
      widgets.forEach(widget => {
        switch (size) {
          case 'small':
            widget.style.transform = 'scale(0.8)';
            break;
          case 'large':
            widget.style.transform = 'scale(1.1)';
            break;
          default:
            widget.style.transform = 'scale(1)';
        }
      });
      
      showNotification(`Widget-Größe zu "${size}" geändert`, 'success');
    }

    function exportData() {
      const data = {
        settings: {
          theme: localStorage.getItem('theme'),
          gradient: localStorage.getItem('dashboardGradient'),
          layout: localStorage.getItem('dashboardLayout'),
          compactMode: localStorage.getItem('compactMode'),
          language: localStorage.getItem('language'),
          timezone: localStorage.getItem('timezone')
        },
        timestamp: new Date().toISOString()
      };
      
      const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `privatevault-settings-${new Date().toISOString().split('T')[0]}.json`;
      a.click();
      URL.revokeObjectURL(url);
      
      showNotification('Daten erfolgreich exportiert', 'success');
    }

    function clearCache() {
      // Clear localStorage except for auth data
      const authKeys = ['user_id', 'username', 'is_admin'];
      const tempData = {};
      
      authKeys.forEach(key => {
        if (localStorage.getItem(key)) {
          tempData[key] = localStorage.getItem(key);
        }
      });
      
      localStorage.clear();
      
      // Restore auth data
      Object.keys(tempData).forEach(key => {
        localStorage.setItem(key, tempData[key]);
      });
      
      showNotification('Cache erfolgreich geleert', 'success');
      setTimeout(() => location.reload(), 2000);
    }

    function resetSettings() {
      if (confirm('Sind Sie sicher, dass Sie alle Einstellungen zurücksetzen möchten?')) {
        const authKeys = ['user_id', 'username', 'is_admin'];
        const tempData = {};
        
        authKeys.forEach(key => {
          if (localStorage.getItem(key)) {
            tempData[key] = localStorage.getItem(key);
          }
        });
        
        localStorage.clear();
        
        // Restore auth data
        Object.keys(tempData).forEach(key => {
          localStorage.setItem(key, tempData[key]);
        });
        
        showNotification('Einstellungen zurückgesetzt. Seite wird neu geladen...', 'info');
        setTimeout(() => location.reload(), 2000);
      }
    }

    function togglePerformanceMetrics(show) {
      if (show) {
        // Create performance overlay
        const overlay = document.createElement('div');
        overlay.id = 'performanceOverlay';
        overlay.className = 'fixed bottom-4 left-4 bg-black/80 text-white p-4 rounded-lg text-sm z-50';
        overlay.innerHTML = `
          <div class="space-y-2">
            <div>FPS: <span id="fpsCounter">--</span></div>
            <div>Memory: <span id="memoryUsage">--</span></div>
            <div>Load Time: <span id="loadTime">${performance.now().toFixed(2)}ms</span></div>
          </div>
        `;
        
        document.body.appendChild(overlay);
        
        // Update FPS counter
        let frames = 0;
        let lastTime = Date.now();
        
        function updateFPS() {
          frames++;
          const now = Date.now();
          
          if (now - lastTime >= 1000) {
            const fps = Math.round((frames * 1000) / (now - lastTime));
            const fpsCounter = document.getElementById('fpsCounter');
            if (fpsCounter) {
              fpsCounter.textContent = fps;
            }
            frames = 0;
            lastTime = now;
          }
          
          if (document.getElementById('performanceOverlay')) {
            requestAnimationFrame(updateFPS);
          }
        }
        
        requestAnimationFrame(updateFPS);
        
        // Update memory usage if available
        if (performance.memory) {
          setInterval(() => {
            const memoryUsage = document.getElementById('memoryUsage');
            if (memoryUsage) {
              const used = Math.round(performance.memory.usedJSHeapSize / 1024 / 1024);
              memoryUsage.textContent = `${used}MB`;
            }
          }, 1000);
        }
      } else {
        const overlay = document.getElementById('performanceOverlay');
        if (overlay) {
          overlay.remove();
        }
      }
    }

    // Initialize settings on page load
    function initializeSettings() {
      // Apply saved theme
      const savedTheme = localStorage.getItem('theme');
      if (savedTheme === 'light') {
        toggleTheme(); // This will switch to light theme
      }
      
      // Apply saved gradient
      const savedGradient = localStorage.getItem('dashboardGradient');
      if (savedGradient && gradients[savedGradient]) {
        document.body.style.background = gradients[savedGradient];
      }
      
      // Apply saved compact mode
      const savedCompactMode = localStorage.getItem('compactMode');
      if (savedCompactMode === 'true') {
        toggleCompactMode();
      }
      
      // Apply saved layout
      const savedLayout = localStorage.getItem('dashboardLayout');
      if (savedLayout) {
        applyLayout(savedLayout);
      }
      
      // Apply saved widget size
      const savedWidgetSize = localStorage.getItem('widgetSize');
      if (savedWidgetSize) {
        adjustWidgetSize(savedWidgetSize);
      }
      
      // Apply saved dashboard options
      const showGreeting = localStorage.getItem('showGreeting');
      if (showGreeting === 'false') {
        const greetingElement = document.querySelector('.greeting-text');
        if (greetingElement) {
          greetingElement.style.display = 'none';
        }
      }
      
      const showStats = localStorage.getItem('showStats');
      if (showStats === 'false') {
        const statsElements = document.querySelectorAll('.stats-number');
        statsElements.forEach(el => {
          el.style.display = 'none';
        });
      }
      
      const animateWidgets = localStorage.getItem('animateWidgets');
      if (animateWidgets === 'false') {
        const widgets = document.querySelectorAll('.dashboard-short');
        widgets.forEach(widget => {
          widget.style.transition = 'none';
        });
      }
      
      // Show performance metrics if enabled
      const showPerformance = localStorage.getItem('showPerformance');
      if (showPerformance === 'true') {
        togglePerformanceMetrics(true);
      }
    }

    // System Settings Function
    function openSystemSettings() {
      const modal = createModal('System-Einstellungen', `
        <div class="space-y-6">
          <div>
            <h4 class="font-medium text-white mb-3">Sprache</h4>
            <select id="languageSelect" class="w-full p-3 rounded-lg bg-white/10 border border-white/20 text-white">
              <option value="de">Deutsch</option>
              <option value="en">English</option>
              <option value="es">Español</option>
              <option value="fr">Français</option>
            </select>
          </div>
          
          <div>
            <h4 class="font-medium text-white mb-3">Zeitzone</h4>
            <select id="timezoneSelect" class="w-full p-3 rounded-lg bg-white/10 border border-white/20 text-white">
              <option value="Europe/Berlin">Europa/Berlin</option>
              <option value="Europe/London">Europa/London</option>
              <option value="America/New_York">Amerika/New York</option>
              <option value="America/Los_Angeles">Amerika/Los Angeles</option>
              <option value="Asia/Tokyo">Asien/Tokyo</option>
            </select>
          </div>
          
          <div>
            <h4 class="font-medium text-white mb-3">Automatische Updates</h4>
            <label class="flex items-center gap-3">
              <input type="checkbox" id="autoUpdates" ${localStorage.getItem('autoUpdates') !== 'false' ? 'checked' : ''} class="rounded">
              <span class="text-white">Automatische Updates aktivieren</span>
            </label>
          </div>
          
          <div>
            <h4 class="font-medium text-white mb-3">Entwickleroptionen</h4>
            <div class="space-y-3">
              <label class="flex items-center gap-3">
                <input type="checkbox" id="debugMode" ${localStorage.getItem('debugMode') === 'true' ? 'checked' : ''} class="rounded">
                <span class="text-white">Debug-Modus</span>
              </label>
              <label class="flex items-center gap-3">
                <input type="checkbox" id="showPerformance" ${localStorage.getItem('showPerformance') === 'true' ? 'checked' : ''} class="rounded">
                <span class="text-white">Performance-Metriken anzeigen</span>
              </label>
            </div>
          </div>
          
          <div class="pt-4 border-t border-white/20">
            <h4 class="font-medium text-white mb-3">Daten & Privatsphäre</h4>
            <div class="space-y-3">
              <button class="w-full p-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition-colors" onclick="exportData()">
                <i class="fas fa-download mr-2"></i>
                Daten exportieren
              </button>
              <button class="w-full p-3 rounded-lg bg-yellow-600 hover:bg-yellow-700 text-white transition-colors" onclick="clearCache()">
                <i class="fas fa-trash mr-2"></i>
                Cache leeren
              </button>
              <button class="w-full p-3 rounded-lg bg-red-600 hover:bg-red-700 text-white transition-colors" onclick="resetSettings()">
                <i class="fas fa-undo mr-2"></i>
                Einstellungen zurücksetzen
              </button>
            </div>
          </div>
        </div>
      `);
      
      // Add event listeners for system settings
      const languageSelect = modal.querySelector('#languageSelect');
      const timezoneSelect = modal.querySelector('#timezoneSelect');
      const autoUpdates = modal.querySelector('#autoUpdates');
      const debugMode = modal.querySelector('#debugMode');
      const showPerformance = modal.querySelector('#showPerformance');
      
      languageSelect.addEventListener('change', function() {
        localStorage.setItem('language', this.value);
      });
      
      timezoneSelect.addEventListener('change', function() {
        localStorage.setItem('timezone', this.value);
      });
      
      autoUpdates.addEventListener('change', function() {
        localStorage.setItem('autoUpdates', this.checked);
      });
      
      debugMode.addEventListener('change', function() {
        localStorage.setItem('debugMode', this.checked);
      });
      
      showPerformance.addEventListener('change', function() {
        localStorage.setItem('showPerformance', this.checked);
        togglePerformanceMetrics(this.checked);
      });
    }

    // Helper Functions for Control Bar
    function createModal(title, content) {
      const modal = document.createElement('div');
      modal.className = 'fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4';
      modal.innerHTML = `
        <div class="bg-gray-900/95 backdrop-blur-xl border border-white/20 rounded-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
          <div class="p-6 border-b border-white/10">
            <div class="flex items-center justify-between">
              <h2 class="text-xl font-semibold text-white">${title}</h2>
              <button class="text-white/60 hover:text-white transition-colors" onclick="this.closest('.fixed').remove()">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <div class="p-6">
            ${content}
          </div>
        </div>
      `;
      
      document.body.appendChild(modal);
      
      // Close on background click
      modal.addEventListener('click', function(e) {
        if (e.target === modal) {
          modal.remove();
        }
      });
      
      return modal;
    }

    // Control Bar State Functions
    function updateControlBarState() {
      // Update theme icon
      const themeIcon = document.querySelector('.control-icon [class*="moon"], .control-icon [class*="sun"]');
      if (themeIcon) {
        const currentTheme = localStorage.getItem('theme') || 'dark';
        themeIcon.className = currentTheme === 'dark' ? 'fas fa-moon text-sm' : 'fas fa-sun text-sm';
      }
      
      // Update compact mode icon
      const compactIcon = document.querySelector('.control-icon [class*="compress"], .control-icon [class*="expand"]');
      if (compactIcon) {
        const isCompact = localStorage.getItem('compactMode') === 'true';
        compactIcon.className = isCompact ? 'fas fa-expand text-sm' : 'fas fa-compress text-sm';
      }
    }

    function setupKeyboardShortcuts() {
      document.addEventListener('keydown', function(e) {
        if (e.ctrlKey || e.metaKey) {
          switch (e.key) {
            case 'p':
              e.preventDefault();
              openGradientPicker();
              break;
            case 't':
              e.preventDefault();
              toggleTheme();
              break;
            case 'l':
              e.preventDefault();
              openLayoutSettings();
              break;
            case 'n':
              e.preventDefault();
              openNotificationSettings();
              break;
          }
        }
      });
    }

    function initializeTooltips() {
      // Tooltip functionality is already handled by the title attributes
    }

    function setupDashboardWidgets() {
      // Apply dynamic layout
      applyDynamicLayout();
      
      // Setup resize observer for responsive layout
      if (window.ResizeObserver) {
        const resizeObserver = new ResizeObserver(() => {
          applyDynamicLayout();
        });
        
        const dashboard = document.querySelector('.dashboard-grid');
        if (dashboard) {
          resizeObserver.observe(dashboard);
        }
      }
    }

    function setupGreeting() {
      // Greeting is already rendered server-side
    }

    function setupAutosave() {
      // Auto-save settings every 30 seconds
      setInterval(() => {
        // Settings are already saved in localStorage when changed
      }, 30000);
    }

    // Layout Settings Function
    function openLayoutSettings() {
      const modal = createModal('Layout-Einstellungen', `
        <div class="space-y-6">
          <div>
            <h4 class="font-medium text-white mb-3">Widget-Größe</h4>
            <div class="grid grid-cols-3 gap-3">
              <button class="p-3 rounded-lg bg-white/10 border border-white/20 text-white hover:bg-white/20 transition-colors" onclick="setWidgetSize('small')">
                <i class="fas fa-th-large mb-1"></i>
                <div class="text-xs">Klein</div>
              </button>
              <button class="p-3 rounded-lg bg-white/10 border border-white/20 text-white hover:bg-white/20 transition-colors" onclick="setWidgetSize('medium')">
                <i class="fas fa-th mb-1"></i>
                <div class="text-xs">Mittel</div>
              </button>
              <button class="p-3 rounded-lg bg-white/10 border border-white/20 text-white hover:bg-white/20 transition-colors" onclick="setWidgetSize('large')">
                <i class="fas fa-th-large mb-1"></i>
                <div class="text-xs">Groß</div>
              </button>
            </div>
          </div>
          
          <div>
            <h4 class="font-medium text-white mb-3">Spalten</h4>
            <div class="flex items-center gap-3">
              <label class="text-white">Desktop:</label>
              <input type="range" id="desktopColumns" min="2" max="6" value="${localStorage.getItem('desktopColumns') || '4'}" 
                     class="flex-1" onchange="updateDesktopColumns(this.value)">
              <span id="desktopColumnsValue" class="text-white w-8">${localStorage.getItem('desktopColumns') || '4'}</span>
            </div>
            <div class="flex items-center gap-3 mt-2">
              <label class="text-white">Tablet:</label>
              <input type="range" id="tabletColumns" min="1" max="3" value="${localStorage.getItem('tabletColumns') || '2'}" 
                     class="flex-1" onchange="updateTabletColumns(this.value)">
              <span id="tabletColumnsValue" class="text-white w-8">${localStorage.getItem('tabletColumns') || '2'}</span>
            </div>
          </div>
          
          <div>
            <h4 class="font-medium text-white mb-3">Anzeige-Optionen</h4>
            <div class="space-y-3">
              <label class="flex items-center gap-3">
                <input type="checkbox" id="showGreeting" ${localStorage.getItem('showGreeting') !== 'false' ? 'checked' : ''} class="rounded">
                <span class="text-white">Begrüßung anzeigen</span>
              </label>
              <label class="flex items-center gap-3">
                <input type="checkbox" id="showStats" ${localStorage.getItem('showStats') !== 'false' ? 'checked' : ''} class="rounded">
                <span class="text-white">Statistiken anzeigen</span>
              </label>
              <label class="flex items-center gap-3">
                <input type="checkbox" id="animateWidgets" ${localStorage.getItem('animateWidgets') !== 'false' ? 'checked' : ''} class="rounded">
                <span class="text-white">Widget-Animationen</span>
              </label>
              <label class="flex items-center gap-3">
                <input type="checkbox" id="dynamicLayout" ${localStorage.getItem('dynamicLayout') !== 'false' ? 'checked' : ''} class="rounded">
                <span class="text-white">Dynamisches Layout</span>
              </label>
            </div>
          </div>
          
          <div class="pt-4 border-t border-white/20">
            <button class="w-full p-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition-colors" onclick="resetLayout()">
              <i class="fas fa-undo mr-2"></i>
              Layout zurücksetzen
            </button>
          </div>
        </div>
      `);
      
      // Add event listeners
      const showGreeting = modal.querySelector('#showGreeting');
      const showStats = modal.querySelector('#showStats');
      const animateWidgets = modal.querySelector('#animateWidgets');
      const dynamicLayout = modal.querySelector('#dynamicLayout');
      
      showGreeting.addEventListener('change', function() {
        localStorage.setItem('showGreeting', this.checked);
        applyLayoutSettings();
      });
      
      showStats.addEventListener('change', function() {
        localStorage.setItem('showStats', this.checked);
        applyLayoutSettings();
      });
      
      animateWidgets.addEventListener('change', function() {
        localStorage.setItem('animateWidgets', this.checked);
        applyLayoutSettings();
      });
      
      dynamicLayout.addEventListener('change', function() {
        localStorage.setItem('dynamicLayout', this.checked);
        applyDynamicLayout();
      });
    }

    // Theme Toggle Function
    function toggleTheme() {
      const currentTheme = localStorage.getItem('theme') || 'dark';
      const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
      
      localStorage.setItem('theme', newTheme);
      document.documentElement.setAttribute('data-theme', newTheme);
      
      // Update body class
      document.body.className = document.body.className.replace(/theme-\w+/g, '');
      document.body.classList.add(`theme-${newTheme}`);
      
      // Update control bar icon
      updateControlBarState();
      
      // Apply theme colors
      applyThemeColors(newTheme);
    }

    // Compact Mode Toggle Function
    function toggleCompactMode() {
      const currentCompact = localStorage.getItem('compactMode') === 'true';
      const newCompact = !currentCompact;
      
      localStorage.setItem('compactMode', newCompact);
      
      // Apply compact mode
      const widgets = document.querySelectorAll('.dashboard-short');
      widgets.forEach(widget => {
        if (newCompact) {
          widget.classList.add('compact-mode');
        } else {
          widget.classList.remove('compact-mode');
        }
      });
      
      // Update control bar icon
      updateControlBarState();
      
      // Reapply layout
      applyDynamicLayout();
    }

    // Enhanced Notification Settings Function
    function openNotificationSettings() {
      // First load current settings from server
      loadNotificationSettings().then(settings => {
        const modal = createModal('Benachrichtigungen', `
          <div class="space-y-6">
            <div>
              <h4 class="font-medium text-white mb-3">Desktop-Benachrichtigungen</h4>
              <div class="space-y-3">
                <label class="flex items-center gap-3">
                  <input type="checkbox" id="desktopNotifications" ${settings.desktop_enabled ? 'checked' : ''} class="rounded">
                  <span class="text-white">Desktop-Benachrichtigungen aktivieren</span>
                </label>
                <label class="flex items-center gap-3">
                  <input type="checkbox" id="soundNotifications" ${settings.sound_enabled ? 'checked' : ''} class="rounded">
                  <span class="text-white">Ton-Benachrichtigungen</span>
                </label>
                <label class="flex items-center gap-3">
                  <input type="checkbox" id="emailNotifications" ${settings.email_enabled ? 'checked' : ''} class="rounded">
                  <span class="text-white">E-Mail-Benachrichtigungen</span>
                </label>
              </div>
            </div>
            
            <div>
              <h4 class="font-medium text-white mb-3">Benachrichtigungstypen</h4>
              <div class="space-y-3">
                <label class="flex items-center gap-3">
                  <input type="checkbox" id="taskNotifications" ${settings.task_reminders ? 'checked' : ''} class="rounded">
                  <span class="text-white">Aufgaben-Erinnerungen</span>
                </label>
                <label class="flex items-center gap-3">
                  <input type="checkbox" id="calendarNotifications" ${settings.calendar_events ? 'checked' : ''} class="rounded">
                  <span class="text-white">Kalender-Ereignisse</span>
                </label>
                <label class="flex items-center gap-3">
                  <input type="checkbox" id="noteNotifications" ${settings.note_reminders ? 'checked' : ''} class="rounded">
                  <span class="text-white">Notiz-Erinnerungen</span>
                </label>
                <label class="flex items-center gap-3">
                  <input type="checkbox" id="systemNotifications" ${settings.system_alerts ? 'checked' : ''} class="rounded">
                  <span class="text-white">System-Benachrichtigungen</span>
                </label>
                <label class="flex items-center gap-3">
                  <input type="checkbox" id="financeNotifications" ${settings.finance_updates ? 'checked' : ''} class="rounded">
                  <span class="text-white">Finanz-Updates</span>
                </label>
                <label class="flex items-center gap-3">
                  <input type="checkbox" id="documentNotifications" ${settings.document_uploads ? 'checked' : ''} class="rounded">
                  <span class="text-white">Dokument-Uploads</span>
                </label>
                <label class="flex items-center gap-3">
                  <input type="checkbox" id="securityNotifications" ${settings.security_warnings ? 'checked' : ''} class="rounded">
                  <span class="text-white">Sicherheitswarnungen</span>
                </label>
              </div>
            </div>
            
            <div>
              <h4 class="font-medium text-white mb-3">Benachrichtigungszeiten</h4>
              <div class="space-y-3">
                <div class="flex items-center gap-3">
                  <label class="text-white">Ruhemodus von:</label>
                  <input type="time" id="quietStart" value="${settings.quiet_start || '22:00'}" class="p-2 rounded bg-white/10 border border-white/20 text-white">
                </div>
                <div class="flex items-center gap-3">
                  <label class="text-white">Ruhemodus bis:</label>
                  <input type="time" id="quietEnd" value="${settings.quiet_end || '07:00'}" class="p-2 rounded bg-white/10 border border-white/20 text-white">
                </div>
                <div class="flex items-center gap-3">
                  <label class="text-white">Häufigkeit:</label>
                  <select id="notificationFrequency" class="p-2 rounded bg-white/10 border border-white/20 text-white">
                    <option value="immediate" ${settings.frequency === 'immediate' ? 'selected' : ''}>Sofort</option>
                    <option value="hourly" ${settings.frequency === 'hourly' ? 'selected' : ''}>Stündlich</option>
                    <option value="daily" ${settings.frequency === 'daily' ? 'selected' : ''}>Täglich</option>
                    <option value="weekly" ${settings.frequency === 'weekly' ? 'selected' : ''}>Wöchentlich</option>
                  </select>
                </div>
              </div>
            </div>
            
            <div class="pt-4 border-t border-white/20 space-y-3">
              <button class="w-full p-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition-colors" onclick="testNotification()">
                <i class="fas fa-bell mr-2"></i>
                Test-Benachrichtigung senden
              </button>
              <button class="w-full p-3 rounded-lg bg-green-600 hover:bg-green-700 text-white transition-colors" onclick="saveNotificationSettings()">
                <i class="fas fa-save mr-2"></i>
                Einstellungen speichern
              </button>
            </div>
          </div>
        `);
        
        // Add event listeners for notification settings
        const inputs = modal.querySelectorAll('input[type="checkbox"], input[type="time"], select');
        inputs.forEach(input => {
          input.addEventListener('change', function() {
            // Mark as changed for saving
            this.dataset.changed = 'true';
          });
        });
      });
    }

    // Load notification settings from server
    async function loadNotificationSettings() {
      try {
        const response = await fetch('./api/notifications.php', {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json'
          }
        });
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
          return data.settings;
        } else {
          console.error('Failed to load notification settings:', data.error);
          return getDefaultNotificationSettings();
        }
      } catch (error) {
        console.error('Error loading notification settings:', error);
        return getDefaultNotificationSettings();
      }
    }

    // Get default notification settings
    function getDefaultNotificationSettings() {
      return {
        desktop_enabled: true,
        sound_enabled: true,
        email_enabled: true,
        push_enabled: true,
        task_reminders: true,
        calendar_events: true,
        note_reminders: false,
        system_alerts: true,
        finance_updates: true,
        document_uploads: true,
        security_warnings: true,
        quiet_start: '22:00',
        quiet_end: '07:00',
        frequency: 'immediate'
      };
    }

    // Save notification settings to server
    async function saveNotificationSettings() {
      const modal = document.querySelector('.fixed');
      if (!modal) return;
      
      // Collect all settings
      const settings = {
        desktop_enabled: modal.querySelector('#desktopNotifications').checked,
        sound_enabled: modal.querySelector('#soundNotifications').checked,
        email_enabled: modal.querySelector('#emailNotifications').checked,
        task_reminders: modal.querySelector('#taskNotifications').checked,
        calendar_events: modal.querySelector('#calendarNotifications').checked,
        note_reminders: modal.querySelector('#noteNotifications').checked,
        system_alerts: modal.querySelector('#systemNotifications').checked,
        finance_updates: modal.querySelector('#financeNotifications').checked,
        document_uploads: modal.querySelector('#documentNotifications').checked,
        security_warnings: modal.querySelector('#securityNotifications').checked,
        quiet_start: modal.querySelector('#quietStart').value,
        quiet_end: modal.querySelector('#quietEnd').value,
        frequency: modal.querySelector('#notificationFrequency').value
      };
      
      try {
        const response = await fetch('./api/notifications.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(settings)
        });
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
          showNotification('Benachrichtigungseinstellungen gespeichert!', 'success');
          
          // Update localStorage for immediate use
          Object.keys(settings).forEach(key => {
            localStorage.setItem(key, settings[key]);
          });
          
          // Request notification permission if desktop notifications are enabled
          if (settings.desktop_enabled && 'Notification' in window) {
            if (Notification.permission === 'default') {
              Notification.requestPermission();
            }
          }
        } else {
          showNotification('Fehler beim Speichern: ' + data.error, 'error');
        }
      } catch (error) {
        console.error('Error saving notification settings:', error);
        showNotification('Fehler beim Speichern der Einstellungen', 'error');
      }
    }

    // Helper Functions for Layout Settings
    function setWidgetSize(size) {
      localStorage.setItem('widgetSize', size);
      applyWidgetSizing(size);
    }

    function updateDesktopColumns(value) {
      localStorage.setItem('desktopColumns', value);
      document.getElementById('desktopColumnsValue').textContent = value;
      applyDynamicLayout();
    }

    function updateTabletColumns(value) {
      localStorage.setItem('tabletColumns', value);
      document.getElementById('tabletColumnsValue').textContent = value;
      applyDynamicLayout();
    }

    function resetLayout() {
      // Clear layout settings
      localStorage.removeItem('widgetSize');
      localStorage.removeItem('desktopColumns');
      localStorage.removeItem('tabletColumns');
      localStorage.removeItem('showGreeting');
      localStorage.removeItem('showStats');
      localStorage.removeItem('animateWidgets');
      localStorage.removeItem('dynamicLayout');
      
      // Reload page to apply defaults
      location.reload();
    }

    function applyWidgetSizing(size) {
      const widgets = document.querySelectorAll('.dashboard-short');
      widgets.forEach(widget => {
        widget.classList.remove('widget-small', 'widget-medium', 'widget-large');
        widget.classList.add(`widget-${size}`);
      });
    }

    function applyThemeColors(theme) {
      const root = document.documentElement;
      
      if (theme === 'light') {
        root.style.setProperty('--bg-primary', '#ffffff');
        root.style.setProperty('--bg-secondary', '#f8fafc');
        root.style.setProperty('--text-primary', '#1f2937');
        root.style.setProperty('--text-secondary', '#6b7280');
        root.style.setProperty('--border-color', '#e5e7eb');
      } else {
        root.style.setProperty('--bg-primary', '#0f172a');
        root.style.setProperty('--bg-secondary', '#1e293b');
        root.style.setProperty('--text-primary', '#f1f5f9');
        root.style.setProperty('--text-secondary', '#cbd5e1');
        root.style.setProperty('--border-color', '#334155');
      }
    }

    // Enhanced Test Notification Function
    function testNotification() {
      const titles = ['Test-Benachrichtigung', 'Neue Nachricht', 'Erinnerung', 'Info'];
      const messages = [
        'Dies ist eine Test-Benachrichtigung von PrivateVault.',
        'Sie haben eine neue Nachricht erhalten.',
        'Vergessen Sie nicht Ihre wichtigen Aufgaben!',
        'Das System funktioniert einwandfrei.'
      ];
      const types = ['info', 'success', 'warning', 'error'];
      
      const randomTitle = titles[Math.floor(Math.random() * titles.length)];
      const randomMessage = messages[Math.floor(Math.random() * messages.length)];
      const randomType = types[Math.floor(Math.random() * types.length)];
      
      sendNotification(randomTitle, randomMessage, randomType);
    }

    // Send notification function
    async function sendNotification(title, message, type = 'info', data = null) {
      try {
        // Send to server first
        const response = await fetch('./api/notifications.php', {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            title: title,
            message: message,
            type: type,
            data: data
          })
        });
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
          // Show desktop notification if enabled and permitted
          const desktopEnabled = localStorage.getItem('desktop_enabled') !== 'false';
          
          if (desktopEnabled && 'Notification' in window) {
            if (Notification.permission === 'granted') {
              const notification = new Notification(title, {
                body: message,
                icon: '/favicon.ico',
                badge: '/favicon.ico',
                tag: 'privatevault-' + Date.now(),
                requireInteraction: type === 'error' || type === 'warning',
                silent: localStorage.getItem('sound_enabled') === 'false'
              });
              
              // Auto-close after 5 seconds for non-critical notifications
              if (type === 'info' || type === 'success') {
                setTimeout(() => {
                  notification.close();
                }, 5000);
              }
              
              // Handle notification click
              notification.onclick = function() {
                window.focus();
                this.close();
                
                // Handle specific notification actions based on type
                if (data && data.action) {
                  handleNotificationAction(data.action, data);
                }
              };
            } else if (Notification.permission === 'default') {
              // Request permission
              const permission = await Notification.requestPermission();
              if (permission === 'granted') {
                // Retry sending notification
                sendNotification(title, message, type, data);
              }
            }
          }
          
          // Show in-app notification
          showInAppNotification(title, message, type);
          
          // Play sound if enabled
          if (localStorage.getItem('sound_enabled') !== 'false') {
            playNotificationSound(type);
          }
          
          return true;
        } else {
          console.error('Failed to send notification:', result.error);
          return false;
        }
      } catch (error) {
        console.error('Error sending notification:', error);
        showInAppNotification('Fehler beim Senden der Benachrichtigung', error.message, 'error');
        return false;
      }
    }

    // Show in-app notification
    function showInAppNotification(title, message, type = 'info') {
      // Remove existing notifications
      const existing = document.querySelectorAll('.in-app-notification');
      existing.forEach(n => n.remove());
      
      const notification = document.createElement('div');
      notification.className = `in-app-notification fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg border backdrop-blur-sm max-w-sm transform transition-all duration-300 translate-x-full`;
      
      // Set colors based on type
      let bgColor, borderColor, iconClass;
      switch (type) {
        case 'success':
          bgColor = 'bg-green-900/90';
          borderColor = 'border-green-500/30';
          iconClass = 'fas fa-check-circle text-green-400';
          break;
        case 'warning':
          bgColor = 'bg-yellow-900/90';
          borderColor = 'border-yellow-500/30';
          iconClass = 'fas fa-exclamation-triangle text-yellow-400';
          break;
        case 'error':
          bgColor = 'bg-red-900/90';
          borderColor = 'border-red-500/30';
          iconClass = 'fas fa-times-circle text-red-400';
          break;
        default:
          bgColor = 'bg-blue-900/90';
          borderColor = 'border-blue-500/30';
          iconClass = 'fas fa-info-circle text-blue-400';
      }
      
      notification.className += ` ${bgColor} ${borderColor}`;
      
      notification.innerHTML = `
        <div class="flex items-start gap-3">
          <i class="${iconClass} mt-0.5"></i>
          <div class="flex-1">
            <h4 class="font-medium text-white text-sm">${title}</h4>
            <p class="text-gray-300 text-xs mt-1">${message}</p>
          </div>
          <button class="text-gray-400 hover:text-white transition-colors" onclick="this.closest('.in-app-notification').remove()">
            <i class="fas fa-times text-xs"></i>
          </button>
        </div>
      `;
      
      document.body.appendChild(notification);
      
      // Animate in
      setTimeout(() => {
        notification.classList.remove('translate-x-full');
      }, 100);
      
      // Auto-remove after 5 seconds
      setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
          notification.remove();
        }, 300);
      }, 5000);
    }

    // Play notification sound
    function playNotificationSound(type = 'info') {
      try {
        // Create audio context if needed
        if (!window.audioContext) {
          window.audioContext = new (window.AudioContext || window.webkitAudioContext)();
        }
        
        // Different frequencies for different notification types
        let frequency;
        switch (type) {
          case 'success':
            frequency = 800;
            break;
          case 'warning':
            frequency = 600;
            break;
          case 'error':
            frequency = 400;
            break;
          default:
            frequency = 700;
        }
        
        const oscillator = window.audioContext.createOscillator();
        const gainNode = window.audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(window.audioContext.destination);
        
        oscillator.frequency.setValueAtTime(frequency, window.audioContext.currentTime);
        oscillator.type = 'sine';
        
        gainNode.gain.setValueAtTime(0.1, window.audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, window.audioContext.currentTime + 0.3);
        
        oscillator.start(window.audioContext.currentTime);
        oscillator.stop(window.audioContext.currentTime + 0.3);
      } catch (error) {
        console.warn('Could not play notification sound:', error);
      }
    }

    // Handle notification actions
    function handleNotificationAction(action, data) {
      switch (action) {
        case 'open_task':
          if (data.taskId) {
            window.location.href = `/taskboard.php?task=${data.taskId}`;
          }
          break;
        case 'open_calendar':
          if (data.eventId) {
            window.location.href = `/calendar.php?event=${data.eventId}`;
          }
          break;
        case 'open_note':
          if (data.noteId) {
            // Open notes app with specific note
            openNotesApp();
            // Load specific note (implementation depends on notes app)
          }
          break;
        case 'open_finance':
          window.location.href = '/havetopay.php';
          break;
        default:
          console.log('Unknown notification action:', action);
      }
    }

    // Check for notifications periodically
    function startNotificationPolling() {
      // Check every 30 seconds for new notifications
      setInterval(async () => {
        await checkForNewNotifications();
      }, 30000);
    }

    // Check for new notifications
    async function checkForNewNotifications() {
      try {
        const lastCheck = localStorage.getItem('lastNotificationCheck') || '';
        const response = await fetch(`./api/notifications.php?since=${encodeURIComponent(lastCheck)}`, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json'
          }
        });
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success && data.notifications && data.notifications.length > 0) {
          data.notifications.forEach(notification => {
            sendNotification(
              notification.title,
              notification.message,
              notification.type,
              notification.data ? JSON.parse(notification.data) : null
            );
          });
          
          // Update last check time
          localStorage.setItem('lastNotificationCheck', new Date().toISOString());
        }
      } catch (error) {
        console.error('Error checking for notifications:', error);
      }
    }

    // Initialize notification system
    function initializeNotifications() {
      // Request notification permission on first load
      if ('Notification' in window && Notification.permission === 'default') {
        // Don't request immediately, wait for user interaction
        document.addEventListener('click', function requestPermissionOnce() {
          if (localStorage.getItem('desktop_enabled') !== 'false') {
            Notification.requestPermission();
          }
          document.removeEventListener('click', requestPermissionOnce);
        }, { once: true });
      }
      
      // Start polling for notifications
      startNotificationPolling();
      
      // Check immediately
      checkForNewNotifications();
      
      // Update notification badge
      updateNotificationBadge();
    }

    // Update notification badge
    async function updateNotificationBadge() {
      try {
        const response = await fetch('./api/notifications.php?action=unread_count', {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json'
          }
        });
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success && data.count !== undefined) {
          const badge = document.getElementById('notificationBadge');
          if (badge) {
            if (data.count > 0) {
              badge.textContent = data.count > 99 ? '99+' : data.count;
              badge.style.display = 'flex';
            } else {
              badge.style.display = 'none';
            }
          }
        }
      } catch (error) {
        console.error('Error updating notification badge:', error);
      }
    }

    // Show notification to user immediately
    function showNotification(title, message, type = 'info') {
      showInAppNotification(title, message, type);
      updateNotificationBadge(); // Update badge when new notification is shown
    }

    // Data Management Functions
    function exportData() {
      const data = {
        settings: {},
        timestamp: new Date().toISOString()
      };
      
      // Export all localStorage data
      for (let i = 0; i < localStorage.length; i++) {
        const key = localStorage.key(i);
        data.settings[key] = localStorage.getItem(key);
      }
      
      const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `privatevault-settings-${new Date().toISOString().split('T')[0]}.json`;
      a.click();
      URL.revokeObjectURL(url);
    }

    function clearCache() {
      if (confirm('Möchten Sie wirklich den Cache leeren? Dies kann die Ladezeit temporär erhöhen.')) {
        // Clear localStorage
        localStorage.clear();
        
        // Clear sessionStorage
        sessionStorage.clear();
        
        // Clear browser cache if possible
        if ('caches' in window) {
          caches.keys().then(names => {
            names.forEach(name => {
              caches.delete(name);
            });
          });
        }
        
        alert('Cache erfolgreich geleert.');
        location.reload();
      }
    }

    function resetSettings() {
      if (confirm('Möchten Sie wirklich alle Einstellungen zurücksetzen? Diese Aktion kann nicht rückgängig gemacht werden.')) {
        localStorage.clear();
        location.reload();
      }
    }

    function togglePerformanceMetrics(show) {
      const metricsContainer = document.getElementById('performance-metrics');
      if (show) {
        if (!metricsContainer) {
          const metrics = document.createElement('div');
          metrics.id = 'performance-metrics';
          metrics.className = 'fixed bottom-4 right-4 bg-black/80 text-white p-4 rounded-lg backdrop-blur-sm z-40';
          metrics.innerHTML = `
            <div class="text-sm font-medium mb-2">Performance</div>
            <div class="text-xs space-y-1">
              <div>Memory: <span id="memory-usage">--</span></div>
              <div>Load Time: <span id="load-time">--</span></div>
              <div>FPS: <span id="fps-counter">--</span></div>
            </div>
          `;
          document.body.appendChild(metrics);
          
          // Start performance monitoring
          startPerformanceMonitoring();
        } else {
          metricsContainer.style.display = 'block';
        }
      } else {
        if (metricsContainer) {
          metricsContainer.style.display = 'none';
        }
      }
    }

    function startPerformanceMonitoring() {
      // Memory usage
      if (performance.memory) {
        setInterval(() => {
          const memoryElement = document.getElementById('memory-usage');
          if (memoryElement) {
            const memory = Math.round(performance.memory.usedJSHeapSize / 1024 / 1024);
            memoryElement.textContent = `${memory} MB`;
          }
        }, 1000);
      }
      
      // Load time
      window.addEventListener('load', () => {
        const loadTimeElement = document.getElementById('load-time');
        if (loadTimeElement) {
          const loadTime = Math.round(performance.now());
          loadTimeElement.textContent = `${loadTime} ms`;
        }
      });
      
      // FPS counter
      let fps = 0;
      let lastTime = performance.now();
      function countFPS() {
        const currentTime = performance.now();
        fps = Math.round(1000 / (currentTime - lastTime));
        lastTime = currentTime;
        
        const fpsElement = document.getElementById('fps-counter');
        if (fpsElement) {
          fpsElement.textContent = fps;
        }
        
        requestAnimationFrame(countFPS);
      }
      requestAnimationFrame(countFPS);
    }

    // Enhanced Dynamic Layout System
    function applyDynamicLayout() {
      const dashboard = document.querySelector('.dashboard-grid');
      if (!dashboard) return;
      
      const screenWidth = window.innerWidth;
      const isDesktop = screenWidth >= 1024;
      const isTablet = screenWidth >= 768 && screenWidth < 1024;
      const isMobile = screenWidth < 768;
      
      // Check if dynamic layout is enabled
      const dynamicLayout = localStorage.getItem('dynamicLayout') !== 'false';
      
      let columns;
      let gap;
      
      if (isMobile) {
        columns = 1;
        gap = '1rem';
      } else if (isTablet) {
        columns = parseInt(localStorage.getItem('tabletColumns') || '2');
        gap = '1.5rem';
      } else {
        // Desktop: use user setting or calculate dynamic columns
        if (dynamicLayout) {
          const availableWidth = screenWidth - 256 - 48; // Subtract sidebar and padding
          const widgetSize = localStorage.getItem('widgetSize') || 'medium';
          
          let minWidgetWidth;
          switch (widgetSize) {
            case 'small':
              minWidgetWidth = 280;
              break;
            case 'large':
              minWidgetWidth = 400;
              break;
            default: // medium
              minWidgetWidth = 320;
          }
          
          const maxColumns = Math.floor(availableWidth / minWidgetWidth);
          columns = Math.min(Math.max(maxColumns, 2), 6); // Between 2 and 6 columns
        } else {
          columns = parseInt(localStorage.getItem('desktopColumns') || '4');
        }
        gap = '2rem';
      }
      
      // Apply CSS Grid
      dashboard.style.display = 'grid';
      dashboard.style.gridTemplateColumns = `repeat(${columns}, 1fr)`;
      dashboard.style.gap = gap;
      dashboard.style.transition = 'all 0.3s ease';
      
      // Store current layout
      localStorage.setItem('currentColumns', columns);
      
      // Apply widget sizing classes
      const widgetSize = localStorage.getItem('widgetSize') || 'medium';
      applyWidgetSizing(widgetSize);
    }

    // Enhanced Layout Functions
    function applyLayoutSettings() {
      const showGreeting = localStorage.getItem('showGreeting') !== 'false';
      const showStats = localStorage.getItem('showStats') !== 'false';
      const animateWidgets = localStorage.getItem('animateWidgets') !== 'false';
      
      // Apply greeting visibility
      const greetingElement = document.querySelector('.greeting-text');
      if (greetingElement) {
        greetingElement.style.display = showGreeting ? 'block' : 'none';
      }
      
      // Apply stats visibility
      const statsElements = document.querySelectorAll('.stats-number');
      statsElements.forEach(el => {
        el.style.display = showStats ? 'block' : 'none';
      });
      
      // Apply animations
      const widgets = document.querySelectorAll('.dashboard-short');
      widgets.forEach(widget => {
        if (animateWidgets) {
          widget.style.transition = 'all 0.3s ease';
        } else {
          widget.style.transition = 'none';
        }
      });
    }

    // Initialize all dashboard functionality
    function initializeDashboard() {
      setupDashboardWidgets();
      applyLayoutSettings();
      updateControlBarState();
      setupKeyboardShortcuts();
      initializeNotifications(); // Add notification system
      
      // Apply theme on load
      const theme = localStorage.getItem('theme') || 'dark';
      applyThemeColors(theme);
      
      // Apply compact mode if enabled
      const compactMode = localStorage.getItem('compactMode') === 'true';
      if (compactMode) {
        toggleCompactMode();
      }
    }

    // Call initialization when DOM is ready
    document.addEventListener('DOMContentLoaded', initializeDashboard);

    // Handle window resize
    window.addEventListener('resize', () => {
      applyDynamicLayout();
    });

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
      // Initialize settings first
      initializeSettings();
      
      // Apply saved gradient
      if (gradients[currentGradient]) {
        document.body.style.background = gradients[currentGradient];
      }
      
      // Update control bar state
      updateControlBarState();
      
      // Setup keyboard shortcuts
      setupKeyboardShortcuts();
      
      // Initialize tooltips
      initializeTooltips();
      
      // Setup dashboard widgets
      setupDashboardWidgets();
      
      // Setup greeting
      setupGreeting();
      
      // Setup autosave
      setupAutosave();
      
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
