-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 25, 2025
-- Server version: 8.0.30
-- PHP Version: 8.1.12
-- Create by: tho493

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Tạo database
CREATE DATABASE IF NOT EXISTS `khao_sat_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `khao_sat_db`;

-- --------------------------------------------------------

-- Bảng tài khoản quản trị
CREATE TABLE IF NOT EXISTS `taikhoan` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tendangnhap` VARCHAR(50) NOT NULL UNIQUE,
  `matkhau` VARCHAR(255) NOT NULL, -- Tăng độ dài để hỗ trợ hash mạnh hơn
  `hoten` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100),
  `sodienthoai` VARCHAR(20),
  -- `quyen` ENUM('admin', 'manager', 'viewer') DEFAULT 'viewer', -- Dành cho phân quyền
  `trangthai` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME ON UPDATE CURRENT_TIMESTAMP,
  `last_login` DATETIME,
  PRIMARY KEY (`id`),
  KEY `idx_tendangnhap` (`tendangnhap`),
  KEY `idx_trangthai` (`trangthai`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng phân quyền chi tiết.
-- CREATE TABLE IF NOT EXISTS `phanquyen` (
--   `id` INT(11) NOT NULL AUTO_INCREMENT,
--   `taikhoan_id` INT(11) NOT NULL,
--   `chucnang` VARCHAR(50) NOT NULL,
--   `quyen` ENUM('view', 'create', 'edit', 'delete', 'full') DEFAULT 'view',
--   PRIMARY KEY (`id`),
--   UNIQUE KEY `unique_taikhoan_chucnang` (`taikhoan_id`, `chucnang`),
--   CONSTRAINT `fk_phanquyen_taikhoan` FOREIGN KEY (`taikhoan_id`) 
--     REFERENCES `taikhoan` (`id`) ON DELETE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng năm học
CREATE TABLE IF NOT EXISTS `namhoc` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `namhoc` VARCHAR(10) NOT NULL UNIQUE,
  `trangthai` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_namhoc` (`namhoc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- CÁC BẢNG QUẢN LÝ KHẢO SÁT
-- --------------------------------------------------------

-- Bảng mẫu khảo sát
CREATE TABLE IF NOT EXISTS `mau_khaosat` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ten_mau` VARCHAR(255) NOT NULL,
  `mota` TEXT,
  `version` INT DEFAULT 1,
  `trangthai` ENUM('draft', 'active', 'inactive') DEFAULT 'draft',
  `nguoi_tao_id` INT(11),
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_trangthai` (`trangthai`),
  KEY `idx_nguoi_tao` (`nguoi_tao_id`),
  CONSTRAINT `fk_mau_nguoitao` FOREIGN KEY (`nguoi_tao_id`) 
    REFERENCES `taikhoan` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng câu hỏi khảo sát
CREATE TABLE IF NOT EXISTS `cauhoi_khaosat` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `mau_khaosat_id` INT(11) NOT NULL,
  `noidung_cauhoi` TEXT NOT NULL,
  `loai_cauhoi` ENUM('single_choice', 'multiple_choice', 'text', 'likert', 'rating', 'date', 'number') DEFAULT 'single_choice',
  `batbuoc` TINYINT(1) DEFAULT 1,
  `thutu` INT DEFAULT 0,
  `cau_dieukien_id` INT(11), -- Câu hỏi phụ thuộc
  `dieukien_hienthi` JSON, -- Điều kiện hiển thị câu hỏi
  `trangthai` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_mau_khaosat` (`mau_khaosat_id`),
  KEY `idx_cau_dieukien` (`cau_dieukien_id`),
  CONSTRAINT `fk_cauhoi_mau` FOREIGN KEY (`mau_khaosat_id`) 
    REFERENCES `mau_khaosat` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng phương án trả lời
CREATE TABLE IF NOT EXISTS `phuongan_traloi` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `cauhoi_id` INT(11) NOT NULL,
  `noidung` VARCHAR(500) NOT NULL,
  `giatri` VARCHAR(50),
  `thutu` INT DEFAULT 0,
  `cho_nhap_khac` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cauhoi` (`cauhoi_id`),
  CONSTRAINT `fk_phuongan_cauhoi` FOREIGN KEY (`cauhoi_id`) 
    REFERENCES `cauhoi_khaosat` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng đợt khảo sát
CREATE TABLE IF NOT EXISTS `dot_khaosat` (
  `id` VARCHAR(50) NOT NULL,
  `ten_dot` VARCHAR(255) NOT NULL,
  `mau_khaosat_id` INT(11) NOT NULL,
  `namhoc_id` INT(11),
  `tungay` DATE NOT NULL,
  `denngay` DATE NOT NULL,
  `trangthai` ENUM('draft', 'active', 'closed') DEFAULT 'draft',
  `mota` TEXT,
  `nguoi_tao_id` INT(11),
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_mau_khaosat` (`mau_khaosat_id`),
  KEY `idx_namhoc` (`namhoc_id`),
  KEY `idx_trangthai_ngay` (`trangthai`, `tungay`, `denngay`),
  CONSTRAINT `fk_dot_mau` FOREIGN KEY (`mau_khaosat_id`) 
    REFERENCES `mau_khaosat` (`id`),
  CONSTRAINT `fk_dot_namhoc` FOREIGN KEY (`namhoc_id`) 
    REFERENCES `namhoc` (`id`),
  CONSTRAINT `fk_dot_nguoitao` FOREIGN KEY (`nguoi_tao_id`) 
    REFERENCES `taikhoan` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng phiếu khảo sát
CREATE TABLE IF NOT EXISTS `phieu_khaosat` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `dot_khaosat_id` VARCHAR(50) NOT NULL,
  `ma_nguoi_traloi` VARCHAR(50), -- Mã SV, mã NV, mã DN...
  `metadata` JSON, -- Thông tin người trả lời (họ tên, đơn vị, email...)
  `trangthai` ENUM('draft', 'completed') DEFAULT 'draft',
  `thoigian_batdau` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `thoigian_hoanthanh` DATETIME,
  `ip_address` VARCHAR(45),
  `user_agent` TEXT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_dot_khaosat` (`dot_khaosat_id`),
  KEY `idx_ma_nguoi_traloi` (`ma_nguoi_traloi`),
  KEY `idx_trangthai` (`trangthai`),
  KEY `idx_thoigian` (`thoigian_hoanthanh`),
  CONSTRAINT `fk_phieu_dot` FOREIGN KEY (`dot_khaosat_id`) 
    REFERENCES `dot_khaosat` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng chi tiết phiếu khảo sát
CREATE TABLE IF NOT EXISTS `phieu_khaosat_chitiet` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `phieu_khaosat_id` INT(11) NOT NULL,
  `cauhoi_id` INT(11) NOT NULL,
  `phuongan_id` INT(11),
  `giatri_text` TEXT,
  `giatri_number` DECIMAL(10,2),
  `giatri_date` DATE,
  `giatri_json` JSON, -- Cho multiple choice
  `thoigian` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_phieu` (`phieu_khaosat_id`),
  KEY `idx_cauhoi` (`cauhoi_id`),
  KEY `idx_phuongan` (`phuongan_id`),
  CONSTRAINT `fk_chitiet_phieu` FOREIGN KEY (`phieu_khaosat_id`) 
    REFERENCES `phieu_khaosat` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_chitiet_cauhoi` FOREIGN KEY (`cauhoi_id`) 
    REFERENCES `cauhoi_khaosat` (`id`),
  CONSTRAINT `fk_chitiet_phuongan` FOREIGN KEY (`phuongan_id`) 
    REFERENCES `phuongan_traloi` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Bảng lịch sử thay đổi
CREATE TABLE IF NOT EXISTS `lichsu_thaydoi` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `bang_thaydoi` VARCHAR(50) NOT NULL,
  `id_banghi` INT(11) NOT NULL,
  `nguoi_thuchien_id` INT(11),
  `hanhdong` ENUM('create', 'update', 'delete', 'publish', 'close') NOT NULL,
  `noidung_cu` JSON,
  `noidung_moi` JSON,
  `ghi_chu` TEXT,
  `thoigian` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_bang_id` (`bang_thaydoi`, `id_banghi`),
  KEY `idx_nguoi_thuchien` (`nguoi_thuchien_id`),
  KEY `idx_thoigian` (`thoigian`),
  CONSTRAINT `fk_lichsu_nguoi` FOREIGN KEY (`nguoi_thuchien_id`) 
    REFERENCES `taikhoan` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng cấu hình hệ thống
CREATE TABLE IF NOT EXISTS `cau_hinh_dich_vu` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ma_cauhinh` VARCHAR(50) NOT NULL UNIQUE,
  `giatri` TEXT,
  `mota` VARCHAR(255),
  `nhom_cauhinh` VARCHAR(50),
  `updated_at` DATETIME ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ma_cauhinh` (`ma_cauhinh`),
  KEY `idx_nhom` (`nhom_cauhinh`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng thông báo
CREATE TABLE IF NOT EXISTS `thongbao` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tieude` VARCHAR(255) NOT NULL,
  `noidung` TEXT,
  `loai_thongbao` ENUM('info', 'warning', 'error', 'success') DEFAULT 'info',
  -- `doi_tuong` VARCHAR(50), 
  `dot_khaosat_id` VARCHAR(50),
  `trangthai` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `ngay_hethan` DATETIME,
  PRIMARY KEY (`id`),
  KEY `idx_trangthai_ngay` (`trangthai`, `created_at`, `ngay_hethan`),
  KEY `idx_dot_khaosat` (`dot_khaosat_id`),
  CONSTRAINT `fk_thongbao_dot` FOREIGN KEY (`dot_khaosat_id`) 
    REFERENCES `dot_khaosat` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng template email
CREATE TABLE IF NOT EXISTS `template_email` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ma_template` VARCHAR(50) NOT NULL UNIQUE,
  `ten_template` VARCHAR(255) NOT NULL,
  `tieude` VARCHAR(255) NOT NULL,
  `noidung` TEXT NOT NULL,
  `bien_template` JSON, -- Các biến có thể sử dụng
  `trangthai` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_ma_template` (`ma_template`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- CÁC VIEW ĐỂ TRUY VẤN NHANH
-- --------------------------------------------------------

-- View thống kê đợt khảo sát
CREATE OR REPLACE VIEW v_thongke_dot_khaosat AS
SELECT 
  dk.id,
  dk.ten_dot,
  dk.tungay,
  dk.denngay,
  dk.trangthai,
  mk.ten_mau,
  COUNT(DISTINCT pk.id) AS tong_phieu,
  COUNT(DISTINCT CASE WHEN pk.trangthai = 'completed' THEN pk.id END) AS phieu_hoanthanh,
  ROUND(COUNT(DISTINCT CASE WHEN pk.trangthai = 'completed' THEN pk.id END) * 100.0 / 
        NULLIF(COUNT(DISTINCT pk.id), 0), 2) AS ty_le_hoanthanh
FROM dot_khaosat dk
LEFT JOIN mau_khaosat mk ON dk.mau_khaosat_id = mk.id
LEFT JOIN phieu_khaosat pk ON dk.id = pk.dot_khaosat_id
GROUP BY dk.id;

-- View danh sách khảo sát đang hoạt động
CREATE OR REPLACE VIEW v_khaosat_hoatdong AS
SELECT 
  dk.*,
  mk.ten_mau
FROM dot_khaosat dk
JOIN mau_khaosat mk ON dk.mau_khaosat_id = mk.id
WHERE dk.trangthai = 'active'
  AND CURDATE() BETWEEN dk.tungay AND dk.denngay;

-- --------------------------------------------------------
-- CÁC STORED PROCEDURES
-- --------------------------------------------------------

DELIMITER //

-- Procedure tạo mẫu khảo sát mới
CREATE PROCEDURE sp_TaoMauKhaoSat(
  IN p_ten_mau VARCHAR(255),
  IN p_mota TEXT,
  IN p_nguoi_tao_id INT
)
BEGIN
  DECLARE v_mau_id INT;
  
  -- Tạo mẫu khảo sát
  INSERT INTO mau_khaosat (ten_mau, mota, nguoi_tao_id)
  VALUES (p_ten_mau, p_mota, p_nguoi_tao_id);
  
  SET v_mau_id = LAST_INSERT_ID();
  
  -- Ghi log
  INSERT INTO lichsu_thaydoi (bang_thaydoi, id_banghi, nguoi_thuchien_id, hanhdong)
  VALUES ('mau_khaosat', v_mau_id, p_nguoi_tao_id, 'create');
  
  SELECT v_mau_id AS mau_khaosat_id;
END//

DELIMITER ;

DELIMITER //

-- Procedure sao chép mẫu khảo sát
CREATE PROCEDURE sp_SaoChepMauKhaoSat(
  IN p_mau_goc_id INT,
  IN p_ten_mau_moi VARCHAR(255),
  IN p_nguoi_tao_id INT
)
BEGIN
  DECLARE v_mau_moi_id INT;
  
  -- Tạo mẫu mới
  INSERT INTO mau_khaosat (ten_mau, mota, nguoi_tao_id)
  SELECT p_ten_mau_moi, CONCAT('Sao chép từ: ', ten_mau), p_nguoi_tao_id
  FROM mau_khaosat WHERE id = p_mau_goc_id;
  
  SET v_mau_moi_id = LAST_INSERT_ID();
  
  -- Sao chép câu hỏi và phương án
  INSERT INTO cauhoi_khaosat (mau_khaosat_id, noidung_cauhoi, loai_cauhoi, batbuoc, thutu)
  SELECT v_mau_moi_id, noidung_cauhoi, loai_cauhoi, batbuoc, thutu
  FROM cauhoi_khaosat c WHERE mau_khaosat_id = p_mau_goc_id;
  
  -- Ghi log
  INSERT INTO lichsu_thaydoi (bang_thaydoi, id_banghi, nguoi_thuchien_id, hanhdong, ghi_chu)
  VALUES ('mau_khaosat', v_mau_moi_id, p_nguoi_tao_id, 'create', 
          CONCAT('Sao chép từ mẫu ID: ', p_mau_goc_id));
  
  SELECT v_mau_moi_id AS mau_khaosat_id;
END//

DELIMITER ;

DELIMITER //

-- Procedure tạo đợt khảo sát
CREATE PROCEDURE sp_TaoDotKhaoSat(
  IN p_ten_dot VARCHAR(255),
  IN p_mau_khaosat_id INT,
  IN p_namhoc_id INT,
  IN p_tungay DATE,
  IN p_denngay DATE,
  IN p_nguoi_tao_id INT
)
BEGIN
  DECLARE v_dot_id INT;
  
  -- Kiểm tra ngày hợp lệ
  IF p_tungay > p_denngay THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Ngày bắt đầu phải trước ngày kết thúc';
  END IF;
  
  -- Tạo đợt khảo sát
  INSERT INTO dot_khaosat (ten_dot, mau_khaosat_id, namhoc_id, tungay, denngay, nguoi_tao_id)
  VALUES (p_ten_dot, p_mau_khaosat_id, p_namhoc_id, p_tungay, p_denngay, p_nguoi_tao_id);
  
  SET v_dot_id = LAST_INSERT_ID();
  
  -- Ghi log
  INSERT INTO lichsu_thaydoi (bang_thaydoi, id_banghi, nguoi_thuchien_id, hanhdong)
  VALUES ('dot_khaosat', v_dot_id, p_nguoi_tao_id, 'create');
  
  SELECT v_dot_id AS dot_khaosat_id;
END//

DELIMITER ;

DELIMITER //

-- Procedure xuất kết quả khảo sát (MySQL syntax)
CREATE PROCEDURE sp_XuatKetQuaKhaoSat(
  IN p_dot_khaosat_id INT,
  IN p_cauhoi_id INT
)
BEGIN
  IF p_cauhoi_id IS NULL OR p_cauhoi_id = 0 THEN
    -- Xuất tất cả câu hỏi
    SELECT 
      ch.id AS cauhoi_id,
      ch.noidung_cauhoi,
      ch.loai_cauhoi,
      pt.noidung AS phuongan,
      COUNT(pc.id) AS so_luong,
      ROUND(COUNT(pc.id) * 100.0 / 
            (SELECT COUNT(DISTINCT pc2.phieu_khaosat_id) 
             FROM phieu_khaosat_chitiet pc2 
             INNER JOIN phieu_khaosat pk2 ON pc2.phieu_khaosat_id = pk2.id
             WHERE pc2.cauhoi_id = ch.id AND pk2.dot_khaosat_id = p_dot_khaosat_id), 2) AS ty_le
    FROM cauhoi_khaosat ch
    LEFT JOIN phuongan_traloi pt ON ch.id = pt.cauhoi_id
    LEFT JOIN phieu_khaosat_chitiet pc ON pt.id = pc.phuongan_id
    LEFT JOIN phieu_khaosat pk ON pc.phieu_khaosat_id = pk.id
    WHERE pk.dot_khaosat_id = p_dot_khaosat_id OR pk.dot_khaosat_id IS NULL
    GROUP BY ch.id, pt.id
    ORDER BY ch.thutu, pt.thutu;
  ELSE
    SELECT 
      pt.noidung AS phuongan,
      COUNT(pc.id) AS so_luong,
      ROUND(COUNT(pc.id) * 100.0 / 
            (SELECT COUNT(DISTINCT pc2.phieu_khaosat_id) 
             FROM phieu_khaosat_chitiet pc2
             INNER JOIN phieu_khaosat pk2 ON pc2.phieu_khaosat_id = pk2.id
             WHERE pc2.cauhoi_id = p_cauhoi_id AND pk2.dot_khaosat_id = p_dot_khaosat_id), 2) AS ty_le
    FROM phuongan_traloi pt
    LEFT JOIN phieu_khaosat_chitiet pc ON pt.id = pc.phuongan_id
    LEFT JOIN phieu_khaosat pk ON pc.phieu_khaosat_id = pk.id
    WHERE pt.cauhoi_id = p_cauhoi_id 
      AND (pk.dot_khaosat_id = p_dot_khaosat_id OR pk.dot_khaosat_id IS NULL)
    GROUP BY pt.id
    ORDER BY pt.thutu;
  END IF;
END//

DELIMITER ;

-- --------------------------------------------------------
-- CÁC TRIGGER
-- --------------------------------------------------------

-- Trigger kiểm tra đợt khảo sát còn hoạt động
DELIMITER //
CREATE TRIGGER trg_KiemTraDotKhaoSat
BEFORE INSERT ON phieu_khaosat
FOR EACH ROW
BEGIN
  DECLARE v_trangthai VARCHAR(20);
  DECLARE v_denngay DATE;
  
  SELECT trangthai, denngay INTO v_trangthai, v_denngay
  FROM dot_khaosat
  WHERE id = NEW.dot_khaosat_id;
  
  IF v_trangthai != 'active' THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Đợt khảo sát không hoạt động';
  END IF;
  
  IF CURDATE() > v_denngay THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Đợt khảo sát đã kết thúc';
  END IF;
END//

DELIMITER ;

-- Trigger tự động cập nhật trạng thái đợt khảo sát
DELIMITER //
CREATE TRIGGER trg_CapNhatTrangThaiDot
BEFORE UPDATE ON dot_khaosat
FOR EACH ROW
BEGIN
  -- Tự động chuyển sang active nếu đến ngày
  IF NEW.trangthai = 'draft' AND CURDATE() >= NEW.tungay THEN
    SET NEW.trangthai = 'active';
  END IF;
  
  -- Tự động đóng nếu quá hạn
  IF NEW.trangthai = 'active' AND CURDATE() > NEW.denngay THEN
    SET NEW.trangthai = 'closed';
  END IF;
END//

DELIMITER ;

DELIMITER //

-- Trigger cập nhật thời gian hoàn thành phiếu
CREATE TRIGGER trg_CapNhatThoiGianHoanThanh
BEFORE UPDATE ON phieu_khaosat
FOR EACH ROW
BEGIN
  IF OLD.trangthai = 'draft' AND NEW.trangthai = 'completed' THEN
    SET NEW.thoigian_hoanthanh = NOW();
  END IF;
END//

DELIMITER ;

-- Trigger ghi log thay đổi mẫu khảo sát
DELIMITER //
CREATE TRIGGER trg_LogThayDoiMauKhaoSat
AFTER UPDATE ON mau_khaosat
FOR EACH ROW
BEGIN
  INSERT INTO lichsu_thaydoi (bang_thaydoi, id_banghi, hanhdong, noidung_cu, noidung_moi)
  VALUES ('mau_khaosat', NEW.id, 'update',
          JSON_OBJECT('ten_mau', OLD.ten_mau, 'trangthai', OLD.trangthai),
          JSON_OBJECT('ten_mau', NEW.ten_mau, 'trangthai', NEW.trangthai));
END//

DELIMITER ;

-- --------------------------------------------------------
-- CÁC FUNCTION HỖ TRỢ
-- --------------------------------------------------------

DELIMITER //

-- Function tính tỷ lệ hoàn thành
CREATE FUNCTION fn_TinhTyLeHoanThanh(p_dot_khaosat_id INT)
RETURNS DECIMAL(5,2)
DETERMINISTIC
READS SQL DATA
BEGIN
  DECLARE v_tong INT;
  DECLARE v_hoanthanh INT;
  
  SELECT 
    COUNT(*),
    COUNT(CASE WHEN trangthai = 'completed' THEN 1 END)
  INTO v_tong, v_hoanthanh
  FROM phieu_khaosat
  WHERE dot_khaosat_id = p_dot_khaosat_id;
  
  IF v_tong = 0 THEN
    RETURN 0;
  END IF;
  
  RETURN ROUND(v_hoanthanh * 100.0 / v_tong, 2);
END//

DELIMITER ;

DELIMITER //

-- Function kiểm tra quyền truy cập
CREATE FUNCTION fn_KiemTraQuyen(
  p_taikhoan_id INT,
  p_chucnang VARCHAR(50),
  p_quyen VARCHAR(10)
)
RETURNS BOOLEAN
DETERMINISTIC
READS SQL DATA
BEGIN
  DECLARE v_quyen_taikhoan VARCHAR(10);
  DECLARE v_quyen_phanquyen VARCHAR(10);
  
  -- Kiểm tra quyền admin
  SELECT quyen INTO v_quyen_taikhoan
  FROM taikhoan
  WHERE id = p_taikhoan_id;
  
  IF v_quyen_taikhoan = 'admin' THEN
    RETURN TRUE;
  END IF;
  
  -- Kiểm tra phân quyền chi tiết
  SELECT quyen INTO v_quyen_phanquyen
  FROM phanquyen
  WHERE taikhoan_id = p_taikhoan_id AND chucnang = p_chucnang;
  
  IF v_quyen_phanquyen = 'full' OR v_quyen_phanquyen = p_quyen THEN
    RETURN TRUE;
  END IF;
  
  RETURN FALSE;
END//

DELIMITER ;

-- --------------------------------------------------------
-- DỮ LIỆU MẪU
-- --------------------------------------------------------

-- Thêm tài khoản admin mặc định
INSERT INTO `taikhoan` (`tendangnhap`, `matkhau`, `hoten`, `email`) VALUES
('tho493', '2584fcf4f93b79886a1bba3c47dc5cac', 'Administrator', 'tho493@admin.com');

-- Thêm năm học
INSERT INTO `namhoc` (`namhoc`) VALUES
('2023-2024'),
('2024-2025'),
('2025-2026');

-- Thêm cấu hình hệ thống
INSERT INTO `cau_hinh_dich_vu` (`ma_cauhinh`, `giatri`, `mota`, `nhom_cauhinh`) VALUES
-- Cấu hình email mặc định
('email_smtp_host', 'smtp.gmail.com', 'SMTP Host', 'email'),
('email_smtp_port', '587', 'SMTP Port', 'email'),
('email_smtp_encryption', 'tls', 'SMTP Encryption (tls/ssl)', 'email'),
('email_smtp_username', 'test@gmail.com', 'SMTP Username', 'email'),
('email_smtp_password', '123456', 'SMTP Password/App Password', 'email'),
('email_from_address', 'noreply@test.com', 'Địa chỉ email gửi đi mặc định', 'email'),
('email_from_name', 'Hệ thống Khảo sát', 'Tên người gửi mặc định', 'email'),
('email_reply_to', 'support@test.com', 'Địa chỉ email nhận phản hồi', 'email'),

-- Cấu hình giới hạn gửi
('email_max_attempts', '3', 'Số lần thử gửi lại tối đa', 'email'),
('email_queue_timeout', '300', 'Thời gian timeout của queue gửi mail (giây)', 'email'),
('email_batch_size', '50', 'Số email tối đa gửi trong 1 batch', 'email'),
('email_rate_limit', '100', 'Số email tối đa gửi trong 1 giờ', 'email'),

-- Cấu hình thông báo
('email_notify_error', '1', 'Gửi thông báo khi có lỗi', 'email'),
('email_error_notify_to', 'admin@test.com', 'Email nhận thông báo lỗi', 'email'),
('email_test_mode', '0', 'Chế độ test email (1: bật, 0: tắt)', 'email'),
('email_test_recipient', 'test@test.com', 'Email nhận khi ở chế độ test', 'email'),

-- Config app
('system_name', 'Hệ thống khảo sát nội bộ', 'Hệ thống khảo sát nội bộ của trường Đại học Sao Đỏ', 'general'),
('max_file_size', '10485760', 'Dung lượng file tối đa (bytes)', 'upload'),
('session_timeout', '3600', 'Thời gian timeout phiên (giây)', 'security');


-- Thêm template email mẫu
INSERT INTO `template_email` (`ma_template`, `ten_template`, `tieude`, `noidung`, `bien_template`) VALUES
('invite_survey', 'Mời tham gia khảo sát', 'Thư mời tham gia khảo sát {ten_khaosat}', 
'Kính gửi {ho_ten},\n\nTrường mời Anh/Chị tham gia khảo sát "{ten_khaosat}".\n\nThời gian: từ {ngay_batdau} đến {ngay_ketthuc}\n\nLink khảo sát: {link_khaosat}\n\nTrân trọng!', 
'["ho_ten", "ten_khaosat", "ngay_batdau", "ngay_ketthuc", "link_khaosat"]'),
('remind_survey', 'Nhắc nhở khảo sát', 'Nhắc nhở: Khảo sát {ten_khaosat} sắp kết thúc',
'Kính gửi {ho_ten},\n\nKhảo sát "{ten_khaosat}" sẽ kết thúc vào ngày {ngay_ketthuc}.\n\nNếu Anh/Chị chưa tham gia, vui lòng truy cập: {link_khaosat}\n\nTrân trọng!',
'["ho_ten", "ten_khaosat", "ngay_ketthuc", "link_khaosat"]');

-- --------------------------------------------------------
-- CÁC INDEX BỔ SUNG CHO HIỆU NĂNG
-- --------------------------------------------------------

-- Index cho tìm kiếm và thống kê
CREATE INDEX idx_phieu_metadata_khoa ON phieu_khaosat((CAST(JSON_EXTRACT(metadata, '$.khoa') AS CHAR(50))));
CREATE INDEX idx_lichsu_thoigian_bang ON lichsu_thaydoi(thoigian, bang_thaydoi);
CREATE INDEX idx_chitiet_composite ON phieu_khaosat_chitiet(phieu_khaosat_id, cauhoi_id, phuongan_id);

-- --------------------------------------------------------
-- EVENT TỰ ĐỘNG
-- --------------------------------------------------------

DELIMITER //

-- Event tự động cập nhật trạng thái đợt khảo sát
CREATE EVENT IF NOT EXISTS evt_CapNhatTrangThaiDot
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_DATE
DO
BEGIN
  -- Kích hoạt các đợt đến ngày
  UPDATE dot_khaosat 
  SET trangthai = 'active'
  WHERE trangthai = 'draft' 
    AND CURDATE() >= tungay;
  
  -- Đóng các đợt quá hạn
  UPDATE dot_khaosat 
  SET trangthai = 'closed'
  WHERE trangthai = 'active' 
    AND CURDATE() > denngay;
END//

DELIMITER ;

-- --------------------------------------------------------
-- GRANT QUYỀN CHO USER
-- --------------------------------------------------------

-- Tạo user cho ứng dụng (thay đổi password khi deploy)
-- CREATE USER 'khaosat_app'@'localhost' IDENTIFIED BY 'your_secure_password';
-- GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE ON khaosat_db_optimized.* TO 'khaosat_app'@'localhost';
-- GRANT CREATE TEMPORARY TABLES ON khaosat_db_optimized.* TO 'khaosat_app'@'localhost';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;