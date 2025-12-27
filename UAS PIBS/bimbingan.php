<?php
// LOGIKA SIMPAN LOGBOOK
if (isset($_POST['tambah_log'])) {
    $nidn   = $_POST['nidn'];
    $topik  = $_POST['topik'];
    $tgl    = date('Y-m-d');
    
    $query = "INSERT INTO bimbingan (nim, nidn_pembimbing, tanggal, topik, status) 
              VALUES ('$nim', '$nidn', '$tgl', '$topik', 'Menunggu')";
    
    if(mysqli_query($conn, $query)){
        // Redirect pakai JS agar tetap di halaman bimbingan
        echo "<script>alert('Logbook berhasil disimpan!'); window.location='dashboard_mhs.php?page=bimbingan';</script>";
    }
}
?>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <i class="bi bi-pencil-fill"></i> Isi Logbook Baru
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="mb-3">
                <label class="fw-bold small mb-1">Pilih Dosen Pembimbing</label>
                <select name="nidn" class="form-select" required>
                    <option value="">-- Pilih Dosen --</option>
                    <?php
                    $dosen = mysqli_query($conn, "SELECT * FROM dosen");
                    while($d = mysqli_fetch_array($dosen)){
                        echo "<option value='{$d['nidn']}'>{$d['nama']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="fw-bold small mb-1">Topik Bimbingan</label>
                <textarea name="topik" class="form-control" rows="3" required placeholder="Misal: Revisi Bab 1 tentang Latar Belakang..."></textarea>
            </div>
            <button type="submit" name="tambah_log" class="btn btn-primary w-100">
                <i class="bi bi-save"></i> Simpan Logbook
            </button>
        </form>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header bg-white fw-bold border-bottom">
        <i class="bi bi-clock-history"></i> Riwayat Bimbingan Anda
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 small align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">No</th>
                        <th>Tanggal</th>
                        <th>Topik</th>
                        <th>Catatan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $log = mysqli_query($conn, "SELECT b.*, d.nama as nama_dosen FROM bimbingan b JOIN dosen d ON b.nidn_pembimbing = d.nidn WHERE b.nim='$nim' ORDER BY b.tanggal DESC");
                    $no = 1;
                    while($row = mysqli_fetch_array($log)):
                    ?>
                    <tr>
                        <td class="ps-3"><?= $no++ ?></td>
                        <td><?= $row['tanggal'] ?></td>
                        <td><?= $row['topik'] ?></td>
                        <td class="text-danger fst-italic"><?= $row['catatan_dosen'] ?? '-' ?></td>
                        <td>
                            <?php if($row['status'] == 'ACC'): ?>
                                <span class="badge bg-success">ACC</span>
                            <?php elseif($row['status'] == 'Revisi'): ?>
                                <span class="badge bg-danger">Revisi</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Menunggu</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php if(mysqli_num_rows($log) == 0): ?>
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-inbox fs-4 d-block mb-2"></i> Belum ada data bimbingan.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>