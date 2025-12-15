/* =========================================================
   FOODEDU - Dynamic Gizi & Kelayakan Content
   - Menampilkan konten gizi & kelayakan dari backend (api/gizi_content.php)
   - Dipakai di index.html, indexsiswaorangtua.html, gizi.html, kelayakan.html
   - Animasi smooth & aesthetic, tidak mengganggu konten lama
========================================================= */

(function () {
  'use strict';

  const qs = (sel, ctx) => (ctx || document).querySelector(sel);
  const qsa = (sel, ctx) => Array.from((ctx || document).querySelectorAll(sel));

  // Store items for search functionality
  let allGiziItems = [];

  async function fetchContent(type) {
    try {
      const BASE = (location.protocol === 'file:' ? '/' : (location.origin + '/'));
      const ABS = (p) => (p.startsWith('http') ? p : BASE + p.replace(/^\//, ''));
      const url = type ? ABS(`api/gizi_content.php?type=${encodeURIComponent(type)}&t=${Date.now()}`) : ABS('api/gizi_content.php?t=' + Date.now());
      const res = await fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        cache: 'no-cache'
      });
      if (!res.ok) throw new Error('Gagal memuat konten');
      const json = await res.json();
      if (!json.success) throw new Error(json.message || 'Gagal memuat konten');
      return json;
    } catch (e) {
      console.error('[gizi-content] Error:', e);
      return null;
    }
  }

  function createGiziCard(item, index) {
    const a = document.createElement('a');
    a.className = `gizi-card ${item.color === 'orange' ? 'orange' : 'green'} reveal`;
    // Stagger animation based on index
    a.style.animationDelay = `${0.05 * index}s`;

    // Dynamic Link
    a.href = item.slug ? `detail_gizi.php?slug=${item.slug}` : '#';

    const img = document.createElement('img');
    img.src = item.image_url;
    img.alt = item.name || 'Makanan';
    // CIRCULAR STYLE modification
    img.style.borderRadius = '50%';
    img.style.width = '120px'; // Set fixed size for circle
    img.style.height = '120px';
    img.style.objectFit = 'cover';
    img.style.display = 'block';
    img.style.margin = '0 auto 16px auto'; // Center image

    const title = document.createElement('h3');
    title.textContent = item.name || 'Item Gizi';

    a.appendChild(img);
    a.appendChild(title);
    return a;
  }

  function createKelayakanRow(item, index) {
    const article = document.createElement('article');
    article.className = `kelayakan-row ${index % 2 === 0 ? 'reveal-left' : 'reveal-right'}`;
    article.style.animationDelay = `${0.07 * index}s`;

    const h2 = document.createElement('h2');
    h2.className = 'kelayakan-food-title';
    h2.textContent = item.food_name || 'Makanan';
    article.appendChild(h2);

    const cols = document.createElement('div');
    cols.className = 'kelayakan-columns';

    // Helper membuat list <ul> dari teks poin (dipisah newline)
    const makeList = (text) => {
      const ul = document.createElement('ul');
      const lines = (text || '').split(/\r?\n/).map((s) => s.trim()).filter(Boolean);
      if (!lines.length) return ul;
      lines.forEach((line) => {
        const li = document.createElement('li');
        li.textContent = line;
        ul.appendChild(li);
      });
      return ul;
    };

    const good = document.createElement('div');
    good.className = 'kelayakan-card kelayakan-good';
    // CIRCULAR STYLE for inner images
    // We inject inline style for img
    good.innerHTML = `
      <h3>${item.good_title || 'Layak Dikonsumsi'}</h3>
      <div class="kelayakan-image-wrap" style="display:flex;justify-content:center;margin-bottom:16px;">
        <img src="${item.good_image_url}" alt="${item.good_title || ''}" 
             style="border-radius:50%; width:150px; height:150px; object-fit:cover;">
      </div>
    `;
    good.appendChild(makeList(item.good_points));

    const bad = document.createElement('div');
    bad.className = 'kelayakan-card kelayakan-bad';
    bad.innerHTML = `
      <h3>${item.bad_title || 'Tidak Layak Dikonsumsi'}</h3>
      <div class="kelayakan-image-wrap" style="display:flex;justify-content:center;margin-bottom:16px;">
        <img src="${item.bad_image_url}" alt="${item.bad_title || ''}" 
             style="border-radius:50%; width:150px; height:150px; object-fit:cover;">
      </div>
    `;
    bad.appendChild(makeList(item.bad_points));

    cols.appendChild(good);
    cols.appendChild(bad);
    article.appendChild(cols);
    return article;
  }

  function hydrateGiziGrid(items) {
    const grid = qs('[data-gizi-grid]');
    if (!grid) return;

    grid.innerHTML = '';

    if (!Array.isArray(items) || !items.length) {
      grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: #666; padding: 40px;">Tidak ada data ditemukan.</div>';
      return;
    }

    items.forEach((item, idx) => {
      grid.appendChild(createGiziCard(item, idx));
    });

    requestAnimationFrame(() => {
      qsa('.reveal, .reveal-left, .reveal-right', grid).forEach(el => el.classList.add('active'));
    });
  }

  function hydrateKelayakanSection(items) {
    const container = qs('[data-kelayakan-grid]');
    if (!container || !Array.isArray(items) || !items.length) return;
    container.innerHTML = '';
    items.forEach((item, idx) => {
      container.appendChild(createKelayakanRow(item, idx));
    });
    requestAnimationFrame(() => {
      qsa('.reveal, .reveal-left, .reveal-right', container).forEach(el => el.classList.add('active'));
    });
  }

  function hydrateIndexPreview(json) {
    const wrap = qs('[data-gizi-preview]');
    if (!wrap || !json) return;

    const gizi = json.gizi || [];
    const kel = json.kelayakan || [];

    wrap.innerHTML = '';

    const block = document.createElement('div');
    block.className = 'gizi-preview-wrapper reveal';

    const header = document.createElement('div');
    header.className = 'gizi-preview-header';
    header.innerHTML = `
      <h2>Informasi Gizi & Kelayakan Makanan</h2>
      <p>Cuplikan konten edukatif dari halaman program gizi dan kelayakan makanan. Klik untuk melihat detail lengkap.</p>
      <div class="gizi-preview-actions">
        <a href="gizi.html#informasi-gizi" class="btn-primary">Lihat Informasi Gizi</a>
        <a href="gizi.html#kelayakan" class="btn-secondary">Lihat Kelayakan Makanan</a>
      </div>
    `;
    block.appendChild(header);

    const row = document.createElement('div');
    row.className = 'gizi-preview-row';

    const left = document.createElement('div');
    left.className = 'gizi-preview-col';
    left.innerHTML = '<h3>Contoh Makanan</h3>';
    const giziContainer = document.createElement('div');
    giziContainer.className = 'gizi-preview-cards';
    gizi.slice(0, 4).forEach((item, idx) => {
      giziContainer.appendChild(createGiziCard(item, idx));
    });
    left.appendChild(giziContainer);

    const right = document.createElement('div');
    right.className = 'gizi-preview-col';
    right.innerHTML = '<h3>Edukasi Kelayakan</h3>';
    const kelContainer = document.createElement('div');
    kelContainer.className = 'gizi-preview-kelayakan';
    kel.slice(0, 2).forEach((item, idx) => {
      kelContainer.appendChild(createKelayakanRow(item, idx));
    });
    right.appendChild(kelContainer);

    row.appendChild(left);
    row.appendChild(right);
    block.appendChild(row);
    wrap.appendChild(block);
    requestAnimationFrame(() => {
      qsa('.reveal, .reveal-left, .reveal-right', wrap).forEach(el => el.classList.add('active'));
    });
  }

  function initSearch() {
    const searchInput = document.getElementById('giziSearchInput');
    if (!searchInput) return;

    searchInput.addEventListener('input', (e) => {
      const term = e.target.value.toLowerCase().trim();

      const filtered = allGiziItems.filter(item => {
        const name = (item.name || '').toLowerCase();
        const slug = (item.slug || '').toLowerCase();
        return name.includes(term) || slug.includes(term);
      });

      hydrateGiziGrid(filtered);
    });
  }

  async function initGiziContent() {
    const isIndex = !!qs('[data-gizi-preview]');
    const hasGiziGrid = !!qs('[data-gizi-grid]');
    const hasKelayakan = !!qs('[data-kelayakan-grid]');

    if (!isIndex && !hasGiziGrid && !hasKelayakan) return;

    if (isIndex) {
      const json = await fetchContent('all');
      if (json) {
        hydrateIndexPreview(json);
      }
    }

    if (hasGiziGrid) {
      const json = await fetchContent('gizi');
      if (json) {
        // STORE GLOBAL ITEMS
        allGiziItems = json.items || [];
        hydrateGiziGrid(allGiziItems);
        // Init Search AFTER data is loaded
        initSearch();
      }
    }

    if (hasKelayakan) {
      const json = await fetchContent('kelayakan');
      if (json) {
        hydrateKelayakanSection(json.items || []);
      }
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initGiziContent);
  } else {
    initGiziContent();
  }
})();
