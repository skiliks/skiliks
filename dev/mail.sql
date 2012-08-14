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


ALTER TABLE mail_template AUTO_INCREMENT = 0;

insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (1, 1, 1, 'we have tasks', 'message');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (1, 1, 1, 'subject2', 'message2');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (1, 1, 1, 'subject3', 'message3');

insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (1, 1, 1, 'По ценовой политике', 'Добрый день! 

Я немного с опережением сделала работу по ценовой политике (вчера выдался свободный вечер). Мне кажется, что я отразила все мысли, которые мы обсуждали на установочной встрече. Будет время в отпуске - посмотрите. 

С уважением, Марина Крутько  
Аналитик Отдела аналитики');

insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (1, 1, 1, 'Форма отчетности для производства', 'Доброго вам времени суток! 
Производственный отдел просит вас рассмотреть возможность внесения изменеий в текущую форму отчетности по объемам производства и производственным мощностям. На текущий момент в отчетности не достает развернутого анализа остатков на всех наших складах, включая торговые. Это приводит к тому, что мы периодически производим товар, который уже есть в регионах. Логисты говорят, что вполне могли бы обеспечить перебросу товара из одного региона в другой. Таким образом, нам удалось бы сэкономить на производственных издержках без снижения объемов продаж. Прошу вас оценить сроки, тродоемкость и наличие возможности добавить в производственный отчет данные по складским остаткам.
Заранее благодарю, Бобр В.,  
Нач. производственного отдела.');

insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (1, 1, 1, 'Новая система мотивации', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (1, 1, 1, 'Новая система мотивации 2222', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (1, 1, 1, 'Новая система мотивации 3333333333', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (1, 1, 1, 'Новая система мотивации 44444444', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (1, 1, 1, 'Новая система мотивации 5555', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (1, 1, 1, 'Новая система мотивации 66666666666', 'message3');


insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (2, 1, 1, 'test new folder just for fun', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (2, 1, 1, 'test new folder just for fun2', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (2, 1, 1, 'test new folder just for fun3', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (2, 1, 1, 'test new folder just for fun4', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (2, 1, 1, 'test new folder just for fun5', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (2, 1, 1, 'test new folder just for fun6', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (2, 1, 1, 'test new folder just for fun7', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (2, 1, 1, 'test new folder just for fun8', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (2, 1, 1, 'test new folder just for fun9', 'message3');

insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (3, 1, 1, 'что же делать  когда не знааешь что еще ссказать', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (3, 1, 1, 'что же делать  когда не знааешь что еще ссказать2', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (3, 1, 1, 'что же делать  когда не знааешь что еще ссказать3', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (3, 1, 1, 'что же делать  когда не знааешь что еще ссказать4', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (3, 1, 1, 'что же делать  когда не знааешь что еще ссказать5', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (3, 1, 1, 'что же делать  когда не знааешь что еще ссказать6', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (3, 1, 1, 'что же делать  когда не знааешь что еще ссказать8', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (3, 1, 1, 'что же делать  когда не знааешь что еще ссказать9', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (3, 1, 1, 'что же делать  когда не знааешь что еще ссказать0', 'message3');

insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (4, 1, 1, 'no more lies', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (4, 1, 1, 'no more lies2', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (4, 1, 1, 'no more lies3', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (4, 1, 1, 'no more lies4', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (4, 1, 1, 'no more lies564654', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (4, 1, 1, 'no more liesfdgfd', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (4, 1, 1, 'no more liesfdgfdg', 'message3');
insert into mail_template (`group_id`, sender_id, receiver_id, subject, message) values (4, 1, 1, 'no more liesdfgfdgfdgfd', 'message3');

insert into mail_tasks (`mail_id`, `name`) values (1, 'task1 from mail');
insert into mail_tasks (`mail_id`, `name`) values (1, 'task2 from mail');
insert into mail_tasks (`mail_id`, `name`) values (1, 'task3 from mail');
insert into mail_tasks (`mail_id`, `name`) values (1, 'task4 from mail');

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

drop table `mail_themes`;
CREATE TABLE `mail_themes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Темы писем';

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

   



