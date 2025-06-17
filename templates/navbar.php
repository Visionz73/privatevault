<?php
// Adjusted Navbar with proper paths for all files
require_once __DIR__ . '/../src/lib/auth.php';
require_once __DIR__ . '/../src/lib/utils.php';
$user = getUser();

// Only groups.php is in the admin directory
$isAdminPage = strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false;

// Determine if we're on the havetopay page to add specific styling
$isHaveToPayPage = basename($_SERVER['PHP_SELF']) === 'havetopay.php' || 
                   basename($_SERVER['PHP_SELF']) === 'havetopay_add.php' ||
                   basename($_SERVER['PHP_SELF']) === 'havetopay_detail.php';
?>

<style>
  /* Modern dark gradient navbar styling */
  @media (min-width: 769px) {
    nav#sidebar {
      position: fixed;
      left: 0;
      top: 0;
      bottom: 0;
      width: 16rem; /* w-64 */
      background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
      border-right: 1px solid rgba(255,255,255,0.1);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      z-index: 50;
    }
    .mobile-menu { display: none; }
    .sidebar-content { display: block; }
  }

  /* Mobile dark gradient styling */
  @media (max-width: 768px) {
    nav#sidebar {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 4rem;
      background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
      border-bottom: 1px solid rgba(255,255,255,0.1);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
      z-index: 50;
      transform: none; /* Remove transform for header */
    }
    
    .mobile-menu { 
      display: flex; 
      align-items: center;
      justify-content: space-between;
      width: 100%;
      height: 100%;
      padding: 0 1rem;
    }
    
    .sidebar-content {
      display: none;
      position: fixed;
      top: 4rem;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
      backdrop-filter: saturate(180%) blur(20px);
      z-index: 49;
      padding: 1rem;
      overflow-y: auto;
      transform: translateX(-100%);
      transition: transform 0.3s ease;
    }
    
    .sidebar-content.active {
      display: block;
      transform: translateX(0);
    }
    
    /* Mobile overlay */
    .mobile-overlay {
      display: none;
      position: fixed;
      top: 4rem;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5);
      z-index: 48;
    }
    
    .mobile-overlay.active {
      display: block;
    }
  }

  /* Logo styling for dark background */
  .logo-container {
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    padding: 0.5rem 1rem;
  }

  .logo-image {
    max-height: 2.5rem;
    width: auto;
    max-width: 100%;
    object-fit: contain;
    filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.3));
    transition: all 0.3s ease;
  }

  .logo-container:hover .logo-image {
    transform: scale(1.05);
    filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.4));
  }

  /* Mobile logo adjustments */
  @media (max-width: 768px) {
    .logo-image {
      max-height: 2rem;
    }
    
    .mobile-header-content .logo-container {
      padding: 0.25rem;
    }
    
    .mobile-header-content .logo-image {
      max-height: 1.75rem;
    }
  }

  /* Rounded container for navigation items */
  .nav-container {
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 1rem;
    padding: 1rem;
    margin: 1rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
  }

  /* Modern navigation links */
  .nav-link-modern {
    color: rgba(255, 255, 255, 0.9) !important;
    padding: 0.75rem 1rem !important;
    border-radius: 0.5rem !important;
    transition: all 0.3s ease !important;
    margin-bottom: 0.25rem;
    text-decoration: none;
    display: flex;
    align-items: center;
  }
  .nav-link-modern:hover {
    background: rgba(255, 255, 255, 0.15) !important;
    color: white !important;
    transform: translateX(3px);
  }
  .nav-link-modern.active {
    background: rgba(255, 255, 255, 0.2) !important;
    color: white !important;
    font-weight: 600;
  }

  .nav-link-modern svg {
    margin-right: 0.75rem;
    width: 1.25rem;
    height: 1.25rem;
    color: rgba(255, 255, 255, 0.7);
  }

  /* User Banner Styles for dark theme */
  .user-banner {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding: 1rem;
    margin: 0 1rem 1rem;
  }
  .user-banner button {
    width: 100%;
    display: flex;
    align-items: center;
    padding: 1rem;
    border-radius: 1rem;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    cursor: pointer;
  }
  .user-banner button:hover {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.08) 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    border-color: rgba(255, 255, 255, 0.3);
  }
  .user-banner .user-info {
    flex: 1;
    text-align: left;
    margin-left: 0.75rem;
    margin-right: 0.75rem;
  }
  .user-banner .user-info .user-name {
    color: white;
    font-weight: 600;
    font-size: 0.95rem;
    margin-bottom: 0.125rem;
  }
  .user-banner .user-info .user-role {
    color: rgba(255, 255, 255, 0.75);
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .user-banner svg {
    color: rgba(255, 255, 255, 0.6);
    transition: all 0.3s ease;
  }
  .user-banner button:hover svg {
    color: rgba(255, 255, 255, 0.9);
    transform: rotate(180deg);
  }

  /* Profile avatar styling for dark theme */
  .profile-avatar {
    width: 2.5rem;
    height: 2.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1rem;
    border: 2px solid rgba(255,255,255,0.3);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
  }
  .user-banner button:hover .profile-avatar {
    transform: scale(1.05);
    border-color: rgba(255,255,255,0.5);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
  }

  /* Mobile header styling */
  .mobile-header-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 0 1rem;
    height: 100%;
  }

  .mobile-toggle-btn {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    border-radius: 0.5rem;
    padding: 0.5rem;
    color: white;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 2.5rem;
    min-width: 2.5rem;
  }
  .mobile-toggle-btn:hover {
    background: rgba(255, 255, 255, 0.2);
  }

  /* Fix mobile user avatar */
  .mobile-user-avatar {
    width: 2rem;
    height: 2rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 0.75rem;
    border: 2px solid rgba(255,255,255,0.3);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
  }

  /* Profile Modal dark theme adjustments */
  .profile-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(5px);
    z-index: 9999 !important;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
  }
  .profile-modal.active {
    opacity: 1;
    visibility: visible;
  }
  .profile-modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.9);
    background: linear-gradient(135deg, #2d1b69 0%, #11101d 100%);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 1rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    max-width: 400px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
    transition: all 0.3s ease;
    z-index: 10000;
  }
  .profile-modal.active .profile-modal-content {
    transform: translate(-50%, -50%) scale(1);
  }
  .profile-modal-header {
    padding: 1.5rem 1.5rem 1rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .profile-modal-body {
    padding: 1.5rem;
  }
  .close-modal {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #6b7280;
    cursor: pointer;
    transition: color 0.2s ease;
  }
  .close-modal:hover {
    color: #374151;
  }
  .modal-menu-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    margin-bottom: 0.5rem;
    border-radius: 0.5rem;
    text-decoration: none;
    color: #374151;
    transition: all 0.2s ease;
    border: 1px solid transparent;
  }
  .modal-menu-item:hover {
    background: #f3f4f6;
    color: #667eea;
    border-color: #e5e7eb;
  }
  .modal-menu-item svg {
    margin-right: 0.75rem;
    width: 1.25rem;
    height: 1.25rem;
  }

  /* User Banner Styles */
  .user-banner {
    @apply border-t border-gray-200 p-4;
  }
  .user-banner button {
    @apply w-full flex items-center p-3 rounded-lg bg-gradient-to-r from-blue-50 to-purple-50 hover:from-blue-100 hover:to-purple-100 transition-all duration-200;
  }
  .user-banner .user-info {
    @apply flex-1 text-left;
  }
  .user-banner .user-info .user-name {
    @apply font-medium text-gray-900;
  }
  .user-banner .user-info .user-role {
    @apply text-xs text-gray-500 capitalize;
  }
  .user-banner svg {
    @apply h-4 w-4 text-gray-400;
  }

  /* Sidebar styles */
  #sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 16rem;
    height: 100vh;
    background: white;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    z-index: 40;
    transition: transform 0.3s ease;
  }
  
  @media (max-width: 768px) {
    #sidebar {
      transform: translateX(-100%);
    }
    
    #sidebar.active {
      transform: translateX(0);
    }
  }
  
  /* Profile modal styles */
  .profile-modal {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: none;
    z-index: 50;
  }
  
  .profile-modal.active {
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .profile-modal-content {
    background: white;
    border-radius: 0.75rem;
    width: 100%;
    max-width: 20rem;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    margin: 1rem;
  }
  
  .profile-modal-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .profile-modal-body {
    padding: 1rem 1.5rem;
  }
  
  .profile-avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(74, 144, 226, 0.1);
    color: #4A90E2;
    font-weight: 600;
    border-radius: 9999px;
  }
  
  .modal-menu-item {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    border-radius: 0.5rem;
    color: #4b5563;
    transition: all 0.2s;
  }
  
  .modal-menu-item:hover {
    background-color: #f3f4f6;
  }
  
  .modal-menu-item svg {
    width: 1.25rem;
    height: 1.25rem;
    margin-right: 0.75rem;
  }
  
  .close-modal {
    font-size: 1.5rem;
    line-height: 1;
    color: #9ca3af;
    cursor: pointer;
  }
  
  .close-modal:hover {
    color: #6b7280;
  }

  /* Shortcuts Container */
  .shortcuts-container {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding: 1rem;
    margin: 0 1rem 1rem;
  }
  
  .shortcuts-title {
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  
  .shortcuts-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.5rem;
  }
  
  .shortcut-item {
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 0.75rem;
    padding: 0.75rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    color: rgba(255, 255, 255, 0.9);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    position: relative;
    overflow: hidden;
    min-height: 4rem;
    justify-content: center;
  }
  
  .shortcut-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    transition: left 0.5s ease;
  }
  
  .shortcut-item:hover::before {
    left: 100%;
  }
  
  .shortcut-item:hover {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.25);
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    color: white;
  }
  
  .shortcut-item.predefined.create-task:hover {
    background: rgba(34, 197, 94, 0.2);
    border-color: rgba(34, 197, 94, 0.4);
    color: #86efac;
  }
  
  .shortcut-item.predefined.logout:hover {
    background: rgba(239, 68, 68, 0.2);
    border-color: rgba(239, 68, 68, 0.4);
    color: #fca5a5;
  }
  
  .shortcut-item.empty {
    border-style: dashed;
    border-color: rgba(255, 255, 255, 0.3);
    color: rgba(255, 255, 255, 0.6);
  }
  
  .shortcut-item.empty:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.4);
    color: rgba(255, 255, 255, 0.8);
  }
  
  .shortcut-icon {
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
  }
  
  .shortcut-item:hover .shortcut-icon {
    transform: scale(1.1);
  }
  
  .shortcut-label {
    font-size: 0.625rem;
    text-align: center;
    font-weight: 500;
    line-height: 1.2;
    opacity: 0.9;
  }
  
  .shortcut-item:hover .shortcut-label {
    opacity: 1;
  }

  /* Custom Shortcut Modal */
  .shortcuts-modal {
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
  }
  
  .shortcuts-modal.active {
    display: flex;
  }
  
  .shortcuts-modal-content {
    background: linear-gradient(135deg, #2d1b69 0%, #11101d 100%);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 1rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    max-width: 400px;
    width: 100%;
    max-height: 80vh;
    overflow-y: auto;
    color: white;
  }
  
  .shortcuts-modal-header {
    padding: 1.5rem 1.5rem 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .shortcuts-modal-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
  }
  
  .shortcuts-modal-close {
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
  }
  
  .shortcuts-modal-close:hover {
    color: white;
  }
  
  .shortcuts-modal-body {
    padding: 1.5rem;
  }
  
  .form-group {
    margin-bottom: 1rem;
  }
  
  .form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.9);
  }
  
  .form-group input,
  .form-group select {
    width: 100%;
    padding: 0.75rem;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 0.5rem;
    color: white;
    font-size: 0.875rem;
    transition: all 0.3s ease;
  }
  
  .form-group input:focus,
  .form-group select:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.3);
    outline: none;
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
  }
  
  .form-group input::placeholder {
    color: rgba(255, 255, 255, 0.5);
  }
  
  .form-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1.5rem;
  }
  
  .btn-cancel,
  .btn-delete,
  .btn-save {
    padding: 0.75rem 1rem;
    border: none;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    flex: 1;
  }
  
  .btn-cancel {
    background: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.2);
  }
  
  .btn-cancel:hover {
    background: rgba(255, 255, 255, 0.15);
    color: white;
  }
  
  .btn-delete {
    background: rgba(239, 68, 68, 0.2);
    color: #fca5a5;
    border: 1px solid rgba(239, 68, 68, 0.3);
  }
  
  .btn-delete:hover {
    background: rgba(239, 68, 68, 0.3);
    color: white;
  }
  
  .btn-save {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.8) 0%, rgba(34, 197, 94, 0.6) 100%);
    color: white;
    border: 1px solid rgba(34, 197, 94, 0.3);
  }
  
  .btn-save:hover {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.9) 0%, rgba(34, 197, 94, 0.7) 100%);
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(34, 197, 94, 0.3);
  }

  /* Mobile optimizations for shortcuts */
  @media (max-width: 768px) {
    .shortcuts-container {
      padding: 0.75rem;
      margin: 0 0.75rem 0.75rem;
    }
    
    .shortcuts-grid {
      grid-template-columns: repeat(2, 1fr);
      gap: 0.375rem;
    }
    
    .shortcut-item {
      padding: 0.5rem;
      min-height: 3.5rem;
    }
    
    .shortcut-label {
      font-size: 0.5rem;
    }
    
    .shortcuts-modal-content {
      margin: 0.5rem;
      max-width: none;
    }
    
    .shortcuts-modal-header,
    .shortcuts-modal-body {
      padding: 1rem;
    }
  }

  /* Stats Container */
  .stats-container {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding: 1rem;
    margin: 0 1rem 1rem;
  }
  
  .stats-title {
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.5rem;
  }
  
  .stat-item {
    background: rgba(255, 255, 255, 0.06);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 0.75rem;
    padding: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    cursor: pointer;
  }
  
  .stat-item:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
    transform: translateY(-1px);
  }
  
  .stat-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 0.5rem;
    color: rgba(255, 255, 255, 0.7);
    flex-shrink: 0;
  }
  
  .stat-content {
    display: flex;
    flex-direction: column;
    min-width: 0;
    flex: 1;
  }
  
  .stat-value {
    font-size: 0.75rem;
    font-weight: 600;
    color: white;
    line-height: 1;
  }
  
  .stat-label {
    font-size: 0.625rem;
    color: rgba(255, 255, 255, 0.6);
    line-height: 1;
    margin-top: 0.125rem;
  }
  
  .stat-item.cpu:hover .stat-icon {
    background: rgba(59, 130, 246, 0.3);
    color: #93c5fd;
  }
  
  .stat-item.memory:hover .stat-icon {
    background: rgba(34, 197, 94, 0.3);
    color: #86efac;
  }
  
  .stat-item.storage:hover .stat-icon {
    background: rgba(251, 191, 36, 0.3);
    color: #fde047;
  }
  
  .stat-item.network:hover .stat-icon {
    background: rgba(147, 51, 234, 0.3);
    color: #c4b5fd;
  }

  /* Enhanced shortcut modal styling */
  .form-help {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.5);
    margin-top: 0.25rem;
    display: block;
  }

  /* Mobile optimizations for stats */
  @media (max-width: 768px) {
    .stats-container {
      padding: 0.75rem;
      margin: 0 0.75rem 0.75rem;
    }
    
    .stats-grid {
      grid-template-columns: repeat(2, 1fr);
      gap: 0.375rem;
    }
    
    .stat-item {
      padding: 0.5rem;
    }
    
    .stat-icon {
      width: 1.5rem;
      height: 1.5rem;
    }
    
    .stat-value {
      font-size: 0.625rem;
    }
    
    .stat-label {
      font-size: 0.5rem;
    }
  }

  /* Custom scrollbar for webkit browsers */
  ::-webkit-scrollbar {
    width: 0.5rem;
    height: 0.5rem;
  }
  
  ::-webkit-scrollbar-thumb {
    background-color: rgba(255, 255, 255, 0.3);
    border-radius: 0.25rem;
  }
  
  ::-webkit-scrollbar-thumb:hover {
    background-color: rgba(255, 255, 255, 0.5);
  }
  
  ::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 0.25rem;
  }
