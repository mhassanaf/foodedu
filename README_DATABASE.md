# Setup Database FoodEdu

## Instruksi Setup

### 1. Buat Database
Jalankan file `database.sql` di phpMyAdmin atau MySQL:

```sql
-- Import file database.sql melalui phpMyAdmin
-- atau jalankan perintah berikut di MySQL:
mysql -u root -p < database.sql
```

### 2. Konfigurasi Database
File konfigurasi ada di `auth/config.php`:

```php
$DB_HOST = '127.0.0.1';
$DB_NAME = 'foodedu';
$DB_USER = 'root';
$DB_PASS = ''; // untuk XAMPP default kosong
```

**Pastikan:**
- Database `foodedu` sudah dibuat
- Username dan password sesuai dengan konfigurasi MySQL/XAMPP Anda
- Tabel `users` sudah dibuat

### 3. Struktur Tabel Users
Tabel `users` memiliki kolom:
- `id` (INT, AUTO_INCREMENT, PRIMARY KEY)
- `name` (VARCHAR 255)
- `email` (VARCHAR 255, UNIQUE)
- `phone` (VARCHAR 20)
- `role` (ENUM: 'siswa', 'ortu', 'sekolah', 'mbg')
- `username` (VARCHAR 100, UNIQUE)
- `password_hash` (VARCHAR 255)
- `sekolah` (VARCHAR 255, optional)
- `anak` (VARCHAR 255, optional)
- `nip` (VARCHAR 50, optional)
- `idk` (VARCHAR 50, optional)
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

### 4. Testing
1. Buka `auth.html` di browser
2. Daftar akun baru dengan role apapun
3. Login dengan username dan password yang baru dibuat
4. Sistem akan redirect ke dashboard sesuai role:
   - `siswa` → `dashboard/siswa.php`
   - `ortu` → `dashboard/orangtua.php`
   - `sekolah` → `dashboard/sekolah.php`
   - `mbg` → `dashboard/mbg.php`

### 5. Troubleshooting
- **Error koneksi database**: Pastikan MySQL/XAMPP sudah running
- **Error "Table doesn't exist"**: Import file `database.sql`
- **Error "Access denied"**: Periksa username/password di `auth/config.php`

