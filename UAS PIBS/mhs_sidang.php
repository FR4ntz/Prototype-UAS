<?php
$nim = $_SESSION['nim'];

// --- PERBAIKAN LOGIKA DISINI ---
// Kita cek status 'ACC' ATAU 'Disetujui' agar lebih aman
$q_cek_bim = mysqli_query($conn, "SELECT COUNT(*) as total FROM bimbingan WHERE nim='$nim' AND (status='ACC' OR status='Disetujui')");
$data_bim = mysqli_fetch_assoc($q_cek_bim);
$total_bimbingan = $data_bim['total'];
$syarat_minimal = 8;
$lolos_syarat = ($total_bimbingan >= $syarat_minimal);
// ------------------------------

// Cek Proposal
$q_prop = mysqli_query($conn, "SELECT id_proposal FROM proposal WHERE nim='$nim' AND status='Disetujui'");
$d_prop = mysqli_fetch_assoc($q_prop);
$id_proposal = $d_prop['id_proposal'] ?? null;

$sudah_daftar = false;
$status_saat_ini = '';

if ($id_proposal) {
    $q_cek_sidang = mysqli_query($conn, "SELECT * FROM sidang WHERE id_proposal='$id_proposal'");
    if (mysqli_num_rows($q_cek_sidang) > 0) {
        $sudah_daftar = true;
        $d_sidang = mysqli_fetch_assoc($q_cek_sidang);
        $status_saat_ini = $d_sidang['status_sidang'];
    }
}

if (isset($_POST['daftar_sidang'])) {
    $link_laporan = mysqli_real_escape_string($conn, $_POST['link_laporan']);
    
    // Pastikan ID Proposal Ada
    if(!$id_proposal) {
        echo "<script>alert('Proposal belum disetujui atau tidak ditemukan!');</script>";
    } else {
        $query = "INSERT INTO sidang (id_proposal, file_laporan, status_sidang) VALUES ('$id_proposal', '$link_laporan', 'Menunggu Jadwal')";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Pendaftaran Berhasil!'); window.location='dashboard_mhs.php?page=sidang';</script>";
        } else {
            echo "<script>alert('Error: ".mysqli_error($conn)."');</script>";
        }
    }
}
?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white fw-bold">
        <i class="bi bi-mortarboard-fill me-2"></i> Pendaftaran Sidang Akhir
    </div>
    <div class="card-body">
        
        <div class="alert <?= $lolos_syarat ? 'alert-success' : 'alert-warning' ?> d-flex align-items-center">
            <i class="bi <?= $lolos_syarat ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?> fs-1 me-3"></i>
            <div>
                <h6 class="fw-bold mb-1">Status Syarat Bimbingan</h6>
                <span>Total Disetujui: <strong><?= $total_bimbingan ?></strong> / <?= $syarat_minimal ?> Sesi.</span>
            </div>
        </div>

        <?php if ($sudah_daftar): ?>
            <div class="text-center py-5 bg-light rounded border">
                <i class="bi bi-send-check-fill fs-1 text-success"></i>
                <h4 class="mt-3 fw-bold">Pendaftaran Telah Diterima</h4>
                <p class="text-muted mb-3">Status saat ini: <span class="badge bg-warning text-dark"><?= $status_saat_ini ?></span></p>
                <a href="dashboard_mhs.php?page=jadwal_sidang" class="btn btn-outline-primary">Lihat Detail Jadwal</a>
            </div>

        <?php elseif ($lolos_syarat): ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Link Laporan Akhir (GDrive/PDF)</label>
                    <input type="text" name="link_laporan" class="form-control" placeholder="https://..." required>
                </div>
                <button type="submit" name="daftar_sidang" class="btn btn-primary w-100 fw-bold">
                    <i class="bi bi-send me-2"></i> Daftar Sidang Sekarang
                </button>
            </form>

        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-lock-fill fs-1 text-secondary opacity-25"></i>
                <h5 class="text-muted mt-2">Pendaftaran Terkunci</h5>
                <p class="small text-muted">Anda belum memenuhi jumlah minimal bimbingan.</p>
            </div>
        <?php endif; ?>

    </div>
</div>