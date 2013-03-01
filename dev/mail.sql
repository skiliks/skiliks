alter table characters add column `fio` varchar(64);
alter table characters add column `email` varchar(64);

update characters set fio='герой Клинт Иствуд', email='clint@eastvood.com' where id=1;
update characters set fio='финдир Джон Гекко', email='gekko@jhon.com' where id=2;
update characters set fio='аналитик 2 Миллка Красотка', email='girl@free.com' where id=3;
update characters set fio='аналитик 1 Иван Васильевич', email='ivan@mail.com' where id=4;
update characters set fio='ГД Роман Рабинович', email='romka@business.com' where id=5;
update characters set fio='нач.отдела ИТ Билл Гейтс', email='it@gay.com' where id=6;
update characters set fio='нач.производства Стив Джобс', email='steeve@apple.com' where id=7;
update characters set fio='секретарь Маша Распутина', email='mashka@free.com' where id=8;
update characters set fio='консультант Иван Барыгин', email='barigin@torgash.com' where id=9;


drop table `mail_themes`;
CREATE TABLE `mail_themes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Темы писем';

alter table `mail_themes` add column sim_id int(11) default null;
alter table `mail_themes` add CONSTRAINT `fk_mail_themes_sim_id` FOREIGN KEY (`sim_id`) 
    REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


delete from mail_themes;
insert into mail_themes (`id`, `name`) values (1, 'Сервер');
insert into mail_themes (`id`, `name`) values (2, 'Нужна замена сервера');
insert into mail_themes (`id`, `name`) values (3, 'Срочно приобретите сервер');
insert into mail_themes (`id`, `name`) values (4, 'Служебная записка');
insert into mail_themes (`id`, `name`) values (5, 'Отчет для Правления');
insert into mail_themes (`id`, `name`) values (6, 'Прошу выделить деньги');
insert into mail_themes (`id`, `name`) values (7, 'Срочная замена сервера аналитического отдела');
insert into mail_themes (`id`, `name`) values (8, 'СРОЧНО! СЗ деньги на замену сервера аналитического отдела');
insert into mail_themes (`id`, `name`) values (9, 'Сводный бюджет');
insert into mail_themes (`id`, `name`) values (10, 'Нужны деньги');

insert into mail_themes (`id`, `name`) values (11, 'Отчет для Правления');
insert into mail_themes (`id`, `name`) values (12, 'Календарный план');
insert into mail_themes (`id`, `name`) values (13, 'Re: Задача на завтра');

insert into mail_themes (`id`, `name`) values (14, 'Да пошел ты в жопу, директор!');
insert into mail_themes (`id`, `name`) values (15, 'Заявление об увольнении');
insert into mail_themes (`id`, `name`) values (16, 'Презентация');

-- тестовые темы
insert into mail_themes (`id`, `name`) values (17, 'Отчет');
insert into mail_themes (`id`, `name`) values (18, 'Отчет с копией');
insert into mail_themes (`id`, `name`) values (19, 'Re: Отчет с копией с пробелом');
insert into mail_themes (`id`, `name`) values (20, 'Re: Отчет с пробелом');
insert into mail_themes (`id`, `name`) values (21, 'Re:Атчет с копией без пробела');
insert into mail_themes (`id`, `name`) values (22, 'Re:Атчет без пробела');
insert into mail_themes (`id`, `name`) values (23, 'Fwd: Отчет с копией и пробелом');
insert into mail_themes (`id`, `name`) values (24, 'Fwd: Отчет с пробелом');
insert into mail_themes (`id`, `name`) values (25, 'Fwd:Атчет с копией без пробела');
insert into mail_themes (`id`, `name`) values (26, 'Fwd:Атчет без пробела');
insert into mail_themes (`id`, `name`) values (27, 'Fwd:Fwd:Атчет с копией без пробела');
insert into mail_themes (`id`, `name`) values (28, 'Fwd:Re:Атчет с копией без пробела');
insert into mail_themes (`id`, `name`) values (29, 'Re:Fwd:Атчет с копией без пробела');
insert into mail_themes (`id`, `name`) values (30, 'Re:Re:Атчет с копией без пробела');
insert into mail_themes (`id`, `name`) values (31, 'Fwd:Fwd: Отчет с копией с пробелом');
insert into mail_themes (`id`, `name`) values (32, 'Fwd:Re: Отчет с копией с пробелом');
insert into mail_themes (`id`, `name`) values (33, 'Re:Fwd: Отчет с копией с пробелом');
insert into mail_themes (`id`, `name`) values (34, 'Re:Re: Отчет с копией с пробелом');


drop table if exists `mail_group`;
CREATE TABLE `mail_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) default 'название группы',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Группы писем';

