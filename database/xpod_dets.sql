-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 03 Des 2020 pada 09.19
-- Versi server: 10.4.11-MariaDB
-- Versi PHP: 7.4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `web_supp`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `xpod_dets`
--

CREATE TABLE `xpod_dets` (
  `id` int(11) NOT NULL,
  `xpod_domain` varchar(10) NOT NULL,
  `xpod_nbr` varchar(15) NOT NULL,
  `xpod_vend` varchar(10) NOT NULL,
  `xpod_line` int(11) NOT NULL,
  `xpod_part` varchar(50) NOT NULL,
  `xpod_desc` varchar(100) DEFAULT NULL,
  `xpod_um` varchar(5) DEFAULT NULL,
  `xpod_qty_ord` decimal(10,2) DEFAULT NULL,
  `xpod_qty_rcvd` decimal(10,2) DEFAULT NULL,
  `xpod_qty_open` decimal(10,0) DEFAULT NULL,
  `xpod_qty_ship` decimal(12,2) NOT NULL,
  `xpod_qty_tole` decimal(12,2) NOT NULL,
  `xpod_qty_prom` decimal(10,0) NOT NULL,
  `xpod_price` varchar(20) DEFAULT NULL,
  `xpod_loc` varchar(15) DEFAULT NULL,
  `xpod_lot` varchar(25) DEFAULT NULL,
  `xpod_date` date DEFAULT NULL,
  `xpod_ship_date` date DEFAULT NULL,
  `xpod_due_date` date DEFAULT NULL,
  `xpod_eff_date` date DEFAULT NULL,
  `xpod_prom_date` date NOT NULL,
  `xpod_status` varchar(100) DEFAULT 'UnConfirm',
  `xpod_status1` varchar(40) NOT NULL,
  `xpod_cancel` varchar(5) DEFAULT NULL,
  `xpod_site` varchar(10) DEFAULT NULL,
  `xpod_ref` varchar(255) DEFAULT NULL,
  `xpod_last_conf` date DEFAULT NULL,
  `xpod_tot_conf` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `xpod_qty_shipx` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `xpod_dets`
--

INSERT INTO `xpod_dets` (`id`, `xpod_domain`, `xpod_nbr`, `xpod_vend`, `xpod_line`, `xpod_part`, `xpod_desc`, `xpod_um`, `xpod_qty_ord`, `xpod_qty_rcvd`, `xpod_qty_open`, `xpod_qty_ship`, `xpod_qty_tole`, `xpod_qty_prom`, `xpod_price`, `xpod_loc`, `xpod_lot`, `xpod_date`, `xpod_ship_date`, `xpod_due_date`, `xpod_eff_date`, `xpod_prom_date`, `xpod_status`, `xpod_status1`, `xpod_cancel`, `xpod_site`, `xpod_ref`, `xpod_last_conf`, `xpod_tot_conf`, `created_at`, `updated_at`, `xpod_qty_shipx`) VALUES
(4469, '', 'T5', '10L1001', 1, 'T5', 'tes125t text123', 'EA', '1.00', NULL, NULL, '0.00', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0000-00-00', 'UnConfirm', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `xpod_dets`
--
ALTER TABLE `xpod_dets`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `xpod_dets`
--
ALTER TABLE `xpod_dets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4470;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