</style>

<!-- Add the haveToPay-layout class to body if on HaveToPay page -->
<script>
  if (<?php echo $isHaveToPayPage ? 'true' : 'false'; ?>) {
    document.body.classList.add('haveToPay-layout');
  }
</script>

<nav id="sidebar">
  <div class="sidebar-content flex flex-col h-full">
    <!-- Navigation Links Container with Logo -->
    <div class="flex-1">
      <div class="nav-container">
        <!-- Logo/Header inside container -->
        <div class="mb-6 text-center">
          <a href="/" class="logo-container">
            <img src="/assets/logo.png" alt="Private Vault" class="logo-image" style="max-height: 10rem;">
          </a>
        </div>
        
        <ul class="space-y-1">
          <li>
            <a href="/dashboard.php" class="nav-link-modern">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
              <span>Dashboard</span>
            </a>
          </li>
          <li>
            <a href="/inbox.php" class="nav-link-modern">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
              </svg>
              <span>Inbox</span>
            </a>
          </li>
          <li>
            <a href="/calendar.php" class="nav-link-modern">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span>Kalender</span>
            </a>
          </li>
          <li>
            <a href="/havetopay.php" class="nav-link-modern">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V4m0 12v4" />
              </svg>
              <span>HaveToPay</span>
            </a>
          </li>
          <?php if ($user && ($user['role'] ?? '') === 'admin'): ?>
          <li>
            <a href="/admin.php" class="nav-link-modern">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3zm0 2c-2.21 0-4 1.79-4 4v1h8v-1c0-2.21-1.79-4-4-4z" />
              </svg>
              <span>Admin</span>
            </a>
          </li>
          <li>
            <a href="/admin/groups.php" class="nav-link-modern">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-3-3h-2M9 20H4v-2a3 3 0 013-3h2m7-4a4 4 0 10-8 0 4 4 0 008 0z" />
              </svg>
              <span>Gruppen</span>
            </a>
          </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>

    <!-- User Banner at Bottom -->
    <div class="user-banner">
      <button onclick="openProfileModal()">
        <div class="profile-avatar mr-3">
          <?= isset($user) ? strtoupper(substr($user['username'], 0, 2)) : 'GU' ?>
        </div>
        <div class="flex-1 text-left">
          <div class="user-name truncate"><?= isset($user) ? htmlspecialchars($user['username']) : 'Gast' ?></div>
          <div class="user-role"><?= isset($user) ? ucfirst($user['role'] ?? 'user') : 'Nicht angemeldet' ?></div>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </button>
    </div>

    <!-- Shortcuts Section -->
    <div class="shortcuts-container">
      <h3 class="shortcuts-title">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
        Shortcuts
      </h3>
      
      <div class="shortcuts-grid">
        <!-- Create Task Shortcut -->
        <a href="/create_task.php" class="shortcut-item predefined create-task">
          <div class="shortcut-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
          </div>
          <span class="shortcut-label">Task erstellen</span>
        </a>

        <!-- Logout Shortcut -->
        <a href="/logout.php" class="shortcut-item predefined logout" onclick="return confirm('Möchten Sie sich wirklich abmelden?')">
          <div class="shortcut-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
          </div>
          <span class="shortcut-label">Abmelden</span>
        </a>

        <!-- Custom Shortcut Slots -->
        <div class="shortcut-item empty" data-slot="custom1" onclick="openCustomShortcutModal(this)">
          <div class="shortcut-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
          </div>
          <span class="shortcut-label">Add Shortcut</span>
        </div>

        <div class="shortcut-item empty" data-slot="custom2" onclick="openCustomShortcutModal(this)">
          <div class="shortcut-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
          </div>
          <span class="shortcut-label">Add Shortcut</span>
        </div>
      </div>
    </div>

    <!-- System Stats Section -->
    <div class="stats-container">
      <h3 class="stats-title">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
        System
      </h3>
      
      <div class="stats-grid">
        <!-- CPU Usage -->
        <div class="stat-item">
          <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
            </svg>
          </div>
          <div class="stat-content">
            <span class="stat-value" id="cpuUsage">--</span>
            <span class="stat-label">CPU</span>
          </div>
        </div>

        <!-- Memory Usage -->
        <div class="stat-item">
          <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2H5a2 2 0 00-2 2v2M7 7h10" />
            </svg>
          </div>
          <div class="stat-content">
            <span class="stat-value" id="memoryUsage">--</span>
            <span class="stat-label">RAM</span>
          </div>
        </div>

        <!-- Storage -->
        <div class="stat-item">
          <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
            </svg>
          </div>
          <div class="stat-content">
            <span class="stat-value" id="storageUsage">--</span>
            <span class="stat-label">Storage</span>
          </div>
        </div>

        <!-- Network Status -->
        <div class="stat-item">
          <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
            </svg>
          </div>
          <div class="stat-content">
            <span class="stat-value" id="networkStatus">Online</span>
            <span class="stat-label">Netzwerk</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</nav>

