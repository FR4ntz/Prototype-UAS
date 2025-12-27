<?php
session_start();
include 'koneksi.php';

// 1. CEK KEAMANAN AKSES
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'Dosen' && $_SESSION['role'] != 'Koordinator')) {
    header("Location: index.php"); 
    exit; 
}

$nidn = $_SESSION['username'];
$role = $_SESSION['role']; 
$nama = $_SESSION['user'];

// 2. HITUNG STATISTIK 
$jml_bim_pending = 0;
$jml_prop_baru = 0;
$jml_extend_pending = 0;

if ($role == 'Dosen') {
    // Statistik Dosen: Hanya hitung bimbingan
    $query_bim = mysqli_query($conn, "SELECT * FROM bimbingan WHERE nidn_pembimbing='$nidn' AND status='Menunggu'");
    $jml_bim_pending = mysqli_num_rows($query_bim);
} elseif ($role == 'Koordinator') {
    // Statistik Koordinator
    $query_prop = mysqli_query($conn, "SELECT * FROM proposal WHERE status='Diajukan'");
    $jml_prop_baru = mysqli_num_rows($query_prop);

    $query_ext = @mysqli_query($conn, "SELECT * FROM perpanjangan WHERE status_perpanjangan='Diajukan'");
    if ($query_ext) $jml_extend_pending = mysqli_num_rows($query_ext);
}

