<?php
session_start();
include 'koneksi.php';
$nim = $_SESSION['nim'];

// Proses Simpan Data
if (isset($_POST['submit'])) {
    $judul = $_POST['judul'];
    $jenis = $_POST['jenis'];
    $tgl = date('Y-m-d');
    
    // Validasi sederhana: Cek apakah sudah pernah ajukan
    $cek = mysqli_query($conn, "SELECT * FROM proposal WHERE nim='$nim'");
    if(mysqli_num_rows($cek) == 0){
        $query = "INSERT INTO proposal (nim, judul, jenis_ta, status, tanggal_pengajuan) 
                  VALUES ('$nim', '$judul', '$jenis', 'Diajukan', '$tgl')";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Berhasil diajukan!'); window.location='dashboard_mhs.php';</script>";
        }
    } else {
        echo "<script>alert('Anda sudah memiliki proposal aktif!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Pengajuan Proposal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width: 600px;">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">Form Pengajuan Proposal</div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label>Judul Tugas Akhir</label>
                        <textarea name="judul" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Jenis TA</label>
                        <select name="jenis" class="form-select">
                            <option value="Rancang Bangun">Rancang Bangun</option>
                            <option value="Skripsi">Skripsi / Penelitian</option>
                            <option value="Publikasi">Jalur Publikasi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>File Proposal (PDF)</label>
                        <input type="file" name="file" class="form-control">
                        <small class="text-muted">*Upload dinonaktifkan untuk demo ini.</small>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="dashboard_mhs.php" class="btn btn-secondary">Kembali</a>
                        <button type="submit" name="submit" class="btn btn-primary">Kirim Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>