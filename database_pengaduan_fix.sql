-- =========================================================
-- PENGADUAN TABLE - FIXED VERSION
-- Solusi untuk error Foreign key constraint
-- =========================================================

USE foodedu;

-- Langkah 1: Pastikan tabel users sudah ada
-- Jika belum, jalankan database.sql terlebih dahulu

-- Langkah 2: Pastikan tabel users menggunakan InnoDB
-- Jika belum, ubah engine tabel users:
-- ALTER TABLE users ENGINE=InnoDB;

-- Langkah 3: Buat tabel pengaduan TANPA foreign key dulu
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

-- Langkah 4: Tambahkan foreign key secara terpisah
-- Hapus foreign key jika sudah ada sebelumnya
ALTER TABLE pengaduan DROP FOREIGN KEY IF EXISTS fk_pengaduan_user_id;

-- Tambahkan foreign key
ALTER TABLE pengaduan 
ADD CONSTRAINT fk_pengaduan_user_id 
FOREIGN KEY (user_id) 
REFERENCES users(id) 
ON DELETE CASCADE;

