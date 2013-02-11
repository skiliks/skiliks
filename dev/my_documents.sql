CREATE TABLE `my_documents_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fileName` varchar(128),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Шаблон моих документов';

delete from my_documents_template;
insert into my_documents_template (id, fileName) values (1, 'test_1.doc');
insert into my_documents_template (id, fileName) values (2, 'test_2.doc');
insert into my_documents_template (id, fileName) values (3, 'test_3.doc');
insert into my_documents_template (id, fileName) values (4, 'test_4.doc');
insert into my_documents_template (id, fileName) values (5, 'test_5.doc');
insert into my_documents_template (id, fileName) values (6, 'test_6.doc');
insert into my_documents_template (id, fileName) values (7, 'test_1.ppt');
insert into my_documents_template (id, fileName) values (8, 'test_2.ppt');
insert into my_documents_template (id, fileName) values (9, 'test_3.ppt');
insert into my_documents_template (id, fileName) values (10, 'test_4.ppt');
insert into my_documents_template (id, fileName) values (11, 'test_5.ppt');
insert into my_documents_template (id, fileName) values (12, 'test_6.ppt');

drop table if exists `my_documents`;
CREATE TABLE `my_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11),  
  `template_id` int(11),
  `fileName` varchar(128),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Мои документы';

alter table `my_documents` add CONSTRAINT `fk_my_documents_sim_id` FOREIGN KEY (`sim_id`) 
    REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

alter table `my_documents` add CONSTRAINT `fk_my_documents_template_id` FOREIGN KEY (`template_id`) 
    REFERENCES `my_documents_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- alter
alter table `my_documents` add column `hidden` tinyint(1) default 0;
alter table `my_documents_template` add column `hidden` tinyint(1) default 0;

insert into my_documents_template (fileName, hidden) values ('attach.ppt', 1);
UPDATE  `mail_attachments_template` SET  `file_id` =  '17' WHERE  `mail_attachments_template`.`id` =1;

---------------------------------
alter table my_documents_template add column code varchar(5);
alter table my_documents_template add column srcFile varchar(32);
alter table my_documents_template add column format varchar(5);

-----------------
alter table my_documents_template add column `type` varchar(5);