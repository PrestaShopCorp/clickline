CREATE TABLE IF NOT EXISTS `PREFIX_clickline_cart` 
(
	`id_cart` int(10) unsigned NOT NULL auto_increment,
	`config` text,
	`configWS` text,
	PRIMARY KEY (`id_cart`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_clickline_order` 
(
	`id_order` int(10) unsigned NOT NULL auto_increment,
	`config` text,
	`configWS` text,
	PRIMARY KEY (`id_order`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
