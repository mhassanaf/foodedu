<?php
require_once '../auth/check_session.php';
$user = getCurrentUser();

// Ensure only ortu can access
if ($user['role'] !== 'ortu') {
    header('Location: ../auth.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Orang Tua - FoodEdu</title>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../main.css">
    <style>
        .panel {
            animation: fadeInUp 0.6s cubic-bezier(0.2, 0.0, 0.2, 1) backwards;
        }

        .panel:nth-child(2) {
            animation-delay: 0.1s;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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
                        <a href="../gizi.html#informasi-gizi">Informasi Gizi Seimbang</a>
                        <a href="../gizi.html#kelayakan">Edukasi Kelayakan Makanan</a>
                    </div>
                </div>
                <a href="../pengaduan.php" class="nav-item pengaduan-link">Pengaduan</a>
                <a href="../saran.php" class="nav-item saran-link">Saran</a>
                <div class="nav-buttons">
                    <button class="btn-login" onclick="window.location.href='../auth.html'">Login</button>
                    <button class="btn-signup" onclick="window.location.href='../auth.html'">Sign Up</button>
                </div>
            </nav>
            <button class="hamburger" id="hamburger">
                <span></span><span></span><span></span>
            </button>
        </div>
    </header>

    <main style="max-width: 1200px; margin: 40px auto; padding: 20px;">
        <h1 style="font-size: 32px; margin-bottom: 30px; color: var(--green); animation: fadeInUp 0.5s ease backwards;">
            Dashboard Orang Tua</h1>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <div class="panel" style="padding: 30px;">
                <h3 style="margin-bottom: 15px; color: #333;">Informasi Akun</h3>
                <p><strong>Nama:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                <p><strong>Role:</strong> <span
                        style="background:#e6f4ea; color:var(--green); padding:2px 8px; border-radius:4px; font-size:12px; font-weight:600;">ORANG
                        TUA</span></p>
                <?php if (isset($user['anak'])): ?>
                    <p style="margin-top:8px;"><strong>Nama Anak:</strong> <?php echo htmlspecialchars($user['anak']); ?>
                    </p>
                <?php endif; ?>
                <?php if (isset($user['sekolah'])): ?>
                    <p><strong>Sekolah:</strong> <?php echo htmlspecialchars($user['sekolah']); ?></p>
                <?php endif; ?>
            </div>

            <div class="panel" style="padding: 30px;">
                <h3 style="margin-bottom: 15px; color: #333;">Akses Cepat</h3>
                <a href="../gizi.html" class="btn-primary"
                    style="display: block; text-align: center; margin-bottom: 10px;">Informasi Gizi</a>
                <a href="../pengaduan.php" class="btn-secondary"
                    style="display: block; text-align: center; margin-bottom: 10px;">Pengaduan</a>
                <a href="../saran.php" class="btn-secondary" style="display: block; text-align: center;">Saran</a>
            </div>
        </div>
    </main>

    <script src="../main.js"></script>
</body>

</html>