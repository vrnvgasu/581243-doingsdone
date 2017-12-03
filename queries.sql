INSERT INTO `users` (`id`, `register_date`, `email`, `name`, `password`, `contacts`)
VALUES (
NULL, NULL, 'ignat.v@gmail.com', 'Игнат', '$2y$10$Iux5Azaf6stLu6uHIcl9b.MQUI54.Jd/2P.leJMJ0pf6fDVB8f9PC', NULL
), (
NULL, NULL, 'kitty_93@li.ru', 'Леночка', '$2y$10$7KpgwGHCuUukUANEU3jM9uCY7p2gDhChwz22BidrbGnFBo/Fx3v.6', NULL
), (
NULL, NULL, 'warrior07@mail.ru', 'Руслан', '$2y$10$WeWqQ3/ehWC1u44AAN3f1eC7VAQYLnvsnJLgUcAAlIsDETeazHXu2', NULL
);

INSERT INTO `items` (`id`, `date_create`, `date_done`, `title`, `url_file`, `date_deadline`, `users_id`, `projects_id`)
VALUES (
NULL, NULL, NULL, 'Собеседование в IT компании', NULL, '2018.06.01', 1, 3
), (
NULL, NULL, NULL, 'Выполнить тестовое задание', NULL, '2018.05.25', 1, 3
), (
NULL, NULL, '2017.11.30', 'Сделать задание первого раздела', NULL, '2018.04.21', 1, 2
), (
NULL, NULL, NULL, 'Встреча с другом', NULL, '2018.04.22', 1, 1
), (
NULL, NULL, NULL, 'Купить корм для кота', NULL, NULL, 1, 4
), (
NULL, NULL, NULL, 'Заказать пиццу', NULL, NULL, 1, 4
);

INSERT INTO `projects` (`id`, `project`, `users_id`)
VALUES (
NULL, 'Входящие', 1
), (
NULL, 'Учеба', 1
), (
NULL, 'Работа', 1
), (
NULL, 'Домашние дела', 1
), (
NULL, 'Авто', 1
);


SELECT `title` FROM `items` `i`
JOIN `users` `u`
ON `i`.`users_id` =  `u`.`id`
WHERE `u`.`id` = 1

SELECT `title` FROM `items` `i`
JOIN  `projects` `p`
ON `i`.`projects_id` = `p`.`id`
WHERE `p`.`id` = 1

UPDATE `items` SET `date_done` = NULL
WHERE `title` = 'Заказать пиццу'

SELECT * FROM `items`
WHERE `date_deadline` = (CURDATE()+1)

UPDATE `items`SET `title` = 'новая задача'
WHERE `id` = 2