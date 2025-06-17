<?php
/**
 * Modern Navbar with Theme Support for PrivateVault
 */

// Function to check if the current page matches a specific page
function isCurrentPage($pageName) {
    $currentPage = basename($_SERVER['PHP_SELF']);
    return $currentPage == $pageName;
}

// Define the navigation items
$navItems = [
    'index.php' => ['title' => 'Dashboard', 'icon' => 'fas fa-home'],
    'inbox.php' => ['title' => 'Inbox', 'icon' => 'fas fa-inbox'],
    'taskboard.php' => ['title' => 'Taskboard', 'icon' => 'fas fa-tasks'],
    'calendar.php' => ['title' => 'Kalender', 'icon' => 'fas fa-calendar'],
    'havetopay.php' => ['title' => 'HaveToPay', 'icon' => 'fas fa-money-bill-wave'],
];

// Optional: Add admin-only nav items
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
    $navItems['admin.php'] = ['title' => 'Admin', 'icon' => 'fas fa-shield-alt'];
    $navItems['admin/groups.php'] = ['title' => 'Gruppen', 'icon' => 'fas fa-users'];
}
?>

<!-- Mobile Top Navbar -->
<nav class="mobile-navbar">
    <div class="navbar-brand">
        <div class="brand-icon">
            <i class="fas fa-vault"></i>
        </div>
        <span>PrivateVault</span>
    </div>
    
    <button class="mobile-menu-toggle" onclick="toggleMobileSidebar()">
        <i class="fas fa-bars"></i>
    </button>
</nav>

<!-- Sidebar -->
<nav class="modern-sidebar" id="sidebar">
    <!-- Brand -->
    <a href="index.php" class="navbar-brand">
        <div class="brand-icon">
            <i class="fas fa-vault"></i>
        </div>
        <span>PrivateVault</span>
    </a>
    
    <!-- Navigation Menu -->
    <div class="nav-menu">
        <?php foreach ($navItems as $url => $item): ?>
            <div class="nav-item">
                <a href="<?php echo $url; ?>" class="nav-link <?php echo isCurrentPage(basename($url)) ? 'active' : ''; ?>">
                    <i class="<?php echo $item['icon']; ?> nav-icon"></i>
                    <span><?php echo $item['title']; ?></span>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- User Menu -->
    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="user-menu">
        <div class="user-dropdown" id="userDropdown">
            <a href="profile.php" class="dropdown-item">
                <i class="fas fa-id-card nav-icon"></i>
                <span>Profil</span>
            </a>
            <a href="settings.php" class="dropdown-item">
                <i class="fas fa-cog nav-icon"></i>
                <span>Einstellungen</span>
            </a>
            <hr style="border: none; border-top: 1px solid var(--navbar-border); margin: 0.5rem;">
            <a href="logout.php" class="dropdown-item">
                <i class="fas fa-sign-out-alt nav-icon"></i>
                <span>Abmelden</span>
            </a>
        </div>
        
        <button class="user-toggle" onclick="toggleUserDropdown()">
            <div class="user-avatar">
                <?php echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 2)); ?>
            </div>
            <div style="flex: 1; text-align: left;">
                <div style="font-weight: 500; font-size: 0.875rem;">
                    <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>
                </div>
                <div style="font-size: 0.75rem; opacity: 0.7;">
                    <?php echo ucfirst($_SESSION['role'] ?? 'member'); ?>
                </div>
            </div>
            <i class="fas fa-chevron-up" style="font-size: 0.75rem; transition: transform 0.3s ease;" id="userChevron"></i>
        </button>
    </div>
    <?php else: ?>
    <div class="user-menu">
        <a href="login.php" class="nav-link">
            <i class="fas fa-sign-in-alt nav-icon"></i>
            <span>Anmelden</span>
        </a>
    </div>
    <?php endif; ?>
</nav>

<!-- Theme Control Bar -->
<div class="theme-control-bar">
    <div class="theme-control-icon" onclick="openThemePicker()" title="Theme ändern">
        <i class="fas fa-palette text-sm"></i>
    </div>
    <div class="theme-control-icon" onclick="toggleNavbarStyle()" title="Navbar-Stil ändern">
        <i class="fas fa-layout text-sm"></i>
    </div>
    <div class="theme-control-icon" onclick="toggleCompactMode()" title="Kompakter Modus">
        <i class="fas fa-compress text-sm"></i>
    </div>
    <div class="theme-control-icon" onclick="resetTheme()" title="Theme zurücksetzen">
        <i class="fas fa-undo text-sm"></i>
    </div>
