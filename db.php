<?php
$host = "localhost";
$user = "iqbal";
$pass = "#semarangwhj354iqbal#";
$db   = "pengaduan_kampus";

$conn = mysqli_connect($host, $user, $pass, $db);

if(!$conn){
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