insert into mail_group (`name`) values ('Входящие');
insert into mail_group (`name`) values ('Черновики');
insert into mail_group (`name`) values ('Исходящие');
insert into mail_group (`name`) values ('Корзина');

-------------------------

drop table if exists mail_copies;
drop table if exists mail_messages;
drop table if exists mail_receivers;
drop table if exists mail_tasks;
drop table if exists mail_box;
drop table if exists mail_template;



CREATE TABLE `mail_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11),
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `subject` varchar(255),
  `sending_date` int(11),
  `receiving_date` int(11),  
  `message` text,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_mail_template_group_id` FOREIGN KEY (`group_id`) 
        REFERENCES `mail_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT `fk_mail_template_sender_id` FOREIGN KEY (`sender_id`) 
        REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT `fk_mail_template_receiver_id` FOREIGN KEY (`receiver_id`) 
        REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE

            
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Шаблоны писем';

alter table mail_template add column subject_id int(11);
alter table mail_template add CONSTRAINT `fk_mail_template_subject_id` FOREIGN KEY (`subject_id`) 
        REFERENCES `mail_themes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;                  


CREATE TABLE `mail_tasks` (
    `id`        int(11) NOT NULL AUTO_INCREMENT,
    `mail_id`   int(11),
    `name`      varchar(255),
    PRIMARY KEY (`id`),
    
    CONSTRAINT `fk_mail_tasks_mail_id` FOREIGN KEY (`mail_id`) 
        REFERENCES `mail_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 
    comment 'Задачи, которые можно создать на основании письма';

CREATE TABLE `mail_box` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) comment 'шаблон, на основании которого создано письмо',  
  `sim_id` int(11),
  `group_id` int(11),
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `subject` varchar(255),
  `sending_date` int(11),
  `receiving_date` int(11),  
  `message` text,
  `readed` tinyint(1) default 0,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_mail_box_group_id` FOREIGN KEY (`group_id`) 
        REFERENCES `mail_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT `fk_mail_box_sender_id` FOREIGN KEY (`sender_id`) 
        REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT `fk_mail_box_receiver_id` FOREIGN KEY (`receiver_id`) 
        REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT `fk_mail_box_sim_id` FOREIGN KEY (`sim_id`) 
        REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT `fk_mail_box_template_id` FOREIGN KEY (`template_id`) 
        REFERENCES `mail_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE                                  
            
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Почтовый ящик';

alter table mail_box add column subject_id int(11);
alter table mail_box add CONSTRAINT `fk_mail_box_subject_id` FOREIGN KEY (`subject_id`) 
        REFERENCES `mail_themes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;                  


CREATE TABLE `mail_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_id` int(11),
  `file_id` int(11),  
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_mail_attachments_mail_id` FOREIGN KEY (`mail_id`) 
        REFERENCES `mail_box` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT `fk_mail_attachments_file_id` FOREIGN KEY (`file_id`) 
        REFERENCES `my_documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE    

) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Вложения писем';        


CREATE TABLE `mail_attachments_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_id` int(11),
  `file_id` int(11),  
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_mail_attachments_template_mail_id` FOREIGN KEY (`mail_id`) 
        REFERENCES `mail_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT `fk_mail_attachments_template_file_id` FOREIGN KEY (`file_id`) 
        REFERENCES `my_documents_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE    

) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Шаблоны вложений писем';        

insert into `mail_attachments_template` (mail_id, file_id) values (1,1);


