-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 21, 2025 at 05:12 PM
-- Server version: 8.4.7
-- PHP Version: 8.5.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quan_ly_ban_ve_tau`
--
CREATE DATABASE IF NOT EXISTS `quan_ly_ban_ve_tau` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `quan_ly_ban_ve_tau`;

-- --------------------------------------------------------

--
-- Table structure for table `ga_tau`
--

DROP TABLE IF EXISTS `ga_tau`;
CREATE TABLE IF NOT EXISTS `ga_tau` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ma_ga` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'VD: HN, DN, SG',
  `ten_ga` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'VD: Ga Hà Nội',
  `dia_chi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thanh_pho` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ma_ga` (`ma_ga`),
  UNIQUE KEY `ten_ga` (`ten_ga`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ghe`
--

DROP TABLE IF EXISTS `ghe`;
CREATE TABLE IF NOT EXISTS `ghe` (
  `id` int NOT NULL AUTO_INCREMENT,
  `so_ghe` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'VD: A1, B2',
  `id_toa_tau` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_ghe_trong_toa` (`so_ghe`,`id_toa_tau`),
  KEY `id_toa_tau` (`id_toa_tau`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `khach_hang`
--

DROP TABLE IF EXISTS `khach_hang`;
CREATE TABLE IF NOT EXISTS `khach_hang` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cccd` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ho_ten` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ngay_sinh` date DEFAULT (curdate()),
  `gioi_tinh` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sdt` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dia_chi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sdt` (`sdt`),
  UNIQUE KEY `cccd` (`cccd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lich_trinh`
--

DROP TABLE IF EXISTS `lich_trinh`;
CREATE TABLE IF NOT EXISTS `lich_trinh` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ma_lich_trinh` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_tau` int NOT NULL,
  `id_tuyen_duong` int NOT NULL,
  `ngay_di` datetime NOT NULL,
  `ngay_den` datetime NOT NULL,
  `trang_thai` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_tau_schedule` (`id_tau`,`ngay_di`),
  UNIQUE KEY `ma_lich_trinh` (`ma_lich_trinh`),
  KEY `id_tuyen_duong` (`id_tuyen_duong`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loai_toa`
--

DROP TABLE IF EXISTS `loai_toa`;
CREATE TABLE IF NOT EXISTS `loai_toa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ten_loai` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'VD: Ngồi mềm điều hòa, Giường nằm',
  `he_so_gia` decimal(3,2) DEFAULT '1.00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ten_loai` (`ten_loai`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nhan_vien`
--

DROP TABLE IF EXISTS `nhan_vien`;
CREATE TABLE IF NOT EXISTS `nhan_vien` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ma_nhan_vien` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mat_khau` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ho_ten` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ngay_sinh` date NOT NULL DEFAULT (curdate()),
  `gioi_tinh` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sdt` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dia_chi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vai_tro` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ma_nhan_vien` (`ma_nhan_vien`),
  UNIQUE KEY `sdt` (`sdt`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tau`
--

DROP TABLE IF EXISTS `tau`;
CREATE TABLE IF NOT EXISTS `tau` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ma_tau` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'VD: SE1, TN1',
  `ten_tau` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'VD: Tàu Thống Nhất SE1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ma_tau` (`ma_tau`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `toa_tau`
--

DROP TABLE IF EXISTS `toa_tau`;
CREATE TABLE IF NOT EXISTS `toa_tau` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ma_toa` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'VD: Toa 1, Toa 2',
  `id_tau` int NOT NULL,
  `id_loai_toa` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_toa_trong_tau` (`ma_toa`,`id_tau`),
  KEY `id_tau` (`id_tau`),
  KEY `id_loai_toa` (`id_loai_toa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tuyen_duong`
--

DROP TABLE IF EXISTS `tuyen_duong`;
CREATE TABLE IF NOT EXISTS `tuyen_duong` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ma_tuyen` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'VD: HN-SG',
  `ten_tuyen` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_ga_di` int NOT NULL,
  `id_ga_den` int NOT NULL,
  `khoang_cach_km` int DEFAULT NULL,
  `gia_co_ban` decimal(10,2) NOT NULL COMMENT 'Giá gốc chưa nhân hệ số',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ma_tuyen` (`ma_tuyen`),
  UNIQUE KEY `unique_route` (`id_ga_di`,`id_ga_den`),
  KEY `id_ga_den` (`id_ga_den`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ve_tau`
--

DROP TABLE IF EXISTS `ve_tau`;
CREATE TABLE IF NOT EXISTS `ve_tau` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ma_ve` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_khach_hang` int NOT NULL,
  `id_lich_trinh` int NOT NULL,
  `id_ghe` int NOT NULL,
  `id_nhan_vien` int DEFAULT NULL,
  `ngay_dat` datetime DEFAULT CURRENT_TIMESTAMP,
  `gia_ve` decimal(10,2) NOT NULL,
  `trang_thai` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ma_ve` (`ma_ve`),
  UNIQUE KEY `unique_booking` (`id_lich_trinh`,`id_ghe`),
  KEY `id_khach_hang` (`id_khach_hang`),
  KEY `id_ghe` (`id_ghe`),
  KEY `id_nhan_vien` (`id_nhan_vien`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ghe`
--
ALTER TABLE `ghe`
  ADD CONSTRAINT `ghe_ibfk_1` FOREIGN KEY (`id_toa_tau`) REFERENCES `toa_tau` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lich_trinh`
--
ALTER TABLE `lich_trinh`
  ADD CONSTRAINT `lich_trinh_ibfk_1` FOREIGN KEY (`id_tau`) REFERENCES `tau` (`id`),
  ADD CONSTRAINT `lich_trinh_ibfk_2` FOREIGN KEY (`id_tuyen_duong`) REFERENCES `tuyen_duong` (`id`);

--
-- Constraints for table `toa_tau`
--
ALTER TABLE `toa_tau`
  ADD CONSTRAINT `toa_tau_ibfk_1` FOREIGN KEY (`id_tau`) REFERENCES `tau` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `toa_tau_ibfk_2` FOREIGN KEY (`id_loai_toa`) REFERENCES `loai_toa` (`id`);

--
-- Constraints for table `tuyen_duong`
--
ALTER TABLE `tuyen_duong`
  ADD CONSTRAINT `tuyen_duong_ibfk_1` FOREIGN KEY (`id_ga_di`) REFERENCES `ga_tau` (`id`),
  ADD CONSTRAINT `tuyen_duong_ibfk_2` FOREIGN KEY (`id_ga_den`) REFERENCES `ga_tau` (`id`);

--
-- Constraints for table `ve_tau`
--
ALTER TABLE `ve_tau`
  ADD CONSTRAINT `ve_tau_ibfk_1` FOREIGN KEY (`id_khach_hang`) REFERENCES `khach_hang` (`id`),
  ADD CONSTRAINT `ve_tau_ibfk_2` FOREIGN KEY (`id_lich_trinh`) REFERENCES `lich_trinh` (`id`),
  ADD CONSTRAINT `ve_tau_ibfk_3` FOREIGN KEY (`id_ghe`) REFERENCES `ghe` (`id`),
  ADD CONSTRAINT `ve_tau_ibfk_4` FOREIGN KEY (`id_nhan_vien`) REFERENCES `nhan_vien` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
