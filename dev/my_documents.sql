CREATE TABLE `my_documents_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fileName` varchar(128),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Шаблон моих документов';

insert into my_documents_template (id, fileName) values (1, 'test.xls');
insert into my_documents_template (id, fileName) values (2, 'some.doc');
insert into my_documents_template (id, fileName) values (3, 'present.ppt');

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