CREATE TABLE `mail_copies_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_id` int(11),
  `receiver_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_mail_copies_template_mail_id` FOREIGN KEY (`mail_id`) 
        REFERENCES `mail_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT `fk_mail_copies_template_receiver_id` FOREIGN KEY (`receiver_id`) 
        REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE                  
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Копии шаблонов писем';



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


CREATE TABLE `mail_receivers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_id` int(11),
  `receiver_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_mail_receivers_mail_id` FOREIGN KEY (`mail_id`) 
        REFERENCES `mail_box` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT `fk_mail_receivers_receiver_id` FOREIGN KEY (`receiver_id`) 
        REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE                  
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Получатели писем';


delete from mail_template;
ALTER TABLE mail_template AUTO_INCREMENT = 0;

-- входящие
insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, receiving_date) values (1, 1, 1, 1, 1, UNIX_TIMESTAMP());
insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, receiving_date) values (2, 1, 2, 1, 2, UNIX_TIMESTAMP()+10);
insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, receiving_date) values (3, 1, 3, 1, 3, UNIX_TIMESTAMP()+20);
insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, receiving_date) values (4, 1, 4, 1, 4, UNIX_TIMESTAMP()+30);
insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, receiving_date) values (5, 1, 5, 1, 5, UNIX_TIMESTAMP()+40);

-- черновики
insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id) values (6, 2, 6, 1, 6);
insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id) values (7, 2, 7, 1, 7);
insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id) values (8, 2, 8, 1, 8);
insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id) values (9, 2, 9, 1, 9);

-- исходящие
insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, sending_date) values (10, 3, 1, 1, 10, UNIX_TIMESTAMP()+50);
insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, sending_date) values (11, 3, 1, 2, 11, UNIX_TIMESTAMP()+60);
insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, sending_date) values (12, 3, 1, 3, 12, UNIX_TIMESTAMP()+70);
insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, sending_date) values (13, 3, 1, 4, 13, UNIX_TIMESTAMP()+80);

-- корзина
insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, receiving_date) values (14, 4, 2, 1, 14, UNIX_TIMESTAMP()+90);
insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, receiving_date) values (15, 4, 3, 1, 15, UNIX_TIMESTAMP()+100);
insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, receiving_date) values (16, 4, 4, 1, 16, UNIX_TIMESTAMP()+110);

insert into mail_tasks (`mail_id`, `name`) values (1, 'task1 from mail');
insert into mail_tasks (`mail_id`, `name`) values (1, 'task2 from mail');
insert into mail_tasks (`mail_id`, `name`) values (1, 'task3 from mail');
insert into mail_tasks (`mail_id`, `name`) values (1, 'task4 from mail');


-- тестовые письма
insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, receiving_date) values (17, 1, 4, 1, 17, UNIX_TIMESTAMP()+90);
insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, receiving_date) values (18, 1, 4, 1, 18, UNIX_TIMESTAMP()+90);
insert into mail_copies_template (mail_id, receiver_id) values (18, 3);
insert into mail_copies_template (mail_id, receiver_id) values (18, 2);
insert into mail_copies_template (mail_id, receiver_id) values (18, 5);
insert into mail_copies_template (mail_id, receiver_id) values (18, 6);
insert into mail_copies_template (mail_id, receiver_id) values (18, 7);
insert into mail_copies_template (mail_id, receiver_id) values (18, 8);
insert into mail_copies_template (mail_id, receiver_id) values (18, 9);

insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, receiving_date) values (19, 1, 2, 1, 19, UNIX_TIMESTAMP()+90);
insert into mail_copies_template (mail_id, receiver_id) values (19, 3);
insert into mail_copies_template (mail_id, receiver_id) values (19, 4);
insert into mail_copies_template (mail_id, receiver_id) values (19, 5);
insert into mail_copies_template (mail_id, receiver_id) values (19, 6);
insert into mail_copies_template (mail_id, receiver_id) values (19, 7);
insert into mail_copies_template (mail_id, receiver_id) values (19, 8);
insert into mail_copies_template (mail_id, receiver_id) values (19, 9);

insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, receiving_date) values (20, 1, 2, 1, 20, UNIX_TIMESTAMP()+90);

insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, receiving_date) values (21, 1, 2, 1, 21, UNIX_TIMESTAMP()+90);
insert into mail_copies_template (mail_id, receiver_id) values (21, 3);
insert into mail_copies_template (mail_id, receiver_id) values (21, 4);
insert into mail_copies_template (mail_id, receiver_id) values (21, 5);
insert into mail_copies_template (mail_id, receiver_id) values (21, 6);
insert into mail_copies_template (mail_id, receiver_id) values (21, 7);
insert into mail_copies_template (mail_id, receiver_id) values (21, 8);
insert into mail_copies_template (mail_id, receiver_id) values (21, 9);

insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, receiving_date) values (22, 1, 2, 1, 22, UNIX_TIMESTAMP()+90);

insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, receiving_date) values (23, 1, 2, 1, 23, UNIX_TIMESTAMP()+90);
insert into mail_copies_template (mail_id, receiver_id) values (23, 3);
insert into mail_copies_template (mail_id, receiver_id) values (23, 4);
insert into mail_copies_template (mail_id, receiver_id) values (23, 5);
insert into mail_copies_template (mail_id, receiver_id) values (23, 6);
insert into mail_copies_template (mail_id, receiver_id) values (23, 7);
insert into mail_copies_template (mail_id, receiver_id) values (23, 8);
insert into mail_copies_template (mail_id, receiver_id) values (23, 9);

insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, receiving_date) values (24, 1, 2, 1, 24, UNIX_TIMESTAMP()+90);

insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, receiving_date) values (25, 1, 2, 1, 25, UNIX_TIMESTAMP()+90);
insert into mail_copies_template (mail_id, receiver_id) values (25, 3);
insert into mail_copies_template (mail_id, receiver_id) values (25, 4);
insert into mail_copies_template (mail_id, receiver_id) values (25, 5);
insert into mail_copies_template (mail_id, receiver_id) values (25, 6);
insert into mail_copies_template (mail_id, receiver_id) values (25, 7);
insert into mail_copies_template (mail_id, receiver_id) values (25, 8);
insert into mail_copies_template (mail_id, receiver_id) values (25, 9);

insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, receiving_date) values (26, 1, 2, 1, 26, UNIX_TIMESTAMP()+90);

insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, receiving_date) values (27, 1, 2, 1, 27, UNIX_TIMESTAMP()+90);
insert into mail_copies_template (mail_id, receiver_id) values (27, 3);
insert into mail_copies_template (mail_id, receiver_id) values (27, 4);
insert into mail_copies_template (mail_id, receiver_id) values (27, 5);
insert into mail_copies_template (mail_id, receiver_id) values (27, 6);
insert into mail_copies_template (mail_id, receiver_id) values (27, 7);
insert into mail_copies_template (mail_id, receiver_id) values (27, 8);
insert into mail_copies_template (mail_id, receiver_id) values (27, 9);

insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, receiving_date) values (28, 1, 2, 1, 28, UNIX_TIMESTAMP()+90);
insert into mail_copies_template (mail_id, receiver_id) values (28, 3);
insert into mail_copies_template (mail_id, receiver_id) values (28, 4);
insert into mail_copies_template (mail_id, receiver_id) values (28, 5);
insert into mail_copies_template (mail_id, receiver_id) values (28, 6);
insert into mail_copies_template (mail_id, receiver_id) values (28, 7);
insert into mail_copies_template (mail_id, receiver_id) values (28, 8);
insert into mail_copies_template (mail_id, receiver_id) values (28, 9);

insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, receiving_date) values (29, 1, 2, 1, 29, UNIX_TIMESTAMP()+90);
insert into mail_copies_template (mail_id, receiver_id) values (29, 3);
insert into mail_copies_template (mail_id, receiver_id) values (29, 4);
insert into mail_copies_template (mail_id, receiver_id) values (29, 5);
insert into mail_copies_template (mail_id, receiver_id) values (29, 6);
insert into mail_copies_template (mail_id, receiver_id) values (29, 7);
insert into mail_copies_template (mail_id, receiver_id) values (29, 8);
insert into mail_copies_template (mail_id, receiver_id) values (29, 9);

insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, receiving_date) values (30, 1, 2, 1, 30, UNIX_TIMESTAMP()+90);
insert into mail_copies_template (mail_id, receiver_id) values (30, 3);
insert into mail_copies_template (mail_id, receiver_id) values (30, 4);
insert into mail_copies_template (mail_id, receiver_id) values (30, 5);
insert into mail_copies_template (mail_id, receiver_id) values (30, 6);
insert into mail_copies_template (mail_id, receiver_id) values (30, 7);
insert into mail_copies_template (mail_id, receiver_id) values (30, 8);
insert into mail_copies_template (mail_id, receiver_id) values (30, 9);

insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, receiving_date) values (31, 1, 2, 1, 31, UNIX_TIMESTAMP()+90);
insert into mail_copies_template (mail_id, receiver_id) values (31, 3);
insert into mail_copies_template (mail_id, receiver_id) values (31, 4);
insert into mail_copies_template (mail_id, receiver_id) values (31, 5);
insert into mail_copies_template (mail_id, receiver_id) values (31, 6);
insert into mail_copies_template (mail_id, receiver_id) values (31, 7);
insert into mail_copies_template (mail_id, receiver_id) values (31, 8);
insert into mail_copies_template (mail_id, receiver_id) values (31, 9);

insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, receiving_date) values (32, 1, 2, 1, 32, UNIX_TIMESTAMP()+90);
insert into mail_copies_template (mail_id, receiver_id) values (32, 3);
insert into mail_copies_template (mail_id, receiver_id) values (32, 4);
insert into mail_copies_template (mail_id, receiver_id) values (32, 5);
insert into mail_copies_template (mail_id, receiver_id) values (32, 6);
insert into mail_copies_template (mail_id, receiver_id) values (32, 7);
insert into mail_copies_template (mail_id, receiver_id) values (32, 8);
insert into mail_copies_template (mail_id, receiver_id) values (32, 9);

insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, receiving_date) values (33, 1, 2, 1, 33, UNIX_TIMESTAMP()+90);
insert into mail_copies_template (mail_id, receiver_id) values (33, 3);
insert into mail_copies_template (mail_id, receiver_id) values (33, 4);
insert into mail_copies_template (mail_id, receiver_id) values (33, 5);
insert into mail_copies_template (mail_id, receiver_id) values (33, 6);
insert into mail_copies_template (mail_id, receiver_id) values (33, 7);
insert into mail_copies_template (mail_id, receiver_id) values (33, 8);
insert into mail_copies_template (mail_id, receiver_id) values (33, 9);

insert into mail_template (`id`, `group_id`, sender_id, receiver_id, subject_id, receiving_date) values (34, 1, 2, 1, 34, UNIX_TIMESTAMP()+90);
insert into mail_copies_template (mail_id, receiver_id) values (34, 3);
insert into mail_copies_template (mail_id, receiver_id) values (34, 4);
insert into mail_copies_template (mail_id, receiver_id) values (34, 5);
insert into mail_copies_template (mail_id, receiver_id) values (34, 6);
insert into mail_copies_template (mail_id, receiver_id) values (34, 7);
insert into mail_copies_template (mail_id, receiver_id) values (34, 8);
insert into mail_copies_template (mail_id, receiver_id) values (34, 9);

------------------------------------------------------------------------------



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








CREATE TABLE `mail_character_themes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `character_id` int(11),  
  `theme_id` int(11),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_mail_character_themes_character_id` FOREIGN KEY (`character_id`) 
    REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,  

  CONSTRAINT `fk_mail_character_themes_theme_id` FOREIGN KEY (`theme_id`) 
    REFERENCES `mail_themes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Темы писем для персонажей';

