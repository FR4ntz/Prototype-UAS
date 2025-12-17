<?php
session_start();
include 'koneksi.php';

// Jika user sudah login, langsung arahkan ke dashboard sesuai role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'mahasiswa') {
        header("Location: dashboard_mhs.php");
        exit;
    } elseif ($_SESSION['role'] == 'Dosen' || $_SESSION['role'] == 'Koordinator') {
        header("Location: dashboard_dosen.php");
        exit;
    }
}

// LOGIKA LOGIN SAAT TOMBOL DITEKAN
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']); // Menggunakan MD5 sesuai database dummy

    // 1. Cek Tabel MAHASISWA
    $cek_mhs = mysqli_query($conn, "SELECT * FROM mahasiswa WHERE nim='$username' AND password='$password'");
    if (mysqli_num_rows($cek_mhs) > 0) {
        $data = mysqli_fetch_assoc($cek_mhs);
        
        // Set Session Mahasiswa
        $_SESSION['user'] = $data['nama'];
        $_SESSION['nim']  = $data['nim'];
        $_SESSION['role'] = 'mahasiswa';
        
        // Redirect ke Dashboard Mahasiswa
        echo "<script>alert('Login Berhasil! Selamat datang {$data['nama']}'); window.location='dashboard_mhs.php';</script>";
        exit;
    }

    // 2. Cek Tabel DOSEN (Jika bukan mahasiswa, cek dosen)
    $cek_dosen = mysqli_query($conn, "SELECT * FROM dosen WHERE nidn='$username' AND password='$password'");
    if (mysqli_num_rows($cek_dosen) > 0) {
        $data = mysqli_fetch_assoc($cek_dosen);
        
        // Set Session Dosen
        $_SESSION['user']     = $data['nama'];
        $_SESSION['username'] = $data['nidn']; // Simpan NIDN
        $_SESSION['role']     = $data['role']; // 'Dosen' atau 'Koordinator'
        
        // Redirect ke Dashboard Dosen
        echo "<script>alert('Login Berhasil! Selamat datang Bpk/Ibu {$data['nama']}'); window.location='dashboard_dosen.php';</script>";
        exit;
    }

    // 3. Jika Gagal
    $error = "NIM/NIDN atau Password Anda salah!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SITA UPJ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #2980b9, #8e44ad);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 25px rgba(0,0,0,0.2);
        }
        .card-header {
            background-color: #fff;
            border-bottom: none;
            padding-top: 30px;
            text-align: center;
        }
        .btn-login {
            background: #2980b9;
            border: none;
        }
        .btn-login:hover {
            background: #3498db;
        }
    </style>
</head>
<body>

    <div class="card login-card">
        <div class="card-header">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/ac/No_image_available.svg/100px-No_image_available.svg.png" width="80" alt="Logo" class="mb-3">
            <h4 class="fw-bold text-dark">SITA - UPJ</h4>
            <p class="text-muted small">Sistem Informasi Tugas Akhir</p>
        </div>
        <div class="card-body p-4">
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger text-center p-2 small">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold small">NIM / NIDN</label>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan ID Pengguna" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required>
                </div>
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" name="login" class="btn btn-primary btn-login py-2">MASUK SISTEM</button>
                </div>
            </form>
        </div>
        <div class="card-footer text-center bg-light py-3">
            <small class="text-muted">&copy; 2025 Universitas Pembangunan Jaya</small>
        </div>
    </div>

</body>
</html>