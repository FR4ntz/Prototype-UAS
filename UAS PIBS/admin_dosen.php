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
    $pass  = md5($_POST['password']); 
    
    // Cek NIDN Kembar
    $cek = mysqli_query($conn, "SELECT nidn FROM dosen WHERE nidn='$nidn'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Gagal: NIDN sudah terdaftar!');</script>";
    } else {
        // Karena kolom di database Anda bernama 'role', kita pakai query ini:
        $q_insert = "INSERT INTO dosen (nidn, nama, password, role) VALUES ('$nidn', '$nama', '$pass', '$peran')";
        
        if (mysqli_query($conn, $q_insert)) {
            echo "<script>alert('Berhasil menambah akun!'); window.location='dashboard_dosen.php?page=master_dosen';</script>";
        } else {
            echo "<script>alert('Error Database: ".mysqli_error($conn)."');</script>";
        }
    }
}

// ==============================================================================
// 2. LOGIKA HAPUS DOSEN (PERBAIKAN UTAMA DI SINI)
// ==============================================================================
if (isset($_POST['hapus_dosen'])) {
    $nidn = mysqli_real_escape_string($conn, $_POST['nidn_hapus']);
    
    if ($nidn == $_SESSION['username']) {
        echo "<script>alert('Tidak bisa menghapus akun sendiri!');</script>";
    } else {
        $q_del = mysqli_query($conn, "DELETE FROM dosen WHERE nidn='$nidn'");
        if($q_del) {
            echo "<script>alert('Data dosen dihapus.'); window.location='dashboard_dosen.php?page=master_dosen';</script>";
        } else {
            echo "<script>alert('Gagal hapus. Dosen ini mungkin sedang membimbing mahasiswa. Hapus data bimbingannya dulu.');</script>";
        }
    }
}
?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-success text-white fw-bold py-3">
        <i class="bi bi-person-plus-fill me-2"></i> Tambah Akun Dosen / Staff
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
                        <option value="Penguji">Dosen Penguji</option>
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
                    // Ambil data dosen (sesuaikan dengan nama kolom di DB Anda: 'role')
                    $q_dosen = mysqli_query($conn, "SELECT * FROM dosen ORDER BY nidn ASC");

                    if ($q_dosen && mysqli_num_rows($q_dosen) > 0):
                        while($r = mysqli_fetch_array($q_dosen)):
                            // Pastikan membaca kolom 'role'
                            $role_user = $r['role']; 
                    ?>
                    <tr>
                        <td class="ps-4 fw-bold text-dark"><?= $r['nidn'] ?></td>
                        <td class="text-secondary"><?= $r['nama'] ?></td>
                        <td class="text-center">
                            <?php if($role_user == 'Koordinator'): ?>
                                <span class="badge bg-warning text-dark rounded-pill px-3">Koordinator</span>
                            <?php elseif($role_user == 'Penguji'): ?>
                                <span class="badge bg-danger rounded-pill px-3">Penguji</span>
                            <?php else: ?>
                                <span class="badge bg-primary rounded-pill px-3">Pembimbing</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if($r['nidn'] != $_SESSION['username']): ?>
                                <form method="POST" onsubmit="return confirm('Yakin hapus akun <?= $r['nama'] ?>?');">
                                    <input type="hidden" name="nidn_hapus" value="<?= $r['nidn'] ?>">
                                    
                                    <button type="submit" name="hapus_dosen" class="btn btn-danger btn-sm p-1 px-2" title="Hapus">
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