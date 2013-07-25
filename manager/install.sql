CREATE TABLE IF NOT EXISTS `#__booksextension` (
	`id` int(255) NOT NULL AUTO_INCREMENT COMMENT 'Номер заявки',
	`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата подачи заявки',
	`ip` varchar(15) NOT NULL COMMENT 'IP пользователя (на всякий случай)',
	`name` varchar(255) NOT NULL COMMENT 'имя пользователя',
	`abonement` int(4) NOT NULL COMMENT 'Абонемент пользователя',
	`email` varchar(255) NOT NULL COMMENT 'Электропочта пользователя',
	`book` varchar(255) NOT NULL COMMENT 'Название книги',
	`processed` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Статус обработки заявки',
	`accepted` tinyint(1) NOT NULL COMMENT 'Продлена ли книга',
	PRIMARY KEY (`id`),
	KEY `processed` (`processed`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
