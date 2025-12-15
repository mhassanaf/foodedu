<?php
header('Content-Type: application/json; charset=utf-8');
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'null';
header("Access-Control-Allow-Origin: $origin");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once __DIR__ . '/config.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// get JSON body if sent
$inputRaw = file_get_contents('php://input');
$data = json_decode($inputRaw, true);
if (!$data) {
    // fallback to form-encoded
    $data = $_POST;
}

// basic validation
$required = ['name', 'email', 'phone', 'role', 'username', 'password'];
foreach ($required as $r) {
    if (empty($data[$r])) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => "Field {$r} is required"]);
        exit;
    }
}

$name = trim($data['name']);
$email = filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL);
$phone = trim($data['phone']);
$role = trim($data['role']);
$username = trim($data['username']);
$password = $data['password'];

// optional extra fields
$sekolah = isset($data['sekolah']) ? trim($data['sekolah']) : null;
$anak = isset($data['anak']) ? trim($data['anak']) : null;
$nip = isset($data['nip']) ? trim($data['nip']) : null;
$idk = isset($data['idk']) ? trim($data['idk']) : null;

if (!$email) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Email tidak valid']);
    exit;
}

// check unique username/email
try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email LIMIT 1");
    $stmt->execute(['username' => $username, 'email' => $email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Username atau email sudah digunakan']);
        exit;
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $insert = $pdo->prepare("
    INSERT INTO users (name,email,phone,role,sekolah,anak,nip,idk,username,password_hash)
    VALUES (:name,:email,:phone,:role,:sekolah,:anak,:nip,:idk,:username,:password_hash)
");


    $insert->execute([
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'role' => $role,
        'sekolah' => $sekolah,
        'anak' => $anak,
        'nip' => $nip,
        'idk' => $idk,
        'username' => $username,
        'password_hash' => $password_hash
    ]);

    echo json_encode(['success' => true, 'message' => 'Registrasi berhasil']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
