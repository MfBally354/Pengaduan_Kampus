<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include "db.php";
$q = mysqli_query($conn, "SELECT * FROM pengaduan WHERE status='pending' ORDER BY created_at DESC");
$total = mysqli_num_rows($q);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaduan Pending</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h2>Pengaduan Pending (<?= $total ?>)</h2>
            <a href="dashboard.php" class="back-link">← Kembali</a>
        </div>

        <?php if ($total > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while($d = mysqli_fetch_assoc($q)): 
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($d['nama']) ?></td>
                    <td><?= htmlspecialchars($d['kategori']) ?></td>
                    <td><?= date('d/m/Y', strtotime($d['tanggal'])) ?></td>
                    <td><?= date('H:i', strtotime($d['waktu'])) ?></td>
                    <td>
                        <a href="detail.php?id=<?= $d['id'] ?>" class="btn-detail">Detail</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-message">
            <h3>Tidak ada pengaduan pending</h3>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
