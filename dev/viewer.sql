CREATE TABLE `viewer_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) comment 'С каким файлом связаны',
  `filePath` varchar(128) comment 'Путь к файлу',  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Шаблон просмотрщика файлов';

alter table `viewer_template` add CONSTRAINT `fk_viewer_template_file_id` FOREIGN KEY (`file_id`) 
        REFERENCES `my_documents_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;                  


insert into `viewer_template` (id, file_id, filePath) values (1, 17, "media\documents\doc_img_01.png");
insert into `viewer_template` (id, file_id, filePath) values (2, 17, "media\documents\doc_img_02.png");
insert into `viewer_template` (id, file_id, filePath) values (3, 17, "media\documents\doc_img_03.png");
insert into `viewer_template` (id, file_id, filePath) values (4, 17, "media\documents\doc_img_04.png");
insert into `viewer_template` (id, file_id, filePath) values (5, 17, "media\documents\doc_img_05.png");
insert into `viewer_template` (id, file_id, filePath) values (6, 17, "media\documents\doc_img_06.png");
insert into `viewer_template` (id, file_id, filePath) values (7, 17, "media\documents\doc_img_07.png");
insert into `viewer_template` (id, file_id, filePath) values (8, 17, "media\documents\doc_img_08.png");
insert into `viewer_template` (id, file_id, filePath) values (9, 17, "media\documents\doc_img_09.png");
insert into `viewer_template` (id, file_id, filePath) values (10, 17, "media\documents\doc_img_10.png");