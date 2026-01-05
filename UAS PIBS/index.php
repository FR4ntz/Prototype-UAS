<?php
session_start();
// Hapus include koneksi jika hanya ingin tampilan statis, 
// atau biarkan jika footer/header butuh config.
include 'koneksi.php'; 

// 1. CEK JIKA SUDAH LOGIN (Redirect jika sudah login)
if (isset($_SESSION['role'])) {
    $role = strtolower($_SESSION['role']);
    if ($role == 'mahasiswa') header("Location: dashboard_mhs.php");
    else header("Location: dashboard_dosen.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SITA UPJ - Portal Akademik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    
    <style>
        /* CSS KHUSUS */
        body { background-color: #f5f7fa; }
        
        /* WIDGETS & CARDS */
        .rounded-4 { border-radius: 1rem !important; }
        .shadow-sm { box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important; }
        .card { border: none; }
        
        /* HEADER WARNA BIRU UPJ */
        .header-brand-blue {
            background-color: #003366; /* Warna biru tua, sesuaikan hex code jika perlu */
            color: white;
        }

        /* NAVIGASI KIRI */
        .menu-header { font-weight: 700; color: #333; }
        .nav-link.active { background-color: #0d6efd; color: white !important; font-weight: 600; border-radius: 8px; }
        .nav-link { color: #555; padding: 10px 15px; border-radius: 8px; transition: 0.2s; }
        .nav-link:hover:not(.active) { background-color: #e9ecef; }

        /* UTILS */
        .link-hover { transition: all 0.3s ease; }
        .link-hover:hover { padding-right: 5px; color: #0d6efd !important; }
        .text-white-50 { color: rgba(255, 255, 255, 0.65) !important; }
    </style>
</head>
<body>

    <header class="header-brand-blue py-3 sticky-top shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="bi bi-mortarboard-fill fs-2 me-3 text-white"></i>
                <div style="line-height: 1.2;">
                    <h5 class="m-0 fw-bold text-white">SITA - UPJ</h5>
                    <small class="text-white-50" style="font-size: 0.85rem;">Sistem Informasi Tugas Akhir</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-md-block" style="line-height: 1.2;">
                    <span class="d-block fw-bold text-white" style="font-size: 0.9rem;">Tamu (Guest)</span>
                    <small class="text-white-50" style="font-size: 0.75rem;">Akses Publik</small>
                </div>
                <a href="login.php" class="btn btn-outline-light px-4 rounded-pill fw-bold">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Login
                </a>
            </div>
        </div>
    </header>
    <div class="container my-4">
        <div class="row g-4">
            
            <div class="col-lg-3">
                <div class="card shadow-sm rounded-4 p-3 bg-white">
                    <div class="mb-3 px-2 mt-2">
                        <h6 class="menu-header"><i class="bi bi-grid-fill me-2"></i> Menu Utama</h6>
                    </div>
                    <nav class="nav flex-column gap-1">
                        <a class="nav-link active" href="index.php">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard Publik
                        </a>
                        <a class="nav-link" href="#" onclick="alert('Silakan login untuk fitur ini')">
                            <i class="bi bi-info-circle me-2"></i> Tentang Aplikasi
                        </a>
                        <a class="nav-link" href="https://upj.ac.id" target="_blank">
                            <i class="bi bi-globe me-2"></i> Website UPJ
                        </a>
                    </nav>
                </div>
            </div>

            <div class="col-lg-6">
                
                <div class="mb-4">
                    <h4 class="fw-bold text-dark mb-1">Selamat Datang di Portal SITA UPJ 👋</h4>
                    <p class="text-secondary small">Berikut rangkuman informasi dan pengumuman terbaru terkait Tugas Akhir.</p>
                </div>

                <div class="row g-4">
                    
                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm rounded-4 overflow-hidden bg-white">
                            <div class="position-relative" style="height: 180px; background-color: #f8f9fa;">
                                <img src="assets/img/ta1.jpg" class="w-100 h-100" style="object-fit: cover;" alt="Ilustrasi">
                            </div>
                            <div class="card-body d-flex flex-column p-4">
                                <h6 class="fw-bold mb-3 lh-base">Alur Pendaftaran Tugas Akhir 2025</h6>
                                <div class="mb-3">
                                    <span class="badge bg-light text-secondary border rounded-pill px-3 py-1 me-1">
                                        <i class="bi bi-calendar-event me-1"></i> 22 Des 2025
                                    </span>
                                    <span class="badge bg-primary text-white rounded-pill px-3 py-1">
                                        <i class="bi bi-person me-1"></i> Admin
                                    </span>
                                </div>
                                <p class="text-secondary small mb-4" style="line-height: 1.6;">
                                    Pendaftaran Tugas Akhir periode Genap 2025 telah dibuka. Mahasiswa dapat mengajukan topik melalui sistem SITA UPJ.
                                </p>
                                <div class="mt-auto text-end">
                                    <a href="#" onclick="alert('Silakan Login untuk melihat detail.')" class="text-primary text-decoration-none fw-bold link-hover" style="font-size: 0.9rem;">
                                        Detail Informasi <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm rounded-4 overflow-hidden bg-white">
                            <div class="position-relative" style="height: 180px; background-color: #f8f9fa;">
                                <img src="assets/img/ta2.jpg" class="w-100 h-100" style="object-fit: cover;" alt="Ilustrasi">
                            </div>
                            <div class="card-body d-flex flex-column p-4">
                                <h6 class="fw-bold mb-3 lh-base">Tips Sukses Menghadapi Sidang Skripsi</h6>
                                <div class="mb-3">
                                    <span class="badge bg-light text-secondary border rounded-pill px-3 py-1 me-1">
                                        <i class="bi bi-calendar-event me-1"></i> 22 Des 2025
                                    </span>
                                    <span class="badge bg-success text-white rounded-pill px-3 py-1">
                                        <i class="bi bi-person-badge me-1"></i> Koor TA
                                    </span>
                                </div>
                                <p class="text-secondary small mb-4" style="line-height: 1.6;">
                                    Sidang skripsi merupakan tahap akhir sebelum kelulusan. Persiapan dan pemahaman alur sangat menentukan hasil.
                                </p>
                                <div class="mt-auto text-end">
                                    <a href="#" onclick="alert('Silakan Login untuk melihat detail.')" class="text-primary text-decoration-none fw-bold link-hover" style="font-size: 0.9rem;">
                                        Detail Informasi <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-lg-3">
                <div class="card shadow-sm border-0 rounded-4 mb-3 overflow-hidden">
                    <div class="card-header text-white fw-bold py-3" style="background-color: #dc3545;">
                        <i class="bi bi-alarm me-2"></i> Deadline Penting
                    </div>
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item list-group-item-warning py-3">
                            <strong><i class="bi bi-exclamation-circle me-2"></i> Revisi:</strong> 20 Sept 2025
                        </li>
                        <li class="list-group-item py-3">
                            <strong><i class="bi bi-calendar-event me-2"></i> Sidang 1:</strong> 05 Nov 2025
                        </li>
                        <li class="list-group-item py-3">
                            <strong><i class="bi bi-calendar-event me-2"></i> Sidang 2:</strong> 20 Des 2025
                        </li>
                    </ul>
                </div>

                <div class="card shadow-sm border-0 rounded-4 mb-3">
                    <div class="card-header fw-bold bg-white py-3 border-bottom">
                        <i class="bi bi-folder2-open me-2"></i> Dokumen Publik
                    </div>
                    <div class="list-group list-group-flush small">
                        <a href="#" class="list-group-item list-group-item-action py-3 border-0 border-bottom">
                            Template Proposal 2025 <i class="bi bi-download float-end text-secondary"></i>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action py-3 border-0 border-bottom">
                            Lembar Persetujuan <i class="bi bi-download float-end text-secondary"></i>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action py-3 border-0">
                            Panduan Penulisan <i class="bi bi-download float-end text-secondary"></i>
                        </a>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body text-center py-4">
                        <h6 class="fw-bold text-muted mb-1">Semester Gasal 2025</h6>
                        <h1 class="display-4 fw-bold text-primary mb-0"><?= date('d') ?></h1>
                        <span class="text-uppercase fw-bold text-secondary ls-1"><?= date('F Y') ?></span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <footer class="text-center py-4 bg-white mt-5 border-top">
        <div class="container">
            <h6 class="fw-bold mb-1">Universitas Pembangunan Jaya</h6>
            <small class="d-block text-secondary">Jln. Cendrawasih Raya Blok B7/P, Bintaro Jaya, Tangerang Selatan</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>