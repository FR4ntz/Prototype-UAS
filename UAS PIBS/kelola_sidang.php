<?php
// Cek akses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Koordinator') { exit("Akses Ditolak."); }

// ==============================================================================
// 1. AMBIL DATA DOSEN
// ==============================================================================
$arr_dosen = [];
$q_dosen = @mysqli_query($conn, "SELECT nidn, nama FROM dosen WHERE peran='Dosen'");
if (!$q_dosen) { $q_dosen = mysqli_query($conn, "SELECT nidn, nama FROM dosen WHERE role='Dosen'"); }
if ($q_dosen) {
    while ($d = mysqli_fetch_assoc($q_dosen)) { $arr_dosen[] = $d; }
}

// ==============================================================================
// 2. LOGIKA TETAPKAN JADWAL
// ==============================================================================
if (isset($_POST['tetapkan_jadwal'])) {
    $id_sidang = $_POST['id_sidang'];
    $penguji   = $_POST['penguji'];
    $tanggal   = $_POST['tanggal'] . ' ' . $_POST['jam']; 
    $ruang     = mysqli_real_escape_string($conn, $_POST['ruang']);
    
    $q = "UPDATE sidang SET nidn_penguji='$penguji', tanggal_sidang='$tanggal', ruangan='$ruang', status_sidang='Dijadwalkan' WHERE id_sidang='$id_sidang'";
    if(mysqli_query($conn, $q)){
        echo "<script>alert('Jadwal berhasil ditetapkan!'); window.location='dashboard_dosen.php?page=sidang';</script>";
    }
}

// ==============================================================================
// 3. LOGIKA SIMPAN NILAI
// ==============================================================================
if (isset($_POST['simpan_nilai'])) {
    $id_sidang = $_POST['id_sidang'];
    $nilai     = $_POST['nilai_akhir'];
    if ($nilai !== "") {
        $nilai = (int) $nilai;
        $q = "UPDATE sidang SET nilai_akhir='$nilai', status_sidang='Selesai' WHERE id_sidang='$id_sidang'";
        mysqli_query($conn, $q);
        echo "<script>window.location='dashboard_dosen.php?page=sidang';</script>";
    }
}

// ==============================================================================
// 4. AMBIL DATA SIDANG MENUNGGU (SIMPAN KE ARRAY DULU)
// ==============================================================================
$data_pending = [];
$q_pending = mysqli_query($conn, "
    SELECT s.*, m.nama, m.nim, p.judul 
    FROM sidang s
    JOIN proposal p ON s.id_proposal = p.id_proposal
    JOIN mahasiswa m ON p.nim = m.nim
    WHERE s.status_sidang = 'Menunggu Jadwal'
");
while($row = mysqli_fetch_assoc($q_pending)){
    $data_pending[] = $row;
}
?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-start border-4 border-warning">
            <div class="card-header bg-white fw-bold py-3">
                <i class="bi bi-inbox-fill text-warning me-2"></i> Permintaan Sidang Baru
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary small">
                            <tr>
                                <th class="ps-3">Mahasiswa</th>
                                <th>Judul Laporan</th>
                                <th>File</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($data_pending) > 0): ?>
                                <?php foreach($data_pending as $row): ?>
                                <tr>
                                    <td class="ps-3">
                                        <span class="fw-bold"><?= $row['nama'] ?></span><br>
                                        <small class="text-muted"><?= $row['nim'] ?></small>
                                    </td>
                                    <td><?= substr($row['judul'], 0, 40) ?>...</td>
                                    <td>
                                        <?php if(!empty($row['file_laporan'])): ?>
                                            <a href="<?= $row['file_laporan'] ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                <i class="bi bi-link-45deg"></i> Link
                                            </a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-warning btn-sm fw-bold text-dark" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalJadwal<?= $row['id_sidang'] ?>">
                                            Atur Jadwal
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">Tidak ada permintaan sidang baru.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card shadow-sm border-start border-4 border-success">
            <div class="card-header bg-white fw-bold py-3">
                <i class="bi bi-calendar-check text-success me-2"></i> Jadwal Aktif & Nilai
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">Mahasiswa</th>
                                <th>Jadwal & Penguji</th>
                                <th class="text-center">Nilai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $q_fixed = mysqli_query($conn, "
                                SELECT s.*, m.nama, d.nama as penguji 
                                FROM sidang s 
                                JOIN proposal p ON s.id_proposal = p.id_proposal
                                JOIN mahasiswa m ON p.nim = m.nim
                                LEFT JOIN dosen d ON s.nidn_penguji = d.nidn
                                WHERE s.status_sidang != 'Menunggu Jadwal'
                                ORDER BY s.tanggal_sidang DESC
                            ");
                            while($row = mysqli_fetch_assoc($q_fixed)):
                            ?>
                            <tr>
                                <td class="ps-3 fw-bold"><?= $row['nama'] ?></td>
                                <td>
                                    <?= date('d M Y, H:i', strtotime($row['tanggal_sidang'])) ?><br>
                                    <small class="text-muted">Penguji: <?= $row['penguji'] ?? '-' ?></small>
                                </td>
                                <td class="text-center">
                                    <h5 class="fw-bold m-0 text-success"><?= $row['nilai_akhir'] ?? '-' ?></h5>
                                </td>
                                <td class="p-2">
                                    <form method="POST" class="d-flex gap-1">
                                        <input type="hidden" name="id_sidang" value="<?= $row['id_sidang'] ?>">
                                        <input type="number" name="nilai_akhir" class="form-control form-control-sm" style="width:70px" placeholder="0-100" value="<?= $row['nilai_akhir'] ?>">
                                        <button type="submit" name="simpan_nilai" class="btn btn-success btn-sm"><i class="bi bi-save"></i></button>
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

<?php foreach($data_pending as $row): ?>
<div class="modal fade" id="modalJadwal<?= $row['id_sidang'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h6 class="modal-title fw-bold">Tetapkan Jadwal Sidang</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_sidang" value="<?= $row['id_sidang'] ?>">
                    <div class="mb-3">
                        <label class="small fw-bold">Mahasiswa</label>
                        <input type="text" class="form-control bg-light" value="<?= $row['nama'] ?>" readonly>
                    </div>
                    <div class="mb-2">
                        <label class="small fw-bold">Penguji</label>
                        <select name="penguji" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <?php foreach($arr_dosen as $d): ?>
                                <option value="<?= $d['nidn'] ?>"><?= $d['nama'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">
                            <label class="small fw-bold">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="small fw-bold">Jam</label>
                            <input type="time" name="jam" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Ruangan</label>
                        <input type="text" name="ruang" class="form-control" placeholder="Cth: B202" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tetapkan_jadwal" class="btn btn-primary btn-sm fw-bold px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>