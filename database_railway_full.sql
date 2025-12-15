-- =========================================================
-- FOODEDU RAILWAY DATABASE SCHEMA (FULL)
-- Jalankan ini di DBeaver yang terhubung ke RAILWAY
-- =========================================================

-- 1. Hapus database default jika perlu (Opsional, hati-hati)
-- DROP DATABASE IF EXISTS railway;
-- CREATE DATABASE railway;

USE railway;

-- =========================================================
-- 1. USERS TABLE
-- =========================================================
DROP TABLE IF EXISTS users;
CREATE TABLE users (
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
-- 2. GIZI ITEMS TABLE
-- =========================================================
DROP TABLE IF EXISTS gizi_items;
CREATE TABLE gizi_items (
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

-- =========================================================
-- 3. KELAYAKAN ITEMS TABLE
-- =========================================================
DROP TABLE IF EXISTS kelayakan_items;
CREATE TABLE kelayakan_items (
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

-- =========================================================
-- 4. PENGADUAN TABLE
-- =========================================================
DROP TABLE IF EXISTS pengaduan;
CREATE TABLE pengaduan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_lengkap VARCHAR(255) NOT NULL,
    nama_sekolah VARCHAR(255) NOT NULL,
    tanggal_kejadian DATE NOT NULL,
    jenis_pengaduan ENUM('Kualitas Makanan', 'Kebersihan Makanan', 'Kuantitas Makanan', 'Lainnya') NOT NULL,
    deskripsi TEXT NOT NULL,
    bukti_path VARCHAR(500) NULL,
    status ENUM('pending', 'diproses', 'selesai', 'ditolak') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 5. SARAN TABLE
-- =========================================================
DROP TABLE IF EXISTS saran;
CREATE TABLE saran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(255) NOT NULL,
    nama_sekolah VARCHAR(255) NOT NULL,
    saran_dan_masukan TEXT NOT NULL,
    alasan TEXT,
    status ENUM('baru','ditinjau','diterima','ditolak') DEFAULT 'baru',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- DATA SAMPLE (Optional - Agar tidak kosong melompong)
-- =========================================================

-- Sample User MBG (Password: 123456)
-- Harap ganti password setelah login!
INSERT INTO users (name, email, phone, role, username, password_hash, idk) 
VALUES ('Admin MBG', 'mbg@foodedu.id', '081234567890', 'mbg', 'mbg', '$2y$10$PhW.u.t.h/t.u.t.h.u.t.h.u.t.h.u.t.h.u.t.h.u.t.h.', 'PEG-001');

