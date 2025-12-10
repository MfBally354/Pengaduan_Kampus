<?php
session_start();

// Cek apakah sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Include koneksi database
require_once 'db.php';

// Ambil data user
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - Eco Campus</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
/* GLOBAL STYLES */
* { margin:0; padding:0; box-sizing:border-box; font-family: 'Inter', sans-serif; }
body { background:#f5f7fa; color:#111827; line-height:1.6; }
a { text-decoration:none; color:inherit; }
button { cursor:pointer; }

/* NAVBAR */
nav { background:#1f2937; padding:16px 40px; display:flex; justify-content:space-between; align-items:center; color:white; position:sticky; top:0; z-index:1000; }
nav .logo { font-size:24px; font-weight:700; letter-spacing:1px; }
.user-info { display:flex; align-items:center; gap:15px; color:#4ade80; font-weight:500; }
nav ul { display:flex; gap:20px; }
nav ul li { list-style:none; }
nav ul li a { color:white; font-weight:500; transition:0.2s; padding:8px 16px; border-radius:6px; }
nav ul li a:hover { color:#4ade80; background:rgba(255,255,255,0.1); }

/* HERO SECTION */
.hero { background:linear-gradient(135deg,#2563eb,#1e40af); color:white; text-align:center; padding:100px 20px; }
.hero h1 { font-size:48px; font-weight:700; margin-bottom:16px; }
.hero p { font-size:18px; opacity:0.9; margin-bottom:24px; }
.hero button { background:#4ade80; border:none; padding:16px 32px; border-radius:10px; font-size:16px; font-weight:600; transition:0.3s; }
.hero button:hover { background:#22c55e; }

/* DASHBOARD CARDS */
.cards { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:20px; margin:40px 20px; }
.card { background:white; border-radius:12px; padding:24px; box-shadow:0 8px 16px rgba(0,0,0,0.1); transition:0.3s; }
.card:hover { transform:translateY(-6px); box-shadow:0 12px 24px rgba(0,0,0,0.15); }
.card h3 { font-size:20px; margin-bottom:8px; }
.card p { color:#6b7280; }

/* FORM PENGADUAN */
.form-container { max-width:700px; margin:40px auto; background:white; padding:32px; border-radius:14px; box-shadow:0 8px 24px rgba(0,0,0,0.1); }
.form-container h2 { margin-bottom:24px; font-size:24px; font-weight:700; }
.form-container input, .form-container select, .form-container textarea { width:100%; padding:14px 16px; margin-bottom:16px; border-radius:8px; border:1px solid #d1d5db; font-size:14px; transition:0.2s; }
.form-container input:focus, .form-container select:focus, .form-container textarea:focus { border-color:#2563eb; outline:none; }
.form-container button { background:#2563eb; color:white; border:none; padding:14px 28px; border-radius:10px; font-size:16px; font-weight:600; transition:0.3s; }
.form-container button:hover { background:#1e40af; }

/* TABLE STATUS */
.table-container { overflow-x:auto; margin:40px 20px; }
table { width:100%; border-collapse:collapse; background:white; border-radius:12px; overflow:hidden; box-shadow:0 8px 24px rgba(0,0,0,0.1); }
table th, table td { padding:14px 16px; text-align:left; }
table th { background:#1f2937; color:white; font-weight:600; }
table tr:nth-child(even) { background:#f9fafb; }
.status-pending { background:#fef3c7; color:#b45309; padding:6px 12px; border-radius:6px; font-weight:500; font-size:13px; }
.status-proses { background:#dbeafe; color:#1d4ed8; padding:6px 12px; border-radius:6px; font-weight:500; font-size:13px; }
.status-done { background:#dcfce7; color:#15803d; padding:6px 12px; border-radius:6px; font-weight:500; font-size:13px; }

/* FOOTER */
footer { text-align:center; padding:24px; background:#1f2937; color:white; margin-top:40px; }

/* RESPONSIVE */
@media(max-width:768px){ nav ul { flex-direction:column; gap:12px; margin-top:12px; } .hero h1{ font-size:36px; } }
</style>
</head>
<body>

<!-- NAVBAR -->
<nav>
    <div class="logo">Eco Campus</div>
    <div class="user-info">
        👤 <?php echo htmlspecialchars($username); ?>
    </div>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="submit.php">Buat Pengaduan</a></li>
        <li><a href="pending.php">Status</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<!-- HERO -->
<section class="hero">
    <h1>Selamat Datang di Eco Campus!</h1>
    <p>Halo, <?php echo htmlspecialchars($username); ?>! Sampaikan pengaduan fasilitas, administrasi, dan pelayanan kampus dengan cepat dan mudah</p>
    <a href="submit.php"><button>Buat Pengaduan</button></a>
</section>

<!-- DASHBOARD CARDS -->
<div class="cards">
    <div class="card"><h3>Total Pengaduan</h3><p>152 laporan</p></div>
    <div class="card"><h3>Pending</h3><p>34 laporan</p></div>
    <div class="card"><h3>Proses</h3><p>59 laporan</p></div>
    <div class="card"><h3>Done</h3><p>58 laporan</p></div>
</div>

<!-- FORM PENGADUAN -->
<div class="form-container">
    <h2>Form Pengaduan</h2>
    <form action="submit.php" method="POST">
        <input type="text" name="nama" placeholder="Nama Lengkap" value="<?php echo htmlspecialchars($username); ?>" required>
        <input type="email" name="email" placeholder="Email" required>
        <select name="kategori" required>
            <option value="">Kategori Pengaduan</option>
            <option value="Fasilitas">Fasilitas</option>
            <option value="Kebersihan">Kebersihan</option>
            <option value="Layanan Akademik">Layanan Akademik</option>
        </select>
        <textarea name="uraian" rows="5" placeholder="Uraian Pengaduan" required></textarea>
        <input type="file" name="lampiran">
        <button type="submit">Kirim Pengaduan</button>
    </form>
</div>

<!-- TABLE STATUS -->
<div class="table-container">
<table>
    <tr><th>ID</th><th>Nama</th><th>Kategori</th><th>Status</th><th>Tanggal</th></tr>
    <tr><td>1</td><td>Budi</td><td>Fasilitas</td><td><span class="status-pending">Pending</span></td><td>2025-12-09</td></tr>
    <tr><td>2</td><td>Siti</td><td>Kebersihan</td><td><span class="status-proses">Proses</span></td><td>2025-12-08</td></tr>
    <tr><td>3</td><td>Andi</td><td>Layanan Akademik</td><td><span class="status-done">Done</span></td></td><td>2025-12-07</td></tr>
</table>
</div>

<!-- FOOTER -->
<footer>© 2025 Eco Campus — All Rights Reserved</footer>

</body>
</html>