delete from `mail_character_themes`;
insert into `mail_character_themes` (id, character_id, theme_id) values (1, 2, 1);
insert into `mail_character_themes` (id, character_id, theme_id) values (2, 2, 2);
insert into `mail_character_themes` (id, character_id, theme_id) values (3, 2, 3);
insert into `mail_character_themes` (id, character_id, theme_id) values (4, 2, 4);  --
insert into `mail_character_themes` (id, character_id, theme_id) values (5, 2, 5);
insert into `mail_character_themes` (id, character_id, theme_id) values (6, 2, 6);
insert into `mail_character_themes` (id, character_id, theme_id) values (7, 2, 7);
insert into `mail_character_themes` (id, character_id, theme_id) values (8, 2, 8);
insert into `mail_character_themes` (id, character_id, theme_id) values (9, 2, 9);  --
insert into `mail_character_themes` (id, character_id, theme_id) values (10, 2, 10);

insert into `mail_character_themes` (id, character_id, theme_id) values (11, 3, 9);
insert into `mail_character_themes` (id, character_id, theme_id) values (12, 3, 11);
insert into `mail_character_themes` (id, character_id, theme_id) values (13, 3, 12);
insert into `mail_character_themes` (id, character_id, theme_id) values (14, 3, 13);

insert into `mail_character_themes` (id, character_id, theme_id) values (15, 5, 14);
insert into `mail_character_themes` (id, character_id, theme_id) values (16, 5, 15); --
insert into `mail_character_themes` (id, character_id, theme_id) values (17, 5, 16);
insert into `mail_character_themes` (id, character_id, theme_id) values (18, 5, 9);

