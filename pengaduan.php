<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: auth.html');
    exit;
}

$user = [
    'id' => $_SESSION['user_id'],
    'username' => $_SESSION['username'],
    'role' => $_SESSION['role'],
    'name' => $_SESSION['name']
];

// Jika role adalah mbg, ambil data pengaduan dari database
$pengaduanList = [];
$isMBG = ($user['role'] === 'mbg');

if ($isMBG) {
    require_once __DIR__ . '/auth/config.php';

    try {
        $stmt = $pdo->query("
            SELECT 
                p.id,
                p.nama_lengkap,
                p.nama_sekolah,
                p.tanggal_kejadian,
                p.jenis_pengaduan,
                p.deskripsi,
                p.bukti_path,
                p.status,
                p.created_at
            FROM pengaduan p
            ORDER BY p.created_at DESC
        ");
        $pengaduanList = $stmt->fetchAll();
    } catch (Exception $e) {
        $pengaduanList = [];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isMBG ? 'Hasil Pengaduan - FoodEdu' : 'Form Pengaduan - FoodEdu'; ?></title>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <!-- ================= NAVBAR ================= -->
    <header class="navbar-container">
        <div class="navbar-inner">
            <!-- Logo -->
            <a href="indexsiswaorangtua.html" class="logo">
                <img src="asset/logo/foodedu.png" alt="FoodEdu Logo">
            </a>

            <!-- Navigation -->
            <nav class="nav-menu">
                <a href="indexsiswaorangtua.html" class="nav-item">Beranda</a>
                    <div class="dropdown">
                        <a class="nav-item dropdown-toggle">
                            Program <span class="arrow"></span>
                        </a>
                        <div class="dropdown-menu">
                            <a href="gizi.html#informasi-gizi">Informasi Gizi Seimbang</a>
                            <a href="gizi.html#kelayakan">Edukasi Kelayakan Makanan</a>
                        </div>
                    </div>
                <a href="pengaduan.php" class="nav-item pengaduan-link active">Pengaduan</a>
                <a href="saran.php" class="nav-item saran-link"><?php echo $isMBG ? 'Data Saran' : 'Saran'; ?></a>

                <!-- User Profile Buttons (Logged In) -->
                <div class="nav-buttons">
                    <div class="user-profile">
                        <span class="username"><?php echo htmlspecialchars($user['name']); ?></span>
                        <button class="btn-logout" id="logoutBtn">Keluar</button>
                    </div>
                </div>
            </nav>

            <!-- Hamburger -->
            <button class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>

    <!-- ================= FORM / HASIL PENGADUAN ================= -->
    <main class="pengaduan-container">
        <div class="pengaduan-card">
            <div class="pengaduan-header">
                <?php if ($isMBG): ?>
                    <h1 class="pengaduan-title">Hasil Pengaduan Pengguna</h1>
                    <p class="pengaduan-subtitle">
                        Rekap laporan pengaduan dari siswa, orang tua, dan pihak sekolah terkait program makan bergizi.
                    </p>
                <?php else: ?>
                    <h1 class="pengaduan-title">Form Pengaduan</h1>
                    <p class="pengaduan-subtitle">Sampaikan keluhan atau masukan Anda terkait program makan bergizi</p>
                <?php endif; ?>
            </div>

            <?php if ($isMBG): ?>
                <!-- Tampilan daftar pengaduan untuk MBG -->
                <section class="admin-review-wrapper">
                    <div class="admin-review-summary">
                        <div class="admin-summary-item">
                            <span class="label">Total Pengaduan</span>
                            <span class="value"><?php echo count($pengaduanList); ?></span>
                        </div>
                    </div>

                    <?php if (empty($pengaduanList)): ?>
                        <div class="admin-empty-state">
                            <h2>Tidak ada pengaduan</h2>
                            <p>Belum ada laporan yang masuk dari pengguna. Pantau kembali secara berkala.</p>
                        </div>
                    <?php else: ?>
                        <div class="admin-review-list">
                            <?php foreach ($pengaduanList as $item): ?>
                                <article class="admin-review-card reveal">
                                    <header class="admin-review-header">
                                        <div>
                                            <h3><?php echo htmlspecialchars($item['jenis_pengaduan']); ?></h3>
                                            <p class="admin-review-meta">
                                                <span><?php echo htmlspecialchars($item['nama_lengkap']); ?></span>
                                                <span>•</span>
                                                <span><?php echo htmlspecialchars($item['nama_sekolah']); ?></span>
                                            </p>
                                        </div>
                                        <div class="admin-review-status status-<?php echo htmlspecialchars($item['status']); ?>">
                                            <?php echo ucfirst(htmlspecialchars($item['status'])); ?>
                                        </div>
                                    </header>

                                    <div class="admin-review-body">
                                        <p class="admin-review-date">
                                            Tanggal kejadian: 
                                            <strong>
                                                <?php 
                                                    $tgl = $item['tanggal_kejadian'];
                                                    echo $tgl ? date('d M Y', strtotime($tgl)) : '-';
                                                ?>
                                            </strong>
                                        </p>
                                        <p class="admin-review-text">
                                            <?php echo nl2br(htmlspecialchars($item['deskripsi'])); ?>
                                        </p>

                                        <?php if (!empty($item['bukti_path'])): ?>
                                            <a href="<?php echo htmlspecialchars($item['bukti_path']); ?>" target="_blank" class="admin-review-attachment">
                                                Lihat bukti pendukung
                                            </a>
                                        <?php endif; ?>
                                    </div>

                                    <footer class="admin-review-footer">
                                        <span class="admin-review-created">
                                            Dikirim pada: 
                                            <?php 
                                                $created = $item['created_at'];
                                                echo $created ? date('d M Y H:i', strtotime($created)) : '-';
                                            ?>
                                        </span>
                                    </footer>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            <?php else: ?>
                <!-- Form pengaduan untuk pengguna biasa -->
                <form id="formPengaduan" class="pengaduan-form" enctype="multipart/form-data">
                    <!-- Nama Lengkap -->
                    <div class="form-group">
                        <label for="nama_lengkap" class="form-label">
                            Nama Lengkap <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="nama_lengkap" 
                            name="nama_lengkap" 
                            class="form-input"
                            value="<?php echo htmlspecialchars($user['name']); ?>"
                            required
                            readonly
                        >
                    </div>

                    <!-- Nama Sekolah -->
                    <div class="form-group">
                        <label for="nama_sekolah" class="form-label">
                            Nama Sekolah <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="nama_sekolah" 
                            name="nama_sekolah" 
                            class="form-input"
                            placeholder="Masukkan nama sekolah"
                            required
                        >
                    </div>

                    <!-- Tanggal Kejadian -->
                    <div class="form-group">
                        <label for="tanggal_kejadian" class="form-label">
                            Tanggal Kejadian <span class="required">*</span>
                        </label>
                        <div class="input-with-icon">
                            <input 
                                type="date" 
                                id="tanggal_kejadian" 
                                name="tanggal_kejadian" 
                                class="form-input"
                                required
                            >
                            <span class="input-icon">📅</span>
                        </div>
                    </div>

                    <!-- Jenis Pengaduan -->
                    <div class="form-group">
                        <label for="jenis_pengaduan" class="form-label">
                            Jenis Pengaduan <span class="required">*</span>
                        </label>
                        <select 
                            id="jenis_pengaduan" 
                            name="jenis_pengaduan" 
                            class="form-select"
                            required
                        >
                            <option value="">Pilih Jenis Pengaduan</option>
                            <option value="Kualitas Makanan">Kualitas Makanan</option>
                            <option value="Kebersihan Makanan">Kebersihan Makanan</option>
                            <option value="Kuantitas Makanan">Kuantitas Makanan</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <!-- Deskripsi Pengaduan -->
                    <div class="form-group">
                        <label for="deskripsi" class="form-label">
                            Deskripsi Pengaduan <span class="required">*</span>
                        </label>
                        <textarea 
                            id="deskripsi" 
                            name="deskripsi" 
                            class="form-textarea"
                            rows="5"
                            placeholder="Jelaskan secara detail keluhan atau masukan Anda..."
                            required
                        ></textarea>
                    </div>

                    <!-- Upload Bukti -->
                    <div class="form-group">
                        <label for="bukti" class="form-label">
                            Upload Bukti Pendukung
                        </label>
                        <div class="file-upload-wrapper">
                            <input 
                                type="file" 
                                id="bukti" 
                                name="bukti" 
                                class="file-input"
                                accept="image/*,.pdf"
                            >
                            <label for="bukti" class="file-label">
                                <span class="file-icon">📎</span>
                                <span class="file-text">Pilih File</span>
                                <span class="file-name" id="fileName">Tidak ada file dipilih</span>
                            </label>
                        </div>
                        <small class="form-hint">Format: JPG, PNG, atau PDF (Maks. 5MB)</small>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-actions">
                        <button type="submit" class="btn-submit" id="btnSubmit">
                            <span class="btn-text">Kirim Pengaduan</span>
                            <span class="btn-loader" style="display: none;">⏳</span>
                        </button>
                    </div>

                    <!-- Success/Error Message -->
                    <div id="formMessage" class="form-message" style="display: none;"></div>
                </form>
            <?php endif; ?>
        </div>
    </main>

    <!-- ================= FOOTER ================= -->
    <footer class="footer">
        <div class="footer-left">
            <h3>FOODEDU</h3>
            <p>
                FoodEdu adalah platform berbasis web yang dirancang sebagai media edukasi 
                dan pengumpulan laporan terkait program makan bergizi di sekolah.
            </p>
        </div>
        <div class="footer-right">
            <p><strong>Contact</strong></p>
            <p>📧 support@foodedu.id</p>
            <p>📱 @foodedu</p>
            <p>📍 Indonesia</p>
        </div>
    </footer>

    <script src="main.js"></script>

    <?php if (!$isMBG): ?>
        <script>
            // Setup logout button
            document.addEventListener('DOMContentLoaded', function() {
                const logoutBtn = document.getElementById('logoutBtn');
                if (logoutBtn) {
                    logoutBtn.addEventListener('click', function() {
                        fetch('auth/logout.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({})
                        })
                        .then(response => response.json())
                        .then(data => {
                            window.location.href = 'index.html';
                        })
                        .catch(error => {
                            console.error('Logout error:', error);
                            window.location.href = 'index.html';
                        });
                    });
                }
            });
        </script>
        <script>
            // File input handler
            const fileInput = document.getElementById('bukti');
            const fileName = document.getElementById('fileName');
            
            if (fileInput && fileName) {
                fileInput.addEventListener('change', function(e) {
                    if (e.target.files.length > 0) {
                        fileName.textContent = e.target.files[0].name;
                        fileName.style.color = 'var(--green)';
                    } else {
                        fileName.textContent = 'Tidak ada file dipilih';
                        fileName.style.color = '#999';
                    }
                });
            }

            // Form submission
            const pengaduanForm = document.getElementById('formPengaduan');
            if (pengaduanForm) {
                pengaduanForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const btnSubmit = document.getElementById('btnSubmit');
                    const btnText = btnSubmit.querySelector('.btn-text');
                    const btnLoader = btnSubmit.querySelector('.btn-loader');
                    const formMessage = document.getElementById('formMessage');
                    
                    // Disable button and show loader
                    btnSubmit.disabled = true;
                    btnText.style.display = 'none';
                    btnLoader.style.display = 'inline-block';
                    formMessage.style.display = 'none';
                    
                    // Create FormData
                    const formData = new FormData(this);
                    
                    try {
                        const response = await fetch('pengaduan/submit.php', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            formMessage.className = 'form-message success';
                            formMessage.textContent = result.message || 'Pengaduan berhasil dikirim!';
                            formMessage.style.display = 'block';
                            
                            // Reset form
                            this.reset();
                            if (fileName) {
                                fileName.textContent = 'Tidak ada file dipilih';
                                fileName.style.color = '#999';
                            }
                            
                            // Scroll to message
                            formMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        } else {
                            formMessage.className = 'form-message error';
                            formMessage.textContent = result.message || 'Terjadi kesalahan. Silakan coba lagi.';
                            formMessage.style.display = 'block';
                        }
                    } catch (error) {
                        formMessage.className = 'form-message error';
                        formMessage.textContent = 'Terjadi kesalahan koneksi. Silakan coba lagi.';
                        formMessage.style.display = 'block';
                    } finally {
                        // Enable button
                        btnSubmit.disabled = false;
                        btnText.style.display = 'inline-block';
                        btnLoader.style.display = 'none';
                    }
                });
            }

            // Logout function
            async function logout() {
                try {
                    const response = await fetch('auth/logout.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({})
                    });
                    window.location.href = 'index.html';
                } catch(e) {
                    window.location.href = 'index.html';
                }
            }
        </script>
    <?php endif; ?>
</body>
</html>

