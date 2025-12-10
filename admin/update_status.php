<?php
include '../db.php';
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['email']!=='admin@kampus.com'){
header('Location: ../index.php'); exit;
}


if($_SERVER['REQUEST_METHOD']=='POST'){
$id = (int)$_POST['id'];
$status = mysqli_real_escape_string($conn, $_POST['status']);
mysqli_query($conn, "UPDATE pengaduan SET status='$status' WHERE id=$id");
header('Location: admin_dashboard.php');
exit;
}
?>