-- вставка тестовых фраз
insert into `mail_character_themes` (id, character_id, theme_id) values (19, 2, 17);
insert into `mail_character_themes` (id, character_id, theme_id) values (20, 2, 18);

drop table `mail_phrases`;
CREATE TABLE `mail_phrases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `character_theme_id` int(11),
  `name` varchar(128),
  PRIMARY KEY (`id`),

  CONSTRAINT `fk_mail_phrases_character_theme_id` FOREIGN KEY (`character_theme_id`) 
        REFERENCES `mail_character_themes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Фразы для сообщения';

insert into mail_phrases (character_theme_id, `name`) values (4, '(заключение прилагаю)');
insert into mail_phrases (character_theme_id, `name`) values (4, 'аналитический отдел');
insert into mail_phrases (character_theme_id, `name`) values (4, 'ближайшие дни');
insert into mail_phrases (character_theme_id, `name`) values (4, 'буду в отпуске');
insert into mail_phrases (character_theme_id, `name`) values (4, 'в');
insert into mail_phrases (character_theme_id, `name`) values (4, 'в бюджете');
insert into mail_phrases (character_theme_id, `name`) values (4, 'в связи с');
insert into mail_phrases (character_theme_id, `name`) values (4, 'выделить');
insert into mail_phrases (character_theme_id, `name`) values (4, 'денег');
insert into mail_phrases (character_theme_id, `name`) values (4, 'Денежной Р.Р.');
insert into mail_phrases (character_theme_id, `name`) values (4, 'для');
insert into mail_phrases (character_theme_id, `name`) values (4, 'для аналитического отдела');
insert into mail_phrases (character_theme_id, `name`) values (4, 'достаточно места');
insert into mail_phrases (character_theme_id, `name`) values (4, 'других');
insert into mail_phrases (character_theme_id, `name`) values (4, 'жаловался ни раз');
insert into mail_phrases (character_theme_id, `name`) values (4, 'замену сервера');
insert into mail_phrases (character_theme_id, `name`) values (4, 'и');
insert into mail_phrases (character_theme_id, `name`) values (4, 'имеющийся сервер');
insert into mail_phrases (character_theme_id, `name`) values (4, 'исчерпана');
insert into mail_phrases (character_theme_id, `name`) values (4, 'ИТ подразделения');
insert into mail_phrases (character_theme_id, `name`) values (4, 'к сожалению');

