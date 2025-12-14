<?php
require_once '../db.php';
requireRole('mahasiswa');

$user_id = getUserId();

// Statistik pengaduan
$stmt = $conn->prepare("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'diproses' THEN 1 ELSE 0 END) as diproses,
    SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as selesai
    FROM pengaduan WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Pengaduan terbaru
$stmt = $conn->prepare("SELECT * FROM pengaduan WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pengaduan_list = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa</title>
    <link rel="stylesheet" href="/assets/styles.css">
    <link rel="stylesheet" href="/assets/mahasiswa.css">
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
            color: #007bff;
        }
        .pengaduan-list {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .pengaduan-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .pengaduan-item:last-child {
            border-bottom: none;
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
        .btn-submit {
            display: inline-block;
            padding: 12px 24px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .btn-submit:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <?php include '../navbar.php'; ?>
    
    <div class="dashboard">
        <h1>Dashboard Mahasiswa</h1>
        <p>Selamat datang, <?= htmlspecialchars(getUserName()) ?>!</p>
        
        <a href="submit.php" class="btn-submit">+ Buat Pengaduan Baru</a>
        
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
        
        <div class="pengaduan-list">
            <h2>Pengaduan Terbaru</h2>
            <?php if ($pengaduan_list->num_rows > 0): ?>
                <?php while ($row = $pengaduan_list->fetch_assoc()): ?>
                    <div class="pengaduan-item">
                        <h3><?= htmlspecialchars($row['judul']) ?></h3>
                        <p><?= htmlspecialchars(substr($row['deskripsi'], 0, 100)) ?>...</p>
                        <span class="status-badge status-<?= $row['status'] ?>">
                            <?= ucfirst($row['status']) ?>
                        </span>
                        <small style="margin-left: 10px; color: #666;">
                            <?= date('d M Y H:i', strtotime($row['created_at'])) ?>
                        </small>
                        <a href="/detail.php?id=<?= $row['id'] ?>" style="float: right;">Lihat Detail</a>
                    </div>
                <?php endwhile; ?>
                <a href="pengaduan_saya.php" style="display: block; text-align: center; margin-top: 20px;">
                    Lihat Semua Pengaduan →
                </a>
            <?php else: ?>
                <p style="text-align: center; color: #999; padding: 40px;">
                    Belum ada pengaduan. <a href="submit.php">Buat pengaduan pertama Anda</a>
                </p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
