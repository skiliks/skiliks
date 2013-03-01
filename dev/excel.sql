CREATE TABLE `excel_document_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) default 'название документа',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Excel Документ-шаблон';


alter table `excel_document_template` add  column `file_id` int(11);

alter table `excel_document_template` add CONSTRAINT `fk_excel_document_template_file_id` FOREIGN KEY (`file_id`) 
        REFERENCES `my_documents_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


CREATE TABLE `excel_worksheet_template` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `document_id` int(11) NOT NULL,
    `name` varchar(128) default 'Новая вкладка',
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_excel_worksheet_template_document_id` FOREIGN KEY (`document_id`) 
        REFERENCES `excel_document_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Рабочий лист документа-шаблона';


CREATE TABLE `excel_worksheet_template_cells` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `worksheet_id` int(11) NOT NULL comment 'ссылка на лист',
    `string` int(11) NOT NULL comment 'номер строки',
    `column` varchar(3) NOT NULL comment 'номер колонки',
    `value` varchar(255) comment 'значение ячейки',
    `read_only` tinyint(1) default 1,
    `comment` varchar(255) comment 'комментарий к ячейке',
    `formula` varchar(255) comment 'формула, применяемая для ячейки',
    `colspan` tinyint(2) default 0,
    `rowspan` tinyint(2) default 0,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_worksheet_template_cells_worksheet_id` FOREIGN KEY (`worksheet_id`) 
        REFERENCES `excel_worksheet_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Ячейки рабочего листа шаблона';





CREATE TABLE `excel_document` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `document_id` int(11) NOT NULL comment 'какой шаблон мы используем',
  `sim_id` int(11) NOT NULL comment 'идентификатор симуляции',
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_excel_document_sim_id` FOREIGN KEY (`sim_id`) 
        REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_excel_document_document_id` FOREIGN KEY (`document_id`) 
        REFERENCES `excel_document_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Excel Документ';

alter table `excel_document` add  column `file_id` int(11) comment 'с каким файлом связан документ';
alter table `excel_document` add CONSTRAINT `fk_excel_document_file_id` FOREIGN KEY (`file_id`) 
        REFERENCES `my_documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


