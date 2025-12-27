<?php
// Cek akses
if ($_SESSION['role'] != 'Koordinator') { exit("Akses Ditolak."); }

// ==============================================================================
// 1. AMBIL DATA DOSEN (Untuk Dropdown)
// ==============================================================================
$arr_dosen = [];
$q_dosen = mysqli_query($conn, "SELECT * FROM dosen WHERE peran='Dosen'");
// Fallback jika nama kolom beda
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
// 3. LOGIKA UPDATE (ACC/TOLAK)
// ==============================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pastikan tombol verifikasi ditekan atau aksi dikirim
    if (isset($_POST['verifikasi']) || isset($_POST['aksi'])) {
        
        $id = mysqli_real_escape_string($conn, $_POST['id_proposal']);
        $aksi = $_POST['aksi'];
        
        if ($aksi == 'acc') {
            $pembimbing = mysqli_real_escape_string($conn, $_POST['pembimbing']);
            // Validasi: Pembimbing tidak boleh kosong
            if (!empty($pembimbing)) {
                $q = "UPDATE proposal SET status='Disetujui', nidn_pembimbing='$pembimbing' WHERE id_proposal='$id'";
                mysqli_query($conn, $q);
                echo "<script>alert('Berhasil di-ACC!'); window.location='dashboard_dosen.php?page=proposal';</script>";
                exit;
            } else {
                echo "<script>alert('Gagal: Pilih Dosen Pembimbing dulu!'); window.location='dashboard_dosen.php?page=proposal';</script>";
                exit;
            }
        } 
        elseif ($aksi == 'tolak') {
            mysqli_query($conn, "UPDATE proposal SET status='Ditolak' WHERE id_proposal='$id'");
            echo "<script>window.location='dashboard_dosen.php?page=proposal';</script>";
            exit;
        } 
        elseif ($aksi == 'hapus') {
            mysqli_query($conn, "DELETE FROM proposal WHERE id_proposal='$id'");
            echo "<script>window.location='dashboard_dosen.php?page=proposal';</script>";
            exit;
        }
    }
}
?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="m-0 fw-bold text-dark">Data Proposal Masuk</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="bg-light text-secondary text-uppercase small">
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        <th width="15%">NIM</th>
                        <th width="20%">Nama Mahasiswa</th>
                        <th>Judul</th>
                        <th class="text-center" width="15%">Status</th>
                        <th class="text-center" width="10%">Aksi (Verifikasi)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // LOOP 1: TABEL
                    $no = 1;
                    if (!empty($arr_proposal)):
                        foreach ($arr_proposal as $row):
                            $badge = 'bg-secondary';
                            if($row['status']=='Disetujui') $badge = 'bg-success';
                            if($row['status']=='Diajukan') $badge = 'bg-warning text-dark';
                            if($row['status']=='Ditolak') $badge = 'bg-danger';
                            
                            $modalID = "modalAcc" . $row['id_proposal'];
                    ?>
                    <tr>
                        <td class="text-center text-secondary"><?= $no++ ?></td>
                        <td class="text-secondary"><?= $row['nim'] ?></td>
                        <td><?= $row['nama'] ?></td>
                        <td class="small"><?= $row['judul'] ?></td>
                        <td class="text-center">
                            <span class="badge <?= $badge ?> rounded-pill px-3"><?= $row['status'] ?></span>
                        </td>
                        <td class="p-2">
                            <div class="d-flex flex-column gap-1">
                                <button type="button" class="btn btn-success btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#<?= $modalID ?>">
                                    ACC
                                </button>
                                
                                <form method="POST" onsubmit="return confirm('Tolak proposal ini?');">
                                    <input type="hidden" name="id_proposal" value="<?= $row['id_proposal'] ?>">
                                    <input type="hidden" name="aksi" value="tolak">
                                    <button type="submit" class="btn btn-warning btn-sm w-100 fw-bold">Tolak</button>
                                </form>

                                <form method="POST" onsubmit="return confirm('Hapus permanen?');">
                                    <input type="hidden" name="id_proposal" value="<?= $row['id_proposal'] ?>">
                                    <input type="hidden" name="aksi" value="hapus">
                                    <button type="submit" class="btn btn-danger btn-sm w-100 fw-bold">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6" class="text-center py-4">Tidak ada data.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php foreach ($arr_proposal as $row): 
    $modalID = "modalAcc" . $row['id_proposal'];
?>
<div class="modal fade" id="<?= $modalID ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h6 class="modal-title fw-bold">Pilih Pembimbing</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <form method="POST" action="">
                <div class="modal-body text-start">
                    <input type="hidden" name="id_proposal" value="<?= $row['id_proposal'] ?>">
                    <input type="hidden" name="aksi" value="acc">
                    
                    <p class="small mb-2">Mahasiswa: <strong><?= $row['nama'] ?></strong></p>
                    
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Dosen Pembimbing:</label>
                        <select name="pembimbing" class="form-select form-select-sm" required>
                            <option value="">-- Pilih --</option>
                            <?php 
                            foreach($arr_dosen as $d) {
                                $current = isset($row['nidn_pembimbing']) ? $row['nidn_pembimbing'] : '';
                                $selected = ($d['nidn'] == $current) ? 'selected' : '';
                                echo "<option value='{$d['nidn']}' $selected>{$d['nama']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" name="verifikasi" class="btn btn-success w-100 btn-sm">Simpan & ACC</button>
                </div>
            </form>
            
        </div>
    </div>
</div>
<?php endforeach; ?>