-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 20, 2026 at 07:44 AM
-- Server version: 8.0.30
-- PHP Version: 8.2.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `scm_konstruksi_ta`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('15756cf20e856d2c475dca87a38c5da5a70b3dc1', 'i:1;', 1776173181),
('15756cf20e856d2c475dca87a38c5da5a70b3dc1:timer', 'i:1776173181;', 1776173181),
('ac6b3a69ffd41b82ddb4213defe7cf47be121c04', 'i:1;', 1776535471),
('ac6b3a69ffd41b82ddb4213defe7cf47be121c04:timer', 'i:1776535471;', 1776535471),
('timproyek@scm.com|127.0.0.1', 'i:1;', 1776173622),
('timproyek@scm.com|127.0.0.1:timer', 'i:1776173622;', 1776173622);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_kontrak`
--

CREATE TABLE `detail_kontrak` (
  `id_detail_kontrak` varchar(20) NOT NULL,
  `id_kontrak` varchar(20) NOT NULL,
  `id_material` varchar(20) NOT NULL,
  `jumlah_final` int NOT NULL,
  `harga_negosiasi_satuan` decimal(15,2) NOT NULL,
  `jumlah_diterima` int DEFAULT '0',
  `catatan_penerimaan` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_kontrak`
--

INSERT INTO `detail_kontrak` (`id_detail_kontrak`, `id_kontrak`, `id_material`, `jumlah_final`, `harga_negosiasi_satuan`, `jumlah_diterima`, `catatan_penerimaan`, `created_at`, `updated_at`) VALUES
('DKO0001', 'KON0001', 'MAT0001', 150, 50000.00, 0, NULL, '2026-03-14 03:10:33', '2026-03-14 03:10:33'),
('DKO0002', 'KON0001', 'MAT0003', 100, 20000.00, 0, NULL, '2026-03-14 03:10:33', '2026-03-14 03:10:33'),
('DKO0003', 'KON0002', 'MAT0001', 10, 50000.00, 0, NULL, '2026-03-14 03:32:55', '2026-03-14 03:32:55'),
('DKO0004', 'KON0002', 'MAT0002', 50, 40000.00, 0, NULL, '2026-03-14 03:32:55', '2026-03-14 03:32:55'),
('DKO0005', 'KON0003', 'MAT0001', 10, 50000.00, 0, NULL, '2026-03-28 04:06:45', '2026-03-28 04:06:45'),
('DKO0006', 'KON0004', 'MAT0007', 100, 50000.00, 0, NULL, '2026-03-31 02:24:59', '2026-03-31 02:24:59'),
('DKO0007', 'KON0005', 'MAT0001', 100000, 100000.00, 0, NULL, '2026-03-31 21:21:26', '2026-03-31 21:21:26'),
('DKO0008', 'KON0006', 'MAT0008', 50, 100000.00, 25, NULL, '2026-03-31 21:42:48', '2026-03-31 21:42:48'),
('DKO0009', 'KON0007', 'MAT0010', 100, 50000.00, 95, NULL, '2026-04-06 06:17:04', '2026-04-06 06:17:04'),
('DKO0010', 'KON0008', 'MAT0012', 10, 30000.00, 10, NULL, '2026-04-07 20:37:05', '2026-04-07 20:37:05'),
('DKO0011', 'KON0009', 'MAT0001', 10, 20000.00, 0, NULL, '2026-04-07 23:32:47', '2026-04-07 23:32:47'),
('DKO0012', 'KON0010', 'MAT0004', 50, 35000.00, 50, NULL, '2026-04-13 12:34:55', '2026-04-13 12:34:55'),
('DKO0013', 'KON0010', 'MAT0001', 50, 40000.00, 50, NULL, '2026-04-13 12:34:55', '2026-04-13 12:34:55'),
('DKO0014', 'KON0011', 'MAT0003', 10, 29999.00, 0, NULL, '2026-04-14 03:13:46', '2026-04-14 03:13:46'),
('DKO0015', 'KON0012', 'MAT0016', 1, 30000.00, 1, NULL, '2026-04-14 06:12:10', '2026-04-14 06:12:10'),
('DKO0016', 'KON0013', 'MAT0011', 20, 30000.00, 20, NULL, '2026-04-18 11:02:46', '2026-04-18 11:02:46'),
('DKO0017', 'KON0013', 'MAT0016', 15, 20000.00, 15, NULL, '2026-04-18 11:02:46', '2026-04-18 11:02:46');

-- --------------------------------------------------------

--
-- Table structure for table `detail_penerimaan`
--

