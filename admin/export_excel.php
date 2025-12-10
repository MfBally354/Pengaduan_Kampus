<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=data_pengaduan.xls");

include "db.php";

$q = mysqli_query($conn, "SELECT * FROM pengaduan");

echo "<table border='1'>
<tr>
<th>Nama</th><th>Email</th><th>Kategori</th>
<th>Uraian</th><th>Status</th><th>Tanggal</th><th>Waktu</th>
</tr>";

while($d=mysqli_fetch_assoc($q)){
    echo "<tr>
    <td>$d[nama]</td>
    <td>$d[email]</td>
    <td>$d[kategori]</td>
    <td>$d[uraian]</td>
    <td>$d[status]</td>
    <td>$d[tanggal]</td>
    <td>$d[waktu]</td>
    </tr>";
}

echo "</table>";
?>
