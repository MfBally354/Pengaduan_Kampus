<?php
session_start();
include 'db.php'; // db.php di root

// Proteksi halaman hanya untuk admin
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header('Location: login.php');
    exit;
}

// Statistik pengaduan
$total = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM pengaduan"))['total'];
$pending = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM pengaduan WHERE status='pending'"))['total'];
$proses = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM pengaduan WHERE status='proses'"))['total'];
$done = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM pengaduan WHERE status='done'"))['total'];

// Ambil semua pengaduan
$pengaduan = mysqli_query($conn,"SELECT * FROM pengaduan ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard — Eco Campus</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
/* DARK MODE PREMIUM */
*{margin:0;padding:0;box-sizing:border-box;font-family:'Inter',sans-serif;}
body{background:#111827;color:#f9fafb;line-height:1.6;}
a{text-decoration:none;color:inherit;}
button{cursor:pointer;}

/* NAVBAR */
nav{background:#1f2937;padding:16px 40px;display:flex;justify-content:space-between;align-items:center;color:white;position:sticky;top:0;z-index:1000;}
nav .logo{font-size:24px;font-weight:700;letter-spacing:1px;}
nav ul{display:flex;gap:20px;}
nav ul li{list-style:none;}
nav ul li a{color:white;font-weight:500;transition:0.2s;}
nav ul li a:hover{color:#4ade80;}

/* DASHBOARD CARDS */
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;margin:40px 20px;}
.card{background:#1f2937;border-radius:12px;padding:24px;box-shadow:0 8px 16px rgba(0,0,0,0.3);transition:0.3s;}
.card:hover{transform:translateY(-6px);box-shadow:0 12px 24px rgba(0,0,0,0.5);}
.card h3{font-size:20px;margin-bottom:8px;}
.card p{color:#9ca3af;}

/* TABLE STATUS */
.table-container{overflow-x:auto;margin:40px 20px;}
table{width:100%;border-collapse:collapse;background:#1f2937;border-radius:12px;overflow:hidden;box-shadow:0 8px 24px rgba(0,0,0,0.3);}
table th, table td{padding:14px 16px;text-align:left;}
table th{background:#111827;color:#f9fafb;font-weight:600;}
table tr:nth-child(even){background:#181b23;}
select{padding:6px;border-radius:6px;border:none;background:#111827;color:#f9fafb;}

/* FOOTER */
footer{text-align:center;padding:24px;background:#1f2937;color:white;margin-top:40px;}

/* RESPONSIVE */
@media(max-width:768px){nav ul{flex-direction:column;gap:12px;margin-top:12px;}}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav>
    <div class="logo">Eco Campus Admin</div>
    <ul>
        <li><a href="admin_dashboard.php">Dashboard</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<!-- DASHBOARD CARDS -->
<div class="cards">
    <div class="card"><h3>Total Pengaduan</h3><p><?= $total ?> laporan</p></div>
    <div class="card"><h3>Pending</h3><p><?= $pending ?> laporan</p></div>
    <div class="card"><h3>Proses</h3><p><?= $proses ?> laporan</p></div>
    <div class="card"><h3>Done</h3><p><?= $done ?> laporan</p></div>
</div>

<!-- TABLE PENGADUAN -->
<div class="table-container">
<table>
    <tr><th>ID</th><th>Nama</th><th>Email</th><th>Kategori</th><th>Status</th><th>Tanggal</th><th>Waktu</th><th>Aksi</th></tr>
    <?php while($row = mysqli_fetch_assoc($pengaduan)): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['nama'] ?></td>
        <td><?= $row['email'] ?></td>
        <td><?= $row['kategori'] ?></td>
        <td>
            <form method="post" action="admin/update_status.php">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <select name="status" onchange="this.form.submit()">
                    <option value="pending" <?= $row['status']=='pending'?'selected':'' ?>>Pending</option>
                    <option value="proses" <?= $row['status']=='proses'?'selected':'' ?>>Proses</option>
                    <option value="done" <?= $row['status']=='done'?'selected':'' ?>>Done</option>
                </select>
            </form>
        </td>
        <td><?= $row['tanggal'] ?></td>
        <td><?= $row['waktu'] ?></td>
        <td>
            <?php if($row['lampiran']): ?>
            <a href="assets/uploads/<?= $row['lampiran'] ?>" target="_blank" style="color:#4ade80;">Lihat Lampiran</a>
            <?php else: ?> -
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
</div>

<!-- EXPORT EXCEL -->
<div style="margin:20px;">
    <a href="admin/export_excel.php"><button style="padding:10px 20px;border-radius:8px;background:#4ade80;border:none;color:#111827;font-weight:600;">Export Excel</button></a>
</div>

<!-- FOOTER -->
<footer>© 2025 Eco Campus — Admin Dashboard</footer>

</body>
</html>
