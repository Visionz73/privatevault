// public/assets/js/file-explorer.js
// AJAX-driven File Explorer frontend logic

document.addEventListener('DOMContentLoaded', () => {
  const categoryMap = {
    all: 'Alle Dateien',
    documents: 'Dokumente',
    images: 'Bilder',
    music: 'Musik',
    videos: 'Videos',
    archives: 'Archive',
    other: 'Sonstige'
  };
  const iconMap = {
    all: 'fas fa-th',
    documents: 'fas fa-file-alt text-blue-400',
    images: 'fas fa-image text-green-400',
    music: 'fas fa-music text-purple-400',
    videos: 'fas fa-video text-pink-400',
    archives: 'fas fa-file-archive text-yellow-400',
    other: 'fas fa-file text-gray-400'
  };

  let currentCategory = 'all';
  let currentSearch = '';
  let currentSort = 'upload_date_DESC';
  let currentView = 'grid';

  const elems = {
    search: document.getElementById('searchInput'),
    categoryList: document.getElementById('categoryList'),
    sort: document.getElementById('sortSelect'),
    gridBtn: document.getElementById('gridViewBtn'),
    listBtn: document.getElementById('listViewBtn'),
    totalCount: document.getElementById('totalCount'),
    grid: document.getElementById('fileGrid'),
    list: document.getElementById('fileList')
  };

  // Debounce helper
  function debounce(fn, delay) {
    let timeout;
    return (...args) => {
      clearTimeout(timeout);
      timeout = setTimeout(() => fn.apply(this, args), delay);
    };
  }

  // Fetch files from API
  async function fetchFiles() {
    const params = new URLSearchParams({
      category: currentCategory,
      search: currentSearch,
      sort: currentSort.split('_')[0],
      order: currentSort.split('_')[1]
    });
    try {
      const res = await fetch('/api/file-explorer.php?' + params.toString());
      const json = await res.json();
      if (json.status !== 'success') return;
      renderCategories(json.stats);
      elems.totalCount.textContent = json.stats.all || 0;
      renderFiles(json.files);
    } catch (e) {
      console.error('Fehler beim Laden der Dateien', e);
    }
  }

  // Render category navigation
  function renderCategories(stats) {
    elems.categoryList.innerHTML = '';
    Object.keys(categoryMap).forEach(key => {
      const btn = document.createElement('button');
      btn.className = `nav-item flex items-center gap-3 text-white/80 hover:text-white text-sm ${currentCategory===key?'active':''}`;
      btn.innerHTML = `
        <i class="${iconMap[key]} w-4 text-base"></i>
        <span>${categoryMap[key]}</span>
        <span class="ml-auto text-xs bg-white/10 px-2 py-1 rounded-full">${stats[key]||0}</span>
      `;
      btn.addEventListener('click', () => {
        currentCategory = key;
        fetchFiles();
      });
      elems.categoryList.appendChild(btn);
    });
  }

  // Render file cards or list
  function renderFiles(files) {
    elems.grid.innerHTML = '';
    elems.list.innerHTML = '';
    if (currentView === 'grid') {
      elems.grid.classList.remove('hidden');
      elems.list.classList.add('hidden');
      files.forEach(file => {
        const card = document.createElement('div');
        card.className = 'file-card';
        card.innerHTML = `
          <div class="flex items-center justify-between">
            <div>
              <p class="text-white font-semibold truncate">${file.title}</p>
              <p class="text-white/60 text-sm truncate">${file.filename}</p>
            </div>
            <p class="text-white/80 text-sm">${file.formatted_size}</p>
          </div>
          <p class="text-white/60 text-xs mt-2">${new Date(file.upload_date).toLocaleString()}</p>
        `;
        card.addEventListener('click', () => {
          window.location.href = '/download.php?id=' + file.id;
        });
        elems.grid.appendChild(card);
      });
    } else {
      elems.grid.classList.add('hidden');
      elems.list.classList.remove('hidden');
      files.forEach(file => {
        const item = document.createElement('div');
        item.className = 'liquid-glass flex items-center justify-between p-4 mb-2';
        item.innerHTML = `
          <div class="flex items-center gap-3">
            <i class="fas fa-file text-white/80 w-6"></i>
            <div>
              <p class="text-white font-medium truncate">${file.title}</p>
              <p class="text-white/60 text-sm truncate">${file.filename}</p>
            </div>
          </div>
          <div class="text-white/80 text-sm mr-4">${file.formatted_size}</div>
        `;
        item.addEventListener('click', () => {
          window.location.href = '/download.php?id=' + file.id;
        });
        elems.list.appendChild(item);
      });
    }
  }

  // Event listeners
  elems.search.addEventListener('input', debounce((e) => {
    currentSearch = e.target.value;
    fetchFiles();
  }, 300));
  elems.sort.addEventListener('change', (e) => {
    currentSort = e.target.value;
    fetchFiles();
  });
  elems.gridBtn.addEventListener('click', () => {
    currentView = 'grid';
    elems.gridBtn.classList.add('active');
    elems.listBtn.classList.remove('active');
    renderFiles(currentFiles);
  });
  elems.listBtn.addEventListener('click', () => {
    currentView = 'list';
    elems.listBtn.classList.add('active');
    elems.gridBtn.classList.remove('active');
    renderFiles(currentFiles);
  });

  // Track last fetched files for view toggling
  let currentFiles = [];
  const originalRenderFiles = renderFiles;
  renderFiles = (files) => {
    currentFiles = files;
    originalRenderFiles(files);
  };

  // Initial load
  fetchFiles();
});
