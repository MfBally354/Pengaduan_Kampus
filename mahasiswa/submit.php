<?php
require_once '../db.php';
requireRole('mahasiswa');

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $kategori = $_POST['kategori'] ?? '';
    $user_id = getUserId();
    
    if (empty($judul) || empty($deskripsi) || empty($kategori)) {
        $error = 'Semua field harus diisi';
    } else {
        $stmt = $conn->prepare("INSERT INTO pengaduan (user_id, judul, deskripsi, kategori) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $judul, $deskripsi, $kategori);
        
        if ($stmt->execute()) {
            $success = 'Pengaduan berhasil dikirim!';
            $_SESSION['flash_message'] = 'Pengaduan berhasil dikirim!';
            $_SESSION['flash_type'] = 'success';
            redirect('dashboard.php');
        } else {
            $error = 'Gagal mengirim pengaduan. Coba lagi.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Pengaduan</title>
    <link rel="stylesheet" href="/assets/styles.css">
    <style>
        .submit-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }
        .btn-submit {
            padding: 12px 30px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-submit:hover {
            background: #0056b3;
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
    
    <div class="submit-container">
        <h1>Buat Pengaduan Baru</h1>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Judul Pengaduan <span style="color: red;">*</span></label>
                <input type="text" name="judul" required placeholder="Masukkan judul pengaduan yang jelas">
            </div>
            
            <div class="form-group">
                <label>Kategori <span style="color: red;">*</span></label>
                <select name="kategori" required>
                    <option value="">Pilih Kategori</option>
                    <option value="akademik">Akademik</option>
                    <option value="fasilitas">Fasilitas</option>
                    <option value="administrasi">Administrasi</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Deskripsi Pengaduan <span style="color: red;">*</span></label>
                <textarea name="deskripsi" required placeholder="Jelaskan pengaduan Anda secara detail..."></textarea>
            </div>
            
            <div>
                <button type="submit" class="btn-submit">Kirim Pengaduan</button>
                <a href="dashboard.php" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>

