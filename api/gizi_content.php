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

require_once __DIR__ . '/../auth/config.php';
require_once __DIR__ . '/../auth/check_session.php';

$method = $_SERVER['REQUEST_METHOD'];

// Handle JSON input or Form input
$inputRaw = file_get_contents('php://input');
$data = json_decode($inputRaw, true);
if (!$data)
    $data = $_REQUEST; // This picks up $_POST for multipart forms

// ====== AUTO CREATE TABLES (tidak mengubah tabel lama) ======
try {
    // Tabel kartu informasi gizi (grid: nasi, mie, dll)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS gizi_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(120) NOT NULL UNIQUE,
            color ENUM('green','orange') NOT NULL DEFAULT 'green',
            image_url VARCHAR(255) NOT NULL,
            category VARCHAR(100) DEFAULT NULL,
            sort_order INT NOT NULL DEFAULT 0,
            description TEXT,
            portion_size VARCHAR(50),
            energy VARCHAR(50),
            fat VARCHAR(50),
            protein VARCHAR(50),
            carbs VARCHAR(50),
            sodium VARCHAR(50),
            calcium VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Tabel edukasi kelayakan (good/bad pair per jenis makanan)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS kelayakan_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            food_name VARCHAR(100) NOT NULL,
            good_title VARCHAR(150) NOT NULL,
            good_image_url VARCHAR(255) NOT NULL,
            good_points TEXT NOT NULL,
            bad_title VARCHAR(150) NOT NULL,
            bad_image_url VARCHAR(255) NOT NULL,
            bad_points TEXT NOT NULL,
            sort_order INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Gagal inisialisasi tabel konten', 'error' => $e->getMessage()]);
    exit;
}

// Helper: cek role MBG
function require_mbg()
{
    $user = getCurrentUser();
    if (!$user || $user['role'] !== 'mbg') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Hanya akun MBG yang dapat mengelola konten.']);
        exit;
    }
    return $user;
}

// Helper: Handle File Upload
function handleUpload($fileKey)
{
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
        return null; // No file uploaded or error
    }

    $file = $_FILES[$fileKey];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($ext, $allowed)) {
        throw new Exception("Format file tidak valid. Gunakan: " . implode(', ', $allowed));
    }

    // Generate unique name
    $filename = uniqid('img_') . '.' . $ext;
    $targetDir = __DIR__ . '/../uploads/';

    if (!is_dir($targetDir))
        mkdir($targetDir, 0755, true);

    if (move_uploaded_file($file['tmp_name'], $targetDir . $filename)) {
        return 'uploads/' . $filename;
    }

    throw new Exception("Gagal menyimpan file ke server.");
}

