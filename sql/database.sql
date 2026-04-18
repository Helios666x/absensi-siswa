-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 18 Apr 2026 pada 18.01
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_absensi`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `tipe_user` enum('guru','siswa') NOT NULL,
  `user_id` int(11) NOT NULL,
  `jadwal_id` int(11) DEFAULT NULL,
  `jenis_absensi` enum('pagi','kelas','sholat_zuhur') NOT NULL,
  `status` enum('hadir','terlambat','izin','sakit','alpha') NOT NULL,
  `keterangan` text DEFAULT NULL,
  `waktu_input` datetime NOT NULL,
  `metode` enum('fingerprint','manual') DEFAULT 'fingerprint',
  `device_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `device`
--

CREATE TABLE `device` (
  `id` int(11) NOT NULL,
  `nama_device` varchar(100) NOT NULL,
  `lokasi` varchar(100) NOT NULL,
  `api_key` varchar(100) NOT NULL,
  `sesi_jadwal_id` int(11) DEFAULT NULL,
  `last_heartbeat` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `guru`
--

CREATE TABLE `guru` (
  `id` int(11) NOT NULL,
  `nip` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `mapel` varchar(100) DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `foto` varchar(255) DEFAULT 'avatar-default.png',
  `status_aktif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal`
--

CREATE TABLE `jadwal` (
  `id` int(11) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  `guru_id` int(11) NOT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu') NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `nama_kegiatan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kelas`
--

CREATE TABLE `kelas` (
  `id` int(11) NOT NULL,
  `nama_kelas` varchar(50) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `tingkat` varchar(10) NOT NULL,
  `guru_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengaturan_absensi`
--

CREATE TABLE `pengaturan_absensi` (
  `id` int(11) NOT NULL,
  `jenis` enum('pagi','sholat_zuhur') NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `toleransi_terlambat` int(11) DEFAULT 15
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengaturan_absensi`
--

INSERT INTO `pengaturan_absensi` (`id`, `jenis`, `jam_mulai`, `jam_selesai`, `toleransi_terlambat`) VALUES
(1, 'pagi', '06:30:00', '07:00:00', 15),
(2, 'sholat_zuhur', '12:00:00', '12:30:00', 10);

-- --------------------------------------------------------

--
-- Struktur dari tabel `sidik_jari`
--

CREATE TABLE `sidik_jari` (
  `id` int(11) NOT NULL,
  `tipe_user` enum('guru','siswa') NOT NULL,
  `user_id` int(11) NOT NULL,
  `fingerprint_id` int(11) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `siswa`
--

CREATE TABLE `siswa` (
  `id` int(11) NOT NULL,
  `nis` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `id_kelas` int(11) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `foto` varchar(255) DEFAULT 'avatar-default.png',
  `status_aktif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','guru','siswa') NOT NULL,
  `related_id` int(11) DEFAULT NULL,
  `status_aktif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `related_id`, `status_aktif`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, 1, '2026-04-18 15:50:16');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jadwal_id` (`jadwal_id`),
  ADD KEY `device_id` (`device_id`);

--
-- Indeks untuk tabel `device`
--
ALTER TABLE `device`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `api_key` (`api_key`),
  ADD KEY `sesi_jadwal_id` (`sesi_jadwal_id`);

--
-- Indeks untuk tabel `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nip` (`nip`);

--
-- Indeks untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kelas_id` (`kelas_id`),
  ADD KEY `guru_id` (`guru_id`);

--
-- Indeks untuk tabel `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guru_id` (`guru_id`);

--
-- Indeks untuk tabel `pengaturan_absensi`
--
ALTER TABLE `pengaturan_absensi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `jenis` (`jenis`);

--
-- Indeks untuk tabel `sidik_jari`
--
ALTER TABLE `sidik_jari`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nis` (`nis`),
  ADD KEY `id_kelas` (`id_kelas`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `device`
--
ALTER TABLE `device`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `guru`
--
ALTER TABLE `guru`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pengaturan_absensi`
--
ALTER TABLE `pengaturan_absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `sidik_jari`
--
ALTER TABLE `sidik_jari`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`jadwal_id`) REFERENCES `jadwal` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `absensi_ibfk_2` FOREIGN KEY (`device_id`) REFERENCES `device` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `device`
--
ALTER TABLE `device`
  ADD CONSTRAINT `device_ibfk_1` FOREIGN KEY (`sesi_jadwal_id`) REFERENCES `jadwal` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `jadwal_ibfk_1` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`),
  ADD CONSTRAINT `jadwal_ibfk_2` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`id`);

--
-- Ketidakleluasaan untuk tabel `kelas`
--
ALTER TABLE `kelas`
  ADD CONSTRAINT `kelas_ibfk_1` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD CONSTRAINT `siswa_ibfk_1` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
