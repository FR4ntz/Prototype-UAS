-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 28 Des 2025 pada 20.10
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
  `bukti_foto` varchar(255) DEFAULT NULL,
  `catatan_dosen` text DEFAULT NULL,
  `status` enum('Menunggu','ACC','Revisi') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `dosen`
--

CREATE TABLE `dosen` (
  `nidn` char(15) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('Dosen','Koordinator','Penguji') NOT NULL DEFAULT 'Dosen'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `dosen`
--

INSERT INTO `dosen` (`nidn`, `nama`, `email`, `password`, `role`) VALUES
('041002', 'Pak Chan', NULL, 'e10adc3949ba59abbe56e057f20f883e', 'Penguji'),
('041003', 'Pak Cahyono', NULL, 'e10adc3949ba59abbe56e057f20f883e', 'Dosen'),
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
('2024081010', 'Zidane Tirta Nugraha', NULL, '202cb962ac59075b964b07152d234b70', 160, 1000),
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
  `nim` char(15) NOT NULL,
  `id_proposal` int(11) NOT NULL,
  `lama_perpanjangan` int(11) NOT NULL,
  `alasan` text NOT NULL,
  `status_perpanjangan` enum('Diajukan','Disetujui','Ditolak') NOT NULL DEFAULT 'Diajukan',
  `tanggal_pengajuan` date DEFAULT NULL
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
  `nim` char(15) NOT NULL,
  `nidn_pembimbing` char(20) DEFAULT NULL,
  `judul` text NOT NULL,
  `jenis_ta` varchar(50) NOT NULL DEFAULT 'Rancang Bangun',
  `file_proposal` varchar(255) DEFAULT NULL,
  `status` enum('Diajukan','Disetujui','Ditolak','Revisi') DEFAULT 'Diajukan',
  `catatan_koor` text DEFAULT NULL,
  `tanggal_pengajuan` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `proposal`
--

INSERT INTO `proposal` (`id_proposal`, `nim`, `nidn_pembimbing`, `judul`, `jenis_ta`, `file_proposal`, `status`, `catatan_koor`, `tanggal_pengajuan`) VALUES
(2, '2024081010', '041003', 'Proposal Saya', 'Rancang Bangun', '2024081010_1766946505.pdf', 'Disetujui', 'Semangat', '2025-12-28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sidang`
--

CREATE TABLE `sidang` (
  `id_sidang` int(11) NOT NULL,
  `id_proposal` int(11) NOT NULL,
  `nidn_penguji` char(20) DEFAULT NULL,
  `tanggal_sidang` datetime DEFAULT NULL,
  `ruangan` varchar(50) DEFAULT NULL,
  `nilai_akhir` int(11) DEFAULT NULL,
  `status_sidang` enum('Menunggu Jadwal','Dijadwalkan','Selesai','Lulus','Tidak Lulus','Revisi') DEFAULT 'Menunggu Jadwal',
  `file_laporan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  ADD KEY `nim` (`nim`),
  ADD KEY `id_proposal` (`id_proposal`);

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
  ADD KEY `nim` (`nim`),
  ADD KEY `nidn_pembimbing` (`nidn_pembimbing`);

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
  MODIFY `id_bimbingan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id_notif` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `perpanjangan`
--
ALTER TABLE `perpanjangan`
  MODIFY `id_perpanjangan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pesan`
--
ALTER TABLE `pesan`
  MODIFY `id_pesan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `proposal`
--
ALTER TABLE `proposal`
  MODIFY `id_proposal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `sidang`
--
ALTER TABLE `sidang`
  MODIFY `id_sidang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  ADD CONSTRAINT `perpanjangan_ibfk_1` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`) ON DELETE CASCADE,
  ADD CONSTRAINT `perpanjangan_ibfk_2` FOREIGN KEY (`id_proposal`) REFERENCES `proposal` (`id_proposal`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `proposal`
--
ALTER TABLE `proposal`
  ADD CONSTRAINT `proposal_ibfk_1` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`) ON DELETE CASCADE,
  ADD CONSTRAINT `proposal_ibfk_2` FOREIGN KEY (`nidn_pembimbing`) REFERENCES `dosen` (`nidn`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `sidang`
--
ALTER TABLE `sidang`
  ADD CONSTRAINT `sidang_ibfk_1` FOREIGN KEY (`id_proposal`) REFERENCES `proposal` (`id_proposal`) ON DELETE CASCADE,
  ADD CONSTRAINT `sidang_ibfk_2` FOREIGN KEY (`nidn_penguji`) REFERENCES `dosen` (`nidn`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
