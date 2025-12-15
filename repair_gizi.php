<?php
require_once 'auth/config.php';

echo "<h1>Repairing gizi_items Table</h1>";

try {
    // 1. DROP the table if it exists (or is corrupted)
    echo "Attempting to DROP table `gizi_items`... ";
    try {
        $pdo->exec("DROP TABLE IF EXISTS gizi_items");
        echo "<span style='color:green'>SUCCESS</span><br>";
    } catch (PDOException $e) {
        echo "<span style='color:orange'>WARNING (Drop failed, might be normal if missing): " . $e->getMessage() . "</span><br>";
    }

    // 2. CREATE the table
    echo "Attempting to CREATE table `gizi_items`... ";
    $sql = "CREATE TABLE gizi_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        slug VARCHAR(120) NOT NULL UNIQUE,
        color ENUM('green','orange') NOT NULL DEFAULT 'green',
        image_url VARCHAR(255) NOT NULL,
        category VARCHAR(100) DEFAULT NULL,
        sort_order INT NOT NULL DEFAULT 0,
        description TEXT,
        portion_size VARCHAR(50),
        energy VARCHAR(50),
        fat VARCHAR(50),
        protein VARCHAR(50),
        carbs VARCHAR(50),
        sodium VARCHAR(50),
        calcium VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $pdo->exec($sql);
    echo "<span style='color:green'>SUCCESS</span><br>";

    echo "<h3>Repair Complete. Please check your database or application.</h3>";
    echo "<p><a href='index.html'>Go to Home</a></p>";

} catch (PDOException $e) {
    echo "<h2 style='color:red'>Critical Error</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?>