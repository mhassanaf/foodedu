<?php
require_once __DIR__ . '/auth/config.php';

try {
    echo "Starting migration...\n";

    // 1. Insert Telur Rebus into gizi_items
    $stmt = $pdo->prepare("INSERT INTO gizi_items (name, slug, color, image_url, category, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        'Telur Rebus',
        'telur-rebus',
        'orange',
        'uploads/gizi_telur.png',
        'Protein',
        10
    ]);
    echo "Inserted 'Telur Rebus' into gizi_items.\n";

    // 2. Insert Roti Tawar into kelayakan_items
    $stmt = $pdo->prepare("INSERT INTO kelayakan_items (food_name, good_title, good_image_url, good_points, bad_title, bad_image_url, bad_points, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        'Roti Tawar',
        'Layak Dikonsumsi',
        'uploads/kelayakan_roti_good.png',
        "Warna putih bersih\nTidak ada bintik jamur\nAroma segar khas roti\nTekstur lembut & empuk",
        'Tidak Layak Dikonsumsi',
        'uploads/kelayakan_roti_bad.png',
        "Muncul bintik hijau/hitam (jamur)\nBau apek atau tengik\nTekstur keras atau berlendir\nRasa pahit/asam",
        10
    ]);
    echo "Inserted 'Roti Tawar' into kelayakan_items.\n";

    echo "Migration completed successfully.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
