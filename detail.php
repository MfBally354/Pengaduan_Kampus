<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Cek login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include "db.php";

// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id == 0) {
    die("ID tidak valid!");
}

// Query pengaduan berdasarkan ID
$sql = "SELECT * FROM pengaduan WHERE id = $id";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) == 0) {
    die("Data pengaduan tidak ditemukan!");
}

$data = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengaduan</title>
    <link rel="stylesheet" href="assets/styles.css">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f7fa; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #333; margin-bottom: 20px; }
        .detail-item { margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 5px; }
        .detail-item strong { display: inline-block; width: 120px; color: #555; }
        .status { padding: 5px 15px; border-radius: 20px; font-size: 14px; font-weight: bold; }
        .status.pending { background: #ffc107; color: #856404; }
        .status.proses { background: #17a2b8; color: white; }
        .status.done { background: #28a745; color: white; }
        .back-btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
        .back-btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Detail Pengaduan</h2>
        
        <div class="detail-item">
            <strong>ID:</strong> <?= htmlspecialchars($data['id']) ?>
        </div>
        
        <div class="detail-item">
            <strong>Nama:</strong> <?= htmlspecialchars($data['nama']) ?>
        </div>
        
        <div class="detail-item">
            <strong>Email:</strong> <?= htmlspecialchars($data['email']) ?>
        </div>
        
        <div class="detail-item">
            <strong>Kategori:</strong> <?= htmlspecialchars($data['kategori']) ?>
        </div>
        
        <div class="detail-item">
            <strong>Tanggal:</strong> <?= htmlspecialchars($data['tanggal']) ?>
        </div>
        
        <div class="detail-item">
            <strong>Waktu:</strong> <?= htmlspecialchars($data['waktu']) ?>
        </div>
        
        <div class="detail-item">
            <strong>Status:</strong> 
            <span class="status <?= $data['status'] ?>">
                <?= strtoupper($data['status']) ?>
            </span>
        </div>
        
        <div class="detail-item">
            <strong>Uraian:</strong><br>
            <div style="margin-top: 10px; padding: 15px; background: white; border: 1px solid #ddd; border-radius: 5px;">
                <?= nl2br(htmlspecialchars($data['uraian'])) ?>
            </div>
        </div>
        
        <?php if (!empty($data['lampiran'])): ?>
        <div class="detail-item">
            <strong>Lampiran:</strong><br>
            <a href="<?= htmlspecialchars($data['lampiran']) ?>" target="_blank" style="color: #007bff;">
                📎 Lihat Lampiran
            </a>
        </div>
        <?php endif; ?>
        
        <a href="pending.php" class="back-btn">← Kembali</a>
        
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
        <a href="admin/update_status.php?id=<?= $data['id'] ?>" class="back-btn" style="background: #28a745;">
            ✏️ Update Status
        </a>
        <?php endif; ?>
    </div>
</body>
</html>
