-- =========================================================
-- PENGADUAN TABLE
-- =========================================================

USE foodedu;

-- Pastikan tabel users sudah ada dan menggunakan InnoDB
-- Jika belum, buat dulu dengan menjalankan database.sql

-- Hapus tabel jika sudah ada (untuk testing)
-- DROP TABLE IF EXISTS pengaduan;

-- Buat tabel pengaduan tanpa foreign key dulu
CREATE TABLE IF NOT EXISTS pengaduan (
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

-- Tambahkan foreign key setelah tabel dibuat
-- Hapus foreign key jika sudah ada
SET @dbname = DATABASE();
SET @tablename = "pengaduan";
SET @columnname = "user_id";
SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = @dbname
        AND TABLE_NAME = @tablename
        AND COLUMN_NAME = @columnname
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ) > 0,
    "SELECT 'Foreign key already exists' AS result;",
    CONCAT("ALTER TABLE ", @tablename, " ADD CONSTRAINT fk_pengaduan_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;")
) INTO @sql;
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

