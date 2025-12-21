<?php
include "db.php";

if(isset($_POST['kirim'])){
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $kategori = $_POST['kategori'];
    $uraian = $_POST['uraian'];

    $filename = $_FILES['lampiran']['name'];
    $tmp = $_FILES['lampiran']['tmp_name'];
    move_uploaded_file($tmp, "assets/uploads/".$filename);

    $tanggal = date("Y-m-d");
    $waktu = date("H:i:s");

    mysqli_query($conn, "INSERT INTO pengaduan
    (nama,email,kategori,uraian,lampiran,tanggal,waktu)
    VALUES ('$nama','$email','$kategori','$uraian','$filename','$tanggal','$waktu')");

    echo "<script>alert('Pengaduan berhasil dikirim!'); location='dashboard.php';</script>";
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Submit Pengaduan</title>
<link rel="stylesheet" href="assets/styles.css">
</head>
<body>

<h2>Submit Pengaduan</h2>

<form method="POST" enctype="multipart/form-data">
<input type="text" name="nama" placeholder="Nama Anda" required>
<input type="email" name="email" placeholder="Email" required>

<select name="kategori">
<option>Fasilitas</option>
<option>Kebersihan</option>
<option>Keamanan</option>
<option>Layanan Kampus</option>
</select>

<textarea name="uraian" placeholder="Uraian Pengaduan..." required></textarea>

<label>Lampiran (opsional)</label>
<input type="file" name="lampiran">

<button type="submit" name="kirim">Kirim</button>
</form>

</body>
</html>
