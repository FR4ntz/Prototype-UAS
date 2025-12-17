<?php
session_start();
include 'koneksi.php';
$me = $_SESSION['nim'] ?? $_SESSION['username']; // NIM atau NIDN

// Ambil lawan chat (Kalau Mhs -> Dosen, Kalau Dosen -> Pilih Mhs)
// Untuk demo ini kita set statis lawan bicaranya
$lawan = ($_SESSION['role'] == 'mahasiswa') ? 'DOSEN001' : '2024081012'; 

if(isset($_POST['kirim'])){
    $isi = $_POST['isi'];
    mysqli_query($conn, "INSERT INTO pesan (pengirim, penerima, isi_pesan) VALUES ('$me', '$lawan', '$isi')");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Chat Pembimbing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4" style="max-width: 500px;">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            Chat dengan Pembimbing/Mahasiswa
            <a href="dashboard_<?= ($_SESSION['role']=='mahasiswa')?'mhs':'dosen' ?>.php" class="btn btn-sm btn-light float-end">Kembali</a>
        </div>
        <div class="card-body" style="height: 350px; overflow-y: scroll;">
            <?php
            $chat = mysqli_query($conn, "SELECT * FROM pesan WHERE (pengirim='$me' AND penerima='$lawan') OR (pengirim='$lawan' AND penerima='$me') ORDER BY waktu ASC");
            while($c = mysqli_fetch_array($chat)){
                $align = ($c['pengirim'] == $me) ? 'text-end' : 'text-start';
                $bg = ($c['pengirim'] == $me) ? 'bg-primary text-white' : 'bg-light border';
                echo "<div class='$align mb-2'><span class='d-inline-block p-2 rounded $bg'>{$c['isi_pesan']}</span><br><small class='text-muted' style='font-size:10px'>{$c['waktu']}</small></div>";
            }
            ?>
        </div>
        <div class="card-footer">
            <form method="POST" class="d-flex">
                <input type="text" name="isi" class="form-control me-2" placeholder="Tulis pesan..." required>
                <button type="submit" name="kirim" class="btn btn-success">Kirim</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>