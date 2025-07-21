# 🔔 Benachrichtigungssystem - PrivateVault

## Übersicht

Das Benachrichtigungssystem von PrivateVault bietet eine vollständige Lösung für Desktop-Benachrichtigungen, In-App-Notifications und E-Mail-Benachrichtigungen.

## ✨ Features

### 🖥️ **Desktop-Benachrichtigungen**
- Native Browser-Benachrichtigungen
- Automatische Berechtigungsanfrage
- Unterschiedliche Töne je Benachrichtigungstyp
- Click-to-Action Funktionalität

### 📱 **In-App-Benachrichtigungen**
- Glassmorphism-Design
- Verschiedene Typen (Info, Erfolg, Warnung, Fehler)
- Automatisches Ausblenden nach 5 Sekunden
- Responsive Design

### 📧 **E-Mail-Benachrichtigungen**
- HTML-E-Mail-Templates
- Benutzerindividuelle Einstellungen
- Verschiedene Kategorien
- Automatische Versendung

### ⚙️ **Erweiterte Einstellungen**
- Ruhemodus (Quiet Hours)
- Benachrichtigungsfrequenz
- Kategorienspezifische Einstellungen
- Vollständige Anpassbarkeit

## 🚀 Implementierung

### 1. **Dashboard-Integration**

Das Benachrichtigungssystem ist vollständig in das Dashboard integriert:

```javascript
// Benachrichtigung senden
sendNotification('Titel', 'Nachricht', 'info');

// Einstellungen öffnen
openNotificationSettings();

// Test-Benachrichtigung
testNotification();
```

### 2. **Control Bar**

Das Benachrichtigungs-Icon in der Control Bar zeigt:
- 🔔 Benachrichtigungs-Icon
- 🔴 Rotes Badge mit Anzahl ungelesener Benachrichtigungen
- Klick öffnet die Einstellungen

### 3. **API-Endpunkte**

#### **GET** `/api/notifications.php`
```json
{
  "success": true,
  "settings": {
    "desktop_enabled": true,
    "sound_enabled": true,
    "email_enabled": true,
    "task_reminders": true,
    "calendar_events": true,
    "quiet_start": "22:00",
    "quiet_end": "07:00",
    "frequency": "immediate"
  }
}
```

#### **POST** `/api/notifications.php`
```json
{
  "desktop_enabled": true,
  "sound_enabled": false,
  "task_reminders": true,
  "quiet_start": "23:00",
  "quiet_end": "06:00"
}
```

#### **PUT** `/api/notifications.php` (Neue Benachrichtigung)
```json
{
  "title": "Test-Benachrichtigung",
  "message": "Dies ist eine Test-Nachricht",
  "type": "info",
  "data": {
    "action": "open_task",
    "taskId": 123
  }
}
```

#### **GET** `/api/notifications.php?action=unread_count`
```json
{
  "success": true,
  "count": 5
}
```

### 4. **Datenbank-Schema**

#### **notifications** Tabelle
```sql
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT,
    type ENUM('info', 'success', 'warning', 'error', 'reminder'),
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    scheduled_for TIMESTAMP NULL,
    data JSON NULL
);
```

#### **notification_types** Tabelle
```sql
CREATE TABLE notification_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50),
    color VARCHAR(7) DEFAULT '#3B82F6'
);
```

## 🛠️ Verwendung

### **Grundlegende Benachrichtigung senden**

```php
// NotificationManager verwenden
$notificationManager = new NotificationManager($pdo);

$notificationManager->sendNotification(
    $userId,
    'Aufgaben-Erinnerung',
    'Ihre Aufgabe "Projekt fertigstellen" ist heute fällig.',
    'task_reminder',
    ['action' => 'open_task', 'taskId' => 123]
);
```

### **Aufgaben-Erinnerung**

```php
$notificationManager->sendTaskReminder(
    $userId,
    'Projekt fertigstellen',
    123,
    '2024-01-15 14:00:00'
);
```

### **Kalender-Ereignis**

```php
$notificationManager->sendCalendarReminder(
    $userId,
    'Meeting mit Team',
    456,
    '2024-01-15 09:00:00'
);
```

### **Finanz-Update**

```php
$notificationManager->sendFinanceUpdate(
    $userId,
    '-50.00',
    'Abonnement erneuert'
);
```

### **Bulk-Benachrichtigung**

```php
$userIds = [1, 2, 3, 4, 5];
$sent = $notificationManager->sendBulkNotification(
    $userIds,
    'System-Wartung',
    'Das System wird heute um 22:00 Uhr für Wartungsarbeiten offline sein.',
    'system_alert'
);
```

## 🎛️ Einstellungen

### **Verfügbare Optionen**

