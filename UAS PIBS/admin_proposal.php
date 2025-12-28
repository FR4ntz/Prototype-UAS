<?php
// Cek akses
if ($_SESSION['role'] != 'Koordinator') { exit("Akses Ditolak."); }

// ==============================================================================
// 1. AMBIL DATA DOSEN (Untuk Dropdown)
// ==============================================================================
$arr_dosen = [];
$q_dosen = mysqli_query($conn, "SELECT * FROM dosen WHERE peran='Dosen'");
if (!$q_dosen) { $q_dosen = mysqli_query($conn, "SELECT * FROM dosen WHERE role='Dosen'"); } 

if ($q_dosen) {
    while ($d = mysqli_fetch_assoc($q_dosen)) {
        $arr_dosen[] = $d;
    }
}

// ==============================================================================
// 2. AMBIL DATA PROPOSAL
// ==============================================================================
$arr_proposal = [];
$q_prop = mysqli_query($conn, "SELECT p.*, m.nama FROM proposal p JOIN mahasiswa m ON p.nim = m.nim ORDER BY p.tanggal_pengajuan DESC");

if ($q_prop) {
    while ($row = mysqli_fetch_assoc($q_prop)) {
        $arr_proposal[] = $row;
    }
}

// ==============================================================================
// 3. LOGIKA UPDATE (ACC/TOLAK/HAPUS)
// ==============================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['aksi'])) {
        
        $id = mysqli_real_escape_string($conn, $_POST['id_proposal']);
        $aksi = $_POST['aksi'];
        $catatan = isset($_POST['catatan']) ? mysqli_real_escape_string($conn, $_POST['catatan']) : '';
        
        // --- AKSI ACC ---
        if ($aksi == 'acc') {
            $pembimbing = mysqli_real_escape_string($conn, $_POST['pembimbing']);
            
            if (!empty($pembimbing)) {
                $q = "UPDATE proposal SET 
                      status='Disetujui', 
                      nidn_pembimbing='$pembimbing', 
                      catatan_koor='$catatan' 
                      WHERE id_proposal='$id'";
                      
                if(mysqli_query($conn, $q)) {
                    echo "<script>alert('Berhasil di-ACC!'); window.location='dashboard_dosen.php?page=proposal';</script>";
                } else {
                    echo "<script>alert('Error DB: ".mysqli_error($conn)."');</script>";
                }
                exit;
            } else {
                echo "<script>alert('Gagal: Pilih Dosen Pembimbing dulu!'); window.location='dashboard_dosen.php?page=proposal';</script>";
                exit;
            }
        } 
        // --- AKSI TOLAK ---
        elseif ($aksi == 'tolak') {
            $q = "UPDATE proposal SET status='Ditolak', catatan_koor='$catatan' WHERE id_proposal='$id'";
            mysqli_query($conn, $q);
            echo "<script>alert('Proposal Ditolak.'); window.location='dashboard_dosen.php?page=proposal';</script>";
            exit;
        } 
        // --- AKSI HAPUS ---
        elseif ($aksi == 'hapus') {
            mysqli_query($conn, "DELETE FROM proposal WHERE id_proposal='$id'");
            echo "<script>alert('Data dihapus.'); window.location='dashboard_dosen.php?page=proposal';</script>";
            exit;
        }
    }
}
?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="m-0 fw-bold text-dark"><i class="bi bi-table me-2"></i>Data Proposal Masuk</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0 small">
                <thead class="bg-light text-secondary text-uppercase">
                    <tr>
                        <th class="text-center">ID</th>
                        <th>Tgl Masuk</th>
                        <th>Mahasiswa</th>
                        <th>Jenis TA</th>
                        <th>Judul & File</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" width="12%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($arr_proposal)):
                        foreach ($arr_proposal as $row):
                            $badge = 'bg-secondary';
                            if($row['status']=='Disetujui') $badge = 'bg-success';
                            if($row['status']=='Diajukan' || $row['status']=='Menunggu') $badge = 'bg-warning text-dark';
                            if($row['status']=='Ditolak' || $row['status']=='Revisi') $badge = 'bg-danger';
                            
                            $modalAccID = "modalAcc" . $row['id_proposal'];
                            $modalTolakID = "modalTolak" . $row['id_proposal'];
                    ?>
                    <tr>
                        <td class="text-center fw-bold"><?= $row['id_proposal'] ?></td>
                        <td><?= date('d M Y', strtotime($row['tanggal_pengajuan'])) ?></td>
                        <td>
                            <span class="fw-bold"><?= $row['nama'] ?></span><br>
                            <span class="text-muted"><?= $row['nim'] ?></span>
                        </td>
                        <td>
                            <span class="badge bg-info text-dark"><?= $row['jenis_ta'] ?></span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark mb-1"><?= $row['judul'] ?></div>
                            
                            <?php if(!empty($row['file_proposal'])): ?>
                                <a href="uploads/proposal/<?= $row['file_proposal'] ?>" target="_blank" class="btn btn-outline-primary btn-sm py-0 px-2 rounded-pill" style="font-size: 0.75rem;">
                                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Lihat Dokumen
                                </a>
                            <?php else: ?>
                                <span class="text-muted fst-italic" style="font-size: 0.75rem;">(Tidak ada file)</span>
                            <?php endif; ?>

                            <?php if(!empty($row['catatan_koor'])): ?>
                                <div class="mt-2 text-muted fst-italic border-start border-3 border-warning ps-2">
                                    "<?= $row['catatan_koor'] ?>"
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge <?= $badge ?> rounded-pill px-3"><?= $row['status'] ?></span>
                        </td>
                        <td class="p-2">
                            <div class="d-flex flex-column gap-1">
                                <button type="button" class="btn btn-success btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#<?= $modalAccID ?>">
                                    <i class="bi bi-check-lg"></i> Verifikasi
                                </button>
                                
                                <button type="button" class="btn btn-warning btn-sm fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#<?= $modalTolakID ?>">
                                    <i class="bi bi-x-lg"></i> Tolak
                                </button>

                                <form method="POST" onsubmit="return confirm('Hapus permanen proposal ID <?= $row['id_proposal'] ?>?');">
                                    <input type="hidden" name="id_proposal" value="<?= $row['id_proposal'] ?>">
                                    <input type="hidden" name="aksi" value="hapus">
                                    <button type="submit" class="btn btn-danger btn-sm w-100 fw-bold">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data proposal masuk.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php foreach ($arr_proposal as $row): 
    $modalAccID = "modalAcc" . $row['id_proposal'];
    $modalTolakID = "modalTolak" . $row['id_proposal'];
