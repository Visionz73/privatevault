# Second Brain Datenbank-Schema Dokumentation

## 🎯 Übersicht

Diese Datenbank-Struktur implementiert ein vollständiges "Second Brain" System mit folgenden Hauptfeatures:

## 📊 Kern-Tabellen

### `notes` - Haupttabelle
```sql
- Grunddaten: id, user_id, title, content
- Graph-Position: node_position_x, node_position_y, node_size
- Metadaten: color, is_pinned, is_archived, word_count, view_count
- Volltext-Suche über title + content
```

### `tags` & `note_tags` - Tag-System
```sql
- Farbige Tags mit Verwendungszähler
- Many-to-Many Beziehung zwischen Notes und Tags
- Automatische usage_count Updates via Trigger
```

### `note_links` - Bidirektionale Verlinkung
```sql
- Verschiedene Link-Typen: reference, mention, relates_to, etc.
- Link-Stärke für gewichtete Graphen
- Anchor-Text und Kontext-Snippets
- Verhindert Selbstverlinkung
```

## 🔧 Erweiterte Features

### Versionierung
- `note_versions`: Automatische Speicherung bei Änderungen
- Trigger erstellt neue Version bei title/content Änderung

### Erinnerungen
- `note_reminders`: Einmalige oder wiederkehrende Erinnerungen
- Support für Desktop-Notifications

### Clustering
- `note_clusters` + `note_cluster_members`
- Manuelle oder automatische Gruppierung
- Similarity-Scores für AI-basierte Cluster

### Benutzer-Einstellungen
- `user_graph_settings`: Graph-Layout, Farben, Filter
- JSON-Felder für flexible Konfiguration

### Statistiken
- `daily_stats`: Tägliche Nutzungsmetriken
- Tracking von erstellten Notizen, Links, Suchanfragen

## 🚀 Performance-Features

### Indizes
```sql
- Volltext-Index auf notes(title, content)
- Kombinierte Indizes für häufige Abfragen
- Foreign Key Indizes für JOIN-Performance
```

### Views
```sql
- v_notes_complete: Notizen mit allen verbundenen Daten
- v_graph_data: Optimiert für Graph-Visualisierung
```

### Triggers
```sql
- Automatische word_count Berechnung
- Automatische Versionierung
- Tag usage_count Updates
```

## 📋 Ausführung

### 1. Automatisches Setup
```bash
php database/setup_second_brain.php
```

### 2. Manuelles Setup
```bash
mysql -u username -p database_name < database/second_brain_complete.sql
```

## 🔍 Wichtige Abfragen

### Graph-Daten abrufen
```sql
SELECT * FROM v_graph_data WHERE node_tags LIKE '%Ideen%';
```

### Bidirektionale Links finden
```sql
SELECT 
    s.title as source_title,
    t.title as target_title,
    nl.link_type,
    nl.anchor_text
FROM note_links nl
JOIN notes s ON nl.source_note_id = s.id
JOIN notes t ON nl.target_note_id = t.id
WHERE s.user_id = ?;
```

### Tag-Cloud generieren
```sql
SELECT name, usage_count, color
FROM tags 
WHERE user_id = ?
ORDER BY usage_count DESC;
```

### Cluster-Analyse
```sql
SELECT 
    nc.cluster_name,
    COUNT(ncm.note_id) as note_count,
    AVG(ncm.similarity_score) as avg_similarity
FROM note_clusters nc
LEFT JOIN note_cluster_members ncm ON nc.id = ncm.cluster_id
WHERE nc.user_id = ?
GROUP BY nc.id;
```

## 🔗 API-Endpoints (Empfehlung)

```
GET    /api/notes              - Alle Notizen
POST   /api/notes              - Neue Notiz
PUT    /api/notes/{id}         - Notiz aktualisieren
DELETE /api/notes/{id}         - Notiz löschen

GET    /api/graph              - Graph-Daten
POST   /api/links              - Link erstellen
DELETE /api/links/{id}         - Link löschen

GET    /api/tags               - Alle Tags
POST   /api/tags               - Tag erstellen
GET    /api/notes/tags/{tag}   - Notizen nach Tag

GET    /api/search?q={query}   - Volltext-Suche
POST   /api/clusters/auto      - Auto-Clustering
```

## 🎨 Frontend-Integration

### Graph-Visualisierung (D3.js/Vis.js)
```javascript
// Nodes aus v_graph_data
// Links aus note_links
// Filter nach Tags
// Zoom auf Cluster
```

### Tag-Filter
```javascript
// Checkbox-Liste aus tags Tabelle
// Multi-Select mit AND/OR Logik
// Live-Update des Graphen
```

### Bidirektionale Links
```javascript
// Markdown-Editor mit [[Notiz-Titel]] Syntax
// Auto-Complete für existierende Notizen
// Backlink-Panel zeigt eingehende Links
```

---

**🚀 Ready to build your Second Brain!**
