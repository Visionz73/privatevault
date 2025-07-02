let currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
function toggleTheme(){
  currentTheme = currentTheme==='light'?'dark':'light';
  document.documentElement.setAttribute('data-theme', currentTheme);
}
function switchView(view){
  document.getElementById('gridView').style.display = view==='grid'?'grid':'none';
  document.getElementById('listView').style.display = view==='list'?'table':'none';
  // optional: URL anpassen
}
function filterFiles(){
  const term = document.getElementById('liveSearch').value.toLowerCase();
  document.querySelectorAll('.file-card, .file-list tr').forEach(el=>{
    const name = (el.dataset.filename||el.textContent).toLowerCase();
    el.style.display = name.includes(term)?'' :'none';
  });
}
function previewFile(el){
  const name = el.dataset.filename;
  openPreview(name);
}
function previewFileElement(btn){
  const tr = btn.closest('tr');
  const name = tr.cells[0].textContent;
  openPreview(name);
}
function openPreview(filename){
  const content = document.getElementById('previewContent');
  content.innerHTML = '';
  const ext = filename.split('.').pop().toLowerCase();
  if(ext==='pdf'){
    // PDF.js
    const canvas = document.createElement('canvas');
    content.appendChild(canvas);
    const loading = pdfjsLib.getDocument(`/uploads/${filename}`);
    loading.promise.then(doc=> doc.getPage(1).then(page=>{
      const viewport = page.getViewport({scale:1.2});
      canvas.height = viewport.height; canvas.width = viewport.width;
      page.render({canvasContext: canvas.getContext('2d'), viewport});
    }));
  } else if(['jpg','png','gif','jpeg','webp'].includes(ext)){
    const img = document.createElement('img');
    img.src = `/uploads/${filename}`; img.style.maxWidth='100%';
    content.appendChild(img);
  } else {
    content.textContent = 'Vorschau nicht verfügbar.';
  }
  document.getElementById('previewBackdrop').style.display='flex';
}
function closePreview(){
  document.getElementById('previewBackdrop').style.display='none';
}
function deleteFile(id){
  if(confirm('Löschen?')) window.location.search='delete='+id;
}
document.addEventListener('DOMContentLoaded', ()=>{
  // initial view
  switchView('<?= $currentView ?>');
});
