-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 27 Des 2025 pada 08.48
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_sita`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `bimbingan`
--

CREATE TABLE `bimbingan` (
  `id_bimbingan` int(11) NOT NULL,
  `nim` char(15) DEFAULT NULL,
  `nidn_pembimbing` char(15) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `topik` varchar(255) DEFAULT NULL,
  `catatan_dosen` text DEFAULT NULL,
  `status` enum('Menunggu','ACC','Revisi') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bimbingan`
--

INSERT INTO `bimbingan` (`id_bimbingan`, `nim`, `nidn_pembimbing`, `tanggal`, `topik`, `catatan_dosen`, `status`) VALUES
(4, '202408108', 'DOSEN001', '2025-12-27', 'wdadwa', 'www', 'ACC'),
(5, '202408108', 'DOSEN001', '2025-12-27', 'ssss', 'aww', 'ACC'),
(6, '202408108', 'DOSEN001', '2025-12-27', 'ssss', 'aww', 'ACC'),
(7, '202408108', 'DOSEN001', '2025-12-27', 'sssssssss', 'aww', 'ACC'),
(8, '202408108', 'DOSEN001', '2025-12-27', 'awdasd', 'aww', 'ACC'),
(9, '202408108', 'DOSEN001', '2025-12-27', 'asdwas', 'aww', 'ACC'),
(10, '202408108', 'DOSEN001', '2025-12-27', 'sdawsd', 'aww', 'ACC'),
(11, '202408108', 'DOSEN001', '2025-12-27', 'wasdwa', 'aww', 'ACC');

-- --------------------------------------------------------

--
-- Struktur dari tabel `dosen`
--

