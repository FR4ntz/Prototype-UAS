<?php
session_start();
include 'koneksi.php';
// Cek Login
if (!isset($_SESSION['role'])) { header("Location: index.php"); exit; }

// LOGIKA UPDATE RESPON DOSEN
if (isset($_POST['update_respon'])) {
    $id_bim = $_POST['id_bimbingan'];
    $catatan = $_POST['catatan'];
    $status = $_POST['status'];
    
    $query = "UPDATE bimbingan SET catatan_dosen='$catatan', status='$status' WHERE id_bimbingan='$id_bim'";
    mysqli_query($conn, $query);
    echo "<script>alert('Respon tersimpan!'); window.location='kelola_bimbingan.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Bimbingan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-success mb-4">
    <div class="container">
        <a class="navbar-brand" href="dashboard_dosen.php">SITA - Dosen</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link active" href="dashboard_dosen.php">Kembali ke Dashboard</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="card shadow">
        <div class="card-header bg-white fw-bold">Daftar Logbook Mahasiswa (Perlu Respon)</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Mahasiswa</th>
                            <th>Tanggal & Topik</th>
                            <th>Status Saat Ini</th>
                            <th width="40%">Respon Dosen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Menampilkan bimbingan yang diarahkan ke Dosen ini (atau semua jika demo)
                        // Untuk demo kita tampilkan semua, idealnya: WHERE nidn_pembimbing = 'SESSION_NIDN'
                        $query = mysqli_query($conn, "SELECT b.*, m.nama as mhs_nama FROM bimbingan b JOIN mahasiswa m ON b.nim = m.nim ORDER BY b.status DESC, b.tanggal DESC");
                        
                        while($row = mysqli_fetch_array($query)):
                        ?>
                        <tr>
                            <td>
                                <strong><?= $row['mhs_nama'] ?></strong><br>
                                <small class="text-muted"><?= $row['nim'] ?></small>
                            </td>
                            <td>
                                <small class="text-muted"><?= $row['tanggal'] ?></small><br>
                                <?= $row['topik'] ?>
                            </td>
                            <td>
                                <?php 
                                    $bg = ($row['status']=='Menunggu')?'warning':(($row['status']=='ACC')?'success':'danger');
                                    echo "<span class='badge bg-$bg'>{$row['status']}</span>";
                                ?>
                            </td>
                            <td>
                                <form method="POST" class="d-flex gap-2">
                                    <input type="hidden" name="id_bimbingan" value="<?= $row['id_bimbingan'] ?>">
                                    <input type="text" name="catatan" class="form-control form-control-sm" placeholder="Berikan catatan..." value="<?= $row['catatan_dosen'] ?>" required>
                                    <select name="status" class="form-select form-select-sm" style="width: 100px;">
                                        <option value="ACC" <?= $row['status']=='ACC'?'selected':'' ?>>ACC</option>
                                        <option value="Revisi" <?= $row['status']=='Revisi'?'selected':'' ?>>Revisi</option>
                                    </select>
                                    <button type="submit" name="update_respon" class="btn btn-primary btn-sm">Simpan</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>