<?php
session_start();
include 'koneksi.php';

// 1. CEK KEAMANAN AKSES
// Menggunakan strtolower() agar 'Mahasiswa' atau 'mahasiswa' tetap terbaca benar
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) != 'mahasiswa') { 
    header("Location: index.php"); 
    exit; 
}

// Ambil NIM (Support 'username' atau 'nim' dari session login)
$nim = isset($_SESSION['username']) ? $_SESSION['username'] : (isset($_SESSION['nim']) ? $_SESSION['nim'] : '');
$nama_user = isset($_SESSION['user']) ? $_SESSION['user'] : 'Mahasiswa';

// 2. DATA GLOBAL
// Menggunakan @ untuk mencegah error jika tabel kosong/salah nama
$mhs = @mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM mahasiswa WHERE nim='$nim'"));
$prop = @mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM proposal WHERE nim='$nim'"));

// --- PERBAIKAN LOGIKA SKS & JSDP (ANTI ERROR CASE SENSITIVE) ---
// Kita cek berbagai kemungkinan nama kolom di database
$sks = $mhs['total_sks'] ?? $mhs['Total_SKS'] ?? $mhs['SKS'] ?? $mhs['sks'] ?? 0;
$jsdp = $mhs['jsdp_poin'] ?? $mhs['Total_JSDP'] ?? $mhs['JSDP'] ?? $mhs['jsdp'] ?? $mhs['poin_jsdp'] ?? 0;

// 3. LOGIKA PROGRESS BIMBINGAN
$cek_bim = @mysqli_query($conn, "SELECT * FROM bimbingan WHERE nim='$nim' AND (status='ACC' OR status='Disetujui')");
$jml_bim = ($cek_bim) ? mysqli_num_rows($cek_bim) : 0;
$persen_bim = ($jml_bim / 8) * 100; 
if($persen_bim > 100) $persen_bim = 100;

// 4. AMBIL NOTIFIKASI
$notif_query = @mysqli_query($conn, "SELECT * FROM notifikasi WHERE nim='$nim' AND is_read=0 ORDER BY tanggal DESC");

