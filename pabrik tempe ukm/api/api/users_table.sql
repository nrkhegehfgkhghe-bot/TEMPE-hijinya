-- SQL untuk membuat tabel users saja
-- Jalankan di phpMyAdmin pada database db_pabrik_tempe

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

-- Admin default (password: admin123)
INSERT IGNORE INTO `users` (`nama`, `email`, `password`, `role`, `is_verified`) VALUES 
('admin', 'admin@pabrik-tempe.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1);

SELECT 'Tabel users dibuat + admin default!' as Status;

