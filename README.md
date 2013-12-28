# WPolyglot

## What is this project all about?
This projects mission is to make WordPress core multilingual, just by adding a constant in wp-config.php, similar to setting up a multisite installation.

## Migrations
Currently these steps need to be done manually, eventually they will be taken over by a set-up wizard.

### Table: wp_lang
```mysql
CREATE TABLE IF NOT EXISTS `wp_lang` (
	`ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`code` VARCHAR(6) NOT NULL,
	`name` VARCHAR(60) NOT NULL,
	`default` INT(1) NOT NULL DEFAULT  '0',
	`created_at` TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
```
### Table: wp_posts
```mysql
ALTER TABLE  `wp_posts` ADD  `lang_ID` INT(11) NOT NULL AFTER  `ID`;
```

## Todo
* Update permalinks of archives, categories & taxonomies
* Use correct translation files
* Create backend interface for post types
* Create backend interface for custom WPolyglot settings (such as which languages, default language & show comments in current or all language(s))
* Creating install wizard
* Force in pretty permalinks when installing