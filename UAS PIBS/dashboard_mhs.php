<?php
session_start();
include 'koneksi.php';

// 1. CEK KEAMANAN AKSES
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'mahasiswa') { 
    header("Location: index.php"); 
    exit; 
}

$nim = $_SESSION['nim'];

// 2. DATA GLOBAL (Dipakai di Sidebar & Header)
$mhs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM mahasiswa WHERE nim='$nim'"));
$prop = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM proposal WHERE nim='$nim'"));

// 3. LOGIKA PROGRESS BIMBINGAN
$cek_bim = mysqli_query($conn, "SELECT * FROM bimbingan WHERE nim='$nim' AND (status='ACC' OR status='Disetujui')");
$jml_bim = mysqli_num_rows($cek_bim);
$persen_bim = ($jml_bim / 8) * 100; 
if($persen_bim > 100) $persen_bim = 100;

// 4. AMBIL NOTIFIKASI (Khusus tampilan Home)
$notif_query = mysqli_query($conn, "SELECT * FROM notifikasi WHERE nim='$nim' AND is_read=0 ORDER BY tanggal DESC");

// 5. TENTUKAN HALAMAN AKTIF (Logic Switch Page)
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SITA UPJ - Dashboard Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
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
                            <a class="nav-link <?= ($page=='home')?'active':'' ?>" href="dashboard_mhs.php?page=home">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                            <a class="nav-link <?= ($page=='pengajuan')?'active':'' ?>" href="dashboard_mhs.php?page=pengajuan">
                                <i class="bi bi-file-earmark-plus me-2"></i> Pengajuan Proposal
                            </a>
                            <a class="nav-link <?= ($page=='bimbingan')?'active':'' ?>" href="dashboard_mhs.php?page=bimbingan">
                                <i class="bi bi-journal-text me-2"></i> Bimbingan (Logbook)
                            </a>

                            <a class="nav-link <?= ($page=='daftar_sidang')?'active':'' ?>" href="dashboard_mhs.php?page=daftar_sidang">
                                <i class="bi bi-pencil-square me-2"></i> Daftar Sidang Akhir
                            </a>
                            <a class="nav-link <?= ($page=='jadwal')?'active':'' ?>" href="dashboard_mhs.php?page=jadwal">
                                <i class="bi bi-calendar-event me-2"></i> Jadwal Sidang
                            </a>
                            
                            <hr class="my-2 border-secondary opacity-25">
                            
                            <a class="nav-link <?= ($page=='chat')?'active':'' ?>" href="dashboard_mhs.php?page=chat">
                                <i class="bi bi-chat-dots me-2"></i> Chat Pembimbing
                            </a>
                            <a class="nav-link <?= ($page=='ai')?'active':'' ?>" href="dashboard_mhs.php?page=ai">
                                <i class="bi bi-stars me-2"></i> Konsultasi AI
                            </a>
                            <a class="nav-link <?= ($page=='extend')?'active':'' ?>" href="dashboard_mhs.php?page=extend">
                                <i class="bi bi-hourglass-split me-2"></i> Perpanjangan TA
                            </a>
                            <a class="nav-link <?= ($page=='laporan')?'active':'' ?>" href="laporan_ta.php" target="_blank">
                                <i class="bi bi-printer me-2"></i> Cetak Laporan
                            </a>
                            <a class="nav-link <?= ($page=='bantuan')?'active':'' ?>" href="dashboard_mhs.php?page=bantuan">
                                <i class="bi bi-question-circle me-2"></i> Bantuan
                            </a>
                        </nav>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header"><i class="bi bi-person-vcard"></i> Info Akademik</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                Total SKS
                                <span class="badge <?= ($mhs['total_sks'] >= 120) ? 'text-bg-success' : 'text-bg-danger' ?> rounded-pill">
                                    <?= $mhs['total_sks'] ?> / 120
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
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
                <?php 
                switch ($page) {
                    case 'home':
                        // === KONTEN DASHBOARD UTAMA ===
                        ?>
                        
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
                                        <span class="badge bg-light text-dark border"><i class="bi bi-tag"></i> <?= $prop['jenis_ta'] ?></span>
                                        <span class="badge bg-light text-dark border"><i class="bi bi-calendar"></i> <?= $prop['tanggal_pengajuan'] ?></span>
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
                                        <a href="dashboard_mhs.php?page=bimbingan" class="btn btn-outline-success btn-sm w-100">
                                            <i class="bi bi-plus-circle"></i> Tambah Logbook Bimbingan Baru
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="bi bi-file-earmark-x fs-1 text-muted"></i>
                                        <p class="text-muted mt-2">Anda belum mengajukan proposal.</p>
                                        <a href="dashboard_mhs.php?page=pengajuan" class="btn btn-primary">
                                            <i class="bi bi-rocket-takeoff"></i> Ajukan Sekarang
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-end mb-2">
                                    <div>
                                        <h6 class="fw-bold m-0"><i class="bi bi-bar-chart-line-fill text-success"></i> Progres Bimbingan</h6>
                                        <small class="text-muted">Target: Minimal 8 kali (ACC)</small>
                                    </div>
                                    <span class="fs-4 fw-bold text-success"><?= $jml_bim ?><small class="fs-6 text-muted">/8</small></span>
                                </div>
                                <div class="progress" style="height: 25px; border-radius: 15px;">
                                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $persen_bim ?>%;">
                                        <?= round($persen_bim) ?>%
                                    </div>
                                </div>
                                <?php if($jml_bim >= 8): ?>
                                    <div class="alert alert-success mt-3 py-2 small mb-0">
                                        <i class="bi bi-check-circle-fill"></i> Syarat bimbingan terpenuhi. <a href="dashboard_mhs.php?page=daftar_sidang" class="fw-bold text-success">Daftar Sidang Sekarang</a>.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-body">
                                <h5 class="card-title fw-bold"><i class="bi bi-megaphone"></i> Informasi Akademik</h5>
                                <p class="card-text text-muted small mb-2"><i class="bi bi-clock"></i> Diposting: 15 Sep 2025</p>
                                <p class="card-text">Mahasiswa yang telah menyelesaikan minimal 8x bimbingan dapat segera mendaftarkan diri untuk sidang proposal.</p>
                            </div>
                        </div>
                        <?php
                        // === AKHIR KONTEN DASHBOARD UTAMA ===
                        break;

                    case 'pengajuan': include 'pengajuan.php'; break;
                    case 'bimbingan': include 'bimbingan.php'; break;
                    
                    // [TAMBAHAN BARU] INCLUDE FILE SIDANG
                    case 'daftar_sidang': include 'mhs_sidang.php'; break;
                    // [AKHIR TAMBAHAN]

                    case 'jadwal': include 'jadwal_sidang_view.php'; break; // pastikan file ini ada
                    case 'chat': include 'chat_dosen.php'; break;
                    case 'ai': include 'ai_assistant.php'; break;
                    case 'extend': include 'perpanjangan_ta.php'; break;
                    case 'bantuan': include 'panduan.php'; break;
                    default: echo "<div class='alert alert-danger'>Halaman tidak ditemukan!</div>"; break;
                }
                ?>
            </div>

            <div class="col-lg-3">
                
                <?php if($page == 'home' || $page == 'pengajuan'): ?>
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white fw-bold"><i class="bi bi-alarm"></i> Deadline Penting</div>
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item list-group-item-warning"><strong><i class="bi bi-exclamation-circle"></i> Revisi:</strong> 20 Sept 2025</li>
                            <li class="list-group-item"><strong><i class="bi bi-calendar-event"></i> Sidang 1:</strong> 05 Nov 2025</li>
                            <li class="list-group-item"><strong><i class="bi bi-calendar-event"></i> Sidang 2:</strong> 20 Des 2025</li>
                        </ul>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header fw-bold bg-light"><i class="bi bi-folder2-open"></i> Dokumen</div>
                        <div class="list-group list-group-flush small">
                            <a href="#" class="list-group-item list-group-item-action">Template Proposal 2025 <i class="bi bi-download float-end"></i></a>
                            <a href="#" class="list-group-item list-group-item-action">Lembar Persetujuan <i class="bi bi-download float-end"></i></a>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-body text-center">
                            <h6 class="fw-bold text-muted">Semester Gasal 2025</h6>
                            <h2 class="display-4 fw-bold text-primary"><?= date('d') ?></h2>
                            <span class="text-uppercase ls-1"><?= date('F Y') ?></span>
                        </div>
                    </div>

                <?php elseif($page == 'ai'): ?>
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white fw-bold"><i class="bi bi-lightbulb"></i> Tips Prompt</div>
                        <div class="card-body small">
                            <p class="mb-2">Cobalah bertanya:</p>
                            <ul class="ps-3 mb-0">
                                <li>"Ide judul skripsi IoT."</li>
                                <li>"Buatkan latar belakang..."</li>
                            </ul>
                        </div>
                    </div>

                <?php elseif($page == 'jadwal'): ?>
                     <div class="card border-info">
                        <div class="card-header bg-info text-white fw-bold"><i class="bi bi-info-circle"></i> Tata Tertib</div>
                        <div class="card-body small">Wajib menggunakan jas almamater dan datang 30 menit sebelum jadwal.</div>
                    </div>

                <?php elseif($page == 'extend'): ?>
                     <div class="card border-danger">
                        <div class="card-header bg-danger text-white fw-bold">Syarat</div>
                        <div class="card-body small">Maksimal perpanjangan 6 bulan dengan persetujuan Koordinator.</div>
                    </div>

                <?php else: ?>
                    <div class="card">
                        <div class="card-header fw-bold bg-light">Butuh Bantuan?</div>
                        <div class="card-body text-center">
                            <i class="bi bi-headset fs-1 text-info"></i>
                            <a href="dashboard_mhs.php?page=bantuan" class="btn btn-outline-info btn-sm w-100 mt-2">Hubungi Helpdesk</a>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

        </div>
    </div>

    <footer class="text-center">
        <div class="container">
            <h6 class="fw-bold mb-2">Universitas Pembangunan Jaya</h6>
            <small class="d-block text-white-50">Jln. Cendrawasih Raya Blok B7/P, Bintaro Jaya, Tangerang Selatan</small>
            <div class="social-links mt-2">
                <a href="#" class="text-white mx-2"><i class="bi bi-instagram"></i></a>
                <a href="#" class="text-white mx-2"><i class="bi bi-linkedin"></i></a>
            </div>
            <hr class="border-light opacity-25 my-3">
            <small class="d-block text-white-50">&copy; 2025 Kelompok Sistem Informasi. All Rights Reserved.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php if($page == 'ai'): ?>
    <script>
        document.getElementById("userInput").addEventListener("keypress", function(e) { if(e.key==="Enter") sendMessage(); });
        function sendMessage() {
            let input = document.getElementById('userInput');
            let message = input.value.trim();
            let chatBox = document.getElementById('chatContainer');
            let loading = document.getElementById('loading');
            if(message === "") return;
            chatBox.innerHTML += `<div class="msg-container"><div class="msg-user">${message}</div></div>`;
            input.value = ''; chatBox.scrollTop = chatBox.scrollHeight; loading.style.display = 'block';
            fetch('ai_handler.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ message: message }) })
            .then(res => res.json()).then(data => { loading.style.display = 'none'; chatBox.innerHTML += `<div class="msg-container"><div class="msg-ai">${data.reply}</div></div>`; chatBox.scrollTop = chatBox.scrollHeight; })
            .catch(err => { loading.style.display = 'none'; chatBox.innerHTML += `<div class="msg-container"><div class="msg-ai text-danger">Error koneksi server.</div></div>`; });
        }
    </script>
    <?php endif; ?>

</body>
</html>