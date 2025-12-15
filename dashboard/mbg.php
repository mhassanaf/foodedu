<?php
require_once '../auth/check_session.php';
$user = getCurrentUser();

// Ensure only mbg can access
if ($user['role'] !== 'mbg') {
    header('Location: ../auth.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard MBG - FoodEdu</title>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../main.css">
    <style>
        /* =========================================
           MODERN DASHBOARD STYLES (REFINED)
           ========================================= */
        :root {
            --primary: #27AE60;
            --primary-dark: #219150;
            --surface: #ffffff;
            --bg-body: #f4f6f8;
            --text-main: #202124;
            --text-sub: #5f6368;
            --border: #e0e0e0;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
            --radius-md: 12px;
            --radius-lg: 16px;
        }

        body {
            background-color: var(--bg-body);
        }

        .dashboard-container {
            max-width: 1280px;
            margin: 40px auto;
            padding: 0 24px;
        }

        .dashboard-header {
            margin-bottom: 32px;
            text-align: left;
            animation: fadeInDown 0.6s ease-out;
        }

        .dashboard-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .dashboard-header p {
            color: var(--text-sub);
            font-size: 15px;
            max-width: 600px;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 48px;
            align-items: start;
        }

        @media (max-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        /* PANELS */
        .panel {
            background: var(--surface);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            padding: 24px;
            border: 1px solid var(--border);
            transition: box-shadow 0.3s ease;
        }

        .panel:hover {
            box-shadow: var(--shadow-md);
        }

        /* SIDEBAR INFO */
        .sidebar-info p {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
            color: var(--text-sub);
        }

        .sidebar-info strong {
            color: var(--text-main);
        }

        .role-badge {
            background: #e6f4ea;
            color: var(--primary);
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .quick-links {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .quick-link-btn {
            display: block;
            text-align: center;
            padding: 12px;
            border-radius: 10px;
            font-weight: 500;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .quick-link-primary {
            background: var(--primary);
            color: white;
        }

        .quick-link-primary:hover {
            background: var(--primary-dark);
            box-shadow: 0 4px 12px rgba(39, 174, 96, 0.2);
        }

        .quick-link-secondary {
            background: #f1f3f4;
            color: var(--text-main);
        }

        .quick-link-secondary:hover {
            background: #e8eaed;
        }

        /* TABS */
        .tab-container {
            display: flex;
            background: #f1f3f4;
            border-radius: 14px;
            padding: 4px;
            margin-bottom: 32px;
            position: relative;
        }

        .tab-btn {
            flex: 1;
            padding: 12px;
            border: none;
            background: transparent;
            font-weight: 600;
            color: var(--text-sub);
            cursor: pointer;
            z-index: 2;
            transition: color 0.3s;
            font-size: 14px;
            border-radius: 10px;
        }

        .tab-btn.active {
            color: var(--primary);
            background: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
        }

        /* FORM */
        .form-info-box {
            background: #e6f4ea;
            color: #1e8e3e;
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .form-section-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
        }

        /* Responsive Input Grid */
        .input-grid-responsive {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-sub);
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .input-group input,
        .input-group select,
        .input-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #eee;
            border-radius: 10px;
            font-size: 14px;
            color: var(--text-main);
            transition: border-color 0.2s;
            font-family: inherit;
        }

        .input-group input:focus,
        .input-group select:focus,
        .input-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
        }

        .nutrition-box {
            background: #fafafa;
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }

        /* LIST ITEMS */
        .list-container {
            margin-top: 32px;
            border-top: 1px solid #eee;
            padding-top: 24px;
        }

        .admin-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            border: 1px solid #eee;
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 12px;
            transition: all 0.2s;
        }

        @media (max-width: 600px) {
            .admin-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .admin-item-actions {
                width: 100%;
                justify-content: flex-end;
            }
        }

        .admin-item:hover {
            border-color: #c6f7a3;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }

        .item-info {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .item-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #f0f0f0;
        }

        .item-details h4 {
            margin: 0;
            font-size: 15px;
            font-weight: 600;
            color: var(--text-main);
        }

        .item-details span {
            font-size: 12px;
            color: var(--text-sub);
        }

        .item-actions {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
        }

        .btn-edit {
            background: #e8f0fe;
            color: #1967d2;
        }

        .btn-edit:hover {
            background: #d2e3fc;
        }

        .btn-delete {
            background: #fce8e6;
            color: #c5221f;
        }

        .btn-delete:hover {
            background: #fad2cf;
        }

        /* Animation Key frames */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .hidden-tab {
            display: none !important;
        }

        .visible-tab {
            display: block !important;
            animation: fadeIn 0.4s ease;
        }
    </style>
</head>

<body>
    <header class="navbar-container">
        <div class="navbar-inner">
            <a href="../index.html" class="logo">
                <img src="../asset/logo/foodedu.png" alt="FoodEdu Logo">
            </a>
            <nav class="nav-menu">
                <a href="../index.html" class="nav-item">Beranda</a>
                <div class="dropdown">
                    <a class="nav-item dropdown-toggle">Program <span class="arrow"></span></a>
                    <div class="dropdown-menu">
                        <a href="../gizi.html#informasi-gizi">Informasi Gizi</a>
                        <a href="../gizi.html#kelayakan">Edukasi Kelayakan</a>
                    </div>
                </div>
                <a href="../pengaduan.php" class="nav-item">Pengaduan</a>
                <a href="../saran.php" class="nav-item">Saran</a>
                <div class="nav-buttons">
                    <button class="btn-login" onclick="location.href='../auth.html'">Login</button>
                    <button class="btn-signup" onclick="location.href='../auth.html'">Sign Up</button>
                </div>
            </nav>
            <button class="hamburger" id="hamburger"><span></span><span></span><span></span></button>
        </div>
    </header>

    <main class="dashboard-container">
        <div class="dashboard-header">
            <h1>Dashboard Pihak MBG</h1>
            <p>Kelola konten edukasi gizi & kelayakan makanan secara terpusat.</p>
        </div>

        <div class="dashboard-grid">
            <!-- Sidebar -->
            <div class="panel">
                <h3 style="margin-bottom: 20px; font-size: 16px;">Profil Akun</h3>
                <div class="sidebar-info">
                    <p><span>Nama</span> <strong><?php echo htmlspecialchars($user['name']); ?></strong></p>
                    <p><span>Username</span> <strong><?php echo htmlspecialchars($user['username']); ?></strong></p>
                    <p><span>Role</span> <span class="role-badge">MBG</span></p>
                </div>
                <hr style="margin: 24px 0; border-top: 1px solid #eee;">
                <h3 style="margin-bottom: 16px; font-size: 16px;">Quick Actions</h3>
                <div class="quick-links">
                    <a href="../gizi.html" class="quick-link-btn quick-link-primary">Lihat Halaman Gizi</a>
                    <a href="../kelayakan.html" class="quick-link-btn quick-link-secondary">Lihat Edukasi Kelayakan</a>
                    <a href="../pengaduan.php" class="quick-link-btn quick-link-secondary">Cek Pengaduan</a>
                </div>
            </div>

            <!-- Content Manager -->
            <div class="panel" style="min-height: 800px;">
                <h2 style="margin-bottom: 24px; font-size: 20px;">Manajemen Konten</h2>

                <div class="tab-container">
                    <button id="btnTabGizi" class="tab-btn active">Informasi Gizi</button>
                    <button id="btnTabKel" class="tab-btn">Edukasi Kelayakan</button>
                </div>

                <!-- GIZI SECTION -->
                <div id="sectionGizi" class="visible-tab">
                    <form id="giziForm">
                        <input type="hidden" id="giziId" name="id" value="">
                        <input type="hidden" name="action" value="create_gizi">

                        <div class="input-grid-responsive">
                            <div class="input-group">
                                <label>Nama Makanan</label>
                                <input type="text" id="giziName" name="name" placeholder="Misal: Nasi Putih" required>
                            </div>
                            <div class="input-group">
                                <label>Slug URL</label>
                                <input type="text" id="giziSlug" name="slug" placeholder="Misal: nasi-putih" required>
                            </div>
                        </div>

                        <div class="input-grid-responsive">
                            <div class="input-group">
                                <label>Kategori Warna</label>
                                <select id="giziColor" name="color">
                                    <option value="green">Hijau</option>
                                    <option value="orange">Oranye</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label>Urutan</label>
                                <input type="number" id="giziSort" name="sort_order" placeholder="0">
                            </div>
                        </div>

                        <div class="nutrition-box">
                            <h4 class="form-section-title">Detail Nutrisi (Opsional)</h4>
                            <div class="input-group" style="margin-bottom: 20px;">
                                <label>Deskripsi Singkat</label>
                                <textarea id="giziDesc" name="description" rows="2"></textarea>
                            </div>

                            <!-- Responsive Grid for Nutrition inputs -->
                            <div class="input-grid-responsive"
                                style="grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));">
                                <div class="input-group">
                                    <label>Porsi</label>
                                    <input type="text" id="giziPortion" name="portion_size" placeholder="100g">
                                </div>
                                <div class="input-group">
                                    <label>Energi</label>
                                    <input type="text" id="giziEnergy" name="energy" placeholder="kcal">
                                </div>
                                <div class="input-group">
                                    <label>Lemak</label>
                                    <input type="text" id="giziFat" name="fat" placeholder="gram">
                                </div>
                                <div class="input-group">
                                    <label>Protein</label>
                                    <input type="text" id="giziProt" name="protein" placeholder="gram">
                                </div>
                            </div>

                            <div class="input-grid-responsive"
                                style="grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));">
                                <div class="input-group">
                                    <label>Karbohidrat</label>
                                    <input type="text" id="giziCarb" name="carbs" placeholder="gram">
                                </div>
                                <div class="input-group">
                                    <label>Natrium</label>
                                    <input type="text" id="giziSod" name="sodium" placeholder="mg">
                                </div>
                                <div class="input-group">
                                    <label>Kalsium</label>
                                    <input type="text" id="giziCalc" name="calcium" placeholder="mg">
                                </div>
                            </div>
                        </div>

                        <div class="input-group" style="margin-bottom: 24px;">
                            <label>Upload Gambar</label>
                            <input type="file" id="giziFile" name="image_file" accept="image/*">
                            <input type="hidden" id="giziImageUrl" name="image_url">
                        </div>

                        <div style="display: flex; gap: 12px; justify-content: flex-end;">
                            <button type="button" class="action-btn" id="btnResetGizi"
                                style="background:#f0f0f0;">Reset</button>
                            <button type="submit" class="action-btn"
                                style="background:var(--primary); color:white;">Simpan</button>
                        </div>
                    </form>

                    <div class="list-container">
                        <h3 style="font-size: 18px; margin-bottom: 16px;">Daftar Item (<span id="countGizi">0</span>)
                        </h3>
                        <div id="listGizi"></div>
                    </div>
                </div>

                <!-- KELAYAKAN SECTION -->
                <div id="sectionKel" class="hidden-tab">
                    <form id="kelForm">
                        <input type="hidden" id="kelId" name="id" value="">
                        <input type="hidden" name="action" value="create_kelayakan">

                        <div class="input-group" style="margin-bottom: 24px;">
                            <label>Nama Makanan</label>
                            <input type="text" id="kelFood" name="food_name" placeholder="Contoh: Roti" required>
                        </div>

                        <div class="input-grid-responsive">
                            <!-- Good -->
                            <div
                                style="background:#f9fff9; padding: 16px; border: 1px solid #d0ebd0; border-radius: 12px;">
                                <h4 style="color:var(--primary); margin-bottom:12px;">Layak Konsumsi (Good)</h4>
                                <div class="input-group">
                                    <label>Judul</label>
                                    <input type="text" id="kelGoodTitle" name="good_title" required>
                                </div>
                                <div class="input-group" style="margin-top:12px;">
                                    <label>Gambar</label>
                                    <input type="file" id="kelGoodFile" name="good_image_file">
                                    <input type="hidden" id="kelGoodUrl" name="good_image_url">
                                </div>
                                <div class="input-group" style="margin-top:12px;">
                                    <label>Poin Ciri-ciri</label>
                                    <textarea id="kelGoodPoints" name="good_points" rows="3"></textarea>
                                </div>
                            </div>
                            <!-- Bad -->
                            <div
                                style="background:#fff9f9; padding: 16px; border: 1px solid #ebd0d0; border-radius: 12px;">
                                <h4 style="color:#d32f2f; margin-bottom:12px;">Tidak Layak (Bad)</h4>
                                <div class="input-group">
                                    <label>Judul</label>
                                    <input type="text" id="kelBadTitle" name="bad_title" required>
                                </div>
                                <div class="input-group" style="margin-top:12px;">
                                    <label>Gambar</label>
                                    <input type="file" id="kelBadFile" name="bad_image_file">
                                    <input type="hidden" id="kelBadUrl" name="bad_image_url">
                                </div>
                                <div class="input-group" style="margin-top:12px;">
                                    <label>Poin Ciri-ciri</label>
                                    <textarea id="kelBadPoints" name="bad_points" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="input-group" style="width: 150px; margin-bottom: 24px;">
                            <label>Urutan Tampil</label>
                            <input type="number" id="kelSort" name="sort_order" placeholder="0">
                        </div>

                        <div style="display: flex; gap: 12px; justify-content: flex-end;">
                            <button type="button" class="action-btn" id="btnResetKel"
                                style="background:#f0f0f0;">Reset</button>
                            <button type="submit" class="action-btn"
                                style="background:var(--primary); color:white;">Simpan</button>
                        </div>
                    </form>

                    <div class="list-container">
                        <h3 style="font-size: 18px; margin-bottom: 16px;">Daftar Item (<span id="countKel">0</span>)
                        </h3>
                        <div id="listKel"></div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <script src="../main.js"></script>
    <script>
        // --- API HELPERS ---
        async function apiGet(type) {
            try {
                const res = await fetch(`../api/gizi_content.php?type=${type}`);
                return await res.json();
            } catch (e) { console.error(e); return { success: false }; }
        }
        async function apiPost(formData) {
            try {
                const res = await fetch('../api/gizi_content.php', { method: 'POST', body: formData });
                return await res.json();
            } catch (e) { return { success: false, message: e.message }; }
        }

        // --- DOM ELEMENTS ---
        const btnTabGizi = document.getElementById('btnTabGizi');
        const btnTabKel = document.getElementById('btnTabKel');
        const sectionGizi = document.getElementById('sectionGizi');
        const sectionKel = document.getElementById('sectionKel');

        // --- TABS LOGIC ---
        function setTab(mode) {
            if (mode === 'gizi') {
                btnTabGizi.classList.add('active');
                btnTabKel.classList.remove('active');
                sectionGizi.className = 'visible-tab';
                sectionKel.className = 'hidden-tab';
            } else {
                btnTabKel.classList.add('active');
                btnTabGizi.classList.remove('active');
                sectionKel.className = 'visible-tab';
                sectionGizi.className = 'hidden-tab';
            }
        }
        btnTabGizi.onclick = () => setTab('gizi');
        btnTabKel.onclick = () => setTab('kel');

        // --- GIZI LOGIC ---
        const giziForm = document.getElementById('giziForm');
        const listGizi = document.getElementById('listGizi');
        let giziData = [];

        async function loadGizi() {
            const data = await apiGet('gizi');
            giziData = data.items || [];
            document.getElementById('countGizi').innerText = giziData.length;
            renderGizi();
        }

        function renderGizi() {
            listGizi.innerHTML = '';
            giziData.forEach(item => {
                const div = document.createElement('div');
                div.className = 'admin-item';
                div.innerHTML = `
                    <div class="item-info">
                        <img src="../${item.image_url}" class="item-img" onerror="this.src='../asset/logo/foodedu.png'">
                        <div class="item-details">
                            <h4>${item.name}</h4>
                            <span>Slug: ${item.slug} | Sort: ${item.sort_order} | 
                                <b style="color:${item.color === 'green' ? 'green' : 'orange'}">${item.color.toUpperCase()}</b>
                            </span>
                        </div>
                    </div>
                    <div class="item-actions">
                        <button class="action-btn btn-edit" data-id="${item.id}">✏️ Edit</button>
                        <button class="action-btn btn-delete" data-id="${item.id}">🗑️ Hapus</button>
                    </div>
                `;
                listGizi.appendChild(div);
            });
        }

        // --- KELAYAKAN LOGIC ---
        const kelForm = document.getElementById('kelForm');
        const listKel = document.getElementById('listKel');
        let kelData = [];

        async function loadKel() {
            const data = await apiGet('kelayakan');
            kelData = data.items || [];
            document.getElementById('countKel').innerText = kelData.length;
            renderKel();
        }

        function renderKel() {
            listKel.innerHTML = '';
            kelData.forEach(item => {
                const div = document.createElement('div');
                div.className = 'admin-item';
                div.innerHTML = `
                    <div class="item-info">
                        <div style="display:flex;">
                            <img src="../${item.good_image_url}" class="item-img" style="border-color:green;">
                            <img src="../${item.bad_image_url}" class="item-img" style="margin-left:-15px; border-color:red;">
                        </div>
                        <div class="item-details" style="margin-left:12px;">
                            <h4>${item.food_name}</h4>
                            <span>Good: ${item.good_title} | Bad: ${item.bad_title}</span>
                        </div>
                    </div>
                    <div class="item-actions">
                        <button class="action-btn btn-edit" data-id="${item.id}">✏️ Edit</button>
                        <button class="action-btn btn-delete" data-id="${item.id}">🗑️ Hapus</button>
                    </div>
                `;
                listKel.appendChild(div);
            });
        }

        // --- GLOBAL EVENT DELEGATION (Fix buttons not working) ---
        // GIZI EVENTS
        listGizi.addEventListener('click', async (e) => {
            const btn = e.target.closest('button');
            if (!btn) return;
            const id = btn.getAttribute('data-id');
            const item = giziData.find(x => x.id == id);

            if (btn.classList.contains('btn-delete')) {
                if (confirm('Hapus item ini?')) {
                    const fd = new FormData();
                    fd.append('action', 'delete_gizi');
                    fd.append('id', id);
                    await apiPost(fd);
                    loadGizi();
                }
            }
            if (btn.classList.contains('btn-edit')) {
                // Populate Form
                document.getElementById('giziId').value = item.id;
                document.getElementById('giziName').value = item.name;
                document.getElementById('giziSlug').value = item.slug;
                document.getElementById('giziColor').value = item.color;
                document.getElementById('giziSort').value = item.sort_order;
                document.getElementById('giziImageUrl').value = item.image_url;

                document.getElementById('giziDesc').value = item.description || '';
                document.getElementById('giziPortion').value = item.portion_size || '';
                document.getElementById('giziEnergy').value = item.energy || '';
                document.getElementById('giziFat').value = item.fat || '';
                document.getElementById('giziProt').value = item.protein || '';
                document.getElementById('giziCarb').value = item.carbs || '';
                document.getElementById('giziSod').value = item.sodium || '';
                document.getElementById('giziCalc').value = item.calcium || '';

                giziForm.scrollIntoView({ behavior: 'smooth' });
                giziForm.style.background = '#eef';
                setTimeout(() => giziForm.style.background = 'transparent', 500);
            }
        });

        giziForm.onsubmit = async (e) => {
            e.preventDefault();
            const fd = new FormData(giziForm);
            const id = document.getElementById('giziId').value;
            fd.append('action', id ? 'update_gizi' : 'create_gizi');
            const res = await apiPost(fd);
            if (res.success) {
                alert('Berhasil!');
                giziForm.reset();
                document.getElementById('giziId').value = '';
                loadGizi();
            } else {
                alert('Gagal: ' + res.message);
            }
        };

        document.getElementById('btnResetGizi').onclick = () => {
            giziForm.reset();
            document.getElementById('giziId').value = '';
        };

        // KELAYAKAN EVENTS
        listKel.addEventListener('click', async (e) => {
            const btn = e.target.closest('button');
            if (!btn) return;
            const id = btn.getAttribute('data-id');
            const item = kelData.find(x => x.id == id);

            if (btn.classList.contains('btn-delete')) {
                if (confirm('Hapus edukasi ini?')) {
                    const fd = new FormData();
                    fd.append('action', 'delete_kelayakan');
                    fd.append('id', id);
                    await apiPost(fd);
                    loadKel();
                }
            }
            if (btn.classList.contains('btn-edit')) {
                document.getElementById('kelId').value = item.id;
                document.getElementById('kelFood').value = item.food_name;
                document.getElementById('kelSort').value = item.sort_order;

                document.getElementById('kelGoodTitle').value = item.good_title;
                document.getElementById('kelGoodUrl').value = item.good_image_url;
                document.getElementById('kelGoodPoints').value = item.good_points;

                document.getElementById('kelBadTitle').value = item.bad_title;
                document.getElementById('kelBadUrl').value = item.bad_image_url;
                document.getElementById('kelBadPoints').value = item.bad_points;

                kelForm.scrollIntoView({ behavior: 'smooth' });
            }
        });

        kelForm.onsubmit = async (e) => {
            e.preventDefault();
            const fd = new FormData(kelForm);
            const id = document.getElementById('kelId').value;
            fd.append('action', id ? 'update_kelayakan' : 'create_kelayakan');
            const res = await apiPost(fd);
            if (res.success) {
                alert('Berhasil!');
                kelForm.reset();
                document.getElementById('kelId').value = '';
                loadKel();
            } else {
                alert('Gagal: ' + res.message);
            }
        };

        document.getElementById('btnResetKel').onclick = () => {
            kelForm.reset();
            document.getElementById('kelId').value = '';
        };

        // INIT
        loadGizi();
        loadKel();

    </script>
</body>

</html>