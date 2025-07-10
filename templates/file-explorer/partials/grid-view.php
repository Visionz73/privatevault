<div class="fe-grid">
  <?php foreach($files as $f): ?>
    <div class="fe-card">
      <div class="fe-icon">📄</div>
      <div class="fe-name"><?=htmlspecialchars($f['filename'])?></div>
      <div class="fe-date"><?=date('d.m.Y', strtotime($f['upload_date']))?></div>
      <div class="fe-actions">
        <a href="/public/uploads/<?=urlencode($f['filename'])?>" download>🔽</a>
        <a href="?delete=<?=$f['id']?>" onclick="return confirm('Löschen?');">❌</a>
      </div>
    </div>
  <?php endforeach; ?>
</div>
