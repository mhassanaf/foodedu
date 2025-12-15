<?php
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'null';
header("Access-Control-Allow-Origin: $origin");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }
session_start();

// Return user data function
function getCurrentUser() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'role' => $_SESSION['role'],
        'name' => $_SESSION['name']
    ];
}

// If called directly (not included), return JSON for AJAX
if (basename($_SERVER['PHP_SELF']) === 'check_session.php') {
    header('Content-Type: application/json; charset=utf-8');
    
    $user = getCurrentUser();
    if ($user) {
        echo json_encode([
            'logged_in' => true,
            'user' => $user,
            'username' => $user['username'],
            'name' => $user['name'],
            'role' => $user['role']
        ]);
    } else {
        echo json_encode(['logged_in' => false]);
    }
    exit;
}

// If included, check session and redirect if not logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    // Only redirect if not called via AJAX
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        header('Location: ../auth.html');
        exit;
    }
}
?>