CREATE TABLE `excel_worksheet` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `document_id` int(11) NOT NULL,
    `name` varchar(128) default 'Новая вкладка',
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_excel_worksheet_document_id` FOREIGN KEY (`document_id`) 
        REFERENCES `excel_document` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Рабочий лист документа';

CREATE TABLE `excel_worksheet_cells` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `worksheet_id` int(11) NOT NULL comment 'ссылка на лист',
    `string` int(11) NOT NULL comment 'номер строки',
    `column` varchar(3) NOT NULL comment 'номер колонки',
    `value` varchar(255) comment 'значение ячейки',
    `read_only` tinyint(1) default 1,
    `comment` varchar(255) comment 'комментарий к ячейке',
    `formula` varchar(255) comment 'формула, применяемая для ячейки',
    `colspan` tinyint(2) default 0,
    `rowspan` tinyint(2) default 0,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_worksheet_cells_worksheet_id` FOREIGN KEY (`worksheet_id`) 
        REFERENCES `excel_worksheet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Ячейки конкретного рабочго листа';







CREATE TABLE `excel_clipboard` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `worksheet_id` int(11) NOT NULL comment 'ссылка на лист',
    `range` varchar(16) comment 'диапазон, который надо копировать',
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_excel_clipboard_worksheet_id` FOREIGN KEY (`worksheet_id`) 
        REFERENCES `excel_worksheet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Буффер обмена для Excel';

-----------------------

alter table excel_worksheet_template_cells add column bold tinyint(1);
alter table excel_worksheet_template_cells add column color varchar(16);
alter table excel_worksheet_template_cells add column font varchar(32);
alter table excel_worksheet_template_cells add column fontSize tinyint(3);


alter table excel_worksheet_cells add column bold tinyint(1);
alter table excel_worksheet_cells add column color varchar(16);
alter table excel_worksheet_cells add column font varchar(32);
alter table excel_worksheet_cells add column fontSize tinyint(3);

alter table `tasks` add column `sim_id` int(11);

delete from `tasks`;

alter table `tasks` add CONSTRAINT `fk_tasks_sim_id` FOREIGN KEY (`sim_id`) 
        REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO `tasks` (`id`, `title`, `start_time`, `duration`, `type`, `sim_id`) VALUES
(1, 'Поставить задачи сотрудникам на время моего отпуска', 0, 90, 1, null),
(2, 'Проверить работу Аналитика 2 по ценовой политике', 0, 20, 1, null),
(3, 'Согласовать с производственным отделом новую отчетную форму', 0, 20, 1, null),
(4, 'Запустить сбор информации по продажам 3 квартала', 0, 30, 1, null),
(5, 'Позвонить в АХО про работу батарей - плохо работают', 0, 5, 1, null),
(6, 'Подготовить итоговый отчет "Прибыли и убытки" для Генерального директора по 1 полугодию (жду протоко', 0, 90, 1, null),
(7, 'Ответить на запрос HR по новой системе мотивации', 0, 90, 1, null),
(8, 'Доклад ГД на конференции в декабре', 34200, 180, 1, null),
(9, 'Рассказать моим сотрудникам о новой системе премирования с 4 кв. ', 0, 30, 1, null),
(10, 'Посмотреть договор от юристов (уже третий раз присылают)', 0, 60, 1, null),
(11, 'Проверить, что сделал аналитик 1 по задаче логистического отдела.  Трудякин просил сегодня.', 0, 20, 1, null),
(12, 'Встретиться с аналитиком 3 по результатам испытательного срока', 0, 60, 1, null),
(13, 'Срочно доделать сводный бюджет', 0, 180, 1, null),
(14, 'Проверить презентацию для ГД в четверг. Крутько обещала в 15.30', 55800, 30, 1, null),
(15, 'Встреча с клиентом по автоматизации данных, предварительно четверг около часа ', 46800, 60, 1, null),
(16, 'Совещание " О старте годовой аттестации", 17.00', 0, 90, 1, null),
(17, 'Встреча с ГД в 16.00 по презентации', 57600, 30, 2, null),
(18, 'Обед', 0, 30, 1, null),
(19, 'Уйти с работы на день рождения тещи (заехать за цветами)', 64800, 60, 1, null);

alter table `tasks` add CONSTRAINT `fk_tasks_sim_id` FOREIGN KEY (`sim_id`) 
        REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;



---- alter
alter table `excel_worksheet_template_cells` add column `borderTop`     tinyint(1);
alter table `excel_worksheet_template_cells` add column `borderBottom`  tinyint(1);
alter table `excel_worksheet_template_cells` add column `borderLeft`    tinyint(1);
alter table `excel_worksheet_template_cells` add column `borderRight`   tinyint(1);

alter table `excel_worksheet_cells` add column `borderTop`     tinyint(1);
alter table `excel_worksheet_cells` add column `borderBottom`  tinyint(1);
alter table `excel_worksheet_cells` add column `borderLeft`    tinyint(1);
alter table `excel_worksheet_cells` add column `borderRight`   tinyint(1);


alter table `excel_worksheet_template` add column `cellHeight` int(4);
alter table `excel_worksheet_template` add column `cellWidth` int(4);



alter table `excel_worksheet` add column `cellHeight` int(4);
alter table `excel_worksheet` add column `cellWidth` int(4);

-- alter2
update excel_worksheet_template_cells set `colspan`=16 where `worksheet_id`=126 and `column`='B' and `string`=1;
update excel_worksheet_template_cells set `colspan`=12 where `worksheet_id`=126 and `column`='B' and `string`=3;
update excel_worksheet_template_cells set `colspan`=4 where `worksheet_id`=126 and `column`='N' and `string`=3;
b1 16

-- alter3
alter table `excel_worksheet_template_cells` add column `width` varchar(16) comment 'Ширина ячейки';
alter table `excel_worksheet_cells` add column `width` varchar(16) comment 'Ширина ячейки';