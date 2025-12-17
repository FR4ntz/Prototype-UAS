<?php
session_start();
include 'koneksi.php';
$nim = $_SESSION['nim'];
$mhs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM mahasiswa WHERE nim='$nim'"));
$prop = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM proposal WHERE nim='$nim'"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Laporan Status TA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body onload="window.print()">
    <div class="container mt-4 border p-5">
        <div class="text-center mb-5">
            <h4>UNIVERSITAS PEMBANGUNAN JAYA</h4>
            <h5>LAPORAN STATUS TUGAS AKHIR MAHASISWA</h5>
        </div>
        
        <table class="table table-borderless">
            <tr><td width="200">NIM</td><td>: <?= $mhs['nim'] ?></td></tr>
            <tr><td>Nama Lengkap</td><td>: <?= $mhs['nama'] ?></td></tr>
            <tr><td>Judul TA</td><td>: <?= $prop['judul'] ?? '-' ?></td></tr>
            <tr><td>Status Proposal</td><td>: <?= $prop['status'] ?? 'Belum Mengajukan' ?></td></tr>
        </table>

        <h6 class="mt-4">Rekapitulasi Bimbingan</h6>
        <table class="table table-bordered small">
            <thead><tr class="table-light"><th>No</th><th>Tanggal</th><th>Topik</th><th>Status</th></tr></thead>
            <tbody>
                <?php
                $bim = mysqli_query($conn, "SELECT * FROM bimbingan WHERE nim='$nim'");
                $no=1;
                while($b = mysqli_fetch_array($bim)){
                    echo "<tr><td>{$no}</td><td>{$b['tanggal']}</td><td>{$b['topik']}</td><td>{$b['status']}</td></tr>";
                    $no++;
                }
                ?>
            </tbody>
        </table>
        
        <div class="mt-5 text-end">
            <p>Tangerang Selatan, <?= date('d-m-Y') ?></p>
            <br><br>
            <p>( Koordinator TA )</p>
        </div>
    </div>
</body>
</html>