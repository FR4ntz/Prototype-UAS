<?php
// Cek akses
if ($_SESSION['role'] != 'Koordinator') { exit("Akses Ditolak."); }

// LOGIKA VERIFIKASI EXTEND
if (isset($_POST['aksi_extend'])) {
    $id_ext = $_POST['id_perpanjangan'];
    $status = $_POST['aksi_extend']; // 'Disetujui' atau 'Ditolak'
    
    $query = "UPDATE perpanjangan SET status_perpanjangan='$status' WHERE id_perpanjangan='$id_ext'";
    if(mysqli_query($conn, $query)){
        echo "<script>alert('Status perpanjangan diperbarui!'); window.location='dashboard_dosen.php?page=extend';</script>";
    }
}
?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-danger text-white fw-bold d-flex justify-content-between align-items-center py-3">
        <span><i class="bi bi-hourglass-split me-2"></i> Validasi Perpanjangan TA</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light text-secondary text-uppercase small">
                    <tr>
                        <th class="ps-3">Tgl Masuk</th>
                        <th>Mahasiswa</th>
                        <th class="text-center">Durasi</th>
                        <th>Alasan Pengajuan</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Ambil semua pengajuan extend
                    // Join ke mahasiswa untuk nama & nim
                    $q_ext = mysqli_query($conn, "
                        SELECT ex.*, m.nama, m.nim 
                        FROM perpanjangan ex 
                        JOIN mahasiswa m ON ex.nim = m.nim 
                        ORDER BY FIELD(ex.status_perpanjangan, 'Diajukan') DESC, ex.tanggal_pengajuan ASC
                    ");
                    
                    if(mysqli_num_rows($q_ext) > 0):
                        while($row = mysqli_fetch_assoc($q_ext)):
                    ?>
                    <tr>
                        <td class="ps-3 text-muted small">
                            <?= date('d M Y', strtotime($row['tanggal_pengajuan'])) ?>
                        </td>
                        <td>
                            <strong><?= $row['nama'] ?></strong><br>
                            <span class="text-muted small"><?= $row['nim'] ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info text-dark">
                                <?= $row['lama_perpanjangan'] ?> Bulan
                            </span>
                        </td>
                        <td>
                            <p class="text-muted small mb-0 fst-italic text-break" style="max-width: 250px;">
                                "<?= substr($row['alasan'], 0, 100) ?>..."
                            </p>
                        </td>
                        <td class="text-center">
                            <?php
                            $bg = 'secondary';
                            if($row['status_perpanjangan']=='Diajukan') $bg='warning text-dark';
                            if($row['status_perpanjangan']=='Disetujui') $bg='success';
                            if($row['status_perpanjangan']=='Ditolak') $bg='danger';
                            echo "<span class='badge bg-$bg rounded-pill px-3'>{$row['status_perpanjangan']}</span>";
                            ?>
                        </td>
                        <td class="text-center">
                            <?php if($row['status_perpanjangan'] == 'Diajukan'): ?>
                                <form method="POST" class="d-flex justify-content-center gap-1">
                                    <input type="hidden" name="id_perpanjangan" value="<?= $row['id_perpanjangan'] ?>">
                                    
                                    <button type="submit" name="aksi_extend" value="Disetujui" class="btn btn-success btn-sm fw-bold" onclick="return confirm('Setujui perpanjangan <?= $row['nama'] ?>?');">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    
                                    <button type="submit" name="aksi_extend" value="Ditolak" class="btn btn-danger btn-sm fw-bold" onclick="return confirm('Tolak pengajuan ini?');">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted small">
                                    <i class="bi bi-check2-all"></i> Selesai
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada pengajuan perpanjangan masuk.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>