CREATE TABLE `dosen` (
  `nidn` char(15) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('Dosen','Koordinator') DEFAULT 'Dosen'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `dosen`
--

INSERT INTO `dosen` (`nidn`, `nama`, `email`, `password`, `role`) VALUES
('DOSEN001', 'Ibu Pembimbing', 'dosen@upj.ac.id', '202cb962ac59075b964b07152d234b70', 'Dosen'),
('KOOR001', 'Bpk. Koordinator', 'koor@upj.ac.id', '202cb962ac59075b964b07152d234b70', 'Koordinator');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `nim` char(15) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `total_sks` int(11) DEFAULT NULL,
  `jsdp_poin` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mahasiswa`
--

INSERT INTO `mahasiswa` (`nim`, `nama`, `email`, `password`, `total_sks`, `jsdp_poin`) VALUES
('202408108', 'Laurensius Jovito', NULL, '202cb962ac59075b964b07152d234b70', 160, 10000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id_notif` int(11) NOT NULL,
  `nim` char(15) DEFAULT NULL,
  `judul` varchar(100) DEFAULT NULL,
  `pesan` text DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `perpanjangan`
--

CREATE TABLE `perpanjangan` (
  `id_perpanjangan` int(11) NOT NULL,
  `id_proposal` int(11) DEFAULT NULL,
  `nim` char(15) DEFAULT NULL,
  `alasan` text DEFAULT NULL,
  `durasi_bulan` int(11) DEFAULT 6,
  `tanggal_pengajuan` date DEFAULT NULL,
  `status_perpanjangan` enum('Diajukan','Disetujui','Ditolak') DEFAULT 'Diajukan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesan`
--

CREATE TABLE `pesan` (
  `id_pesan` int(11) NOT NULL,
  `pengirim` char(15) DEFAULT NULL,
  `penerima` char(15) DEFAULT NULL,
  `isi_pesan` text DEFAULT NULL,
  `waktu` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pesan`
--

INSERT INTO `pesan` (`id_pesan`, `pengirim`, `penerima`, `isi_pesan`, `waktu`, `is_read`) VALUES
(8, '202408108', 'DOSEN001', 'haloo bu', '2025-12-27 05:44:13', 0),
(9, 'DOSEN001', '202408108', 'halo', '2025-12-27 05:46:57', 0),
(10, 'DOSEN001', '202408108', 'halooo', '2025-12-27 05:49:37', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `proposal`
--

CREATE TABLE `proposal` (
  `id_proposal` int(11) NOT NULL,
  `nim` char(15) DEFAULT NULL,
  `judul` text DEFAULT NULL,
  `jenis_ta` enum('Rancang Bangun','Skripsi','Publikasi') DEFAULT NULL,
  `file_proposal` varchar(255) DEFAULT NULL,
  `status` enum('Diajukan','Disetujui','Revisi','Ditolak') DEFAULT 'Diajukan',
  `tanggal_pengajuan` date DEFAULT NULL,
  `nidn_pembimbing` char(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `proposal`
--

INSERT INTO `proposal` (`id_proposal`, `nim`, `judul`, `jenis_ta`, `file_proposal`, `status`, `tanggal_pengajuan`, `nidn_pembimbing`) VALUES
(3, '202408108', 'wwww', 'Skripsi', NULL, 'Disetujui', '2025-12-27', 'DOSEN001');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sidang`
--

CREATE TABLE `sidang` (
  `id_sidang` int(11) NOT NULL,
  `id_proposal` int(11) DEFAULT NULL,
  `file_laporan` varchar(255) DEFAULT NULL,
  `nidn_penguji` char(15) DEFAULT NULL,
  `tanggal_sidang` datetime DEFAULT NULL,
  `ruangan` varchar(50) DEFAULT NULL,
  `nilai_akhir` float DEFAULT NULL,
  `status_sidang` varchar(30) DEFAULT 'Menunggu Jadwal',
  `status_lulus` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `sidang`
--

INSERT INTO `sidang` (`id_sidang`, `id_proposal`, `file_laporan`, `nidn_penguji`, `tanggal_sidang`, `ruangan`, `nilai_akhir`, `status_sidang`, `status_lulus`) VALUES
(3, 3, 'aww', 'DOSEN001', '2025-12-20 03:00:00', 'B801', NULL, 'Dijadwalkan', NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bimbingan`
--
ALTER TABLE `bimbingan`
  ADD PRIMARY KEY (`id_bimbingan`),
  ADD KEY `nim` (`nim`),
  ADD KEY `nidn_pembimbing` (`nidn_pembimbing`);

--
-- Indeks untuk tabel `dosen`
--
ALTER TABLE `dosen`
  ADD PRIMARY KEY (`nidn`);

--
-- Indeks untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`nim`);

--
-- Indeks untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id_notif`);

--
-- Indeks untuk tabel `perpanjangan`
--
ALTER TABLE `perpanjangan`
  ADD PRIMARY KEY (`id_perpanjangan`),
  ADD KEY `id_proposal` (`id_proposal`),
  ADD KEY `nim` (`nim`);

--
-- Indeks untuk tabel `pesan`
--
ALTER TABLE `pesan`
  ADD PRIMARY KEY (`id_pesan`);

--
-- Indeks untuk tabel `proposal`
--
ALTER TABLE `proposal`
  ADD PRIMARY KEY (`id_proposal`),
  ADD KEY `nim` (`nim`);

--
-- Indeks untuk tabel `sidang`
--
ALTER TABLE `sidang`
  ADD PRIMARY KEY (`id_sidang`),
  ADD KEY `id_proposal` (`id_proposal`),
  ADD KEY `nidn_penguji` (`nidn_penguji`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `bimbingan`
--
ALTER TABLE `bimbingan`
  MODIFY `id_bimbingan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id_notif` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `perpanjangan`
--
ALTER TABLE `perpanjangan`
  MODIFY `id_perpanjangan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pesan`
--
ALTER TABLE `pesan`
  MODIFY `id_pesan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `proposal`
--
ALTER TABLE `proposal`
  MODIFY `id_proposal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `sidang`
--
ALTER TABLE `sidang`
  MODIFY `id_sidang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `bimbingan`
--
ALTER TABLE `bimbingan`
  ADD CONSTRAINT `bimbingan_ibfk_1` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`),
  ADD CONSTRAINT `bimbingan_ibfk_2` FOREIGN KEY (`nidn_pembimbing`) REFERENCES `dosen` (`nidn`);

--
-- Ketidakleluasaan untuk tabel `perpanjangan`
--
ALTER TABLE `perpanjangan`
  ADD CONSTRAINT `perpanjangan_ibfk_1` FOREIGN KEY (`id_proposal`) REFERENCES `proposal` (`id_proposal`),
  ADD CONSTRAINT `perpanjangan_ibfk_2` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`);

--
-- Ketidakleluasaan untuk tabel `proposal`
--
ALTER TABLE `proposal`
  ADD CONSTRAINT `proposal_ibfk_1` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `sidang`
--
ALTER TABLE `sidang`
  ADD CONSTRAINT `sidang_ibfk_1` FOREIGN KEY (`id_proposal`) REFERENCES `proposal` (`id_proposal`),
  ADD CONSTRAINT `sidang_ibfk_2` FOREIGN KEY (`nidn_penguji`) REFERENCES `dosen` (`nidn`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
