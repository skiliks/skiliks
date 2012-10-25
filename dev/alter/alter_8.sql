-- логировани окон

drop table if exists `window_log`;
CREATE TABLE `window_log` (
  `id`              int(11) NOT NULL AUTO_INCREMENT,
  `sim_id`          int(11)                 comment 'Симуляция',
  `activeWindow`    tinyint(1)  default 0   comment 'Активное окно',  
  `activeSubWindow` tinyint(1)  default 0   comment 'Активное подокно',   
  `timeStart`       int(11)     default 0   comment 'Игровое время - start',
  `timeEnd`         int(11)     default 0   comment 'Игровое время - end',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'логирование окон';

alter table `window_log` add CONSTRAINT `fk_window_log_sim_id` FOREIGN KEY (`sim_id`) 
        REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;