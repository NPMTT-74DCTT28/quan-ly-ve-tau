-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 31, 2025 at 06:08 AM
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

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `sp_ThongKeDoanhSoNhanVien`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ThongKeDoanhSoNhanVien` (IN `p_thang` INT, IN `p_nam` INT)   BEGIN
    SELECT 
        nv.ma_nhan_vien,
        nv.ho_ten,
        COUNT(vt.id) as so_ve_ban,
        COALESCE(SUM(vt.gia_ve), 0) as doanh_so
    FROM nhan_vien nv
    LEFT JOIN ve_tau vt ON nv.id = vt.id_nhan_vien 
        AND MONTH(vt.ngay_dat) = p_thang 
        AND YEAR(vt.ngay_dat) = p_nam
        AND vt.trang_thai = 'Đã thanh toán'
    WHERE nv.vai_tro = 'Nhân viên'
    GROUP BY nv.id
    ORDER BY doanh_so DESC;
END$$

DROP PROCEDURE IF EXISTS `sp_ThongKeDoanhThuTheoNgay`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ThongKeDoanhThuTheoNgay` (IN `p_ngay_bat_dau` DATE, IN `p_ngay_ket_thuc` DATE)   BEGIN
    SELECT 
        DATE(ngay_dat) as ngay, 
        COALESCE(SUM(gia_ve), 0) as doanh_thu,
        COUNT(id) as so_ve_ban
    FROM ve_tau
    WHERE DATE(ngay_dat) BETWEEN p_ngay_bat_dau AND p_ngay_ket_thuc
      AND trang_thai = 'Đã thanh toán' 
    GROUP BY DATE(ngay_dat)
    ORDER BY ngay ASC;
END$$

DROP PROCEDURE IF EXISTS `sp_ThongKeDoanhThuTheoTuyen`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ThongKeDoanhThuTheoTuyen` (IN `p_ngay_bat_dau` DATE, IN `p_ngay_ket_thuc` DATE)   BEGIN
    SELECT 
        td.ten_tuyen,
        COALESCE(SUM(vt.gia_ve), 0) as doanh_thu
    FROM ve_tau vt
    JOIN lich_trinh lt ON vt.id_lich_trinh = lt.id
    JOIN tuyen_duong td ON lt.id_tuyen_duong = td.id
    WHERE DATE(vt.ngay_dat) BETWEEN p_ngay_bat_dau AND p_ngay_ket_thuc
      AND vt.trang_thai = 'Đã thanh toán'
    GROUP BY td.ten_tuyen
    ORDER BY doanh_thu DESC;
END$$

DROP PROCEDURE IF EXISTS `sp_ThongKeKhachHangVIP`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ThongKeKhachHangVIP` (IN `p_limit` INT)   BEGIN
    SELECT 
        kh.ho_ten,
        kh.sdt,
        COUNT(vt.id) as so_ve_da_mua,
        COALESCE(SUM(vt.gia_ve), 0) as tong_tien_chi_tieu
    FROM khach_hang kh
    JOIN ve_tau vt ON kh.id = vt.id_khach_hang
    WHERE vt.trang_thai = 'Đã thanh toán'
    GROUP BY kh.id
    ORDER BY tong_tien_chi_tieu DESC
    LIMIT p_limit;
END$$

DROP PROCEDURE IF EXISTS `sp_ThongKeTyLeLapDay`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ThongKeTyLeLapDay` (IN `p_ngay_bat_dau` DATE, IN `p_ngay_ket_thuc` DATE)   BEGIN
    SELECT 
        lt.ma_lich_trinh,
        t.ten_tau,
        lt.ngay_di,
        -- Đếm tổng số ghế của tàu (Dựa vào bảng ghe -> toa -> tau)
        (SELECT COUNT(g.id) 
         FROM ghe g 
         JOIN toa_tau tt ON g.id_toa_tau = tt.id 
         WHERE tt.id_tau = t.id) AS tong_so_ghe,
        -- Đếm số vé đã bán (trừ vé hủy)
        COUNT(vt.id) as ve_da_ban,
        -- Tính phần trăm
        ROUND((COUNT(vt.id) * 100.0 / NULLIF((SELECT COUNT(g.id) 
                                              FROM ghe g 
                                              JOIN toa_tau tt ON g.id_toa_tau = tt.id 
                                              WHERE tt.id_tau = t.id), 0)), 2) as ty_le_lap_day
    FROM lich_trinh lt
    JOIN tau t ON lt.id_tau = t.id
    LEFT JOIN ve_tau vt ON lt.id = vt.id_lich_trinh AND vt.trang_thai = 'Đã thanh toán'
    WHERE DATE(lt.ngay_di) BETWEEN p_ngay_bat_dau AND p_ngay_ket_thuc
    GROUP BY lt.id, t.id
    ORDER BY ty_le_lap_day DESC;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ga_tau`
