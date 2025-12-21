<?php
require_once '../db.php';
requireRole('admin');

// Statistik Umum
$stats = $conn->query("SELECT 
    (SELECT COUNT(*) FROM users WHERE role='mahasiswa') as total_mahasiswa,
    (SELECT COUNT(*) FROM users WHERE role='dosen') as total_dosen,
    (SELECT COUNT(*) FROM pengaduan) as total_pengaduan,
    (SELECT COUNT(*) FROM pengaduan WHERE status='pending') as pending,
    (SELECT COUNT(*) FROM pengaduan WHERE status='diproses') as diproses,
    (SELECT COUNT(*) FROM pengaduan WHERE status='selesai') as selesai,
    (SELECT COUNT(*) FROM pengaduan WHERE status='ditolak') as ditolak
")->fetch_assoc();

// Statistik per Kategori
$kategori_stats = $conn->query("SELECT kategori, COUNT(*) as total 
    FROM pengaduan GROUP BY kategori ORDER BY total DESC");

// Pengaduan Terbaru
$pengaduan_terbaru = $conn->query("SELECT p.*, u.nama as nama_mahasiswa, u.email 
    FROM pengaduan p 
    JOIN users u ON p.user_id = u.id 
    ORDER BY p.created_at DESC LIMIT 10");

// User Terbaru
$user_terbaru = $conn->query("SELECT * FROM users 
    WHERE role IN ('mahasiswa', 'dosen') 
    ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="/assets/styles.css">
    <style>
        body {
            background: #f5f5f5;
        }
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .admin-header h1 {
            margin: 0;
            font-size: 32px;
        }
        .admin-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card .icon {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .stat-card .number {
            font-size: 36px;
            font-weight: bold;
            margin: 10px 0;
        }
        .stat-card .label {
            color: #666;
            font-size: 14px;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        .action-btn {
            display: flex;
            align-items: center;
            padding: 20px;
            background: white;
            border-radius: 10px;
            text-decoration: none;
            color: #333;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .action-btn .icon {
            font-size: 30px;
            margin-right: 15px;
        }
        .action-btn .text h3 {
            margin: 0;
            font-size: 16px;
        }
        .action-btn .text p {
            margin: 5px 0 0 0;
            font-size: 12px;
            color: #666;
        }
        
        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        /* Card */
        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card h2 {
            margin: 0 0 20px 0;
            font-size: 20px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        
        /* Table */
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
            font-size: 13px;
        }
        tr:hover {
            background: #f8f9fa;
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .status-pending { background: #ffc107; color: #000; }
        .status-diproses { background: #17a2b8; color: white; }
        .status-selesai { background: #28a745; color: white; }
        .status-ditolak { background: #dc3545; color: white; }
        
        /* Role Badge */
        .role-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .role-mahasiswa { background: #007bff; color: white; }
        .role-dosen { background: #28a745; color: white; }
        
        /* Chart */
        .chart-container {
            margin-top: 20px;
        }
        .chart-bar {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .chart-label {
            width: 120px;
            font-size: 14px;
            font-weight: 600;
        }
        .chart-progress {
            flex: 1;
            height: 30px;
            background: #e9ecef;
            border-radius: 5px;
            overflow: hidden;
            position: relative;
        }
        .chart-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 10px;
            color: white;
            font-weight: bold;
            font-size: 12px;
        }
        
        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include '../navbar.php'; ?>
    
    <div class="admin-container">
        <!-- Header -->
        <div class="admin-header">
            <h1>👨‍💼 Dashboard Administrator</h1>
            <p>Selamat datang, <?= htmlspecialchars(getUserName()) ?>! Kelola sistem pengaduan kampus.</p>
        </div>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon">👨‍🎓</div>
                <div class="number" style="color: #007bff;"><?= $stats['total_mahasiswa'] ?></div>
                <div class="label">Total Mahasiswa</div>
            </div>
            <div class="stat-card">
                <div class="icon">👨‍🏫</div>
                <div class="number" style="color: #28a745;"><?= $stats['total_dosen'] ?></div>
                <div class="label">Total Dosen</div>
            </div>
            <div class="stat-card">
                <div class="icon">📋</div>
                <div class="number" style="color: #667eea;"><?= $stats['total_pengaduan'] ?></div>
                <div class="label">Total Pengaduan</div>
            </div>
            <div class="stat-card">
                <div class="icon">⏳</div>
                <div class="number" style="color: #ffc107;"><?= $stats['pending'] ?></div>
                <div class="label">Pending</div>
            </div>
            <div class="stat-card">
                <div class="icon">🔄</div>
                <div class="number" style="color: #17a2b8;"><?= $stats['diproses'] ?></div>
                <div class="label">Diproses</div>
            </div>
            <div class="stat-card">
                <div class="icon">✅</div>
                <div class="number" style="color: #28a745;"><?= $stats['selesai'] ?></div>
                <div class="label">Selesai</div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="export_excel.php" class="action-btn" style="background: #28a745; color: white;">
                <div class="icon">📊</div>
                <div class="text">
                    <h3>Export ke Excel</h3>
                    <p>Download laporan lengkap</p>
                </div>
            </a>
            <a href="/dosen/lihat_pengaduan.php" class="action-btn">
                <div class="icon">📋</div>
                <div class="text">
                    <h3>Lihat Semua Pengaduan</h3>
                    <p>Kelola pengaduan mahasiswa</p>
                </div>
            </a>
            <a href="update_status.php" class="action-btn">
                <div class="icon">⚙️</div>
                <div class="text">
                    <h3>Update Status</h3>
                    <p>Ubah status pengaduan</p>
                </div>
            </a>
        </div>
        
        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Pengaduan Terbaru -->
            <div class="card">
                <h2>📋 Pengaduan Terbaru</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Mahasiswa</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($p = $pengaduan_terbaru->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <a href="/detail.php?id=<?= $p['id'] ?>" style="color: #007bff; text-decoration: none;">
                                        <?= htmlspecialchars(substr($p['judul'], 0, 40)) ?>...
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($p['nama_mahasiswa']) ?></td>
                                <td>
                                    <span class="status-badge status-<?= $p['status'] ?>">
                                        <?= ucfirst($p['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Sidebar -->
            <div>
                <!-- Statistik Kategori -->
                <div class="card">
                    <h2>📊 Statistik Kategori</h2>
                    <div class="chart-container">
                        <?php 
                        $max = $conn->query("SELECT MAX(cnt) as max FROM (SELECT COUNT(*) as cnt FROM pengaduan GROUP BY kategori) as t")->fetch_assoc()['max'];
                        $kategori_stats->data_seek(0);
                        while ($k = $kategori_stats->fetch_assoc()): 
                            $percentage = $max > 0 ? ($k['total'] / $max) * 100 : 0;
                        ?>
                            <div class="chart-bar">
                                <div class="chart-label"><?= ucfirst($k['kategori']) ?></div>
                                <div class="chart-progress">
                                    <div class="chart-fill" style="width: <?= $percentage ?>%">
                                        <?= $k['total'] ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                
                <!-- User Terbaru -->
                <div class="card" style="margin-top: 20px;">
                    <h2>👥 User Terbaru</h2>
                    <table>
                        <tbody>
                            <?php while ($u = $user_terbaru->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($u['nama']) ?></strong>
                                        <br>
                                        <small style="color: #666;"><?= htmlspecialchars($u['email']) ?></small>
                                    </td>
                                    <td>
                                        <span class="role-badge role-<?= $u['role'] ?>">
                                            <?= ucfirst($u['role']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
