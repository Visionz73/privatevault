document.querySelectorAll('.fe-toggle button').forEach(btn=>{
  btn.onclick=e=>{
    const v=btn.dataset.view;
    const url=new URL(window.location);
    url.searchParams.set('view',v);
    window.location.href=url.toString();
  };
});
