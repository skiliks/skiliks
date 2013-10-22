/*
Navicat MySQL Data Transfer

Source Server         : Local
Source Server Version : 50533
Source Host           : localhost:3306
Source Database       : skiliks_dev3

Target Server Type    : MYSQL
Target Server Version : 50533
File Encoding         : 65001

Date: 2013-10-17 15:14:58
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for action
-- ----------------------------
DROP TABLE IF EXISTS `action`;
CREATE TABLE `action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `comment` text,
  `subject` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of action
-- ----------------------------

-- ----------------------------
-- Table structure for activity
-- ----------------------------
DROP TABLE IF EXISTS `activity`;
CREATE TABLE `activity` (
  `code` varchar(60) NOT NULL,
  `parent` varchar(10) NOT NULL,
  `grandparent` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category_id` varchar(10) DEFAULT NULL,
  `import_id` varchar(255) NOT NULL,
  `numeric_id` int(11) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL COMMENT 'Task ot Activity. Task is important thihg, activiti - trash.',
  `scenario_id` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `activity_uniq` (`code`,`scenario_id`),
  KEY `fk_activity_category_id` (`category_id`),
  KEY `activity_scenario` (`scenario_id`),
  CONSTRAINT `activity_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_activity_category_id` FOREIGN KEY (`category_id`) REFERENCES `activity_category` (`code`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of activity
-- ----------------------------

-- ----------------------------
-- Table structure for activity_action
-- ----------------------------
DROP TABLE IF EXISTS `activity_action`;
CREATE TABLE `activity_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dialog_id` int(11) DEFAULT NULL,
  `mail_id` int(11) DEFAULT NULL,
  `document_id` int(11) DEFAULT NULL,
  `import_id` varchar(255) NOT NULL,
  `window_id` int(11) DEFAULT NULL,
  `is_keep_last_category` tinyint(1) DEFAULT '0',
  `leg_type` varchar(40) DEFAULT NULL,
  `scenario_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `meeting_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_activity_action_dialog_id` (`dialog_id`),
  KEY `fk_activity_action_mail_id` (`mail_id`),
  KEY `fk_activity_action_document_id` (`document_id`),
  KEY `activity_action_leg_type` (`leg_type`),
  KEY `activity_action_scenario` (`scenario_id`),
  KEY `activity_action_activity` (`activity_id`),
  KEY `activity_action_document_unique` (`document_id`,`activity_id`,`scenario_id`),
  KEY `activity_action_mail_unique` (`mail_id`,`activity_id`,`scenario_id`),
  KEY `fk_activity_action_meeting_id` (`meeting_id`),
  CONSTRAINT `fk_activity_action_meeting_id` FOREIGN KEY (`meeting_id`) REFERENCES `meeting` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `activity_action_activity` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`),
  CONSTRAINT `activity_action_leg_type` FOREIGN KEY (`leg_type`) REFERENCES `activity_type` (`type`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `activity_action_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of activity_action
-- ----------------------------

-- ----------------------------
-- Table structure for activity_category
-- ----------------------------
DROP TABLE IF EXISTS `activity_category`;
CREATE TABLE `activity_category` (
  `code` varchar(10) NOT NULL,
  `priority` int(11) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of activity_category
-- ----------------------------
INSERT INTO `activity_category` VALUES ('0', '1');
INSERT INTO `activity_category` VALUES ('1', '2');
INSERT INTO `activity_category` VALUES ('2', '4');
INSERT INTO `activity_category` VALUES ('2_min', '3');
INSERT INTO `activity_category` VALUES ('3', '5');
INSERT INTO `activity_category` VALUES ('4', '6');
INSERT INTO `activity_category` VALUES ('5', '7');

-- ----------------------------
-- Table structure for activity_parent
-- ----------------------------
DROP TABLE IF EXISTS `activity_parent`;
CREATE TABLE `activity_parent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `import_id` varchar(14) DEFAULT NULL,
  `parent_code` varchar(10) NOT NULL,
  `dialog_id` int(11) DEFAULT NULL,
  `mail_id` int(11) DEFAULT NULL,
  `scenario_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_parent_scenario` (`scenario_id`),
  CONSTRAINT `activity_parent_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of activity_parent
-- ----------------------------

-- ----------------------------
-- Table structure for activity_parent_availability
-- ----------------------------
DROP TABLE IF EXISTS `activity_parent_availability`;
CREATE TABLE `activity_parent_availability` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL,
  `category` varchar(10) NOT NULL,
  `available_at` time NOT NULL,
  `scenario_id` int(11) DEFAULT NULL,
  `import_id` varchar(14) DEFAULT NULL,
  `is_keep_last_category` tinyint(1) DEFAULT '0',
  `must_present_for_214d` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `activity_parent_availability_fk_scenario` (`scenario_id`),
  CONSTRAINT `activity_parent_availability_fk_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of activity_parent_availability
-- ----------------------------

-- ----------------------------
-- Table structure for activity_type
-- ----------------------------
DROP TABLE IF EXISTS `activity_type`;
CREATE TABLE `activity_type` (
  `type` varchar(40) NOT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of activity_type
-- ----------------------------
INSERT INTO `activity_type` VALUES ('Documents_leg');
INSERT INTO `activity_type` VALUES ('Inbox_leg');
INSERT INTO `activity_type` VALUES ('Manual_dial_leg');
INSERT INTO `activity_type` VALUES ('Meeting');
INSERT INTO `activity_type` VALUES ('Outbox_leg');
INSERT INTO `activity_type` VALUES ('System_dial_leg');
INSERT INTO `activity_type` VALUES ('Window');

-- ----------------------------
-- Table structure for assessment_aggregated
-- ----------------------------
DROP TABLE IF EXISTS `assessment_aggregated`;
CREATE TABLE `assessment_aggregated` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `point_id` int(11) NOT NULL,
  `value` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assessment_aggregated_I_id` (`id`),
  KEY `assessment_aggregated_I_point_id` (`point_id`),
  KEY `assessment_aggregated_I_sim_id` (`sim_id`),
  CONSTRAINT `assessment_aggregated_FK_character_point_title` FOREIGN KEY (`point_id`) REFERENCES `hero_behaviour` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `assessment_aggregated_FK_simulations` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of assessment_aggregated
-- ----------------------------

-- ----------------------------
-- Table structure for assessment_calculation
-- ----------------------------
DROP TABLE IF EXISTS `assessment_calculation`;
CREATE TABLE `assessment_calculation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL COMMENT 'идентификатор симуляции',
  `point_id` int(11) NOT NULL COMMENT 'поинт',
  `value` float(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_simulations_mail_points_sim_id` (`sim_id`),
  KEY `fk_simulations_mail_point_id` (`point_id`),
  CONSTRAINT `fk_simulations_mail_points_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_simulations_mail_point_id` FOREIGN KEY (`point_id`) REFERENCES `hero_behaviour` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8628 DEFAULT CHARSET=utf8 COMMENT='Баллы, набранные в почтовике';

-- ----------------------------
-- Records of assessment_calculation
-- ----------------------------
INSERT INTO `assessment_calculation` VALUES ('5703', '7219', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5704', '7219', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5705', '7219', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5706', '7219', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5707', '7219', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5708', '7219', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5709', '7220', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5710', '7220', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5711', '7220', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5712', '7220', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5713', '7220', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5714', '7220', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5715', '7225', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5716', '7225', '79', '-0.20');
INSERT INTO `assessment_calculation` VALUES ('5717', '7225', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5718', '7225', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5719', '7225', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5720', '7225', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5721', '7226', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5722', '7226', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5723', '7226', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5724', '7226', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5725', '7226', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5726', '7226', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5727', '7229', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5728', '7229', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5729', '7229', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5730', '7229', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5731', '7229', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5732', '7229', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5733', '7232', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5734', '7232', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5735', '7232', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5736', '7232', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5737', '7232', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5738', '7232', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5739', '7233', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5740', '7233', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5741', '7233', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5742', '7233', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5743', '7233', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5744', '7233', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5745', '7234', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5746', '7234', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5747', '7234', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5748', '7234', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5749', '7234', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5750', '7234', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5751', '7235', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5752', '7235', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5753', '7235', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5754', '7235', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5755', '7235', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5756', '7235', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5757', '7248', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5758', '7248', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5759', '7248', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5760', '7248', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5761', '7248', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5762', '7248', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5763', '7250', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5764', '7250', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5765', '7250', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5766', '7250', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5767', '7250', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5768', '7250', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5769', '7253', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5770', '7253', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5771', '7253', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5772', '7253', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5773', '7253', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5774', '7253', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5775', '7254', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5776', '7254', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5777', '7254', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5778', '7254', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5779', '7254', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5780', '7254', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5781', '7260', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5782', '7260', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5783', '7260', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5784', '7260', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5785', '7260', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5786', '7260', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5787', '7262', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5788', '7262', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5789', '7262', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5790', '7262', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5791', '7262', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5792', '7262', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5793', '7265', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5794', '7265', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5795', '7265', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5796', '7265', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5797', '7265', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5798', '7265', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5799', '7272', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5800', '7272', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5801', '7272', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5802', '7272', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5803', '7272', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5804', '7272', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5805', '7274', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5806', '7274', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5807', '7274', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5808', '7274', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5809', '7274', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5810', '7274', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5811', '7275', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5812', '7275', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5813', '7275', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5814', '7275', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5815', '7275', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5816', '7275', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5817', '7279', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5818', '7279', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5819', '7279', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5820', '7279', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5821', '7279', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5822', '7279', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5823', '7280', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5824', '7280', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5825', '7280', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5826', '7280', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5827', '7280', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5828', '7280', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5829', '7282', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5830', '7282', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5831', '7282', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5832', '7282', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5833', '7282', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5834', '7282', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5835', '7284', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5836', '7284', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5837', '7284', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5838', '7284', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5839', '7284', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5840', '7284', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5841', '7285', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5842', '7285', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5843', '7285', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5844', '7285', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5845', '7285', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5846', '7285', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5847', '7286', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5848', '7286', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5849', '7286', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5850', '7286', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5851', '7286', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5852', '7286', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5853', '7288', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5854', '7288', '79', '-0.20');
INSERT INTO `assessment_calculation` VALUES ('5855', '7288', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5856', '7288', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5857', '7288', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5858', '7288', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5859', '7289', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5860', '7289', '79', '-1.00');
INSERT INTO `assessment_calculation` VALUES ('5861', '7289', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5862', '7289', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5863', '7289', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5864', '7289', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5865', '7290', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5866', '7290', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5867', '7290', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5868', '7290', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5869', '7290', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5870', '7290', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5871', '7292', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5872', '7292', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5873', '7292', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5874', '7292', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5875', '7292', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5876', '7292', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5877', '7310', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5878', '7310', '79', '-0.20');
INSERT INTO `assessment_calculation` VALUES ('5879', '7310', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5880', '7310', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5881', '7310', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5882', '7310', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5883', '7311', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5884', '7311', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5885', '7311', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5886', '7311', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5887', '7311', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5888', '7311', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5889', '7314', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5890', '7314', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5891', '7314', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5892', '7314', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5893', '7314', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5894', '7314', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5895', '7318', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5896', '7318', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5897', '7318', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5898', '7318', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5899', '7318', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5900', '7318', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5901', '7328', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5902', '7328', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5903', '7328', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5904', '7328', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5905', '7328', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5906', '7328', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5907', '7332', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5908', '7332', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5909', '7332', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5910', '7332', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5911', '7332', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5912', '7332', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5913', '7336', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5914', '7336', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5915', '7336', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5916', '7336', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5917', '7336', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5918', '7336', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5919', '7337', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5920', '7337', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5921', '7337', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5922', '7337', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5923', '7337', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5924', '7337', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5925', '7338', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5926', '7338', '79', '-0.40');
INSERT INTO `assessment_calculation` VALUES ('5927', '7338', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5928', '7338', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5929', '7338', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5930', '7338', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5931', '7339', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5932', '7339', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5933', '7339', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5934', '7339', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5935', '7339', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5936', '7339', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5937', '7341', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5938', '7341', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5939', '7341', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5940', '7341', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5941', '7341', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5942', '7341', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5943', '7342', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5944', '7342', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5945', '7342', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5946', '7342', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5947', '7342', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5948', '7342', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5949', '7343', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5950', '7343', '79', '-0.20');
INSERT INTO `assessment_calculation` VALUES ('5951', '7343', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5952', '7343', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5953', '7343', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5954', '7343', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5955', '7344', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5956', '7344', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5957', '7344', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5958', '7344', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5959', '7344', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5960', '7344', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5961', '7345', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5962', '7345', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5963', '7345', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5964', '7345', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5965', '7345', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5966', '7345', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5967', '7346', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5968', '7346', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5969', '7346', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5970', '7346', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5971', '7346', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5972', '7346', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5973', '7347', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5974', '7347', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5975', '7347', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5976', '7347', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5977', '7347', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5978', '7347', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5979', '7348', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5980', '7348', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5981', '7348', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5982', '7348', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5983', '7348', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5984', '7348', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5985', '7349', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5986', '7349', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5987', '7349', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5988', '7349', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5989', '7349', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5990', '7349', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5991', '7350', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5992', '7350', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5993', '7350', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5994', '7350', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5995', '7350', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5996', '7350', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('5997', '7351', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5998', '7351', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('5999', '7351', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6000', '7351', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6001', '7351', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6002', '7351', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6003', '7352', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6004', '7352', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6005', '7352', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6006', '7352', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6007', '7352', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6008', '7352', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6009', '7354', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6010', '7354', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6011', '7354', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6012', '7354', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6013', '7354', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6014', '7354', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6015', '7355', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6016', '7355', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6017', '7355', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6018', '7355', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6019', '7355', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6020', '7355', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6021', '7356', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6022', '7356', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6023', '7356', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6024', '7356', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6025', '7356', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6026', '7356', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6027', '7357', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6028', '7357', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6029', '7357', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6030', '7357', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6031', '7357', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6032', '7357', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6033', '7362', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6034', '7362', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6035', '7362', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6036', '7362', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6037', '7362', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6038', '7362', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6039', '7363', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6040', '7363', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6041', '7363', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6042', '7363', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6043', '7363', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6044', '7363', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6045', '7366', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6046', '7366', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6047', '7366', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6048', '7366', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6049', '7366', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6050', '7366', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6051', '7367', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6052', '7367', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6053', '7367', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6054', '7367', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6055', '7367', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6056', '7367', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6057', '7368', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6058', '7368', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6059', '7368', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6060', '7368', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6061', '7368', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6062', '7368', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6063', '7369', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6064', '7369', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6065', '7369', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6066', '7369', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6067', '7369', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6068', '7369', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6069', '7370', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6070', '7370', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6071', '7370', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6072', '7370', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6073', '7370', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6074', '7370', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6075', '7374', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6076', '7374', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6077', '7374', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6078', '7374', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6079', '7374', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6080', '7374', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6081', '7378', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6082', '7378', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6083', '7378', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6084', '7378', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6085', '7378', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6086', '7378', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6087', '7392', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6088', '7392', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6089', '7392', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6090', '7392', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6091', '7392', '74', '1.00');
INSERT INTO `assessment_calculation` VALUES ('6092', '7392', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6093', '7395', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6094', '7395', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6095', '7395', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6096', '7395', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6097', '7395', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6098', '7395', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6099', '7399', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6100', '7399', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6101', '7399', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6102', '7399', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6103', '7399', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6104', '7399', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6105', '7401', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6106', '7401', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6107', '7401', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6108', '7401', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6109', '7401', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6110', '7401', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6111', '7402', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6112', '7402', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6113', '7402', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6114', '7402', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6115', '7402', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6116', '7402', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6117', '7406', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6118', '7406', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6119', '7406', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6120', '7406', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6121', '7406', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6122', '7406', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6123', '7408', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6124', '7408', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6125', '7408', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6126', '7408', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6127', '7408', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6128', '7408', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6129', '7411', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6130', '7411', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6131', '7411', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6132', '7411', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6133', '7411', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6134', '7411', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6135', '7413', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6136', '7413', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6137', '7413', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6138', '7413', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6139', '7413', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6140', '7413', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6141', '7412', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6142', '7412', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6143', '7412', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6144', '7412', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6145', '7412', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6146', '7412', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6147', '7414', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6148', '7414', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6149', '7414', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6150', '7414', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6151', '7414', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6152', '7414', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6153', '7409', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6154', '7409', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6155', '7409', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6156', '7409', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6157', '7409', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6158', '7409', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6159', '7416', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6160', '7416', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6161', '7416', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6162', '7416', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6163', '7416', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6164', '7416', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6165', '7429', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6166', '7429', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6167', '7429', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6168', '7429', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6169', '7429', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6170', '7429', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6171', '7431', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6172', '7431', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6173', '7431', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6174', '7431', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6175', '7431', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6176', '7431', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6177', '7436', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6178', '7436', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6179', '7436', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6180', '7436', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6181', '7436', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6182', '7436', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6183', '7435', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6184', '7435', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6185', '7435', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6186', '7435', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6187', '7435', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6188', '7435', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6189', '7437', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6190', '7437', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6191', '7437', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6192', '7437', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6193', '7437', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6194', '7437', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6195', '7438', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6196', '7438', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6197', '7438', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6198', '7438', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6199', '7438', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6200', '7438', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6201', '7433', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6202', '7433', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6203', '7433', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6204', '7433', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6205', '7433', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6206', '7433', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6207', '7441', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6208', '7441', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6209', '7441', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6210', '7441', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6211', '7441', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6212', '7441', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6213', '7443', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6214', '7443', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6215', '7443', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6216', '7443', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6217', '7443', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6218', '7443', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6219', '7448', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6220', '7448', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6221', '7448', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6222', '7448', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6223', '7448', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6224', '7448', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6225', '7447', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6226', '7447', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6227', '7447', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6228', '7447', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6229', '7447', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6230', '7447', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6231', '7450', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6232', '7450', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6233', '7450', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6234', '7450', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6235', '7450', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6236', '7450', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6237', '7451', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6238', '7451', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6239', '7451', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6240', '7451', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6241', '7451', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6242', '7451', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6243', '7454', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6244', '7454', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6245', '7454', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6246', '7454', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6247', '7454', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6248', '7454', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6249', '7456', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6250', '7456', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6251', '7456', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6252', '7456', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6253', '7456', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6254', '7456', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6255', '7460', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6256', '7460', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6257', '7460', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6258', '7460', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6259', '7460', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6260', '7460', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6261', '7461', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6262', '7461', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6263', '7461', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6264', '7461', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6265', '7461', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6266', '7461', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6267', '7465', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6268', '7465', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6269', '7465', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6270', '7465', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6271', '7465', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6272', '7465', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6273', '7458', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6274', '7458', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6275', '7458', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6276', '7458', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6277', '7458', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6278', '7458', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6279', '7466', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6280', '7466', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6281', '7466', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6282', '7466', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6283', '7466', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6284', '7466', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6285', '7467', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6286', '7467', '79', '-0.20');
INSERT INTO `assessment_calculation` VALUES ('6287', '7467', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6288', '7467', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6289', '7467', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6290', '7467', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6291', '7468', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6292', '7468', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6293', '7468', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6294', '7468', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6295', '7468', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6296', '7468', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6297', '7469', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6298', '7469', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6299', '7469', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6300', '7473', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6301', '7473', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6302', '7473', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6303', '7473', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6304', '7473', '74', '1.00');
INSERT INTO `assessment_calculation` VALUES ('6305', '7473', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6306', '7474', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6307', '7474', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6308', '7474', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6309', '7474', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6310', '7474', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6311', '7474', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6312', '7476', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6313', '7476', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6314', '7476', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6315', '7476', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6316', '7476', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6317', '7476', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6318', '7478', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6319', '7478', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6320', '7478', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6321', '7478', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6322', '7478', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6323', '7478', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6324', '7477', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6325', '7477', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6326', '7477', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6327', '7477', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6328', '7477', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6329', '7477', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6330', '7483', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6331', '7483', '79', '-0.20');
INSERT INTO `assessment_calculation` VALUES ('6332', '7483', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6333', '7483', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6334', '7483', '74', '1.00');
INSERT INTO `assessment_calculation` VALUES ('6335', '7483', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6336', '7485', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6337', '7485', '79', '-0.20');
INSERT INTO `assessment_calculation` VALUES ('6338', '7485', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6339', '7485', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6340', '7485', '74', '1.00');
INSERT INTO `assessment_calculation` VALUES ('6341', '7485', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6342', '7486', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6343', '7486', '79', '-0.20');
INSERT INTO `assessment_calculation` VALUES ('6344', '7486', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6345', '7486', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6346', '7486', '74', '1.00');
INSERT INTO `assessment_calculation` VALUES ('6347', '7486', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6348', '7489', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6349', '7489', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6350', '7489', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6351', '7489', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6352', '7489', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6353', '7489', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6354', '7494', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6355', '7494', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6356', '7494', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6357', '7494', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6358', '7494', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6359', '7494', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6360', '7495', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6361', '7495', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6362', '7495', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6363', '7495', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6364', '7495', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6365', '7495', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6366', '7498', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6367', '7498', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6368', '7498', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6369', '7498', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6370', '7498', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6371', '7498', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6372', '7499', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6373', '7499', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6374', '7499', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6375', '7499', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6376', '7499', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6377', '7499', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6378', '7500', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6379', '7500', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6380', '7500', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6381', '7500', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6382', '7500', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6383', '7500', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6384', '7505', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6385', '7505', '79', '-0.20');
INSERT INTO `assessment_calculation` VALUES ('6386', '7505', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6387', '7505', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6388', '7505', '74', '1.00');
INSERT INTO `assessment_calculation` VALUES ('6389', '7505', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6390', '7506', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6391', '7506', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6392', '7506', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6393', '7506', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6394', '7506', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6395', '7506', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6396', '7507', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6397', '7507', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6398', '7507', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6399', '7507', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6400', '7507', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6401', '7507', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6402', '7508', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6403', '7508', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6404', '7508', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6405', '7508', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6406', '7508', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6407', '7508', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6408', '7512', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6409', '7512', '79', '-0.20');
INSERT INTO `assessment_calculation` VALUES ('6410', '7512', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6411', '7512', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6412', '7512', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6413', '7512', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6414', '7516', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6415', '7516', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6416', '7516', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6417', '7516', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6418', '7516', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6419', '7516', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6420', '7518', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6421', '7518', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6422', '7518', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6423', '7518', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6424', '7518', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6425', '7518', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6426', '7519', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6427', '7519', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6428', '7519', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6429', '7519', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6430', '7519', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6431', '7519', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6432', '7520', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6433', '7520', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6434', '7520', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6435', '7520', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6436', '7520', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6437', '7520', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6438', '7521', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6439', '7521', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6440', '7521', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6441', '7521', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6442', '7521', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6443', '7521', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6444', '7522', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6445', '7522', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6446', '7522', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6447', '7522', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6448', '7522', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6449', '7522', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6450', '7523', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6451', '7523', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6452', '7523', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6453', '7523', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6454', '7523', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6455', '7523', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6456', '7525', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6457', '7525', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6458', '7525', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6459', '7525', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6460', '7525', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6461', '7525', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6462', '7526', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6463', '7526', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6464', '7526', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6465', '7526', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6466', '7526', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6467', '7526', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6468', '7527', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6469', '7527', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6470', '7527', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6471', '7527', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6472', '7527', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6473', '7527', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6474', '7528', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6475', '7528', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6476', '7528', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6477', '7528', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6478', '7528', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6479', '7528', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6480', '7529', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6481', '7529', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6482', '7529', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6483', '7529', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6484', '7529', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6485', '7529', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6486', '7531', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6487', '7531', '79', '-0.20');
INSERT INTO `assessment_calculation` VALUES ('6488', '7531', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6489', '7531', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6490', '7531', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6491', '7531', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6492', '7532', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6493', '7532', '79', '-0.20');
INSERT INTO `assessment_calculation` VALUES ('6494', '7532', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6495', '7532', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6496', '7532', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6497', '7532', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6498', '7533', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6499', '7533', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6500', '7533', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6501', '7533', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6502', '7533', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6503', '7533', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6504', '7534', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6505', '7534', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6506', '7534', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6507', '7534', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6508', '7534', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6509', '7534', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6510', '7535', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6511', '7535', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6512', '7535', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6513', '7535', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6514', '7535', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6515', '7535', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6516', '7536', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6517', '7536', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6518', '7536', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6519', '7536', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6520', '7536', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6521', '7536', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6522', '7537', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6523', '7537', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6524', '7537', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6525', '7537', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6526', '7537', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6527', '7537', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6528', '7539', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6529', '7539', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6530', '7539', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6531', '7539', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6532', '7539', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6533', '7539', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6534', '7540', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6535', '7540', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6536', '7540', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6537', '7540', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6538', '7540', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6539', '7540', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6540', '7541', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6541', '7541', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6542', '7541', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6543', '7541', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6544', '7541', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6545', '7541', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6546', '7538', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6547', '7538', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6548', '7538', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6549', '7538', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6550', '7538', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6551', '7538', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6552', '7542', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6553', '7542', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6554', '7542', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6555', '7542', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6556', '7542', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6557', '7542', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6558', '7544', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6559', '7544', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6560', '7544', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6561', '7544', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6562', '7544', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6563', '7544', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6564', '7543', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6565', '7543', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6566', '7543', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6567', '7543', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6568', '7543', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6569', '7543', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6570', '7545', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6571', '7545', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6572', '7545', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6573', '7545', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6574', '7545', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6575', '7545', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6576', '7546', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6577', '7546', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6578', '7546', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6579', '7546', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6580', '7546', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6581', '7546', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6582', '7547', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6583', '7547', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6584', '7547', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6585', '7547', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6586', '7547', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6587', '7547', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6588', '7549', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6589', '7549', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6590', '7549', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6591', '7549', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6592', '7549', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6593', '7549', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6594', '7551', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6595', '7551', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6596', '7551', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6597', '7551', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6598', '7551', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6599', '7551', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6600', '7552', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6601', '7552', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6602', '7552', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6603', '7552', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6604', '7552', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6605', '7552', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6606', '7553', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6607', '7553', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6608', '7553', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6609', '7553', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6610', '7553', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6611', '7553', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6612', '7554', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6613', '7554', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6614', '7554', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6615', '7554', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6616', '7554', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6617', '7554', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6618', '7555', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6619', '7555', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6620', '7555', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6621', '7555', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6622', '7555', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6623', '7555', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6624', '7567', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6625', '7567', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6626', '7567', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6627', '7567', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6628', '7567', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6629', '7567', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6630', '7569', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6631', '7569', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6632', '7569', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6633', '7569', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6634', '7569', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6635', '7569', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6636', '7570', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6637', '7570', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6638', '7570', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6639', '7570', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6640', '7570', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6641', '7570', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6642', '7571', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6643', '7571', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6644', '7571', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6645', '7571', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6646', '7571', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6647', '7571', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6648', '7572', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6649', '7572', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6650', '7572', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6651', '7572', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6652', '7572', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6653', '7572', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6654', '7578', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6655', '7578', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6656', '7578', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6657', '7578', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6658', '7578', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6659', '7578', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6660', '7580', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6661', '7580', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6662', '7580', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6663', '7580', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6664', '7580', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6665', '7580', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6666', '7581', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6667', '7581', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6668', '7581', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6669', '7581', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6670', '7581', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6671', '7581', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6672', '7582', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6673', '7582', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6674', '7582', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6675', '7582', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6676', '7582', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6677', '7582', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6678', '7584', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6679', '7584', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6680', '7584', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6681', '7584', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6682', '7584', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6683', '7584', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6684', '7583', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6685', '7583', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6686', '7583', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6687', '7583', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6688', '7583', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6689', '7583', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6690', '7594', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6691', '7594', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6692', '7594', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6693', '7594', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6694', '7594', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6695', '7594', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6696', '7595', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6697', '7595', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6698', '7595', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6699', '7595', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6700', '7595', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6701', '7595', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6702', '7596', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6703', '7596', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6704', '7596', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6705', '7596', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6706', '7596', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6707', '7596', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6708', '7613', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6709', '7613', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6710', '7613', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6711', '7613', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6712', '7613', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6713', '7613', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6714', '7623', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6715', '7623', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6716', '7623', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6717', '7623', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6718', '7623', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6719', '7623', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6720', '7626', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6721', '7626', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6722', '7626', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6723', '7626', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6724', '7626', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6725', '7626', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6726', '7617', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6727', '7617', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6728', '7617', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6729', '7617', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6730', '7617', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6731', '7617', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6732', '7627', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6733', '7627', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6734', '7627', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6735', '7627', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6736', '7627', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6737', '7627', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6738', '7628', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6739', '7628', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6740', '7628', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6741', '7628', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6742', '7628', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6743', '7628', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6744', '7629', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6745', '7629', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6746', '7629', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6747', '7629', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6748', '7629', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6749', '7629', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6750', '7630', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6751', '7630', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6752', '7630', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6753', '7630', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6754', '7630', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6755', '7630', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6756', '7631', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6757', '7631', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6758', '7631', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6759', '7631', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6760', '7631', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6761', '7631', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6762', '7634', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6763', '7634', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6764', '7634', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6765', '7634', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6766', '7634', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6767', '7634', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6768', '7648', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6769', '7648', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6770', '7648', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6771', '7648', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6772', '7648', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6773', '7648', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6774', '7662', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6775', '7662', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6776', '7662', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6777', '7662', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6778', '7662', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6779', '7662', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6780', '7695', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6781', '7695', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6782', '7695', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6783', '7695', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6784', '7695', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6785', '7695', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6786', '7697', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6787', '7697', '79', '-0.20');
INSERT INTO `assessment_calculation` VALUES ('6788', '7697', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6789', '7697', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6790', '7697', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6791', '7697', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6792', '7700', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6793', '7700', '79', '-0.20');
INSERT INTO `assessment_calculation` VALUES ('6794', '7700', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6795', '7700', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6796', '7700', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6797', '7700', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6798', '7701', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6799', '7701', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6800', '7701', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6801', '7701', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6802', '7701', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6803', '7701', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6804', '7702', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6805', '7702', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6806', '7702', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6807', '7702', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6808', '7702', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6809', '7702', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6810', '7706', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6811', '7706', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6812', '7706', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6813', '7706', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6814', '7706', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6815', '7706', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6816', '7708', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6817', '7708', '79', '-0.20');
INSERT INTO `assessment_calculation` VALUES ('6818', '7708', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6819', '7708', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6820', '7708', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6821', '7708', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6822', '7711', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6823', '7711', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6824', '7711', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6825', '7711', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6826', '7711', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6827', '7711', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6828', '7712', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6829', '7712', '79', '-0.40');
INSERT INTO `assessment_calculation` VALUES ('6830', '7712', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6831', '7712', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6832', '7712', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6833', '7712', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6834', '7713', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6835', '7713', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6836', '7713', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6837', '7713', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6838', '7713', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6839', '7713', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6840', '7714', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6841', '7714', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6842', '7714', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6843', '7714', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6844', '7714', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6845', '7714', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6846', '7715', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6847', '7715', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6848', '7715', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6849', '7715', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6850', '7715', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6851', '7715', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6852', '7716', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6853', '7716', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6854', '7716', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6855', '7716', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6856', '7716', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6857', '7716', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6858', '7721', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6859', '7721', '79', '-0.20');
INSERT INTO `assessment_calculation` VALUES ('6860', '7721', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6861', '7721', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6862', '7721', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6863', '7721', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6864', '7723', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6865', '7723', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6866', '7723', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6867', '7723', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6868', '7723', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6869', '7723', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6870', '7739', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6871', '7739', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6872', '7739', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6873', '7739', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6874', '7739', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6875', '7739', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6876', '7740', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6877', '7740', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6878', '7740', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6879', '7740', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6880', '7740', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6881', '7740', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6882', '7741', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6883', '7741', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6884', '7741', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6885', '7741', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6886', '7741', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6887', '7741', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6888', '7749', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6889', '7749', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6890', '7749', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6891', '7749', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6892', '7749', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6893', '7749', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6894', '7750', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6895', '7750', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6896', '7750', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6897', '7750', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6898', '7750', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6899', '7750', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6900', '7751', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6901', '7751', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6902', '7751', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6903', '7751', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6904', '7751', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6905', '7751', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6906', '7760', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6907', '7760', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6908', '7760', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6909', '7760', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6910', '7760', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6911', '7760', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6912', '7762', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6913', '7762', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6914', '7762', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6915', '7762', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6916', '7762', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6917', '7762', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6918', '7764', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6919', '7764', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6920', '7764', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6921', '7764', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6922', '7764', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6923', '7764', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6924', '7768', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6925', '7768', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6926', '7768', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6927', '7768', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6928', '7768', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6929', '7768', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6930', '7773', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6931', '7773', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6932', '7773', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6933', '7773', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6934', '7773', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6935', '7773', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6936', '7772', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6937', '7772', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6938', '7772', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6939', '7772', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6940', '7772', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6941', '7772', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6942', '7774', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6943', '7774', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6944', '7774', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6945', '7774', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6946', '7774', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6947', '7774', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6948', '7777', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6949', '7777', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6950', '7777', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6951', '7777', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6952', '7777', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6953', '7777', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6954', '7781', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6955', '7781', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6956', '7781', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6957', '7781', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6958', '7781', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6959', '7781', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6960', '7784', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6961', '7784', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6962', '7784', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6963', '7784', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6964', '7784', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6965', '7784', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6966', '7785', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6967', '7785', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6968', '7785', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6969', '7785', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6970', '7785', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6971', '7785', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6972', '7786', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6973', '7786', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6974', '7786', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6975', '7786', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6976', '7786', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6977', '7786', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6978', '7791', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6979', '7791', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6980', '7791', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6981', '7791', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6982', '7791', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6983', '7791', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6984', '7787', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6985', '7787', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6986', '7787', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6987', '7787', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6988', '7787', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6989', '7787', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6990', '7797', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6991', '7797', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6992', '7797', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6993', '7797', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6994', '7797', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6995', '7797', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('6996', '7802', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6997', '7802', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6998', '7802', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('6999', '7802', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7000', '7802', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7001', '7802', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7002', '7803', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7003', '7803', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7004', '7803', '80', '-0.20');
INSERT INTO `assessment_calculation` VALUES ('7005', '7803', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7006', '7803', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7007', '7803', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7008', '7805', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7009', '7805', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7010', '7805', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7011', '7805', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7012', '7805', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7013', '7805', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7014', '7806', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7015', '7806', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7016', '7806', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7017', '7806', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7018', '7806', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7019', '7806', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7020', '7808', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7021', '7808', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7022', '7808', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7023', '7808', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7024', '7808', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7025', '7808', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7026', '7809', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7027', '7809', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7028', '7809', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7029', '7809', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7030', '7809', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7031', '7809', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7032', '7810', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7033', '7810', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7034', '7810', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7035', '7810', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7036', '7810', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7037', '7810', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7038', '7812', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7039', '7812', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7040', '7812', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7041', '7812', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7042', '7812', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7043', '7812', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7044', '7813', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7045', '7813', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7046', '7813', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7047', '7813', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7048', '7813', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7049', '7813', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7050', '7814', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7051', '7814', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7052', '7814', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7053', '7814', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7054', '7814', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7055', '7814', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7056', '7831', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7057', '7831', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7058', '7831', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7059', '7831', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7060', '7831', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7061', '7831', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7062', '7826', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7063', '7826', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7064', '7826', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7065', '7826', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7066', '7826', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7067', '7826', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7068', '7828', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7069', '7828', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7070', '7828', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7071', '7828', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7072', '7828', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7073', '7828', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7074', '7853', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7075', '7853', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7076', '7853', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7077', '7853', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7078', '7853', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7079', '7853', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7080', '7839', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7081', '7839', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7082', '7839', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7083', '7839', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7084', '7839', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7085', '7839', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7086', '7851', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7087', '7851', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7088', '7851', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7089', '7851', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7090', '7851', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7091', '7851', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7092', '7840', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7093', '7840', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7094', '7840', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7095', '7840', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7096', '7840', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7097', '7840', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7098', '7856', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7099', '7856', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7100', '7856', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7101', '7856', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7102', '7856', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7103', '7856', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7104', '7847', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7105', '7847', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7106', '7847', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7107', '7847', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7108', '7847', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7109', '7847', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7110', '7857', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7111', '7857', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7112', '7857', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7113', '7857', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7114', '7857', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7115', '7857', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7116', '7858', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7117', '7858', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7118', '7858', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7119', '7858', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7120', '7858', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7121', '7858', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7122', '7860', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7123', '7860', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7124', '7860', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7125', '7860', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7126', '7860', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7127', '7860', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7128', '7861', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7129', '7861', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7130', '7861', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7131', '7861', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7132', '7861', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7133', '7861', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7134', '7862', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7135', '7862', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7136', '7862', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7137', '7862', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7138', '7862', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7139', '7862', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7140', '7865', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7141', '7865', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7142', '7865', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7143', '7865', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7144', '7865', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7145', '7865', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7146', '7866', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7147', '7866', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7148', '7866', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7149', '7866', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7150', '7866', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7151', '7866', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7152', '7867', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7153', '7867', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7154', '7867', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7155', '7867', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7156', '7867', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7157', '7867', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7158', '7868', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7159', '7868', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7160', '7868', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7161', '7868', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7162', '7868', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7163', '7868', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7164', '7869', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7165', '7869', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7166', '7869', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7167', '7869', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7168', '7869', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7169', '7869', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7170', '7873', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7171', '7873', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7172', '7873', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7173', '7873', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7174', '7873', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7175', '7873', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7176', '7876', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7177', '7876', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7178', '7876', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7179', '7876', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7180', '7876', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7181', '7876', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7182', '7878', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7183', '7878', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7184', '7878', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7185', '7878', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7186', '7878', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7187', '7878', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7188', '7880', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7189', '7880', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7190', '7880', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7191', '7880', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7192', '7882', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7193', '7882', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7194', '7882', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7195', '7882', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7196', '7882', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7197', '7882', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7198', '7883', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7199', '7883', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7200', '7883', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7201', '7883', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7202', '7883', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7203', '7883', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7204', '7886', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7205', '7886', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7206', '7886', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7207', '7886', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7208', '7886', '74', '1.00');
INSERT INTO `assessment_calculation` VALUES ('7209', '7886', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7210', '7887', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7211', '7887', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7212', '7887', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7213', '7887', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7214', '7887', '74', '1.00');
INSERT INTO `assessment_calculation` VALUES ('7215', '7887', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7216', '7890', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7217', '7890', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7218', '7890', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7219', '7890', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7220', '7890', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7221', '7890', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7222', '7891', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7223', '7891', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7224', '7891', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7225', '7891', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7226', '7891', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7227', '7891', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7228', '7892', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7229', '7892', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7230', '7892', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7231', '7892', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7232', '7892', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7233', '7892', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7234', '7897', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7235', '7897', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7236', '7897', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7237', '7897', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7238', '7897', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7239', '7897', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7240', '7899', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7241', '7899', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7242', '7899', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7243', '7899', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7244', '7899', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7245', '7899', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7246', '7900', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7247', '7900', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7248', '7900', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7249', '7900', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7250', '7900', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7251', '7900', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7252', '7901', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7253', '7901', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7254', '7901', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7255', '7901', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7256', '7901', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7257', '7901', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7258', '7902', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7259', '7902', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7260', '7902', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7261', '7902', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7262', '7902', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7263', '7902', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7264', '7906', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7265', '7906', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7266', '7906', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7267', '7906', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7268', '7906', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7269', '7906', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7270', '7907', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7271', '7907', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7272', '7907', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7273', '7907', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7274', '7907', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7275', '7907', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7276', '7908', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7277', '7908', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7278', '7908', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7279', '7908', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7280', '7908', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7281', '7908', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7282', '7904', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7283', '7904', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7284', '7904', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7285', '7904', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7286', '7904', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7287', '7904', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7288', '7909', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7289', '7909', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7290', '7909', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7291', '7909', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7292', '7909', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7293', '7909', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7294', '7919', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7295', '7919', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7296', '7919', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7297', '7919', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7298', '7919', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7299', '7919', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7300', '7922', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7301', '7922', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7302', '7922', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7303', '7922', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7304', '7922', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7305', '7922', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7306', '7925', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7307', '7925', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7308', '7925', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7309', '7925', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7310', '7925', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7311', '7925', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7312', '7928', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7313', '7928', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7314', '7928', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7315', '7928', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7316', '7928', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7317', '7928', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7318', '7930', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7319', '7930', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7320', '7930', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7321', '7930', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7322', '7930', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7323', '7930', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7324', '7931', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7325', '7931', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7326', '7931', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7327', '7931', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7328', '7931', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7329', '7931', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7330', '7936', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7331', '7936', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7332', '7936', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7333', '7936', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7334', '7936', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7335', '7936', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7336', '7937', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7337', '7937', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7338', '7937', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7339', '7937', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7340', '7937', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7341', '7937', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7342', '7938', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7343', '7938', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7344', '7938', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7345', '7938', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7346', '7938', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7347', '7938', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7348', '7940', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7349', '7940', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7350', '7940', '80', '-0.40');
INSERT INTO `assessment_calculation` VALUES ('7351', '7940', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7352', '7942', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7353', '7942', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7354', '7942', '80', '-0.20');
INSERT INTO `assessment_calculation` VALUES ('7355', '7942', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7356', '7942', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7357', '7942', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7358', '7943', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7359', '7943', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7360', '7943', '80', '-0.40');
INSERT INTO `assessment_calculation` VALUES ('7361', '7943', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7362', '7943', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7363', '7943', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7364', '7945', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7365', '7945', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7366', '7945', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7367', '7945', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7368', '7945', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7369', '7945', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7370', '7946', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7371', '7946', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7372', '7946', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7373', '7946', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7374', '7946', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7375', '7946', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7376', '7951', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7377', '7951', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7378', '7951', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7379', '7951', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7380', '7951', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7381', '7951', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7382', '7949', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7383', '7949', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7384', '7949', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7385', '7949', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7386', '7949', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7387', '7949', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7388', '7953', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7389', '7953', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7390', '7953', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7391', '7953', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7392', '7953', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7393', '7953', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7394', '7954', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7395', '7954', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7396', '7954', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7397', '7954', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7398', '7954', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7399', '7954', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7400', '7960', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7401', '7960', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7402', '7960', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7403', '7960', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7404', '7960', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7405', '7960', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7406', '7961', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7407', '7961', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7408', '7961', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7409', '7961', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7410', '7961', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7411', '7961', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7412', '7962', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7413', '7962', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7414', '7962', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7415', '7962', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7416', '7962', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7417', '7962', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7418', '7963', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7419', '7963', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7420', '7963', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7421', '7963', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7422', '7963', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7423', '7963', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7424', '7964', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7425', '7964', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7426', '7964', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7427', '7964', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7428', '7964', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7429', '7964', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7430', '7965', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7431', '7965', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7432', '7965', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7433', '7965', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7434', '7965', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7435', '7965', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7436', '7966', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7437', '7966', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7438', '7966', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7439', '7966', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7440', '7966', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7441', '7966', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7442', '7967', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7443', '7967', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7444', '7967', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7445', '7967', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7446', '7967', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7447', '7967', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7448', '7969', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7449', '7969', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7450', '7969', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7451', '7969', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7452', '7969', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7453', '7969', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7454', '7968', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7455', '7968', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7456', '7968', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7457', '7968', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7458', '7968', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7459', '7968', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7460', '7971', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7461', '7971', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7462', '7971', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7463', '7971', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7464', '7971', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7465', '7971', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7466', '7970', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7467', '7970', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7468', '7970', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7469', '7970', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7470', '7970', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7471', '7970', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7472', '7980', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7473', '7980', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7474', '7980', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7475', '7980', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7476', '7980', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7477', '7980', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7478', '7986', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7479', '7986', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7480', '7986', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7481', '7986', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7482', '7986', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7483', '7986', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7484', '7987', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7485', '7987', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7486', '7987', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7487', '7987', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7488', '7987', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7489', '7987', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7490', '7988', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7491', '7988', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7492', '7988', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7493', '7988', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7494', '7988', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7495', '7988', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7496', '7989', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7497', '7989', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7498', '7989', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7499', '7989', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7500', '7989', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7501', '7989', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7502', '7990', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7503', '7990', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7504', '7990', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7505', '7990', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7506', '7990', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7507', '7990', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7508', '7991', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7509', '7991', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7510', '7991', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7511', '7991', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7512', '7991', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7513', '7991', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7514', '7993', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7515', '7993', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7516', '7993', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7517', '7993', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7518', '7993', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7519', '7993', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7520', '7994', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7521', '7994', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7522', '7994', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7523', '7994', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7524', '7994', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7525', '7994', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7526', '7996', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7527', '7996', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7528', '7996', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7529', '7996', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7530', '7996', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7531', '7996', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7532', '7997', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7533', '7997', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7534', '7997', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7535', '7997', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7536', '7997', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7537', '7997', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7538', '7998', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7539', '7998', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7540', '7998', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7541', '7998', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7542', '7998', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7543', '7998', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7544', '7999', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7545', '7999', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7546', '7999', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7547', '7999', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7548', '7999', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7549', '7999', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7550', '8003', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7551', '8003', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7552', '8003', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7553', '8003', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7554', '8003', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7555', '8003', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7556', '8004', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7557', '8004', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7558', '8004', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7559', '8004', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7560', '8004', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7561', '8004', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7562', '8008', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7563', '8008', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7564', '8008', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7565', '8008', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7566', '8008', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7567', '8008', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7568', '8010', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7569', '8010', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7570', '8010', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7571', '8010', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7572', '8010', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7573', '8010', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7574', '8017', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7575', '8017', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7576', '8017', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7577', '8017', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7578', '8017', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7579', '8017', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7580', '8020', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7581', '8020', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7582', '8020', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7583', '8020', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7584', '8020', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7585', '8020', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7586', '8007', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7587', '8007', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7588', '8007', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7589', '8007', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7590', '8007', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7591', '8007', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7592', '8019', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7593', '8019', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7594', '8019', '80', '-0.60');
INSERT INTO `assessment_calculation` VALUES ('7595', '8019', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7596', '8019', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7597', '8019', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7598', '8025', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7599', '8025', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7600', '8025', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7601', '8025', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7602', '8025', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7603', '8025', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7604', '8026', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7605', '8026', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7606', '8026', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7607', '8026', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7608', '8026', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7609', '8026', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7610', '8027', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7611', '8027', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7612', '8027', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7613', '8027', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7614', '8027', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7615', '8027', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7616', '8028', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7617', '8028', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7618', '8028', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7619', '8028', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7620', '8028', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7621', '8028', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7622', '8029', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7623', '8029', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7624', '8029', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7625', '8029', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7626', '8029', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7627', '8029', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7628', '8031', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7629', '8031', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7630', '8031', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7631', '8031', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7632', '8031', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7633', '8031', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7634', '8032', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7635', '8032', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7636', '8032', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7637', '8032', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7638', '8032', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7639', '8032', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7640', '8033', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7641', '8033', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7642', '8033', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7643', '8033', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7644', '8033', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7645', '8033', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7646', '8034', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7647', '8034', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7648', '8034', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7649', '8034', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7650', '8034', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7651', '8034', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7652', '8048', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7653', '8048', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7654', '8048', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7655', '8048', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7656', '8048', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7657', '8048', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7658', '8052', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7659', '8052', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7660', '8052', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7661', '8052', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7662', '8052', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7663', '8052', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7664', '8053', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7665', '8053', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7666', '8053', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7667', '8053', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7668', '8053', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7669', '8053', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7670', '8063', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7671', '8063', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7672', '8063', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7673', '8063', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7674', '8063', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7675', '8063', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7676', '8067', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7677', '8067', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7678', '8067', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7679', '8067', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7680', '8067', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7681', '8067', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7682', '8093', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7683', '8093', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7684', '8093', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7685', '8093', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7686', '8093', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7687', '8093', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7688', '8091', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7689', '8091', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7690', '8091', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7691', '8091', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7692', '8091', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7693', '8091', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7694', '8098', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7695', '8098', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7696', '8098', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7697', '8098', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7698', '8098', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7699', '8098', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7700', '8101', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7701', '8101', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7702', '8101', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7703', '8101', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7704', '8101', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7705', '8101', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7706', '8102', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7707', '8102', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7708', '8102', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7709', '8102', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7710', '8102', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7711', '8102', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7712', '8105', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7713', '8105', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7714', '8105', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7715', '8105', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7716', '8105', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7717', '8105', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7718', '8104', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7719', '8104', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7720', '8104', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7721', '8104', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7722', '8104', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7723', '8104', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7724', '8106', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7725', '8106', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7726', '8106', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7727', '8106', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7728', '8106', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7729', '8106', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7731', '8122', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7732', '8122', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7733', '8122', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7734', '8122', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7735', '8122', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7736', '8124', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7737', '8124', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7738', '8124', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7739', '8124', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7740', '8124', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7741', '8124', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7742', '8123', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7743', '8123', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7744', '8123', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7745', '8123', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7746', '8123', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7747', '8123', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7748', '8128', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7749', '8128', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7750', '8128', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7751', '8128', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7752', '8128', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7753', '8128', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7754', '8131', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7755', '8131', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7756', '8131', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7757', '8131', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7758', '8131', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7759', '8131', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7760', '8134', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7761', '8134', '79', '-0.20');
INSERT INTO `assessment_calculation` VALUES ('7762', '8134', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7763', '8134', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7764', '8134', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7765', '8134', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7766', '8158', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7767', '8158', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7768', '8158', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7769', '8158', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7770', '8158', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7771', '8158', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7772', '8172', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7773', '8172', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7774', '8172', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7775', '8172', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7776', '8172', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7777', '8172', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7778', '8179', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7779', '8179', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7780', '8179', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7781', '8179', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7782', '8179', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7783', '8179', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7784', '8184', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7785', '8184', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7786', '8184', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7787', '8184', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7788', '8184', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7789', '8184', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7790', '8186', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7791', '8186', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7792', '8186', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7793', '8186', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7794', '8186', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7795', '8186', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7796', '8211', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7797', '8211', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7798', '8211', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7799', '8211', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7800', '8211', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7801', '8211', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7802', '8221', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7803', '8221', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7804', '8221', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7805', '8221', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7806', '8221', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7807', '8221', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7808', '8226', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7809', '8226', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7810', '8226', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7811', '8226', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7812', '8226', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7813', '8226', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7814', '8227', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7815', '8227', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7816', '8227', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7817', '8227', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7818', '8227', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7819', '8227', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7820', '8228', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7821', '8228', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7822', '8228', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7823', '8228', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7824', '8228', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7825', '8228', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7826', '8229', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7827', '8229', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7828', '8229', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7829', '8229', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7830', '8229', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7831', '8229', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7832', '8233', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7833', '8233', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7834', '8233', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7835', '8233', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7836', '8233', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7837', '8233', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7838', '8234', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7839', '8234', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7840', '8234', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7841', '8234', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7842', '8234', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7843', '8234', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7844', '8235', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7845', '8235', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7846', '8235', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7847', '8235', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7848', '8235', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7849', '8235', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7850', '8236', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7851', '8236', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7852', '8236', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7853', '8236', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7854', '8236', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7855', '8236', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7856', '8238', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7857', '8238', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7858', '8238', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7859', '8238', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7860', '8238', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7861', '8238', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7862', '8232', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7863', '8232', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7864', '8232', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7865', '8232', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7866', '8232', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7867', '8232', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7868', '8239', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7869', '8239', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7870', '8239', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7871', '8239', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7872', '8239', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7873', '8239', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7874', '8240', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7875', '8240', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7876', '8240', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7877', '8240', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7878', '8240', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7879', '8240', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7880', '8225', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7881', '8225', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7882', '8225', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7883', '8225', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7884', '8225', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7885', '8225', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7886', '8241', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7887', '8241', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7888', '8241', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7889', '8241', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7890', '8241', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7891', '8241', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7892', '8252', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7893', '8252', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7894', '8252', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7895', '8252', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7896', '8252', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7897', '8252', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7898', '8258', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7899', '8258', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7900', '8258', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7901', '8258', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7902', '8258', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7903', '8258', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7904', '8261', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7905', '8261', '79', '-0.20');
INSERT INTO `assessment_calculation` VALUES ('7906', '8261', '80', '-1.00');
INSERT INTO `assessment_calculation` VALUES ('7907', '8261', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7908', '8261', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7909', '8261', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7910', '8260', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7911', '8260', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7912', '8260', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7913', '8260', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7914', '8260', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7915', '8260', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7916', '8273', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7917', '8273', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7918', '8273', '80', '-0.80');
INSERT INTO `assessment_calculation` VALUES ('7919', '8273', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7920', '8273', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7921', '8273', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7922', '8289', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7923', '8289', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7924', '8289', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7925', '8289', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7926', '8289', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7927', '8289', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7928', '8293', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7929', '8293', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7930', '8293', '80', '-0.20');
INSERT INTO `assessment_calculation` VALUES ('7931', '8293', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7932', '8293', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7933', '8293', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7934', '8301', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7935', '8301', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7936', '8301', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7937', '8301', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7938', '8301', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7939', '8301', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7940', '8306', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7941', '8306', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7942', '8306', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7943', '8306', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7944', '8306', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7945', '8306', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7946', '8310', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7947', '8310', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7948', '8310', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7949', '8310', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7950', '8310', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7951', '8310', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7952', '8316', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7953', '8316', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7954', '8316', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7955', '8316', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7956', '8316', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7957', '8316', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7958', '8318', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7959', '8318', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7960', '8318', '80', '-1.20');
INSERT INTO `assessment_calculation` VALUES ('7961', '8318', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7962', '8318', '74', '1.00');
INSERT INTO `assessment_calculation` VALUES ('7963', '8318', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7964', '8324', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7965', '8324', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7966', '8324', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7967', '8324', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7968', '8324', '74', '1.00');
INSERT INTO `assessment_calculation` VALUES ('7969', '8324', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7970', '8330', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7971', '8330', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7972', '8330', '80', '-0.20');
INSERT INTO `assessment_calculation` VALUES ('7973', '8330', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7974', '8330', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7975', '8330', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7976', '8323', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7977', '8323', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7978', '8323', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7979', '8323', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7980', '8323', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7981', '8323', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7982', '8325', '77', '0.40');
INSERT INTO `assessment_calculation` VALUES ('7983', '8325', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7984', '8325', '80', '-0.40');
INSERT INTO `assessment_calculation` VALUES ('7985', '8325', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7986', '8325', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7987', '8325', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7988', '8350', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7989', '8350', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7990', '8350', '80', '-0.20');
INSERT INTO `assessment_calculation` VALUES ('7991', '8350', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7992', '8350', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7993', '8350', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('7994', '8362', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7995', '8362', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7996', '8362', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7997', '8362', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7998', '8362', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('7999', '8362', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8000', '8363', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8001', '8363', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8002', '8363', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8003', '8363', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8004', '8363', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8005', '8363', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8006', '8364', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8007', '8364', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8008', '8364', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8009', '8364', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8010', '8364', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8011', '8364', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8012', '8375', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8013', '8375', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8014', '8375', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8015', '8375', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8016', '8375', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8017', '8375', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8018', '8377', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8019', '8377', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8020', '8377', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8021', '8377', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8022', '8377', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8023', '8377', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8024', '8381', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8025', '8381', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8026', '8381', '80', '-0.40');
INSERT INTO `assessment_calculation` VALUES ('8027', '8381', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8028', '8381', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8029', '8381', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8030', '8382', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8031', '8382', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8032', '8382', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8033', '8382', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8034', '8382', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8035', '8382', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8036', '8385', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8037', '8385', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8038', '8385', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8039', '8385', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8040', '8385', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8041', '8385', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8042', '8390', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8043', '8390', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8044', '8390', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8045', '8390', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8046', '8390', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8047', '8390', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8048', '8391', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8049', '8391', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8050', '8391', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8051', '8391', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8052', '8391', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8053', '8391', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8054', '8408', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8055', '8408', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8056', '8408', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8057', '8408', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8058', '8408', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8059', '8408', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8060', '8425', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8061', '8425', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8062', '8425', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8063', '8425', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8064', '8425', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8065', '8425', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8066', '8432', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8067', '8432', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8068', '8432', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8069', '8432', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8070', '8432', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8071', '8432', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8072', '8434', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8073', '8434', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8074', '8434', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8075', '8434', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8076', '8434', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8077', '8434', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8078', '8435', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8079', '8435', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8080', '8435', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8081', '8435', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8082', '8435', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8083', '8435', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8084', '8436', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8085', '8436', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8086', '8436', '80', '-0.20');
INSERT INTO `assessment_calculation` VALUES ('8087', '8436', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8088', '8436', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8089', '8436', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8090', '8441', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8091', '8441', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8092', '8441', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8093', '8441', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8094', '8441', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8095', '8441', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8096', '8443', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8097', '8443', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8098', '8443', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8099', '8443', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8100', '8443', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8101', '8443', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8102', '8444', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8103', '8444', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8104', '8444', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8105', '8444', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8106', '8444', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8107', '8444', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8108', '8445', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8109', '8445', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8110', '8445', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8111', '8445', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8112', '8445', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8113', '8445', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8114', '8446', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8115', '8446', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8116', '8446', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8117', '8446', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8118', '8446', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8119', '8446', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8120', '8447', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8121', '8447', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8122', '8447', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8123', '8447', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8124', '8447', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8125', '8447', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8126', '8448', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8127', '8448', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8128', '8448', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8129', '8448', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8130', '8448', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8131', '8448', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8132', '8439', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8133', '8439', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8134', '8439', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8135', '8439', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8136', '8439', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8137', '8439', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8138', '8454', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8139', '8454', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8140', '8454', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8141', '8454', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8142', '8454', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8143', '8454', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8144', '8480', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8145', '8480', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8146', '8480', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8147', '8480', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8148', '8480', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8149', '8480', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8150', '8496', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8151', '8496', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8152', '8496', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8153', '8496', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8154', '8496', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8155', '8496', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8156', '8511', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8157', '8511', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8158', '8511', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8159', '8511', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8160', '8511', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8161', '8511', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8162', '8536', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8163', '8536', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8164', '8536', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8165', '8536', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8166', '8536', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8167', '8536', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8168', '8540', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8169', '8540', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8170', '8540', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8171', '8540', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8172', '8540', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8173', '8540', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8174', '8549', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8175', '8549', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8176', '8549', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8177', '8549', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8178', '8549', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8179', '8549', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8180', '8552', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8181', '8552', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8182', '8552', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8183', '8552', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8184', '8552', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8185', '8552', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8186', '8565', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8187', '8565', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8188', '8565', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8189', '8565', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8190', '8565', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8191', '8565', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8192', '8567', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8193', '8567', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8194', '8567', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8195', '8567', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8196', '8567', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8197', '8567', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8198', '8569', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8199', '8569', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8200', '8569', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8201', '8569', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8202', '8569', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8203', '8569', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8204', '8568', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8205', '8568', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8206', '8568', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8207', '8568', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8208', '8568', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8209', '8568', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8210', '8570', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8211', '8570', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8212', '8570', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8213', '8570', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8214', '8570', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8215', '8570', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8216', '8571', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8217', '8571', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8218', '8571', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8219', '8571', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8220', '8571', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8221', '8571', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8222', '8572', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8223', '8572', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8224', '8572', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8225', '8572', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8226', '8572', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8227', '8572', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8228', '8573', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8229', '8573', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8230', '8573', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8231', '8573', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8232', '8573', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8233', '8573', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8234', '8576', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8235', '8576', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8236', '8576', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8237', '8576', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8238', '8576', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8239', '8576', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8240', '8584', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8241', '8584', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8242', '8584', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8243', '8584', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8244', '8584', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8245', '8584', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8246', '8583', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8247', '8583', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8248', '8583', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8249', '8583', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8250', '8583', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8251', '8583', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8252', '8585', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8253', '8585', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8254', '8585', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8255', '8585', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8256', '8585', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8257', '8585', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8258', '8594', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8259', '8594', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8260', '8594', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8261', '8594', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8262', '8594', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8263', '8594', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8264', '8596', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8265', '8596', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8266', '8596', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8267', '8596', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8268', '8596', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8269', '8596', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8270', '8597', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8271', '8597', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8272', '8597', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8273', '8597', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8274', '8597', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8275', '8597', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8276', '8598', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8277', '8598', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8278', '8598', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8279', '8598', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8280', '8598', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8281', '8598', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8282', '8592', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8283', '8592', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8284', '8592', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8285', '8592', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8286', '8592', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8287', '8592', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8288', '8593', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8289', '8593', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8290', '8593', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8291', '8593', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8292', '8593', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8293', '8593', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8294', '8599', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8295', '8599', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8296', '8599', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8297', '8599', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8298', '8599', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8299', '8599', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8300', '8602', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8301', '8602', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8302', '8602', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8303', '8602', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8304', '8602', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8305', '8602', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8306', '8604', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8307', '8604', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8308', '8604', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8309', '8604', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8310', '8604', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8311', '8604', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8312', '8618', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8313', '8618', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8314', '8618', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8315', '8618', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8316', '8618', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8317', '8618', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8318', '8621', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8319', '8621', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8320', '8621', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8321', '8621', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8322', '8621', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8323', '8621', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8324', '8627', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8325', '8627', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8326', '8627', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8327', '8627', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8328', '8627', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8329', '8627', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8330', '8628', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8331', '8628', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8332', '8628', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8333', '8628', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8334', '8628', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8335', '8628', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8336', '8630', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8337', '8630', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8338', '8630', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8339', '8630', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8340', '8630', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8341', '8630', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8342', '8631', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8343', '8631', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8344', '8631', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8345', '8631', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8346', '8631', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8347', '8631', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8348', '8632', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8349', '8632', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8350', '8632', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8351', '8632', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8352', '8632', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8353', '8632', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8354', '8633', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8355', '8633', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8356', '8633', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8357', '8633', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8358', '8633', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8359', '8633', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8360', '8635', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8361', '8635', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8362', '8635', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8363', '8635', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8364', '8635', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8365', '8635', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8366', '8636', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8367', '8636', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8368', '8636', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8369', '8636', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8370', '8636', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8371', '8636', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8372', '8637', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8373', '8637', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8374', '8637', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8375', '8637', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8376', '8637', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8377', '8637', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8378', '8638', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8379', '8638', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8380', '8638', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8381', '8638', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8382', '8638', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8383', '8638', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8384', '8639', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8385', '8639', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8386', '8639', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8387', '8639', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8388', '8639', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8389', '8639', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8390', '8640', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8391', '8640', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8392', '8640', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8393', '8640', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8394', '8640', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8395', '8640', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8396', '8641', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8397', '8641', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8398', '8641', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8399', '8641', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8400', '8641', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8401', '8641', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8402', '8642', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8403', '8642', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8404', '8642', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8405', '8642', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8406', '8642', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8407', '8642', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8408', '8643', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8409', '8643', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8410', '8643', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8411', '8643', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8412', '8643', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8413', '8643', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8414', '8644', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8415', '8644', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8416', '8644', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8417', '8644', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8418', '8644', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8419', '8644', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8420', '8645', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8421', '8645', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8422', '8645', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8423', '8645', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8424', '8645', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8425', '8645', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8426', '8646', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8427', '8646', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8428', '8646', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8429', '8646', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8430', '8646', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8431', '8646', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8432', '8647', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8433', '8647', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8434', '8647', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8435', '8647', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8436', '8647', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8437', '8647', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8438', '8648', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8439', '8648', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8440', '8648', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8441', '8648', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8442', '8648', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8443', '8648', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8444', '8650', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8445', '8650', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8446', '8650', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8447', '8650', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8448', '8650', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8449', '8650', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8450', '8651', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8451', '8651', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8452', '8651', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8453', '8651', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8454', '8651', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8455', '8651', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8456', '8652', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8457', '8652', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8458', '8652', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8459', '8652', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8460', '8652', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8461', '8652', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8462', '8653', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8463', '8653', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8464', '8653', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8465', '8653', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8466', '8653', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8467', '8653', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8468', '8654', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8469', '8654', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8470', '8654', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8471', '8654', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8472', '8654', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8473', '8654', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8474', '8656', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8475', '8656', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8476', '8656', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8477', '8656', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8478', '8656', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8479', '8656', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8480', '8672', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8481', '8672', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8482', '8672', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8483', '8672', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8484', '8672', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8485', '8672', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8486', '8678', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8487', '8678', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8488', '8678', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8489', '8678', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8490', '8678', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8491', '8678', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8492', '8683', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8493', '8683', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8494', '8683', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8495', '8683', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8496', '8683', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8497', '8683', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8498', '8688', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8499', '8688', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8500', '8688', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8501', '8688', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8502', '8688', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8503', '8688', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8504', '8692', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8505', '8692', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8506', '8692', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8507', '8692', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8508', '8692', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8509', '8692', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8510', '8693', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8511', '8693', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8512', '8693', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8513', '8693', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8514', '8693', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8515', '8693', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8516', '8695', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8517', '8695', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8518', '8695', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8519', '8695', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8520', '8695', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8521', '8695', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8522', '8698', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8523', '8698', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8524', '8698', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8525', '8698', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8526', '8698', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8527', '8698', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8528', '8696', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8529', '8696', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8530', '8696', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8531', '8696', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8532', '8696', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8533', '8696', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8534', '8699', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8535', '8699', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8536', '8699', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8537', '8699', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8538', '8699', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8539', '8699', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8540', '8703', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8541', '8703', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8542', '8703', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8543', '8703', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8544', '8703', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8545', '8703', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8546', '8706', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8547', '8706', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8548', '8706', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8549', '8706', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8550', '8706', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8551', '8706', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8552', '8710', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8553', '8710', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8554', '8710', '80', '-0.20');
INSERT INTO `assessment_calculation` VALUES ('8555', '8710', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8556', '8710', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8557', '8710', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8558', '8714', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8559', '8714', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8560', '8714', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8561', '8714', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8562', '8714', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8563', '8714', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8564', '8715', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8565', '8715', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8566', '8715', '80', '-1.80');
INSERT INTO `assessment_calculation` VALUES ('8567', '8715', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8568', '8715', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8569', '8715', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8570', '8720', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8571', '8720', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8572', '8720', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8573', '8720', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8574', '8720', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8575', '8720', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8576', '8722', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8577', '8722', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8578', '8722', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8579', '8722', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8580', '8722', '74', '1.00');
INSERT INTO `assessment_calculation` VALUES ('8581', '8722', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8582', '8723', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8583', '8723', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8584', '8723', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8585', '8723', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8586', '8723', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8587', '8723', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8588', '8732', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8589', '8732', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8590', '8732', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8591', '8732', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8592', '8732', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8593', '8732', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8594', '8735', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8595', '8735', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8596', '8735', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8597', '8735', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8598', '8735', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8599', '8735', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8600', '8755', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8601', '8755', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8602', '8755', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8603', '8755', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8604', '8755', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8605', '8755', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8606', '8759', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8607', '8759', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8608', '8759', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8609', '8759', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8610', '8776', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8611', '8776', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8612', '8776', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8613', '8776', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8614', '8776', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8615', '8776', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8616', '8794', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8617', '8794', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8618', '8794', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8619', '8794', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8620', '8794', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8621', '8794', '84', '0.75');
INSERT INTO `assessment_calculation` VALUES ('8622', '8796', '77', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8623', '8796', '79', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8624', '8796', '80', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8625', '8796', '78', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8626', '8796', '74', '0.00');
INSERT INTO `assessment_calculation` VALUES ('8627', '8796', '84', '0.75');

-- ----------------------------
-- Table structure for assessment_category
-- ----------------------------
DROP TABLE IF EXISTS `assessment_category`;
CREATE TABLE `assessment_category` (
  `code` varchar(50) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of assessment_category
-- ----------------------------
INSERT INTO `assessment_category` VALUES ('management');
INSERT INTO `assessment_category` VALUES ('overall');
INSERT INTO `assessment_category` VALUES ('performance');
INSERT INTO `assessment_category` VALUES ('time');

-- ----------------------------
-- Table structure for assessment_group
-- ----------------------------
DROP TABLE IF EXISTS `assessment_group`;
CREATE TABLE `assessment_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `import_id` varchar(14) NOT NULL DEFAULT '00000000000000' COMMENT 'setvice value,used to remove old data after reimport.',
  `scenario_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of assessment_group
-- ----------------------------

-- ----------------------------
-- Table structure for assessment_overall
-- ----------------------------
DROP TABLE IF EXISTS `assessment_overall`;
CREATE TABLE `assessment_overall` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) DEFAULT NULL,
  `assessment_category_code` varchar(50) DEFAULT NULL,
  `value` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `assessment_overall_sim_category` (`sim_id`,`assessment_category_code`),
  KEY `fk_assessment_overall_assessment_category_code` (`assessment_category_code`),
  CONSTRAINT `fk_assessment_overall_assessment_category_code` FOREIGN KEY (`assessment_category_code`) REFERENCES `assessment_category` (`code`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_assessment_overall_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of assessment_overall
-- ----------------------------

-- ----------------------------
-- Table structure for assessment_planing_point
-- ----------------------------
DROP TABLE IF EXISTS `assessment_planing_point`;
CREATE TABLE `assessment_planing_point` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `hero_behaviour_id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `type_scale` int(11) NOT NULL,
  `value` decimal(6,2) NOT NULL,
  `activity_parent_code` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assessment_planing_point_planing_fk_simulations` (`sim_id`),
  KEY `assessment_planing_point_fk_hero_behaviour` (`hero_behaviour_id`),
  KEY `assessment_planing_point_fk_task` (`task_id`),
  CONSTRAINT `assessment_planing_point_fk_hero_behaviour` FOREIGN KEY (`hero_behaviour_id`) REFERENCES `hero_behaviour` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `assessment_planing_point_fk_task` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `assessment_planing_point_planing_fk_simulations` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of assessment_planing_point
-- ----------------------------

-- ----------------------------
-- Table structure for assessment_points
-- ----------------------------
DROP TABLE IF EXISTS `assessment_points`;
CREATE TABLE `assessment_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `point_id` int(11) DEFAULT NULL,
  `dialog_id` int(11) DEFAULT NULL,
  `task_id` int(11) DEFAULT NULL,
  `mail_id` int(11) DEFAULT NULL,
  `value` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `assessment_detail_dialog_unique` (`dialog_id`,`point_id`,`sim_id`),
  UNIQUE KEY `assessment_detail_task_unique` (`task_id`,`point_id`,`sim_id`),
  UNIQUE KEY `assessment_detail_mail_unique` (`mail_id`,`point_id`,`sim_id`),
  KEY `fk_assessment_detail_sim_id` (`sim_id`),
  KEY `fk_assessment_detail_point_id` (`point_id`),
  CONSTRAINT `fk_assessment_detail_task_id` FOREIGN KEY (`task_id`) REFERENCES `day_plan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_assessment_detail_dialog_id` FOREIGN KEY (`dialog_id`) REFERENCES `replica` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_assessment_detail_mail_id` FOREIGN KEY (`mail_id`) REFERENCES `mail_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_assessment_detail_point_id` FOREIGN KEY (`point_id`) REFERENCES `hero_behaviour` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_assessment_detail_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15733 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of assessment_points
-- ----------------------------

-- ----------------------------
-- Table structure for characters
-- ----------------------------
DROP TABLE IF EXISTS `characters`;
CREATE TABLE `characters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `fio` varchar(64) DEFAULT NULL,
  `email` varchar(64) DEFAULT NULL,
  `code` tinyint(3) DEFAULT NULL,
  `skype` varchar(128) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `import_id` varchar(14) DEFAULT NULL COMMENT 'setvice value,used to remove old data after reimport.',
  `scenario_id` int(11) NOT NULL,
  `sex` varchar(1) DEFAULT NULL,
  `has_mail_theme` int(1) NOT NULL DEFAULT '0',
  `has_phone_theme` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `characters_scenario` (`scenario_id`),
  CONSTRAINT `characters_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of characters
-- ----------------------------

-- ----------------------------
-- Table structure for characters_points
-- ----------------------------
DROP TABLE IF EXISTS `characters_points`;
CREATE TABLE `characters_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dialog_id` int(11) NOT NULL,
  `point_id` int(11) NOT NULL,
  `add_value` int(11) NOT NULL COMMENT 'добавочное кол-во очков за данный ответ',
  `import_id` varchar(14) NOT NULL DEFAULT '00000000000000' COMMENT 'setvice value,used to remove old data after reimport.',
  `scenario_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_characters_points_dialog_id` (`dialog_id`),
  KEY `fk_characters_points_point_id` (`point_id`),
  KEY `characters_points_scenario` (`scenario_id`),
  CONSTRAINT `characters_points_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_characters_points_dialog_id` FOREIGN KEY (`dialog_id`) REFERENCES `replica` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_characters_points_point_id` FOREIGN KEY (`point_id`) REFERENCES `hero_behaviour` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Требуеме поведения';

-- ----------------------------
-- Records of characters_points
-- ----------------------------

-- ----------------------------
-- Table structure for communication_themes
-- ----------------------------
DROP TABLE IF EXISTS `communication_themes`;
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
  `scenario_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `communication_theme_uniq` (`code`,`mail_prefix`,`character_id`,`theme_usage`,`scenario_id`),
  KEY `fk_mail_character_themes_character_id` (`character_id`),
  KEY `fk_mail_character_themes_letter_number` (`letter_number`),
  KEY `communication_themes_mail_prefix` (`mail_prefix`),
  KEY `communication_themes_scenario` (`scenario_id`),
  KEY `communication_theme_recipient_profix` (`character_id`,`code`,`mail_prefix`),
  CONSTRAINT `communication_themes_characher` FOREIGN KEY (`character_id`) REFERENCES `characters` (`id`),
  CONSTRAINT `communication_themes_mail_prefix` FOREIGN KEY (`mail_prefix`) REFERENCES `mail_prefix` (`code`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `communication_themes_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Темы писем для персонажей';

-- ----------------------------
-- Records of communication_themes
-- ----------------------------

-- ----------------------------
-- Table structure for company_sizes
-- ----------------------------
DROP TABLE IF EXISTS `company_sizes`;
CREATE TABLE `company_sizes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of company_sizes
-- ----------------------------
INSERT INTO `company_sizes` VALUES ('1', 'менее 10 человек');
INSERT INTO `company_sizes` VALUES ('2', '10-50 человек');
INSERT INTO `company_sizes` VALUES ('3', '50-100 человек');
INSERT INTO `company_sizes` VALUES ('4', '100-500 человек');
INSERT INTO `company_sizes` VALUES ('5', '500-1000 человек');
INSERT INTO `company_sizes` VALUES ('6', '1000-5000 человек');
INSERT INTO `company_sizes` VALUES ('7', '5000-10000 человек');
INSERT INTO `company_sizes` VALUES ('8', 'более 10000 человек');

-- ----------------------------
-- Table structure for day_plan
-- ----------------------------
DROP TABLE IF EXISTS `day_plan`;
CREATE TABLE `day_plan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `date` time DEFAULT NULL,
  `day` varchar(50) NOT NULL,
  `task_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_day_plan_task_id` (`task_id`),
  KEY `fk_day_plan_sim_id` (`sim_id`),
  CONSTRAINT `fk_day_plan_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_day_plan_task_id` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of day_plan
-- ----------------------------

-- ----------------------------
-- Table structure for day_plan_log
-- ----------------------------
DROP TABLE IF EXISTS `day_plan_log`;
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
  CONSTRAINT `fk_day_plan_log_task_id` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Логирование состояние плана';

-- ----------------------------
-- Records of day_plan_log
-- ----------------------------

-- ----------------------------
-- Table structure for decline_explanation
-- ----------------------------
DROP TABLE IF EXISTS `decline_explanation`;
CREATE TABLE `decline_explanation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invite_id` int(11) DEFAULT NULL,
  `invite_recipient_id` int(10) unsigned DEFAULT NULL,
  `invite_owner_id` int(10) unsigned DEFAULT NULL,
  `vacancy_label` int(11) DEFAULT NULL,
  `reason_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `decline_explanation_fk_invite` (`invite_id`),
  KEY `decline_explanation_fk_recipient_id` (`invite_recipient_id`),
  KEY `decline_explanation_fk_invite_owner_id` (`invite_owner_id`),
  KEY `decline_explanation_fk_decline_reason_id` (`reason_id`),
  CONSTRAINT `decline_explanation_fk_decline_reason_id` FOREIGN KEY (`reason_id`) REFERENCES `decline_reason` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `decline_explanation_fk_invite` FOREIGN KEY (`invite_id`) REFERENCES `invites` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `decline_explanation_fk_invite_owner_id` FOREIGN KEY (`invite_owner_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `decline_explanation_fk_recipient_id` FOREIGN KEY (`invite_recipient_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of decline_explanation
-- ----------------------------

-- ----------------------------
-- Table structure for decline_reason
-- ----------------------------
DROP TABLE IF EXISTS `decline_reason`;
CREATE TABLE `decline_reason` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(120) NOT NULL,
  `sort_order` int(11) DEFAULT '0',
  `is_display` tinyint(1) DEFAULT '1',
  `alias` varchar(120) DEFAULT NULL,
  `registration_only` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of decline_reason
-- ----------------------------
INSERT INTO `decline_reason` VALUES ('1', 'Не хочу регистрироваться', '0', '1', 'dont_want_to_register', '1');
INSERT INTO `decline_reason` VALUES ('2', 'Не интересует вакансия', '0', '1', 'nor_interest_vacancy', '0');
INSERT INTO `decline_reason` VALUES ('3', 'Не хочу проходить тест', '0', '1', 'dont_want_pass_test', '0');
INSERT INTO `decline_reason` VALUES ('4', 'Другое', '0', '1', 'other', '0');

-- ----------------------------
-- Table structure for dialogs
-- ----------------------------
DROP TABLE IF EXISTS `dialogs`;
CREATE TABLE `dialogs` (
  `code` varchar(10) NOT NULL,
  `title` varchar(250) NOT NULL,
  `type` varchar(30) DEFAULT NULL,
  `start_by` varchar(30) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `delay` int(11) DEFAULT NULL,
  `category` int(11) DEFAULT NULL,
  `is_use_in_demo` tinyint(1) DEFAULT NULL,
  `import_id` varchar(60) NOT NULL,
  `scenario_id` int(11) NOT NULL,
  KEY `dialogs_scenario` (`scenario_id`),
  CONSTRAINT `dialogs_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dialogs
-- ----------------------------

-- ----------------------------
-- Table structure for dialog_subtypes
-- ----------------------------
DROP TABLE IF EXISTS `dialog_subtypes`;
CREATE TABLE `dialog_subtypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL COMMENT 'идентификатор типа диалога',
  `title` varchar(20) NOT NULL,
  `slug` varchar(60) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_dialog_subtypes_type_id` (`type_id`),
  CONSTRAINT `fk_dialog_subtypes_type_id` FOREIGN KEY (`type_id`) REFERENCES `dialog_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dialog_subtypes
-- ----------------------------
INSERT INTO `dialog_subtypes` VALUES ('1', '1', 'Звонок', 'call');
INSERT INTO `dialog_subtypes` VALUES ('2', '1', 'Разговор по телефону', 'phone_talk');
INSERT INTO `dialog_subtypes` VALUES ('3', '2', 'Визит', 'visit');
INSERT INTO `dialog_subtypes` VALUES ('4', '2', 'Встреча', 'meeting');
INSERT INTO `dialog_subtypes` VALUES ('5', '2', 'Стук в дверь', 'knock_knock');

-- ----------------------------
-- Table structure for dialog_types
-- ----------------------------
DROP TABLE IF EXISTS `dialog_types`;
CREATE TABLE `dialog_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dialog_types
-- ----------------------------

-- ----------------------------
-- Table structure for emails_queue
-- ----------------------------
DROP TABLE IF EXISTS `emails_queue`;
CREATE TABLE `emails_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(200) DEFAULT NULL,
  `sender_email` varchar(200) DEFAULT NULL,
  `recipients` text,
  `copies` text,
  `body` longblob,
  `attachments` text,
  `created_at` datetime DEFAULT NULL,
  `sended_at` datetime DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  `errors` longblob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of emails_queue
-- ----------------------------

-- ----------------------------
-- Table structure for emails_sub
-- ----------------------------
DROP TABLE IF EXISTS `emails_sub`;
CREATE TABLE `emails_sub` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of emails_sub
-- ----------------------------

-- ----------------------------
-- Table structure for events_on_hold_logic
-- ----------------------------
DROP TABLE IF EXISTS `events_on_hold_logic`;
CREATE TABLE `events_on_hold_logic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='перечень вариантов для удержанного события';

-- ----------------------------
-- Records of events_on_hold_logic
-- ----------------------------
INSERT INTO `events_on_hold_logic` VALUES ('1', 'ничего');
INSERT INTO `events_on_hold_logic` VALUES ('2', 'Покашливания, полтергейсты');

-- ----------------------------
-- Table structure for events_results
-- ----------------------------
DROP TABLE IF EXISTS `events_results`;
CREATE TABLE `events_results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of events_results
-- ----------------------------
INSERT INTO `events_results` VALUES ('1', 'не ответить');
INSERT INTO `events_results` VALUES ('2', 'сделаю сам');
INSERT INTO `events_results` VALUES ('3', 'пригласить аналитика2');
INSERT INTO `events_results` VALUES ('4', 'Сделает аналитик 2');
INSERT INTO `events_results` VALUES ('5', 'пригласить аналитика1');
INSERT INTO `events_results` VALUES ('6', 'Сделает аналитик 1');
INSERT INTO `events_results` VALUES ('7', 'нет результата');

-- ----------------------------
-- Table structure for events_triggers
-- ----------------------------
DROP TABLE IF EXISTS `events_triggers`;
CREATE TABLE `events_triggers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `trigger_time` time DEFAULT NULL,
  `force_run` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_events_triggers_event_id` (`event_id`),
  KEY `fk_events_triggers_sim_id` (`sim_id`),
  CONSTRAINT `fk_events_triggers_event_id` FOREIGN KEY (`event_id`) REFERENCES `event_sample` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_events_triggers_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of events_triggers
-- ----------------------------

-- ----------------------------
-- Table structure for event_sample
-- ----------------------------
DROP TABLE IF EXISTS `event_sample`;
CREATE TABLE `event_sample` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL,
  `title` varchar(100) NOT NULL,
  `on_ignore_result` int(11) NOT NULL,
  `on_hold_logic` int(11) NOT NULL,
  `trigger_time` time DEFAULT NULL,
  `import_id` varchar(14) DEFAULT NULL COMMENT 'setvice value,used to remove old data after reimport.',
  `scenario_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_events_samples_on_hold_logic` (`on_hold_logic`),
  KEY `fk_events_samples_on_ignore_result` (`on_ignore_result`),
  KEY `event_sample_scenario` (`scenario_id`),
  CONSTRAINT `event_sample_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_events_samples_on_hold_logic` FOREIGN KEY (`on_hold_logic`) REFERENCES `events_on_hold_logic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_events_samples_on_ignore_result` FOREIGN KEY (`on_ignore_result`) REFERENCES `events_results` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of event_sample
-- ----------------------------

-- ----------------------------
-- Table structure for excel_points_formula
-- ----------------------------
DROP TABLE IF EXISTS `excel_points_formula`;
CREATE TABLE `excel_points_formula` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `formula` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='Формулы для расчета оценки по экселю';

-- ----------------------------
-- Records of excel_points_formula
-- ----------------------------
INSERT INTO `excel_points_formula` VALUES ('1', '=SUM(Логистика!B6:M7)+SUM(Логистика!B10:M14)');
INSERT INTO `excel_points_formula` VALUES ('2', '=SUM(Производство!B6:M7)+SUM(Производство!B10:M14)');
INSERT INTO `excel_points_formula` VALUES ('3', '=SUM(Сводный!N6:Q7)+SUM(Сводный!N10:Q14)-SUM(Сводный!B6:M7)-SUM(Сводный!B10:M14)');
INSERT INTO `excel_points_formula` VALUES ('4', '=SUM(Сводный!R6:R7)+SUM(Сводный!R10:R14)-SUM(Сводный!B6:M7)-SUM(Сводный!B10:M14)');
INSERT INTO `excel_points_formula` VALUES ('5', '=SUM(Сводный!N16:Q16)-(SUM(Сводный!B8:M8)-SUM(Сводный!B15:M15))');
INSERT INTO `excel_points_formula` VALUES ('6', '=Сводный!R16-(SUM(Сводный!B8:M8)-SUM(Сводный!B15:M15))');
INSERT INTO `excel_points_formula` VALUES ('7', '=Сводный!R18');
INSERT INTO `excel_points_formula` VALUES ('8', '=SUM(Сводный!N19:Q19)');
INSERT INTO `excel_points_formula` VALUES ('9', '=SUM(Сводный!N20:Q20)');

-- ----------------------------
-- Table structure for feedback
-- ----------------------------
DROP TABLE IF EXISTS `feedback`;
CREATE TABLE `feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `theme` varchar(200) NOT NULL,
  `message` text,
  `email` varchar(100) DEFAULT NULL,
  `addition` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of feedback
-- ----------------------------

-- ----------------------------
-- Table structure for flag
-- ----------------------------
DROP TABLE IF EXISTS `flag`;
CREATE TABLE `flag` (
  `code` varchar(10) NOT NULL,
  `description` text NOT NULL,
  `import_id` varchar(60) NOT NULL,
  `scenario_id` int(11) NOT NULL,
  `delay` int(3) DEFAULT '0',
  PRIMARY KEY (`code`),
  KEY `flag_scenario` (`scenario_id`),
  CONSTRAINT `flag_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of flag
-- ----------------------------

-- ----------------------------
-- Table structure for flag_allow_meeting
-- ----------------------------
DROP TABLE IF EXISTS `flag_allow_meeting`;
CREATE TABLE `flag_allow_meeting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `flag_code` varchar(10) NOT NULL,
  `meeting_id` int(11) NOT NULL,
  `value` tinyint(1) DEFAULT NULL,
  `import_id` varchar(14) NOT NULL,
  `scenario_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of flag_allow_meeting
-- ----------------------------

-- ----------------------------
-- Table structure for flag_block_dialog
-- ----------------------------
DROP TABLE IF EXISTS `flag_block_dialog`;
CREATE TABLE `flag_block_dialog` (
  `flag_code` varchar(5) NOT NULL,
  `dialog_code` varchar(10) NOT NULL,
  `value` tinyint(1) DEFAULT NULL,
  `import_id` varchar(14) DEFAULT NULL,
  `scenario_id` int(11) NOT NULL,
  KEY `fk_flag_block_dialog_flag_code` (`flag_code`),
  KEY `flag_block_dialog_scenario` (`scenario_id`),
  CONSTRAINT `flag_block_dialog_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_flag_block_dialog_flag_code` FOREIGN KEY (`flag_code`) REFERENCES `flag` (`code`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of flag_block_dialog
-- ----------------------------

-- ----------------------------
-- Table structure for flag_block_mail
-- ----------------------------
DROP TABLE IF EXISTS `flag_block_mail`;
CREATE TABLE `flag_block_mail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `flag_code` varchar(5) NOT NULL,
  `value` tinyint(1) DEFAULT NULL,
  `mail_template_id` int(11) NOT NULL,
  `import_id` varchar(60) DEFAULT NULL,
  `scenario_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `flag_block_mail_mail_template` (`mail_template_id`),
  KEY `fk_flag_block_mail__flag_code` (`flag_code`),
  KEY `flag_block_mail_scenario` (`scenario_id`),
  CONSTRAINT `flag_block_mail_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_flag_block_mail__flag_code` FOREIGN KEY (`flag_code`) REFERENCES `flag` (`code`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `flag_block_mail_mail_template` FOREIGN KEY (`mail_template_id`) REFERENCES `mail_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of flag_block_mail
-- ----------------------------

-- ----------------------------
-- Table structure for flag_block_replica
-- ----------------------------
DROP TABLE IF EXISTS `flag_block_replica`;
CREATE TABLE `flag_block_replica` (
  `flag_code` varchar(5) NOT NULL,
  `replica_id` int(11) NOT NULL,
  `value` tinyint(1) DEFAULT NULL,
  `import_id` varchar(14) DEFAULT NULL,
  `scenario_id` int(11) NOT NULL,
  KEY `fk_flag_block_replica_flag_code` (`flag_code`),
  KEY `flag_block_replica_scenario` (`scenario_id`),
  CONSTRAINT `flag_block_replica_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_flag_block_replica_flag_code` FOREIGN KEY (`flag_code`) REFERENCES `flag` (`code`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of flag_block_replica
-- ----------------------------

-- ----------------------------
-- Table structure for flag_communication_theme_dependence
-- ----------------------------
DROP TABLE IF EXISTS `flag_communication_theme_dependence`;
CREATE TABLE `flag_communication_theme_dependence` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `communication_theme_id` int(11) NOT NULL,
  `flag_code` varchar(10) NOT NULL,
  `scenario_id` int(11) NOT NULL,
  `import_id` varchar(60) NOT NULL,
  `value` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_communication_theme_id` (`communication_theme_id`),
  KEY `fk_flag_code` (`flag_code`),
  KEY `fk_scenario_id` (`scenario_id`),
  CONSTRAINT `fk_communication_theme_id` FOREIGN KEY (`communication_theme_id`) REFERENCES `communication_themes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_flag_code` FOREIGN KEY (`flag_code`) REFERENCES `flag` (`code`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_scenario_id` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of flag_communication_theme_dependence
-- ----------------------------

-- ----------------------------
-- Table structure for flag_run_email
-- ----------------------------
DROP TABLE IF EXISTS `flag_run_email`;
CREATE TABLE `flag_run_email` (
  `flag_code` varchar(10) NOT NULL,
  `mail_code` varchar(5) NOT NULL,
  `import_id` varchar(14) DEFAULT NULL,
  `scenario_id` int(11) NOT NULL,
  PRIMARY KEY (`flag_code`,`mail_code`),
  KEY `fk_flag_run_email_flag_code` (`flag_code`),
  KEY `fk_flag_run_email_mail_code` (`mail_code`),
  KEY `flag_run_email_scenario` (`scenario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of flag_run_email
-- ----------------------------

-- ----------------------------
-- Table structure for flag_switch_time
-- ----------------------------
DROP TABLE IF EXISTS `flag_switch_time`;
CREATE TABLE `flag_switch_time` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `flag_code` varchar(10) NOT NULL,
  `value` tinyint(1) DEFAULT NULL,
  `time` time DEFAULT NULL,
  `scenario_id` int(11) NOT NULL,
  `import_id` varchar(14) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of flag_switch_time
-- ----------------------------

-- ----------------------------
-- Table structure for free_email_provider
-- ----------------------------
DROP TABLE IF EXISTS `free_email_provider`;
CREATE TABLE `free_email_provider` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3575 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of free_email_provider
-- ----------------------------
INSERT INTO `free_email_provider` VALUES ('1', '1033edge.com');
INSERT INTO `free_email_provider` VALUES ('2', '11mail.com');
INSERT INTO `free_email_provider` VALUES ('3', '123india.com');
INSERT INTO `free_email_provider` VALUES ('4', '2bmail.co.uk');
INSERT INTO `free_email_provider` VALUES ('5', '321media.com');
INSERT INTO `free_email_provider` VALUES ('6', '3dmail.com');
INSERT INTO `free_email_provider` VALUES ('7', '5star.com');
INSERT INTO `free_email_provider` VALUES ('8', '888.nu');
INSERT INTO `free_email_provider` VALUES ('9', 'abcflash.net');
INSERT INTO `free_email_provider` VALUES ('10', 'abolition-now.com');
INSERT INTO `free_email_provider` VALUES ('11', 'advalvas.be');
INSERT INTO `free_email_provider` VALUES ('12', 'acmecity.com');
INSERT INTO `free_email_provider` VALUES ('13', 'aamail.net');
INSERT INTO `free_email_provider` VALUES ('14', 'alloymail.com');
INSERT INTO `free_email_provider` VALUES ('15', 'ato.check.com');
INSERT INTO `free_email_provider` VALUES ('16', 'altavista.net');
INSERT INTO `free_email_provider` VALUES ('17', 'iname.com');
INSERT INTO `free_email_provider` VALUES ('18', 'cheerful.com');
INSERT INTO `free_email_provider` VALUES ('19', 'earthling.net');
INSERT INTO `free_email_provider` VALUES ('20', 'innocent.com');
INSERT INTO `free_email_provider` VALUES ('21', 'technologist.com');
INSERT INTO `free_email_provider` VALUES ('22', 'mindless.com');
INSERT INTO `free_email_provider` VALUES ('23', 'cyberdude.com');
INSERT INTO `free_email_provider` VALUES ('24', 'cybergal.com');
INSERT INTO `free_email_provider` VALUES ('25', 'unforgettable.com');
INSERT INTO `free_email_provider` VALUES ('26', 'writeme.com');
INSERT INTO `free_email_provider` VALUES ('27', 'usa.net');
INSERT INTO `free_email_provider` VALUES ('28', 'amrer.net');
INSERT INTO `free_email_provider` VALUES ('29', 'amuro.net');
INSERT INTO `free_email_provider` VALUES ('30', 'amuromail.com');
INSERT INTO `free_email_provider` VALUES ('31', 'ancestry.com');
INSERT INTO `free_email_provider` VALUES ('32', 'angelfan.com');
INSERT INTO `free_email_provider` VALUES ('33', 'angelfire.com');
INSERT INTO `free_email_provider` VALUES ('34', 'animalhouse.com');
INSERT INTO `free_email_provider` VALUES ('35', 'anotherwin95.com');
INSERT INTO `free_email_provider` VALUES ('36', 'anti-social.com');
INSERT INTO `free_email_provider` VALUES ('37', 'apexmail.com');
INSERT INTO `free_email_provider` VALUES ('38', 'apmail.com');
INSERT INTO `free_email_provider` VALUES ('39', 'arcademaster.com');
INSERT INTO `free_email_provider` VALUES ('40', 'aristotle.org');
INSERT INTO `free_email_provider` VALUES ('41', 'asean-mail.com');
INSERT INTO `free_email_provider` VALUES ('42', 'asheville.com');
INSERT INTO `free_email_provider` VALUES ('43', 'communityconnect.com');
INSERT INTO `free_email_provider` VALUES ('44', 'asiancityweb.com');
INSERT INTO `free_email_provider` VALUES ('45', 'asiansonly.net');
INSERT INTO `free_email_provider` VALUES ('46', 'asianwired.net');
INSERT INTO `free_email_provider` VALUES ('47', 'asiapoint.net');
INSERT INTO `free_email_provider` VALUES ('48', 'anote.com');
INSERT INTO `free_email_provider` VALUES ('49', 'atlink.com');
INSERT INTO `free_email_provider` VALUES ('50', 'ausi.com');
INSERT INTO `free_email_provider` VALUES ('51', 'australia.edu');
INSERT INTO `free_email_provider` VALUES ('52', 'awsom.net');
INSERT INTO `free_email_provider` VALUES ('53', 'axoskate.com');
INSERT INTO `free_email_provider` VALUES ('54', 'backpackers.com');
INSERT INTO `free_email_provider` VALUES ('55', 'bangkok.com');
INSERT INTO `free_email_provider` VALUES ('56', 'bangkok2000.com');
INSERT INTO `free_email_provider` VALUES ('57', 'sukhumvit.net');
INSERT INTO `free_email_provider` VALUES ('58', 'mekhong.com');
INSERT INTO `free_email_provider` VALUES ('59', 'krongthip.com');
INSERT INTO `free_email_provider` VALUES ('60', 'farang.net');
INSERT INTO `free_email_provider` VALUES ('61', 'bannertown.net');
INSERT INTO `free_email_provider` VALUES ('62', 'bboy.zzn.com');
INSERT INTO `free_email_provider` VALUES ('63', 'beer.com');
INSERT INTO `free_email_provider` VALUES ('64', 'webmail.bellsouth.net');
INSERT INTO `free_email_provider` VALUES ('65', 'berkscounty.com');
INSERT INTO `free_email_provider` VALUES ('66', 'bettergolf.net');
INSERT INTO `free_email_provider` VALUES ('67', 'bigassweb.com');
INSERT INTO `free_email_provider` VALUES ('68', 'bigfoot.com');
INSERT INTO `free_email_provider` VALUES ('69', 'bimamail.com');
INSERT INTO `free_email_provider` VALUES ('70', 'bitmail.com');
INSERT INTO `free_email_provider` VALUES ('71', 'our.st');
INSERT INTO `free_email_provider` VALUES ('72', 'bla-bla.com');
INSERT INTO `free_email_provider` VALUES ('73', 'blazemail.com');
INSERT INTO `free_email_provider` VALUES ('74', 'bombdiggity.com');
INSERT INTO `free_email_provider` VALUES ('75', 'broadcast.net');
INSERT INTO `free_email_provider` VALUES ('76', 'billsfan.net');
INSERT INTO `free_email_provider` VALUES ('77', 'bisons.com');
INSERT INTO `free_email_provider` VALUES ('78', 'wingnutz.com');
INSERT INTO `free_email_provider` VALUES ('79', 'buffymail.com');
INSERT INTO `free_email_provider` VALUES ('80', 'businessweekmail.com');
INSERT INTO `free_email_provider` VALUES ('81', 'busta-rhymes.com');
INSERT INTO `free_email_provider` VALUES ('82', 'busymail.com');
INSERT INTO `free_email_provider` VALUES ('83', 'homeart.com');
INSERT INTO `free_email_provider` VALUES ('84', 'buyersusa.com');
INSERT INTO `free_email_provider` VALUES ('85', 'mail.byte.it');
INSERT INTO `free_email_provider` VALUES ('86', 'byteme.com');
INSERT INTO `free_email_provider` VALUES ('87', 'callsign.net');
INSERT INTO `free_email_provider` VALUES ('88', 'canada.com');
INSERT INTO `free_email_provider` VALUES ('89', 'ehmail.com');
INSERT INTO `free_email_provider` VALUES ('90', 'canadianmail.com');
INSERT INTO `free_email_provider` VALUES ('91', 'canoemail.com');
INSERT INTO `free_email_provider` VALUES ('92', 'wildmail.com');
INSERT INTO `free_email_provider` VALUES ('93', 'animal.net');
INSERT INTO `free_email_provider` VALUES ('94', 'moose-mail.com');
INSERT INTO `free_email_provider` VALUES ('95', 'snail-mail.ney');
INSERT INTO `free_email_provider` VALUES ('96', 'whale-mail.com');
INSERT INTO `free_email_provider` VALUES ('97', 'careerbuildermail.com');
INSERT INTO `free_email_provider` VALUES ('98', 'emailem.com');
INSERT INTO `free_email_provider` VALUES ('99', 'resumemail.com');
INSERT INTO `free_email_provider` VALUES ('100', 'casablancaresort.com');
INSERT INTO `free_email_provider` VALUES ('101', 'casino.com');
INSERT INTO `free_email_provider` VALUES ('102', 'catholic.org');
INSERT INTO `free_email_provider` VALUES ('103', 'sportsmail.com');
INSERT INTO `free_email_provider` VALUES ('104', 'ccnmail.com');
INSERT INTO `free_email_provider` VALUES ('105', 'celtic.com');
INSERT INTO `free_email_provider` VALUES ('106', 'charmedmail.com');
INSERT INTO `free_email_provider` VALUES ('107', 'chek.com');
INSERT INTO `free_email_provider` VALUES ('108', '97rock.com');
INSERT INTO `free_email_provider` VALUES ('109', 'astrosfan.com');
INSERT INTO `free_email_provider` VALUES ('110', 'billsfan.com');
INSERT INTO `free_email_provider` VALUES ('111', 'bossofthemoss.com');
INSERT INTO `free_email_provider` VALUES ('112', 'cyclefanz.com');
INSERT INTO `free_email_provider` VALUES ('113', 'dawsonmail.com');
INSERT INTO `free_email_provider` VALUES ('114', 'disinfo.net');
INSERT INTO `free_email_provider` VALUES ('115', 'eatmydirt.com');
INSERT INTO `free_email_provider` VALUES ('116', 'felicitymail.com');
INSERT INTO `free_email_provider` VALUES ('117', 'goodstick.com');
INSERT INTO `free_email_provider` VALUES ('118', 'handleit.com');
INSERT INTO `free_email_provider` VALUES ('119', 'hitthe.net');
INSERT INTO `free_email_provider` VALUES ('120', 'iowaemail.com');
INSERT INTO `free_email_provider` VALUES ('121', 'lowandslow.com');
INSERT INTO `free_email_provider` VALUES ('122', 'miatadriver.com');
INSERT INTO `free_email_provider` VALUES ('123', 'mjfrogmail.com');
INSERT INTO `free_email_provider` VALUES ('124', 'nhmail.com');
INSERT INTO `free_email_provider` VALUES ('125', 'oldies1041.com');
INSERT INTO `free_email_provider` VALUES ('126', 'pickupman.com');
INSERT INTO `free_email_provider` VALUES ('127', 'racefanz.com');
INSERT INTO `free_email_provider` VALUES ('128', 'socceramerica.net');
INSERT INTO `free_email_provider` VALUES ('129', 'soccermomz.com');
INSERT INTO `free_email_provider` VALUES ('130', 'speedrules.com');
INSERT INTO `free_email_provider` VALUES ('131', 'speedrulz.com');
INSERT INTO `free_email_provider` VALUES ('132', 'sporttruckdriver.com');
INSERT INTO `free_email_provider` VALUES ('133', 'swingeasyhithard.com');
INSERT INTO `free_email_provider` VALUES ('134', 'teamdiscovery.com');
INSERT INTO `free_email_provider` VALUES ('135', 'temtulsa.net');
INSERT INTO `free_email_provider` VALUES ('136', 'toolsource.com');
INSERT INTO `free_email_provider` VALUES ('137', 'truckerz.com');
INSERT INTO `free_email_provider` VALUES ('138', 'vote4gop.org');
INSERT INTO `free_email_provider` VALUES ('139', 'zensearch.net');
INSERT INTO `free_email_provider` VALUES ('140', 'cheyenneweb.com');
INSERT INTO `free_email_provider` VALUES ('141', 'chez.com');
INSERT INTO `free_email_provider` VALUES ('142', 'chickmail.com');
INSERT INTO `free_email_provider` VALUES ('143', 'chinalook.com');
INSERT INTO `free_email_provider` VALUES ('144', 'christianmail.net');
INSERT INTO `free_email_provider` VALUES ('145', 'churchusa.com');
INSERT INTO `free_email_provider` VALUES ('146', 'cincinow.net');
INSERT INTO `free_email_provider` VALUES ('147', 'citeweb.net');
INSERT INTO `free_email_provider` VALUES ('148', 'city2city.com');
INSERT INTO `free_email_provider` VALUES ('149', 'claramail.com');
INSERT INTO `free_email_provider` VALUES ('150', 'clubvdo.net');
INSERT INTO `free_email_provider` VALUES ('151', 'cmpmail.com');
INSERT INTO `free_email_provider` VALUES ('152', 'edtnmail.com');
INSERT INTO `free_email_provider` VALUES ('153', 'crwmail.com');
INSERT INTO `free_email_provider` VALUES ('154', 'iwmail.com');
INSERT INTO `free_email_provider` VALUES ('155', 'varbizmail.com');
INSERT INTO `free_email_provider` VALUES ('156', 'homemail.com');
INSERT INTO `free_email_provider` VALUES ('157', 'workmail.com');
INSERT INTO `free_email_provider` VALUES ('158', 'schoolmail.com');
INSERT INTO `free_email_provider` VALUES ('159', 'fan.com');
INSERT INTO `free_email_provider` VALUES ('160', 'cnnsimail.com');
INSERT INTO `free_email_provider` VALUES ('161', 'footballmail.com');
INSERT INTO `free_email_provider` VALUES ('162', 'baseballmail.com');
INSERT INTO `free_email_provider` VALUES ('163', 'basketballmail.com');
INSERT INTO `free_email_provider` VALUES ('164', 'hockeymail.com');
INSERT INTO `free_email_provider` VALUES ('165', 'hoopsmail.com');
INSERT INTO `free_email_provider` VALUES ('166', 'racingmail.com');
INSERT INTO `free_email_provider` VALUES ('167', 'soccermail.com');
INSERT INTO `free_email_provider` VALUES ('168', 'tennismail.com');
INSERT INTO `free_email_provider` VALUES ('169', 'codec.ro');
INSERT INTO `free_email_provider` VALUES ('170', 'email.ro');
INSERT INTO `free_email_provider` VALUES ('171', 'coldmail.com');
INSERT INTO `free_email_provider` VALUES ('172', 'collectiblesuperstore.com');
INSERT INTO `free_email_provider` VALUES ('173', 'collegebeat.com');
INSERT INTO `free_email_provider` VALUES ('174', 'collegeclub.com');
INSERT INTO `free_email_provider` VALUES ('175', 'colleges.com');
INSERT INTO `free_email_provider` VALUES ('176', 'comprendemail.com');
INSERT INTO `free_email_provider` VALUES ('177', 'conk.com');
INSERT INTO `free_email_provider` VALUES ('178', 'conok.com');
INSERT INTO `free_email_provider` VALUES ('179', 'planetarymotion.net');
INSERT INTO `free_email_provider` VALUES ('180', 'cabacabana.com');
INSERT INTO `free_email_provider` VALUES ('181', 'cornells.com');
INSERT INTO `free_email_provider` VALUES ('182', 'CPAonline.net');
INSERT INTO `free_email_provider` VALUES ('183', 'cristianemail.com');
INSERT INTO `free_email_provider` VALUES ('184', 'crosshairs.com');
INSERT INTO `free_email_provider` VALUES ('185', 'crosswinds.net');
INSERT INTO `free_email_provider` VALUES ('186', 'cyber-africa.net');
INSERT INTO `free_email_provider` VALUES ('187', 'cyber4all.com');
INSERT INTO `free_email_provider` VALUES ('188', 'CyberCafeMaui.com');
INSERT INTO `free_email_provider` VALUES ('189', 'cyberspace-asia.com');
INSERT INTO `free_email_provider` VALUES ('190', 'deutschland-net.com');
INSERT INTO `free_email_provider` VALUES ('191', 'cybergrrl.com');
INSERT INTO `free_email_provider` VALUES ('192', 'cybermail.net');
INSERT INTO `free_email_provider` VALUES ('193', 'cybertrains.org');
INSERT INTO `free_email_provider` VALUES ('194', 'cynetcity.com');
INSERT INTO `free_email_provider` VALUES ('195', 'dwp.net');
INSERT INTO `free_email_provider` VALUES ('196', 'dawnsonmail.com');
INSERT INTO `free_email_provider` VALUES ('197', 'DCemail.com');
INSERT INTO `free_email_provider` VALUES ('198', 'dejanews.com');
INSERT INTO `free_email_provider` VALUES ('199', 'deneg.net');
INSERT INTO `free_email_provider` VALUES ('200', 'depechemode.com');
INSERT INTO `free_email_provider` VALUES ('201', 'deseretmail.com');
INSERT INTO `free_email_provider` VALUES ('202', 'deskmail.com');
INSERT INTO `free_email_provider` VALUES ('203', 'destin.com');
INSERT INTO `free_email_provider` VALUES ('204', 'digibel.be');
INSERT INTO `free_email_provider` VALUES ('205', 'discovery.com');
INSERT INTO `free_email_provider` VALUES ('206', 'discoverymail.com');
INSERT INTO `free_email_provider` VALUES ('207', 'thedoghousemail.com');
INSERT INTO `free_email_provider` VALUES ('208', 'dog.com');
INSERT INTO `free_email_provider` VALUES ('209', 'doityourself.com');
INSERT INTO `free_email_provider` VALUES ('210', 'doramail.com');
INSERT INTO `free_email_provider` VALUES ('211', 'dragoncon.net');
INSERT INTO `free_email_provider` VALUES ('212', 'posta.net');
INSERT INTO `free_email_provider` VALUES ('213', 'galamb.net');
INSERT INTO `free_email_provider` VALUES ('214', 'gramszu.net');
INSERT INTO `free_email_provider` VALUES ('215', 'drotposta.hu');
INSERT INTO `free_email_provider` VALUES ('216', 'dunlopdriver.com');
INSERT INTO `free_email_provider` VALUES ('217', 'dunloprider.com');
INSERT INTO `free_email_provider` VALUES ('218', 'duno.com');
INSERT INTO `free_email_provider` VALUES ('219', 'ecompare.com');
INSERT INTO `free_email_provider` VALUES ('220', 'EarthCam.net');
INSERT INTO `free_email_provider` VALUES ('221', 'WebCamMail.com');
INSERT INTO `free_email_provider` VALUES ('222', 'earthonline.net');
INSERT INTO `free_email_provider` VALUES ('223', 'eastmail.com');
INSERT INTO `free_email_provider` VALUES ('224', 'easypost.com');
INSERT INTO `free_email_provider` VALUES ('225', 'easy.to');
INSERT INTO `free_email_provider` VALUES ('226', 'hello.to');
INSERT INTO `free_email_provider` VALUES ('227', 'i.am');
INSERT INTO `free_email_provider` VALUES ('228', 'hey.to');
INSERT INTO `free_email_provider` VALUES ('229', 'pagina.de');
INSERT INTO `free_email_provider` VALUES ('230', 'w3.to');
INSERT INTO `free_email_provider` VALUES ('231', 'messages.to');
INSERT INTO `free_email_provider` VALUES ('232', 'edmail.com');
INSERT INTO `free_email_provider` VALUES ('233', 'educastmail.com');
INSERT INTO `free_email_provider` VALUES ('234', 'wwdg.com');
INSERT INTO `free_email_provider` VALUES ('235', 'email.com');
INSERT INTO `free_email_provider` VALUES ('236', 'science.com.au');
INSERT INTO `free_email_provider` VALUES ('237', 'medical.net.au');
INSERT INTO `free_email_provider` VALUES ('238', 'ecardmail.com');
INSERT INTO `free_email_provider` VALUES ('239', 'england.com');
INSERT INTO `free_email_provider` VALUES ('240', 'mail.entrepeneurmag.com');
INSERT INTO `free_email_provider` VALUES ('241', 'ethos.st');
INSERT INTO `free_email_provider` VALUES ('242', 'etoast.com');
INSERT INTO `free_email_provider` VALUES ('243', 'etrademail.com');
INSERT INTO `free_email_provider` VALUES ('244', 'eudoramail.com');
INSERT INTO `free_email_provider` VALUES ('245', 'extenda.net');
INSERT INTO `free_email_provider` VALUES ('246', 'fansonlymail.com');
INSERT INTO `free_email_provider` VALUES ('247', 'fastermail.com');
INSERT INTO `free_email_provider` VALUES ('248', 'felicity.com');
INSERT INTO `free_email_provider` VALUES ('249', 'fetchmail.com');
INSERT INTO `free_email_provider` VALUES ('250', 'fetchmail.co.uk');
INSERT INTO `free_email_provider` VALUES ('251', 'fiberia.com');
INSERT INTO `free_email_provider` VALUES ('252', 'yoursubdomain.findhere.com');
INSERT INTO `free_email_provider` VALUES ('253', 'finfin.com');
INSERT INTO `free_email_provider` VALUES ('254', 'flashemail.com');
INSERT INTO `free_email_provider` VALUES ('255', 'flashmail.com');
INSERT INTO `free_email_provider` VALUES ('256', 'flashmail.net');
INSERT INTO `free_email_provider` VALUES ('257', 'letterbox.com');
INSERT INTO `free_email_provider` VALUES ('258', 'foodmail.com');
INSERT INTO `free_email_provider` VALUES ('259', 'forfree.at');
INSERT INTO `free_email_provider` VALUES ('260', 'fortunecity.com');
INSERT INTO `free_email_provider` VALUES ('261', 'freeaccount.com');
INSERT INTO `free_email_provider` VALUES ('262', 'freemail.c3.hu');
INSERT INTO `free_email_provider` VALUES ('263', 'freemail.com.au');
INSERT INTO `free_email_provider` VALUES ('264', 'freemail.com.pk');
INSERT INTO `free_email_provider` VALUES ('265', 'daha.com');
INSERT INTO `free_email_provider` VALUES ('266', 'freemail.org.mk');
INSERT INTO `free_email_provider` VALUES ('267', 'free-org.com');
INSERT INTO `free_email_provider` VALUES ('268', 'yourname.freeservers.com');
INSERT INTO `free_email_provider` VALUES ('269', 'freestamp.com');
INSERT INTO `free_email_provider` VALUES ('270', 'mail.freetown.com');
INSERT INTO `free_email_provider` VALUES ('271', 'freeyellow.com');
INSERT INTO `free_email_provider` VALUES ('272', 'fresnomail.com');
INSERT INTO `free_email_provider` VALUES ('273', 'schoolemail.com');
INSERT INTO `free_email_provider` VALUES ('274', 'collegemail.com');
INSERT INTO `free_email_provider` VALUES ('275', 'thedorm.com');
INSERT INTO `free_email_provider` VALUES ('276', 'mycampus.com');
INSERT INTO `free_email_provider` VALUES ('277', 'mypad.com');
INSERT INTO `free_email_provider` VALUES ('278', 'friends-cafe.com');
INSERT INTO `free_email_provider` VALUES ('279', 'fullmail.com');
INSERT INTO `free_email_provider` VALUES ('280', 'fwnb.com');
INSERT INTO `free_email_provider` VALUES ('281', 'garbage.com');
INSERT INTO `free_email_provider` VALUES ('282', 'catsrule.garfield.com');
INSERT INTO `free_email_provider` VALUES ('283', 'gaybrighton.co.uk');
INSERT INTO `free_email_provider` VALUES ('284', 'geecities.com');
INSERT INTO `free_email_provider` VALUES ('285', 'geeklife.com');
INSERT INTO `free_email_provider` VALUES ('286', 'geocities.com');
INSERT INTO `free_email_provider` VALUES ('287', 'ghanamail.com');
INSERT INTO `free_email_provider` VALUES ('288', 'ghostmail.com');
INSERT INTO `free_email_provider` VALUES ('289', 'giantsfan.com');
INSERT INTO `free_email_provider` VALUES ('290', 'gigileung.org');
INSERT INTO `free_email_provider` VALUES ('291', 'gmx.de');
INSERT INTO `free_email_provider` VALUES ('292', 'gnwmail.com');
INSERT INTO `free_email_provider` VALUES ('293', 'go2.com.py');
INSERT INTO `free_email_provider` VALUES ('294', 'gocollege.com');
INSERT INTO `free_email_provider` VALUES ('295', 'gocubs.com');
INSERT INTO `free_email_provider` VALUES ('296', 'go.com');
INSERT INTO `free_email_provider` VALUES ('297', 'goplay.com');
INSERT INTO `free_email_provider` VALUES ('298', 'gotmail.com');
INSERT INTO `free_email_provider` VALUES ('299', 'govolsfan.com');
INSERT INTO `free_email_provider` VALUES ('300', 'grabmail.com');
INSERT INTO `free_email_provider` VALUES ('301', 'graffiti.net');
INSERT INTO `free_email_provider` VALUES ('302', 'grapplers.com');
INSERT INTO `free_email_provider` VALUES ('303', 'gtemail.net');
INSERT INTO `free_email_provider` VALUES ('304', 'gurlmail.com');
INSERT INTO `free_email_provider` VALUES ('305', 'hamptonroads.com');
INSERT INTO `free_email_provider` VALUES ('306', 'hanmail.net');
INSERT INTO `free_email_provider` VALUES ('307', 'happypuppy.com');
INSERT INTO `free_email_provider` VALUES ('308', 'headbone.com');
INSERT INTO `free_email_provider` VALUES ('309', 'hello.net.au');
INSERT INTO `free_email_provider` VALUES ('310', 'hempseed.com');
INSERT INTO `free_email_provider` VALUES ('311', 'heremail.com');
INSERT INTO `free_email_provider` VALUES ('312', 'MTtestdriver.com');
INSERT INTO `free_email_provider` VALUES ('313', 'vivavelocity.com');
INSERT INTO `free_email_provider` VALUES ('314', 'automotiveauthority.com');
INSERT INTO `free_email_provider` VALUES ('315', 'offroadwarrior.com');
INSERT INTO `free_email_provider` VALUES ('316', 'truckers.com');
INSERT INTO `free_email_provider` VALUES ('317', 'mail.hitthebeach.com');
INSERT INTO `free_email_provider` VALUES ('318', 'hkg.net');
INSERT INTO `free_email_provider` VALUES ('319', 'homeworkcentral.com');
INSERT INTO `free_email_provider` VALUES ('320', 'hongkong.com');
INSERT INTO `free_email_provider` VALUES ('321', 'hotbot.com');
INSERT INTO `free_email_provider` VALUES ('322', 'hotepmail.com');
INSERT INTO `free_email_provider` VALUES ('323', 'hotmail.com');
INSERT INTO `free_email_provider` VALUES ('324', 'HotPOP.com');
INSERT INTO `free_email_provider` VALUES ('325', 'PunkAss.com');
INSERT INTO `free_email_provider` VALUES ('326', 'Phreaker.net');
INSERT INTO `free_email_provider` VALUES ('327', 'SexMagnet.com');
INSERT INTO `free_email_provider` VALUES ('328', 'BonBon.net');
INSERT INTO `free_email_provider` VALUES ('329', 'ToughGuy.net');
INSERT INTO `free_email_provider` VALUES ('330', 'astrosfan.net');
INSERT INTO `free_email_provider` VALUES ('331', 'html.tou.com');
INSERT INTO `free_email_provider` VALUES ('332', 'hypernautica.com');
INSERT INTO `free_email_provider` VALUES ('333', 'i-connect.com');
INSERT INTO `free_email_provider` VALUES ('334', 'ID-base.com');
INSERT INTO `free_email_provider` VALUES ('335', 'techspot.com');
INSERT INTO `free_email_provider` VALUES ('336', 'techscout.com');
INSERT INTO `free_email_provider` VALUES ('337', 'techseek.com');
INSERT INTO `free_email_provider` VALUES ('338', 'ignmail.com');
INSERT INTO `free_email_provider` VALUES ('339', 'ihateclowns.com');
INSERT INTO `free_email_provider` VALUES ('340', 'ilovejesus.com');
INSERT INTO `free_email_provider` VALUES ('341', 'ilovethemovies.com');
INSERT INTO `free_email_provider` VALUES ('342', 'imail.org');
INSERT INTO `free_email_provider` VALUES ('343', 'pcsrock.com');
INSERT INTO `free_email_provider` VALUES ('344', 'imaginemail.com');
INSERT INTO `free_email_provider` VALUES ('345', 'indocities.com');
INSERT INTO `free_email_provider` VALUES ('346', 'indo-mail.com');
INSERT INTO `free_email_provider` VALUES ('347', 'indomail.com');
INSERT INTO `free_email_provider` VALUES ('348', 'surat.com');
INSERT INTO `free_email_provider` VALUES ('349', 'info66.com');
INSERT INTO `free_email_provider` VALUES ('350', 'info-media.de');
INSERT INTO `free_email_provider` VALUES ('351', 'infospacemail.com');
INSERT INTO `free_email_provider` VALUES ('352', 'insidebaltimore.net');
INSERT INTO `free_email_provider` VALUES ('353', 'host-it.com.sg');
INSERT INTO `free_email_provider` VALUES ('354', 'interburp.com');
INSERT INTO `free_email_provider` VALUES ('355', 'internet-club.com');
INSERT INTO `free_email_provider` VALUES ('356', 'investormail.com');
INSERT INTO `free_email_provider` VALUES ('357', 'ireland.com');
INSERT INTO `free_email_provider` VALUES ('358', 'isleuthmail.com');
INSERT INTO `free_email_provider` VALUES ('359', 'ivillage.com');
INSERT INTO `free_email_provider` VALUES ('360', 'jahoopa.com');
INSERT INTO `free_email_provider` VALUES ('361', 'jaydemail.com');
INSERT INTO `free_email_provider` VALUES ('362', 'jerusalemmail.com');
INSERT INTO `free_email_provider` VALUES ('363', 'jewishmail.com');
INSERT INTO `free_email_provider` VALUES ('364', 'joinme.com');
INSERT INTO `free_email_provider` VALUES ('365', 'jokes.com');
INSERT INTO `free_email_provider` VALUES ('366', 'joymail.com');
INSERT INTO `free_email_provider` VALUES ('367', 'jump.com');
INSERT INTO `free_email_provider` VALUES ('368', 'juniormail.com');
INSERT INTO `free_email_provider` VALUES ('369', 'juno.com');
INSERT INTO `free_email_provider` VALUES ('370', 'justicemail.com');
INSERT INTO `free_email_provider` VALUES ('371', 'kansascity.com');
INSERT INTO `free_email_provider` VALUES ('372', 'ksanmail.com');
INSERT INTO `free_email_provider` VALUES ('373', 'kbjrmail.com');
INSERT INTO `free_email_provider` VALUES ('374', 'kcks.com');
INSERT INTO `free_email_provider` VALUES ('375', 'keyemail.com');
INSERT INTO `free_email_provider` VALUES ('376', 'kitznet.at');
INSERT INTO `free_email_provider` VALUES ('377', 'kmail.com.au');
INSERT INTO `free_email_provider` VALUES ('378', 'mail.kmsp.com');
INSERT INTO `free_email_provider` VALUES ('379', 'konx.com');
INSERT INTO `free_email_provider` VALUES ('380', 'kozmail.com');
INSERT INTO `free_email_provider` VALUES ('381', 'ksee24mail.com');
INSERT INTO `free_email_provider` VALUES ('382', 'kube93mail.com');
INSERT INTO `free_email_provider` VALUES ('383', 'outel.com');
INSERT INTO `free_email_provider` VALUES ('384', 'la.com');
INSERT INTO `free_email_provider` VALUES ('385', 'latinmail.com');
INSERT INTO `free_email_provider` VALUES ('386', 'latino.com');
INSERT INTO `free_email_provider` VALUES ('387', 'law.com');
INSERT INTO `free_email_provider` VALUES ('388', 'letsgomets.net');
INSERT INTO `free_email_provider` VALUES ('389', 'lick101.com');
INSERT INTO `free_email_provider` VALUES ('390', 'linktrader.com');
INSERT INTO `free_email_provider` VALUES ('391', 'looksmart.com');
INSERT INTO `free_email_provider` VALUES ('392', 'liquidinformation.net');
INSERT INTO `free_email_provider` VALUES ('393', 'loobie.com');
INSERT INTO `free_email_provider` VALUES ('394', 'looksmart.com.au');
INSERT INTO `free_email_provider` VALUES ('395', 'looksmart.co.uk');
INSERT INTO `free_email_provider` VALUES ('396', 'lovemail.com');
INSERT INTO `free_email_provider` VALUES ('397', 'lycosemail.com');
INSERT INTO `free_email_provider` VALUES ('398', 'm4.org');
INSERT INTO `free_email_provider` VALUES ('399', 'macbox.com');
INSERT INTO `free_email_provider` VALUES ('400', 'macfreak.com');
INSERT INTO `free_email_provider` VALUES ('401', 'madcreations.com');
INSERT INTO `free_email_provider` VALUES ('402', 'MailandNews.com');
INSERT INTO `free_email_provider` VALUES ('403', 'check.com');
INSERT INTO `free_email_provider` VALUES ('404', 'mailcity.com');
INSERT INTO `free_email_provider` VALUES ('405', 'excite.com');
INSERT INTO `free_email_provider` VALUES ('406', 'mailgate.gr');
INSERT INTO `free_email_provider` VALUES ('407', 'mail.md');
INSERT INTO `free_email_provider` VALUES ('408', 'mail.org.uk');
INSERT INTO `free_email_provider` VALUES ('409', 'mailpost.zzn.com');
INSERT INTO `free_email_provider` VALUES ('410', 'mailshuttle.com');
INSERT INTO `free_email_provider` VALUES ('411', 'mailtag.com');
INSERT INTO `free_email_provider` VALUES ('412', 'tfz.net');
INSERT INTO `free_email_provider` VALUES ('413', 'mailwire.com');
INSERT INTO `free_email_provider` VALUES ('414', 'maktoob.com');
INSERT INTO `free_email_provider` VALUES ('415', 'mariahc.com');
INSERT INTO `free_email_provider` VALUES ('416', 'mariah-carey.ml.org');
INSERT INTO `free_email_provider` VALUES ('417', 'martindalemail.com');
INSERT INTO `free_email_provider` VALUES ('418', 'm-hmail.com');
INSERT INTO `free_email_provider` VALUES ('419', 'attymail.com');
INSERT INTO `free_email_provider` VALUES ('420', 'lexis-nexis-mail.com');
INSERT INTO `free_email_provider` VALUES ('421', 'sagra.lu');
INSERT INTO `free_email_provider` VALUES ('422', 'marketing.lu');
INSERT INTO `free_email_provider` VALUES ('423', 'matmail.com');
INSERT INTO `free_email_provider` VALUES ('424', 'mauimail.com');
INSERT INTO `free_email_provider` VALUES ('425', 'cybercafemaui.com');
INSERT INTO `free_email_provider` VALUES ('426', 'mauritius.com');
INSERT INTO `free_email_provider` VALUES ('427', 'maxmail.co.uk');
INSERT INTO `free_email_provider` VALUES ('428', 'ukmax.com');
INSERT INTO `free_email_provider` VALUES ('429', 'medscape.com');
INSERT INTO `free_email_provider` VALUES ('430', 'medmail.com');
INSERT INTO `free_email_provider` VALUES ('431', 'megapoint.com');
INSERT INTO `free_email_provider` VALUES ('432', 'mikrotamanet.com');
INSERT INTO `free_email_provider` VALUES ('433', 'mini-mail.com');
INSERT INTO `free_email_provider` VALUES ('434', 'mochamail.com');
INSERT INTO `free_email_provider` VALUES ('435', 'movieluver.com');
INSERT INTO `free_email_provider` VALUES ('436', 'mrpost.com');
INSERT INTO `free_email_provider` VALUES ('437', 'msgbox.com');
INSERT INTO `free_email_provider` VALUES ('438', 'music.com');
INSERT INTO `free_email_provider` VALUES ('439', 'muslimsonline.com');
INSERT INTO `free_email_provider` VALUES ('440', 'muslimemail.comandother');
INSERT INTO `free_email_provider` VALUES ('441', 'mycool.com');
INSERT INTO `free_email_provider` VALUES ('442', 'mygo.com');
INSERT INTO `free_email_provider` VALUES ('443', 'speedemail.net');
INSERT INTO `free_email_provider` VALUES ('444', 'myworldmail.com');
INSERT INTO `free_email_provider` VALUES ('445', 'nandomail.com');
INSERT INTO `free_email_provider` VALUES ('446', 'naplesnews.net');
INSERT INTO `free_email_provider` VALUES ('447', 'naui.net');
INSERT INTO `free_email_provider` VALUES ('448', 'naz.com');
INSERT INTO `free_email_provider` VALUES ('449', 'netby.dk');
INSERT INTO `free_email_provider` VALUES ('450', 'netgenie.com');
INSERT INTO `free_email_provider` VALUES ('451', 'nimail.com');
INSERT INTO `free_email_provider` VALUES ('452', 'netmanor.com');
INSERT INTO `free_email_provider` VALUES ('453', 'netnet.com.sg');
INSERT INTO `free_email_provider` VALUES ('454', 'netnoir.net');
INSERT INTO `free_email_provider` VALUES ('455', 'clubnetnoir.com');
INSERT INTO `free_email_provider` VALUES ('456', 'net-pager.net');
INSERT INTO `free_email_provider` VALUES ('457', 'netradiomail.com');
INSERT INTO `free_email_provider` VALUES ('458', 'ntscan.com');
INSERT INTO `free_email_provider` VALUES ('459', 'netscape.net');
INSERT INTO `free_email_provider` VALUES ('460', 'nettaxi.com');
INSERT INTO `free_email_provider` VALUES ('461', 'netzero.net');
INSERT INTO `free_email_provider` VALUES ('462', 'newmail.net');
INSERT INTO `free_email_provider` VALUES ('463', 'nchoicemail.com');
INSERT INTO `free_email_provider` VALUES ('464', 'nexxmail.com');
INSERT INTO `free_email_provider` VALUES ('465', 'nfmail.com');
INSERT INTO `free_email_provider` VALUES ('466', 'nicebush.com');
INSERT INTO `free_email_provider` VALUES ('467', 'norikomail.com');
INSERT INTO `free_email_provider` VALUES ('468', 'OaklandAs-fan.com');
INSERT INTO `free_email_provider` VALUES ('469', 'officedomain.com');
INSERT INTO `free_email_provider` VALUES ('470', 'home-email.com');
INSERT INTO `free_email_provider` VALUES ('471', 'office-email.com');
INSERT INTO `free_email_provider` VALUES ('472', 'AirForceEmail.com');
INSERT INTO `free_email_provider` VALUES ('473', 'oldies104mail.com');
INSERT INTO `free_email_provider` VALUES ('474', 'onvillage.com');
INSERT INTO `free_email_provider` VALUES ('475', 'operamail.com');
INSERT INTO `free_email_provider` VALUES ('476', 'ozbytes.net.au');
INSERT INTO `free_email_provider` VALUES ('477', 'pagons.org');
INSERT INTO `free_email_provider` VALUES ('478', 'ParsMail.com');
INSERT INTO `free_email_provider` VALUES ('479', 'pathfindermail.com');
INSERT INTO `free_email_provider` VALUES ('480', 'peachworld.com');
INSERT INTO `free_email_provider` VALUES ('481', 'pemail.net');
INSERT INTO `free_email_provider` VALUES ('482', 'pconnections.net');
INSERT INTO `free_email_provider` VALUES ('483', 'mail.pharmacy.com');
INSERT INTO `free_email_provider` VALUES ('484', 'pinoymail.com');
INSERT INTO `free_email_provider` VALUES ('485', 'planet-mail.com');
INSERT INTO `free_email_provider` VALUES ('486', 'planetaccess.com');
INSERT INTO `free_email_provider` VALUES ('487', 'planetall.com');
INSERT INTO `free_email_provider` VALUES ('488', 'planetdirect.com');
INSERT INTO `free_email_provider` VALUES ('489', 'planetearthinter.net');
INSERT INTO `free_email_provider` VALUES ('490', 'polbox.com');
INSERT INTO `free_email_provider` VALUES ('491', 'popaccount.com');
INSERT INTO `free_email_provider` VALUES ('492', 'popmail.com');
INSERT INTO `free_email_provider` VALUES ('493', 'prontomail.com');
INSERT INTO `free_email_provider` VALUES ('494', 'populus.net');
INSERT INTO `free_email_provider` VALUES ('495', 'portableoffice.com');
INSERT INTO `free_email_provider` VALUES ('496', 'posta.ro');
INSERT INTO `free_email_provider` VALUES ('497', 'sativa.ro.org');
INSERT INTO `free_email_provider` VALUES ('498', 'postmark.net');
INSERT INTO `free_email_provider` VALUES ('499', 'postmaster.co.uk');
INSERT INTO `free_email_provider` VALUES ('500', 'pousa.com');
INSERT INTO `free_email_provider` VALUES ('501', 'probemail.com');
INSERT INTO `free_email_provider` VALUES ('502', 'prolaunch.com');
INSERT INTO `free_email_provider` VALUES ('503', 'quakemail.com');
INSERT INTO `free_email_provider` VALUES ('504', 'quikmail.com');
INSERT INTO `free_email_provider` VALUES ('505', 'quiklinks.com');
INSERT INTO `free_email_provider` VALUES ('506', 'sharewaredevelopers.com');
INSERT INTO `free_email_provider` VALUES ('507', 'allracing.com');
INSERT INTO `free_email_provider` VALUES ('508', 'bikeracer.com');
INSERT INTO `free_email_provider` VALUES ('509', 'boatracers.com');
INSERT INTO `free_email_provider` VALUES ('510', 'dirtracer.com');
INSERT INTO `free_email_provider` VALUES ('511', 'dragracer.com');
INSERT INTO `free_email_provider` VALUES ('512', 'indyracers.com');
INSERT INTO `free_email_provider` VALUES ('513', 'latemodels.com');
INSERT INTO `free_email_provider` VALUES ('514', 'racedriver.com');
INSERT INTO `free_email_provider` VALUES ('515', 'stockracer.com');
INSERT INTO `free_email_provider` VALUES ('516', 'truckracer.com');
INSERT INTO `free_email_provider` VALUES ('517', 'ragingbull.com');
INSERT INTO `free_email_provider` VALUES ('518', 'subnetwork.com');
INSERT INTO `free_email_provider` VALUES ('519', 'rsub.com');
INSERT INTO `free_email_provider` VALUES ('520', 'bunko.com');
INSERT INTO `free_email_provider` VALUES ('521', 'thisgirl.com');
INSERT INTO `free_email_provider` VALUES ('522', 'ratt-n-roll.com');
INSERT INTO `free_email_provider` VALUES ('523', 'realradiomail.com');
INSERT INTO `free_email_provider` VALUES ('524', 'recycler.com');
INSERT INTO `free_email_provider` VALUES ('525', 'rediffmail.com');
INSERT INTO `free_email_provider` VALUES ('526', 'myremarq.com');
INSERT INTO `free_email_provider` VALUES ('527', 'richmondhill.com');
INSERT INTO `free_email_provider` VALUES ('528', 'roanokemail.com');
INSERT INTO `free_email_provider` VALUES ('529', 'rocketmail.com');
INSERT INTO `free_email_provider` VALUES ('530', 'rockfan.com');
INSERT INTO `free_email_provider` VALUES ('531', 'metalfan.com');
INSERT INTO `free_email_provider` VALUES ('532', 'bluesfan.com');
INSERT INTO `free_email_provider` VALUES ('533', 'jazzfan.com');
INSERT INTO `free_email_provider` VALUES ('534', 'reggafan.com');
INSERT INTO `free_email_provider` VALUES ('535', 'hiphopfan.com');
INSERT INTO `free_email_provider` VALUES ('536', 'ravemail.com');
INSERT INTO `free_email_provider` VALUES ('537', 'housemail.com');
INSERT INTO `free_email_provider` VALUES ('538', 'housefancom');
INSERT INTO `free_email_provider` VALUES ('539', 'operafan.com');
INSERT INTO `free_email_provider` VALUES ('540', 'zydecofan.com');
INSERT INTO `free_email_provider` VALUES ('541', 'classicalfan.com');
INSERT INTO `free_email_provider` VALUES ('542', 'folkfan.com');
INSERT INTO `free_email_provider` VALUES ('543', 'swingfan.com');
INSERT INTO `free_email_provider` VALUES ('544', 'discofan.com');
INSERT INTO `free_email_provider` VALUES ('545', 'skafan.com');
INSERT INTO `free_email_provider` VALUES ('546', 'funkfan.com');
INSERT INTO `free_email_provider` VALUES ('547', 'gospelfan.com');
INSERT INTO `free_email_provider` VALUES ('548', 'roosh.com');
INSERT INTO `free_email_provider` VALUES ('549', 'rotfl.com');
INSERT INTO `free_email_provider` VALUES ('550', 'rvshop.com');
INSERT INTO `free_email_provider` VALUES ('551', 'sabreshockey.com');
INSERT INTO `free_email_provider` VALUES ('552', 'sacbeemail.com');
INSERT INTO `free_email_provider` VALUES ('553', 'safarimail.com');
INSERT INTO `free_email_provider` VALUES ('554', 'saintmail.net');
INSERT INTO `free_email_provider` VALUES ('555', 'mail.salu.net');
INSERT INTO `free_email_provider` VALUES ('556', 'silkroad.net');
INSERT INTO `free_email_provider` VALUES ('557', 'schizo.com');
INSERT INTO `free_email_provider` VALUES ('558', 'schoolsucks.com');
INSERT INTO `free_email_provider` VALUES ('559', 'seguros.com.br');
INSERT INTO `free_email_provider` VALUES ('560', 'singpost.com');
INSERT INTO `free_email_provider` VALUES ('561', 'soccerAmerica.net');
INSERT INTO `free_email_provider` VALUES ('562', 'softhome.net');
INSERT INTO `free_email_provider` VALUES ('563', 'samilan.net');
INSERT INTO `free_email_provider` VALUES ('564', 'mail.spaceports.com');
INSERT INTO `free_email_provider` VALUES ('565', 'spacewar.com');
INSERT INTO `free_email_provider` VALUES ('566', 'spaceart.com');
INSERT INTO `free_email_provider` VALUES ('567', 'spacebank.com');
INSERT INTO `free_email_provider` VALUES ('568', 'space-bank.com');
INSERT INTO `free_email_provider` VALUES ('569', 'space-man.com');
INSERT INTO `free_email_provider` VALUES ('570', 'spacemart.com');
INSERT INTO `free_email_provider` VALUES ('571', 'space-ship.com');
INSERT INTO `free_email_provider` VALUES ('572', 'space-travel.com');
INSERT INTO `free_email_provider` VALUES ('573', 'stalag13.com');
INSERT INTO `free_email_provider` VALUES ('574', 'start.com.au');
INSERT INTO `free_email_provider` VALUES ('575', 'starting-point.com');
INSERT INTO `free_email_provider` VALUES ('576', 'StarTrekMail.com');
INSERT INTO `free_email_provider` VALUES ('577', 'stealthmail.com');
INSERT INTO `free_email_provider` VALUES ('578', 'stones.com');
INSERT INTO `free_email_provider` VALUES ('579', 'storksite.com');
INSERT INTO `free_email_provider` VALUES ('580', 'studentcenter.org');
INSERT INTO `free_email_provider` VALUES ('581', 'members.student.com');
INSERT INTO `free_email_provider` VALUES ('582', 'wyrm.supernews.com');
INSERT INTO `free_email_provider` VALUES ('583', 'surfree.com');
INSERT INTO `free_email_provider` VALUES ('584', 'surfy.net');
INSERT INTO `free_email_provider` VALUES ('585', 'swipermail.zzn.com');
INSERT INTO `free_email_provider` VALUES ('586', 'switchboardmail.com');
INSERT INTO `free_email_provider` VALUES ('587', 'talk21.com');
INSERT INTO `free_email_provider` VALUES ('588', 'tbwt.com');
INSERT INTO `free_email_provider` VALUES ('589', 'teamtulsa.net');
INSERT INTO `free_email_provider` VALUES ('590', 'telebot.net');
INSERT INTO `free_email_provider` VALUES ('591', 'telerymd.com');
INSERT INTO `free_email_provider` VALUES ('592', 'teleserve.dynip.com');
INSERT INTO `free_email_provider` VALUES ('593', 'dallas.theboys.com');
INSERT INTO `free_email_provider` VALUES ('594', 'fan.theboys.com');
INSERT INTO `free_email_provider` VALUES ('595', 'football.theboys.com');
INSERT INTO `free_email_provider` VALUES ('596', 'mail.theboys.com');
INSERT INTO `free_email_provider` VALUES ('597', 'theglobe.com');
INSERT INTO `free_email_provider` VALUES ('598', 'passwordmail.com');
INSERT INTO `free_email_provider` VALUES ('599', 'mythirdage.com');
INSERT INTO `free_email_provider` VALUES ('600', 'thoic.com');
INSERT INTO `free_email_provider` VALUES ('601', 'topchat.com');
INSERT INTO `free_email_provider` VALUES ('602', 'topteam.bg');
INSERT INTO `free_email_provider` VALUES ('603', 'totalmusic.net');
INSERT INTO `free_email_provider` VALUES ('604', 'ukmail.org');
INSERT INTO `free_email_provider` VALUES ('605', 'unbounded.com');
INSERT INTO `free_email_provider` VALUES ('606', 'uni.de');
INSERT INTO `free_email_provider` VALUES ('607', 'mailto.de');
INSERT INTO `free_email_provider` VALUES ('608', 'unomail.com');
INSERT INTO `free_email_provider` VALUES ('609', 'uymail.com');
INSERT INTO `free_email_provider` VALUES ('610', 'usaaccess.net');
INSERT INTO `free_email_provider` VALUES ('611', 'mail.usa.com');
INSERT INTO `free_email_provider` VALUES ('612', 'freedom.usa.com');
INSERT INTO `free_email_provider` VALUES ('613', 'public.usa.com');
INSERT INTO `free_email_provider` VALUES ('614', 'usermail.com');
INSERT INTO `free_email_provider` VALUES ('615', 'vahoo.com');
INSERT INTO `free_email_provider` VALUES ('616', 'vcmail.com');
INSERT INTO `free_email_provider` VALUES ('617', 'vr9.com');
INSERT INTO `free_email_provider` VALUES ('618', 'virtualactive.com');
INSERT INTO `free_email_provider` VALUES ('619', 'vjmail.com');
INSERT INTO `free_email_provider` VALUES ('620', 'visitweb.com');
INSERT INTO `free_email_provider` VALUES ('621', 'visto.com');
INSERT INTO `free_email_provider` VALUES ('622', 'vlmail.com');
INSERT INTO `free_email_provider` VALUES ('623', 'votenet.com');
INSERT INTO `free_email_provider` VALUES ('624', 'wbdet.com');
INSERT INTO `free_email_provider` VALUES ('625', 'webinbox.com');
INSERT INTO `free_email_provider` VALUES ('626', 'korea.com');
INSERT INTO `free_email_provider` VALUES ('627', 'yourname.ddns.org');
INSERT INTO `free_email_provider` VALUES ('628', 'weekmail.com');
INSERT INTO `free_email_provider` VALUES ('629', 'wickedmail.com');
INSERT INTO `free_email_provider` VALUES ('630', 'windrivers.net');
INSERT INTO `free_email_provider` VALUES ('631', 'wiz.cc');
INSERT INTO `free_email_provider` VALUES ('632', 'gee-wiz.com');
INSERT INTO `free_email_provider` VALUES ('633', 'wkbwmail.com');
INSERT INTO `free_email_provider` VALUES ('634', 'email.women.com');
INSERT INTO `free_email_provider` VALUES ('635', 'wonder-net.com');
INSERT INTO `free_email_provider` VALUES ('636', 'worldmailer.com');
INSERT INTO `free_email_provider` VALUES ('637', 'wowmail.com');
INSERT INTO `free_email_provider` VALUES ('638', 'wptamail.com');
INSERT INTO `free_email_provider` VALUES ('639', 'wtvhmail.com');
INSERT INTO `free_email_provider` VALUES ('640', 'www2000.net');
INSERT INTO `free_email_provider` VALUES ('641', 'x-networks.net');
INSERT INTO `free_email_provider` VALUES ('642', 'xmastime.com');
INSERT INTO `free_email_provider` VALUES ('643', 'xoommail.com');
INSERT INTO `free_email_provider` VALUES ('644', 'xpressmail.zzn.com');
INSERT INTO `free_email_provider` VALUES ('645', 'yahoo.com');
INSERT INTO `free_email_provider` VALUES ('646', 'yawmail.com');
INSERT INTO `free_email_provider` VALUES ('647', 'yclub.com');
INSERT INTO `free_email_provider` VALUES ('648', 'yehaa.com');
INSERT INTO `free_email_provider` VALUES ('649', 'yesbox.net');
INSERT INTO `free_email_provider` VALUES ('650', 'ynnmail.com');
INSERT INTO `free_email_provider` VALUES ('651', 'yogotemail.com');
INSERT INTO `free_email_provider` VALUES ('652', 'youpy.com');
INSERT INTO `free_email_provider` VALUES ('653', 'youvegotmail.net');
INSERT INTO `free_email_provider` VALUES ('654', 'yoursubdomain.zzn.com');
INSERT INTO `free_email_provider` VALUES ('655', 'zcities.com');
INSERT INTO `free_email_provider` VALUES ('656', 'zdnetmail.com');
INSERT INTO `free_email_provider` VALUES ('657', 'zionweb.org');
INSERT INTO `free_email_provider` VALUES ('658', 'zuzzurello.com');
INSERT INTO `free_email_provider` VALUES ('659', 'littleblueroom.com');
INSERT INTO `free_email_provider` VALUES ('660', 'hhdevel.com');
INSERT INTO `free_email_provider` VALUES ('661', 'hotpop.com');
INSERT INTO `free_email_provider` VALUES ('662', 'toughguy.net');
INSERT INTO `free_email_provider` VALUES ('663', 'punkass.com');
INSERT INTO `free_email_provider` VALUES ('664', 'bonbon.net');
INSERT INTO `free_email_provider` VALUES ('665', 'phreaker.net');
INSERT INTO `free_email_provider` VALUES ('666', 'sexmagnet.com');
INSERT INTO `free_email_provider` VALUES ('667', 'gamebox.net');
INSERT INTO `free_email_provider` VALUES ('668', 'mailcc.com');
INSERT INTO `free_email_provider` VALUES ('669', 'telebot.com');
INSERT INTO `free_email_provider` VALUES ('670', 'tritium.net');
INSERT INTO `free_email_provider` VALUES ('671', 'visualcities.com');
INSERT INTO `free_email_provider` VALUES ('672', 'airforce.net');
INSERT INTO `free_email_provider` VALUES ('673', 'army.net');
INSERT INTO `free_email_provider` VALUES ('674', 'sentrismail.com');
INSERT INTO `free_email_provider` VALUES ('675', 'gonavy.net');
INSERT INTO `free_email_provider` VALUES ('676', 'ifoward.com');
INSERT INTO `free_email_provider` VALUES ('677', 'themillionare.net');
INSERT INTO `free_email_provider` VALUES ('678', 'navy.org');
INSERT INTO `free_email_provider` VALUES ('679', 'milmail.com');
INSERT INTO `free_email_provider` VALUES ('680', 'upf.org');
INSERT INTO `free_email_provider` VALUES ('681', 'usma.net');
INSERT INTO `free_email_provider` VALUES ('682', 'usmc.net');
INSERT INTO `free_email_provider` VALUES ('683', 'wrestlingpages.com');
INSERT INTO `free_email_provider` VALUES ('684', 'talkcity.com');
INSERT INTO `free_email_provider` VALUES ('685', 'uswestmail.net');
INSERT INTO `free_email_provider` VALUES ('686', 'zeeks.com');
INSERT INTO `free_email_provider` VALUES ('687', 'geek.com');
INSERT INTO `free_email_provider` VALUES ('688', 'gamegeek.com');
INSERT INTO `free_email_provider` VALUES ('689', 'peopleweb.com');
INSERT INTO `free_email_provider` VALUES ('690', 'mailstart.com');
INSERT INTO `free_email_provider` VALUES ('691', 'mailstartplus.com');
INSERT INTO `free_email_provider` VALUES ('692', 'wowgirl.com');
INSERT INTO `free_email_provider` VALUES ('693', 'mail1st.com');
INSERT INTO `free_email_provider` VALUES ('694', 'gh2000.com');
INSERT INTO `free_email_provider` VALUES ('695', 'asiafind.com');
INSERT INTO `free_email_provider` VALUES ('696', 'cuemail.com');
INSERT INTO `free_email_provider` VALUES ('697', 'gamespotmail.com');
INSERT INTO `free_email_provider` VALUES ('698', 'mailboom.com');
INSERT INTO `free_email_provider` VALUES ('699', 'money.net');
INSERT INTO `free_email_provider` VALUES ('700', 'nafe.com');
INSERT INTO `free_email_provider` VALUES ('701', 'techpointer.com');
INSERT INTO `free_email_provider` VALUES ('702', 'aaamail.zzn.com');
INSERT INTO `free_email_provider` VALUES ('703', 'wongfaye.com');
INSERT INTO `free_email_provider` VALUES ('704', 'post.cz');
INSERT INTO `free_email_provider` VALUES ('705', 'emumail.com');
INSERT INTO `free_email_provider` VALUES ('706', 'gmail.com');
INSERT INTO `free_email_provider` VALUES ('707', 'rambler.ru');
INSERT INTO `free_email_provider` VALUES ('708', 'mail.rambler.ru');
INSERT INTO `free_email_provider` VALUES ('709', 'mail.yandex.ru');
INSERT INTO `free_email_provider` VALUES ('710', 'mail.com');
INSERT INTO `free_email_provider` VALUES ('711', 'mail.yahoo.com');
INSERT INTO `free_email_provider` VALUES ('712', 'pochta.ru');
INSERT INTO `free_email_provider` VALUES ('713', 'mail.bigmir.net');
INSERT INTO `free_email_provider` VALUES ('714', 'freemail.ru');
INSERT INTO `free_email_provider` VALUES ('715', 'mail.ru');
INSERT INTO `free_email_provider` VALUES ('716', 'mail.i.ua');
INSERT INTO `free_email_provider` VALUES ('717', 'bigmir.net');
INSERT INTO `free_email_provider` VALUES ('718', 'yandex.ru');
INSERT INTO `free_email_provider` VALUES ('719', 'ukr.net');
INSERT INTO `free_email_provider` VALUES ('720', '123.com');
INSERT INTO `free_email_provider` VALUES ('721', '123box.net');
INSERT INTO `free_email_provider` VALUES ('722', '123mail.cl');
INSERT INTO `free_email_provider` VALUES ('723', '123qwe.co.uk');
INSERT INTO `free_email_provider` VALUES ('724', '150ml.com');
INSERT INTO `free_email_provider` VALUES ('725', '15meg4free.com');
INSERT INTO `free_email_provider` VALUES ('726', '163.com');
INSERT INTO `free_email_provider` VALUES ('727', '1coolplace.com');
INSERT INTO `free_email_provider` VALUES ('728', '1freeemail.com');
INSERT INTO `free_email_provider` VALUES ('729', '1funplace.com');
INSERT INTO `free_email_provider` VALUES ('730', '1internetdrive.com');
INSERT INTO `free_email_provider` VALUES ('731', '1mail.net');
INSERT INTO `free_email_provider` VALUES ('732', '1me.net');
INSERT INTO `free_email_provider` VALUES ('733', '1mum.com');
INSERT INTO `free_email_provider` VALUES ('734', '1musicrow.com');
INSERT INTO `free_email_provider` VALUES ('735', '1netdrive.com');
INSERT INTO `free_email_provider` VALUES ('736', '1nsyncfan.com');
INSERT INTO `free_email_provider` VALUES ('737', '1under.com');
INSERT INTO `free_email_provider` VALUES ('738', '1webave.com');
INSERT INTO `free_email_provider` VALUES ('739', '1webhighway.com');
INSERT INTO `free_email_provider` VALUES ('740', '212.com');
INSERT INTO `free_email_provider` VALUES ('741', '24horas.com');
INSERT INTO `free_email_provider` VALUES ('742', '2911.net');
INSERT INTO `free_email_provider` VALUES ('743', '2d2i.com');
INSERT INTO `free_email_provider` VALUES ('744', '2die4.com');
INSERT INTO `free_email_provider` VALUES ('745', '3000.it');
INSERT INTO `free_email_provider` VALUES ('746', '37.com');
INSERT INTO `free_email_provider` VALUES ('747', '3ammagazine.com');
INSERT INTO `free_email_provider` VALUES ('748', '3email.com');
INSERT INTO `free_email_provider` VALUES ('749', '3xl.net');
INSERT INTO `free_email_provider` VALUES ('750', '444.net');
INSERT INTO `free_email_provider` VALUES ('751', '4email.com');
INSERT INTO `free_email_provider` VALUES ('752', '4email.net');
INSERT INTO `free_email_provider` VALUES ('753', '4mg.com');
INSERT INTO `free_email_provider` VALUES ('754', '4newyork.com');
INSERT INTO `free_email_provider` VALUES ('755', '4x4man.com');
INSERT INTO `free_email_provider` VALUES ('756', '5iron.com');
INSERT INTO `free_email_provider` VALUES ('757', '88.am');
INSERT INTO `free_email_provider` VALUES ('758', '8848.net');
INSERT INTO `free_email_provider` VALUES ('759', 'aaronkwok.net');
INSERT INTO `free_email_provider` VALUES ('760', 'abbeyroadlondon.co.uk');
INSERT INTO `free_email_provider` VALUES ('761', 'abdulnour.com');
INSERT INTO `free_email_provider` VALUES ('762', 'aberystwyth.com');
INSERT INTO `free_email_provider` VALUES ('763', 'about.com');
INSERT INTO `free_email_provider` VALUES ('764', 'academycougars.com');
INSERT INTO `free_email_provider` VALUES ('765', 'acceso.or.cr');
INSERT INTO `free_email_provider` VALUES ('766', 'access4less.net');
INSERT INTO `free_email_provider` VALUES ('767', 'accessgcc.com');
INSERT INTO `free_email_provider` VALUES ('768', 'ace-of-base.com');
INSERT INTO `free_email_provider` VALUES ('769', 'acmemail.net');
INSERT INTO `free_email_provider` VALUES ('770', 'acninc.net');
INSERT INTO `free_email_provider` VALUES ('771', 'adexec.com');
INSERT INTO `free_email_provider` VALUES ('772', 'adios.net');
INSERT INTO `free_email_provider` VALUES ('773', 'ados.fr');
INSERT INTO `free_email_provider` VALUES ('774', 'aeiou.pt');
INSERT INTO `free_email_provider` VALUES ('775', 'aemail4u.com');
INSERT INTO `free_email_provider` VALUES ('776', 'aeneasmail.com');
INSERT INTO `free_email_provider` VALUES ('777', 'afreeinternet.com');
INSERT INTO `free_email_provider` VALUES ('778', 'africamail.com');
INSERT INTO `free_email_provider` VALUES ('779', 'agoodmail.com');
INSERT INTO `free_email_provider` VALUES ('780', 'ahaa.dk');
INSERT INTO `free_email_provider` VALUES ('781', 'aichi.com');
INSERT INTO `free_email_provider` VALUES ('782', 'airpost.net');
INSERT INTO `free_email_provider` VALUES ('783', 'ajacied.com');
INSERT INTO `free_email_provider` VALUES ('784', 'ak47.hu');
INSERT INTO `free_email_provider` VALUES ('785', 'aknet.kg');
INSERT INTO `free_email_provider` VALUES ('786', 'albawaba.com');
INSERT INTO `free_email_provider` VALUES ('787', 'alex4all.com');
INSERT INTO `free_email_provider` VALUES ('788', 'alexandria.cc');
INSERT INTO `free_email_provider` VALUES ('789', 'algeria.com');
INSERT INTO `free_email_provider` VALUES ('790', 'alhilal.net');
INSERT INTO `free_email_provider` VALUES ('791', 'alibaba.com');
INSERT INTO `free_email_provider` VALUES ('792', 'alive.cz');
INSERT INTO `free_email_provider` VALUES ('793', 'allmail.net');
INSERT INTO `free_email_provider` VALUES ('794', 'allsaintsfan.com');
INSERT INTO `free_email_provider` VALUES ('795', 'alskens.dk');
INSERT INTO `free_email_provider` VALUES ('796', 'altavista.com');
INSERT INTO `free_email_provider` VALUES ('797', 'altavista.se');
INSERT INTO `free_email_provider` VALUES ('798', 'alternativagratis.com');
INSERT INTO `free_email_provider` VALUES ('799', 'alumnidirector.com');
INSERT INTO `free_email_provider` VALUES ('800', 'alvilag.hu');
INSERT INTO `free_email_provider` VALUES ('801', 'amele.com');
INSERT INTO `free_email_provider` VALUES ('802', 'america.hm');
INSERT INTO `free_email_provider` VALUES ('803', 'amnetsal.com');
INSERT INTO `free_email_provider` VALUES ('804', 'ananzi.co.za');
INSERT INTO `free_email_provider` VALUES ('805', 'andylau.net');
INSERT INTO `free_email_provider` VALUES ('806', 'anfmail.com');
INSERT INTO `free_email_provider` VALUES ('807', 'animalwoman.net');
INSERT INTO `free_email_provider` VALUES ('808', 'anjungcafe.com');
INSERT INTO `free_email_provider` VALUES ('809', 'another.com');
INSERT INTO `free_email_provider` VALUES ('810', 'antisocial.com');
INSERT INTO `free_email_provider` VALUES ('811', 'antongijsen.com');
INSERT INTO `free_email_provider` VALUES ('812', 'antwerpen.com');
INSERT INTO `free_email_provider` VALUES ('813', 'anymoment.com');
INSERT INTO `free_email_provider` VALUES ('814', 'anytimenow.com');
INSERT INTO `free_email_provider` VALUES ('815', 'apollo.lv');
INSERT INTO `free_email_provider` VALUES ('816', 'approvers.net');
INSERT INTO `free_email_provider` VALUES ('817', 'arabia.com');
INSERT INTO `free_email_provider` VALUES ('818', 'arabtop.net');
INSERT INTO `free_email_provider` VALUES ('819', 'archaeologist.com');
INSERT INTO `free_email_provider` VALUES ('820', 'arcor.de');
INSERT INTO `free_email_provider` VALUES ('821', 'arcotronics.bg');
INSERT INTO `free_email_provider` VALUES ('822', 'argentina.com');
INSERT INTO `free_email_provider` VALUES ('823', 'arnet.com.ar');
INSERT INTO `free_email_provider` VALUES ('824', 'artlover.com');
INSERT INTO `free_email_provider` VALUES ('825', 'artlover.com.au');
INSERT INTO `free_email_provider` VALUES ('826', 'as-if.com');
INSERT INTO `free_email_provider` VALUES ('827', 'asia-links.com');
INSERT INTO `free_email_provider` VALUES ('828', 'asia.com');
INSERT INTO `free_email_provider` VALUES ('829', 'asianavenue.com');
INSERT INTO `free_email_provider` VALUES ('830', 'assala.com');
INSERT INTO `free_email_provider` VALUES ('831', 'assamesemail.com');
INSERT INTO `free_email_provider` VALUES ('832', 'astroboymail.com');
INSERT INTO `free_email_provider` VALUES ('833', 'astrolover.com');
INSERT INTO `free_email_provider` VALUES ('834', 'asurfer.com');
INSERT INTO `free_email_provider` VALUES ('835', 'athenachu.net');
INSERT INTO `free_email_provider` VALUES ('836', 'atina.cl');
INSERT INTO `free_email_provider` VALUES ('837', 'atl.lv');
INSERT INTO `free_email_provider` VALUES ('838', 'atlaswebmail.com');
INSERT INTO `free_email_provider` VALUES ('839', 'atozasia.com');
INSERT INTO `free_email_provider` VALUES ('840', 'au.ru');
INSERT INTO `free_email_provider` VALUES ('841', 'australiamail.com');
INSERT INTO `free_email_provider` VALUES ('842', 'austrosearch.net');
INSERT INTO `free_email_provider` VALUES ('843', 'autoescuelanerja.com');
INSERT INTO `free_email_provider` VALUES ('844', 'avh.hu');
INSERT INTO `free_email_provider` VALUES ('845', 'ayna.com');
INSERT INTO `free_email_provider` VALUES ('846', 'azimiweb.com');
INSERT INTO `free_email_provider` VALUES ('847', 'bachelorboy.com');
INSERT INTO `free_email_provider` VALUES ('848', 'bachelorgal.com');
INSERT INTO `free_email_provider` VALUES ('849', 'backstreet-boys.com');
INSERT INTO `free_email_provider` VALUES ('850', 'backstreetboysclub.com');
INSERT INTO `free_email_provider` VALUES ('851', 'bagherpour.com');
INSERT INTO `free_email_provider` VALUES ('852', 'baptistmail.com');
INSERT INTO `free_email_provider` VALUES ('853', 'baptized.com');
INSERT INTO `free_email_provider` VALUES ('854', 'barcelona.com');
INSERT INTO `free_email_provider` VALUES ('855', 'batuta.net');
INSERT INTO `free_email_provider` VALUES ('856', 'baudoinconsulting.com');
INSERT INTO `free_email_provider` VALUES ('857', 'bcvibes.com');
INSERT INTO `free_email_provider` VALUES ('858', 'beeebank.com');
INSERT INTO `free_email_provider` VALUES ('859', 'beenhad.com');
INSERT INTO `free_email_provider` VALUES ('860', 'beep.ru');
INSERT INTO `free_email_provider` VALUES ('861', 'beethoven.com');
INSERT INTO `free_email_provider` VALUES ('862', 'belice.com');
INSERT INTO `free_email_provider` VALUES ('863', 'belizehome.com');
INSERT INTO `free_email_provider` VALUES ('864', 'berlin.com');
INSERT INTO `free_email_provider` VALUES ('865', 'berlin.de');
INSERT INTO `free_email_provider` VALUES ('866', 'berlinexpo.de');
INSERT INTO `free_email_provider` VALUES ('867', 'bestmail.us');
INSERT INTO `free_email_provider` VALUES ('868', 'bharatmail.com');
INSERT INTO `free_email_provider` VALUES ('869', 'bigblue.net.au');
INSERT INTO `free_email_provider` VALUES ('870', 'bigboab.com');
INSERT INTO `free_email_provider` VALUES ('871', 'bigfoot.de');
INSERT INTO `free_email_provider` VALUES ('872', 'bigger.com');
INSERT INTO `free_email_provider` VALUES ('873', 'bigmailbox.com');
INSERT INTO `free_email_provider` VALUES ('874', 'bigramp.com');
INSERT INTO `free_email_provider` VALUES ('875', 'bikemechanics.com');
INSERT INTO `free_email_provider` VALUES ('876', 'bikeracers.net');
INSERT INTO `free_email_provider` VALUES ('877', 'bikerider.com');
INSERT INTO `free_email_provider` VALUES ('878', 'bimla.net');
INSERT INTO `free_email_provider` VALUES ('879', 'birdowner.net');
INSERT INTO `free_email_provider` VALUES ('880', 'bitpage.net');
INSERT INTO `free_email_provider` VALUES ('881', 'bizhosting.com');
INSERT INTO `free_email_provider` VALUES ('882', 'blackburnmail.com');
INSERT INTO `free_email_provider` VALUES ('883', 'blackplanet.com');
INSERT INTO `free_email_provider` VALUES ('884', 'bluehyppo.com');
INSERT INTO `free_email_provider` VALUES ('885', 'bluemail.ch');
INSERT INTO `free_email_provider` VALUES ('886', 'bluemail.dk');
INSERT INTO `free_email_provider` VALUES ('887', 'blushmail.com');
INSERT INTO `free_email_provider` VALUES ('888', 'bmlsports.net');
INSERT INTO `free_email_provider` VALUES ('889', 'boardermail.com');
INSERT INTO `free_email_provider` VALUES ('890', 'bol.com.br');
INSERT INTO `free_email_provider` VALUES ('891', 'bolando.com');
INSERT INTO `free_email_provider` VALUES ('892', 'bollywoodz.com');
INSERT INTO `free_email_provider` VALUES ('893', 'bolt.com');
INSERT INTO `free_email_provider` VALUES ('894', 'boltonfans.com');
INSERT INTO `free_email_provider` VALUES ('895', 'boom.com');
INSERT INTO `free_email_provider` VALUES ('896', 'bootmail.com');
INSERT INTO `free_email_provider` VALUES ('897', 'bornnaked.com');
INSERT INTO `free_email_provider` VALUES ('898', 'bostonoffice.com');
INSERT INTO `free_email_provider` VALUES ('899', 'bounce.net');
INSERT INTO `free_email_provider` VALUES ('900', 'box.az');
INSERT INTO `free_email_provider` VALUES ('901', 'boxbg.com');
INSERT INTO `free_email_provider` VALUES ('902', 'boxemail.com');
INSERT INTO `free_email_provider` VALUES ('903', 'boxfrog.com');
INSERT INTO `free_email_provider` VALUES ('904', 'boyzoneclub.com');
INSERT INTO `free_email_provider` VALUES ('905', 'bradfordfans.com');
INSERT INTO `free_email_provider` VALUES ('906', 'brasilia.net');
INSERT INTO `free_email_provider` VALUES ('907', 'brazilmail.com.br');
INSERT INTO `free_email_provider` VALUES ('908', 'breathe.com');
INSERT INTO `free_email_provider` VALUES ('909', 'brfree.com.br');
INSERT INTO `free_email_provider` VALUES ('910', 'britneyclub.com');
INSERT INTO `free_email_provider` VALUES ('911', 'brittonsign.com');
INSERT INTO `free_email_provider` VALUES ('912', 'btopenworld.co.uk');
INSERT INTO `free_email_provider` VALUES ('913', 'bullsfan.com');
INSERT INTO `free_email_provider` VALUES ('914', 'bullsgame.com');
INSERT INTO `free_email_provider` VALUES ('915', 'bumerang.ro');
INSERT INTO `free_email_provider` VALUES ('916', 'buryfans.com');
INSERT INTO `free_email_provider` VALUES ('917', 'business-man.com');
INSERT INTO `free_email_provider` VALUES ('918', 'businessman.net');
INSERT INTO `free_email_provider` VALUES ('919', 'bvimailbox.com');
INSERT INTO `free_email_provider` VALUES ('920', 'c2i.net');
INSERT INTO `free_email_provider` VALUES ('921', 'c3.hu');
INSERT INTO `free_email_provider` VALUES ('922', 'c4.com');
INSERT INTO `free_email_provider` VALUES ('923', 'caere.it');
INSERT INTO `free_email_provider` VALUES ('924', 'cairomail.com');
INSERT INTO `free_email_provider` VALUES ('925', 'callnetuk.com');
INSERT INTO `free_email_provider` VALUES ('926', 'caltanet.it');
INSERT INTO `free_email_provider` VALUES ('927', 'camidge.com');
INSERT INTO `free_email_provider` VALUES ('928', 'canada-11.com');
INSERT INTO `free_email_provider` VALUES ('929', 'canwetalk.com');
INSERT INTO `free_email_provider` VALUES ('930', 'caramail.com');
INSERT INTO `free_email_provider` VALUES ('931', 'care2.com');
INSERT INTO `free_email_provider` VALUES ('932', 'carioca.net');
INSERT INTO `free_email_provider` VALUES ('933', 'cartestraina.ro');
INSERT INTO `free_email_provider` VALUES ('934', 'catcha.com');
INSERT INTO `free_email_provider` VALUES ('935', 'catlover.com');
INSERT INTO `free_email_provider` VALUES ('936', 'cd2.com');
INSERT INTO `free_email_provider` VALUES ('937', 'celineclub.com');
INSERT INTO `free_email_provider` VALUES ('938', 'centoper.it');
INSERT INTO `free_email_provider` VALUES ('939', 'centralpets.com');
INSERT INTO `free_email_provider` VALUES ('940', 'centrum.cz');
INSERT INTO `free_email_provider` VALUES ('941', 'centrum.sk');
INSERT INTO `free_email_provider` VALUES ('942', 'cgac.es');
INSERT INTO `free_email_provider` VALUES ('943', 'chaiyomail.com');
INSERT INTO `free_email_provider` VALUES ('944', 'chance2mail.com');
INSERT INTO `free_email_provider` VALUES ('945', 'chandrasekar.net');
INSERT INTO `free_email_provider` VALUES ('946', 'chat.ru');
INSERT INTO `free_email_provider` VALUES ('947', 'chattown.com');
INSERT INTO `free_email_provider` VALUES ('948', 'chauhanweb.com');
INSERT INTO `free_email_provider` VALUES ('949', 'check1check.com');
INSERT INTO `free_email_provider` VALUES ('950', 'chemist.com');
INSERT INTO `free_email_provider` VALUES ('951', 'chequemail.com');
INSERT INTO `free_email_provider` VALUES ('952', 'china.net.vg');
INSERT INTO `free_email_provider` VALUES ('953', 'chirk.com');
INSERT INTO `free_email_provider` VALUES ('954', 'chocaholic.com.au');
INSERT INTO `free_email_provider` VALUES ('955', 'cia-agent.com');
INSERT INTO `free_email_provider` VALUES ('956', 'cia.hu');
INSERT INTO `free_email_provider` VALUES ('957', 'ciaoweb.it');
INSERT INTO `free_email_provider` VALUES ('958', 'cicciociccio.com');
INSERT INTO `free_email_provider` VALUES ('959', 'city-of-bath.org');
INSERT INTO `free_email_provider` VALUES ('960', 'city-of-birmingham.com');
INSERT INTO `free_email_provider` VALUES ('961', 'city-of-brighton.org');
INSERT INTO `free_email_provider` VALUES ('962', 'city-of-cambridge.com');
INSERT INTO `free_email_provider` VALUES ('963', 'city-of-coventry.com');
INSERT INTO `free_email_provider` VALUES ('964', 'city-of-edinburgh.com');
INSERT INTO `free_email_provider` VALUES ('965', 'city-of-lichfield.com');
INSERT INTO `free_email_provider` VALUES ('966', 'city-of-lincoln.com');
INSERT INTO `free_email_provider` VALUES ('967', 'city-of-liverpool.com');
INSERT INTO `free_email_provider` VALUES ('968', 'city-of-manchester.com');
INSERT INTO `free_email_provider` VALUES ('969', 'city-of-nottingham.com');
INSERT INTO `free_email_provider` VALUES ('970', 'city-of-oxford.com');
INSERT INTO `free_email_provider` VALUES ('971', 'city-of-swansea.com');
INSERT INTO `free_email_provider` VALUES ('972', 'city-of-westminster.com');
INSERT INTO `free_email_provider` VALUES ('973', 'city-of-westminster.net');
INSERT INTO `free_email_provider` VALUES ('974', 'city-of-york.net');
INSERT INTO `free_email_provider` VALUES ('975', 'cityofcardiff.net');
INSERT INTO `free_email_provider` VALUES ('976', 'cityoflondon.org');
INSERT INTO `free_email_provider` VALUES ('977', 'classicmail.co.za');
INSERT INTO `free_email_provider` VALUES ('978', 'clerk.com');
INSERT INTO `free_email_provider` VALUES ('979', 'cliffhanger.com');
INSERT INTO `free_email_provider` VALUES ('980', 'close2you.net');
INSERT INTO `free_email_provider` VALUES ('981', 'club4x4.net');
INSERT INTO `free_email_provider` VALUES ('982', 'clubalfa.com');
INSERT INTO `free_email_provider` VALUES ('983', 'clubbers.net');
INSERT INTO `free_email_provider` VALUES ('984', 'clubducati.com');
INSERT INTO `free_email_provider` VALUES ('985', 'clubhonda.net');
INSERT INTO `free_email_provider` VALUES ('986', 'cluemail.com');
INSERT INTO `free_email_provider` VALUES ('987', 'coder.hu');
INSERT INTO `free_email_provider` VALUES ('988', 'coid.biz');
INSERT INTO `free_email_provider` VALUES ('989', 'columnist.com');
INSERT INTO `free_email_provider` VALUES ('990', 'comic.com');
INSERT INTO `free_email_provider` VALUES ('991', 'compuserve.com');
INSERT INTO `free_email_provider` VALUES ('992', 'computer-freak.com');
INSERT INTO `free_email_provider` VALUES ('993', 'computermail.net');
INSERT INTO `free_email_provider` VALUES ('994', 'conexcol.com');
INSERT INTO `free_email_provider` VALUES ('995', 'connect4free.net');
INSERT INTO `free_email_provider` VALUES ('996', 'connectbox.com');
INSERT INTO `free_email_provider` VALUES ('997', 'consultant.com');
INSERT INTO `free_email_provider` VALUES ('998', 'cookiemonster.com');
INSERT INTO `free_email_provider` VALUES ('999', 'cool.br');
INSERT INTO `free_email_provider` VALUES ('1000', 'coolgoose.ca');
INSERT INTO `free_email_provider` VALUES ('1001', 'coolgoose.com');
INSERT INTO `free_email_provider` VALUES ('1002', 'coolkiwi.com');
INSERT INTO `free_email_provider` VALUES ('1003', 'coollist.com');
INSERT INTO `free_email_provider` VALUES ('1004', 'coolmail.com');
INSERT INTO `free_email_provider` VALUES ('1005', 'coolmail.net');
INSERT INTO `free_email_provider` VALUES ('1006', 'coolsend.com');
INSERT INTO `free_email_provider` VALUES ('1007', 'cooooool.com');
INSERT INTO `free_email_provider` VALUES ('1008', 'cooperation.net');
INSERT INTO `free_email_provider` VALUES ('1009', 'cooperationtogo.net');
INSERT INTO `free_email_provider` VALUES ('1010', 'copacabana.com');
INSERT INTO `free_email_provider` VALUES ('1011', 'cornerpub.com');
INSERT INTO `free_email_provider` VALUES ('1012', 'corporatedirtbag.com');
INSERT INTO `free_email_provider` VALUES ('1013', 'correo.terra.com.gt');
INSERT INTO `free_email_provider` VALUES ('1014', 'cortinet.com');
INSERT INTO `free_email_provider` VALUES ('1015', 'cotas.net');
INSERT INTO `free_email_provider` VALUES ('1016', 'counsellor.com');
INSERT INTO `free_email_provider` VALUES ('1017', 'countrylover.com');
INSERT INTO `free_email_provider` VALUES ('1018', 'cracker.hu');
INSERT INTO `free_email_provider` VALUES ('1019', 'crazedanddazed.com');
INSERT INTO `free_email_provider` VALUES ('1020', 'crazysexycool.com');
INSERT INTO `free_email_provider` VALUES ('1021', 'critterpost.com');
INSERT INTO `free_email_provider` VALUES ('1022', 'croeso.com');
INSERT INTO `free_email_provider` VALUES ('1023', 'cry4helponline.com');
INSERT INTO `free_email_provider` VALUES ('1024', 'cs.com');
INSERT INTO `free_email_provider` VALUES ('1025', 'csinibaba.hu');
INSERT INTO `free_email_provider` VALUES ('1026', 'curio-city.com');
INSERT INTO `free_email_provider` VALUES ('1027', 'cute-girl.com');
INSERT INTO `free_email_provider` VALUES ('1028', 'cuteandcuddly.com');
INSERT INTO `free_email_provider` VALUES ('1029', 'cutey.com');
INSERT INTO `free_email_provider` VALUES ('1030', 'cww.de');
INSERT INTO `free_email_provider` VALUES ('1031', 'cyberbabies.com');
INSERT INTO `free_email_provider` VALUES ('1032', 'cyberforeplay.net');
INSERT INTO `free_email_provider` VALUES ('1033', 'cyberinbox.com');
INSERT INTO `free_email_provider` VALUES ('1034', 'cyberleports.com');
INSERT INTO `free_email_provider` VALUES ('1035', 'cybernet.it');
INSERT INTO `free_email_provider` VALUES ('1036', 'dabsol.net');
INSERT INTO `free_email_provider` VALUES ('1037', 'dadacasa.com');
INSERT INTO `free_email_provider` VALUES ('1038', 'dailypioneer.com');
INSERT INTO `free_email_provider` VALUES ('1039', 'dangerous-minds.com');
INSERT INTO `free_email_provider` VALUES ('1040', 'dansegulvet.com');
INSERT INTO `free_email_provider` VALUES ('1041', 'data54.com');
INSERT INTO `free_email_provider` VALUES ('1042', 'davegracey.com');
INSERT INTO `free_email_provider` VALUES ('1043', 'dazedandconfused.com');
INSERT INTO `free_email_provider` VALUES ('1044', 'dbzmail.com');
INSERT INTO `free_email_provider` VALUES ('1045', 'dcemail.com');
INSERT INTO `free_email_provider` VALUES ('1046', 'deadlymob.org');
INSERT INTO `free_email_provider` VALUES ('1047', 'deal-maker.com');
INSERT INTO `free_email_provider` VALUES ('1048', 'dearriba.com');
INSERT INTO `free_email_provider` VALUES ('1049', 'death-star.com');
INSERT INTO `free_email_provider` VALUES ('1050', 'deliveryman.com');
INSERT INTO `free_email_provider` VALUES ('1051', 'desertmail.com');
INSERT INTO `free_email_provider` VALUES ('1052', 'desilota.com');
INSERT INTO `free_email_provider` VALUES ('1053', 'deskpilot.com');
INSERT INTO `free_email_provider` VALUES ('1054', 'detik.com');
INSERT INTO `free_email_provider` VALUES ('1055', 'devotedcouples.com');
INSERT INTO `free_email_provider` VALUES ('1056', 'dfwatson.com');
INSERT INTO `free_email_provider` VALUES ('1057', 'di-ve.com');
INSERT INTO `free_email_provider` VALUES ('1058', 'diplomats.com');
INSERT INTO `free_email_provider` VALUES ('1059', 'dmailman.com');
INSERT INTO `free_email_provider` VALUES ('1060', 'dnsmadeeasy.com');
INSERT INTO `free_email_provider` VALUES ('1061', 'doctor.com');
INSERT INTO `free_email_provider` VALUES ('1062', 'doglover.com');
INSERT INTO `free_email_provider` VALUES ('1063', 'dogmail.co.uk');
INSERT INTO `free_email_provider` VALUES ('1064', 'dogsnob.net');
INSERT INTO `free_email_provider` VALUES ('1065', 'doneasy.com');
INSERT INTO `free_email_provider` VALUES ('1066', 'donjuan.com');
INSERT INTO `free_email_provider` VALUES ('1067', 'dontgotmail.com');
INSERT INTO `free_email_provider` VALUES ('1068', 'dontmesswithtexas.com');
INSERT INTO `free_email_provider` VALUES ('1069', 'dostmail.com');
INSERT INTO `free_email_provider` VALUES ('1070', 'dotcom.fr');
INSERT INTO `free_email_provider` VALUES ('1071', 'dott.it');
INSERT INTO `free_email_provider` VALUES ('1072', 'dplanet.ch');
INSERT INTO `free_email_provider` VALUES ('1073', 'dr.com');
INSERT INTO `free_email_provider` VALUES ('1074', 'dropzone.com');
INSERT INTO `free_email_provider` VALUES ('1075', 'dubaimail.com');
INSERT INTO `free_email_provider` VALUES ('1076', 'dublin.com');
INSERT INTO `free_email_provider` VALUES ('1077', 'dublin.ie');
INSERT INTO `free_email_provider` VALUES ('1078', 'dygo.com');
INSERT INTO `free_email_provider` VALUES ('1079', 'dynamitemail.com');
INSERT INTO `free_email_provider` VALUES ('1080', 'e-apollo.lv');
INSERT INTO `free_email_provider` VALUES ('1081', 'e-mail.dk');
INSERT INTO `free_email_provider` VALUES ('1082', 'e-mail.ru');
INSERT INTO `free_email_provider` VALUES ('1083', 'e-mailanywhere.com');
INSERT INTO `free_email_provider` VALUES ('1084', 'e-mails.ru');
INSERT INTO `free_email_provider` VALUES ('1085', 'e-tapaal.com');
INSERT INTO `free_email_provider` VALUES ('1086', 'earthalliance.com');
INSERT INTO `free_email_provider` VALUES ('1087', 'earthdome.com');
INSERT INTO `free_email_provider` VALUES ('1088', 'eastcoast.co.za');
INSERT INTO `free_email_provider` VALUES ('1089', 'ecbsolutions.net');
INSERT INTO `free_email_provider` VALUES ('1090', 'echina.com');
INSERT INTO `free_email_provider` VALUES ('1091', 'ednatx.com');
INSERT INTO `free_email_provider` VALUES ('1092', 'educacao.te.pt');
INSERT INTO `free_email_provider` VALUES ('1093', 'eircom.net');
INSERT INTO `free_email_provider` VALUES ('1094', 'elsitio.com');
INSERT INTO `free_email_provider` VALUES ('1095', 'elvis.com');
INSERT INTO `free_email_provider` VALUES ('1096', 'email-london.co.uk');
INSERT INTO `free_email_provider` VALUES ('1097', 'email.cz');
INSERT INTO `free_email_provider` VALUES ('1098', 'email.ee');
INSERT INTO `free_email_provider` VALUES ('1099', 'email.it');
INSERT INTO `free_email_provider` VALUES ('1100', 'email.nu');
INSERT INTO `free_email_provider` VALUES ('1101', 'email.ru');
INSERT INTO `free_email_provider` VALUES ('1102', 'email.si');
INSERT INTO `free_email_provider` VALUES ('1103', 'email2me.net');
INSERT INTO `free_email_provider` VALUES ('1104', 'emailacc.com');
INSERT INTO `free_email_provider` VALUES ('1105', 'emailaccount.com');
INSERT INTO `free_email_provider` VALUES ('1106', 'emailchoice.com');
INSERT INTO `free_email_provider` VALUES ('1107', 'emailcorner.net');
INSERT INTO `free_email_provider` VALUES ('1108', 'emailengine.net');
INSERT INTO `free_email_provider` VALUES ('1109', 'emailforyou.net');
INSERT INTO `free_email_provider` VALUES ('1110', 'emailgroups.net');
INSERT INTO `free_email_provider` VALUES ('1111', 'emailpinoy.com');
INSERT INTO `free_email_provider` VALUES ('1112', 'emailplanet.com');
INSERT INTO `free_email_provider` VALUES ('1113', 'emails.ru');
INSERT INTO `free_email_provider` VALUES ('1114', 'emailuser.net');
INSERT INTO `free_email_provider` VALUES ('1115', 'emailx.net');
INSERT INTO `free_email_provider` VALUES ('1116', 'ematic.com');
INSERT INTO `free_email_provider` VALUES ('1117', 'end-war.com');
INSERT INTO `free_email_provider` VALUES ('1118', 'enel.net');
INSERT INTO `free_email_provider` VALUES ('1119', 'engineer.com');
INSERT INTO `free_email_provider` VALUES ('1120', 'england.edu');
INSERT INTO `free_email_provider` VALUES ('1121', 'epatra.com');
INSERT INTO `free_email_provider` VALUES ('1122', 'epost.de');
INSERT INTO `free_email_provider` VALUES ('1123', 'eposta.hu');
INSERT INTO `free_email_provider` VALUES ('1124', 'eqqu.com');
INSERT INTO `free_email_provider` VALUES ('1125', 'eramail.co.za');
INSERT INTO `free_email_provider` VALUES ('1126', 'eresmas.com');
INSERT INTO `free_email_provider` VALUES ('1127', 'eriga.lv');
INSERT INTO `free_email_provider` VALUES ('1128', 'estranet.it');
INSERT INTO `free_email_provider` VALUES ('1129', 'europe.com');
INSERT INTO `free_email_provider` VALUES ('1130', 'euroseek.com');
INSERT INTO `free_email_provider` VALUES ('1131', 'every1.net');
INSERT INTO `free_email_provider` VALUES ('1132', 'everyday.com.kh');
INSERT INTO `free_email_provider` VALUES ('1133', 'everyone.net');
INSERT INTO `free_email_provider` VALUES ('1134', 'examnotes.net');
INSERT INTO `free_email_provider` VALUES ('1135', 'excite.co.jp');
INSERT INTO `free_email_provider` VALUES ('1136', 'excite.it');
INSERT INTO `free_email_provider` VALUES ('1137', 'execs.com');
INSERT INTO `free_email_provider` VALUES ('1138', 'expressasia.com');
INSERT INTO `free_email_provider` VALUES ('1139', 'extended.com');
INSERT INTO `free_email_provider` VALUES ('1140', 'eyou.com');
INSERT INTO `free_email_provider` VALUES ('1141', 'ezcybersearch.com');
INSERT INTO `free_email_provider` VALUES ('1142', 'ezmail.egine.com');
INSERT INTO `free_email_provider` VALUES ('1143', 'ezmail.ru');
INSERT INTO `free_email_provider` VALUES ('1144', 'ezrs.com');
INSERT INTO `free_email_provider` VALUES ('1145', 'f1fans.net');
INSERT INTO `free_email_provider` VALUES ('1146', 'fantasticmail.com');
INSERT INTO `free_email_provider` VALUES ('1147', 'faroweb.com');
INSERT INTO `free_email_provider` VALUES ('1148', 'fastem.com');
INSERT INTO `free_email_provider` VALUES ('1149', 'fastemail.us');
INSERT INTO `free_email_provider` VALUES ('1150', 'fastemailer.com');
INSERT INTO `free_email_provider` VALUES ('1151', 'fastimap.com');
INSERT INTO `free_email_provider` VALUES ('1152', 'fastmail.fm');
INSERT INTO `free_email_provider` VALUES ('1153', 'fastmailbox.net');
INSERT INTO `free_email_provider` VALUES ('1154', 'fastmessaging.com');
INSERT INTO `free_email_provider` VALUES ('1155', 'fatcock.net');
INSERT INTO `free_email_provider` VALUES ('1156', 'fathersrightsne.org');
INSERT INTO `free_email_provider` VALUES ('1157', 'fbi-agent.com');
INSERT INTO `free_email_provider` VALUES ('1158', 'fbi.hu');
INSERT INTO `free_email_provider` VALUES ('1159', 'federalcontractors.com');
INSERT INTO `free_email_provider` VALUES ('1160', 'femenino.com');
INSERT INTO `free_email_provider` VALUES ('1161', 'feyenoorder.com');
INSERT INTO `free_email_provider` VALUES ('1162', 'ffanet.com');
INSERT INTO `free_email_provider` VALUES ('1163', 'filipinolinks.com');
INSERT INTO `free_email_provider` VALUES ('1164', 'financemail.net');
INSERT INTO `free_email_provider` VALUES ('1165', 'financier.com');
INSERT INTO `free_email_provider` VALUES ('1166', 'findmail.com');
INSERT INTO `free_email_provider` VALUES ('1167', 'finebody.com');
INSERT INTO `free_email_provider` VALUES ('1168', 'fire-brigade.com');
INSERT INTO `free_email_provider` VALUES ('1169', 'fishburne.org');
INSERT INTO `free_email_provider` VALUES ('1170', 'flipcode.com');
INSERT INTO `free_email_provider` VALUES ('1171', 'fmail.co.uk');
INSERT INTO `free_email_provider` VALUES ('1172', 'fmailbox.com');
INSERT INTO `free_email_provider` VALUES ('1173', 'fmgirl.com');
INSERT INTO `free_email_provider` VALUES ('1174', 'fmguy.com');
INSERT INTO `free_email_provider` VALUES ('1175', 'fnbmail.co.za');
INSERT INTO `free_email_provider` VALUES ('1176', 'fnmail.com');
INSERT INTO `free_email_provider` VALUES ('1177', 'for-president.com');
INSERT INTO `free_email_provider` VALUES ('1178', 'forpresident.com');
INSERT INTO `free_email_provider` VALUES ('1179', 'fortuncity.com');
INSERT INTO `free_email_provider` VALUES ('1180', 'forum.dk');
INSERT INTO `free_email_provider` VALUES ('1181', 'free.com.pe');
INSERT INTO `free_email_provider` VALUES ('1182', 'free.fr');
INSERT INTO `free_email_provider` VALUES ('1183', 'freeaccess.nl');
INSERT INTO `free_email_provider` VALUES ('1184', 'freeandsingle.com');
INSERT INTO `free_email_provider` VALUES ('1185', 'freedomlover.com');
INSERT INTO `free_email_provider` VALUES ('1186', 'freegates.be');
INSERT INTO `free_email_provider` VALUES ('1187', 'freeghana.com');
INSERT INTO `free_email_provider` VALUES ('1188', 'freeler.nl');
INSERT INTO `free_email_provider` VALUES ('1189', 'freemail.de');
INSERT INTO `free_email_provider` VALUES ('1190', 'freemail.et');
INSERT INTO `free_email_provider` VALUES ('1191', 'freemail.gr');
INSERT INTO `free_email_provider` VALUES ('1192', 'freemail.hu');
INSERT INTO `free_email_provider` VALUES ('1193', 'freemail.it');
INSERT INTO `free_email_provider` VALUES ('1194', 'freemail.lt');
INSERT INTO `free_email_provider` VALUES ('1195', 'freemail.nl');
INSERT INTO `free_email_provider` VALUES ('1196', 'freenet.de');
INSERT INTO `free_email_provider` VALUES ('1197', 'freenet.kg');
INSERT INTO `free_email_provider` VALUES ('1198', 'freeola.com');
INSERT INTO `free_email_provider` VALUES ('1199', 'freeola.net');
INSERT INTO `free_email_provider` VALUES ('1200', 'freeserve.co.uk');
INSERT INTO `free_email_provider` VALUES ('1201', 'freestart.hu');
INSERT INTO `free_email_provider` VALUES ('1202', 'freesurf.fr');
INSERT INTO `free_email_provider` VALUES ('1203', 'freesurf.nl');
INSERT INTO `free_email_provider` VALUES ('1204', 'freeuk.com');
INSERT INTO `free_email_provider` VALUES ('1205', 'freeuk.net');
INSERT INTO `free_email_provider` VALUES ('1206', 'freeukisp.co.uk');
INSERT INTO `free_email_provider` VALUES ('1207', 'freeweb.org');
INSERT INTO `free_email_provider` VALUES ('1208', 'freewebemail.com');
INSERT INTO `free_email_provider` VALUES ('1209', 'freezone.co.uk');
INSERT INTO `free_email_provider` VALUES ('1210', 'friendsfan.com');
INSERT INTO `free_email_provider` VALUES ('1211', 'from-africa.com');
INSERT INTO `free_email_provider` VALUES ('1212', 'from-america.com');
INSERT INTO `free_email_provider` VALUES ('1213', 'from-argentina.com');
INSERT INTO `free_email_provider` VALUES ('1214', 'from-asia.com');
INSERT INTO `free_email_provider` VALUES ('1215', 'from-australia.com');
INSERT INTO `free_email_provider` VALUES ('1216', 'from-belgium.com');
INSERT INTO `free_email_provider` VALUES ('1217', 'from-brazil.com');
INSERT INTO `free_email_provider` VALUES ('1218', 'from-canada.com');
INSERT INTO `free_email_provider` VALUES ('1219', 'from-china.net');
INSERT INTO `free_email_provider` VALUES ('1220', 'from-england.com');
INSERT INTO `free_email_provider` VALUES ('1221', 'from-europe.com');
INSERT INTO `free_email_provider` VALUES ('1222', 'from-france.net');
INSERT INTO `free_email_provider` VALUES ('1223', 'from-germany.net');
INSERT INTO `free_email_provider` VALUES ('1224', 'from-holland.com');
INSERT INTO `free_email_provider` VALUES ('1225', 'from-israel.com');
INSERT INTO `free_email_provider` VALUES ('1226', 'from-italy.net');
INSERT INTO `free_email_provider` VALUES ('1227', 'from-japan.net');
INSERT INTO `free_email_provider` VALUES ('1228', 'from-korea.com');
INSERT INTO `free_email_provider` VALUES ('1229', 'from-mexico.com');
INSERT INTO `free_email_provider` VALUES ('1230', 'from-outerspace.com');
INSERT INTO `free_email_provider` VALUES ('1231', 'from-russia.com');
INSERT INTO `free_email_provider` VALUES ('1232', 'from-spain.net');
INSERT INTO `free_email_provider` VALUES ('1233', 'fromalabama.com');
INSERT INTO `free_email_provider` VALUES ('1234', 'fromalaska.com');
INSERT INTO `free_email_provider` VALUES ('1235', 'fromarizona.com');
INSERT INTO `free_email_provider` VALUES ('1236', 'fromarkansas.com');
INSERT INTO `free_email_provider` VALUES ('1237', 'fromcalifornia.com');
INSERT INTO `free_email_provider` VALUES ('1238', 'fromcolorado.com');
INSERT INTO `free_email_provider` VALUES ('1239', 'fromconnecticut.com');
INSERT INTO `free_email_provider` VALUES ('1240', 'fromdelaware.com');
INSERT INTO `free_email_provider` VALUES ('1241', 'fromflorida.net');
INSERT INTO `free_email_provider` VALUES ('1242', 'fromgeorgia.com');
INSERT INTO `free_email_provider` VALUES ('1243', 'fromhawaii.net');
INSERT INTO `free_email_provider` VALUES ('1244', 'fromidaho.com');
INSERT INTO `free_email_provider` VALUES ('1245', 'fromillinois.com');
INSERT INTO `free_email_provider` VALUES ('1246', 'fromindiana.com');
INSERT INTO `free_email_provider` VALUES ('1247', 'fromiowa.com');
INSERT INTO `free_email_provider` VALUES ('1248', 'fromjupiter.com');
INSERT INTO `free_email_provider` VALUES ('1249', 'fromkansas.com');
INSERT INTO `free_email_provider` VALUES ('1250', 'fromkentucky.com');
INSERT INTO `free_email_provider` VALUES ('1251', 'fromlouisiana.com');
INSERT INTO `free_email_provider` VALUES ('1252', 'frommaine.net');
INSERT INTO `free_email_provider` VALUES ('1253', 'frommaryland.com');
INSERT INTO `free_email_provider` VALUES ('1254', 'frommassachusetts.com');
INSERT INTO `free_email_provider` VALUES ('1255', 'frommiami.com');
INSERT INTO `free_email_provider` VALUES ('1256', 'frommichigan.com');
INSERT INTO `free_email_provider` VALUES ('1257', 'fromminnesota.com');
INSERT INTO `free_email_provider` VALUES ('1258', 'frommississippi.com');
INSERT INTO `free_email_provider` VALUES ('1259', 'frommissouri.com');
INSERT INTO `free_email_provider` VALUES ('1260', 'frommontana.com');
INSERT INTO `free_email_provider` VALUES ('1261', 'fromnebraska.com');
INSERT INTO `free_email_provider` VALUES ('1262', 'fromnevada.com');
INSERT INTO `free_email_provider` VALUES ('1263', 'fromnewhampshire.com');
INSERT INTO `free_email_provider` VALUES ('1264', 'fromnewjersey.com');
INSERT INTO `free_email_provider` VALUES ('1265', 'fromnewmexico.com');
INSERT INTO `free_email_provider` VALUES ('1266', 'fromnewyork.net');
INSERT INTO `free_email_provider` VALUES ('1267', 'fromnorthcarolina.com');
INSERT INTO `free_email_provider` VALUES ('1268', 'fromnorthdakota.com');
INSERT INTO `free_email_provider` VALUES ('1269', 'fromohio.com');
INSERT INTO `free_email_provider` VALUES ('1270', 'fromoklahoma.com');
INSERT INTO `free_email_provider` VALUES ('1271', 'fromoregon.net');
INSERT INTO `free_email_provider` VALUES ('1272', 'frompennsylvania.com');
INSERT INTO `free_email_provider` VALUES ('1273', 'fromrhodeisland.com');
INSERT INTO `free_email_provider` VALUES ('1274', 'fromru.com');
INSERT INTO `free_email_provider` VALUES ('1275', 'fromsouthcarolina.com');
INSERT INTO `free_email_provider` VALUES ('1276', 'fromsouthdakota.com');
INSERT INTO `free_email_provider` VALUES ('1277', 'fromtennessee.com');
INSERT INTO `free_email_provider` VALUES ('1278', 'fromtexas.com');
INSERT INTO `free_email_provider` VALUES ('1279', 'fromthestates.com');
INSERT INTO `free_email_provider` VALUES ('1280', 'fromutah.com');
INSERT INTO `free_email_provider` VALUES ('1281', 'fromvermont.com');
INSERT INTO `free_email_provider` VALUES ('1282', 'fromvirginia.com');
INSERT INTO `free_email_provider` VALUES ('1283', 'fromwashington.com');
INSERT INTO `free_email_provider` VALUES ('1284', 'fromwashingtondc.com');
INSERT INTO `free_email_provider` VALUES ('1285', 'fromwestvirginia.com');
INSERT INTO `free_email_provider` VALUES ('1286', 'fromwisconsin.com');
INSERT INTO `free_email_provider` VALUES ('1287', 'fromwyoming.com');
INSERT INTO `free_email_provider` VALUES ('1288', 'front.ru');
INSERT INTO `free_email_provider` VALUES ('1289', 'frostbyte.uk.net');
INSERT INTO `free_email_provider` VALUES ('1290', 'fsmail.net');
INSERT INTO `free_email_provider` VALUES ('1291', 'ftml.net');
INSERT INTO `free_email_provider` VALUES ('1292', 'fuorissimo.com');
INSERT INTO `free_email_provider` VALUES ('1293', 'furnitureprovider.com');
INSERT INTO `free_email_provider` VALUES ('1294', 'fut.es');
INSERT INTO `free_email_provider` VALUES ('1295', 'fxsmails.com');
INSERT INTO `free_email_provider` VALUES ('1296', 'galaxy5.com');
INSERT INTO `free_email_provider` VALUES ('1297', 'gardener.com');
INSERT INTO `free_email_provider` VALUES ('1298', 'gawab.com');
INSERT INTO `free_email_provider` VALUES ('1299', 'gaza.net');
INSERT INTO `free_email_provider` VALUES ('1300', 'gazeta.pl');
INSERT INTO `free_email_provider` VALUES ('1301', 'gazibooks.com');
INSERT INTO `free_email_provider` VALUES ('1302', 'geek.hu');
INSERT INTO `free_email_provider` VALUES ('1303', 'general-hospital.com');
INSERT INTO `free_email_provider` VALUES ('1304', 'geologist.com');
INSERT INTO `free_email_provider` VALUES ('1305', 'geopia.com');
INSERT INTO `free_email_provider` VALUES ('1306', 'giga4u.de');
INSERT INTO `free_email_provider` VALUES ('1307', 'givepeaceachance.com');
INSERT INTO `free_email_provider` VALUES ('1308', 'glay.org');
INSERT INTO `free_email_provider` VALUES ('1309', 'glendale.net');
INSERT INTO `free_email_provider` VALUES ('1310', 'globalfree.it');
INSERT INTO `free_email_provider` VALUES ('1311', 'globalpagan.com');
INSERT INTO `free_email_provider` VALUES ('1312', 'globalsite.com.br');
INSERT INTO `free_email_provider` VALUES ('1313', 'gmx.at');
INSERT INTO `free_email_provider` VALUES ('1314', 'gmx.li');
INSERT INTO `free_email_provider` VALUES ('1315', 'gmx.net');
INSERT INTO `free_email_provider` VALUES ('1316', 'go.ro');
INSERT INTO `free_email_provider` VALUES ('1317', 'go.ru');
INSERT INTO `free_email_provider` VALUES ('1318', 'go2net.com');
INSERT INTO `free_email_provider` VALUES ('1319', 'gofree.co.uk');
INSERT INTO `free_email_provider` VALUES ('1320', 'goldenmail.ru');
INSERT INTO `free_email_provider` VALUES ('1321', 'goldmail.ru');
INSERT INTO `free_email_provider` VALUES ('1322', 'golfemail.com');
INSERT INTO `free_email_provider` VALUES ('1323', 'golfmail.be');
INSERT INTO `free_email_provider` VALUES ('1324', 'gorontalo.net');
INSERT INTO `free_email_provider` VALUES ('1325', 'gothere.uk.com');
INSERT INTO `free_email_provider` VALUES ('1326', 'gotomy.com');
INSERT INTO `free_email_provider` VALUES ('1327', 'gportal.hu');
INSERT INTO `free_email_provider` VALUES ('1328', 'gratisweb.com');
INSERT INTO `free_email_provider` VALUES ('1329', 'grungecafe.com');
INSERT INTO `free_email_provider` VALUES ('1330', 'gua.net');
INSERT INTO `free_email_provider` VALUES ('1331', 'guessmail.com');
INSERT INTO `free_email_provider` VALUES ('1332', 'guju.net');
INSERT INTO `free_email_provider` VALUES ('1333', 'guy.com');
INSERT INTO `free_email_provider` VALUES ('1334', 'guy2.com');
INSERT INTO `free_email_provider` VALUES ('1335', 'guyanafriends.com');
INSERT INTO `free_email_provider` VALUES ('1336', 'gyorsposta.com');
INSERT INTO `free_email_provider` VALUES ('1337', 'gyorsposta.hu');
INSERT INTO `free_email_provider` VALUES ('1338', 'hackermail.net');
INSERT INTO `free_email_provider` VALUES ('1339', 'hailmail.net');
INSERT INTO `free_email_provider` VALUES ('1340', 'hairdresser.net');
INSERT INTO `free_email_provider` VALUES ('1341', 'handbag.com');
INSERT INTO `free_email_provider` VALUES ('1342', 'hang-ten.com');
INSERT INTO `free_email_provider` VALUES ('1343', 'happemail.com');
INSERT INTO `free_email_provider` VALUES ('1344', 'happycounsel.com');
INSERT INTO `free_email_provider` VALUES ('1345', 'hardcorefreak.com');
INSERT INTO `free_email_provider` VALUES ('1346', 'heartthrob.com');
INSERT INTO `free_email_provider` VALUES ('1347', 'heerschap.com');
INSERT INTO `free_email_provider` VALUES ('1348', 'heesun.net');
INSERT INTO `free_email_provider` VALUES ('1349', 'hehe.com');
INSERT INTO `free_email_provider` VALUES ('1350', 'hello.hu');
INSERT INTO `free_email_provider` VALUES ('1351', 'helter-skelter.com');
INSERT INTO `free_email_provider` VALUES ('1352', 'herediano.com');
INSERT INTO `free_email_provider` VALUES ('1353', 'herono1.com');
INSERT INTO `free_email_provider` VALUES ('1354', 'highmilton.com');
INSERT INTO `free_email_provider` VALUES ('1355', 'highquality.com');
INSERT INTO `free_email_provider` VALUES ('1356', 'highveldmail.co.za');
INSERT INTO `free_email_provider` VALUES ('1357', 'hispavista.com');
INSERT INTO `free_email_provider` VALUES ('1358', 'hkstarphoto.com');
INSERT INTO `free_email_provider` VALUES ('1359', 'hollywoodkids.com');
INSERT INTO `free_email_provider` VALUES ('1360', 'home.no.net');
INSERT INTO `free_email_provider` VALUES ('1361', 'home.ro');
INSERT INTO `free_email_provider` VALUES ('1362', 'home.se');
INSERT INTO `free_email_provider` VALUES ('1363', 'homelocator.com');
INSERT INTO `free_email_provider` VALUES ('1364', 'homestead.com');
INSERT INTO `free_email_provider` VALUES ('1365', 'hookup.net');
INSERT INTO `free_email_provider` VALUES ('1366', 'horrormail.com');
INSERT INTO `free_email_provider` VALUES ('1367', 'hot-shot.com');
INSERT INTO `free_email_provider` VALUES ('1368', 'hot.ee');
INSERT INTO `free_email_provider` VALUES ('1369', 'hotbrev.com');
INSERT INTO `free_email_provider` VALUES ('1370', 'hotfire.net');
INSERT INTO `free_email_provider` VALUES ('1371', 'hotletter.com');
INSERT INTO `free_email_provider` VALUES ('1372', 'hotmail.co.il');
INSERT INTO `free_email_provider` VALUES ('1373', 'hotmail.fr');
INSERT INTO `free_email_provider` VALUES ('1374', 'hotmail.kg');
INSERT INTO `free_email_provider` VALUES ('1375', 'hotmail.kz');
INSERT INTO `free_email_provider` VALUES ('1376', 'hotmail.ru');
INSERT INTO `free_email_provider` VALUES ('1377', 'hotpop3.com');
INSERT INTO `free_email_provider` VALUES ('1378', 'hotvoice.com');
INSERT INTO `free_email_provider` VALUES ('1379', 'hsuchi.net');
INSERT INTO `free_email_provider` VALUES ('1380', 'hunsa.com');
INSERT INTO `free_email_provider` VALUES ('1381', 'hushmail.com');
INSERT INTO `free_email_provider` VALUES ('1382', 'i-france.com');
INSERT INTO `free_email_provider` VALUES ('1383', 'i-mail.com.au');
INSERT INTO `free_email_provider` VALUES ('1384', 'i-p.com');
INSERT INTO `free_email_provider` VALUES ('1385', 'i12.com');
INSERT INTO `free_email_provider` VALUES ('1386', 'iamawoman.com');
INSERT INTO `free_email_provider` VALUES ('1387', 'iamwaiting.com');
INSERT INTO `free_email_provider` VALUES ('1388', 'iamwasted.com');
INSERT INTO `free_email_provider` VALUES ('1389', 'iamyours.com');
INSERT INTO `free_email_provider` VALUES ('1390', 'icestorm.com');
INSERT INTO `free_email_provider` VALUES ('1391', 'icmsconsultants.com');
INSERT INTO `free_email_provider` VALUES ('1392', 'icq.com');
INSERT INTO `free_email_provider` VALUES ('1393', 'icqmail.com');
INSERT INTO `free_email_provider` VALUES ('1394', 'icrazy.com');
INSERT INTO `free_email_provider` VALUES ('1395', 'ididitmyway.com');
INSERT INTO `free_email_provider` VALUES ('1396', 'idirect.com');
INSERT INTO `free_email_provider` VALUES ('1397', 'iespana.es');
INSERT INTO `free_email_provider` VALUES ('1398', 'ignazio.it');
INSERT INTO `free_email_provider` VALUES ('1399', 'ijustdontcare.com');
INSERT INTO `free_email_provider` VALUES ('1400', 'ilovechocolate.com');
INSERT INTO `free_email_provider` VALUES ('1401', 'ilovetocollect.net');
INSERT INTO `free_email_provider` VALUES ('1402', 'ilse.nl');
INSERT INTO `free_email_provider` VALUES ('1403', 'imail.ru');
INSERT INTO `free_email_provider` VALUES ('1404', 'imailbox.com');
INSERT INTO `free_email_provider` VALUES ('1405', 'imel.org');
INSERT INTO `free_email_provider` VALUES ('1406', 'imneverwrong.com');
INSERT INTO `free_email_provider` VALUES ('1407', 'imposter.co.uk');
INSERT INTO `free_email_provider` VALUES ('1408', 'imstressed.com');
INSERT INTO `free_email_provider` VALUES ('1409', 'imtoosexy.com');
INSERT INTO `free_email_provider` VALUES ('1410', 'in-box.net');
INSERT INTO `free_email_provider` VALUES ('1411', 'inbox.net');
INSERT INTO `free_email_provider` VALUES ('1412', 'inbox.ru');
INSERT INTO `free_email_provider` VALUES ('1413', 'incamail.com');
INSERT INTO `free_email_provider` VALUES ('1414', 'incredimail.com');
INSERT INTO `free_email_provider` VALUES ('1415', 'indexa.fr');
INSERT INTO `free_email_provider` VALUES ('1416', 'india.com');
INSERT INTO `free_email_provider` VALUES ('1417', 'indiatimes.com');
INSERT INTO `free_email_provider` VALUES ('1418', 'infohq.com');
INSERT INTO `free_email_provider` VALUES ('1419', 'infomail.es');
INSERT INTO `free_email_provider` VALUES ('1420', 'infomart.or.jp');
INSERT INTO `free_email_provider` VALUES ('1421', 'infovia.com.ar');
INSERT INTO `free_email_provider` VALUES ('1422', 'inicia.es');
INSERT INTO `free_email_provider` VALUES ('1423', 'inmail.sk');
INSERT INTO `free_email_provider` VALUES ('1424', 'inorbit.com');
INSERT INTO `free_email_provider` VALUES ('1425', 'insurer.com');
INSERT INTO `free_email_provider` VALUES ('1426', 'interfree.it');
INSERT INTO `free_email_provider` VALUES ('1427', 'interia.pl');
INSERT INTO `free_email_provider` VALUES ('1428', 'interlap.com.ar');
INSERT INTO `free_email_provider` VALUES ('1429', 'intermail.co.il');
INSERT INTO `free_email_provider` VALUES ('1430', 'internet-police.com');
INSERT INTO `free_email_provider` VALUES ('1431', 'internetbiz.com');
INSERT INTO `free_email_provider` VALUES ('1432', 'internetdrive.com');
INSERT INTO `free_email_provider` VALUES ('1433', 'internetegypt.com');
INSERT INTO `free_email_provider` VALUES ('1434', 'internetemails.net');
INSERT INTO `free_email_provider` VALUES ('1435', 'internetmailing.net');
INSERT INTO `free_email_provider` VALUES ('1436', 'inwind.it');
INSERT INTO `free_email_provider` VALUES ('1437', 'iobox.com');
INSERT INTO `free_email_provider` VALUES ('1438', 'iobox.fi');
INSERT INTO `free_email_provider` VALUES ('1439', 'iol.it');
INSERT INTO `free_email_provider` VALUES ('1440', 'ip3.com');
INSERT INTO `free_email_provider` VALUES ('1441', 'iqemail.com');
INSERT INTO `free_email_provider` VALUES ('1442', 'irangate.net');
INSERT INTO `free_email_provider` VALUES ('1443', 'iraqmail.com');
INSERT INTO `free_email_provider` VALUES ('1444', 'irj.hu');
INSERT INTO `free_email_provider` VALUES ('1445', 'isellcars.com');
INSERT INTO `free_email_provider` VALUES ('1446', 'islamonline.net');
INSERT INTO `free_email_provider` VALUES ('1447', 'ismart.net');
INSERT INTO `free_email_provider` VALUES ('1448', 'isonfire.com');
INSERT INTO `free_email_provider` VALUES ('1449', 'isp9.net');
INSERT INTO `free_email_provider` VALUES ('1450', 'itloox.com');
INSERT INTO `free_email_provider` VALUES ('1451', 'itmom.com');
INSERT INTO `free_email_provider` VALUES ('1452', 'ivebeenframed.com');
INSERT INTO `free_email_provider` VALUES ('1453', 'iwan-fals.com');
INSERT INTO `free_email_provider` VALUES ('1454', 'iwon.com');
INSERT INTO `free_email_provider` VALUES ('1455', 'izadpanah.com');
INSERT INTO `free_email_provider` VALUES ('1456', 'jakuza.hu');
INSERT INTO `free_email_provider` VALUES ('1457', 'japan.com');
INSERT INTO `free_email_provider` VALUES ('1458', 'jazzandjava.com');
INSERT INTO `free_email_provider` VALUES ('1459', 'jazzgame.com');
INSERT INTO `free_email_provider` VALUES ('1460', 'jetemail.net');
INSERT INTO `free_email_provider` VALUES ('1461', 'jippii.fi');
INSERT INTO `free_email_provider` VALUES ('1462', 'jmail.co.za');
INSERT INTO `free_email_provider` VALUES ('1463', 'jordanmail.com');
INSERT INTO `free_email_provider` VALUES ('1464', 'journalist.com');
INSERT INTO `free_email_provider` VALUES ('1465', 'jovem.te.pt');
INSERT INTO `free_email_provider` VALUES ('1466', 'jpopmail.com');
INSERT INTO `free_email_provider` VALUES ('1467', 'jubiimail.dk');
INSERT INTO `free_email_provider` VALUES ('1468', 'jumpy.it');
INSERT INTO `free_email_provider` VALUES ('1469', 'justemail.net');
INSERT INTO `free_email_provider` VALUES ('1470', 'kaazoo.com');
INSERT INTO `free_email_provider` VALUES ('1471', 'kaixo.com');
INSERT INTO `free_email_provider` VALUES ('1472', 'kalpoint.com');
INSERT INTO `free_email_provider` VALUES ('1473', 'kapoorweb.com');
INSERT INTO `free_email_provider` VALUES ('1474', 'karachian.com');
INSERT INTO `free_email_provider` VALUES ('1475', 'karachioye.com');
INSERT INTO `free_email_provider` VALUES ('1476', 'karbasi.com');
INSERT INTO `free_email_provider` VALUES ('1477', 'katamail.com');
INSERT INTO `free_email_provider` VALUES ('1478', 'kayafmmail.co.za');
INSERT INTO `free_email_provider` VALUES ('1479', 'keg-party.com');
INSERT INTO `free_email_provider` VALUES ('1480', 'keko.com.ar');
INSERT INTO `free_email_provider` VALUES ('1481', 'kellychen.com');
INSERT INTO `free_email_provider` VALUES ('1482', 'keromail.com');
INSERT INTO `free_email_provider` VALUES ('1483', 'kgb.hu');
INSERT INTO `free_email_provider` VALUES ('1484', 'khosropour.com');
INSERT INTO `free_email_provider` VALUES ('1485', 'kickassmail.com');
INSERT INTO `free_email_provider` VALUES ('1486', 'killermail.com');
INSERT INTO `free_email_provider` VALUES ('1487', 'kimo.com');
INSERT INTO `free_email_provider` VALUES ('1488', 'kinki-kids.com');
INSERT INTO `free_email_provider` VALUES ('1489', 'kittymail.com');
INSERT INTO `free_email_provider` VALUES ('1490', 'kiwibox.com');
INSERT INTO `free_email_provider` VALUES ('1491', 'kiwitown.com');
INSERT INTO `free_email_provider` VALUES ('1492', 'krunis.com');
INSERT INTO `free_email_provider` VALUES ('1493', 'kukamail.com');
INSERT INTO `free_email_provider` VALUES ('1494', 'kumarweb.com');
INSERT INTO `free_email_provider` VALUES ('1495', 'kuwait-mail.com');
INSERT INTO `free_email_provider` VALUES ('1496', 'ladymail.cz');
INSERT INTO `free_email_provider` VALUES ('1497', 'lagerlouts.com');
INSERT INTO `free_email_provider` VALUES ('1498', 'lahoreoye.com');
INSERT INTO `free_email_provider` VALUES ('1499', 'lakmail.com');
INSERT INTO `free_email_provider` VALUES ('1500', 'lamer.hu');
INSERT INTO `free_email_provider` VALUES ('1501', 'land.ru');
INSERT INTO `free_email_provider` VALUES ('1502', 'lankamail.com');
INSERT INTO `free_email_provider` VALUES ('1503', 'laposte.net');
INSERT INTO `free_email_provider` VALUES ('1504', 'lawyer.com');
INSERT INTO `free_email_provider` VALUES ('1505', 'leehom.net');
INSERT INTO `free_email_provider` VALUES ('1506', 'legalactions.com');
INSERT INTO `free_email_provider` VALUES ('1507', 'legislator.com');
INSERT INTO `free_email_provider` VALUES ('1508', 'leonlai.net');
INSERT INTO `free_email_provider` VALUES ('1509', 'levele.com');
INSERT INTO `free_email_provider` VALUES ('1510', 'levele.hu');
INSERT INTO `free_email_provider` VALUES ('1511', 'lex.bg');
INSERT INTO `free_email_provider` VALUES ('1512', 'liberomail.com');
INSERT INTO `free_email_provider` VALUES ('1513', 'linkmaster.com');
INSERT INTO `free_email_provider` VALUES ('1514', 'linuxfreemail.com');
INSERT INTO `free_email_provider` VALUES ('1515', 'linuxmail.org');
INSERT INTO `free_email_provider` VALUES ('1516', 'lionsfan.com.au');
INSERT INTO `free_email_provider` VALUES ('1517', 'liontrucks.com');
INSERT INTO `free_email_provider` VALUES ('1518', 'list.ru');
INSERT INTO `free_email_provider` VALUES ('1519', 'liverpoolfans.com');
INSERT INTO `free_email_provider` VALUES ('1520', 'llandudno.com');
INSERT INTO `free_email_provider` VALUES ('1521', 'llangollen.com');
INSERT INTO `free_email_provider` VALUES ('1522', 'lmxmail.sk');
INSERT INTO `free_email_provider` VALUES ('1523', 'lobbyist.com');
INSERT INTO `free_email_provider` VALUES ('1524', 'localbar.com');
INSERT INTO `free_email_provider` VALUES ('1525', 'london.com');
INSERT INTO `free_email_provider` VALUES ('1526', 'lopezclub.com');
INSERT INTO `free_email_provider` VALUES ('1527', 'louiskoo.com');
INSERT INTO `free_email_provider` VALUES ('1528', 'love.cz');
INSERT INTO `free_email_provider` VALUES ('1529', 'loveable.com');
INSERT INTO `free_email_provider` VALUES ('1530', 'lovelygirl.net');
INSERT INTO `free_email_provider` VALUES ('1531', 'lover-boy.com');
INSERT INTO `free_email_provider` VALUES ('1532', 'lovergirl.com');
INSERT INTO `free_email_provider` VALUES ('1533', 'lovingjesus.com');
INSERT INTO `free_email_provider` VALUES ('1534', 'luso.pt');
INSERT INTO `free_email_provider` VALUES ('1535', 'luukku.com');
INSERT INTO `free_email_provider` VALUES ('1536', 'lycos.co.uk');
INSERT INTO `free_email_provider` VALUES ('1537', 'lycos.com');
INSERT INTO `free_email_provider` VALUES ('1538', 'lycos.es');
INSERT INTO `free_email_provider` VALUES ('1539', 'lycos.it');
INSERT INTO `free_email_provider` VALUES ('1540', 'lycos.ne.jp');
INSERT INTO `free_email_provider` VALUES ('1541', 'lycosmail.com');
INSERT INTO `free_email_provider` VALUES ('1542', 'm-a-i-l.com');
INSERT INTO `free_email_provider` VALUES ('1543', 'mac.com');
INSERT INTO `free_email_provider` VALUES ('1544', 'machinecandy.com');
INSERT INTO `free_email_provider` VALUES ('1545', 'macmail.com');
INSERT INTO `free_email_provider` VALUES ('1546', 'madrid.com');
INSERT INTO `free_email_provider` VALUES ('1547', 'maffia.hu');
INSERT INTO `free_email_provider` VALUES ('1548', 'magicmail.co.za');
INSERT INTO `free_email_provider` VALUES ('1549', 'mahmoodweb.com');
INSERT INTO `free_email_provider` VALUES ('1550', 'mail-awu.de');
INSERT INTO `free_email_provider` VALUES ('1551', 'mail-box.cz');
INSERT INTO `free_email_provider` VALUES ('1552', 'mail-center.com');
INSERT INTO `free_email_provider` VALUES ('1553', 'mail-central.com');
INSERT INTO `free_email_provider` VALUES ('1554', 'mail-page.com');
INSERT INTO `free_email_provider` VALUES ('1555', 'mail.austria.com');
INSERT INTO `free_email_provider` VALUES ('1556', 'mail.az');
INSERT INTO `free_email_provider` VALUES ('1557', 'mail.be');
INSERT INTO `free_email_provider` VALUES ('1558', 'mail.bulgaria.com');
INSERT INTO `free_email_provider` VALUES ('1559', 'mail.co.za');
INSERT INTO `free_email_provider` VALUES ('1560', 'mail.ee');
INSERT INTO `free_email_provider` VALUES ('1561', 'mail.gr');
INSERT INTO `free_email_provider` VALUES ('1562', 'mail.nu');
INSERT INTO `free_email_provider` VALUES ('1563', 'mail.pf');
INSERT INTO `free_email_provider` VALUES ('1564', 'mail.pt');
INSERT INTO `free_email_provider` VALUES ('1565', 'mail.r-o-o-t.com');
INSERT INTO `free_email_provider` VALUES ('1566', 'mail.sisna.com');
INSERT INTO `free_email_provider` VALUES ('1567', 'mail.vasarhely.hu');
INSERT INTO `free_email_provider` VALUES ('1568', 'mail15.com');
INSERT INTO `free_email_provider` VALUES ('1569', 'mail2007.com');
INSERT INTO `free_email_provider` VALUES ('1570', 'mail2aaron.com');
INSERT INTO `free_email_provider` VALUES ('1571', 'mail2abby.com');
INSERT INTO `free_email_provider` VALUES ('1572', 'mail2abc.com');
INSERT INTO `free_email_provider` VALUES ('1573', 'mail2actor.com');
INSERT INTO `free_email_provider` VALUES ('1574', 'mail2admiral.com');
INSERT INTO `free_email_provider` VALUES ('1575', 'mail2adorable.com');
INSERT INTO `free_email_provider` VALUES ('1576', 'mail2adoration.com');
INSERT INTO `free_email_provider` VALUES ('1577', 'mail2adore.com');
INSERT INTO `free_email_provider` VALUES ('1578', 'mail2adventure.com');
INSERT INTO `free_email_provider` VALUES ('1579', 'mail2aeolus.com');
INSERT INTO `free_email_provider` VALUES ('1580', 'mail2aether.com');
INSERT INTO `free_email_provider` VALUES ('1581', 'mail2affection.com');
INSERT INTO `free_email_provider` VALUES ('1582', 'mail2afghanistan.com');
INSERT INTO `free_email_provider` VALUES ('1583', 'mail2africa.com');
INSERT INTO `free_email_provider` VALUES ('1584', 'mail2agent.com');
INSERT INTO `free_email_provider` VALUES ('1585', 'mail2aha.com');
INSERT INTO `free_email_provider` VALUES ('1586', 'mail2ahoy.com');
INSERT INTO `free_email_provider` VALUES ('1587', 'mail2aim.com');
INSERT INTO `free_email_provider` VALUES ('1588', 'mail2air.com');
INSERT INTO `free_email_provider` VALUES ('1589', 'mail2airbag.com');
INSERT INTO `free_email_provider` VALUES ('1590', 'mail2airforce.com');
INSERT INTO `free_email_provider` VALUES ('1591', 'mail2airport.com');
INSERT INTO `free_email_provider` VALUES ('1592', 'mail2alabama.com');
INSERT INTO `free_email_provider` VALUES ('1593', 'mail2alan.com');
INSERT INTO `free_email_provider` VALUES ('1594', 'mail2alaska.com');
INSERT INTO `free_email_provider` VALUES ('1595', 'mail2albania.com');
INSERT INTO `free_email_provider` VALUES ('1596', 'mail2alcoholic.com');
INSERT INTO `free_email_provider` VALUES ('1597', 'mail2alec.com');
INSERT INTO `free_email_provider` VALUES ('1598', 'mail2alexa.com');
INSERT INTO `free_email_provider` VALUES ('1599', 'mail2algeria.com');
INSERT INTO `free_email_provider` VALUES ('1600', 'mail2alicia.com');
INSERT INTO `free_email_provider` VALUES ('1601', 'mail2alien.com');
INSERT INTO `free_email_provider` VALUES ('1602', 'mail2allan.com');
INSERT INTO `free_email_provider` VALUES ('1603', 'mail2allen.com');
INSERT INTO `free_email_provider` VALUES ('1604', 'mail2allison.com');
INSERT INTO `free_email_provider` VALUES ('1605', 'mail2alpha.com');
INSERT INTO `free_email_provider` VALUES ('1606', 'mail2alyssa.com');
INSERT INTO `free_email_provider` VALUES ('1607', 'mail2amanda.com');
INSERT INTO `free_email_provider` VALUES ('1608', 'mail2amazing.com');
INSERT INTO `free_email_provider` VALUES ('1609', 'mail2amber.com');
INSERT INTO `free_email_provider` VALUES ('1610', 'mail2america.com');
INSERT INTO `free_email_provider` VALUES ('1611', 'mail2american.com');
INSERT INTO `free_email_provider` VALUES ('1612', 'mail2andorra.com');
INSERT INTO `free_email_provider` VALUES ('1613', 'mail2andrea.com');
INSERT INTO `free_email_provider` VALUES ('1614', 'mail2andy.com');
INSERT INTO `free_email_provider` VALUES ('1615', 'mail2anesthesiologist.com');
INSERT INTO `free_email_provider` VALUES ('1616', 'mail2angela.com');
INSERT INTO `free_email_provider` VALUES ('1617', 'mail2angola.com');
INSERT INTO `free_email_provider` VALUES ('1618', 'mail2ann.com');
INSERT INTO `free_email_provider` VALUES ('1619', 'mail2anna.com');
INSERT INTO `free_email_provider` VALUES ('1620', 'mail2anne.com');
INSERT INTO `free_email_provider` VALUES ('1621', 'mail2anthony.com');
INSERT INTO `free_email_provider` VALUES ('1622', 'mail2anything.com');
INSERT INTO `free_email_provider` VALUES ('1623', 'mail2aphrodite.com');
INSERT INTO `free_email_provider` VALUES ('1624', 'mail2apollo.com');
INSERT INTO `free_email_provider` VALUES ('1625', 'mail2april.com');
INSERT INTO `free_email_provider` VALUES ('1626', 'mail2aquarius.com');
INSERT INTO `free_email_provider` VALUES ('1627', 'mail2arabia.com');
INSERT INTO `free_email_provider` VALUES ('1628', 'mail2arabic.com');
INSERT INTO `free_email_provider` VALUES ('1629', 'mail2architect.com');
INSERT INTO `free_email_provider` VALUES ('1630', 'mail2ares.com');
INSERT INTO `free_email_provider` VALUES ('1631', 'mail2argentina.com');
INSERT INTO `free_email_provider` VALUES ('1632', 'mail2aries.com');
INSERT INTO `free_email_provider` VALUES ('1633', 'mail2arizona.com');
INSERT INTO `free_email_provider` VALUES ('1634', 'mail2arkansas.com');
INSERT INTO `free_email_provider` VALUES ('1635', 'mail2armenia.com');
INSERT INTO `free_email_provider` VALUES ('1636', 'mail2army.com');
INSERT INTO `free_email_provider` VALUES ('1637', 'mail2arnold.com');
INSERT INTO `free_email_provider` VALUES ('1638', 'mail2art.com');
INSERT INTO `free_email_provider` VALUES ('1639', 'mail2artemus.com');
INSERT INTO `free_email_provider` VALUES ('1640', 'mail2arthur.com');
INSERT INTO `free_email_provider` VALUES ('1641', 'mail2artist.com');
INSERT INTO `free_email_provider` VALUES ('1642', 'mail2ashley.com');
INSERT INTO `free_email_provider` VALUES ('1643', 'mail2ask.com');
INSERT INTO `free_email_provider` VALUES ('1644', 'mail2astronomer.com');
INSERT INTO `free_email_provider` VALUES ('1645', 'mail2athena.com');
INSERT INTO `free_email_provider` VALUES ('1646', 'mail2athlete.com');
INSERT INTO `free_email_provider` VALUES ('1647', 'mail2atlas.com');
INSERT INTO `free_email_provider` VALUES ('1648', 'mail2atom.com');
INSERT INTO `free_email_provider` VALUES ('1649', 'mail2attitude.com');
INSERT INTO `free_email_provider` VALUES ('1650', 'mail2auction.com');
INSERT INTO `free_email_provider` VALUES ('1651', 'mail2aunt.com');
INSERT INTO `free_email_provider` VALUES ('1652', 'mail2australia.com');
INSERT INTO `free_email_provider` VALUES ('1653', 'mail2austria.com');
INSERT INTO `free_email_provider` VALUES ('1654', 'mail2azerbaijan.com');
INSERT INTO `free_email_provider` VALUES ('1655', 'mail2baby.com');
INSERT INTO `free_email_provider` VALUES ('1656', 'mail2bahamas.com');
INSERT INTO `free_email_provider` VALUES ('1657', 'mail2bahrain.com');
INSERT INTO `free_email_provider` VALUES ('1658', 'mail2ballerina.com');
INSERT INTO `free_email_provider` VALUES ('1659', 'mail2ballplayer.com');
INSERT INTO `free_email_provider` VALUES ('1660', 'mail2band.com');
INSERT INTO `free_email_provider` VALUES ('1661', 'mail2bangladesh.com');
INSERT INTO `free_email_provider` VALUES ('1662', 'mail2bank.com');
INSERT INTO `free_email_provider` VALUES ('1663', 'mail2banker.com');
INSERT INTO `free_email_provider` VALUES ('1664', 'mail2bankrupt.com');
INSERT INTO `free_email_provider` VALUES ('1665', 'mail2baptist.com');
INSERT INTO `free_email_provider` VALUES ('1666', 'mail2bar.com');
INSERT INTO `free_email_provider` VALUES ('1667', 'mail2barbados.com');
INSERT INTO `free_email_provider` VALUES ('1668', 'mail2barbara.com');
INSERT INTO `free_email_provider` VALUES ('1669', 'mail2barter.com');
INSERT INTO `free_email_provider` VALUES ('1670', 'mail2basketball.com');
INSERT INTO `free_email_provider` VALUES ('1671', 'mail2batter.com');
INSERT INTO `free_email_provider` VALUES ('1672', 'mail2beach.com');
INSERT INTO `free_email_provider` VALUES ('1673', 'mail2beast.com');
INSERT INTO `free_email_provider` VALUES ('1674', 'mail2beatles.com');
INSERT INTO `free_email_provider` VALUES ('1675', 'mail2beauty.com');
INSERT INTO `free_email_provider` VALUES ('1676', 'mail2becky.com');
INSERT INTO `free_email_provider` VALUES ('1677', 'mail2beijing.com');
INSERT INTO `free_email_provider` VALUES ('1678', 'mail2belgium.com');
INSERT INTO `free_email_provider` VALUES ('1679', 'mail2belize.com');
INSERT INTO `free_email_provider` VALUES ('1680', 'mail2ben.com');
INSERT INTO `free_email_provider` VALUES ('1681', 'mail2bernard.com');
INSERT INTO `free_email_provider` VALUES ('1682', 'mail2beth.com');
INSERT INTO `free_email_provider` VALUES ('1683', 'mail2betty.com');
INSERT INTO `free_email_provider` VALUES ('1684', 'mail2beverly.com');
INSERT INTO `free_email_provider` VALUES ('1685', 'mail2beyond.com');
INSERT INTO `free_email_provider` VALUES ('1686', 'mail2biker.com');
INSERT INTO `free_email_provider` VALUES ('1687', 'mail2bill.com');
INSERT INTO `free_email_provider` VALUES ('1688', 'mail2billionaire.com');
INSERT INTO `free_email_provider` VALUES ('1689', 'mail2billy.com');
INSERT INTO `free_email_provider` VALUES ('1690', 'mail2bio.com');
INSERT INTO `free_email_provider` VALUES ('1691', 'mail2biologist.com');
INSERT INTO `free_email_provider` VALUES ('1692', 'mail2black.com');
INSERT INTO `free_email_provider` VALUES ('1693', 'mail2blackbelt.com');
INSERT INTO `free_email_provider` VALUES ('1694', 'mail2blake.com');
INSERT INTO `free_email_provider` VALUES ('1695', 'mail2blind.com');
INSERT INTO `free_email_provider` VALUES ('1696', 'mail2blonde.com');
INSERT INTO `free_email_provider` VALUES ('1697', 'mail2blues.com');
INSERT INTO `free_email_provider` VALUES ('1698', 'mail2bob.com');
INSERT INTO `free_email_provider` VALUES ('1699', 'mail2bobby.com');
INSERT INTO `free_email_provider` VALUES ('1700', 'mail2bolivia.com');
INSERT INTO `free_email_provider` VALUES ('1701', 'mail2bombay.com');
INSERT INTO `free_email_provider` VALUES ('1702', 'mail2bonn.com');
INSERT INTO `free_email_provider` VALUES ('1703', 'mail2bookmark.com');
INSERT INTO `free_email_provider` VALUES ('1704', 'mail2boreas.com');
INSERT INTO `free_email_provider` VALUES ('1705', 'mail2bosnia.com');
INSERT INTO `free_email_provider` VALUES ('1706', 'mail2boston.com');
INSERT INTO `free_email_provider` VALUES ('1707', 'mail2botswana.com');
INSERT INTO `free_email_provider` VALUES ('1708', 'mail2bradley.com');
INSERT INTO `free_email_provider` VALUES ('1709', 'mail2brazil.com');
INSERT INTO `free_email_provider` VALUES ('1710', 'mail2breakfast.com');
INSERT INTO `free_email_provider` VALUES ('1711', 'mail2brian.com');
INSERT INTO `free_email_provider` VALUES ('1712', 'mail2bride.com');
INSERT INTO `free_email_provider` VALUES ('1713', 'mail2brittany.com');
INSERT INTO `free_email_provider` VALUES ('1714', 'mail2broker.com');
INSERT INTO `free_email_provider` VALUES ('1715', 'mail2brook.com');
INSERT INTO `free_email_provider` VALUES ('1716', 'mail2bruce.com');
INSERT INTO `free_email_provider` VALUES ('1717', 'mail2brunei.com');
INSERT INTO `free_email_provider` VALUES ('1718', 'mail2brunette.com');
INSERT INTO `free_email_provider` VALUES ('1719', 'mail2brussels.com');
INSERT INTO `free_email_provider` VALUES ('1720', 'mail2bryan.com');
INSERT INTO `free_email_provider` VALUES ('1721', 'mail2bug.com');
INSERT INTO `free_email_provider` VALUES ('1722', 'mail2bulgaria.com');
INSERT INTO `free_email_provider` VALUES ('1723', 'mail2business.com');
INSERT INTO `free_email_provider` VALUES ('1724', 'mail2buy.com');
INSERT INTO `free_email_provider` VALUES ('1725', 'mail2ca.com');
INSERT INTO `free_email_provider` VALUES ('1726', 'mail2california.com');
INSERT INTO `free_email_provider` VALUES ('1727', 'mail2calvin.com');
INSERT INTO `free_email_provider` VALUES ('1728', 'mail2cambodia.com');
INSERT INTO `free_email_provider` VALUES ('1729', 'mail2cameroon.com');
INSERT INTO `free_email_provider` VALUES ('1730', 'mail2canada.com');
INSERT INTO `free_email_provider` VALUES ('1731', 'mail2cancer.com');
INSERT INTO `free_email_provider` VALUES ('1732', 'mail2capeverde.com');
INSERT INTO `free_email_provider` VALUES ('1733', 'mail2capricorn.com');
INSERT INTO `free_email_provider` VALUES ('1734', 'mail2cardinal.com');
INSERT INTO `free_email_provider` VALUES ('1735', 'mail2cardiologist.com');
INSERT INTO `free_email_provider` VALUES ('1736', 'mail2care.com');
INSERT INTO `free_email_provider` VALUES ('1737', 'mail2caroline.com');
INSERT INTO `free_email_provider` VALUES ('1738', 'mail2carolyn.com');
INSERT INTO `free_email_provider` VALUES ('1739', 'mail2casey.com');
INSERT INTO `free_email_provider` VALUES ('1740', 'mail2cat.com');
INSERT INTO `free_email_provider` VALUES ('1741', 'mail2caterer.com');
INSERT INTO `free_email_provider` VALUES ('1742', 'mail2cathy.com');
INSERT INTO `free_email_provider` VALUES ('1743', 'mail2catlover.com');
INSERT INTO `free_email_provider` VALUES ('1744', 'mail2catwalk.com');
INSERT INTO `free_email_provider` VALUES ('1745', 'mail2cell.com');
INSERT INTO `free_email_provider` VALUES ('1746', 'mail2chad.com');
INSERT INTO `free_email_provider` VALUES ('1747', 'mail2champaign.com');
INSERT INTO `free_email_provider` VALUES ('1748', 'mail2charles.com');
INSERT INTO `free_email_provider` VALUES ('1749', 'mail2chef.com');
INSERT INTO `free_email_provider` VALUES ('1750', 'mail2chemist.com');
INSERT INTO `free_email_provider` VALUES ('1751', 'mail2cherry.com');
INSERT INTO `free_email_provider` VALUES ('1752', 'mail2chicago.com');
INSERT INTO `free_email_provider` VALUES ('1753', 'mail2chile.com');
INSERT INTO `free_email_provider` VALUES ('1754', 'mail2china.com');
INSERT INTO `free_email_provider` VALUES ('1755', 'mail2chinese.com');
INSERT INTO `free_email_provider` VALUES ('1756', 'mail2chocolate.com');
INSERT INTO `free_email_provider` VALUES ('1757', 'mail2christian.com');
INSERT INTO `free_email_provider` VALUES ('1758', 'mail2christie.com');
INSERT INTO `free_email_provider` VALUES ('1759', 'mail2christmas.com');
INSERT INTO `free_email_provider` VALUES ('1760', 'mail2christy.com');
INSERT INTO `free_email_provider` VALUES ('1761', 'mail2chuck.com');
INSERT INTO `free_email_provider` VALUES ('1762', 'mail2cindy.com');
INSERT INTO `free_email_provider` VALUES ('1763', 'mail2clark.com');
INSERT INTO `free_email_provider` VALUES ('1764', 'mail2classifieds.com');
INSERT INTO `free_email_provider` VALUES ('1765', 'mail2claude.com');
INSERT INTO `free_email_provider` VALUES ('1766', 'mail2cliff.com');
INSERT INTO `free_email_provider` VALUES ('1767', 'mail2clinic.com');
INSERT INTO `free_email_provider` VALUES ('1768', 'mail2clint.com');
INSERT INTO `free_email_provider` VALUES ('1769', 'mail2close.com');
INSERT INTO `free_email_provider` VALUES ('1770', 'mail2club.com');
INSERT INTO `free_email_provider` VALUES ('1771', 'mail2coach.com');
INSERT INTO `free_email_provider` VALUES ('1772', 'mail2coastguard.com');
INSERT INTO `free_email_provider` VALUES ('1773', 'mail2colin.com');
INSERT INTO `free_email_provider` VALUES ('1774', 'mail2college.com');
INSERT INTO `free_email_provider` VALUES ('1775', 'mail2colombia.com');
INSERT INTO `free_email_provider` VALUES ('1776', 'mail2color.com');
INSERT INTO `free_email_provider` VALUES ('1777', 'mail2colorado.com');
INSERT INTO `free_email_provider` VALUES ('1778', 'mail2columbia.com');
INSERT INTO `free_email_provider` VALUES ('1779', 'mail2comedian.com');
INSERT INTO `free_email_provider` VALUES ('1780', 'mail2composer.com');
INSERT INTO `free_email_provider` VALUES ('1781', 'mail2computer.com');
INSERT INTO `free_email_provider` VALUES ('1782', 'mail2computers.com');
INSERT INTO `free_email_provider` VALUES ('1783', 'mail2concert.com');
INSERT INTO `free_email_provider` VALUES ('1784', 'mail2congo.com');
INSERT INTO `free_email_provider` VALUES ('1785', 'mail2connect.com');
INSERT INTO `free_email_provider` VALUES ('1786', 'mail2connecticut.com');
INSERT INTO `free_email_provider` VALUES ('1787', 'mail2consultant.com');
INSERT INTO `free_email_provider` VALUES ('1788', 'mail2convict.com');
INSERT INTO `free_email_provider` VALUES ('1789', 'mail2cook.com');
INSERT INTO `free_email_provider` VALUES ('1790', 'mail2cool.com');
INSERT INTO `free_email_provider` VALUES ('1791', 'mail2cory.com');
INSERT INTO `free_email_provider` VALUES ('1792', 'mail2costarica.com');
INSERT INTO `free_email_provider` VALUES ('1793', 'mail2country.com');
INSERT INTO `free_email_provider` VALUES ('1794', 'mail2courtney.com');
INSERT INTO `free_email_provider` VALUES ('1795', 'mail2cowboy.com');
INSERT INTO `free_email_provider` VALUES ('1796', 'mail2cowgirl.com');
INSERT INTO `free_email_provider` VALUES ('1797', 'mail2craig.com');
INSERT INTO `free_email_provider` VALUES ('1798', 'mail2crave.com');
INSERT INTO `free_email_provider` VALUES ('1799', 'mail2crazy.com');
INSERT INTO `free_email_provider` VALUES ('1800', 'mail2create.com');
INSERT INTO `free_email_provider` VALUES ('1801', 'mail2croatia.com');
INSERT INTO `free_email_provider` VALUES ('1802', 'mail2cry.com');
INSERT INTO `free_email_provider` VALUES ('1803', 'mail2crystal.com');
INSERT INTO `free_email_provider` VALUES ('1804', 'mail2cuba.com');
INSERT INTO `free_email_provider` VALUES ('1805', 'mail2culture.com');
INSERT INTO `free_email_provider` VALUES ('1806', 'mail2curt.com');
INSERT INTO `free_email_provider` VALUES ('1807', 'mail2customs.com');
INSERT INTO `free_email_provider` VALUES ('1808', 'mail2cute.com');
INSERT INTO `free_email_provider` VALUES ('1809', 'mail2cutey.com');
INSERT INTO `free_email_provider` VALUES ('1810', 'mail2cynthia.com');
INSERT INTO `free_email_provider` VALUES ('1811', 'mail2cyprus.com');
INSERT INTO `free_email_provider` VALUES ('1812', 'mail2czechrepublic.com');
INSERT INTO `free_email_provider` VALUES ('1813', 'mail2dad.com');
INSERT INTO `free_email_provider` VALUES ('1814', 'mail2dale.com');
INSERT INTO `free_email_provider` VALUES ('1815', 'mail2dallas.com');
INSERT INTO `free_email_provider` VALUES ('1816', 'mail2dan.com');
INSERT INTO `free_email_provider` VALUES ('1817', 'mail2dana.com');
INSERT INTO `free_email_provider` VALUES ('1818', 'mail2dance.com');
INSERT INTO `free_email_provider` VALUES ('1819', 'mail2dancer.com');
INSERT INTO `free_email_provider` VALUES ('1820', 'mail2danielle.com');
INSERT INTO `free_email_provider` VALUES ('1821', 'mail2danny.com');
INSERT INTO `free_email_provider` VALUES ('1822', 'mail2darlene.com');
INSERT INTO `free_email_provider` VALUES ('1823', 'mail2darling.com');
INSERT INTO `free_email_provider` VALUES ('1824', 'mail2darren.com');
INSERT INTO `free_email_provider` VALUES ('1825', 'mail2daughter.com');
INSERT INTO `free_email_provider` VALUES ('1826', 'mail2dave.com');
INSERT INTO `free_email_provider` VALUES ('1827', 'mail2dawn.com');
INSERT INTO `free_email_provider` VALUES ('1828', 'mail2dc.com');
INSERT INTO `free_email_provider` VALUES ('1829', 'mail2dealer.com');
INSERT INTO `free_email_provider` VALUES ('1830', 'mail2deanna.com');
INSERT INTO `free_email_provider` VALUES ('1831', 'mail2dearest.com');
INSERT INTO `free_email_provider` VALUES ('1832', 'mail2debbie.com');
INSERT INTO `free_email_provider` VALUES ('1833', 'mail2debby.com');
INSERT INTO `free_email_provider` VALUES ('1834', 'mail2deer.com');
INSERT INTO `free_email_provider` VALUES ('1835', 'mail2delaware.com');
INSERT INTO `free_email_provider` VALUES ('1836', 'mail2delicious.com');
INSERT INTO `free_email_provider` VALUES ('1837', 'mail2demeter.com');
INSERT INTO `free_email_provider` VALUES ('1838', 'mail2democrat.com');
INSERT INTO `free_email_provider` VALUES ('1839', 'mail2denise.com');
INSERT INTO `free_email_provider` VALUES ('1840', 'mail2denmark.com');
INSERT INTO `free_email_provider` VALUES ('1841', 'mail2dennis.com');
INSERT INTO `free_email_provider` VALUES ('1842', 'mail2dentist.com');
INSERT INTO `free_email_provider` VALUES ('1843', 'mail2derek.com');
INSERT INTO `free_email_provider` VALUES ('1844', 'mail2desert.com');
INSERT INTO `free_email_provider` VALUES ('1845', 'mail2devoted.com');
INSERT INTO `free_email_provider` VALUES ('1846', 'mail2devotion.com');
INSERT INTO `free_email_provider` VALUES ('1847', 'mail2diamond.com');
INSERT INTO `free_email_provider` VALUES ('1848', 'mail2diana.com');
INSERT INTO `free_email_provider` VALUES ('1849', 'mail2diane.com');
INSERT INTO `free_email_provider` VALUES ('1850', 'mail2diehard.com');
INSERT INTO `free_email_provider` VALUES ('1851', 'mail2dilemma.com');
INSERT INTO `free_email_provider` VALUES ('1852', 'mail2dillon.com');
INSERT INTO `free_email_provider` VALUES ('1853', 'mail2dinner.com');
INSERT INTO `free_email_provider` VALUES ('1854', 'mail2dinosaur.com');
INSERT INTO `free_email_provider` VALUES ('1855', 'mail2dionysos.com');
INSERT INTO `free_email_provider` VALUES ('1856', 'mail2diplomat.com');
INSERT INTO `free_email_provider` VALUES ('1857', 'mail2director.com');
INSERT INTO `free_email_provider` VALUES ('1858', 'mail2dirk.com');
INSERT INTO `free_email_provider` VALUES ('1859', 'mail2disco.com');
INSERT INTO `free_email_provider` VALUES ('1860', 'mail2dive.com');
INSERT INTO `free_email_provider` VALUES ('1861', 'mail2diver.com');
INSERT INTO `free_email_provider` VALUES ('1862', 'mail2divorced.com');
INSERT INTO `free_email_provider` VALUES ('1863', 'mail2djibouti.com');
INSERT INTO `free_email_provider` VALUES ('1864', 'mail2doctor.com');
INSERT INTO `free_email_provider` VALUES ('1865', 'mail2doglover.com');
INSERT INTO `free_email_provider` VALUES ('1866', 'mail2dominic.com');
INSERT INTO `free_email_provider` VALUES ('1867', 'mail2dominica.com');
INSERT INTO `free_email_provider` VALUES ('1868', 'mail2dominicanrepublic.com');
INSERT INTO `free_email_provider` VALUES ('1869', 'mail2don.com');
INSERT INTO `free_email_provider` VALUES ('1870', 'mail2donald.com');
INSERT INTO `free_email_provider` VALUES ('1871', 'mail2donna.com');
INSERT INTO `free_email_provider` VALUES ('1872', 'mail2doris.com');
INSERT INTO `free_email_provider` VALUES ('1873', 'mail2dorothy.com');
INSERT INTO `free_email_provider` VALUES ('1874', 'mail2doug.com');
INSERT INTO `free_email_provider` VALUES ('1875', 'mail2dough.com');
INSERT INTO `free_email_provider` VALUES ('1876', 'mail2douglas.com');
INSERT INTO `free_email_provider` VALUES ('1877', 'mail2dow.com');
INSERT INTO `free_email_provider` VALUES ('1878', 'mail2downtown.com');
INSERT INTO `free_email_provider` VALUES ('1879', 'mail2dream.com');
INSERT INTO `free_email_provider` VALUES ('1880', 'mail2dreamer.com');
INSERT INTO `free_email_provider` VALUES ('1881', 'mail2dude.com');
INSERT INTO `free_email_provider` VALUES ('1882', 'mail2dustin.com');
INSERT INTO `free_email_provider` VALUES ('1883', 'mail2dyke.com');
INSERT INTO `free_email_provider` VALUES ('1884', 'mail2dylan.com');
INSERT INTO `free_email_provider` VALUES ('1885', 'mail2earl.com');
INSERT INTO `free_email_provider` VALUES ('1886', 'mail2earth.com');
INSERT INTO `free_email_provider` VALUES ('1887', 'mail2eastend.com');
INSERT INTO `free_email_provider` VALUES ('1888', 'mail2eat.com');
INSERT INTO `free_email_provider` VALUES ('1889', 'mail2economist.com');
INSERT INTO `free_email_provider` VALUES ('1890', 'mail2ecuador.com');
INSERT INTO `free_email_provider` VALUES ('1891', 'mail2eddie.com');
INSERT INTO `free_email_provider` VALUES ('1892', 'mail2edgar.com');
INSERT INTO `free_email_provider` VALUES ('1893', 'mail2edwin.com');
INSERT INTO `free_email_provider` VALUES ('1894', 'mail2egypt.com');
INSERT INTO `free_email_provider` VALUES ('1895', 'mail2electron.com');
INSERT INTO `free_email_provider` VALUES ('1896', 'mail2eli.com');
INSERT INTO `free_email_provider` VALUES ('1897', 'mail2elizabeth.com');
INSERT INTO `free_email_provider` VALUES ('1898', 'mail2ellen.com');
INSERT INTO `free_email_provider` VALUES ('1899', 'mail2elliot.com');
INSERT INTO `free_email_provider` VALUES ('1900', 'mail2elsalvador.com');
INSERT INTO `free_email_provider` VALUES ('1901', 'mail2elvis.com');
INSERT INTO `free_email_provider` VALUES ('1902', 'mail2emergency.com');
INSERT INTO `free_email_provider` VALUES ('1903', 'mail2emily.com');
INSERT INTO `free_email_provider` VALUES ('1904', 'mail2engineer.com');
INSERT INTO `free_email_provider` VALUES ('1905', 'mail2english.com');
INSERT INTO `free_email_provider` VALUES ('1906', 'mail2environmentalist.com');
INSERT INTO `free_email_provider` VALUES ('1907', 'mail2eos.com');
INSERT INTO `free_email_provider` VALUES ('1908', 'mail2eric.com');
INSERT INTO `free_email_provider` VALUES ('1909', 'mail2erica.com');
INSERT INTO `free_email_provider` VALUES ('1910', 'mail2erin.com');
INSERT INTO `free_email_provider` VALUES ('1911', 'mail2erinyes.com');
INSERT INTO `free_email_provider` VALUES ('1912', 'mail2eris.com');
INSERT INTO `free_email_provider` VALUES ('1913', 'mail2eritrea.com');
INSERT INTO `free_email_provider` VALUES ('1914', 'mail2ernie.com');
INSERT INTO `free_email_provider` VALUES ('1915', 'mail2eros.com');
INSERT INTO `free_email_provider` VALUES ('1916', 'mail2estonia.com');
INSERT INTO `free_email_provider` VALUES ('1917', 'mail2ethan.com');
INSERT INTO `free_email_provider` VALUES ('1918', 'mail2ethiopia.com');
INSERT INTO `free_email_provider` VALUES ('1919', 'mail2eu.com');
INSERT INTO `free_email_provider` VALUES ('1920', 'mail2europe.com');
INSERT INTO `free_email_provider` VALUES ('1921', 'mail2eurus.com');
INSERT INTO `free_email_provider` VALUES ('1922', 'mail2eva.com');
INSERT INTO `free_email_provider` VALUES ('1923', 'mail2evan.com');
INSERT INTO `free_email_provider` VALUES ('1924', 'mail2evelyn.com');
INSERT INTO `free_email_provider` VALUES ('1925', 'mail2everything.com');
INSERT INTO `free_email_provider` VALUES ('1926', 'mail2exciting.com');
INSERT INTO `free_email_provider` VALUES ('1927', 'mail2expert.com');
INSERT INTO `free_email_provider` VALUES ('1928', 'mail2fairy.com');
INSERT INTO `free_email_provider` VALUES ('1929', 'mail2faith.com');
INSERT INTO `free_email_provider` VALUES ('1930', 'mail2fanatic.com');
INSERT INTO `free_email_provider` VALUES ('1931', 'mail2fancy.com');
INSERT INTO `free_email_provider` VALUES ('1932', 'mail2fantasy.com');
INSERT INTO `free_email_provider` VALUES ('1933', 'mail2farm.com');
INSERT INTO `free_email_provider` VALUES ('1934', 'mail2farmer.com');
INSERT INTO `free_email_provider` VALUES ('1935', 'mail2fashion.com');
INSERT INTO `free_email_provider` VALUES ('1936', 'mail2fat.com');
INSERT INTO `free_email_provider` VALUES ('1937', 'mail2feeling.com');
INSERT INTO `free_email_provider` VALUES ('1938', 'mail2female.com');
INSERT INTO `free_email_provider` VALUES ('1939', 'mail2fever.com');
INSERT INTO `free_email_provider` VALUES ('1940', 'mail2fighter.com');
INSERT INTO `free_email_provider` VALUES ('1941', 'mail2fiji.com');
INSERT INTO `free_email_provider` VALUES ('1942', 'mail2filmfestival.com');
INSERT INTO `free_email_provider` VALUES ('1943', 'mail2films.com');
INSERT INTO `free_email_provider` VALUES ('1944', 'mail2finance.com');
INSERT INTO `free_email_provider` VALUES ('1945', 'mail2finland.com');
INSERT INTO `free_email_provider` VALUES ('1946', 'mail2fireman.com');
INSERT INTO `free_email_provider` VALUES ('1947', 'mail2firm.com');
INSERT INTO `free_email_provider` VALUES ('1948', 'mail2fisherman.com');
INSERT INTO `free_email_provider` VALUES ('1949', 'mail2flexible.com');
INSERT INTO `free_email_provider` VALUES ('1950', 'mail2florence.com');
INSERT INTO `free_email_provider` VALUES ('1951', 'mail2florida.com');
INSERT INTO `free_email_provider` VALUES ('1952', 'mail2floyd.com');
INSERT INTO `free_email_provider` VALUES ('1953', 'mail2fly.com');
INSERT INTO `free_email_provider` VALUES ('1954', 'mail2fond.com');
INSERT INTO `free_email_provider` VALUES ('1955', 'mail2fondness.com');
INSERT INTO `free_email_provider` VALUES ('1956', 'mail2football.com');
INSERT INTO `free_email_provider` VALUES ('1957', 'mail2footballfan.com');
INSERT INTO `free_email_provider` VALUES ('1958', 'mail2found.com');
INSERT INTO `free_email_provider` VALUES ('1959', 'mail2france.com');
INSERT INTO `free_email_provider` VALUES ('1960', 'mail2frank.com');
INSERT INTO `free_email_provider` VALUES ('1961', 'mail2frankfurt.com');
INSERT INTO `free_email_provider` VALUES ('1962', 'mail2franklin.com');
INSERT INTO `free_email_provider` VALUES ('1963', 'mail2fred.com');
INSERT INTO `free_email_provider` VALUES ('1964', 'mail2freddie.com');
INSERT INTO `free_email_provider` VALUES ('1965', 'mail2free.com');
INSERT INTO `free_email_provider` VALUES ('1966', 'mail2freedom.com');
INSERT INTO `free_email_provider` VALUES ('1967', 'mail2french.com');
INSERT INTO `free_email_provider` VALUES ('1968', 'mail2freudian.com');
INSERT INTO `free_email_provider` VALUES ('1969', 'mail2friendship.com');
INSERT INTO `free_email_provider` VALUES ('1970', 'mail2from.com');
INSERT INTO `free_email_provider` VALUES ('1971', 'mail2fun.com');
INSERT INTO `free_email_provider` VALUES ('1972', 'mail2gabon.com');
INSERT INTO `free_email_provider` VALUES ('1973', 'mail2gabriel.com');
INSERT INTO `free_email_provider` VALUES ('1974', 'mail2gail.com');
INSERT INTO `free_email_provider` VALUES ('1975', 'mail2galaxy.com');
INSERT INTO `free_email_provider` VALUES ('1976', 'mail2gambia.com');
INSERT INTO `free_email_provider` VALUES ('1977', 'mail2games.com');
INSERT INTO `free_email_provider` VALUES ('1978', 'mail2gary.com');
INSERT INTO `free_email_provider` VALUES ('1979', 'mail2gavin.com');
INSERT INTO `free_email_provider` VALUES ('1980', 'mail2gemini.com');
INSERT INTO `free_email_provider` VALUES ('1981', 'mail2gene.com');
INSERT INTO `free_email_provider` VALUES ('1982', 'mail2genes.com');
INSERT INTO `free_email_provider` VALUES ('1983', 'mail2geneva.com');
INSERT INTO `free_email_provider` VALUES ('1984', 'mail2george.com');
INSERT INTO `free_email_provider` VALUES ('1985', 'mail2georgia.com');
INSERT INTO `free_email_provider` VALUES ('1986', 'mail2gerald.com');
INSERT INTO `free_email_provider` VALUES ('1987', 'mail2german.com');
INSERT INTO `free_email_provider` VALUES ('1988', 'mail2germany.com');
INSERT INTO `free_email_provider` VALUES ('1989', 'mail2ghana.com');
INSERT INTO `free_email_provider` VALUES ('1990', 'mail2gilbert.com');
INSERT INTO `free_email_provider` VALUES ('1991', 'mail2gina.com');
INSERT INTO `free_email_provider` VALUES ('1992', 'mail2girl.com');
INSERT INTO `free_email_provider` VALUES ('1993', 'mail2glen.com');
INSERT INTO `free_email_provider` VALUES ('1994', 'mail2gloria.com');
INSERT INTO `free_email_provider` VALUES ('1995', 'mail2goddess.com');
INSERT INTO `free_email_provider` VALUES ('1996', 'mail2gold.com');
INSERT INTO `free_email_provider` VALUES ('1997', 'mail2golfclub.com');
INSERT INTO `free_email_provider` VALUES ('1998', 'mail2golfer.com');
INSERT INTO `free_email_provider` VALUES ('1999', 'mail2gordon.com');
INSERT INTO `free_email_provider` VALUES ('2000', 'mail2government.com');
INSERT INTO `free_email_provider` VALUES ('2001', 'mail2grab.com');
INSERT INTO `free_email_provider` VALUES ('2002', 'mail2grace.com');
INSERT INTO `free_email_provider` VALUES ('2003', 'mail2graham.com');
INSERT INTO `free_email_provider` VALUES ('2004', 'mail2grandma.com');
INSERT INTO `free_email_provider` VALUES ('2005', 'mail2grandpa.com');
INSERT INTO `free_email_provider` VALUES ('2006', 'mail2grant.com');
INSERT INTO `free_email_provider` VALUES ('2007', 'mail2greece.com');
INSERT INTO `free_email_provider` VALUES ('2008', 'mail2green.com');
INSERT INTO `free_email_provider` VALUES ('2009', 'mail2greg.com');
INSERT INTO `free_email_provider` VALUES ('2010', 'mail2grenada.com');
INSERT INTO `free_email_provider` VALUES ('2011', 'mail2gsm.com');
INSERT INTO `free_email_provider` VALUES ('2012', 'mail2guard.com');
INSERT INTO `free_email_provider` VALUES ('2013', 'mail2guatemala.com');
INSERT INTO `free_email_provider` VALUES ('2014', 'mail2guy.com');
INSERT INTO `free_email_provider` VALUES ('2015', 'mail2hades.com');
INSERT INTO `free_email_provider` VALUES ('2016', 'mail2haiti.com');
INSERT INTO `free_email_provider` VALUES ('2017', 'mail2hal.com');
INSERT INTO `free_email_provider` VALUES ('2018', 'mail2handhelds.com');
INSERT INTO `free_email_provider` VALUES ('2019', 'mail2hank.com');
INSERT INTO `free_email_provider` VALUES ('2020', 'mail2hannah.com');
INSERT INTO `free_email_provider` VALUES ('2021', 'mail2harold.com');
INSERT INTO `free_email_provider` VALUES ('2022', 'mail2harry.com');
INSERT INTO `free_email_provider` VALUES ('2023', 'mail2hawaii.com');
INSERT INTO `free_email_provider` VALUES ('2024', 'mail2headhunter.com');
INSERT INTO `free_email_provider` VALUES ('2025', 'mail2heal.com');
INSERT INTO `free_email_provider` VALUES ('2026', 'mail2heather.com');
INSERT INTO `free_email_provider` VALUES ('2027', 'mail2heaven.com');
INSERT INTO `free_email_provider` VALUES ('2028', 'mail2hebe.com');
INSERT INTO `free_email_provider` VALUES ('2029', 'mail2hecate.com');
INSERT INTO `free_email_provider` VALUES ('2030', 'mail2heidi.com');
INSERT INTO `free_email_provider` VALUES ('2031', 'mail2helen.com');
INSERT INTO `free_email_provider` VALUES ('2032', 'mail2hell.com');
INSERT INTO `free_email_provider` VALUES ('2033', 'mail2help.com');
INSERT INTO `free_email_provider` VALUES ('2034', 'mail2helpdesk.com');
INSERT INTO `free_email_provider` VALUES ('2035', 'mail2henry.com');
INSERT INTO `free_email_provider` VALUES ('2036', 'mail2hephaestus.com');
INSERT INTO `free_email_provider` VALUES ('2037', 'mail2hera.com');
INSERT INTO `free_email_provider` VALUES ('2038', 'mail2hercules.com');
INSERT INTO `free_email_provider` VALUES ('2039', 'mail2herman.com');
INSERT INTO `free_email_provider` VALUES ('2040', 'mail2hermes.com');
INSERT INTO `free_email_provider` VALUES ('2041', 'mail2hespera.com');
INSERT INTO `free_email_provider` VALUES ('2042', 'mail2hestia.com');
INSERT INTO `free_email_provider` VALUES ('2043', 'mail2highschool.com');
INSERT INTO `free_email_provider` VALUES ('2044', 'mail2hindu.com');
INSERT INTO `free_email_provider` VALUES ('2045', 'mail2hip.com');
INSERT INTO `free_email_provider` VALUES ('2046', 'mail2hiphop.com');
INSERT INTO `free_email_provider` VALUES ('2047', 'mail2holland.com');
INSERT INTO `free_email_provider` VALUES ('2048', 'mail2holly.com');
INSERT INTO `free_email_provider` VALUES ('2049', 'mail2hollywood.com');
INSERT INTO `free_email_provider` VALUES ('2050', 'mail2homer.com');
INSERT INTO `free_email_provider` VALUES ('2051', 'mail2honduras.com');
INSERT INTO `free_email_provider` VALUES ('2052', 'mail2honey.com');
INSERT INTO `free_email_provider` VALUES ('2053', 'mail2hongkong.com');
INSERT INTO `free_email_provider` VALUES ('2054', 'mail2hope.com');
INSERT INTO `free_email_provider` VALUES ('2055', 'mail2horse.com');
INSERT INTO `free_email_provider` VALUES ('2056', 'mail2hot.com');
INSERT INTO `free_email_provider` VALUES ('2057', 'mail2hotel.com');
INSERT INTO `free_email_provider` VALUES ('2058', 'mail2houston.com');
INSERT INTO `free_email_provider` VALUES ('2059', 'mail2howard.com');
INSERT INTO `free_email_provider` VALUES ('2060', 'mail2hugh.com');
INSERT INTO `free_email_provider` VALUES ('2061', 'mail2human.com');
INSERT INTO `free_email_provider` VALUES ('2062', 'mail2hungary.com');
INSERT INTO `free_email_provider` VALUES ('2063', 'mail2hungry.com');
INSERT INTO `free_email_provider` VALUES ('2064', 'mail2hygeia.com');
INSERT INTO `free_email_provider` VALUES ('2065', 'mail2hyperspace.com');
INSERT INTO `free_email_provider` VALUES ('2066', 'mail2hypnos.com');
INSERT INTO `free_email_provider` VALUES ('2067', 'mail2ian.com');
INSERT INTO `free_email_provider` VALUES ('2068', 'mail2ice-cream.com');
INSERT INTO `free_email_provider` VALUES ('2069', 'mail2iceland.com');
INSERT INTO `free_email_provider` VALUES ('2070', 'mail2idaho.com');
INSERT INTO `free_email_provider` VALUES ('2071', 'mail2idontknow.com');
INSERT INTO `free_email_provider` VALUES ('2072', 'mail2illinois.com');
INSERT INTO `free_email_provider` VALUES ('2073', 'mail2imam.com');
INSERT INTO `free_email_provider` VALUES ('2074', 'mail2in.com');
INSERT INTO `free_email_provider` VALUES ('2075', 'mail2india.com');
INSERT INTO `free_email_provider` VALUES ('2076', 'mail2indian.com');
INSERT INTO `free_email_provider` VALUES ('2077', 'mail2indiana.com');
INSERT INTO `free_email_provider` VALUES ('2078', 'mail2indonesia.com');
INSERT INTO `free_email_provider` VALUES ('2079', 'mail2infinity.com');
INSERT INTO `free_email_provider` VALUES ('2080', 'mail2intense.com');
INSERT INTO `free_email_provider` VALUES ('2081', 'mail2iowa.com');
INSERT INTO `free_email_provider` VALUES ('2082', 'mail2iran.com');
INSERT INTO `free_email_provider` VALUES ('2083', 'mail2iraq.com');
INSERT INTO `free_email_provider` VALUES ('2084', 'mail2ireland.com');
INSERT INTO `free_email_provider` VALUES ('2085', 'mail2irene.com');
INSERT INTO `free_email_provider` VALUES ('2086', 'mail2iris.com');
INSERT INTO `free_email_provider` VALUES ('2087', 'mail2irresistible.com');
INSERT INTO `free_email_provider` VALUES ('2088', 'mail2irving.com');
INSERT INTO `free_email_provider` VALUES ('2089', 'mail2irwin.com');
INSERT INTO `free_email_provider` VALUES ('2090', 'mail2isaac.com');
INSERT INTO `free_email_provider` VALUES ('2091', 'mail2israel.com');
INSERT INTO `free_email_provider` VALUES ('2092', 'mail2italian.com');
INSERT INTO `free_email_provider` VALUES ('2093', 'mail2italy.com');
INSERT INTO `free_email_provider` VALUES ('2094', 'mail2jackie.com');
INSERT INTO `free_email_provider` VALUES ('2095', 'mail2jacob.com');
INSERT INTO `free_email_provider` VALUES ('2096', 'mail2jail.com');
INSERT INTO `free_email_provider` VALUES ('2097', 'mail2jaime.com');
INSERT INTO `free_email_provider` VALUES ('2098', 'mail2jake.com');
INSERT INTO `free_email_provider` VALUES ('2099', 'mail2jamaica.com');
INSERT INTO `free_email_provider` VALUES ('2100', 'mail2james.com');
INSERT INTO `free_email_provider` VALUES ('2101', 'mail2jamie.com');
INSERT INTO `free_email_provider` VALUES ('2102', 'mail2jan.com');
INSERT INTO `free_email_provider` VALUES ('2103', 'mail2jane.com');
INSERT INTO `free_email_provider` VALUES ('2104', 'mail2janet.com');
INSERT INTO `free_email_provider` VALUES ('2105', 'mail2janice.com');
INSERT INTO `free_email_provider` VALUES ('2106', 'mail2japan.com');
INSERT INTO `free_email_provider` VALUES ('2107', 'mail2japanese.com');
INSERT INTO `free_email_provider` VALUES ('2108', 'mail2jasmine.com');
INSERT INTO `free_email_provider` VALUES ('2109', 'mail2jason.com');
INSERT INTO `free_email_provider` VALUES ('2110', 'mail2java.com');
INSERT INTO `free_email_provider` VALUES ('2111', 'mail2jay.com');
INSERT INTO `free_email_provider` VALUES ('2112', 'mail2jazz.com');
INSERT INTO `free_email_provider` VALUES ('2113', 'mail2jed.com');
INSERT INTO `free_email_provider` VALUES ('2114', 'mail2jeffrey.com');
INSERT INTO `free_email_provider` VALUES ('2115', 'mail2jennifer.com');
INSERT INTO `free_email_provider` VALUES ('2116', 'mail2jenny.com');
INSERT INTO `free_email_provider` VALUES ('2117', 'mail2jeremy.com');
INSERT INTO `free_email_provider` VALUES ('2118', 'mail2jerry.com');
INSERT INTO `free_email_provider` VALUES ('2119', 'mail2jessica.com');
INSERT INTO `free_email_provider` VALUES ('2120', 'mail2jessie.com');
INSERT INTO `free_email_provider` VALUES ('2121', 'mail2jesus.com');
INSERT INTO `free_email_provider` VALUES ('2122', 'mail2jew.com');
INSERT INTO `free_email_provider` VALUES ('2123', 'mail2jeweler.com');
INSERT INTO `free_email_provider` VALUES ('2124', 'mail2jim.com');
INSERT INTO `free_email_provider` VALUES ('2125', 'mail2jimmy.com');
INSERT INTO `free_email_provider` VALUES ('2126', 'mail2joan.com');
INSERT INTO `free_email_provider` VALUES ('2127', 'mail2joann.com');
INSERT INTO `free_email_provider` VALUES ('2128', 'mail2joanna.com');
INSERT INTO `free_email_provider` VALUES ('2129', 'mail2jody.com');
INSERT INTO `free_email_provider` VALUES ('2130', 'mail2joe.com');
INSERT INTO `free_email_provider` VALUES ('2131', 'mail2joel.com');
INSERT INTO `free_email_provider` VALUES ('2132', 'mail2joey.com');
INSERT INTO `free_email_provider` VALUES ('2133', 'mail2john.com');
INSERT INTO `free_email_provider` VALUES ('2134', 'mail2join.com');
INSERT INTO `free_email_provider` VALUES ('2135', 'mail2jon.com');
INSERT INTO `free_email_provider` VALUES ('2136', 'mail2jonathan.com');
INSERT INTO `free_email_provider` VALUES ('2137', 'mail2jones.com');
INSERT INTO `free_email_provider` VALUES ('2138', 'mail2jordan.com');
INSERT INTO `free_email_provider` VALUES ('2139', 'mail2joseph.com');
INSERT INTO `free_email_provider` VALUES ('2140', 'mail2josh.com');
INSERT INTO `free_email_provider` VALUES ('2141', 'mail2joy.com');
INSERT INTO `free_email_provider` VALUES ('2142', 'mail2juan.com');
INSERT INTO `free_email_provider` VALUES ('2143', 'mail2judge.com');
INSERT INTO `free_email_provider` VALUES ('2144', 'mail2judy.com');
INSERT INTO `free_email_provider` VALUES ('2145', 'mail2juggler.com');
INSERT INTO `free_email_provider` VALUES ('2146', 'mail2julian.com');
INSERT INTO `free_email_provider` VALUES ('2147', 'mail2julie.com');
INSERT INTO `free_email_provider` VALUES ('2148', 'mail2jumbo.com');
INSERT INTO `free_email_provider` VALUES ('2149', 'mail2junk.com');
INSERT INTO `free_email_provider` VALUES ('2150', 'mail2justin.com');
INSERT INTO `free_email_provider` VALUES ('2151', 'mail2justme.com');
INSERT INTO `free_email_provider` VALUES ('2152', 'mail2kansas.com');
INSERT INTO `free_email_provider` VALUES ('2153', 'mail2karate.com');
INSERT INTO `free_email_provider` VALUES ('2154', 'mail2karen.com');
INSERT INTO `free_email_provider` VALUES ('2155', 'mail2karl.com');
INSERT INTO `free_email_provider` VALUES ('2156', 'mail2karma.com');
INSERT INTO `free_email_provider` VALUES ('2157', 'mail2kathleen.com');
INSERT INTO `free_email_provider` VALUES ('2158', 'mail2kathy.com');
INSERT INTO `free_email_provider` VALUES ('2159', 'mail2katie.com');
INSERT INTO `free_email_provider` VALUES ('2160', 'mail2kay.com');
INSERT INTO `free_email_provider` VALUES ('2161', 'mail2kazakhstan.com');
INSERT INTO `free_email_provider` VALUES ('2162', 'mail2keen.com');
INSERT INTO `free_email_provider` VALUES ('2163', 'mail2keith.com');
INSERT INTO `free_email_provider` VALUES ('2164', 'mail2kelly.com');
INSERT INTO `free_email_provider` VALUES ('2165', 'mail2kelsey.com');
INSERT INTO `free_email_provider` VALUES ('2166', 'mail2ken.com');
INSERT INTO `free_email_provider` VALUES ('2167', 'mail2kendall.com');
INSERT INTO `free_email_provider` VALUES ('2168', 'mail2kennedy.com');
INSERT INTO `free_email_provider` VALUES ('2169', 'mail2kenneth.com');
INSERT INTO `free_email_provider` VALUES ('2170', 'mail2kenny.com');
INSERT INTO `free_email_provider` VALUES ('2171', 'mail2kentucky.com');
INSERT INTO `free_email_provider` VALUES ('2172', 'mail2kenya.com');
INSERT INTO `free_email_provider` VALUES ('2173', 'mail2kerry.com');
INSERT INTO `free_email_provider` VALUES ('2174', 'mail2kevin.com');
INSERT INTO `free_email_provider` VALUES ('2175', 'mail2kim.com');
INSERT INTO `free_email_provider` VALUES ('2176', 'mail2kimberly.com');
INSERT INTO `free_email_provider` VALUES ('2177', 'mail2king.com');
INSERT INTO `free_email_provider` VALUES ('2178', 'mail2kirk.com');
INSERT INTO `free_email_provider` VALUES ('2179', 'mail2kiss.com');
INSERT INTO `free_email_provider` VALUES ('2180', 'mail2kosher.com');
INSERT INTO `free_email_provider` VALUES ('2181', 'mail2kristin.com');
INSERT INTO `free_email_provider` VALUES ('2182', 'mail2kurt.com');
INSERT INTO `free_email_provider` VALUES ('2183', 'mail2kuwait.com');
INSERT INTO `free_email_provider` VALUES ('2184', 'mail2kyle.com');
INSERT INTO `free_email_provider` VALUES ('2185', 'mail2kyrgyzstan.com');
INSERT INTO `free_email_provider` VALUES ('2186', 'mail2la.com');
INSERT INTO `free_email_provider` VALUES ('2187', 'mail2lacrosse.com');
INSERT INTO `free_email_provider` VALUES ('2188', 'mail2lance.com');
INSERT INTO `free_email_provider` VALUES ('2189', 'mail2lao.com');
INSERT INTO `free_email_provider` VALUES ('2190', 'mail2larry.com');
INSERT INTO `free_email_provider` VALUES ('2191', 'mail2latvia.com');
INSERT INTO `free_email_provider` VALUES ('2192', 'mail2laugh.com');
INSERT INTO `free_email_provider` VALUES ('2193', 'mail2laura.com');
INSERT INTO `free_email_provider` VALUES ('2194', 'mail2lauren.com');
INSERT INTO `free_email_provider` VALUES ('2195', 'mail2laurie.com');
INSERT INTO `free_email_provider` VALUES ('2196', 'mail2lawrence.com');
INSERT INTO `free_email_provider` VALUES ('2197', 'mail2lawyer.com');
INSERT INTO `free_email_provider` VALUES ('2198', 'mail2lebanon.com');
INSERT INTO `free_email_provider` VALUES ('2199', 'mail2lee.com');
INSERT INTO `free_email_provider` VALUES ('2200', 'mail2leo.com');
INSERT INTO `free_email_provider` VALUES ('2201', 'mail2leon.com');
INSERT INTO `free_email_provider` VALUES ('2202', 'mail2leonard.com');
INSERT INTO `free_email_provider` VALUES ('2203', 'mail2leone.com');
INSERT INTO `free_email_provider` VALUES ('2204', 'mail2leslie.com');
INSERT INTO `free_email_provider` VALUES ('2205', 'mail2letter.com');
INSERT INTO `free_email_provider` VALUES ('2206', 'mail2liberia.com');
INSERT INTO `free_email_provider` VALUES ('2207', 'mail2libertarian.com');
INSERT INTO `free_email_provider` VALUES ('2208', 'mail2libra.com');
INSERT INTO `free_email_provider` VALUES ('2209', 'mail2libya.com');
INSERT INTO `free_email_provider` VALUES ('2210', 'mail2liechtenstein.com');
INSERT INTO `free_email_provider` VALUES ('2211', 'mail2life.com');
INSERT INTO `free_email_provider` VALUES ('2212', 'mail2linda.com');
INSERT INTO `free_email_provider` VALUES ('2213', 'mail2linux.com');
INSERT INTO `free_email_provider` VALUES ('2214', 'mail2lionel.com');
INSERT INTO `free_email_provider` VALUES ('2215', 'mail2lipstick.com');
INSERT INTO `free_email_provider` VALUES ('2216', 'mail2liquid.com');
INSERT INTO `free_email_provider` VALUES ('2217', 'mail2lisa.com');
INSERT INTO `free_email_provider` VALUES ('2218', 'mail2lithuania.com');
INSERT INTO `free_email_provider` VALUES ('2219', 'mail2litigator.com');
INSERT INTO `free_email_provider` VALUES ('2220', 'mail2liz.com');
INSERT INTO `free_email_provider` VALUES ('2221', 'mail2lloyd.com');
INSERT INTO `free_email_provider` VALUES ('2222', 'mail2lois.com');
INSERT INTO `free_email_provider` VALUES ('2223', 'mail2lola.com');
INSERT INTO `free_email_provider` VALUES ('2224', 'mail2london.com');
INSERT INTO `free_email_provider` VALUES ('2225', 'mail2looking.com');
INSERT INTO `free_email_provider` VALUES ('2226', 'mail2lori.com');
INSERT INTO `free_email_provider` VALUES ('2227', 'mail2lost.com');
INSERT INTO `free_email_provider` VALUES ('2228', 'mail2lou.com');
INSERT INTO `free_email_provider` VALUES ('2229', 'mail2louis.com');
INSERT INTO `free_email_provider` VALUES ('2230', 'mail2louisiana.com');
INSERT INTO `free_email_provider` VALUES ('2231', 'mail2lovable.com');
INSERT INTO `free_email_provider` VALUES ('2232', 'mail2love.com');
INSERT INTO `free_email_provider` VALUES ('2233', 'mail2lucky.com');
INSERT INTO `free_email_provider` VALUES ('2234', 'mail2lucy.com');
INSERT INTO `free_email_provider` VALUES ('2235', 'mail2lunch.com');
INSERT INTO `free_email_provider` VALUES ('2236', 'mail2lust.com');
INSERT INTO `free_email_provider` VALUES ('2237', 'mail2luxembourg.com');
INSERT INTO `free_email_provider` VALUES ('2238', 'mail2luxury.com');
INSERT INTO `free_email_provider` VALUES ('2239', 'mail2lyle.com');
INSERT INTO `free_email_provider` VALUES ('2240', 'mail2lynn.com');
INSERT INTO `free_email_provider` VALUES ('2241', 'mail2madagascar.com');
INSERT INTO `free_email_provider` VALUES ('2242', 'mail2madison.com');
INSERT INTO `free_email_provider` VALUES ('2243', 'mail2madrid.com');
INSERT INTO `free_email_provider` VALUES ('2244', 'mail2maggie.com');
INSERT INTO `free_email_provider` VALUES ('2245', 'mail2mail4.com');
INSERT INTO `free_email_provider` VALUES ('2246', 'mail2maine.com');
INSERT INTO `free_email_provider` VALUES ('2247', 'mail2malawi.com');
INSERT INTO `free_email_provider` VALUES ('2248', 'mail2malaysia.com');
INSERT INTO `free_email_provider` VALUES ('2249', 'mail2maldives.com');
INSERT INTO `free_email_provider` VALUES ('2250', 'mail2mali.com');
INSERT INTO `free_email_provider` VALUES ('2251', 'mail2malta.com');
INSERT INTO `free_email_provider` VALUES ('2252', 'mail2mambo.com');
INSERT INTO `free_email_provider` VALUES ('2253', 'mail2man.com');
INSERT INTO `free_email_provider` VALUES ('2254', 'mail2mandy.com');
INSERT INTO `free_email_provider` VALUES ('2255', 'mail2manhunter.com');
INSERT INTO `free_email_provider` VALUES ('2256', 'mail2mankind.com');
INSERT INTO `free_email_provider` VALUES ('2257', 'mail2many.com');
INSERT INTO `free_email_provider` VALUES ('2258', 'mail2marc.com');
INSERT INTO `free_email_provider` VALUES ('2259', 'mail2marcia.com');
INSERT INTO `free_email_provider` VALUES ('2260', 'mail2margaret.com');
INSERT INTO `free_email_provider` VALUES ('2261', 'mail2margie.com');
INSERT INTO `free_email_provider` VALUES ('2262', 'mail2marhaba.com');
INSERT INTO `free_email_provider` VALUES ('2263', 'mail2maria.com');
INSERT INTO `free_email_provider` VALUES ('2264', 'mail2marilyn.com');
INSERT INTO `free_email_provider` VALUES ('2265', 'mail2marines.com');
INSERT INTO `free_email_provider` VALUES ('2266', 'mail2mark.com');
INSERT INTO `free_email_provider` VALUES ('2267', 'mail2marriage.com');
INSERT INTO `free_email_provider` VALUES ('2268', 'mail2married.com');
INSERT INTO `free_email_provider` VALUES ('2269', 'mail2marries.com');
INSERT INTO `free_email_provider` VALUES ('2270', 'mail2mars.com');
INSERT INTO `free_email_provider` VALUES ('2271', 'mail2marsha.com');
INSERT INTO `free_email_provider` VALUES ('2272', 'mail2marshallislands.com');
INSERT INTO `free_email_provider` VALUES ('2273', 'mail2martha.com');
INSERT INTO `free_email_provider` VALUES ('2274', 'mail2martin.com');
INSERT INTO `free_email_provider` VALUES ('2275', 'mail2marty.com');
INSERT INTO `free_email_provider` VALUES ('2276', 'mail2marvin.com');
INSERT INTO `free_email_provider` VALUES ('2277', 'mail2mary.com');
INSERT INTO `free_email_provider` VALUES ('2278', 'mail2maryland.com');
INSERT INTO `free_email_provider` VALUES ('2279', 'mail2mason.com');
INSERT INTO `free_email_provider` VALUES ('2280', 'mail2massachusetts.com');
INSERT INTO `free_email_provider` VALUES ('2281', 'mail2matt.com');
INSERT INTO `free_email_provider` VALUES ('2282', 'mail2matthew.com');
INSERT INTO `free_email_provider` VALUES ('2283', 'mail2maurice.com');
INSERT INTO `free_email_provider` VALUES ('2284', 'mail2mauritania.com');
INSERT INTO `free_email_provider` VALUES ('2285', 'mail2mauritius.com');
INSERT INTO `free_email_provider` VALUES ('2286', 'mail2max.com');
INSERT INTO `free_email_provider` VALUES ('2287', 'mail2maxwell.com');
INSERT INTO `free_email_provider` VALUES ('2288', 'mail2maybe.com');
INSERT INTO `free_email_provider` VALUES ('2289', 'mail2mba.com');
INSERT INTO `free_email_provider` VALUES ('2290', 'mail2me4u.com');
INSERT INTO `free_email_provider` VALUES ('2291', 'mail2mechanic.com');
INSERT INTO `free_email_provider` VALUES ('2292', 'mail2medieval.com');
INSERT INTO `free_email_provider` VALUES ('2293', 'mail2megan.com');
INSERT INTO `free_email_provider` VALUES ('2294', 'mail2mel.com');
INSERT INTO `free_email_provider` VALUES ('2295', 'mail2melanie.com');
INSERT INTO `free_email_provider` VALUES ('2296', 'mail2melissa.com');
INSERT INTO `free_email_provider` VALUES ('2297', 'mail2melody.com');
INSERT INTO `free_email_provider` VALUES ('2298', 'mail2member.com');
INSERT INTO `free_email_provider` VALUES ('2299', 'mail2memphis.com');
INSERT INTO `free_email_provider` VALUES ('2300', 'mail2methodist.com');
INSERT INTO `free_email_provider` VALUES ('2301', 'mail2mexican.com');
INSERT INTO `free_email_provider` VALUES ('2302', 'mail2mexico.com');
INSERT INTO `free_email_provider` VALUES ('2303', 'mail2mgz.com');
INSERT INTO `free_email_provider` VALUES ('2304', 'mail2miami.com');
INSERT INTO `free_email_provider` VALUES ('2305', 'mail2michael.com');
INSERT INTO `free_email_provider` VALUES ('2306', 'mail2michelle.com');
INSERT INTO `free_email_provider` VALUES ('2307', 'mail2michigan.com');
INSERT INTO `free_email_provider` VALUES ('2308', 'mail2mike.com');
INSERT INTO `free_email_provider` VALUES ('2309', 'mail2milan.com');
INSERT INTO `free_email_provider` VALUES ('2310', 'mail2milano.com');
INSERT INTO `free_email_provider` VALUES ('2311', 'mail2mildred.com');
INSERT INTO `free_email_provider` VALUES ('2312', 'mail2milkyway.com');
INSERT INTO `free_email_provider` VALUES ('2313', 'mail2millennium.com');
INSERT INTO `free_email_provider` VALUES ('2314', 'mail2millionaire.com');
INSERT INTO `free_email_provider` VALUES ('2315', 'mail2milton.com');
INSERT INTO `free_email_provider` VALUES ('2316', 'mail2mime.com');
INSERT INTO `free_email_provider` VALUES ('2317', 'mail2mindreader.com');
INSERT INTO `free_email_provider` VALUES ('2318', 'mail2mini.com');
INSERT INTO `free_email_provider` VALUES ('2319', 'mail2minister.com');
INSERT INTO `free_email_provider` VALUES ('2320', 'mail2minneapolis.com');
INSERT INTO `free_email_provider` VALUES ('2321', 'mail2minnesota.com');
INSERT INTO `free_email_provider` VALUES ('2322', 'mail2miracle.com');
INSERT INTO `free_email_provider` VALUES ('2323', 'mail2missionary.com');
INSERT INTO `free_email_provider` VALUES ('2324', 'mail2mississippi.com');
INSERT INTO `free_email_provider` VALUES ('2325', 'mail2missouri.com');
INSERT INTO `free_email_provider` VALUES ('2326', 'mail2mitch.com');
INSERT INTO `free_email_provider` VALUES ('2327', 'mail2model.com');
INSERT INTO `free_email_provider` VALUES ('2328', 'mail2moldova.commail2molly.com');
INSERT INTO `free_email_provider` VALUES ('2329', 'mail2mom.com');
INSERT INTO `free_email_provider` VALUES ('2330', 'mail2monaco.com');
INSERT INTO `free_email_provider` VALUES ('2331', 'mail2money.com');
INSERT INTO `free_email_provider` VALUES ('2332', 'mail2mongolia.com');
INSERT INTO `free_email_provider` VALUES ('2333', 'mail2monica.com');
INSERT INTO `free_email_provider` VALUES ('2334', 'mail2montana.com');
INSERT INTO `free_email_provider` VALUES ('2335', 'mail2monty.com');
INSERT INTO `free_email_provider` VALUES ('2336', 'mail2moon.com');
INSERT INTO `free_email_provider` VALUES ('2337', 'mail2morocco.com');
INSERT INTO `free_email_provider` VALUES ('2338', 'mail2morpheus.com');
INSERT INTO `free_email_provider` VALUES ('2339', 'mail2mors.com');
INSERT INTO `free_email_provider` VALUES ('2340', 'mail2moscow.com');
INSERT INTO `free_email_provider` VALUES ('2341', 'mail2moslem.com');
INSERT INTO `free_email_provider` VALUES ('2342', 'mail2mouseketeer.com');
INSERT INTO `free_email_provider` VALUES ('2343', 'mail2movies.com');
INSERT INTO `free_email_provider` VALUES ('2344', 'mail2mozambique.com');
INSERT INTO `free_email_provider` VALUES ('2345', 'mail2mp3.com');
INSERT INTO `free_email_provider` VALUES ('2346', 'mail2mrright.com');
INSERT INTO `free_email_provider` VALUES ('2347', 'mail2msright.com');
INSERT INTO `free_email_provider` VALUES ('2348', 'mail2museum.com');
INSERT INTO `free_email_provider` VALUES ('2349', 'mail2music.com');
INSERT INTO `free_email_provider` VALUES ('2350', 'mail2musician.com');
INSERT INTO `free_email_provider` VALUES ('2351', 'mail2muslim.com');
INSERT INTO `free_email_provider` VALUES ('2352', 'mail2my.com');
INSERT INTO `free_email_provider` VALUES ('2353', 'mail2myboat.com');
INSERT INTO `free_email_provider` VALUES ('2354', 'mail2mycar.com');
INSERT INTO `free_email_provider` VALUES ('2355', 'mail2mycell.com');
INSERT INTO `free_email_provider` VALUES ('2356', 'mail2mygsm.com');
INSERT INTO `free_email_provider` VALUES ('2357', 'mail2mylaptop.com');
INSERT INTO `free_email_provider` VALUES ('2358', 'mail2mymac.com');
INSERT INTO `free_email_provider` VALUES ('2359', 'mail2mypager.com');
INSERT INTO `free_email_provider` VALUES ('2360', 'mail2mypalm.com');
INSERT INTO `free_email_provider` VALUES ('2361', 'mail2mypc.com');
INSERT INTO `free_email_provider` VALUES ('2362', 'mail2myphone.com');
INSERT INTO `free_email_provider` VALUES ('2363', 'mail2myplane.com');
INSERT INTO `free_email_provider` VALUES ('2364', 'mail2namibia.com');
INSERT INTO `free_email_provider` VALUES ('2365', 'mail2nancy.com');
INSERT INTO `free_email_provider` VALUES ('2366', 'mail2nasdaq.com');
INSERT INTO `free_email_provider` VALUES ('2367', 'mail2nathan.com');
INSERT INTO `free_email_provider` VALUES ('2368', 'mail2nauru.com');
INSERT INTO `free_email_provider` VALUES ('2369', 'mail2navy.com');
INSERT INTO `free_email_provider` VALUES ('2370', 'mail2neal.com');
INSERT INTO `free_email_provider` VALUES ('2371', 'mail2nebraska.com');
INSERT INTO `free_email_provider` VALUES ('2372', 'mail2ned.com');
INSERT INTO `free_email_provider` VALUES ('2373', 'mail2neil.com');
INSERT INTO `free_email_provider` VALUES ('2374', 'mail2nelson.com');
INSERT INTO `free_email_provider` VALUES ('2375', 'mail2nemesis.com');
INSERT INTO `free_email_provider` VALUES ('2376', 'mail2nepal.com');
INSERT INTO `free_email_provider` VALUES ('2377', 'mail2netherlands.com');
INSERT INTO `free_email_provider` VALUES ('2378', 'mail2network.com');
INSERT INTO `free_email_provider` VALUES ('2379', 'mail2nevada.com');
INSERT INTO `free_email_provider` VALUES ('2380', 'mail2newhampshire.com');
INSERT INTO `free_email_provider` VALUES ('2381', 'mail2newjersey.com');
INSERT INTO `free_email_provider` VALUES ('2382', 'mail2newmexico.com');
INSERT INTO `free_email_provider` VALUES ('2383', 'mail2newyork.com');
INSERT INTO `free_email_provider` VALUES ('2384', 'mail2newzealand.com');
INSERT INTO `free_email_provider` VALUES ('2385', 'mail2nicaragua.com');
INSERT INTO `free_email_provider` VALUES ('2386', 'mail2nick.com');
INSERT INTO `free_email_provider` VALUES ('2387', 'mail2nicole.com');
INSERT INTO `free_email_provider` VALUES ('2388', 'mail2niger.com');
INSERT INTO `free_email_provider` VALUES ('2389', 'mail2nigeria.com');
INSERT INTO `free_email_provider` VALUES ('2390', 'mail2nike.com');
INSERT INTO `free_email_provider` VALUES ('2391', 'mail2no.com');
INSERT INTO `free_email_provider` VALUES ('2392', 'mail2noah.com');
INSERT INTO `free_email_provider` VALUES ('2393', 'mail2noel.com');
INSERT INTO `free_email_provider` VALUES ('2394', 'mail2noelle.com');
INSERT INTO `free_email_provider` VALUES ('2395', 'mail2normal.com');
INSERT INTO `free_email_provider` VALUES ('2396', 'mail2norman.com');
INSERT INTO `free_email_provider` VALUES ('2397', 'mail2northamerica.com');
INSERT INTO `free_email_provider` VALUES ('2398', 'mail2northcarolina.com');
INSERT INTO `free_email_provider` VALUES ('2399', 'mail2northdakota.com');
INSERT INTO `free_email_provider` VALUES ('2400', 'mail2northpole.com');
INSERT INTO `free_email_provider` VALUES ('2401', 'mail2norway.com');
INSERT INTO `free_email_provider` VALUES ('2402', 'mail2notus.com');
INSERT INTO `free_email_provider` VALUES ('2403', 'mail2noway.com');
INSERT INTO `free_email_provider` VALUES ('2404', 'mail2nowhere.com');
INSERT INTO `free_email_provider` VALUES ('2405', 'mail2nuclear.com');
INSERT INTO `free_email_provider` VALUES ('2406', 'mail2nun.com');
INSERT INTO `free_email_provider` VALUES ('2407', 'mail2ny.com');
INSERT INTO `free_email_provider` VALUES ('2408', 'mail2oasis.com');
INSERT INTO `free_email_provider` VALUES ('2409', 'mail2oceanographer.com');
INSERT INTO `free_email_provider` VALUES ('2410', 'mail2ohio.com');
INSERT INTO `free_email_provider` VALUES ('2411', 'mail2ok.com');
INSERT INTO `free_email_provider` VALUES ('2412', 'mail2oklahoma.com');
INSERT INTO `free_email_provider` VALUES ('2413', 'mail2oliver.com');
INSERT INTO `free_email_provider` VALUES ('2414', 'mail2oman.com');
INSERT INTO `free_email_provider` VALUES ('2415', 'mail2one.com');
INSERT INTO `free_email_provider` VALUES ('2416', 'mail2onfire.com');
INSERT INTO `free_email_provider` VALUES ('2417', 'mail2online.com');
INSERT INTO `free_email_provider` VALUES ('2418', 'mail2oops.com');
INSERT INTO `free_email_provider` VALUES ('2419', 'mail2open.com');
INSERT INTO `free_email_provider` VALUES ('2420', 'mail2ophthalmologist.com');
INSERT INTO `free_email_provider` VALUES ('2421', 'mail2optometrist.com');
INSERT INTO `free_email_provider` VALUES ('2422', 'mail2oregon.com');
INSERT INTO `free_email_provider` VALUES ('2423', 'mail2oscars.com');
INSERT INTO `free_email_provider` VALUES ('2424', 'mail2oslo.com');
INSERT INTO `free_email_provider` VALUES ('2425', 'mail2painter.com');
INSERT INTO `free_email_provider` VALUES ('2426', 'mail2pakistan.com');
INSERT INTO `free_email_provider` VALUES ('2427', 'mail2palau.com');
INSERT INTO `free_email_provider` VALUES ('2428', 'mail2pan.com');
INSERT INTO `free_email_provider` VALUES ('2429', 'mail2panama.com');
INSERT INTO `free_email_provider` VALUES ('2430', 'mail2paraguay.com');
INSERT INTO `free_email_provider` VALUES ('2431', 'mail2paralegal.com');
INSERT INTO `free_email_provider` VALUES ('2432', 'mail2paris.com');
INSERT INTO `free_email_provider` VALUES ('2433', 'mail2park.com');
INSERT INTO `free_email_provider` VALUES ('2434', 'mail2parker.com');
INSERT INTO `free_email_provider` VALUES ('2435', 'mail2party.com');
INSERT INTO `free_email_provider` VALUES ('2436', 'mail2passion.com');
INSERT INTO `free_email_provider` VALUES ('2437', 'mail2pat.com');
INSERT INTO `free_email_provider` VALUES ('2438', 'mail2patricia.com');
INSERT INTO `free_email_provider` VALUES ('2439', 'mail2patrick.com');
INSERT INTO `free_email_provider` VALUES ('2440', 'mail2patty.com');
INSERT INTO `free_email_provider` VALUES ('2441', 'mail2paul.com');
INSERT INTO `free_email_provider` VALUES ('2442', 'mail2paula.com');
INSERT INTO `free_email_provider` VALUES ('2443', 'mail2pay.com');
INSERT INTO `free_email_provider` VALUES ('2444', 'mail2peace.com');
INSERT INTO `free_email_provider` VALUES ('2445', 'mail2pediatrician.com');
INSERT INTO `free_email_provider` VALUES ('2446', 'mail2peggy.com');
INSERT INTO `free_email_provider` VALUES ('2447', 'mail2pennsylvania.com');
INSERT INTO `free_email_provider` VALUES ('2448', 'mail2perry.com');
INSERT INTO `free_email_provider` VALUES ('2449', 'mail2persephone.com');
INSERT INTO `free_email_provider` VALUES ('2450', 'mail2persian.com');
INSERT INTO `free_email_provider` VALUES ('2451', 'mail2peru.com');
INSERT INTO `free_email_provider` VALUES ('2452', 'mail2pete.com');
INSERT INTO `free_email_provider` VALUES ('2453', 'mail2peter.com');
INSERT INTO `free_email_provider` VALUES ('2454', 'mail2pharmacist.com');
INSERT INTO `free_email_provider` VALUES ('2455', 'mail2phil.com');
INSERT INTO `free_email_provider` VALUES ('2456', 'mail2philippines.com');
INSERT INTO `free_email_provider` VALUES ('2457', 'mail2phoenix.com');
INSERT INTO `free_email_provider` VALUES ('2458', 'mail2phonecall.com');
INSERT INTO `free_email_provider` VALUES ('2459', 'mail2phyllis.com');
INSERT INTO `free_email_provider` VALUES ('2460', 'mail2pickup.com');
INSERT INTO `free_email_provider` VALUES ('2461', 'mail2pilot.com');
INSERT INTO `free_email_provider` VALUES ('2462', 'mail2pisces.com');
INSERT INTO `free_email_provider` VALUES ('2463', 'mail2planet.com');
INSERT INTO `free_email_provider` VALUES ('2464', 'mail2platinum.com');
INSERT INTO `free_email_provider` VALUES ('2465', 'mail2plato.com');
INSERT INTO `free_email_provider` VALUES ('2466', 'mail2pluto.com');
INSERT INTO `free_email_provider` VALUES ('2467', 'mail2pm.com');
INSERT INTO `free_email_provider` VALUES ('2468', 'mail2podiatrist.com');
INSERT INTO `free_email_provider` VALUES ('2469', 'mail2poet.com');
INSERT INTO `free_email_provider` VALUES ('2470', 'mail2poland.com');
INSERT INTO `free_email_provider` VALUES ('2471', 'mail2policeman.com');
INSERT INTO `free_email_provider` VALUES ('2472', 'mail2policewoman.com');
INSERT INTO `free_email_provider` VALUES ('2473', 'mail2politician.com');
INSERT INTO `free_email_provider` VALUES ('2474', 'mail2pop.com');
INSERT INTO `free_email_provider` VALUES ('2475', 'mail2pope.com');
INSERT INTO `free_email_provider` VALUES ('2476', 'mail2popular.com');
INSERT INTO `free_email_provider` VALUES ('2477', 'mail2portugal.com');
INSERT INTO `free_email_provider` VALUES ('2478', 'mail2poseidon.com');
INSERT INTO `free_email_provider` VALUES ('2479', 'mail2potatohead.com');
INSERT INTO `free_email_provider` VALUES ('2480', 'mail2power.com');
INSERT INTO `free_email_provider` VALUES ('2481', 'mail2presbyterian.com');
INSERT INTO `free_email_provider` VALUES ('2482', 'mail2president.com');
INSERT INTO `free_email_provider` VALUES ('2483', 'mail2priest.com');
INSERT INTO `free_email_provider` VALUES ('2484', 'mail2prince.com');
INSERT INTO `free_email_provider` VALUES ('2485', 'mail2princess.com');
INSERT INTO `free_email_provider` VALUES ('2486', 'mail2producer.com');
INSERT INTO `free_email_provider` VALUES ('2487', 'mail2professor.com');
INSERT INTO `free_email_provider` VALUES ('2488', 'mail2protect.com');
INSERT INTO `free_email_provider` VALUES ('2489', 'mail2psychiatrist.com');
INSERT INTO `free_email_provider` VALUES ('2490', 'mail2psycho.com');
INSERT INTO `free_email_provider` VALUES ('2491', 'mail2psychologist.com');
INSERT INTO `free_email_provider` VALUES ('2492', 'mail2qatar.com');
INSERT INTO `free_email_provider` VALUES ('2493', 'mail2queen.com');
INSERT INTO `free_email_provider` VALUES ('2494', 'mail2rabbi.com');
INSERT INTO `free_email_provider` VALUES ('2495', 'mail2race.com');
INSERT INTO `free_email_provider` VALUES ('2496', 'mail2racer.com');
INSERT INTO `free_email_provider` VALUES ('2497', 'mail2rachel.com');
INSERT INTO `free_email_provider` VALUES ('2498', 'mail2rage.com');
INSERT INTO `free_email_provider` VALUES ('2499', 'mail2rainmaker.com');
INSERT INTO `free_email_provider` VALUES ('2500', 'mail2ralph.com');
INSERT INTO `free_email_provider` VALUES ('2501', 'mail2randy.com');
INSERT INTO `free_email_provider` VALUES ('2502', 'mail2rap.com');
INSERT INTO `free_email_provider` VALUES ('2503', 'mail2rare.com');
INSERT INTO `free_email_provider` VALUES ('2504', 'mail2rave.com');
INSERT INTO `free_email_provider` VALUES ('2505', 'mail2ray.com');
INSERT INTO `free_email_provider` VALUES ('2506', 'mail2raymond.com');
INSERT INTO `free_email_provider` VALUES ('2507', 'mail2realtor.com');
INSERT INTO `free_email_provider` VALUES ('2508', 'mail2rebecca.com');
INSERT INTO `free_email_provider` VALUES ('2509', 'mail2recruiter.com');
INSERT INTO `free_email_provider` VALUES ('2510', 'mail2recycle.com');
INSERT INTO `free_email_provider` VALUES ('2511', 'mail2redhead.com');
INSERT INTO `free_email_provider` VALUES ('2512', 'mail2reed.com');
INSERT INTO `free_email_provider` VALUES ('2513', 'mail2reggie.com');
INSERT INTO `free_email_provider` VALUES ('2514', 'mail2register.com');
INSERT INTO `free_email_provider` VALUES ('2515', 'mail2rent.com');
INSERT INTO `free_email_provider` VALUES ('2516', 'mail2republican.com');
INSERT INTO `free_email_provider` VALUES ('2517', 'mail2resort.com');
INSERT INTO `free_email_provider` VALUES ('2518', 'mail2rex.com');
INSERT INTO `free_email_provider` VALUES ('2519', 'mail2rhodeisland.com');
INSERT INTO `free_email_provider` VALUES ('2520', 'mail2rich.com');
INSERT INTO `free_email_provider` VALUES ('2521', 'mail2richard.com');
INSERT INTO `free_email_provider` VALUES ('2522', 'mail2ricky.com');
INSERT INTO `free_email_provider` VALUES ('2523', 'mail2ride.com');
INSERT INTO `free_email_provider` VALUES ('2524', 'mail2riley.com');
INSERT INTO `free_email_provider` VALUES ('2525', 'mail2rita.com');
INSERT INTO `free_email_provider` VALUES ('2526', 'mail2rob.com');
INSERT INTO `free_email_provider` VALUES ('2527', 'mail2robert.com');
INSERT INTO `free_email_provider` VALUES ('2528', 'mail2roberta.com');
INSERT INTO `free_email_provider` VALUES ('2529', 'mail2robin.com');
INSERT INTO `free_email_provider` VALUES ('2530', 'mail2rock.com');
INSERT INTO `free_email_provider` VALUES ('2531', 'mail2rocker.com');
INSERT INTO `free_email_provider` VALUES ('2532', 'mail2rod.com');
INSERT INTO `free_email_provider` VALUES ('2533', 'mail2rodney.com');
INSERT INTO `free_email_provider` VALUES ('2534', 'mail2romania.com');
INSERT INTO `free_email_provider` VALUES ('2535', 'mail2rome.com');
INSERT INTO `free_email_provider` VALUES ('2536', 'mail2ron.com');
INSERT INTO `free_email_provider` VALUES ('2537', 'mail2ronald.com');
INSERT INTO `free_email_provider` VALUES ('2538', 'mail2ronnie.com');
INSERT INTO `free_email_provider` VALUES ('2539', 'mail2rose.com');
INSERT INTO `free_email_provider` VALUES ('2540', 'mail2rosie.com');
INSERT INTO `free_email_provider` VALUES ('2541', 'mail2roy.com');
INSERT INTO `free_email_provider` VALUES ('2542', 'mail2rudy.com');
INSERT INTO `free_email_provider` VALUES ('2543', 'mail2rugby.com');
INSERT INTO `free_email_provider` VALUES ('2544', 'mail2runner.com');
INSERT INTO `free_email_provider` VALUES ('2545', 'mail2russell.com');
INSERT INTO `free_email_provider` VALUES ('2546', 'mail2russia.com');
INSERT INTO `free_email_provider` VALUES ('2547', 'mail2russian.com');
INSERT INTO `free_email_provider` VALUES ('2548', 'mail2rusty.com');
INSERT INTO `free_email_provider` VALUES ('2549', 'mail2ruth.com');
INSERT INTO `free_email_provider` VALUES ('2550', 'mail2rwanda.com');
INSERT INTO `free_email_provider` VALUES ('2551', 'mail2ryan.com');
INSERT INTO `free_email_provider` VALUES ('2552', 'mail2sa.com');
INSERT INTO `free_email_provider` VALUES ('2553', 'mail2sabrina.com');
INSERT INTO `free_email_provider` VALUES ('2554', 'mail2safe.com');
INSERT INTO `free_email_provider` VALUES ('2555', 'mail2sagittarius.com');
INSERT INTO `free_email_provider` VALUES ('2556', 'mail2sail.com');
INSERT INTO `free_email_provider` VALUES ('2557', 'mail2sailor.com');
INSERT INTO `free_email_provider` VALUES ('2558', 'mail2sal.com');
INSERT INTO `free_email_provider` VALUES ('2559', 'mail2salaam.com');
INSERT INTO `free_email_provider` VALUES ('2560', 'mail2sam.com');
INSERT INTO `free_email_provider` VALUES ('2561', 'mail2samantha.com');
INSERT INTO `free_email_provider` VALUES ('2562', 'mail2samoa.com');
INSERT INTO `free_email_provider` VALUES ('2563', 'mail2samurai.com');
INSERT INTO `free_email_provider` VALUES ('2564', 'mail2sandra.com');
INSERT INTO `free_email_provider` VALUES ('2565', 'mail2sandy.com');
INSERT INTO `free_email_provider` VALUES ('2566', 'mail2sanfrancisco.com');
INSERT INTO `free_email_provider` VALUES ('2567', 'mail2sanmarino.com');
INSERT INTO `free_email_provider` VALUES ('2568', 'mail2santa.com');
INSERT INTO `free_email_provider` VALUES ('2569', 'mail2sara.com');
INSERT INTO `free_email_provider` VALUES ('2570', 'mail2sarah.com');
INSERT INTO `free_email_provider` VALUES ('2571', 'mail2sat.com');
INSERT INTO `free_email_provider` VALUES ('2572', 'mail2saturn.com');
INSERT INTO `free_email_provider` VALUES ('2573', 'mail2saudi.com');
INSERT INTO `free_email_provider` VALUES ('2574', 'mail2saudiarabia.com');
INSERT INTO `free_email_provider` VALUES ('2575', 'mail2save.com');
INSERT INTO `free_email_provider` VALUES ('2576', 'mail2savings.com');
INSERT INTO `free_email_provider` VALUES ('2577', 'mail2school.com');
INSERT INTO `free_email_provider` VALUES ('2578', 'mail2scientist.com');
INSERT INTO `free_email_provider` VALUES ('2579', 'mail2scorpio.com');
INSERT INTO `free_email_provider` VALUES ('2580', 'mail2scott.com');
INSERT INTO `free_email_provider` VALUES ('2581', 'mail2sean.com');
INSERT INTO `free_email_provider` VALUES ('2582', 'mail2search.com');
INSERT INTO `free_email_provider` VALUES ('2583', 'mail2seattle.com');
INSERT INTO `free_email_provider` VALUES ('2584', 'mail2secretagent.com');
INSERT INTO `free_email_provider` VALUES ('2585', 'mail2senate.com');
INSERT INTO `free_email_provider` VALUES ('2586', 'mail2senegal.com');
INSERT INTO `free_email_provider` VALUES ('2587', 'mail2sensual.com');
INSERT INTO `free_email_provider` VALUES ('2588', 'mail2seth.com');
INSERT INTO `free_email_provider` VALUES ('2589', 'mail2sevenseas.com');
INSERT INTO `free_email_provider` VALUES ('2590', 'mail2sexy.com');
INSERT INTO `free_email_provider` VALUES ('2591', 'mail2seychelles.com');
INSERT INTO `free_email_provider` VALUES ('2592', 'mail2shane.com');
INSERT INTO `free_email_provider` VALUES ('2593', 'mail2sharon.com');
INSERT INTO `free_email_provider` VALUES ('2594', 'mail2shawn.com');
INSERT INTO `free_email_provider` VALUES ('2595', 'mail2ship.com');
INSERT INTO `free_email_provider` VALUES ('2596', 'mail2shirley.com');
INSERT INTO `free_email_provider` VALUES ('2597', 'mail2shoot.com');
INSERT INTO `free_email_provider` VALUES ('2598', 'mail2shuttle.com');
INSERT INTO `free_email_provider` VALUES ('2599', 'mail2sierraleone.com');
INSERT INTO `free_email_provider` VALUES ('2600', 'mail2simon.com');
INSERT INTO `free_email_provider` VALUES ('2601', 'mail2singapore.com');
INSERT INTO `free_email_provider` VALUES ('2602', 'mail2single.com');
INSERT INTO `free_email_provider` VALUES ('2603', 'mail2site.com');
INSERT INTO `free_email_provider` VALUES ('2604', 'mail2skater.com');
INSERT INTO `free_email_provider` VALUES ('2605', 'mail2skier.com');
INSERT INTO `free_email_provider` VALUES ('2606', 'mail2sky.com');
INSERT INTO `free_email_provider` VALUES ('2607', 'mail2sleek.com');
INSERT INTO `free_email_provider` VALUES ('2608', 'mail2slim.com');
INSERT INTO `free_email_provider` VALUES ('2609', 'mail2slovakia.com');
INSERT INTO `free_email_provider` VALUES ('2610', 'mail2slovenia.com');
INSERT INTO `free_email_provider` VALUES ('2611', 'mail2smile.com');
INSERT INTO `free_email_provider` VALUES ('2612', 'mail2smith.com');
INSERT INTO `free_email_provider` VALUES ('2613', 'mail2smooth.com');
INSERT INTO `free_email_provider` VALUES ('2614', 'mail2soccer.com');
INSERT INTO `free_email_provider` VALUES ('2615', 'mail2soccerfan.com');
INSERT INTO `free_email_provider` VALUES ('2616', 'mail2socialist.com');
INSERT INTO `free_email_provider` VALUES ('2617', 'mail2soldier.com');
INSERT INTO `free_email_provider` VALUES ('2618', 'mail2somalia.com');
INSERT INTO `free_email_provider` VALUES ('2619', 'mail2son.com');
INSERT INTO `free_email_provider` VALUES ('2620', 'mail2song.com');
INSERT INTO `free_email_provider` VALUES ('2621', 'mail2sos.com');
INSERT INTO `free_email_provider` VALUES ('2622', 'mail2sound.com');
INSERT INTO `free_email_provider` VALUES ('2623', 'mail2southafrica.com');
INSERT INTO `free_email_provider` VALUES ('2624', 'mail2southamerica.com');
INSERT INTO `free_email_provider` VALUES ('2625', 'mail2southcarolina.com');
INSERT INTO `free_email_provider` VALUES ('2626', 'mail2southdakota.com');
INSERT INTO `free_email_provider` VALUES ('2627', 'mail2southkorea.com');
INSERT INTO `free_email_provider` VALUES ('2628', 'mail2southpole.com');
INSERT INTO `free_email_provider` VALUES ('2629', 'mail2spain.com');
INSERT INTO `free_email_provider` VALUES ('2630', 'mail2spanish.com');
INSERT INTO `free_email_provider` VALUES ('2631', 'mail2spare.com');
INSERT INTO `free_email_provider` VALUES ('2632', 'mail2spectrum.com');
INSERT INTO `free_email_provider` VALUES ('2633', 'mail2splash.com');
INSERT INTO `free_email_provider` VALUES ('2634', 'mail2sponsor.com');
INSERT INTO `free_email_provider` VALUES ('2635', 'mail2sports.com');
INSERT INTO `free_email_provider` VALUES ('2636', 'mail2srilanka.com');
INSERT INTO `free_email_provider` VALUES ('2637', 'mail2stacy.com');
INSERT INTO `free_email_provider` VALUES ('2638', 'mail2stan.com');
INSERT INTO `free_email_provider` VALUES ('2639', 'mail2stanley.com');
INSERT INTO `free_email_provider` VALUES ('2640', 'mail2star.com');
INSERT INTO `free_email_provider` VALUES ('2641', 'mail2state.com');
INSERT INTO `free_email_provider` VALUES ('2642', 'mail2stephanie.com');
INSERT INTO `free_email_provider` VALUES ('2643', 'mail2steve.com');
INSERT INTO `free_email_provider` VALUES ('2644', 'mail2steven.com');
INSERT INTO `free_email_provider` VALUES ('2645', 'mail2stewart.com');
INSERT INTO `free_email_provider` VALUES ('2646', 'mail2stlouis.com');
INSERT INTO `free_email_provider` VALUES ('2647', 'mail2stock.com');
INSERT INTO `free_email_provider` VALUES ('2648', 'mail2stockholm.com');
INSERT INTO `free_email_provider` VALUES ('2649', 'mail2stockmarket.com');
INSERT INTO `free_email_provider` VALUES ('2650', 'mail2storage.com');
INSERT INTO `free_email_provider` VALUES ('2651', 'mail2store.com');
INSERT INTO `free_email_provider` VALUES ('2652', 'mail2strong.com');
INSERT INTO `free_email_provider` VALUES ('2653', 'mail2student.com');
INSERT INTO `free_email_provider` VALUES ('2654', 'mail2studio.com');
INSERT INTO `free_email_provider` VALUES ('2655', 'mail2studio54.com');
INSERT INTO `free_email_provider` VALUES ('2656', 'mail2stuntman.com');
INSERT INTO `free_email_provider` VALUES ('2657', 'mail2subscribe.com');
INSERT INTO `free_email_provider` VALUES ('2658', 'mail2sudan.com');
INSERT INTO `free_email_provider` VALUES ('2659', 'mail2superstar.com');
INSERT INTO `free_email_provider` VALUES ('2660', 'mail2surfer.com');
INSERT INTO `free_email_provider` VALUES ('2661', 'mail2suriname.com');
INSERT INTO `free_email_provider` VALUES ('2662', 'mail2susan.com');
INSERT INTO `free_email_provider` VALUES ('2663', 'mail2suzie.com');
INSERT INTO `free_email_provider` VALUES ('2664', 'mail2swaziland.com');
INSERT INTO `free_email_provider` VALUES ('2665', 'mail2sweden.com');
INSERT INTO `free_email_provider` VALUES ('2666', 'mail2sweetheart.com');
INSERT INTO `free_email_provider` VALUES ('2667', 'mail2swim.com');
INSERT INTO `free_email_provider` VALUES ('2668', 'mail2swimmer.com');
INSERT INTO `free_email_provider` VALUES ('2669', 'mail2swiss.com');
INSERT INTO `free_email_provider` VALUES ('2670', 'mail2switzerland.com');
INSERT INTO `free_email_provider` VALUES ('2671', 'mail2sydney.com');
INSERT INTO `free_email_provider` VALUES ('2672', 'mail2sylvia.com');
INSERT INTO `free_email_provider` VALUES ('2673', 'mail2syria.com');
INSERT INTO `free_email_provider` VALUES ('2674', 'mail2taboo.com');
INSERT INTO `free_email_provider` VALUES ('2675', 'mail2taiwan.com');
INSERT INTO `free_email_provider` VALUES ('2676', 'mail2tajikistan.com');
INSERT INTO `free_email_provider` VALUES ('2677', 'mail2tammy.com');
INSERT INTO `free_email_provider` VALUES ('2678', 'mail2tango.com');
INSERT INTO `free_email_provider` VALUES ('2679', 'mail2tanya.com');
INSERT INTO `free_email_provider` VALUES ('2680', 'mail2tanzania.com');
INSERT INTO `free_email_provider` VALUES ('2681', 'mail2tara.com');
INSERT INTO `free_email_provider` VALUES ('2682', 'mail2taurus.com');
INSERT INTO `free_email_provider` VALUES ('2683', 'mail2taxi.com');
INSERT INTO `free_email_provider` VALUES ('2684', 'mail2taxidermist.com');
INSERT INTO `free_email_provider` VALUES ('2685', 'mail2taylor.com');
INSERT INTO `free_email_provider` VALUES ('2686', 'mail2taz.com');
INSERT INTO `free_email_provider` VALUES ('2687', 'mail2teacher.com');
INSERT INTO `free_email_provider` VALUES ('2688', 'mail2technician.com');
INSERT INTO `free_email_provider` VALUES ('2689', 'mail2ted.com');
INSERT INTO `free_email_provider` VALUES ('2690', 'mail2telephone.com');
INSERT INTO `free_email_provider` VALUES ('2691', 'mail2teletubbie.com');
INSERT INTO `free_email_provider` VALUES ('2692', 'mail2tenderness.com');
INSERT INTO `free_email_provider` VALUES ('2693', 'mail2tennessee.com');
INSERT INTO `free_email_provider` VALUES ('2694', 'mail2tennis.com');
INSERT INTO `free_email_provider` VALUES ('2695', 'mail2tennisfan.com');
INSERT INTO `free_email_provider` VALUES ('2696', 'mail2terri.com');
INSERT INTO `free_email_provider` VALUES ('2697', 'mail2terry.com');
INSERT INTO `free_email_provider` VALUES ('2698', 'mail2test.com');
INSERT INTO `free_email_provider` VALUES ('2699', 'mail2texas.com');
INSERT INTO `free_email_provider` VALUES ('2700', 'mail2thailand.com');
INSERT INTO `free_email_provider` VALUES ('2701', 'mail2therapy.com');
INSERT INTO `free_email_provider` VALUES ('2702', 'mail2think.com');
INSERT INTO `free_email_provider` VALUES ('2703', 'mail2tickets.com');
INSERT INTO `free_email_provider` VALUES ('2704', 'mail2tiffany.com');
INSERT INTO `free_email_provider` VALUES ('2705', 'mail2tim.com');
INSERT INTO `free_email_provider` VALUES ('2706', 'mail2time.com');
INSERT INTO `free_email_provider` VALUES ('2707', 'mail2timothy.com');
INSERT INTO `free_email_provider` VALUES ('2708', 'mail2tina.com');
INSERT INTO `free_email_provider` VALUES ('2709', 'mail2titanic.com');
INSERT INTO `free_email_provider` VALUES ('2710', 'mail2toby.com');
INSERT INTO `free_email_provider` VALUES ('2711', 'mail2todd.com');
INSERT INTO `free_email_provider` VALUES ('2712', 'mail2togo.com');
INSERT INTO `free_email_provider` VALUES ('2713', 'mail2tom.com');
INSERT INTO `free_email_provider` VALUES ('2714', 'mail2tommy.com');
INSERT INTO `free_email_provider` VALUES ('2715', 'mail2tonga.com');
INSERT INTO `free_email_provider` VALUES ('2716', 'mail2tony.com');
INSERT INTO `free_email_provider` VALUES ('2717', 'mail2touch.com');
INSERT INTO `free_email_provider` VALUES ('2718', 'mail2tourist.com');
INSERT INTO `free_email_provider` VALUES ('2719', 'mail2tracey.com');
INSERT INTO `free_email_provider` VALUES ('2720', 'mail2tracy.com');
INSERT INTO `free_email_provider` VALUES ('2721', 'mail2tramp.com');
INSERT INTO `free_email_provider` VALUES ('2722', 'mail2travel.com');
INSERT INTO `free_email_provider` VALUES ('2723', 'mail2traveler.com');
INSERT INTO `free_email_provider` VALUES ('2724', 'mail2travis.com');
INSERT INTO `free_email_provider` VALUES ('2725', 'mail2trekkie.com');
INSERT INTO `free_email_provider` VALUES ('2726', 'mail2trex.com');
INSERT INTO `free_email_provider` VALUES ('2727', 'mail2triallawyer.com');
INSERT INTO `free_email_provider` VALUES ('2728', 'mail2trick.com');
INSERT INTO `free_email_provider` VALUES ('2729', 'mail2trillionaire.com');
INSERT INTO `free_email_provider` VALUES ('2730', 'mail2troy.com');
INSERT INTO `free_email_provider` VALUES ('2731', 'mail2truck.com');
INSERT INTO `free_email_provider` VALUES ('2732', 'mail2trump.com');
INSERT INTO `free_email_provider` VALUES ('2733', 'mail2try.com');
INSERT INTO `free_email_provider` VALUES ('2734', 'mail2tunisia.com');
INSERT INTO `free_email_provider` VALUES ('2735', 'mail2turbo.com');
INSERT INTO `free_email_provider` VALUES ('2736', 'mail2turkey.com');
INSERT INTO `free_email_provider` VALUES ('2737', 'mail2turkmenistan.com');
INSERT INTO `free_email_provider` VALUES ('2738', 'mail2tv.com');
INSERT INTO `free_email_provider` VALUES ('2739', 'mail2tycoon.com');
INSERT INTO `free_email_provider` VALUES ('2740', 'mail2tyler.com');
INSERT INTO `free_email_provider` VALUES ('2741', 'mail2u4me.com');
INSERT INTO `free_email_provider` VALUES ('2742', 'mail2uae.com');
INSERT INTO `free_email_provider` VALUES ('2743', 'mail2uganda.com');
INSERT INTO `free_email_provider` VALUES ('2744', 'mail2uk.com');
INSERT INTO `free_email_provider` VALUES ('2745', 'mail2ukraine.com');
INSERT INTO `free_email_provider` VALUES ('2746', 'mail2uncle.com');
INSERT INTO `free_email_provider` VALUES ('2747', 'mail2unsubscribe.com');
INSERT INTO `free_email_provider` VALUES ('2748', 'mail2uptown.com');
INSERT INTO `free_email_provider` VALUES ('2749', 'mail2uruguay.com');
INSERT INTO `free_email_provider` VALUES ('2750', 'mail2usa.com');
INSERT INTO `free_email_provider` VALUES ('2751', 'mail2utah.com');
INSERT INTO `free_email_provider` VALUES ('2752', 'mail2uzbekistan.com');
INSERT INTO `free_email_provider` VALUES ('2753', 'mail2v.com');
INSERT INTO `free_email_provider` VALUES ('2754', 'mail2vacation.com');
INSERT INTO `free_email_provider` VALUES ('2755', 'mail2valentines.com');
INSERT INTO `free_email_provider` VALUES ('2756', 'mail2valerie.com');
INSERT INTO `free_email_provider` VALUES ('2757', 'mail2valley.com');
INSERT INTO `free_email_provider` VALUES ('2758', 'mail2vamoose.com');
INSERT INTO `free_email_provider` VALUES ('2759', 'mail2vanessa.com');
INSERT INTO `free_email_provider` VALUES ('2760', 'mail2vanuatu.com');
INSERT INTO `free_email_provider` VALUES ('2761', 'mail2venezuela.com');
INSERT INTO `free_email_provider` VALUES ('2762', 'mail2venous.com');
INSERT INTO `free_email_provider` VALUES ('2763', 'mail2venus.com');
INSERT INTO `free_email_provider` VALUES ('2764', 'mail2vermont.com');
INSERT INTO `free_email_provider` VALUES ('2765', 'mail2vickie.com');
INSERT INTO `free_email_provider` VALUES ('2766', 'mail2victor.com');
INSERT INTO `free_email_provider` VALUES ('2767', 'mail2victoria.com');
INSERT INTO `free_email_provider` VALUES ('2768', 'mail2vienna.com');
INSERT INTO `free_email_provider` VALUES ('2769', 'mail2vietnam.com');
INSERT INTO `free_email_provider` VALUES ('2770', 'mail2vince.com');
INSERT INTO `free_email_provider` VALUES ('2771', 'mail2virginia.com');
INSERT INTO `free_email_provider` VALUES ('2772', 'mail2virgo.com');
INSERT INTO `free_email_provider` VALUES ('2773', 'mail2visionary.com');
INSERT INTO `free_email_provider` VALUES ('2774', 'mail2vodka.com');
INSERT INTO `free_email_provider` VALUES ('2775', 'mail2volleyball.com');
INSERT INTO `free_email_provider` VALUES ('2776', 'mail2waiter.com');
INSERT INTO `free_email_provider` VALUES ('2777', 'mail2wallstreet.com');
INSERT INTO `free_email_provider` VALUES ('2778', 'mail2wally.com');
INSERT INTO `free_email_provider` VALUES ('2779', 'mail2walter.com');
INSERT INTO `free_email_provider` VALUES ('2780', 'mail2warren.com');
INSERT INTO `free_email_provider` VALUES ('2781', 'mail2washington.com');
INSERT INTO `free_email_provider` VALUES ('2782', 'mail2wave.com');
INSERT INTO `free_email_provider` VALUES ('2783', 'mail2way.com');
INSERT INTO `free_email_provider` VALUES ('2784', 'mail2waycool.com');
INSERT INTO `free_email_provider` VALUES ('2785', 'mail2wayne.com');
INSERT INTO `free_email_provider` VALUES ('2786', 'mail2webmaster.com');
INSERT INTO `free_email_provider` VALUES ('2787', 'mail2webtop.com');
INSERT INTO `free_email_provider` VALUES ('2788', 'mail2webtv.com');
INSERT INTO `free_email_provider` VALUES ('2789', 'mail2weird.com');
INSERT INTO `free_email_provider` VALUES ('2790', 'mail2wendell.com');
INSERT INTO `free_email_provider` VALUES ('2791', 'mail2wendy.com');
INSERT INTO `free_email_provider` VALUES ('2792', 'mail2westend.com');
INSERT INTO `free_email_provider` VALUES ('2793', 'mail2westvirginia.com');
INSERT INTO `free_email_provider` VALUES ('2794', 'mail2whether.com');
INSERT INTO `free_email_provider` VALUES ('2795', 'mail2whip.com');
INSERT INTO `free_email_provider` VALUES ('2796', 'mail2white.com');
INSERT INTO `free_email_provider` VALUES ('2797', 'mail2whitehouse.com');
INSERT INTO `free_email_provider` VALUES ('2798', 'mail2whitney.com');
INSERT INTO `free_email_provider` VALUES ('2799', 'mail2why.com');
INSERT INTO `free_email_provider` VALUES ('2800', 'mail2wilbur.com');
INSERT INTO `free_email_provider` VALUES ('2801', 'mail2wild.com');
INSERT INTO `free_email_provider` VALUES ('2802', 'mail2willard.com');
INSERT INTO `free_email_provider` VALUES ('2803', 'mail2willie.com');
INSERT INTO `free_email_provider` VALUES ('2804', 'mail2wine.com');
INSERT INTO `free_email_provider` VALUES ('2805', 'mail2winner.com');
INSERT INTO `free_email_provider` VALUES ('2806', 'mail2wired.com');
INSERT INTO `free_email_provider` VALUES ('2807', 'mail2wisconsin.com');
INSERT INTO `free_email_provider` VALUES ('2808', 'mail2woman.com');
INSERT INTO `free_email_provider` VALUES ('2809', 'mail2wonder.com');
INSERT INTO `free_email_provider` VALUES ('2810', 'mail2world.com');
INSERT INTO `free_email_provider` VALUES ('2811', 'mail2worship.com');
INSERT INTO `free_email_provider` VALUES ('2812', 'mail2wow.com');
INSERT INTO `free_email_provider` VALUES ('2813', 'mail2www.com');
INSERT INTO `free_email_provider` VALUES ('2814', 'mail2wyoming.com');
INSERT INTO `free_email_provider` VALUES ('2815', 'mail2xfiles.com');
INSERT INTO `free_email_provider` VALUES ('2816', 'mail2xox.com');
INSERT INTO `free_email_provider` VALUES ('2817', 'mail2yachtclub.com');
INSERT INTO `free_email_provider` VALUES ('2818', 'mail2yahalla.com');
INSERT INTO `free_email_provider` VALUES ('2819', 'mail2yemen.com');
INSERT INTO `free_email_provider` VALUES ('2820', 'mail2yes.com');
INSERT INTO `free_email_provider` VALUES ('2821', 'mail2yugoslavia.com');
INSERT INTO `free_email_provider` VALUES ('2822', 'mail2zack.com');
INSERT INTO `free_email_provider` VALUES ('2823', 'mail2zambia.com');
INSERT INTO `free_email_provider` VALUES ('2824', 'mail2zenith.com');
INSERT INTO `free_email_provider` VALUES ('2825', 'mail2zephir.com');
INSERT INTO `free_email_provider` VALUES ('2826', 'mail2zeus.com');
INSERT INTO `free_email_provider` VALUES ('2827', 'mail2zipper.com');
INSERT INTO `free_email_provider` VALUES ('2828', 'mail2zoo.com');
INSERT INTO `free_email_provider` VALUES ('2829', 'mail2zoologist.com');
INSERT INTO `free_email_provider` VALUES ('2830', 'mail2zurich.com');
INSERT INTO `free_email_provider` VALUES ('2831', 'mail3000.com');
INSERT INTO `free_email_provider` VALUES ('2832', 'mail333.com');
INSERT INTO `free_email_provider` VALUES ('2833', 'mailandftp.com');
INSERT INTO `free_email_provider` VALUES ('2834', 'mailandnews.com');
INSERT INTO `free_email_provider` VALUES ('2835', 'mailas.com');
INSERT INTO `free_email_provider` VALUES ('2836', 'mailasia.com');
INSERT INTO `free_email_provider` VALUES ('2837', 'mailbolt.com');
INSERT INTO `free_email_provider` VALUES ('2838', 'mailbomb.net');
INSERT INTO `free_email_provider` VALUES ('2839', 'mailbox.as');
INSERT INTO `free_email_provider` VALUES ('2840', 'mailbox.co.za');
INSERT INTO `free_email_provider` VALUES ('2841', 'mailbox.gr');
INSERT INTO `free_email_provider` VALUES ('2842', 'mailbox.hu');
INSERT INTO `free_email_provider` VALUES ('2843', 'mailbr.com.br');
INSERT INTO `free_email_provider` VALUES ('2844', 'mailc.net');
INSERT INTO `free_email_provider` VALUES ('2845', 'mailcan.com');
INSERT INTO `free_email_provider` VALUES ('2846', 'mailchoose.co');
INSERT INTO `free_email_provider` VALUES ('2847', 'mailclub.fr');
INSERT INTO `free_email_provider` VALUES ('2848', 'mailclub.net');
INSERT INTO `free_email_provider` VALUES ('2849', 'mailexcite.com');
INSERT INTO `free_email_provider` VALUES ('2850', 'mailforce.net');
INSERT INTO `free_email_provider` VALUES ('2851', 'mailftp.com');
INSERT INTO `free_email_provider` VALUES ('2852', 'mailgenie.net');
INSERT INTO `free_email_provider` VALUES ('2853', 'mailhaven.com');
INSERT INTO `free_email_provider` VALUES ('2854', 'mailhood.com');
INSERT INTO `free_email_provider` VALUES ('2855', 'mailingweb.com');
INSERT INTO `free_email_provider` VALUES ('2856', 'mailisent.com');
INSERT INTO `free_email_provider` VALUES ('2857', 'mailite.com');
INSERT INTO `free_email_provider` VALUES ('2858', 'mailme.dk');
INSERT INTO `free_email_provider` VALUES ('2859', 'mailmight.com');
INSERT INTO `free_email_provider` VALUES ('2860', 'mailmij.nl');
INSERT INTO `free_email_provider` VALUES ('2861', 'mailnew.com');
INSERT INTO `free_email_provider` VALUES ('2862', 'mailops.com');
INSERT INTO `free_email_provider` VALUES ('2863', 'mailoye.com');
INSERT INTO `free_email_provider` VALUES ('2864', 'mailpanda.com');
INSERT INTO `free_email_provider` VALUES ('2865', 'mailpride.com');
INSERT INTO `free_email_provider` VALUES ('2866', 'mailpuppy.com');
INSERT INTO `free_email_provider` VALUES ('2867', 'mailroom.com');
INSERT INTO `free_email_provider` VALUES ('2868', 'mailru.com');
INSERT INTO `free_email_provider` VALUES ('2869', 'mailsent.net');
INSERT INTO `free_email_provider` VALUES ('2870', 'mailsurf.com');
INSERT INTO `free_email_provider` VALUES ('2871', 'mailup.net');
INSERT INTO `free_email_provider` VALUES ('2872', 'malayalamtelevision.net');
INSERT INTO `free_email_provider` VALUES ('2873', 'manager.de');
INSERT INTO `free_email_provider` VALUES ('2874', 'mantrafreenet.com');
INSERT INTO `free_email_provider` VALUES ('2875', 'mantramail.com');
INSERT INTO `free_email_provider` VALUES ('2876', 'mantraonline.com');
INSERT INTO `free_email_provider` VALUES ('2877', 'marchmail.com');
INSERT INTO `free_email_provider` VALUES ('2878', 'marijuana.nl');
INSERT INTO `free_email_provider` VALUES ('2879', 'married-not.com');
INSERT INTO `free_email_provider` VALUES ('2880', 'marsattack.com');
INSERT INTO `free_email_provider` VALUES ('2881', 'masrawy.com');
INSERT INTO `free_email_provider` VALUES ('2882', 'maxleft.com');
INSERT INTO `free_email_provider` VALUES ('2883', 'mbox.com.au');
INSERT INTO `free_email_provider` VALUES ('2884', 'me-mail.hu');
INSERT INTO `free_email_provider` VALUES ('2885', 'meetingmall.com');
INSERT INTO `free_email_provider` VALUES ('2886', 'megago.com');
INSERT INTO `free_email_provider` VALUES ('2887', 'megamail.pt');
INSERT INTO `free_email_provider` VALUES ('2888', 'mehrani.com');
INSERT INTO `free_email_provider` VALUES ('2889', 'mehtaweb.com');
INSERT INTO `free_email_provider` VALUES ('2890', 'melodymail.com');
INSERT INTO `free_email_provider` VALUES ('2891', 'meloo.com');
INSERT INTO `free_email_provider` VALUES ('2892', 'message.hu');
INSERT INTO `free_email_provider` VALUES ('2893', 'metacrawler.com');
INSERT INTO `free_email_provider` VALUES ('2894', 'metta.lk');
INSERT INTO `free_email_provider` VALUES ('2895', 'miesto.sk');
INSERT INTO `free_email_provider` VALUES ('2896', 'mighty.co.za');
INSERT INTO `free_email_provider` VALUES ('2897', 'miho-nakayama.com');
INSERT INTO `free_email_provider` VALUES ('2898', 'millionaireintraining.com');
INSERT INTO `free_email_provider` VALUES ('2899', 'misery.net');
INSERT INTO `free_email_provider` VALUES ('2900', 'mittalweb.com');
INSERT INTO `free_email_provider` VALUES ('2901', 'mixmail.com');
INSERT INTO `free_email_provider` VALUES ('2902', 'ml1.net');
INSERT INTO `free_email_provider` VALUES ('2903', 'mobilbatam.com');
INSERT INTO `free_email_provider` VALUES ('2904', 'mohammed.com');
INSERT INTO `free_email_provider` VALUES ('2905', 'moldova.cc');
INSERT INTO `free_email_provider` VALUES ('2906', 'moldova.com');
INSERT INTO `free_email_provider` VALUES ('2907', 'moldovacc.com');
INSERT INTO `free_email_provider` VALUES ('2908', 'montevideo.com.uy');
INSERT INTO `free_email_provider` VALUES ('2909', 'moonman.com');
INSERT INTO `free_email_provider` VALUES ('2910', 'mortaza.com');
INSERT INTO `free_email_provider` VALUES ('2911', 'mosaicfx.com');
INSERT INTO `free_email_provider` VALUES ('2912', 'most-wanted.com');
INSERT INTO `free_email_provider` VALUES ('2913', 'mostlysunny.com');
INSERT INTO `free_email_provider` VALUES ('2914', 'motormania.com');
INSERT INTO `free_email_provider` VALUES ('2915', 'movemail.com');
INSERT INTO `free_email_provider` VALUES ('2916', 'mp4.it');
INSERT INTO `free_email_provider` VALUES ('2917', 'mr-potatohead.com');
INSERT INTO `free_email_provider` VALUES ('2918', 'mscold.com');
INSERT INTO `free_email_provider` VALUES ('2919', 'mundomail.net');
INSERT INTO `free_email_provider` VALUES ('2920', 'munich.com');
INSERT INTO `free_email_provider` VALUES ('2921', 'musician.org');
INSERT INTO `free_email_provider` VALUES ('2922', 'musicscene.org');
INSERT INTO `free_email_provider` VALUES ('2923', 'mybox.it');
INSERT INTO `free_email_provider` VALUES ('2924', 'mycabin.com');
INSERT INTO `free_email_provider` VALUES ('2925', 'mycity.com');
INSERT INTO `free_email_provider` VALUES ('2926', 'mydomain.com');
INSERT INTO `free_email_provider` VALUES ('2927', 'mydotcomaddress.com');
INSERT INTO `free_email_provider` VALUES ('2928', 'myfamily.com');
INSERT INTO `free_email_provider` VALUES ('2929', 'myiris.com');
INSERT INTO `free_email_provider` VALUES ('2930', 'mynamedot.com');
INSERT INTO `free_email_provider` VALUES ('2931', 'mynetaddress.com');
INSERT INTO `free_email_provider` VALUES ('2932', 'myownemail.com');
INSERT INTO `free_email_provider` VALUES ('2933', 'myownfriends.com');
INSERT INTO `free_email_provider` VALUES ('2934', 'mypersonalemail.com');
INSERT INTO `free_email_provider` VALUES ('2935', 'myplace.com');
INSERT INTO `free_email_provider` VALUES ('2936', 'myrealbox.com');
INSERT INTO `free_email_provider` VALUES ('2937', 'myself.com');
INSERT INTO `free_email_provider` VALUES ('2938', 'mystupidjob.com');
INSERT INTO `free_email_provider` VALUES ('2939', 'myway.com');
INSERT INTO `free_email_provider` VALUES ('2940', 'n2.com');
INSERT INTO `free_email_provider` VALUES ('2941', 'n2business.com');
INSERT INTO `free_email_provider` VALUES ('2942', 'n2mail.com');
INSERT INTO `free_email_provider` VALUES ('2943', 'n2software.com');
INSERT INTO `free_email_provider` VALUES ('2944', 'nabc.biz');
INSERT INTO `free_email_provider` VALUES ('2945', 'nagpal.net');
INSERT INTO `free_email_provider` VALUES ('2946', 'nakedgreens.com');
INSERT INTO `free_email_provider` VALUES ('2947', 'name.com');
INSERT INTO `free_email_provider` VALUES ('2948', 'nameplanet.com');
INSERT INTO `free_email_provider` VALUES ('2949', 'naseej.com');
INSERT INTO `free_email_provider` VALUES ('2950', 'nativestar.net');
INSERT INTO `free_email_provider` VALUES ('2951', 'nativeweb.net');
INSERT INTO `free_email_provider` VALUES ('2952', 'navigator.lv');
INSERT INTO `free_email_provider` VALUES ('2953', 'neeva.net');
INSERT INTO `free_email_provider` VALUES ('2954', 'nemra1.com');
INSERT INTO `free_email_provider` VALUES ('2955', 'nenter.com');
INSERT INTO `free_email_provider` VALUES ('2956', 'nervhq.org');
INSERT INTO `free_email_provider` VALUES ('2957', 'net4b.pt');
INSERT INTO `free_email_provider` VALUES ('2958', 'net4you.at');
INSERT INTO `free_email_provider` VALUES ('2959', 'netbounce.com');
INSERT INTO `free_email_provider` VALUES ('2960', 'netbroadcaster.com');
INSERT INTO `free_email_provider` VALUES ('2961', 'netcenter-vn.net');
INSERT INTO `free_email_provider` VALUES ('2962', 'netcourrier.com');
INSERT INTO `free_email_provider` VALUES ('2963', 'netexecutive.com');
INSERT INTO `free_email_provider` VALUES ('2964', 'netexpressway.com');
INSERT INTO `free_email_provider` VALUES ('2965', 'netian.com');
INSERT INTO `free_email_provider` VALUES ('2966', 'netizen.com.ar');
INSERT INTO `free_email_provider` VALUES ('2967', 'netlane.com');
INSERT INTO `free_email_provider` VALUES ('2968', 'netlimit.com');
INSERT INTO `free_email_provider` VALUES ('2969', 'netmongol.com');
INSERT INTO `free_email_provider` VALUES ('2970', 'netpiper.com');
INSERT INTO `free_email_provider` VALUES ('2971', 'netposta.net');
INSERT INTO `free_email_provider` VALUES ('2972', 'netralink.com');
INSERT INTO `free_email_provider` VALUES ('2973', 'netscapeonline.co.uk');
INSERT INTO `free_email_provider` VALUES ('2974', 'netspeedway.com');
INSERT INTO `free_email_provider` VALUES ('2975', 'netsquare.com');
INSERT INTO `free_email_provider` VALUES ('2976', 'netster.com');
INSERT INTO `free_email_provider` VALUES ('2977', 'netzero.com');
INSERT INTO `free_email_provider` VALUES ('2978', 'newmail.com');
INSERT INTO `free_email_provider` VALUES ('2979', 'newmail.ru');
INSERT INTO `free_email_provider` VALUES ('2980', 'newyork.com');
INSERT INTO `free_email_provider` VALUES ('2981', 'nicegal.com');
INSERT INTO `free_email_provider` VALUES ('2982', 'nicholastse.net');
INSERT INTO `free_email_provider` VALUES ('2983', 'nicolastse.com');
INSERT INTO `free_email_provider` VALUES ('2984', 'nightmail.com');
INSERT INTO `free_email_provider` VALUES ('2985', 'nikopage.com');
INSERT INTO `free_email_provider` VALUES ('2986', 'nirvanafan.com');
INSERT INTO `free_email_provider` VALUES ('2987', 'noavar.com');
INSERT INTO `free_email_provider` VALUES ('2988', 'norika-fujiwara.com');
INSERT INTO `free_email_provider` VALUES ('2989', 'northgates.net');
INSERT INTO `free_email_provider` VALUES ('2990', 'nospammail.net');
INSERT INTO `free_email_provider` VALUES ('2991', 'ny.com');
INSERT INTO `free_email_provider` VALUES ('2992', 'nyc.com');
INSERT INTO `free_email_provider` VALUES ('2993', 'nycmail.com');
INSERT INTO `free_email_provider` VALUES ('2994', 'nzoomail.com');
INSERT INTO `free_email_provider` VALUES ('2995', 'o-tay.com');
INSERT INTO `free_email_provider` VALUES ('2996', 'o2.co.uk');
INSERT INTO `free_email_provider` VALUES ('2997', 'oceanfree.net');
INSERT INTO `free_email_provider` VALUES ('2998', 'oddpost.com');
INSERT INTO `free_email_provider` VALUES ('2999', 'odmail.com');
INSERT INTO `free_email_provider` VALUES ('3000', 'oicexchange.com');
INSERT INTO `free_email_provider` VALUES ('3001', 'okbank.com');
INSERT INTO `free_email_provider` VALUES ('3002', 'okhuman.com');
INSERT INTO `free_email_provider` VALUES ('3003', 'okmad.com');
INSERT INTO `free_email_provider` VALUES ('3004', 'okmagic.com');
INSERT INTO `free_email_provider` VALUES ('3005', 'okname.net');
INSERT INTO `free_email_provider` VALUES ('3006', 'okuk.com');
INSERT INTO `free_email_provider` VALUES ('3007', 'ole.com');
INSERT INTO `free_email_provider` VALUES ('3008', 'olemail.com');
INSERT INTO `free_email_provider` VALUES ('3009', 'olympist.net');
INSERT INTO `free_email_provider` VALUES ('3010', 'omaninfo.com');
INSERT INTO `free_email_provider` VALUES ('3011', 'onebox.com');
INSERT INTO `free_email_provider` VALUES ('3012', 'onenet.com.ar');
INSERT INTO `free_email_provider` VALUES ('3013', 'onet.pl');
INSERT INTO `free_email_provider` VALUES ('3014', 'oninet.pt');
INSERT INTO `free_email_provider` VALUES ('3015', 'online.ie');
INSERT INTO `free_email_provider` VALUES ('3016', 'onlinewiz.com');
INSERT INTO `free_email_provider` VALUES ('3017', 'onmilwaukee.com');
INSERT INTO `free_email_provider` VALUES ('3018', 'onobox.com');
INSERT INTO `free_email_provider` VALUES ('3019', 'optician.com');
INSERT INTO `free_email_provider` VALUES ('3020', 'orbitel.bg');
INSERT INTO `free_email_provider` VALUES ('3021', 'orgmail.net');
INSERT INTO `free_email_provider` VALUES ('3022', 'osite.com.br');
INSERT INTO `free_email_provider` VALUES ('3023', 'oso.com');
INSERT INTO `free_email_provider` VALUES ('3024', 'otakumail.com');
INSERT INTO `free_email_provider` VALUES ('3025', 'our-computer.com');
INSERT INTO `free_email_provider` VALUES ('3026', 'our-office.com');
INSERT INTO `free_email_provider` VALUES ('3027', 'ourbrisbane.com');
INSERT INTO `free_email_provider` VALUES ('3028', 'ournet.md');
INSERT INTO `free_email_provider` VALUES ('3029', 'outgun.com');
INSERT INTO `free_email_provider` VALUES ('3030', 'over-the-rainbow.com');
INSERT INTO `free_email_provider` VALUES ('3031', 'ownmail.net');
INSERT INTO `free_email_provider` VALUES ('3032', 'packersfan.com');
INSERT INTO `free_email_provider` VALUES ('3033', 'pakistanoye.com');
INSERT INTO `free_email_provider` VALUES ('3034', 'palestinemail.com');
INSERT INTO `free_email_provider` VALUES ('3035', 'parkjiyoon.com');
INSERT INTO `free_email_provider` VALUES ('3036', 'parrot.com');
INSERT INTO `free_email_provider` VALUES ('3037', 'partlycloudy.com');
INSERT INTO `free_email_provider` VALUES ('3038', 'partynight.at');
INSERT INTO `free_email_provider` VALUES ('3039', 'parvazi.com');
INSERT INTO `free_email_provider` VALUES ('3040', 'pcpostal.com');
INSERT INTO `free_email_provider` VALUES ('3041', 'pediatrician.com');
INSERT INTO `free_email_provider` VALUES ('3042', 'penpen.com');
INSERT INTO `free_email_provider` VALUES ('3043', 'perfectmail.com');
INSERT INTO `free_email_provider` VALUES ('3044', 'personal.ro');
INSERT INTO `free_email_provider` VALUES ('3045', 'personales.com');
INSERT INTO `free_email_provider` VALUES ('3046', 'petml.com');
INSERT INTO `free_email_provider` VALUES ('3047', 'pettypool.com');
INSERT INTO `free_email_provider` VALUES ('3048', 'pezeshkpour.com');
INSERT INTO `free_email_provider` VALUES ('3049', 'phayze.com');
INSERT INTO `free_email_provider` VALUES ('3050', 'picusnet.com');
INSERT INTO `free_email_provider` VALUES ('3051', 'pigpig.net');
INSERT INTO `free_email_provider` VALUES ('3052', 'piracha.net');
INSERT INTO `free_email_provider` VALUES ('3053', 'pisem.net');
INSERT INTO `free_email_provider` VALUES ('3054', 'planetout.com');
INSERT INTO `free_email_provider` VALUES ('3055', 'plasa.com');
INSERT INTO `free_email_provider` VALUES ('3056', 'playersodds.com');
INSERT INTO `free_email_provider` VALUES ('3057', 'playful.com');
INSERT INTO `free_email_provider` VALUES ('3058', 'plusmail.com.br');
INSERT INTO `free_email_provider` VALUES ('3059', 'pmail.net');
INSERT INTO `free_email_provider` VALUES ('3060', 'pobox.hu');
INSERT INTO `free_email_provider` VALUES ('3061', 'pobox.sk');
INSERT INTO `free_email_provider` VALUES ('3062', 'poczta.fm');
INSERT INTO `free_email_provider` VALUES ('3063', 'poetic.com');
INSERT INTO `free_email_provider` VALUES ('3064', 'policeoffice.com');
INSERT INTO `free_email_provider` VALUES ('3065', 'pool-sharks.com');
INSERT INTO `free_email_provider` VALUES ('3066', 'poond.com');
INSERT INTO `free_email_provider` VALUES ('3067', 'popsmail.com');
INSERT INTO `free_email_provider` VALUES ('3068', 'popstar.com');
INSERT INTO `free_email_provider` VALUES ('3069', 'portugalmail.com');
INSERT INTO `free_email_provider` VALUES ('3070', 'portugalmail.pt');
INSERT INTO `free_email_provider` VALUES ('3071', 'portugalnet.com');
INSERT INTO `free_email_provider` VALUES ('3072', 'positive-thinking.com');
INSERT INTO `free_email_provider` VALUES ('3073', 'post.com');
INSERT INTO `free_email_provider` VALUES ('3074', 'post.sk');
INSERT INTO `free_email_provider` VALUES ('3075', 'postaccesslite.com');
INSERT INTO `free_email_provider` VALUES ('3076', 'postafree.com');
INSERT INTO `free_email_provider` VALUES ('3077', 'postaweb.com');
INSERT INTO `free_email_provider` VALUES ('3078', 'postinbox.com');
INSERT INTO `free_email_provider` VALUES ('3079', 'postino.ch');
INSERT INTO `free_email_provider` VALUES ('3080', 'postpro.net');
INSERT INTO `free_email_provider` VALUES ('3081', 'powerfan.com');
INSERT INTO `free_email_provider` VALUES ('3082', 'praize.com');
INSERT INTO `free_email_provider` VALUES ('3083', 'premiumservice.com');
INSERT INTO `free_email_provider` VALUES ('3084', 'presidency.com');
INSERT INTO `free_email_provider` VALUES ('3085', 'press.co.jp');
INSERT INTO `free_email_provider` VALUES ('3086', 'priest.com');
INSERT INTO `free_email_provider` VALUES ('3087', 'primposta.com');
INSERT INTO `free_email_provider` VALUES ('3088', 'primposta.hu');
INSERT INTO `free_email_provider` VALUES ('3089', 'pro.hu');
INSERT INTO `free_email_provider` VALUES ('3090', 'progetplus.it');
INSERT INTO `free_email_provider` VALUES ('3091', 'programmer.net');
INSERT INTO `free_email_provider` VALUES ('3092', 'programozo.hu');
INSERT INTO `free_email_provider` VALUES ('3093', 'proinbox.com');
INSERT INTO `free_email_provider` VALUES ('3094', 'project2k.com');
INSERT INTO `free_email_provider` VALUES ('3095', 'promessage.com');
INSERT INTO `free_email_provider` VALUES ('3096', 'psv-supporter.com');
INSERT INTO `free_email_provider` VALUES ('3097', 'publicist.com');
INSERT INTO `free_email_provider` VALUES ('3098', 'pulp-fiction.com');
INSERT INTO `free_email_provider` VALUES ('3099', 'qatarmail.com');
INSERT INTO `free_email_provider` VALUES ('3100', 'qprfans.com');
INSERT INTO `free_email_provider` VALUES ('3101', 'qrio.com');
INSERT INTO `free_email_provider` VALUES ('3102', 'quackquack.com');
INSERT INTO `free_email_provider` VALUES ('3103', 'qudsmail.com');
INSERT INTO `free_email_provider` VALUES ('3104', 'quepasa.com');
INSERT INTO `free_email_provider` VALUES ('3105', 'quickwebmail.com');
INSERT INTO `free_email_provider` VALUES ('3106', 'r-o-o-t.com');
INSERT INTO `free_email_provider` VALUES ('3107', 'raakim.com');
INSERT INTO `free_email_provider` VALUES ('3108', 'racingfan.com.au');
INSERT INTO `free_email_provider` VALUES ('3109', 'radicalz.com');
INSERT INTO `free_email_provider` VALUES ('3110', 'ranmamail.com');
INSERT INTO `free_email_provider` VALUES ('3111', 'rastogi.net');
INSERT INTO `free_email_provider` VALUES ('3112', 'rattle-snake.com');
INSERT INTO `free_email_provider` VALUES ('3113', 'ravearena.com');
INSERT INTO `free_email_provider` VALUES ('3114', 'razormail.com');
INSERT INTO `free_email_provider` VALUES ('3115', 'rccgmail.org');
INSERT INTO `free_email_provider` VALUES ('3116', 'realemail.net');
INSERT INTO `free_email_provider` VALUES ('3117', 'reallyfast.biz');
INSERT INTO `free_email_provider` VALUES ('3118', 'rediffmailpro.com');
INSERT INTO `free_email_provider` VALUES ('3119', 'rednecks.com');
INSERT INTO `free_email_provider` VALUES ('3120', 'redseven.de');
INSERT INTO `free_email_provider` VALUES ('3121', 'redsfans.com');
INSERT INTO `free_email_provider` VALUES ('3122', 'registerednurses.com');
INSERT INTO `free_email_provider` VALUES ('3123', 'repairman.com');
INSERT INTO `free_email_provider` VALUES ('3124', 'reply.hu');
INSERT INTO `free_email_provider` VALUES ('3125', 'representative.com');
INSERT INTO `free_email_provider` VALUES ('3126', 'rescueteam.com');
INSERT INTO `free_email_provider` VALUES ('3127', 'rezai.com');
INSERT INTO `free_email_provider` VALUES ('3128', 'rickymail.com');
INSERT INTO `free_email_provider` VALUES ('3129', 'rin.ru');
INSERT INTO `free_email_provider` VALUES ('3130', 'rn.com');
INSERT INTO `free_email_provider` VALUES ('3131', 'rock.com');
INSERT INTO `free_email_provider` VALUES ('3132', 'rodrun.com');
INSERT INTO `free_email_provider` VALUES ('3133', 'rome.com');
INSERT INTO `free_email_provider` VALUES ('3134', 'roughnet.com');
INSERT INTO `free_email_provider` VALUES ('3135', 'rubyridge.com');
INSERT INTO `free_email_provider` VALUES ('3136', 'runbox.com');
INSERT INTO `free_email_provider` VALUES ('3137', 'rushpost.com');
INSERT INTO `free_email_provider` VALUES ('3138', 'ruttolibero.com');
INSERT INTO `free_email_provider` VALUES ('3139', 's-mail.com');
INSERT INTO `free_email_provider` VALUES ('3140', 'safe-mail.net');
INSERT INTO `free_email_provider` VALUES ('3141', 'sailormoon.com');
INSERT INTO `free_email_provider` VALUES ('3142', 'saintly.com');
INSERT INTO `free_email_provider` VALUES ('3143', 'sale-sale-sale.com');
INSERT INTO `free_email_provider` VALUES ('3144', 'salehi.net');
INSERT INTO `free_email_provider` VALUES ('3145', 'samerica.com');
INSERT INTO `free_email_provider` VALUES ('3146', 'sammimail.com');
INSERT INTO `free_email_provider` VALUES ('3147', 'sanfranmail.com');
INSERT INTO `free_email_provider` VALUES ('3148', 'sanook.com');
INSERT INTO `free_email_provider` VALUES ('3149', 'sapo.pt');
INSERT INTO `free_email_provider` VALUES ('3150', 'saudia.com');
INSERT INTO `free_email_provider` VALUES ('3151', 'sayhi.net');
INSERT INTO `free_email_provider` VALUES ('3152', 'scandalmail.com');
INSERT INTO `free_email_provider` VALUES ('3153', 'schweiz.org');
INSERT INTO `free_email_provider` VALUES ('3154', 'sci.fi');
INSERT INTO `free_email_provider` VALUES ('3155', 'scientist.com');
INSERT INTO `free_email_provider` VALUES ('3156', 'scifianime.com');
INSERT INTO `free_email_provider` VALUES ('3157', 'scottishmail.co.uk');
INSERT INTO `free_email_provider` VALUES ('3158', 'scubadiving.com');
INSERT INTO `free_email_provider` VALUES ('3159', 'searchwales.com');
INSERT INTO `free_email_provider` VALUES ('3160', 'sebil.com');
INSERT INTO `free_email_provider` VALUES ('3161', 'secret-police.com');
INSERT INTO `free_email_provider` VALUES ('3162', 'secretservices.net');
INSERT INTO `free_email_provider` VALUES ('3163', 'seductive.com');
INSERT INTO `free_email_provider` VALUES ('3164', 'seekstoyboy.com');
INSERT INTO `free_email_provider` VALUES ('3165', 'send.hu');
INSERT INTO `free_email_provider` VALUES ('3166', 'sendme.cz');
INSERT INTO `free_email_provider` VALUES ('3167', 'sent.com');
INSERT INTO `free_email_provider` VALUES ('3168', 'serga.com.ar');
INSERT INTO `free_email_provider` VALUES ('3169', 'servemymail.com');
INSERT INTO `free_email_provider` VALUES ('3170', 'sesmail.com');
INSERT INTO `free_email_provider` VALUES ('3171', 'seznam.cz');
INSERT INTO `free_email_provider` VALUES ('3172', 'shahweb.net');
INSERT INTO `free_email_provider` VALUES ('3173', 'shaniastuff.com');
INSERT INTO `free_email_provider` VALUES ('3174', 'sharmaweb.com');
INSERT INTO `free_email_provider` VALUES ('3175', 'she.com');
INSERT INTO `free_email_provider` VALUES ('3176', 'shootmail.com');
INSERT INTO `free_email_provider` VALUES ('3177', 'shotgun.hu');
INSERT INTO `free_email_provider` VALUES ('3178', 'shuf.com');
INSERT INTO `free_email_provider` VALUES ('3179', 'sialkotcity.com');
INSERT INTO `free_email_provider` VALUES ('3180', 'sialkotian.com');
INSERT INTO `free_email_provider` VALUES ('3181', 'sialkotoye.com');
INSERT INTO `free_email_provider` VALUES ('3182', 'sify.com');
INSERT INTO `free_email_provider` VALUES ('3183', 'sinamail.com');
INSERT INTO `free_email_provider` VALUES ('3184', 'singapore.com');
INSERT INTO `free_email_provider` VALUES ('3185', 'singmail.com');
INSERT INTO `free_email_provider` VALUES ('3186', 'singnet.com.sg');
INSERT INTO `free_email_provider` VALUES ('3187', 'skim.com');
INSERT INTO `free_email_provider` VALUES ('3188', 'skizo.hu');
INSERT INTO `free_email_provider` VALUES ('3189', 'slamdunkfan.com');
INSERT INTO `free_email_provider` VALUES ('3190', 'slingshot.com');
INSERT INTO `free_email_provider` VALUES ('3191', 'slo.net');
INSERT INTO `free_email_provider` VALUES ('3192', 'slotter.com');
INSERT INTO `free_email_provider` VALUES ('3193', 'smapxsmap.net');
INSERT INTO `free_email_provider` VALUES ('3194', 'smileyface.comsmithemail.net');
INSERT INTO `free_email_provider` VALUES ('3195', 'smoothmail.com');
INSERT INTO `free_email_provider` VALUES ('3196', 'snail-mail.net');
INSERT INTO `free_email_provider` VALUES ('3197', 'snakemail.com');
INSERT INTO `free_email_provider` VALUES ('3198', 'sndt.net');
INSERT INTO `free_email_provider` VALUES ('3199', 'sneakemail.com');
INSERT INTO `free_email_provider` VALUES ('3200', 'sniper.hu');
INSERT INTO `free_email_provider` VALUES ('3201', 'snoopymail.com');
INSERT INTO `free_email_provider` VALUES ('3202', 'snowboarding.com');
INSERT INTO `free_email_provider` VALUES ('3203', 'snowdonia.net');
INSERT INTO `free_email_provider` VALUES ('3204', 'socamail.com');
INSERT INTO `free_email_provider` VALUES ('3205', 'sociologist.com');
INSERT INTO `free_email_provider` VALUES ('3206', 'sol.dk');
INSERT INTO `free_email_provider` VALUES ('3207', 'soldier.hu');
INSERT INTO `free_email_provider` VALUES ('3208', 'soon.com');
INSERT INTO `free_email_provider` VALUES ('3209', 'soulfoodcookbook.com');
INSERT INTO `free_email_provider` VALUES ('3210', 'sp.nl');
INSERT INTO `free_email_provider` VALUES ('3211', 'space.com');
INSERT INTO `free_email_provider` VALUES ('3212', 'spacetowns.com');
INSERT INTO `free_email_provider` VALUES ('3213', 'spamex.com');
INSERT INTO `free_email_provider` VALUES ('3214', 'spartapiet.com');
INSERT INTO `free_email_provider` VALUES ('3215', 'spazmail.com');
INSERT INTO `free_email_provider` VALUES ('3216', 'speedpost.net');
INSERT INTO `free_email_provider` VALUES ('3217', 'spils.com');
INSERT INTO `free_email_provider` VALUES ('3218', 'spinfinder.com');
INSERT INTO `free_email_provider` VALUES ('3219', 'sportemail.com');
INSERT INTO `free_email_provider` VALUES ('3220', 'spray.no');
INSERT INTO `free_email_provider` VALUES ('3221', 'spray.se');
INSERT INTO `free_email_provider` VALUES ('3222', 'spymac.com');
INSERT INTO `free_email_provider` VALUES ('3223', 'srilankan.net');
INSERT INTO `free_email_provider` VALUES ('3224', 'st-davids.net');
INSERT INTO `free_email_provider` VALUES ('3225', 'stade.fr');
INSERT INTO `free_email_provider` VALUES ('3226', 'stargateradio.com');
INSERT INTO `free_email_provider` VALUES ('3227', 'starmail.com');
INSERT INTO `free_email_provider` VALUES ('3228', 'starmail.org');
INSERT INTO `free_email_provider` VALUES ('3229', 'starmedia.com');
INSERT INTO `free_email_provider` VALUES ('3230', 'starplace.com');
INSERT INTO `free_email_provider` VALUES ('3231', 'starspath.com');
INSERT INTO `free_email_provider` VALUES ('3232', 'stopdropandroll.com');
INSERT INTO `free_email_provider` VALUES ('3233', 'stribmail.com');
INSERT INTO `free_email_provider` VALUES ('3234', 'strompost.com');
INSERT INTO `free_email_provider` VALUES ('3235', 'strongguy.com');
INSERT INTO `free_email_provider` VALUES ('3236', 'subram.com');
INSERT INTO `free_email_provider` VALUES ('3237', 'sudanmail.net');
INSERT INTO `free_email_provider` VALUES ('3238', 'suhabi.com');
INSERT INTO `free_email_provider` VALUES ('3239', 'suisse.org');
INSERT INTO `free_email_provider` VALUES ('3240', 'sunpoint.net');
INSERT INTO `free_email_provider` VALUES ('3241', 'sunrise-sunset.com');
INSERT INTO `free_email_provider` VALUES ('3242', 'sunsgame.com');
INSERT INTO `free_email_provider` VALUES ('3243', 'sunumail.sn');
INSERT INTO `free_email_provider` VALUES ('3244', 'superdada.com');
INSERT INTO `free_email_provider` VALUES ('3245', 'supereva.it');
INSERT INTO `free_email_provider` VALUES ('3246', 'supermail.ru');
INSERT INTO `free_email_provider` VALUES ('3247', 'surf3.net');
INSERT INTO `free_email_provider` VALUES ('3248', 'surimail.com');
INSERT INTO `free_email_provider` VALUES ('3249', 'survivormail.com');
INSERT INTO `free_email_provider` VALUES ('3250', 'sweb.cz');
INSERT INTO `free_email_provider` VALUES ('3251', 'swiftdesk.com');
INSERT INTO `free_email_provider` VALUES ('3252', 'swirve.com');
INSERT INTO `free_email_provider` VALUES ('3253', 'swissinfo.org');
INSERT INTO `free_email_provider` VALUES ('3254', 'swissmail.net');
INSERT INTO `free_email_provider` VALUES ('3255', 'switzerland.org');
INSERT INTO `free_email_provider` VALUES ('3256', 'sx172.com');
INSERT INTO `free_email_provider` VALUES ('3257', 'syom.com');
INSERT INTO `free_email_provider` VALUES ('3258', 'syriamail.com');
INSERT INTO `free_email_provider` VALUES ('3259', 't2mail.com');
INSERT INTO `free_email_provider` VALUES ('3260', 'takuyakimura.com');
INSERT INTO `free_email_provider` VALUES ('3261', 'tamil.com');
INSERT INTO `free_email_provider` VALUES ('3262', 'tatanova.com');
INSERT INTO `free_email_provider` VALUES ('3263', 'tech4peace.org');
INSERT INTO `free_email_provider` VALUES ('3264', 'techemail.com');
INSERT INTO `free_email_provider` VALUES ('3265', 'techie.com');
INSERT INTO `free_email_provider` VALUES ('3266', 'technisamail.co.za');
INSERT INTO `free_email_provider` VALUES ('3267', 'teenagedirtbag.com');
INSERT INTO `free_email_provider` VALUES ('3268', 'teleline.es');
INSERT INTO `free_email_provider` VALUES ('3269', 'telinco.net');
INSERT INTO `free_email_provider` VALUES ('3270', 'telkom.net');
INSERT INTO `free_email_provider` VALUES ('3271', 'telpage.net');
INSERT INTO `free_email_provider` VALUES ('3272', 'tenchiclub.com');
INSERT INTO `free_email_provider` VALUES ('3273', 'tenderkiss.com');
INSERT INTO `free_email_provider` VALUES ('3274', 'terra.cl');
INSERT INTO `free_email_provider` VALUES ('3275', 'terra.com');
INSERT INTO `free_email_provider` VALUES ('3276', 'terra.com.ar');
INSERT INTO `free_email_provider` VALUES ('3277', 'terra.com.br');
INSERT INTO `free_email_provider` VALUES ('3278', 'terra.es');
INSERT INTO `free_email_provider` VALUES ('3279', 'tfanus.com.er');
INSERT INTO `free_email_provider` VALUES ('3280', 'thai.com');
INSERT INTO `free_email_provider` VALUES ('3281', 'thaimail.com');
INSERT INTO `free_email_provider` VALUES ('3282', 'thaimail.net');
INSERT INTO `free_email_provider` VALUES ('3283', 'the-african.com');
INSERT INTO `free_email_provider` VALUES ('3284', 'the-airforce.com');
INSERT INTO `free_email_provider` VALUES ('3285', 'the-aliens.com');
INSERT INTO `free_email_provider` VALUES ('3286', 'the-american.com');
INSERT INTO `free_email_provider` VALUES ('3287', 'the-animal.com');
INSERT INTO `free_email_provider` VALUES ('3288', 'the-army.com');
INSERT INTO `free_email_provider` VALUES ('3289', 'the-astronaut.com');
INSERT INTO `free_email_provider` VALUES ('3290', 'the-beauty.com');
INSERT INTO `free_email_provider` VALUES ('3291', 'the-big-apple.com');
INSERT INTO `free_email_provider` VALUES ('3292', 'the-biker.com');
INSERT INTO `free_email_provider` VALUES ('3293', 'the-boss.com');
INSERT INTO `free_email_provider` VALUES ('3294', 'the-brazilian.com');
INSERT INTO `free_email_provider` VALUES ('3295', 'the-canadian.com');
INSERT INTO `free_email_provider` VALUES ('3296', 'the-canuck.com');
INSERT INTO `free_email_provider` VALUES ('3297', 'the-captain.com');
INSERT INTO `free_email_provider` VALUES ('3298', 'the-chinese.com');
INSERT INTO `free_email_provider` VALUES ('3299', 'the-country.com');
INSERT INTO `free_email_provider` VALUES ('3300', 'the-cowboy.com');
INSERT INTO `free_email_provider` VALUES ('3301', 'the-davis-home.com');
INSERT INTO `free_email_provider` VALUES ('3302', 'the-dutchman.com');
INSERT INTO `free_email_provider` VALUES ('3303', 'the-eagles.com');
INSERT INTO `free_email_provider` VALUES ('3304', 'the-englishman.com');
INSERT INTO `free_email_provider` VALUES ('3305', 'the-fastest.net');
INSERT INTO `free_email_provider` VALUES ('3306', 'the-fool.com');
INSERT INTO `free_email_provider` VALUES ('3307', 'the-frenchman.com');
INSERT INTO `free_email_provider` VALUES ('3308', 'the-galaxy.net');
INSERT INTO `free_email_provider` VALUES ('3309', 'the-genius.com');
INSERT INTO `free_email_provider` VALUES ('3310', 'the-gentleman.com');
INSERT INTO `free_email_provider` VALUES ('3311', 'the-german.com');
INSERT INTO `free_email_provider` VALUES ('3312', 'the-gremlin.com');
INSERT INTO `free_email_provider` VALUES ('3313', 'the-hooligan.com');
INSERT INTO `free_email_provider` VALUES ('3314', 'the-italian.com');
INSERT INTO `free_email_provider` VALUES ('3315', 'the-japanese.com');
INSERT INTO `free_email_provider` VALUES ('3316', 'the-lair.com');
INSERT INTO `free_email_provider` VALUES ('3317', 'the-madman.com');
INSERT INTO `free_email_provider` VALUES ('3318', 'the-mailinglist.com');
INSERT INTO `free_email_provider` VALUES ('3319', 'the-marine.com');
INSERT INTO `free_email_provider` VALUES ('3320', 'the-master.com');
INSERT INTO `free_email_provider` VALUES ('3321', 'the-mexican.com');
INSERT INTO `free_email_provider` VALUES ('3322', 'the-ministry.com');
INSERT INTO `free_email_provider` VALUES ('3323', 'the-monkey.com');
INSERT INTO `free_email_provider` VALUES ('3324', 'the-newsletter.net');
INSERT INTO `free_email_provider` VALUES ('3325', 'the-pentagon.com');
INSERT INTO `free_email_provider` VALUES ('3326', 'the-police.com');
INSERT INTO `free_email_provider` VALUES ('3327', 'the-prayer.com');
INSERT INTO `free_email_provider` VALUES ('3328', 'the-professional.com');
INSERT INTO `free_email_provider` VALUES ('3329', 'the-quickest.com');
INSERT INTO `free_email_provider` VALUES ('3330', 'the-russian.com');
INSERT INTO `free_email_provider` VALUES ('3331', 'the-snake.com');
INSERT INTO `free_email_provider` VALUES ('3332', 'the-spaceman.com');
INSERT INTO `free_email_provider` VALUES ('3333', 'the-stock-market.com');
INSERT INTO `free_email_provider` VALUES ('3334', 'the-student.net');
INSERT INTO `free_email_provider` VALUES ('3335', 'the-whitehouse.net');
INSERT INTO `free_email_provider` VALUES ('3336', 'the-wild-west.com');
INSERT INTO `free_email_provider` VALUES ('3337', 'the18th.com');
INSERT INTO `free_email_provider` VALUES ('3338', 'thecoolguy.com');
INSERT INTO `free_email_provider` VALUES ('3339', 'thecriminals.com');
INSERT INTO `free_email_provider` VALUES ('3340', 'theend.hu');
INSERT INTO `free_email_provider` VALUES ('3341', 'thegolfcourse.com');
INSERT INTO `free_email_provider` VALUES ('3342', 'thegooner.com');
INSERT INTO `free_email_provider` VALUES ('3343', 'theheadoffice.com');
INSERT INTO `free_email_provider` VALUES ('3344', 'thelanddownunder.com');
INSERT INTO `free_email_provider` VALUES ('3345', 'theoffice.net');
INSERT INTO `free_email_provider` VALUES ('3346', 'thepokerface.com');
INSERT INTO `free_email_provider` VALUES ('3347', 'thepostmaster.net');
INSERT INTO `free_email_provider` VALUES ('3348', 'theraces.com');
INSERT INTO `free_email_provider` VALUES ('3349', 'theracetrack.com');
INSERT INTO `free_email_provider` VALUES ('3350', 'thestreetfighter.com');
INSERT INTO `free_email_provider` VALUES ('3351', 'theteebox.com');
INSERT INTO `free_email_provider` VALUES ('3352', 'thewatercooler.com');
INSERT INTO `free_email_provider` VALUES ('3353', 'thewebpros.co.uk');
INSERT INTO `free_email_provider` VALUES ('3354', 'thewizzard.com');
INSERT INTO `free_email_provider` VALUES ('3355', 'thewizzkid.com');
INSERT INTO `free_email_provider` VALUES ('3356', 'thezhangs.net');
INSERT INTO `free_email_provider` VALUES ('3357', 'thirdage.com');
INSERT INTO `free_email_provider` VALUES ('3358', 'thundermail.com');
INSERT INTO `free_email_provider` VALUES ('3359', 'tidni.com');
INSERT INTO `free_email_provider` VALUES ('3360', 'timein.net');
INSERT INTO `free_email_provider` VALUES ('3361', 'tiscali.at');
INSERT INTO `free_email_provider` VALUES ('3362', 'tiscali.be');
INSERT INTO `free_email_provider` VALUES ('3363', 'tiscali.co.uk');
INSERT INTO `free_email_provider` VALUES ('3364', 'tiscali.lu');
INSERT INTO `free_email_provider` VALUES ('3365', 'tiscali.se');
INSERT INTO `free_email_provider` VALUES ('3366', 'tkcity.com');
INSERT INTO `free_email_provider` VALUES ('3367', 'topgamers.co.uk');
INSERT INTO `free_email_provider` VALUES ('3368', 'topletter.com');
INSERT INTO `free_email_provider` VALUES ('3369', 'topmail.com.ar');
INSERT INTO `free_email_provider` VALUES ('3370', 'topsurf.com');
INSERT INTO `free_email_provider` VALUES ('3371', 'torchmail.com');
INSERT INTO `free_email_provider` VALUES ('3372', 'travel.li');
INSERT INTO `free_email_provider` VALUES ('3373', 'trialbytrivia.com');
INSERT INTO `free_email_provider` VALUES ('3374', 'trmailbox.com');
INSERT INTO `free_email_provider` VALUES ('3375', 'tropicalstorm.com');
INSERT INTO `free_email_provider` VALUES ('3376', 'trust-me.com');
INSERT INTO `free_email_provider` VALUES ('3377', 'tsamail.co.za');
INSERT INTO `free_email_provider` VALUES ('3378', 'ttml.co.in');
INSERT INTO `free_email_provider` VALUES ('3379', 'tunisiamail.com');
INSERT INTO `free_email_provider` VALUES ('3380', 'turkey.com');
INSERT INTO `free_email_provider` VALUES ('3381', 'twinstarsmail.com');
INSERT INTO `free_email_provider` VALUES ('3382', 'tycoonmail.com');
INSERT INTO `free_email_provider` VALUES ('3383', 'typemail.com');
INSERT INTO `free_email_provider` VALUES ('3384', 'u2club.com');
INSERT INTO `free_email_provider` VALUES ('3385', 'uae.ac');
INSERT INTO `free_email_provider` VALUES ('3386', 'uaemail.com');
INSERT INTO `free_email_provider` VALUES ('3387', 'ubbi.com');
INSERT INTO `free_email_provider` VALUES ('3388', 'ubbi.com.br');
INSERT INTO `free_email_provider` VALUES ('3389', 'uboot.com');
INSERT INTO `free_email_provider` VALUES ('3390', 'uk2k.com');
INSERT INTO `free_email_provider` VALUES ('3391', 'uk2net.com');
INSERT INTO `free_email_provider` VALUES ('3392', 'uk7.net');
INSERT INTO `free_email_provider` VALUES ('3393', 'uk8.net');
INSERT INTO `free_email_provider` VALUES ('3394', 'ukbuilder.com');
INSERT INTO `free_email_provider` VALUES ('3395', 'ukcool.com');
INSERT INTO `free_email_provider` VALUES ('3396', 'ukdreamcast.com');
INSERT INTO `free_email_provider` VALUES ('3397', 'uku.co.uk');
INSERT INTO `free_email_provider` VALUES ('3398', 'ultapulta.com');
INSERT INTO `free_email_provider` VALUES ('3399', 'ultrapostman.com');
INSERT INTO `free_email_provider` VALUES ('3400', 'ummah.org');
INSERT INTO `free_email_provider` VALUES ('3401', 'umpire.com');
INSERT INTO `free_email_provider` VALUES ('3402', 'unican.es');
INSERT INTO `free_email_provider` VALUES ('3403', 'unihome.com');
INSERT INTO `free_email_provider` VALUES ('3404', 'universal.pt');
INSERT INTO `free_email_provider` VALUES ('3405', 'uno.ee');
INSERT INTO `free_email_provider` VALUES ('3406', 'uno.it');
INSERT INTO `free_email_provider` VALUES ('3407', 'unofree.it');
INSERT INTO `free_email_provider` VALUES ('3408', 'uol.com.ar');
INSERT INTO `free_email_provider` VALUES ('3409', 'uol.com.br');
INSERT INTO `free_email_provider` VALUES ('3410', 'uol.com.co');
INSERT INTO `free_email_provider` VALUES ('3411', 'uol.com.mx');
INSERT INTO `free_email_provider` VALUES ('3412', 'uol.com.ve');
INSERT INTO `free_email_provider` VALUES ('3413', 'uole.com');
INSERT INTO `free_email_provider` VALUES ('3414', 'uole.com.ve');
INSERT INTO `free_email_provider` VALUES ('3415', 'uolmail.com');
INSERT INTO `free_email_provider` VALUES ('3416', 'uomail.com');
INSERT INTO `free_email_provider` VALUES ('3417', 'ureach.com');
INSERT INTO `free_email_provider` VALUES ('3418', 'urgentmail.biz');
INSERT INTO `free_email_provider` VALUES ('3419', 'usa.com');
INSERT INTO `free_email_provider` VALUES ('3420', 'usanetmail.com');
INSERT INTO `free_email_provider` VALUES ('3421', 'uyuyuy.com');
INSERT INTO `free_email_provider` VALUES ('3422', 'v-sexi.com');
INSERT INTO `free_email_provider` VALUES ('3423', 'velnet.co.uk');
INSERT INTO `free_email_provider` VALUES ('3424', 'velocall.com');
INSERT INTO `free_email_provider` VALUES ('3425', 'verizonmail.com');
INSERT INTO `free_email_provider` VALUES ('3426', 'veryfast.biz');
INSERT INTO `free_email_provider` VALUES ('3427', 'veryspeedy.net');
INSERT INTO `free_email_provider` VALUES ('3428', 'violinmakers.co.uk');
INSERT INTO `free_email_provider` VALUES ('3429', 'vip.gr');
INSERT INTO `free_email_provider` VALUES ('3430', 'vipmail.ru');
INSERT INTO `free_email_provider` VALUES ('3431', 'virgilio.it');
INSERT INTO `free_email_provider` VALUES ('3432', 'virgin.net');
INSERT INTO `free_email_provider` VALUES ('3433', 'virtualmail.com');
INSERT INTO `free_email_provider` VALUES ('3434', 'visitmail.com');
INSERT INTO `free_email_provider` VALUES ('3435', 'vivianhsu.net');
INSERT INTO `free_email_provider` VALUES ('3436', 'vjtimail.com');
INSERT INTO `free_email_provider` VALUES ('3437', 'vnn.vn');
INSERT INTO `free_email_provider` VALUES ('3438', 'volcanomail.com');
INSERT INTO `free_email_provider` VALUES ('3439', 'vote-democrats.com');
INSERT INTO `free_email_provider` VALUES ('3440', 'vote-hillary.com');
INSERT INTO `free_email_provider` VALUES ('3441', 'vote-republicans.com');
INSERT INTO `free_email_provider` VALUES ('3442', 'wahoye.com');
INSERT INTO `free_email_provider` VALUES ('3443', 'wales2000.net');
INSERT INTO `free_email_provider` VALUES ('3444', 'wam.co.za');
INSERT INTO `free_email_provider` VALUES ('3445', 'wanadoo.es');
INSERT INTO `free_email_provider` VALUES ('3446', 'warmmail.com');
INSERT INTO `free_email_provider` VALUES ('3447', 'warpmail.net');
INSERT INTO `free_email_provider` VALUES ('3448', 'warrior.hu');
INSERT INTO `free_email_provider` VALUES ('3449', 'waumail.com');
INSERT INTO `free_email_provider` VALUES ('3450', 'wearab.net');
INSERT INTO `free_email_provider` VALUES ('3451', 'web-mail.com.ar');
INSERT INTO `free_email_provider` VALUES ('3452', 'web-police.com');
INSERT INTO `free_email_provider` VALUES ('3453', 'web.de');
INSERT INTO `free_email_provider` VALUES ('3454', 'webave.com');
INSERT INTO `free_email_provider` VALUES ('3455', 'webcity.ca');
INSERT INTO `free_email_provider` VALUES ('3456', 'webdream.com');
INSERT INTO `free_email_provider` VALUES ('3457', 'webindia123.com');
INSERT INTO `free_email_provider` VALUES ('3458', 'webjump.com');
INSERT INTO `free_email_provider` VALUES ('3459', 'webmail.co.yu');
INSERT INTO `free_email_provider` VALUES ('3460', 'webmail.co.za');
INSERT INTO `free_email_provider` VALUES ('3461', 'webmail.hu');
INSERT INTO `free_email_provider` VALUES ('3462', 'webmails.com');
INSERT INTO `free_email_provider` VALUES ('3463', 'webprogramming.com');
INSERT INTO `free_email_provider` VALUES ('3464', 'webstation.com');
INSERT INTO `free_email_provider` VALUES ('3465', 'websurfer.co.za');
INSERT INTO `free_email_provider` VALUES ('3466', 'webtopmail.com');
INSERT INTO `free_email_provider` VALUES ('3467', 'weedmail.com');
INSERT INTO `free_email_provider` VALUES ('3468', 'weekonline.com');
INSERT INTO `free_email_provider` VALUES ('3469', 'wehshee.com');
INSERT INTO `free_email_provider` VALUES ('3470', 'welsh-lady.com');
INSERT INTO `free_email_provider` VALUES ('3471', 'whartontx.com');
INSERT INTO `free_email_provider` VALUES ('3472', 'wheelweb.com');
INSERT INTO `free_email_provider` VALUES ('3473', 'whipmail.com');
INSERT INTO `free_email_provider` VALUES ('3474', 'whoever.com');
INSERT INTO `free_email_provider` VALUES ('3475', 'whoopymail.com');
INSERT INTO `free_email_provider` VALUES ('3476', 'winmail.com.au');
INSERT INTO `free_email_provider` VALUES ('3477', 'winning.com');
INSERT INTO `free_email_provider` VALUES ('3478', 'witty.com');
INSERT INTO `free_email_provider` VALUES ('3479', 'wolf-web.com');
INSERT INTO `free_email_provider` VALUES ('3480', 'wombles.com');
INSERT INTO `free_email_provider` VALUES ('3481', 'wooow.it');
INSERT INTO `free_email_provider` VALUES ('3482', 'worldemail.com');
INSERT INTO `free_email_provider` VALUES ('3483', 'wosaddict.com');
INSERT INTO `free_email_provider` VALUES ('3484', 'wouldilie.com');
INSERT INTO `free_email_provider` VALUES ('3485', 'wp.pl');
INSERT INTO `free_email_provider` VALUES ('3486', 'wrexham.net');
INSERT INTO `free_email_provider` VALUES ('3487', 'writemeback.com');
INSERT INTO `free_email_provider` VALUES ('3488', 'wrongmail.com');
INSERT INTO `free_email_provider` VALUES ('3489', 'www.com');
INSERT INTO `free_email_provider` VALUES ('3490', 'wx88.net');
INSERT INTO `free_email_provider` VALUES ('3491', 'wxs.net');
INSERT INTO `free_email_provider` VALUES ('3492', 'x-mail.net');
INSERT INTO `free_email_provider` VALUES ('3493', 'x5g.com');
INSERT INTO `free_email_provider` VALUES ('3494', 'xmsg.com');
INSERT INTO `free_email_provider` VALUES ('3495', 'xoom.com');
INSERT INTO `free_email_provider` VALUES ('3496', 'xsmail.com');
INSERT INTO `free_email_provider` VALUES ('3497', 'xuno.com');
INSERT INTO `free_email_provider` VALUES ('3498', 'xzapmail.com');
INSERT INTO `free_email_provider` VALUES ('3499', 'yada-yada.com');
INSERT INTO `free_email_provider` VALUES ('3500', 'yaho.com');
INSERT INTO `free_email_provider` VALUES ('3501', 'yahoo.ca');
INSERT INTO `free_email_provider` VALUES ('3502', 'yahoo.co.in');
INSERT INTO `free_email_provider` VALUES ('3503', 'yahoo.co.jp');
INSERT INTO `free_email_provider` VALUES ('3504', 'yahoo.co.kr');
INSERT INTO `free_email_provider` VALUES ('3505', 'yahoo.co.nz');
INSERT INTO `free_email_provider` VALUES ('3506', 'yahoo.co.uk');
INSERT INTO `free_email_provider` VALUES ('3507', 'yahoo.com.ar');
INSERT INTO `free_email_provider` VALUES ('3508', 'yahoo.com.au');
INSERT INTO `free_email_provider` VALUES ('3509', 'yahoo.com.br');
INSERT INTO `free_email_provider` VALUES ('3510', 'yahoo.com.cn');
INSERT INTO `free_email_provider` VALUES ('3511', 'yahoo.com.hk');
INSERT INTO `free_email_provider` VALUES ('3512', 'yahoo.com.is');
INSERT INTO `free_email_provider` VALUES ('3513', 'yahoo.com.mx');
INSERT INTO `free_email_provider` VALUES ('3514', 'yahoo.com.ru');
INSERT INTO `free_email_provider` VALUES ('3515', 'yahoo.com.sg');
INSERT INTO `free_email_provider` VALUES ('3516', 'yahoo.de');
INSERT INTO `free_email_provider` VALUES ('3517', 'yahoo.dk');
INSERT INTO `free_email_provider` VALUES ('3518', 'yahoo.es');
INSERT INTO `free_email_provider` VALUES ('3519', 'yahoo.fr');
INSERT INTO `free_email_provider` VALUES ('3520', 'yahoo.ie');
INSERT INTO `free_email_provider` VALUES ('3521', 'yahoo.it');
INSERT INTO `free_email_provider` VALUES ('3522', 'yahoo.jp');
INSERT INTO `free_email_provider` VALUES ('3523', 'yahoo.ru');
INSERT INTO `free_email_provider` VALUES ('3524', 'yahoo.se');
INSERT INTO `free_email_provider` VALUES ('3525', 'yahoofs.com');
INSERT INTO `free_email_provider` VALUES ('3526', 'yalla.com');
INSERT INTO `free_email_provider` VALUES ('3527', 'yalla.com.lb');
INSERT INTO `free_email_provider` VALUES ('3528', 'yalook.com');
INSERT INTO `free_email_provider` VALUES ('3529', 'yam.com');
INSERT INTO `free_email_provider` VALUES ('3530', 'yapost.com');
INSERT INTO `free_email_provider` VALUES ('3531', 'yebox.com');
INSERT INTO `free_email_provider` VALUES ('3532', 'yehey.com');
INSERT INTO `free_email_provider` VALUES ('3533', 'yemenmail.com');
INSERT INTO `free_email_provider` VALUES ('3534', 'yepmail.net');
INSERT INTO `free_email_provider` VALUES ('3535', 'yifan.net');
INSERT INTO `free_email_provider` VALUES ('3536', 'yopolis.com');
INSERT INTO `free_email_provider` VALUES ('3537', 'youareadork.com');
INSERT INTO `free_email_provider` VALUES ('3538', 'your-house.com');
INSERT INTO `free_email_provider` VALUES ('3539', 'yourinbox.com');
INSERT INTO `free_email_provider` VALUES ('3540', 'yourlover.net');
INSERT INTO `free_email_provider` VALUES ('3541', 'yournightmare.com');
INSERT INTO `free_email_provider` VALUES ('3542', 'yours.com');
INSERT INTO `free_email_provider` VALUES ('3543', 'yourssincerely.com');
INSERT INTO `free_email_provider` VALUES ('3544', 'yourteacher.net');
INSERT INTO `free_email_provider` VALUES ('3545', 'yourwap.com');
INSERT INTO `free_email_provider` VALUES ('3546', 'yuuhuu.net');
INSERT INTO `free_email_provider` VALUES ('3547', 'yyhmail.com');
INSERT INTO `free_email_provider` VALUES ('3548', 'zahadum.com');
INSERT INTO `free_email_provider` VALUES ('3549', 'zeepost.nl');
INSERT INTO `free_email_provider` VALUES ('3550', 'zhaowei.net');
INSERT INTO `free_email_provider` VALUES ('3551', 'zip.net');
INSERT INTO `free_email_provider` VALUES ('3552', 'zipido.com');
INSERT INTO `free_email_provider` VALUES ('3553', 'ziplip.com');
INSERT INTO `free_email_provider` VALUES ('3554', 'zipmail.com');
INSERT INTO `free_email_provider` VALUES ('3555', 'zipmail.com.br');
INSERT INTO `free_email_provider` VALUES ('3556', 'zipmax.com');
INSERT INTO `free_email_provider` VALUES ('3557', 'zmail.ru');
INSERT INTO `free_email_provider` VALUES ('3558', 'zonnet.nl');
INSERT INTO `free_email_provider` VALUES ('3559', 'zubee.com');
INSERT INTO `free_email_provider` VALUES ('3560', 'zuvio.com');
INSERT INTO `free_email_provider` VALUES ('3561', 'zwallet.com');
INSERT INTO `free_email_provider` VALUES ('3562', 'zybermail.com');
INSERT INTO `free_email_provider` VALUES ('3563', 'zzn.com');
INSERT INTO `free_email_provider` VALUES ('3564', 'zzom.co.uk');
INSERT INTO `free_email_provider` VALUES ('3565', 'webnames.ru');
INSERT INTO `free_email_provider` VALUES ('3566', 'mail.online.ua');
INSERT INTO `free_email_provider` VALUES ('3567', 'online.ua');
INSERT INTO `free_email_provider` VALUES ('3568', 'ua.fm');
INSERT INTO `free_email_provider` VALUES ('3569', 'mail.qip.ru');
INSERT INTO `free_email_provider` VALUES ('3570', 'qip.ru');
INSERT INTO `free_email_provider` VALUES ('3571', 'inbox.ru');
INSERT INTO `free_email_provider` VALUES ('3572', 'bk.ru');
INSERT INTO `free_email_provider` VALUES ('3573', 'list.ru');
INSERT INTO `free_email_provider` VALUES ('3574', 'yandex.com');

-- ----------------------------
-- Table structure for friendship
-- ----------------------------
DROP TABLE IF EXISTS `friendship`;
CREATE TABLE `friendship` (
  `inviter_id` int(11) NOT NULL,
  `friend_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `acknowledgetime` int(11) DEFAULT NULL,
  `requesttime` int(11) DEFAULT NULL,
  `updatetime` int(11) DEFAULT NULL,
  `message` varchar(255) NOT NULL,
  PRIMARY KEY (`inviter_id`,`friend_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of friendship
-- ----------------------------

-- ----------------------------
-- Table structure for hero_behaviour
-- ----------------------------
DROP TABLE IF EXISTS `hero_behaviour`;
CREATE TABLE `hero_behaviour` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL COMMENT 'Номер требуемого поведения',
  `title` text NOT NULL,
  `scale` float(10,2) DEFAULT NULL COMMENT 'Scale',
  `type_scale` tinyint(4) DEFAULT NULL,
  `import_id` varchar(14) NOT NULL DEFAULT '00000000000000' COMMENT 'setvice value,used to remove old data after reimport.',
  `scenario_id` int(11) NOT NULL,
  `group_id` int(11) DEFAULT NULL,
  `learning_goal_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `hero_behaviour_scenario` (`scenario_id`),
  KEY `fk_hero_behaviour_group_id` (`group_id`),
  KEY `hero_behaviour_learning_goal` (`learning_goal_id`),
  CONSTRAINT `hero_behaviour_learning_goal` FOREIGN KEY (`learning_goal_id`) REFERENCES `learning_goal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_hero_behaviour_group_id` FOREIGN KEY (`group_id`) REFERENCES `assessment_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `hero_behaviour_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Наименования требуемых поведений';

-- ----------------------------
-- Records of hero_behaviour
-- ----------------------------

-- ----------------------------
-- Table structure for industry
-- ----------------------------
DROP TABLE IF EXISTS `industry`;
CREATE TABLE `industry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `industry_I_id_language` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of industry
-- ----------------------------
INSERT INTO `industry` VALUES ('3', 'Автомобильный бизнес');
INSERT INTO `industry` VALUES ('4', 'Агропромышленный комплекс');
INSERT INTO `industry` VALUES ('5', 'Финансовые услуги');
INSERT INTO `industry` VALUES ('6', 'Информационные технологии, интернет, телеком');
INSERT INTO `industry` VALUES ('7', 'Добыча и металлургия');
INSERT INTO `industry` VALUES ('8', 'Лесная и деревообрабатывающая промышленность');
INSERT INTO `industry` VALUES ('9', 'Машиностроение');
INSERT INTO `industry` VALUES ('10', 'Медицина и фармацевтика');
INSERT INTO `industry` VALUES ('11', 'Недвижимость, девелопмент, строительство');
INSERT INTO `industry` VALUES ('12', 'Оборудование, приборостроение, электротехника');
INSERT INTO `industry` VALUES ('13', 'Cтроительные материалы и оборудование');
INSERT INTO `industry` VALUES ('14', 'Производство товаров массового спроса');
INSERT INTO `industry` VALUES ('15', 'Розничная торговля');
INSERT INTO `industry` VALUES ('16', 'Топливно-энергетический комплекс');
INSERT INTO `industry` VALUES ('17', 'Транспорт и логистика');
INSERT INTO `industry` VALUES ('18', 'Туризм, отдых, гостеприимство');
INSERT INTO `industry` VALUES ('19', 'Химическая промышленность');
INSERT INTO `industry` VALUES ('20', 'Профессиональные услуги');
INSERT INTO `industry` VALUES ('21', 'Услуги населению');
INSERT INTO `industry` VALUES ('22', 'Наука, образование');
INSERT INTO `industry` VALUES ('23', 'Искусство, развлечения, масс-медиа');
INSERT INTO `industry` VALUES ('24', 'Государственная служба, некоммерческие организации');
INSERT INTO `industry` VALUES ('25', 'Авиация и космос');
INSERT INTO `industry` VALUES ('26', 'Управляющие компании и холдинги');
INSERT INTO `industry` VALUES ('27', 'Другая');

-- ----------------------------
-- Table structure for invites
-- ----------------------------
DROP TABLE IF EXISTS `invites`;
CREATE TABLE `invites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(10) unsigned DEFAULT NULL,
  `receiver_id` int(10) unsigned DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `message` text,
  `signature` varchar(255) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `vacancy_id` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `sent_time` int(11) DEFAULT NULL,
  `simulation_id` int(11) DEFAULT NULL,
  `scenario_id` int(11) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `tutorial_scenario_id` int(11) DEFAULT NULL,
  `tutorial_displayed_at` datetime DEFAULT NULL,
  `tutorial_finished_at` datetime DEFAULT NULL,
  `can_be_reloaded` tinyint(1) DEFAULT '1',
  `is_display_simulation_results` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `invites_code_unique` (`code`),
  KEY `invites_fk_simulation_id` (`simulation_id`),
  KEY `fk_invites_owner_id` (`owner_id`),
  KEY `fk_invites_receiver_id` (`receiver_id`),
  KEY `fk_invites_vacancy_id` (`vacancy_id`),
  KEY `fk_invites_tutorial_scenario_id` (`tutorial_scenario_id`),
  CONSTRAINT `fk_invites_owner_id` FOREIGN KEY (`owner_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_invites_receiver_id` FOREIGN KEY (`receiver_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_invites_tutorial_scenario_id` FOREIGN KEY (`tutorial_scenario_id`) REFERENCES `scenario` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_invites_vacancy_id` FOREIGN KEY (`vacancy_id`) REFERENCES `vacancy` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `invites_fk_simulation_id` FOREIGN KEY (`simulation_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of invites
-- ----------------------------

-- ----------------------------
-- Table structure for invoice
-- ----------------------------
DROP TABLE IF EXISTS `invoice`;
CREATE TABLE `invoice` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `tariff_id` int(11) DEFAULT NULL,
  `amount` float DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `payment_system` varchar(100) DEFAULT NULL,
  `additional_data` text,
  `comment` text,
  `month_selected` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_ID` (`user_id`),
  KEY `tariff_id` (`tariff_id`),
  CONSTRAINT `tariff_id_key` FOREIGN KEY (`tariff_id`) REFERENCES `tariff` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `user_id_key` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of invoice
-- ----------------------------

-- ----------------------------
-- Table structure for learning_area
-- ----------------------------
DROP TABLE IF EXISTS `learning_area`;
CREATE TABLE `learning_area` (
  `code` varchar(10) NOT NULL,
  `title` text,
  `import_id` varchar(14) DEFAULT NULL,
  `scenario_id` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `learning_area_uniq` (`code`,`scenario_id`),
  KEY `learning_area_scenario` (`scenario_id`),
  CONSTRAINT `learning_area_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of learning_area
-- ----------------------------

-- ----------------------------
-- Table structure for learning_goal
-- ----------------------------
DROP TABLE IF EXISTS `learning_goal`;
CREATE TABLE `learning_goal` (
  `code` varchar(10) NOT NULL,
  `title` text,
  `import_id` varchar(14) NOT NULL DEFAULT '00000000000000',
  `scenario_id` int(11) NOT NULL,
  `learning_area_code` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `learning_goal_group_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `learning_goal_uniq` (`code`,`scenario_id`),
  KEY `learning_goal_scenario` (`scenario_id`),
  KEY `learning_goal_area` (`learning_area_code`),
  CONSTRAINT `learning_goal_area` FOREIGN KEY (`learning_area_code`) REFERENCES `learning_area` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `learning_goal_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of learning_goal
-- ----------------------------

-- ----------------------------
-- Table structure for learning_goal_group
-- ----------------------------
DROP TABLE IF EXISTS `learning_goal_group`;
CREATE TABLE `learning_goal_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(5) NOT NULL,
  `title` text,
  `import_id` varchar(14) NOT NULL DEFAULT '00000000000000',
  `scenario_id` int(11) NOT NULL,
  `learning_area_code` varchar(10) NOT NULL,
  `learning_area_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_simulation_learning_goal_group_learning_area_id` (`learning_area_id`),
  CONSTRAINT `fk_simulation_learning_goal_group_learning_area_id` FOREIGN KEY (`learning_area_id`) REFERENCES `learning_area` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of learning_goal_group
-- ----------------------------

-- ----------------------------
-- Table structure for log_account_invite
-- ----------------------------
DROP TABLE IF EXISTS `log_account_invite`;
CREATE TABLE `log_account_invite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `direction` varchar(10) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `limit_after_transaction` int(11) DEFAULT NULL,
  `comment` text,
  `date` datetime DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `invites_limit_referrals` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of log_account_invite
-- ----------------------------

-- ----------------------------
-- Table structure for log_activity_action
-- ----------------------------
DROP TABLE IF EXISTS `log_activity_action`;
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
  `meeting_id` int(11) DEFAULT NULL,
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

-- ----------------------------
-- Records of log_activity_action
-- ----------------------------

-- ----------------------------
-- Table structure for log_activity_action_agregated
-- ----------------------------
DROP TABLE IF EXISTS `log_activity_action_agregated`;
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
  `keep_last_category_after_60_sec` tinyint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `log_activity_action_agregated_FK_activity_action` (`activity_action_id`),
  KEY `log_activity_action_agregated_FK_simulations` (`sim_id`),
  CONSTRAINT `log_activity_action_agregated_FK_activity_action` FOREIGN KEY (`activity_action_id`) REFERENCES `activity_action` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `log_activity_action_agregated_FK_simulations` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1144 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of log_activity_action_agregated
-- ----------------------------

-- ----------------------------
-- Table structure for log_activity_action_agregated_214d
-- ----------------------------
DROP TABLE IF EXISTS `log_activity_action_agregated_214d`;
CREATE TABLE `log_activity_action_agregated_214d` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `leg_type` varchar(30) DEFAULT NULL COMMENT 'Just text label',
  `leg_action` varchar(30) DEFAULT NULL COMMENT 'Just text label',
  `activity_action_id` int(11) DEFAULT NULL,
  `category` varchar(30) DEFAULT NULL COMMENT 'Just text label',
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `duration` time NOT NULL,
  `parent` varchar(10) NOT NULL,
  `keep_last_category_initial` tinyint(1) DEFAULT '0',
  `keep_last_category_after` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `log_activity_action_agregated_214d_FK_activity_action` (`activity_action_id`),
  KEY `log_activity_action_agregated_214d_FK_simulations` (`sim_id`),
  CONSTRAINT `log_activity_action_agregated_214d_FK_activity_action` FOREIGN KEY (`activity_action_id`) REFERENCES `activity_action` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `log_activity_action_agregated_214d_FK_simulations` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of log_activity_action_agregated_214d
-- ----------------------------

-- ----------------------------
-- Table structure for log_assessment_214g
-- ----------------------------
DROP TABLE IF EXISTS `log_assessment_214g`;
CREATE TABLE `log_assessment_214g` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `start_time` time DEFAULT NULL,
  `code` varchar(10) DEFAULT NULL,
  `parent` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_log_assessment_214g_sim_id` (`sim_id`),
  CONSTRAINT `fk_log_assessment_214g_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of log_assessment_214g
-- ----------------------------

-- ----------------------------
-- Table structure for log_communication_theme_usage
-- ----------------------------
DROP TABLE IF EXISTS `log_communication_theme_usage`;
CREATE TABLE `log_communication_theme_usage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `communication_theme_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_log_communication_theme_usage_sim_id` (`sim_id`),
  KEY `fk_log_communication_theme_usage_communication_theme_id` (`communication_theme_id`),
  CONSTRAINT `fk_log_communication_theme_usage_communication_theme_id` FOREIGN KEY (`communication_theme_id`) REFERENCES `communication_themes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_log_communication_theme_usage_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of log_communication_theme_usage
-- ----------------------------

-- ----------------------------
-- Table structure for log_dialogs
-- ----------------------------
DROP TABLE IF EXISTS `log_dialogs`;
CREATE TABLE `log_dialogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `dialog_id` int(11) DEFAULT NULL,
  `last_id` int(11) DEFAULT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL DEFAULT '00:00:00',
  `window_uid` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `log_dialogs_sim_id` (`sim_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4361 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of log_dialogs
-- ----------------------------

-- ----------------------------
-- Table structure for log_documents
-- ----------------------------
DROP TABLE IF EXISTS `log_documents`;
CREATE TABLE `log_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL DEFAULT '00:00:00',
  `window_uid` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `log_documents_sim_id` (`sim_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1332 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of log_documents
-- ----------------------------

-- ----------------------------
-- Table structure for log_import
-- ----------------------------
DROP TABLE IF EXISTS `log_import`;
CREATE TABLE `log_import` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `scenario_id` int(11) DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `finished_at` datetime DEFAULT NULL,
  `text` longblob,
  PRIMARY KEY (`id`),
  KEY `log_import_fk_user` (`user_id`),
  KEY `log_import_fk_scenario` (`scenario_id`),
  CONSTRAINT `log_import_fk_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `log_import_fk_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of log_import
-- ----------------------------

-- ----------------------------
-- Table structure for log_incoming_call_sound_switcher
-- ----------------------------
DROP TABLE IF EXISTS `log_incoming_call_sound_switcher`;
CREATE TABLE `log_incoming_call_sound_switcher` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `is_play` int(1) NOT NULL,
  `sound_alias` varchar(50) NOT NULL,
  `game_time` time NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_log_incoming_call_sound_switcher_sim_id` (`sim_id`),
  CONSTRAINT `fk_log_incoming_call_sound_switcher_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of log_incoming_call_sound_switcher
-- ----------------------------

-- ----------------------------
-- Table structure for log_invite
-- ----------------------------
DROP TABLE IF EXISTS `log_invite`;
CREATE TABLE `log_invite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invite_id` int(11) DEFAULT NULL,
  `status` varchar(40) DEFAULT NULL,
  `sim_id` int(11) DEFAULT NULL,
  `action` text,
  `comment` text,
  `real_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of log_invite
-- ----------------------------

-- ----------------------------
-- Table structure for log_mail
-- ----------------------------
DROP TABLE IF EXISTS `log_mail`;
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
  PRIMARY KEY (`id`),
  KEY `log_mail_sim_id` (`sim_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19409 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of log_mail
-- ----------------------------

-- ----------------------------
-- Table structure for log_meeting
-- ----------------------------
DROP TABLE IF EXISTS `log_meeting`;
CREATE TABLE `log_meeting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `meeting_id` int(11) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time DEFAULT NULL,
  `window_uid` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `log_meeting_unique_record` (`sim_id`,`meeting_id`),
  KEY `fk_log_meeting_meeting_id` (`meeting_id`),
  CONSTRAINT `fk_log_meeting_meeting_id` FOREIGN KEY (`meeting_id`) REFERENCES `meeting` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_log_meeting_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of log_meeting
-- ----------------------------

-- ----------------------------
-- Table structure for log_payment
-- ----------------------------
DROP TABLE IF EXISTS `log_payment`;
CREATE TABLE `log_payment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) DEFAULT NULL,
  `text` text,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of log_payment
-- ----------------------------

-- ----------------------------
-- Table structure for log_replica
-- ----------------------------
DROP TABLE IF EXISTS `log_replica`;
CREATE TABLE `log_replica` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `replica_id` int(11) NOT NULL,
  `time` time NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_log_replica_sim_id` (`sim_id`),
  KEY `fk_log_replica_replica_id` (`replica_id`),
  CONSTRAINT `fk_log_replica_replica_id` FOREIGN KEY (`replica_id`) REFERENCES `replica` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_log_replica_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of log_replica
-- ----------------------------

-- ----------------------------
-- Table structure for log_server_request
-- ----------------------------
DROP TABLE IF EXISTS `log_server_request`;
CREATE TABLE `log_server_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) DEFAULT NULL,
  `request_uid` varchar(100) NOT NULL,
  `request_url` varchar(100) NOT NULL,
  `request_body` longblob,
  `response_body` longblob,
  `frontend_game_time` time NOT NULL,
  `backend_game_time` time DEFAULT NULL,
  `real_time` datetime NOT NULL,
  `is_processed` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_log_server_request_sim_id` (`sim_id`),
  CONSTRAINT `fk_log_server_request_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of log_server_request
-- ----------------------------

-- ----------------------------
-- Table structure for log_simulation
-- ----------------------------
DROP TABLE IF EXISTS `log_simulation`;
CREATE TABLE `log_simulation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invite_id` int(11) DEFAULT NULL,
  `sim_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `scenario_name` varchar(20) DEFAULT NULL,
  `mode` varchar(20) DEFAULT NULL,
  `action` text,
  `game_time_frontend` time DEFAULT NULL,
  `game_time_backend` time DEFAULT NULL,
  `comment` text,
  `real_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of log_simulation
-- ----------------------------

-- ----------------------------
-- Table structure for log_windows
-- ----------------------------
DROP TABLE IF EXISTS `log_windows`;
CREATE TABLE `log_windows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `window` tinyint(4) DEFAULT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL DEFAULT '00:00:00',
  `window_uid` varchar(32) DEFAULT NULL COMMENT 'md5',
  PRIMARY KEY (`id`),
  KEY `log_windows_sim_id` (`sim_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of log_windows
-- ----------------------------

-- ----------------------------
-- Table structure for mail_attachments
-- ----------------------------
DROP TABLE IF EXISTS `mail_attachments`;
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

-- ----------------------------
-- Records of mail_attachments
-- ----------------------------

-- ----------------------------
-- Table structure for mail_attachments_template
-- ----------------------------
DROP TABLE IF EXISTS `mail_attachments_template`;
CREATE TABLE `mail_attachments_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_id` int(11) DEFAULT NULL,
  `file_id` int(11) DEFAULT NULL,
  `import_id` varchar(14) NOT NULL COMMENT 'setvice value,used to remove old data after reimport.',
  `scenario_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_mail_attachments_template_mail_id` (`mail_id`),
  KEY `fk_mail_attachments_template_file_id` (`file_id`),
  KEY `mail_attachments_template_scenario` (`scenario_id`),
  CONSTRAINT `mail_attachments_template_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mail_attachments_template_file_id` FOREIGN KEY (`file_id`) REFERENCES `my_documents_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mail_attachments_template_mail_id` FOREIGN KEY (`mail_id`) REFERENCES `mail_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Шаблоны вложений писем';

-- ----------------------------
-- Records of mail_attachments_template
-- ----------------------------

-- ----------------------------
-- Table structure for mail_box
-- ----------------------------
DROP TABLE IF EXISTS `mail_box`;
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

-- ----------------------------
-- Records of mail_box
-- ----------------------------

-- ----------------------------
-- Table structure for mail_constructor
-- ----------------------------
DROP TABLE IF EXISTS `mail_constructor`;
CREATE TABLE `mail_constructor` (
  `code` varchar(11) NOT NULL,
  `import_id` varchar(60) NOT NULL,
  `scenario_id` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mail_constructor_uniq` (`code`,`scenario_id`),
  KEY `mail_constructor_scenario` (`scenario_id`),
  CONSTRAINT `mail_constructor_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mail_constructor
-- ----------------------------

-- ----------------------------
-- Table structure for mail_copies
-- ----------------------------
DROP TABLE IF EXISTS `mail_copies`;
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

-- ----------------------------
-- Records of mail_copies
-- ----------------------------

-- ----------------------------
-- Table structure for mail_copies_template
-- ----------------------------
DROP TABLE IF EXISTS `mail_copies_template`;
CREATE TABLE `mail_copies_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) NOT NULL,
  `scenario_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_mail_copies_template_mail_id` (`mail_id`),
  KEY `fk_mail_copies_template_receiver_id` (`receiver_id`),
  KEY `mail_copies_template_scenario` (`scenario_id`),
  CONSTRAINT `mail_copies_template_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mail_copies_template_mail_id` FOREIGN KEY (`mail_id`) REFERENCES `mail_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mail_copies_template_receiver_id` FOREIGN KEY (`receiver_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Копии шаблонов писем';

-- ----------------------------
-- Records of mail_copies_template
-- ----------------------------

-- ----------------------------
-- Table structure for mail_group
-- ----------------------------
DROP TABLE IF EXISTS `mail_group`;
CREATE TABLE `mail_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT 'название группы',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='Группы писем';

-- ----------------------------
-- Records of mail_group
-- ----------------------------
INSERT INTO `mail_group` VALUES ('1', 'Входящие');
INSERT INTO `mail_group` VALUES ('2', 'Черновики');
INSERT INTO `mail_group` VALUES ('3', 'Исходящие');
INSERT INTO `mail_group` VALUES ('4', 'Корзина');
INSERT INTO `mail_group` VALUES ('5', 'не пришло');

-- ----------------------------
-- Table structure for mail_messages
-- ----------------------------
DROP TABLE IF EXISTS `mail_messages`;
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

-- ----------------------------
-- Records of mail_messages
-- ----------------------------

-- ----------------------------
-- Table structure for mail_phrases
-- ----------------------------
DROP TABLE IF EXISTS `mail_phrases`;
CREATE TABLE `mail_phrases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `character_theme_id` int(11) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  `phrase_type` tinyint(1) DEFAULT NULL,
  `import_id` varchar(60) DEFAULT NULL,
  `scenario_id` int(11) NOT NULL,
  `constructor_id` int(11) NOT NULL,
  `column_number` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_mail_phrases_character_theme_id` (`character_theme_id`),
  KEY `mail_phrases_scenario` (`scenario_id`),
  KEY `mail_phrases_constructor` (`constructor_id`),
  CONSTRAINT `fk_mail_phrases_character_theme_id` FOREIGN KEY (`character_theme_id`) REFERENCES `communication_themes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mail_phrases_constructor` FOREIGN KEY (`constructor_id`) REFERENCES `mail_constructor` (`id`),
  CONSTRAINT `mail_phrases_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Фразы для сообщения';

-- ----------------------------
-- Records of mail_phrases
-- ----------------------------

-- ----------------------------
-- Table structure for mail_points
-- ----------------------------
DROP TABLE IF EXISTS `mail_points`;
CREATE TABLE `mail_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_id` int(11) NOT NULL,
  `point_id` int(11) NOT NULL,
  `add_value` int(11) NOT NULL COMMENT 'добавочное кол-во очков за данный ответ',
  `import_id` varchar(14) DEFAULT NULL COMMENT 'setvice value,used to remove old data after reimport.',
  `scenario_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_mail_points_mail_id` (`mail_id`),
  KEY `fk_mail_points_point_id` (`point_id`),
  KEY `mail_points_scenario` (`scenario_id`),
  CONSTRAINT `mail_points_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mail_points_dialog_id` FOREIGN KEY (`mail_id`) REFERENCES `mail_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mail_points_point_id` FOREIGN KEY (`point_id`) REFERENCES `hero_behaviour` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Очки для почты';

-- ----------------------------
-- Records of mail_points
-- ----------------------------

-- ----------------------------
-- Table structure for mail_prefix
-- ----------------------------
DROP TABLE IF EXISTS `mail_prefix`;
CREATE TABLE `mail_prefix` (
  `code` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mail_prefix
-- ----------------------------
INSERT INTO `mail_prefix` VALUES ('fwd', 'Fwd:');
INSERT INTO `mail_prefix` VALUES ('fwdfwd', 'Fwd: Fwd: ');
INSERT INTO `mail_prefix` VALUES ('fwdre', 'Fwd: Re: ');
INSERT INTO `mail_prefix` VALUES ('fwdrefwd', 'Fwd: Re: Fwd:');
INSERT INTO `mail_prefix` VALUES ('fwdrere', 'Fwd: Re: Re:');
INSERT INTO `mail_prefix` VALUES ('fwdrerere', 'Fwd: Re: Re: Re:');
INSERT INTO `mail_prefix` VALUES ('re', 'Re:');
INSERT INTO `mail_prefix` VALUES ('refwd', 'Re: Fwd: ');
INSERT INTO `mail_prefix` VALUES ('rere', 'Re: Re:');
INSERT INTO `mail_prefix` VALUES ('rerefwd', 'Re: Re: Fwd:');
INSERT INTO `mail_prefix` VALUES ('rerere', 'Re: Re: Re:');
INSERT INTO `mail_prefix` VALUES ('rererere', 'Re:: Re: Re: Re:');

-- ----------------------------
-- Table structure for mail_receivers
-- ----------------------------
DROP TABLE IF EXISTS `mail_receivers`;
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

-- ----------------------------
-- Records of mail_receivers
-- ----------------------------

-- ----------------------------
-- Table structure for mail_receivers_template
-- ----------------------------
DROP TABLE IF EXISTS `mail_receivers_template`;
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

-- ----------------------------
-- Records of mail_receivers_template
-- ----------------------------

-- ----------------------------
-- Table structure for mail_tasks
-- ----------------------------
DROP TABLE IF EXISTS `mail_tasks`;
CREATE TABLE `mail_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `code` varchar(5) DEFAULT NULL,
  `wr` char(1) DEFAULT NULL,
  `category` tinyint(1) DEFAULT NULL,
  `import_id` varchar(14) DEFAULT NULL COMMENT 'setvice value,used to remove old data after reimport.',
  `scenario_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_mail_tasks_mail_id` (`mail_id`),
  KEY `mail_tasks_scenario` (`scenario_id`),
  CONSTRAINT `mail_tasks_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mail_tasks_mail_id` FOREIGN KEY (`mail_id`) REFERENCES `mail_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Задачи, которые можно создать на основании письма';

-- ----------------------------
-- Records of mail_tasks
-- ----------------------------

-- ----------------------------
-- Table structure for mail_template
-- ----------------------------
DROP TABLE IF EXISTS `mail_template`;
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
  `scenario_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_mail_template_group_id` (`group_id`),
  KEY `fk_mail_template_sender_id` (`sender_id`),
  KEY `fk_mail_template_receiver_id` (`receiver_id`),
  KEY `fk_mail_template_subject_id` (`subject_id`),
  KEY `mail_template_scenario` (`scenario_id`),
  KEY `mail_code_unique` (`code`,`scenario_id`),
  CONSTRAINT `mail_template_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mail_template_subject_id` FOREIGN KEY (`subject_id`) REFERENCES `communication_themes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Шаблоны писем';

-- ----------------------------
-- Records of mail_template
-- ----------------------------

-- ----------------------------
-- Table structure for max_rate
-- ----------------------------
DROP TABLE IF EXISTS `max_rate`;
CREATE TABLE `max_rate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) DEFAULT NULL,
  `rate` float DEFAULT NULL,
  `learning_goal_id` int(11) DEFAULT NULL,
  `hero_behaviour_id` int(11) DEFAULT NULL,
  `performance_rule_category_id` varchar(10) DEFAULT NULL,
  `scenario_id` int(11) DEFAULT NULL,
  `import_id` varchar(14) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_max_rate_learning_goal_id` (`learning_goal_id`),
  KEY `fk_max_rate_hero_behaviour_id` (`hero_behaviour_id`),
  KEY `fk_max_rate_performance_rule_category_id` (`performance_rule_category_id`),
  KEY `fk_max_rate_scenario_id` (`scenario_id`),
  CONSTRAINT `fk_max_rate_hero_behaviour_id` FOREIGN KEY (`hero_behaviour_id`) REFERENCES `hero_behaviour` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_max_rate_learning_goal_id` FOREIGN KEY (`learning_goal_id`) REFERENCES `learning_goal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_max_rate_performance_rule_category_id` FOREIGN KEY (`performance_rule_category_id`) REFERENCES `activity_category` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_max_rate_scenario_id` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of max_rate
-- ----------------------------

-- ----------------------------
-- Table structure for meeting
-- ----------------------------
DROP TABLE IF EXISTS `meeting`;
CREATE TABLE `meeting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `popup_text` text,
  `duration` int(11) DEFAULT '0',
  `task_id` int(11) DEFAULT NULL,
  `import_id` varchar(14) NOT NULL,
  `scenario_id` int(11) NOT NULL,
  `icon_text` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_meeting_task_id` (`task_id`),
  CONSTRAINT `fk_meeting_task_id` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of meeting
-- ----------------------------

-- ----------------------------
-- Table structure for membership
-- ----------------------------
DROP TABLE IF EXISTS `membership`;
CREATE TABLE `membership` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `membership_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `order_date` int(11) NOT NULL,
  `end_date` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `zipcode` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `payment_date` int(11) DEFAULT NULL,
  `subscribed` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10000 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of membership
-- ----------------------------

-- ----------------------------
-- Table structure for message
-- ----------------------------
DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int(10) unsigned NOT NULL,
  `from_user_id` int(10) unsigned NOT NULL,
  `to_user_id` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text,
  `message_read` tinyint(1) NOT NULL,
  `answered` tinyint(1) DEFAULT NULL,
  `draft` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of message
-- ----------------------------

-- ----------------------------
-- Table structure for my_documents
-- ----------------------------
DROP TABLE IF EXISTS `my_documents`;
CREATE TABLE `my_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) DEFAULT NULL,
  `template_id` int(11) DEFAULT NULL,
  `fileName` varchar(128) DEFAULT NULL,
  `hidden` tinyint(1) DEFAULT '0',
  `uuid` varchar(255) NOT NULL,
  `is_was_saved` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_my_documents_sim_id` (`sim_id`),
  KEY `fk_my_documents_template_id` (`template_id`),
  CONSTRAINT `fk_my_documents_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_my_documents_template_id` FOREIGN KEY (`template_id`) REFERENCES `my_documents_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Мои документы';

-- ----------------------------
-- Records of my_documents
-- ----------------------------

-- ----------------------------
-- Table structure for my_documents_template
-- ----------------------------
DROP TABLE IF EXISTS `my_documents_template`;
CREATE TABLE `my_documents_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fileName` varchar(128) DEFAULT NULL,
  `hidden` tinyint(1) DEFAULT '0',
  `code` varchar(5) DEFAULT NULL,
  `srcFile` varchar(255) NOT NULL,
  `format` varchar(5) DEFAULT NULL,
  `type` varchar(5) DEFAULT NULL,
  `import_id` varchar(14) DEFAULT NULL COMMENT 'setvice value,used to remove old data after reimport.',
  `scenario_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `my_documents_template_scenario` (`scenario_id`),
  CONSTRAINT `my_documents_template_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Шаблон моих документов';

-- ----------------------------
-- Records of my_documents_template
-- ----------------------------

-- ----------------------------
-- Table structure for payment
-- ----------------------------
DROP TABLE IF EXISTS `payment`;
CREATE TABLE `payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `text` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of payment
-- ----------------------------

-- ----------------------------
-- Table structure for performance_aggregated
-- ----------------------------
DROP TABLE IF EXISTS `performance_aggregated`;
CREATE TABLE `performance_aggregated` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `category_id` varchar(10) DEFAULT NULL,
  `value` int(11) DEFAULT NULL,
  `percent` decimal(10,6) NOT NULL DEFAULT '0.000000',
  PRIMARY KEY (`id`),
  KEY `fk_performance_aggregated_sim_id` (`sim_id`),
  KEY `fk_performance_aggregated_category_id` (`category_id`),
  CONSTRAINT `fk_performance_aggregated_category_id` FOREIGN KEY (`category_id`) REFERENCES `activity_category` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_performance_aggregated_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of performance_aggregated
-- ----------------------------

-- ----------------------------
-- Table structure for performance_point
-- ----------------------------
DROP TABLE IF EXISTS `performance_point`;
CREATE TABLE `performance_point` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `performance_rule_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_performance_point_performance_rule_id` (`performance_rule_id`),
  KEY `fk_performance_point_sim_id` (`sim_id`),
  CONSTRAINT `fk_performance_point_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_performance_point_performance_rule_id` FOREIGN KEY (`performance_rule_id`) REFERENCES `performance_rule` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of performance_point
-- ----------------------------

-- ----------------------------
-- Table structure for performance_rule
-- ----------------------------
DROP TABLE IF EXISTS `performance_rule`;
CREATE TABLE `performance_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_id` int(11) NOT NULL,
  `operation` varchar(5) DEFAULT NULL,
  `value` int(11) DEFAULT NULL,
  `import_id` varchar(14) DEFAULT NULL,
  `scenario_id` int(11) NOT NULL,
  `code` int(11) NOT NULL,
  `category_id` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `performance_rule` (`code`,`scenario_id`),
  KEY `fk_performance_rule_activity_id` (`activity_id`),
  KEY `performance_rule_scenario` (`scenario_id`),
  KEY `fk_performance_rule_category_id` (`category_id`),
  CONSTRAINT `fk_performance_rule_category_id` FOREIGN KEY (`category_id`) REFERENCES `activity_category` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `performance_rule_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of performance_rule
-- ----------------------------

-- ----------------------------
-- Table structure for performance_rule_condition
-- ----------------------------
DROP TABLE IF EXISTS `performance_rule_condition`;
CREATE TABLE `performance_rule_condition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `performance_rule_id` int(11) NOT NULL,
  `replica_id` int(11) DEFAULT NULL,
  `mail_id` int(11) DEFAULT NULL,
  `import_id` varchar(14) DEFAULT NULL,
  `scenario_id` int(11) NOT NULL,
  `excel_formula_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_performance_rule_condition_performance_rule_id` (`performance_rule_id`),
  KEY `fk_performance_rule_condition_replica_id` (`replica_id`),
  KEY `fk_performance_rule_condition_mail_id` (`mail_id`),
  KEY `performance_rule_condition_scenario` (`scenario_id`),
  KEY `fk_performance_rule_condition_excel_formula_id` (`excel_formula_id`),
  CONSTRAINT `fk_performance_rule_condition_excel_formula_id` FOREIGN KEY (`excel_formula_id`) REFERENCES `excel_points_formula` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_performance_rule_condition_mail_id` FOREIGN KEY (`mail_id`) REFERENCES `mail_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_performance_rule_condition_performance_rule_id` FOREIGN KEY (`performance_rule_id`) REFERENCES `performance_rule` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_performance_rule_condition_replica_id` FOREIGN KEY (`replica_id`) REFERENCES `replica` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `performance_rule_condition_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of performance_rule_condition
-- ----------------------------

-- ----------------------------
-- Table structure for permission
-- ----------------------------
DROP TABLE IF EXISTS `permission`;
CREATE TABLE `permission` (
  `principal_id` int(11) NOT NULL,
  `subordinate_id` int(11) NOT NULL DEFAULT '0',
  `type` enum('user','role') NOT NULL,
  `action` int(11) NOT NULL,
  `template` tinyint(1) NOT NULL,
  `comment` text,
  PRIMARY KEY (`principal_id`,`subordinate_id`,`type`,`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of permission
-- ----------------------------

-- ----------------------------
-- Table structure for phone_calls
-- ----------------------------
DROP TABLE IF EXISTS `phone_calls`;
CREATE TABLE `phone_calls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL COMMENT 'идентификатор симуляции',
  `call_type` tinyint(1) DEFAULT '0',
  `from_id` int(11) DEFAULT NULL COMMENT 'Кто звонил',
  `to_id` int(11) DEFAULT NULL COMMENT 'Кому звонил',
  `call_time` time NOT NULL DEFAULT '00:00:00',
  `dialog_code` varchar(20) DEFAULT NULL,
  `theme_id` int(11) DEFAULT NULL,
  `is_displayed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_phone_calls_sim_id` (`sim_id`),
  KEY `fk_phone_calls_from_id` (`from_id`),
  KEY `fk_phone_calls_to_id` (`to_id`),
  KEY `phone_calls_theme_id` (`theme_id`),
  CONSTRAINT `fk_phone_calls_from_id` FOREIGN KEY (`from_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_phone_calls_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_phone_calls_to_id` FOREIGN KEY (`to_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `phone_calls_theme_id` FOREIGN KEY (`theme_id`) REFERENCES `communication_themes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='История звонков';

-- ----------------------------
-- Records of phone_calls
-- ----------------------------

-- ----------------------------
-- Table structure for positions
-- ----------------------------
DROP TABLE IF EXISTS `positions`;
CREATE TABLE `positions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(120) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of positions
-- ----------------------------
INSERT INTO `positions` VALUES ('1', 'Собственник');
INSERT INTO `positions` VALUES ('2', 'Высшее руководство');
INSERT INTO `positions` VALUES ('3', 'Функциональный руководитель');
INSERT INTO `positions` VALUES ('4', 'Проектный менеджер');
INSERT INTO `positions` VALUES ('5', 'Функциональный специалист');
INSERT INTO `positions` VALUES ('6', 'Руководитель HR');
INSERT INTO `positions` VALUES ('7', 'Специалист HR');

-- ----------------------------
-- Table structure for position_level
-- ----------------------------
DROP TABLE IF EXISTS `position_level`;
CREATE TABLE `position_level` (
  `slug` varchar(50) NOT NULL,
  `label` varchar(120) NOT NULL,
  PRIMARY KEY (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of position_level
-- ----------------------------
INSERT INTO `position_level` VALUES ('manager', 'Руководитель');
INSERT INTO `position_level` VALUES ('specialist', 'Специалист');

-- ----------------------------
-- Table structure for privacysetting
-- ----------------------------
DROP TABLE IF EXISTS `privacysetting`;
CREATE TABLE `privacysetting` (
  `user_id` int(10) unsigned NOT NULL,
  `message_new_friendship` tinyint(1) NOT NULL DEFAULT '1',
  `message_new_message` tinyint(1) NOT NULL DEFAULT '1',
  `message_new_profilecomment` tinyint(1) NOT NULL DEFAULT '1',
  `appear_in_search` tinyint(1) NOT NULL DEFAULT '1',
  `show_online_status` tinyint(1) NOT NULL DEFAULT '1',
  `log_profile_visits` tinyint(1) NOT NULL DEFAULT '1',
  `ignore_users` varchar(255) DEFAULT NULL,
  `public_profile_fields` bigint(15) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of privacysetting
-- ----------------------------

-- ----------------------------
-- Table structure for professional_occupation
-- ----------------------------
DROP TABLE IF EXISTS `professional_occupation`;
CREATE TABLE `professional_occupation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(120) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of professional_occupation
-- ----------------------------
INSERT INTO `professional_occupation` VALUES ('1', 'Автомобильный бизнес');
INSERT INTO `professional_occupation` VALUES ('2', 'Агропромышленный комплекс');
INSERT INTO `professional_occupation` VALUES ('3', 'Финансовые услуги');
INSERT INTO `professional_occupation` VALUES ('4', 'Информационные технологии, интернет, телеком');
INSERT INTO `professional_occupation` VALUES ('5', 'Добыча и металлургия');
INSERT INTO `professional_occupation` VALUES ('6', 'Лесная и деревообрабатывающая промышленность');
INSERT INTO `professional_occupation` VALUES ('7', 'Машиностроение');
INSERT INTO `professional_occupation` VALUES ('8', 'Медицина и фармацевтика');
INSERT INTO `professional_occupation` VALUES ('9', 'Недвижимость, девелопмент, строительство');
INSERT INTO `professional_occupation` VALUES ('10', 'Оборудование, приборостроение, электротехника');
INSERT INTO `professional_occupation` VALUES ('11', 'Строительные материалы и оборудование');
INSERT INTO `professional_occupation` VALUES ('12', 'Производство товаров массового спроса');
INSERT INTO `professional_occupation` VALUES ('13', 'Розничная торговля');
INSERT INTO `professional_occupation` VALUES ('14', 'Топливно-энергетический комплекс');
INSERT INTO `professional_occupation` VALUES ('15', 'Транспорт и логистика');
INSERT INTO `professional_occupation` VALUES ('16', 'Туризм, отдых, гостеприимство');
INSERT INTO `professional_occupation` VALUES ('17', 'Химическая промышленность');
INSERT INTO `professional_occupation` VALUES ('18', 'Профессиональные услуги');
INSERT INTO `professional_occupation` VALUES ('19', 'Услуги населению');
INSERT INTO `professional_occupation` VALUES ('20', 'Наука, образование');
INSERT INTO `professional_occupation` VALUES ('21', 'Искусство, развлечения, масс-медиа');
INSERT INTO `professional_occupation` VALUES ('22', 'Государственная служба, некоммерческие организации');
INSERT INTO `professional_occupation` VALUES ('23', 'Авиация и космос');
INSERT INTO `professional_occupation` VALUES ('24', 'Управляющие компании и холдинги');
INSERT INTO `professional_occupation` VALUES ('25', 'Другая');

-- ----------------------------
-- Table structure for professional_specialization
-- ----------------------------
DROP TABLE IF EXISTS `professional_specialization`;
CREATE TABLE `professional_specialization` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(120) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of professional_specialization
-- ----------------------------
INSERT INTO `professional_specialization` VALUES ('1', 'Развитие бизнеса');
INSERT INTO `professional_specialization` VALUES ('2', 'Управление продуктами');
INSERT INTO `professional_specialization` VALUES ('3', 'PR, Маркетинг, продвижение');
INSERT INTO `professional_specialization` VALUES ('4', 'Исследования, разработки');
INSERT INTO `professional_specialization` VALUES ('5', 'Аналитика');
INSERT INTO `professional_specialization` VALUES ('6', 'Продажи');
INSERT INTO `professional_specialization` VALUES ('7', 'Колл-центр');
INSERT INTO `professional_specialization` VALUES ('8', 'Производство');
INSERT INTO `professional_specialization` VALUES ('9', 'Оказание услуг');
INSERT INTO `professional_specialization` VALUES ('10', 'Cоздание контента');
INSERT INTO `professional_specialization` VALUES ('11', 'Редактура, художественное оформление');
INSERT INTO `professional_specialization` VALUES ('12', 'Операционная деятельность');
INSERT INTO `professional_specialization` VALUES ('13', 'Постпродажное обслуживание');
INSERT INTO `professional_specialization` VALUES ('14', 'Трейдинг');
INSERT INTO `professional_specialization` VALUES ('15', 'Транспортировка и хранение');
INSERT INTO `professional_specialization` VALUES ('16', 'Закупки');
INSERT INTO `professional_specialization` VALUES ('17', 'Информационные технологии');
INSERT INTO `professional_specialization` VALUES ('18', 'Управление персоналом');
INSERT INTO `professional_specialization` VALUES ('19', 'Управление рисками');
INSERT INTO `professional_specialization` VALUES ('20', 'Бюджетирование и планирование');
INSERT INTO `professional_specialization` VALUES ('21', 'Финансовый менеджмент');
INSERT INTO `professional_specialization` VALUES ('22', 'Учёт, налоги, отчетность');
INSERT INTO `professional_specialization` VALUES ('23', 'Мониторинг и контроль');
INSERT INTO `professional_specialization` VALUES ('24', 'Юридическая поддержка');
INSERT INTO `professional_specialization` VALUES ('25', 'Безопасность');
INSERT INTO `professional_specialization` VALUES ('26', 'Методология, оптимизация');
INSERT INTO `professional_specialization` VALUES ('27', 'Административно-хозяйственная деятельность');
INSERT INTO `professional_specialization` VALUES ('28', 'Документооборот');
INSERT INTO `professional_specialization` VALUES ('29', 'Управление проектами');
INSERT INTO `professional_specialization` VALUES ('30', 'Прочее');

-- ----------------------------
-- Table structure for professional_statuses
-- ----------------------------
DROP TABLE IF EXISTS `professional_statuses`;
CREATE TABLE `professional_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `position_I_id_language` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of professional_statuses
-- ----------------------------
INSERT INTO `professional_statuses` VALUES ('3', 'Собственник');
INSERT INTO `professional_statuses` VALUES ('4', 'Высшее руководство');
INSERT INTO `professional_statuses` VALUES ('5', 'Функциональный менеджер');
INSERT INTO `professional_statuses` VALUES ('6', 'Проектный менеджер');
INSERT INTO `professional_statuses` VALUES ('7', 'Специалист');
INSERT INTO `professional_statuses` VALUES ('9', 'Индивидуальная деятельность');
INSERT INTO `professional_statuses` VALUES ('10', 'Студент');

-- ----------------------------
-- Table structure for profile
-- ----------------------------
DROP TABLE IF EXISTS `profile`;
CREATE TABLE `profile` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `timestamp` int(20) DEFAULT NULL,
  `privacy` enum('protected','private','public') NOT NULL,
  `lastname` varchar(50) NOT NULL DEFAULT '',
  `firstname` varchar(50) NOT NULL DEFAULT '',
  `show_friends` tinyint(1) DEFAULT '1',
  `allow_comments` tinyint(1) DEFAULT '1',
  `email` varchar(255) NOT NULL DEFAULT '',
  `street` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `about` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of profile
-- ----------------------------

-- ----------------------------
-- Table structure for profile_comment
-- ----------------------------
DROP TABLE IF EXISTS `profile_comment`;
CREATE TABLE `profile_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `createtime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of profile_comment
-- ----------------------------

-- ----------------------------
-- Table structure for profile_field
-- ----------------------------
DROP TABLE IF EXISTS `profile_field`;
CREATE TABLE `profile_field` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `varname` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `hint` text NOT NULL,
  `field_type` varchar(50) NOT NULL DEFAULT '',
  `field_size` int(3) NOT NULL DEFAULT '0',
  `field_size_min` int(3) NOT NULL DEFAULT '0',
  `required` int(1) NOT NULL DEFAULT '0',
  `match` varchar(255) NOT NULL DEFAULT '',
  `range` varchar(255) NOT NULL DEFAULT '',
  `error_message` varchar(255) NOT NULL DEFAULT '',
  `other_validator` varchar(255) NOT NULL DEFAULT '',
  `default` varchar(255) NOT NULL DEFAULT '',
  `position` int(3) NOT NULL DEFAULT '0',
  `visible` int(1) NOT NULL DEFAULT '0',
  `related_field_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `varname` (`varname`,`visible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of profile_field
-- ----------------------------

-- ----------------------------
-- Table structure for profile_visit
-- ----------------------------
DROP TABLE IF EXISTS `profile_visit`;
CREATE TABLE `profile_visit` (
  `visitor_id` int(11) NOT NULL,
  `visited_id` int(11) NOT NULL,
  `timestamp_first_visit` int(11) NOT NULL,
  `timestamp_last_visit` int(11) NOT NULL,
  `num_of_visits` int(11) NOT NULL,
  PRIMARY KEY (`visitor_id`,`visited_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of profile_visit
-- ----------------------------

-- ----------------------------
-- Table structure for replica
-- ----------------------------
DROP TABLE IF EXISTS `replica`;
CREATE TABLE `replica` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ch_from` int(11) NOT NULL COMMENT 'персонаж от которого должен исходить текст',
  `ch_to` int(11) DEFAULT NULL COMMENT 'персонаж которому должен исходить текст',
  `dialog_subtype` int(11) NOT NULL COMMENT 'Подтип диалога',
  `text` text NOT NULL,
  `event_result` int(11) NOT NULL COMMENT 'результат диалога, который вернется событию',
  `code` varchar(10) NOT NULL,
  `step_number` int(11) NOT NULL,
  `replica_number` int(11) NOT NULL,
  `next_event` int(11) DEFAULT NULL,
  `delay` int(11) NOT NULL DEFAULT '0',
  `is_final_replica` tinyint(1) NOT NULL,
  `sound` varchar(200) DEFAULT NULL,
  `excel_id` int(11) DEFAULT NULL,
  `next_event_code` varchar(10) DEFAULT NULL,
  `flag_to_switch` varchar(5) DEFAULT NULL,
  `flag_to_switch_2` varchar(5) DEFAULT NULL,
  `demo` tinyint(1) DEFAULT '0',
  `type_of_init` varchar(32) DEFAULT NULL COMMENT 'Replica initialization type: dialog, icon, time, flex etc.',
  `import_id` varchar(14) NOT NULL DEFAULT '00000000000000' COMMENT 'setvice value,used to remove old data after reimport.',
  `fantastic_result` tinyint(1) NOT NULL DEFAULT '0',
  `scenario_id` int(11) NOT NULL,
  `duration` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_dialogs_branch_id` (`step_number`),
  KEY `fk_dialogs_ch_from` (`ch_from`),
  KEY `fk_dialogs_ch_to` (`ch_to`),
  KEY `fk_dialogs_dialog_subtype` (`dialog_subtype`),
  KEY `fk_dialogs_event_result` (`event_result`),
  KEY `fk_dialogs_next_branch` (`replica_number`),
  KEY `fk_dialogs_next_event` (`next_event`),
  KEY `replica_scenario` (`scenario_id`),
  CONSTRAINT `fk_dialogs_ch_from` FOREIGN KEY (`ch_from`) REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_dialogs_ch_to` FOREIGN KEY (`ch_to`) REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_dialogs_dialog_subtype` FOREIGN KEY (`dialog_subtype`) REFERENCES `dialog_subtypes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_dialogs_event_result` FOREIGN KEY (`event_result`) REFERENCES `events_results` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_dialogs_next_event` FOREIGN KEY (`next_event`) REFERENCES `event_sample` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `replica_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of replica
-- ----------------------------

-- ----------------------------
-- Table structure for robokassa_transaction
-- ----------------------------
DROP TABLE IF EXISTS `robokassa_transaction`;
CREATE TABLE `robokassa_transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `request_body` text,
  `description` varchar(100) DEFAULT NULL,
  `amount` decimal(10,4) DEFAULT NULL,
  `invoice_id` int(11) NOT NULL,
  `request` varchar(15) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `displayed_at` datetime DEFAULT NULL,
  `widget_body` text,
  `processed_at` datetime DEFAULT NULL,
  `response_body` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of robokassa_transaction
-- ----------------------------

-- ----------------------------
-- Table structure for role
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `membership_priority` int(11) DEFAULT NULL,
  `price` double DEFAULT NULL COMMENT 'Price (when using membership module)',
  `duration` int(11) DEFAULT NULL COMMENT 'How long a membership is valid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of role
-- ----------------------------

-- ----------------------------
-- Table structure for scenario
-- ----------------------------
DROP TABLE IF EXISTS `scenario`;
CREATE TABLE `scenario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `finish_time` time DEFAULT NULL,
  `duration_in_game_min` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `scenario_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of scenario
-- ----------------------------

-- ----------------------------
-- Table structure for scenario_config
-- ----------------------------
DROP TABLE IF EXISTS `scenario_config`;
CREATE TABLE `scenario_config` (
  `scenario_id` int(11) NOT NULL AUTO_INCREMENT,
  `import_id` varchar(14) DEFAULT NULL COMMENT 'setvice value,used to remove old data after reimport.',
  `game_start_timestamp` varchar(250) NOT NULL,
  `game_end_workday_timestamp` varchar(250) NOT NULL,
  `game_end_timestamp` varchar(250) NOT NULL,
  `game_help_folder_name` varchar(250) NOT NULL,
  `game_help_background_jst` varchar(250) NOT NULL,
  `game_help_pages` text NOT NULL,
  `inbox_folder_icons` text NOT NULL,
  `draft_folder_icons` text NOT NULL,
  `outbox_folder_icon` text NOT NULL,
  `trash_folder_icons` text NOT NULL,
  `read_email_screen_icons` text NOT NULL,
  `write_new_email_screen_icons` text NOT NULL,
  `edit_draft_email_screen_icons` text NOT NULL,
  `game_date` varchar(250) NOT NULL,
  `intro_video_path` varchar(250) NOT NULL,
  `docs_to_save` varchar(250) NOT NULL,
  `is_calculate_assessment` varchar(250) NOT NULL,
  `is_display_assessment_result_po_up` varchar(250) NOT NULL,
  `is_allow_override` varchar(250) NOT NULL,
  `scenario_label_text` varchar(120) DEFAULT NULL,
  `scenario_label_image` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`scenario_id`),
  CONSTRAINT `fk_scenario_config_scenario_id` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of scenario_config
-- ----------------------------

-- ----------------------------
-- Table structure for simulations
-- ----------------------------
DROP TABLE IF EXISTS `simulations`;
CREATE TABLE `simulations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  `mode` tinyint(1) DEFAULT NULL,
  `paused` datetime DEFAULT NULL,
  `skipped` int(11) DEFAULT '0',
  `scenario_id` int(11) NOT NULL,
  `results_popup_partials_path` text,
  `results_popup_cache` blob,
  `is_emergency_panel_allowed` tinyint(1) DEFAULT '0',
  `user_agent` text,
  `screen_resolution` varchar(20) DEFAULT NULL,
  `window_resolution` varchar(20) DEFAULT NULL,
  `ipv4` varchar(20) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_simulations_user_id` (`user_id`),
  KEY `simulations_scenario` (`scenario_id`),
  CONSTRAINT `fk_simulations_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `simulations_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of simulations
-- ----------------------------

-- ----------------------------
-- Table structure for simulations_excel_points
-- ----------------------------
DROP TABLE IF EXISTS `simulations_excel_points`;
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

-- ----------------------------
-- Records of simulations_excel_points
-- ----------------------------

-- ----------------------------
-- Table structure for simulation_completed_parent
-- ----------------------------
DROP TABLE IF EXISTS `simulation_completed_parent`;
CREATE TABLE `simulation_completed_parent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `parent_code` varchar(10) NOT NULL,
  `end_time` time DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `completed_parent` (`parent_code`,`sim_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of simulation_completed_parent
-- ----------------------------

-- ----------------------------
-- Table structure for simulation_flags
-- ----------------------------
DROP TABLE IF EXISTS `simulation_flags`;
CREATE TABLE `simulation_flags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) DEFAULT NULL,
  `flag` varchar(5) DEFAULT NULL COMMENT 'название флага',
  `value` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_simulation_flags_sim_id` (`sim_id`),
  CONSTRAINT `fk_simulation_flags_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='флаги симуляции';

-- ----------------------------
-- Records of simulation_flags
-- ----------------------------

-- ----------------------------
-- Table structure for simulation_flag_queue
-- ----------------------------
DROP TABLE IF EXISTS `simulation_flag_queue`;
CREATE TABLE `simulation_flag_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `flag_code` varchar(10) NOT NULL,
  `delay` int(3) DEFAULT '0',
  `switch_time` time DEFAULT NULL,
  `is_processed` int(3) DEFAULT '0',
  `value` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_simulation_flag_queue_sim_id` (`sim_id`),
  KEY `fk_simulation_flag_queue_flag_code` (`flag_code`),
  CONSTRAINT `fk_simulation_flag_queue_flag_code` FOREIGN KEY (`flag_code`) REFERENCES `flag` (`code`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_simulation_flag_queue_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of simulation_flag_queue
-- ----------------------------

-- ----------------------------
-- Table structure for simulation_learning_area
-- ----------------------------
DROP TABLE IF EXISTS `simulation_learning_area`;
CREATE TABLE `simulation_learning_area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `learning_area_id` int(11) NOT NULL,
  `value` float(10,6) DEFAULT NULL,
  `sim_id` int(11) NOT NULL,
  `score` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `simulation_learning_area_learning_area_id` (`learning_area_id`),
  KEY `simulation_learning_area_sim_id` (`sim_id`),
  CONSTRAINT `simulation_learning_area_learning_area_id` FOREIGN KEY (`learning_area_id`) REFERENCES `learning_area` (`id`),
  CONSTRAINT `simulation_learning_area_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of simulation_learning_area
-- ----------------------------

-- ----------------------------
-- Table structure for simulation_learning_goal
-- ----------------------------
DROP TABLE IF EXISTS `simulation_learning_goal`;
CREATE TABLE `simulation_learning_goal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `learning_goal_id` int(11) NOT NULL,
  `value` decimal(10,2) DEFAULT NULL,
  `percent` decimal(10,2) DEFAULT NULL,
  `problem` decimal(10,2) DEFAULT NULL,
  `total_positive` decimal(10,2) DEFAULT NULL,
  `total_negative` decimal(10,2) DEFAULT NULL,
  `max_positive` decimal(10,2) DEFAULT NULL,
  `max_negative` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_simulation_learning_goal_sim_id` (`sim_id`),
  KEY `fk_simulation_learning_goal_learning_goal_id` (`learning_goal_id`),
  CONSTRAINT `fk_simulation_learning_goal_learning_goal_id` FOREIGN KEY (`learning_goal_id`) REFERENCES `learning_goal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_simulation_learning_goal_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of simulation_learning_goal
-- ----------------------------

-- ----------------------------
-- Table structure for simulation_learning_goal_group
-- ----------------------------
DROP TABLE IF EXISTS `simulation_learning_goal_group`;
CREATE TABLE `simulation_learning_goal_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `learning_goal_group_id` int(11) NOT NULL,
  `value` decimal(10,2) DEFAULT NULL,
  `percent` decimal(10,2) DEFAULT NULL,
  `problem` decimal(10,2) DEFAULT NULL,
  `total_positive` decimal(10,2) DEFAULT NULL,
  `total_negative` decimal(10,2) DEFAULT NULL,
  `max_positive` decimal(10,2) DEFAULT NULL,
  `max_negative` decimal(10,2) DEFAULT NULL,
  `coefficient` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_simulation_learning_goal_group_sim_id` (`sim_id`),
  KEY `fk_simulation_learning_goal_group_learning_goal_group_id` (`learning_goal_group_id`),
  CONSTRAINT `fk_simulation_learning_goal_group_learning_goal_group_id` FOREIGN KEY (`learning_goal_group_id`) REFERENCES `learning_goal_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_simulation_learning_goal_group_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of simulation_learning_goal_group
-- ----------------------------

-- ----------------------------
-- Table structure for stress_point
-- ----------------------------
DROP TABLE IF EXISTS `stress_point`;
CREATE TABLE `stress_point` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `stress_rule_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_stress_point_sim_id` (`sim_id`),
  KEY `fk_stress_point_stress_rule_id` (`stress_rule_id`),
  CONSTRAINT `fk_stress_point_stress_rule_id` FOREIGN KEY (`stress_rule_id`) REFERENCES `stress_rule` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_stress_point_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of stress_point
-- ----------------------------

-- ----------------------------
-- Table structure for stress_rule
-- ----------------------------
DROP TABLE IF EXISTS `stress_rule`;
CREATE TABLE `stress_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `replica_id` int(11) DEFAULT NULL,
  `mail_id` int(11) DEFAULT NULL,
  `value` int(11) DEFAULT NULL,
  `import_id` varchar(14) DEFAULT NULL,
  `code` int(11) NOT NULL,
  `scenario_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stress_rule_uniq` (`code`,`scenario_id`),
  KEY `fk_stress_rule_replica_id` (`replica_id`),
  KEY `fk_stress_rule_mail_id` (`mail_id`),
  KEY `fk_stress_rule_scenario_id` (`scenario_id`),
  CONSTRAINT `fk_stress_rule_scenario_id` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_stress_rule_mail_id` FOREIGN KEY (`mail_id`) REFERENCES `mail_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_stress_rule_replica_id` FOREIGN KEY (`replica_id`) REFERENCES `replica` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of stress_rule
-- ----------------------------

-- ----------------------------
-- Table structure for tariff
-- ----------------------------
DROP TABLE IF EXISTS `tariff`;
CREATE TABLE `tariff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(120) NOT NULL,
  `is_free` tinyint(1) DEFAULT '0',
  `price` decimal(10,2) NOT NULL,
  `safe_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `simulations_amount` int(11) DEFAULT '0',
  `description` text,
  `benefits` text,
  `order` int(11) DEFAULT NULL,
  `slug` varchar(20) NOT NULL,
  `price_usd` decimal(10,2) NOT NULL,
  `safe_amount_usd` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tariff
-- ----------------------------
INSERT INTO `tariff` VALUES ('1', 'Lite', '0', '3990.00', '0.00', '10', null, 'Free updates', '1', 'lite', '133.00', '0.00');
INSERT INTO `tariff` VALUES ('2', 'Starter', '0', '6980.00', '1000.00', '20', null, 'Free updates', '2', 'starter', '233.00', '33.00');
INSERT INTO `tariff` VALUES ('3', 'Professional', '0', '14950.00', '5000.00', '50', null, 'Free updates', '3', 'professional', '498.00', '167.00');
INSERT INTO `tariff` VALUES ('4', 'Business', '0', '49800.00', '30000.00', '200', null, 'Free updates', '4', 'business', '1660.00', '1000.00');

-- ----------------------------
-- Table structure for tasks
-- ----------------------------
DROP TABLE IF EXISTS `tasks`;
CREATE TABLE `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `start_time` time DEFAULT NULL,
  `duration` int(11) NOT NULL,
  `sim_id` int(11) DEFAULT NULL,
  `code` varchar(5) DEFAULT NULL,
  `start_type` varchar(5) DEFAULT NULL,
  `category` tinyint(1) DEFAULT NULL,
  `import_id` varchar(14) NOT NULL COMMENT 'setvice value,used to remove old data after reimport.',
  `time_limit_type` varchar(30) DEFAULT NULL,
  `fixed_day` varchar(30) DEFAULT NULL,
  `is_cant_be_moved` tinyint(1) DEFAULT '0',
  `scenario_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tasks_sim_id` (`sim_id`),
  KEY `tasks_scenario` (`scenario_id`),
  CONSTRAINT `tasks_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tasks_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tasks
-- ----------------------------

-- ----------------------------
-- Table structure for tbl_migration
-- ----------------------------
DROP TABLE IF EXISTS `tbl_migration`;
CREATE TABLE `tbl_migration` (
  `version` varchar(255) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_migration
-- ----------------------------
INSERT INTO `tbl_migration` VALUES ('m130801_120340_remove_aggregated_reduction', '1382012031');
INSERT INTO `tbl_migration` VALUES ('m130802_122516_day_plan_refactor', '1382012031');
INSERT INTO `tbl_migration` VALUES ('m130805_160507_drop_day_plan_tables', '1382012032');
INSERT INTO `tbl_migration` VALUES ('m130814_100814_scenario', '1382012033');
INSERT INTO `tbl_migration` VALUES ('m130815_123110_universal_log', '1382012033');
INSERT INTO `tbl_migration` VALUES ('m130815_130449_meeting', '1382012034');
INSERT INTO `tbl_migration` VALUES ('m130815_153151_clear', '1382012035');
INSERT INTO `tbl_migration` VALUES ('m130823_130957_duration', '1382012035');
INSERT INTO `tbl_migration` VALUES ('m130829_120205_config', '1382012035');
INSERT INTO `tbl_migration` VALUES ('m130829_143002_index', '1382012035');
INSERT INTO `tbl_migration` VALUES ('m130829_171024_add_pk_for_scenario_config', '1382012036');
INSERT INTO `tbl_migration` VALUES ('m130829_180447_add_mail_prefix', '1382012036');
INSERT INTO `tbl_migration` VALUES ('m130830_102243_set_user_default_message', '1382012036');
INSERT INTO `tbl_migration` VALUES ('m130830_114500_update_corparate_user_default_message', '1382012036');
INSERT INTO `tbl_migration` VALUES ('m130830_133633_pk', '1382012036');
INSERT INTO `tbl_migration` VALUES ('m130902_140455_add_new_scenario_configs', '1382012037');
INSERT INTO `tbl_migration` VALUES ('m130902_145611_sim_status', '1382012037');
INSERT INTO `tbl_migration` VALUES ('m130903_152602_add_keep_last_category_after_60_sec_field', '1382012037');
INSERT INTO `tbl_migration` VALUES ('m130903_210752_update_profile', '1382012039');
INSERT INTO `tbl_migration` VALUES ('m130905_093156_parent_must', '1382012039');
INSERT INTO `tbl_migration` VALUES ('m130906_084619_debug', '1382012039');
INSERT INTO `tbl_migration` VALUES ('m130908_190533_add_import_log', '1382012040');
INSERT INTO `tbl_migration` VALUES ('m130911_140942_deleting_one_month_free', '1382012040');
INSERT INTO `tbl_migration` VALUES ('m130912_082855_payment_system_migration', '1382012048');
INSERT INTO `tbl_migration` VALUES ('m130912_104353_add_yandex_com', '1382012048');
INSERT INTO `tbl_migration` VALUES ('m130919_141851_addMonthToInvoice', '1382012048');
INSERT INTO `tbl_migration` VALUES ('m130923_090135_payment_log', '1382012049');
INSERT INTO `tbl_migration` VALUES ('m130923_091155_payment_create_date', '1382012049');
INSERT INTO `tbl_migration` VALUES ('m130923_092936_payment_paid_date_changes', '1382012050');
INSERT INTO `tbl_migration` VALUES ('m130924_102943_disabling', '1382012050');
INSERT INTO `tbl_migration` VALUES ('m130924_112214_add_refer_invites_to_corporate_account', '1382012051');
INSERT INTO `tbl_migration` VALUES ('m130925_085824_width', '1382012052');
INSERT INTO `tbl_migration` VALUES ('m130925_115101_emails_queue', '1382012052');
INSERT INTO `tbl_migration` VALUES ('m130926_071243_decimal', '1382012052');
INSERT INTO `tbl_migration` VALUES ('m130926_133427_user', '1382012052');
INSERT INTO `tbl_migration` VALUES ('m130926_134059_sim', '1382012053');
INSERT INTO `tbl_migration` VALUES ('m130927_075445_add_column_is_diplay_popup_in_corporate', '1382012054');
INSERT INTO `tbl_migration` VALUES ('m130927_103836_add_column_refferals_invite_in_invite_log', '1382012054');
INSERT INTO `tbl_migration` VALUES ('m130930_055500_renaming_refferal_table', '1382012055');

-- ----------------------------
-- Table structure for time_management_aggregated
-- ----------------------------
DROP TABLE IF EXISTS `time_management_aggregated`;
CREATE TABLE `time_management_aggregated` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `slug` varchar(60) NOT NULL,
  `value` decimal(6,2) NOT NULL,
  `unit_label` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of time_management_aggregated
-- ----------------------------

-- ----------------------------
-- Table structure for time_management_aggregated_debug
-- ----------------------------
DROP TABLE IF EXISTS `time_management_aggregated_debug`;
CREATE TABLE `time_management_aggregated_debug` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) NOT NULL,
  `slug` varchar(60) NOT NULL,
  `value` decimal(6,2) NOT NULL,
  `unit_label` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of time_management_aggregated_debug
-- ----------------------------

-- ----------------------------
-- Table structure for translation
-- ----------------------------
DROP TABLE IF EXISTS `translation`;
CREATE TABLE `translation` (
  `message` varbinary(255) NOT NULL,
  `translation` varchar(255) NOT NULL,
  `language` varchar(5) NOT NULL,
  `category` varchar(255) NOT NULL,
  PRIMARY KEY (`message`,`language`,`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of translation
-- ----------------------------
INSERT INTO `translation` VALUES (0x41626F7574, 'Über', 'de', 'yum');
INSERT INTO `translation` VALUES (0x41626F7574, 'Acerca', 'es', 'yum');
INSERT INTO `translation` VALUES (0x41626F7574, 'me concernant ??', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x41626F7574, 'Info', 'it', 'yum');
INSERT INTO `translation` VALUES (0x41626F7574, 'Info', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x41636365737320636F6E74726F6C, 'Zugangskontrolle', 'de', 'yum');
INSERT INTO `translation` VALUES (0x41636365737320636F6E74726F6C, 'Control de acceso', 'es', 'yum');
INSERT INTO `translation` VALUES (0x41636365737320636F6E74726F6C, 'Controle d acces', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x41636365737320636F6E74726F6C, 'Controllo accesso', 'it', 'yum');
INSERT INTO `translation` VALUES (0x416374696F6E, 'Aktion', 'de', 'yum');
INSERT INTO `translation` VALUES (0x416374696F6E, 'Acción', 'es', 'yum');
INSERT INTO `translation` VALUES (0x416374696F6E, 'Action', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x416374696F6E, 'Azione', 'it', 'yum');
INSERT INTO `translation` VALUES (0x416374696F6E73, 'Aktionen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x416374696F6E73, 'Acciones', 'es', 'yum');
INSERT INTO `translation` VALUES (0x416374696F6E73, 'Actions', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x416374696F6E73, 'Azioni', 'it', 'yum');
INSERT INTO `translation` VALUES (0x416374697661746564, 'erstmalig Aktiviert', 'de', 'yum');
INSERT INTO `translation` VALUES (0x416374697661746564, 'Activado', 'es', 'yum');
INSERT INTO `translation` VALUES (0x416374697661746564, 'Premiere activation de votre compte', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x416374697661746564, 'Attivato', 'it', 'yum');
INSERT INTO `translation` VALUES (0x416374697665, 'Aktiv', 'de', 'yum');
INSERT INTO `translation` VALUES (0x416374697665, 'Activo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x416374697665, 'Actif', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x416374697665, 'Attiv', 'it', 'yum');
INSERT INTO `translation` VALUES (0x416374697665, 'Aktiv', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x416374697665, 'Активирован', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x416374697665202D204669727374207669736974, 'Aktiv - erster Besuch', 'de', 'yum');
INSERT INTO `translation` VALUES (0x416374697665202D204669727374207669736974, 'Activo - Primera visita', 'es', 'yum');
INSERT INTO `translation` VALUES (0x416374697665202D204669727374207669736974, 'Actif - premiere visite', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x416374697665202D204669727374207669736974, 'Attivo - Priva visita', 'it', 'yum');
INSERT INTO `translation` VALUES (0x416374697665207573657273, 'Aktive Benutzer', 'de', 'yum');
INSERT INTO `translation` VALUES (0x416374697665207573657273, 'Usuarios activos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x416374697665207573657273, 'Utiliateurs actifs', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x416374697665207573657273, 'Utenti attivi', 'it', 'yum');
INSERT INTO `translation` VALUES (0x416374697665207573657273, 'Aktywni uzytkownicy', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x41637469766974696573, 'Aktivitäten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x41637469766974696573, 'Actividades', 'es', 'yum');
INSERT INTO `translation` VALUES (0x41637469766974696573, 'Activites', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x41637469766974696573, 'Attivita', 'it', 'yum');
INSERT INTO `translation` VALUES (0x416464206173206120667269656E64, 'Zur Kontaktliste hinzufügen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x416464206173206120667269656E64, 'Agregar como amigo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x416464206173206120667269656E64, 'Ajouter a ma liste de contact', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x416464206173206120667269656E64, 'Aggiungi un contatto', 'it', 'yum');
INSERT INTO `translation` VALUES (0x41646D696E20696E626F78, 'Administratorposteingang', 'de', 'yum');
INSERT INTO `translation` VALUES (0x41646D696E20696E626F78, 'Bandeja de entrada de Admin', 'es', 'yum');
INSERT INTO `translation` VALUES (0x41646D696E20696E626F78, 'Boite e-mail de l administrateur', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x41646D696E20696E626F78, 'Admin - Posta in arrivo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x41646D696E20696E626F78, 'Zarzadzaj skrzynka odbiorcza', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x41646D696E2073656E74206D65737361676573, 'Gesendete Nachrichten des Administrators', 'de', 'yum');
INSERT INTO `translation` VALUES (0x41646D696E2073656E74206D65737361676573, 'Mensajes enviados de Admin', 'es', 'yum');
INSERT INTO `translation` VALUES (0x41646D696E2073656E74206D65737361676573, 'E-mail envoye par l administrateur', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x41646D696E2073656E74206D65737361676573, 'Admin - Messaggi inviati', 'it', 'yum');
INSERT INTO `translation` VALUES (0x41646D696E2073656E74206D65737361676573, 'Wiadomosci wyslane przez administratora', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x41646D696E207573657273, 'Administratoren', 'de', 'yum');
INSERT INTO `translation` VALUES (0x41646D696E207573657273, 'Usuarios administradores', 'es', 'yum');
INSERT INTO `translation` VALUES (0x41646D696E207573657273, 'Administrateur', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x41646D696E207573657273, 'Utenti admin', 'it', 'yum');
INSERT INTO `translation` VALUES (0x41646D696E207573657273, 'Administratorzy', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x41646D696E2075736572732063616E206E6F742062652064656C6574656421, 'Administratoren können nicht gelöscht werden', 'de', 'yum');
INSERT INTO `translation` VALUES (0x41646D696E2075736572732063616E206E6F742062652064656C6574656421, '¡No se pueden eliminar los usuarios Administradores!', 'es', 'yum');
INSERT INTO `translation` VALUES (0x41646D696E2075736572732063616E206E6F742062652064656C6574656421, 'UN compte administrateur ne peut pas etre supprime', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x41646D696E2075736572732063616E206E6F742062652064656C6574656421, 'Utente admin non cancellabile!', 'it', 'yum');
INSERT INTO `translation` VALUES (0x41646D696E2075736572732063616E206E6F742062652064656C6574656421, 'Nie mozna usunac konta administratora', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x416C6C, 'Alle', 'de', 'yum');
INSERT INTO `translation` VALUES (0x416C6C, 'Todos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x416C6C, 'Ade tous', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x416C6C, 'Tutto', 'it', 'yum');
INSERT INTO `translation` VALUES (0x416C6C6F772070726F66696C6520636F6D6D656E7473, 'Profilkommentare erlauben', 'de', 'yum');
INSERT INTO `translation` VALUES (0x416C6C6F772070726F66696C6520636F6D6D656E7473, 'Permitir comentarios en perfiles', 'es', 'yum');
INSERT INTO `translation` VALUES (0x416C6C6F772070726F66696C6520636F6D6D656E7473, 'Autoriser les commentaires de profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x416C6C6F772070726F66696C6520636F6D6D656E7473, 'Consenti commenti profili', 'it', 'yum');
INSERT INTO `translation` VALUES (0x416C6C6F77656420617265206C6F77657263617365206C65747465727320616E64206469676974732E, 'Erlaubt sind Kleinbuchstaben und Ziffern.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x416C6C6F77656420617265206C6F77657263617365206C65747465727320616E64206469676974732E, 'Se permiten letras minúsculas y dígitos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x416C6C6F77656420617265206C6F77657263617365206C65747465727320616E64206469676974732E, 'Seules les minuscule et les chiffres sont autorises.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x416C6C6F77656420617265206C6F77657263617365206C65747465727320616E64206469676974732E, 'Sono consentiti lettere minuscole e numeri.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x416C6C6F77656420617265206C6F77657263617365206C65747465727320616E64206469676974732E, 'Erlaubt sind Kleinbuchstaben und Ziffern.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x416C6C6F776564206C6F77657263617365206C65747465727320616E64206469676974732E, 'Consenti lettere minuscole e numeri.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x416C6C6F776564206C6F77657263617365206C65747465727320616E64206469676974732E, 'Допускаются строчные буквы и цифры.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x416C6C6F77656420726F6C6573, 'Erlaubte Rollen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x416C6C6F77656420726F6C6573, 'Roles permitidos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x416C6C6F77656420726F6C6573, 'Permission role', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x416C6C6F77656420726F6C6573, 'Ruoli autorizzati', 'it', 'yum');
INSERT INTO `translation` VALUES (0x416C6C6F77656420726F6C6573, 'Dostepne role', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x416C6C6F776564207573657273, 'Erlaubte Benutzer', 'de', 'yum');
INSERT INTO `translation` VALUES (0x416C6C6F776564207573657273, 'Usuarios permitidos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x416C6C6F776564207573657273, 'Permission utilisateur', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x416C6C6F776564207573657273, 'Utenti autorizzati', 'it', 'yum');
INSERT INTO `translation` VALUES (0x416C6C6F776564207573657273, 'Dostepni uzytkownicy', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x416C7265616479206578697374732E, 'Existiert bereits.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x416C7265616479206578697374732E, 'Ya existe.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x416C7265616479206578697374732E, 'Existe deja.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x416C7265616479206578697374732E, 'Gia esistente', 'it', 'yum');
INSERT INTO `translation` VALUES (0x416C7265616479206578697374732E, 'Existiert bereits.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x416C7265616479206578697374732E, 'Уже существует.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x416E206572726F72206F636375726564207768696C6520736176696E6720796F7572206368616E676573, 'Es ist ein Fehler aufgetreten.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x416E206572726F72206F636375726564207768696C6520736176696E6720796F7572206368616E676573, 'Ocurrió un error al guardar los cambios', 'es', 'yum');
INSERT INTO `translation` VALUES (0x416E206572726F72206F636375726564207768696C6520736176696E6720796F7572206368616E676573, 'Une erreur est survenue.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x416E206572726F72206F636375726564207768696C6520736176696E6720796F7572206368616E676573, 'Si e verificato un errore durante il salvataggio delle modifiche.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x416E206572726F72206F636375726564207768696C6520736176696E6720796F7572206368616E676573, 'Wystapil blad podczas zapisywania Twoich zmian.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x416E206572726F72206F636375726564207768696C652075706C6F6164696E6720796F75722061766174617220696D616765, 'Ein Fehler ist beim hochladen ihres Profilbildes aufgetreten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x416E206572726F72206F636375726564207768696C652075706C6F6164696E6720796F75722061766174617220696D616765, 'Ha ocurrido un error al cargar una imagen de tu avatar', 'es', 'yum');
INSERT INTO `translation` VALUES (0x416E206572726F72206F636375726564207768696C652075706C6F6164696E6720796F75722061766174617220696D616765, 'Une erreur est survenue lors du chargement de votre photo de profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x416E206572726F72206F636375726564207768696C652075706C6F6164696E6720796F75722061766174617220696D616765, 'Si e verificato un errore durante il caricamento dell\'immagine', 'it', 'yum');
INSERT INTO `translation` VALUES (0x416E73776572, 'Antworten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x416E73776572, 'Respuesta ', 'es', 'yum');
INSERT INTO `translation` VALUES (0x41707065617220696E20736561726368, 'In der Suche erscheinen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x41707065617220696E20736561726368, 'Aparecen en la b', 'es', 'yum');
INSERT INTO `translation` VALUES (0x41707065617220696E20736561726368, 'Je souhaite apparaitre dans les resultats de recherche', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x41707065617220696E20736561726368, 'Mostra nelle ricerche', 'it', 'yum');
INSERT INTO `translation` VALUES (0x41726520796F75207265616C6C79207375726520796F752077616E7420746F2064656C65746520796F7572204163636F756E743F, 'Sind Sie Sicher, dass Sie Ihren Zugang löschen wollen?', 'de', 'yum');
INSERT INTO `translation` VALUES (0x41726520796F75207265616C6C79207375726520796F752077616E7420746F2064656C65746520796F7572204163636F756E743F, '¿Seguro que desea eliminar su cuenta?', 'es', 'yum');
INSERT INTO `translation` VALUES (0x41726520796F75207265616C6C79207375726520796F752077616E7420746F2064656C65746520796F7572204163636F756E743F, 'Etes vous sur de vouloir supprimer votre compte?', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x41726520796F75207265616C6C79207375726520796F752077616E7420746F2064656C65746520796F7572204163636F756E743F, 'Sicuro di voler cancellare il tuo account?', 'it', 'yum');
INSERT INTO `translation` VALUES (0x41726520796F75207265616C6C79207375726520796F752077616E7420746F2064656C65746520796F7572204163636F756E743F, 'Czy jestes pewien, ze chcesz usunac konto?', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x41726520796F75207375726520746F2064656C6574652074686973206974656D3F, 'Sind Sie sicher, dass Sie dieses Element wirklich löschen wollen? ', 'de', 'yum');
INSERT INTO `translation` VALUES (0x41726520796F75207375726520746F2064656C6574652074686973206974656D3F, '¿Seguro desea eliminar este elemento?', 'es', 'yum');
INSERT INTO `translation` VALUES (0x41726520796F75207375726520746F2064656C6574652074686973206974656D3F, 'Etes vous sur de supprime cet element?', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x41726520796F75207375726520746F2064656C6574652074686973206974656D3F, 'Sicuro di cancellare questo elemento?', 'it', 'yum');
INSERT INTO `translation` VALUES (0x41726520796F75207375726520746F2064656C6574652074686973206974656D3F, 'Вы действительно хотите удалить пользователя?', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x41726520796F75207375726520746F2072656D6F7665207468697320636F6D6D656E742066726F6D20796F75722070726F66696C653F, 'Sind Sie sicher, dass sie diesen Kommentar entfernen wollen?', 'de', 'yum');
INSERT INTO `translation` VALUES (0x41726520796F75207375726520746F2072656D6F7665207468697320636F6D6D656E742066726F6D20796F75722070726F66696C653F, '¿Estás seguro que deseas borrar este comentario?', 'es', 'yum');
INSERT INTO `translation` VALUES (0x41726520796F75207375726520746F2072656D6F7665207468697320636F6D6D656E742066726F6D20796F75722070726F66696C653F, 'Etes vous sur de vouloir supprimer ce commentaire?', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x41726520796F75207375726520746F2072656D6F7665207468697320636F6D6D656E742066726F6D20796F75722070726F66696C653F, 'Sicuro di voler eliminare il commento dal tuo profilo?', 'it', 'yum');
INSERT INTO `translation` VALUES (0x41726520796F75207375726520796F752077616E7420746F2072656D6F7665207468697320667269656E643F, 'Sind Sie sicher, dass Sie diesen Kontakt aus ihrer Liste entfernen wollen?', 'de', 'yum');
INSERT INTO `translation` VALUES (0x41726520796F75207375726520796F752077616E7420746F2072656D6F7665207468697320667269656E643F, '', 'es', 'yum');
INSERT INTO `translation` VALUES (0x41726520796F75207375726520796F752077616E7420746F2072656D6F7665207468697320667269656E643F, 'Etes vous sur de vouloir suprimer ce membre de votre liste de contact?', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x41726520796F75207375726520796F752077616E7420746F2072656D6F7665207468697320667269656E643F, 'Sicuro di voler rimuovere questo contatto?', 'it', 'yum');
INSERT INTO `translation` VALUES (0x41737369676E207468697320726F6C6520746F206E6577207573657273206175746F6D61746963616C6C79, 'Rolle automatisch an neue Benutzer zuweisen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x41737369676E207468697320726F6C6520746F206E6577207573657273206175746F6D61746963616C6C79, 'Asignar esta funci', 'es', 'yum');
INSERT INTO `translation` VALUES (0x41737369676E207468697320726F6C6520746F206E6577207573657273206175746F6D61746963616C6C79, 'Role automatique pour un nouveau membre', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x41737369676E207468697320726F6C6520746F206E6577207573657273206175746F6D61746963616C6C79, 'Assegna questo ruolo automaticamente ai nuovi utenti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4175746F6D61746963616C6C7920657874656E6420737562736372697074696F6E, 'Mitgliedschaft automatisch verlängern', 'de', 'yum');
INSERT INTO `translation` VALUES (0x41766174617220696D616765, 'Profilbild', 'de', 'yum');
INSERT INTO `translation` VALUES (0x41766174617220696D616765, 'Tu Avatar', 'es', 'yum');
INSERT INTO `translation` VALUES (0x41766174617220696D616765, 'Image de profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x41766174617220696D616765, 'Avatar', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4261636B, 'Zurück', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4261636B, 'Volver', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4261636B, 'Retour', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4261636B, 'Indietro', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4261636B20746F20696E626F78, 'Zurück zum Posteingang', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4261636B20746F20696E626F78, 'Volver a la bandeja de entrada', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4261636B20746F20696E626F78, 'Retour a la boite mail', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4261636B20746F20696E626F78, 'Torna alla posta in arrivo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4261636B20746F206D792050726F66696C65, 'Zurück zu meinem Profil', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4261636B20746F206D792050726F66696C65, 'Volver a mi Perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4261636B20746F206D792050726F66696C65, 'Retour a mon profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4261636B20746F206D792050726F66696C65, 'Torna al mio profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4261636B20746F2070726F66696C65, 'Zurück zum Profil', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4261636B20746F2070726F66696C65, 'Volver a perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4261636B20746F2070726F66696C65, 'Retour au profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4261636B20746F2070726F66696C65, 'Torna al mio profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4261636B20746F2070726F66696C65, 'Zuruck zum Profil', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x42616E6E6564, 'Verbannt', 'de', 'yum');
INSERT INTO `translation` VALUES (0x42616E6E6564, 'Excluido', 'es', 'yum');
INSERT INTO `translation` VALUES (0x42616E6E6564, 'Membre banni', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x42616E6E6564, 'Bannato', 'it', 'yum');
INSERT INTO `translation` VALUES (0x42616E6E6564, 'Verbannt', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x42616E6E6564, 'Заблокирован', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x42616E6E6564207573657273, 'Gesperrte Benuter', 'de', 'yum');
INSERT INTO `translation` VALUES (0x42616E6E6564207573657273, 'Usuarios excluidos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x42616E6E6564207573657273, 'Utilisateur bloque', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x42616E6E6564207573657273, 'Utenti bannati', 'it', 'yum');
INSERT INTO `translation` VALUES (0x42616E6E6564207573657273, 'Zbanowani uzytkownicy', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x42726F777365, 'Durchsuchen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x42726F777365, 'Navegar', 'es', 'yum');
INSERT INTO `translation` VALUES (0x42726F7773652067726F757073, 'Buscar grupos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x42726F777365206C6F6767656420757365722061637469766974696573, 'Benutzeraktivitäten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x42726F777365206C6F6767656420757365722061637469766974696573, 'Consultar bitácora de actividades del usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x42726F777365206C6F6767656420757365722061637469766974696573, 'Activite des membres', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x42726F777365206C6F6767656420757365722061637469766974696573, 'Naviga attivita utenti loggati', 'it', 'yum');
INSERT INTO `translation` VALUES (0x42726F777365206D656D6265727368697073, 'Mitgliedschaften kaufen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x42726F777365206D656D6265727368697073, 'Ver membres', 'es', 'yum');
INSERT INTO `translation` VALUES (0x42726F777365206D656D6265727368697073, 'Mitgliedschaften kaufen ??', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x42726F777365206D656D6265727368697073, 'Naviga iscrizioni', 'it', 'yum');
INSERT INTO `translation` VALUES (0x42726F77736520757365722061637469766974696573, 'Tätigkeitenhistorie', 'de', 'yum');
INSERT INTO `translation` VALUES (0x42726F77736520757365722061637469766974696573, 'Examinar las actividades del usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x42726F77736520757365722061637469766974696573, 'Activite de mon compte', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x42726F77736520757365722061637469766974696573, 'Naviga attivita utenti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x42726F77736520757365722067726F757073, 'Benutzergruppen durchsuchen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x42726F77736520757365722067726F757073, 'Buscar grupos de usuarios', 'es', 'yum');
INSERT INTO `translation` VALUES (0x42726F77736520757365722067726F757073, 'Rechercher dans un grouppe d utilisateurs', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x42726F77736520757365722067726F757073, 'Naviga gruppi utenti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x42726F777365207573657267726F757073, 'Gruppen durchsuchen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x42726F777365207573657267726F757073, 'Ver Grupos de Usuarios', 'es', 'yum');
INSERT INTO `translation` VALUES (0x42726F777365207573657267726F757073, 'Rechercher dans les grouppes', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x42726F777365207573657267726F757073, 'Naviga gruppi utenti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x42726F777365207573657273, 'Benutzer durchsuchen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x42726F777365207573657273, 'Buscar usuarios', 'es', 'yum');
INSERT INTO `translation` VALUES (0x42726F777365207573657273, 'Rechercher dans la liste des membres', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x42726F777365207573657273, 'Naviga utenti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x43616E63656C, 'Abbrechen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x43616E63656C, 'Cancelar', 'es', 'yum');
INSERT INTO `translation` VALUES (0x43616E63656C, 'Annuler', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x43616E63656C, 'Cancella', 'it', 'yum');
INSERT INTO `translation` VALUES (0x43616E63656C2064656C6574696F6E, 'Löschvorgang abbrechen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x43616E63656C2064656C6574696F6E, 'Cancelar eliminación', 'es', 'yum');
INSERT INTO `translation` VALUES (0x43616E63656C2064656C6574696F6E, 'Stopper la suppression', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x43616E63656C2064656C6574696F6E, 'Annulla cancellazione', 'it', 'yum');
INSERT INTO `translation` VALUES (0x43616E63656C2064656C6574696F6E, 'Anuluj usuwanie', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x43616E63656C2072657175657374, 'Anfrage zurückziehen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x43616E63656C2072657175657374, 'Cancelar pedido', 'es', 'yum');
INSERT INTO `translation` VALUES (0x43616E63656C2072657175657374, 'Annuler la demande de contact', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x43616E63656C2072657175657374, 'Cancella richiesta', 'it', 'yum');
INSERT INTO `translation` VALUES (0x43616E63656C20737562736372697074696F6E, 'Mitgliedschaft beenden', 'de', 'yum');
INSERT INTO `translation` VALUES (0x43616E6E6F74207365742070617373776F72642E2054727920616761696E2E, 'No pudimos guardar tu contraseña. Inténtalo otra vez', 'es', 'yum');
INSERT INTO `translation` VALUES (0x43617465676F7279, 'Kategorie', 'de', 'yum');
INSERT INTO `translation` VALUES (0x43617465676F7279, 'Categor', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4368616E67652050617373776F7264, 'Изменить пароль', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4368616E67652061646D696E2050617373776F7264, 'Administratorpasswort ändern', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4368616E67652061646D696E2050617373776F7264, 'Cambiar contraseña de Admin', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4368616E67652061646D696E2050617373776F7264, 'Changer le mot de passe de l administrateur', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4368616E67652061646D696E2050617373776F7264, 'Modifica password admin', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4368616E67652061646D696E2050617373776F7264, 'Zmien haslo administratora', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4368616E67652070617373776F7264, 'Passwort ändern', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4368616E67652070617373776F7264, 'Cambiar contraseña', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4368616E67652070617373776F7264, 'Modification du mot de', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4368616E67652070617373776F7264, 'Cambia password', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4368616E67652070617373776F7264, 'Passwort andern', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4368616E676573, 'Änderungen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4368616E676573, 'Cambios', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4368616E676573, 'Modification', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4368616E676573, 'Modifiche', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4368616E676573, 'Zmiany', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4368616E67657320617265207361766564, 'Änderungen wurden gespeichert.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4368616E67657320617265207361766564, 'Cambios guardados', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4368616E67657320617265207361766564, 'Les modifications ont bien ete enregistrees.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4368616E67657320617265207361766564, 'Modifiche salvate.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4368616E67657320617265207361766564, 'Zmiany zostaly zapisane.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4368616E6765732069732073617665642E, 'Änderungen wurde gespeichert.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4368616E6765732069732073617665642E, 'Cambio guardado', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4368616E6765732069732073617665642E, 'Modifications memorisees.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4368616E6765732069732073617665642E, 'Modifiche salvate', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4368616E6765732069732073617665642E, 'Изменения сохранены.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x43686F6F736520416C6C, 'Alle auswählen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x43686F6F736520416C6C, 'Seleccionar todos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x43686F6F736520416C6C, 'Selectioner tout', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x43686F6F736520416C6C, 'Scegli tutti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x43686F6F736520416C6C, 'Wybierz wszystkie', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x43697479, 'Stadt', 'de', 'yum');
INSERT INTO `translation` VALUES (0x43697479, 'Ciudad', 'es', 'yum');
INSERT INTO `translation` VALUES (0x43697479, 'Ville', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x43697479, 'Citta', 'it', 'yum');
INSERT INTO `translation` VALUES (0x43697479, 'Miasto', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x436C69636B206865726520746F20726573706F6E6420746F207B757365726E616D657D, 'Klicke hier, um {username} zu antworten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x436F6C756D6E204669656C64207479706520696E207468652064617461626173652E, 'Spaltentyp in der Datenbank', 'de', 'yum');
INSERT INTO `translation` VALUES (0x436F6C756D6E204669656C64207479706520696E207468652064617461626173652E, 'Columna tipo de Campo en la base de datos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x436F6C756D6E204669656C64207479706520696E207468652064617461626173652E, 'Type de la colone dans la banque de donnee', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x436F6C756D6E204669656C64207479706520696E207468652064617461626173652E, 'Tipo di colonna nel database', 'it', 'yum');
INSERT INTO `translation` VALUES (0x436F6C756D6E204669656C64207479706520696E207468652064617461626173652E, 'Spaltentyp in der Datenbank', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x436F6D6D656E74, 'Kommentar', 'de', 'yum');
INSERT INTO `translation` VALUES (0x436F6D6D656E74, 'Comentario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x436F6D6D656E74, 'Commentaire', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x436F6D6D656E74, 'Commento', 'it', 'yum');
INSERT INTO `translation` VALUES (0x436F6D706F7365, 'Nachricht schreiben', 'de', 'yum');
INSERT INTO `translation` VALUES (0x436F6D706F7365, 'Componer', 'es', 'yum');
INSERT INTO `translation` VALUES (0x436F6D706F7365, 'Ecrire un message', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x436F6D706F7365, 'Scrivi', 'it', 'yum');
INSERT INTO `translation` VALUES (0x436F6D706F7365206E6577206D657373616765, 'Nachricht schreiben', 'de', 'yum');
INSERT INTO `translation` VALUES (0x436F6D706F7365206E6577206D657373616765, 'Crear mensaje nuevo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x436F6D706F7365206E6577206D657373616765, 'Ecrire un nouveau message', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x436F6D706F7365206E6577206D657373616765, 'Scrivi nuovo messaggio', 'it', 'yum');
INSERT INTO `translation` VALUES (0x436F6D706F73696E67206E6577206D657373616765, 'Nachricht schreiben', 'de', 'yum');
INSERT INTO `translation` VALUES (0x436F6D706F73696E67206E6577206D657373616765, 'Creando mensaje nuevo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x436F6D706F73696E67206E6577206D657373616765, 'Ecrire un nouveau message', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x436F6D706F73696E67206E6577206D657373616765, 'Scrittura nuovo messaggio', 'it', 'yum');
INSERT INTO `translation` VALUES (0x436F6E6669726D, 'Bestätigen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x436F6E6669726D, 'Confirmar', 'es', 'yum');
INSERT INTO `translation` VALUES (0x436F6E6669726D, 'Confirmer', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x436F6E6669726D, 'Conferma', 'it', 'yum');
INSERT INTO `translation` VALUES (0x436F6E6669726D2064656C6574696F6E, 'Löschvorgang bestätigen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x436F6E6669726D2064656C6574696F6E, 'Confirmar eliminación', 'es', 'yum');
INSERT INTO `translation` VALUES (0x436F6E6669726D2064656C6574696F6E, 'Confirmation de suppression', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x436F6E6669726D2064656C6574696F6E, 'Conferma cancellazione', 'it', 'yum');
INSERT INTO `translation` VALUES (0x436F6E6669726D2064656C6574696F6E, 'Potwierdz usuwanie', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x436F6E6669726D6174696F6E2070656E64696E67, 'Bestätigung ausstehend', 'de', 'yum');
INSERT INTO `translation` VALUES (0x436F6E6669726D6174696F6E2070656E64696E67, 'Esperando confirmación', 'es', 'yum');
INSERT INTO `translation` VALUES (0x436F6E6669726D6174696F6E2070656E64696E67, 'Confirmation en attente', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x436F6E6669726D6174696F6E2070656E64696E67, 'In attesa di conferma', 'it', 'yum');
INSERT INTO `translation` VALUES (0x436F6E74656E74, 'Inhalt', 'de', 'yum');
INSERT INTO `translation` VALUES (0x436F6E74656E74, 'Contenido', 'es', 'yum');
INSERT INTO `translation` VALUES (0x436F6E74656E74, 'Texte du message', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x436F6E74656E74, 'Contenuto', 'it', 'yum');
INSERT INTO `translation` VALUES (0x437265617465, 'Anlegen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x437265617465, 'Crear', 'es', 'yum');
INSERT INTO `translation` VALUES (0x437265617465, 'Ceer', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x437265617465, 'Aggiungi', 'it', 'yum');
INSERT INTO `translation` VALUES (0x437265617465, 'Dodaj', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x437265617465, 'Новый', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x43726561746520416374696F6E, 'Crea azione', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4372656174652050726F66696C65204669656C64, 'Profilfeld anlegen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4372656174652050726F66696C65204669656C64, 'Crear Campo de Perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4372656174652050726F66696C65204669656C64, 'Nouveau champ de profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4372656174652050726F66696C65204669656C64, 'Aggiungi campo Profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4372656174652050726F66696C65204669656C64, 'Dodaj pole profilu', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4372656174652050726F66696C65204669656C64, 'Добавить', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x43726561746520526F6C65, 'Rolle anlegen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x43726561746520526F6C65, 'Crear Rol', 'es', 'yum');
INSERT INTO `translation` VALUES (0x43726561746520526F6C65, 'Creer un role', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x43726561746520526F6C65, 'Crea ruolo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x43726561746520526F6C65, 'Dodaj role', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4372656174652055736572, 'Benutzer anlegen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4372656174652055736572, 'Crear Usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4372656174652055736572, 'Creer un nouvel utilisateur', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4372656174652055736572, 'Nuovo utente', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4372656174652055736572, 'Новый', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x437265617465205573657267726F7570, 'Neue Gruppe erstellen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x437265617465205573657267726F7570, 'Crear un grupo de usuarios', 'es', 'yum');
INSERT INTO `translation` VALUES (0x437265617465205573657267726F7570, 'Crea gruppo utenti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206D792070726F66696C65, 'Mein Profil anlegen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206D792070726F66696C65, 'Crear mi perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206D792070726F66696C65, 'Crea profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E6577205472616E736C6174696F6E, 'Neue Übersetzung erstellen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E6577205472616E736C6174696F6E, 'Crear nueva traducción', 'es', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E65772055736572, 'Neuen Benutzer anlegen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E65772055736572, 'Crear nuevo usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E6577205573657267726F7570, 'Neue Gruppe erstellen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E6577205573657267726F7570, 'Crear nuevo grupo de usuarios', 'es', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E657720616374696F6E, 'Neue Aktion', 'de', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E657720616374696F6E, 'Crear acción nueva', 'es', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E657720616374696F6E, 'Nouvelle action', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E657720616374696F6E, 'Nuova azione', 'it', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E6577206669656C642067726F7570, 'Neue Feldgruppe erstellen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E6577206669656C642067726F7570, 'Crear campo de grupo nuevo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E6577206669656C642067726F7570, 'Creer un nouveau champs dans le groupe', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E6577206669656C642067726F7570, 'Nuovo campo gruppo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E6577206669656C642067726F7570, 'Dodaj nowa grupe pol', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E6577207061796D656E742074797065, 'Neue Zahlungsart hinzufügen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E6577207061796D656E742074797065, 'Crear nueva forma de pago', 'es', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E6577207061796D656E742074797065, 'Creer un nouveau mode de paiement', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E6577207061796D656E742074797065, 'Nuovo tipo pagamento', 'it', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E657720726F6C65, 'Neue Rolle anlegen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E657720726F6C65, 'Crear rol nuevo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E657720726F6C65, 'Creer un nouveau role', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E657720726F6C65, 'Nuovo ruolo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E657720726F6C65, 'Dodaj nowa role', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E65772073657474696E67732070726F66696C65, 'Neues Einstellungsprofil erstellen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E65772073657474696E67732070726F66696C65, 'Crear ajuste de perfil nuevo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E65772073657474696E67732070726F66696C65, 'creer une nouvelle configuration de profil.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E65772073657474696E67732070726F66696C65, 'Nuova opzion profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E65772073657474696E67732070726F66696C65, 'Dodaj nowe ustawienia profilu', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E65772075736572, 'Crear usuario nuevo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E65772075736572, 'Creer un nouveau membre', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E65772075736572, 'Nuovo utente', 'it', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E65772075736572, 'Dodaj nowego uzytkownika', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E6577207573657267726F7570, 'Neue Gruppe erstellen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E6577207573657267726F7570, 'Crear un nuevo grupo de usuarios', 'es', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E6577207573657267726F7570, 'Creer un nouveau grouppe', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x437265617465206E6577207573657267726F7570, 'Nuovo usergroup', 'it', 'yum');
INSERT INTO `translation` VALUES (0x437265617465207061796D656E742074797065, 'Zahlungsart anlegen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x437265617465207061796D656E742074797065, 'Crear el tipo de pago', 'es', 'yum');
INSERT INTO `translation` VALUES (0x437265617465207061796D656E742074797065, 'Crea tipo pagamento', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4372656174652070726F66696C65206669656C64, 'Ein neues Profilfeld erstellen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4372656174652070726F66696C65206669656C64, 'Crear campo de perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4372656174652070726F66696C65206669656C64, 'Creer un nouveau champ de profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4372656174652070726F66696C65206669656C64, 'Crea campo profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4372656174652070726F66696C65206669656C64, 'Dodaj pole do profilu', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4372656174652070726F66696C65206669656C64732067726F7570, 'Crear grupo de campos de perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4372656174652070726F66696C65206669656C64732067726F7570, 'Nuovo gruppo di campi profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4372656174652070726F66696C65206669656C64732067726F7570, 'Dodaj grupe pol do profilu', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x43726561746520726F6C65, 'Neue Rolle anlegen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x43726561746520726F6C65, 'Crear rol', 'es', 'yum');
INSERT INTO `translation` VALUES (0x43726561746520726F6C65, 'Creer un nouveau role', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x43726561746520726F6C65, 'Crea ruolo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x43726561746520726F6C65, 'Dodaj role', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4372656174652075736572, 'Benutzer anlegen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4372656174652075736572, 'Crear usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4372656174652075736572, 'Creer un membre', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4372656174652075736572, 'Crea utente', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4372656174652075736572, 'Dodaj uzytkownika', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x44617465, 'Datum', 'de', 'yum');
INSERT INTO `translation` VALUES (0x44617465, 'Fecha', 'es', 'yum');
INSERT INTO `translation` VALUES (0x44617465, 'Date', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x44617465, 'Data', 'it', 'yum');
INSERT INTO `translation` VALUES (0x44617465, 'Data', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x44656661756C74, 'Default', 'de', 'yum');
INSERT INTO `translation` VALUES (0x44656661756C74, 'Predeterminado', 'es', 'yum');
INSERT INTO `translation` VALUES (0x44656661756C74, 'Default', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x44656661756C74, 'Predefinito', 'it', 'yum');
INSERT INTO `translation` VALUES (0x44656661756C74, 'По умолчанию', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x44656C657465204163636F756E74, 'Zugang löschen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x44656C657465204163636F756E74, 'Eliminar Cuenta', 'es', 'yum');
INSERT INTO `translation` VALUES (0x44656C657465204163636F756E74, 'Supprimer le compte', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x44656C657465204163636F756E74, 'Cancella account', 'it', 'yum');
INSERT INTO `translation` VALUES (0x44656C6574652050726F66696C65204669656C64, 'Cancella campo Profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x44656C6574652050726F66696C65204669656C64, 'Удалить', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x44656C6574652055736572, 'Benutzer löschen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x44656C6574652055736572, 'Eliminar Usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x44656C6574652055736572, 'Supprimer le membre', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x44656C6574652055736572, 'Cancella utente', 'it', 'yum');
INSERT INTO `translation` VALUES (0x44656C6574652055736572, 'Удалить', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x44656C657465206163636F756E74, 'Zugang löschen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x44656C657465206163636F756E74, 'Eliminar cuenta', 'es', 'yum');
INSERT INTO `translation` VALUES (0x44656C657465206163636F756E74, 'Supprimer ce compte', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x44656C657465206163636F756E74, 'Cancella account', 'it', 'yum');
INSERT INTO `translation` VALUES (0x44656C657465206163636F756E74, 'Usun konto', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x44656C6574652066696C65, 'Cancella file', 'it', 'yum');
INSERT INTO `translation` VALUES (0x44656C657465206D657373616765, 'Nachricht löschen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x44656C657465206D657373616765, 'Eliminar mensaje', 'es', 'yum');
INSERT INTO `translation` VALUES (0x44656C657465206D657373616765, 'Supprimer le message', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x44656C657465206D657373616765, 'Cancella messaggio', 'it', 'yum');
INSERT INTO `translation` VALUES (0x44656C65746564, 'Gelöscht', 'de', 'yum');
INSERT INTO `translation` VALUES (0x44656C65746564, 'Eliminado', 'es', 'yum');
INSERT INTO `translation` VALUES (0x44656C65746564, 'Supprime', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x44656C65746564, 'Cancella', 'it', 'yum');
INSERT INTO `translation` VALUES (0x44656E79, 'Ablehnen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x44656E79, 'Negar', 'es', 'yum');
INSERT INTO `translation` VALUES (0x44656E79, 'Refuser', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x44656E79, 'Vietao', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4465736372697074696F6E, 'Beschreibung', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4465736372697074696F6E, 'Descripción', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4465736372697074696F6E, 'Description', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4465736372697074696F6E, 'Descrizione', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4465736372697074696F6E, 'Opis', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x446966666572656E74207573657273206C6F6767656420696E20746F646179, 'Anzahl der heute angemeldeten Benutzer', 'de', 'yum');
INSERT INTO `translation` VALUES (0x446966666572656E74207573657273206C6F6767656420696E20746F646179, 'Diferentes usuarios iniciaron sesión hoy', 'es', 'yum');
INSERT INTO `translation` VALUES (0x446966666572656E74207573657273206C6F6767656420696E20746F646179, 'Nombre d utilisateurs inscrits/connectes aujourd hui.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x446966666572656E74207573657273206C6F6767656420696E20746F646179, 'Numero di utenti connessi oggi', 'it', 'yum');
INSERT INTO `translation` VALUES (0x446966666572656E74207573657273206C6F6767656420696E20746F646179, 'Liczba dzisiejszych unikalnych logowan', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x446966666572656E7420766965776E2050726F66696C6573, 'Insgesamt betrachtete Profile', 'de', 'yum');
INSERT INTO `translation` VALUES (0x446966666572656E7420766965776E2050726F66696C6573, 'Perfiles diferentes vistos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x446966666572656E7420766965776E2050726F66696C6573, 'Total des profils consultes', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x446966666572656E7420766965776E2050726F66696C6573, 'Visualizzazioni profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x446973706C6179206F72646572206F66206669656C64732E, 'Reihenfolgenposition, in der das Feld angezeigt wird', 'de', 'yum');
INSERT INTO `translation` VALUES (0x446973706C6179206F72646572206F66206669656C64732E, 'Mostrar orden de los campos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x446973706C6179206F72646572206F66206669656C64732E, 'Ordre de position dans laquelle le champ apparaitra', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x446973706C6179206F72646572206F66206669656C64732E, 'Mostra ordine dei campi.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x446973706C6179206F72646572206F66206669656C64732E, 'Kolejnosc wyswietlania pol.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x446973706C6179206F72646572206F66206669656C64732E, 'Порядок отображения полей.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x446973706C6179206F72646572206F662067726F75702E, 'Anzeigereihenfolge der Gruppe.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x446973706C6179206F72646572206F662067726F75702E, 'Mostrar orden del grupo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x446973706C6179206F72646572206F662067726F75702E, 'Annonces ordonnees du grouppe.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x446973706C6179206F72646572206F662067726F75702E, 'Ordine di visualizzazione del gruppo.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x446973706C6179206F72646572206F662067726F75702E, 'Wyswietl kolejnosc grup.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x446F206E6F742061707065617220696E20736561726368, 'Nicht in der Suche erscheinen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x446F206E6F742061707065617220696E20736561726368, 'No aparecer en la b', 'es', 'yum');
INSERT INTO `translation` VALUES (0x446F206E6F742061707065617220696E20736561726368, 'Ne pas paraitre dans les resultat de recherche', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x446F206E6F742061707065617220696E20736561726368, 'Non mostrare nelle ricerche', 'it', 'yum');
INSERT INTO `translation` VALUES (0x446F206E6F742073686F77206D79206F6E6C696E6520737461747573, 'Status verstecken', 'de', 'yum');
INSERT INTO `translation` VALUES (0x446F206E6F742073686F77206D79206F6E6C696E6520737461747573, 'No mostrar mi estado de conexi', 'es', 'yum');
INSERT INTO `translation` VALUES (0x446F206E6F742073686F77206D79206F6E6C696E6520737461747573, 'Ne pas rendre mon profil visible lorsque je suis en ligne', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x446F206E6F742073686F77206D79206F6E6C696E6520737461747573, 'Non mostrare il mio stato online', 'it', 'yum');
INSERT INTO `translation` VALUES (0x446F206E6F742073686F7720746865206F776E6572206F6620612070726F66696C65207768656E20692076697369742068696D, 'Niemandem zeigen, wen ich besucht habe', 'de', 'yum');
INSERT INTO `translation` VALUES (0x446F206E6F742073686F7720746865206F776E6572206F6620612070726F66696C65207768656E20692076697369742068696D, 'No se repite el due', 'es', 'yum');
INSERT INTO `translation` VALUES (0x446F206E6F742073686F7720746865206F776E6572206F6620612070726F66696C65207768656E20692076697369742068696D, 'Ne pas montrer les profils que j ai visite', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x446F206E6F742073686F7720746865206F776E6572206F6620612070726F66696C65207768656E20692076697369742068696D, 'Non mostrare al proprietario quando visito il suo profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x446F776E677261646520746F207B726F6C657D, 'Wechsle auf {role}', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4475726174696F6E20696E2064617973, 'Gültigkeitsdauer in Tagen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4475726174696F6E20696E2064617973, 'Duraci', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4475726174696F6E20696E2064617973, 'Validite en jours', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4475726174696F6E20696E2064617973, 'Durata in giorni', 'it', 'yum');
INSERT INTO `translation` VALUES (0x452D4D61696C2061646472657373, 'E-Mail Adresse', 'de', 'yum');
INSERT INTO `translation` VALUES (0x452D4D61696C2061646472657373, 'Correo electrónico', 'es', 'yum');
INSERT INTO `translation` VALUES (0x452D4D61696C2061646472657373, 'Adresse e-mail', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x452D4D61696C2061646472657373, 'Indirizzo email', 'it', 'yum');
INSERT INTO `translation` VALUES (0x452D4D61696C20616C726561647920696E207573652E20496620796F752068617665206E6F742072656769737465726564206265666F72652C20706C6561736520636F6E74616374206F75722053797374656D2061646D696E6973747261746F722E, 'Este correo ya está siendo usado por alguien. Si no te habías registrado antes entonces contáctanos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x452D6D61696C, 'E-mail', 'de', 'yum');
INSERT INTO `translation` VALUES (0x452D6D61696C, 'Correo electrónico', 'es', 'yum');
INSERT INTO `translation` VALUES (0x452D6D61696C, 'E-mail', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x452D6D61696C, 'E-mail', 'it', 'yum');
INSERT INTO `translation` VALUES (0x452D6D61696C, 'Mejl', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x452D6D61696C, 'Электронная почта', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x45646974, 'Bearbeiten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x45646974, 'Editar', 'es', 'yum');
INSERT INTO `translation` VALUES (0x45646974, 'Editer', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x45646974, 'Modifica', 'it', 'yum');
INSERT INTO `translation` VALUES (0x45646974, 'Bearbeiten', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x45646974, 'Редактировать', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4564697420706572736F6E616C2064617461, 'Persönliche Daten bearbeiten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4564697420706572736F6E616C2064617461, 'Editar datos personales', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4564697420706572736F6E616C2064617461, 'Modifier mes donnees personnelles', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4564697420706572736F6E616C2064617461, 'Modifica dati personali', 'it', 'yum');
INSERT INTO `translation` VALUES (0x456469742070726F66696C65, 'Profil bearbeiten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x456469742070726F66696C65, 'Editar perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x456469742070726F66696C65, 'Editer le profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x456469742070726F66696C65, 'Modifica profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x456469742070726F66696C65, 'Meine Profildaten bearbeiten', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x456469742070726F66696C65, 'Редактирование профиля', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x456469742070726F66696C65206669656C64, 'Profilfeld bearbeiten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x456469742070726F66696C65206669656C64, 'Editar campo del perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x456469742070726F66696C65206669656C64, 'Editer les champ du profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x456469742070726F66696C65206669656C64, 'Modifica campi profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x456469742070726F66696C65206669656C64, 'Profilfeld bearbeiten', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x456469742074657874, 'Modifica testo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x45646974207468697320726F6C65, 'Diese Rolle bearbeiten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x45646974207468697320726F6C65, 'Editar este rol', 'es', 'yum');
INSERT INTO `translation` VALUES (0x45646974207468697320726F6C65, 'Modifier ce role', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x45646974207468697320726F6C65, 'Modifica questo ruolo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x45646974207468697320726F6C65, 'Zmien te role', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x456D61696C20697320696E636F72726563742E, 'E-Mail ist nicht korrekt.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x456D61696C20697320696E636F72726563742E, 'Email incorrecto', 'es', 'yum');
INSERT INTO `translation` VALUES (0x456D61696C20697320696E636F72726563742E, 'L adresse e-mail est incorrecte.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x456D61696C20697320696E636F72726563742E, 'Email non corretta.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x456D61696C20697320696E636F72726563742E, 'Mejl jest niepoprawny.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x456D61696C20697320696E636F72726563742E, 'Пользователь с таким электроным адресом не зарегистрирован.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x456D61696C206973206E6F7420736574207768656E20747279696E6720746F2073656E6420526567697374726174696F6E20456D61696C, 'Debes colocar el correo electrónico para enviar el correo de registro', 'es', 'yum');
INSERT INTO `translation` VALUES (0x456E61626C652043617074636861, 'Captcha Überprüfung aktivieren', 'de', 'yum');
INSERT INTO `translation` VALUES (0x456E61626C652043617074636861, 'Habilitar Captcha', 'es', 'yum');
INSERT INTO `translation` VALUES (0x456E61626C652043617074636861, 'Activer le controle par Captcha', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x456E61626C652043617074636861, 'Attiva Captcha', 'it', 'yum');
INSERT INTO `translation` VALUES (0x456E61626C652043617074636861, 'Wlacz Captcha', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x456E61626C6520456D61696C2041637469766174696F6E, 'Aktivierung per E-Mail einschalten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x456E61626C6520456D61696C2041637469766174696F6E, 'Habilitar Activación por Email', 'es', 'yum');
INSERT INTO `translation` VALUES (0x456E61626C6520456D61696C2041637469766174696F6E, 'Activer l activation par e-mail', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x456E61626C6520456D61696C2041637469766174696F6E, 'Attiva attivazione via Email', 'it', 'yum');
INSERT INTO `translation` VALUES (0x456E61626C6520456D61696C2041637469766174696F6E, 'Wlacz aktywacje mejlem', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x456E61626C652050726F66696C6520486973746F7279, 'Profilhistorie einschalten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x456E61626C652050726F66696C6520486973746F7279, 'Habilitar Historial de Perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x456E61626C652050726F66696C6520486973746F7279, 'Activer le protocole des profils', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x456E61626C652050726F66696C6520486973746F7279, 'Attiva storico Profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x456E61626C652050726F66696C6520486973746F7279, 'Wlacz historie profilow', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x456E61626C65205265636F76657279, 'Wiederherstellung einschalten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x456E61626C65205265636F76657279, 'Habilitar Recuperación', 'es', 'yum');
INSERT INTO `translation` VALUES (0x456E61626C65205265636F76657279, 'Activer la restauration', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x456E61626C65205265636F76657279, 'Attiva rispristino', 'it', 'yum');
INSERT INTO `translation` VALUES (0x456E61626C65205265636F76657279, 'Wlacz odzyskiwanie hasel', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x456E61626C6520526567697374726174696F6E, 'Registrierung einschalten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x456E61626C6520526567697374726174696F6E, 'Habilitar Registro', 'es', 'yum');
INSERT INTO `translation` VALUES (0x456E61626C6520526567697374726174696F6E, 'Activer l enregistrement', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x456E61626C6520526567697374726174696F6E, 'Attiva registrazione', 'it', 'yum');
INSERT INTO `translation` VALUES (0x456E61626C6520526567697374726174696F6E, 'Wlacz rejestracje', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x456E642064617465, 'Enddatum', 'de', 'yum');
INSERT INTO `translation` VALUES (0x456E642064617465, 'Fecha final', 'es', 'yum');
INSERT INTO `translation` VALUES (0x456E642064617465, 'Data scadenza', 'it', 'yum');
INSERT INTO `translation` VALUES (0x456E6473206174, 'Endet am', 'de', 'yum');
INSERT INTO `translation` VALUES (0x456E6473206174, 'Termina en', 'es', 'yum');
INSERT INTO `translation` VALUES (0x456E6473206174, 'Scade il', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4572726F72204D657373616765, 'Fehlermeldung', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4572726F72204D657373616765, 'Mensaje de Error', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4572726F72204D657373616765, 'Message d erreur', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4572726F72204D657373616765, 'Messaggio d\'errore', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4572726F72204D657373616765, 'Сообщение об ошибке', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4572726F72206D657373616765207768656E2056616C69646174696F6E206661696C732E, 'Fehlermeldung falls die Validierung fehlschlägt', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4572726F72206D657373616765207768656E2056616C69646174696F6E206661696C732E, 'Mensaje de error cuando la Validación falla', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4572726F72206D657373616765207768656E2056616C69646174696F6E206661696C732E, 'Message d erreur pour le cas ou la validation echoue', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4572726F72206D657373616765207768656E2056616C69646174696F6E206661696C732E, 'Messaggio d\'errore se fallisce la validazione', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4572726F72206D657373616765207768656E20796F752076616C69646174652074686520666F726D2E, 'Messaggio d\'errore durante la validazione del form.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4572726F72206D657373616765207768656E20796F752076616C69646174652074686520666F726D2E, 'Сообщение об ошибке при проверке формы.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4572726F72207768696C652070726F63657373696E67206E65772061766174617220696D616765203A207B6572726F725F6D6573736167657D3B2046696C65207761732075706C6F6164656420776974686F757420726573697A696E67, 'Das Bild konnte nicht richtig skaliert werden: {error_message}. Es wurde trotzdem erfolgreich hochgeladen und in ihrem Profil aktiviert.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4572726F72207768696C652070726F63657373696E67206E65772061766174617220696D616765203A207B6572726F725F6D6573736167657D3B2046696C65207761732075706C6F6164656420776974686F757420726573697A696E67, 'Error al procesar la imagen nuevo avatar: {mensaje_error}; El archivo se ha subido sin cambiar el tama', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4572726F72207768696C652070726F63657373696E67206E65772061766174617220696D616765203A207B6572726F725F6D6573736167657D3B2046696C65207761732075706C6F6164656420776974686F757420726573697A696E67, 'L image n a pas pu etre retaillee automatiquement lors du chargement. : {error_message}. elle a ete cependant chargee avec succes et activee dans votre profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4572726F72207768696C652070726F63657373696E67206E65772061766174617220696D616765203A207B6572726F725F6D6573736167657D3B2046696C65207761732075706C6F6164656420776974686F757420726573697A696E67, 'Errore processando il nuovo avatar: {error_message}. File caricato senza ridimensionamento.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x45787069726564, 'Abgelaufen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x45787069726564, 'Caducado', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4669656C64, 'Feld', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4669656C64, 'Campo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4669656C64, 'Champ', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4669656C64, 'Campo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4669656C64, 'Pole', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642053697A65, 'Feldgröße', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642053697A65, 'Tamaño del Campo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642053697A65, 'Longueur du champ', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642053697A65, 'Dimensione campo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642053697A65, 'Размер поля', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642053697A65206D696E, 'min Feldgröße', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642053697A65206D696E, 'Tamaño mínimo del campo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642053697A65206D696E, 'longueur du champ minimum', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642053697A65206D696E, 'Dimesione minima campo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642053697A65206D696E, 'Минимальное значение', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642054797065, 'Feldtyp', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642054797065, 'Tipo de Campo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642054797065, 'Type du champ', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642054797065, 'Tipo campo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642054797065, 'Тип поля', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642067726F7570, 'Feldgruppe', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642067726F7570, 'Grupo de Campos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642067726F7570, 'Champ des groupes', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642067726F7570, 'Campi gruppo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642067726F7570, 'Grupa pol', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4669656C64206E616D65206F6E20746865206C616E6775616765206F662022736F757263654C616E6775616765222E, 'Feldname in der Ursprungssprache', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4669656C64206E616D65206F6E20746865206C616E6775616765206F662022736F757263654C616E6775616765222E, 'Nombre del campo en el idioma \"sourceLanguage\".', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4669656C64206E616D65206F6E20746865206C616E6775616765206F66202671756F743B736F757263654C616E67756167652671756F743B2E, 'Non du champ dans la langue standard', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4669656C64206E616D65206F6E20746865206C616E6775616765206F66202671756F743B736F757263654C616E67756167652671756F743B2E, 'Nome campo per il linguaggio di \"sourceLanguage\".', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4669656C64206E616D65206F6E20746865206C616E6775616765206F66202671756F743B736F757263654C616E67756167652671756F743B2E, 'Feldname in der Ursprungssprache', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4669656C64206E616D65206F6E20746865206C616E6775616765206F66202671756F743B736F757263654C616E67756167652671756F743B2E, 'Название поля на языке \"sourceLanguage\".', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642073697A65, 'Feldgröße', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642073697A65, 'Tamaño del campo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642073697A65, 'Longueur du champ', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642073697A65, 'Dimensione campo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642073697A65, 'Feldgro?e', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642073697A6520636F6C756D6E20696E207468652064617461626173652E, 'Dimensione campo nel database', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642073697A6520636F6C756D6E20696E207468652064617461626173652E, 'Размер поля колонки в базе данных', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642073697A6520696E207468652064617461626173652E, 'Feldgröße in der Datenbank', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642073697A6520696E207468652064617461626173652E, 'Tamaño del campo en la base de datos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642073697A6520696E207468652064617461626173652E, 'Longueur du champ dans la banque de donnee', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642073697A6520696E207468652064617461626173652E, 'Dimensione campo nel database', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642073697A6520696E207468652064617461626173652E, 'Feldgro?e in der Datenbank', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642074797065, 'Feldtyp', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642074797065, 'Tipo de campo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642074797065, 'Type de champ', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642074797065, 'Tipo campo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4669656C642074797065, 'Feldtyp', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4669656C64207479706520636F6C756D6E20696E207468652064617461626173652E, 'Tipo campo nel database.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4669656C64207479706520636F6C756D6E20696E207468652064617461626173652E, 'Тип поля колонки в базе данных.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4669656C64732077697468202A206172652072657175697265642E, 'Los campos con * son obligatorios', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4669656C64732077697468203C7370616E20636C6173733D227265717569726564223E2A3C2F7370616E3E206172652072657175697265642E, 'Felder mit <span class=\"required\">*</span> sind Pflichtfelder.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4669727374204E616D65, 'Nome', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4669727374204E616D65, 'Имя', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4669727374206E616D65, 'Vorname', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4669727374206E616D65, 'Nombre', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4669727374206E616D65, 'Prenom', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4669727374206E616D65, 'Cognome', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4669727374206E616D65, 'Vorname', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x466F7220616C6C, 'Für alle', 'de', 'yum');
INSERT INTO `translation` VALUES (0x466F7220616C6C, 'Para todos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x466F7220616C6C, 'Pour tous', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x466F7220616C6C, 'Per tutti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x466F7220616C6C, 'Для всех', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x466F726D2076616C69646174696F6E206572726F72, 'Error en la validación del formulario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x467269656E6473, 'Kontakte', 'de', 'yum');
INSERT INTO `translation` VALUES (0x467269656E6473, 'Amigos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x467269656E6473, 'Mes contacts', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x467269656E6473, 'Contatti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x467269656E6473206F66207B757365726E616D657D, 'Kontakte von {username}', 'de', 'yum');
INSERT INTO `translation` VALUES (0x467269656E6473206F66207B757365726E616D657D, 'Amigos de {username}', 'es', 'yum');
INSERT INTO `translation` VALUES (0x467269656E6473206F66207B757365726E616D657D, 'Contact de {username}', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x467269656E6473206F66207B757365726E616D657D, 'Contatti di {username}', 'it', 'yum');
INSERT INTO `translation` VALUES (0x467269656E6473686970, 'Kontakt', 'de', 'yum');
INSERT INTO `translation` VALUES (0x467269656E6473686970, 'Amistades', 'es', 'yum');
INSERT INTO `translation` VALUES (0x467269656E6473686970, 'Contact', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x467269656E6473686970, 'Contatto', 'it', 'yum');
INSERT INTO `translation` VALUES (0x467269656E647368697020636F6E6669726D6564, 'Freundschaft bestätigt', 'de', 'yum');
INSERT INTO `translation` VALUES (0x467269656E647368697020636F6E6669726D6564, 'Amistad confirmada', 'es', 'yum');
INSERT INTO `translation` VALUES (0x467269656E647368697020636F6E6669726D6564, 'Demande de contact confirmee', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x467269656E647368697020636F6E6669726D6564, 'Contatto confermato', 'it', 'yum');
INSERT INTO `translation` VALUES (0x467269656E64736869702072656A6563746564, 'Kontaktanfrage abgelehnt', 'de', 'yum');
INSERT INTO `translation` VALUES (0x467269656E64736869702072656A6563746564, 'La amistad rechazada', 'es', 'yum');
INSERT INTO `translation` VALUES (0x467269656E64736869702072656A6563746564, 'Demande de contact refusee', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x467269656E64736869702072656A6563746564, 'Amizicia rigettata', 'it', 'yum');
INSERT INTO `translation` VALUES (0x467269656E6473686970207265717565737420616C72656164792073656E74, 'Kontaktbestätigung ausstehend', 'de', 'yum');
INSERT INTO `translation` VALUES (0x467269656E6473686970207265717565737420616C72656164792073656E74, 'Ya se envió la solicitud de amistad', 'es', 'yum');
INSERT INTO `translation` VALUES (0x467269656E6473686970207265717565737420616C72656164792073656E74, 'En attente de confirmation', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x467269656E6473686970207265717565737420616C72656164792073656E74, 'Richiesta di contatto gia inviata', 'it', 'yum');
INSERT INTO `translation` VALUES (0x467269656E6473686970207265717565737420666F72207B757365726E616D657D20686173206265656E2073656E74, 'Kontaktanfrage an {username} gesendet', 'de', 'yum');
INSERT INTO `translation` VALUES (0x467269656E6473686970207265717565737420666F72207B757365726E616D657D20686173206265656E2073656E74, 'La solicitud de amistad a {username} ha sido enviada', 'es', 'yum');
INSERT INTO `translation` VALUES (0x467269656E6473686970207265717565737420666F72207B757365726E616D657D20686173206265656E2073656E74, 'Demande de contact envoyee a {username}', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x467269656E6473686970207265717565737420666F72207B757365726E616D657D20686173206265656E2073656E74, 'Inviata richiesta di contatto a {username}', 'it', 'yum');
INSERT INTO `translation` VALUES (0x467269656E6473686970207265717565737420686173206265656E2072656A6563746564, 'Kontaktanfrage zurückgewiesen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x467269656E6473686970207265717565737420686173206265656E2072656A6563746564, 'Solicitud de amistad rechazada', 'es', 'yum');
INSERT INTO `translation` VALUES (0x467269656E6473686970207265717565737420686173206265656E2072656A6563746564, 'Votre demande de contact a ete rejetee', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x467269656E6473686970207265717565737420686173206265656E2072656A6563746564, 'Richiesta di contatto respinta', 'it', 'yum');
INSERT INTO `translation` VALUES (0x46726F6D, 'Von', 'de', 'yum');
INSERT INTO `translation` VALUES (0x46726F6D, 'Desde', 'es', 'yum');
INSERT INTO `translation` VALUES (0x46726F6D, 'De', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x46726F6D, 'Da', 'it', 'yum');
INSERT INTO `translation` VALUES (0x46726F6D, 'Od', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x47656E6572616C, 'Allgemein', 'de', 'yum');
INSERT INTO `translation` VALUES (0x47656E6572616C, ' General ', 'es', 'yum');
INSERT INTO `translation` VALUES (0x47656E6572616C, 'Generale', 'it', 'yum');
INSERT INTO `translation` VALUES (0x47656E65726174652044656D6F2044617461, 'Zufallsbenutzer erzeugen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x47656E65726174652044656D6F2044617461, 'Genera datos de prueba', 'es', 'yum');
INSERT INTO `translation` VALUES (0x47656E65726174652044656D6F2044617461, 'Generer un compte membre-demo', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x47656E65726174652044656D6F2044617461, 'Genera dati demo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x476F20746F2070726F66696C65206F66207B757365726E616D657D, 'Zum Profil von {username}', 'de', 'yum');
INSERT INTO `translation` VALUES (0x476F20746F2070726F66696C65206F66207B757365726E616D657D, 'Ir al perfil de {username}', 'es', 'yum');
INSERT INTO `translation` VALUES (0x476F20746F2070726F66696C65206F66207B757365726E616D657D, 'Voir le profil de {username}', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x476F20746F2070726F66696C65206F66207B757365726E616D657D, 'Vai al profilo di {username}', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4772616E74207065726D697373696F6E, 'Berechtigung zuweisen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4772616E74207065726D697373696F6E, 'Otorgar permiso', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4772616E74207065726D697373696F6E, 'Attribuer une permission', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4772616E74207065726D697373696F6E, 'Assegna permesso', 'it', 'yum');
INSERT INTO `translation` VALUES (0x47726F7570204E616D65, 'Gruppenname', 'de', 'yum');
INSERT INTO `translation` VALUES (0x47726F7570204E616D65, 'Nombre de grupo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x47726F7570204E616D65, 'Nom du groupe', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x47726F7570204E616D65, 'Nome gruppo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x47726F7570204E616D65, 'Nazwa grupy', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x47726F7570206E616D65206F6E20746865206C616E6775616765206F662022736F757263654C616E6775616765222E, 'Gruppenname in der Basissprache.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x47726F7570206E616D65206F6E20746865206C616E6775616765206F662022736F757263654C616E6775616765222E, 'Nombre del grupo en el idioma \"sourceLanguage\".', 'es', 'yum');
INSERT INTO `translation` VALUES (0x47726F7570206E616D65206F6E20746865206C616E6775616765206F66202671756F743B736F757263654C616E67756167652671756F743B2E, 'Nom du groupe dans la langue principale.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x47726F7570206E616D65206F6E20746865206C616E6775616765206F66202671756F743B736F757263654C616E67756167652671756F743B2E, 'Il nome del gruppo nella lingua di base.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x47726F7570206E616D65206F6E20746865206C616E6775616765206F66202671756F743B736F757263654C616E67756167652671756F743B2E, 'Nazwa grupy w jezyku uzytkownika.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x47726F7570206F776E6572, 'Gruppeneigentümer', 'de', 'yum');
INSERT INTO `translation` VALUES (0x47726F7570206F776E6572, 'Dueño del grupo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x47726F7570206F776E6572, 'Proprietaire du grouppe', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x47726F7570206F776E6572, 'Proprietario gruppo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x47726F7570207469746C65, 'Titel der Gruppe', 'de', 'yum');
INSERT INTO `translation` VALUES (0x47726F7570207469746C65, 'Título del grupo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x47726F7570207469746C65, 'Titre du grouppe', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x47726F7570207469746C65, 'Titolo gruppo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x486176696E67, 'Anzeigen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x486176696E67, 'Teniendo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x486176696E67, 'Annonce', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x486176696E67, 'Visualizza', 'it', 'yum');
INSERT INTO `translation` VALUES (0x48696464656E, 'Verstecken', 'de', 'yum');
INSERT INTO `translation` VALUES (0x48696464656E, 'Escondido', 'es', 'yum');
INSERT INTO `translation` VALUES (0x48696464656E, 'Cache', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x48696464656E, 'Nascosto', 'it', 'yum');
INSERT INTO `translation` VALUES (0x48696464656E, 'Скрыт', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x486F7720657870656E736976652069732061206D656D626572736869703F, 'Preis der Mitgliedschaft', 'de', 'yum');
INSERT INTO `translation` VALUES (0x486F7720657870656E736976652069732061206D656D626572736869703F, '', 'es', 'yum');
INSERT INTO `translation` VALUES (0x486F7720657870656E736976652069732061206D656D626572736869703F, 'Prix de l abbonement', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x486F7720657870656E736976652069732061206D656D626572736869703F, 'Quanto costa l\'iscrizione?', 'it', 'yum');
INSERT INTO `translation` VALUES (0x486F77206D616E7920646179732077696C6C20746865206D656D626572736869702062652076616C6964206166746572207061796D656E743F, 'Wie viele Tage ist die Mitgliedschaft nach Zahlungseingang gültig?', 'de', 'yum');
INSERT INTO `translation` VALUES (0x486F77206D616E7920646179732077696C6C20746865206D656D626572736869702062652076616C6964206166746572207061796D656E743F, '', 'es', 'yum');
INSERT INTO `translation` VALUES (0x486F77206D616E7920646179732077696C6C20746865206D656D626572736869702062652076616C6964206166746572207061796D656E743F, 'Nombre de jours pour la validite d un abbonement apres paiement?', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x486F77206D616E7920646179732077696C6C20746865206D656D626572736869702062652076616C6964206166746572207061796D656E743F, 'Quanti giorni e valida l\'iscrizione dopo il pagamento?', 'it', 'yum');
INSERT INTO `translation` VALUES (0x49676E6F7265, 'Ignorieren', 'de', 'yum');
INSERT INTO `translation` VALUES (0x49676E6F7265, 'Ignorar', 'es', 'yum');
INSERT INTO `translation` VALUES (0x49676E6F7265, 'Ignorer', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x49676E6F7265, 'Ignora', 'it', 'yum');
INSERT INTO `translation` VALUES (0x49676E6F726564207573657273, 'Ignorierliste', 'de', 'yum');
INSERT INTO `translation` VALUES (0x49676E6F726564207573657273, 'Usuarios ignorados', 'es', 'yum');
INSERT INTO `translation` VALUES (0x49676E6F726564207573657273, 'Liste noire', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x49676E6F726564207573657273, 'Utenti ignorati', 'it', 'yum');
INSERT INTO `translation` VALUES (0x496E616374697665207573657273, 'Inaktive Benutzer', 'de', 'yum');
INSERT INTO `translation` VALUES (0x496E616374697665207573657273, 'Usuarios inactivos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x496E616374697665207573657273, 'Utilisateur inactif', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x496E616374697665207573657273, 'Utenti inattivi', 'it', 'yum');
INSERT INTO `translation` VALUES (0x496E616374697665207573657273, 'Nieaktywni uzytkownicy', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x496E636F72726563742061637469766174696F6E2055524C, 'El enlace de activación que usaste es incorrecto', 'es', 'yum');
INSERT INTO `translation` VALUES (0x496E636F72726563742061637469766174696F6E2055524C2E, 'Falsche Aktivierungs URL.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x496E636F72726563742061637469766174696F6E2055524C2E, 'URL de activación incorrecta.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x496E636F72726563742061637469766174696F6E2055524C2E, 'Le lien d activation de votre compte est incorrect ou perime. Consultez notre FAQ: mot cle= inscription ou contactez gratuitement notre Help-Center en ligne sur la page d aide.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x496E636F72726563742061637469766174696F6E2055524C2E, 'URL di attivazione incorretto', 'it', 'yum');
INSERT INTO `translation` VALUES (0x496E636F72726563742061637469766174696F6E2055524C2E, 'Falsche Aktivierungs URL.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x496E636F72726563742061637469766174696F6E2055524C2E, 'Неправильная ссылка активации учетной записи.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x496E636F72726563742070617373776F726420286D696E696D616C206C656E67746820342073796D626F6C73292E, 'Falsches Passwort (minimale Länge 4 Zeichen).', 'de', 'yum');
INSERT INTO `translation` VALUES (0x496E636F72726563742070617373776F726420286D696E696D616C206C656E67746820342073796D626F6C73292E, 'Contraseña incorrecta (debe tener mínimo 4 caracteres).', 'es', 'yum');
INSERT INTO `translation` VALUES (0x496E636F72726563742070617373776F726420286D696E696D616C206C656E67746820342073796D626F6C73292E, 'Mot de passe incorrect (longueur minimal de 4 characteres).', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x496E636F72726563742070617373776F726420286D696E696D616C206C656E67746820342073796D626F6C73292E, 'Password sbagliata (lunga almeno 4 caratteri).', 'it', 'yum');
INSERT INTO `translation` VALUES (0x496E636F72726563742070617373776F726420286D696E696D616C206C656E67746820342073796D626F6C73292E, 'Falsches Passwort (minimale Lange 4 Zeichen).', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x496E636F72726563742070617373776F726420286D696E696D616C206C656E67746820342073796D626F6C73292E, 'Минимальная длина пароля 4 символа.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x496E636F7272656374207265636F76657279206C696E6B2E, 'Recovery link ist falsch.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x496E636F7272656374207265636F76657279206C696E6B2E, 'Enlace de recuperación que usaste es incorrecto', 'es', 'yum');
INSERT INTO `translation` VALUES (0x496E636F7272656374207265636F76657279206C696E6B2E, 'Le lien de restauration est incorrect ou perime.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x496E636F7272656374207265636F76657279206C696E6B2E, 'Link ripristino incorretto.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x496E636F7272656374207265636F76657279206C696E6B2E, 'Recovery link ist falsch.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x496E636F7272656374207265636F76657279206C696E6B2E, 'Неправильная ссылка востановления пароля.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x496E636F72726563742073796D626F6C27732E2028412D7A302D3929, 'Im Benutzernamen sind nur Buchstaben und Zahlen erlaubt.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x496E636F72726563742073796D626F6C27732E2028412D7A302D3929, 'Caracteres incorrectos. (A-z0-9)', 'es', 'yum');
INSERT INTO `translation` VALUES (0x496E636F72726563742073796D626F6C27732E2028412D7A302D3929, 'Pour le choix de votre nom d utilisateur seules les lettres de l alphabet et les chiffres sont acceptes .', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x496E636F72726563742073796D626F6C27732E2028412D7A302D3929, 'Sono consentiti solo lettere e numeri', 'it', 'yum');
INSERT INTO `translation` VALUES (0x496E636F72726563742073796D626F6C27732E2028412D7A302D3929, 'Im Benutzernamen sind nur Buchstaben und Zahlen erlaubt.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x496E636F72726563742073796D626F6C27732E2028412D7A302D3929, 'В имени пользователя допускаются только латинские буквы и цифры.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x496E636F727265637420757365726E616D6520286C656E677468206265747765656E203320616E642032302063686172616374657273292E, 'Falscher Benutzername (Länge zwischen 3 und 20 Zeichen).', 'de', 'yum');
INSERT INTO `translation` VALUES (0x496E636F727265637420757365726E616D6520286C656E677468206265747765656E203320616E642032302063686172616374657273292E, 'Nombre de usuario incorrecto (debe tener longitud entre 3 y 20 caracteres)', 'es', 'yum');
INSERT INTO `translation` VALUES (0x496E636F727265637420757365726E616D6520286C656E677468206265747765656E203320616E642032302063686172616374657273292E, 'Nom d utilisateur incorrect (Longueur comprise entre 3 et 20 characteres).', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x496E636F727265637420757365726E616D6520286C656E677468206265747765656E203320616E642032302063686172616374657273292E, 'Username errato (lunghezza tra i 3 e i 20 caratteri).', 'it', 'yum');
INSERT INTO `translation` VALUES (0x496E636F727265637420757365726E616D6520286C656E677468206265747765656E203320616E642032302063686172616374657273292E, 'Falscher Benutzername (Lange zwischen 3 und 20 Zeichen).', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x496E636F727265637420757365726E616D6520286C656E677468206265747765656E203320616E642032302063686172616374657273292E, 'Длина имени пользователя от 3 до 20 символов.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x496E737472756374696F6E732068617665206265656E2073656E7420746F20796F752E20506C6561736520636865636B20796F757220656D61696C2E, 'Weitere Anweisungen wurden an ihr E-Mail Postfach geschickt. Bitte prüfen Sie ihre E-Mails', 'de', 'yum');
INSERT INTO `translation` VALUES (0x496E737472756374696F6E732068617665206265656E2073656E7420746F20796F752E20506C6561736520636865636B20796F757220656D61696C2E, 'Se enviarion instrucciones a tu correo. Por favor, ve tu cuenta de correo.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x496E737472756374696F6E732068617665206265656E2073656E7420746F20796F752E20506C6561736520636865636B20796F757220656D61696C2E, 'Merci pour votre inscription.Controlez votre boite e-mail, le code d activation de votre compte vous a ete envoye par e-mail. *IMPORTANT:pour le cas ou notre e-mail ne vous serais pas parvenu, il est possible que notre e-mail ai ete filtre par votre', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x496E737472756374696F6E732068617665206265656E2073656E7420746F20796F752E20506C6561736520636865636B20796F757220656D61696C2E, 'Istruzioni inviate per email. Controlla la tua casella di posta elettronica.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x496E76616C6964207265636F76657279206B6579, 'Fehlerhafter Wiederherstellungsschlüssel', 'de', 'yum');
INSERT INTO `translation` VALUES (0x496E76616C6964207265636F76657279206B6579, 'Clave de recuperaci', 'es', 'yum');
INSERT INTO `translation` VALUES (0x496E7669746174696F6E, 'Einladung', 'de', 'yum');
INSERT INTO `translation` VALUES (0x496E7669746174696F6E, 'Invitaciones', 'es', 'yum');
INSERT INTO `translation` VALUES (0x496E7669746174696F6E, 'Invitation', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x496E7669746174696F6E, 'Invito', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4973206D656D6265727368697020706F737369626C65, 'Mitgliedschaft möglich?', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4973206D656D6265727368697020706F737369626C65, '', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4973206D656D6265727368697020706F737369626C65, 'Inscription possible?', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4973206D656D6265727368697020706F737369626C65, 'Iscrizione possibile?', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4A6F696E2067726F7570, 'Beitreten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4A6F696E2067726F7570, 'Unirse al grupo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4A6F696E2067726F7570, 'Collega al gruppo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4A756D7020746F2070726F66696C65, 'Zum Profil', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4A756D7020746F2070726F66696C65, 'Ir al perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4A756D7020746F2070726F66696C65, 'Consulter le profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4A756D7020746F2070726F66696C65, 'Vai al profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4C616E6775616765, 'Sprache', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4C616E6775616765, 'Idioma', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4C616E6775616765, '   Langue', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4C616E6775616765, 'Lingua', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4C617374204E616D65, 'Cognome', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4C617374204E616D65, 'Фамилия', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4C617374206E616D65, 'Nachname', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4C617374206E616D65, 'Apellido', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4C617374206E616D65, 'Nom de famille', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4C617374206E616D65, 'Nome', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4C617374206E616D65, 'Nachname', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4C617374207669736974, 'Letzter Besuch', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4C617374207669736974, 'òltima visita', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4C617374207669736974, 'Dernere visite', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4C617374207669736974, 'Ultima visita', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4C617374207669736974, 'Letzter Besuch', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4C617374207669736974, 'Последний визит', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4C656176652067726F7570, 'Gruppe verlassen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4C6574206D652061707065617220696E2074686520736561726368, 'Ich möchte in der Suche erscheinen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4C6574206D652061707065617220696E2074686520736561726368, 'Perm', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4C6574206D652061707065617220696E2074686520736561726368, 'Je ne souhaite pas apparaitre dans les resultats des moteurs de recherche', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4C6574206D652061707065617220696E2074686520736561726368, 'Mostrami nei risultati', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4C65742074686520757365722063686F6F736520696E20707269766163792073657474696E6773, 'Den Benutzer entscheiden lassen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4C65742074686520757365722063686F6F736520696E20707269766163792073657474696E6773, 'Permita que el usuario elija en la configuraci', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4C65742074686520757365722063686F6F736520696E20707269766163792073657474696E6773, 'Laisser l utilisateur choisir lui-meme', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4C65742074686520757365722063686F6F736520696E20707269766163792073657474696E6773, 'Consentire all\'utente di scegliere le impostazioni della privacy', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4C65747465727320617265206E6F7420636173652D73656E7369746976652E, 'Zwischen Groß-und Kleinschreibung wird nicht unterschieden.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4C65747465727320617265206E6F7420636173652D73656E7369746976652E, 'Las letras nos son sensibles a mayúsculas y minúsculas.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4C65747465727320617265206E6F7420636173652D73656E7369746976652E, 'Aucune importance ne sera apportee aux minuscules ou majuscules.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4C65747465727320617265206E6F7420636173652D73656E7369746976652E, 'La ricerca non e case sensitive.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4C65747465727320617265206E6F7420636173652D73656E7369746976652E, 'Zwischen Gro?-und Kleinschreibung wird nicht unterschieden.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4C65747465727320617265206E6F7420636173652D73656E7369746976652E, 'Регистр значение не имеет.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4C6973742050726F66696C65204669656C64, 'Lista campi Profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4C6973742050726F66696C65204669656C64, 'Список', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4C6973742055736572, 'Lista utenti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4C6973742055736572, 'Список пользователей', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4C69737420726F6C6573, 'Rollen anzeigen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4C69737420726F6C6573, 'Listar roles', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4C69737420726F6C6573, 'liste des roles', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4C69737420726F6C6573, 'Lista ruoli', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4C69737420726F6C6573, 'Lista rol', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4C6973742075736572, 'Benutzer auflisten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4C6973742075736572, 'Listar usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4C6973742075736572, 'Liste completes des membres', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4C6973742075736572, 'Lista utenti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4C6973742075736572, 'Benutzer auflisten', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4C697374207573657273, 'Benutzer anzeigen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4C697374207573657273, 'Listar usuarios', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4C697374207573657273, 'Liste des membres', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4C697374207573657273, 'Lista utenti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4C697374207573657273, 'Lista uzytkownikow', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4C6F672070726F66696C6520766973697473, 'Meine Profilbesuche anzeigen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4C6F672070726F66696C6520766973697473, 'Registrarse visitas al perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4C6F672070726F66696C6520766973697473, 'Voir les statistiques des visiteurs de mon profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4C6F672070726F66696C6520766973697473, 'Log visite profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4C6F6767656420696E206173, 'Angemeldet als', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4C6F6767656420696E206173, 'Conectado como', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4C6F6767656420696E206173, 'Connecte en tant que', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4C6F6767656420696E206173, 'Loggato come', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E, 'Anmeldung', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E, 'Iniciar sesión', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E, 'Inscription', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E, 'Entra', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E, 'Logowanie', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E, 'Вход', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E2054797065, 'Anmeldungsart', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E2054797065, 'Tipo de inicio de sesión', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E2054797065, 'Mode de connection', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E2054797065, 'Tipo login', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E2054797065, 'Rodzaj logowania', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E20616C6C6F77656420627920456D61696C20616E6420557365726E616D65, 'Anmeldung per Benutzername oder E-Mail adresse', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E20616C6C6F77656420627920456D61696C20616E6420557365726E616D65, 'Inicio de sesión por Email y Nombre de usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E20616C6C6F77656420627920456D61696C20616E6420557365726E616D65, 'Connection avec le nom d utilisateur ou adresse e-mail.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E20616C6C6F77656420627920456D61696C20616E6420557365726E616D65, 'Login con il nome utente o l\'indirizzo e-mail', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E20616C6C6F77656420627920456D61696C20616E6420557365726E616D65, 'Logowanie przez nazwe lub mejl', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E20616C6C6F776564206F6E6C7920627920456D61696C, 'Anmeldung nur per E-Mail adresse', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E20616C6C6F776564206F6E6C7920627920456D61696C, 'Inicio de sesión sólo por Email', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E20616C6C6F776564206F6E6C7920627920456D61696C, 'COnnection avec l adresse e-mail seulement', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E20616C6C6F776564206F6E6C7920627920456D61696C, 'Login solo tramite email', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E20616C6C6F776564206F6E6C7920627920456D61696C, 'Logowanie poprzez mejl', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E20616C6C6F776564206F6E6C7920627920557365726E616D65, 'Anmeldung nur per Benutzername', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E20616C6C6F776564206F6E6C7920627920557365726E616D65, 'Inicio de sesión sólo por Nombre de usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E20616C6C6F776564206F6E6C7920627920557365726E616D65, 'Connection avec le nom d utilisateur seulement', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E20616C6C6F776564206F6E6C7920627920557365726E616D65, 'Login solo tramite username', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E20616C6C6F776564206F6E6C7920627920557365726E616D65, 'Logowanie poprzez nazwe', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E206973206E6F7420706F737369626C6520776974682074686520676976656E2063726564656E7469616C73, 'Anmeldung mit den angegebenen Werten nicht möglich', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4C6F67696E206973206E6F7420706F737369626C6520776974682074686520676976656E2063726564656E7469616C73, 'Inicio de sesi', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4C6F676F7574, 'Abmelden', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4C6F676F7574, 'Cerrar sesión', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4C6F676F7574, 'Deconnection', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4C6F676F7574, 'Esci', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4C6F676F7574, 'Wyloguj', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4C6F676F7574, 'Выйти', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4C6F73742050617373776F72643F, 'Password dimenticata?', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4C6F73742050617373776F72643F, 'Забыли пароль?', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4C6F73742070617373776F72643F, 'Passwort vergessen?', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4C6F73742070617373776F72643F, '¿Olvidó la contraseña?', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4C6F73742070617373776F72643F, 'Mot de passe oublie?', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4C6F73742070617373776F72643F, 'Password dimenticata?', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4C6F73742070617373776F72643F, 'Passwort vergessen?', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4D61696C2073656E64206D6574686F64, 'Nachrichtenversandmethode', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D61696C2073656E64206D6574686F64, 'Método de envío de correo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D61696C2073656E64206D6574686F64, 'Mode d envoie des messages', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D61696C2073656E64206D6574686F64, 'Metodo invio mail', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D61696C2073656E64206D6574686F64, 'Metoda wysylania mejli', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4D616B65207B6669656C647D207075626C696320617661696C61626C65, 'Das Feld {field} öffentlich machen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D616B65207B6669656C647D207075626C696320617661696C61626C65, 'Haga {field} disponible al p', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D616B65207B6669656C647D207075626C696320617661696C61626C65, 'Rendre publique le champ {field}', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D616B65207B6669656C647D207075626C696320617661696C61626C65, 'Rendi pubblico il campo {field}', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765, 'Verwalten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765, 'Administrar', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765, 'Gestion', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765, 'Gestione', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765, 'Управление', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520416374696F6E73, 'Gestione azioni', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652050726F66696C65204669656C64, 'Profilfeld verwalten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652050726F66696C65204669656C64, 'Administrar Campos de Perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652050726F66696C65204669656C64, 'Gerer le champ de profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652050726F66696C65204669656C64, 'Gestione campi profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652050726F66696C65204669656C64, 'Zarzadzaj polem profilu', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652050726F66696C65204669656C64, 'Настройка полей', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652050726F66696C65204669656C6473, 'Profilfelder verwalten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652050726F66696C65204669656C6473, 'Administrar Campos de Perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652050726F66696C65204669656C6473, 'Gerer les champs de profils', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652050726F66696C65204669656C6473, 'Gestione campi Profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652050726F66696C65204669656C6473, 'Zarzadzaj polami profilu', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652050726F66696C65204669656C6473, 'Настройка полей', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652050726F66696C6573, 'Profile verwalten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652050726F66696C6573, 'Administrar Perfiles', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652050726F66696C6573, 'Gerer les profils', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652050726F66696C6573, 'Gestione profili', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520526F6C6573, 'Rollenverwaltung', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520526F6C6573, 'Administrar Roles', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520526F6C6573, 'Gestion des roles', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520526F6C6573, 'Gestione Ruoli', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520526F6C6573, 'Zarzadzaj rolami', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652055736572, 'Benutzerverwaltung', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652055736572, 'Administrar Usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652055736572, 'Gestion utilisateur', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652055736572, 'Gestione utente', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652055736572, 'Benutzerverwaltung', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652055736572, 'Управление пользователями', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765205573657273, 'Benutzerverwaltung', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765205573657273, 'Administrar Usuarios', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765205573657273, 'Gestion des membres', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765205573657273, 'Gestione utenti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765206669656C642067726F757073, 'Feldgruppen verwalten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765206669656C642067726F757073, 'Administrar grupos de campos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765206669656C642067726F757073, 'Gerer les champs des groupes', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765206669656C642067726F757073, 'Gestione campo gruppi', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765206669656C642067726F757073, 'Zarzadzaj grupami pol', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520667269656E6473, 'Freundschaftsverwaltung', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520667269656E6473, 'Administrar amigos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520667269656E6473, 'Gestion des contacts', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520667269656E6473, 'Gestione contatti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765206D79207573657273, 'Meine Benutzer verwalten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765206D79207573657273, 'Administrar mis usuarios', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765206D79207573657273, 'Gerer mes membres', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765206D79207573657273, 'Gestione utenti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765206D79207573657273, 'Zarzadzaj moimi uzytkownikami', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765207061796D656E7473, 'Zahlungsarten verwalten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765207061796D656E7473, 'Gesti', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765207061796D656E7473, 'Gestione pagamenti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765207065726D697373696F6E73, 'Berechtigungen verwalten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765207065726D697373696F6E73, 'Administrar los permisos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765207065726D697373696F6E73, 'Gestione permessi', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652070726F66696C65204669656C6473, 'Profilfelder verwalten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652070726F66696C65204669656C6473, 'Administrar Campos de Perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652070726F66696C65204669656C6473, 'Gerer les champs du profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652070726F66696C65204669656C6473, 'Gestione campi profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652070726F66696C65204669656C6473, 'Profilfelder verwalten', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652070726F66696C65206669656C642067726F757073, 'Administrar grupos de campos de perfiles', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652070726F66696C65206669656C642067726F757073, 'Gerer les champs des profils de grouppes', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652070726F66696C65206669656C642067726F757073, 'Gestione campo profilo gruppi', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652070726F66696C65206669656C642067726F757073, 'Zarzadzaj grupami pol w profilu', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652070726F66696C65206669656C6473, 'Profilfelder verwalten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652070726F66696C65206669656C6473, 'Gesti', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652070726F66696C65206669656C6473, 'Gerer les champs de profils', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652070726F66696C65206669656C6473, 'Gestione campi profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652070726F66696C65206669656C6473, 'Zarzadzaj polami profilu', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652070726F66696C65206669656C64732067726F757073, 'Gestione campi profilo gruppi', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652070726F66696C65206669656C64732067726F757073, 'Zarzadzaj grupami pol w profilu', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652070726F66696C6573, 'Profile verwalten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652070726F66696C6573, 'Administrar perfiles', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652070726F66696C6573, 'Gerer les profils', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652070726F66696C6573, 'Gestione profili', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D616E6167652070726F66696C6573, 'Zarzadzaj profilem', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520726F6C6573, 'Rollen verwalten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520726F6C6573, 'Adminsitrar roles', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520726F6C6573, 'Gerer les roles', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520726F6C6573, 'Gestione Ruoli', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520726F6C6573, 'Zarzadzaj rolami', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520746578742073657474696E6773, 'Texteinstellungen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520746578742073657474696E6773, 'Administrar configuración de texto', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520746578742073657474696E6773, 'Option de texte', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520746578742073657474696E6773, 'Impostazioni di testo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520746869732070726F66696C65, 'dieses Profil bearbeiten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520746869732070726F66696C65, 'Administrar este perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520746869732070726F66696C65, 'Modifier ce profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520746869732070726F66696C65, 'Modifica profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520746869732070726F66696C65, 'Zarzadzaj tym profilem', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520757365722047726F757073, 'Benutzergruppen verwalten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520757365722047726F757073, 'Administrar Grupos de usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520757365722047726F757073, 'Gerer les utilisateurs des grouppes', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D616E61676520757365722047726F757073, 'Gestine gruppi', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765207573657273, 'Benutzer verwalten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765207573657273, 'Administrar usuarios', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765207573657273, 'Gerer les membres', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765207573657273, 'Gestione utenti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D616E616765207573657273, 'Zarzadzaj uzytkownikaki', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4D616E67652050726F66696C65204669656C64, 'Mange Profil Field', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D616E67652050726F66696C65204669656C64, 'Administrar Campo del Perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D616E67652050726F66696C65204669656C64, 'Gestione campo profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D61726B2061732072656164, 'Als gelesen markieren', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D61726B2061732072656164, 'Marcar como le', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D61726B2061732072656164, 'Marquer comme lu', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D61726B2061732072656164, 'Segna come letto', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D61746368, 'Treffer', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D61746368, 'Combinar', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D61746368, 'Resultat', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D61746368, 'Corrispondenza (RegExp)', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D61746368, 'Совпадение (RegExp)', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4D656D62657273686970, 'Mitgliedschaft', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D656D62657273686970, 'Membres', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D656D62657273686970, 'Devenir membre', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D656D62657273686970, 'Iscrizione', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D656D6265727368697020656E64732061743A207B646174657D, 'Mitgliedschaft endet am: {date}', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D656D6265727368697020656E64732061743A207B646174657D, 'Membres', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D656D6265727368697020656E64732061743A207B646174657D, 'Iscrizione termina il: {date}', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D656D6265727368697020686173206E6F74206265656E20706179656420796574, 'Zahlungseingang noch nicht erfolgt', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D656D6265727368697020686173206E6F74206265656E20706179656420796574, 'La membres', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D656D6265727368697020686173206E6F74206265656E20706179656420796574, 'Iscrizione non pagata', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D656D626572736869702070617965642061743A207B646174657D, 'Zahlungseingang erfolgt am: {date}', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D656D626572736869702070617965642061743A207B646174657D, 'Membres', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D656D626572736869702070617965642061743A207B646174657D, 'Iscrizione pagata il: {date}', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D656D6265727368697073, 'Mitgliedschaften', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D656D6265727368697073, 'Membres', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D656D6265727368697073, 'Iscrizioni', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D657373616765, 'Nachricht', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D657373616765, 'Mensaje', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D657373616765, 'Message', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D657373616765, 'Messaggio', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D65737361676520227B6D6573736167657D2220686173206265656E2073656E7420746F207B746F7D, 'Nachricht \"{message}\" wurde an {to} gesendet', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D65737361676520227B6D6573736167657D2220686173206265656E2073656E7420746F207B746F7D, 'Mensaje \"{message}\" ha sido enviada a {to}', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D65737361676520227B6D6573736167657D2220776173206D61726B65642061732072656164, 'Nachricht \"{message}\" wurde als gelesen markiert.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D65737361676520227B6D6573736167657D2220776173206D61726B65642061732072656164, 'Mensaje \"{message}\" se ha marcado como le', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D657373616765202671756F743B7B6D6573736167657D2671756F743B20686173206265656E2073656E7420746F207B746F7D, 'Message \"{message}\" a ete envoye {to}', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D657373616765202671756F743B7B6D6573736167657D2671756F743B20686173206265656E2073656E7420746F207B746F7D, 'Messaggio \"{message}\" e stato inviato a {to}', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D657373616765202671756F743B7B6D6573736167657D2671756F743B20776173206D61726B65642061732072656164, 'Message \"{message}\" marquer comme lu.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D657373616765202671756F743B7B6D6573736167657D2671756F743B20776173206D61726B65642061732072656164, 'Messaggio \"{message}\" e stato contrassegnato come letto.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D65737361676520636F756E74, 'Anzahl Nachrichten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D65737361676520636F756E74, 'Recuento de mensajes', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D6573736167652066726F6D, 'Nachricht von', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D6573736167652066726F6D, 'Mensaje del', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D6573736167652066726F6D, 'Message de', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D6573736167652066726F6D, 'Messaggio da', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D6573736167652066726F6D, 'Nachricht von', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4D6573736167652066726F6D20, 'Nachricht von ', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D6573736167652066726F6D20, 'Mensaje de', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D65737361676573, 'Nachrichten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D65737361676573, 'Mensajes', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D65737361676573, 'Message', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D65737361676573, 'Messagi', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D65737361676573, 'Wiadomosci', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4D6573736167696E672073797374656D, 'Nachrichtensystem', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D6573736167696E672073797374656D, 'Sistema de mensajes', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D6573736167696E672073797374656D, 'Message-Board', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D6573736167696E672073797374656D, 'Sistema messaggistica', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D6573736167696E672073797374656D, 'System wiadomosci', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4D696E696D616C2070617373776F7264206C656E67746820342073796D626F6C732E, 'Minimale Länge des Passworts 4 Zeichen.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D696E696D616C2070617373776F7264206C656E67746820342073796D626F6C732E, 'Mínimo 4 caracteres para la contraseña', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D696E696D616C2070617373776F7264206C656E67746820342073796D626F6C732E, 'La longueur de votre mot de passe doit comporter au moins quatre characteres.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D696E696D616C2070617373776F7264206C656E67746820342073796D626F6C732E, 'Lunghezza minima password di 4 caratteri.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D696E696D616C2070617373776F7264206C656E67746820342073796D626F6C732E, 'Minimale Lange des Passworts 4 Zeichen.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4D696E696D616C2070617373776F7264206C656E67746820342073796D626F6C732E, 'Минимальная длина пароля 4 символа.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4D6F64756C652073657474696E6773, 'Moduleinstellungen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D6F64756C652073657474696E6773, 'Ajustes del módulo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D6F64756C652073657474696E6773, 'Reglage des modules', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D6F64756C652073657474696E6773, 'Opzioni modulo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D6F64756C652073657474696E6773, 'Ustawienia modulu', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4D6F64756C6520746578742073657474696E6773, 'Ajustes de texto del módulo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D6F64756C6520746578742073657474696E6773, 'Opzioni testo modulo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D6F64756C6520746578742073657474696E6773, 'Ustawienia tekstow modulu', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4D7920496E626F78, 'Posteingang', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D7920496E626F78, 'Mi bandeja de entrada', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D7920496E626F78, 'Boite e-mail', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D7920496E626F78, 'Moja skrzynka odbiorcza', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4D7920667269656E6473, 'Meine Kontakte', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D7920667269656E6473, 'Mis amigos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D7920667269656E6473, 'Mes contact', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D7920667269656E6473, 'Contatti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D792067726F757073, 'Meine Gruppen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D792067726F757073, 'Mis grupos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D792067726F757073, 'Mes grouppes', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D792067726F757073, 'Gruppi', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D7920696E626F78, 'Mein Posteingang', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D7920696E626F78, 'Mi bandeja de entrada', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D7920696E626F78, 'Ma boite e-mail', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D7920696E626F78, 'Posta in arrivo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D79206D656D6265727368697073, 'Meine Mitgliedschaften', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D79206D656D6265727368697073, 'Mis membres', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D79206D656D6265727368697073, 'Options de mon compte', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D79206D656D6265727368697073, 'Iscrizioni', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4D792070726F66696C65, 'Mein Profil', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4D792070726F66696C65, 'Mi perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4D792070726F66696C65, 'Mon profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4D792070726F66696C65, 'Profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4E657720667269656E64736869702072657175657374, 'nueva solicitud de amistad', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4E657720667269656E647368697020726571756573742066726F6D207B757365726E616D657D, 'neue Kontaktanfrage von {username}', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4E657720667269656E647368697020726571756573742066726F6D207B757365726E616D657D, 'Nueva solicitud de amistad de {username}', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4E657720667269656E647368697020726571756573742066726F6D207B757365726E616D657D, 'Nouvelle demande de contact de {username}', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4E657720667269656E647368697020726571756573742066726F6D207B757365726E616D657D, 'Nuova richiesta di contatto da {username}', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4E657720667269656E6473686970207265717565737473, 'Neue Freundschaftsanfragen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4E657720667269656E6473686970207265717565737473, 'Nueva solicitud de amistad', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4E657720667269656E6473686970207265717565737473, 'Nouvelle demande de contact', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4E657720667269656E6473686970207265717565737473, 'Nuova richiesta contatto', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4E6577206D6573736167652066726F6D207B66726F6D7D3A207B7375626A6563747D, 'Neue Nachricht von {from}: {subject}', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4E6577206D65737361676573, 'Neue Nachrichten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4E6577206D65737361676573, 'Nuevos mensajes', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4E6577206D65737361676573, 'Nouveaux messages', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4E6577206D65737361676573, 'Nuovo messaggio', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4E65772070617373776F7264, 'Neues Passwort', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4E65772070617373776F7264, 'Nueva contrase', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4E65772070617373776F7264, 'Nouveau mot de passe', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4E65772070617373776F7264, 'Nuovo Password', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4E65772070617373776F72642069732073617665642E, 'Neues Passwort wird gespeichert.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4E65772070617373776F72642069732073617665642E, 'La contraseña nueva ha sido guardada', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4E65772070617373776F72642069732073617665642E, 'Votre nouveau mot de passe a bien ete memorise.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4E65772070617373776F72642069732073617665642E, 'Nuova passowrd salvata', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4E65772070617373776F72642069732073617665642E, 'Neues Passwort wird gespeichert.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4E65772070617373776F72642069732073617665642E, 'Новый пароль сохранен.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4E65772070726F66696C6520636F6D6D656E74, 'Nuevo comentario de perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4E65772070726F66696C6520636F6D6D656E742066726F6D207B757365726E616D657D, 'Neuer Profilkommentar von {username}', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4E65772070726F66696C6520636F6D6D656E742066726F6D207B757365726E616D657D, 'Comentario nuevo tu perfil de {username}', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4E65772070726F66696C6520636F6D6D656E742066726F6D207B757365726E616D657D, 'Nouveau commentaire pour le profil de {username}', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4E65772070726F66696C6520636F6D6D656E742066726F6D207B757365726E616D657D, 'Nuovo commento per il profilo {username}', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4E65772073657474696E67732070726F66696C65, 'Neues Einstellungsprofil', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4E65772073657474696E67732070726F66696C65, 'Nuevos ajustes de perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4E65772073657474696E67732070726F66696C65, 'Nouvelle configuration de profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4E65772073657474696E67732070726F66696C65, 'Nuova preferenze profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4E65772073657474696E67732070726F66696C65, 'Nowe ustawienia profilu', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4E6577207472616E736C6174696F6E, 'Neue Übersetzung', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4E6577207472616E736C6174696F6E, 'Nueva traducción', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4E65772076616C7565, 'Neuer Wert', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4E65772076616C7565, 'Valor nuevo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4E65772076616C7565, 'Nouvelle valeur', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4E65772076616C7565, 'Nuovo valore', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4E65772076616C7565, 'Nowa wartosc', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4E6F, 'Nein', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4E6F, 'No', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4E6F, 'Non', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4E6F, 'No', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4E6F, 'Nein', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4E6F, 'Нет', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4E6F20667269656E647368697020726571756573746564, 'Keine Freundschaft angefragt', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4E6F20667269656E647368697020726571756573746564, 'No hay solicitud de amistad', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4E6F20667269656E647368697020726571756573746564, 'Pas de demande de contact', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4E6F20667269656E647368697020726571756573746564, 'Contatto non richiesto', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4E6F206E6577206D65737361676573, 'Keine neuen Nachrichten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4E6F206E6577206D65737361676573, 'No hay mensajes nuevos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4E6F206E6577206D65737361676573, 'Pas de nouveaux messages', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4E6F206E6577206D65737361676573, 'Nessun nuovo messaggio', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4E6F2070726F66696C65206368616E6765732077657265206D616465, 'Keine Profiländerungen stattgefunden', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4E6F2070726F66696C65206368616E6765732077657265206D616465, 'No se hicieron cambios en el perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4E6F2070726F66696C65206368616E6765732077657265206D616465, 'pas de resultat pour les profils modifies', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4E6F2070726F66696C65206368616E6765732077657265206D616465, 'Nessun cambiamento al profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4E6F2070726F66696C65206368616E6765732077657265206D616465, 'Nie dokonano zmian w profilu', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4E6F2C206275742073686F77206F6E20726567697374726174696F6E20666F726D, 'Ja, und auf Registrierungsseite anzeigen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4E6F2C206275742073686F77206F6E20726567697374726174696F6E20666F726D, 'No, pero mostrar en formulario de registro', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4E6F2C206275742073686F77206F6E20726567697374726174696F6E20666F726D, 'non et charger le formulaire d inscription', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4E6F2C206275742073686F77206F6E20726567697374726174696F6E20666F726D, 'No, ma mostra nel form di registrazione', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4E6F2C206275742073686F77206F6E20726567697374726174696F6E20666F726D, 'Nie, ale pokaz w formularzu rejestracji', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4E6F2C206275742073686F77206F6E20726567697374726174696F6E20666F726D, 'Нет, но показать при регистрации', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4E6F626F64792068617320636F6D6D656E74656420796F75722070726F66696C6520796574, 'Bisher hat niemand mein Profil kommentiert', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4E6F626F64792068617320636F6D6D656E74656420796F75722070726F66696C6520796574, 'Nadie ha comentado el perfil a', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4E6F626F64792068617320636F6D6D656E74656420796F75722070726F66696C6520796574, 'Aucun commentaire pour votre profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4E6F626F64792068617320636F6D6D656E74656420796F75722070726F66696C6520796574, 'Nessuno ha commentato il tuo profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4E6F626F647920686173207669736974656420796F75722070726F66696C6520796574, 'Bisher hat noch niemand ihr Profil angesehen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4E6F626F647920686173207669736974656420796F75722070726F66696C6520796574, 'Nadie ha visitado tu perfil todavía', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4E6F626F647920686173207669736974656420796F75722070726F66696C6520796574, 'Aucune visite recente de votre profil.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4E6F626F647920686173207669736974656420796F75722070726F66696C6520796574, 'Fino ad ora nessuno ha visto il tuo profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4E6F6E65, 'Keine', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4E6F6E65, 'Ninguno', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4E6F6E65, 'Aucun', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4E6F6E65, 'Nessuno', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4E6F6E65, 'Zaden', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4E6F7420616374697665, 'Nicht aktiv', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4E6F7420616374697665, 'Innactivo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4E6F7420616374697665, 'Non actif', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4E6F7420616374697665, 'Non attivo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4E6F7420616374697665, 'Nicht aktiv', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4E6F7420616374697665, 'Не активирован', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4E6F742061737369676E6564, 'Nicht zugewiesen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4E6F742061737369676E6564, 'No asignado', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4E6F742061737369676E6564, 'Non assigne', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4E6F742061737369676E6564, 'Non assegnato', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4E6F742061737369676E6564, 'Nie przypisano', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4E6F742076697369746564, 'Non visitato', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4E6F7420796574207061796564, 'Noch nicht bezahlt', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4E6F7420796574207061796564, 'Todav', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4F6B, 'Ok', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4F6B, 'Aceptar', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4F6B, 'Ok', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4F6B, 'Ok', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4F6B, 'Ok', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4F6B, 'Ok', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4F6C642076616C7565, 'Alter Wert', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4F6C642076616C7565, 'Valor antiguo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4F6C642076616C7565, 'Ancienne valeur', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4F6C642076616C7565, 'Vecchio valore', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4F6C642076616C7565, 'Stara wartosc', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4F6E65206F662074686520726563697069656E747320287B757365726E616D657D29206861732069676E6F72656420796F752E204D6573736167652077696C6C206E6F742062652073656E7421, 'Einer der gewählten Benutzer ({username}) hat Sie auf seiner Ignorier-Liste. Die Nachricht wird nicht gesendet!', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4F6E65206F662074686520726563697069656E747320287B757365726E616D657D29206861732069676E6F72656420796F752E204D6573736167652077696C6C206E6F742062652073656E7421, 'Uno de los destinatarios ({username}) te ha ignorado. ¡No se enviará el mensaje!', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4F6E65206F662074686520726563697069656E747320287B757365726E616D657D29206861732069676E6F72656420796F752E204D6573736167652077696C6C206E6F742062652073656E7421, 'Un des membres selectionne vous a mis sur sa liste noire ({username}). Ce message ne sera pas envoye!', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4F6E65206F662074686520726563697069656E747320287B757365726E616D657D29206861732069676E6F72656420796F752E204D6573736167652077696C6C206E6F742062652073656E7421, 'Un destinatario ({username}) ti ha inserito nella lista degli ignorati. Messaggio non inviato!', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4F6E6C79206F776E6572, 'Nur Besitzer', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4F6E6C79206F776E6572, 'Sólo el dueño', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4F6E6C79206F776E6572, 'Proprietaire seulement', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4F6E6C79206F776E6572, 'Solo proprietario', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4F6E6C79206F776E6572, 'Только владелец', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x4F6E6C7920796F757220667269656E6473206172652073686F776E2068657265, 'Nur ihre Kontakte werden hier angezeigt', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4F6E6C7920796F757220667269656E6473206172652073686F776E2068657265, 'S', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4F6E6C7920796F757220667269656E6473206172652073686F776E2068657265, 'Seuls vos contacts seront visibles ici', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4F6E6C7920796F757220667269656E6473206172652073686F776E2068657265, 'Solo i tuoi contatti verranno visualizzati', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4F7264657220636F6E6669726D6564, 'Bestellbestätigung', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4F7264657220636F6E6669726D6564, 'Orden confirmada', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4F7264657220636F6E6669726D6564, 'Ordini confermati', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4F726465722064617465, 'Bestelldatum', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4F726465722064617465, 'Fecha de pedido', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4F726465722064617465, 'Data ordine', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4F72646572206D656D62657273686970, 'Mitgliedschaft bestellen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4F72646572206D656D62657273686970, 'Pedido de miembro', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4F72646572206D656D62657273686970, 'Ordine iscrizione', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4F72646572206E756D626572, 'Bestellnummer', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4F72646572206E756D626572, 'N', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4F72646572206E756D626572, 'Numero ordine', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4F726465726564206174, 'Bestellt am', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4F726465726564206174, 'Pedido en', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4F726465726564206174, 'Ordinato il', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4F726465726564206D656D6265727368697073, 'Bestellte Mitgliedschaften', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4F726465726564206D656D6265727368697073, 'Pedido de membres', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4F726465726564206D656D6265727368697073, 'Options complementaires', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4F726465726564206D656D6265727368697073, 'Iscrizioni ordinate', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4F74686572, 'Verschiedenes', 'de', 'yum');
INSERT INTO `translation` VALUES (0x4F74686572, 'Otro', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4F74686572, 'Divers', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4F74686572, 'Altro', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4F74686572, 'Pozostale', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x4F746865722056616C696461746F72, 'Otro validador', 'es', 'yum');
INSERT INTO `translation` VALUES (0x4F746865722056616C696461746F72, 'Autre validation', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x4F746865722056616C696461746F72, 'Altro validatore', 'it', 'yum');
INSERT INTO `translation` VALUES (0x4F746865722056616C696461746F72, 'Другой валидатор', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x5061727469636970616E7420636F756E74, 'Anzahl Teilnehmer', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5061727469636970616E7420636F756E74, 'N', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5061727469636970616E7473, 'Teilnehmer', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5061727469636970616E7473, 'Participantes', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5061727469636970616E7473, 'Partecipanti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x50617373776F7264, 'Passwort', 'de', 'yum');
INSERT INTO `translation` VALUES (0x50617373776F7264, 'Contraseña', 'es', 'yum');
INSERT INTO `translation` VALUES (0x50617373776F7264, 'Passwort', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x50617373776F7264, 'Password', 'it', 'yum');
INSERT INTO `translation` VALUES (0x50617373776F7264, 'Haslo', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x50617373776F72642045787069726174696F6E2054696D65, 'Ablaufzeit von Passwörtern', 'de', 'yum');
INSERT INTO `translation` VALUES (0x50617373776F72642045787069726174696F6E2054696D65, 'Tiempo de expiración de la contraseña', 'es', 'yum');
INSERT INTO `translation` VALUES (0x50617373776F72642045787069726174696F6E2054696D65, 'Duree de vie des mot de passe', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x50617373776F72642045787069726174696F6E2054696D65, 'Scadenza password', 'it', 'yum');
INSERT INTO `translation` VALUES (0x50617373776F72642045787069726174696F6E2054696D65, 'Czas waznosci hasla', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x50617373776F726420697320696E636F72726563742E, 'Passwort ist falsch.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x50617373776F726420697320696E636F72726563742E, 'Contraseña incorrecta', 'es', 'yum');
INSERT INTO `translation` VALUES (0x50617373776F726420697320696E636F72726563742E, 'Le mot de passe est incorrect.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x50617373776F726420697320696E636F72726563742E, 'Password incorretta', 'it', 'yum');
INSERT INTO `translation` VALUES (0x50617373776F726420697320696E636F72726563742E, 'Niepoprawne haslo.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x50617373776F726420697320696E636F72726563742E, 'Неверный пароль.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x50617373776F7264207265636F76657279, 'Passwort wiederherstellen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x50617373776F7264207265636F76657279, 'Recuperación de contraseña', 'es', 'yum');
INSERT INTO `translation` VALUES (0x50617373776F72647320646F206E6F74206D61746368, 'Las contraseñas no coinciden', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5061796D656E74, 'Zahlungsmethode', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5061796D656E74, 'Pago', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5061796D656E74, 'Pagamento', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5061796D656E742061727269766564, 'Zahlungseingang bestätigt', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5061796D656E742061727269766564, 'El pago lleg', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5061796D656E742061727269766564, 'Pagamento arrivato', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5061796D656E742064617465, 'Bezahlt am', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5061796D656E742064617465, 'Fecha de pago', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5061796D656E742064617465, 'Data pagamento', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5061796D656E742074797065, 'Zahlungstyp', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5061796D656E74207479706573, 'Zahlungsarten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5061796D656E74207479706573, 'Formas de pago', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5061796D656E74207479706573, 'Options de paiement', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5061796D656E74207479706573, 'Tipi pagamento', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5061796D656E7473, 'Zahlungsarten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5061796D656E7473, 'Pagos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5061796D656E7473, 'Pagamenti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5065726D697373696F6E73, 'Berechtigungen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5065726D697373696F6E73, 'Permisos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5065726D697373696F6E73, 'Permissions', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5065726D697373696F6E73, 'Autorizzazioni', 'it', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520616374697661746520796F75206163636F756E7420676F20746F207B61637469766174696F6E5F75726C7D, 'Perfavore attiva il tuo accounto all\'indirizzo {activation_url}', 'it', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520636865636B20796F757220656D61696C2E20416E20696E737472756374696F6E73207761732073656E7420746F20796F757220656D61696C20616464726573732E, 'Bitte überprüfen Sie Ihre E-Mails. Eine Anleitung wurde an Ihre E-Mail-Adresse geschickt.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520636865636B20796F757220656D61696C2E20416E20696E737472756374696F6E73207761732073656E7420746F20796F757220656D61696C20616464726573732E, 'Por favor verifica tu e-mail a donde se han enviado más instrucciones.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520636865636B20796F757220656D61696C2E20416E20696E737472756374696F6E73207761732073656E7420746F20796F757220656D61696C20616464726573732E, 'Controlez votre boite e-mail, d autres instructions vous ont ete envoyees par e-mail. *IMPORTANT:pour le cas ou notre e-mail ne vous serais pas parvenu, il est possible que notre e-mail ai ete filtre par votre fournisseur  d acces internet et plac?', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520636865636B20796F757220656D61696C2E20416E20696E737472756374696F6E73207761732073656E7420746F20796F757220656D61696C20616464726573732E, 'Perfavore controlla la tua email con le istruzioni che ti abbiamo inviato', 'it', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520636865636B20796F757220656D61696C2E20416E20696E737472756374696F6E73207761732073656E7420746F20796F757220656D61696C20616464726573732E, 'Bitte uberprufen Sie Ihre E-Mails. Eine Anleitung wurde an Ihre E-Mail-Adresse geschickt.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520636865636B20796F757220656D61696C2E20416E20696E737472756374696F6E73207761732073656E7420746F20796F757220656D61696C20616464726573732E, 'На ваш адрес электронной почты было отправлено письмо с инструкциями.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520636865636B20796F757220656D61696C2E20496E737472756374696F6E732068617665206265656E2073656E7420746F20796F757220656D61696C20616464726573732E, 'Bitte schauen Sie in Ihr Postfach. Weitere Anweisungen wurden per E-Mail geschickt.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520636865636B20796F757220656D61696C2E20496E737472756374696F6E732068617665206265656E2073656E7420746F20796F757220656D61696C20616464726573732E, 'Por favor revisa tu e-mail. Hemos enviado intrusiones a tu dirección de e-mail.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520636865636B20796F757220656D61696C2E20496E737472756374696F6E732068617665206265656E2073656E7420746F20796F757220656D61696C20616464726573732E, 'Controlez votre boite e-mail. D autres instructions vous ont ete envoyees par e-mail. *IMPORTANT:pour le cas ou notre e-mail ne vous serais pas parvenu, il est possible que notre e-mail ai ete filtre par votre fournisseur  d acces internet et plac?', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520636865636B20796F757220656D61696C2E20496E737472756374696F6E732068617665206265656E2073656E7420746F20796F757220656D61696C20616464726573732E, 'Si prega di controllare la casella di posta. Ulteriori istruzioni sono state inviate via e-mail.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520636865636B20796F757220656D61696C2E20496E737472756374696F6E732068617665206265656E2073656E7420746F20796F757220656D61696C20616464726573732E, 'Prosze sprawdz Twoj mejl. Instrukcje zostaly wyslane na Twoj adres mejlowy.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520656E74657220612072657175657374204D65737361676520757020746F203235352063686172616374657273, 'Bitte geben Sie eine Nachricht bis zu 255 Zeichen an, die dem Benutzer bei der Kontaktanfrage mitgegeben wird', 'de', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520656E74657220612072657175657374204D65737361676520757020746F203235352063686172616374657273, 'Por favor escribe un mensaje no mayor a 255 caracteres', 'es', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520656E74657220612072657175657374204D65737361676520757020746F203235352063686172616374657273, 'Vous pouvez ajouter un message personalise de 255 characteres a votre demande de contact', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520656E74657220612072657175657374204D65737361676520757020746F203235352063686172616374657273, 'Perfavore inserisci un messaggio di richiesta di massimo 255 caratteri', 'it', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520656E74657220746865206C6574746572732061732074686579206172652073686F776E20696E2074686520696D6167652061626F76652E, 'Bitte geben Sie die, oben im Bild angezeigten, Buchstaben ein.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520656E74657220746865206C6574746572732061732074686579206172652073686F776E20696E2074686520696D6167652061626F76652E, 'Por favor escribe las letras que se muestran en la imagen.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520656E74657220746865206C6574746572732061732074686579206172652073686F776E20696E2074686520696D6167652061626F76652E, 'Recopiez les characteres apparaissant dans l image au dessus.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520656E74657220746865206C6574746572732061732074686579206172652073686F776E20696E2074686520696D6167652061626F76652E, 'Perfavore inserire le lettere mostrate nella seguente immagine.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520656E74657220746865206C6574746572732061732074686579206172652073686F776E20696E2074686520696D6167652061626F76652E, 'Bitte geben Sie die, oben im Bild angezeigten, Buchstaben ein.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520656E74657220746865206C6574746572732061732074686579206172652073686F776E20696E2074686520696D6167652061626F76652E, 'Пожалуйста, введите буквы, показанные на картинке выше.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520656E74657220796F7572206C6F67696E206F7220656D61696C206164647265732E, 'Perfavore inserisci il tuo username o l\'indirizzo mail.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520656E74657220796F7572206C6F67696E206F7220656D61696C206164647265732E, 'Пожалуйста, введите ваш логин или адрес электронной почты.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520656E74657220796F7572206C6F67696E206F7220656D61696C20616464726573732E, 'Bitte geben Sie Ihren Benutzernamen oder E-Mail-Adresse ein.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520656E74657220796F7572206C6F67696E206F7220656D61696C20616464726573732E, 'Por favor escribe tu nombre de usuario o dirección de e-mail.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520656E74657220796F7572206C6F67696E206F7220656D61696C20616464726573732E, 'Indiquez dans ce champ, votre nom d utilisateur ou votre adresse e-mail.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520656E74657220796F7572206C6F67696E206F7220656D61696C20616464726573732E, 'Inserisci il tuo nome utente o indirizzo e-mail.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520656E74657220796F7572206C6F67696E206F7220656D61696C20616464726573732E, 'Bitte geben Sie Ihren Benutzernamen oder E-Mail-Adresse ein.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520656E74657220796F75722070617373776F726420746F20636F6E6669726D2064656C6574696F6E3A, 'Bitte geben Sie Ihr Passwort ein, um den Löschvorgang zu bestätigen:', 'de', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520656E74657220796F75722070617373776F726420746F20636F6E6669726D2064656C6574696F6E3A, 'Por favor escribe tu contraseña para confirmar la eliminación:', 'es', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520656E74657220796F75722070617373776F726420746F20636F6E6669726D2064656C6574696F6E3A, 'Renseignez votre mot de passe, pour confirmer la suppression:', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520656E74657220796F75722070617373776F726420746F20636F6E6669726D2064656C6574696F6E3A, 'Si prega di inserire la password per confermare l\'eliminazione:', 'it', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520656E74657220796F75722070617373776F726420746F20636F6E6669726D2064656C6574696F6E3A, 'Prosze wprowadz swoje haslo w celu potwierdzenia usuwania:', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520656E74657220796F75722075736572206E616D65206F7220656D61696C20616464726573732E, 'Bitte geben Sie Ihren Benutzernamen oder E-mail Adresse ein', 'de', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520656E74657220796F75722075736572206E616D65206F7220656D61696C20616464726573732E, 'Por favor, ingrese su nombre de usuario o direcci', 'es', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520656E74657220796F75722075736572206E616D65206F7220656D61696C20616464726573732E, 'Renseignez votre nom d utilisateur ou votre adresse e-mail', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x506C6561736520656E74657220796F75722075736572206E616D65206F7220656D61696C20616464726573732E, 'Inserisci il tuo nome utente o indirizzo e-mail', 'it', 'yum');
INSERT INTO `translation` VALUES (0x506C656173652066696C6C206F75742074686520666F6C6C6F77696E6720666F726D207769746820796F7572206C6F67696E2063726564656E7469616C733A, 'Bitte geben Sie ihre Login-Daten ein:', 'de', 'yum');
INSERT INTO `translation` VALUES (0x506C656173652066696C6C206F75742074686520666F6C6C6F77696E6720666F726D207769746820796F7572206C6F67696E2063726564656E7469616C733A, 'Por favor llena el formulario con tu información de inicio de sesión:', 'es', 'yum');
INSERT INTO `translation` VALUES (0x506C656173652066696C6C206F75742074686520666F6C6C6F77696E6720666F726D207769746820796F7572206C6F67696E2063726564656E7469616C733A, 'Entrez dans le champ vos donnees de connection:', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x506C656173652066696C6C206F75742074686520666F6C6C6F77696E6720666F726D207769746820796F7572206C6F67696E2063726564656E7469616C733A, 'Perfavore inserisci le tue credenziali d\'accesso:', 'it', 'yum');
INSERT INTO `translation` VALUES (0x506C656173652066696C6C206F75742074686520666F6C6C6F77696E6720666F726D207769746820796F7572206C6F67696E2063726564656E7469616C733A, 'Bitte geben Sie ihre Login-Daten ein:', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x506C656173652066696C6C206F75742074686520666F6C6C6F77696E6720666F726D207769746820796F7572206C6F67696E2063726564656E7469616C733A, 'Пожалуйста, заполните следующую форму с вашими Логин и паролем:', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x506C65617365206C6F6720696E20696E746F20746865206170706C69636174696F6E2E, 'Por favor, entra a la aplicación', 'es', 'yum');
INSERT INTO `translation` VALUES (0x506C656173652076657269667920796F757220452D4D61696C2061646472657373, 'Por favor verifica tu dirección de correo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x506F736974696F6E, 'Position', 'de', 'yum');
INSERT INTO `translation` VALUES (0x506F736974696F6E, 'Posición', 'es', 'yum');
INSERT INTO `translation` VALUES (0x506F736974696F6E, 'Position', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x506F736974696F6E, 'Posizioe', 'it', 'yum');
INSERT INTO `translation` VALUES (0x506F736974696F6E, 'Позиция', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x507265646566696E65642076616C75657320286578616D706C653A20312C20322C20332C20342C20353B292E, 'Vordefinierter Bereich (z.B. 1, 2, 3, 4, 5),', 'de', 'yum');
INSERT INTO `translation` VALUES (0x507265646566696E65642076616C75657320286578616D706C653A20312C20322C20332C20342C20353B292E, 'Valores predefinidos (ejemplo: 1,2,3,4,5;).', 'es', 'yum');
INSERT INTO `translation` VALUES (0x507265646566696E65642076616C75657320286578616D706C653A20312C20322C20332C20342C20353B292E, 'Valeur predefinie (z.B. 1, 2, 3, 4, 5),', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x507265646566696E65642076616C75657320286578616D706C653A20312C20322C20332C20342C20353B292E, 'Valori predefiniti (es. 1, 2, 3, 4, 5),', 'it', 'yum');
INSERT INTO `translation` VALUES (0x507265646566696E65642076616C75657320286578616D706C653A20312C20322C20332C20342C20353B292E, 'Предопределенные значения (пример: 1;2;3;4;5;).', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x507265736576652050726F66696C6573, 'Profile aufbewahren', 'de', 'yum');
INSERT INTO `translation` VALUES (0x507265736576652050726F66696C6573, 'Preservar Perfiles', 'es', 'yum');
INSERT INTO `translation` VALUES (0x507265736576652050726F66696C6573, 'Profile aufbewahren ???', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x507265736576652050726F66696C6573, 'Mantieni profili', 'it', 'yum');
INSERT INTO `translation` VALUES (0x507265736576652050726F66696C6573, 'Zachowaj profil', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x5072696365, 'Preis', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5072696365, 'Precio', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5072696365, 'Prix', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5072696365, 'Prezzo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x50726976616379, 'Privatsphäre', 'de', 'yum');
INSERT INTO `translation` VALUES (0x50726976616379, 'Privacidad', 'es', 'yum');
INSERT INTO `translation` VALUES (0x50726976616379, 'Donnees privees', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x50726976616379, 'Privacy', 'it', 'yum');
INSERT INTO `translation` VALUES (0x50726976616379, 'Privatsphare', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x507269766163792073657474696E6773, 'Privatsphäre', 'de', 'yum');
INSERT INTO `translation` VALUES (0x507269766163792073657474696E6773, 'Configuración de Privacidad', 'es', 'yum');
INSERT INTO `translation` VALUES (0x507269766163792073657474696E6773, 'Vos donnees personnelles', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x507269766163792073657474696E6773, 'Privacy', 'it', 'yum');
INSERT INTO `translation` VALUES (0x507269766163792073657474696E677320666F72207B757365726E616D657D, 'Privatsphäreneinstellungen für {username}', 'de', 'yum');
INSERT INTO `translation` VALUES (0x507269766163792073657474696E677320666F72207B757365726E616D657D, 'Configuración de Privacidad para {username}', 'es', 'yum');
INSERT INTO `translation` VALUES (0x507269766163792073657474696E677320666F72207B757365726E616D657D, 'Configuration des donnees privees pour {username}', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x507269766163792073657474696E677320666F72207B757365726E616D657D, 'Opzioni Privacy per {username}', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5072697661637973657474696E6773, 'Privatsphäre', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5072697661637973657474696E6773, 'Configuración de Privacidad', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5072697661637973657474696E6773, 'Donnees privees', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5072697661637973657474696E6773, 'Opzioni privacy', 'it', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65, 'Profil', 'de', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65, 'Perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65, 'Profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65, 'Profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65, 'Profil', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65, 'Профиль', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C6520436F6D6D656E7473, 'Pinnwand', 'de', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C6520436F6D6D656E7473, 'COmentarios de perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C6520436F6D6D656E7473, 'Pinnwand', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C6520436F6D6D656E7473, 'Commenti profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65204669656C6473, 'Profilfelder', 'de', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65204669656C6473, 'Campos de Perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65204669656C6473, 'Champs des profils', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65204669656C6473, 'Campi profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65204669656C6473, 'Pola profilu', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65204669656C6473, 'Поля профиля', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206669656C642067726F757073, 'Profilfeldgruppen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206669656C642067726F757073, 'Grupos de campos de perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206669656C642067726F757073, 'Champs des profils de groupes.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206669656C642067726F757073, 'Campo profilo gruppi', 'it', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206669656C64207075626C6963206F7074696F6E73, 'Einstellungen der Profilfelder', 'de', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206669656C64207075626C6963206F7074696F6E73, 'Opciones de campo de perfil p', 'es', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206669656C64207075626C6963206F7074696F6E73, 'Configuration des champs publique du profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206669656C64207075626C6963206F7074696F6E73, 'Opzioni pubbliche campi profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206669656C64207B6669656C646E616D657D, 'Profilfeld {fieldname}', 'de', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206669656C64207B6669656C646E616D657D, 'Campo de perfil {fieldname}', 'es', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206669656C64207B6669656C646E616D657D, 'Camp de profil {fieldname}', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206669656C64207B6669656C646E616D657D, '{fieldname} campo profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206669656C64207B6669656C646E616D657D, 'Pole profilu {fieldname}', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206669656C6473, 'Profilfeldverwaltung', 'de', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206669656C6473, 'Campos de perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206669656C6473, 'Gestion des champs de profils', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206669656C6473, 'Campi profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206669656C6473, 'Pole profilu', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206669656C64732067726F757073, 'Profilfeldgruppen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206669656C64732067726F757073, 'Grupos de campos de perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206669656C64732067726F757073, 'Champ des profils de groupes', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206669656C64732067726F757073, 'Campi profilo gruppi', 'it', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206669656C64732067726F757073, 'Grupy pol w profilu', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C6520686973746F7279, 'Profilverlauf', 'de', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C6520686973746F7279, 'Historial del perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C6520686973746F7279, 'Chronique du profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C6520686973746F7279, 'Storico profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C6520686973746F7279, 'Historia profilu', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206E756D626572, 'Profilnummer: ', 'de', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206E756D626572, 'Número de perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206E756D626572, 'Numero du profil:', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206E756D626572, 'Numero profilo:', 'it', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206E756D626572, 'Numer profilu:', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206F66, 'Profil de', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206F66, 'Profilo di', 'it', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206F6620, 'Profil von ', 'de', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C65206F6620, 'Perfil de', 'es', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C6520766973697473, 'Profilbesuche', 'de', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C6520766973697473, 'Visitas del perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C6520766973697473, 'Visiteurs de mon profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C6520766973697473, 'Visite profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C6573, 'Profile', 'de', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C6573, 'Perfiles', 'es', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C6573, 'Profiles', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C6573, 'Profili', 'it', 'yum');
INSERT INTO `translation` VALUES (0x50726F66696C6573, 'Profile', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x52616E6765, 'Bereich', 'de', 'yum');
INSERT INTO `translation` VALUES (0x52616E6765, 'Rango', 'es', 'yum');
INSERT INTO `translation` VALUES (0x52616E6765, 'Intervallo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x52616E6765, 'Ряд значений', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x52656164204F6E6C792050726F66696C6573, 'Nur-Lese Profile', 'de', 'yum');
INSERT INTO `translation` VALUES (0x52656164204F6E6C792050726F66696C6573, 'Perfiles de Sólo Lectura', 'es', 'yum');
INSERT INTO `translation` VALUES (0x52656164204F6E6C792050726F66696C6573, 'Lecture seule des profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x52656164204F6E6C792050726F66696C6573, 'Profilo sola lettura', 'it', 'yum');
INSERT INTO `translation` VALUES (0x52656164204F6E6C792050726F66696C6573, 'Profile tylko do odczytu', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x52656365697665206120456D61696C20666F72206E657720467269656E64736869702072657175657374, 'E-Mail Benachrichtigung bei neuer Kontaktanfrage', 'de', 'yum');
INSERT INTO `translation` VALUES (0x52656365697665206120456D61696C20666F72206E657720467269656E64736869702072657175657374, 'Recibir un correo cuando recibas una nueva solicitud de amistad', 'es', 'yum');
INSERT INTO `translation` VALUES (0x52656365697665206120456D61696C20666F72206E657720467269656E64736869702072657175657374, 'Informez moi par e-mail pour les nouvelles demandes de contact.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x52656365697665206120456D61696C20666F72206E657720467269656E64736869702072657175657374, 'Email di notifica per nuovo contatto', 'it', 'yum');
INSERT INTO `translation` VALUES (0x52656365697665206120456D61696C207768656E2061206E65772070726F66696C6520636F6D6D656E7420776173206D616465, 'E-Mail Benachrichtigung bei Profilkommentar', 'de', 'yum');
INSERT INTO `translation` VALUES (0x52656365697665206120456D61696C207768656E2061206E65772070726F66696C6520636F6D6D656E7420776173206D616465, 'Recibir un correo cuando comenten en tu perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x52656365697665206120456D61696C207768656E2061206E65772070726F66696C6520636F6D6D656E7420776173206D616465, 'Informez moi par e-mail e-mail pour les nouveaux commentaire de mon profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x52656365697665206120456D61696C207768656E2061206E65772070726F66696C6520636F6D6D656E7420776173206D616465, 'Email di notifica per nuovo commento al profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x52656365697665206120456D61696C207768656E206E6577204D6573736167652061727269766573, 'E-Mail Benachrichtigung bei neuer interner Nachricht', 'de', 'yum');
INSERT INTO `translation` VALUES (0x52656365697665206120456D61696C207768656E206E6577204D6573736167652061727269766573, 'Recibir un correo cuanto te llegue un nuevo mensaje', 'es', 'yum');
INSERT INTO `translation` VALUES (0x52656365697665206120456D61696C207768656E206E6577204D6573736167652061727269766573, 'Informez moi par e-mail pour les nouveaux messages.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x52656365697665206120456D61696C207768656E206E6577204D6573736167652061727269766573, 'Email di notifica per i nuovi messaggi', 'it', 'yum');
INSERT INTO `translation` VALUES (0x52656769737465726564207573657273, 'Registrierte Benutzer', 'de', 'yum');
INSERT INTO `translation` VALUES (0x52656769737465726564207573657273, 'Usuarios registrados', 'es', 'yum');
INSERT INTO `translation` VALUES (0x52656769737465726564207573657273, 'Membre enregistre', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x52656769737465726564207573657273, 'Utenti registrati', 'it', 'yum');
INSERT INTO `translation` VALUES (0x52656769737465726564207573657273, 'Зарегистрированные пользователи', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x526567697374726174696F6E, 'Registrierung', 'de', 'yum');
INSERT INTO `translation` VALUES (0x526567697374726174696F6E, 'Registro', 'es', 'yum');
INSERT INTO `translation` VALUES (0x526567697374726174696F6E, 'Inscription', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x526567697374726174696F6E, 'Reistrazione', 'it', 'yum');
INSERT INTO `translation` VALUES (0x526567697374726174696F6E, 'Rejestracja', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x526567697374726174696F6E, 'Регистрация', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x526567697374726174696F6E2064617465, 'Anmeldedatum', 'de', 'yum');
INSERT INTO `translation` VALUES (0x526567697374726174696F6E2064617465, 'Fecha de registro', 'es', 'yum');
INSERT INTO `translation` VALUES (0x526567697374726174696F6E2064617465, 'Date d inscription', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x526567697374726174696F6E2064617465, 'Data registrazione', 'it', 'yum');
INSERT INTO `translation` VALUES (0x526567697374726174696F6E2064617465, 'Anmeldedatum', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x526567697374726174696F6E2064617465, 'Дата регистрации', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x526567756C61722065787072657373696F6E20286578616D706C653A20222F5E5B412D5A612D7A302D39732C5D2B242F7522292E, 'Expresión regular (ejemplo: \"/^[A-Za-z0-9s,]+$/u\")', 'es', 'yum');
INSERT INTO `translation` VALUES (0x526567756C61722065787072657373696F6E20286578616D706C653A20272F5E5B412D5A612D7A302D39732C5D2B242F7527292E, 'Regulärer Ausdruck (z. B.: \'/^[A-Za-z0-9s,]+$/u\')', 'de', 'yum');
INSERT INTO `translation` VALUES (0x526567756C61722065787072657373696F6E20286578616D706C653A272F5E5B412D5A612D7A302D39732C5D2B242F7527292E, 'Expression regulaire (exemple.:\'/^[A-Za-z0-9s,]+$/u\')', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x526567756C61722065787072657373696F6E20286578616D706C653A272F5E5B412D5A612D7A302D39732C5D2B242F7527292E, 'Espressione regolare (esempio:\'/^[A-Za-z0-9s,]+$/u\')', 'it', 'yum');
INSERT INTO `translation` VALUES (0x526567756C61722065787072657373696F6E20286578616D706C653A272F5E5B412D5A612D7A302D39732C5D2B242F7527292E, 'Регулярные выражения (пример:\'/^[A-Za-z0-9s,]+$/u\')', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x52656D656D626572206D65206E65742074696D65, 'Zapamietaj mnie nastepnym razem', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x52656D656D626572206D65206E6578742074696D65, 'Angemeldet bleiben', 'de', 'yum');
INSERT INTO `translation` VALUES (0x52656D656D626572206D65206E6578742074696D65, 'Recordarme la próxima vez', 'es', 'yum');
INSERT INTO `translation` VALUES (0x52656D656D626572206D65206E6578742074696D65, 'Rester connecte', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x52656D656D626572206D65206E6578742074696D65, 'Ricordami', 'it', 'yum');
INSERT INTO `translation` VALUES (0x52656D656D626572206D65206E6578742074696D65, 'Запомнить меня', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x52656D6F7665, 'Entfernen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x52656D6F7665, 'Quitar', 'es', 'yum');
INSERT INTO `translation` VALUES (0x52656D6F7665, 'Supprimer', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x52656D6F7665, 'Rimuovi', 'it', 'yum');
INSERT INTO `translation` VALUES (0x52656D6F766520417661746172, 'Profilbild entfernen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x52656D6F766520417661746172, 'Borrar este Avatar', 'es', 'yum');
INSERT INTO `translation` VALUES (0x52656D6F766520417661746172, 'Supprimer l image de profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x52656D6F766520417661746172, 'Rimuovi avatar', 'it', 'yum');
INSERT INTO `translation` VALUES (0x52656D6F766520636F6D6D656E74, 'Kommentar entfernen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x52656D6F766520636F6D6D656E74, 'Borrar comentario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x52656D6F766520636F6D6D656E74, 'Supprimer ce commentaire', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x52656D6F766520636F6D6D656E74, 'Rimuovi commento', 'it', 'yum');
INSERT INTO `translation` VALUES (0x52656D6F766520667269656E64, 'Freundschaft kündigen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x52656D6F766520667269656E64, 'Borrar amigo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x52656D6F766520667269656E64, 'Supprimer ce contact de ma liste', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x52656D6F766520667269656E64, 'Rimuovi contatto', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5265706C79, 'Antwort', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5265706C79, 'Responder', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5265706C79, 'Repondre', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5265706C79, 'Rispondi', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5265706C79, 'Odpowiedz', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x5265706C7920746F204D657373616765, 'auf diese Nachricht antworten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5265706C7920746F204D657373616765, 'Responder al Mensaje', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5265706C7920746F204D657373616765, 'Repondre a ce message', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5265706C7920746F204D657373616765, 'Rispondi al messaggio', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5265706C7920746F204D657373616765, 'Odpowiedz', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x5265706C7920746F206D657373616765, 'Responder al mensaje', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5265706C7920746F206D657373616765, 'Rispondi al messaggio', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5265717565737420667269656E647368697020666F722075736572207B757365726E616D657D, 'Kontaktanfrage für {username}', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5265717565737420667269656E647368697020666F722075736572207B757365726E616D657D, 'Solicitar amistar al usuario {username}', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5265717565737420667269656E647368697020666F722075736572207B757365726E616D657D, 'Demande de contact pour {username}', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5265717565737420667269656E647368697020666F722075736572207B757365726E616D657D, 'Richiesta contatto per {username}', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5265717569726564, 'Benötigt', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5265717569726564, 'Requerido', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5265717569726564, 'Recquis', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5265717569726564, 'Obbligatorio', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5265717569726564, 'Обязательность', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x5265717569726564206669656C642028666F726D2076616C696461746F72292E, 'Campo obbligatorio (validazione form).', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5265717569726564206669656C642028666F726D2076616C696461746F72292E, 'Обязательное поле (проверка формы).', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x526573746F7265, 'Wiederherstellen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x526573746F7265, 'Recuperar', 'es', 'yum');
INSERT INTO `translation` VALUES (0x526573746F7265, 'Restaurer', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x526573746F7265, 'Ripristino', 'it', 'yum');
INSERT INTO `translation` VALUES (0x526573746F7265, 'Wiederherstellen', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x526573746F7265, 'Восстановить', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x5265747970652050617373776F7264, 'Повторите пароль', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x5265747970652050617373776F726420697320696E636F72726563742E, 'Пароли не совпадают.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x5265747970652070617373776F7264, 'Passwort wiederholen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5265747970652070617373776F7264, 'Repite la contraseña', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5265747970652070617373776F7264, 'Redonnez votre mot de passe', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5265747970652070617373776F7264, 'Conferma password', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5265747970652070617373776F7264, 'Passwort wiederholen', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x5265747970652070617373776F726420697320696E636F72726563742E, 'Wiederholtes Passwort ist falsch.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5265747970652070617373776F726420697320696E636F72726563742E, 'Contraseña repetida incorrecta', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5265747970652070617373776F726420697320696E636F72726563742E, 'Le mot de passe est a nouveau incorrect.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5265747970652070617373776F726420697320696E636F72726563742E, 'Conferma password e errata.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5265747970652070617373776F726420697320696E636F72726563742E, 'Wiederholtes Passwort ist falsch.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x52657479706520796F7572206E65772070617373776F7264, 'Wiederholen Sie Ihr neues Passwort', 'de', 'yum');
INSERT INTO `translation` VALUES (0x52657479706520796F7572206E65772070617373776F7264, 'Vuelva a escribir su nueva contrase', 'es', 'yum');
INSERT INTO `translation` VALUES (0x52657479706520796F7572206E65772070617373776F7264, 'Confirmez votre nouveau mot de passe', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x52657479706520796F7572206E65772070617373776F7264, 'Confermare la nuova password', 'it', 'yum');
INSERT INTO `translation` VALUES (0x526574797065642070617373776F726420697320696E636F7272656374, 'Wiederholtes Passwort ist nicht identisch', 'de', 'yum');
INSERT INTO `translation` VALUES (0x526574797065642070617373776F726420697320696E636F7272656374, 'La contrase', 'es', 'yum');
INSERT INTO `translation` VALUES (0x526574797065642070617373776F726420697320696E636F7272656374, 'Le mot de passe renseigne n est pas identique au precedent', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x526574797065642070617373776F726420697320696E636F7272656374, 'Password di conferma non identica', 'it', 'yum');
INSERT INTO `translation` VALUES (0x526F6C652041646D696E697374726174696F6E, 'Rollenverwaltung', 'de', 'yum');
INSERT INTO `translation` VALUES (0x526F6C652041646D696E697374726174696F6E, 'Administración de rol', 'es', 'yum');
INSERT INTO `translation` VALUES (0x526F6C652041646D696E697374726174696F6E, 'Gestion des roles', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x526F6C652041646D696E697374726174696F6E, 'Gestione dei ruoli', 'it', 'yum');
INSERT INTO `translation` VALUES (0x526F6C652041646D696E697374726174696F6E, 'Zarzadzanie rolami', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x526F6C6573, 'Rollen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x526F6C6573, 'Roles', 'es', 'yum');
INSERT INTO `translation` VALUES (0x526F6C6573, 'Roles', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x526F6C6573, 'Ruoli', 'it', 'yum');
INSERT INTO `translation` VALUES (0x526F6C6573, 'Role', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x526F6C6573202F2041636365737320636F6E74726F6C, 'Rollen / Zugangskontrolle', 'de', 'yum');
INSERT INTO `translation` VALUES (0x526F6C6573202F2041636365737320636F6E74726F6C, 'Funciones y de control de acceso', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53617665, 'Sichern', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53617665, 'Guardar', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53617665, 'Memoriser', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x53617665, 'Salva', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53617665, 'Sichern', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x53617665, 'Сохранить', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x53617665207061796D656E742074797065, 'Zahlungsart speichern', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53617665207061796D656E742074797065, 'Guardar el tipo de pago', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53617665207061796D656E742074797065, 'Salva tipo pagamento', 'it', 'yum');
INSERT INTO `translation` VALUES (0x536176652070726F66696C65206368616E676573, 'Profiländerungen speichern', 'de', 'yum');
INSERT INTO `translation` VALUES (0x536176652070726F66696C65206368616E676573, 'Guardar los cambios de perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x536176652070726F66696C65206368616E676573, 'Salva modifiche profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5361766520726F6C65, 'Rolle speichern', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5361766520726F6C65, 'Guardar funci', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5361766520726F6C65, 'Memoriser ce role', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5361766520726F6C65, 'Salva ruolo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53656172636820666F7220757365726E616D65, 'Suche nach Benutzer', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53656172636820666F7220757365726E616D65, 'B', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53656172636820666F7220757365726E616D65, 'Recherche par nom d\'utilisateur', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x53656172636820666F7220757365726E616D65, 'Cerca per username', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53656172636861626C65, 'Suchbar', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53656172636861626C65, 'Investigable', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53656172636861626C65, 'visible', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x53656172636861626C65, 'Ricercabile', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53656C6563742061206D6F6E7468, 'Monatsauswahl', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53656C6563742061206D6F6E7468, 'Seleccione un mes', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53656C6563742061206D6F6E7468, 'Seleziona un mese', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53656C656374206D756C7469706C6520726563697069656E747320627920686F6C64696E6720746865204354524C206B6579, 'Wählen Sie mehrere Empfänger mit der STRG-Taste aus', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53656C656374206D756C7469706C6520726563697069656E747320627920686F6C64696E6720746865204354524C206B6579, 'Selecciona varios destinatarios manteniendo presionada la tecla CTRL', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53656C656374206D756C7469706C6520726563697069656E747320627920686F6C64696E6720746865204354524C206B6579, 'Choix multiple en laissant la touche STRG de votre clavier enfoncee', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x53656C656374206D756C7469706C6520726563697069656E747320627920686F6C64696E6720746865204354524C206B6579, 'Seleziona destinatari multipli con il tasto CTRL', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53656C65637420746865206669656C647320746861742073686F756C64206265207075626C6963, 'Diese Felder sind öffentlich einsehbar', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53656C65637420746865206669656C647320746861742073686F756C64206265207075626C6963, 'Seleccione los campos que deben ser p', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53656C65637420746865206669656C647320746861742073686F756C64206265207075626C6963, 'Ces champs sont publiques et seront visibles', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x53656C65637420746865206669656C647320746861742073686F756C64206265207075626C6963, 'Scegli i campi da rendere publici', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53656C65637461626C65206F6E20726567697374726174696F6E, 'Während der Registrierung wählbar', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53656C65637461626C65206F6E20726567697374726174696F6E, 'Seleccionable en el registro', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53656C65637461626C65206F6E20726567697374726174696F6E, 'Option a selectionner au cours de l inscription', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x53656C65637461626C65206F6E20726567697374726174696F6E, 'Selezionabile durante la registrazione', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53656E64, 'Senden', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53656E64, 'Enviar', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53656E64, 'Envoyer', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x53656E64, 'Invia', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53656E64, 'Senden', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x53656E642061206D65737361676520746F20746869732055736572, 'Diesem Benutzer eine Nachricht senden', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53656E642061206D65737361676520746F20746869732055736572, 'Enviar un mensaje a este Usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53656E642061206D65737361676520746F20746869732055736572, 'Faire parvenir un message a ce membre', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x53656E642061206D65737361676520746F20746869732055736572, 'Invia messaggio all\'utente', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53656E6420696E7669746174696F6E, 'Kontaktanfrage senden', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53656E6420696E7669746174696F6E, 'Enviar invitación', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53656E6420696E7669746174696F6E, 'Envoyer la demande de contact', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x53656E6420696E7669746174696F6E, 'Kontaktanfrage senden', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53656E64206D657373616765206E6F74696669657220656D61696C73, 'Benachrichtigungen schicken', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53656E64206D657373616765206E6F74696669657220656D61696C73, 'Enviar mensaje de e-mail de notificación', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53656E64206D657373616765206E6F74696669657220656D61696C73, 'Envoie d une notification', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x53656E64206D657373616765206E6F74696669657220656D61696C73, 'Notifiche e-mail', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53656E74206174, 'Gesendet am', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53656E74206174, 'Enviado al', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53656E74206174, 'ENvoye le', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x53656E74206174, 'Pubblicato il', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53656E74206174, 'Wyslano', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x53656E74206D65737361676573, 'Gesendete Nachrichten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53656E74206D65737361676573, 'Mensajes enviados', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53656E74206D65737361676573, 'Message envoye', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x53656E74206D65737361676573, 'Messaggi inviati', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53656E74206D65737361676573, 'Wyslane wiadomosci', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x536570617261746520757365726E616D6573207769746820636F6D6D6120746F2069676E6F726520737065636966696564207573657273, 'Benutzernamen mit Komma trennen, um sie zu ignorieren', 'de', 'yum');
INSERT INTO `translation` VALUES (0x536570617261746520757365726E616D6573207769746820636F6D6D6120746F2069676E6F726520737065636966696564207573657273, 'Separa con coma los nombres de los usuarios que deseas ignorar', 'es', 'yum');
INSERT INTO `translation` VALUES (0x536570617261746520757365726E616D6573207769746820636F6D6D6120746F2069676E6F726520737065636966696564207573657273, 'Ma liste noire, pour introduire plusieurs membres en une seule fois, separer les noms d utilisateur avec une virgule', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x536570617261746520757365726E616D6573207769746820636F6D6D6120746F2069676E6F726520737065636966696564207573657273, 'Separa gli username con una virgola, per ignorare gli utenti specificati', 'it', 'yum');
INSERT INTO `translation` VALUES (0x536574207061796D656E74206461746520746F20746F646179, 'Zahlungseingang bestätigen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x536574207061796D656E74206461746520746F20746F646179, 'Establecer fecha de pago el d', 'es', 'yum');
INSERT INTO `translation` VALUES (0x536574207061796D656E74206461746520746F20746F646179, 'Imposta data pagamento ad oggi', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53657474696E6773, 'Einstellungen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53657474696E6773, 'Ajustes', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53657474696E6773, 'Reglage', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x53657474696E6773, 'Impostazioni', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53657474696E6773, 'Ustawienia', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x53657474696E67732070726F66696C6573, 'Einstellungsprofile', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53657474696E67732070726F66696C6573, 'Ajustes de perfiles', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53657474696E67732070726F66696C6573, 'Reglages des profiles', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x53657474696E67732070726F66696C6573, 'Impostazioni profili', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53657474696E67732070726F66696C6573, 'Ustawienia profili', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x53686F772061637469766974696573, 'Zeige Aktivitäten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53686F772061637469766974696573, 'Mostrar las actividades', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53686F772061637469766974696573, 'Voir la chronique des activites', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x53686F772061637469766974696573, 'Mostra attivita', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53686F772061646D696E697374726174696F6E20486965726172636879, 'Hierarchie', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53686F772061646D696E697374726174696F6E20486965726172636879, 'Mostrar jerarquía de administración', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53686F772061646D696E697374726174696F6E20486965726172636879, 'Hierarchie', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x53686F772061646D696E697374726174696F6E20486965726172636879, 'Gerarchia', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53686F772061646D696E697374726174696F6E20486965726172636879, 'Pokaz hierarchie administrowania', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x53686F7720616C6C, 'Mostra tutti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53686F7720667269656E6473, 'Kontaktliste veröffentlichen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53686F7720667269656E6473, 'Mostrar amigos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53686F7720667269656E6473, 'REndre ma liste de contacts visible', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x53686F7720667269656E6473, 'Mostra contatti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53686F77206D79206F6E6C696E652073746174757320746F2065766572796F6E65, 'Meinen Online-Status veröffentlichen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53686F77206D79206F6E6C696E652073746174757320746F2065766572796F6E65, 'Mostrar mi estado de conexi', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53686F77206D79206F6E6C696E652073746174757320746F2065766572796F6E65, 'Montrer lorsque je suis en ligne', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x53686F77206D79206F6E6C696E652073746174757320746F2065766572796F6E65, 'Mostra il mio stato a tutti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53686F77206F6E6C696E6520737461747573, 'Online-Status anzeigen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53686F77206F6E6C696E6520737461747573, 'Mostrar estado de conexi', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53686F77206F6E6C696E6520737461747573, 'Status en ligne visible', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x53686F77206F6E6C696E6520737461747573, 'Mostra lo stato online', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53686F77207065726D697373696F6E73, 'Berechtigungen anzeigen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53686F77207065726D697373696F6E73, 'Mostrar permisos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53686F77207065726D697373696F6E73, 'Montrer les permissions', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x53686F77207065726D697373696F6E73, 'Mostra autorizzazioni', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53686F772070726F66696C6520766973697473, 'Profilbesuche anzeigen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53686F772070726F66696C6520766973697473, 'Mostrar perfil de visitas', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53686F772070726F66696C6520766973697473, 'Montrer les visites de profils', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x53686F772070726F66696C6520766973697473, 'Visualizza visite profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53686F7720726F6C6573, 'Rollen anzeigen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53686F7720726F6C6573, 'Mostrar roles', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53686F7720726F6C6573, 'Voir les roles', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x53686F7720726F6C6573, 'Mostra ruoli', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53686F7720726F6C6573, 'Pokaz role', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x53686F7720746865206F776E6572207768656E2069207669736974206869732070726F66696C65, 'Dem Profileigentümer erkenntlich machen, wenn ich sein Profil besuche', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53686F7720746865206F776E6572207768656E2069207669736974206869732070726F66696C65, 'Mostrar el due', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53686F7720746865206F776E6572207768656E2069207669736974206869732070726F66696C65, 'Montrer aux proprietaires des profils lorsque je consulte leur profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x53686F7720746865206F776E6572207768656E2069207669736974206869732070726F66696C65, 'Mostra al proprietario quando visito il suo profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53686F77207573657273, 'Benutzer anzeigen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53686F77207573657273, 'Mostrar usuarios', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53686F77207573657273, 'Voir les membres', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x53686F77207573657273, 'Mostra utenti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53686F77207573657273, 'Pokaz uzytkownikow', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x53746174697374696373, 'Benutzerstatistik', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53746174697374696373, 'Estadísticas', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53746174697374696373, 'Statistiques des membres', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x53746174697374696373, 'Statistiche', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53746174697374696373, 'Statystyki', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x537461747573, 'Status', 'de', 'yum');
INSERT INTO `translation` VALUES (0x537461747573, 'Estado', 'es', 'yum');
INSERT INTO `translation` VALUES (0x537461747573, 'Status', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x537461747573, 'Stato', 'it', 'yum');
INSERT INTO `translation` VALUES (0x537461747573, 'Status', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x537461747573, 'Статус', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x537472656574, 'Straße', 'de', 'yum');
INSERT INTO `translation` VALUES (0x537472656574, 'Calle', 'es', 'yum');
INSERT INTO `translation` VALUES (0x537472656574, 'Rue', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x537472656574, 'Indirizzo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x537472656574, 'Ulica', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x5375626A656374, 'Titel', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5375626A656374, 'Tema', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5375626A656374, 'Sujet', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5375626A656374, 'Oggetto', 'it', 'yum');
INSERT INTO `translation` VALUES (0x53756363657373, 'Erfolgreich', 'de', 'yum');
INSERT INTO `translation` VALUES (0x53756363657373, 'Exitoso', 'es', 'yum');
INSERT INTO `translation` VALUES (0x53756363657373, 'Reussi', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x53756363657373, 'Successo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x537570657275736572, 'Superuser', 'de', 'yum');
INSERT INTO `translation` VALUES (0x537570657275736572, 'Superusuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x537570657275736572, 'Superuser', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x537570657275736572, 'Superuser', 'it', 'yum');
INSERT INTO `translation` VALUES (0x537570657275736572, 'Superuser', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x537570657275736572, 'Супер пользователь', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x5465787420456D61696C2041637469766174696F6E, 'Text Email Konto-Aktivierung', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5465787420456D61696C2041637469766174696F6E, 'Texto de activación por correo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5465787420456D61696C2041637469766174696F6E, 'Texte contenu dans l e-mail d activation de compte', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5465787420456D61696C2041637469766174696F6E, 'Testo email d\'attivazione account', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5465787420456D61696C205265636F76657279, 'Text E-Mail Passwort wiederherstellen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5465787420456D61696C205265636F76657279, 'Texto de recuperación de contraseña por correo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5465787420456D61696C205265636F76657279, 'Texte contenu dans l e-Mail de renouvellement de mot de passe', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5465787420456D61696C205265636F76657279, 'Testo email recupero password', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5465787420456D61696C20526567697374726174696F6E, 'Text E-Mail Registrierung', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5465787420456D61696C20526567697374726174696F6E, 'Texto de registro por correo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5465787420456D61696C20526567697374726174696F6E, 'Texte contenu dans l e-Mail d enregistrement', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5465787420456D61696C20526567697374726174696F6E, 'Testo email di registrazione', 'it', 'yum');
INSERT INTO `translation` VALUES (0x54657874204C6F67696E20466F6F746572, 'Text im Login-footer', 'de', 'yum');
INSERT INTO `translation` VALUES (0x54657874204C6F67696E20466F6F746572, 'Text im Login-footer', 'es', 'yum');
INSERT INTO `translation` VALUES (0x54657874204C6F67696E20466F6F746572, 'Text im Login-footer', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x54657874204C6F67696E20466F6F746572, 'Testo nel piepagina del login', 'it', 'yum');
INSERT INTO `translation` VALUES (0x54657874204C6F67696E20486561646572, 'Text im Login-header', 'de', 'yum');
INSERT INTO `translation` VALUES (0x54657874204C6F67696E20486561646572, 'Text im Login-header', 'es', 'yum');
INSERT INTO `translation` VALUES (0x54657874204C6F67696E20486561646572, 'Texte de connection-header', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x54657874204C6F67696E20486561646572, 'Testo nell\'intestazione del login', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5465787420526567697374726174696F6E20466F6F746572, 'Text im Registrierung-footer', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5465787420526567697374726174696F6E20466F6F746572, 'Text im Registrierung-footer', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5465787420526567697374726174696F6E20466F6F746572, 'Texte d enregistrement-footer', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5465787420526567697374726174696F6E20466F6F746572, 'Testo nel piepagina della registrazione', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5465787420526567697374726174696F6E20486561646572, 'Text im Registrierung-header', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5465787420526567697374726174696F6E20486561646572, 'Text im Registrierung-header', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5465787420526567697374726174696F6E20486561646572, 'Texte d enregistrement-header', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5465787420526567697374726174696F6E20486561646572, 'Testo nell\'intestazione della registrazione', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5465787420666F72206E657720667269656E64736869702072657175657374, 'Text für eine neue Kontaktanfrage', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5465787420666F72206E657720667269656E64736869702072657175657374, 'Text für eine neue Kontaktanfrage', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5465787420666F72206E657720667269656E64736869702072657175657374, 'Texte pour une nouvelle demande de contact', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5465787420666F72206E657720667269656E64736869702072657175657374, 'Testo per una nuova richiesta di contatto', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5465787420666F72206E65772070726F66696C6520636F6D6D656E74, 'Text für neuen Profilkommentar', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5465787420666F72206E65772070726F66696C6520636F6D6D656E74, 'Text für neuen Profilkommentar', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5465787420666F72206E65772070726F66696C6520636F6D6D656E74, 'Texte pour un nouveau commentaire dans un profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5465787420666F72206E65772070726F66696C6520636F6D6D656E74, 'Testo per un nuovo commento al profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x54657874207472616E736C6174696F6E73, 'Übersetzungstexte', 'de', 'yum');
INSERT INTO `translation` VALUES (0x54657874207472616E736C6174696F6E73, 'Traducciones de texto', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5468616E6B20796F7520666F7220796F757220726567697374726174696F6E2E20436F6E746163742041646D696E20746F20616374697661746520796F7572206163636F756E742E, 'Grazie per la tua registrazione. Contatta l\'ammnistratore per attivare l\'account', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5468616E6B20796F7520666F7220796F757220726567697374726174696F6E2E20506C6561736520636865636B20796F757220656D61696C206F72206C6F67696E2E, 'Vielen Dank für Ihre Anmeldung. Bitte überprüfen Sie Ihre E-Mails oder loggen Sie sich ein.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5468616E6B20796F7520666F7220796F757220726567697374726174696F6E2E20506C6561736520636865636B20796F757220656D61696C206F72206C6F67696E2E, 'Gracias por su registro. Por favor, compruebe su correo electr', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5468616E6B20796F7520666F7220796F757220726567697374726174696F6E2E20506C6561736520636865636B20796F757220656D61696C206F72206C6F67696E2E, 'Merci pour votre inscription.Controlez votre boite e-mail, le code d activation de votre compte vous a ete envoye par e-mail.Attention! Par mesure de securite, le lien contenu dans ce mail, n est valable que 48h *IMPORTANT:pour le cas ou notre e-mail', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5468616E6B20796F7520666F7220796F757220726567697374726174696F6E2E20506C6561736520636865636B20796F757220656D61696C206F72206C6F67696E2E, 'Grazie per la tua registrazione, controlla la tua email o effettua il login,', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5468616E6B20796F7520666F7220796F757220726567697374726174696F6E2E20506C6561736520636865636B20796F757220656D61696C206F72206C6F67696E2E, 'Vielen Dank fur Ihre Anmeldung. Bitte uberprufen Sie Ihre E-Mails oder loggen Sie sich ein.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x5468616E6B20796F7520666F7220796F757220726567697374726174696F6E2E20506C6561736520636865636B20796F757220656D61696C206F72206C6F67696E2E, 'Регистрация завершена. Пожалуйста проверьте свой электронный ящик или выполните вход.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x5468616E6B20796F7520666F7220796F757220726567697374726174696F6E2E20506C6561736520636865636B20796F757220656D61696C2E, 'Vielen Dank für Ihre Anmeldung. Bitte überprüfen Sie Ihre E-Mails.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5468616E6B20796F7520666F7220796F757220726567697374726174696F6E2E20506C6561736520636865636B20796F757220656D61696C2E, 'Gracias por su registro. Por favor revise su email.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5468616E6B20796F7520666F7220796F757220726567697374726174696F6E2E20506C6561736520636865636B20796F757220656D61696C2E, 'Merci pour votre inscription.Controlez votre boite e-mail, le code d activation de votre compte vous a ete envoye par e-mail. *IMPORTANT:pour le cas ou notre e-mail ne vous serais pas parvenu, il est possible que notre e-mail ai ete filtre par votre', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5468616E6B20796F7520666F7220796F757220726567697374726174696F6E2E20506C6561736520636865636B20796F757220656D61696C2E, 'Grazie per la tua registrazione, controlla la tua email,', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5468616E6B20796F7520666F7220796F757220726567697374726174696F6E2E20506C6561736520636865636B20796F757220656D61696C2E, 'Vielen Dank fur Ihre Anmeldung. Bitte uberprufen Sie Ihre E-Mails.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x5468616E6B20796F7520666F7220796F757220726567697374726174696F6E2E20506C6561736520636865636B20796F757220656D61696C2E, 'Регистрация завершена. Пожалуйста проверьте свой электронный ящик.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x5468616E6B20796F7520666F7220796F757220726567697374726174696F6E2E20506C65617365207B7B6C6F67696E7D7D2E, 'Grazie per la tua registrazone. Effettua il {{login}}.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x54686520636F6D6D656E7420686173206265656E207361766564, 'Der Kommentar wurde gespeichert', 'de', 'yum');
INSERT INTO `translation` VALUES (0x54686520636F6D6D656E7420686173206265656E207361766564, 'Der Kommentar wurde gespeichert', 'es', 'yum');
INSERT INTO `translation` VALUES (0x54686520636F6D6D656E7420686173206265656E207361766564, 'Le commentaire a bien ete memorise', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x54686520636F6D6D656E7420686173206265656E207361766564, 'Il commento e stato salvato', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5468652066696C6520227B66696C657D22206973206E6F7420616E20696D6167652E, 'Die Datei {file} ist kein Bild.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5468652066696C6520227B66696C657D22206973206E6F7420616E20696D6167652E, 'Este archivo {file} no es una imagen.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5468652066696C65202671756F743B7B66696C657D2671756F743B206973206E6F7420616E20696D6167652E, 'DLe fichier {file} n est pas un fichier image.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5468652066696C65202671756F743B7B66696C657D2671756F743B206973206E6F7420616E20696D6167652E, 'Il file {file} non e un\'immagine.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x54686520667269656E6473686970207265717565737420686173206265656E2073656E74, 'Die Kontaktanfrage wurde gesendet', 'de', 'yum');
INSERT INTO `translation` VALUES (0x54686520667269656E6473686970207265717565737420686173206265656E2073656E74, 'La solicitud de amistad ha sido enviado', 'es', 'yum');
INSERT INTO `translation` VALUES (0x54686520667269656E6473686970207265717565737420686173206265656E2073656E74, 'Votre demande de contact a bien ete envoyee', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x54686520667269656E6473686970207265717565737420686173206265656E2073656E74, 'La richiesta di contatto e stata inviata', 'it', 'yum');
INSERT INTO `translation` VALUES (0x54686520696D61676520227B66696C657D22206865696768742073686F756C6420626520227B6865696768747D7078222E, 'Die Datei {file} muss genau {height}px hoch sein.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x54686520696D61676520227B66696C657D22206865696768742073686F756C6420626520227B6865696768747D7078222E, 'La imagen {file} debe tener {height}px de largo.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x54686520696D61676520227B66696C657D222077696474682073686F756C6420626520227B77696474687D7078222E, 'Die Datei {file} muss genau {width}px breit sein.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x54686520696D61676520227B66696C657D222077696474682073686F756C6420626520227B77696474687D7078222E, 'La imagen {file} debe tener {width}px de ancho.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x54686520696D616765202671756F743B7B66696C657D2671756F743B206865696768742073686F756C64206265202671756F743B7B6865696768747D70782671756F743B2E, 'La photo {file} doit avoir une hauteur maximum de {height}px .', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x54686520696D616765202671756F743B7B66696C657D2671756F743B206865696768742073686F756C64206265202671756F743B7B6865696768747D70782671756F743B2E, 'L\'immagine {file} deve essere {height}px.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x54686520696D616765202671756F743B7B66696C657D2671756F743B2077696474682073686F756C64206265202671756F743B7B77696474687D70782671756F743B2E, 'La photo {file} doit avoir une largeur maximum de {width}px .', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x54686520696D616765202671756F743B7B66696C657D2671756F743B2077696474682073686F756C64206265202671756F743B7B77696474687D70782671756F743B2E, 'L\'immagine {file} deve essere larga {width}px.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x54686520696D61676520686173206265656E20726573697A656420746F207B6D61785F706978656C7D7078207769647468207375636365737366756C6C79, 'Das Bild wurde beim hochladen automatisch auf eine Breite von {max_pixel} skaliert', 'de', 'yum');
INSERT INTO `translation` VALUES (0x54686520696D61676520686173206265656E20726573697A656420746F207B6D61785F706978656C7D7078207769647468207375636365737366756C6C79, 'La imagen ha sido redimensionada a {max_pixel} px de ancho con ', 'es', 'yum');
INSERT INTO `translation` VALUES (0x54686520696D61676520686173206265656E20726573697A656420746F207B6D61785F706978656C7D7078207769647468207375636365737366756C6C79, 'Votre photo de profil a ete retaillee automatiquement a une taille de{max_pixel}', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x54686520696D61676520686173206265656E20726573697A656420746F207B6D61785F706978656C7D7078207769647468207375636365737366756C6C79, 'Immagine ridimensionata a {max_pixel}px con successo.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x54686520696D6167652073686F756C642068617665206174206C65617374203530707820616E642061206D6178696D756D206F6620323030707820696E20776964746820616E64206865696768742E20537570706F727465642066696C65747970657320617265202E6A70672C202E67696620616E64202E706E67, 'das Bild sollte mindestens 50px und maximal 200px in der Höhe und Breite betragen. Mögliche Dateitypen sind .jpg, .gif und .png', 'de', 'yum');
INSERT INTO `translation` VALUES (0x54686520696D6167652073686F756C642068617665206174206C65617374203530707820616E642061206D6178696D756D206F6620323030707820696E20776964746820616E64206865696768742E20537570706F727465642066696C65747970657320617265202E6A70672C202E67696620616E64202E706E67, 'La imagen debe tener un mínimo de 50px y un máximo de 200px de ancho y largo. Los tipos de archivo soportados son .jpg, .gif y .png', 'es', 'yum');
INSERT INTO `translation` VALUES (0x54686520696D6167652073686F756C642068617665206174206C65617374203530707820616E642061206D6178696D756D206F6620323030707820696E20776964746820616E64206865696768742E20537570706F727465642066696C65747970657320617265202E6A70672C202E67696620616E64202E706E67, 'La foto chargee doit avoir une largeur maximum de 50px  et une hauteur maximale de 200px. Les fichiers acceptes sont; .jpg, .gif und .png', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x54686520696D6167652073686F756C642068617665206174206C65617374203530707820616E642061206D6178696D756D206F6620323030707820696E20776964746820616E64206865696768742E20537570706F727465642066696C65747970657320617265202E6A70672C202E67696620616E64202E706E67, 'L\'immagine deve essere almeno 50px e massimo 200px in larghezza e altezza. Tipi di file supportati .jpg, .gif e .png', 'it', 'yum');
INSERT INTO `translation` VALUES (0x54686520696D616765207761732075706C6F61646564207375636365737366756C6C79, 'Das Bild wurde erfolgreich hochgeladen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x54686520696D616765207761732075706C6F61646564207375636365737366756C6C79, 'La imagen se ha carregado correctamente', 'es', 'yum');
INSERT INTO `translation` VALUES (0x54686520696D616765207761732075706C6F61646564207375636365737366756C6C79, 'L image a ete chargee avec succes', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x54686520696D616765207761732075706C6F61646564207375636365737366756C6C79, 'Immagine caricata con successo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x546865206D6573736167657320666F7220796F7572206170706C69636174696F6E206C616E677561676520617265206E6F7420646566696E65642E, 'Los mensajes para el idioma de tu aplicación no están definidos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x546865206D696E696D756D2076616C7565206F6620746865206669656C642028666F726D2076616C696461746F72292E, 'Minimalwert des Feldes (Form-Validierung', 'de', 'yum');
INSERT INTO `translation` VALUES (0x546865206D696E696D756D2076616C7565206F6620746865206669656C642028666F726D2076616C696461746F72292E, 'El valor mínimo del campo (validador de formulario)', 'es', 'yum');
INSERT INTO `translation` VALUES (0x546865206D696E696D756D2076616C7565206F6620746865206669656C642028666F726D2076616C696461746F72292E, 'Valeur minimum du champ (Validation du formulaire)', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x546865206D696E696D756D2076616C7565206F6620746865206669656C642028666F726D2076616C696461746F72292E, 'Valore minimo del campo (validazione form).', 'it', 'yum');
INSERT INTO `translation` VALUES (0x546865206D696E696D756D2076616C7565206F6620746865206669656C642028666F726D2076616C696461746F72292E, 'Минимальное значение поля (проверка формы).', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x546865206E65772070617373776F726420686173206265656E207361766564, 'Das neue Passwort wurde gespeichert.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x546865206E65772070617373776F726420686173206265656E207361766564, 'La nueva contrase', 'es', 'yum');
INSERT INTO `translation` VALUES (0x546865206E65772070617373776F726420686173206265656E207361766564, 'Votre nouveau mot de passe a bien ete memorise.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x546865206E65772070617373776F726420686173206265656E207361766564, 'La nuova password e stata salvata.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x546865206E65772070617373776F726420686173206265656E2073617665642E, 'La nueva contraseña ha sido guardada', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5468652076616C7565206F66207468652064656661756C74206669656C6420286461746162617365292E, 'Standard-Wert für die Datenbank', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5468652076616C7565206F66207468652064656661756C74206669656C6420286461746162617365292E, 'El valor predeterminado del campo (base de datos).', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5468652076616C7565206F66207468652064656661756C74206669656C6420286461746162617365292E, 'Valeur standard pour la banque de donnee', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5468652076616C7565206F66207468652064656661756C74206669656C6420286461746162617365292E, 'Valore del campo predefnito (database).', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5468652076616C7565206F66207468652064656661756C74206669656C6420286461746162617365292E, 'Domyslna wartosc pola (bazodanowego).', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x5468652076616C7565206F66207468652064656661756C74206669656C6420286461746162617365292E, 'Значение поля по умолчанию (база данных).', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x546865726520617265206120746F74616C206F66207B6D657373616765737D206D6573736167657320696E20796F75722053797374656D2E, 'Es gibt in ihrem System insgesamt {messages} Nachrichten.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x546865726520617265206120746F74616C206F66207B6D657373616765737D206D6573736167657320696E20796F75722053797374656D2E, 'Hay un total de {messages} mensajes en su sistema.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x546865726520617265206120746F74616C206F66207B6D657373616765737D206D6573736167657320696E20796F75722053797374656D2E, 'Il existe dans votre systeme {messages} messages.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x546865726520617265206120746F74616C206F66207B6D657373616765737D206D6573736167657320696E20796F75722053797374656D2E, 'Ci sno un totale di {messages} messaggi nel Sistema.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x546865726520617265206120746F74616C206F66207B6D657373616765737D206D6573736167657320696E20796F75722053797374656D2E, 'Istnieje {messages} wiadomosci w Twoim systemie.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x546865726520617265207B6163746976655F75736572737D2061637469766520616E64207B696E6163746976655F75736572737D20696E61637469766520757365727320696E20796F75722053797374656D2C2066726F6D207768696368207B61646D696E5F75736572737D206172652041646D696E6973747261746F72732E, ' Es gibt {active_users} aktive und {inactive_users} inaktive Benutzer in ihrem System, von denen {admin_users} Benutzer Administratoren sind.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x546865726520617265207B6163746976655F75736572737D2061637469766520616E64207B696E6163746976655F75736572737D20696E61637469766520757365727320696E20796F75722053797374656D2C2066726F6D207768696368207B61646D696E5F75736572737D206172652041646D696E6973747261746F72732E, 'Hay {active_users} usuarios activos y {inactive_users} usuarios inactivos en su sistema, de los cuales {admin_users} son Administradores.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x546865726520617265207B6163746976655F75736572737D2061637469766520616E64207B696E6163746976655F75736572737D20696E61637469766520757365727320696E20796F75722053797374656D2C2066726F6D207768696368207B61646D696E5F75736572737D206172652041646D696E6973747261746F72732E, 'Il existe {active_users}  membres actifs et {inactive_users} membres inactifs dans votre systeme, pour lesquels {admin_users} membres sont designes en tant qu administrateurs.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x546865726520617265207B6163746976655F75736572737D2061637469766520616E64207B696E6163746976655F75736572737D20696E61637469766520757365727320696E20796F75722053797374656D2C2066726F6D207768696368207B61646D696E5F75736572737D206172652041646D696E6973747261746F72732E, 'Ci sono {active_users} utenti attivi e {inactive_users} utenti inattivi nel Sistema, di cui {admin_users} sono amministratori.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x546865726520617265207B6163746976655F75736572737D2061637469766520616E64207B696E6163746976655F75736572737D20696E61637469766520757365727320696E20796F75722053797374656D2C2066726F6D207768696368207B61646D696E5F75736572737D206172652041646D696E6973747261746F72732E, 'Istnieja {active_users} aktywni i {inactive_users} nieaktywni uzytkownicy w Twoim systemie, w tym {admin_users} administratorzy.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x546865726520617265207B70726F66696C65737D2070726F66696C657320696E20796F75722053797374656D2E20546865736520636F6E73697374206F66207B70726F66696C655F6669656C64737D2070726F66696C65206669656C647320696E207B70726F66696C655F6669656C645F67726F7570737D2070726F66696C65206669656C642067726F757073, 'Es gibt {profiles} Profile in ihren System. Diese bestehen aus {profile_fields} Profilfeldern, die sich in {profile_field_groups} Profilfeldgruppen aufteilen.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x546865726520617265207B70726F66696C65737D2070726F66696C657320696E20796F75722053797374656D2E20546865736520636F6E73697374206F66207B70726F66696C655F6669656C64737D2070726F66696C65206669656C647320696E207B70726F66696C655F6669656C645F67726F7570737D2070726F66696C65206669656C642067726F757073, 'Hay {profiles} perfiles en su sistema. Estos consisten de {profile_fields} campos de perfiles en {profile_field_groups} grupos de campos de perfiles', 'es', 'yum');
INSERT INTO `translation` VALUES (0x546865726520617265207B70726F66696C65737D2070726F66696C657320696E20796F75722053797374656D2E20546865736520636F6E73697374206F66207B70726F66696C655F6669656C64737D2070726F66696C65206669656C647320696E207B70726F66696C655F6669656C645F67726F7570737D2070726F66696C65206669656C642067726F757073, 'Il existe {profiles} profils dans votre systeme. Ils se composent de {profile_fields} champs de profils, qui se decomposent {profile_field_groups} en grouppe de champs de profils.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x546865726520617265207B70726F66696C65737D2070726F66696C657320696E20796F75722053797374656D2E20546865736520636F6E73697374206F66207B70726F66696C655F6669656C64737D2070726F66696C65206669656C647320696E207B70726F66696C655F6669656C645F67726F7570737D2070726F66696C65206669656C642067726F757073, 'Ci sono {profiles} profili nel Sistema. sono costituiti da {profile_fields} campi profili, in {profile_field_groups} campo profili gruppi.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x546865726520617265207B70726F66696C65737D2070726F66696C657320696E20796F75722053797374656D2E20546865736520636F6E73697374206F66207B70726F66696C655F6669656C64737D2070726F66696C65206669656C647320696E207B70726F66696C655F6669656C645F67726F7570737D2070726F66696C65206669656C642067726F757073, 'Istnieja {profiles} profile w Twoim systemie, ktore zawieraja pola {profile_fields} w grupach {profile_field_groups}', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x546865726520617265207B726F6C65737D20726F6C657320696E20796F75722053797374656D2E, 'Es gibt {roles} Rollen in ihrem System', 'de', 'yum');
INSERT INTO `translation` VALUES (0x546865726520617265207B726F6C65737D20726F6C657320696E20796F75722053797374656D2E, 'Hay {roles} roles en su sistema.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x546865726520617265207B726F6C65737D20726F6C657320696E20796F75722053797374656D2E, 'Il existe les {roles} roles suivant dans votre systeme', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x546865726520617265207B726F6C65737D20726F6C657320696E20796F75722053797374656D2E, 'Ci sono {roles} ruoli nel Sistema', 'it', 'yum');
INSERT INTO `translation` VALUES (0x546865726520617265207B726F6C65737D20726F6C657320696E20796F75722053797374656D2E, 'Istnieje {roles} rol w Twoim systemie', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x54686572652077617320616E206572726F7220736176696E67207468652070617373776F7264, 'Fehler beim speichern des Passwortes', 'de', 'yum');
INSERT INTO `translation` VALUES (0x54686572652077617320616E206572726F7220736176696E67207468652070617373776F7264, 'Hubo un error al guardar la contrase', 'es', 'yum');
INSERT INTO `translation` VALUES (0x54686572652077617320616E206572726F7220736176696E67207468652070617373776F7264, 'Erreur produite lors de la memorisation de votre mot de passe.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x54686572652077617320616E206572726F7220736176696E67207468652070617373776F7264, 'Impossibile salvare la password', 'it', 'yum');
INSERT INTO `translation` VALUES (0x546865736520757365727320686176652061206F726465726564206D656D6265727368697073206F66207468697320726F6C65, 'Diese Benutzer haben eine Mitgliedschaft in dieser Rolle', 'de', 'yum');
INSERT INTO `translation` VALUES (0x546865736520757365727320686176652061206F726465726564206D656D6265727368697073206F66207468697320726F6C65, 'Ces membres sont assignes a ce role', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x546865736520757365727320686176652061206F726465726564206D656D6265727368697073206F66207468697320726F6C65, 'Questi utenti hanno ordinato l\'iscrizione a questo ruolo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x54686573652075736572732068617665206265656E2061737369676E656420746F207468697320526F6C65, 'Diese Nutzer gehören dieser Rolle an: ', 'de', 'yum');
INSERT INTO `translation` VALUES (0x54686573652075736572732068617665206265656E2061737369676E656420746F207468697320526F6C65, 'A ces membres ont ete attribues ce role:', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x54686573652075736572732068617665206265656E2061737369676E656420746F207468697320526F6C65, 'Questi utenti sono assegnati al ruolo:', 'it', 'yum');
INSERT INTO `translation` VALUES (0x54686573652075736572732068617665206265656E2061737369676E656420746F207468697320526F6C65, 'Uzytkownik zostal przypisany do rol:', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x54686573652075736572732068617665206265656E2061737369676E656420746F207468697320726F6C65, 'Dieser Rolle gehören diese Benutzer an', 'de', 'yum');
INSERT INTO `translation` VALUES (0x54686573652075736572732068617665206265656E2061737369676E656420746F207468697320726F6C65, 'Ce role a bien ete attribue a ces membres', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x54686573652075736572732068617665206265656E2061737369676E656420746F207468697320726F6C65, 'Questo ruolo e assegnato  a questo utente', 'it', 'yum');
INSERT INTO `translation` VALUES (0x54686573652075736572732068617665206265656E2061737369676E656420746F207468697320726F6C65, 'Uzytkownik zostal przypisany do rol', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x5468657365207573657273206861766520636F6D6D656E74656420796F75722070726F66696C6520726563656E746C79, 'Diese Benutzer haben mein Profil kürzlich kommentiert', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5468657365207573657273206861766520636F6D6D656E74656420796F75722070726F66696C6520726563656E746C79, 'Cet utilisateur a commente recemment votre profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5468657365207573657273206861766520636F6D6D656E74656420796F75722070726F66696C6520726563656E746C79, 'Questo utente ha recentemente commentato sul tuo profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x546865736520757365727320686176652076697369746564206D792070726F66696C65, 'Diese Benutzer haben mein Profil besucht', 'de', 'yum');
INSERT INTO `translation` VALUES (0x546865736520757365727320686176652076697369746564206D792070726F66696C65, 'Les membres ayant visite mon profil.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x546865736520757365727320686176652076697369746564206D792070726F66696C65, 'Questi utenti hanno visitato il tuo profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x54686573652075736572732068617665207669736974656420796F75722070726F66696C6520726563656E746C79, 'Diese Benutzer haben kürzlich mein Profil besucht', 'de', 'yum');
INSERT INTO `translation` VALUES (0x54686573652075736572732068617665207669736974656420796F75722070726F66696C6520726563656E746C79, 'Cet utilisateur a visite votre profil recemment', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x54686573652075736572732068617665207669736974656420796F75722070726F66696C6520726563656E746C79, 'Questi utenti hanno recentemente visitato il tuo profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x54686973206163636F756E7420697320626C6F636B65642E, 'Ihr Konto wurde blockiert.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x54686973206163636F756E7420697320626C6F636B65642E, 'Esta cuenta está bloqueada.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x54686973206163636F756E7420697320626C6F636B65642E, 'Votre compte a ete bloque. Contactez nous.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x54686973206163636F756E7420697320626C6F636B65642E, 'Il tuo account e stato bloccato.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x54686973206163636F756E7420697320626C6F636B65642E, 'To konto jest zablokowane.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x54686973206163636F756E74206973206E6F74206163746976617465642E, 'Ihr Konto wurde nicht aktiviert.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x54686973206163636F756E74206973206E6F74206163746976617465642E, 'Esta cuenta no está activada.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x54686973206163636F756E74206973206E6F74206163746976617465642E, 'Votre compte n a pas ete active.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x54686973206163636F756E74206973206E6F74206163746976617465642E, 'Il tuo account non e attivato.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x54686973206163636F756E74206973206E6F74206163746976617465642E, 'To konto nie zostalo jeszcze aktywowane.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x54686973206D656D62657273686970206973207374696C6C20616374697665207B646179737D2064617973, 'Die Mitgliedschaft ist noch {days} Tage aktiv', 'de', 'yum');
INSERT INTO `translation` VALUES (0x54686973206D656D62657273686970206973207374696C6C207B646179737D206461797320616374697665, 'Esta membres', 'es', 'yum');
INSERT INTO `translation` VALUES (0x54686973206D656D62657273686970206973207374696C6C207B646179737D206461797320616374697665, 'L\'iscrizione e ancora attiva per {days} giorni', 'it', 'yum');
INSERT INTO `translation` VALUES (0x54686973206D6573736167652077696C6C2062652073656E7420746F207B757365726E616D657D, 'Diese Nachricht wird an {username} versandt', 'de', 'yum');
INSERT INTO `translation` VALUES (0x54686973206D6573736167652077696C6C2062652073656E7420746F207B757365726E616D657D, 'Este mensaje será enviado a {username}', 'es', 'yum');
INSERT INTO `translation` VALUES (0x54686973206D6573736167652077696C6C2062652073656E7420746F207B757365726E616D657D, 'Ce message sera envoye a {username}', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x54686973206D6573736167652077696C6C2062652073656E7420746F207B757365726E616D657D, 'Questo messaggio verra inviato a {username}', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5468697320726F6C652063616E2061646D696E6973746572207573657273206F66207468697320726F6C6573, 'Este rol puede administrar usuarios de estos roles', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5468697320726F6C652063616E2061646D696E6973746572207573657273206F66207468697320726F6C6573, 'Membres ayant ce role peuvent administrer ces utilisateurs', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5468697320726F6C652063616E2061646D696E6973746572207573657273206F66207468697320726F6C6573, 'Questo ruolo puo amministrare utenti di questo ruolo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5468697320757365722062656C6F6E677320746F20746865736520726F6C65733A, 'Benutzer gehört diesen Rollen an:', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5468697320757365722062656C6F6E677320746F20746865736520726F6C65733A, 'Este usuario pertenece a estos roles:', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5468697320757365722062656C6F6E677320746F20746865736520726F6C65733A, 'A ce membre a ete attribue ces roles:', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5468697320757365722062656C6F6E677320746F20746865736520726F6C65733A, 'L\'Utente appartiene a questi ruoli:', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5468697320757365722062656C6F6E677320746F20746865736520726F6C65733A, 'Uzytkownik posiada role:', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x5468697320757365722063616E2061646D696E69737465722074686973207573657273, 'Dieser Benutzer kann diese Nutzer administrieren', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5468697320757365722063616E2061646D696E69737465722074686973207573657273, 'Este usuario puede administrar estos usuarios', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5468697320757365722063616E2061646D696E69737465722074686973207573657273, 'Ce membre peut gerer ces utilisateurs.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5468697320757365722063616E2061646D696E69737465722074686973207573657273, 'Gli utenti possono gestire questi utenti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5468697320757365722063616E2061646D696E697374657220746869732075736572733A, 'Benutzer kann diese Benutzer verwalten:', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5468697320757365722063616E2061646D696E697374657220746869732075736572733A, 'Este usuario puede administrar estos usuarios:', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5468697320757365722063616E2061646D696E697374657220746869732075736572733A, 'Ce membre peut administrer ces membres:', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5468697320757365722063616E2061646D696E697374657220746869732075736572733A, 'Gli utenti possono gestire questi utenti:', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5468697320757365722063616E2061646D696E697374657220746869732075736572733A, 'Uzytkownik moze zarzadzaj nastepujacymi uzytkownikami:', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x5468697320757365722063616E2061646D696E697374726174652074686973207573657273, 'Uzytkownik moze administrowac podanymi uzytkownikami', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x546869732075736572277320656D61696C206164647265737320616C7265616479206578697374732E, 'Indirizzo email esistente.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x546869732075736572277320656D61696C2061647265737320616C7265616479206578697374732E, 'Der Benutzer E-Mail-Adresse existiert bereits.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x546869732075736572277320656D61696C2061647265737320616C7265616479206578697374732E, 'La dirección de e-mail de este usuario ya existe.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x546869732075736572277320656D61696C2061647265737320616C7265616479206578697374732E, 'Cette adresse e-mail existe deja dans notre banque de donnee.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x546869732075736572277320656D61696C2061647265737320616C7265616479206578697374732E, 'Indirizzo e-mail gia esistente.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x546869732075736572277320656D61696C2061647265737320616C7265616479206578697374732E, 'Podany adres melopwy jest w uzyciu', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x546869732075736572277320656D61696C2061647265737320616C7265616479206578697374732E, 'Пользователь с таким электронным адресом уже существует.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x5468697320757365722773206E616D6520616C7265616479206578697374732E, 'Der Benutzer Name existiert bereits.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5468697320757365722773206E616D6520616C7265616479206578697374732E, 'Este nombre de usuario ya existe.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5468697320757365722773206E616D6520616C7265616479206578697374732E, 'Ce nom d utilisateur existe deja dans notre banque de donnee.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5468697320757365722773206E616D6520616C7265616479206578697374732E, 'Nome esistenze', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5468697320757365722773206E616D6520616C7265616479206578697374732E, 'Podana nazwa uzytkownika jest w uzyciu.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x5468697320757365722773206E616D6520616C7265616479206578697374732E, 'Пользователь с таким именем уже существует.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x5468697320757365727320686176652061206F726465726564206D656D6265727368697073206F66207468697320726F6C65, 'Estos usuarios tienen una membres', 'es', 'yum');
INSERT INTO `translation` VALUES (0x546869732075736572732068617665206265656E2061737369676E656420746F207468697320526F6C65, 'Este usuario ha sido asignado a este Rol', 'es', 'yum');
INSERT INTO `translation` VALUES (0x546869732075736572732068617665206265656E2061737369676E656420746F207468697320726F6C65, 'Este usuario ha sido asignado a este rol', 'es', 'yum');
INSERT INTO `translation` VALUES (0x54686973207573657273206861766520636F6D6D656E74656420796F75722070726F66696C6520726563656E746C79, 'Estos usuarios han comentado su perfil recientemente', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5468697320757365727320686176652076697369746564206D792070726F66696C65, 'Estos usuarios han visitado mi perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x546869732075736572732068617665207669736974656420796F75722070726F66696C6520726563656E746C79, 'Estos usuarios han visitado tu perfil recientemente', 'es', 'yum');
INSERT INTO `translation` VALUES (0x54696D65206C656674, 'Zeit übrig', 'de', 'yum');
INSERT INTO `translation` VALUES (0x54696D65206C656674, 'Tiempo restante', 'es', 'yum');
INSERT INTO `translation` VALUES (0x54696D652073656E74, 'Gesendet am', 'de', 'yum');
INSERT INTO `translation` VALUES (0x54696D652073656E74, 'Hora de envío', 'es', 'yum');
INSERT INTO `translation` VALUES (0x54696D652073656E74, 'Envoye le', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x54696D652073656E74, 'Pubblicato su', 'it', 'yum');
INSERT INTO `translation` VALUES (0x54696D652073656E74, 'Wyslano', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x5469746C65, 'Titel', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5469746C65, 'Título', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5469746C65, 'Titre', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5469746C65, 'Titolo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5469746C65, 'Название', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x546F, 'An', 'de', 'yum');
INSERT INTO `translation` VALUES (0x546F, 'Para', 'es', 'yum');
INSERT INTO `translation` VALUES (0x546F, 'A', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x546F, 'A', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5472616E736C6174696F6E, 'Übersetzung', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5472616E736C6174696F6E, 'Traducción', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5472616E736C6174696F6E732068617665206265656E207361766564, 'Die Übersetzungen wurden gespeichert', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5472616E736C6174696F6E732068617665206265656E207361766564, 'Las traducciones han sido salvadas', 'es', 'yum');
INSERT INTO `translation` VALUES (0x54727920616761696E, 'Erneut versuchen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x54727920616761696E, 'Intenta de nuevo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x54727920616761696E, 'Essayer a nouveau', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x54727920616761696E, 'Prova di nuovo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x54727920616761696E, 'Sprobuj jeszcze raz', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x557064617465, 'Bearbeiten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x557064617465, 'Actualizar', 'es', 'yum');
INSERT INTO `translation` VALUES (0x557064617465, 'Modifier', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x557064617465, 'Aggiorna', 'it', 'yum');
INSERT INTO `translation` VALUES (0x557064617465, 'Zmien', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x5570646174652050726F66696C65204669656C64, 'Profilfeld bearbeiten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5570646174652050726F66696C65204669656C64, 'Actualizar Campo del Perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5570646174652050726F66696C65204669656C64, 'Modifier le champ du profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5570646174652050726F66696C65204669656C64, 'Aggiorna campo Profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5570646174652050726F66696C65204669656C64, 'Zmien pole w profilu', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x5570646174652050726F66696C65204669656C64, 'Править', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x5570646174652055736572, 'Benutzer bearbeiten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5570646174652055736572, 'Actualizar Usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5570646174652055736572, 'Gerer les membres', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5570646174652055736572, 'Aggiorna utenti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5570646174652055736572, 'Править', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x557064617465206D792070726F66696C65, 'Mein Profil bearbeiten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x557064617465206D792070726F66696C65, 'Actualizar mi perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x557064617465206D792070726F66696C65, 'Aggiorna profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x557064617465207061796D656E74, 'Zahlungsart bearbeiten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x557064617465207061796D656E74, 'Actualizar el pago', 'es', 'yum');
INSERT INTO `translation` VALUES (0x557064617465207061796D656E74, 'Aggiorna pagamento', 'it', 'yum');
INSERT INTO `translation` VALUES (0x55706461746520726F6C65, 'Rolle bearbeiten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x55706461746520726F6C65, 'Actualizar rol', 'es', 'yum');
INSERT INTO `translation` VALUES (0x55706461746520726F6C65, 'Modifier les roles', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x55706461746520726F6C65, 'Aggiorna ruolo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x55706461746520726F6C65, 'Edytuj role', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x5570646174652075736572, 'Benutzer bearbeiten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5570646174652075736572, 'Actualizar usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5570646174652075736572, 'Modifier un membre', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5570646174652075736572, 'Aggiorna utente', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5570646174652075736572, 'Zmien uzytkownika', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x5570677261646520746F207B726F6C657D, 'Wechsle auf {role}', 'de', 'yum');
INSERT INTO `translation` VALUES (0x55706C6F616420417661746172, 'Subir un Avatar', 'es', 'yum');
INSERT INTO `translation` VALUES (0x55706C6F616420417661746172, 'Charger une image de profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x55706C6F616420417661746172, 'Carica avatar', 'it', 'yum');
INSERT INTO `translation` VALUES (0x55706C6F616420617661746172, 'Profilbild hochladen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x55706C6F616420617661746172, 'Subir un avatar', 'es', 'yum');
INSERT INTO `translation` VALUES (0x55706C6F616420617661746172, 'Charger une image de profil maintenant', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x55706C6F616420617661746172, 'Carica avatar', 'it', 'yum');
INSERT INTO `translation` VALUES (0x55706C6F61642061766174617220496D616765, 'Carica avatar', 'it', 'yum');
INSERT INTO `translation` VALUES (0x55706C6F61642061766174617220696D616765, 'Profilbild hochladen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x55706C6F61642061766174617220696D616765, 'Cargar imagen de perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x55706C6F61642061766174617220696D616765, 'Charger une image pour votre profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x55706C6F61642061766174617220696D616765, 'Carica immagine avatar', 'it', 'yum');
INSERT INTO `translation` VALUES (0x557365206D79204772617661746172, 'Meinen Gravatar benutzen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x557365206D79204772617661746172, 'Usar mi Gravatar', 'es', 'yum');
INSERT INTO `translation` VALUES (0x55736572, 'Benutzer', 'de', 'yum');
INSERT INTO `translation` VALUES (0x55736572, 'Usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x55736572, 'Utilisateur', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x55736572, 'Utente', 'it', 'yum');
INSERT INTO `translation` VALUES (0x557365722041646D696E697374726174696F6E, 'Benutzerverwaltung', 'de', 'yum');
INSERT INTO `translation` VALUES (0x557365722041646D696E697374726174696F6E, 'Administración de usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x557365722041646D696E697374726174696F6E, 'Gestion des membres', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x557365722041646D696E697374726174696F6E, 'Gestione utente', 'it', 'yum');
INSERT INTO `translation` VALUES (0x557365722041646D696E697374726174696F6E, 'Zarzadzanie uzytkownikami', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x55736572204D616E6167656D656E7420486F6D65, 'Benutzerverwaltung Startseite', 'de', 'yum');
INSERT INTO `translation` VALUES (0x55736572204D616E6167656D656E7420486F6D65, 'Administración de usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x55736572204D616E6167656D656E7420486F6D65, 'Page de gestion des membres', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x55736572204D616E6167656D656E7420486F6D65, 'Home gestione utente', 'it', 'yum');
INSERT INTO `translation` VALUES (0x55736572204D616E6167656D656E7420486F6D65, 'Strona startowa profilu', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x55736572204D616E6167656D656E742073657474696E677320636F6E66696775726174696F6E, 'Einstellungen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x55736572204D616E6167656D656E742073657474696E677320636F6E66696775726174696F6E, 'Ajustes de configuración de la Administración de usuarios', 'es', 'yum');
INSERT INTO `translation` VALUES (0x55736572204D616E6167656D656E742073657474696E677320636F6E66696775726174696F6E, 'Options de configuration des profils', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x55736572204D616E6167656D656E742073657474696E677320636F6E66696775726174696F6E, 'Configurazione impostazioni gestione utente', 'it', 'yum');
INSERT INTO `translation` VALUES (0x55736572204F7065726174696F6E73, 'Benutzeraktionen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x55736572204F7065726174696F6E73, 'Operaciones de usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x55736572204F7065726174696F6E73, 'Action de l utilisateur', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x55736572204F7065726174696F6E73, 'Azioni utente', 'it', 'yum');
INSERT INTO `translation` VALUES (0x55736572204F7065726174696F6E73, 'Czynnosci uzytkownika', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x557365722061637469766174696F6E, 'User-Aktivierung', 'de', 'yum');
INSERT INTO `translation` VALUES (0x557365722061637469766174696F6E, 'Activación de usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x557365722061637469766174696F6E, 'Activation du compte utilisateur', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x557365722061637469766174696F6E, 'Attivazione utente', 'it', 'yum');
INSERT INTO `translation` VALUES (0x557365722061637469766174696F6E, 'User-Aktivierung', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x557365722061637469766174696F6E, 'Активация пользователя', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x557365722061646D696E697374726174696F6E2050616E656C, 'Benutzerkontrollzentrum', 'de', 'yum');
INSERT INTO `translation` VALUES (0x557365722061646D696E697374726174696F6E2050616E656C, 'Panel de administración de usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x557365722061646D696E697374726174696F6E2050616E656C, 'Centre de controle des membres', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x557365722061646D696E697374726174696F6E2050616E656C, 'Pannello di controllo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x557365722061646D696E697374726174696F6E2050616E656C, 'Panel zarzadzania uzytkownika', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x557365722061646D696E697374726174696F6E2070616E656C, 'Kontrollzentrum', 'de', 'yum');
INSERT INTO `translation` VALUES (0x557365722061646D696E697374726174696F6E2070616E656C, 'Panel de administración de usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x557365722061646D696E697374726174696F6E2070616E656C, 'Centre de controle user', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x557365722061646D696E697374726174696F6E2070616E656C, 'Pannello di controllo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x557365722061646D696E697374726174696F6E2070616E656C, 'Panel zarzadzania uzytkownikiem', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x557365722062656C6F6E677320746F20526F6C6573, 'Benutzer gehört diesen Rollen an', 'de', 'yum');
INSERT INTO `translation` VALUES (0x557365722062656C6F6E677320746F20526F6C6573, 'El usuario pertenece al los Roles', 'es', 'yum');
INSERT INTO `translation` VALUES (0x557365722062656C6F6E677320746F20526F6C6573, 'Attribuer des roles a un membre', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x557365722062656C6F6E677320746F20526F6C6573, 'Utente appartiene a questi ruoli', 'it', 'yum');
INSERT INTO `translation` VALUES (0x557365722062656C6F6E677320746F20526F6C6573, 'Uzytkownik posiada role', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x557365722062656C6F6E677320746F20746865736520726F6C6573, 'Benutzer gehört diesen Rollen an', 'de', 'yum');
INSERT INTO `translation` VALUES (0x557365722062656C6F6E677320746F20746865736520726F6C6573, 'El usuario pertenece a estos roles', 'es', 'yum');
INSERT INTO `translation` VALUES (0x557365722062656C6F6E677320746F20746865736520726F6C6573, 'Attribuer ce role a un membre', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x557365722062656C6F6E677320746F20746865736520726F6C6573, 'Utente appartiene a questi ruoli', 'it', 'yum');
INSERT INTO `translation` VALUES (0x557365722062656C6F6E677320746F20746865736520726F6C6573, 'Uzytkownik posiada role', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x557365722063616E206E6F742061646D696E697374657220616E79207573657273, 'Kann keine Benutzer verwalten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x557365722063616E206E6F742061646D696E697374657220616E79207573657273, 'El usuario no puede administrar ningún usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x557365722063616E206E6F742061646D696E697374657220616E79207573657273, 'Ne peut pas gerer les utilisateurs', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x557365722063616E206E6F742061646D696E697374657220616E79207573657273, 'Impossibile gestire gli utenti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x557365722063616E206E6F742061646D696E697374657220616E79207573657273, 'Uzytkownik nie moze zarzadzac zadnymi uzytkownikami', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x557365722063616E206E6F742061646D696E697374657220616E79207573657273206F6620616E7920726F6C65, 'Kann keine Rollen verwalten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x557365722063616E206E6F742061646D696E697374657220616E79207573657273206F6620616E7920726F6C65, 'El usuario no puede administrar ningún usuario o ningún rol', 'es', 'yum');
INSERT INTO `translation` VALUES (0x557365722063616E206E6F742061646D696E697374657220616E79207573657273206F6620616E7920726F6C65, 'Ne peut pas gerer les rolles', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x557365722063616E206E6F742061646D696E697374657220616E79207573657273206F6620616E7920726F6C65, 'Impossibile gestire i ruoli', 'it', 'yum');
INSERT INTO `translation` VALUES (0x557365722063616E206E6F742061646D696E697374657220616E79207573657273206F6620616E7920726F6C65, 'Uzytkownik nie moze zarzadzac zadnymi rolami uzytkownikow', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x557365722063616E206E6F7420626520666F756E64, 'Benutzer kann nicht gefunden werden', 'de', 'yum');
INSERT INTO `translation` VALUES (0x55736572206973204F6E6C696E6521, 'Benutzer ist Online!', 'de', 'yum');
INSERT INTO `translation` VALUES (0x55736572206973204F6E6C696E6521, 'El usuario est', 'es', 'yum');
INSERT INTO `translation` VALUES (0x55736572206973204F6E6C696E6521, 'Utilisateur en ligne!', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x55736572206973204F6E6C696E6521, 'Utente online!', 'it', 'yum');
INSERT INTO `translation` VALUES (0x55736572206973206E6F7420616374697665, 'Benutzer ist nicht aktiv', 'de', 'yum');
INSERT INTO `translation` VALUES (0x55736572206D6F64756C652073657474696E6773, 'Moduleinstellungen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x55736572206D6F64756C652073657474696E6773, 'Ajustes del módulo de usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x55736572206D6F64756C652073657474696E6773, 'Reglages du module user', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x55736572206D6F64756C652073657474696E6773, 'Modulo impostazioni utente', 'it', 'yum');
INSERT INTO `translation` VALUES (0x55736572206D6F64756C652073657474696E6773, 'Ustawienia modulu uzytkownika', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x5573657267726F757073, 'Benutzergruppen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5573657267726F757073, 'Grupos del usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5573657267726F757073, 'Utilisateur des grouppes', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5573657267726F757073, 'Gruppi utenti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x557365726E616D65, 'Benutzername', 'de', 'yum');
INSERT INTO `translation` VALUES (0x557365726E616D65, 'Usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x557365726E616D65, 'Benutzername', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x557365726E616D65, 'Username', 'it', 'yum');
INSERT INTO `translation` VALUES (0x557365726E616D65, 'Uzytkownik', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x557365726E616D6520697320696E636F72726563742E, 'Benutzername ist falsch.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x557365726E616D6520697320696E636F72726563742E, 'Nombre de usuario incorrecto', 'es', 'yum');
INSERT INTO `translation` VALUES (0x557365726E616D6520697320696E636F72726563742E, 'Le nom d utilisateur est incorrect.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x557365726E616D6520697320696E636F72726563742E, 'Username non corretto.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x557365726E616D6520697320696E636F72726563742E, 'Nazwa uzytkownika jest niepoprawna.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x557365726E616D6520697320696E636F72726563742E, 'Пользователь с таким именем не зарегистрирован.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x557365726E616D65206F7220456D61696C, 'Benutzername oder E-mail', 'de', 'yum');
INSERT INTO `translation` VALUES (0x557365726E616D65206F7220456D61696C, 'Nombre de usuario o Email', 'es', 'yum');
INSERT INTO `translation` VALUES (0x557365726E616D65206F7220456D61696C, 'Nom d utilisateur ou adresse e-mail.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x557365726E616D65206F7220456D61696C, 'Username o email', 'it', 'yum');
INSERT INTO `translation` VALUES (0x557365726E616D65206F722050617373776F726420697320696E636F7272656374, 'Benutzername oder Passwort ist falsch', 'de', 'yum');
INSERT INTO `translation` VALUES (0x557365726E616D65206F722050617373776F726420697320696E636F7272656374, 'Usuario o contraseña incorrectos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x557365726E616D65206F722050617373776F726420697320696E636F7272656374, 'Nom d utilisateur ou mot passe incorrect', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x557365726E616D65206F722050617373776F726420697320696E636F7272656374, 'Username o password errato/a', 'it', 'yum');
INSERT INTO `translation` VALUES (0x557365726E616D65206F7220656D61696C, 'Benutzername oder E-Mail', 'de', 'yum');
INSERT INTO `translation` VALUES (0x557365726E616D65206F7220656D61696C, 'Nombre de usuario o correo electr', 'es', 'yum');
INSERT INTO `translation` VALUES (0x557365726E616D65206F7220656D61696C, 'Nom d utilisateur ou adresse e-mail', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x557365726E616D65206F7220656D61696C, 'Username o email', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5573657273, 'Usuarios', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5573657273, 'Utilisateur', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5573657273, 'Utenti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5573657273, 'Пользователи', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x55736572733A, 'Membres:', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x55736572733A, 'Utenti:', 'it', 'yum');
INSERT INTO `translation` VALUES (0x55736572733A, 'Uzytkownicy:', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x55736572733A20, 'Benutzer: ', 'de', 'yum');
INSERT INTO `translation` VALUES (0x55736572733A20, 'Usuarios:', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5661726961626C65206E616D65, 'Variablen name', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5661726961626C65206E616D65, 'Nombre de variable', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5661726961626C65206E616D65, 'Nom de la variable', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5661726961626C65206E616D65, 'Nome variabile', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5661726961626C65206E616D65, 'Имя переменной', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x566572696669636174696F6E20436F6465, 'Codice verifica', 'it', 'yum');
INSERT INTO `translation` VALUES (0x566572696669636174696F6E20436F6465, 'Kod weryfikujacy', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x566572696669636174696F6E20436F6465, 'Проверочный код', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x566572696669636174696F6E20636F6465, 'Verifizierung', 'de', 'yum');
INSERT INTO `translation` VALUES (0x566572696669636174696F6E20636F6465, 'Código de verificación', 'es', 'yum');
INSERT INTO `translation` VALUES (0x566572696669636174696F6E20636F6465, 'Code de verification', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x566572696669636174696F6E20636F6465, 'Codice verifica', 'it', 'yum');
INSERT INTO `translation` VALUES (0x56696577, 'Anzeigen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x56696577, 'Ver', 'es', 'yum');
INSERT INTO `translation` VALUES (0x56696577, 'Editer', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x56696577, 'Visualizza', 'it', 'yum');
INSERT INTO `translation` VALUES (0x56696577, 'Polaz', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x566965772044657461696C73, 'Zur Gruppe', 'de', 'yum');
INSERT INTO `translation` VALUES (0x566965772044657461696C73, 'Ver detalles', 'es', 'yum');
INSERT INTO `translation` VALUES (0x566965772044657461696C73, 'Mostra dettagli', 'it', 'yum');
INSERT INTO `translation` VALUES (0x566965772050726F66696C65204669656C64, 'Mostra campo Profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x566965772050726F66696C65204669656C64, 'Просмотр', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x566965772050726F66696C65204669656C642023, 'Mostra # campo Profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x566965772050726F66696C65204669656C642023, 'Поле профиля #', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x566965772055736572, 'Benutzer anzeigen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x566965772055736572, 'Ver Usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x566965772055736572, 'Consulter le profil du membre', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x566965772055736572, 'Mostra utente', 'it', 'yum');
INSERT INTO `translation` VALUES (0x566965772055736572, 'Просмотр профиля', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x566965772061646D696E206D65737361676573, 'Administratornachrichten anzeigen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x566965772061646D696E206D65737361676573, 'Ver mensajes de admin', 'es', 'yum');
INSERT INTO `translation` VALUES (0x566965772061646D696E206D65737361676573, 'Voir les messages de l administateur', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x566965772061646D696E206D65737361676573, 'Visualizza messaggi amministratore', 'it', 'yum');
INSERT INTO `translation` VALUES (0x566965772061646D696E206D65737361676573, 'Pokaz wiadomosci administratora', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x56696577206D79206D65737361676573, 'Meine Nachrichten ansehen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x56696577206D79206D65737361676573, 'Ver mis mensajes', 'es', 'yum');
INSERT INTO `translation` VALUES (0x56696577206D79206D65737361676573, 'Voir mes messages', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x56696577206D79206D65737361676573, 'Visualizza messaggi', 'it', 'yum');
INSERT INTO `translation` VALUES (0x56696577206D79206D65737361676573, 'Wyswietl moje wiadomosci', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x56696577207573657220227B757365726E616D657D22, 'Benutzer \"{username}\"', 'de', 'yum');
INSERT INTO `translation` VALUES (0x56696577207573657220227B757365726E616D657D22, 'Ver usuario \"{username}\"', 'es', 'yum');
INSERT INTO `translation` VALUES (0x566965772075736572202671756F743B7B757365726E616D657D2671756F743B, 'Membre \"{username}\"', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x566965772075736572202671756F743B7B757365726E616D657D2671756F743B, 'Visualizza utente \"{username}\"', 'it', 'yum');
INSERT INTO `translation` VALUES (0x566965772075736572202671756F743B7B757365726E616D657D2671756F743B, 'Uzytkownik \"{username}\"', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x56696577207573657273, 'Benutzer anzeigen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x56696577207573657273, 'Ver usuarios', 'es', 'yum');
INSERT INTO `translation` VALUES (0x56696577207573657273, 'Montrer les utilisateurs', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x56696577207573657273, 'Visualizza utenti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x56696577207573657273, 'Pokaz uzytkownika', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x56697369626C65, 'Sichtbar', 'de', 'yum');
INSERT INTO `translation` VALUES (0x56697369626C65, 'Visible', 'es', 'yum');
INSERT INTO `translation` VALUES (0x56697369626C65, 'Visible', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x56697369626C65, 'Visibile', 'it', 'yum');
INSERT INTO `translation` VALUES (0x56697369626C65, 'Видимость', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x56697369742070726F66696C65, 'Profil besuchen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x56697369742070726F66696C65, 'Ver el perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x56697369742070726F66696C65, 'Visiter le profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x56697369742070726F66696C65, 'Visita profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5768656E2073656C656374696E672073656172636861626C652C207573657273206F66207468697320726F6C652063616E20626520736561726368656420696E207468652022757365722042726F777365222066756E6374696F6E, 'Wenn \"suchbar\" ausgewählt wird, kann man Nutzer dieser Rolle in der \"Benutzer durchsuchen\"-Funktion suchen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5768656E2073656C656374696E672073656172636861626C652C207573657273206F66207468697320726F6C652063616E20626520736561726368656420696E207468652022757365722042726F777365222066756E6374696F6E, 'Al seleccionar b', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5768656E2073656C656374696E672073656172636861626C652C207573657273206F66207468697320726F6C652063616E20626520736561726368656420696E20746865202671756F743B757365722042726F7773652671756F743B2066756E6374696F6E, 'Si le status de \"visible\" est choisi, un membre de ce role pourra apparaitre dans les resultats d une recherche', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5768656E2073656C656374696E672073656172636861626C652C207573657273206F66207468697320726F6C652063616E20626520736561726368656420696E20746865202671756F743B757365722042726F7773652671756F743B2066756E6374696F6E, 'Quando selezioni \"Ricercabile\", gli utenti di questo ruolo sono ricercabili nella funzione \"Browser utenti\"', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5768656E20746865206D656D626572736869702065787069726573, 'Wenn die Mitgliedschaft abläuft', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5772697465206120636F6D6D656E74, 'Kommentar hinterlassen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5772697465206120636F6D6D656E74, 'Escribir un comentario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5772697465206120636F6D6D656E74, 'Laisser un commentaire', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5772697465206120636F6D6D656E74, 'Scrivi commento', 'it', 'yum');
INSERT INTO `translation` VALUES (0x57726974652061206D657373616765, 'Nachricht schreiben', 'de', 'yum');
INSERT INTO `translation` VALUES (0x57726974652061206D657373616765, 'Escribir un mensaje', 'es', 'yum');
INSERT INTO `translation` VALUES (0x57726974652061206D657373616765, 'Ecrire un message', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x57726974652061206D657373616765, 'Scrivi messaggio', 'it', 'yum');
INSERT INTO `translation` VALUES (0x57726974652061206D657373616765, 'Napisz wiadomosc', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x57726974652061206D65737361676520746F20746869732055736572, 'Diesem Benutzer eine Nachricht schreiben', 'de', 'yum');
INSERT INTO `translation` VALUES (0x57726974652061206D65737361676520746F20746869732055736572, 'Escribir un mensaje a este Usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x57726974652061206D65737361676520746F20746869732055736572, 'Ecrire un message a ce membre', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x57726974652061206D65737361676520746F20746869732055736572, 'Scrivi messaggio a questo utente', 'it', 'yum');
INSERT INTO `translation` VALUES (0x57726974652061206D65737361676520746F207B757365726E616D657D, 'Nachricht an {username} schreiben', 'de', 'yum');
INSERT INTO `translation` VALUES (0x57726974652061206D65737361676520746F207B757365726E616D657D, 'Escribir un mensaje a {username}', 'es', 'yum');
INSERT INTO `translation` VALUES (0x57726974652061206D65737361676520746F207B757365726E616D657D, 'Message ecrire a {username}', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x57726974652061206D65737361676520746F207B757365726E616D657D, 'Scrivi messaggio a {username}', 'it', 'yum');
INSERT INTO `translation` VALUES (0x577269746520616E6F74686572206D657373616765, 'Eine weitere Nachricht schreiben', 'de', 'yum');
INSERT INTO `translation` VALUES (0x577269746520616E6F74686572206D657373616765, 'Escribir otro mensaje', 'es', 'yum');
INSERT INTO `translation` VALUES (0x577269746520616E6F74686572206D657373616765, 'Ecrire un autre message', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x577269746520616E6F74686572206D657373616765, 'Scrivi un\'altro messaggio', 'it', 'yum');
INSERT INTO `translation` VALUES (0x577269746520616E6F74686572206D657373616765, 'Eine weitere Nachricht schreiben', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x577269746520636F6D6D656E74, 'Kommentar schreiben', 'de', 'yum');
INSERT INTO `translation` VALUES (0x577269746520636F6D6D656E74, 'Escribir comentario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x577269746520636F6D6D656E74, 'Ecrire un commentaire', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x577269746520636F6D6D656E74, 'Scrivi commento', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5772697465206D657373616765, 'Nachricht schreiben', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5772697465206D657373616765, 'Escribir un mensaje', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5772697474656E206174, 'Geschrieben am', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5772697474656E206174, 'Escrito el', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5772697474656E206174, 'Ecrit le', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5772697474656E206174, 'Scritto a', 'it', 'yum');
INSERT INTO `translation` VALUES (0x5772697474656E2066726F6D, 'Geschrieben von', 'de', 'yum');
INSERT INTO `translation` VALUES (0x5772697474656E2066726F6D, 'Escrito por', 'es', 'yum');
INSERT INTO `translation` VALUES (0x5772697474656E2066726F6D, 'Ecrit par', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x5772697474656E2066726F6D, 'Scritto da', 'it', 'yum');
INSERT INTO `translation` VALUES (0x57726F6E672070617373776F726420636F6E6669726D6174696F6E21204163636F756E7420776173206E6F742064656C65746564, 'Falsches Bestätigugspasswort! Zugang wurde nicht gelöscht', 'de', 'yum');
INSERT INTO `translation` VALUES (0x57726F6E672070617373776F726420636F6E6669726D6174696F6E21204163636F756E7420776173206E6F742064656C65746564, '¡Contraseña para confirmación incorrecta! Lacuenta no ha sido eliminada', 'es', 'yum');
INSERT INTO `translation` VALUES (0x57726F6E672070617373776F726420636F6E6669726D6174696F6E21204163636F756E7420776173206E6F742064656C65746564, 'Confirmation incorrecte! Le compte n a pas ete supprime', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x57726F6E672070617373776F726420636F6E6669726D6174696F6E21204163636F756E7420776173206E6F742064656C65746564, 'Password id oconferma errata! Account non cancellato', 'it', 'yum');
INSERT INTO `translation` VALUES (0x57726F6E672070617373776F726420636F6E6669726D6174696F6E21204163636F756E7420776173206E6F742064656C65746564, 'Niepoprawne haslo! Konto nie zostalo usuniete', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x596573, 'Ja', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596573, 'Sí', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596573, 'Oui', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x596573, 'Si', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596573, 'Ja', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x596573, 'Да', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x59657320616E642073686F77206F6E20726567697374726174696F6E20666F726D, 'Ja, und auf Registrierungsseite anzeigen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x59657320616E642073686F77206F6E20726567697374726174696F6E20666F726D, 'Si y mostrar en formulario de registro', 'es', 'yum');
INSERT INTO `translation` VALUES (0x59657320616E642073686F77206F6E20726567697374726174696F6E20666F726D, 'oui et charger le formulaire d inscription', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x59657320616E642073686F77206F6E20726567697374726174696F6E20666F726D, 'Si e mostra nel form di registrazione', 'it', 'yum');
INSERT INTO `translation` VALUES (0x59657320616E642073686F77206F6E20726567697374726174696F6E20666F726D, 'Tak i pokaz w formularzu rejestracji', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x59657320616E642073686F77206F6E20726567697374726174696F6E20666F726D, 'Да и показать при регистрации', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x5969692D757365722D6D616E6167656D656E7420697320616C726561647920696E7374616C6C65642E20506C656173652072656D6F7665206974206D616E75616C6C7920746F20636F6E74696E7565, 'Yii-user-management ist bereits installiert. Bitte löschen Sie es manuell, um fortzufahren', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F75206163636F756E74206973206163746976617465642E, 'Ihr Konto wurde aktiviert.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F75206163636F756E74206973206163746976617465642E, 'Su cuenta está activada.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F75206163636F756E74206973206163746976617465642E, 'Votre compte a bien ete active.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x596F75206163636F756E74206973206163746976617465642E, 'Account attivato', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F75206163636F756E74206973206163746976617465642E, 'Ihr Konto wurde aktiviert.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x596F75206163636F756E74206973206163746976617465642E, 'Ваша учетная запись активирована.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x596F75206163636F756E74206973206163746976652E, 'Ihr Konto ist aktiv.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F75206163636F756E74206973206163746976652E, 'Su cuenta está activa.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F75206163636F756E74206973206163746976652E, 'Votre compte est actif.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x596F75206163636F756E74206973206163746976652E, 'Account attivo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F75206163636F756E74206973206163746976652E, 'Ihr Konto ist aktiv.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x596F75206163636F756E74206973206163746976652E, 'Ваша учетная запись уже активирована.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x596F75206163636F756E7420697320626C6F636B65642E, 'Account bloccato', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F75206163636F756E7420697320626C6F636B65642E, 'Ваш аккаунт заблокирован.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x596F75206163636F756E74206973206E6F74206163746976617465642E, 'Account non attivo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F75206163636F756E74206973206E6F74206163746976617465642E, 'Ваш аккаунт не активирован.', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x596F7520616C72656164792061726520667269656E6473, 'Ihr seid bereits Freunde', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F7520616C72656164792061726520667269656E6473, 'Ya son amigos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F7520616C72656164792061726520667269656E6473, 'Ce membre figure deja dans votre liste de contact', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x596F7520616C72656164792061726520667269656E6473, 'Siete gia in contatto', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F7520617265206E6F7420616C6C6F77656420746F207669657720746869732070726F66696C652E, 'Sie dürfen dieses Profil nicht ansehen.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F7520617265206E6F7420616C6C6F77656420746F207669657720746869732070726F66696C652E, 'No tiene permiso para ver este perfil.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F7520617265206E6F7420616C6C6F77656420746F207669657720746869732070726F66696C652E, 'VOus ne pouvez pas consulter ce profil.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x596F7520617265206E6F7420616C6C6F77656420746F207669657720746869732070726F66696C652E, 'Non puoi vedere questo profilo.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F7520617265206E6F7420616C6C6F77656420746F207669657720746869732070726F66696C652E, 'Nie masz uprawnie do przegladania tego profilu', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x596F75206172652072756E6E696E6720746865205969692055736572204D616E6167656D656E74204D6F64756C65207B76657273696F6E7D20696E204465627567204D6F646521, 'Dies ist das Yii-User-Management Modul in Version {version} im Debug Modus!', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F75206172652072756E6E696E6720746865205969692055736572204D616E6167656D656E74204D6F64756C65207B76657273696F6E7D20696E204465627567204D6F646521, '¡Está ejecutando el Módulo de Administración de Usuarios Yii {version} en modo de depuración!', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F75206172652072756E6E696E6720746865205969692055736572204D616E6167656D656E74204D6F64756C65207B76657273696F6E7D20696E204465627567204D6F646521, 'Dies ist das Yii-User-Management Modul in Version {version} im Debug Modus!', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x596F75206172652072756E6E696E6720746865205969692055736572204D616E6167656D656E74204D6F64756C65207B76657273696F6E7D20696E204465627567204D6F646521, 'Questo e il modulo di YUM versione {version} in modalita debug!', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F75206172652072756E6E696E6720746865205969692055736572204D616E6167656D656E74204D6F64756C65207B76657273696F6E7D20696E204465627567204D6F646521, 'Uruchamiasz modul Yii User Management Modul, wersja {version}, w trybie DEBUG!', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x596F7520646F206E6F74206861766520616E7920667269656E647320796574, 'Ihre Kontaktliste ist leer', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F7520646F206E6F74206861766520616E7920667269656E647320796574, 'No tienes ningún amigo todavía', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F7520646F206E6F74206861766520616E7920667269656E647320796574, 'Votre liste de contact est vide', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x596F7520646F206E6F74206861766520616E7920667269656E647320796574, 'Lista contatti vuota', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F7520646F206E6F7420686176652073657420616E2061766174617220696D61676520796574, 'Es wurde noch kein Profilbild hochgeladen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F7520646F206E6F7420686176652073657420616E2061766174617220696D61676520796574, 'Aún no has subido tu imágen de Avatar', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F7520646F206E6F7420686176652073657420616E2061766174617220696D61676520796574, 'Aucune photo de votre profil disponible', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x596F7520646F206E6F7420686176652073657420616E2061766174617220696D61676520796574, 'Non hai settato un\'avatar', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F752068617665206A6F696E656420746869732067726F7570, 'Sie sind dieser Gruppe beigetreten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F752068617665206A6F696E656420746869732067726F7570, 'Te has unido a este grupo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F752068617665206C65667420746869732067726F7570, 'Du hast diese Gruppe verlassen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F752068617665206E6577204D657373616765732021, 'Sie haben neue Nachrichten !', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F752068617665206E6577204D657373616765732021, '¡Tienes Mensajes nuevos!', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F752068617665206E6577204D657373616765732021, 'Vous avez de nouveaux messages !', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x596F752068617665206E6577204D657373616765732021, 'Hai un nuovo messaggio!', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F752068617665206E6577206D6573736167657321, 'Sie haben neue Nachrichten!', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F752068617665206E6577206D6573736167657321, '¡Tienes mensajes nuevos!', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F752068617665206E6577206D6573736167657321, 'Vous n avez pas de messages!', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x596F752068617665206E6577206D6573736167657321, 'Hai un nuovo messaggio!', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F752068617665206E6577206D6573736167657321, 'Masz nowa wiadomosc!', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x596F752068617665206E6F206D6573736167657320796574, 'Sie haben bisher noch keine Nachrichten', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F752068617665206E6F206D6573736167657320796574, 'Usted no tiene mensajes a', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F752068617665206E6F206D6573736167657320796574, 'Aucun message recent', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x596F752068617665206E6F206D6573736167657320796574, 'Non hai messaggi', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F752068617665207B636F756E747D206E6577204D657373616765732021, 'Sie haben {count} neue Nachricht(en)!', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F752068617665207B636F756E747D206E6577204D657373616765732021, '¡Tienes {count} mensajes nuevos!', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F752068617665207B636F756E747D206E6577204D657373616765732021, 'Vous avez {count} nouveau(x) message(s)!', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x596F752068617665207B636F756E747D206E6577204D657373616765732021, 'Hai {count} nuovi messaggi!', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F752068617665207B636F756E747D206E6577204D657373616765732021, 'Masz {count} nowych wiadomosci !', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x596F7520726567697374657265642066726F6D207B736974655F6E616D657D, 'Sei registrato su {site_name}', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F7572204163636F756E7420686173206265656E206163746976617465642E205468616E6B20796F7520666F7220796F757220726567697374726174696F6E, 'Ihr Zugang wurde aktiviert. Danke für die Registierung', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F7572204163636F756E7420686173206265656E206163746976617465642E205468616E6B20796F7520666F7220796F757220726567697374726174696F6E, 'Su cuenta ha sido activada. Gracias por su inscripci', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F7572204163636F756E7420686173206265656E206163746976617465642E205468616E6B20796F7520666F7220796F757220726567697374726174696F6E2E, 'Votre compte a bien ete active. Merci pour votre inscription.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x596F7572204163636F756E7420686173206265656E206163746976617465642E205468616E6B20796F7520666F7220796F757220726567697374726174696F6E2E, 'Il tuo account e stato attivato. Grazie per la tua registrazione', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F75722041766174617220696D616765, 'Ihr Avatar-Bild', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F75722041766174617220696D616765, 'Tu imagen de Avatar', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F75722041766174617220696D616765, 'Votre image de profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x596F75722041766174617220696D616765, 'Il tuo avatar', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F7572204D65737361676520686173206265656E2073656E742E, 'El Mensaje ha sido enviado.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F7572204D65737361676520686173206265656E2073656E742E, 'Votre message a ete envoye.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x596F7572204D65737361676520686173206265656E2073656E742E, 'Messaggio inviato.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F7572206163636F756E7420686173206265656E206163746976617465642E, 'Tu cuenta ha sido activada.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F7572206163636F756E7420686173206265656E206163746976617465642E205468616E6B20796F7520666F7220796F757220726567697374726174696F6E, 'Ihr Zugang wurde aktiviert. Danke für ihre Registrierung', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F7572206163636F756E7420686173206265656E206163746976617465642E205468616E6B20796F7520666F7220796F757220726567697374726174696F6E, 'Su cuenta ha sido activada. Gracias por su inscripci', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F7572206163636F756E7420686173206265656E206163746976617465642E205468616E6B20796F7520666F7220796F757220726567697374726174696F6E, 'VOtre compte est maintenant actif. Merci de vous etre enregistre', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x596F7572206163636F756E7420686173206265656E206163746976617465642E205468616E6B20796F7520666F7220796F757220726567697374726174696F6E, 'Il tuo account e stato attivato. Grazie per esserti registrato', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F7572206163636F756E7420686173206265656E206163746976617465642E205468616E6B20796F7520666F7220796F757220726567697374726174696F6E2E, 'Tu cuenta ha sido activada. Gracias por registrarte.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F7572206163636F756E7420686173206265656E206163746976617465642E205468616E6B20796F7520666F7220796F757220726567697374726174696F6E2E, 'Twoje konto zostalo aktywowane. Dziekujemy za rejestracje.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x596F7572206163636F756E7420686173206265656E2064656C657465642E, 'Ihr Zugang wurde gelöscht', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F7572206163636F756E7420686173206265656E2064656C657465642E, ' Su cuenta ha sido borrada.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F7572206163636F756E7420686173206265656E2064656C657465642E, 'Votre compte a bien ete supprime', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x596F7572206163636F756E7420686173206265656E2064656C657465642E, 'Il tuo account e stato cancellato.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F75722061637469766174696F6E20737563636565646564, 'Ihre Aktivierung war erfolgreich', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F75722061637469766174696F6E20737563636565646564, '', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F75722061637469766174696F6E20737563636565646564, 'Votre compte a ete active', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x596F75722061637469766174696F6E20737563636565646564, 'Attivazione riuscita', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F7572206368616E6765732068617665206265656E207361766564, 'Ihre Änderungen wurden gespeichert', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F7572206368616E6765732068617665206265656E207361766564, 'Los cambios han sido guardados', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F7572206368616E6765732068617665206265656E207361766564, 'Vos modification ont ete memorisees', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x596F7572206368616E6765732068617665206265656E207361766564, 'Le modifiche sono state salvate', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F7572206368616E6765732068617665206265656E207361766564, 'Twoje zmiany zostaly zapisane', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x596F75722063757272656E742070617373776F7264, 'Ihr aktuelles Passwort', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F75722063757272656E742070617373776F7264, 'Su contrase', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F75722063757272656E742070617373776F7264, 'Votre mot de passe actuel', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x596F75722063757272656E742070617373776F7264, 'La tua password corrente', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F75722063757272656E742070617373776F7264206973206E6F7420636F7272656374, 'Ihr aktuelles Passwort ist nicht korrekt', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F75722063757272656E742070617373776F7264206973206E6F7420636F7272656374, 'Su contrase', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F75722063757272656E742070617373776F7264206973206E6F7420636F7272656374, 'Votre mot de passe actuel n est pas correct', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x596F75722063757272656E742070617373776F7264206973206E6F7420636F7272656374, 'La tua password corrente non e corretta', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F757220667269656E6473686970207265717565737420686173206265656E206163636570746564, 'Ihre Freundschaftsanfrage wurde akzeptiert', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F757220667269656E6473686970207265717565737420686173206265656E206163636570746564, 'Su solicitud de amistad ha sido aceptada', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F757220667269656E6473686970207265717565737420686173206265656E206163636570746564, 'Votre demande de contact a bien ete acceptee', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x596F757220667269656E6473686970207265717565737420686173206265656E206163636570746564, 'La richiesta di contatto e stata accettata', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F7572206D65737361676520686173206265656E2073656E74, 'Ihre Nachricht wurde gesendet', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F7572206D65737361676520686173206265656E2073656E74, 'El mensaje ha sido enviado', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F7572206D65737361676520686173206265656E2073656E74, 'Votre message a bien ete envoye', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x596F7572206D65737361676520686173206265656E2073656E74, 'Il tuo messaggio e stato inviato.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F7572206D65737361676520686173206265656E2073656E74, 'Twoja wiadomosc zostala wyslana', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x596F7572206E65772070617373776F726420686173206265656E2073617665642E, 'Ihr Passwort wurde gespeichert.', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F7572206E65772070617373776F726420686173206265656E2073617665642E, 'La nueva contraseña ha sido guardada.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F7572206E65772070617373776F726420686173206265656E2073617665642E, 'La modification de votre mot de passe a bien ete memorise.', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x596F7572206E65772070617373776F726420686173206265656E2073617665642E, 'La nuova password e stata salvata.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F7572206E65772070617373776F726420686173206265656E2073617665642E, 'Twoje nowe haslo zostalo zapisane.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x596F75722070617373776F72642068617320657870697265642E20506C6561736520656E74657220796F7572206E65772050617373776F72642062656C6F773A, 'Ihr Passwort ist abgelaufen. Bitte geben Sie ein neues Passwort an:', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F75722070617373776F72642068617320657870697265642E20506C6561736520656E74657220796F7572206E65772050617373776F72642062656C6F773A, 'La contraseña ha expirado. Por favor escribe una contraseña nueva abajo:', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F75722070617373776F72642068617320657870697265642E20506C6561736520656E74657220796F7572206E65772050617373776F72642062656C6F773A, 'La duree de vie de votre mot de passe est arrivee a echeance. Veuillez en definir un nouveau:', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x596F75722070617373776F72642068617320657870697265642E20506C6561736520656E74657220796F7572206E65772050617373776F72642062656C6F773A, 'La password e scaduta. Si prega di inserire una nuova password:', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F757220707269766163792073657474696E67732068617665206265656E207361766564, 'Ihre Privatsphären-einstellungen wurden gespeichert', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F757220707269766163792073657474696E67732068617665206265656E207361766564, 'Sus opciones de privacidad se han salvado', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F757220707269766163792073657474696E67732068617665206265656E207361766564, 'La configuration de vos donnees privees a bien ete enregistree', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x596F757220707269766163792073657474696E67732068617665206265656E207361766564, 'Le tue opzioni Privacy sono state salvate', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F75722070726F66696C65, 'Ihr Profil', 'de', 'yum');
INSERT INTO `translation` VALUES (0x596F75722070726F66696C65, 'Tu perfil', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F75722070726F66696C65, 'Ihr Profil', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x596F75722070726F66696C65, 'Il tuo profilo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x596F75722070726F66696C65, 'Ihr Profil', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x596F75722070726F66696C65, 'Ваш профиль', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x596F757220726567697374726174696F6E206469646E277420776F726B2E20506C656173652074727920616E6F7468657220452D4D61696C20616464726573732E20496620746869732070726F626C656D2070657273697374732C20706C6561736520636F6E74616374206F75722053797374656D2041646D696E6973747261746F722E20, 'Tu proceso de registro falló. Por favor intenta con otra cuenta de correo. Si el problema persiste por favor contáctanos.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F75722072657175657374207375636365656465642E20506C6561736520656E7465722062656C6F7720796F7572206E65772070617373776F72643A, 'Tu solicitud fué exitosa. Por favor, escribe a continuación tu nueva contraseña:', 'es', 'yum');
INSERT INTO `translation` VALUES (0x596F757220737562736372697074696F6E2073657474696E6720686173206265656E207361766564, 'Ihre Einstellungen wurden gespeichert', 'de', 'yum');
INSERT INTO `translation` VALUES (0x61626F7574, 'information me concernant', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x61626F7574, 'Informazioni su', 'it', 'yum');
INSERT INTO `translation` VALUES (0x61637469766174696F6E206B6579, 'Aktivierungsschlüssel', 'de', 'yum');
INSERT INTO `translation` VALUES (0x61637469766174696F6E206B6579, 'clave de activación', 'es', 'yum');
INSERT INTO `translation` VALUES (0x61637469766174696F6E206B6579, 'Cle d activation de votre compte', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x61637469766174696F6E206B6579, 'chiave di attivazione', 'it', 'yum');
INSERT INTO `translation` VALUES (0x61637469766174696F6E206B6579, 'Aktivierungsschlussel', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x61637469766174696F6E206B6579, 'Ключ активации', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x626972746864617465, 'Geburtstag', 'de', 'yum');
INSERT INTO `translation` VALUES (0x626972746864617465, 'fecha de nacimiento', 'es', 'yum');
INSERT INTO `translation` VALUES (0x626972746864617465, 'anniversaire', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x626972746864617465, 'Compleanno', 'it', 'yum');
INSERT INTO `translation` VALUES (0x6269727468646179, 'Geburtstag', 'de', 'yum');
INSERT INTO `translation` VALUES (0x6269727468646179, 'cumplea', 'es', 'yum');
INSERT INTO `translation` VALUES (0x6269727468646179, 'date de naissance', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x6269727468646179, 'Compleanno', 'it', 'yum');
INSERT INTO `translation` VALUES (0x6368616E67652050617373776F7264, 'Passwort ändern', 'de', 'yum');
INSERT INTO `translation` VALUES (0x6368616E67652050617373776F7264, 'cambiar Contraseña', 'es', 'yum');
INSERT INTO `translation` VALUES (0x6368616E67652050617373776F7264, 'Changer le mot de passe', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x6368616E67652050617373776F7264, 'Zmien haslo', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x6368616E67652070617373776F7264, 'Passwort ändern', 'de', 'yum');
INSERT INTO `translation` VALUES (0x6368616E67652070617373776F7264, 'cambiar contraseña', 'es', 'yum');
INSERT INTO `translation` VALUES (0x6368616E67652070617373776F7264, 'Modifier le mot de passe', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x6368616E67652070617373776F7264, 'Cambia password', 'it', 'yum');
INSERT INTO `translation` VALUES (0x646F206E6F74206D616B65206D7920667269656E6473207075626C6963, 'Meine Kontakte nicht veröffentlichen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x646F206E6F74206D616B65206D7920667269656E6473207075626C6963, 'no hacer mis amigos p', 'es', 'yum');
INSERT INTO `translation` VALUES (0x646F206E6F74206D616B65206D7920667269656E6473207075626C6963, 'Ne pas rendre publique la liste de mes contacts', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x646F206E6F74206D616B65206D7920667269656E6473207075626C6963, 'Non mostrare i miei contatti pubblicamente', 'it', 'yum');
INSERT INTO `translation` VALUES (0x656D61696C, 'E-Mail', 'de', 'yum');
INSERT INTO `translation` VALUES (0x656D61696C, 'correo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x656D61696C, 'e-Mail', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x656D61696C, 'email', 'it', 'yum');
INSERT INTO `translation` VALUES (0x656D61696C, 'mejl', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x656D61696C2061646472657373, 'correo electrónico', 'es', 'yum');
INSERT INTO `translation` VALUES (0x656D61696C2061646472657373, 'Adres mejlowy', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x66697273746E616D65, 'Vorname', 'de', 'yum');
INSERT INTO `translation` VALUES (0x66697273746E616D65, 'primer nombre', 'es', 'yum');
INSERT INTO `translation` VALUES (0x66697273746E616D65, 'prenom', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x66697273746E616D65, 'Cognome', 'it', 'yum');
INSERT INTO `translation` VALUES (0x667269656E6473206F6E6C79, 'Nur Freunde', 'de', 'yum');
INSERT INTO `translation` VALUES (0x667269656E6473206F6E6C79, 'sólo amigos', 'es', 'yum');
INSERT INTO `translation` VALUES (0x667269656E6473206F6E6C79, 'A mes contacts seulement', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x667269656E6473206F6E6C79, 'Solo contatti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x6C6173746E616D65, 'Nachname', 'de', 'yum');
INSERT INTO `translation` VALUES (0x6C6173746E616D65, 'apellido', 'es', 'yum');
INSERT INTO `translation` VALUES (0x6C6173746E616D65, 'nom de famille', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x6C6173746E616D65, 'Nome', 'it', 'yum');
INSERT INTO `translation` VALUES (0x6D616B65206D7920667269656E6473207075626C6963, 'Meine Kontakte veröffentlichen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x6D616B65206D7920667269656E6473207075626C6963, 'hacer mi amigos p', 'es', 'yum');
INSERT INTO `translation` VALUES (0x6D616B65206D7920667269656E6473207075626C6963, 'Rendre visibles mes contacts', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x6D616B65206D7920667269656E6473207075626C6963, 'Rendi pubblici i miei contatti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x6E6F, 'Nein', 'de', 'yum');
INSERT INTO `translation` VALUES (0x6E6F, 'no', 'es', 'yum');
INSERT INTO `translation` VALUES (0x6E6F, 'Non', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x6E6F, 'No', 'it', 'yum');
INSERT INTO `translation` VALUES (0x6F662075736572, 'von Benutzer', 'de', 'yum');
INSERT INTO `translation` VALUES (0x6F662075736572, 'de usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x6F662075736572, 'de l utilisateur', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x6F662075736572, 'dell\'utente', 'it', 'yum');
INSERT INTO `translation` VALUES (0x6F6E6C7920746F206D7920667269656E6473, 'Nur an meine Freunde veröffentlichen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x6F6E6C7920746F206D7920667269656E6473, 's', 'es', 'yum');
INSERT INTO `translation` VALUES (0x6F6E6C7920746F206D7920667269656E6473, 'Visible seulement pour mes contacts', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x6F6E6C7920746F206D7920667269656E6473, 'solamente ai miei contatti', 'it', 'yum');
INSERT INTO `translation` VALUES (0x70617373776F7264, 'Passwort', 'de', 'yum');
INSERT INTO `translation` VALUES (0x70617373776F7264, 'contraseña', 'es', 'yum');
INSERT INTO `translation` VALUES (0x70617373776F7264, 'mot de passe', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x70617373776F7264, 'password', 'it', 'yum');
INSERT INTO `translation` VALUES (0x70617373776F7264, 'hadlo', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x70617373776F7264, 'Пароль', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x70726976617465, 'Privat', 'de', 'yum');
INSERT INTO `translation` VALUES (0x70726976617465, 'privado', 'es', 'yum');
INSERT INTO `translation` VALUES (0x70726976617465, 'Prive', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x70726976617465, 'Privato', 'it', 'yum');
INSERT INTO `translation` VALUES (0x70726976617465, 'prywatny', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x70726F746563746564, 'Geschützt', 'de', 'yum');
INSERT INTO `translation` VALUES (0x70726F746563746564, 'protegido', 'es', 'yum');
INSERT INTO `translation` VALUES (0x70726F746563746564, 'Protege', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x70726F746563746564, 'Protetto', 'it', 'yum');
INSERT INTO `translation` VALUES (0x70726F746563746564, 'chroniony', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x7075626C6963, 'Öffentlich', 'de', 'yum');
INSERT INTO `translation` VALUES (0x7075626C6963, 'público', 'es', 'yum');
INSERT INTO `translation` VALUES (0x7075626C6963, 'Publique', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x7075626C6963, 'Pubblico', 'it', 'yum');
INSERT INTO `translation` VALUES (0x7075626C6963, 'publiczny', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x737472656574, 'rue', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x737472656574, 'Indirizzo', 'it', 'yum');
INSERT INTO `translation` VALUES (0x74696D657374616D70, 'Zeitstempel', 'de', 'yum');
INSERT INTO `translation` VALUES (0x74696D657374616D70, 'marca de tiempo', 'es', 'yum');
INSERT INTO `translation` VALUES (0x74696D657374616D70, 'tempon de date et heure', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x74696D657374616D70, 'timestamp', 'it', 'yum');
INSERT INTO `translation` VALUES (0x757365726E616D65, 'Benutzername', 'de', 'yum');
INSERT INTO `translation` VALUES (0x757365726E616D65, 'usuario', 'es', 'yum');
INSERT INTO `translation` VALUES (0x757365726E616D65, 'nom d utilisateur', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x757365726E616D65, 'username', 'it', 'yum');
INSERT INTO `translation` VALUES (0x757365726E616D65, 'nazwa uzytkownika', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x757365726E616D65, 'Логин', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x757365726E616D65206F7220656D61696C, 'Benutzername oder E-Mail Adresse', 'de', 'yum');
INSERT INTO `translation` VALUES (0x757365726E616D65206F7220656D61696C, 'nombre de usuario o email', 'es', 'yum');
INSERT INTO `translation` VALUES (0x757365726E616D65206F7220656D61696C, 'nom d utilisateur ou adresse e-mail', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x757365726E616D65206F7220656D61696C, 'username or email', 'it', 'yum');
INSERT INTO `translation` VALUES (0x757365726E616D65206F7220656D61696C, 'nazwa uzytkowniak lub mejl', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x757365726E616D65206F7220656D61696C, 'Логин или email', 'ru', 'yum');
INSERT INTO `translation` VALUES (0x76657269667950617373776F7264, 'Passwort wiederholen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x76657269667950617373776F7264, 'verifique su contrase', 'es', 'yum');
INSERT INTO `translation` VALUES (0x796573, 'Ja, diese Daten veröffentlichen', 'de', 'yum');
INSERT INTO `translation` VALUES (0x796573, 's', 'es', 'yum');
INSERT INTO `translation` VALUES (0x796573, 'Oui, rendre publique ces donnees', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x796573, 'Si', 'it', 'yum');
INSERT INTO `translation` VALUES (0x7A6970636F6465, 'Postleitzahl', 'de', 'yum');
INSERT INTO `translation` VALUES (0x7A6970636F6465, 'c', 'es', 'yum');
INSERT INTO `translation` VALUES (0x7A6970636F6465, 'code postal', 'fr', 'yum');
INSERT INTO `translation` VALUES (0x7A6970636F6465, 'CAP', 'it', 'yum');
INSERT INTO `translation` VALUES (0x7B6174747269627574657D20697320746F6F206C6F6E6720286D61782E207B6E756D7D2063686172616374657273292E, '{attribute} es muy larga (max. {num} caracteres).', 'es', 'yum');
INSERT INTO `translation` VALUES (0x7B6174747269627574657D20697320746F6F206C6F6E6720286D61782E207B6E756D7D2063686172616374657273292E, '{attribute} troppo lungo (max. {num} caratteri).', 'it', 'yum');
INSERT INTO `translation` VALUES (0x7B6174747269627574657D20697320746F6F206C6F6E6720286D61782E207B6E756D7D2063686172616374657273292E, '{attribute} jest zbyt dlugi (max. {num} znakow).', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x7B6174747269627574657D20697320746F6F2073686F727420286D696E2E207B6E756D7D2063686172616374657273292E, '{attribute} es muy corta (min. {num} caracteres).', 'es', 'yum');
INSERT INTO `translation` VALUES (0x7B6174747269627574657D20697320746F6F2073686F727420286D696E2E207B6E756D7D2063686172616374657273292E, '{attribute} troppo corto (min. {num} caratteri).', 'it', 'yum');
INSERT INTO `translation` VALUES (0x7B6174747269627574657D20697320746F6F2073686F727420286D696E2E207B6E756D7D2063686172616374657273292E, '{attribute} jest zbyt krotki (min. {num} znakow).', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x7B6174747269627574657D206D75737420696E636C756465206174206C65617374207B6E756D7D206469676974732E, '{attribute} debe tener al menos {num} dígitos.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x7B6174747269627574657D206D75737420696E636C756465206174206C65617374207B6E756D7D206469676974732E, '{attribute}deve includere almeno {num} numeri.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x7B6174747269627574657D206D75737420696E636C756465206174206C65617374207B6E756D7D206469676974732E, '{attribute} musi zawierac co najmniej {num} cyfr.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x7B6174747269627574657D206D75737420696E636C756465206174206C65617374207B6E756D7D206C6F7765722063617365206C6574746572732E, '{attribute} debe tener al menos {num} caracteres en minúscula.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x7B6174747269627574657D206D75737420696E636C756465206174206C65617374207B6E756D7D206C6F7765722063617365206C6574746572732E, '{attribute} deve includere almeno {num} lettere minuscole.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x7B6174747269627574657D206D75737420696E636C756465206174206C65617374207B6E756D7D206C6F7765722063617365206C6574746572732E, '{attribute} musi zawierac co najmniej {num} malych liter.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x7B6174747269627574657D206D75737420696E636C756465206174206C65617374207B6E756D7D2073796D626F6C732E, '{attribute} debe tener al menos {num} símbolos.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x7B6174747269627574657D206D75737420696E636C756465206174206C65617374207B6E756D7D2073796D626F6C732E, '{attribute} deve includere almeno {num} simboli.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x7B6174747269627574657D206D75737420696E636C756465206174206C65617374207B6E756D7D2073796D626F6C732E, '{attribute} musi zawierac co najmniej {num} symboli.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x7B6174747269627574657D206D75737420696E636C756465206174206C65617374207B6E756D7D2075707065722063617365206C6574746572732E, '{attribute} debe tener al menos {num} caracteres en mayúscula.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x7B6174747269627574657D206D75737420696E636C756465206174206C65617374207B6E756D7D2075707065722063617365206C6574746572732E, '{attribute} deve includere almeno {num} lettere maiuscole.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x7B6174747269627574657D206D75737420696E636C756465206174206C65617374207B6E756D7D2075707065722063617365206C6574746572732E, '{attribute} musi zawierac co najmniej {num} duzych liter.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x7B6174747269627574657D206D757374206E6F7420636F6E7461696E206D6F7265207468616E207B6E756D7D2073657175656E7469616C6C7920726570656174656420636861726163746572732E, '{attribute} no debe tener más de {num} caracteres repetidos secuencialmente.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x7B6174747269627574657D206D757374206E6F7420636F6E7461696E206D6F7265207468616E207B6E756D7D2073657175656E7469616C6C7920726570656174656420636861726163746572732E, '{attribute} non deve contenere {num} caratteri ripetuti sequenzialmente.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x7B6174747269627574657D206D757374206E6F7420636F6E7461696E206D6F7265207468616E207B6E756D7D2073657175656E7469616C6C7920726570656174656420636861726163746572732E, '{attribute} nie moze zawierac wiecej niz {num} sekwencji znakow.', 'pl', 'yum');
INSERT INTO `translation` VALUES (0x7B6174747269627574657D206D757374206E6F7420636F6E7461696E20776869746573706163652E, '{attribute} no debe contener espacios.', 'es', 'yum');
INSERT INTO `translation` VALUES (0x7B6174747269627574657D206D757374206E6F7420636F6E7461696E20776869746573706163652E, '{attribute} non deve contenere spazi.', 'it', 'yum');
INSERT INTO `translation` VALUES (0x7B6174747269627574657D206D757374206E6F7420636F6E7461696E20776869746573706163652E, '{attribute} nie moze zawierac bialych znakow.', 'pl', 'yum');

-- ----------------------------
-- Table structure for type_scale
-- ----------------------------
DROP TABLE IF EXISTS `type_scale`;
CREATE TABLE `type_scale` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of type_scale
-- ----------------------------
INSERT INTO `type_scale` VALUES ('1', 'positive');
INSERT INTO `type_scale` VALUES ('2', 'negative');
INSERT INTO `type_scale` VALUES ('3', 'personal');

-- ----------------------------
-- Table structure for universal_log
-- ----------------------------
DROP TABLE IF EXISTS `universal_log`;
CREATE TABLE `universal_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_id` int(11) DEFAULT NULL,
  `window_id` int(11) DEFAULT NULL,
  `mail_id` int(11) DEFAULT NULL,
  `file_id` int(11) DEFAULT NULL,
  `replica_id` int(11) DEFAULT NULL,
  `last_dialog_id` int(11) DEFAULT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL DEFAULT '00:00:00',
  `meeting_id` int(11) DEFAULT NULL,
  `window_uid` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `universal_log_dialog_id` (`replica_id`),
  KEY `universal_log_dialog_last_id` (`last_dialog_id`),
  KEY `universal_log_file_id` (`file_id`),
  KEY `universal_log_mail_id` (`mail_id`),
  KEY `universal_log_window_id` (`window_id`),
  KEY `universal_log_sim_id` (`sim_id`),
  KEY `fk_universal_log_meeting_id` (`meeting_id`),
  CONSTRAINT `fk_universal_log_meeting_id` FOREIGN KEY (`meeting_id`) REFERENCES `meeting` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_universal_log_replica_id` FOREIGN KEY (`replica_id`) REFERENCES `replica` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `universal_log_dialog_last_id` FOREIGN KEY (`last_dialog_id`) REFERENCES `replica` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `universal_log_file_id` FOREIGN KEY (`file_id`) REFERENCES `my_documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `universal_log_mail_id` FOREIGN KEY (`mail_id`) REFERENCES `mail_box` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `universal_log_sim_id` FOREIGN KEY (`sim_id`) REFERENCES `simulations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `universal_log_window_id` FOREIGN KEY (`window_id`) REFERENCES `window` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of universal_log
-- ----------------------------

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `password` varchar(128) NOT NULL,
  `salt` varchar(128) NOT NULL,
  `activationKey` varchar(128) NOT NULL DEFAULT '',
  `createtime` int(20) DEFAULT NULL,
  `lastvisit` int(20) DEFAULT NULL,
  `lastaction` int(20) DEFAULT NULL,
  `lastpasswordchange` int(20) DEFAULT NULL,
  `failedloginattempts` int(20) DEFAULT NULL,
  `superuser` int(1) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT '0',
  `avatar` varchar(255) DEFAULT NULL,
  `notifyType` enum('None','Digest','Instant','Threshold') DEFAULT 'Instant',
  `agree_with_terms` varchar(3) DEFAULT NULL,
  `is_admin` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `status` (`status`),
  KEY `superuser` (`superuser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------

-- ----------------------------
-- Table structure for usergroup
-- ----------------------------
DROP TABLE IF EXISTS `usergroup`;
CREATE TABLE `usergroup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `participants` text,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of usergroup
-- ----------------------------

-- ----------------------------
-- Table structure for usergroup_message
-- ----------------------------
DROP TABLE IF EXISTS `usergroup_message`;
CREATE TABLE `usergroup_message` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(11) unsigned NOT NULL,
  `group_id` int(11) unsigned NOT NULL,
  `createtime` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of usergroup_message
-- ----------------------------

-- ----------------------------
-- Table structure for user_account_corporate
-- ----------------------------
DROP TABLE IF EXISTS `user_account_corporate`;
CREATE TABLE `user_account_corporate` (
  `user_id` int(10) unsigned NOT NULL,
  `industry_id` int(11) DEFAULT NULL,
  `corporate_email` varchar(120) DEFAULT NULL,
  `is_corporate_email_verified` tinyint(1) DEFAULT '0',
  `corporate_email_verified_at` datetime DEFAULT NULL,
  `corporate_email_activation_code` varchar(128) DEFAULT NULL,
  `invites_limit` int(4) unsigned NOT NULL DEFAULT '0',
  `position_id` int(11) DEFAULT NULL,
  `company_size_id` int(11) DEFAULT NULL,
  `ownership_type` varchar(50) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `company_description` text,
  `tariff_id` int(11) DEFAULT NULL,
  `tariff_activated_at` datetime DEFAULT NULL,
  `tariff_expired_at` datetime DEFAULT NULL,
  `inn` varchar(50) DEFAULT NULL,
  `cpp` varchar(50) DEFAULT NULL,
  `bank_account_number` varchar(50) DEFAULT NULL,
  `bic` varchar(50) DEFAULT NULL,
  `preference_payment_method` varchar(50) DEFAULT NULL,
  `default_invitation_mail_text` text,
  `referrals_invite_limit` int(11) DEFAULT NULL,
  `is_display_referrals_popup` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`user_id`),
  KEY `user_account_corporate_FK_industry` (`industry_id`),
  KEY `fk_user_account_corporate_position_id` (`position_id`),
  KEY `fk_user_account_corporate_company_size_id` (`company_size_id`),
  KEY `user_account_corporate_fk_tariff` (`tariff_id`),
  CONSTRAINT `fk_user_account_corporate_company_size_id` FOREIGN KEY (`company_size_id`) REFERENCES `company_sizes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_user_account_corporate_position_id` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `user_account_corporate_FK_industry` FOREIGN KEY (`industry_id`) REFERENCES `industry` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `user_account_corporate_fk_tariff` FOREIGN KEY (`tariff_id`) REFERENCES `tariff` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `user_account_corporate_FK_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_account_corporate
-- ----------------------------

-- ----------------------------
-- Table structure for user_account_personal
-- ----------------------------
DROP TABLE IF EXISTS `user_account_personal`;
CREATE TABLE `user_account_personal` (
  `user_id` int(10) unsigned NOT NULL,
  `industry_id` int(11) DEFAULT NULL,
  `professional_status_id` int(11) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `user_account_personal_FK_industry` (`industry_id`),
  KEY `fk_user_account_personal_professional_status_id` (`professional_status_id`),
  CONSTRAINT `fk_user_account_personal_professional_status_id` FOREIGN KEY (`professional_status_id`) REFERENCES `professional_statuses` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `user_account_personal_FK_industry` FOREIGN KEY (`industry_id`) REFERENCES `industry` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `user_account_personal_FK_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_account_personal
-- ----------------------------

-- ----------------------------
-- Table structure for user_referral
-- ----------------------------
DROP TABLE IF EXISTS `user_referral`;
CREATE TABLE `user_referral` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referral_id` int(10) unsigned DEFAULT NULL,
  `referral_email` varchar(150) DEFAULT NULL,
  `referrer_id` int(10) unsigned DEFAULT NULL,
  `invited_at` datetime DEFAULT NULL,
  `registered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `referral_id_index` (`referral_id`),
  KEY `referrer_id_index` (`referrer_id`),
  CONSTRAINT `referral_id_fk` FOREIGN KEY (`referral_id`) REFERENCES `user` (`id`),
  CONSTRAINT `referrer_id_fk` FOREIGN KEY (`referrer_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_referral
-- ----------------------------

-- ----------------------------
-- Table structure for user_role
-- ----------------------------
DROP TABLE IF EXISTS `user_role`;
CREATE TABLE `user_role` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_role
-- ----------------------------

-- ----------------------------
-- Table structure for vacancy
-- ----------------------------
DROP TABLE IF EXISTS `vacancy`;
CREATE TABLE `vacancy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `professional_occupation_id` int(11) DEFAULT NULL,
  `professional_specialization_id` int(11) DEFAULT NULL,
  `label` varchar(120) NOT NULL,
  `link` text,
  `import_id` varchar(60) DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `position_level_slug` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `vacancy_fk_professional_occupation` (`professional_occupation_id`),
  KEY `vacancy_fk_professional_specialization` (`professional_specialization_id`),
  KEY `vacancy_fk_user` (`user_id`),
  KEY `vacancy_FK_position_level` (`position_level_slug`),
  CONSTRAINT `vacancy_FK_position_level` FOREIGN KEY (`position_level_slug`) REFERENCES `position_level` (`slug`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vacancy_fk_professional_occupation` FOREIGN KEY (`professional_occupation_id`) REFERENCES `professional_occupation` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `vacancy_fk_professional_specialization` FOREIGN KEY (`professional_specialization_id`) REFERENCES `professional_specialization` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `vacancy_fk_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of vacancy
-- ----------------------------

-- ----------------------------
-- Table structure for weight
-- ----------------------------
DROP TABLE IF EXISTS `weight`;
CREATE TABLE `weight` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rule_id` int(11) DEFAULT NULL,
  `performance_rule_category_id` varchar(10) DEFAULT NULL,
  `hero_behaviour_id` int(11) DEFAULT NULL,
  `assessment_category_code` varchar(50) DEFAULT NULL,
  `value` decimal(11,10) NOT NULL DEFAULT '0.0000000000',
  `scenario_id` int(11) DEFAULT NULL,
  `import_id` varchar(14) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_weight_performance_rule_category_id` (`performance_rule_category_id`),
  KEY `fk_weight_hero_behaviour_id` (`hero_behaviour_id`),
  KEY `fk_weight_assessment_category_code` (`assessment_category_code`),
  KEY `fk_weight_scenario_id` (`scenario_id`),
  CONSTRAINT `fk_weight_assessment_category_code` FOREIGN KEY (`assessment_category_code`) REFERENCES `assessment_category` (`code`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_weight_hero_behaviour_id` FOREIGN KEY (`hero_behaviour_id`) REFERENCES `hero_behaviour` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_weight_performance_rule_category_id` FOREIGN KEY (`performance_rule_category_id`) REFERENCES `activity_category` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_weight_scenario_id` FOREIGN KEY (`scenario_id`) REFERENCES `scenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of weight
-- ----------------------------

-- ----------------------------
-- Table structure for window
-- ----------------------------
DROP TABLE IF EXISTS `window`;
CREATE TABLE `window` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `subtype` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `type_subtype_unique` (`type`,`subtype`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of window
-- ----------------------------
INSERT INTO `window` VALUES ('51', 'browser', 'browser main');
INSERT INTO `window` VALUES ('42', 'documents', 'documents files');
INSERT INTO `window` VALUES ('41', 'documents', 'documents main');
INSERT INTO `window` VALUES ('11', 'mail', 'mail main');
INSERT INTO `window` VALUES ('13', 'mail', 'mail new');
INSERT INTO `window` VALUES ('14', 'mail', 'mail plan');
INSERT INTO `window` VALUES ('12', 'mail', 'mail preview');
INSERT INTO `window` VALUES ('1', 'main screen', 'main screen');
INSERT INTO `window` VALUES ('2', 'main screen', 'manual');
INSERT INTO `window` VALUES ('24', 'phone', 'phone call');
INSERT INTO `window` VALUES ('21', 'phone', 'phone main');
INSERT INTO `window` VALUES ('23', 'phone', 'phone talk');
INSERT INTO `window` VALUES ('3', 'plan', 'plan');
INSERT INTO `window` VALUES ('33', 'visitor', 'meeting choice');
INSERT INTO `window` VALUES ('34', 'visitor', 'meeting gone');
INSERT INTO `window` VALUES ('31', 'visitor', 'visitor entrance');
INSERT INTO `window` VALUES ('32', 'visitor', 'visitor talk');

-- ----------------------------
-- Table structure for YiiCache
-- ----------------------------
DROP TABLE IF EXISTS `YiiCache`;
CREATE TABLE `YiiCache` (
  `id` varchar(255) NOT NULL,
  `expire` int(11) DEFAULT NULL,
  `value` longblob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of YiiCache
-- ----------------------------
INSERT INTO `YiiCache` VALUES ('a198e6d4aed7f655d42cb501b968f19f', '1382015631', 0x613A323A7B693A303B613A31303A7B733A31313A227065726D697373696F6E73223B613A333A7B693A303B733A31363A22434861734D616E7952656C6174696F6E223B693A313B733A31333A2259756D5065726D697373696F6E223B693A323B733A31323A227072696E636970616C5F6964223B7D733A31303A226D616E616765645F6279223B613A333A7B693A303B733A31363A22434861734D616E7952656C6174696F6E223B693A313B733A31333A2259756D5065726D697373696F6E223B693A323B733A31343A227375626F7264696E6174655F6964223B7D733A353A22726F6C6573223B613A333A7B693A303B733A31373A22434D616E794D616E7952656C6174696F6E223B693A313B733A373A2259756D526F6C65223B693A323B733A32373A22757365725F726F6C6528757365725F69642C20726F6C655F696429223B7D733A383A226D65737361676573223B613A343A7B693A303B733A31363A22434861734D616E7952656C6174696F6E223B693A313B733A31303A2259756D4D657373616765223B693A323B733A31303A22746F5F757365725F6964223B733A353A226F72646572223B733A31343A2274696D657374616D702044455343223B7D733A31353A22756E726561645F6D65737361676573223B613A353A7B693A303B733A31363A22434861734D616E7952656C6174696F6E223B693A313B733A31303A2259756D4D657373616765223B693A323B733A31303A22746F5F757365725F6964223B733A393A22636F6E646974696F6E223B733A31363A226D6573736167655F72656164203D2030223B733A353A226F72646572223B733A31343A2274696D657374616D702044455343223B7D733A31333A2273656E745F6D65737361676573223B613A333A7B693A303B733A31363A22434861734D616E7952656C6174696F6E223B693A313B733A31303A2259756D4D657373616765223B693A323B733A31323A2266726F6D5F757365725F6964223B7D733A363A22766973697473223B613A333A7B693A303B733A31363A22434861734D616E7952656C6174696F6E223B693A313B733A31353A2259756D50726F66696C655669736974223B693A323B733A31303A22766973697465645F6964223B7D733A373A2276697369746564223B613A333A7B693A303B733A31363A22434861734D616E7952656C6174696F6E223B693A313B733A31353A2259756D50726F66696C655669736974223B693A323B733A31303A2276697369746F725F6964223B7D733A373A2270726F66696C65223B613A333A7B693A303B733A31353A22434861734F6E6552656C6174696F6E223B693A313B733A31303A2259756D50726F66696C65223B693A323B733A373A22757365725F6964223B7D733A373A2270726976616379223B613A333A7B693A303B733A31353A22434861734F6E6552656C6174696F6E223B693A313B733A31373A2259756D5072697661637953657474696E67223B693A323B733A373A22757365725F6964223B7D7D693A313B4E3B7D);

-- ----------------------------
-- Table structure for YiiSession
-- ----------------------------
DROP TABLE IF EXISTS `YiiSession`;
CREATE TABLE `YiiSession` (
  `id` varchar(255) NOT NULL,
  `expire` int(11) DEFAULT NULL,
  `data` blob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of YiiSession
-- ----------------------------