CREATE TABLE `detail_penerimaan` (
  `id_detail_terima` varchar(50) NOT NULL,
  `id_penerimaan` varchar(50) NOT NULL,
  `id_pengiriman_detail` varchar(50) NOT NULL,
  `id_detail_kontrak` varchar(20) NOT NULL,
  `jumlah_bagus` int DEFAULT '0',
  `jumlah_rusak` int DEFAULT '0',
  `alasan_return` text,
  `foto_bukti_rusak` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_penerimaan`
--

INSERT INTO `detail_penerimaan` (`id_detail_terima`, `id_penerimaan`, `id_pengiriman_detail`, `id_detail_kontrak`, `jumlah_bagus`, `jumlah_rusak`, `alasan_return`, `foto_bukti_rusak`, `created_at`, `updated_at`) VALUES
('DTR260314101229654', 'TRM0001', 'KRD0001', 'DKO0001', 100, 0, '', NULL, '2026-03-14 03:12:29', '2026-03-14 03:12:29'),
('DTR260314101229980', 'TRM0001', 'KRD0002', 'DKO0002', 100, 0, '', NULL, '2026-03-14 03:12:29', '2026-03-14 03:12:29'),
('DTR260314101304544', 'TRM0002', 'KRD0003', 'DKO0001', 40, 10, '', 'bukti-retur/SPT1PEOJyaEchTbtXSvnpd7Ac0J4hm11QK07nf7F.jpg', '2026-03-14 03:13:04', '2026-03-14 03:13:04'),
('DTR260314101505617', 'TRM0003', 'KRD0004', 'DKO0001', 10, 0, '', NULL, '2026-03-14 03:15:05', '2026-03-14 03:15:05'),
('DTR260314103405482', 'TRM0004', 'KRD0006', 'DKO0004', 50, 0, '', NULL, '2026-03-14 03:34:05', '2026-03-14 03:34:05'),
('DTR260314103405751', 'TRM0004', 'KRD0005', 'DKO0003', 10, 0, '', NULL, '2026-03-14 03:34:05', '2026-03-14 03:34:05'),
('DTR260328111206635', 'TRM0005', 'KRD0007', 'DKO0005', 10, 0, '', NULL, '2026-03-28 04:12:06', '2026-03-28 04:12:06'),
('DTR260331093846460', 'TRM0006', 'KRD0008', 'DKO0006', 100, 0, '', NULL, '2026-03-31 02:38:46', '2026-03-31 02:38:46'),
('DTR260401043209543', 'TRM0007', 'KRD0009', 'DKO0007', 100000, 0, '', NULL, '2026-03-31 21:32:09', '2026-03-31 21:32:09'),
('DTR260406095157974', 'TRM0008', 'KRD0010', 'DKO0008', 25, 0, '', NULL, '2026-04-06 02:51:57', '2026-04-06 02:51:57'),
('DTR260406102654875', 'TRM0009', 'KRD0011', 'DKO0008', 20, 5, 'ada yang rusak ', 'bukti-retur/WB31v0U3Jw7jfOLfDXeylIrWX7Jm4KWdoyO58x6P.png', '2026-04-06 03:26:54', '2026-04-06 03:26:54'),
('DTR260406103523325', 'TRM0010', 'KRD0012', 'DKO0008', 5, 0, '', NULL, '2026-04-06 03:35:23', '2026-04-06 03:35:23'),
('DTR260406132321530', 'TRM0011', 'KRD0013', 'DKO0009', 25, 0, '', NULL, '2026-04-06 06:23:21', '2026-04-06 06:23:21'),
('DTR260406132421521', 'TRM0012', 'KRD0014', 'DKO0009', 20, 5, 'rusak', 'bukti-retur/b55tFUdK2deDTpBByyNPq7iDaSjGAQknN4hOyCk3.jpg', '2026-04-06 06:24:21', '2026-04-06 06:24:21'),
('DTR260406132524214', 'TRM0013', 'KRD0015', 'DKO0009', 5, 0, '', NULL, '2026-04-06 06:25:24', '2026-04-06 06:25:24'),
('DTR260408034550757', 'TRM0014', 'KRD0016', 'DKO0010', 2, 3, 'rusak', 'bukti-retur/JyR2rsb15rcxhIfEEpxcJ97EgwrFgGaTYybdjxFL.jpg', '2026-04-07 20:45:50', '2026-04-07 20:45:50'),
('DTR260408034642525', 'TRM0015', 'KRD0017', 'DKO0010', 5, 0, '', NULL, '2026-04-07 20:46:42', '2026-04-07 20:46:42'),
('DTR260413184403979', 'TRM0016', 'KRD0020', 'DKO0009', 25, 0, '', NULL, '2026-04-13 11:44:03', '2026-04-13 11:44:03'),
('DTR260413184457190', 'TRM0017', 'KRD0019', 'DKO0009', 20, 5, 'rusak', 'bukti-retur/qpx7HQCdcJy9NFBaoSVsOBRnVP5GBO85NEwAy2tf.jpg', '2026-04-13 11:44:57', '2026-04-13 11:44:57'),
('DTR260413193812197', 'TRM0018', 'KRD0022', 'DKO0012', 25, 0, '', NULL, '2026-04-13 12:38:12', '2026-04-13 12:38:12'),
('DTR260413193812461', 'TRM0018', 'KRD0023', 'DKO0013', 25, 0, '', NULL, '2026-04-13 12:38:12', '2026-04-13 12:38:12'),
('DTR260413193835658', 'TRM0019', 'KRD0024', 'DKO0012', 25, 0, '', NULL, '2026-04-13 12:38:35', '2026-04-13 12:38:35'),
('DTR260413193835924', 'TRM0019', 'KRD0025', 'DKO0013', 25, 0, '', NULL, '2026-04-13 12:38:35', '2026-04-13 12:38:35'),
('DTR260414101053808', 'TRM0020', 'KRD0018', 'DKO0010', 3, 0, '', NULL, '2026-04-14 03:10:53', '2026-04-14 03:10:53'),
('DTR260414123747970', 'TRM0021', 'KRD0027', 'DKO0014', 0, 5, 'hancur', 'bukti-retur/bBW9IWlJyJ6omHC1EIXIV5CUEm4rkYuhL8pLf36b.png', '2026-04-14 05:37:47', '2026-04-14 05:37:47'),
('DTR260414132614377', 'TRM0022', 'KRD0029', 'DKO0015', 0, 1, 'patah ', 'bukti-retur/iHgDVpg2IKCdqmyFaG85GhgULDtljhoXVnLBQZzh.jpg', '2026-04-14 06:26:14', '2026-04-14 06:26:14'),
('DTR260414132944378', 'TRM0023', 'KRD0030', 'DKO0015', 1, 0, '', NULL, '2026-04-14 06:29:44', '2026-04-14 06:29:44'),
('DTR260418181554119', 'TRM0024', 'KRD0033', 'DKO0017', 10, 0, '', NULL, '2026-04-18 11:15:54', '2026-04-18 11:15:54'),
('DTR260418181554667', 'TRM0024', 'KRD0032', 'DKO0016', 10, 0, '', NULL, '2026-04-18 11:15:54', '2026-04-18 11:15:54'),
('DTR260418181604999', 'TRM0025', 'KRD0034', 'DKO0016', 5, 0, '', NULL, '2026-04-18 11:16:04', '2026-04-18 11:16:04'),
('DTR260418181632771', 'TRM0026', 'KRD0036', 'DKO0017', 5, 0, '', NULL, '2026-04-18 11:16:32', '2026-04-18 11:16:32'),
('DTR260418181632827', 'TRM0026', 'KRD0035', 'DKO0016', 5, 0, '', NULL, '2026-04-18 11:16:32', '2026-04-18 11:16:32');

-- --------------------------------------------------------

--
-- Table structure for table `detail_pengajuan_pembelian`
--

CREATE TABLE `detail_pengajuan_pembelian` (
  `id_detail_pengajuan` varchar(20) NOT NULL,
  `id_pengajuan` varchar(20) NOT NULL,
  `id_material` varchar(20) NOT NULL,
  `jumlah_minta_beli` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_pengajuan_pembelian`
--

INSERT INTO `detail_pengajuan_pembelian` (`id_detail_pengajuan`, `id_pengajuan`, `id_material`, `jumlah_minta_beli`, `created_at`, `updated_at`) VALUES
('DPR0001', 'PR0001', 'MAT0001', 100, '2026-03-14 03:03:56', '2026-03-14 03:03:56'),
('DPR0002', 'PR0001', 'MAT0003', 100, '2026-03-14 03:03:56', '2026-03-14 03:03:56'),
('DPR0003', 'PR0002', 'MAT0001', 10, '2026-03-14 03:21:07', '2026-03-14 03:21:07'),
('DPR0004', 'PR0002', 'MAT0002', 50, '2026-03-14 03:21:07', '2026-03-14 03:21:07'),
('DPR0005', 'PR0003', 'MAT0001', 10, '2026-03-28 04:04:07', '2026-03-28 04:04:07'),
('DPR0006', 'PR0004', 'MAT0007', 100, '2026-03-31 02:22:11', '2026-03-31 02:22:11'),
('DPR0007', 'PR0005', 'MAT0010', 100, '2026-03-31 16:31:52', '2026-03-31 16:31:52'),
('DPR0008', 'PR0006', 'MAT0001', 100000, '2026-03-31 21:16:16', '2026-03-31 21:16:16'),
('DPR0009', 'PR0007', 'MAT0008', 50, '2026-03-31 21:38:32', '2026-03-31 21:38:32'),
('DPR0010', 'PR0008', 'MAT0012', 10, '2026-04-07 20:32:03', '2026-04-07 20:32:03'),
('DPR0011', 'PR0009', 'MAT0001', 10, '2026-04-07 22:54:26', '2026-04-07 22:54:26'),
('DPR0012', 'PR0010', 'MAT0004', 50, '2026-04-13 12:33:08', '2026-04-13 12:33:08'),
('DPR0013', 'PR0010', 'MAT0001', 50, '2026-04-13 12:33:08', '2026-04-13 12:33:08'),
('DPR0014', 'PR0011', 'MAT0003', 10, '2026-04-14 03:12:13', '2026-04-14 03:12:13'),
('DPR0015', 'PR0012', 'MAT0016', 1, '2026-04-14 06:05:03', '2026-04-14 06:05:03'),
('DPR0016', 'PR0013', 'MAT0011', 20, '2026-04-18 10:25:52', '2026-04-18 10:25:52'),
('DPR0017', 'PR0013', 'MAT0016', 15, '2026-04-18 10:25:52', '2026-04-18 10:25:52'),
('DPR0018', 'PR0014', 'MAT0010', 10, '2026-04-19 00:31:11', '2026-04-19 00:31:11'),
('DPR0019', 'PR0015', 'MAT0003', 100, '2026-04-19 00:49:13', '2026-04-19 00:49:13');

-- --------------------------------------------------------

--
-- Table structure for table `detail_penggunaan_material`
--

CREATE TABLE `detail_penggunaan_material` (
  `id_detail_penggunaan` varchar(20) NOT NULL,
  `id_penggunaan` varchar(20) NOT NULL,
  `id_material` varchar(20) NOT NULL,
  `jumlah_terpasang_riil` int NOT NULL,
  `jumlah_rusak_lapangan` int DEFAULT '0',
  `jumlah_sisa_material` int DEFAULT '0',
  `catatan_khusus` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_penggunaan_material`
--

INSERT INTO `detail_penggunaan_material` (`id_detail_penggunaan`, `id_penggunaan`, `id_material`, `jumlah_terpasang_riil`, `jumlah_rusak_lapangan`, `jumlah_sisa_material`, `catatan_khusus`, `created_at`, `updated_at`) VALUES
('USE-20260314-001-D1', 'USE-20260314-001', 'MAT0001', 100, 0, 0, 'terpakai semua', '2026-03-14 03:18:12', '2026-03-14 03:18:12'),
('USE-20260314-001-D2', 'USE-20260314-001', 'MAT0003', 100, 0, 0, 'terpakai semua', '2026-03-14 03:18:12', '2026-03-14 03:18:12'),
('USE-20260314-002-D1', 'USE-20260314-002', 'MAT0001', 60, 0, 0, '', '2026-03-14 04:14:43', '2026-03-14 04:14:43'),
('USE-20260314-002-D2', 'USE-20260314-002', 'MAT0002', 50, 0, 0, '', '2026-03-14 04:14:43', '2026-03-14 04:14:43'),
('USE-20260401-001-D1', 'USE-20260401-001', 'MAT0001', 100000, 0, 0, '', '2026-03-31 21:33:06', '2026-03-31 21:33:06'),
('USE-20260414-001-D1', 'USE-20260414-001', 'MAT0016', 1, 0, 0, '', '2026-04-14 06:37:47', '2026-04-14 06:37:47'),
('USE-20260414-001-D2', 'USE-20260414-001', 'MAT0001', 1, 0, 0, '', '2026-04-14 06:37:47', '2026-04-14 06:37:47'),
('USE-20260418-001-D1', 'USE-20260418-001', 'MAT0007', 10, 0, 0, '', '2026-04-18 12:05:38', '2026-04-18 12:05:38');

-- --------------------------------------------------------

--
-- Table structure for table `detail_permintaan_proyek`
--

CREATE TABLE `detail_permintaan_proyek` (
  `id_detail_permintaan` varchar(20) NOT NULL,
  `id_permintaan` varchar(20) NOT NULL,
  `id_material` varchar(20) NOT NULL,
  `jumlah_diminta` int NOT NULL,
  `jumlah_terkirim` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_permintaan_proyek`
--

INSERT INTO `detail_permintaan_proyek` (`id_detail_permintaan`, `id_permintaan`, `id_material`, `jumlah_diminta`, `jumlah_terkirim`, `created_at`, `updated_at`) VALUES
('DRQ0001', 'REQ0001', 'MAT0001', 100, 100, '2026-03-14 02:38:12', '2026-03-14 03:15:56'),
('DRQ0002', 'REQ0001', 'MAT0003', 100, 100, '2026-03-14 02:38:12', '2026-03-14 03:15:56'),
('DRQ0003', 'REQ0002', 'MAT0001', 60, 60, '2026-03-14 03:20:27', '2026-03-14 03:34:18'),
('DRQ0004', 'REQ0002', 'MAT0002', 50, 50, '2026-03-14 03:20:27', '2026-03-14 03:34:18'),
('DRQ0005', 'REQ0003', 'MAT0001', 10, 10, '2026-03-14 20:51:05', '2026-03-28 04:12:14'),
('DRQ0006', 'REQ0004', 'MAT0001', 100000, 100000, '2026-03-31 21:13:35', '2026-03-31 21:32:20'),
('DRQ0007', 'REQ0005', 'MAT0010', 30, 30, '2026-04-13 14:00:34', '2026-04-13 14:33:39'),
('DRQ0008', 'REQ0005', 'MAT0001', 30, 30, '2026-04-13 14:00:34', '2026-04-13 14:33:39'),
('DRQ0009', 'REQ0006', 'MAT0016', 1, 1, '2026-04-14 06:01:31', '2026-04-14 06:30:26'),
('DRQ0010', 'REQ0006', 'MAT0001', 1, 1, '2026-04-14 06:01:31', '2026-04-14 06:05:03'),
('DRQ0011', 'REQ0007', 'MAT0011', 20, 20, '2026-04-18 10:11:53', '2026-04-18 11:17:23'),
('DRQ0012', 'REQ0007', 'MAT0016', 15, 15, '2026-04-18 10:11:53', '2026-04-18 11:17:23'),
('DRQ0013', 'REQ0007', 'MAT0008', 35, 35, '2026-04-18 10:11:53', '2026-04-18 10:25:52'),
('DRQ0014', 'REQ0008', 'MAT0007', 5, 5, '2026-04-18 11:22:02', '2026-04-18 11:54:46'),
('DRQ0015', 'REQ0009', 'MAT0007', 10, 10, '2026-04-18 11:53:12', '2026-04-18 11:54:53');

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `id_detail_pesanan` varchar(20) NOT NULL,
  `id_pesanan` varchar(20) NOT NULL,
  `id_material` varchar(20) NOT NULL,
  `jumlah_pesan` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`id_detail_pesanan`, `id_pesanan`, `id_material`, `jumlah_pesan`, `created_at`, `updated_at`) VALUES
('DPS0001', 'RFQ0001', 'MAT0001', 150, '2026-03-14 03:08:49', '2026-03-14 03:08:49'),
('DPS0002', 'RFQ0001', 'MAT0003', 100, '2026-03-14 03:08:49', '2026-03-14 03:08:49'),
('DPS0003', 'RFQ0002', 'MAT0001', 10, '2026-03-14 03:31:12', '2026-03-14 03:31:12'),
('DPS0004', 'RFQ0002', 'MAT0002', 50, '2026-03-14 03:31:12', '2026-03-14 03:31:12'),
('DPS0005', 'RFQ0003', 'MAT0001', 10, '2026-03-28 04:05:19', '2026-03-28 04:05:19'),
('DPS0006', 'RFQ0004', 'MAT0007', 100, '2026-03-31 02:23:22', '2026-03-31 02:23:22'),
('DPS0007', 'RFQ0005', 'MAT0010', 100, '2026-03-31 16:34:57', '2026-03-31 16:34:57'),
('DPS0008', 'RFQ0006', 'MAT0001', 100000, '2026-03-31 21:16:58', '2026-03-31 21:16:58'),
('DPS0009', 'RFQ0007', 'MAT0008', 50, '2026-03-31 21:41:43', '2026-03-31 21:41:43'),
('DPS0010', 'RFQ0008', 'MAT0012', 10, '2026-04-07 20:33:54', '2026-04-07 20:33:54'),
('DPS0011', 'RFQ0009', 'MAT0001', 10, '2026-04-07 22:55:09', '2026-04-07 22:55:09'),
('DPS0012', 'RFQ0010', 'MAT0004', 50, '2026-04-13 12:33:44', '2026-04-13 12:33:44'),
('DPS0013', 'RFQ0010', 'MAT0001', 50, '2026-04-13 12:33:44', '2026-04-13 12:33:44'),
('DPS0014', 'RFQ0011', 'MAT0003', 10, '2026-04-14 03:13:00', '2026-04-14 03:13:00'),
('DPS0015', 'RFQ0012', 'MAT0016', 1, '2026-04-14 06:07:39', '2026-04-14 06:07:39'),
('DPS0016', 'RFQ0013', 'MAT0011', 20, '2026-04-18 10:26:57', '2026-04-18 10:26:57'),
('DPS0017', 'RFQ0013', 'MAT0016', 15, '2026-04-18 10:26:57', '2026-04-18 10:26:57'),
('DPS0018', 'RFQ0014', 'MAT0010', 10, '2026-04-19 01:12:55', '2026-04-19 01:12:55');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_pembelian`
--

CREATE TABLE `invoice_pembelian` (
  `id_invoice` varchar(20) NOT NULL,
  `id_kontrak` varchar(20) NOT NULL,
  `id_user` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `nomor_invoice_supplier` varchar(100) NOT NULL,
  `tanggal_invoice` date NOT NULL,
  `jatuh_tempo` date NOT NULL,
  `total_tagihan` decimal(15,2) NOT NULL,
  `status_invoice` enum('Menunggu Pembayaran','Dibayar Sebagian','Lunas','Dibatalkan') DEFAULT 'Menunggu Pembayaran',
  `file_invoice` varchar(255) DEFAULT NULL,
  `catatan` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `invoice_pembelian`
--

INSERT INTO `invoice_pembelian` (`id_invoice`, `id_kontrak`, `id_user`, `nomor_invoice_supplier`, `tanggal_invoice`, `jatuh_tempo`, `total_tagihan`, `status_invoice`, `file_invoice`, `catatan`, `created_at`, `updated_at`) VALUES
('INV-2603-0001', 'KON0001', NULL, 'INV/23453/3435', '2026-03-31', '2026-04-09', 8745000.00, 'Lunas', 'invoices/X3jmWweb4CwbUaewbr7Fr44pV2EdpPWQC9ducaCS.jpg', '', '2026-03-31 01:25:17', '2026-03-31 01:48:45'),
('INV-2603-0002', 'KON0004', NULL, '097656jhgfdrty', '2026-03-31', '2026-03-31', 5140000.00, 'Lunas', 'invoices/q4GO99R9T22wniMd3zlS5hgz0g6SD8iqReR7xpef.png', 'done\n', '2026-03-31 02:37:33', '2026-03-31 02:37:36'),
('INV-2603-0003', 'KON0002', 'USR0003', '905yjhgrert5', '2026-03-31', '2026-03-31', 1820000.00, 'Lunas', 'invoices/0PsKBUQWNSepxXwA6R79bUQwLcihRorCfw2JZ9tD.jpg', '', '2026-03-31 04:14:51', '2026-03-31 04:14:58'),
('INV-2604-0001', 'KON0005', 'USR0003', 'e5678987y', '2026-04-02', '2026-04-02', 9600022000.00, 'Lunas', 'invoices/kcaLslyQg09SqHV18JzO4O0MzgdmTHfmzMRcxscO.jpg', '', '2026-03-31 21:26:25', '2026-03-31 21:26:37'),
('INV-2604-0002', 'KON0003', 'USR0003', '345678iuy', '2026-04-01', '2026-04-01', 520000.00, 'Lunas', 'invoices/WAMG64iGPF3sA5tGAQIUZ2taogta5Kgx5oSFROm9.png', '', '2026-03-31 21:43:34', '2026-03-31 21:43:37'),
('INV-2604-0003', 'KON0006', 'USR0003', '0987th75', '2026-04-06', '2026-04-06', 4570000.00, 'Lunas', 'invoices/otUxurYWLtkIeoOrB90VniA4z6oA1C9pp2aUOY3s.jpg', '', '2026-04-06 02:24:48', '2026-04-06 02:24:56'),
('INV-2604-0004', 'KON0007', 'USR0003', '098765tyu', '2026-04-06', '2026-04-06', 4989500.00, 'Lunas', 'invoices/u4tVWN41Zd3VtqyGMcPHQilYn5snhqi0yxjRKx7O.jpg', 'sesuai', '2026-04-06 06:18:01', '2026-04-06 06:18:04'),
('INV-2604-0005', 'KON0008', 'USR0003', '5678iu', '2026-04-08', '2026-04-10', 303760.00, 'Lunas', 'invoices/fCBpqTM3Qb4MNhrTLefZwGqVwBShwkTUdqjPhMzh.jpg', 'gasss', '2026-04-07 20:37:37', '2026-04-07 20:37:42'),
('INV-2604-0006', 'KON0009', 'USR0003', '4567890iuytrty', '2026-04-08', '2026-04-09', 220920.00, 'Lunas', 'invoices/id7H4p4Q9eN6rwdteLzGRAsQha3cSnXHuwLn38QX.jpg', '', '2026-04-07 23:38:13', '2026-04-07 23:38:20'),
('INV-2604-0007', 'KON0010', 'USR0003', '678ijh9iue', '2026-04-14', '2026-04-14', 3879750.00, 'Lunas', 'invoices/PrU92GoLeJiDOluN8T23WvBN0kpOrsK4AtuUhyB1.jpg', 'done', '2026-04-13 12:35:30', '2026-04-13 12:35:46'),
('INV-2604-0008', 'KON0011', 'USR0003', '0987yuikj', '2026-04-14', '2026-04-15', 382989.00, 'Lunas', 'invoices/ID1fBYyqf0F66Hr0ysF8CPW21I2W4bID8oa9TlRz.png', 'done\n', '2026-04-14 03:15:01', '2026-04-14 03:15:23'),
('INV-2604-0009', 'KON0012', 'USR0003', '567uytfyh', '2026-04-14', '2026-04-15', 43300.00, 'Lunas', 'invoices/PAUK43br2XSSCJc7TVP5bu7nWnttnGgFcT11iRPb.jpg', 'done', '2026-04-14 06:13:25', '2026-04-14 06:14:09'),
('INV-2604-0010', 'KON0013', 'USR0003', 'o98764rek', '2026-04-19', '2026-04-22', 1022000.00, 'Lunas', 'invoices/lQzMah81qWgwfoUDi1LI79HRyHU3kMBGh8N9JbBw.jpg', '', '2026-04-18 11:03:38', '2026-04-18 11:05:30');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori_material`
--

CREATE TABLE `kategori_material` (
  `id_kategori_material` varchar(20) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text,
  `status_kategori` enum('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategori_material`
--

INSERT INTO `kategori_material` (`id_kategori_material`, `nama_kategori`, `deskripsi`, `status_kategori`, `created_at`, `updated_at`) VALUES
('CAT0001', 'Semen & Pasir', 'Bahan pengikat dan agregat halus untuk beton dan plester', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('CAT0002', 'Besi & Baja', 'Material tulangan beton dan struktur baja ringan/berat', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('CAT0003', 'Kayu & Multiplek', 'Material kayu untuk begisting, perancah, dan rangka', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('CAT0004', 'Batu & Bata', 'Material pasangan dinding, pondasi, dan agregat kasar', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('CAT0005', 'Cat & Thinner', 'Material pelapis, pelindung, dan finishing dinding/besi', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('CAT0006', 'Pipa & Plumbing', 'Material saluran air bersih, kotor, dan perlengkapan sanitasi', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('CAT0007', 'Kabel & Elektrikal', 'Material instalasi listrik dan komponen pendukungnya', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('CAT0008', 'Keramik & Granit', 'Material penutup lantai dan dinding', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('CAT0009', 'Atap & Plafon', 'Material penutup atas bangunan dan rangka plafon', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('CAT0010', 'Paku & Baut', 'Material pengikat dan penyambung berbagai elemen konstruksi', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33');

-- --------------------------------------------------------

--
-- Table structure for table `kontrak`
--

CREATE TABLE `kontrak` (
  `id_kontrak` varchar(20) NOT NULL,
  `id_pesanan` varchar(20) NOT NULL,
  `id_supplier` varchar(20) NOT NULL,
  `id_user_pengadaan` varchar(20) NOT NULL,
  `nomor_kontrak` varchar(100) DEFAULT NULL,
  `file_kontrak_path` varchar(255) DEFAULT NULL,
  `tanggal_kontrak` date DEFAULT NULL,
  `total_harga_awal` decimal(15,2) DEFAULT NULL,
  `total_harga_negosiasi` decimal(15,2) DEFAULT NULL,
  `total_diskon` decimal(15,2) DEFAULT NULL,
  `total_ongkir` decimal(15,2) DEFAULT NULL,
  `total_ppn` decimal(15,2) DEFAULT NULL,
  `total_nilai_kontrak` decimal(15,2) DEFAULT NULL,
  `status_kontrak` enum('Draft','Disepakati','Batal') DEFAULT 'Draft',
  `status_pengiriman` enum('Menunggu','Pengiriman','Return','Selesai') DEFAULT 'Menunggu',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kontrak`
--

INSERT INTO `kontrak` (`id_kontrak`, `id_pesanan`, `id_supplier`, `id_user_pengadaan`, `nomor_kontrak`, `file_kontrak_path`, `tanggal_kontrak`, `total_harga_awal`, `total_harga_negosiasi`, `total_diskon`, `total_ongkir`, `total_ppn`, `total_nilai_kontrak`, `status_kontrak`, `status_pengiriman`, `created_at`, `updated_at`) VALUES
('KON0001', 'RFQ0001', 'SUP0002', 'USR0003', 'PO/20260314/OAWC', NULL, '2026-03-14', 9500000.00, 9500000.00, 1330000.00, 500000.00, 75000.00, 8745000.00, 'Disepakati', 'Menunggu', '2026-03-14 03:10:33', '2026-03-14 03:10:37'),
('KON0002', 'RFQ0002', 'SUP0002', 'USR0003', 'PO/20260314/OT2J', NULL, '2026-03-14', 2500000.00, 2500000.00, 750000.00, 50000.00, 20000.00, 1820000.00, 'Disepakati', 'Menunggu', '2026-03-14 03:32:55', '2026-03-14 03:33:07'),
('KON0003', 'RFQ0003', 'SUP0004', 'USR0003', 'PO/20260328/EXVK', NULL, '2026-03-28', 500000.00, 500000.00, 0.00, 20000.00, 0.00, 520000.00, 'Disepakati', 'Menunggu', '2026-03-28 04:06:45', '2026-03-28 04:10:29'),
('KON0004', 'RFQ0004', 'SUP0004', 'USR0003', 'PO/20260331/MAUZ', NULL, '2026-03-31', 5000000.00, 5000000.00, 0.00, 120000.00, 20000.00, 5140000.00, 'Disepakati', 'Menunggu', '2026-03-31 02:24:59', '2026-03-31 02:25:08'),
('KON0005', 'RFQ0006', 'SUP0001', 'USR0003', 'PO/20260401/UEEH', NULL, '2026-04-01', 10000000000.00, 10000000000.00, 400000000.00, 2000.00, 20000.00, 9600022000.00, 'Disepakati', 'Menunggu', '2026-03-31 21:21:26', '2026-03-31 21:21:35'),
('KON0006', 'RFQ0007', 'SUP0004', 'USR0003', 'PO/20260401/OMRI', NULL, '2026-04-01', 5000000.00, 5000000.00, 500000.00, 20000.00, 50000.00, 4570000.00, 'Disepakati', 'Menunggu', '2026-03-31 21:42:48', '2026-04-06 02:23:58'),
('KON0007', 'RFQ0005', 'SUP0003', 'USR0003', 'PO/20260406/0I1J', NULL, '2026-04-06', 5000000.00, 5000000.00, 550000.00, 50000.00, 489500.00, 4989500.00, 'Disepakati', 'Menunggu', '2026-04-06 06:17:04', '2026-04-06 06:17:09'),
('KON0008', 'RFQ0008', 'SUP0002', 'USR0003', 'PO/20260408/SYRV', NULL, '2026-04-08', 300000.00, 300000.00, 12000.00, 10000.00, 5760.00, 303760.00, 'Disepakati', 'Menunggu', '2026-04-07 20:37:05', '2026-04-07 20:37:09'),
('KON0009', 'RFQ0009', 'SUP0004', 'USR0003', 'PO/20260408/GOMV', NULL, '2026-04-08', 200000.00, 200000.00, 28000.00, 30000.00, 18920.00, 220920.00, 'Disepakati', 'Menunggu', '2026-04-07 23:32:47', '2026-04-07 23:34:28'),
('KON0010', 'RFQ0010', 'SUP0001', 'USR0003', 'PO/20260413/JLEV', NULL, '2026-04-13', 3750000.00, 3750000.00, 525000.00, 300000.00, 354750.00, 3879750.00, 'Disepakati', 'Menunggu', '2026-04-13 12:34:55', '2026-04-13 12:35:01'),
('KON0011', 'RFQ0011', 'SUP0003', 'USR0003', 'PO/20260414/R3BW', NULL, '2026-04-14', 299990.00, 299990.00, 0.00, 50000.00, 32998.90, 382988.90, 'Disepakati', 'Menunggu', '2026-04-14 03:13:46', '2026-04-14 03:13:50'),
('KON0012', 'RFQ0012', 'SUP0001', 'USR0003', 'PO/20260414/THSJ', NULL, '2026-04-14', 30000.00, 30000.00, 0.00, 10000.00, 3300.00, 43300.00, 'Disepakati', 'Menunggu', '2026-04-14 06:12:10', '2026-04-14 06:12:23'),
('KON0013', 'RFQ0013', 'SUP0004', 'USR0003', 'PO/20260418/WZOL', NULL, '2026-04-18', 900000.00, 900000.00, 0.00, 32000.00, 90000.00, 1022000.00, 'Disepakati', 'Menunggu', '2026-04-18 11:01:37', '2026-04-18 11:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `master_lokasi_rak`
--

CREATE TABLE `master_lokasi_rak` (
  `id_lokasi` varchar(20) NOT NULL,
  `nama_lokasi` varchar(50) NOT NULL,
  `area` varchar(50) DEFAULT NULL,
  `keterangan` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `master_lokasi_rak`
--

INSERT INTO `master_lokasi_rak` (`id_lokasi`, `nama_lokasi`, `area`, `keterangan`, `created_at`, `updated_at`) VALUES
('LOC0001', 'Gudang Utama - Rak A1', 'Semen & Pasir', 'Area penyimpanan semen dan pasir', '2026-03-13 16:35:14', '2026-03-13 16:35:14'),
('LOC0002', 'Gudang Utama - Rak A2', 'Semen & Pasir', 'Area tambahan untuk semen dan mortar', '2026-03-13 16:35:14', '2026-03-13 16:35:14'),
('LOC0003', 'Gudang Utama - Rak B1', 'Besi & Baja', 'Area penyimpanan besi tulangan dan baja', '2026-03-13 16:35:14', '2026-03-13 16:35:14'),
('LOC0004', 'Gudang Utama - Rak C1', 'Kayu & Multiplek', 'Area penyimpanan kayu konstruksi dan multiplek', '2026-03-13 16:35:14', '2026-03-13 16:35:14'),
('LOC0005', 'Gudang Utama - Rak D1', 'Batu & Bata', 'Area penyimpanan batu bata dan agregat kasar', '2026-03-13 16:35:14', '2026-03-13 16:35:14'),
('LOC0006', 'Gudang Utama - Rak E1', 'Cat & Thinner', 'Area penyimpanan cat, thinner, dan bahan finishing', '2026-03-13 16:35:14', '2026-03-13 16:35:14'),
('LOC0007', 'Gudang Utama - Rak F1', 'Pipa & Plumbing', 'Area penyimpanan pipa PVC, paralon, dan perlengkapan plumbing', '2026-03-13 16:35:14', '2026-03-13 16:35:14'),
('LOC0008', 'Gudang Utama - Rak G1', 'Kabel & Elektrikal', 'Area penyimpanan kabel listrik dan komponen elektrikal', '2026-03-13 16:35:14', '2026-03-13 16:35:14'),
('LOC0009', 'Gudang Utama - Rak H1', 'Keramik & Granit', 'Area penyimpanan keramik dan granit', '2026-03-13 16:35:14', '2026-03-13 16:35:14'),
('LOC0010', 'Gudang Utama - Rak I1', 'Atap & Plafon', 'Area penyimpanan material atap dan plafon', '2026-03-13 16:35:14', '2026-03-13 16:35:14'),
('LOC0011', 'Gudang Utama - Rak J1', 'Paku & Baut', 'Area penyimpanan paku, baut, dan pengikat', '2026-03-13 16:35:14', '2026-03-13 16:35:14');

-- --------------------------------------------------------

--
-- Table structure for table `material`
--

CREATE TABLE `material` (
  `id_material` varchar(20) NOT NULL,
  `id_kategori_material` varchar(20) NOT NULL,
  `nama_material` varchar(150) NOT NULL,
  `satuan` varchar(50) NOT NULL,
  `spesifikasi` text,
  `standar_kualitas` varchar(100) DEFAULT NULL,
  `status_material` enum('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `material`
--

INSERT INTO `material` (`id_material`, `id_kategori_material`, `nama_material`, `satuan`, `spesifikasi`, `standar_kualitas`, `status_material`, `created_at`, `updated_at`) VALUES
('MAT0001', 'CAT0001', 'Semen Portland Tiga Roda', 'Sak (50kg)', 'PC Tipe 1', 'SNI', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('MAT0002', 'CAT0001', 'Semen Gresik', 'Sak (40kg)', 'PCC', 'SNI', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('MAT0003', 'CAT0001', 'Pasir Lumajang', 'M3', 'Pasir Cor Bersih (Kasar)', 'Lokal Terbaik', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('MAT0004', 'CAT0001', 'Pasir Pasang/Cileungsi', 'M3', 'Pasir untuk Plesteran', 'Lokal', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('MAT0005', 'CAT0002', 'Besi Beton Ulir 13mm (Krakatau Steel)', 'Batang (12m)', 'Ulir TS 420', 'SNI', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('MAT0006', 'CAT0002', 'Besi Beton Polos 8mm', 'Batang (12m)', 'Polos TP 280', 'SNI', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('MAT0007', 'CAT0002', 'Baja Ringan Canal C 75', 'Batang (6m)', 'Tebal 0.75mm (Galvalum)', 'SNI', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('MAT0008', 'CAT0002', 'Kawat Bendrat', 'Roll (20kg)', 'Kawat pengikat tulangan', 'Standar', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('MAT0009', 'CAT0003', 'Triplek/Multiplek 12mm', 'Lembar', 'Ukuran 122x244 cm', 'Lokal', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('MAT0010', 'CAT0003', 'Kayu Meranti 4x6', 'Batang (4m)', 'Kayu Begisting Tahan Air', 'Lokal', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('MAT0011', 'CAT0004', 'Bata Ringan (Hebel)', 'M3', 'Ukuran 60x20x10 cm', 'SNI', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('MAT0012', 'CAT0004', 'Bata Merah Press', 'Pcs', 'Ukuran Standar', 'Lokal', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('MAT0013', 'CAT0004', 'Batu Pecah / Split 1/2', 'M3', 'Batu Cor / Agregat Kasar', 'Lokal', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('MAT0014', 'CAT0005', 'Cat Tembok Interior Dulux', 'Pail (20L)', 'Warna Putih Bersih', 'Standar Pabrik', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('MAT0015', 'CAT0005', 'Cat Besi Nippon Paint', 'Kaleng (1kg)', 'Warna Hitam Anti Karat', 'Standar Pabrik', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('MAT0016', 'CAT0006', 'Pipa PVC Wavin 4 inch', 'Batang (4m)', 'Tipe AW (Tebal, untuk air kotor)', 'SNI', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('MAT0017', 'CAT0006', 'Pipa PVC Wavin 1/2 inch', 'Batang (4m)', 'Tipe AW (untuk air bersih)', 'SNI', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('MAT0018', 'CAT0007', 'Kabel NYM 3x2.5mm Eterna', 'Roll (50m)', 'Kabel instalasi dalam', 'SNI/LMK', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('MAT0019', 'CAT0008', 'Granit Roman 60x60', 'Dus (1.44m2)', 'Warna Cream Polos (KW 1)', 'SNI', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('MAT0020', 'CAT0009', 'Papan Gypsum Jayaboard 9mm', 'Lembar', 'Ukuran 120x240 cm', 'Standar Pabrik', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_02_14_060302_create_proyek_table', 1),
(5, '2026_02_14_060303_create_supplier_table', 1),
(6, '2026_02_14_060304_create_kategori_material_table', 1),
(7, '2026_02_14_060305_create_materials_table', 1),
(8, '2026_02_14_060306_create_usulan_materials_table', 1),
(9, '2026_02_14_060307_create_pengajuan_materials_table', 1),
(10, '2026_02_14_060308_create_detail_pengajuan_materials_table', 1),
(11, '2026_02_14_060309_create_kontraks_table', 1),
(12, '2026_02_14_060310_create_detail_kontraks_table', 1),
(13, '2026_02_14_060311_create_pesanans_table', 1),
(14, '2026_02_14_060312_create_detail_pesanans_table', 1),
(15, '2026_02_14_060313_create_pengirimen_table', 1),
(16, '2026_02_14_060314_create_penerimaan_materials_table', 1),
(17, '2026_02_14_060315_create_stok_material_proyeks_table', 1),
(18, '2026_02_14_060316_create_penggunaan_materials_table', 1),
(19, '2026_02_16_095336_add_is_active_to_users_table', 2),
(20, '2026_02_16_151037_create_penugasan_proyek_table', 3);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penerimaan_material`
--

CREATE TABLE `penerimaan_material` (
  `id_penerimaan` varchar(50) NOT NULL,
  `id_pengiriman` varchar(50) NOT NULL,
  `id_user_penerima` varchar(50) NOT NULL,
  `tanggal_terima` date NOT NULL,
  `nomor_surat_jalan` varchar(100) DEFAULT NULL,
  `status_penerimaan` enum('Diterima Penuh','Diterima Sebagian','Return') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `penerimaan_material`
--

INSERT INTO `penerimaan_material` (`id_penerimaan`, `id_pengiriman`, `id_user_penerima`, `tanggal_terima`, `nomor_surat_jalan`, `status_penerimaan`, `created_at`, `updated_at`) VALUES
('TRM0001', 'KRM0001', 'USR0005', '2026-03-14', NULL, 'Diterima Penuh', '2026-03-14 03:12:29', '2026-03-14 03:12:29'),
('TRM0002', 'KRM0002', 'USR0005', '2026-03-14', NULL, 'Diterima Sebagian', '2026-03-14 03:13:04', '2026-03-14 03:13:04'),
('TRM0003', 'KRM0003', 'USR0005', '2026-03-14', NULL, 'Diterima Penuh', '2026-03-14 03:15:05', '2026-03-14 03:15:05'),
('TRM0004', 'KRM0004', 'USR0005', '2026-03-14', NULL, 'Diterima Penuh', '2026-03-14 03:34:05', '2026-03-14 03:34:05'),
('TRM0005', 'KRM0005', 'USR0005', '2026-03-28', NULL, 'Diterima Penuh', '2026-03-28 04:12:06', '2026-03-28 04:12:06'),
('TRM0006', 'KRM0006', 'USR0005', '2026-03-31', NULL, 'Diterima Penuh', '2026-03-31 02:38:46', '2026-03-31 02:38:46'),
('TRM0007', 'KRM0007', 'USR0005', '2026-04-01', NULL, 'Diterima Penuh', '2026-03-31 21:32:09', '2026-03-31 21:32:09'),
('TRM0008', 'KRM0008', 'USR0005', '2026-04-06', NULL, 'Diterima Penuh', '2026-04-06 02:51:57', '2026-04-06 02:51:57'),
('TRM0009', 'KRM0009', 'USR0005', '2026-04-06', NULL, 'Diterima Sebagian', '2026-04-06 03:26:54', '2026-04-06 03:26:54'),
('TRM0010', 'KRM0010', 'USR0005', '2026-04-06', NULL, 'Diterima Penuh', '2026-04-06 03:35:23', '2026-04-06 03:35:23'),
('TRM0011', 'KRM0011', 'USR0005', '2026-04-06', NULL, 'Diterima Penuh', '2026-04-06 06:23:21', '2026-04-06 06:23:21'),
('TRM0012', 'KRM0012', 'USR0005', '2026-04-06', NULL, 'Diterima Sebagian', '2026-04-06 06:24:21', '2026-04-06 06:24:21'),
('TRM0013', 'KRM0013', 'USR0005', '2026-04-06', NULL, 'Diterima Penuh', '2026-04-06 06:25:24', '2026-04-06 06:25:24'),
('TRM0014', 'KRM0014', 'USR0005', '2026-04-08', NULL, 'Diterima Sebagian', '2026-04-07 20:45:50', '2026-04-07 20:45:50'),
('TRM0015', 'KRM0015', 'USR0005', '2026-04-08', NULL, 'Diterima Penuh', '2026-04-07 20:46:42', '2026-04-07 20:46:42'),
('TRM0016', 'KRM0018', 'USR0005', '2026-04-13', NULL, 'Diterima Penuh', '2026-04-13 11:44:03', '2026-04-13 11:44:03'),
('TRM0017', 'KRM0017', 'USR0005', '2026-04-13', NULL, 'Diterima Sebagian', '2026-04-13 11:44:57', '2026-04-13 11:44:57'),
('TRM0018', 'KRM0020', 'USR0005', '2026-04-13', NULL, 'Diterima Penuh', '2026-04-13 12:38:12', '2026-04-13 12:38:12'),
('TRM0019', 'KRM0021', 'USR0005', '2026-04-13', NULL, 'Diterima Penuh', '2026-04-13 12:38:35', '2026-04-13 12:38:35'),
('TRM0020', 'KRM0016', 'USR0005', '2026-04-14', NULL, 'Diterima Penuh', '2026-04-14 03:10:53', '2026-04-14 03:10:53'),
('TRM0021', 'KRM0023', 'USR0005', '2026-04-14', NULL, 'Diterima Sebagian', '2026-04-14 05:37:47', '2026-04-14 05:37:47'),
('TRM0022', 'KRM0025', 'USR0005', '2026-04-14', NULL, 'Diterima Sebagian', '2026-04-14 06:26:14', '2026-04-14 06:26:14'),
('TRM0023', 'KRM0026', 'USR0005', '2026-04-14', NULL, 'Diterima Penuh', '2026-04-14 06:29:44', '2026-04-14 06:29:44'),
('TRM0024', 'KRM0028', 'USR0005', '2026-04-18', NULL, 'Diterima Penuh', '2026-04-18 11:15:54', '2026-04-18 11:15:54'),
('TRM0025', 'KRM0029', 'USR0005', '2026-04-18', NULL, 'Diterima Penuh', '2026-04-18 11:16:04', '2026-04-18 11:16:04'),
('TRM0026', 'KRM0030', 'USR0005', '2026-04-18', NULL, 'Diterima Penuh', '2026-04-18 11:16:32', '2026-04-18 11:16:32');

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_pembelian`
--

CREATE TABLE `pengajuan_pembelian` (
  `id_pengajuan` varchar(20) NOT NULL,
  `id_user_logistik` varchar(20) NOT NULL,
  `referensi_id_permintaan` varchar(20) DEFAULT NULL,
  `tanggal_pengajuan` date NOT NULL,
  `status_pengajuan` enum('Menunggu Pengadaan','Proses RFQ','PO Dibuat','Selesai') DEFAULT 'Menunggu Pengadaan',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pengajuan_pembelian`
--

INSERT INTO `pengajuan_pembelian` (`id_pengajuan`, `id_user_logistik`, `referensi_id_permintaan`, `tanggal_pengajuan`, `status_pengajuan`, `created_at`, `updated_at`) VALUES
('PR0001', 'USR0005', 'REQ0001', '2026-03-14', 'Proses RFQ', '2026-03-14 02:58:45', '2026-03-14 03:08:49'),
('PR0002', 'USR0005', 'REQ0002', '2026-03-14', 'Proses RFQ', '2026-03-14 03:21:07', '2026-03-14 03:31:12'),
('PR0003', 'USR0005', 'REQ0003', '2026-03-28', 'Proses RFQ', '2026-03-28 04:04:07', '2026-03-28 04:05:19'),
('PR0004', 'USR0005', NULL, '2026-03-31', 'Proses RFQ', '2026-03-31 02:22:11', '2026-03-31 02:23:22'),
('PR0005', 'USR0005', NULL, '2026-04-01', 'Proses RFQ', '2026-03-31 16:31:52', '2026-03-31 16:34:57'),
('PR0006', 'USR0005', 'REQ0004', '2026-04-01', 'Proses RFQ', '2026-03-31 21:16:16', '2026-03-31 21:16:58'),
('PR0007', 'USR0005', NULL, '2026-04-01', 'Proses RFQ', '2026-03-31 21:38:32', '2026-03-31 21:41:43'),
('PR0008', 'USR0005', NULL, '2026-04-08', 'Proses RFQ', '2026-04-07 20:32:03', '2026-04-07 20:33:54'),
('PR0009', 'USR0005', NULL, '2026-04-08', 'Proses RFQ', '2026-04-07 22:54:26', '2026-04-07 22:55:09'),
('PR0010', 'USR0005', NULL, '2026-04-13', 'Proses RFQ', '2026-04-13 12:33:08', '2026-04-13 12:33:44'),
('PR0011', 'USR0005', NULL, '2026-04-14', 'Proses RFQ', '2026-04-14 03:12:13', '2026-04-14 03:13:00'),
('PR0012', 'USR0005', 'REQ0006', '2026-04-14', 'Proses RFQ', '2026-04-14 06:05:03', '2026-04-14 06:07:39'),
('PR0013', 'USR0005', 'REQ0007', '2026-04-18', 'Proses RFQ', '2026-04-18 10:25:52', '2026-04-18 10:26:57'),
('PR0014', 'USR0005', NULL, '2026-04-19', 'Proses RFQ', '2026-04-19 00:31:11', '2026-04-19 01:12:55'),
('PR0015', 'USR0009', NULL, '2026-04-19', 'Menunggu Pengadaan', '2026-04-19 00:49:13', '2026-04-19 00:49:13');

-- --------------------------------------------------------

--
-- Table structure for table `pengeluaran_stok_fifo`
--

CREATE TABLE `pengeluaran_stok_fifo` (
  `id` bigint NOT NULL,
  `id_permintaan` varchar(20) NOT NULL,
  `id_material` varchar(20) NOT NULL,
  `id_stok` varchar(20) NOT NULL,
  `jumlah_diambil` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pengeluaran_stok_fifo`
--

INSERT INTO `pengeluaran_stok_fifo` (`id`, `id_permintaan`, `id_material`, `id_stok`, `jumlah_diambil`, `created_at`, `updated_at`) VALUES
(1, 'REQ0003', 'MAT0001', 'STK26032811120611', 10, '2026-03-28 04:12:14', '2026-03-28 04:12:14'),
(2, 'REQ0004', 'MAT0001', 'STK26040104320934', 100000, '2026-03-31 21:32:20', '2026-03-31 21:32:20'),
(3, 'REQ0005', 'MAT0010', 'STK26040613232139', 25, '2026-04-13 14:33:39', '2026-04-13 14:33:39'),
(4, 'REQ0005', 'MAT0010', 'STK26040613242127', 5, '2026-04-13 14:33:39', '2026-04-13 14:33:39'),
(5, 'REQ0005', 'MAT0001', 'STK26041319381265', 25, '2026-04-13 14:33:39', '2026-04-13 14:33:39'),
(6, 'REQ0005', 'MAT0001', 'STK26041319383547', 5, '2026-04-13 14:33:39', '2026-04-13 14:33:39'),
(7, 'REQ0006', 'MAT0001', 'STK26041319383547', 1, '2026-04-14 06:05:03', '2026-04-14 06:05:03'),
(8, 'REQ0006', 'MAT0016', 'STK26041413294410', 1, '2026-04-14 06:30:26', '2026-04-14 06:30:26'),
(9, 'REQ0007', 'MAT0008', 'STK26040609515761', 24, '2026-04-18 10:25:52', '2026-04-18 10:25:52'),
(10, 'REQ0007', 'MAT0008', 'STK26040610265489', 11, '2026-04-18 10:25:52', '2026-04-18 10:25:52'),
(11, 'REQ0007', 'MAT0011', 'STK26041818155457', 10, '2026-04-18 11:17:23', '2026-04-18 11:17:23'),
(12, 'REQ0007', 'MAT0011', 'STK26041818160482', 5, '2026-04-18 11:17:23', '2026-04-18 11:17:23'),
(13, 'REQ0007', 'MAT0011', 'STK26041818163287', 5, '2026-04-18 11:17:23', '2026-04-18 11:17:23'),
(14, 'REQ0007', 'MAT0016', 'STK26041818155469', 10, '2026-04-18 11:17:23', '2026-04-18 11:17:23'),
(15, 'REQ0007', 'MAT0016', 'STK26041818163288', 5, '2026-04-18 11:17:23', '2026-04-18 11:17:23'),
(16, 'REQ0008', 'MAT0007', 'STK26033109384685', 5, '2026-04-18 11:54:46', '2026-04-18 11:54:46'),
(17, 'REQ0009', 'MAT0007', 'STK26033109384685', 10, '2026-04-18 11:54:53', '2026-04-18 11:54:53');

-- --------------------------------------------------------

--
-- Table structure for table `penggunaan_material`
--

CREATE TABLE `penggunaan_material` (
  `id_penggunaan` varchar(20) NOT NULL,
  `id_permintaan` varchar(20) NOT NULL,
  `id_proyek` varchar(20) NOT NULL,
  `id_user_pelaksana` varchar(20) NOT NULL,
  `tanggal_laporan` date NOT NULL,
  `area_pekerjaan` varchar(150) DEFAULT NULL,
  `keterangan_umum` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `penggunaan_material`
--

INSERT INTO `penggunaan_material` (`id_penggunaan`, `id_permintaan`, `id_proyek`, `id_user_pelaksana`, `tanggal_laporan`, `area_pekerjaan`, `keterangan_umum`, `created_at`, `updated_at`) VALUES
('USE-20260314-001', 'REQ0001', 'PRY0001', 'USR0006', '2026-03-14', 'pemasanagn tehel', '', '2026-03-14 03:18:12', '2026-03-14 03:18:12'),
('USE-20260314-002', 'REQ0002', 'PRY0001', 'USR0006', '2026-03-14', 'Untuk lantai', '', '2026-03-14 04:14:43', '2026-03-14 04:14:43'),
('USE-20260401-001', 'REQ0004', 'PRY0001', 'USR0006', '2026-04-01', 'lt1', '', '2026-03-31 21:33:06', '2026-03-31 21:33:06'),
('USE-20260414-001', 'REQ0006', 'PRY0002', 'USR0006', '2026-04-14', 'renov lantai', '', '2026-04-14 06:37:47', '2026-04-14 06:37:47'),
('USE-20260418-001', 'REQ0009', 'PRY0001', 'USR0007', '2026-04-18', 'tembok', '', '2026-04-18 12:05:38', '2026-04-18 12:05:38');

-- --------------------------------------------------------

--
-- Table structure for table `pengiriman`
--

CREATE TABLE `pengiriman` (
  `id_pengiriman` varchar(50) NOT NULL,
  `id_kontrak` varchar(50) NOT NULL,
  `id_user_pengadaan` varchar(50) NOT NULL,
  `tanggal_berangkat` date NOT NULL,
  `estimasi_tanggal_tiba` date NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `status_pengiriman` enum('Pending','Dalam Perjalanan','Tiba di Lokasi','Return & Kirim Ulang','Selesai') DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pengiriman`
--

INSERT INTO `pengiriman` (`id_pengiriman`, `id_kontrak`, `id_user_pengadaan`, `tanggal_berangkat`, `estimasi_tanggal_tiba`, `keterangan`, `status_pengiriman`, `created_at`, `updated_at`) VALUES
('KRM0001', 'KON0001', 'USR0003', '2026-03-14', '2026-03-15', '', 'Selesai', '2026-03-14 03:11:06', '2026-03-14 03:12:29'),
('KRM0002', 'KON0001', 'USR0003', '2026-03-14', '2026-03-15', '', 'Selesai', '2026-03-14 03:11:54', '2026-03-14 03:14:38'),
('KRM0003', 'KON0001', 'USR0003', '2026-03-14', '2026-03-15', 'Penggantian Retur dari DO: KRM0002', 'Selesai', '2026-03-14 03:14:38', '2026-03-14 03:15:05'),
('KRM0004', 'KON0002', 'USR0003', '2026-03-14', '2026-03-15', '', 'Selesai', '2026-03-14 03:33:39', '2026-03-14 03:34:05'),
('KRM0005', 'KON0003', 'USR0003', '2026-03-28', '2026-03-29', '', 'Selesai', '2026-03-28 04:10:55', '2026-03-28 04:12:06'),
('KRM0006', 'KON0004', 'USR0003', '2026-03-31', '2026-04-01', '', 'Selesai', '2026-03-31 02:37:51', '2026-03-31 02:38:46'),
('KRM0007', 'KON0005', 'USR0003', '2026-04-01', '2026-04-02', '', 'Selesai', '2026-03-31 21:30:58', '2026-03-31 21:32:09'),
('KRM0008', 'KON0006', 'USR0003', '2026-04-06', '2026-04-07', '', 'Selesai', '2026-04-06 02:50:28', '2026-04-06 02:51:57'),
('KRM0009', 'KON0006', 'USR0003', '2026-04-06', '2026-04-07', '', 'Selesai', '2026-04-06 02:50:28', '2026-04-06 03:34:33'),
('KRM0010', 'KON0006', 'USR0003', '2026-04-06', '2026-04-07', 'Penggantian Retur dari DO: KRM0009', 'Selesai', '2026-04-06 03:34:33', '2026-04-06 03:35:23'),
('KRM0011', 'KON0007', 'USR0003', '2026-04-06', '2026-04-07', '', 'Selesai', '2026-04-06 06:21:07', '2026-04-06 06:23:21'),
('KRM0012', 'KON0007', 'USR0003', '2026-04-06', '2026-04-07', '', 'Selesai', '2026-04-06 06:21:07', '2026-04-06 06:24:50'),
('KRM0013', 'KON0007', 'USR0003', '2026-04-06', '2026-04-07', 'Penggantian Retur dari DO: KRM0012', 'Selesai', '2026-04-06 06:24:50', '2026-04-06 06:25:24'),
('KRM0014', 'KON0008', 'USR0003', '2026-04-08', '2026-04-09', '', 'Selesai', '2026-04-07 20:44:37', '2026-04-07 20:46:16'),
('KRM0015', 'KON0008', 'USR0003', '2026-04-08', '2026-04-09', '', 'Selesai', '2026-04-07 20:44:37', '2026-04-07 20:46:42'),
('KRM0016', 'KON0008', 'USR0003', '2026-04-08', '2026-04-09', 'Penggantian Retur dari DO: KRM0014', 'Selesai', '2026-04-07 20:46:16', '2026-04-14 03:10:53'),
('KRM0017', 'KON0007', 'USR0003', '2026-04-14', '2026-04-14', '', 'Selesai', '2026-04-13 11:42:29', '2026-04-13 11:45:40'),
('KRM0018', 'KON0007', 'USR0003', '2026-04-14', '2026-04-14', '', 'Selesai', '2026-04-13 11:42:29', '2026-04-13 11:44:03'),
('KRM0019', 'KON0007', 'USR0003', '2026-04-13', '2026-04-14', 'Penggantian Retur dari DO: KRM0017', 'Dalam Perjalanan', '2026-04-13 11:45:40', '2026-04-13 11:46:20'),
('KRM0020', 'KON0010', 'USR0003', '2026-04-14', '2026-04-14', '', 'Selesai', '2026-04-13 12:37:02', '2026-04-13 12:38:12'),
('KRM0021', 'KON0010', 'USR0003', '2026-04-14', '2026-04-14', '', 'Selesai', '2026-04-13 12:37:02', '2026-04-13 12:38:35'),
('KRM0022', 'KON0009', 'USR0003', '2026-04-14', '2026-04-14', '', 'Tiba di Lokasi', '2026-04-14 00:09:59', '2026-04-14 01:57:24'),
('KRM0023', 'KON0011', 'USR0003', '2026-04-14', '2026-04-14', '', 'Selesai', '2026-04-14 05:33:33', '2026-04-14 05:54:21'),
('KRM0024', 'KON0011', 'USR0003', '2026-04-16', '2026-04-17', 'Penggantian Retur dari DO: KRM0023', 'Tiba di Lokasi', '2026-04-14 05:54:21', '2026-04-17 12:22:25'),
('KRM0025', 'KON0012', 'USR0003', '2026-04-14', '2026-04-15', '', 'Selesai', '2026-04-14 06:22:26', '2026-04-14 06:27:26'),
('KRM0026', 'KON0012', 'USR0003', '2026-04-14', '2026-04-14', 'Penggantian Retur dari DO: KRM0025', 'Selesai', '2026-04-14 06:27:26', '2026-04-14 06:29:44'),
('KRM0027', 'KON0011', 'USR0003', '2026-04-18', '2026-04-18', '', 'Tiba di Lokasi', '2026-04-17 10:39:01', '2026-04-20 00:21:48'),
('KRM0028', 'KON0013', 'USR0003', '2026-04-19', '2026-04-19', '', 'Selesai', '2026-04-18 11:08:06', '2026-04-18 11:15:54'),
('KRM0029', 'KON0013', 'USR0003', '2026-04-19', '2026-04-19', '', 'Selesai', '2026-04-18 11:08:06', '2026-04-18 11:16:04'),
('KRM0030', 'KON0013', 'USR0003', '2026-04-19', '2026-04-19', '', 'Selesai', '2026-04-18 11:08:06', '2026-04-18 11:16:32');

-- --------------------------------------------------------

--
-- Table structure for table `pengiriman_detail`
--

CREATE TABLE `pengiriman_detail` (
  `id_pengiriman_detail` varchar(50) NOT NULL,
  `id_pengiriman` varchar(50) NOT NULL,
  `id_detail_kontrak` varchar(20) NOT NULL,
  `jumlah_dikirim` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pengiriman_detail`
--

INSERT INTO `pengiriman_detail` (`id_pengiriman_detail`, `id_pengiriman`, `id_detail_kontrak`, `jumlah_dikirim`, `created_at`, `updated_at`) VALUES
('KRD0001', 'KRM0001', 'DKO0001', 100, '2026-03-14 03:11:06', '2026-03-14 03:11:06'),
('KRD0002', 'KRM0001', 'DKO0002', 100, '2026-03-14 03:11:06', '2026-03-14 03:11:06'),
('KRD0003', 'KRM0002', 'DKO0001', 50, '2026-03-14 03:11:54', '2026-03-14 03:11:54'),
('KRD0004', 'KRM0003', 'DKO0001', 10, '2026-03-14 03:14:38', '2026-03-14 03:14:38'),
('KRD0005', 'KRM0004', 'DKO0003', 10, '2026-03-14 03:33:39', '2026-03-14 03:33:39'),
('KRD0006', 'KRM0004', 'DKO0004', 50, '2026-03-14 03:33:39', '2026-03-14 03:33:39'),
('KRD0007', 'KRM0005', 'DKO0005', 10, '2026-03-28 04:10:55', '2026-03-28 04:10:55'),
('KRD0008', 'KRM0006', 'DKO0006', 100, '2026-03-31 02:37:51', '2026-03-31 02:37:51'),
('KRD0009', 'KRM0007', 'DKO0007', 100000, '2026-03-31 21:30:58', '2026-03-31 21:30:58'),
('KRD0010', 'KRM0008', 'DKO0008', 25, '2026-04-06 02:50:28', '2026-04-06 02:50:28'),
('KRD0011', 'KRM0009', 'DKO0008', 25, '2026-04-06 02:50:28', '2026-04-06 02:50:28'),
('KRD0012', 'KRM0010', 'DKO0008', 5, '2026-04-06 03:34:33', '2026-04-06 03:34:33'),
('KRD0013', 'KRM0011', 'DKO0009', 25, '2026-04-06 06:21:07', '2026-04-06 06:21:07'),
('KRD0014', 'KRM0012', 'DKO0009', 25, '2026-04-06 06:21:07', '2026-04-06 06:21:07'),
('KRD0015', 'KRM0013', 'DKO0009', 5, '2026-04-06 06:24:50', '2026-04-06 06:24:50'),
('KRD0016', 'KRM0014', 'DKO0010', 5, '2026-04-07 20:44:37', '2026-04-07 20:44:37'),
('KRD0017', 'KRM0015', 'DKO0010', 5, '2026-04-07 20:44:37', '2026-04-07 20:44:37'),
('KRD0018', 'KRM0016', 'DKO0010', 3, '2026-04-07 20:46:16', '2026-04-07 20:46:16'),
('KRD0019', 'KRM0017', 'DKO0009', 25, '2026-04-13 11:42:29', '2026-04-13 11:42:29'),
('KRD0020', 'KRM0018', 'DKO0009', 25, '2026-04-13 11:42:29', '2026-04-13 11:42:29'),
('KRD0021', 'KRM0019', 'DKO0009', 5, '2026-04-13 11:45:40', '2026-04-13 11:45:40'),
('KRD0022', 'KRM0020', 'DKO0012', 25, '2026-04-13 12:37:02', '2026-04-13 12:37:02'),
('KRD0023', 'KRM0020', 'DKO0013', 25, '2026-04-13 12:37:02', '2026-04-13 12:37:02'),
('KRD0024', 'KRM0021', 'DKO0012', 25, '2026-04-13 12:37:02', '2026-04-13 12:37:02'),
('KRD0025', 'KRM0021', 'DKO0013', 25, '2026-04-13 12:37:02', '2026-04-13 12:37:02'),
('KRD0026', 'KRM0022', 'DKO0011', 10, '2026-04-14 00:09:59', '2026-04-14 00:09:59'),
('KRD0027', 'KRM0023', 'DKO0014', 5, '2026-04-14 05:33:33', '2026-04-14 05:33:33'),
('KRD0028', 'KRM0024', 'DKO0014', 5, '2026-04-14 05:54:21', '2026-04-14 05:54:21'),
('KRD0029', 'KRM0025', 'DKO0015', 1, '2026-04-14 06:22:26', '2026-04-14 06:22:26'),
('KRD0030', 'KRM0026', 'DKO0015', 1, '2026-04-14 06:27:26', '2026-04-14 06:27:26'),
('KRD0031', 'KRM0027', 'DKO0014', 5, '2026-04-17 10:39:01', '2026-04-17 10:39:01'),
('KRD0032', 'KRM0028', 'DKO0016', 10, '2026-04-18 11:08:06', '2026-04-18 11:08:06'),
('KRD0033', 'KRM0028', 'DKO0017', 10, '2026-04-18 11:08:06', '2026-04-18 11:08:06'),
('KRD0034', 'KRM0029', 'DKO0016', 5, '2026-04-18 11:08:06', '2026-04-18 11:08:06'),
('KRD0035', 'KRM0030', 'DKO0016', 5, '2026-04-18 11:08:06', '2026-04-18 11:08:06'),
('KRD0036', 'KRM0030', 'DKO0017', 5, '2026-04-18 11:08:06', '2026-04-18 11:08:06');

-- --------------------------------------------------------

--
-- Table structure for table `penugasan_proyek`
--

CREATE TABLE `penugasan_proyek` (
  `id_penugasan` varchar(20) NOT NULL,
  `id_user` varchar(20) NOT NULL,
  `id_proyek` varchar(20) NOT NULL,
  `peran_proyek` varchar(100) DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status_penugasan` enum('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `penugasan_proyek`
--

INSERT INTO `penugasan_proyek` (`id_penugasan`, `id_user`, `id_proyek`, `peran_proyek`, `tanggal_mulai`, `tanggal_selesai`, `status_penugasan`, `created_at`, `updated_at`) VALUES
('PNG0001', 'USR0006', 'PRY0001', 'Kepala Lapangan', '2026-03-09', '2026-08-31', 'Aktif', '2026-03-08 10:00:30', '2026-03-08 10:00:30'),
('PNG0002', 'USR0006', 'PRY0002', 'Mandor', '2026-03-15', '2026-06-10', 'Aktif', '2026-04-13 14:57:48', '2026-04-13 14:57:48'),
('PNG0003', 'USR0007', 'PRY0001', 'asisten mandor', '2026-04-19', '2026-08-31', 'Aktif', '2026-04-18 11:51:44', '2026-04-18 11:51:44');

-- --------------------------------------------------------

--
-- Table structure for table `penyesuaian_stok`
--

CREATE TABLE `penyesuaian_stok` (
  `id_penyesuaian` varchar(20) NOT NULL,
  `id_stok` varchar(20) NOT NULL,
  `id_material` varchar(20) NOT NULL,
  `id_user` varchar(20) NOT NULL,
  `jenis_penyesuaian` enum('Rusak','Hilang','Kadaluarsa','Selisih Opname') DEFAULT 'Rusak',
  `jumlah_penyesuaian` int NOT NULL,
  `keterangan` text NOT NULL,
  `bukti_foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `penyesuaian_stok`
--

INSERT INTO `penyesuaian_stok` (`id_penyesuaian`, `id_stok`, `id_material`, `id_user`, `jenis_penyesuaian`, `jumlah_penyesuaian`, `keterangan`, `bukti_foto`, `created_at`, `updated_at`) VALUES
('ADJ-20260331-165837', 'STK26033109384685', 'MAT0007', 'USR0005', 'Rusak', 1, 'karat', 'bukti_penyesuaian/Iaci4n0rUyZoob1U99zHpYhSmSmjs0v7UVk20fbz.webp', '2026-03-31 09:58:37', '2026-03-31 09:58:37'),
('ADJ-20260408-034720', 'STK26040609515761', 'MAT0008', 'USR0005', 'Rusak', 1, 'rusak', 'bukti_penyesuaian/uGllDEm53LHkSCN46gitiOKGGFsBjnTAzffRLAGt.png', '2026-04-07 20:47:20', '2026-04-07 20:47:20'),
('ADJ-20260413-203711', 'STK26033109384685', 'MAT0007', 'USR0005', 'Rusak', 1, 'karat kena air', 'bukti_penyesuaian/SJ2uRJZRQVJfNULmfYDagYgDbrNCP6zBBj0hbGoc.webp', '2026-04-13 13:37:11', '2026-04-13 13:37:11');

-- --------------------------------------------------------

--
-- Table structure for table `permintaan_proyek`
--

CREATE TABLE `permintaan_proyek` (
  `id_permintaan` varchar(20) NOT NULL,
  `id_proyek` varchar(20) NOT NULL,
  `id_user` varchar(20) NOT NULL,
  `tanggal_permintaan` date NOT NULL,
  `status_permintaan` enum('Menunggu Persetujuan','Disetujui PM','Diproses Sebagian','Selesai','Ditolak') DEFAULT 'Menunggu Persetujuan',
  `catatan_penolakan` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `permintaan_proyek`
--

INSERT INTO `permintaan_proyek` (`id_permintaan`, `id_proyek`, `id_user`, `tanggal_permintaan`, `status_permintaan`, `catatan_penolakan`, `created_at`, `updated_at`) VALUES
('REQ0001', 'PRY0001', 'USR0006', '2026-03-14', 'Selesai', NULL, '2026-03-14 02:38:12', '2026-03-14 03:15:56'),
('REQ0002', 'PRY0001', 'USR0006', '2026-03-14', 'Selesai', NULL, '2026-03-14 03:20:27', '2026-03-14 03:34:18'),
('REQ0003', 'PRY0001', 'USR0006', '2026-03-15', 'Selesai', NULL, '2026-03-14 20:51:05', '2026-03-28 04:12:14'),
('REQ0004', 'PRY0001', 'USR0006', '2026-04-01', 'Selesai', NULL, '2026-03-31 21:13:35', '2026-03-31 21:32:20'),
('REQ0005', 'PRY0001', 'USR0006', '2026-04-13', 'Selesai', NULL, '2026-04-13 14:00:34', '2026-04-13 14:33:39'),
('REQ0006', 'PRY0002', 'USR0006', '2026-04-14', 'Selesai', NULL, '2026-04-14 06:01:31', '2026-04-14 06:30:26'),
('REQ0007', 'PRY0002', 'USR0006', '2026-04-18', 'Selesai', NULL, '2026-04-18 08:32:36', '2026-04-18 11:17:23'),
('REQ0008', 'PRY0001', 'USR0006', '2026-04-18', 'Selesai', NULL, '2026-04-18 11:22:02', '2026-04-18 11:54:46'),
('REQ0009', 'PRY0001', 'USR0007', '2026-04-18', 'Selesai', NULL, '2026-04-18 11:53:12', '2026-04-18 11:54:53');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` varchar(20) NOT NULL,
  `id_pengajuan` varchar(20) NOT NULL,
  `id_supplier` varchar(20) NOT NULL,
  `id_user_pengadaan` varchar(20) NOT NULL,
  `nomor_pesanan` varchar(100) DEFAULT NULL,
  `tanggal_pesanan` date NOT NULL,
  `status_pesanan` enum('Draft','Proses Negosiasi','Dibatalkan','Berlanjut ke Kontrak') DEFAULT 'Draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `id_pengajuan`, `id_supplier`, `id_user_pengadaan`, `nomor_pesanan`, `tanggal_pesanan`, `status_pesanan`, `created_at`, `updated_at`) VALUES
('RFQ0001', 'PR0001', 'SUP0002', 'USR0003', 'RFQ/2026/03/001', '2026-03-14', 'Berlanjut ke Kontrak', '2026-03-14 03:08:49', '2026-03-14 03:10:33'),
('RFQ0002', 'PR0002', 'SUP0002', 'USR0003', 'RFQ/2026/03/002', '2026-03-14', 'Berlanjut ke Kontrak', '2026-03-14 03:31:12', '2026-03-14 03:32:55'),
('RFQ0003', 'PR0003', 'SUP0004', 'USR0003', 'RFQ/2026/03/003', '2026-03-28', 'Berlanjut ke Kontrak', '2026-03-28 04:05:19', '2026-03-28 04:06:45'),
('RFQ0004', 'PR0004', 'SUP0004', 'USR0003', 'RFQ/2026/03/004', '2026-03-31', 'Berlanjut ke Kontrak', '2026-03-31 02:23:22', '2026-03-31 02:24:59'),
('RFQ0005', 'PR0005', 'SUP0003', 'USR0003', 'RFQ/2026/03/005', '2026-04-01', 'Berlanjut ke Kontrak', '2026-03-31 16:34:57', '2026-04-06 06:17:04'),
('RFQ0006', 'PR0006', 'SUP0001', 'USR0003', 'RFQ/2026/04/001', '2026-04-01', 'Berlanjut ke Kontrak', '2026-03-31 21:16:58', '2026-03-31 21:21:26'),
('RFQ0007', 'PR0007', 'SUP0004', 'USR0003', 'RFQ/2026/04/002', '2026-04-01', 'Berlanjut ke Kontrak', '2026-03-31 21:41:43', '2026-03-31 21:42:48'),
('RFQ0008', 'PR0008', 'SUP0002', 'USR0003', 'RFQ/2026/04/003', '2026-04-08', 'Berlanjut ke Kontrak', '2026-04-07 20:33:54', '2026-04-07 20:37:05'),
('RFQ0009', 'PR0009', 'SUP0004', 'USR0003', 'RFQ/2026/04/004', '2026-04-08', 'Berlanjut ke Kontrak', '2026-04-07 22:55:09', '2026-04-07 23:32:47'),
('RFQ0010', 'PR0010', 'SUP0001', 'USR0003', 'RFQ/2026/04/005', '2026-04-13', 'Berlanjut ke Kontrak', '2026-04-13 12:33:44', '2026-04-13 12:34:55'),
('RFQ0011', 'PR0011', 'SUP0003', 'USR0003', 'RFQ/2026/04/006', '2026-04-14', 'Berlanjut ke Kontrak', '2026-04-14 03:13:00', '2026-04-14 03:13:46'),
('RFQ0012', 'PR0012', 'SUP0001', 'USR0003', 'RFQ/2026/04/007', '2026-04-14', 'Berlanjut ke Kontrak', '2026-04-14 06:07:39', '2026-04-14 06:12:10'),
('RFQ0013', 'PR0013', 'SUP0004', 'USR0003', 'RFQ/2026/04/008', '2026-04-18', 'Berlanjut ke Kontrak', '2026-04-18 10:26:57', '2026-04-18 11:01:37'),
('RFQ0014', 'PR0014', 'SUP0003', 'USR0003', 'RFQ/2026/04/009', '2026-04-19', 'Draft', '2026-04-19 01:12:55', '2026-04-19 01:12:55');

-- --------------------------------------------------------

--
-- Table structure for table `proyek`
--

CREATE TABLE `proyek` (
  `id_proyek` varchar(20) NOT NULL,
  `nama_proyek` varchar(150) NOT NULL,
  `lokasi_proyek` varchar(150) DEFAULT NULL,
  `deskripsi_proyek` text,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status_proyek` enum('Aktif','Selesai','Ditunda') NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `proyek`
--

INSERT INTO `proyek` (`id_proyek`, `nama_proyek`, `lokasi_proyek`, `deskripsi_proyek`, `tanggal_mulai`, `tanggal_selesai`, `status_proyek`, `created_at`, `updated_at`) VALUES
('PRY0001', 'Pembangunan Apartemen Senja', 'Jakarta Selatan', 'Proyek 10 Lantai', '2026-03-01', '2026-08-31', 'Aktif', '2026-03-08 16:52:19', '2026-03-08 16:52:19'),
('PRY0002', 'Renovasi Gedung Rektorat Kampus ABC', 'Bandung', 'Pekerjaan interior dan sipil', '2026-03-15', '2026-07-15', 'Aktif', '2026-03-08 16:52:19', '2026-03-08 16:52:19'),
('PRY0003', 'Pembangunan Perumahan Harmoni (Tahap 1)', 'Depok', 'Pembangunan 50 Unit Rumah Tipe 45', '2026-04-01', '2026-08-01', 'Aktif', '2026-03-08 16:52:19', '2026-03-08 16:52:19'),
('PRY0004', 'Gedung Perkantoran Sudirman Tower', 'Jakarta Pusat', 'Proyek High-Rise 25 Lantai', '2026-05-01', '2026-08-31', 'Ditunda', '2026-03-08 16:52:19', '2026-03-08 16:52:19'),
('PRY0005', 'Infrastruktur Jembatan Antar Desa', 'Bantul, Yogyakarta', 'Pekerjaan struktur baja dan beton', '2026-03-01', '2026-04-30', 'Selesai', '2026-03-08 16:52:19', '2026-03-08 16:52:19');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` varchar(20) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('LZYIrOuCdGoqfsAEL8mGTpVRCXj0xGJ1K1hWxxsI', 'USR0005', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWUVzdFBWano1UjREU2VkUkJtNk9qdkNvRFdCZHdiUlVaSmRvUm5mUiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpc3Rpay9wZXJtaW50YWFuLXByb3llayI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtzOjc6IlVTUjAwMDUiO30=', 1776669884);

-- --------------------------------------------------------

--
-- Table structure for table `stok_batch_fifo`
--

CREATE TABLE `stok_batch_fifo` (
  `id_stok` varchar(20) NOT NULL,
  `id_material` varchar(20) NOT NULL,
  `id_lokasi` varchar(20) DEFAULT NULL,
  `id_penerimaan` varchar(20) DEFAULT NULL,
  `tanggal_masuk` date NOT NULL,
  `jumlah_awal` int NOT NULL,
  `sisa_stok` int NOT NULL,
  `status_stok` enum('Tersedia','Habis') DEFAULT 'Tersedia',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `stok_batch_fifo`
--

INSERT INTO `stok_batch_fifo` (`id_stok`, `id_material`, `id_lokasi`, `id_penerimaan`, `tanggal_masuk`, `jumlah_awal`, `sisa_stok`, `status_stok`, `created_at`, `updated_at`) VALUES
('STK26031410122955', 'MAT0001', 'LOC0001', 'TRM0001', '2026-03-14', 100, 0, 'Tersedia', '2026-03-14 03:12:29', '2026-03-14 03:15:56'),
('STK26031410122977', 'MAT0003', 'LOC0001', 'TRM0001', '2026-03-14', 100, 0, 'Tersedia', '2026-03-14 03:12:29', '2026-03-14 03:15:56'),
('STK26031410130439', 'MAT0001', 'LOC0001', 'TRM0002', '2026-03-14', 40, 0, 'Tersedia', '2026-03-14 03:13:04', '2026-03-14 03:21:07'),
('STK26031410150570', 'MAT0001', 'LOC0001', 'TRM0003', '2026-03-14', 10, 0, 'Tersedia', '2026-03-14 03:15:05', '2026-03-14 03:21:07'),
('STK26031410340562', 'MAT0002', 'LOC0001', 'TRM0004', '2026-03-14', 50, 0, 'Tersedia', '2026-03-14 03:34:05', '2026-03-14 03:34:18'),
('STK26031410340588', 'MAT0001', 'LOC0001', 'TRM0004', '2026-03-14', 10, 0, 'Tersedia', '2026-03-14 03:34:05', '2026-03-14 03:34:18'),
('STK26032811120611', 'MAT0001', 'LOC0001', 'TRM0005', '2026-03-28', 10, 0, 'Tersedia', '2026-03-28 04:12:06', '2026-03-28 04:12:14'),
('STK26033109384685', 'MAT0007', 'LOC0003', 'TRM0006', '2026-03-31', 100, 83, 'Tersedia', '2026-03-31 02:38:46', '2026-04-18 11:54:53'),
('STK26040104320934', 'MAT0001', 'LOC0001', 'TRM0007', '2026-04-01', 100000, 0, 'Tersedia', '2026-03-31 21:32:09', '2026-03-31 21:32:20'),
('STK26040609515761', 'MAT0008', 'LOC0003', 'TRM0008', '2026-04-06', 25, 0, 'Tersedia', '2026-04-06 02:51:57', '2026-04-18 10:25:52'),
('STK26040610265489', 'MAT0008', 'LOC0003', 'TRM0009', '2026-04-06', 20, 9, 'Tersedia', '2026-04-06 03:26:54', '2026-04-18 10:25:52'),
('STK26040610352365', 'MAT0008', 'LOC0003', 'TRM0010', '2026-04-06', 5, 5, 'Tersedia', '2026-04-06 03:35:23', '2026-04-06 03:35:23'),
('STK26040613232139', 'MAT0010', 'LOC0004', 'TRM0011', '2026-04-06', 25, 0, 'Tersedia', '2026-04-06 06:23:21', '2026-04-13 14:33:39'),
('STK26040613242127', 'MAT0010', 'LOC0004', 'TRM0012', '2026-04-06', 20, 15, 'Tersedia', '2026-04-06 06:24:21', '2026-04-13 14:33:39'),
('STK26040613252498', 'MAT0010', 'LOC0004', 'TRM0013', '2026-04-06', 5, 5, 'Tersedia', '2026-04-06 06:25:24', '2026-04-06 06:25:24'),
('STK26040803455031', 'MAT0012', 'LOC0005', 'TRM0014', '2026-04-08', 2, 2, 'Tersedia', '2026-04-07 20:45:50', '2026-04-07 20:45:50'),
('STK26040803464297', 'MAT0012', 'LOC0005', 'TRM0015', '2026-04-08', 5, 5, 'Tersedia', '2026-04-07 20:46:42', '2026-04-07 20:46:42'),
('STK26041318440351', 'MAT0010', 'LOC0004', 'TRM0016', '2026-04-13', 25, 25, 'Tersedia', '2026-04-13 11:44:03', '2026-04-13 11:44:03'),
('STK26041318445718', 'MAT0010', 'LOC0004', 'TRM0017', '2026-04-13', 20, 20, 'Tersedia', '2026-04-13 11:44:57', '2026-04-13 11:44:57'),
('STK26041319381265', 'MAT0001', 'LOC0001', 'TRM0018', '2026-04-13', 25, 0, 'Tersedia', '2026-04-13 12:38:12', '2026-04-13 14:33:39'),
('STK26041319381293', 'MAT0004', 'LOC0001', 'TRM0018', '2026-04-13', 25, 10, 'Tersedia', '2026-04-13 12:38:12', '2026-04-13 13:28:29'),
('STK26041319383522', 'MAT0004', 'LOC0002', 'TRM0019', '2026-04-13', 15, 15, 'Tersedia', '2026-04-13 13:14:58', '2026-04-13 13:14:58'),
('STK26041319383523', 'MAT0004', 'LOC0001', 'TRM0019', '2026-04-13', 25, 10, 'Tersedia', '2026-04-13 12:38:35', '2026-04-13 13:14:58'),
('STK26041319383547', 'MAT0001', 'LOC0001', 'TRM0019', '2026-04-13', 25, 19, 'Tersedia', '2026-04-13 12:38:35', '2026-04-14 06:05:03'),
('STK26041320282943', 'MAT0004', 'LOC0002', 'TRM0018', '2026-04-13', 15, 15, 'Tersedia', '2026-04-13 13:28:29', '2026-04-13 13:28:29'),
('STK26041410105394', 'MAT0012', 'LOC0005', 'TRM0020', '2026-04-14', 3, 3, 'Tersedia', '2026-04-14 03:10:53', '2026-04-14 03:10:53'),
('STK26041413294410', 'MAT0016', 'LOC0007', 'TRM0023', '2026-04-14', 1, 0, 'Tersedia', '2026-04-14 06:29:44', '2026-04-14 06:30:26'),
('STK26041818155457', 'MAT0011', 'LOC0005', 'TRM0024', '2026-04-18', 10, 0, 'Tersedia', '2026-04-18 11:15:54', '2026-04-18 11:17:23'),
('STK26041818155469', 'MAT0016', 'LOC0007', 'TRM0024', '2026-04-18', 10, 0, 'Tersedia', '2026-04-18 11:15:54', '2026-04-18 11:17:23'),
('STK26041818160482', 'MAT0011', 'LOC0005', 'TRM0025', '2026-04-18', 5, 0, 'Tersedia', '2026-04-18 11:16:04', '2026-04-18 11:17:23'),
('STK26041818163287', 'MAT0011', 'LOC0005', 'TRM0026', '2026-04-18', 5, 0, 'Tersedia', '2026-04-18 11:16:32', '2026-04-18 11:17:23'),
('STK26041818163288', 'MAT0016', 'LOC0007', 'TRM0026', '2026-04-18', 5, 0, 'Tersedia', '2026-04-18 11:16:32', '2026-04-18 11:17:23');

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `id_supplier` varchar(20) NOT NULL,
  `nama_supplier` varchar(150) NOT NULL,
  `alamat` text,
  `kota` varchar(100) DEFAULT NULL,
  `kontak_person` varchar(100) DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status_supplier` enum('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`id_supplier`, `nama_supplier`, `alamat`, `kota`, `kontak_person`, `no_telepon`, `email`, `status_supplier`, `created_at`, `updated_at`) VALUES
('SUP0001', 'PT Bangun Jaya Indo', 'Jl. Industri No 1', 'Jakarta', 'Andi', '081234567890', 'sales@bangunjaya.com', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('SUP0002', 'CV Baja Besi Kuat', 'Jl. Logam No 25', 'Tangerang', 'Bowo Kusumo', '081987654321', 'order@bajakuat.com', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('SUP0003', 'UD Sumber Alam (Material Alam)', 'Jl. Raya Bogor Km 30', 'Depok', 'Haji Lulung', '085612349876', 'sumberalam@gmail.com', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('SUP0004', 'PT Warna Agung Nusantara', 'Kawasan Industri Cikarang', 'Bekasi', 'Sinta', '0218901234', 'corporate@warnaagung.co.id', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33'),
('SUP0005', 'Toko Besi & Listrik Sinar Makmur', 'Jl. Matraman Raya No 10', 'Jakarta Timur', 'Koh Aseng', '0218509988', 'sinarmakmur@yahoo.com', 'Aktif', '2026-03-08 16:13:33', '2026-03-08 16:13:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` varchar(20) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `ROLE` enum('Admin','Tim Pengadaan','Tim Pelaksanaan','Logistik','Top Manajemen') NOT NULL,
  `can_manage_master` tinyint(1) DEFAULT '0',
  `jabatan` varchar(100) DEFAULT NULL,
  `status_user` enum('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama_lengkap`, `email`, `password`, `is_active`, `ROLE`, `can_manage_master`, `jabatan`, `status_user`, `created_at`, `updated_at`) VALUES
('USR0001', 'Super Admin', 'admin@scm.com', '$2y$12$W99GOiql83X4AqawXzqjSerCmE84DJtbhnEFsl4x7zHMDYG59j69.', 1, 'Admin', 0, 'Super Administrator', 'Aktif', '2026-02-14 00:37:08', '2026-02-14 00:37:08'),
('USR0003', 'Siti Aminah', 'pengadaank@scm.com', '$2y$12$QFZtOv4ygSP2hj5TK2AE9..WlnwkMQ27h94Kbhm414RKGxGrCnAC6', 1, 'Tim Pengadaan', 0, 'staff pengadaan', 'Aktif', '2026-02-14 05:06:29', '2026-02-14 05:06:29'),
('USR0004', 'Desi Fitriani', 'topmj@scm.com', '$2y$12$JGcabJWN0mSw2fqx01W8a.GgB8OoO7rtpAUju/P5o83SgTfzlytDm', 1, 'Top Manajemen', 0, 'HRD', 'Aktif', '2026-03-04 13:34:53', '2026-03-04 13:34:53'),
('USR0005', 'Gabriel Liza', 'logistik@scm.com', '$2y$12$UjnOblUQYvzhfLgXDpSLBOtJq7WHNLnvSAm.0rJRVaKaoObMV6WhK', 1, 'Logistik', 1, 'Manejer Gudang', 'Aktif', '2026-03-04 13:50:11', '2026-04-18 22:11:19'),
('USR0006', 'Reindhito', 'pelaksanaan@scm.com', '$2y$12$W2p0JlTD.IP3Hy1CdAGcJevqdP4qV1odnfOljId0mQScHrtDFkFea', 1, 'Tim Pelaksanaan', 0, 'Mandor', 'Aktif', '2026-03-04 13:53:22', '2026-03-08 01:18:05'),
('USR0007', 'Theodor ', 'pelaksanaan1@scm.com', '$2y$12$rRKwhQPCJ.rAeF4zwcOqLO/sYVez0JcC495MATBBPMzyrRgO8njnm', 1, 'Tim Pelaksanaan', 0, 'lapangan', 'Aktif', '2026-04-18 11:44:21', '2026-04-18 11:44:21'),
('USR0008', 'Sebastian ', 'pengadaank1@scm.com', '$2y$12$yCoanAdrNcMb0JfkutP2HeEEHMbaMrq.TwOq0PGmGucAXvCT76vBS', 1, 'Tim Pengadaan', 0, 'Staff', 'Aktif', '2026-04-18 12:31:22', '2026-04-18 12:31:22'),
('USR0009', 'Loki Luther', 'logistik1@scm.com', '$2y$12$opygRYAlT.k5ajYlSZp6i.EllBQWbO3Xb6oLtAFrUeTYhkTXqIzXu', 1, 'Logistik', 0, 'Staff', 'Aktif', '2026-04-18 12:32:09', '2026-04-18 22:11:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `detail_kontrak`
--
ALTER TABLE `detail_kontrak`
  ADD PRIMARY KEY (`id_detail_kontrak`),
  ADD KEY `id_kontrak` (`id_kontrak`),
  ADD KEY `id_material` (`id_material`);

--
-- Indexes for table `detail_penerimaan`
--
ALTER TABLE `detail_penerimaan`
  ADD PRIMARY KEY (`id_detail_terima`),
  ADD KEY `fk_dp_penerimaan` (`id_penerimaan`),
  ADD KEY `fk_dp_pengiriman_det` (`id_pengiriman_detail`),
  ADD KEY `fk_dp_detail_kontrak` (`id_detail_kontrak`);

--
-- Indexes for table `detail_pengajuan_pembelian`
--
ALTER TABLE `detail_pengajuan_pembelian`
  ADD PRIMARY KEY (`id_detail_pengajuan`),
  ADD KEY `id_pengajuan` (`id_pengajuan`),
  ADD KEY `id_material` (`id_material`);

--
-- Indexes for table `detail_penggunaan_material`
--
ALTER TABLE `detail_penggunaan_material`
  ADD PRIMARY KEY (`id_detail_penggunaan`),
  ADD KEY `id_penggunaan` (`id_penggunaan`),
  ADD KEY `id_material` (`id_material`);

--
-- Indexes for table `detail_permintaan_proyek`
--
ALTER TABLE `detail_permintaan_proyek`
  ADD PRIMARY KEY (`id_detail_permintaan`),
  ADD KEY `id_permintaan` (`id_permintaan`),
  ADD KEY `id_material` (`id_material`);

--
-- Indexes for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id_detail_pesanan`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_material` (`id_material`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `invoice_pembelian`
--
ALTER TABLE `invoice_pembelian`
  ADD PRIMARY KEY (`id_invoice`),
  ADD KEY `id_kontrak` (`id_kontrak`),
  ADD KEY `fk_invoice_user` (`id_user`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategori_material`
--
ALTER TABLE `kategori_material`
  ADD PRIMARY KEY (`id_kategori_material`);

--
-- Indexes for table `kontrak`
--
ALTER TABLE `kontrak`
  ADD PRIMARY KEY (`id_kontrak`),
  ADD UNIQUE KEY `nomor_kontrak` (`nomor_kontrak`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_supplier` (`id_supplier`),
  ADD KEY `id_user_pengadaan` (`id_user_pengadaan`);

--
-- Indexes for table `master_lokasi_rak`
--
ALTER TABLE `master_lokasi_rak`
  ADD PRIMARY KEY (`id_lokasi`);

--
-- Indexes for table `material`
--
ALTER TABLE `material`
  ADD PRIMARY KEY (`id_material`),
  ADD KEY `material_id_kategori_material_foreign` (`id_kategori_material`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `penerimaan_material`
--
ALTER TABLE `penerimaan_material`
  ADD PRIMARY KEY (`id_penerimaan`),
  ADD KEY `fk_terima_pengiriman` (`id_pengiriman`);

--
-- Indexes for table `pengajuan_pembelian`
--
ALTER TABLE `pengajuan_pembelian`
  ADD PRIMARY KEY (`id_pengajuan`),
  ADD KEY `id_user_logistik` (`id_user_logistik`),
  ADD KEY `referensi_id_permintaan` (`referensi_id_permintaan`);

--
-- Indexes for table `pengeluaran_stok_fifo`
--
ALTER TABLE `pengeluaran_stok_fifo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_permintaan` (`id_permintaan`),
  ADD KEY `id_material` (`id_material`),
  ADD KEY `id_stok` (`id_stok`);

--
-- Indexes for table `penggunaan_material`
--
ALTER TABLE `penggunaan_material`
  ADD PRIMARY KEY (`id_penggunaan`),
  ADD KEY `id_permintaan` (`id_permintaan`),
  ADD KEY `id_proyek` (`id_proyek`),
  ADD KEY `id_user_pelaksana` (`id_user_pelaksana`);

--
-- Indexes for table `pengiriman`
--
ALTER TABLE `pengiriman`
  ADD PRIMARY KEY (`id_pengiriman`);

--
-- Indexes for table `pengiriman_detail`
--
ALTER TABLE `pengiriman_detail`
  ADD PRIMARY KEY (`id_pengiriman_detail`),
  ADD KEY `fk_pd_pengiriman` (`id_pengiriman`),
  ADD KEY `fk_pd_detail_kontrak` (`id_detail_kontrak`);

--
-- Indexes for table `penugasan_proyek`
--
ALTER TABLE `penugasan_proyek`
  ADD PRIMARY KEY (`id_penugasan`),
  ADD KEY `penugasan_proyek_id_user_foreign` (`id_user`),
  ADD KEY `penugasan_proyek_id_proyek_foreign` (`id_proyek`);

--
-- Indexes for table `penyesuaian_stok`
--
ALTER TABLE `penyesuaian_stok`
  ADD PRIMARY KEY (`id_penyesuaian`),
  ADD KEY `id_stok` (`id_stok`),
  ADD KEY `id_material` (`id_material`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `permintaan_proyek`
--
ALTER TABLE `permintaan_proyek`
  ADD PRIMARY KEY (`id_permintaan`),
  ADD KEY `id_proyek` (`id_proyek`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`),
  ADD UNIQUE KEY `nomor_pesanan` (`nomor_pesanan`),
  ADD KEY `id_pengajuan` (`id_pengajuan`),
  ADD KEY `id_supplier` (`id_supplier`),
  ADD KEY `id_user_pengadaan` (`id_user_pengadaan`);

--
-- Indexes for table `proyek`
--
ALTER TABLE `proyek`
  ADD PRIMARY KEY (`id_proyek`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `stok_batch_fifo`
--
ALTER TABLE `stok_batch_fifo`
  ADD PRIMARY KEY (`id_stok`),
  ADD KEY `id_material` (`id_material`),
  ADD KEY `id_lokasi` (`id_lokasi`),
  ADD KEY `id_penerimaan` (`id_penerimaan`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id_supplier`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `pengeluaran_stok_fifo`
--
ALTER TABLE `pengeluaran_stok_fifo`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_kontrak`
--
ALTER TABLE `detail_kontrak`
  ADD CONSTRAINT `detail_kontrak_ibfk_1` FOREIGN KEY (`id_kontrak`) REFERENCES `kontrak` (`id_kontrak`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_kontrak_ibfk_2` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`);

--
-- Constraints for table `detail_penerimaan`
--
ALTER TABLE `detail_penerimaan`
  ADD CONSTRAINT `fk_dp_detail_kontrak` FOREIGN KEY (`id_detail_kontrak`) REFERENCES `detail_kontrak` (`id_detail_kontrak`),
  ADD CONSTRAINT `fk_dp_penerimaan` FOREIGN KEY (`id_penerimaan`) REFERENCES `penerimaan_material` (`id_penerimaan`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_dp_pengiriman_det` FOREIGN KEY (`id_pengiriman_detail`) REFERENCES `pengiriman_detail` (`id_pengiriman_detail`);

--
-- Constraints for table `detail_pengajuan_pembelian`
--
ALTER TABLE `detail_pengajuan_pembelian`
  ADD CONSTRAINT `detail_pengajuan_pembelian_ibfk_1` FOREIGN KEY (`id_pengajuan`) REFERENCES `pengajuan_pembelian` (`id_pengajuan`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_pengajuan_pembelian_ibfk_2` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`);

--
-- Constraints for table `detail_penggunaan_material`
--
ALTER TABLE `detail_penggunaan_material`
  ADD CONSTRAINT `detail_penggunaan_material_ibfk_1` FOREIGN KEY (`id_penggunaan`) REFERENCES `penggunaan_material` (`id_penggunaan`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_penggunaan_material_ibfk_2` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`);

--
-- Constraints for table `detail_permintaan_proyek`
--
ALTER TABLE `detail_permintaan_proyek`
  ADD CONSTRAINT `detail_permintaan_proyek_ibfk_1` FOREIGN KEY (`id_permintaan`) REFERENCES `permintaan_proyek` (`id_permintaan`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_permintaan_proyek_ibfk_2` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`);

--
-- Constraints for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `detail_pesanan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_pesanan_ibfk_2` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`);

--
-- Constraints for table `invoice_pembelian`
--
ALTER TABLE `invoice_pembelian`
  ADD CONSTRAINT `fk_invoice_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE RESTRICT,
  ADD CONSTRAINT `invoice_pembelian_ibfk_1` FOREIGN KEY (`id_kontrak`) REFERENCES `kontrak` (`id_kontrak`) ON DELETE RESTRICT;

--
-- Constraints for table `kontrak`
--
ALTER TABLE `kontrak`
  ADD CONSTRAINT `kontrak_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`),
  ADD CONSTRAINT `kontrak_ibfk_2` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id_supplier`),
  ADD CONSTRAINT `kontrak_ibfk_3` FOREIGN KEY (`id_user_pengadaan`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `material`
--
ALTER TABLE `material`
  ADD CONSTRAINT `material_id_kategori_material_foreign` FOREIGN KEY (`id_kategori_material`) REFERENCES `kategori_material` (`id_kategori_material`);

--
-- Constraints for table `penerimaan_material`
--
ALTER TABLE `penerimaan_material`
  ADD CONSTRAINT `fk_terima_pengiriman` FOREIGN KEY (`id_pengiriman`) REFERENCES `pengiriman` (`id_pengiriman`);

--
-- Constraints for table `pengajuan_pembelian`
--
ALTER TABLE `pengajuan_pembelian`
  ADD CONSTRAINT `pengajuan_pembelian_ibfk_1` FOREIGN KEY (`id_user_logistik`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `pengajuan_pembelian_ibfk_2` FOREIGN KEY (`referensi_id_permintaan`) REFERENCES `permintaan_proyek` (`id_permintaan`);

--
-- Constraints for table `pengeluaran_stok_fifo`
--
ALTER TABLE `pengeluaran_stok_fifo`
  ADD CONSTRAINT `pengeluaran_stok_fifo_ibfk_1` FOREIGN KEY (`id_permintaan`) REFERENCES `permintaan_proyek` (`id_permintaan`) ON DELETE CASCADE,
  ADD CONSTRAINT `pengeluaran_stok_fifo_ibfk_2` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`) ON DELETE CASCADE,
  ADD CONSTRAINT `pengeluaran_stok_fifo_ibfk_3` FOREIGN KEY (`id_stok`) REFERENCES `stok_batch_fifo` (`id_stok`) ON DELETE CASCADE;

--
-- Constraints for table `penggunaan_material`
--
ALTER TABLE `penggunaan_material`
  ADD CONSTRAINT `penggunaan_material_ibfk_1` FOREIGN KEY (`id_permintaan`) REFERENCES `permintaan_proyek` (`id_permintaan`),
  ADD CONSTRAINT `penggunaan_material_ibfk_2` FOREIGN KEY (`id_proyek`) REFERENCES `proyek` (`id_proyek`),
  ADD CONSTRAINT `penggunaan_material_ibfk_3` FOREIGN KEY (`id_user_pelaksana`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `pengiriman_detail`
--
ALTER TABLE `pengiriman_detail`
  ADD CONSTRAINT `fk_pd_detail_kontrak` FOREIGN KEY (`id_detail_kontrak`) REFERENCES `detail_kontrak` (`id_detail_kontrak`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pd_pengiriman` FOREIGN KEY (`id_pengiriman`) REFERENCES `pengiriman` (`id_pengiriman`) ON DELETE CASCADE;

--
-- Constraints for table `penugasan_proyek`
--
ALTER TABLE `penugasan_proyek`
  ADD CONSTRAINT `penugasan_proyek_id_proyek_foreign` FOREIGN KEY (`id_proyek`) REFERENCES `proyek` (`id_proyek`) ON DELETE CASCADE,
  ADD CONSTRAINT `penugasan_proyek_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `penyesuaian_stok`
--
ALTER TABLE `penyesuaian_stok`
  ADD CONSTRAINT `penyesuaian_stok_ibfk_1` FOREIGN KEY (`id_stok`) REFERENCES `stok_batch_fifo` (`id_stok`),
  ADD CONSTRAINT `penyesuaian_stok_ibfk_2` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`),
  ADD CONSTRAINT `penyesuaian_stok_ibfk_3` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `permintaan_proyek`
--
ALTER TABLE `permintaan_proyek`
  ADD CONSTRAINT `permintaan_proyek_ibfk_1` FOREIGN KEY (`id_proyek`) REFERENCES `proyek` (`id_proyek`),
  ADD CONSTRAINT `permintaan_proyek_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`id_pengajuan`) REFERENCES `pengajuan_pembelian` (`id_pengajuan`),
  ADD CONSTRAINT `pesanan_ibfk_2` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id_supplier`),
  ADD CONSTRAINT `pesanan_ibfk_3` FOREIGN KEY (`id_user_pengadaan`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `stok_batch_fifo`
--
ALTER TABLE `stok_batch_fifo`
  ADD CONSTRAINT `stok_batch_fifo_ibfk_1` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`),
  ADD CONSTRAINT `stok_batch_fifo_ibfk_2` FOREIGN KEY (`id_lokasi`) REFERENCES `master_lokasi_rak` (`id_lokasi`),
  ADD CONSTRAINT `stok_batch_fifo_ibfk_3` FOREIGN KEY (`id_penerimaan`) REFERENCES `penerimaan_material` (`id_penerimaan`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
