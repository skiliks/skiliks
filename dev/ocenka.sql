alter table simulations_dialogs_points add column `count6x` int(11);
alter table simulations_dialogs_points add column `value6x` float(10,2);

CREATE TABLE `simulations_excel_points` (
  `id`      int(11) NOT NULL AUTO_INCREMENT,
  `sim_id`  int(11) NOT NULL comment 'идентификатор симуляции',
  `value`   float(10, 2),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_simulations_excel_points_sim_id` FOREIGN KEY (`sim_id`) 
        REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Баллы, набранные в экселе';