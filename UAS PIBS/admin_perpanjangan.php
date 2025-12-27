<?php
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

<div class="card shadow-sm">
    <div class="card-header bg-danger text-white fw-bold d-flex justify-content-between align-items-center">
        <span><i class="bi bi-hourglass-split me-2"></i> Validasi Perpanjangan TA</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Tgl Pengajuan</th>
                        <th>Mahasiswa</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Ambil semua pengajuan extend
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
                        <td class="ps-3"><?= date('d/m/Y', strtotime($row['tanggal_pengajuan'])) ?></td>
                        <td>
                            <strong><?= $row['nama'] ?></strong><br>
                            <span class="text-muted small"><?= $row['nim'] ?></span>
                        </td>
                        <td>
                            <p class="text-muted small mb-0 fst-italic">"<?= $row['alasan'] ?>"</p>
                        </td>
                        <td>
                            <?php
                            $bg = 'secondary';
                            if($row['status_perpanjangan']=='Diajukan') $bg='warning text-dark';
                            if($row['status_perpanjangan']=='Disetujui') $bg='success';
                            if($row['status_perpanjangan']=='Ditolak') $bg='danger';
                            echo "<span class='badge bg-$bg'>{$row['status_perpanjangan']}</span>";
                            ?>
                        </td>
                        <td>
                            <?php if($row['status_perpanjangan'] == 'Diajukan'): ?>
                                <form method="POST" class="d-flex gap-1">
                                    <input type="hidden" name="id_perpanjangan" value="<?= $row['id_perpanjangan'] ?>">
                                    <button type="submit" name="aksi_extend" value="Disetujui" class="btn btn-success btn-sm" title="Setujui">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button type="submit" name="aksi_extend" value="Ditolak" class="btn btn-danger btn-sm" title="Tolak">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted small"><i class="bi bi-check-all"></i> Selesai</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada pengajuan perpanjangan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>