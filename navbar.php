<?php
// navbar.php - Universal Navigation
if (!isset($_SESSION)) {
    session_start();
}

$role = $_SESSION['role'] ?? 'guest';
$nama = $_SESSION['nama'] ?? 'Guest';
?>
<style>
    .navbar {
        background: #007bff;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .navbar-brand {
        color: white;
        font-size: 20px;
        font-weight: bold;
        text-decoration: none;
    }
    .navbar-menu {
        display: flex;
        gap: 20px;
        align-items: center;
    }
    .navbar-menu a {
        color: white;
        text-decoration: none;
        padding: 8px 16px;
        border-radius: 5px;
        transition: background 0.3s;
    }
    .navbar-menu a:hover {
        background: rgba(255,255,255,0.2);
    }
    .navbar-user {
        color: white;
        font-weight: 600;
    }
    .btn-logout {
        background: #dc3545;
        padding: 8px 16px;
        border-radius: 5px;
    }
    .btn-logout:hover {
        background: #c82333;
    }
</style>

<nav class="navbar">
    <a href="/index.php" class="navbar-brand">🎓 Pengaduan Kampus</a>
    
    <div class="navbar-menu">
        <?php if ($role == 'mahasiswa'): ?>
            <a href="/mahasiswa/dashboard.php">Dashboard</a>
            <a href="/mahasiswa/submit.php">Buat Pengaduan</a>
            <a href="/mahasiswa/pengaduan_saya.php">Pengaduan Saya</a>
        <?php elseif ($role == 'dosen'): ?>
            <a href="/dosen/dashboard.php">Dashboard</a>
            <a href="/dosen/lihat_pengaduan.php">Lihat Pengaduan</a>
        <?php elseif ($role == 'admin'): ?>
            <a href="/admin/admin_dashboard.php">Dashboard Admin</a>
        <?php endif; ?>
        
        <?php if (isLoggedIn()): ?>
            <span class="navbar-user">👤 <?= htmlspecialchars($nama) ?></span>
            <a href="/logout.php" class="btn-logout">Logout</a>
        <?php else: ?>
            <a href="/login.php">Login</a>
            <a href="/register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>
