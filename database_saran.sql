-- =========================================================
-- SARAN TABLE - SIMPLE VERSION (Tanpa Foreign Key)
-- =========================================================

USE foodedu;

-- Hapus tabel jika sudah ada (untuk testing)
DROP TABLE IF EXISTS saran;

-- Buat tabel saran
CREATE TABLE saran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_lengkap VARCHAR(255) NOT NULL,
    nama_sekolah VARCHAR(255) NOT NULL,
    saran_dan_masukan TEXT NOT NULL,
    alasan TEXT NOT NULL,
    status ENUM('pending', 'diproses', 'selesai', 'ditolak') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

