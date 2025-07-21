<?php
/**
 * Test script for notification system
 * Run this file to test the notification functionality
 */

require_once __DIR__ . '/../src/lib/auth.php';
requireLogin();

$user = getUser();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benachrichtigungs-Test | PrivateVault</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #2d1b69 0%, #11101d 30%, #1a0909 100%);
            color: white;
            min-height: 100vh;
        }
        
        .test-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 2rem;
            margin: 1rem;
        }
        
        .btn {
            background: rgba(59, 130, 246, 0.8);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 0.5rem;
        }
        
        .btn:hover {
            background: rgba(59, 130, 246, 1);
            transform: translateY(-1px);
        }
        
        .btn-success { background: rgba(16, 185, 129, 0.8); }
        .btn-success:hover { background: rgba(16, 185, 129, 1); }
        
        .btn-warning { background: rgba(245, 158, 11, 0.8); }
        .btn-warning:hover { background: rgba(245, 158, 11, 1); }
        
        .btn-error { background: rgba(239, 68, 68, 0.8); }
        .btn-error:hover { background: rgba(239, 68, 68, 1); }
    </style>
</head>
<body>
    <div class="container mx-auto p-8">
        <h1 class="text-4xl font-bold text-center mb-8">🔔 Benachrichtigungs-Test</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Desktop Notification Test -->
            <div class="test-card">
                <h2 class="text-xl font-semibold mb-4">
                    <i class="fas fa-desktop mr-2"></i>
                    Desktop-Benachrichtigungen
                </h2>
                <p class="text-gray-300 mb-4">Teste Browser-Benachrichtigungen</p>
                <button class="btn" onclick="testDesktopNotification()">
                    <i class="fas fa-bell mr-2"></i>
                    Test senden
                </button>
                <button class="btn btn-warning" onclick="requestNotificationPermission()">
                    <i class="fas fa-key mr-2"></i>
                    Berechtigung anfordern
                </button>
            </div>
            
            <!-- API Notification Test -->
            <div class="test-card">
                <h2 class="text-xl font-semibold mb-4">
                    <i class="fas fa-server mr-2"></i>
                    API-Benachrichtigungen
                </h2>
                <p class="text-gray-300 mb-4">Teste Server-Benachrichtigungen</p>
                <button class="btn" onclick="testApiNotification('info')">
                    <i class="fas fa-info-circle mr-2"></i>
                    Info
                </button>
                <button class="btn btn-success" onclick="testApiNotification('success')">
                    <i class="fas fa-check-circle mr-2"></i>
                    Erfolg
                </button>
                <button class="btn btn-warning" onclick="testApiNotification('warning')">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Warnung
                </button>
                <button class="btn btn-error" onclick="testApiNotification('error')">
                    <i class="fas fa-times-circle mr-2"></i>
                    Fehler
                </button>
            </div>
            
            <!-- Notification Settings -->
            <div class="test-card">
                <h2 class="text-xl font-semibold mb-4">
                    <i class="fas fa-cog mr-2"></i>
                    Einstellungen
                </h2>
                <p class="text-gray-300 mb-4">Benachrichtigungseinstellungen verwalten</p>
                <button class="btn" onclick="loadNotificationSettings()">
                    <i class="fas fa-download mr-2"></i>
                    Einstellungen laden
                </button>
                <button class="btn btn-success" onclick="saveTestSettings()">
                    <i class="fas fa-save mr-2"></i>
                    Test-Einstellungen speichern
                </button>
            </div>
            
            <!-- Task Reminder Test -->
            <div class="test-card">
                <h2 class="text-xl font-semibold mb-4">
                    <i class="fas fa-tasks mr-2"></i>
                    Aufgaben-Erinnerung
                </h2>
                <p class="text-gray-300 mb-4">Simuliere Aufgaben-Erinnerung</p>
                <button class="btn" onclick="testTaskReminder()">
                    <i class="fas fa-clock mr-2"></i>
                    Erinnerung senden
                </button>
            </div>
            
            <!-- Calendar Event Test -->
            <div class="test-card">
                <h2 class="text-xl font-semibold mb-4">
                    <i class="fas fa-calendar mr-2"></i>
                    Kalender-Ereignis
                </h2>
                <p class="text-gray-300 mb-4">Simuliere Kalender-Erinnerung</p>
                <button class="btn" onclick="testCalendarEvent()">
                    <i class="fas fa-calendar-check mr-2"></i>
                    Ereignis senden
                </button>
            </div>
            
            <!-- Finance Update Test -->
            <div class="test-card">
                <h2 class="text-xl font-semibold mb-4">
                    <i class="fas fa-euro-sign mr-2"></i>
                    Finanz-Update
                </h2>
                <p class="text-gray-300 mb-4">Simuliere Finanz-Benachrichtigung</p>
                <button class="btn" onclick="testFinanceUpdate()">
                    <i class="fas fa-money-bill-wave mr-2"></i>
                    Update senden
                </button>
            </div>
        </div>
        
        <!-- Status Display -->
        <div class="test-card mt-8">
            <h2 class="text-xl font-semibold mb-4">
                <i class="fas fa-chart-line mr-2"></i>
                Status & Logs
            </h2>
            <div id="statusLog" class="bg-black bg-opacity-50 p-4 rounded-lg font-mono text-sm max-h-64 overflow-y-auto">
                <div class="text-green-400">Benachrichtigungs-Test bereit...</div>
            </div>
        </div>
        
        <div class="text-center mt-8">
            <a href="/templates/dashboard.php" class="btn">
                <i class="fas fa-arrow-left mr-2"></i>
                Zurück zum Dashboard
            </a>
        </div>
    </div>

    <script>
        function log(message, type = 'info') {
            const logElement = document.getElementById('statusLog');
            const timestamp = new Date().toLocaleTimeString();
            const colors = {
                'info': 'text-blue-400',
                'success': 'text-green-400',
                'warning': 'text-yellow-400',
                'error': 'text-red-400'
            };
            
            const logEntry = document.createElement('div');
            logEntry.className = colors[type] || 'text-gray-400';
            logEntry.textContent = `[${timestamp}] ${message}`;
            
            logElement.appendChild(logEntry);
            logElement.scrollTop = logElement.scrollHeight;
        }
        
        function testDesktopNotification() {
            log('Teste Desktop-Benachrichtigung...');
            
            if ('Notification' in window) {
                if (Notification.permission === 'granted') {
                    new Notification('Test-Benachrichtigung', {
                        body: 'Dies ist eine Test-Benachrichtigung von PrivateVault.',
                        icon: '/favicon.ico'
                    });
                    log('Desktop-Benachrichtigung gesendet!', 'success');
                } else {
                    log('Keine Berechtigung für Desktop-Benachrichtigungen', 'warning');
                }
            } else {
                log('Desktop-Benachrichtigungen werden nicht unterstützt', 'error');
            }
        }
        
        function requestNotificationPermission() {
            log('Fordere Benachrichtigungsberechtigung an...');
            
            if ('Notification' in window) {
                Notification.requestPermission().then(permission => {
                    if (permission === 'granted') {
                        log('Berechtigung erteilt!', 'success');
                    } else {
                        log('Berechtigung verweigert', 'warning');
                    }
                });
            } else {
                log('Benachrichtigungen werden nicht unterstützt', 'error');
            }
        }
        
        async function testApiNotification(type) {
            log(`Sende ${type}-Benachrichtigung über API...`);
            
            const messages = {
                'info': 'Dies ist eine Informations-Benachrichtigung.',
                'success': 'Vorgang erfolgreich abgeschlossen!',
                'warning': 'Achtung: Hier ist eine Warnung.',
                'error': 'Fehler: Etwas ist schiefgelaufen.'
            };
            
            try {
                const response = await fetch('/api/notifications.php', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        title: `Test ${type.toUpperCase()}`,
                        message: messages[type],
                        type: type
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    log(`${type}-Benachrichtigung erfolgreich gesendet!`, 'success');
                } else {
                    log(`Fehler: ${result.error}`, 'error');
                }
            } catch (error) {
                log(`API-Fehler: ${error.message}`, 'error');
            }
        }
        
        async function loadNotificationSettings() {
            log('Lade Benachrichtigungseinstellungen...');
            
            try {
                const response = await fetch('/api/notifications.php');
                const result = await response.json();
                
                if (result.success) {
                    log('Einstellungen erfolgreich geladen:', 'success');
                    log(JSON.stringify(result.settings, null, 2));
                } else {
                    log(`Fehler beim Laden: ${result.error}`, 'error');
                }
            } catch (error) {
                log(`Fehler: ${error.message}`, 'error');
            }
        }
        
        async function saveTestSettings() {
            log('Speichere Test-Einstellungen...');
            
            const testSettings = {
                desktop_enabled: true,
                sound_enabled: true,
                email_enabled: false,
                task_reminders: true,
                calendar_events: true,
                system_alerts: true,
                quiet_start: '22:00',
                quiet_end: '07:00',
                frequency: 'immediate'
            };
            
            try {
                const response = await fetch('/api/notifications.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(testSettings)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    log('Test-Einstellungen gespeichert!', 'success');
                } else {
                    log(`Fehler: ${result.error}`, 'error');
                }
            } catch (error) {
                log(`Fehler: ${error.message}`, 'error');
            }
        }
        
        function testTaskReminder() {
            testApiNotification('info');
            log('Aufgaben-Erinnerung simuliert', 'info');
        }
        
        function testCalendarEvent() {
            testApiNotification('info');
            log('Kalender-Ereignis simuliert', 'info');
        }
        
        function testFinanceUpdate() {
            testApiNotification('success');
            log('Finanz-Update simuliert', 'info');
        }
        
        // Initialize
        log('Benachrichtigungs-Test geladen');
        log(`Notification API verfügbar: ${'Notification' in window ? 'Ja' : 'Nein'}`);
        if ('Notification' in window) {
            log(`Berechtigung: ${Notification.permission}`);
        }
    </script>
</body>
</html>