</div>

<!-- Sidebar Overlay for Mobile -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleMobileSidebar()"></div>

<!-- Theme Picker Modal (will be added to each page) -->
<div id="themePickerModal" class="theme-picker-modal">
    <div class="theme-picker-content">
        <div class="theme-picker-header">
            <h3 class="text-lg font-semibold">Theme & Style auswählen</h3>
            <button class="theme-picker-close" onclick="closeThemePicker()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="theme-picker-body">
            <!-- Background Themes -->
            <div class="theme-section">
                <h4 class="theme-section-title">Hintergrund-Themes</h4>
                <div class="theme-grid" id="themeGrid">
                    <!-- Themes will be populated by JavaScript -->
                </div>
            </div>
            
            <!-- Navbar Styles -->
            <div class="theme-section">
                <h4 class="theme-section-title">Navbar-Stile</h4>
                <div class="navbar-style-grid" id="navbarStyleGrid">
                    <!-- Navbar styles will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global theme management
const ThemeManager = {
    themes: {
        cosmic: {
            name: 'Cosmic (Standard)',
            background: 'linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%)',
            navbar: 'rgba(255, 255, 255, 0.08)'
        },
        ocean: {
            name: 'Ocean Blue',
            background: 'linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3730a3 100%)',
            navbar: 'rgba(255, 255, 255, 0.08)'
        },
        sunset: {
            name: 'Sunset Fire',
            background: 'linear-gradient(135deg, #f59e0b 0%, #dc2626 50%, #7c2d12 100%)',
            navbar: 'rgba(255, 255, 255, 0.08)'
        },
        forest: {
            name: 'Forest Green',
            background: 'linear-gradient(135deg, #064e3b 0%, #047857 50%, #065f46 100%)',
            navbar: 'rgba(255, 255, 255, 0.08)'
        },
        purple: {
            name: 'Royal Purple',
            background: 'linear-gradient(135deg, #581c87 0%, #7c3aed 50%, #3730a3 100%)',
            navbar: 'rgba(255, 255, 255, 0.08)'
        },
        rose: {
            name: 'Rose Garden',
            background: 'linear-gradient(135deg, #9f1239 0%, #e11d48 50%, #881337 100%)',
            navbar: 'rgba(255, 255, 255, 0.08)'
        },
        cyber: {
            name: 'Cyber Teal',
            background: 'linear-gradient(135deg, #065f46 0%, #0891b2 50%, #1e40af 100%)',
            navbar: 'rgba(255, 255, 255, 0.08)'
        },
        ember: {
            name: 'Ember Glow',
            background: 'linear-gradient(135deg, #7c2d12 0%, #ea580c 50%, #92400e 100%)',
            navbar: 'rgba(255, 255, 255, 0.08)'
        },
        midnight: {
            name: 'Midnight Black',
            background: 'linear-gradient(135deg, #111827 0%, #1f2937 50%, #374151 100%)',
            navbar: 'rgba(255, 255, 255, 0.08)'
        },
        aurora: {
            name: 'Aurora Borealis',
            background: 'linear-gradient(135deg, #065f46 0%, #059669 25%, #0891b2 50%, #3b82f6 75%, #8b5cf6 100%)',
            navbar: 'rgba(255, 255, 255, 0.08)'
        },
        neon: {
            name: 'Neon Dreams',
            background: 'linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%)',
            navbar: 'rgba(255, 255, 255, 0.08)'
        },
        volcanic: {
            name: 'Volcanic Night',
            background: 'linear-gradient(135deg, #2c1810 0%, #8b0000 50%, #ff4500 100%)',
            navbar: 'rgba(255, 255, 255, 0.08)'
        }
    },
    
    navbarStyles: {
        glass: {
            name: 'Glassmorphism',
            properties: {
                background: 'rgba(255, 255, 255, 0.08)',
                backdropFilter: 'blur(20px)',
                border: '1px solid rgba(255, 255, 255, 0.15)'
            }
        },
        solid: {
            name: 'Solid Dark',
            properties: {
                background: 'rgba(0, 0, 0, 0.8)',
                backdropFilter: 'none',
                border: '1px solid rgba(255, 255, 255, 0.1)'
            }
        },
        gradient: {
            name: 'Gradient',
            properties: {
                background: 'linear-gradient(135deg, rgba(102, 126, 234, 0.8), rgba(118, 75, 162, 0.8))',
                backdropFilter: 'blur(10px)',
                border: '1px solid rgba(255, 255, 255, 0.2)'
            }
        },
        minimal: {
            name: 'Minimal',
            properties: {
                background: 'rgba(255, 255, 255, 0.05)',
                backdropFilter: 'blur(30px)',
                border: 'none'
            }
        },
        neon: {
            name: 'Neon Edge',
            properties: {
                background: 'rgba(0, 0, 0, 0.7)',
                backdropFilter: 'blur(15px)',
                border: '1px solid rgba(0, 255, 255, 0.3)',
                boxShadow: '0 0 20px rgba(0, 255, 255, 0.1)'
            }
        },
        metallic: {
            name: 'Metallic',
            properties: {
                background: 'linear-gradient(135deg, rgba(55, 65, 81, 0.9), rgba(75, 85, 99, 0.9))',
                backdropFilter: 'blur(20px)',
                border: '1px solid rgba(156, 163, 175, 0.3)'
            }
        }
    },

    init() {
        this.loadSavedTheme();
        this.createThemeModal();
        this.bindEvents();
    },

    loadSavedTheme() {
        const savedTheme = localStorage.getItem('privatevault_theme') || 'cosmic';
        const savedNavbarStyle = localStorage.getItem('privatevault_navbar_style') || 'glass';
        
        this.applyTheme(savedTheme);
        this.applyNavbarStyle(savedNavbarStyle);
    },

    applyTheme(themeName) {
        const theme = this.themes[themeName];
        if (theme) {
            document.body.style.background = theme.background;
            localStorage.setItem('privatevault_theme', themeName);
            
            // Trigger custom event for other components
            window.dispatchEvent(new CustomEvent('themeChanged', { 
                detail: { theme: themeName, background: theme.background } 
            }));
        }
    },

    applyNavbarStyle(styleName) {
        const style = this.navbarStyles[styleName];
        if (style) {
            const sidebar = document.querySelector('.modern-sidebar');
            const mobileNavbar = document.querySelector('.mobile-navbar');
            const themeBar = document.querySelector('.theme-control-bar');
            
            if (sidebar) {
                Object.assign(sidebar.style, style.properties);
            }
            if (mobileNavbar) {
                Object.assign(mobileNavbar.style, style.properties);
            }
            if (themeBar) {
                Object.assign(themeBar.style, style.properties);
            }
            
            localStorage.setItem('privatevault_navbar_style', styleName);
        }
    },

    createThemeModal() {
        // Create theme picker modal if it doesn't exist
        if (!document.getElementById('themePickerModal')) {
            const modalHTML = `
                <div id="themePickerModal" class="theme-picker-modal">
                    <div class="theme-picker-content">
                        <div class="theme-picker-header">
                            <h3 class="text-lg font-semibold">Theme & Style auswählen</h3>
                            <button class="theme-picker-close" onclick="closeThemePicker()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <div class="theme-picker-body">
                            <!-- Background Themes -->
                            <div class="theme-section">
                                <h4 class="theme-section-title">Hintergrund-Themes</h4>
                                <div class="theme-grid" id="themeGrid">
                                    <!-- Themes will be populated by JavaScript -->
                                </div>
                            </div>
                            
                            <!-- Navbar Styles -->
                            <div class="theme-section">
                                <h4 class="theme-section-title">Navbar-Stile</h4>
                                <div class="navbar-style-grid" id="navbarStyleGrid">
                                    <!-- Navbar styles will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            this.populateThemeModal();
        }
    },

    populateThemeModal() {
        const themeGrid = document.getElementById('themeGrid');
        const navbarGrid = document.getElementById('navbarStyleGrid');
        
        if (themeGrid) {
            themeGrid.innerHTML = '';
            Object.entries(this.themes).forEach(([key, theme]) => {
                const themeOption = document.createElement('div');
                themeOption.className = 'theme-option';
                themeOption.innerHTML = `
                    <div class="theme-preview" style="background: ${theme.background}" onclick="ThemeManager.applyTheme('${key}')"></div>
                    <div class="theme-label">${theme.name}</div>
                `;
                themeGrid.appendChild(themeOption);
            });
        }
        
        if (navbarGrid) {
            navbarGrid.innerHTML = '';
            Object.entries(this.navbarStyles).forEach(([key, style]) => {
                const styleOption = document.createElement('div');
                styleOption.className = 'navbar-style-option';
                styleOption.innerHTML = `
                    <div class="navbar-preview" onclick="ThemeManager.applyNavbarStyle('${key}')">
                        <div class="navbar-sample" style="background: ${style.properties.background}; backdrop-filter: ${style.properties.backdropFilter || 'none'}; border: ${style.properties.border || 'none'}"></div>
                    </div>
                    <div class="style-label">${style.name}</div>
                `;
                navbarGrid.appendChild(styleOption);
            });
        }
    },

    bindEvents() {
        // Listen for theme changes from other components
        window.addEventListener('themeChanged', (e) => {
            // Update any theme-dependent elements
        });
    }
};

// Mobile sidebar functions
function toggleMobileSidebar() {
    const body = document.body;
    const overlay = document.getElementById('sidebarOverlay');
    
    body.classList.toggle('sidebar-active');
    overlay.classList.toggle('show');
}

// User dropdown functions
function toggleUserDropdown() {
    const dropdown = document.getElementById('userDropdown');
    const chevron = document.getElementById('userChevron');
    
    dropdown.classList.toggle('show');
    chevron.style.transform = dropdown.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
}

// Theme control functions
function openThemePicker() {
    const modal = document.getElementById('themePickerModal');
    if (modal) {
        modal.classList.add('active');
    }
}

function closeThemePicker() {
    const modal = document.getElementById('themePickerModal');
    if (modal) {
        modal.classList.remove('active');
    }
}

function toggleNavbarStyle() {
    const styles = Object.keys(ThemeManager.navbarStyles);
    const current = localStorage.getItem('privatevault_navbar_style') || 'glass';
    const currentIndex = styles.indexOf(current);
    const nextIndex = (currentIndex + 1) % styles.length;
    const nextStyle = styles[nextIndex];
    
    ThemeManager.applyNavbarStyle(nextStyle);
    
    // Show notification
    showNotification(`Navbar-Stil: ${ThemeManager.navbarStyles[nextStyle].name}`);
}

function toggleCompactMode() {
    const isCompact = localStorage.getItem('privatevault_compact_mode') === 'true';
    const newMode = !isCompact;
    
    localStorage.setItem('privatevault_compact_mode', newMode.toString());
    
    if (newMode) {
        document.documentElement.classList.add('compact-mode');
    } else {
        document.documentElement.classList.remove('compact-mode');
    }
    
    showNotification(`Kompakter Modus: ${newMode ? 'Aktiviert' : 'Deaktiviert'}`);
}

function resetTheme() {
    localStorage.removeItem('privatevault_theme');
    localStorage.removeItem('privatevault_navbar_style');
    localStorage.removeItem('privatevault_compact_mode');
    
    ThemeManager.applyTheme('cosmic');
    ThemeManager.applyNavbarStyle('glass');
    document.documentElement.classList.remove('compact-mode');
    
    showNotification('Theme zurückgesetzt');
}

function showNotification(message) {
    // Create and show a temporary notification
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 1rem;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        z-index: 9999;
        font-size: 0.875rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 2000);
}

// Initialize theme management when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    ThemeManager.init();
    
    // Load compact mode
    if (localStorage.getItem('privatevault_compact_mode') === 'true') {
        document.documentElement.classList.add('compact-mode');
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.user-menu')) {
            const dropdown = document.getElementById('userDropdown');
            const chevron = document.getElementById('userChevron');
            
            if (dropdown && dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
                chevron.style.transform = 'rotate(0deg)';
            }
        }
    });
});
</script>

<style>
/* Global theme variables */
:root {
    --navbar-bg: rgba(255, 255, 255, 0.08);
    --navbar-border: rgba(255, 255, 255, 0.15);
    --navbar-text: rgba(255, 255, 255, 0.9);
    --navbar-text-hover: white;
    --navbar-active: rgba(255, 255, 255, 0.2);
    --navbar-backdrop: blur(20px);
    --theme-primary: #667eea;
    --theme-secondary: #764ba2;
}

/* Theme Picker Modal Styles */
.theme-picker-modal {
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

.theme-picker-modal.active {
    display: flex;
    opacity: 1;
}

.theme-picker-content {
    background: rgba(30, 30, 30, 0.95);
    backdrop-filter: blur(30px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 1.5rem;
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.5);
    max-width: 600px;
    width: 100%;
    max-height: 80vh;
    overflow-y: auto;
    color: white;
    transform: scale(0.9) translateY(20px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.theme-picker-modal.active .theme-picker-content {
    transform: scale(1) translateY(0);
}

.theme-picker-header {
    padding: 1.5rem 1.5rem 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.theme-picker-close {
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

.theme-picker-close:hover {
    color: white;
    background: rgba(255, 255, 255, 0.1);
}

.theme-picker-body {
    padding: 1.5rem;
}

.theme-section {
    margin-bottom: 2rem;
}

.theme-section-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: rgba(255, 255, 255, 0.9);
}

.theme-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 1rem;
}

.theme-option {
    text-align: center;
}

.theme-preview {
    aspect-ratio: 16/9;
    border-radius: 0.75rem;
    cursor: pointer;
    position: relative;
    border: 2px solid transparent;
    transition: all 0.3s ease;
    overflow: hidden;
}

.theme-preview:hover {
    transform: scale(1.05);
    border-color: rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
}

.theme-label {
    margin-top: 0.5rem;
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.7);
    font-weight: 500;
}

.navbar-style-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 1rem;
}

.navbar-style-option {
    text-align: center;
}

.navbar-preview {
    height: 60px;
    border-radius: 0.75rem;
    cursor: pointer;
    position: relative;
    border: 2px solid transparent;
    transition: all 0.3s ease;
    overflow: hidden;
    background: rgba(20, 20, 20, 0.8);
}

.navbar-preview:hover {
    transform: scale(1.05);
    border-color: rgba(255, 255, 255, 0.3);
}

.navbar-sample {
    height: 100%;
    border-radius: 0.5rem;
    margin: 4px;
    position: relative;
}

.style-label {
    margin-top: 0.5rem;
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.7);
    font-weight: 500;
}

/* Modern Sidebar Navigation */
.modern-sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 16rem;
    height: 100vh;
    background: var(--navbar-bg);
    backdrop-filter: var(--navbar-backdrop);
    border-right: 1px solid var(--navbar-border);
    box-shadow: 4px 0 20px rgba(0, 0, 0, 0.3);
    z-index: 1000;
    transform: translateX(-100%);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: var(--navbar-border) transparent;
}

.modern-sidebar::-webkit-scrollbar {
    width: 6px;
}

.modern-sidebar::-webkit-scrollbar-track {
    background: transparent;
}

.modern-sidebar::-webkit-scrollbar-thumb {
    background: var(--navbar-border);
    border-radius: 3px;
}

@media (min-width: 769px) {
    .modern-sidebar {
        transform: translateX(0);
    }
}

/* Mobile top navbar */
.mobile-navbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 4rem;
    background: var(--navbar-bg);
    backdrop-filter: var(--navbar-backdrop);
    border-bottom: 1px solid var(--navbar-border);
    z-index: 1001;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 1rem;
}

@media (min-width: 769px) {
    .mobile-navbar {
        display: none;
    }
}

/* Brand/Logo */
.navbar-brand {
    display: flex;
    align-items: center;
    padding: 1.5rem 1rem;
    color: var(--navbar-text);
    text-decoration: none;
    font-weight: 700;
    font-size: 1.25rem;
    transition: all 0.3s ease;
    border-bottom: 1px solid var(--navbar-border);
}

.navbar-brand:hover {
    color: var(--navbar-text-hover);
    background: rgba(255, 255, 255, 0.05);
}

.brand-icon {
    background: linear-gradient(135deg, var(--theme-primary), var(--theme-secondary));
    color: white;
    width: 2rem;
    height: 2rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.75rem;
    font-size: 1rem;
}

/* Navigation Links */
.nav-menu {
    padding: 1rem 0;
    flex: 1;
}

.nav-item {
    margin-bottom: 0.25rem;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    color: var(--navbar-text);
    text-decoration: none;
    transition: all 0.3s ease;
    border-radius: 0.5rem;
    margin: 0 0.5rem;
    position: relative;
    overflow: hidden;
}

.nav-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    transition: left 0.5s ease;
}

.nav-link:hover::before {
    left: 100%;
}

.nav-link:hover {
    color: var(--navbar-text-hover);
    background: rgba(255, 255, 255, 0.1);
    transform: translateX(4px);
}

.nav-link.active {
    background: var(--navbar-active);
    color: var(--navbar-text-hover);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.nav-icon {
    width: 1.25rem;
    margin-right: 0.75rem;
    text-align: center;
}

/* User Menu */
.user-menu {
    padding: 1rem;
    border-top: 1px solid var(--navbar-border);
    position: relative;
}

.user-toggle {
    display: flex;
    align-items: center;
    width: 100%;
    padding: 0.75rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--navbar-border);
    border-radius: 0.75rem;
    color: var(--navbar-text);
    cursor: pointer;
    transition: all 0.3s ease;
}

.user-toggle:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.25);
}

.user-avatar {
    width: 2rem;
    height: 2rem;
    background: linear-gradient(135deg, var(--theme-primary), var(--theme-secondary));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    margin-right: 0.75rem;
    font-size: 0.875rem;
}

.user-dropdown {
    position: absolute;
    bottom: 100%;
    left: 1rem;
    right: 1rem;
    background: var(--navbar-bg);
    backdrop-filter: var(--navbar-backdrop);
    border: 1px solid var(--navbar-border);
    border-radius: 0.75rem;
    box-shadow: 0 -8px 32px rgba(0, 0, 0, 0.3);
    opacity: 0;
    visibility: hidden;
    transform: translateY(10px);
    transition: all 0.3s ease;
    margin-bottom: 0.5rem;
}

.user-dropdown.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    color: var(--navbar-text);
    text-decoration: none;
    transition: all 0.3s ease;
    border-radius: 0.5rem;
    margin: 0.25rem;
}

.dropdown-item:hover {
    color: var(--navbar-text-hover);
    background: rgba(255, 255, 255, 0.1);
}

/* Theme Control Bar */
.theme-control-bar {
    position: fixed;
    top: 1rem;
    right: 1rem;
    z-index: 1050;
    background: var(--navbar-bg);
    backdrop-filter: var(--navbar-backdrop);
    border: 1px solid var(--navbar-border);
    border-radius: 1rem;
    padding: 0.5rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

@media (max-width: 768px) {
    .theme-control-bar {
        top: 5rem;
        right: 0.5rem;
    }
}

.theme-control-icon {
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

.theme-control-icon::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    transition: left 0.5s ease;
}

.theme-control-icon:hover::before {
    left: 100%;
}

.theme-control-icon:hover {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.25);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.theme-control-icon.active {
    background: rgba(102, 126, 234, 0.3);
    border-color: rgba(102, 126, 234, 0.5);
    color: #93c5fd;
}

/* Mobile menu toggle */
.mobile-menu-toggle {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--navbar-border);
    border-radius: 0.5rem;
    color: var(--navbar-text);
    padding: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.mobile-menu-toggle:hover {
    background: rgba(255, 255, 255, 0.15);
    color: var(--navbar-text-hover);
}

/* Mobile sidebar overlay */
.sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.sidebar-overlay.show {
    opacity: 1;
    visibility: visible;
}

/* Show sidebar on mobile when active */
.sidebar-active .modern-sidebar {
    transform: translateX(0);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .modern-sidebar {
        width: 18rem;
    }
}

/* Print styles */
@media print {
    .modern-sidebar,
    .mobile-navbar,
    .theme-control-bar {
        display: none !important;
    }
}
</style>
