# Solusi Error Foreign Key Constraint

## ❌ Error yang Terjadi
```
#1005 - Can't create table `foodedu`.`pengaduan` 
(errno: 150 "Foreign key constraint is incorrectly formed")
```

## 🔍 Penyebab Error

Error ini biasanya terjadi karena:
1. **Tabel `users` belum ada** - Foreign key memerlukan tabel referensi yang sudah ada
2. **Engine tabel berbeda** - Foreign key hanya bekerja jika kedua tabel menggunakan InnoDB
3. **Tipe data tidak cocok** - Kolom `user_id` dan `users.id` harus memiliki tipe data yang sama
4. **Charset/Collation berbeda** - Harus sama antara kedua tabel

## ✅ Solusi

### **Solusi 1: Pastikan Tabel Users Sudah Ada (RECOMMENDED)**

1. **Cek apakah tabel users sudah ada:**
   ```sql
   USE foodedu;
   SHOW TABLES LIKE 'users';
   ```

2. **Jika belum ada, buat dulu:**
   - Import file `database.sql` di phpMyAdmin
   - Atau jalankan query berikut:
   ```sql
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
   ```

3. **Pastikan tabel users menggunakan InnoDB:**
   ```sql
   ALTER TABLE users ENGINE=InnoDB;
   ```

4. **Kemudian buat tabel pengaduan:**
   - Gunakan file `database_pengaduan_fix.sql` (sudah diperbaiki)
   - Atau jalankan query berikut:

### **Solusi 2: Buat Tabel Tanpa Foreign Key Dulu**

Jalankan query ini di phpMyAdmin (tab SQL):

```sql
USE foodedu;

-- Buat tabel tanpa foreign key
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
ALTER TABLE pengaduan 
ADD CONSTRAINT fk_pengaduan_user_id 
FOREIGN KEY (user_id) 
REFERENCES users(id) 
ON DELETE CASCADE;
```

### **Solusi 3: Tanpa Foreign Key (Alternatif)**

Jika masih error, buat tabel tanpa foreign key (tetap bisa digunakan):

```sql
USE foodedu;

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
```

**Catatan:** Tanpa foreign key, integritas data tetap terjaga melalui aplikasi PHP.

## 📋 Langkah-langkah di phpMyAdmin

1. **Buka phpMyAdmin** (http://localhost/phpmyadmin)
2. **Pilih database `foodedu`**
3. **Cek tabel users:**
   - Klik tab **SQL**
   - Jalankan: `SHOW TABLES LIKE 'users';`
   - Jika tidak ada hasil, import `database.sql` dulu

4. **Pastikan engine InnoDB:**
   - Klik tabel `users`
   - Klik tab **Operations**
   - Pastikan **Storage Engine** = **InnoDB**
   - Jika bukan, ubah ke InnoDB

5. **Buat tabel pengaduan:**
   - Klik tab **SQL**
   - Copy query dari **Solusi 2** di atas
   - Klik **Go**

## ✅ Verifikasi

Setelah berhasil, cek dengan:

```sql
-- Cek struktur tabel
DESCRIBE pengaduan;

-- Cek foreign key
SHOW CREATE TABLE pengaduan;

-- Atau lihat di tab Structure di phpMyAdmin
```

## 🔧 Troubleshooting Tambahan

### Jika masih error setelah langkah di atas:

1. **Cek tipe data kolom id di tabel users:**
   ```sql
   DESCRIBE users;
   ```
   Pastikan kolom `id` adalah `INT` (bukan `BIGINT` atau lainnya)

2. **Cek charset dan collation:**
   ```sql
   SHOW CREATE TABLE users;
   SHOW CREATE TABLE pengaduan;
   ```
   Pastikan keduanya menggunakan `utf8mb4` dan `utf8mb4_unicode_ci`

3. **Hapus tabel pengaduan jika sudah ada (untuk testing):**
   ```sql
   DROP TABLE IF EXISTS pengaduan;
   ```
   Kemudian buat ulang dengan query di atas

## 💡 Tips

- **Selalu buat tabel referensi (users) terlebih dahulu**
- **Gunakan InnoDB untuk semua tabel yang memiliki foreign key**
- **Pastikan charset dan collation sama**
- **Gunakan tipe data yang sama untuk kolom yang direferensikan**