<!-- Mobile header -->
<div class="md:hidden fixed top-0 left-0 right-0 h-16 z-30" style="background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);">
  <div class="mobile-header-content">
    <button id="mobileToggleBtn" class="mobile-toggle-btn">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>
    <a href="/" class="logo-container">
      <img src="/assets/logo.png" alt="Private Vault" class="logo-image">
    </a>
    <button onclick="openProfileModal()" class="mobile-toggle-btn">
      <div class="mobile-user-avatar">
        <?= isset($user) ? strtoupper(substr($user['username'], 0, 2)) : 'GU' ?>
      </div>
    </button>
  </div>
</div>

<!-- Mobile overlay -->
<div id="mobileOverlay" class="mobile-overlay md:hidden"></div>

<!-- Profile Modal -->
<?php if ($user): ?>
<div id="profileModal" class="profile-modal">
  <div class="profile-modal-content">
    <div class="profile-modal-header">
      <div class="flex items-center">
        <div class="profile-avatar mr-3" style="width: 3rem; height: 3rem;">
          <?= getUserInitials($user) ?>
        </div>
        <div>
          <h3 class="font-semibold text-gray-900"><?= htmlspecialchars($user['username']) ?></h3>
          <p class="text-sm text-gray-500"><?= ucfirst($user['role']) ?></p>
        </div>
      </div>
      <button class="close-modal" onclick="closeProfileModal()">&times;</button>
    </div>
    <div class="profile-modal-body">
      <nav class="space-y-1">
        <a href="/profile.php" class="modal-menu-item">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
          </svg>
          Profil bearbeiten
        </a>
        <a href="/settings.php" class="modal-menu-item">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
          Einstellungen
        </a>
        <a href="/profile.php?tab=notifications" class="modal-menu-item">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 17H4l5 5v-5zM9 7v10m6-10v10"></path>
          </svg>
          Benachrichtigungen
        </a>
        <a href="/profile.php?tab=security" class="modal-menu-item">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
          </svg>
          Sicherheit
        </a>
        <hr class="my-3">
        <a href="/logout.php" class="modal-menu-item text-red-600 hover:text-red-700 hover:bg-red-50">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
          </svg>
          Abmelden
        </a>
      </nav>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Custom Shortcut Modal -->
