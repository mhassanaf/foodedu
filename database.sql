-- =========================================================
-- FOODEDU DATABASE SCHEMA
-- =========================================================

CREATE DATABASE IF NOT EXISTS foodedu CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE foodedu;

-- =========================================================
-- USERS TABLE
-- =========================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20),
    role ENUM('siswa', 'ortu', 'sekolah', 'mbg') NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- SAMPLE DATA (Optional - untuk testing)
-- =========================================================
-- Password untuk semua user sample: "password123"
-- INSERT INTO users (name, email, phone, role, username, password_hash, sekolah) VALUES
-- ('Admin Siswa', 'siswa@foodedu.id', '081234567890', 'siswa', 'siswa1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'SD Negeri 1'),
-- ('Admin Ortu', 'ortu@foodedu.id', '081234567891', 'ortu', 'ortu1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'SD Negeri 1'),
-- ('Admin Sekolah', 'sekolah@foodedu.id', '081234567892', 'sekolah', 'sekolah1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'SD Negeri 1'),
-- ('Admin MBG', 'mbg@foodedu.id', '081234567893', 'mbg', 'mbg1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'SD Negeri 1');

CREATE TABLE IF NOT EXISTS gizi_items (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS kelayakan_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    food_name VARCHAR(100) NOT NULL,
    good_title VARCHAR(150) NOT NULL,
    good_image_url VARCHAR(255) NOT NULL,
    good_points TEXT NOT NULL,
    bad_title VARCHAR(150) NOT NULL,
    bad_image_url VARCHAR(255) NOT NULL,
    bad_points TEXT NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pengaduan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(255) NOT NULL,
    nama_sekolah VARCHAR(255) NOT NULL,
    tanggal_kejadian DATE NOT NULL,
    jenis_pengaduan VARCHAR(100) NOT NULL,
    deskripsi TEXT NOT NULL,
    bukti_path VARCHAR(255),
    status ENUM('baru','diproses','selesai') DEFAULT 'baru',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS saran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(255) NOT NULL,
    nama_sekolah VARCHAR(255) NOT NULL,
    saran_dan_masukan TEXT NOT NULL,
    alasan TEXT,
    status ENUM('baru','ditinjau','diterima','ditolak') DEFAULT 'baru',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
