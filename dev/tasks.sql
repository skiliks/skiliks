alter table tasks add column code varchar(5);
alter table tasks add column start_type varchar(5);
alter table tasks add column category tinyint(1);


-------------
alter table todo add column adding_date int(11) comment "Дата добавления задачи";

---------------------------
CREATE TABLE `day_plan_log` (
  `id`              int(11) NOT NULL AUTO_INCREMENT,
  `uid`             int(11) NOT NULL comment 'Пользователь, прохоядщий симуляцию',
  `snapshot_date`   int(11) comment 'Дата логирования',
  `date`            int(11) NOT NULL,
  `day`             tinyint(1) NOT NULL,
  `task_id`         int(11) NOT NULL,
  PRIMARY KEY (`id`),

  CONSTRAINT `fk_day_plan_log_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_day_plan_log_task_id` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 comment 'Логирование состояние плана';

alter table `day_plan_log` add column snapshot_time int(11) default 0 comment 'Время логирования';