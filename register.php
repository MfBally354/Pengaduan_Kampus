<?php
include 'db.php';

$error = '';

if (isset($_POST['register'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    // Validasi input
    if (empty($nama) || empty($email) || empty($password)) {
        $error = 'Semua field harus diisi!';
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        // Cek apakah email sudah terdaftar
        $cek = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        
        if (mysqli_num_rows($cek) > 0) {
            $error = 'Email sudah terdaftar!';
        } else {
            // Insert user baru
            $sql = "INSERT INTO users (nama, email, password) VALUES ('$nama', '$email', '$hashed_password')";
            
            if (mysqli_query($conn, $sql)) {
                // Auto login setelah registrasi (opsional)
                session_start();
                $_SESSION['user_id'] = mysqli_insert_id($conn);
                $_SESSION['username'] = $nama;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = 'mahasiswa'; // Default role
                
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Terjadi kesalahan: ' . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Eco Campus</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/styles.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Inter',sans-serif; }
        body { background:#f5f7fa; display:flex; justify-content:center; align-items:center; min-height:100vh; padding:20px; }
        .register-container { background:white; padding:48px; border-radius:20px; box-shadow:0 15px 35px rgba(0,0,0,0.1); width:100%; max-width:480px; }
        .register-header { text-align:center; margin-bottom:32px; }
        .register-header h1 { font-size:32px; color:#1f2937; margin-bottom:8px; }
        .register-header p { color:#6b7280; }
        .form-group { margin-bottom:20px; }
        .form-group label { display:block; margin-bottom:8px; color:#374151; font-weight:500; }
        .form-group input { width:100%; padding:14px 18px; border:2px solid #d1d5db; border-radius:10px; font-size:16px; transition:0.2s; }
        .form-group input:focus { border-color:#2563eb; outline:none; }
        .btn-register { width:100%; background:#10b981; color:white; border:none; padding:16px; border-radius:10px; font-size:16px; font-weight:600; transition:0.3s; margin-top:8px; }
        .btn-register:hover { background:#059669; }
        .error { background:#fee2e2; color:#dc2626; padding:12px; border-radius:8px; margin-bottom:20px; text-align:center; }
        .links { text-align:center; margin-top:24px; }
        .links a { color:#2563eb; text-decoration:none; }
        .links a:hover { text-decoration:underline; }
        .password-requirements { font-size:14px; color:#6b7280; margin-top:4px; }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>Daftar Akun Baru</h1>
            <p>Bergabung dengan sistem pengaduan kampus</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="6">
                <div class="password-requirements">Minimal 6 karakter</div>
            </div>
            
            <button type="submit" name="register" class="btn-register">Daftar Sekarang</button>
        </form>
        
        <div class="links">
            <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
            <p><a href="index.php">← Kembali ke Beranda</a></p>
        </div>
    </div>
</body>
</html>
