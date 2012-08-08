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
        REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT `fk_mail_box_receiver_id` FOREIGN KEY (`receiver_id`) 
        REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE            
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Почтовый ящик';

CREATE TABLE `mail_copies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_id` int(11),
  `receiver_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_mail_copies_mail_id` FOREIGN KEY (`mail_id`) 
        REFERENCES `mail_box` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT `fk_mail_copies_receiver_id` FOREIGN KEY (`receiver_id`) 
        REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE                  
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Копии';

insert into mail_box (`group_id`, sender_id, receiver_id, subject, message) values (1, 1, 1, 'subject', 'message');
insert into mail_box (`group_id`, sender_id, receiver_id, subject, message) values (1, 1, 1, 'subject2', 'message2');
insert into mail_box (`group_id`, sender_id, receiver_id, subject, message) values (1, 1, 1, 'subject3', 'message3');
