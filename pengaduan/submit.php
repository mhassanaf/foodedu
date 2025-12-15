<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../auth/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
    exit;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get form data
$nama_lengkap = isset($_POST['nama_lengkap']) ? trim($_POST['nama_lengkap']) : '';
$nama_sekolah = isset($_POST['nama_sekolah']) ? trim($_POST['nama_sekolah']) : '';
$tanggal_kejadian = isset($_POST['tanggal_kejadian']) ? trim($_POST['tanggal_kejadian']) : '';
$jenis_pengaduan = isset($_POST['jenis_pengaduan']) ? trim($_POST['jenis_pengaduan']) : '';
$deskripsi = isset($_POST['deskripsi']) ? trim($_POST['deskripsi']) : '';
$user_id = $_SESSION['user_id'];

// Validation
$errors = [];
if (empty($nama_lengkap)) $errors[] = 'Nama lengkap harus diisi';
if (empty($nama_sekolah)) $errors[] = 'Nama sekolah harus diisi';
if (empty($tanggal_kejadian)) $errors[] = 'Tanggal kejadian harus diisi';
if (empty($jenis_pengaduan)) $errors[] = 'Jenis pengaduan harus dipilih';
if (empty($deskripsi)) $errors[] = 'Deskripsi pengaduan harus diisi';

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

// Handle file upload
$bukti_path = null;
if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['bukti'];
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowed_types)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Format file tidak didukung. Gunakan JPG, PNG, atau PDF']);
        exit;
    }
    
    if ($file['size'] > $max_size) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Ukuran file terlalu besar. Maksimal 5MB']);
        exit;
    }
    
    // Create upload directory if not exists
    $upload_dir = __DIR__ . '/../uploads/pengaduan/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Generate unique filename
    $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $file_name = 'pengaduan_' . time() . '_' . uniqid() . '.' . $file_ext;
    $file_path = $upload_dir . $file_name;
    
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        $bukti_path = 'uploads/pengaduan/' . $file_name;
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Gagal mengupload file']);
        exit;
    }
}

try {
    // Insert into database
    $stmt = $pdo->prepare("
        INSERT INTO pengaduan 
        (user_id, nama_lengkap, nama_sekolah, tanggal_kejadian, jenis_pengaduan, deskripsi, bukti_path, status, created_at)
        VALUES (:user_id, :nama_lengkap, :nama_sekolah, :tanggal_kejadian, :jenis_pengaduan, :deskripsi, :bukti_path, 'pending', NOW())
    ");
    
    $stmt->execute([
        'user_id' => $user_id,
        'nama_lengkap' => $nama_lengkap,
        'nama_sekolah' => $nama_sekolah,
        'tanggal_kejadian' => $tanggal_kejadian,
        'jenis_pengaduan' => $jenis_pengaduan,
        'deskripsi' => $deskripsi,
        'bukti_path' => $bukti_path
    ]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Pengaduan berhasil dikirim! Terima kasih atas laporan Anda.'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>

