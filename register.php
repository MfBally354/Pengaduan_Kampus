<?php
require_once 'db.php';

if (isLoggedIn()) {
    redirect('/index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';
    $nim = trim($_POST['nim'] ?? '');
    $nip = trim($_POST['nip'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');
    
    // Validasi
    if (empty($nama) || empty($email) || empty($password) || empty($role)) {
        $error = 'Semua field wajib diisi';
    } elseif ($password !== $confirm_password) {
        $error = 'Password tidak cocok';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter';
    } elseif ($role == 'mahasiswa' && empty($nim)) {
        $error = 'NIM harus diisi untuk mahasiswa';
    } elseif ($role == 'dosen' && empty($nip)) {
        $error = 'NIP harus diisi untuk dosen';
    } else {
        // Cek email sudah terdaftar
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'Email sudah terdaftar';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role, nim, nip, jurusan) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $nama, $email, $hashed_password, $role, $nim, $nip, $jurusan);
            
            if ($stmt->execute()) {
                $success = 'Registrasi berhasil! Silakan login.';
            } else {
                $error = 'Registrasi gagal. Coba lagi.';
            }
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
    <title>Register - Sistem Pengaduan Kampus</title>
    <link rel="stylesheet" href="/assets/styles.css">
    <style>
        .register-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .register-container h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn-register {
            width: 100%;
            padding: 12px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-register:hover {
            background: #218838;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .login-link {
            text-align: center;
            margin-top: 15px;
        }
        .conditional-field {
            display: none;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Registrasi Akun</h2>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" id="registerForm">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" required>
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label>Role</label>
                <select name="role" id="roleSelect" required>
                    <option value="">Pilih Role</option>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="dosen">Dosen</option>
                    <!-- Admin tidak bisa register, harus ditambahkan manual ke database -->
                </select>
            </div>
            
            <div class="form-group conditional-field" id="nimField">
                <label>NIM</label>
                <input type="text" name="nim">
            </div>
            
            <div class="form-group conditional-field" id="nipField">
                <label>NIP</label>
                <input type="text" name="nip">
            </div>
            
            <div class="form-group">
                <label>Jurusan/Fakultas</label>
                <input type="text" name="jurusan">
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="btn-register">Daftar</button>
        </form>
        
        <div class="login-link">
            Sudah punya akun? <a href="/login.php">Login di sini</a>
        </div>
    </div>
    
    <script>
        document.getElementById('roleSelect').addEventListener('change', function() {
            const nimField = document.getElementById('nimField');
            const nipField = document.getElementById('nipField');
            
            nimField.style.display = 'none';
            nipField.style.display = 'none';
            
            if (this.value === 'mahasiswa') {
                nimField.style.display = 'block';
            } else if (this.value === 'dosen') {
                nipField.style.display = 'block';
            }
        });
    </script>
</body>
</html>
