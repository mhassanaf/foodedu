# Setup Halaman Pengaduan - Panduan Lengkap

## ✅ File yang Dibuat

1. **pengaduan.php** - Halaman form pengaduan (memerlukan login)
2. **pengaduan/submit.php** - Handler untuk submit form ke database
3. **database_pengaduan.sql** - SQL untuk membuat tabel pengaduan
4. **uploads/pengaduan/** - Folder untuk menyimpan file bukti (akan dibuat otomatis)

## 📋 Langkah Setup Database

### 1. Import Tabel Pengaduan ke phpMyAdmin

1. Buka **phpMyAdmin** (http://localhost/phpmyadmin)
2. Pilih database **foodedu**
3. Klik tab **SQL**
4. Copy dan paste isi file `database_pengaduan.sql` atau jalankan query berikut:

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
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

5. Klik **Go** untuk menjalankan query
6. Pastikan tabel `pengaduan` berhasil dibuat

### 2. Buat Folder Uploads

Folder `uploads/pengaduan/` akan dibuat otomatis saat pertama kali upload file. Namun, untuk memastikan, Anda bisa membuat manual:

1. Buat folder `uploads` di root project
2. Buat folder `pengaduan` di dalam `uploads`
3. Set permission folder agar bisa ditulis (chmod 755 atau 777)

**Atau jalankan di terminal:**
```bash
mkdir -p uploads/pengaduan
```

## 🔗 Cara Menghubungkan ke phpMyAdmin

### Koneksi Database

Koneksi database sudah dikonfigurasi di `auth/config.php`:

```php
$DB_HOST = '127.0.0.1';
$DB_NAME = 'foodedu';
$DB_USER = 'root';
$DB_PASS = ''; // untuk XAMPP default kosong
```

**Jika menggunakan konfigurasi berbeda:**
1. Edit file `auth/config.php`
2. Sesuaikan dengan konfigurasi MySQL/XAMPP Anda

### Struktur Tabel Pengaduan

| Kolom | Tipe | Deskripsi |
|-------|------|-----------|
| id | INT | Primary key, auto increment |
| user_id | INT | Foreign key ke tabel users |
| nama_lengkap | VARCHAR(255) | Nama lengkap pengadu |
| nama_sekolah | VARCHAR(255) | Nama sekolah |
| tanggal_kejadian | DATE | Tanggal kejadian |
| jenis_pengaduan | ENUM | Jenis pengaduan (Kualitas/Kebersihan/Kuantitas/Lainnya) |
| deskripsi | TEXT | Deskripsi lengkap pengaduan |
| bukti_path | VARCHAR(500) | Path file bukti (nullable) |
| status | ENUM | Status (pending/diproses/selesai/ditolak) |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diupdate |

## 🚀 Cara Menggunakan

### 1. Akses Halaman Pengaduan

- **Jika sudah login**: Klik "Pengaduan" di navbar → langsung ke `pengaduan.php`
- **Jika belum login**: Klik "Pengaduan" di navbar → akan muncul konfirmasi → redirect ke `auth.html`

### 2. Isi Form Pengaduan

1. **Nama Lengkap**: Otomatis terisi dari data user (readonly)
2. **Nama Sekolah**: Input manual
3. **Tanggal Kejadian**: Pilih dari date picker
4. **Jenis Pengaduan**: Pilih dari dropdown
5. **Deskripsi**: Textarea untuk detail pengaduan
6. **Upload Bukti**: Optional, format JPG/PNG/PDF (max 5MB)

### 3. Submit Form

- Klik "Kirim Pengaduan"
- Data akan tersimpan ke database
- File bukti akan tersimpan di `uploads/pengaduan/`
- Muncul pesan sukses/error

## 📊 Melihat Data di phpMyAdmin

1. Buka phpMyAdmin
2. Pilih database **foodedu**
3. Klik tabel **pengaduan**
4. Klik tab **Browse** untuk melihat semua data
5. Klik tab **Structure** untuk melihat struktur tabel

## 🔒 Keamanan

- ✅ Session check untuk proteksi halaman
- ✅ File upload validation (type & size)
- ✅ SQL injection protection dengan PDO prepared statements
- ✅ XSS protection dengan htmlspecialchars
- ✅ Foreign key constraint untuk integritas data

## ⚠️ Troubleshooting

### Error: "Table doesn't exist"
**Solusi**: Import file `database_pengaduan.sql` ke phpMyAdmin

### Error: "Cannot upload file"
**Solusi**: 
- Pastikan folder `uploads/pengaduan/` ada dan bisa ditulis
- Check permission folder (chmod 755 atau 777)

### Error: "Access denied for user"
**Solusi**: Periksa username/password di `auth/config.php`

### Form tidak submit
**Solusi**: 
- Check console browser untuk error JavaScript
- Pastikan `pengaduan/submit.php` bisa diakses
- Check network tab untuk melihat response dari server

## 📝 Catatan

- File upload maksimal 5MB
- Format file yang didukung: JPG, PNG, PDF
- Data pengaduan terhubung dengan user melalui `user_id`
- Status default: `pending`
- Timestamp otomatis untuk `created_at` dan `updated_at`

