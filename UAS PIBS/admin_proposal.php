<?php
session_start();
include 'koneksi.php';

// Cek akses Koordinator
if ($_SESSION['role'] != 'Koordinator') { header("Location: index.php"); exit; }

// LOGIKA UPDATE / APPROVE
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $status = $_GET['action'] == 'approve' ? 'Disetujui' : 'Ditolak';
    mysqli_query($conn, "UPDATE proposal SET status='$status' WHERE id_proposal='$id'");
    header("Location: admin_proposal.php");
}

// LOGIKA HAPUS
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM proposal WHERE id_proposal='$id'");
    header("Location: admin_proposal.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Admin - Verifikasi Proposal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <span class="navbar-brand">Admin Panel - Koordinator TA</span>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
</nav>

<div class="container">
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between">
            <h5>Data Proposal Masuk</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Judul</th>
                            <th>Status</th>
                            <th>Aksi (Verifikasi)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($conn, "SELECT p.*, m.nama FROM proposal p JOIN mahasiswa m ON p.nim = m.nim");
                        $no = 1;
                        while($row = mysqli_fetch_assoc($query)):
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $row['nim'] ?></td>
                            <td><?= $row['nama'] ?></td>
                            <td><?= $row['judul'] ?></td>
                            <td>
                                <span class="badge bg-<?= ($row['status']=='Disetujui')?'success':(($row['status']=='Ditolak')?'danger':'warning') ?>">
                                    <?= $row['status'] ?>
                                </span>
                            </td>
                            <td>
                                <a href="?action=approve&id=<?= $row['id_proposal'] ?>" class="btn btn-success btn-sm" onclick="return confirm('Setujui proposal ini?')">ACC</a>
                                <a href="?action=reject&id=<?= $row['id_proposal'] ?>" class="btn btn-warning btn-sm" onclick="return confirm('Tolak proposal ini?')">Tolak</a>
                                <a href="?hapus=<?= $row['id_proposal'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data?')">Hapus</a>
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