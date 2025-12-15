-- =========================================================
-- PENGADUAN TABLE - SIMPLE VERSION (Tanpa Foreign Key)
-- Gunakan ini jika masih error dengan foreign key
-- =========================================================

USE foodedu;

-- Hapus tabel jika sudah ada (untuk testing)
DROP TABLE IF EXISTS pengaduan;

-- Buat tabel pengaduan
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

