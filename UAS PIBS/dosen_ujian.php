<?php
// Cek akses: Hanya Penguji (atau Koordinator untuk pantau) yang boleh lihat
if ($_SESSION['role'] != 'Penguji' && $_SESSION['role'] != 'Koordinator') {
    echo "<div class='alert alert-danger'>Akses Ditolak.</div>";
    exit;
}

// ==============================================================================
// LOGIKA SIMPAN NILAI
// ==============================================================================
if (isset($_POST['simpan_nilai'])) {
    $id_sidang = $_POST['id_sidang'];
    $nilai     = $_POST['nilai'];
    $status    = $_POST['status'];
    
    $q_update = "UPDATE sidang SET nilai_akhir='$nilai', status_sidang='$status' WHERE id_sidang='$id_sidang'";
    
    if (mysqli_query($conn, $q_update)) {
        echo "<script>alert('Nilai berhasil disimpan!'); window.location='dashboard_dosen.php?page=ujian';</script>";
    } else {
        echo "<script>alert('Gagal simpan: ".mysqli_error($conn)."');</script>";
    }
}

// ==============================================================================
// AMBIL DATA & SIMPAN KE ARRAY (Supaya bisa di-loop 2 kali: Tabel & Modal)
// ==============================================================================
$data_ujian = [];
$q_ujian = mysqli_query($conn, "
    SELECT s.*, m.nama, m.nim, p.judul, p.file_proposal
    FROM sidang s
    JOIN proposal p ON s.id_proposal = p.id_proposal
    JOIN mahasiswa m ON p.nim = m.nim
    WHERE s.nidn_penguji = '$nidn'
    ORDER BY s.tanggal_sidang ASC
");

if ($q_ujian) {
    while($row = mysqli_fetch_assoc($q_ujian)) {
        $data_ujian[] = $row;
    }
}
?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-danger text-white fw-bold py-3 d-flex justify-content-between align-items-center">
        <span><i class="bi bi-mortarboard-fill me-2"></i> Jadwal & Penilaian Sidang</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary small text-uppercase">
                    <tr>
                        <th class="ps-4">Jadwal Sidang</th>
                        <th>Mahasiswa</th>
                        <th>Judul TA</th>
                        <th class="text-center">Nilai</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($data_ujian) > 0): ?>
                        <?php foreach($data_ujian as $row): 
                            $tgl = $row['tanggal_sidang'] ? date('d M Y, H:i', strtotime($row['tanggal_sidang'])) : 'Belum Dijadwalkan';
                            $modalID = "nilaiModal" . $row['id_sidang'];
                        ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center text-danger fw-bold">
                                    <i class="bi bi-calendar-check me-2"></i> <?= $tgl ?>
                                </div>
                                <div class="small text-muted mt-1">
                                    Ruang: <?= $row['ruangan'] ?? '-' ?>
                                </div>
                            </td>
                            <td>
                                <strong><?= $row['nama'] ?></strong><br>
                                <span class="text-muted small"><?= $row['nim'] ?></span>
                            </td>
                            <td>
                                <p class="mb-1 text-dark small fw-bold" style="max-width: 250px;"><?= $row['judul'] ?></p>
                                <?php if(!empty($row['file_proposal'])): ?>
                                    <a href="uploads/proposal/<?= $row['file_proposal'] ?>" target="_blank" class="badge bg-light text-primary border text-decoration-none">
                                        <i class="bi bi-file-pdf"></i> Laporan TA
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td class="text-center fw-bold fs-5">
                                <?= $row['nilai_akhir'] ?? '-' ?>
                            </td>
                            <td class="text-center">
                                <?php
                                $st = $row['status_sidang'];
                                $bg = 'secondary';
                                if($st == 'Dijadwalkan') $bg = 'warning text-dark';
                                if($st == 'Lulus') $bg = 'success';
                                if($st == 'Tidak Lulus') $bg = 'danger';
                                if($st == 'Revisi') $bg = 'info text-dark'; // Warna untuk Revisi
                                
                                echo "<span class='badge bg-$bg rounded-pill'>$st</span>";
                                ?>
                            </td>
                            <td class="text-center">
                                <?php if($st != 'Menunggu Jadwal'): ?>
                                    <button type="button" class="btn btn-outline-danger btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#<?= $modalID ?>">
                                        <i class="bi bi-pencil-square me-1"></i> Nilai
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted small">Menunggu Jadwal</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada jadwal sidang untuk Anda.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php foreach($data_ujian as $row): 
    $modalID = "nilaiModal" . $row['id_sidang'];
    $st = $row['status_sidang'];
?>
<div class="modal fade" id="<?= $modalID ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h6 class="modal-title fw-bold">Input Nilai Sidang</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_sidang" value="<?= $row['id_sidang'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Mahasiswa</label>
                        <input type="text" class="form-control form-control-sm bg-light" value="<?= $row['nama'] ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nilai Akhir (0-100)</label>
                        <input type="number" name="nilai" class="form-control" min="0" max="100" value="<?= $row['nilai_akhir'] ?>" required placeholder="0">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Keputusan Sidang</label>
                        <select name="status" class="form-select" required>
                            <option value="Dijadwalkan" <?= ($st=='Dijadwalkan')?'selected':'' ?>>Belum Putus (Simpan Draft)</option>
                            <option value="Revisi" <?= ($st=='Revisi')?'selected':'' ?>>REVISI (Perbaikan)</option>
                            <option value="Lulus" <?= ($st=='Lulus')?'selected':'' ?>>LULUS (Tanpa Revisi)</option>
                            <option value="Tidak Lulus" <?= ($st=='Tidak Lulus')?'selected':'' ?>>TIDAK LULUS / Mengulang</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="simpan_nilai" class="btn btn-danger btn-sm fw-bold w-100">Simpan Keputusan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>