<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white fw-bold">
        <i class="bi bi-calendar-week me-2"></i> Jadwal Sidang Anda
    </div>
    <div class="card-body text-center py-5">
        <?php
        // 1. QUERY DATABASE
        // Mengambil data sidang mahasiswa yang sedang login ($nim diambil dari dashboard_mhs.php)
        $q_jadwal = mysqli_query($conn, "SELECT s.*, d.nama as penguji, p.judul 
                                         FROM sidang s
                                         JOIN proposal p ON s.id_proposal = p.id_proposal
                                         LEFT JOIN dosen d ON s.nidn_penguji = d.nidn
                                         WHERE p.nim = '$nim'");
        
        // 2. CEK APAKAH ADA DATA SIDANG
        if (mysqli_num_rows($q_jadwal) > 0) {
            $row = mysqli_fetch_assoc($q_jadwal);
            
            // --- LOGIKA STATUS (YANG ANDA MINTA) ---
            
            // KONDISI 1: JIKA STATUS REVISI
            if ($row['status_sidang'] == 'Revisi') {
                echo "<div class='alert alert-warning border-warning shadow-sm d-inline-block text-start p-4'>";
                echo "<h4 class='fw-bold text-warning'><i class='bi bi-exclamation-triangle-fill'></i> Status: REVISI</h4>";
                echo "<p>Anda diminta melakukan revisi laporan. Silakan perbaiki dan hubungi dosen penguji.</p>";
                echo "<hr>";
                echo "<p class='mb-0 fw-bold'>Nilai Sementara: <span class='badge bg-warning text-dark fs-6'>" . $row['nilai_akhir'] . "</span></p>";
                echo "</div>";
                
                // Opsional: Disini bisa ditambahkan tombol/link upload ulang jika fitur tersedia
            } 
            
            // KONDISI 2: JIKA MENUNGGU JADWAL DARI KOORDINATOR
            elseif ($row['status_sidang'] == 'Menunggu Jadwal') {
                echo "<div class='py-4'>";
                echo "<i class='bi bi-hourglass-split fs-1 text-muted'></i>";
                echo "<h4 class='text-muted mt-3'>Menunggu Penjadwalan</h4>";
                echo "<p class='text-secondary'>Koordinator belum menetapkan jadwal sidang untuk Anda.<br>Mohon cek secara berkala.</p>";
                echo "</div>";
            } 
            
            // KONDISI 3: JIKA SUDAH LULUS
            elseif ($row['status_sidang'] == 'Lulus') {
                echo "<div class='alert alert-success d-inline-block px-5 py-4 shadow-sm'>";
                echo "<h1 class='display-1 mb-3'><i class='bi bi-trophy-fill text-success'></i></h1>";
                echo "<h3 class='fw-bold'>SELAMAT! ANDA LULUS</h3>";
                echo "<p class='lead mb-0'>Nilai Akhir: <strong class='fs-3'>{$row['nilai_akhir']}</strong></p>";
                echo "</div>";
            }
            
            // KONDISI 4: JIKA TIDAK LULUS
            elseif ($row['status_sidang'] == 'Tidak Lulus') {
                echo "<div class='alert alert-danger d-inline-block px-5 py-4 shadow-sm'>";
                echo "<h1 class='display-1 mb-3'><i class='bi bi-x-circle-fill text-danger'></i></h1>";
                echo "<h3 class='fw-bold'>TIDAK LULUS</h3>";
                echo "<p>Silakan hubungi Dosen Pembimbing untuk arahan selanjutnya.</p>";
                echo "<p class='lead mb-0'>Nilai Akhir: <strong>{$row['nilai_akhir']}</strong></p>";
                echo "</div>";
            }

            // KONDISI 5: JIKA DIJADWALKAN (NORMAL / AKAN SIDANG)
            else {
                echo "<div class='border rounded p-4 d-inline-block shadow-sm bg-light'>";
                echo "<h3 class='text-primary fw-bold mb-3'>Sidang Dijadwalkan!</h3>";
                echo "<h5 class='mb-3'><i class='bi bi-calendar-event'></i> " . date('d F Y', strtotime($row['tanggal_sidang'])) . "</h5>";
                echo "<h2 class='display-6 fw-bold mb-3'>" . date('H:i', strtotime($row['tanggal_sidang'])) . " WIB</h2>";
                
                echo "<div class='d-flex justify-content-center gap-3 mt-4'>";
                echo "<div class='text-start border-end pe-3'>";
                echo "<small class='text-muted d-block'>Ruangan</small>";
                echo "<strong>" . ($row['ruangan'] ?? 'Online') . "</strong>";
                echo "</div>";
                echo "<div class='text-start'>";
                echo "<small class='text-muted d-block'>Dosen Penguji</small>";
                echo "<strong>" . ($row['penguji'] ?? '-') . "</strong>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }

        } else {
            // 3. JIKA BELUM DAFTAR SAMA SEKALI (DATA TIDAK DITEMUKAN)
            echo "<img src='https://cdn-icons-png.flaticon.com/512/7486/7486744.png' width='100' class='mb-3 opacity-50'>";
            echo "<h5 class='text-muted'>Belum Ada Data Sidang</h5>";
            echo "<p class='small text-secondary'>Silakan ajukan pendaftaran sidang terlebih dahulu pada menu Daftar Sidang.</p>";
            echo "<a href='dashboard_mhs.php?page=daftar_sidang' class='btn btn-primary btn-sm px-4 rounded-pill'>Daftar Sidang Sekarang</a>";
        }
        ?>
    </div>
</div>