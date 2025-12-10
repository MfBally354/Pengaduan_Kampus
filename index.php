<?php
session_start();
// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eco Campus — Sistem Pengaduan Kampus</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* GLOBAL STYLES */
        * { margin:0; padding:0; box-sizing:border-box; font-family: 'Inter', sans-serif; }
        body { background:#f5f7fa; color:#111827; line-height:1.6; }
        a { text-decoration:none; color:inherit; }
        
        /* NAVBAR LANDING */
        .landing-nav { background:#1f2937; padding:16px 40px; display:flex; justify-content:space-between; align-items:center; color:white; }
        .landing-nav .logo { font-size:24px; font-weight:700; letter-spacing:1px; }
        .nav-buttons { display:flex; gap:12px; }
        .btn-outline { border:2px solid #4ade80; color:#4ade80; padding:10px 24px; border-radius:8px; font-weight:600; transition:0.3s; }
        .btn-outline:hover { background:#4ade80; color:white; }
        .btn-primary { background:#2563eb; color:white; padding:10px 24px; border-radius:8px; font-weight:600; transition:0.3s; }
        .btn-primary:hover { background:#1e40af; }
        
        /* HERO LANDING */
        .hero-landing { background:linear-gradient(135deg,#2563eb,#1e40af); color:white; text-align:center; padding:120px 20px; }
        .hero-landing h1 { font-size:52px; font-weight:800; margin-bottom:20px; }
        .hero-landing p { font-size:20px; opacity:0.9; max-width:700px; margin:0 auto 32px; }
        .btn-large { background:#4ade80; color:white; border:none; padding:18px 40px; border-radius:12px; font-size:18px; font-weight:700; transition:0.3s; display:inline-block; }
        .btn-large:hover { background:#22c55e; transform:translateY(-3px); }
        
        /* FEATURES */
        .features { display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:30px; max-width:1200px; margin:80px auto; padding:0 20px; }
        .feature-card { background:white; padding:32px; border-radius:16px; box-shadow:0 10px 25px rgba(0,0,0,0.08); text-align:center; transition:0.3s; }
        .feature-card:hover { transform:translateY(-8px); box-shadow:0 15px 35px rgba(0,0,0,0.12); }
        .feature-card h3 { font-size:22px; margin-bottom:12px; color:#1f2937; }
        .feature-card p { color:#6b7280; }
        
        /* CTA SECTION */
        .cta { text-align:center; padding:80px 20px; background:#f0f9ff; }
        .cta h2 { font-size:36px; margin-bottom:16px; color:#1f2937; }
        .cta-buttons { display:flex; gap:16px; justify-content:center; margin-top:32px; }
        
        /* FOOTER */
        footer { text-align:center; padding:32px; background:#1f2937; color:white; margin-top:80px; }
    </style>
</head>
<body>
    <!-- NAVBAR LANDING -->
    <nav class="landing-nav">
        <div class="logo">Eco Campus</div>
        <div class="nav-buttons">
            <a href="login.php" class="btn-primary">Login</a>
            <a href="register.php" class="btn-outline">Daftar</a>
        </div>
    </nav>

    <!-- HERO LANDING -->
    <section class="hero-landing">
        <h1>Sistem Pengaduan Kampus Terpadu</h1>
        <p>Laporkan masalah fasilitas, kebersihan, dan layanan kampus dengan mudah, cepat, dan transparan</p>
        <a href="register.php" class="btn-large">Mulai Laporkan Sekarang</a>
    </section>

    <!-- FEATURES -->
    <div class="features">
        <div class="feature-card">
            <h3>📋 Buat Pengaduan</h3>
            <p>Laporkan masalah dengan form yang mudah diisi, lengkap dengan lampiran foto</p>
        </div>
        <div class="feature-card">
            <h3>📊 Pantau Status</h3>
            <p>Pantau perkembangan pengaduan Anda secara real-time dari pending hingga selesai</p>
        </div>
        <div class="feature-card">
            <h3>🔔 Notifikasi</h3>
            <p>Dapatkan pemberitahuan ketika status pengaduan Anda berubah</p>
        </div>
    </div>

    <!-- CTA SECTION -->
    <section class="cta">
        <h2>Siap Membuat Kampus Lebih Baik?</h2>
        <p>Bergabung dengan ratusan mahasiswa yang telah menggunakan sistem kami</p>
        <div class="cta-buttons">
            <a href="register.php" class="btn-large">Daftar Sekarang</a>
            <a href="login.php" class="btn-outline" style="padding:18px 40px;">Login</a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <p>© 2025 Eco Campus — Sistem Pengaduan Kampus. All Rights Reserved.</p>
    </footer>
</body>
</html>
