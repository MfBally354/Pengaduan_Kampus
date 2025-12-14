<?php
require_once '../db.php';
requireRole('dosen');

// Statistik pengaduan
$result = $conn->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'diproses' THEN 1 ELSE 0 END) as diproses,
    SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as selesai
    FROM pengaduan");
$stats = $result->fetch_assoc();

// Pengaduan terbaru
$pengaduan_list = $conn->query("SELECT p.*, u.nama as nama_mahasiswa 
    FROM pengaduan p 
    JOIN users u ON p.user_id = u.id 
    ORDER BY p.created_at DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dosen</title>
    <link rel="stylesheet" href="/assets/styles.css">
    <link rel="stylesheet" href="/assets/dosen.css">
    <style>
        .dashboard {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .stat-card h3 {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .stat-card .number {
            font-size: 36px;
            font-weight: bold;
            color: #28a745;
        }
        .pengaduan-table {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pending { background: #ffc107; color: white; }
        .status-diproses { background: #17a2b8; color: white; }
        .status-selesai { background: #28a745; color: white; }
        .status-ditolak { background: #dc3545; color: white; }
        .btn-view {
            padding: 6px 12px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 12px;
        }
        .btn-view:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <?php include '../navbar.php'; ?>
    
    <div class="dashboard">
        <h1>Dashboard Dosen</h1>
        <p>Selamat datang, <?= htmlspecialchars(getUserName()) ?>!</p>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Pengaduan</h3>
                <div class="number"><?= $stats['total'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Pending</h3>
                <div class="number" style="color: #ffc107;"><?= $stats['pending'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Diproses</h3>
                <div class="number" style="color: #17a2b8;"><?= $stats['diproses'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Selesai</h3>
                <div class="number" style="color: #28a745;"><?= $stats['selesai'] ?></div>
            </div>
        </div>
        
        <div class="pengaduan-table">
            <h2>Pengaduan Terbaru</h2>
            <?php if ($pengaduan_list->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Mahasiswa</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $pengaduan_list->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['judul']) ?></td>
                                <td><?= htmlspecialchars($row['nama_mahasiswa']) ?></td>
                                <td><?= ucfirst($row['kategori']) ?></td>
                                <td>
                                    <span class="status-badge status-<?= $row['status'] ?>">
                                        <?= ucfirst($row['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                                <td>
                                    <a href="/detail.php?id=<?= $row['id'] ?>" class="btn-view">Lihat</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <a href="lihat_pengaduan.php" style="display: block; text-align: center; margin-top: 20px;">
                    Lihat Semua Pengaduan →
                </a>
            <?php else: ?>
                <p style="text-align: center; color: #999; padding: 40px;">
                    Belum ada pengaduan
                </p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
