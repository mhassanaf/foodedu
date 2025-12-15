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

// Jika role adalah mbg, ambil data saran dari database
$saranList = [];
$isMBG = ($user['role'] === 'mbg');

if ($isMBG) {
    require_once __DIR__ . '/auth/config.php';

    try {
        $stmt = $pdo->query("
            SELECT 
                s.id,
                s.nama_lengkap,
                s.nama_sekolah,
                s.saran_dan_masukan,
                s.alasan,
                s.status,
                s.created_at
            FROM saran s
            ORDER BY s.created_at DESC
        ");
        $saranList = $stmt->fetchAll();
    } catch (Exception $e) {
        $saranList = [];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isMBG ? 'Hasil Saran - FoodEdu' : 'Form Saran - FoodEdu'; ?></title>
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
                <a href="pengaduan.php" class="nav-item pengaduan-link"><?php echo $isMBG ? 'Data Pengaduan' : 'Pengaduan'; ?></a>
                <a href="saran.php" class="nav-item saran-link active">Saran</a>

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

    <!-- ================= FORM / HASIL SARAN ================= -->
    <main class="pengaduan-container">
        <div class="pengaduan-card">
            <div class="pengaduan-header">
                <?php if ($isMBG): ?>
                    <h1 class="pengaduan-title">Hasil Saran Pengguna</h1>
                    <p class="pengaduan-subtitle">
                        Kumpulan saran dan masukan dari pengguna untuk pengembangan program makan bergizi.
                    </p>
                <?php else: ?>
                    <h1 class="pengaduan-title">Form Saran</h1>
                    <p class="pengaduan-subtitle">Berikan saran dan masukan Anda untuk meningkatkan program makan bergizi</p>
                <?php endif; ?>
            </div>

            <?php if ($isMBG): ?>
                <!-- Tampilan daftar saran untuk MBG -->
                <section class="admin-review-wrapper">
                    <div class="admin-review-summary">
                        <div class="admin-summary-item">
                            <span class="label">Total Saran</span>
                            <span class="value"><?php echo count($saranList); ?></span>
                        </div>
                    </div>

                    <?php if (empty($saranList)): ?>
                        <div class="admin-empty-state">
                            <h2>Belum ada saran</h2>
                            <p>Pengguna belum mengirimkan saran. Ajak sekolah dan siswa untuk aktif memberikan masukan.</p>
                        </div>
                    <?php else: ?>
                        <div class="admin-review-list">
                            <?php foreach ($saranList as $item): ?>
                                <article class="admin-review-card reveal">
                                    <header class="admin-review-header">
                                        <div>
                                            <h3><?php echo htmlspecialchars($item['nama_sekolah']); ?></h3>
                                            <p class="admin-review-meta">
                                                <span><?php echo htmlspecialchars($item['nama_lengkap']); ?></span>
                                                <span>•</span>
                                                <span>Saran Program MBG</span>
                                            </p>
                                        </div>
                                        <div class="admin-review-status status-<?php echo htmlspecialchars($item['status']); ?>">
                                            <?php echo ucfirst(htmlspecialchars($item['status'])); ?>
                                        </div>
                                    </header>

                                    <div class="admin-review-body">
                                        <p class="admin-review-text">
                                            <strong>Saran:</strong><br>
                                            <?php echo nl2br(htmlspecialchars($item['saran_dan_masukan'])); ?>
                                        </p>
                                        <p class="admin-review-text" style="margin-top: 8px;">
                                            <strong>Alasan:</strong><br>
                                            <?php echo nl2br(htmlspecialchars($item['alasan'])); ?>
                                        </p>
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
                <!-- Form saran untuk pengguna biasa -->
                <form id="formSaran" class="pengaduan-form">
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

                    <!-- Saran dan Masukan -->
                    <div class="form-group">
                        <label for="saran_dan_masukan" class="form-label">
                            Saran dan Masukan <span class="required">*</span>
                        </label>
                        <textarea 
                            id="saran_dan_masukan" 
                            name="saran_dan_masukan" 
                            class="form-textarea"
                            rows="6"
                            placeholder="Tuliskan saran dan masukan Anda untuk program makan bergizi..."
                            required
                        ></textarea>
                    </div>

                    <!-- Alasan -->
                    <div class="form-group">
                        <label for="alasan" class="form-label">
                            Alasan <span class="required">*</span>
                        </label>
                        <textarea 
                            id="alasan" 
                            name="alasan" 
                            class="form-textarea"
                            rows="6"
                            placeholder="Jelaskan alasan mengapa saran ini penting..."
                            required
                        ></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-actions">
                        <button type="submit" class="btn-submit" id="btnSubmit">
                            <span class="btn-text">Kirim Saran</span>
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
                FoodEdu adalah sebuah platform berbasis web yang dirancang sebagai media edukasi 
                dan pengumpulan umpan balik mengenai program Makanan Bergizi Gratis (MBG) dari pemerintah. 
                Website ini berfungsi sebagai perantara antara siswa, orang tua, sekolah, dan pemerintah, 
                sehingga seluruh pihak dapat saling terhubung dalam memantau kualitas makanan sekolah.
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

            // Form submission
            const formSaran = document.getElementById('formSaran');
            if (formSaran) {
                formSaran.addEventListener('submit', async function(e) {
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
                    
                    // Get form data
                    const formData = new FormData(this);
                    
                    try {
                        const response = await fetch('saran/submit.php', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            formMessage.className = 'form-message success';
                            formMessage.textContent = result.message || 'Saran berhasil dikirim!';
                            formMessage.style.display = 'block';
                            
                            // Reset form
                            this.reset();
                            document.getElementById('nama_lengkap').value = '<?php echo htmlspecialchars($user['name']); ?>';
                            
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
        </script>
    <?php endif; ?>
</body>
</html>

