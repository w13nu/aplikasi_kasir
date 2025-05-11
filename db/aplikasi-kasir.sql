-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 11, 2025 at 09:57 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aplikasi-kasir`
--

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `idkategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `tgl_dibuat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`idkategori`, `nama_kategori`, `tgl_dibuat`) VALUES
(12, 'Rokok', '2025-05-05 12:45:50'),
(13, 'Sembako', '2025-05-05 06:26:16'),
(14, 'Elektronik', '2025-05-05 06:26:23');

-- --------------------------------------------------------

--
-- Table structure for table `keranjang`
--

CREATE TABLE `keranjang` (
  `idcart` int(11) NOT NULL,
  `no_nota` varchar(100) NOT NULL,
  `idproduk` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `idlaporan` int(11) NOT NULL,
  `no_nota` varchar(50) NOT NULL,
  `idpelanggan` int(11) NOT NULL,
  `catatan` text NOT NULL,
  `totalbeli` int(11) NOT NULL,
  `metode` varchar(20) DEFAULT NULL,
  `pembayaran` int(11) NOT NULL,
  `kembalian` int(11) NOT NULL,
  `tgl_sub` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan`
--

INSERT INTO `laporan` (`idlaporan`, `no_nota`, `idpelanggan`, `catatan`, `totalbeli`, `metode`, `pembayaran`, `kembalian`, `tgl_sub`) VALUES
(48, 'AD55251930001', 6, '', 46000, NULL, 50000, 4000, '2025-05-05 12:31:18'),
(51, 'AD55252209701', 6, '', 18000, NULL, 50000, 32000, '2025-05-05 15:09:27'),
(54, 'AD55252214420', 6, '', 26000, NULL, 50000, 24000, '2025-05-05 15:14:36'),
(56, 'AD55252249045', 6, '', 30000, NULL, 50000, 20000, '2025-05-05 15:50:09'),
(57, 'AD95252132905', 6, '', 157000, NULL, 200000, 43000, '2025-05-09 14:32:28'),
(58, 'AD95252227291', 6, '', 35000, NULL, 50000, 15000, '2025-05-09 15:29:21'),
(59, 'AD95252258730', 6, '', 30000, 'QRIS', 50000, 20000, '2025-05-09 15:58:36');

-- --------------------------------------------------------

--
-- Table structure for table `laporan_data_produk`
--

