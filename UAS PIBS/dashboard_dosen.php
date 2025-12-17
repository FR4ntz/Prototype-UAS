<?php
session_start();
include 'koneksi.php';

// 1. CEK KEAMANAN AKSES
// Hanya Dosen atau Koordinator yang boleh masuk
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'Dosen' && $_SESSION['role'] != 'Koordinator')) {
    header("Location: index.php"); 
    exit; 
}

$nidn = $_SESSION['username']; // Menggunakan NIDN dari session
$role = $_SESSION['role'];
$nama = $_SESSION['user'];

// 2. HITUNG STATISTIK (Untuk Badge Notifikasi)

// A. Statistik Umum (Dosen Pembimbing)
// Hitung jumlah bimbingan yang statusnya "Menunggu" respon dosen ini
$query_bim = mysqli_query($conn, "SELECT * FROM bimbingan WHERE nidn_pembimbing='$nidn' AND status='Menunggu'");
$jml_bim_pending = mysqli_num_rows($query_bim);

// B. Statistik Khusus (Koordinator)
$jml_prop_baru = 0;
$jml_extend_pending = 0;

if ($role == 'Koordinator') {
    // Hitung proposal baru (Status: Diajukan)
    $query_prop = mysqli_query($conn, "SELECT * FROM proposal WHERE status='Diajukan'");
    $jml_prop_baru = mysqli_num_rows($query_prop);

    // Hitung pengajuan extend baru (Status: Diajukan) - Cek dulu kalau tabel ada
    // (Menggunakan @ untuk suppress error jika tabel belum dibuat user, tapi idealnya tabel sudah ada)
    $query_ext = @mysqli_query($conn, "SELECT * FROM perpanjangan WHERE status_perpanjangan='Diajukan'");
    if ($query_ext) {
        $jml_extend_pending = mysqli_num_rows($query_ext);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dosen & Koordinator - SITA UPJ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body { background-color: #f4f6f9; }
        /* Warna Header Hijau untuk membedakan dengan Mahasiswa (Biru) */
        header { background: #157347; color: white; padding: 15px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        footer { background: #157347; color: white; padding: 20px 0; margin-top: 50px; }
        
        .nav-link { color: #495057; font-weight: 500; padding: 10px 15px; border-radius: 5px; transition: 0.3s; }
        .nav-link:hover { background-color: #e9ecef; color: #157347; transform: translateX(5px); }
        .nav-link.active { background-color: #d1e7dd; color: #0f5132; font-weight: bold; }
        
        .card-stat { transition: transform 0.2s; border: none; }
        .card-stat:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        
        /* Badge Notifikasi */
        .badge-notif { float: right; background-color: #dc3545; color: white; border-radius: 50px; padding: 2px 8px; font-size: 0.8rem; }
    </style>
</head>
<body>

    <header>
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="bi bi-building-fill fs-3 me-3"></i>
                <div>
                    <h5 class="m-0 fw-bold">SITA - STAFF PANEL</h5>
                    <small style="opacity: 0.9;">Portal Dosen & Koordinator</small>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <div class="text-end me-3 d-none d-md-block">
                    <span class="d-block fw-bold"><?= $nama ?></span>
                    <span class="badge bg-warning text-dark"><?= $role ?></span>
                </div>
                <a href="logout.php" class="btn btn-outline-light btn-sm ms-2">Logout</a>
            </div>
        </div>
    </header>

    <div class="container my-4">
        <div class="row">
            
            <div class="col-lg-3 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-bold border-bottom">
                        <i class="bi bi-grid-fill me-2 text-success"></i> Menu Utama
                    </div>
                    <div class="card-body p-2">
                        <nav class="nav flex-column gap-1">
                            <a class="nav-link active" href="dashboard_dosen.php">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                            
                            <div class="text-muted small fw-bold mt-2 ms-2">DOSEN PEMBIMBING</div>
                            <a class="nav-link" href="kelola_bimbingan.php">
                                <i class="bi bi-people me-2"></i> Kelola Bimbingan
                                <?php if($jml_bim_pending > 0): ?>
                                    <span class="badge-notif"><?= $jml_bim_pending ?></span>
                                <?php endif; ?>
                            </a>
                            <a class="nav-link" href="chat_dosen.php">
                                <i class="bi bi-chat-dots me-2"></i> Pesan Masuk
                            </a>

                            <?php if($role == 'Koordinator'): ?>
                                <hr class="my-2 border-secondary opacity-25">
                                <div class="text-muted small fw-bold mt-1 ms-2">KOORDINATOR TA</div>
                                
                                <a class="nav-link" href="admin_proposal.php">
                                    <i class="bi bi-file-earmark-check me-2"></i> Validasi Proposal
                                    <?php if($jml_prop_baru > 0): ?>
                                        <span class="badge-notif"><?= $jml_prop_baru ?></span>
                                    <?php endif; ?>
                                </a>
                                <a class="nav-link" href="kelola_sidang.php">
                                    <i class="bi bi-calendar-check me-2"></i> Kelola Sidang & Nilai
                                </a>
                                <a class="nav-link" href="admin_perpanjangan.php">
                                    <i class="bi bi-hourglass-split me-2"></i> Validasi Extend
                                    <?php if($jml_extend_pending > 0): ?>
                                        <span class="badge-notif"><?= $jml_extend_pending ?></span>
                                    <?php endif; ?>
                                </a>
                                <a class="nav-link" href="admin_mahasiswa.php">
                                    <i class="bi bi-person-lines-fill me-2"></i> Master Data Mahasiswa
                                </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                
                <div class="alert alert-success shadow-sm border-0 d-flex align-items-center" role="alert">
                    <i class="bi bi-person-badge-fill fs-1 me-3 opacity-50"></i>
                    <div>
                        <h4 class="alert-heading fw-bold mb-1">Selamat Datang!</h4>
                        <p class="mb-0">Anda login sebagai <strong><?= $role ?></strong>. Kelola aktivitas akademik mahasiswa melalui panel ini.</p>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card card-stat shadow-sm border-start border-4 border-primary h-100">
                            <div class="card-body">
                                <h6 class="text-muted text-uppercase small fw-bold">Bimbingan Pending</h6>
                                <div class="d-flex align-items-center justify-content-between">
                                    <h2 class="mb-0 fw-bold text-primary"><?= $jml_bim_pending ?></h2>
                                    <i class="bi bi-person-video3 fs-1 text-black-50"></i>
                                </div>
                                <a href="kelola_bimbingan.php" class="stretched-link small text-decoration-none">Lihat Detail &rarr;</a>
                            </div>
                        </div>
                    </div>

                    <?php if($role == 'Koordinator'): ?>
                    <div class="col-md-6">
                        <div class="card card-stat shadow-sm border-start border-4 border-warning h-100">
                            <div class="card-body">
                                <h6 class="text-muted text-uppercase small fw-bold">Proposal Masuk</h6>
                                <div class="d-flex align-items-center justify-content-between">
                                    <h2 class="mb-0 fw-bold text-warning"><?= $jml_prop_baru ?></h2>
                                    <i class="bi bi-file-earmark-plus fs-1 text-black-50"></i>
                                </div>
                                <a href="admin_proposal.php" class="stretched-link small text-decoration-none text-warning">Verifikasi Sekarang &rarr;</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card card-stat shadow-sm border-start border-4 border-danger h-100">
                            <div class="card-body">
                                <h6 class="text-muted text-uppercase small fw-bold">Request Extend</h6>
                                <div class="d-flex align-items-center justify-content-between">
                                    <h2 class="mb-0 fw-bold text-danger"><?= $jml_extend_pending ?></h2>
                                    <i class="bi bi-clock-history fs-1 text-black-50"></i>
                                </div>
                                <a href="admin_perpanjangan.php" class="stretched-link small text-decoration-none text-danger">Tinjau Pengajuan &rarr;</a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-bold">
                        <i class="bi bi-info-circle text-info"></i> Informasi Sistem
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Total Mahasiswa Aktif TA
                                <span class="badge bg-secondary rounded-pill">
                                    <?php echo mysqli_num_rows(mysqli_query($conn, "SELECT * FROM proposal WHERE status='Disetujui'")); ?>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Jadwal Sidang Terdekat
                                <span class="fw-bold text-dark">10 Nov 2025</span>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>

            <div class="col-lg-3">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-dark text-white fw-bold">
                        <i class="bi bi-calendar-event"></i> Agenda Akademik
                    </div>
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item">
                            <strong>15 Sep - 20 Sep</strong><br>
                            <span class="text-muted">Masa Pengajuan Proposal Gelombang 1</span>
                        </li>
                        <li class="list-group-item">
                            <strong>05 Nov 2025</strong><br>
                            <span class="text-muted">Pelaksanaan Sidang Periode 1</span>
                        </li>
                        <li class="list-group-item">
                            <strong>20 Des 2025</strong><br>
                            <span class="text-muted">Batas Akhir Input Nilai</span>
                        </li>
                    </ul>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="fw-bold">Butuh Bantuan?</h6>
                        <p class="small text-muted mb-3">Hubungi tim IT jika terdapat kendala pada sistem SITA.</p>
                        <a href="#" class="btn btn-outline-success btn-sm w-100"><i class="bi bi-whatsapp"></i> Hubungi Helpdesk</a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <footer class="text-center">
        <div class="container">
            <h6 class="fw-bold mb-2">Universitas Pembangunan Jaya</h6>
            <small class="d-block text-white-50">Jln. Cendrawasih Raya Blok B7/P, Bintaro Jaya, Tangerang Selatan</small>
            <div class="mt-3">
                <small class="d-block text-white-50">&copy; 2025 Kelompok Sistem Informasi. All Rights Reserved.</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>