CREATE DATABASE dela DEFAULT CHARACTER SET utf8;
USE dela;
CREATE TABLE `projects` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `project` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `users_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB;

CREATE TABLE `items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `date_create` DATE DEFAULT NULL,
  `date_done` DATE DEFAULT NULL,
  `title` VARCHAR(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `url_file` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
  `date_deadline` DATE DEFAULT NULL,
  `users_id` INT(11),
  `projects_id` INT(11),
  PRIMARY KEY (`id`)
) ENGINE = InnoDB;

  CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `register_date` DATE DEFAULT NULL,
  `email` VARCHAR(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `name` VARCHAR(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `password` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `contacts` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
  PRIMARY KEY (`id`),
  UNIQUE(`email`, `name`)
) ENGINE = InnoDB;
	
  CREATE INDEX `ind_title` ON `items` (`title`);

