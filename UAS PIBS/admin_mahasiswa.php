<?php
session_start();
include 'koneksi.php';

// Cek akses Koordinator
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Koordinator') { 
    header("Location: index.php"); exit; 
}

// 1. LOGIKA TAMBAH DATA
if (isset($_POST['simpan'])) {
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $pass = md5($_POST['password']);
    $sks = $_POST['sks'];
    $jsdp = $_POST['jsdp'];
    
    $query = "INSERT INTO mahasiswa (nim, nama, password, total_sks, jsdp_poin) VALUES ('$nim', '$nama', '$pass', '$sks', '$jsdp')";
    if(mysqli_query($conn, $query)) echo "<script>alert('Data Berhasil Disimpan');</script>";
}

// 2. LOGIKA HAPUS DATA
if (isset($_GET['hapus'])) {
    $nim = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM mahasiswa WHERE nim='$nim'");
    echo "<script>alert('Data Dihapus'); window.location='admin_mahasiswa.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Data Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <span class="navbar-brand">Admin Panel - Master Data</span>
        <a href="dashboard_dosen.php" class="btn btn-secondary btn-sm">Kembali ke Dashboard</a>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">Form Mahasiswa</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-2">
                            <label>NIM</label>
                            <input type="text" name="nim" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-2">
                                <label>Total SKS</label>
                                <input type="number" name="sks" class="form-control" value="0">
                            </div>
                            <div class="col-6 mb-2">
                                <label>Poin JSDP</label>
                                <input type="number" name="jsdp" class="form-control" value="0">
                            </div>
                        </div>
                        <button type="submit" name="simpan" class="btn btn-success w-100 mt-2">Simpan Data</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">Data Mahasiswa Terdaftar</div>
                <div class="card-body">
                    <table class="table table-bordered table-striped small">
                        <thead>
                            <tr>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>SKS</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $mhs = mysqli_query($conn, "SELECT * FROM mahasiswa ORDER BY nim ASC");
                            while($row = mysqli_fetch_array($mhs)):
                            ?>
                            <tr>
                                <td><?= $row['nim'] ?></td>
                                <td><?= $row['nama'] ?></td>
                                <td><?= $row['total_sks'] ?></td>
                                <td>
                                    <a href="?hapus=<?= $row['nim'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus mahasiswa ini?')">Hapus</a>
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