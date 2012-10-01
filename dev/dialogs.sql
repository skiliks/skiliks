ALTER TABLE  `dialogs` CHANGE  `ch_to`  `ch_to` INT( 11 ) NOT NULL DEFAULT  '0' COMMENT  'персонаж которому должен исходить текст';


ALTER TABLE  `dialogs` CHANGE  `ch_to`  `ch_to` INT( 11 ) NULL DEFAULT NULL COMMENT  'персонаж которому должен исходить текст'


INSERT INTO  `skiliks`.`dialog_subtypes` (
`id` ,
`type_id` ,
`title`
)
VALUES (
'5',  '2',  'Стук в дверь'
);

ALTER TABLE  `dialogs` add column `sound` varchar(32);

ALTER TABLE  `dialogs` add column `excel_id` int(11);


-------
ALTER TABLE  `dialogs` add column `next_event_code` varchar(5);
----------------
ALTER TABLE  `dialogs` add column `flag` varchar(5);

CREATE TABLE `simulation_flags` (
  `id`      int(11) NOT NULL AUTO_INCREMENT,
  `sim_id`  int(11),
  `flag`    varchar(5) comment 'название флага',
  `value`   tinyint(1) default null,  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'флаги симуляции';

alter table `simulation_flags` add CONSTRAINT `fk_simulation_flags_sim_id` FOREIGN KEY (`sim_id`) 
        REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE `flags_rules` (
  `id`          int(11) NOT NULL AUTO_INCREMENT,
  `rule_name`   varchar(32) comment 'имя правила',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'правила на основании флагов';