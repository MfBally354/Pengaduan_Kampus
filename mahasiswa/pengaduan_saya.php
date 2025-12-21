<?php
require_once '../db.php';
requireRole('mahasiswa');

$user_id = getUserId();

// Filter
$filter_status = $_GET['status'] ?? '';
$filter_kategori = $_GET['kategori'] ?? '';

// Build query
$query = "SELECT * FROM pengaduan WHERE user_id = ?";
$params = [$user_id];
$types = 'i';

if ($filter_status) {
    $query .= " AND status = ?";
    $params[] = $filter_status;
    $types .= 's';
}

if ($filter_kategori) {
    $query .= " AND kategori = ?";
    $params[] = $filter_kategori;
    $types .= 's';
}

$query .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$pengaduan_list = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaduan Saya</title>
    <link rel="stylesheet" href="/assets/styles.css">
    <link rel="stylesheet" href="/assets/mahasiswa.css">
    <style>
        .pengaduan-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .filter-row {
            display: grid;
            grid-template-columns: 1fr 1fr 150px;
            gap: 15px;
            align-items: end;
        }
        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .filter-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn-filter {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .pengaduan-grid {
            display: grid;
            gap: 20px;
        }
        .pengaduan-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .pengaduan-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }
        .card-title {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }
        .card-badges {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pending { background: #ffc107; color: #000; }
        .status-diproses { background: #17a2b8; color: white; }
        .status-selesai { background: #28a745; color: white; }
        .status-ditolak { background: #dc3545; color: white; }
        .kategori-badge {
            background: #e9ecef;
            padding: 6px 12px;
            border-radius: 5px;
            font-size: 12px;
            color: #495057;
        }
        .card-body {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        .card-date {
            color: #999;
            font-size: 14px;
        }
        .btn-detail {
            padding: 8px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }
        .btn-detail:hover {
            background: #0056b3;
        }
        .no-data {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 10px;
            color: #999;
        }
        .btn-new {
            display: inline-block;
            padding: 12px 30px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .tanggapan-preview {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
            border-left: 4px solid #28a745;
        }
        .tanggapan-preview strong {
            color: #28a745;
        }
    </style>
</head>
<body>
    <?php include '../navbar.php'; ?>
    
    <div class="pengaduan-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1>📋 Pengaduan Saya</h1>
            <a href="submit.php" class="btn-new">+ Buat Pengaduan Baru</a>
        </div>
        
        <!-- Filter -->
        <div class="filter-section">
            <form method="GET" action="">
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Filter Status</label>
                        <select name="status">
                            <option value="">Semua Status</option>
                            <option value="pending" <?= $filter_status == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="diproses" <?= $filter_status == 'diproses' ? 'selected' : '' ?>>Diproses</option>
                            <option value="selesai" <?= $filter_status == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                            <option value="ditolak" <?= $filter_status == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>Filter Kategori</label>
                        <select name="kategori">
                            <option value="">Semua Kategori</option>
                            <option value="akademik" <?= $filter_kategori == 'akademik' ? 'selected' : '' ?>>Akademik</option>
                            <option value="fasilitas" <?= $filter_kategori == 'fasilitas' ? 'selected' : '' ?>>Fasilitas</option>
                            <option value="administrasi" <?= $filter_kategori == 'administrasi' ? 'selected' : '' ?>>Administrasi</option>
                            <option value="lainnya" <?= $filter_kategori == 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <button type="submit" class="btn-filter">🔍 Filter</button>
                    </div>
                </div>
            </form>
            <a href="pengaduan_saya.php" style="color: #007bff; margin-top: 10px; display: inline-block;">🔄 Reset Filter</a>
        </div>
        
        <!-- Pengaduan List -->
        <div class="pengaduan-grid">
            <?php if ($pengaduan_list->num_rows > 0): ?>
                <?php while ($row = $pengaduan_list->fetch_assoc()): ?>
                    <div class="pengaduan-card">
                        <div class="card-header">
                            <div>
                                <div class="card-title"><?= htmlspecialchars($row['judul']) ?></div>
                            </div>
                            <div class="card-badges">
                                <span class="kategori-badge"><?= ucfirst($row['kategori']) ?></span>
                                <span class="status-badge status-<?= $row['status'] ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <?= nl2br(htmlspecialchars(substr($row['deskripsi'], 0, 200))) ?>
                            <?= strlen($row['deskripsi']) > 200 ? '...' : '' ?>
                        </div>
                        
                        <?php if ($row['tanggapan']): ?>
                            <div class="tanggapan-preview">
                                <strong>💬 Sudah Ditanggapi:</strong><br>
                                <?= nl2br(htmlspecialchars(substr($row['tanggapan'], 0, 150))) ?>
                                <?= strlen($row['tanggapan']) > 150 ? '...' : '' ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-footer">
                            <div class="card-date">
                                📅 <?= date('d F Y', strtotime($row['created_at'])) ?> | 
                                🕐 <?= date('H:i', strtotime($row['created_at'])) ?>
                            </div>
                            <a href="/detail.php?id=<?= $row['id'] ?>" class="btn-detail">
                                Lihat Detail →
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-data">
                    <h2>📭 Belum Ada Pengaduan</h2>
                    <p>Anda belum membuat pengaduan apapun</p>
                    <a href="submit.php" class="btn-new" style="margin-top: 20px;">
                        + Buat Pengaduan Pertama
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
