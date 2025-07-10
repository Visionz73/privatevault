<table class="fe-list">
  <thead><tr><th>Name</th><th>Datum</th><th>Aktionen</th></tr></thead>
  <tbody>
  <?php foreach($files as $f): ?>
    <tr>
      <td><?=htmlspecialchars($f['filename'])?></td>
      <td><?=date('d.m.Y', strtotime($f['upload_date']))?></td>
      <td>
        <a href="/public/uploads/<?=urlencode($f['filename'])?>" download>🔽</a>
        <a href="?delete=<?=$f['id']?>" onclick="return confirm('Löschen?');">❌</a>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
