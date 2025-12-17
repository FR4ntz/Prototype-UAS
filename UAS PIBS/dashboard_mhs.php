<?php
session_start();
include 'koneksi.php';

// 1. CEK KEAMANAN AKSES
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'mahasiswa') { 
    header("Location: index.php"); 
    exit; 
}

$nim = $_SESSION['nim'];

// 2. AMBIL DATA MAHASISWA (Nama, SKS, JSDP)
$mhs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM mahasiswa WHERE nim='$nim'"));

// 3. AMBIL DATA PROPOSAL (Untuk cek status & judul)
$prop = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM proposal WHERE nim='$nim'"));

// 4. HITUNG PROGRESS BIMBINGAN (Hanya yang statusnya 'ACC')
$cek_bim = mysqli_query($conn, "SELECT * FROM bimbingan WHERE nim='$nim' AND status='ACC'");
$jml_bim = mysqli_num_rows($cek_bim);

// Logika Persentase (Target 8 kali = 100%)
$persen_bim = ($jml_bim / 8) * 100; 
if($persen_bim > 100) $persen_bim = 100;

// 5. AMBIL NOTIFIKASI BELUM DIBACA
$notif_query = mysqli_query($conn, "SELECT * FROM notifikasi WHERE nim='$nim' AND is_read=0 ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa - SITA UPJ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        /* Styling Layout agar sesuai Soal UAS */
        body { background-color: #f8f9fa; }
        header { background: #003366; color: white; padding: 15px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        footer { background: #003366; color: white; padding: 20px 0; margin-top: 50px; }
        
        .nav-link { color: #495057; font-weight: 500; padding: 10px 15px; border-radius: 5px; transition: 0.3s; }
        .nav-link:hover { background-color: #e9ecef; color: #0d6efd; transform: translateX(5px); }
        .nav-link.active { background-color: #e7f1ff; color: #0d6efd; font-weight: bold; }
        
        .card { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); margin-bottom: 20px; }
        .card-header { background-color: white; border-bottom: 2px solid #f0f0f0; font-weight: bold; }
        
        /* Responsive Breakpoints */
        @media (max-width: 768px) {
            .col-lg-3, .col-lg-6 { margin-bottom: 20px; }
        }
    </style>
</head>
<body>

    <header>
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="bi bi-mortarboard-fill fs-3 me-3"></i>
                <div>
                    <h5 class="m-0 fw-bold">SITA - UPJ</h5>
                    <small style="opacity: 0.8;">Sistem Informasi Tugas Akhir</small>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <div class="text-end me-3 d-none d-md-block">
                    <span class="d-block fw-bold"><?= $_SESSION['user'] ?></span>
                    <small style="opacity: 0.8;"><?= $nim ?></small>
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
                    <div class="card-header"><i class="bi bi-menu-button-wide"></i> Menu Mahasiswa</div>
                    <div class="card-body p-2">
                        <nav class="nav flex-column">
                            <a class="nav-link active" href="dashboard_mhs.php">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                            <a class="nav-link" href="pengajuan.php">
                                <i class="bi bi-file-earmark-plus me-2"></i> Pengajuan Proposal
                            </a>
                            <a class="nav-link" href="bimbingan.php">
                                <i class="bi bi-journal-text me-2"></i> Bimbingan (Logbook)
                            </a>
                            
                            <hr class="my-2 border-secondary opacity-25">
                            
                            <a class="nav-link text-success" href="chat_dosen.php">
                                <i class="bi bi-chat-dots me-2"></i> Chat Pembimbing
                            </a>
                            <a class="nav-link text-primary" href="ai_assistant.php">
                                <i class="bi bi-stars me-2"></i> Konsultasi AI
                            </a>
                            <a class="nav-link text-danger" href="perpanjangan_ta.php">
                                <i class="bi bi-hourglass-split me-2"></i> Perpanjangan TA
                            </a>
                            <a class="nav-link text-secondary" href="laporan_ta.php" target="_blank">
                                <i class="bi bi-printer me-2"></i> Cetak Laporan
                            </a>
                            <a class="nav-link text-info" href="panduan.php">
                                <i class="bi bi-question-circle me-2"></i> Bantuan
                            </a>
                        </nav>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header"><i class="bi bi-person-vcard"></i> Info Akademik</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Total SKS
                                <span class="badge <?= ($mhs['total_sks'] >= 120) ? 'text-bg-success' : 'text-bg-danger' ?> rounded-pill">
                                    <?= $mhs['total_sks'] ?> / 120
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Poin JSDP
                                <span class="badge <?= ($mhs['jsdp_poin'] >= 600) ? 'text-bg-success' : 'text-bg-danger' ?> rounded-pill">
                                    <?= $mhs['jsdp_poin'] ?> / 600
                                </span>
                            </li>
                        </ul>
                        <div class="mt-3 text-center">
                            <?php if($mhs['total_sks'] >= 120 && $mhs['jsdp_poin'] >= 600): ?>
                                <span class="text-success small fw-bold"><i class="bi bi-check-circle-fill"></i> Syarat Terpenuhi</span>
                            <?php else: ?>
                                <span class="text-danger small fw-bold"><i class="bi bi-x-circle-fill"></i> Belum Memenuhi</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                
                <?php if(mysqli_num_rows($notif_query) > 0): ?>
                    <?php while($n = mysqli_fetch_array($notif_query)): ?>
                        <div class="alert alert-warning alert-dismissible fade show shadow-sm border-warning" role="alert">
                            <strong><i class="bi bi-bell-fill"></i> <?= htmlspecialchars($n['judul']) ?></strong><br>
                            <small><?= htmlspecialchars($n['pesan']) ?></small>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>

                <div class="alert alert-primary shadow-sm d-flex align-items-center border-0" role="alert" style="background: linear-gradient(45deg, #0d6efd, #0a58ca); color: white;">
                    <i class="bi bi-info-circle-fill fs-1 me-3 opacity-50"></i>
                    <div>
                        <h5 class="alert-heading fw-bold mb-1">Selamat Datang, <?= explode(' ', $_SESSION['user'])[0] ?>!</h5>
                        <p class="mb-0 small opacity-75">Pantau terus progres Tugas Akhir Anda dan jangan lupa isi logbook bimbingan.</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-white text-primary">
                        <i class="bi bi-file-earmark-text-fill"></i> Status Proposal Tugas Akhir
                    </div>
                    <div class="card-body">
                        <?php if ($prop): ?>
                            <h5 class="card-title fw-bold text-dark"><?= htmlspecialchars($prop['judul']) ?></h5>
                            <div class="d-flex gap-2 my-2">
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-tag"></i> <?= $prop['jenis_ta'] ?>
                                </span>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-calendar"></i> <?= $prop['tanggal_pengajuan'] ?>
                                </span>
                            </div>
                            
                            <div class="mt-3">
                                Status Saat Ini: 
                                <?php 
                                    $status_color = 'secondary';
                                    if($prop['status'] == 'Disetujui') $status_color = 'success';
                                    elseif($prop['status'] == 'Revisi') $status_color = 'warning';
                                    elseif($prop['status'] == 'Ditolak') $status_color = 'danger';
                                    
                                    echo "<span class='badge bg-$status_color fs-6'>{$prop['status']}</span>"; 
                                ?>
                            </div>

                            <?php if($prop['status'] == 'Disetujui'): ?>
                                <hr>
                                <a href="bimbingan.php" class="btn btn-outline-success btn-sm w-100">
                                    <i class="bi bi-plus-circle"></i> Tambah Logbook Bimbingan Baru
                                </a>
                            <?php endif; ?>
                            
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-file-earmark-x fs-1 text-muted"></i>
                                <p class="text-muted mt-2">Anda belum mengajukan proposal.</p>
                                <a href="pengajuan.php" class="btn btn-primary">
                                    <i class="bi bi-rocket-takeoff"></i> Ajukan Sekarang
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-end mb-2">
                            <div>
                                <h6 class="fw-bold m-0"><i class="bi bi-bar-chart-line-fill text-success"></i> Progres Bimbingan</h6>
                                <small class="text-muted">Target: Minimal 8 kali (ACC)</small>
                            </div>
                            <span class="fs-4 fw-bold text-success"><?= $jml_bim ?><small class="fs-6 text-muted">/8</small></span>
                        </div>
                        
                        <div class="progress" style="height: 25px; border-radius: 15px;">
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                                 role="progressbar" 
                                 style="width: <?= $persen_bim ?>%;" 
                                 aria-valuenow="<?= $jml_bim ?>" aria-valuemin="0" aria-valuemax="8">
                                <?= round($persen_bim) ?>%
                            </div>
                        </div>

                        <?php if($jml_bim >= 8): ?>
                            <div class="alert alert-success mt-3 py-2 small mb-0">
                                <i class="bi bi-check-circle-fill"></i> Selamat! Syarat bimbingan terpenuhi. Anda dapat mendaftar sidang.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title fw-bold"><i class="bi bi-megaphone"></i> Informasi Akademik</h5>
                        <p class="card-text text-muted small mb-2"><i class="bi bi-clock"></i> Diposting: 15 Sep 2025</p>
                        <p class="card-text">
                            Mahasiswa yang telah menyelesaikan minimal 8x bimbingan dapat segera mendaftarkan diri untuk sidang proposal. 
                            Pastikan semua dokumen persyaratan (B-1 s/d B-4) telah lengkap dan diunggah.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white fw-bold">
                        <i class="bi bi-alarm"></i> Deadline Penting
                    </div>
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item list-group-item-warning">
                            <strong><i class="bi bi-exclamation-circle"></i> Batas Revisi:</strong><br>
                            20 September 2025
                        </li>
                        <li class="list-group-item">
                            <strong><i class="bi bi-calendar-event"></i> Sidang Periode 1:</strong><br>
                            05 November 2025
                        </li>
                        <li class="list-group-item">
                            <strong><i class="bi bi-calendar-event"></i> Sidang Periode 2:</strong><br>
                            20 Desember 2025
                        </li>
                    </ul>
                </div>

                <div class="card mt-3">
                    <div class="card-header fw-bold bg-light">
                        <i class="bi bi-folder2-open"></i> Dokumen
                    </div>
                    <div class="list-group list-group-flush small">
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            Template Proposal 2025 <i class="bi bi-download"></i>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            Lembar Persetujuan <i class="bi bi-download"></i>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            Pedoman Penulisan TA <i class="bi bi-download"></i>
                        </a>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body text-center">
                        <h6 class="fw-bold text-muted">Semester Gasal 2025/2026</h6>
                        <h2 class="display-4 fw-bold text-primary"><?= date('d') ?></h2>
                        <span class="text-uppercase ls-1"><?= date('F Y') ?></span>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <footer class="text-center">
        <div class="container">
            <h6 class="fw-bold mb-2">Universitas Pembangunan Jaya</h6>
            <small class="d-block text-white-50 mb-3">Jln. Cendrawasih Raya Blok B7/P, Bintaro Jaya, Tangerang Selatan</small>
            
            <div class="social-links">
                <a href="#" class="text-white mx-2 fs-5"><i class="bi bi-facebook"></i></a>
                <a href="#" class="text-white mx-2 fs-5"><i class="bi bi-twitter-x"></i></a>
                <a href="#" class="text-white mx-2 fs-5"><i class="bi bi-instagram"></i></a>
                <a href="#" class="text-white mx-2 fs-5"><i class="bi bi-linkedin"></i></a>
            </div>
            
            <hr class="border-light opacity-25 my-3">
            <small class="d-block text-white-50">&copy; 2025 Kelompok Sistem Informasi. All Rights Reserved.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>