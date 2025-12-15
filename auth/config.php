<?php
// Function to parse DATABASE_URL if present
function get_db_config()
{
    $config = [
        'host' => '127.0.0.1',
        'port' => '3306',
        'dbname' => 'foodedu',
        'user' => 'root',
        'pass' => '',
    ];

    // Priority 1: DATABASE_URL (Railway default for many setups)
    if ($url = getenv('DATABASE_URL')) {
        $parts = parse_url($url);
        if ($parts) {
            $config['host'] = $parts['host'] ?? $config['host'];
            $config['port'] = $parts['port'] ?? $config['port'];
            $config['user'] = $parts['user'] ?? $config['user'];
            $config['pass'] = $parts['pass'] ?? $config['pass'];
            $config['dbname'] = ltrim($parts['path'] ?? '/' . $config['dbname'], '/');
        }
    } else {
        // Priority 2: Individual variables
        $config['host'] = getenv('FOODEDU_DB_HOST') ?: getenv('MYSQLHOST') ?: $config['host'];
        $config['port'] = getenv('FOODEDU_DB_PORT') ?: getenv('MYSQLPORT') ?: $config['port'];
        $config['dbname'] = getenv('FOODEDU_DB_NAME') ?: getenv('MYSQLDATABASE') ?: $config['dbname'];
        $config['user'] = getenv('FOODEDU_DB_USER') ?: getenv('MYSQLUSER') ?: $config['user'];
        // Use generic MYSQLPASSWORD if specific one not set
        $pass = getenv('FOODEDU_DB_PASS');
        if ($pass === false) {
            $pass = getenv('MYSQLPASSWORD');
        }
        if ($pass !== false) {
            $config['pass'] = $pass;
        }
    }

    // Handle host:port format in host variable
    if (strpos($config['host'], ':') !== false) {
        list($host, $port) = explode(':', $config['host'], 2);
        $config['host'] = $host;
        $config['port'] = $port;
    }

    return $config;
}

$db_cfg = get_db_config();
$DB_HOST = $db_cfg['host'];
$DB_PORT = $db_cfg['port'];
$DB_NAME = $db_cfg['dbname'];
$DB_USER = $db_cfg['user'];
$DB_PASS = $db_cfg['pass'];

// Allow local override
if (file_exists(__DIR__ . '/config.override.php')) {
    include __DIR__ . '/config.override.php';
}

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

// Attempt connection
try {
    $dsn = "mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;charset=utf8mb4";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    // Attempt bootstrap if database might not exist (Development only usually, but kept for compatibility)
    // ONLY if the error code suggests "Unknown database" (1049)
    if ($e->getCode() == 1049) {
        try {
            $pdoBootstrap = new PDO("mysql:host=$DB_HOST;port=$DB_PORT;charset=utf8mb4", $DB_USER, $DB_PASS, $options);
            $pdoBootstrap->exec("CREATE DATABASE IF NOT EXISTS `$DB_NAME` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdoBootstrap->exec("USE `$DB_NAME`");
            // Basic table structure
            $pdoBootstrap->exec(
                "CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL UNIQUE,
                    phone VARCHAR(20),
                    role ENUM('siswa','ortu','sekolah','mbg') NOT NULL,
                    username VARCHAR(100) NOT NULL UNIQUE,
                    password_hash VARCHAR(255) NOT NULL,
                    sekolah VARCHAR(255),
                    anak VARCHAR(255),
                    nip VARCHAR(50),
                    idk VARCHAR(50),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_username (username),
                    INDEX idx_email (email),
                    INDEX idx_role (role)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            );
            // Re-connect to the new DB
            $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
        } catch (Exception $ex) {
            // If bootstrap fails, show original error
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage() . ' (Bootstrap failed: ' . $ex->getMessage() . ')']);
            exit;
        }
    } else {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
        exit;
    }
}