// 5. TENTUKAN HALAMAN AKTIF
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
                    <span class="d-block fw-bold"><?= $nama_user ?></span>
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
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-white fw-bold"><i class="bi bi-menu-button-wide"></i> Menu Mahasiswa</div>
                    <div class="card-body p-2">
                        <nav class="nav flex-column gap-1">
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
                            
                            <a class="nav-link <?= ($page=='extend')?'active':'' ?>" href="dashboard_mhs.php?page=pengajuan">
                                <i class="bi bi-hourglass-split me-2"></i> Perpanjangan TA
                            </a>
                            
                            <a class="nav-link <?= ($page=='ai')?'active':'' ?>" href="dashboard_mhs.php?page=ai">
                                <i class="bi bi-stars me-2"></i> Konsultasi AI
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
                
                <div class="card mt-3 shadow-sm border-0">
                    <div class="card-header bg-white fw-bold"><i class="bi bi-person-vcard"></i> Info Akademik</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                Total SKS
                                <span class="badge <?= ($sks >= 120) ? 'text-bg-success' : 'text-bg-danger' ?> rounded-pill">
                                    <?= $sks ?> / 120
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                Poin JSDP
                                <span class="badge <?= ($jsdp >= 600) ? 'text-bg-success' : 'text-bg-danger' ?> rounded-pill">
                                    <?= $jsdp ?> / 600
                                </span>
                            </li>
                        </ul>
                        <div class="mt-3 text-center">
                            <?php if($sks >= 120 && $jsdp >= 600): ?>
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
                $data_berita = [
    1 => [
        'judul'   => 'Alur Pendaftaran Tugas Akhir 2025',
        'tanggal' => '22 Des 2025',
        'penulis' => 'Admin',
        'gambar'  => 'assets/img/ta1.jpg', // Pastikan path gambar sesuai
        'isi'     => '<p>Pendaftaran Tugas Akhir periode Genap 2025 telah dibuka. Mahasiswa diharapkan memperhatikan alur berikut:</p>
                      <ol>
                        <li>Melakukan pembayaran biaya TA.</li>
                        <li>Mengisi KRS Tugas Akhir.</li>
                        <li>Mengajukan topik melalui menu "Pengajuan Proposal".</li>
                        <li>Menunggu persetujuan Dosen Pembimbing Akademik.</li>
                      </ol>
                      <p>Pastikan semua syarat administrasi telah terpenuhi sebelum tanggal 30 Januari 2026.</p>'
    ],
    2 => [
        'judul'   => 'Tips Sukses Menghadapi Sidang Skripsi',
        'tanggal' => '22 Des 2025',
        'penulis' => 'Koordinator TA',
        'gambar'  => 'assets/img/ta2.jpg',
        'isi'     => '<p>Sidang skripsi seringkali menjadi momen menegangkan. Berikut tips agar lancar:</p>
                      <ul>
                        <li>Pahami materi skripsi dari A sampai Z.</li>
                        <li>Buat slide presentasi yang ringkas dan visual.</li>
                        <li>Latihan presentasi di depan cermin atau teman.</li>
                        <li>Jaga etika dan penampilan saat sidang.</li>
                      </ul>
                      <p>Semoga sukses untuk seluruh mahasiswa tingkat akhir!</p>'
    ]
];
switch ($page) {
    case 'home':
        ?>
        <div class="mb-4">
            <h5 class="fw-bold mb-1">
                Selamat Datang, <?= htmlspecialchars($nama_user) ?> 👋
            </h5>
            <p class="text-muted small">
                Berikut rangkuman informasi dan pengumuman terbaru terkait Tugas Akhir.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="position-relative" style="height: 200px; background-color: #f8f9fa;">
                        <img src="assets/img/ta1.jpg" class="w-100 h-100" style="object-fit: cover;" alt="Gambar Berita">
                    </div>
                    
                    <div class="card-body d-flex flex-column p-4">
                        <h5 class="fw-bold mb-3">Alur Pendaftaran Tugas Akhir 2025</h5>

                        <div class="mb-3">
                            <span class="badge bg-light text-secondary border border-light-subtle rounded-pill px-3 py-2 me-1">
                                <i class="bi bi-calendar-event me-1"></i> 22 Des 2025
                            </span>
                            <span class="badge bg-primary rounded-pill px-3 py-2">
                                <i class="bi bi-person me-1"></i> Admin
                            </span>
                        </div>

                        <p class="text-secondary mb-4" style="line-height: 1.6;">
                            Pendaftaran Tugas Akhir periode Genap 2025 telah dibuka. Mahasiswa dapat mengajukan topik melalui sistem SITA UPJ.
                        </p>

                        <div class="mt-auto text-end">
    <a href="dashboard_mhs.php?page=detail_berita&id=1" class="text-primary text-decoration-none fw-bold link-hover">
        Detail Informasi <i class="bi bi-arrow-right ms-1"></i>
    </a>
</div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="position-relative" style="height: 200px; background-color: #f8f9fa;">
                         <img src="assets/img/ta2.jpg" class="w-100 h-100" style="object-fit: cover;" alt="Gambar Berita">
                    </div>

                    <div class="card-body d-flex flex-column p-4">
                        <h5 class="fw-bold mb-3">Tips Sukses Menghadapi Sidang Skripsi</h5>

                        <div class="mb-3">
                            <span class="badge bg-light text-secondary border border-light-subtle rounded-pill px-3 py-2 me-1">
                                <i class="bi bi-calendar-event me-1"></i> 22 Des 2025
                            </span>
                            <span class="badge bg-success rounded-pill px-3 py-2">
                                <i class="bi bi-person-badge me-1"></i> Koordinator TA
                            </span>
                        </div>

                        <p class="text-secondary mb-4" style="line-height: 1.6;">
                            Sidang skripsi merupakan tahap akhir sebelum kelulusan. Persiapan dan pemahaman alur sangat menentukan hasil.
                        </p>

                        <div class="mt-auto text-end">
    <a href="dashboard_mhs.php?page=detail_berita&id=2" class="text-primary text-decoration-none fw-bold link-hover">
        Detail Informasi <i class="bi bi-arrow-right ms-1"></i>
    </a>
</div>
                    </div>
                </div>
            </div>
            
            <div class="col-12">
                 <div class="card border-0 shadow-sm rounded-4 p-3">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-circle me-3 text-primary">
                             <i class="bi bi-info-circle-fill fs-3"></i>
                        </div>
                        <div>
                             <h6 class="fw-bold mb-1">Tahapan Seminar Proposal (Sempro)</h6>
                             <p class="text-muted small mb-0">Wajib dipahami oleh seluruh mahasiswa yang mengambil mata kuliah Tugas Akhir.</p>
                        </div>
                         <div class="ms-auto">
                            <a href="#" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Panduan</a>
                        </div>
                    </div>
                 </div>
            </div>

        </div>
        <?php
        break;
// ... code selanjutnya (case 'pengajuan', dll)
// ... setelah case 'home' ...

    case 'detail_berita':
        $id_berita = isset($_GET['id']) ? $_GET['id'] : 0;
        
        // Cek apakah ID ada di data array kita
        if (array_key_exists($id_berita, $data_berita)) {
            $berita = $data_berita[$id_berita];
            ?>
            
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden animate__animated animate__fadeIn">
                <div class="position-relative" style="height: 300px; background-color: #eee;">
                    <img src="<?= $berita['gambar'] ?>" class="w-100 h-100" style="object-fit: cover; filter: brightness(0.9);">
                    <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);">
                        <h2 class="text-white fw-bold mb-2"><?= $berita['judul'] ?></h2>
                        <div class="text-white-50 small">
                            <i class="bi bi-calendar-event me-2"></i> <?= $berita['tanggal'] ?> &nbsp;|&nbsp; 
                            <i class="bi bi-person me-2"></i> <?= $berita['penulis'] ?>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">
                    <div class="row justify-content-center">
                        <div class="col-lg-10">
                            <article class="lh-lg text-secondary">
                                <?= $berita['isi'] ?>
                            </article>
                            
                            <hr class="my-5">
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="dashboard_mhs.php?page=home" class="btn btn-outline-secondary rounded-pill px-4">
                                    <i class="bi bi-arrow-left me-2"></i> Kembali
                                </a>
                                <button class="btn btn-primary rounded-pill px-4" onclick="window.print()">
                                    <i class="bi bi-printer me-2"></i> Cetak
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
        } else {
            // Jika ID tidak ditemukan
            echo "<div class='alert alert-warning'>Berita tidak ditemukan! <a href='dashboard_mhs.php?page=home'>Kembali</a></div>";
        }
        break;

    // ... lanjutkan ke case 'pengajuan', dll ...
                    // === ROUTING HALAMAN ===
                    case 'pengajuan':     include 'pengajuan.php'; break;
                    case 'bimbingan':     include 'bimbingan.php'; break;
                    
                    // DAFTAR SIDANG (File: mhs_sidang.php)
                    case 'daftar_sidang': include 'mhs_sidang.php'; break; 
                    
                    // JADWAL SIDANG (File: jadwal_sidang_view.php)
                    case 'jadwal':        include 'jadwal_sidang_view.php'; break; 

                    case 'chat':          include 'chat_dosen.php'; break;
                    case 'ai':            include 'ai_assistant.php'; break;
                    case 'extend':        include 'pengajuan.php'; break;
                    case 'bantuan':       include 'panduan.php'; break;
                    
                    default: 
                        echo "<div class='alert alert-danger'>Halaman tidak ditemukan!</div>"; 
                        break;
                }
                ?>
            </div>

            <div class="col-lg-3">
                
                <?php if($page == 'home' || $page == 'pengajuan'): ?>
                    <div class="card border-danger shadow-sm mb-3">
                        <div class="card-header bg-danger text-white fw-bold"><i class="bi bi-alarm"></i> Deadline Penting</div>
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item list-group-item-warning"><strong><i class="bi bi-exclamation-circle"></i> Revisi:</strong> 20 Sept 2025</li>
                            <li class="list-group-item"><strong><i class="bi bi-calendar-event"></i> Sidang 1:</strong> 05 Nov 2025</li>
                            <li class="list-group-item"><strong><i class="bi bi-calendar-event"></i> Sidang 2:</strong> 20 Des 2025</li>
                        </ul>
                    </div>

                    <div class="card mt-3 shadow-sm border-0">
                        <div class="card-header fw-bold bg-light"><i class="bi bi-folder2-open"></i> Dokumen</div>
                        <div class="list-group list-group-flush small">
                            <a href="#" class="list-group-item list-group-item-action">Template Proposal 2025 <i class="bi bi-download float-end"></i></a>
                            <a href="#" class="list-group-item list-group-item-action">Lembar Persetujuan <i class="bi bi-download float-end"></i></a>
                        </div>
                    </div>

                    <div class="card mt-3 shadow-sm border-0">
                        <div class="card-body text-center">
                            <h6 class="fw-bold text-muted">Semester Gasal 2025</h6>
                            <h2 class="display-4 fw-bold text-primary"><?= date('d') ?></h2>
                            <span class="text-uppercase ls-1"><?= date('F Y') ?></span>
                        </div>
                    </div>

                <?php elseif($page == 'ai'): ?>
                    <div class="card border-primary shadow-sm">
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
                     <div class="card border-info shadow-sm">
                        <div class="card-header bg-info text-white fw-bold"><i class="bi bi-info-circle"></i> Tata Tertib</div>
                        <div class="card-body small">Wajib menggunakan jas almamater dan datang 30 menit sebelum jadwal.</div>
                    </div>

                <?php elseif($page == 'extend'): ?>
                     <div class="card border-danger shadow-sm">
                        <div class="card-header bg-danger text-white fw-bold">Syarat</div>
                        <div class="card-body small">Maksimal perpanjangan 6 bulan dengan persetujuan Koordinator.</div>
                    </div>

                <?php else: ?>
                    <div class="card shadow-sm border-0">
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
            
            setTimeout(() => {
                loading.style.display = 'none';
                chatBox.innerHTML += `<div class="msg-container"><div class="msg-ai">Maaf, fitur AI sedang dalam pengembangan.</div></div>`;
                chatBox.scrollTop = chatBox.scrollHeight;
            }, 1000);
        }
    </script>
    <?php endif; ?>

</body>
</html>