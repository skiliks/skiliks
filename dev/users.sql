

CREATE TABLE `groups` (
  `id`      int(11) NOT NULL AUTO_INCREMENT,
  `name`    varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 comment 'Группы пользователей';

insert into `groups` (`name`) values ('promo');
insert into `groups` (`name`) values ('developer');

CREATE TABLE `user_groups` (
  `id`      int(11) NOT NULL AUTO_INCREMENT,
  `uid`     int(11) NOT NULL comment 'пользователь',
  `gid`     int(11) NOT NULL comment 'группа',
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_user_groups_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_user_groups_gid` FOREIGN KEY (`gid`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 comment 'Группы пользователей';

insert into `user_groups` (uid, gid) values (1, 2);

alter table `simulations` add column `type` tinyint(1);