CREATE TABLE `laporan_data_produk` (
  `kode_produk` varchar(20) NOT NULL,
  `nama_produk` varchar(100) DEFAULT NULL,
  `stok_awal` int(11) DEFAULT 0,
  `produk_masuk` int(11) DEFAULT 0,
  `produk_keluar` int(11) DEFAULT 0,
  `stok_akhir` int(11) GENERATED ALWAYS AS (`stok_awal` + `produk_masuk` - `produk_keluar`) STORED,
  `total_pembelian` decimal(12,2) DEFAULT 0.00,
  `total_penjualan` decimal(12,2) DEFAULT 0.00,
  `laba` decimal(12,2) GENERATED ALWAYS AS (`total_penjualan` - `total_pembelian`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `id_login` int(11) NOT NULL,
  `nama_toko` varchar(20) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(100) NOT NULL,
  `alamat` varchar(100) NOT NULL,
  `telepon` varchar(15) NOT NULL,
  `role` enum('admin','kasir') NOT NULL DEFAULT 'kasir'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`id_login`, `nama_toko`, `username`, `password`, `alamat`, `telepon`, `role`) VALUES
(1, 'TOKO ADE', 'admin', '$2y$10$tzNAIT9pw79JljGf8dWAM.wUeCKB1vloZiXBorPZMyMAxDM2YMP7O', 'Desa Karangsono Kecamatan Kwadungan Kabupaten Ngawi', '085666666', 'admin'),
(2, 'Toko Oke', 'kasir', '$2y$10$SFoxyUw.RYGKfDGA74stN.v0g4CWOVGcGDNFmEnL6ypyF6eAuuU1a', 'Ds. Karangsono', '0987654321', 'kasir');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `idpelanggan` int(11) NOT NULL,
  `nama_pelanggan` varchar(30) NOT NULL,
  `telepon_pelanggan` varchar(15) NOT NULL,
  `alamat_pelanggan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`idpelanggan`, `nama_pelanggan`, `telepon_pelanggan`, `alamat_pelanggan`) VALUES
(6, 'UMUM', '0', 'UMUM');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `idproduk` int(11) NOT NULL,
  `idkategori` int(11) NOT NULL,
  `kode_produk` varchar(100) NOT NULL,
  `nama_produk` varchar(150) NOT NULL,
  `harga_modal` int(11) NOT NULL,
  `harga_jual` int(11) NOT NULL,
  `stock` int(11) NOT NULL,
  `tgl_input` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `satuan` varchar(10) DEFAULT 'pcs'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`idproduk`, `idkategori`, `kode_produk`, `nama_produk`, `harga_modal`, `harga_jual`, `stock`, `tgl_input`, `satuan`) VALUES
(30, 12, 'PRD001', 'surya 12', 24000, 27000, 8, '2025-05-09 14:31:23', 'pcs'),
(31, 12, 'PRD002', 'surya 16', 24000, 30000, 21, '2025-05-09 16:27:17', 'pcs'),
(32, 12, 'PRD003', 'Djarum Espresso', 15000, 18000, 24, '2025-05-09 14:33:20', 'pcs'),
(33, 14, 'ELK001', 'Lampu kuning', 10000, 15000, 21, '2025-05-09 14:31:54', 'pcs'),
(34, 14, 'ELK002', 'kabel', 12000, 16000, 23, '2025-05-05 15:14:02', 'pcs'),
(35, 14, 'ELK003', 'olor', 20000, 26000, 29, '2025-05-05 15:14:22', 'pcs'),
(36, 13, 'SMB001', 'beras', 35000, 40000, 14, '2025-05-09 14:32:01', 'pcs'),
(37, 13, 'SMB002', 'ketan', 30000, 35000, 14, '2025-05-09 14:32:10', 'kg'),
(38, 13, 'SMB003', 'kopi', 25000, 35000, 19, '2025-05-09 15:27:34', 'kg'),
(39, 12, 'PRD004', 'Dunhill', 30000, 40000, 23, '2025-05-09 14:32:16', 'pcs'),
(40, 14, 'ELK004', 'Antena TV', 50000, 70000, 20, '2025-05-05 09:28:58', 'pcs'),
(42, 12, 'PRD005', 'korek', 5000, 7000, 20, '2025-05-05 10:00:31', 'pcs'),
(43, 13, 'SMB004', 'Gula', 50000, 80000, 20, '2025-05-05 10:17:08', 'kg'),
(44, 12, 'PRD006', 'Korek', 3000, 5000, 20, '2025-05-09 06:20:57', 'pcs');

-- --------------------------------------------------------

--
-- Table structure for table `tb_nota`
--

CREATE TABLE `tb_nota` (
  `idnota` int(11) NOT NULL,
  `no_nota` varchar(100) NOT NULL,
  `idproduk` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `tgl_nota` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_nota`
--

INSERT INTO `tb_nota` (`idnota`, `no_nota`, `idproduk`, `quantity`, `tgl_nota`) VALUES
(79, 'AD55251930001', 31, 1, '2025-05-09'),
(80, 'AD55251930001', 34, 1, '2025-05-09'),
(86, 'AD55252209701', 32, 1, '2025-05-09'),
(89, 'AD55252214420', 35, 1, '2025-05-09'),
(91, 'AD55252249045', 31, 1, '2025-05-09'),
(92, 'AD95252132905', 30, 1, '2025-05-09'),
(93, 'AD95252132905', 33, 1, '2025-05-09'),
(94, 'AD95252132905', 36, 1, '2025-05-09'),
(95, 'AD95252132905', 37, 1, '2025-05-09'),
(96, 'AD95252132905', 39, 1, '2025-05-09'),
(99, 'AD95252227291', 38, 1, '2025-05-09'),
(100, 'AD95252258730', 31, 1, '2025-05-09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`idkategori`);

--
-- Indexes for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`idcart`);

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`idlaporan`);

--
-- Indexes for table `laporan_data_produk`
--
ALTER TABLE `laporan_data_produk`
  ADD PRIMARY KEY (`kode_produk`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id_login`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`idpelanggan`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`idproduk`);

--
-- Indexes for table `tb_nota`
--
ALTER TABLE `tb_nota`
  ADD PRIMARY KEY (`idnota`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `idkategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `idcart` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=164;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `idlaporan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `id_login` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `idpelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `idproduk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `tb_nota`
--
ALTER TABLE `tb_nota`
  MODIFY `idnota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