// 3. LOGIKA HALAMAN DINAMIS
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Staff - SITA UPJ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="dosen-mode">
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
                <a href="logout.php" class="btn btn-outline-light btn-sm ms-2">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </header>

    <div class="container my-4">
        <div class="row">
            
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-header"><i class="bi bi-grid-fill"></i> Menu Utama</div>
                    <div class="card-body p-2">
                        <nav class="nav flex-column">
                            <a class="nav-link <?= ($page=='home')?'active':'' ?>" href="dashboard_dosen.php?page=home">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                            
                            <?php if($role == 'Dosen'): ?>
                                <div class="text-muted small fw-bold mt-3 ms-2 mb-1">DOSEN PEMBIMBING</div>
                                <a class="nav-link <?= ($page=='bimbingan')?'active':'' ?>" href="dashboard_dosen.php?page=bimbingan">
                                    <i class="bi bi-people me-2"></i> Kelola Bimbingan
                                    <?php if($jml_bim_pending > 0): ?>
                                        <span class="badge bg-danger ms-auto rounded-pill"><?= $jml_bim_pending ?></span>
                                    <?php endif; ?>
                                </a>
                                <a class="nav-link <?= ($page=='chat')?'active':'' ?>" href="dashboard_dosen.php?page=chat">
                                    <i class="bi bi-chat-dots me-2"></i> Pesan Masuk
                                </a>
                            <?php endif; ?>

                            <?php if($role == 'Koordinator'): ?>
                                <hr class="my-2 border-secondary opacity-25">
                                <div class="text-muted small fw-bold mt-1 ms-2 mb-1">MASTER DATA</div>
                                
                                <a class="nav-link <?= ($page=='master_dosen')?'active':'' ?>" href="dashboard_dosen.php?page=master_dosen">
                                    <i class="bi bi-person-badge-fill me-2"></i> Kelola Akun Dosen
                                </a>
                                <a class="nav-link <?= ($page=='mahasiswa')?'active':'' ?>" href="dashboard_dosen.php?page=mahasiswa">
                                    <i class="bi bi-person-lines-fill me-2"></i> Master Mahasiswa
                                </a>

                                <div class="text-muted small fw-bold mt-3 ms-2 mb-1">KOORDINATOR TA</div>
                                
                                <a class="nav-link <?= ($page=='proposal')?'active':'' ?>" href="dashboard_dosen.php?page=proposal">
                                    <i class="bi bi-file-earmark-check me-2"></i> Validasi Proposal
                                    <?php if($jml_prop_baru > 0): ?>
                                        <span class="badge bg-warning text-dark ms-auto rounded-pill"><?= $jml_prop_baru ?></span>
                                    <?php endif; ?>
                                </a>
                                <a class="nav-link <?= ($page=='sidang')?'active':'' ?>" href="dashboard_dosen.php?page=sidang">
                                    <i class="bi bi-calendar-check me-2"></i> Kelola Sidang & Nilai
                                </a>
                                <a class="nav-link <?= ($page=='extend')?'active':'' ?>" href="dashboard_dosen.php?page=extend">
                                    <i class="bi bi-hourglass-split me-2"></i> Validasi Extend
                                    <?php if($jml_extend_pending > 0): ?>
                                        <span class="badge bg-danger ms-auto rounded-pill"><?= $jml_extend_pending ?></span>
                                    <?php endif; ?>
                                </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <?php 
                switch ($page) {
                    case 'home':
                        // === TAMPILAN DASHBOARD HOME ===
                        ?>
                        <div class="alert alert-success shadow-sm border-0 d-flex align-items-center mb-4" role="alert" style="background: linear-gradient(135deg, #198754, #146c43); color: white;">
                            <i class="bi bi-person-badge-fill fs-1 me-3 opacity-50"></i>
                            <div>
                                <h4 class="alert-heading fw-bold mb-1">Selamat Datang!</h4>
                                <p class="mb-0 small opacity-90">Anda login sebagai <strong><?= $role ?></strong>. Kelola aktivitas akademik di sini.</p>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <?php if($role == 'Dosen'): ?>
                                <div class="col-md-6">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-body border-start border-4 border-primary">
                                            <h6 class="text-muted text-uppercase small fw-bold">Bimbingan Pending</h6>
                                            <div class="d-flex align-items-center justify-content-between mt-2">
                                                <h2 class="mb-0 fw-bold text-primary"><?= $jml_bim_pending ?></h2>
                                                <i class="bi bi-person-video3 fs-1 text-black-50"></i>
                                            </div>
                                            <a href="dashboard_dosen.php?page=bimbingan" class="stretched-link small text-decoration-none mt-2 d-block">Tinjau Logbook &rarr;</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if($role == 'Koordinator'): ?>
                                <div class="col-md-6">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-body border-start border-4 border-success">
                                            <h6 class="text-muted text-uppercase small fw-bold">Total Dosen</h6>
                                            <div class="d-flex align-items-center justify-content-between mt-2">
                                                <h2 class="mb-0 fw-bold text-success">
                                                    <?php 
                                                    // Hitung Dosen (Handle error kolom peran/role)
                                                    $q_dosen = mysqli_query($conn, "SELECT nidn FROM dosen WHERE peran='Dosen'");
                                                    if (!$q_dosen) { $q_dosen = mysqli_query($conn, "SELECT nidn FROM dosen WHERE role='Dosen'"); }
                                                    echo ($q_dosen) ? mysqli_num_rows($q_dosen) : 0;
                                                    ?>
                                                </h2>
                                                <i class="bi bi-person-badge fs-1 text-black-50"></i>
                                            </div>
                                            <a href="dashboard_dosen.php?page=master_dosen" class="stretched-link small text-decoration-none text-success mt-2 d-block">Kelola Akun &rarr;</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-body border-start border-4 border-warning">
                                            <h6 class="text-muted text-uppercase small fw-bold">Proposal Masuk</h6>
                                            <div class="d-flex align-items-center justify-content-between mt-2">
                                                <h2 class="mb-0 fw-bold text-warning"><?= $jml_prop_baru ?></h2>
                                                <i class="bi bi-file-earmark-plus fs-1 text-black-50"></i>
                                            </div>
                                            <a href="dashboard_dosen.php?page=proposal" class="stretched-link small text-decoration-none text-warning mt-2 d-block">Verifikasi &rarr;</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-body border-start border-4 border-danger">
                                            <h6 class="text-muted text-uppercase small fw-bold">Request Extend</h6>
                                            <div class="d-flex align-items-center justify-content-between mt-2">
                                                <h2 class="mb-0 fw-bold text-danger"><?= $jml_extend_pending ?></h2>
                                                <i class="bi bi-clock-history fs-1 text-black-50"></i>
                                            </div>
                                            <a href="dashboard_dosen.php?page=extend" class="stretched-link small text-decoration-none text-danger mt-2 d-block">Tinjau &rarr;</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-body border-start border-4 border-info">
                                            <h6 class="text-muted text-uppercase small fw-bold">Mahasiswa TA Aktif</h6>
                                            <div class="d-flex align-items-center justify-content-between mt-2">
                                                <h2 class="mb-0 fw-bold text-info">
                                                    <?= mysqli_num_rows(mysqli_query($conn, "SELECT nim FROM mahasiswa")) ?>
                                                </h2>
                                                <i class="bi bi-mortarboard fs-1 text-black-50"></i>
                                            </div>
                                            <a href="dashboard_dosen.php?page=mahasiswa" class="stretched-link small text-decoration-none text-info mt-2 d-block">Lihat Data &rarr;</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="card">
                            <div class="card-header bg-white fw-bold"><i class="bi bi-info-circle text-info"></i> Informasi Sistem</div>
                            <div class="card-body">
                                <p class="card-text small text-muted">
                                    Sistem Informasi Tugas Akhir (SITA) Versi 1.0.<br>
                                    Terakhir diperbarui: 15 September 2025.
                                </p>
                            </div>
                        </div>
                        <?php
                        // === AKHIR DASHBOARD HOME ===
                        break;

                    // CASE UNTUK INCLUDE FILE LAIN
                    case 'bimbingan': include 'kelola_bimbingan.php'; break;
                    case 'chat':      include 'chat_dosen_view.php'; break;
                    case 'proposal':  include 'admin_proposal.php'; break;
                    case 'sidang':    include 'kelola_sidang.php'; break;
                    case 'extend':    include 'admin_perpanjangan.php'; break;
                    case 'mahasiswa': include 'admin_mahasiswa.php'; break;
                    
                    // FILE BARU UNTUK KELOLA DOSEN
                    case 'master_dosen': include 'admin_dosen.php'; break;

                    default:
                        echo "<div class='alert alert-danger'>Halaman tidak ditemukan!</div>";
                        break;
                }
                ?>
            </div>

            <div class="col-lg-3">
                <div class="card bg-dark text-white mb-3">
                    <div class="card-header bg-dark border-bottom border-secondary fw-bold">
                        <i class="bi bi-calendar-event"></i> Agenda Akademik
                    </div>
                    <ul class="list-group list-group-flush small bg-dark">
                        <li class="list-group-item bg-dark text-white-50 border-secondary">
                            <strong class="text-white">15 - 20 Sep</strong><br> Pengajuan Proposal Gelombang 1
                        </li>
                        <li class="list-group-item bg-dark text-white-50 border-secondary">
                            <strong class="text-white">05 Nov 2025</strong><br> Pelaksanaan Sidang Periode 1
                        </li>
                        <li class="list-group-item bg-dark text-white-50 border-secondary">
                            <strong class="text-white">20 Des 2025</strong><br> Batas Akhir Input Nilai
                        </li>
                    </ul>
                </div>

                <div class="card">
                    <div class="card-header fw-bold bg-light">Aksi Cepat</div>
                    <div class="card-body d-grid gap-2">
                         <a href="#" class="btn btn-outline-primary btn-sm text-start"><i class="bi bi-cloud-download me-2"></i> Download Panduan TA</a>
                         <a href="#" class="btn btn-outline-primary btn-sm text-start"><i class="bi bi-printer me-2"></i> Cetak Berita Acara</a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <footer class="text-center" style="background: #157347;">
        <div class="container">
            <h6 class="fw-bold mb-2">Universitas Pembangunan Jaya</h6>
            <small class="d-block text-white-50">&copy; 2025 Kelompok Sistem Informasi.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>