-- FULL DATABASE SETUP for Pabrik Tempe
-- Jalankan di phpMyAdmin → Import

-- 1. Buat database
CREATE DATABASE IF NOT EXISTS `db_pabrik_tempe` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `db_pabrik_tempe`;

-- 2. Tabel users (lengkap dengan email)
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','operator','seller','pengunjung') NOT NULL DEFAULT 'pengunjung',
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama` (`nama`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabel products (untuk stok)
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_name` varchar(255) NOT NULL,
  `current_stock_kg` decimal(10,2) DEFAULT 0.00,
  `last_updated` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Tabel sales
CREATE TABLE IF NOT EXISTS `sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_date` date NOT NULL,
  `quantity_kg` decimal(10,2) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_address` text,
  `customer_phone` varchar(20),
  `recorded_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Tabel production
CREATE TABLE IF NOT EXISTS `production` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `production_date` date NOT NULL,
  `quantity_kg` decimal(10,2) NOT NULL,
  `notes` text,
  `recorded_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Insert admin default (password: admin123)
INSERT IGNORE INTO `users` (`nama`, `email`, `password`, `role`, `is_verified`) VALUES 
('admin', 'admin@pabrik-tempe.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1);

-- Done!
SELECT 'Database setup completed!' as Status;

