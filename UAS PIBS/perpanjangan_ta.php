<?php
session_start();
include 'koneksi.php';

// Cek Login Mahasiswa
if ($_SESSION['role'] != 'mahasiswa') { header("Location: index.php"); exit; }

$nim = $_SESSION['nim'];

// Cek apakah punya proposal yang sedang berjalan (Disetujui)
$prop = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM proposal WHERE nim='$nim' AND status='Disetujui'"));

// PROSES PENGAJUAN
if (isset($_POST['ajukan_extend'])) {
    $id_prop = $prop['id_proposal'];
    $alasan  = $_POST['alasan'];
    $tgl     = date('Y-m-d');
    
    // Cek apakah sedang ada pengajuan pending (biar ga spam)
    $cek = mysqli_query($conn, "SELECT * FROM perpanjangan WHERE id_proposal='$id_prop' AND status_perpanjangan='Diajukan'");
    
    if (mysqli_num_rows($cek) == 0) {
        $query = "INSERT INTO perpanjangan (id_proposal, nim, alasan, tanggal_pengajuan) 
                  VALUES ('$id_prop', '$nim', '$alasan', '$tgl')";
        if(mysqli_query($conn, $query)){
            echo "<script>alert('Pengajuan Perpanjangan Berhasil Dikirim!'); window.location='perpanjangan_ta.php';</script>";
        }
    } else {
        echo "<script>alert('Anda masih memiliki pengajuan perpanjangan yang belum diverifikasi!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Perpanjangan Masa Studi TA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container">
        <span class="navbar-brand">Formulir Perpanjangan TA (Extend)</span>
        <a href="dashboard_mhs.php" class="btn btn-outline-light btn-sm">Kembali ke Dashboard</a>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold">Ajukan Perpanjangan (6 Bulan)</div>
                <div class="card-body">
                    <?php if ($prop): ?>
                        <div class="alert alert-info small">
                            <strong>Judul TA Saat Ini:</strong><br>
                            <?= $prop['judul'] ?>
                        </div>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Alasan Perpanjangan</label>
                                <textarea name="alasan" class="form-control" rows="5" required placeholder="Jelaskan mengapa Anda membutuhkan tambahan waktu (misal: Kendala data, sakit, alat rusak, dll)..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Durasi</label>
                                <input type="text" class="form-control" value="6 Bulan (Sesuai Aturan Akademik)" readonly>
                            </div>
                            <button type="submit" name="ajukan_extend" class="btn btn-warning w-100 fw-bold">Kirim Pengajuan</button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-danger">
                            Anda belum memiliki proposal yang disetujui. Tidak dapat mengajukan perpanjangan.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">Riwayat Pengajuan Perpanjangan</div>
                <div class="card-body">
                    <table class="table table-striped small">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Alasan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $hist = mysqli_query($conn, "SELECT * FROM perpanjangan WHERE nim='$nim' ORDER BY tanggal_pengajuan DESC");
                            while($h = mysqli_fetch_array($hist)):
                            ?>
                            <tr>
                                <td><?= $h['tanggal_pengajuan'] ?></td>
                                <td><?= $h['alasan'] ?></td>
                                <td>
                                    <?php 
                                        $bg = ($h['status_perpanjangan']=='Disetujui') ? 'success' : (($h['status_perpanjangan']=='Ditolak')?'danger':'secondary');
                                        echo "<span class='badge bg-$bg'>{$h['status_perpanjangan']}</span>";
                                    ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if(mysqli_num_rows($hist) == 0) echo "<tr><td colspan='3' class='text-center'>Belum ada riwayat.</td></tr>"; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>