insert into mail_phrases (character_theme_id, `name`) values (9, 'к сожалению');
insert into mail_phrases (character_theme_id, `name`) values (9, 'имеющийся сервер');
insert into mail_phrases (character_theme_id, `name`) values (9, 'исчерпана');
insert into mail_phrases (character_theme_id, `name`) values (9, 'ИТ подразделения');
insert into mail_phrases (character_theme_id, `name`) values (9, 'к сожалению');
insert into mail_phrases (character_theme_id, `name`) values (9, 'компании');
insert into mail_phrases (character_theme_id, `name`) values (9, 'компания останется без информации');
insert into mail_phrases (character_theme_id, `name`) values (9, 'которым');
insert into mail_phrases (character_theme_id, `name`) values (9, 'купить');
insert into mail_phrases (character_theme_id, `name`) values (9, 'модели EX5');
insert into mail_phrases (character_theme_id, `name`) values (9, 'на');
insert into mail_phrases (character_theme_id, `name`) values (9, 'на приобретение сервера');

insert into mail_phrases (character_theme_id, `name`) values (16, 'на приобретение сервера');
insert into mail_phrases (character_theme_id, `name`) values (16, 'начальника аналитического отдела');
insert into mail_phrases (character_theme_id, `name`) values (16, 'не');
insert into mail_phrases (character_theme_id, `name`) values (16, 'не предусмотрено');
insert into mail_phrases (character_theme_id, `name`) values (16, 'не прошел');
insert into mail_phrases (character_theme_id, `name`) values (16, 'нет');
insert into mail_phrases (character_theme_id, `name`) values (16, 'от');
insert into mail_phrases (character_theme_id, `name`) values (16, 'отдела');
insert into mail_phrases (character_theme_id, `name`) values (16, 'положительное решение');
insert into mail_phrases (character_theme_id, `name`) values (16, 'пользуется');
insert into mail_phrases (character_theme_id, `name`) values (16, 'принять');