<div id="customShortcutModal" class="shortcuts-modal">
  <div class="shortcuts-modal-content">
    <div class="shortcuts-modal-header">
      <h3 class="shortcuts-modal-title">Custom Shortcut erstellen</h3>
      <button class="shortcuts-modal-close" onclick="closeCustomShortcutModal()">&times;</button>
    </div>
    <div class="shortcuts-modal-body">
      <form id="customShortcutForm">
        <div class="form-group">
          <label for="shortcutName">Name</label>
          <input type="text" id="shortcutName" name="name" required placeholder="z.B. Meine Notizen, Quick Access">
        </div>
        <div class="form-group">
          <label for="shortcutUrl">URL oder Seite</label>
          <input type="text" id="shortcutUrl" name="url" required placeholder="/meine-seite.php oder https://example.com">
          <small class="form-help">Interne Seiten: /seite.php | Externe: https://example.com</small>
        </div>
        <div class="form-group">
          <label for="shortcutIcon">Icon auswählen</label>
          <select id="shortcutIcon" name="icon">
            <option value="link">🔗 Link</option>
            <option value="note">📝 Notizen</option>
            <option value="calendar">📅 Kalender</option>
            <option value="mail">📧 E-Mail</option>
            <option value="folder">📁 Ordner</option>
            <option value="settings">⚙️ Einstellungen</option>
            <option value="star">⭐ Favorit</option>
            <option value="bookmark">🔖 Lesezeichen</option>
            <option value="dashboard">📊 Dashboard</option>
            <option value="chart">📈 Diagramm</option>
            <option value="tool">🔧 Tool</option>
            <option value="globe">🌐 Website</option>
          </select>
        </div>
        <div class="form-group">
          <label for="shortcutTarget">Link-Verhalten</label>
          <select id="shortcutTarget" name="target">
            <option value="_self">Gleiche Seite</option>
            <option value="_blank">Neuer Tab</option>
          </select>
        </div>
        <div class="form-actions">
          <button type="button" onclick="closeCustomShortcutModal()" class="btn-cancel">Abbrechen</button>
          <button type="button" onclick="deleteCustomShortcut()" class="btn-delete" id="deleteShortcutBtn" style="display: none;">Löschen</button>
          <button type="submit" class="btn-save">Speichern</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Enhanced navigation scripts -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const mobileToggleBtn = document.getElementById('mobileToggleBtn');
    const sidebar = document.querySelector('.sidebar-content');
    const mobileOverlay = document.getElementById('mobileOverlay');
    
    function toggleMobileMenu() {
      if (sidebar && mobileOverlay) {
        const isActive = sidebar.classList.contains('active');
        
        if (isActive) {
          sidebar.classList.remove('active');
          mobileOverlay.classList.remove('active');
          document.body.style.overflow = '';
        } else {
          sidebar.classList.add('active');
          mobileOverlay.classList.add('active');
          document.body.style.overflow = 'hidden';
        }
      }
    }
    
    function closeMobileMenu() {
      if (sidebar && mobileOverlay) {
        sidebar.classList.remove('active');
        mobileOverlay.classList.remove('active');
        document.body.style.overflow = '';
      }
    }
    
    if (mobileToggleBtn) {
      mobileToggleBtn.addEventListener('click', toggleMobileMenu);
    }
    
    if (mobileOverlay) {
      mobileOverlay.addEventListener('click', closeMobileMenu);
    }
    
    // Close menu when clicking on nav links
    const navLinks = document.querySelectorAll('.sidebar-content .nav-link-modern');
    navLinks.forEach(link => {
      link.addEventListener('click', closeMobileMenu);
    });
  });

  function openProfileModal() {
    const modal = document.getElementById('profileModal');
    if (modal) {
      modal.classList.add('active');
    }
  }

  function closeProfileModal() {
    const modal = document.getElementById('profileModal');
    if (modal) {
      modal.classList.remove('active');
    }
  }

  // Close modal when clicking outside
  document.addEventListener('click', function(e) {
    const modal = document.getElementById('profileModal');
    if (modal && modal.classList.contains('active') && !modal.contains(e.target) && !e.target.closest('button[onclick="openProfileModal()"]')) {
      closeProfileModal();
    }
  });

  let currentSlot = null;
  let customShortcuts = JSON.parse(localStorage.getItem('customShortcuts') || '{}');
  
  // Load custom shortcuts on page load
  document.addEventListener('DOMContentLoaded', function() {
    loadCustomShortcuts();
    
    // System stats monitoring
    function updateSystemStats() {
      // Simulate system stats (in a real application, these would come from server-side monitoring)
      const cpuUsage = Math.floor(Math.random() * 30 + 10) + '%';
      const memoryUsage = Math.floor(Math.random() * 40 + 30) + '%';
      const storageUsage = Math.floor(Math.random() * 20 + 60) + '%';
      const networkStatus = navigator.onLine ? 'Online' : 'Offline';
      
      document.getElementById('cpuUsage').textContent = cpuUsage;
      document.getElementById('memoryUsage').textContent = memoryUsage;
      document.getElementById('storageUsage').textContent = storageUsage;
      document.getElementById('networkStatus').textContent = networkStatus;
      
      // Update network status color
      const networkStatElement = document.getElementById('networkStatus');
      networkStatElement.style.color = navigator.onLine ? '#86efac' : '#fca5a5';
    }

    // Initialize system monitoring
    document.addEventListener('DOMContentLoaded', function() {
      loadCustomShortcuts();
      updateSystemStats();
      
      // Update stats every 30 seconds
      setInterval(updateSystemStats, 30000);
      
      // Listen for online/offline events
      window.addEventListener('online', updateSystemStats);
      window.addEventListener('offline', updateSystemStats);
    });
  });

  function openCustomShortcutModal(slotElement, existingShortcut = null) {
    const modal = document.getElementById('customShortcutModal');
    const form = document.getElementById('customShortcutForm');
    const deleteBtn = document.getElementById('deleteShortcutBtn');
    const modalTitle = document.querySelector('.shortcuts-modal-title');
    
    currentSlot = slotElement.dataset.slot;
    
    if (existingShortcut) {
      document.getElementById('shortcutName').value = existingShortcut.name;
      document.getElementById('shortcutUrl').value = existingShortcut.url;
      document.getElementById('shortcutIcon').value = existingShortcut.icon;
      document.getElementById('shortcutTarget').value = existingShortcut.target || '_self';
      deleteBtn.style.display = 'block';
      modalTitle.textContent = 'Shortcut bearbeiten';
    } else {
      form.reset();
      document.getElementById('shortcutTarget').value = '_self';
      deleteBtn.style.display = 'none';
      modalTitle.textContent = 'Custom Shortcut erstellen';
    }
    
    modal.classList.add('active');
    
    // Focus on first input
    setTimeout(() => {
      document.getElementById('shortcutName').focus();
    }, 100);
  }

  function closeCustomShortcutModal() {
    const modal = document.getElementById('customShortcutModal');
    modal.classList.remove('active');
    currentSlot = null;
  }
  
  function deleteCustomShortcut() {
    if (currentSlot !== null && customShortcuts[currentSlot]) {
      if (confirm('Sind Sie sicher, dass Sie diesen Shortcut löschen möchten?')) {
        delete customShortcuts[currentSlot];
        localStorage.setItem('customShortcuts', JSON.stringify(customShortcuts));
        
        const slotElement = document.querySelector(`[data-slot="${currentSlot}"]`);
        resetShortcutSlot(slotElement);
        
        closeCustomShortcutModal();
      }
    }
  }
  
  function resetShortcutSlot(slotElement) {
    slotElement.classList.add('empty');
    
    // Remove all event listeners
    slotElement.replaceWith(slotElement.cloneNode(true));
    
    // Get the new element reference
    const newSlotElement = document.querySelector(`[data-slot="${slotElement.dataset.slot}"]`);
    
    newSlotElement.onclick = function() {
      openCustomShortcutModal(newSlotElement);
    };
  }
  
  function updateShortcutSlot(slotElement, shortcut) {
    slotElement.classList.remove('empty');
    slotElement.onclick = null; // Remove the old onclick handler
    
    // Create new click handler that respects target setting
    slotElement.addEventListener('click', function(e) {
      e.preventDefault();
      if (shortcut.target === '_blank') {
        window.open(shortcut.url, '_blank');
      } else {
        window.location.href = shortcut.url;
      }
    });
    
    const iconElement = slotElement.querySelector('.shortcut-icon');
    const labelElement = slotElement.querySelector('.shortcut-label');
    
    iconElement.innerHTML = getIconHtml(shortcut.icon);
    labelElement.textContent = shortcut.name;
    
    // Add edit functionality on right click
    slotElement.addEventListener('contextmenu', function(e) {
      e.preventDefault();
      openCustomShortcutModal(slotElement, shortcut);
    });
    
    // Add double-click for editing (more user-friendly)
    slotElement.addEventListener('dblclick', function(e) {
      e.preventDefault();
      openCustomShortcutModal(slotElement, shortcut);
    });
  }
  
  function loadCustomShortcuts() {
    Object.keys(customShortcuts).forEach(slot => {
      const shortcut = customShortcuts[slot];
      const slotElement = document.querySelector(`[data-slot="${slot}"]`);
      updateShortcutSlot(slotElement, shortcut);
    });
  }
  
  function getIconHtml(iconType) {
    const icons = {
      link: '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>',
      note: '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>',
      calendar: '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>',
      mail: '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>',
      folder: '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>',
      settings: '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>',
      star: '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" /></svg>',
      bookmark: '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>'
    };
    return icons[iconType] || icons.link;
  }

  // Handle form submission with enhanced validation
  document.getElementById('customShortcutForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const name = document.getElementById('shortcutName').value.trim();
    let url = document.getElementById('shortcutUrl').value.trim();
    const icon = document.getElementById('shortcutIcon').value;
    const target = document.getElementById('shortcutTarget').value;
    
    if (name && url && currentSlot !== null) {
      // Enhanced URL validation and formatting
      if (!url.startsWith('http://') && !url.startsWith('https://') && !url.startsWith('/')) {
        // If it looks like a domain, add https://
        if (url.includes('.') && !url.includes(' ')) {
          url = 'https://' + url;
        } else {
          // Otherwise treat as internal path
          url = '/' + url.replace(/^\/+/, '');
        }
      }
      
      const shortcut = { name, url, icon, target };
      customShortcuts[currentSlot] = shortcut;
      localStorage.setItem('customShortcuts', JSON.stringify(customShortcuts));
      
      const slotElement = document.querySelector(`[data-slot="${currentSlot}"]`);
      updateShortcutSlot(slotElement, shortcut);
      
      closeCustomShortcutModal();
      
      // Show success message
      showNotification('Shortcut erfolgreich gespeichert!', 'success');
    }
  });
  
  // Utility function to show notifications
  function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg text-white font-medium transform translate-x-full transition-transform duration-300`;
    
    switch(type) {
      case 'success':
        notification.classList.add('bg-green-500');
        break;
      case 'error':
        notification.classList.add('bg-red-500');
        break;
      default:
        notification.classList.add('bg-blue-500');
    }
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
      notification.classList.remove('translate-x-full');
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
      notification.classList.add('translate-x-full');
      setTimeout(() => {
        document.body.removeChild(notification);
      }, 300);
    }, 3000);
  }
  
  // Close modal on background click
  document.getElementById('customShortcutModal').addEventListener('click', function(e) {
    if (e.target === this) {
      closeCustomShortcutModal();
    }
  });
  
  // Close modal on Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('customShortcutModal').classList.contains('active')) {
      closeCustomShortcutModal();
    }
  });
</script>
