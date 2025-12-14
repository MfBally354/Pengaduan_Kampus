<?php
require_once '../db.php';
requireRole('admin');

// Ambil semua data pengaduan dengan detail lengkap
$query = "SELECT 
    p.id,
    p.judul,
    p.deskripsi,
    p.kategori,
    p.status,
    p.created_at,
    p.updated_at,
    p.tanggapan,
    p.tanggapan_at,
    u.nama as nama_mahasiswa,
    u.email as email_mahasiswa,
    u.nim,
    u.jurusan,
    u2.nama as nama_dosen
    FROM pengaduan p
    JOIN users u ON p.user_id = u.id
    LEFT JOIN users u2 ON p.tanggapan_by = u2.id
    ORDER BY p.created_at DESC";

$result = $conn->query($query);

// Set header untuk download Excel
$filename = "Laporan_Pengaduan_" . date('Y-m-d_His') . ".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4472C4;
            color: white;
            font-weight: bold;
        }
        .header-section {
            margin-bottom: 20px;
        }
        .status-pending { background-color: #FFC107; }
        .status-diproses { background-color: #17A2B8; color: white; }
        .status-selesai { background-color: #28A745; color: white; }
        .status-ditolak { background-color: #DC3545; color: white; }
    </style>
</head>
<body>
    <!-- Header Laporan -->
    <div class="header-section">
        <h1>LAPORAN PENGADUAN KAMPUS</h1>
        <p><strong>Tanggal Export:</strong> <?= date('d F Y H:i:s') ?></p>
        <p><strong>Total Data:</strong> <?= $result->num_rows ?> pengaduan</p>
        <hr>
    </div>
    
    <!-- Statistik Ringkasan -->
    <h2>STATISTIK RINGKASAN</h2>
    <table>
        <tr>
            <th>Kategori</th>
            <th>Jumlah</th>
        </tr>
        <?php
        $stats = $conn->query("SELECT 
            'Total Pengaduan' as kategori, COUNT(*) as jumlah FROM pengaduan
            UNION ALL
            SELECT CONCAT('Status: ', UPPER(status)), COUNT(*) FROM pengaduan GROUP BY status
            UNION ALL
            SELECT CONCAT('Kategori: ', UPPER(kategori)), COUNT(*) FROM pengaduan GROUP BY kategori
        ");
        while ($stat = $stats->fetch_assoc()):
        ?>
            <tr>
                <td><?= htmlspecialchars($stat['kategori']) ?></td>
                <td><?= $stat['jumlah'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
    
    <br><br>
    
    <!-- Data Detail Pengaduan -->
    <h2>DATA DETAIL PENGADUAN</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ID</th>
                <th>Tanggal Dibuat</th>
                <th>Judul</th>
                <th>Deskripsi</th>
                <th>Kategori</th>
                <th>Status</th>
                <th>Nama Mahasiswa</th>
                <th>Email Mahasiswa</th>
                <th>NIM</th>
                <th>Jurusan</th>
                <th>Tanggapan</th>
                <th>Ditanggapi Oleh</th>
                <th>Tanggal Tanggapan</th>
                <th>Terakhir Update</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($row = $result->fetch_assoc()): 
            ?>
                <tr class="status-<?= $row['status'] ?>">
                    <td><?= $no++ ?></td>
                    <td><?= $row['id'] ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                    <td><?= htmlspecialchars($row['judul']) ?></td>
                    <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                    <td><?= ucfirst($row['kategori']) ?></td>
                    <td><strong><?= ucfirst($row['status']) ?></strong></td>
                    <td><?= htmlspecialchars($row['nama_mahasiswa']) ?></td>
                    <td><?= htmlspecialchars($row['email_mahasiswa']) ?></td>
                    <td><?= htmlspecialchars($row['nim'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['jurusan'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['tanggapan'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['nama_dosen'] ?? '-') ?></td>
                    <td><?= $row['tanggapan_at'] ? date('d/m/Y H:i', strtotime($row['tanggapan_at'])) : '-' ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($row['updated_at'])) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <br><br>
    
    <!-- Footer -->
    <div class="header-section">
        <hr>
        <p><em>Laporan ini dibuat secara otomatis oleh Sistem Pengaduan Kampus</em></p>
        <p><em>Dicetak pada: <?= date('d F Y H:i:s') ?> oleh <?= htmlspecialchars(getUserName()) ?></em></p>
    </div>
</body>
</html>
<?php
// Tutup koneksi
$conn->close();
exit;
?>
