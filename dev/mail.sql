alter table characters add column `fio` varchar(64);
alter table characters add column `email` varchar(64);

update characters set fio='Клинт Иствуд', email='clint@eastvood.com' where id=1;
update characters set fio='Джон Гекко', email='gekko@jhon.com' where id=2;
update characters set fio='Миллка Красотка', email='girl@free.com' where id=3;
update characters set fio='Иван Васильевич', email='ivan@mail.com' where id=4;
update characters set fio='Роман Рабинович', email='romka@business.com' where id=5;
update characters set fio='Билл Гейтс', email='it@gay.com' where id=6;
update characters set fio='Стив Джобс', email='steeve@apple.com' where id=7;
update characters set fio='Маша Распутина', email='mashka@free.com' where id=8;
update characters set fio='Иван Барыгин', email='barigin@torgash.com' where id=9;



CREATE TABLE `mail_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) default 'название группы',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Группы писем';

insert into mail_group (`name`) values ('Входящие');
insert into mail_group (`name`) values ('Черновики');
insert into mail_group (`name`) values ('Исходящие');
insert into mail_group (`name`) values ('Корзина');


CREATE TABLE `mail_box` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11),
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `subject` varchar(255),
  `sending_date` int(11),
  `receiving_date` int(11),  
  `message` text,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_mail_box_group_id` FOREIGN KEY (`group_id`) 
        REFERENCES `mail_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT `fk_mail_box_sender_id` FOREIGN KEY (`sender_id`) 
        REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT `fk_mail_box_receiver_id` FOREIGN KEY (`receiver_id`) 
        REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE            
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Почтовый ящик';

insert into mail_box (`group_id`, sender_id, receiver_id, subject, message) values (1, 1, 1, 'subject', 'message');
insert into mail_box (`group_id`, sender_id, receiver_id, subject, message) values (1, 1, 1, 'subject2', 'message2');
insert into mail_box (`group_id`, sender_id, receiver_id, subject, message) values (1, 1, 1, 'subject3', 'message3');


CREATE TABLE `mail_copies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_id` int(11),
  `receiver_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_mail_copies_mail_id` FOREIGN KEY (`mail_id`) 
        REFERENCES `mail_box` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT `fk_mail_copies_receiver_id` FOREIGN KEY (`receiver_id`) 
        REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE                  
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Копии';

CREATE TABLE `mail_phrases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Фразы для сообщения';

insert into mail_phrases (`name`) values ('Привет');
insert into mail_phrases (`name`) values ('Как дела');
insert into mail_phrases (`name`) values ('Что нового');
insert into mail_phrases (`name`) values ('Айда в кино!');




CREATE TABLE `mail_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_id` int(11),
  `phrase_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_mail_messages_mail_id` FOREIGN KEY (`mail_id`) 
        REFERENCES `mail_box` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT `fk_mail_messages_phrase_id` FOREIGN KEY (`phrase_id`) 
        REFERENCES `mail_phrases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE                  
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Почтовые сообщения';

drop table `mail_settings`;
CREATE TABLE `mail_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11),
  `messageArriveSound` tinyint(1) default 1,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_mail_settings_sim_id` FOREIGN KEY (`sim_id`) 
    REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Настройки почты';

delete from characters;
CREATE TABLE `mail_themes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `character_id` int(11),  
  `name` varchar(128),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_mail_themes_character_id` FOREIGN KEY (`character_id`) 
    REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE  
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Темы писем';

insert into mail_themes (`character_id`, `name`) values (1, 'Скоро собрание');
insert into mail_phrases (`name`) values ('Как дела');
insert into mail_phrases (`name`) values ('Что нового');
insert into mail_phrases (`name`) values ('Айда в кино!');
