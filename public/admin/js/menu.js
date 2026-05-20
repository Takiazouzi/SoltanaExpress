(function() {
  'use strict';
  const API = '/admin/menu-items.php';
  let items = [];
  const tbody = document.getElementById('table-body');
  const drawerTitle = document.getElementById('drawer-title');
  const drawerContent = document.getElementById('drawer-content');
  const btnSave = document.getElementById('btn-save');
  const uz = document.getElementById('upload-zone-dynamic');

  async function load() {
    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:24px;color:var(--text-muted)">Loading...</td></tr>';
    try {
      const res = await fetch(API + '?action=list', {credentials:'same-origin'});
      const d = await res.json();
      if (d.success) { items = d.data; render(items); } else throw new Error(d.message);
    } catch(e) { tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:24px;color:#DC2626">Error: ${e.message}</td></tr>`; }
  }

  function render(data) {
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:24px;color:var(--text-muted)">No items found</td></tr>'; return; }
    tbody.innerHTML = data.map(i => `<tr data-id="${i.id}">
      <td style="color:var(--text-muted)">${i.id}</td>
      <td>${i.image_path ? `<img src="${i.image_path}" style="width:40px;height:40px;object-fit:cover;border-radius:6px;border:1px solid var(--border)">` : '<div style="width:40px;height:40px;background:#F3F4F6;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#9CA3AF"><i class="ti ti-photo"></i></div>'}</td>
      <td><div style="font-weight:500">${i.name}</div><div style="font-size:12px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px">${i.description||'—'}</div></td>
      <td><span style="background:#F3F4F6;padding:3px 8px;border-radius:4px;font-size:12px">${i.category}</span></td>
      <td style="text-align:right;font-weight:500">$${parseFloat(i.price).toFixed(2)}</td>
      <td><div class="status-toggle ${i.available?'active':''}" onclick="window.toggle(${i.id}, this)"><div class="toggle-track"><div class="toggle-thumb"></div></div><span class="status-label">${i.available?'Active':'Hidden'}</span></div></td>
      <td class="actions">
        <button class="btn-icon" onclick="window.edit(${i.id})" data-tooltip="Edit"><i class="ti ti-edit"></i></button>
        <button class="btn-icon danger" onclick="window.del(${i.id})" data-tooltip="Delete"><i class="ti ti-trash"></i></button>
      </td>
    </tr>`).join('');
  }

  window.toggle = function(id, el) {
    el.classList.toggle('active'); const a = el.classList.contains('active');
    el.querySelector('.status-label').textContent = a ? 'Active' : 'Hidden';
    const it = items.find(x=>x.id===id); if(it) { it.available = a;
      fetch(API, {method:'POST',credentials:'same-origin',body:new URLSearchParams({action:'update',id:id,name:it.name,description:it.description||'',price:it.price,category:it.category,available:a?1:0,image_path:it.image_path||''})});
    }
  };

  window.edit = function(id) {
    const it = items.find(x=>x.id===id); if(!it) return;
    drawerTitle.textContent = 'Edit Item';
    renderDrawer(it);
    openDrawer();
  };

  function renderDrawer(it = {}) {
    drawerContent.innerHTML = `
      <form id="item-form">
        <input type="hidden" id="item-id" name="id" value="${it.id||''}">
        <div class="form-group"><label class="form-label">Name</label><input type="text" id="item-name" name="name" class="input" value="${it.name||''}" required></div>
        <div class="form-group"><label class="form-label">Description</label><textarea id="item-desc" name="description" class="textarea">${it.description||''}</textarea></div>
        <div class="form-grid">
          <div class="form-group" style="margin:0"><label class="form-label">Price ($)</label><input type="number" id="item-price" name="price" class="input" step="0.01" min="0" value="${it.price||''}" required></div>
          <div class="form-group" style="margin:0"><label class="form-label">Category</label><select id="item-category" name="category" class="select" required><option value="">Select...</option><option ${it.category==='Starters'?'selected':''}>Starters</option><option ${it.category==='Mains'?'selected':''}>Mains</option><option ${it.category==='Desserts'?'selected':''}>Desserts</option></select></div>
        </div>
        <div class="form-group"><label class="form-label">Availability</label><div class="status-toggle ${it.available==1?'active':''}" id="status-toggle"><div class="toggle-track"><div class="toggle-thumb"></div></div><span class="status-label">${it.available==1?'Active':'Hidden'}</span></div><input type="hidden" id="item-available" name="available" value="${it.available??1}"></div>
        <div class="form-group"><label class="form-label">Image</label><div class="upload-zone ${it.image_path?'has-image':''}" id="upload-zone-dynamic" style="background-image:url('${it.image_path||''}')"><input type="file" id="item-image" name="image" accept="image/jpeg,image/png,image/webp"><div class="upload-placeholder" style="position:relative;z-index:1"><i class="ti ti-upload" style="font-size:24px;margin-bottom:4px;display:block"></i>Click or drag image here<br><small style="opacity:0.7">JPG, PNG, WebP • Max 2MB</small></div></div></div>
      </form>`;
    
    document.getElementById('status-toggle')?.addEventListener('click', function() {
      this.classList.toggle('active');
      document.getElementById('item-available').value = this.classList.contains('active') ? 1 : 0;
    });
    setupUploadPreview();
  }

  function setupUploadPreview() {
    const uz = document.getElementById('upload-zone-dynamic');
    if(!uz) return;
    uz.addEventListener('dragover', e => { e.preventDefault(); uz.classList.add('dragover'); });
    uz.addEventListener('dragleave', () => uz.classList.remove('dragover'));
    uz.addEventListener('drop', e => { e.preventDefault(); uz.classList.remove('dragover'); if(e.dataTransfer.files[0]) handleFile(e.dataTransfer.files[0]); });
    document.getElementById('item-image')?.addEventListener('change', e => { if(e.target.files[0]) handleFile(e.target.files[0]); });
  }
  function handleFile(f) { if(f.type.startsWith('image/')) { const r=new FileReader(); r.onload=ev => { uz.classList.add('has-image'); uz.style.backgroundImage=`url(${ev.target.result})`; }; r.readAsDataURL(f); } }

  document.getElementById('btn-add')?.addEventListener('click', () => {
    drawerTitle.textContent = 'New Menu Item';
    renderDrawer({});
    openDrawer();
  });

  btnSave?.addEventListener('click', async function() {
    this.textContent='Saving...'; this.disabled=true;
    const form = document.getElementById('item-form');
    const fd = new FormData(form);
    const id = fd.get('id');
    fd.set('action', id ? 'update' : 'create');
    fd.set('available', document.getElementById('item-available').value);
    try {
      const res = await fetch(API, {method:'POST',credentials:'same-origin',body:fd});
      const d = await res.json();
      if(d.success) {
        if(id && document.getElementById('item-image')?.files[0]) {
          const fd2 = new FormData(); fd2.set('action','upload_image'); fd2.set('id',id); fd2.set('image',document.getElementById('item-image').files[0]);
          await fetch(API, {method:'POST',credentials:'same-origin',body:fd2});
        }
        closeDrawer(); load();
      } else alert(d.message);
    } catch(e) { alert('Error: '+e.message); }
    this.textContent='Save'; this.disabled=false;
  });

  window.del = async function(id) { if(!confirm('Delete this item?')) return; await fetch(API,{method:'POST',credentials:'same-origin',body:new URLSearchParams({action:'delete',id:id})}); load(); };

  const filter = () => { const q=document.getElementById('search-input').value.toLowerCase(), c=document.getElementById('filter-cat').value; render(items.filter(i=>i.name.toLowerCase().includes(q)&&(!c||i.category===c))); };
  document.getElementById('search-input')?.addEventListener('input',filter);
  document.getElementById('filter-cat')?.addEventListener('change',filter);

  document.addEventListener('DOMContentLoaded', load);
})();
