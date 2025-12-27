<?php
// LOGIKA UPDATE RESPON DOSEN
if (isset($_POST['update_respon'])) {
    $id_bim  = $_POST['id_bimbingan'];
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);
    $status  = $_POST['status'];
    
    $query = "UPDATE bimbingan SET catatan_dosen='$catatan', status='$status' WHERE id_bimbingan='$id_bim'";
    
    if (mysqli_query($conn, $query)) {
        // Redirect kembali ke halaman bimbingan di dalam dashboard
        echo "<script>alert('Respon berhasil disimpan!'); window.location='dashboard_dosen.php?page=bimbingan';</script>";
    }
}
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people-fill me-2"></i> Daftar Logbook Mahasiswa</span>
        <span class="badge bg-light text-primary">
            Pending: <?= mysqli_num_rows(mysqli_query($conn, "SELECT * FROM bimbingan WHERE nidn_pembimbing='$nidn' AND status='Menunggu'")) ?>
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Mahasiswa</th>
                        <th width="25%">Tanggal & Topik</th>
                        <th>Status Saat Ini</th>
                        <th width="35%">Respon Dosen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query: Hanya ambil bimbingan milik dosen yang sedang login ($nidn dari dashboard)
                    // Urutkan: Yang 'Menunggu' paling atas, lalu berdasarkan tanggal terbaru
                    $query = mysqli_query($conn, "SELECT b.*, m.nama as mhs_nama, m.nim 
                                                  FROM bimbingan b 
                                                  JOIN mahasiswa m ON b.nim = m.nim 
                                                  WHERE b.nidn_pembimbing = '$nidn' 
                                                  ORDER BY FIELD(b.status, 'Menunggu') DESC, b.tanggal DESC");
                    
                    if (mysqli_num_rows($query) > 0):
                        while($row = mysqli_fetch_array($query)):
                    ?>
                    <tr>
                        <td class="ps-3">
                            <span class="fw-bold text-dark"><?= $row['mhs_nama'] ?></span><br>
                            <small class="text-muted"><?= $row['nim'] ?></small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center mb-1">
                                <i class="bi bi-calendar-event me-2 text-muted"></i> 
                                <small class="fw-bold"><?= date('d M Y', strtotime($row['tanggal'])) ?></small>
                            </div>
                            <p class="mb-0 small text-secondary fst-italic border-start border-3 ps-2">
                                "<?= $row['topik'] ?>"
                            </p>
                        </td>
                        <td>
                            <?php 
                                $bg = 'secondary';
                                if($row['status']=='Menunggu') $bg = 'warning text-dark';
                                elseif($row['status']=='ACC') $bg = 'success';
                                elseif($row['status']=='Revisi') $bg = 'danger';
                                echo "<span class='badge bg-$bg'>{$row['status']}</span>";
                            ?>
                        </td>
                        <td>
                            <form method="POST" class="d-flex flex-column gap-2">
                                <input type="hidden" name="id_bimbingan" value="<?= $row['id_bimbingan'] ?>">
                                
                                <textarea name="catatan" class="form-control form-control-sm" rows="2" placeholder="Tulis catatan revisi..." required><?= $row['catatan_dosen'] ?></textarea>
                                
                                <div class="d-flex gap-2">
                                    <select name="status" class="form-select form-select-sm" style="width: 120px;">
                                        <option value="Menunggu" <?= $row['status']=='Menunggu'?'selected':'' ?>>Menunggu</option>
                                        <option value="Revisi" <?= $row['status']=='Revisi'?'selected':'' ?>>Revisi</option>
                                        <option value="ACC" <?= $row['status']=='ACC'?'selected':'' ?>>ACC</option>
                                    </select>
                                    <button type="submit" name="update_respon" class="btn btn-primary btn-sm w-100">
                                        <i class="bi bi-send-fill"></i> Simpan
                                    </button>
                                </div>
                            </form>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                Belum ada data bimbingan masuk.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>