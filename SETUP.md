# Setup FoodEdu - Panduan Lengkap

## ✅ Yang Sudah Dikerjakan

### 1. **File CSS & JS Terpusat**
- ✅ Semua CSS digabungkan menjadi `main.css`
- ✅ Semua JS digabungkan menjadi `main.js`
- ✅ Semua file HTML sudah menggunakan `main.css` dan `main.js`

### 2. **Sistem Autentikasi**
- ✅ File auth: `auth/register.php`, `auth/login.php`, `auth/logout.php`
- ✅ File config: `auth/config.php` (koneksi database)
- ✅ File session check: `auth/check_session.php`
- ✅ Halaman auth: `auth.html`

### 3. **Dashboard**
- ✅ `dashboard/siswa.php` - Dashboard untuk siswa
- ✅ `dashboard/orangtua.php` - Dashboard untuk orang tua
- ✅ `dashboard/sekolah.php` - Dashboard untuk pihak sekolah
- ✅ `dashboard/mbg.php` - Dashboard untuk pihak MBG

### 4. **Integrasi**
- ✅ Tombol Login/Signup di semua navbar terhubung ke `auth.html`
- ✅ Redirect otomatis setelah login berdasarkan role
- ✅ Session management untuk proteksi dashboard

## 📋 Setup Database

### Langkah 1: Buat Database
1. Buka phpMyAdmin (http://localhost/phpmyadmin)
2. Import file `database.sql` atau jalankan query berikut:

```sql
CREATE DATABASE IF NOT EXISTS foodedu CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE foodedu;

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

### Langkah 2: Konfigurasi Database
File `auth/config.php` sudah dikonfigurasi untuk XAMPP default:
- Host: `127.0.0.1`
- Database: `foodedu`
- User: `root`
- Password: `` (kosong untuk XAMPP)

**Jika menggunakan konfigurasi berbeda, edit file `auth/config.php`**

## 🚀 Cara Menggunakan

### 1. Registrasi
1. Klik tombol **"Sign Up"** di navbar
2. Isi form registrasi:
   - Nama Lengkap
   - Email
   - No Handphone
   - Pilih Peran (Siswa/Orang Tua/Pihak Sekolah/Pihak MBG)
   - Field tambahan sesuai peran
   - Username & Password
3. Klik **"Create"**

### 2. Login
1. Klik tombol **"Login"** di navbar
2. Masukkan Username dan Password
3. Klik **"Log in"**
4. Sistem akan redirect ke dashboard sesuai role

### 3. Dashboard
Setelah login, user akan diarahkan ke:
- **Siswa** → `dashboard/siswa.php`
- **Orang Tua** → `dashboard/orangtua.php`
- **Pihak Sekolah** → `dashboard/sekolah.php`
- **Pihak MBG** → `dashboard/mbg.php`

### 4. Logout
Klik tombol **"Logout"** di dashboard untuk keluar

## 🔒 Keamanan

- ✅ Password di-hash menggunakan `password_hash()`
- ✅ Session management untuk proteksi dashboard
- ✅ Role-based access control (setiap role hanya bisa akses dashboardnya)
- ✅ SQL injection protection dengan PDO prepared statements

## 📁 Struktur File

```
foodedu/
├── auth/
│   ├── config.php          # Konfigurasi database
│   ├── register.php         # API registrasi
│   ├── login.php            # API login
│   ├── logout.php           # API logout
│   └── check_session.php    # Session check untuk dashboard
├── dashboard/
│   ├── siswa.php            # Dashboard siswa
│   ├── orangtua.php         # Dashboard orang tua
│   ├── sekolah.php          # Dashboard pihak sekolah
│   └── mbg.php              # Dashboard pihak MBG
├── main.css                 # File CSS utama
├── main.js                  # File JS utama
├── auth.html                # Halaman login/register
├── index.html               # Halaman utama
└── database.sql             # File SQL untuk setup database
```

## ⚠️ Troubleshooting

### Error: "Table doesn't exist"
**Solusi**: Import file `database.sql` ke phpMyAdmin

### Error: "Access denied for user"
**Solusi**: Periksa username/password di `auth/config.php`

### Error: "Connection refused"
**Solusi**: Pastikan MySQL/XAMPP sudah running

### Tombol Login/Signup tidak bekerja
**Solusi**: Pastikan `main.js` sudah di-load di halaman

### Redirect tidak bekerja setelah login
**Solusi**: Periksa console browser untuk error JavaScript

