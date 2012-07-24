CREATE TABLE `excel_document_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) default 'название документа',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Excel Документ-шаблон';

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