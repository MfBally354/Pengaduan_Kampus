<?php
// Baris PALING ATAS
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Debug: cek session
if (isset($_SESSION['user_id'])) {
    error_log("User sudah login, redirect ke dashboard");
    header('Location: dashboard.php');
    exit;
}

require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Debug input
    error_log("Login attempt: " . print_r($_POST, true));
    
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $error = "Email dan password harus diisi!";
    } else {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = $_POST['password'];
        
        // Debug query
        error_log("Query email: $email");
        
        $sql = "SELECT id, nama, email, password, role FROM users WHERE email = '$email'";
        error_log("SQL: $sql");
        
        $result = mysqli_query($conn, $sql);
        
        if (!$result) {
            // Debug error query
            $error = "Error query: " . mysqli_error($conn);
            error_log("MySQL Error: " . mysqli_error($conn));
        } elseif (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result); 
            error_log("User found: " . print_r($user, true));
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['nama'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'] ?? 'mahasiswa';
                
                error_log("Login successful, redirecting to dashboard");
                header('Location: dashboard.php');
                exit;
            } else {
                $error = "Password salah!";
                error_log("Password verification failed");
            }
        } else {
            $error = "Email tidak ditemukan!";
            error_log("Email not found: $email");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Eco Campus</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* CSS tetap sama */
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Inter',sans-serif; }
        body { background:#f5f7fa; display:flex; justify-content:center; align-items:center; min-height:100vh; padding:20px; }
        .login-container { background:white; padding:48px; border-radius:20px; box-shadow:0 15px 35px rgba(0,0,0,0.1); width:100%; max-width:480px; }
        .login-header { text-align:center; margin-bottom:32px; }
        .login-header h1 { font-size:32px; color:#1f2937; margin-bottom:8px; }
        .login-header p { color:#6b7280; }
        .form-group { margin-bottom:24px; }
        .form-group label { display:block; margin-bottom:8px; color:#374151; font-weight:500; }
        .form-group input { width:100%; padding:14px 18px; border:2px solid #d1d5db; border-radius:10px; font-size:16px; transition:0.2s; }
        .form-group input:focus { border-color:#2563eb; outline:none; }
        .btn-login { width:100%; background:#2563eb; color:white; border:none; padding:16px; border-radius:10px; font-size:16px; font-weight:600; transition:0.3s; margin-top:8px; }
        .btn-login:hover { background:#1e40af; }
        .error { background:#fee2e2; color:#dc2626; padding:12px; border-radius:8px; margin-bottom:20px; text-align:center; }
        .links { text-align:center; margin-top:24px; }
        .links a { color:#2563eb; text-decoration:none; }
        .links a:hover { text-decoration:underline; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Login ke Eco Campus</h1>
            <p>Masuk dengan email Anda</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required autofocus value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn-login">Login</button>
        </form>
        
        <div class="links">
            <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
            <p><a href="index.php">← Kembali ke Beranda</a></p>
        </div>
    </div>
    
    <?php
    // Debug info di footer (hanya untuk development)
    if (isset($_SESSION['debug'])) {
        echo "<div style='margin-top:20px; padding:10px; background:#f0f0f0; border-radius:5px;'>";
        echo "<h4>Debug Info:</h4>";
        echo "<pre>POST: " . print_r($_POST, true) . "</pre>";
        echo "<pre>SESSION: " . print_r($_SESSION, true) . "</pre>";
        echo "</div>";
    }
    ?>
</body>
</html>
