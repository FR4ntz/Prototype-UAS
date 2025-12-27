<?php
// Pastikan sesi ada
if (!isset($_SESSION['nim'])) { exit; }
$nim = $_SESSION['nim'];

// Ambil Data Jadwal Sidang Mahasiswa yang Login
// Menggunakan LEFT JOIN ke dosen agar jika penguji belum diset, tidak error
$query = "SELECT s.*, p.judul, d.nama AS penguji 
          FROM sidang s 
          JOIN proposal p ON s.id_proposal = p.id_proposal 
          LEFT JOIN dosen d ON s.nidn_penguji = d.nidn 
          WHERE p.nim = '$nim'";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white fw-bold">
        <i class="bi bi-calendar-check me-2"></i> Jadwal & Hasil Sidang Akhir
    </div>
    <div class="card-body">
        
        <?php if($data): ?>
            <div class="alert alert-info d-flex align-items-center mb-4">
                <i class="bi bi-info-circle-fill fs-3 me-3"></i>
                <div>
                    <strong>Status Sidang:</strong> 
                    <span class="badge bg-warning text-dark ms-2"><?= $data['status_sidang'] ?></span>
                </div>
            </div>

            <table class="table table-bordered align-middle">
                <tr>
                    <th width="30%" class="bg-light">Judul Proposal</th>
                    <td><strong><?= $data['judul'] ?></strong></td>
                </tr>
                <tr>
                    <th class="bg-light">Dosen Penguji</th>
                    <td>
                        <?php if($data['penguji']): ?>
                            <?= $data['penguji'] ?>
                        <?php else: ?>
                            <span class="text-muted fst-italic">Sedang ditentukan Koordinator...</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th class="bg-light">Waktu Pelaksanaan</th>
                    <td>
                        <?php if($data['tanggal_sidang']): ?>
                            <span class="text-primary fw-bold">
                                <?= date('d F Y', strtotime($data['tanggal_sidang'])) ?>
                            </span><br>
                            Pukul <?= date('H:i', strtotime($data['tanggal_sidang'])) ?> WIB
                        <?php else: ?>
                            <span class="text-muted fst-italic">Menunggu Jadwal</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th class="bg-light">Ruangan</th>
                    <td>
                        <?php if($data['ruangan']): ?>
                            <span class="badge bg-warning text-dark fs-6"><?= $data['ruangan'] ?></span>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th class="bg-light">Hasil Sidang</th>
                    <td>
                        <?php if($data['nilai_akhir'] !== null): ?>
                            <div class="d-flex align-items-center gap-3">
                                <h1 class="m-0 fw-bold display-5"><?= $data['nilai_akhir'] ?></h1>
                                
                                <?php 
                                // --- LOGIKA PERBAIKAN NILAI (SUPAYA 100 JADI LULUS) ---
                                $nilai = (int) $data['nilai_akhir']; // Ubah ke Integer
                                
                                if($nilai >= 70) {
                                    echo '<span class="badge bg-success rounded-pill px-3 py-2">LULUS</span>';
                                } else {
                                    echo '<span class="badge bg-danger rounded-pill px-3 py-2">TIDAK LULUS</span>';
                                }
                                ?>
                            </div>
                            <small class="text-muted mt-2 d-block">
                                *Syarat kelulusan: Nilai minimal 70.
                            </small>
                        <?php else: ?>
                            <span class="badge bg-secondary">Nilai Belum Keluar</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>

            <div class="mt-4 p-3 bg-light border rounded small">
                <strong>Catatan:</strong>
                <ul class="mb-0 ps-3">
                    <li>Harap hadir 30 menit sebelum jadwal yang ditentukan.</li>
                    <li>Wajib mengenakan Jas Almamater.</li>
                    <li>Bawa berkas laporan rangkap 3 (jika diminta penguji).</li>
                </ul>
            </div>

        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-clipboard-x fs-1 text-muted opacity-50"></i>
                <h5 class="text-muted mt-3">Belum Mendaftar Sidang</h5>
                <p class="text-muted">Anda belum melakukan pendaftaran sidang.<br>Silakan cek menu <strong>Daftar Sidang Akhir</strong>.</p>
                <a href="dashboard_mhs.php?page=daftar_sidang" class="btn btn-primary fw-bold">
                    <i class="bi bi-pencil-square me-2"></i> Daftar Sekarang
                </a>
            </div>
        <?php endif; ?>

    </div>
</div>