--

DROP TABLE IF EXISTS `ga_tau`;
CREATE TABLE IF NOT EXISTS `ga_tau` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ma_ga` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'VD: HN, DN, SG',
  `ten_ga` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'VD: Ga Hà Nội',
  `dia_chi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thanh_pho` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ma_ga` (`ma_ga`),
  UNIQUE KEY `ten_ga` (`ten_ga`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ga_tau`
--

INSERT INTO `ga_tau` (`id`, `ma_ga`, `ten_ga`, `dia_chi`, `thanh_pho`) VALUES
(1, 'HN', 'Ga Hà Nội', '120 Lê Duẩn, Hoàn Kiếm', 'Hà Nội'),
(2, 'DN', 'Ga Đà Nẵng', '202 Hải Phòng, Thanh Khê', 'Đà Nẵng'),
(3, 'SG', 'Ga Sài Gòn', '1 Nguyễn Thông, Quận 3', 'TP. Hồ Chí Minh'),
(4, 'HUE', 'Ga Huế', '2 Bùi Thị Xuân', 'Thừa Thiên Huế'),
(5, 'NT', 'Ga Nha Trang', '17 Thái Nguyên', 'Khánh Hòa'),
(6, 'VINH', 'Ga Vinh', '1 Lệ Ninh', 'Nghệ An');

-- --------------------------------------------------------

--
-- Table structure for table `ghe`
--

DROP TABLE IF EXISTS `ghe`;
CREATE TABLE IF NOT EXISTS `ghe` (
  `id` int NOT NULL AUTO_INCREMENT,
  `so_ghe` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'VD: A1, B2',
  `id_toa_tau` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_ghe_trong_toa` (`so_ghe`,`id_toa_tau`),
  KEY `id_toa_tau` (`id_toa_tau`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ghe`
--

INSERT INTO `ghe` (`id`, `so_ghe`, `id_toa_tau`) VALUES
(1, '1A', 1),
(2, '1B', 1),
(3, '2A', 1),
(4, '2B', 1),
(5, '3A', 1),
(6, '3B', 1),
(11, 'C1', 3),
(12, 'C2', 3),
(13, 'C3', 3),
(14, 'C4', 3),
(7, 'G1', 2),
(8, 'G2', 2),
(9, 'G3', 2),
(10, 'G4', 2);

-- --------------------------------------------------------

--
-- Table structure for table `khach_hang`
--

DROP TABLE IF EXISTS `khach_hang`;
CREATE TABLE IF NOT EXISTS `khach_hang` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cccd` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ho_ten` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ngay_sinh` date DEFAULT (curdate()),
  `gioi_tinh` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sdt` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `dia_chi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sdt` (`sdt`),
  UNIQUE KEY `cccd` (`cccd`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `khach_hang`
--

INSERT INTO `khach_hang` (`id`, `cccd`, `ho_ten`, `ngay_sinh`, `gioi_tinh`, `sdt`, `dia_chi`) VALUES
(1, '001090000001', 'Phạm Khách Nam', '1985-02-10', 'Nam', '0912345678', '100 Cầu Giấy, Hà Nội'),
(2, '001090000002', 'Nguyễn Khách Nữ', '1992-11-20', 'Nữ', '0987654321', '200 Điện Biên Phủ, TP.HCM'),
(3, '001090000003', 'Lê Khách Khác', '2000-01-01', 'Khác', '0999888777', '300 Nguyễn Văn Linh, Đà Nẵng');

-- --------------------------------------------------------

--
-- Table structure for table `lich_trinh`
--

DROP TABLE IF EXISTS `lich_trinh`;
CREATE TABLE IF NOT EXISTS `lich_trinh` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ma_lich_trinh` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_tau` int NOT NULL,
  `id_tuyen_duong` int NOT NULL,
  `ngay_di` datetime NOT NULL,
  `ngay_den` datetime NOT NULL,
  `trang_thai` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_tau_schedule` (`id_tau`,`ngay_di`),
  UNIQUE KEY `ma_lich_trinh` (`ma_lich_trinh`),
  KEY `id_tuyen_duong` (`id_tuyen_duong`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lich_trinh`
--

INSERT INTO `lich_trinh` (`id`, `ma_lich_trinh`, `id_tau`, `id_tuyen_duong`, `ngay_di`, `ngay_den`, `trang_thai`) VALUES
(1, 'LT-SE1-001', 1, 1, '2025-05-01 06:00:00', '2025-05-02 12:00:00', 'Chờ'),
(2, 'LT-SE2-001', 2, 2, '2025-04-29 19:00:00', '2025-04-30 08:00:00', 'Đang chạy'),
(3, 'LT-TN1-OLD', 3, 2, '2024-12-01 08:00:00', '2024-12-01 20:00:00', 'Hoàn thành'),
(4, 'LT-QB1-HUY', 4, 4, '2025-05-05 07:00:00', '2025-05-05 14:00:00', 'Hủy');

-- --------------------------------------------------------

--
-- Table structure for table `loai_toa`
--

DROP TABLE IF EXISTS `loai_toa`;
CREATE TABLE IF NOT EXISTS `loai_toa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ten_loai` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'VD: Ngồi mềm điều hòa, Giường nằm',
  `he_so_gia` decimal(3,2) DEFAULT '1.00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ten_loai` (`ten_loai`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loai_toa`
--

INSERT INTO `loai_toa` (`id`, `ten_loai`, `he_so_gia`) VALUES
(1, 'Ngồi cứng', 1.00),
(2, 'Ngồi mềm điều hòa', 1.20),
(3, 'Giường nằm khoang 6', 1.50),
(4, 'Giường nằm khoang 4', 1.80);

-- --------------------------------------------------------

--
-- Table structure for table `nhan_vien`
--

DROP TABLE IF EXISTS `nhan_vien`;
CREATE TABLE IF NOT EXISTS `nhan_vien` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ma_nhan_vien` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mat_khau` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ho_ten` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ngay_sinh` date NOT NULL DEFAULT (curdate()),
  `gioi_tinh` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sdt` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dia_chi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `vai_tro` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ma_nhan_vien` (`ma_nhan_vien`),
  UNIQUE KEY `sdt` (`sdt`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nhan_vien`
--

INSERT INTO `nhan_vien` (`id`, `ma_nhan_vien`, `mat_khau`, `ho_ten`, `ngay_sinh`, `gioi_tinh`, `sdt`, `email`, `dia_chi`, `vai_tro`) VALUES
(2, 'NV001', '$2a$12$XUDz5TLpGqBa8LeDQfFIUurqjNF2S6GBE4Y/rS3uXhHT8NGALzcxu', 'Trần Thu Ngân', '2000-05-15', 'Nữ', '0909000222', 'ngan.tt@tauhoa.vn', 'Đà Nẵng', 'Nhân viên'),
(3, 'NV002', '$2a$12$XUDz5TLpGqBa8LeDQfFIUurqjNF2S6GBE4Y/rS3uXhHT8NGALzcxu', 'Lê Văn Soát Vé', '1995-08-20', 'Nam', '0909000333', 'soat.lv@tauhoa.vn', 'TP.HCM', 'Nhân viên');

-- --------------------------------------------------------

--
-- Table structure for table `tau`
--

DROP TABLE IF EXISTS `tau`;
CREATE TABLE IF NOT EXISTS `tau` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ma_tau` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'VD: SE1, TN1',
  `ten_tau` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'VD: Tàu Thống Nhất SE1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ma_tau` (`ma_tau`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tau`
--

INSERT INTO `tau` (`id`, `ma_tau`, `ten_tau`) VALUES
(1, 'SE1', 'Tàu Thống Nhất SE1 (Nhanh)'),
(2, 'SE2', 'Tàu Thống Nhất SE2 (Nhanh)'),
(3, 'TN1', 'Tàu Thống Nhất TN1 (Thường)'),
(4, 'QB1', 'Tàu Quảng Bình Express');

-- --------------------------------------------------------

--
-- Table structure for table `toa_tau`
--

DROP TABLE IF EXISTS `toa_tau`;
CREATE TABLE IF NOT EXISTS `toa_tau` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ma_toa` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'VD: Toa 1, Toa 2',
  `id_tau` int NOT NULL,
  `id_loai_toa` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_toa_trong_tau` (`ma_toa`,`id_tau`),
  KEY `id_tau` (`id_tau`),
  KEY `id_loai_toa` (`id_loai_toa`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `toa_tau`
--

INSERT INTO `toa_tau` (`id`, `ma_toa`, `id_tau`, `id_loai_toa`) VALUES
(1, 'Toa 1', 1, 2),
(2, 'Toa 2', 1, 4),
(3, 'Toa 3', 1, 1),
(4, 'Toa 1', 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tuyen_duong`
--

DROP TABLE IF EXISTS `tuyen_duong`;
CREATE TABLE IF NOT EXISTS `tuyen_duong` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ma_tuyen` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'VD: HN-SG',
  `ten_tuyen` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_ga_di` int NOT NULL,
  `id_ga_den` int NOT NULL,
  `khoang_cach_km` int DEFAULT NULL,
  `gia_co_ban` decimal(10,2) NOT NULL COMMENT 'Giá gốc chưa nhân hệ số',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ma_tuyen` (`ma_tuyen`),
  UNIQUE KEY `unique_route` (`id_ga_di`,`id_ga_den`),
  KEY `id_ga_den` (`id_ga_den`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tuyen_duong`
--

INSERT INTO `tuyen_duong` (`id`, `ma_tuyen`, `ten_tuyen`, `id_ga_di`, `id_ga_den`, `khoang_cach_km`, `gia_co_ban`) VALUES
(1, 'HN-SG', 'Hà Nội - Sài Gòn', 1, 3, 1726, 1000000.00),
(2, 'HN-DN', 'Hà Nội - Đà Nẵng', 1, 2, 791, 500000.00),
(3, 'SG-NT', 'Sài Gòn - Nha Trang', 3, 5, 411, 300000.00),
(4, 'HN-VINH', 'Hà Nội - Vinh', 1, 6, 319, 200000.00);

-- --------------------------------------------------------

--
-- Table structure for table `ve_tau`
--

DROP TABLE IF EXISTS `ve_tau`;
CREATE TABLE IF NOT EXISTS `ve_tau` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ma_ve` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_khach_hang` int NOT NULL,
  `id_lich_trinh` int NOT NULL,
  `id_ghe` int NOT NULL,
  `id_nhan_vien` int DEFAULT NULL,
  `ngay_dat` datetime DEFAULT CURRENT_TIMESTAMP,
  `gia_ve` decimal(10,2) NOT NULL,
  `trang_thai` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ma_ve` (`ma_ve`),
  UNIQUE KEY `unique_booking` (`id_lich_trinh`,`id_ghe`),
  KEY `id_khach_hang` (`id_khach_hang`),
  KEY `id_ghe` (`id_ghe`),
  KEY `id_nhan_vien` (`id_nhan_vien`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ve_tau`
--

INSERT INTO `ve_tau` (`id`, `ma_ve`, `id_khach_hang`, `id_lich_trinh`, `id_ghe`, `id_nhan_vien`, `ngay_dat`, `gia_ve`, `trang_thai`) VALUES
(1, 'VE-250501-001', 1, 1, 1, 2, '2025-12-29 20:08:42', 1200000.00, 'Đã thanh toán'),
(2, 'VE-250501-002', 2, 1, 2, 2, '2025-12-29 20:08:42', 1200000.00, 'Đã thanh toán');

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
