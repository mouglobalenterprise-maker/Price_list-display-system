<?php
// admin.php — Admin Panel (Add + List + Edit + Delete)
// Phone & Accessory Price Lookup System
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Admin Panel — PhoneStore</title>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
<style>
:root {
  --bg:       #0d0f14;
  --surface:  #161a22;
  --surface2: #1e2330;
  --border:   rgba(255,255,255,0.07);
  --accent:   #4f8cff;
  --accent2:  #a78bfa;
  --text:     #e8eaf0;
  --muted:    #7a8299;
  --success:  #34d399;
  --error:    #f87171;
  --warning:  #fbbf24;
  --radius:   14px;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body { min-height: 100vh; background: var(--bg); color: var(--text); font-family: 'DM Sans', sans-serif; }
body::before {
  content: '⚙️'; position: fixed; inset: 0; display: flex;
  align-items: center; justify-content: center;
  font-size: clamp(200px,40vw,400px); opacity: 0.025;
  pointer-events: none; z-index: 0; filter: grayscale(1);
}
.watermark-text {
  position: fixed; top: 50%; left: 50%;
  transform: translate(-50%,-50%) rotate(-20deg);
  font-family: 'Syne',sans-serif; font-size: clamp(50px,12vw,140px);
  font-weight: 800; color: rgba(255,255,255,0.02);
  white-space: nowrap; pointer-events: none; z-index: 0; user-select: none;
}
.page {
  position: relative; z-index: 1; min-height: 100vh;
  display: flex; flex-direction: column; align-items: center;
  padding: 48px 20px 100px;
}
/* Nav */
.nav { width: 100%; max-width: 960px; display: flex; align-items: center; gap: 12px; margin-bottom: 36px; }
.back-link {
  display: inline-flex; align-items: center; gap: 6px;
  color: var(--muted); text-decoration: none; font-size: 0.85rem;
  border: 1px solid var(--border); border-radius: 50px; padding: 6px 14px;
  transition: color .2s, border-color .2s;
}
.back-link:hover { color: var(--accent); border-color: var(--accent); }
/* Header */
header { text-align: center; margin-bottom: 40px; animation: fadeDown .5s ease both; }
header h1 {
  font-family: 'Syne',sans-serif; font-size: clamp(1.6rem,4vw,2.4rem);
  font-weight: 800; letter-spacing: -0.03em;
  background: linear-gradient(135deg,var(--accent),var(--accent2));
  -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
header p { color: var(--muted); font-size: .9rem; margin-top: 6px; }
/* Section title */
.section-title {
  width: 100%; max-width: 960px;
  font-family: 'Syne',sans-serif; font-size: .9rem; font-weight: 700;
  letter-spacing: .05em; text-transform: uppercase; color: var(--muted);
  margin-bottom: 14px; display: flex; align-items: center; gap: 10px;
}
.section-title::after { content:''; flex:1; height:1px; background: var(--border); }
/* Card */
.card {
  width: 100%; max-width: 960px;
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 20px; padding: 36px 32px;
  box-shadow: 0 8px 40px rgba(0,0,0,.5); margin-bottom: 40px;
  animation: fadeDown .5s .1s ease both;
}
/* Fields */
.field { margin-bottom: 20px; }
.field label {
  display: block; font-size: .8rem; font-weight: 500; color: var(--muted);
  letter-spacing: .04em; text-transform: uppercase; margin-bottom: 8px;
}
.field label .req { color: var(--accent); }
.field input, .field select {
  width: 100%; padding: 13px 16px; background: var(--surface2);
  border: 1.5px solid var(--border); border-radius: 10px;
  color: var(--text); font-family: 'DM Sans',sans-serif; font-size: .97rem;
  outline: none; transition: border-color .2s, box-shadow .2s;
}
.field input::placeholder { color: var(--muted); }
.field input:focus, .field select:focus {
  border-color: var(--accent); box-shadow: 0 0 0 3px rgba(79,140,255,.12);
}
.field select option { background: var(--surface2); }
.field-note { font-size: .75rem; color: var(--muted); margin-top: 5px; }
.row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
/* Preview */
.preview-label { font-size: .78rem; color: var(--muted); margin-bottom: 6px; letter-spacing: .04em; text-transform: uppercase; }
.preview-box {
  background: var(--surface2); border: 1.5px dashed rgba(79,140,255,.3);
  border-radius: 10px; padding: 14px 16px;
  font-family: 'Syne',sans-serif; font-size: 1.05rem; font-weight: 700;
  color: var(--accent); letter-spacing: -.01em; min-height: 50px;
  display: flex; align-items: center;
}
.preview-box.empty { color: var(--muted); font-family: 'DM Sans',sans-serif; font-weight: 400; font-size: .88rem; }
.divider { height: 1px; background: var(--border); margin: 28px 0; }
/* Buttons */
.btn-submit {
  width: 100%; padding: 15px;
  background: linear-gradient(135deg,var(--accent),var(--accent2));
  border: none; border-radius: 50px; color: #fff;
  font-family: 'Syne',sans-serif; font-size: 1rem; font-weight: 700; letter-spacing: .02em;
  cursor: pointer; transition: opacity .2s, transform .15s, box-shadow .2s;
  box-shadow: 0 4px 24px rgba(79,140,255,.25);
}
.btn-submit:hover { opacity: .9; transform: translateY(-1px); }
.btn-submit:active { transform: translateY(0); }
.btn-submit:disabled { opacity: .5; cursor: not-allowed; }
/* Table */
.table-wrap { width: 100%; max-width: 960px; overflow-x: auto; animation: fadeDown .5s .2s ease both; }
.table-toolbar { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; flex-wrap: wrap; }
.table-search {
  flex: 1; min-width: 200px; padding: 10px 16px;
  background: var(--surface); border: 1.5px solid var(--border);
  border-radius: 50px; color: var(--text); font-family: 'DM Sans',sans-serif;
  font-size: .9rem; outline: none; transition: border-color .2s;
}
.table-search:focus { border-color: var(--accent); }
.table-search::placeholder { color: var(--muted); }
.filter-select {
  padding: 10px 14px; background: var(--surface); border: 1.5px solid var(--border);
  border-radius: 50px; color: var(--text); font-family: 'DM Sans',sans-serif;
  font-size: .85rem; outline: none; cursor: pointer; transition: border-color .2s;
}
.filter-select:focus { border-color: var(--accent); }
.filter-select option { background: var(--surface2); }
.product-count { font-size: .82rem; color: var(--muted); white-space: nowrap; }
.product-count span { color: var(--accent); font-weight: 500; }
table { width: 100%; border-collapse: collapse; background: var(--surface); border-radius: 16px; overflow: hidden; box-shadow: 0 8px 40px rgba(0,0,0,.4); }
thead tr { background: var(--surface2); border-bottom: 1px solid var(--border); }
thead th {
  padding: 14px 16px; text-align: left;
  font-family: 'Syne',sans-serif; font-size: .75rem; font-weight: 700;
  letter-spacing: .06em; text-transform: uppercase; color: var(--muted); white-space: nowrap;
}
tbody tr { border-bottom: 1px solid var(--border); transition: background .15s; }
tbody tr:last-child { border-bottom: none; }
tbody tr:hover { background: var(--surface2); }
tbody td { padding: 13px 16px; font-size: .9rem; vertical-align: middle; }
.td-name { font-weight: 500; max-width: 280px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.td-price { font-family: 'Syne',sans-serif; font-weight: 700; color: var(--success); white-space: nowrap; }
.cat-badge { display: inline-block; padding: 3px 10px; border-radius: 50px; font-size: .72rem; font-weight: 500; white-space: nowrap; }
.cat-iphone      { background: rgba(79,140,255,.12); color: #4f8cff; }
.cat-android     { background: rgba(52,211,153,.12); color: #34d399; }
.cat-accessories { background: rgba(167,139,250,.12); color: #a78bfa; }
.stock-dot { display: inline-flex; align-items: center; gap: 5px; font-size: .82rem; }
.dot { width: 7px; height: 7px; border-radius: 50%; }
.dot-green { background: var(--success); }
.dot-yellow { background: var(--warning); }
.dot-red { background: var(--error); }
.actions { display: flex; gap: 8px; align-items: center; }
.btn-edit, .btn-delete {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 6px 14px; border-radius: 50px; font-size: .78rem; font-weight: 500;
  cursor: pointer; border: none; transition: opacity .2s, transform .15s;
  text-decoration: none; white-space: nowrap; font-family: 'DM Sans',sans-serif;
}
.btn-edit   { background: rgba(79,140,255,.15); color: var(--accent); }
.btn-delete { background: rgba(248,113,113,.12); color: var(--error); }
.btn-edit:hover, .btn-delete:hover { opacity: .8; transform: translateY(-1px); }
.empty-row td { text-align: center; padding: 48px 16px; color: var(--muted); font-size: .9rem; }
/* Modal */
.modal-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,.75);
  display: flex; align-items: center; justify-content: center;
  z-index: 999; opacity: 0; pointer-events: none; transition: opacity .25s;
}
.modal-overlay.open { opacity: 1; pointer-events: all; }
.modal {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 20px; padding: 36px 32px; max-width: 420px; width: 90%;
  box-shadow: 0 20px 60px rgba(0,0,0,.6);
  transform: scale(.95); transition: transform .25s; text-align: center;
}
.modal-overlay.open .modal { transform: scale(1); }
.modal-icon { font-size: 2.5rem; margin-bottom: 12px; display: block; }
.modal h3 { font-family: 'Syne',sans-serif; font-size: 1.2rem; font-weight: 700; margin-bottom: 8px; }
.modal p { color: var(--muted); font-size: .9rem; margin-bottom: 16px; line-height: 1.5; }
.modal-product-name {
  color: var(--text); font-weight: 500; background: var(--surface2);
  border-radius: 8px; padding: 8px 14px; display: inline-block;
  margin: 0 0 20px; font-size: .9rem; word-break: break-word;
}
.modal-actions { display: flex; gap: 12px; justify-content: center; }
.btn-cancel {
  padding: 11px 24px; background: var(--surface2); border: 1px solid var(--border);
  border-radius: 50px; color: var(--muted); font-family: 'DM Sans',sans-serif;
  font-size: .9rem; cursor: pointer; transition: color .2s, border-color .2s;
}
.btn-cancel:hover { color: var(--text); border-color: rgba(255,255,255,.2); }
.btn-confirm-delete {
  padding: 11px 24px; background: var(--error); border: none;
  border-radius: 50px; color: #fff;
  font-family: 'Syne',sans-serif; font-size: .9rem; font-weight: 700;
  cursor: pointer; transition: opacity .2s;
}
.btn-confirm-delete:hover { opacity: .85; }
/* Toast */
.toast {
  position: fixed; bottom: 32px; left: 50%;
  transform: translateX(-50%) translateY(80px);
  background: var(--surface2); border-radius: 50px; padding: 14px 24px;
  font-size: .9rem; display: flex; align-items: center; gap: 10px;
  box-shadow: 0 8px 40px rgba(0,0,0,.5);
  transition: transform .35s cubic-bezier(.22,1,.36,1), opacity .3s;
  opacity: 0; z-index: 1000; max-width: 90vw;
}
.toast.show { transform: translateX(-50%) translateY(0); opacity: 1; }
.toast.success { border: 1px solid rgba(52,211,153,.3); color: var(--success); }
.toast.error   { border: 1px solid rgba(248,113,113,.3); color: var(--error); }
@keyframes fadeDown { from { opacity:0; transform:translateY(-16px); } to { opacity:1; transform:translateY(0); } }
@media (max-width: 640px) {
  .card { padding: 24px 16px; }
  .row-2, .row-3 { grid-template-columns: 1fr; gap: 0; }
  thead th:nth-child(3), tbody td:nth-child(3) { display: none; }
}
</style>
</head>
<body>

<div class="watermark-text" aria-hidden="true">ADMIN</div>

<div class="page">

  <div class="nav">
    <a href="index.html" class="back-link">← Back to Search</a>
  </div>

  <header>
    <h1>⚙️ Admin Panel</h1>
    <p>Add products · Edit prices · Delete listings</p>
  </header>

  <!-- ── ADD PRODUCT ─────────────────────────────────────────── -->
  <div class="section-title">➕ Add New Product</div>

  <div class="card">
    <div class="row-3">
      <div class="field">
        <label for="category">Category <span class="req">*</span></label>
        <select id="category">
          <option value="">— Select —</option>
          <option value="iPhone">iPhone</option>
          <option value="Android">Android</option>
          <option value="Accessories">Accessories</option>
        </select>
      </div>
      <div class="field">
        <label for="brand">Brand <span class="req">*</span></label>
        <input type="text" id="brand" placeholder="Apple, Samsung, Oraimo…" />
      </div>
      <div class="field">
        <label for="variant">Variant</label>
        <input type="text" id="variant" placeholder="Pro Max, 20000mAh…" />
      </div>
    </div>

    <div class="field" id="seriesField">
      <label for="series">Series <span class="req">*</span></label>
      <input type="text" id="series" placeholder="e.g. iPhone 13, Galaxy S23, Camon 20" />
      <p class="field-note">Required for phones.</p>
    </div>

    <div class="field" id="typeField" style="display:none;">
      <label for="type">Type <span class="req">*</span></label>
      <input type="text" id="type" placeholder="e.g. Power Bank, Charger, Case, Earbuds" />
      <p class="field-note">Required for accessories.</p>
    </div>

    <div class="field">
      <div class="preview-label">Auto-generated Full Name</div>
      <div class="preview-box empty" id="fullNamePreview">Fill in fields above to preview…</div>
      <p class="field-note">⚠️ Generated automatically — never typed manually.</p>
    </div>

    <div class="divider"></div>

    <div class="row-3">
      <div class="field">
        <label for="price">Price (D) <span class="req">*</span></label>
        <input type="number" id="price" placeholder="45000" min="0" step="0.01" />
      </div>
      <div class="field">
        <label for="quantity">Quantity <span class="req">*</span></label>
        <input type="number" id="quantity" placeholder="10" min="0" step="1" value="0" />
      </div>
      <div class="field">
        <label for="keywords">Keywords <span style="font-weight:300;text-transform:none;letter-spacing:0">(optional)</span></label>
        <input type="text" id="keywords" placeholder="iphone13 apple pro" />
      </div>
    </div>

    <div class="divider"></div>
    <button class="btn-submit" id="submitBtn">+ Add Product</button>
  </div>

  <!-- ── PRODUCT LIST ────────────────────────────────────────── -->
  <div class="section-title">📦 All Products</div>

  <div class="table-wrap">
    <div class="table-toolbar">
      <input type="text" class="table-search" id="tableSearch" placeholder="🔍  Filter by name…" />
      <select class="filter-select" id="filterCategory">
        <option value="">All Categories</option>
        <option value="iPhone">iPhone</option>
        <option value="Android">Android</option>
        <option value="Accessories">Accessories</option>
      </select>
      <span class="product-count" id="productCount"></span>
    </div>

    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Product Name</th>
          <th>Category</th>
          <th>Price (D)</th>
          <th>Qty</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="productTableBody">
        <tr class="empty-row"><td colspan="6">Loading products…</td></tr>
      </tbody>
    </table>
  </div>

</div>

<!-- Delete Modal -->
<div class="modal-overlay" id="deleteModal">
  <div class="modal">
    <span class="modal-icon">🗑️</span>
    <h3>Delete Product?</h3>
    <p>You are about to permanently delete:</p>
    <div class="modal-product-name" id="modalProductName">—</div>
    <p>This action cannot be undone.</p>
    <div class="modal-actions">
      <button class="btn-cancel"  id="cancelDelete">Cancel</button>
      <button class="btn-confirm-delete" id="confirmDelete">Yes, Delete</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
(function(){

  const categoryEl   = document.getElementById('category');
  const brandEl      = document.getElementById('brand');
  const seriesField  = document.getElementById('seriesField');
  const typeField    = document.getElementById('typeField');
  const seriesEl     = document.getElementById('series');
  const typeEl       = document.getElementById('type');
  const variantEl    = document.getElementById('variant');
  const priceEl      = document.getElementById('price');
  const quantityEl   = document.getElementById('quantity');
  const keywordsEl   = document.getElementById('keywords');
  const preview      = document.getElementById('fullNamePreview');
  const submitBtn    = document.getElementById('submitBtn');
  const toast        = document.getElementById('toast');
  const tableBody    = document.getElementById('productTableBody');
  const tableSearch  = document.getElementById('tableSearch');
  const filterCat    = document.getElementById('filterCategory');
  const productCount = document.getElementById('productCount');
  const deleteModal  = document.getElementById('deleteModal');
  const modalName    = document.getElementById('modalProductName');
  const cancelDelete = document.getElementById('cancelDelete');
  const confirmDelBtn= document.getElementById('confirmDelete');

  let allProducts  = [];
  let deleteTarget = null;
  let toastTimer   = null;

  /* toast */
  function showToast(msg, type) {
    toast.textContent = (type === 'success' ? '✅ ' : '❌ ') + msg;
    toast.className = 'toast ' + type;
    void toast.offsetWidth;
    toast.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function(){ toast.classList.remove('show'); }, 4000);
  }

  /* category toggle */
  categoryEl.addEventListener('change', function(){
    const isAcc = categoryEl.value === 'Accessories';
    seriesField.style.display = isAcc ? 'none' : 'block';
    typeField.style.display   = isAcc ? 'block' : 'none';
    updatePreview();
  });

  /* preview */
  function updatePreview(){
    const brand   = brandEl.value.trim();
    const series  = seriesEl.value.trim();
    const type    = typeEl.value.trim();
    const variant = variantEl.value.trim();
    const cat     = categoryEl.value;
    const middle  = (cat === 'Accessories') ? type : series;
    const parts   = [brand, middle, variant].filter(Boolean);
    if(parts.length === 0){
      preview.textContent = 'Fill in fields above to preview…';
      preview.classList.add('empty');
    } else {
      preview.textContent = parts.join(' ');
      preview.classList.remove('empty');
    }
  }
  [brandEl, seriesEl, typeEl, variantEl].forEach(function(el){ el.addEventListener('input', updatePreview); });

  /* helpers */
  function fmt(n){ return Number(n).toLocaleString('en-US'); }
  function esc(s){
    return String(s ?? '')
      .replace(/&/g,'&amp;').replace(/</g,'&lt;')
      .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }
  function stockDot(qty){
    const q = parseInt(qty,10);
    if(q === 0) return '<span class="stock-dot"><span class="dot dot-red"></span> 0</span>';
    if(q <= 3)  return `<span class="stock-dot"><span class="dot dot-yellow"></span> ${q}</span>`;
    return `<span class="stock-dot"><span class="dot dot-green"></span> ${q}</span>`;
  }
  function catBadge(cat){
    const m = { iPhone:'cat-iphone', Android:'cat-android', Accessories:'cat-accessories' };
    return `<span class="cat-badge ${m[cat]||''}">${esc(cat)}</span>`;
  }

  /* render table */
  function renderTable(products){
    productCount.innerHTML = `<span>${products.length}</span> product${products.length!==1?'s':''}`;
    if(products.length === 0){
      tableBody.innerHTML = '<tr class="empty-row"><td colspan="6">No products found.</td></tr>';
      return;
    }
    let html = '';
    products.forEach(function(p, i){
      html += `<tr>
        <td style="color:var(--muted);font-size:.8rem">${i+1}</td>
        <td class="td-name" title="${esc(p.full_name)}">${esc(p.full_name)}</td>
        <td>${catBadge(p.category)}</td>
        <td class="td-price">D ${fmt(p.price)}</td>
        <td>${stockDot(p.quantity)}</td>
        <td>
          <div class="actions">
            <a href="admin_edit.php?id=${p.id}" class="btn-edit">✏️ Edit</a>
            <button class="btn-delete" data-id="${p.id}" data-name="${esc(p.full_name)}">🗑️ Delete</button>
          </div>
        </td>
      </tr>`;
    });
    tableBody.innerHTML = html;
    tableBody.querySelectorAll('.btn-delete').forEach(function(btn){
      btn.addEventListener('click', function(){
        deleteTarget = btn.dataset.id;
        modalName.textContent = btn.dataset.name;
        deleteModal.classList.add('open');
      });
    });
  }

  /* filter */
  function applyFilters(){
    const q   = tableSearch.value.toLowerCase();
    const cat = filterCat.value;
    renderTable(allProducts.filter(function(p){
      return (!q || p.full_name.toLowerCase().includes(q)) &&
             (!cat || p.category === cat);
    }));
  }
  tableSearch.addEventListener('input',  applyFilters);
  filterCat.addEventListener('change', applyFilters);

  /* load products */
  function loadProducts(){
    const xhr = new XMLHttpRequest();
    xhr.open('GET','admin_list.php',true);
    xhr.onload = function(){
      if(xhr.status === 200){
        try{ allProducts = JSON.parse(xhr.responseText); applyFilters(); }
        catch(e){ tableBody.innerHTML='<tr class="empty-row"><td colspan="6">Error loading products.</td></tr>'; }
      }
    };
    xhr.onerror = function(){ tableBody.innerHTML='<tr class="empty-row"><td colspan="6">Network error.</td></tr>'; };
    xhr.send();
  }
  loadProducts();

  /* delete modal */
  cancelDelete.addEventListener('click', function(){ deleteModal.classList.remove('open'); deleteTarget=null; });
  deleteModal.addEventListener('click', function(e){ if(e.target===deleteModal){ deleteModal.classList.remove('open'); deleteTarget=null; } });

  confirmDelBtn.addEventListener('click', function(){
    if(!deleteTarget) return;
    confirmDelBtn.textContent = 'Deleting…';
    confirmDelBtn.disabled = true;
    const fd = new FormData();
    fd.append('id', deleteTarget);
    const xhr = new XMLHttpRequest();
    xhr.open('POST','admin_delete.php',true);
    xhr.onload = function(){
      confirmDelBtn.textContent = 'Yes, Delete';
      confirmDelBtn.disabled = false;
      deleteModal.classList.remove('open');
      try{
        const res = JSON.parse(xhr.responseText);
        if(res.success){ showToast(res.message,'success'); loadProducts(); }
        else { showToast(res.error||'Could not delete.','error'); }
      } catch(e){ showToast('Server error.','error'); }
      deleteTarget = null;
    };
    xhr.onerror = function(){ confirmDelBtn.textContent='Yes, Delete'; confirmDelBtn.disabled=false; showToast('Network error.','error'); };
    xhr.send(fd);
  });

  /* add product */
  submitBtn.addEventListener('click', function(){
    const category = categoryEl.value;
    const brand    = brandEl.value.trim();
    const series   = seriesEl.value.trim();
    const type     = typeEl.value.trim();
    const variant  = variantEl.value.trim();
    const price    = priceEl.value.trim();
    const quantity = quantityEl.value.trim();
    const keywords = keywordsEl.value.trim();

    if(!category) { showToast('Please select a category.','error'); return; }
    if(!brand)    { showToast('Brand is required.','error'); return; }
    if(category!=='Accessories' && !series){ showToast('Series is required for phones.','error'); return; }
    if(category==='Accessories' && !type)  { showToast('Type is required for accessories.','error'); return; }
    if(!price||isNaN(price)||parseFloat(price)<=0){ showToast('A valid price is required.','error'); return; }

    const fd = new FormData();
    fd.append('category', category); fd.append('brand', brand);
    fd.append('series', series);     fd.append('type', type);
    fd.append('variant', variant);   fd.append('price', price);
    fd.append('quantity', quantity||'0'); fd.append('keywords', keywords);

    submitBtn.disabled = true; submitBtn.textContent = 'Saving…';
    const xhr = new XMLHttpRequest();
    xhr.open('POST','admin_insert.php',true);
    xhr.onload = function(){
      submitBtn.disabled=false; submitBtn.textContent='+ Add Product';
      try{
        const res = JSON.parse(xhr.responseText);
        if(res.success){
          showToast(res.message,'success');
          categoryEl.value=''; brandEl.value=''; seriesEl.value='';
          typeEl.value=''; variantEl.value=''; priceEl.value='';
          quantityEl.value='0'; keywordsEl.value='';
          seriesField.style.display='block'; typeField.style.display='none';
          updatePreview(); loadProducts();
        } else {
          const msg = res.errors ? res.errors.join(' ') : (res.error||'Unknown error.');
          showToast(msg,'error');
        }
      } catch(e){ showToast('Server response error.','error'); }
    };
    xhr.onerror = function(){ submitBtn.disabled=false; submitBtn.textContent='+ Add Product'; showToast('Network error.','error'); };
    xhr.send(fd);
  });

})();
</script>
</body>
</html>
