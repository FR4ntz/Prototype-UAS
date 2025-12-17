<?php
session_start();
include 'koneksi.php';

// Cek apakah user adalah Dosen atau Koordinator
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'Dosen' && $_SESSION['role'] != 'Koordinator')) {
    header("Location: index.php"); 
    exit; 
}

$nidn = $_SESSION['username']; // Asumsi session username menyimpan NIDN saat login
$role = $_SESSION['role'];
$nama = $_SESSION['user'];

// Hitung Statistik Sederhana
$jml_proposal = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM proposal WHERE status='Diajukan'"));
$jml_bimbingan = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM bimbingan WHERE nidn_pembimbing='$nidn' AND status='Menunggu'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Dosen/Koordinator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        header { background: #198754; color: white; padding: 15px 0; } /* Warna Hijau Pembeda Dosen */
        footer { background: #198754; color: white; padding: 20px 0; margin-top: 50px;}
        .nav-link { color: #333; }
        .nav-link.active { background-color: #e9f7ef; color: #198754; border-radius: 5px; font-weight: bold; }
    </style>
</head>
<body>

<header>
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h5 class="m-0 fw-bold">SITA - STAFF PANEL</h5>
            <small>Area Dosen & Koordinator</small>
        </div>
        <div>
            <span class="me-3">Halo, <?= $nama ?> (<?= $role ?>)</span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</header>

<div class="container mt-4">
    <div class="row">
        
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold">Menu Staff</div>
                <div class="list-group list-group-flush">
                    <a href="dashboard_dosen.php" class="list-group-item list-group-item-action active">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                    
                    <a href="kelola_bimbingan.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-people me-2"></i> Kelola Bimbingan 
                        <?php if($jml_bimbingan > 0) echo "<span class='badge bg-danger float-end'>$jml_bimbingan</span>"; ?>
                    </a>

                    <?php if($role == 'Koordinator'): ?>
                        <a href="admin_mahasiswa.php" class="list-group-item list-group-item-action">
    <i class="bi bi-person-lines-fill me-2"></i> Master Data Mahasiswa
</a>
                        <div class="list-group-item bg-light fw-bold small text-muted">MENU KOORDINATOR</div>
                        <a href="admin_proposal.php" class="list-group-item list-group-item-action">
                            <i class="bi bi-file-earmark-check me-2"></i> Validasi Proposal
                            <?php if($jml_proposal > 0) echo "<span class='badge bg-danger float-end'>$jml_proposal</span>"; ?>
                        </a>
                        <a href="kelola_sidang.php" class="list-group-item list-group-item-action">
                            <i class="bi bi-calendar-check me-2"></i> Kelola Jadwal Sidang
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="alert alert-success shadow-sm">
                <h4 class="alert-heading"><i class="bi bi-person-badge"></i> Selamat Datang!</h4>
                <p>Anda login sebagai <strong><?= $role ?></strong>. Silakan kelola aktivitas akademik mahasiswa melalui menu di sebelah kiri.</p>
            </div>

            <div class="row g-2">
                <div class="col-6">
                    <div class="card text-center shadow-sm h-100 border-success">
                        <div class="card-body">
                            <h3 class="text-success"><?= $jml_bimbingan ?></h3>
                            <small class="text-muted">Bimbingan Pending</small>
                        </div>
                    </div>
                </div>
                <?php if($role == 'Koordinator'): ?>
                <div class="col-6">
                    <div class="card text-center shadow-sm h-100 border-warning">
                        <div class="card-body">
                            <h3 class="text-warning"><?= $jml_proposal ?></h3>
                            <small class="text-muted">Proposal Baru</small>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm mb-3">
                <div class="card-header fw-bold bg-dark text-white">Kalender Akademik</div>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item">Sidang Periode 1: <br><strong>10 November 2025</strong></li>
                    <li class="list-group-item">Batas Nilai Masuk: <br><strong>20 Desember 2025</strong></li>
                </ul>
            </div>
        </div>

    </div>
</div>

<footer class="text-center">
    <small>&copy; 2025 Universitas Pembangunan Jaya - Staff Portal</small>
</footer>

</body>
</html>