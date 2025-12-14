<?php
require_once '../db.php';
requireRole('dosen');

$id = $_GET['id'] ?? 0;
$user_id = getUserId();

// Ambil detail pengaduan
$stmt = $conn->prepare("SELECT p.*, u.nama as nama_mahasiswa 
    FROM pengaduan p 
    JOIN users u ON p.user_id = u.id 
    WHERE p.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Pengaduan tidak ditemukan");
}

$pengaduan = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggapan = trim($_POST['tanggapan'] ?? '');
    $status = $_POST['status'] ?? '';
    
    if (empty($tanggapan) || empty($status)) {
        $error = 'Tanggapan dan status harus diisi';
    } else {
        $stmt = $conn->prepare("UPDATE pengaduan 
            SET tanggapan = ?, status = ?, tanggapan_by = ?, tanggapan_at = NOW() 
            WHERE id = ?");
        $stmt->bind_param("ssii", $tanggapan, $status, $user_id, $id);
        
        if ($stmt->execute()) {
            flashMessage('Tanggapan berhasil dikirim', 'success');
            redirect('/detail.php?id=' . $id);
        } else {
            $error = 'Gagal mengirim tanggapan';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Beri Tanggapan</title>
    <link rel="stylesheet" href="/assets/styles.css">
    <style>
        .tanggapan-container {
            max-width: 900px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .pengaduan-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            min-height: 150px;
            resize: vertical;
        }
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn-submit {
            padding: 12px 30px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-cancel {
            padding: 12px 30px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-left: 10px;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include '../navbar.php'; ?>
    
    <div class="tanggapan-container">
        <h1>Beri Tanggapan Pengaduan</h1>
        
        <div class="pengaduan-info">
            <h3><?= htmlspecialchars($pengaduan['judul']) ?></h3>
            <p><strong>Pengadu:</strong> <?= htmlspecialchars($pengaduan['nama_mahasiswa']) ?></p>
            <p><strong>Kategori:</strong> <?= ucfirst($pengaduan['kategori']) ?></p>
            <p><strong>Deskripsi:</strong></p>
            <p><?= nl2br(htmlspecialchars($pengaduan['deskripsi'])) ?></p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Status Pengaduan <span style="color: red;">*</span></label>
                <select name="status" required>
                    <option value="">Pilih Status</option>
                    <option value="diproses" <?= $pengaduan['status'] == 'diproses' ? 'selected' : '' ?>>Diproses</option>
                    <option value="selesai">Selesai</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Tanggapan <span style="color: red;">*</span></label>
                <textarea name="tanggapan" required placeholder="Berikan tanggapan Anda terhadap pengaduan ini..."><?= htmlspecialchars($pengaduan['tanggapan'] ?? '') ?></textarea>
            </div>
            
            <div>
                <button type="submit" class="btn-submit">Kirim Tanggapan</button>
                <a href="/detail.php?id=<?= $id ?>" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>
