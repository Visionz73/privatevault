# Second Brain - Setup & Usage Guide

## 🧠 Was ist das Second Brain System?

Das Second Brain System erweitert deine bestehende Notiz-Anwendung um moderne Knowledge-Management-Features:

- **Graph-Ansicht**: Visualisiere Verbindungen zwischen deinen Notizen
- **Bidirektionale Verlinkung**: Erstelle automatische Backlinks zwischen Notizen
- **Erweiterte Tagging**: Filtere und organisiere mit einem intelligenten Tag-System
- **Statistik-Dashboard**: Verfolge deine Notiz-Aktivitäten
- **Erweiterte Suche**: Finde Notizen nach Inhalt, Tags und Verbindungen

## 📋 Setup-Anleitung

### 1. Datenbank-Setup

```bash
# Führe das Enhanced-Schema aus
php database/enhanced_notes_tables.php
```

### 2. Integration in bestehende Seiten

Ersetze das bestehende Notes-Template:

```php
// In deiner notes.php oder entsprechenden Route:
require_once __DIR__ . '/templates/enhanced-notes.php';
```

### 3. API-Integration

Die erweiterte API ist bereits in `src/api/notes.php` integriert. Neue Endpunkte:

- `GET /src/api/notes.php?action=graph` - Graph-Daten
- `GET /src/api/notes.php?action=links&id=123` - Notiz-Verbindungen
- `GET /src/api/notes.php?action=search&q=query` - Erweiterte Suche
- `GET /src/api/notes.php?action=stats` - Statistiken
- `POST /src/api/notes.php?action=link` - Verbindung erstellen
- `PUT /src/api/notes.php?action=position` - Knoten-Position speichern

## 🎯 Wichtigste Features

### 1. Knowledge Graph

**Aktivierung**: Klicke auf "Graph View" oder drücke `Ctrl+Shift+G`

**Features**:
- Interaktive Knoten-Darstellung aller Notizen
- Hover-Tooltips mit Notiz-Titeln
- Drag & Drop für Knoten-Positionierung
- Zoom und Pan-Funktionen
- Verbindungslinien zwischen verknüpften Notizen

**Steuerelemente**:
- Re-layout: Neuanordnung der Knoten
- Center: Zentriert die Ansicht
- Fullscreen: Vollbild-Modus
- Node Size: Knotengröße anpassen
- Link Strength: Verbindungsstärke

### 2. Bidirektionale Verlinkung

**Verwendung**:
1. Öffne eine Notiz im Editor (`Ctrl+N` für neue Notiz)
2. Verwende `[[Notiz Titel]]` Syntax für automatische Verlinkung
3. Oder klicke auf "Create Link" Button (`Ctrl+L`)
4. Wähle Ziel-Notiz im Graph-Modus

**Link-Typen**:
- `reference`: Standard-Referenz
- `mention`: Erwähnung
- `relates_to`: Thematische Verbindung
- `follows_from`: Logische Folge

### 3. Erweiterte Tags

**Tag-Syntax**:
- Einfache Tags: `#wichtig`, `#idee`, `#projekt`
- Hierarchische Tags: `#projekt/arbeit`, `#daily/gedanken`
- Spezial-Tags: `TAG:DailyThoughts`, `TAG:Dokumentation`

**Tag-Filter**:
- Klicke auf Tags in der Tag-Cloud
- Verwende die Filter-Buttons
- Kombiniere mehrere Tags

### 4. Statistik-Dashboard

**Metriken**:
- Gesamtanzahl Notizen
- Notizen diese Woche
- Anzahl Verbindungen
- Top-verwendete Tags
- Aktivitäts-Chart

### 5. Quick Actions

**Daily Note**: 
- Erstellt automatisch eine Tagesnotiz mit Vorlage
- Format: "Daily Note - YYYY-MM-DD"
- Enthält Sections für Ziele, Notizen, Reflexion

**Random Note**:
- Öffnet eine zufällige Notiz zur Inspiration
- Hilfreich für Wiederentdeckung alter Ideen

**Unlinked Notes**:
- Zeigt Notizen ohne Verbindungen
- Hilft beim Identifizieren isolierter Inhalte

**Export Backup**:
- Exportiert alle Notizen als JSON
- Enthält Metadaten und Verbindungen

## ⌨️ Keyboard Shortcuts

### Globale Shortcuts:
- `Ctrl+N`: Neue Notiz
- `Ctrl+K`: Suche fokussieren  
- `Ctrl+Shift+G`: Graph-Ansicht
- `Ctrl+Shift+D`: Daily Note erstellen
- `Ctrl+Shift+R`: Zufällige Notiz
- `Ctrl+Shift+E`: Backup exportieren

