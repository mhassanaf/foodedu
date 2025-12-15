<?php
require_once 'auth/config.php';

// Get Slug
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
if (!$slug) {
    header('Location: gizi.html');
    exit;
}

// Fetch Item
$stmt = $pdo->prepare("SELECT * FROM gizi_items WHERE slug = ?");
$stmt->execute([$slug]);
$item = $stmt->fetch();

if (!$item) {
    echo "<h1>Konten tidak ditemukan</h1><a href='gizi.html'>Kembali</a>";
    exit;
}

// Defaults for missing data
function val($v, $default = '-')
{
    return $v ? htmlspecialchars($v) : $default;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Gizi – <?php echo val($item['name']); ?></title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="main.css">
</head>

<body>

    <!-- ================= NAVBAR ================= -->
    <nav class="navbar-gizi">
        <a href="index.html" class="logo">
            <img src="asset/logo/foodedu.png" alt="FoodEdu">
        </a>

        <div class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <div class="nav-menu" id="navMenu">
            <a href="index.html">Beranda</a>
            <a href="gizi.html" class="active">Program</a>
            <a href="pengaduan.php">Pengaduan</a>
            <a href="saran.php">Saran</a>

            <div class="nav-buttons">
                <!-- Buttons managed by main.js -->
                <button class="btn-login">Log in</button>
                <button class="btn-signup">Sign up</button>
            </div>
        </div>
    </nav>

    <!-- ================= HEADER ================= -->
    <div class="page-header" style="background: <?php echo $item['color'] === 'orange' ? '#fff6e6' : '#e6f4ea'; ?>;">
        <h1 class="page-title" style="color: <?php echo $item['color'] === 'orange' ? '#e67e22' : '#27ae60'; ?>;">
            INFORMASI GIZI</h1>
        <h2 class="food-title"><?php echo strtoupper(val($item['name'])); ?></h2>
    </div>

    <!-- ================= CONTENT CONTAINER ================= -->
    <div class="content-container">

        <!-- ================= IMAGE SECTION ================= -->
        <div class="image-section">
            <div class="food-image-container">
                <img src="<?php echo val($item['image_url']); ?>" alt="<?php echo val($item['name']); ?>"
                    class="food-image" style="object-fit:cover; border-radius:50%; aspect-ratio:1/1;">
            </div>
        </div>

        <!-- ================= INFO SECTION ================= -->
        <div class="info-section">

            <?php if ($item['description']): ?>
                <p style="margin-bottom: 24px; color: #555; line-height: 1.6;">
                    <?php echo nl2br(val($item['description'])); ?></p>
            <?php endif; ?>

            <!-- ================= NUTRITION TABLE ================= -->
            <div class="nutrition-box"
                style="border-top: 5px solid <?php echo $item['color'] === 'orange' ? '#e67e22' : '#27ae60'; ?>;">
                <div class="nutrition-title"><?php echo val($item['name']); ?></div>

                <table class="nutrition-table">
                    <tr>
                        <td>Ukuran Porsi</td>
                        <td><?php echo val($item['portion_size'], '100 gram'); ?></td>
                    </tr>
                    <tr>
                        <td>Energi</td>
                        <td><?php echo val($item['energy']); ?></td>
                    </tr>
                    <tr>
                        <td>Lemak</td>
                        <td><?php echo val($item['fat']); ?></td>
                    </tr>
                    <tr>
                        <td>Protein</td>
                        <td><?php echo val($item['protein']); ?></td>
                    </tr>
                    <tr>
                        <td>Karbohidrat</td>
                        <td><?php echo val($item['carbs']); ?></td>
                    </tr>
                    <tr>
                        <td>Natrium</td>
                        <td><?php echo val($item['sodium']); ?></td>
                    </tr>
                    <tr>
                        <td>Kalsium</td>
                        <td><?php echo val($item['calcium']); ?></td>
                    </tr>
                </table>
            </div>

            <!-- ================= MACRO CIRCLES ================= -->
            <div class="macro-wrapper">
                <!-- Energy -->
                <div class="macro-item <?php echo $item['color'] === 'orange' ? 'orange' : 'green'; ?>">
                    <span class="macro-value"><?php echo floatval($item['energy']); ?></span>
                    <span class="macro-label">KAL</span>
                </div>

                <!-- Fat -->
                <div class="macro-item orange">
                    <span class="macro-value"><?php echo val($item['fat']); ?></span>
                    <span class="macro-label">LEMAK</span>
                </div>

                <!-- Protein -->
                <div class="macro-item green">
                    <span class="macro-value"><?php echo val($item['protein']); ?></span>
                    <span class="macro-label">PROT</span>
                </div>

                <!-- Carbs -->
                <div class="macro-item orange">
                    <span class="macro-value"><?php echo val($item['carbs']); ?></span>
                    <span class="macro-label">KARB</span>
                </div>
            </div>
        </div>
    </div>

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
</body>

</html>