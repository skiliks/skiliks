CREATE TABLE `viewer_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) comment 'С каким файлом связаны',
  `filePath` varchar(128) comment 'Путь к файлу',  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Шаблон просмотрщика файлов';

alter table `viewer_template` add CONSTRAINT `fk_viewer_template_file_id` FOREIGN KEY (`file_id`) 
        REFERENCES `my_documents_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;                  


delete from `viewer_template`;
insert into `viewer_template` (id, file_id, filePath) values (1, 17, "demo_page_01.png");
insert into `viewer_template` (id, file_id, filePath) values (2, 17, "demo_page_02.png");
insert into `viewer_template` (id, file_id, filePath) values (3, 17, "demo_page_03.png");
insert into `viewer_template` (id, file_id, filePath) values (4, 17, "demo_page_04.png");
insert into `viewer_template` (id, file_id, filePath) values (5, 17, "demo_page_05.png");
insert into `viewer_template` (id, file_id, filePath) values (6, 17, "demo_page_06.png");
insert into `viewer_template` (id, file_id, filePath) values (7, 17, "demo_page_07.png");
insert into `viewer_template` (id, file_id, filePath) values (8, 17, "demo_page_08.png");
insert into `viewer_template` (id, file_id, filePath) values (9, 17, "demo_page_09.png");
insert into `viewer_template` (id, file_id, filePath) values (10, 17, "demo_page_10.png");
insert into `viewer_template` (id, file_id, filePath) values (11, 17, "demo_page_11.png");
insert into `viewer_template` (id, file_id, filePath) values (12, 17, "demo_page_12.png");
insert into `viewer_template` (id, file_id, filePath) values (13, 17, "demo_page_13.png");
insert into `viewer_template` (id, file_id, filePath) values (14, 17, "demo_page_14.png");
insert into `viewer_template` (id, file_id, filePath) values (15, 17, "demo_page_15.png");
insert into `viewer_template` (id, file_id, filePath) values (16, 17, "demo_page_16.png");
insert into `viewer_template` (id, file_id, filePath) values (17, 17, "demo_page_17.png");