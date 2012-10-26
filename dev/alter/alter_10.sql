CREATE TABLE `plan_log` (
  `id`              int(11) NOT NULL AUTO_INCREMENT,
  `sim_id`          int(11)                 comment 'Симуляция',
  `day_type`        tinyint(1)              comment 'Графа плана',
  `logging_time`    int(11)                 comment 'Время логирования состояния плана',
  `task_id`         int(11),
  `plan_time`       int(11)                 comment 'Время, на которое стоит в плане',
  `is_task_done`    tinyint(1)              comment 'Сделана ли задача',
  `todo_count`      tinyint(3)              comment 'Кол-во задач в "Сделать"',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'логирование плана';

alter table `plan_log` add CONSTRAINT `fk_plan_log_sim_id` FOREIGN KEY (`sim_id`) 
        REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

alter table `plan_log` add CONSTRAINT `fk_plan_log_task_id` FOREIGN KEY (`task_id`) 
        REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

----------------------------
alter table `day_plan_log` add column `sim_id` int(11) comment 'Симуляция';
alter table `day_plan_log` add CONSTRAINT `fk_day_plan_log_sim_id` FOREIGN KEY (`sim_id`) 
        REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

alter table `day_plan_log` add column `todo_count` tinyint(3) comment 'Кол-во задач в "Сделать"';