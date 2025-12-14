<!-- FILE: detail.php -->
<?php
require_once 'db.php';
requireLogin();

$id = $_GET['id'] ?? 0;
$user_id = getUserId();
$role = getRole();

// Ambil detail pengaduan
$stmt = $conn->prepare("SELECT p.*, u.nama as nama_mahasiswa, u.email, 
    u2.nama as nama_dosen 
    FROM pengaduan p 
    JOIN users u ON p.user_id = u.id 
    LEFT JOIN users u2 ON p.tanggapan_by = u2.id 
    WHERE p.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Pengaduan tidak ditemukan");
}

$pengaduan = $result->fetch_assoc();

// Cek akses - mahasiswa hanya bisa lihat pengaduan sendiri
if ($role == 'mahasiswa' && $pengaduan['user_id'] != $user_id) {
    die("Anda tidak memiliki akses ke pengaduan ini");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Pengaduan</title>
    <link rel="stylesheet" href="/assets/styles.css">
    <style>
        .detail-container {
            max-width: 900px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .detail-header {
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 15px;
        }
        .info-label {
            font-weight: 600;
            width: 150px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 5px;
            font-weight: bold;
        }
        .status-pending { background: #ffc107; color: white; }
        .status-diproses { background: #17a2b8; color: white; }
        .status-selesai { background: #28a745; color: white; }
        .status-ditolak { background: #dc3545; color: white; }
        .tanggapan-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }
        .btn-back {
            display: inline-block;
            padding: 10px 20px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn-tanggapi {
            display: inline-block;
            padding: 10px 20px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="detail-container">
        <div class="detail-header">
            <h1><?= htmlspecialchars($pengaduan['judul']) ?></h1>
        </div>
        
        <div class="info-row">
            <span class="info-label">Status:</span>
            <span class="status-badge status-<?= $pengaduan['status'] ?>">
                <?= ucfirst($pengaduan['status']) ?>
            </span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Kategori:</span>
            <span><?= ucfirst($pengaduan['kategori']) ?></span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Pengadu:</span>
            <span><?= htmlspecialchars($pengaduan['nama_mahasiswa']) ?></span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Tanggal:</span>
            <span><?= date('d F Y H:i', strtotime($pengaduan['created_at'])) ?></span>
        </div>
        
        <div style="margin-top: 30px;">
            <h3>Deskripsi Pengaduan</h3>
            <p style="line-height: 1.8;"><?= nl2br(htmlspecialchars($pengaduan['deskripsi'])) ?></p>
        </div>
        
        <?php if ($pengaduan['tanggapan']): ?>
            <div class="tanggapan-section">
                <h3>Tanggapan</h3>
                <p style="line-height: 1.8;"><?= nl2br(htmlspecialchars($pengaduan['tanggapan'])) ?></p>
                <small style="color: #666;">
                    Ditanggapi oleh: <?= htmlspecialchars($pengaduan['nama_dosen'] ?? 'Admin') ?> 
                    pada <?= date('d F Y H:i', strtotime($pengaduan['tanggapan_at'])) ?>
                </small>
            </div>
        <?php endif; ?>
        
        <?php if ($role == 'dosen' && !$pengaduan['tanggapan']): ?>
            <a href="/dosen/beri_tanggapan.php?id=<?= $id ?>" class="btn-tanggapi">
                Beri Tanggapan
            </a>
        <?php endif; ?>
        
        <a href="javascript:history.back()" class="btn-back">← Kembali</a>
    </div>
</body>
</html>
