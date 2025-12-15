# Panduan Hosting ke Railway & Koneksi DBeaver

Berikut adalah langkah-langkah untuk menghosting aplikasi **FoodEdu** ke Railway dan mengelola database menggunakan **DBeaver**.

## 1. Persiapan Kode

Kode aplikasi telah saya update agar siap untuk di-deploy (`auth/config.php` sekarang mendukung Environment Variables).

1.  Pastikan semua perubahan kode sudah di-**Commit** dan di-**Push** ke repository GitHub Anda.
2.  File `.gitignore` sudah saya buat agar file sampah tidak ikut ter-upload.

## 2. Setup di Railway

1.  Buka [Railway.app](https://railway.app/) dan login (bisa pakai GitHub).
2.  Klik **New Project** -> **Deploy from GitHub repo**.
3.  Pilih repository **foodedu** Anda.
4.  Klik **Deploy Now**.
5.  Setelah project terbuat, kita perlu menambahkan database.
    *   Klik tombol **New** (atau klik kanan di area kosong kanvas) -> **Database** -> **MySQL**.
    *   Tunggu hingga service MySQL selesai di-provision (status hijau).

## 3. Menghubungkan Aplikasi dengan Database

Agar aplikasi PHP bisa "berbicara" dengan MySQL di Railway, kita perlu mengatur Environment Variables.

1.  Buka service **MySQL** di dashboard Railway.
2.  Masuk ke tab **Variables**. Anda akan melihat daftar variable seperti `MYSQLHOST`, `MYSQLUSER`, `MYSQLPASSWORD`, dll.
3.  **TIPS MUDAH**:
    *   Buka service **Aplikasi (foodedu)** -> tab **Variables**.
    *   Klik **New Variable**.
    *   Kita akan menambahkan variable-variable berikut (Anda bisa copy value dari service MySQL):
        *   `MYSQLHOST`: (Copy value `MYSQLHOST` dari service MySQL)
        *   `MYSQLDATABASE`: (Copy value `MYSQLDATABASE` dari service MySQL)
        *   `MYSQLUSER`: (Copy value `MYSQLUSER` dari service MySQL)
        *   `MYSQLPASSWORD`: (Copy value `MYSQLPASSWORD` dari service MySQL)
        *   `MYSQLPORT`: (Copy value `MYSQLPORT` dari service MySQL)

    *Catatan: Kode aplikasi Anda sudah saya modifikasi untuk otomatis membaca variable `MYSQL...` ini. Jadi Anda tidak perlu mengubah nama variable menjadi `FOODEDU_...`.*

4.  Setelah variable disimpan, Railway akan otomatis **Redeploy** aplikasi Anda. Tunggu hingga selesai.

## 4. Mengakses Database dengan DBeaver

Untuk melihat atau mengedit data database dari komputer Anda:

1.  Buka service **MySQL** di dashboard Railway.
2.  Masuk ke tab **Connect**.
3.  Lihat bagian **Public Networking**. Jika belum ada, klik tombol untuk mengaktifkannya (Public Domain).
4.  Catat informasi berikut:
    *   **Host**: (Contoh: `viaduct.proxy.rlwy.net`)
    *   **Port**: (Perhatikan port publik ini BEDA dengan port internal 3306. Contoh: `54321`)
    *   **User**: `root`
    *   **Password**: (Lihat di tab Variables)
    *   **Database**: `railway` (atau nama database yang ada)

5.  Buka aplikasi **DBeaver** di komputer Anda.
6.  Klik **New Database Connection** -> Pilih **MySQL**.
7.  Masukkan data yang dicatat tadi:
    *   Server Host: (Isi Domain Public Railway)
    *   Port: (Isi Port Public Railway)
    *   Database: (Isi nama database)
    *   Username: (Isi User)
    *   Password: (Isi Password)
8.  Klik **Test Connection**. Jika sukses, klik **Finish**.

## 5. Migrasi Database (Tabel)

Aplikasi FoodEdu memiliki fitur "Auto Migration" sederhana.
*   Saat Anda pertama kali mengakses aplikasi, kode akan mencoba membuat tabel (`users`, `gizi_items`, `kelayakan_items`) jika belum ada.
*   Jadi, Anda cukup buka URL aplikasi Railway Anda (contoh: `https://foodedu-production.up.railway.app`) di browser.
*   Cek DBeaver, refresh tabelnya. Tabel-tabel harusnya sudah muncul otomatis.

## Catatan Penting (Limitasi)

**File Uploads**: Railway menggunakan sistem file sementara (*ephemeral*).
*   Artinya, gambar yang Anda upload lewat fitur "Tambah Gizi/Kelayakan" akan **HILANG** jika aplikasi di-restart atau di-deploy ulang.
*   Untuk solusi permanen di masa depan, Anda perlu mengubah cara simpan gambar ke layanan "Object Storage" seperti AWS S3 atau Cloudinary (ini memerlukan coding tambahan lanjuran).
*   Untuk demo/tugas saat ini, ini mungkin tidak masalah, tapi harap diingat datanya bisa hilang.
