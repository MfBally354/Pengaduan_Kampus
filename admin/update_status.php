<?php
require_once '../db.php';
requireRole('admin');

$success = '';
$error = '';

// Handle Update Status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $pengaduan_id = $_POST['pengaduan_id'];
    $new_status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE pengaduan SET status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $new_status, $pengaduan_id);
    
    if ($stmt->execute()) {
        $success = "Status pengaduan #$pengaduan_id berhasil diubah menjadi '$new_status'";
    } else {
        $error = "Gagal mengubah status";
    }
    $stmt->close();
}

// Handle Bulk Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bulk_update'])) {
    $selected_ids = $_POST['selected_ids'] ?? [];
    $bulk_status = $_POST['bulk_status'];
    
    if (count($selected_ids) > 0) {
        $ids_placeholder = implode(',', array_fill(0, count($selected_ids), '?'));
        $stmt = $conn->prepare("UPDATE pengaduan SET status = ?, updated_at = NOW() WHERE id IN ($ids_placeholder)");
        
        $types = str_repeat('i', count($selected_ids));
        $stmt->bind_param("s$types", $bulk_status, ...$selected_ids);
        
        if ($stmt->execute()) {
            $success = count($selected_ids) . " pengaduan berhasil diupdate ke status '$bulk_status'";
        } else {
            $error = "Gagal melakukan bulk update";
        }
        $stmt->close();
    } else {
        $error = "Pilih minimal 1 pengaduan untuk bulk update";
    }
}

// Get all pengaduan
$filter_status = $_GET['status'] ?? '';
$query = "SELECT p.*, u.nama as nama_mahasiswa 
    FROM pengaduan p 
    JOIN users u ON p.user_id = u.id";

if ($filter_status) {
    $query .= " WHERE p.status = '$filter_status'";
}

$query .= " ORDER BY 
    CASE p.status 
        WHEN 'pending' THEN 1 
        WHEN 'diproses' THEN 2 
        WHEN 'selesai' THEN 3 
        WHEN 'ditolak' THEN 4 
    END, 
    p.created_at DESC";

$pengaduan_list = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Status - Admin</title>
    <link rel="stylesheet" href="/assets/styles.css">
    <style>
        .update-container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 20px;
        }
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .filter-section select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 10px;
        }
        .bulk-actions {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .bulk-actions select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn-bulk {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-bulk:hover {
            background: #0056b3;
        }
        .pengaduan-table {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pending { background: #ffc107; color: #000; }
        .status-diproses { background: #17a2b8; color: white; }
        .status-selesai { background: #28a745; color: white; }
        .status-ditolak { background: #dc3545; color: white; }
        .status-select {
            padding: 6px 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 13px;
        }
        .btn-update {
            padding: 6px 12px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
        }
        .btn-update:hover {
            background: #218838;
        }
        .checkbox-col {
            width: 40px;
        }
        input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        .btn-view {
            padding: 6px 12px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 12px;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <?php include '../navbar.php'; ?>
    
    <div class="update-container">
        <h1>⚙️ Update Status Pengaduan</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <!-- Filter -->
        <div class="filter-section">
            <form method="GET" action="" style="display: inline;">
                <label><strong>Filter Status:</strong></label>
                <select name="status" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="pending" <?= $filter_status == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="diproses" <?= $filter_status == 'diproses' ? 'selected' : '' ?>>Diproses</option>
                    <option value="selesai" <?= $filter_status == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                    <option value="ditolak" <?= $filter_status == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                </select>
                <a href="update_status.php" style="margin-left: 10px; color: #007bff;">Reset Filter</a>
            </form>
        </div>
        
        <!-- Bulk Actions -->
        <form method="POST" action="" id="bulkForm">
            <div class="bulk-actions">
                <label><strong>Bulk Action:</strong></label>
                <select name="bulk_status" required>
                    <option value="">Pilih Status</option>
                    <option value="pending">Pending</option>
                    <option value="diproses">Diproses</option>
                    <option value="selesai">Selesai</option>
                    <option value="ditolak">Ditolak</option>
                </select>
                <button type="submit" name="bulk_update" class="btn-bulk" onclick="return confirm('Update status untuk semua yang dipilih?')">
                    🔄 Update Terpilih
                </button>
                <span id="selected-count" style="color: #666;">0 dipilih</span>
            </div>
            
            <!-- Table -->
            <div class="pengaduan-table">
                <table>
                    <thead>
                        <tr>
                            <th class="checkbox-col">
                                <input type="checkbox" id="select-all" title="Pilih Semua">
                            </th>
                            <th>ID</th>
                            <th>Judul</th>
                            <th>Mahasiswa</th>
                            <th>Kategori</th>
                            <th>Status Saat Ini</th>
                            <th>Tanggal</th>
                            <th>Ubah Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($pengaduan_list->num_rows > 0): ?>
                            <?php while ($row = $pengaduan_list->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected_ids[]" value="<?= $row['id'] ?>" class="row-checkbox">
                                    </td>
                                    <td>#<?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars(substr($row['judul'], 0, 50)) ?>...</td>
                                    <td><?= htmlspecialchars($row['nama_mahasiswa']) ?></td>
                                    <td><?= ucfirst($row['kategori']) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= $row['status'] ?>">
                                            <?= ucfirst($row['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                                    <td>
                                        <form method="POST" action="" style="display: inline;">
                                            <input type="hidden" name="pengaduan_id" value="<?= $row['id'] ?>">
                                            <select name="status" class="status-select" required>
                                                <option value="">Pilih</option>
                                                <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="diproses" <?= $row['status'] == 'diproses' ? 'selected' : '' ?>>Diproses</option>
                                                <option value="selesai" <?= $row['status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                                <option value="ditolak" <?= $row['status'] == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                                            </select>
                                            <button type="submit" name="update_status" class="btn-update">Update</button>
                                        </form>
                                    </td>
                                    <td>
                                        <a href="/detail.php?id=<?= $row['id'] ?>" class="btn-view">👁️ Lihat</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 40px; color: #999;">
                                    Tidak ada pengaduan
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
    
    <script>
        // Select All Checkbox
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateSelectedCount();
        });
        
        // Update count on individual checkbox change
        document.querySelectorAll('.row-checkbox').forEach(cb => {
            cb.addEventListener('change', updateSelectedCount);
        });
        
        function updateSelectedCount() {
            const count = document.querySelectorAll('.row-checkbox:checked').length;
            document.getElementById('selected-count').textContent = count + ' dipilih';
        }
    </script>
</body>
</html>
