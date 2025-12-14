<?php
require_once 'db.php';

// Jika sudah login, redirect ke dashboard sesuai role
if (isLoggedIn()) {
    $role = getRole();
    if ($role == 'admin') redirect('/admin/admin_dashboard.php');
    elseif ($role == 'dosen') redirect('/dosen/dashboard.php');
    elseif ($role == 'mahasiswa') redirect('/mahasiswa/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Email dan password harus diisi';
    } else {
        $stmt = $conn->prepare("SELECT id, nama, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                // Redirect berdasarkan role
                if ($user['role'] == 'admin') {
                    redirect('/admin/admin_dashboard.php');
                } elseif ($user['role'] == 'dosen') {
                    redirect('/dosen/dashboard.php');
                } elseif ($user['role'] == 'mahasiswa') {
                    redirect('/mahasiswa/dashboard.php');
                }
            } else {
                $error = 'Password salah';
            }
        } else {
            $error = 'Email tidak ditemukan';
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
    <title>Login - Sistem Pengaduan Kampus</title>
    <link rel="stylesheet" href="/assets/styles.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .btn-login:hover {
            background: #0056b3;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
        }
        .register-link a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login Sistem Pengaduan</h2>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="email@kampus.ac.id">
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Masukkan password">
            </div>
            
            <button type="submit" class="btn-login">Login</button>
        </form>
        
        <div class="register-link">
            Belum punya akun? <a href="/register.php">Daftar di sini</a>
        </div>
    </div>
</body>
</html>
