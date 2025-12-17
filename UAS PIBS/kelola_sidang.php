<?php
session_start();
include 'koneksi.php';

// Validasi Akses
if ($_SESSION['role'] != 'Koordinator') { 
    header("Location: dashboard_dosen.php"); exit; 
}

// PROSES TAMBAH JADWAL
if (isset($_POST['set_jadwal'])) {
    $id_prop = $_POST['id_proposal'];
    $penguji = $_POST['penguji'];
    $tgl     = $_POST['tgl_sidang'];
    $ruang   = $_POST['ruangan'];
    
    $query = "INSERT INTO sidang (id_proposal, nidn_penguji, tanggal_sidang, ruangan, status_lulus) 
              VALUES ('$id_prop', '$penguji', '$tgl', '$ruang', 0)";
    if(mysqli_query($conn, $query)){
        echo "<script>alert('Jadwal Sidang Terbuat!'); window.location='kelola_sidang.php';</script>";
    }
}

// PROSES INPUT NILAI (BARU)
if (isset($_POST['simpan_nilai'])) {
    $id_sidang = $_POST['id_sidang'];
    $nilai = $_POST['nilai_akhir'];
    $lulus = ($nilai >= 60) ? 1 : 0; // Otomatis lulus jika nilai >= 60
    
    $query = "UPDATE sidang SET nilai_akhir='$nilai', status_lulus='$lulus' WHERE id_sidang='$id_sidang'";
    mysqli_query($conn, $query);
    echo "<script>alert('Nilai Berhasil Disimpan!'); window.location='kelola_sidang.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Sidang & Nilai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-success mb-4">
    <div class="container">
        <a class="navbar-brand" href="dashboard_dosen.php">Panel Koordinator - Sidang</a>
        <a href="dashboard_dosen.php" class="btn btn-outline-light btn-sm">Kembali</a>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">1. Buat Jadwal Baru</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-2">
                            <label>Pilih Mahasiswa</label>
                            <select name="id_proposal" class="form-select" required>
                                <option value="">-- Proposal Disetujui --</option>
                                <?php
                                $props = mysqli_query($conn, "SELECT p.id_proposal, m.nama FROM proposal p JOIN mahasiswa m ON p.nim = m.nim WHERE p.status='Disetujui'");
                                while($p = mysqli_fetch_array($props)){
                                    echo "<option value='{$p['id_proposal']}'>{$p['nama']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label>Penguji</label>
                            <select name="penguji" class="form-select">
                                <?php
                                $dosen = mysqli_query($conn, "SELECT * FROM dosen");
                                while($d = mysqli_fetch_array($dosen)){
                                    echo "<option value='{$d['nidn']}'>{$d['nama']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label>Waktu</label>
                            <input type="datetime-local" name="tgl_sidang" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Ruang</label>
                            <input type="text" name="ruangan" class="form-control" required>
                        </div>
                        <button type="submit" name="set_jadwal" class="btn btn-success w-100">Simpan Jadwal</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">2. Daftar Sidang & Input Nilai</div>
                <div class="card-body">
                    <table class="table table-bordered small align-middle">
                        <thead>
                            <tr>
                                <th>Mahasiswa</th>
                                <th>Jadwal</th>
                                <th>Nilai</th>
                                <th>Input Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sidang = mysqli_query($conn, "SELECT s.*, m.nama as mhs, d.nama as penguji FROM sidang s 
                                                           JOIN proposal p ON s.id_proposal = p.id_proposal 
                                                           JOIN mahasiswa m ON p.nim = m.nim
                                                           JOIN dosen d ON s.nidn_penguji = d.nidn");
                            while($row = mysqli_fetch_array($sidang)):
                            ?>
                            <tr>
                                <td>
                                    <strong><?= $row['mhs'] ?></strong><br>
                                    <span class="text-muted">Penguji: <?= $row['penguji'] ?></span>
                                </td>
                                <td>
                                    <?= date('d M Y, H:i', strtotime($row['tanggal_sidang'])) ?><br>
                                    Ruang: <?= $row['ruangan'] ?>
                                </td>
                                <td class="text-center">
                                    <?php if($row['nilai_akhir'] > 0): ?>
                                        <h5 class="m-0"><?= $row['nilai_akhir'] ?></h5>
                                        <span class="badge bg-<?= ($row['status_lulus'])?'success':'danger' ?>">
                                            <?= ($row['status_lulus'])?'LULUS':'TIDAK' ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Belum Dinilai</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST" class="input-group input-group-sm">
                                        <input type="hidden" name="id_sidang" value="<?= $row['id_sidang'] ?>">
                                        <input type="number" name="nilai_akhir" class="form-control" placeholder="0-100" min="0" max="100" required>
                                        <button type="submit" name="simpan_nilai" class="btn btn-primary">Simpan</button>
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

</body>
</html>