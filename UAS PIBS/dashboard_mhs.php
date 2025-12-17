<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login dan role-nya mahasiswa
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'mahasiswa') { 
    header("Location: index.php"); 
    exit; 
}

$nim = $_SESSION['nim'];

// 1. Ambil data Mahasiswa (Nama, SKS, JSDP)
$mhs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM mahasiswa WHERE nim='$nim'"));

// 2. Ambil data Proposal (untuk cek status)
$prop = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM proposal WHERE nim='$nim'"));

// 3. Hitung Jumlah Bimbingan yang sudah ACC (untuk progres bar)
$cek_bim = mysqli_query($conn, "SELECT * FROM bimbingan WHERE nim='$nim' AND status='ACC'");
$jml_bim = mysqli_num_rows($cek_bim);
$persen_bim = ($jml_bim / 8) * 100; // Asumsi target 8 kali
if($persen_bim > 100) $persen_bim = 100;

// 4. (FITUR BARU) Ambil Notifikasi Belum Dibaca
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
        /* Layout CSS sesuai panduan UAS (Desktop) */
        header { background: #003366; color: white; padding: 15px 0; }
        footer { background: #003366; color: white; padding: 20px 0; margin-top: 50px; }
        .nav-link { color: #333; font-weight: 500; }
        .nav-link:hover { background-color: #f8f9fa; color: #0d6efd; }
        .nav-link.active { background-color: #e7f1ff; color: #0d6efd; font-weight: bold; }
        
        /* Responsiveness untuk Mobile */
        @media (max-width: 768px) {
            .col-md-3, .col-md-6 { margin-bottom: 20px; }
        }
    </style>
</head>
<body>

    <header>
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="bi bi-mortarboard-fill fs-3 me-2"></i>
                <div>
                    <h5 class="m-0 fw-bold">SITA - UPJ</h5>
                    <small>Sistem Informasi Tugas Akhir</small>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <span class="me-3 d-none d-md-block">Halo, <?= $_SESSION['user'] ?> (<?= $nim ?>)</span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </header>

    <div class="container my-4">
        <div class="row">
            
            <div class="col-lg-3 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white fw-bold">Menu Mahasiswa</div>
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
                            
                            <hr class="my-2">
                            <a class="nav-link text-success" href="chat_dosen.php">
                                <i class="bi bi-chat-dots me-2"></i> Chat Dosen
                            </a>
                            <a class="nav-link text-primary" href="ai_assistant.php">
                                <i class="bi bi-robot me-2"></i> Konsultasi AI
                            </a>
                            <a class="nav-link text-warning" href="laporan_ta.php" target="_blank">
                                <i class="bi bi-printer me-2"></i> Cetak Laporan Status
                            </a>
                            <a class="nav-link text-info" href="panduan.php">
                                <i class="bi bi-question-circle me-2"></i> Panduan & Bantuan
                            </a>
                        </nav>
                    </div>
                </div>
                
                <div class="card shadow-sm mt-3">
                    <div class="card-header bg-white fw-bold">Status Akademik</div>
                    <div class="card-body">
                        <ul class="list-unstyled small mb-0">
                            <li class="mb-2 d-flex justify-content-between">
                                <span>Total SKS:</span> 
                                <span class="fw-bold <?= ($mhs['total_sks'] >= 120) ? 'text-success' : 'text-danger' ?>">
                                    <?= $mhs['total_sks'] ?> / 120
                                </span>
                            </li>
                            <li class="d-flex justify-content-between">
                                <span>Poin JSDP:</span> 
                                <span class="fw-bold <?= ($mhs['jsdp_poin'] >= 600) ? 'text-success' : 'text-danger' ?>">
                                    <?= $mhs['jsdp_poin'] ?> / 600
                                </span>
                            </li>
                        </ul>
                        <hr>
                        <?php if($mhs['total_sks'] >= 120 && $mhs['jsdp_poin'] >= 600): ?>
                            <div class="alert alert-success py-1 px-2 small text-center mb-0">
                                <i class="bi bi-check-circle"></i> Syarat Terpenuhi
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger py-1 px-2 small text-center mb-0">
                                <i class="bi bi-x-circle"></i> Belum Memenuhi
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                
                <?php if(mysqli_num_rows($notif_query) > 0): ?>
                    <?php while($n = mysqli_fetch_array($notif_query)): ?>
                        <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
                            <strong><i class="bi bi-exclamation-triangle-fill"></i> <?= $n['judul'] ?>:</strong> <?= $n['pesan'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>

                <div class="alert alert-primary shadow-sm d-flex align-items-center" role="alert">
                    <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                    <div>
                        <strong>Selamat Datang, <?= $_SESSION['user'] ?>!</strong><br>
                        Silakan pantau status pengajuan dan bimbingan Anda secara berkala.
                    </div>
                </div>

                <div class="card shadow-sm mb-3 border-primary">
                    <div class="card-header bg-primary text-white fw-bold">
                        <i class="bi bi-file-earmark-text"></i> Status Proposal TA
                    </div>
                    <div class="card-body">
                        <?php if ($prop): ?>
                            <h5 class="card-title"><?= $prop['judul'] ?></h5>
                            <p class="card-text text-muted small mb-2">Jenis: <?= $prop['jenis_ta'] ?> | Tanggal: <?= $prop['tanggal_pengajuan'] ?></p>
                            <p class="card-text">Status Saat Ini: 
                                <?php 
                                    $status_color = 'secondary';
                                    if($prop['status'] == 'Disetujui') $status_color = 'success';
                                    elseif($prop['status'] == 'Revisi') $status_color = 'warning';
                                    elseif($prop['status'] == 'Ditolak') $status_color = 'danger';
                                    
                                    echo "<span class='badge bg-$status_color fs-6'>{$prop['status']}</span>"; 
                                ?>
                            </p>
                            <?php if($prop['status'] == 'Disetujui'): ?>
                                <hr>
                                <a href="bimbingan.php" class="btn btn-success btn-sm w-100">
                                    <i class="bi bi-journal-plus"></i> Isi Logbook Bimbingan
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-center text-muted my-3">Anda belum mengajukan proposal tugas akhir.</p>
                            <a href="pengajuan.php" class="btn btn-primary w-100">Ajukan Proposal Baru</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold m-0"><i class="bi bi-graph-up"></i> Progres Bimbingan</h6>
                            <span class="badge bg-secondary"><?= $jml_bim ?> / 8 Pertemuan</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" 
                                 style="width: <?= $persen_bim ?>%;" aria-valuenow="<?= $jml_bim ?>" aria-valuemin="0" aria-valuemax="8">
                                <?= round($persen_bim) ?>%
                            </div>
                        </div>
                        <small class="text-muted mt-2 d-block">*Syarat sidang minimal 8x bimbingan (Status: ACC).</small>
                    </div>
                </div>

                <article class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">Alur Pendaftaran Sidang</h5>
                        <p class="card-text text-muted small"><i class="bi bi-clock"></i> Diposting: 15 Sep 2025</p>
                        <p class="card-text">Mahasiswa yang telah menyelesaikan minimal 8x bimbingan dapat segera mendaftarkan diri untuk sidang proposal. Harap melengkapi dokumen persyaratan di menu Upload.</p>
                    </div>
                </article>
            </div>

            <div class="col-lg-3">
                <aside class="card shadow-sm mb-3">
                    <div class="card-header bg-danger text-white fw-bold">
                        <i class="bi bi-bell-fill"></i> Jadwal Penting
                    </div>
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item list-group-item-warning">
                            <strong>Deadline Revisi:</strong><br>20 September 2025
                        </li>
                        <li class="list-group-item">
                            <strong>Sidang Periode 1:</strong><br>05 November 2025
                        </li>
                    </ul>
                </aside>

                <aside class="card shadow-sm">
                    <div class="card-header bg-light fw-bold">Dokumen</div>
                    <div class="list-group list-group-flush small">
                        <a href="#" class="list-group-item list-group-item-action">Template Proposal 2025</a>
                        <a href="#" class="list-group-item list-group-item-action">Lembar Persetujuan</a>
                        <a href="#" class="list-group-item list-group-item-action">Pedoman Penulisan</a>
                    </div>
                </aside>
            </div>

        </div>
    </div>

    <footer class="text-center">
        <div class="container">
            <p class="mb-1 fw-bold">Universitas Pembangunan Jaya</p>
            <small class="d-block text-white-50">Jln. Cendrawasih Raya Blok B7/P, Bintaro Jaya, Tangerang Selatan</small>
            <div class="mt-2">
                <a href="#" class="text-white mx-2"><i class="bi bi-facebook"></i></a>
                <a href="#" class="text-white mx-2"><i class="bi bi-twitter"></i></a>
                <a href="#" class="text-white mx-2"><i class="bi bi-instagram"></i></a>
            </div>
            <small class="mt-3 d-block text-white-50">&copy; 2025 Kelompok Sistem Informasi. All Rights Reserved.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>