<?php
// Cek akses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Koordinator') { 
    echo "<div class='alert alert-danger'>Akses Ditolak.</div>"; 
    exit; 
}

// ==============================================================================
// 1. LOGIKA TAMBAH DATA MAHASISWA
// ==============================================================================
if (isset($_POST['simpan_mhs'])) {
    $nim  = mysqli_real_escape_string($conn, $_POST['nim']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $pass = md5($_POST['password']); 
    $sks  = (int) $_POST['sks'];
    $jsdp = (int) $_POST['jsdp'];
    
    // Cek NIM (Tabel Mahasiswa, Kolom NIM)
    $cek = mysqli_query($conn, "SELECT NIM FROM Mahasiswa WHERE NIM='$nim'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Gagal: NIM sudah terdaftar!');</script>";
    } else {
        // Insert (Kolom: NIM, Nama, Password, Total_SKS, Total_JSDP)
        $query = "INSERT INTO Mahasiswa (NIM, Nama, Password, Total_SKS, Total_JSDP) VALUES ('$nim', '$nama', '$pass', '$sks', '$jsdp')";
        if(mysqli_query($conn, $query)){
            echo "<script>alert('Mahasiswa berhasil ditambahkan!'); window.location='dashboard_dosen.php?page=mahasiswa';</script>";
        } else {
            echo "<script>alert('Error: ".mysqli_error($conn)."');</script>";
        }
    }
}

// ==============================================================================
// 2. LOGIKA HAPUS DATA (FIX RELASI & NAMA TABEL)
// ==============================================================================
if (isset($_POST['hapus_mhs'])) {
    $nim_hapus = mysqli_real_escape_string($conn, $_POST['nim_hapus']);
    
    // TAHAP 1: Hapus Bimbingan (Tabel Bimbingan, Kolom NIM)
    mysqli_query($conn, "DELETE FROM Bimbingan WHERE NIM='$nim_hapus'");

    // TAHAP 2: Hapus Sidang (Cari idProposal dulu di tabel Proposal)
    $q_prop = mysqli_query($conn, "SELECT idProposal FROM Proposal WHERE NIM='$nim_hapus'");
    while($p = mysqli_fetch_assoc($q_prop)){
        $id_p = $p['idProposal'];
        mysqli_query($conn, "DELETE FROM Sidang WHERE idProposal='$id_p'");
    }

    // TAHAP 3: Hapus Proposal
    mysqli_query($conn, "DELETE FROM Proposal WHERE NIM='$nim_hapus'");

    // TAHAP 4: Hapus Notifikasi (Asumsi kolom nim lowercase di tabel Notifikasi)
    mysqli_query($conn, "DELETE FROM Notifikasi WHERE nim='$nim_hapus'");

    // TAHAP 5: Hapus Perpanjangan (Asumsi kolom nim lowercase di tabel Perpanjangan)
    mysqli_query($conn, "DELETE FROM Perpanjangan WHERE nim='$nim_hapus'");

    // TAHAP 6: Hapus Akun Mahasiswa (Induk)
    if(mysqli_query($conn, "DELETE FROM Mahasiswa WHERE NIM='$nim_hapus'")){
        echo "<script>alert('Data mahasiswa dan seluruh riwayatnya BERHASIL dihapus.'); window.location='dashboard_dosen.php?page=mahasiswa';</script>";
    } else {
        echo "<script>alert('Masih Gagal Hapus: ".mysqli_error($conn)."');</script>";
    }
}
?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white fw-bold py-3">
                <i class="bi bi-person-plus-fill me-2"></i> Tambah Mahasiswa Baru
            </div>
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">NIM</label>
                        <input type="text" name="nim" class="form-control" placeholder="Cth: 2021001" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" placeholder="Nama Mahasiswa" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Password Default</label>
                        <input type="text" name="password" class="form-control" value="123456" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Total SKS Awal</label>
                        <input type="number" name="sks" class="form-control" value="0" min="0" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Poin JSDP Awal</label>
                        <input type="number" name="jsdp" class="form-control" value="0" min="0" required>
                    </div>
                    <div class="col-12 mt-4">
                        <button type="submit" name="simpan_mhs" class="btn btn-success fw-bold px-4">
                            <i class="bi bi-save me-2"></i> Simpan Data Mahasiswa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center py-3">
                <span><i class="bi bi-people-fill text-primary me-2"></i> Data Mahasiswa Terdaftar</span>
                <span class="badge bg-secondary">Total: <?= mysqli_num_rows(mysqli_query($conn, "SELECT NIM FROM Mahasiswa")) ?></span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-striped table-hover mb-0 align-middle">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th class="ps-4">NIM</th>
                                <th>Nama Lengkap</th>
                                <th class="text-center">SKS</th>
                                <th class="text-center">JSDP</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $mhs = mysqli_query($conn, "SELECT * FROM Mahasiswa ORDER BY NIM ASC");
                            if(mysqli_num_rows($mhs) > 0):
                                while($row = mysqli_fetch_array($mhs)):
                            ?>
                            <tr>
                                <td class="ps-4 fw-bold"><?= $row['NIM'] ?></td>
                                <td><?= $row['Nama'] ?></td>
                                <td class="text-center">
                                    <span class="badge <?= ($row['Total_SKS']>=120)?'bg-success':'bg-warning text-dark' ?>">
                                        <?= $row['Total_SKS'] ?>
                                    </span>
                                </td>
                                <td class="text-center"><?= $row['Total_JSDP'] ?></td>
                                <td class="text-center">
                                    <form method="POST" onsubmit="return confirm('PERINGATAN: Menghapus mahasiswa ini akan MENGHAPUS SEMUA DATA riwayatnya (Proposal, Bimbingan, Sidang, Perpanjangan). Lanjutkan?');">
                                        <input type="hidden" name="nim_hapus" value="<?= $row['NIM'] ?>">
                                        <button type="submit" name="hapus_mhs" class="btn btn-danger btn-sm py-1 px-2" title="Hapus Permanen">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php 
                                endwhile; 
                            else:
                            ?>
                                <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada data mahasiswa.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>