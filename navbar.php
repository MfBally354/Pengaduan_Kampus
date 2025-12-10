<?php
// navbar.php
session_start();
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>
<nav style="background:#273344;color:white;padding:12px;border-radius:8px;margin-bottom:18px;">
<div style="max-width:1100px;margin:auto;display:flex;align-items:center;justify-content:space-between;">
<div style="font-weight:700">Eco Campus — Pengaduan</div>
<div>
<?php if($user): ?>
<span style="margin-right:12px">Hai, <?= htmlspecialchars($user['nama']) ?></span>
<a href="dashboard.php" style="color:white;margin-right:8px;text-decoration:none;">Dashboard</a>
<a href="submit.php" style="color:white;margin-right:8px;text-decoration:none;">Submit</a>
<a href="export_excel.php" style="color:white;margin-right:8px;text-decoration:none;">Export</a>
<a href="logout.php" style="color:white;text-decoration:none;">Logout</a>
<?php else: ?>
<a href="index.php" style="color:white;text-decoration:none;margin-right:8px;">Login</a>
<?php endif; ?>
</div>
</div>
</nav>