### Editor-Shortcuts:
- `Ctrl+S`: Notiz speichern
- `Ctrl+L`: Verbindung erstellen
- `Escape`: Editor schließen

## 🔧 Erweiterte Konfiguration

### Graph-Einstellungen

```javascript
// Node-Größe basierend auf Verbindungen
brain.calculateNodeSize = (node) => {
    const baseSize = 12;
    const linkCount = node.incoming_links + node.outgoing_links;
    return Math.min(baseSize + (linkCount * 2), 32);
};

// Custom Farben für Knoten-Typen
brain.getNodeColor = (node) => {
    if (node.tags.includes('important')) return '#ef4444';
    if (node.tags.includes('project')) return '#8b5cf6';
    return node.color || '#fbbf24';
};
```

### Custom Link-Typen

```sql
-- Erweitere die ENUM-Werte in der Datenbank
ALTER TABLE note_links 
MODIFY link_type ENUM('reference', 'mention', 'relates_to', 'follows_from', 'contradicts', 'supports') 
DEFAULT 'reference';
```

### Template-System

Erstelle eigene Notiz-Templates:

```javascript
// Custom Daily Note Template
const customDailyTemplate = `# Daily Note - {{date}}

## 🎯 Goals for today
- [ ] 

## 💭 Notes


## 📚 Learned today


## 🔄 Tomorrow's priorities


## 💡 Ideas & Insights


---
Tags: #daily #journal
`;
```

## 🎨 Styling Anpassungen

### Custom CSS für Themes

```css
/* Dark Purple Theme */
:root {
    --primary-gradient: linear-gradient(135deg, #6366f1 0%, #3730a3 100%);
    --glass-bg: rgba(99, 102, 241, 0.08);
    --glass-border: rgba(99, 102, 241, 0.15);
}

/* Custom Node Colors */
.knowledge-graph .node-important {
    fill: #ef4444;
    stroke: #dc2626;
}

.knowledge-graph .node-project {
    fill: #8b5cf6;
    stroke: #7c3aed;
}
```

## 📊 Performance-Optimierung

### Graph-Performance

```javascript
// Für große Notiz-Sammlungen (>1000 Notizen)
brain.maxNodes = 500; // Limitiere Anzahl der Knoten
brain.enableClustering = true; // Aktiviere Clustering
brain.renderDistance = 1000; // Render-Distanz
```

### Batch-Updates

```javascript
// Batch-Aktualisierungen für bessere Performance
brain.batchUpdate(() => {
    // Mehrere Updates hier
    brain.addNode(node1);
    brain.addNode(node2);
    brain.addLink(link1);
});
```

## 🔍 Troubleshooting

### Häufige Probleme:

**Graph lädt nicht**:
```javascript
// Prüfe Browser-Konsole auf Fehler
console.error(); // Zeigt API-Fehler

// Prüfe API-Verbindung
fetch('/src/api/notes.php?action=graph')
    .then(r => r.json())
    .then(console.log);
```

**Verbindungen werden nicht angezeigt**:
```sql
-- Prüfe Datenbank-Verbindungen
SELECT * FROM note_links WHERE source_note_id = 123;
```

**Performance-Probleme**:
- Reduziere `maxNodes` in der Graph-Konfiguration
- Aktiviere Clustering für große Datenmengen
- Verwende Pagination in der API

## 🚀 Nächste Schritte

### Geplante Features:
1. **AI-Integration**: Automatische Tag-Vorschläge und Verbindungen
2. **Kollaboration**: Geteilte Notiz-Räume
3. **Mobile App**: Native iOS/Android App
4. **Plugin-System**: Erweiterbare Funktionalitäten
5. **Advanced Analytics**: Tiefere Einblicke in Wissens-Patterns

### API-Erweiterungen:
- WebSocket für Echtzeit-Updates
- REST API v2 mit GraphQL
- Webhook-System für externe Integrationen

## 📝 Best Practices

### Notiz-Organisation:
1. **Verwende konsistente Tags** - Etabliere ein Tag-System
2. **Verlinke proaktiv** - Erstelle Verbindungen während des Schreibens
3. **Regelmäßige Reviews** - Nutze die Statistiken für Reflexion
4. **Atomic Notes** - Eine Idee pro Notiz für bessere Verlinkung

### Workflow-Tipps:
1. **Daily Notes als Eingang** - Sammle Ideen zuerst hier
2. **Wöchentliche Graph-Review** - Entdecke neue Verbindungen
3. **Tag-Cleanup** - Regelmäßige Bereinigung des Tag-Systems
4. **Export-Backups** - Wöchentliche Sicherungen

Viel Erfolg mit deinem Second Brain! 🧠✨
