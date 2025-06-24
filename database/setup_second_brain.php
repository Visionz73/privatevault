<?php
/**
 * Second Brain Database Setup Script
 * 
 * Dieses Script erstellt alle notwendigen Tabellen für das Second Brain Notiz-System
 * mit Graph-Ansicht, bidirektionalen Links, Tags, Clustering und mehr.
 * 
 * Ausführung: php database/setup_second_brain.php
 */

require_once __DIR__ . '/../config.php';

echo "🧠 Second Brain Database Setup wird gestartet...\n";
echo "================================================\n\n";

try {
    // SQL-Datei lesen
    $sqlFile = __DIR__ . '/second_brain_complete.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL-Datei nicht gefunden: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // SQL in einzelne Statements aufteilen (bei Semikolon, aber nicht in Strings)
    $statements = [];
    $currentStatement = '';
    $inString = false;
    $stringChar = '';
    $escaped = false;
    
    for ($i = 0; $i < strlen($sql); $i++) {
        $char = $sql[$i];
        
        if ($escaped) {
            $currentStatement .= $char;
            $escaped = false;
            continue;
        }
        
        if ($char === '\\') {
            $currentStatement .= $char;
            $escaped = true;
            continue;
        }
        
        if (!$inString && ($char === '"' || $char === "'")) {
            $inString = true;
            $stringChar = $char;
            $currentStatement .= $char;
            continue;
        }
        
        if ($inString && $char === $stringChar) {
            $inString = false;
            $stringChar = '';
            $currentStatement .= $char;
            continue;
        }
        
        if (!$inString && $char === ';') {
            $currentStatement = trim($currentStatement);
            if (!empty($currentStatement) && !preg_match('/^--/', $currentStatement)) {
                $statements[] = $currentStatement;
            }
            $currentStatement = '';
            continue;
        }
        
        $currentStatement .= $char;
    }
    
    // Letztes Statement hinzufügen, falls vorhanden
    $currentStatement = trim($currentStatement);
    if (!empty($currentStatement) && !preg_match('/^--/', $currentStatement)) {
        $statements[] = $currentStatement;
    }
    
    echo "📋 " . count($statements) . " SQL-Statements gefunden\n\n";
    
    // Statements ausführen
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $index => $statement) {
        try {
            // Skip Kommentare und Delimiter-Statements
            if (preg_match('/^\s*(--|DELIMITER|CREATE\s+TRIGGER|CREATE\s+OR\s+REPLACE\s+VIEW)/i', $statement)) {
                continue;
            }
            
            $pdo->exec($statement);
            $successCount++;
            
            // Bestimme Statement-Typ für bessere Ausgabe
            if (preg_match('/^\s*CREATE\s+TABLE/i', $statement)) {
                preg_match('/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?(\w+)`?/i', $statement, $matches);
                $tableName = $matches[1] ?? 'Unknown';
                echo "✅ Tabelle erstellt: $tableName\n";
            } elseif (preg_match('/^\s*CREATE\s+INDEX/i', $statement)) {
                echo "🔍 Index erstellt\n";
            } elseif (preg_match('/^\s*INSERT/i', $statement)) {
                echo "📝 Daten eingefügt\n";
            } else {
                echo "✅ Statement ausgeführt\n";
            }
            
        } catch (PDOException $e) {
            $errorCount++;
            echo "❌ Fehler bei Statement " . ($index + 1) . ": " . $e->getMessage() . "\n";
            echo "   Statement: " . substr($statement, 0, 100) . "...\n";
        }
    }
    
    echo "\n================================================\n";
    echo "🎉 Setup abgeschlossen!\n";
    echo "✅ Erfolgreich: $successCount Statements\n";
    echo "❌ Fehler: $errorCount Statements\n\n";
    
    // Tabellen-Übersicht anzeigen
    echo "📊 Erstellte Tabellen:\n";
    echo "================================================\n";
    
    $tables = [
        'notes' => 'Haupttabelle für alle Notizen',
        'tags' => 'Tag-Definitionen mit Farben',
        'note_tags' => 'Zuordnung von Tags zu Notizen',
        'note_links' => 'Bidirektionale Verlinkungen',
        'note_versions' => 'Versionshistorie der Notizen',
        'note_reminders' => 'Erinnerungen für Notizen',
        'note_clusters' => 'Semantische Cluster',
        'note_cluster_members' => 'Cluster-Zugehörigkeiten',
        'saved_searches' => 'Gespeicherte Suchanfragen',
        'user_graph_settings' => 'Benutzer-Graph-Einstellungen',
        'daily_stats' => 'Tägliche Nutzungsstatistiken'
    ];
    
    foreach ($tables as $table => $description) {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "✅ $table - $description\n";
            } else {
                echo "❌ $table - Nicht gefunden\n";
            }
        } catch (PDOException $e) {
            echo "❓ $table - Konnte nicht überprüft werden\n";
        }
    }
    
    echo "\n🚀 Dein Second Brain System ist bereit!\n";
    echo "================================================\n";
    echo "Nächste Schritte:\n";
    echo "1. Frontend-Components implementieren\n";
    echo "2. API-Endpoints erstellen\n";
    echo "3. Graph-Visualisierung einrichten\n";
    echo "4. Tag-Filter implementieren\n\n";
    
} catch (Exception $e) {
    echo "💥 Kritischer Fehler: " . $e->getMessage() . "\n";
    exit(1);
}

/**
 * Hilfsfunktion: Zeigt Tabellen-Schema an
 */
function showTableSchema($pdo, $tableName) {
    try {
        echo "\n📋 Schema für Tabelle '$tableName':\n";
        echo str_repeat("-", 50) . "\n";
        
        $stmt = $pdo->query("DESCRIBE $tableName");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($columns as $column) {
            echo sprintf("%-20s %-15s %s\n", 
                $column['Field'], 
                $column['Type'], 
                $column['Extra']
            );
        }
        echo "\n";
        
    } catch (PDOException $e) {
        echo "Fehler beim Anzeigen des Schemas: " . $e->getMessage() . "\n";
    }
}

// Beispiel-Aufruf zum Anzeigen einzelner Tabellen-Schemas:
// showTableSchema($pdo, 'notes');
// showTableSchema($pdo, 'note_links');

?>
