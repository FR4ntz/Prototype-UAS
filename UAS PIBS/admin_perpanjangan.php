<?php
session_start();
include 'koneksi.php';

// Validasi Akses Koordinator
if ($_SESSION['role'] != 'Koordinator') { header("Location: index.php"); exit; }

// LOGIKA ACC / TOLAK
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $status = ($_GET['action'] == 'acc') ? 'Disetujui' : 'Ditolak';
    
    // Update status di tabel perpanjangan
    mysqli_query($conn, "UPDATE perpanjangan SET status_perpanjangan='$status' WHERE id_perpanjangan='$id'");
    
    // (Opsional) Kirim notifikasi ke mahasiswa bisa ditambahkan di sini
    
    echo "<script>alert('Status Berhasil Diubah!'); window.location='admin_perpanjangan.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Validasi Perpanjangan TA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <span class="navbar-brand">Admin Panel - Validasi Extend</span>
        <a href="dashboard_dosen.php" class="btn btn-secondary btn-sm">Kembali</a>
    </div>
</nav>

<div class="container">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark fw-bold">Daftar Permohonan Perpanjangan (Extend)</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Mahasiswa</th>
                            <th>Judul Proposal</th>
                            <th>Alasan Extend</th>
                            <th>Tanggal Ajuan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Join 3 tabel: Perpanjangan -> Proposal -> Mahasiswa
                        $query = "SELECT ext.*, m.nama, p.judul 
                                  FROM perpanjangan ext 
                                  JOIN proposal p ON ext.id_proposal = p.id_proposal
                                  JOIN mahasiswa m ON ext.nim = m.nim 
                                  ORDER BY ext.status_perpanjangan ASC, ext.tanggal_pengajuan DESC";
                        
                        $result = mysqli_query($conn, $query);
                        while($row = mysqli_fetch_array($result)):
                        ?>
                        <tr>
                            <td>
                                <strong><?= $row['nama'] ?></strong><br>
                                <small class="text-muted"><?= $row['nim'] ?></small>
                            </td>
                            <td><?= substr($row['judul'], 0, 50) ?>...</td>
                            <td><?= $row['alasan'] ?></td>
                            <td><?= $row['tanggal_pengajuan'] ?></td>
                            <td>
                                <?php 
                                    $s = $row['status_perpanjangan'];
                                    $badge = ($s=='Diajukan')?'warning':(($s=='Disetujui')?'success':'danger');
                                    echo "<span class='badge bg-$badge'>$s</span>";
                                ?>
                            </td>
                            <td>
                                <?php if($s == 'Diajukan'): ?>
                                    <a href="?action=acc&id=<?= $row['id_perpanjangan'] ?>" class="btn btn-success btn-sm" onclick="return confirm('Setujui perpanjangan 6 bulan?')">ACC</a>
                                    <a href="?action=reject&id=<?= $row['id_perpanjangan'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tolak perpanjangan?')">Tolak</a>
                                <?php else: ?>
                                    <span class="text-muted small">Selesai</span>
                                <?php endif; ?>
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