| Einstellung | Beschreibung | Standard |
|-------------|--------------|----------|
| `desktop_enabled` | Desktop-Benachrichtigungen | `true` |
| `sound_enabled` | Ton-Benachrichtigungen | `true` |
| `email_enabled` | E-Mail-Benachrichtigungen | `true` |
| `task_reminders` | Aufgaben-Erinnerungen | `true` |
| `calendar_events` | Kalender-Ereignisse | `true` |
| `note_reminders` | Notiz-Erinnerungen | `false` |
| `system_alerts` | System-Benachrichtigungen | `true` |
| `finance_updates` | Finanz-Updates | `true` |
| `document_uploads` | Dokument-Uploads | `true` |
| `security_warnings` | Sicherheitswarnungen | `true` |
| `quiet_start` | Ruhemodus Start | `"22:00"` |
| `quiet_end` | Ruhemodus Ende | `"07:00"` |
| `frequency` | Benachrichtigungsfrequenz | `"immediate"` |

### **Frequenz-Optionen**
- `immediate` - Sofort
- `hourly` - Stündlich
- `daily` - Täglich
- `weekly` - Wöchentlich

## 🔊 Benachrichtigungstypen

### **Verfügbare Typen**

| Typ | Icon | Farbe | Beschreibung |
|-----|------|-------|--------------|
| `info` | `fas fa-info-circle` | Blau | Allgemeine Informationen |
| `success` | `fas fa-check-circle` | Grün | Erfolgreiche Aktionen |
| `warning` | `fas fa-exclamation-triangle` | Gelb | Warnungen |
| `error` | `fas fa-times-circle` | Rot | Fehlermeldungen |
| `task_reminder` | `fas fa-tasks` | Orange | Aufgaben-Erinnerungen |
| `calendar_event` | `fas fa-calendar` | Grün | Kalender-Ereignisse |
| `finance_update` | `fas fa-euro-sign` | Blau | Finanz-Updates |
| `security_warning` | `fas fa-shield-alt` | Rot | Sicherheitswarnungen |

## 🎵 Audio-Feedback

Verschiedene Benachrichtigungstypen haben unterschiedliche Töne:
- **Info**: 700 Hz
- **Erfolg**: 800 Hz  
- **Warnung**: 600 Hz
- **Fehler**: 400 Hz

## 🕰️ Ruhemodus

Der Ruhemodus verhindert Benachrichtigungen während bestimmter Zeiten:
- Standard: 22:00 - 07:00
- Vollständig anpassbar
- Gilt nur für Desktop/Audio-Benachrichtigungen
- E-Mail-Benachrichtigungen werden nicht beeinflusst

## 🧪 Testing

### **Test-Seite verwenden**

Besuchen Sie `/test/notification_test.php` für eine umfassende Test-Umgebung:

1. **Desktop-Benachrichtigungen testen**
2. **API-Funktionalität testen**
3. **Einstellungen verwalten**
4. **Verschiedene Benachrichtigungstypen ausprobieren**

### **Entwickler-Tools**

```javascript
// Im Browser-Console
sendNotification('Test', 'Nachricht', 'info');

// Einstellungen überprüfen
loadNotificationSettings().then(settings => console.log(settings));

// Badge aktualisieren
updateNotificationBadge();
```

## 🔄 Automatisierung

### **Cron Job für Erinnerungen**

```bash
# Jede Stunde prüfen
0 * * * * php /path/to/privatevault/src/lib/notification_examples.php
```

### **Scheduled Notifications**

Das System kann Benachrichtigungen für die Zukunft planen:

```php
$scheduledFor = '2024-01-15 09:00:00';
$notificationManager->sendNotification(
    $userId,
    'Geplante Erinnerung',
    'Dies ist eine geplante Benachrichtigung.',
    'reminder',
    null,
    $scheduledFor
);
```

## 🐛 Debugging

### **Console-Logs aktivieren**

```javascript
localStorage.setItem('debugMode', 'true');
```

### **Häufige Probleme**

1. **Berechtigung verweigert**: Benutzer muss Benachrichtigungen explizit erlauben
2. **Keine Töne**: Audio-Context benötigt Benutzerinteraktion
3. **E-Mails kommen nicht an**: SMTP-Konfiguration prüfen

## 📱 Mobile Support

- Touch-optimierte In-App-Benachrichtigungen
- Responsive Design
- Progressive Web App (PWA) Unterstützung
- Service Worker Integration (geplant)

## 🔐 Sicherheit

- Alle Benachrichtigungen sind benutzergebunden
- Keine sensiblen Daten in Client-seitigen Logs
- E-Mail-Templates sind XSS-sicher
- Rate-Limiting für API-Aufrufe (empfohlen)

## 🎯 Fazit

Das Benachrichtigungssystem bietet eine vollständige, benutzerfreundliche Lösung für alle Notification-Bedürfnisse in PrivateVault. Mit der flexiblen API, umfangreichen Anpassungsmöglichkeiten und modernem Design ist es bereit für den Produktiveinsatz.

**Viel Spaß mit den Benachrichtigungen! 🎉**
