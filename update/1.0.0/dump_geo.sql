CREATE TABLE `prefix_ymaps_geo` ( 
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`target_id` BIGINT UNSIGNED NOT NULL,
`target_type` VARCHAR(30) NOT NULL,  
`long` DECIMAL(10,8) UNSIGNED NULL , 
`lat` DECIMAL(10,8) UNSIGNED NULL , 
`radius` MEDIUMINT UNSIGNED NULL , 
`zoom` TINYINT NULL,
`data` VARCHAR(2000) NULL ,
PRIMARY KEY (`id`),
INDEX (`target_id`),
INDEX (`target_type`),
INDEX (`long`), 
INDEX (`lat`)) 
ENGINE = InnoDB;