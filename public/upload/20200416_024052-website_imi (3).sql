-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 01 Apr 2020 pada 05.43
-- Versi server: 10.3.16-MariaDB
-- Versi PHP: 7.3.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `website_imi`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `expireds`
--

CREATE TABLE `expireds` (
  `expired_domain` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expired_part` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expired_desc` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expired_expdate` date NOT NULL,
  `expired_loc` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expired_lot` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expired_amt` decimal(10,2) NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `expireds`
--

INSERT INTO `expireds` (`expired_domain`, `expired_part`, `expired_desc`, `expired_expdate`, `expired_loc`, `expired_lot`, `expired_amt`, `remember_token`, `created_at`, `updated_at`) VALUES
('10-USA', '01010', 'test123', '2020-04-04', '010', '01010-1216-12', '2500.00', NULL, '2020-03-10 22:28:34', '2020-03-10 22:28:34'),
('10-USA', '01011', 'Supplies Kit', '2020-05-05', '010', NULL, '0.00', NULL, '2020-03-10 22:28:34', '2020-03-10 22:28:34'),
('10-USA', '50001', 'Probe Unit - 10 Mhz', '2020-03-13', '020', '50001-0617-9', '1320.00', NULL, '2020-03-10 22:28:34', '2020-03-10 22:28:34'),
('10-USA', '02001', 'Automotive Connector', '2020-06-06', '010', NULL, '107430.00', NULL, '2020-03-10 22:28:34', '2020-03-10 22:28:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `expitem`
--

CREATE TABLE `expitem` (
  `id` int(11) NOT NULL,
  `onemonth` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `expitem`
--

INSERT INTO `expitem` (`id`, `onemonth`) VALUES
(1, 4),
(2, 5),
(3, 9);

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(4, '2014_10_12_000000_create_users_table', 1),
(5, '2014_10_12_100000_create_password_resets_table', 1),
(6, '2019_08_19_000000_create_failed_jobs_table', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `nosales`
--

CREATE TABLE `nosales` (
  `nosales_domain` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nosales_cust` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nosales_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nosales_period` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nosales_lastdate` date NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `safeties`
--

CREATE TABLE `safeties` (
  `safety_domain` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `safety_part` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `safety_desc` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `safety_um` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `safety_prod_line` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `safety_qty_oh` decimal(10,2) NOT NULL,
  `safety_safe_stock` decimal(10,2) NOT NULL,
  `safety_qty_ord` int(11) NOT NULL,
  `safety_qty_all` int(11) NOT NULL,
  `safety_flag` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `safeties`
--

INSERT INTO `safeties` (`safety_domain`, `safety_part`, `safety_desc`, `safety_um`, `safety_prod_line`, `safety_qty_oh`, `safety_safe_stock`, `safety_qty_ord`, `safety_qty_all`, `safety_flag`, `remember_token`, `created_at`, `updated_at`) VALUES
('10-USA', '01012', 'Sterile Probe Covers, 20', 'BX', '20', '675.00', '250.00', 2925, 0, 'high', NULL, '2020-03-19 01:31:04', '2020-03-19 01:31:04'),
('10-USA', '01012', 'Sterile Probe Covers, 20', 'BX', '20', '0.00', '250.00', 10, 0, 'low', NULL, '2020-03-19 01:31:04', '2020-03-19 01:31:04'),
('10-USA', '01013', 'Sterile Wipes, Box of 50', 'BX', '20', '0.00', '250.00', 8100, 0, 'low', NULL, '2020-03-19 01:31:04', '2020-03-19 01:31:04'),
('10-USA', '01013', 'Sterile Wipes, Box of 50', 'BX', '20', '0.00', '250.00', 5, 0, 'low', NULL, '2020-03-19 01:31:04', '2020-03-19 01:31:04'),
('10-USA', '02301', 'Compact Valve Assembly ', 'EA', '10', '62.00', '60.00', 0, 62, 'high', NULL, '2020-03-19 01:31:04', '2020-03-19 01:31:04'),
('10-USA', '02302', 'Compact Valve Assembly ', 'EA', '10', '81.00', '80.00', 0, 81, 'high', NULL, '2020-03-19 01:31:04', '2020-03-19 01:31:04'),
('10-USA', '02303', 'Compact Valve Assembly ', 'EA', '10', '12.00', '10.00', 0, 12, 'high', NULL, '2020-03-19 01:31:05', '2020-03-19 01:31:05'),
('10-USA', '02306', 'Compact Valve Assembly', 'EA', '10', '610.00', '580.00', 0, 225, 'high', NULL, '2020-03-19 01:31:05', '2020-03-19 01:31:05'),
('10-USA', '60010', 'Pepared Layered Mat', 'G', '60', '0.00', '1000.00', 27150, 0, 'low', NULL, '2020-03-19 01:31:05', '2020-03-19 01:31:05'),
('10-USA', '60010', 'Pepared Layered Mat', 'G', '60', '0.00', '1000.00', 0, 0, 'low', NULL, '2020-03-19 01:31:05', '2020-03-19 01:31:05'),
('10-USA', '60012', 'Electrodes', 'EA', '60', '534.00', '200.00', 684, 0, 'high', NULL, '2020-03-19 01:31:05', '2020-03-19 01:31:05'),
('10-USA', '60012', 'Electrodes', 'EA', '60', '0.00', '200.00', 0, 0, 'low', NULL, '2020-03-19 01:31:05', '2020-03-19 01:31:05'),
('10-USA', '60050', 'Base Unit / CPU ', 'EA', '60', '373.00', '25.00', 114, 0, 'high', NULL, '2020-03-19 01:31:05', '2020-03-19 01:31:05'),
('10-USA', '60050', 'Base Unit / CPU ', 'EA', '60', '0.00', '25.00', 0, 0, 'low', NULL, '2020-03-19 01:31:05', '2020-03-19 01:31:05'),
('10-USA', '60051', 'Microprocessor IM Rev. A', 'EA', '60', '201.00', '25.00', 114, 0, 'high', NULL, '2020-03-19 01:31:05', '2020-03-19 01:31:05'),
('10-USA', '60051', 'Microprocessor IM Rev. A', 'EA', '60', '0.00', '25.00', 0, 0, 'low', NULL, '2020-03-19 01:31:05', '2020-03-19 01:31:05'),
('10-USA', '60051H', 'Microprocess IM HighRes', 'EA', '60', '0.00', '25.00', 0, 0, 'low', NULL, '2020-03-19 01:31:05', '2020-03-19 01:31:05'),
('10-USA', '60051M', 'Microprocess IM MedRes', 'EA', '60', '0.00', '25.00', 0, 0, 'low', NULL, '2020-03-19 01:31:05', '2020-03-19 01:31:05'),
('10-USA', '60052', 'High Performance CPU', 'EA', '60', '61.00', '25.00', 0, 0, 'high', NULL, '2020-03-19 01:31:05', '2020-03-19 01:31:05'),
('10-USA', '60052', 'High Performance CPU', 'EA', '60', '0.00', '25.00', 0, 0, 'low', NULL, '2020-03-19 01:31:05', '2020-03-19 01:31:05'),
('10-USA', '60052C', 'Consigned CPU', 'EA', '60', '0.00', '25.00', 0, 0, 'low', NULL, '2020-03-19 01:31:05', '2020-03-19 01:31:05'),
('10-USA', '60092', 'Microprocessor', 'EA', '60', '0.00', '25.00', 0, 0, 'low', NULL, '2020-03-19 01:31:05', '2020-03-19 01:31:05'),
('10-USA', '90098', 'Returnable Containers', 'EA', '65', '100.00', '10.00', 0, 0, 'high', NULL, '2020-03-19 01:31:05', '2020-03-19 01:31:05'),
('10-USA', '90098', 'Returnable Containers', 'EA', '65', '18750.00', '10.00', 0, 0, 'high', NULL, '2020-03-19 01:31:05', '2020-03-19 01:31:05'),
('10-USA', '90099', 'Expendable Containers', 'EA', '65', '100.00', '10.00', 0, 0, 'high', NULL, '2020-03-19 01:31:05', '2020-03-19 01:31:05'),
('10-USA', '90099', 'Expendable Containers', 'EA', '65', '18750.00', '10.00', 0, 0, 'high', NULL, '2020-03-19 01:31:05', '2020-03-19 01:31:05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `shipments`
--

CREATE TABLE `shipments` (
  `ship_id` int(10) UNSIGNED NOT NULL,
  `ship_domain` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ship_so` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ship_item` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ship_amt` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `flag` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `domain`, `role`, `flag`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(4, 'Andrew Conan', 'admin', 'andrew@ptimi.co.id', 'IMI', 'Admin', NULL, NULL, '$2y$10$cSNa6TZWULxH72nfo7wlZOAXzjmpC.VoHhW9WcnTegcxGtiz70gX6', NULL, '2020-03-16 23:39:35', '2020-03-16 23:39:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `xxrole_mstrs`
--

CREATE TABLE `xxrole_mstrs` (
  `id` int(11) NOT NULL,
  `xxrole_domain` varchar(10) NOT NULL,
  `xxrole_role` varchar(50) NOT NULL,
  `xxrole_desc` varchar(100) NOT NULL,
  `xxrole_flag` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `xxrole_mstrs`
--

INSERT INTO `xxrole_mstrs` (`id`, `xxrole_domain`, `xxrole_role`, `xxrole_desc`, `xxrole_flag`) VALUES
(2, 'IMI', 'Admin', 'Admin IMI', 'IV01IV02IV03IV04SO01SO02US01US02US03'),
(3, 'IMI', 'Support', 'Support IMI', 'IV01IV03IV04SO02US03');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `expitem`
--
ALTER TABLE `expitem`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `xxrole_mstrs`
--
ALTER TABLE `xxrole_mstrs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `expitem`
--
ALTER TABLE `expitem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `xxrole_mstrs`
--
ALTER TABLE `xxrole_mstrs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
