CREATE TABLE IF NOT EXISTS `#__chirp_control` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`state` TINYINT(1)  NULL  DEFAULT 1,
`ordering` INT(11)  NULL  DEFAULT 0,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`key` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jos_chirp_analytics` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `click_id` varchar(36) DEFAULT NULL,
  `order_id` int(11) NOT NULL,
  `click_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `shop_name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_id` (`click_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

 CREATE TABLE IF NOT EXISTS `#__chirp_order_ref` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `updated` date NOT NULL DEFAULT current_timestamp(),
  `order_id` int(11) NOT NULL DEFAULT 0,
  `table_name` varchar(64) NULL,
  `shop_name` varchar(64) NULL,
  `column_id` varchar(64) NULL,
  PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

INSERT INTO `#__chirp_order_ref` ( table_name, shop_name, column_id ) VALUES ( 'easyshop_orders', 'easyshop', 'id' );
INSERT INTO `#__chirp_order_ref` ( table_name, shop_name, column_id ) VALUES ( 'eshop_orderproducts', 'eshop', 'id' );
INSERT INTO `#__chirp_order_ref` ( table_name, shop_name, column_id ) VALUES ( 'hikashop_order', 'hikashop', 'order_id' );
INSERT INTO `#__chirp_order_ref` ( table_name, shop_name, column_id ) VALUES ( 'phocacart_orders', 'phocacart', 'id' );
