CREATE TABLE `phone_calls` (
  `id`      int(11) NOT NULL AUTO_INCREMENT,
  `sim_id`  int(11) NOT NULL comment 'идентификатор симуляции',
  `call_date`   int(11),
  `call_type`  tinyint(1) default 0,
  `from_id` int(11) comment 'Кто звонил',
  `to_id` int(11) comment 'Кому звонил',

  PRIMARY KEY (`id`),

  CONSTRAINT `fk_phone_calls_sim_id` FOREIGN KEY (`sim_id`) 
        REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT `fk_phone_calls_from_id` FOREIGN KEY (`from_id`) 
        REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT `fk_phone_calls_to_id` FOREIGN KEY (`to_id`) 
        REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'История звонков';