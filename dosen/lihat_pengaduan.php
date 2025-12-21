<?php
require_once '../db.php';
requireRole('dosen');

// Filter
$filter_status = $_GET['status'] ?? '';
$filter_kategori = $_GET['kategori'] ?? '';
$search = $_GET['search'] ?? '';

// Build query dengan filter
$query = "SELECT p.*, u.nama as nama_mahasiswa, u.email, u.jurusan 
    FROM pengaduan p 
    JOIN users u ON p.user_id = u.id 
    WHERE 1=1";

$params = [];
$types = '';

if ($filter_status) {
    $query .= " AND p.status = ?";
    $params[] = $filter_status;
    $types .= 's';
}

if ($filter_kategori) {
    $query .= " AND p.kategori = ?";
    $params[] = $filter_kategori;
    $types .= 's';
}

if ($search) {
    $query .= " AND (p.judul LIKE ? OR p.deskripsi LIKE ? OR u.nama LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'sss';
}

$query .= " ORDER BY 
    CASE p.status 
        WHEN 'pending' THEN 1 
        WHEN 'diproses' THEN 2 
        WHEN 'selesai' THEN 3 
        WHEN 'ditolak' THEN 4 
    END, 
    p.created_at DESC";

$stmt = $conn->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$pengaduan_list = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Pengaduan - Dosen</title>
    <link rel="stylesheet" href="/assets/styles.css">
    <link rel="stylesheet" href="/assets/dosen.css">
    <style>
        .pengaduan-container {
            max-width: 1400px;
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
            grid-template-columns: 1fr 1fr 1fr 100px;
            gap: 15px;
            align-items: end;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        .filter-group label {
            margin-bottom: 5px;
            font-weight: 600;
            font-size: 14px;
        }
        .filter-group select, .filter-group input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .btn-filter {
            padding: 10px 20px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-filter:hover {
            background: #218838;
        }
        .btn-reset {
            padding: 10px 20px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            font-size: 14px;
            text-align: center;
        }
        .pengaduan-table {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
            position: sticky;
            top: 0;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
            white-space: nowrap;
        }
        .status-pending { background: #ffc107; color: #000; }
        .status-diproses { background: #17a2b8; color: white; }
        .status-selesai { background: #28a745; color: white; }
        .status-ditolak { background: #dc3545; color: white; }
        .kategori-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            background: #e9ecef;
            color: #495057;
        }
        .btn-action {
            padding: 6px 12px;
            margin: 2px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 12px;
            display: inline-block;
        }
        .btn-view {
            background: #007bff;
            color: white;
        }
        .btn-view:hover {
            background: #0056b3;
        }
        .btn-respond {
            background: #28a745;
            color: white;
        }
        .btn-respond:hover {
            background: #218838;
        }
        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        .stats-mini {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-mini {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stat-mini .number {
            font-size: 28px;
            font-weight: bold;
            color: #28a745;
        }
        .stat-mini .label {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <?php include '../navbar.php'; ?>
    
    <div class="pengaduan-container">
        <h1>📋 Lihat Semua Pengaduan</h1>
        
        <!-- Mini Statistics -->
        <div class="stats-mini">
            <?php
            $stats = $conn->query("SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'diproses' THEN 1 ELSE 0 END) as diproses,
                SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as selesai
                FROM pengaduan")->fetch_assoc();
            ?>
            <div class="stat-mini">
                <div class="number"><?= $stats['total'] ?></div>
                <div class="label">Total</div>
            </div>
            <div class="stat-mini">
                <div class="number" style="color: #ffc107;"><?= $stats['pending'] ?></div>
                <div class="label">Pending</div>
            </div>
            <div class="stat-mini">
                <div class="number" style="color: #17a2b8;"><?= $stats['diproses'] ?></div>
                <div class="label">Diproses</div>
            </div>
            <div class="stat-mini">
                <div class="number" style="color: #28a745;"><?= $stats['selesai'] ?></div>
                <div class="label">Selesai</div>
            </div>
        </div>
        
        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="">
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="">Semua Status</option>
                            <option value="pending" <?= $filter_status == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="diproses" <?= $filter_status == 'diproses' ? 'selected' : '' ?>>Diproses</option>
                            <option value="selesai" <?= $filter_status == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                            <option value="ditolak" <?= $filter_status == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>Kategori</label>
                        <select name="kategori">
                            <option value="">Semua Kategori</option>
                            <option value="akademik" <?= $filter_kategori == 'akademik' ? 'selected' : '' ?>>Akademik</option>
                            <option value="fasilitas" <?= $filter_kategori == 'fasilitas' ? 'selected' : '' ?>>Fasilitas</option>
                            <option value="administrasi" <?= $filter_kategori == 'administrasi' ? 'selected' : '' ?>>Administrasi</option>
                            <option value="lainnya" <?= $filter_kategori == 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>Cari</label>
                        <input type="text" name="search" placeholder="Cari judul, mahasiswa..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    
                    <div class="filter-group">
                        <button type="submit" class="btn-filter">🔍 Filter</button>
                    </div>
                </div>
            </form>
            <a href="lihat_pengaduan.php" class="btn-reset" style="margin-top: 10px;">🔄 Reset Filter</a>
        </div>
        
        <!-- Pengaduan Table -->
        <div class="pengaduan-table">
            <h2>Daftar Pengaduan</h2>
            <?php if ($pengaduan_list->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Judul</th>
                            <th>Mahasiswa</th>
                            <th>Jurusan</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $pengaduan_list->fetch_assoc()): ?>
                            <tr>
                                <td>#<?= $row['id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($row['judul']) ?></strong>
                                    <br>
                                    <small style="color: #666;">
                                        <?= htmlspecialchars(substr($row['deskripsi'], 0, 60)) ?>...
                                    </small>
                                </td>
                                <td>
                                    <?= htmlspecialchars($row['nama_mahasiswa']) ?>
                                    <br>
                                    <small style="color: #666;"><?= htmlspecialchars($row['email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($row['jurusan'] ?? '-') ?></td>
                                <td>
                                    <span class="kategori-badge">
                                        <?= ucfirst($row['kategori']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= $row['status'] ?>">
                                        <?= ucfirst($row['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= date('d/m/Y', strtotime($row['created_at'])) ?>
                                    <br>
                                    <small><?= date('H:i', strtotime($row['created_at'])) ?></small>
                                </td>
                                <td>
                                    <a href="/detail.php?id=<?= $row['id'] ?>" class="btn-action btn-view">
                                        👁️ Lihat
                                    </a>
                                    <?php if (!$row['tanggapan']): ?>
                                        <a href="beri_tanggapan.php?id=<?= $row['id'] ?>" class="btn-action btn-respond">
                                            💬 Tanggapi
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <h3>📭 Tidak ada pengaduan</h3>
                    <p>Tidak ada pengaduan yang sesuai dengan filter Anda</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
