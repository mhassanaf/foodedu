<?php
// test_db_connection.php - STANDALONE VERSION
// Tidak bergantung pada config.php agar tidak exit() saat error

header('Content-Type: text/plain');

echo "=== DIAGNOSTIK KONEKSI DATABASE (STANDALONE) ===\n\n";

// 1. Ambil Env Vars Manual (Copy logika dari config.php)
$raw_host = getenv('FOODEDU_DB_HOST') ?: getenv('MYSQLHOST') ?: '127.0.0.1';
$DB_HOST = $raw_host;
$host_port = null;

// Parse host:port
if (strpos($raw_host, ':') !== false) {
    list($DB_HOST, $host_port) = explode(':', $raw_host, 2);
    echo "   [INFO] Port terdeteksi di Hostname: $host_port\n";
}

$DB_NAME = getenv('FOODEDU_DB_NAME') ?: getenv('MYSQLDATABASE') ?: 'foodedu';
$DB_USER = getenv('FOODEDU_DB_USER') ?: getenv('MYSQLUSER') ?: 'root';
$DB_PASS = getenv('FOODEDU_DB_PASS') !== false ? getenv('FOODEDU_DB_PASS') : (getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD') : '');
$DB_PORT = getenv('FOODEDU_DB_PORT') ?: getenv('MYSQLPORT') ?: ($host_port ? $host_port : '3306');

// Tampilkan Nilai Akhir yang akan dipakai
echo "1. PARAMETER KONEKSI:\n";
echo "   Host Asli : $raw_host\n";
echo "   Host Final: $DB_HOST\n";
echo "   Port Final: $DB_PORT\n";
echo "   User      : $DB_USER\n";
echo "   Database  : $DB_NAME\n";
echo "\n";

// 2. Coba Koneksi PDO
echo "2. TES KONEKSI PDO...\n";
try {
    $dsn = "mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;charset=utf8mb4";
    echo "   DSN: $dsn\n";

    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5
    ]);

    echo "   [SUKSES] Koneksi Berhasil!\n";

    // 3. Cek Tabel hanya jika koneksi sukses
    echo "\n3. CEK TABEL:\n";
    $tables = ['users', 'gizi_items', 'kelayakan_items', 'pengaduan'];
    foreach ($tables as $t) {
        try {
            $pdo->query("SELECT 1 FROM $t LIMIT 1");
            echo "   - Tabel '$t': ADA\n";
        } catch (PDOException $e) {
            echo "   - Tabel '$t': TIDAK ADA (Code: " . $e->getCode() . ")\n";
        }
    }

} catch (PDOException $e) {
    echo "   [GAGAL KONEKSI]\n";
    echo "   Pesan Error: " . $e->getMessage() . "\n";
    echo "   Kode Error : " . $e->getCode() . "\n";
}
?>