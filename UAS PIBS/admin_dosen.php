<?php
// Cek akses
if ($_SESSION['role'] != 'Koordinator') { exit("Akses Ditolak."); }

// ==============================================================================
// 1. LOGIKA TAMBAH DOSEN
// ==============================================================================
if (isset($_POST['simpan_dosen'])) {
    $nidn  = mysqli_real_escape_string($conn, $_POST['nidn']);
    $nama  = mysqli_real_escape_string($conn, $_POST['nama']);
    $peran = mysqli_real_escape_string($conn, $_POST['peran']);
    $pass  = md5($_POST['password']); // Enkripsi MD5
    
    // Cek NIDN Kembar
    $cek = mysqli_query($conn, "SELECT nidn FROM dosen WHERE nidn='$nidn'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Gagal: NIDN sudah terdaftar!');</script>";
    } else {
        // Coba insert pakai kolom 'peran'
        $q_insert = "INSERT INTO dosen (nidn, nama, password, peran) VALUES ('$nidn', '$nama', '$pass', '$peran')";
        
        // Jika gagal (mungkin nama kolomnya 'role'), coba insert pakai 'role'
        if (!@mysqli_query($conn, $q_insert)) {
             $q_insert_alt = "INSERT INTO dosen (nidn, nama, password, role) VALUES ('$nidn', '$nama', '$pass', '$peran')";
             mysqli_query($conn, $q_insert_alt);
        }
        
        echo "<script>alert('Berhasil menambah dosen!'); window.location='dashboard_dosen.php?page=master_dosen';</script>";
    }
}

// ==============================================================================
// 2. LOGIKA HAPUS DOSEN
// ==============================================================================
if (isset($_POST['hapus_dosen'])) {
    $nidn = mysqli_real_escape_string($conn, $_POST['nidn_hapus']);
    
    // Cegah hapus akun sendiri
    if ($nidn == $_SESSION['username']) {
        echo "<script>alert('Tidak bisa menghapus akun sendiri!');</script>";
    } else {
        mysqli_query($conn, "DELETE FROM dosen WHERE nidn='$nidn'");
        echo "<script>alert('Data dosen dihapus.'); window.location='dashboard_dosen.php?page=master_dosen';</script>";
    }
}
?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-success text-white fw-bold py-3">
        <i class="bi bi-person-plus-fill me-2"></i> Tambah Akun Dosen / Koordinator
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="small fw-bold mb-1">NIDN</label>
                    <input type="text" name="nidn" class="form-control" placeholder="Cth: 041002" required>
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold mb-1">Nama Lengkap & Gelar</label>
                    <input type="text" name="nama" class="form-control" placeholder="Nama Dosen" required>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold mb-1">Peran</label>
                    <select name="peran" class="form-select" required>
                        <option value="Dosen">Dosen Pembimbing</option>
                        <option value="Koordinator">Koordinator TA</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold mb-1">Password</label>
                    <input type="text" name="password" class="form-control" value="123456" readonly>
                </div>
                <div class="col-12 mt-3">
                    <button type="submit" name="simpan_dosen" class="btn btn-success w-100 fw-bold">
                        <i class="bi bi-save me-2"></i> Simpan Data
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white fw-bold py-3 border-bottom">
        <i class="bi bi-people-fill me-2 text-success"></i> Daftar Dosen Terdaftar
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead class="bg-light text-secondary small text-uppercase">
                    <tr>
                        <th class="ps-4">NIDN</th>
                        <th>Nama Lengkap</th>
                        <th class="text-center">Role</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Ambil data dosen (Handle error kolom peran/role)
                    $q_dosen = mysqli_query($conn, "SELECT * FROM dosen ORDER BY nidn ASC");
                    // Fallback jika error
                    if (!$q_dosen) { 
                        $q_dosen = mysqli_query($conn, "SELECT nidn, nama, password, role as peran FROM dosen ORDER BY nidn ASC"); 
                    }

                    if ($q_dosen && mysqli_num_rows($q_dosen) > 0):
                        while($r = mysqli_fetch_array($q_dosen)):
                            // Deteksi nama kolom dinamis
                            $role_user = isset($r['peran']) ? $r['peran'] : (isset($r['role']) ? $r['role'] : '-');
                    ?>
                    <tr>
                        <td class="ps-4 fw-bold text-dark"><?= $r['nidn'] ?></td>
                        <td class="text-secondary"><?= $r['nama'] ?></td>
                        <td class="text-center">
                            <?php if($role_user == 'Koordinator'): ?>
                                <span class="badge bg-warning text-dark rounded-pill px-3">Koordinator</span>
                            <?php else: ?>
                                <span class="badge bg-primary rounded-pill px-3">Dosen</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if($r['nidn'] != $_SESSION['username']): ?>
                                <form method="POST" onsubmit="return confirm('Yakin hapus akun <?= $r['nama'] ?>?');">
                                    <input type="hidden" name="nidn_hapus" value="<?= $r['nidn'] ?>">
                                    <button class="btn btn-danger btn-sm p-1 px-2" title="Hapus">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="badge bg-light text-muted border">Akun Anda</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada data dosen.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>