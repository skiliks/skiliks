-- MySQL dump 10.13  Distrib 5.5.29, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: skiliks
-- ------------------------------------------------------
-- Server version	5.5.29-0ubuntu0.12.10.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `YiiCache`
--

DROP TABLE IF EXISTS `YiiCache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `YiiCache` (
  `id` varchar(255) NOT NULL,
  `expire` int(11) DEFAULT NULL,
  `value` longblob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `YiiCache`
--

LOCK TABLES `YiiCache` WRITE;
/*!40000 ALTER TABLE `YiiCache` DISABLE KEYS */;
/*!40000 ALTER TABLE `YiiCache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `YiiSession`
--

DROP TABLE IF EXISTS `YiiSession`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `YiiSession` (
  `id` varchar(255) NOT NULL,
  `expire` int(11) DEFAULT NULL,
  `data` blob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `YiiSession`
--

LOCK TABLES `YiiSession` WRITE;
/*!40000 ALTER TABLE `YiiSession` DISABLE KEYS */;
/*!40000 ALTER TABLE `YiiSession` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity`
--

DROP TABLE IF EXISTS `activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity` (
  `id` varchar(60) NOT NULL,
  `parent` varchar(10) NOT NULL,
  `grandparent` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category_id` varchar(10) DEFAULT NULL,
  `import_id` varchar(255) NOT NULL,
  `numeric_id` int(11) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL COMMENT 'Task ot Activity. Task is important thihg, activiti - trash.',
  PRIMARY KEY (`id`),
  KEY `fk_activity_category_id` (`category_id`),
  CONSTRAINT `fk_activity_category_id` FOREIGN KEY (`category_id`) REFERENCES `activity_category` (`code`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity`
--

LOCK TABLES `activity` WRITE;
/*!40000 ALTER TABLE `activity` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_action`
--

DROP TABLE IF EXISTS `activity_action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_id` varchar(60) NOT NULL,
  `dialog_id` int(11) DEFAULT NULL,
  `mail_id` int(11) DEFAULT NULL,
  `document_id` int(11) DEFAULT NULL,
  `import_id` varchar(255) NOT NULL,
  `window_id` int(11) DEFAULT NULL,
  `is_keep_last_category` tinyint(1) DEFAULT '0',
  `leg_type` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `activity_action_document_unique` (`activity_id`,`document_id`),
  UNIQUE KEY `activity_action_dialog_unique` (`activity_id`,`dialog_id`),
  UNIQUE KEY `activity_action_mail_unique` (`activity_id`,`mail_id`),
  KEY `fk_activity_action_dialog_id` (`dialog_id`),
  KEY `fk_activity_action_mail_id` (`mail_id`),
  KEY `fk_activity_action_document_id` (`document_id`),
  KEY `activity_action_activity_id` (`activity_id`),
  KEY `activity_action_leg_type` (`leg_type`),
  CONSTRAINT `activity_action_action_id` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `activity_action_leg_type` FOREIGN KEY (`leg_type`) REFERENCES `activity_type` (`type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_action`
--

LOCK TABLES `activity_action` WRITE;
/*!40000 ALTER TABLE `activity_action` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_action` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_category`
--

DROP TABLE IF EXISTS `activity_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_category` (
  `code` varchar(10) NOT NULL,
  `priority` int(11) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_category`
--

LOCK TABLES `activity_category` WRITE;
/*!40000 ALTER TABLE `activity_category` DISABLE KEYS */;
INSERT INTO `activity_category` VALUES ('0',2),('1',3),('2',4),('2_min',1),('3',5),('4',6),('5',7);
/*!40000 ALTER TABLE `activity_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_efficiency_conditions`
--

DROP TABLE IF EXISTS `activity_efficiency_conditions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_efficiency_conditions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_id` varchar(10) DEFAULT NULL,
  `type` varchar(30) DEFAULT NULL,
  `result_code` varchar(30) DEFAULT NULL,
  `email_template_id` int(11) DEFAULT NULL,
  `dialog_id` int(11) DEFAULT NULL,
  `operation` varchar(5) DEFAULT NULL,
  `efficiency_value` int(11) DEFAULT NULL,
  `fail_less_coeficient` varchar(5) DEFAULT NULL,
  `import_id` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_efficiency_conditions`
--

LOCK TABLES `activity_efficiency_conditions` WRITE;
/*!40000 ALTER TABLE `activity_efficiency_conditions` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_efficiency_conditions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_parent`
--

DROP TABLE IF EXISTS `activity_parent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_parent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `import_id` varchar(14) DEFAULT NULL,
  `parent_code` varchar(10) NOT NULL,
  `dialog_id` int(11) DEFAULT NULL,
  `mail_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_parent`
--

LOCK TABLES `activity_parent` WRITE;
/*!40000 ALTER TABLE `activity_parent` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_parent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_type`
--

DROP TABLE IF EXISTS `activity_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_type` (
  `type` varchar(40) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_type`
--

LOCK TABLES `activity_type` WRITE;
/*!40000 ALTER TABLE `activity_type` DISABLE KEYS */;
INSERT INTO `activity_type` VALUES ('Documents_leg'),('Inbox_leg'),('Manual_dial_leg'),('Outbox_leg'),('System_dial_leg'),('Window');
/*!40000 ALTER TABLE `activity_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assessment_aggregated`
--

DROP TABLE IF EXISTS `assessment_aggregated`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessment_aggregated` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `point_id` int(11) NOT NULL,
  `value` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assessment_aggregated_I_id` (`id`),
  KEY `assessment_aggregated_I_point_id` (`point_id`),
  KEY `assessment_aggregated_I_sim_id` (`sim_id`),
  CONSTRAINT `assessment_aggregated_FK_character_point_title` FOREIGN KEY (`point_id`) REFERENCES `characters_points_titles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `assessment_aggregated_FK_simulations` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessment_aggregated`
--

LOCK TABLES `assessment_aggregated` WRITE;
/*!40000 ALTER TABLE `assessment_aggregated` DISABLE KEYS */;
/*!40000 ALTER TABLE `assessment_aggregated` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `characters`
--

DROP TABLE IF EXISTS `characters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `characters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(48) NOT NULL,
  `fio` varchar(64) DEFAULT NULL,
  `email` varchar(64) DEFAULT NULL,
  `code` tinyint(3) DEFAULT NULL,
  `skype` varchar(128) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `import_id` varchar(14) DEFAULT NULL COMMENT 'setvice value,used to remove old data after reimport.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `characters`
--

LOCK TABLES `characters` WRITE;
/*!40000 ALTER TABLE `characters` DISABLE KEYS */;
/*!40000 ALTER TABLE `characters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `characters_points`
--

DROP TABLE IF EXISTS `characters_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `characters_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dialog_id` int(11) NOT NULL,
  `point_id` int(11) NOT NULL,
  `add_value` int(11) NOT NULL COMMENT 'добавочное кол-во очков за данный ответ',
  `import_id` varchar(14) NOT NULL DEFAULT '00000000000000' COMMENT 'setvice value,used to remove old data after reimport.',
  PRIMARY KEY (`id`),
  KEY `fk_characters_points_dialog_id` (`dialog_id`),
  KEY `fk_characters_points_point_id` (`point_id`),
  CONSTRAINT `fk_characters_points_dialog_id` FOREIGN KEY (`dialog_id`) REFERENCES `dialogs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_characters_points_point_id` FOREIGN KEY (`point_id`) REFERENCES `characters_points_titles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Требуеме поведения';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `characters_points`
--

LOCK TABLES `characters_points` WRITE;
/*!40000 ALTER TABLE `characters_points` DISABLE KEYS */;
/*!40000 ALTER TABLE `characters_points` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `characters_points_titles`
--

DROP TABLE IF EXISTS `characters_points_titles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `characters_points_titles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL COMMENT 'Номер требуемого поведения',
  `title` text NOT NULL,
  `scale` float(10,2) DEFAULT NULL COMMENT 'Scale',
  `type_scale` tinyint(4) DEFAULT NULL,
  `import_id` varchar(14) NOT NULL DEFAULT '00000000000000' COMMENT 'setvice value,used to remove old data after reimport.',
  `learning_goal_code` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Наименования требуемых поведений';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `characters_points_titles`
--

LOCK TABLES `characters_points_titles` WRITE;
/*!40000 ALTER TABLE `characters_points_titles` DISABLE KEYS */;
/*!40000 ALTER TABLE `characters_points_titles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `characters_states`
--

DROP TABLE IF EXISTS `characters_states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `characters_states` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL COMMENT 'название состояние персонажа',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `characters_states`
--

LOCK TABLES `characters_states` WRITE;
/*!40000 ALTER TABLE `characters_states` DISABLE KEYS */;
INSERT INTO `characters_states` VALUES (1,'уравновешенное'),(2,'в гневе'),(3,'Туповатый позитив'),(4,'обиженное');
/*!40000 ALTER TABLE `characters_states` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `communication_themes`
--

DROP TABLE IF EXISTS `communication_themes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `communication_themes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `character_id` int(11) DEFAULT NULL,
  `text` varchar(250) DEFAULT NULL COMMENT 'Text for subject',
  `letter_number` varchar(5) DEFAULT NULL,
  `wr` char(1) DEFAULT NULL,
  `constructor_number` varchar(5) DEFAULT NULL,
  `phone` tinyint(1) DEFAULT NULL,
  `phone_wr` char(1) DEFAULT NULL,
  `phone_dialog_number` varchar(12) DEFAULT NULL,
  `mail` tinyint(1) DEFAULT NULL,
  `source` varchar(32) DEFAULT NULL COMMENT 'Used to score user behaviour.',
  `import_id` varchar(14) DEFAULT NULL COMMENT 'setvice value,used to remove old data after reimport.',
  `code` int(11) NOT NULL,
  `mail_prefix` varchar(255) DEFAULT NULL,
  `theme_usage` varchar(30) DEFAULT NULL COMMENT 'Representation of Theme_usage',
  PRIMARY KEY (`id`),
  KEY `fk_mail_character_themes_character_id` (`character_id`),
  KEY `fk_mail_character_themes_letter_number` (`letter_number`),
  KEY `communication_themes_mail_prefix` (`mail_prefix`),
  CONSTRAINT `communication_themes_mail_prefix` FOREIGN KEY (`mail_prefix`) REFERENCES `mail_prefix` (`code`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Темы писем для персонажей';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `communication_themes`
--

LOCK TABLES `communication_themes` WRITE;
/*!40000 ALTER TABLE `communication_themes` DISABLE KEYS */;
/*!40000 ALTER TABLE `communication_themes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `day_plan`
--

DROP TABLE IF EXISTS `day_plan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `day_plan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `date` time DEFAULT NULL,
  `day` tinyint(1) NOT NULL,
  `task_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_day_plan_task_id` (`task_id`),
  KEY `fk_day_plan_sim_id` (`sim_id`),
  CONSTRAINT `fk_day_plan_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_day_plan_task_id` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `day_plan`
--

LOCK TABLES `day_plan` WRITE;
/*!40000 ALTER TABLE `day_plan` DISABLE KEYS */;
/*!40000 ALTER TABLE `day_plan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `day_plan_after_vacation`
--

DROP TABLE IF EXISTS `day_plan_after_vacation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `day_plan_after_vacation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `sim_id` int(11) NOT NULL,
  `date` time DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_day_plan_after_vacation_task_id` (`task_id`),
  KEY `fk_day_plan_after_vacation_sim_id` (`sim_id`),
  CONSTRAINT `fk_day_plan_after_vacation_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_day_plan_after_vacation_task_id` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `day_plan_after_vacation`
--

LOCK TABLES `day_plan_after_vacation` WRITE;
/*!40000 ALTER TABLE `day_plan_after_vacation` DISABLE KEYS */;
/*!40000 ALTER TABLE `day_plan_after_vacation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `day_plan_log`
--

DROP TABLE IF EXISTS `day_plan_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `day_plan_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT 'Пользователь, прохоядщий симуляцию',
  `snapshot_date` datetime DEFAULT NULL COMMENT 'Дата логирования',
  `date` time DEFAULT NULL,
  `day` tinyint(1) NOT NULL,
  `task_id` int(11) NOT NULL,
  `snapshot_time` int(11) DEFAULT '0' COMMENT 'Время логирования',
  `sim_id` int(11) DEFAULT NULL COMMENT 'Симуляция',
  `todo_count` tinyint(3) DEFAULT NULL COMMENT 'Кол-во задач в "Сделать"',
  PRIMARY KEY (`id`),
  KEY `fk_day_plan_log_uid` (`uid`),
  KEY `fk_day_plan_log_task_id` (`task_id`),
  KEY `fk_day_plan_log_sim_id` (`sim_id`),
  CONSTRAINT `fk_day_plan_log_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_day_plan_log_task_id` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_day_plan_log_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Логирование состояние плана';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `day_plan_log`
--

LOCK TABLES `day_plan_log` WRITE;
/*!40000 ALTER TABLE `day_plan_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `day_plan_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dialog_subtypes`
--

DROP TABLE IF EXISTS `dialog_subtypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dialog_subtypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL COMMENT 'идентификатор типа диалога',
  `title` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_dialog_subtypes_type_id` (`type_id`),
  CONSTRAINT `fk_dialog_subtypes_type_id` FOREIGN KEY (`type_id`) REFERENCES `dialog_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dialog_subtypes`
--

LOCK TABLES `dialog_subtypes` WRITE;
/*!40000 ALTER TABLE `dialog_subtypes` DISABLE KEYS */;
INSERT INTO `dialog_subtypes` VALUES (1,1,'Звонок'),(2,1,'Разговор по телефону'),(3,2,'Визит'),(4,2,'Встреча'),(5,2,'Стук в дверь');
/*!40000 ALTER TABLE `dialog_subtypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dialog_types`
--

DROP TABLE IF EXISTS `dialog_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dialog_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dialog_types`
--

LOCK TABLES `dialog_types` WRITE;
/*!40000 ALTER TABLE `dialog_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `dialog_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dialogs`
--

DROP TABLE IF EXISTS `dialogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dialogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ch_from` int(11) NOT NULL COMMENT 'персонаж от которого должен исходить текст',
  `ch_from_state` int(11) NOT NULL COMMENT 'эмоциональное состояние',
  `ch_to` int(11) DEFAULT NULL COMMENT 'персонаж которому должен исходить текст',
  `ch_to_state` int(11) NOT NULL,
  `dialog_subtype` int(11) NOT NULL COMMENT 'Подтип диалога',
  `text` text NOT NULL,
  `event_result` int(11) NOT NULL COMMENT 'результат диалога, который вернется событию',
  `code` varchar(10) NOT NULL,
  `step_number` int(11) NOT NULL,
  `replica_number` int(11) NOT NULL,
  `next_event` int(11) DEFAULT NULL,
  `delay` int(11) NOT NULL DEFAULT '0',
  `is_final_replica` tinyint(1) NOT NULL,
  `sound` varchar(64) DEFAULT NULL,
  `excel_id` int(11) DEFAULT NULL,
  `next_event_code` varchar(10) DEFAULT NULL,
  `flag_to_switch` varchar(5) DEFAULT NULL,
  `demo` tinyint(1) DEFAULT '0',
  `type_of_init` varchar(32) DEFAULT NULL COMMENT 'Replica initialization type: dialog, icon, time, flex etc.',
  `import_id` varchar(14) NOT NULL DEFAULT '00000000000000' COMMENT 'setvice value,used to remove old data after reimport.',
  PRIMARY KEY (`id`),
  KEY `fk_dialogs_branch_id` (`step_number`),
  KEY `fk_dialogs_ch_from` (`ch_from`),
  KEY `fk_dialogs_ch_from_state` (`ch_from_state`),
  KEY `fk_dialogs_ch_to` (`ch_to`),
  KEY `fk_dialogs_ch_to_state` (`ch_to_state`),
  KEY `fk_dialogs_dialog_subtype` (`dialog_subtype`),
  KEY `fk_dialogs_event_result` (`event_result`),
  KEY `fk_dialogs_next_branch` (`replica_number`),
  KEY `fk_dialogs_next_event` (`next_event`),
  CONSTRAINT `fk_dialogs_ch_from` FOREIGN KEY (`ch_from`) REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_dialogs_ch_from_state` FOREIGN KEY (`ch_from_state`) REFERENCES `characters_states` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_dialogs_ch_to` FOREIGN KEY (`ch_to`) REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_dialogs_ch_to_state` FOREIGN KEY (`ch_to_state`) REFERENCES `characters_states` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_dialogs_dialog_subtype` FOREIGN KEY (`dialog_subtype`) REFERENCES `dialog_subtypes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_dialogs_event_result` FOREIGN KEY (`event_result`) REFERENCES `events_results` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_dialogs_next_event` FOREIGN KEY (`next_event`) REFERENCES `events_samples` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dialogs`
--

LOCK TABLES `dialogs` WRITE;
/*!40000 ALTER TABLE `dialogs` DISABLE KEYS */;
/*!40000 ALTER TABLE `dialogs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emails_sub`
--

DROP TABLE IF EXISTS `emails_sub`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emails_sub` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emails_sub`
--

LOCK TABLES `emails_sub` WRITE;
/*!40000 ALTER TABLE `emails_sub` DISABLE KEYS */;
/*!40000 ALTER TABLE `emails_sub` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events_on_hold_logic`
--

DROP TABLE IF EXISTS `events_on_hold_logic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events_on_hold_logic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='перечень вариантов для удержанного события';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events_on_hold_logic`
--

LOCK TABLES `events_on_hold_logic` WRITE;
/*!40000 ALTER TABLE `events_on_hold_logic` DISABLE KEYS */;
INSERT INTO `events_on_hold_logic` VALUES (1,'ничего'),(2,'Покашливания, полтергейсты');
/*!40000 ALTER TABLE `events_on_hold_logic` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events_results`
--

DROP TABLE IF EXISTS `events_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events_results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events_results`
--

LOCK TABLES `events_results` WRITE;
/*!40000 ALTER TABLE `events_results` DISABLE KEYS */;
INSERT INTO `events_results` VALUES (1,'не ответить'),(2,'сделаю сам'),(3,'пригласить аналитика2'),(4,'Сделает аналитик 2'),(5,'пригласить аналитика1'),(6,'Сделает аналитик 1'),(7,'нет результата');
/*!40000 ALTER TABLE `events_results` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events_samples`
--

DROP TABLE IF EXISTS `events_samples`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events_samples` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL,
  `title` varchar(100) NOT NULL,
  `on_ignore_result` int(11) NOT NULL,
  `on_hold_logic` int(11) NOT NULL,
  `trigger_time` time DEFAULT NULL,
  `import_id` varchar(14) DEFAULT NULL COMMENT 'setvice value,used to remove old data after reimport.',
  PRIMARY KEY (`id`),
  KEY `fk_events_samples_on_hold_logic` (`on_hold_logic`),
  KEY `fk_events_samples_on_ignore_result` (`on_ignore_result`),
  CONSTRAINT `fk_events_samples_on_hold_logic` FOREIGN KEY (`on_hold_logic`) REFERENCES `events_on_hold_logic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_events_samples_on_ignore_result` FOREIGN KEY (`on_ignore_result`) REFERENCES `events_results` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events_samples`
--

LOCK TABLES `events_samples` WRITE;
/*!40000 ALTER TABLE `events_samples` DISABLE KEYS */;
/*!40000 ALTER TABLE `events_samples` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events_triggers`
--

DROP TABLE IF EXISTS `events_triggers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events_triggers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `trigger_time` time DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_events_triggers_event_id` (`event_id`),
  KEY `fk_events_triggers_sim_id` (`sim_id`),
  CONSTRAINT `fk_events_triggers_event_id` FOREIGN KEY (`event_id`) REFERENCES `events_samples` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_events_triggers_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events_triggers`
--

LOCK TABLES `events_triggers` WRITE;
/*!40000 ALTER TABLE `events_triggers` DISABLE KEYS */;
/*!40000 ALTER TABLE `events_triggers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `excel_document`
--

DROP TABLE IF EXISTS `excel_document`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `excel_document` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `document_id` int(11) NOT NULL COMMENT 'какой шаблон мы используем',
  `sim_id` int(11) NOT NULL COMMENT 'идентификатор симуляции',
  `file_id` int(11) DEFAULT NULL COMMENT 'с каким файлом связан документ',
  PRIMARY KEY (`id`),
  KEY `fk_excel_document_sim_id` (`sim_id`),
  KEY `fk_excel_document_document_id` (`document_id`),
  KEY `fk_excel_document_file_id` (`file_id`),
  CONSTRAINT `fk_excel_document_document_id` FOREIGN KEY (`document_id`) REFERENCES `excel_document_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_excel_document_file_id` FOREIGN KEY (`file_id`) REFERENCES `my_documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_excel_document_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Excel Документ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `excel_document`
--

LOCK TABLES `excel_document` WRITE;
/*!40000 ALTER TABLE `excel_document` DISABLE KEYS */;
/*!40000 ALTER TABLE `excel_document` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `excel_document_template`
--

DROP TABLE IF EXISTS `excel_document_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `excel_document_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT 'название документа',
  `file_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_excel_document_template_file_id` (`file_id`),
  CONSTRAINT `fk_excel_document_template_file_id` FOREIGN KEY (`file_id`) REFERENCES `my_documents_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Excel Документ-шаблон';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `excel_document_template`
--

LOCK TABLES `excel_document_template` WRITE;
/*!40000 ALTER TABLE `excel_document_template` DISABLE KEYS */;
/*!40000 ALTER TABLE `excel_document_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `excel_points_formula`
--

DROP TABLE IF EXISTS `excel_points_formula`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `excel_points_formula` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `formula` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='Формулы для расчета оценки по экселю';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `excel_points_formula`
--

INSERT INTO `excel_points_formula` (`id`,`formula`) VALUES (1,'=SUM(Логистика!B6:M7)+SUM(Логистика!B10:M14)');
INSERT INTO `excel_points_formula` (`id`,`formula`) VALUES (2,'=SUM(Производство!B6:M7)+SUM(Производство!B10:M14)');
INSERT INTO `excel_points_formula` (`id`,`formula`) VALUES (3,'=SUM(Сводный!N6:Q7)+SUM(Сводный!N10:Q14)-SUM(Сводный!B6:M7)-SUM(Сводный!B10:M14)');
INSERT INTO `excel_points_formula` (`id`,`formula`) VALUES (4,'=SUM(Сводный!R6:R7)+SUM(Сводный!R10:R14)-SUM(Сводный!B6:M7)-SUM(Сводный!B10:M14)');
INSERT INTO `excel_points_formula` (`id`,`formula`) VALUES (5,'=SUM(Сводный!N16:Q16)-(SUM(Сводный!B8:M8)-SUM(Сводный!B15:M15))');
INSERT INTO `excel_points_formula` (`id`,`formula`) VALUES (6,'=Сводный!R16-(SUM(Сводный!B8:M8)-SUM(Сводный!B15:M15))');
INSERT INTO `excel_points_formula` (`id`,`formula`) VALUES (7,'=Сводный!R18');
INSERT INTO `excel_points_formula` (`id`,`formula`) VALUES (8,'=SUM(Сводный!N19:Q19)');
INSERT INTO `excel_points_formula` (`id`,`formula`) VALUES (9,'=SUM(Сводный!N20:Q20)');


LOCK TABLES `excel_points_formula` WRITE;
/*!40000 ALTER TABLE `excel_points_formula` DISABLE KEYS */;
/*!40000 ALTER TABLE `excel_points_formula` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flag`
--

DROP TABLE IF EXISTS `flag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flag` (
  `code` varchar(10) NOT NULL,
  `description` text NOT NULL,
  `import_id` varchar(60) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flag`
--

LOCK TABLES `flag` WRITE;
/*!40000 ALTER TABLE `flag` DISABLE KEYS */;
/*!40000 ALTER TABLE `flag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flag_block_dialog`
--

DROP TABLE IF EXISTS `flag_block_dialog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flag_block_dialog` (
  `flag_code` varchar(5) NOT NULL,
  `dialog_code` varchar(10) NOT NULL,
  `value` tinyint(1) DEFAULT NULL,
  `import_id` varchar(14) DEFAULT NULL,
  KEY `fk_flag_block_dialog_flag_code` (`flag_code`),
  CONSTRAINT `fk_flag_block_dialog_flag_code` FOREIGN KEY (`flag_code`) REFERENCES `flag` (`code`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flag_block_dialog`
--

LOCK TABLES `flag_block_dialog` WRITE;
/*!40000 ALTER TABLE `flag_block_dialog` DISABLE KEYS */;
/*!40000 ALTER TABLE `flag_block_dialog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flag_block_mail`
--

DROP TABLE IF EXISTS `flag_block_mail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flag_block_mail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `flag_code` varchar(5) NOT NULL,
  `value` tinyint(1) DEFAULT NULL,
  `mail_template_id` int(11) NOT NULL,
  `import_id` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `flag_block_mail_mail_template` (`mail_template_id`),
  KEY `fk_flag_block_mail__flag_code` (`flag_code`),
  CONSTRAINT `fk_flag_block_mail__flag_code` FOREIGN KEY (`flag_code`) REFERENCES `flag` (`code`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `flag_block_mail_mail_template` FOREIGN KEY (`mail_template_id`) REFERENCES `mail_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flag_block_mail`
--

LOCK TABLES `flag_block_mail` WRITE;
/*!40000 ALTER TABLE `flag_block_mail` DISABLE KEYS */;
/*!40000 ALTER TABLE `flag_block_mail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flag_block_replica`
--

DROP TABLE IF EXISTS `flag_block_replica`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flag_block_replica` (
  `flag_code` varchar(5) NOT NULL,
  `replica_id` int(11) NOT NULL,
  `value` tinyint(1) DEFAULT NULL,
  `import_id` varchar(14) DEFAULT NULL,
  KEY `fk_flag_block_replica_flag_code` (`flag_code`),
  CONSTRAINT `fk_flag_block_replica_flag_code` FOREIGN KEY (`flag_code`) REFERENCES `flag` (`code`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flag_block_replica`
--

LOCK TABLES `flag_block_replica` WRITE;
/*!40000 ALTER TABLE `flag_block_replica` DISABLE KEYS */;
/*!40000 ALTER TABLE `flag_block_replica` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flag_run_email`
--

DROP TABLE IF EXISTS `flag_run_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flag_run_email` (
  `flag_code` varchar(10) NOT NULL,
  `mail_code` varchar(5) NOT NULL,
  `import_id` varchar(14) DEFAULT NULL,
  KEY `fk_flag_run_email_flag_code` (`flag_code`),
  KEY `fk_flag_run_email_mail_code` (`mail_code`),
  CONSTRAINT `fk_flag_run_email_flag_code` FOREIGN KEY (`flag_code`) REFERENCES `flag` (`code`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_flag_run_email_mail_code` FOREIGN KEY (`mail_code`) REFERENCES `mail_template` (`code`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flag_run_email`
--

LOCK TABLES `flag_run_email` WRITE;
/*!40000 ALTER TABLE `flag_run_email` DISABLE KEYS */;
/*!40000 ALTER TABLE `flag_run_email` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Группы пользователей';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` VALUES (1,'promo'),(2,'developer');
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `learning_goals`
--

DROP TABLE IF EXISTS `learning_goals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `learning_goals` (
  `code` varchar(10) NOT NULL,
  `title` text,
  `import_id` varchar(14) NOT NULL DEFAULT '00000000000000',
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `learning_goals`
--

LOCK TABLES `learning_goals` WRITE;
/*!40000 ALTER TABLE `learning_goals` DISABLE KEYS */;
/*!40000 ALTER TABLE `learning_goals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_activity_action`
--

DROP TABLE IF EXISTS `log_activity_action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_activity_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `window` tinyint(4) DEFAULT NULL,
  `start_time` time NOT NULL,
  `end_time` time DEFAULT NULL,
  `activity_action_id` int(11) DEFAULT NULL,
  `mail_id` int(11) DEFAULT NULL,
  `document_id` int(11) DEFAULT NULL,
  `window_uid` varchar(32) DEFAULT NULL COMMENT 'md5',
  PRIMARY KEY (`id`),
  KEY `activity_action_id` (`activity_action_id`),
  KEY `log_activity_action_sim_id` (`sim_id`),
  KEY `log_activity_action_mail_id` (`mail_id`),
  KEY `log_activity_action_document_id` (`document_id`),
  CONSTRAINT `activity_action_id` FOREIGN KEY (`activity_action_id`) REFERENCES `activity_action` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `log_activity_action_document_id` FOREIGN KEY (`document_id`) REFERENCES `my_documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `log_activity_action_mail_id` FOREIGN KEY (`mail_id`) REFERENCES `mail_box` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `log_activity_action_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_activity_action`
--

LOCK TABLES `log_activity_action` WRITE;
/*!40000 ALTER TABLE `log_activity_action` DISABLE KEYS */;
/*!40000 ALTER TABLE `log_activity_action` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_activity_action_agregated`
--

DROP TABLE IF EXISTS `log_activity_action_agregated`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_activity_action_agregated` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `leg_type` varchar(30) DEFAULT NULL COMMENT 'Just text label',
  `leg_action` varchar(30) DEFAULT NULL COMMENT 'Just text label',
  `activity_action_id` int(11) DEFAULT NULL,
  `category` varchar(30) DEFAULT NULL COMMENT 'Just text label',
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `duration` time NOT NULL,
  `is_keep_last_category` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `log_activity_action_agregated_FK_activity_action` (`activity_action_id`),
  KEY `log_activity_action_agregated_FK_simulations` (`sim_id`),
  CONSTRAINT `log_activity_action_agregated_FK_activity_action` FOREIGN KEY (`activity_action_id`) REFERENCES `activity_action` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `log_activity_action_agregated_FK_simulations` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1144 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_activity_action_agregated`
--

LOCK TABLES `log_activity_action_agregated` WRITE;
/*!40000 ALTER TABLE `log_activity_action_agregated` DISABLE KEYS */;
/*!40000 ALTER TABLE `log_activity_action_agregated` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_dialog_points`
--

DROP TABLE IF EXISTS `log_dialog_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_dialog_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `point_id` int(11) DEFAULT NULL,
  `dialog_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15733 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_dialog_points`
--

LOCK TABLES `log_dialog_points` WRITE;
/*!40000 ALTER TABLE `log_dialog_points` DISABLE KEYS */;
/*!40000 ALTER TABLE `log_dialog_points` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_dialogs`
--

DROP TABLE IF EXISTS `log_dialogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_dialogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `dialog_id` int(11) DEFAULT NULL,
  `last_id` int(11) DEFAULT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL DEFAULT '00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4361 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_dialogs`
--

LOCK TABLES `log_dialogs` WRITE;
/*!40000 ALTER TABLE `log_dialogs` DISABLE KEYS */;
/*!40000 ALTER TABLE `log_dialogs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_documents`
--

DROP TABLE IF EXISTS `log_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL DEFAULT '00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1332 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_documents`
--

LOCK TABLES `log_documents` WRITE;
/*!40000 ALTER TABLE `log_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `log_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_mail`
--

DROP TABLE IF EXISTS `log_mail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_mail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `mail_id` int(11) DEFAULT NULL,
  `window` tinyint(4) DEFAULT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL DEFAULT '00:00:00',
  `mail_task_id` int(11) DEFAULT NULL COMMENT 'Id of planned taks. Null - no tasks planned.',
  `full_coincidence` varchar(5) DEFAULT '-' COMMENT 'Code of MS mail template, that fully considenced with user email.',
  `part1_coincidence` varchar(5) DEFAULT '-' COMMENT 'Code of MS mail template, that partly (type1) considenced with user email.',
  `part2_coincidence` varchar(5) DEFAULT '-' COMMENT 'Code of MS mail template, that partly (part2) considenced with user email.',
  `is_coincidence` tinyint(1) DEFAULT '0' COMMENT 'Summarize considerence. Boolean.',
  `window_uid` varchar(32) DEFAULT NULL COMMENT 'md5',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19409 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_mail`
--

LOCK TABLES `log_mail` WRITE;
/*!40000 ALTER TABLE `log_mail` DISABLE KEYS */;
/*!40000 ALTER TABLE `log_mail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_windows`
--

DROP TABLE IF EXISTS `log_windows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_windows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `window` tinyint(4) DEFAULT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL DEFAULT '00:00:00',
  `window_uid` varchar(32) DEFAULT NULL COMMENT 'md5',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_windows`
--

LOCK TABLES `log_windows` WRITE;
/*!40000 ALTER TABLE `log_windows` DISABLE KEYS */;
/*!40000 ALTER TABLE `log_windows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_attachments`
--

DROP TABLE IF EXISTS `mail_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_id` int(11) DEFAULT NULL,
  `file_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_mail_attachments_mail_id` (`mail_id`),
  KEY `fk_mail_attachments_file_id` (`file_id`),
  CONSTRAINT `fk_mail_attachments_file_id` FOREIGN KEY (`file_id`) REFERENCES `my_documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mail_attachments_mail_id` FOREIGN KEY (`mail_id`) REFERENCES `mail_box` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Вложения писем';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mail_attachments`
--

LOCK TABLES `mail_attachments` WRITE;
/*!40000 ALTER TABLE `mail_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_attachments_template`
--

DROP TABLE IF EXISTS `mail_attachments_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_attachments_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_id` int(11) DEFAULT NULL,
  `file_id` int(11) DEFAULT NULL,
  `import_id` varchar(14) NOT NULL COMMENT 'setvice value,used to remove old data after reimport.',
  PRIMARY KEY (`id`),
  KEY `fk_mail_attachments_template_mail_id` (`mail_id`),
  KEY `fk_mail_attachments_template_file_id` (`file_id`),
  CONSTRAINT `fk_mail_attachments_template_file_id` FOREIGN KEY (`file_id`) REFERENCES `my_documents_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mail_attachments_template_mail_id` FOREIGN KEY (`mail_id`) REFERENCES `mail_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Шаблоны вложений писем';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mail_attachments_template`
--

LOCK TABLES `mail_attachments_template` WRITE;
/*!40000 ALTER TABLE `mail_attachments_template` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_attachments_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_box`
--

DROP TABLE IF EXISTS `mail_box`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_box` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) DEFAULT NULL COMMENT 'шаблон, на основании которого создано письмо',
  `sim_id` int(11) DEFAULT NULL,
  `group_id` int(11) NOT NULL DEFAULT '5',
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text,
  `readed` tinyint(1) DEFAULT '0',
  `subject_id` int(11) DEFAULT NULL,
  `code` varchar(5) DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Столбец нужен для типа сообщения 1 - Входящие, 2 - Исходящие, 3 - Входящие(доставлен), 4 - Исходящие(доставлен)',
  `plan` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Cтолбец reply для обозначения был ответ(1) или нет(0)',
  `reply` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'mail_box.plan состояние плана для для письма, 0 - не запланировано, 1- заплпнировано',
  `message_id` int(11) DEFAULT NULL,
  `letter_type` varchar(10) NOT NULL,
  `coincidence_type` varchar(25) DEFAULT NULL COMMENT 'full/part1/part2, null - no coincidence',
  `coincidence_mail_code` varchar(5) DEFAULT NULL COMMENT 'Like MS1, MS2 etc., null - no coincidence',
  `sent_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_mail_box_group_id` (`group_id`),
  KEY `fk_mail_box_sender_id` (`sender_id`),
  KEY `fk_mail_box_receiver_id` (`receiver_id`),
  KEY `fk_mail_box_sim_id` (`sim_id`),
  KEY `fk_mail_box_template_id` (`template_id`),
  KEY `fk_mail_box_subject_id` (`subject_id`),
  CONSTRAINT `fk_mail_box_group_id` FOREIGN KEY (`group_id`) REFERENCES `mail_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mail_box_receiver_id` FOREIGN KEY (`receiver_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mail_box_sender_id` FOREIGN KEY (`sender_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mail_box_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mail_box_subject_id` FOREIGN KEY (`subject_id`) REFERENCES `communication_themes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mail_box_template_id` FOREIGN KEY (`template_id`) REFERENCES `mail_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Почтовый ящик';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mail_box`
--

LOCK TABLES `mail_box` WRITE;
/*!40000 ALTER TABLE `mail_box` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_box` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_copies`
--

DROP TABLE IF EXISTS `mail_copies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_copies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_mail_copies_mail_id` (`mail_id`),
  KEY `fk_mail_copies_receiver_id` (`receiver_id`),
  CONSTRAINT `fk_mail_copies_mail_id` FOREIGN KEY (`mail_id`) REFERENCES `mail_box` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mail_copies_receiver_id` FOREIGN KEY (`receiver_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Копии';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mail_copies`
--

LOCK TABLES `mail_copies` WRITE;
/*!40000 ALTER TABLE `mail_copies` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_copies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_copies_template`
--

DROP TABLE IF EXISTS `mail_copies_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_copies_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_mail_copies_template_mail_id` (`mail_id`),
  KEY `fk_mail_copies_template_receiver_id` (`receiver_id`),
  CONSTRAINT `fk_mail_copies_template_mail_id` FOREIGN KEY (`mail_id`) REFERENCES `mail_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mail_copies_template_receiver_id` FOREIGN KEY (`receiver_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Копии шаблонов писем';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mail_copies_template`
--

LOCK TABLES `mail_copies_template` WRITE;
/*!40000 ALTER TABLE `mail_copies_template` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_copies_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_group`
--

DROP TABLE IF EXISTS `mail_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT 'название группы',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='Группы писем';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mail_group`
--

LOCK TABLES `mail_group` WRITE;
/*!40000 ALTER TABLE `mail_group` DISABLE KEYS */;
INSERT INTO `mail_group` VALUES (1,'Входящие'),(2,'Черновики'),(3,'Исходящие'),(4,'Корзина'),(5,'не пришло');
/*!40000 ALTER TABLE `mail_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_messages`
--

DROP TABLE IF EXISTS `mail_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_id` int(11) DEFAULT NULL,
  `phrase_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_mail_messages_mail_id` (`mail_id`),
  KEY `fk_mail_messages_phrase_id` (`phrase_id`),
  CONSTRAINT `fk_mail_messages_mail_id` FOREIGN KEY (`mail_id`) REFERENCES `mail_box` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mail_messages_phrase_id` FOREIGN KEY (`phrase_id`) REFERENCES `mail_phrases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Почтовые сообщения';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mail_messages`
--

LOCK TABLES `mail_messages` WRITE;
/*!40000 ALTER TABLE `mail_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_phrases`
--

DROP TABLE IF EXISTS `mail_phrases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_phrases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `character_theme_id` int(11) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  `phrase_type` tinyint(1) DEFAULT NULL,
  `code` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_mail_phrases_character_theme_id` (`character_theme_id`),
  CONSTRAINT `fk_mail_phrases_character_theme_id` FOREIGN KEY (`character_theme_id`) REFERENCES `communication_themes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Фразы для сообщения';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mail_phrases`
--

LOCK TABLES `mail_phrases` WRITE;
/*!40000 ALTER TABLE `mail_phrases` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_phrases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_points`
--

DROP TABLE IF EXISTS `mail_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_id` int(11) NOT NULL,
  `point_id` int(11) NOT NULL,
  `add_value` int(11) NOT NULL COMMENT 'добавочное кол-во очков за данный ответ',
  `import_id` varchar(14) DEFAULT NULL COMMENT 'setvice value,used to remove old data after reimport.',
  PRIMARY KEY (`id`),
  KEY `fk_mail_points_mail_id` (`mail_id`),
  KEY `fk_mail_points_point_id` (`point_id`),
  CONSTRAINT `fk_mail_points_dialog_id` FOREIGN KEY (`mail_id`) REFERENCES `mail_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mail_points_point_id` FOREIGN KEY (`point_id`) REFERENCES `characters_points_titles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Очки для почты';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mail_points`
--

LOCK TABLES `mail_points` WRITE;
/*!40000 ALTER TABLE `mail_points` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_points` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_prefix`
--

DROP TABLE IF EXISTS `mail_prefix`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_prefix` (
  `code` varchar(255) NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mail_prefix`
--

LOCK TABLES `mail_prefix` WRITE;
/*!40000 ALTER TABLE `mail_prefix` DISABLE KEYS */;
INSERT INTO `mail_prefix` VALUES ('fwd','Fwd:'),('fwdfwd','Fwd: Fwd: '),('fwdre','Fwd: Re: '),('fwdrere','Fwd: Re: Re:'),('fwdrerere','Fwd: Re: Re: Re:'),('re','Re:'),('refwd','Re: Fwd: '),('rere','Re: Re:'),('rerere','Re: Re: Re:'),('rererere','Re:: Re: Re: Re:');
/*!40000 ALTER TABLE `mail_prefix` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_receivers`
--

DROP TABLE IF EXISTS `mail_receivers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_receivers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_mail_receivers_mail_id` (`mail_id`),
  KEY `fk_mail_receivers_receiver_id` (`receiver_id`),
  CONSTRAINT `fk_mail_receivers_mail_id` FOREIGN KEY (`mail_id`) REFERENCES `mail_box` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mail_receivers_receiver_id` FOREIGN KEY (`receiver_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Получатели писем';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mail_receivers`
--

LOCK TABLES `mail_receivers` WRITE;
/*!40000 ALTER TABLE `mail_receivers` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_receivers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_receivers_template`
--

DROP TABLE IF EXISTS `mail_receivers_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_receivers_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_mail_receivers_template_mail_id` (`mail_id`),
  KEY `fk_mail_receivers_template_receiver_id` (`receiver_id`),
  CONSTRAINT `fk_mail_receivers_template_mail_id` FOREIGN KEY (`mail_id`) REFERENCES `mail_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mail_receivers_template_receiver_id` FOREIGN KEY (`receiver_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Шаблоны получателей писем';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mail_receivers_template`
--

LOCK TABLES `mail_receivers_template` WRITE;
/*!40000 ALTER TABLE `mail_receivers_template` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_receivers_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_settings`
--

DROP TABLE IF EXISTS `mail_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) DEFAULT NULL,
  `messageArriveSound` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_mail_settings_sim_id` (`sim_id`),
  CONSTRAINT `fk_mail_settings_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Настройки почты';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mail_settings`
--

LOCK TABLES `mail_settings` WRITE;
/*!40000 ALTER TABLE `mail_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_tasks`
--

DROP TABLE IF EXISTS `mail_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `code` varchar(5) DEFAULT NULL,
  `wr` char(1) DEFAULT NULL,
  `category` tinyint(1) DEFAULT NULL,
  `import_id` varchar(14) DEFAULT NULL COMMENT 'setvice value,used to remove old data after reimport.',
  PRIMARY KEY (`id`),
  KEY `fk_mail_tasks_mail_id` (`mail_id`),
  CONSTRAINT `fk_mail_tasks_mail_id` FOREIGN KEY (`mail_id`) REFERENCES `mail_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Задачи, которые можно создать на основании письма';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mail_tasks`
--

LOCK TABLES `mail_tasks` WRITE;
/*!40000 ALTER TABLE `mail_tasks` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_template`
--

DROP TABLE IF EXISTS `mail_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL DEFAULT '5',
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text,
  `subject_id` int(11) DEFAULT NULL,
  `code` varchar(5) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Столбец нужен для типа сообщения 1 - Входящие, 2 - Исходящие, 3 - Входящие(доставлен), 4 - Исходящие(доставлен)',
  `type_of_importance` enum('none','2_min','plan','info','first_category','spam','reply_all') DEFAULT 'none' COMMENT 'Is it spam, is it impotrant etc. None - not desided by game autor jet.',
  `sent_at` datetime DEFAULT NULL,
  `import_id` varchar(14) DEFAULT NULL COMMENT 'setvice value,used to remove old data after reimport.',
  `flag_to_switch` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mail_code_unique` (`code`),
  KEY `fk_mail_template_group_id` (`group_id`),
  KEY `fk_mail_template_sender_id` (`sender_id`),
  KEY `fk_mail_template_receiver_id` (`receiver_id`),
  KEY `fk_mail_template_subject_id` (`subject_id`),
  CONSTRAINT `mail_template_subject_id` FOREIGN KEY (`subject_id`) REFERENCES `communication_themes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Шаблоны писем';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mail_template`
--

LOCK TABLES `mail_template` WRITE;
/*!40000 ALTER TABLE `mail_template` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `my_documents`
--

DROP TABLE IF EXISTS `my_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `my_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) DEFAULT NULL,
  `template_id` int(11) DEFAULT NULL,
  `fileName` varchar(128) DEFAULT NULL,
  `hidden` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_my_documents_sim_id` (`sim_id`),
  KEY `fk_my_documents_template_id` (`template_id`),
  CONSTRAINT `fk_my_documents_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_my_documents_template_id` FOREIGN KEY (`template_id`) REFERENCES `my_documents_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Мои документы';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `my_documents`
--

LOCK TABLES `my_documents` WRITE;
/*!40000 ALTER TABLE `my_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `my_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `my_documents_template`
--

DROP TABLE IF EXISTS `my_documents_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `my_documents_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fileName` varchar(128) DEFAULT NULL,
  `hidden` tinyint(1) DEFAULT '0',
  `code` varchar(5) DEFAULT NULL,
  `srcFile` varchar(255) NOT NULL,
  `format` varchar(5) DEFAULT NULL,
  `type` varchar(5) DEFAULT NULL,
  `import_id` varchar(14) DEFAULT NULL COMMENT 'setvice value,used to remove old data after reimport.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Шаблон моих документов';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `my_documents_template`
--

LOCK TABLES `my_documents_template` WRITE;
/*!40000 ALTER TABLE `my_documents_template` DISABLE KEYS */;
/*!40000 ALTER TABLE `my_documents_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phone_calls`
--

DROP TABLE IF EXISTS `phone_calls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phone_calls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL COMMENT 'идентификатор симуляции',
  `call_type` tinyint(1) DEFAULT '0',
  `from_id` int(11) DEFAULT NULL COMMENT 'Кто звонил',
  `to_id` int(11) DEFAULT NULL COMMENT 'Кому звонил',
  `call_time` time NOT NULL DEFAULT '00:00:00',
  `dialog_code` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_phone_calls_sim_id` (`sim_id`),
  KEY `fk_phone_calls_from_id` (`from_id`),
  KEY `fk_phone_calls_to_id` (`to_id`),
  CONSTRAINT `fk_phone_calls_from_id` FOREIGN KEY (`from_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_phone_calls_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_phone_calls_to_id` FOREIGN KEY (`to_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='История звонков';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phone_calls`
--

LOCK TABLES `phone_calls` WRITE;
/*!40000 ALTER TABLE `phone_calls` DISABLE KEYS */;
/*!40000 ALTER TABLE `phone_calls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `simulation_completed_parent`
--

DROP TABLE IF EXISTS `simulation_completed_parent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `simulation_completed_parent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `parent_code` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `completed_parent` (`parent_code`,`sim_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `simulation_completed_parent`
--

LOCK TABLES `simulation_completed_parent` WRITE;
/*!40000 ALTER TABLE `simulation_completed_parent` DISABLE KEYS */;
/*!40000 ALTER TABLE `simulation_completed_parent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `simulation_flags`
--

DROP TABLE IF EXISTS `simulation_flags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `simulation_flags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) DEFAULT NULL,
  `flag` varchar(5) DEFAULT NULL COMMENT 'название флага',
  `value` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_simulation_flags_sim_id` (`sim_id`),
  CONSTRAINT `fk_simulation_flags_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='флаги симуляции';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `simulation_flags`
--

LOCK TABLES `simulation_flags` WRITE;
/*!40000 ALTER TABLE `simulation_flags` DISABLE KEYS */;
/*!40000 ALTER TABLE `simulation_flags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `simulations`
--

DROP TABLE IF EXISTS `simulations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `simulations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  `difficulty` varchar(20) NOT NULL,
  `type` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_simulations_user_id` (`user_id`),
  CONSTRAINT `fk_simulations_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `simulations`
--

LOCK TABLES `simulations` WRITE;
/*!40000 ALTER TABLE `simulations` DISABLE KEYS */;
/*!40000 ALTER TABLE `simulations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `simulations_dialogs_durations`
--

DROP TABLE IF EXISTS `simulations_dialogs_durations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `simulations_dialogs_durations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `duration` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_simulations_dialogs_durations_sim_id` (`sim_id`),
  CONSTRAINT `fk_simulations_dialogs_durations_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `simulations_dialogs_durations`
--

LOCK TABLES `simulations_dialogs_durations` WRITE;
/*!40000 ALTER TABLE `simulations_dialogs_durations` DISABLE KEYS */;
/*!40000 ALTER TABLE `simulations_dialogs_durations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `simulations_dialogs_points`
--

DROP TABLE IF EXISTS `simulations_dialogs_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `simulations_dialogs_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `point_id` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `value` float(10,2) NOT NULL,
  `count6x` int(11) DEFAULT NULL,
  `value6x` float(10,2) DEFAULT NULL,
  `count_negative` int(11) NOT NULL,
  `value_negative` float(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_simulations_dialogs_points_sim_id` (`sim_id`),
  KEY `fk_simulations_dialogs_points_point_id` (`point_id`),
  CONSTRAINT `fk_simulations_dialogs_points_point_id` FOREIGN KEY (`point_id`) REFERENCES `characters_points_titles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_simulations_dialogs_points_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `simulations_dialogs_points`
--

LOCK TABLES `simulations_dialogs_points` WRITE;
/*!40000 ALTER TABLE `simulations_dialogs_points` DISABLE KEYS */;
/*!40000 ALTER TABLE `simulations_dialogs_points` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `simulations_excel_points`
--

DROP TABLE IF EXISTS `simulations_excel_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `simulations_excel_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL COMMENT 'идентификатор симуляции',
  `value` float(10,2) DEFAULT NULL,
  `formula_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_simulations_excel_points_sim_id` (`sim_id`),
  KEY `fk_simulations_excel_points_formula_id` (`formula_id`),
  CONSTRAINT `fk_simulations_excel_points_formula_id` FOREIGN KEY (`formula_id`) REFERENCES `excel_points_formula` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_simulations_excel_points_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Баллы, набранные в экселе';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `simulations_excel_points`
--

LOCK TABLES `simulations_excel_points` WRITE;
/*!40000 ALTER TABLE `simulations_excel_points` DISABLE KEYS */;
/*!40000 ALTER TABLE `simulations_excel_points` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `simulations_mail_points`
--

DROP TABLE IF EXISTS `simulations_mail_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `simulations_mail_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL COMMENT 'идентификатор симуляции',
  `point_id` int(11) NOT NULL COMMENT 'поинт',
  `value` float(10,2) DEFAULT NULL,
  `scale_type_id` int(11) DEFAULT NULL COMMENT '1 - positive, 2 - negative, 3 - personal.',
  PRIMARY KEY (`id`),
  KEY `fk_simulations_mail_points_sim_id` (`sim_id`),
  KEY `fk_simulations_mail_point_id` (`point_id`),
  CONSTRAINT `fk_simulations_mail_points_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_simulations_mail_point_id` FOREIGN KEY (`point_id`) REFERENCES `characters_points_titles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8628 DEFAULT CHARSET=utf8 COMMENT='Баллы, набранные в почтовике';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `simulations_mail_points`
--

LOCK TABLES `simulations_mail_points` WRITE;
/*!40000 ALTER TABLE `simulations_mail_points` DISABLE KEYS */;
INSERT INTO `simulations_mail_points` VALUES (5703,7219,77,0.00,1),(5704,7219,79,0.00,2),(5705,7219,80,0.00,2),(5706,7219,78,0.00,1),(5707,7219,74,0.00,1),(5708,7219,84,0.75,1),(5709,7220,77,0.00,1),(5710,7220,79,0.00,2),(5711,7220,80,0.00,2),(5712,7220,78,0.00,1),(5713,7220,74,0.00,1),(5714,7220,84,0.75,1),(5715,7225,77,0.00,1),(5716,7225,79,-0.20,2),(5717,7225,80,0.00,2),(5718,7225,78,0.00,1),(5719,7225,74,0.00,1),(5720,7225,84,0.75,1),(5721,7226,77,0.00,1),(5722,7226,79,0.00,2),(5723,7226,80,0.00,2),(5724,7226,78,0.00,1),(5725,7226,74,0.00,1),(5726,7226,84,0.75,1),(5727,7229,77,0.00,1),(5728,7229,79,0.00,2),(5729,7229,80,0.00,2),(5730,7229,78,0.00,1),(5731,7229,74,0.00,1),(5732,7229,84,0.75,1),(5733,7232,77,0.00,1),(5734,7232,79,0.00,2),(5735,7232,80,0.00,2),(5736,7232,78,0.00,1),(5737,7232,74,0.00,1),(5738,7232,84,0.75,1),(5739,7233,77,0.00,1),(5740,7233,79,0.00,2),(5741,7233,80,0.00,2),(5742,7233,78,0.00,1),(5743,7233,74,0.00,1),(5744,7233,84,0.75,1),(5745,7234,77,0.00,1),(5746,7234,79,0.00,2),(5747,7234,80,0.00,2),(5748,7234,78,0.00,1),(5749,7234,74,0.00,1),(5750,7234,84,0.75,1),(5751,7235,77,0.00,1),(5752,7235,79,0.00,2),(5753,7235,80,0.00,2),(5754,7235,78,0.00,1),(5755,7235,74,0.00,1),(5756,7235,84,0.75,1),(5757,7248,77,0.00,1),(5758,7248,79,0.00,2),(5759,7248,80,0.00,2),(5760,7248,78,0.00,1),(5761,7248,74,0.00,1),(5762,7248,84,0.75,1),(5763,7250,77,0.00,1),(5764,7250,79,0.00,2),(5765,7250,80,0.00,2),(5766,7250,78,0.00,1),(5767,7250,74,0.00,1),(5768,7250,84,0.75,1),(5769,7253,77,0.00,1),(5770,7253,79,0.00,2),(5771,7253,80,0.00,2),(5772,7253,78,0.00,1),(5773,7253,74,0.00,1),(5774,7253,84,0.75,1),(5775,7254,77,0.00,1),(5776,7254,79,0.00,2),(5777,7254,80,0.00,2),(5778,7254,78,0.00,1),(5779,7254,74,0.00,1),(5780,7254,84,0.75,1),(5781,7260,77,0.00,1),(5782,7260,79,0.00,2),(5783,7260,80,0.00,2),(5784,7260,78,0.00,1),(5785,7260,74,0.00,1),(5786,7260,84,0.75,1),(5787,7262,77,0.00,1),(5788,7262,79,0.00,2),(5789,7262,80,0.00,2),(5790,7262,78,0.00,1),(5791,7262,74,0.00,1),(5792,7262,84,0.75,1),(5793,7265,77,0.00,1),(5794,7265,79,0.00,2),(5795,7265,80,0.00,2),(5796,7265,78,0.00,1),(5797,7265,74,0.00,1),(5798,7265,84,0.75,1),(5799,7272,77,0.00,1),(5800,7272,79,0.00,2),(5801,7272,80,0.00,2),(5802,7272,78,0.00,1),(5803,7272,74,0.00,1),(5804,7272,84,0.75,1),(5805,7274,77,0.00,1),(5806,7274,79,0.00,2),(5807,7274,80,0.00,2),(5808,7274,78,0.00,1),(5809,7274,74,0.00,1),(5810,7274,84,0.75,1),(5811,7275,77,0.00,1),(5812,7275,79,0.00,2),(5813,7275,80,0.00,2),(5814,7275,78,0.00,1),(5815,7275,74,0.00,1),(5816,7275,84,0.75,1),(5817,7279,77,0.00,1),(5818,7279,79,0.00,2),(5819,7279,80,0.00,2),(5820,7279,78,0.00,1),(5821,7279,74,0.00,1),(5822,7279,84,0.75,1),(5823,7280,77,0.00,1),(5824,7280,79,0.00,2),(5825,7280,80,0.00,2),(5826,7280,78,0.00,1),(5827,7280,74,0.00,1),(5828,7280,84,0.75,1),(5829,7282,77,0.00,1),(5830,7282,79,0.00,2),(5831,7282,80,0.00,2),(5832,7282,78,0.00,1),(5833,7282,74,0.00,1),(5834,7282,84,0.75,1),(5835,7284,77,0.00,1),(5836,7284,79,0.00,2),(5837,7284,80,0.00,2),(5838,7284,78,0.00,1),(5839,7284,74,0.00,1),(5840,7284,84,0.75,1),(5841,7285,77,0.00,1),(5842,7285,79,0.00,2),(5843,7285,80,0.00,2),(5844,7285,78,0.00,1),(5845,7285,74,0.00,1),(5846,7285,84,0.75,1),(5847,7286,77,0.00,1),(5848,7286,79,0.00,2),(5849,7286,80,0.00,2),(5850,7286,78,0.00,1),(5851,7286,74,0.00,1),(5852,7286,84,0.75,1),(5853,7288,77,0.00,1),(5854,7288,79,-0.20,2),(5855,7288,80,0.00,2),(5856,7288,78,0.00,1),(5857,7288,74,0.00,1),(5858,7288,84,0.75,1),(5859,7289,77,0.00,1),(5860,7289,79,-1.00,2),(5861,7289,80,0.00,2),(5862,7289,78,0.00,1),(5863,7289,74,0.00,1),(5864,7289,84,0.75,1),(5865,7290,77,0.00,1),(5866,7290,79,0.00,2),(5867,7290,80,0.00,2),(5868,7290,78,0.00,1),(5869,7290,74,0.00,1),(5870,7290,84,0.75,1),(5871,7292,77,0.00,1),(5872,7292,79,0.00,2),(5873,7292,80,0.00,2),(5874,7292,78,0.00,1),(5875,7292,74,0.00,1),(5876,7292,84,0.75,1),(5877,7310,77,0.00,1),(5878,7310,79,-0.20,2),(5879,7310,80,0.00,2),(5880,7310,78,0.00,1),(5881,7310,74,0.00,1),(5882,7310,84,0.75,1),(5883,7311,77,0.00,1),(5884,7311,79,0.00,2),(5885,7311,80,0.00,2),(5886,7311,78,0.00,1),(5887,7311,74,0.00,1),(5888,7311,84,0.75,1),(5889,7314,77,0.00,1),(5890,7314,79,0.00,2),(5891,7314,80,0.00,2),(5892,7314,78,0.00,1),(5893,7314,74,0.00,1),(5894,7314,84,0.75,1),(5895,7318,77,0.00,1),(5896,7318,79,0.00,2),(5897,7318,80,0.00,2),(5898,7318,78,0.00,1),(5899,7318,74,0.00,1),(5900,7318,84,0.75,1),(5901,7328,77,0.00,1),(5902,7328,79,0.00,2),(5903,7328,80,0.00,2),(5904,7328,78,0.00,1),(5905,7328,74,0.00,1),(5906,7328,84,0.75,1),(5907,7332,77,0.00,1),(5908,7332,79,0.00,2),(5909,7332,80,0.00,2),(5910,7332,78,0.00,1),(5911,7332,74,0.00,1),(5912,7332,84,0.75,1),(5913,7336,77,0.00,1),(5914,7336,79,0.00,2),(5915,7336,80,0.00,2),(5916,7336,78,0.00,1),(5917,7336,74,0.00,1),(5918,7336,84,0.75,1),(5919,7337,77,0.00,1),(5920,7337,79,0.00,2),(5921,7337,80,0.00,2),(5922,7337,78,0.00,1),(5923,7337,74,0.00,1),(5924,7337,84,0.75,1),(5925,7338,77,0.00,1),(5926,7338,79,-0.40,2),(5927,7338,80,0.00,2),(5928,7338,78,0.00,1),(5929,7338,74,0.00,1),(5930,7338,84,0.75,1),(5931,7339,77,0.00,1),(5932,7339,79,0.00,2),(5933,7339,80,0.00,2),(5934,7339,78,0.00,1),(5935,7339,74,0.00,1),(5936,7339,84,0.75,1),(5937,7341,77,0.00,1),(5938,7341,79,0.00,2),(5939,7341,80,0.00,2),(5940,7341,78,0.00,1),(5941,7341,74,0.00,1),(5942,7341,84,0.75,1),(5943,7342,77,0.00,1),(5944,7342,79,0.00,2),(5945,7342,80,0.00,2),(5946,7342,78,0.00,1),(5947,7342,74,0.00,1),(5948,7342,84,0.75,1),(5949,7343,77,0.00,1),(5950,7343,79,-0.20,2),(5951,7343,80,0.00,2),(5952,7343,78,0.00,1),(5953,7343,74,0.00,1),(5954,7343,84,0.75,1),(5955,7344,77,0.00,1),(5956,7344,79,0.00,2),(5957,7344,80,0.00,2),(5958,7344,78,0.00,1),(5959,7344,74,0.00,1),(5960,7344,84,0.75,1),(5961,7345,77,0.00,1),(5962,7345,79,0.00,2),(5963,7345,80,0.00,2),(5964,7345,78,0.00,1),(5965,7345,74,0.00,1),(5966,7345,84,0.75,1),(5967,7346,77,0.00,1),(5968,7346,79,0.00,2),(5969,7346,80,0.00,2),(5970,7346,78,0.00,1),(5971,7346,74,0.00,1),(5972,7346,84,0.75,1),(5973,7347,77,0.00,1),(5974,7347,79,0.00,2),(5975,7347,80,0.00,2),(5976,7347,78,0.00,1),(5977,7347,74,0.00,1),(5978,7347,84,0.75,1),(5979,7348,77,0.00,1),(5980,7348,79,0.00,2),(5981,7348,80,0.00,2),(5982,7348,78,0.00,1),(5983,7348,74,0.00,1),(5984,7348,84,0.75,1),(5985,7349,77,0.00,1),(5986,7349,79,0.00,2),(5987,7349,80,0.00,2),(5988,7349,78,0.00,1),(5989,7349,74,0.00,1),(5990,7349,84,0.75,1),(5991,7350,77,0.00,1),(5992,7350,79,0.00,2),(5993,7350,80,0.00,2),(5994,7350,78,0.00,1),(5995,7350,74,0.00,1),(5996,7350,84,0.75,1),(5997,7351,77,0.00,1),(5998,7351,79,0.00,2),(5999,7351,80,0.00,2),(6000,7351,78,0.00,1),(6001,7351,74,0.00,1),(6002,7351,84,0.75,1),(6003,7352,77,0.00,1),(6004,7352,79,0.00,2),(6005,7352,80,0.00,2),(6006,7352,78,0.00,1),(6007,7352,74,0.00,1),(6008,7352,84,0.75,1),(6009,7354,77,0.00,1),(6010,7354,79,0.00,2),(6011,7354,80,0.00,2),(6012,7354,78,0.00,1),(6013,7354,74,0.00,1),(6014,7354,84,0.75,1),(6015,7355,77,0.00,1),(6016,7355,79,0.00,2),(6017,7355,80,0.00,2),(6018,7355,78,0.00,1),(6019,7355,74,0.00,1),(6020,7355,84,0.75,1),(6021,7356,77,0.00,1),(6022,7356,79,0.00,2),(6023,7356,80,0.00,2),(6024,7356,78,0.00,1),(6025,7356,74,0.00,1),(6026,7356,84,0.75,1),(6027,7357,77,0.00,1),(6028,7357,79,0.00,2),(6029,7357,80,0.00,2),(6030,7357,78,0.00,1),(6031,7357,74,0.00,1),(6032,7357,84,0.75,1),(6033,7362,77,0.00,1),(6034,7362,79,0.00,2),(6035,7362,80,0.00,2),(6036,7362,78,0.00,1),(6037,7362,74,0.00,1),(6038,7362,84,0.75,1),(6039,7363,77,0.00,1),(6040,7363,79,0.00,2),(6041,7363,80,0.00,2),(6042,7363,78,0.00,1),(6043,7363,74,0.00,1),(6044,7363,84,0.75,1),(6045,7366,77,0.00,1),(6046,7366,79,0.00,2),(6047,7366,80,0.00,2),(6048,7366,78,0.00,1),(6049,7366,74,0.00,1),(6050,7366,84,0.75,1),(6051,7367,77,0.00,1),(6052,7367,79,0.00,2),(6053,7367,80,0.00,2),(6054,7367,78,0.00,1),(6055,7367,74,0.00,1),(6056,7367,84,0.75,1),(6057,7368,77,0.00,1),(6058,7368,79,0.00,2),(6059,7368,80,0.00,2),(6060,7368,78,0.00,1),(6061,7368,74,0.00,1),(6062,7368,84,0.75,1),(6063,7369,77,0.00,1),(6064,7369,79,0.00,2),(6065,7369,80,0.00,2),(6066,7369,78,0.00,1),(6067,7369,74,0.00,1),(6068,7369,84,0.75,1),(6069,7370,77,0.00,1),(6070,7370,79,0.00,2),(6071,7370,80,0.00,2),(6072,7370,78,0.00,1),(6073,7370,74,0.00,1),(6074,7370,84,0.75,1),(6075,7374,77,0.00,1),(6076,7374,79,0.00,2),(6077,7374,80,0.00,2),(6078,7374,78,0.00,1),(6079,7374,74,0.00,1),(6080,7374,84,0.75,1),(6081,7378,77,0.00,1),(6082,7378,79,0.00,2),(6083,7378,80,0.00,2),(6084,7378,78,0.00,1),(6085,7378,74,0.00,1),(6086,7378,84,0.75,1),(6087,7392,77,0.00,1),(6088,7392,79,0.00,2),(6089,7392,80,0.00,2),(6090,7392,78,0.00,1),(6091,7392,74,1.00,1),(6092,7392,84,0.75,1),(6093,7395,77,0.00,1),(6094,7395,79,0.00,2),(6095,7395,80,0.00,2),(6096,7395,78,0.00,1),(6097,7395,74,0.00,1),(6098,7395,84,0.75,1),(6099,7399,77,0.00,1),(6100,7399,79,0.00,2),(6101,7399,80,0.00,2),(6102,7399,78,0.00,1),(6103,7399,74,0.00,1),(6104,7399,84,0.75,1),(6105,7401,77,0.00,1),(6106,7401,79,0.00,2),(6107,7401,80,0.00,2),(6108,7401,78,0.00,1),(6109,7401,74,0.00,1),(6110,7401,84,0.75,1),(6111,7402,77,0.00,1),(6112,7402,79,0.00,2),(6113,7402,80,0.00,2),(6114,7402,78,0.00,1),(6115,7402,74,0.00,1),(6116,7402,84,0.75,1),(6117,7406,77,0.00,1),(6118,7406,79,0.00,2),(6119,7406,80,0.00,2),(6120,7406,78,0.00,1),(6121,7406,74,0.00,1),(6122,7406,84,0.75,1),(6123,7408,77,0.00,1),(6124,7408,79,0.00,2),(6125,7408,80,0.00,2),(6126,7408,78,0.00,1),(6127,7408,74,0.00,1),(6128,7408,84,0.75,1),(6129,7411,77,0.00,1),(6130,7411,79,0.00,2),(6131,7411,80,0.00,2),(6132,7411,78,0.00,1),(6133,7411,74,0.00,1),(6134,7411,84,0.75,1),(6135,7413,77,0.00,1),(6136,7413,79,0.00,2),(6137,7413,80,0.00,2),(6138,7413,78,0.00,1),(6139,7413,74,0.00,1),(6140,7413,84,0.75,1),(6141,7412,77,0.00,1),(6142,7412,79,0.00,2),(6143,7412,80,0.00,2),(6144,7412,78,0.00,1),(6145,7412,74,0.00,1),(6146,7412,84,0.75,1),(6147,7414,77,0.00,1),(6148,7414,79,0.00,2),(6149,7414,80,0.00,2),(6150,7414,78,0.00,1),(6151,7414,74,0.00,1),(6152,7414,84,0.75,1),(6153,7409,77,0.00,1),(6154,7409,79,0.00,2),(6155,7409,80,0.00,2),(6156,7409,78,0.00,1),(6157,7409,74,0.00,1),(6158,7409,84,0.75,1),(6159,7416,77,0.00,1),(6160,7416,79,0.00,2),(6161,7416,80,0.00,2),(6162,7416,78,0.00,1),(6163,7416,74,0.00,1),(6164,7416,84,0.75,1),(6165,7429,77,0.00,1),(6166,7429,79,0.00,2),(6167,7429,80,0.00,2),(6168,7429,78,0.00,1),(6169,7429,74,0.00,1),(6170,7429,84,0.75,1),(6171,7431,77,0.00,1),(6172,7431,79,0.00,2),(6173,7431,80,0.00,2),(6174,7431,78,0.00,1),(6175,7431,74,0.00,1),(6176,7431,84,0.75,1),(6177,7436,77,0.00,1),(6178,7436,79,0.00,2),(6179,7436,80,0.00,2),(6180,7436,78,0.00,1),(6181,7436,74,0.00,1),(6182,7436,84,0.75,1),(6183,7435,77,0.00,1),(6184,7435,79,0.00,2),(6185,7435,80,0.00,2),(6186,7435,78,0.00,1),(6187,7435,74,0.00,1),(6188,7435,84,0.75,1),(6189,7437,77,0.00,1),(6190,7437,79,0.00,2),(6191,7437,80,0.00,2),(6192,7437,78,0.00,1),(6193,7437,74,0.00,1),(6194,7437,84,0.75,1),(6195,7438,77,0.00,1),(6196,7438,79,0.00,2),(6197,7438,80,0.00,2),(6198,7438,78,0.00,1),(6199,7438,74,0.00,1),(6200,7438,84,0.75,1),(6201,7433,77,0.00,1),(6202,7433,79,0.00,2),(6203,7433,80,0.00,2),(6204,7433,78,0.00,1),(6205,7433,74,0.00,1),(6206,7433,84,0.75,1),(6207,7441,77,0.00,1),(6208,7441,79,0.00,2),(6209,7441,80,0.00,2),(6210,7441,78,0.00,1),(6211,7441,74,0.00,1),(6212,7441,84,0.75,1),(6213,7443,77,0.00,1),(6214,7443,79,0.00,2),(6215,7443,80,0.00,2),(6216,7443,78,0.00,1),(6217,7443,74,0.00,1),(6218,7443,84,0.75,1),(6219,7448,77,0.00,1),(6220,7448,79,0.00,2),(6221,7448,80,0.00,2),(6222,7448,78,0.00,1),(6223,7448,74,0.00,1),(6224,7448,84,0.75,1),(6225,7447,77,0.00,1),(6226,7447,79,0.00,2),(6227,7447,80,0.00,2),(6228,7447,78,0.00,1),(6229,7447,74,0.00,1),(6230,7447,84,0.75,1),(6231,7450,77,0.00,1),(6232,7450,79,0.00,2),(6233,7450,80,0.00,2),(6234,7450,78,0.00,1),(6235,7450,74,0.00,1),(6236,7450,84,0.75,1),(6237,7451,77,0.00,1),(6238,7451,79,0.00,2),(6239,7451,80,0.00,2),(6240,7451,78,0.00,1),(6241,7451,74,0.00,1),(6242,7451,84,0.75,1),(6243,7454,77,0.00,1),(6244,7454,79,0.00,2),(6245,7454,80,0.00,2),(6246,7454,78,0.00,1),(6247,7454,74,0.00,1),(6248,7454,84,0.75,1),(6249,7456,77,0.00,1),(6250,7456,79,0.00,2),(6251,7456,80,0.00,2),(6252,7456,78,0.00,1),(6253,7456,74,0.00,1),(6254,7456,84,0.75,1),(6255,7460,77,0.00,1),(6256,7460,79,0.00,2),(6257,7460,80,0.00,2),(6258,7460,78,0.00,1),(6259,7460,74,0.00,1),(6260,7460,84,0.75,1),(6261,7461,77,0.00,1),(6262,7461,79,0.00,2),(6263,7461,80,0.00,2),(6264,7461,78,0.00,1),(6265,7461,74,0.00,1),(6266,7461,84,0.75,1),(6267,7465,77,0.00,1),(6268,7465,79,0.00,2),(6269,7465,80,0.00,2),(6270,7465,78,0.00,1),(6271,7465,74,0.00,1),(6272,7465,84,0.75,1),(6273,7458,77,0.00,1),(6274,7458,79,0.00,2),(6275,7458,80,0.00,2),(6276,7458,78,0.00,1),(6277,7458,74,0.00,1),(6278,7458,84,0.75,1),(6279,7466,77,0.00,1),(6280,7466,79,0.00,2),(6281,7466,80,0.00,2),(6282,7466,78,0.00,1),(6283,7466,74,0.00,1),(6284,7466,84,0.75,1),(6285,7467,77,0.00,1),(6286,7467,79,-0.20,2),(6287,7467,80,0.00,2),(6288,7467,78,0.00,1),(6289,7467,74,0.00,1),(6290,7467,84,0.75,1),(6291,7468,77,0.00,1),(6292,7468,79,0.00,2),(6293,7468,80,0.00,2),(6294,7468,78,0.00,1),(6295,7468,74,0.00,1),(6296,7468,84,0.75,1),(6297,7469,77,0.00,1),(6298,7469,79,0.00,2),(6299,7469,80,0.00,2),(6300,7473,77,0.00,1),(6301,7473,79,0.00,2),(6302,7473,80,0.00,2),(6303,7473,78,0.00,1),(6304,7473,74,1.00,1),(6305,7473,84,0.75,1),(6306,7474,77,0.00,1),(6307,7474,79,0.00,2),(6308,7474,80,0.00,2),(6309,7474,78,0.00,1),(6310,7474,74,0.00,1),(6311,7474,84,0.75,1),(6312,7476,77,0.00,1),(6313,7476,79,0.00,2),(6314,7476,80,0.00,2),(6315,7476,78,0.00,1),(6316,7476,74,0.00,1),(6317,7476,84,0.75,1),(6318,7478,77,0.00,1),(6319,7478,79,0.00,2),(6320,7478,80,0.00,2),(6321,7478,78,0.00,1),(6322,7478,74,0.00,1),(6323,7478,84,0.75,1),(6324,7477,77,0.00,1),(6325,7477,79,0.00,2),(6326,7477,80,0.00,2),(6327,7477,78,0.00,1),(6328,7477,74,0.00,1),(6329,7477,84,0.75,1),(6330,7483,77,0.00,1),(6331,7483,79,-0.20,2),(6332,7483,80,0.00,2),(6333,7483,78,0.00,1),(6334,7483,74,1.00,1),(6335,7483,84,0.75,1),(6336,7485,77,0.00,1),(6337,7485,79,-0.20,2),(6338,7485,80,0.00,2),(6339,7485,78,0.00,1),(6340,7485,74,1.00,1),(6341,7485,84,0.75,1),(6342,7486,77,0.00,1),(6343,7486,79,-0.20,2),(6344,7486,80,0.00,2),(6345,7486,78,0.00,1),(6346,7486,74,1.00,1),(6347,7486,84,0.75,1),(6348,7489,77,0.00,1),(6349,7489,79,0.00,2),(6350,7489,80,0.00,2),(6351,7489,78,0.00,1),(6352,7489,74,0.00,1),(6353,7489,84,0.75,1),(6354,7494,77,0.00,1),(6355,7494,79,0.00,2),(6356,7494,80,0.00,2),(6357,7494,78,0.00,1),(6358,7494,74,0.00,1),(6359,7494,84,0.75,1),(6360,7495,77,0.00,1),(6361,7495,79,0.00,2),(6362,7495,80,0.00,2),(6363,7495,78,0.00,1),(6364,7495,74,0.00,1),(6365,7495,84,0.75,1),(6366,7498,77,0.00,1),(6367,7498,79,0.00,2),(6368,7498,80,0.00,2),(6369,7498,78,0.00,1),(6370,7498,74,0.00,1),(6371,7498,84,0.75,1),(6372,7499,77,0.00,1),(6373,7499,79,0.00,2),(6374,7499,80,0.00,2),(6375,7499,78,0.00,1),(6376,7499,74,0.00,1),(6377,7499,84,0.75,1),(6378,7500,77,0.00,1),(6379,7500,79,0.00,2),(6380,7500,80,0.00,2),(6381,7500,78,0.00,1),(6382,7500,74,0.00,1),(6383,7500,84,0.75,1),(6384,7505,77,0.00,1),(6385,7505,79,-0.20,2),(6386,7505,80,0.00,2),(6387,7505,78,0.00,1),(6388,7505,74,1.00,1),(6389,7505,84,0.75,1),(6390,7506,77,0.00,1),(6391,7506,79,0.00,2),(6392,7506,80,0.00,2),(6393,7506,78,0.00,1),(6394,7506,74,0.00,1),(6395,7506,84,0.75,1),(6396,7507,77,0.00,1),(6397,7507,79,0.00,2),(6398,7507,80,0.00,2),(6399,7507,78,0.00,1),(6400,7507,74,0.00,1),(6401,7507,84,0.75,1),(6402,7508,77,0.00,1),(6403,7508,79,0.00,2),(6404,7508,80,0.00,2),(6405,7508,78,0.00,1),(6406,7508,74,0.00,1),(6407,7508,84,0.75,1),(6408,7512,77,0.00,1),(6409,7512,79,-0.20,2),(6410,7512,80,0.00,2),(6411,7512,78,0.00,1),(6412,7512,74,0.00,1),(6413,7512,84,0.75,1),(6414,7516,77,0.00,1),(6415,7516,79,0.00,2),(6416,7516,80,0.00,2),(6417,7516,78,0.00,1),(6418,7516,74,0.00,1),(6419,7516,84,0.75,1),(6420,7518,77,0.00,1),(6421,7518,79,0.00,2),(6422,7518,80,0.00,2),(6423,7518,78,0.00,1),(6424,7518,74,0.00,1),(6425,7518,84,0.75,1),(6426,7519,77,0.00,1),(6427,7519,79,0.00,2),(6428,7519,80,0.00,2),(6429,7519,78,0.00,1),(6430,7519,74,0.00,1),(6431,7519,84,0.75,1),(6432,7520,77,0.00,1),(6433,7520,79,0.00,2),(6434,7520,80,0.00,2),(6435,7520,78,0.00,1),(6436,7520,74,0.00,1),(6437,7520,84,0.75,1),(6438,7521,77,0.00,1),(6439,7521,79,0.00,2),(6440,7521,80,0.00,2),(6441,7521,78,0.00,1),(6442,7521,74,0.00,1),(6443,7521,84,0.75,1),(6444,7522,77,0.00,1),(6445,7522,79,0.00,2),(6446,7522,80,0.00,2),(6447,7522,78,0.00,1),(6448,7522,74,0.00,1),(6449,7522,84,0.75,1),(6450,7523,77,0.00,1),(6451,7523,79,0.00,2),(6452,7523,80,0.00,2),(6453,7523,78,0.00,1),(6454,7523,74,0.00,1),(6455,7523,84,0.75,1),(6456,7525,77,0.00,1),(6457,7525,79,0.00,2),(6458,7525,80,0.00,2),(6459,7525,78,0.00,1),(6460,7525,74,0.00,1),(6461,7525,84,0.75,1),(6462,7526,77,0.00,1),(6463,7526,79,0.00,2),(6464,7526,80,0.00,2),(6465,7526,78,0.00,1),(6466,7526,74,0.00,1),(6467,7526,84,0.75,1),(6468,7527,77,0.00,1),(6469,7527,79,0.00,2),(6470,7527,80,0.00,2),(6471,7527,78,0.00,1),(6472,7527,74,0.00,1),(6473,7527,84,0.75,1),(6474,7528,77,0.00,1),(6475,7528,79,0.00,2),(6476,7528,80,0.00,2),(6477,7528,78,0.00,1),(6478,7528,74,0.00,1),(6479,7528,84,0.75,1),(6480,7529,77,0.00,1),(6481,7529,79,0.00,2),(6482,7529,80,0.00,2),(6483,7529,78,0.00,1),(6484,7529,74,0.00,1),(6485,7529,84,0.75,1),(6486,7531,77,0.00,1),(6487,7531,79,-0.20,2),(6488,7531,80,0.00,2),(6489,7531,78,0.00,1),(6490,7531,74,0.00,1),(6491,7531,84,0.75,1),(6492,7532,77,0.00,1),(6493,7532,79,-0.20,2),(6494,7532,80,0.00,2),(6495,7532,78,0.00,1),(6496,7532,74,0.00,1),(6497,7532,84,0.75,1),(6498,7533,77,0.00,1),(6499,7533,79,0.00,2),(6500,7533,80,0.00,2),(6501,7533,78,0.00,1),(6502,7533,74,0.00,1),(6503,7533,84,0.75,1),(6504,7534,77,0.00,1),(6505,7534,79,0.00,2),(6506,7534,80,0.00,2),(6507,7534,78,0.00,1),(6508,7534,74,0.00,1),(6509,7534,84,0.75,1),(6510,7535,77,0.00,1),(6511,7535,79,0.00,2),(6512,7535,80,0.00,2),(6513,7535,78,0.00,1),(6514,7535,74,0.00,1),(6515,7535,84,0.75,1),(6516,7536,77,0.00,1),(6517,7536,79,0.00,2),(6518,7536,80,0.00,2),(6519,7536,78,0.00,1),(6520,7536,74,0.00,1),(6521,7536,84,0.75,1),(6522,7537,77,0.00,1),(6523,7537,79,0.00,2),(6524,7537,80,0.00,2),(6525,7537,78,0.00,1),(6526,7537,74,0.00,1),(6527,7537,84,0.75,1),(6528,7539,77,0.00,1),(6529,7539,79,0.00,2),(6530,7539,80,0.00,2),(6531,7539,78,0.00,1),(6532,7539,74,0.00,1),(6533,7539,84,0.75,1),(6534,7540,77,0.00,1),(6535,7540,79,0.00,2),(6536,7540,80,0.00,2),(6537,7540,78,0.00,1),(6538,7540,74,0.00,1),(6539,7540,84,0.75,1),(6540,7541,77,0.00,1),(6541,7541,79,0.00,2),(6542,7541,80,0.00,2),(6543,7541,78,0.00,1),(6544,7541,74,0.00,1),(6545,7541,84,0.75,1),(6546,7538,77,0.00,1),(6547,7538,79,0.00,2),(6548,7538,80,0.00,2),(6549,7538,78,0.00,1),(6550,7538,74,0.00,1),(6551,7538,84,0.75,1),(6552,7542,77,0.00,1),(6553,7542,79,0.00,2),(6554,7542,80,0.00,2),(6555,7542,78,0.00,1),(6556,7542,74,0.00,1),(6557,7542,84,0.75,1),(6558,7544,77,0.00,1),(6559,7544,79,0.00,2),(6560,7544,80,0.00,2),(6561,7544,78,0.00,1),(6562,7544,74,0.00,1),(6563,7544,84,0.75,1),(6564,7543,77,0.00,1),(6565,7543,79,0.00,2),(6566,7543,80,0.00,2),(6567,7543,78,0.00,1),(6568,7543,74,0.00,1),(6569,7543,84,0.75,1),(6570,7545,77,0.00,1),(6571,7545,79,0.00,2),(6572,7545,80,0.00,2),(6573,7545,78,0.00,1),(6574,7545,74,0.00,1),(6575,7545,84,0.75,1),(6576,7546,77,0.00,1),(6577,7546,79,0.00,2),(6578,7546,80,0.00,2),(6579,7546,78,0.00,1),(6580,7546,74,0.00,1),(6581,7546,84,0.75,1),(6582,7547,77,0.00,1),(6583,7547,79,0.00,2),(6584,7547,80,0.00,2),(6585,7547,78,0.00,1),(6586,7547,74,0.00,1),(6587,7547,84,0.75,1),(6588,7549,77,0.00,1),(6589,7549,79,0.00,2),(6590,7549,80,0.00,2),(6591,7549,78,0.00,1),(6592,7549,74,0.00,1),(6593,7549,84,0.75,1),(6594,7551,77,0.00,1),(6595,7551,79,0.00,2),(6596,7551,80,0.00,2),(6597,7551,78,0.00,1),(6598,7551,74,0.00,1),(6599,7551,84,0.75,1),(6600,7552,77,0.00,1),(6601,7552,79,0.00,2),(6602,7552,80,0.00,2),(6603,7552,78,0.00,1),(6604,7552,74,0.00,1),(6605,7552,84,0.75,1),(6606,7553,77,0.00,1),(6607,7553,79,0.00,2),(6608,7553,80,0.00,2),(6609,7553,78,0.00,1),(6610,7553,74,0.00,1),(6611,7553,84,0.75,1),(6612,7554,77,0.00,1),(6613,7554,79,0.00,2),(6614,7554,80,0.00,2),(6615,7554,78,0.00,1),(6616,7554,74,0.00,1),(6617,7554,84,0.75,1),(6618,7555,77,0.00,1),(6619,7555,79,0.00,2),(6620,7555,80,0.00,2),(6621,7555,78,0.00,1),(6622,7555,74,0.00,1),(6623,7555,84,0.75,1),(6624,7567,77,0.00,1),(6625,7567,79,0.00,2),(6626,7567,80,0.00,2),(6627,7567,78,0.00,1),(6628,7567,74,0.00,1),(6629,7567,84,0.75,1),(6630,7569,77,0.00,1),(6631,7569,79,0.00,2),(6632,7569,80,0.00,2),(6633,7569,78,0.00,1),(6634,7569,74,0.00,1),(6635,7569,84,0.75,1),(6636,7570,77,0.00,1),(6637,7570,79,0.00,2),(6638,7570,80,0.00,2),(6639,7570,78,0.00,1),(6640,7570,74,0.00,1),(6641,7570,84,0.75,1),(6642,7571,77,0.00,1),(6643,7571,79,0.00,2),(6644,7571,80,0.00,2),(6645,7571,78,0.00,1),(6646,7571,74,0.00,1),(6647,7571,84,0.75,1),(6648,7572,77,0.00,1),(6649,7572,79,0.00,2),(6650,7572,80,0.00,2),(6651,7572,78,0.00,1),(6652,7572,74,0.00,1),(6653,7572,84,0.75,1),(6654,7578,77,0.00,1),(6655,7578,79,0.00,2),(6656,7578,80,0.00,2),(6657,7578,78,0.00,1),(6658,7578,74,0.00,1),(6659,7578,84,0.75,1),(6660,7580,77,0.00,1),(6661,7580,79,0.00,2),(6662,7580,80,0.00,2),(6663,7580,78,0.00,1),(6664,7580,74,0.00,1),(6665,7580,84,0.75,1),(6666,7581,77,0.00,1),(6667,7581,79,0.00,2),(6668,7581,80,0.00,2),(6669,7581,78,0.00,1),(6670,7581,74,0.00,1),(6671,7581,84,0.75,1),(6672,7582,77,0.00,1),(6673,7582,79,0.00,2),(6674,7582,80,0.00,2),(6675,7582,78,0.00,1),(6676,7582,74,0.00,1),(6677,7582,84,0.75,1),(6678,7584,77,0.00,1),(6679,7584,79,0.00,2),(6680,7584,80,0.00,2),(6681,7584,78,0.00,1),(6682,7584,74,0.00,1),(6683,7584,84,0.75,1),(6684,7583,77,0.00,1),(6685,7583,79,0.00,2),(6686,7583,80,0.00,2),(6687,7583,78,0.00,1),(6688,7583,74,0.00,1),(6689,7583,84,0.75,1),(6690,7594,77,0.00,1),(6691,7594,79,0.00,2),(6692,7594,80,0.00,2),(6693,7594,78,0.00,1),(6694,7594,74,0.00,1),(6695,7594,84,0.75,1),(6696,7595,77,0.00,1),(6697,7595,79,0.00,2),(6698,7595,80,0.00,2),(6699,7595,78,0.00,1),(6700,7595,74,0.00,1),(6701,7595,84,0.75,1),(6702,7596,77,0.00,1),(6703,7596,79,0.00,2),(6704,7596,80,0.00,2),(6705,7596,78,0.00,1),(6706,7596,74,0.00,1),(6707,7596,84,0.75,1),(6708,7613,77,0.00,1),(6709,7613,79,0.00,2),(6710,7613,80,0.00,2),(6711,7613,78,0.00,1),(6712,7613,74,0.00,1),(6713,7613,84,0.75,1),(6714,7623,77,0.00,1),(6715,7623,79,0.00,2),(6716,7623,80,0.00,2),(6717,7623,78,0.00,1),(6718,7623,74,0.00,1),(6719,7623,84,0.75,1),(6720,7626,77,0.00,1),(6721,7626,79,0.00,2),(6722,7626,80,0.00,2),(6723,7626,78,0.00,1),(6724,7626,74,0.00,1),(6725,7626,84,0.75,1),(6726,7617,77,0.00,1),(6727,7617,79,0.00,2),(6728,7617,80,0.00,2),(6729,7617,78,0.00,1),(6730,7617,74,0.00,1),(6731,7617,84,0.75,1),(6732,7627,77,0.00,1),(6733,7627,79,0.00,2),(6734,7627,80,0.00,2),(6735,7627,78,0.00,1),(6736,7627,74,0.00,1),(6737,7627,84,0.75,1),(6738,7628,77,0.00,1),(6739,7628,79,0.00,2),(6740,7628,80,0.00,2),(6741,7628,78,0.00,1),(6742,7628,74,0.00,1),(6743,7628,84,0.75,1),(6744,7629,77,0.00,1),(6745,7629,79,0.00,2),(6746,7629,80,0.00,2),(6747,7629,78,0.00,1),(6748,7629,74,0.00,1),(6749,7629,84,0.75,1),(6750,7630,77,0.00,1),(6751,7630,79,0.00,2),(6752,7630,80,0.00,2),(6753,7630,78,0.00,1),(6754,7630,74,0.00,1),(6755,7630,84,0.75,1),(6756,7631,77,0.00,1),(6757,7631,79,0.00,2),(6758,7631,80,0.00,2),(6759,7631,78,0.00,1),(6760,7631,74,0.00,1),(6761,7631,84,0.75,1),(6762,7634,77,0.00,1),(6763,7634,79,0.00,2),(6764,7634,80,0.00,2),(6765,7634,78,0.00,1),(6766,7634,74,0.00,1),(6767,7634,84,0.75,1),(6768,7648,77,0.00,1),(6769,7648,79,0.00,2),(6770,7648,80,0.00,2),(6771,7648,78,0.00,1),(6772,7648,74,0.00,1),(6773,7648,84,0.75,1),(6774,7662,77,0.00,1),(6775,7662,79,0.00,2),(6776,7662,80,0.00,2),(6777,7662,78,0.00,1),(6778,7662,74,0.00,1),(6779,7662,84,0.75,1),(6780,7695,77,0.00,1),(6781,7695,79,0.00,2),(6782,7695,80,0.00,2),(6783,7695,78,0.00,1),(6784,7695,74,0.00,1),(6785,7695,84,0.75,1),(6786,7697,77,0.00,1),(6787,7697,79,-0.20,2),(6788,7697,80,0.00,2),(6789,7697,78,0.00,1),(6790,7697,74,0.00,1),(6791,7697,84,0.75,1),(6792,7700,77,0.00,1),(6793,7700,79,-0.20,2),(6794,7700,80,0.00,2),(6795,7700,78,0.00,1),(6796,7700,74,0.00,1),(6797,7700,84,0.75,1),(6798,7701,77,0.00,1),(6799,7701,79,0.00,2),(6800,7701,80,0.00,2),(6801,7701,78,0.00,1),(6802,7701,74,0.00,1),(6803,7701,84,0.75,1),(6804,7702,77,0.00,1),(6805,7702,79,0.00,2),(6806,7702,80,0.00,2),(6807,7702,78,0.00,1),(6808,7702,74,0.00,1),(6809,7702,84,0.75,1),(6810,7706,77,0.00,1),(6811,7706,79,0.00,2),(6812,7706,80,0.00,2),(6813,7706,78,0.00,1),(6814,7706,74,0.00,1),(6815,7706,84,0.75,1),(6816,7708,77,0.00,1),(6817,7708,79,-0.20,2),(6818,7708,80,0.00,2),(6819,7708,78,0.00,1),(6820,7708,74,0.00,1),(6821,7708,84,0.75,1),(6822,7711,77,0.00,1),(6823,7711,79,0.00,2),(6824,7711,80,0.00,2),(6825,7711,78,0.00,1),(6826,7711,74,0.00,1),(6827,7711,84,0.75,1),(6828,7712,77,0.00,1),(6829,7712,79,-0.40,2),(6830,7712,80,0.00,2),(6831,7712,78,0.00,1),(6832,7712,74,0.00,1),(6833,7712,84,0.75,1),(6834,7713,77,0.00,1),(6835,7713,79,0.00,2),(6836,7713,80,0.00,2),(6837,7713,78,0.00,1),(6838,7713,74,0.00,1),(6839,7713,84,0.75,1),(6840,7714,77,0.00,1),(6841,7714,79,0.00,2),(6842,7714,80,0.00,2),(6843,7714,78,0.00,1),(6844,7714,74,0.00,1),(6845,7714,84,0.75,1),(6846,7715,77,0.00,1),(6847,7715,79,0.00,2),(6848,7715,80,0.00,2),(6849,7715,78,0.00,1),(6850,7715,74,0.00,1),(6851,7715,84,0.75,1),(6852,7716,77,0.00,1),(6853,7716,79,0.00,2),(6854,7716,80,0.00,2),(6855,7716,78,0.00,1),(6856,7716,74,0.00,1),(6857,7716,84,0.75,1),(6858,7721,77,0.00,1),(6859,7721,79,-0.20,2),(6860,7721,80,0.00,2),(6861,7721,78,0.00,1),(6862,7721,74,0.00,1),(6863,7721,84,0.75,1),(6864,7723,77,0.00,1),(6865,7723,79,0.00,2),(6866,7723,80,0.00,2),(6867,7723,78,0.00,1),(6868,7723,74,0.00,1),(6869,7723,84,0.75,1),(6870,7739,77,0.00,1),(6871,7739,79,0.00,2),(6872,7739,80,0.00,2),(6873,7739,78,0.00,1),(6874,7739,74,0.00,1),(6875,7739,84,0.75,1),(6876,7740,77,0.00,1),(6877,7740,79,0.00,2),(6878,7740,80,0.00,2),(6879,7740,78,0.00,1),(6880,7740,74,0.00,1),(6881,7740,84,0.75,1),(6882,7741,77,0.00,1),(6883,7741,79,0.00,2),(6884,7741,80,0.00,2),(6885,7741,78,0.00,1),(6886,7741,74,0.00,1),(6887,7741,84,0.75,1),(6888,7749,77,0.00,1),(6889,7749,79,0.00,2),(6890,7749,80,0.00,2),(6891,7749,78,0.00,1),(6892,7749,74,0.00,1),(6893,7749,84,0.75,1),(6894,7750,77,0.00,1),(6895,7750,79,0.00,2),(6896,7750,80,0.00,2),(6897,7750,78,0.00,1),(6898,7750,74,0.00,1),(6899,7750,84,0.75,1),(6900,7751,77,0.00,1),(6901,7751,79,0.00,2),(6902,7751,80,0.00,2),(6903,7751,78,0.00,1),(6904,7751,74,0.00,1),(6905,7751,84,0.75,1),(6906,7760,77,0.00,1),(6907,7760,79,0.00,2),(6908,7760,80,0.00,2),(6909,7760,78,0.00,1),(6910,7760,74,0.00,1),(6911,7760,84,0.75,1),(6912,7762,77,0.00,1),(6913,7762,79,0.00,2),(6914,7762,80,0.00,2),(6915,7762,78,0.00,1),(6916,7762,74,0.00,1),(6917,7762,84,0.75,1),(6918,7764,77,0.00,1),(6919,7764,79,0.00,2),(6920,7764,80,0.00,2),(6921,7764,78,0.00,1),(6922,7764,74,0.00,1),(6923,7764,84,0.75,1),(6924,7768,77,0.00,1),(6925,7768,79,0.00,2),(6926,7768,80,0.00,2),(6927,7768,78,0.00,1),(6928,7768,74,0.00,1),(6929,7768,84,0.75,1),(6930,7773,77,0.00,1),(6931,7773,79,0.00,2),(6932,7773,80,0.00,2),(6933,7773,78,0.00,1),(6934,7773,74,0.00,1),(6935,7773,84,0.75,1),(6936,7772,77,0.00,1),(6937,7772,79,0.00,2),(6938,7772,80,0.00,2),(6939,7772,78,0.00,1),(6940,7772,74,0.00,1),(6941,7772,84,0.75,1),(6942,7774,77,0.00,1),(6943,7774,79,0.00,2),(6944,7774,80,0.00,2),(6945,7774,78,0.00,1),(6946,7774,74,0.00,1),(6947,7774,84,0.75,1),(6948,7777,77,0.00,1),(6949,7777,79,0.00,2),(6950,7777,80,0.00,2),(6951,7777,78,0.00,1),(6952,7777,74,0.00,1),(6953,7777,84,0.75,1),(6954,7781,77,0.00,1),(6955,7781,79,0.00,2),(6956,7781,80,0.00,2),(6957,7781,78,0.00,1),(6958,7781,74,0.00,1),(6959,7781,84,0.75,1),(6960,7784,77,0.00,1),(6961,7784,79,0.00,2),(6962,7784,80,0.00,2),(6963,7784,78,0.00,1),(6964,7784,74,0.00,1),(6965,7784,84,0.75,1),(6966,7785,77,0.00,1),(6967,7785,79,0.00,2),(6968,7785,80,0.00,2),(6969,7785,78,0.00,1),(6970,7785,74,0.00,1),(6971,7785,84,0.75,1),(6972,7786,77,0.00,1),(6973,7786,79,0.00,2),(6974,7786,80,0.00,2),(6975,7786,78,0.00,1),(6976,7786,74,0.00,1),(6977,7786,84,0.75,1),(6978,7791,77,0.00,1),(6979,7791,79,0.00,2),(6980,7791,80,0.00,2),(6981,7791,78,0.00,1),(6982,7791,74,0.00,1),(6983,7791,84,0.75,1),(6984,7787,77,0.00,1),(6985,7787,79,0.00,2),(6986,7787,80,0.00,2),(6987,7787,78,0.00,1),(6988,7787,74,0.00,1),(6989,7787,84,0.75,1),(6990,7797,77,0.00,1),(6991,7797,79,0.00,2),(6992,7797,80,0.00,2),(6993,7797,78,0.00,1),(6994,7797,74,0.00,1),(6995,7797,84,0.75,1),(6996,7802,77,0.00,1),(6997,7802,79,0.00,2),(6998,7802,80,0.00,2),(6999,7802,78,0.00,1),(7000,7802,74,0.00,1),(7001,7802,84,0.75,1),(7002,7803,77,0.00,1),(7003,7803,79,0.00,2),(7004,7803,80,-0.20,2),(7005,7803,78,0.00,1),(7006,7803,74,0.00,1),(7007,7803,84,0.75,1),(7008,7805,77,0.00,1),(7009,7805,79,0.00,2),(7010,7805,80,0.00,2),(7011,7805,78,0.00,1),(7012,7805,74,0.00,1),(7013,7805,84,0.75,1),(7014,7806,77,0.00,1),(7015,7806,79,0.00,2),(7016,7806,80,0.00,2),(7017,7806,78,0.00,1),(7018,7806,74,0.00,1),(7019,7806,84,0.75,1),(7020,7808,77,0.00,1),(7021,7808,79,0.00,2),(7022,7808,80,0.00,2),(7023,7808,78,0.00,1),(7024,7808,74,0.00,1),(7025,7808,84,0.75,1),(7026,7809,77,0.00,1),(7027,7809,79,0.00,2),(7028,7809,80,0.00,2),(7029,7809,78,0.00,1),(7030,7809,74,0.00,1),(7031,7809,84,0.75,1),(7032,7810,77,0.00,1),(7033,7810,79,0.00,2),(7034,7810,80,0.00,2),(7035,7810,78,0.00,1),(7036,7810,74,0.00,1),(7037,7810,84,0.75,1),(7038,7812,77,0.00,1),(7039,7812,79,0.00,2),(7040,7812,80,0.00,2),(7041,7812,78,0.00,1),(7042,7812,74,0.00,1),(7043,7812,84,0.75,1),(7044,7813,77,0.00,1),(7045,7813,79,0.00,2),(7046,7813,80,0.00,2),(7047,7813,78,0.00,1),(7048,7813,74,0.00,1),(7049,7813,84,0.75,1),(7050,7814,77,0.00,1),(7051,7814,79,0.00,2),(7052,7814,80,0.00,2),(7053,7814,78,0.00,1),(7054,7814,74,0.00,1),(7055,7814,84,0.75,1),(7056,7831,77,0.00,1),(7057,7831,79,0.00,2),(7058,7831,80,0.00,2),(7059,7831,78,0.00,1),(7060,7831,74,0.00,1),(7061,7831,84,0.75,1),(7062,7826,77,0.00,1),(7063,7826,79,0.00,2),(7064,7826,80,0.00,2),(7065,7826,78,0.00,1),(7066,7826,74,0.00,1),(7067,7826,84,0.75,1),(7068,7828,77,0.00,1),(7069,7828,79,0.00,2),(7070,7828,80,0.00,2),(7071,7828,78,0.00,1),(7072,7828,74,0.00,1),(7073,7828,84,0.75,1),(7074,7853,77,0.00,1),(7075,7853,79,0.00,2),(7076,7853,80,0.00,2),(7077,7853,78,0.00,1),(7078,7853,74,0.00,1),(7079,7853,84,0.75,1),(7080,7839,77,0.00,1),(7081,7839,79,0.00,2),(7082,7839,80,0.00,2),(7083,7839,78,0.00,1),(7084,7839,74,0.00,1),(7085,7839,84,0.75,1),(7086,7851,77,0.00,1),(7087,7851,79,0.00,2),(7088,7851,80,0.00,2),(7089,7851,78,0.00,1),(7090,7851,74,0.00,1),(7091,7851,84,0.75,1),(7092,7840,77,0.00,1),(7093,7840,79,0.00,2),(7094,7840,80,0.00,2),(7095,7840,78,0.00,1),(7096,7840,74,0.00,1),(7097,7840,84,0.75,1),(7098,7856,77,0.00,1),(7099,7856,79,0.00,2),(7100,7856,80,0.00,2),(7101,7856,78,0.00,1),(7102,7856,74,0.00,1),(7103,7856,84,0.75,1),(7104,7847,77,0.00,1),(7105,7847,79,0.00,2),(7106,7847,80,0.00,2),(7107,7847,78,0.00,1),(7108,7847,74,0.00,1),(7109,7847,84,0.75,1),(7110,7857,77,0.00,1),(7111,7857,79,0.00,2),(7112,7857,80,0.00,2),(7113,7857,78,0.00,1),(7114,7857,74,0.00,1),(7115,7857,84,0.75,1),(7116,7858,77,0.00,1),(7117,7858,79,0.00,2),(7118,7858,80,0.00,2),(7119,7858,78,0.00,1),(7120,7858,74,0.00,1),(7121,7858,84,0.75,1),(7122,7860,77,0.00,1),(7123,7860,79,0.00,2),(7124,7860,80,0.00,2),(7125,7860,78,0.00,1),(7126,7860,74,0.00,1),(7127,7860,84,0.75,1),(7128,7861,77,0.00,1),(7129,7861,79,0.00,2),(7130,7861,80,0.00,2),(7131,7861,78,0.00,1),(7132,7861,74,0.00,1),(7133,7861,84,0.75,1),(7134,7862,77,0.00,1),(7135,7862,79,0.00,2),(7136,7862,80,0.00,2),(7137,7862,78,0.00,1),(7138,7862,74,0.00,1),(7139,7862,84,0.75,1),(7140,7865,77,0.00,1),(7141,7865,79,0.00,2),(7142,7865,80,0.00,2),(7143,7865,78,0.00,1),(7144,7865,74,0.00,1),(7145,7865,84,0.75,1),(7146,7866,77,0.00,1),(7147,7866,79,0.00,2),(7148,7866,80,0.00,2),(7149,7866,78,0.00,1),(7150,7866,74,0.00,1),(7151,7866,84,0.75,1),(7152,7867,77,0.00,1),(7153,7867,79,0.00,2),(7154,7867,80,0.00,2),(7155,7867,78,0.00,1),(7156,7867,74,0.00,1),(7157,7867,84,0.75,1),(7158,7868,77,0.00,1),(7159,7868,79,0.00,2),(7160,7868,80,0.00,2),(7161,7868,78,0.00,1),(7162,7868,74,0.00,1),(7163,7868,84,0.75,1),(7164,7869,77,0.00,1),(7165,7869,79,0.00,2),(7166,7869,80,0.00,2),(7167,7869,78,0.00,1),(7168,7869,74,0.00,1),(7169,7869,84,0.75,1),(7170,7873,77,0.00,1),(7171,7873,79,0.00,2),(7172,7873,80,0.00,2),(7173,7873,78,0.00,1),(7174,7873,74,0.00,1),(7175,7873,84,0.75,1),(7176,7876,77,0.00,1),(7177,7876,79,0.00,2),(7178,7876,80,0.00,2),(7179,7876,78,0.00,1),(7180,7876,74,0.00,1),(7181,7876,84,0.75,1),(7182,7878,77,0.00,1),(7183,7878,79,0.00,2),(7184,7878,80,0.00,2),(7185,7878,78,0.00,1),(7186,7878,74,0.00,1),(7187,7878,84,0.75,1),(7188,7880,77,0.00,1),(7189,7880,79,0.00,2),(7190,7880,80,0.00,2),(7191,7880,78,0.00,1),(7192,7882,77,0.00,1),(7193,7882,79,0.00,2),(7194,7882,80,0.00,2),(7195,7882,78,0.00,1),(7196,7882,74,0.00,1),(7197,7882,84,0.75,1),(7198,7883,77,0.00,1),(7199,7883,79,0.00,2),(7200,7883,80,0.00,2),(7201,7883,78,0.00,1),(7202,7883,74,0.00,1),(7203,7883,84,0.75,1),(7204,7886,77,0.00,1),(7205,7886,79,0.00,2),(7206,7886,80,0.00,2),(7207,7886,78,0.00,1),(7208,7886,74,1.00,1),(7209,7886,84,0.75,1),(7210,7887,77,0.00,1),(7211,7887,79,0.00,2),(7212,7887,80,0.00,2),(7213,7887,78,0.00,1),(7214,7887,74,1.00,1),(7215,7887,84,0.75,1),(7216,7890,77,0.00,1),(7217,7890,79,0.00,2),(7218,7890,80,0.00,2),(7219,7890,78,0.00,1),(7220,7890,74,0.00,1),(7221,7890,84,0.75,1),(7222,7891,77,0.00,1),(7223,7891,79,0.00,2),(7224,7891,80,0.00,2),(7225,7891,78,0.00,1),(7226,7891,74,0.00,1),(7227,7891,84,0.75,1),(7228,7892,77,0.00,1),(7229,7892,79,0.00,2),(7230,7892,80,0.00,2),(7231,7892,78,0.00,1),(7232,7892,74,0.00,1),(7233,7892,84,0.75,1),(7234,7897,77,0.00,1),(7235,7897,79,0.00,2),(7236,7897,80,0.00,2),(7237,7897,78,0.00,1),(7238,7897,74,0.00,1),(7239,7897,84,0.75,1),(7240,7899,77,0.00,1),(7241,7899,79,0.00,2),(7242,7899,80,0.00,2),(7243,7899,78,0.00,1),(7244,7899,74,0.00,1),(7245,7899,84,0.75,1),(7246,7900,77,0.00,1),(7247,7900,79,0.00,2),(7248,7900,80,0.00,2),(7249,7900,78,0.00,1),(7250,7900,74,0.00,1),(7251,7900,84,0.75,1),(7252,7901,77,0.00,1),(7253,7901,79,0.00,2),(7254,7901,80,0.00,2),(7255,7901,78,0.00,1),(7256,7901,74,0.00,1),(7257,7901,84,0.75,1),(7258,7902,77,0.00,1),(7259,7902,79,0.00,2),(7260,7902,80,0.00,2),(7261,7902,78,0.00,1),(7262,7902,74,0.00,1),(7263,7902,84,0.75,1),(7264,7906,77,0.00,1),(7265,7906,79,0.00,2),(7266,7906,80,0.00,2),(7267,7906,78,0.00,1),(7268,7906,74,0.00,1),(7269,7906,84,0.75,1),(7270,7907,77,0.00,1),(7271,7907,79,0.00,2),(7272,7907,80,0.00,2),(7273,7907,78,0.00,1),(7274,7907,74,0.00,1),(7275,7907,84,0.75,1),(7276,7908,77,0.00,1),(7277,7908,79,0.00,2),(7278,7908,80,0.00,2),(7279,7908,78,0.00,1),(7280,7908,74,0.00,1),(7281,7908,84,0.75,1),(7282,7904,77,0.00,1),(7283,7904,79,0.00,2),(7284,7904,80,0.00,2),(7285,7904,78,0.00,1),(7286,7904,74,0.00,1),(7287,7904,84,0.75,1),(7288,7909,77,0.00,1),(7289,7909,79,0.00,2),(7290,7909,80,0.00,2),(7291,7909,78,0.00,1),(7292,7909,74,0.00,1),(7293,7909,84,0.75,1),(7294,7919,77,0.00,1),(7295,7919,79,0.00,2),(7296,7919,80,0.00,2),(7297,7919,78,0.00,1),(7298,7919,74,0.00,1),(7299,7919,84,0.75,1),(7300,7922,77,0.00,1),(7301,7922,79,0.00,2),(7302,7922,80,0.00,2),(7303,7922,78,0.00,1),(7304,7922,74,0.00,1),(7305,7922,84,0.75,1),(7306,7925,77,0.00,1),(7307,7925,79,0.00,2),(7308,7925,80,0.00,2),(7309,7925,78,0.00,1),(7310,7925,74,0.00,1),(7311,7925,84,0.75,1),(7312,7928,77,0.00,1),(7313,7928,79,0.00,2),(7314,7928,80,0.00,2),(7315,7928,78,0.00,1),(7316,7928,74,0.00,1),(7317,7928,84,0.75,1),(7318,7930,77,0.00,1),(7319,7930,79,0.00,2),(7320,7930,80,0.00,2),(7321,7930,78,0.00,1),(7322,7930,74,0.00,1),(7323,7930,84,0.75,1),(7324,7931,77,0.00,1),(7325,7931,79,0.00,2),(7326,7931,80,0.00,2),(7327,7931,78,0.00,1),(7328,7931,74,0.00,1),(7329,7931,84,0.75,1),(7330,7936,77,0.00,1),(7331,7936,79,0.00,2),(7332,7936,80,0.00,2),(7333,7936,78,0.00,1),(7334,7936,74,0.00,1),(7335,7936,84,0.75,1),(7336,7937,77,0.00,1),(7337,7937,79,0.00,2),(7338,7937,80,0.00,2),(7339,7937,78,0.00,1),(7340,7937,74,0.00,1),(7341,7937,84,0.75,1),(7342,7938,77,0.00,1),(7343,7938,79,0.00,2),(7344,7938,80,0.00,2),(7345,7938,78,0.00,1),(7346,7938,74,0.00,1),(7347,7938,84,0.75,1),(7348,7940,77,0.00,1),(7349,7940,79,0.00,2),(7350,7940,80,-0.40,2),(7351,7940,78,0.00,1),(7352,7942,77,0.00,1),(7353,7942,79,0.00,2),(7354,7942,80,-0.20,2),(7355,7942,78,0.00,1),(7356,7942,74,0.00,1),(7357,7942,84,0.75,1),(7358,7943,77,0.00,1),(7359,7943,79,0.00,2),(7360,7943,80,-0.40,2),(7361,7943,78,0.00,1),(7362,7943,74,0.00,1),(7363,7943,84,0.75,1),(7364,7945,77,0.00,1),(7365,7945,79,0.00,2),(7366,7945,80,0.00,2),(7367,7945,78,0.00,1),(7368,7945,74,0.00,1),(7369,7945,84,0.75,1),(7370,7946,77,0.00,1),(7371,7946,79,0.00,2),(7372,7946,80,0.00,2),(7373,7946,78,0.00,1),(7374,7946,74,0.00,1),(7375,7946,84,0.75,1),(7376,7951,77,0.00,1),(7377,7951,79,0.00,2),(7378,7951,80,0.00,2),(7379,7951,78,0.00,1),(7380,7951,74,0.00,1),(7381,7951,84,0.75,1),(7382,7949,77,0.00,1),(7383,7949,79,0.00,2),(7384,7949,80,0.00,2),(7385,7949,78,0.00,1),(7386,7949,74,0.00,1),(7387,7949,84,0.75,1),(7388,7953,77,0.00,1),(7389,7953,79,0.00,2),(7390,7953,80,0.00,2),(7391,7953,78,0.00,1),(7392,7953,74,0.00,1),(7393,7953,84,0.75,1),(7394,7954,77,0.00,1),(7395,7954,79,0.00,2),(7396,7954,80,0.00,2),(7397,7954,78,0.00,1),(7398,7954,74,0.00,1),(7399,7954,84,0.75,1),(7400,7960,77,0.00,1),(7401,7960,79,0.00,2),(7402,7960,80,0.00,2),(7403,7960,78,0.00,1),(7404,7960,74,0.00,1),(7405,7960,84,0.75,1),(7406,7961,77,0.00,1),(7407,7961,79,0.00,2),(7408,7961,80,0.00,2),(7409,7961,78,0.00,1),(7410,7961,74,0.00,1),(7411,7961,84,0.75,1),(7412,7962,77,0.00,1),(7413,7962,79,0.00,2),(7414,7962,80,0.00,2),(7415,7962,78,0.00,1),(7416,7962,74,0.00,1),(7417,7962,84,0.75,1),(7418,7963,77,0.00,1),(7419,7963,79,0.00,2),(7420,7963,80,0.00,2),(7421,7963,78,0.00,1),(7422,7963,74,0.00,1),(7423,7963,84,0.75,1),(7424,7964,77,0.00,1),(7425,7964,79,0.00,2),(7426,7964,80,0.00,2),(7427,7964,78,0.00,1),(7428,7964,74,0.00,1),(7429,7964,84,0.75,1),(7430,7965,77,0.00,1),(7431,7965,79,0.00,2),(7432,7965,80,0.00,2),(7433,7965,78,0.00,1),(7434,7965,74,0.00,1),(7435,7965,84,0.75,1),(7436,7966,77,0.00,1),(7437,7966,79,0.00,2),(7438,7966,80,0.00,2),(7439,7966,78,0.00,1),(7440,7966,74,0.00,1),(7441,7966,84,0.75,1),(7442,7967,77,0.00,1),(7443,7967,79,0.00,2),(7444,7967,80,0.00,2),(7445,7967,78,0.00,1),(7446,7967,74,0.00,1),(7447,7967,84,0.75,1),(7448,7969,77,0.00,1),(7449,7969,79,0.00,2),(7450,7969,80,0.00,2),(7451,7969,78,0.00,1),(7452,7969,74,0.00,1),(7453,7969,84,0.75,1),(7454,7968,77,0.00,1),(7455,7968,79,0.00,2),(7456,7968,80,0.00,2),(7457,7968,78,0.00,1),(7458,7968,74,0.00,1),(7459,7968,84,0.75,1),(7460,7971,77,0.00,1),(7461,7971,79,0.00,2),(7462,7971,80,0.00,2),(7463,7971,78,0.00,1),(7464,7971,74,0.00,1),(7465,7971,84,0.75,1),(7466,7970,77,0.00,1),(7467,7970,79,0.00,2),(7468,7970,80,0.00,2),(7469,7970,78,0.00,1),(7470,7970,74,0.00,1),(7471,7970,84,0.75,1),(7472,7980,77,0.00,1),(7473,7980,79,0.00,2),(7474,7980,80,0.00,2),(7475,7980,78,0.00,1),(7476,7980,74,0.00,1),(7477,7980,84,0.75,1),(7478,7986,77,0.00,1),(7479,7986,79,0.00,2),(7480,7986,80,0.00,2),(7481,7986,78,0.00,1),(7482,7986,74,0.00,1),(7483,7986,84,0.75,1),(7484,7987,77,0.00,1),(7485,7987,79,0.00,2),(7486,7987,80,0.00,2),(7487,7987,78,0.00,1),(7488,7987,74,0.00,1),(7489,7987,84,0.75,1),(7490,7988,77,0.00,1),(7491,7988,79,0.00,2),(7492,7988,80,0.00,2),(7493,7988,78,0.00,1),(7494,7988,74,0.00,1),(7495,7988,84,0.75,1),(7496,7989,77,0.00,1),(7497,7989,79,0.00,2),(7498,7989,80,0.00,2),(7499,7989,78,0.00,1),(7500,7989,74,0.00,1),(7501,7989,84,0.75,1),(7502,7990,77,0.00,1),(7503,7990,79,0.00,2),(7504,7990,80,0.00,2),(7505,7990,78,0.00,1),(7506,7990,74,0.00,1),(7507,7990,84,0.75,1),(7508,7991,77,0.00,1),(7509,7991,79,0.00,2),(7510,7991,80,0.00,2),(7511,7991,78,0.00,1),(7512,7991,74,0.00,1),(7513,7991,84,0.75,1),(7514,7993,77,0.00,1),(7515,7993,79,0.00,2),(7516,7993,80,0.00,2),(7517,7993,78,0.00,1),(7518,7993,74,0.00,1),(7519,7993,84,0.75,1),(7520,7994,77,0.00,1),(7521,7994,79,0.00,2),(7522,7994,80,0.00,2),(7523,7994,78,0.00,1),(7524,7994,74,0.00,1),(7525,7994,84,0.75,1),(7526,7996,77,0.00,1),(7527,7996,79,0.00,2),(7528,7996,80,0.00,2),(7529,7996,78,0.00,1),(7530,7996,74,0.00,1),(7531,7996,84,0.75,1),(7532,7997,77,0.00,1),(7533,7997,79,0.00,2),(7534,7997,80,0.00,2),(7535,7997,78,0.00,1),(7536,7997,74,0.00,1),(7537,7997,84,0.75,1),(7538,7998,77,0.00,1),(7539,7998,79,0.00,2),(7540,7998,80,0.00,2),(7541,7998,78,0.00,1),(7542,7998,74,0.00,1),(7543,7998,84,0.75,1),(7544,7999,77,0.00,1),(7545,7999,79,0.00,2),(7546,7999,80,0.00,2),(7547,7999,78,0.00,1),(7548,7999,74,0.00,1),(7549,7999,84,0.75,1),(7550,8003,77,0.00,1),(7551,8003,79,0.00,2),(7552,8003,80,0.00,2),(7553,8003,78,0.00,1),(7554,8003,74,0.00,1),(7555,8003,84,0.75,1),(7556,8004,77,0.00,1),(7557,8004,79,0.00,2),(7558,8004,80,0.00,2),(7559,8004,78,0.00,1),(7560,8004,74,0.00,1),(7561,8004,84,0.75,1),(7562,8008,77,0.00,1),(7563,8008,79,0.00,2),(7564,8008,80,0.00,2),(7565,8008,78,0.00,1),(7566,8008,74,0.00,1),(7567,8008,84,0.75,1),(7568,8010,77,0.00,1),(7569,8010,79,0.00,2),(7570,8010,80,0.00,2),(7571,8010,78,0.00,1),(7572,8010,74,0.00,1),(7573,8010,84,0.75,1),(7574,8017,77,0.00,1),(7575,8017,79,0.00,2),(7576,8017,80,0.00,2),(7577,8017,78,0.00,1),(7578,8017,74,0.00,1),(7579,8017,84,0.75,1),(7580,8020,77,0.00,1),(7581,8020,79,0.00,2),(7582,8020,80,0.00,2),(7583,8020,78,0.00,1),(7584,8020,74,0.00,1),(7585,8020,84,0.75,1),(7586,8007,77,0.00,1),(7587,8007,79,0.00,2),(7588,8007,80,0.00,2),(7589,8007,78,0.00,1),(7590,8007,74,0.00,1),(7591,8007,84,0.75,1),(7592,8019,77,0.00,1),(7593,8019,79,0.00,2),(7594,8019,80,-0.60,2),(7595,8019,78,0.00,1),(7596,8019,74,0.00,1),(7597,8019,84,0.75,1),(7598,8025,77,0.00,1),(7599,8025,79,0.00,2),(7600,8025,80,0.00,2),(7601,8025,78,0.00,1),(7602,8025,74,0.00,1),(7603,8025,84,0.75,1),(7604,8026,77,0.00,1),(7605,8026,79,0.00,2),(7606,8026,80,0.00,2),(7607,8026,78,0.00,1),(7608,8026,74,0.00,1),(7609,8026,84,0.75,1),(7610,8027,77,0.00,1),(7611,8027,79,0.00,2),(7612,8027,80,0.00,2),(7613,8027,78,0.00,1),(7614,8027,74,0.00,1),(7615,8027,84,0.75,1),(7616,8028,77,0.00,1),(7617,8028,79,0.00,2),(7618,8028,80,0.00,2),(7619,8028,78,0.00,1),(7620,8028,74,0.00,1),(7621,8028,84,0.75,1),(7622,8029,77,0.00,1),(7623,8029,79,0.00,2),(7624,8029,80,0.00,2),(7625,8029,78,0.00,1),(7626,8029,74,0.00,1),(7627,8029,84,0.75,1),(7628,8031,77,0.00,1),(7629,8031,79,0.00,2),(7630,8031,80,0.00,2),(7631,8031,78,0.00,1),(7632,8031,74,0.00,1),(7633,8031,84,0.75,1),(7634,8032,77,0.00,1),(7635,8032,79,0.00,2),(7636,8032,80,0.00,2),(7637,8032,78,0.00,1),(7638,8032,74,0.00,1),(7639,8032,84,0.75,1),(7640,8033,77,0.00,1),(7641,8033,79,0.00,2),(7642,8033,80,0.00,2),(7643,8033,78,0.00,1),(7644,8033,74,0.00,1),(7645,8033,84,0.75,1),(7646,8034,77,0.00,1),(7647,8034,79,0.00,2),(7648,8034,80,0.00,2),(7649,8034,78,0.00,1),(7650,8034,74,0.00,1),(7651,8034,84,0.75,1),(7652,8048,77,0.00,1),(7653,8048,79,0.00,2),(7654,8048,80,0.00,2),(7655,8048,78,0.00,1),(7656,8048,74,0.00,1),(7657,8048,84,0.75,1),(7658,8052,77,0.00,1),(7659,8052,79,0.00,2),(7660,8052,80,0.00,2),(7661,8052,78,0.00,1),(7662,8052,74,0.00,1),(7663,8052,84,0.75,1),(7664,8053,77,0.00,1),(7665,8053,79,0.00,2),(7666,8053,80,0.00,2),(7667,8053,78,0.00,1),(7668,8053,74,0.00,1),(7669,8053,84,0.75,1),(7670,8063,77,0.00,1),(7671,8063,79,0.00,2),(7672,8063,80,0.00,2),(7673,8063,78,0.00,1),(7674,8063,74,0.00,1),(7675,8063,84,0.75,1),(7676,8067,77,0.00,1),(7677,8067,79,0.00,2),(7678,8067,80,0.00,2),(7679,8067,78,0.00,1),(7680,8067,74,0.00,1),(7681,8067,84,0.75,1),(7682,8093,77,0.00,1),(7683,8093,79,0.00,2),(7684,8093,80,0.00,2),(7685,8093,78,0.00,1),(7686,8093,74,0.00,1),(7687,8093,84,0.75,1),(7688,8091,77,0.00,1),(7689,8091,79,0.00,2),(7690,8091,80,0.00,2),(7691,8091,78,0.00,1),(7692,8091,74,0.00,1),(7693,8091,84,0.75,1),(7694,8098,77,0.00,1),(7695,8098,79,0.00,2),(7696,8098,80,0.00,2),(7697,8098,78,0.00,1),(7698,8098,74,0.00,1),(7699,8098,84,0.75,1),(7700,8101,77,0.00,1),(7701,8101,79,0.00,2),(7702,8101,80,0.00,2),(7703,8101,78,0.00,1),(7704,8101,74,0.00,1),(7705,8101,84,0.75,1),(7706,8102,77,0.00,1),(7707,8102,79,0.00,2),(7708,8102,80,0.00,2),(7709,8102,78,0.00,1),(7710,8102,74,0.00,1),(7711,8102,84,0.75,1),(7712,8105,77,0.00,1),(7713,8105,79,0.00,2),(7714,8105,80,0.00,2),(7715,8105,78,0.00,1),(7716,8105,74,0.00,1),(7717,8105,84,0.75,1),(7718,8104,77,0.00,1),(7719,8104,79,0.00,2),(7720,8104,80,0.00,2),(7721,8104,78,0.00,1),(7722,8104,74,0.00,1),(7723,8104,84,0.75,1),(7724,8106,77,0.00,1),(7725,8106,79,0.00,2),(7726,8106,80,0.00,2),(7727,8106,78,0.00,1),(7728,8106,74,0.00,1),(7729,8106,84,0.75,1),(7731,8122,79,0.00,2),(7732,8122,80,0.00,2),(7733,8122,78,0.00,1),(7734,8122,74,0.00,1),(7735,8122,84,0.75,1),(7736,8124,77,0.00,1),(7737,8124,79,0.00,2),(7738,8124,80,0.00,2),(7739,8124,78,0.00,1),(7740,8124,74,0.00,1),(7741,8124,84,0.75,1),(7742,8123,77,0.00,1),(7743,8123,79,0.00,2),(7744,8123,80,0.00,2),(7745,8123,78,0.00,1),(7746,8123,74,0.00,1),(7747,8123,84,0.75,1),(7748,8128,77,0.00,1),(7749,8128,79,0.00,2),(7750,8128,80,0.00,2),(7751,8128,78,0.00,1),(7752,8128,74,0.00,1),(7753,8128,84,0.75,1),(7754,8131,77,0.00,1),(7755,8131,79,0.00,2),(7756,8131,80,0.00,2),(7757,8131,78,0.00,1),(7758,8131,74,0.00,1),(7759,8131,84,0.75,1),(7760,8134,77,0.00,1),(7761,8134,79,-0.20,2),(7762,8134,80,0.00,2),(7763,8134,78,0.00,1),(7764,8134,74,0.00,1),(7765,8134,84,0.75,1),(7766,8158,77,0.00,1),(7767,8158,79,0.00,2),(7768,8158,80,0.00,2),(7769,8158,78,0.00,1),(7770,8158,74,0.00,1),(7771,8158,84,0.75,1),(7772,8172,77,0.00,1),(7773,8172,79,0.00,2),(7774,8172,80,0.00,2),(7775,8172,78,0.00,1),(7776,8172,74,0.00,1),(7777,8172,84,0.75,1),(7778,8179,77,0.00,1),(7779,8179,79,0.00,2),(7780,8179,80,0.00,2),(7781,8179,78,0.00,1),(7782,8179,74,0.00,1),(7783,8179,84,0.75,1),(7784,8184,77,0.00,1),(7785,8184,79,0.00,2),(7786,8184,80,0.00,2),(7787,8184,78,0.00,1),(7788,8184,74,0.00,1),(7789,8184,84,0.75,1),(7790,8186,77,0.00,1),(7791,8186,79,0.00,2),(7792,8186,80,0.00,2),(7793,8186,78,0.00,1),(7794,8186,74,0.00,1),(7795,8186,84,0.75,1),(7796,8211,77,0.00,1),(7797,8211,79,0.00,2),(7798,8211,80,0.00,2),(7799,8211,78,0.00,1),(7800,8211,74,0.00,1),(7801,8211,84,0.75,1),(7802,8221,77,0.00,1),(7803,8221,79,0.00,2),(7804,8221,80,0.00,2),(7805,8221,78,0.00,1),(7806,8221,74,0.00,1),(7807,8221,84,0.75,1),(7808,8226,77,0.00,1),(7809,8226,79,0.00,2),(7810,8226,80,0.00,2),(7811,8226,78,0.00,1),(7812,8226,74,0.00,1),(7813,8226,84,0.75,1),(7814,8227,77,0.00,1),(7815,8227,79,0.00,2),(7816,8227,80,0.00,2),(7817,8227,78,0.00,1),(7818,8227,74,0.00,1),(7819,8227,84,0.75,1),(7820,8228,77,0.00,1),(7821,8228,79,0.00,2),(7822,8228,80,0.00,2),(7823,8228,78,0.00,1),(7824,8228,74,0.00,1),(7825,8228,84,0.75,1),(7826,8229,77,0.00,1),(7827,8229,79,0.00,2),(7828,8229,80,0.00,2),(7829,8229,78,0.00,1),(7830,8229,74,0.00,1),(7831,8229,84,0.75,1),(7832,8233,77,0.00,1),(7833,8233,79,0.00,2),(7834,8233,80,0.00,2),(7835,8233,78,0.00,1),(7836,8233,74,0.00,1),(7837,8233,84,0.75,1),(7838,8234,77,0.00,1),(7839,8234,79,0.00,2),(7840,8234,80,0.00,2),(7841,8234,78,0.00,1),(7842,8234,74,0.00,1),(7843,8234,84,0.75,1),(7844,8235,77,0.00,1),(7845,8235,79,0.00,2),(7846,8235,80,0.00,2),(7847,8235,78,0.00,1),(7848,8235,74,0.00,1),(7849,8235,84,0.75,1),(7850,8236,77,0.00,1),(7851,8236,79,0.00,2),(7852,8236,80,0.00,2),(7853,8236,78,0.00,1),(7854,8236,74,0.00,1),(7855,8236,84,0.75,1),(7856,8238,77,0.00,1),(7857,8238,79,0.00,2),(7858,8238,80,0.00,2),(7859,8238,78,0.00,1),(7860,8238,74,0.00,1),(7861,8238,84,0.75,1),(7862,8232,77,0.00,1),(7863,8232,79,0.00,2),(7864,8232,80,0.00,2),(7865,8232,78,0.00,1),(7866,8232,74,0.00,1),(7867,8232,84,0.75,1),(7868,8239,77,0.00,1),(7869,8239,79,0.00,2),(7870,8239,80,0.00,2),(7871,8239,78,0.00,1),(7872,8239,74,0.00,1),(7873,8239,84,0.75,1),(7874,8240,77,0.00,1),(7875,8240,79,0.00,2),(7876,8240,80,0.00,2),(7877,8240,78,0.00,1),(7878,8240,74,0.00,1),(7879,8240,84,0.75,1),(7880,8225,77,0.00,1),(7881,8225,79,0.00,2),(7882,8225,80,0.00,2),(7883,8225,78,0.00,1),(7884,8225,74,0.00,1),(7885,8225,84,0.75,1),(7886,8241,77,0.00,1),(7887,8241,79,0.00,2),(7888,8241,80,0.00,2),(7889,8241,78,0.00,1),(7890,8241,74,0.00,1),(7891,8241,84,0.75,1),(7892,8252,77,0.00,1),(7893,8252,79,0.00,2),(7894,8252,80,0.00,2),(7895,8252,78,0.00,1),(7896,8252,74,0.00,1),(7897,8252,84,0.75,1),(7898,8258,77,0.00,1),(7899,8258,79,0.00,2),(7900,8258,80,0.00,2),(7901,8258,78,0.00,1),(7902,8258,74,0.00,1),(7903,8258,84,0.75,1),(7904,8261,77,0.00,1),(7905,8261,79,-0.20,2),(7906,8261,80,-1.00,2),(7907,8261,78,0.00,1),(7908,8261,74,0.00,1),(7909,8261,84,0.75,1),(7910,8260,77,0.00,1),(7911,8260,79,0.00,2),(7912,8260,80,0.00,2),(7913,8260,78,0.00,1),(7914,8260,74,0.00,1),(7915,8260,84,0.75,1),(7916,8273,77,0.00,1),(7917,8273,79,0.00,2),(7918,8273,80,-0.80,2),(7919,8273,78,0.00,1),(7920,8273,74,0.00,1),(7921,8273,84,0.75,1),(7922,8289,77,0.00,1),(7923,8289,79,0.00,2),(7924,8289,80,0.00,2),(7925,8289,78,0.00,1),(7926,8289,74,0.00,1),(7927,8289,84,0.75,1),(7928,8293,77,0.00,1),(7929,8293,79,0.00,2),(7930,8293,80,-0.20,2),(7931,8293,78,0.00,1),(7932,8293,74,0.00,1),(7933,8293,84,0.75,1),(7934,8301,77,0.00,1),(7935,8301,79,0.00,2),(7936,8301,80,0.00,2),(7937,8301,78,0.00,1),(7938,8301,74,0.00,1),(7939,8301,84,0.75,1),(7940,8306,77,0.00,1),(7941,8306,79,0.00,2),(7942,8306,80,0.00,2),(7943,8306,78,0.00,1),(7944,8306,74,0.00,1),(7945,8306,84,0.75,1),(7946,8310,77,0.00,1),(7947,8310,79,0.00,2),(7948,8310,80,0.00,2),(7949,8310,78,0.00,1),(7950,8310,74,0.00,1),(7951,8310,84,0.75,1),(7952,8316,77,0.00,1),(7953,8316,79,0.00,2),(7954,8316,80,0.00,2),(7955,8316,78,0.00,1),(7956,8316,74,0.00,1),(7957,8316,84,0.75,1),(7958,8318,77,0.00,1),(7959,8318,79,0.00,2),(7960,8318,80,-1.20,2),(7961,8318,78,0.00,1),(7962,8318,74,1.00,1),(7963,8318,84,0.75,1),(7964,8324,77,0.00,1),(7965,8324,79,0.00,2),(7966,8324,80,0.00,2),(7967,8324,78,0.00,1),(7968,8324,74,1.00,1),(7969,8324,84,0.75,1),(7970,8330,77,0.00,1),(7971,8330,79,0.00,2),(7972,8330,80,-0.20,2),(7973,8330,78,0.00,1),(7974,8330,74,0.00,1),(7975,8330,84,0.75,1),(7976,8323,77,0.00,1),(7977,8323,79,0.00,2),(7978,8323,80,0.00,2),(7979,8323,78,0.00,1),(7980,8323,74,0.00,1),(7981,8323,84,0.75,1),(7982,8325,77,0.40,1),(7983,8325,79,0.00,2),(7984,8325,80,-0.40,2),(7985,8325,78,0.00,1),(7986,8325,74,0.00,1),(7987,8325,84,0.75,1),(7988,8350,77,0.00,1),(7989,8350,79,0.00,2),(7990,8350,80,-0.20,2),(7991,8350,78,0.00,1),(7992,8350,74,0.00,1),(7993,8350,84,0.75,1),(7994,8362,77,0.00,1),(7995,8362,79,0.00,2),(7996,8362,80,0.00,2),(7997,8362,78,0.00,1),(7998,8362,74,0.00,1),(7999,8362,84,0.75,1),(8000,8363,77,0.00,1),(8001,8363,79,0.00,2),(8002,8363,80,0.00,2),(8003,8363,78,0.00,1),(8004,8363,74,0.00,1),(8005,8363,84,0.75,1),(8006,8364,77,0.00,1),(8007,8364,79,0.00,2),(8008,8364,80,0.00,2),(8009,8364,78,0.00,1),(8010,8364,74,0.00,1),(8011,8364,84,0.75,1),(8012,8375,77,0.00,1),(8013,8375,79,0.00,2),(8014,8375,80,0.00,2),(8015,8375,78,0.00,1),(8016,8375,74,0.00,1),(8017,8375,84,0.75,1),(8018,8377,77,0.00,1),(8019,8377,79,0.00,2),(8020,8377,80,0.00,2),(8021,8377,78,0.00,1),(8022,8377,74,0.00,1),(8023,8377,84,0.75,1),(8024,8381,77,0.00,1),(8025,8381,79,0.00,2),(8026,8381,80,-0.40,2),(8027,8381,78,0.00,1),(8028,8381,74,0.00,1),(8029,8381,84,0.75,1),(8030,8382,77,0.00,1),(8031,8382,79,0.00,2),(8032,8382,80,0.00,2),(8033,8382,78,0.00,1),(8034,8382,74,0.00,1),(8035,8382,84,0.75,1),(8036,8385,77,0.00,1),(8037,8385,79,0.00,2),(8038,8385,80,0.00,2),(8039,8385,78,0.00,1),(8040,8385,74,0.00,1),(8041,8385,84,0.75,1),(8042,8390,77,0.00,1),(8043,8390,79,0.00,2),(8044,8390,80,0.00,2),(8045,8390,78,0.00,1),(8046,8390,74,0.00,1),(8047,8390,84,0.75,1),(8048,8391,77,0.00,1),(8049,8391,79,0.00,2),(8050,8391,80,0.00,2),(8051,8391,78,0.00,1),(8052,8391,74,0.00,1),(8053,8391,84,0.75,1),(8054,8408,77,0.00,1),(8055,8408,79,0.00,2),(8056,8408,80,0.00,2),(8057,8408,78,0.00,1),(8058,8408,74,0.00,1),(8059,8408,84,0.75,1),(8060,8425,77,0.00,1),(8061,8425,79,0.00,2),(8062,8425,80,0.00,2),(8063,8425,78,0.00,1),(8064,8425,74,0.00,1),(8065,8425,84,0.75,1),(8066,8432,77,0.00,1),(8067,8432,79,0.00,2),(8068,8432,80,0.00,2),(8069,8432,78,0.00,1),(8070,8432,74,0.00,1),(8071,8432,84,0.75,1),(8072,8434,77,0.00,1),(8073,8434,79,0.00,2),(8074,8434,80,0.00,2),(8075,8434,78,0.00,1),(8076,8434,74,0.00,1),(8077,8434,84,0.75,1),(8078,8435,77,0.00,1),(8079,8435,79,0.00,2),(8080,8435,80,0.00,2),(8081,8435,78,0.00,1),(8082,8435,74,0.00,1),(8083,8435,84,0.75,1),(8084,8436,77,0.00,1),(8085,8436,79,0.00,2),(8086,8436,80,-0.20,2),(8087,8436,78,0.00,1),(8088,8436,74,0.00,1),(8089,8436,84,0.75,1),(8090,8441,77,0.00,1),(8091,8441,79,0.00,2),(8092,8441,80,0.00,2),(8093,8441,78,0.00,1),(8094,8441,74,0.00,1),(8095,8441,84,0.75,1),(8096,8443,77,0.00,1),(8097,8443,79,0.00,2),(8098,8443,80,0.00,2),(8099,8443,78,0.00,1),(8100,8443,74,0.00,1),(8101,8443,84,0.75,1),(8102,8444,77,0.00,1),(8103,8444,79,0.00,2),(8104,8444,80,0.00,2),(8105,8444,78,0.00,1),(8106,8444,74,0.00,1),(8107,8444,84,0.75,1),(8108,8445,77,0.00,1),(8109,8445,79,0.00,2),(8110,8445,80,0.00,2),(8111,8445,78,0.00,1),(8112,8445,74,0.00,1),(8113,8445,84,0.75,1),(8114,8446,77,0.00,1),(8115,8446,79,0.00,2),(8116,8446,80,0.00,2),(8117,8446,78,0.00,1),(8118,8446,74,0.00,1),(8119,8446,84,0.75,1),(8120,8447,77,0.00,1),(8121,8447,79,0.00,2),(8122,8447,80,0.00,2),(8123,8447,78,0.00,1),(8124,8447,74,0.00,1),(8125,8447,84,0.75,1),(8126,8448,77,0.00,1),(8127,8448,79,0.00,2),(8128,8448,80,0.00,2),(8129,8448,78,0.00,1),(8130,8448,74,0.00,1),(8131,8448,84,0.75,1),(8132,8439,77,0.00,1),(8133,8439,79,0.00,2),(8134,8439,80,0.00,2),(8135,8439,78,0.00,1),(8136,8439,74,0.00,1),(8137,8439,84,0.75,1),(8138,8454,77,0.00,1),(8139,8454,79,0.00,2),(8140,8454,80,0.00,2),(8141,8454,78,0.00,1),(8142,8454,74,0.00,1),(8143,8454,84,0.75,1),(8144,8480,77,0.00,1),(8145,8480,79,0.00,2),(8146,8480,80,0.00,2),(8147,8480,78,0.00,1),(8148,8480,74,0.00,1),(8149,8480,84,0.75,1),(8150,8496,77,0.00,1),(8151,8496,79,0.00,2),(8152,8496,80,0.00,2),(8153,8496,78,0.00,1),(8154,8496,74,0.00,1),(8155,8496,84,0.75,1),(8156,8511,77,0.00,1),(8157,8511,79,0.00,2),(8158,8511,80,0.00,2),(8159,8511,78,0.00,1),(8160,8511,74,0.00,1),(8161,8511,84,0.75,1),(8162,8536,77,0.00,1),(8163,8536,79,0.00,2),(8164,8536,80,0.00,2),(8165,8536,78,0.00,1),(8166,8536,74,0.00,1),(8167,8536,84,0.75,1),(8168,8540,77,0.00,1),(8169,8540,79,0.00,2),(8170,8540,80,0.00,2),(8171,8540,78,0.00,1),(8172,8540,74,0.00,1),(8173,8540,84,0.75,1),(8174,8549,77,0.00,1),(8175,8549,79,0.00,2),(8176,8549,80,0.00,2),(8177,8549,78,0.00,1),(8178,8549,74,0.00,1),(8179,8549,84,0.75,1),(8180,8552,77,0.00,1),(8181,8552,79,0.00,2),(8182,8552,80,0.00,2),(8183,8552,78,0.00,1),(8184,8552,74,0.00,1),(8185,8552,84,0.75,1),(8186,8565,77,0.00,1),(8187,8565,79,0.00,2),(8188,8565,80,0.00,2),(8189,8565,78,0.00,1),(8190,8565,74,0.00,1),(8191,8565,84,0.75,1),(8192,8567,77,0.00,1),(8193,8567,79,0.00,2),(8194,8567,80,0.00,2),(8195,8567,78,0.00,1),(8196,8567,74,0.00,1),(8197,8567,84,0.75,1),(8198,8569,77,0.00,1),(8199,8569,79,0.00,2),(8200,8569,80,0.00,2),(8201,8569,78,0.00,1),(8202,8569,74,0.00,1),(8203,8569,84,0.75,1),(8204,8568,77,0.00,1),(8205,8568,79,0.00,2),(8206,8568,80,0.00,2),(8207,8568,78,0.00,1),(8208,8568,74,0.00,1),(8209,8568,84,0.75,1),(8210,8570,77,0.00,1),(8211,8570,79,0.00,2),(8212,8570,80,0.00,2),(8213,8570,78,0.00,1),(8214,8570,74,0.00,1),(8215,8570,84,0.75,1),(8216,8571,77,0.00,1),(8217,8571,79,0.00,2),(8218,8571,80,0.00,2),(8219,8571,78,0.00,1),(8220,8571,74,0.00,1),(8221,8571,84,0.75,1),(8222,8572,77,0.00,1),(8223,8572,79,0.00,2),(8224,8572,80,0.00,2),(8225,8572,78,0.00,1),(8226,8572,74,0.00,1),(8227,8572,84,0.75,1),(8228,8573,77,0.00,1),(8229,8573,79,0.00,2),(8230,8573,80,0.00,2),(8231,8573,78,0.00,1),(8232,8573,74,0.00,1),(8233,8573,84,0.75,1),(8234,8576,77,0.00,1),(8235,8576,79,0.00,2),(8236,8576,80,0.00,2),(8237,8576,78,0.00,1),(8238,8576,74,0.00,1),(8239,8576,84,0.75,1),(8240,8584,77,0.00,1),(8241,8584,79,0.00,2),(8242,8584,80,0.00,2),(8243,8584,78,0.00,1),(8244,8584,74,0.00,1),(8245,8584,84,0.75,1),(8246,8583,77,0.00,1),(8247,8583,79,0.00,2),(8248,8583,80,0.00,2),(8249,8583,78,0.00,1),(8250,8583,74,0.00,1),(8251,8583,84,0.75,1),(8252,8585,77,0.00,1),(8253,8585,79,0.00,2),(8254,8585,80,0.00,2),(8255,8585,78,0.00,1),(8256,8585,74,0.00,1),(8257,8585,84,0.75,1),(8258,8594,77,0.00,1),(8259,8594,79,0.00,2),(8260,8594,80,0.00,2),(8261,8594,78,0.00,1),(8262,8594,74,0.00,1),(8263,8594,84,0.75,1),(8264,8596,77,0.00,1),(8265,8596,79,0.00,2),(8266,8596,80,0.00,2),(8267,8596,78,0.00,1),(8268,8596,74,0.00,1),(8269,8596,84,0.75,1),(8270,8597,77,0.00,1),(8271,8597,79,0.00,2),(8272,8597,80,0.00,2),(8273,8597,78,0.00,1),(8274,8597,74,0.00,1),(8275,8597,84,0.75,1),(8276,8598,77,0.00,1),(8277,8598,79,0.00,2),(8278,8598,80,0.00,2),(8279,8598,78,0.00,1),(8280,8598,74,0.00,1),(8281,8598,84,0.75,1),(8282,8592,77,0.00,1),(8283,8592,79,0.00,2),(8284,8592,80,0.00,2),(8285,8592,78,0.00,1),(8286,8592,74,0.00,1),(8287,8592,84,0.75,1),(8288,8593,77,0.00,1),(8289,8593,79,0.00,2),(8290,8593,80,0.00,2),(8291,8593,78,0.00,1),(8292,8593,74,0.00,1),(8293,8593,84,0.75,1),(8294,8599,77,0.00,1),(8295,8599,79,0.00,2),(8296,8599,80,0.00,2),(8297,8599,78,0.00,1),(8298,8599,74,0.00,1),(8299,8599,84,0.75,1),(8300,8602,77,0.00,1),(8301,8602,79,0.00,2),(8302,8602,80,0.00,2),(8303,8602,78,0.00,1),(8304,8602,74,0.00,1),(8305,8602,84,0.75,1),(8306,8604,77,0.00,1),(8307,8604,79,0.00,2),(8308,8604,80,0.00,2),(8309,8604,78,0.00,1),(8310,8604,74,0.00,1),(8311,8604,84,0.75,1),(8312,8618,77,0.00,1),(8313,8618,79,0.00,2),(8314,8618,80,0.00,2),(8315,8618,78,0.00,1),(8316,8618,74,0.00,1),(8317,8618,84,0.75,1),(8318,8621,77,0.00,1),(8319,8621,79,0.00,2),(8320,8621,80,0.00,2),(8321,8621,78,0.00,1),(8322,8621,74,0.00,1),(8323,8621,84,0.75,1),(8324,8627,77,0.00,1),(8325,8627,79,0.00,2),(8326,8627,80,0.00,2),(8327,8627,78,0.00,1),(8328,8627,74,0.00,1),(8329,8627,84,0.75,1),(8330,8628,77,0.00,1),(8331,8628,79,0.00,2),(8332,8628,80,0.00,2),(8333,8628,78,0.00,1),(8334,8628,74,0.00,1),(8335,8628,84,0.75,1),(8336,8630,77,0.00,1),(8337,8630,79,0.00,2),(8338,8630,80,0.00,2),(8339,8630,78,0.00,1),(8340,8630,74,0.00,1),(8341,8630,84,0.75,1),(8342,8631,77,0.00,1),(8343,8631,79,0.00,2),(8344,8631,80,0.00,2),(8345,8631,78,0.00,1),(8346,8631,74,0.00,1),(8347,8631,84,0.75,1),(8348,8632,77,0.00,1),(8349,8632,79,0.00,2),(8350,8632,80,0.00,2),(8351,8632,78,0.00,1),(8352,8632,74,0.00,1),(8353,8632,84,0.75,1),(8354,8633,77,0.00,1),(8355,8633,79,0.00,2),(8356,8633,80,0.00,2),(8357,8633,78,0.00,1),(8358,8633,74,0.00,1),(8359,8633,84,0.75,1),(8360,8635,77,0.00,1),(8361,8635,79,0.00,2),(8362,8635,80,0.00,2),(8363,8635,78,0.00,1),(8364,8635,74,0.00,1),(8365,8635,84,0.75,1),(8366,8636,77,0.00,1),(8367,8636,79,0.00,2),(8368,8636,80,0.00,2),(8369,8636,78,0.00,1),(8370,8636,74,0.00,1),(8371,8636,84,0.75,1),(8372,8637,77,0.00,1),(8373,8637,79,0.00,2),(8374,8637,80,0.00,2),(8375,8637,78,0.00,1),(8376,8637,74,0.00,1),(8377,8637,84,0.75,1),(8378,8638,77,0.00,1),(8379,8638,79,0.00,2),(8380,8638,80,0.00,2),(8381,8638,78,0.00,1),(8382,8638,74,0.00,1),(8383,8638,84,0.75,1),(8384,8639,77,0.00,1),(8385,8639,79,0.00,2),(8386,8639,80,0.00,2),(8387,8639,78,0.00,1),(8388,8639,74,0.00,1),(8389,8639,84,0.75,1),(8390,8640,77,0.00,1),(8391,8640,79,0.00,2),(8392,8640,80,0.00,2),(8393,8640,78,0.00,1),(8394,8640,74,0.00,1),(8395,8640,84,0.75,1),(8396,8641,77,0.00,1),(8397,8641,79,0.00,2),(8398,8641,80,0.00,2),(8399,8641,78,0.00,1),(8400,8641,74,0.00,1),(8401,8641,84,0.75,1),(8402,8642,77,0.00,1),(8403,8642,79,0.00,2),(8404,8642,80,0.00,2),(8405,8642,78,0.00,1),(8406,8642,74,0.00,1),(8407,8642,84,0.75,1),(8408,8643,77,0.00,1),(8409,8643,79,0.00,2),(8410,8643,80,0.00,2),(8411,8643,78,0.00,1),(8412,8643,74,0.00,1),(8413,8643,84,0.75,1),(8414,8644,77,0.00,1),(8415,8644,79,0.00,2),(8416,8644,80,0.00,2),(8417,8644,78,0.00,1),(8418,8644,74,0.00,1),(8419,8644,84,0.75,1),(8420,8645,77,0.00,1),(8421,8645,79,0.00,2),(8422,8645,80,0.00,2),(8423,8645,78,0.00,1),(8424,8645,74,0.00,1),(8425,8645,84,0.75,1),(8426,8646,77,0.00,1),(8427,8646,79,0.00,2),(8428,8646,80,0.00,2),(8429,8646,78,0.00,1),(8430,8646,74,0.00,1),(8431,8646,84,0.75,1),(8432,8647,77,0.00,1),(8433,8647,79,0.00,2),(8434,8647,80,0.00,2),(8435,8647,78,0.00,1),(8436,8647,74,0.00,1),(8437,8647,84,0.75,1),(8438,8648,77,0.00,1),(8439,8648,79,0.00,2),(8440,8648,80,0.00,2),(8441,8648,78,0.00,1),(8442,8648,74,0.00,1),(8443,8648,84,0.75,1),(8444,8650,77,0.00,1),(8445,8650,79,0.00,2),(8446,8650,80,0.00,2),(8447,8650,78,0.00,1),(8448,8650,74,0.00,1),(8449,8650,84,0.75,1),(8450,8651,77,0.00,1),(8451,8651,79,0.00,2),(8452,8651,80,0.00,2),(8453,8651,78,0.00,1),(8454,8651,74,0.00,1),(8455,8651,84,0.75,1),(8456,8652,77,0.00,1),(8457,8652,79,0.00,2),(8458,8652,80,0.00,2),(8459,8652,78,0.00,1),(8460,8652,74,0.00,1),(8461,8652,84,0.75,1),(8462,8653,77,0.00,1),(8463,8653,79,0.00,2),(8464,8653,80,0.00,2),(8465,8653,78,0.00,1),(8466,8653,74,0.00,1),(8467,8653,84,0.75,1),(8468,8654,77,0.00,1),(8469,8654,79,0.00,2),(8470,8654,80,0.00,2),(8471,8654,78,0.00,1),(8472,8654,74,0.00,1),(8473,8654,84,0.75,1),(8474,8656,77,0.00,1),(8475,8656,79,0.00,2),(8476,8656,80,0.00,2),(8477,8656,78,0.00,1),(8478,8656,74,0.00,1),(8479,8656,84,0.75,1),(8480,8672,77,0.00,1),(8481,8672,79,0.00,2),(8482,8672,80,0.00,2),(8483,8672,78,0.00,1),(8484,8672,74,0.00,1),(8485,8672,84,0.75,1),(8486,8678,77,0.00,1),(8487,8678,79,0.00,2),(8488,8678,80,0.00,2),(8489,8678,78,0.00,1),(8490,8678,74,0.00,1),(8491,8678,84,0.75,1),(8492,8683,77,0.00,1),(8493,8683,79,0.00,2),(8494,8683,80,0.00,2),(8495,8683,78,0.00,1),(8496,8683,74,0.00,1),(8497,8683,84,0.75,1),(8498,8688,77,0.00,1),(8499,8688,79,0.00,2),(8500,8688,80,0.00,2),(8501,8688,78,0.00,1),(8502,8688,74,0.00,1),(8503,8688,84,0.75,1),(8504,8692,77,0.00,1),(8505,8692,79,0.00,2),(8506,8692,80,0.00,2),(8507,8692,78,0.00,1),(8508,8692,74,0.00,1),(8509,8692,84,0.75,1),(8510,8693,77,0.00,1),(8511,8693,79,0.00,2),(8512,8693,80,0.00,2),(8513,8693,78,0.00,1),(8514,8693,74,0.00,1),(8515,8693,84,0.75,1),(8516,8695,77,0.00,1),(8517,8695,79,0.00,2),(8518,8695,80,0.00,2),(8519,8695,78,0.00,1),(8520,8695,74,0.00,1),(8521,8695,84,0.75,1),(8522,8698,77,0.00,1),(8523,8698,79,0.00,2),(8524,8698,80,0.00,2),(8525,8698,78,0.00,1),(8526,8698,74,0.00,1),(8527,8698,84,0.75,1),(8528,8696,77,0.00,1),(8529,8696,79,0.00,2),(8530,8696,80,0.00,2),(8531,8696,78,0.00,1),(8532,8696,74,0.00,1),(8533,8696,84,0.75,1),(8534,8699,77,0.00,1),(8535,8699,79,0.00,2),(8536,8699,80,0.00,2),(8537,8699,78,0.00,1),(8538,8699,74,0.00,1),(8539,8699,84,0.75,1),(8540,8703,77,0.00,1),(8541,8703,79,0.00,2),(8542,8703,80,0.00,2),(8543,8703,78,0.00,1),(8544,8703,74,0.00,1),(8545,8703,84,0.75,1),(8546,8706,77,0.00,1),(8547,8706,79,0.00,2),(8548,8706,80,0.00,2),(8549,8706,78,0.00,1),(8550,8706,74,0.00,1),(8551,8706,84,0.75,1),(8552,8710,77,0.00,1),(8553,8710,79,0.00,2),(8554,8710,80,-0.20,2),(8555,8710,78,0.00,1),(8556,8710,74,0.00,1),(8557,8710,84,0.75,1),(8558,8714,77,0.00,1),(8559,8714,79,0.00,2),(8560,8714,80,0.00,2),(8561,8714,78,0.00,1),(8562,8714,74,0.00,1),(8563,8714,84,0.75,1),(8564,8715,77,0.00,1),(8565,8715,79,0.00,2),(8566,8715,80,-1.80,2),(8567,8715,78,0.00,1),(8568,8715,74,0.00,1),(8569,8715,84,0.75,1),(8570,8720,77,0.00,1),(8571,8720,79,0.00,2),(8572,8720,80,0.00,2),(8573,8720,78,0.00,1),(8574,8720,74,0.00,1),(8575,8720,84,0.75,1),(8576,8722,77,0.00,1),(8577,8722,79,0.00,2),(8578,8722,80,0.00,2),(8579,8722,78,0.00,1),(8580,8722,74,1.00,1),(8581,8722,84,0.75,1),(8582,8723,77,0.00,1),(8583,8723,79,0.00,2),(8584,8723,80,0.00,2),(8585,8723,78,0.00,1),(8586,8723,74,0.00,1),(8587,8723,84,0.75,1),(8588,8732,77,0.00,1),(8589,8732,79,0.00,2),(8590,8732,80,0.00,2),(8591,8732,78,0.00,1),(8592,8732,74,0.00,1),(8593,8732,84,0.75,1),(8594,8735,77,0.00,1),(8595,8735,79,0.00,2),(8596,8735,80,0.00,2),(8597,8735,78,0.00,1),(8598,8735,74,0.00,1),(8599,8735,84,0.75,1),(8600,8755,77,0.00,1),(8601,8755,79,0.00,2),(8602,8755,80,0.00,2),(8603,8755,78,0.00,1),(8604,8755,74,0.00,1),(8605,8755,84,0.75,1),(8606,8759,77,0.00,1),(8607,8759,79,0.00,2),(8608,8759,80,0.00,2),(8609,8759,78,0.00,1),(8610,8776,77,0.00,1),(8611,8776,79,0.00,2),(8612,8776,80,0.00,2),(8613,8776,78,0.00,1),(8614,8776,74,0.00,1),(8615,8776,84,0.75,1),(8616,8794,77,0.00,1),(8617,8794,79,0.00,2),(8618,8794,80,0.00,2),(8619,8794,78,0.00,1),(8620,8794,74,0.00,1),(8621,8794,84,0.75,1),(8622,8796,77,0.00,1),(8623,8796,79,0.00,2),(8624,8796,80,0.00,2),(8625,8796,78,0.00,1),(8626,8796,74,0.00,1),(8627,8796,84,0.75,1);
/*!40000 ALTER TABLE `simulations_mail_points` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `start_time` time DEFAULT NULL,
  `duration` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `sim_id` int(11) DEFAULT NULL,
  `code` varchar(5) DEFAULT NULL,
  `start_type` varchar(5) DEFAULT NULL,
  `category` tinyint(1) DEFAULT NULL,
  `import_id` varchar(14) NOT NULL COMMENT 'setvice value,used to remove old data after reimport.',
  PRIMARY KEY (`id`),
  KEY `fk_tasks_sim_id` (`sim_id`),
  CONSTRAINT `fk_tasks_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasks`
--

LOCK TABLES `tasks` WRITE;
/*!40000 ALTER TABLE `tasks` DISABLE KEYS */;
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_migration`
--

DROP TABLE IF EXISTS `tbl_migration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_migration` (
  `version` varchar(255) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_migration`
--

LOCK TABLES `tbl_migration` WRITE;
/*!40000 ALTER TABLE `tbl_migration` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_migration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `todo`
--

DROP TABLE IF EXISTS `todo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `todo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `adding_date` datetime DEFAULT NULL COMMENT 'Дата добавления задачи',
  PRIMARY KEY (`id`),
  KEY `fk_todo_sim_id` (`sim_id`),
  KEY `fk_todo_task_id` (`task_id`),
  CONSTRAINT `fk_todo_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_todo_task_id` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `todo`
--

LOCK TABLES `todo` WRITE;
/*!40000 ALTER TABLE `todo` DISABLE KEYS */;
/*!40000 ALTER TABLE `todo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `type_scale`
--

DROP TABLE IF EXISTS `type_scale`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `type_scale` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `type_scale`
--

LOCK TABLES `type_scale` WRITE;
/*!40000 ALTER TABLE `type_scale` DISABLE KEYS */;
INSERT INTO `type_scale` VALUES (1,'positive'),(2,'negative'),(3,'personal');
/*!40000 ALTER TABLE `type_scale` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universal_log`
--

DROP TABLE IF EXISTS `universal_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `universal_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) DEFAULT NULL,
  `window_id` int(11) DEFAULT NULL,
  `mail_id` int(11) DEFAULT NULL,
  `file_id` int(11) DEFAULT NULL,
  `dialog_id` int(11) DEFAULT NULL,
  `last_dialog_id` int(11) DEFAULT NULL,
  `activity_action_id` int(11) DEFAULT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL DEFAULT '00:00:00',
  PRIMARY KEY (`id`),
  KEY `universal_log_activity_action_id` (`activity_action_id`),
  KEY `universal_log_dialog_id` (`dialog_id`),
  KEY `universal_log_dialog_last_id` (`last_dialog_id`),
  KEY `universal_log_file_id` (`file_id`),
  KEY `universal_log_mail_id` (`mail_id`),
  KEY `universal_log_window_id` (`window_id`),
  KEY `universal_log_sim_id` (`sim_id`),
  CONSTRAINT `universal_log_activity_action_id` FOREIGN KEY (`activity_action_id`) REFERENCES `activity_action` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `universal_log_dialog_id` FOREIGN KEY (`dialog_id`) REFERENCES `dialogs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `universal_log_dialog_last_id` FOREIGN KEY (`last_dialog_id`) REFERENCES `dialogs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `universal_log_file_id` FOREIGN KEY (`file_id`) REFERENCES `my_documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `universal_log_mail_id` FOREIGN KEY (`mail_id`) REFERENCES `mail_box` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `universal_log_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `universal_log_window_id` FOREIGN KEY (`window_id`) REFERENCES `window` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universal_log`
--

LOCK TABLES `universal_log` WRITE;
/*!40000 ALTER TABLE `universal_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `universal_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_groups`
--

DROP TABLE IF EXISTS `user_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT 'пользователь',
  `gid` int(11) NOT NULL COMMENT 'группа',
  PRIMARY KEY (`id`),
  KEY `fk_user_groups_uid` (`uid`),
  KEY `fk_user_groups_gid` (`gid`),
  CONSTRAINT `fk_user_groups_gid` FOREIGN KEY (`gid`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_user_groups_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Группы пользователей';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_groups`
--

LOCK TABLES `user_groups` WRITE;
/*!40000 ALTER TABLE `user_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `password` varchar(32) NOT NULL,
  `email` varchar(50) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_activation_code`
--

DROP TABLE IF EXISTS `users_activation_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_activation_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `code` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_users_activation_code_uid` (`uid`),
  CONSTRAINT `fk_users_activation_code_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_activation_code`
--

LOCK TABLES `users_activation_code` WRITE;
/*!40000 ALTER TABLE `users_activation_code` DISABLE KEYS */;
/*!40000 ALTER TABLE `users_activation_code` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `window`
--

DROP TABLE IF EXISTS `window`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `window` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `subtype` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `type_subtype_unique` (`type`,`subtype`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `window`
--

LOCK TABLES `window` WRITE;
/*!40000 ALTER TABLE `window` DISABLE KEYS */;
INSERT INTO `window` VALUES (42,'documents','documents files'),(41,'documents','documents main'),(11,'mail','mail main'),(13,'mail','mail new'),(14,'mail','mail plan'),(12,'mail','mail preview'),(1,'main screen','main screen'),(24,'phone','phone call'),(21,'phone','phone main'),(23,'phone','phone talk'),(3,'plan','plan'),(31,'visitor','visitor entrance'),(32,'visitor','visitor talk');
/*!40000 ALTER TABLE `window` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-02-26 12:47:10