?>

<div class="modal fade" id="<?= $modalAccID ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h6 class="modal-title fw-bold">Verifikasi Proposal (ACC)</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_proposal" value="<?= $row['id_proposal'] ?>">
                    <input type="hidden" name="aksi" value="acc">
                    
                    <div class="mb-3 p-2 bg-light border rounded">
                        <small class="d-block text-muted">Mahasiswa:</small>
                        <strong><?= $row['nama'] ?> (<?= $row['nim'] ?>)</strong>
                        <hr class="my-1">
                        <small class="d-block text-muted">Judul:</small>
                        <span><?= $row['judul'] ?></span>
                        
                        <?php if(!empty($row['file_proposal'])): ?>
                            <div class="mt-2">
                                <a href="uploads/proposal/<?= $row['file_proposal'] ?>" target="_blank" class="text-primary text-decoration-none small">
                                    <i class="bi bi-paperclip"></i> Buka File Proposal
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Pilih Dosen Pembimbing <span class="text-danger">*</span></label>
                        <select name="pembimbing" class="form-select" required>
                            <option value="">-- Pilih Dosen --</option>
                            <?php 
                            foreach($arr_dosen as $d) {
                                $current = isset($row['nidn_pembimbing']) ? $row['nidn_pembimbing'] : '';
                                $selected = ($d['nidn'] == $current) ? 'selected' : '';
                                echo "<option value='{$d['nidn']}' $selected>{$d['nama']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Catatan Koordinator (Opsional)</label>
                        <textarea name="catatan" class="form-control" rows="2" placeholder="Contoh: Judul disetujui dengan revisi minor..."><?= $row['catatan_koor'] ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm fw-bold px-4">Simpan & ACC</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="<?= $modalTolakID ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h6 class="modal-title fw-bold text-dark">Tolak Proposal</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_proposal" value="<?= $row['id_proposal'] ?>">
                    <input type="hidden" name="aksi" value="tolak">
                    
                    <div class="alert alert-warning small">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Anda yakin ingin menolak proposal ini?
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Alasan Penolakan / Catatan <span class="text-danger">*</span></label>
                        <textarea name="catatan" class="form-control" rows="3" required placeholder="Jelaskan kenapa proposal ditolak..."><?= $row['catatan_koor'] ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm fw-bold px-4">Konfirmasi Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php endforeach; ?>