<?php
// AMBIL DATA PROPOSAL EKSISTING
$prop_lama = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM proposal WHERE nim='$nim'"));

// LOGIKA PROSES SIMPAN / UPDATE
if (isset($_POST['submit'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $jenis = $_POST['jenis'];
    $tgl = date('Y-m-d');

    if ($prop_lama) {
        // --- LOGIKA UPDATE (JIKA SUDAH ADA PROPOSAL) ---
        if ($prop_lama['status'] == 'Revisi' || $prop_lama['status'] == 'Ditolak') {
            $query = "UPDATE proposal SET judul='$judul', jenis_ta='$jenis', status='Diajukan', tanggal_pengajuan='$tgl' WHERE nim='$nim'";
            if (mysqli_query($conn, $query)) {
                echo "<script>alert('Revisi proposal berhasil dikirim ulang!'); window.location='dashboard_mhs.php?page=pengajuan';</script>";
            }
        } else {
            echo "<script>alert('Proposal Anda sedang diproses atau sudah disetujui. Tidak bisa mengajukan baru.');</script>";
        }
    } else {
        // --- LOGIKA INSERT (BARU PERTAMA KALI) ---
        $query = "INSERT INTO proposal (nim, judul, jenis_ta, status, tanggal_pengajuan) 
                  VALUES ('$nim', '$judul', '$jenis', 'Diajukan', '$tgl')";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Berhasil diajukan!'); window.location='dashboard_mhs.php?page=pengajuan';</script>";
        }
    }
}
?>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <i class="bi bi-pencil-square"></i> 
        <?= ($prop_lama && $prop_lama['status']=='Revisi') ? 'Form Revisi Proposal' : 'Form Pengajuan Proposal' ?>
    </div>
    <div class="card-body">
        
        <?php if ($prop_lama && $prop_lama['status'] == 'Revisi'): ?>
            <div class="alert alert-warning">
                <strong><i class="bi bi-exclamation-circle"></i> Status Revisi:</strong><br>
                Silakan perbaiki judul atau dokumen sesuai masukan dosen, lalu kirim ulang form ini.
            </div>
        <?php elseif ($prop_lama && $prop_lama['status'] == 'Diajukan'): ?>
            <div class="alert alert-info">Proposal Anda sedang direview. Harap tunggu.</div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label fw-bold">Judul Tugas Akhir</label>
                <textarea name="judul" class="form-control" rows="4" required><?= ($prop_lama)?$prop_lama['judul']:'' ?></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold">Jenis Tugas Akhir</label>
                <select name="jenis" class="form-select">
                    <option value="Rancang Bangun" <?= ($prop_lama && $prop_lama['jenis_ta']=='Rancang Bangun')?'selected':'' ?>>Rancang Bangun</option>
                    <option value="Skripsi" <?= ($prop_lama && $prop_lama['jenis_ta']=='Skripsi')?'selected':'' ?>>Skripsi / Penelitian</option>
                    <option value="Publikasi" <?= ($prop_lama && $prop_lama['jenis_ta']=='Publikasi')?'selected':'' ?>>Jalur Publikasi</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-bold">File Proposal (PDF)</label>
                <input type="file" name="file" class="form-control" accept=".pdf">
                <div class="form-text text-danger small">*Upload dinonaktifkan demo.</div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="dashboard_mhs.php" class="btn btn-outline-secondary">Batal</a>
                
                <?php 
                $disabled = '';
                if ($prop_lama && ($prop_lama['status']=='Diajukan' || $prop_lama['status']=='Disetujui')) {
                    $disabled = 'disabled';
                }
                ?>
                <button type="submit" name="submit" class="btn btn-primary px-4" <?= $disabled ?>>
                    <i class="bi bi-send"></i> Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>