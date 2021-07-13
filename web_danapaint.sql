-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 05 Des 2020 pada 13.43
-- Versi server: 10.4.14-MariaDB
-- Versi PHP: 7.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `web_danapaint`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `activity`
--

CREATE TABLE `activity` (
  `activity_code` varchar(24) NOT NULL,
  `activity_desc` varchar(24) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `activity`
--

INSERT INTO `activity` (`activity_code`, `activity_desc`, `created_at`, `updated_at`) VALUES
('DED', 'Abebe', '2020-11-05 09:01:02', '2020-11-05 09:01:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `approvals`
--

CREATE TABLE `approvals` (
  `id` int(11) NOT NULL,
  `userid` varchar(6) NOT NULL,
  `site_app` varchar(50) NOT NULL,
  `order` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `approvals`
--

INSERT INTO `approvals` (`id`, `userid`, `site_app`, `order`, `created_at`, `updated_at`) VALUES
(62, 'admin', 'R0014', 1, '2020-11-09 09:21:22', '2020-11-09 09:21:22'),
(63, 'b', 'R0014', 2, '2020-11-09 09:21:22', '2020-11-09 09:21:22'),
(64, 'abebe', 'R0013', 1, '2020-11-09 09:21:32', '2020-11-09 09:21:32'),
(65, 'test', 'R0013', 2, '2020-11-09 09:21:32', '2020-11-09 09:21:32'),
(70, 'test', 'R0015', 1, '2020-11-09 09:24:21', '2020-11-09 09:24:21'),
(71, 'abebe', 'R0012', 1, '2020-11-10 17:02:07', '2020-11-10 17:02:07'),
(72, 'abebe', '10-100', 1, '2020-11-30 16:16:29', '2020-11-30 16:16:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `approval_hist`
--

CREATE TABLE `approval_hist` (
  `id` int(11) NOT NULL,
  `so_nbr` varchar(12) NOT NULL,
  `approval_approver` varchar(6) NOT NULL,
  `approval_alt_approver` varchar(6) DEFAULT NULL,
  `approval_seq` int(11) NOT NULL,
  `approval_date` datetime DEFAULT NULL,
  `approval_reason` varchar(100) DEFAULT NULL,
  `approval_status` varchar(10) DEFAULT NULL,
  `approval_by` varchar(6) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `approval_tmp`
--

CREATE TABLE `approval_tmp` (
  `id` int(11) NOT NULL,
  `approval_approver` varchar(6) NOT NULL,
  `approval_alt_approver` varchar(6) DEFAULT NULL,
  `approval_seq` int(11) NOT NULL,
  `approval_date` datetime DEFAULT NULL,
  `approval_reason` varchar(100) DEFAULT NULL,
  `approval_status` varchar(10) DEFAULT NULL,
  `approval_by` varchar(6) DEFAULT NULL,
  `so_nbr` varchar(8) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `approval_tmp`
--

INSERT INTO `approval_tmp` (`id`, `approval_approver`, `approval_alt_approver`, `approval_seq`, `approval_date`, `approval_reason`, `approval_status`, `approval_by`, `so_nbr`, `created_at`) VALUES
(16, 'admin', NULL, 1, NULL, NULL, NULL, NULL, '10S10066', '2020-12-01 10:27:31'),
(17, 'a', NULL, 2, NULL, NULL, NULL, NULL, '10S10066', '2020-12-04 10:28:20'),
(18, 'b', NULL, 3, NULL, NULL, NULL, NULL, '10S10066', '2020-12-04 10:28:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `customers`
--

CREATE TABLE `customers` (
  `cust_code` varchar(255) NOT NULL,
  `cust_desc` varchar(255) NOT NULL,
  `cust_top` int(25) NOT NULL,
  `cust_alamat` varchar(255) NOT NULL,
  `customer_site` varchar(255) NOT NULL,
  `customer_region` varchar(255) NOT NULL,
  `custcredit_limit` int(24) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `customers`
--

INSERT INTO `customers` (`cust_code`, `cust_desc`, `cust_top`, `cust_alamat`, `customer_site`, `customer_region`, `custcredit_limit`, `created_at`, `updated_at`) VALUES
('10C1000', 'LTZ Retail', 30, '300 Moutain View  ', '10-400', 'US-S', 0, NULL, '2020-12-04 15:03:42'),
('10C1001', 'MediLogic', 3, '225 South Street  ', '10-100', 'US-E', 0, NULL, '2020-12-04 15:03:42'),
('10C1003', 'Pacific Health Care Systems', 2, '1001 Bryant Avenue  ', '10-100', 'US-W', 10000000, NULL, '2020-12-04 15:03:42'),
('10C1005', 'Rockland Industrial Company', 60, '100 Liberty Circle  ', '10-100', 'US-E', 5000000, NULL, '2020-12-04 15:03:42'),
('11C1000', 'Cryocath Technologies Inc.', 30, '100 Quebec City Avenue  ', '10-100', 'CAN', 100000000, NULL, '2020-12-04 15:03:42'),
('11C1002', 'CanCar Corporation', 30, '500 Little River Blvd.  ', '10-200', 'CAN', 2500000, NULL, '2020-12-04 15:03:42'),
('12C1000', 'Alcon Laboratories', 30, '55 Mexico City Blvd  ', '10-500', 'MEX', 100000000, NULL, '2020-12-04 15:03:42'),
('12C1002', 'Commercial Mexicana Supermar', 90, 'Poinente 150 No. 956  ', '10-300', 'MEX', 0, NULL, '2020-12-04 15:03:42'),
('20C1001', 'Bon Marche', 60, '55 Rue Gauthey  ', '10-100', 'EMEA', 760000, NULL, '2020-12-04 15:03:42'),
('20C1002', 'BGM', 60, '700 Rue Gadon  ', '10-100', 'EMEA', 290000, NULL, '2020-12-04 15:03:42'),
('21C1001', 'Hospital Equipment Services', 60, '30 De Bolder  ', '10-100', 'EMEA', 6000000, NULL, '2020-12-04 15:03:42'),
('21C1002', 'Van Hess Foods International', 30, 'De Bolder 55  ', '10-400', 'EMEA', 500000, NULL, '2020-12-04 15:03:42'),
('22C1001', 'Teasdale Hospital Equipment', 30, '500 Mentor Hse  ', '10-100', 'EMEA', 4067000, NULL, '2020-12-04 15:03:42'),
('22C1002', 'Electronic Services Ltd', 30, '180 Mentor Hse  ', '10-100', 'EMEA', 999000, NULL, '2020-12-04 15:03:42'),
('30C1000', 'Chaobao Cleaning Products', 60, '40 Naycn  ', '10-300', 'AP', 0, NULL, '2020-12-04 15:03:42'),
('30C1001', 'Medical Products Co., Ltd', 60, '100 Danxia Road  ', '10-100', 'AP', 0, NULL, '2020-12-04 15:03:42'),
('31C1000', 'Healthscope', 30, '700 St. Kilda Road  ', '10-500', 'AP', 0, NULL, '2020-12-04 15:03:42'),
('31C1002', 'Woolworths Ltd', 90, '567 St Kilda Road  ', '10-400', 'AP', 0, NULL, '2020-12-04 15:03:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `customers_type`
--

CREATE TABLE `customers_type` (
  `id` int(11) NOT NULL,
  `cust_type` varchar(4) NOT NULL,
  `description` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `customers_type`
--

INSERT INTO `customers_type` (`id`, `cust_type`, `description`) VALUES
(1, 'DIST', 'Distributor'),
(2, 'TOKO', 'Toko');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cust_relation`
--

CREATE TABLE `cust_relation` (
  `id` int(11) NOT NULL,
  `cust_code_parent` varchar(24) NOT NULL,
  `cust_code_child` varchar(24) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cust_shipto`
--

CREATE TABLE `cust_shipto` (
  `id` int(12) NOT NULL,
  `cust_code` varchar(50) NOT NULL,
  `shipto` varchar(50) NOT NULL,
  `custname` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `cust_shipto`
--

INSERT INTO `cust_shipto` (`id`, `cust_code`, `shipto`, `custname`, `created_at`, `updated_at`) VALUES
(1, '10C1000', '10C1000E', 'LTZ Retail', '2020-12-04 15:06:07', '2020-12-05 11:44:06'),
(2, '11C1002', '11C1002C', 'CanCar Corporation', '2020-12-04 15:06:07', '2020-12-05 11:44:06'),
(3, '12C1002', '12C1002A', 'Commercial Mexicana Supermarket', '2020-12-04 15:06:07', '2020-12-05 11:44:06'),
(4, '20C1001', '20C1001A', 'Bon Marche', '2020-12-04 15:06:07', '2020-12-05 11:44:06'),
(5, '21C1002', '21C1002A', 'Van es Foods International', '2020-12-04 15:06:07', '2020-12-05 11:44:06'),
(6, '22C1002', '22C1002A', 'Electronic Services Ltd', '2020-12-04 15:06:07', '2020-12-05 11:44:06'),
(7, '30C1000', '30C1000A', 'Chaobao Cleaning Products', '2020-12-04 15:06:07', '2020-12-05 11:44:06'),
(8, '31C1002', '31C1002A', 'Woolworths Ltd', '2020-12-04 15:06:07', '2020-12-05 11:44:06'),
(9, '10C1005', '10C1005A', 'Rockland Industrial Company', '2020-12-04 15:06:07', '2020-12-05 11:44:06'),
(10, '21C1001', '21C1001', 'Hospital Equipment Services', '2020-12-04 15:06:07', '2020-12-05 11:44:06'),
(11, '30C1001', '30C1001A', 'Medical Products Co., Ltd', '2020-12-04 15:06:07', '2020-12-05 11:44:06'),
(12, '10C1003', '10C1003', 'Pacific Health Care Systems', '2020-12-04 15:06:07', '2020-12-05 11:44:06'),
(13, '31C1000', '31C1000A', 'Healthscope', '2020-12-04 15:06:07', '2020-12-05 11:44:06'),
(14, '22C1001', '22C1001A', 'Teasdale Hospital Equipment', '2020-12-04 15:06:07', '2020-12-05 11:44:06'),
(15, '10C1001', '10C1001B', 'Morristown Memorial Hospital', '2020-12-04 15:06:07', '2020-12-05 11:44:06'),
(16, '11C1000', '11C1000A', 'Cryocath Technologies Inc.', '2020-12-04 15:06:07', '2020-12-05 11:44:06'),
(17, '12C1000', '12C1000A', 'Alcon Laboratories', '2020-12-04 15:06:07', '2020-12-05 11:44:06'),
(18, '20C1002', '20C1002A', 'BGM', '2020-12-04 15:06:07', '2020-12-05 11:44:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `dod_det`
--

CREATE TABLE `dod_det` (
  `id` int(11) NOT NULL,
  `dod_nbr` varchar(20) NOT NULL,
  `dod_so` varchar(8) NOT NULL,
  `dod_line` int(11) NOT NULL,
  `dod_flag` int(11) NOT NULL DEFAULT 0,
  `dod_part` varchar(20) NOT NULL,
  `dod_qty` int(11) NOT NULL,
  `dod_status` int(11) NOT NULL DEFAULT 0,
  `do_um` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `dod_det`
--

INSERT INTO `dod_det` (`id`, `dod_nbr`, `dod_so`, `dod_line`, `dod_flag`, `dod_part`, `dod_qty`, `dod_status`, `do_um`, `created_at`) VALUES
(1, 'DO000001', '11000002', 1, 0, 'ab', 12, 1, 'EA', '2020-12-04 17:00:00'),
(2, 'DO000002', '11000002', 1, 0, 'ab', 1, 3, 'EA', '2020-12-04 17:00:00'),
(3, 'DO000003', '11000003', 1, 0, '01040-003', 1, 3, 'EA', '2020-12-04 17:00:00'),
(4, 'DO000003', '11000003', 2, 0, '01040-005', 1, 3, 'EA', '2020-12-04 17:00:00'),
(5, 'DO000004', '11000002', 1, 0, 'ab', 2, 1, 'EA', '2020-12-04 17:00:00'),
(6, 'DO000005', '11000004', 1, 0, '01011', 10, 3, 'EA', '2020-12-04 17:00:00'),
(7, 'DO000005', '11000004', 2, 0, '01012', 2, 3, 'BX', '2020-12-04 17:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `do_mstr`
--

CREATE TABLE `do_mstr` (
  `do_nbr` varchar(20) NOT NULL,
  `do_cust` varchar(20) NOT NULL,
  `do_date` date NOT NULL,
  `do_shipto` varchar(20) NOT NULL,
  `do_site` varchar(20) NOT NULL,
  `do_status` int(11) DEFAULT NULL,
  `do_notes` varchar(200) DEFAULT NULL,
  `do_user` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `do_mstr`
--

INSERT INTO `do_mstr` (`do_nbr`, `do_cust`, `do_date`, `do_shipto`, `do_site`, `do_status`, `do_notes`, `do_user`, `created_at`) VALUES
('DO000001', '10C1000', '2020-12-05', '10C1000', '', 6, 'ini note', 'adm1', '2020-12-04 17:00:00'),
('DO000002', '10C1000', '2020-12-05', '10C1000', '', 3, 'ini note', 'adm1', '2020-12-04 17:00:00'),
('DO000003', '10C1005', '2020-12-05', '10C1005', '', 3, 'tes note', 'adm1', '2020-12-04 17:00:00'),
('DO000004', '10C1000', '2020-12-05', '10C1000', '', 2, 'tes note', 'adm1', '2020-12-04 17:00:00'),
('DO000005', '10C1000', '2020-12-05', '10C1000', '', 3, NULL, 'adm1', '2020-12-04 17:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `endofday`
--

CREATE TABLE `endofday` (
  `id` int(11) NOT NULL,
  `last_run` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `endofday`
--

INSERT INTO `endofday` (`id`, `last_run`) VALUES
(1, '2020-12-03 00:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `connection` text CHARACTER SET utf8mb4 NOT NULL,
  `queue` text CHARACTER SET utf8mb4 NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `groups`
--

CREATE TABLE `groups` (
  `id` varchar(12) NOT NULL,
  `group_name` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `groups`
--

INSERT INTO `groups` (`id`, `group_name`) VALUES
('GR0001', 'Group Pestisida'),
('GR0002', 'Group Herbisida'),
('GR9999', 'Bonus');

-- --------------------------------------------------------

--
-- Struktur dari tabel `items`
--

CREATE TABLE `items` (
  `itemcode` varchar(24) NOT NULL,
  `itemdesc` varchar(255) NOT NULL,
  `item_um` varchar(24) NOT NULL,
  `safety_stock` int(11) NOT NULL,
  `item_location` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `items`
--

INSERT INTO `items` (`itemcode`, `itemdesc`, `item_um`, `safety_stock`, `item_location`, `created_at`, `updated_at`) VALUES
('01010', 'tes125t text123', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('010106', ' ', 'EA', 0, '', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01010d', '00002 text123', 'EA', 0, '', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01010x', ' ', 'EA', 0, '', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01011', 'Supplies Kit ', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01012', 'Sterile Probe Covers, 20 One time use', 'BX', 250, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01013', 'Sterile Wipes, Box of 50 ', 'BX', 250, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01020', 'wwewqeqweqwe ', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01021', 'Surgical Kit ', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01030', 'Consumer Ultrasound ', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01040', 'Industrial Ultrasound ', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01040-001', 'Industrial Ultrasound TO P D 500 KH STANDARD', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01040-002', 'Industrial Ultrasound TO P D 10MHZ STANDARD', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01040-003', 'Industrial Ultrasound FR ONT 500 KH HIGH', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01040-004', 'Industrial Ultrasound TO P D 10MHZ HIGH', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01040-005', 'Industrial Ultrasound TOP D 500KHZ HIGH', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01040-006', 'Industrial Ultrasound TOP D 500KHZ STANDARD', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01040-007', 'Industrial Ultrasound TOP D 10MHZ STANDARD', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01040-008', 'Industrial Ultrasound TOP D 500KHZ STANDARD', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01040-009', 'Industrial Ultrasound TOP D 500KHZ STANDARD', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01040-010', 'Industrial Ultrasound TOP D 10MHZ HIGH', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01040-011', 'Industrial Ultrasound TOP D 500KHZ STANDARD', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01040-012', 'Industrial Ultrasound TOP D 500KHZ STANDARD', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01040-013', 'Industrial Ultrasound TOP D 10MHZ HIGH', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01040-014', 'Industrial Ultrasound TOP D 500KHZ STANDARD', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01040P', 'Industrial Ultrasound Planning Item', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01041', 'Portable 10mhz Ultrasnd ', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01042', 'Portable 500khz Ultrasnd ', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01049', 'Universal Ind Ultrasound Configured', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01050', 'Pocket Ultrasound ', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01060', 'Miniature Implant R1 High Res Imaging', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01061', 'Miniature Implant R2 High Res Imaging', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01070', 'Medical Cart ', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('01090', 'Implantable Ultrasound ', 'EA', 0, '193', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('02001', 'Automotive Connector ', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('02002', 'Electrical Connector  ', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('02003', 'Standard Connector  ', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('02004', 'Laptop Connector ', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('02005', 'Valve Connector ', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('02006', 'Small Standard Connector ', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('02010', 'Motor Asm Connector ', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('02101', 'Valve Assembly 1 Kanban Item', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('02102', 'Valve Assembly 2 Kanban Item', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('02103', 'Valve Assembly 3 Kanban Item', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('02104', 'Valve Assembly 4 Kanban Item', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('02105', 'Valve Assembly 5 Kanban Item', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('02200', 'Motor Asm 8 Way Seat Adj  24V amp 2 hp', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('02210', 'Motor Asm 6-Way Seat Adj 24V 3 amp 1.hp', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('02220', 'Motor Asm 4-Way Seat Adj 24V 3 amp 1.hp', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('02301', 'Compact Valve Assembly  OEM HighV Customer A', 'EA', 60, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('02302', 'Compact Valve Assembly  OEM HighV Customer B', 'EA', 80, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('02303', 'Compact Valve Assembly  OEM HighV Customer C', 'EA', 10, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('02304', 'Compact Valve Assembly Service Item - Discrete', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('02305', 'Compact Valve Assembly DRP Demand', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('02306', 'Compact Valve Assembly Build To Forecast', 'EA', 580, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('02307', 'Compact Valve Assembly MTO A - Discrete', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('02308', 'Compact Valve Assembly MTO B - Discrete', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('02505', 'Sm Valve Connector PL-Casting', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('02600', 'Large Chassis ', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('02610', 'Small Chassis ', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('02620', 'Wide Chassis ', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('03011', 'Pump/Refill, Medical Assortment', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('03012', '2-.5l Bottles, Medical Multi-Pack', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('03013', '4-.5l Bottles, Medical Multi-Pack', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('03021', 'Pump/Refill, Unscented Assortment', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('03022', '2-.5l Bottles, Unscented Multi-Pack', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('03023', '4-.5l Bottles, Unscented Multi-Pack', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('03031', 'Pump/Refill, Scented Assortment', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('03032', '2-.5l Bottles, Scented Multi-Pack', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('03033', '4-.5l Bottles, Scented Multi-Pack', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('03040', 'Lubricant 4l tub ', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('03041', '50-5 ml tube Gel Anesthetic', 'BX', 0, '40', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('03042', '25-15ml tube Gel Anesthetic', 'BX', 0, '40', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('03043', '20-25ml tube Gel Anesthetic', 'BX', 0, '040', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('03090', '25 gallon Disinfectant Bulk', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('03110', 'Pump, Medical Disinfectant', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('03111', '.5l Bottle, Medical Disinfectant', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('03112', '1l Refill, Medical Disinfectant', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('03120', 'Pump, Scented Disinfectant', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('03120-A', 'Disinfectant Kit ', 'EA', 0, '01', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('03121', '.5l, Bottle Scented Disinfectant', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('03122', '1l Bottle, Scented Disinfectant', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('03130', 'Pump, Unscented Disinfectant', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('03131', '.5l Bottle, Unscented Disinfectant', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('03132', '1l Bottle, Unscented Disinfectant', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('03210', '1000 wipes Disinfectant', 'EA', 0, '010RC001', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('03240', 'Lubricant 4 liter Tub ', 'EA', 0, '010RC001', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('04001', 'Fruit Juice  750 ml Bottle', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('04401', 'Grade A No Pulp RTD Case of 24, 750 ml', 'CS', 0, '040', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('04402', 'Grade A Pulp RTD Case of 24, 750 ml', 'CS', 0, '040', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('04405', 'Grade A Concentrate 5 Liter Bottle', 'EA', 0, '040', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('04411', 'Grade B No Pulp RTD Case of 24, 750 ml', 'CS', 0, '040', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('04412', 'Grade B Pulp RTD Case of 24, 750 ml', 'CS', 0, '040', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('04415', 'Grade B Concentrate 5 Liter Bottle', 'EA', 0, '040', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('04510', 'Extra Virgin 500 ml Olive Oil', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('04512', 'Extra Virgin 750 ml Olive Oil', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('04900', 'Animal Feed, Dehydrated 25 Gallon Drum', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('05001', 'Pills, Blister of 12 ', 'EA', 0, '050', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('05002', 'Pills, 50 Tab  ', 'EA', 0, '050', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('05003', 'Pills, 100 Tab ', 'EA', 0, '050', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('05005', 'Pills, 500 Tab ', 'EA', 0, '050', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('05005W', 'Pills, 500 Tab Customer specific pack', 'EA', 0, '050', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('05010', 'Hydration Essentials 50 ', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('05020', 'Salt Pills,100 Tab ', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('50001', 'Probe Unit - 10 Mhz ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('50002', 'Probe Unit - 500 kHz ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('50010', 'Acoustic Transducer A ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('50011', 'Ultrasound Array ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('50020', 'Industrial Housing ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('50020-001', 'Industrial Housing ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('50020-002', 'Industrial Housing ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('50020-003', 'Industrial Housing ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('50020-004', 'Industrial Housing ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('50020-P', 'Housing Pricing Item ', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('50090', 'Acoustic Trandsucer ', 'EA', 0, '193', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('51000', 'Acoustic Oscillator ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('51001', 'Oscillator Element-LG ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('51002', 'Electrode-LG ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('51003', 'PC Chassis ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('52001', 'Valve Body Assembly1 ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('52002', 'Valve Body Assembly 2 ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('52003', 'Valve Body Assembly 3 ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('52004', 'Valve Body Assembly 4 ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('52005', 'Valve Body Assembly 5 ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('52050', 'Stamped Connector ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('52200', 'Motor Mounting Plate Asm 8 Way OEM Cust A', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('52201', 'Motor Mtg Plate 8 Way ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('52210', 'Motor Mtg Plate Asm 6-Way OEM Cust A', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('52211', 'Motor Mtg Plate6-Way ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('52220', 'Motor Mtg Plate Asm 4-Way OEM Cust A', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('52221', 'Motor Mtg Plate4-way ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('52280', 'Seat Adj Switch Assy  ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('53001', 'Sm Valve Body Assy1 PL-Plate-G', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('53002', 'Sm Valve Body Assy2 PL-Plate-G', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('53003', 'Sm Valve Body Assy3 PL-Plate-G', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('53004', 'Sm Valve Body Assy4 PL-Plate-G', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('53005', 'Sm Valve Body Assy5 PL-Plate-G', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('53006', 'Sm Valve Body Assy6 PL-Plate-G', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('53007', 'Sm Valve Body Assy7 PL-Plate-G', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('53008', 'Sm Valve Body Assy8 PL-Plate-G', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('53101', 'Sm Machine Casting1 Press-Dept', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('53102', 'Sm Machine Casting2 PL-Press Group', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('53103', 'Sm Machine Casting3 PL-Press Group', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('53104', 'Sm Machine Casting4 PL-Press Group', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('53105', 'Sm Machine Casting5 PL-Press Group', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('53106', 'Sm Machine Casting6 PL-Press Group', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('53107', 'Sm Machine Casting7 PL-Press Group', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('53108', 'Sm Machine Casting8 PL-Press Group', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('54002', 'Manf (global phantom) Kanban - PL-Plate/03', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('60001', 'Durable Plastic Housing ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('60002', 'Display / Readout tess', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('60003', 'Keyboard ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('60004', 'Transducer - 10 Mhz ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('60005', 'Battery ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('60006', 'Monitor Cable ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('60007', 'Movable Cart ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('60008', 'Printer ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('60008U', 'Universal Printer ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('60009', 'Probe Housing Rev. A ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('60010', 'Pepared Layered Mat ', 'G', 1000, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('60011', 'Oscillator Elements ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('60012', 'Electrodes ', 'EA', 200, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('60013', 'Probe Unit Sealed Unit', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('60014', 'Software CD ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('60015', 'Keyboard Cover ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('60016', 'Transducer -500 kHz ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('60020', 'Cooling Fan ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('60021', 'Battery Backup, Alkaline ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('60022', 'Battery Backup, Lithium ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('60023', 'Battery Backup, NiCd ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('60030', 'Ultrasound Cart Assembled To Order', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:18'),
('60030-001', 'Ultrasound Cart Assembled To Order', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60031', 'Drawer Assembly ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60032', 'Shelf Assembly ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60041', 'Aluminum Housing Machined', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60042', 'Sensor P-Ultrasound ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60043', 'Touch Screen P-Ultrasound', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60044', 'Battery, Lithium Ion P-Ultrasound', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60045', 'Circuitboard Module P-Ultrasound', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60046', 'CPU P-Ultrasound ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60050', 'Base Unit / CPU  ', 'EA', 25, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60051', 'Microprocessor IM Rev. A ', 'EA', 25, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60051H', 'Microprocess IM HighRes ', 'EA', 25, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60051M', 'Microprocess IM MedRes ', 'EA', 25, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60052', 'High Performance CPU ', 'EA', 25, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60052C', 'Consigned CPU ', 'EA', 25, '100', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60060', 'White Paint ', 'GA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60061', 'Black Paint ', 'GA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60062', 'Paint, Other ', 'GA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60070', 'Medical Cart 1\" Wheels ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60080', 'Power Cord - UK ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60081', 'Power Cord - US ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60082', 'Power Cord - Australia ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60083', 'Power Cord - Universal ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60088', 'Power Converter-Standard ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60089', 'Power Converter - Smart ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60090', 'Small Sheet Steel 80X 120 cm', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60091', 'Large Sheet Steel 160 x 200 cm', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60092', 'Microprocessor ', 'EA', 25, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60093', 'Stainless Steel Sheet Supplier Schedules', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('60099', 'Probe Housing ', 'EA', 0, '193', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('62001', 'Machine Casting ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('62002', 'Valve Stop ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('62003', 'Seal Discrete PO', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('62050', 'Beryllium Copper Discrete PO', 'rl', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('62060', 'Aluminum Bronze ', 'M', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('62200', 'Actuator ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('62201', '24V Amp 2HP, 1/2 Dia ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('62202', 'Pem Nut #6 .0125 ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('62203', 'Stud Press Fit #6 1/4\" ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('62221', '24V Amp, 1HP,  Dia ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('62250', 'Steel CRS 18ga 4301 ', 'LB', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('62290', 'Tool Forming Die Motor Assembly', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('62291', 'Tool Forming Die  Life ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('62301', 'Sm Actuator1 Discrete PO', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('62302', 'Sm Actuator2 Discrete PO', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('62303', 'Sm Actuator3 Discrete PO', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('62304', 'Sm Actuator4 Discrete PO', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('62305', 'Sm Actuator5 Discrete PO', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('62306', 'Sm Actuator6 Discrete PO', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('62307', 'Sm Actuator7 Discrete PO', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('62308', 'Sm Actuator8 Discrete PO', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('62500', '8 Way Switch ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('62510', '6 Way Switch ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('62520', '4 Way Switch ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('63001', 'Flat Head Screw Supplier Schedules', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('63002', 'Washer 1/4\" Floor Stock', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('63003', 'Nut - Med Gauge Purchased Discrete', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('63004', 'Washer -1/8\" Discrete PO', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('63005', 'Nut - Fine Gauge Discrete PO', 'EA', 0, '200', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('70001', 'Disinfectant Medical Grade', 'L', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('70002', 'Disinfectant, Scented ', 'L', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('70003', 'Disinfectant, Unscented ', 'L', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('70004', 'Lubricant ', 'L', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('70005', 'Anesthetic Gel ', 'L', 0, '040', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('70010', '5ml tube Gel Anesthetic', 'EA', 0, '040', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('70011', '15ml tube Gel Anesthetic', 'EA', 0, '040', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('70012', '25ml tube Gel Anesthetic', 'EA', 0, '040', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('70040', 'Fruit Juice (unpackaged) ', 'L', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('70050', 'Pills ', 'EA', 0, '050', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('70110', 'Hydration E Tablet ', 'KG', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('70120', 'Salt Pills ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('70210', 'Extra Virgin Olive Oil ', 'HL', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('70400', 'Grade A Oranges Washed and Sorted', 'LB', 0, '040', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('70400BP', 'Grade A Juice Process Base Process', 'L', 0, '', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('70440', 'Grade A Juice No Pulp Co-Product', 'L', 0, '040', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('70441', 'Grade A Juice With Pulp Co-Product', 'L', 0, '040', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('70445', 'Grade A Concentrate ', 'L', 0, '040', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('70445BP', 'Grade A Concentrate Proc Base Process', 'L', 0, '', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('70500', 'Grade B Oranges Washed and Sorted', 'LB', 0, '040', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('70500BP', 'Grade B Juice  Process Base Process', 'L', 0, '', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('70540', 'Grade B Juice No Pulp Co-Product', 'L', 0, '040', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('70541', 'Grade B Juice With Pulp Co-Product', 'L', 0, '040', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('70545', 'Grade B Concentrate ', 'L', 0, '040', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('70545BP', 'Grade B Concentrate Base Process', 'L', 0, '', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('79000', 'Rejected Oranges By-Product', 'LB', 0, '040', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('79001', 'Peel & Seeds By-Product', 'LB', 0, '040', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('79002', 'Filtered Pulp By-Product', 'LB', 0, '040', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80001', 'Quaternary Amonium Chloride', 'G', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80002', 'Biguanide Compound ', 'G', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80003', 'Phenolic ', 'ML', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80010', 'Distilled Water Liquid', 'GA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80011', 'Purified Water Liquid', 'GA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80012', 'Unfiltered Water Liquid', 'L', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80020', 'Fragrance ', 'ML', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80021', 'Green Colorant ', 'GA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80031', 'Ethanol Liquid', 'L', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80032', 'Glycerin Liquid', 'L', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80033', 'Jojoba Oil Liquid', 'L', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80034', 'Aloe Vera Oil Liquid', 'L', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80035', 'Glyceryl Monoiaurate Liquid', 'L', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80036', 'Benzyl Alcohol Liquid', 'L', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80037', 'EDTA Liquid', 'L', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80038', 'Phenoxyethanol Liquid', 'L', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80039', 'Linum usitatissimum (Flax Extract) Liquid', 'L', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80040', 'Lidocaine Liquid', 'L', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80041', 'Caromer 980 Powder/Thickening Agent', 'G', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80042', 'Potassium Sorbate Powder', 'G', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80043', 'Ceratonia Siliquis (Locust Bean Gum) Powder', 'G', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80044', 'Xanthan Gum Powder', 'G', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80045', 'Cyamposis Tetragonolobus (Guar Gum) Powder', 'G', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80046', 'Citric Acid Powder', 'G', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80050', 'Fruit ', 'LB', 0, '040', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80051', 'Proprietary Spice Mix ', 'G', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80052', 'Sterlized Water Liquid', 'L', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80053', 'Preservative ', 'ML', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80060', 'Simethicone ', 'G', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80061', 'Compressable Sugar ', 'G', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80062', 'Dextrose ', 'ML', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80063', 'Flavor ', 'ML', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80064', 'Magnesium Stearate ', 'G', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80065', 'Maltodextrin ', 'G', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80066', 'Sorbitol ', 'G', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80103', 'Calcium Carbonate ', 'KG', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80116', 'Magnesium Sulfate ', 'KG', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80124', 'Sodium Bicarbonate ', 'KG', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80126', 'Sodium Carbonate ', 'KG', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80220', 'Olives, Fresh ', 'T', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80450', 'Oranges ', 'LB', 0, '040', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80450BP', 'Sorting Process Base Process', 'LB', 0, '', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80451', 'Calcium Additive', 'G', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80452', 'Purified Water Liquid', 'L', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('80453', 'Vitamins Additive', 'ML', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90010', 'Pump Dispenser .25l ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90011', 'Plastic Bottle .5l ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90012', 'Plastic Bottle 1l ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90013', '4 Liter Tub ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90014', '5 ml Tube ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90015', '15 ml Tube ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90016', '25 ml Tube ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90017', 'Bottle, 50 Size ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90018', 'Bottle, 100 Size ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90019', 'Plastic Bottle, 750 ml ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90020', 'Bottle, 500 Size ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90020W', 'Bottle, 500 Size Customer specific pack', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90030', 'Blister Pack, 6 Tablet ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90031', 'Generic Packaging ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90040', 'Label 150,000 Labels Per Roll', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90050', '25 Gallon Drum ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90051', '50 Gallon Drum ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90070', 'Assortment Box ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90071', 'Multi-Pack, 2 Bottle Box ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90072', 'Multi-Pack, 4 Bottle Box ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90073', 'Box, 2 Blister Pack Size ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90091', 'Standard Shipping Box ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90092', 'Standard Box ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90093', 'Shipping Carton ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90098', 'Returnable Containers ', 'EA', 10, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90099', 'Expendable Containers ', 'EA', 10, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90100', 'Tote ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90101', 'Lid ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90102', 'Divider Box ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90230', 'Bottle, Glass, 500 ml ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90232', 'Bottle, Glass, 750 ml ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90410', 'Cap, 500 ml ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90412', 'Cap, 750 ml ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90418', 'Orange Juice Labels ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90419', 'Orange Juice Container 750 ml', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90420', 'Label Olive Oil 500 ml ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('90422', 'Label Olive Oil 750 ml ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('99010', 'Installation Kit ', 'EA', 0, '190', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('99020', 'Field Sterilization Kit ', 'EA', 0, '190', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('99030', 'Field Diagnostic Kit ', 'EA', 0, '190', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('99040', 'Wall Mount Kit ', 'EA', 0, '190', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('99090', 'Sterilization Kit ', 'EA', 0, '193', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('99091', 'Diagnostic Kit ', 'EA', 0, '193', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('ab', ' ', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('ac', ' ', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('alchohol', ' ', 'EA', 0, '', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('CT20800120', 'front back label playboy ', 'EA', 0, '', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('CT20800130', 'front back label playboy ', 'EA', 0, '', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('DI00000670', 'lakeland lf40/mackam ', 'KG', 0, '', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('DI00001011', 'plastik pail 10 kg ', 'EA', 0, '', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('item a', ' ', 'EA', 0, '010', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('item d', ' ', 'EA', 50, '', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('JH4000000060', 'premix shoe care ', 'KG', 0, '', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('JH503012ID21', 'kiwi paste shoe polish ', 'CM', 0, '', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('JH527011AU10', 'kiwi kids scuff black ', 'BX', 0, '', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('JH527021AU10', 'kiwi kids scuff brown ', 'CM', 0, '', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('JH560010JP20', 'kiwi elite 75ml black ', 'CM', 0, '', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('JS001', 'Toilet Paper ', 'CS', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('JS002', 'Paper Towels ', 'BX', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('JS003', 'Soap ', 'BX', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('JS004', 'Air Freshener ', 'CS', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('JS005', 'Bleach ', 'EA', 0, '020', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('METANOL', ' ', 'EA', 0, '', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('panadol', ' ', 'EA', 0, '', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('PZ05111001MF', 'cb milk bath 7 mlx576 ', 'CM', 0, '', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('QC', 'QC ', 'EA', 0, 'WHFG', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('SO01200002', 'bulk secrets body milk ', 'KG', 0, '', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('t', ' ', 'EA', 0, '', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('TEST', 'Test Item ', 'EA', 0, 'WHFG', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('testload', ' ', 'EA', 0, '', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('TP01112002', 'power booster fuel ', 'CM', 0, '', '2020-12-04 15:03:30', '2020-12-05 11:56:19'),
('tpart', 'tess tes1', 'EA', 200, '', '2020-12-04 15:03:30', '2020-12-05 11:56:19');

-- --------------------------------------------------------

--
-- Struktur dari tabel `item_konversi`
--

CREATE TABLE `item_konversi` (
  `id` int(12) NOT NULL,
  `item_code` varchar(24) NOT NULL,
  `um_1` varchar(12) NOT NULL,
  `um_2` varchar(12) NOT NULL,
  `qty_item` int(12) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `item_konversi`
--

INSERT INTO `item_konversi` (`id`, `item_code`, `um_1`, `um_2`, `qty_item`, `created_at`, `updated_at`) VALUES
(1, '141020021', 'BT', 'CB', 50, '2020-12-04 15:06:15', '2020-12-04 15:06:15'),
(2, '141020251', 'CB', 'DS', 12, '2020-12-04 15:06:15', '2020-12-04 15:06:15'),
(3, '', 'T', 'KG', 0, '2020-12-04 15:06:15', '2020-12-04 15:06:15'),
(4, '123', 'CI', 'CF', 1, '2020-12-04 15:06:15', '2020-12-04 15:06:15'),
(5, '141020981', 'DS', 'BX', 80, '2020-12-04 15:06:15', '2020-12-04 15:06:15'),
(6, '90040', 'EA', 'RL', 25000, '2020-12-04 15:06:15', '2020-12-04 15:06:15'),
(7, '141020181', 'PC', 'CB', 480, '2020-12-04 15:06:15', '2020-12-04 15:06:15'),
(8, '141040991', 'SC', 'CB', 960, '2020-12-04 15:06:15', '2020-12-04 15:06:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `retur_dets`
--

CREATE TABLE `retur_dets` (
  `id` int(11) NOT NULL,
  `so_nbr` varchar(8) NOT NULL,
  `so_itemcode` varchar(20) NOT NULL,
  `so_qty` decimal(20,2) NOT NULL,
  `so_line` int(11) NOT NULL,
  `so_status` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `retur_mstrs`
--

CREATE TABLE `retur_mstrs` (
  `so_nbr` varchar(8) NOT NULL,
  `so_cust` varchar(20) NOT NULL,
  `so_shipto` varchar(20) NOT NULL,
  `so_so_awal` varchar(8) NOT NULL,
  `so_status` int(11) NOT NULL,
  `so_price` decimal(20,2) NOT NULL,
  `so_site` varchar(20) NOT NULL,
  `so_remarks` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `rewards`
--

CREATE TABLE `rewards` (
  `id` int(11) NOT NULL,
  `reward_id` varchar(8) NOT NULL,
  `reward_start` int(11) NOT NULL,
  `reward_end` int(11) NOT NULL,
  `reward_total` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `rewards`
--

INSERT INTO `rewards` (`id`, `reward_id`, `reward_start`, `reward_end`, `reward_total`, `created_at`, `updated_at`) VALUES
(72, 'R1000', 0, 50, 30, '2020-10-08 15:26:39', '2020-10-08 15:26:39'),
(73, 'R1000', 51, 80, 50, '2020-10-08 15:26:39', '2020-10-08 15:26:39'),
(74, 'R1000', 81, 100, 100, '2020-10-08 15:26:39', '2020-10-08 15:26:39'),
(75, 'R2000', 0, 50, 0, '2020-10-08 15:46:55', '2020-10-08 15:46:55'),
(76, 'R2000', 51, 100, 50, '2020-10-08 15:46:55', '2020-10-08 15:46:55');

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `role_code` varchar(24) NOT NULL,
  `role_desc` varchar(100) NOT NULL,
  `salesman` varchar(5) NOT NULL DEFAULT 'N',
  `menu_access` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`role_code`, `role_desc`, `salesman`, `menu_access`, `created_at`, `updated_at`) VALUES
('53150', 'yuhuu', 'N', 'MT01MT02MT03MT04MT05MT06MT07MT08MT09MT10MT11TS01TS02TS03TS09TS04TS05TS06TS08TK01TS07', '2020-12-03 03:51:33', '2020-12-03 03:51:33'),
('abesa', 'admin', 'N', 'TS01TS02TS03TS04TS05TS06TS08TK01TS07', '2020-11-10 08:46:06', '2020-11-20 10:14:52'),
('ADDM', 'Admin Pusat', 'Y', 'MT01MT02MT03MT04MT05MT06MT07MT08MT09MT10MT11TS01TS02TS03TS09TS04TS05TS06TS08TK01TS07', '2020-12-01 02:43:18', '2020-12-04 17:46:03'),
('ADM', 'admin', 'N', 'MT01MT02MT03MT04MT05MT06MT07MT08MT09MT10MT11TS03TK01', '2020-11-02 06:45:45', '2020-11-03 07:40:49'),
('role1', 'role1', 'N', 'MT07MT11', '2020-11-04 10:20:26', '2020-11-10 08:37:31'),
('test', 'testing', 'N', 'MT01MT02MT03TS03TK01', '2020-11-03 07:40:31', '2020-11-13 10:33:40'),
('test1', 'abebe', 'N', 'MT01', '2020-11-02 06:46:12', '2020-11-02 06:46:12'),
('testbar', 'testbar', 'N', 'TS01TS02TS03TS04TS05TS06TS08TK01TS07', '2020-11-10 08:38:08', '2020-11-10 08:45:32'),
('teste', 'teste', 'N', 'MT01MT02MT03', '2020-11-03 08:01:29', '2020-11-03 08:01:29'),
('testro1', 'aaaa', '', 'TS01TS02TS03TS09TS04TS05TS06TS08TK01TS07', '2020-12-04 14:34:56', '2020-12-04 17:48:42'),
('testro2', 'bbbbb', 'N', 'TS02', '2020-12-04 14:35:19', '2020-12-04 14:35:19'),
('testroll', '1233', 'Y', '', '2020-12-04 17:42:06', '2020-12-04 17:42:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sales_activity`
--

CREATE TABLE `sales_activity` (
  `id` int(12) NOT NULL,
  `username_sales` varchar(24) DEFAULT NULL,
  `activity_sales` varchar(24) DEFAULT NULL,
  `to_cust` varchar(50) DEFAULT NULL,
  `inout` varchar(24) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `sales_activity`
--

INSERT INTO `sales_activity` (`id`, `username_sales`, `activity_sales`, `to_cust`, `inout`, `created_at`, `updated_at`) VALUES
(1, 'admin', NULL, 'C9102', 'checkout', '2020-11-10 03:19:04', '2020-11-10 03:23:11'),
(2, 'admin', NULL, 'B1032', 'checkout', '2020-11-10 03:23:17', '2020-11-10 03:23:28'),
(3, 'admin', NULL, 'C9102', 'checkout', '2020-11-10 03:24:26', '2020-11-10 03:24:29'),
(4, 'admin', NULL, 'C9102', 'checkout', '2020-11-10 03:27:58', '2020-11-10 03:28:15'),
(5, 'admin', NULL, 'C9102', 'checkout', '2020-11-10 03:28:18', '2020-11-10 03:28:21'),
(6, 'admin', 'test3', 'B1032', 'checkout', '2020-11-10 03:28:26', '2020-11-11 02:55:06'),
(7, 'admin', 'test3', 'B1032', 'checkout', '2020-11-10 03:28:27', '2020-11-11 02:55:06'),
(8, 'admin', NULL, 'B1032', 'checkout', '2020-11-10 03:28:36', '2020-11-10 03:28:42'),
(9, 'admin', 'test3', 'B1032', 'checkout', '2020-11-10 03:31:59', '2020-11-11 02:55:06'),
(10, 'admin', NULL, 'B1032', 'checkout', '2020-11-10 03:32:01', '2020-11-10 03:32:14'),
(11, 'admin', 'test3', 'C9102', 'checkout', '2020-11-10 03:32:22', '2020-11-11 02:55:06'),
(12, 'admin', 'test3', 'C9102', 'checkout', '2020-11-10 03:33:43', '2020-11-11 02:55:06'),
(13, 'admin', 'test3', 'C9102', 'checkout', '2020-11-10 03:34:15', '2020-11-11 02:55:06'),
(14, 'admin', 'test3', 'C9102', 'checkout', '2020-11-10 03:34:48', '2020-11-11 02:55:06'),
(15, 'admin', 'test3', 'C9102', 'checkout', '2020-11-10 03:35:27', '2020-11-11 02:55:06'),
(16, 'admin', NULL, 'C9102', 'checkout', '2020-11-10 03:36:07', '2020-11-10 03:36:12'),
(17, 'admin', 'test3', NULL, 'checkout', '2020-11-10 03:36:23', '2020-11-11 02:55:06'),
(18, 'admin', 'test3', NULL, 'checkout', '2020-11-10 03:36:23', '2020-11-11 02:55:06'),
(19, 'admin', NULL, NULL, 'checkout', '2020-11-10 03:36:24', '2020-11-10 03:36:28'),
(20, 'admin', 'test3', NULL, 'checkout', '2020-11-10 03:36:33', '2020-11-11 02:55:06'),
(21, 'admin', NULL, NULL, 'checkout', '2020-11-10 03:42:45', '2020-11-10 03:55:47'),
(22, 'admin', NULL, 'C9102', 'checkout', '2020-11-10 03:55:50', '2020-11-10 03:55:55'),
(23, 'admin', NULL, 'B1032', 'checkout', '2020-11-10 03:55:59', '2020-11-10 03:56:03'),
(24, 'admin', NULL, 'P1019', 'checkout', '2020-11-10 03:56:06', '2020-11-10 03:59:19'),
(25, 'admin', NULL, NULL, 'checkout', '2020-11-10 03:59:22', '2020-11-10 03:59:24'),
(26, 'admin', NULL, NULL, 'checkout', '2020-11-10 03:59:50', '2020-11-10 04:38:07'),
(27, 'admin', 'test3', 'A1122', 'checkout', '2020-11-11 02:55:02', '2020-11-11 02:55:06'),
(28, 'admin', NULL, 'B1032', 'checkout', '2020-11-12 04:59:50', '2020-11-12 05:00:14'),
(29, 'admin', NULL, 'B1032', 'checkout', '2020-11-12 05:00:11', '2020-11-12 05:00:14'),
(30, 'admin', 'test3', 'P1019', 'checkout', '2020-11-12 05:23:10', '2020-11-12 05:23:25'),
(31, 'admin', 'test3', 'A1122', 'checkout', '2020-11-12 05:24:32', '2020-11-12 05:24:36'),
(32, 'admin', 'test3', 'A1122', 'checkout', '2020-11-12 05:24:52', '2020-11-12 05:24:55'),
(33, 'admin', 'test3', 'A1122', 'checkout', '2020-11-12 06:42:42', '2020-11-12 06:42:46'),
(34, 'a', 'DED', 'C9102', 'checkout', '2020-11-24 03:09:45', '2020-11-24 03:10:05'),
(35, 'admin', 'DED', 'B1032', 'checkout', '2020-11-24 03:09:59', '2020-11-24 03:10:30'),
(36, 'admin', 'A1122', '10C1000', 'checkout', '2020-12-03 07:26:45', '2020-12-03 07:26:49'),
(37, 'admin', 'DED', '10C1000', 'checkout', '2020-12-03 08:19:23', '2020-12-03 08:20:02'),
(38, 'adm1', 'DED', '10C1000', 'checkout', '2020-12-04 14:29:25', '2020-12-04 14:29:29'),
(39, 'adm1', 'DED', '10C1000', 'checkout', '2020-12-04 14:29:32', '2020-12-04 14:29:56');

-- --------------------------------------------------------

--
-- Struktur dari tabel `site_mstrs`
--

CREATE TABLE `site_mstrs` (
  `site_code` varchar(24) NOT NULL,
  `site_desc` varchar(24) NOT NULL,
  `pusat_cabang` int(11) DEFAULT NULL,
  `site_flag` varchar(11) DEFAULT NULL,
  `r_nbr_eod` varchar(10) NOT NULL DEFAULT '0000',
  `r_nbr_aut` varchar(10) NOT NULL DEFAULT '0000',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `site_mstrs`
--

INSERT INTO `site_mstrs` (`site_code`, `site_desc`, `pusat_cabang`, `site_flag`, `r_nbr_eod`, `r_nbr_aut`, `created_at`, `updated_at`) VALUES
('10-100', 'Ultrasound Mfg Site', 0, 'Y', '0000', '0000', '2020-12-05 11:56:32', '2020-12-05 11:59:19'),
('10-200', 'Automotive Mfg', 0, NULL, '0000', '0000', '2020-12-05 11:56:32', '2020-12-05 11:56:32'),
('10-201', 'Lean Manufacturing Site', 0, NULL, '0000', '0000', '2020-12-05 11:56:32', '2020-12-05 11:56:32'),
('10-202', 'Automotive Mfg Site 2', 0, NULL, '0000', '0000', '2020-12-05 11:56:32', '2020-12-05 11:56:32'),
('10-300', 'Process Mfg Site', 0, NULL, '0000', '0000', '2020-12-05 11:56:32', '2020-12-05 11:56:32'),
('10-301', 'Distribution Site 1', 0, NULL, '0000', '0000', '2020-12-05 11:56:32', '2020-12-05 11:56:32'),
('10-302', 'Distribution Site 2', 0, NULL, '0000', '0000', '2020-12-05 11:56:32', '2020-12-05 11:56:32'),
('10-303', 'Distribution Site 3', 0, NULL, '0000', '0000', '2020-12-05 11:56:32', '2020-12-05 11:56:32'),
('10-400', 'Food & Bev Mfg Site', 0, NULL, '0000', '0000', '2020-12-05 11:56:32', '2020-12-05 11:56:32'),
('10-500', 'Pharmaceutical Mfg Site', 0, NULL, '0000', '0000', '2020-12-05 11:56:32', '2020-12-05 11:56:32'),
('11-100', 'Ultrasound Mfg Site', 0, NULL, '0000', '0000', '2020-12-05 11:56:32', '2020-12-05 11:56:32'),
('12-100', 'Ultrasound Mfg Site', 0, NULL, '0000', '0000', '2020-12-05 11:56:32', '2020-12-05 11:56:32'),
('20-100', 'Ultrasound Mfg Site', 0, NULL, '0000', '0000', '2020-12-05 11:56:32', '2020-12-05 11:56:32'),
('21-100', 'Ultrasound Mfg Site', 0, NULL, '0000', '0000', '2020-12-05 11:56:32', '2020-12-05 11:56:32'),
('22-100', 'Ultrasound Mfg Site', 0, NULL, '0000', '0000', '2020-12-05 11:56:32', '2020-12-05 11:56:32'),
('30-100', 'Ultrasound Mfg Site', 0, NULL, '0000', '0000', '2020-12-05 11:56:32', '2020-12-05 11:56:32'),
('31-100', 'Ultrasound Mfg Site', 0, NULL, '0000', '0000', '2020-12-05 11:56:32', '2020-12-05 11:56:32'),
('PHSMG', 'PHAPROS', 0, NULL, '0000', '0000', '2020-12-05 11:56:32', '2020-12-05 11:56:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `so_dets`
--

CREATE TABLE `so_dets` (
  `id` int(11) NOT NULL,
  `so_nbr` varchar(8) NOT NULL,
  `so_itemcode` varchar(20) NOT NULL,
  `so_qty` decimal(11,2) NOT NULL,
  `so_line` int(11) NOT NULL,
  `so_qty_open` int(11) NOT NULL,
  `so_um` varchar(2) NOT NULL,
  `so_status` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `so_dets`
--

INSERT INTO `so_dets` (`id`, `so_nbr`, `so_itemcode`, `so_qty`, `so_line`, `so_qty_open`, `so_um`, `so_status`, `created_at`, `updated_at`) VALUES
(1, '11000001', '01040-007', '3242.00', 1, 3242, 'EA', 1, '2020-12-04 22:08:20', '2020-12-04 22:08:20'),
(2, '11000001', '01040-005', '34.00', 2, 34, 'EA', 1, '2020-12-04 22:08:20', '2020-12-04 22:08:20'),
(3, '11000001', '01060', '324342.00', 3, 324342, 'EA', 1, '2020-12-04 22:08:20', '2020-12-04 22:08:20'),
(4, '10000001', 'ab', '15.00', 1, 15, 'EA', 1, '2020-12-04 22:08:36', '2020-12-04 22:08:36'),
(5, '11000002', 'ab', '11.00', 1, 5, 'EA', 1, '2020-12-04 23:48:19', '2020-12-04 23:48:19'),
(6, '11000003', '01040-003', '12.00', 1, 12, 'EA', 1, '2020-12-05 17:26:45', '2020-12-05 17:26:45'),
(7, '11000003', '01040-005', '12.00', 2, 12, 'EA', 1, '2020-12-05 17:26:45', '2020-12-05 17:26:45'),
(8, '11000004', '01011', '12.00', 1, 12, 'EA', 1, '2020-12-05 18:22:51', '2020-12-05 18:22:51'),
(9, '11000004', '01012', '12.00', 2, 12, 'BX', 1, '2020-12-05 18:22:51', '2020-12-05 18:22:51');

-- --------------------------------------------------------

--
-- Struktur dari tabel `so_mstrs`
--

CREATE TABLE `so_mstrs` (
  `so_nbr` varchar(8) NOT NULL,
  `so_cust` varchar(20) NOT NULL,
  `so_duedate` date NOT NULL,
  `so_shipto` varchar(20) NOT NULL,
  `so_notes` varchar(50) DEFAULT NULL,
  `so_status` int(11) NOT NULL,
  `so_price` decimal(18,2) DEFAULT NULL,
  `so_site` varchar(20) NOT NULL,
  `so_user` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `so_mstrs`
--

INSERT INTO `so_mstrs` (`so_nbr`, `so_cust`, `so_duedate`, `so_shipto`, `so_notes`, `so_status`, `so_price`, `so_site`, `so_user`, `created_at`, `updated_at`) VALUES
('10000001', '10C1000', '2021-03-04', '10C1000', 'Cabai', 1, '248339301.02', '10-200', '', '2020-12-04 22:08:36', '2020-12-04 22:08:36'),
('11000001', '10C1003', '2020-12-14', '10C1003', NULL, 3, '2697545809.80', '11-100', '', '2020-12-04 22:08:20', '2020-12-04 22:08:20'),
('11000002', '10C1000', '2021-03-05', '10C1000', 'Tomat', 1, '19800.00', '11-100', 'adm1', '2020-12-04 23:48:19', '2020-12-04 23:48:19'),
('11000003', '10C1005', '2021-03-17', '10C1005', 'ini toko', 1, '178050.60', '11-100', 'adm1', '2020-12-05 17:26:45', '2020-12-05 17:26:45'),
('11000004', '10C1000', '2021-03-16', '10C1000', 'asa', 1, '1155.60', '11-100', 'adm1', '2020-12-05 18:22:51', '2020-12-05 18:22:51');

-- --------------------------------------------------------

--
-- Struktur dari tabel `so_temp`
--

CREATE TABLE `so_temp` (
  `so_nbr` varchar(10) NOT NULL,
  `so_cust` varchar(10) NOT NULL,
  `so_custname` varchar(200) DEFAULT NULL,
  `so_duedate` date NOT NULL,
  `so_line` int(11) NOT NULL,
  `so_itemcode` varchar(20) NOT NULL,
  `so_itemdesc` varchar(50) NOT NULL,
  `so_um` varchar(10) NOT NULL,
  `so_site` varchar(255) NOT NULL,
  `so_shipto` varchar(255) NOT NULL,
  `so_shipdesc` varchar(255) DEFAULT NULL,
  `so_qtyso` decimal(11,0) NOT NULL,
  `so_qtyopen` decimal(11,0) NOT NULL,
  `so_qtyd` decimal(10,0) NOT NULL,
  `so_user` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `so_temp`
--

INSERT INTO `so_temp` (`so_nbr`, `so_cust`, `so_custname`, `so_duedate`, `so_line`, `so_itemcode`, `so_itemdesc`, `so_um`, `so_site`, `so_shipto`, `so_shipdesc`, `so_qtyso`, `so_qtyopen`, `so_qtyd`, `so_user`) VALUES
('11000004', '10C1000', 'LTZ Retail', '2021-03-16', 1, '01011', 'Supplies Kit ', 'EA', '11-100', '10C1000', NULL, '12', '12', '0', 'adm1'),
('11000004', '10C1000', 'LTZ Retail', '2021-03-16', 2, '01012', 'Sterile Probe Covers, 20 One time use', 'BX', '11-100', '10C1000', NULL, '12', '12', '0', 'adm1');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sub_groups`
--

CREATE TABLE `sub_groups` (
  `id` varchar(12) NOT NULL,
  `groupid` varchar(12) NOT NULL,
  `description` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `sub_groups`
--

INSERT INTO `sub_groups` (`id`, `groupid`, `description`) VALUES
('SGR001', 'GR0002', 'Sub Group Pestisida'),
('SGR002', 'GR0002', 'Sub Group Pestisida 2');

-- --------------------------------------------------------

--
-- Struktur dari tabel `supp_mstrs`
--

CREATE TABLE `supp_mstrs` (
  `supp_code` varchar(25) NOT NULL,
  `supp_desc` varchar(25) NOT NULL,
  `supp_telepon` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `supp_mstrs`
--

INSERT INTO `supp_mstrs` (`supp_code`, `supp_desc`, `supp_telepon`, `created_at`, `updated_at`) VALUES
('10-200', 'QMI - USA Division', '201-233-5461', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('10-300', 'QMI - USA Division', '201-233-5461', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('10L1000', 'Keuhne & Nagel', '1-201-413-5500', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('10L1001', 'UTi', '1-310-783-5020', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('10L1002', 'CGI', '303-987-6543', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('10PLATSP', 'Plating Subcontractor - U', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('10S1001', 'Taylor & Fulton Fruit Co.', '941-555-0101', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('10S1002', 'Bridgeville Industries', '269-555-0101', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('10S1003', 'Heron Surgical Supply', '718-555-0102', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('10S1004', 'Sungro Chemicals', '213-555-0101', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('10S1005', 'Absolute Electronics Comp', '973-5550100', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('10S1006', 'Hampton Electronics', '561-555-0102', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('10S1010', 'Eldon Motor Co.', '(973) 555-1202', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('10S1011', 'HTZ Switch Co.', '(973) 123-4567', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('10S1012', 'J&P Metalware', '(908) 555-1201', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('10S2000', 'J. Williams & Company', '973-555-0103', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('10S2001', 'Ischinger & Marken', '212-555-0102', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('10S2002', 'Lee Sheldon', '862-555-0103', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('10S3002', 'Bridgeville Industries', '269-555-0101', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('10S3004', 'Sungro Chemicals', '213-555-0101', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('10SC1005', 'Rockland Industrial Compa', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('10SUBCT', 'Subcontract Supplier - US', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('11-300', 'QMI - Canada Division', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('11PLATSP', 'Plating Subcontractor - C', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('11S1000', 'Chiro Foods limited', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('11S1001', 'Plastics of Oshawa', '780-555-0101', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('11S1111', 'Chiro Foods limited', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('11SUBCT', 'Subcontract Supplier - CA', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('12-100', 'QMI-Mexico Division', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('12-300', 'QMI-Mexico Division', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('12PLATSP', 'Plating Subcontractor - M', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('12S1001', 'Packaging Components Ltd.', '52-55-3412-7856', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('12S1002', 'Mexico City Chemicals', '23 879-5556', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('12S1003', 'Puerto Vallarta Surgical ', '52-322-3412-7856', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('12SUBCT', 'Subcontract Supplier - MX', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('20-300', 'QMI-France Division', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('20PLATSP', 'Plating Subcontractor - F', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('20S1001', 'Paris Fruit Products', '33-1-0975-8531', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('20S1002', 'Pharmaceutical Chemical S', '33-15536-0101', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('20S1003', 'Containers Ltd', '33-1-0975-0101', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('20SUBCT', 'Subcontract Supplier - FR', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('21-300', 'QMI-Netherlands Division', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('21PLATSP', 'Plating Subcontractor - N', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('21S1001', 'Power Cord International', '31-512-520-111', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('21S1002', 'Van es Surgical Supply', '31-255-100-293', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('21S1003', 'Babberich Electronics', '31-316-631-101', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('21S1004', 'Servizi di Contabilitia M', '39-02-111029', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('21S2001', 'Ospedale S. Raffaele', '39-02-212023', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('21S2002', 'Studi Legali Riuniti', '39-06-111602', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('21S2003', 'Rafmil-FRA Division', '33-4-28-01-02-99', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('21S2004', 'Rafmil-ZA Division', '32-05-64-1134', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('21SEPASU', '21-SEPA-SU', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('21SUBCT', 'Subcontract Supplier - NL', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('22-300', 'QMI-UK Division', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('22PLATSP', 'Plating Subcontractor - U', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('22S1000', 'Auto-Plas International', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('22S1001', 'Cheshire Packaging Produc', '44-844-187-1102', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('22S1002', 'Organic Food Supply', '44-871-1-023-1348', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('22SUBCT', 'Subcontract Supplier - UK', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('23-300', 'QMI German Division', '+49 69/788985', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('23PLATSP', 'Plating Subcontractor - G', '+49 841/988547', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('23S1000', 'Leiferant A', '+ 49 351/213941', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('23S1001', 'BGS Innovation AG', '+49 841/987552', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('23S1002', 'Huber Meallwaren', '+49 69/788985', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('23SUBCT', 'Subcontract Supplier - GE', '+49 221/566489', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('30-100', 'QMI-China Division', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('30-300', 'QMI-China Division', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('30PLATSP', 'Plating Subcontractor - C', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('30S1001', 'Xia Electronics', '86-20-3101-9866', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('30S1002', 'Tabu Power Cord Co', '+86 571-5555-0103', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('30S1003', 'Tanaka Surgical Ltd.', '86+21-3867-1111', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('30S1004', 'Shanghai Chemical Mfg', '86-21-3801-8888', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('30SUBCT', 'Subcontract Supplier - CN', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('31-300', 'QMI-Australia Division', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('31PLATSP', 'Plating Subconractor - AU', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('31S1001', 'Australian Fruit Products', '63-3-9311-1000', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('31S1002', 'Sydney Copper Company', '61-2-8201-9999', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('31S1003', 'Henderson Industrial', '61-2-8261-5555', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('31SUBCT', 'Subcontract Supplier - AU', '', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('32S1000', 'Benson Motors', '81-3-3203-1100', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('32S1001', 'CAN Enterprises', '81-3-3203-1444', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('40-300', 'QMI - Brazil Division', '33319700', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('40L1000', 'Transportadora Maua SA', '11 3529 4545', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('40PLATSP', 'Plating Subcontractor - B', '11 3529 4545', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('40S1000', 'Lojas Americanas', '11 3899 4545', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('40S1001', 'ABC Componentes Electroni', '11 3529 4545', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('40S1002', 'Tintas Coloridas SA', '11 3529 4545', '2020-12-04 15:03:48', '2020-12-04 15:03:48'),
('40SUBCT', 'Subcontract Supplier - BR', '11 3529 4545', '2020-12-04 15:03:48', '2020-12-04 15:03:48');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `username` varchar(6) CHARACTER SET utf8mb4 NOT NULL,
  `name` varchar(24) CHARACTER SET utf8mb4 NOT NULL,
  `role_user` varchar(24) CHARACTER SET utf8mb4 NOT NULL,
  `site` varchar(24) CHARACTER SET utf8mb4 NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `session_id` text CHARACTER SET utf8mb4 DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `role_user`, `site`, `password`, `session_id`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Andrew Conan', 'ADDM', '10-100', '$2y$10$kgz/uzuXwXjDk4Ud.a/6CuhTk5foxvfGRve0B4A8z74548JnW35WW', 'ZcOHivweCuGeUymO1RjqPA6XZdFbhHIZ09InRaGX', '2020-08-12 20:39:30', '2020-12-05 10:04:24'),
(18, 'a', 'a', 'ADDM', '10-200', '$2y$10$fRkhd5taEVAjeEcGg00zneiZNkQ6AzFnAkPFo7e1LQylE4nZ0bOFm', 'qWMKVVG2jmxwtlOjhOFM3ZLX2BwIEI1GtSJp0EyD', '2020-11-04 07:59:46', '2020-12-05 12:34:09'),
(19, 'b', 'cd', 'ADDM', '10-100', '$2y$10$VcJ0E7lHN.FCNmusK2nNxOSbBLq5g2PmDBNOyEjcntWX8mu4Ea94K', 'AU2yFf8jnPHOlzieejeFbrwGqvTdwpWL8UXqDypO', '2020-11-04 08:01:21', '2020-12-05 10:05:51'),
(20, 'abebe', '321', 'test', 'R0012', '$2y$10$IJDF8my7MKoXIwpp2BxHTe8.kU0ou5UMWyfwlmxktMg/XPoqMNfQy', 'htarN14hjwouS8MSXJGq26LAq3wxhlpTTNHICdjC', '2020-11-04 08:39:31', '2020-11-06 02:39:44'),
(21, 'test', 'test', 'role1', 'R0012', '$2y$10$EZbzYQ8Z12zFXa6SJSHKAe5fNXtGMeViUu8CLh6.C5.poSdLr17xK', 'b1EMF8C6fYlec22Ne1HOAuuFXiMTSQb0eok0kTAk', '2020-11-04 10:18:53', '2020-11-04 10:19:08'),
(22, 'Tommy', 'Tom', 'testro1', '11-100', '$2y$10$mM/wwch9eQm4c7upRm.NVuTUXXbx6EqVpzXWWkGEikoDZ6Kzi/Sse', 'fP8iXGSaMyWm7DAWYs2XVbBkM5W3suluZbGipeuF', '2020-12-04 14:28:07', '2020-12-05 11:29:40'),
(23, 'adm1', 'adm1', 'ADDM', '11-100', '$2y$10$U4fXNYKJcCurhVeykmFatuh7hmpd9jXAhnZHO.tiLfIFmMiiGLl0C', 'ieUnpxtyfDAvohGBrdvJ2N5gIJQHlIMFH5sTfqTP', '2020-12-04 14:28:38', '2020-12-05 09:50:05'),
(24, 'SADjkt', 'ray', 'abesa', '10-301', '$2y$10$MmbKaN3j1X9AQ2jit2UMz.zk7DDXule2fIbp//ERx0e9FcIi3FQ8O', NULL, '2020-12-05 10:41:06', '2020-12-05 10:41:06');

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
(4469, '', 'T5', '10L1001', 1, 'T5', 'tes125t text123', 'EA', '1.00', NULL, NULL, '0.00', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0000-00-00', 'UnConfirm', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(4470, '', 'pox', '10-200', 1, 'pox', 'tes125t text123', 'EA', '234.00', NULL, NULL, '0.00', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0000-00-00', 'UnConfirm', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(4471, '', 'pox', '10-200', 1, 'pox', 'tes125t text123', 'EA', '234.00', NULL, NULL, '0.00', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0000-00-00', 'UnConfirm', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(4502, '', '000014', '10-200', 1, '000014', ' ', 'EA', '54.00', NULL, NULL, '0.00', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0000-00-00', 'UnConfirm', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(4503, '', '000014', '10-200', 2, '000014', ' ', 'EA', '44.00', NULL, NULL, '0.00', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0000-00-00', 'UnConfirm', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(4504, '', '000014', '10-200', 3, '000014', ' ', 'EA', '34.00', NULL, NULL, '0.00', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0000-00-00', 'UnConfirm', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(4513, '', '000004', '10-200', 1, '000004', 'tes125t text123', 'EA', '5.00', NULL, NULL, '0.00', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0000-00-00', 'UnConfirm', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(4514, '', '000004', '10-200', 2, '000004', ' ', 'EA', '3.00', NULL, NULL, '0.00', '0.00', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0000-00-00', 'UnConfirm', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `activity`
--
ALTER TABLE `activity`
  ADD PRIMARY KEY (`activity_code`);

--
-- Indeks untuk tabel `approvals`
--
ALTER TABLE `approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userID` (`userid`);

--
-- Indeks untuk tabel `approval_hist`
--
ALTER TABLE `approval_hist`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `approval_tmp`
--
ALTER TABLE `approval_tmp`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`cust_code`);

--
-- Indeks untuk tabel `customers_type`
--
ALTER TABLE `customers_type`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `cust_relation`
--
ALTER TABLE `cust_relation`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `cust_shipto`
--
ALTER TABLE `cust_shipto`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `dod_det`
--
ALTER TABLE `dod_det`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `do_mstr`
--
ALTER TABLE `do_mstr`
  ADD PRIMARY KEY (`do_nbr`);

--
-- Indeks untuk tabel `endofday`
--
ALTER TABLE `endofday`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`itemcode`);

--
-- Indeks untuk tabel `item_konversi`
--
ALTER TABLE `item_konversi`
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
-- Indeks untuk tabel `retur_dets`
--
ALTER TABLE `retur_dets`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `retur_mstrs`
--
ALTER TABLE `retur_mstrs`
  ADD PRIMARY KEY (`so_nbr`);

--
-- Indeks untuk tabel `rewards`
--
ALTER TABLE `rewards`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_code`);

--
-- Indeks untuk tabel `sales_activity`
--
ALTER TABLE `sales_activity`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `site_mstrs`
--
ALTER TABLE `site_mstrs`
  ADD PRIMARY KEY (`site_code`);

--
-- Indeks untuk tabel `so_dets`
--
ALTER TABLE `so_dets`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `so_mstrs`
--
ALTER TABLE `so_mstrs`
  ADD PRIMARY KEY (`so_nbr`);

--
-- Indeks untuk tabel `sub_groups`
--
ALTER TABLE `sub_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `groupid` (`groupid`);

--
-- Indeks untuk tabel `supp_mstrs`
--
ALTER TABLE `supp_mstrs`
  ADD PRIMARY KEY (`supp_code`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `username_2` (`username`);

--
-- Indeks untuk tabel `xpod_dets`
--
ALTER TABLE `xpod_dets`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `approvals`
--
ALTER TABLE `approvals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT untuk tabel `approval_hist`
--
ALTER TABLE `approval_hist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `approval_tmp`
--
ALTER TABLE `approval_tmp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `customers_type`
--
ALTER TABLE `customers_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `cust_relation`
--
ALTER TABLE `cust_relation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `cust_shipto`
--
ALTER TABLE `cust_shipto`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `dod_det`
--
ALTER TABLE `dod_det`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `endofday`
--
ALTER TABLE `endofday`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `item_konversi`
--
ALTER TABLE `item_konversi`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `retur_dets`
--
ALTER TABLE `retur_dets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `rewards`
--
ALTER TABLE `rewards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT untuk tabel `sales_activity`
--
ALTER TABLE `sales_activity`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT untuk tabel `so_dets`
--
ALTER TABLE `so_dets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `xpod_dets`
--
ALTER TABLE `xpod_dets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4515;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