// ====== HANDLER GET (PUBLIC) ======
if ($method === 'GET') {
    $type = isset($_GET['type']) ? $_GET['type'] : 'all';
    try {
        if ($type === 'gizi') {
            $stmt = $pdo->query("SELECT * FROM gizi_items ORDER BY sort_order ASC, id ASC");
            $items = $stmt->fetchAll();
            echo json_encode(['success' => true, 'type' => 'gizi', 'items' => $items]);
        } elseif ($type === 'kelayakan') {
            $stmt = $pdo->query("SELECT * FROM kelayakan_items ORDER BY sort_order ASC, id ASC");
            $items = $stmt->fetchAll();
            echo json_encode(['success' => true, 'type' => 'kelayakan', 'items' => $items]);
        } else {
            $gizi = $pdo->query("SELECT * FROM gizi_items ORDER BY sort_order ASC, id ASC")->fetchAll();
            $kel = $pdo->query("SELECT * FROM kelayakan_items ORDER BY sort_order ASC, id ASC")->fetchAll();
            echo json_encode([
                'success' => true,
                'type' => 'all',
                'gizi' => $gizi,
                'kelayakan' => $kel
            ]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Gagal mengambil data konten', 'error' => $e->getMessage()]);
    }
    exit;
}

// ====== HANDLER WRITE (MBG ONLY) ======
if (in_array($method, ['POST', 'PUT', 'DELETE'], true)) {
    $user = require_mbg();
}

$action = isset($data['action']) ? $data['action'] : '';

try {
    switch ($action) {
        // ---------- GIZI ITEMS ----------
        case 'create_gizi':
            $name = trim($data['name'] ?? '');
            $slug = trim($data['slug'] ?? '');
            $color = $data['color'] ?? 'green';
            $category = trim($data['category'] ?? '');
            $sort = intval($data['sort_order'] ?? 0);

            // Nutrition Data
            $desc = trim($data['description'] ?? '');
            $portion = trim($data['portion_size'] ?? '');
            $energy = trim($data['energy'] ?? '');
            $fat = trim($data['fat'] ?? '');
            $prot = trim($data['protein'] ?? '');
            $carb = trim($data['carbs'] ?? '');
            $sod = trim($data['sodium'] ?? '');
            $calc = trim($data['calcium'] ?? '');

            // Upload Image
            $image = handleUpload('image_file');
            if (!$image) {
                // Fallback check if URL string was sent
                $image = trim($data['image_url'] ?? '');
            }

            if (!$name || !$slug || !$image) {
                throw new Exception('Nama, slug, dan gambar wajib diisi.');
            }
            $stmt = $pdo->prepare("INSERT INTO gizi_items (name, slug, color, image_url, category, sort_order, description, portion_size, energy, fat, protein, carbs, sodium, calcium) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$name, $slug, $color, $image, $category ?: null, $sort, $desc, $portion, $energy, $fat, $prot, $carb, $sod, $calc]);
            echo json_encode(['success' => true, 'message' => 'Item gizi berhasil dibuat.']);
            break;

        case 'update_gizi':
            $id = intval($data['id'] ?? 0);
            if (!$id)
                throw new Exception('ID tidak valid.');
            $name = trim($data['name'] ?? '');
            $slug = trim($data['slug'] ?? '');
            $color = $data['color'] ?? 'green';
            $category = trim($data['category'] ?? '');
            $sort = intval($data['sort_order'] ?? 0);

            // Nutrition Data
            $desc = trim($data['description'] ?? '');
            $portion = trim($data['portion_size'] ?? '');
            $energy = trim($data['energy'] ?? '');
            $fat = trim($data['fat'] ?? '');
            $prot = trim($data['protein'] ?? '');
            $carb = trim($data['carbs'] ?? '');
            $sod = trim($data['sodium'] ?? '');
            $calc = trim($data['calcium'] ?? '');

            // Check for new image
            $newImage = handleUpload('image_file');

            // Base SQL
            $sql = "UPDATE gizi_items SET name=?, slug=?, color=?, category=?, sort_order=?, description=?, portion_size=?, energy=?, fat=?, protein=?, carbs=?, sodium=?, calcium=?";
            $params = [$name, $slug, $color, $category ?: null, $sort, $desc, $portion, $energy, $fat, $prot, $carb, $sod, $calc];

            if ($newImage) {
                $sql .= ", image_url=?";
                $params[] = $newImage;
            } elseif (!empty($data['image_url'])) {
                // Manually updated text URL
                $sql .= ", image_url=?";
                $params[] = $data['image_url'];
            }

            $sql .= " WHERE id=?";
            $params[] = $id;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            echo json_encode(['success' => true, 'message' => 'Item gizi berhasil diperbarui.']);
            break;

        case 'delete_gizi':
            $id = intval($data['id'] ?? 0);
            if (!$id)
                throw new Exception('ID tidak valid.');
            $stmt = $pdo->prepare("DELETE FROM gizi_items WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Item gizi berhasil dihapus.']);
            break;

        // ---------- KELAYAKAN ITEMS ----------
        case 'create_kelayakan':
            $food = trim($data['food_name'] ?? '');
            $good_title = trim($data['good_title'] ?? '');
            $bad_title = trim($data['bad_title'] ?? '');
            $good_points = trim($data['good_points'] ?? '');
            $bad_points = trim($data['bad_points'] ?? '');
            $sort = intval($data['sort_order'] ?? 0);

            $good_image = handleUpload('good_image_file');
            if (!$good_image)
                $good_image = trim($data['good_image_url'] ?? '');

            $bad_image = handleUpload('bad_image_file');
            if (!$bad_image)
                $bad_image = trim($data['bad_image_url'] ?? '');

            if (!$food || !$good_title || !$good_image || !$bad_title || !$bad_image) {
                throw new Exception('Nama makanan, judul & gambar (layak/tidak layak) wajib diisi.');
            }
            $stmt = $pdo->prepare("
                INSERT INTO kelayakan_items 
                    (food_name, good_title, good_image_url, good_points, bad_title, bad_image_url, bad_points, sort_order)
                VALUES (?,?,?,?,?,?,?,?)
            ");
            $stmt->execute([$food, $good_title, $good_image, $good_points, $bad_title, $bad_image, $bad_points, $sort]);
            echo json_encode(['success' => true, 'message' => 'Item kelayakan berhasil dibuat.']);
            break;

        case 'update_kelayakan':
            $id = intval($data['id'] ?? 0);
            if (!$id)
                throw new Exception('ID tidak valid.');

            $food = trim($data['food_name'] ?? '');
            $good_title = trim($data['good_title'] ?? '');
            $bad_title = trim($data['bad_title'] ?? '');
            $good_points = trim($data['good_points'] ?? '');
            $bad_points = trim($data['bad_points'] ?? '');
            $sort = intval($data['sort_order'] ?? 0);

            $newGood = handleUpload('good_image_file');
            $newBad = handleUpload('bad_image_file');

            // Build Update Query
            $sql = "UPDATE kelayakan_items SET food_name=?, good_title=?, bad_title=?, good_points=?, bad_points=?, sort_order=?";
            $params = [$food, $good_title, $bad_title, $good_points, $bad_points, $sort];

            if ($newGood) {
                $sql .= ", good_image_url=?";
                $params[] = $newGood;
            } elseif (!empty($data['good_image_url'])) {
                $sql .= ", good_image_url=?";
                $params[] = $data['good_image_url'];
            }

            if ($newBad) {
                $sql .= ", bad_image_url=?";
                $params[] = $newBad;
            } elseif (!empty($data['bad_image_url'])) {
                $sql .= ", bad_image_url=?";
                $params[] = $data['bad_image_url'];
            }

            $sql .= " WHERE id=?";
            $params[] = $id;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            echo json_encode(['success' => true, 'message' => 'Item kelayakan berhasil diperbarui.']);
            break;

        case 'delete_kelayakan':
            $id = intval($data['id'] ?? 0);
            if (!$id)
                throw new Exception('ID tidak valid.');
            $stmt = $pdo->prepare("DELETE FROM kelayakan_items WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Item kelayakan berhasil dihapus.']);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Aksi tidak dikenal.']);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>