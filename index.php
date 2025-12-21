<?php
require_once 'db.php';

// Jika sudah login, redirect ke dashboard sesuai role
if (isLoggedIn()) {
    $role = getRole();
    if ($role == 'admin') redirect('/admin/admin_dashboard.php');
    elseif ($role == 'dosen') redirect('/dosen/dashboard.php');
    elseif ($role == 'mahasiswa') redirect('/mahasiswa/dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pengaduan Kampus</title>
    <link rel="stylesheet" href="/assets/styles.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 20px;
            text-align: center;
        }
        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }
        .hero p {
            font-size: 20px;
            margin-bottom: 40px;
            opacity: 0.9;
        }
        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
        }
        .btn {
            padding: 15px 40px;
            font-size: 18px;
            border-radius: 8px;
            text-decoration: none;
            transition: transform 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .btn-primary {
            background: white;
            color: #667eea;
            font-weight: bold;
        }
        .btn-secondary {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 2px solid white;
        }
        .features {
            max-width: 1200px;
            margin: 80px auto;
            padding: 0 20px;
        }
        .features h2 {
            text-align: center;
            font-size: 36px;
            margin-bottom: 50px;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }
        .feature-card {
            text-align: center;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .feature-icon {
            font-size: 50px;
            margin-bottom: 20px;
        }
        .feature-card h3 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #333;
        }
        .feature-card p {
            color: #666;
            line-height: 1.6;
        }
        .stats {
            background: #f8f9fa;
            padding: 60px 20px;
            text-align: center;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            max-width: 1000px;
            margin: 0 auto;
        }
        .stat-item {
            background: white;
            padding: 30px;
            border-radius: 10px;
        }
        .stat-number {
            font-size: 48px;
            font-weight: bold;
            color: #667eea;
        }
        .stat-label {
            color: #666;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="hero">
        <h1>🎓 Sistem Pengaduan Kampus</h1>
        <p>Platform terpadu untuk menyampaikan aspirasi dan keluhan mahasiswa</p>
        <div class="cta-buttons">
            <a href="/login.php" class="btn btn-primary">Login</a>
            <a href="/register.php" class="btn btn-secondary">Daftar Sekarang</a>
        </div>
    </div>
    
    <div class="features">
        <h2>Fitur Unggulan</h2>
        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon">📝</div>
                <h3>Mudah Digunakan</h3>
                <p>Interface yang sederhana dan intuitif memudahkan mahasiswa menyampaikan pengaduan</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>Respon Cepat</h3>
                <p>Pengaduan akan segera ditindaklanjuti oleh dosen dan admin kampus</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>Aman & Terenkripsi</h3>
                <p>Data pengaduan Anda dijamin keamanannya dengan sistem enkripsi</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Tracking Status</h3>
                <p>Pantau status pengaduan Anda secara real-time dari dashboard</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💬</div>
                <h3>Komunikasi 2 Arah</h3>
                <p>Dapatkan feedback dan tanggapan langsung dari pihak kampus</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📈</div>
                <h3>Laporan Lengkap</h3>
                <p>Admin dapat mengekspor data pengaduan untuk analisis lebih lanjut</p>
            </div>
        </div>
    </div>
    
    <div class="stats">
        <h2>Statistik Platform</h2>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">
                    <?php
                    $result = $conn->query("SELECT COUNT(*) as total FROM pengaduan");
                    echo $result->fetch_assoc()['total'];
                    ?>
                </div>
                <div class="stat-label">Total Pengaduan</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">
                    <?php
                    $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='mahasiswa'");
                    echo $result->fetch_assoc()['total'];
                    ?>
                </div>
                <div class="stat-label">Mahasiswa Terdaftar</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">
                    <?php
                    $result = $conn->query("SELECT COUNT(*) as total FROM pengaduan WHERE status='selesai'");
                    echo $result->fetch_assoc()['total'];
                    ?>
                </div>
                <div class="stat-label">Pengaduan Selesai</div>
            </div>
        </div>
    </div>
    
    <footer style="background: #333; color: white; text-align: center; padding: 30px;">
        <p>&copy; 2024 Sistem Pengaduan Kampus. All rights reserved.</p>
    </footer>
</body>
</html>
