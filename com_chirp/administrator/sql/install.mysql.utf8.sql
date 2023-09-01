CREATE TABLE IF NOT EXISTS `#__chirp_control` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`state` TINYINT(1)  NULL  DEFAULT 1,
`ordering` INT(11)  NULL  DEFAULT 0,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`key` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `#__chirp_order_ref` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `updated` date NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `table_name` (`table_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