--------
insert into mail_phrases (character_theme_id, `name`) values (19, 'раз');
insert into mail_phrases (character_theme_id, `name`) values (19, 'два');
insert into mail_phrases (character_theme_id, `name`) values (19, 'три');
insert into mail_phrases (character_theme_id, `name`) values (19, 'четыре');
insert into mail_phrases (character_theme_id, `name`) values (19, 'пять');

insert into mail_phrases (character_theme_id, `name`) values (20, 'сколько');
insert into mail_phrases (character_theme_id, `name`) values (20, 'волка');
insert into mail_phrases (character_theme_id, `name`) values (20, 'не');
insert into mail_phrases (character_theme_id, `name`) values (20, 'корми');
insert into mail_phrases (character_theme_id, `name`) values (20, 'он');
   
-------------------
alter table mail_template add column code varchar(5);
alter table mail_box add column code varchar(5);

alter table mail_tasks add column duration tinyint(3);


INSERT INTO `mail_character_themes` (`id`, `character_id`, `theme_id`) VALUES (NULL, '2', '3');

alter table mail_phrases add column phrase_type tinyint(1);


CREATE TABLE `mail_receivers_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_mail_receivers_template_mail_id` (`mail_id`),
  KEY `fk_mail_receivers_template_receiver_id` (`receiver_id`),
  CONSTRAINT `fk_mail_receivers_template_mail_id` FOREIGN KEY (`mail_id`) REFERENCES `mail_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mail_receivers_template_receiver_id` FOREIGN KEY (`receiver_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Шаблоны получателей писем';

CREATE TABLE `mail_points` (
  `id`          int(11) NOT NULL AUTO_INCREMENT,
  `mail_id`     int(11) NOT NULL,
  `point_id`    int(11) NOT NULL,
  `add_value`   int(11) NOT NULL COMMENT 'добавочное кол-во очков за данный ответ',
  PRIMARY KEY (`id`),
  KEY `fk_mail_points_mail_id` (`mail_id`),
  KEY `fk_mail_points_point_id` (`point_id`),
  CONSTRAINT `fk_mail_points_dialog_id` FOREIGN KEY (`mail_id`) REFERENCES `mail_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mail_points_point_id` FOREIGN KEY (`point_id`) REFERENCES `characters_points_titles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Очки для почты';


-----------------
alter table mail_tasks add column code varchar(5);
alter table mail_tasks add column wr char(1);
alter table mail_tasks add column category tinyint(1);

alter table mail_character_themes add column letter_number varchar(5);
alter table mail_character_themes add column wr char(1);
alter table mail_character_themes add column constructor_number varchar(5);

alter table mail_phrases add column code varchar(5);

------------------
alter table mail_template add column sending_time int(11);
alter table mail_template add column sending_time_str varchar(5);

alter table mail_template add column sending_date_str varchar(10);

alter table mail_box add column sending_time int(11);


alter table mail_character_themes add column phone tinyint(1);
alter table mail_character_themes add column phone_wr char(1);
alter table mail_character_themes add column phone_dialog_number varchar(12);
alter table mail_character_themes add column mail tinyint(1);

----
CREATE TABLE `simulations_mail_points` (
  `id`          int(11) NOT NULL AUTO_INCREMENT,
  `sim_id`      int(11) NOT NULL COMMENT 'идентификатор симуляции',
  `point_id`    int(11) NOT NULL COMMENT 'поинт',  
  `value`       float(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_simulations_mail_points_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_simulations_mail_point_id` FOREIGN KEY (`point_id`) REFERENCES `characters_points_titles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='Баллы, набранные в почтовике';
