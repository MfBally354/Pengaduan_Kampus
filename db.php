
<?php
// db.php - Koneksi Database
session_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pengaduan_kampus');

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        throw new Exception("Koneksi gagal: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    die("Error Database: " . $e->getMessage());
}

// Fungsi helper
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getRole() {
    return $_SESSION['role'] ?? null;
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getUserName() {
    return $_SESSION['nama'] ?? 'Guest';
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function flashMessage($message, $type = 'success') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

function showFlash() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'];
        echo "<div class='alert alert-$type'>$message</div>";
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
    }
}

// Cek apakah user sudah login
function requireLogin() {
    if (!isLoggedIn()) {
        flashMessage('Silakan login terlebih dahulu', 'danger');
        redirect('/login.php');
    }
}

// Cek role user
function requireRole($allowedRoles) {
    requireLogin();
    if (!in_array(getRole(), (array)$allowedRoles)) {
        flashMessage('Anda tidak memiliki akses ke halaman ini', 'danger');
        redirect('/index.php');
    }
}
?>

