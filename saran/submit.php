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
$saran_dan_masukan = isset($_POST['saran_dan_masukan']) ? trim($_POST['saran_dan_masukan']) : '';
$alasan = isset($_POST['alasan']) ? trim($_POST['alasan']) : '';
$user_id = $_SESSION['user_id'];

// Validation
$errors = [];
if (empty($nama_lengkap)) $errors[] = 'Nama lengkap harus diisi';
if (empty($nama_sekolah)) $errors[] = 'Nama sekolah harus diisi';
if (empty($saran_dan_masukan)) $errors[] = 'Saran dan masukan harus diisi';
if (empty($alasan)) $errors[] = 'Alasan harus diisi';

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

try {
    // Insert into database
    $stmt = $pdo->prepare("
        INSERT INTO saran 
        (user_id, nama_lengkap, nama_sekolah, saran_dan_masukan, alasan, status, created_at)
        VALUES (:user_id, :nama_lengkap, :nama_sekolah, :saran_dan_masukan, :alasan, 'pending', NOW())
    ");
    
    $stmt->execute([
        'user_id' => $user_id,
        'nama_lengkap' => $nama_lengkap,
        'nama_sekolah' => $nama_sekolah,
        'saran_dan_masukan' => $saran_dan_masukan,
        'alasan' => $alasan
    ]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Saran berhasil dikirim! Terima kasih atas masukan Anda.'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>

