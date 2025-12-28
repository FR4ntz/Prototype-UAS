<?php
// ==============================================================================
// 1. LOGIKA UPDATE RESPON DOSEN
// ==============================================================================
if (isset($_POST['update_respon'])) {
    $id_bim  = $_POST['id_bimbingan'];
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);
    $status  = $_POST['status'];
    
    $query = "UPDATE bimbingan SET catatan_dosen='$catatan', status='$status' WHERE id_bimbingan='$id_bim'";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Respon berhasil disimpan!'); window.location='dashboard_dosen.php?page=bimbingan';</script>";
    }
}
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white fw-bold">
        <i class="bi bi-people-fill me-2"></i> Daftar Mahasiswa Bimbingan
    </div>
    <div class="card-body p-0">
        
        <?php
        // 1. AMBIL DATA MAHASISWA (DISTINCT / UNIK)
        // Kita hanya mengambil daftar mahasiswa yang punya bimbingan dengan dosen ini
        $q_mhs = mysqli_query($conn, "SELECT DISTINCT m.nim, m.nama 
                                      FROM bimbingan b 
                                      JOIN mahasiswa m ON b.nim = m.nim 
                                      WHERE b.nidn_pembimbing = '$nidn'
                                      ORDER BY m.nama ASC");

        if(mysqli_num_rows($q_mhs) > 0):
        ?>
            <div class="accordion accordion-flush" id="accordionBimbingan">
                
                <?php 
                $no = 1;
                while($mhs = mysqli_fetch_array($q_mhs)): 
                    $nim_mhs = $mhs['nim'];
                    
                    // Hitung jumlah bimbingan pending per mahasiswa untuk Badge
                    $q_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM bimbingan WHERE nim='$nim_mhs' AND nidn_pembimbing='$nidn' AND status='Menunggu'");
                    $d_count = mysqli_fetch_assoc($q_count);
                    $pending = $d_count['total'];
                ?>
                
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading<?= $nim_mhs ?>">
                        <button class="accordion-button collapsed py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $nim_mhs ?>">
                            <div class="d-flex w-100 justify-content-between align-items-center pe-3">
                                <div>
                                    <span class="fw-bold text-dark"><?= $mhs['nama'] ?></span>
                                    <span class="text-muted small ms-2">(<?= $nim_mhs ?>)</span>
                                </div>
                                <?php if($pending > 0): ?>
                                    <span class="badge bg-danger rounded-pill"><?= $pending ?> Menunggu Respon</span>
                                <?php else: ?>
                                    <span class="badge bg-success rounded-pill opacity-75"><i class="bi bi-check"></i> Aman</span>
                                <?php endif; ?>
                            </div>
                        </button>
                    </h2>
                    
                    <div id="collapse<?= $nim_mhs ?>" class="accordion-collapse collapse" data-bs-parent="#accordionBimbingan">
                        <div class="accordion-body bg-light p-3">
                            
                            <div class="card border-0 shadow-sm">
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0 align-middle small bg-white">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th width="20%">Tanggal</th>
                                                <th>Topik & Bukti</th>
                                                <th width="15%">Status</th>
                                                <th width="35%">Respon Dosen</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // 2. AMBIL DETAIL BIMBINGAN PER MAHASISWA
                                            $q_bim = mysqli_query($conn, "SELECT * FROM bimbingan 
                                                                          WHERE nim='$nim_mhs' AND nidn_pembimbing='$nidn' 
                                                                          ORDER BY tanggal DESC");
                                            while($row = mysqli_fetch_array($q_bim)):
                                            ?>
                                            <tr>
                                                <td>
                                                    <i class="bi bi-calendar-event me-1 text-muted"></i> 
                                                    <?= date('d M Y', strtotime($row['tanggal'])) ?>
                                                </td>
                                                <td>
                                                    <div class="fw-bold mb-1">Topik:</div>
                                                    <p class="mb-2 text-muted fst-italic">"<?= $row['topik'] ?>"</p>
                                                    
                                                    <?php if(!empty($row['bukti_foto'])): ?>
                                                        <a href="uploads/bukti_bimbingan/<?= $row['bukti_foto'] ?>" target="_blank" class="btn btn-sm btn-outline-info py-0 px-2" style="font-size: 0.75rem;">
                                                            <i class="bi bi-image me-1"></i> Lihat Bukti
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php 
                                                        $bg = 'secondary';
                                                        if($row['status']=='Menunggu') $bg = 'warning text-dark';
                                                        elseif($row['status']=='ACC' || $row['status']=='Disetujui') $bg = 'success';
                                                        elseif($row['status']=='Revisi') $bg = 'danger';
                                                        echo "<span class='badge bg-$bg w-100'>{$row['status']}</span>";
                                                    ?>
                                                </td>
                                                <td class="bg-white">
                                                    <form method="POST">
                                                        <input type="hidden" name="id_bimbingan" value="<?= $row['id_bimbingan'] ?>">
                                                        
                                                        <textarea name="catatan" class="form-control form-control-sm mb-2" rows="2" placeholder="Catatan..."><?= $row['catatan_dosen'] ?></textarea>
                                                        
                                                        <div class="input-group input-group-sm">
                                                            <select name="status" class="form-select">
                                                                <option value="Menunggu" <?= $row['status']=='Menunggu'?'selected':'' ?>>Menunggu</option>
                                                                <option value="Revisi" <?= $row['status']=='Revisi'?'selected':'' ?>>Revisi</option>
                                                                <option value="ACC" <?= ($row['status']=='ACC' || $row['status']=='Disetujui')?'selected':'' ?>>ACC</option>
                                                            </select>
                                                            <button type="submit" name="update_respon" class="btn btn-primary">
                                                                <i class="bi bi-send"></i>
                                                            </button>
                                                        </div>
                                                    </form>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            </div>
                    </div>
                </div>
                <?php endwhile; ?>
                
            </div>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-people fs-1 opacity-25"></i>
                <p class="mt-2">Belum ada mahasiswa yang melakukan bimbingan.</p>
            </div>
        <?php endif; ?>
        
    </div>
</div>