<?php
// Cek Data Proposal
$prop_setuju = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM proposal WHERE nim='$nim' AND status='Disetujui'"));

if (isset($_POST['ajukan_extend'])) {
    $id_prop = $prop_setuju['id_proposal'];
    $alasan  = $_POST['alasan'];
    $tgl     = date('Y-m-d');
    
    $cek = mysqli_query($conn, "SELECT * FROM perpanjangan WHERE id_proposal='$id_prop' AND status_perpanjangan='Diajukan'");
    if (mysqli_num_rows($cek) == 0) {
        $query = "INSERT INTO perpanjangan (id_proposal, nim, alasan, tanggal_pengajuan) VALUES ('$id_prop', '$nim', '$alasan', '$tgl')";
        if(mysqli_query($conn, $query)){ 
            echo "<script>alert('Berhasil diajukan!'); window.location='dashboard_mhs.php?page=extend';</script>"; 
        }
    } else {
        echo "<script>alert('Masih ada pengajuan pending!');</script>";
    }
}
?>

<div class="card shadow-sm">
    <div class="card-header bg-danger text-white">
        <i class="bi bi-clock-history"></i> Form Perpanjangan (Extend)
    </div>
    <div class="card-body">
        <?php if ($prop_setuju): ?>
            <div class="alert alert-info small mb-3">
                <strong>Judul TA Saat Ini:</strong><br>
                <?= $prop_setuju['judul'] ?>
            </div>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Alasan Perpanjangan</label>
                    <textarea name="alasan" class="form-control" rows="4" required placeholder="Jelaskan alasan keterlambatan..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Durasi</label>
                    <input type="text" class="form-control" value="6 Bulan (Sesuai Aturan)" readonly>
                </div>
                <button type="submit" name="ajukan_extend" class="btn btn-danger w-100">Kirim Pengajuan</button>
            </form>
        <?php else: ?>
            <div class="alert alert-warning">
                Anda belum memiliki proposal yang disetujui. Tidak dapat mengajukan perpanjangan.
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header fw-bold">Riwayat Pengajuan</div>
    <div class="card-body p-0">
        <table class="table table-striped mb-0 small">
            <thead><tr><th>Tgl</th><th>Alasan</th><th>Status</th></tr></thead>
            <tbody>
                <?php
                $hist = mysqli_query($conn, "SELECT * FROM perpanjangan WHERE nim='$nim' ORDER BY tanggal_pengajuan DESC");
                while($h = mysqli_fetch_array($hist)):
                ?>
                <tr>
                    <td><?= $h['tanggal_pengajuan'] ?></td>
                    <td><?= substr($h['alasan'],0,30) ?>...</td>
                    <td><span class="badge bg-<?= ($h['status_perpanjangan']=='Disetujui')?'success':'warning' ?>"><?= $h['status_perpanjangan'] ?></span></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>