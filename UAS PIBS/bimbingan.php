<?php
session_start();
include 'koneksi.php';
if ($_SESSION['role'] != 'mahasiswa') { header("Location: index.php"); exit; }

$nim = $_SESSION['nim'];

// PROSES TAMBAH LOGBOOK
if (isset($_POST['tambah_log'])) {
    $nidn   = $_POST['nidn'];
    $topik  = $_POST['topik'];
    $tgl    = date('Y-m-d');
    
    $query = "INSERT INTO bimbingan (nim, nidn_pembimbing, tanggal, topik, status) 
              VALUES ('$nim', '$nidn', '$tgl', '$topik', 'Menunggu')";
    
    if(mysqli_query($conn, $query)){
        echo "<script>alert('Logbook berhasil disimpan!'); window.location='bimbingan.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Bimbingan & Logbook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<header class="bg-primary text-white py-3 mb-4">
    <div class="container d-flex justify-content-between">
        <h4 class="mb-0">Logbook Bimbingan</h4>
        <a href="dashboard_mhs.php" class="btn btn-outline-light btn-sm">Kembali ke Dashboard</a>
    </div>
</header>

<div class="container">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold">Isi Logbook Baru</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label>Pilih Dosen Pembimbing</label>
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
                            <label>Topik Bimbingan</label>
                            <textarea name="topik" class="form-control" rows="4" required placeholder="Misal: Revisi Bab 1 tentang Latar Belakang..."></textarea>
                        </div>
                        <button type="submit" name="tambah_log" class="btn btn-primary w-100">Simpan Logbook</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold">Riwayat Bimbingan Anda</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover small">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Topik</th>
                                    <th>Catatan Dosen</th>
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
                                    <td><?= $no++ ?></td>
                                    <td><?= $row['tanggal'] ?></td>
                                    <td><?= $row['topik'] ?></td>
                                    <td class="text-danger"><?= $row['catatan_dosen'] ?? '-' ?></td>
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
                        <?php if(mysqli_num_rows($log) == 0) echo "<p class='text-center text-muted mt-3'>Belum ada data bimbingan.</p>"; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>