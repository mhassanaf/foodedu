<?php
// FoodEdu Router
// Mengatur routing untuk Railway (PHP Built-in Server)

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// 1. Jika request adalah file (gambar, css, js) dan filenya ada, sajikan langsung.
if (is_file(__DIR__ . $uri)) {
    return false;
}

// 2. Jika request halaman root, sajikan index.html
if ($uri === '/' || $uri === '/index.php') {
    require __DIR__ . '/index.html';
    exit;
}

// 3. Jika file .php atau .html yang diminta ada, sajikan.
$file = __DIR__ . $uri;
if (is_file($file)) {
    // Deteksi mime type sederhana untuk HTML/PHP
    if (str_ends_with($file, '.php')) {
        require $file;
    } else {
        // Untuk file statis lain (html) yang tidak tertangkap di cek #1
        readfile($file);
    }
    exit;
}

// 4. Fallback 404 (Opsional, atau sajikan index.html)
http_response_code(404);
echo "404 Not Found: " . htmlspecialchars($uri);
?>