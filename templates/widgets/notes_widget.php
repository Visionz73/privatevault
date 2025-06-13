<?php
require_once __DIR__.'/../../src/lib/auth.php';
requireLogin();
require_once __DIR__.'/../../src/lib/db.php';

// Check if notes table exists
try {
    $stmt = $pdo->prepare("
        SELECT n.*, u.username as creator_name
        FROM notes n
        LEFT JOIN users u ON n.created_by = u.id
        WHERE n.user_id = ? 
        ORDER BY n.updated_at DESC 
        LIMIT 5
    ");
    $stmt->execute([$user['id']]);
    $recentNotes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total note count
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM notes WHERE user_id = ?");
    $stmt->execute([$user['id']]);
    $noteCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (PDOException $e) {
    // Notes table doesn't exist
    $recentNotes = [];
    $noteCount = 0;
}
?>

<article class="widget-card p-6 flex flex-col">
  <div class="flex justify-between items-center mb-4">
    <a href="notes.php" class="group inline-flex items-center widget-header">
      <h2 class="mr-1">Notizen</h2>
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
      </svg>
    </a>
    
    <button onclick="openNoteCreator()" class="widget-button text-sm flex items-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      Notiz
    </button>
  </div>
  
  <p class="widget-description mb-4"><?= $noteCount ?> Notizen gespeichert</p>

  <div class="widget-scroll-container flex-1">
    <div class="widget-scroll-content space-y-2">
      <?php if (!empty($recentNotes)): ?>
        <?php foreach ($recentNotes as $note): ?>
          <div class="widget-list-item" onclick="openNote(<?= $note['id'] ?>)">
            <div class="flex justify-between items-start">
              <div class="flex-1 min-w-0">
                <div class="task-title text-sm truncate">
                  <?= htmlspecialchars($note['title'] ?? 'Unbenannte Notiz') ?>
                </div>
                <?php if (!empty($note['content'])): ?>
                  <div class="task-description text-xs truncate">
                    <?= htmlspecialchars(mb_strimwidth(strip_tags($note['content']), 0, 60, "...")) ?>
                  </div>
                <?php endif; ?>
                <div class="task-meta text-xs">
                  <?= date('d.m.Y H:i', strtotime($note['updated_at'])) ?>
                </div>
              </div>
              <div class="flex-shrink-0 text-right">
                <span class="status-badge bg-blue-100 text-blue-800 px-1 py-0.5 rounded-full text-xs">
                  <?= !empty($note['category']) ? htmlspecialchars($note['category']) : 'Allgemein' ?>
                </span>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="widget-list-item text-center task-meta py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
          </svg>
          Keine Notizen gefunden.
          <button onclick="openNoteCreator()" 
                  class="block mx-auto mt-2 text-blue-400 hover:text-blue-300 text-xs">
            Erste Notiz erstellen
          </button>
        </div>
      <?php endif; ?>
    </div>
  </div>
</article>

<script>
function openNoteCreator() {
    // Redirect to notes page or open inline creator
    window.location.href = 'notes.php?action=create';
}

function openNote(noteId) {
    // Open specific note
    window.location.href = 'notes.php?id=' + noteId;
}
</script>
