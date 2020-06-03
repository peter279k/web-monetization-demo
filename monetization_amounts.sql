CREATE DATABASE IF NOT EXISTS `monetization_demo`;

use `monetization_demo`;

DROP TABLE IF EXISTS `monetization_amounts`;

CREATE TABLE `monetization_amounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `asset_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `asset_scale` tinyint COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_pointer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_date_time` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
