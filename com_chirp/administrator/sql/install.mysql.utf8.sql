CREATE TABLE IF NOT EXISTS `#__chirp_control` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`state` TINYINT(1)  NULL  DEFAULT 1,
`ordering` INT(11)  NULL  DEFAULT 0,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`key` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

 CREATE TABLE IF NOT EXISTS `#__chirp_order_ref` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `updated` date NOT NULL DEFAULT current_timestamp(),
  `order_id` int(11) NOT NULL DEFAULT 0,
  `table_name` varchar(64) NULL,
  PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

INSERT INTO `#__chirp_order_ref` ( table_name ) VALUES ( 'easyshop_orders' );
INSERT INTO `#__chirp_order_ref` ( table_name ) VALUES ( 'eshop_orders' );
INSERT INTO `#__chirp_order_ref` ( table_name ) VALUES ( 'hikashop_order' );
INSERT INTO `#__chirp_order_ref` ( table_name ) VALUES ( 'phocacart_orders' );

