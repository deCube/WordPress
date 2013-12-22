# Migrations
## Table: wp_lang
```mysql
CREATE TABLE IF NOT EXISTS `wp_lang` (
	`ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`code` VARCHAR(6) NOT NULL,
	`name` VARCHAR(60) NOT NULL,
	`default` INT(1) NOT NULL DEFAULT  '0',
	`created_at` TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
```
## Table: wp_posts
```mysql
ALTER TABLE  `wp_posts` ADD  `lang_ID` INT(11) NOT NULL AFTER  `ID`;
```