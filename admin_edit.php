<?php
// admin_edit.php — Edit Product (price, quantity, variant, etc.)
// Phone & Accessory Price Lookup System

require_once 'config.php';

// ── Load product by ID ────────────────────────────────────────
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: admin.php');
    exit;
}

try {
    $pdo  = getDB();
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch();
} catch (PDOException $e) {
    die('Database error. Could not load product.');
}

if (!$product) {
    header('Location: admin.php');
    exit;
}

// ── Helper: escape for HTML output ───────────────────────────
function h($str) { return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8'); }
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Edit Product — PhoneStore Admin</title>
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
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body { min-height: 100vh; background: var(--bg); color: var(--text); font-family: 'DM Sans',sans-serif; }
body::before {
  content: '✏️'; position: fixed; inset: 0; display: flex;
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
.nav { width: 100%; max-width: 680px; display: flex; align-items: center; gap: 12px; margin-bottom: 36px; }
.back-link {
  display: inline-flex; align-items: center; gap: 6px;
  color: var(--muted); text-decoration: none; font-size: 0.85rem;
  border: 1px solid var(--border); border-radius: 50px; padding: 6px 14px;
  transition: color .2s, border-color .2s;
}
.back-link:hover { color: var(--accent); border-color: var(--accent); }
header { text-align: center; margin-bottom: 36px; animation: fadeDown .5s ease both; }
header h1 {
  font-family: 'Syne',sans-serif; font-size: clamp(1.6rem,4vw,2.4rem);
  font-weight: 800; letter-spacing: -.03em;
  background: linear-gradient(135deg,var(--accent),var(--accent2));
  -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
header p { color: var(--muted); font-size: .9rem; margin-top: 6px; }
/* Current name banner */
.current-name-banner {
  width: 100%; max-width: 680px;
  background: var(--surface2); border: 1px solid var(--border);
  border-radius: 14px; padding: 16px 20px;
  margin-bottom: 24px; animation: fadeDown .5s .05s ease both;
}
.current-name-banner .label { font-size: .75rem; color: var(--muted); letter-spacing: .05em; text-transform: uppercase; margin-bottom: 6px; }
.current-name-banner .name { font-family: 'Syne',sans-serif; font-size: 1.1rem; font-weight: 700; color: var(--text); }
.current-name-banner .price-row { margin-top: 8px; font-size: .85rem; color: var(--muted); display: flex; gap: 16px; flex-wrap: wrap; }
.current-name-banner .price-val { color: var(--success); font-family: 'Syne',sans-serif; font-weight: 700; font-size: 1rem; }
/* Card */
.card {
  width: 100%; max-width: 680px;
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 20px; padding: 36px 32px;
  box-shadow: 0 8px 40px rgba(0,0,0,.5);
  animation: fadeDown .5s .1s ease both;
}
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
.field input.highlight-price { border-color: rgba(52,211,153,.4); }
.field input.highlight-price:focus { border-color: var(--success); box-shadow: 0 0 0 3px rgba(52,211,153,.12); }
.field select option { background: var(--surface2); }
.field-note { font-size: .75rem; color: var(--muted); margin-top: 5px; }
.row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
/* Preview */
.preview-label { font-size: .78rem; color: var(--muted); margin-bottom: 6px; letter-spacing: .04em; text-transform: uppercase; }
.preview-box {
  background: var(--surface2); border: 1.5px dashed rgba(79,140,255,.3);
  border-radius: 10px; padding: 14px 16px;
  font-family: 'Syne',sans-serif; font-size: 1.05rem; font-weight: 700;
  color: var(--accent); letter-spacing: -.01em; min-height: 50px;
  display: flex; align-items: center;
}
.divider { height: 1px; background: var(--border); margin: 28px 0; }
/* Price highlight box */
.price-section {
  background: rgba(52,211,153,.05);
  border: 1.5px solid rgba(52,211,153,.15);
  border-radius: 14px; padding: 20px;
  margin-bottom: 20px;
}
.price-section-title {
  font-family: 'Syne',sans-serif; font-size: .8rem; font-weight: 700;
  letter-spacing: .05em; text-transform: uppercase;
  color: var(--success); margin-bottom: 14px;
  display: flex; align-items: center; gap: 8px;
}
/* Buttons */
.btn-row { display: flex; gap: 12px; }
.btn-save {
  flex: 1; padding: 15px;
  background: linear-gradient(135deg,var(--accent),var(--accent2));
  border: none; border-radius: 50px; color: #fff;
  font-family: 'Syne',sans-serif; font-size: 1rem; font-weight: 700; letter-spacing: .02em;
  cursor: pointer; transition: opacity .2s, transform .15s, box-shadow .2s;
  box-shadow: 0 4px 24px rgba(79,140,255,.25);
}
.btn-save:hover { opacity: .9; transform: translateY(-1px); }
.btn-save:disabled { opacity: .5; cursor: not-allowed; }
.btn-back {
  padding: 15px 28px;
  background: var(--surface2); border: 1px solid var(--border);
  border-radius: 50px; color: var(--muted);
  font-family: 'DM Sans',sans-serif; font-size: .95rem;
  cursor: pointer; transition: color .2s, border-color .2s; text-decoration: none;
  display: inline-flex; align-items: center;
}
.btn-back:hover { color: var(--text); border-color: rgba(255,255,255,.2); }
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
@media (max-width: 520px) {
  .card { padding: 24px 16px; }
  .row-2 { grid-template-columns: 1fr; gap: 0; }
  .btn-row { flex-direction: column; }
}
</style>
</head>
<body>

<div class="watermark-text" aria-hidden="true">EDIT</div>

<div class="page">

  <div class="nav">
    <a href="admin.php" class="back-link">← Back to Admin</a>
  </div>

  <header>
    <h1>✏️ Edit Product</h1>
    <p>Update price, quantity, or any product detail</p>
  </header>

  <!-- Current product banner -->
  <div class="current-name-banner">
    <div class="label">Currently saved as</div>
    <div class="name"><?= h($product['full_name']) ?></div>
    <div class="price-row">
      <span>Price: <span class="price-val">D <?= number_format($product['price'], 0) ?></span></span>
      <span>Qty: <?= (int)$product['quantity'] ?></span>
      <span>Category: <?= h($product['category']) ?></span>
    </div>
  </div>

  <div class="card">

    <!-- Category -->
    <div class="field">
      <label for="category">Category <span class="req">*</span></label>
      <select id="category">
        <option value="iPhone"      <?= $product['category']==='iPhone'      ? 'selected' : '' ?>>iPhone</option>
        <option value="Android"     <?= $product['category']==='Android'     ? 'selected' : '' ?>>Android</option>
        <option value="Accessories" <?= $product['category']==='Accessories' ? 'selected' : '' ?>>Accessories</option>
      </select>
    </div>

    <!-- Brand -->
    <div class="field">
      <label for="brand">Brand <span class="req">*</span></label>
      <input type="text" id="brand" value="<?= h($product['brand']) ?>" placeholder="Apple, Samsung…" />
    </div>

    <!-- Series (phones) -->
    <div class="field" id="seriesField" <?= $product['category']==='Accessories' ? 'style="display:none"' : '' ?>>
      <label for="series">Series</label>
      <input type="text" id="series" value="<?= h($product['series']) ?>" placeholder="iPhone 13, Galaxy S23…" />
    </div>

    <!-- Type (accessories) -->
    <div class="field" id="typeField" <?= $product['category']!=='Accessories' ? 'style="display:none"' : '' ?>>
      <label for="type">Type</label>
      <input type="text" id="type" value="<?= h($product['type']) ?>" placeholder="Power Bank, Charger…" />
    </div>

    <!-- Variant -->
    <div class="field">
      <label for="variant">Variant</label>
      <input type="text" id="variant" value="<?= h($product['variant']) ?>" placeholder="Pro Max, 20000mAh…" />
    </div>

    <!-- Full name preview -->
    <div class="field">
      <div class="preview-label">New Full Name (auto-generated)</div>
      <div class="preview-box" id="fullNamePreview"><?= h($product['full_name']) ?></div>
      <p class="field-note">Updates live as you type — saved automatically on submit.</p>
    </div>

    <div class="divider"></div>

    <!-- Price section — highlighted -->
    <div class="price-section">
      <div class="price-section-title">💰 Price & Stock</div>
      <div class="row-2">
        <div class="field" style="margin-bottom:0">
          <label for="price">Price (D) <span class="req">*</span></label>
          <input type="number" id="price" class="highlight-price" value="<?= h($product['price']) ?>" min="0" step="0.01" />
        </div>
        <div class="field" style="margin-bottom:0">
          <label for="quantity">Quantity <span class="req">*</span></label>
          <input type="number" id="quantity" value="<?= h($product['quantity']) ?>" min="0" step="1" />
        </div>
      </div>
    </div>

    <!-- Keywords -->
    <div class="field">
      <label for="keywords">Keywords <span style="font-weight:300;text-transform:none;letter-spacing:0">(optional)</span></label>
      <input type="text" id="keywords" value="<?= h($product['keywords']) ?>" placeholder="iphone13 apple pro max" />
    </div>

    <div class="divider"></div>

    <div class="btn-row">
      <a href="admin.php" class="btn-back">Cancel</a>
      <button class="btn-save" id="saveBtn">💾 Save Changes</button>
    </div>

  </div>
</div>

<div class="toast" id="toast"></div>

<script>
(function(){
  const PRODUCT_ID   = <?= (int)$product['id'] ?>;
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
  const saveBtn      = document.getElementById('saveBtn');
  const toast        = document.getElementById('toast');

  let toastTimer = null;
  function showToast(msg, type){
    toast.textContent = (type==='success'?'✅ ':'❌ ') + msg;
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

  /* live preview */
  function updatePreview(){
    const brand   = brandEl.value.trim();
    const series  = seriesEl.value.trim();
    const type    = typeEl.value.trim();
    const variant = variantEl.value.trim();
    const cat     = categoryEl.value;
    const middle  = (cat==='Accessories') ? type : series;
    const parts   = [brand, middle, variant].filter(Boolean);
    preview.textContent = parts.length > 0 ? parts.join(' ') : '—';
  }
  [brandEl, seriesEl, typeEl, variantEl].forEach(function(el){ el.addEventListener('input', updatePreview); });

  /* save */
  saveBtn.addEventListener('click', function(){
    const category = categoryEl.value;
    const brand    = brandEl.value.trim();
    const price    = priceEl.value.trim();

    if(!brand)  { showToast('Brand is required.','error'); return; }
    if(!price || isNaN(price) || parseFloat(price) <= 0){ showToast('A valid price is required.','error'); return; }

    const fd = new FormData();
    fd.append('id',       PRODUCT_ID);
    fd.append('category', category);
    fd.append('brand',    brand);
    fd.append('series',   seriesEl.value.trim());
    fd.append('type',     typeEl.value.trim());
    fd.append('variant',  variantEl.value.trim());
    fd.append('price',    price);
    fd.append('quantity', quantityEl.value.trim()||'0');
    fd.append('keywords', keywordsEl.value.trim());

    saveBtn.disabled = true; saveBtn.textContent = 'Saving…';

    const xhr = new XMLHttpRequest();
    xhr.open('POST','admin_edit_save.php',true);
    xhr.onload = function(){
      saveBtn.disabled = false; saveBtn.textContent = '💾 Save Changes';
      try{
        const res = JSON.parse(xhr.responseText);
        if(res.success){
          showToast(res.message,'success');
          /* update banner */
          document.querySelector('.current-name-banner .name').textContent = res.full_name;
          document.querySelector('.current-name-banner .price-val').textContent =
            'D ' + Number(res.price).toLocaleString('en-US');
          setTimeout(function(){ window.location.href='admin.php'; }, 1500);
        } else {
          const msg = res.errors ? res.errors.join(' ') : (res.error||'Unknown error.');
          showToast(msg,'error');
        }
      } catch(e){ showToast('Server response error.','error'); }
    };
    xhr.onerror = function(){ saveBtn.disabled=false; saveBtn.textContent='💾 Save Changes'; showToast('Network error.','error'); };
    xhr.send(fd);
  });

})();
</script>
</body>
</html>
