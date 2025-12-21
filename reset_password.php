<?php
include "db.php";

if(isset($_POST['reset'])){
    $email = $_POST['email'];
    $newpass = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $q = mysqli_query($conn, "UPDATE users SET password='$newpass' WHERE email='$email'");

    if(mysqli_affected_rows($conn)>0){
        echo "<script>alert('Password berhasil direset'); location='index.php';</script>";
    } else {
        echo "<script>alert('Email tidak ditemukan');</script>";
    }
}
?>
<html>
<head>
<title>Reset Password</title>
<link rel="stylesheet" href="assets/styles.css">
</head>
<body>

<h2>Reset Password</h2>

<form method="POST">
<input type="email" name="email" placeholder="Masukkan Email" required>
<input type="password" name="password" placeholder="Password Baru" required>

<button type="submit" name="reset">Reset</button>
</form>

</body>
</html>
